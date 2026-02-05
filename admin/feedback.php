<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_feedback') {
        $id = intval($_POST['feedback_id']);
        $conn->query("DELETE FROM feedback WHERE id = $id");
        $_SESSION['msg'] = "Feedback berhasil dihapus.";
    }
    header("Location: feedback.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$feedbacks = $conn->query("SELECT feedback.*, users.username as user_uname FROM feedback LEFT JOIN users ON feedback.user_id = users.id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Pelanggan - Admin Panel</title>
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
                    <span class="current">Feedback Board</span>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">📣</div>
                <div class="context-info">
                    <div class="title">Suara Pelanggan</div>
                    <div class="subtitle">Kumpulan feedback dan saran dari pengguna untuk meningkatkan kualitas layanan Neydream.</div>
                </div>
            </div>

            <div style="padding: 24px;">
                <?php if($msg): ?>
                    <div class="item-card" style="border-left: 4px solid #10b981; margin-bottom: 24px; padding: 12px 20px;">
                        <span style="color: #10b981; font-weight: 600;">Status:</span> <?= $msg ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3>Feed Balik Masuk</h3>
                        <div class="meta"><?= $feedbacks->num_rows ?> Messages Received</div>
                    </div>
                    <div class="table-container" style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th width="180">Waktu & Pengirim</th>
                                    <th>Isi Feedback</th>
                                    <th width="120">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($feedbacks->num_rows == 0): ?>
                                    <tr><td colspan="3" style="text-align: center; padding: 60px; color: var(--text-muted);">Belum ada feedback yang diterima</td></tr>
                                <?php else: ?>
                                    <?php while($f = $feedbacks->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-primary);"><?= date('d M Y', strtotime($f['created_at'])) ?></div>
                                            <div style="font-size: 12px; font-weight: 600; color: var(--primary);">@<?= htmlspecialchars($f['name']) ?></div>
                                            <?php if($f['whatsapp_number']): ?>
                                                <a href="https://wa.me/62<?= ltrim($f['whatsapp_number'], '0') ?>" target="_blank" style="font-size: 10px; color: #16a34a; font-weight: 700; text-decoration: none;">🟢 WhatsApp Hubungi</a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="line-height: 1.6; color: var(--text-secondary); font-size: 13px; background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
                                                <?= nl2br(htmlspecialchars($f['message'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus feedback ini?');">
                                                <input type="hidden" name="action" value="delete_feedback">
                                                <input type="hidden" name="feedback_id" value="<?= $f['id'] ?>">
                                                <button type="submit" class="btn btn-outline" style="padding: 6px 14px; font-size: 11px; color: #ef4444; border-color: var(--primary-light);">Hapus</button>
                                            </form>
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

