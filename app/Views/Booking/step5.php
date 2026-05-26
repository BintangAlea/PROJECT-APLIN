<?php
$booking = $booking ?? [];
$details = $details ?? [];
$pricing = $pricing ?? [
    'base_price' => 0,
    'addons_price' => 0,
    'promo_discount' => 0,
    'total_price' => 0,
];

$serviceName = $details['bundle']['bundle_name'] ?? ($details['service']['service_name'] ?? 'Booking Service');
$serviceItems = $details['bundle']['services'] ?? [];
$addons = $details['addons'] ?? [];
$beauticianName = $details['beautician']['name'] ?? 'Any Available Staff';
$beauticianRole = $details['beautician']['specialization'] ?? 'Assigned stylist';
$reservationDate = !empty($details['date']) ? date('l, d M Y', strtotime($details['date'])) : 'Select your date';
$reservationTime = $details['time'] ?? 'Select your time';
$subtotal = (int) ($pricing['base_price'] ?? 0) + (int) ($pricing['addons_price'] ?? 0);
$discount = (int) ($pricing['promo_discount'] ?? 0);
$total = (int) ($pricing['total_price'] ?? 0);
$requiredDp = max(0, (int) ceil($total * 0.5));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review & Payment - Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f4eded;
            --surface: #f8f2f2;
            --card: #fffafa;
            --line: #d9ccd0;
            --ink: #4f4248;
            --muted: #7a6e73;
            --accent: #8d616f;
            --accent-dark: #724e5a;
            --accent-soft: #ead2db;
        }

        html, body {
            min-height: 100%;
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .topbar {
            min-height: 68px;
            background: rgba(255, 249, 249, 0.94);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(7px);
        }

        .brand {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            text-decoration: none;
            letter-spacing: 2px;
            font-size: 1.9rem;
            font-weight: 600;
        }

        .nav-link-soft {
            color: #6f6268;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1.35px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .nav-link-soft:hover {
            color: var(--accent);
        }

        .signin-btn {
            border: 1px solid #b79fa6;
            border-radius: 0;
            color: #6d5660;
            background: #fff;
            text-transform: uppercase;
            letter-spacing: 1.35px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.48rem 0.8rem;
            line-height: 1;
        }

        .signin-btn:hover {
            color: #fff;
            background: var(--accent);
            border-color: var(--accent);
        }

        .checkout-shell {
            min-height: calc(100vh - 68px);
        }

        .sidebar {
            min-height: calc(100vh - 68px);
            background: var(--surface);
            border-right: 1px solid var(--line);
            padding: 2rem 1.05rem;
        }

        .step-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 0.6rem 0.7rem;
            color: #6e6166;
            text-decoration: none;
            font-size: 0.86rem;
        }

        .step-item.active {
            background: #f0d4e2;
            color: #794f61;
            font-weight: 700;
        }

        .step-item:hover {
            background: #efe5e8;
            color: #6f4f5c;
        }

        .step-item.disabled {
            opacity: 0.55;
            pointer-events: none;
        }

        .main-panel {
            padding: 2rem 1.4rem 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0));
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            color: #46373f;
            font-size: clamp(2.2rem, 4vw, 4.1rem);
            line-height: 1;
            margin-bottom: 0.4rem;
        }

        .page-subtitle {
            color: #7a6e73;
            margin-bottom: 1.4rem;
            max-width: 720px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.02fr 1.15fr;
            gap: 1.8rem;
            align-items: start;
        }

        .panel-card {
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 12px 30px rgba(73, 53, 60, 0.06);
        }

        .receipt-card {
            padding: 1rem 1.1rem 1.05rem;
            border-top: 4px solid #d0a0b1;
        }

        .receipt-header {
            text-align: center;
            font-size: 0.72rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #7b6a70;
            margin-bottom: 1rem;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #5d4d53;
        }

        .receipt-item .left {
            flex: 1;
        }

        .receipt-item .title {
            font-size: 0.98rem;
            margin-bottom: 0.2rem;
        }

        .receipt-item .desc {
            color: #82747a;
            font-size: 0.82rem;
            line-height: 1.45;
        }

        .receipt-item .amount {
            white-space: nowrap;
            font-size: 0.95rem;
        }

        .receipt-divider {
            border-top: 1px dashed #cbb9bf;
            margin: 0.9rem 0;
        }

        .receipt-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding-top: 0.2rem;
            margin-bottom: 0.9rem;
        }

        .receipt-total .label {
            font-size: 0.92rem;
            color: #5d4d53;
        }

        .receipt-total .value {
            font-size: 1.02rem;
            color: #7a5361;
            font-weight: 600;
        }

        .dp-box {
            border: 1px solid #e0d2d6;
            background: #fbf5f6;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            padding: 0.8rem 0.9rem;
        }

        .dp-box .small {
            color: #85787d;
            font-size: 0.76rem;
        }

        .dp-box .big {
            font-family: 'Playfair Display', serif;
            color: #7d5160;
            font-size: 1.6rem;
            line-height: 1;
        }

        .scan-card {
            padding: 1rem 1.1rem 1.1rem;
            min-height: 100%;
        }

        .scan-title {
            text-align: center;
            font-family: 'Playfair Display', serif;
            color: #43353d;
            font-size: clamp(1.8rem, 2.8vw, 2.5rem);
            margin-bottom: 0.4rem;
        }

        .scan-copy {
            text-align: center;
            color: #7a6e73;
            font-size: 0.92rem;
            max-width: 420px;
            margin: 0 auto 1rem;
        }

        .phone-frame {
            width: 170px;
            height: 235px;
            margin: 0 auto 1rem;
            border-radius: 20px;
            background: linear-gradient(145deg, #f1d2c2 0%, #e7b79d 100%);
            box-shadow: 0 22px 30px rgba(85, 62, 69, 0.22);
            position: relative;
            border: 8px solid #3e2e31;
            transform: rotate(7deg);
        }

        .phone-camera {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 46px;
            height: 5px;
            border-radius: 999px;
            background: rgba(255,255,255,0.48);
        }

        .qr-art {
            position: absolute;
            inset: 33px 22px 42px;
            border-radius: 12px;
            background:
                linear-gradient(90deg, #101010 0 12px, transparent 12px 24px),
                linear-gradient(180deg, #101010 0 12px, transparent 12px 24px),
                repeating-linear-gradient(0deg, #101010 0 6px, transparent 6px 12px),
                repeating-linear-gradient(90deg, #101010 0 6px, transparent 6px 12px),
                #fff;
            background-size: 24px 24px, 24px 24px, 100% 100%, 100% 100%, cover;
            background-position: left top, left top, center, center, center;
            border: 10px solid rgba(255,255,255,0.42);
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
        }

        .merchant-name {
            text-align: center;
            font-size: 0.86rem;
            color: #7b6e73;
            margin-bottom: 0.1rem;
        }

        .merchant-id {
            text-align: center;
            font-size: 0.77rem;
            color: #918488;
        }

        .payment-options {
            margin-top: 1.2rem;
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .proof-toggle {
            display: none;
        }

        .proof-button {
            border: 1px solid #b9a3aa;
            background: #fff;
            color: #7b5964;
            text-transform: uppercase;
            letter-spacing: 1.25px;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 0.9rem 1rem;
            border-radius: 0;
            text-align: center;
            cursor: pointer;
        }

        .proof-button:hover {
            background: #f6ecef;
        }

        .chosen-file {
            font-size: 0.84rem;
            color: #7f7277;
        }

        .agreement {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            color: #6f6267;
            font-size: 0.86rem;
            line-height: 1.5;
        }

        .agreement input {
            margin-top: 0.2rem;
        }

        .agreement a {
            color: #7b5866;
        }

        .action-row {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 0.85rem;
            margin-top: 1rem;
        }

        .back-btn,
        .confirm-btn {
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 0.95rem 1rem;
        }

        .back-btn {
            border: 1px solid #b9aab0;
            color: #795c66;
            background: #fff;
        }

        .confirm-btn {
            border: 1px solid var(--accent-dark);
            background: var(--accent);
            color: #fff;
        }

        .confirm-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .confirm-btn:disabled {
            background: #b79ea6;
            border-color: #b79ea6;
            opacity: 0.7;
        }

        .booking-footer {
            margin-top: 2rem;
            background: #e7e1e0;
            border-top: 1px solid #d4c9cc;
            padding: 1.7rem 1rem 2rem;
            color: #7a6e73;
        }

        .footer-brand {
            text-align: center;
            font-family: 'Playfair Display', serif;
            color: #74545f;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            flex-wrap: wrap;
            margin-bottom: 1.1rem;
            font-size: 0.8rem;
        }

        .footer-links a {
            color: #7a6e73;
        }

        .footer-copy {
            text-align: center;
            font-size: 0.8rem;
        }

        .alert {
            border-radius: 0;
            font-size: 0.9rem;
        }

        .alert-danger {
            background: #fbeaec;
            border-color: #efc9d1;
            color: #7c3547;
        }

        @media (max-width: 1199.98px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid var(--line);
            }
        }

        @media (max-width: 575.98px) {
            .main-panel {
                padding: 1.2rem 0.85rem 0;
            }

            .action-row {
                grid-template-columns: 1fr;
            }

            .phone-frame {
                transform: none;
            }
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="container-fluid px-4 h-100 d-flex align-items-center justify-content-between">
        <a class="brand" href="index.php?page=home">MERISH</a>
    </div>
</header>

<div class="checkout-shell">
    <div class="container-fluid">
        <div class="row g-0">
            <aside class="col-lg-3 col-xl-2 sidebar">
                <ul class="step-nav">
                    <li><a class="step-item" href="index.php?page=booking&step=1">▵ <span>Category</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=2">✧ <span>Bundle</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=3">◷ <span>Schedule</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=4">◉ <span>Stylist</span></a></li>
                    <li><a class="step-item active" href="index.php?page=booking&step=5">▣ <span>Checkout</span></a></li>
                    <li><span class="step-item disabled">◌ <span>Confirm</span></span></li>
                </ul>
            </aside>

            <main class="col-lg-9 col-xl-10 main-panel">
                <h1 class="page-title">Review & Payment</h1>
                <p class="page-subtitle">Please review your booking details and secure your appointment with a down payment.</p>

                <?php if (isset($_SESSION['booking_error']) && $_SESSION['booking_error']): ?>
                    <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($_SESSION['booking_error']); unset($_SESSION['booking_error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="/index.php?page=booking&action=submit" enctype="multipart/form-data" id="checkoutForm">
                    <input type="hidden" name="payment_method" value="qris">
                    <input type="hidden" name="promo_code" value="">

                    <div class="checkout-grid">
                        <section class="panel-card receipt-card">
                            <div class="receipt-header">MERISH STUDIO RECEIPT</div>

                            <div class="receipt-item">
                                <div class="left">
                                    <div class="title"><?php echo htmlspecialchars($serviceName); ?></div>
                                    <div class="desc"><?php echo htmlspecialchars($beauticianRole); ?> - <?php echo htmlspecialchars($beauticianName); ?></div>
                                </div>
                                <div class="amount">Rp<?php echo number_format((int) ($pricing['base_price'] ?? 0), 0, ',', '.'); ?></div>
                            </div>

                            <?php if (!empty($serviceItems)): ?>
                                <?php foreach ($serviceItems as $service): ?>
                                    <div class="receipt-item">
                                        <div class="left">
                                            <div class="title"><?php echo htmlspecialchars($service['service_name'] ?? 'Service'); ?></div>
                                            <div class="desc">Included in selected bundle</div>
                                        </div>
                                        <div class="amount">Rp<?php echo number_format((int) ($service['price'] ?? 0), 0, ',', '.'); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($addons)): ?>
                                <?php foreach ($addons as $addon): ?>
                                    <div class="receipt-item">
                                        <div class="left">
                                            <div class="title"><?php echo htmlspecialchars($addon['addon_name'] ?? 'Add-on'); ?></div>
                                            <div class="desc">Bundle Savings</div>
                                        </div>
                                        <div class="amount">Rp<?php echo number_format((int) ($addon['price'] ?? 0), 0, ',', '.'); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if ($discount > 0): ?>
                                <div class="receipt-item">
                                    <div class="left">
                                        <div class="title">VIP Discount</div>
                                        <div class="desc">Applied automatically</div>
                                    </div>
                                    <div class="amount">-Rp<?php echo number_format($discount, 0, ',', '.'); ?></div>
                                </div>
                            <?php endif; ?>

                            <div class="receipt-divider"></div>

                            <div class="receipt-total">
                                <div class="label">Subtotal</div>
                                <div class="value">Rp<?php echo number_format($subtotal, 0, ',', '.'); ?></div>
                            </div>

                            <div class="dp-box">
                                <div>
                                    <div class="small fw-semibold text-uppercase">Required DP</div>
                                    <div class="small">50% to secure booking</div>
                                </div>
                                <div class="big">Rp<?php echo number_format($requiredDp, 0, ',', '.'); ?></div>
                            </div>

                            <div class="mt-4">
                                <div class="small text-uppercase fw-semibold" style="letter-spacing:1.2px;color:#7b6a70;">Appointment Details</div>
                                <div class="receipt-item mt-2 mb-2">
                                    <div class="left">
                                        <div class="title"><?php echo htmlspecialchars($reservationDate); ?></div>
                                        <div class="desc">Schedule date</div>
                                    </div>
                                    <div class="amount"><?php echo htmlspecialchars($reservationTime); ?></div>
                                </div>
                            </div>
                        </section>

                        <section class="panel-card scan-card">
                            <h2 class="scan-title">Scan to Pay</h2>
                            <p class="scan-copy">Please scan the QRIS code below using your preferred banking or e-wallet app to transfer the down payment.</p>

                            <div class="phone-frame">
                                <div class="phone-camera"></div>
                                <div class="qr-art"></div>
                            </div>

                            <div class="merchant-name">Merish Beauty Studio</div>
                            <div class="merchant-id">ID: 0938472948</div>

                            <div class="payment-options">
                                <label class="agreement" for="payment_proof_agree">
                                    <input class="form-check-input" type="checkbox" id="payment_proof_agree" required>
                                    <span>I agree to the <a href="#">Cancellation Policy</a> and confirm that the details provided are correct.</span>
                                </label>

                                <input type="file" id="payment_proof" name="payment_proof" class="proof-toggle" accept="image/*,application/pdf">
                                <label for="payment_proof" class="proof-button">Upload Bukti Bayar</label>
                                <div id="chosenFile" class="chosen-file">No file chosen</div>

                                <div class="action-row">
                                    <a href="index.php?page=booking&step=4" class="btn back-btn">Back</a>
                                    <button type="submit" class="btn confirm-btn" id="confirmBtn">Confirm Appointment</button>
                                </div>
                            </div>
                        </section>
                    </div>
                </form>

                <footer class="booking-footer">
                    <div class="footer-brand">Merish</div>
                    <div class="footer-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Contact Us</a>
                        <a href="#">Location</a>
                    </div>
                    <div class="footer-copy">© 2024 Merish Beauty Studio. All rights reserved.</div>
                </footer>
            </main>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const paymentProof = document.getElementById('payment_proof');
    const chosenFile = document.getElementById('chosenFile');
    const confirmBtn = document.getElementById('confirmBtn');
    const agreement = document.getElementById('payment_proof_agree');

    paymentProof.addEventListener('change', function () {
        chosenFile.textContent = this.files && this.files.length ? this.files[0].name : 'No file chosen';
    });

    agreement.addEventListener('change', function () {
        confirmBtn.disabled = !this.checked;
    });

    confirmBtn.disabled = true;
</script>
</body>
</html>
