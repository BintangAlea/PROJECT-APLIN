CREATE DATABASE IF NOT EXISTS db_merish_salon;
USE db_merish_salon;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS reviews, transactions, reservation_details, reservations, promotions, staff_profiles, seats, services, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT,
    NAME VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    ROLE ENUM('Admin', 'Receptionist', 'Barista', 'Beautician', 'Customer') NOT NULL,
    PRIMARY KEY (user_id)
) ENGINE=INNODB;

CREATE TABLE services (
    service_id VARCHAR(10),
    service_name VARCHAR(100) NOT NULL,
    category ENUM('Hair', 'Nails', 'Lashes', 'Wax & Eyebrows') NOT NULL, 
    is_addon BOOLEAN NOT NULL DEFAULT FALSE,
    base_tariff DECIMAL(10,2) NOT NULL,
    est_duration INT NOT NULL,
    PRIMARY KEY (service_id)
) ENGINE=INNODB;

CREATE TABLE seats (
    seat_id VARCHAR(10),
    seat_name VARCHAR(50) NOT NULL,
    zone_type ENUM('Kursi Salon', 'Meja Kafe') NOT NULL,
    qr_code_url VARCHAR(255) NOT NULL UNIQUE, 
    PRIMARY KEY (seat_id)
) ENGINE=INNODB;

CREATE TABLE staff_profiles (
    profile_id INT AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    specialization ENUM('Hair Stylist', 'Nailist', 'Lash Technician', 'Wax & Threading Specialist', 'Barista') NOT NULL,
    work_status ENUM('Online', 'Offline') NOT NULL DEFAULT 'Offline', 
    PRIMARY KEY (profile_id),
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE promotions (
    promo_id INT AUTO_INCREMENT,
    promo_name VARCHAR(100) NOT NULL,
    included_fb_item VARCHAR(100) NOT NULL, 
    discount_value DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (promo_id)
) ENGINE=INNODB;

CREATE TABLE reservations (
    res_id INT AUTO_INCREMENT,
    user_id INT NULL,                                      
    seat_id VARCHAR(10) NOT NULL,
    promo_id INT NULL,                  
    STATUS ENUM('Pending', 'Confirmed', 'In-Service', 'Selesai', 'Canceled') NOT NULL DEFAULT 'Pending',
    schedule_time DATETIME NOT NULL,
    is_dp_paid BOOLEAN DEFAULT FALSE,      
    dp_amount DECIMAL(10,2) DEFAULT 0,     
    payment_proof_url VARCHAR(255) NULL,   
    PRIMARY KEY (res_id),
    CONSTRAINT fk_res_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE,
    CONSTRAINT fk_res_seat FOREIGN KEY (seat_id) REFERENCES seats(seat_id) ON UPDATE CASCADE,
    CONSTRAINT fk_res_promo FOREIGN KEY (promo_id) REFERENCES promotions(promo_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE reservation_details (
    detail_id INT AUTO_INCREMENT,
    res_id INT NOT NULL,
    service_id VARCHAR(10) NOT NULL,
    beautician_id INT NULL,
    qty INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (detail_id),
    CONSTRAINT fk_resdet_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_resdet_service FOREIGN KEY (service_id) REFERENCES services(service_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE transactions (
    trans_id INT AUTO_INCREMENT,
    res_id INT NOT NULL UNIQUE,
    total_amount DECIMAL(12,2) NOT NULL, 
    payment_method ENUM('Cash', 'Debit', 'QRIS', 'Transfer') NOT NULL DEFAULT 'Cash', 
    payment_date DATETIME NOT NULL,
    PRIMARY KEY (trans_id),
    CONSTRAINT fk_trans_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT,
    res_id INT NOT NULL,
    rating INT NOT NULL,
    COMMENT TEXT NULL,
    PRIMARY KEY (review_id),
    CONSTRAINT chk_reviews_rating CHECK (rating >= 1 AND rating <= 5),
    CONSTRAINT fk_reviews_res FOREIGN KEY (res_id) REFERENCES reservations(res_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;