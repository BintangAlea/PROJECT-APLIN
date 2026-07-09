<?php
// New QR-based checkout page
// Loads cart and scenario from sessionStorage
// Handles both customer/companion (bill addition) and guest (payment) scenarios
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pesanan - MERISH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f4eded;
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Montserrat', sans-serif;
        }

        .topbar {
            background: #fffafa;
            border-bottom: 1px solid #d9ccd0;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 500px;
            margin: 0 auto 0;
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

        .checkout-container {
            background: #fffafa;
            border-radius: 4px;
            border: 1px solid #d9ccd0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            max-width: 500px;
            margin: 20px auto;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            color: #8d616f;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header .scenario-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .scenario-badge.customer {
            background: #ead2db;
            color: #724e5a;
        }

        .scenario-badge.guest {
            background: #fdf8f8;
            color: #8d616f;
            border: 1px solid #d9ccd0;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: #4f4248;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #d9ccd0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ead2db;
        }

        .item-name {
            flex: 1;
            color: #4f4248;
        }

        .item-qty {
            color: #7a6e73;
            margin: 0 10px;
            min-width: 40px;
            text-align: right;
        }

        .item-price {
            font-weight: 600;
            color: #8d616f;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: #4f4248;
        }

        .summary-line.total {
            border-top: 1px solid #d9ccd0;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 700;
            color: #8d616f;
        }

        .message-box {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .message-box.info {
            background: #ead2db;
            color: #724e5a;
            border-left: 4px solid #8d616f;
        }

        .message-box.warning {
            background: #fdf8f8;
            color: #8d616f;
            border-left: 4px solid #8d616f;
        }

        .payment-section {
            background: #fdf8f8;
            padding: 20px;
            border-radius: 4px;
            border: 1px solid #d9ccd0;
            margin: 20px 0;
        }

        .payment-method {
            margin-bottom: 12px;
        }

        .payment-method input[type="radio"] {
            margin-right: 8px;
        }

        .payment-method label {
            margin-bottom: 0;
            cursor: pointer;
            color: #4f4248;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .actions button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-back {
            background: #ead2db;
            color: #4f4248;
        }

        .btn-back:hover {
            background: #d9ccd0;
        }

        .btn-confirm {
            background: #8d616f;
            color: white;
        }

        .btn-confirm:hover:not(:disabled) {
            background: #724e5a;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(141, 97, 111, 0.3);
        }

        .btn-confirm:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #ead2db;
            border-top: 3px solid #8d616f;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background: #fdf8f8;
            color: #724e5a;
            padding: 12px;
            border-radius: 4px;
            border-left: 4px solid #8d616f;
            margin-bottom: 15px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="/?page=home" class="topbar-back">&larr; Kembali</a>
        <a href="/?page=home" class="topbar-brand">MERISH</a>
    </div>

    <div class="checkout-container">
        <div class="header">
            <h1>Ringkasan Pesanan</h1>
            <span id="scenarioBadge" class="scenario-badge"></span>
        </div>

        <div id="errorMessage" class="error-message"></div>

        <!-- Customer/Companion Message -->
        <div id="customerMessage" class="message-box info" style="display: none;">
            Pesanan Anda akan ditambahkan ke <strong>Bill Salon</strong> Anda dan tidak perlu pembayaran terpisah.
        </div>

        <!-- Guest Message -->
        <div id="guestMessage" class="message-box warning" style="display: none;">
            Anda harus melakukan pembayaran sekarang sebelum pesanan dikirim ke dapur.
        </div>

        <!-- Orders Section -->
        <div class="section">
            <div class="section-title">Pesanan Anda</div>
            <div id="ordersList"></div>
        </div>

        <!-- Totals Section -->
        <div class="section">
            <div class="summary-line">
                <span>Subtotal:</span>
                <span>Rp <span id="subtotal">0</span></span>
            </div>
            <div class="summary-line">
                <span>Diskon Sinergis (20%):</span>
                <span id="discountRow" style="display: none;">-Rp <span id="discount">0</span></span>
                <span id="noDiscountRow">-</span>
            </div>
            <div class="summary-line">
                <span>Subtotal Setelah Diskon:</span>
                <span>Rp <span id="subtotalAfterDiscount">0</span></span>
            </div>
            <div class="summary-line">
                <span>Pajak (11%):</span>
                <span>Rp <span id="tax">0</span></span>
            </div>
            <div class="summary-line total">
                <span>Total:</span>
                <span>Rp <span id="total">0</span></span>
            </div>
        </div>

        <!-- Payment Section (only for guests) -->
        <div id="paymentSection" class="payment-section" style="display: none;">
            <div class="section-title">Metode Pembayaran</div>
            <div class="payment-method">
                <input type="radio" id="payQRIS" name="payment" value="QRIS" checked>
                <label for="payQRIS">💳 QRIS / Dana</label>
            </div>
            <div class="payment-method">
                <input type="radio" id="payDebit" name="payment" value="Debit">
                <label for="payDebit">🏧 Debit Card</label>
            </div>
            <div class="payment-method">
                <input type="radio" id="payCash" name="payment" value="Cash">
                <label for="payCash">💵 Tunai</label>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button class="btn-back" onclick="backToMenu()">Kembali</button>
            <button id="confirmBtn" class="btn-confirm" onclick="confirmCheckout()">Lanjutkan</button>
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Memproses...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cartData = [];
        let scenarioData = {
            scenario: null,
            customer_role: null,
            bill_id: null,
            requires_payment: false
        };

        document.addEventListener('DOMContentLoaded', function() {
            initializeCheckout();
        });

        function initializeCheckout() {
            // Load cart from sessionStorage
            const cart = sessionStorage.getItem('cafe_cart');
            const billId = sessionStorage.getItem('cafe_bill_id');
            const scenario = sessionStorage.getItem('cafe_scenario');
            const role = sessionStorage.getItem('cafe_customer_role');

            if (!cart || !billId) {
                showError('Data tidak lengkap. Silakan scan QR ulang.');
                setTimeout(() => window.location.href = '/?page=cafe&action=scan', 2000);
                return;
            }

            try {
                cartData = JSON.parse(cart);
            } catch (error) {
                showError('Data pesanan tidak valid');
                return;
            }

            scenarioData = {
                scenario: scenario || 'unknown',
                customer_role: role || 'unknown',
                bill_id: parseInt(billId),
                requires_payment: (role === 'guest')
            };

            renderCheckout();
        }

        function renderCheckout() {
            // Set scenario badge
            const badge = document.getElementById('scenarioBadge');
            if (scenarioData.customer_role === 'guest') {
                badge.className = 'scenario-badge guest';
                badge.textContent = '🚶 Pengunjung Cafe';
                document.getElementById('guestMessage').style.display = 'block';
                document.getElementById('paymentSection').style.display = 'block';
            } else {
                badge.className = 'scenario-badge customer';
                badge.textContent = scenarioData.customer_role === 'companion' ? '👥 Teman Pelanggan' : '👤 Pelanggan Salon';
                document.getElementById('customerMessage').style.display = 'block';
                document.getElementById('paymentSection').style.display = 'none';
            }

            // Render orders
            const ordersList = document.getElementById('ordersList');
            ordersList.innerHTML = cartData.map(item => `
                <div class="order-item">
                    <div class="item-name">${item.menu_name}</div>
                    <div class="item-qty">x${item.qty}</div>
                    <div class="item-price">Rp ${formatPrice(item.price * item.qty)}</div>
                </div>
            `).join('');

            calculateTotals();
        }

        function calculateTotals() {
            const subtotal = cartData.reduce((sum, item) => sum + (item.price * item.qty), 0);
            
            // No discount yet (will be calculated when added to salon bill at receptionist)
            const discount = 0;
            const subtotalAfterDiscount = subtotal - discount;
            const tax = Math.round(subtotalAfterDiscount * 0.11);
            const total = subtotalAfterDiscount + tax;

            document.getElementById('subtotal').textContent = formatPrice(subtotal);
            document.getElementById('discount').textContent = formatPrice(discount);
            document.getElementById('subtotalAfterDiscount').textContent = formatPrice(subtotalAfterDiscount);
            document.getElementById('tax').textContent = formatPrice(tax);
            document.getElementById('total').textContent = formatPrice(total);

            if (discount > 0) {
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('noDiscountRow').style.display = 'none';
            }
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(Math.round(price));
        }

        function backToMenu() {
            if (confirm('Kembali ke menu? Pesanan belum disimpan.')) {
                window.location.href = 'index.php?page=cafe&action=menu';
            }
        }

        function confirmCheckout() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;

            if (scenarioData.customer_role === 'guest') {
                // Guest: process payment
                const paymentMethod = document.querySelector('input[name="payment"]:checked')?.value;
                if (!paymentMethod) {
                    showError('Pilih metode pembayaran');
                    confirmBtn.disabled = false;
                    return;
                }
                processGuestPayment(paymentMethod);
            } else {
                // Customer/Companion: add to bill
                addOrdersToBill();
            }
        }

        function addOrdersToBill() {
            const loading = document.getElementById('loading');
            loading.style.display = 'block';

            const deliveryMethod = sessionStorage.getItem('cafe_delivery_method') || 'dine-in';
            const orders = cartData.map(item => ({
                bill_id: scenarioData.bill_id,
                menu_id: item.menu_id,
                qty: item.qty,
                delivery_method: deliveryMethod
            }));

            // Add each order to bill
            let completed = 0;
            const total = orders.length;

            orders.forEach((order, index) => {
                fetch('/api/cafe/add-order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(order)
                })
                .then(res => res.json())
                .then(data => {
                    completed++;
                    if (!data.success) {
                        throw new Error(data.message || 'Gagal menambahkan pesanan');
                    }
                    
                    if (completed === total) {
                        showSuccess('Pesanan ditambahkan ke bill Anda!');
                        clearCart();
                        setTimeout(() => {
                            window.location.href = 'index.php?page=cafe&action=menu';
                        }, 1500);
                    }
                })
                .catch(error => {
                    showError('Error: ' + error.message);
                    document.getElementById('confirmBtn').disabled = false;
                    loading.style.display = 'none';
                });
            });
        }

        function processGuestPayment(paymentMethod) {
            const loading = document.getElementById('loading');
            loading.style.display = 'block';

            const deliveryMethod = sessionStorage.getItem('cafe_delivery_method') || 'dine-in';
            const subtotal = cartData.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const tax = Math.round(subtotal * 0.11);
            const total = subtotal + tax;

            const payload = {
                bill_id: scenarioData.bill_id,
                payment_method: paymentMethod,
                amount_paid: total,
                delivery_method: deliveryMethod,
                orders: cartData.map(item => ({
                    menu_id: item.menu_id,
                    qty: item.qty
                }))
            };

            fetch('/api/cafe/payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Pembayaran berhasil! Pesanan dikirim ke dapur.');
                    clearCart();
                    setTimeout(() => {
                        window.location.href = 'index.php?page=cafe&action=menu';
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Pembayaran gagal');
                }
            })
            .catch(error => {
                showError('Error pembayaran: ' + error.message);
                document.getElementById('confirmBtn').disabled = false;
                loading.style.display = 'none';
            });
        }

        function clearCart() {
            sessionStorage.removeItem('cafe_cart');
            sessionStorage.removeItem('cafe_delivery_method');
            sessionStorage.removeItem('cafe_bill_id');
            sessionStorage.removeItem('cafe_scenario');
            sessionStorage.removeItem('cafe_scan_response');
        }

        function showError(message) {
            const errorBox = document.getElementById('errorMessage');
            errorBox.textContent = message;
            errorBox.style.display = 'block';
            setTimeout(() => { errorBox.style.display = 'none'; }, 5000);
        }

        function showSuccess(message) {
            alert(message);
        }
    </script>
</body>
</html>
