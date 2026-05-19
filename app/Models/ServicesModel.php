<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ServicesModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        // Include a promo flag when a promotion exists for the service (uses promotions.service_id_req)
        $sql = '
            SELECT s.*,
                   CASE WHEN p.promo_id IS NOT NULL THEN 1 ELSE 0 END AS promo
            FROM services s
            LEFT JOIN promotions p ON p.service_id_req = s.service_id
            GROUP BY s.service_id
        ';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(string $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM services WHERE service_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
