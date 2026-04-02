<?php
require 'core/config.php';

echo "<h2>Database Update Tool - Ney Dream</h2>";

function addColumn($conn, $table, $column, $type) {
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check->num_rows == 0) {
        if ($conn->query("ALTER TABLE `$table` ADD `$column` $type")) {
            echo "<p style='color:green'>Berhasil menambah kolom: <strong>$column</strong></p>";
        } else {
            echo "<p style='color:red'>Gagal menambah kolom $column: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:gray'>Kolom <strong>$column</strong> sudah ada (Skipped).</p>";
    }
}


addColumn($conn, 'users', 'is_verified', 'TINYINT(1) DEFAULT 0');
addColumn($conn, 'users', 'verification_token', 'VARCHAR(255) DEFAULT NULL');
addColumn($conn, 'users', 'google_id', 'VARCHAR(255) DEFAULT NULL');
addColumn($conn, 'users', 'profile_pic', 'VARCHAR(255) DEFAULT NULL');


$conn->query("UPDATE users SET is_verified = 1 WHERE role = 'admin'");
echo "<p style='color:blue'>Admin otomatis diset terverifikasi.</p>";

echo "<br><hr><br>";
echo "<strong>Struktur Database Berhasil Disinkronkan!</strong><br>";
echo "Silakan coba login kembali atau daftar akun baru.<br><br>";
echo "<a href='index.php' style='padding:10px 20px; background:#ea3671; color:white; text-decoration:none; border-radius:5px;'>Kembali ke Home</a>";
?>
