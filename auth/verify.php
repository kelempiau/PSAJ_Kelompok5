<?php
require '../core/config.php';

$message = '';
$status = 'error';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        
        
        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $update->bind_param("i", $user_id);
        
        if ($update->execute()) {
            
            $userDataQuery = $conn->query("SELECT id, username, role FROM users WHERE id = $user_id");
            $userData = $userDataQuery->fetch_assoc();
            
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['role'] = $userData['role'];
            
            $message = "Akun berhasil diverifikasi! Mengarahkan Anda ke Beranda...";
            $status = 'success';
            header("refresh:2;url=../index.php"); 
        } else {
            $message = "Terjadi kesalahan saat memverifikasi.";
        }
    } else {
        $message = "Token tidak valid atau akun sudah diverifikasi.";
    }
} else {
    $message = "Token tidak ditemukan.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun - Ney Dream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffd9e2 0%, #ffe6f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1) ;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        h2 { color: #5f162e; margin-bottom: 20px; }
        .message { margin-bottom: 30px; font-size: 1.1rem; }
        .success { color: #2ecc71; }
        .error { color: #e74c3c; }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #ea3671, #d63060);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verifikasi Akun ✨</h2>
        <p class="message <?= $status ?>"><?= $message ?></p>
        <a href="login.php" class="btn">Kembali ke Login</a>
    </div>
</body>
</html>
