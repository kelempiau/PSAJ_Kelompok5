<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'reset_all_transactions') {
        try {
            
            $conn->query("SET FOREIGN_KEY_CHECKS = 0");
            
            
            $tables = ['reservations', 'feedback'];
            
            
            $checkChat = $conn->query("SHOW TABLES LIKE 'chat_messages'");
            if ($checkChat->num_rows > 0) {
                $tables[] = 'chat_messages';
            }

            foreach ($tables as $table) {
                $conn->query("TRUNCATE TABLE $table");
            }
            
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
            
            echo json_encode(['success' => true, 'message' => 'Semua data reservasi dan transaksi berhasil direset!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mereset data: ' . $e->getMessage()]);
        }
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Request']);
