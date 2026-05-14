<?php

namespace App\Controllers;

use App\Models\UsersModel;

class AuthController
{
    private UsersModel $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }

    public function index()
    {
        require __DIR__ . '/../Views/Auth/login.php';
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = '';

        if (!$email || !$password) {
            $_SESSION['error'] = 'Email dan password harus diisi';
            header('Location: index.php?page=login');
            exit;
        }

        $user = $this->usersModel->login($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect berdasarkan role
            $redirectPage = match ($user['role']) {
                'admin' => 'admin',
                'receptionist' => 'receptionist',
                'barista' => 'barista',
                'beautician' => 'beautician',
                'customer' => 'customer',
                default => 'home',
            };

            header('Location: index.php?page=' . $redirectPage);
            exit;
        } else {
            $_SESSION['error'] = 'Email atau password salah';
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function register()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $role = $_POST['role'] ?? 'customer';
        $error = '';

        if (!$email || !$password || !$fullName) {
            $_SESSION['error'] = 'Email, password, dan nama lengkap harus diisi';
            header('Location: index.php?page=register');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Password tidak sesuai';
            header('Location: index.php?page=register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password minimal 6 karakter';
            header('Location: index.php?page=register');
            exit;
        }

        $result = $this->usersModel->register($email, $password, $fullName, $phone, $role);
        if (!$result) {
            $_SESSION['error'] = 'Email sudah terdaftar atau terjadi kesalahan';
            header('Location: index.php?page=register');
            exit;
        }

        // If beautician role, create beautician profile
        if ($role === 'beautician') {
            $user = $this->usersModel->findByEmail($email);
            if ($user) {
                $stmt = \App\Core\Database::getConnection()->prepare('INSERT INTO beauticians (user_id, specialization, status) VALUES (:user_id, :specialization, :status)');
                $stmt->execute([
                    ':user_id' => $user['id'],
                    ':specialization' => 'General',
                    ':status' => 'available'
                ]);
            }
        }

        $_SESSION['success'] = 'Registrasi berhasil. Silakan login.';
        header('Location: index.php?page=login');
        exit;
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_login']);
        unset($_SESSION['role']);
        unset($_SESSION['full_name']);
        
        $_SESSION['success'] = 'Logout berhasil';
        header('Location: index.php?page=login');
        exit;
    }
}
