<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Force Login to submit feedback
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Silakan login terlebih dahulu untuk mengirim masukan.'); window.location.href='login.php';</script>";
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO feedback (user_id, name, whatsapp_number, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $name, $whatsapp, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Terima kasih atas masukannya! ✨'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim masukan. Silakan coba lagi.'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>
