<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Service Bundle Model
 * Handle bundling services dengan discount
 */
class ServiceBundleModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Get all active bundles
     */
    public function getAllActiveBundles(): array
    {
        try {
            $stmt = $this->db->query(
                'SELECT * FROM service_bundles WHERE is_active = TRUE ORDER BY bundle_name'
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get bundle by ID
     */
    public function getBundleById(int $bundleId): array|false
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM service_bundles WHERE bundle_id = :id'
            );
            $stmt->execute([':id' => $bundleId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get services dalam bundle
     */
    public function getBundleServices(int $bundleId): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT bs.*, s.service_name, s.base_tariff, s.image_url, s.description, s.est_duration
                 FROM bundle_services bs
                 JOIN services s ON bs.service_id = s.service_id
                 WHERE bs.bundle_id = :bundle_id AND s.is_active = TRUE
                 ORDER BY bs.sequence_order'
            );
            $stmt->execute([':bundle_id' => $bundleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get bundle dengan pricing detail
     */
    public function getBundleWithPrice(int $bundleId): array|false
    {
        $bundle = $this->getBundleById($bundleId);
        if (!$bundle) return false;

        $services = $this->getBundleServices($bundleId);
        
        // Calculate totals
        $baseTotal = 0;
        $totalDuration = 0;
        
        foreach ($services as $service) {
            $baseTotal += $service['base_tariff'];
            $totalDuration += $service['est_duration'] ?? 0;
        }

        $discountAmount = $baseTotal * ($bundle['discount_percentage'] / 100);
        $finalPrice = $baseTotal - $discountAmount;

        return [
            ...$bundle,
            'services' => $services,
            'service_count' => count($services),
            'base_total' => (float)$baseTotal,
            'discount_amount' => (float)$discountAmount,
            'discount_percentage' => (float)$bundle['discount_percentage'],
            'final_price' => (float)$finalPrice,
            'total_duration' => $totalDuration
        ];
    }

    /**
     * Get bundles untuk suggestion (dengan limit)
     */
    public function getSuggestedBundles(int $limit = 3): array
    {
        $bundles = $this->getAllActiveBundles();
        
        $suggested = [];
        foreach (array_slice($bundles, 0, $limit) as $bundle) {
            $suggested[] = $this->getBundleWithPrice($bundle['bundle_id']);
        }
        
        return $suggested;
    }
}
