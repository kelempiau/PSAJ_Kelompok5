<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $id = intval($_POST['res_id']);
        $status = $_POST['status'];
        $conn->query("UPDATE reservations SET status = '$status' WHERE id = $id");
        $_SESSION['msg'] = "Status reservasi diperbarui.";
    }
    elseif ($_POST['action'] === 'delete_reservation') {
        $id = intval($_POST['res_id']);
        $conn->query("DELETE FROM reservations WHERE id = $id");
        $_SESSION['msg'] = "Data reservasi berhasil dihapus.";
    }
    elseif ($_POST['action'] === 'settle_balance') {
        $id = intval($_POST['res_id']);
        $res = $conn->query("SELECT total_price FROM reservations WHERE id = $id");
        if ($row = $res->fetch_assoc()) {
            $total = $row['total_price'];
            $conn->query("UPDATE reservations SET balance_due = 0, amount_paid = $total WHERE id = $id");
            $_SESSION['msg'] = "Pembayaran sisa telah dilunasi secara manual.";
        }
    }
    header("Location: reservations.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$reservations = $conn->query("SELECT reservations.*, users.username FROM reservations LEFT JOIN users ON reservations.user_id = users.id ORDER BY reservation_date DESC, reservation_time ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Reservasi - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'includes/admin_styles.php'; ?>
</head>
<body>
    <?php include 'includes/loading.php'; ?>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="breadcrumbs">
                    <a href="dashboard.php" class="sep">Dashboard</a>
                    <span class="sep">/</span>
                    <span class="current">Data Reservasi</span>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">📅</div>
                <div class="context-info">
                    <div class="title">Manajemen Reservasi</div>
                    <div class="subtitle">Pantau pesanan pelanggan, status pembayaran, dan konfirmasi jadwal.</div>
                </div>
            </div>

            <div style="padding: 24px;">
                <?php if($msg): ?>
                    <div class="item-card" style="border-left: 4px solid #10b981; margin-bottom: 24px; padding: 12px 20px;">
                        <span style="color: #10b981; font-weight: 600;">Update:</span> <?= $msg ?>
                    </div>
                <?php endif; ?>

<?php
$resByDate = [];
$counts = ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
$total_bookings = $reservations->num_rows;

while ($r = $reservations->fetch_assoc()) {
    $date = $r['reservation_date'];
    if (!isset($resByDate[$date])) {
        $resByDate[$date] = [];
    }
    $resByDate[$date][] = $r;
    if (isset($counts[$r['status']])) {
        $counts[$r['status']]++;
    }
}
?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <h2 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin: 0;">Log Reservasi Terbaru</h2>
                    <div style="display: flex; gap: 15px; align-items: center; background: white; padding: 10px 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                        <div style="display: flex; gap: 15px; font-weight: 700; font-size: 14px;">
                            <span style="color: #d97706;" title="Pending">🕒 <?= $counts['pending'] ?></span>
                            <span style="color: #16a34a;" title="Finish/Confirm">✅ <?= $counts['completed'] + $counts['confirmed'] ?></span>
                            <span style="color: #dc2626;" title="Cancel">❌ <?= $counts['cancelled'] ?></span>
                        </div>
                        <div class="meta" style="color: #64748b; font-size: 13px; font-weight: 600; border-left: 2px solid #e2e8f0; padding-left: 15px;">
                            <?= $total_bookings ?> Bookings Tracked
                        </div>
                    </div>
                </div>

                <?php if ($total_bookings == 0): ?>
                    <div class="card" style="text-align: center; padding: 60px; color: var(--text-muted);">Belum ada data reservasi masuk</div>
                <?php else: ?>
                    <?php foreach ($resByDate as $date => $dailyRes): ?>
                    <div class="card" style="margin-bottom: 30px; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 15px 24px;">
                            <h3 style="margin: 0; color: #334155; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                                📅 <?= date('d M Y', strtotime($date)) ?>
                            </h3>
                            <div class="meta" style="background: #ea3671; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">
                                <?= count($dailyRes) ?> Reservasi
                            </div>
                        </div>
                        <div class="table-container" style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="padding: 12px 24px; text-align: left; border-bottom: 1px solid #e2e8f0;">Waktu & Client</th>
                                        <th style="padding: 12px 24px; text-align: left; border-bottom: 1px solid #e2e8f0;">Layanan & Biaya</th>
                                        <th style="padding: 12px 24px; text-align: left; border-bottom: 1px solid #e2e8f0;" width="100">Bukti Bayar</th>
                                        <th style="padding: 12px 24px; text-align: left; border-bottom: 1px solid #e2e8f0;">Status Approval</th>
                                        <th style="padding: 12px 24px; text-align: left; border-bottom: 1px solid #e2e8f0;" width="180">Manajemen Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dailyRes as $r): 
                                        $rowBg = '';
                                        if ($r['status'] == 'pending') $rowBg = 'background-color: #fef9c3;'; 
                                        elseif ($r['status'] == 'confirmed' || $r['status'] == 'completed') $rowBg = 'background-color: #dcfce7;'; 
                                        elseif ($r['status'] == 'cancelled') $rowBg = 'background-color: #fee2e2;';
                                    ?>
                                    <tr style="<?= $rowBg ?>">
                                        <td style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="item-avatar" style="background: rgba(255,255,255,0.6); color: var(--text-primary); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px;">⏰</div>
                                                <div>
                                                    <div style="font-weight: 800; color: #1e293b; font-size: 15px;"><?= $r['reservation_time'] ?></div>
                                                    <div style="font-size: 12px; color: #64748b; font-weight: 500;"><?= htmlspecialchars($r['name'] ?? $r['username']) ?></div>
                                                    <?php if(!empty($r['notes'])): ?>
                                                        <div style="font-size: 11px; color: #ea3671; font-style: italic; margin-top: 4px; border-left: 2px solid #ea367133; padding-left: 6px;">
                                                            "<?= htmlspecialchars($r['notes']) ?>"
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0;">
                                            <div style="font-weight: 800; color: #1e293b; font-size: 14px;">Rp<?= number_format($r['total_price']) ?></div>
                                            <div style="font-size: 11px; margin-top: 4px;">
                                                <?php if($r['balance_due'] > 0): ?>
                                                    <span style="background: #ef4444; color: white; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px; display: inline-block;">BELUM LUNAS</span>
                                                    <div style="color: #b91c1c; margin-top: 4px; font-weight: 600;">Sisa: Rp<?= number_format($r['balance_due']) ?></div>
                                                <?php else: ?>
                                                    <span style="background: #10b981; color: white; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px; display: inline-block;">LUNAS</span>
                                                <?php endif; ?>
                                                
                                                <?php if($r['payment_type'] === 'dp'): ?>
                                                    <div style="color: #64748b; font-size: 10px; margin-top: 4px; font-weight: 500;">Terbayar: Rp<?= number_format($r['amount_paid']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size: 10px; color: #64748b; margin-top: 6px;">Metode: <span style="text-transform: uppercase; font-weight: 700; color: #1e293b;"><?= $r['payment_method'] ?></span></div>
                                        </td>
                                        <td style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0;">
                                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                                <?php if(!empty($r['payment_proof'])): ?>
                                                    <button onclick="openProofModal('../<?= $r['payment_proof'] ?>', 'Bukti Transaksi')" class="btn" style="background: #3b82f6; color: white; border: none; padding: 6px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; box-shadow: 0 2px 4px rgba(59,130,246,0.3);">
                                                        <span>📄</span> Tampilkan
                                                    </button>
                                                <?php endif; ?>
                                                <?php if(!empty($r['final_payment_proof'])): ?>
                                                    <button onclick="openProofModal('../<?= $r['final_payment_proof'] ?>', 'Bukti Pelunasan')" class="btn" style="background: #e11d48; color: white; border: none; padding: 6px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; box-shadow: 0 2px 4px rgba(225,29,72,0.3);">
                                                        <span>📸</span> Final
                                                    </button>
                                                <?php endif; ?>
                                                <?php if(empty($r['payment_proof']) && empty($r['final_payment_proof'])): ?>
                                                    <span style="font-size: 10px; color: #94a3b8; font-style: italic; text-align:center; display:block;">No Proof</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0;">
                                            <form method="POST" style="margin: 0;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                                <select name="status" onchange="this.form.submit()" style="
                                                    padding: 6px 10px;
                                                    border-radius: 8px;
                                                    font-size: 12px;
                                                    font-weight: 700;
                                                    border: 1px solid rgba(0,0,0,0.1);
                                                    cursor: pointer;
                                                    background: white; color: #334155;
                                                ">
                                                    <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>🕒 Pending</option>
                                                    <option value="confirmed" <?= $r['status']=='confirmed'?'selected':'' ?>>✅ Confirm</option>
                                                    <option value="completed" <?= $r['status']=='completed'?'selected':'' ?>>🏁 Finish</option>
                                                    <option value="cancelled" <?= $r['status']=='cancelled'?'selected':'' ?>>❌ Cancel</option>
                                                </select>
                                            </form>
                                            <?php if($r['refund_status']): ?>
                                                <div class="meta" style="margin-top: 6px; color: #ef4444; font-weight: 800; font-size: 10px; background: rgba(239,68,68,0.1); padding: 4px 8px; border-radius: 4px; display: inline-block;">💸 REFUND: <?= strtoupper($r['refund_status']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0;">
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus reservasi ini?');">
                                                <input type="hidden" name="action" value="delete_reservation">
                                                <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                                <button type="submit" class="btn" style="padding: 8px 12px; font-size: 12px; background: #ef4444; color: white !important; border: none; border-radius: 8px; width: 100%; font-weight: 800; cursor: pointer; text-align: center; box-shadow: 0 4px 6px rgba(239,68,68,0.2);">Hapus Data</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="proofModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 100000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;">
        <div style="position: relative; max-width: 90%; max-height: 90%;">

            <div style="background: white; padding: 10px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
                <div id="proofTitle" style="text-align: center; margin-bottom: 10px; font-weight: 700; color: #333;">Bukti Transfer</div>
                <img id="proofImage" src="" style="max-width: 100%; max-height: 80vh; border-radius: 8px; display: block;">
                <button onclick="closeProofModal()" style="width: 100%; padding: 12px; margin-top: 10px; background: #ea3671; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Tutup / Kembali</button>
            </div>
        </div>
    </div>

    <script>
        function openProofModal(src, title) {
            const modal = document.getElementById('proofModal');
            document.getElementById('proofImage').src = src;
            document.getElementById('proofTitle').innerText = title || 'Bukti Transfer';
            modal.style.display = 'flex';
            setTimeout(() => { modal.style.opacity = '1'; }, 10);
        }

        function closeProofModal() {
            const modal = document.getElementById('proofModal');
            modal.style.opacity = '0';
            setTimeout(() => { modal.style.display = 'none'; }, 300);
        }
        document.getElementById('proofModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProofModal();
            }
        });
    </script>
</body>
</html>

