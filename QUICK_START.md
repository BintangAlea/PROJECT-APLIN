# ⚡ QUICK START GUIDE - DATABASE MIGRATION & IMPLEMENTATION

**Last Updated:** 24 May 2026

---

## 🔴 STEP 1: DATABASE MIGRATION (DO THIS FIRST!)

### 1.1 Backup Database Existing

```bash
cd C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin

# Backup database sebelum migration
mysqldump -u root -p db_merish > C:\laragon\www\SIB\PROJECT-APLIN\db_merish_backup_20260524.sql
```

### 1.2 Run Migration Script

```bash
# Login ke MySQL
mysql -u root -p

# Select database
USE db_merish;

# Run migration script
SOURCE C:/laragon/www/SIB/PROJECT-APLIN/migration_tier1_booking_flow.sql;

# Verify tables created
SHOW TABLES;

# Check new columns di services
DESCRIBE services;

# Check new columns di reservations
DESCRIBE reservations;

# Check new tables
DESCRIBE service_bundles;
DESCRIBE bundle_services;
DESCRIBE booking_addons;
DESCRIBE reservation_addons;
```

### 1.3 Verify Migration Success

```sql
-- Test: Cek semua column baru ada
SELECT * FROM services LIMIT 1;
SELECT * FROM reservations LIMIT 1;
SELECT * FROM staff_profiles LIMIT 1;

-- Test: Cek sample data
SELECT * FROM service_bundles;
SELECT * FROM booking_addons;

-- Test: Cek foreign keys
SHOW CREATE TABLE service_bundles\G
SHOW CREATE TABLE reservation_addons\G
```

✅ **If all above successful → Database migration DONE!**

---

## 📦 STEP 2: CREATE MODELS (Backend Support)

### 2.1 Create ServiceBundleModel.php

**File:** `app/Models/ServiceBundleModel.php`

```php
<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ServiceBundleModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Get all active bundles
    public function getAllActiveBundles(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM service_bundles WHERE is_active = TRUE ORDER BY bundle_name'
        );
        return $stmt->fetchAll();
    }

    // Get bundle by ID with services
    public function getBundleById(int $bundleId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM service_bundles WHERE bundle_id = :id'
        );
        $stmt->execute([':id' => $bundleId]);
        return $stmt->fetch();
    }

    // Get services in bundle
    public function getBundleServices(int $bundleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT bs.*, s.service_name, s.base_tariff, s.image_url
             FROM bundle_services bs
             JOIN services s ON bs.service_id = s.service_id
             WHERE bs.bundle_id = :bundle_id
             ORDER BY bs.sequence_order'
        );
        $stmt->execute([':bundle_id' => $bundleId]);
        return $stmt->fetchAll();
    }

    // Get bundle with total price
    public function getBundleWithPrice(int $bundleId): array|false
    {
        $bundle = $this->getBundleById($bundleId);
        if (!$bundle) return false;

        $services = $this->getBundleServices($bundleId);
        $baseTotal = array_reduce($services, fn($sum, $s) => $sum + $s['base_tariff'], 0);
        $discount = $baseTotal * ($bundle['discount_percentage'] / 100);
        $finalPrice = $baseTotal - $discount;

        return [
            ...$bundle,
            'services' => $services,
            'base_total' => $baseTotal,
            'discount_amount' => $discount,
            'final_price' => $finalPrice
        ];
    }
}
```

### 2.2 Create BookingAddonModel.php

**File:** `app/Models/BookingAddonModel.php`

```php
<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class BookingAddonModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Get all active add-ons
    public function getActiveAddons(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM booking_addons WHERE is_active = TRUE ORDER BY addon_name'
        );
        return $stmt->fetchAll();
    }

    // Get add-ons by type
    public function getAddonsByType(string $type): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM booking_addons 
             WHERE addon_type = :type AND is_active = TRUE 
             ORDER BY price DESC'
        );
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll();
    }

    // Get add-on by ID
    public function getAddonById(int $addonId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM booking_addons WHERE addon_id = :id'
        );
        $stmt->execute([':id' => $addonId]);
        return $stmt->fetch();
    }
}
```

### 2.3 Create ReservationAddonModel.php

**File:** `app/Models/ReservationAddonModel.php`

```php
<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ReservationAddonModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Add addon to reservation
    public function addAddonToReservation(int $resId, int $addonId, int $quantity = 1): bool
    {
        $addon = $this->getAddonPrice($addonId);
        if (!$addon) return false;

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

    // Get reservation addons
    public function getReservationAddons(int $resId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ra.*, ba.addon_name, ba.addon_type, ba.description
             FROM reservation_addons ra
             JOIN booking_addons ba ON ra.addon_id = ba.addon_id
             WHERE ra.res_id = :res_id'
        );
        $stmt->execute([':res_id' => $resId]);
        return $stmt->fetchAll();
    }

    // Get addon price
    private function getAddonPrice(int $addonId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT addon_id, price FROM booking_addons WHERE addon_id = :id'
        );
        $stmt->execute([':id' => $addonId]);
        return $stmt->fetch();
    }

    // Get total addon price for reservation
    public function getTotalAddonPrice(int $resId): float
    {
        $stmt = $this->db->prepare(
            'SELECT SUM(addon_price_at_booking * quantity) as total
             FROM reservation_addons
             WHERE res_id = :res_id'
        );
        $stmt->execute([':res_id' => $resId]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }
}
```

---

## 🔌 STEP 3: CREATE API ENDPOINTS

### 3.1 Update ApiBookingController.php

**Add these methods to existing ApiBookingController:**

```php
/**
 * GET /api/bundles/active
 * Get all active service bundles
 */
public function getActiveBundles()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo ApiResponse::error('Method not allowed', 405);
        return;
    }

    $bundleModel = new \App\Models\ServiceBundleModel();
    $bundles = $bundleModel->getAllActiveBundles();

    echo ApiResponse::success($bundles, 'Active bundles retrieved', 200);
}

/**
 * GET /api/bundles/:bundle_id/details
 * Get bundle details with services & pricing
 */
public function getBundleDetails($bundleId)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo ApiResponse::error('Method not allowed', 405);
        return;
    }

    $bundleModel = new \App\Models\ServiceBundleModel();
    $bundle = $bundleModel->getBundleWithPrice($bundleId);

    if (!$bundle) {
        echo ApiResponse::notFound('Bundle not found');
        return;
    }

    echo ApiResponse::success($bundle, 'Bundle details retrieved', 200);
}

/**
 * GET /api/addons/active
 * Get all active add-ons grouped by type
 */
public function getActiveAddons()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo ApiResponse::error('Method not allowed', 405);
        return;
    }

    $addonModel = new \App\Models\BookingAddonModel();
    $addons = $addonModel->getActiveAddons();

    $grouped = [];
    foreach ($addons as $addon) {
        $grouped[$addon['addon_type']][] = $addon;
    }

    echo ApiResponse::success($grouped, 'Active addons retrieved', 200);
}
```

### 3.2 Add Routes ke api.php

```php
// Add ke $routes array di api.php:

'GET:bundles/active' => [ApiBookingController::class, 'getActiveBundles'],
'GET:addons/active' => [ApiBookingController::class, 'getActiveAddons'],
```

---

## 📝 STEP 4: UPDATE api.php ROUTES

**File:** `api.php`

Add routes untuk parameterized bundle detail:

```php
// After existing beautician routes, add:

if (preg_match('/^bundles\/(\d+)\/details$/', $route, $matches)) {
    if ($method === 'GET') {
        $controller = new ApiBookingController();
        $controller->getBundleDetails($matches[1]);
        exit;
    }
}
```

---

## 🧪 STEP 5: TEST API ENDPOINTS

Open **Postman** atau **Thunder Client** dan test:

### Test 1: Get Active Bundles
```
GET http://localhost:8000/api.php/bundles/active
```

Expected response:
```json
{
  "status": "success",
  "data": [
    {
      "bundle_id": 1,
      "bundle_name": "Signature Balayage Spa",
      "discount_percentage": 20.00,
      "is_active": true
    }
  ]
}
```

### Test 2: Get Bundle Details
```
GET http://localhost:8000/api.php/bundles/1/details
```

Expected response:
```json
{
  "status": "success",
  "data": {
    "bundle_id": 1,
    "bundle_name": "Signature Balayage Spa",
    "services": [
      {
        "service_id": "SRV001",
        "service_name": "Hair Styling",
        "base_tariff": 500000
      }
    ],
    "base_total": 500000,
    "discount_amount": 100000,
    "final_price": 400000
  }
}
```

### Test 3: Get Active Add-ons
```
GET http://localhost:8000/api.php/addons/active
```

---

## ✅ VERIFICATION CHECKLIST

- [ ] Database migration ran successfully
- [ ] All new tables created (service_bundles, booking_addons, etc)
- [ ] All new columns added (image_url, pricing fields, QR fields)
- [ ] Models created (ServiceBundleModel, BookingAddonModel, ReservationAddonModel)
- [ ] API endpoints working
- [ ] Sample data visible via API

---

## 🚀 WHAT'S NEXT?

After this is done:
1. Create public booking landing page
2. Create multi-step form (step1-step6)
3. Create booking views
4. Create pricing calculation service
5. Create QR code generation

**Ready to proceed?** Confirm when steps 1-5 are complete!

---
