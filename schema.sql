-- Run these in MySQL (Workbench or phpMyAdmin)
CREATE DATABASE IF NOT EXISTS dailyems CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dailyems;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS expenses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL CHECK (amount >= 0),
  category ENUM('Medicine','Food','Bills and Recharges','Entertainment','Clothings','Household Items','Rent','Others') NOT NULL,
  notes VARCHAR(255) DEFAULT '',
  spent_on DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
