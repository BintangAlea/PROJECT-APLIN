<?php

namespace App\Controllers;

use App\Models\MenusModel;

class CafeController
{
    /**
     * Display cafe menu page
     */
    public function index()
    {
        $isLoggedIn = isset($_SESSION['user_id']);
        $salonHistory = [];
        $cafeHistory = [];

        if ($isLoggedIn) {
            $userModel = new \App\Models\UsersModel();
            $salonHistory = $userModel->getSalonHistory((int)$_SESSION['user_id']);
            $cafeHistory = $userModel->getCafeHistory($_SESSION['full_name'] ?? '');
        }

        return [
            'view' => 'Cafe.index',
            'data' => [
                'salonHistory' => $salonHistory,
                'cafeHistory' => $cafeHistory
            ]
        ];
    }

    public function cart()
    {
        $menusModel = new MenusModel();
        $menus = $menusModel->findAll();

        require __DIR__ . '/../Views/Cafe/cart.php';
    }

    public function addToCart()
    {
        $seat = $_POST['seat'] ?? $_GET['seat'] ?? '';
        $redirectUrl = 'index.php?page=cafe&action=cart';
        if ($seat !== '') {
            $redirectUrl .= '&seat=' . urlencode($seat);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectUrl);
            exit;
        }

        $menuId = (string) ($_POST['menu_id'] ?? '');
        $menuName = trim($_POST['menu_name'] ?? 'Menu Item');
        $price = (int) ($_POST['price'] ?? 0);
        $image = trim($_POST['image'] ?? '');
        $category = trim($_POST['category'] ?? 'kopi');

        if ($menuId === '' || $price <= 0) {
            header('Location: ' . $redirectUrl);
            exit;
        }

        $_SESSION['cart'] = $_SESSION['cart'] ?? [];

        if (!isset($_SESSION['cart'][$menuId])) {
            $_SESSION['cart'][$menuId] = [
                'menu_id' => $menuId,
                'menu_name' => $menuName,
                'price' => $price,
                'qty' => 0,
                'image' => $image,
                'category' => $category,
            ];
        }

        $_SESSION['cart'][$menuId]['qty'] = (int) ($_SESSION['cart'][$menuId]['qty'] ?? 0) + 1;

        header('Location: ' . $redirectUrl);
        exit;
    }

    public function updateCart()
    {
        $seat = $_POST['seat'] ?? $_GET['seat'] ?? '';
        $redirectUrl = 'index.php?page=cafe&action=cart';
        if ($seat !== '') {
            $redirectUrl .= '&seat=' . urlencode($seat);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectUrl);
            exit;
        }

        $menuId = (string) ($_POST['menu_id'] ?? '');
        $qty = max(0, (int) ($_POST['qty'] ?? 0));

        if ($menuId === '' || !isset($_SESSION['cart'][$menuId])) {
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($qty <= 0) {
            unset($_SESSION['cart'][$menuId]);
        } else {
            $_SESSION['cart'][$menuId]['qty'] = $qty;
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    public function removeFromCart()
    {
        $menuId = (string) ($_GET['menu_id'] ?? '');

        if ($menuId !== '' && isset($_SESSION['cart'][$menuId])) {
            unset($_SESSION['cart'][$menuId]);
        }

        header('Location: index.php?page=cafe&action=cart');
        exit;
    }

    public function checkout()
    {
        // Check if this is QR-based flow or legacy session-based
        if (isset($_GET['qr']) || isset($_SESSION['cafe_bill_id'])) {
            // QR-based flow
            require __DIR__ . '/../Views/Cafe/checkout_qr.php';
            exit;
        }

        // Legacy flow
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require __DIR__ . '/../Views/Cafe/checkout.php';
            exit;
        }

        $_SESSION['cafe_order'] = [
            'order_type' => $_POST['order_type'] ?? 'Takeaway',
            'guest_name' => $_POST['guest_name'] ?? ($_SESSION['full_name'] ?? 'Guest'),
            'table_name' => $_POST['table_name'] ?? 'Pick Up',
            'items' => $_SESSION['cart'] ?? [],
            'total_price' => array_reduce($_SESSION['cart'] ?? [], function ($carry, $item) {
                return $carry + ((int)($item['price'] ?? 0) * (int)($item['qty'] ?? 1));
            }, 0),
        ];

        require __DIR__ . '/../Views/Cafe/confirm.php';
    }

    /**
     * New QR-based flow: Scan QR code at table
     */
    public function scan()
    {
        require __DIR__ . '/../Views/Cafe/qr_scan.php';
    }

    /**
     * New QR-based flow: Select menu items
     */
    public function menu()
    {
        require __DIR__ . '/../Views/Cafe/menu_selection.php';
    }

    /**
     * Success page after order placed
     */
    public function success()
    {
        require __DIR__ . '/../Views/Cafe/success.php';
    }

}
