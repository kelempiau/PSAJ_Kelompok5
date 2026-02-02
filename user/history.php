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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: 
            font-size: 1.8rem;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-back {
            background: 
            color: 
        }

        .btn-back:hover {
            background: 
        }

        .empty-state {
            background: white;
            padding: 60px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .empty-state h2 {
            color: 
            margin-bottom: 15px;
        }

        .empty-state p {
            color: 
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
            border-bottom: 2px solid 
        }

        .booking-id {
            font-size: 0.9rem;
            color: 
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending {
            background: 
            color: 
        }

        .status-confirmed {
            background: 
            color: 
        }

        .status-completed {
            background: 
            color: 
        }

        .status-cancelled {
            background: 
            color: 
        }

        .refund-pending {
            background: 
            color: 
        }

        .refund-approved {
            background: 
            color: 
        }

        .refund-rejected {
            background: 
            color: 
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item strong {
            color: 
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .detail-item span {
            color: 
        }

        .booking-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid 
        }

        .btn-refund {
            background: 
            color: white;
            font-size: 0.9rem;
            padding: 8px 18px;
        }

        .btn-refund:hover {
            background: 
            transform: translateY(-2px);
        }

        .btn-refund:disabled {
            background: 
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
            color: 
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid 
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 15px;
        }

        textarea:focus {
            outline: none;
            border-color: 
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cancel {
            background: 
            color: 
        }

        .btn-submit {
            background: 
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
    <div class="container">
        <div class="header">
            <h1>📋 Riwayat Transaksi</h1>
            <div style="display: flex; gap: 10px;">
                <a href="refund_status.php" class="btn" style="background: 
                <a href="../index.php" class="btn btn-back">← Kembali</a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: 
                ✅ <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: 
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
                        <span class="booking-id">
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
                        </div>
                        <div class="detail-item">
                            <strong>💰 Total Harga</strong>
                            <span>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <?php if ($booking['addons']): ?>
                        <div class="detail-item" style="margin-bottom: 15px;">
                            <strong>➕ Add Ons</strong>
                            <span><?= $booking['addons'] ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($booking['refund_status']): ?>
                        <div style="margin-top: 15px; padding: 12px; background: 
                            <strong style="color: 
                            <span class="status-badge refund-<?= $booking['refund_status'] ?>"><?= ucfirst($booking['refund_status']) ?></span>
                            <?php if ($booking['refund_reason']): ?>
                                <p style="margin-top: 8px; color: 
                                    <strong>Alasan:</strong> <?= htmlspecialchars($booking['refund_reason']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="booking-actions">
                        
                        <button class="btn" style="background: 
                            🧾 Cetak Kwitansi
                        </button>

                        <?php if ($booking['payment_proof']): ?>
                            <a href="../<?= $booking['payment_proof'] ?>" target="_blank" class="btn btn-back" style="background: 
                                📄 Lihat Bukti
                            </a>
                        <?php endif; ?>

                        <?php if (in_array($booking['status'], ['pending', 'confirmed', 'completed']) && !$booking['refund_status']): ?>
                            <button class="btn btn-refund" onclick="openRefundModal(<?= $booking['id'] ?>)">
                                🔄 Ajukan Refund
                            </button>
                        <?php elseif ($booking['refund_status']): ?>
                            <span style="color: 
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

