<?php
/**
 * Booking Step 4.1: Login / Register (separate pages)
 * ?mode=register  → show register form
 * (default)       → show login form
 */
$booking = $booking ?? [];
$flashError = $_SESSION['booking_error'] ?? null;
unset($_SESSION['booking_error']);
$isRegister = (isset($_GET['mode']) && $_GET['mode'] === 'register');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 4.1: <?= $isRegister ? 'Create Account' : 'Sign In' ?> - Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4eded;
            --surface: #f8f2f2;
            --card: #fffafa;
            --line: #d9ccd0;
            --ink: #4f4248;
            --muted: #7a6e73;
            --accent: #8d616f;
            --accent-dark: #724e5a;
            --accent-soft: #ead2db;
            --input-border: #c8b8bd;
            --input-bg: #fdf8f8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background:
                radial-gradient(circle at 2px 2px, rgba(141,97,111,0.06) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* ── Topbar ────────────────────────────────── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 2.5rem;
            background: rgba(255,249,249,0.92);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 3px;
            color: var(--accent);
            text-decoration: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #b79fa6;
            color: #6d5660;
            background: #fff;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.55rem 1.1rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: #fff;
            background: var(--accent);
            border-color: var(--accent);
        }

        /* ── Step indicator ─────────────────────────── */
        .step-bar {
            text-align: center;
            padding: 1rem 1rem 0;
            font-size: 0.72rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
        }

        .step-bar span { color: var(--accent); }

        /* ── Main content ───────────────────────────── */
        .auth-shell {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 1.5rem 2rem 2rem;
        }

        .auth-card {
            max-width: 480px;
            width: 100%;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 16px 48px rgba(73,53,60,0.08);
            padding: 2.6rem 2.4rem 2.2rem;
        }

        .panel-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            color: #46373f;
            margin-bottom: 0.35rem;
        }

        .panel-sub {
            color: var(--muted);
            font-size: 0.88rem;
            margin-bottom: 1.8rem;
            line-height: 1.5;
        }

        /* ── Alert ──────────────────────────────────── */
        .alert-box {
            padding: 0.7rem 0.9rem;
            margin-bottom: 1.2rem;
            border: 1px solid #efc9d1;
            background: #fbeaec;
            color: #7c3547;
            font-size: 0.84rem;
        }

        /* ── Form elements ──────────────────────────── */
        .field {
            margin-bottom: 1.15rem;
        }

        .field label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #6f5d64;
            margin-bottom: 0.4rem;
        }

        .field input {
            width: 100%;
            padding: 0.7rem 0.85rem;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            color: var(--ink);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(141,97,111,0.1);
        }

        .field input::placeholder {
            color: #b5a5aa;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .forgot-link {
            display: inline-block;
            font-size: 0.78rem;
            color: var(--accent);
            text-decoration: none;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }

        .forgot-link:hover { text-decoration: underline; }

        /* ── Buttons ────────────────────────────────── */
        .submit-btn {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid var(--accent-dark);
            background: var(--accent);
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submit-btn:hover {
            background: var(--accent-dark);
        }

        .switch-text {
            text-align: center;
            margin-top: 1.3rem;
            font-size: 0.84rem;
            color: var(--muted);
        }

        .switch-text a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
        }

        .switch-text a:hover { text-decoration: underline; }

        /* ── Divider ────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin: 1.5rem 0;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        /* ── Footer ─────────────────────────────────── */
        .auth-footer {
            background: #e7e1e0;
            border-top: 1px solid #d4c9cc;
            padding: 1.8rem 2rem 2rem;
            text-align: center;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: #74545f;
            margin-bottom: 0.8rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            font-size: 0.78rem;
            color: var(--muted);
            text-decoration: none;
        }

        .footer-links a:hover { color: var(--accent); }

        .footer-copy {
            font-size: 0.76rem;
            color: #9a8b8f;
        }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width: 768px) {
            .topbar { padding: 1rem 1.2rem; }
            .auth-shell { padding: 1rem; }
            .auth-card { padding: 2rem 1.5rem; }
            .field-row { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .brand { font-size: 1.4rem; }
            .panel-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <header class="topbar">
        <a href="/index.php?page=home" class="brand">MERISH</a>
        <a href="/index.php?page=booking&step=4" class="back-link">← Back</a>
    </header>

    <div class="step-bar">Step 4.1 — <span><?= $isRegister ? 'Create Account' : 'Sign In' ?></span></div>

    <?php if ($flashError): ?>
        <div style="max-width:480px;margin:1rem auto 0;padding:0 2rem;">
            <div class="alert-box"><?= htmlspecialchars($flashError) ?></div>
        </div>
    <?php endif; ?>

    <section class="auth-shell">
        <div class="auth-card">

<?php if ($isRegister): ?>
            <!-- ═══ Register Form ═══ -->
            <h1 class="panel-title">Create Your Account</h1>
            <p class="panel-sub">Join Merish for exclusive VIP benefits and booking privileges.</p>

            <form method="POST" action="/index.php?page=booking&step=4.1" id="registerForm">
                <input type="hidden" name="action" value="register">

                <div class="field-row">
                    <div class="field">
                        <label for="reg_first">First Name</label>
                        <input type="text" id="reg_first" placeholder="First name">
                    </div>
                    <div class="field">
                        <label for="reg_last">Last Name</label>
                        <input type="text" id="reg_last" placeholder="Last name">
                    </div>
                </div>

                <!-- Hidden combined name field for backend -->
                <input type="hidden" id="reg_name" name="name">

                <div class="field">
                    <label for="reg_email">Email</label>
                    <input type="email" id="reg_email" name="email" placeholder="your@email.com" required>
                </div>

                <div class="field">
                    <label for="reg_phone">Phone</label>
                    <input type="tel" id="reg_phone" name="phone" placeholder="+62 812 3456 7890">
                </div>

                <div class="field">
                    <label for="reg_password">Password</label>
                    <input type="password" id="reg_password" name="password" placeholder="Create a password" required>
                </div>

                <div class="field">
                    <label for="reg_confirm">Confirm Password</label>
                    <input type="password" id="reg_confirm" placeholder="Re-enter your password">
                </div>

                <button type="submit" class="submit-btn">Create Account</button>
            </form>

            <div class="divider">or</div>

            <p class="switch-text">
                Already have an account? <a href="/index.php?page=booking&step=4.1">Log in</a>
            </p>

<?php else: ?>
            <!-- ═══ Login Form ═══ -->
            <h1 class="panel-title">Welcome Back</h1>
            <p class="panel-sub">Please sign in to continue your booking with Merish.</p>

            <form method="POST" action="/index.php?page=booking&step=4.1" id="loginForm">
                <input type="hidden" name="action" value="login">

                <div class="field">
                    <label for="login_email">Email</label>
                    <input type="email" id="login_email" name="email" placeholder="your@email.com" required>
                </div>

                <div class="field">
                    <label for="login_password">Password</label>
                    <input type="password" id="login_password" name="password" placeholder="Enter your password" required>
                </div>

                <a href="#" class="forgot-link">Forgot password?</a>

                <button type="submit" class="submit-btn">Sign In</button>
            </form>

            <div class="divider">or</div>

            <p class="switch-text">
                Don't have an account yet? <a href="/index.php?page=booking&step=4.1&mode=register">Create an account</a>
            </p>
<?php endif; ?>

        </div>
    </section>

    <footer class="auth-footer">
        <div class="footer-brand">Merish</div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact Us</a>
            <a href="#">Location</a>
        </div>
        <div class="footer-copy">&copy; 2024 Merish Beauty Studio. All rights reserved.</div>
    </footer>

    <script>
<?php if ($isRegister): ?>
        // Register form: combine first + last name before submit, validate confirm password
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            var first = document.getElementById('reg_first').value.trim();
            var last  = document.getElementById('reg_last').value.trim();
            var pass  = document.getElementById('reg_password').value;
            var conf  = document.getElementById('reg_confirm').value;

            if (!first) {
                e.preventDefault();
                alert('First name is required.');
                document.getElementById('reg_first').focus();
                return;
            }

            if (pass !== conf) {
                e.preventDefault();
                alert('Password and Confirm Password do not match.');
                document.getElementById('reg_confirm').focus();
                return;
            }

            // Combine into single name for backend
            document.getElementById('reg_name').value = last ? (first + ' ' + last) : first;
        });
<?php else: ?>
        // Login form: basic validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            var email = document.getElementById('login_email').value.trim();
            var pass  = document.getElementById('login_password').value;
            if (!email || !pass) {
                e.preventDefault();
                alert('Email and password are required.');
            }
        });
<?php endif; ?>
    </script>

</body>
</html>
