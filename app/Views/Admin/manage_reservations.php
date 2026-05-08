<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Reservasi - Admin</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 20px auto; padding: 20px;">
        <h1>Kelola Reservasi</h1>
        <a href="<?php echo url('admin'); ?>" style="padding: 10px 15px; background: #f0f0f0; text-decoration: none;">Kembali ke Dashboard</a>

        <table border="1" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th style="padding: 10px;">Res ID</th>
                    <th style="padding: 10px;">Pelanggan</th>
                    <th style="padding: 10px;">Service</th>
                    <th style="padding: 10px;">Tanggal</th>
                    <th style="padding: 10px;">Jam</th>
                    <th style="padding: 10px;">Beautician</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($res['res_id']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($res['customer_name']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($res['service_name']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($res['reservation_date']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($res['reservation_time']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($res['beautician_name'] ?? '-'); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($res['status']); ?></td>
                        <td style="padding: 10px;">
                            <button style="padding: 5px 10px; margin-right: 5px;">Edit</button>
                            <button style="padding: 5px 10px;">Detail</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
