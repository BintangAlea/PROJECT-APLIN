<?php

namespace App\Controllers;

use App\Models\SeatModel;
use App\Models\QRTokenModel;
use App\Models\OpenBillModel;
use App\Models\CafeGuestOrderModel;
use App\Models\MenusModel;
use App\Models\OrdersModel;
use App\Models\UsersModel;
use App\Core\ApiResponse;

/**
 * QR Order Controller
 * Handle cafe QR-based ordering for guest and member flows
 * 
 * Flow:
 * 1. Guest scans QR → index.php?page=qrorder&action=start&token=ABC123
 * 2. Choose menu items
 * 3. Checkout with payment method (guest: QRIS only, member: add to bill)
 * 4. Payment processing or redirect to member bill
 */
class QROrderController
{
    private SeatModel $seatModel;
    private QRTokenModel $tokenModel;
    private OpenBillModel $billModel;
    private CafeGuestOrderModel $guestOrderModel;
    private MenusModel $menusModel;
    private OrdersModel $ordersModel;
    private UsersModel $usersModel;

    public function __construct()
    {
        $this->seatModel = new SeatModel();
        $this->tokenModel = new QRTokenModel();
        $this->billModel = new OpenBillModel();
        $this->guestOrderModel = new CafeGuestOrderModel();
        $this->menusModel = new MenusModel();
        $this->ordersModel = new OrdersModel();
        $this->usersModel = new UsersModel();
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

        // Validate token and get seat
        $seatId = $this->tokenModel->getSeatIdFromToken($token);
        if (!$seatId) {
            ApiResponse::error('QR token expired or invalid', 401);
            return;
        }

        // Get seat info
        $seat = $this->seatModel->findById($seatId);
        if (!$seat) {
            ApiResponse::error('Seat not found', 404);
            return;
        }

        // Check if user is logged in (member flow) or guest
        $userId = $_SESSION['user_id'] ?? null;
        $isGuest = !$userId;

        // Get or create open bill for this seat
        $bill = $this->billModel->getActiveBillForSeat($seatId);
        if (!$bill) {
            // Create new bill
            $billId = $this->billModel->create([
                'user_id' => $userId,
                'seat_id' => $seatId,
                'guest_name' => $isGuest ? 'Guest' : ($this->usersModel->findById($userId)['NAME'] ?? 'Member'),
                'bill_type' => 'Cafe Only'
            ]);
            if (!$billId) {
                ApiResponse::error('Failed to create bill', 500);
                return;
            }
            $bill = $this->billModel->findById($billId);
        }

        // Store in session
        $_SESSION['qr_order'] = [
            'token' => $token,
            'seat_id' => $seatId,
            'bill_id' => $bill['bill_id'],
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
                'category' => $menu['category'],
                'qty' => $qty,
                'image' => $menu['image'] ?? ''
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

        // Get payment methods
        $paymentMethods = $qrOrder['is_guest'] 
            ? ['QRIS', 'LinkAja', 'Cash'] 
            : ['Member Bill', 'QRIS', 'Cash'];

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

        // For guest: only QRIS/LinkAja/Cash
        if ($qrOrder['is_guest'] && !in_array($paymentMethod, ['QRIS', 'LinkAja', 'Cash'])) {
            ApiResponse::error('Payment method not allowed for guests', 403);
            return;
        }

        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        // Create cafe guest order record
        $guestOrderId = $this->guestOrderModel->create([
            'bill_id' => $qrOrder['bill_id'],
            'seat_token' => $qrOrder['token'],
            'guest_name' => $qrOrder['is_guest'] ? 'Guest' : 'Member',
            'seat_id' => $qrOrder['seat_id'],
            'total_amount' => $total
        ]);

        if (!$guestOrderId) {
            ApiResponse::error('Failed to create order', 500);
            return;
        }

        // Add items to orders table
        foreach ($cart as $item) {
            $this->ordersModel->create([
                'res_id' => 0,
                'guest_name' => 'QR Order Guest',
                'order_type' => 'Cafe',
                'seat_id' => $qrOrder['seat_id'],
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'payment_status' => 'Pending',
                'status' => 'New'
            ]);
        }

        // For guests: redirect to payment gateway
        if ($qrOrder['is_guest']) {
            // TODO: Integrate payment gateway (QRIS, LinkAja)
            $_SESSION['payment'] = [
                'guest_order_id' => $guestOrderId,
                'amount' => $total,
                'method' => $paymentMethod
            ];

            header('Location: index.php?page=payment&action=process&order_id=' . $guestOrderId);
            exit;
        }

        // For members: add to open bill and redirect to member bill
        $_SESSION['member_bill_items'][] = [
            'guest_order_id' => $guestOrderId,
            'items' => $cart,
            'total' => $total
        ];

        // Update open bill with cafe charges
        $this->billModel->updateBillTotals($qrOrder['bill_id'], [
            'salon_subtotal' => 0,
            'cafe_subtotal' => $total
        ]);

        // Clear cart
        unset($_SESSION['qr_cart']);
        unset($_SESSION['qr_order']);

        // Redirect to member bill
        header('Location: index.php?page=openbill&bill_id=' . $qrOrder['bill_id']);
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

        $order = $this->guestOrderModel->findById($guestOrderId);
        if (!$order) {
            ApiResponse::error('Order not found', 404);
            return;
        }

        // Check payment status (in real implementation, query payment gateway)
        $isPaid = $order['payment_status'] === 'Paid';

        if ($isPaid) {
            // Mark order as paid and move to barista queue
            $this->guestOrderModel->updatePaymentStatus($guestOrderId, 'Paid', [
                'payment_method' => $_GET['method'] ?? 'QRIS'
            ]);

            // Update order status to "In Progress" (barista queue)
            $this->guestOrderModel->updateStatus($guestOrderId, 'In Progress');
        }

        // View: payment confirmation
        include __DIR__ . '/../Views/QrOrder/payment_status.php';
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
