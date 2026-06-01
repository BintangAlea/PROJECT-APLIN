<?php
$booking = $booking ?? [];
$minDate = $min_date ?? date('Y-m-d', strtotime('+1 day'));
$maxDate = $max_date ?? date('Y-m-d', strtotime('+30 days'));
$vipAccessEnabled = (bool) ($vip_access_enabled ?? false);
$memberName = $member_name ?? 'Guest';
$selectedDate = $booking['reservation_date'] ?? $minDate;
$selectedTime = $booking['reservation_time'] ?? '10:00';

$startDate = new DateTime($minDate);
$dayOptions = [];
for ($i = 0; $i < 7; $i++) {
    $day = clone $startDate;
    $day->modify("+{$i} day");
    if ($day->format('Y-m-d') > $maxDate) {
        break;
    }
    $dayOptions[] = $day;
}

$monthLabel = !empty($dayOptions) ? $dayOptions[0]->format('F Y') : date('F Y');
$serviceLabel = !empty($booking['service_id']) ? ('Service #' . htmlspecialchars((string)$booking['service_id'])) : 'Signature Look';
$addonLabel = !empty($booking['bundle_id']) ? ('Bundle #' . htmlspecialchars((string)$booking['bundle_id'])) : 'Add-on';

$morningSlots = [
    ['time' => '09:00', 'label' => '09:00 AM', 'disabled' => false],
    ['time' => '09:30', 'label' => '09:30 AM', 'disabled' => false],
    ['time' => '10:00', 'label' => '10:00 AM', 'disabled' => false],
    ['time' => '10:30', 'label' => '10:30 AM', 'disabled' => true],
    ['time' => '11:00', 'label' => '11:00 AM', 'disabled' => true],
    ['time' => '11:30', 'label' => '11:30 AM', 'disabled' => false],
];

$afternoonSlots = [
    ['time' => '12:00', 'label' => '12:00 PM', 'disabled' => false],
    ['time' => '12:30', 'label' => '12:30 PM', 'disabled' => false],
    ['time' => '13:00', 'label' => '01:00 PM', 'disabled' => false],
    ['time' => '13:30', 'label' => '01:30 PM', 'disabled' => true],
    ['time' => '14:00', 'label' => '02:00 PM', 'disabled' => false],
    ['time' => '14:30', 'label' => '02:30 PM', 'disabled' => false],
    ['time' => '15:00', 'label' => '03:00 PM', 'disabled' => false],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Date & Time - Booking | Merish</title>
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

        .panel-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 3.8vw, 3.8rem);
            margin-bottom: 0.45rem;
            color: #483c42;
        }

        .panel-subtitle {
            color: #766a6f;
            margin-bottom: 1.25rem;
            max-width: 620px;
        }

        .vip-note {
            background: #f5f1f2;
            border: 1px solid #d5cbcd;
            border-radius: 4px;
            padding: 0.75rem 0.85rem;
            margin-bottom: 1rem;
            color: #6e6166;
            font-size: 0.84rem;
        }

        .card-soft {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 4px;
            padding: 0.9rem;
            margin-bottom: 0.9rem;
        }

        .month-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.8rem;
        }

        .month-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin: 0;
            color: #5a474f;
        }

        .icon-btn {
            width: 26px;
            height: 26px;
            border: 1px solid #d8cccf;
            border-radius: 50%;
            background: transparent;
            color: #7a6f73;
            line-height: 1;
            font-size: 0.8rem;
            margin-left: 0.3rem;
        }

        .day-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .day-btn {
            border: 1px solid #d7ccce;
            background: #f7f4f4;
            border-radius: 2px;
            padding: 0.5rem 0.2rem;
            min-height: 74px;
            text-align: center;
            color: #6f6368;
            transition: all 0.2s ease;
        }

        .day-btn .dow {
            display: block;
            text-transform: uppercase;
            font-size: 0.62rem;
            letter-spacing: 1px;
            margin-bottom: 0.22rem;
            font-weight: 700;
        }

        .day-btn .dom {
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
            color: #57474d;
            line-height: 1;
            display: block;
        }

        .day-btn.active {
            background: #f3e7ec;
            border-color: #8f6a7a;
            box-shadow: inset 0 0 0 1px #8f6a7a;
        }

        .slot-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
        }

        .slot-title {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #5a474f;
        }

        .tz {
            color: #7d7377;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .period {
            margin-top: 0.85rem;
            margin-bottom: 0.4rem;
            color: #7d7276;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .time-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.5rem;
        }

        .time-btn {
            border: 1px solid #d7cbce;
            background: #fbf9f9;
            border-radius: 2px;
            color: #6c6165;
            font-size: 0.77rem;
            font-weight: 600;
            padding: 0.52rem 0.35rem;
            transition: all 0.2s ease;
        }

        .time-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .time-btn.disabled {
            opacity: 0.35;
            text-decoration: line-through;
            pointer-events: none;
        }

        .summary-card {
            background: #f7f3f3;
            border: 1px solid #d6cbcd;
            border-radius: 2px;
            padding: 0.95rem;
            margin-bottom: 0.9rem;
        }

        .summary-title {
            text-align: center;
            color: #7c7074;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.55rem;
            color: #5e5358;
            font-size: 0.86rem;
        }

        .summary-date {
            border: 1px solid #ddd3d5;
            background: #f9f5f6;
            border-radius: 3px;
            padding: 0.58rem;
            margin: 0.8rem 0;
            color: #6f6368;
            font-size: 0.8rem;
        }

        .due-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #dbd0d2;
            padding-top: 0.65rem;
        }

        .due-row .label {
            color: #7f7277;
            font-size: 0.73rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        .due-row .value {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: #4e4147;
            line-height: 1;
        }

        .continue-btn {
            width: 100%;
            border: none;
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            font-size: 0.71rem;
            font-weight: 700;
            border-radius: 0;
            padding: 0.72rem;
            margin-bottom: 0.45rem;
        }

        .continue-btn:hover {
            background: #674151;
            color: #fff;
        }

        .modify-btn {
            width: 100%;
            border: 1px solid #cbbdc2;
            background: transparent;
            color: #6a5f63;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 0;
            padding: 0.65rem;
        }

        .footer-line {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: #eae4e4;
            border-top: 1px solid #d5cbcd;
            min-height: 58px;
            display: flex;
            align-items: center;
            z-index: 10;
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

        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid var(--line);
            }

            .main-panel {
                padding: 1.4rem 0.85rem 6rem;
            }

            .time-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .day-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .footer-line {
                position: static;
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
                    <li><a class="step-item active" href="index.php?page=booking&step=3">◷ <span>Schedule</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=4">◉ <span>Stylist</span></a></li>
                    <li><a class="step-item" href="index.php?page=booking&step=5">▣ <span>Checkout</span></a></li>
                    <li><span class="step-item disabled">◌ <span>Confirm</span></span></li>
                </ul>
            </aside>

            <main class="col-lg-9 col-xl-10 main-panel">
                <form method="POST" action="/index.php?page=booking&step=3" id="scheduleForm">
                    <input type="hidden" name="reservation_date" id="reservation_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                    <input type="hidden" name="reservation_time" id="reservation_time" value="<?php echo htmlspecialchars($selectedTime); ?>">

                    <div class="row g-4">
                        <div class="col-xl-8">
                            <h1 class="panel-title">Select Date & Time</h1>
                            <p class="panel-subtitle">Choose a convenient slot for your Signature Style Session. Availability is shown in your local timezone.</p>

                            <?php if ($vipAccessEnabled): ?>
                                <div class="vip-note">★ <strong>VIP Member Access Unlocked</strong><br><small>Hi, <?php echo htmlspecialchars($memberName); ?>. You have extended 14-day booking availability.</small></div>
                            <?php endif; ?>

                            <div class="card-soft">
                                <div class="month-header">
                                    <h2 class="month-title"><?php echo htmlspecialchars($monthLabel); ?></h2>
                                    <div>
                                        <button type="button" class="icon-btn">‹</button>
                                        <button type="button" class="icon-btn">›</button>
                                    </div>
                                </div>
                                <div class="day-grid" id="dayGrid">
                                    <?php foreach ($dayOptions as $day): ?>
                                        <?php
                                        $dayValue = $day->format('Y-m-d');
                                        $isActive = $dayValue === $selectedDate;
                                        ?>
                                        <button type="button" class="day-btn <?php echo $isActive ? 'active' : ''; ?>" data-date="<?php echo $dayValue; ?>">
                                            <span class="dow"><?php echo htmlspecialchars($day->format('D')); ?></span>
                                            <span class="dom"><?php echo htmlspecialchars($day->format('d')); ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="card-soft">
                                <div class="slot-head">
                                    <h2 class="slot-title">Available Times</h2>
                                    <span class="tz">EST (UTC-5)</span>
                                </div>

                                <p class="period">Morning</p>
                                <div class="time-grid mb-2" id="morningGrid">
                                    <?php foreach ($morningSlots as $slot): ?>
                                        <?php
                                        $activeClass = $slot['time'] === $selectedTime ? 'active' : '';
                                        $disabledClass = $slot['disabled'] ? 'disabled' : '';
                                        ?>
                                        <button type="button" class="time-btn <?php echo $activeClass . ' ' . $disabledClass; ?>" data-time="<?php echo htmlspecialchars($slot['time']); ?>"><?php echo htmlspecialchars($slot['label']); ?></button>
                                    <?php endforeach; ?>
                                </div>

                                <p class="period">Afternoon</p>
                                <div class="time-grid" id="afternoonGrid">
                                    <?php foreach ($afternoonSlots as $slot): ?>
                                        <?php
                                        $activeClass = $slot['time'] === $selectedTime ? 'active' : '';
                                        $disabledClass = $slot['disabled'] ? 'disabled' : '';
                                        ?>
                                        <button type="button" class="time-btn <?php echo $activeClass . ' ' . $disabledClass; ?>" data-time="<?php echo htmlspecialchars($slot['time']); ?>"><?php echo htmlspecialchars($slot['label']); ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="summary-card">
                                <div class="summary-title">Booking Summary</div>
                                <div class="summary-row"><span><?php echo $serviceLabel; ?></span><span>Rp800.000</span></div>
                                <div class="summary-row"><span><?php echo $addonLabel; ?></span><span>Rp150.000</span></div>
                                <div class="summary-date" id="summaryDate">📅 <?php echo htmlspecialchars(date('D, M d, Y', strtotime($selectedDate))); ?><br>🕒 <span id="summaryTime"><?php echo htmlspecialchars(date('h:i A', strtotime($selectedTime))); ?></span> - 11:30 AM</div>
                                <div class="due-row">
                                    <span class="label">Total Due Today</span>
                                    <span class="value">Rp950.000</span>
                                </div>
                            </div>

                            <button type="submit" class="btn continue-btn">Continue to Stylist</button>
                            <a href="index.php?page=booking&step=1" class="btn modify-btn">Modify Selection</a>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <div class="footer-line">
        <div class="footer-inner">
            <a href="index.php?page=booking&step=2" style="text-decoration:none;color:#7d7276;">← Back</a>
            <div class="footer-links">
                <strong style="font-family:'Playfair Display',serif;color:#6f505f;font-size:1.35rem;line-height:1;">Merish</strong>
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
    const dateInput = document.getElementById('reservation_date');
    const timeInput = document.getElementById('reservation_time');
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');

    document.querySelectorAll('.day-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.day-btn').forEach((item) => item.classList.remove('active'));
            btn.classList.add('active');
            const selected = btn.dataset.date;
            dateInput.value = selected;

            const dateObj = new Date(selected + 'T00:00:00');
            const label = dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: '2-digit', year: 'numeric' });
            summaryDate.innerHTML = '📅 ' + label + '<br>🕒 <span id="summaryTime">' + summaryTime.textContent + '</span> - 11:30 AM';
        });
    });

    document.querySelectorAll('.time-btn').forEach((btn) => {
        if (btn.classList.contains('disabled')) {
            return;
        }

        btn.addEventListener('click', () => {
            document.querySelectorAll('.time-btn').forEach((item) => item.classList.remove('active'));
            btn.classList.add('active');
            const selected = btn.dataset.time;
            timeInput.value = selected;

            const parts = selected.split(':');
            const dt = new Date();
            dt.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10));
            const formatted = dt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            summaryTime.textContent = formatted;
        });
    });
</script>
</body>
</html>
