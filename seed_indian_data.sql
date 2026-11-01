-- ===================================================
-- 10ccy Trips - Indian Bus Reservation System
-- Database Schema & Indian Fleet Seed Data
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

-- ===================================================
-- SEED DATA
-- ===================================================

-- Clear existing sample data for clean re-seed
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `feedback`;
TRUNCATE TABLE `bookings`;
TRUNCATE TABLE `schedules`;
TRUNCATE TABLE `routes`;
TRUNCATE TABLE `buses`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Seed Users (All default passwords: password123, Admin: admin123)
-- Hash for 'admin123': $2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe
-- Hash for 'password123': $2y$10$4y9pBq4xX3d7I78x5pI.AOH3QzB3X4uYgGq1LgM1.qG2Z.L6S7Z2S
INSERT INTO `users` (`id`, `full_name`, `email`, `username`, `password`, `user_type`) VALUES
(1, 'Admin User', 'admin@10ccytrips.com', 'admin', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'admin'),
(2, 'Ritesh Kulkarni', 'ritesh@10ccytrips.com', 'ritesh0w0', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'user'),
(3, 'Rahul Sharma', 'rahul.sharma@example.com', 'rahul_s', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'user'),
(4, 'Priya Patel', 'priya.patel@example.com', 'priya_p', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'user'),
(5, 'Ananya Iyer', 'ananya.iyer@example.com', 'ananya_i', '$2y$10$Ko/jBn0K4qqNPVeBn5Bw1en5auohXCc/9Ae9RNYEdW8oM5vMsPiSe', 'user');

-- 2. Seed Buses (Popular Indian Operators & Fleets)
INSERT INTO `buses` (`id`, `bus_number`, `name`, `type`, `seats`) VALUES
(1, 'KA-01-F-1024', 'KSRTC Airavat Club Class', 'Multi-Axle Volvo AC Sleeper', 40),
(2, 'MH-12-Q-5566', 'SRS Travels Scania', 'Multi-Axle AC Semi-Sleeper', 44),
(3, 'DL-01-AB-3344', 'Zingbus Electric Express', 'AC Executive Seater', 36),
(4, 'AP-09-BK-7788', 'IntrCity SmartBus Premium', 'Volvo B11R AC Sleeper', 40),
(5, 'TS-07-UA-9911', 'Orange Travels Royal', 'BharatBenz AC Sleeper', 32),
(6, 'GJ-01-XY-2200', 'VRL Logistics I-Shift', 'Multi-Axle Volvo AC', 40);

-- 3. Seed Routes (High-traffic Indian Intercity Corridors)
INSERT INTO `routes` (`id`, `source`, `destination`, `distance_km`, `duration_minutes`) VALUES
(1, 'Bengaluru', 'Chennai', 350, 360),
(2, 'Chennai', 'Bengaluru', 350, 360),
(3, 'Mumbai', 'Pune', 150, 180),
(4, 'Pune', 'Mumbai', 150, 180),
(5, 'Delhi', 'Jaipur', 280, 300),
(6, 'Jaipur', 'Delhi', 280, 300),
(7, 'Hyderabad', 'Vijayawada', 275, 290),
(8, 'Vijayawada', 'Hyderabad', 275, 290),
(9, 'Ahmedabad', 'Surat', 260, 270),
(10, 'Pune', 'Goa', 450, 540);

-- 4. Seed Schedules (Upcoming dynamic future dates so queries in book.php always return active trips)
INSERT INTO `schedules` (`id`, `bus_id`, `route_id`, `departure_time`, `arrival_time`, `price`) VALUES
(1, 1, 1, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 6 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 12 HOUR, 850.00),
(2, 2, 2, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 14 HOUR, 799.00),
(3, 3, 5, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 12 HOUR, 650.00),
(4, 4, 7, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 22 HOUR, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 3 HOUR, 950.00),
(5, 5, 3, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 16 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 19 HOUR, 499.00),
(6, 6, 10, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 21 HOUR, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 6 HOUR, 1250.00),
(7, 1, 1, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 23 HOUR, DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 5 HOUR, 890.00),
(8, 2, 4, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 10 HOUR, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 13 HOUR, 520.00);

-- 5. Seed Bookings (Active Confirmed Trips)
INSERT INTO `bookings` (`id`, `user_id`, `schedule_id`, `seat_no`, `passenger_name`, `status`, `booked_on`) VALUES
(1, 2, 1, 'A1', 'Ritesh Kulkarni', 'booked', NOW() - INTERVAL 2 DAY),
(2, 2, 1, 'A2', 'Kiran Kulkarni', 'booked', NOW() - INTERVAL 2 DAY),
(3, 3, 1, 'B4', 'Rahul Sharma', 'booked', NOW() - INTERVAL 1 DAY),
(4, 4, 3, 'A3', 'Priya Patel', 'booked', NOW() - INTERVAL 12 HOUR),
(5, 5, 5, 'C1', 'Ananya Iyer', 'booked', NOW() - INTERVAL 6 HOUR),
(6, 3, 6, 'D2', 'Amit Verma', 'booked', NOW() - INTERVAL 3 HOUR);

-- 6. Seed Feedback
INSERT INTO `feedback` (`id`, `user_id`, `message`, `submitted_on`) VALUES
(1, 2, 'Smooth seat selection UI and prompt booking confirmation! Loved the experience with 10ccy Trips.', NOW() - INTERVAL 1 DAY),
(2, 3, 'The bus was clean, on time, and the e-ticket generation worked seamlessly.', NOW() - INTERVAL 2 DAY),
(3, 4, 'Great transparent pricing for the Delhi-Jaipur route. Will book again!', NOW() - INTERVAL 3 DAY);
