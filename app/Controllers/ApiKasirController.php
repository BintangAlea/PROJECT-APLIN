<?php

namespace App\Controllers;

use App\Core\ApiResponse;
use App\Core\Database;

/**
 * API Kasir & Promo Controller
 * Handle billing calculation with:
 * 1. Unified bill (salon + cafe)
 * 2. Bundling discount
 * 3. Extra material charges
 */
class ApiKasirController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        header('Content-Type: application/json');
    }

    /**
     * POST /api/billing/calculate
     * Calculate total billing with bundling & extra materials
     * 
     * Request body:
     * {
     *   "res_id": 1,
     *   "salon_services": [
     *     {"service_id": "SRV001", "qty": 1, "price": 500000}
     *   ],
     *   "cafe_orders": [
     *     {"order_id": 1, "price": 150000}
     *   ],
     *   "extra_materials": [
     *     {"item_id": 1, "qty": 2, "price_per_unit": 50000}
     *   ],
     *   "promo_id": 1
     * }
     */
    public function calculate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['res_id'])) {
            echo ApiResponse::validationError(['res_id' => 'Reservation ID required']);
            return;
        }

        // Calculate salon services
        $salonTotal = 0;
        foreach ($input['salon_services'] ?? [] as $service) {
            $salonTotal += $service['price'] * ($service['qty'] ?? 1);
        }

        // Calculate cafe orders
        $cafeTotal = 0;
        foreach ($input['cafe_orders'] ?? [] as $order) {
            $cafeTotal += $order['price'];
        }

        // Calculate extra materials
        $extraMaterialTotal = 0;
        foreach ($input['extra_materials'] ?? [] as $material) {
            $extraMaterialTotal += $material['price_per_unit'] * $material['qty'];
        }

        // Subtotal
        $subtotal = $salonTotal + $cafeTotal + $extraMaterialTotal;

        // Apply bundling promo (if exists)
        $discount = 0;
        $promoName = null;
        if (!empty($input['promo_id'])) {
            $promo = $this->db->prepare('SELECT * FROM promotions WHERE promo_id = :promo_id');
            $promo->execute([':promo_id' => $input['promo_id']]);
            $promoData = $promo->fetch();

            if ($promoData) {
                $discount = $promoData['discount_value'];
                $promoName = $promoData['promo_name'];
            }
        }

        // Bundling discount (20% if both salon + cafe)
        $bundlingDiscount = 0;
        if ($salonTotal > 0 && $cafeTotal > 0) {
            $bundlingDiscount = ($salonTotal + $cafeTotal) * 0.20; // 20% discount
        }

        // Total discount
        $totalDiscount = $discount + $bundlingDiscount;

        // Grand total
        $grandTotal = $subtotal - $totalDiscount;

        echo ApiResponse::success([
            'res_id' => $input['res_id'],
            'breakdown' => [
                'salon_services' => $salonTotal,
                'cafe_orders' => $cafeTotal,
                'extra_materials' => $extraMaterialTotal,
            ],
            'subtotal' => $subtotal,
            'discounts' => [
                'promo_discount' => $discount,
                'promo_name' => $promoName,
                'bundling_discount' => $bundlingDiscount,
                'total_discount' => $totalDiscount
            ],
            'grand_total' => max(0, $grandTotal),
            'payment_status' => 'Ready for payment'
        ], 'Billing calculated successfully', 200);
    }

    /**
     * POST /api/billing/checkout
     * Process payment/checkout
     * 
     * Request body:
     * {
     *   "res_id": 1,
     *   "total_amount": 1230000,
     *   "payment_method": "QRIS"
     * }
     */
    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $errors = [];
        if (empty($input['res_id'])) $errors['res_id'] = 'Reservation ID required';
        if (empty($input['total_amount'])) $errors['total_amount'] = 'Total amount required';
        if (empty($input['payment_method'])) $errors['payment_method'] = 'Payment method required';

        if (!empty($errors)) {
            echo ApiResponse::validationError($errors);
            return;
        }

        $validMethods = ['Cash', 'Debit', 'QRIS', 'Transfer'];
        if (!in_array($input['payment_method'], $validMethods)) {
            echo ApiResponse::error('Invalid payment method', 400);
            return;
        }

        try {
            // Insert transaction
            $stmt = $this->db->prepare(
                'INSERT INTO transactions (res_id, total_amount, payment_method, payment_date)
                 VALUES (:res_id, :total_amount, :payment_method, :payment_date)'
            );

            $stmt->execute([
                ':res_id' => $input['res_id'],
                ':total_amount' => $input['total_amount'],
                ':payment_method' => $input['payment_method'],
                ':payment_date' => date('Y-m-d H:i:s')
            ]);

            // Update reservation status
            $updateRes = $this->db->prepare('UPDATE reservations SET STATUS = :status WHERE res_id = :res_id');
            $updateRes->execute([
                ':status' => 'Selesai',
                ':res_id' => $input['res_id']
            ]);

            echo ApiResponse::success([
                'trans_id' => $this->db->lastInsertId(),
                'res_id' => $input['res_id'],
                'total_amount' => $input['total_amount'],
                'payment_method' => $input['payment_method'],
                'payment_date' => date('Y-m-d H:i:s'),
                'status' => 'Completed'
            ], 'Payment processed successfully', 201);
        } catch (\Exception $e) {
            echo ApiResponse::error('Checkout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/promotions
     * Get available promotions
     */
    public function getPromotions()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $stmt = $this->db->query('SELECT * FROM promotions ORDER BY promo_id DESC');
        $promos = $stmt->fetchAll();

        echo ApiResponse::success($promos, 'Promotions retrieved', 200);
    }

    /**
     * GET /api/billing/:res_id
     * Get billing history/details for a reservation
     */
    public function getBilling($resId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $stmt = $this->db->prepare(
            'SELECT t.trans_id, t.res_id, t.total_amount, t.payment_method, t.payment_date,
                    r.user_id, COALESCE(u.NAME, \'Guest\') AS customer_name, r.STATUS as reservation_status
             FROM transactions t
             LEFT JOIN reservations r ON t.res_id = r.res_id
             LEFT JOIN users u ON r.user_id = u.user_id
             WHERE t.res_id = :res_id'
        );
        $stmt->execute([':res_id' => $resId]);
        $transaction = $stmt->fetch();

        if (!$transaction) {
            echo ApiResponse::notFound('Transaction not found');
            return;
        }

        echo ApiResponse::success($transaction, 'Billing details retrieved', 200);
    }
}
