<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;

if ($conversation_id === 0) {
    echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
    exit;
}

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$sender_type = $is_admin ? 'customer' : 'admin';

$stmt = $conn->prepare("UPDATE messages SET is_read = TRUE WHERE conversation_id = ? AND sender_type = ?");
$stmt->bind_param("is", $conversation_id, $sender_type);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to mark as read']);
}
