<?php
$isLoggedIn = isset($_SESSION['user_id']);
$displayName = $_SESSION['full_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merish - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-soft: #f2ebeb;
            --bg-hero: #d8cac2;
            --hero-overlay: rgba(210, 196, 186, 0.56);
            --ink: #4c3d44;
            --ink-muted: #6f6367;
            --accent: #7b4e61;
            --accent-dark: #684051;
            --card-bg: #fbf9f9;
            --card-border: #e9e1e1;
        }

        body {
            margin: 0;
            background: var(--bg-soft);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 600;
            color: var(--accent);
            letter-spacing: 0.5px;
            text-decoration: none;
        }

        .top-nav {
            background: #f8f4f4;
            border-bottom: 1px solid #e7dede;
            min-height: 72px;
        }

        .top-nav .nav-link {
            color: #55484d;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            font-size: 0.72rem;
            font-weight: 500;
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }

        .top-nav .nav-link:hover,
        .top-nav .nav-link:focus {
            color: var(--accent);
        }

        .hero-wrap {
            position: relative;
            min-height: 680px;
            background-image:
                linear-gradient(var(--hero-overlay), var(--hero-overlay)),
                radial-gradient(circle at 5% 50%, rgba(255, 255, 255, 0.48) 0 13%, transparent 14%),
                radial-gradient(circle at 95% 40%, rgba(255, 255, 255, 0.45) 0 12%, transparent 13%),
                repeating-linear-gradient(
                    to right,
                    rgba(120, 103, 96, 0.12) 0 6px,
                    rgba(229, 220, 213, 0.06) 6px 16px
                ),
                linear-gradient(130deg, #d7cbc4 0%, #d2c0b3 45%, #c9b4a8 100%);
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
        }

        .hero-panel {
            max-width: 780px;
            margin: 0 auto;
            text-align: center;
            padding: 1rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2.1rem, 4vw, 3.85rem);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            color: #5f5258;
            max-width: 700px;
            margin: 0 auto 2rem;
            font-weight: 400;
            line-height: 1.8;
        }

        .btn-book {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.9rem 2.5rem;
        }

        .btn-book:hover,
        .btn-book:focus {
            background: var(--accent-dark);
            border-color: var(--accent-dark);
            color: #fff;
        }

        .cert-section {
            background: var(--bg-soft);
            padding: 5.5rem 0;
        }

        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 2.6vw, 2.8rem);
            font-weight: 500;
            text-align: center;
            color: #68495a;
            margin-bottom: 3rem;
            line-height: 1.35;
        }

        .cert-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            padding: 2.2rem 1.4rem;
            text-align: center;
            min-height: 180px;
        }

        .cert-icon {
            color: #c792a2;
            font-size: 1.35rem;
            margin-bottom: 1rem;
        }

        .cert-card h3 {
            font-size: 0.7rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
            color: #6b565f;
            margin-bottom: 0.8rem;
        }

        .cert-card p {
            font-size: 0.9rem;
            color: #6f666a;
            margin-bottom: 0;
        }

        .site-footer {
            border-top: 1px solid #e1d8d8;
            padding: 2.5rem 0;
            background: #f8f4f4;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 3.1rem;
            color: #714f5e;
            line-height: 1;
        }

        .copyright {
            margin-top: 0.55rem;
            font-size: 0.85rem;
            color: #7f7478;
        }

        .footer-links {
            display: flex;
            justify-content: flex-end;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #655b5f;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .footer-links a:hover,
        .footer-links a:focus {
            color: var(--accent);
        }

        @media (max-width: 991.98px) {
            .brand {
                font-size: 2.25rem;
            }

            .hero-wrap {
                min-height: 560px;
            }

            .footer-links {
                justify-content: flex-start;
                margin-top: 1rem;
                gap: 1.15rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg top-nav sticky-top">
        <div class="container">
            <a class="brand" href="index.php?page=home">Merish</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php?page=home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=cafe">The Cafe</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=booking&step=1">Book Now</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isLoggedIn): ?>
                        <span class="nav-link mb-0">Hi, <?php echo htmlspecialchars(substr($displayName, 0, 14)); ?></span>
                        <a href="#" class="nav-link mb-0 text-decoration-underline" data-bs-toggle="modal" data-bs-target="#historyModal" style="cursor: pointer; font-weight: 600; text-transform: uppercase; letter-spacing: 1.2px; font-size: 0.72rem;">History</a>
                        <form method="POST" action="index.php?page=login&action=logout" class="m-0">
                            <button class="btn btn-book py-2 px-3" type="submit">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="index.php?page=login" class="nav-link mb-0">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-wrap">
        <div class="container">
            <div class="hero-panel">
                <h1 class="hero-title">Elevate Your Aesthetic</h1>
                <p class="hero-subtitle">
                    Experience editorial sophistication in every detail. A curated sanctuary for
                    professional beauty services and mindful relaxation.
                </p>
                <a href="index.php?page=booking&step=1" class="btn btn-book">Book an Appointment</a>
            </div>
        </div>
    </header>

    <section class="cert-section">
        <div class="container">
            <h2 class="cert-title">Recognized Excellence<br>Sertifikasi</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <article class="cert-card h-100">
                        <div class="cert-icon">✪</div>
                        <h3>Voted Best Salon</h3>
                        <p>Recognized for unparalleled service and editorial styling.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="cert-card h-100">
                        <div class="cert-icon">✿</div>
                        <h3>Certified Master Colorists</h3>
                        <p>Our team holds advanced certifications in modern color techniques.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="cert-card h-100">
                        <div class="cert-icon">◌</div>
                        <h3>Organic & Sustainable</h3>
                        <p>Committed to using premium, eco-conscious products.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">
            <div class="row align-items-end g-3">
                <div class="col-lg-6">
                    <div class="footer-brand">Merish</div>
                    <div class="copyright">&copy; 2026 Merish Beauty & Cafe. All rights reserved.</div>
                </div>
                <div class="col-lg-6">
                    <div class="footer-links">
                        <a href="#">Contact</a>
                        <a href="#">Location</a>
                        <a href="#">Instagram</a>
                        <a href="#">Pinterest</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php if ($isLoggedIn): ?>
    <!-- History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 0; border: 1px solid #e1d8d8; background-color: #fffaf9;">
                <div class="modal-header" style="border-bottom: 1px solid #eadedf; background-color: #f7ecea;">
                    <h5 class="modal-title" id="historyModalLabel" style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 600;">Riwayat Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="color: var(--ink);">
                    <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-2" id="historyTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-4 py-2" id="salon-tab" data-bs-toggle="pill" data-bs-target="#salon-history" type="button" role="tab" aria-controls="salon-history" aria-selected="true" style="font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; border-radius: 0; border: 1px solid #7b4e61;">Salon History</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-4 py-2" id="cafe-tab" data-bs-toggle="pill" data-bs-target="#cafe-history" type="button" role="tab" aria-controls="cafe-history" aria-selected="false" style="font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; border-radius: 0; border: 1px solid #7b4e61;">Cafe History</button>
                        </li>
                    </ul>
                    
                    <style>
                        #historyModal .nav-pills .nav-link {
                            color: #7b4e61;
                            background: transparent;
                        }
                        #historyModal .nav-pills .nav-link.active {
                            color: #fff;
                            background-color: #7b4e61;
                        }
                        .history-card-item {
                            background: #fff;
                            border: 1px solid #eadedf;
                            margin-bottom: 1rem;
                            padding: 1.25rem;
                            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
                        }
                        .history-badge {
                            font-size: 0.7rem;
                            font-weight: 700;
                            text-transform: uppercase;
                            letter-spacing: 0.8px;
                            padding: 0.3rem 0.6rem;
                            border-radius: 2px;
                        }
                        .badge-pending { background: #fdf5e6; color: #b8860b; }
                        .badge-confirmed { background: #e6f2ff; color: #0066cc; }
                        .badge-inservice { background: #eafaf1; color: #2e7d32; }
                        .badge-selesai { background: #eafaf1; color: #2e7d32; }
                        .badge-completed { background: #eafaf1; color: #2e7d32; }
                        .badge-ready { background: #eafaf1; color: #2e7d32; }
                        .badge-canceled { background: #ffebee; color: #c62828; }
                        .badge-new { background: #f3e5f5; color: #7b1fa2; }
                        .badge-inprogress { background: #e8f5e9; color: #2e7d32; }
                    </style>

                    <div class="tab-content" id="historyTabContent">
                        <!-- Salon History Sector -->
                        <div class="tab-pane fade show active" id="salon-history" role="tabpanel" aria-labelledby="salon-tab">
                            <?php if (empty($salonHistory)): ?>
                                <div class="text-center py-4 text-muted">Belum ada riwayat pemesanan salon.</div>
                            <?php else: ?>
                                <?php foreach ($salonHistory as $res): ?>
                                    <?php
                                    $resId = (int)$res['res_id'];
                                    $statusLower = strtolower($res['status']);
                                    $badgeClass = match($statusLower) {
                                        'pending' => 'badge-pending',
                                        'confirmed' => 'badge-confirmed',
                                        'in-service' => 'badge-inservice',
                                        'selesai' => 'badge-selesai',
                                        'canceled' => 'badge-canceled',
                                        default => 'badge-pending'
                                    };
                                    
                                    // Calculate total
                                    $subtotal = 0;
                                    foreach ($res['details'] as $det) {
                                        $subtotal += (float)$det['subtotal'];
                                    }
                                    
                                    $discount = 0;
                                    if (!empty($res['discount_value'])) {
                                        $discount = (float)$res['discount_value'];
                                    }
                                    $finalTotal = max(0, $subtotal - $discount);
                                    $remainingDue = max(0, $finalTotal - (float)$res['dp_amount']);
                                    ?>
                                    <div class="history-card-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-0" style="font-weight: 600; font-family: 'Playfair Display', serif;">Appointment #SR-<?php echo $resId; ?></h6>
                                                <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($res['schedule_time'])); ?> | Seat: <?php echo htmlspecialchars($res['seat_name'] ?? '-'); ?></small>
                                            </div>
                                            <span class="history-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($res['status']); ?></span>
                                        </div>
                                        <div class="border-top border-bottom py-2 my-2">
                                            <div class="small fw-semibold text-muted mb-1">Layanan / Services:</div>
                                            <?php foreach ($res['details'] as $det): ?>
                                                <div class="d-flex justify-content-between small">
                                                    <span><?php echo htmlspecialchars($det['service_name']); ?></span>
                                                    <span>Rp <?php echo number_format($det['base_tariff'], 0, ',', '.'); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <!-- Accordion for Invoice Details -->
                                        <div class="accordion accordion-flush" id="invoiceAccordion-<?php echo $resId; ?>">
                                            <div class="accordion-item" style="border: 0; background: transparent;">
                                                <h2 class="accordion-header" id="invoiceHead-<?php echo $resId; ?>">
                                                    <button class="accordion-button collapsed p-0 py-2 small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#invoiceCollapse-<?php echo $resId; ?>" aria-expanded="false" aria-controls="invoiceCollapse-<?php echo $resId; ?>" style="background: transparent; color: var(--accent); box-shadow: none; font-size: 0.8rem;">
                                                        Lihat Invoice / Detail Biaya
                                                    </button>
                                                </h2>
                                                <div id="invoiceCollapse-<?php echo $resId; ?>" class="accordion-collapse collapse" aria-labelledby="invoiceHead-<?php echo $resId; ?>" data-bs-parent="#invoiceAccordion-<?php echo $resId; ?>">
                                                    <div class="accordion-body p-0 pt-2 small">
                                                        <div class="d-flex justify-content-between text-muted mb-1">
                                                            <span>Subtotal Layanan:</span>
                                                            <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                                                        </div>
                                                        <?php if ($discount > 0): ?>
                                                            <div class="d-flex justify-content-between text-success mb-1">
                                                                <span>Promo (<?php echo htmlspecialchars($res['promo_name']); ?>):</span>
                                                                <span>-Rp <?php echo number_format($discount, 0, ',', '.'); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="d-flex justify-content-between fw-semibold border-top pt-1 mb-1">
                                                            <span>Total Tagihan:</span>
                                                            <span>Rp <?php echo number_format($finalTotal, 0, ',', '.'); ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between text-muted mb-1">
                                                            <span>DP Telah Dibayar (<?php echo $res['is_dp_paid'] ? 'Lunas' : 'Belum Lunas'; ?>):</span>
                                                            <span>Rp <?php echo number_format($res['dp_amount'], 0, ',', '.'); ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between border-top fw-bold pt-1 text-danger">
                                                            <span>Sisa Harus Dibayar di Salon:</span>
                                                            <span>Rp <?php echo number_format($remainingDue, 0, ',', '.'); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Cafe History Sector -->
                        <div class="tab-pane fade" id="cafe-history" role="tabpanel" aria-labelledby="cafe-tab">
                            <?php if (empty($cafeHistory)): ?>
                                <div class="text-center py-4 text-muted">Belum ada riwayat pesanan cafe.</div>
                            <?php else: ?>
                                <?php foreach ($cafeHistory as $order): ?>
                                    <?php
                                    $orderId = (int)$order['order_id'];
                                    $statusLower = strtolower($order['status']);
                                    $badgeClass = match($statusLower) {
                                        'new' => 'badge-new',
                                        'in progress' => 'badge-inprogress',
                                        'ready' => 'badge-ready',
                                        'completed' => 'badge-completed',
                                        default => 'badge-new'
                                    };
                                    
                                    // Determine waiting / processing message
                                    $isProcessing = in_array($order['status'], ['New', 'In Progress']);
                                    $processText = $isProcessing ? 'Barista is still making your drinks (Sedang Diproses)' : 'Pesanan Selesai / Siap Diambil';
                                    $processColor = $isProcessing ? 'text-warning fw-semibold' : 'text-success fw-semibold';
                                    ?>
                                    <div class="history-card-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-0" style="font-weight: 600; font-family: 'Playfair Display', serif;">Order #CF-<?php echo $orderId; ?></h6>
                                                <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?> | Seat/Table: <?php echo htmlspecialchars($order['seat_id'] ?? 'Pick Up'); ?></small>
                                            </div>
                                            <span class="history-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                                        </div>
                                        <div class="border-top border-bottom py-2 my-2">
                                            <div class="small fw-semibold text-muted mb-1">F&B Items:</div>
                                            <?php foreach ($order['details'] as $det): ?>
                                                <div class="d-flex justify-content-between small">
                                                    <span><?php echo htmlspecialchars($det['menu_name']); ?> x<?php echo (int)$det['qty']; ?></span>
                                                    <span>Rp <?php echo number_format($det['subtotal'], 0, ',', '.'); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="d-flex justify-content-between small mb-2">
                                            <span class="text-muted">Total Pembayaran:</span>
                                            <span class="fw-bold">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> (<?php echo htmlspecialchars($order['payment_status']); ?>)</span>
                                        </div>
                                        <div class="border-top pt-2 small">
                                            <span class="text-muted">Status Proses:</span>
                                            <span class="<?php echo $processColor; ?> d-block mt-1"><?php echo $processText; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #eadedf;">
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 0; background: #8a7c80; border-color: #8a7c80; text-transform: uppercase; font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
