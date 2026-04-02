<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

$results = [];


$sql1 = "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    details TEXT,
    price_start DECIMAL(10,2),
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


$sql2 = "CREATE TABLE IF NOT EXISTS why_choose_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


$sql3 = "CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


$sql4 = "CREATE TABLE IF NOT EXISTS studio_settings (
    id INT PRIMARY KEY DEFAULT 1,
    province VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    full_address TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql1)) $results[] = "✅ Table 'services' ready."; else $results[] = "❌ Error 'services': " . $conn->error;
if ($conn->query($sql2)) $results[] = "✅ Table 'why_choose_us' ready."; else $results[] = "❌ Error 'why_choose_us': " . $conn->error;
if ($conn->query($sql3)) $results[] = "✅ Table 'faq' ready."; else $results[] = "❌ Error 'faq': " . $conn->error;
if ($conn->query($sql4)) $results[] = "✅ Table 'studio_settings' ready."; else $results[] = "❌ Error 'studio_settings': " . $conn->error;


$conn->query("INSERT IGNORE INTO studio_settings (id, province, city, full_address) VALUES (1, 'Jawa Tengah', 'Purwokerto', 'Dusun Sokawera, Rempoah, Baturaden, Banyumas Regency, Central Java 53126')");


$checkColUser = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_pic'");
if ($checkColUser->num_rows == 0) {
    if ($conn->query("ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL")) {
        $results[] = "✅ Column 'profile_pic' added to 'users'.";
    }
}

$checkColAdmin = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'admin_profile_pic'");
if ($checkColAdmin->num_rows == 0) {
    if ($conn->query("ALTER TABLE studio_settings ADD COLUMN admin_profile_pic VARCHAR(255) DEFAULT NULL")) {
        $results[] = "✅ Column 'admin_profile_pic' added to 'studio_settings'.";
    }
}

$checkServices = $conn->query("SELECT id FROM services LIMIT 1");
if ($checkServices->num_rows === 0) {
    $conn->query("INSERT INTO services (name, description, details, price_start, image_path) VALUES 
        ('Nail Art', 'Kreasi seni pada kuku dengan berbagai desain yang dapat disesuaikan dengan keinginan anda.', 'Gel Polish, French Tips, Ombre, Marble', 30000, 'assets/img/img4.png'),
        ('Extension', 'Memberikan tambahan detail dan desain pada NailArt anda agar terlihat lebih menarik lagi.', 'Acrylic Extension, Gel Extension, Polygel', 60000, 'assets/img/img5.png'),
        ('Nail Art Kaki', 'Kreasi seni pada kuku kaki dengan berbagai desain yang dapat disesuaikan dengan keinginan anda.', 'Pedicure, Gel Polish Kaki, Spa Kaki', 35000, 'assets/img/img6.png'),
        ('Add Ons', 'Memberi tambahan pada NailArt sesuai keinginan anda dengan tambahan biaya yang tersedia.', 'Diamond, Sticker, 3D Charm, Cat Eye Effect', 2000, 'assets/img/img7.png')");
    $results[] = "✨ Initial services data inserted.";
}

$checkWhy = $conn->query("SELECT id FROM why_choose_us LIMIT 1");
if ($checkWhy->num_rows === 0) {
    $conn->query("INSERT INTO why_choose_us (title, description, icon) VALUES 
        ('Premium Quality', 'Kami menggunakan bahan terbaik yang aman untuk kuku asli.', '✨'),
        ('Custom Art', 'Setiap kuku adalah kanvas seni yang eksklusif untuk Karakter Anda.', '🎨'),
        ('Hygienic Tools', 'Seluruh alat melalui proses sterilisasi menyeluruh.', '🛡️')");
    $results[] = "✨ Initial explanations data inserted.";
}

setcookie('force_refresh', '1', time() + 5, '/'); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Fixer</title>
    <style>
        body { font-family: sans-serif; padding: 50px; background: #fdf2f8; color: #831843; text-align: center; }
        .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
        .success { color: #059669; font-weight: bold; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 30px; background: #ea3671; color: white; text-decoration: none; border-radius: 10px; margin-top: 30px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🛠️ Database Fixer</h1>
        <p>Proses sinkronisasi tabel database sedang berjalan...</p>
        <div style="text-align: left; margin: 20px 0; border: 1px solid #ffccdd; padding: 15px; border-radius: 10px; background: #fff;">
            <?php foreach($results as $r) echo "<div>$r</div>"; ?>
        </div>
        <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
        <p style="font-size: 12px; margin-top:20px; color: #999;">Jika masih ada error, pastikan nama database di core/config.php sudah benar.</p>
    </div>
</body>
</html>
