<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}


$sql1 = "CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(100) NOT NULL,
    account_name VARCHAR(100) NULL,
    account_number VARCHAR(100) NULL,
    qr_image VARCHAR(255) NULL,
    type ENUM('transfer', 'qris') DEFAULT 'transfer',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


$sql2 = "CREATE TABLE IF NOT EXISTS studio_settings (
    id INT PRIMARY KEY DEFAULT 1,
    province VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    full_address TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";


$sql3 = "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    details TEXT,
    price_start DECIMAL(10,2),
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


$sql4 = "CREATE TABLE IF NOT EXISTS why_choose_us (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


$sql5 = "CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql1) && $conn->query($sql2) && $conn->query($sql3) && $conn->query($sql4) && $conn->query($sql5)) {
    
    $conn->query("INSERT IGNORE INTO studio_settings (id, province, city, full_address) VALUES (1, 'Kalimantan Barat', 'Pontianak', 'Jl. Contoh No. 123, Pontianak')");
    
    
    $checkServices = $conn->query("SELECT id FROM services LIMIT 1");
    if ($checkServices->num_rows === 0) {
        $conn->query("INSERT INTO services (name, description, details, price_start, image_path) VALUES 
            ('Nail Art', 'Kreasi seni pada kuku dengan berbagai desain yang dapat disesuaikan dengan keinginan anda.', 'Gel Polish, French Tips, Ombre, Marble', 30000, 'assets/img/img4.png'),
            ('Extension', 'Memberikan tambahan detail dan desain pada NailArt anda agar terlihat lebih menarik lagi.', 'Acrylic Extension, Gel Extension, Polygel', 60000, 'assets/img/img5.png'),
            ('Nail Art Kaki', 'Kreasi seni pada kuku kaki dengan berbagai desain yang dapat disesuaikan dengan keinginan anda.', 'Pedicure, Gel Polish Kaki, Spa Kaki', 35000, 'assets/img/img6.png'),
            ('Add Ons', 'Memberi tambahan pada NailArt sesuai keinginan anda dengan tambahan biaya yang tersedia.', 'Diamond, Sticker, 3D Charm, Cat Eye Effect', 2000, 'assets/img/img7.png')");
    }

    
    $checkWhy = $conn->query("SELECT id FROM why_choose_us LIMIT 1");
    if ($checkWhy->num_rows === 0) {
        $conn->query("INSERT INTO why_choose_us (title, description, icon) VALUES 
            ('Premium Quality', 'Kami hanya menggunakan gel polish pilihan dengan kualitas terbaik yang telah teruji aman untuk kuku asli. Formulanya dirancang agar warna tahan lama, berkilau sempurna, dan tetap menjaga kekuatan serta kesehatan kuku Anda tanpa membuat kuku rapuh atau rusak.', '✨'),
            ('Custom Art', 'Setiap kuku adalah kanvas seni. Anda bebas membawa referensi, ide, atau desain impian apa pun, dan nail artist profesional kami akan menerjemahkannya dengan presisi dan detail tinggi.', '🎨'),
            ('Hygienic Tools', 'Kebersihan dan keamanan adalah prioritas utama kami. Seluruh alat yang digunakan melalui proses sterilisasi menyeluruh sebelum dan sesudah pemakaian.', '🛡️')");
    }

    
    $checkFaq = $conn->query("SELECT id FROM faq LIMIT 1");
    if ($checkFaq->num_rows === 0) {
        $conn->query("INSERT INTO faq (question, answer) VALUES 
            ('Apakah harus bayar DP untuk booking?', 'Ya, Kak. Kami memerlukan DP sebesar Rp20.000 untuk mengunci slot Kakak agar tidak diambil orang lain.'),
            ('Di mana lokasi tepatnya Neydream Studio?', 'Neydream Studio berada di pusat kota, Kak! Aksesnya sangat mudah.'),
            ('Berapa lama daya tahan Nail Art di Neydream?', 'Kuku dari Neydream dijamin awet! Biasanya bertahan 3 hingga 4 minggu.'),
            ('Apakah bisa membawa referensi desain sendiri?', 'Bisa banget! Kakak boleh bawa foto referensi dari Pinterest atau Instagram.'),
            ('Kenapa namanya ganti menjadi Neydream?', 'Neydream Studio sebelumnya dikenal sebagai Glamour Nails. Kami melakukan rebranding agar tampil lebih fresh dan modern.')");
    }

    
    $checkPay = $conn->query("SELECT id FROM payment_methods LIMIT 1");
    if ($checkPay->num_rows === 0) {
        $conn->query("INSERT INTO payment_methods (method_name, account_name, account_number, type) VALUES 
            ('Transfer BCA', 'Glamour Nails Studio', '123-456-7890', 'transfer'),
            ('GoPay', 'Jazin volney', '0812-0389-443', 'transfer'),
            ('ShopeePay', 'Jazin volney', '0812-0389-443', 'transfer'),
            ('Dana', 'Jazin volney', '0812-0389-443', 'transfer')");
            
        $conn->query("INSERT INTO payment_methods (method_name, qr_image, type) VALUES 
            ('QRIS (GoPay/Shopee/Dana)', 'assets/img/qris.jpeg', 'qris')");
    }

    echo "Migration Successful! All tables are ready.";
} else {
    echo "Error during migration: " . $conn->error;
}
?>
