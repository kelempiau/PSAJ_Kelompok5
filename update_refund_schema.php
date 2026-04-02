<?php
require 'core/config.php';

$queries = [
    "ALTER TABLE reservations ADD COLUMN IF NOT EXISTS refund_target VARCHAR(50) NULL AFTER refund_date",
    "ALTER TABLE reservations ADD COLUMN IF NOT EXISTS refund_account VARCHAR(50) NULL AFTER refund_target"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Success: $q <br>";
    } else {
        echo "Error: " . $conn->error . " <br>";
    }
}
?>
