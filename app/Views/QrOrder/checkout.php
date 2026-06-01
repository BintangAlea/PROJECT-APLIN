<?php 
// QR Order - Checkout Review
// Review order items and choose payment method
$seatName = $seat_name ?? 'Your Seat';
$isGuest = $is_guest ?? true;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merish Cafe - Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { background: #f8f6f3; }
        .checkout-header {
            background: linear-gradient(135deg, #8B7355 0%, #6B5B4F 100%);
            color: white;
            padding: 2rem;
            border-radius: 0 0 2rem 2rem;
            margin-bottom: 2rem;
        }
        .order-summary {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-details {
            flex: 1;
        }
        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }
        .item-qty {
            font-size: 0.85rem;
            color: #666;
        }
        .item-total {
            font-weight: 700;
            color: #D4A574;
            text-align: right;
            min-width: 100px;
        }
        .price-section {
            background: #f8f6f3;
            border-radius: 1rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        .price-row.total {
            border-top: 2px solid #D4A574;
            padding-top: 1rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
        }
        .payment-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .payment-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        .payment-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
        }
        .payment-option {
            position: relative;
        }
        .payment-option input[type="radio"] {
            display: none;
        }
        .payment-option label {
            display: block;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        .payment-option input[type="radio"]:checked + label {
            border-color: #8B7355;
            background: #f5ede3;
        }
        .payment-method-name {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 0.5rem;
        }
        .payment-method-icon {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .btn-back {
            flex: 1;
            background: #E8D5C4;
            color: #333;
            border: none;
            padding: 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-back:hover {
            background: #DCC6B5;
        }
        .btn-checkout {
            flex: 1;
            background: #8B7355;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-checkout:hover {
            background: #6B5B4F;
        }
        .alert-note {
            background: #FFF3CD;
            border-left: 4px solid #FFC107;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="checkout-header">
        <div class="container">
            <h1 class="mb-2">Checkout</h1>
            <p class="mb-0">
                <span style="display: inline-block; background: rgba(255,255,255,0.2); padding: 0.5rem 1.5rem; border-radius: 2rem;">
                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($seatName); ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" style="margin-bottom: 2rem;">
        <!-- Order Summary -->
        <div class="order-summary">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #333; margin-bottom: 1rem;">
                <i class="bi bi-bag-check"></i> Pesanan Anda
            </h2>

            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <div class="item-details">
                            <div class="item-name"><?php echo htmlspecialchars($item['menu_name']); ?></div>
                            <div class="item-qty">
                                <?php echo $item['qty']; ?>x @ IDR <?php echo number_format($item['price']); ?>
                            </div>
                        </div>
                        <div class="item-total">
                            IDR <?php echo number_format($item['price'] * $item['qty']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Price Breakdown -->
            <div class="price-section">
                <div class="price-row">
                    <span>Subtotal:</span>
                    <span>IDR <?php echo number_format($total); ?></span>
                </div>
                <div class="price-row">
                    <span>Tax (10%):</span>
                    <span>IDR <?php echo number_format($total * 0.1); ?></span>
                </div>
                <div class="price-row total">
                    <span>Total Pembayaran:</span>
                    <span>IDR <?php echo number_format($total * 1.1); ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Method Section -->
        <div class="payment-section">
            <h2 class="payment-title">Metode Pembayaran</h2>

            <?php if ($isGuest): ?>
                <div class="alert-note">
                    <i class="bi bi-info-circle"></i>
                    <strong>Info:</strong> Sebagai guest, Anda hanya dapat membayar dengan QRIS, LinkAja, atau Tunai.
                    Pembayaran harus diselesaikan dalam 5 menit.
                </div>
            <?php else: ?>
                <div class="alert-note">
                    <i class="bi bi-info-circle"></i>
                    <strong>Opsi Member:</strong> Anda bisa menambahkan ke open bill atau membayar langsung sekarang.
                </div>
            <?php endif; ?>

            <form id="checkoutForm" method="POST" action="index.php?page=qrorder&action=processPayment">
                <div class="payment-options">
                    <?php foreach ($paymentMethods as $method): ?>
                        <div class="payment-option">
                            <input type="radio" id="payment_<?php echo strtolower($method); ?>" name="payment_method" value="<?php echo htmlspecialchars($method); ?>" <?php echo ($method === 'QRIS') ? 'checked' : ''; ?>>
                            <label for="payment_<?php echo strtolower($method); ?>">
                                <span class="payment-method-icon">
                                    <?php 
                                        $icons = [
                                            'QRIS' => '📱',
                                            'LinkAja' => '💳',
                                            'Cash' => '💵',
                                            'Member Bill' => '📋'
                                        ];
                                        echo $icons[$method] ?? '💰';
                                    ?>
                                </span>
                                <span class="payment-method-name"><?php echo $method; ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn-back" onclick="window.history.back()">
                <i class="bi bi-arrow-left"></i> Kembali
            </button>
            <button class="btn-checkout" id="confirmCheckout">
                <i class="bi bi-check-circle"></i> Konfirmasi Pembayaran
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('confirmCheckout').addEventListener('click', () => {
            document.getElementById('checkoutForm').submit();
        });
    </script>
</body>
</html>
