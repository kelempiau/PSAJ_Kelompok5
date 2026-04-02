<?php
require '../../core/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Not authenticated']) . "!!!JSON_END!!!";
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;

if ($conversation_id > 0) {
    if (!$is_admin) {
        
        $check = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
        $check->bind_param("ii", $conversation_id, $user_id);
    } else {
        
        $check = $conn->prepare("SELECT id FROM conversations WHERE id = ?");
        $check->bind_param("i", $conversation_id);
    }
    $check->execute();
    $res = $check->get_result();
} else {
    
    $stmt = $conn->prepare("SELECT id FROM conversations WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
}

if ($row = $res->fetch_assoc()) {
    $conversation_id = $row['id'];
    
    
    $del = $conn->prepare("DELETE FROM messages WHERE conversation_id = ?");
    $del->bind_param("i", $conversation_id);
    $del->execute();
    
    
    $upd = $conn->prepare("UPDATE conversations SET status = 'active', last_message_at = NULL WHERE id = ?");
    $upd->bind_param("i", $conversation_id);
    $upd->execute();
    
    $out = ['success' => true];
} else {
    $out = ['success' => false, 'error' => 'No conversation found'];
}
ob_clean();
echo "!!!JSON_START!!!" . json_encode($out) . "!!!JSON_END!!!";
exit;
