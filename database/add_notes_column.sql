-- Add notes column for custom requests
ALTER TABLE reservations ADD COLUMN notes TEXT NULL AFTER addons;
