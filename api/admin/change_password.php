<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';
    $admin_id = $_SESSION['user_id'];

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi.']);
        exit();
    }

    if ($new_pass !== $confirm_pass) {
        echo json_encode(['success' => false, 'message' => 'Konfirmasi password baru tidak cocok.']);
        exit();
    }

    if (strlen($new_pass) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password baru minimal 6 karakter.']);
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $admin_data = $stmt->get_result()->fetch_assoc();

    if ($admin_data && password_verify($current_pass, $admin_data['password'])) {
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $new_hash, $admin_id);
        
        if ($update->execute()) {
            $_SESSION['admin_temp_pass'] = $new_pass;
            echo json_encode(['success' => true, 'message' => 'Password berhasil diperbarui!', 'new_pass' => $new_pass]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui database.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Password saat ini salah.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
