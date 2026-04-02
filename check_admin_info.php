<?php
require 'core/config.php';
$res = $conn->query("SELECT email, phone FROM users WHERE role = 'admin' LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    echo "Admin email: " . $row['email'] . "\n";
    echo "Admin phone: " . $row['phone'] . "\n";
} else {
    echo "No admin found.\n";
}
?>
