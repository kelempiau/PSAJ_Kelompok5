<?php
// Report all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'core/config.php';

$results = [];

echo "<h1>🛠️ Database Migration v2</h1>";

// 1. Check & Add to users table
$check_user = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_pic'");
if ($check_user->num_rows == 0) {
    echo "Attempting to add 'profile_pic' to 'users'...<br>";
    if ($conn->query("ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL")) {
        echo "<span style='color:green'>✅ Kolom 'profile_pic' BERHASIL ditambahkan ke tabel users.</span><br>";
    } else {
        echo "<span style='color:red'>❌ GAGAL menambahkan kolom 'profile_pic': " . $conn->error . "</span><br>";
    }
} else {
    echo "ℹ️ Kolom 'profile_pic' sudah ada di tabel users.<br>";
}

// 2. Check & Add to studio_settings table
$check_admin = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'admin_profile_pic'");
if ($check_admin->num_rows == 0) {
    echo "Attempting to add 'admin_profile_pic' to 'studio_settings'...<br>";
    if ($conn->query("ALTER TABLE studio_settings ADD COLUMN admin_profile_pic VARCHAR(255) DEFAULT NULL")) {
        echo "<span style='color:green'>✅ Kolom 'admin_profile_pic' BERHASIL ditambahkan ke tabel studio_settings.</span><br>";
    } else {
        echo "<span style='color:red'>❌ GAGAL menambahkan kolom 'admin_profile_pic': " . $conn->error . "</span><br>";
    }
} else {
    echo "ℹ️ Kolom 'admin_profile_pic' sudah ada di tabel studio_settings.<br>";
}

echo "<br><hr>";
echo "<a href='index.php' style='display:inline-block; padding:10px 20px; background:#ea3671; color:white; text-decoration:none; border-radius:5px;'>Selesai, Kembali ke Home</a>";
?>
