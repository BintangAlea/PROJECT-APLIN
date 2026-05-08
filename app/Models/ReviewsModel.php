<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ReviewsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT r.*, u.full_name as customer_name, b.id as beautician_id, ub.full_name as beautician_name, res.res_id
             FROM reviews r
             JOIN users u ON r.customer_id = u.id
             LEFT JOIN beauticians b ON r.beautician_id = b.id
             LEFT JOIN users ub ON b.user_id = ub.id
             JOIN reservations res ON r.reservation_id = res.id
             ORDER BY r.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function findByBeautician(int $beauticiansId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.full_name as customer_name, res.res_id
             FROM reviews r
             JOIN users u ON r.customer_id = u.id
             JOIN reservations res ON r.reservation_id = res.id
             WHERE r.beautician_id = :beautician_id
             ORDER BY r.created_at DESC'
        );
        $stmt->execute([':beautician_id' => $beauticiansId]);
        return $stmt->fetchAll();
    }

    public function findByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, ub.full_name as beautician_name, res.res_id
             FROM reviews r
             LEFT JOIN beauticians b ON r.beautician_id = b.id
             LEFT JOIN users ub ON b.user_id = ub.id
             JOIN reservations res ON r.reservation_id = res.id
             WHERE r.customer_id = :customer_id
             ORDER BY r.created_at DESC'
        );
        $stmt->execute([':customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO reviews (reservation_id, customer_id, beautician_id, rating, comment)
             VALUES (:reservation_id, :customer_id, :beautician_id, :rating, :comment)'
        );

        return $stmt->execute($data);
    }

    public function getAverageRating(int $beauticiansId): float
    {
        $stmt = $this->db->prepare(
            'SELECT AVG(rating) as avg_rating FROM reviews WHERE beautician_id = :beautician_id'
        );
        $stmt->execute([':beautician_id' => $beauticiansId]);
        $result = $stmt->fetch();
        return (float) ($result['avg_rating'] ?? 0);
    }
}
