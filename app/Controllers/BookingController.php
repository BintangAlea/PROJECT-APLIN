<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Models\ServicesModel;
use App\Models\UsersModel;
use App\Models\ReservationsModel;
use PDO;

/**
 * Public Booking Controller
 * Handle multi-step booking flow (6 steps)
 * NO LOGIN REQUIRED - accessible to all visitors
 */
class BookingController
{
    private PDO $db;
    private Session $session;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->session = new Session();
    }

    /**
     * Step 0: Landing page - select category/service
     */
    public function index()
    {
        // Get all active services
        $servicesModel = new ServicesModel();
        $services = $servicesModel->findAll();

        return [
            'view' => 'Booking/index',
            'data' => [
                'services' => $services,
                'step' => 0,
                'title' => 'Pilih Layanan Kecantikan'
            ]
        ];
    }

    /**
     * Step 1: Category/Service selection
     * POST /booking/step1
     */
    public function step1()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $servicesModel = new ServicesModel();
            $services = $servicesModel->findAll();

            return [
                'view' => 'Booking/step1',
                'data' => [
                    'services' => $services,
                    'step' => 1,
                    'title' => 'Pilih Layanan'
                ]
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serviceId = $_POST['service_id'] ?? null;

            if (!$serviceId) {
                $_SESSION['booking_error'] = 'Pilih layanan terlebih dahulu';
                header('Location: /index.php?page=booking&step=1');
                exit;
            }

            // Store in session
            $_SESSION['booking'] = $_SESSION['booking'] ?? [];
            $_SESSION['booking']['service_id'] = (int)$serviceId;

            header('Location: /index.php?page=booking&step=2');
            exit;
        }
    }

    /**
     * Step 2: Bundle & Add-ons selection
     */
    public function step2()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $bundleModel = new \App\Models\ServiceBundleModel();
            $addonModel = new \App\Models\BookingAddonModel();

            $bundles = $bundleModel->getAllActiveBundles();
            $addons = $addonModel->getAddonsGroupedByType();

            // Enrich bundles with pricing
            foreach ($bundles as &$bundle) {
                $bundle = $bundleModel->getBundleWithPrice($bundle['bundle_id']);
            }

            return [
                'view' => 'Booking/step2',
                'data' => [
                    'bundles' => $bundles,
                    'addons' => $addons,
                    'step' => 2,
                    'title' => 'Pilih Paket & Tambahan',
                    'booking' => $_SESSION['booking'] ?? []
                ]
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['booking'] = $_SESSION['booking'] ?? [];
            $_SESSION['booking']['bundle_id'] = $_POST['bundle_id'] ?? null;
            $_SESSION['booking']['addon_ids'] = $_POST['addon_ids'] ?? [];

            header('Location: /index.php?page=booking&step=3');
            exit;
        }
    }

    /**
     * Step 3: Date & Time selection
     */
    public function step3()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return [
                'view' => 'Booking/step3',
                'data' => [
                    'step' => 3,
                    'title' => 'Pilih Tanggal & Waktu',
                    'booking' => $_SESSION['booking'] ?? [],
                    'min_date' => date('Y-m-d', strtotime('+1 day')),
                    'max_date' => date('Y-m-d', strtotime('+30 days'))
                ]
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['reservation_date'] ?? null;
            $time = $_POST['reservation_time'] ?? null;

            if (!$date || !$time) {
                $_SESSION['booking_error'] = 'Pilih tanggal dan waktu';
                header('Location: /index.php?page=booking&step=3');
                exit;
            }

            $_SESSION['booking'] = $_SESSION['booking'] ?? [];
            $_SESSION['booking']['reservation_date'] = $date;
            $_SESSION['booking']['reservation_time'] = $time;

            header('Location: /index.php?page=booking&step=4');
            exit;
        }
    }

    /**
     * Step 4: Beautician selection & Login prompt
     */
    public function step4()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get online beauticians
            $stmt = $this->db->prepare(
                'SELECT sp.profile_id, u.user_id, u.NAME as name, u.email,
                        sp.specialization, sp.photo_url, sp.rating, sp.bio
                 FROM staff_profiles sp
                 JOIN users u ON sp.user_id = u.user_id
                 WHERE sp.work_status = :status AND u.role = :role
                 ORDER BY sp.rating DESC'
            );
            $stmt->execute([
                ':status' => 'Online',
                ':role' => 'Beautician'
            ]);
            $beauticians = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'view' => 'Booking/step4',
                'data' => [
                    'beauticians' => $beauticians,
                    'step' => 4,
                    'title' => 'Pilih Beautician',
                    'booking' => $_SESSION['booking'] ?? [],
                    'is_logged_in' => $this->session->isLoggedIn()
                ]
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $beauticianId = $_POST['beautician_id'] ?? null;

            $_SESSION['booking'] = $_SESSION['booking'] ?? [];
            $_SESSION['booking']['beautician_id'] = $beauticianId;

            header('Location: /index.php?page=booking&step=5');
            exit;
        }
    }

    /**
     * Step 4.1: Login/Register (if not logged in)
     */
    public function step4Auth()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return [
                'view' => 'Booking/step4-1-login',
                'data' => [
                    'step' => '4.1',
                    'title' => 'Login / Daftar',
                    'booking' => $_SESSION['booking'] ?? []
                ]
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'login';

            if ($action === 'login') {
                $email = $_POST['email'] ?? null;
                $password = $_POST['password'] ?? null;

                $usersModel = new UsersModel();
                $user = $usersModel->findByEmail($email);

                if (!$user || !password_verify($password, $user['password'])) {
                    $_SESSION['booking_error'] = 'Email atau password salah';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                // Login success
                $this->session->createSession($user['user_id'], $user['role'], $user['NAME']);
                header('Location: /index.php?page=booking&step=5');
                exit;
            } elseif ($action === 'register') {
                // Handle registration
                $name = $_POST['name'] ?? null;
                $email = $_POST['email'] ?? null;
                $phone = $_POST['phone'] ?? null;
                $password = $_POST['password'] ?? null;

                if (!$name || !$email || !$password) {
                    $_SESSION['booking_error'] = 'Lengkapi semua data';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                $usersModel = new UsersModel();
                
                // Check if email exists
                if ($usersModel->findByEmail($email)) {
                    $_SESSION['booking_error'] = 'Email sudah terdaftar';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                // Register new user
                $userId = $usersModel->create([
                    'NAME' => $name,
                    'user_login' => $email,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'role' => 'Customer'
                ]);

                if (!$userId) {
                    $_SESSION['booking_error'] = 'Gagal membuat akun';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                // Auto-login
                $this->session->createSession($userId, 'Customer', $name);
                header('Location: /index.php?page=booking&step=5');
                exit;
            }
        }
    }

    /**
     * Step 5: Review & Pricing
     */
    public function step5()
    {
        $booking = $_SESSION['booking'] ?? [];

        if (empty($booking)) {
            header('Location: /index.php?page=booking&step=1');
            exit;
        }

        // Calculate pricing
        $pricingService = new \App\Core\PricingService();
        $pricing = $pricingService->calculateTotal(
            $booking['service_id'] ?? null,
            $booking['bundle_id'] ?? null,
            $booking['addon_ids'] ?? [],
            $_POST['promo_code'] ?? null
        );

        // Get booking details for review
        $details = $this->getBookingDetails($booking);

        return [
            'view' => 'Booking/step5',
            'data' => [
                'step' => 5,
                'title' => 'Konfirmasi Pesanan',
                'booking' => $booking,
                'details' => $details,
                'pricing' => $pricing,
                'is_logged_in' => $this->session->isLoggedIn()
            ]
        ];
    }

    /**
     * Step 6: Confirmation & QR Code
     */
    public function step6()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $resId = $_GET['res_id'] ?? null;

            if (!$resId) {
                header('Location: /index.php?page=booking&step=1');
                exit;
            }

            $qrService = new \App\Core\QrCodeService();
            $reservation = $qrService->getReservationWithQr($resId);

            if (!$reservation) {
                $_SESSION['booking_error'] = 'Reservation tidak ditemukan';
                header('Location: /index.php?page=booking&step=1');
                exit;
            }

            return [
                'view' => 'Booking/step6',
                'data' => [
                    'step' => 6,
                    'title' => 'Konfirmasi Booking',
                    'reservation' => $reservation,
                    'qr_code_url' => $reservation['booking_qr_code_url'],
                    'confirmation_id' => $reservation['booking_confirmation_id']
                ]
            ];
        }
    }

    /**
     * Handle final booking submission
     * POST /booking/submit
     */
    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $booking = $_SESSION['booking'] ?? [];

        if (empty($booking)) {
            $_SESSION['booking_error'] = 'Data booking tidak valid';
            header('Location: /index.php?page=booking&step=1');
            exit;
        }

        try {
            // Create reservation
            $reservationsModel = new ReservationsModel();
            
            // Get current user ID or use from POST
            $userId = $this->session->getUserId() ?? $_POST['user_id'] ?? null;

            if (!$userId) {
                $_SESSION['booking_error'] = 'Silakan login terlebih dahulu';
                header('Location: /index.php?page=booking&step=4.1');
                exit;
            }

            // Calculate pricing
            $pricingService = new \App\Core\PricingService();
            $pricing = $pricingService->calculateTotal(
                $booking['service_id'] ?? null,
                $booking['bundle_id'] ?? null,
                $booking['addon_ids'] ?? [],
                $_POST['promo_code'] ?? null
            );

            // Create reservation
            $resId = $reservationsModel->create([
                'user_id' => $userId,
                'service_id' => $booking['service_id'],
                'reservation_date' => $booking['reservation_date'],
                'reservation_time' => $booking['reservation_time'],
                'service_bundle_id' => $booking['bundle_id'],
                'base_price' => $pricing['base_price'],
                'addons_price' => $pricing['addons_price'],
                'promo_id' => $pricing['promo_id'],
                'promo_discount' => $pricing['promo_discount'],
                'total_price' => $pricing['total_price'],
                'payment_method' => $_POST['payment_method'] ?? 'cash',
                'status' => 'Pending'
            ]);

            if (!$resId) {
                throw new \Exception('Gagal membuat reservasi');
            }

            // Add addons if any
            if (!empty($booking['addon_ids'])) {
                $resAddonModel = new \App\Models\ReservationAddonModel();
                foreach ($booking['addon_ids'] as $addonId) {
                    $resAddonModel->addAddonToReservation($resId, $addonId);
                }
            }

            // Generate QR code
            $qrService = new \App\Core\QrCodeService();
            $confirmationId = $qrService->generateBookingId();
            $qrCodeUrl = $qrService->generateQrCode([
                'booking_confirmation_id' => $confirmationId,
                'user_id' => $userId,
                'reservation_date' => $booking['reservation_date'],
                'reservation_time' => $booking['reservation_time'],
                'total_price' => $pricing['total_price']
            ]);

            // Save QR code to DB
            if ($qrCodeUrl) {
                $qrService->saveQrCodeToReservation($resId, $confirmationId, $qrCodeUrl);
            }

            // Clear booking session
            unset($_SESSION['booking']);

            // Redirect to confirmation
            header('Location: /index.php?page=booking&step=6&res_id=' . $resId);
            exit;
        } catch (\Exception $e) {
            $_SESSION['booking_error'] = 'Error: ' . $e->getMessage();
            header('Location: /index.php?page=booking&step=5');
            exit;
        }
    }

    /**
     * Get booking details from session data
     */
    private function getBookingDetails(array $booking): array
    {
        $details = [];

        // Get service details
        if (!empty($booking['service_id'])) {
            $servicesModel = new ServicesModel();
            $service = $servicesModel->findById($booking['service_id']);
            $details['service'] = $service;
        }

        // Get bundle details
        if (!empty($booking['bundle_id'])) {
            $bundleModel = new \App\Models\ServiceBundleModel();
            $bundle = $bundleModel->getBundleWithPrice($booking['bundle_id']);
            $details['bundle'] = $bundle;
        }

        // Get addon details
        if (!empty($booking['addon_ids'])) {
            $addonModel = new \App\Models\BookingAddonModel();
            $addons = [];
            foreach ($booking['addon_ids'] as $addonId) {
                $addon = $addonModel->getAddonById($addonId);
                if ($addon) {
                    $addons[] = $addon;
                }
            }
            $details['addons'] = $addons;
        }

        // Get beautician details
        if (!empty($booking['beautician_id'])) {
            $stmt = $this->db->prepare(
                'SELECT u.user_id, u.NAME as name, sp.specialization, sp.photo_url, sp.rating
                 FROM users u
                 JOIN staff_profiles sp ON u.user_id = sp.user_id
                 WHERE u.user_id = :id'
            );
            $stmt->execute([':id' => $booking['beautician_id']]);
            $details['beautician'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $details['date'] = $booking['reservation_date'] ?? null;
        $details['time'] = $booking['reservation_time'] ?? null;

        return $details;
    }
}
