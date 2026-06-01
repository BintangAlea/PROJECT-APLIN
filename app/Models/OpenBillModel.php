<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Open Bill Model
 * Manage unified billing for Salon + Cafe integration
 */
class OpenBillModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create new open bill
     */
    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare(
            'INSERT INTO open_bills (res_id, user_id, seat_id, guest_name, bill_type, status)
             VALUES (:res_id, :user_id, :seat_id, :guest_name, :bill_type, :status)'
        );

        $result = $stmt->execute([
            ':res_id' => $data['res_id'] ?? null,
            ':user_id' => $data['user_id'] ?? null,
            ':seat_id' => $data['seat_id'] ?? '',
            ':guest_name' => $data['guest_name'] ?? 'Guest',
            ':bill_type' => $data['bill_type'] ?? 'Salon Only',
            ':status' => 'Open'
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Get bill by ID
     */
    public function findById(int $billId): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM open_bills WHERE bill_id = :bill_id');
        $stmt->execute([':bill_id' => $billId]);
        return $stmt->fetch();
    }

    /**
     * Get active bill for a seat
     */
    public function getActiveBillForSeat(string $seatId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM open_bills 
             WHERE seat_id = :seat_id 
             AND status = "Open"
             ORDER BY created_at DESC
             LIMIT 1'
        );
        $stmt->execute([':seat_id' => $seatId]);
        return $stmt->fetch();
    }

    /**
     * Get active bill for user (current session)
     */
    public function getActiveBillForUser(int $userId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM open_bills 
             WHERE user_id = :user_id 
             AND status IN ("Open", "Pending Payment")
             ORDER BY created_at DESC
             LIMIT 1'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Update bill totals and apply synergy discount
     */
    public function updateBillTotals(int $billId, array $charges): bool
    {
        $salonSubtotal = (float) ($charges['salon_subtotal'] ?? 0);
        $cafeSubtotal = (float) ($charges['cafe_subtotal'] ?? 0);
        
        // Apply Synergy Discount: 20% off if both salon and cafe items present
        $synergyDiscount = ($salonSubtotal > 0 && $cafeSubtotal > 0) 
            ? ($salonSubtotal + $cafeSubtotal) * 0.20 
            : 0;

        $totalAmount = $salonSubtotal + $cafeSubtotal - $synergyDiscount;

        $stmt = $this->db->prepare(
            'UPDATE open_bills 
             SET salon_subtotal = :salon,
                 cafe_subtotal = :cafe,
                 synergy_discount = :discount,
                 total_amount = :total,
                 bill_type = :bill_type
             WHERE bill_id = :bill_id'
        );

        // Determine bill type
        $billType = 'Salon Only';
        if ($salonSubtotal == 0 && $cafeSubtotal > 0) {
            $billType = 'Cafe Only';
        } elseif ($salonSubtotal > 0 && $cafeSubtotal > 0) {
            $billType = 'Salon+Cafe';
        }

        return $stmt->execute([
            ':salon' => $salonSubtotal,
            ':cafe' => $cafeSubtotal,
            ':discount' => $synergyDiscount,
            ':total' => $totalAmount,
            ':bill_type' => $billType,
            ':bill_id' => $billId
        ]);
    }

    /**
     * Close bill and mark as paid
     */
    public function closeBill(int $billId, array $paymentData): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE open_bills 
             SET status = "Paid",
                 payment_method = :payment_method,
                 is_dp_paid = :is_dp_paid,
                 closed_at = NOW()
             WHERE bill_id = :bill_id'
        );

        return $stmt->execute([
            ':payment_method' => $paymentData['payment_method'] ?? null,
            ':is_dp_paid' => $paymentData['is_dp_paid'] ?? false,
            ':bill_id' => $billId
        ]);
    }

    /**
     * Get all open bills for admin dashboard
     */
    public function getAllOpenBills(): array
    {
        $stmt = $this->db->prepare(
            'SELECT ob.*, s.seat_name, s.zone_type, u.NAME as customer_name
             FROM open_bills ob
             JOIN seats s ON ob.seat_id = s.seat_id
             LEFT JOIN users u ON ob.user_id = u.user_id
             WHERE ob.status IN ("Open", "Pending Payment")
             ORDER BY ob.created_at DESC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get bill unified view (consolidated salon + cafe charges)
     */
    public function getUnifiedBillView(int $billId): array|false
    {
        $bill = $this->findById($billId);
        if (!$bill) {
            return false;
        }

        // Get salon charges from reservation
        $salonCharges = [];
        if ($bill['res_id']) {
            $salonStmt = $this->db->prepare(
                'SELECT s.service_name, s.base_tariff as amount 
                 FROM reservation_details rd
                 JOIN services s ON rd.service_id = s.service_id
                 WHERE rd.res_id = :res_id'
            );
            $salonStmt->execute([':res_id' => $bill['res_id']]);
            $salonCharges = $salonStmt->fetchAll();
        }

        // Get cafe charges from orders
        $cafeCharges = [];
        $cafeStmt = $this->db->prepare(
            'SELECT m.menu_name, m.price, o.qty, (m.price * o.qty) as amount
             FROM orders o
             JOIN menus m ON o.menu_id = m.menu_id
             WHERE o.seat_id = :seat_id
             AND o.STATUS IN ("In Progress", "Ready", "Completed")'
        );
        $cafeStmt->execute([':seat_id' => $bill['seat_id']]);
        $cafeCharges = $cafeStmt->fetchAll();

        return [
            'bill' => $bill,
            'salon_charges' => $salonCharges,
            'cafe_charges' => $cafeCharges,
            'synergy_eligible' => !empty($salonCharges) && !empty($cafeCharges)
        ];
    }
}
