<?php
require '../../core/config.php';


header('Content-Type: text/plain');
header('X-Chat-Engine-Version: 4.1.0-Admin');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Admin access required']) . "!!!JSON_END!!!";
    exit;
}


try {
    $check_col = $conn->query("SHOW COLUMNS FROM users LIKE 'last_seen'");
    if ($check_col->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }
} catch (Exception $e) {}


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
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Database error']) . "!!!JSON_END!!!";
    exit;
}

$users = [];
$current_time = time();

while ($row = $result->fetch_assoc()) {
    $last_seen = !empty($row['last_seen']) ? strtotime($row['last_seen']) : 0;
    $row['is_online'] = ($current_time - $last_seen) < 300;
    $row['username'] = $row['username'] ?: 'Guest ' . $row['user_id'];
    $row['last_message'] = $row['last_message'] ?: 'Belum ada pesan';
    $users[] = $row;
}

ob_clean();


echo "!!!JSON_START!!!" . json_encode([
    'success' => true, 
    'conversations' => $users,
    'total_users' => count($users)
]) . "!!!JSON_END!!!";
exit;
