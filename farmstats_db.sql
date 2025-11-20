-- FarmStat Database Schema
-- PHP 8 Compatible
-- Import this file into phpMyAdmin

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `farmstats_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `farmstats_db`;

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','farmer','client') NOT NULL DEFAULT 'farmer',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `login_count` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `farmers`
CREATE TABLE IF NOT EXISTS `farmers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `years_experience` int(11) DEFAULT 0,
  `farm_location` varchar(255) DEFAULT NULL,
  `farm_size` decimal(10,2) DEFAULT NULL,
  `farming_method` varchar(100) DEFAULT NULL,
  `land_ownership` varchar(50) DEFAULT NULL,
  `varieties` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `campaigns`
CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `campaign_type` varchar(100) DEFAULT NULL,
  `funding_goal` decimal(15,2) DEFAULT NULL,
  `amount_raised` decimal(15,2) DEFAULT 0.00,
  `deadline` date DEFAULT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `farmer_id` (`farmer_id`),
  CONSTRAINT `campaigns_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `activities`
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farmer_id` int(11) DEFAULT NULL,
  `activity_type` varchar(100) DEFAULT NULL,
  `progress_percent` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `farmer_id` (`farmer_id`),
  CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `user_activities` (if needed)
CREATE TABLE IF NOT EXISTS `user_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `campaign_supporters` (if needed)
CREATE TABLE IF NOT EXISTS `campaign_supporters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `campaign_supporters_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaign_supporters_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('Admin User', 'admin@farmstat.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
('Farmer One', 'farmer@farmstat.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', 'active');
-- Default password for demo: password

INSERT INTO `farmers` (`full_name`, `years_experience`, `farm_location`, `farm_size`, `farming_method`, `land_ownership`, `varieties`) VALUES
('Juan Dela Cruz', 15, 'Nueva Ecija', 5.50, 'Organic', 'Owned', 'IR64, Jasmine'),
('Maria Santos', 8, 'Isabela', 3.20, 'Traditional', 'Leased', 'Sinandomeng'),
('Pedro Reyes', 20, 'Tarlac', 8.00, 'Modern', 'Owned', 'Hybrid');

INSERT INTO `campaigns` (`title`, `description`, `campaign_type`, `funding_goal`, `amount_raised`, `deadline`, `farmer_id`, `status`) VALUES
('Rice Planting Season Support', 'Help fund seeds and fertilizers for the upcoming planting season', 'Equipment', 50000.00, 25000.00, '2025-12-31', 1, 'active'),
('Irrigation System Upgrade', 'Modernize farm irrigation for better water management', 'Infrastructure', 75000.00, 15000.00, '2025-11-30', 2, 'active');

INSERT INTO `activities` (`farmer_id`, `activity_type`, `progress_percent`) VALUES
(1, 'Land Preparation', 75),
(2, 'Seed Planting', 50),
(3, 'Harvesting', 90);

COMMIT;

