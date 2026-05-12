<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class BeauticiansModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT sp.profile_id, sp.user_id, sp.specialization, sp.hire_date, u.NAME, u.email
             FROM staff_profiles sp
             JOIN users u ON sp.user_id = u.user_id
             WHERE u.ROLE = "Beautician"
             ORDER BY u.NAME'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT sp.profile_id, sp.user_id, sp.specialization, sp.hire_date, u.NAME, u.email
             FROM staff_profiles sp
             JOIN users u ON sp.user_id = u.user_id
             WHERE sp.profile_id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByUserId(int $userId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM staff_profiles WHERE user_id = :user_id'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function findAvailable(): array
    {
        $stmt = $this->db->query(
            'SELECT sp.profile_id, sp.user_id, sp.specialization, sp.hire_date, u.NAME, u.email
             FROM staff_profiles sp
             JOIN users u ON sp.user_id = u.user_id
             WHERE u.ROLE = "Beautician"
             ORDER BY u.NAME'
        );
        return $stmt->fetchAll();
    }

    public function getSchedule(int $userId, string $date): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.res_id, r.schedule_time, r.STATUS, u.NAME as customer_name, s.service_name
             FROM reservations r
             JOIN reservation_details rd ON r.res_id = rd.res_id
             JOIN users u ON r.user_id = u.user_id
             JOIN services s ON rd.service_id = s.service_id
             WHERE rd.beautician_id = :beautician_id AND DATE(r.schedule_time) = :date
             ORDER BY r.schedule_time'
        );
        $stmt->execute([':beautician_id' => $userId, ':date' => $date]);
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
            'UPDATE staff_profiles SET ' . implode(', ', $fields) . ' WHERE profile_id = :id'
        );

        return $stmt->execute($values);
    }
}
