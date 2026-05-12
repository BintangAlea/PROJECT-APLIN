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
        // Check role
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
            header('Location: index.php?page=login');
            exit;
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
        $services = $this->servicesModel->findAll();
        $beauticians = $this->beauticiansModel->findAvailable();
        require __DIR__ . '/../Views/Customer/appointment.php';
    }

    public function bookAppointment()
    {
        $customerId = $_SESSION['user_id'];
        $serviceId = $_POST['service_id'] ?? '';
        $beauticiansId = $_POST['beautician_id'] ?? null;
        $reservationDate = $_POST['reservation_date'] ?? '';
        $reservationTime = $_POST['reservation_time'] ?? '';
        $notes = $_POST['notes'] ?? '';

        $error = '';
        if (!$serviceId || !$reservationDate || !$reservationTime) {
            $_SESSION['error'] = 'Service, tanggal, dan waktu harus diisi';
            header('Location: index.php?page=customer&action=appointment');
            exit;
        }

        if (empty($beauticiansId)) {
            $beauticiansId = null;
        }

        $service = $this->servicesModel->findById($serviceId);
        if (!$service) {
            $_SESSION['error'] = 'Service tidak ditemukan';
            header('Location: index.php?page=customer&action=appointment');
            exit;
        }

        $resId = $this->reservationsModel->generateResId();
        $result = $this->reservationsModel->create([
            'res_id' => $resId,
            'customer_id' => $customerId,
            'beautician_id' => $beauticiansId,
            'service_id' => $serviceId,
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'duration_minutes' => $service['duration_minutes'] ?? 60,
            'status' => 'Stage1',
            'notes' => $notes,
        ]);

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
        $customerId = $_SESSION['user_id'];
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
            'reservation_id' => $reservationId,
            'menu_id' => $menuId,
            'quantity' => $quantity,
            'price_per_item' => $menu['price'],
            'table_number' => $tableNumber,
            'status' => 'Pending',
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
