<?php
// QR Order - Success Page
$orderStatus = $order['STATUS'] ?? 'New';
$paymentMethod = $order['payment_method'] ?? '-';
$totalAmount = $order['total_amount'] ?? 0;
$seatName = $payment['seat_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merish Cafe - Pesanan Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #8d616f;
            --accent-dark: #6B5B4F;
            --bg: #f4eded;
            --card-bg: #fff;
            --text: #3a2a2f;
            --text-light: #7a6a6f;
            --gold: #D4A574;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 1rem;
        }
        .success-wrap {
            max-width: 480px;
            width: 100%;
        }
        .brand {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: var(--accent);
            letter-spacing: 2px;
        }
        .brand small {
            font-size: 0.75rem;
            color: var(--text-light);
            letter-spacing: 1px;
        }
        .success-card {
            background: var(--card-bg);
            border-radius: 1.2rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .check-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .check-circle i {
            font-size: 2rem;
            color: #fff;
        }
        .success-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }
        .success-sub {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        .order-info {
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .order-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            font-size: 0.85rem;
        }
        .info-row .label {
            color: var(--text-light);
        }
        .info-row .value {
            font-weight: 600;
            color: var(--text);
        }
        .item-list {
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-bottom: 1.5rem;
        }
        .item-list h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--accent);
            margin-bottom: 0.8rem;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 0.35rem 0;
            font-size: 0.85rem;
        }
        .item-row .item-name {
            color: var(--text);
        }
        .item-row .item-sub {
            color: var(--text-light);
            font-weight: 500;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0 0;
            border-top: 2px solid var(--accent);
            margin-top: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
        }
        .total-row .value {
            color: var(--accent);
        }
        .btn-home {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            padding: 0.8rem 2.5rem;
            border-radius: 2rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: opacity 0.2s;
            margin-top: 1rem;
        }
        .btn-home:hover { opacity: 0.9; color: #fff; }
        .seat-info {
            display: inline-block;
            background: var(--bg);
            padding: 0.4rem 1.2rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="success-wrap">
        <div class="brand">
            <h1>MERISH</h1>
            <small>SALON & CAFE</small>
        </div>

        <div class="success-card">
            <div class="check-circle">
                <i class="bi bi-check-lg"></i>
            </div>
            <h2 class="success-title">Pesanan Berhasil!</h2>
            <p class="success-sub">Pesanan Anda telah diterima dan sedang diproses.</p>

            <?php if ($seatName): ?>
                <span class="seat-info"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($seatName) ?></span>
            <?php endif; ?>

            <div class="order-info">
                <h3>Detail Pesanan</h3>
                <div class="info-row">
                    <span class="label">No. Pesanan</span>
                    <span class="value">#<?= (int)($order['order_id'] ?? 0) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Pembayaran</span>
                    <span class="value"><?= htmlspecialchars($paymentMethod) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Status</span>
                    <span class="value"><?= htmlspecialchars($orderStatus) ?></span>
                </div>
            </div>

            <?php if (!empty($orderDetails)): ?>
                <div class="item-list">
                    <h3>Item Pesanan</h3>
                    <?php 
                    $detailsSubtotal = 0;
                    foreach ($orderDetails as $detail) {
                        $detailsSubtotal += (float)$detail['subtotal'];
                    }
                    $hasTax = ($totalAmount > $detailsSubtotal);
                    $calculatedTax = (int) round($detailsSubtotal * 0.1);
                    ?>
                    <?php foreach ($orderDetails as $detail): ?>
                        <div class="item-row">
                            <span class="item-name"><?= htmlspecialchars($detail['menu_name']) ?> x<?= (int)$detail['qty'] ?></span>
                            <span class="item-sub">Rp <?= number_format((float)$detail['subtotal'], 0, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($hasTax): ?>
                        <div class="item-row text-muted border-top pt-1 mt-1" style="font-size: 0.8rem;">
                            <span>Tax & Service (10%)</span>
                            <span>Rp <?= number_format($calculatedTax, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="total-row">
                        <span>Total</span>
                        <span class="value">Rp <?= number_format((float)$totalAmount, 0, ',', '.') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <a href="index.php" class="btn-home">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
