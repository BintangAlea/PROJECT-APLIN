<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class BeauticiansModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT b.id, b.user_id, b.specialization, b.rating, b.status, u.full_name, u.email, u.phone
             FROM beauticians b
             JOIN users u ON b.user_id = u.id
             ORDER BY u.full_name'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT b.id, b.user_id, b.specialization, b.rating, b.status, u.full_name, u.email, u.phone
             FROM beauticians b
             JOIN users u ON b.user_id = u.id
             WHERE b.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByUserId(int $userId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM beauticians WHERE user_id = :user_id'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function findAvailable(): array
    {
        $stmt = $this->db->query(
            'SELECT b.id, b.user_id, b.specialization, b.rating, b.status, u.full_name, u.email
             FROM beauticians b
             JOIN users u ON b.user_id = u.id
             WHERE b.status = "available"
             ORDER BY b.rating DESC'
        );
        return $stmt->fetchAll();
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.id, b.user_id, b.specialization, b.rating, b.status, u.full_name, u.email
             FROM beauticians b
             JOIN users u ON b.user_id = u.id
             WHERE b.status = :status
             ORDER BY u.full_name'
        );
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function getSchedule(int $beauticiansId, string $date): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.id, r.res_id, r.reservation_time, r.duration_minutes, r.status, u.full_name as customer_name, s.name as service_name
             FROM reservations r
             JOIN users u ON r.customer_id = u.id
             JOIN services s ON r.service_id = s.id
             WHERE r.beautician_id = :beautician_id AND r.reservation_date = :date
             ORDER BY r.reservation_time'
        );
        $stmt->execute([':beautician_id' => $beauticiansId, ':date' => $date]);
        return $stmt->fetchAll();
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
            'UPDATE beauticians SET ' . implode(', ', $fields) . ' WHERE id = :id'
        );

        return $stmt->execute($values);
    }
}
