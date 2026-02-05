<?php
require '../../core/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;

if ($conversation_id > 0) {
    if (!$is_admin) {
        // Customer can only clear their own conversation
        $check = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
        $check->bind_param("ii", $conversation_id, $user_id);
    } else {
        // Admin can clear any conversation
        $check = $conn->prepare("SELECT id FROM conversations WHERE id = ?");
        $check->bind_param("i", $conversation_id);
    }
    $check->execute();
    $res = $check->get_result();
} else {
    // Legacy behavior: find conversation for current user
    $stmt = $conn->prepare("SELECT id FROM conversations WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
}

if ($row = $res->fetch_assoc()) {
    $conversation_id = $row['id'];
    
    // Delete messages
    $del = $conn->prepare("DELETE FROM messages WHERE conversation_id = ?");
    $del->bind_param("i", $conversation_id);
    $del->execute();
    
    // Reset conversation status
    $upd = $conn->prepare("UPDATE conversations SET status = 'active', last_message_at = NULL WHERE id = ?");
    $upd->bind_param("i", $conversation_id);
    $upd->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No conversation found']);
}
