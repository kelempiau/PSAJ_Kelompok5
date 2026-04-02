<?php
require 'core/config.php';
$res = $conn->query('SELECT * FROM payment_methods');
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['method_name'] . " | Status: " . $row['is_active'] . "\n";
}
?>
