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
            $serviceIds = $_POST['service_ids'] ?? [];
            $serviceId = $_POST['service_id'] ?? null;
            
            if ($serviceId && !in_array($serviceId, $serviceIds)) {
                $serviceIds[] = $serviceId;
            }

            if (empty($serviceIds)) {
                $_SESSION['booking_error'] = 'Pilih minimal satu layanan terlebih dahulu';
                header('Location: /index.php?page=booking&step=1');
                exit;
            }

            // Fetch new category
            $servicesModel = new ServicesModel();
            $newService = $servicesModel->findById($serviceIds[0]);
            $newCategory = $this->normalizeBookingCategory((string) ($newService['category'] ?? 'hair'));

            // Fetch old category from session if exists
            $oldServiceId = $_SESSION['booking']['service_id'] ?? null;
            $oldService = $oldServiceId ? $servicesModel->findById($oldServiceId) : null;
            $oldCategory = $oldService ? $this->normalizeBookingCategory((string) ($oldService['category'] ?? 'hair')) : null;

            // Store in session
            $_SESSION['booking'] = $_SESSION['booking'] ?? [];
            
            // If category changed, reset addons and promo
            if ($oldCategory && $oldCategory !== $newCategory) {
                $_SESSION['booking']['addon_ids'] = [];
                $_SESSION['booking']['promo_id'] = null;
                $_SESSION['booking']['beautician_id'] = null;
            }

            $_SESSION['booking']['service_ids'] = $serviceIds;
            // Kept for backward compatibility if needed in UI
            $_SESSION['booking']['service_id'] = $serviceIds[0]; 

            header('Location: /index.php?page=booking&step=2');
            exit;
        }
    }

    /**
     * Step 2: Bundle & Add-ons selection
     * Bundles = promotions (promo_id, promo_name, included_fb_item, discount_value)
     * Addons = services WHERE is_addon = TRUE (grouped by category)
     */
    public function step2()
    {
        // Map promos to bundle-like format for the view
        $mapping = [
            1 => [
                'services' => ['SV01'],
                'menus' => ['M001']
            ],
            2 => [
                'services' => ['SV03'],
                'menus' => ['M009']
            ],
            3 => [
                'services' => ['SV33'],
                'menus' => [],
                'fb_custom_price' => 25000
            ],
            4 => [
                'services' => ['SV52'],
                'menus' => ['M002']
            ],
            5 => [
                'services' => ['SV50', 'SV63', 'ADD-20'],
                'menus' => []
            ],
            6 => [
                'services' => ['SV36', 'SV19', 'ADD-16'],
                'menus' => []
            ],
            7 => [
                'services' => ['SV02', 'SV37', 'ADD-15'],
                'menus' => []
            ],
            8 => [
                'services' => ['SV49', 'SV60', 'ADD-19'],
                'menus' => []
            ]
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $db = \App\Core\Database::getConnection();

            // Addons = services with is_addon = TRUE, grouped by category
            $addonStmt = $db->query(
                "SELECT service_id, service_name, category, base_tariff, est_duration
                 FROM services
                 WHERE is_addon = TRUE
                 ORDER BY category, service_name"
            );
            $allAddons = $addonStmt->fetchAll();
            $addons = [];
            foreach ($allAddons as $addon) {
                $cat = $addon['category'];
                if (!isset($addons[$cat])) {
                    $addons[$cat] = [];
                }
                $addons[$cat][] = $addon;
            }

            // Bundles: use promotions table as bundle-like offers
            $promoStmt = $db->query(
                "SELECT promo_id, promo_name, included_fb_item, discount_value
                 FROM promotions
                 ORDER BY promo_id"
            );
            $promos = $promoStmt->fetchAll();

            $selectedServiceIds = $_SESSION['booking']['service_ids'] ?? [];

            $bundles = [];
            foreach ($promos as $promo) {
                $id = (int)$promo['promo_id'];
                $originalPrice = 0;
                
                $isSelectable = false;
                if (isset($mapping[$id])) {
                    $map = $mapping[$id];
                    $primaryServices = array_filter($map['services'], function($sId) {
                        return strpos($sId, 'ADD-') !== 0;
                    });
                    
                    $intersect = array_intersect($selectedServiceIds, $primaryServices);
                    if (!empty($intersect)) {
                        $isSelectable = true;
                    }
                    
                    if (!empty($map['services'])) {
                        $placeholders = implode(',', array_fill(0, count($map['services']), '?'));
                        $sStmt = $db->prepare("SELECT SUM(base_tariff) as total FROM services WHERE service_id IN ($placeholders)");
                        $sStmt->execute($map['services']);
                        $originalPrice += (float)($sStmt->fetchColumn() ?? 0);
                    }
                    
                    if (!empty($map['menus'])) {
                        $placeholders = implode(',', array_fill(0, count($map['menus']), '?'));
                        $mStmt = $db->prepare("SELECT SUM(price) as total FROM db_merish_cafe.menus WHERE menu_id IN ($placeholders)");
                        $mStmt->execute($map['menus']);
                        $originalPrice += (float)($mStmt->fetchColumn() ?? 0);
                    }
                    
                    if (isset($map['fb_custom_price'])) {
                        $originalPrice += $map['fb_custom_price'];
                    }
                }
                
                $discountValue = (float)$promo['discount_value'];
                $finalPrice = max(0, $originalPrice - $discountValue);
                
                $bundles[] = [
                    'bundle_id' => 'promo_' . $promo['promo_id'],
                    'bundle_name' => $promo['promo_name'],
                    'description' => 'Termasuk: ' . ($promo['included_fb_item'] ?: 'Paket Spesial'),
                    'original_price' => $originalPrice,
                    'final_price' => $finalPrice,
                    'discount_value' => $discountValue,
                    'badge' => 'Promo',
                    'icon' => '✦',
                    'is_selectable' => $isSelectable,
                ];
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

            // Map bundle_id back to promo_id if it starts with "promo_"
            $bundleId = $_POST['bundle_id'] ?? null;
            if ($bundleId && str_starts_with($bundleId, 'promo_')) {
                $promoId = (int)substr($bundleId, 6);
                
                // Validate if this promo is selectable for the selected services
                $selectedServiceIds = $_SESSION['booking']['service_ids'] ?? [];
                $isSelectable = false;
                if (isset($mapping[$promoId])) {
                    $map = $mapping[$promoId];
                    $primaryServices = array_filter($map['services'], function($sId) {
                        return strpos($sId, 'ADD-') !== 0;
                    });
                    $intersect = array_intersect($selectedServiceIds, $primaryServices);
                    if (!empty($intersect)) {
                        $isSelectable = true;
                    }
                }
                
                if (!$isSelectable) {
                    $_SESSION['booking_error'] = 'Promo bundling ini tidak cocok dengan treatment yang dipilih';
                    header('Location: /index.php?page=booking&step=2');
                    exit;
                }
                
                $_SESSION['booking']['promo_id'] = $promoId;
            } else {
                $_SESSION['booking']['promo_id'] = null;
            }

            // Addon IDs are service_ids with is_addon=TRUE
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
            date_default_timezone_set('Asia/Jakarta');
            
            $isLoggedIn = isset($_SESSION['user_id']);
            $user = null;
            $vipAccessEnabled = false;
            $bookingWindowDays = 30;
            $memberTierName = 'Guest';

            if ($isLoggedIn) {
                $usersModel = new UsersModel();
                $user = $usersModel->findById((int) $_SESSION['user_id']);
                $loyaltyStage = (int) ($user['loyalty_stage'] ?? 1);
                $totalSpent = (float) ($user['total_spent'] ?? 0);

                $bookingWindowDays = $loyaltyStage >= 3 ? 45 : 30;
                $vipAccessEnabled = $loyaltyStage >= 3 || $totalSpent >= 2000000;
                $memberTierName = LoyaltyModel::getTierName($loyaltyStage);
            }

            $booking = $_SESSION['booking'] ?? [];
            $minDate = date('Y-m-d');
            $maxDate = date('Y-m-d', strtotime('+' . max(1, $bookingWindowDays) . ' days'));
            
            // Accept date parameter from GET and save to session
            $selectedDate = $_GET['date'] ?? ($booking['reservation_date'] ?? $minDate);
            if ($selectedDate < $minDate) {
                $selectedDate = $minDate;
            } elseif ($selectedDate > $maxDate) {
                $selectedDate = $maxDate;
            }
            $_SESSION['booking'] = $_SESSION['booking'] ?? [];
            $_SESSION['booking']['reservation_date'] = $selectedDate;

            // Accept view month/year from GET parameters
            $selectedDateObj = new \DateTime($selectedDate);
            $viewMonth = isset($_GET['view_month']) ? (int) $_GET['view_month'] : (int) $selectedDateObj->format('m');
            $viewYear = isset($_GET['view_year']) ? (int) $_GET['view_year'] : (int) $selectedDateObj->format('Y');

            // Sanitize view month and year
            if ($viewMonth < 1 || $viewMonth > 12) {
                $viewMonth = (int) $selectedDateObj->format('m');
            }
            if ($viewYear < 2000 || $viewYear > 2100) {
                $viewYear = (int) $selectedDateObj->format('Y');
            }

            // Fetch pricing
            $pricingService = new \App\Core\PricingService();
            $serviceIdsParam = !empty($booking['service_ids']) ? $booking['service_ids'] : ($booking['service_id'] ?? null);
            $pricing = $pricingService->calculateTotal(
                $serviceIdsParam,
                $booking['addon_ids'] ?? [],
                $booking['promo_id'] ?? null
            );

            // Fetch duration
            $durationMinutes = $this->getBookingDurationMinutes($booking);

            return [
                'view' => 'Booking/step3',
                'data' => [
                    'step' => 3,
                    'title' => 'Pilih Tanggal & Waktu',
                    'booking' => $_SESSION['booking'],
                    'min_date' => $minDate,
                    'max_date' => $maxDate,
                    'booking_window_days' => $bookingWindowDays,
                    'vip_access_enabled' => $vipAccessEnabled,
                    'member_name' => $user['NAME'] ?? ($_SESSION['full_name'] ?? 'Guest'),
                    'member_tier_name' => $memberTierName,
                    'pricing' => $pricing,
                    'duration_minutes' => $durationMinutes,
                    'selected_date' => $selectedDate,
                    'view_month' => $viewMonth,
                    'view_year' => $viewYear
                ]
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            date_default_timezone_set('Asia/Jakarta');
            
            $date = $_POST['reservation_date'] ?? null;
            $time = $_POST['reservation_time'] ?? null;

            if (!$date || !$time) {
                $_SESSION['booking_error'] = 'Pilih tanggal dan waktu';
                header('Location: /index.php?page=booking&step=3');
                exit;
            }

            // Check if selected time is in the past
            if ($date === date('Y-m-d') && $time <= date('H:i')) {
                $_SESSION['booking_error'] = 'Slot waktu sudah terlewat. Silakan pilih waktu lain.';
                header('Location: /index.php?page=booking&step=3');
                exit;
            }

            $booking = $_SESSION['booking'] ?? [];
            $durationMinutes = $this->getBookingDurationMinutes($booking);

            // Get category
            $servicesModel = new ServicesModel();
            $service = $servicesModel->findById((string)($booking['service_id'] ?? ''));
            $category = $this->normalizeBookingCategory((string)($service['category'] ?? 'hair'));

            // Check if there are any staff members registered in the database for this category at all
            $specializations = $this->getSpecializationsForCategory($category);
            $placeholders = implode(',', array_fill(0, count($specializations), '?'));
            $countStmt = $this->db->prepare("SELECT COUNT(*) FROM staff_profiles WHERE specialization IN ({$placeholders})");
            $countStmt->execute($specializations);
            $totalMatchingBeauticians = (int)$countStmt->fetchColumn();

            // Check overlap
            $availableSeats = $this->getAvailableSeatsForBooking($date, $time, $durationMinutes);
            
            $beauticianCheckPassed = false;
            if ($totalMatchingBeauticians === 0) {
                $beauticianCheckPassed = true; // Skip beautician check if no matching specialization registered in DB
            } else {
                $availableBeauticians = $this->getAvailableBeauticiansForBooking($category, $date, $time, $durationMinutes);
                if (!empty($availableBeauticians)) {
                    $beauticianCheckPassed = true;
                }
            }

            // Check if end time is past 18:00
            $startDateTimeStr = $date . ' ' . $time . ':00';
            $endTimestamp = strtotime($startDateTimeStr) + ($durationMinutes * 60);
            $endTimeFormatted = date('H:i', $endTimestamp);

            if ($endTimeFormatted > '21:00' || empty($availableSeats) || !$beauticianCheckPassed) {
                // Conflict detected! Find next suggestion
                $endTimeStr = date('H:i', strtotime($startDateTimeStr) + ($durationMinutes * 60));
                $suggestion = $this->findNextAvailableSlot($date, $endTimeStr, $durationMinutes, $category);

                if ($suggestion) {
                    $_SESSION['booking_error'] = "Slot waktu " . htmlspecialchars($time) . " - " . htmlspecialchars($endTimeStr) . " tidak tersedia karena bentrok. Rekomendasi slot terdekat: " . htmlspecialchars($suggestion['time']) . " pada " . htmlspecialchars(date('d M Y', strtotime($suggestion['date'])));
                    $_SESSION['booking_suggestion'] = $suggestion;
                } else {
                    $_SESSION['booking_error'] = "Slot waktu " . htmlspecialchars($time) . " - " . htmlspecialchars($endTimeStr) . " tidak tersedia. Silakan pilih waktu lain.";
                }

                // Keep selected inputs in session so they can see what they selected
                $_SESSION['booking']['reservation_date'] = $date;
                $_SESSION['booking']['reservation_time'] = $time;

                header('Location: /index.php?page=booking&step=3');
                exit;
            }

            $_SESSION['booking']['reservation_date'] = $date;
            $_SESSION['booking']['reservation_time'] = $time;
            $_SESSION['booking']['seat_id'] = $availableSeats[0]['seat_id'];

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
            $beauticians = $this->getBeauticiansForBookingView($selectedCategory, $selectedDate, $selectedTime, $durationMinutes);

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

            if (empty($beauticianId)) {
                // Sapu Jagat: Pick the first available beautician
                $servicesModel = new ServicesModel();
                $selectedServiceId = (string) ($_SESSION['booking']['service_id'] ?? '');
                $selectedService = $selectedServiceId !== '' ? $servicesModel->findById($selectedServiceId) : null;
                $category = $this->normalizeBookingCategory((string) ($selectedService['category'] ?? 'hair'));
                $date = $_SESSION['booking']['reservation_date'] ?? date('Y-m-d');
                $time = $_SESSION['booking']['reservation_time'] ?? '10:00';
                $durationMinutes = $this->getBookingDurationMinutes($_SESSION['booking']);

                $available = $this->getAvailableBeauticiansForBooking($category, $date, $time, $durationMinutes);
                if (!empty($available)) {
                    $beauticianId = $available[0]['user_id'];
                }
            }

            $_SESSION['booking']['beautician_id'] = $beauticianId;

            // Check if logged in before proceeding to checkout
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['post_login_redirect'] = 'index.php?page=booking&step=5';
                header('Location: index.php?page=register&context=checkout');
                exit;
            }

            header('Location: index.php?page=booking&step=5');
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
                    header('Location: /index.php?page=booking&step=4.1&mode=register');
                    exit;
                }

                // Check if email exists
                if ($usersModel->findByEmail($email)) {
                    $_SESSION['booking_error'] = 'Email sudah terdaftar';
                    header('Location: /index.php?page=booking&step=4.1&mode=register');
                    exit;
                }

                // Register new user
                $registered = $usersModel->register((string) $email, (string) $password, (string) $name, (string) ($phone ?? ''), 'Customer');

                if (!$registered) {
                    $_SESSION['booking_error'] = 'Gagal membuat akun';
                    header('Location: /index.php?page=booking&step=4.1&mode=register');
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
        // Check if logged in before viewing checkout
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['post_login_redirect'] = 'index.php?page=booking&step=5';
            header('Location: index.php?page=register&context=checkout');
            exit;
        }

        $booking = $_SESSION['booking'] ?? [];

        if (empty($booking)) {
            $_SESSION['booking_error'] = 'Pilih layanan, jadwal, dan stylist terlebih dahulu. Checkout tetap bisa dibuka setelah draft booking tersimpan.';
        }

        // Calculate pricing
        $pricingService = new \App\Core\PricingService();
        $serviceIdsParam = !empty($booking['service_ids']) ? $booking['service_ids'] : ($booking['service_id'] ?? null);
        $pricing = $pricingService->calculateTotal(
            $serviceIdsParam,
            $booking['addon_ids'] ?? [],
            $booking['promo_id'] ?? null
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
                header('Location: index.php?page=booking&step=1');
                exit;
            }

            $qrService = new \App\Core\QrCodeService();
            $reservation = $qrService->getReservationWithQr($resId);

            if (!$reservation) {
                $_SESSION['booking_error'] = 'Reservation tidak ditemukan';
                header('Location: index.php?page=booking&step=1');
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
                 WHERE r.res_id = :res_id'
            );
            $stmt->execute([':res_id' => $resId]);
            $reservationDetailsRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
            $serviceNames = [];
            $reservationDetails = null;
            foreach ($reservationDetailsRows as $row) {
                if ($row['service_name']) {
                    $serviceNames[] = $row['service_name'];
                }
                if (!$reservationDetails) {
                    $reservationDetails = $row;
                }
            }

            $scheduleTime = $reservation['reservation_date'] ?? null;
            if (empty($scheduleTime) && !empty($reservation['schedule_time'])) {
                $scheduleTime = $reservation['schedule_time'];
            }

            $reservation['service_name'] = !empty($serviceNames) ? implode(', ', $serviceNames) : 'Signature Look';
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
                    'qr_code_url' => $reservation['booking_qr_code_url'] ?? '',
                    'confirmation_id' => $reservation['booking_confirmation_id'] ?? ''
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
            
            // Get current user ID or use from POST (can be null for guest checkout)
            $userId = $_SESSION['user_id'] ?? $_POST['user_id'] ?? null;

            // Calculate pricing
            $pricingService = new \App\Core\PricingService();
            $serviceIdsParam = !empty($booking['service_ids']) ? $booking['service_ids'] : ($booking['service_id'] ?? null);
            $pricing = $pricingService->calculateTotal(
                $serviceIdsParam,
                $booking['addon_ids'] ?? [],
                $booking['promo_id'] ?? null
            );

            $paymentProofUrl = null;
            if (!empty($_FILES['payment_proof']['name']) && ($_FILES['payment_proof']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $proofDir = __DIR__ . '/../../assets/uploads/bukti_dp';
                if (!is_dir($proofDir)) {
                    mkdir($proofDir, 0755, true);
                }

                $originalName = basename($_FILES['payment_proof']['name']);
                $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $fileName = time() . '_' . $safeName;
                $targetPath = $proofDir . DIRECTORY_SEPARATOR . $fileName;

                if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetPath)) {
                    $paymentProofUrl = '/assets/uploads/bukti_dp/' . $fileName;
                }
            }

            // Create reservation (only columns from original schema)
            $resId = $reservationsModel->create([
                'user_id' => $userId,
                'service_id' => $booking['service_id'] ?? null,
                'reservation_date' => $booking['reservation_date'],
                'reservation_time' => $booking['reservation_time'],
                'promo_id' => $pricing['promo_id'],
                'payment_proof_url' => $paymentProofUrl,
                'is_dp_paid' => $paymentProofUrl ? 1 : 0,
                'dp_amount' => 50000,
                'status' => 'Pending',
                'service_ids' => array_merge(
                    $booking['service_ids'] ?? [$booking['service_id'] ?? null],
                    $booking['addon_ids'] ?? []
                ),
                'beautician_id' => $booking['beautician_id'] ?? null,
                'seat_id' => $booking['seat_id'] ?? null
            ]);

            if (!$resId) {
                throw new \Exception('Gagal membuat reservasi');
            }

            // Create Cafe Order if bundle includes FB item
            if (!empty($pricing['promo_detail']['included_fb_item'])) {
                $fbItem = $pricing['promo_detail']['included_fb_item'];
                
                // Try to find the closest menu_id in Kafe DB.
                $stmtMenu = $this->db->prepare("SELECT menu_id, menu_name, price FROM db_merish_cafe.menus WHERE :fb_item LIKE CONCAT('%', menu_name, '%') LIMIT 1");
                $stmtMenu->execute([':fb_item' => $fbItem]);
                $menu = $stmtMenu->fetch();

                if ($menu) {
                    $ordersModel = new \App\Models\OrdersModel();
                    
                    // Guest name: Use logged-in user's name or a generic name.
                    $guestName = $_SESSION['full_name'] ?? 'Guest Salon (Bundling)';
                    
                    // Get the actual seat_id used for the reservation
                    $stmtRes = $this->db->prepare('SELECT seat_id FROM reservations WHERE res_id = :id');
                    $stmtRes->execute([':id' => $resId]);
                    $resRow = $stmtRes->fetch();
                    $assignedSeatId = $resRow['seat_id'] ?? null;
                    
                    $orderId = $ordersModel->create([
                        'guest_name' => $guestName,
                        'seat_id' => $assignedSeatId,
                        'total_amount' => 0, // Bundle price is handled in salon bill
                        'payment_method' => 'Salon Bill',
                        'payment_status' => 'Paid',
                        'status' => 'New'
                    ]);

                    if ($orderId) {
                        $ordersModel->createDetail([
                            'order_id' => $orderId,
                            'menu_id' => $menu['menu_id'],
                            'qty' => 1,
                            'subtotal' => 0
                        ]);
                    }
                }
            }

            // Generate QR code
            $qrService = new \App\Core\QrCodeService();
            $confirmationId = $qrService->generateBookingId();
            $qrCodeUrl = $qrService->generateQrCode([
                'res_id' => $resId,
                'user_id' => $userId,
                'reservation_date' => $booking['reservation_date'],
                'reservation_time' => $booking['reservation_time'],
            ]);

            // Save QR code to session
            if ($qrCodeUrl) {
                $qrService->saveQrCodeToReservation($resId, $confirmationId, $qrCodeUrl);
            }

            // Clear booking session
            unset($_SESSION['booking']);

            // Redirect to confirmation
            header('Location: index.php?page=booking&step=6&res_id=' . $resId);
            exit;
        } catch (\Exception $e) {
            $_SESSION['booking_error'] = 'Error: ' . $e->getMessage();
            header('Location: index.php?page=booking&step=5');
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
        if (!empty($booking['service_ids']) || !empty($booking['service_id'])) {
            $servicesModel = new ServicesModel();
            $serviceIds = !empty($booking['service_ids']) ? $booking['service_ids'] : [$booking['service_id']];
            $servicesList = [];
            foreach ($serviceIds as $sid) {
                $service = $servicesModel->findById($sid);
                if ($service) {
                    $servicesList[] = $service;
                }
            }
            $details['services'] = $servicesList;
            // Kept for backward compatibility if needed by old views
            $details['service'] = $servicesList[0] ?? null;
        }

        // Get bundle/promo details
        if (!empty($booking['promo_id'])) {
            $promoStmt = $this->db->prepare(
                "SELECT promo_id, promo_name, included_fb_item, discount_value
                 FROM promotions
                 WHERE promo_id = :id"
            );
            $promoStmt->execute([':id' => $booking['promo_id']]);
            $details['promo'] = $promoStmt->fetch(PDO::FETCH_ASSOC);
        }

        // Get addon details (addons = services WHERE is_addon = TRUE)
        if (!empty($booking['addon_ids'])) {
            $addons = [];
            $servicesModel = new ServicesModel();
            foreach ($booking['addon_ids'] as $addonId) {
                $addon = $servicesModel->findById($addonId);
                if ($addon) {
                    $addons[] = $addon;
                }
            }
            $details['addons'] = $addons;
        }

        // Get beautician details
        if (!empty($booking['beautician_id'])) {
            $stmt = $this->db->prepare(
                'SELECT u.user_id, u.NAME as name, sp.specialization
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
        $totalDuration = 0;
        $serviceIds = [];
        if (!empty($booking['service_ids'])) {
            $serviceIds = $booking['service_ids'];
        } elseif (!empty($booking['service_id'])) {
            $serviceIds = [$booking['service_id']];
        }
        
        $addonIds = $booking['addon_ids'] ?? [];
        $allIds = array_merge($serviceIds, $addonIds);
        $allIds = array_filter(array_unique($allIds));
        
        if (!empty($allIds)) {
            $placeholders = implode(',', array_fill(0, count($allIds), '?'));
            $stmt = $this->db->prepare("SELECT SUM(COALESCE(est_duration, 0)) as total_duration FROM services WHERE service_id IN ($placeholders)");
            $stmt->execute($allIds);
            $totalDuration = (int)$stmt->fetchColumn();
        }
        
        return $totalDuration > 0 ? $totalDuration : 60;
    }

    private function getBeauticiansForBookingView(string $category, string $reservationDate, string $reservationTime, int $durationMinutes): array
    {
        $stmt = $this->db->prepare("
            SELECT es.employee_name AS name,
                   es.role,
                   es.shift_start,
                   es.shift_end,
                   u.user_id,
                   u.email,
                   sp.profile_id,
                   sp.specialization,
                   sp.work_status
            FROM employee_schedules es
            LEFT JOIN users u ON es.employee_name = u.NAME
            LEFT JOIN staff_profiles sp ON u.user_id = sp.user_id
            WHERE (
                (:cat1 = 'nails' AND es.role LIKE '%Nailist%') OR
                (:cat2 = 'lashes' AND es.role LIKE '%Lash%') OR
                (:cat3 = 'wax' AND (es.role LIKE '%Wax%' OR es.role LIKE '%Eyebrow%')) OR
                (:cat4 = 'hair' AND es.role LIKE '%Hair%')
            )
            ORDER BY es.employee_name ASC
        ");
        $stmt->execute([
            ':cat1' => $category,
            ':cat2' => $category,
            ':cat3' => $category,
            ':cat4' => $category
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $requestStart = new \DateTimeImmutable($reservationDate . ' ' . $reservationTime);
        $requestEnd = $requestStart->modify('+' . max(30, $durationMinutes) . ' minutes');

        $busyStmt = $this->db->prepare("
            SELECT rd.beautician_id,
                   r.schedule_time,
                   COALESCE(s.est_duration, 60) AS est_duration
            FROM reservations r
            JOIN reservation_details rd ON rd.res_id = r.res_id
            JOIN services s ON s.service_id = rd.service_id
            WHERE DATE(r.schedule_time) = :date
              AND r.STATUS IN ('Pending', 'Confirmed', 'In-Service')
              AND rd.beautician_id IS NOT NULL
        ");
        $busyStmt->execute([':date' => $reservationDate]);
        $busyRows = $busyStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $busyBeauticians = [];
        foreach ($busyRows as $busyRow) {
            $busyBeauticianId = (int) ($busyRow['beautician_id'] ?? 0);
            if ($busyBeauticianId <= 0) continue;

            $busyStart = new \DateTimeImmutable((string) ($busyRow['schedule_time'] ?? $reservationDate . ' 00:00:00'));
            $busyEnd = $busyStart->modify('+' . max(30, (int) ($busyRow['est_duration'] ?? 60)) . ' minutes');

            if ($requestStart < $busyEnd && $requestEnd > $busyStart) {
                $busyBeauticians[$busyBeauticianId] = true;
            }
        }

        $result = [];
        foreach ($rows as $row) {
            $beauticianId = (int) ($row['user_id'] ?? 0);
            $available = true;

            // Check shift hours
            $shiftStartStr = $reservationDate . ' ' . ($row['shift_start'] ?? '09:00:00');
            $shiftEndStr = $reservationDate . ' ' . ($row['shift_end'] ?? '21:00:00');
            $shiftStart = new \DateTimeImmutable($shiftStartStr);
            $shiftEnd = new \DateTimeImmutable($shiftEndStr);

            if ($requestStart < $shiftStart || $requestEnd > $shiftEnd) {
                $available = false;
            }

            // Check conflict
            if ($beauticianId > 0 && isset($busyBeauticians[$beauticianId])) {
                $available = false;
            }

            $row['available'] = $available;
            $row['category'] = $category;
            $result[] = $row;
        }

        return $result;
    }

    private function getAvailableBeauticiansForBooking(string $category, string $reservationDate, string $reservationTime, int $durationMinutes): array
    {
        $all = $this->getBeauticiansForBookingView($category, $reservationDate, $reservationTime, $durationMinutes);
        $available = [];
        foreach ($all as $b) {
            if ($b['available']) {
                $available[] = $b;
            }
        }
        return $available;
    }

    private function getAvailableSeatsForBooking(string $reservationDate, string $reservationTime, int $durationMinutes): array
    {
        $startStr = $reservationDate . ' ' . $reservationTime . ':00';
        $endStr = date('Y-m-d H:i:s', strtotime($startStr) + ($durationMinutes * 60));
        
        $stmtSeats = $this->db->query("SELECT seat_id, seat_name FROM seats WHERE zone_type = 'Kursi Salon'");
        $allSeats = $stmtSeats->fetchAll(PDO::FETCH_ASSOC);
        
        $stmtOccupied = $this->db->prepare("
            SELECT DISTINCT r.seat_id 
            FROM reservations r
            WHERE r.status IN ('Pending', 'Confirmed', 'In-Service')
              AND :new_start < DATE_ADD(r.schedule_time, INTERVAL (
                  SELECT COALESCE(SUM(s.est_duration), 60) 
                  FROM reservation_details rd 
                  JOIN services s ON rd.service_id = s.service_id 
                  WHERE rd.res_id = r.res_id
              ) MINUTE)
              AND :new_end > r.schedule_time
        ");
        $stmtOccupied->execute([
            ':new_start' => $startStr,
            ':new_end' => $endStr
        ]);
        $occupiedSeatIds = $stmtOccupied->fetchAll(PDO::FETCH_COLUMN) ?: [];
        
        $available = [];
        foreach ($allSeats as $seat) {
            if (!in_array($seat['seat_id'], $occupiedSeatIds)) {
                $available[] = $seat;
            }
        }
        return $available;
    }

    private function findNextAvailableSlot(string $selectedDate, string $endTimeStr, int $durationMinutes, string $category): ?array
    {
        $currentDateTime = new \DateTime($selectedDate . ' ' . $endTimeStr);
        
        $specializations = $this->getSpecializationsForCategory($category);
        $placeholders = implode(',', array_fill(0, count($specializations), '?'));
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM staff_profiles WHERE specialization IN ({$placeholders})");
        $countStmt->execute($specializations);
        $totalMatchingBeauticians = (int)$countStmt->fetchColumn();

        // Loop up to 7 days ahead
        for ($day = 0; $day < 7; $day++) {
            if ($day > 0) {
                $currentDateTime->setTime(9, 0);
            }
            
            while ($currentDateTime->format('H:i') <= '20:30') {
                $startStr = $currentDateTime->format('Y-m-d H:i:s');
                $endTimestamp = $currentDateTime->getTimestamp() + ($durationMinutes * 60);
                $endDayStr = date('Y-m-d', $endTimestamp);
                $endTimeFormatted = date('H:i', $endTimestamp);
                
                if ($endDayStr === $currentDateTime->format('Y-m-d') && $endTimeFormatted <= '21:00') {
                    $availableSeats = $this->getAvailableSeatsForBooking(
                        $currentDateTime->format('Y-m-d'),
                        $currentDateTime->format('H:i'),
                        $durationMinutes
                    );
                    if (!empty($availableSeats)) {
                        $beauticianCheckPassed = false;
                        if ($totalMatchingBeauticians === 0) {
                            $beauticianCheckPassed = true;
                        } else {
                            $availBeauticians = $this->getAvailableBeauticiansForBooking(
                                $category, 
                                $currentDateTime->format('Y-m-d'), 
                                $currentDateTime->format('H:i'), 
                                $durationMinutes
                            );
                            if (!empty($availBeauticians)) {
                                $beauticianCheckPassed = true;
                            }
                        }

                        if ($beauticianCheckPassed) {
                            return [
                                'date' => $currentDateTime->format('Y-m-d'),
                                'time' => $currentDateTime->format('H:i'),
                                'seat_id' => $availableSeats[0]['seat_id'],
                                'seat_name' => $availableSeats[0]['seat_name'],
                                'duration' => $durationMinutes
                            ];
                        }
                    }
                }
                
                $currentDateTime->modify('+30 minutes');
            }
            
            $currentDateTime->modify('+1 day');
        }
        
        return null;
    }

    /**
     * Submit review from customer history modal
     */
    public function addReview()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $resId = (int) ($_POST['res_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');
        $redirect = trim($_POST['redirect'] ?? 'index.php');

        if ($resId <= 0 || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Ulasan tidak valid.';
            header('Location: ' . $redirect);
            exit;
        }

        try {
            // Check if reservation belongs to current user
            $stmt = $this->db->prepare("SELECT user_id FROM db_merish_salon.reservations WHERE res_id = :res_id LIMIT 1");
            $stmt->execute([':res_id' => $resId]);
            $res = $stmt->fetch();

            if (!$res || (int)$res['user_id'] !== (int)$_SESSION['user_id']) {
                $_SESSION['error'] = 'Anda tidak memiliki hak untuk mengulas reservasi ini.';
                header('Location: ' . $redirect);
                exit;
            }

            // Check if review already exists
            $checkStmt = $this->db->prepare("SELECT review_id FROM db_merish_salon.reviews WHERE res_id = :res_id LIMIT 1");
            $checkStmt->execute([':res_id' => $resId]);
            if ($checkStmt->fetch()) {
                $_SESSION['error'] = 'Anda sudah mengirimkan ulasan untuk reservasi ini.';
                header('Location: ' . $redirect);
                exit;
            }

            // Insert review using ReviewsModel
            $reviewsModel = new \App\Models\ReviewsModel();
            $reviewsModel->create([
                'res_id' => $resId,
                'rating' => $rating,
                'comment' => $comment !== '' ? $comment : null
            ]);

            $_SESSION['success'] = 'Terima kasih atas ulasan Anda!';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal mengirim ulasan: ' . $e->getMessage();
        }

        header('Location: ' . $redirect);
        exit;
    }
}
