<?php
require '../core/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Fetch booking details
$sql = "SELECT r.*, u.username, u.email 
        FROM reservations r 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.id = ? AND r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Booking tidak ditemukan atau Anda tidak memiliki akses.");
}

$booking = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi #<?= $booking['id'] ?> - Neydream Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            padding: 30px;
            background: #f5f5f5;
        }

        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 2px solid #ea3671;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #ea3671;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #ea3671;
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 0.9rem;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-section h3 {
            color: #5f162e;
            font-size: 1rem;
            margin-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            color: #333;
            text-align: right;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #ffd9e2;
            color: #5f162e;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .total-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .total-row.grand {
            border-top: 2px solid #ea3671;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 1.3rem;
            font-weight: 700;
            color: #ea3671;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .footer {
            text-align: center;
            color: #999;
            font-size: 0.85rem;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .print-btn {
            background: #ea3671;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            display: block;
            margin: 20px auto;
        }

        .print-btn:hover {
            background: #d63060;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .receipt {
                border: none;
                padding: 20px;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>🎨 Neydream Nail Art Studio</h1>
            <p>Beautiful Nails, Beautiful You</p>
            <p style="margin-top: 10px;">📍 Pusat Kota | 📞 0812-xxxx-xxxx | 📧 hello@neydream.com</p>
        </div>

        <h2 style="text-align: center; color: #5f162e; margin-bottom: 20px;">
            KWITANSI PEMBAYARAN
        </h2>

        <div class="receipt-info">
            <div class="info-section">
                <h3>Informasi Customer</h3>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span class="info-value"><?= htmlspecialchars($booking['name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($booking['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. HP:</span>
                    <span class="info-value"><?= $booking['phone'] ?></span>
                </div>
            </div>

            <div class="info-section">
                <h3>Detail Booking</h3>
                <div class="info-row">
                    <span class="info-label">No. Invoice:</span>
                    <span class="info-value">#NEY-<?= str_pad($booking['id'], 5, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Booking:</span>
                    <span class="info-value"><?= date('d F Y', strtotime($booking['created_at'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Reservasi:</span>
                    <span class="info-value"><?= date('d F Y', strtotime($booking['reservation_date'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jam:</span>
                    <span class="info-value"><?= $booking['reservation_time'] ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th style="text-align: right;">Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= $booking['service_type'] ?></strong>
                        <?php if ($booking['addons']): ?>
                            <br><small style="color: #666;">Add-ons: <?= $booking['addons'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></span>
            </div>
            <div class="total-row">
                <span>Metode Pembayaran:</span>
                <span><?= ucfirst($booking['payment_method'] ?? 'Transfer Bank') ?></span>
            </div>
            <div class="total-row grand">
                <span>TOTAL PEMBAYARAN:</span>
                <span>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></span>
            </div>
        </div>

        <?php if ($booking['payment_proof']): ?>
            <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                <strong style="color: #2e7d32;">✓ Bukti Pembayaran Tersedia</strong>
            </div>
        <?php endif; ?>

        <?php if ($booking['refund_status']): ?>
            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>Status Refund:</strong> 
                <span class="status-badge refund-<?= $booking['refund_status'] ?>"><?= ucfirst($booking['refund_status']) ?></span>
                <?php if ($booking['refund_reason']): ?>
                    <p style="margin-top: 10px; color: #666;">
                        <strong>Alasan:</strong> <?= htmlspecialchars($booking['refund_reason']) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <button class="print-btn" onclick="window.print()">🖨️ Cetak Kwitansi</button>

        <div class="footer">
            <p><strong>Terima kasih atas kepercayaan Anda!</strong></p>
            <p>Kwitansi ini dicetak secara otomatis dan sah tanpa tanda tangan.</p>
            <p style="margin-top: 10px;">© 2026 Neydream Nail Art Studio. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
