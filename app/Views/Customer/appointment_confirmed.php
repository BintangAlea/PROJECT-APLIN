<?php include __DIR__ . '/../Layout/header.php'; ?>

<div class="container py-5">
    <div class="card p-4 text-center">
        <h2>Booking Confirmed!</h2>
        <p class="text-muted">Reservation ID: <strong>#<?php echo htmlspecialchars($reservation['res_id'] ?? ''); ?></strong></p>

        <div class="my-3">
            <div class="card mx-auto" style="max-width:400px;padding:20px;text-align:left;">
                <h5 class="mb-2"><?php echo htmlspecialchars($reservation['service_name'] ?? 'Service'); ?></h5>
                <p class="mb-1">Date: <?php echo htmlspecialchars(date('d M Y', strtotime($reservation['schedule_time'] ?? ''))); ?></p>
                <p class="mb-1">Time: <?php echo htmlspecialchars(date('H:i', strtotime($reservation['schedule_time'] ?? ''))); ?></p>
                <p class="mb-1">Status: <?php echo htmlspecialchars($reservation['STATUS'] ?? ''); ?></p>
                <div style="text-align:center;margin-top:12px;">
                    <img src="/SIB/PROJECT-APLIN/assets/images/qr-ticket-placeholder.png" style="max-width:140px;" alt="QR Code">
                </div>
            </div>
        </div>

        <p class="text-muted">Tunggu verifikasi DP oleh admin. Bawa QR ini saat datang ke salon.</p>

        <a href="index.php?page=customer" class="btn btn-outline-secondary">My Appointments</a>
        <a href="index.php?page=home" class="btn btn-merish">Back to Home</a>
    </div>
</div>

<?php include __DIR__ . '/../Layout/footer.php'; ?>