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

        $mapping = [
            1 => ['services' => ['SV01']],
            2 => ['services' => ['SV03']],
            3 => ['services' => ['SV33']],
            4 => ['services' => ['SV52', 'SV05']],
            5 => ['services' => ['SV50', 'SV63', 'ADD-20']],
            6 => ['services' => ['SV36', 'SV19', 'ADD-16']],
            7 => ['services' => ['SV02', 'SV37', 'ADD-15']],
            8 => ['services' => ['SV49', 'SV60', 'ADD-19']]
        ];

        // Filter out any addons that are already included in the promo bundle
        $promoServices = [];
        if ($promoId && isset($mapping[$promoId])) {
            $promoServices = $mapping[$promoId]['services'] ?? [];
        }

        // Calculate addon prices (addons are services with is_addon=TRUE)
        $addonsPrice = 0;
        $addonsDetail = [];
        if (!empty($addonIds)) {
            foreach ($addonIds as $addonId) {
                if (in_array($addonId, $promoServices, true)) {
                    continue; // Skip addon as it is included in the promo for free
                }
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
        $promoItemsDetail = [];
        $fbPromoAddition = 0;

        if ($promoId) {
            $promoStmt = $this->db->prepare(
                'SELECT promo_id, promo_name, included_fb_item, discount_value FROM promotions WHERE promo_id = :id'
            );
            $promoStmt->execute([':id' => $promoId]);
            $promo = $promoStmt->fetch();
            if ($promo) {
                $discountVal = (float) $promo['discount_value'];
                $fbPromoAddition = $discountVal;
                $promoDiscount = $discountVal;

                $promoDetail = [
                    'promo_id' => $promo['promo_id'],
                    'promo_name' => $promo['promo_name'],
                    'included_fb_item' => $promo['included_fb_item'],
                    'discount_amount' => $promoDiscount
                ];

                // Determine name of the freebie
                $freebieName = $promo['included_fb_item'] ?: '';
                if (empty($freebieName)) {
                    if ($promoId === 5) $freebieName = 'Under-Eye Collagen Patches';
                    elseif ($promoId === 6) $freebieName = 'Matte Top Coat Finish';
                    elseif ($promoId === 7) $freebieName = 'Hand Paraffin Treatment';
                    elseif ($promoId === 8) $freebieName = 'Keratin Lash Boost Serum';
                    else $freebieName = 'Promo Special Freebie';
                }

                $promoItemsDetail[] = [
                    'name' => $freebieName,
                    'price' => $discountVal
                ];
            }
        }

        // Calculate totals
        $subtotal = $basePrice + $addonsPrice + $fbPromoAddition;
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
            'promo_items_detail' => $promoItemsDetail,
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
