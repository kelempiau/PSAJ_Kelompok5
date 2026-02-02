# Setup Database Localhost XAMPP

## 1. Buat Database Baru

Buka phpMyAdmin: http://localhost/phpmyadmin

Klik tab "SQL" dan jalankan:

```sql
CREATE DATABASE reservasi_db;
USE reservasi_db;
```

## 2. Import Semua Table

Copy-paste SQL berikut ke phpMyAdmin (tab SQL):

```sql
-- Table Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table Reservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time VARCHAR(20) NOT NULL,
    service_type VARCHAR(100),
    addons VARCHAR(255),
    total_price DECIMAL(10, 2),
    payment_method VARCHAR(50),
    payment_proof VARCHAR(255),
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    refund_status ENUM('pending', 'approved', 'rejected') NULL DEFAULT NULL,
    refund_reason TEXT NULL,
    refund_date DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table Locked Slots
CREATE TABLE IF NOT EXISTS locked_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    time VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table Feedback
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT,
    user_id INT,
    rating INT,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table Conversations (Chat System)
CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    status ENUM('ai', 'escalated', 'resolved') DEFAULT 'ai',
    last_message_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table Messages (Chat System)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    sender_type ENUM('customer', 'admin', 'bot'),
    sender_id INT NULL,
    message TEXT,
    image_path VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
);

-- Table Admin Settings
CREATE TABLE IF NOT EXISTS admin_settings (
    user_id INT PRIMARY KEY,
    theme ENUM('light', 'dark') DEFAULT 'light',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Admin User (password: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@neydream.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Test User (password: user123)
INSERT INTO users (username, email, password, role) VALUES 
('user', 'user@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');
```

## 3. Selesai!

Sekarang akses:
- **Admin Panel**: http://localhost/reservasi/admin/dashboard.php
  - Username: `admin`
  - Password: `admin123`

- **Chat System**: http://localhost/reservasi/admin/conversations.php

- **Customer**: http://localhost/reservasi/user/reservasi.php
  - Username: `user`
  - Password: `user123`

## Troubleshooting

### MySQL tidak jalan?
- Buka XAMPP Control Panel
- Start MySQL (klik tombol Start)

### Error "Access denied"?
- Pastikan username: `root`, password: kosong
- Cek di config.php sudah benar

### Table sudah ada?
- DROP DATABASE reservasi_db; lalu buat lagi
- Atau gunakan database lain dan ganti nama di config.php
