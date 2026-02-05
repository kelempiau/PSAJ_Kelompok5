<?php
// Letakkan file ini di folder: admin/fix_database.php
// Akses di browser: neydream.rf.gd/admin/fix_database.php

require '../core/config.php';

echo "<h1>🛠️ Database Repair Tool</h1>";

// 1. Cek Koneksi
if ($conn->connect_error) {
    die("<p style='color:red'>❌ Koneksi Database Gagal: " . $conn->connect_error . "</p>");
} else {
    echo "<p style='color:green'>✅ Koneksi Database Berhasil!</p>";
}

// 2. Buat Tabel CONVERSATIONS
$sql1 = "CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('active', 'closed', 'archived') DEFAULT 'active',
    last_message_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql1)) {
    echo "<p style='color:green'>✅ Tabel 'conversations' siap.</p>";
} else {
    echo "<p style='color:red'>❌ Gagal buat tabel 'conversations': " . $conn->error . "</p>";
}

// 3. Buat Tabel MESSAGES
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
    echo "<p style='color:green'>✅ Tabel 'messages' siap.</p>";
} else {
    echo "<p style='color:red'>❌ Gagal buat tabel 'messages': " . $conn->error . "</p>";
}

echo "<hr>";
echo "<h3>Selesai! Sekarang coba buka halaman Chat Admin.</h3>";
echo "<a href='conversations.php'>Kembali ke Chat Admin</a>";
?>
