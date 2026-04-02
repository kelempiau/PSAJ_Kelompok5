<?php
require '../../core/config.php';
header('Content-Type: text/plain');
header('X-Chat-Engine-Version: 4.0.0');

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Not authenticated']) . "!!!JSON_END!!!";
    exit;
}

$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;
if ($conversation_id === 0) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'ID required']) . "!!!JSON_END!!!";
    exit;
}

$sql = "SELECT m.*, u.username, u.profile_pic FROM messages m LEFT JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = ? ORDER BY m.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversation_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) $messages[] = $row;

ob_clean();
echo "!!!JSON_START!!!" . json_encode(['success' => true, 'messages' => $messages]) . "!!!JSON_END!!!";
exit;
