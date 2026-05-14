-- 0. Buat Database dan Gunakan
CREATE DATABASE IF NOT EXISTS db_merish;
USE db_merish;

-- 1. SAPU JAGAT: Bersihkan sisa tabel lama agar tidak bentrok
DROP TABLE IF EXISTS extra_material_usages, redemptions, reward_catalog, reviews, transactions, orders, promotions, reservation_details, reservations, staff_profiles, menus, bom_details, seats, inventories, services, users;

-- 2. Buat Tabel Master (Tanpa Foreign Key)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT,
    NAME VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    ROLE ENUM('Admin', 'Receptionist', 'Barista', 'Beautician', 'Customer') NOT NULL,
    loyalty_stage INT NOT NULL DEFAULT 1,
    total_spent DECIMAL(12,2) NOT NULL DEFAULT 0, 
    reward_points INT NOT NULL DEFAULT 0,       
    PRIMARY KEY (user_id)
) ENGINE=INNODB;

CREATE TABLE services (
    service_id VARCHAR(10),
    service_name VARCHAR(100) NOT NULL,
    category ENUM('Hair', 'Nails', 'Lashes', 'Wax & Eyebrows') NOT NULL, 
    base_tariff DECIMAL(10,2) NOT NULL,
    est_duration INT NOT NULL,
    PRIMARY KEY (service_id)
) ENGINE=INNODB;

CREATE TABLE inventories (
    item_id INT AUTO_INCREMENT,
    item_name VARCHAR(100) NOT NULL,
    stock_qty DECIMAL(8,2) NOT NULL DEFAULT 0,
    min_stock DECIMAL(8,2) NOT NULL DEFAULT 0,            
    unit VARCHAR(20) NOT NULL,
    extra_charge_per_unit DECIMAL(10,2) NOT NULL DEFAULT 0, 
    PRIMARY KEY (item_id)
) ENGINE=INNODB;

CREATE TABLE seats (
    seat_id VARCHAR(10),
    seat_name VARCHAR(50) NOT NULL,
    zone_type ENUM('Active Area', 'Relaxation Lounge') NOT NULL,
    qr_code_url VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (seat_id)
) ENGINE=INNODB;

CREATE TABLE reward_catalog ( 
    reward_id INT AUTO_INCREMENT,
    reward_name VARCHAR(100) NOT NULL,
    points_required INT NOT NULL,
    reward_type ENUM('F&B Tier', 'Retail Product', 'Salon Service/Cashback') NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    PRIMARY KEY (reward_id)
) ENGINE=INNODB;

-- 3. Buat Tabel Berelasi Tingkat 1 (FK ke Tabel Master)
CREATE TABLE bom_details (
    bom_recipe_id INT AUTO_INCREMENT,
    item_id INT NOT NULL,
    quantity_required DECIMAL(8,2) NOT NULL,
    PRIMARY KEY (bom_recipe_id),
    CONSTRAINT fk_bom_item FOREIGN KEY (item_id) REFERENCES inventories(item_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE staff_profiles (
    profile_id INT AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    specialization ENUM('Hair Stylist', 'Nailist', 'Lash Technician', 'Wax & Threading Specialist', 'Barista') NOT NULL,
    work_status ENUM('Online', 'Offline') NOT NULL DEFAULT 'Offline', 
    hire_date DATE NULL,
    PRIMARY KEY (profile_id),
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE reservations (
    res_id INT AUTO_INCREMENT,
    user_id INT NULL,                                      
    guest_name VARCHAR(100) NULL,                          
    seat_id VARCHAR(10) NOT NULL,
    companion_seat_id VARCHAR(10) NULL,                    
    STATUS ENUM('Pending', 'Confirmed', 'In-Service', 'Selesai', 'Canceled') NOT NULL DEFAULT 'Pending',
    schedule_time DATETIME NOT NULL,
    is_dp_paid BOOLEAN DEFAULT FALSE,      
    dp_amount DECIMAL(10,2) DEFAULT 0,     
    payment_proof_url VARCHAR(255) NULL,   
    PRIMARY KEY (res_id),
    CONSTRAINT fk_reservations_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_reservations_seat FOREIGN KEY (seat_id) REFERENCES seats(seat_id) ON UPDATE CASCADE,
    CONSTRAINT fk_reservations_companion FOREIGN KEY (companion_seat_id) REFERENCES seats(seat_id) ON UPDATE CASCADE
) ENGINE=INNODB;

-- 4. Buat Tabel Berelasi Tingkat 2 
CREATE TABLE menus (
    menu_id VARCHAR(10),
    menu_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_available BOOLEAN NOT NULL DEFAULT TRUE,  
    bom_recipe_id INT NULL,
    PRIMARY KEY (menu_id),
    CONSTRAINT fk_menus_bom FOREIGN KEY (bom_recipe_id) REFERENCES bom_details(bom_recipe_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE reservation_details (
    detail_id INT AUTO_INCREMENT,
    res_id INT NOT NULL,
    service_id VARCHAR(10) NOT NULL,
    beautician_id INT NULL,
    PRIMARY KEY (detail_id),
    CONSTRAINT fk_resdetails_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_resdetails_service FOREIGN KEY (service_id) REFERENCES services(service_id) ON UPDATE CASCADE,
    CONSTRAINT fk_resdetails_beautician FOREIGN KEY (beautician_id) REFERENCES users(user_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE extra_material_usages (
    usage_id INT AUTO_INCREMENT,
    res_id INT NOT NULL,
    item_id INT NOT NULL,
    qty_used DECIMAL(8,2) NOT NULL,
    PRIMARY KEY (usage_id),
    CONSTRAINT fk_extra_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_extra_item FOREIGN KEY (item_id) REFERENCES inventories(item_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE redemptions ( 
    redemption_id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    reward_id INT NOT NULL,
    redemption_date DATETIME NOT NULL,
    PRIMARY KEY (redemption_id),
    CONSTRAINT fk_redemptions_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_redemptions_reward FOREIGN KEY (reward_id) REFERENCES reward_catalog(reward_id) ON UPDATE CASCADE
) ENGINE=INNODB;

-- 5. Buat Tabel Berelasi Tingkat 3 
CREATE TABLE promotions (
    promo_id INT AUTO_INCREMENT,
    promo_name VARCHAR(100) NOT NULL,
    service_id_req VARCHAR(10) NULL,
    menu_id_req VARCHAR(10) NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (promo_id),
    CONSTRAINT fk_promotions_service FOREIGN KEY (service_id_req) REFERENCES services(service_id) ON UPDATE CASCADE,
    CONSTRAINT fk_promotions_menu FOREIGN KEY (menu_id_req) REFERENCES menus(menu_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT,
    res_id INT NULL,                                   
    guest_name VARCHAR(100) NULL,                      
    order_type ENUM('Dine-In', 'Takeaway') NOT NULL DEFAULT 'Dine-In', 
    seat_id VARCHAR(10) NULL,                                          
    menu_id VARCHAR(10) NOT NULL,
    qty INT NOT NULL,
    payment_status ENUM('Unpaid', 'Paid') NOT NULL DEFAULT 'Unpaid', 
    STATUS ENUM('New', 'In Progress', 'Selesai') NOT NULL DEFAULT 'New', 
    PRIMARY KEY (order_id),
    CONSTRAINT chk_orders_qty CHECK (qty > 0),
    CONSTRAINT fk_orders_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_orders_seat FOREIGN KEY (seat_id) REFERENCES seats(seat_id) ON UPDATE CASCADE, -- << SUDAH DIPERBAIKI
    CONSTRAINT fk_orders_menu FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON UPDATE CASCADE
) ENGINE=INNODB;

-- 6. Buat Tabel Transaksi dan Review 
CREATE TABLE transactions (
    trans_id INT AUTO_INCREMENT,
    res_id INT NOT NULL UNIQUE,
    total_amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('Cash', 'Debit', 'QRIS', 'Transfer') NOT NULL DEFAULT 'Cash', 
    payment_date DATETIME NOT NULL,
    PRIMARY KEY (trans_id),
    CONSTRAINT fk_transactions_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT,
    res_id INT NOT NULL,
    customer_id INT NOT NULL,
    beautician_id INT NULL,
    menu_id VARCHAR(10) NULL,
    rating INT NOT NULL,
    COMMENT TEXT NULL,
    PRIMARY KEY (review_id),
    CONSTRAINT chk_reviews_rating CHECK (rating >= 1 AND rating <= 5),
    CONSTRAINT fk_reviews_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_customer FOREIGN KEY (customer_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_beautician FOREIGN KEY (beautician_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_menu FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON UPDATE CASCADE
) ENGINE=INNODB;