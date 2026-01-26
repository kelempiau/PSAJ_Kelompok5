-- Migration: Add Refund Columns to Reservations Table
-- Run this in phpMyAdmin to add refund functionality

USE neydream_db;

ALTER TABLE reservations 
ADD COLUMN refund_status ENUM('pending', 'approved', 'rejected') NULL DEFAULT NULL AFTER status,
ADD COLUMN refund_reason TEXT NULL AFTER refund_status,
ADD COLUMN refund_date DATETIME NULL AFTER refund_reason;

-- Verify columns were added
DESCRIBE reservations;
