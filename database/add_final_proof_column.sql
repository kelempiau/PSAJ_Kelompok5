-- Migration to support DP finalization proof
ALTER TABLE reservations 
ADD COLUMN final_payment_proof VARCHAR(255) AFTER payment_proof;
