-- ===================================================
-- 10ccy Trips - Database Schema & Seed Data
-- Matched to Original Project Report & Screenshots
-- ===================================================

CREATE DATABASE IF NOT EXISTS `10ccy_trips` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `10ccy_trips`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `user_type` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Buses Table
CREATE TABLE IF NOT EXISTS `buses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bus_number` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `seats` INT NOT NULL DEFAULT 40,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Routes Table
CREATE TABLE IF NOT EXISTS `routes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `source` VARCHAR(100) NOT NULL,
  `destination` VARCHAR(100) NOT NULL,
  `distance_km` INT NOT NULL,
  `duration_minutes` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Schedules Table
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

-- 5. Bookings Table
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

-- 6. Feedback Table
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `message` TEXT NOT NULL,
  `submitted_on` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clear existing data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `feedback`;
TRUNCATE TABLE `bookings`;
TRUNCATE TABLE `schedules`;
TRUNCATE TABLE `routes`;
TRUNCATE TABLE `buses`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users (admin123 for all)
INSERT INTO `users` (`id`, `full_name`, `email`, `username`, `password`, `user_type`) VALUES
(1, 'System Admin', 'admin@10ccytrips.com', 'admin', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'admin'),
(2, 'Roshan', 'roshan@bmsit.in', 'roshan', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'user'),
(3, 'Ritesh Kulkarni', 'ritesh@10ccytrips.com', 'ritesh0w0', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'user');

-- 2. Buses (From report screenshots)
INSERT INTO `buses` (`id`, `bus_number`, `name`, `type`, `seats`) VALUES
(1, 'KA01AB1234', 'KSRTC Rajahamsa', 'AC Sleeper', 40),
(2, 'KA05CD5678', 'VRL Travels', 'Non-AC Seater', 30),
(3, 'KA09EF9012', 'SRS Travels', 'AC Seater', 35),
(4, 'KA03GH3456', 'Orange Tours', 'Sleeper', 40),
(5, 'KA07IJ7890', 'Sugama Travels', 'AC Sleeper', 30);

-- 3. Routes (From report screenshots)
INSERT INTO `routes` (`id`, `source`, `destination`, `distance_km`, `duration_minutes`) VALUES
(1, 'Bangalore', 'Mysore', 145, 180),
(2, 'Bangalore', 'Mangalore', 350, 480),
(3, 'Bangalore', 'Hubli', 410, 420),
(4, 'Bangalore', 'Belgaum', 510, 540),
(5, 'Mysore', 'Mangalore', 255, 360),
(6, 'Hubli', 'Belgaum', 110, 150),
(7, 'Mangalore', 'Udupi', 60, 90),
(8, 'Bangalore', 'Udupi', 403, 450);

-- 4. Schedules (Active future departure dates so search queries always return results)
INSERT INTO `schedules` (`id`, `bus_id`, `route_id`, `departure_time`, `arrival_time`, `price`) VALUES
(1, 1, 8, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 20 HOUR + INTERVAL 30 MINUTE, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 4 HOUR + INTERVAL 30 MINUTE, 650.00),
(2, 3, 2, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 21 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 5 HOUR, 660.00),
(3, 1, 1, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 6 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 9 HOUR, 300.00),
(4, 2, 2, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 21 HOUR, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 5 HOUR, 650.00),
(5, 3, 3, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 23 HOUR + INTERVAL 30 MINUTE, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 6 HOUR, 600.00),
(6, 5, 5, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 13 HOUR, 450.00),
(7, 4, 4, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 20 HOUR, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 5 HOUR, 700.00),
(8, 2, 7, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 9 HOUR + INTERVAL 30 MINUTE, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 11 HOUR, 180.00);

-- 5. Bookings
INSERT INTO `bookings` (`id`, `user_id`, `schedule_id`, `seat_no`, `passenger_name`, `status`, `booked_on`) VALUES
(1, 2, 1, '1', 'name', 'booked', NOW() - INTERVAL 2 DAY),
(2, 2, 1, '2', 'name2', 'booked', NOW() - INTERVAL 2 DAY),
(3, 2, 1, '3', 'vivek', 'booked', NOW() - INTERVAL 1 DAY),
(4, 2, 1, '4', 'Abhishek', 'booked', NOW() - INTERVAL 1 DAY),
(5, 3, 1, '5', 'Ritesh Kulkarni', 'booked', NOW() - INTERVAL 12 HOUR),
(6, 2, 2, '11', 'abb', 'booked', NOW() - INTERVAL 1 DAY),
(7, 2, 2, '10', 'Abc', 'booked', NOW() - INTERVAL 1 DAY),
(8, 2, 7, '2', 'ritesh', 'cancelled', NOW() - INTERVAL 3 DAY);
