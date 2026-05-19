<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Merish</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: white; padding: 40px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h1 { text-align: center; margin-bottom: 10px; font-size: 28px; color: #333; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: bold; font-size: 14px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: Arial, sans-serif; }
        input:focus, select:focus { outline: none; border-color: #666; }
        button { width: 100%; padding: 12px; background-color: #333; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #555; }
        .divider { text-align: center; margin: 30px 0; color: #999; font-size: 12px; }
        .footer-link { text-align: center; margin-top: 20px; }
        .footer-link a { color: #333; text-decoration: none; font-size: 14px; display: block; margin: 8px 0; }
        .footer-link a:hover { text-decoration: underline; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; font-size: 14px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Merish</h1>
        <p class="subtitle">Buat akun baru</p>
        <?php if (isset($_SESSION['error']) && $_SESSION['error']): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <form method="POST" action="index.php?page=register&action=register">
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" required placeholder="Masukkan nama lengkap anda" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Masukkan email anda" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="customer" selected>Customer</option>
                    <option value="barista">Barista</option>
                    <option value="beautician">Beautician</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Masukkan ulang password">
            </div>
            <button type="submit">Daftar</button>
        </form>
        <div class="divider">atau</div>
        <div class="footer-link">
            <span style="color: #666; font-size: 14px;">Sudah punya akun?</span>
            <a href="index.php?page=login">Masuk di sini</a>
            <a href="index.php">Kembali ke beranda</a>
        </div>
    </div>
</body>
</html>
