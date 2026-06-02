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
    <title>Scan QR Kafe - Pesan Menu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 30px;
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .qr-scanner-wrapper {
            position: relative;
            margin: 25px 0;
            background: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #qr-scanner {
            width: 100%;
            height: 100%;
        }

        .scanner-placeholder {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .scanner-placeholder svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .scanner-placeholder p {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .permissions-request {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            display: none;
        }

        .permissions-request button {
            background: #ffc107;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }

        .permissions-request button:hover {
            background: #e0a800;
        }

        .result-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            display: none;
        }

        .result-box.error {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .result-box h3 {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .result-box p {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .result-box button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .result-box.error button {
            background: #dc3545;
        }

        .result-box button:hover {
            opacity: 0.9;
        }

        .manual-input {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .manual-input label {
            display: block;
            margin-bottom: 10px;
            color: #666;
            font-size: 14px;
            font-weight: bold;
        }

        .manual-input input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .manual-input input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .manual-input button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .manual-input button:hover {
            background: #5568d3;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-icon, .success-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Pesan Menu Kafe</h1>
            <p>Scan kode QR meja Anda untuk mulai memesan</p>
        </div>

        <div class="permissions-request" id="permissionsRequest">
            <p>Izin akses kamera diperlukan untuk scan QR code</p>
            <button onclick="requestCameraPermission()">Berikan Izin Kamera</button>
        </div>

        <div class="qr-scanner-wrapper">
            <div id="qr-scanner"></div>
            <div class="scanner-placeholder" id="scannerPlaceholder">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9V5a2 2 0 0 1 2-2h4M21 9V5a2 2 0 0 0-2-2h-4M3 15v4a2 2 0 0 0 2 2h4m12 0h4a2 2 0 0 0 2-2v-4"></path>
                    <rect x="7" y="7" width="10" height="10" rx="1.5"></rect>
                </svg>
                <p>Kamera akan muncul di sini</p>
                <p style="font-size: 12px; margin-top: 10px;">Pastikan browser memiliki akses ke kamera</p>
            </div>
        </div>

        <div class="result-box" id="resultBox">
            <div class="success-icon">✓</div>
            <h3>QR Terdeteksi!</h3>
            <p>Memproses data meja Anda...</p>
            <button onclick="proceedToMenu()">Lanjut ke Menu</button>
        </div>

        <div class="loading" id="loading">Memproses...</div>

        <div class="manual-input">
            <label for="manualToken">Atau masukkan nomor meja:</label>
            <input type="text" id="manualToken" placeholder="Contoh: C01 atau T-01" maxlength="10">
            <button onclick="processManualToken()">Masuk ke Menu</button>
        </div>
    </div>

    <!-- Load jsQR library for QR code scanning -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        let currentToken = null;
        let cameraStream = null;
        let html5QrcodeScanner = null;

        // Initialize QR scanner
        async function initQRScanner() {
            try {
                // Check camera permission
                const permission = await navigator.permissions.query({ name: 'camera' });
                
                if (permission.state === 'denied') {
                    document.getElementById('permissionsRequest').style.display = 'block';
                    document.getElementById('qr-scanner').style.display = 'none';
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
                video.style.width = '100%';
                video.style.height = '100%';

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
                        // QR code found
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
                    window.location.href = '/?page=cafe&action=menu';
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
                <div class="error-icon">✕</div>
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
