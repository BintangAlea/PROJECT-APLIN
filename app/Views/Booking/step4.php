<?php
$beauticians = $beauticians ?? [];
$selectedCategory = $selected_category ?? 'hair';
$selectedCategoryLabel = $selected_category_label ?? 'Hair';
$selectedServiceName = $selected_service_name ?? 'Signature Service';
$selectedDate = $selected_date ?? date('Y-m-d');
$selectedTime = $selected_time ?? '10:00';
$durationMinutes = (int) ($duration_minutes ?? 60);

$categoryButtons = [
    'hair' => 'Hair',
    'nails' => 'Nails',
    'lashes' => 'Lashes',
    'wax' => 'Wax & Eyebrows',
];

$staffCards = [];
foreach ($beauticians as $b) {
    $specialization = (string) ($b['specialization'] ?? 'Hair Stylist');
    $category = (string) ($b['category'] ?? 'hair');

    $staffCards[] = [
        'id' => (string) ($b['user_id'] ?? ''),
        'name' => (string) ($b['name'] ?? 'Staff'),
        'role' => strtoupper($specialization),
        'category' => $category,
        'available' => true,
        'avatar' => 'avatar-' . ((int) ($b['user_id'] ?? 0) % 3),
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Stylist - Booking | Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f3efef;
            --surface: #f6f2f2;
            --line: #d7ccce;
            --ink: #584b51;
            --muted: #7d7276;
            --accent: #7a4f61;
            --accent-soft: #e8d6e7;
            --card: #faf8f8;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
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
            min-height: calc(100vh - 74px);
            background: var(--bg);
            padding: 2rem 1.6rem 5rem;
        }

        .title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.3rem, 4vw, 4.2rem);
            margin-bottom: 0.35rem;
            color: #44383e;
            line-height: 1.08;
        }

        .subtitle {
            color: #766a6f;
            margin-bottom: 1.2rem;
            max-width: 680px;
        }

        .filter-tabs {
            display: flex;
            gap: 0.45rem;
            flex-wrap: wrap;
            margin-bottom: 1.1rem;
        }

        .filter-btn {
            border: 1px solid #d2c5ca;
            background: #f8f5f5;
            color: #695d62;
            border-radius: 0;
            font-size: 0.72rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 600;
            padding: 0.36rem 0.72rem;
        }

        .filter-btn.active,
        .filter-btn:hover {
            border-color: #d6b9ca;
            background: var(--accent-soft);
            color: #7a4f61;
        }

        .staff-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.9rem;
        }

        .staff-card {
            border: 1px solid #d3c8cb;
            background: var(--card);
            min-height: 304px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .staff-visual {
            height: 210px;
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid #ddd2d5;
            filter: grayscale(100%);
        }

        .avatar-0 {
            background-image: linear-gradient(140deg, #c7c9cf 0%, #8f949d 100%);
        }

        .avatar-1 {
            background-image: linear-gradient(140deg, #d8d9de 0%, #a9acb4 100%);
        }

        .avatar-2 {
            background-image: linear-gradient(140deg, #a4a8b0 0%, #7d828d 100%);
        }

        .staff-info {
            padding: 0.78rem 0.88rem 0.85rem;
        }

        .staff-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            line-height: 1.08;
            color: #4f4148;
            margin: 0;
        }

        .staff-role {
            margin-top: 0.15rem;
            color: #7d7276;
            font-size: 0.74rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .select-btn {
            position: absolute;
            right: 0.85rem;
            bottom: 0.85rem;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid #bcaab1;
            background: #f7f4f5;
            color: #7b4f61;
            font-size: 1.05rem;
            line-height: 1;
        }

        .select-btn:hover {
            background: #f0e6ea;
            border-color: #a98d99;
        }

        .auto-card {
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 1rem;
        }

        .auto-icon {
            width: 74px;
            height: 74px;
            border-radius: 12px;
            background: #d9afbf;
            color: #6d4757;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 0.8rem;
        }

        .auto-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 0.35rem;
            color: #4f4148;
            line-height: 1.08;
        }

        .auto-sub {
            color: #7c7176;
            font-size: 0.86rem;
            max-width: 210px;
            margin: 0 auto;
        }

        .unavailable {
            opacity: 0.35;
        }

        .unavailable-overlay {
            position: absolute;
            top: 38%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #f6f3f4;
            border: 1px solid #ddd2d5;
            color: #7a6f73;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 0.35rem 0.7rem;
        }

        .footer-line {
            margin-top: 1.2rem;
            background: #eae4e4;
            border-top: 1px solid #d5cbcd;
            min-height: 58px;
            display: flex;
            align-items: center;
        }

        .footer-inner {
            width: 100%;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: #7d7276;
            font-size: 0.78rem;
        }

        .footer-links {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #7d7276;
            text-decoration: none;
        }

        @media (max-width: 1199.98px) {
            .staff-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid var(--line);
            }

            .main-panel {
                padding: 1.4rem 0.85rem 2rem;
            }

            .staff-grid {
                grid-template-columns: 1fr;
            }

            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
                padding: 0.7rem 1rem;
            }
        }
    </style>
</head>
<body>
<div class="booking-shell">
    <header class="top-header d-flex align-items-center">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
            <a class="logo" href="index.php?page=home">Merish</a>
            <a href="index.php?page=home" class="btn btn-sm" style="border:1px solid #b69fa8;color:#6b5961;border-radius:0;letter-spacing:1.2px;text-transform:uppercase;font-size:.68rem;font-weight:700;">Exit Booking</a>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row g-0">
            <aside class="col-lg-3 col-xl-2 sidebar">
                <ul class="step-nav">
                    <li><a class="step-item" href="index.php?page=booking&step=1">▵ <span>Category</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=2">✧ <span>Bundle</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=3">◷ <span>Schedule</span></a></li>
                    <li><a class="step-item active" href="index.php?page=booking&step=4">◉ <span>Stylist</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=5">▣ <span>Checkout</span></a></li>
                    <li><span class="step-item disabled">◌ <span>Confirm</span></span></li>
                </ul>
            </aside>

            <main class="col-lg-9 col-xl-10 main-panel">
                <h1 class="title">Select Your Stylist</h1>
                <p class="subtitle"><?php echo htmlspecialchars($selectedServiceName); ?> · <?php echo htmlspecialchars($selectedCategoryLabel); ?> · <?php echo htmlspecialchars($selectedDate); ?> at <?php echo htmlspecialchars($selectedTime); ?> (<?php echo (int) $durationMinutes; ?> mins)</p>

                <div class="filter-tabs" id="filterTabs">
                    <?php foreach ($categoryButtons as $key => $label): ?>
                        <button type="button" class="btn filter-btn <?php echo $selectedCategory === $key ? 'active' : ''; ?>" data-filter="<?php echo htmlspecialchars($key); ?>"><?php echo $selectedCategory === $key ? '✓ ' : ''; ?><?php echo htmlspecialchars($label); ?></button>
                    <?php endforeach; ?>
                </div>

                <form method="POST" action="/index.php?page=booking&step=4" id="stylistForm">
                    <input type="hidden" name="beautician_id" id="beautician_id" value="">

                    <div class="staff-grid" id="staffGrid">
                        <article class="staff-card auto-card" data-category="hair nails lashes wax" data-available="true">
                            <div class="auto-icon">🗂</div>
                            <h2 class="auto-title">Any Available Staff</h2>
                            <p class="auto-sub">Let us match you with the first available expert for your requested time.</p>
                            <button type="button" class="select-btn" data-select-id="">→</button>
                        </article>

                        <?php if (empty($staffCards)): ?>
                            <div class="alert alert-light border mt-3 mb-0">Belum ada beautician online yang cocok untuk kategori <?php echo htmlspecialchars($selectedCategoryLabel); ?>.</div>
                        <?php else: ?>
                            <?php foreach ($staffCards as $staff): ?>
                                <article class="staff-card <?php echo !$staff['available'] ? 'unavailable' : ''; ?>" data-category="<?php echo htmlspecialchars($staff['category']); ?>" data-available="<?php echo $staff['available'] ? 'true' : 'false'; ?>">
                                    <div class="staff-visual <?php echo htmlspecialchars($staff['avatar']); ?>"></div>
                                    <?php if (!$staff['available']): ?>
                                        <div class="unavailable-overlay">Not Available</div>
                                    <?php endif; ?>
                                    <div class="staff-info">
                                        <h2 class="staff-name"><?php echo htmlspecialchars($staff['name']); ?></h2>
                                        <p class="staff-role"><?php echo htmlspecialchars($staff['role']); ?></p>
                                    </div>
                                    <?php if ($staff['available']): ?>
                                        <button type="button" class="select-btn" data-select-id="<?php echo htmlspecialchars($staff['id']); ?>">→</button>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <div class="footer-line">
        <div class="footer-inner">
            <strong style="font-family:'Playfair Display',serif;color:#6f505f;font-size:1.9rem;line-height:1;">Merish</strong>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
                <a href="#">Location</a>
            </div>
            <span>© 2024 Merish Beauty Studio. All rights reserved.</span>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const filterButtons = document.querySelectorAll('.filter-btn');
    const staffCards = document.querySelectorAll('#staffGrid .staff-card');
    const beauticianInput = document.getElementById('beautician_id');
    const stylistForm = document.getElementById('stylistForm');

    function applyFilter(filter) {
        staffCards.forEach((card) => {
            const categories = card.dataset.category || '';
            const isAvailable = card.dataset.available === 'true';
            const show = categories.includes(filter);
            card.style.display = show ? '' : 'none';

            if (!isAvailable && show) {
                card.style.display = '';
            }
        });
    }

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            filterButtons.forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
            applyFilter(button.dataset.filter);
        });
    });

    document.querySelectorAll('[data-select-id]').forEach((button) => {
        button.addEventListener('click', () => {
            beauticianInput.value = button.dataset.selectId;
            stylistForm.submit();
        });
    });

    applyFilter('<?php echo htmlspecialchars($selectedCategory); ?>');
</script>
</body>
</html>
