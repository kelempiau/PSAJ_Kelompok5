<?php
require '../core/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit();
}

$sql = "SELECT * FROM reservations WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Neydream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <?php include 'includes/loading_styles.php'; ?>
    <style>
        #sb98124, #sb98124_image, #sb98124_close, .tutup2,
        div[id*="sb"][style*="fixed"], 
        div[class*="sb"][style*="fixed"],
        a[href*="infinityfree"], 
        center a[title*="Free Web Hosting"],
        div[style*="z-index: 99999"], 
        .disclaimer {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
            position: absolute !important;
            left: -9999px !important;
            visibility: hidden !important;
        }
    </style>
    <script>
        (function(){
            const cleanup = () => {
                const selectors = [
                    '#sb98124', '.tutup2', 'div[id^="sb"]', 
                    'a[href*="infinityfree"]', 'center a[title*="Hosting"]',
                    'div[style*="z-index: 99999"]', 'div[style*="position: fixed"][style*="99999"]'
                ];
                selectors.forEach(s => {
                    document.querySelectorAll(s).forEach(el => {
                        if (!el.innerText.includes("AI") && !el.id.includes("chat") && !el.className.includes("modal")) {
                            el.remove();
                        }
                    });
                });
            };
            cleanup();
            setInterval(cleanup, 1000);
            window.addEventListener('load', cleanup);
        })();
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fff0f3 0%, #ffb7c5 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(255, 133, 161, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255,255,255,0.6);
        }

        .header h1 {
            color: #ea3671;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-back {
            background: white;
            color: #ea3671;
            border: 1px solid #ffe6f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .btn-back:hover {
            background: #fff0f3;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 133, 161, 0.2);
        }

        .empty-state {
            background: white;
            padding: 60px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .empty-state h2 {
            color: #5f162e;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 25px;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .booking-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .booking-id {
            font-size: 0.9rem;
            color: #999;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .refund-pending {
            background: #e7f3ff;
            color: #004085;
        }

        .refund-approved {
            background: #d4edda;
            color: #155724;
        }

        .refund-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item strong {
            color: #5f162e;
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .detail-item span {
            color: #666;
        }

        .booking-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-refund {
            background: #ea3671;
            color: white;
            font-size: 0.9rem;
            padding: 8px 18px;
        }

        .btn-refund:hover {
            background: #d63060;
            transform: translateY(-2px);
        }

        .btn-refund:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal h3 {
            color: #5f162e;
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 15px;
        }

        textarea:focus {
            outline: none;
            border-color: #ea3671;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #333;
        }

        .btn-submit {
            background: #ea3671;
            color: white;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .booking-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/loading.php'; ?>
    <div class="container">
        <div class="header">
            <h1>📋 Riwayat Transaksi</h1>
            <div style="display: flex; gap: 10px;">
                <a href="refund_status.php" class="btn" style="background: #ea3671; color: white;">🔄 Status Refund</a>
                <a href="../index.php" class="btn btn-back">← Kembali</a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                ✅ <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
                ❌ <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($result->num_rows == 0): ?>
            <div class="empty-state">
                <h2>Belum Ada Transaksi</h2>
                <p>Anda belum memiliki riwayat booking. Yuk booking sekarang!</p>
                <a href="reservasi.php" class="btn btn-refund">Buat Reservasi</a>
            </div>
        <?php else: ?>
            <?php while ($booking = $result->fetch_assoc()): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <span class="booking-id">#<?= $booking['id'] ?> - <?= date('d M Y H:i', strtotime($booking['created_at'])) ?></span>
                        <span class="status-badge status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                    </div>

                    <div class="booking-details">
                        <div class="detail-item">
                            <strong>📅 Tanggal Reservasi</strong>
                            <span><?= date('d F Y', strtotime($booking['reservation_date'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>🕐 Waktu</strong>
                            <span><?= $booking['reservation_time'] ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>💅 Layanan</strong>
                            <span><?= $booking['service_type'] ?></span>
                            <?php 
                                // Simple deduction of service price
                                $addons_val = (isset($booking['addons']) && is_numeric($booking['addons'])) ? (float)$booking['addons'] : 0;
                                $serv_price = (float)$booking['total_price'] - $addons_val;
                                if ($serv_price > 0):
                            ?>
                                <small style="display: block; color: #ea3671; font-weight: 600;">Rp <?= number_format($serv_price, 0, ',', '.') ?></small>
                            <?php endif; ?>
                        </div>
                        <?php if ($booking['addons']): ?>
                        <div class="detail-item">
                            <strong>➕ Add Ons</strong>
                            <span><?= $booking['addons'] ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <strong>💰 Total Harga</strong>
                            <span>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>💳 Status Bayar</strong>
                            <?php if ($booking['balance_due'] > 0): ?>
                                <span style="color: #ea3671; font-weight: bold;">Belum Lunas</span>
                                <small style="display: block; color: #888;">Sudah Bayar: Rp <?= number_format($booking['amount_paid'], 0, ',', '.') ?></small>
                                <small style="display: block; color: #ea3671;">Sisa: Rp <?= number_format($booking['balance_due'], 0, ',', '.') ?></small>
                            <?php else: ?>
                                <span style="color: #28a745; font-weight: bold;">Lunas</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="booking-details" style="margin-top: 10px;">
                        <div class="detail-item">
                            <strong>🏦 Metode</strong>
                            <span style="text-transform: uppercase;"><?= $booking['payment_method'] ?></span>
                        </div>
                    </div>

                    <?php if ($booking['refund_status']): ?>
                        <div style="margin-top: 15px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                            <strong style="color: #5f162e;">Status Refund:</strong>
                            <span class="status-badge refund-<?= $booking['refund_status'] ?>"><?= ucfirst($booking['refund_status']) ?></span>
                            <?php if ($booking['refund_reason']): ?>
                                <p style="margin-top: 8px; color: #666; font-size: 0.9rem;">
                                    <strong>Alasan:</strong> <?= htmlspecialchars($booking['refund_reason']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="booking-actions">
                        <?php if (($booking['payment_type'] === 'dp' || $booking['payment_type'] === 'dp_transfer') && $booking['balance_due'] > 0): ?>
                            <a href="lunasi.php?id=<?= $booking['id'] ?>" class="btn" style="background: #ea3671; color: white; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                                💳 Lunasi Sisa
                            </a>
                        <?php endif; ?>
                        
                        <button class="btn" style="background: #007bff; color: white;" onclick="printReceipt(<?= $booking['id'] ?>)">
                            🧾 Cetak Kwitansi
                        </button>

                        <?php if ($booking['payment_proof']): ?>
                            <a href="../<?= $booking['payment_proof'] ?>" target="_blank" class="btn btn-back" style="background: #e8f5e9; color: #2e7d32; text-decoration: none;">
                                📄 Lihat Bukti
                            </a>
                        <?php endif; ?>

                        <?php if (in_array($booking['status'], ['pending', 'confirmed', 'completed']) && !$booking['refund_status']): ?>
                            <button class="btn btn-refund" onclick="openRefundModal(<?= $booking['id'] ?>)">
                                🔄 Ajukan Refund
                            </button>
                        <?php elseif ($booking['refund_status']): ?>
                            <span style="color: #999; font-size: 0.9rem; padding: 10px;">
                                <?php 
                                    if ($booking['refund_status'] == 'pending') echo '⏳ Refund Pending';
                                    elseif ($booking['refund_status'] == 'approved') echo '✅ Refund Disetujui';
                                    else echo '❌ Refund Ditolak';
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <div id="refundModal" class="modal">
        <div class="modal-content">
            <h3>Request Refund</h3>
            <form id="refundForm" method="POST" action="refund_request.php">
                <input type="hidden" name="booking_id" id="refundBookingId">
                <label for="reason"><strong>Alasan Refund:</strong></label>
                <textarea name="reason" id="reason" placeholder="Jelaskan alasan Anda meminta refund..." required></textarea>
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeRefundModal()">Batal</button>
                    <button type="submit" class="btn btn-submit">Kirim Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRefundModal(bookingId) {
            document.getElementById('refundBookingId').value = bookingId;
            document.getElementById('refundModal').style.display = 'block';
        }

        function closeRefundModal() {
            document.getElementById('refundModal').style.display = 'none';
            document.getElementById('reason').value = '';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('refundModal');
            if (event.target == modal) {
                closeRefundModal();
            }
        }

        function printReceipt(bookingId) {
            window.open('receipt.php?id=' + bookingId, '_blank', 'width=800,height=600');
        }
    </script>
</body>
</html>
