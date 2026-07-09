<?php

namespace App\Controllers;

use App\Models\OrdersModel;
use App\Models\MenusModel;

class BaristaController
{
    private OrdersModel $ordersModel;
    private MenusModel $menusModel;

    public function __construct()
    {
        // Check role
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'barista') {
            header('Location: index.php?page=login');
            exit;
        }
        
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
            $_SESSION['success'] = 'Order status updated';
            header('Location: index.php?page=barista');
        } else {
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
}
