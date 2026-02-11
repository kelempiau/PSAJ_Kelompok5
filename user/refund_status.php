<?php
require '../core/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Strict Access Control: Admin cannot access user pages
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Fetch all refund requests from this user
$sql = "SELECT * FROM reservations 
        WHERE user_id = ? AND refund_status IS NOT NULL 
        ORDER BY refund_date DESC";
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
    <title>Status Refund - Neydream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
        }

        .btn-back {
            background: #f0f0f0;
            color: #333;
        }

        .btn-back:hover {
            background: #e0e0e0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .pending .stat-number { color: #856404; }
        .approved .stat-number { color: #155724; }
        .rejected .stat-number { color: #721c24; }

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

        .refund-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .refund-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .refund-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .refund-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
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

        .reason-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .reason-box strong {
            color: #5f162e;
            display: block;
            margin-bottom: 8px;
        }

        .timeline {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .timeline-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            background: #ea3671;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-date {
            color: #999;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/loading.php'; ?>
    <div class="container">
        <div class="header">
            <h1>🔄 Status Refund</h1>
            <a href="history.php" class="btn btn-back">← Kembali</a>
        </div>

        <?php
        $pending = 0;
        $approved = 0;
        $rejected = 0;
        
        // Count statuses
        $temp_result = $result;
        mysqli_data_seek($result, 0); // Reset pointer
        while ($row = $result->fetch_assoc()) {
            if ($row['refund_status'] == 'pending') $pending++;
            elseif ($row['refund_status'] == 'approved') $approved++;
            elseif ($row['refund_status'] == 'rejected') $rejected++;
        }
        mysqli_data_seek($result, 0); // Reset again for display
        ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="stats">
                <div class="stat-card pending">
                    <div class="stat-number"><?= $pending ?></div>
                    <div class="stat-label">⏳ Pending</div>
                </div>
                <div class="stat-card approved">
                    <div class="stat-number"><?= $approved ?></div>
                    <div class="stat-label">✅ Disetujui</div>
                </div>
                <div class="stat-card rejected">
                    <div class="stat-number"><?= $rejected ?></div>
                    <div class="stat-label">❌ Ditolak</div>
                </div>
            </div>

            <?php while ($refund = $result->fetch_assoc()): ?>
                <div class="refund-card">
                    <div class="refund-header">
                        <div>
                            <strong>Booking #<?= $refund['id'] ?></strong>
                            <div style="color: #999; font-size: 0.9rem; margin-top: 5px;">
                                Request: <?= date('d M Y H:i', strtotime($refund['refund_date'])) ?>
                            </div>
                        </div>
                        <span class="status-badge status-<?= $refund['refund_status'] ?>">
                            <?= ucfirst($refund['refund_status']) ?>
                        </span>
                    </div>

                    <div class="refund-details">
                        <div class="detail-item">
                            <strong>📅 Tanggal Reservasi</strong>
                            <span><?= date('d F Y', strtotime($refund['reservation_date'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>🕐 Jam</strong>
                            <span><?= $refund['reservation_time'] ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>💅 Layanan</strong>
                            <span><?= $refund['service_type'] ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>💰 Total</strong>
                            <span>Rp <?= number_format($refund['total_price'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="reason-box">
                        <strong>Alasan Refund:</strong>
                        <p style="color: #666;"><?= htmlspecialchars($refund['refund_reason']) ?></p>
                    </div>

                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon">📝</div>
                            <div class="timeline-content">
                                <strong>Request Dibuat</strong>
                                <div class="timeline-date"><?= date('d M Y H:i', strtotime($refund['refund_date'])) ?></div>
                            </div>
                        </div>

                        <?php if ($refund['refund_status'] == 'pending'): ?>
                            <div class="timeline-item">
                                <div class="timeline-icon" style="background: #ffc107;">⏳</div>
                                <div class="timeline-content">
                                    <strong>Menunggu Review Admin</strong>
                                    <div class="timeline-date">Status akan diupdate dalam 1-2 hari kerja</div>
                                </div>
                            </div>
                        <?php elseif ($refund['refund_status'] == 'approved'): ?>
                            <div class="timeline-item">
                                <div class="timeline-icon" style="background: #28a745;">✓</div>
                                <div class="timeline-content">
                                    <strong>Refund Disetujui</strong>
                                    <div class="timeline-date">Dana akan dikembalikan dalam 3-5 hari kerja</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="timeline-item">
                                <div class="timeline-icon" style="background: #dc3545;">✗</div>
                                <div class="timeline-content">
                                    <strong>Refund Ditolak</strong>
                                    <div class="timeline-date">Hubungi admin untuk informasi lebih lanjut</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="empty-state">
                <h2>Belum Ada Request Refund</h2>
                <p style="color: #666; margin-bottom: 25px;">
                    Anda belum pernah mengajukan refund untuk transaksi apapun.
                </p>
                <a href="history.php" class="btn" style="background: #ea3671; color: white;">
                    Lihat Riwayat Transaksi
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
