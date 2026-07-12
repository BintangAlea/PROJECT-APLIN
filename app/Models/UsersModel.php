<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UsersModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findByRole(string $role): array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE ROLE = :role');
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    public function register(string $email, string $password, string $fullName, string $phone, string $role): bool
    {
        // Check if email already exists
        if ($this->findByEmail($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (NAME, email, PASSWORD, ROLE) 
             VALUES (:name, :email, :password, :role)'
        );

        return $stmt->execute([
            ':name' => $fullName,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
        ]);
    }

    public function login(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        $storedPassword = (string) ($user['PASSWORD'] ?? '');
        $isBcryptHash = str_starts_with($storedPassword, '$2y$') || str_starts_with($storedPassword, '$argon2');

        if ($isBcryptHash) {
            if (!password_verify($password, $storedPassword)) {
                return false;
            }
        } elseif (!hash_equals($storedPassword, $password)) {
            // Support legacy seed data that still stores plaintext passwords.
            return false;
        }

        return $user;
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
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE user_id = :id'
        );

        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE user_id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getTotalByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM users WHERE ROLE = :role');
        $stmt->execute([':role' => $role]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getSalonHistory(int $userId): array
    {
        $salonHistory = [];
        try {
            $salonStmt = $this->db->prepare("
                SELECT r.res_id, r.schedule_time, r.STATUS AS status, r.is_dp_paid, r.dp_amount, r.payment_proof_url, s.seat_name, p.promo_name, p.discount_value,
                       rv.review_id, rv.rating, rv.COMMENT AS review_comment
                FROM db_merish_salon.reservations r
                LEFT JOIN db_merish_salon.seats s ON r.seat_id = s.seat_id
                LEFT JOIN db_merish_salon.promotions p ON r.promo_id = p.promo_id
                LEFT JOIN db_merish_salon.reviews rv ON r.res_id = rv.res_id
                WHERE r.user_id = :user_id
                ORDER BY r.schedule_time DESC
            ");
            $salonStmt->execute([':user_id' => $userId]);
            $salonHistory = $salonStmt->fetchAll();

            foreach ($salonHistory as &$res) {
                $detailsStmt = $this->db->prepare("
                    SELECT rd.qty, rd.subtotal, s.service_name, s.base_tariff
                    FROM db_merish_salon.reservation_details rd
                    JOIN db_merish_salon.services s ON rd.service_id = s.service_id
                    WHERE rd.res_id = :res_id
                ");
                $detailsStmt->execute([':res_id' => $res['res_id']]);
                $res['details'] = $detailsStmt->fetchAll();
            }
        } catch (\Exception $e) {
            error_log("UsersModel.getSalonHistory() failed: " . $e->getMessage());
        }
        return $salonHistory;
    }

    public function getCafeHistory(string $fullName): array
    {
        $cafeHistory = [];
        try {
            $cafeStmt = $this->db->prepare("
                SELECT o.order_id, o.seat_id, o.total_amount, o.payment_method, o.payment_status, o.STATUS AS status, o.order_date
                FROM db_merish_cafe.orders o
                WHERE o.guest_name = :guest_name
                ORDER BY o.order_date DESC
            ");
            $cafeStmt->execute([':guest_name' => $fullName]);
            $cafeHistory = $cafeStmt->fetchAll();

            foreach ($cafeHistory as &$order) {
                $detailsStmt = $this->db->prepare("
                    SELECT od.qty, od.subtotal, m.menu_name, m.price
                    FROM db_merish_cafe.order_details od
                    JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id
                    WHERE od.order_id = :order_id
                ");
                $detailsStmt->execute([':order_id' => $order['order_id']]);
                $order['details'] = $detailsStmt->fetchAll();
            }
        } catch (\Exception $e) {
            error_log("UsersModel.getCafeHistory() failed: " . $e->getMessage());
        }
        return $cafeHistory;
    }
}
