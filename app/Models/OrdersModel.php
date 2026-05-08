<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class OrdersModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, m.name as menu_name, m.price as menu_price
             FROM orders o
             JOIN menus m ON o.menu_id = m.id
             ORDER BY o.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, m.name as menu_name, m.price as menu_price
             FROM orders o
             JOIN menus m ON o.menu_id = m.id
             WHERE o.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByReservationId(int $reservationId): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, m.name as menu_name, m.price as menu_price
             FROM orders o
             JOIN menus m ON o.menu_id = m.id
             WHERE o.reservation_id = :reservation_id
             ORDER BY o.created_at DESC'
        );
        $stmt->execute([':reservation_id' => $reservationId]);
        return $stmt->fetchAll();
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, m.name as menu_name, m.price as menu_price, r.res_id
             FROM orders o
             JOIN menus m ON o.menu_id = m.id
             LEFT JOIN reservations r ON o.reservation_id = r.id
             WHERE o.status = :status
             ORDER BY o.created_at DESC'
        );
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function findPendingOrInProgress(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, m.name as menu_name, m.price as menu_price, r.res_id
             FROM orders o
             JOIN menus m ON o.menu_id = m.id
             LEFT JOIN reservations r ON o.reservation_id = r.id
             WHERE o.status IN ("Pending", "In Progress")
             ORDER BY o.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (order_id, reservation_id, menu_id, quantity, price_per_item, table_number, status)
             VALUES (:order_id, :reservation_id, :menu_id, :quantity, :price_per_item, :table_number, :status)'
        );

        return $stmt->execute($data);
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
            'UPDATE orders SET ' . implode(', ', $fields) . ' WHERE id = :id'
        );

        return $stmt->execute($values);
    }

    public function generateOrderId(): string
    {
        return 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);
    }
}
