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

$stmt = $conn->prepare("UPDATE conversations SET status = 'escalated' WHERE id = ?");
$stmt->bind_param("i", $conversation_id);

if ($stmt->execute()) {
    $bot_message = "Maaf saya tidak mengerti. Chat ini akan dialihkan dan dibalas oleh admin.";
    $stmt2 = $conn->prepare("INSERT INTO messages (conversation_id, sender_type, message) VALUES (?, 'bot', ?)");
    $stmt2->bind_param("is", $conversation_id, $bot_message);
    $stmt2->execute();
    
    echo json_encode(['success' => true, 'message' => 'Escalated to admin']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to escalate']);
}
