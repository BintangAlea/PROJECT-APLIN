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
        if (($_SESSION['role'] ?? '') !== 'Admin') {
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
             WHERE DATE(order_date) = CURDATE()"
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

    /**
     * GET /api/admin/low-stock-alerts
     * Get detailed low stock alerts
     */
    public function getLowStockAlerts()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $this->requireAdmin();

        $alerts = $this->db->query(
            'SELECT item_id, item_name, stock_qty, min_stock, unit,
                    (min_stock - stock_qty) as shortage,
                    CASE
                        WHEN stock_qty = 0 THEN "Out of Stock"
                        WHEN stock_qty < min_stock THEN "Low Stock"
                        WHEN stock_qty <= min_stock * 1.5 THEN "Warning"
                    END as alert_type
             FROM inventories
             WHERE stock_qty <= min_stock * 1.5
             ORDER BY stock_qty ASC'
        );
        $alertItems = $alerts->fetchAll();

        echo ApiResponse::success([
            'total_alerts' => count($alertItems),
            'out_of_stock' => count(array_filter($alertItems, fn($a) => $a['alert_type'] === 'Out of Stock')),
            'low_stock' => count(array_filter($alertItems, fn($a) => $a['alert_type'] === 'Low Stock')),
            'warning' => count(array_filter($alertItems, fn($a) => $a['alert_type'] === 'Warning')),
            'items' => $alertItems
        ], 'Low stock alerts', 200);
    }

    /**
     * GET /api/admin/revenue
     * Get revenue report
     * 
     * Query params:
     * - period: today, week, month, year
     */
    public function getRevenue()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $this->requireAdmin();

        $period = $_GET['period'] ?? 'today';

        $where = match($period) {
            'today' => 'DATE(payment_date) = CURDATE()',
            'week' => 'WEEK(payment_date) = WEEK(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())',
            'month' => 'MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())',
            'year' => 'YEAR(payment_date) = YEAR(CURDATE())',
            default => 'DATE(payment_date) = CURDATE()'
        };

        $revenue = $this->db->prepare(
            "SELECT SUM(total_amount) as total, COUNT(*) as transaction_count
             FROM transactions
             WHERE {$where}"
        );
        $revenue->execute();
        $data = $revenue->fetch();

        echo ApiResponse::success([
            'period' => $period,
            'total_revenue' => (float)($data['total'] ?? 0),
            'transaction_count' => (int)($data['transaction_count'] ?? 0),
            'average_transaction' => (float)(($data['total'] ?? 0) / max(($data['transaction_count'] ?? 1), 1))
        ], 'Revenue report', 200);
    }

    /**
     * GET /api/admin/users
     * Get user statistics
     */
    public function getUsers()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $this->requireAdmin();

        $roles = ['Admin', 'Receptionist', 'Barista', 'Beautician', 'Customer'];
        $userStats = [];

        foreach ($roles as $role) {
            $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM users WHERE ROLE = :role');
            $stmt->execute([':role' => $role]);
            $count = $stmt->fetch()['count'];
            $userStats[] = ['role' => $role, 'count' => $count];
        }

        $total = $this->db->query('SELECT COUNT(*) as count FROM users');
        $totalUsers = $total->fetch()['count'];

        echo ApiResponse::success([
            'total_users' => $totalUsers,
            'by_role' => $userStats
        ], 'User statistics', 200);
    }

    /**
     * GET /api/admin/top-menus
     * Get top selling menus
     */
    public function getTopMenus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $this->requireAdmin();

        $limit = (int)($_GET['limit'] ?? 10);

        $top = $this->db->query(
            "SELECT m.menu_id, m.menu_name, m.price,
                    SUM(od.qty) as total_qty,
                    COUNT(o.order_id) as order_count,
                    SUM(od.subtotal) as total_revenue
             FROM orders o
             JOIN order_details od ON o.order_id = od.order_id
             JOIN menus m ON od.menu_id = m.menu_id
             GROUP BY m.menu_id, m.menu_name, m.price
             ORDER BY total_qty DESC
             LIMIT {$limit}"
        );
        $topMenus = $top->fetchAll();

        echo ApiResponse::success($topMenus, 'Top selling menus', 200);
    }

    /**
     * POST /api/admin/trigger-low-stock-alert
     * Trigger alert when stock hits minimum
     * (Auto-called after stock deduction)
     *
     * Request body:
     * {
     *   "item_id": 1
     * }
     */
    public function triggerLowStockAlert()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $this->requireAdmin();

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['item_id'])) {
            echo ApiResponse::validationError(['item_id' => 'Item ID required']);
            return;
        }

        $item = $this->db->prepare('SELECT * FROM inventories WHERE item_id = :item_id');
        $item->execute([':item_id' => $input['item_id']]);
        $itemData = $item->fetch();

        if (!$itemData) {
            echo ApiResponse::notFound('Item not found');
            return;
        }

        if ($itemData['stock_qty'] > $itemData['min_stock']) {
            echo ApiResponse::success([
                'item_id' => $input['item_id'],
                'alert_triggered' => false,
                'reason' => 'Stock is above minimum threshold'
            ], 'No alert needed', 200);
            return;
        }

        // Alert triggered
        $alertLevel = $itemData['stock_qty'] == 0 ? 'CRITICAL' : 'WARNING';
        $message = "Stock Alert: {$itemData['item_name']} is at critical level (Stock: {$itemData['stock_qty']}, Min: {$itemData['min_stock']})";

        echo ApiResponse::success([
            'item_id' => $input['item_id'],
            'item_name' => $itemData['item_name'],
            'current_stock' => $itemData['stock_qty'],
            'minimum_stock' => $itemData['min_stock'],
            'alert_level' => $alertLevel,
            'alert_triggered' => true,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ], 'Alert triggered', 200);
    }

    /**
     * GET /api/admin/bookings
     * Get booking statistics
     * 
     * Query params:
     * - date: YYYY-MM-DD (specific date)
     * - status: Pending, Confirmed, In-Service, Selesai, Canceled
     */
    public function getBookings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $this->requireAdmin();

        $date = $_GET['date'] ?? date('Y-m-d');
        $status = $_GET['status'] ?? null;

        $where = "DATE(r.schedule_time) = :date";
        $params = [':date' => $date];

        if ($status) {
            $where .= " AND r.STATUS = :status";
            $params[':status'] = $status;
        }

        $bookings = $this->db->prepare(
            "SELECT r.res_id, COALESCE(u.NAME, 'Guest') AS customer_name, r.STATUS, r.schedule_time, r.dp_amount, r.is_dp_paid
             FROM reservations r
             LEFT JOIN users u ON r.user_id = u.user_id
             WHERE {$where}
             ORDER BY r.schedule_time ASC"
        );
        $bookings->execute($params);
        $bookingData = $bookings->fetchAll();

        // Get summary
        $total = count($bookingData);
        $confirmed = count(array_filter($bookingData, fn($b) => $b['STATUS'] === 'Confirmed'));
        $inService = count(array_filter($bookingData, fn($b) => $b['STATUS'] === 'In-Service'));
        $completed = count(array_filter($bookingData, fn($b) => $b['STATUS'] === 'Selesai'));

        echo ApiResponse::success([
            'date' => $date,
            'summary' => [
                'total_bookings' => $total,
                'confirmed' => $confirmed,
                'in_service' => $inService,
                'completed' => $completed
            ],
            'bookings' => $bookingData
        ], 'Booking statistics', 200);
    }

}
