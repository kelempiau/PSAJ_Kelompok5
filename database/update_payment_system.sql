
ALTER TABLE reservations 
ADD COLUMN payment_type ENUM('full', 'dp') DEFAULT 'full' AFTER payment_method,
ADD COLUMN amount_paid DECIMAL(10, 2) DEFAULT 0.00 AFTER payment_type,
ADD COLUMN balance_due DECIMAL(10, 2) DEFAULT 0.00 AFTER amount_paid;
