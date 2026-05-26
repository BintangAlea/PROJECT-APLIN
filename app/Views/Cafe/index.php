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
            height: 180px;
            border-bottom: 1px solid #ddd2d2;
            background-size: cover;
            background-position: center;
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
            text-align: center;
            padding: 1.55rem 0 2.8rem;
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
                <?php if ($isLoggedIn): ?>
                    <span class="signin-link">Hi, <?php echo htmlspecialchars($displayName); ?></span>
                <?php else: ?>
                    <a class="signin-link" href="index.php?page=login">Sign In</a>
                <?php endif; ?>
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
                                <span class="avatar-chip">👤</span>
                                <p class="greet-line">Halo, <?php echo htmlspecialchars($displayName); ?>!</p>
                            </div>
                            <span class="location-pill">📍 Lokasi: Kursi Salon 2 (Auto-detected)</span>
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
        </section>

        <section class="menu-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 cafe-item" data-category="kopi">
                        <article class="menu-card">
                            <div class="menu-image drink-latte"></div>
                            <div class="menu-body">
                                <div class="title-row">
                                    <h2 class="menu-name">Iced Rose Latte</h2>
                                    <p class="menu-price">Rp 45.000</p>
                                </div>
                                <p class="menu-desc">Signature espresso blend with delicate rose syrup, creamy milk, and organic dried rose.</p>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6 cafe-item" data-category="kopi">
                        <article class="menu-card">
                            <div class="menu-image drink-cappuccino"></div>
                            <div class="menu-body">
                                <div class="title-row">
                                    <h2 class="menu-name">Classic Cappuccino</h2>
                                    <p class="menu-price">Rp 38.000</p>
                                </div>
                                <p class="menu-desc">Rich double espresso balanced with equal parts steamed milk and a thick layer of foam.</p>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6 cafe-item" data-category="teh">
                        <article class="menu-card">
                            <div class="menu-image drink-earlgrey"></div>
                            <div class="menu-body">
                                <div class="title-row">
                                    <h2 class="menu-name">Earl Grey Lavender</h2>
                                    <p class="menu-price">Rp 42.000</p>
                                </div>
                                <p class="menu-desc">Premium bergamot-infused black tea steeped with culinary lavender buds for aroma.</p>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 col-md-6 cafe-item" data-category="pastry">
                        <article class="menu-card">
                            <div class="menu-image food-croissant"></div>
                            <div class="menu-body">
                                <div class="title-row">
                                    <h2 class="menu-name">Almond Croissant</h2>
                                    <p class="menu-price">Rp 35.000</p>
                                </div>
                                <p class="menu-desc">Twice-baked butter croissant filled with rich almond frangipane and topped with sliced almonds.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="order-wrap">
            <div class="container">
                <button class="btn order-btn" type="button" id="orderNowBtn">Order Now <span class="ms-2">→</span></button>
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
</body>
</html>
