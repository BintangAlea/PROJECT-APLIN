<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class OrdersModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, m.menu_name, m.price as menu_price
             FROM orders o
             JOIN menus m ON o.menu_id = m.menu_id
             ORDER BY o.order_id DESC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, m.menu_name, m.price as menu_price
             FROM orders o
             JOIN menus m ON o.menu_id = m.menu_id
             WHERE o.order_id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByReservationId(int $reservationId): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, m.menu_name, m.price as menu_price
             FROM orders o
             JOIN menus m ON o.menu_id = m.menu_id
             WHERE o.res_id = :res_id
             ORDER BY o.order_id DESC'
        );
        $stmt->execute([':res_id' => $reservationId]);
        return $stmt->fetchAll();
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, m.menu_name, m.price as menu_price, r.res_id
             FROM orders o
             JOIN menus m ON o.menu_id = m.menu_id
             LEFT JOIN reservations r ON o.res_id = r.res_id
             WHERE o.STATUS = :status
             ORDER BY o.order_id DESC'
        );
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function findPendingOrInProgress(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, m.menu_name, m.price as menu_price, r.res_id
             FROM orders o
             JOIN menus m ON o.menu_id = m.menu_id
             LEFT JOIN reservations r ON o.res_id = r.res_id
             WHERE o.STATUS IN ("New", "Pending", "In Progress", "Selesai")
             ORDER BY o.order_id DESC'
        );
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (res_id, menu_id, qty, STATUS)
             VALUES (:res_id, :menu_id, :qty, :status)'
        );

        return $stmt->execute([
            ':res_id' => $data['res_id'] ?? $data['reservation_id'] ?? null,
            ':menu_id' => $data['menu_id'],
            ':qty' => $data['qty'] ?? $data['quantity'] ?? 1,
            ':status' => $data['status'] ?? 'New',
        ]);
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
            'UPDATE orders SET ' . implode(', ', $fields) . ' WHERE order_id = :id'
        );

        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM orders WHERE order_id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
