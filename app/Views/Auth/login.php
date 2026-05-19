<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Merish</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: white; padding: 40px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { text-align: center; margin-bottom: 10px; font-size: 28px; color: #333; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: bold; font-size: 14px; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        input:focus { outline: none; border-color: #666; }
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
        <p class="subtitle">Masuk ke akun anda</p>
        <?php if (isset($_SESSION['error']) && $_SESSION['error']): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <form method="POST" action="index.php?page=login&action=login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus placeholder="Masukkan email anda" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit">Masuk</button>
        </form>
        <div class="divider">atau</div>
        <div class="footer-link">
            <span style="color: #666; font-size: 14px;">Belum punya akun?</span>
            <a href="index.php?page=register">Buat akun baru</a>
            <a href="index.php">Kembali ke beranda</a>
        </div>
    </div>
</body>
</html>
