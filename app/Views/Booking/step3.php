<?php
date_default_timezone_set('Asia/Jakarta');
$booking = $booking ?? [];
$minDate = $min_date ?? date('Y-m-d');
$bookingWindowDays = max(1, (int) ($booking_window_days ?? 1));
$maxDate = $max_date ?? date('Y-m-d', strtotime('+' . $bookingWindowDays . ' days'));
$vipAccessEnabled = (bool) ($vip_access_enabled ?? false);
$memberName = $member_name ?? 'Guest';
$memberTierName = $member_tier_name ?? 'Guest';
$selectedDate = $selected_date ?? ($booking['reservation_date'] ?? $minDate);
$selectedTime = $booking['reservation_time'] ?? '10:00';
$pricing = $pricing ?? [];
$durationMinutes = (int) ($duration_minutes ?? 60);

$viewMonth = (int) ($view_month ?? date('m'));
$viewYear = (int) ($view_year ?? date('Y'));

$firstDayOfMonth = new DateTime("$viewYear-$viewMonth-01");
$daysInMonth = (int)$firstDayOfMonth->format('t');
$monthLabel = $firstDayOfMonth->format('F Y');

// Weekday of the first day (0 for Sunday, 6 for Saturday)
$firstWeekday = (int)$firstDayOfMonth->format('w');

// Previous Month Info for padding
$prevMonthDate = clone $firstDayOfMonth;
$prevMonthDate->modify('-1 month');
$prevMonth = (int)$prevMonthDate->format('m');
$prevYear = (int)$prevMonthDate->format('Y');
$daysInPrevMonth = (int)$prevMonthDate->format('t');

// Next Month Info for padding
$nextMonthDate = clone $firstDayOfMonth;
$nextMonthDate->modify('+1 month');
$nextMonth = (int)$nextMonthDate->format('m');
$nextYear = (int)$nextMonthDate->format('Y');

// Construct calendar grid cells (Sunday to Saturday)
$gridCells = [];

// 1. Previous month padding cells
for ($i = $firstWeekday - 1; $i >= 0; $i--) {
    $dNum = $daysInPrevMonth - $i;
    $dVal = sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $dNum);
    $gridCells[] = [
        'date' => $dVal,
        'day_num' => $dNum,
        'current_month' => false,
        'disabled' => true
    ];
}

// 2. Current month cells
for ($dNum = 1; $dNum <= $daysInMonth; $dNum++) {
    $dVal = sprintf('%04d-%02d-%02d', $viewYear, $viewMonth, $dNum);
    $disabled = false;
    
    // Check if in the past
    if ($dVal < date('Y-m-d')) {
        $disabled = true;
    }
    // Check if beyond max date
    if ($dVal > $maxDate) {
        $disabled = true;
    }
    
    $gridCells[] = [
        'date' => $dVal,
        'day_num' => $dNum,
        'current_month' => true,
        'disabled' => $disabled
    ];
}

// 3. Next month padding cells to complete multiple of 7
$totalCells = count($gridCells);
$rem = 7 - ($totalCells % 7);
if ($rem < 7) {
    for ($dNum = 1; $dNum <= $rem; $dNum++) {
        $dVal = sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $dNum);
        $gridCells[] = [
            'date' => $dVal,
            'day_num' => $dNum,
            'current_month' => false,
            'disabled' => true
        ];
    }
}

// Enable/Disable navigation buttons
$currentMonthYear = date('Y-m');
$viewMonthYear = sprintf('%04d-%02d', $viewYear, $viewMonth);
$maxMonthYear = date('Y-m', strtotime($maxDate));

$prevMonthDisabled = $viewMonthYear <= $currentMonthYear;
$nextMonthDisabled = $viewMonthYear >= $maxMonthYear;

$prevMonthUrl = $prevMonthDisabled ? 'javascript:void(0);' : "index.php?page=booking&step=3&date=" . urlencode($selectedDate) . "&view_month=" . $prevMonth . "&view_year=" . $prevYear;
$nextMonthUrl = $nextMonthDisabled ? 'javascript:void(0);' : "index.php?page=booking&step=3&date=" . urlencode($selectedDate) . "&view_month=" . $nextMonth . "&view_year=" . $nextYear;
$serviceLabel = !empty($booking['service_id']) ? ('Service #' . htmlspecialchars((string)$booking['service_id'])) : 'Signature Look';
$addonLabel = !empty($booking['bundle_id']) ? ('Bundle #' . htmlspecialchars((string)$booking['bundle_id'])) : 'Add-on';

$morningSlots = [
    ['time' => '09:00', 'label' => '09:00 AM', 'disabled' => false],
    ['time' => '09:30', 'label' => '09:30 AM', 'disabled' => false],
    ['time' => '10:00', 'label' => '10:00 AM', 'disabled' => false],
    ['time' => '10:30', 'label' => '10:30 AM', 'disabled' => false],
    ['time' => '11:00', 'label' => '11:00 AM', 'disabled' => false],
    ['time' => '11:30', 'label' => '11:30 AM', 'disabled' => false],
];

$afternoonSlots = [
    ['time' => '12:00', 'label' => '12:00 PM', 'disabled' => false],
    ['time' => '12:30', 'label' => '12:30 PM', 'disabled' => false],
    ['time' => '13:00', 'label' => '01:00 PM', 'disabled' => false],
    ['time' => '13:30', 'label' => '01:30 PM', 'disabled' => false],
    ['time' => '14:00', 'label' => '02:00 PM', 'disabled' => false],
    ['time' => '14:30', 'label' => '02:30 PM', 'disabled' => false],
    ['time' => '15:00', 'label' => '03:00 PM', 'disabled' => false],
];

// DYNAMIC SLOT EVALUATION (SMART SCHEDULING)
$db = \App\Core\Database::getConnection();

// Normalize category
$category = 'hair';
if (!empty($booking['service_id'])) {
    $stmtS = $db->prepare("SELECT category FROM services WHERE service_id = :id");
    $stmtS->execute([':id' => $booking['service_id']]);
    $catVal = $stmtS->fetchColumn();
    if ($catVal) {
        $catNormalized = strtolower(trim($catVal));
        $category = match ($catNormalized) {
            'hair' => 'hair',
            'nails' => 'nails',
            'lashes' => 'lashes',
            'wax & eyebrows', 'wax', 'eyebrows', 'wax and eyebrows' => 'wax',
            default => 'hair',
        };
    }
}

// Helpers for checking slots on this selected date
$getAvailSeats = function(string $time, int $dur) use ($db, $selectedDate) {
    $startStr = $selectedDate . ' ' . $time . ':00';
    $endStr = date('Y-m-d H:i:s', strtotime($startStr) + ($dur * 60));
    
    $stmtSeats = $db->query("SELECT seat_id FROM seats WHERE zone_type = 'Kursi Salon'");
    $allSeats = $stmtSeats->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
    $stmtOccupied = $db->prepare("
        SELECT DISTINCT r.seat_id 
        FROM reservations r
        WHERE r.status IN ('Pending', 'Confirmed', 'In-Service')
          AND :new_start < DATE_ADD(r.schedule_time, INTERVAL (
              SELECT COALESCE(SUM(s.est_duration), 60) 
              FROM reservation_details rd 
              JOIN services s ON rd.service_id = s.service_id 
              WHERE rd.res_id = r.res_id
          ) MINUTE)
          AND :new_end > r.schedule_time
    ");
    $stmtOccupied->execute([
        ':new_start' => $startStr,
        ':new_end' => $endStr
    ]);
    $occupiedSeatIds = $stmtOccupied->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
    return count(array_diff($allSeats, $occupiedSeatIds)) > 0;
};

$getSpecializationsForCategory = function(string $cat) {
    return match ($cat) {
        'nails' => ['Nailist'],
        'lashes' => ['Lash Technician'],
        'wax' => ['Wax & Threading Specialist'],
        default => ['Hair Stylist'],
    };
};

$getAvailBeauticians = function(string $time, int $dur) use ($db, $selectedDate, $category, $getSpecializationsForCategory) {
    $specializations = $getSpecializationsForCategory($category);
    $placeholders = implode(',', array_fill(0, count($specializations), '?'));

    $countStmt = $db->prepare("SELECT COUNT(*) FROM staff_profiles WHERE specialization IN ({$placeholders})");
    $countStmt->execute($specializations);
    $totalMatching = (int)$countStmt->fetchColumn();

    if ($totalMatching === 0) {
        return true; // No staff of this specialization registered at all, don't block
    }

    $stmt = $db->prepare(
        "SELECT u.user_id
         FROM staff_profiles sp
         JOIN users u ON sp.user_id = u.user_id
         WHERE sp.work_status = 'Online'
           AND u.ROLE = 'Beautician'
           AND sp.specialization IN ({$placeholders})
         ORDER BY u.NAME ASC"
    );
    $stmt->execute($specializations);
    $candidates = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    if (empty($candidates)) {
        return false;
    }

    $busyStmt = $db->prepare(
        "SELECT rd.beautician_id,
                r.schedule_time,
                COALESCE(s.est_duration, 60) AS est_duration
         FROM reservations r
         JOIN reservation_details rd ON rd.res_id = r.res_id
         JOIN services s ON s.service_id = rd.service_id
         WHERE DATE(r.schedule_time) = :date
           AND r.STATUS IN ('Pending', 'Confirmed', 'In-Service')
           AND rd.beautician_id IS NOT NULL"
    );
    $busyStmt->execute([':date' => $selectedDate]);
    $busyRows = $busyStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $requestStart = new \DateTimeImmutable($selectedDate . ' ' . $time);
    $requestEnd = $requestStart->modify('+' . max(30, $dur) . ' minutes');
    $busyBeauticians = [];

    foreach ($busyRows as $busyRow) {
        $busyBeauticianId = (int) ($busyRow['beautician_id'] ?? 0);
        if ($busyBeauticianId <= 0) {
            continue;
        }

        $busyStart = new \DateTimeImmutable((string) ($busyRow['schedule_time'] ?? $selectedDate . ' 00:00:00'));
        $busyEnd = $busyStart->modify('+' . max(30, (int) ($busyRow['est_duration'] ?? 60)) . ' minutes');

        if ($requestStart < $busyEnd && $requestEnd > $busyStart) {
            $busyBeauticians[$busyBeauticianId] = true;
        }
    }

    $availableCount = 0;
    foreach ($candidates as $candId) {
        if (!isset($busyBeauticians[(int)$candId])) {
            $availableCount++;
        }
    }

    return $availableCount > 0;
};

foreach ($morningSlots as &$slot) {
    $endTimestamp = strtotime($selectedDate . ' ' . $slot['time']) + ($durationMinutes * 60);
    $endTimeFormatted = date('H:i', $endTimestamp);
    if ($selectedDate === date('Y-m-d') && $slot['time'] <= date('H:i')) {
        $slot['disabled'] = true;
    } elseif ($endTimeFormatted > '18:00') {
        $slot['disabled'] = true;
    } else {
        $slot['disabled'] = !$getAvailSeats($slot['time'], $durationMinutes) || !$getAvailBeauticians($slot['time'], $durationMinutes);
    }
}
unset($slot);

foreach ($afternoonSlots as &$slot) {
    $endTimestamp = strtotime($selectedDate . ' ' . $slot['time']) + ($durationMinutes * 60);
    $endTimeFormatted = date('H:i', $endTimestamp);
    if ($selectedDate === date('Y-m-d') && $slot['time'] <= date('H:i')) {
        $slot['disabled'] = true;
    } elseif ($endTimeFormatted > '18:00') {
        $slot['disabled'] = true;
    } else {
        $slot['disabled'] = !$getAvailSeats($slot['time'], $durationMinutes) || !$getAvailBeauticians($slot['time'], $durationMinutes);
    }
}
unset($slot);

$allSlots = array_merge($morningSlots, $afternoonSlots);
$selectedTimeIsAvailable = false;
foreach ($allSlots as $s) {
    if ($s['time'] === $selectedTime && !$s['disabled']) {
        $selectedTimeIsAvailable = true;
        break;
    }
}

if (!$selectedTimeIsAvailable) {
    foreach ($allSlots as $s) {
        if (!$s['disabled']) {
            $selectedTime = $s['time'];
            break;
        }
    }
}
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

        .weekday-header {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.55rem;
            text-align: center;
            font-weight: 700;
            font-size: 0.72rem;
            color: #7a6f73;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #6f6368;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .day-btn .dom {
            font-size: 1.35rem;
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

        .day-btn.pad-btn {
            opacity: 0.25;
            background: transparent;
            border-color: transparent;
            cursor: default;
            pointer-events: none;
        }

        .day-btn.disabled {
            opacity: 0.35;
            background: #eae6e7;
            border-color: #dfd7d9;
            color: #b1a5a9;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .day-btn.disabled .dom {
            color: #b1a5a9;
        }

        .icon-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
        }

        a.disabled {
            pointer-events: none;
            cursor: default;
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

                    <?php if (isset($_SESSION['booking_error']) && $_SESSION['booking_error']): ?>
                        <div class="alert alert-danger mb-4" style="border-radius:0;"><?php echo htmlspecialchars($_SESSION['booking_error']); unset($_SESSION['booking_error']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['booking_suggestion']) && $_SESSION['booking_suggestion']): ?>
                        <?php $sugg = $_SESSION['booking_suggestion']; ?>
                        <div class="alert alert-warning mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius:0;border:1px solid #dcb56a;color:#6b4f1b;background:#fefbf3;">
                            <div>
                                <strong>Rekomendasi Waktu Terdekat:</strong> Slot kosong berikutnya adalah pada 
                                <strong><?php echo date('d M Y', strtotime($sugg['date'])); ?> pukul <?php echo htmlspecialchars($sugg['time']); ?></strong>.
                            </div>
                            <button type="button" class="btn btn-sm btn-dark text-uppercase fw-bold" id="applySuggestionBtn" 
                                    data-date="<?php echo htmlspecialchars($sugg['date']); ?>" 
                                    data-time="<?php echo htmlspecialchars($sugg['time']); ?>"
                                    style="letter-spacing:1px;font-size:0.75rem;border-radius:0;background:#4a3b1a;border:none;">
                                Gunakan Rekomendasi
                            </button>
                        </div>
                        <?php unset($_SESSION['booking_suggestion']); ?>
                    <?php endif; ?>

                    <div class="row g-4">
                        <div class="col-xl-8">
                            <h1 class="panel-title">Select Date & Time</h1>
                            <p class="panel-subtitle">Choose a convenient slot for your Signature Style Session. Availability follows your member tier and the active booking window.</p>

                            <div class="vip-note">
                                <strong><?php echo htmlspecialchars($memberTierName); ?></strong> booking window active.
                                <?php if ($bookingWindowDays > 1): ?>
                                    You can view dates up to <?php echo (int) $bookingWindowDays; ?> days ahead.
                                <?php else: ?>
                                    Booking is currently limited to the nearest available day.
                                <?php endif; ?>
                            </div>

                            <?php if ($vipAccessEnabled): ?>
                                <div class="vip-note">★ <strong>VIP Member Access Unlocked</strong><br><small>Hi, <?php echo htmlspecialchars($memberName); ?>. You have extended booking availability.</small></div>
                            <?php endif; ?>

                            <div class="card-soft">
                                <div class="month-header">
                                    <h2 class="month-title"><?php echo htmlspecialchars($monthLabel); ?></h2>
                                    <div>
                                        <a href="<?php echo htmlspecialchars($prevMonthUrl); ?>" class="icon-btn-link <?php echo $prevMonthDisabled ? 'disabled' : ''; ?>" style="text-decoration:none;">
                                            <button type="button" class="icon-btn" <?php echo $prevMonthDisabled ? 'disabled' : ''; ?>>‹</button>
                                        </a>
                                        <a href="<?php echo htmlspecialchars($nextMonthUrl); ?>" class="icon-btn-link <?php echo $nextMonthDisabled ? 'disabled' : ''; ?>" style="text-decoration:none;">
                                            <button type="button" class="icon-btn" <?php echo $nextMonthDisabled ? 'disabled' : ''; ?>>›</button>
                                        </a>
                                    </div>
                                </div>
                                <div class="weekday-header">
                                    <span>SUN</span>
                                    <span>MON</span>
                                    <span>TUE</span>
                                    <span>WED</span>
                                    <span>THU</span>
                                    <span>FRI</span>
                                    <span>SAT</span>
                                </div>
                                <div class="day-grid" id="dayGrid">
                                    <?php foreach ($gridCells as $cell): ?>
                                        <?php
                                        $cellDate = $cell['date'];
                                        $isActive = $cell['current_month'] && ($cellDate === $selectedDate);
                                        $isPad = !$cell['current_month'];
                                        $isDisabled = $cell['disabled'];
                                        
                                        $classes = [];
                                        if ($isActive) $classes[] = 'active';
                                        if ($isPad) $classes[] = 'pad-btn';
                                        if ($isDisabled) $classes[] = 'disabled';
                                        
                                        $classStr = implode(' ', $classes);
                                        ?>
                                        <button type="button" class="day-btn <?php echo $classStr; ?>" data-date="<?php echo htmlspecialchars($cellDate); ?>" <?php echo $isDisabled ? 'disabled' : ''; ?>>
                                            <span class="dom"><?php echo htmlspecialchars($cell['day_num']); ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="card-soft">
                                <div class="slot-head">
                                    <h2 class="slot-title">Available Times</h2>
                                    <span class="tz">WIB (UTC+7)</span>
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
                                
                                <?php if (!empty($pricing['services_detail'])): ?>
                                    <?php foreach ($pricing['services_detail'] as $s): ?>
                                        <div class="summary-row">
                                            <span><?php echo htmlspecialchars($s['service_name']); ?></span>
                                            <span>Rp<?php echo number_format($s['price'], 0, ',', '.'); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="summary-row"><span><?php echo $serviceLabel; ?></span><span>Rp0</span></div>
                                <?php endif; ?>

                                <?php if (!empty($pricing['addons_detail'])): ?>
                                    <?php foreach ($pricing['addons_detail'] as $a): ?>
                                        <div class="summary-row">
                                            <span>[Add-on] <?php echo htmlspecialchars($a['addon_name']); ?></span>
                                            <span>Rp<?php echo number_format($a['price'], 0, ',', '.'); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if (!empty($pricing['promo_items_detail'])): ?>
                                    <?php foreach ($pricing['promo_items_detail'] as $item): ?>
                                        <div class="summary-row">
                                            <span>[Promo Freebie] <?php echo htmlspecialchars($item['name']); ?></span>
                                            <span>Rp<?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if (!empty($pricing['promo_discount']) && $pricing['promo_discount'] > 0): ?>
                                    <div class="summary-row text-danger">
                                        <span>Diskon (<?php echo htmlspecialchars($pricing['promo_detail']['promo_name'] ?? 'Promo'); ?>)</span>
                                        <span>-Rp<?php echo number_format($pricing['promo_discount'], 0, ',', '.'); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $selectedDateTime = strtotime($selectedDate . ' ' . $selectedTime);
                                $endDateTime = $selectedDateTime + ($durationMinutes * 60);
                                ?>
                                <div class="summary-date" id="summaryDate">
                                    📅 <span id="summaryDateText"><?php echo htmlspecialchars(date('D, M d, Y', $selectedDateTime)); ?></span><br>
                                    🕒 <span id="summaryTime"><?php echo htmlspecialchars(date('h:i A', $selectedDateTime)); ?></span> - <span id="summaryEndTime"><?php echo htmlspecialchars(date('h:i A', $endDateTime)); ?></span>
                                </div>
                                <div class="due-row">
                                    <span class="label">Total Due Today</span>
                                    <span class="value">Rp<?php echo number_format($pricing['total_price'] ?? 0, 0, ',', '.'); ?></span>
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
    const summaryDateText = document.getElementById('summaryDateText');
    const summaryTime = document.getElementById('summaryTime');
    const summaryEndTime = document.getElementById('summaryEndTime');
    const durationMinutes = <?php echo $durationMinutes; ?>;

    function formatTime(hours, minutes) {
        let ampm = hours >= 12 ? 'PM' : 'AM';
        let displayHours = hours % 12;
        displayHours = displayHours ? displayHours : 12;
        let displayMinutes = minutes < 10 ? '0' + minutes : minutes;
        return (displayHours < 10 ? '0' + displayHours : displayHours) + ':' + displayMinutes + ' ' + ampm;
    }

    function updateSummaryEndTime() {
        const timeVal = timeInput.value;
        if (!timeVal) return;
        const parts = timeVal.split(':');
        const startHours = parseInt(parts[0], 10);
        const startMinutes = parseInt(parts[1], 10);

        summaryTime.textContent = formatTime(startHours, startMinutes);

        let totalMinutes = startHours * 60 + startMinutes + durationMinutes;
        let endHours = Math.floor(totalMinutes / 60) % 24;
        let endMinutes = totalMinutes % 60;

        summaryEndTime.textContent = formatTime(endHours, endMinutes);
    }

    document.querySelectorAll('.day-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (btn.classList.contains('disabled') || btn.classList.contains('pad-btn')) {
                return;
            }
            const selected = btn.dataset.date;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('date', selected);
            window.location.search = urlParams.toString();
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
            updateSummaryEndTime();
        });
    });

    // Handle suggestion button
    const applySuggBtn = document.getElementById('applySuggestionBtn');
    if (applySuggBtn) {
        applySuggBtn.addEventListener('click', () => {
            const suggDate = applySuggBtn.dataset.date;
            const suggTime = applySuggBtn.dataset.time;
            
            dateInput.value = suggDate;
            timeInput.value = suggTime;
            
            document.querySelectorAll('.day-btn').forEach((btn) => {
                if (btn.dataset.date === suggDate) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            document.querySelectorAll('.time-btn').forEach((btn) => {
                if (btn.dataset.time === suggTime) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            updateSummaryEndTime();
            document.getElementById('scheduleForm').submit();
        });
    }
</script>
</body>
</html>
