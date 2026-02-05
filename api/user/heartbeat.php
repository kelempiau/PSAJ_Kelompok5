<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn->query("UPDATE users SET last_seen = CURRENT_TIMESTAMP WHERE id = $user_id");

echo json_encode(['success' => true]);
?>
