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


    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT o.*
             FROM db_merish_cafe.orders o
             WHERE o.order_id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getOrderDetails(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT od.*, m.menu_name, m.price
             FROM db_merish_cafe.order_details od
             JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id
             WHERE od.order_id = :order_id'
        );
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function findPendingOrInProgress(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, od.menu_id, od.qty, m.menu_name, m.price,
                    s.seat_name, s.zone_type
             FROM db_merish_cafe.orders o
             JOIN db_merish_cafe.order_details od ON o.order_id = od.order_id
             JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id
             LEFT JOIN seats s ON o.seat_id = s.seat_id
             WHERE o.STATUS IN ("New", "In Progress", "Ready", "Pending")
             ORDER BY o.order_id ASC'
        );
        return $stmt->fetchAll();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT o.*, od.menu_id, od.qty, m.menu_name, m.price,
                    s.seat_name, s.zone_type
             FROM db_merish_cafe.orders o
             JOIN db_merish_cafe.order_details od ON o.order_id = od.order_id
             JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id
             LEFT JOIN seats s ON o.seat_id = s.seat_id
             ORDER BY o.order_id DESC'
        );
        return $stmt->fetchAll();
    }

    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare(
            'INSERT INTO db_merish_cafe.orders (guest_name, seat_id, total_amount, payment_method, payment_status, STATUS, order_date)
             VALUES (:guest_name, :seat_id, :total_amount, :payment_method, :payment_status, :status, NOW())'
        );

        $paymentMethod = $data['payment_method'] ?? 'Cash';
        $paymentStatus = $data['payment_status'] ?? 'Unpaid';
        if ($paymentMethod === 'QRIS') {
            $paymentStatus = 'Paid';
        }

        $success = $stmt->execute([
            ':guest_name' => $data['guest_name'],
            ':seat_id' => $data['seat_id'] ?? null,
            ':total_amount' => $data['total_amount'] ?? 0,
            ':payment_method' => $paymentMethod,
            ':payment_status' => $paymentStatus,
            ':status' => $data['status'] ?? 'New',
        ]);

        if ($success) {
            $stmt = $this->db->query('SELECT LAST_INSERT_ID() as id');
            $row = $stmt->fetch();
            return (int)($row['id'] ?? 0);
        }
        return false;
    }

    public function createDetail(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO db_merish_cafe.order_details (order_id, menu_id, qty, subtotal)
             VALUES (:order_id, :menu_id, :qty, :subtotal)'
        );
        return $stmt->execute([
            ':order_id' => $data['order_id'],
            ':menu_id' => $data['menu_id'],
            ':qty' => $data['qty'],
            ':subtotal' => $data['subtotal'] ?? 0,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) return true;
        
        $fields = [];
        $values = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $values[":$key"] = $value;
        }

        $stmt = $this->db->prepare(
            'UPDATE db_merish_cafe.orders SET ' . implode(', ', $fields) . ' WHERE order_id = :id'
        );

        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM db_merish_cafe.orders WHERE order_id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
