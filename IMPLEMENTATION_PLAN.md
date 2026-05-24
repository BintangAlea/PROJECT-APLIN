# 🔧 IMPLEMENTATION PLAN - MERISH 6-STEP BOOKING FLOW

**Status:** Ready for Development  
**Tanggal:** 24 May 2026  
**Priority:** TIER 1 (Critical) + TIER 2 (Should Have)

---

## 📋 PRE-IMPLEMENTATION CHECKLIST

### Database Setup:
- [ ] Backup database existing: `db_merish_backup_20260524.sql`
- [ ] Run migration script: `migration_tier1_booking_flow.sql`
- [ ] Verify all tables created
- [ ] Test foreign keys tidak error
- [ ] Insert sample data untuk testing

### Development Environment:
- [ ] Pastikan PHP 8.0+
- [ ] Composer packages updated
- [ ] VS Code REST Client installed (untuk testing API)
- [ ] Git branch created untuk feature ini

---

## 🏗️ IMPLEMENTATION PHASES

---

## **PHASE 1: Database & Backend API Setup (2-3 hours)**

### Step 1.1: Run Database Migration
```bash
# Login ke MySQL
mysql -u root -p

# Run migration
USE db_merish;
SOURCE /path/to/migration_tier1_booking_flow.sql;
```

### Step 1.2: Create API Models untuk Tables Baru

**File:** `app/Models/ServiceBundleModel.php`
- Method: `getAllBundles()`
- Method: `getBundleById($id)`
- Method: `getBundleServices($bundle_id)`

**File:** `app/Models/BookingAddonModel.php`
- Method: `getAllAddons()`
- Method: `getActiveAddons()`

**File:** `app/Models/ReservationAddonModel.php`
- Method: `addAddonToReservation($res_id, $addon_id, $qty)`
- Method: `getReservationAddons($res_id)`

### Step 1.3: Create New API Endpoints

**ApiBookingController (expand existing):**

```php
// STEP 1: Get Service Categories with Images
GET /api/services/categories
GET /api/services/by-category/:category

// STEP 2: Get Bundle Suggestions
GET /api/bundles/suggestions
GET /api/bundles/:bundle_id/details
GET /api/addons/available

// STEP 3: Time Slots (existing, keep as-is)
GET /api/time-slots?beautician_id=:id&date=:date

// STEP 4: Stylist Profiles
GET /api/stylists/available?service_id=:id&date=:date

// STEP 5: Calculate Booking Total
POST /api/booking/calculate-total
Body: {
  "service_ids": ["SRV001"],
  "bundle_id": 1,
  "addon_ids": [1, 2],
  "promo_code": "PROMO20",
  "beautician_id": 5
}

// STEP 5: Create Payment
POST /api/booking/process-payment
Body: {
  "res_id": 123,
  "payment_method": "QRIS",
  "total_amount": 1500000
}

// STEP 6: Get Booking Confirmation
GET /api/booking/:booking_confirmation_id/confirmation
```

---

## **PHASE 2: Frontend - Public Booking Page (3-4 hours)**

### Step 2.1: Create Public Booking Landing Page

**File:** `app/Views/Booking/index.php` (NEW)
- Remove login requirement - bisa diakses anonymous
- Display banner dengan booking flow
- Button "Start Booking"

**URL:** `index.php?page=booking`

### Step 2.2: Create Multi-Step Booking Form (Steps 1-6)

**Files to Create:**

```
app/Views/Booking/
├── step1-categories.php      # Category & Service Selection
├── step2-suggestions.php     # Smart Suggestions + Add-ons
├── step3-datetime.php        # Date & Time Picker
├── step4-stylist.php         # Stylist Selection
├── step4-1-login.php         # Conditional Login/Register
├── step5-review.php          # Review & Payment
└── step6-confirmation.php    # Confirmation + QR Code
```

### Step 2.3: Create Booking Controller

**File:** `app/Controllers/BookingController.php` (NEW)

```php
class BookingController {
    public function index() {
        // Show Step 1
        require __DIR__ . '/../Views/Booking/step1-categories.php';
    }
    
    public function step1() {}    // Category selection
    public function step2() {}    // Add-ons suggestions
    public function step3() {}    // Date/time
    public function step4() {}    // Stylist
    public function step4Auth() {} // Login prompt
    public function step5() {}    // Review & payment
    public function step6() {}    // Confirmation
}
```

### Step 2.4: Create Frontend JavaScript

**File:** `assets/js/booking-flow.js` (NEW)

```javascript
// Multi-step form handler
// - Store current step
// - Load/save form data
// - API calls untuk data fetching
// - Navigation (next/back)
// - Validation
```

---

## **PHASE 3: Backend Logic (2-3 hours)**

### Step 3.1: Create Booking Service Class

**File:** `app/Services/BookingService.php` (NEW)

```php
class BookingService {
    // Create draft reservation
    public function createDraftReservation($service_ids, $bundle_id, $addon_ids, $date, $time, $beautician_id)
    
    // Calculate total price
    public function calculateTotal($res_id, $promo_code)
    
    // Process payment
    public function processPayment($res_id, $payment_method)
    
    // Generate QR code & confirmation
    public function confirmBooking($res_id)
    
    // Send confirmation email
    public function sendConfirmationEmail($res_id)
}
```

### Step 3.2: Update Existing Controllers

**ApiBookingController:**
- Add new endpoints untuk bundles, add-ons, pricing calculation

**AuthController:**
- Update `login()` & `register()` untuk redirect ke booking step 6 setelah auth

### Step 3.3: Create Pricing & QR Generation Logic

**File:** `app/Services/PricingService.php` (NEW)
```php
// Calculate: base_price + addons - bundle_discount - promo_discount = total
// Handle decimal rounding
// Update reservation dengan pricing fields
```

**File:** `app/Services/QrCodeService.php` (NEW)
```php
// Generate QR code dengan booking_confirmation_id
// Save QR image
// Return URL
```

---

## **PHASE 4: UI/UX Polish (2-3 hours)**

### Step 4.1: Create Booking Styles

**File:** `assets/css/booking-flow.css` (NEW)
- Multi-step form styles
- Progress bar
- Step indicator
- Button styles
- Form validation styles

### Step 4.2: Add Image Gallery for Services

**File:** `assets/images/services/`
- Hair services images
- Nails images
- Lashes images
- Wax & Eyebrows images
- Bundle images

### Step 4.3: Create Modal Components

- Stylist profile modal
- Add-ons details modal
- Promo code info modal
- Confirmation modal

---

## **PHASE 5: Testing & Debugging (1-2 hours)**

### Testing Checklist:

- [ ] **Step 1:** User bisa pilih category & service tanpa login
- [ ] **Step 2:** Bundle suggestions tampil dengan benar
- [ ] **Step 2:** Add-ons bisa ditambahkan dengan benar
- [ ] **Step 3:** Date/time picker berfungsi
- [ ] **Step 4:** Stylist list filtered by availability
- [ ] **Step 4.1:** Login prompt tampil jika user belum auth
- [ ] **Step 5:** Total price calculated correctly (base - bundle discount - promo discount)
- [ ] **Step 5:** Payment method selection work
- [ ] **Step 6:** QR code generated
- [ ] **Step 6:** Booking confirmation email sent
- [ ] **API:** Semua endpoint return correct data
- [ ] **Database:** Data saved dengan benar di semua tables

---

## 📊 DETAILED BREAKDOWN - TIER 1 CRITICAL ITEMS

### ✅ ITEM 1: Service Image Display (Step 1)

**Requirement:**
- Services ditampilkan dengan image card (bukan dropdown)
- Grouped by category (Hair, Nails, Lashes, Wax)
- Show: Service name, description, price, duration

**Implementation:**
1. Update `services` table dengan `image_url`, `description` ✅ (Migration)
2. Create API endpoint `/api/services/by-category/:category`
3. Create frontend `step1-categories.php` dengan image cards
4. Create CSS untuk card layout

**Files to Modify:**
- `migration_tier1_booking_flow.sql` ✅
- `app/Controllers/ApiBookingController.php` (add endpoint)
- `app/Views/Booking/step1-categories.php` (create)
- `assets/css/booking-flow.css` (create)

---

### ✅ ITEM 2: Bundle/Add-ons Suggestions (Step 2)

**Requirement:**
- Show recommended bundles
- Show available add-ons
- Calculate bundle discount (e.g., 20% off)
- User bisa pilih 0 atau lebih add-ons

**Implementation:**
1. Create `service_bundles` table ✅ (Migration)
2. Create API endpoint `/api/bundles/suggestions`
3. Create API endpoint `/api/addons/available`
4. Create frontend `step2-suggestions.php` dengan bundle & addon cards
5. Add JavaScript untuk add-ons selection

**Files to Modify:**
- `migration_tier1_booking_flow.sql` ✅
- `app/Models/ServiceBundleModel.php` (create)
- `app/Models/BookingAddonModel.php` (create)
- `app/Controllers/ApiBookingController.php` (add endpoints)
- `app/Views/Booking/step2-suggestions.php` (create)
- `assets/js/booking-flow.js` (create)

---

### ✅ ITEM 3: Conditional Login at Step 4.1

**Requirement:**
- Step 1-4 bisa diakses tanpa login
- At Step 4.1, jika user belum login → show login form
- Jika sudah login → lanjut ke Step 5
- User bisa register langsung dari booking flow

**Implementation:**
1. Create BookingController tanpa login requirement
2. Create login/register embedded form (bukan redirect)
3. After auth → automatic resume booking
4. Save draft reservation untuk anonymous user

**Files to Modify:**
- `app/Controllers/BookingController.php` (create)
- `app/Views/Booking/step4-1-login.php` (create)
- `app/Controllers/AuthController.php` (add method untuk auth modal)
- `assets/js/booking-flow.js` (handle auth flow)

---

### ✅ ITEM 4: Price Calculation & Review (Step 5)

**Requirement:**
- Show: base_price + addons_price - bundle_discount - promo_discount = total
- User bisa input promo code
- Show payment method options
- Create reservation record dengan pricing

**Implementation:**
1. Add pricing fields ke `reservations` table ✅ (Migration)
2. Create `PricingService` class untuk calculation
3. Create API endpoint `/api/booking/calculate-total`
4. Create `step5-review.php` dengan pricing breakdown
5. Add promo code validation

**Files to Modify:**
- `migration_tier1_booking_flow.sql` ✅
- `app/Services/PricingService.php` (create)
- `app/Controllers/ApiBookingController.php` (add calculate endpoint)
- `app/Views/Booking/step5-review.php` (create)
- `assets/js/booking-flow.js` (promo validation)

---

### ✅ ITEM 5: QR Code Confirmation (Step 6)

**Requirement:**
- Generate unique booking confirmation ID (e.g., #MRSH-8924)
- Generate QR code
- Show booking details + QR
- Send confirmation email
- Update reservation status to 'Confirmed'

**Implementation:**
1. Add QR fields ke `reservations` table ✅ (Migration)
2. Create `QrCodeService` untuk generate QR
3. Create API endpoint `/api/booking/:id/confirm`
4. Create `step6-confirmation.php` dengan QR display
5. Add email notification

**Files to Modify:**
- `migration_tier1_booking_flow.sql` ✅
- `app/Services/QrCodeService.php` (create)
- `app/Controllers/ApiBookingController.php` (add confirm endpoint)
- `app/Views/Booking/step6-confirmation.php` (create)
- `app/Services/EmailService.php` (create/update)

---

## 📝 PRIORITY IMPLEMENTATION ORDER

### **Week 1:**
1. ✅ Database migration (already done)
2. Create Models (BundleModel, AddonModel)
3. Create ApiBookingController endpoints
4. Create BookingController

### **Week 2:**
5. Create Views (step1-step6)
6. Create JavaScript untuk multi-step form
7. Create CSS styling

### **Week 3:**
8. Create Service classes (Pricing, QR, Email)
9. Testing & debugging
10. Polish & optimization

---

## 🎯 SUCCESS METRICS

**After Implementation:**
- ✅ User dapat browse services tanpa login
- ✅ User dapat select bundle & add-ons
- ✅ User dapat pick date/time
- ✅ User dapat select stylist
- ✅ User prompted to login ONLY at step 4.1
- ✅ Booking review shows correct total price
- ✅ QR code generated & confirmation email sent
- ✅ Booking status tracked correctly (Draft → Quote Pending → Payment Done → Confirmed)

---

## 📞 NEXT STEPS

**Option 1:** Mulai dari database migration (run SQL script)
**Option 2:** Start dengan backend API endpoints  
**Option 3:** Start dengan frontend multi-step form

**Which one to start?** Recommend: Database first → Backend API → Frontend

---
