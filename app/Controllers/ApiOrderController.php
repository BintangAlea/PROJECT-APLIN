<?php

namespace App\Controllers;

use App\Core\ApiResponse;
use App\Core\Database;

/**
 * API Order Kafe Controller
 * Handle coffee/cafe orders with two scenarios:
 * 1. Registered customer (linked to reservation/salon treatment)
 * 2. Walk-in guest (takeaway only)
 */
class ApiOrderController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        header('Content-Type: application/json');
    }

    /**
     * POST /api/orders/create
     * Create new order
     * 
     * Scenario 1: Registered Customer (Dine-In)
     * {
     *   "seat_id": "A1",
     *   "menu_id": "MENU001",
     *   "qty": 2,
     *   "order_type": "Dine-In"
     * }
     * 
     * Scenario 2: Walk-in Guest (Takeaway)
     * {
     *   "guest_name": "John Doe",
     *   "menu_id": "MENU001",
     *   "qty": 1,
     *   "order_type": "Takeaway"
     * }
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validasi
        $errors = [];
        if (empty($input['menu_id'])) $errors['menu_id'] = 'Menu ID required';
        if (empty($input['qty']) || $input['qty'] < 1) $errors['qty'] = 'Quantity must be > 0';
        if (empty($input['order_type'])) $errors['order_type'] = 'Order type required (Dine-In/Takeaway)';

        // Validasi berdasarkan order type
        if ($input['order_type'] === 'Dine-In') {
            if (empty($input['seat_id'])) $errors['seat_id'] = 'Seat ID required for Dine-In';
        } elseif ($input['order_type'] === 'Takeaway') {
            if (empty($input['guest_name'])) $errors['guest_name'] = 'Guest name required for Takeaway';
        }

        if (!empty($errors)) {
            echo ApiResponse::validationError($errors);
            return;
        }

        // Check menu exists
        $menu = $this->db->prepare('SELECT * FROM menus WHERE menu_id = :menu_id');
        $menu->execute([':menu_id' => $input['menu_id']]);
        $menuData = $menu->fetch();

        if (!$menuData) {
            echo ApiResponse::notFound('Menu not found');
            return;
        }

        // Check if menu available
        if (!$menuData['is_available']) {
            echo ApiResponse::error('Menu is not available (out of stock)', 400);
            return;
        }

        try {
            // Insert order
            $insertOrder = $this->db->prepare(
                'INSERT INTO orders (res_id, guest_name, order_type, seat_id, menu_id, qty, payment_status, STATUS)
                 VALUES (:res_id, :guest_name, :order_type, :seat_id, :menu_id, :qty, :payment_status, :status)'
            );

            $insertOrder->execute([
                ':res_id' => null,
                ':guest_name' => $input['guest_name'] ?? null,
                ':order_type' => $input['order_type'],
                ':seat_id' => $input['seat_id'] ?? null,
                ':menu_id' => $input['menu_id'],
                ':qty' => $input['qty'],
                ':payment_status' => 'Unpaid',
                ':status' => 'New'
            ]);

            $orderId = $this->db->lastInsertId();

            // Calculate total
            $totalPrice = $menuData['price'] * $input['qty'];

            echo ApiResponse::success([
                'order_id' => $orderId,
                'menu_id' => $input['menu_id'],
                'menu_name' => $menuData['menu_name'],
                'qty' => $input['qty'],
                'unit_price' => $menuData['price'],
                'total_price' => $totalPrice,
                'order_type' => $input['order_type'],
                'seat_id' => $input['seat_id'] ?? null,
                'guest_name' => $input['guest_name'] ?? null,
                'status' => 'New',
                'payment_status' => 'Unpaid'
            ], 'Order created successfully', 201);
        } catch (\Exception $e) {
            echo ApiResponse::error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/orders/:order_id
     * Get order details
     */
    public function getOrder($orderId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $stmt = $this->db->prepare(
            'SELECT o.*, m.menu_name, m.price 
             FROM orders o 
             JOIN menus m ON o.menu_id = m.menu_id 
             WHERE o.order_id = :order_id'
        );
        $stmt->execute([':order_id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            echo ApiResponse::notFound('Order not found');
            return;
        }

        echo ApiResponse::success($order, 'Order details', 200);
    }

    /**
     * PUT /api/orders/:order_id/status
     * Update order status
     * 
     * Request body:
     * {
     *   "status": "In Progress" OR "Selesai"
     * }
     */
    public function updateStatus($orderId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['status'])) {
            echo ApiResponse::validationError(['status' => 'Status required']);
            return;
        }

        $validStatuses = ['New', 'In Progress', 'Selesai'];
        if (!in_array($input['status'], $validStatuses)) {
            echo ApiResponse::error('Invalid status. Must be one of: ' . implode(', ', $validStatuses), 400);
            return;
        }

        try {
            $stmt = $this->db->prepare('UPDATE orders SET STATUS = :status WHERE order_id = :order_id');
            $stmt->execute([
                ':status' => $input['status'],
                ':order_id' => $orderId
            ]);

            echo ApiResponse::success(['order_id' => $orderId, 'status' => $input['status']], 'Order status updated', 200);
        } catch (\Exception $e) {
            echo ApiResponse::error('Failed to update order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/orders
     * Get all orders with optional filters
     * 
     * Query params:
     * - status: New, In Progress, Selesai
     * - order_type: Dine-In, Takeaway
     * - page: pagination
     * - limit: items per page
     */
    public function getAll()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $status = $_GET['status'] ?? null;
        $orderType = $_GET['order_type'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;

        // Build query
        $where = [];
        $params = [];

        if ($status) {
            $where[] = 'o.STATUS = :status';
            $params[':status'] = $status;
        }

        if ($orderType) {
            $where[] = 'o.order_type = :order_type';
            $params[':order_type'] = $orderType;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count total
        $countStmt = $this->db->prepare("SELECT COUNT(*) as count FROM orders o {$whereClause}");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['count'];

        // Get orders
        $stmt = $this->db->prepare(
            "SELECT o.*, m.menu_name, m.price 
             FROM orders o 
             JOIN menus m ON o.menu_id = m.menu_id 
             {$whereClause}
             ORDER BY o.order_id DESC
             LIMIT :limit OFFSET :offset"
        );

        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        echo ApiResponse::paginated($orders, $total, $page, $limit, 'Orders retrieved successfully');
    }
}
