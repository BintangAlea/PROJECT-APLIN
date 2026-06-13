USE db_merish_salon;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE reviews;
TRUNCATE TABLE transactions;
TRUNCATE TABLE reservation_details;
TRUNCATE TABLE reservations;
TRUNCATE TABLE promotions;
TRUNCATE TABLE staff_profiles;
TRUNCATE TABLE seats;
TRUNCATE TABLE services;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insert Pengguna Inti
INSERT INTO users (NAME, email, PASSWORD, ROLE) VALUES
('Budi Admin', 'admin@merish.com', '12345', 'Admin'),
('Siska Resepsionis', 'resep@merish.com', '12345', 'Receptionist'),
('Kak Sarah', 'sarah@merish.com', '12345', 'Beautician'),
('Dimas Barista', 'dimas@merish.com', '12345', 'Barista'),
('Alina Customer', 'alina@gmail.com', '12345', 'Customer');

INSERT INTO staff_profiles (user_id, specialization, work_status) VALUES
(3, 'Hair Stylist', 'Online'),
(4, 'Barista', 'Online');

-- 2. Master Data Layanan Salon (Utama & Add-On)
INSERT INTO services (service_id, service_name, category, is_addon, base_tariff, est_duration) VALUES
('SV01', 'Luminous Balayage', 'Hair', FALSE, 850000.00, 180),
('SV02', 'Premium Haircut', 'Hair', FALSE, 250000.00, 60),
('SV03', 'Classic Gel Polish', 'Nails', FALSE, 150000.00, 90),
('ADD-01', 'Manik-manik (Per Pcs)', 'Nails', TRUE, 5000.00, 0),
('ADD-02', 'Top Coat Tambahan', 'Nails', TRUE, 20000.00, 0),
('ADD-03', 'Extra Cup Bleaching', 'Hair', TRUE, 75000.00, 0);

INSERT INTO seats (seat_id, seat_name, zone_type, qr_code_url) VALUES
('S01', 'Kursi Salon 1', 'Kursi Salon', 'merish.test/qr?seat=S01'),
('C01', 'Meja Kafe 1', 'Meja Kafe', 'merish.test/qr?seat=C01');

INSERT INTO promotions (promo_name, included_fb_item, discount_value) VALUES
('Synergy Promo: Balayage + Free Coffee', '1x Iced Aren Latte', 50000.00);

-- ==========================================
-- SKENARIO TRANSAKSI SALON: Pelanggan Appointment + ADD-ON
-- ==========================================
-- Alina booking Balayage dan sudah bayar DP di website
INSERT INTO reservations (user_id, seat_id, promo_id, STATUS, schedule_time, is_dp_paid, dp_amount) VALUES
(5, 'S01', 1, 'In-Service', '2026-06-10 10:00:00', TRUE, 50000.00); 

-- Layanan Utama yang dikunci sejak awal: Balayage
INSERT INTO reservation_details (res_id, service_id, beautician_id, qty, subtotal) VALUES
(1, 'SV01', 3, 1, 850000.00);

-- Resepsionis Input ADD-ON: Alina mendadak minta Extra Bleaching saat di kursi
INSERT INTO reservation_details (res_id, service_id, beautician_id, qty, subtotal) VALUES
(1, 'ADD-03', NULL, 1, 75000.00);