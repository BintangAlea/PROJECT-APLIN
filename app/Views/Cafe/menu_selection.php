<?php
/**
 * Cafe Menu Selection
 * Display menu items and allow customer to add to cart
 */

// Get menu data from session
$billId = (int) (($_SESSION['cafe_bill_id'] ?? sessionStorage.getItem('cafe_bill_id')) ?: 0);
$scenario = $_SESSION['cafe_scenario'] ?? 'unknown';
$customerName = $_SESSION['cafe_customer_name'] ?? 'Guest';

// For now, show placeholder - will be populated from API response
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Kafe - MERISH</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #f4eded;
            min-height: 100vh;
        }

        .topbar {
            background: #fffafa;
            border-bottom: 1px solid #d9ccd0;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: #8d616f;
            text-decoration: none;
            letter-spacing: 2px;
        }

        .topbar-back {
            color: #7a6e73;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .topbar-back:hover {
            color: #8d616f;
        }

        .header {
            background: linear-gradient(135deg, #8d616f 0%, #724e5a 100%);
            color: white;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 100px;
        }

        .menu-card {
            background: #fffafa;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #d9ccd0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(141, 97, 111, 0.15);
        }

        .menu-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #ead2db 0%, #fdf8f8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }

        .menu-info {
            padding: 15px;
        }

        .menu-name {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #4f4248;
        }

        .menu-description {
            font-size: 12px;
            color: #7a6e73;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .menu-price {
            font-size: 18px;
            font-weight: bold;
            color: #8d616f;
            margin-bottom: 10px;
        }

        .qty-selector {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .qty-selector button {
            width: 30px;
            height: 30px;
            border: 1px solid #d9ccd0;
            background: #fffafa;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
        }

        .qty-selector button:hover {
            background: #ead2db;
        }

        .qty-selector input {
            width: 50px;
            text-align: center;
            border: 1px solid #d9ccd0;
            padding: 5px;
            font-size: 14px;
            background: #fdf8f8;
        }

        .add-to-cart {
            width: 100%;
            padding: 10px;
            background: #8d616f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s;
        }

        .add-to-cart:hover {
            background: #724e5a;
        }

        .cart-summary {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fffafa;
            padding: 15px 20px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
            border-top: 1px solid #d9ccd0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .cart-items {
            display: flex;
            gap: 20px;
            flex: 1;
        }

        .cart-item {
            display: flex;
            flex-direction: column;
        }

        .cart-item label {
            font-size: 12px;
            color: #7a6e73;
        }

        .cart-item strong {
            color: #4f4248;
        }

        .cart-actions {
            display: flex;
            gap: 10px;
        }

        .cart-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        .btn-continue {
            background: #8d616f;
            color: white;
        }

        .btn-continue:hover {
            background: #724e5a;
        }

        .btn-checkout {
            background: #724e5a;
            color: white;
        }

        .btn-checkout:hover {
            background: #5e3f4a;
        }

        .loading {
            text-align: center;
            padding: 40px 20px;
            color: #7a6e73;
        }

        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-left: 10px;
            border: 3px solid #ead2db;
            border-top: 3px solid #8d616f;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7a6e73;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(79, 66, 72, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fffafa;
            padding: 30px;
            border-radius: 4px;
            max-width: 400px;
            border: 1px solid #d9ccd0;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 20px;
            color: #4f4248;
        }

        .modal-content p {
            color: #7a6e73;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .delivery-options {
            margin-bottom: 20px;
        }

        .delivery-options label {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #d9ccd0;
            border-radius: 4px;
            cursor: pointer;
        }

        .delivery-options label:hover {
            background: #f4eded;
        }

        .delivery-options input[type="radio"] {
            margin-right: 10px;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-cancel {
            background: #ead2db;
            color: #4f4248;
        }

        .btn-confirm {
            background: #8d616f;
            color: white;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="/?page=home" class="topbar-back">&larr; Kembali</a>
        <a href="/?page=home" class="topbar-brand">MERISH</a>
    </div>

    <div class="header">
        <h1>☕ Menu Kafe</h1>
        <div class="subtitle">Pilih menu favorit Anda</div>
    </div>

    <div class="container">
        <div id="menuContainer" class="menu-grid">
            <div class="loading">Memuat menu...</div>
        </div>
    </div>

    <div class="cart-summary">
        <div class="cart-items">
            <div class="cart-item">
                <label>Total Items</label>
                <strong id="cartCount">0</strong>
            </div>
            <div class="cart-item">
                <label>Subtotal</label>
                <strong id="cartTotal">Rp 0</strong>
            </div>
        </div>
        <div class="cart-actions">
            <button class="btn-continue" onclick="continueShopping()">Lanjut Belanja</button>
            <button class="btn-checkout" onclick="openCheckout()">Bayar</button>
        </div>
    </div>

    <!-- Delivery Method Modal -->
    <div class="modal" id="deliveryModal">
        <div class="modal-content">
            <h2>Metode Pengiriman</h2>
            <p>Pilih bagaimana Anda akan menerima pesanan:</p>
            <div class="delivery-options">
                <label>
                    <input type="radio" name="delivery" value="dine-in" checked>
                    <span>Makan di Tempat (Dine-In)</span>
                </label>
                <label>
                    <input type="radio" name="delivery" value="takeaway">
                    <span>Bawa Pulang (Takeaway)</span>
                </label>
            </div>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal()">Batal</button>
                <button class="btn-confirm" onclick="confirmCheckout()">Lanjut</button>
            </div>
        </div>
    </div>

    <script>
        let cart = {};
        let menuData = [];
        let billId = null;
        let scenario = null;

        // Load menu data from sessionStorage (set by QR scan page)
        function loadMenuData() {
            billId = sessionStorage.getItem('cafe_bill_id');
            scenario = sessionStorage.getItem('cafe_scenario');

            if (!billId) {
                alert('Data tagihan tidak ditemukan. Silakan scan QR lagi.');
                window.location.href = '/?page=cafe&action=scan';
                return;
            }

            // Fetch menu from API (will be implemented)
            fetchMenu();
        }

        function fetchMenu() {
            // Menus should have been passed from scanQR API
            // Check if we have scanQRResponse in sessionStorage
            const scanResponse = sessionStorage.getItem('cafe_scan_response');
            
            if (!scanResponse) {
                alert('Scan QR terlebih dahulu');
                window.location.href = '/?page=cafe&action=scan';
                return;
            }

            try {
                const response = JSON.parse(scanResponse);
                const menus = response.data.menus || [];
                
                // Store additional context
                sessionStorage.setItem('cafe_scenario', response.data.scenario);
                sessionStorage.setItem('cafe_customer_role', response.data.customer_role);
                
                if (menus.length === 0) {
                    const container = document.getElementById('menuContainer');
                    container.innerHTML = '<div class="empty-state">Tidak ada menu tersedia</div>';
                    return;
                }
                
                renderMenu(menus);
            } catch (error) {
                console.error('Error parsing scan response:', error);
                alert('Data QR tidak valid');
                window.location.href = '/?page=cafe&action=scan';
            }
        }

        function renderMenu(menus) {
            menuData = menus;
            const container = document.getElementById('menuContainer');
            
            if (menus.length === 0) {
                container.innerHTML = '<div class="empty-state">Tidak ada menu tersedia</div>';
                return;
            }

            container.innerHTML = menus.map(menu => {
                const imageEmoji = menu.emoji || '🍽️';
                return `
                <div class="menu-card">
                    <div class="menu-image">${imageEmoji}</div>
                    <div class="menu-info">
                        <div class="menu-name">${menu.menu_name}</div>
                        <div class="menu-description">${menu.description || menu.category || ''}</div>
                        <div class="menu-price">Rp ${formatPrice(menu.price)}</div>
                        <div class="qty-selector">
                            <button onclick="updateQty('${menu.menu_id}', -1)">−</button>
                            <input type="number" id="qty-${menu.menu_id}" value="0" min="0" readonly>
                            <button onclick="updateQty('${menu.menu_id}', 1)">+</button>
                        </div>
                        <button class="add-to-cart" onclick="addToCart('${menu.menu_id}')">Tambah ke Pesanan</button>
                    </div>
                </div>
            `}).join('');
        }

        function updateQty(menuId, delta) {
            const input = document.getElementById(`qty-${menuId}`);
            let qty = parseInt(input.value) || 0;
            qty = Math.max(0, qty + delta);
            input.value = qty;
        }

        function addToCart(menuId) {
            const input = document.getElementById(`qty-${menuId}`);
            const qty = parseInt(input.value) || 0;

            if (qty <= 0) {
                alert('Silakan pilih jumlah yang lebih dari 0');
                return;
            }

            const menu = menuData.find(m => m.menu_id === menuId);
            if (!menu) return;

            if (!cart[menuId]) {
                cart[menuId] = {
                    ...menu,
                    qty: 0
                };
            }

            cart[menuId].qty += qty;
            input.value = 0;
            updateCartSummary();
        }

        function updateCartSummary() {
            const items = Object.values(cart);
            const totalQty = items.reduce((sum, item) => sum + item.qty, 0);
            const totalPrice = items.reduce((sum, item) => sum + (item.price * item.qty), 0);

            document.getElementById('cartCount').textContent = totalQty;
            document.getElementById('cartTotal').textContent = `Rp ${formatPrice(totalPrice)}`;
        }

        function continueShopping() {
            // Just close if modal was open
            closeModal();
        }

        function openCheckout() {
            const items = Object.values(cart);
            if (items.length === 0) {
                alert('Keranjang Anda kosong');
                return;
            }

            document.getElementById('deliveryModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('deliveryModal').classList.remove('active');
        }

        function confirmCheckout() {
            const deliveryMethod = document.querySelector('input[name="delivery"]:checked').value;
            
            // Store cart data
            sessionStorage.setItem('cafe_cart', JSON.stringify(cart));
            sessionStorage.setItem('cafe_delivery_method', deliveryMethod);

            // Redirect to checkout
            window.location.href = '/?page=cafe&action=checkout';
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        }

        // Initialize on load
        window.addEventListener('load', loadMenuData);
    </script>
</body>
</html>
