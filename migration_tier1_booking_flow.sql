-- ===================================================================
-- DATABASE MIGRATION: MERISH BOOKING FLOW - TIER 1 CHANGES
-- ===================================================================
-- Tanggal: 24 May 2026
-- Purpose: Add fields & tables untuk mendukung 6-Step Booking Flow
-- ===================================================================

USE db_merish;

-- ===================================================================
-- STEP 1: ALTER `services` TABLE - Add Image & Description
-- ===================================================================
ALTER TABLE services ADD COLUMN (
    image_url VARCHAR(255) NULL COMMENT 'URL ke service image untuk Step 1',
    description TEXT NULL COMMENT 'Deskripsi service',
    category_order INT DEFAULT 0 COMMENT 'Sorting order per category',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Active/inactive status'
);

-- ===================================================================
-- STEP 2: CREATE SERVICE BUNDLES TABLES (For Smart Suggestions)
-- ===================================================================

CREATE TABLE service_bundles (
    bundle_id INT AUTO_INCREMENT,
    bundle_name VARCHAR(100) NOT NULL COMMENT 'Bundle name (e.g., "Hair + Massage + Spa")',
    description TEXT NULL COMMENT 'Bundle description untuk UI',
    bundle_image_url VARCHAR(255) NULL COMMENT 'Image untuk bundle',
    discount_percentage DECIMAL(5,2) DEFAULT 0 COMMENT '20.00 untuk 20% discount',
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (bundle_id)
) ENGINE=INNODB COMMENT='Paket bundling services dengan discount';

CREATE TABLE bundle_services (
    bundle_service_id INT AUTO_INCREMENT,
    bundle_id INT NOT NULL,
    service_id VARCHAR(10) NOT NULL,
    sequence_order INT DEFAULT 1 COMMENT 'Order dalam bundle',
    is_required BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (bundle_service_id),
    UNIQUE KEY unique_bundle_service (bundle_id, service_id),
    CONSTRAINT fk_bundle_services_bundle FOREIGN KEY (bundle_id) 
        REFERENCES service_bundles(bundle_id) ON DELETE CASCADE,
    CONSTRAINT fk_bundle_services_service FOREIGN KEY (service_id) 
        REFERENCES services(service_id) ON UPDATE CASCADE
) ENGINE=INNODB COMMENT='Services yang tergabung dalam bundle';

-- ===================================================================
-- STEP 2: CREATE BOOKING ADD-ONS TABLES (For Extra Options)
-- ===================================================================

CREATE TABLE booking_addons (
    addon_id INT AUTO_INCREMENT,
    addon_name VARCHAR(100) NOT NULL COMMENT 'e.g., "Premium Treatment", "Aromatherapy"',
    addon_type ENUM('Product', 'Service Extra', 'Package') NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    addon_image_url VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (addon_id)
) ENGINE=INNODB COMMENT='Add-ons yang bisa ditambahkan ke booking';

CREATE TABLE reservation_addons (
    res_addon_id INT AUTO_INCREMENT,
    res_id INT NOT NULL,
    addon_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    addon_price_at_booking DECIMAL(10,2) NOT NULL COMMENT 'Price saat di-book (untuk history)',
    PRIMARY KEY (res_addon_id),
    CONSTRAINT fk_res_addon_reservation FOREIGN KEY (res_id) 
        REFERENCES reservations(res_id) ON DELETE CASCADE,
    CONSTRAINT fk_res_addon_addon FOREIGN KEY (addon_id) 
        REFERENCES booking_addons(addon_id) ON UPDATE CASCADE
) ENGINE=INNODB COMMENT='Add-ons yang dipilih per booking';

-- ===================================================================
-- STEP 3-5: ALTER `reservations` TABLE - Add Multi-Step Flow Fields
-- ===================================================================

ALTER TABLE reservations ADD COLUMN (
    -- Step 1-4: Booking Details
    service_bundle_id INT NULL COMMENT 'Bundle yang dipilih (jika ada)',
    
    -- Step 5: Pricing & Discount
    base_price DECIMAL(12,2) DEFAULT 0 COMMENT 'Harga service dasar',
    addons_price DECIMAL(12,2) DEFAULT 0 COMMENT 'Total harga add-ons',
    bundle_discount DECIMAL(12,2) DEFAULT 0 COMMENT 'Discount dari bundle',
    promo_id INT NULL COMMENT 'Promo yang digunakan',
    promo_discount DECIMAL(12,2) DEFAULT 0 COMMENT 'Harga discount dari promo',
    total_price DECIMAL(12,2) DEFAULT 0 COMMENT 'Total harga untuk review',
    
    -- Step 5: Payment Info
    payment_method VARCHAR(50) NULL COMMENT 'Cash, Debit, QRIS, Transfer',
    
    -- Step 6: Confirmation
    booking_qr_code_url VARCHAR(255) NULL COMMENT 'QR code untuk booking confirmation',
    booking_confirmation_id VARCHAR(50) NULL UNIQUE COMMENT 'Booking ID (e.g., #MRSH-8924)',
    confirmation_date DATETIME NULL COMMENT 'Tanggal booking dikonfirmasi',
    
    -- Workflow
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add Foreign Keys untuk new columns
ALTER TABLE reservations ADD CONSTRAINT fk_reservations_bundle 
    FOREIGN KEY (service_bundle_id) REFERENCES service_bundles(bundle_id) 
    ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE reservations ADD CONSTRAINT fk_reservations_promo 
    FOREIGN KEY (promo_id) REFERENCES promotions(promo_id) 
    ON UPDATE CASCADE ON DELETE SET NULL;

-- ===================================================================
-- STEP 4: ALTER `staff_profiles` TABLE - Add Photo & Rating
-- ===================================================================

ALTER TABLE staff_profiles ADD COLUMN (
    photo_url VARCHAR(255) NULL COMMENT 'Foto beautician untuk Step 4',
    rating DECIMAL(3,2) DEFAULT 0 COMMENT 'Average rating (0-5)',
    review_count INT DEFAULT 0 COMMENT 'Jumlah reviews',
    bio TEXT NULL COMMENT 'Short bio untuk profile'
);

-- ===================================================================
-- STEP 6: UPDATE `reservations.STATUS` ENUM - Add Multi-Step States
-- ===================================================================

-- First, update existing data to compatible values
UPDATE reservations SET STATUS = 'Confirmed' WHERE STATUS = 'Confirmed';
UPDATE reservations SET STATUS = 'Draft' WHERE STATUS = 'Pending';
UPDATE reservations SET STATUS = 'Confirmed' WHERE STATUS IN ('In-Service', 'Selesai');
UPDATE reservations SET STATUS = 'Canceled' WHERE STATUS = 'Canceled';

-- Then modify the ENUM with all states
ALTER TABLE reservations MODIFY STATUS ENUM(
    'Draft',              -- Step 1-4: User masih dalam proses booking
    'Quote Pending',      -- Step 5: Menampilkan review/payment page
    'Payment Pending',    -- Step 5: Menunggu user bayar
    'Payment Done',       -- Step 5: Bayar berhasil, tunggu admin confirm
    'Confirmed',          -- Step 6: Booking confirmed + QR sent
    'In-Service',         -- Service sedang berlangsung
    'Completed',          -- Service selesai
    'Canceled'            -- Booking dibatalkan
) DEFAULT 'Draft';

-- ===================================================================
-- HELPER: CREATE INDEX untuk performance
-- ===================================================================

CREATE INDEX idx_reservations_user_status ON reservations(user_id, STATUS);
CREATE INDEX idx_reservations_beautician ON reservations(
    (SELECT beautician_id FROM reservation_details rd WHERE rd.res_id = reservations.res_id LIMIT 1)
);
CREATE INDEX idx_bundle_active ON service_bundles(is_active);
CREATE INDEX idx_addon_active ON booking_addons(is_active);
CREATE INDEX idx_service_category ON services(category, category_order);

-- ===================================================================
-- SAMPLE DATA: Untuk testing
-- ===================================================================

-- Sample: Service Bundle (Hair + Massage + Spa Package)
INSERT INTO service_bundles (bundle_name, description, discount_percentage, is_active) 
VALUES 
    ('Signature Balayage Spa', 'Hair Color + Scalp Massage + Aromatherapy Spa', 20.00, TRUE),
    ('Nails & Lashes Combo', 'Full Set Nails + Lash Extensions', 15.00, TRUE),
    ('Complete Makeover', 'Hair + Makeup + Nails + Lashes Package', 25.00, TRUE);

-- Sample: Bundle Services
INSERT INTO bundle_services (bundle_id, service_id, sequence_order, is_required) VALUES
    (1, 'SRV001', 1, TRUE),   -- Hair service
    (1, 'SRV002', 2, TRUE);   -- Massage (jika ada)

-- Sample: Add-ons
INSERT INTO booking_addons (addon_name, addon_type, description, price, is_active) VALUES
    ('Premium Treatment', 'Service Extra', 'Advanced treatment untuk hasil lebih baik', 150000, TRUE),
    ('Aromatherapy Oil', 'Product', 'Essential oil untuk relaksasi', 75000, TRUE),
    ('Express Service', 'Package', 'Priority slot (+ 100k)', 100000, TRUE),
    ('Hair Photoshoot', 'Service Extra', 'Professional photoshoot hasil styling', 200000, TRUE);

-- ===================================================================
-- MIGRATION COMPLETE
-- ===================================================================
-- ✅ Services table ditambah: image_url, description
-- ✅ Created: service_bundles, bundle_services
-- ✅ Created: booking_addons, reservation_addons
-- ✅ Reservations ditambah: pricing, bundle, promo, QR code fields
-- ✅ Staff profiles ditambah: photo, rating
-- ✅ Status enum updated untuk multi-step workflow
-- ===================================================================
