<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class TransactionsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT t.*, r.res_id, u.full_name as customer_name
             FROM transactions t
             LEFT JOIN reservations r ON t.reservation_id = r.id
             LEFT JOIN users u ON r.customer_id = u.id
             ORDER BY t.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, r.res_id, u.full_name as customer_name
             FROM transactions t
             LEFT JOIN reservations r ON t.reservation_id = r.id
             LEFT JOIN users u ON r.customer_id = u.id
             WHERE t.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByReservationId(int $reservationId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM transactions WHERE reservation_id = :reservation_id'
        );
        $stmt->execute([':reservation_id' => $reservationId]);
        return $stmt->fetch();
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, r.res_id, u.full_name as customer_name
             FROM transactions t
             LEFT JOIN reservations r ON t.reservation_id = r.id
             LEFT JOIN users u ON r.customer_id = u.id
             WHERE t.payment_status = :status
             ORDER BY t.created_at DESC'
        );
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO transactions (trans_id, reservation_id, subtotal, discount_amount, discount_reason, 
                                      synergy_discount, multiplier_applied, total_amount, payment_method, payment_status)
             VALUES (:trans_id, :reservation_id, :subtotal, :discount_amount, :discount_reason, 
                     :synergy_discount, :multiplier_applied, :total_amount, :payment_method, :payment_status)'
        );

        return $stmt->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $values[":$key"] = $value;
        }

        $stmt = $this->db->prepare(
            'UPDATE transactions SET ' . implode(', ', $fields) . ' WHERE id = :id'
        );

        return $stmt->execute($values);
    }

    public function getTotalRevenue(string $startDate = null, string $endDate = null): float
    {
        $query = 'SELECT SUM(total_amount) as total FROM transactions WHERE payment_status = "Paid"';
        $params = [];

        if ($startDate && $endDate) {
            $query .= ' AND DATE(created_at) BETWEEN :start_date AND :end_date';
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (float) ($result['total'] ?? 0);
    }

    public function generateTransId(): string
    {
        return 'TRX-' . date('YmdHis') . '-' . rand(1000, 9999);
    }
}
