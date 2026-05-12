<?php

namespace App\Controllers;

use App\Models\ReservationsModel;
use App\Models\OrdersModel;

class ReceptionistController
{
    private ReservationsModel $reservationsModel;
    private OrdersModel $ordersModel;

    public function __construct()
    {
        // Check role
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'receptionist') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->reservationsModel = new ReservationsModel();
        $this->ordersModel = new OrdersModel();
    }

    public function index()
    {
        $today = date('Y-m-d');
        $reservations = $this->reservationsModel->findByDate($today);
        $pendingOrders = $this->ordersModel->findByStatus('Pending');
        require __DIR__ . '/../Views/Receptionist/index.php';
    }

    public function scheduleBooking()
    {
        $reservations = $this->reservationsModel->findAll();
        require __DIR__ . '/../Views/Receptionist/schedule_booking.php';
    }

    public function viewReservations()
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $reservations = $this->reservationsModel->findByDate($date);
        require __DIR__ . '/../Views/Receptionist/view_reservations.php';
    }

    public function updateReservationStatus()
    {
        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($reservationId > 0 && !empty($status)) {
            $this->reservationsModel->update($reservationId, ['status' => $status]);
            $_SESSION['success'] = 'Reservation status updated';
        }

        header('Location: index.php?page=receptionist');
        exit;
    }

    public function viewOrders()
    {
        $orders = $this->ordersModel->findAll();
        require __DIR__ . '/../Views/Receptionist/view_orders.php';
    }

    public function checkIn()
    {
        require __DIR__ . '/../Views/Receptionist/check_in.php';
    }
}
