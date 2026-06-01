<?php 
// QR Order - Payment Status
// Show payment processing or success/failure
$guestOrderId = $guest_order_id ?? null;
$isPaid = $is_paid ?? false;
$seatName = $seat_name ?? 'Your Seat';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merish Cafe - Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #f8f6f3 0%, #E8D5C4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .status-container {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            max-width: 500px;
            text-align: center;
        }
        .status-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .status-icon.success {
            color: #28a745;
            animation: pop 0.6s ease-out;
        }
        .status-icon.pending {
            color: #ffc107;
            animation: spin 2s linear infinite;
        }
        .status-icon.failed {
            color: #dc3545;
        }
        @keyframes pop {
            0% { transform: scale(0); }
            70% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .status-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        .status-message {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        .order-details {
            background: #f8f6f3;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #E8D5C4;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            color: #666;
            font-weight: 600;
        }
        .detail-value {
            color: #333;
            font-weight: 700;
        }
        .action-button {
            background: #8B7355;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .action-button:hover {
            background: #6B5B4F;
            text-decoration: none;
        }
        .secondary-button {
            background: #E8D5C4;
            color: #333;
            margin-left: 0.5rem;
        }
        .secondary-button:hover {
            background: #DCC6B5;
        }
        .countdown {
            font-size: 3rem;
            font-weight: 700;
            color: #D4A574;
            margin: 1rem 0;
        }
        .timer-warning {
            color: #dc3545;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="status-container">
        <?php if ($isPaid): ?>
            <!-- Success State -->
            <div class="status-icon success">✓</div>
            <h1 class="status-title">Pembayaran Berhasil!</h1>
            <p class="status-message">Pesanan Anda telah dikonfirmasi dan diteruskan ke barista.</p>

            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#<?php echo str_pad($guestOrderId ?? 0, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Lokasi:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($seatName); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><i class="bi bi-check-circle" style="color: #28a745;"></i> Diproses</span>
                </div>
            </div>

            <p class="status-message" style="margin-bottom: 1.5rem;">
                <strong>Estimasi waktu:</strong> 10-15 menit. Kami akan memanggil nomor meja Anda saat pesanan siap.
            </p>

            <div>
                <a href="index.php?page=cafe" class="action-button">
                    <i class="bi bi-arrow-left"></i> Kembali ke Menu
                </a>
            </div>

        <?php else: ?>
            <!-- Processing/Failed State -->
            <div class="status-icon pending">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <h1 class="status-title">Memproses Pembayaran...</h1>
            <p class="status-message">Mohon tunggu. Sistem kami sedang memverifikasi pembayaran Anda.</p>

            <div class="timer-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Pembayaran harus diselesaikan dalam waktu:
            </div>
            <div class="countdown" id="countdown">5:00</div>

            <p class="status-message">
                Jika pembayaran tidak selesai dalam 5 menit, pesanan akan otomatis dibatalkan.
            </p>

            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#<?php echo str_pad($guestOrderId ?? 0, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status Pembayaran:</span>
                    <span class="detail-value" style="color: #ffc107;">Menunggu Konfirmasi</span>
                </div>
            </div>

            <div>
                <button class="action-button" onclick="checkPaymentStatus()">
                    <i class="bi bi-arrow-clockwise"></i> Cek Status
                </button>
                <a href="index.php?page=cafe" class="action-button secondary-button">
                    <i class="bi bi-x"></i> Batalkan
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (!$isPaid): ?>
        // Countdown timer for guest orders (5 minutes)
        let timeRemaining = 300; // 5 minutes in seconds
        const countdownElement = document.getElementById('countdown');

        function updateCountdown() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            countdownElement.innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timeRemaining <= 0) {
                clearInterval(countdownInterval);
                location.href = 'index.php?page=cafe&error=order_cancelled';
            }

            timeRemaining--;
        }

        const countdownInterval = setInterval(updateCountdown, 1000);

        // Check payment status periodically
        async function checkPaymentStatus() {
            try {
                const response = await fetch('index.php?page=qrorder&action=paymentStatus&guest_order_id=<?php echo $guestOrderId ?? 0; ?>');
                location.reload();
            } catch (error) {
                console.error('Error checking payment:', error);
                alert('Gagal memeriksa status pembayaran');
            }
        }

        // Auto-check payment status every 10 seconds
        setInterval(checkPaymentStatus, 10000);
        <?php endif; ?>
    </script>
</body>
</html>
