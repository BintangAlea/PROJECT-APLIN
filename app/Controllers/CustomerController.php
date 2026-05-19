<?php

namespace App\Controllers;

use App\Models\ReservationsModel;
use App\Models\ServicesModel;
use App\Models\MenusModel;
use App\Models\BeauticiansModel;
use App\Models\OrdersModel;

class CustomerController
{
    private ReservationsModel $reservationsModel;
    private ServicesModel $servicesModel;
    private MenusModel $menusModel;
    private BeauticiansModel $beauticiansModel;
    private OrdersModel $ordersModel;

    public function __construct()
    {
        // Allow public access to appointment creation steps for guests.
        $publicActions = ['appointment', 'orderMenu', 'createOrder', 'saveAppointmentStep', 'confirmAppointment', 'appointmentConfirmed', 'bookAppointment'];
        $action = $_GET['action'] ?? 'index';
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'customer') {
            if (!in_array($action, $publicActions)) {
                header('Location: index.php?page=login');
                exit;
            }
        }
        
        $this->reservationsModel = new ReservationsModel();
        $this->servicesModel = new ServicesModel();
        $this->menusModel = new MenusModel();
        $this->beauticiansModel = new BeauticiansModel();
        $this->ordersModel = new OrdersModel();
    }

    public function index()
    {
        $customerId = $_SESSION['user_id'];
        $reservations = $this->reservationsModel->findByCustomerId($customerId);
        require __DIR__ . '/../Views/Customer/index.php';
    }

    public function appointment()
    {
        // Multi-step server-side wizard (no AJAX)
        $step = (int)($_GET['step'] ?? 1);

        // Load data
        $services = $this->servicesModel->findAll();
        $beauticians = $this->beauticiansModel->findAvailable();
        $draft = $_SESSION['appointment_draft'] ?? [];
        $userStage = $_SESSION['loyalty_stage'] ?? 1; // 1=Regular,2=Loyal,3=VIP
        
        // For Step 5: Load the selected service object for display
        $service = null;
        if ($step >= 5 && !empty($draft['service_id'])) {
            $service = $this->servicesModel->findById($draft['service_id']);
        }

        // Handle POST (form submissions from steps)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postedStep = (int)($_POST['step'] ?? 1);
            // Merge posted fields into draft
            foreach ($_POST as $k => $v) {
                if ($k === 'step') continue;
                $_SESSION['appointment_draft'][$k] = $v;
            }

            // If final step submitted (step 5), call confirmAppointment to finalize
            if ($postedStep >= 5) {
                return $this->confirmAppointment();
            }

            // Otherwise redirect to next step
            $next = $postedStep + 1;
            header('Location: index.php?page=customer&action=appointment&step=' . $next);
            exit;
        }

        require __DIR__ . '/../Views/Customer/appointment.php';
    }

    // NOTE: removed AJAX endpoint; wizard is now fully server-side (POST redirects between steps)

    // Finalize appointment, handle DP upload and create reservation
    public function confirmAppointment()
    {
        // Ensure POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=customer&action=appointment');
            exit;
        }

        // If user not logged in, save draft and redirect to register/login
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['appointment_draft'] = array_merge($_SESSION['appointment_draft'] ?? [], $_POST);
            $_SESSION['post_login_redirect'] = 'index.php?page=customer&action=appointment&step=5';
            $_SESSION['info'] = 'Silakan daftar atau masuk untuk menyelesaikan pembayaran DP';
            header('Location: index.php?page=register');
            exit;
        }

        $draft = $_SESSION['appointment_draft'] ?? [];
        // Merge any final POST fields
        foreach ($_POST as $k => $v) {
            $draft[$k] = $v;
        }

        $customerId = $_SESSION['user_id'];
        $serviceId = $draft['service_id'] ?? null;
        $reservationDate = $draft['reservation_date'] ?? null;
        $reservationTime = $draft['reservation_time'] ?? null;
        $beauticianId = !empty($draft['beautician_id']) ? $draft['beautician_id'] : null;
        $notes = $draft['notes'] ?? '';
        $dpAmount = (int)($draft['dp_amount'] ?? 50000);

        // Handle file upload for payment proof (optional)
        $paymentProofUrl = null;
        if (!empty($_FILES['dp_proof']['name'])) {
            $uploadDir = __DIR__ . '/../../uploads/dp_proofs/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($_FILES['dp_proof']['name']));
            $target = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['dp_proof']['tmp_name'], $target)) {
                // store relative path
                $paymentProofUrl = 'uploads/dp_proofs/' . $filename;
            }
        }

        // Create reservation record
        $resId = $this->reservationsModel->create([
            'customer_id' => $customerId,
            'service_id' => $serviceId,
            'beautician_id' => $beauticianId,
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'status' => 'Stage 1',
            'is_dp_paid' => 0,
            'dp_amount' => $dpAmount,
            'payment_proof_url' => $paymentProofUrl,
        ]);

        if ($resId) {
            unset($_SESSION['appointment_draft']);
            $_SESSION['success'] = 'Booking berhasil dibuat. Silakan tunggu verifikasi DP oleh admin.';
            header('Location: index.php?page=customer&action=appointmentConfirmed&res_id=' . $resId);
            exit;
        } else {
            $_SESSION['error'] = 'Gagal membuat booking. Silakan coba lagi.';
            header('Location: index.php?page=customer&action=appointment&step=5');
            exit;
        }
    }

    public function appointmentConfirmed()
    {
        $resId = $_GET['res_id'] ?? null;
        if (!$resId) {
            header('Location: index.php?page=customer');
            exit;
        }
        $reservation = $this->reservationsModel->findById((int)$resId);
        require __DIR__ . '/../Views/Customer/appointment_confirmed.php';
    }

    public function bookAppointment()
    {
        error_log('bookAppointment() called - POST data: ' . print_r($_POST, true));
        
        $customerId = $_SESSION['user_id'];
        $serviceId = $_POST['service_id'] ?? '';
        $beauticiansId = !empty($_POST['beautician_id']) ? (int) $_POST['beautician_id'] : null;
        $reservationDate = trim($_POST['reservation_date'] ?? '');
        $reservationTime = trim($_POST['reservation_time'] ?? '');
        $notes = $_POST['notes'] ?? '';

        error_log('bookAppointment() - customerId: ' . $customerId . ', serviceId: ' . $serviceId . ', date: ' . $reservationDate);

        if (!$serviceId || !$reservationDate || !$reservationTime) {
            $_SESSION['error'] = 'Service, tanggal, dan waktu harus diisi';
            error_log('bookAppointment() - validation failed');
            header('Location: index.php?page=customer&action=appointment');
            exit;
        }

        $service = $this->servicesModel->findById($serviceId);
        
        
        if (!$service) {
            $_SESSION['error'] = 'Service tidak ditemukan';
            error_log('bookAppointment() - service not found: ' . $serviceId);
            header('Location: index.php?page=customer&action=appointment');
            exit;
        }

        // Create reservation and get res_id
        $result = $this->reservationsModel->create([
            'customer_id' => $customerId,
            'service_id' => $serviceId,
            'beautician_id' => $beauticiansId,
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'status' => 'Pending',
        ]);

        error_log('bookAppointment() - create result: ' . $result);

        if ($result) {
            $_SESSION['success'] = 'Appointment berhasil dibuat';
            header('Location: index.php?page=customer');
            exit;
        } else {
            $_SESSION['error'] = 'Gagal membuat appointment';
            header('Location: index.php?page=customer&action=appointment');
            exit;
        }
    }

    public function orderMenu()
    {
        $menus = $this->menusModel->findAll();
        require __DIR__ . '/../Views/Customer/order_menu.php';
    }

    public function createOrder()
    {
        $menuId = $_POST['menu_id'] ?? '';
        $quantity = $_POST['quantity'] ?? 1;
        $reservationId = $_POST['reservation_id'] ?? null;
        $tableNumber = $_POST['table_number'] ?? 'ONLINE';

        if (!$menuId) {
            $_SESSION['error'] = 'Menu harus dipilih';
            header('Location: index.php?page=customer&action=orderMenu');
            exit;
        }

        $menu = $this->menusModel->findById($menuId);
        if (!$menu) {
            $_SESSION['error'] = 'Menu tidak ditemukan';
            header('Location: index.php?page=customer&action=orderMenu');
            exit;
        }

        $result = $this->ordersModel->create([
            'res_id' => $reservationId,
            'menu_id' => $menuId,
            'qty' => $quantity,
            'status' => 'New',
        ]);

        if ($result) {
            $_SESSION['success'] = 'Order berhasil dibuat';
            header('Location: index.php?page=customer');
            exit;
        } else {
            $_SESSION['error'] = 'Gagal membuat order';
            header('Location: index.php?page=customer&action=orderMenu');
            exit;
        }
    }
}
