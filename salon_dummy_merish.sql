USE db_merish_salon;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE reviews;
TRUNCATE TABLE transactions;
TRUNCATE TABLE reservation_details;
TRUNCATE TABLE reservations;
TRUNCATE TABLE promotions;
TRUNCATE TABLE staff_profiles;
TRUNCATE TABLE employee_schedules;
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
('Alina Customer', 'alina@gmail.com', '12345', 'Customer'),
('Kak Niki', 'niki@merish.com', '12345', 'Beautician'),
('Kak Ambar', 'ambar@merish.com', '12345', 'Beautician'),
('Kak Nurul', 'nurul@merish.com', '12345', 'Beautician'),
('Kak Angel', 'angel@merish.com', '12345', 'Beautician'),
('Kak Jennifer', 'jennifer@merish.com', '12345', 'Beautician'),
('Kak Winda', 'winda@merish.com', '12345', 'Beautician'),
('Kak Wendy', 'wendy@merish.com', '12345', 'Beautician'),
('Kak Nirmana', 'nirmana@merish.com', '12345', 'Beautician'),
('Kak Zeba', 'zeba@merish.com', '12345', 'Beautician'),
('Kak Zac', 'zac@merish.com', '12345', 'Beautician');

ALTER TABLE staff_profiles 
MODIFY COLUMN specialization ENUM('Barista', 'Hair Stylist', 'Lash Technician', 'Nailist', 'Wax & Eyebrows Specialist');
INSERT INTO staff_profiles (user_id, specialization, work_status) VALUES
(3, 'Hair Stylist', 'Online'),
(4, 'Barista', 'Online'),
(6, 'Nailist', 'Online'),                 -- Kak Niki
(7, 'Lash Technician', 'Online'),              -- Kak Ambar
(8, 'Lash Technician', 'Online'),              -- Kak Nurul
(9, 'Nailist', 'Online'),                      -- Kak Angel
(10, 'Nailist', 'Online'),                     -- Kak Jennifer
(11, 'Wax & Eyebrows Specialist', 'Online'),   -- Kak Winda
(12, 'Wax & Eyebrows Specialist', 'Online'),   -- Kak Wendy
(13, 'Hair Stylist', 'Online'),
(14, 'Hair Stylist', 'Online'), 
(15, 'Hair Stylist', 'Online'); 


-- 2. Master Data Layanan Salon (Utama & Add-On)
INSERT INTO services (service_id, service_name, category, is_addon, base_tariff, est_duration) VALUES
-- ==========================================
-- KATEGORI: HAIRS (Cut, Style & Treatment)
-- ==========================================
('SV01', 'Signature Balayage', 'Hair', FALSE, 850000, 240),
('SV02', 'Ladies Signature Cut & Blow', 'Hair', FALSE, 250000, 60),
('SV03', 'Editorial Manicure', 'Nails', FALSE, 350000, 60),
('SV05', 'Volume Lash Extensions', 'Lashes', FALSE, 600000, 120),
('SV06', 'Kids Haircut (Under 10 y.o)', 'Hair', FALSE, 150000, 30),
('SV07', 'Wash & Blow Dry (Standard)', 'Hair', FALSE, 120000, 45),
('SV08', 'Permanent Blow / Korean Wave', 'Hair', FALSE, 1200000, 180),
('SV09', 'Root Touch Up (Coloring Akar)', 'Hair', FALSE, 500000, 90),
('SV10', 'Full Head Coloring (Single Color)', 'Hair', FALSE, 900000, 120),
('SV11', 'Balayage / Ombre Technique', 'Hair', FALSE, 1800000, 240),
('SV12', 'Highlight / Babylights', 'Hair', FALSE, 1200000, 150),
('SV13', 'Keratin Smooth Treatment', 'Hair', FALSE, 1500000, 150),
('SV14', 'Hair Mask Repair (L''Oreal/Kerastase)', 'Hair', FALSE, 450000, 60),
('SV15', 'Scalp Detox Treatment', 'Hair', FALSE, 350000, 60),
('SV16', 'Cream Bath Traditional (+ Massage)', 'Hair', FALSE, 250000, 60),
('SV17', 'Hair Manicure / Glossing', 'Hair', FALSE, 600000, 90),
('SV18', 'Up-do / Styling Sanggul Modern', 'Hair', FALSE, 400000, 60),

-- ==========================================
-- KATEGORI: WAX (Hair Removal)
-- ==========================================
('SV19', 'Underarm Wax', 'Wax & Eyebrows', FALSE, 95000, 15),
('SV20', 'Half Arm Wax', 'Wax & Eyebrows', FALSE, 130000, 20),
('SV21', 'Full Arm Wax', 'Wax & Eyebrows', FALSE, 200000, 35),
('SV22', 'Half Leg Wax', 'Wax & Eyebrows', FALSE, 180000, 25),
('SV23', 'Full Leg Wax', 'Wax & Eyebrows', FALSE, 300000, 45),
('SV24', 'Bikini Line Wax', 'Wax & Eyebrows', FALSE, 180000, 30),
('SV25', 'Brazilian Wax (All off)', 'Wax & Eyebrows', FALSE, 350000, 45),
('SV26', 'Eyebrow Threading/Wax', 'Wax & Eyebrows', FALSE, 100000, 20),
('SV27', 'Upper Lip Wax', 'Wax & Eyebrows', FALSE, 70000, 10),
('SV28', 'Chin / Jawline Wax', 'Wax & Eyebrows', FALSE, 80000, 15),
('SV29', 'Full Face Wax', 'Wax & Eyebrows', FALSE, 250000, 40),
('SV30', 'Stomach / Chest Wax', 'Wax & Eyebrows', FALSE, 200000, 25),
('SV31', 'Back Wax (Punggung)', 'Wax & Eyebrows', FALSE, 280000, 35),
('SV32', 'Buttocks Wax', 'Wax & Eyebrows', FALSE, 200000, 20),
('SV33', 'Full Body Package', 'Wax & Eyebrows', FALSE, 1500000, 120),

-- ==========================================
-- KATEGORI: NAILS (Care & Art)
-- ==========================================
('SV34', 'Express Manicure (Cuticle & Shape)', 'Nails', FALSE, 120000, 30),
('SV35', 'Express Pedicure', 'Nails', FALSE, 135000, 35),
('SV36', 'Russian Manicure (Deep Clean)', 'Nails', FALSE, 180000, 45),
('SV37', 'Spa Pedicure (+Scrub & Massage)', 'Nails', FALSE, 250000, 60),
('SV38', 'Gel Polish Hands (Polos)', 'Nails', FALSE, 180000, 45),
('SV39', 'Gel Polish Feet (Polos)', 'Nails', FALSE, 200000, 50),
('SV40', 'Halal Breathable Polish', 'Nails', FALSE, 150000, 30),
('SV41', 'Cat Eye / Chrome Effect (Per 10 jari)', 'Nails', TRUE, 100000, 20),
('SV42', 'French Manicure (Gel)', 'Nails', FALSE, 250000, 60),
('SV43', 'Simple Nail Art (2-4 jari)', 'Nails', TRUE, 100000, 20),
('SV44', 'Premium Nail Art (Full Design)', 'Nails', FALSE, 350000, 90),
('SV45', 'Nail Extension (Fake Nails/Gel Tip)', 'Nails', FALSE, 450000, 90),
('SV46', 'Removal Gel Polish (From other salon)', 'Nails', FALSE, 50000, 20),
('SV47', 'Callus Treatment (Pembersih Kapalan)', 'Nails', FALSE, 100000, 20),
('SV48', 'Kids Manicure + Polish', 'Nails', FALSE, 100000, 30),

-- ==========================================
-- KATEGORI: LASHES & BROWS
-- ==========================================
('SV49', 'Lash Lift & Tint (Keratin)', 'Lashes', FALSE, 350000, 60),
('SV50', 'Classic Eyelash Extension', 'Lashes', FALSE, 400000, 90),
('SV51', 'Hybrid Eyelash Extension', 'Lashes', FALSE, 500000, 105),
('SV52', 'Volume Eyelash Extension (3D - 5D)', 'Lashes', FALSE, 600000, 120),
('SV53', 'Mega Volume / Russian (6D+)', 'Lashes', FALSE, 750000, 135),
('SV54', 'Douyin / Anime Style Lashes', 'Lashes', FALSE, 550000, 120),
('SV55', 'Retouch Classic (max 2 minggu)', 'Lashes', FALSE, 250000, 45),
('SV56', 'Retouch Volume (max 2 minggu)', 'Lashes', FALSE, 350000, 60),
('SV57', 'Bottom Lash Extension', 'Lashes', FALSE, 150000, 30),
('SV58', 'Lash Removal (From this salon)', 'Lashes', FALSE, 0, 20), 
('SV59', 'Lash Removal (From other salon)', 'Lashes', FALSE, 100000, 30),
('SV60', 'Brow Bomber / Lamination', 'Wax & Eyebrows', FALSE, 400000, 60),
('SV61', 'Brow Henna', 'Wax & Eyebrows', FALSE, 150000, 45),
('SV62', 'Brow Shaping & Tint', 'Wax & Eyebrows', FALSE, 200000, 45),
('SV63', 'Eyebrow Threading', 'Wax & Eyebrows', FALSE, 70000, 15);


INSERT INTO services (service_id, service_name, category, is_addon, base_tariff, est_duration) VALUES
-- ==========================================
-- KATEGORI: HAIRS (Add-ons)
-- ==========================================
('ADD-03', 'Extra Bleaching (Add-on to any service)', 'Hair', TRUE, 75000, 0),
('ADD-04', 'Shampoo Upgrade (Anti-Dandruff / Color Protect / Scalp Care)', 'Hair', TRUE, 50000, 0),
('ADD-05', 'Hair Serum / Ampoule Injection', 'Hair', TRUE, 75000, 0),
('ADD-06', 'Collagen Booster Shot (Mixed into hair color/treatment)', 'Hair', TRUE, 100000, 0),
('ADD-07', 'Extra Long / Thick Hair Surcharge', 'Hair', TRUE, 150000, 0),
('ADD-08', 'Express Scalp Massage (Extra 15 Minutes)', 'Hair', TRUE, 80000, 0),

-- ==========================================
-- KATEGORI: WAX (Add-ons)
-- ==========================================
('ADD-09', 'Soothing Hydrojelly Mask (Calming post-wax treatment)', 'Wax & Eyebrows', TRUE, 65000, 0),
('ADD-10', 'Ingrown Hair Extraction (Per area)', 'Wax & Eyebrows', TRUE, 50000, 0),
('ADD-11', 'Anti-Bump & Brightening Serum Application', 'Wax & Eyebrows', TRUE, 40000, 0),
('ADD-12', 'Sensitive Skin Hard Wax Upgrade', 'Wax & Eyebrows', TRUE, 35000, 0),
('ADD-13', 'Toes or Fingers Quick Wax (Add-on to any service)', 'Wax & Eyebrows', TRUE, 30000, 0),

-- ==========================================
-- KATEGORI: NAILS (Add-ons)
-- ==========================================
('ADD-14', 'Nail Charms / Rhinestones (Per piece)', 'Nails', TRUE, 15000, 0),
('ADD-15', 'Hand / Foot Paraffin Wax Treatment', 'Nails', TRUE, 95000, 0),
('ADD-16', 'Matte Top Coat Finish', 'Nails', TRUE, 30000, 0),
('ADD-17', 'Nail Repair (Per nail fixing for cracked/broken nails)', 'Nails', TRUE, 40000, 0),
('ADD-18', 'Overlay Gel (Extra layer for strength and durability)', 'Nails', TRUE, 75000, 0),

-- ==========================================
-- KATEGORI: BROWS & LASHES (Add-ons)
-- ==========================================
('ADD-19', 'Keratin Lash Boost Serum', 'Lashes', TRUE, 50000, 0),
('ADD-20', 'Under-Eye Collagen Patches (Applied during lash service)', 'Lashes', TRUE, 35000, 0),
('ADD-21', 'Extra Thickness / Volume Upgrade', 'Lashes', TRUE, 80000, 0),
('ADD-22', 'Lash Cleansing Foam Wash (For stubborn makeup residue)', 'Lashes', TRUE, 40000, 0),
('ADD-23', 'Lip Mask Treatment (Hydrating treatment during lash service)', 'Lashes', TRUE, 30000, 0);

INSERT INTO seats (seat_id, seat_name, zone_type, qr_code_url) VALUES
('S01', 'Kursi Salon 1', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+1'),
('S02', 'Kursi Salon 2', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+2'),
('S03', 'Kursi Salon 3', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+3'),
('S04', 'Kursi Salon 4', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+4'),
('S05', 'Kursi Salon 5', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+5'),
('S06', 'Kursi Salon 6', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+6'),
('S07', 'Kursi Salon 7', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+7'),
('S08', 'Kursi Salon 8', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+8'),
('S09', 'Kursi Salon 9', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+9'),
('S10', 'Kursi Salon 10', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+10'),
('S11', 'Kursi Salon 11', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+11'),
('S12', 'Kursi Salon 12', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+12'),
('S13', 'Kursi Salon 13', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+13'),
('S14', 'Kursi Salon 14', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+14'),
('S15', 'Kursi Salon 15', 'Kursi Salon', 'localhost:8000/index.php?page=cafe&action=cart&seat=Kursi+Salon+15');

ALTER TABLE promotions MODIFY included_fb_item VARCHAR(255) NULL;

-- 3. Baru masukkan data yang baru
INSERT INTO promotions (promo_name, included_fb_item, discount_value) 
VALUES 
('Synergy Promo : Signature Balayage + Free Aren Latte', '1x Aren Latte', 25000.00),
('Pamper Package : Editorial Manicure + Free Almond Croissant', '1x Almond Croissant', 15000.00),
('Smooth & Soothe : Full Body Wax + Free Relaxing Earl Grey Tea', '1x Relaxing Earl Grey Tea', 25000.00),
('Lash & Sip : Volume Lash Extension + Free Choco Frappe', '1x Choco Frappe', 20000.00),
('The Framing Duo : Classic Eyelash Extension + Eyebrow Threading + Free Under-Eye Collagen Patches', NULL, 35000.00),
('Sleek & Sleek Package : Russian Manicure (Gel Polish) + Underarm Wax + Free Matte Top Coat Finish', NULL, 25000.00),
('Glow Up Combo : Ladies Signature Cut & Blow + Spa Pedicure + Free Hand Paraffin Treatment', NULL, 59999.00),
('Eye Catching Promo : Lash Lift & Tint + Brow Bomber / Lamination + Free Keratin Lash Boost Serum', NULL, 45000.00);

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

INSERT INTO employee_schedules (employee_name, role, shift_start, shift_end) VALUES
('Kak Sarah', 'Senior Hair Stylist', '09:00:00', '21:00:00'),
('Kak Niki', 'Senior Nailist', '09:00:00', '21:00:00'),
('Kak Ambar', 'Senior Lash Technician', '09:00:00', '21:00:00'),
('Kak Nurul', 'Lash Technician', '09:00:00', '21:00:00'),
('Kak Angel', 'Nailist', '09:00:00', '21:00:00'),
('Kak Jennifer', 'Nailist', '09:00:00', '21:00:00'),
('Kak Winda', 'Wax & Eyebrow Specialist', '09:00:00', '21:00:00'),
('Kak Wendy', 'Wax & Eyebrow Specialist', '09:00:00', '21:00:00'),
('Kak Nirmana', 'Hair Stylist', '09:00:00', '21:00:00'),
('Kak Zeba', 'Hair Stylist', '09:00:00', '21:00:00'),
('Kak Zac', 'Hair Stylist', '09:00:00', '21:00:00');

INSERT INTO `inventories` (`id`, `item_name`, `stock_quantity`, `minimum_stock`, `unit`) VALUES
-- KATEGORI RAMBUT (HAIR CARE & STYLING)
(1, 'Shampoo Premium (Color Protect & Anti-Dandruff)', 5000.0, 1000.0, 'ML'),
(2, 'Conditioner Premium', 5000.0, 1000.0, 'ML'),
(3, 'Bleaching Powder', 2000.0, 500.0, 'Gram'),
(4, 'Developer / Peroxide Liquid', 3000.0, 1000.0, 'ML'),
(5, 'Hair Color Cream (Assorted)', 3000.0, 500.0, 'Gram'),
(6, 'Keratin Smooth Treatment Solution', 2000.0, 500.0, 'ML'),
(7, 'Hair Mask (L''Oreal / Kerastase)', 2000.0, 500.0, 'Gram'),
(8, 'Hair Serum / Ampoule', 100.0, 20.0, 'Pcs'),
(9, 'Collagen Booster Liquid', 1000.0, 200.0, 'ML'),
(10, 'Scalp Detox Scrub', 1000.0, 200.0, 'Gram'),
(11, 'Traditional Creambath Cream', 3000.0, 500.0, 'Gram'),
(12, 'Korean Wave / Perming Lotion', 1000.0, 200.0, 'ML'),
(13, 'Hair Manicure / Glossing Solution', 1000.0, 200.0, 'ML'),
(14, 'Hair Styling Spray / Gel', 1000.0, 200.0, 'ML'),

-- KATEGORI WAXING & ALIS (WAX & EYEBROWS)
(15, 'Hard Wax Beans (Sensitive Skin)', 2000.0, 500.0, 'Gram'),
(16, 'Soft Wax Cartridge / Pot', 1500.0, 300.0, 'Gram'),
(17, 'Waxing Strips (Kertas Wax)', 500.0, 100.0, 'Pcs'),
(18, 'Pre-Wax Cleanser', 1000.0, 200.0, 'ML'),
(19, 'Post-Wax Soothing Oil / Lotion', 1000.0, 200.0, 'ML'),
(20, 'Hydrojelly Mask Powder', 1000.0, 200.0, 'Gram'),
(21, 'Anti-Bump & Brightening Serum', 500.0, 100.0, 'ML'),
(22, 'Eyebrow Threading Thread', 20.0, 5.0, 'Roll'),
(23, 'Brow Henna Powder', 200.0, 50.0, 'Gram'),
(24, 'Brow Bomber / Lamination Kit', 500.0, 100.0, 'ML'),

-- KATEGORI KUKU (NAILS)
(25, 'Gel Nail Polish (Assorted Colors)', 100.0, 20.0, 'Bottle'),
(26, 'Halal Breathable Polish', 50.0, 10.0, 'Bottle'),
(27, 'Base Coat Gel', 30.0, 5.0, 'Bottle'),
(28, 'Top Coat Gel (Glossy & Matte)', 30.0, 5.0, 'Bottle'),
(29, 'Gel Overlay / Builder Gel', 500.0, 100.0, 'Gram'),
(30, 'Nail Polish Remover / Acetone', 2000.0, 500.0, 'ML'),
(31, 'Cuticle Oil', 500.0, 100.0, 'ML'),
(32, 'Nail Charms / Rhinestones', 1000.0, 200.0, 'Pcs'),
(33, 'Fake Nails / Gel Tips', 500.0, 100.0, 'Pcs'),
(34, 'Paraffin Wax Blocks', 2000.0, 500.0, 'Gram'),
(35, 'Foot Scrub / Spa Salt', 2000.0, 500.0, 'Gram'),
(36, 'Callus Treatment Gel', 1000.0, 200.0, 'ML'),

-- KATEGORI BULU MATA (LASHES)
(37, 'Classic Eyelash Trays', 50.0, 10.0, 'Pcs'),
(38, 'Volume / Mega Volume Eyelash Trays', 50.0, 10.0, 'Pcs'),
(39, 'Eyelash Adhesive / Glue', 50.0, 10.0, 'ML'),
(40, 'Lash Primer', 100.0, 20.0, 'ML'),
(41, 'Lash Cream Remover', 100.0, 20.0, 'Gram'),
(42, 'Under-Eye Collagen Patches', 200.0, 50.0, 'Pairs'),
(43, 'Keratin Lash Boost Serum', 200.0, 50.0, 'ML'),
(44, 'Lash Cleansing Foam', 500.0, 100.0, 'ML'),
(45, 'Hydrating Lip Masks', 200.0, 50.0, 'Pcs'),

-- KATEGORI UMUM (GENERAL SALON SUPPLIES)
(46, 'Cotton Pads (Kapas Wajah)', 1000.0, 200.0, 'Pcs'),
(47, 'Alcohol Swabs', 500.0, 100.0, 'Pcs'),
(48, 'Disposable Bed Sheets', 100.0, 20.0, 'Pcs'),
(49, 'Disposable Panties (For Waxing)', 100.0, 20.0, 'Pcs'),
(50, 'Hand Sanitizer', 2000.0, 500.0, 'ML');