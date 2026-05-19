<?php 
$pageTitle = "Order Confirmed - MERISH Cafe";
include __DIR__ . '/../Layout/header.php'; 
?>

<!-- Confirmation Section -->
<section class="py-5" style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle" style="font-size: 4rem; color: #4CAF50;"></i>
                    </div>
                    <h2 class="mb-3">Pesanan Berhasil Dibuat!</h2>
                    <p class="lead text-muted mb-4">
                        Pesanan Anda sedang kami proses. Barista akan segera mempersiapkan minuman Anda.
                    </p>

                    <div class="alert alert-info" role="alert">
                        <h5 class="mb-3">📋 Rincian Pesanan</h5>
                        <p class="mb-2"><strong>Order ID:</strong> #<?php echo date('YmdHis') . rand(100, 999); ?></p>
                        <p class="mb-2"><strong>Status:</strong> <span class="badge bg-primary">In Progress</span></p>
                        <p class="mb-0"><strong>Estimasi Selesai:</strong> 5-10 Menit</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-3">Apa yang Terjadi Selanjutnya?</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="mb-2"><i class="fas fa-brewing fa-2x" style="color: var(--merish-primary);"></i></h6>
                                    <small><strong>Sedang Disiapkan</strong><br>Tim barista kami sedang membuat pesanan Anda</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="mb-2"><i class="fas fa-bell fa-2x" style="color: var(--merish-secondary);"></i></h6>
                                    <small><strong>Notifikasi</strong><br>Kami akan memberitahu saat pesanan siap</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="mb-2"><i class="fas fa-mug-hot fa-2x" style="color: #D2691E;"></i></h6>
                                    <small><strong>Ambil & Nikmati</strong><br>Ambil pesanan Anda di bar atau diantar ke meja</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <h5 class="mb-3">💡 Saran untuk Anda</h5>
                        <div class="alert alert-warning" role="alert">
                            <p class="mb-2"><strong>Jangan lupa booking salon!</strong></p>
                            <p class="text-muted mb-0">
                                Sudah tahu mau perawatan apa hari ini? 
                                <a href="index.php?page=services">Lihat layanan kami</a> dan dapatkan diskon bundling hingga 20%!
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="index.php?page=cafe" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Kembali ke Menu
                        </a>
                        <a href="index.php?page=services" class="btn btn-merish">
                            <i class="fas fa-calendar-check"></i> Book Treatment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../Layout/footer.php'; ?>
