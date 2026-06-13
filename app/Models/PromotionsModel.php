<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PromotionsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM promotions');
        return $stmt->fetchAll();
    }

    public function findByServiceId(string $serviceId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM promotions');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
