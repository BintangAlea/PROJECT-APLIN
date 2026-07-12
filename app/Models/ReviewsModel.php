<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ReviewsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT r.*, u.NAME as customer_name, ub.NAME as beautician_name, res.res_id
             FROM reviews r
             JOIN users u ON r.customer_id = u.user_id
             LEFT JOIN users ub ON r.beautician_id = ub.user_id
             JOIN reservations res ON r.res_id = res.res_id
             ORDER BY r.review_id DESC'
        );
        return $stmt->fetchAll();
    }

    public function findByBeautician(int $beauticiansId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.NAME as customer_name, res.res_id
             FROM reviews r
             JOIN users u ON r.customer_id = u.user_id
             JOIN reservations res ON r.res_id = res.res_id
             WHERE r.beautician_id = :beautician_id
             ORDER BY r.review_id DESC'
        );
        $stmt->execute([':beautician_id' => $beauticiansId]);
        return $stmt->fetchAll();
    }

    public function findByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, ub.NAME as beautician_name, res.res_id
             FROM reviews r
             LEFT JOIN users ub ON r.beautician_id = ub.user_id
             JOIN reservations res ON r.res_id = res.res_id
             WHERE r.customer_id = :customer_id
             ORDER BY r.review_id DESC'
        );
        $stmt->execute([':customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO reviews (res_id, rating, COMMENT)
             VALUES (:res_id, :rating, :comment)'
        );

        return $stmt->execute([
            ':res_id' => $data['res_id'],
            ':rating' => $data['rating'],
            ':comment' => $data['comment'] ?? null,
        ]);
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
