<?php 
$pageTitle = "Home - MERISH Salon & Cafe";
include __DIR__ . '/../Layout/header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="mb-3">Selamat Datang di MERISH</h1>
        <p class="mb-4">Salon premium meets cozy cafe - Tempat sempurna untuk self-care dan relaksasi</p>
        <div>
            <a href="index.php?page=customer&action=appointment" class="btn btn-light btn-lg me-3 mb-2" style="font-weight: 600; color: var(--merish-primary);">
                <i class="fas fa-calendar-check"></i> Book an Appointment
            </a>
            <a href="index.php?page=cafe" class="btn btn-outline-light btn-lg mb-2" style="font-weight: 600;">
                <i class="fas fa-coffee"></i> Order Your Drink
            </a>
        </div>
    </div>
</section>

<!-- Trust Section with Certificates -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="section-title mb-5">Dipercaya & Tersertifikasi</h3>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="p-4">
                    <i class="fas fa-certificate fa-3x mb-3" style="color: var(--merish-primary);"></i>
                    <h5>ISO Certified</h5>
                    <p class="small text-muted">Quality Management System</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="p-4">
                    <i class="fas fa-shield-alt fa-3x mb-3" style="color: var(--merish-primary);"></i>
                    <h5>100% Hygiene</h5>
                    <p class="small text-muted">Standar kebersihan internasional</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="p-4">
                    <i class="fas fa-star fa-3x mb-3" style="color: var(--merish-primary);"></i>
                    <h5>Award Winner</h5>
                    <p class="small text-muted">Best Salon & Cafe 2024</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="p-4">
                    <i class="fas fa-users fa-3x mb-3" style="color: var(--merish-primary);"></i>
                    <h5>50K+ Members</h5>
                    <p class="small text-muted">Komunitas pelanggan setia</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Preview -->
<section class="py-5">
    <div class="container">
        <h3 class="section-title">Layanan Unggulan Kami</h3>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card card-service">
                    <div style="background: linear-gradient(135deg, #FFB6C1 0%, #FFC0CB 100%); height: 150px;"></div>
                    <div class="card-body">
                        <h5 class="card-title">Hair Treatment</h5>
                        <p class="card-text text-muted small">Creambath, coloring, hingga smoothing</p>
                        <div class="badge-promo">5 layanan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-service">
                    <div style="background: linear-gradient(135deg, #FFE4E1 0%, #FFB6C1 100%); height: 150px;"></div>
                    <div class="card-body">
                        <h5 class="card-title">Nail Art</h5>
                        <p class="card-text text-muted small">Gel polish, acrylic, nail design</p>
                        <div class="badge-promo">8 layanan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-service">
                    <div style="background: linear-gradient(135deg, #E6F2FF 0%, #D0E8FF 100%); height: 150px;"></div>
                    <div class="card-body">
                        <h5 class="card-title">Lash Extension</h5>
                        <p class="card-text text-muted small">Natural, volume, dan mega lash</p>
                        <div class="badge-promo">3 layanan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-service">
                    <div style="background: linear-gradient(135deg, #FFF8DC 0%, #FFE4B5 100%); height: 150px;"></div>
                    <div class="card-body">
                        <h5 class="card-title">Wax & Eyebrow</h5>
                        <p class="card-text text-muted small">Threading dan waxing profesional</p>
                        <div class="badge-promo">4 layanan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="index.php?page=services" class="btn btn-merish btn-lg">Lihat Semua Layanan</a>
        </div>
    </div>
</section>

<!-- Testimonials Slider -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="section-title">Apa Kata Pelanggan Kami</h3>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card card-service">
                    <div class="card-body">
                        <div class="mb-3">
                            <span style="color: #FFD700;">★★★★★</span>
                        </div>
                        <p class="card-text">"Pelayanan super ramah, hasil salon bagus, dan cafe-nya enak! Jadi 2 jam di Merish tanpa terasa. Recommend banget!"</p>
                        <h6 class="mt-3 mb-0"><strong>Siska Wijaya</strong></h6>
                        <small class="text-muted">VIP Member</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card card-service">
                    <div class="card-body">
                        <div class="mb-3">
                            <span style="color: #FFD700;">★★★★★</span>
                        </div>
                        <p class="card-text">"Sistemnya canggih, dari booking sampai pembayaran semua mudah. Kapsternya profesional dan hasilnya maksimal."</p>
                        <h6 class="mt-3 mb-0"><strong>Budi Santoso</strong></h6>
                        <small class="text-muted">Loyal Member</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card card-service">
                    <div class="card-body">
                        <div class="mb-3">
                            <span style="color: #FFD700;">★★★★★</span>
                        </div>
                        <p class="card-text">"First time di Merish, langsung takjub. Suasananya cozy, stafnya welcoming, dan produk beauty-nya premium!"</p>
                        <h6 class="mt-3 mb-0"><strong>Rina Putri</strong></h6>
                        <small class="text-muted">Regular Member</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--merish-primary) 0%, var(--merish-secondary) 100%);">
    <div class="container text-center text-white">
        <h2 class="mb-3">Siap untuk Me-Time Terbaik?</h2>
        <p class="mb-4 lead">Nikmati diskon spesial untuk member baru hingga 20%</p>
        <div>
            <a href="index.php?page=login" class="btn btn-light btn-lg me-3" style="font-weight: 600; color: var(--merish-primary);">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </a>
            <a href="index.php?page=customer&action=appointment" class="btn btn-outline-light btn-lg" style="font-weight: 600;">
                <i class="fas fa-calendar-check"></i> Booking Appointment
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../Layout/footer.php'; ?>
