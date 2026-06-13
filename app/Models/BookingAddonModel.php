<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Booking Addon Model
 * Handle add-ons untuk booking (premium treatment, products, etc)
 */
class BookingAddonModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Get all active add-ons
     */
    public function getActiveAddons(): array
    {
        try {
            $stmt = $this->db->query(
                'SELECT * FROM booking_addons WHERE is_active = TRUE ORDER BY addon_name'
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get add-ons grouped by type
     */
    public function getAddonsByType(string $type): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM booking_addons 
                 WHERE addon_type = :type AND is_active = TRUE 
                 ORDER BY price DESC'
            );
            $stmt->execute([':type' => $type]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get add-on by ID
     */
    public function getAddonById(string|int $addonId): array|false
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM booking_addons WHERE addon_id = :id'
            );
            $stmt->execute([':id' => $addonId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get add-ons grouped by type (untuk response struktur)
     */
    public function getAddonsGroupedByType(): array
    {
        $allAddons = $this->getActiveAddons();
        
        $grouped = [];
        foreach ($allAddons as $addon) {
            $type = $addon['addon_type'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $addon;
        }
        
        return $grouped;
    }

    /**
     * Get suggested add-ons (top/popular ones)
     */
    public function getSuggestedAddons(int $limit = 4): array
    {
        try {
            $stmt = $this->db->query(
                'SELECT * FROM booking_addons 
                 WHERE is_active = TRUE 
                 ORDER BY price ASC 
                 LIMIT ' . (int)$limit
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
