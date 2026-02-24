-- City Councilor Office Visitor E-Logbook System
-- Database Schema for MySQL
-- Run this script in your MySQL server to set up the database

CREATE DATABASE IF NOT EXISTS visitor_management;
USE visitor_management;

-- ============================================
-- Staff / Users Table (user_tbl)
-- ============================================
CREATE TABLE IF NOT EXISTS user_tbl (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Visitor Table (visitor_tbl)
-- ============================================
CREATE TABLE IF NOT EXISTS visitor_tbl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    purpose VARCHAR(150) NOT NULL,
    address VARCHAR(150) NOT NULL,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    user_id INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_tbl(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Indexes for Faster Search
-- ============================================
CREATE INDEX idx_visitor_name ON visitor_tbl(visitor_name);
CREATE INDEX idx_visit_date ON visitor_tbl(visit_date);

-- ============================================
-- Default admin user
-- IMPORTANT: Run api/setup.php from your browser to create
-- the admin user with a properly hashed password.
-- ============================================
