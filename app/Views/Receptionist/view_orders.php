<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Pesanan - Receptionist</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 20px auto; padding: 20px;">
        <h1>Daftar Pesanan</h1>
        <a href="index.php?page=receptionist; ?>" style="padding: 10px 15px; background: #f0f0f0; text-decoration: none;">Kembali ke Dashboard</a>

        <table border="1" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th style="padding: 10px;">Order ID</th>
                    <th style="padding: 10px;">Menu</th>
                    <th style="padding: 10px;">Qty</th>
                    <th style="padding: 10px;">Harga</th>
                    <th style="padding: 10px;">Meja</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px;">Dibuat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['menu_name']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td style="padding: 10px;">Rp <?php echo number_format($order['price_per_item']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['table_number']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['status']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


