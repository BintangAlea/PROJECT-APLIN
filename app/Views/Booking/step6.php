<?php
/**
 * Booking Step 6: Confirmation with QR Code
 */
?>
<?php include __DIR__ . '/../Layout/base.php'; ?>

<div class="booking-container">
    <div class="booking-header">
        <h1>✅ Pesanan Berhasil Dikonfirmasi!</h1>
        <p>Terima kasih telah memesan layanan kami</p>
    </div>

    <div class="booking-progress">
        <div class="progress-bar">
            <div class="progress-step">1</div>
            <div class="progress-line"></div>
            <div class="progress-step">2</div>
            <div class="progress-line"></div>
            <div class="progress-step">3</div>
            <div class="progress-line"></div>
            <div class="progress-step">4</div>
            <div class="progress-line"></div>
            <div class="progress-step">5</div>
            <div class="progress-line"></div>
            <div class="progress-step active">6</div>
        </div>
    </div>

    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-icon">✅</div>
            <h2>Konfirmasi Booking Anda</h2>

            <div class="confirmation-details">
                <div class="detail-row">
                    <span class="label">Nomor Referensi</span>
                    <span class="value"><?= htmlspecialchars($confirmation_id) ?></span>
                </div>

                <div class="detail-row">
                    <span class="label">Status</span>
                    <span class="value status-pending">Menunggu Konfirmasi</span>
                </div>

                <?php if (!empty($reservation['reservation_date'])): ?>
                    <div class="detail-row">
                        <span class="label">Jadwal</span>
                        <span class="value">
                            <?= date('d M Y', strtotime($reservation['reservation_date'])) ?>
                            <?php if (!empty($reservation['reservation_time'])): ?>
                                @ <?= htmlspecialchars($reservation['reservation_time']) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($reservation['total_price'])): ?>
                    <div class="detail-row">
                        <span class="label">Total Pembayaran</span>
                        <span class="value price">Rp <?= number_format($reservation['total_price'], 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- QR Code Section -->
            <?php if (!empty($qr_code_url)): ?>
                <div class="qr-section">
                    <h3>Kode QR Booking</h3>
                    <p class="qr-info">Tunjukkan QR code ini saat tiba di salon</p>
                    <div class="qr-code">
                        <?php if (strpos($qr_code_url, '/uploads') !== false): ?>
                            <img src="<?= htmlspecialchars($qr_code_url) ?>" alt="QR Code">
                        <?php else: ?>
                            <div class="qr-placeholder">QR Code tidak dapat ditampilkan</div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="printQR()">🖨️ Cetak QR</button>
                </div>
            <?php endif; ?>

            <div class="next-steps">
                <h3>Langkah Berikutnya</h3>
                <ol>
                    <li>Simpan nomor referensi: <strong><?= htmlspecialchars($confirmation_id) ?></strong></li>
                    <li>Simpan atau cetak QR code di atas</li>
                    <li>Kami akan mengirim konfirmasi via email/WhatsApp</li>
                    <li>Tiba 10 menit lebih awal pada jadwal yang ditentukan</li>
                    <li>Tunjukkan QR code saat check-in</li>
                </ol>
            </div>

            <div class="actions">
                <a href="/" class="btn btn-primary">Kembali ke Beranda</a>
                <a href="/index.php?page=customer" class="btn btn-secondary">Lihat Pesanan Saya</a>
            </div>
        </div>
    </div>
</div>

<script>
function printQR() {
    const qrImage = document.querySelector('.qr-code img');
    if (!qrImage) {
        alert('QR Code tidak tersedia');
        return;
    }

    const printWindow = window.open('', '', 'height=400,width=600');
    printWindow.document.write('<html><head><title>Cetak QR Code Booking</title></head><body>');
    printWindow.document.write('<div style="text-align: center;">');
    printWindow.document.write('<h1>Kode QR Booking</h1>');
    printWindow.document.write('<p>Referensi: <?= htmlspecialchars($confirmation_id) ?></p>');
    printWindow.document.write(qrImage.outerHTML);
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>

<style>
.confirmation-container {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
}

.confirmation-card {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
}

.confirmation-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.confirmation-card h2 {
    color: #2d5a2d;
    margin-bottom: 1.5rem;
}

.confirmation-details {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.detail-row .label {
    font-weight: 600;
    color: #666;
}

.detail-row .value {
    color: #333;
    font-weight: 500;
}

.detail-row .value.price {
    color: #d4a574;
    font-weight: bold;
    font-size: 1.1rem;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.qr-section {
    background: #f0f8ff;
    border: 2px solid #d4a574;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.qr-section h3 {
    margin-top: 0;
    color: #333;
}

.qr-info {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.qr-code {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    text-align: center;
}

.qr-code img {
    max-width: 100%;
    height: auto;
}

.qr-placeholder {
    background: #f0f0f0;
    padding: 3rem;
    border-radius: 4px;
    color: #999;
}

.next-steps {
    text-align: left;
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.next-steps h3 {
    margin-top: 0;
    color: #333;
    font-size: 1rem;
}

.next-steps ol {
    margin: 0;
    padding-left: 1.5rem;
}

.next-steps li {
    margin-bottom: 0.5rem;
    color: #666;
    line-height: 1.6;
}

.next-steps strong {
    color: #d4a574;
}

.actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-primary {
    background: #d4a574;
    color: white;
}

.btn-primary:hover {
    background: #c29458;
}

.btn-secondary {
    background: #f0f0f0;
    color: #333;
}

.btn-secondary:hover {
    background: #e0e0e0;
}

@media (max-width: 600px) {
    .confirmation-card {
        padding: 1rem;
    }

    .actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>
