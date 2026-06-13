<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Seat Model
 * Manage seat/table mapping, zones, and QR code URLs
 */
class SeatModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Get all seats
     */
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM seats ORDER BY seat_id ASC');
        return $stmt->fetchAll();
    }

    /**
     * Get seat by ID
     */
    public function findById(string $seatId): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM seats WHERE seat_id = :seat_id');
        $stmt->execute([':seat_id' => $seatId]);
        return $stmt->fetch();
    }

    /**
     * Get seats by zone type
     */
    public function findByZone(string $zoneType): array
    {
        $stmt = $this->db->prepare('SELECT * FROM seats WHERE zone_type = :zone_type ORDER BY seat_id ASC');
        $stmt->execute([':zone_type' => $zoneType]);
        return $stmt->fetchAll();
    }

    /**
     * Get all zones
     */
    public function getAllZones(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT zone_type FROM seats ORDER BY zone_type ASC');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get seat availability (check if seat is currently occupied)
     */
    public function isOccupied(string $seatId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as count FROM reservations 
             WHERE seat_id = :seat_id 
             AND STATUS IN ("Confirmed", "In-Service", "Waiting")
             AND schedule_time > NOW()'
        );
        $stmt->execute([':seat_id' => $seatId]);
        $result = $stmt->fetch();
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Create new seat
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO seats (seat_id, seat_name, zone_type, qr_code_url)
             VALUES (:seat_id, :seat_name, :zone_type, :qr_code_url)'
        );

        return $stmt->execute([
            ':seat_id' => $data['seat_id'] ?? '',
            ':seat_name' => $data['seat_name'] ?? '',
            ':zone_type' => $data['zone_type'] ?? 'Kursi Salon',
            ':qr_code_url' => $data['qr_code_url'] ?? ''
        ]);
    }

    /**
     * Update seat
     */
    public function update(string $seatId, array $data): bool
    {
        $fields = [];
        $values = [':seat_id' => $seatId];

        foreach ($data as $key => $value) {
            if (in_array($key, ['seat_name', 'zone_type', 'qr_code_url'])) {
                $fields[] = "$key = :$key";
                $values[":$key"] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE seats SET ' . implode(', ', $fields) . ' WHERE seat_id = :seat_id');
        return $stmt->execute($values);
    }

    /**
     * Delete seat
     */
    public function delete(string $seatId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM seats WHERE seat_id = :seat_id');
        return $stmt->execute([':seat_id' => $seatId]);
    }

    /**
     * Get seat with current reservation info
     */
    public function getSeatWithCurrentReservation(string $seatId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, r.res_id, r.user_id, u.name AS guest_name, r.STATUS, r.schedule_time
             FROM seats s
             LEFT JOIN reservations r ON s.seat_id = r.seat_id 
               AND r.STATUS IN ("Confirmed", "In-Service", "Waiting")
               AND r.schedule_time > NOW()
             LEFT JOIN users u ON r.user_id = u.user_id
             WHERE s.seat_id = :seat_id'
        );
        $stmt->execute([':seat_id' => $seatId]);
        return $stmt->fetch();
    }

    /**
     * Get all seats with current reservation status (for seat map)
     */
    public function getAllSeatsWithStatus(): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.seat_id, s.seat_name, s.zone_type,
                    r.res_id, r.user_id, u.name AS guest_name, r.STATUS, r.schedule_time,
                    COUNT(o.order_id) as cafe_order_count
             FROM seats s
             LEFT JOIN reservations r ON s.seat_id = r.seat_id 
               AND r.STATUS IN ("Confirmed", "In-Service", "Waiting")
               AND r.schedule_time > NOW()
             LEFT JOIN users u ON r.user_id = u.user_id
             LEFT JOIN db_merish_cafe.orders o ON o.seat_id = s.seat_id AND o.STATUS = "In Progress"
             GROUP BY s.seat_id
             ORDER BY s.zone_type, s.seat_id'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
