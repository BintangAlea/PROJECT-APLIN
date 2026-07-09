ï»¿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Availability - Merish System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7eeed;
            --panel: #fff8f6;
            --line: #eadbd7;
            --ink: #342629;
            --muted: #8b7074;
            --accent: #8f6771;
            --accent-soft: #f5dde2;
        }
        body {
            background: radial-gradient(circle at top left, #232323 0, #141414 24%, #0f0f0f 100%);
            font-family: 'Inter', sans-serif;
            color: var(--ink);
        }
        .shell { min-height: 100vh; padding: 10px; }
        .app-frame {
            min-height: calc(100vh - 20px);
            border-radius: 14px;
            overflow: hidden;
            background: var(--bg);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }
        .sidebar {
            background: rgba(250, 241, 240, 0.92);
            border-right: 1px solid var(--line);
            min-height: calc(100vh - 20px);
        }
        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            line-height: 0.95;
            color: #6f4e56;
        }
        .brand-subtitle { color: var(--muted); font-size: 0.85rem; }
        .avatar {
            width: 62px; height: 62px; border-radius: 50%; object-fit: cover;
            border: 2px solid #f1dedb; background: #f1dedb;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 0.65rem;
            padding: 0.85rem 1rem; border-radius: 10px; color: #6f5a5d;
            text-decoration: none; margin-bottom: 0.35rem;
            transition: all 0.2s ease; border: 1px solid transparent;
        }
        .sidebar-link:hover { background: rgba(244, 219, 227, 0.55); color: var(--ink); }
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(244, 219, 227, 0.95), rgba(239, 209, 221, 0.9));
            border-color: rgba(143, 103, 113, 0.15); color: var(--ink);
        }
        .sidebar-footer { border-top: 1px solid var(--line); }
        .main-topbar {
            background: rgba(255, 248, 246, 0.8);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(10px);
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3vw, 3rem);
            color: #7d5a62;
            margin-bottom: 0;
        }
        .page-subtitle { color: var(--muted); margin-bottom: 0; }
        .quick-btn {
            border: 1px solid var(--line); background: #fff; color: #5e4a4d;
            border-radius: 8px; padding: 0.55rem 0.9rem; font-size: 0.8rem;
            letter-spacing: 0.06em; text-transform: uppercase;
        }
        .panel {
            background: rgba(255, 250, 248, 0.76); border: 1px solid var(--line);
            border-radius: 10px; box-shadow: 0 8px 24px rgba(118, 83, 89, 0.06);
        }
        .card-title-hero {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 2vw, 2.7rem);
            color: #4c3538;
        }
        .menu-row {
            border-top: 1px solid rgba(234, 219, 215, 0.8);
            padding: 1rem 0;
        }
        .menu-row:first-child { border-top: 0; }
        .menu-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            color: var(--ink);
        }
        .menu-desc { color: var(--muted); font-size: 0.9rem; }
        .availability {
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
        }
        .available { color: #8b6772; }
        .empty { color: #e37972; }
        .menu-thumb {
            width: 44px;
            height: 44px;
            border-radius: 4px;
            background: linear-gradient(135deg, #2d2d2d, #808080);
        }
        @media (max-width: 991.98px) {
            .sidebar { min-height: auto; }
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="app-frame row g-0">
        <aside class="col-lg-2 sidebar d-flex flex-column">
            <div class="p-4">
                <div class="d-flex justify-content-center mb-3">
                    <img class="avatar" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=240&q=80" alt="Barista profile">
                </div>
                <div class="text-center mb-4">
                    <div class="brand-title">Merish<br>Barista</div>
                    <div class="brand-subtitle mt-2">Online</div>
                </div>
                <a class="sidebar-link" href="index.php?page=barista">
                    <span>Ã¢Ëœâ€¢</span>
                    <span>KDS Board</span>
                </a>
                <a class="sidebar-link active" href="index.php?page=barista&action=menuAvailability">
                    <span>Ã¢ËœÂ°</span>
                    <span>Menu Availability</span>
                </a>
            </div>
            <div class="mt-auto p-4 sidebar-footer">
                <a class="sidebar-link" href="<?= LOGOUT_URL ?>">
                    <span>Ã¢Å¡â„¢</span>
                    <span>Settings</span>
                </a>
                <a class="sidebar-link" href="<?= LOGOUT_URL ?>">
                    <span>?</span>
                    <span>Support</span>
                </a>
            </div>
        </aside>

        <section class="col-lg-10 d-flex flex-column">
            <div class="main-topbar px-4 px-lg-5 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Item Availability Manager') ?></h1>
                    <p class="page-subtitle">Toggle items to instantly update their availability across the ordering system.</p>
                </div>
                <button class="quick-btn">Quick Menu</button>
            </div>

            <div class="p-4 p-lg-5 flex-grow-1">
                <div class="panel p-4 p-lg-5 mx-auto" style="max-width: 820px;">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <div class="text-uppercase small" style="color:#9a7480; letter-spacing:0.12em;">Quick Menu</div>
                            <div class="card-title-hero">Ketersediaan Item</div>
                        </div>
                        <div class="text-muted" style="max-width: 280px; font-size: 0.95rem;">
                            Toggle items to instantly update their availability across the ordering system.
                        </div>
                    </div>

                    <?php if (!empty($menus)): ?>
                        <?php foreach ($menus as $menu): ?>
                            <?php $available = !empty($menu['is_available']); ?>
                            <div class="menu-row d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="menu-thumb"></div>
                                    <div>
                                        <div class="menu-name"><?= htmlspecialchars($menu['menu_name'] ?? 'Menu') ?></div>
                                        <div class="menu-desc">Merish beverage item</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="availability <?= $available ? 'available' : 'empty' ?>"><?= $available ? 'Available' : 'Empty' ?></div>
                                    <div class="small text-muted">Rp <?= number_format((float)($menu['price'] ?? 0), 0, ',', '.') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">No menu items found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>

