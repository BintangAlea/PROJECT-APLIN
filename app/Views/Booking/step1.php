<?php
$services = $services ?? [];
$selectedServiceId = trim((string)($_GET['service_id'] ?? ''));
$selectedServiceName = trim((string)($_GET['service'] ?? ''));

$categories = [
    'hair' => ['label' => 'Hair', 'subtitle' => 'Cuts, Color & Styling', 'visual' => 'visual-hair'],
    'nails' => ['label' => 'Nails', 'subtitle' => 'Manicures & Pedicures', 'visual' => 'visual-nails'],
    'lashes' => ['label' => 'Lashes', 'subtitle' => 'Extensions & Lifts', 'visual' => 'visual-lashes'],
    'wax' => ['label' => 'Wax & Brows', 'subtitle' => 'Shaping & Hair Removal', 'visual' => 'visual-wax'],
];

$normalizeCategory = static function (?string $category): string {
    $value = strtolower(trim((string)$category));
    return match ($value) {
        'hair' => 'hair',
        'nails' => 'nails',
        'lashes' => 'lashes',
        'wax & eyebrows', 'wax', 'eyebrows', 'wax and eyebrows' => 'wax',
        default => 'hair',
    };
};

if ($selectedServiceId === '' && $selectedServiceName !== '') {
    foreach ($services as $item) {
        $name = strtolower(trim((string)($item['service_name'] ?? '')));
        if ($name === strtolower($selectedServiceName)) {
            $selectedServiceId = (string)($item['service_id'] ?? '');
            break;
        }
    }
}

$activeCategory = 'hair';
foreach ($services as $item) {
    if ((string)($item['service_id'] ?? '') === $selectedServiceId) {
        $activeCategory = $normalizeCategory($item['category'] ?? '');
        break;
    }
}

$serviceCountByCategory = ['hair' => 0, 'nails' => 0, 'lashes' => 0, 'wax' => 0];
foreach ($services as $item) {
    $serviceCountByCategory[$normalizeCategory($item['category'] ?? '')]++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Category - Booking | Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f3efef;
            --surface: #f6f2f2;
            --line: #d5cbcd;
            --ink: #54464d;
            --muted: #7d7276;
            --accent: #7a4f61;
            --accent-soft: #e8d6e6;
            --card: #faf9f9;
        }

        html,
        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .booking-shell {
            min-height: 100vh;
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

        .exit-btn {
            border: 1px solid #ab949d;
            color: #5f4f55;
            background: transparent;
            border-radius: 0;
            font-size: 0.72rem;
            letter-spacing: 1.7px;
            text-transform: uppercase;
            font-weight: 600;
            padding: 0.45rem 1rem;
        }

        .exit-btn:hover {
            color: #fff;
            background: var(--accent);
            border-color: var(--accent);
        }

        .sidebar {
            border-right: 1px solid var(--line);
            background: var(--surface);
            min-height: calc(100vh - 74px);
            padding: 2rem 1.3rem;
        }

        .step-nav {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .step-nav .step-item {
            border-radius: 12px;
            color: #665b60;
            font-size: 0.86rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.65rem;
            text-decoration: none;
            border: 1px solid transparent;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .step-nav .step-item.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
        }

        .step-nav .step-item:hover {
            background: #efe7eb;
            color: #6e4f5d;
        }

        .step-nav .step-item.disabled {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .main-panel {
            padding: 1.8rem 2rem 2.2rem;
        }

        .main-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 3.5vw, 3.8rem);
            color: #6d4b5b;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .main-subtitle {
            color: #7b7074;
            margin-bottom: 1.6rem;
        }

        .category-card {
            border: 1px solid #cfc2c6;
            border-radius: 8px;
            overflow: hidden;
            background: var(--card);
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .category-card.active {
            border-color: #8a5f71;
            box-shadow: 0 0 0 2px rgba(122, 79, 97, 0.12);
        }

        .category-visual {
            height: 145px;
            position: relative;
            background-size: cover;
            background-position: center;
            filter: grayscale(100%);
        }

        .visual-hair {
            background-image: repeating-linear-gradient(145deg, #8d7f76 0 7px, #b4a79f 7px 14px, #7c6f68 14px 22px);
        }

        .visual-nails {
            background-image: linear-gradient(130deg, #5a5757 0%, #989191 45%, #c4bdbc 100%);
        }

        .visual-lashes {
            background-image: linear-gradient(130deg, #bcb4b4 0%, #8c8484 100%);
        }

        .visual-wax {
            background-image: linear-gradient(130deg, #b2a6a4 0%, #8e8180 100%);
        }

        .check-mark {
            position: absolute;
            right: 10px;
            top: 10px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            background: #7c5a68;
            color: #fff;
            font-size: 0.7rem;
        }

        .category-card.active .check-mark {
            display: inline-flex;
        }

        .category-body {
            text-align: center;
            padding: 0.7rem 0.6rem 0.85rem;
        }

        .category-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #5a474f;
            margin: 0;
            line-height: 1.1;
        }

        .category-sub {
            font-size: 0.86rem;
            color: #7d7176;
            margin-top: 0.2rem;
        }

        .services-head {
            margin-top: 1.9rem;
            margin-bottom: 0.8rem;
            padding-bottom: 0.65rem;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .services-head h2 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #704f5d;
        }

        .step-chip {
            background: var(--accent-soft);
            color: #7f5f6d;
            font-size: 0.68rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0.2rem 0.48rem;
            border-radius: 4px;
            font-weight: 700;
        }

        .service-option {
            border: 1px solid transparent;
            border-radius: 8px;
            margin-bottom: 0.68rem;
            padding: 0.75rem 0.85rem;
            background: transparent;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .service-option.is-active {
            border-color: #ad8b98;
            background: #f8f1f4;
        }

        .service-option input {
            margin-top: 0.15rem;
        }

        .service-name {
            margin: 0;
            font-size: 0.95rem;
            color: #56484f;
            font-weight: 500;
        }

        .service-desc {
            margin: 0.15rem 0 0;
            color: #80757a;
            font-size: 0.84rem;
        }

        .promo-tag {
            display: inline-block;
            margin-left: 0.35rem;
            background: var(--accent-soft);
            color: #7e5d6b;
            font-size: 0.62rem;
            letter-spacing: 0.6px;
            border-radius: 3px;
            padding: 0.1rem 0.35rem;
            font-weight: 700;
        }

        .service-meta {
            text-align: right;
            color: #6b6065;
            font-size: 0.86rem;
            min-width: 76px;
        }

        .service-meta small {
            display: block;
            color: #8a8084;
            margin-top: 0.2rem;
            font-size: 0.8rem;
        }

        .panel-footer {
            margin-top: 1.3rem;
            border-top: 1px solid var(--line);
            padding-top: 1rem;
            display: flex;
            justify-content: flex-end;
        }

        .next-btn {
            border: none;
            border-radius: 0;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            font-size: 0.73rem;
            font-weight: 700;
            min-width: 182px;
            padding: 0.82rem 1.2rem;
        }

        .next-btn:hover {
            background: #653f4f;
            color: #fff;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid var(--line);
                padding-bottom: 1rem;
            }

            .main-panel {
                padding: 1.5rem 1rem 2rem;
            }
        }
    </style>
</head>
<body>
<div class="booking-shell">
    <header class="top-header d-flex align-items-center">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
            <a class="logo" href="index.php?page=home">Merish</a>
            <a href="index.php?page=home" class="btn exit-btn">Exit Booking</a>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row g-0">
            <aside class="col-lg-3 col-xl-2 sidebar">
                <ul class="step-nav">
                    <li><a class="step-item active" href="index.php?page=booking&step=1">▴ <span>Category</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=2">✧ <span>Bundle</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=3">◷ <span>Schedule</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=4">◉ <span>Stylist</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=5">▣ <span>Checkout</span></a></li>
                    <li><span class="step-item disabled">◌ <span>Confirm</span></span></li>
                </ul>
            </aside>

            <main class="col-lg-9 col-xl-10 main-panel">
                <h1 class="main-title">Select Category</h1>
                <p class="main-subtitle">Choose the primary focus for your session today.</p>

                <div class="row g-3">
                    <?php foreach ($categories as $key => $cat): ?>
                        <div class="col-md-6">
                            <div class="category-card <?php echo $activeCategory === $key ? 'active' : ''; ?>" data-category="<?php echo $key; ?>">
                                <div class="category-visual <?php echo $cat['visual']; ?>">
                                    <span class="check-mark">✓</span>
                                </div>
                                <div class="category-body">
                                    <p class="category-name"><?php echo htmlspecialchars($cat['label']); ?></p>
                                    <p class="category-sub"><?php echo htmlspecialchars($cat['subtitle']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="services-head">
                    <h2 id="serviceCategoryTitle"><?php echo htmlspecialchars($categories[$activeCategory]['label']); ?> Services</h2>
                    <span class="step-chip">Step 1.2</span>
                </div>

                <form method="POST" action="/index.php?page=booking&step=1" id="bookingStep1Form">
                    <div id="serviceList">
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <?php
                                $serviceId = (string)($service['service_id'] ?? '');
                                $serviceCategory = $normalizeCategory($service['category'] ?? '');
                                $checked = $serviceId === $selectedServiceId ? 'checked' : '';
                                $isActive = $checked !== '' ? 'is-active' : '';
                                $price = (float)($service['base_tariff'] ?? 0);
                                $duration = (int)($service['est_duration'] ?? 0);
                                ?>
                                <label class="service-option <?php echo $isActive; ?>" data-service-category="<?php echo $serviceCategory; ?>">
                                    <div class="row g-2 align-items-start">
                                        <div class="col-1 d-flex justify-content-center pt-1">
                                            <input type="radio" class="form-check-input" name="service_id" value="<?php echo htmlspecialchars($serviceId); ?>" <?php echo $checked; ?> required>
                                        </div>
                                        <div class="col-8 col-md-9">
                                            <p class="service-name">
                                                <?php echo htmlspecialchars($service['service_name'] ?? 'Service'); ?>
                                                <?php if (!empty($service['promo'])): ?><span class="promo-tag">Promo Available</span><?php endif; ?>
                                            </p>
                                            <p class="service-desc"><?php echo htmlspecialchars($service['description'] ?? 'Professional service with premium treatment quality.'); ?></p>
                                        </div>
                                        <div class="col-3 col-md-2 service-meta">
                                            $<?php echo number_format($price, 0); ?>
                                            <small><?php echo $duration > 0 ? $duration . ' mins' : '60 mins'; ?></small>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">Belum ada data layanan tersedia.</div>
                        <?php endif; ?>
                    </div>

                    <div class="panel-footer">
                        <button type="submit" class="btn next-btn">Next Step →</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const categoryCards = document.querySelectorAll('.category-card');
    const serviceRows = document.querySelectorAll('[data-service-category]');
    const categoryTitle = document.getElementById('serviceCategoryTitle');
    const radios = document.querySelectorAll('input[name="service_id"]');
    const categoryLabels = {
        hair: 'Hair Services',
        nails: 'Nails Services',
        lashes: 'Lashes Services',
        wax: 'Wax & Brows Services'
    };

    function updateActiveOptionStyle() {
        document.querySelectorAll('.service-option').forEach((row) => row.classList.remove('is-active'));
        const checked = document.querySelector('input[name="service_id"]:checked');
        if (checked) {
            checked.closest('.service-option')?.classList.add('is-active');
        }
    }

    function filterServices(category) {
        categoryTitle.textContent = categoryLabels[category] || 'Services';
        serviceRows.forEach((row) => {
            row.style.display = row.dataset.serviceCategory === category ? '' : 'none';
        });

        const checked = document.querySelector('input[name="service_id"]:checked');
        if (checked && checked.closest('.service-option')?.dataset.serviceCategory !== category) {
            checked.checked = false;
        }
        updateActiveOptionStyle();
    }

    categoryCards.forEach((card) => {
        card.addEventListener('click', () => {
            categoryCards.forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            filterServices(card.dataset.category);
        });
    });

    radios.forEach((radio) => {
        radio.addEventListener('change', updateActiveOptionStyle);
    });

    filterServices('<?php echo $activeCategory; ?>');
    updateActiveOptionStyle();
</script>
</body>
</html>
