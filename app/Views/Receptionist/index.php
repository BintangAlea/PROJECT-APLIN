ï»¿<?php
$activeAreaSeats = $activeAreaSeats ?? [];
$loungeSeats = $loungeSeats ?? [];
$seatOccupancy = $seatOccupancy ?? [];
$loungeQueue = $loungeQueue ?? [];
$availableTransferSeats = $availableTransferSeats ?? [];

$flashSuccess = $flashSuccess ?? null;
$flashError = $flashError ?? null;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$statusClass = static function (string $status): string {
    return match ($status) {
        'In-Service' => 'state-inservice',
        'Pending' => 'state-attention',
        default => 'state-empty',
    };
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Seat Map - Receptionist Merish</title>
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
            --inservice: #e7c7d0;
            --attention: #f5d8d6;
            --empty: #f8f2f3;
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
            letter-spacing: 0.3px;
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

        .new-booking:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .main {
            padding: 0;
        }

        .topbar {
            background: #f7f0f1;
            border-bottom: 1px solid var(--line);
            padding: 0.75rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .title {
            font-family: 'Playfair Display', serif;
            color: #4a3940;
            font-size: 2.2rem;
            margin: 0;
        }

        .search-box {
            width: min(360px, 100%);
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
            margin-left: 0.55rem;
            font-size: 0.9rem;
            color: #66575d;
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
            padding: 0.7rem 0.95rem;
        }

        .action-btn:hover {
            background: var(--accent-dark);
            color: #fff;
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

        .content {
            padding: 1.3rem 1.2rem 1.5rem;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            margin: 0;
            color: #30262b;
        }

        .subtitle {
            color: var(--muted);
            margin-top: 0.25rem;
            margin-bottom: 0;
            font-size: 0.92rem;
        }

        .legend {
            display: inline-flex;
            gap: 1rem;
            border: 1px solid #e1d2d6;
            padding: 0.45rem 0.75rem;
            font-size: 0.82rem;
            color: #725f67;
            background: #fff;
        }

        .dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            display: inline-block;
            margin-right: 0.3rem;
            border: 1px solid #cdbac1;
        }

        .dot-empty { background: var(--empty); }
        .dot-inservice { background: var(--inservice); }
        .dot-attention { background: var(--attention); }

        .zone-head {
            margin-top: 1.3rem;
            margin-bottom: 0.8rem;
            border-bottom: 1px solid #deced3;
            padding-bottom: 0.45rem;
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            color: #634f57;
            font-weight: 700;
        }

        .seat-grid {
            display: grid;
            gap: 0.85rem;
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .seat-card {
            background: #fff;
            border: 1px solid #e4d5d9;
            min-height: 168px;
            padding: 0.65rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .seat-card.state-empty {
            background: #faf6f7;
            color: #9a8b91;
        }

        .seat-card.state-inservice {
            border-top: 3px solid #c292a5;
        }

        .seat-card.state-attention {
            border-top: 3px solid #d36b66;
            background: #fff7f6;
        }

        .seat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.4rem;
        }

        .seat-id {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            line-height: 1;
        }

        .mini-badge {
            border: 1px solid #d9c6cf;
            background: #f5ebef;
            color: #7f6170;
            padding: 0.2rem 0.45rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .mini-badge.attention {
            border-color: #efc7c5;
            background: #fde9e8;
            color: #a84943;
        }

        .customer {
            font-weight: 600;
            color: #56444b;
            font-size: 0.95rem;
            line-height: 1.2;
        }

        .meta {
            color: #89787e;
            font-size: 0.78rem;
        }

        .transfer-btn {
            border: 1px solid #c7a8b4;
            background: #fff;
            color: #6e5560;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.68rem;
            padding: 0.42rem 0.55rem;
            font-weight: 700;
            margin-top: auto;
            width: 100%;
        }

        .transfer-btn:hover {
            background: #f3e5ea;
            color: #5d3f4d;
        }

        .empty-center {
            margin: auto;
            text-align: center;
            color: #9d8d93;
        }

        .empty-center .seat-id {
            font-size: 2.2rem;
            margin-bottom: 0.35rem;
            display: block;
        }

        .small-pill {
            background: #ebd4eb;
            color: #7a4e76;
            border-radius: 999px;
            padding: 0.08rem 0.45rem;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .alert {
            border-radius: 0;
            border: 0;
        }

        @media (max-width: 1200px) {
            .seat-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .seat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .seat-grid {
                grid-template-columns: 1fr;
            }
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
                <a class="nav-link active" href="index.php?page=receptionist">Seat Map</a>
                <a class="nav-link" href="index.php?page=receptionist&action=viewReservations">Appointments</a>
                <a class="nav-link" href="index.php?page=receptionist&action=viewOrders">Cashier</a>
            </nav>

            <a class="new-booking" href="index.php?page=receptionist&action=scheduleBooking">New Booking</a>
        </aside>

        <main class="main">
            <header class="topbar">
                <h1 class="title">Merish</h1>
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <div class="search-box rounded-0">
                        <span>âŒ•</span>
                        <input type="text" placeholder="Search customer...">
                    </div>
                    <button type="button" class="action-btn" data-bs-toggle="modal" data-bs-target="#walkInModal">Walk-In Check-In</button>
                    <button class="icon-btn" type="button">ðŸ””</button>
                    <button class="icon-btn" type="button">?</button>
                    <a class="icon-btn text-decoration-none" href="<?= LOGOUT_URL ?>">âŽ‹</a>
                </div>
            </header>

            <div class="content">
                <?php if ($flashSuccess): ?>
                    <div class="alert alert-success mb-3"><?php echo $escape($flashSuccess); ?></div>
                <?php endif; ?>
                <?php if ($flashError): ?>
                    <div class="alert alert-danger mb-3"><?php echo $escape($flashError); ?></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                    <div>
                        <h2 class="page-title">Live Seat Map</h2>
                        <p class="subtitle">Pusat Zonasi Â· Real-time occupancy status</p>
                    </div>
                    <div class="legend">
                        <span><i class="dot dot-empty"></i> Empty</span>
                        <span><i class="dot dot-inservice"></i> In-Service</span>
                        <span><i class="dot dot-attention"></i> Attention</span>
                    </div>
                </div>

                <div class="zone-head">Kursi Salon (Salon Seats)</div>
                <section class="seat-grid">
                    <?php foreach ($activeAreaSeats as $seat): ?>
                        <?php
                        $seatId = (string) ($seat['seat_id'] ?? '');
                        $seatLabel = (string) ($seat['seat_name'] ?? $seatId);
                        $occupancy = $seatOccupancy[$seatId] ?? null;
                        ?>
                        <?php if ($occupancy): ?>
                            <?php $status = (string) ($occupancy['STATUS'] ?? 'Pending'); ?>
                            <article class="seat-card <?php echo $statusClass($status); ?>">
                                <div class="seat-top">
                                    <div class="seat-id"><?php echo $escape(preg_replace('/[^0-9]/', '', $seatLabel) ?: $seatLabel); ?></div>
                                    <span class="mini-badge <?php echo $status === 'Pending' ? 'attention' : ''; ?>"><?php echo $escape($status); ?></span>
                                </div>
                                <div class="customer"><?php echo $escape($occupancy['customer_name']); ?></div>
                                <div class="meta"><?php echo $escape($occupancy['service_name']); ?></div>
                                <div class="meta">
                                    <?php
                                    $minutes = max(0, (int) ($occupancy['duration_minutes'] ?? 0));
                                    echo $minutes > 0 ? $minutes . ' min berjalan' : 'Baru check-in';
                                    ?>
                                </div>

                                <button
                                    type="button"
                                    class="transfer-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#transferModal"
                                    data-res-id="<?php echo $escape($occupancy['res_id']); ?>"
                                    data-customer-name="<?php echo $escape($occupancy['customer_name']); ?>">
                                    Transfer Seat
                                </button>
                            </article>
                        <?php else: ?>
                            <article class="seat-card state-empty">
                                <div class="empty-center">
                                    <span class="seat-id"><?php echo $escape(preg_replace('/[^0-9]/', '', $seatLabel) ?: $seatLabel); ?></span>
                                    <div>Available</div>
                                </div>
                            </article>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </section>

                <div class="zone-head" style="margin-top:1.8rem;">Meja Kafe (Cafe Tables)</div>
                <section class="seat-grid">
                    <?php for ($i = 0; $i < count($loungeSeats); $i++): ?>
                        <?php
                        $table = $loungeSeats[$i];
                        $queue = $loungeQueue[$i] ?? null;
                        $tableLabel = (string) ($table['seat_name'] ?? $table['seat_id'] ?? 'T-0' . ($i + 1));
                        ?>

                        <?php if ($queue): ?>
                            <article class="seat-card state-inservice">
                                <div class="seat-top">
                                    <div class="seat-id" style="font-size:1.5rem;"><?php echo $escape($tableLabel); ?></div>
                                    <span class="mini-badge">Queue</span>
                                </div>

                                <div class="customer"><?php echo $escape($queue['customer_name']); ?></div>
                                <div class="meta">Waiting lounge</div>
                                <div class="meta">Orders <span class="small-pill"><?php echo (int) ($queue['fnb_count'] ?? 0); ?></span></div>

                                <button
                                    type="button"
                                    class="transfer-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#transferModal"
                                    data-res-id="<?php echo $escape($queue['res_id']); ?>"
                                    data-customer-name="<?php echo $escape($queue['customer_name']); ?>">
                                    Transfer Seat
                                </button>
                            </article>
                        <?php else: ?>
                            <article class="seat-card state-empty">
                                <div class="empty-center">
                                    <span class="seat-id" style="font-size:1.7rem;"><?php echo $escape($tableLabel); ?></span>
                                    <div>Empty</div>
                                </div>
                            </article>
                        <?php endif; ?>
                    <?php endfor; ?>
                </section>
            </div>
        </main>
    </div>

    <div class="modal fade" id="walkInModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0 border-0">
                <form method="post" action="index.php?page=receptionist&action=walkInCheckIn">
                    <div class="modal-header">
                        <h5 class="modal-title">Walk-In Check-In</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" name="guest_name" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Arahkan ke</label>
                            <select class="form-select" name="destination" required>
                                <option value="salon">Kursi Salon (Langsung)</option>
                                <option value="cafe">Meja Kafe (Waiting is Earning)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="action-btn">Check-In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0 border-0">
                <form method="post" action="index.php?page=receptionist&action=transferSeat">
                    <input type="hidden" name="res_id" id="transferResId">
                    <div class="modal-header">
                        <h5 class="modal-title">Pindahkan Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Pelanggan: <strong id="transferCustomerName">-</strong></p>
                        <div class="mb-0">
                            <label class="form-label">Target Kursi Salon</label>
                            <select class="form-select" name="target_seat_id" required>
                                <option value="">Pilih kursi kosong</option>
                                <?php foreach ($availableTransferSeats as $seat): ?>
                                    <option value="<?php echo $escape($seat['seat_id']); ?>"><?php echo $escape($seat['seat_id'] . ' - ' . ($seat['seat_name'] ?? $seat['seat_id'])); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (empty($availableTransferSeats)): ?>
                            <div class="text-danger small mt-2">Tidak ada kursi kosong saat ini.</div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="action-btn" <?php echo empty($availableTransferSeats) ? 'disabled' : ''; ?>>Transfer Seat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const transferModal = document.getElementById('transferModal');
        if (transferModal) {
            transferModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const reservationId = button.getAttribute('data-res-id') || '';
                const customerName = button.getAttribute('data-customer-name') || '-';

                const resInput = transferModal.querySelector('#transferResId');
                const customerText = transferModal.querySelector('#transferCustomerName');
                if (resInput) resInput.value = reservationId;
                if (customerText) customerText.textContent = customerName;
            });
        }
    </script>
</body>
</html>



