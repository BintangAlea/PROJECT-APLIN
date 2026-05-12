<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class TransactionsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT t.*, u.NAME as customer_name
             FROM transactions t
             LEFT JOIN reservations r ON t.res_id = r.res_id
             LEFT JOIN users u ON r.user_id = u.user_id
             ORDER BY t.payment_date DESC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.NAME as customer_name
             FROM transactions t
             LEFT JOIN reservations r ON t.res_id = r.res_id
             LEFT JOIN users u ON r.user_id = u.user_id
             WHERE t.trans_id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByReservationId(int $resId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM transactions WHERE res_id = :res_id'
        );
        $stmt->execute([':res_id' => $resId]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO transactions (res_id, total_amount, payment_date)
             VALUES (:res_id, :total_amount, NOW())'
        );

        return $stmt->execute([
            ':res_id' => $data['res_id'],
            ':total_amount' => $data['total_amount'],
        ]);
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
            'UPDATE transactions SET ' . implode(', ', $fields) . ' WHERE trans_id = :id'
        );

        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM transactions WHERE trans_id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getTotalRevenue(string $startDate = null, string $endDate = null): float
    {
        $query = 'SELECT SUM(total_amount) as total FROM transactions';
        $params = [];

        if ($startDate && $endDate) {
            $query .= ' WHERE DATE(payment_date) BETWEEN :start_date AND :end_date';
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }
}
