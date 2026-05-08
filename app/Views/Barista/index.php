<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barista Dashboard - Merish System</title>
</head>
<body>
    <div style="max-width: 1000px; margin: 20px auto; padding: 20px;">
        <h1>Barista Dashboard</h1>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green; padding: 10px; background: #e8f5e9;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <h2>Menu Barista</h2>
            <ul style="list-style: none; padding: 0;">
                <li><a href="<?php echo url('barista/history'); ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Riwayat Pesanan</a></li>
                <li><a href="<?php echo url('auth/logout'); ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin: 5px; text-decoration: none;">Logout</a></li>
            </ul>
        </div>

        <div style="margin-bottom: 20px;">
            <h2>Pesanan Pending & In Progress</h2>
            <?php if (count($orders) > 0): ?>
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f0f0;">
                            <th style="padding: 10px;">Order ID</th>
                            <th style="padding: 10px;">Menu</th>
                            <th style="padding: 10px;">Qty</th>
                            <th style="padding: 10px;">Meja</th>
                            <th style="padding: 10px;">Status</th>
                            <th style="padding: 10px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($order['menu_name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($order['table_number']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($order['status']); ?></td>
                                <td style="padding: 10px;">
                                    <?php if ($order['status'] === 'Pending'): ?>
                                        <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'In Progress')">Proses</button>
                                    <?php else: ?>
                                        <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'Done')">Selesai</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada pesanan yang pending atau sedang diproses</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateOrderStatus(orderId, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo url('barista/update-order'); ?>';
            
            const idField = document.createElement('input');
            idField.type = 'hidden';
            idField.name = 'order_id';
            idField.value = orderId;
            
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
