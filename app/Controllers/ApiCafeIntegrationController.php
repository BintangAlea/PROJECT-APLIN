<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\ApiResponse;
use PDO;

/**
 * API Cafe Integration Controller
 * 
 * Handle cafe ordering system yang terintegrasi dengan Unified Billing
 * 
 * Scenario A: Customer Salon Scan QR Cafe
 *   - Customer sudah di dalam open_bill (dari receptionist check-in)
 *   - Scan QR meja/kursi → system auto-link order ke bill_id
 *   - Tanpa pembayaran, langsung masuk bill salon
 * 
 * Scenario B: Companion (Pendamping) Scan QR Cafe
 *   - Receptionist allocate companion ke lounge seat
 *   - Companion scan QR → system detect seat ada di companion_seat_id
 *   - Order auto-link ke bill utama (salon customer)
 *   - Tanpa pembayaran, masuk satu bill dengan customer
 * 
 * Scenario C: Walk-in Cafe Guest (No Reservation)
 *   - Guest scan QR tanpa login
 *   - Create standalone "Cafe Only" bill
 *   - Harus bayar dulu (QRIS/Cash) sebelum pesanan masuk ke barista
 */
class ApiCafeIntegrationController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        header('Content-Type: application/json');
    }

    /**
     * POST /api/cafe/scan-qr
     * 
     * Initial QR scan dari customer cafe
     * - Detect seat nomor berapa
     * - Check apakah seat ini linked ke customer salon (companion_seat_id)
     * - Check apakah seat ini meja cafe standalone
     * - Return menu list + billing context
     */
    public function scanQR()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $tokenHash = trim($input['token'] ?? '');

        if (!$tokenHash) {
            echo ApiResponse::error('QR token required', 400);
            return;
        }

        // Validate QR token dan get seat_id
        $tokenStmt = $this->db->prepare(
            "SELECT t.seat_id, t.expires_at, s.seat_id, s.seat_name, s.zone_type
             FROM qr_tokens t
             JOIN seats s ON s.seat_id = t.seat_id
             WHERE t.token_hash = :token_hash AND t.is_active = TRUE AND t.expires_at > NOW()
             LIMIT 1"
        );
        $tokenStmt->execute([':token_hash' => $tokenHash]);
        $token = $tokenStmt->fetch();

        if (!$token) {
            echo ApiResponse::error('Invalid or expired QR token', 401);
            return;
        }

        $seatId = $token['seat_id'];

        // ====== DETECT SCENARIO ======
        $scenario = null;
        $linkedBillId = null;
        $linkedResId = null;
        $mainCustomerName = null;
        $customerRole = null;  // 'customer', 'companion', or 'guest'

        // Cek: Apakah seat ini companion_seat_id dari open bill?
        $companionCheckStmt = $this->db->prepare(
            "SELECT b.bill_id, b.res_id, r.guest_name
             FROM open_bills b
             LEFT JOIN reservations r ON b.res_id = r.res_id
             WHERE b.companion_seat_id = :seat_id AND b.bill_status IN ('Open', 'Ready for Checkout')
             LIMIT 1"
        );
        $companionCheckStmt->execute([':seat_id' => $seatId]);
        $companionBill = $companionCheckStmt->fetch();

        if ($companionBill) {
            // Scenario B: Companion Scan QR
            $scenario = 'companion';
            $linkedBillId = (int)$companionBill['bill_id'];
            $linkedResId = (int)$companionBill['res_id'];
            $mainCustomerName = $companionBill['guest_name'];
            $customerRole = 'companion';
        } else {
            // Cek: Apakah seat ini main_seat_id dari open bill?
            $mainCheckStmt = $this->db->prepare(
                "SELECT b.bill_id, b.res_id, r.guest_name
                 FROM open_bills b
                 LEFT JOIN reservations r ON b.res_id = r.res_id
                 WHERE b.main_seat_id = :seat_id AND b.bill_status IN ('Open', 'Ready for Checkout')
                 LIMIT 1"
            );
            $mainCheckStmt->execute([':seat_id' => $seatId]);
            $mainBill = $mainCheckStmt->fetch();

            if ($mainBill) {
                // Scenario A: Customer Salon Scan QR (dari kursi salon)
                $scenario = 'salon_customer';
                $linkedBillId = (int)$mainBill['bill_id'];
                $linkedResId = (int)$mainBill['res_id'];
                $mainCustomerName = $mainBill['guest_name'];
                $customerRole = 'customer';
            }
        }

        // Jika bukan customer/companion dari open bill, assume walk-in guest
        if (!$scenario) {
            // Scenario C: Walk-in Guest (Cafe Only)
            // Cek apakah sudah ada cafe-only bill untuk seat ini (on-going)
            $cafeOnlyStmt = $this->db->prepare(
                "SELECT b.bill_id
                 FROM open_bills b
                 WHERE b.main_seat_id = :seat_id AND b.zone_type = 'Cafe Only'
                 AND b.bill_status IN ('Open', 'Ready for Checkout')
                 LIMIT 1"
            );
            $cafeOnlyStmt->execute([':seat_id' => $seatId]);
            $existingCafeBill = $cafeOnlyStmt->fetch();

            if ($existingCafeBill) {
                $linkedBillId = (int)$existingCafeBill['bill_id'];
            } else {
                // Create new cafe-only bill
                $newBillStmt = $this->db->prepare(
                    "INSERT INTO open_bills (res_id, guest_name, zone_type, main_seat_id, bill_status)
                     VALUES (NULL, :guest_name, 'Cafe Only', :seat_id, 'Open')"
                );
                $newBillStmt->execute([
                    ':guest_name' => 'Cafe Walk-In',
                    ':seat_id' => $seatId
                ]);
                $linkedBillId = (int)$this->db->lastInsertId();
            }
            $scenario = 'guest';
            $customerRole = 'guest';
            $mainCustomerName = 'Cafe Walk-In';
        }

        // ====== Get Menu List ======
        $menuStmt = $this->db->prepare(
            "SELECT menu_id, menu_name, price, image_url, category, is_available
             FROM menus
             WHERE is_available = TRUE
             ORDER BY category ASC, menu_name ASC"
        );
        $menuStmt->execute();
        $menus = $menuStmt->fetchAll();

        // ====== Get Current Bill Details ======
        $billStmt = $this->db->prepare(
            "SELECT bill_id, res_id, guest_name, zone_type, subtotal_salon, subtotal_cafe
             FROM open_bills
             WHERE bill_id = :bill_id
             LIMIT 1"
        );
        $billStmt->execute([':bill_id' => $linkedBillId]);
        $bill = $billStmt->fetch();

        echo ApiResponse::success([
            'scenario' => $scenario,
            'customer_role' => $customerRole,
            'bill_id' => $linkedBillId,
            'res_id' => $linkedResId,
            'customer_name' => $mainCustomerName,
            'seat_id' => $seatId,
            'seat_name' => $token['seat_name'],
            'zone_type' => $token['zone_type'],
            'billing_context' => [
                'bill_status' => $bill['zone_type'] ?? 'Cafe Only',
                'current_salon_total' => (float)($bill['subtotal_salon'] ?? 0),
                'current_cafe_total' => (float)($bill['subtotal_cafe'] ?? 0),
                'requires_payment' => $customerRole === 'guest'  // Guest must pay first
            ],
            'menus' => $menus
        ], 'QR scanned successfully', 200);
    }

    /**
     * POST /api/cafe/add-order
     * 
     * Customer add menu item ke cart before checkout
     * 
     * Body:
     * {
     *   "bill_id": 1,
     *   "menu_id": "MENU001",
     *   "qty": 2,
     *   "delivery_method": "dine-in" | "takeaway"
     * }
     */
    public function addOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $errors = [];
        if (empty($input['bill_id'])) $errors['bill_id'] = 'Bill ID required';
        if (empty($input['menu_id'])) $errors['menu_id'] = 'Menu ID required';
        if (empty($input['qty']) || $input['qty'] < 1) $errors['qty'] = 'Quantity must be > 0';
        if (empty($input['delivery_method']) || !in_array($input['delivery_method'], ['dine-in', 'takeaway'])) {
            $errors['delivery_method'] = 'Delivery method must be dine-in or takeaway';
        }

        if (!empty($errors)) {
            echo ApiResponse::validationError($errors);
            return;
        }

        $billId = (int)$input['bill_id'];
        $menuId = trim($input['menu_id']);
        $qty = (int)$input['qty'];
        $deliveryMethod = $input['delivery_method'];

        // Get bill info
        $billStmt = $this->db->prepare(
            "SELECT b.bill_id, b.res_id, b.zone_type, b.main_seat_id, b.companion_seat_id, b.bill_status
             FROM open_bills b
             WHERE b.bill_id = :bill_id
             LIMIT 1"
        );
        $billStmt->execute([':bill_id' => $billId]);
        $bill = $billStmt->fetch();

        if (!$bill) {
            echo ApiResponse::error('Bill not found', 404);
            return;
        }

        if ($bill['bill_status'] !== 'Open') {
            echo ApiResponse::error('Bill is not open for orders', 400);
            return;
        }

        // Get menu
        $menuStmt = $this->db->prepare(
            "SELECT menu_id, menu_name, price, is_available
             FROM menus
             WHERE menu_id = :menu_id
             LIMIT 1"
        );
        $menuStmt->execute([':menu_id' => $menuId]);
        $menu = $menuStmt->fetch();

        if (!$menu) {
            echo ApiResponse::error('Menu not found', 404);
            return;
        }

        if (!$menu['is_available']) {
            echo ApiResponse::error('Menu is not available (out of stock)', 400);
            return;
        }

        // Determine seat untuk delivery (untuk dine-in)
        $deliverySeatId = null;
        if ($deliveryMethod === 'dine-in') {
            // Cek dari request: apakah customer atau companion yang order
            // Untuk sekarang, assume customer order ke main_seat (bisa improve dengan session tracking)
            $deliverySeatId = $bill['main_seat_id'] ?? null;
        }

        try {
            // Insert order
            $insertStmt = $this->db->prepare(
                "INSERT INTO orders (bill_id, menu_id, qty, order_type, seat_id, payment_status, STATUS, created_at)
                 VALUES (:bill_id, :menu_id, :qty, :order_type, :seat_id, 'Unpaid', 'New', NOW())"
            );
            $insertStmt->execute([
                ':bill_id' => $billId,
                ':menu_id' => $menuId,
                ':qty' => $qty,
                ':order_type' => ($deliveryMethod === 'dine-in') ? 'Dine-In' : 'Takeaway',
                ':seat_id' => $deliverySeatId
            ]);

            $orderId = (int)$this->db->lastInsertId();
            $totalPrice = $menu['price'] * $qty;

            echo ApiResponse::success([
                'order_id' => $orderId,
                'bill_id' => $billId,
                'menu_id' => $menuId,
                'menu_name' => $menu['menu_name'],
                'qty' => $qty,
                'unit_price' => (float)$menu['price'],
                'total_price' => (float)$totalPrice,
                'delivery_method' => $deliveryMethod,
                'order_status' => 'New',
                'payment_status' => 'Unpaid'
            ], 'Order added to bill', 201);
        } catch (\Exception $e) {
            echo ApiResponse::error('Failed to add order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/cafe/bill/:bill_id/summary
     * 
     * Get current bill summary (untuk customer lihat apa saja yang sudah di-order)
     */
    public function getBillSummary($billId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $billId = (int)$billId;

        // Get bill details
        $billStmt = $this->db->prepare(
            "SELECT b.bill_id, b.res_id, b.guest_name, b.zone_type, b.bill_status
             FROM open_bills b
             WHERE b.bill_id = :bill_id
             LIMIT 1"
        );
        $billStmt->execute([':bill_id' => $billId]);
        $bill = $billStmt->fetch();

        if (!$bill) {
            echo ApiResponse::error('Bill not found', 404);
            return;
        }

        // Get cafe orders
        $ordersStmt = $this->db->prepare(
            "SELECT o.order_id, o.menu_id, m.menu_name, o.qty, m.price, 
                    (o.qty * m.price) as line_total, o.STATUS as order_status
             FROM orders o
             JOIN menus m ON m.menu_id = o.menu_id
             WHERE o.bill_id = :bill_id AND o.payment_status = 'Unpaid'
             ORDER BY o.created_at DESC"
        );
        $ordersStmt->execute([':bill_id' => $billId]);
        $cafeOrders = $ordersStmt->fetchAll();

        // Calculate cafe total
        $cafeTotal = array_reduce($cafeOrders, static function (float $carry, array $item): float {
            return $carry + (float)($item['line_total'] ?? 0);
        }, 0.0);

        // Get salon charges (jika ada res_id)
        $salonTotal = 0;
        if ($bill['res_id']) {
            $salonStmt = $this->db->prepare(
                "SELECT SUM(s.base_tariff) as salon_total
                 FROM reservation_details rd
                 JOIN services s ON s.service_id = rd.service_id
                 WHERE rd.res_id = :res_id"
            );
            $salonStmt->execute([':res_id' => $bill['res_id']]);
            $salonData = $salonStmt->fetch();
            $salonTotal = (float)($salonData['salon_total'] ?? 0);
        }

        // Calculate synergy discount
        $synergyDiscount = ($salonTotal > 0 && $cafeTotal > 0) ? ($cafeTotal * 0.20) : 0;
        $subtotal = $salonTotal + $cafeTotal;
        $afterDiscount = $subtotal - $synergyDiscount;
        $tax = $afterDiscount * 0.11;
        $totalDue = $afterDiscount + $tax;

        echo ApiResponse::success([
            'bill_id' => $billId,
            'res_id' => $bill['res_id'],
            'guest_name' => $bill['guest_name'],
            'zone_type' => $bill['zone_type'],
            'bill_status' => $bill['bill_status'],
            'cafe_orders' => $cafeOrders,
            'summary' => [
                'salon_total' => (float)$salonTotal,
                'cafe_total' => (float)$cafeTotal,
                'subtotal' => (float)$subtotal,
                'synergy_discount' => (float)$synergyDiscount,
                'tax' => (float)$tax,
                'total_due' => (float)$totalDue
            ]
        ], 'Bill summary retrieved', 200);
    }

    /**
     * POST /api/cafe/payment
     * 
     * Payment untuk walk-in guest (Cafe Only)
     * Customer salon tidak perlu payment di cafe, langsung masuk unified bill
     * 
     * Body:
     * {
     *   "bill_id": 1,
     *   "payment_method": "Cash" | "QRIS" | "Debit" | "Transfer",
     *   "guest_name": "Budi" (for walk-in only)
     * }
     */
    public function processPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $billId = (int)($input['bill_id'] ?? 0);
        $paymentMethod = trim($input['payment_method'] ?? 'Cash');
        $guestName = trim($input['guest_name'] ?? '');

        if ($billId <= 0) {
            echo ApiResponse::error('Invalid bill ID', 400);
            return;
        }

        $validMethods = ['Cash', 'Debit', 'QRIS', 'Transfer'];
        if (!in_array($paymentMethod, $validMethods)) {
            echo ApiResponse::error('Invalid payment method', 400);
            return;
        }

        // Get bill
        $billStmt = $this->db->prepare(
            "SELECT b.bill_id, b.res_id, b.zone_type, b.bill_status
             FROM open_bills b
             WHERE b.bill_id = :bill_id
             LIMIT 1"
        );
        $billStmt->execute([':bill_id' => $billId]);
        $bill = $billStmt->fetch();

        if (!$bill) {
            echo ApiResponse::error('Bill not found', 404);
            return;
        }

        // Only walk-in guests (Cafe Only) can pay at cafe
        // Salon customer payment handled at receptionist checkout
        if ($bill['zone_type'] !== 'Cafe Only') {
            echo ApiResponse::error('This bill is linked to salon service. Pay at receptionist checkout.', 400);
            return;
        }

        try {
            // Mark all orders in this bill as paid
            $markPaidStmt = $this->db->prepare(
                "UPDATE orders SET payment_status = 'Paid' WHERE bill_id = :bill_id"
            );
            $markPaidStmt->execute([':bill_id' => $billId]);

            // Create transaction record
            $cafeTotal = 0;
            $cafeStmt = $this->db->prepare(
                "SELECT SUM(o.qty * m.price) as total
                 FROM orders o
                 JOIN menus m ON m.menu_id = o.menu_id
                 WHERE o.bill_id = :bill_id"
            );
            $cafeStmt->execute([':bill_id' => $billId]);
            $cafeData = $cafeStmt->fetch();
            $cafeTotal = (float)($cafeData['total'] ?? 0);

            $tax = $cafeTotal * 0.11;
            $totalDue = $cafeTotal + $tax;

            // Update bill status
            $updateBillStmt = $this->db->prepare(
                "UPDATE open_bills
                 SET bill_status = 'Closed', subtotal_cafe = :cafe_total, tax_amount = :tax, total_due = :total, closed_at = NOW()
                 WHERE bill_id = :bill_id"
            );
            $updateBillStmt->execute([
                ':cafe_total' => $cafeTotal,
                ':tax' => $tax,
                ':total' => $totalDue,
                ':bill_id' => $billId
            ]);

            echo ApiResponse::success([
                'bill_id' => $billId,
                'payment_method' => $paymentMethod,
                'payment_status' => 'Completed',
                'total_paid' => (float)$totalDue
            ], 'Cafe order payment processed', 200);
        } catch (\Exception $e) {
            echo ApiResponse::error('Payment processing failed: ' . $e->getMessage(), 500);
        }
    }
}
