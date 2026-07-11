<?php
$pageTitle = 'Master Data & Stock Management - Merish Admin';
$inventories = $inventories ?? [];
$services = $services ?? [];
$menus = $menus ?? [];
$lowStockItems = $lowStockItems ?? [];
$summaryStats = $summaryStats ?? [
    'total_inventory_items' => 0,
    'low_stock_items' => 0,
    'total_services' => 0,
    'total_menus' => 0,
];
$flashSuccess = $_SESSION['success'] ?? null;
$flashError = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatCurrency = static fn ($value): string => 'Rp ' . number_format((float) $value, 0, ',', '.');
$formatNumber = static fn ($value): string => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
$catalogAnchor = '#catalog-form';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $escape($pageTitle); ?></title>
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
            --soft: #f7ece9;
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
            border-right: 1px solid var(--line);
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
            display: inline-block;
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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            display: flex;
            align-items: center;
            gap: 0.55rem;
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
            margin-bottom: 1.1rem;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3vw, 2.7rem);
            color: var(--accent);
            margin: 0;
        }

        .search-box {
            width: min(360px, 100%);
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

        .hero-box {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            border-bottom: 1px solid #eadfdd;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            color: #302429;
            font-size: clamp(2.45rem, 4vw, 4rem);
            line-height: 0.95;
            margin: 0 0 0.45rem;
        }

        .hero-subtitle {
            color: #72666b;
            font-size: 0.98rem;
        }

        .action-btn {
            background: var(--accent);
            color: #fff;
            border: 0;
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.9rem 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .tabs-line {
            display: flex;
            gap: 1.25rem;
            border-bottom: 1px solid #e8dedd;
            margin-bottom: 1.35rem;
            flex-wrap: wrap;
        }

        .tabs-line a {
            text-decoration: none;
            color: #79676c;
            font-size: 0.8rem;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.85rem 0;
            position: relative;
        }

        .tabs-line a.active,
        .tabs-line a:hover {
            color: #5d3f4d;
        }

        .tabs-line a.active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 2px;
            background: var(--accent);
        }

        .panel,
        .stat-card,
        .form-card,
        .table-card,
        .inventory-card,
        .menu-card {
            background: var(--panel);
            border: 1px solid #efe4e2;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.04);
        }

        .panel {
            padding: 1.05rem 1.05rem 1.1rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            color: #3e3136;
            font-size: 1.55rem;
            margin: 0;
        }

        .card-headline {
            font-family: 'Playfair Display', serif;
            color: #3e3136;
            font-size: 1.35rem;
            margin: 0;
        }

        .small-muted {
            color: var(--muted);
            font-size: 0.88rem;
        }

        .summary-grid {
            display: grid;
            gap: 1rem;
        }

        .stat-card {
            padding: 1.15rem 1.2rem;
            min-height: 128px;
        }

        .stat-label {
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.72rem;
            color: #8e7b80;
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2rem, 3vw, 3rem);
            line-height: 1.05;
            margin-top: 0.4rem;
        }

        .stat-note {
            margin-top: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #f8ecef;
            color: #8f707d;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            font-size: 0.72rem;
        }

        .alerts-box {
            background: #fbf1f1;
            border: 1px solid #f0d8d8;
            padding: 1rem;
        }

        .alerts-box .item {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f1e1de;
        }

        .alerts-box .item:last-child {
            border-bottom: 0;
        }

        .badge-soft {
            border-radius: 999px;
            padding: 0.4rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .badge-new {
            background: #e8e1de;
            color: #6e6462;
            border-color: #dbd1cc;
        }

        .badge-ready {
            background: #eee7d9;
            color: #6b614e;
            border-color: #ddd3c0;
        }

        .badge-low {
            background: #f6dede;
            color: #8c4b4f;
            border-color: #efc5c6;
        }

        .badge-ok {
            background: #ece7dc;
            color: #6a5c48;
            border-color: #ddd4bf;
        }

        .orders-table,
        .inventory-table,
        .menus-table {
            margin-bottom: 0;
        }

        .orders-table thead th,
        .inventory-table thead th,
        .menus-table thead th {
            border-bottom: 1px solid #eee3e1 !important;
            color: #7f6f74;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #fff;
        }

        .orders-table td,
        .inventory-table td,
        .menus-table td {
            color: #66575d;
            border-color: #f2e7e6;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .form-label {
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-size: 0.7rem;
            color: #806f75;
            font-weight: 700;
        }

        .form-control,
        .form-select {
            border-radius: 0;
            border: 1px solid #e3d5d7;
            background: #fff;
        }

        .section-anchor {
            scroll-margin-top: 1rem;
        }

        .pill-action {
            border: 1px solid #e4d7d8;
            background: #fff;
            color: #8b6472;
            padding: 0.35rem 0.65rem;
            font-size: 0.78rem;
            text-decoration: none;
        }

        .pill-action:hover {
            background: #f3e2e7;
            color: #5d3f4d;
        }

        .muted-caption {
            color: #948286;
            font-size: 0.8rem;
        }

        @media (max-width: 1199.98px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div>
                <a class="brand" href="index.php?page=admin">Merish</a>
                <div class="brand-sub">MANAGEMENT PORTAL</div>
            </div>

            <a class="new-booking" href="index.php?page=admin&action=manageReservations">+ New Appointment</a>

            <nav class="nav flex-column side-nav gap-1">
                <a class="nav-link" href="index.php?page=admin">Dashboard</a>
                <a class="nav-link" href="index.php?page=admin&action=manageReservations">Appointments</a>
                <a class="nav-link" href="index.php?page=admin&action=manageCafeOrders">Cafe Orders</a>
                <a class="nav-link active" href="index.php?page=admin&action=manageMenus">Inventory</a>
                <a class="nav-link" href="index.php?page=admin&action=manageStaff">Staff Management</a>
                <a class="nav-link" href="index.php?page=admin&action=reports">Analytics</a>
            </nav>

            <div class="sidebar-footer d-grid gap-1">
                <a class="nav-link" href="<?= LOGOUT_URL ?>">Logout</a>
            </div>
        </aside>

        <main class="main">
            <div class="topbar">
                <div class="search-box rounded-0">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="menus-search" placeholder="Search services, menus, stock...">
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <div class="d-none d-md-block text-uppercase small text-muted fw-semibold">Admin Overview</div>
                    <button type="button" class="icon-btn" aria-label="Refresh" onclick="location.reload()" title="Refresh">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                    </button>
                    <a class="action-btn ms-2" href="<?php echo $catalogAnchor; ?>">Add New Service/Menu</a>
                </div>
            </div>

            <?php if ($flashSuccess || $flashError): ?>
                <div class="mb-3">
                    <?php if ($flashSuccess): ?>
                        <div class="alert alert-success border-0 rounded-0 mb-2"><?php echo $escape($flashSuccess); ?></div>
                    <?php endif; ?>
                    <?php if ($flashError): ?>
                        <div class="alert alert-danger border-0 rounded-0 mb-2"><?php echo $escape($flashError); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <section class="hero-box">
                <div>
                    <h1 class="hero-title">Master Data &amp; Stock Management</h1>
                    <p class="hero-subtitle mb-0">Manage your service catalog, cafe menu, and raw inventory.</p>
                </div>
                <a href="<?php echo $catalogAnchor; ?>" class="action-btn">Add New Service/Menu</a>
            </section>

            <nav class="tabs-line">
                <a class="active" href="#services-section">Salon Services</a>
                <a href="#menus-section">Cafe Menu</a>
                <a href="#inventory-section">Raw Goods Inventory</a>
            </nav>

            <div class="row g-4 align-items-start">
                <div class="col-12 col-lg-8">
                    <section id="services-section" class="panel section-anchor mb-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                            <div>
                                <h2 class="card-headline mb-1">Active Treatments</h2>
                                <div class="small-muted">Daftar master service yang dipakai untuk booking salon.</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="small-muted text-uppercase fw-semibold">Total Services</div>
                                <div class="stat-value fs-3 mb-0"><?php echo number_format($summaryStats['total_services']); ?></div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table inventory-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="services-tbody">
                                    <?php if (empty($services)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada service yang terdaftar.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?php echo $escape($service['service_name']); ?></div>
                                                    <div class="muted-caption"><?php echo $escape($service['service_id']); ?></div>
                                                </td>
                                                <td><span class="badge badge-soft badge-ready"><?php echo $escape($service['category']); ?></span></td>
                                                <td><?php echo $escape($service['est_duration']); ?> min</td>
                                                <td><?php echo $formatCurrency($service['base_tariff']); ?></td>
                                                <td>
                                                    <a class="pill-action" href="index.php?page=admin&action=manageReservations">Book Flow</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section id="menus-section" class="menu-card section-anchor mb-4">
                        <div class="panel border-0 shadow-none mb-0">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                                <div>
                                    <h2 class="card-headline mb-1">Cafe Menu Items</h2>
                                    <div class="small-muted">BOM lengkap memastikan stok raw goods otomatis dipotong saat order masuk.</div>
                                </div>
                                <div class="text-end">
                                    <div class="small-muted text-uppercase fw-semibold">Menu Items</div>
                                    <div class="stat-value fs-3 mb-0"><?php echo number_format($summaryStats['total_menus']); ?></div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table menus-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Menu</th>
                                            <th>BOM Status</th>
                                            <th>Ingredients</th>
                                            <th>Price</th>
                                            <th>Availability</th>
                                        </tr>
                                    </thead>
                                    <tbody id="menus-tbody">
                                        <?php if (empty($menus)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Belum ada menu cafe yang terhubung ke BOM.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($menus as $menu): ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold"><?php echo $escape($menu['menu_name']); ?></div>
                                                        <div class="muted-caption"><?php echo $escape($menu['menu_id']); ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-soft <?php echo $menu['bom_status'] === 'Complete' ? 'badge-ok' : 'badge-low'; ?>">
                                                            <?php echo $escape($menu['bom_status']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="small"><?php echo $escape($menu['bom_items']); ?></td>
                                                    <td><?php echo $formatCurrency($menu['price']); ?></td>
                                                    <td>
                                                        <span class="badge badge-soft <?php echo (int) $menu['is_available'] === 1 ? 'badge-ok' : 'badge-low'; ?>">
                                                            <?php echo (int) $menu['is_available'] === 1 ? 'Available' : 'Unavailable'; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- CAFE INVENTORY -->
                    <section id="inventory-section" class="panel section-anchor mb-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                            <div>
                                <h2 class="card-headline mb-1">Raw Goods Inventory — Cafe</h2>
                                <div class="small-muted">Bahan baku kafe beserta menu-menu yang menggunakannya (berdasarkan BOM).</div>
                            </div>
                            <div>
                                <div class="small-muted text-uppercase fw-semibold">Low Stock</div>
                                <div class="stat-value fs-3 mb-0"><?php echo number_format($summaryStats['low_stock_items']); ?></div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table inventory-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Stock</th>
                                        <th>Minimum</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Dipakai di Menu</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory-tbody">
                                    <?php if (empty($cafeInventories)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data inventory kafe.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($cafeInventories as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?php echo $escape($item['item_name']); ?></div>
                                                    <div class="muted-caption">#<?php echo $escape($item['item_id']); ?></div>
                                                </td>
                                                <td><?php echo $formatNumber($item['stock_qty']); ?></td>
                                                <td><?php echo $formatNumber($item['min_stock']); ?></td>
                                                <td><?php echo $escape($item['unit']); ?></td>
                                                <td>
                                                    <span class="badge badge-soft <?php echo (float) $item['stock_qty'] <= (float) $item['min_stock'] ? 'badge-low' : 'badge-ok'; ?>">
                                                        <?php echo $escape($item['stock_status']); ?>
                                                    </span>
                                                </td>
                                                <td class="small text-muted"><?php echo $escape($item['used_in_menus']); ?></td>
                                                <td>
                                                    <a class="pill-action" href="#restock-form" data-item-id="<?php echo $escape($item['item_id']); ?>">Add Stock</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- SALON INVENTORY -->
                    <section id="salon-inventory-section" class="panel section-anchor">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                            <div>
                                <h2 class="card-headline mb-1">Raw Goods Inventory — Salon</h2>
                                <div class="small-muted">Bahan baku salon (shampoo, conditioner, cat rambut, dll). Belum ada data saat ini.</div>
                            </div>
                            <div>
                                <div class="small-muted text-uppercase fw-semibold">Total Items</div>
                                <div class="stat-value fs-3 mb-0">0</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table inventory-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Stock</th>
                                        <th>Minimum</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="salon-inventory-tbody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data inventory salon. Data akan ditambahkan kemudian.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="summary-grid mb-4">
                        <div class="stat-card position-relative overflow-hidden">
                            <div class="stat-label">Total Items</div>
                            <div class="stat-value"><?php echo number_format($summaryStats['total_inventory_items']); ?></div>
                            <div class="stat-note">+ <?php echo number_format($summaryStats['low_stock_items']); ?> items need attention</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-label">Inventory Alerts</div>
                            <div class="small-muted mb-2">Item yang mendekati batas minimum akan muncul di sini.</div>
                            <div class="alerts-box rounded-0">
                                <?php if (empty($lowStockItems)): ?>
                                    <div class="text-muted small">Tidak ada alert stok saat ini.</div>
                                <?php else: ?>
                                    <?php foreach ($lowStockItems as $item): ?>
                                        <div class="item">
                                            <div>
                                                <div class="fw-semibold"><?php echo $escape($item['item_name']); ?></div>
                                                <div class="muted-caption">Min <?php echo $formatNumber($item['min_stock']); ?> <?php echo $escape($item['unit']); ?></div>
                                            </div>
                                            <div class="text-end text-danger fw-semibold"><?php echo $formatNumber(abs((float) $item['shortage'])); ?> left</div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <a class="action-btn w-100 mt-3" href="#restock-form">Order Stock Now</a>
                        </div>
                    </div>

                    <div id="restock-form" class="form-card p-3 mb-4 section-anchor">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h3 class="card-headline mb-1">Add Stock</h3>
                                <div class="small-muted">Update inventory ketika supplier datang membawa barang baru.</div>
                            </div>
                        </div>

                        <form action="index.php?page=admin&action=saveInventory" method="post" class="d-grid gap-3">
                            <div>
                                <label class="form-label">Existing Item</label>
                                <select name="item_id" class="form-select">
                                    <option value="">Create new item</option>
                                    <?php foreach ($inventories as $item): ?>
                                        <option value="<?php echo $escape($item['item_id']); ?>"><?php echo $escape($item['item_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Item Name</label>
                                <input type="text" name="item_name" class="form-control" placeholder="e.g. Full Cream Milk">
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">Add Qty</label>
                                    <input type="number" min="0.01" step="0.01" name="stock_add" class="form-control" placeholder="50">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Unit</label>
                                    <input type="text" name="unit" class="form-control" placeholder="L / pcs / kg">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">Minimum Stock</label>
                                    <input type="number" min="0" step="0.01" name="min_stock" class="form-control" placeholder="10">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Extra Charge / Unit</label>
                                    <input type="number" min="0" step="0.01" name="extra_charge_per_unit" class="form-control" placeholder="0">
                                </div>
                            </div>

                            <button type="submit" class="action-btn w-100">Add Stock</button>
                            <div class="small-muted">Kalau item dipilih, sistem akan menambah stok item tersebut. Jika kosong, item baru akan dibuat.</div>
                        </form>
                    </div>

                    <div id="catalog-form" class="form-card p-3 section-anchor">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h3 class="card-headline mb-1">Add New Service/Menu</h3>
                                <div class="small-muted">Tambah master service salon atau menu cafe beserta BOM-nya.</div>
                            </div>
                        </div>

                        <form action="index.php?page=admin&action=saveCatalogItem" method="post" class="d-grid gap-3">
                            <div>
                                <label class="form-label">Catalog Type</label>
                                <select name="catalog_type" class="form-select">
                                    <option value="service">Service</option>
                                    <option value="menu">Menu</option>
                                </select>
                            </div>

                            <div class="border-top pt-3">
                                <div class="fw-semibold text-uppercase small text-muted mb-2">Service Fields</div>
                                <div class="d-grid gap-3">
                                    <div>
                                        <label class="form-label">Service Name</label>
                                        <input type="text" name="service_name" class="form-control" placeholder="Signature Balayage">
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label">Category</label>
                                            <select name="service_category" class="form-select">
                                                <option value="Hair">Hair</option>
                                                <option value="Nails">Nails</option>
                                                <option value="Lashes">Lashes</option>
                                                <option value="Wax & Eyebrows">Wax & Eyebrows</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Duration (min)</label>
                                            <input type="number" min="0" step="1" name="service_duration" class="form-control" placeholder="90">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">Service Price</label>
                                        <input type="number" min="0" step="0.01" name="service_price" class="form-control" placeholder="250000">
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <div class="fw-semibold text-uppercase small text-muted mb-2">Menu Fields</div>
                                <div class="d-grid gap-3">
                                    <div>
                                        <label class="form-label">Menu Name</label>
                                        <input type="text" name="menu_name" class="form-control" placeholder="Matcha Latte">
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label">Menu Price</label>
                                            <input type="number" min="0" step="0.01" name="menu_price" class="form-control" placeholder="65000">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label d-block">Available</label>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" value="1" id="menuAvailable" name="menu_available" checked>
                                                <label class="form-check-label" for="menuAvailable">Visible in cafe menu</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">BOM Ingredient</label>
                                        <select name="bom_item_id" class="form-select">
                                            <option value="">No BOM yet</option>
                                            <?php foreach ($inventories as $item): ?>
                                                <option value="<?php echo $escape($item['item_id']); ?>"><?php echo $escape($item['item_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label">Qty per Serving</label>
                                        <input type="number" min="0" step="0.01" name="bom_qty_required" class="form-control" placeholder="0.25">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="action-btn w-100">Add New Service/Menu</button>
                            <div class="small-muted">Untuk menu cafe, BOM sederhana akan dibuat dari satu bahan utama. Jika kosong, menu tetap tersimpan tetapi BOM belum lengkap.</div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
(function() {
    const input = document.getElementById('menus-search');
    if (!input) return;
    input.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        ['services-tbody','menus-tbody','inventory-tbody'].forEach(function(id) {
            const tbody = document.getElementById(id);
            if (!tbody) return;
            tbody.querySelectorAll('tr').forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    });
})();
</script>
</body>
</html>



