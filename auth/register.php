<?php
require '../core/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $verification_token = bin2hex(random_bytes(16));

    
    $check = $conn->query("SELECT id FROM users WHERE email = '$email' OR username = '$username'");
    if ($check->num_rows > 0) {
        $message = "Username atau Email sudah terdaftar!";
    } else {
        $sql = "INSERT INTO users (username, email, password, phone, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $password, $phone, $verification_token);

        if ($stmt->execute()) {
            $message = "Registrasi Berhasil! ✨ Silakan login.";
            header("refresh:2;url=login.php");
            $success_register = true;
        } else {
            $message = "Terjadi kesalahan: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Ney Dream</title>
    <link rel="icon" type="image/png" href="../assets/img/neydream.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/loading.css">
    <script defer src="../js/loading.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffd9e2 0%, #ffe6f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: white;
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            color: #5f162e;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .alert {
            background: #ffe6e6;
            color: #d63060;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 15px 20px;
            margin: 10px 0;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #ea3671;
            box-shadow: 0 0 0 3px rgba(234, 54, 113, 0.1);
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #ea3671, #d63060);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(234, 54, 113, 0.3);
        }

        .link {
            margin-top: 25px;
            color: #666;
            font-size: 0.95rem;
        }

        .link a {
            color: #ea3671;
            text-decoration: none;
            font-weight: 600;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #999;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: #666;
        }

        .close-auth {
            position: absolute;
            top: 20px;
            right: 20px;
            text-decoration: none;
            color: #ccc;
            font-size: 1.5rem;
            line-height: 1;
            transition: color 0.3s;
        }

        .close-auth:hover {
            color: #ea3671;
        }        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            color: #555;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .google-btn:hover {
            background: #f9f9f9;
            border-color: #ddd;
        }

        .google-btn img {
            width: 20px;
            margin-right: 12px;
        }

        .divider {
            margin: 20px 0;
            display: flex;
            align-items: center;
            color: #ccc;
            font-size: 0.8rem;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <?php include '../includes/loading.php'; ?>
    <div class="auth-container" style="position: relative;">
        
        <a href="../index.php" class="close-auth" title="Kembali ke Beranda">✕</a>

        <h2>Join Us! ✨</h2>
        <p class="subtitle">Buat akun baru di Ney Dream</p>
        
        <?php if($message): ?>
            <div class="alert" style="<?= isset($success_register) ? 'background:#e6fffa; color:#2c7a7b;' : '' ?>"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if(!isset($success_register)): ?>
        <form method="POST" autocomplete="off">
            <input type="text" name="username" placeholder="Username" required autofocus autocomplete="off">
            <input type="email" name="email" placeholder="Email" required autocomplete="off">
            <input type="tel" name="phone" placeholder="Nomor HP (08xx)" required autocomplete="off">
            <input type="password" name="password" placeholder="Password" required minlength="6" autocomplete="new-password">
            <button type="submit">Daftar Sekarang</button>
        </form>

        <div class="divider">atau daftar dengan</div>

        <a href="google_auth.php" class="google-btn">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo">
            Daftar dengan Google
        </a>        <?php else: ?>
            <div style="margin-top: 20px; text-align: center;">
                <div style="background: #e6fffa; color: #2c7a7b; padding: 20px; border-radius: 15px; border: 1px solid #b2f5ea;">
                    <p style="font-weight: 700; font-size: 1.1rem; margin-bottom: 5px;">Registrasi Berhasil! ✨</p>
                    <p style="font-size: 0.9rem;">Akun Anda sudah siap. Mengarahkan ke halaman login...</p>
                </div>
                <a href="login.php" class="google-btn" style="margin-top: 20px;">Lanjut ke Login</a>
            </div>
        <?php endif; ?>
        
        <div class="link">
            Sudah punya akun? <a href="login.php">Masuk disini</a>
        </div>
    </div>
</body>
</html>
