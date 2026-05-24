-- Add pricing columns to reservations
ALTER TABLE reservations ADD COLUMN service_bundle_id INT NULL COMMENT 'Bundle yang dipilih' AFTER status;
ALTER TABLE reservations ADD COLUMN base_price DECIMAL(12,2) DEFAULT 0 AFTER service_bundle_id;
ALTER TABLE reservations ADD COLUMN addons_price DECIMAL(12,2) DEFAULT 0 AFTER base_price;
ALTER TABLE reservations ADD COLUMN bundle_discount DECIMAL(12,2) DEFAULT 0 AFTER addons_price;
ALTER TABLE reservations ADD COLUMN promo_id INT NULL AFTER bundle_discount;
ALTER TABLE reservations ADD COLUMN promo_discount DECIMAL(12,2) DEFAULT 0 AFTER promo_id;
ALTER TABLE reservations ADD COLUMN total_price DECIMAL(12,2) DEFAULT 0 AFTER promo_discount;
ALTER TABLE reservations ADD COLUMN payment_method VARCHAR(50) NULL AFTER total_price;
ALTER TABLE reservations ADD COLUMN booking_qr_code_url VARCHAR(255) NULL AFTER payment_method;
ALTER TABLE reservations ADD COLUMN booking_confirmation_id VARCHAR(50) NULL UNIQUE AFTER booking_qr_code_url;
ALTER TABLE reservations ADD COLUMN confirmation_date DATETIME NULL AFTER booking_confirmation_id;
ALTER TABLE reservations ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER confirmation_date;
ALTER TABLE reservations ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add foreign keys
ALTER TABLE reservations ADD CONSTRAINT fk_reservations_bundle FOREIGN KEY (service_bundle_id) REFERENCES service_bundles(bundle_id) ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE reservations ADD CONSTRAINT fk_reservations_promo FOREIGN KEY (promo_id) REFERENCES promotions(promo_id) ON UPDATE CASCADE ON DELETE SET NULL;

-- Add fields to staff_profiles
ALTER TABLE staff_profiles ADD COLUMN photo_url VARCHAR(255) NULL COMMENT 'Foto beautician' AFTER hire_date;
ALTER TABLE staff_profiles ADD COLUMN rating DECIMAL(3,2) DEFAULT 0 AFTER photo_url;
ALTER TABLE staff_profiles ADD COLUMN review_count INT DEFAULT 0 AFTER rating;
ALTER TABLE staff_profiles ADD COLUMN bio TEXT NULL AFTER review_count;
