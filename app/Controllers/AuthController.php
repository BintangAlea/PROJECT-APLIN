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
        $page = $_GET['page'] ?? 'login';
        
        if ($page === 'register') {
            require __DIR__ . '/../Views/Auth/register.php';
        } else {
            require __DIR__ . '/../Views/Auth/login.php';
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $_SESSION['error'] = 'Email dan password harus diisi';
            header('Location: index.php?page=login');
            exit;
        }

        $user = $this->usersModel->login($email, $password);
        
        // Debug logging
        error_log('Login attempt - Email: ' . $email);
        error_log('Login result: ' . ($user ? 'Success' : 'Failed'));
        error_log('User data: ' . print_r($user, true));
        
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_login'] = $user['email'];
            $_SESSION['role'] = $user['ROLE'];
            $_SESSION['full_name'] = $user['NAME'];

            error_log('Session set - User ID: ' . $user['user_id'] . ', Role: ' . $user['ROLE']);

            // Redirect berdasarkan role
            $redirectPage = match ($user['ROLE']) {
                'Admin' => 'admin',
                'Receptionist' => 'receptionist',
                'Barista' => 'barista',
                'Beautician' => 'beautician',
                'Customer' => 'customer',
                default => 'home',
            };

            error_log('Redirecting to: ' . $redirectPage);
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
        $role = $_POST['role'] ?? 'customer';

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

        $result = $this->usersModel->register($email, $password, $fullName, '', $role);
        if (!$result) {
            $_SESSION['error'] = 'Email sudah terdaftar atau terjadi kesalahan';
            header('Location: index.php?page=register');
            exit;
        }

        // Jika role beautician, create staff profile
        if ($role === 'beautician') {
            $user = $this->usersModel->findByEmail($email);
            if ($user) {
                $db = \App\Core\Database::getConnection();
                $stmt = $db->prepare('INSERT INTO staff_profiles (user_id, specialization, work_status) VALUES (:user_id, :specialization, :status)');
                $stmt->execute([
                    ':user_id' => $user['user_id'],
                    ':specialization' => 'Hair Stylist',
                    ':status' => 'Offline'
                ]);
            }
        }

        $_SESSION['success'] = 'Registrasi berhasil! Silahkan login dengan akun anda';
        header('Location: index.php?page=login');
        exit;
    }
}
