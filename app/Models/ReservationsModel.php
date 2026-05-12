<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ReservationsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM reservations');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE res_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByCustomerId(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT r.*, rd.service_id, s.service_name, u.NAME as beautician_name, rd.beautician_id
            FROM reservations r
            LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
            LEFT JOIN services s ON rd.service_id = s.service_id
            LEFT JOIN users u ON rd.beautician_id = u.user_id
            WHERE r.user_id = :user_id
            ORDER BY r.schedule_time DESC
        ');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findByDate(string $date): array
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE DATE(schedule_time) = :date ORDER BY schedule_time');
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll();
    }

    public function generateResId(): string
    {
        $timestamp = time();
        $random = mt_rand(100, 999);
        return 'RES' . $timestamp . $random;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO reservations (user_id, seat_id, STATUS, schedule_time, is_dp_paid, dp_amount, payment_proof_url)
            VALUES (:user_id, :seat_id, :status, :schedule_time, :is_dp_paid, :dp_amount, :payment_proof_url)
        ');
        
        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? $data['customer_id'],
            ':seat_id' => $data['seat_id'] ?? 'A1',
            ':status' => $data['status'] ?? 'Stage 1',
            ':schedule_time' => $data['schedule_time'] ?? $data['reservation_date'] . ' ' . ($data['reservation_time'] ?? '10:00:00'),
            ':is_dp_paid' => $data['is_dp_paid'] ?? false,
            ':dp_amount' => $data['dp_amount'] ?? 0,
            ':payment_proof_url' => $data['payment_proof_url'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $updates = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $stmt = $this->db->prepare('UPDATE reservations SET ' . implode(', ', $updates) . ' WHERE res_id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM reservations WHERE res_id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
