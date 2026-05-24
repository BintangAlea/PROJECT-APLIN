<?php
/**
 * Booking Step 2: Bundles & Add-ons Selection
 */
?>
<?php include __DIR__ . '/../Layout/base.php'; ?>

<div class="booking-container">
    <div class="booking-header">
        <h1><?= htmlspecialchars($title ?? 'Pilih Paket') ?></h1>
        <p>Lengkapi pilihan Anda dengan paket bundel atau tambahan</p>
    </div>

    <div class="booking-progress">
        <div class="progress-bar">
            <div class="progress-step">1</div>
            <div class="progress-line"></div>
            <div class="progress-step active">2</div>
            <div class="progress-line"></div>
            <div class="progress-step">3</div>
            <div class="progress-line"></div>
            <div class="progress-step">4</div>
            <div class="progress-line"></div>
            <div class="progress-step">5</div>
            <div class="progress-line"></div>
            <div class="progress-step">6</div>
        </div>
    </div>

    <?php if (isset($_SESSION['booking_error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['booking_error']) ?>
            <?php unset($_SESSION['booking_error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/index.php?page=booking&step=2" class="booking-form">
        <!-- Bundles Section -->
        <div class="form-section">
            <h2>Paket Bundel (Opsional)</h2>
            <div class="bundles-list">
                <label class="bundle-option">
                    <input type="radio" name="bundle_id" value="">
                    <span class="option-content">
                        <strong>Tanpa Paket</strong>
                        <small>Lanjut dengan layanan biasa</small>
                    </span>
                </label>

                <?php if (!empty($bundles)): ?>
                    <?php foreach ($bundles as $bundle): ?>
                        <label class="bundle-option">
                            <input type="radio" name="bundle_id" value="<?= (int)$bundle['bundle_id'] ?>">
                            <span class="option-content">
                                <strong><?= htmlspecialchars($bundle['bundle_name']) ?></strong>
                                <small><?= htmlspecialchars($bundle['description'] ?? '') ?></small>
                                <div class="bundle-details">
                                    <span class="services-count"><?= (int)$bundle['service_count'] ?> layanan</span>
                                    <span class="savings">Hemat <?= (int)$bundle['discount_percentage'] ?>%</span>
                                    <span class="final-price">Rp <?= number_format($bundle['final_price'], 0, ',', '.') ?></span>
                                </div>
                            </span>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add-ons Section -->
        <div class="form-section">
            <h2>Tambahan (Opsional)</h2>
            <div class="addons-grid">
                <?php if (!empty($addons)): ?>
                    <?php foreach ($addons as $type => $addonsList): ?>
                        <div class="addons-group">
                            <h3><?= htmlspecialchars($type) ?></h3>
                            <?php foreach ($addonsList as $addon): ?>
                                <label class="addon-checkbox">
                                    <input type="checkbox" name="addon_ids[]" value="<?= (int)$addon['addon_id'] ?>">
                                    <span class="addon-content">
                                        <strong><?= htmlspecialchars($addon['addon_name']) ?></strong>
                                        <span class="addon-price">Rp <?= number_format($addon['price'], 0, ',', '.') ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="booking-actions">
            <a href="/index.php?page=booking&step=1" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-primary">Lanjut →</button>
        </div>
    </form>
</div>

<style>
.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.form-section h2 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #333;
}

.bundles-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.bundle-option {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.bundle-option:hover {
    border-color: #d4a574;
    background: #fafaf5;
}

.bundle-option input[type="radio"]:checked + .option-content {
    color: #d4a574;
}

.bundle-option input[type="radio"] {
    margin-right: 1rem;
    cursor: pointer;
}

.option-content {
    flex: 1;
}

.option-content strong {
    display: block;
    margin-bottom: 0.25rem;
}

.option-content small {
    color: #666;
    display: block;
    margin-bottom: 0.5rem;
}

.bundle-details {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    margin-top: 0.5rem;
    flex-wrap: wrap;
}

.services-count {
    background: #f0f0f0;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
    color: #666;
}

.savings {
    background: #d4f0d4;
    color: #2d5a2d;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
    font-weight: bold;
}

.final-price {
    font-weight: bold;
    color: #d4a574;
    margin-left: auto;
}

.addons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.addons-group h3 {
    font-size: 1rem;
    margin-bottom: 0.75rem;
    color: #555;
}

.addon-checkbox {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.addon-checkbox:hover {
    background: #fafaf5;
}

.addon-checkbox input[type="checkbox"] {
    margin-right: 0.75rem;
    cursor: pointer;
}

.addon-content {
    flex: 1;
}

.addon-content strong {
    display: block;
}

.addon-price {
    font-weight: bold;
    color: #d4a574;
    display: block;
    font-size: 0.9rem;
    margin-top: 0.25rem;
}
</style>
