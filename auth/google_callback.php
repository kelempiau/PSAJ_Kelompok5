<?php
require '../core/config.php';


$client_id = GOOGLE_CLIENT_ID;
$client_secret = GOOGLE_CLIENT_SECRET;


$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? "https" : "http";
$redirect_uri = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/auth/google_callback.php";

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    
    $token_url = "https://oauth2.googleapis.com/token";
    $post_fields = [
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];

        
        $userinfo_url = "https://www.googleapis.com/oauth2/v3/userinfo?access_token=" . $access_token;
        $userinfo_response = file_get_contents($userinfo_url);
        $user = json_decode($userinfo_response, true);

        if (isset($user['email'])) {
            $email = $user['email'];
            $google_id = $user['sub'];
            $name = $user['name'];
            $picture = $user['picture'] ?? null;

            
            $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE email = ? OR google_id = ?");
            $stmt->bind_param("ss", $email, $google_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                
                $existing_user = $result->fetch_assoc();
                
                
                $conn->query("UPDATE users SET google_id = '$google_id', profile_pic = '$picture', is_verified = 1 WHERE id = " . $existing_user['id']);
                
                $_SESSION['user_id'] = $existing_user['id'];
                $_SESSION['username'] = $existing_user['username'];
                $_SESSION['role'] = $existing_user['role'];
            } else {
                
                $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
                $password = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, google_id, profile_pic, is_verified) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->bind_param("sssss", $username, $email, $password, $google_id, $picture);
                
                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $conn->insert_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'user';
                } else {
                    die("Gagal mendaftar via Google: " . $conn->error);
                }
            }

            header("Location: ../index.php");
            exit();
        }
    } else {
        echo "Error: Gagal mendapatkan access token.";
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
