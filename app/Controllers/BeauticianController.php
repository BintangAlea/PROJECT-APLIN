<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\BeauticiansModel;
use App\Models\ReservationsModel;

class BeauticianController
{
    private BeauticiansModel $beauticiansModel;
    private ReservationsModel $reservationsModel;

    public function __construct()
    {
        Auth::requireRole('beautician');
        $this->beauticiansModel = new BeauticiansModel();
        $this->reservationsModel = new ReservationsModel();
    }

    public function index(): void
    {
        $userId = Auth::getId();
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticiansId = $beautician['id'] ?? null;

        if (!$beauticiansId) {
            die('Beautician profile not found');
        }

        $todaySchedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticiansId);
        $upcomingSchedule = $this->reservationsModel->getUpcomingByBeautician($beauticiansId);

        require_once __DIR__ . '/../Views/Beautician/index.php';
    }

    public function todaySchedule(): void
    {
        $userId = Auth::getId();
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticiansId = $beautician['id'] ?? null;

        if (!$beauticiansId) {
            die('Beautician profile not found');
        }

        $schedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticiansId);
        require_once __DIR__ . '/../Views/Beautician/today_schedule.php';
    }

    public function upcomingSchedule(): void
    {
        $userId = Auth::getId();
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticiansId = $beautician['id'] ?? null;

        if (!$beauticiansId) {
            die('Beautician profile not found');
        }

        $days = $_GET['days'] ?? 30;
        $schedule = $this->reservationsModel->getUpcomingByBeautician($beauticiansId, $days);
        require_once __DIR__ . '/../Views/Beautician/upcoming_schedule.php';
    }

    public function updateReservationStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=beautician');
            exit;
        }

        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($reservationId > 0 && !empty($status)) {
            $this->reservationsModel->update($reservationId, ['status' => $status]);
            header('Location: /SIB/PROJECT-APLIN/router.php?route=beautician&success=Status%20updated');
        } else {
            header('Location: /SIB/PROJECT-APLIN/router.php?route=beautician&error=Invalid%20data');
        }
        exit;
    }
}
