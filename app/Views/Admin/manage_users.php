<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin</title>
</head>
<body>
    <div style="max-width: 1000px; margin: 20px auto; padding: 20px;">
        <h1>Kelola Pengguna</h1>
        <a href="<?php echo url('admin'); ?>" style="padding: 10px 15px; background: #f0f0f0; text-decoration: none;">Kembali ke Dashboard</a>

        <table border="1" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Nama</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Role</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['id']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['status']); ?></td>
                        <td style="padding: 10px;">
                            <button style="padding: 5px 10px; margin-right: 5px;">Edit</button>
                            <button style="padding: 5px 10px;">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
