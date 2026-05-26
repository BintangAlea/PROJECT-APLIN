<?php
$reservation = $reservation ?? [];
$confirmationId = $confirmation_id ?? ($reservation['booking_confirmation_id'] ?? 'MRISH-0000');
$serviceName = $reservation['service_name'] ?? 'The Signature Look';
$beauticianName = $reservation['beautician_name'] ?? 'Elena R.';
$specialization = $reservation['specialization'] ?? 'Stylist';
$reservationDate = !empty($reservation['reservation_date']) ? date('M d, Y', strtotime($reservation['reservation_date'])) : 'Oct 24, 2024';
$reservationTime = !empty($reservation['reservation_time']) ? date('g:i A', strtotime($reservation['reservation_time'])) : '2:00 PM';
$qrCodeUrl = $qr_code_url ?? ($reservation['booking_qr_code_url'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #fbf3f5;
            --card: #fffaf9;
            --line: #e6d8db;
            --ink: #6b4f5d;
            --muted: #847378;
            --accent: #8e6170;
            --accent-dark: #7b4f5d;
            --soft: #f3e3ea;
        }

        html, body {
            min-height: 100%;
            margin: 0;
            background: radial-gradient(circle at top, #fffefe 0%, var(--bg) 48%, #f8ecef 100%);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .confirm-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .confirm-shell {
            width: 100%;
            max-width: 560px;
            text-align: center;
        }

        .status-icon {
            width: 56px;
            height: 56px;
            border: 2px solid var(--accent);
            color: var(--accent);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            line-height: 1;
            margin-bottom: 1.15rem;
            background: rgba(255,255,255,0.5);
            box-shadow: 0 10px 24px rgba(102, 72, 82, 0.08);
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 4vw, 3.4rem);
            color: #7d5563;
            margin-bottom: 0.65rem;
        }

        .page-copy {
            color: var(--muted);
            max-width: 420px;
            margin: 0 auto 1.6rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .ticket-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 10px;
            box-shadow: 0 18px 30px rgba(96, 67, 78, 0.08);
            overflow: hidden;
            text-align: left;
        }

        .ticket-head {
            padding: 1rem 1.15rem 0.35rem;
            border-bottom: 1px solid #efe5e7;
        }

        .ticket-kicker {
            font-size: 0.68rem;
            letter-spacing: 1.9px;
            color: #8d7b80;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .ticket-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.9rem;
            color: #4e4047;
            line-height: 1.05;
            margin-bottom: 0.6rem;
        }

        .ticket-meta {
            padding: 0.25rem 0 0.15rem;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.7rem 0;
            border-top: 1px solid #f0e8ea;
            color: #55464c;
            font-size: 0.95rem;
        }

        .meta-label {
            color: #8b7d82;
        }

        .ticket-qr-area {
            position: relative;
            padding: 1rem 1.15rem 1.15rem;
            background: linear-gradient(180deg, #fffdfd 0%, #fff8fb 100%);
        }

        .ticket-qr-area:before,
        .ticket-qr-area:after {
            content: '';
            position: absolute;
            top: -12px;
            width: 22px;
            height: 24px;
            border: 1px solid #e1d2d7;
            border-radius: 50%;
            background: var(--bg);
        }

        .ticket-qr-area:before {
            left: -11px;
        }

        .ticket-qr-area:after {
            right: -11px;
        }

        .qr-shell {
            max-width: 160px;
            margin: 0 auto;
            padding: 0.65rem;
            border: 1px solid #ebe0e3;
            background: #fff;
            border-radius: 8px;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.02);
        }

        .qr-box {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 6px;
            background: linear-gradient(145deg, #7d5564 0%, #97626d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
            background: #fff;
        }

        .qr-fallback {
            width: 100%;
            height: 100%;
            border-radius: 4px;
            background:
                linear-gradient(90deg, #fff 0 10%, transparent 10% 20%, #fff 20% 30%, transparent 30% 40%, #fff 40% 50%, transparent 50% 60%, #fff 60% 70%, transparent 70% 80%, #fff 80% 90%, transparent 90% 100%),
                linear-gradient(180deg, #fff 0 10%, transparent 10% 20%, #fff 20% 30%, transparent 30% 40%, #fff 40% 50%, transparent 50% 60%, #fff 60% 70%, transparent 70% 80%, #fff 80% 90%, transparent 90% 100%),
                #e9d8de;
            background-size: 100% 100%;
        }

        .qr-id {
            text-align: center;
            font-size: 0.72rem;
            letter-spacing: 1.3px;
            color: #84777b;
            margin-top: 0.7rem;
        }

        .support-stack {
            margin-top: 1.1rem;
            display: grid;
            gap: 0.85rem;
        }

        .support-card {
            background: rgba(255,255,255,0.45);
            border: 1px solid #f0e5e8;
            border-radius: 10px;
            padding: 1rem 1rem 0.95rem;
            text-align: left;
        }

        .support-head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.45rem;
        }

        .support-icon {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: var(--soft);
            color: var(--accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex: 0 0 30px;
        }

        .support-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #604c54;
            margin: 0;
        }

        .support-copy {
            margin: 0;
            color: var(--muted);
            font-size: 0.84rem;
            line-height: 1.55;
        }

        .return-btn {
            margin-top: 1.5rem;
            background: var(--accent);
            border: 1px solid var(--accent);
            color: #fff;
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.9rem 1.3rem;
            min-width: 160px;
        }

        .return-btn:hover {
            background: var(--accent-dark);
            border-color: var(--accent-dark);
            color: #fff;
        }

        @media (max-width: 575.98px) {
            .confirm-wrap {
                padding: 1.25rem 0.85rem 2rem;
            }

            .ticket-title {
                font-size: 1.65rem;
            }
        }
    </style>
</head>
<body>
    <main class="confirm-wrap">
        <div class="confirm-shell">
            <div class="status-icon">✓</div>
            <h1 class="page-title">Booking Confirmed!</h1>
            <p class="page-copy">Your appointment request has been successfully submitted and is awaiting final review.</p>

            <section class="ticket-card">
                <div class="ticket-head">
                    <div class="ticket-kicker">Reservation Ticket</div>
                    <div class="ticket-title"><?php echo htmlspecialchars($serviceName); ?></div>

                    <div class="ticket-meta">
                        <div class="meta-row">
                            <span class="meta-label">Date</span>
                            <span><?php echo htmlspecialchars($reservationDate); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Time</span>
                            <span><?php echo htmlspecialchars($reservationTime); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Stylist</span>
                            <span><?php echo htmlspecialchars($beauticianName); ?></span>
                        </div>
                    </div>
                </div>

                <div class="ticket-qr-area">
                    <div class="qr-shell">
                        <div class="qr-box">
                            <?php if (!empty($qrCodeUrl) && strpos($qrCodeUrl, '/uploads') !== false): ?>
                                <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="Booking QR Code">
                            <?php else: ?>
                                <div class="qr-fallback" aria-label="QR placeholder"></div>
                            <?php endif; ?>
                        </div>
                        <div class="qr-id">ID: <?php echo htmlspecialchars($confirmationId); ?></div>
                    </div>
                </div>
            </section>

            <div class="support-stack">
                <article class="support-card">
                    <div class="support-head">
                        <span class="support-icon">✉</span>
                        <h2 class="support-title">Email Notification Sent</h2>
                    </div>
                    <p class="support-copy">We've sent a summary of your request to your registered email address. Please check your spam folder if you don't see it within 5 minutes.</p>
                </article>

                <article class="support-card">
                    <div class="support-head">
                        <span class="support-icon">⌛</span>
                        <h2 class="support-title">Admin Verification Pending</h2>
                    </div>
                    <p class="support-copy">Your requested time slot is held. Our team will review and finalize the booking shortly. You will receive a final confirmation email once approved.</p>
                </article>
            </div>

            <a href="/index.php?page=home" class="btn return-btn">Return to Home</a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
