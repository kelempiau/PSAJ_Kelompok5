-- Setup Database Lengkap untuk Localhost

CREATE DATABASE IF NOT EXISTS neydream_db;
USE neydream_db;

-- Import dari database.sql yang sudah ada
-- (Kakak import manual file database/database.sql via phpMyAdmin)

-- Tambahan: Install Chat System
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `status` enum('active','resolved','escalated') DEFAULT 'active',
  `last_message_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `sender_type` enum('customer','admin','bot') NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update config untuk localhost
-- File: C:\xampp\htdocs\neydream\core\config.php
-- Ganti:
-- $host = 'sql108.infinityfree.com';
-- $db = 'if0_37974518_neydream_db';
-- $user = 'if0_37974518';
-- $pass = 'zk0hWKZWsw';
-- 
-- Menjadi:
-- $host = 'localhost';
-- $db = 'neydream_db';
-- $user = 'root';
-- $pass = '';
