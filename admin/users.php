<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_user') {
        $id = intval($_POST['user_id']);
        $conn->query("DELETE FROM users WHERE id = $id");
        $_SESSION['msg'] = "User berhasil dihapus.";
    }
    elseif ($_POST['action'] === 'update_role') {
        $id = intval($_POST['user_id']);
        $role = $_POST['role'];
        $conn->query("UPDATE users SET role = '$role' WHERE id = $id");
        $_SESSION['msg'] = "Role pengguna berhasil diperbarui.";
    }
    header("Location: users.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin Panel</title>
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
                    <span class="current">Basis Pengguna</span>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">👥</div>
                <div class="context-info">
                    <div class="title">Basis Pengguna</div>
                    <div class="subtitle">Kelola akun pengguna, hak akses, dan detail profil.</div>
                </div>
            </div>

            <div style="padding: 24px;">
                <?php if($msg): ?>
                    <div class="item-card" style="border-left: 4px solid #10b981; margin-bottom: 24px; padding: 12px 20px;">
                        <span style="color: #10b981; font-weight: 600;">Sukses:</span> <?= $msg ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Akun Terdaftar</h3>
                        <div class="meta"><?= $users->num_rows ?> Users Total</div>
                    </div>
                    <div class="table-container" style="overflow-x: auto;">
                        <table>
                        <thead>
                            <tr>
                                <th width="80">UID</th>
                                <th>Informasi Pengguna</th>
                                <th>Role</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $users->num_rows; while($u = $users->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $no-- ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="item-avatar" style="width: 32px; height: 32px; font-size: 12px; font-weight: 700; background: var(--primary-light); color: var(--primary); overflow: hidden;">
                                            <?php if(!empty($u['profile_pic'])): ?>
                                                <img src="../<?= $u['profile_pic'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--text-primary);"><?= htmlspecialchars($u['username']) ?></div>
                                            <div style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="status-badge status-confirmed" style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">🛡️ Admin</span>
                                    <?php else: ?>
                                        <span class="status-badge" style="background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">👤 User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($u['role'] !== 'admin' && $u['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus user ini? Semua data terkait (reservasi, feedback) juga akan terhapus!');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; border-color: var(--primary-light); color: #ef4444;">Hapus</button>
                                    </form>
                                    <?php else: ?>
                                         <span style="font-size: 11px; color: var(--text-muted); font-style: italic;">🛡️ Anda (Admin)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

