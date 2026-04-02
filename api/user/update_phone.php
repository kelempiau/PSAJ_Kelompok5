<?php
require '../../core/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $phone = $_POST['phone'] ?? '';
    
    // Validate phone simply
    if (!empty($phone)) {
        $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE id = ?");
        $stmt->bind_param("si", $phone, $_SESSION['user_id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
}
echo json_encode(['success' => false]);
?>
