<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Menu - Merish System</title>
</head>
<body>
    <div style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h1>Pesan Menu Cafe</h1>

        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form method="POST" action="<?php echo url('customer/create-order'); ?>">
            <div style="margin-bottom: 15px;">
                <label for="menu_id">Menu:</label>
                <select id="menu_id" name="menu_id" required style="width: 100%; padding: 5px;">
                    <option value="">-- Pilih Menu --</option>
                    <?php foreach ($menus as $menu): ?>
                        <option value="<?php echo htmlspecialchars($menu['id']); ?>">
                            <?php echo htmlspecialchars($menu['name']); ?> - Rp <?php echo number_format($menu['price']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="quantity">Jumlah:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" required style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="table_number">Nomor Meja (Opsional):</label>
                <input type="text" id="table_number" name="table_number" style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="reservation_id">Terkait Appointment (Opsional):</label>
                <input type="text" id="reservation_id" name="reservation_id" placeholder="ID Reservation" style="width: 100%; padding: 5px;">
            </div>

            <button type="submit" style="padding: 10px 20px; cursor: pointer;">Pesan</button>
            <a href="<?php echo url('customer'); ?>" style="margin-left: 10px; padding: 10px 20px; background: #f0f0f0; text-decoration: none;">Kembali</a>
        </form>
    </div>
</body>
</html>
