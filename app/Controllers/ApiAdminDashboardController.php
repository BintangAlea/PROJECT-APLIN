<?php

namespace App\Controllers;

use App\Core\ApiResponse;
use App\Core\Database;
use App\Core\Auth;

/**
 * API Admin Dashboard Controller
 * Handle admin dashboard queries with:
 * 1. Low stock alerts
 * 2. Sales summary
 * 3. Booking statistics
 */
class ApiAdminDashboardController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        header('Content-Type: application/json');
    }

    /**
     * Middleware: Check if user is admin
     */
    private function requireAdmin()
    {
        if ($_SESSION['role'] !== 'Admin') {
            echo ApiResponse::forbidden('Only admin can access this resource');
            exit;
        }
    }

    /**
     * GET /api/admin/dashboard
     * Get admin dashboard summary
     */
    public function getDashboard()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $this->requireAdmin();

        // Total revenue (today)
        $today = date('Y-m-d');
        $revenue = $this->db->prepare(
            'SELECT SUM(total_amount) as total FROM transactions 
             WHERE DATE(payment_date) = :date'
        );
        $revenue->execute([':date' => $today]);
        $totalRevenue = $revenue->fetch()['total'] ?? 0;

        // Total orders (today)
        $orders = $this->db->query(
            "SELECT COUNT(*) as total FROM orders 
             WHERE DATE(CURDATE()) = CURDATE()"
        );
        $totalOrders = $orders->fetch()['total'] ?? 0;

        // Total bookings (today)
        $bookings = $this->db->query(
            "SELECT COUNT(*) as total FROM reservations 
             WHERE DATE(schedule_time) = CURDATE() 
             AND STATUS != 'Canceled'"
        );
        $totalBookings = $bookings->fetch()['total'] ?? 0;

        // Low stock alerts
        $alerts = $this->db->query(
            'SELECT COUNT(*) as total FROM inventories 
             WHERE stock_qty <= min_stock'
        );
        $lowStockCount = $alerts->fetch()['total'] ?? 0;

        echo ApiResponse::success([
            'summary' => [
                'total_revenue_today' => (float)$totalRevenue,
                'total_orders_today' => (int)$totalOrders,
                'total_bookings_today' => (int)$totalBookings,
                'low_stock_alerts' => (int)$lowStockCount
            ],
            'date' => $today
        ], 'Dashboard summary retrieved', 200);
    }

    /**\n     * GET /api/admin/low-stock-alerts\n     * Get detailed low stock alerts\n     */\n    public function getLowStockAlerts()\n    {\n        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {\n            echo ApiResponse::error('Method not allowed', 405);\n            return;\n        }\n\n        $this->requireAdmin();\n\n        $alerts = $this->db->query(\n            'SELECT item_id, item_name, stock_qty, min_stock, unit,\n                    (min_stock - stock_qty) as shortage,\n                    CASE\n                        WHEN stock_qty = 0 THEN \"Out of Stock\"\n                        WHEN stock_qty < min_stock THEN \"Low Stock\"\n                        WHEN stock_qty <= min_stock * 1.5 THEN \"Warning\"\n                    END as alert_type\n             FROM inventories\n             WHERE stock_qty <= min_stock * 1.5\n             ORDER BY stock_qty ASC'\n        );\n        $alertItems = $alerts->fetchAll();\n\n        echo ApiResponse::success([\n            'total_alerts' => count($alertItems),\n            'out_of_stock' => count(array_filter($alertItems, fn($a) => $a['alert_type'] === 'Out of Stock')),\n            'low_stock' => count(array_filter($alertItems, fn($a) => $a['alert_type'] === 'Low Stock')),\n            'warning' => count(array_filter($alertItems, fn($a) => $a['alert_type'] === 'Warning')),\n            'items' => $alertItems\n        ], 'Low stock alerts', 200);\n    }\n\n    /**\n     * GET /api/admin/revenue\n     * Get revenue report\n     * \n     * Query params:\n     * - period: today, week, month, year\n     */\n    public function getRevenue()\n    {\n        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {\n            echo ApiResponse::error('Method not allowed', 405);\n            return;\n        }\n\n        $this->requireAdmin();\n\n        $period = $_GET['period'] ?? 'today';\n\n        $where = match($period) {\n            'today' => 'DATE(payment_date) = CURDATE()',\n            'week' => 'WEEK(payment_date) = WEEK(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())',\n            'month' => 'MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())',\n            'year' => 'YEAR(payment_date) = YEAR(CURDATE())',\n            default => 'DATE(payment_date) = CURDATE()'\n        };\n\n        $revenue = $this->db->prepare(\n            \"SELECT SUM(total_amount) as total, COUNT(*) as transaction_count\n             FROM transactions\n             WHERE {$where}\"\n        );\n        $revenue->execute();\n        $data = $revenue->fetch();\n\n        echo ApiResponse::success([\n            'period' => $period,\n            'total_revenue' => (float)($data['total'] ?? 0),\n            'transaction_count' => (int)($data['transaction_count'] ?? 0),\n            'average_transaction' => (float)(($data['total'] ?? 0) / max(($data['transaction_count'] ?? 1), 1))\n        ], 'Revenue report', 200);\n    }\n\n    /**\n     * GET /api/admin/users\n     * Get user statistics\n     */\n    public function getUsers()\n    {\n        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {\n            echo ApiResponse::error('Method not allowed', 405);\n            return;\n        }\n\n        $this->requireAdmin();\n\n        $roles = ['Admin', 'Receptionist', 'Barista', 'Beautician', 'Customer'];\n        $userStats = [];\n\n        foreach ($roles as $role) {\n            $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM users WHERE ROLE = :role');\n            $stmt->execute([':role' => $role]);\n            $count = $stmt->fetch()['count'];\n            $userStats[] = ['role' => $role, 'count' => $count];\n        }\n\n        $total = $this->db->query('SELECT COUNT(*) as count FROM users');\n        $totalUsers = $total->fetch()['count'];\n\n        echo ApiResponse::success([\n            'total_users' => $totalUsers,\n            'by_role' => $userStats\n        ], 'User statistics', 200);\n    }\n\n    /**\n     * GET /api/admin/top-menus\n     * Get top selling menus\n     */\n    public function getTopMenus()\n    {\n        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {\n            echo ApiResponse::error('Method not allowed', 405);\n            return;\n        }\n\n        $this->requireAdmin();\n\n        $limit = $_GET['limit'] ?? 10;\n\n        $top = $this->db->query(\n            \"SELECT m.menu_id, m.menu_name, m.price,\n                    SUM(o.qty) as total_qty,\n                    COUNT(o.order_id) as order_count,\n                    SUM(o.qty * m.price) as total_revenue\n             FROM orders o\n             JOIN menus m ON o.menu_id = m.menu_id\n             GROUP BY m.menu_id\n             ORDER BY total_qty DESC\n             LIMIT {$limit}\"\n        );\n        $topMenus = $top->fetchAll();\n\n        echo ApiResponse::success($topMenus, 'Top selling menus', 200);\n    }\n\n    /**\n     * POST /api/admin/trigger-low-stock-alert\n     * Trigger alert when stock hits minimum\n     * (Auto-called after stock deduction)\n     * \n     * Request body:\n     * {\n     *   \"item_id\": 1\n     * }\n     */\n    public function triggerLowStockAlert()\n    {\n        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {\n            echo ApiResponse::error('Method not allowed', 405);\n            return;\n        }\n\n        $this->requireAdmin();\n\n        $input = json_decode(file_get_contents('php://input'), true);\n\n        if (empty($input['item_id'])) {\n            echo ApiResponse::validationError(['item_id' => 'Item ID required']);\n            return;\n        }\n\n        $item = $this->db->prepare('SELECT * FROM inventories WHERE item_id = :item_id');\n        $item->execute([':item_id' => $input['item_id']]);\n        $itemData = $item->fetch();\n\n        if (!$itemData) {\n            echo ApiResponse::notFound('Item not found');\n            return;\n        }\n\n        if ($itemData['stock_qty'] > $itemData['min_stock']) {\n            echo ApiResponse::success([\n                'item_id' => $input['item_id'],\n                'alert_triggered' => false,\n                'reason' => 'Stock is above minimum threshold'\n            ], 'No alert needed', 200);\n            return;\n        }\n\n        // Alert triggered\n        $alertLevel = $itemData['stock_qty'] == 0 ? 'CRITICAL' : 'WARNING';\n        $message = \"Stock Alert: {$itemData['item_name']} is at critical level (Stock: {$itemData['stock_qty']}, Min: {$itemData['min_stock']})\";\n\n        echo ApiResponse::success([\n            'item_id' => $input['item_id'],\n            'item_name' => $itemData['item_name'],\n            'current_stock' => $itemData['stock_qty'],\n            'minimum_stock' => $itemData['min_stock'],\n            'alert_level' => $alertLevel,\n            'alert_triggered' => true,\n            'message' => $message,\n            'timestamp' => date('Y-m-d H:i:s')\n        ], 'Alert triggered', 200);\n    }\n\n    /**\n     * GET /api/admin/bookings\n     * Get booking statistics\n     * \n     * Query params:\n     * - date: YYYY-MM-DD (specific date)\n     * - status: Pending, Confirmed, In-Service, Selesai, Canceled\n     */\n    public function getBookings()\n    {\n        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {\n            echo ApiResponse::error('Method not allowed', 405);\n            return;\n        }\n\n        $this->requireAdmin();\n\n        $date = $_GET['date'] ?? date('Y-m-d');\n        $status = $_GET['status'] ?? null;\n\n        $where = \"DATE(schedule_time) = :date\";\n        $params = [':date' => $date];\n\n        if ($status) {\n            $where .= \" AND STATUS = :status\";\n            $params[':status'] = $status;\n        }\n\n        $bookings = $this->db->prepare(\n            \"SELECT res_id, guest_name, STATUS, schedule_time, dp_amount, is_dp_paid\n             FROM reservations\n             WHERE {$where}\n             ORDER BY schedule_time ASC\"\n        );\n        $bookings->execute($params);\n        $bookingData = $bookings->fetchAll();\n\n        // Get summary\n        $total = count($bookingData);\n        $confirmed = count(array_filter($bookingData, fn($b) => $b['STATUS'] === 'Confirmed'));\n        $inService = count(array_filter($bookingData, fn($b) => $b['STATUS'] === 'In-Service'));\n        $completed = count(array_filter($bookingData, fn($b) => $b['STATUS'] === 'Selesai'));\n\n        echo ApiResponse::success([\n            'date' => $date,\n            'summary' => [\n                'total_bookings' => $total,\n                'confirmed' => $confirmed,\n                'in_service' => $inService,\n                'completed' => $completed\n            ],\n            'bookings' => $bookingData\n        ], 'Booking statistics', 200);\n    }\n}\n