<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ReservationsModel;
use App\Models\OrdersModel;

class ReceptionistController
{
    private ReservationsModel $reservationsModel;
    private OrdersModel $ordersModel;

    public function __construct()
    {
        Auth::requireRole('receptionist');
        $this->reservationsModel = new ReservationsModel();
        $this->ordersModel = new OrdersModel();
    }

    public function index(): void
    {
        $today = date('Y-m-d');
        $reservations = $this->reservationsModel->findByDate($today);
        $pendingOrders = $this->ordersModel->findByStatus('Pending');
        require_once __DIR__ . '/../Views/Receptionist/index.php';
    }

    public function scheduleBooking(): void
    {
        $reservations = $this->reservationsModel->findAll();
        require_once __DIR__ . '/../Views/Receptionist/schedule_booking.php';
    }

    public function viewReservations(): void
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $reservations = $this->reservationsModel->findByDate($date);
        require_once __DIR__ . '/../Views/Receptionist/view_reservations.php';
    }

    public function updateReservationStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=receptionist');
            exit;
        }

        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($reservationId > 0 && !empty($status)) {
            $this->reservationsModel->update($reservationId, ['status' => $status]);
        }

        header('Location: /SIB/PROJECT-APLIN/router.php?route=receptionist&success=Reservation%20status%20updated');
        exit;
    }

    public function viewOrders(): void
    {
        $orders = $this->ordersModel->findAll();
        require_once __DIR__ . '/../Views/Receptionist/view_orders.php';
    }

    public function checkIn(): void
    {
        require_once __DIR__ . '/../Views/Receptionist/check_in.php';
    }
}
