USE db_merish_cafe;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE order_details;
TRUNCATE TABLE orders;
TRUNCATE TABLE bom_details;
TRUNCATE TABLE inventories;
TRUNCATE TABLE menus;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Master Data Dapur Kafe
INSERT INTO menus (menu_id, menu_name, price) VALUES
('M001', 'Aren Latte', 25000.00),
('M002', 'Choco Frappe', 30000.00);

INSERT INTO inventories (item_name, stock_qty, min_stock, unit) VALUES
('Susu Oat (ML)', 5000.00, 1000.00, 'ML'),
('Kopi Espresso (Gram)', 1000.00, 200.00, 'Gram');

-- Resep Aren Latte (Memotong 150ml Susu & 20g Espresso)
INSERT INTO bom_details (menu_id, item_id, quantity_required) VALUES 
('M001', 1, 150.00), 
('M001', 2, 20.00);  

-- ==========================================
-- SKENARIO TRANSAKSI KAFE (Dua Skenario)
-- ==========================================

-- Skenario 1: Alina pesan minuman tambahan dari Kursi Salon (S01)
-- Perhatikan seat_id 'S01' ini otomatis terhubung ke database Salon!
INSERT INTO orders (guest_name, seat_id, total_amount, payment_method, payment_status, STATUS, order_date) VALUES
('Alina', 'S01', 30000.00, 'QRIS', 'Paid', 'Ready', '2026-06-10 11:30:00');

INSERT INTO order_details (order_id, menu_id, qty, subtotal) VALUES
(1, 'M002', 1, 30000.00);

-- Skenario 2: Mas Budi (Walk-In murni ke area Kafe untuk WFA)
-- seat_id NULL karena Mas Budi ambil sendiri di meja kasir (Takeaway/