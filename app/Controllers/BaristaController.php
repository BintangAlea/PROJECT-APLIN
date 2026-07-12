<?php

namespace App\Controllers;

use App\Models\OrdersModel;
use App\Models\MenusModel;

class BaristaController
{
    private OrdersModel $ordersModel;
    private MenusModel $menusModel;
    private \PDO $db;

    public function __construct()
    {
        // Check role
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'barista') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->db = \App\Core\Database::getConnection();
        $this->ordersModel = new OrdersModel();
        $this->menusModel = new MenusModel();
    }

    public function index()
    {
        $orders = $this->ordersModel->findAll();
        $pageTitle = 'Barista Dashboard';
        $activeMenu = 'kds';
        require __DIR__ . '/../Views/Barista/index.php';
    }

    public function menuAvailability()
    {
        $menus = $this->menusModel->findAll();
        $pageTitle = 'Item Availability Manager';
        $activeMenu = 'availability';
        require __DIR__ . '/../Views/Barista/menu_availability.php';
    }

    public function updateOrderStatus()
    {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        $normalizedStatus = $this->normalizeOrderStatus($status);

        if ($orderId > 0 && $normalizedStatus !== null) {
            $this->ordersModel->update($orderId, ['STATUS' => $normalizedStatus]);
            // If AJAX request, return JSON; otherwise fallback to session+redirect
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Order status updated']);
                exit;
            }
            $_SESSION['success'] = 'Order status updated';
            header('Location: index.php?page=barista');
        } else {
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                exit;
            }
            $_SESSION['error'] = 'Invalid data';
            header('Location: index.php?page=barista');
        }
        exit;
    }

    private function normalizeOrderStatus(string $status): ?string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'new', 'pending' => 'New',
            'in progress', 'making', 'processing' => 'In Progress',
            'ready' => 'Ready',
            'done', 'completed', 'selesai' => 'Completed',
            default => null,
        };
    }

    public function orderHistory()
    {
        $orders = $this->ordersModel->findAll();
        require __DIR__ . '/../Views/Barista/order_history.php';
    }

    public function paymentCashier()
    {
        // Auto-heal/update any QRIS orders that are incorrectly marked as Unpaid to Paid
        $this->db->exec("UPDATE db_merish_cafe.orders SET payment_status = 'Paid' WHERE payment_method = 'QRIS' AND payment_status = 'Unpaid'");

        $statusFilter = $_GET['filter'] ?? 'unpaid';

        // Fetch orders based on filter
        if ($statusFilter === 'paid') {
            $stmt = $this->db->query("
                SELECT o.*, 
                       (SELECT GROUP_CONCAT(CONCAT(m.menu_name, ' (x', od.qty, ')') SEPARATOR ', ') 
                        FROM db_merish_cafe.order_details od 
                        JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id 
                        WHERE od.order_id = o.order_id) as items_summary
                FROM db_merish_cafe.orders o
                WHERE o.payment_status = 'Paid'
                ORDER BY o.order_date DESC
            ");
        } else {
            $stmt = $this->db->query("
                SELECT o.*, 
                       (SELECT GROUP_CONCAT(CONCAT(m.menu_name, ' (x', od.qty, ')') SEPARATOR ', ') 
                        FROM db_merish_cafe.order_details od 
                        JOIN db_merish_cafe.menus m ON od.menu_id = m.menu_id 
                        WHERE od.order_id = o.order_id) as items_summary
                FROM db_merish_cafe.orders o
                WHERE o.payment_status = 'Unpaid'
                ORDER BY o.order_date DESC
            ");
        }
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $selectedOrderId = (int)($_GET['order_id'] ?? 0);
        $selectedOrder = null;
        $selectedOrderDetails = [];
        if ($selectedOrderId > 0) {
            $orderStmt = $this->db->prepare("SELECT * FROM db_merish_cafe.orders WHERE order_id = :id");
            $orderStmt->execute([':id' => $selectedOrderId]);
            $selectedOrder = $orderStmt->fetch(\PDO::FETCH_ASSOC);

            if ($selectedOrder) {
                $selectedOrderDetails = $this->ordersModel->getOrderDetails($selectedOrderId);
            }
        }

        $pageTitle = 'Cafe Cashier';
        $activeMenu = 'payments';
        
        $flashSuccess = $_SESSION['success'] ?? null;
        $flashError = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require __DIR__ . '/../Views/Barista/payments.php';
    }

    public function settlePayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=barista&action=paymentCashier');
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $paymentMethod = $_POST['payment_method'] ?? 'Cash';

        if ($orderId > 0 && in_array($paymentMethod, ['Cash', 'QRIS'], true)) {
            $updated = $this->ordersModel->update($orderId, [
                'payment_status' => 'Paid',
                'payment_method' => $paymentMethod
            ]);

            if ($updated) {
                $_SESSION['success'] = 'Pembayaran berhasil diselesaikan!';
            } else {
                $_SESSION['error'] = 'Gagal memproses pembayaran.';
            }
        } else {
            $_SESSION['error'] = 'Input tidak valid.';
        }

        header('Location: index.php?page=barista&action=paymentCashier&order_id=' . $orderId);
        exit;
    }
}
