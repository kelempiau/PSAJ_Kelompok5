<?php
require '../core/config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $booking_id = $_POST['booking_id'];
    $reason = trim($_POST['reason']);

    
    $check = $conn->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $booking_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        
        
        if ($booking['refund_status']) {
            $_SESSION['error'] = "Booking ini sudah memiliki request refund.";
            header("Location: history.php");
            exit();
        }

        
        $update = $conn->prepare("UPDATE reservations SET refund_status = 'pending', refund_reason = ?, refund_date = NOW() WHERE id = ?");
        $update->bind_param("si", $reason, $booking_id);
        
        if ($update->execute()) {
            $_SESSION['success'] = "Request refund berhasil dikirim. Menunggu persetujuan admin.";
        } else {
            $_SESSION['error'] = "Gagal mengirim request refund.";
        }
    } else {
        $_SESSION['error'] = "Booking tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: refund_status.php");
exit();
?>
