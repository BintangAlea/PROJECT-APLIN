<?php
/**
 * Booking Step 3: Date & Time Selection
 */
?>
<?php include __DIR__ . '/../Layout/base.php'; ?>

<div class="booking-container">
    <div class="booking-header">
        <h1><?= htmlspecialchars($title ?? 'Pilih Tanggal & Waktu') ?></h1>
        <p>Tentukan kapan Anda ingin datang</p>
    </div>

    <div class="booking-progress">
        <div class="progress-bar">
            <div class="progress-step">1</div>
            <div class="progress-line"></div>
            <div class="progress-step">2</div>
            <div class="progress-line"></div>
            <div class="progress-step active">3</div>
            <div class="progress-line"></div>
            <div class="progress-step">4</div>
            <div class="progress-line"></div>
            <div class="progress-step">5</div>
            <div class="progress-line"></div>
            <div class="progress-step">6</div>
        </div>
    </div>

    <form method="POST" action="/index.php?page=booking&step=3" class="booking-form">
        <div class="form-group">
            <label for="reservation_date">Tanggal</label>
            <input type="date" id="reservation_date" name="reservation_date" 
                   min="<?= htmlspecialchars($min_date) ?>" 
                   max="<?= htmlspecialchars($max_date) ?>" 
                   required>
            <small>Pilih tanggal dalam 30 hari ke depan</small>
        </div>

        <div class="form-group">
            <label for="reservation_time">Waktu</label>
            <select id="reservation_time" name="reservation_time" required>
                <option value="">-- Pilih Waktu --</option>
                <option value="09:00">09:00 - 10:30</option>
                <option value="10:00">10:00 - 11:30</option>
                <option value="11:00">11:00 - 12:30</option>
                <option value="13:00">13:00 - 14:30</option>
                <option value="14:00">14:00 - 15:30</option>
                <option value="15:00">15:00 - 16:30</option>
                <option value="16:00">16:00 - 17:30</option>
            </select>
        </div>

        <div class="booking-actions">
            <a href="/index.php?page=booking&step=2" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-primary">Lanjut →</button>
        </div>
    </form>
</div>

<style>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

.form-group input[type="date"],
.form-group select {
    width: 100%;
    max-width: 400px;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input[type="date"]:focus,
.form-group select:focus {
    outline: none;
    border-color: #d4a574;
    box-shadow: 0 0 0 2px rgba(212, 165, 116, 0.1);
}

.form-group small {
    display: block;
    margin-top: 0.25rem;
    color: #666;
    font-size: 0.85rem;
}
</style>
