<?php
/**
 * Booking Step 5: Review & Pricing
 */
?>
<?php include __DIR__ . '/../Layout/base.php'; ?>

<div class="booking-container">
    <div class="booking-header">
        <h1><?= htmlspecialchars($title ?? 'Konfirmasi Pesanan') ?></h1>
        <p>Periksa rincian pesanan Anda</p>
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
            <div class="progress-step active">5</div>
            <div class="progress-line"></div>
            <div class="progress-step">6</div>
        </div>
    </div>

    <form method="POST" action="/index.php?page=booking&action=submit" class="booking-form">
        <div class="review-container">
            <div class="review-details">
                <!-- Service Info -->
                <div class="review-section">
                    <h2>Layanan</h2>
                    <?php if (!empty($details['bundle'])): ?>
                        <div class="detail-item">
                            <span class="label">Paket</span>
                            <span class="value"><?= htmlspecialchars($details['bundle']['bundle_name']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Layanan Termasuk</span>
                            <div class="value">
                                <?php foreach ($details['bundle']['services'] ?? [] as $service): ?>
                                    <div>• <?= htmlspecialchars($service['service_name']) ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php elseif (!empty($details['service'])): ?>
                        <div class="detail-item">
                            <span class="label">Layanan</span>
                            <span class="value"><?= htmlspecialchars($details['service']['service_name']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Add-ons Info -->
                <?php if (!empty($details['addons'])): ?>
                    <div class="review-section">
                        <h2>Tambahan</h2>
                        <?php foreach ($details['addons'] as $addon): ?>
                            <div class="detail-item">
                                <span class="label">+ <?= htmlspecialchars($addon['addon_name']) ?></span>
                                <span class="value">Rp <?= number_format($addon['price'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Date & Time Info -->
                <div class="review-section">
                    <h2>Jadwal</h2>
                    <div class="detail-item">
                        <span class="label">Tanggal</span>
                        <span class="value"><?= date('d M Y', strtotime($details['date'])) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Waktu</span>
                        <span class="value"><?= htmlspecialchars($details['time']) ?></span>
                    </div>
                </div>

                <!-- Beautician Info -->
                <?php if (!empty($details['beautician'])): ?>
                    <div class="review-section">
                        <h2>Beautician</h2>
                        <div class="beautician-info">
                            <div class="beautician-name"><?= htmlspecialchars($details['beautician']['name']) ?></div>
                            <div class="beautician-spec"><?= htmlspecialchars($details['beautician']['specialization']) ?></div>
                            <div class="beautician-rating">⭐ <?= number_format($details['beautician']['rating'] ?? 0, 1) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Promo Code -->
                <div class="review-section">
                    <h2>Kode Promo (Opsional)</h2>
                    <div class="form-group">
                        <input type="text" name="promo_code" placeholder="Masukkan kode promo...">
                    </div>
                </div>
            </div>

            <!-- Pricing Sidebar -->
            <div class="pricing-summary">
                <h2>Ringkasan Harga</h2>
                
                <div class="price-row">
                    <span>Harga Dasar</span>
                    <span>Rp <?= number_format($pricing['base_price'], 0, ',', '.') ?></span>
                </div>

                <?php if ($pricing['addons_price'] > 0): ?>
                    <div class="price-row">
                        <span>Tambahan</span>
                        <span>Rp <?= number_format($pricing['addons_price'], 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($pricing['promo_discount'] > 0): ?>
                    <div class="price-row promo">
                        <span>Diskon Promo</span>
                        <span>-Rp <?= number_format($pricing['promo_discount'], 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>

                <div class="price-row divider"></div>

                <div class="price-row total">
                    <span>Total</span>
                    <span>Rp <?= number_format($pricing['total_price'], 0, ',', '.') ?></span>
                </div>

                <div class="payment-method">
                    <label>Metode Pembayaran</label>
                    <select name="payment_method" required>
                        <option value="cash">Tunai</option>
                        <option value="card">Kartu Kredit/Debit</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-full btn-large">
                    Konfirmasi & Lanjut ke Pembayaran
                </button>
            </div>
        </div>

        <div class="booking-actions">
            <a href="/index.php?page=booking&step=4" class="btn btn-secondary">← Kembali</a>
        </div>
    </form>
</div>

<style>
.review-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
    margin-bottom: 2rem;
}

.review-details {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
}

.review-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.review-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.review-section h2 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: #333;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
}

.detail-item .label {
    font-weight: 600;
    color: #666;
}

.detail-item .value {
    color: #333;
    text-align: right;
    flex: 1;
    margin-left: 1rem;
}

.beautician-info {
    padding: 1rem;
    background: #f9f9f9;
    border-radius: 4px;
}

.beautician-name {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
}

.beautician-spec {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.beautician-rating {
    color: #f39c12;
    font-weight: bold;
}

.pricing-summary {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 1.5rem;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.pricing-summary h2 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.price-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.price-row.total {
    font-size: 1.1rem;
    font-weight: bold;
    color: #d4a574;
    margin-top: 0.5rem;
}

.price-row.promo {
    color: #2d5a2d;
    font-weight: bold;
}

.price-row.divider {
    border-bottom: 1px solid #ddd;
    margin: 1rem 0;
}

.payment-method {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.payment-method label {
    display: block;
    font-weight: bold;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.payment-method select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
}

.btn-large {
    font-size: 1.05rem;
    padding: 1rem;
}

@media (max-width: 768px) {
    .review-container {
        grid-template-columns: 1fr;
    }
    
    .pricing-summary {
        position: static;
    }
}
</style>
