<?php
$pageTitle = 'Checkout - MERISH Cafe';
$isLoggedIn = isset($_SESSION['user_id']);
$displayName = $_SESSION['full_name'] ?? $_SESSION['user_login'] ?? 'Guest';
$cart = $_SESSION['cart'] ?? [];

$orderItems = [];
$totalPrice = 0;
foreach ($cart as $item) {
    $qty = (int) ($item['qty'] ?? 0);
    $price = (int) ($item['price'] ?? 0);
    $lineTotal = $price * $qty;
    $totalPrice += $lineTotal;
    $orderItems[] = [
        'name' => $item['menu_name'] ?? 'Menu Item',
        'qty' => $qty,
        'price' => $price,
        'line_total' => $lineTotal,
    ];
}

$tax = (int) round($totalPrice * 0.1);
$finalTotal = $totalPrice + $tax;
$serviceName = 'Merish Cafe';

// Check for seat/table scanned
$seatId = $_GET['seat'] ?? $_GET['seat_id'] ?? $_GET['table'] ?? $_SESSION['qr_order']['seat_id'] ?? $_SESSION['seat_id'] ?? null;

// If user is a logged-in customer and no seat is explicitly scanned/passed,
// automatically find their active salon reservation seat for today
if (!$seatId && $isLoggedIn) {
    try {
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.seat_id 
            FROM reservations r
            WHERE r.user_id = :user_id 
              AND r.STATUS IN ('Confirmed', 'In-Service')
            ORDER BY r.schedule_time DESC
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $row = $stmt->fetch();
        if ($row && !empty($row['seat_id'])) {
            $seatId = $row['seat_id'];
        }
    } catch (\Exception $e) {
        // Safe fallback
    }
}

$seatName = 'Pick Up';
if ($seatId) {
    try {
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT seat_name FROM seats WHERE seat_id = :id OR seat_name = :id LIMIT 1");
        $stmt->execute([':id' => $seatId]);
        $row = $stmt->fetch();
        if ($row) {
            $seatName = $row['seat_name'];
        } else {
            $seatName = $seatId;
        }
    } catch (\Exception $e) {
        $seatName = $seatId;
    }
}
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
            --bg: #fbf3f1;
            --panel: #fffdfc;
            --line: #eadfdd;
            --ink: #4c3f45;
            --muted: #7b6d72;
            --accent: #8c6674;
            --accent-dark: #75505d;
            --soft: #f7eff0;
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
            padding: 1.1rem 0 0.3rem;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            text-decoration: none;
            line-height: 1;
        }

        .back-link {
            color: #8a767d;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2.5rem, 4vw, 4.2rem);
            line-height: 1;
            margin: 0 0 0.2rem;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 1.7rem;
        }

        .page-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(280px, 0.82fr);
            gap: 2.8rem;
            align-items: start;
        }

        .method-block {
            border-left: 3px solid var(--accent);
            padding-left: 1rem;
            margin-bottom: 2rem;
        }

        .method-head {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            color: #59484f;
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            margin-bottom: 0.45rem;
        }

        .method-copy {
            color: var(--muted);
            font-size: 0.88rem;
            margin-bottom: 1rem;
        }

        .appointment-card {
            background: #f4ebeb;
            border: 1px solid #eadfe1;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .appointment-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            background: #d9aeb9;
            color: #fff;
            border-radius: 10px;
            padding: 0.8rem 0.9rem;
            min-width: 0;
        }

        .appointment-pill .icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255,255,255,0.18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .appointment-pill .texts {
            line-height: 1.15;
        }

        .appointment-pill .eyebrow {
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.65rem;
            font-weight: 700;
            opacity: 0.95;
        }

        .appointment-pill .detail {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .btn-accent {
            width: 100%;
            border: 0;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 0.9rem 1rem;
        }

        .btn-accent:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .divider-or {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.35rem 0 1.4rem;
            color: #86757a;
            text-transform: lowercase;
        }

        .divider-or::before,
        .divider-or::after {
            content: '';
            height: 1px;
            background: #eadfdf;
            flex: 1;
        }

        .guest-card {
            padding-top: 0.2rem;
        }

        .guest-card .head {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            color: #5c4a51;
            font-family: 'Playfair Display', serif;
            font-size: 1.08rem;
            margin-bottom: 0.8rem;
        }

        .field-label {
            text-transform: uppercase;
            letter-spacing: 1.3px;
            font-size: 0.65rem;
            color: #7f7075;
            margin-bottom: 0.35rem;
            font-weight: 700;
        }

        .form-control,
        .payment-choice {
            border-radius: 0;
            border: 1px solid #e3d5d7;
            background: #fff;
            font-size: 0.92rem;
        }

        .form-control:focus {
            border-color: #b28f9a;
            box-shadow: 0 0 0 0.15rem rgba(178, 143, 154, 0.12);
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.8rem;
            margin: 0.35rem 0 1rem;
        }

        .payment-choice {
            padding: 0.9rem 1rem;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.72rem;
            color: #6c5b62;
            background: #faf5f6;
            cursor: pointer;
        }

        .payment-choice.active {
            background: #efe3e6;
            border-color: #b28f9a;
        }

        .timer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            background: #fbf3f3;
            border: 1px solid #eee0e1;
            padding: 0.8rem 0.9rem;
        }

        .timer {
            font-family: 'Playfair Display', serif;
            color: #d86c6c;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .pay-now {
            border: 1px solid #d0b8bd;
            background: #fff;
            color: #7f6670;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.55rem 1rem;
        }

        .summary-card {
            background: var(--panel);
            border: 1px solid #efe4e4;
            box-shadow: 0 12px 26px rgba(69, 45, 51, 0.06);
            padding: 1.55rem 1.45rem;
            position: sticky;
            top: 1.25rem;
        }

        .summary-top {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.64rem;
            color: #8a7b7f;
            margin-bottom: 0.15rem;
        }

        .summary-title {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: #524148;
            margin-bottom: 1.3rem;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.86rem;
            color: #69585f;
            margin-bottom: 0.85rem;
        }

        .summary-divider {
            border-top: 1px solid #eee3e4;
            margin: 1rem 0;
        }

        .summary-footer {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 1rem;
            margin-top: 1rem;
        }

        .summary-footer .label {
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .summary-footer .value {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1;
        }

        .footer-spacer {
            margin-top: 4rem;
            height: 1px;
            background: #ebdfdf;
        }

        .footer {
            background: #f1e9e8;
            border-top: 1px solid #e2d6d6;
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            color: var(--accent);
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

        @media (max-width: 991.98px) {
            .page-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .summary-card {
                position: static;
            }
        }

        @media (max-width: 575.98px) {
            .payment-grid {
                grid-template-columns: 1fr;
            }

            .timer-row {
                flex-direction: column;
                align-items: stretch;
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

    <main class="container py-4 py-md-5">
        <div class="page-grid">
            <section>
                <h1 class="page-title">Checkout</h1>
                <p class="page-subtitle">Selesaikan pembayaran untuk pesanan kafe Anda.</p>

                <div class="guest-card">
                    <div class="head">☕ Rincian Pesanan Kafe</div>
                    <form method="POST" action="index.php?page=cafe&action=checkout">
                        <input type="hidden" name="order_type" value="<?php echo ($seatName !== 'Pick Up') ? 'Dine-In' : 'Takeaway'; ?>">

                        <div class="mb-3">
                            <div class="field-label">Nama Panggilan</div>
                            <input type="text" name="guest_name" class="form-control" placeholder="e.g. Jane" value="<?php echo htmlspecialchars($displayName !== 'Guest' ? $displayName : ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <div class="field-label">Meja / Table</div>
                            <input type="text" name="table_name" class="form-control" value="<?php echo htmlspecialchars($seatName); ?>" readonly style="background-color: #f1e9e8; cursor: not-allowed;">
                        </div>

                        <div class="mb-2">
                            <div class="field-label">Digital Payment</div>
                            <div class="payment-grid">
                                <label class="payment-choice active">
                                    <input type="radio" class="d-none" name="payment_method" value="QRIS" checked>
                                    QRIS
                                </label>
                                <label class="payment-choice">
                                    <input type="radio" class="d-none" name="payment_method" value="Cash">
                                    Cash
                                </label>
                            </div>
                        </div>

                        <div class="timer-row mb-3">
                            <div class="timer">⏱ 04:59</div>
                            <button type="submit" class="pay-now">Bayar Sekarang</button>
                        </div>
                    </form>
                </div>
            </section>

            <aside>
                <div class="summary-card">
                    <div class="summary-top">Order Summary</div>
                    <div class="summary-title"><?php echo htmlspecialchars($serviceName); ?></div>

                    <?php if (!empty($orderItems)): ?>
                        <?php foreach ($orderItems as $item): ?>
                            <div class="summary-line">
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                                <span>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="summary-line"><span>Iced Matcha Latte</span><span>Rp 45.000</span></div>
                        <div class="summary-line"><span>Almond Croissant</span><span>Rp 35.000</span></div>
                    <?php endif; ?>

                    <div class="summary-divider"></div>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Tax (10%)</span>
                        <span>Rp <?php echo number_format($tax, 0, ',', '.'); ?></span>
                    </div>

                    <div class="summary-footer">
                        <div class="label">Total</div>
                        <div class="value">Rp <?php echo number_format($finalTotal, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </aside>
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
<script>
document.querySelectorAll('.payment-choice').forEach((choice) => {
    choice.addEventListener('click', () => {
        document.querySelectorAll('.payment-choice').forEach((item) => item.classList.remove('active'));
        choice.classList.add('active');
        const radio = choice.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    });
});
</script>
</body>
</html>
