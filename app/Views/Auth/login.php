<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Merish System</title>
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h1>Login</h1>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <?php if (isset($error) && $error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="<?php echo url('auth/login'); ?>">
            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 5px;">
            </div>

            <button type="submit" style="padding: 10px 20px; cursor: pointer;">Login</button>
        </form>

        <p style="margin-top: 20px;">
            Belum punya akun? <a href="<?php echo url('register'); ?>">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
