<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UsersModel;
use App\Models\ReservationsModel;
use App\Models\OrdersModel;
use App\Models\TransactionsModel;

class AdminController
{
    private UsersModel $usersModel;
    private ReservationsModel $reservationsModel;
    private OrdersModel $ordersModel;
    private TransactionsModel $transactionsModel;

    public function __construct()
    {
        Auth::requireRole('admin');
        $this->usersModel = new UsersModel();
        $this->reservationsModel = new ReservationsModel();
        $this->ordersModel = new OrdersModel();
        $this->transactionsModel = new TransactionsModel();
    }

    public function index(): void
    {
        $totalCustomers = $this->usersModel->getTotalByRole('customer');
        $totalBeauticians = $this->usersModel->getTotalByRole('beautician');
        $totalReservations = count($this->reservationsModel->findAll());
        $totalRevenue = $this->transactionsModel->getTotalRevenue();

        require_once __DIR__ . '/../Views/Admin/index.php';
    }

    public function manageUsers(): void
    {
        $users = $this->usersModel->findAll();
        require_once __DIR__ . '/../Views/Admin/manage_users.php';
    }

    public function manageReservations(): void
    {
        $reservations = $this->reservationsModel->findAll();
        require_once __DIR__ . '/../Views/Admin/manage_reservations.php';
    }

    public function manageServices(): void
    {
        // Will implement service management
        require_once __DIR__ . '/../Views/Admin/manage_services.php';
    }

    public function manageMenus(): void
    {
        // Will implement menu management
        require_once __DIR__ . '/../Views/Admin/manage_menus.php';
    }

    public function manageStaff(): void
    {
        $staff = $this->usersModel->findByRole('beautician');
        require_once __DIR__ . '/../Views/Admin/manage_staff.php';
    }

    public function reports(): void
    {
        $reservations = $this->reservationsModel->findAll();
        $transactions = $this->transactionsModel->findAll();
        require_once __DIR__ . '/../Views/Admin/reports.php';
    }

    public function settings(): void
    {
        require_once __DIR__ . '/../Views/Admin/settings.php';
    }
}
