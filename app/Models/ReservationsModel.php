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
}
