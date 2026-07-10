USE db_merish_cafe;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE order_details;
TRUNCATE TABLE orders;
TRUNCATE TABLE bom_details;
TRUNCATE TABLE inventories;
TRUNCATE TABLE menus;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Master Data Dapur Kafe
INSERT INTO menus (menu_id, menu_name, price, is_available, category, description, image) VALUES
('M001', 'Aren Latte', 25000.00, TRUE, 'Kopi', 'Signature espresso blend dengan susu segar dan gula aren alami.', 'assets/MERISH_PICTURES/CAFE/aren latte.jpg'),
('M002', 'Choco Frappe', 30000.00, TRUE, 'Non Coffee', 'Cokelat blend premium krimi dengan topping whipped cream manis.', 'assets/MERISH_PICTURES/CAFE/choco frappe.jpg'),
('M003', 'Americano', 22000.00, TRUE, 'Kopi', 'Espresso shot ganda dengan air panas untuk rasa kopi murni dan mantap.', 'assets/MERISH_PICTURES/CAFE/americano.jpg'),
('M004', 'Caramel Macchiato', 38000.00, TRUE, 'Kopi', 'Kombinasi espresso, susu hangat, sirup vanilla, dan siraman saus karamel.', 'assets/MERISH_PICTURES/CAFE/caramel macchiato.jpg'),
('M005', 'Espresso', 18000.00, TRUE, 'Kopi', 'Ekstraksi konsentrat kopi murni dengan aroma pekat dan rasa yang kuat.', 'assets/MERISH_PICTURES/CAFE/espresso.png'),
('M006', 'Mocchacino', 35000.00, TRUE, 'Kopi', 'Paduan espresso seimbang dengan saus cokelat premium dan susu hangat lembut.', 'assets/MERISH_PICTURES/CAFE/mocchacino.png'),
('M007', 'Ginger Tea', 25000.00, TRUE, 'Teh', 'Seduhan teh berkualitas dengan irisan jahe bakar segar yang menghangatkan.', 'assets/MERISH_PICTURES/CAFE/ginger tea.jpg'),
('M008', 'Jasmine Tea', 20000.00, TRUE, 'Teh', 'Teh hijau aroma melati yang harum wangi dan menenangkan.', 'assets/MERISH_PICTURES/CAFE/jasmine tea.jpg'),
('M009', 'Almond Croissant', 35000.00, TRUE, 'Pastry', 'Croissant mentega renyah dengan isian frangipane almond manis dan taburan almond panggang.', 'assets/MERISH_PICTURES/CAFE/almond croissant.png'),
('M010', 'Pain au Chocolat', 30000.00, TRUE, 'Pastry', 'Pastry mentega berlapis khas Perancis dengan isian cokelat batangan lezat.', 'assets/MERISH_PICTURES/CAFE/pain au chocolat.jpg'),
('M011', 'Tiramisu Cake', 42000.00, TRUE, 'Pastry', 'Kue tiramisu lembut dengan aroma kopi espresso dan keju mascarpone premium.', 'assets/MERISH_PICTURES/CAFE/tiramisu cake.jpg'),
('M012', 'Matcha Latte', 32000.00, TRUE, 'Non Coffee', 'Teh hijau matcha Jepang premium yang diseduh dengan susu segar krimi.', 'assets/MERISH_PICTURES/CAFE/matcha latte.webp'),
('M013', 'Cookies & Cream', 35000.00, TRUE, 'Non Coffee', 'Minuman es blend dengan remahan biskuit cokelat oreo dan vanilla manis.', 'assets/MERISH_PICTURES/CAFE/cookies cream.png'),
('M014', 'Avocado Strawberry Smoothies', 40000.00, TRUE, 'Non Coffee', 'Smoothie alpukat mentega segar dengan siraman saus strawberry manis.', 'assets/MERISH_PICTURES/CAFE/avocado strawberry smoothies.png'),
('M015', 'Berry Hibiscus Mocktail', 38000.00, TRUE, 'Non Coffee', 'Mocktail segar dari teh bunga hibiscus alami, sirup berry wangi, dan soda.', 'assets/MERISH_PICTURES/CAFE/berry hisbicus mocktail.jpg'),
('M016', 'Strawberry Mojito', 35000.00, TRUE, 'Non Coffee', 'Minuman soda segar rasa strawberry manis dengan daun mint dan perasan jeruk nipis.', 'assets/MERISH_PICTURES/CAFE/strawberry mojito.webp'),
('M017', 'Yuzu Mango Sparkling', 38000.00, TRUE, 'Non Coffee', 'Perpaduan jeruk yuzu Jepang asam manis, potongan mangga harum, dan air soda sparkling.', 'assets/MERISH_PICTURES/CAFE/yuzu mango sparkling.jpg'),
('M018', 'Truffle Fries', 35000.00, TRUE, 'Snack', 'Kentang goreng gurih renyah dengan siraman minyak truffle wangi dan parmesan.', 'assets/MERISH_PICTURES/CAFE/truffle fries.png');

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