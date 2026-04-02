<?php
require 'core/config.php';
$res = $conn->query("DESC studio_settings");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
