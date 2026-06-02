<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .success-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .check-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #4caf50;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 70px;
            animation: scaleIn 0.6s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .message {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .details {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: left;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #999;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .cta-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            color: white;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
            text-decoration: none;
            color: #333;
        }

        .timer {
            color: #999;
            font-size: 12px;
            margin-top: 20px;
        }

        .emoji-success {
            font-size: 100px;
            animation: bounce 0.8s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="emoji-success">🎉</div>
        <div class="check-circle">✓</div>

        <h1>Pesanan Berhasil!</h1>

        <div id="customerMessage">
            <p class="message">
                Pesanan Anda telah ditambahkan ke <strong>Bill Salon</strong>.<br>
                Silakan ke <strong>Receptionist</strong> untuk menyelesaikan pembayaran.
            </p>
        </div>

        <div id="guestMessage" style="display: none;">
            <p class="message">
                Pembayaran Anda telah diterima!<br>
                Pesanan sedang disiapkan di dapur. Terima kasih!
            </p>
        </div>

        <div class="details" id="orderDetails">
            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value">✓ Dikonfirmasi</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Lokasi Meja</span>
                <span class="detail-value" id="seatInfo">-</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Jenis Pesanan</span>
                <span class="detail-value" id="orderTypeInfo">Dine-In</span>
            </div>
        </div>

        <div class="cta-buttons">
            <a href="/?page=cafe&action=scan" class="btn btn-primary">Pesan Lagi</a>
            <a href="/?page=home" class="btn btn-secondary">Kembali ke Home</a>
        </div>

        <div class="timer">
            <small>Anda akan dialihkan ke home dalam <span id="countdown">10</span> detik...</small>
        </div>
    </div>

    <script>
        // Detect scenario and show appropriate message
        document.addEventListener('DOMContentLoaded', function() {
            const role = sessionStorage.getItem('cafe_customer_role');
            const seatName = sessionStorage.getItem('cafe_seat_name');
            const deliveryMethod = sessionStorage.getItem('cafe_delivery_method') || 'Dine-In';

            if (role === 'guest') {
                document.getElementById('customerMessage').style.display = 'none';
                document.getElementById('guestMessage').style.display = 'block';
            } else {
                document.getElementById('customerMessage').style.display = 'block';
                document.getElementById('guestMessage').style.display = 'none';
            }

            if (seatName) {
                document.getElementById('seatInfo').textContent = seatName;
            }

            // Capitalize delivery method
            const capitalizedMethod = deliveryMethod.charAt(0).toUpperCase() + deliveryMethod.slice(1);
            document.getElementById('orderTypeInfo').textContent = capitalizedMethod;

            // Auto-redirect after 10 seconds
            let countdown = 10;
            const timer = setInterval(() => {
                countdown--;
                document.getElementById('countdown').textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timer);
                    window.location.href = '/?page=home';
                }
            }, 1000);
        });
    </script>
</body>
</html>
