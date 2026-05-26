<?php
$pageTitle = 'Admin Overview - Merish';
$displayName = $_SESSION['full_name'] ?? 'Admin';
$formatCurrency = static function ($value): string {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};
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
            --bg: #fbf1ef;
            --panel: #ffffff;
            --line: #eadfdc;
            --ink: #513f46;
            --muted: #88787d;
            --accent: #8b6472;
            --accent-dark: #764f5d;
            --soft: #f6ecea;
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
            grid-template-columns: 250px minmax(0, 1fr);
        }

        .sidebar {
            background: #f8efed;
            border-right: 1px solid #eadfdc;
            padding: 1.4rem 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: 3rem;
            line-height: 1;
            text-decoration: none;
        }

        .brand-sub {
            color: #7e6d72;
            font-size: 0.84rem;
            letter-spacing: 0.6px;
        }

        .new-booking {
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.72rem;
            font-weight: 700;
            border: 0;
            padding: 0.85rem 1rem;
        }

        .new-booking:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .side-nav .nav-link {
            color: #5c4d53;
            padding: 0.8rem 0.9rem;
            border-radius: 0;
            font-size: 0.92rem;
        }

        .side-nav .nav-link.active,
        .side-nav .nav-link:hover {
            background: #f3e2e7;
            color: #5d3f4d;
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid #e8dddd;
            padding-top: 1rem;
        }

        .main {
            padding: 1.2rem 1.2rem 1.5rem;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.2rem;
        }

        .search-box {
            width: min(380px, 100%);
            background: #f7efef;
            border: 1px solid #eee3e1;
            display: flex;
            align-items: center;
            padding: 0.6rem 0.8rem;
            color: #9a888e;
        }

        .search-box input {
            border: 0;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
            color: #5b4d53;
            margin-left: 0.6rem;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3vw, 2.8rem);
            color: var(--accent);
            margin: 0;
        }

        .icon-btn {
            width: 38px;
            height: 38px;
            border: 1px solid #e4d7d8;
            background: #fff;
            color: #836872;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card {
            background: var(--panel);
            border: 1px solid #efe3e1;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.05);
            padding: 1.35rem 1.35rem 1.15rem;
            height: 100%;
        }

        .stat-label {
            text-transform: uppercase;
            letter-spacing: 1.3px;
            font-size: 0.72rem;
            color: #8e7b80;
            margin-bottom: 0.55rem;
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2rem, 3vw, 3.2rem);
            line-height: 1.05;
        }

        .stat-note {
            margin-top: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #f8ecef;
            color: #8f707d;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            font-size: 0.72rem;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin: 2rem 0 1rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            color: #3e3136;
            font-size: 1.55rem;
            margin: 0;
        }

        .queue-table,
        .inventory-panel {
            background: var(--panel);
            border: 1px solid #efe4e2;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.04);
        }

        .queue-table thead th {
            border-bottom: 1px solid #eee3e1 !important;
            color: #7f6f74;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #fff;
        }

        .queue-table td {
            color: #66575d;
            border-color: #f2e7e6;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .badge-soft {
            background: #efe1ec;
            color: #7a5d74;
            border: 1px solid #e0cbd6;
            border-radius: 0;
            padding: 0.35rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-waiting {
            background: #e8e1de;
            color: #6e6462;
            border-color: #dbd1cc;
        }

        .badge-ready {
            background: #f5ece3;
            color: #8a6d4f;
            border-color: #e9d7c1;
        }

        .inventory-panel {
            padding: 1rem 1.05rem 1.1rem;
        }

        .inventory-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #3e3136;
            margin-bottom: 1rem;
        }

        .alert-card {
            border: 1px solid #f0d5d7;
            background: #fbefef;
            padding: 1rem;
        }

        .alert-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f1e0df;
            font-size: 0.92rem;
            color: #66585d;
        }

        .alert-line:last-child {
            border-bottom: 0;
        }

        .restock-btn {
            width: 100%;
            margin-top: 1rem;
            border: 1px solid #d7c0c5;
            background: #fff;
            color: #7a646c;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.85rem 1rem;
        }

        @media (max-width: 991.98px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid #eadfdc;
            }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div>
            <span class="brand">Merish</span>
            <div class="brand-sub">Management Portal</div>
        </div>

        <button class="new-booking btn w-100" type="button" onclick="window.location.href='index.php?page=booking&step=1'">New Booking</button>

        <nav class="nav flex-column side-nav gap-1">
            <a class="nav-link active" href="index.php?page=admin">Dashboard</a>
            <a class="nav-link" href="index.php?page=admin&action=manageReservations">Appointments</a>
            <a class="nav-link" href="index.php?page=admin&action=manageCafeOrders">Cafe Orders</a>
            <a class="nav-link" href="index.php?page=admin&action=manageMenus">Inventory</a>
            <a class="nav-link" href="index.php?page=admin&action=manageStaff">Staff Management</a>
            <a class="nav-link" href="index.php?page=admin&action=reports">Analytics</a>
        </nav>

        <div class="sidebar-footer">
            <span class="nav-link px-0 d-block" aria-disabled="true" style="cursor: default; opacity: 0.8;">System Settings</span>
            <a class="nav-link px-0" href="index.php?page=login&action=logout">Logout</a>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="search-box rounded-0">
                <span>⌕</span>
                <input type="text" placeholder="Search..." aria-label="Search">
            </div>

            <h1 class="page-title text-center flex-grow-1">Admin Overview</h1>

            <div class="d-flex align-items-center gap-2">
                <button class="icon-btn" type="button" aria-label="Notifications">🔔</button>
                <button class="icon-btn" type="button" aria-label="Refresh">↻</button>
                <button class="icon-btn" type="button" aria-label="Help">?</button>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="stat-card">
                    <div class="stat-label">Total Pendapatan Hari Ini</div>
                    <div class="stat-value"><?php echo $formatCurrency($totalRevenueToday); ?></div>
                    <div class="stat-note">↗ Sesuai transaksi yang sudah lunas</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stat-card">
                    <div class="stat-label">Reservasi Aktif</div>
                    <div class="stat-value"><?php echo number_format($activeReservations, 0, ',', '.'); ?> Sesi</div>
                    <div class="stat-note">⏱ Next in 15 mins</div>
                </div>
            </div>
        </div>

        <div class="section-head">
            <h2 class="section-title">Integrated Queue</h2>
            <a href="#" class="text-decoration-none" style="color: var(--muted);">View All</a>
            <h2 class="section-title ms-auto me-3">Inventory Status</h2>
            <span style="color: var(--muted);">⋯</span>
        </div>

        <div class="row g-3 align-items-start">
            <div class="col-xl-8">
                <div class="table-responsive queue-table rounded-3 overflow-hidden">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Coordinate</th>
                                <th>Customer</th>
                                <th>Order Detail</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($queueItems)): ?>
                                <?php foreach ($queueItems as $row): ?>
                                    <?php
                                        $status = (string) ($row['status'] ?? 'Waiting');
                                        $badgeClass = match ($status) {
                                            'In Progress' => 'badge-soft',
                                            'Ready' => 'badge-soft badge-ready',
                                            'Selesai' => 'badge-soft badge-ready',
                                            default => 'badge-soft badge-waiting',
                                        };
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['coordinate']); ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['order_detail']); ?></td>
                                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada antrean aktif.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="inventory-panel rounded-3">
                    <div class="inventory-title">Inventory Status</div>
                    <div class="alert-card rounded-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <div class="fw-semibold" style="color: #7d5b68;">Low Stock Alert</div>
                                <div class="small text-muted"><?php echo $lowStockCount; ?> item(s) need attention</div>
                            </div>
                            <div class="fs-3" style="color: #d58a8e;">!</div>
                        </div>

                        <?php if (!empty($lowStockItems)): ?>
                            <?php foreach ($lowStockItems as $item): ?>
                                <div class="alert-line">
                                    <span><?php echo htmlspecialchars($item['item_name']); ?></span>
                                    <span style="color: #c45d5d; font-weight: 700;">
                                        <?php echo rtrim(rtrim(number_format((float) $item['stock_qty'], 2, '.', ''), '0'), '.'); ?>
                                        <?php echo htmlspecialchars($item['unit']); ?> left
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert-line">
                                <span>Coffee Beans</span>
                                <span style="color: #c45d5d; font-weight: 700;">2kg left</span>
                            </div>
                            <div class="alert-line">
                                <span>Whole Milk</span>
                                <span style="color: #c45d5d; font-weight: 700;">5L left</span>
                            </div>
                            <div class="alert-line">
                                <span>Shampoo (Premium)</span>
                                <span style="color: #c45d5d; font-weight: 700;">12btl left</span>
                            </div>
                        <?php endif; ?>

                        <button class="restock-btn" type="button">Restock Required</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php // Admin view ?>


