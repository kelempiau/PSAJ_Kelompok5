<?php
require '../../core/config.php';

header('Content-Type: text/html');

echo "<h2>🛠️ Database Auto-Installer</h2>";

// 1. Create Conversations Table
$sql1 = "CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('active', 'closed', 'archived') DEFAULT 'active',
    last_message_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql1)) {
    echo "✅ Tabel 'conversations' siap.<br>";
} else {
    echo "❌ Gagal buat tabel 'conversations': " . $conn->error . "<br>";
}

// 2. Create Messages Table
$sql2 = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_type ENUM('customer', 'admin', 'bot') NOT NULL,
    sender_id INT NOT NULL,
    message TEXT,
    image_path VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
)";

if ($conn->query($sql2)) {
    echo "✅ Tabel 'messages' siap.<br>";
} else {
    echo "❌ Gagal buat tabel 'messages': " . $conn->error . "<br>";
}

echo "<hr><h3>🎉 Selesai! Silakan coba refresh halaman Admin Chat sekarang.</h3>";
?>
