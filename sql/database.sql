-- Visitor Management System Database Schema
-- Run this script in your MySQL server to set up the database

CREATE DATABASE IF NOT EXISTS visitor_management;
USE visitor_management;

-- Users table for admin authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Visitors table for visitor records
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    purpose VARCHAR(255) NOT NULL,
    person_to_visit VARCHAR(100) NOT NULL,
    check_in DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    check_out DATETIME DEFAULT NULL,
    status ENUM('checked_in', 'checked_out') DEFAULT 'checked_in',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
-- Username: admin | Password: admin123
-- IMPORTANT: After importing this SQL, run setup.php from your browser to create
-- the admin user with a properly hashed password. 
-- Or you can manually run: php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
-- and paste the result below.
-- 
-- If you want to insert directly, uncomment and run this after generating a hash:
-- INSERT INTO users (username, password, full_name) VALUES
-- ('admin', 'YOUR_GENERATED_HASH_HERE', 'System Administrator');
