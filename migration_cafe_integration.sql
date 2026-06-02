-- ============================================================================
-- CAFE INTEGRATION MIGRATION
-- Integrate Cafe Orders into Unified Billing System with Salon Services
-- ============================================================================

-- 1. Pastikan orders table memiliki res_id (sudah ada) dan field untuk zone tracking
-- Jika belum, tambahkan:
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 2. Buat tabel untuk Open Bill (tracking tagihan active)
CREATE TABLE IF NOT EXISTS `open_bills` (
    `bill_id` INT AUTO_INCREMENT,
    `res_id` INT NULL,  -- Linked reservation (jika ada)
    `guest_name` VARCHAR(100) NOT NULL DEFAULT 'Guest',
    `zone_type` ENUM('Salon Only', 'Cafe Only', 'Salon + Cafe') NOT NULL DEFAULT 'Salon + Cafe',
    `main_seat_id` VARCHAR(10) NULL,  -- Kursi salon utama (jika pelanggan salon)
    `companion_seat_id` VARCHAR(10) NULL,  -- Meja cafe pendamping (jika ada)
    `bill_status` ENUM('Open', 'Ready for Checkout', 'Closed') NOT NULL DEFAULT 'Open',
    `subtotal_salon` DECIMAL(12,2) DEFAULT 0,
    `subtotal_cafe` DECIMAL(12,2) DEFAULT 0,
    `synergy_discount` DECIMAL(12,2) DEFAULT 0,
    `tax_amount` DECIMAL(12,2) DEFAULT 0,
    `total_due` DECIMAL(12,2) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `closed_at` DATETIME NULL,
    PRIMARY KEY (`bill_id`),
    UNIQUE KEY `uk_open_bills_res_id` (`res_id`),
    CONSTRAINT `fk_open_bills_res` FOREIGN KEY (`res_id`) REFERENCES `reservations`(`res_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_open_bills_main_seat` FOREIGN KEY (`main_seat_id`) REFERENCES `seats`(`seat_id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_open_bills_companion_seat` FOREIGN KEY (`companion_seat_id`) REFERENCES `seats`(`seat_id`) ON UPDATE CASCADE
) ENGINE=INNODB;

-- 3. Buat tabel untuk QR Token (untuk scan QR Cafe)
CREATE TABLE IF NOT EXISTS `qr_tokens` (
    `token_id` INT AUTO_INCREMENT,
    `seat_id` VARCHAR(10) NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL UNIQUE,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,  -- 7 hari validity
    PRIMARY KEY (`token_id`),
    CONSTRAINT `fk_qr_tokens_seat` FOREIGN KEY (`seat_id`) REFERENCES `seats`(`seat_id`) ON UPDATE CASCADE
) ENGINE=INNODB;

-- 4. Pastikan orders table mempunyai relasi ke bill (jika belum existing)
ALTER TABLE `orders` MODIFY COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `orders` ADD COLUMN `bill_id` INT NULL AFTER `updated_at`;
ALTER TABLE `orders` ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `bill_id`;

-- Add indexes for bill tracking
ALTER TABLE `orders` ADD INDEX `idx_orders_bill_id` (`bill_id`);
ALTER TABLE `orders` ADD INDEX `idx_orders_res_id` (`res_id`);
ALTER TABLE `orders` ADD INDEX `idx_orders_seat_id` (`seat_id`);

-- Add foreign key constraint
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_bill` FOREIGN KEY (`bill_id`) REFERENCES `open_bills`(`bill_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 5. Create view untuk Unified Billing (otomatis konsolidasi salon + cafe)
CREATE OR REPLACE VIEW `vw_unified_bill` AS
SELECT 
    b.bill_id,
    b.res_id,
    b.guest_name,
    b.zone_type,
    b.main_seat_id,
    b.companion_seat_id,
    COALESCE(r.schedule_time, NOW()) as created_time,
    
    -- Salon charges
    COALESCE((
        SELECT SUM(s.base_tariff)
        FROM reservation_details rd
        JOIN services s ON s.service_id = rd.service_id
        WHERE rd.res_id = b.res_id
    ), 0) AS salon_total,
    
    -- Cafe charges
    COALESCE((
        SELECT SUM(o.qty * m.price)
        FROM orders o
        JOIN menus m ON m.menu_id = o.menu_id
        WHERE o.bill_id = b.bill_id AND o.payment_status = 'Unpaid'
    ), 0) AS cafe_total,
    
    -- Synergy discount (20% if both salon + cafe)
    CASE 
        WHEN COALESCE((
            SELECT SUM(s.base_tariff)
            FROM reservation_details rd
            JOIN services s ON s.service_id = rd.service_id
            WHERE rd.res_id = b.res_id
        ), 0) > 0
        AND COALESCE((
            SELECT SUM(o.qty * m.price)
            FROM orders o
            JOIN menus m ON m.menu_id = o.menu_id
            WHERE o.bill_id = b.bill_id AND o.payment_status = 'Unpaid'
        ), 0) > 0
        THEN COALESCE((
            SELECT SUM(o.qty * m.price)
            FROM orders o
            JOIN menus m ON m.menu_id = o.menu_id
            WHERE o.bill_id = b.bill_id AND o.payment_status = 'Unpaid'
        ), 0) * 0.20
        ELSE 0
    END AS synergy_discount,
    
    b.bill_status,
    b.created_at,
    b.updated_at
FROM open_bills b
LEFT JOIN reservations r ON b.res_id = r.res_id;

-- 6. Index optimization
CREATE INDEX IF NOT EXISTS `idx_open_bills_res_id` ON `open_bills`(`res_id`);
CREATE INDEX IF NOT EXISTS `idx_open_bills_main_seat` ON `open_bills`(`main_seat_id`);
CREATE INDEX IF NOT EXISTS `idx_open_bills_companion_seat` ON `open_bills`(`companion_seat_id`);
CREATE INDEX IF NOT EXISTS `idx_qr_tokens_seat_id` ON `qr_tokens`(`seat_id`);
CREATE INDEX IF NOT EXISTS `idx_qr_tokens_token_hash` ON `qr_tokens`(`token_hash`);

-- ============================================================================
-- DATA SAMPLE (Optional - untuk testing)
-- ============================================================================
-- Insert sample QR tokens for each cafe seat
-- (Uncomment if needed for testing)
/*
INSERT INTO qr_tokens (seat_id, token_hash, expires_at)
SELECT 
    s.seat_id,
    SHA2(CONCAT(s.seat_id, '_', RAND(), '_', UNIX_TIMESTAMP()), 256),
    DATE_ADD(NOW(), INTERVAL 7 DAY)
FROM seats s
WHERE s.zone_type = 'Relaxation Lounge'
ON DUPLICATE KEY UPDATE expires_at = DATE_ADD(NOW(), INTERVAL 7 DAY);
*/

-- ============================================================================
-- NOTES
-- ============================================================================
-- 1. When receptionist check-in customer:
--    - Create open_bill with res_id + main_seat_id
--
-- 2. When customer scans QR (cafe):
--    - Link order to bill_id (not res_id directly)
--    - If no bill exists for seat → create standalone cafe-only bill
--
-- 3. If customer with companion:
--    - open_bill has both main_seat_id + companion_seat_id
--    - Companion's cafe orders → linked to same bill_id
--    - No need for payment, included in main bill
--
-- 4. Unified Checkout:
--    - Query vw_unified_bill by res_id or bill_id
--    - Shows salon_total + cafe_total + synergy_discount
--    - One payment transaction closes bill
