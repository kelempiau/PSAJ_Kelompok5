<?php
// Test script untuk debug conversations API
require '../core/config.php';

echo "=== TEST CONVERSATIONS API ===\n\n";

// 1. Check if last_seen column exists
echo "1. Checking last_seen column...\n";
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'last_seen'");
if ($check && $check->num_rows > 0) {
    echo "   ✅ last_seen column EXISTS\n\n";
} else {
    echo "   ❌ last_seen column MISSING - Run update_db_online_status.php first!\n\n";
}

// 2. Count users
echo "2. Total users in database...\n";
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "   Total users: " . $row['count'] . "\n\n";
} else {
    echo "   ❌ Error: " . $conn->error . "\n\n";
}

// 3. Test the actual query from conversations.php
echo "3. Testing conversations query...\n";
$sql = "SELECT u.id as user_id, u.username, u.email, u.last_seen,
               c.id as conversation_id, c.status as conv_status,
               (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND is_read = FALSE AND sender_type = 'customer') as unread_count,
               (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message
        FROM users u
        LEFT JOIN conversations c ON u.id = c.user_id
        WHERE u.role = 'user'
        ORDER BY (c.last_message_at IS NULL), c.last_message_at DESC, u.username ASC";

$result = $conn->query($sql);

if ($result) {
    echo "   ✅ Query successful!\n";
    echo "   Found " . $result->num_rows . " users\n\n";
    
    echo "4. User details:\n";
    while ($row = $result->fetch_assoc()) {
        $current_time = time();
        $last_seen = strtotime($row['last_seen']);
        $is_online = ($current_time - $last_seen) < 60;
        
        echo "   - " . $row['username'] . " (" . $row['email'] . ")\n";
        echo "     User ID: " . $row['user_id'] . "\n";
        echo "     Conversation ID: " . ($row['conversation_id'] ?? 'NULL') . "\n";
        echo "     Online: " . ($is_online ? 'YES' : 'NO') . "\n";
        echo "     Last Message: " . ($row['last_message'] ?? 'No messages') . "\n\n";
    }
} else {
    echo "   ❌ Query FAILED: " . $conn->error . "\n\n";
}

echo "=== END TEST ===\n";
?>
