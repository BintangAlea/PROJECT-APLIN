<?php
$pageTitle = 'Order Status - MERISH Cafe';
$order = $_SESSION['cafe_order'] ?? [];
$items = $order['items'] ?? [];
$guestName = $order['guest_name'] ?? ($_SESSION['full_name'] ?? 'Kak');
$orderType = $order['order_type'] ?? 'Dine-In';
$orderNumber = '#MRSH-' . strtoupper(substr(md5((string) microtime(true)), 0, 3)) . rand(10, 99);
$createdAt = date('h:i A');
$tableName = $order['table_name'] ?? 'Pick Up';
$deliveryText = ($tableName !== 'Pick Up') ? 'Pesanan akan diantar ke ' . $tableName . '.' : 'Pesanan siap diambil sendiri.';

$summaryItems = [];
$totalPrice = 0;
foreach ($items as $item) {
    $qty = (int) ($item['qty'] ?? 1);
    $price = (int) ($item['price'] ?? 0);
    $name = $item['menu_name'] ?? 'Menu Item';
    $summaryItems[] = [
        'name' => $name,
        'qty' => $qty,
        'price' => $price,
    ];
    $totalPrice += $price * $qty;
}

$tax = (int) round($totalPrice * 0.1);
$finalTotal = $totalPrice + $tax;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #faf1ef;
            --panel: #fffdfc;
            --line: #eadfdd;
            --ink: #594850;
            --muted: #8a7b80;
            --accent: #8c6674;
            --accent-dark: #774e5a;
            --soft: #f4e9e8;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .page-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            padding: 1.15rem 0 0.3rem;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            color: var(--accent);
            text-decoration: none;
            line-height: 1;
        }

        .back-link {
            color: #8d7b80;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .center-wrap {
            max-width: 920px;
            margin: 0 auto;
            padding: 2.25rem 0 3rem;
            text-align: center;
        }

        .title {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2.8rem, 5vw, 4.3rem);
            line-height: 1;
            margin-bottom: 0.8rem;
        }

        .subtitle {
            color: var(--muted);
            max-width: 320px;
            margin: 0 auto 2.8rem;
            font-size: 0.9rem;
        }

        .steps {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .step {
            min-width: 84px;
            text-align: center;
            position: relative;
        }

        .step-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            margin: 0 auto 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            background: #ede3e4;
            color: #ccb9bc;
            border: 1px solid #e4d8da;
        }

        .step.active .step-icon {
            background: #8c6674;
            color: #fff;
            border-color: #8c6674;
            box-shadow: 0 7px 16px rgba(140, 102, 116, 0.18);
        }

        .step.completed .step-icon {
            background: #8c6674;
            color: #fff;
            border-color: #8c6674;
        }

        .step-line {
            position: absolute;
            top: 16px;
            left: calc(100% + 1rem);
            width: 2rem;
            height: 1px;
            background: #d7c8ca;
        }

        .step:last-child .step-line {
            display: none;
        }

        .step-label {
            color: #8a7a80;
            font-size: 0.64rem;
            font-weight: 700;
            line-height: 1.15;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .order-card {
            width: min(100%, 270px);
            margin: 0 auto 3rem;
            background: var(--panel);
            border: 1px solid #efe2e0;
            box-shadow: 0 16px 28px rgba(82, 53, 62, 0.05);
            padding: 1.1rem 1.05rem 1rem;
            position: relative;
        }

        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 42px;
            height: 4px;
            border-radius: 999px;
            background: #e1d2d4;
        }

        .order-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.8rem;
        }

        .eyebrow {
            color: #b39aa0;
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            margin-bottom: 0.12rem;
        }

        .order-number {
            font-family: 'Playfair Display', serif;
            color: #7e5c68;
            font-size: 1.15rem;
            line-height: 1;
        }

        .time-badge {
            font-size: 0.56rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #86747a;
            background: #f3e9e8;
            border: 1px solid #eadfe1;
            padding: 0.2rem 0.45rem;
        }

        .mini-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            font-size: 0.78rem;
            color: #68575d;
            padding: 0.35rem 0;
        }

        .mini-divider {
            border-top: 1px dashed #eadedf;
            margin: 0.7rem 0;
        }

        .delivery-box {
            border: 1px solid #f0e4e3;
            background: #faf3f3;
            padding: 0.95rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .delivery-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #ead6d9;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8c6674;
            flex: 0 0 auto;
        }

        .delivery-title {
            color: #b39aa0;
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            margin-bottom: 0.15rem;
        }

        .delivery-text {
            color: #7e5c68;
            font-size: 0.82rem;
            line-height: 1.4;
        }

        .status-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--accent);
            color: #fff;
            text-decoration: none;
            border: 0;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 0.85rem 1.25rem;
            min-width: 132px;
            box-shadow: 0 8px 18px rgba(140, 102, 116, 0.16);
        }

        .status-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .footer-spacer {
            margin-top: auto;
            height: 1px;
            background: #ebdfdf;
        }

        .footer {
            background: #efe5e4;
            border-top: 1px solid #e1d4d4;
            padding: 2rem 0;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: 2.8rem;
            text-decoration: none;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }

        .footer-links a,
        .footer-copy {
            color: #74686d;
            font-size: 0.84rem;
            text-decoration: none;
        }

        @media (max-width: 575.98px) {
            .steps {
                gap: 1rem;
            }

            .step-line {
                width: 1.35rem;
                left: calc(100% + 0.45rem);
            }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <div class="container topbar">
        <div class="d-flex justify-content-between align-items-center">
            <a href="index.php?page=home" class="brand">Merish</a>
            <a href="index.php?page=cafe&action=cart" class="back-link">← Back</a>
        </div>
    </div>

    <main class="container">
        <div class="center-wrap">
            <h1 class="title">Status Pesanan</h1>
            <p class="subtitle">Kami sedang menyiapkan pesanan Anda dengan penuh perhatian. Nikmati waktu luang Anda.</p>

            <div class="steps">
                <div class="step completed">
                    <div class="step-icon">✓</div>
                    <div class="step-line"></div>
                    <div class="step-label">Pesanan Diterima</div>
                </div>
                <div class="step active">
                    <div class="step-icon">□</div>
                    <div class="step-line"></div>
                    <div class="step-label">Sedang Dibuat</div>
                </div>
                <div class="step">
                    <div class="step-icon">⌂</div>
                    <div class="step-label">Siap Diantar</div>
                </div>
            </div>

            <div class="order-card">
                <div class="order-meta">
                    <div>
                        <div class="eyebrow">Nomor Pesanan</div>
                        <div class="order-number"><?php echo htmlspecialchars($orderNumber); ?></div>
                    </div>
                    <div class="time-badge"><?php echo htmlspecialchars($createdAt); ?></div>
                </div>

                <?php if (!empty($summaryItems)): ?>
                    <?php foreach ($summaryItems as $item): ?>
                        <div class="mini-item">
                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                            <span>x<?php echo (int) $item['qty']; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="mini-item">
                        <span>Signature Matcha Latte</span>
                        <span>x1</span>
                    </div>
                    <div class="mini-item">
                        <span>Almond Croissant</span>
                        <span>x1</span>
                    </div>
                <?php endif; ?>

                <div class="mini-divider"></div>

                <div class="delivery-box">
                    <div class="delivery-icon">☕</div>
                    <div>
                        <div class="delivery-title">Status Pembuatan</div>
                        <div class="delivery-text">Barista is still making your drinks. Please wait...</div>
                    </div>
                </div>
            </div>

            <a href="index.php?page=cafe" class="status-btn">Kembali ke Menu</a>
        </div>
    </main>

    <div class="footer-spacer"></div>
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-lg-2">
                    <a class="footer-brand" href="index.php?page=home">Merish</a>
                </div>
                <div class="col-lg-5">
                    <nav class="footer-links">
                        <a href="#">Contact</a>
                        <a href="#">Location</a>
                        <a href="#">Instagram</a>
                        <a href="#">Pinterest</a>
                    </nav>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <span class="footer-copy">© 2024 Merish Beauty & Cafe. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
