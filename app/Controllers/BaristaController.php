<?php

namespace App\Controllers;

use App\Models\OrdersModel;

class BaristaController
{
    private OrdersModel $ordersModel;

    public function __construct()
    {
        // Check role
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'barista') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->ordersModel = new OrdersModel();
    }

    public function index()
    {
        $orders = $this->ordersModel->findPendingOrInProgress();
        require __DIR__ . '/../Views/Barista/index.php';
    }

    public function updateOrderStatus()
    {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($orderId > 0 && !empty($status)) {
            $this->ordersModel->update($orderId, ['status' => $status]);
            $_SESSION['success'] = 'Order status updated';
            header('Location: index.php?page=barista');
        } else {
            $_SESSION['error'] = 'Invalid data';
            header('Location: index.php?page=barista');
        }
        exit;
    }

    public function orderHistory()
    {
        $orders = $this->ordersModel->findAll();
        require __DIR__ . '/../Views/Barista/order_history.php';
    }
}
