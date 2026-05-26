<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back - Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f7f0ef;
            --panel: #fffafb;
            --line: #dccfd2;
            --ink: #5b4950;
            --muted: #7d7176;
            --accent: #8a6271;
            --accent-dark: #6f4c58;
        }

        html, body {
            min-height: 100%;
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .topbar {
            min-height: 58px;
            border-bottom: 1px solid var(--line);
            background: rgba(255, 250, 250, 0.92);
            backdrop-filter: blur(6px);
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--accent);
            text-decoration: none;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .nav-link-soft {
            color: #6f6268;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .nav-link-soft:hover {
            color: var(--accent);
        }

        .signup-btn {
            border: 1px solid #b596a2;
            border-radius: 0;
            color: #fff;
            background: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.45rem 0.95rem;
            line-height: 1;
        }

        .signup-btn:hover {
            background: var(--accent-dark);
            color: #fff;
            border-color: var(--accent-dark);
        }

        .page-wrap {
            min-height: calc(100vh - 58px);
            display: flex;
            align-items: center;
            padding: 2rem 0 3rem;
        }

        .login-shell {
            max-width: 880px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .hero-panel,
        .form-panel {
            border: 1px solid var(--line);
            background: var(--panel);
            box-shadow: 0 15px 35px rgba(76, 52, 59, 0.06);
        }

        .form-panel {
            padding: 2.3rem;
        }

        .hero-panel {
            min-height: 100%;
            padding: 2.3rem;
            background:
                linear-gradient(135deg, rgba(255,255,255,0.5), rgba(255,255,255,0.18)),
                radial-gradient(circle at top left, rgba(218, 193, 201, 0.48), transparent 44%),
                linear-gradient(145deg, #f8eeef 0%, #f2e5e6 100%);
        }

        .hero-kicker {
            display: inline-block;
            border: 1px solid #cfb7bf;
            color: #7b5f69;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-size: 0.66rem;
            font-weight: 700;
            padding: 0.28rem 0.55rem;
            margin-bottom: 1rem;
        }

        .hero-title,
        .form-title {
            font-family: 'Playfair Display', serif;
            color: #7b5564;
        }

        .hero-title {
            font-size: clamp(2.8rem, 4vw, 4.6rem);
            line-height: 0.95;
            margin-bottom: 0.85rem;
        }

        .hero-copy {
            color: var(--muted);
            max-width: 350px;
            line-height: 1.7;
            font-size: 0.98rem;
        }

        .login-card-title {
            text-align: center;
            font-size: clamp(2.6rem, 4.2vw, 4rem);
            line-height: 0.95;
            margin-bottom: 0.7rem;
        }

        .login-card-copy {
            text-align: center;
            color: var(--muted);
            margin-bottom: 1.6rem;
            max-width: 360px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-label {
            color: #57474d;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            margin-bottom: 0.45rem;
        }

        .form-control {
            border-radius: 2px;
            border-color: #d8ccd0;
            background: #fff;
            font-size: 0.95rem;
            padding: 0.85rem 0.9rem;
        }

        .form-control:focus {
            border-color: #b58b9c;
            box-shadow: 0 0 0 0.15rem rgba(181, 139, 156, 0.15);
        }

        .submit-btn {
            background: var(--accent);
            border: none;
            border-radius: 2px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.95rem 1rem;
        }

        .submit-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .small-note {
            color: var(--muted);
            font-size: 0.84rem;
            line-height: 1.6;
        }

        .back-link {
            color: #6b5e63;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: var(--accent);
        }

        .alert {
            border-radius: 2px;
            font-size: 0.92rem;
        }

        .alert-danger {
            background: #fbeaec;
            color: #7a3043;
            border-color: #efc9d1;
        }

        .alert-success {
            background: #eaf6ed;
            color: #306347;
            border-color: #cfe8d6;
        }

        @media (max-width: 991.98px) {
            .hero-panel {
                min-height: auto;
            }

            .page-wrap {
                padding: 1rem 0 2rem;
            }

            .form-panel,
            .hero-panel {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-between h-100">
            <a class="brand" href="index.php?page=home">Merish</a>
            <nav class="d-none d-md-flex gap-4 align-items-center">
                <a class="nav-link-soft" href="index.php?page=services">Services</a>
                <a class="nav-link-soft" href="index.php?page=booking&step=1">Book Now</a>
                <a class="nav-link-soft" href="index.php?page=login">Account</a>
            </nav>
            <a href="index.php?page=register" class="btn signup-btn">Sign Up</a>
        </div>
    </header>

    <main class="page-wrap">
        <div class="login-shell w-100">
            <div class="row g-0 align-items-stretch justify-content-center">
                <div class="col-lg-8">
                    <section class="form-panel h-100">
                        <div class="text-center mb-4">
                            <span class="hero-kicker">Checkout Login</span>
                            <h1 class="login-card-title">Welcome Back</h1>
                            <p class="login-card-copy">Please sign in to confirm your booking and continue checkout.</p>
                        </div>

                        <?php if (isset($_SESSION['booking_error']) && $_SESSION['booking_error']): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['booking_error']); unset($_SESSION['booking_error']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error']) && $_SESSION['error']): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?page=login&action=login&context=checkout" class="mx-auto" style="max-width: 420px;">
                            <input type="hidden" name="context" value="checkout">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0" for="password">Password</label>
                                    <a href="#" class="back-link" style="font-size:0.82rem;">Forgot password?</a>
                                </div>
                                <input type="password" class="form-control mt-2" id="password" name="password" placeholder="••••••••" required>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn submit-btn w-100">Sign In</button>
                            </div>
                        </form>

                        <div class="text-center mt-4 small-note">
                            Don't have an account yet? <a href="index.php?page=register" class="back-link">Create an account</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
