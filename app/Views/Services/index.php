<?php 
$pageTitle = "Services - MERISH Salon & Cafe";
include __DIR__ . '/../Layout/header.php'; 
?>

<!-- Services Hero -->
<section style="background: linear-gradient(135deg, var(--merish-primary) 0%, var(--merish-secondary) 100%); color: white; padding: 60px 0;">
    <div class="container">
        <h1 class="mb-3">Our Signature Treatments</h1>
        <p class="lead mb-0">✨ Get 20% Synergy Discount when combining Salon & Cafe orders!</p>
    </div>
</section>

<!-- Category Filter -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="index.php?page=services&category=ALL" class="btn <?php echo ($category === 'ALL') ? 'btn-merish' : 'btn-outline-secondary'; ?>">
                [ ALL ]
            </a>
            <a href="index.php?page=services&category=Hair" class="btn <?php echo ($category === 'Hair') ? 'btn-merish' : 'btn-outline-secondary'; ?>">
                [ HAIR ]
            </a>
            <a href="index.php?page=services&category=Nails" class="btn <?php echo ($category === 'Nails') ? 'btn-merish' : 'btn-outline-secondary'; ?>">
                [ NAILS ]
            </a>
            <a href="index.php?page=services&category=Lashes" class="btn <?php echo ($category === 'Lashes') ? 'btn-merish' : 'btn-outline-secondary'; ?>">
                [ LASHES ]
            </a>
            <a href="index.php?page=services&category=Wax & Eyebrows" class="btn <?php echo ($category === 'Wax & Eyebrows') ? 'btn-merish' : 'btn-outline-secondary'; ?>">
                [ WAX & EYEBROWS ]
            </a>
        </div>
    </div>
</section>

<!-- Services Catalog -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-service h-100">
                            <div style="background: linear-gradient(135deg, var(--merish-secondary) 0%, var(--merish-accent) 100%); height: 180px; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                                <?php 
                                $icons = [
                                    'Hair' => '💇',
                                    'Nails' => '💅',
                                    'Lashes' => '✨',
                                    'Wax & Eyebrows' => '🧵'
                                ];
                                echo $icons[$service['category']] ?? '💄';
                                ?>
                            </div>
                            <div class="card-body">
                                <span class="badge-promo"><?php echo htmlspecialchars($service['category']); ?></span>
                                <h5 class="card-title mt-2"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                                <p class="card-text text-muted small">
                                    💰 Mulai dari <strong>Rp <?php echo number_format($service['base_tariff'], 0, ',', '.'); ?></strong>
                                    <br>⏱️ Durasi: <?php echo $service['est_duration']; ?> menit
                                </p>
                            </div>
                            <div class="card-footer bg-light border-0">
                                <a href="index.php?page=customer&action=appointment" class="btn btn-merish w-100">
                                    <i class="fas fa-calendar-check"></i> BOOK NOW
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-muted">Tidak ada layanan ditemukan</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Top Performers Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">Our Top Performers</h2>
        <div class="row g-4">
            <?php if (count($beauticians) > 0): ?>
                <?php $counter = 0; foreach ($beauticians as $beautician): $counter++; if ($counter > 6) break; ?>
                    <div class="col-md-4 col-lg-2 text-center">
                        <div class="mb-3">
                            <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--merish-secondary) 0%, var(--merish-primary) 100%); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700;">
                                <?php echo strtoupper(substr($beautician['NAME'], 0, 2)); ?>
                            </div>
                        </div>
                        <h6 class="mb-1"><strong><?php echo htmlspecialchars($beautician['NAME']); ?></strong></h6>
                        <p class="small text-muted mb-2">
                            <?php 
                            $spec_labels = [
                                'Hair Stylist' => '💇 Hair Stylist',
                                'Nailist' => '💅 Master Nailist',
                                'Lash Technician' => '✨ Lash Expert',
                                'Wax & Threading Specialist' => '🧵 Threading Pro'
                            ];
                            echo $spec_labels[$beautician['specialization']] ?? $beautician['specialization'];
                            ?>
                        </p>
                        <p style="color: #FFD700; font-size: 0.9rem;">⭐⭐⭐⭐⭐</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-muted">Data beautician tidak tersedia</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--merish-primary) 0%, var(--merish-secondary) 100%);">
    <div class="container text-center text-white">
        <h2 class="mb-3">Ready to Book Your Treatment?</h2>
        <p class="mb-4 lead">Pilih layanan, pilih kapster favorit, dan nikmati pengalaman salon terbaik</p>
        <a href="index.php?page=customer&action=appointment" class="btn btn-light btn-lg" style="font-weight: 600; color: var(--merish-primary);">
            <i class="fas fa-calendar-check"></i> Book Appointment Now
        </a>
    </div>
</section>

<?php include __DIR__ . '/../Layout/footer.php'; ?>
