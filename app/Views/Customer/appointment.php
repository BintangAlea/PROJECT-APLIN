<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Appointment - Merish System</title>
</head>
<body>
    <div style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h1>Buat Appointment</h1>

        <?php if (isset($error) && $error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error']) && $_SESSION['error']): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?page=customer&action=bookAppointment">
            <div style="margin-bottom: 15px;">
                <label for="service_id">Service/Treatment:</label>
                <select id="service_id" name="service_id" required style="width: 100%; padding: 5px;">
                    <option value="">-- Pilih Service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo htmlspecialchars($service['service_id']); ?>">
                            <?php echo htmlspecialchars($service['service_name']); ?> (Rp <?php echo number_format($service['base_tariff']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="reservation_date">Tanggal:</label>
                <input type="date" id="reservation_date" name="reservation_date" required style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="reservation_time">Jam:</label>
                <input type="time" id="reservation_time" name="reservation_time" required style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="beautician_id">Pilih Beautician (Opsional):</label>
                <select id="beautician_id" name="beautician_id" style="width: 100%; padding: 5px;">
                    <option value="">-- Sistem akan memilih --</option>
                    <?php foreach ($beauticians as $beautician): ?>
                        <option value="<?php echo htmlspecialchars($beautician['profile_id']); ?>">
                            <?php echo htmlspecialchars($beautician['NAME']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="notes">Catatan:</label>
                <textarea id="notes" name="notes" style="width: 100%; padding: 5px; height: 80px;"></textarea>
            </div>

            <button type="submit" style="padding: 10px 20px; cursor: pointer;">Buat Appointment</button>
            <a href="index.php?page=customer" style="margin-left: 10px; padding: 10px 20px; background: #f0f0f0; text-decoration: none;">Kembali</a>
        </form>
    </div>
</body>
</html>


