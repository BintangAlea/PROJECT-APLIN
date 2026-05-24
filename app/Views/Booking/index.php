<?php
/**
 * Booking Index: Landing Page
 */
?>
<?php include __DIR__ . '/../Layout/base.php'; ?>

<div class="booking-landing">
    <div class="landing-hero">
        <div class="hero-content">
            <h1>Pesan Layanan Kecantikan Anda Sekarang</h1>
            <p>Dapatkan pengalaman kecantikan terbaik dengan beautician profesional kami</p>
            <a href="/index.php?page=booking&step=1" class="btn btn-large btn-primary">Mulai Pemesanan</a>
        </div>
    </div>

    <div class="features-section">
        <div class="container">
            <h2>Mengapa Memilih Kami?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">👩‍🦰</div>
                    <h3>Beautician Profesional</h3>
                    <p>Tim beautician berpengalaman dan tersertifikasi</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📅</div>
                    <h3>Jadwal Fleksibel</h3>
                    <p>Pesan sesuai waktu yang Anda inginkan</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Harga Terjangkau</h3>
                    <p>Paket bundel dengan harga spesial</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h3>Hasil Terjamin</h3>
                    <p>Kepuasan pelanggan adalah prioritas kami</p>
                </div>
            </div>
        </div>
    </div>

    <div class="services-preview">
        <div class="container">
            <h2>Layanan Kami</h2>
            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php $count = 0; foreach ($services as $service): $count++; if ($count > 3) break; ?>
                        <div class="service-preview">
                            <?php if (!empty($service['image_url'])): ?>
                                <img src="<?= htmlspecialchars($service['image_url']) ?>" alt="">
                            <?php else: ?>
                                <div class="image-placeholder">📷</div>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($service['service_name']) ?></h3>
                            <p><?= htmlspecialchars($service['description'] ?? '') ?></p>
                            <div class="price">Rp <?= number_format($service['base_tariff'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center" style="margin-top: 2rem;">
                <a href="/index.php?page=booking&step=1" class="btn btn-primary">Lihat Semua Layanan</a>
            </div>
        </div>
    </div>
</div>

<style>
.booking-landing {
    background: white;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.landing-hero {
    background: linear-gradient(135deg, #d4a574 0%, #c29458 100%);
    color: white;
    padding: 4rem 1rem;
    text-align: center;
}

.hero-content h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.hero-content p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.btn-primary {
    background: white;
    color: #d4a574;
    text-decoration: none;
    display: inline-block;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: bold;
}

.btn-primary:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.features-section {
    padding: 4rem 1rem;
    background: #f9f9f9;
}

.features-section h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 3rem;
    color: #333;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: white;
    padding: 2rem;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(212, 165, 116, 0.2);
}

.feature-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.feature-card h3 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.feature-card p {
    color: #666;
    line-height: 1.6;
}

.services-preview {
    padding: 4rem 1rem;
}

.services-preview h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 3rem;
    color: #333;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.service-preview {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.service-preview:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(212, 165, 116, 0.2);
}

.service-preview img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.image-placeholder {
    width: 100%;
    height: 200px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}

.service-preview h3 {
    padding: 1rem 1rem 0.5rem;
    margin: 0;
    font-size: 1.1rem;
    color: #333;
}

.service-preview p {
    padding: 0 1rem;
    margin: 0.5rem 0;
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
}

.price {
    padding: 1rem;
    font-weight: bold;
    color: #d4a574;
    font-size: 1.1rem;
}

.text-center {
    text-align: center;
}
</style>
