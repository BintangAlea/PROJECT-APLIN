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

INSERT INTO inventories (item_id, item_name, stock_qty, min_stock, unit) VALUES
(1, 'Susu Oat (ML)', 5000.00, 1000.00, 'ML'),
(2, 'Kopi Espresso (Gram)', 1000.00, 200.00, 'Gram'),
(3, 'Biji Kopi / Coffee Beans', 5000.0, 1000.0, 'Gram'),
(4, 'Air Mineral / Purified Water', 10000.0, 2000.0, 'ML'),
(5, 'Es Batu / Ice Cubes', 10000.0, 2000.0, 'Gram'),
(6, 'Susu Segar / Fresh Milk', 10000.0, 2000.0, 'ML'),
(7, 'Whipped Cream', 2000.0, 500.0, 'Gram'),
(8, 'Bubuk Cokelat Premium / Cocoa Powder', 2500.0, 500.0, 'Gram'),
(9, 'Saus Cokelat Belgia / Belgian Chocolate Paste', 2000.0, 500.0, 'Gram'),
(10, 'Bubuk Matcha Jepang Uji / Pure Matcha Powder', 1500.0, 300.0, 'Gram'),
(11, 'Sirup Gula / Simple Syrup', 3000.0, 500.0, 'ML'),
(12, 'Sirup Gula Aren Organik / Liquid Palm Sugar', 3000.0, 500.0, 'ML'),
(13, 'Sirup Vanilla / Vanilla Syrup', 2500.0, 500.0, 'ML'),
(14, 'Saus Karamel / Caramel Sauce Topping', 2500.0, 500.0, 'ML'),
(15, 'Madu / Honey', 2000.0, 500.0, 'ML'),
(16, 'Sari Jahe Alami / Ginger Extract', 2000.0, 500.0, 'ML'),
(17, 'Kantong Teh / Tea Bag', 500.0, 100.0, 'Pcs'),
(18, 'Daun Teh Premium / Artisan Loose Tea Leaves', 1500.0, 300.0, 'Gram'),
(19, 'Frozen Dough Butter Croissant', 100.0, 20.0, 'Pcs'),
(20, 'Frozen Dough Almond Croissant', 100.0, 20.0, 'Pcs'),
(21, 'Frozen Dough Pain Au Chocolat', 100.0, 20.0, 'Pcs'),
(22, 'Kue Tiramisu Potong / Pre-sliced Tiramisu Cake', 50.0, 10.0, 'Pcs'),
(23, 'Mentega Cair / Melted Butter', 2000.0, 500.0, 'Gram'),
(24, 'Isian Krim Almond / Almond Frangipane Cream', 1500.0, 300.0, 'Gram'),
(25, 'Kacang Almond Iris / Sliced Almond Topping', 1000.0, 200.0, 'Gram'),
(26, 'Gula Halus / Icing Sugar', 2000.0, 500.0, 'Gram'),
(27, 'Kentang Goreng Potong / Shoestring French Fries', 10000.0, 2000.0, 'Gram'),
(28, 'Minyak Truffle / Truffle Oil', 1000.0, 200.0, 'ML'),
(29, 'Keju Parmesan Parut / Grated Parmesan Cheese', 1500.0, 300.0, 'Gram'),
(30, 'Daun Peterseli Cincang / Chopped Parsley', 500.0, 100.0, 'Gram'),
(31, 'Minyak Goreng / Frying Oil', 10000.0, 2000.0, 'ML'),
(32, 'Selai Mangga / Mango Puree', 2000.0, 500.0, 'Gram'),
(33, 'Konsentrat Jeruk Yuzu / Yuzu Fruit Base', 2000.0, 500.0, 'ML'),
(34, 'Selai atau Buah Stroberi / Strawberry Puree', 2000.0, 500.0, 'Gram'),
(35, 'Daging Alpukat Segar / Fresh Avocado Pulp', 3000.0, 500.0, 'Gram'),
(36, 'Buah Beri Segar / Fresh Berries Garnishment', 500.0, 100.0, 'Pcs'),
(37, 'Buah Stroberi Segar / Fresh Strawberry', 500.0, 100.0, 'Pcs'),
(38, 'Daun Mint Segar / Fresh Mint Leaves', 500.0, 100.0, 'Gram'),
(39, 'Air Soda / Sparkling Soda Water', 10000.0, 2000.0, 'ML'),
(40, 'Sirup / Ekstrak Bunga Rosella', 2000.0, 500.0, 'ML'),
(41, 'Sirup Blackberry / Mixed Berry Syrup', 2000.0, 500.0, 'ML'),
(42, 'Sirup Jeruk Nipis / Lime Syrup', 2000.0, 500.0, 'ML'),
(43, 'Krim Kental Manis / Condensed Milk', 3000.0, 500.0, 'ML'),
(44, 'Es Krim Vanila / Vanilla Ice Cream', 5000.0, 1000.0, 'Gram'),
(45, 'Biskuit Oreo / Oreo Cookies', 500.0, 100.0, 'Pcs');

-- Resep Aren Latte (Memotong 150ml Susu & 20g Espresso)
-- Matikan pengaman relasi sementara
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO bom_details (menu_id, item_id, quantity_required) VALUES
-- ==========================================
-- 1. Espresso Single/Double
-- ==========================================
('M001', 1, 18),    
('M001', 2, 30),    

-- ==========================================
-- 2. Americano (Hot/Ice)
-- ==========================================
('M002', 1, 18),    
('M002', 2, 150),   
('M002', 3, 120),   

-- ==========================================
-- 3. Cappuccino
-- ==========================================
('M003', 1, 18),    
('M003', 4, 150),   
('M003', 6, 5),     

-- ==========================================
-- 4. Choco Frappe
-- ==========================================
('M004', 6, 30),    
('M004', 4, 150),   
('M004', 9, 20),    
('M004', 5, 30),    
('M004', 3, 150),   

-- ==========================================
-- 5. Aren Latte Premium
-- ==========================================
('M005', 1, 18),    
('M005', 4, 150),   
('M005', 10, 30),   
('M005', 3, 150),   

-- ==========================================
-- 6. Caramel Macchiato
-- ==========================================
('M006', 1, 18),    
('M006', 4, 150),   
('M006', 11, 20),   
('M006', 12, 15),   
('M006', 3, 150),   

-- ==========================================
-- 7. Mochaccino (Belgian Chocolate)
-- ==========================================
('M007', 1, 18),    
('M007', 7, 30),    
('M007', 4, 150),   
('M007', 3, 150),   

-- ==========================================
-- 8. Signature Matcha Latte
-- ==========================================
('M008', 8, 20),    
('M008', 4, 150),   
('M008', 9, 20),    
('M008', 3, 150),   

-- ==========================================
-- 9. Ginger Tea
-- ==========================================
('M009', 14, 30),   
('M009', 15, 1),    
('M009', 13, 20),   
('M009', 2, 200),   

-- ==========================================
-- 10. Artisan Tea Selection
-- ==========================================
('M010', 16, 5),    
('M010', 2, 200),   

-- ==========================================
-- 11. Butter Croissant
-- ==========================================
('M011', 17, 1),    
('M011', 21, 10),   

-- ==========================================
-- 12. Almond Croissant
-- ==========================================
('M012', 18, 1),    
('M012', 22, 20),   
('M012', 23, 10),   
('M012', 24, 5),    

-- ==========================================
-- 13. Pain Au Chocolat
-- ==========================================
('M013', 19, 1),    
('M013', 6, 5),     

-- ==========================================
-- 14. Truffle Fries with Parmesan
-- ==========================================
('M014', 25, 200),  
('M014', 26, 10),   
('M014', 27, 15),   
('M014', 28, 5),    
('M014', 29, 50),   

-- ==========================================
-- 15. Classic Tiramisu Cake
-- ==========================================
('M015', 20, 1),    
('M015', 6, 5),     

-- ==========================================
-- 16. Yuzu Mango Sparkling
-- ==========================================
('M016', 30, 30),   
('M016', 31, 30),   
('M016', 37, 150),  
('M016', 3, 150),   

-- ==========================================
-- 17. Berry Hibiscus Mocktail
-- ==========================================
('M017', 38, 20),   
('M017', 39, 20),   
('M017', 37, 150),  
('M017', 34, 3),    
('M017', 3, 150),   

-- ==========================================
-- 18. Cookies & Cream Milkshake
-- ==========================================
('M018', 42, 100),  
('M018', 4, 100),   
('M018', 43, 3),    
('M018', 5, 30),    

-- ==========================================
-- 19. Avocado Strawberry Smoothies
-- ==========================================
('M019', 33, 100),  
('M019', 32, 30),   
('M019', 4, 100),   
('M019', 41, 30),   
('M019', 3, 150),   

-- ==========================================
-- 20. Strawberry Mint Mojito
-- ==========================================
('M020', 35, 3),    
('M020', 36, 5),    
('M020', 40, 20),   
('M020', 37, 150),  
('M020', 3, 150);   

-- Nyalakan kembali pengaman relasinya
SET FOREIGN_KEY_CHECKS = 1; 

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