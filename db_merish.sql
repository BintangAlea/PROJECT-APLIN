-- Merish - Unified Hybrid Business System Database Schema

CREATE DATABASE IF NOT EXISTS db_merish;
USE db_merish;

-- Users/Accounts Table (RBAC - Role-Based Access Control)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'receptionist', 'barista', 'beautician', 'customer') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Salon Services/Treatments Table
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    duration_minutes INT NOT NULL,
    category ENUM('Hair', 'Skincare', 'Massage', 'Other') DEFAULT 'Hair',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cafe Menu Items Table
CREATE TABLE IF NOT EXISTS menus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_id VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    element ENUM('Water', 'Air', 'Electro') NOT NULL,
    category VARCHAR(100),
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Beautician/Staff Table
CREATE TABLE IF NOT EXISTS beauticians (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    specialization VARCHAR(255),
    rating DECIMAL(3, 2) DEFAULT 0.00,
    status ENUM('available', 'busy', 'off-duty') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reservations/Appointments Table
CREATE TABLE IF NOT EXISTS reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    res_id VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    beautician_id INT,
    service_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    duration_minutes INT,
    status ENUM('Stage1', 'Stage2', 'Stage3', 'Completed', 'Cancelled') DEFAULT 'Stage1',
    dp_paid BOOLEAN DEFAULT FALSE,
    dp_amount DECIMAL(10, 2) DEFAULT 50000,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (beautician_id) REFERENCES beauticians(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Orders Table (Cafe Orders)
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(20) NOT NULL UNIQUE,
    reservation_id INT,
    menu_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_per_item DECIMAL(10, 2),
    table_number VARCHAR(20),
    status ENUM('Pending', 'In Progress', 'Done', 'Served') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL,
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);

-- Transactions/Billing Table
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    trans_id VARCHAR(20) NOT NULL UNIQUE,
    reservation_id INT,
    subtotal DECIMAL(12, 2),
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    discount_reason VARCHAR(255),
    synergy_discount BOOLEAN DEFAULT FALSE,
    multiplier_applied DECIMAL(3, 2) DEFAULT 1.00,
    total_amount DECIMAL(12, 2) NOT NULL,
    payment_method ENUM('Cash', 'Card', 'Digital Wallet') DEFAULT 'Cash',
    payment_status ENUM('Pending', 'Paid', 'Partial') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- Customer Loyalty/Evolution Levels Table
CREATE TABLE IF NOT EXISTS customer_loyalty (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL UNIQUE,
    visit_count INT DEFAULT 0,
    evolution_stage ENUM('Stage1', 'Stage2', 'Stage3') DEFAULT 'Stage1',
    loyalty_points DECIMAL(10, 2) DEFAULT 0,
    multiplier_tier DECIMAL(3, 2) DEFAULT 1.00,
    last_visit TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Inventory/Stock Management Table
CREATE TABLE IF NOT EXISTS inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_id INT NOT NULL,
    quantity INT NOT NULL,
    last_restock TIMESTAMP,
    reorder_level INT DEFAULT 5,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
    UNIQUE KEY (menu_id)
);

-- Service Package/Bundling Table (untuk rekomendasi paket)
CREATE TABLE IF NOT EXISTS service_bundles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    base_price DECIMAL(10, 2),
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    services_included TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customer Reviews/Ratings Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservation_id INT NOT NULL,
    customer_id INT NOT NULL,
    beautician_id INT,
    rating INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (beautician_id) REFERENCES beauticians(id) ON DELETE SET NULL
);

-- Insert default services
INSERT INTO services (service_id, name, description, base_price, duration_minutes, category) VALUES
('SQ-Water', 'Water Treatment', 'Refreshing water-based treatment', 150000, 60, 'Skincare'),
('CH-Fire', 'Fire Treatment', 'Intensive heat-based treatment', 200000, 90, 'Massage'),
('HC-Basic', 'Basic Haircut', 'Standard haircut service', 75000, 45, 'Hair'),
('HC-VIP', 'VIP Haircut', 'Premium haircut with head massage', 150000, 60, 'Hair'),
('SK-Facial', 'Facial Treatment', 'Complete facial treatment', 250000, 75, 'Skincare');

-- Insert default menu items
INSERT INTO menus (menu_id, name, description, price, element, category, stock) VALUES
('MN-01', 'Iced Latte', 'Cold coffee with milk', 35000, 'Water', 'Beverage', 50),
('MN-02', 'Hot Coffee', 'Freshly brewed hot coffee', 30000, 'Electro', 'Beverage', 50),
('MN-03', 'Herbal Tea', 'Organic herbal tea blend', 25000, 'Water', 'Beverage', 40),
('MN-04', 'Pastry', 'Fresh baked pastry', 45000, 'Air', 'Snack', 30),
('MN-05', 'Cake Slice', 'Delicious cake slice', 50000, 'Air', 'Dessert', 25);

-- Insert default inventory
INSERT INTO inventory (menu_id, quantity, reorder_level) 
SELECT id, stock, 5 FROM menus;

-- Insert some test users (passwords are hashed with password_hash)
-- Customer test: email: customer@test.com, password: password123
-- Admin test: email: admin@test.com, password: password123
-- Receptionist test: email: receptionist@test.com, password: password123
-- Barista test: email: barista@test.com, password: password123

INSERT INTO users (email, password, full_name, phone, role, status) VALUES
('customer@test.com', '$2y$10$0.kLaJ2w.Z4u0.6nNJG2DOqNZG/8gZbcLvCWyGR0yHZo6Mfbgk5D.', 'Customer Test', '08123456789', 'customer', 'active'),
('admin@test.com', '$2y$10$0.kLaJ2w.Z4u0.6nNJG2DOqNZG/8gZbcLvCWyGR0yHZo6Mfbgk5D.', 'Admin Test', '08198765432', 'admin', 'active'),
('receptionist@test.com', '$2y$10$0.kLaJ2w.Z4u0.6nNJG2DOqNZG/8gZbcLvCWyGR0yHZo6Mfbgk5D.', 'Receptionist Test', '08111111111', 'receptionist', 'active'),
('barista@test.com', '$2y$10$0.kLaJ2w.Z4u0.6nNJG2DOqNZG/8gZbcLvCWyGR0yHZo6Mfbgk5D.', 'Barista Test', '08122222222', 'barista', 'active'),
('beautician@test.com', '$2y$10$0.kLaJ2w.Z4u0.6nNJG2DOqNZG/8gZbcLvCWyGR0yHZo6Mfbgk5D.', 'Beautician Test', '08133333333', 'beautician', 'active');

-- Insert beautician profile for beautician test user
INSERT INTO beauticians (user_id, specialization, status, rating) VALUES
((SELECT id FROM users WHERE email='beautician@test.com'), 'Hair Styling & Treatment', 'available', 4.5);
