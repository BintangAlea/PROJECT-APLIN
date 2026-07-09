<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Hari Ini - Beautician</title>
</head>
<body>
    <?php $schedule = $schedule ?? []; ?>
    <div style="max-width: 1000px; margin: 20px auto; padding: 20px;">
        <h1>Jadwal Hari Ini</h1>
        <a href="index.php?page=beautician" style="padding: 10px 15px; background: #f0f0f0; text-decoration: none;">Kembali ke Dashboard</a>

        <?php if (count($schedule) > 0): ?>
            <table border="1" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f0f0f0;">
                        <th style="padding: 10px;">Jam</th>
                        <th style="padding: 10px;">Pelanggan</th>
                        <th style="padding: 10px;">Service</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedule as $item): ?>
                        <tr>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['reservation_time']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['customer_name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['service_name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($item['status']); ?></td>
                            <td style="padding: 10px;">
                                <button onclick="updateStatus(<?php echo $item['id']; ?>, 'Stage2')">Stage 2</button>
                                <button onclick="updateStatus(<?php echo $item['id']; ?>, 'Stage3')">Stage 3</button>
                                <button onclick="updateStatus(<?php echo $item['id']; ?>, 'Completed')">Selesai</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="margin-top: 20px;">Tidak ada booking untuk hari ini</p>
        <?php endif; ?>
    </div>

    <script>
        function updateStatus(resId, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?page=beautician&action=updateReservationStatus';
            
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


