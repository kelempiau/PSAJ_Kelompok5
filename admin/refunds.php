<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'approve_refund') {
        $id = intval($_POST['booking_id']);
        $conn->query("UPDATE reservations SET refund_status = 'approved', status = 'cancelled' WHERE id = $id");
        $_SESSION['msg'] = "Refund disetujui.";
    }
    elseif ($_POST['action'] === 'reject_refund') {
        $id = intval($_POST['booking_id']);
        $conn->query("UPDATE reservations SET refund_status = 'rejected' WHERE id = $id");
        $_SESSION['msg'] = "Refund ditolak.";
    }
    header("Location: refunds.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$refund_requests = $conn->query("SELECT reservations.*, users.username, users.email FROM reservations LEFT JOIN users ON reservations.user_id = users.id WHERE refund_status IS NOT NULL ORDER BY refund_date DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Requests - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'includes/admin_styles.php'; ?>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="breadcrumbs">
                    <a href="dashboard.php" class="sep">Dashboard</a>
                    <span class="sep">/</span>
                    <span class="current">Refund Board</span>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">💰</div>
                <div class="context-info">
                    <div class="title">Permintaan Pengembalian Dana</div>
                    <div class="subtitle">Tinjau dan proses permintaan refund dari pelanggan secara transparan.</div>
                </div>
            </div>

            <div style="padding: 24px;">
                <?php if($msg): ?>
                    <div class="item-card" style="border-left: 4px solid #6366f1; margin-bottom: 24px; padding: 12px 20px;">
                        <span style="color: var(--primary); font-weight: 600;">Status Update:</span> <?= $msg ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3>Antrian Permintaan Refund</h3>
                        <div class="meta"><?= $refund_requests->num_rows ?> Requests Found</div>
                    </div>
                    <div class="table-container" style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Detil Reservasi</th>
                                    <th>Alasan & Catatan</th>
                                    <th>Info Akun</th>
                                    <th>Status Approval</th>
                                    <th width="150">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($refund_requests->num_rows == 0): ?>
                                    <tr><td colspan="5" style="text-align: center; padding: 60px; color: var(--text-muted);">Tidak ada permintaan refund saat ini</td></tr>
                                <?php else: ?>
                                    <?php while($rf = $refund_requests->fetch_assoc()): ?>
                                    <tr style="<?= $rf['refund_status'] == 'pending' ? 'background: rgba(99, 102, 241, 0.02);' : '' ?>">
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-primary);"><?= htmlspecialchars($rf['username']) ?></div>
                                            <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($rf['email']) ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; font-size: 13px;"><?= date('d M Y', strtotime($rf['reservation_date'])) ?></div>
                                            <div style="font-size: 12px; color: var(--primary); font-weight: 700;">Rp<?= number_format($rf['total_price']) ?></div>
                                        </td>
                                        <td style="max-width: 250px;">
                                            <div style="font-size: 13px; line-height: 1.4; color: var(--text-secondary);"><?= htmlspecialchars($rf['refund_reason']) ?></div>
                                            <div style="font-size: 10px; color: var(--text-muted); margin-top: 4px;">Diajukan: <?= date('d/m/Y H:i', strtotime($rf['refund_date'])) ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($rf['refund_target'])): ?>
                                                <div style="background: #f0f7ff; padding: 6px 10px; border-radius: 8px; border: 1px solid #d0e7ff; cursor: pointer;" onclick="viewRefundAccount('<?= addslashes($rf['refund_target']) ?>', '<?= addslashes($rf['refund_account']) ?>', '<?= htmlspecialchars($rf['username']) ?>')">
                                                    <div style="font-size: 10px; font-weight: 800; color: #3b82f6; text-transform: uppercase; margin-bottom: 2px;">🏦 <?= htmlspecialchars($rf['refund_target']) ?></div>
                                                    <div style="font-size: 11px; font-weight: 600; color: #1e3a8a;"><?= htmlspecialchars($rf['refund_account']) ?></div>
                                                </div>
                                            <?php else: ?>
                                                <span style="font-size: 11px; color: var(--text-muted); font-style: italic;">Belum diisi user</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="
                                                padding: 4px 10px;
                                                border-radius: 6px;
                                                font-size: 11px;
                                                font-weight: 800;
                                                text-transform: uppercase;
                                                <?php
                                                if($rf['refund_status'] == 'pending') echo 'background: #fef3c7; color: #92400e;';
                                                elseif($rf['refund_status'] == 'approved') echo 'background: #dcfce7; color: #15803d;';
                                                else echo 'background: var(--primary-light); color: #b91c1c;';
                                                ?>
                                            ">
                                                <?= $rf['refund_status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($rf['refund_status'] == 'pending'): ?>
                                                <div style="display: flex; gap: 8px;">
                                                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Setujui refund?');">
                                                        <input type="hidden" name="action" value="approve_refund">
                                                        <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 11px;">Setujui</button>
                                                    </form>
                                                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Tolak refund?');">
                                                        <input type="hidden" name="action" value="reject_refund">
                                                        <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                        <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; color: #ef4444; border-color: var(--primary-light);">Tolak</button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <span style="font-size: 11px; color: var(--text-muted); font-style: italic;">✔ Selesai</span>
                                            <?php endif; ?>
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
    <!-- Refund Account Detail Modal -->
    <div id="accountModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: white; padding: 32px; border-radius: 20px; width: 90%; max-width: 400px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: popIn 0.3s ease-out;">
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">🏦</div>
                <h3 style="margin-bottom: 8px; font-weight: 800; color: var(--text-primary);">Data Refund Pelanggan</h3>
                <p id="accCustName" style="color: var(--text-muted); font-size: 14px;"></p>
            </div>
            
            <div style="background: #f8faff; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 24px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Bank / E-Wallet</label>
                    <div id="accMethod" style="font-size: 1.1rem; font-weight: 700; color: #1e293b;"></div>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Nomor Rekening / HP</label>
                    <div id="accNumber" style="font-size: 1.2rem; font-weight: 800; color: #6366f1; font-family: 'Courier New', monospace;"></div>
                </div>
            </div>

            <button onclick="closeAccModal()" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: 12px; font-weight: 700;">TUTUP DETAIL</button>
        </div>
    </div>

    <style>
        @keyframes popIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>

    <script>
        function viewRefundAccount(method, number, name) {
            document.getElementById('accCustName').innerText = "Pelanggan: " + name;
            document.getElementById('accMethod').innerText = method;
            document.getElementById('accNumber').innerText = number;
            document.getElementById('accountModal').style.display = 'flex';
        }

        function closeAccModal() {
            document.getElementById('accountModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('accountModal');
            if (event.target == modal) {
                closeAccModal();
            }
        }
    </script>
</body>
</html>

