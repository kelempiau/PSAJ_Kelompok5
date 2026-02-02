<?php
require '../core/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];


    $check = $conn->query("SELECT id FROM users WHERE email = '$email' OR username = '$username'");
    if ($check->num_rows > 0) {
        $message = "Username atau Email sudah terdaftar!";
    } else {
        $sql = "INSERT INTO users (username, email, password, phone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $phone);

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';
            $_SESSION['user_id'] = $conn->insert_id;
            header("Location: ../index.php");
            exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, 
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
            color: 
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .subtitle {
            color: 
            margin-bottom: 30px;
        }

        .alert {
            background: 
            color: 
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 15px 20px;
            margin: 10px 0;
            border: 2px solid 
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: 
            box-shadow: 0 0 0 3px rgba(234, 54, 113, 0.1);
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, 
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
            color: 
            font-size: 0.95rem;
        }

        .link a {
            color: 
            text-decoration: none;
            font-weight: 600;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: 
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: 
        }

        .close-auth {
            position: absolute;
            top: 20px;
            right: 20px;
            text-decoration: none;
            color: 
            font-size: 1.5rem;
            line-height: 1;
            transition: color 0.3s;
        }

        .close-auth:hover {
            color: 
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 40px 25px;
            }

            h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container" style="position: relative;">

        <a href="../index.php" class="close-auth" title="Kembali ke Beranda">✕</a>

        <h2>Join Us! ✨</h2>
        <p class="subtitle">Buat akun baru di Ney Dream</p>
        
        <?php if($message): ?>
            <div class="alert"><?= $message ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="phone" placeholder="Nomor HP (08xx)" required>
            <input type="password" name="password" placeholder="Password" required minlength="6">
            <button type="submit">Daftar Sekarang</button>
        </form>
        
        <div class="link">
            Sudah punya akun? <a href="login.php">Masuk disini</a>
        </div>
    </div>
</body>
</html>

