<?php

namespace App\Core;

use App\Models\ServiceBundleModel;
use App\Models\ReservationAddonModel;
use App\Models\BookingAddonModel;

/**
 * Pricing Service
 * Handle all pricing calculations untuk booking
 */
class PricingService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Calculate total price untuk reservation
     * 
     * @param int $serviceId - Service yang dipilih (jika tidak bundle)
     * @param int|null $bundleId - Bundle ID (jika ada)
     * @param array $addonIds - Array of addon IDs
     * @param string|null $promoCode - Promo code (optional)
     * @return array Pricing breakdown
     */
    public function calculateTotal(
        ?int $serviceId = null,
        ?int $bundleId = null,
        array $addonIds = [],
        ?string $promoCode = null
    ): array {
        $basePrice = 0;
        $description = '';

        // Get base price dari bundle atau service
        if ($bundleId) {
            $bundleModel = new ServiceBundleModel();
            $bundle = $bundleModel->getBundleWithPrice($bundleId);
            if ($bundle) {
                $basePrice = $bundle['final_price'];
                $description = "Bundle: {$bundle['bundle_name']}";
            }
        } else if ($serviceId) {
            $serviceStmt = $this->db->prepare(
                'SELECT base_tariff FROM services WHERE service_id = :id'
            );
            $serviceStmt->execute([':id' => $serviceId]);
            $service = $serviceStmt->fetch();
            if ($service) {
                $basePrice = $service['base_tariff'];
                $serviceNameStmt = $this->db->prepare(
                    'SELECT service_name FROM services WHERE service_id = :id'
                );
                $serviceNameStmt->execute([':id' => $serviceId]);
                $serviceName = $serviceNameStmt->fetch()['service_name'];
                $description = "Service: {$serviceName}";
            }
        }

        // Calculate addon prices
        $addonsPrice = 0;
        $addonsDetail = [];
        if (!empty($addonIds)) {
            $addonModel = new BookingAddonModel();
            foreach ($addonIds as $addonId) {
                $addon = $addonModel->getAddonById($addonId);
                if ($addon) {
                    $addonsPrice += $addon['price'];
                    $addonsDetail[] = [
                        'addon_id' => $addon['addon_id'],
                        'addon_name' => $addon['addon_name'],
                        'price' => $addon['price']
                    ];
                }
            }
        }

        // Get promo discount
        $promoDiscount = 0;
        $promoId = null;
        $promoDetail = null;

        if ($promoCode) {
            $promoDiscount = $this->validateAndGetPromoDiscount($promoCode, $basePrice + $addonsPrice);
            if ($promoDiscount > 0) {
                $promoDetail = [
                    'code' => $promoCode,
                    'discount_amount' => $promoDiscount
                ];
                // Get promo ID
                $promoStmt = $this->db->prepare(
                    'SELECT promo_id FROM promotions WHERE promo_code = :code'
                );
                $promoStmt->execute([':code' => $promoCode]);
                $promo = $promoStmt->fetch();
                if ($promo) {
                    $promoId = $promo['promo_id'];
                }
            }
        }

        // Calculate totals
        $subtotal = $basePrice + $addonsPrice;
        $totalDiscount = $promoDiscount;
        $totalPrice = $subtotal - $totalDiscount;

        return [
            'base_price' => (float)$basePrice,
            'addons_price' => (float)$addonsPrice,
            'addons_detail' => $addonsDetail,
            'subtotal' => (float)$subtotal,
            'promo_discount' => (float)$promoDiscount,
            'promo_id' => $promoId,
            'promo_detail' => $promoDetail,
            'total_discount' => (float)$totalDiscount,
            'total_price' => (float)$totalPrice,
            'description' => $description,
            'is_valid' => $totalPrice > 0
        ];
    }

    /**
     * Validate promo code dan get discount amount
     */
    private function validateAndGetPromoDiscount(string $code, float $amount): float
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM promotions 
             WHERE promo_code = :code 
             AND is_active = TRUE 
             AND start_date <= NOW() 
             AND end_date >= NOW()'
        );
        $stmt->execute([':code' => $code]);
        $promo = $stmt->fetch();

        if (!$promo) {
            return 0;
        }

        // Calculate discount
        if ($promo['discount_type'] === 'percentage') {
            $discount = $amount * ($promo['discount_value'] / 100);
        } else {
            $discount = (float)$promo['discount_value'];
        }

        // Cap discount to not exceed subtotal
        return min($discount, $amount);
    }

    /**
     * Update reservation dengan pricing info
     */
    public function updateReservationPricing(
        int $resId,
        array $pricing,
        ?string $paymentMethod = null
    ): bool {
        $stmt = $this->db->prepare(
            'UPDATE reservations 
             SET base_price = :base_price,
                 addons_price = :addons_price,
                 promo_id = :promo_id,
                 promo_discount = :promo_discount,
                 total_price = :total_price,
                 payment_method = :payment_method,
                 updated_at = NOW()
             WHERE res_id = :res_id'
        );

        return $stmt->execute([
            ':base_price' => $pricing['base_price'],
            ':addons_price' => $pricing['addons_price'],
            ':promo_id' => $pricing['promo_id'],
            ':promo_discount' => $pricing['promo_discount'],
            ':total_price' => $pricing['total_price'],
            ':payment_method' => $paymentMethod,
            ':res_id' => $resId
        ]);
    }

    /**
     * Validate pricing (safety check)
     */
    public function validatePricing(array $pricing): bool
    {
        return isset($pricing['total_price']) && 
               $pricing['total_price'] > 0 &&
               $pricing['is_valid'];
    }
}
