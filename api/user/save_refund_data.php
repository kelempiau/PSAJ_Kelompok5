<?php
require '../../core/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit();
}

$booking_id = intval($_POST['booking_id']);
$user_id = $_SESSION['user_id'];
$method = $_POST['refund_method'];
$account = $_POST['refund_account'];
$other_method = $_POST['other_method'] ?? '';

// Final target method name
$final_target = ($method === 'Lainnya') ? $other_method : $method;

if (empty($final_target) || empty($account)) {
    echo json_encode(['success' => false, 'error' => 'Data tidak lengkap']);
    exit();
}

// Verify booking belongs to user and is approved for refund
$stmt = $conn->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ? AND refund_status = 'approved'");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Pemesanan tidak ditemukan atau belum disetujui refund-nya']);
    exit();
}

// Update refund account data
$update = $conn->prepare("UPDATE reservations SET refund_target = ?, refund_account = ? WHERE id = ?");
$update->bind_param("ssi", $final_target, $account, $booking_id);

if ($update->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>
