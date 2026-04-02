


ALTER TABLE users 
ADD COLUMN is_verified TINYINT(1) DEFAULT 0,
ADD COLUMN verification_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN google_id VARCHAR(255) DEFAULT NULL,
ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL;

UPDATE users SET is_verified = 1 WHERE role = 'admin';


