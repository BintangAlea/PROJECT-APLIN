<?php

namespace App\Controllers;

use App\Core\ApiResponse;
use App\Core\Database;

/**
 * API Booking Filter Controller
 * Handle booking queries with filters for:
 * 1. Online beauticians
 * 2. Available time slots
 * 3. Service-based filtering
 */
class ApiBookingController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        header('Content-Type: application/json');
    }

    /**
     * GET /api/beauticians/online
     * Get all online beauticians
     * 
     * Query params:
     * - specialization: Hair Stylist, Nailist, etc
     * - page: pagination
     * - limit: items per page
     */
    public function getOnlineBeauticians()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $specialization = $_GET['specialization'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;

        // Build query
        $where = "sp.work_status = 'Online'";
        $params = [];

        if ($specialization) {
            $where .= " AND sp.specialization = :specialization";
            $params[':specialization'] = $specialization;
        }

        // Count total
        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) as count 
             FROM staff_profiles sp
             JOIN users u ON sp.user_id = u.user_id
             WHERE {$where}"
        );
        $countStmt->execute($params);
        $total = $countStmt->fetch()['count'];

        // Get beauticians
        $stmt = $this->db->prepare(
            "SELECT sp.profile_id, u.user_id, u.NAME as name, u.email, 
                    sp.specialization, sp.work_status, sp.hire_date
             FROM staff_profiles sp
             JOIN users u ON sp.user_id = u.user_id
             WHERE {$where}
             ORDER BY u.NAME ASC
             LIMIT :limit OFFSET :offset"
        );

        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;
        $stmt->execute($params);
        $beauticians = $stmt->fetchAll();

        echo ApiResponse::paginated(
            $beauticians,
            $total,
            $page,
            $limit,
            'Online beauticians retrieved'
        );
    }

    /**
     * GET /api/beauticians/:user_id
     * Get beautician profile with availability
     */
    public function getBeautician($userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $stmt = $this->db->prepare(
            'SELECT sp.profile_id, u.user_id, u.NAME as name, u.email, 
                    sp.specialization, sp.work_status, sp.hire_date
             FROM staff_profiles sp
             JOIN users u ON sp.user_id = u.user_id
             WHERE u.user_id = :user_id'
        );
        $stmt->execute([':user_id' => $userId]);
        $beautician = $stmt->fetch();

        if (!$beautician) {
            echo ApiResponse::notFound('Beautician not found');
            return;
        }

        echo ApiResponse::success($beautician, 'Beautician profile retrieved', 200);
    }

    /**
     * GET /api/time-slots
     * Get available time slots for a beautician on a specific date
     * 
     * Query params:
     * - beautician_id: user_id
     * - date: YYYY-MM-DD
     */
    public function getTimeSlots()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $beauticianId = $_GET['beautician_id'] ?? null;
        $date = $_GET['date'] ?? null;

        if (!$beauticianId) {
            echo ApiResponse::validationError(['beautician_id' => 'Beautician ID required']);
            return;
        }

        if (!$date) {
            echo ApiResponse::validationError(['date' => 'Date required (YYYY-MM-DD)']);
            return;
        }

        // Verify beautician exists and is online
        $bStmt = $this->db->prepare(
            'SELECT * FROM staff_profiles WHERE user_id = :user_id AND work_status = :status'
        );
        $bStmt->execute([
            ':user_id' => $beauticianId,
            ':status' => 'Online'
        ]);
        $beautician = $bStmt->fetch();

        if (!$beautician) {
            echo ApiResponse::error('Beautician not found or not online', 404);
            return;
        }

        // Get booked slots for this beautician on this date
        $booked = $this->db->prepare(
            'SELECT schedule_time FROM reservations 
             WHERE schedule_time LIKE :date_pattern
             AND res_id IN (
               SELECT res_id FROM reservation_details WHERE beautician_id = :beautician_id
             )'
        );
        $booked->execute([
            ':date_pattern' => $date . '%',
            ':beautician_id' => $beauticianId
        ]);
        $bookedTimes = $booked->fetchAll(\PDO::FETCH_COLUMN);

        // Generate available slots (every 30 minutes, 9AM to 6PM)
        $availableSlots = [];
        $start = strtotime($date . ' 09:00');
        $end = strtotime($date . ' 18:00');
        $interval = 30 * 60; // 30 minutes

        for ($time = $start; $time < $end; $time += $interval) {
            $timeStr = date('Y-m-d H:i:s', $time);
            
            // Check if booked
            $isBooked = in_array($timeStr, $bookedTimes);
            
            $availableSlots[] = [
                'time' => $timeStr,
                'available' => !$isBooked,
                'formatted_time' => date('H:i', $time)
            ];
        }

        echo ApiResponse::success([
            'beautician_id' => $beauticianId,
            'date' => $date,
            'available_slots' => $availableSlots,
            'total_slots' => count($availableSlots),
            'available_count' => count(array_filter($availableSlots, fn($s) => $s['available']))
        ], 'Time slots retrieved', 200);
    }

    /**
     * PUT /api/beauticians/:user_id/status
     * Update beautician status (Online/Offline)
     * 
     * Request body:
     * {
     *   "work_status": "Online" OR "Offline"
     * }
     */
    public function updateBeauticianStatus($userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['work_status'])) {
            echo ApiResponse::validationError(['work_status' => 'Status required']);
            return;
        }

        if (!in_array($input['work_status'], ['Online', 'Offline'])) {
            echo ApiResponse::error('Invalid status. Must be Online or Offline', 400);
            return;
        }

        try {
            $stmt = $this->db->prepare(
                'UPDATE staff_profiles SET work_status = :status WHERE user_id = :user_id'
            );
            $stmt->execute([
                ':status' => $input['work_status'],
                ':user_id' => $userId
            ]);

            echo ApiResponse::success([
                'user_id' => $userId,
                'work_status' => $input['work_status']
            ], 'Beautician status updated', 200);
        } catch (\Exception $e) {
            echo ApiResponse::error('Failed to update status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/specializations
     * Get available specializations
     */
    public function getSpecializations()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $specializations = [
            'Hair Stylist',
            'Nailist',
            'Lash Technician',
            'Wax & Threading Specialist',
            'Barista'
        ];

        echo ApiResponse::success($specializations, 'Specializations retrieved', 200);
    }
}
