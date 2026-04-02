<?php
require '../../core/config.php';
header('Content-Type: text/plain');
header('X-Chat-Engine-Version: 4.0.0');

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Not authenticated']) . "!!!JSON_END!!!";
    exit;
}

$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
if ($conversation_id === 0) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'ID required']) . "!!!JSON_END!!!";
    exit;
}

$stmt = $conn->prepare("UPDATE conversations SET status = 'escalated' WHERE id = ?");
$stmt->bind_param("i", $conversation_id);
if ($stmt->execute()) {
    $out = ['success' => true, 'message' => 'Escalated'];
} else {
    $out = ['success' => false, 'error' => 'Failed'];
}
ob_clean();
echo "!!!JSON_START!!!" . json_encode($out) . "!!!JSON_END!!!";
exit;
