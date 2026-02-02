<?php
require '../../core/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
$sender_type = isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'admin' : 'customer';

if ($conversation_id > 0) {
    $check = $conn->prepare("SELECT id FROM conversations WHERE id = ?");
    $check->bind_param("i", $conversation_id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Conversation not found']);
        exit;
    }
} else {
    $stmt = $conn->prepare("INSERT INTO conversations (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $conversation_id = $conn->insert_id;
}

$image_path = null;
if (!empty($_FILES['image']['name'])) {
    $upload_dir = '../../uploads/chat/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES['image']['name']);
    $target_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
        $image_path = 'uploads/chat/' . $file_name;
    }
}

if (empty($message) && empty($image_path)) {
    echo json_encode(['success' => false, 'error' => 'Message or image required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_type, sender_id, message, image_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isiss", $conversation_id, $sender_type, $user_id, $message, $image_path);

if ($stmt->execute()) {
    $conn->query("UPDATE conversations SET last_message_at = NOW() WHERE id = $conversation_id");
    
    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation_id,
        'message_id' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send message']);
}
