<?php

namespace App\Controllers;

use App\Models\SeatModel;
use App\Models\MenusModel;
use App\Models\OrdersModel;
use App\Core\ApiResponse;

/**
 * QR Order Controller
 * Handle cafe QR-based ordering
 * 
 * Flow:
 * 1. Guest scans QR → index.php?page=qrorder&action=start&token=S01
 * 2. Choose menu items
 * 3. Checkout with payment method (QRIS or Cash)
 * 4. Order created → success page
 */
class QROrderController
{
    private SeatModel $seatModel;
    private MenusModel $menusModel;
    private OrdersModel $ordersModel;

    public function __construct()
    {
        $this->seatModel = new SeatModel();
        $this->menusModel = new MenusModel();
        $this->ordersModel = new OrdersModel();
    }

    /**
     * Start QR order flow
     * GET: index.php?page=qrorder&action=start&token=ABC123
     */
    public function start()
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            ApiResponse::error('Invalid QR token', 400);
            return;
        }

        // Token is the seat_id (from QR code URL: merish.test/qr?seat=S01)
        $seatId = strtoupper($token);

        // Get seat info
        $seat = $this->seatModel->findById($seatId);
        if (!$seat) {
            ApiResponse::error('Seat not found', 404);
            return;
        }

        // Check if user is logged in (member flow) or guest
        $userId = $_SESSION['user_id'] ?? null;
        $isGuest = !$userId;

        // Store in session (replaces open_bills)
        $_SESSION['qr_order'] = [
            'token' => $token,
            'seat_id' => $seatId,
            'is_guest' => $isGuest,
            'user_id' => $userId,
            'seat_name' => $seat['seat_name']
        ];

        // Get all menus
        $menus = $this->menusModel->findAll();

        // View: menu selection
        include __DIR__ . '/../Views/QrOrder/menu_selection.php';
    }

    /**
     * Add item to order
     * POST: index.php?page=qrorder&action=addItem
     */
    public function addItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Method not allowed', 405);
            return;
        }

        $qrOrder = $_SESSION['qr_order'] ?? null;
        if (!$qrOrder) {
            ApiResponse::error('No active QR order session', 400);
            return;
        }

        $menuId = $_POST['menu_id'] ?? null;
        $qty = (int) ($_POST['qty'] ?? 1);

        if (!$menuId || $qty < 1) {
            ApiResponse::error('Invalid menu or quantity', 400);
            return;
        }

        // Get menu item
        $menu = $this->menusModel->findById($menuId);
        if (!$menu) {
            ApiResponse::error('Menu item not found', 404);
            return;
        }

        // Add to cart session
        if (!isset($_SESSION['qr_cart'])) {
            $_SESSION['qr_cart'] = [];
        }

        if (isset($_SESSION['qr_cart'][$menuId])) {
            $_SESSION['qr_cart'][$menuId]['qty'] += $qty;
        } else {
            $_SESSION['qr_cart'][$menuId] = [
                'menu_id' => $menuId,
                'menu_name' => $menu['menu_name'],
                'price' => $menu['price'],
                'qty' => $qty
            ];
        }

        // Calculate total
        $total = 0;
        foreach ($_SESSION['qr_cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        ApiResponse::success([
            'message' => 'Item added to cart',
            'cart_total' => $total,
            'cart_count' => count($_SESSION['qr_cart'])
        ]);
    }

    /**
     * Remove item from cart
     * POST: index.php?page=qrorder&action=removeItem
     */
    public function removeItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Method not allowed', 405);
            return;
        }

        $menuId = $_POST['menu_id'] ?? null;
        if (!$menuId || !isset($_SESSION['qr_cart'][$menuId])) {
            ApiResponse::error('Item not in cart', 404);
            return;
        }

        unset($_SESSION['qr_cart'][$menuId]);

        // Calculate total
        $total = 0;
        foreach ($_SESSION['qr_cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        ApiResponse::success([
            'message' => 'Item removed from cart',
            'cart_total' => $total,
            'cart_count' => count($_SESSION['qr_cart'])
        ]);
    }

    /**
     * View cart before checkout
     * GET: index.php?page=qrorder&action=checkout
     */
    public function checkout()
    {
        $qrOrder = $_SESSION['qr_order'] ?? null;
        $cart = $_SESSION['qr_cart'] ?? [];

        if (!$qrOrder || empty($cart)) {
            ApiResponse::error('No items in cart', 400);
            return;
        }

        // Calculate total
        $total = 0;
        $items = [];
        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['qty'];
            $total += $itemTotal;
            $items[] = $item;
        }

        // Get payment methods (orders.payment_method ENUM: QRIS, Cash)
        $paymentMethods = ['QRIS', 'Cash'];

        // View: checkout
        include __DIR__ . '/../Views/QrOrder/checkout.php';
    }

    /**
     * Process payment
     * POST: index.php?page=qrorder&action=processPayment
     */
    public function processPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Method not allowed', 405);
            return;
        }

        $qrOrder = $_SESSION['qr_order'] ?? null;
        if (!$qrOrder) {
            ApiResponse::error('No active QR order', 400);
            return;
        }

        $paymentMethod = $_POST['payment_method'] ?? null;
        $cart = $_SESSION['qr_cart'] ?? [];

        if (!$paymentMethod || empty($cart)) {
            ApiResponse::error('Invalid payment method or empty cart', 400);
            return;
        }

        // Validate payment method (orders.payment_method ENUM: QRIS, Cash)
        if (!in_array($paymentMethod, ['QRIS', 'Cash'])) {
            ApiResponse::error('Invalid payment method', 400);
            return;
        }

        // Calculate total
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
        $tax = (int) round($subtotal * 0.1);
        $totalWithTax = $subtotal + $tax;

        // Create cafe order record
        $orderId = $this->ordersModel->create([
            'guest_name' => $qrOrder['is_guest'] ? 'Guest' : 'Member',
            'seat_id' => $qrOrder['seat_id'],
            'total_amount' => $totalWithTax,
            'payment_method' => $paymentMethod,
            'payment_status' => ($paymentMethod === 'QRIS') ? 'Paid' : 'Unpaid',
            'status' => 'New'
        ]);

        if (!$orderId) {
            ApiResponse::error('Failed to create order', 500);
            return;
        }

        // Add items to order_details table
        foreach ($cart as $item) {
            $this->ordersModel->createDetail([
                'order_id' => $orderId,
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'subtotal' => $item['price'] * $item['qty']
            ]);
        }

        // Store payment info in session
        $_SESSION['payment'] = [
            'order_id' => $orderId,
            'amount' => $total,
            'method' => $paymentMethod,
            'seat_name' => $qrOrder['seat_name'] ?? ''
        ];

        // Clear cart and order session
        unset($_SESSION['qr_cart']);
        unset($_SESSION['qr_order']);

        // Redirect to success page
        header('Location: index.php?page=qrorder&action=success&order_id=' . $orderId);
        exit;
    }

    /**
     * Payment confirmation (after QRIS/LinkAja payment)
     * GET: index.php?page=qrorder&action=paymentStatus&guest_order_id=123
     */
    public function paymentStatus()
    {
        $guestOrderId = $_GET['guest_order_id'] ?? null;
        if (!$guestOrderId) {
            ApiResponse::error('Missing guest order ID', 400);
            return;
        }

        $order = $this->ordersModel->findById((int)$guestOrderId);
        if (!$order) {
            ApiResponse::error('Order not found', 404);
            return;
        }

        // Check payment status (in real implementation, query payment gateway)
        $isPaid = $order['payment_status'] === 'Paid';

        if ($isPaid) {
            // Mark order as paid and move to barista queue
            $this->ordersModel->update((int)$guestOrderId, [
                'payment_status' => 'Paid',
                'payment_method' => $_GET['method'] ?? 'QRIS',
                'status' => 'In Progress'
            ]);
        }

        // View: payment confirmation
        include __DIR__ . '/../Views/QrOrder/payment_status.php';
    }

    /**
     * Order success page
     * GET: index.php?page=qrorder&action=success&order_id=123
     */
    public function success()
    {
        $orderId = $_GET['order_id'] ?? null;
        $payment = $_SESSION['payment'] ?? null;

        if (!$orderId) {
            header('Location: index.php');
            exit;
        }

        $order = $this->ordersModel->findById((int)$orderId);
        if (!$order) {
            header('Location: index.php');
            exit;
        }

        $orderDetails = $this->ordersModel->getOrderDetails((int)$orderId);

        // View: success
        include __DIR__ . '/../Views/QrOrder/success.php';
    }

    /**
     * Get current cart (AJAX)
     * GET: index.php?page=qrorder&action=getCart
     */
    public function getCart()
    {
        $cart = $_SESSION['qr_cart'] ?? [];
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        ApiResponse::success([
            'cart' => $cart,
            'total' => $total,
            'count' => count($cart)
        ]);
    }
}
