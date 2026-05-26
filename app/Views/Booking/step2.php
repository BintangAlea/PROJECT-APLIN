<?php
$bundles = $bundles ?? [];

if (empty($bundles)) {
    $bundles = [
        [
            'bundle_id' => 'bundle_spa_coffee',
            'bundle_name' => 'Hair Spa & Artisan Coffee',
            'description' => 'Complete your deep conditioning treatment with a freshly brewed iced brown sugar latte, served right at your station.',
            'original_price' => 200000,
            'final_price' => 150000,
            'discount_value' => 50000,
            'badge' => 'Popular',
            'icon' => '✦',
        ],
        [
            'bundle_id' => 'bundle_scalp_tea',
            'bundle_name' => 'Scalp Massage & Herbal Tea',
            'description' => 'Extend your wash with a 15-minute invigorating scalp massage accompanied by a soothing chamomile blend.',
            'original_price' => 150000,
            'final_price' => 100000,
            'discount_value' => 50000,
            'badge' => 'Relax',
            'icon' => '⌁',
        ],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bundle - Booking | Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f3efef;
            --surface: #f6f2f2;
            --line: #d8cecf;
            --ink: #574a50;
            --muted: #7f7478;
            --accent: #7b4f61;
            --accent-soft: #e8d6e7;
            --card: #faf8f8;
        }

        html,
        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
            min-height: 100%;
        }

        .booking-shell {
            min-height: 100vh;
            background: var(--bg);
        }

        .top-header {
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            min-height: 74px;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            text-decoration: none;
            letter-spacing: 2px;
            font-size: 2rem;
            font-weight: 600;
        }

        .signin-btn {
            border: none;
            background: var(--accent);
            color: #fff;
            border-radius: 0;
            font-size: 0.68rem;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.45rem 1rem;
            line-height: 1.1;
        }

        .signin-btn:hover {
            background: #664050;
            color: #fff;
        }

        .sidebar {
            border-right: 1px solid var(--line);
            background: var(--surface);
            min-height: calc(100vh - 74px);
            padding: 2rem 1.25rem;
        }

        .step-nav {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .step-item {
            border-radius: 12px;
            color: #695d62;
            font-size: 0.86rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.65rem;
            text-decoration: none;
            border: 1px solid transparent;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .step-item.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
        }

        .step-item:hover {
            background: #efe7eb;
            color: #6e4f5d;
        }

        .step-item.disabled {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .main-panel {
            padding: 2.2rem 2.2rem 2.8rem;
            min-height: calc(100vh - 74px);
            background: var(--bg);
        }

        .title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3vw, 3.2rem);
            color: #6c4c5a;
            font-weight: 500;
            margin-bottom: 0.3rem;
            text-align: center;
        }

        .subtitle {
            color: #7d7377;
            text-align: center;
            margin-bottom: 1.85rem;
        }

        .bundle-card {
            border: 1px solid #cec3c5;
            background: var(--card);
            padding: 0.85rem 0.9rem 0.95rem;
            height: 100%;
        }

        .badge-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.65rem;
        }

        .chip {
            background: #d7a8bd;
            color: #654251;
            font-size: 0.62rem;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.14rem 0.44rem;
        }

        .badge-icon {
            color: #7d5767;
            font-size: 1rem;
        }

        .bundle-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #56464c;
            margin-bottom: 0.4rem;
            line-height: 1.12;
        }

        .bundle-desc {
            color: #7f7478;
            font-size: 0.87rem;
            line-height: 1.6;
            min-height: 76px;
            margin-bottom: 0.6rem;
        }

        .price-row {
            display: flex;
            align-items: flex-end;
            gap: 0.55rem;
            margin-bottom: 0.75rem;
        }

        .price-old {
            color: #9c9296;
            text-decoration: line-through;
            font-size: 0.95rem;
        }

        .price-main {
            color: #5e4b53;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            line-height: 1;
        }

        .save-chip {
            margin-left: auto;
            background: #e4dbe1;
            color: #75686f;
            font-size: 0.63rem;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            font-weight: 700;
            padding: 0.16rem 0.42rem;
        }

        .add-btn {
            width: 100%;
            border: 1px solid #bcaeb2;
            background: transparent;
            color: #64585d;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 0;
            padding: 0.62rem;
        }

        .add-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: #f6eef2;
        }

        .bottom-actions {
            border-top: 1px solid var(--line);
            margin-top: 2rem;
            padding-top: 2rem;
            text-align: center;
        }

        .skip-btn {
            border: none;
            background: transparent;
            color: #6d6267;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            font-size: 0.73rem;
            font-weight: 700;
        }

        .skip-btn:hover {
            color: var(--accent);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid var(--line);
                padding-bottom: 1rem;
            }

            .main-panel {
                padding: 1.5rem 1rem 2.3rem;
            }
        }
    </style>
</head>
<body>
<div class="booking-shell">
<header class="top-header d-flex align-items-center">
    <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
        <a class="logo" href="index.php?page=home">Merish</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?page=home" class="btn signin-btn">Home</a>
        <?php else: ?>
            <a href="index.php?page=login" class="btn signin-btn">Sign In</a>
        <?php endif; ?>
    </div>
</header>

<div class="container-fluid">
    <div class="row g-0">
        <aside class="col-lg-3 col-xl-2 sidebar">
            <ul class="step-nav">
                <li><a class="step-item" href="index.php?page=booking&step=1">▵ <span>Category</span></a></li>
                <li><a class="step-item active" href="index.php?page=booking&step=2">✧ <span>Bundle</span></a></li>
                <li><a class="step-item" href="index.php?page=booking&step=3">◷ <span>Schedule</span></a></li>
                <li><a class="step-item" href="index.php?page=booking&step=4">◉ <span>Stylist</span></a></li>
                <li><a class="step-item" href="index.php?page=booking&step=5">▣ <span>Checkout</span></a></li>
                <li><span class="step-item disabled">◌ <span>Confirm</span></span></li>
            </ul>
        </aside>

        <main class="col-lg-9 col-xl-10 main-panel">
            <h1 class="title">Bikin sesi perawatanmu makin rileks...</h1>
            <p class="subtitle">Enhance your experience with our curated add-ons.</p>

            <?php if (isset($_SESSION['booking_error'])): ?>
                <div class="alert alert-danger mb-3"><?php echo htmlspecialchars($_SESSION['booking_error']); ?></div>
                <?php unset($_SESSION['booking_error']); ?>
            <?php endif; ?>

            <form method="POST" action="/index.php?page=booking&step=2" id="bundleForm">
                <input type="hidden" name="bundle_id" id="bundle_id" value="">
                <div class="row g-3">
                    <?php foreach ($bundles as $bundle): ?>
                        <?php
                        $bundleId = (string)($bundle['bundle_id'] ?? '');
                        $bundleName = (string)($bundle['bundle_name'] ?? 'Curated Bundle');
                        $desc = (string)($bundle['description'] ?? 'Bundle tambahan untuk pengalaman treatment yang lebih lengkap.');
                        $oldPrice = (float)($bundle['original_price'] ?? (($bundle['final_price'] ?? 0) + ($bundle['discount_value'] ?? 0)));
                        $finalPrice = (float)($bundle['final_price'] ?? 0);
                        $discountValue = (float)($bundle['discount_value'] ?? max($oldPrice - $finalPrice, 0));
                        $badge = strtoupper((string)($bundle['badge'] ?? 'Relax'));
                        $icon = (string)($bundle['icon'] ?? '✧');
                        ?>
                        <div class="col-lg-6">
                            <article class="bundle-card">
                                <div class="badge-strip">
                                    <span class="chip"><?php echo htmlspecialchars($badge); ?></span>
                                    <span class="badge-icon"><?php echo htmlspecialchars($icon); ?></span>
                                </div>
                                <h2 class="bundle-name"><?php echo htmlspecialchars($bundleName); ?></h2>
                                <p class="bundle-desc"><?php echo htmlspecialchars($desc); ?></p>
                                <div class="price-row">
                                    <span class="price-old">Rp<?php echo number_format($oldPrice, 0, ',', '.'); ?></span>
                                    <span class="price-main">Rp<?php echo number_format($finalPrice, 0, ',', '.'); ?></span>
                                    <span class="save-chip">Hemat Rp<?php echo number_format($discountValue, 0, ',', '.'); ?></span>
                                </div>
                                <button type="button" class="btn add-btn" data-bundle-id="<?php echo htmlspecialchars($bundleId); ?>">Add to Bill</button>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bottom-actions">
                    <button type="submit" class="skip-btn">Skip Add-Ons →</button>
                </div>
            </form>
        </main>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const bundleIdInput = document.getElementById('bundle_id');
    const form = document.getElementById('bundleForm');

    document.querySelectorAll('[data-bundle-id]').forEach((button) => {
        button.addEventListener('click', () => {
            bundleIdInput.value = button.dataset.bundleId;
            form.submit();
        });
    });
</script>
</body>
</html>
