
ALTER TABLE reservations 
MODIFY COLUMN payment_type ENUM('full', 'dp', 'dp_transfer', 'dp_cash') DEFAULT 'full';
