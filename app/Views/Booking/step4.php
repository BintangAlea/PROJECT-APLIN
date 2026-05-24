<?php
/**
 * Booking Step 4: Beautician Selection
 */
?>
<?php include __DIR__ . '/../Layout/base.php'; ?>

<div class="booking-container">
    <div class="booking-header">
        <h1><?= htmlspecialchars($title ?? 'Pilih Beautician') ?></h1>
        <p>Pilih beautician pilihan Anda atau biarkan sistem memilih</p>
    </div>

    <div class="booking-progress">
        <div class="progress-bar">
            <div class="progress-step">1</div>
            <div class="progress-line"></div>
            <div class="progress-step">2</div>
            <div class="progress-line"></div>
            <div class="progress-step">3</div>
            <div class="progress-line"></div>
            <div class="progress-step active">4</div>
            <div class="progress-line"></div>
            <div class="progress-step">5</div>
            <div class="progress-line"></div>
            <div class="progress-step">6</div>
        </div>
    </div>

    <form method="POST" action="/index.php?page=booking&step=4" class="booking-form">
        <div class="beauticians-grid">
            <label class="beautician-card">
                <input type="radio" name="beautician_id" value="">
                <div class="beautician-content">
                    <div class="beautician-avatar">👩</div>
                    <h3>Sistem Memilih</h3>
                    <p class="specialization">Beautician terbaik tersedia</p>
                </div>
            </label>

            <?php if (!empty($beauticians)): ?>
                <?php foreach ($beauticians as $beautician): ?>
                    <label class="beautician-card">
                        <input type="radio" name="beautician_id" value="<?= (int)$beautician['user_id'] ?>">
                        <div class="beautician-content">
                            <?php if (!empty($beautician['photo_url'])): ?>
                                <div class="beautician-avatar">
                                    <img src="<?= htmlspecialchars($beautician['photo_url']) ?>" alt="">
                                </div>
                            <?php else: ?>
                                <div class="beautician-avatar">👩</div>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($beautician['name']) ?></h3>
                            <p class="specialization"><?= htmlspecialchars($beautician['specialization'] ?? 'Beauty Specialist') ?></p>
                            <div class="rating">
                                ⭐ <?= number_format($beautician['rating'] ?? 0, 1) ?>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="info-box">
            <?php if (!$is_logged_in): ?>
                <p>📝 Anda akan diminta untuk login atau mendaftar di langkah berikutnya</p>
            <?php endif; ?>
        </div>

        <div class="booking-actions">
            <a href="/index.php?page=booking&step=3" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-primary">Lanjut →</button>
        </div>
    </form>
</div>

<style>
.beauticians-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.beautician-card {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    background: white;
}

.beautician-card:hover {
    border-color: #d4a574;
    box-shadow: 0 4px 12px rgba(212, 165, 116, 0.1);
}

.beautician-card input[type="radio"] {
    display: none;
}

.beautician-card input[type="radio"]:checked + .beautician-content {
    color: #d4a574;
}

.beautician-content {
    transition: color 0.3s;
}

.beautician-avatar {
    width: 80px;
    height: 80px;
    margin: 0 auto 0.75rem;
    background: #f5f5f5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    overflow: hidden;
}

.beautician-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.beautician-content h3 {
    margin: 0.5rem 0;
    font-size: 1.1rem;
}

.specialization {
    font-size: 0.85rem;
    color: #666;
    margin: 0.5rem 0;
}

.rating {
    color: #f39c12;
    font-weight: bold;
    margin-top: 0.5rem;
}

.info-box {
    background: #f0f8ff;
    border-left: 4px solid #d4a574;
    padding: 1rem;
    margin-bottom: 2rem;
    border-radius: 4px;
}

.info-box p {
    margin: 0;
    color: #333;
}
</style>
