-- Create the database
CREATE DATABASE IF NOT EXISTS cashier;
USE cashier;

-- Branches Table
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    business_type VARCHAR(50) NOT NULL DEFAULT 'cafe',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users Table (RBAC)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Will store bcrypt hashes
    role ENUM('superadmin', 'admin', 'cashier') NOT NULL DEFAULT 'cashier',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
);

-- Menus Table
CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    category VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
);

-- Transactions Table
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    user_id INT NOT NULL,
    customer_name VARCHAR(255) DEFAULT 'Guest',
    status ENUM('pending', 'paid') DEFAULT 'pending',
    total INT NOT NULL,
    paid_amount INT DEFAULT 0,
    change_amount INT DEFAULT 0,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Transaction Items Table
CREATE TABLE transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    menu_id INT NOT NULL,
    qty INT NOT NULL,
    price INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);

-- Insert Dummy Data for initial testing
INSERT INTO branches (id, name, business_type) VALUES (1, 'Main Cafe', 'cafe');

-- Password is 'password123' (CakePHP Default Bcrypt Hash for testing)
INSERT INTO users (id, branch_id, username, password, role) VALUES 
(1, 1, 'admin', '$2y$10$U3P.1v2y/8v3R4pE0.F.cO.Q/8.7/0.R.k./9./8.1.1.1.1.1.1', 'admin'),
(2, 1, 'cashier1', '$2y$10$U3P.1v2y/8v3R4pE0.F.cO.Q/8.7/0.R.k./9./8.1.1.1.1.1.1', 'cashier');

INSERT INTO menus (id, branch_id, name, price, category, is_active) VALUES 
(1, 1, 'Espresso', 15000, 'Coffee', 1),
(2, 1, 'Latte', 25000, 'Coffee', 1),
(3, 1, 'Croissant', 20000, 'Food', 1);
