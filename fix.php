<?php
// Set error reporting to see everything
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'core/config.php';

echo "<h1>🛠️ Emergency Database Fixer</h1>";

function addColumn($conn, $table, $column, $definition) {
    echo "Checking $table.$column... ";
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check && $check->num_rows == 0) {
        if ($conn->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition")) {
            echo "<span style='color:green'>✅ BERHASIL Ditambahkan!</span><br>";
        } else {
            echo "<span style='color:red'>❌ GAGAL: " . $conn->error . "</span><br>";
        }
    } else {
        echo "<span style='color:blue'>ℹ️ Sudah Ada.</span><br>";
    }
}

// Run the migration
addColumn($conn, 'users', 'profile_pic', 'VARCHAR(255) DEFAULT NULL');
addColumn($conn, 'studio_settings', 'admin_profile_pic', 'VARCHAR(255) DEFAULT NULL');

echo "<br><hr>";
echo "<a href='index.php' style='padding:10px 20px; background:green; color:white; text-decoration:none; border-radius:5px;'>Selesai, Coba Buka Home Sekarang</a>";
?>
