<?php
$pageTitle = 'Salon VIP Appointments - Merish Admin';
$displayName = $_SESSION['full_name'] ?? 'Admin';
$editData = $reservationForEdit ?? null;
$formattedDate = $editData['reservation_date'] ?? date('Y-m-d');
$formattedTime = $editData['reservation_time'] ?? date('H:i');
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
            margin-bottom: 1.25rem;
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
            font-size: clamp(2.5rem, 4vw, 4rem);
            margin-bottom: 0.35rem;
        }

        .hero-subtitle {
            color: #72666b;
            margin-bottom: 0;
        }

        .hero-actions .btn,
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

        .hero-actions .btn:hover,
        .action-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .filter-bar .form-control,
        .filter-bar .form-select {
            border-radius: 0;
            border: 1px solid #ded1d2;
            background: rgba(255, 255, 255, 0.72);
            color: #5f5358;
            min-height: 50px;
        }

        .table-card,
        .form-card {
            background: var(--panel);
            border: 1px solid #efe4e2;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.04);
        }

        .appointments-table thead th {
            border-bottom: 1px solid #eee3e1 !important;
            color: #7f6f74;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #fff;
        }

        .appointments-table td {
            color: #66575d;
            border-color: #f2e7e6;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .badge-status {
            border-radius: 999px;
            padding: 0.4rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .badge-confirmed {
            background: #eee7d9;
            color: #6b614e;
            border-color: #ddd3c0;
        }

        .badge-progress {
            background: #efe1ec;
            color: #7a5d74;
            border-color: #e0cbd6;
        }

        .badge-waiting {
            background: #e8e1de;
            color: #6e6462;
            border-color: #dbd1cc;
        }

        .badge-canceled {
            background: #f4d8db;
            color: #8f5b63;
            border-color: #e5bdc3;
        }

        .form-card {
            padding: 1.3rem;
            position: sticky;
            top: 1.2rem;
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

            .form-card {
                position: static;
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

        <button class="new-booking btn w-100" type="button" onclick="document.getElementById('reservation-form').scrollIntoView({behavior:'smooth'})">+ New Appointment</button>

        <nav class="nav flex-column side-nav gap-1">
            <a class="nav-link" href="index.php?page=admin">Dashboard</a>
            <a class="nav-link active" href="index.php?page=admin&action=manageReservations">Appointments</a>
            <a class="nav-link" href="index.php?page=admin&action=manageCafeOrders">Cafe Orders</a>
            <a class="nav-link" href="index.php?page=admin&action=manageMenus">Inventory</a>
            <a class="nav-link" href="index.php?page=admin&action=manageStaff">Staff Management</a>
            <a class="nav-link" href="index.php?page=admin&action=reports">Analytics</a>
        </nav>

        <div class="sidebar-footer">
            <a class="nav-link px-0" href="<?= LOGOUT_URL ?>">Logout</a>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="search-box rounded-0">
                <span></span>
                <input type="text" id="res-topbar-search" placeholder="Search..." aria-label="Search">
            </div>
            <h1 class="page-title text-center flex-grow-1">Merish Admin</h1>
            <div class="d-flex align-items-center gap-2">
                <button class="icon-btn" type="button" aria-label="Refresh" onclick="location.reload()" title="Refresh">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <h2 class="hero-title">Salon VIP Appointments</h2>
                <p class="hero-subtitle">Manage exclusive bookings and curate the editorial experience.</p>
            </div>
            <div class="hero-actions">
                <a class="btn" href="index.php?page=admin&action=manageReservations">+ New Appointment</a>
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
                <div class="filter-bar row g-3 mb-3">
                    <div class="col-md-4">
                        <select id="res-filter-stylist" class="form-select" aria-label="Filter by stylist">
                            <option value="">By Stylist</option>
                            <?php foreach ($beauticians as $beautician): ?>
                                <option><?php echo htmlspecialchars($beautician['NAME']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="res-filter-status" class="form-select" aria-label="Filter by status">
                            <option value="">By Status</option>
                            <option>Pending</option>
                            <option>Confirmed</option>
                            <option>In-Service</option>
                            <option>Selesai</option>
                            <option>Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input id="res-filter-text" type="text" class="form-control" placeholder="Search client or service...">
                    </div>
                </div>

                <div class="table-card rounded-3 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table appointments-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Service</th>
                                    <th>Seat</th>
                                    <th>Time</th>
                                    <th>Stylist</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="res-tbody">
                                <?php if (!empty($reservations)): ?>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <?php
                                            $status = (string) ($reservation['status'] ?? 'Pending');
                                            $statusClass = match ($status) {
                                                'Confirmed' => 'badge-confirmed',
                                                'In-Service' => 'badge-progress',
                                                'Selesai' => 'badge-confirmed',
                                                'Canceled' => 'badge-canceled',
                                                default => 'badge-waiting',
                                            };
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($reservation['customer_name']); ?></div>
                                                <div class="text-muted small">#<?php echo htmlspecialchars((string) $reservation['res_id']); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($reservation['service_name']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['seat_id']); ?></td>
                                            <td>
                                                <div><?php echo htmlspecialchars((string) $reservation['reservation_date']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars(substr((string) $reservation['reservation_time'], 0, 5)); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($reservation['beautician_name']); ?></td>
                                            <td><span class="badge-status <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                            <td class="text-end">
                                                <a class="small-link me-3" href="index.php?page=admin&action=manageReservations&edit=<?php echo (int) $reservation['res_id']; ?>">Add/Edit Booking</a>
                                                <form method="POST" action="index.php?page=admin&action=cancelReservation" class="d-inline" onsubmit="return confirm('Cancel booking ini?');">
                                                    <input type="hidden" name="res_id" value="<?php echo (int) $reservation['res_id']; ?>">
                                                    <button type="submit" class="btn btn-link p-0 small-link text-danger text-decoration-none">Cancel</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state text-center">Belum ada booking yang tercatat.</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-4" id="reservation-form">
                <div class="form-card rounded-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="section-title mb-1"><?php echo $editData ? 'Edit Booking' : 'Add Booking'; ?></h3>
                            <div class="text-muted small">Create or reschedule a salon VIP reservation.</div>
                        </div>
                        <?php if ($editData): ?>
                            <a class="small-link" href="index.php?page=admin&action=manageReservations">Reset</a>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="index.php?page=admin&action=saveReservation">
                        <input type="hidden" name="res_id" value="<?php echo htmlspecialchars((string) ($editData['res_id'] ?? '')); ?>">

                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="guest_name" class="form-control" value="<?php echo htmlspecialchars($editData['guest_name'] ?? ''); ?>" placeholder="Enter customer name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Seat / Coordinate</label>
                            <select name="seat_id" class="form-select" required>
                                <option value="">Choose seat</option>
                                <?php foreach ($seats as $seat): ?>
                                    <option value="<?php echo htmlspecialchars($seat['seat_id']); ?>" <?php echo (($editData['seat_id'] ?? '') === $seat['seat_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($seat['seat_name']); ?> - <?php echo htmlspecialchars($seat['zone_type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="reservation_date" class="form-control" value="<?php echo htmlspecialchars($formattedDate); ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Time</label>
                                <input type="time" name="reservation_time" class="form-control" value="<?php echo htmlspecialchars(substr($formattedTime, 0, 5)); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service</label>
                            <select name="service_id" class="form-select">
                                <option value="">Choose service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo htmlspecialchars($service['service_id']); ?>" <?php echo (($editData['service_id'] ?? '') === $service['service_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($service['service_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stylist</label>
                            <select name="beautician_id" class="form-select">
                                <option value="">Choose stylist</option>
                                <?php foreach ($beauticians as $beautician): ?>
                                    <option value="<?php echo htmlspecialchars((string) $beautician['user_id']); ?>" <?php echo (($editData['beautician_id'] ?? '') == $beautician['user_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($beautician['NAME']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach (['Pending', 'Confirmed', 'In-Service', 'Selesai', 'Canceled'] as $option): ?>
                                    <option value="<?php echo $option; ?>" <?php echo (($editData['STATUS'] ?? 'Pending') === $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="action-btn"><?php echo $editData ? 'Save Changes' : 'Add Booking'; ?></button>
                            <?php if ($editData): ?>
                                <a href="index.php?page=admin&action=manageReservations" class="btn btn-outline-secondary rounded-0 text-uppercase fw-semibold" style="letter-spacing: 1.2px;">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
(function() {
    function filterRows() {
        const q1 = (document.getElementById('res-topbar-search').value || '').toLowerCase().trim();
        const q2 = (document.getElementById('res-filter-text').value || '').toLowerCase().trim();
        const q = q1 || q2;
        const stylist = (document.getElementById('res-filter-stylist').value || '').toLowerCase().trim();
        const status = (document.getElementById('res-filter-status').value || '').toLowerCase().trim();
        document.querySelectorAll('#res-tbody tr').forEach(function(row) {
            const text = row.textContent.toLowerCase();
            const show = (!q || text.includes(q))
                      && (!stylist || text.includes(stylist))
                      && (!status || text.includes(status));
            row.style.display = show ? '' : 'none';
        });
    }
    ['res-topbar-search','res-filter-text','res-filter-stylist','res-filter-status'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', filterRows);
        if (el) el.addEventListener('change', filterRows);
    });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

