<?php

namespace App\Controllers;

use App\Models\BeauticiansModel;
use App\Models\ReservationsModel;

class BeauticianController
{
    private BeauticiansModel $beauticiansModel;
    private ReservationsModel $reservationsModel;

    public function __construct()
    {
        // Check role
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'beautician') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->beauticiansModel = new BeauticiansModel();
        $this->reservationsModel = new ReservationsModel();
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticiansId = $beautician['id'] ?? null;

        if (!$beauticiansId) {
            $_SESSION['error'] = 'Beautician profile not found';
            header('Location: index.php?page=login');
            exit;
        }

        $todaySchedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticiansId);
        $upcomingSchedule = $this->reservationsModel->getUpcomingByBeautician($beauticiansId);

        require __DIR__ . '/../Views/Beautician/index.php';
    }

    public function todaySchedule()
    {
        $userId = $_SESSION['user_id'];
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticiansId = $beautician['id'] ?? null;

        if (!$beauticiansId) {
            $_SESSION['error'] = 'Beautician profile not found';
            header('Location: index.php?page=login');
            exit;
        }

        $schedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticiansId);
        require __DIR__ . '/../Views/Beautician/today_schedule.php';
    }

    public function upcomingSchedule()
    {
        $userId = $_SESSION['user_id'];
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticiansId = $beautician['id'] ?? null;

        if (!$beauticiansId) {
            $_SESSION['error'] = 'Beautician profile not found';
            header('Location: index.php?page=login');
            exit;
        }

        $days = $_GET['days'] ?? 30;
        $schedule = $this->reservationsModel->getUpcomingByBeautician($beauticiansId, $days);
        require __DIR__ . '/../Views/Beautician/upcoming_schedule.php';
    }

    public function updateReservationStatus()
    {
        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($reservationId > 0 && !empty($status)) {
            $this->reservationsModel->update($reservationId, ['status' => $status]);
            $_SESSION['success'] = 'Status updated';
            header('Location: index.php?page=beautician');
        } else {
            $_SESSION['error'] = 'Invalid data';
            header('Location: index.php?page=beautician');
        }
        exit;
    }
}
