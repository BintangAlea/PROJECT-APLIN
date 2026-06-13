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
        $sql = 'SELECT s.*, 0 AS promo FROM services s ORDER BY s.service_id ASC';
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
