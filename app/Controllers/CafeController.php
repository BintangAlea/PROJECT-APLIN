<?php

namespace App\Controllers;

class CafeController
{
    /**
     * Display cafe menu page
     */
    public function index()
    {
        return [
            'view' => 'Cafe.index',
            'data' => []
        ];
    }

    public function cart()
    {
        require __DIR__ . '/../Views/Cafe/cart.php';
    }

    public function addToCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=cafe&action=cart');
            exit;
        }

        $menuId = (string) ($_POST['menu_id'] ?? '');
        $menuName = trim($_POST['menu_name'] ?? 'Menu Item');
        $price = (int) ($_POST['price'] ?? 0);
        $image = trim($_POST['image'] ?? '');
        $category = trim($_POST['category'] ?? 'kopi');

        if ($menuId === '' || $price <= 0) {
            header('Location: index.php?page=cafe&action=cart');
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

        header('Location: index.php?page=cafe&action=cart');
        exit;
    }

    public function updateCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=cafe&action=cart');
            exit;
        }

        $menuId = (string) ($_POST['menu_id'] ?? '');
        $qty = max(0, (int) ($_POST['qty'] ?? 0));

        if ($menuId === '' || !isset($_SESSION['cart'][$menuId])) {
            header('Location: index.php?page=cafe&action=cart');
            exit;
        }

        if ($qty <= 0) {
            unset($_SESSION['cart'][$menuId]);
        } else {
            $_SESSION['cart'][$menuId]['qty'] = $qty;
        }

        header('Location: index.php?page=cafe&action=cart');
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
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require __DIR__ . '/../Views/Cafe/checkout.php';
            exit;
        }

        $_SESSION['cafe_order'] = [
            'order_type' => $_POST['order_type'] ?? 'Takeaway',
            'guest_name' => $_POST['guest_name'] ?? ($_SESSION['full_name'] ?? 'Guest'),
            'items' => $_SESSION['cart'] ?? [],
            'total_price' => array_reduce($_SESSION['cart'] ?? [], function ($carry, $item) {
                return $carry + ((int)($item['price'] ?? 0) * (int)($item['qty'] ?? 1));
            }, 0),
        ];

        require __DIR__ . '/../Views/Cafe/confirm.php';
    }

}
