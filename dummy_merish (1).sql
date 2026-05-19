-- Matikan dulu pengecekan relasi biar ga error pas dihapus
SET FOREIGN_KEY_CHECKS = 0;

-- Kosongkan isi tabel dan reset ID kembali ke 1
TRUNCATE TABLE staff_profiles;
TRUNCATE TABLE users;
TRUNCATE TABLE menus;
TRUNCATE TABLE seats;
TRUNCATE TABLE reward_catalog;

-- Nyalakan lagi pengecekan relasinya (SANGAT PENTING!)
SET FOREIGN_KEY_CHECKS = 1;


INSERT INTO users (NAME, email, PASSWORD, ROLE, loyalty_stage, total_spent, reward_points) VALUES
('Budi Admin', 'budi.admin@gmail.com', '12345', 'Admin', 1, 0, 0),
('Siska Resepsionis', 'siska.resep@gmail.com', '12345', 'Receptionist', 1, 0, 0),
('Kak Sarah', 'sarah.hair@gmail.com', '12345', 'Beautician', 1, 0, 0),
('Kak Rina', 'rina.nails@gmail.com', '12345', 'Beautician', 1, 0, 0),
('Dimas Barista', 'dimas.barista@gmail.com', '12345', 'Barista', 1, 0, 0),
('Kak Nita', 'nita.lash@gmail.com', '12345', 'Beautician', 1, 0, 0),
('Kak Tika', 'tika.wax@gmail.com', '12345', 'Beautician', 1, 0, 0),
('Alina Customer', 'alina.customer@gmail.com', '12345', 'Customer', 2, 850000, 150);

INSERT INTO staff_profiles (user_id, specialization, work_status, hire_date) VALUES
(3, 'Hair Stylist', 'Online', '2026-01-10'),
(4, 'Nailist', 'Online', '2026-02-15'),
(5, 'Barista', 'Online', '2026-03-01'),
(6, 'Lash Technician', 'Online', '2026-02-15'),
(7, 'Wax & Threading Specialist', 'Online', '2026-02-15');

INSERT INTO menus (menu_id, menu_name, price, is_available, bom_recipe_id) VALUES
('M001', 'Americano', 20000.00, TRUE, NULL),
('M002', 'Aren Latte', 25000.00, TRUE, NULL),
('M003', 'Matcha Latte', 28000.00, TRUE, NULL),
('M004', 'Choco Frappe', 30000.00, TRUE, NULL),
('M005', 'Original Croissant', 15000.00, TRUE, NULL),
('M006', 'Choco Bun', 18000.00, TRUE, NULL),
('M007', 'Choco Cheese Croissant', 22000.00, TRUE, NULL);

INSERT INTO seats (seat_id, seat_name, zone_type, qr_code_url) VALUES
('S01', 'Kursi Salon 1', 'Active Area', 'merish.test/order?seat=S01'),
('S02', 'Kursi Salon 2', 'Active Area', 'merish.test/order?seat=S02'),
('S03', 'Kursi Salon 3', 'Active Area', 'merish.test/order?seat=S03'),
('C01', 'Meja Kafe 1', 'Relaxation Lounge', 'merish.test/order?seat=C01'),
('C02', 'Meja Kafe 2', 'Relaxation Lounge', 'merish.test/order?seat=C02');

INSERT INTO reward_catalog (reward_name, points_required, reward_type, stock) VALUES
('Free Iced Aren Latte', 50, 'F&B Tier', 100),
('Makarizo Hair Mask 50ml', 100, 'Retail Product', 20),
('Cashback Rp 50.000', 150, 'Salon Service/Cashback', 999);

-- 1. Kita buat 1 layanan dulu sebagai syarat promo
INSERT INTO services (service_id, service_name, category, base_tariff, est_duration) VALUES
('SV01', 'Luminous Balayage', 'Hair', 850000.00, 180);

-- 2. Baru kita masukkan dummy promo bundling-nya
INSERT INTO promotions (promo_name, service_id_req, menu_id_req, discount_value) VALUES
('Synergy Promo: Balayage + Aren Latte', 'SV01', 'M002', 50000.00),
('Santai Nunggu: All Services + Croissant', NULL, 'M005', 10000.00);

-- ==========================================
-- TAHAP 6: Melengkapi Katalog Layanan Salon (Services)
-- ==========================================
-- Melengkapi divisi lain selain Balayage (SV01) yang sudah kita buat tadi
INSERT INTO services (service_id, service_name, category, base_tariff, est_duration) VALUES
('SV02', 'Premium Haircut', 'Hair', 250000.00, 60),
('SV03', 'Classic Gel Polish', 'Nails', 150000.00, 90),
('SV04', 'Acrylic Nail Extension', 'Nails', 350000.00, 120),
('SV05', 'Korean Lash Lift', 'Lashes', 200000.00, 60),
('SV06', 'Eyebrow Threading', 'Wax & Eyebrows', 80000.00, 30);

-- ==========================================
-- TAHAP 7: Simulasi Inventaris (Gudang Bahan Baku)
-- ==========================================
-- Bintang butuh ini untuk ngetes fitur "Peringatan Stok Rendah" di Dashboard Admin
INSERT INTO inventories (item_name, stock_qty, min_stock, unit, extra_charge_per_unit) VALUES
('Kopi Espresso (Gram)', 1000.00, 200.00, 'Gram', 0.00),
('Susu Oat (ML)', 5000.00, 1000.00, 'ML', 0.00),
('Croissant Beku (Pcs)', 50.00, 10.00, 'Pcs', 0.00),
('Pewarna Rambut Luminous (Gram)', 500.00, 50.00, 'Gram', 5000.00); -- Ada denda 5rb jika lebih pemakaian

-- ==========================================
-- TAHAP 8: Simulasi Transaksi Sukses (Skenario Bawa Pendamping)
-- ==========================================
-- Kita buat seolah-olah "Alina Customer" booking Kak Sarah, bawa teman nunggu di kafe, lalu sudah bayar lunas.

-- 8A. Masuk ke tabel Reservasi Utama (Status Selesai)
INSERT INTO reservations (user_id, guest_name, seat_id, companion_seat_id, STATUS, schedule_time, is_dp_paid, dp_amount) VALUES
(6, NULL, 'S01', 'C01', 'Selesai', '2026-05-15 10:00:00', TRUE, 50000.00); 
-- ID Reservasi ini akan menjadi angka 1

-- 8B. Detail Layanan Salon (Alina ambil Balayage sama Kak Sarah)
INSERT INTO reservation_details (res_id, service_id, beautician_id) VALUES
(1, 'SV01', 3); 

-- 8C. Order F&B (Temannya Alina pesan Aren Latte di Meja Kafe C01)
INSERT INTO orders (res_id, guest_name, order_type, seat_id, menu_id, qty, payment_status, STATUS) VALUES
(1, NULL, 'Dine-In', 'C01', 'M002', 1, 'Paid', 'Selesai'); 

-- 8D. Checkout di Kasir (Total: Balayage 850rb + Aren Latte 25rb - Diskon Promo 50rb = 825rb)
INSERT INTO transactions (res_id, total_amount, payment_method, payment_date) VALUES
(1, 825000.00, 'QRIS', '2026-05-15 13:30:00');

-- 8E. Alina memberikan Review Bintang 5 untuk Kak Sarah
INSERT INTO reviews (res_id, customer_id, beautician_id, menu_id, rating, COMMENT) VALUES
(1, 6, 3, 'M002', 5, 'Kak Sarah telaten banget warnain rambutku! Kopi arennya juga enak buat nemenin nunggu.');

-- ==========================================
-- TAHAP 9: Simulasi Transaksi Berjalan (In Progress)
-- ==========================================
-- Kita buat satu reservasi lagi yang sedang dilayani saat ini (biar kalender kapster kelihatan penuh)
INSERT INTO reservations (user_id, guest_name, seat_id, companion_seat_id, STATUS, schedule_time, is_dp_paid, dp_amount) VALUES
(6, NULL, 'S02', NULL, 'In-Service', '2026-05-15 15:00:00', TRUE, 50000.00);
-- ID Reservasi ini akan menjadi angka 2

INSERT INTO reservation_details (res_id, service_id, beautician_id) VALUES
(2, 'SV03', 4); -- Alina lanjut Nail Art sama Kak Rina (Status masih jalan, belum ada di tabel transactions)

-- ==========================================
-- TAHAP 10: Simulasi Tamu Kafe Dadakan (Jalur A / Non-Member)
-- ==========================================
INSERT INTO orders (res_id, guest_name, order_type, seat_id, menu_id, qty, payment_status, STATUS) VALUES
(NULL, 'Kak Budi (Tamu Lewat)', 'Takeaway', NULL, 'M003', 2, 'Paid', 'New');

-- ==========================================
-- 1. Dummy Data Resep (BOM)
-- ==========================================
-- Kita buat resep untuk Original Croissant (menggunakan 1 pcs Croissant Beku dari gudang yang item_id-nya 3)
INSERT INTO bom_details (item_id, quantity_required) VALUES (3, 1.00);

-- Update tabel menus agar M005 (Original Croissant) terhubung dengan resep bom_recipe_id = 1 yang baru dibuat
UPDATE menus SET bom_recipe_id = 1 WHERE menu_id = 'M005';


-- ==========================================
-- 2. Dummy Data Penggunaan Bahan Ekstra (Beautician)
-- ==========================================
-- Simulasi: Kak Sarah butuh tambahan 20 gram pewarna rambut (item_id 4) untuk Balayage Alina (res_id 1)
INSERT INTO extra_material_usages (res_id, item_id, qty_used) VALUES (1, 4, 20.00);