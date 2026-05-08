<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Models\UsersModel;

class AuthController
{
    private UsersModel $usersModel;
    private \PDO $db;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->db = Database::getInstance()->getConnection();
    }

    public function loginForm(): void
    {
        require_once __DIR__ . '/../Views/Auth/login.php';
    }

    public function registerForm(): void
    {
        require_once __DIR__ . '/../Views/Auth/register.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Email dan password harus diisi';
        }

        if (!$error) {
            $user = $this->usersModel->login($email, $password);
            if ($user) {
                Session::set('user_id', $user['id']);
                Session::set('email', $user['email']);
                Session::set('role', $user['role']);
                Session::set('full_name', $user['full_name']);

                // Redirect berdasarkan role
                $redirectUrl = match ($user['role']) {
                    'admin' => '/SIB/PROJECT-APLIN/router.php?route=admin',
                    'receptionist' => '/SIB/PROJECT-APLIN/router.php?route=receptionist',
                    'barista' => '/SIB/PROJECT-APLIN/router.php?route=barista',
                    'beautician' => '/SIB/PROJECT-APLIN/router.php?route=beautician',
                    'customer' => '/SIB/PROJECT-APLIN/router.php?route=customer',
                    default => '/SIB/PROJECT-APLIN/router.php',
                };

                header("Location: $redirectUrl");
                exit;
            } else {
                $error = 'Email atau password salah';
            }
        }

        // Render login form dengan error
        require_once __DIR__ . '/../Views/Auth/login.php';
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=register');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $role = $_POST['role'] ?? 'customer';
        $error = '';

        if (empty($email) || empty($password) || empty($fullName)) {
            $error = 'Email, password, dan nama lengkap harus diisi';
        }

        if (!$error && $password !== $confirmPassword) {
            $error = 'Password tidak sesuai';
        }

        if (!$error && strlen($password) < 6) {
            $error = 'Password minimal 6 karakter';
        }

        if (!$error) {
            $result = $this->usersModel->register($email, $password, $fullName, $phone, $role);
            if ($result) {
                // If beautician role, create beautician profile
                if ($role === 'beautician') {
                    $user = $this->usersModel->findByEmail($email);
                    if ($user) {
                        $stmt = $this->db->prepare('INSERT INTO beauticians (user_id, specialization, status) VALUES (:user_id, :specialization, :status)');
                        $stmt->execute([
                            ':user_id' => $user['id'],
                            ':specialization' => 'General',
                            ':status' => 'available'
                        ]);
                    }
                }
                
                header('Location: /SIB/PROJECT-APLIN/router.php?route=login&success=Registration%20successful.%20Please%20login.');
                exit;
            } else {
                $error = 'Email sudah terdaftar atau terjadi kesalahan';
            }
        }

        // Render register form dengan error
        require_once __DIR__ . '/../Views/Auth/register.php';
    }

    public function logout(): void
    {
        Session::logout();
        header('Location: /SIB/PROJECT-APLIN/router.php?route=login&success=Logout%20successful');
        exit;
    }
}
