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
                        <form method="POST" action="index.php?page=login&action=logout">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
