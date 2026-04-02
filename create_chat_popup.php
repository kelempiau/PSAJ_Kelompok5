<?php
$lines = file('d:/web reservasi/index.php');
$chatLines = array_slice($lines, 1326, 1695 - 1326); // Lines 1327 to 1695
$content = implode('', $chatLines);

// Variables
$content = str_replace('htmlspecialchars($username)', 'htmlspecialchars($user_name ?? $username ?? \'\')', $content);

// API URL - replace JS function
$content = preg_replace('/function getAPIUrl\(base\) \{.*?\}/s', "function getAPIUrl(base) { return '../' + base; }", $content);

// Replace URLs that already might be absolute or relative paths
// But getAPIUrl handles it. Wait, the images like 'https://...' are fine.
// What about api/chat/get_status.php etc? getAPIUrl handles them.

file_put_contents('d:/web reservasi/includes/chat_popup.php', $content);
echo "Extracted correctly.";
?>
