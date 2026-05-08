<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In - Receptionist</title>
</head>
<body>
    <div style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h1>Check-In Pelanggan</h1>
        <a href="<?php echo url('receptionist'); ?>" style="display: inline-block; padding: 10px 15px; background: #f0f0f0; margin-bottom: 20px; text-decoration: none;">Kembali</a>

        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label for="res_id">Cari Reservation ID:</label>
                <input type="text" id="res_id" name="res_id" required style="width: 100%; padding: 5px;">
                <small>Masukkan Reservation ID untuk check-in</small>
            </div>

            <button type="submit" style="padding: 10px 20px; cursor: pointer;">Check-In</button>
        </form>
    </div>
</body>
</html>
