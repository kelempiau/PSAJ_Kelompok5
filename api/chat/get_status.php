<?php
require '../../core/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, status FROM conversations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    echo json_encode(['success' => true, 'is_escalated' => ($row['status'] === 'escalated'), 'conversation_id' => $row['id']]);
} else {
    echo json_encode(['success' => true, 'is_escalated' => false, 'conversation_id' => null]);
}
