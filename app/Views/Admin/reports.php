<?php
$pageTitle = 'Performance Intelligence - Merish Admin';
$startDate = $startDate ?? date('Y-m-01');
$endDate = $endDate ?? date('Y-m-t');
$reportType = $reportType ?? 'all';
$reportData = $reportData ?? ['title' => 'Report', 'description' => '', 'headers' => [], 'rows' => []];
$summaryCards = $summaryCards ?? ['total_revenue' => 0, 'total_appointments' => 0, 'total_cafe_orders' => 0];
$trendData = $trendData ?? ['labels' => [], 'salon' => [], 'cafe' => []];

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatCurrency = static fn ($value): string => 'Rp ' . number_format((float) $value, 0, ',', '.');
$reportTypeLabel = match ($reportType) {
    'stock' => 'Laporan Stok',
    'top-services' => 'Layanan Paling Laku (Salon)',
    'top-menu' => 'Menu Paling Laku (Kafe)',
    'top-employee' => 'Performa Stylist & EOTM',
    'all' => 'Semua Laporan Terpadu',
    default => 'Laporan Pendapatan',
};

$baseQuery = 'index.php?page=admin&action=reports&start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate) . '&report_type=' . urlencode($reportType);
$pdfUrl = $baseQuery . '&export=pdf';
$csvUrl = $baseQuery . '&export=csv';

$trendLabels = $trendData['labels'] ?? [];
$salonTrend = $trendData['salon'] ?? [];
$cafeTrend = $trendData['cafe'] ?? [];

$trendMax = 1.0;
foreach (array_merge($salonTrend, $cafeTrend) as $point) {
    $trendMax = max($trendMax, (float) $point);
}

$topSalon = array_slice($reportData['rows'] ?? [], 0, 3);
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
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
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
            padding: 1rem 1.2rem 1.4rem;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.1rem;
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

        .top-title {
            font-family: 'Playfair Display', serif;
            color: #57464c;
            font-size: 2rem;
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

        .action-btn {
            background: var(--accent);
            color: #fff;
            border: 0;
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.7rem 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .panel,
        .stat-card,
        .table-panel {
            background: #fff;
            border: 1px solid #efe4e2;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.04);
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            color: #302429;
            font-size: clamp(2.4rem, 4vw, 3.8rem);
            margin: 0 0 0.25rem;
            line-height: 0.98;
        }

        .hero-sub {
            color: #786b70;
            margin: 0;
        }

        .report-filter {
            padding: 1rem;
        }

        .form-label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.68rem;
            color: #816f75;
            font-weight: 700;
        }

        .form-control,
        .form-select {
            border-radius: 0;
            border: 1px solid #e3d5d7;
        }

        .stat-card {
            padding: 1rem;
            min-height: 112px;
        }

        .stat-label {
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.72rem;
            color: #8d7a7f;
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: 2rem;
            margin-top: 0.35rem;
            line-height: 1.1;
        }

        .trend-panel {
            padding: 1rem;
        }

        .trend-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin: 0;
            color: #392d33;
        }

        .trend-chart {
            height: 230px;
            border: 1px solid #efe4e2;
            background: linear-gradient(to top, #faf4f3 0%, #ffffff 70%);
            position: relative;
            margin-top: 0.8rem;
            padding: 0.8rem;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.45rem;
            align-items: end;
        }

        .trend-chart > div {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .bar-stack {
            display: flex;
            gap: 0.22rem;
            align-items: end;
            height: 100%;
        }

        /* Chart switcher styling */
        .chart-toggle-btn {
            background: transparent;
            border: 1px solid #efe4e2;
            color: #8d7b80;
            padding: 2px 10px;
            font-size: 0.72rem;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .chart-toggle-btn:hover {
            background: #fff0ee;
            border-color: var(--accent);
            color: var(--accent);
        }
        .chart-toggle-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        /* Toggle visibility */
        .trend-chart.view-salon .bar.cafe {
            display: none !important;
        }
        .trend-chart.view-salon .bar.salon {
            width: 100% !important;
        }
        .trend-chart.view-cafe .bar.salon {
            display: none !important;
        }
        .trend-chart.view-cafe .bar.cafe {
            width: 100% !important;
        }

        .bar {
            width: 50%;
            border-radius: 3px 3px 0 0;
        }

        .bar.salon {
            background: rgba(139, 100, 114, 0.45);
            border: 1px solid rgba(139, 100, 114, 0.6);
        }

        .bar.cafe {
            background: rgba(171, 150, 161, 0.35);
            border: 1px dashed rgba(133, 101, 114, 0.7);
        }

        .bar-label {
            margin-top: 0.3rem;
            text-align: center;
            color: #8d7b80;
            font-size: 0.7rem;
        }

        .list-panel {
            padding: 1rem;
        }

        .list-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #3f3237;
            margin: 0;
        }

        .metric-item {
            display: flex;
            justify-content: space-between;
            gap: 0.6rem;
            border-bottom: 1px solid #efe4e2;
            padding: 0.7rem 0;
        }

        .metric-item:last-child {
            border-bottom: 0;
        }

        .table-panel {
            padding: 1rem;
        }

        .table thead th {
            border-bottom: 1px solid #eee3e1 !important;
            color: #7f6f74;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #fff;
        }

        .table td {
            color: #66575d;
            border-color: #f2e7e6;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .export-section {
            text-align: center;
            margin-top: 1.25rem;
            padding: 1.4rem 1rem;
            border-top: 1px solid #eadfdd;
        }

        .export-title {
            font-family: 'Playfair Display', serif;
            color: #372b30;
            margin-bottom: 0.3rem;
            font-size: 2rem;
        }

        .secondary-btn {
            border: 1px solid #d7c6cc;
            color: #715a63;
            background: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.7rem 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .secondary-btn:hover {
            background: #f6ecef;
            color: #5d3f4d;
        }

        .muted-note {
            color: #918085;
            font-size: 0.88rem;
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
                <div class="brand-sub">Management Portal</div>
            </div>

            <a class="new-booking" href="index.php?page=admin&action=manageReservations">+ New Appointment</a>

            <nav class="nav flex-column side-nav gap-1">
                <a class="nav-link" href="index.php?page=admin">Dashboard</a>
                <a class="nav-link" href="index.php?page=admin&action=manageReservations">Appointments</a>
                <a class="nav-link" href="index.php?page=admin&action=manageCafeOrders">Cafe Orders</a>
                <a class="nav-link" href="index.php?page=admin&action=manageMenus">Inventory</a>
                <a class="nav-link" href="index.php?page=admin&action=manageStaff">Staff Management</a>
                <a class="nav-link active" href="index.php?page=admin&action=reports">Analytics</a>
            </nav>

            <div class="sidebar-footer d-grid gap-1">
                <a class="nav-link" href="<?= LOGOUT_URL ?>">Logout</a>
            </div>
        </aside>

        <main class="main">
            <div class="topbar">
                <h1 class="top-title">Admin Overview</h1>
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <div class="search-box rounded-0">
                        <span>⌕</span>
                        <input type="text" placeholder="Search data...">
                    </div>
                    <button class="icon-btn" type="button" onclick="location.reload()" title="Reload page">↺</button>
                    <a class="secondary-btn" href="#generate-reports">Export Report</a>
                    <a class="action-btn" href="index.php?page=admin&action=manageCafeOrders">Live Queue</a>
                </div>
            </div>

            <section class="mb-3">
                <h2 class="hero-title">Performance Intelligence</h2>
                <p class="hero-sub">Analyze service popularity, menu trends, and financial growth.</p>
            </section>

            <section class="panel report-filter mb-3">
                <form action="index.php" method="get" class="row g-3 align-items-end">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="action" value="reports">

                    <div class="col-12 col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $escape($startDate); ?>">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $escape($endDate); ?>">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Jenis Laporan</label>
                        <select class="form-select" name="report_type">
                            <option value="all" <?php echo $reportType === 'all' ? 'selected' : ''; ?>>Semua Laporan Terpadu</option>
                            <option value="revenue" <?php echo $reportType === 'revenue' ? 'selected' : ''; ?>>Laporan Pendapatan</option>
                            <option value="stock" <?php echo $reportType === 'stock' ? 'selected' : ''; ?>>Laporan Stok</option>
                            <option value="top-services" <?php echo $reportType === 'top-services' ? 'selected' : ''; ?>>Layanan Paling Laku (Salon)</option>
                            <option value="top-menu" <?php echo $reportType === 'top-menu' ? 'selected' : ''; ?>>Menu Paling Laku (Kafe)</option>
                            <option value="top-employee" <?php echo $reportType === 'top-employee' ? 'selected' : ''; ?>>Performa Stylist & EOTM</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-grid">
                        <button type="submit" class="action-btn">Filter</button>
                    </div>
                </form>
            </section>

            <div class="row g-3 mb-3">
                <div class="col-12 col-lg-3">
                    <div class="stat-card mb-3">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value"><?php echo $formatCurrency($summaryCards['total_revenue'] ?? 0); ?></div>
                    </div>
                    <div class="stat-card mb-3">
                        <div class="stat-label">Salon Appointments</div>
                        <div class="stat-value"><?php echo number_format((int) ($summaryCards['total_appointments'] ?? 0)); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Cafe Orders</div>
                        <div class="stat-value"><?php echo number_format((int) ($summaryCards['total_cafe_orders'] ?? 0)); ?></div>
                    </div>
                </div>

                <div class="col-12 col-lg-9">
                    <div class="trend-panel panel h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                            <div>
                                <h3 class="trend-title">Revenue Growth Trend</h3>
                                <div class="muted-note">Combined Salon &amp; Cafe performance over the selected period.</div>
                                <div class="d-flex align-items-center gap-1 mt-2">
                                    <button type="button" class="chart-toggle-btn active" data-view="both">Semua</button>
                                    <button type="button" class="chart-toggle-btn" data-view="salon">Salon</button>
                                    <button type="button" class="chart-toggle-btn" data-view="cafe">Kafe</button>
                                </div>
                            </div>
                            <div class="muted-note">Period: <?php echo $escape($startDate); ?> - <?php echo $escape($endDate); ?></div>
                        </div>

                        <div class="trend-chart" id="revenue-trend-chart">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <?php
                                $label = $trendLabels[$i] ?? 'N/A';
                                $salonValue = (float) ($salonTrend[$i] ?? 0);
                                $cafeValue = (float) ($cafeTrend[$i] ?? 0);
                                $salonHeight = max(6, (int) round(($salonValue / $trendMax) * 100));
                                $cafeHeight = max(6, (int) round(($cafeValue / $trendMax) * 100));
                                ?>
                                <div>
                                    <div class="bar-stack">
                                        <div class="bar salon" style="height: <?php echo $salonHeight; ?>%;" title="Salon: <?php echo $escape($formatCurrency($salonValue)); ?>"></div>
                                        <div class="bar cafe" style="height: <?php echo $cafeHeight; ?>%;" title="Cafe: <?php echo $escape($formatCurrency($cafeValue)); ?>"></div>
                                    </div>
                                    <div class="bar-label"><?php echo $escape($label); ?></div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-lg-6">
                    <div class="list-panel panel h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="list-title">Popular Salon Treatments</h3>
                            <a class="muted-note" href="index.php?page=admin&action=reports&report_type=top-services&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>">View All</a>
                        </div>

                        <?php if (empty($topSalon)): ?>
                            <div class="muted-note">Belum ada data layanan pada periode ini.</div>
                        <?php else: ?>
                            <?php foreach ($topSalon as $row): ?>
                                <div class="metric-item">
                                    <div>
                                        <div class="fw-semibold"><?php echo $escape((string) ($row[0] ?? '-')); ?></div>
                                        <div class="muted-note"><?php echo $escape((string) ($row[1] ?? '-')); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold"><?php echo $escape((string) ($row[2] ?? '0')); ?> bookings</div>
                                        <div class="muted-note"><?php echo $escape((string) ($row[4] ?? '-')); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="list-panel panel h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="list-title">Top Selling Cafe Menu</h3>
                            <a class="muted-note" href="index.php?page=admin&action=manageCafeOrders">View All</a>
                        </div>

                        <?php
                        $topMenuRows = [];
                        if (($reportType === 'revenue' || $reportType === 'top-services') && !empty($trendLabels)) {
                            $topMenuRows[] = ['Signature Vanilla Bean Latte', 'Beverage - Hot', 'Trending'];
                            $topMenuRows[] = ['Smashed Avocado Sourdough', 'Food - Breakfast', 'Consistent'];
                            $topMenuRows[] = ['Matcha Almond Croissant', 'Food - Pastry', 'Trending'];
                        }
                        ?>
                        <?php foreach ($topMenuRows as $menu): ?>
                            <div class="metric-item">
                                <div>
                                    <div class="fw-semibold"><?php echo $escape($menu[0]); ?></div>
                                    <div class="muted-note"><?php echo $escape($menu[1]); ?></div>
                                </div>
                                <div class="text-end muted-note"><?php echo $escape($menu[2]); ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($topMenuRows)): ?>
                            <div class="muted-note">Pilih laporan pendapatan untuk melihat ringkasan menu trending.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($reportData['is_all'])): ?>
                <?php foreach ($reportData['sections'] as $section): ?>
                    <section class="table-panel mb-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2 flex-wrap">
                            <div>
                                <h3 class="trend-title"><?php echo $escape($section['title']); ?></h3>
                                <div class="muted-note"><?php echo $escape($section['description']); ?></div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <?php foreach ($section['headers'] as $header): ?>
                                            <th><?php echo $escape($header); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($section['rows'])): ?>
                                        <tr>
                                            <td colspan="<?php echo max(1, count($section['headers'])); ?>" class="text-center text-muted py-4">Tidak ada data.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($section['rows'] as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $cell): ?>
                                                    <td><?php echo $escape((string) $cell); ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php else: ?>
                <section class="table-panel mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2 flex-wrap">
                        <div>
                            <h3 class="trend-title"><?php echo $escape($reportData['title'] ?? 'Report'); ?></h3>
                            <div class="muted-note"><?php echo $escape($reportData['description'] ?? ''); ?></div>
                        </div>
                        <div class="muted-note">Jenis Laporan: <?php echo $escape($reportTypeLabel); ?></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <?php foreach (($reportData['headers'] ?? []) as $header): ?>
                                        <th><?php echo $escape($header); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reportData['rows'] ?? [])): ?>
                                    <tr>
                                        <td colspan="<?php echo max(1, count($reportData['headers'] ?? [])); ?>" class="text-center text-muted py-4">Tidak ada data pada rentang filter ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach (($reportData['rows'] ?? []) as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $cell): ?>
                                                <td><?php echo $escape((string) $cell); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <section id="generate-reports" class="export-section">
                <h3 class="export-title">Generate Official Reports</h3>
                <p class="muted-note">Pilih jenis laporan dan format dokumen yang ingin Anda ekspor.</p>
                <form action="index.php" method="get" class="row g-3 justify-content-center mt-3 text-start" style="max-width: 800px; margin: 0 auto;">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="action" value="reports">
                    <input type="hidden" name="start_date" value="<?php echo $escape($startDate); ?>">
                    <input type="hidden" name="end_date" value="<?php echo $escape($endDate); ?>">

                    <div class="col-12 col-md-5">
                        <label class="form-label small fw-semibold">Pilih Isi Laporan</label>
                        <select name="report_type" class="form-select" style="border-radius:0;">
                            <option value="all" <?php echo $reportType === 'all' ? 'selected' : ''; ?>>Semua Laporan (Pendapatan, Stok, Layanan, Menu, Stylist)</option>
                            <option value="revenue" <?php echo $reportType === 'revenue' ? 'selected' : ''; ?>>Laporan Pendapatan</option>
                            <option value="stock" <?php echo $reportType === 'stock' ? 'selected' : ''; ?>>Laporan Stok</option>
                            <option value="top-services" <?php echo $reportType === 'top-services' ? 'selected' : ''; ?>>Layanan Paling Laku (Salon)</option>
                            <option value="top-menu" <?php echo $reportType === 'top-menu' ? 'selected' : ''; ?>>Menu Paling Laku (Kafe)</option>
                            <option value="top-employee" <?php echo $reportType === 'top-employee' ? 'selected' : ''; ?>>Performa Stylist & EOTM</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold">Format Dokumen</label>
                        <select name="export" class="form-select" style="border-radius:0;">
                            <option value="pdf">Portable Document Format (PDF)</option>
                            <option value="csv">Comma-Separated Values (CSV)</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button type="submit" class="action-btn w-100" style="height: 38px;">Unduh Laporan</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
    <script>
    document.querySelectorAll('.chart-toggle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.chart-toggle-btn').forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const view = this.getAttribute('data-view');
            const chart = document.getElementById('revenue-trend-chart');
            
            if (chart) {
                // Clear any view class
                chart.classList.remove('view-salon', 'view-cafe');
                if (view === 'salon') {
                    chart.classList.add('view-salon');
                } else if (view === 'cafe') {
                    chart.classList.add('view-cafe');
                }
            }
        });
    });
    </script>
</body>
</html>



