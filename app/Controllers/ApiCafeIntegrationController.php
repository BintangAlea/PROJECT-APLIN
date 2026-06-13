<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\ApiResponse;
use PDO;

/**
 * API Cafe Integration Controller
 * 
 * Handle cafe ordering system yang terintegrasi dengan reservasi salon
 * 
 * Scenario A: Customer Salon Scan QR Cafe
 *   - Customer sudah di kursi salon (dari receptionist check-in)
 *   - Scan QR kursi → system detect active reservation untuk seat tersebut
 *   - Order auto-link ke reservation
 * 
 * Scenario B: Walk-in Cafe Guest (No Reservation)
 *   - Guest scan QR tanpa login
 *   - Create standalone cafe order
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
     * - Check apakah seat ini linked ke active reservation salon
     * - Return menu list + billing context
     */
    public function scanQR()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $token = trim($input['token'] ?? '');

        if (!$token) {
            echo ApiResponse::error('QR token required', 400);
            return;
        }

        // Token is the seat_id directly (from QR code URL: merish.test/qr?seat=S01)
        $seatId = strtoupper($token);

        // Validate seat exists
        $seatStmt = $this->db->prepare(
            "SELECT seat_id, seat_name, zone_type
             FROM db_merish_salon.seats
             WHERE seat_id = :seat_id
             LIMIT 1"
        );
        $seatStmt->execute([':seat_id' => $seatId]);
        $seat = $seatStmt->fetch();

        if (!$seat) {
            echo ApiResponse::error('Invalid or expired QR token', 401);
            return;
        }

        // ====== DETECT SCENARIO ======
        $scenario = null;
        $linkedResId = null;
        $mainCustomerName = null;

        // Cek: Apakah seat ini punya active reservation salon?
        $reservationCheckStmt = $this->db->prepare(
            "SELECT r.res_id, u.NAME AS customer_name
             FROM db_merish_salon.reservations r
             LEFT JOIN db_merish_salon.users u ON r.user_id = u.user_id
             WHERE r.seat_id = :seat_id AND r.STATUS IN ('Confirmed', 'In-Service')
             LIMIT 1"
        );
        $reservationCheckStmt->execute([':seat_id' => $seatId]);
        $activeReservation = $reservationCheckStmt->fetch();

        if ($activeReservation) {
            // Scenario A: Customer Salon Scan QR (dari kursi salon)
            $scenario = 'salon_customer';
            $linkedResId = (int)$activeReservation['res_id'];
            $mainCustomerName = $activeReservation['customer_name'] ?? 'Guest';
        } else {
            // Scenario B: Walk-in Guest (Cafe Only)
            $scenario = 'guest';
            $mainCustomerName = 'Cafe Walk-In';
        }

        // ====== Get Menu List ======
        $menuStmt = $this->db->prepare(
            "SELECT menu_id, menu_name, price, is_available
             FROM db_merish_cafe.menus
             WHERE is_available = TRUE
             ORDER BY menu_name ASC"
        );
        $menuStmt->execute();
        $menus = $menuStmt->fetchAll();

        echo ApiResponse::success([
            'scenario' => $scenario,
            'res_id' => $linkedResId,
            'customer_name' => $mainCustomerName,
            'seat_id' => $seatId,
            'seat_name' => $seat['seat_name'],
            'zone_type' => $seat['zone_type'],
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
     *   "seat_id": "A1",
     *   "menu_id": "MENU001",
     *   "qty": 2,
     *   "guest_name": "Budi" (optional, for walk-in)
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
        if (empty($input['seat_id'])) $errors['seat_id'] = 'Seat ID required';
        if (empty($input['menu_id'])) $errors['menu_id'] = 'Menu ID required';
        if (empty($input['qty']) || $input['qty'] < 1) $errors['qty'] = 'Quantity must be > 0';

        if (!empty($errors)) {
            echo ApiResponse::validationError($errors);
            return;
        }

        $seatId = trim($input['seat_id']);
        $menuId = trim($input['menu_id']);
        $qty = (int)$input['qty'];
        $guestName = trim($input['guest_name'] ?? 'Cafe Walk-In');

        // Get menu
        $menuStmt = $this->db->prepare(
            "SELECT menu_id, menu_name, price, is_available
             FROM db_merish_cafe.menus
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

        try {
            $totalPrice = (float)$menu['price'] * $qty;

            // Insert order header
            $insertStmt = $this->db->prepare(
                "INSERT INTO db_merish_cafe.orders (guest_name, seat_id, total_amount, payment_method, payment_status, STATUS, order_date)
                 VALUES (:guest_name, :seat_id, :total_amount, 'Cash', 'Unpaid', 'New', NOW())"
            );
            $insertStmt->execute([
                ':guest_name' => $guestName,
                ':seat_id' => $seatId,
                ':total_amount' => $totalPrice
            ]);

            $orderId = (int)$this->db->lastInsertId();

            // Insert order detail
            $detailStmt = $this->db->prepare(
                "INSERT INTO db_merish_cafe.order_details (order_id, menu_id, qty, subtotal)
                 VALUES (:order_id, :menu_id, :qty, :subtotal)"
            );
            $detailStmt->execute([
                ':order_id' => $orderId,
                ':menu_id' => $menuId,
                ':qty' => $qty,
                ':subtotal' => $totalPrice
            ]);

            echo ApiResponse::success([
                'order_id' => $orderId,
                'menu_id' => $menuId,
                'menu_name' => $menu['menu_name'],
                'qty' => $qty,
                'unit_price' => (float)$menu['price'],
                'total_price' => (float)$totalPrice,
                'seat_id' => $seatId,
                'order_status' => 'New',
                'payment_status' => 'Unpaid'
            ], 'Order added successfully', 201);
        } catch (\Exception $e) {
            echo ApiResponse::error('Failed to add order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/cafe/orders/:seat_id/summary
     * 
     * Get current orders summary for a seat
     */
    public function getBillSummary($seatId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $seatId = trim($seatId);

        // Get cafe orders for this seat
        $ordersStmt = $this->db->prepare(
            "SELECT o.order_id, od.menu_id, m.menu_name, od.qty, m.price, 
                    (od.qty * m.price) as line_total, o.STATUS as order_status
             FROM db_merish_cafe.orders o
             JOIN db_merish_cafe.order_details od ON o.order_id = od.order_id
             JOIN db_merish_cafe.menus m ON m.menu_id = od.menu_id
             WHERE o.seat_id = :seat_id AND o.payment_status = 'Unpaid'
             ORDER BY o.order_date DESC"
        );
        $ordersStmt->execute([':seat_id' => $seatId]);
        $cafeOrders = $ordersStmt->fetchAll();

        // Calculate cafe total
        $cafeTotal = array_reduce($cafeOrders, static function (float $carry, array $item): float {
            return $carry + (float)($item['line_total'] ?? 0);
        }, 0.0);

        // Get salon charges (jika ada active reservation untuk seat ini)
        $salonTotal = 0;
        $resId = null;
        $reservationStmt = $this->db->prepare(
            "SELECT r.res_id
             FROM db_merish_salon.reservations r
             WHERE r.seat_id = :seat_id AND r.STATUS IN ('Confirmed', 'In-Service')
             LIMIT 1"
        );
        $reservationStmt->execute([':seat_id' => $seatId]);
        $reservation = $reservationStmt->fetch();

        if ($reservation) {
            $resId = (int)$reservation['res_id'];
            $salonStmt = $this->db->prepare(
                "SELECT SUM(s.base_tariff) as salon_total
                 FROM db_merish_salon.reservation_details rd
                 JOIN db_merish_salon.services s ON s.service_id = rd.service_id
                 WHERE rd.res_id = :res_id"
            );
            $salonStmt->execute([':res_id' => $resId]);
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
            'seat_id' => $seatId,
            'res_id' => $resId,
            'cafe_orders' => $cafeOrders,
            'summary' => [
                'salon_total' => (float)$salonTotal,
                'cafe_total' => (float)$cafeTotal,
                'subtotal' => (float)$subtotal,
                'synergy_discount' => (float)$synergyDiscount,
                'tax' => (float)$tax,
                'total_due' => (float)$totalDue
            ]
        ], 'Order summary retrieved', 200);
    }

    /**
     * POST /api/cafe/payment
     * 
     * Payment untuk walk-in guest (Cafe Only)
     * 
     * Body:
     * {
     *   "seat_id": "A1",
     *   "payment_method": "Cash" | "QRIS"
     * }
     */
    public function processPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $seatId = trim($input['seat_id'] ?? '');
        $paymentMethod = trim($input['payment_method'] ?? 'Cash');

        if ($seatId === '') {
            echo ApiResponse::error('Seat ID required', 400);
            return;
        }

        // orders.payment_method ENUM: QRIS, Cash
        $validMethods = ['Cash', 'QRIS'];
        if (!in_array($paymentMethod, $validMethods)) {
            echo ApiResponse::error('Invalid payment method', 400);
            return;
        }

        try {
            // Calculate cafe total for unpaid orders at this seat
            $cafeStmt = $this->db->prepare(
                "SELECT SUM(od.qty * m.price) as total
                 FROM db_merish_cafe.orders o
                 JOIN db_merish_cafe.order_details od ON o.order_id = od.order_id
                 JOIN db_merish_cafe.menus m ON m.menu_id = od.menu_id
                 WHERE o.seat_id = :seat_id AND o.payment_status = 'Unpaid'"
            );
            $cafeStmt->execute([':seat_id' => $seatId]);
            $cafeData = $cafeStmt->fetch();
            $cafeTotal = (float)($cafeData['total'] ?? 0);

            if ($cafeTotal <= 0) {
                echo ApiResponse::error('No unpaid orders found for this seat', 404);
                return;
            }

            // Mark all unpaid orders at this seat as paid
            // (transactions table requires res_id NOT NULL, so cafe-only payments
            //  are tracked via orders.payment_status instead)
            $markPaidStmt = $this->db->prepare(
                "UPDATE db_merish_cafe.orders SET payment_status = :payment_status, payment_method = :payment_method
                 WHERE seat_id = :seat_id AND payment_status = 'Unpaid'"
            );
            $markPaidStmt->execute([
                ':payment_status' => 'Paid',
                ':payment_method' => $paymentMethod,
                ':seat_id' => $seatId
            ]);

            echo ApiResponse::success([
                'seat_id' => $seatId,
                'payment_method' => $paymentMethod,
                'payment_status' => 'Completed',
                'total_paid' => (float)$cafeTotal
            ], 'Cafe order payment processed', 200);
        } catch (\Exception $e) {
            echo ApiResponse::error('Payment processing failed: ' . $e->getMessage(), 500);
        }
    }
}
