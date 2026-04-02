<?php
require '../../core/config.php';
header('Content-Type: text/plain');
header('X-Chat-Engine-Version: 4.0.0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Invalid method']) . "!!!JSON_END!!!";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Not authenticated']) . "!!!JSON_END!!!";
    exit;
}

$user_id = $_SESSION['user_id'];
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
$target_user_id = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : 0;
$sender_type = isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'admin' : 'customer';
if (isset($_POST['sender_type']) && $_POST['sender_type'] === 'bot') {
    $sender_type = 'bot';
}

if ($conversation_id > 0) {
    $check = $conn->prepare("SELECT id FROM conversations WHERE id = ?");
    $check->bind_param("i", $conversation_id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        ob_clean();
        echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Conversation not found']) . "!!!JSON_END!!!";
        exit;
    }
} else {
    $conv_user_id = ($sender_type === 'admin') ? $target_user_id : $user_id;
    if (!$conv_user_id) {
        ob_clean();
        echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'User ID required']) . "!!!JSON_END!!!";
        exit;
    }
    $check = $conn->prepare("SELECT id FROM conversations WHERE user_id = ?");
    $check->bind_param("i", $conv_user_id);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) {
        $conversation_id = $res->fetch_assoc()['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO conversations (user_id) VALUES (?)");
        $stmt->bind_param("i", $conv_user_id);
        $stmt->execute();
        $conversation_id = $conn->insert_id;
    }
}

$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../uploads/chat/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $file_name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file_name)) {
        $image_path = 'uploads/chat/' . $file_name;
    }
}

if (empty($message) && empty($image_path)) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Message required']) . "!!!JSON_END!!!";
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_type, sender_id, message, image_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isiss", $conversation_id, $sender_type, $user_id, $message, $image_path);

if ($stmt->execute()) {
    $conn->query("UPDATE conversations SET last_message_at = NOW() WHERE id = $conversation_id");
    $out = ['success' => true, 'conversation_id' => $conversation_id, 'message_id' => $stmt->insert_id];
} else {
    $out = ['success' => false, 'error' => 'Save failed'];
}
ob_clean();
echo "!!!JSON_START!!!" . json_encode($out) . "!!!JSON_END!!!";
exit;
