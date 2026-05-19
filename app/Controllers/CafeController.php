<?php

namespace App\Controllers;

use App\Models\MenusModel;
use App\Models\OrdersModel;

class CafeController
{
    private MenusModel $menusModel;
    private OrdersModel $ordersModel;

    public function __construct()
    {
        $this->menusModel = new MenusModel();
        $this->ordersModel = new OrdersModel();
    }

    public function index()
    {
        // Detect seat token from QR code
        $seatToken = $_GET['seat_token'] ?? null;
        $seatId = $_SESSION['seat_id'] ?? null;
        
        if ($seatToken) {
            // Parse token to extract seat_id (format: SEATID_TIMESTAMP)
            $parts = explode('_', $seatToken);
            $seatId = $parts[0] ?? null;
            $_SESSION['seat_id'] = $seatId;
        }

        // Determine user type
        $isGuest = !isset($_SESSION['user_id']);
        $isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['role'] === 'Customer';

        $menus = $this->menusModel->findAll();
        
        require __DIR__ . '/../Views/Cafe/index.php';
    }

    public function addToCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('Invalid request');
        }

        $menuId = $_POST['menu_id'] ?? null;
        $qty = (int)($_POST['qty'] ?? 1);

        if (!$menuId || $qty < 1) {
            $_SESSION['error'] = 'Menu atau quantity tidak valid';
            header('Location: index.php?page=cafe');
            exit;
        }

        $menu = $this->menusModel->findById($menuId);
        if (!$menu) {
            $_SESSION['error'] = 'Menu tidak ditemukan';
            header('Location: index.php?page=cafe');
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$menuId])) {
            $_SESSION['cart'][$menuId]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$menuId] = [
                'menu_name' => $menu['menu_name'],
                'price' => $menu['price'],
                'qty' => $qty
            ];
        }

        $_SESSION['success'] = 'Item ditambahkan ke keranjang';
        header('Location: index.php?page=cafe&action=viewCart');
        exit;
    }

    public function viewCart()
    {
        $cart = $_SESSION['cart'] ?? [];
        require __DIR__ . '/../Views/Cafe/cart.php';
    }

    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=cafe&action=viewCart');
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $_SESSION['error'] = 'Keranjang kosong';
            header('Location: index.php?page=cafe');
            exit;
        }

        $orderType = $_POST['order_type'] ?? 'Dine-In';
        $guestName = $_POST['guest_name'] ?? null;
        $seatId = $_SESSION['seat_id'] ?? null;

        if ($orderType === 'Dine-In' && !$seatId) {
            $_SESSION['error'] = 'Silakan scan QR code di meja terlebih dahulu';
            header('Location: index.php?page=cafe&action=viewCart');
            exit;
        }

        foreach ($cart as $menuId => $item) {
            $result = $this->ordersModel->create([
                'res_id' => $_POST['res_id'] ?? null,
                'guest_name' => $guestName,
                'order_type' => $orderType,
                'seat_id' => $orderType === 'Dine-In' ? $seatId : null,
                'menu_id' => $menuId,
                'qty' => $item['qty'],
                'payment_status' => 'Paid',
                'status' => 'New'
            ]);

            if (!$result) {
                $_SESSION['error'] = 'Gagal membuat pesanan';
                header('Location: index.php?page=cafe&action=viewCart');
                exit;
            }
        }

        unset($_SESSION['cart']);
        $_SESSION['success'] = 'Pesanan berhasil dibuat!';
        header('Location: index.php?page=cafe&action=orderConfirm');
        exit;
    }

    public function orderConfirm()
    {
        require __DIR__ . '/../Views/Cafe/confirm.php';
    }

    public function removeFromCart()
    {
        $menuId = $_GET['menu_id'] ?? null;
        
        if ($menuId && isset($_SESSION['cart'][$menuId])) {
            unset($_SESSION['cart'][$menuId]);
            $_SESSION['success'] = 'Item dihapus dari keranjang';
        }

        header('Location: index.php?page=cafe&action=viewCart');
        exit;
    }
}
