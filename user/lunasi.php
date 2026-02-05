<?php
require_once '../core/config.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: history.php");
    exit();
}

// Fetch reservation details
if (isset($_SESSION['admin_id'])) {
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
}
$stmt->execute();
$reservation = $stmt->get_result()->fetch_assoc();

if (!$reservation || $reservation['balance_due'] <= 0) {
    header("Location: history.php");
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['Metode_Pembayaran'];
    
    // Handle File Upload
    $proofPath = "";
    if (isset($_FILES['bukti_pelunasan']) && $_FILES['bukti_pelunasan']['error'] == 0) {
        $uploadDir = '../uploads/proofs/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $filename = time() . '_final_' . $_FILES['bukti_pelunasan']['name'];
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['bukti_pelunasan']['tmp_name'], $targetPath)) {
            $proofPath = 'uploads/proofs/' . $filename;
            
            // Update Database
            $new_amount = $reservation['amount_paid'] + $reservation['balance_due'];
            $stmt = $conn->prepare("UPDATE reservations SET amount_paid = ?, balance_due = 0, final_payment_proof = ? WHERE id = ?");
            $stmt->bind_param("dsi", $new_amount, $proofPath, $id);
            
            if ($stmt->execute()) {
                $message = "SUCCESS_MODAL";
            } else {
                $message = "Gagal memperbarui data: " . $conn->error;
            }
        } else {
            $message = "Gagal mengunggah file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunasi Sisa Pembayaran - Neydream</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/reservasi.css">
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
        .lunasi-card {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08); /* Smoother shadow */
            max-width: 500px;
            margin: 50px auto;
        }
        .info-summary {
            text-align: center;
            margin-bottom: 35px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee; /* Light divider instead of box */
        }
        .info-summary span {
            display: block;
            font-size: 0.95rem;
            color: #888;
            margin-bottom: 8px;
        }
        .info-summary strong {
            font-size: 2.2rem;
            color: #ea3671;
            letter-spacing: -1px;
        }
        .qris-img {
            max-width: 250px;
            border-radius: 15px;
            margin: 15px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body style="background-color: #fff1f2; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: 'Outfit', sans-serif;">
    <div class="lunasi-card" style="background: white; padding: 50px; border-radius: 40px; box-shadow: 0 30px 60px rgba(0,0,0,0.1); max-width: 500px; width: 90%;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 3rem; margin-bottom: 15px;">💳</div>
            <h2 style="color: #ea3671; margin: 0; font-weight: 700; font-size: 1.8rem;">Pelunasan Sisa</h2>
            <p style="color: #888; font-size: 0.95rem; margin-top: 5px;">Yuk selesaikan sisa tagihan Kakak! ✨</p>
        </div>
        
        <div class="info-summary" style="text-align: center; margin-bottom: 35px; padding-bottom: 25px; border-bottom: 1px dashed #ffd1dc;">
            <span style="display: block; font-size: 1rem; color: #aaa; margin-bottom: 5px;">Total Sisa Bayar:</span>
            <strong style="font-size: 2.5rem; color: #ea3671; letter-spacing: -1px;">Rp<?= number_format($reservation['balance_due']) ?></strong>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; color: #ea3671; margin-bottom: 10px; font-size: 0.9rem;">Pilih Metode Pembayaran</label>
                <select name="Metode_Pembayaran" onchange="showPaymentDetail(this.value)" required style="width: 100%; padding: 15px; border-radius: 15px; border: 1px solid #ffe4e6; background: #fffafb; font-family: inherit; font-size: 1rem; color: #ea3671;">
                    <option value="" disabled selected>Pilih metode</option>
                    <option value="bca">Transfer BCA</option>
                    <option value="qris">QRIS (GoPay/OVO/Dana)</option>
                    <option value="gopay">GoPay</option>
                    <option value="shopeepay">ShopeePay</option>
                    <option value="dana">Dana</option>
                </select>
            </div>

            <!-- Payment Details (Appears only if selected) -->
            <div id="detail-bca" class="payment-info" style="display: none; padding: 20px; background: #fffafb; border-radius: 20px; margin-bottom: 25px; text-align: center; border: 1px solid #ffe4e6;">
                <p style="font-size: 0.85rem; color: #ea3671; margin-bottom: 5px; opacity: 0.7;">Transfer ke Rekening:</p>
                <strong style="color: #ea3671; font-size: 1.1rem;">BCA: 123-456-7890</strong><br>
                <span style="font-size: 0.9rem; color: #666;">a/n Glamour Nails Studio</span>
            </div>

            <div id="detail-qris" class="payment-info" style="display: none; text-align: center; margin-bottom: 25px; background: #fffafb; padding: 20px; border-radius: 20px; border: 1px solid #ffe4e6;">
                <p style="font-size: 0.9rem; color: #ea3671; font-weight: 600; margin-bottom: 15px;">Scan QRIS di Bawah Ini:</p>
                <img src="../assets/img/qris.jpeg" alt="QRIS" class="qris-img" style="display: block; margin: 0 auto; width: 220px; border-radius: 12px; box-shadow: 0 10px 25px rgba(234, 54, 113, 0.15);" onerror="this.src='https://via.placeholder.com/200?text=QRIS+General'">
            </div>

            <div id="detail-wallet" class="payment-info" style="display: none; padding: 20px; background: #fffafb; border-radius: 20px; margin-bottom: 25px; text-align: center; border: 1px solid #ffe4e6;">
                <p style="font-size: 0.85rem; color: #ea3671; margin-bottom: 5px; opacity: 0.7;">Nomor Tujuan Transfer:</p>
                <strong style="color: #ea3671; font-size: 1.2rem;">0812-0389-443</strong><br>
                <span style="font-size: 0.9rem; color: #666;">a/n Jazin volney</span>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label style="display: block; font-weight: 600; color: #ea3671; margin-bottom: 10px; font-size: 0.9rem;">Upload Bukti Pelunasan</label>
                <input type="file" name="bukti_pelunasan" accept="image/*" required style="width: 100%; padding: 12px; border: 1px solid #ffe4e6; border-radius: 15px; background: #fff; font-family: inherit;">
            </div>

            <button type="submit" class="btn-submit" style="width: 100%; padding: 18px; border-radius: 20px; background: #ea3671; color: white; border: none; font-weight: 700; font-size: 1.1rem; cursor: pointer; box-shadow: 0 10px 30px rgba(234, 54, 113, 0.3); transition: transform 0.2s;">
                KONFIRMASI PELUNASAN
            </button>
            <a href="history.php" style="display: block; text-align: center; margin-top: 20px; color: #aaa; text-decoration: none; font-size: 0.9rem; font-weight: 500;">Batal & Kembali</a>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" style="display: <?= ($message === 'SUCCESS_MODAL') ? 'flex' : 'none' ?>; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 20000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="modal-content" style="background:white; max-width: 400px; padding: 40px; border-radius: 30px; text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 20px;">✨</div>
            <h2 style="color: #ea3671; font-weight: 700;">Pelunasan Berhasil!</h2>
            <p style="color: #666; margin-bottom: 30px; line-height: 1.5;">Bukti pelunasan telah kami terima. Jadwal Kakak segera kami konfirmasi cantik ya! ✨</p>
            <a href="history.php" class="btn-submit" style="text-decoration: none; display: block; padding: 15px;">LIHAT RIWAYAT</a>
        </div>
    </div>

    <script>
        function showPaymentDetail(method) {
            // Hide all
            document.querySelectorAll('.payment-info').forEach(el => el.style.display = 'none');
            
            // Show selected
            if (method === 'bca') {
                document.getElementById('detail-bca').style.display = 'block';
            } else if (method === 'qris') {
                document.getElementById('detail-qris').style.display = 'block';
            } else if (method !== '') {
                document.getElementById('detail-wallet').style.display = 'block';
            }
        }
    </script>
</body>
</html>
