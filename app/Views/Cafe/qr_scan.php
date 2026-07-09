<?php
/**
 * QR Scanning for Cafe Orders
 * Customer scans QR code from cafe table/seat
 */

// Check if we need to redirect (no auth needed for QR scanning)
if (!isset($_GET['action']) || $_GET['action'] !== 'scan') {
    header('Location: index.php?page=cafe&action=scan');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Kafe - Merish</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4eded;
            --surface: #f8f2f2;
            --card: #fffafa;
            --line: #d9ccd0;
            --ink: #4f4248;
            --muted: #7a6e73;
            --accent: #8d616f;
            --accent-dark: #724e5a;
            --accent-soft: #ead2db;
            --input-border: #c8b8bd;
            --input-bg: #fdf8f8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background:
                radial-gradient(circle at 2px 2px, rgba(141,97,111,0.06) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* ── Topbar ────────────────────────────────── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 2.5rem;
            background: rgba(255,249,249,0.92);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 3px;
            color: var(--accent);
            text-decoration: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #b79fa6;
            color: #6d5660;
            background: #fff;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.55rem 1.1rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: #fff;
            background: var(--accent);
            border-color: var(--accent);
        }

        /* ── Page label ─────────────────────────────── */
        .page-label {
            text-align: center;
            padding: 1rem 1rem 0;
            font-size: 0.72rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
        }

        .page-label span { color: var(--accent); }

        /* ── Main content ───────────────────────────── */
        .scan-shell {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 1.5rem 2rem 2rem;
        }

        .scan-card {
            max-width: 500px;
            width: 100%;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 16px 48px rgba(73,53,60,0.08);
            padding: 2.6rem 2.4rem 2.2rem;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            color: #46373f;
            margin-bottom: 0.35rem;
        }

        .card-sub {
            color: var(--muted);
            font-size: 0.88rem;
            margin-bottom: 1.8rem;
            line-height: 1.5;
        }

        /* ── QR Scanner area ────────────────────────── */
        .scanner-area {
            position: relative;
            margin: 0 0 1.5rem;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 4px;
            overflow: hidden;
            min-height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #qr-scanner {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        #qr-scanner video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }

        .scanner-placeholder {
            text-align: center;
            padding: 2.5rem 1.5rem;
            color: var(--muted);
            position: relative;
            z-index: 1;
        }

        .scanner-placeholder svg {
            width: 72px;
            height: 72px;
            margin-bottom: 1.2rem;
            opacity: 0.4;
            stroke: var(--accent);
        }

        .scanner-placeholder p {
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
            line-height: 1.5;
        }

        .scanner-placeholder .hint {
            font-size: 0.75rem;
            color: #b5a5aa;
        }

        /* ── Scan banner ────────────────────────────── */
        .scan-banner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            background: var(--accent);
            color: #fff;
            border: none;
            width: 100%;
            cursor: default;
            margin-bottom: 1.5rem;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-family: 'Montserrat', sans-serif;
        }

        .scan-banner svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .scan-banner .banner-arrow {
            margin-left: auto;
            font-size: 1rem;
        }

        /* ── Permission request ─────────────────────── */
        .permissions-request {
            padding: 0.85rem 1rem;
            margin-bottom: 1.2rem;
            border: 1px solid #efc9d1;
            background: #fbeaec;
            color: #7c3547;
            font-size: 0.84rem;
            display: none;
        }

        .permissions-request button {
            display: block;
            margin-top: 0.7rem;
            padding: 0.6rem 1.2rem;
            border: 1px solid var(--accent-dark);
            background: var(--accent);
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .permissions-request button:hover {
            background: var(--accent-dark);
        }

        /* ── Result box ─────────────────────────────── */
        .result-box {
            padding: 1.2rem 1rem;
            margin: 1.2rem 0;
            border: 1px solid #c3d4c3;
            background: #eef5ee;
            color: #2d5a3d;
            text-align: center;
            display: none;
        }

        .result-box .result-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #3a7d52;
        }

        .result-box h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            margin-bottom: 0.4rem;
        }

        .result-box p {
            font-size: 0.85rem;
            margin-bottom: 0.8rem;
            color: #4a6e55;
        }

        .result-box button {
            padding: 0.7rem 1.5rem;
            border: 1px solid #2d5a3d;
            background: #3a7d52;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .result-box button:hover {
            background: #2d5a3d;
        }

        .result-box.error {
            border-color: #efc9d1;
            background: #fbeaec;
            color: #7c3547;
        }

        .result-box.error .result-icon {
            color: #a8405a;
        }

        .result-box.error button {
            background: #a8405a;
            border-color: #7c3547;
        }

        .result-box.error button:hover {
            background: #7c3547;
        }

        /* ── Divider ────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin: 1.5rem 0;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        /* ── Manual input ───────────────────────────── */
        .manual-section label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #6f5d64;
            margin-bottom: 0.5rem;
        }

        .manual-section input {
            width: 100%;
            padding: 0.7rem 0.85rem;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            color: var(--ink);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            margin-bottom: 0.8rem;
        }

        .manual-section input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(141,97,111,0.1);
        }

        .manual-section input::placeholder {
            color: #b5a5aa;
        }

        .submit-btn {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid var(--accent-dark);
            background: var(--accent);
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submit-btn:hover {
            background: var(--accent-dark);
        }

        /* ── Loading spinner ────────────────────────── */
        .loading {
            display: none;
            text-align: center;
            padding: 1.2rem;
        }

        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--accent-soft);
            border-top: 3px solid var(--accent);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ── Footer ─────────────────────────────────── */
        .scan-footer {
            background: #e7e1e0;
            border-top: 1px solid #d4c9cc;
            padding: 1.5rem 2rem;
            text-align: center;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: #74545f;
            margin-bottom: 0.6rem;
        }

        .footer-copy {
            font-size: 0.74rem;
            color: #9a8b8f;
        }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width: 768px) {
            .topbar { padding: 1rem 1.2rem; }
            .scan-shell { padding: 1rem; }
            .scan-card { padding: 2rem 1.5rem; }
        }

        @media (max-width: 480px) {
            .brand { font-size: 1.4rem; }
            .card-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <header class="topbar">
        <a href="/index.php?page=home" class="brand">MERISH</a>
        <a href="/index.php?page=cafe" class="back-link">← Back to Cafe</a>
    </header>

    <div class="page-label">Cafe Order — <span>Scan QR</span></div>

    <section class="scan-shell">
        <div class="scan-card">

            <h1 class="card-title">Pesan Menu Kafe</h1>
            <p class="card-sub">Scan kode QR meja Anda untuk mulai memesan menu.</p>

            <!-- Scan banner -->
            <div class="scan-banner">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                </svg>
                Scan QR di Meja
                <span class="banner-arrow">→</span>
            </div>

            <!-- Permission request -->
            <div class="permissions-request" id="permissionsRequest">
                <p>Izin akses kamera diperlukan untuk scan QR code.</p>
                <button onclick="requestCameraPermission()">Berikan Izin Kamera</button>
            </div>

            <!-- Scanner area -->
            <div class="scanner-area">
                <div id="qr-scanner"></div>
                <div class="scanner-placeholder" id="scannerPlaceholder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 9V5a2 2 0 0 1 2-2h4M21 9V5a2 2 0 0 0-2-2h-4M3 15v4a2 2 0 0 0 2 2h4m12 0h4a2 2 0 0 0 2-2v-4"></path>
                        <rect x="7" y="7" width="10" height="10" rx="1.5"></rect>
                    </svg>
                    <p>Kamera akan muncul di sini</p>
                    <p class="hint">Pastikan browser memiliki akses ke kamera</p>
                </div>
            </div>

            <!-- Result box -->
            <div class="result-box" id="resultBox">
                <div class="result-icon">✓</div>
                <h3>QR Terdeteksi!</h3>
                <p>Memproses data meja Anda...</p>
                <button onclick="proceedToMenu()">Lanjut ke Menu</button>
            </div>

            <!-- Loading -->
            <div class="loading" id="loading"></div>

            <!-- Divider -->
            <div class="divider">atau masukkan manual</div>

            <!-- Manual input -->
            <div class="manual-section">
                <label for="manualToken">Nomor Meja</label>
                <input type="text" id="manualToken" placeholder="Contoh: C01 atau T-01" maxlength="10">
                <button class="submit-btn" onclick="processManualToken()">Masuk ke Menu</button>
            </div>

        </div>
    </section>

    <footer class="scan-footer">
        <div class="footer-brand">Merish</div>
        <div class="footer-copy">&copy; 2024 Merish Beauty & Cafe. All rights reserved.</div>
    </footer>

    <!-- Load jsQR library for QR code scanning -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        let currentToken = null;
        let cameraStream = null;

        // Initialize QR scanner
        async function initQRScanner() {
            try {
                // Check camera permission
                const permission = await navigator.permissions.query({ name: 'camera' });
                
                if (permission.state === 'denied') {
                    document.getElementById('permissionsRequest').style.display = 'block';
                    return;
                }

                // Try to access camera
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });

                cameraStream = stream;
                const video = document.createElement('video');
                video.srcObject = stream;
                video.setAttribute('autoplay', true);
                video.setAttribute('playsinline', true);

                document.getElementById('qr-scanner').innerHTML = '';
                document.getElementById('qr-scanner').appendChild(video);
                document.getElementById('scannerPlaceholder').style.display = 'none';

                // Start scanning
                startContinuousScan(video);

            } catch (error) {
                console.error('Camera error:', error);
                document.getElementById('permissionsRequest').style.display = 'block';
                if (error.name === 'NotAllowedError') {
                    document.querySelector('.permissions-request p').textContent = 'Akses kamera ditolak. Silakan aktifkan izin kamera di pengaturan browser.';
                }
            }
        }

        // Continuous QR code scanning
        function startContinuousScan(video) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            video.addEventListener('loadedmetadata', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            });

            function scanFrame() {
                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code && code.data) {
                        handleQRDetected(code.data);
                    }
                }
                requestAnimationFrame(scanFrame);
            }

            scanFrame();
        }

        // Handle detected QR code
        function handleQRDetected(data) {
            if (currentToken) return; // Already processing
            
            currentToken = data;
            console.log('QR detected:', data);

            // Stop camera
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }

            // Show result
            document.getElementById('qr-scanner').style.display = 'none';
            document.getElementById('scannerPlaceholder').style.display = 'flex';
            document.getElementById('resultBox').style.display = 'block';
        }

        function requestCameraPermission() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(() => {
                    location.reload();
                })
                .catch(err => {
                    alert('Tidak dapat mengakses kamera: ' + err.message);
                });
        }

        function processManualToken() {
            const token = document.getElementById('manualToken').value.trim().toUpperCase();
            if (!token) {
                alert('Silakan masukkan nomor meja');
                return;
            }
            currentToken = token;
            proceedToMenu();
        }

        function proceedToMenu() {
            if (!currentToken) {
                alert('Data meja tidak valid');
                return;
            }

            // Show loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('resultBox').style.display = 'none';

            // Send token to server
            fetch('/api/cafe/scan-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    token: currentToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store session data
                    sessionStorage.setItem('cafe_bill_id', data.data.bill_id);
                    sessionStorage.setItem('cafe_scenario', data.data.scenario);
                    sessionStorage.setItem('cafe_customer_role', data.data.customer_role);
                    sessionStorage.setItem('cafe_customer_name', data.data.customer_name);
                    sessionStorage.setItem('cafe_seat_id', data.data.seat_id);
                    sessionStorage.setItem('cafe_seat_name', data.data.seat_name);
                    sessionStorage.setItem('cafe_scan_response', JSON.stringify(data));
                    
                    // Redirect to menu
                    window.location.href = 'index.php?page=cafe&action=menu';
                } else {
                    showError(data.message || 'QR tidak valid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Gagal memproses QR: ' + error.message);
            });
        }

        function showError(message) {
            document.getElementById('loading').style.display = 'none';
            const resultBox = document.getElementById('resultBox');
            resultBox.classList.add('error');
            resultBox.innerHTML = `
                <div class="result-icon">✕</div>
                <h3>Error</h3>
                <p>${message}</p>
                <button onclick="location.reload()">Coba Lagi</button>
            `;
            resultBox.style.display = 'block';
        }

        // Initialize on page load
        window.addEventListener('load', () => {
            initQRScanner();
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</body>
</html>
