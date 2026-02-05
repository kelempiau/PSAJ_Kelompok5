<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle POST actions
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
                                    <th>Status Approval</th>
                                    <th width="180">Tindakan</th>
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
    </div>
</body>
</html>

