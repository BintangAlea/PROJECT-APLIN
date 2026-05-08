<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mendatang - Beautician</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 20px auto; padding: 20px;">
        <h1>Jadwal Mendatang</h1>
        <a href="<?php echo url('beautician'); ?>" style="padding: 10px 15px; background: #f0f0f0; text-decoration: none; margin-right: 10px;">Kembali ke Dashboard</a>

        <div style="margin: 20px 0;">
            <form method="GET" style="display: inline-block;">
                <label for="days">Tampilkan:</label>
                <select id="days" name="days">
                    <option value="7" <?php echo ($days == 7) ? 'selected' : ''; ?>>7 hari</option>
                    <option value="14" <?php echo ($days == 14) ? 'selected' : ''; ?>>2 minggu</option>
                    <option value="30" <?php echo ($days == 30) ? 'selected' : ''; ?>>1 bulan</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <?php if (count($schedule) > 0): ?>
            <table border="1" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
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
                    <?php foreach ($schedule as $item): ?>
                        <tr>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['reservation_date']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['reservation_time']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['customer_name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['service_name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="margin-top: 20px;">Tidak ada jadwal mendatang</p>
        <?php endif; ?>
    </div>
</body>
</html>
