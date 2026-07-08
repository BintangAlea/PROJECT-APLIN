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
        $context = $_GET['context'] ?? '';
        
        if ($page === 'register') {
            require __DIR__ . '/../Views/Auth/register.php';
        } elseif ($page === 'login' && $context === 'register') {
            require __DIR__ . '/../Views/Auth/register_login.php';
        } elseif ($page === 'login' && $context === 'checkout') {
            require __DIR__ . '/../Views/Auth/checkout_login.php';
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
        $context = $_POST['context'] ?? $_GET['context'] ?? '';

        $loginRedirect = 'index.php?page=login';
        if ($context !== '') {
            $loginRedirect .= '&context=' . urlencode($context);
        }

        if (!$email || !$password) {
            $_SESSION['error'] = 'Email dan password harus diisi';
            header('Location: ' . $loginRedirect);
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

            $checkoutRedirect = $_SESSION['post_login_redirect'] ?? null;
            if ($checkoutRedirect) {
                unset($_SESSION['post_login_redirect']);
                header('Location: ' . $checkoutRedirect);
                exit;
            }

            if ($context === 'checkout') {
                header('Location: index.php?page=booking&step=5');
                exit;
            }

            $role = strtolower(trim((string) $user['ROLE']));
            if ($role === 'admin') {
                header('Location: index.php?page=admin');
            } elseif ($role === 'receptionist') {
                header('Location: index.php?page=receptionist');
            } elseif ($role === 'barista') {
                header('Location: index.php?page=barista');
            } elseif ($role === 'beautician') {
                header('Location: index.php?page=beautician');
            } else {
                header('Location: index.php?page=home');
            }
            exit;
        } else {
            $_SESSION['error'] = 'Email atau password salah';
            header('Location: ' . $loginRedirect);
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
        $context = $_POST['context'] ?? $_GET['context'] ?? '';

        $registerRedirect = 'index.php?page=register';
        if ($context !== '') {
            $registerRedirect .= '&context=' . urlencode($context);
        }

        if (!$email || !$password || !$fullName) {
            $_SESSION['error'] = 'Email, password, dan nama lengkap harus diisi';
            header('Location: ' . $registerRedirect);
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Password tidak sesuai';
            header('Location: ' . $registerRedirect);
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password minimal 6 karakter';
            header('Location: ' . $registerRedirect);
            exit;
        }

        $result = $this->usersModel->register($email, $password, $fullName, '', $role);
        if (!$result) {
            $_SESSION['error'] = 'Email sudah terdaftar atau terjadi kesalahan';
            header('Location: ' . $registerRedirect);
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

        $_SESSION['success'] = 'Registrasi berhasil! Silakan login dengan akun Anda.';
        
        if ($context === 'checkout') {
            header('Location: index.php?page=login&context=checkout');
        } else {
            header('Location: index.php?page=login');
        }
        exit;
    }
}
