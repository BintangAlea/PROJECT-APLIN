# 📊 DATABASE ANALYSIS - MERISH BOOKING FLOW

**Status:** Gap Analysis untuk Merish 6-Step Booking Flow  
**Tanggal:** 24 May 2026

---

## 1. DATABASE SCHEMA - CURRENT STATE

### Tabel Utama Yang Ada:
1. ✅ `users` - User dengan roles
2. ✅ `services` - Salon services
3. ✅ `menus` - Cafe menu
4. ✅ `seats` - Dining seats dengan QR
5. ✅ `reservations` - Booking data
6. ✅ `reservation_details` - Service details per booking
7. ✅ `staff_profiles` - Beautician profiles
8. ✅ `orders` - Cafe orders
9. ✅ `transactions` - Payment transactions
10. ✅ `promotions` - Promo/discount
11. ✅ `inventories` - Items stock
12. ✅ `reward_catalog` - Loyalty rewards

---

## 2. GAP ANALYSIS - APA YANG KURANG UNTUK MERISH FLOW

### 🔴 CRITICAL GAPS

#### **GAP #1: Service Bundles (Untuk Step 2: Smart Suggestions)**
**Status:** ❌ TIDAK ADA

**Diperlukan untuk:**
- Bundling services (e.g., Hair + Massage + Spa)
- Bundling discounts (20% off)
- Add-on suggestions

**Table yang Perlu Ditambah:**
```sql
CREATE TABLE service_bundles (
    bundle_id INT AUTO_INCREMENT,
    bundle_name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_percentage DECIMAL(5,2) DEFAULT 0,  -- e.g., 20.00
    is_active BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (bundle_id)
);

CREATE TABLE bundle_services (
    bundle_service_id INT AUTO_INCREMENT,
    bundle_id INT NOT NULL,
    service_id VARCHAR(10) NOT NULL,
    sequence_order INT,
    PRIMARY KEY (bundle_service_id),
    FOREIGN KEY (bundle_id) REFERENCES service_bundles(bundle_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id)
);
```

---

#### **GAP #2: Service Images & Metadata (Untuk Step 1: Visual Selection)**
**Status:** ⚠️ PARTIAL

**Current Issue:**
- `services` table tidak punya `image_url`
- Tidak ada `description`
- Tidak ada `category_order` untuk sorting

**Fields Yang Perlu Ditambah ke `services` table:**
```sql
ALTER TABLE services ADD COLUMN (
    image_url VARCHAR(255) NULL,
    description TEXT NULL,
    category_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);
```

---

#### **GAP #3: Booking Add-ons Selection (Untuk Step 2)**
**Status:** ❌ TIDAK ADA

**Diperlukan untuk:**
- Track add-ons yang dipilih per booking
- Add-ons yang tidak termasuk service tapi bisa ditambahkan

**Table yang Perlu Ditambah:**
```sql
CREATE TABLE booking_addons (
    addon_id INT AUTO_INCREMENT,
    addon_name VARCHAR(100) NOT NULL,
    addon_type ENUM('Product', 'Service Extra', 'Package') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (addon_id)
);

CREATE TABLE reservation_addons (
    res_addon_id INT AUTO_INCREMENT,
    res_id INT NOT NULL,
    addon_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    PRIMARY KEY (res_addon_id),
    FOREIGN KEY (res_id) REFERENCES reservations(res_id),
    FOREIGN KEY (addon_id) REFERENCES booking_addons(addon_id)
);
```

---

#### **GAP #4: Promo Code Tracking (Untuk Step 5: Payment)**
**Status:** ⚠️ PARTIAL

**Current Issue:**
- `promotions` table tidak linked ke reservations
- Tidak bisa track promo yang digunakan per booking

**Fields Yang Perlu Ditambah ke `reservations` table:**
```sql
ALTER TABLE reservations ADD COLUMN (
    promo_id INT NULL,
    promo_discount DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (promo_id) REFERENCES promotions(promo_id)
);
```

---

#### **GAP #5: Booking Confirmation QR Code**
**Status:** ⚠️ PARTIAL

**Current Issue:**
- QR code ada di `seats` (untuk dining)
- Tapi tidak ada di `reservations` (untuk booking confirmation)

**Fields Yang Perlu Ditambah ke `reservations` table:**
```sql
ALTER TABLE reservations ADD COLUMN (
    booking_qr_code_url VARCHAR(255) NULL,
    booking_confirmation_id VARCHAR(50) NOT NULL UNIQUE,
    confirmation_date DATETIME NULL
);
```

---

#### **GAP #6: Booking Status untuk Multi-Step Flow**
**Status:** ⚠️ INCOMPLETE

**Current Status Enum:**
```
'Pending', 'Confirmed', 'In-Service', 'Selesai', 'Canceled'
```

**Perlu Ditambah untuk Step-by-Step Flow:**
```sql
-- Tambahkan status:
'Quote Pending'     -- Step 5: Awaiting payment
'Payment Pending'   -- Step 5: Waiting untuk user complete payment
'Payment Done'      -- Step 5: Payment confirmed
'Confirmed'         -- Step 6: Booking confirmed
```

**Update:**
```sql
ALTER TABLE reservations MODIFY STATUS ENUM(
    'Draft',                   -- Step 1-4: User masih booking
    'Quote Pending',           -- Step 5: Showing review/payment
    'Payment Pending',         -- Step 5: Waiting payment
    'Payment Done',            -- Step 5: Payment confirmed
    'Confirmed',               -- Step 6: Booking confirmed
    'In-Service',
    'Completed',
    'Canceled'
) DEFAULT 'Draft';
```

---

### 🟡 IMPROVEMENT OPPORTUNITIES

#### **IMPROVEMENT #1: Reservation Fields untuk Multi-Step Flow**
**Current Issue:**
- Field untuk selected add-ons tidak ada
- Field untuk bundle yang dipilih tidak ada
- Field untuk total price tidak ada

**Fields Yang Perlu Ditambah:**
```sql
ALTER TABLE reservations ADD COLUMN (
    service_bundle_id INT NULL,          -- Bundle yang dipilih
    base_price DECIMAL(12,2) DEFAULT 0,  -- Harga service
    addons_price DECIMAL(12,2) DEFAULT 0,-- Total harga add-ons
    discount_amount DECIMAL(12,2) DEFAULT 0, -- Bundling/Promo discount
    total_price DECIMAL(12,2) DEFAULT 0, -- Total price untuk review
    payment_method VARCHAR(50) NULL,     -- Payment method selected
    FOREIGN KEY (service_bundle_id) REFERENCES service_bundles(bundle_id)
);
```

---

#### **IMPROVEMENT #2: Stylist Profile Enhancement**
**Current Issue:**
- `staff_profiles` tidak punya photo
- Tidak punya rating/review score

**Fields Yang Perlu Ditambah:**
```sql
ALTER TABLE staff_profiles ADD COLUMN (
    photo_url VARCHAR(255) NULL,
    rating DECIMAL(3,2) DEFAULT 0,      -- Average rating
    review_count INT DEFAULT 0
);
```

---

#### **IMPROVEMENT #3: Better Category Management**
**Current Issue:**
- Categories hardcoded di services table
- Tidak ada table untuk categories
- Tidak bisa manage category dengan mudah

**Optional - Create categories table:**
```sql
CREATE TABLE service_categories (
    category_id INT AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    category_icon VARCHAR(50),
    display_order INT,
    image_url VARCHAR(255),
    PRIMARY KEY (category_id)
);

ALTER TABLE services ADD COLUMN category_id INT;
ALTER TABLE services ADD CONSTRAINT fk_services_category 
    FOREIGN KEY (category_id) REFERENCES service_categories(category_id);
```

---

## 3. PRIORITY FIXES

### 🔴 **TIER 1 - MUST HAVE (untuk 6-step flow berfungsi)**

| Priority | Table | Change | Impact |
|----------|-------|--------|--------|
| 1 | `services` | Add `image_url`, `description` | Step 1 UI |
| 2 | `service_bundles` + `bundle_services` | CREATE tables | Step 2 suggestions |
| 3 | `reservations` | Add `booking_qr_code_url`, `confirmation_id` | Step 6 confirmation |
| 4 | `reservations` | Add pricing fields (`base_price`, `total_price`, `discount_amount`) | Step 5 review |
| 5 | `reservations` | Update `STATUS` enum untuk multi-step | Flow tracking |
| 6 | `reservations` | Add `promo_id` linking | Step 5 payment |

### 🟡 **TIER 2 - SHOULD HAVE (untuk UX lebih baik)**

| Priority | Table | Change | Impact |
|----------|-------|--------|--------|
| 7 | `booking_addons` + `reservation_addons` | CREATE tables | Step 2 add-ons |
| 8 | `staff_profiles` | Add `photo_url`, `rating` | Step 4 UI |
| 9 | `reservations` | Add `payment_method` | Step 5 tracking |
| 10 | `service_categories` | CREATE table | Better category management |

---

## 4. MODIFIED DATABASE DIAGRAM

```
Step 1: SELECT SERVICE
├─ services (+ image_url, description)
├─ service_categories (NEW)
└─ service_bundles (NEW) → bundle_services (NEW)

Step 2: SMART SUGGESTIONS
├─ service_bundles → bundle_services
├─ booking_addons (NEW)
└─ promotions

Step 3: PICK DATE & TIME
├─ reservations
├─ staff_profiles
└─ (no changes needed)

Step 4: SELECT STYLIST
├─ staff_profiles (+ photo_url, rating)
└─ (no changes needed)

Step 4.1/4.2: LOGIN/REGISTER
├─ users
└─ (no changes needed)

Step 5: REVIEW & PAYMENT
├─ reservations (+ pricing fields, promo_id)
├─ promotions
├─ transactions
└─ reservations.STATUS = 'Quote Pending' → 'Payment Pending' → 'Payment Done'

Step 6: CONFIRMATION
├─ reservations (+ booking_qr_code_url, confirmation_id)
├─ reservation_details
├─ transactions
└─ reservations.STATUS = 'Confirmed'
```

---

## 5. IMPLEMENTATION CHECKLIST

### Database Modifications:
- [ ] Add fields ke `services` table
- [ ] Create `service_bundles` table
- [ ] Create `bundle_services` table
- [ ] Create `booking_addons` table
- [ ] Create `reservation_addons` table
- [ ] Add fields ke `reservations` table
- [ ] Update `reservations.STATUS` enum
- [ ] Add fields ke `staff_profiles` table
- [ ] Optional: Create `service_categories` table

### Backend Changes:
- [ ] Create API endpoints untuk service bundles
- [ ] Create API endpoints untuk add-ons suggestions
- [ ] Update booking creation logic
- [ ] Create pricing calculation logic
- [ ] Create QR code generation logic
- [ ] Create confirmation email logic

### Frontend Changes:
- [ ] Create public booking landing page
- [ ] Create multi-step booking form
- [ ] Create category selection with images
- [ ] Create bundle suggestions page
- [ ] Create stylist selection with profiles
- [ ] Create review & payment page
- [ ] Create confirmation page with QR

---

## 6. SUMMARY

**Database Readiness: 55%**

✅ **Sudah Ada:**
- Basic tables struktur
- Foreign key relationships
- User & booking tables
- Service & menu tables
- Transaction tracking

❌ **Perlu Ditambah (Critical):**
- Service bundles management
- Service images & descriptions  
- Booking add-ons
- Promo linking ke reservation
- Booking QR codes
- Pricing fields
- Updated status workflow

**Estimasi Work:**
- Database modifications: 1-2 hours
- Backend API updates: 3-4 hours
- Frontend implementation: 6-8 hours
- **Total: ~10-14 hours untuk full implementation**

---
