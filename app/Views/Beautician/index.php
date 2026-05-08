<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beautician Dashboard - Merish System</title>
</head>
<body>
    <div style="max-width: 1000px; margin: 20px auto; padding: 20px;">
        <h1>Beautician Dashboard</h1>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green; padding: 10px; background: #e8f5e9;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <h2>Menu Beautician</h2>
            <ul style="list-style: none; padding: 0;">
                <li><a href="<?php echo url('beautician/today'); ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Jadwal Hari Ini</a></li>
                <li><a href="<?php echo url('beautician/upcoming'); ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Jadwal Mendatang</a></li>
                <li><a href="<?php echo url('auth/logout'); ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Logout</a></li>
            </ul>
        </div>

        <div style="margin-bottom: 30px;">
            <h2>Booking Hari Ini</h2>
            <?php if (count($todaySchedule) > 0): ?>
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f0f0;">
                            <th style="padding: 10px;">Jam</th>
                            <th style="padding: 10px;">Pelanggan</th>
                            <th style="padding: 10px;">Service</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todaySchedule as $schedule): ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['reservation_time']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['customer_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['service_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada booking untuk hari ini</p>
            <?php endif; ?>
        </div>

        <div>
            <h2>Jadwal Mendatang (7 hari)</h2>
            <?php if (count($upcomingSchedule) > 0): ?>
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f0f0;">
                            <th style="padding: 10px;">Tanggal</th>
                            <th style="padding: 10px;">Jam</th>
                            <th style="padding: 10px;">Pelanggan</th>
                            <th style="padding: 10px;">Service</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingSchedule as $schedule): ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['reservation_date']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['reservation_time']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['customer_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['service_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($schedule['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada jadwal mendatang</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
