-- SQLite Schema untuk db_merish
-- Konversi dari MySQL untuk kompatibilitas SQLite

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    NAME TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    PASSWORD TEXT NOT NULL,
    ROLE TEXT NOT NULL CHECK (ROLE IN ('Admin', 'Receptionist', 'Barista', 'Beautician', 'Customer')),
    loyalty_stage INTEGER NOT NULL DEFAULT 1,
    total_spent DECIMAL(12,2) NOT NULL DEFAULT 0,
    reward_points INTEGER NOT NULL DEFAULT 0
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    service_id TEXT PRIMARY KEY,
    service_name TEXT NOT NULL,
    category TEXT NOT NULL CHECK (category IN ('Hair', 'Nails', 'Lashes', 'Wax & Eyebrows')),
    base_tariff DECIMAL(10,2) NOT NULL,
    est_duration INTEGER NOT NULL
);

-- Inventories Table
CREATE TABLE IF NOT EXISTS inventories (
    item_id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_name TEXT NOT NULL,
    stock_qty DECIMAL(8,2) NOT NULL DEFAULT 0,
    unit TEXT NOT NULL
);

-- Seats Table
CREATE TABLE IF NOT EXISTS seats (
    seat_id TEXT PRIMARY KEY,
    seat_name TEXT NOT NULL,
    zone_type TEXT NOT NULL CHECK (zone_type IN ('Active Area', 'Relaxation Lounge')),
    qr_code_url TEXT NOT NULL UNIQUE
);

-- Reward Catalog Table
CREATE TABLE IF NOT EXISTS reward_catalog (
    reward_id INTEGER PRIMARY KEY AUTOINCREMENT,
    reward_name TEXT NOT NULL,
    points_required INTEGER NOT NULL,
    reward_type TEXT NOT NULL CHECK (reward_type IN ('F&B Tier', 'Retail Product', 'Salon Service/Cashback')),
    stock INTEGER NOT NULL DEFAULT 0
);

-- BOM Details Table
CREATE TABLE IF NOT EXISTS bom_details (
    bom_recipe_id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_id INTEGER NOT NULL,
    quantity_required DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (item_id) REFERENCES inventories(item_id) ON UPDATE CASCADE
);

-- Staff Profiles Table
CREATE TABLE IF NOT EXISTS staff_profiles (
    profile_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL UNIQUE,
    specialization TEXT NOT NULL CHECK (specialization IN ('Hair Stylist', 'Nailist', 'Lash Technician', 'Wax & Threading Specialist', 'Barista')),
    hire_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE
);

-- Reservations Table
CREATE TABLE IF NOT EXISTS reservations (
    res_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    seat_id TEXT NOT NULL,
    STATUS TEXT NOT NULL DEFAULT 'Stage 1' CHECK (STATUS IN ('Stage 1', 'In-Service', 'Selesai')),
    schedule_time DATETIME NOT NULL,
    is_dp_paid BOOLEAN DEFAULT 0,
    dp_amount DECIMAL(10,2) DEFAULT 0,
    payment_proof_url TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(seat_id) ON UPDATE CASCADE
);

-- Menus Table
CREATE TABLE IF NOT EXISTS menus (
    menu_id TEXT PRIMARY KEY,
    menu_name TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    bom_recipe_id INTEGER,
    FOREIGN KEY (bom_recipe_id) REFERENCES bom_details(bom_recipe_id) ON UPDATE CASCADE
);

-- Reservation Details Table
CREATE TABLE IF NOT EXISTS reservation_details (
    detail_id INTEGER PRIMARY KEY AUTOINCREMENT,
    res_id INTEGER NOT NULL,
    service_id TEXT NOT NULL,
    beautician_id INTEGER,
    FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON UPDATE CASCADE,
    FOREIGN KEY (beautician_id) REFERENCES users(user_id) ON UPDATE CASCADE
);

-- Redemptions Table
CREATE TABLE IF NOT EXISTS redemptions (
    redemption_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    reward_id INTEGER NOT NULL,
    redemption_date DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES reward_catalog(reward_id) ON UPDATE CASCADE
);

-- Promotions Table
CREATE TABLE IF NOT EXISTS promotions (
    promo_id INTEGER PRIMARY KEY AUTOINCREMENT,
    promo_name TEXT NOT NULL,
    service_id_req TEXT,
    menu_id_req TEXT,
    discount_value DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (service_id_req) REFERENCES services(service_id) ON UPDATE CASCADE,
    FOREIGN KEY (menu_id_req) REFERENCES menus(menu_id) ON UPDATE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id INTEGER PRIMARY KEY AUTOINCREMENT,
    res_id INTEGER NOT NULL,
    menu_id TEXT NOT NULL,
    qty INTEGER NOT NULL CHECK (qty > 0),
    STATUS TEXT NOT NULL DEFAULT 'In Progress' CHECK (STATUS IN ('In Progress', 'Selesai')),
    FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON UPDATE CASCADE
);

-- Transactions Table
CREATE TABLE IF NOT EXISTS transactions (
    trans_id INTEGER PRIMARY KEY AUTOINCREMENT,
    res_id INTEGER NOT NULL UNIQUE,
    total_amount DECIMAL(12,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON UPDATE CASCADE
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    review_id INTEGER PRIMARY KEY AUTOINCREMENT,
    res_id INTEGER NOT NULL,
    customer_id INTEGER NOT NULL,
    beautician_id INTEGER,
    menu_id TEXT,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    COMMENT TEXT,
    FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    FOREIGN KEY (beautician_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON UPDATE CASCADE
);
