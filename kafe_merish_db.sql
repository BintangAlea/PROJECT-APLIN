CREATE DATABASE IF NOT EXISTS db_merish_cafe;
USE db_merish_cafe;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_details, orders, bom_details, inventories, menus;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE menus (
    menu_id VARCHAR(10),
    menu_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_available BOOLEAN NOT NULL DEFAULT TRUE,  
    category ENUM('Kopi', 'Teh', 'Pastry', 'Non Coffee', 'Snack') NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    PRIMARY KEY (menu_id)
) ENGINE=INNODB;

CREATE TABLE inventories (
    item_id INT AUTO_INCREMENT,
    item_name VARCHAR(100) NOT NULL,
    stock_qty DECIMAL(8,2) NOT NULL DEFAULT 0,
    min_stock DECIMAL(8,2) NOT NULL DEFAULT 0,            
    unit VARCHAR(20) NOT NULL,
    PRIMARY KEY (item_id)
) ENGINE=INNODB;

CREATE TABLE bom_details (
    bom_recipe_id INT AUTO_INCREMENT,
    menu_id VARCHAR(10) NOT NULL,
    item_id INT NOT NULL,
    quantity_required DECIMAL(8,2) NOT NULL,
    PRIMARY KEY (bom_recipe_id),
    CONSTRAINT fk_bom_menu FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_bom_item FOREIGN KEY (item_id) REFERENCES inventories(item_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT,
    guest_name VARCHAR(100) NOT NULL, 
    seat_id VARCHAR(10) NULL,                                        
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('QRIS', 'Cash') NOT NULL, 
    payment_status ENUM('Unpaid', 'Paid') NOT NULL DEFAULT 'Unpaid', 
    STATUS ENUM('New', 'In Progress', 'Ready', 'Completed') NOT NULL DEFAULT 'New', 
    order_date DATETIME NOT NULL,
    PRIMARY KEY (order_id),
    -- INI KUNCI RAHASIANYA: Menarik data langsung dari database salon!
    CONSTRAINT fk_orders_seat FOREIGN KEY (seat_id) REFERENCES db_merish_salon.seats(seat_id) ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE order_details (
    detail_id INT AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_id VARCHAR(10) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (detail_id),
    CONSTRAINT fk_orderdet_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_orderdet_menu FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON UPDATE CASCADE
) ENGINE=INNODB;