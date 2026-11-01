-- 10ccy_trips Database Schema
CREATE DATABASE IF NOT EXISTS `10ccy_trips`;
USE `10ccy_trips`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `user_type` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buses table
CREATE TABLE IF NOT EXISTS `buses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bus_number` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `seats` INT NOT NULL DEFAULT 40,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Routes table
CREATE TABLE IF NOT EXISTS `routes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `source` VARCHAR(100) NOT NULL,
  `destination` VARCHAR(100) NOT NULL,
  `distance_km` INT NOT NULL,
  `duration_minutes` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Schedules table
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bus_id` INT NOT NULL,
  `route_id` INT NOT NULL,
  `departure_time` DATETIME NOT NULL,
  `arrival_time` DATETIME NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`bus_id`) REFERENCES `buses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`route_id`) REFERENCES `routes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `schedule_id` INT NOT NULL,
  `seat_no` VARCHAR(10) NOT NULL,
  `passenger_name` VARCHAR(100) NOT NULL,
  `status` ENUM('booked', 'cancelled') NOT NULL DEFAULT 'booked',
  `booked_on` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`schedule_id`) REFERENCES `schedules`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin User (username: admin, password: admin123)
-- Hash generated using PHP password_hash("admin123", PASSWORD_DEFAULT)
INSERT INTO `users` (`full_name`, `email`, `username`, `password`, `user_type`) 
VALUES ('System Admin', 'admin@10ccytrips.com', 'admin', '$2y$10$B.4n3W6y4oVbM0mI5bQhSeVd8lHfxBwO2T6g2/tMcmGZl0aW1X7C2', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;

