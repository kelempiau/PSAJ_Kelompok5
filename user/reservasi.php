<?php
require '../core/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Admins can see but not submit/change
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$message = "";
$messageType = ""; // success or error

// Fetch Current User Data
$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT username, phone FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$current_user = $user_query->get_result()->fetch_assoc();
$user_phone = $current_user['phone'] ?? "";
$user_name = $current_user['username'] ?? "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['Nama_Pelanggan'];
    $phone = $_POST['No_WhatsApp'];
    $date = $_POST['Tanggal_Reservasi'];
    $time = $_POST['Jam_Reservasi'];
    $service = $_POST['Layanan_Utama'];
    $addon1 = $_POST['Layanan_Tambahan'];
    $addon2 = $_POST['Layanan_Tambahan_2'];
    $notes = $_POST['Catatan'] ?? "";
    
    // Combine addons for database
    $addons_list = [];
    if($addon1 != "0") $addons_list[] = $addon1;
    if($addon2 != "0") $addons_list[] = $addon2;
    $combined_addons = implode(", ", $addons_list);
    if(empty($combined_addons)) $combined_addons = "Tanpa Tambahan";

    $payment_id = $_POST['Metode_Pembayaran'];
    $p_stmt = $conn->prepare("SELECT method_name FROM payment_methods WHERE id = ?");
    $p_stmt->bind_param("i", $payment_id);
    $p_stmt->execute();
    $payment_method = $p_stmt->get_result()->fetch_assoc()['method_name'] ?? "Unknown";

    $payment_type = $_POST['payment_type']; // 'full' or 'dp'
    $total_pay = $_POST['Total_Bayar']; // String "Rp..."
    
    // Simple mapping for service names based on price (Reverse engineering user's js logic) or just save the price/value
    // Ideally we save readable text.
    
    // Date & Time Validation
    $validTime = true;
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i');
    
    // Parse input time (e.g., "09:00 WIB" -> "09:00")
    $cleanTime = explode(' ', $time)[0]; 

    if ($date < $currentDate) {
        $validTime = false;
    } elseif ($date == $currentDate) {
        if ($cleanTime < $currentTime) {
            $validTime = false;
        }
    }

    if (!$validTime) {
        $message = "Maaf tidak bisa melakukan reservasi karena sudah melewati hari atau jam dihari ini";
        $messageType = "error";
    } else {
        // Check availability
        $checkRes = $conn->prepare("SELECT id FROM reservations WHERE reservation_date = ? AND reservation_time = ? AND status != 'cancelled'");
        $checkRes->bind_param("ss", $date, $time);
        $checkRes->execute();
        $resResult = $checkRes->get_result();
    
        $checkLock = $conn->prepare("SELECT id FROM locked_slots WHERE date = ? AND time = ?");
        $checkLock->bind_param("ss", $date, $time);
        $checkLock->execute();
        $lockResult = $checkLock->get_result();
    
        if ($resResult->num_rows > 0 || $lockResult->num_rows > 0) {
            $message = "Maaf jadwal yang anda ingin pesan sudah di reservasi";
            $messageType = "error";
        } else {
        // Handle File Upload
        $proofPath = "";
        if (!empty($_FILES["Lampiran_Bukti_Bayar"]["name"])) {
            $targetDir = "../uploads/"; // Save to root uploads folder
            if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
            $fileName = basename($_FILES["Lampiran_Bukti_Bayar"]["name"]);
            $fileName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName); // Clean filename
            $targetFilePath = $targetDir . time() . "_" . $fileName;
            if(move_uploaded_file($_FILES["Lampiran_Bukti_Bayar"]["tmp_name"], $targetFilePath)){
                $proofPath = "uploads/" . time() . "_" . $fileName; // Store path relative to root
            }
        }

        // Parse total price
        $total_price = (float)str_replace(['Rp', '.', ','], '', $total_pay);
        
        // Map payment type to db (save specified subtypes)
        $db_payment_type = $payment_type;
        
        // Calculate amount paid and balance due
        $amount_paid = (strpos($db_payment_type, 'dp') !== false) ? ($total_price / 2) : $total_price;
        
        $balance_due = $total_price - $amount_paid;

        $stmt = $conn->prepare("INSERT INTO reservations (user_id, name, phone, reservation_date, reservation_time, service_type, addons, notes, total_price, payment_method, payment_type, amount_paid, balance_due, payment_proof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssdssdds", $user_id, $name, $phone, $date, $time, $service, $combined_addons, $notes, $total_price, $payment_method, $db_payment_type, $amount_paid, $balance_due, $proofPath);
        
        if ($stmt->execute()) {
            $message = "SUCCESS_MODAL"; // Trigger for JavaScript to show popup
            $messageType = "success";
        } else {
            $message = "Gagal membuat reservasi: " . $conn->error;
            $messageType = "error";
        }
        }
    } // End of validation check
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Nail Art - Glamour Nails</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/reservasi.css?v=2">
    <?php include 'includes/loading_styles.php'; ?>
    <style>
        /* ANTIGRAVITY AD PROTECTION */
        #sb98124, #sb98124_image, #sb98124_close, .tutup2,
        div[id^="sb"][style*="display: block"], 
        div[id^="sb"][style*="position: fixed"],
        a[href*="infinityfree"] {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            z-index: -99999 !important;
        }
    </style>
    <script>
        (function(){
            setInterval(function(){
                var ads = document.querySelectorAll('#sb98124, #sb98124_image, .tutup2, div[id^="sb"][style*="fixed"]');
                ads.forEach(function(el){ el.remove(); });
            }, 500);
        })();
    </script>
    <style>
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 12px; text-align: center; }
        .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        
        .header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .back-link-icon {
            text-decoration: none;
            color: #999;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .back-link-icon:hover { color: #ea3671; }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fffafa;
            padding: 5px 12px;
            border-radius: 20px;
            border: 1px solid #ffe6f0;
            font-size: 0.85rem;
            color: #666;
        }

        .user-badge .dot {
            width: 8px;
            height: 8px;
            background: #2ecc71;
            border-radius: 50%;
        }

        .admin-tag {
            background: #ea3671;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Prevent admin from clicking booking */
        .admin-view-only {
            pointer-events: none;
            opacity: 0.7;
            filter: grayscale(0.5);
        }
        .admin-view-only button[type="submit"] {
            display: none;
        }
        .admin-notice {
            background: #fff4f4;
            color: #d63031;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
            border: 1px dashed #ff7675;
        }
        /* Logout Modal Styles */
        #logoutModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            color: #333;
        }
        /* Delete Confirmation Modal */
        #deleteConfirmModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000000;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include '../includes/loading.php'; ?>
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
        
        <?php if($is_admin): ?>
            <div class="admin-notice">
                📢 <strong>Mode Admin:</strong> Anda hanya bisa melihat tampilan halaman dan chatbot. Fitur reservasi dinonaktifkan untuk akun admin.
            </div>
        <?php endif; ?>
        


        <!-- Form action self -->
        <form action="" method="POST" enctype="multipart/form-data" class="<?= $is_admin ? 'admin-view-only' : '' ?>">
            
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="Nama_Pelanggan" value="<?= htmlspecialchars($user_name) ?>" readonly style="background: #f9f9f9; color: #888; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label for="phone">Nomor HP (WhatsApp)</label>
                <input type="tel" id="phone" name="No_WhatsApp" value="<?= htmlspecialchars($user_phone) ?>" readonly style="background: #f9f9f9; color: #888; cursor: not-allowed;">
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="date">Tanggal Reservasi</label>
                    <input type="date" id="date" name="Tanggal_Reservasi" min="<?= date('Y-m-d') ?>" required>
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
                <!-- Values assume prices as in original html -->
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

            <div class="form-group">
                <label for="addon2">Jenis Tambahan (Opsional 2)</label>
                <select id="addon2" name="Layanan_Tambahan_2" onchange="calculateTotal()">
                    <option value="0" selected>Tanpa Tambahan</option>
                    <option value="10000">Tambah Diamond (+10k)</option>
                    <option value="15000">Tambah Glitter (+15k)</option>
                    <option value="25000">Hapus Gel Lama / Removal (+25k)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Catatan Tambahan (Bebas Custom)</label>
                <textarea id="notes" name="Catatan" placeholder="Contoh: Mau desain kuku kucing, warna soft pink, dsb." style="width: 100%; padding: 12px; border: 1px solid #ffe6f0; border-radius: 12px; font-family: inherit; resize: vertical; min-height: 80px;"></textarea>
            </div>

            <div class="price-display">
                Total Biaya: <span id="total-price">Rp0</span>
                <input type="hidden" name="Total_Bayar" id="hidden-total" value="Rp0">
            </div>

            <div class="form-group">
                <label for="payment_type">Pilihan Pembayaran</label>
                <select id="payment_type" name="payment_type" onchange="calculateTotal()" required>
                    <option value="full" selected>Bayar Lunas (Full Payment)</option>
                    <option value="dp_transfer">DP 50% (Sisa Pelunasan Transfer)</option>
                    <option value="dp_cash">DP 50% (Sisa Pelunasan Cash di Studio)</option>
                </select>
                <div id="dp-warning" style="color: #e67e22; font-size: 0.8rem; font-weight: 600; margin-top: 8px; padding: 10px; background: #fffaf0; border-radius: 8px; border: 1px dashed #ffeaa7; display: none;">
                    ⚠️ Pembayaran DP wajib diselesaikan sekarang untuk konfirmasi jadwal.
                </div>
            </div>

            <div class="form-group">
                <label for="payment">Metode Pembayaran</label>
                <select id="payment" name="Metode_Pembayaran" onchange="showPaymentDetail()" required>
                    <option value="" disabled selected>Pilih metode</option>
                    <?php 
                    $p_methods = $conn->query("SELECT * FROM payment_methods WHERE is_active = 1");
                    $method_details = [];
                    while($pm = $p_methods->fetch_assoc()): 
                        $method_details[] = $pm;
                    ?>
                        <option value="<?= $pm['id'] ?>"><?= htmlspecialchars($pm['method_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <?php foreach($method_details as $pm): ?>
                <div id="detail-pm-<?= $pm['id'] ?>" class="payment-info" style="display: none; text-align: center;">
                    <?php if($pm['type'] === 'qris'): ?>
                        <p>Scan kode QRIS di bawah ini:</p>
                        <img src="../<?= htmlspecialchars($pm['qr_image']) ?>" alt="QRIS" class="qris-img" style="max-width: 200px; border-radius: 10px; margin: 10px 0;">
                    <?php else: ?>
                        <p>Silahkan transfer ke rekening berikut:</p>
                        <div class="bank-box" style="background: #fffafa; border: 1px dashed #ea3671; padding: 15px; border-radius: 12px; margin: 10px 0;">
                            <strong><?= htmlspecialchars($pm['method_name']) ?>: <?= htmlspecialchars($pm['account_number']) ?></strong><br>
                            a/n <?= htmlspecialchars($pm['account_name']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="form-group" id="proof-section">
                <label for="proof">Upload Bukti Pembayaran</label>
                <div class="upload-section">
                    <input type="file" id="proof" name="Lampiran_Bukti_Bayar" accept="image/*" required>
                </div>
                <!-- Dynamic Order Summary -->
                <div id="order-summary" style="margin-top: 20px; padding: 20px; background: #fffafa; border: 1px solid #ffe6f0; border-radius: 15px;">
                    <h4 style="color: #ea3671; margin-bottom: 10px; font-size: 0.95rem; border-bottom: 1px solid #ffe6f0; padding-bottom: 5px;">Ringkasan Pesanan:</h4>
                    <ul id="summary-list" style="list-style: none; padding: 0; font-size: 0.85rem; color: #666;">
                        <!-- JS populated -->
                    </ul>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 2px dashed #ffe6f0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: #333;">Total Pembayaran Sekarang:</span>
                        <span id="summary-pay" style="font-weight: 700; color: #ea3671; font-size: 1.1rem;">Rp0</span>
                    </div>
                </div>
                <small style="color: #ea3671; display: block; margin-top: 5px;" id="dp-note"></small>
            </div>

            <button type="submit" class="btn-submit">DAFTAR RESERVASI SEKARANG</button>
        </form>
    </div>

    <!-- Chat Popup Removed (Moved to chat.php) -->

    <!-- Chat Page Link (No Popup) -->
    <a href="help.php" class="chat-icon-bubble" id="chatIcon" title="Bantuan & Chat">
        <img src="https://cdn-icons-png.flaticon.com/512/5968/5968841.png" alt="Chat">
    </a>

    <!-- UI Core Logic (Embedded for instant response) -->
    <script>
    // Legacy toggleChat function removed.

    function confirmLogout(logoutUrl) {
        const modal = document.getElementById('logoutModal');
        if (modal) {
            modal.dataset.logoutUrl = logoutUrl;
            modal.style.display = 'flex';
        } else {
            if (confirm("Apakah Anda yakin ingin logout?")) window.location.href = logoutUrl;
        }
    }

    function handleLogoutConfirm(confirmed) {
        const modal = document.getElementById('logoutModal');
        if (confirmed) {
            window.location.href = modal.dataset.logoutUrl;
        } else {
            modal.style.display = 'none';
        }
    }
    </script>

    <!-- Main scripts: Unified Smart Chatbot -->
    <script src="../assets/js/reservasi.js"></script>
    <!-- Chatbot Script Removed (Page Specific) -->
    <script>
    async function userHeartbeat() {
        try {
            await fetch('../api/user/heartbeat.php');
        } catch (e) {}
    }
    setInterval(userHeartbeat, 30000);
    userHeartbeat();
    </script>
    
    <!-- Logout Confirmation Modal -->
    <div id="logoutModal">
        <div class="modal-content">
            <div style="font-size: 3rem; margin-bottom: 20px;">🚪</div>
            <h3 style="margin-bottom: 15px;">Yakin ingin Logout?</h3>
            <p style="color: #666; margin-bottom: 30px;">Huhu, Kakak akan keluar dari akun Neydream. Sampai jumpa di lain waktu ya! ✨</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button onclick="handleLogoutConfirm(false)" class="btn-submit" style="background: #ccc; flex: 1; margin: 0; box-shadow: none;">Tidak</button>
                <button onclick="handleLogoutConfirm(true)" class="btn-submit" style="flex: 1; margin: 0;">Ya, Logout</button>
            </div>
        </div>
    </div>

    <!-- Delete Chat Confirmation Modal -->
    <div id="deleteConfirmModal">
        <div class="modal-content">
            <div style="font-size: 3rem; margin-bottom: 20px;">🗑️</div>
            <h3 style="margin-bottom: 15px;">Hapus Riwayat Chat?</h3>
            <p style="color: #666; margin-bottom: 30px;">Apakah anda ingin menghapus chat ini? Riwayat chat yang sudah dihapus tidak dapat dikembalikan.</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button onclick="closeDeleteModal()" class="btn-submit" style="background: #ccc; flex: 1; margin: 0; box-shadow: none;">Tidak</button>
                <button onclick="executeClearChat()" class="btn-submit" style="flex: 1; margin: 0; background: #e74c3c;">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" style="display: <?= ($message === 'SUCCESS_MODAL') ? 'flex' : 'none' ?>; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 20000000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="modal-content" style="max-width: 450px; padding: 40px 30px; border: none; animation: modalPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <div style="font-size: 4.5rem; margin-bottom: 20px;">✨</div>
            <h2 style="color: #ea3671; margin-bottom: 15px; font-weight: 700;">Pembayaran Berhasil!</h2>
            <p style="color: #666; line-height: 1.6; margin-bottom: 30px;">
                Terima kasih, pembayaran reservasi Kakak telah kami terima. Admin akan segera memverifikasi jadwal Anda. Cek menu <strong>Riwayat</strong> untuk memantau statusnya ya!
            </p>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="../index.php" class="btn-submit" style="margin: 0; text-decoration: none; display: block;">KEMBALI KE BERANDA</a>
                <a href="history.php" style="color: #999; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Lihat Riwayat Transaksi</a>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" style="display: <?= ($messageType === 'error' && !empty($message)) ? 'flex' : 'none' ?>; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 20000000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="modal-content" style="max-width: 450px; padding: 40px 30px; border: none; border-top: 8px solid #ef4444; animation: modalPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <div style="font-size: 3.5rem; margin-bottom: 20px;">🚫</div>
            <h2 style="color: #ef4444; margin-bottom: 15px; font-weight: 700;">Gagal Reservasi</h2>
            <p style="color: #666; line-height: 1.6; margin-bottom: 30px; font-weight: 500;">
                <?= htmlspecialchars($message) ?>
            </p>
            <button onclick="document.getElementById('errorModal').style.display='none'" class="btn-submit" style="background: #ef4444; margin: 0; width: 100%;">Coba Lagi</button>
        </div>
    </div>

    <style>
        @keyframes modalPop {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-item .price {
            font-weight: 600;
            color: #333;
        }
    </style>

    <script>
    // Link behavior is now handled naturally by the <a> tag.
    // No JS interception needed.
    </script>
</body>
</html>
