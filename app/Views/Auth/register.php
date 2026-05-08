<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Merish System</title>
</head>
<body>
    <div style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h1>Register</h1>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <?php if (isset($error) && $error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="<?php echo url('auth/register'); ?>">
            <div style="margin-bottom: 15px;">
                <label for="full_name">Nama Lengkap:</label>
                <input type="text" id="full_name" name="full_name" required style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="phone">Nomor Telepon:</label>
                <input type="tel" id="phone" name="phone" style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="role">Role:</label>
                <select id="role" name="role" required style="width: 100%; padding: 5px;">
                    <option value="customer">Customer</option>
                    <option value="beautician">Beautician</option>
                    <option value="barista">Barista</option>
                    <option value="receptionist">Receptionist</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 5px;">
            </div>

            <button type="submit" style="padding: 10px 20px; cursor: pointer;">Register</button>
        </form>

        <p style="margin-top: 20px;">
            Sudah punya akun? <a href="<?php echo url('login'); ?>">Login di sini</a>
        </p>
    </div>
</body>
</html>
