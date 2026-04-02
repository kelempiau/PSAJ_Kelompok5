<?php
require '../../core/config.php';
header('Content-Type: text/plain');
header('X-Chat-Engine-Version: 4.0.0');

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Not authenticated']) . "!!!JSON_END!!!";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, status FROM conversations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $out = ['success' => true, 'is_escalated' => ($row['status'] === 'escalated'), 'conversation_id' => $row['id']];
} else {
    $out = ['success' => true, 'is_escalated' => false, 'conversation_id' => null];
}
ob_clean();
echo "!!!JSON_START!!!" . json_encode($out) . "!!!JSON_END!!!";
exit;
