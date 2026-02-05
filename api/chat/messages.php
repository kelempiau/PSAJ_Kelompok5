<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;

if ($conversation_id === 0) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
    exit;
}

$sql = "SELECT m.*, u.username 
        FROM messages m 
        LEFT JOIN users u ON m.sender_id = u.id 
        WHERE m.conversation_id = ? 
        ORDER BY m.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversation_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

ob_clean();
echo json_encode(['success' => true, 'messages' => $messages]);
exit;

