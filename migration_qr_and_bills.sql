-- Migration: Add QR Token and Open Bill tables
-- This supports the anti-spam cafe ordering and unified billing system

-- Table for QR Code tokens (anti-spam)
CREATE TABLE IF NOT EXISTS `qr_tokens` (
    `token_id` INT AUTO_INCREMENT PRIMARY KEY,
    `seat_id` VARCHAR(10) NOT NULL,
    `token` VARCHAR(20) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NOT NULL,
    KEY `idx_token` (`token`),
    KEY `idx_seat_expires` (`seat_id`, `expires_at`),
    CONSTRAINT `fk_qr_tokens_seat` FOREIGN KEY (`seat_id`) REFERENCES `seats`(`seat_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Open Bills (unified billing - Salon + Cafe)
CREATE TABLE IF NOT EXISTS `open_bills` (
    `bill_id` INT AUTO_INCREMENT PRIMARY KEY,
    `res_id` INT NULL,  -- Link to reservation if from booking
    `user_id` INT NULL,  -- Nullable for guest orders
    `seat_id` VARCHAR(10) NOT NULL,
    `guest_name` VARCHAR(100),
    `bill_type` ENUM('Salon Only', 'Cafe Only', 'Salon+Cafe') DEFAULT 'Salon Only',
    `status` ENUM('Open', 'Pending Payment', 'Paid', 'Cancelled') DEFAULT 'Open',
    `salon_subtotal` DECIMAL(12, 2) DEFAULT 0,
    `cafe_subtotal` DECIMAL(12, 2) DEFAULT 0,
    `synergy_discount` DECIMAL(12, 2) DEFAULT 0,
    `total_amount` DECIMAL(12, 2) DEFAULT 0,
    `payment_method` VARCHAR(50) NULL,
    `is_dp_paid` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `closed_at` TIMESTAMP NULL,
    KEY `idx_user` (`user_id`),
    KEY `idx_seat` (`seat_id`),
    KEY `idx_res` (`res_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_open_bills_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_open_bills_seat` FOREIGN KEY (`seat_id`) REFERENCES `seats`(`seat_id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_open_bills_res` FOREIGN KEY (`res_id`) REFERENCES `reservations`(`res_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for cafe guest orders with payment tracking
CREATE TABLE IF NOT EXISTS `cafe_guest_orders` (
    `guest_order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `bill_id` INT NOT NULL,
    `seat_token` VARCHAR(20) NOT NULL,
    `guest_name` VARCHAR(100) NOT NULL,
    `order_type` ENUM('Dine-In', 'Takeaway') DEFAULT 'Dine-In',
    `seat_id` VARCHAR(10),
    `total_amount` DECIMAL(12, 2),
    `payment_method` VARCHAR(50) NULL,
    `payment_status` ENUM('Pending', 'Paid', 'Failed', 'Cancelled') DEFAULT 'Pending',
    `payment_proof_url` VARCHAR(255) NULL,
    `auto_cancel_at` TIMESTAMP NULL,  -- For 5-minute auto-cancel
    `status` ENUM('New', 'In Progress', 'Ready', 'Completed', 'Cancelled') DEFAULT 'New',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL,
    PRIMARY KEY (`guest_order_id`),
    KEY `idx_bill` (`bill_id`),
    KEY `idx_token` (`seat_token`),
    KEY `idx_status` (`status`, `payment_status`),
    CONSTRAINT `fk_cafe_guest_orders_bill` FOREIGN KEY (`bill_id`) REFERENCES `open_bills`(`bill_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trigger: Auto-cancel guest cafe orders after 5 minutes if not paid
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS `auto_cancel_guest_cafe_orders` 
BEFORE INSERT ON `cafe_guest_orders`
FOR EACH ROW
BEGIN
    SET NEW.auto_cancel_at = DATE_ADD(NOW(), INTERVAL 5 MINUTE);
END$$
DELIMITER ;
