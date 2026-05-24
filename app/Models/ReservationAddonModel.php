<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Reservation Addon Model
 * Track add-ons yang dipilih untuk setiap reservation
 */
class ReservationAddonModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Add addon to reservation
     */
    public function addAddonToReservation(int $resId, int $addonId, int $quantity = 1): bool
    {
        // Get addon price
        $addonModel = new BookingAddonModel();
        $addon = $addonModel->getAddonById($addonId);
        
        if (!$addon) {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO reservation_addons (res_id, addon_id, quantity, addon_price_at_booking)
             VALUES (:res_id, :addon_id, :qty, :price)'
        );
        
        return $stmt->execute([
            ':res_id' => $resId,
            ':addon_id' => $addonId,
            ':qty' => $quantity,
            ':price' => $addon['price']
        ]);
    }

    /**
     * Get all addons for a reservation
     */
    public function getReservationAddons(int $resId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ra.*, ba.addon_name, ba.addon_type, ba.description, ba.addon_image_url
             FROM reservation_addons ra
             JOIN booking_addons ba ON ra.addon_id = ba.addon_id
             WHERE ra.res_id = :res_id
             ORDER BY ra.res_addon_id'
        );
        $stmt->execute([':res_id' => $resId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total addon price for a reservation
     */
    public function getTotalAddonPrice(int $resId): float
    {
        $stmt = $this->db->prepare(
            'SELECT SUM(addon_price_at_booking * quantity) as total
             FROM reservation_addons
             WHERE res_id = :res_id'
        );
        $stmt->execute([':res_id' => $resId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }

    /**
     * Remove addon from reservation
     */
    public function removeAddonFromReservation(int $resAddonId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM reservation_addons WHERE res_addon_id = :id'
        );
        return $stmt->execute([':id' => $resAddonId]);
    }

    /**
     * Remove all addons from reservation
     */
    public function removeAllAddons(int $resId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM reservation_addons WHERE res_id = :res_id'
        );
        return $stmt->execute([':res_id' => $resId]);
    }

    /**
     * Get addon detail by addon ID
     */
    public function getAddonDetail(int $addonId, int $quantity = 1): array|false
    {
        $addonModel = new BookingAddonModel();
        $addon = $addonModel->getAddonById($addonId);
        
        if (!$addon) return false;

        return [
            ...$addon,
            'quantity' => $quantity,
            'subtotal' => $addon['price'] * $quantity
        ];
    }
}
