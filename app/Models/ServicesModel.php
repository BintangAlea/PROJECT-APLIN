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
        // Use EXISTS to compute promo flag without GROUP BY, compatible with ONLY_FULL_GROUP_BY.
        $sql = '
            SELECT s.*,
                   CASE
                       WHEN EXISTS (
                           SELECT 1
                           FROM promotions p
                           WHERE p.service_id_req = s.service_id
                       ) THEN 1
                       ELSE 0
                   END AS promo
            FROM services s
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
