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
$target_user_id = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : 0;
$sender_type = isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'admin' : 'customer';
if (isset($_POST['sender_type']) && $_POST['sender_type'] === 'bot') {
    $sender_type = 'bot';
}

// ERROR LOGGING
$logFile = '../../debug_chat.log';
$logData = date('Y-m-d H:i:s') . " | Send Request: UserID=$user_id | Target=$target_user_id | Type=$sender_type | Msg=$message\n";
file_put_contents($logFile, $logData, FILE_APPEND);

if ($conversation_id > 0) {
    $check = $conn->prepare("SELECT id FROM conversations WHERE id = ?");
    $check->bind_param("i", $conversation_id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Conversation not found']);
        exit;
    }
} else {
    // Determine user_id for new conversation
    $conv_user_id = ($sender_type === 'admin') ? $target_user_id : $user_id;
    
    if (!$conv_user_id) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'User ID required for new conversation']);
        exit;
    }

    // Check if conversation already exists for this pair
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
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = 'Terjadi kesalahan saat upload file.';
        if ($_FILES['image']['error'] === UPLOAD_ERR_INI_SIZE) $error_msg = 'Ukuran file terlalu besar (melebihi batas server).';
        if ($_FILES['image']['error'] === UPLOAD_ERR_FORM_SIZE) $error_msg = 'Ukuran file terlalu besar.';
        
        echo json_encode(['success' => false, 'error' => $error_msg]);
        exit;
    }

    $upload_dir = '../../uploads/chat/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mov'];
    
    if (in_array($file_ext, $allowed_extensions)) {
        $file_name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = 'uploads/chat/' . $file_name;
        } else {
            $error_code = $_FILES['image']['error'];
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | Upload Failed: Move Error | Code: $error_code | Path: $target_path\n", FILE_APPEND);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Format file tidak didukung: ' . $file_ext]);
        exit;
    }
} elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $error_code = $_FILES['image']['error'];
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | Upload Failed: $_FILES Error Code $error_code\n", FILE_APPEND);
}

if (empty($message) && empty($image_path)) {
    echo json_encode(['success' => false, 'error' => 'Message or image required']);
    exit;
}

// Skip saving if this is just an escalation flag
if ($message === '__ESCALATION_FLAG__') {
    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation_id,
        'message_id' => null
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_type, sender_id, message, image_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isiss", $conversation_id, $sender_type, $user_id, $message, $image_path);

if ($stmt->execute()) {
    $conn->query("UPDATE conversations SET last_message_at = NOW() WHERE id = $conversation_id");
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation_id,
        'message_id' => $stmt->insert_id
    ]);
} else {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Failed to send message']);
}
exit;
?>
