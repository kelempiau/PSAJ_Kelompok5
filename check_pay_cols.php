<?php
require 'core/config.php';
$table = 'payment_methods';
$result = $conn->query("SHOW COLUMNS FROM $table");
echo "Columns in $table:\n";
while($row = $result->fetch_assoc()){
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
