<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class MenusModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM db_merish_cafe.menus WHERE is_available = TRUE');
        return $stmt->fetchAll();
    }

    public function findById(string $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM db_merish_cafe.menus WHERE menu_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
