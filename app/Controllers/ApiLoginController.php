<?php

namespace App\Controllers;

use App\Core\ApiResponse;
use App\Models\UsersModel;

/**
 * API Login Controller
 * Handle authentication via API endpoints
 */
class ApiLoginController
{
    private UsersModel $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        header('Content-Type: application/json');
    }

    /**
     * POST /api/login
     * Login dengan email dan password
     * 
     * Request body:
     * {
     *   "email": "user@example.com",
     *   "password": "password123"
     * }
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validasi
        $errors = [];
        if (empty($input['email'])) $errors['email'] = 'Email required';
        if (empty($input['password'])) $errors['password'] = 'Password required';

        if (!empty($errors)) {
            echo ApiResponse::validationError($errors);
            return;
        }

        // Login
        $user = $this->usersModel->login($input['email'], $input['password']);
        if (!$user) {
            echo ApiResponse::error('Invalid email or password', 401);
            return;
        }

        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_login'] = $user['email'];
        $_SESSION['role'] = $user['ROLE'];
        $_SESSION['full_name'] = $user['NAME'];

        // Generate token (simple JWT-like)
        $token = base64_encode(json_encode([
            'user_id' => $user['user_id'],
            'role' => $user['ROLE'],
            'iat' => time()
        ]));

        echo ApiResponse::success([
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'name' => $user['NAME'],
            'role' => $user['ROLE'],
            'token' => $token,
            'loyalty_stage' => $user['loyalty_stage'],
            'total_spent' => $user['total_spent'],
            'reward_points' => $user['reward_points']
        ], 'Login successful', 200);
    }

    /**
     * GET /api/logout
     * Logout user
     */
    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        session_destroy();
        echo ApiResponse::success(null, 'Logout successful', 200);
    }

    /**
     * GET /api/me
     * Get current user info
     */
    public function me()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            echo ApiResponse::unauthorized('Not authenticated');
            return;
        }

        $user = $this->usersModel->findById($_SESSION['user_id']);
        echo ApiResponse::success([
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'name' => $user['NAME'],
            'role' => $user['ROLE'],
            'loyalty_stage' => $user['loyalty_stage'],
            'total_spent' => $user['total_spent'],
            'reward_points' => $user['reward_points']
        ], 'Current user info', 200);
    }

    /**
     * POST /api/register
     * Register new user
     * 
     * Request body:
     * {
     *   "full_name": "John Doe",
     *   "email": "john@example.com",
     *   "password": "password123",
     *   "role": "customer"
     * }
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validasi
        $errors = [];
        if (empty($input['full_name'])) $errors['full_name'] = 'Full name required';
        if (empty($input['email'])) $errors['email'] = 'Email required';
        if (empty($input['password'])) $errors['password'] = 'Password required';
        if (empty($input['role'])) $input['role'] = 'customer';

        if (strlen($input['password'] ?? '') < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if (!empty($errors)) {
            echo ApiResponse::validationError($errors);
            return;
        }

        // Register
        $result = $this->usersModel->register(
            $input['email'],
            $input['password'],
            $input['full_name'],
            $input['phone'] ?? '',
            $input['role']
        );

        if (!$result) {
            echo ApiResponse::error('Email already registered or error occurred', 400);
            return;
        }

        echo ApiResponse::success([
            'email' => $input['email'],
            'name' => $input['full_name'],
            'role' => $input['role']
        ], 'Registration successful', 201);
    }

    /**
     * GET /api/seed-test-data
     * Seed test data dengan 8 akun berbeda role
     * (ONLY untuk testing/development)
     */
    public function seedTestData()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        // Array of test accounts
        $testAccounts = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => 'admin123',
                'role' => 'Admin'
            ],
            [
                'name' => 'Receptionist Test',
                'email' => 'receptionist@test.com',
                'password' => 'receptionist123',
                'role' => 'Receptionist'
            ],
            [
                'name' => 'Barista Test',
                'email' => 'barista@test.com',
                'password' => 'barista123',
                'role' => 'Barista'
            ],
            [
                'name' => 'Beautician Test 1',
                'email' => 'beautician1@test.com',
                'password' => 'beautician123',
                'role' => 'Beautician'
            ],
            [
                'name' => 'Beautician Test 2',
                'email' => 'beautician2@test.com',
                'password' => 'beautician123',
                'role' => 'Beautician'
            ],
            [
                'name' => 'Customer Test 1',
                'email' => 'customer1@test.com',
                'password' => 'customer123',
                'role' => 'Customer'
            ],
            [
                'name' => 'Customer Test 2',
                'email' => 'customer2@test.com',
                'password' => 'customer123',
                'role' => 'Customer'
            ],
            [
                'name' => 'Customer Test 3',
                'email' => 'customer3@test.com',
                'password' => 'customer123',
                'role' => 'Customer'
            ]
        ];

        $created = [];
        $failed = [];

        foreach ($testAccounts as $account) {
            // Check if email already exists
            if ($this->usersModel->findByEmail($account['email'])) {
                $failed[] = [
                    'email' => $account['email'],
                    'reason' => 'Email already exists'
                ];
                continue;
            }

            // Register
            $result = $this->usersModel->register(
                $account['email'],
                $account['password'],
                $account['name'],
                '',
                $account['role']
            );

            if ($result) {
                $created[] = [
                    'email' => $account['email'],
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => $account['password']
                ];

                // If beautician, create staff profile
                if ($account['role'] === 'Beautician') {
                    $user = $this->usersModel->findByEmail($account['email']);
                    if ($user) {
                        $db = \App\Core\Database::getConnection();
                        $stmt = $db->prepare(
                            'INSERT INTO staff_profiles (user_id, specialization, work_status) 
                             VALUES (:user_id, :specialization, :status)'
                        );
                        $stmt->execute([
                            ':user_id' => $user['user_id'],
                            ':specialization' => 'Hair Stylist',
                            ':status' => 'Online'
                        ]);
                    }
                }
            } else {
                $failed[] = [
                    'email' => $account['email'],
                    'reason' => 'Registration failed'
                ];
            }
        }

        echo ApiResponse::success([
            'created_count' => count($created),
            'failed_count' => count($failed),
            'created_accounts' => $created,
            'failed_accounts' => $failed
        ], 'Test data seeded', 200);
    }
}
