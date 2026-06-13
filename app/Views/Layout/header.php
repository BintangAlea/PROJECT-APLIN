<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'MERISH - Salon & Cafe'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --merish-primary: #8d616f;
            --merish-secondary: #724e5a;
            --merish-accent: #ead2db;
            --merish-dark: #4f4248;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--merish-dark);
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--merish-primary) !important;
            letter-spacing: 2px;
        }
        .nav-link {
            color: var(--merish-dark) !important;
            margin: 0 5px;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: var(--merish-primary) !important;
        }
        .btn-merish {
            background: var(--merish-primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 24px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-merish:hover {
            background: var(--merish-secondary);
            color: white;
            transform: translateY(-2px);
        }
        .hero-section {
            background: linear-gradient(135deg, var(--merish-primary) 0%, var(--merish-secondary) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .hero-section p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        .card-service {
            border: none;
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 100%;
        }
        .card-service:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .badge-promo {
            background: var(--merish-secondary);
            color: white;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            color: var(--merish-primary);
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 50px;
            text-align: center;
        }
        .footer {
            background: var(--merish-dark);
            color: white;
            padding: 50px 0 20px;
            margin-top: 80px;
        }
        .user-badge {
            background: var(--merish-accent);
            color: var(--merish-dark);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .progress-loyalty {
            height: 10px;
            background: #ead2db;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-loyalty .progress-bar {
            background: linear-gradient(90deg, var(--merish-primary), var(--merish-secondary));
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?page=home">✨ MERISH</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=cafe">The Cafe</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=services">Services</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <span class="user-badge">Hi, <?php echo htmlspecialchars(substr($_SESSION['full_name'] ?? 'User', 0, 20)); ?>!</span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="index.php?page=login&action=logout" style="display:inline;">
                                <button class="btn btn-merish ms-2" type="submit">Logout</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="index.php?page=login" class="btn btn-merish ms-2">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
