<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Reservasi - Receptionist</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 20px auto; padding: 20px;">
        <h1>Reservasi Tanggal: <?php echo htmlspecialchars($date); ?></h1>
        <a href="index.php?page=receptionist; ?>" style="padding: 10px 15px; background: #f0f0f0; text-decoration: none;">Kembali ke Dashboard</a>

        <div style="margin: 20px 0;">
            <form method="GET" style="display: inline-block;">
                <label for="date">Pilih Tanggal:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <table border="1" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th style="padding: 10px;">Res ID</th>
                    <th style="padding: 10px;">Pelanggan</th>
                    <th style="padding: 10px;">Service</th>
                    <th style="padding: 10px;">Jam</th>
                    <th style="padding: 10px;">Beautician</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($reservations) > 0): ?>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($res['res_id']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($res['customer_name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($res['service_name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($res['reservation_time']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($res['beautician_name'] ?? '-'); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($res['status']); ?></td>
                            <td style="padding: 10px;">
                                <button onclick="updateStatus(<?php echo $res['id']; ?>, 'Stage2')">Stage 2</button>
                                <button onclick="updateStatus(<?php echo $res['id']; ?>, 'Stage3')">Stage 3</button>
                                <button onclick="updateStatus(<?php echo $res['id']; ?>, 'Completed')">Selesai</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="padding: 10px; text-align: center;">Tidak ada reservasi untuk tanggal ini</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateStatus(resId, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo url('receptionist/update-reservation'); ?>';
            
            const idField = document.createElement('input');
            idField.type = 'hidden';
            idField.name = 'reservation_id';
            idField.value = resId;
            
            const statusField = document.createElement('input');
            statusField.type = 'hidden';
            statusField.name = 'status';
            statusField.value = newStatus;
            
            form.appendChild(idField);
            form.appendChild(statusField);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>


