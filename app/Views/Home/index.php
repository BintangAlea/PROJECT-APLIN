<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Merish System</title>
</head>
<body>
    <div style="max-width: 800px; margin: 50px auto; padding: 20px; text-align: center;">
        <h1>Merish - Unified Hybrid Business System</h1>
        <p style="font-size: 18px; margin: 30px 0;">Sistem Manajemen Terpadu untuk Salon & Cafe</p>

        <div style="margin: 50px 0;">
            <h2>Silakan Login</h2>
            <p>Pilih peran Anda untuk melanjutkan</p>
            <ul style="list-style: none; padding: 0;">
                <li><a href="<?php echo url('login'); ?>" style="display: inline-block; padding: 15px 30px; background: #f0f0f0; margin: 10px; text-decoration: none; font-size: 16px;">Login</a></li>
                <li><a href="<?php echo url('register'); ?>" style="display: inline-block; padding: 15px 30px; background: #f0f0f0; margin: 10px; text-decoration: none; font-size: 16px;">Register</a></li>
            </ul>
        </div>

        <div style="margin-top: 50px; padding: 20px; background: #f5f5f5; border: 1px solid #ddd;">
            <h3>Fitur Sistem</h3>
            <ul style="text-align: left; display: inline-block;">
                <li>Login & Register dengan berbagai role (Admin, Receptionist, Beautician, Barista, Customer)</li>
                <li>Manajemen Appointment/Reservasi untuk layanan salon</li>
                <li>Pemesanan menu cafe yang terintegrasi</li>
                <li>Dashboard untuk setiap role dengan fitur spesifik</li>
                <li>Unified billing untuk transaksi salon dan cafe</li>
            </ul>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #ffe8e8; border: 1px solid #ff8888;">
            <p><strong>Testing Roles:</strong></p>
            <p>Register dulu dengan role yang ingin dicoba, atau login dengan akun yang sudah ada.</p>
        </div>
    </div>
</body>
</html>
