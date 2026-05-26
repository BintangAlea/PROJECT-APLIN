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
        }

        .visual-hair {
            background-image:
                linear-gradient(120deg, rgba(0, 0, 0, 0.08), rgba(255, 255, 255, 0.06)),
                repeating-linear-gradient(130deg, #7f8188 0 6px, #9ca0a6 6px 13px, #70737a 13px 19px);
        }

        .visual-nails {
            background-image:
                radial-gradient(circle at 40% 70%, #d9d9d9 0 16%, transparent 17%),
                linear-gradient(145deg, #9ba0a7 0%, #b8bcc1 45%, #8f949a 100%);
        }

        .visual-lashes {
            background-image:
                linear-gradient(90deg, #babec4 0 14%, #d2d4d8 14% 100%),
                linear-gradient(140deg, #a9adb4 0%, #d6d8dc 100%);
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
        }

        .expert-elena {
            background-image: linear-gradient(140deg, #d5d7da 0%, #abafb6 100%);
        }

        .expert-marcus {
            background-image: linear-gradient(140deg, #2f3135 0%, #7c828b 100%);
        }

        .expert-chloe {
            background-image: linear-gradient(140deg, #b7bbc2 0%, #8f949d 100%);
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
                <?php if ($isLoggedIn): ?>
                    <span class="me-3" style="color: #5f5358; font-size: 0.8rem; letter-spacing: 1.2px; text-transform: uppercase; font-weight: 700;">
                        Hi, <?php echo htmlspecialchars($displayName); ?>
                    </span>
                    <a href="index.php?page=login&action=logout" class="btn signin-btn">Logout</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn signin-btn">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="services-hero">
        <div class="container text-center">
            <h1 class="hero-heading">Our Signature Treatments</h1>
            <p class="hero-sub">Curated services designed to elevate your personal style with professional precision.</p>
            <div class="promo-pill">
                <span>✧</span>
                <span>Enjoy a 20% synergy discount on combined services</span>
            </div>
        </div>
    </header>

    <div class="divider-band d-flex align-items-center">
        <div class="container">
            <div class="filters">
                <button type="button" class="btn filter-btn active" data-filter="all">All</button>
                <button type="button" class="btn filter-btn" data-filter="hair">Hair</button>
                <button type="button" class="btn filter-btn" data-filter="nails">Nails</button>
                <button type="button" class="btn filter-btn" data-filter="lashes">Lashes</button>
                <button type="button" class="btn filter-btn" data-filter="wax">Wax & Eyebrows</button>
            </div>
        </div>
    </div>

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
                        <h3 class="expert-name">Elena R.</h3>
                        <p class="expert-role">Color Director</p>
                        <div class="expert-stars">☆☆☆☆☆</div>
                        <p class="expert-copy">Master of dimensional color and balayage techniques with over a decade of high-fashion experience.</p>
                    </article>
                </div>

                <div class="col-lg-4 col-md-6">
                    <article class="expert-card">
                        <div class="expert-photo expert-marcus"></div>
                        <h3 class="expert-name">Marcus T.</h3>
                        <p class="expert-role">Master Cutter</p>
                        <div class="expert-stars">☆☆☆☆☆</div>
                        <p class="expert-copy">Precision cutting specialist known for creating effortless, structured silhouettes tailored to each individual.</p>
                    </article>
                </div>

                <div class="col-lg-4 col-md-6">
                    <article class="expert-card">
                        <div class="expert-photo expert-chloe"></div>
                        <h3 class="expert-name">Chloe M.</h3>
                        <p class="expert-role">Senior Esthetician</p>
                        <div class="expert-stars">☆☆☆☆☆</div>
                        <p class="expert-copy">Specializing in advanced skincare treatments and meticulous brow architecture to enhance natural beauty.</p>
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
        const filterButtons = document.querySelectorAll('.filter-btn');
        const serviceItems = document.querySelectorAll('.service-item');

        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                filterButtons.forEach((item) => item.classList.remove('active'));
                button.classList.add('active');

                const selectedFilter = button.dataset.filter;

                serviceItems.forEach((service) => {
                    const category = service.dataset.category;
                    const shouldShow = selectedFilter === 'all' || category === selectedFilter;
                    service.style.display = shouldShow ? '' : 'none';
                });
            });
        });

        function bookService(serviceId, serviceName) {
            const params = new URLSearchParams({
                page: 'booking',
                step: '1',
                service_id: serviceId,
                service: serviceName
            });
            window.location.href = 'index.php?' + params.toString();
        }
    </script>
</body>
</html>
