<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard - Merish System</title>
</head>
<body>
    <div style="max-width: 1000px; margin: 20px auto; padding: 20px;">
        <h1>Receptionist Dashboard</h1>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green; padding: 10px; background: #e8f5e9;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <h2>Menu Receptionist</h2>
            <ul style="list-style: none; padding: 0;">
                <li><a href="index.php?page=receptionist&action=scheduleBooking; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Jadwal Booking</a></li>
                <li><a href="index.php?page=receptionist&action=checkIn; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Check-In Pelanggan</a></li>
                <li><a href="index.php?page=receptionist&action=viewReservations; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Lihat Reservasi</a></li>
                <li><a href="index.php?page=receptionist&action=viewOrders; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Lihat Pesanan</a></li>
                <li><a href="index.php?page=login&action=logout; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Logout</a></li>
            </ul>
        </div>

        <div>
            <h2>Reservasi Hari Ini</h2>
            <?php if (count($reservations) > 0): ?>
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f0f0;">
                            <th style="padding: 10px;">Res ID</th>
                            <th style="padding: 10px;">Pelanggan</th>
                            <th style="padding: 10px;">Service</th>
                            <th style="padding: 10px;">Jam</th>
                            <th style="padding: 10px;">Beautician</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['res_id']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['customer_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['service_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['reservation_time']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['beautician_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada reservasi untuk hari ini</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


