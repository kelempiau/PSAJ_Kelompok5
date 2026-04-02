<?php
require 'core/config.php';


$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'last_seen'");
if ($check_column->num_rows == 0) {
    if ($conn->query("ALTER TABLE users ADD COLUMN last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP")) {
        echo "✅ Kolom 'last_seen' berhasil ditambahkan ke tabel users.\n";
    } else {
        echo "❌ Gagal menambahkan kolom: " . $conn->error . "\n";
    }
} else {
    echo "ℹ️ Kolom 'last_seen' sudah ada.\n";
}
?>
