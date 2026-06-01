<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Cafe Guest Order Model
 * Handle guest cafe orders with payment tracking and auto-cancel logic
 */
class CafeGuestOrderModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create guest cafe order
     */
    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare(
            'INSERT INTO cafe_guest_orders 
             (bill_id, seat_token, guest_name, order_type, seat_id, total_amount, status)
             VALUES (:bill_id, :seat_token, :guest_name, :order_type, :seat_id, :total_amount, :status)'
        );

        $result = $stmt->execute([
            ':bill_id' => $data['bill_id'] ?? 0,
            ':seat_token' => $data['seat_token'] ?? '',
            ':guest_name' => $data['guest_name'] ?? 'Guest',
            ':order_type' => $data['order_type'] ?? 'Dine-In',
            ':seat_id' => $data['seat_id'] ?? '',
            ':total_amount' => $data['total_amount'] ?? 0,
            ':status' => 'New'
        ]);

        return $result ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Get guest order by ID
     */
    public function findById(int $guestOrderId): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM cafe_guest_orders WHERE guest_order_id = :id');
        $stmt->execute([':id' => $guestOrderId]);
        return $stmt->fetch();
    }

    /**
     * Get guest order by token
     */
    public function findByToken(string $token): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT cgo.*, ob.seat_id, ob.total_amount
             FROM cafe_guest_orders cgo
             JOIN open_bills ob ON cgo.bill_id = ob.bill_id
             WHERE cgo.seat_token = :token
             LIMIT 1'
        );
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    /**
     * Get active orders for a seat
     */
    public function getActiveOrdersForSeat(string $seatId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM cafe_guest_orders 
             WHERE seat_id = :seat_id
             AND status IN ("New", "In Progress", "Ready")
             AND payment_status = "Pending"
             AND auto_cancel_at > NOW()'
        );
        $stmt->execute([':seat_id' => $seatId]);
        return $stmt->fetchAll();
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $guestOrderId, string $status, array $data = []): bool
    {
        $fields = ['payment_status = :status'];
        $values = [':status' => $status, ':id' => $guestOrderId];

        // If paid, update related fields
        if ($status === 'Paid') {
            $fields[] = 'status = :order_status';
            $values[':order_status'] = 'In Progress';
            $fields[] = 'completed_at = NOW()';
        }

        if (!empty($data['payment_method'])) {
            $fields[] = 'payment_method = :method';
            $values[':method'] = $data['payment_method'];
        }

        if (!empty($data['payment_proof_url'])) {
            $fields[] = 'payment_proof_url = :proof';
            $values[':proof'] = $data['payment_proof_url'];
        }

        $stmt = $this->db->prepare('UPDATE cafe_guest_orders SET ' . implode(', ', $fields) . ' WHERE guest_order_id = :id');
        return $stmt->execute($values);
    }

    /**
     * Update order status
     */
    public function updateStatus(int $guestOrderId, string $status): bool
    {
        $updateFields = ['status = :status'];
        $values = [':status' => $status, ':id' => $guestOrderId];

        if ($status === 'Completed') {
            $updateFields[] = 'completed_at = NOW()';
        }

        $stmt = $this->db->prepare('UPDATE cafe_guest_orders SET ' . implode(', ', $updateFields) . ' WHERE guest_order_id = :id');
        return $stmt->execute($values);
    }

    /**
     * Cancel expired guest order (5-minute timeout)
     */
    public function cancelExpired(int $guestOrderId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE cafe_guest_orders 
             SET status = "Cancelled", payment_status = "Cancelled"
             WHERE guest_order_id = :id
             AND auto_cancel_at < NOW()
             AND payment_status = "Pending"'
        );
        return $stmt->execute([':id' => $guestOrderId]);
    }

    /**
     * Auto-cancel all expired pending orders (admin/scheduler)
     */
    public function cancelAllExpiredOrders(): int
    {
        $stmt = $this->db->prepare(
            'UPDATE cafe_guest_orders 
             SET status = "Cancelled", payment_status = "Cancelled"
             WHERE auto_cancel_at < NOW()
             AND payment_status = "Pending"
             AND status != "Cancelled"'
        );
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Get guest order with all details (for payment page)
     */
    public function getOrderDetails(int $guestOrderId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT cgo.*, 
                    ob.seat_id, ob.total_amount, ob.bill_type,
                    s.seat_name, s.zone_type,
                    GROUP_CONCAT(m.menu_name) as menu_names,
                    GROUP_CONCAT(o.qty) as quantities
             FROM cafe_guest_orders cgo
             JOIN open_bills ob ON cgo.bill_id = ob.bill_id
             JOIN seats s ON ob.seat_id = s.seat_id
             LEFT JOIN orders o ON o.seat_id = ob.seat_id AND o.STATUS = "In Progress"
             LEFT JOIN menus m ON o.menu_id = m.menu_id
             WHERE cgo.guest_order_id = :id
             GROUP BY cgo.guest_order_id'
        );
        $stmt->execute([':id' => $guestOrderId]);
        return $stmt->fetch();
    }

    /**
     * Get all pending guest orders (for admin monitoring)
     */
    public function getAllPendingOrders(): array
    {
        $stmt = $this->db->prepare(
            'SELECT cgo.*, s.seat_name, ob.total_amount,
                    TIMESTAMPDIFF(MINUTE, cgo.created_at, cgo.auto_cancel_at) as minutes_remaining
             FROM cafe_guest_orders cgo
             JOIN open_bills ob ON cgo.bill_id = ob.bill_id
             JOIN seats s ON ob.seat_id = s.seat_id
             WHERE cgo.payment_status = "Pending"
             AND cgo.status != "Cancelled"
             ORDER BY cgo.auto_cancel_at ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
