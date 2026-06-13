<?php
$pageTitle = 'Cafe Orders Live - Merish Admin';
$editOrder = $orderForEdit ?? null;
$availableMenu = $menus ?? [];
$availableReservations = $reservations ?? [];
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

        .hero-title {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2.4rem, 4vw, 3.8rem);
            margin-bottom: 0.3rem;
        }

        .hero-subtitle {
            color: #72666b;
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
            padding: 0.85rem 1rem;
        }

        .action-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .table-card,
        .form-card {
            background: var(--panel);
            border: 1px solid #efe4e2;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.04);
        }

        .orders-table thead th {
            border-bottom: 1px solid #eee3e1 !important;
            color: #7f6f74;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #fff;
        }

        .orders-table td {
            color: #66575d;
            border-color: #f2e7e6;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .badge-soft {
            border-radius: 999px;
            padding: 0.4rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .badge-progress {
            background: #efe1ec;
            color: #7a5d74;
            border-color: #e0cbd6;
        }

        .badge-ready {
            background: #eee7d9;
            color: #6b614e;
            border-color: #ddd3c0;
        }

        .badge-new {
            background: #e8e1de;
            color: #6e6462;
            border-color: #dbd1cc;
        }

        .menu-panel {
            background: var(--panel);
            border: 1px solid #efe4e2;
            padding: 1rem 1.05rem 1.1rem;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.04);
            position: sticky;
            top: 1.2rem;
        }

        .menu-title {
            font-family: 'Playfair Display', serif;
            color: #3e3136;
            font-size: 1.45rem;
            margin-bottom: 0.75rem;
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

        .small-link {
            color: #8b6472;
            text-decoration: none;
            font-size: 0.82rem;
        }

        .small-link:hover {
            color: var(--accent-dark);
        }

        .empty-state {
            padding: 1.4rem;
            border: 1px dashed #dfcecf;
            background: rgba(255,255,255,0.4);
            color: #7b6f73;
        }

        @media (max-width: 991.98px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid #eadfdc;
            }

            .menu-panel {
                position: static;
            }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div>
            <span class="brand">MERISH</span>
            <div class="brand-sub">Luxury Suite</div>
        </div>

        <button class="new-booking btn w-100" type="button" onclick="document.getElementById('order-form').scrollIntoView({behavior:'smooth'})">New Booking</button>

        <nav class="nav flex-column side-nav gap-1">
            <a class="nav-link" href="index.php?page=admin">Dashboard</a>
            <a class="nav-link" href="index.php?page=admin&action=manageReservations">Appointments</a>
            <a class="nav-link active" href="index.php?page=admin&action=manageCafeOrders">Cafe Orders</a>
            <a class="nav-link" href="index.php?page=admin&action=manageMenus">Inventory</a>
            <a class="nav-link" href="index.php?page=admin&action=manageStaff">Staff Management</a>
            <a class="nav-link" href="index.php?page=admin&action=reports">Analytics</a>
        </nav>

        <div class="sidebar-footer">
            <a class="nav-link px-0" href="index.php?page=admin&action=settings">Settings</a>
            <a class="nav-link px-0" href="index.php?page=login&action=logout">Logout</a>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="search-box rounded-0">
                <span>⌕</span>
                <input type="text" placeholder="Search..." aria-label="Search">
            </div>
            <h1 class="page-title text-center flex-grow-1">Merish Admin</h1>
            <div class="d-flex align-items-center gap-2">
                <button class="icon-btn" type="button" aria-label="Notifications">🔔</button>
                <button class="icon-btn" type="button" aria-label="Refresh">↻</button>
                <button class="icon-btn" type="button" aria-label="Profile">◌</button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <h2 class="hero-title">Pesanan Cafe (Live)</h2>
                <p class="hero-subtitle">Pantau order F&B yang sedang berjalan, selesai, atau perlu intervensi.</p>
            </div>
            <div>
                <button class="action-btn" type="button" onclick="document.getElementById('order-form').scrollIntoView({behavior:'smooth'})">Add / Edit Order</button>
            </div>
        </div>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success rounded-0 border-0 mb-4"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger rounded-0 border-0 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="table-card rounded-3 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table orders-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Coordinate</th>
                                    <th>Customer</th>
                                    <th>Order</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Bill</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <?php
                                            $status = (string) ($order['status'] ?? 'New');
                                            $statusClass = match ($status) {
                                                'In Progress' => 'badge-progress',
                                                'Ready', 'Completed' => 'badge-ready',
                                                default => 'badge-new',
                                            };
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['seat_id'] ?? 'Walk-in'); ?></td>
                                            <td>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($order['guest_name']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($order['payment_method'] ?? 'Cash'); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($order['order_items'] ?? '-'); ?></td>
                                            <td><span class="badge-soft <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                            <td><?php echo 'Rp ' . number_format((float) ($order['total_amount'] ?? 0), 0, ',', '.'); ?></td>
                                            <td class="text-end">
                                                <a class="small-link me-3" href="index.php?page=admin&action=manageCafeOrders&edit=<?php echo (int) $order['order_id']; ?>">Edit Order</a>
                                                <form method="POST" action="index.php?page=admin&action=forceCompleteOrder" class="d-inline me-2">
                                                    <input type="hidden" name="order_id" value="<?php echo (int) $order['order_id']; ?>">
                                                    <button type="submit" class="btn btn-link p-0 small-link text-decoration-none">Force Complete</button>
                                                </form>
                                                <form method="POST" action="index.php?page=admin&action=cancelCafeOrder" class="d-inline" onsubmit="return confirm('Cancel order ini?');">
                                                    <input type="hidden" name="order_id" value="<?php echo (int) $order['order_id']; ?>">
                                                    <button type="submit" class="btn btn-link p-0 small-link text-danger text-decoration-none">Cancel Order</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7"><div class="empty-state text-center">Belum ada pesanan cafe yang masuk.</div></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-4" id="order-form">
                <div class="menu-panel rounded-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="menu-title mb-1"><?php echo $editOrder ? 'Edit Order' : 'Add Order'; ?></h3>
                            <div class="text-muted small">Intervensi transaksi F&B live tanpa keluar dari dashboard.</div>
                        </div>
                        <?php if ($editOrder): ?>
                            <a class="small-link" href="index.php?page=admin&action=manageCafeOrders">Reset</a>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="index.php?page=admin&action=saveCafeOrder">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars((string) ($editOrder['order_id'] ?? '')); ?>">

                        <div class="mb-3">
                            <label class="form-label">Customer / Guest</label>
                            <input type="text" name="guest_name" class="form-control" value="<?php echo htmlspecialchars($editOrder['guest_name'] ?? ''); ?>" placeholder="Optional guest name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Seat / Table</label>
                            <input type="text" name="seat_id" class="form-control" value="<?php echo htmlspecialchars($editOrder['seat_id'] ?? ''); ?>" placeholder="S01 / C01">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Menu Item</label>
                            <select name="menu_id" class="form-select" required>
                                <option value="">Choose menu</option>
                                <?php foreach ($availableMenu as $menu): ?>
                                    <option value="<?php echo htmlspecialchars($menu['menu_id']); ?>" <?php echo (($editOrder['menu_id'] ?? '') === $menu['menu_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($menu['menu_name'] . ' - Rp ' . number_format((float) $menu['price'], 0, ',', '.')); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <label class="form-label">Qty</label>
                                <input type="number" min="1" name="qty" class="form-control" value="<?php echo htmlspecialchars((string) ($editOrder['qty'] ?? 1)); ?>" required>
                            </div>
                            <div class="col-8">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    <?php foreach (['Unpaid', 'Paid'] as $paymentStatus): ?>
                                        <option value="<?php echo $paymentStatus; ?>" <?php echo (($editOrder['payment_status'] ?? 'Unpaid') === $paymentStatus) ? 'selected' : ''; ?>><?php echo $paymentStatus; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach (['New', 'In Progress', 'Ready', 'Completed'] as $state): ?>
                                    <option value="<?php echo $state; ?>" <?php echo (($editOrder['status'] ?? 'New') === $state) ? 'selected' : ''; ?>><?php echo $state; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="action-btn"><?php echo $editOrder ? 'Save Changes' : 'Add Order'; ?></button>
                            <?php if ($editOrder): ?>
                                <a href="index.php?page=admin&action=manageCafeOrders" class="btn btn-outline-secondary rounded-0 text-uppercase fw-semibold" style="letter-spacing: 1.2px;">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>