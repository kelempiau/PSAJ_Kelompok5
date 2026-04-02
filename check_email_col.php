<?php
require 'core/config.php';
$check = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'email'");
if ($check && $check->num_rows == 0) {
    if ($conn->query("ALTER TABLE studio_settings ADD COLUMN email VARCHAR(100) DEFAULT NULL")) {
        echo "Created email column.\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} else {
    echo "Email column already exists.\n";
}
?>
