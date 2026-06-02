<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Models\LoyaltyModel;
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
            $isLoggedIn = isset($_SESSION['user_id']);
            $user = null;
            $vipAccessEnabled = false;
            $bookingWindowDays = 1;
            $memberTierName = 'Guest';

            if ($isLoggedIn) {
                $usersModel = new UsersModel();
                $user = $usersModel->findById((int) $_SESSION['user_id']);
                $loyaltyStage = (int) ($user['loyalty_stage'] ?? 1);
                $totalSpent = (float) ($user['total_spent'] ?? 0);

                $bookingWindowDays = max(1, LoyaltyModel::getBookingWindow($loyaltyStage));
                $vipAccessEnabled = $loyaltyStage >= 3 || $totalSpent >= 2000000;
                $memberTierName = LoyaltyModel::getTierName($loyaltyStage);
            }

            return [
                'view' => 'Booking/step3',
                'data' => [
                    'step' => 3,
                    'title' => 'Pilih Tanggal & Waktu',
                    'booking' => $_SESSION['booking'] ?? [],
                    'min_date' => date('Y-m-d', strtotime('+1 day')),
                    'max_date' => date('Y-m-d', strtotime('+' . max(1, $bookingWindowDays) . ' days')),
                    'booking_window_days' => $bookingWindowDays,
                    'vip_access_enabled' => $vipAccessEnabled,
                    'member_name' => $user['NAME'] ?? ($_SESSION['full_name'] ?? 'Guest'),
                    'member_tier_name' => $memberTierName
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
            $booking = $_SESSION['booking'] ?? [];
            $servicesModel = new ServicesModel();
            $selectedServiceId = (string) ($booking['service_id'] ?? '');
            $selectedService = $selectedServiceId !== '' ? $servicesModel->findById($selectedServiceId) : null;
            $selectedCategory = $this->normalizeBookingCategory((string) ($selectedService['category'] ?? 'hair'));
            $selectedDate = (string) ($booking['reservation_date'] ?? date('Y-m-d', strtotime('+1 day')));
            $selectedTime = (string) ($booking['reservation_time'] ?? '10:00');
            $durationMinutes = $this->getBookingDurationMinutes($booking);
            $beauticians = $this->getAvailableBeauticiansForBooking($selectedCategory, $selectedDate, $selectedTime, $durationMinutes);

            return [
                'view' => 'Booking/step4',
                'data' => [
                    'beauticians' => $beauticians,
                    'step' => 4,
                    'title' => 'Pilih Beautician',
                    'booking' => $booking,
                    'is_logged_in' => isset($_SESSION['user_id']),
                    'selected_category' => $selectedCategory,
                    'selected_category_label' => $this->getCategoryLabel($selectedCategory),
                    'selected_service_name' => $selectedService['service_name'] ?? 'Signature Service',
                    'selected_date' => $selectedDate,
                    'selected_time' => $selectedTime,
                    'duration_minutes' => $durationMinutes
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
            $usersModel = new UsersModel();

            if ($action === 'login') {
                $email = $_POST['email'] ?? null;
                $password = $_POST['password'] ?? null;

                if (!$email || !$password) {
                    $_SESSION['booking_error'] = 'Email dan password harus diisi';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                $user = $usersModel->login((string) $email, (string) $password);

                if (!$user) {
                    $_SESSION['booking_error'] = 'Email atau password salah';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                // Login success
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['ROLE'] ?? $user['role'] ?? 'Customer';
                $_SESSION['full_name'] = $user['NAME'] ?? $user['name'] ?? 'User';
                $_SESSION['user_login'] = $user['email'] ?? '';
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

                // Check if email exists
                if ($usersModel->findByEmail($email)) {
                    $_SESSION['booking_error'] = 'Email sudah terdaftar';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                // Register new user
                $registered = $usersModel->register((string) $email, (string) $password, (string) $name, (string) ($phone ?? ''), 'Customer');

                if (!$registered) {
                    $_SESSION['booking_error'] = 'Gagal membuat akun';
                    header('Location: /index.php?page=booking&step=4.1');
                    exit;
                }

                // Auto-login
                $user = $usersModel->login((string) $email, (string) $password);
                $_SESSION['user_id'] = $user['user_id'] ?? null;
                $_SESSION['role'] = $user['ROLE'] ?? 'Customer';
                $_SESSION['full_name'] = $user['NAME'] ?? $name;
                $_SESSION['user_login'] = $user['email'] ?? $email;
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

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['booking_error'] = 'Silakan daftar terlebih dahulu untuk melanjutkan checkout';
            $_SESSION['post_login_redirect'] = '/index.php?page=booking&step=5';
            header('Location: /index.php?page=register');
            exit;
        }

        if (empty($booking)) {
            $_SESSION['booking_error'] = 'Pilih layanan, jadwal, dan stylist terlebih dahulu. Checkout tetap bisa dibuka setelah draft booking tersimpan.';
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
                'is_logged_in' => isset($_SESSION['user_id'])
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

            $stmt = $this->db->prepare(
                'SELECT rd.service_id,
                        s.service_name,
                        u.NAME AS beautician_name,
                        sp.specialization,
                        r.schedule_time
                 FROM reservations r
                 LEFT JOIN reservation_details rd ON rd.res_id = r.res_id
                 LEFT JOIN services s ON s.service_id = rd.service_id
                 LEFT JOIN users u ON u.user_id = rd.beautician_id
                 LEFT JOIN staff_profiles sp ON sp.user_id = rd.beautician_id
                 WHERE r.res_id = :res_id
                 LIMIT 1'
            );
            $stmt->execute([':res_id' => $resId]);
            $reservationDetails = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            $scheduleTime = $reservation['reservation_date'] ?? null;
            if (empty($scheduleTime) && !empty($reservation['schedule_time'])) {
                $scheduleTime = $reservation['schedule_time'];
            }

            $reservation['service_name'] = $reservationDetails['service_name'] ?? 'Signature Look';
            $reservation['beautician_name'] = $reservationDetails['beautician_name'] ?? 'Any Available Staff';
            $reservation['specialization'] = $reservationDetails['specialization'] ?? 'Stylist';
            $reservation['reservation_date'] = $reservation['reservation_date'] ?? (!empty($scheduleTime) ? date('Y-m-d', strtotime($scheduleTime)) : null);
            $reservation['reservation_time'] = $reservation['reservation_time'] ?? (!empty($scheduleTime) ? date('H:i', strtotime($scheduleTime)) : null);

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
            $userId = $_SESSION['user_id'] ?? $_POST['user_id'] ?? null;

            if (!$userId) {
                $_SESSION['booking_error'] = 'Silakan daftar terlebih dahulu untuk melanjutkan checkout';
                $_SESSION['post_login_redirect'] = '/index.php?page=booking&step=5';
                header('Location: /index.php?page=register');
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

            $paymentProofUrl = null;
            if (!empty($_FILES['payment_proof']['name']) && ($_FILES['payment_proof']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $proofDir = __DIR__ . '/../../uploads/payment_proofs';
                if (!is_dir($proofDir)) {
                    mkdir($proofDir, 0755, true);
                }

                $originalName = basename($_FILES['payment_proof']['name']);
                $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $fileName = time() . '_' . $safeName;
                $targetPath = $proofDir . DIRECTORY_SEPARATOR . $fileName;

                if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetPath)) {
                    $paymentProofUrl = '/uploads/payment_proofs/' . $fileName;
                }
            }

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
                'payment_proof_url' => $paymentProofUrl,
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

    private function normalizeBookingCategory(string $category): string
    {
        $value = strtolower(trim($category));

        return match ($value) {
            'hair' => 'hair',
            'nails' => 'nails',
            'lashes' => 'lashes',
            'wax & eyebrows', 'wax', 'eyebrows', 'wax and eyebrows' => 'wax',
            default => 'hair',
        };
    }

    private function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'nails' => 'Nails',
            'lashes' => 'Lashes',
            'wax' => 'Wax & Eyebrows',
            default => 'Hair',
        };
    }

    private function getBeauticianCategoryFromSpecialization(string $specialization): string
    {
        $value = strtolower(trim($specialization));

        return match (true) {
            str_contains($value, 'nail') => 'nails',
            str_contains($value, 'lash') => 'lashes',
            str_contains($value, 'wax') => 'wax',
            default => 'hair',
        };
    }

    private function getSpecializationsForCategory(string $category): array
    {
        return match ($category) {
            'nails' => ['Nailist'],
            'lashes' => ['Lash Technician'],
            'wax' => ['Wax & Threading Specialist'],
            default => ['Hair Stylist'],
        };
    }

    private function getBookingDurationMinutes(array $booking): int
    {
        $duration = 60;

        if (!empty($booking['service_id'])) {
            $servicesModel = new ServicesModel();
            $service = $servicesModel->findById((string) $booking['service_id']);
            if (!empty($service['est_duration'])) {
                $duration = (int) $service['est_duration'];
            }
        }

        return max(30, $duration);
    }

    private function getAvailableBeauticiansForBooking(string $category, string $reservationDate, string $reservationTime, int $durationMinutes): array
    {
        $specializations = $this->getSpecializationsForCategory($category);
        $placeholders = implode(',', array_fill(0, count($specializations), '?'));

        $stmt = $this->db->prepare(
            "SELECT sp.profile_id,
                    u.user_id,
                    u.NAME AS name,
                    u.email,
                    sp.specialization,
                    sp.work_status,
                    sp.hire_date
             FROM staff_profiles sp
             JOIN users u ON sp.user_id = u.user_id
             WHERE sp.work_status = 'Online'
               AND u.ROLE = 'Beautician'
               AND sp.specialization IN ({$placeholders})
             ORDER BY u.NAME ASC"
        );
        $stmt->execute($specializations);
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($candidates)) {
            return [];
        }

        $busyStmt = $this->db->prepare(
            "SELECT rd.beautician_id,
                    r.schedule_time,
                    COALESCE(s.est_duration, 60) AS est_duration
             FROM reservations r
             JOIN reservation_details rd ON rd.res_id = r.res_id
             JOIN services s ON s.service_id = rd.service_id
             WHERE DATE(r.schedule_time) = :date
               AND r.STATUS IN ('Pending', 'Confirmed', 'In-Service')
               AND rd.beautician_id IS NOT NULL"
        );
        $busyStmt->execute([':date' => $reservationDate]);
        $busyRows = $busyStmt->fetchAll(PDO::FETCH_ASSOC);

        $requestStart = new \DateTimeImmutable($reservationDate . ' ' . $reservationTime);
        $requestEnd = $requestStart->modify('+' . max(30, $durationMinutes) . ' minutes');
        $busyBeauticians = [];

        foreach ($busyRows as $busyRow) {
            $busyBeauticianId = (int) ($busyRow['beautician_id'] ?? 0);
            if ($busyBeauticianId <= 0) {
                continue;
            }

            $busyStart = new \DateTimeImmutable((string) ($busyRow['schedule_time'] ?? $reservationDate . ' 00:00:00'));
            $busyEnd = $busyStart->modify('+' . max(30, (int) ($busyRow['est_duration'] ?? 60)) . ' minutes');

            if ($requestStart < $busyEnd && $requestEnd > $busyStart) {
                $busyBeauticians[$busyBeauticianId] = true;
            }
        }

        $available = [];
        foreach ($candidates as $candidate) {
            $beauticianId = (int) ($candidate['user_id'] ?? 0);
            if ($beauticianId <= 0 || isset($busyBeauticians[$beauticianId])) {
                continue;
            }

            $candidate['category'] = $this->getBeauticianCategoryFromSpecialization((string) ($candidate['specialization'] ?? 'Hair Stylist'));
            $candidate['available'] = true;
            $available[] = $candidate;
        }

        return $available;
    }
}
