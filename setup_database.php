<?php
require 'core/config.php';

echo "<h1>🚀 Database Ultimate Fixer</h1>";

function runQuery($conn, $sql, $label) {
    if ($conn->query($sql)) {
        echo "<div style='color:green'>✅ $label BERHASIL!</div>";
    } else {
        echo "<div style='color:red'>❌ $label GAGAL: " . $conn->error . "</div>";
    }
}


$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    profile_pic VARCHAR(255) DEFAULT NULL,
    is_verified TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql_users, "Table 'users'");


$sql_services = "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price_start DECIMAL(10,2),
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql_services, "Table 'services'");


$sql_settings = "CREATE TABLE IF NOT EXISTS studio_settings (
    id INT PRIMARY KEY DEFAULT 1,
    studio_name VARCHAR(255) DEFAULT 'Neydream Studio',
    studio_status TINYINT(1) DEFAULT 1,
    province VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    full_address TEXT NULL,
    whatsapp VARCHAR(20) DEFAULT '08123456789',
    instagram VARCHAR(50) DEFAULT '@neydream',
    min_dp_percent INT DEFAULT 20,
    admin_profile_pic VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
runQuery($conn, $sql_settings, "Table 'studio_settings'");


$conn->query("ALTER TABLE studio_settings ADD COLUMN IF NOT EXISTS studio_status TINYINT(1) DEFAULT 1 AFTER studio_name");


$sql_why = "CREATE TABLE IF NOT EXISTS why_choose_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(20)
)";
runQuery($conn, $sql_why, "Table 'why_choose_us'");


$sql_faq = "CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    answer TEXT
)";
runQuery($conn, $sql_faq, "Table 'faq'");


$sql_reservations = "CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100),
    phone VARCHAR(20),
    reservation_date DATE,
    reservation_time VARCHAR(20),
    service_id INT,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql_reservations, "Table 'reservations'");


$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("INSERT IGNORE INTO users (username, email, password, role) VALUES ('admin', 'admin@neydream.com', '$admin_pass', 'admin')");
$conn->query("INSERT IGNORE INTO studio_settings (id, studio_name, studio_status, full_address) VALUES (1, 'Ney Dream Nail Art Studio', 1, 'Purwokerto, Jawa Tengah')");


$checkW = $conn->query("SELECT id FROM why_choose_us LIMIT 1");
if ($checkW->num_rows == 0) {
    $conn->query("INSERT INTO why_choose_us (title, description, icon) VALUES 
    ('Profesional', 'Dikerjakan oleh tenaga ahli berpengalaman.', '🌟'),
    ('Terjangkau', 'Harga kompetitif dengan kualitas premium.', '💰')");
}

echo "<br><hr><br><h3>Selesai! Sekarang silakan <a href='index.php'>Buka Beranda</a>.</h3>";
?>
