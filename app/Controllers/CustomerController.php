<?php

namespace App\Controllers;

use App\Core\Auth;
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
        Auth::requireRole('customer');
        $this->reservationsModel = new ReservationsModel();
        $this->servicesModel = new ServicesModel();
        $this->menusModel = new MenusModel();
        $this->beauticiansModel = new BeauticiansModel();
        $this->ordersModel = new OrdersModel();
    }

    public function index(): void
    {
        $customerId = Auth::getId();
        $reservations = $this->reservationsModel->findByCustomerId($customerId);
        require_once __DIR__ . '/../Views/Customer/index.php';
    }

    public function appointment(): void
    {
        $services = $this->servicesModel->findAll();
        $beauticians = $this->beauticiansModel->findAvailable();
        require_once __DIR__ . '/../Views/Customer/appointment.php';
    }

    public function bookAppointment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=customer/appointment');
            exit;
        }

        $customerId = Auth::getId();
        $serviceId = $_POST['service_id'] ?? '';
        $beauticiansId = $_POST['beautician_id'] ?? null;
        $reservationDate = $_POST['reservation_date'] ?? '';
        $reservationTime = $_POST['reservation_time'] ?? '';
        $notes = $_POST['notes'] ?? '';

        $error = '';
        if (empty($serviceId) || empty($reservationDate) || empty($reservationTime)) {
            $error = 'Service, tanggal, dan waktu harus diisi';
        }

        if (!$error && empty($beauticiansId)) {
            $beauticiansId = null;
        }

        if (!$error) {
            $service = $this->servicesModel->findById($serviceId);
            if (!$service) {
                $error = 'Service tidak ditemukan';
            }
        }

        if (!$error) {
            $resId = $this->reservationsModel->generateResId();
            $result = $this->reservationsModel->create([
                'res_id' => $resId,
                'customer_id' => $customerId,
                'beautician_id' => $beauticiansId,
                'service_id' => $serviceId,
                'reservation_date' => $reservationDate,
                'reservation_time' => $reservationTime,
                'duration_minutes' => $service['duration_minutes'],
                'status' => 'Stage1',
                'notes' => $notes,
            ]);

            if ($result) {
                header('Location: /SIB/PROJECT-APLIN/router.php?route=customer&success=Appointment%20berhasil%20dibuat');
                exit;
            } else {
                $error = 'Gagal membuat appointment';
            }
        }

        $services = $this->servicesModel->findAll();
        $beauticians = $this->beauticiansModel->findAvailable();
        require_once __DIR__ . '/../Views/Customer/appointment.php';
    }

    public function orderMenu(): void
    {
        $menus = $this->menusModel->getAvailable();
        require_once __DIR__ . '/../Views/Customer/order_menu.php';
    }

    public function createOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=customer/order-menu');
            exit;
        }

        $customerId = Auth::getId();
        $menuId = $_POST['menu_id'] ?? '';
        $quantity = $_POST['quantity'] ?? 1;
        $reservationId = $_POST['reservation_id'] ?? null;
        $tableNumber = $_POST['table_number'] ?? 'ONLINE';

        if (empty($menuId)) {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=customer/order-menu&error=Menu%20harus%20dipilih');
            exit;
        }

        $menu = $this->menusModel->findById($menuId);
        if (!$menu) {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=customer/order-menu&error=Menu%20tidak%20ditemukan');
            exit;
        }

        $orderId = $this->ordersModel->generateOrderId();
        $result = $this->ordersModel->create([
            'order_id' => $orderId,
            'reservation_id' => $reservationId,
            'menu_id' => $menuId,
            'quantity' => $quantity,
            'price_per_item' => $menu['price'],
            'table_number' => $tableNumber,
            'status' => 'Pending',
        ]);

        if ($result) {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=customer&success=Order%20berhasil%20dibuat');
        } else {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=customer/order-menu&error=Gagal%20membuat%20order');
        }
        exit;
    }
}
