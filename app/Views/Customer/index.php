<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer - Merish System</title>
</head>
<body>
    <div style="max-width: 800px; margin: 20px auto; padding: 20px;">
        <h1>Customer Dashboard</h1>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green; padding: 10px; background: #e8f5e9;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <div style="margin-bottom: 30px;">
            <h2>Menu Utama</h2>
            <ul style="list-style: none; padding: 0;">
                <li><a href="index.php?page=customer&action=appointment; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Buat Appointment</a></li>
                <li><a href="index.php?page=customer&action=orderMenu; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Pesan Menu</a></li>
                <li><a href="index.php?page=login&action=logout; ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Logout</a></li>
            </ul>
        </div>

        <div style="margin-bottom: 20px;">
            <h2>Appointment Saya</h2>
            <?php if (count($reservations) > 0): ?>
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f0f0;">
                            <th style="padding: 10px;">Tanggal</th>
                            <th style="padding: 10px;">Jam</th>
                            <th style="padding: 10px;">Service</th>
                            <th style="padding: 10px;">Beautician</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['reservation_date']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['reservation_time']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['service_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['beautician_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($res['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Belum ada appointment</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


