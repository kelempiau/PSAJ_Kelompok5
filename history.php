<?php
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Strict Access Control: Admin cannot access user pages
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit();
}

// Fetch user's reservations
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
            background: linear-gradient(135deg, #ffd9e2 0%, #ffe6f0 100%);
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
            color: #5f162e;
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
            background: #f0f0f0;
            color: #333;
        }

        .btn-back:hover {
            background: #e0e0e0;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    <div class="container">
        <div class="header">
            <h1>📋 Riwayat Transaksi</h1>
            <div style="display: flex; gap: 10px;">
                <a href="refund_status.php" class="btn" style="background: #ea3671; color: white;">🔄 Status Refund</a>
                <a href="index.php" class="btn btn-back">← Kembali</a>
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
                        <!-- Receipt/Invoice Button -->
                        <button class="btn" style="background: #007bff; color: white;" onclick="printReceipt(<?= $booking['id'] ?>)">
                            🧾 Cetak Kwitansi
                        </button>

                        <?php if ($booking['payment_proof']): ?>
                            <a href="<?= $booking['payment_proof'] ?>" target="_blank" class="btn btn-back" style="background: #e8f5e9; color: #2e7d32; text-decoration: none;">
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

    <!-- Refund Modal -->
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

        // Close modal if click outside
        window.onclick = function(event) {
            const modal = document.getElementById('refundModal');
            if (event.target == modal) {
                closeRefundModal();
            }
        }

        // Generate and print receipt
        function printReceipt(bookingId) {
            // Open receipt in new window
            window.open('receipt.php?id=' + bookingId, '_blank', 'width=800,height=600');
        }
    </script>
</body>
</html>
