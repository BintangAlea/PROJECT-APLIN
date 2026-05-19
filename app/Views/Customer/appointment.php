<?php
// Server-side multi-step appointment view (no JS)
// Available variables: $services, $beauticians, $draft, $userStage, $step
$draft = $draft ?? [];
$step = (int)($_GET['step'] ?? 1);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Buat Appointment - Merish</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--merish-primary:#7a545e;--merish-surface:#fff8f7}
        body{background:var(--merish-surface);font-family:Montserrat,Arial,Helvetica,sans-serif}
        .wizard{max-width:1100px;margin:28px auto;background:#fff;padding:28px;border-radius:10px;box-shadow:0 6px 22px rgba(90,60,70,0.06)}
        .category-card{border:1px solid #eee;border-radius:8px;padding:24px;text-align:center}
        .service-item{border:1px solid #f0e9e9;border-radius:8px;padding:12px;margin-bottom:10px}
        .promo{background:#fdeef2;color:#7a545e;padding:4px 8px;border-radius:6px;font-size:12px}
    </style>
</head>
<body>
    <div class="container">
        <div class="wizard">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Booking Wizard</h3>
                <div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <small class="text-muted">Hi, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></small>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn btn-outline-secondary btn-sm">Sign in</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($step === 1): ?>
                <form method="POST" action="index.php?page=customer&action=appointment&step=1">
                    <input type="hidden" name="step" value="1">
                    <h5>Select Category</h5>
                    <div class="row g-3 mb-4">
                        <?php $categories = ['Hair','Nails','Lashes','Wax & Eyebrows']; foreach ($categories as $cat): ?>
                            <div class="col-md-3">
                                <label class="category-card d-block">
                                    <input type="radio" name="category" value="<?php echo htmlspecialchars($cat); ?>" <?php if (($draft['category'] ?? '')===$cat) echo 'checked'; ?> required style="display:none">
                                    <h5><?php echo $cat; ?></h5>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <h5>Services</h5>
                    <div class="mb-3">
                        <?php
                            $selectedCat = $draft['category'] ?? null;
                            $filtered = [];
                            if ($selectedCat) {
                                foreach ($services as $s) {
                                    if (strtolower($s['category']) === strtolower($selectedCat)) $filtered[] = $s;
                                }
                            }
                            if (empty($filtered)) $filtered = $services;
                        ?>
                        <?php foreach ($filtered as $s): ?>
                            <div class="service-item">
                                <label>
                                    <input type="radio" name="service_id" value="<?php echo htmlspecialchars($s['service_id']); ?>" <?php if (($draft['service_id'] ?? '')===$s['service_id']) echo 'checked'; ?> required>
                                    <strong><?php echo htmlspecialchars($s['service_name']); ?></strong>
                                    <small class="text-muted"> Rp <?php echo number_format($s['base_tariff'],0,',','.'); ?></small>
                                    <?php if (!empty($s['promo'])): ?><span class="promo ms-2">Promo Available</span><?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?page=customer" class="btn btn-outline-secondary">Cancel</a>
                        <button class="btn btn-primary">Next</button>
                    </div>
                </form>

            <?php elseif ($step === 2): ?>
                <?php // Bundles: read promotions from DB via PromotionsModel ?>
                <?php $promModel = new \App\Models\PromotionsModel(); $promotions = $promModel->findByServiceId($draft['service_id'] ?? ''); ?>
                <form method="POST" action="index.php?page=customer&action=appointment&step=2">
                    <input type="hidden" name="step" value="2">
                    <h5>Bikin sesi perawatanmu makin rileks...</h5>
                    <div class="row g-3 mb-3">
                        <?php if ($promotions): foreach ($promotions as $p): ?>
                            <div class="col-md-6">
                                <div class="card p-3">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($p['promo_name']); ?></h6>
                                    <p class="small text-muted">Diskon: Rp <?php echo number_format($p['discount_value'],0,',','.'); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div><strong>Promo</strong></div>
                                        <div>
                                            <button type="submit" name="bundle_id" value="<?php echo htmlspecialchars($p['promo_id']); ?>" class="btn btn-outline-primary btn-sm">Add to Bill</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="col-12"><p class="text-muted">Tidak ada promo untuk layanan ini.</p></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php?page=customer&action=appointment&step=1" class="btn btn-outline-secondary">Back</a>
                        <button class="btn btn-primary">Next</button>
                    </div>
                </form>

            <?php elseif ($step === 3): ?>
                <?php // Schedule selection server-side ?>
                <?php
                    $maxDays = ($userStage === 3) ? 14 : 1;
                    $dates = [];
                    $today = new DateTime();
                    for ($i=0;$i<=$maxDays;$i++){
                        $d = clone $today; $d->modify("+{$i} day");
                        $dates[] = $d->format('Y-m-d');
                    }
                    $times = ['10:00','11:30','13:00','14:30','16:00'];
                ?>
                <form method="POST" action="index.php?page=customer&action=appointment&step=3">
                    <input type="hidden" name="step" value="3">
                    <h5>Pilih Tanggal & Waktu</h5>
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <select name="reservation_date" class="form-select" required>
                            <?php foreach ($dates as $d): ?>
                                <option value="<?php echo $d; ?>" <?php if (($draft['reservation_date'] ?? '')===$d) echo 'selected'; ?>><?php echo date('D, d M Y', strtotime($d)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jam</label>
                        <select name="reservation_time" class="form-select" required>
                            <?php foreach ($times as $t): ?>
                                <option value="<?php echo $t; ?>" <?php if (($draft['reservation_time'] ?? '')===$t) echo 'selected'; ?>><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php?page=customer&action=appointment&step=2" class="btn btn-outline-secondary">Back</a>
                        <button class="btn btn-primary">Next</button>
                    </div>
                </form>

            <?php elseif ($step === 4): ?>
                <?php // Beautician selection: filter by specialization server-side ?>
                <?php
                    $category = $draft['category'] ?? '';
                    $mapping = ['Hair'=>'Hair Stylist','Nails'=>'Nailist','Lashes'=>'Lash Technician','Wax & Eyebrows'=>'Wax & Threading Specialist'];
                    $expectedSpec = $mapping[$category] ?? null;
                ?>
                <form method="POST" action="index.php?page=customer&action=appointment&step=4">
                    <input type="hidden" name="step" value="4">
                    <h5>Pilih Kapster</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card p-3 text-center">
                                <h6>Any Available Staff</h6>
                                <p class="small text-muted">Biarkan sistem memilih</p>
                                <div class="mt-2">
                                    <button type="submit" name="beautician_id" value="" class="btn btn-outline-secondary btn-sm">Pilih</button>
                                </div>
                            </div>
                        </div>
                        <?php foreach ($beauticians as $b):
                            $show = true;
                            if ($expectedSpec && stripos($b['specialization'], $expectedSpec) === false) $show = false;
                            if (!$show) continue;
                        ?>
                            <div class="col-md-4">
                                <div class="card p-3 text-center">
                                    <div style="height:120px;display:flex;align-items:center;justify-content:center;">
                                        <img src="/SIB/PROJECT-APLIN/assets/images/avatar-placeholder.png" alt="" style="max-width:100%;filter:grayscale(100%);">
                                    </div>
                                    <h6 class="mt-2"><?php echo htmlspecialchars($b['NAME']); ?></h6>
                                    <p class="small text-muted"><?php echo htmlspecialchars($b['specialization']); ?></p>
                                    <div class="mt-2">
                                        <button type="submit" name="beautician_id" value="<?php echo $b['profile_id']; ?>" class="btn btn-outline-secondary btn-sm">Pilih</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <a href="index.php?page=customer&action=appointment&step=3" class="btn btn-outline-secondary">Back</a>
                        <button class="btn btn-primary">Next</button>
                    </div>
                </form>

            <?php elseif ($step === 5): ?>
                <?php // Review & DP ?>
                <?php
                    // $service is passed from controller
                    // If not available, fetch it here as fallback
                    if (!$service && !empty($draft['service_id'])) {
                        $service = (new \App\Models\ServicesModel())->findById($draft['service_id']);
                    }
                    $bundleText = '';
                    $bundleDiscount = 0;
                    if (!empty($draft['bundle_id'])) {
                        $prom = (new \App\Models\PromotionsModel())->findByServiceId($draft['service_id'] ?? '');
                        $bundleText = $prom[0]['promo_name'] ?? '';
                        $bundleDiscount = $prom[0]['discount_value'] ?? 0;
                    }
                    $total = ($service['base_tariff'] ?? 0) - $bundleDiscount; // include bundle discount
                ?>
                <form method="POST" action="index.php?page=customer&action=appointment&step=5" enctype="multipart/form-data">
                    <input type="hidden" name="step" value="5">
                    <h5>Review & Pembayaran DP</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6>Virtual Struk</h6>
                                <p><?php echo htmlspecialchars($service['service_name'] ?? ''); ?> - Rp <?php echo number_format($service['base_tariff'] ?? 0,0,',','.'); ?></p>
                                <?php if ($bundleText): ?><p>Bundle: <?php echo htmlspecialchars($bundleText); ?></p><?php endif; ?>
                                <hr>
                                <p><strong>Total: Rp <?php echo number_format($total,0,',','.'); ?></strong></p>
                                <p><strong>Wajib DP: Rp 50.000</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 text-center">
                                <h6>Scan QRIS untuk Bayar DP</h6>
                                <img src="/SIB/PROJECT-APLIN/assets/images/qris-placeholder.png" alt="QRIS" style="max-width:260px">
                                <div class="mt-3">
                                    <label class="form-label">Upload Bukti Bayar (opsional)</label>
                                    <input type="file" name="dp_proof" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" name="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">Saya setuju dengan Syarat & Ketentuan</label>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="index.php?page=customer&action=appointment&step=4" class="btn btn-outline-secondary">Back</a>
                        <button class="btn btn-primary">Confirm Appointment</button>
                    </div>
                </form>

            <?php else: ?>
                <p>Step tidak valid.</p>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>


