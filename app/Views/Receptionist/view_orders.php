<?php
$activeBills = $activeBills ?? [];
$selectedBill = $selectedBill ?? null;
$flashSuccess = $flashSuccess ?? null;
$flashError = $flashError ?? null;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatCurrency = static fn ($value): string => 'Rp ' . number_format((float) $value, 0, ',', '.');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier / Unified POS - Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f8f2f3;
            --panel: #ffffff;
            --line: #e7dadc;
            --ink: #4e3e45;
            --muted: #8b7c82;
            --accent: #8b6472;
            --accent-dark: #744d5b;
            --green: #2c9b63;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 220px 1fr;
        }

        .sidebar {
            background: #f3ecee;
            border-right: 1px solid var(--line);
            padding: 1.2rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: 2.2rem;
            line-height: 1;
            text-decoration: none;
        }

        .brand-sub {
            font-size: 0.8rem;
            color: #75656b;
        }

        .side-nav .nav-link {
            color: #5f5157;
            border-radius: 0;
            padding: 0.8rem 0.9rem;
            font-size: 0.92rem;
        }

        .side-nav .nav-link.active,
        .side-nav .nav-link:hover {
            background: #e9cfe7;
            color: #533d48;
            border-right: 2px solid #915d77;
        }

        .new-booking {
            margin-top: auto;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.72rem;
            font-weight: 700;
            border: 0;
            padding: 0.85rem 1rem;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
        }

        .main { padding: 0; }

        .topbar {
            background: #f7f0f1;
            border-bottom: 1px solid var(--line);
            padding: 0.75rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .search-box {
            width: min(420px, 100%);
            background: #fff;
            border: 1px solid #dfd0d4;
            display: flex;
            align-items: center;
            padding: 0.55rem 0.75rem;
            color: #96878d;
        }

        .search-box input {
            border: 0;
            outline: none;
            background: transparent;
            width: 100%;
            margin-left: 0.45rem;
            font-size: 0.9rem;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border: 1px solid #ddced2;
            background: #fff;
            color: #7d6d73;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .walkin-btn {
            border: 1px solid #c9b0b9;
            background: #fff;
            color: #6e5560;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.55rem 0.85rem;
            text-decoration: none;
        }

        .content {
            padding: 1.2rem;
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
        }

        .panel {
            background: #fff;
            border: 1px solid #e2d6d9;
        }

        .left-panel-head {
            padding: 1rem;
            border-bottom: 1px solid #efe4e7;
        }

        .left-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #352a2f;
            margin: 0;
        }

        .left-sub {
            color: #8b7c82;
            margin-top: 0.2rem;
            font-size: 0.9rem;
        }

        .bill-list {
            padding: 0.8rem;
            display: grid;
            gap: 0.7rem;
        }

        .bill-card {
            border: 1px solid #e1d4d8;
            background: #fff;
            padding: 0.75rem;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .bill-card.active {
            border-color: #b47b90;
            background: #f7ecef;
        }

        .bill-card:hover { color: inherit; }

        .bill-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #e8dde1;
            color: #6f5d64;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .pill {
            border: 1px solid #d1b4bf;
            background: #e7ced8;
            color: #6e4f5e;
            font-size: 0.64rem;
            font-weight: 700;
            padding: 0.18rem 0.45rem;
            text-transform: uppercase;
            letter-spacing: 0.9px;
        }

        .invoice {
            max-width: 620px;
            margin: 0 auto;
        }

        .invoice-head {
            padding: 1.1rem 1.2rem;
            border-top: 5px solid #d9a8ba;
            border-bottom: 1px solid #ece1e4;
        }

        .invoice-title {
            font-family: 'Playfair Display', serif;
            color: #6c4657;
            font-size: 2.5rem;
            margin: 0;
            text-align: center;
        }

        .invoice-sub {
            text-align: center;
            color: #7f6e74;
            font-size: 0.86rem;
            margin-bottom: 0.8rem;
        }

        .invoice-meta {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.8rem;
        }

        .section {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #eee3e6;
        }

        .section-title {
            text-transform: uppercase;
            letter-spacing: 1.3px;
            font-size: 0.75rem;
            color: #6f5d64;
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        .line-item {
            display: flex;
            justify-content: space-between;
            gap: 0.8rem;
            margin-bottom: 0.6rem;
        }

        .line-item:last-child { margin-bottom: 0; }

        .summary {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #eee3e6;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 0.7rem;
            margin-bottom: 0.5rem;
            color: #6e5d64;
            font-size: 0.9rem;
        }

        .summary-row.discount {
            color: var(--green);
            font-weight: 600;
        }

        .total-due {
            padding: 1rem 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 0.8rem;
            border-bottom: 1px solid #eee3e6;
        }

        .total-value {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: #2d2227;
            line-height: 1;
        }

        .pay-form {
            padding: 1rem 1.2rem 1.2rem;
        }

        .form-label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.7rem;
            color: #806f75;
            font-weight: 700;
        }

        .form-select {
            border-radius: 0;
            border: 1px solid #e3d5d7;
        }

        .pay-btn {
            margin-top: 0.8rem;
            width: 100%;
            border: 0;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 0.85rem 1rem;
        }

        .pay-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        @media (max-width: 1120px) {
            .shell { grid-template-columns: 1fr; }
            .content { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div>
                <a class="brand" href="index.php?page=receptionist">Merish Portal</a>
                <div class="brand-sub">Reception Management</div>
            </div>

            <nav class="nav flex-column side-nav gap-1">
                <a class="nav-link" href="index.php?page=receptionist">Seat Map</a>
                <a class="nav-link" href="index.php?page=receptionist&action=viewReservations">Appointments</a>
                <a class="nav-link active" href="index.php?page=receptionist&action=viewOrders">Cashier</a>
            </nav>

            <a class="new-booking" href="index.php?page=receptionist&action=scheduleBooking">+ New Booking</a>
        </aside>

        <main class="main">
            <header class="topbar">
                <div class="search-box rounded-0">
                    <span>⌕</span>
                    <input type="text" placeholder="Search orders, clients...">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="icon-btn" type="button">🔔</button>
                    <button class="icon-btn" type="button">?</button>
                    <a class="walkin-btn" href="index.php?page=receptionist">Walk-In Check-In</a>
                    <a class="icon-btn text-decoration-none" href="index.php?page=login&action=logout">⎋</a>
                </div>
            </header>

            <div class="content">
                <section class="panel">
                    <div class="left-panel-head">
                        <h2 class="left-title">Ready for Checkout</h2>
                        <div class="left-sub"><?php echo count($activeBills); ?> clients pending</div>
                    </div>

                    <?php if ($flashSuccess): ?>
                        <div class="alert alert-success border-0 rounded-0 m-0"><?php echo $escape($flashSuccess); ?></div>
                    <?php endif; ?>
                    <?php if ($flashError): ?>
                        <div class="alert alert-danger border-0 rounded-0 m-0"><?php echo $escape($flashError); ?></div>
                    <?php endif; ?>

                    <div class="bill-list">
                        <?php if (empty($activeBills)): ?>
                            <div class="text-muted small">Belum ada tagihan aktif yang siap checkout.</div>
                        <?php else: ?>
                            <?php foreach ($activeBills as $bill): ?>
                                <?php
                                $isActive = $selectedBill && (int) $selectedBill['reservation']['res_id'] === (int) $bill['res_id'];
                                $initial = strtoupper(substr((string) $bill['customer_name'], 0, 1));
                                ?>
                                <a class="bill-card <?php echo $isActive ? 'active' : ''; ?>" href="index.php?page=receptionist&action=viewOrders&res_id=<?php echo urlencode((string) $bill['res_id']); ?>">
                                    <div class="bill-top">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar"><?php echo $escape($initial); ?></span>
                                            <div>
                                                <div class="fw-semibold"><?php echo $escape($bill['customer_name']); ?></div>
                                                <div class="small text-muted">Order #<?php echo $escape((string) $bill['res_id']); ?> · <?php echo $escape(date('g:i A', strtotime((string) $bill['schedule_time']))); ?></div>
                                            </div>
                                        </div>
                                        <?php if ($isActive): ?><span class="pill">Active</span><?php endif; ?>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between small">
                                        <span><?php echo $escape($bill['label']); ?></span>
                                        <strong><?php echo $formatCurrency($bill['subtotal']); ?></strong>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <section>
                    <?php if (!$selectedBill): ?>
                        <div class="panel p-4 text-muted">Pilih salah satu tagihan aktif untuk melihat Unified Bill.</div>
                    <?php else: ?>
                        <article class="panel invoice">
                            <div class="invoice-head">
                                <h3 class="invoice-title">Merish Salon</h3>
                                <div class="invoice-sub">Unified Invoice</div>

                                <div class="invoice-meta">
                                    <div>
                                        <div class="small text-muted">Client</div>
                                        <div class="h3 mb-0" style="font-family: 'Playfair Display', serif;"><?php echo $escape($selectedBill['reservation']['customer_name']); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted">Invoice No.</div>
                                        <div class="fw-semibold">#INV-<?php echo $escape((string) $selectedBill['reservation']['res_id']); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <div class="section-title">Salon Charges</div>
                                <?php if (empty($selectedBill['salon_items'])): ?>
                                    <div class="text-muted small">No salon charges.</div>
                                <?php else: ?>
                                    <?php foreach ($selectedBill['salon_items'] as $item): ?>
                                        <div class="line-item">
                                            <div>
                                                <div class="fw-semibold"><?php echo $escape($item['service_name']); ?></div>
                                                <div class="small text-muted">Stylist: <?php echo $escape($item['stylist_name']); ?></div>
                                            </div>
                                            <div class="fw-semibold"><?php echo $formatCurrency($item['base_tariff']); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="section">
                                <div class="section-title">Cafe Charges</div>
                                <?php if (empty($selectedBill['cafe_items'])): ?>
                                    <div class="text-muted small">No cafe charges.</div>
                                <?php else: ?>
                                    <?php foreach ($selectedBill['cafe_items'] as $item): ?>
                                        <div class="line-item">
                                            <div>
                                                <div class="fw-semibold"><?php echo $escape($item['qty'] . 'x ' . $item['menu_name']); ?></div>
                                                <div class="small text-muted">Served at styling station</div>
                                            </div>
                                            <div class="fw-semibold"><?php echo $formatCurrency($item['line_total']); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="summary">
                                <div class="summary-row"><span>Subtotal</span><span><?php echo $formatCurrency($selectedBill['summary']['subtotal']); ?></span></div>
                                <div class="summary-row discount"><span>Synergy Discount Applied (-20% on Cafe)</span><span>- <?php echo $formatCurrency($selectedBill['summary']['synergy_discount']); ?></span></div>
                                <div class="summary-row"><span>Tax (11%)</span><span><?php echo $formatCurrency($selectedBill['summary']['tax']); ?></span></div>
                            </div>

                            <div class="total-due">
                                <div class="small text-uppercase fw-semibold text-muted">Total Due</div>
                                <div class="total-value"><?php echo $formatCurrency($selectedBill['summary']['total_due']); ?></div>
                            </div>

                            <div class="pay-form">
                                <form method="post" action="index.php?page=receptionist&action=processPayment">
                                    <input type="hidden" name="res_id" value="<?php echo $escape((string) $selectedBill['reservation']['res_id']); ?>">

                                    <label class="form-label">Metode Pembayaran</label>
                                    <select class="form-select" name="payment_method">
                                        <option value="Cash">Cash</option>
                                        <option value="QRIS" selected>QRIS</option>
                                        <option value="Debit">Debit</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>

                                    <button type="submit" class="pay-btn">Terima Pembayaran (Process Payment)</button>
                                </form>
                            </div>
                        </article>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>
</body>
</html>


