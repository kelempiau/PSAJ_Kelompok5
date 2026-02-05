<?php
// Standalone DB Debug Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Database Debugger</h3>";

// 1. Check Config File
if (!file_exists('../../core/config.php')) {
    die("❌ config.php not found!");
}
require '../../core/config.php';
echo "✅ config.php loaded.<br>";

// 2. Check Connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Database connected.<br>";

// 3. Check Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session ID: " . session_id() . "<br>";
echo "Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Not set') . "<br>";

// 4. Test Query (The one failing in API)
$sql = "SELECT 
            u.id as user_id, 
            u.username, 
            u.email, 
            u.last_seen,
            c.id as conversation_id
        FROM users u
        LEFT JOIN conversations c ON u.id = c.user_id
        WHERE u.role = 'user'
        LIMIT 5";

$result = $conn->query($sql);

if (!$result) {
    echo "❌ Query Failed: " . $conn->error . "<br>";
} else {
    echo "✅ Query Successful. Found " . $result->num_rows . " users.<br>";
    while($row = $result->fetch_assoc()) {
        echo "- User: " . $row['username'] . " (ID: " . $row['user_id'] . ")<br>";
    }
}
?>
