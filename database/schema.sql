-- Create database
CREATE DATABASE IF NOT EXISTS drug_tracking;
USE drug_tracking;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'procurement', 'pharmacist') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Suppliers table
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_info TEXT
);

-- Procurements table
CREATE TABLE procurements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT,
    date_procured DATE,
    reference_no VARCHAR(50),
    notes TEXT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- Stock table
CREATE TABLE stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    procurement_id INT,
    drug_name VARCHAR(100) NOT NULL,
    batch_number VARCHAR(50),
    quantity INT NOT NULL,
    expiry_date DATE,
    FOREIGN KEY (procurement_id) REFERENCES procurements(id) ON DELETE SET NULL
);

-- Recipients table
CREATE TABLE recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    identifier VARCHAR(50)
);

-- Distributions table
CREATE TABLE distributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stock_id INT,
    recipient_id INT,
    quantity INT NOT NULL,
    date_distributed DATE,
    FOREIGN KEY (stock_id) REFERENCES stock(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES recipients(id) ON DELETE CASCADE
);

-- Logs table
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

ALTER TABLE users ADD COLUMN last_login DATETIME DEFAULT NULL;
