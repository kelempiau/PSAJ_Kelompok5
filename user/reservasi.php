<?php
require '../core/config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}


if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit();
}

$message = "";
$messageType = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['Nama_Pelanggan'];
    $phone = $_POST['No_WhatsApp'];
    $date = $_POST['Tanggal_Reservasi'];
    $time = $_POST['Jam_Reservasi'];
    $service = $_POST['Layanan_Utama'];
    $addon = $_POST['Layanan_Tambahan'];
    $payment_method = $_POST['Metode_Pembayaran'];
    $total_pay = $_POST['Total_Bayar'];
    
    
    
    $checkRes = $conn->prepare("SELECT id FROM reservations WHERE reservation_date = ? AND reservation_time = ? AND status != 'cancelled'");
    $checkRes->bind_param("ss", $date, $time);
    $checkRes->execute();
    $resResult = $checkRes->get_result();

    $checkLock = $conn->prepare("SELECT id FROM locked_slots WHERE date = ? AND time = ?");
    $checkLock->bind_param("ss", $date, $time);
    $checkLock->execute();
    $lockResult = $checkLock->get_result();

    if ($resResult->num_rows > 0 || $lockResult->num_rows > 0) {
        $message = "Maaf, jadwal pada $date jam $time sudah terisi/dikunci. Mohon pilih waktu lain.";
        $messageType = "error";
    } else {
        
        $proofPath = "";
        if (!empty($_FILES["Lampiran_Bukti_Bayar"]["name"])) {
            $targetDir = "../uploads/";
            if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
            $fileName = basename($_FILES["Lampiran_Bukti_Bayar"]["name"]);
            $fileName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
            $targetFilePath = $targetDir . time() . "_" . $fileName;
            if(move_uploaded_file($_FILES["Lampiran_Bukti_Bayar"]["tmp_name"], $targetFilePath)){
                $proofPath = "uploads/" . time() . "_" . $fileName;
            }
        }

        
        $numericPrice = (float)str_replace(['Rp', '.', ','], '', $total_pay);

        $stmt = $conn->prepare("INSERT INTO reservations (user_id, name, phone, reservation_date, reservation_time, service_type, addons, total_price, payment_method, payment_proof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssdss", $user_id, $name, $phone, $date, $time, $service, $addon, $numericPrice, $payment_method, $proofPath);
        
        if ($stmt->execute()) {
            $message = "Reservasi Berhasil! Admin kami akan menghubungi Anda segera.";
            $messageType = "success";
        } else {
            $message = "Gagal membuat reservasi: " . $conn->error;
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Nail Art - Glamour Nails</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/reservasi.css">
    <style>
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 12px; text-align: center; }
        .alert.error { background-color: 
        .alert.success { background-color: 
        
        .header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 10px 0;
            border-bottom: 1px solid 
        }

        .back-link-icon {
            text-decoration: none;
            color: 
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .back-link-icon:hover { color: 

        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: 
            padding: 5px 12px;
            border-radius: 20px;
            border: 1px solid 
            font-size: 0.85rem;
            color: 
        }

        .user-badge .dot {
            width: 8px;
            height: 8px;
            background: 
            border-radius: 50%;
        }

        .admin-tag {
            background: 
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <a href="../index.php" class="back-link-icon" title="Kembali ke Beranda">✕</a>
            <div class="user-badge">
                <span class="dot"></span>
                <span>Login: <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong></span>
                <?php if(($_SESSION['role'] ?? '') === 'admin'): ?>
                    <span class="admin-tag">ADMIN</span>
                <?php endif; ?>
            </div>
        </div>
        <h2>Booking Nail Art</h2>
        
        <?php if($message): ?>
            <div class="alert <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        
        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                
                <input type="text" id="name" name="Nama_Pelanggan" placeholder="Masukkan nama anda" required>
            </div>

            <div class="form-group">
                <label for="phone">Nomor HP (WhatsApp)</label>
                <input type="tel" id="phone" name="No_WhatsApp" placeholder="0812xxxx" required>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="date">Tanggal Reservasi</label>
                    <input type="date" id="date" name="Tanggal_Reservasi" required>
                </div>

                <div class="form-group">
                    <label for="time">Jam Reservasi</label>
                    <select id="time" name="Jam_Reservasi" required>
                        <option value="" disabled selected>Pilih Jam</option>
                        <option value="09:00 WIB">09:00 WIB</option>
                        <option value="11:00 WIB">11:00 WIB</option>
                        <option value="13:00 WIB">13:00 WIB</option>
                        <option value="15:00 WIB">15:00 WIB</option>
                        <option value="17:00 WIB">17:00 WIB</option>
                        <option value="19:00 WIB">19:00 WIB</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="type">Jenis Nail Art Utama</label>
                
                <select id="type" name="Layanan_Utama" onchange="calculateTotal()" required>
                    <option value="0" disabled selected>Pilih jenis layanan</option>
                    <option value="50000">Gel Polish - Rp50.000</option>
                    <option value="75000">French Manicure - Rp75.000</option>
                    <option value="150000">Acrylic Extension - Rp150.000</option>
                    <option value="200000">Custom 3D Nail Art - Rp200.000</option>
                </select>
            </div>

            <div class="form-group">
                <label for="addon">Jenis Tambahan (Opsional)</label>
                <select id="addon" name="Layanan_Tambahan" onchange="calculateTotal()">
                    <option value="0" selected>Tanpa Tambahan</option>
                    <option value="10000">Tambah Diamond (+10k)</option>
                    <option value="15000">Tambah Glitter (+15k)</option>
                    <option value="25000">Hapus Gel Lama / Removal (+25k)</option>
                </select>
            </div>

            <div class="price-display">
                Total Biaya: <span id="total-price">Rp0</span>
                <input type="hidden" name="Total_Bayar" id="hidden-total" value="Rp0">
            </div>

            <div class="form-group">
                <label for="payment">Metode Pembayaran</label>
                <select id="payment" name="Metode_Pembayaran" onchange="showPaymentDetail()" required>
                    <option value="" disabled selected>Pilih metode</option>
                    <option value="bca">Transfer BCA</option>
                    <option value="qris">QRIS (GoPay/OVO/Dana)</option>
                </select>
            </div>

            <div id="detail-bca" class="payment-info" style="display: none;">
                <p>Silahkan transfer ke rekening berikut:</p>
                <div class="bank-box">
                    <strong>BCA: 123-456-7890</strong><br>
                    a/n Glamour Nails Studio
                </div>
            </div>

            <div id="detail-qris" class="payment-info" style="display: none; text-align: center;">
                <p>Scan kode QRIS di bawah ini:</p>
                
                <img src="../assets/img/qris.jpeg" alt="QRIS" class="qris-img" onerror="this.src='https://via.placeholder.com/200?text=QRIS+Placeholder'">
            </div>

            <div class="form-group">
                <label for="proof">Upload Bukti Pembayaran</label>
                <div class="upload-section">
                    <input type="file" id="proof" name="Lampiran_Bukti_Bayar" accept="image/*" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">DAFTAR RESERVASI SEKARANG</button>
        </form>
    </div>

    
    <div class="chat-container" id="chatContainer">
        <div class="chat-header">
            <div class="header-info">
                <div class="admin-avatar">
                    <img src="https://cdn-icons-png.flaticon.com/512/1144/1144760.png" alt="Admin">
                    <span class="online-status"></span>
                </div>
                <div>
                    <h4>Asisten Neydream</h4>
                    <p>Online</p>
                </div>
            </div>
            <button class="close-chat" onclick="toggleChat()">×</button>
        </div>

        <div class="chat-box" id="chatBox">
            <div class="message admin">
                Halo Kak! ✨ Selamat datang di Neydream Studio. Ada yang bisa kami bantu hari ini?
            </div>
        </div>

        <div class="chat-input-area">
            <input type="text" id="userInput" placeholder="Tulis pesan..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">➤</button>
        </div>
    </div>

    <div class="chat-icon-bubble" id="chatIcon" onclick="toggleChat()">
        <img src="https://cdn-icons-png.flaticon.com/512/5968/5968841.png" alt="Chat">
    </div>

    
    <script src="../assets/js/ai_assistant.js"></script>
</body>
</html>

