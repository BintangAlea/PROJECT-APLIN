<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ReservationsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM reservations');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByCustomerId(int $customerId): array
    {
        $stmt = $this->db->prepare('
            SELECT r.*, s.name as service_name, u.full_name as beautician_name
            FROM reservations r
            LEFT JOIN services s ON r.service_id = s.id
            LEFT JOIN beauticians b ON r.beautician_id = b.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE r.customer_id = :customer_id
            ORDER BY r.reservation_date DESC
        ');
        $stmt->execute([':customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function findByDate(string $date): array
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE DATE(reservation_date) = :date ORDER BY reservation_time');
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll();
    }

    public function getTodayScheduleByBeautician(int $beauticiansId): array
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE beautician_id = :beautician_id AND DATE(reservation_date) = :date ORDER BY reservation_time');
        $stmt->execute([':beautician_id' => $beauticiansId, ':date' => $today]);
        return $stmt->fetchAll();
    }

    public function getUpcomingByBeautician(int $beauticiansId, int $days = 7): array
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE beautician_id = :beautician_id AND reservation_date >= CURDATE() AND reservation_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY) ORDER BY reservation_date, reservation_time');
        $stmt->execute([':beautician_id' => $beauticiansId, ':days' => $days]);
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
        $stmt = $this->db->prepare('INSERT INTO reservations (res_id, customer_id, beautician_id, service_id, reservation_date, reservation_time, status, created_at) VALUES (:res_id, :customer_id, :beautician_id, :service_id, :reservation_date, :reservation_time, :status, NOW())');
        
        return $stmt->execute([
            ':res_id' => $data['res_id'],
            ':customer_id' => $data['customer_id'],
            ':beautician_id' => $data['beautician_id'] ?? null,
            ':service_id' => $data['service_id'],
            ':reservation_date' => $data['reservation_date'],
            ':reservation_time' => $data['reservation_time'] ?? '10:00:00',
            ':status' => $data['status'] ?? 'Stage1'
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

        $stmt = $this->db->prepare('UPDATE reservations SET ' . implode(', ', $updates) . ', updated_at = NOW() WHERE id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM reservations WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
