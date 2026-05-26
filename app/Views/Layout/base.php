<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Merish - Premium Salon & Artisan Cafe'; ?></title>
    <link rel="stylesheet" href="/SIB/PROJECT-APLIN/assets/css/style.css">
    <?php if (isset($additional_css)): ?>
        <?php foreach ((array) $additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- HEADER / NAVIGATION -->
    <header>
        <a href="index.php" class="logo">Merish</a>
        
        <nav>
            <a href="index.php#services">Services</a>
            <a href="index.php#cafe">The Cafe</a>
            <a href="index.php#stylists">Stylists</a>
        </nav>

        <div class="nav-right">
            <div class="login-register">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="font-size: 14px; color: var(--text-light);">
                        Hi, <?php echo $_SESSION['full_name'] ?? 'User'; ?>
                    </span>
                    <a href="index.php?page=login&action=logout" class="btn btn-login">Logout</a>
                    <?php
                        $dashboardPage = match($_SESSION['role'] ?? '') {
                            'admin' => 'admin',
                            'receptionist' => 'receptionist',
                            'barista' => 'barista',
                            'beautician' => 'beautician',
                            'customer' => 'home',
                            default => 'home',
                        };
                    ?>
                    <a href="index.php?page=<?php echo $dashboardPage; ?>" class="btn btn-primary">Dashboard</a>
                <?php else: ?>
                    <a href="index.php?page=login&action=login" class="btn btn-login">Login / Register</a>
                    <a href="index.php?page=booking&step=1" class="btn btn-primary">Book Appointment</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="main-content">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h4>Merish</h4>
                    <p>Premium salon and artisan cafe experience</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php; ?>">Home</a></li>
                        <li><a href="index.php; ?>#services">Services</a></li>
                        <li><a href="index.php; ?>#cafe">The Cafe</a></li>
                        <li><a href="index.php; ?>#stylists">Stylists</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>Email: info@merish.com</p>
                    <p>Phone: +62 123 456 789</p>
                </div>
                <div class="footer-section">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#">Facebook</a>
                        <a href="#">Instagram</a>
                        <a href="#">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Merish. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="/SIB/PROJECT-APLIN/assets/js/script.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ((array) $additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>


