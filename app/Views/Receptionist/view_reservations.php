<?php
$reservations = $reservations ?? [];
$availableSalonSeats = $availableSalonSeats ?? [];
$availableLoungeSeats = $availableLoungeSeats ?? [];
$date = $date ?? date('Y-m-d');
$filter = $filter ?? 'all';
$flashSuccess = $flashSuccess ?? null;
$flashError = $flashError ?? null;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$statusClass = static function (string $status): string {
    return match ($status) {
        'In-Service', 'Confirmed' => 'status-checked',
        'Pending' => 'status-pending',
        default => 'status-other',
    };
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Appointments - Merish Reception</title>
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
            grid-template-columns: 220px minmax(0, 1fr);
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
            margin-top: 0.5rem;
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

        .main {
            padding: 1.1rem 1.2rem;
        }

        .header-title {
            color: #7a6a71;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            margin-bottom: 0.1rem;
            font-weight: 700;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            color: #33272c;
            font-size: 4rem;
            line-height: 0.95;
            margin: 0;
        }

        .top-info {
            border: 1px solid #e2d6d9;
            background: #fff;
            min-width: 250px;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .top-info > div {
            padding: 0.6rem 0.8rem;
            border-right: 1px solid #efe4e7;
        }

        .top-info > div:last-child { border-right: 0; }

        .top-info .label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.68rem;
            color: #7e6d73;
            font-weight: 700;
        }

        .top-info .value {
            margin-top: 0.22rem;
            font-size: 1.03rem;
            color: #4c3f45;
        }

        .toolbar {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .search-box {
            min-width: 320px;
            flex: 1;
            max-width: 520px;
            border: 1px solid #dfd0d4;
            background: #fff;
            display: flex;
            align-items: center;
            padding: 0.56rem 0.75rem;
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

        .pill {
            border: 1px solid #d8c8cd;
            background: #fff;
            color: #6b5a61;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.66rem 0.9rem;
            text-decoration: none;
            line-height: 1;
        }

        .pill.active {
            background: #d8aebf;
            color: #fff;
            border-color: #c498aa;
        }

        .table-wrap {
            margin-top: 1rem;
            border: 1px solid #e6d9dd;
            background: #fff;
        }

        .table thead th {
            border-bottom: 1px solid #e9dde1 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.72rem;
            color: #7e6f75;
            font-weight: 700;
            background: #fff;
        }

        .table td {
            vertical-align: middle;
            border-color: #f1e7ea;
            color: #5f4f56;
        }

        .status-badge {
            border: 1px solid #ddd2d6;
            background: #f4edf0;
            color: #715d65;
            padding: 0.2rem 0.5rem;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .status-pending {
            background: #f0ecee;
            color: #6f6167;
        }

        .status-checked {
            background: #efe4ea;
            color: #7f5d6f;
        }

        .status-overdue {
            background: #f8dede;
            color: #a24949;
            border-color: #edc6c6;
        }

        .checkin-btn {
            border: 0;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.68rem;
            padding: 0.5rem 0.8rem;
            font-weight: 700;
        }

        .checkin-btn:disabled {
            background: #ede5e8;
            color: #8c7c82;
        }

        .form-label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.7rem;
            color: #806f75;
            font-weight: 700;
        }

        .form-control,
        .form-select {
            border-radius: 0;
            border: 1px solid #e3d5d7;
        }

        .companion-fields {
            display: none;
        }

        @media (max-width: 1000px) {
            .shell { grid-template-columns: 1fr; }
            .page-title { font-size: 3rem; }
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

            <a class="new-booking" href="index.php?page=receptionist&action=scheduleBooking">New Booking</a>

            <nav class="nav flex-column side-nav gap-1">
                <a class="nav-link" href="index.php?page=receptionist">Seat Map</a>
                <a class="nav-link active" href="index.php?page=receptionist&action=viewReservations">Appointments</a>
                <a class="nav-link" href="index.php?page=receptionist&action=viewOrders">Cashier</a>
            </nav>
        </aside>

        <main class="main">
            <?php if ($flashSuccess): ?>
                <div class="alert alert-success border-0 rounded-0"><?php echo $escape($flashSuccess); ?></div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="alert alert-danger border-0 rounded-0"><?php echo $escape($flashError); ?></div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <div class="header-title">Today's Schedule</div>
                    <h1 class="page-title">Appointments</h1>
                </div>
                <div class="top-info">
                    <div>
                        <div class="label">Date</div>
                        <div class="value"><?php echo $escape(date('M d, Y', strtotime($date))); ?></div>
                    </div>
                    <div>
                        <div class="label">Remaining</div>
                        <div class="value"><?php echo count($reservations); ?> Pre-booked</div>
                    </div>
                </div>
            </div>

            <div class="toolbar">
                <div class="search-box rounded-0">
                    <span>⌕</span>
                    <input type="text" placeholder="Search customer or stylist...">
                </div>

                <a class="pill <?php echo $filter === 'all' ? 'active' : ''; ?>" href="index.php?page=receptionist&action=viewReservations&date=<?php echo urlencode($date); ?>&filter=all">All</a>
                <a class="pill <?php echo $filter === 'pending' ? 'active' : ''; ?>" href="index.php?page=receptionist&action=viewReservations&date=<?php echo urlencode($date); ?>&filter=pending">Pending</a>
                <a class="pill <?php echo $filter === 'checked-in' ? 'active' : ''; ?>" href="index.php?page=receptionist&action=viewReservations&date=<?php echo urlencode($date); ?>&filter=checked-in">Checked In</a>
            </div>

            <div class="table-wrap">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-3">Time</th>
                                <th>Customer</th>
                                <th>Kapster</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reservations)): ?>
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-muted">Tidak ada appointment untuk tanggal ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservations as $row): ?>
                                    <?php
                                    $status = (string) ($row['STATUS'] ?? 'Pending');
                                    $isOverdue = $status === 'Pending' && (int) ($row['lateness_minutes'] ?? 0) > 0;
                                    $statusLabel = $isOverdue ? 'Overdue' : $status;
                                    $statusCss = $isOverdue ? 'status-overdue' : $statusClass($status);
                                    $isAllocated = in_array($status, ['In-Service', 'Confirmed'], true);
                                    ?>
                                    <tr>
                                        <td class="px-3">
                                            <div><?php echo $escape($row['booking_time']); ?></div>
                                            <?php if ($isOverdue): ?>
                                                <small class="text-danger">Late <?php echo (int) $row['lateness_minutes']; ?>m</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $escape($row['customer_name']); ?></td>
                                        <td><?php echo $escape($row['beautician_name']); ?></td>
                                        <td><?php echo $escape($row['service_name']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $statusCss; ?>"><?php echo $escape($statusLabel); ?></span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <?php if ($isAllocated): ?>
                                                <button type="button" class="checkin-btn" disabled>Allocated (<?php echo $escape($row['seat_id']); ?>)</button>
                                            <?php else: ?>
                                                <button
                                                    type="button"
                                                    class="checkin-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#checkInModal"
                                                    data-res-id="<?php echo $escape($row['res_id']); ?>"
                                                    data-customer-name="<?php echo $escape($row['customer_name']); ?>"
                                                    data-date="<?php echo $escape($date); ?>">
                                                    Check-In &amp; Allocate
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center px-3 py-3 text-muted small border-top">
                    <div>Showing <?php echo count($reservations); ?> appointments</div>
                    <div>Today: <?php echo $escape(date('d M Y', strtotime($date))); ?></div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="checkInModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0 border-0">
                <form action="index.php?page=receptionist&action=checkInAllocate" method="post">
                    <input type="hidden" name="res_id" id="checkinResId">
                    <input type="hidden" name="date" id="checkinDate" value="<?php echo $escape($date); ?>">

                    <div class="modal-header">
                        <h5 class="modal-title">Check-In &amp; Alokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-3">Pelanggan: <strong id="checkinCustomerName">-</strong></p>

                        <div class="mb-3">
                            <label class="form-label">Pilih Kursi Salon (Pelanggan)</label>
                            <select class="form-select" name="salon_seat_id" required>
                                <option value="">Pilih kursi...</option>
                                <?php foreach ($availableSalonSeats as $seat): ?>
                                    <option value="<?php echo $escape($seat['seat_id']); ?>"><?php echo $escape($seat['seat_id'] . ' - ' . ($seat['seat_name'] ?? $seat['seat_id'])); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="hasCompanionCheck" name="has_companion">
                            <label class="form-check-label" for="hasCompanionCheck">Bawa Pendamping?</label>
                        </div>

                        <div class="companion-fields" id="companionFields">
                            <label class="form-label">Pilih Meja Kafe (Pendamping)</label>
                            <select class="form-select" id="loungeSeatSelect" name="lounge_seat_id">
                                <option value="">Pilih meja...</option>
                                <?php foreach ($availableLoungeSeats as $seat): ?>
                                    <option value="<?php echo $escape($seat['seat_id']); ?>"><?php echo $escape($seat['seat_id'] . ' - ' . ($seat['seat_name'] ?? $seat['seat_id'])); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="checkin-btn">Simpan Alokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const checkInModal = document.getElementById('checkInModal');
        if (checkInModal) {
            checkInModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const reservationId = button.getAttribute('data-res-id') || '';
                const customerName = button.getAttribute('data-customer-name') || '-';
                const dateValue = button.getAttribute('data-date') || '';

                const resInput = checkInModal.querySelector('#checkinResId');
                const customerText = checkInModal.querySelector('#checkinCustomerName');
                const dateInput = checkInModal.querySelector('#checkinDate');
                if (resInput) resInput.value = reservationId;
                if (customerText) customerText.textContent = customerName;
                if (dateInput && dateValue) dateInput.value = dateValue;
            });
        }

        const companionCheckbox = document.getElementById('hasCompanionCheck');
        const companionFields = document.getElementById('companionFields');
        const loungeSeatSelect = document.getElementById('loungeSeatSelect');

        function toggleCompanionFields() {
            if (!companionCheckbox || !companionFields || !loungeSeatSelect) return;
            const isChecked = companionCheckbox.checked;
            companionFields.style.display = isChecked ? 'block' : 'none';
            loungeSeatSelect.required = isChecked;
            if (!isChecked) {
                loungeSeatSelect.value = '';
            }
        }

        if (companionCheckbox) {
            companionCheckbox.addEventListener('change', toggleCompanionFields);
            toggleCompanionFields();
        }
    </script>
</body>
</html>


