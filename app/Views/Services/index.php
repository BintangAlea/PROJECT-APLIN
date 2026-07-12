<?php
$isLoggedIn = isset($_SESSION['user_id']);
$displayName = $_SESSION['full_name'] ?? $_SESSION['user_login'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Merish Services - Premium Salon & Artisan Cafe. Discover our signature treatments.">
    <title>Services - Merish Premium Salon & Artisan Cafe</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --soft-bg: #f3eded;
            --hero-tint: rgba(240, 234, 232, 0.72);
            --line: #e4dcdc;
            --ink: #5a4a51;
            --muted: #756a6f;
            --accent: #7a4f61;
            --accent-deep: #694352;
            --card-bg: #f7f4f4;
            --filter-bg: #f8f3f3;
        }

        html,
        body {
            background: var(--soft-bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .top-nav {
            background: #faf7f7;
            border-bottom: 1px solid #e7dfdf;
            min-height: 72px;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 600;
            line-height: 1;
            color: var(--accent);
            text-decoration: none;
        }

        .nav-link-custom {
            color: #5b4f54;
            text-transform: uppercase;
            letter-spacing: 1.9px;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.5rem 0.95rem;
            text-decoration: none;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active-link {
            color: var(--accent);
        }

        .signin-btn {
            color: #5f5358;
            border: 1px solid #cfc2c7;
            border-radius: 0;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 1.8px;
            font-weight: 600;
            padding: 0.5rem 1.3rem;
            background: transparent;
        }

        .signin-btn:hover {
            color: #fff;
            background: var(--accent);
            border-color: var(--accent);
        }

        .services-hero {
            min-height: 505px;
            display: flex;
            align-items: center;
            position: relative;
            border-bottom: 1px solid #ebe3e3;
            background-image:
                linear-gradient(var(--hero-tint), var(--hero-tint)),
                radial-gradient(circle at 10% 40%, rgba(255, 255, 255, 0.46) 0 16%, transparent 17%),
                linear-gradient(90deg, rgba(235, 228, 225, 0.85) 0 22%, rgba(0, 0, 0, 0) 22%),
                repeating-linear-gradient(
                    to right,
                    rgba(175, 160, 153, 0.16) 0 6px,
                    rgba(236, 230, 226, 0.08) 6px 16px
                ),
                linear-gradient(130deg, #e8ded8 0%, #dfd3ce 42%, #d8cbc5 100%);
            background-size: cover;
            background-position: center;
        }

        .hero-heading {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 4vw, 4rem);
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 0.8rem;
        }

        .hero-sub {
            color: #6f6669;
            font-size: 1.03rem;
            margin-bottom: 1.6rem;
        }

        .promo-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            border: 1px solid #ddcfd1;
            border-radius: 7px;
            background: rgba(251, 247, 247, 0.78);
            color: #7a606a;
            text-transform: uppercase;
            font-size: 0.69rem;
            letter-spacing: 1.8px;
            font-weight: 700;
            padding: 0.75rem 1.2rem;
        }

        .divider-band {
            height: 86px;
            border-bottom: 1px solid var(--line);
            background: #f6f0f0;
        }

        .filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin: 0 auto;
        }

        .filter-btn {
            border: 1px solid #d7cccf;
            background: var(--filter-bg);
            color: #62565b;
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.68rem;
            font-weight: 700;
            min-width: 72px;
            padding: 0.45rem 0.9rem;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .service-grid {
            padding: 3rem 0 3.3rem;
            background: #f4eded;
        }

        .service-card {
            border: 1px solid #e2d8d8;
            background: var(--card-bg);
            padding: 0.6rem;
            height: 100%;
        }

        .service-visual {
            height: 390px;
            border: 1px solid #d9cece;
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }

        .service-card:hover .service-visual {
            filter: grayscale(0%);
        }

        .visual-hair {
            background-image: url('assets/MERISH_PICTURES/services/signature balayage.jpg');
        }

        .visual-nails {
            background-image: url('assets/MERISH_PICTURES/services/editorial manicure.jpg');
        }

        .visual-lashes {
            background-image: url('assets/MERISH_PICTURES/services/volume lash extensions.jpg');
        }

        .service-body {
            padding: 1.25rem 0.2rem 0.1rem;
            text-align: center;
        }

        .service-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.05rem;
            color: #6f4e5f;
            margin-bottom: 0.5rem;
            line-height: 1.15;
        }

        .service-price {
            color: #8a7d82;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .book-btn {
            width: 100%;
            border-radius: 0;
            border: none;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.72rem;
        }

        .book-btn:hover {
            background: var(--accent-deep);
            color: #fff;
        }

        .experts-section {
            background: #ece5e5;
            padding: 4.8rem 0 4.2rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 4vw, 4rem);
            color: var(--accent);
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.6rem;
        }

        .section-sub {
            text-align: center;
            color: #766a6f;
            margin-bottom: 2.6rem;
        }

        .expert-card {
            text-align: center;
            padding: 0.25rem 1.2rem;
        }

        .expert-photo {
            width: 188px;
            height: 188px;
            margin: 0 auto 1.1rem;
            border-radius: 12px;
            border: 3px solid #ece4e4;
            background-size: cover;
            background-position: center;
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }

        .expert-card:hover .expert-photo {
            filter: grayscale(0%);
        }

        .expert-elena {
            background-image: url('assets/MERISH_PICTURES/experts/Sarah Roberts.webp');
        }

        .expert-marcus {
            background-image: url('assets/MERISH_PICTURES/experts/Niki Rose.webp');
        }

        .expert-chloe {
            background-image: url('assets/MERISH_PICTURES/experts/Ambar Eliyah.webp');
        }

        .expert-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #714f5e;
            margin-bottom: 0.35rem;
            line-height: 1.1;
        }

        .expert-role {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #7f7176;
            margin-bottom: 0.55rem;
        }

        .expert-stars {
            color: #7a5a68;
            letter-spacing: 4px;
            font-size: 0.78rem;
            margin-bottom: 0.85rem;
        }

        .expert-copy {
            color: #6f6569;
            font-size: 0.97rem;
            line-height: 1.65;
            max-width: 300px;
            margin: 0 auto;
        }

        .site-footer {
            background: #f8f3f3;
            border-top: 1px solid #dfd4d4;
            padding: 2.3rem 0;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: #76525f;
            line-height: 1;
            text-decoration: none;
        }

        .footer-links {
            display: flex;
            gap: 1.45rem;
            flex-wrap: wrap;
        }

        .footer-links a,
        .footer-copy {
            color: #756a6f;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        @media (max-width: 991.98px) {
            .brand {
                font-size: 2.25rem;
            }

            .services-hero {
                min-height: 420px;
            }

            .service-visual {
                height: 320px;
            }

            .footer-links {
                margin: 0.9rem 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg top-nav sticky-top">
        <div class="container">
            <a class="brand" href="index.php?page=home">Merish</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#servicesNav" aria-controls="servicesNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="servicesNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link-custom" href="index.php?page=home">Home</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="index.php?page=cafe">The Cafe</a></li>
                    <li class="nav-item"><a class="nav-link-custom active-link" href="index.php?page=services">Services</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="index.php?page=booking&step=1">Book Now</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isLoggedIn): ?>
                        <span class="me-3 mb-0" style="color: #5f5358; font-size: 0.8rem; letter-spacing: 1.2px; text-transform: uppercase; font-weight: 700;">Hi, <?php echo htmlspecialchars(substr($displayName, 0, 14)); ?></span>
                        <a href="#" class="nav-link-custom mb-0 text-decoration-underline" data-bs-toggle="modal" data-bs-target="#historyModal" style="cursor: pointer; font-weight: 600;">History</a>
                        <form method="POST" action="<?= LOGOUT_URL ?>" class="m-0">
                            <button class="btn btn-book py-2 px-3" type="submit" style="background: var(--accent); border-color: var(--accent); color: #fff; border-radius: 0; text-transform: uppercase; letter-spacing: 1.4px; font-size: 0.72rem; font-weight: 600; padding: 0.5rem 1rem;">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="index.php?page=login" class="nav-link-custom mb-0">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <header class="services-hero">
        <div class="container text-center">
            <h1 class="hero-heading">Our Signature Treatments</h1>
            <p class="hero-sub">Curated services designed to elevate your personal style with professional precision.</p>
            <div class="promo-pill">
                <span></span>
                <span>Enjoy a 20% synergy discount on combined services</span>
            </div>
        </div>
    </header>



    <section class="service-grid">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 service-item" data-category="hair">
                    <article class="service-card">
                        <div class="service-visual visual-hair"></div>
                        <div class="service-body">
                            <h3 class="service-title">Signature Balayage</h3>
                            <p class="service-price">Starting from Rp 850.000</p>
                            <button class="btn book-btn" type="button" onclick="bookService('SV01', 'Signature Balayage')">Book Now</button>
                        </div>
                    </article>
                </div>

                <div class="col-lg-4 col-md-6 service-item" data-category="nails">
                    <article class="service-card">
                        <div class="service-visual visual-nails"></div>
                        <div class="service-body">
                            <h3 class="service-title">Editorial Manicure</h3>
                            <p class="service-price">Starting from Rp 350.000</p>
                            <button class="btn book-btn" type="button" onclick="bookService('SV03', 'Editorial Manicure')">Book Now</button>
                        </div>
                    </article>
                </div>

                <div class="col-lg-4 col-md-6 service-item" data-category="lashes">
                    <article class="service-card">
                        <div class="service-visual visual-lashes"></div>
                        <div class="service-body">
                            <h3 class="service-title">Volume Lash Extensions</h3>
                            <p class="service-price">Starting from Rp 550.000</p>
                            <button class="btn book-btn" type="button" onclick="bookService('SV05', 'Volume Lash Extensions')">Book Now</button>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="experts-section">
        <div class="container">
            <h2 class="section-title">Meet the Experts</h2>
            <p class="section-sub">Our top-rated professionals dedicated to elevating your experience.</p>

            <div class="row g-4 mt-1">
                <div class="col-lg-4 col-md-6">
                    <article class="expert-card">
                        <div class="expert-photo expert-elena"></div>
                        <h3 class="expert-name">Sarah Roberts</h3>
                        <p class="expert-role">COLOR & HAIR DIRECTOR</p>
                        <div class="expert-stars"></div>
                        <p class="expert-copy">Master of dimensional color and balayage techniques with over a decade of high-fashion experience.</p>
                    </article>
                </div>

                <div class="col-lg-4 col-md-6">
                    <article class="expert-card">
                        <div class="expert-photo expert-marcus"></div>
                        <h3 class="expert-name">Niki Rose</h3>
                        <p class="expert-role">SENIOR NAILIST</p>
                        <div class="expert-stars"></div>
                        <p class="expert-copy">Precision nail specialist known for creating flawless, structured extensions and elegant designs tailored to each individual.</p>
                    </article>
                </div>

                <div class="col-lg-4 col-md-6">
                    <article class="expert-card">
                        <div class="expert-photo expert-chloe"></div>
                        <h3 class="expert-name">Ambar Eliyah</h3>
                        <p class="expert-role">SENIOR LASH TECHNICIAN</p>
                        <div class="expert-stars"></div>    
                        <p class="expert-copy">Specializing in advanced lash extensions and meticulous lash mapping to enhance natural beauty.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-lg-3">
                    <a class="footer-brand" href="index.php?page=home">Merish</a>
                </div>
                <div class="col-lg-5">
                    <nav class="footer-links">
                        <a href="#">Contact</a>
                        <a href="#">Location</a>
                        <a href="#">Instagram</a>
                        <a href="#">Pinterest</a>
                    </nav>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <span class="footer-copy">&copy; 2026 Merish Beauty & Cafe. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        function bookService(serviceId, serviceName) {
            const params = new URLSearchParams({
                page: 'booking',
                step: '1',
                service_id: serviceId,
                service: serviceName
            });
            window.location.href = 'index.php?' + params.toString();
        }
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
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show border-0 rounded-0 mb-3 small" role="alert">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <?php unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-0 mb-3 small" role="alert">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
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
                        .star-rating-input {
                            display: flex;
                            gap: 0.35rem;
                            direction: ltr;
                        }
                        .star-rating-input .star-item {
                            font-size: 1.75rem;
                            color: #ccc;
                            cursor: pointer;
                            transition: color 0.15s ease-in-out;
                            user-select: none;
                        }
                        .star-rating-input .star-item.active {
                            color: #ffc107;
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
                                    // Do not subtract discount for total in history. Directly subtract DP 50.000.
                                    $finalTotal = $subtotal;
                                    $remainingDue = max(0, $finalTotal - 50000);
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
                                                                <span>Free Item</span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="d-flex justify-content-between fw-semibold border-top pt-1 mb-1">
                                                            <span>Total Tagihan:</span>
                                                            <span>Rp <?php echo number_format($finalTotal, 0, ',', '.'); ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between text-muted mb-1">
                                                            <span>DP Telah Dibayar (<?php echo $res['is_dp_paid'] ? 'Lunas' : 'Belum Lunas'; ?>):</span>
                                                            <span>Rp 50.000</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between border-top fw-bold pt-1 text-danger">
                                                            <span>Sisa Harus Dibayar di Salon:</span>
                                                            <span>Rp <?php echo number_format($remainingDue, 0, ',', '.'); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Review / Rating Section -->
                                        <?php if ($statusLower === 'selesai'): ?>
                                            <?php if (!empty($res['review_id'])): ?>
                                                <div class="mt-3 p-2 bg-light border border-dashed rounded small">
                                                    <div class="fw-semibold text-accent mb-1">
                                                        Rating Anda: 
                                                        <span style="color: #ffc107; font-size: 0.9rem;">
                                                            <?php echo str_repeat('★', (int)$res['rating']); ?><?php echo str_repeat('☆', 5 - (int)$res['rating']); ?>
                                                        </span>
                                                    </div>
                                                    <?php if (!empty($res['review_comment'])): ?>
                                                        <div class="text-muted italic">"<?php echo htmlspecialchars($res['review_comment']); ?>"</div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-3 text-end">
                                                    <button class="btn btn-sm btn-outline-accent" type="button" data-bs-toggle="collapse" data-bs-target="#reviewForm-<?php echo $resId; ?>" style="font-size: 0.75rem; font-weight: 600; border-radius: 0; color: var(--accent); border-color: var(--accent);">
                                                        Beri Ulasan
                                                    </button>
                                                </div>
                                                <div class="collapse mt-2" id="reviewForm-<?php echo $resId; ?>">
                                                    <form action="index.php?page=booking&action=addReview" method="post" class="p-3 bg-light border rounded">
                                                        <input type="hidden" name="res_id" value="<?php echo $resId; ?>">
                                                        <input type="hidden" name="redirect" value="index.php?page=services">
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-semibold mb-1">Rating:</label>
                                                            <div class="star-rating-input" data-res-id="<?php echo $resId; ?>">
                                                                <span class="star-item" data-val="1">★</span>
                                                                <span class="star-item" data-val="2">★</span>
                                                                <span class="star-item" data-val="3">★</span>
                                                                <span class="star-item" data-val="4">★</span>
                                                                <span class="star-item" data-val="5">★</span>
                                                            </div>
                                                            <input type="hidden" name="rating" id="ratingInput-<?php echo $resId; ?>" value="">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-semibold mb-1">Komentar / Feedback (Opsional):</label>
                                                            <textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Tulis masukan Anda di sini..." style="border-radius: 0; font-size: 0.8rem;"></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm w-100 text-white" style="background-color: var(--accent); border-radius: 0; font-size: 0.8rem; font-weight: 600;">Kirim Ulasan</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
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
                                            <?php 
                                            $detailsSubtotal = 0;
                                            foreach ($order['details'] as $det) {
                                                $detailsSubtotal += (float)$det['subtotal'];
                                            }
                                            $hasTax = ($order['total_amount'] > $detailsSubtotal);
                                            $calculatedTax = (int) round($detailsSubtotal * 0.1);
                                            ?>
                                            <?php foreach ($order['details'] as $det): ?>
                                                <div class="d-flex justify-content-between small">
                                                    <span><?php echo htmlspecialchars($det['menu_name']); ?> x<?php echo (int)$det['qty']; ?></span>
                                                    <span>Rp <?php echo number_format($det['subtotal'], 0, ',', '.'); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if ($hasTax): ?>
                                                <div class="d-flex justify-content-between small text-muted border-top pt-1 mt-1">
                                                    <span>Tax & Service (10%)</span>
                                                    <span>Rp <?php echo number_format($calculatedTax, 0, ',', '.'); ?></span>
                                                </div>
                                            <?php endif; ?>
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
    <script>
    document.querySelectorAll('.star-rating-input').forEach(function(container) {
        const resId = container.getAttribute('data-res-id');
        const input = document.getElementById('ratingInput-' + resId);
        const stars = container.querySelectorAll('.star-item');
        const form = container.closest('form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!input.value || input.value === "") {
                    e.preventDefault();
                    alert('Silakan pilih rating bintang terlebih dahulu.');
                }
            });
        }
        
        stars.forEach(function(star) {
            star.addEventListener('mouseover', function() {
                const hoverVal = parseInt(this.getAttribute('data-val'));
                stars.forEach(function(s) {
                    const sVal = parseInt(s.getAttribute('data-val'));
                    if (sVal <= hoverVal) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            container.addEventListener('mouseleave', function() {
                const activeVal = input.value ? parseInt(input.value) : 0;
                stars.forEach(function(s) {
                    const sVal = parseInt(s.getAttribute('data-val'));
                    if (sVal <= activeVal) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            star.addEventListener('click', function() {
                const clickVal = parseInt(this.getAttribute('data-val'));
                input.value = clickVal;
                stars.forEach(function(s) {
                    const sVal = parseInt(s.getAttribute('data-val'));
                    if (sVal <= clickVal) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    });
    </script>
    <?php endif; ?>
    </script>
</body>
</html>

