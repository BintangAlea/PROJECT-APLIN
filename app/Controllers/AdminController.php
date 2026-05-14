<?php

namespace App\Controllers;

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
        // Check role
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->usersModel = new UsersModel();
        $this->reservationsModel = new ReservationsModel();
        $this->ordersModel = new OrdersModel();
        $this->transactionsModel = new TransactionsModel();
    }

    public function index()
    {
        $totalCustomers = $this->usersModel->getTotalByRole('customer');
        $totalBeauticians = $this->usersModel->getTotalByRole('beautician');
        $totalReservations = count($this->reservationsModel->findAll());
        $totalRevenue = $this->transactionsModel->getTotalRevenue();

        require __DIR__ . '/../Views/Admin/index.php';
    }

    public function manageUsers()
    {
        $users = $this->usersModel->findAll();
        require __DIR__ . '/../Views/Admin/manage_users.php';
    }

    public function manageReservations()
    {
        $reservations = $this->reservationsModel->findAll();
        require __DIR__ . '/../Views/Admin/manage_reservations.php';
    }

    public function manageServices()
    {
        require __DIR__ . '/../Views/Admin/manage_services.php';
    }

    public function manageMenus()
    {
        require __DIR__ . '/../Views/Admin/manage_menus.php';
    }

    public function manageStaff()
    {
        $staff = $this->usersModel->findByRole('beautician');
        require __DIR__ . '/../Views/Admin/manage_staff.php';
    }

    public function reports()
    {
        $reservations = $this->reservationsModel->findAll();
        $transactions = $this->transactionsModel->findAll();
        require __DIR__ . '/../Views/Admin/reports.php';
    }

    public function settings()
    {
        require __DIR__ . '/../Views/Admin/settings.php';
    }
}
