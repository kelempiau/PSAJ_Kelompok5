<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle POST actions
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

                <div class="card">
                    <div class="card-header">
                        <h3>Log Reservasi Terbaru</h3>
                        <div class="meta"><?= $reservations->num_rows ?> Bookings Tracked</div>
                    </div>
                    <div class="table-container" style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Waktu & Client</th>
                                    <th>Layanan & Biaya</th>
                                    <th width="100">Bukti Bayar</th>
                                    <th>Status Approval</th>
                                    <th width="180">Manajemen Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($reservations->num_rows == 0): ?>
                                    <tr><td colspan="5" style="text-align: center; padding: 60px; color: var(--text-muted);">Belum ada data reservasi masuk</td></tr>
                                <?php else: ?>
                                    <?php while($r = $reservations->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="item-avatar" style="background: var(--primary-light); color: var(--primary);">📅</div>
                                                <div>
                                                    <div style="font-weight: 700; color: var(--text-primary);"><?= date('d M Y', strtotime($r['reservation_date'])) ?></div>
                                                    <div style="font-size: 12px; color: var(--text-secondary);"><?= $r['reservation_time'] ?> • <?= htmlspecialchars($r['name'] ?? $r['username']) ?></div>
                                                    <?php if(!empty($r['notes'])): ?>
                                                        <div style="font-size: 11px; color: #ea3671; font-style: italic; margin-top: 4px; border-left: 2px solid #ea367133; padding-left: 6px;">
                                                            "<?= htmlspecialchars($r['notes']) ?>"
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">Rp<?= number_format($r['total_price']) ?></div>
                                            <div style="font-size: 11px; margin-top: 4px;">
                                                <?php if($r['balance_due'] > 0): ?>
                                                    <span style="background: #fff1f2; color: #e11d48; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px;">BELUM LUNAS</span>
                                                    <div style="color: #e11d48; margin-top: 2px;">Sisa: <strong>Rp<?= number_format($r['balance_due']) ?></strong></div>
                                                <?php else: ?>
                                                    <span style="background: #f0fdf4; color: #166534; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px;">LUNAS</span>
                                                <?php endif; ?>
                                                
                                                <?php if($r['payment_type'] === 'dp'): ?>
                                                    <div style="color: #666; font-size: 10px; margin-top: 2px;">Terbayar: Rp<?= number_format($r['amount_paid']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size: 10px; color: var(--text-muted); margin-top: 4px;">Metode: <span style="text-transform: uppercase; font-weight: 600;"><?= $r['payment_method'] ?></span></div>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                                <?php if(!empty($r['payment_proof'])): ?>
                                                    <button onclick="openProofModal('../<?= $r['payment_proof'] ?>', 'Bukti Transaksi')" class="btn" style="background: #3b82f6; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%;">
                                                        <span>📄</span> Bukti Transaksi
                                                    </button>
                                                <?php endif; ?>

                                                <?php if(!empty($r['final_payment_proof'])): ?>
                                                    <button onclick="openProofModal('../<?= $r['final_payment_proof'] ?>', 'Bukti Pelunasan')" class="btn" style="background: #e11d48; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%;">
                                                        <span>📸</span> Final
                                                    </button>
                                                <?php endif; ?>

                                                <?php if(empty($r['payment_proof']) && empty($r['final_payment_proof'])): ?>
                                                    <span style="font-size: 10px; color: #ccc; font-style: italic; text-align:center; display:block;">No Proof</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" style="margin: 0;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                                <select name="status" onchange="this.form.submit()" style="
                                                    padding: 6px 10px;
                                                    border-radius: 8px;
                                                    font-size: 11px;
                                                    font-weight: 700;
                                                    border: 1px solid var(--border-color);
                                                    cursor: pointer;
                                                    <?php
                                                    if($r['status']=='pending') echo 'background: #fef3c7; color: #92400e;';
                                                    elseif($r['status']=='confirmed') echo 'background: #dcfce7; color: #15803d;';
                                                    elseif($r['status']=='completed') echo 'background: #e0e7ff; color: #4338ca;';
                                                    else echo 'background: #f1f5f9; color: #475569;';
                                                    ?>
                                                ">
                                                    <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>🕒 Pending</option>
                                                    <option value="confirmed" <?= $r['status']=='confirmed'?'selected':'' ?>>✅ Confirm</option>
                                                    <option value="completed" <?= $r['status']=='completed'?'selected':'' ?>>🏁 Finish</option>
                                                    <option value="cancelled" <?= $r['status']=='cancelled'?'selected':'' ?>>❌ Cancel</option>
                                                </select>
                                            </form>
                                            <?php if($r['refund_status']): ?>
                                                <div class="meta" style="margin-top: 4px; color: #ef4444; font-weight: 700;">💸 REFUND: <?= strtoupper($r['refund_status']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display:flex; flex-direction: column; gap: 8px;">
                                                <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus reservasi ini?');">
                                                    <input type="hidden" name="action" value="delete_reservation">
                                                    <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                                    <button type="submit" class="btn" style="padding: 10px 12px; font-size: 11px; background: rgba(239, 68, 68, 0.75); color: #ffffff !important; border: none; border-radius: 8px; width: 100%; min-width: 130px; font-weight: 800; cursor: pointer; text-align: center; display: flex; align-items: center; justify-content: center; text-decoration: none; -webkit-font-smoothing: antialiased;">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Image Viewer Modal -->
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
            // Trigger reflow for transition
            setTimeout(() => { modal.style.opacity = '1'; }, 10);
        }

        function closeProofModal() {
            const modal = document.getElementById('proofModal');
            modal.style.opacity = '0';
            setTimeout(() => { modal.style.display = 'none'; }, 300);
        }

        // Close on clicking outside
        document.getElementById('proofModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProofModal();
            }
        });
    </script>
</body>
</html>

