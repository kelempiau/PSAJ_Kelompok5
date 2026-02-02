<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit;
}

$sql = "SELECT c.*, u.username, u.email,
        (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND is_read = FALSE AND sender_type = 'customer') as unread_count,
        (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message
        FROM conversations c
        LEFT JOIN users u ON c.user_id = u.id
        ORDER BY c.last_message_at DESC";

$result = $conn->query($sql);

$conversations = [];
while ($row = $result->fetch_assoc()) {
    $conversations[] = $row;
}

echo json_encode(['success' => true, 'conversations' => $conversations]);
