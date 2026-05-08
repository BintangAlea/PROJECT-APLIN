<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\OrdersModel;

class BaristaController
{
    private OrdersModel $ordersModel;

    public function __construct()
    {
        Auth::requireRole('barista');
        $this->ordersModel = new OrdersModel();
    }

    public function index(): void
    {
        $orders = $this->ordersModel->findPendingOrInProgress();
        require_once __DIR__ . '/../Views/Barista/index.php';
    }

    public function updateOrderStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=barista');
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($orderId > 0 && !empty($status)) {
            $this->ordersModel->update($orderId, ['status' => $status]);
            header('Location: /SIB/PROJECT-APLIN/router.php?route=barista&success=Order%20status%20updated');
        } else {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=barista&error=Invalid%20data');
        }
        exit;
    }

    public function orderHistory(): void
    {
        $orders = $this->ordersModel->findAll();
        require_once __DIR__ . '/../Views/Barista/order_history.php';
    }
}
