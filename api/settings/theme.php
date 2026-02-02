<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT theme FROM admin_settings WHERE user_id = $user_id");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'theme' => $row['theme']]);
    } else {
        echo json_encode(['success' => true, 'theme' => 'light']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $theme = isset($_POST['theme']) && $_POST['theme'] === 'dark' ? 'dark' : 'light';
    
    $stmt = $conn->prepare("INSERT INTO admin_settings (user_id, theme) VALUES (?, ?) ON DUPLICATE KEY UPDATE theme = ?");
    $stmt->bind_param("iss", $user_id, $theme, $theme);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'theme' => $theme]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save theme']);
    }
}
