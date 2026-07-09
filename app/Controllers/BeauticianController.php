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

    private function getBeauticianId(): int
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $beautician = $this->beauticiansModel->findByUserId($userId);
        $beauticianId = (int) ($beautician['user_id'] ?? $beautician['profile_id'] ?? 0);

        if ($beauticianId <= 0) {
            $_SESSION['error'] = 'Beautician profile not found';
            header('Location: index.php?page=login');
            exit;
        }

        return $beauticianId;
    }

    private function renderScheduleView(int $upcomingDays, string $pageTitle): void
    {
        $beauticianId = $this->getBeauticianId();
        $todaySchedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticianId);
        $upcomingSchedule = $this->reservationsModel->getUpcomingByBeautician($beauticianId, $upcomingDays);
        $scheduleHeading = $pageTitle;
        $upcomingLabel = $upcomingDays <= 1 ? 'Next 24 Hours' : 'Next ' . $upcomingDays . ' Days';
        $activeMenu = 'schedule';

        require __DIR__ . '/../Views/Beautician/schedule.php';
    }

    private function normalizeReservationStatus(string $status): string
    {
        $normalized = trim($status);

        if ($normalized === 'Completed') {
            return 'Selesai';
        }

        return $normalized;
    }

    public function index()
    {
        $beauticianId = $this->getBeauticianId();
        $todaySchedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticianId);
        $upcomingSchedule = $this->reservationsModel->getUpcomingByBeautician($beauticianId, 14);
        $pageTitle = 'Beautician Dashboard';
        $activeMenu = 'dashboard';

        require __DIR__ . '/../Views/Beautician/index.php';
    }

    public function schedule()
    {
        $this->renderScheduleView(14, 'Schedule');
    }

    public function settings()
    {
        $beauticianId = $this->getBeauticianId();
        $beautician = $this->beauticiansModel->findByUserId((int) ($_SESSION['user_id'] ?? 0)) ?: [];
        $todaySchedule = $this->reservationsModel->getTodayScheduleByBeautician($beauticianId);
        $upcomingSchedule = $this->reservationsModel->getUpcomingByBeautician($beauticianId, 7);
        $pageTitle = 'Settings';
        $activeMenu = 'settings';

        require __DIR__ . '/../Views/Beautician/settings.php';
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
        $this->renderScheduleView(1, "Today's Schedule");
    }

    public function upcomingSchedule()
    {
        $days = max(2, (int) ($_GET['days'] ?? 14));
        $this->renderScheduleView($days, 'Weekly Schedule');
    }

    public function updateReservationStatus()
    {
        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $status = $this->normalizeReservationStatus((string) ($_POST['status'] ?? ''));

        $allowedStatuses = ['Pending', 'Confirmed', 'In-Service', 'Selesai'];
        if ($reservationId > 0 && in_array($status, $allowedStatuses, true)) {
            $this->reservationsModel->update($reservationId, ['status' => $status]);
            $_SESSION['success'] = 'Status updated';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=beautician'));
        } else {
            $_SESSION['error'] = 'Invalid data';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=beautician'));
        }
        exit;
    }
}
