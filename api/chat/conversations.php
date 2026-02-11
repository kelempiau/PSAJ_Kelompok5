<?php
require '../../core/config.php';

header('Content-Type: application/json');

// Check Admin Session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit;
}

// Auto-migration: Ensure last_seen column exists for online tracking
try {
    $check_col = $conn->query("SHOW COLUMNS FROM users LIKE 'last_seen'");
    if ($check_col->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }
} catch (Exception $e) {
    // Silent fail if already exists or other DB issues
}

// Fetch ALL users with role 'user' and their latest conversation/message status
// We use a LEFT JOIN to ensure all users are listed, even without chat history
$sql = "SELECT 
            u.id as user_id, 
            u.username, 
            u.email, 
            u.last_seen,
            u.profile_pic,
            c.id as conversation_id, 
            c.status as conv_status,
            COALESCE((SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND is_read = FALSE AND sender_type = 'customer'), 0) as unread_count,
            COALESCE((SELECT 
                CASE 
                    WHEN (message IS NOT NULL AND message != '') THEN message 
                    WHEN (image_path IS NOT NULL AND image_path != '') THEN '📷 (Kirim Gambar)'
                    ELSE 'Belum ada pesan'
                END
                FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1), 'Belum ada pesan') as last_message,
            (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_msg_time
        FROM users u
        LEFT JOIN conversations c ON u.id = c.user_id
        WHERE u.role = 'user'
        ORDER BY (c.id IS NOT NULL) DESC, last_msg_time DESC, u.username ASC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
    exit;
}

$users = [];
$current_time = time();

while ($row = $result->fetch_assoc()) {
    // Online within 5 minutes
    $last_seen = !empty($row['last_seen']) ? strtotime($row['last_seen']) : 0;
    $row['is_online'] = ($current_time - $last_seen) < 300;
    
    // Formatting values
    $row['username'] = $row['username'] ?: 'Guest ' . $row['user_id'];
    // Logic for 🎥 preview is already handled in the SQL CASE statement above.
    $row['last_message'] = $row['last_message'] ?: '<i>Belum ada pesan</i>';
    $users[] = $row;
}

ob_clean();
echo json_encode([
    'success' => true, 
    'conversations' => $users,
    'total_users' => count($users)
]);
exit;

