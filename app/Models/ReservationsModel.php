<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ReservationsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM reservations');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE res_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByCustomerId(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT r.res_id,
                   r.user_id,
                   r.seat_id,
                   r.STATUS AS status,
                   r.schedule_time,
                   DATE(r.schedule_time) AS reservation_date,
                   TIME(r.schedule_time) AS reservation_time,
                   rd.service_id,
                   s.service_name,
                   u.NAME as beautician_name,
                   rd.beautician_id
            FROM reservations r
            LEFT JOIN reservation_details rd ON r.res_id = rd.res_id
            LEFT JOIN services s ON rd.service_id = s.service_id
            LEFT JOIN users u ON rd.beautician_id = u.user_id
            WHERE r.user_id = :user_id
            ORDER BY r.schedule_time DESC
        ');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findByDate(string $date): array
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE DATE(schedule_time) = :date ORDER BY schedule_time');
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll();
    }

    public function getTodayScheduleByBeautician(int $beauticianId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.res_id,
                    r.STATUS AS status,
                    r.schedule_time,
                    DATE(r.schedule_time) AS reservation_date,
                    TIME(r.schedule_time) AS reservation_time,
                    u.NAME AS customer_name,
                    s.service_name,
                    s.category,
                    se.seat_name
             FROM reservations r
             JOIN reservation_details rd ON r.res_id = rd.res_id
             JOIN users u ON r.user_id = u.user_id
             LEFT JOIN services s ON rd.service_id = s.service_id
             LEFT JOIN seats se ON r.seat_id = se.seat_id
             WHERE rd.beautician_id = :beautician_id
               AND DATE(r.schedule_time) = CURDATE()
             ORDER BY r.schedule_time ASC'
        );
        $stmt->execute([':beautician_id' => $beauticianId]);
        return $stmt->fetchAll();
    }

        public function getScheduleByBeauticianAndDate(int $beauticianId, string $date): array
        {
                $stmt = $this->db->prepare(
                        'SELECT r.res_id,
                                        r.STATUS AS status,
                                        r.schedule_time,
                                        DATE(r.schedule_time) AS reservation_date,
                                        TIME(r.schedule_time) AS reservation_time,
                                        u.NAME AS customer_name,
                                        s.service_name,
                                        s.category,
                                        se.seat_name
                         FROM reservations r
                         JOIN reservation_details rd ON r.res_id = rd.res_id
                         JOIN users u ON r.user_id = u.user_id
                         LEFT JOIN services s ON rd.service_id = s.service_id
                         LEFT JOIN seats se ON r.seat_id = se.seat_id
                         WHERE rd.beautician_id = :beautician_id
                             AND DATE(r.schedule_time) = :date
                         ORDER BY r.schedule_time ASC'
                );
                $stmt->bindValue(':beautician_id', $beauticianId, PDO::PARAM_INT);
                $stmt->bindValue(':date', $date);
                $stmt->execute();
                return $stmt->fetchAll();
        }

    public function getUpcomingByBeautician(int $beauticianId, int $days = 7): array
    {
        $endDate = date('Y-m-d', strtotime('+' . max(1, $days) . ' days'));
        $stmt = $this->db->prepare(
            'SELECT r.res_id,
                    r.STATUS AS status,
                    r.schedule_time,
                    DATE(r.schedule_time) AS reservation_date,
                    TIME(r.schedule_time) AS reservation_time,
                    u.NAME AS customer_name,
                    s.service_name,
                    s.category,
                    se.seat_name
             FROM reservations r
             JOIN reservation_details rd ON r.res_id = rd.res_id
             JOIN users u ON r.user_id = u.user_id
             LEFT JOIN services s ON rd.service_id = s.service_id
             LEFT JOIN seats se ON r.seat_id = se.seat_id
             WHERE rd.beautician_id = :beautician_id
                             AND DATE(r.schedule_time) BETWEEN CURDATE() AND :end_date
             ORDER BY r.schedule_time ASC'
        );
        $stmt->bindValue(':beautician_id', $beauticianId, PDO::PARAM_INT);
                $stmt->bindValue(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function generateResId(): string
    {
        $timestamp = time();
        $random = mt_rand(100, 999);
        return 'RES' . $timestamp . $random;
    }

    public function create(array $data): bool|int
    {
        try {
            // Validate required fields
            $customerId = $data['customer_id'] ?? $data['user_id'] ?? null;
            $reservationDate = $data['reservation_date'] ?? null;
            $reservationTime = $data['reservation_time'] ?? null;
            $serviceId = $data['service_id'] ?? null;
            $beauticianId = $data['beautician_id'] ?? null;

            if (!$reservationDate || !$reservationTime) {
                error_log('ReservationsModel.create() - Validation failed: missing required fields');
                return false;
            }

            if (empty($beauticianId) || $beauticianId === '') {
                $beauticianId = null;
            } else {
                $beauticianStmt = $this->db->prepare('SELECT user_id FROM staff_profiles WHERE profile_id = :profile_id OR user_id = :user_id LIMIT 1');
                $beauticianStmt->execute([
                    ':profile_id' => $beauticianId,
                    ':user_id' => $beauticianId,
                ]);
                $beauticianRow = $beauticianStmt->fetch();
                if ($beauticianRow && !empty($beauticianRow['user_id'])) {
                    $beauticianId = (int) $beauticianRow['user_id'];
                } else {
                    $beauticianId = null;
                }
            }

            $seatId = trim((string) ($data['seat_id'] ?? ''));
            if ($seatId !== '') {
                $seatStmt = $this->db->prepare('SELECT seat_id FROM seats WHERE seat_id = :seat_id AND zone_type = :zone_type LIMIT 1');
                $seatStmt->execute([
                    ':seat_id' => $seatId,
                    ':zone_type' => 'Kursi Salon',
                ]);
                $seatRow = $seatStmt->fetch();
                if (!$seatRow) {
                    error_log('ReservationsModel.create() - Invalid seat_id for salon reservation: ' . $seatId);
                    return false;
                }
            } else {
                $seatStmt = $this->db->prepare('SELECT seat_id FROM seats WHERE zone_type = :zone_type ORDER BY seat_id ASC LIMIT 1');
                $seatStmt->execute([':zone_type' => 'Kursi Salon']);
                $seatRow = $seatStmt->fetch();
                $seatId = $seatRow['seat_id'] ?? null;
            }

            if (empty($seatId)) {
                error_log('ReservationsModel.create() - No salon seat available');
                return false;
            }

            // Calculate service duration
            $durationMinutes = 60;
            $serviceIdsCheck = $data['service_ids'] ?? [];
            if (!empty($serviceId) && !in_array($serviceId, $serviceIdsCheck)) {
                $serviceIdsCheck[] = $serviceId;
            }
            if (!empty($serviceIdsCheck)) {
                $placeholders = implode(',', array_fill(0, count($serviceIdsCheck), '?'));
                $dStmt = $this->db->prepare("SELECT SUM(COALESCE(est_duration, 0)) FROM services WHERE service_id IN ($placeholders)");
                $dStmt->execute($serviceIdsCheck);
                $sumDuration = (int) $dStmt->fetchColumn();
                if ($sumDuration > 0) {
                    $durationMinutes = $sumDuration;
                }
            }

            $scheduleTime = date('Y-m-d H:i:s', strtotime($reservationDate . ' ' . $reservationTime));
            $newEnd = date('Y-m-d H:i:s', strtotime($scheduleTime) + ($durationMinutes * 60));

            // Check seat overlap
            $overlapSeatStmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM reservations r
                WHERE r.seat_id = :seat_id
                  AND r.status IN ('Pending', 'Confirmed', 'In-Service')
                  AND :new_start < DATE_ADD(r.schedule_time, INTERVAL (
                      SELECT COALESCE(SUM(s.est_duration), 60) 
                      FROM reservation_details rd 
                      JOIN services s ON rd.service_id = s.service_id 
                      WHERE rd.res_id = r.res_id
                  ) MINUTE)
                  AND :new_end > r.schedule_time
            ");
            $overlapSeatStmt->execute([
                ':seat_id' => $seatId,
                ':new_start' => $scheduleTime,
                ':new_end' => $newEnd
            ]);
            if ($overlapSeatStmt->fetchColumn() > 0) {
                error_log("ReservationsModel.create() - Seat overlap detected for seat: " . $seatId);
                return false;
            }

            // Check staff (beautician) overlap
            if ($beauticianId !== null) {
                $overlapStaffStmt = $this->db->prepare("
                    SELECT COUNT(*) 
                    FROM reservations r
                    JOIN reservation_details rd ON r.res_id = rd.res_id
                    WHERE rd.beautician_id = :beautician_id
                      AND r.status IN ('Pending', 'Confirmed', 'In-Service')
                      AND :new_start < DATE_ADD(r.schedule_time, INTERVAL (
                          SELECT COALESCE(SUM(s.est_duration), 60) 
                          FROM reservation_details rd2 
                          JOIN services s ON rd2.service_id = s.service_id 
                          WHERE rd2.res_id = r.res_id
                      ) MINUTE)
                      AND :new_end > r.schedule_time
                ");
                $overlapStaffStmt->execute([
                    ':beautician_id' => $beauticianId,
                    ':new_start' => $scheduleTime,
                    ':new_end' => $newEnd
                ]);
                if ($overlapStaffStmt->fetchColumn() > 0) {
                    error_log("ReservationsModel.create() - Staff overlap detected for beautician: " . $beauticianId);
                    return false;
                }
            }
            
            $stmt = $this->db->prepare('
                INSERT INTO reservations (user_id, seat_id, promo_id, STATUS, schedule_time, is_dp_paid, dp_amount, payment_proof_url)
                VALUES (:user_id, :seat_id, :promo_id, :status, :schedule_time, :is_dp_paid, :dp_amount, :payment_proof_url)
            ');

            $params = [
                ':user_id' => $customerId,
                ':seat_id' => $seatId,
                ':promo_id' => $data['promo_id'] ?? null,
                ':status' => $data['status'] ?? 'Pending',
                ':schedule_time' => $scheduleTime,
                ':is_dp_paid' => $data['is_dp_paid'] ?? 0,
                ':dp_amount' => $data['dp_amount'] ?? 0,
                ':payment_proof_url' => $data['payment_proof_url'] ?? null,
            ];
            
            $success = $stmt->execute($params);

            if (!$success) {
                $error = $stmt->errorInfo();
                error_log('ReservationsModel.create() - Insert reservations failed: ' . print_r($error, true));
                return false;
            }

            // Get the last inserted res_id using SQL instead of PDO method
            $resIdStmt = $this->db->query('SELECT LAST_INSERT_ID() as id');
            $resIdRow = $resIdStmt->fetch();
            $resId = $resIdRow['id'] ?? 0;
            
            error_log('ReservationsModel.create() - LAST_INSERT_ID: ' . $resId);

            // Insert into reservation_details (service_ids and beautician_id)
            $serviceIds = $data['service_ids'] ?? [];
            if (!empty($serviceId) && !in_array($serviceId, $serviceIds)) {
                $serviceIds[] = $serviceId;
            }

            if (!empty($serviceIds)) {
                foreach ($serviceIds as $srvId) {
                    error_log('ReservationsModel.create() - Inserting detail: resId=' . $resId . ', serviceId=' . $srvId);
                    $stmtDetail = $this->db->prepare('
                        INSERT INTO reservation_details (res_id, service_id, beautician_id, qty, subtotal)
                        VALUES (:res_id, :service_id, :beautician_id, :qty, :subtotal)
                    ');

                    // Get service price for subtotal
                    $priceStmt = $this->db->prepare('SELECT base_tariff FROM services WHERE service_id = :sid');
                    $priceStmt->execute([':sid' => $srvId]);
                    $priceRow = $priceStmt->fetch();
                    $unitPrice = $priceRow['base_tariff'] ?? 0;

                    $detailSuccess = $stmtDetail->execute([
                        ':res_id' => $resId,
                        ':service_id' => $srvId,
                        ':beautician_id' => $beauticianId,
                        ':qty' => 1,
                        ':subtotal' => $unitPrice,
                    ]);
                    
                    if (!$detailSuccess) {
                        $error = $stmtDetail->errorInfo();
                        error_log('ReservationsModel.create() - Insert detail FAILED: ' . print_r($error, true));
                    } else {
                        error_log('ReservationsModel.create() - Detail insert SUCCESS for serviceId=' . $srvId);
                    }
                }
            }

            error_log('ReservationsModel.create() - SUCCESS: resId=' . $resId);
            return $resId;
        } catch (\Exception $e) {
            error_log('ReservationsModel.create() exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        $updates = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $stmt = $this->db->prepare('UPDATE reservations SET ' . implode(', ', $updates) . ' WHERE res_id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM reservations WHERE res_id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
