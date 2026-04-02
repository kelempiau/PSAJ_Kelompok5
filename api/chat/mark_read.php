<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Not authenticated']) . "!!!JSON_END!!!";
    exit;
}

$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;

if ($conversation_id === 0) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Conversation ID required']) . "!!!JSON_END!!!";
    exit;
}

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$sender_type = $is_admin ? 'customer' : 'admin';

$stmt = $conn->prepare("UPDATE messages SET is_read = TRUE WHERE conversation_id = ? AND sender_type = ?");
$stmt->bind_param("is", $conversation_id, $sender_type);

if ($stmt->execute()) {
    $out = ['success' => true];
} else {
    $out = ['success' => false, 'error' => 'Failed to mark as read'];
}
ob_clean();
echo "!!!JSON_START!!!" . json_encode($out) . "!!!JSON_END!!!";
exit;
