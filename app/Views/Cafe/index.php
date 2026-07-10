<?php
$isLoggedIn = isset($_SESSION['user_id']);
$displayName = $_SESSION['full_name'] ?? $_SESSION['user_login'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Merish The Cafe - Artisan Beverages and pastries.">
    <title>The Cafe - Merish Premium Salon & Artisan Cafe</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --page-bg: #f3eeee;
            --line: #dfd8d8;
            --ink: #5e4e55;
            --ink-soft: #71666b;
            --accent: #7b4f61;
            --accent-deep: #653f4f;
            --panel: #f8f4f4;
            --card-bg: #fbf9f9;
        }

        html,
        body {
            margin: 0;
            background: var(--page-bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .top-nav {
            background: #faf7f7;
            border-bottom: 1px solid #e5dddd;
            min-height: 72px;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: var(--accent);
            line-height: 1;
            text-decoration: none;
            font-weight: 600;
        }

        .nav-link-custom {
            text-transform: uppercase;
            letter-spacing: 1.8px;
            font-size: 0.71rem;
            font-weight: 600;
            color: #605357;
            padding: 0.5rem 0.9rem;
            text-decoration: none;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active-link {
            color: var(--accent);
        }

        .signin-link {
            text-transform: uppercase;
            letter-spacing: 1.8px;
            font-size: 0.72rem;
            font-weight: 600;
            color: #5f5258;
            text-decoration: none;
        }

        .hero-area {
            padding: 2.3rem 0 1.4rem;
        }

        .greeting-box {
            max-width: 375px;
            margin-top: 0.35rem;
        }

        .avatar-chip {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cdc4c6;
            color: #5a4b51;
            font-size: 1.05rem;
            background: #f7f3f3;
            margin-right: 0.45rem;
        }

        .greet-line {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin: 0;
            color: #4f4147;
        }

        .location-pill {
            margin-top: 0.95rem;
            border: 1px solid #d9d0d0;
            background: #f4f1f1;
            border-radius: 7px;
            padding: 0.36rem 0.68rem;
            font-size: 0.7rem;
            color: #7b6f73;
            display: inline-block;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.2vw, 4.4rem);
            font-weight: 600;
            line-height: 1.06;
            color: #4f4148;
            margin-bottom: 0.72rem;
        }

        .hero-sub {
            color: #72686b;
            max-width: 500px;
            font-size: 0.98rem;
            line-height: 1.65;
        }

        .category-tabs {
            margin-top: 0.8rem;
            border-bottom: 1px solid #cfc4c7;
            display: flex;
            gap: 2.1rem;
            flex-wrap: wrap;
            padding-bottom: 0.42rem;
        }

        .tab-btn {
            border: none;
            background: transparent;
            padding: 0 0 0.42rem;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-size: 0.66rem;
            font-weight: 700;
            color: #665b60;
            position: relative;
        }

        .tab-btn.active::after,
        .tab-btn:hover::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -7px;
            height: 2px;
            background: #6d4758;
        }

        .menu-section {
            padding: 1.45rem 0 2.3rem;
        }

        .menu-card {
            background: var(--card-bg);
            border: 1px solid #ddd4d4;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 9px 24px rgba(43, 30, 37, 0.08);
        }

        .menu-image {
            height: 360px;
            border-bottom: 1px solid #ddd2d2;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #ffffff;
        }

        .drink-latte {
            background-image:
                linear-gradient(180deg, rgba(111, 71, 66, 0.3), rgba(52, 35, 35, 0.2)),
                linear-gradient(120deg, #9d6d62 0%, #d8c2b7 45%, #b8c0c6 100%);
        }

        .drink-cappuccino {
            background-image:
                radial-gradient(circle at 50% 40%, #e6d2b9 0 18%, transparent 19%),
                linear-gradient(130deg, #cdb79c 0%, #cabba6 55%, #9d8f82 100%);
        }

        .drink-earlgrey {
            background-image:
                linear-gradient(90deg, #aeb1b0 0 50%, #c2c6c4 50% 100%),
                linear-gradient(130deg, #bbb7ae 0%, #b2aca2 100%);
        }

        .food-croissant {
            background-image:
                radial-gradient(circle at 42% 68%, #be8f59 0 22%, transparent 23%),
                linear-gradient(130deg, #9f8f83 0%, #d4c4b2 65%, #bfa892 100%);
        }

        .menu-body {
            padding: 0.8rem 0.72rem 0.95rem;
        }

        .title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            margin-bottom: 0.42rem;
        }

        .menu-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            color: #4f4047;
            line-height: 1.08;
            margin: 0;
        }

        .menu-price {
            color: #5e5357;
            font-size: 0.86rem;
            margin: 0;
            white-space: nowrap;
            font-weight: 500;
        }

        .menu-desc {
            color: #766d70;
            font-size: 0.77rem;
            line-height: 1.48;
            margin: 0;
        }

        .order-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            padding: 1.55rem 0 2.8rem;
        }

        .order-wrap .container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .order-btn {
            border: none;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 0;
            min-width: 168px;
            padding: 0.9rem 1.6rem;
        }

        .order-btn:hover {
            background: var(--accent-deep);
            color: #fff;
        }

        .site-footer {
            background: var(--panel);
            border-top: 1px solid #ddd2d2;
            padding: 1.65rem 0;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            line-height: 1;
            color: #744f5e;
            text-decoration: none;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.35rem;
        }

        .footer-links a,
        .footer-copy {
            color: #74696e;
            font-size: 0.84rem;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        @media (max-width: 991.98px) {
            .brand {
                font-size: 2.2rem;
            }

            .hero-area {
                padding-top: 1.6rem;
            }

            .hero-title {
                margin-top: 1.15rem;
            }

            .footer-links {
                justify-content: flex-start;
                margin: 0.6rem 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg top-nav sticky-top">
        <div class="container">
            <a class="brand" href="index.php?page=home">Merish</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cafeNav" aria-controls="cafeNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="cafeNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link-custom" href="index.php?page=home">Home</a></li>
                    <li class="nav-item"><a class="nav-link-custom active-link" href="index.php?page=cafe">The Cafe</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="index.php?page=services">Services</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="index.php?page=booking&step=1">Book Now</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isLoggedIn): ?>
                        <span class="signin-link mb-0">Hi, <?php echo htmlspecialchars(substr($displayName, 0, 14)); ?></span>
                        <a href="#" class="signin-link mb-0 text-decoration-underline" data-bs-toggle="modal" data-bs-target="#historyModal" style="cursor: pointer; font-weight: 600; text-transform: uppercase; letter-spacing: 1.2px; font-size: 0.72rem;">History</a>
                        <form method="POST" action="<?= LOGOUT_URL ?>" class="m-0">
                            <button class="btn btn-book py-2 px-3" type="submit" style="background: var(--accent); border-color: var(--accent); color: #fff; border-radius: 0; text-transform: uppercase; letter-spacing: 1.4px; font-size: 0.72rem; font-weight: 600; padding: 0.5rem 1rem;">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="index.php?page=login" class="signin-link mb-0">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero-area">
            <div class="container">
                <div class="row align-items-start gy-4">
                    <div class="col-lg-6">
                        <div class="greeting-box">
                            <div class="d-flex align-items-center">
                                <span class="avatar-chip"></span>
                                <p class="greet-line">Halo, <?php echo htmlspecialchars($displayName); ?>!</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h1 class="hero-title">The Cafe Menu</h1>
                        <p class="hero-sub">Indulge in our curated selection of artisanal beverages and delicate pastries while you enjoy your treatment.</p>
                    </div>
                </div>

                <div class="category-tabs" role="tablist" aria-label="Cafe categories">
                    <button class="tab-btn active" type="button" data-filter="kopi">Kopi</button>
                    <button class="tab-btn" type="button" data-filter="teh">Teh</button>
                    <button class="tab-btn" type="button" data-filter="pastry">Pastry</button>
                    <button class="tab-btn" type="button" data-filter="non-coffee">Non-Coffee</button>
                </div>
            </div>
        </section>        <section class="menu-section">
            <div class="container">
                <div class="row g-4" id="menuContainer">
                    <?php if (!empty($menus)): ?>
                        <?php foreach ($menus as $menu): ?>
                            <?php
                            $menuId = $menu['menu_id'] ?? '';
                            $menuName = $menu['menu_name'] ?? '';
                            $price = (float)($menu['price'] ?? 0);
                            $desc = !empty($menu['description']) ? $menu['description'] : 'Artisan creation crafted by our barista.';
                            
                            // Map category from DB to filter category
                            // DB category: Kopi, Teh, Pastry, Non Coffee, Snack
                            // Filter category: kopi, teh, pastry, non-coffee
                            $dbCat = trim($menu['category'] ?? '');
                            if ($dbCat === '') {
                                $nameLower = strtolower($menuName);
                                if (str_contains($nameLower, 'kopi') || str_contains($nameLower, 'coffee') || str_contains($nameLower, 'latte') || str_contains($nameLower, 'espresso')) {
                                    $dbCat = 'Kopi';
                                } elseif (str_contains($nameLower, 'teh') || str_contains($nameLower, 'tea') || str_contains($nameLower, 'matcha')) {
                                    $dbCat = 'Teh';
                                } elseif (str_contains($nameLower, 'croissant') || str_contains($nameLower, 'pastry') || str_contains($nameLower, 'cake') || str_contains($nameLower, 'roti')) {
                                    $dbCat = 'Pastry';
                                } else {
                                    $dbCat = 'Kopi';
                                }
                            }
                            
                            $filterCat = match (strtolower($dbCat)) {
                                'kopi' => 'kopi',
                                'teh' => 'teh',
                                'pastry' => 'pastry',
                                'non coffee', 'non-coffee' => 'non-coffee',
                                'snack' => 'pastry',
                                default => 'kopi',
                            };

                            $image = trim($menu['image'] ?? '');
                            if (str_contains($image, 'almond croissant.jpg')) {
                                $image = str_replace('almond croissant.jpg', 'almond croissant.png', $image);
                            } elseif (str_contains($image, 'cookies cream.jpg')) {
                                $image = str_replace('cookies cream.jpg', 'cookies cream.png', $image);
                            } elseif (str_contains($image, 'espresso.jpg')) {
                                $image = str_replace('espresso.jpg', 'espresso.png', $image);
                            } elseif (str_contains($image, 'tiramisu cake.webp')) {
                                $image = str_replace('tiramisu cake.webp', 'tiramisu cake.jpg', $image);
                            } elseif (str_contains($image, 'Truffle fries.webp')) {
                                $image = str_replace('Truffle fries.webp', 'truffle fries.png', $image);
                            }

                            if ($image === '') {
                                $image = match ($filterCat) {
                                    'teh' => 'assets/MERISH_PICTURES/CAFE/jasmine tea.jpg',
                                    'pastry' => 'assets/MERISH_PICTURES/CAFE/almond croissant.png',
                                    'non-coffee' => 'assets/MERISH_PICTURES/CAFE/choco frappe.jpg',
                                    default => 'assets/MERISH_PICTURES/CAFE/americano.jpg',
                                };
                            }
                            ?>
                            <div class="col-lg-4 col-md-6 cafe-item" data-category="<?php echo $filterCat; ?>">
                                <article class="menu-card">
                                    <div class="menu-image" style="background-image: url('<?php echo htmlspecialchars($image); ?>');"></div>
                                    <div class="menu-body">
                                        <div class="title-row">
                                            <h2 class="menu-name"><?php echo htmlspecialchars($menuName); ?></h2>
                                            <p class="menu-price">Rp <?php echo number_format($price, 0, ',', '.'); ?></p>
                                        </div>
                                        <p class="menu-desc"><?php echo htmlspecialchars($desc); ?></p>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-4">
                            <div class="alert alert-warning">Belum ada menu cafe yang tersedia.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <section class="order-wrap">
            <div class="container">
                <button class="btn order-btn" type="button" id="orderNowBtn">Order Now <span class="ms-2"></span></button>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-lg-2">
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
                <div class="col-lg-5 text-lg-end">
                    <span class="footer-copy">&copy; 2026 Merish Beauty & Cafe. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tabButtons = document.querySelectorAll('.tab-btn');
        const menuItems = document.querySelectorAll('.cafe-item');

        function applyCafeFilter(filter) {
            menuItems.forEach((item) => {
                const category = item.dataset.category;
                item.style.display = category === filter ? '' : 'none';
            });
        }

        tabButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                tabButtons.forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');
                applyCafeFilter(btn.dataset.filter);
            });
        });

        applyCafeFilter('kopi');

        document.getElementById('orderNowBtn').addEventListener('click', () => {
            window.location.href = 'index.php?page=cafe&action=cart';
        });
    </script>
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
</body>
</html>

