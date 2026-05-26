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
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'beautician') {
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
        $beauticiansId = $beautician['user_id'] ?? $beautician['profile_id'] ?? null;

        if (!$beauticiansId) {
            $_SESSION['error'] = 'Beautician profile not found';
            header('Location: index.php?page=login');
            exit;
        }

        $todaySchedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticiansId);
        $upcomingSchedule = $this->reservationsModel->getUpcomingByBeautician($beauticiansId);
        $pageTitle = 'Beautician Dashboard';
        $activeMenu = 'dashboard';

        require __DIR__ . '/../Views/Beautician/index.php';
    }

    public function schedule()
    {
        $userId = $_SESSION['user_id'];
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticiansId = $beautician['user_id'] ?? $beautician['profile_id'] ?? null;

        if (!$beauticiansId) {
            $_SESSION['error'] = 'Beautician profile not found';
            header('Location: index.php?page=login');
            exit;
        }

        $todaySchedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticiansId);
        $upcomingSchedule = $this->reservationsModel->getUpcomingByBeautician($beauticiansId, 14);
        $pageTitle = 'Schedule';
        $activeMenu = 'schedule';

        require __DIR__ . '/../Views/Beautician/schedule.php';
    }

    public function treatments()
    {
        $pageTitle = 'Treatments';
        $activeMenu = 'treatments';

        $treatments = [
            [
                'name' => 'Luminous Balayage',
                'subtitle' => 'Signature blonding & gloss service',
                'duration' => '180 min',
                'status' => 'Booked',
                'note' => 'Focus on soft dimension and face-framing brightness.',
            ],
            [
                'name' => 'Classic Manicure',
                'subtitle' => 'Clean finish for daily elegance',
                'duration' => '60 min',
                'status' => 'Available',
                'note' => 'Ideal for quick refresh appointments.',
            ],
            [
                'name' => 'Lash Lift',
                'subtitle' => 'Natural curl and lift treatment',
                'duration' => '75 min',
                'status' => 'Available',
                'note' => 'Low-maintenance enhancement with long wear.',
            ],
        ];

        $materials = [
            ['name' => 'Hair Color Tube', 'qty' => '1x'],
            ['name' => 'Developer (30 Vol)', 'qty' => '50ml'],
            ['name' => 'Repair Vitamin', 'qty' => '1x'],
        ];

        require __DIR__ . '/../Views/Beautician/treatments.php';
    }

    public function achievements()
    {
        $pageTitle = 'Achievements';
        $activeMenu = 'achievements';

        $stats = [
            ['label' => 'Avg Rating', 'value' => '4.9', 'hint' => 'Client satisfaction'],
            ['label' => 'Total Clients', 'value' => '124', 'hint' => 'This quarter'],
            ['label' => 'Repeat Clients', 'value' => '86%', 'hint' => 'Loyalty score'],
        ];

        $reviews = [
            ['name' => 'Sarah Jenkins', 'tag' => 'Balayage', 'text' => 'Elena is a master of color. Highly professional and welcoming.'],
            ['name' => 'Michelle T.', 'tag' => 'Color Correction', 'text' => 'Best experience ever. The result matched exactly what I wanted.'],
        ];

        require __DIR__ . '/../Views/Beautician/achievements.php';
    }

    public function todaySchedule()
    {
        $this->schedule();
    }

    public function upcomingSchedule()
    {
        $this->schedule();
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
