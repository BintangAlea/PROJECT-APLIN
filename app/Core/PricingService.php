<?php

namespace App\Core;

use App\Core\Database;
use PDO;

/**
 * Pricing Service
 * Handle all pricing calculations untuk booking
 * Works with original schema: salon_merish_db.sql
 */
class PricingService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Calculate total price untuk reservation
     *
     * @param string|int|null $serviceId - Service utama
     * @param array $addonIds - Array of addon service IDs (is_addon=TRUE)
     * @param int|null $promoId - Promo ID (optional)
     * @return array Pricing breakdown
     */
    public function calculateTotal(
        array|string|int|null $services = null,
        array $addonIds = [],
        ?int $promoId = null
    ): array {
        $basePrice = 0;
        $description = '';
        $servicesDetail = [];

        // Normalize services to array
        $serviceIds = [];
        if (!empty($services)) {
            if (is_array($services)) {
                $serviceIds = $services;
            } else {
                $serviceIds = [$services];
            }
        }

        // Get base price dari service(s)
        if (!empty($serviceIds)) {
            $descNames = [];
            foreach ($serviceIds as $sid) {
                $stmt = $this->db->prepare(
                    'SELECT service_name, base_tariff FROM services WHERE service_id = :id'
                );
                $stmt->execute([':id' => $sid]);
                $service = $stmt->fetch();
                if ($service) {
                    $price = (float) $service['base_tariff'];
                    $basePrice += $price;
                    $descNames[] = $service['service_name'];
                    $servicesDetail[] = [
                        'service_id' => $sid,
                        'service_name' => $service['service_name'],
                        'price' => $price
                    ];
                }
            }
            if (!empty($descNames)) {
                $description = "Services: " . implode(', ', $descNames);
            }
        }

        // Calculate addon prices (addons are services with is_addon=TRUE)
        $addonsPrice = 0;
        $addonsDetail = [];
        if (!empty($addonIds)) {
            foreach ($addonIds as $addonId) {
                $addonStmt = $this->db->prepare(
                    'SELECT service_id, service_name, base_tariff FROM services WHERE service_id = :id AND is_addon = TRUE'
                );
                $addonStmt->execute([':id' => $addonId]);
                $addon = $addonStmt->fetch();
                if ($addon) {
                    $addonPrice = (float) $addon['base_tariff'];
                    $addonsPrice += $addonPrice;
                    $addonsDetail[] = [
                        'addon_id' => $addon['service_id'],
                        'addon_name' => $addon['service_name'],
                        'price' => $addonPrice
                    ];
                }
            }
        }

        // Get promo discount (fixed amount from promotions.discount_value)
        $promoDiscount = 0;
        $promoDetail = null;

        if ($promoId) {
            $promoStmt = $this->db->prepare(
                'SELECT promo_id, promo_name, included_fb_item, discount_value FROM promotions WHERE promo_id = :id'
            );
            $promoStmt->execute([':id' => $promoId]);
            $promo = $promoStmt->fetch();
            if ($promo) {
                $promoDiscount = min((float) $promo['discount_value'], $basePrice + $addonsPrice);
                $promoDetail = [
                    'promo_id' => $promo['promo_id'],
                    'promo_name' => $promo['promo_name'],
                    'included_fb_item' => $promo['included_fb_item'],
                    'discount_amount' => $promoDiscount
                ];
            }
        }

        // Calculate totals
        $subtotal = $basePrice + $addonsPrice;
        $totalPrice = max(0, $subtotal - $promoDiscount);

        return [
            'base_price' => $basePrice,
            'services_detail' => $servicesDetail,
            'addons_price' => (float) $addonsPrice,
            'addons_detail' => $addonsDetail,
            'subtotal' => (float) $subtotal,
            'promo_discount' => (float) $promoDiscount,
            'promo_id' => $promoId,
            'promo_detail' => $promoDetail,
            'total_price' => (float) $totalPrice,
            'description' => $description,
            'is_valid' => $totalPrice >= 0
        ];
    }

    /**
     * Validate pricing (safety check)
     */
    public function validatePricing(array $pricing): bool
    {
        return isset($pricing['total_price']) &&
               $pricing['total_price'] >= 0 &&
               $pricing['is_valid'];
    }
}
