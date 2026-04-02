<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_loyalty') {
        $id = intval($_POST['user_id']);
        $level = isset($_POST['reset_loyalty']) ? 1 : intval($_POST['loyalty_level']);
        $conn->query("UPDATE users SET loyalty_level = $level WHERE id = $id");
        $_SESSION['msg'] = "Loyalty User #$id berhasil diperbarui menjadi $level Bintang.";
    }
    header("Location: loyalty.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$users = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Loyalty - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .btn-refresh {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-refresh:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .star-select {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 13px;
            font-weight: 600;
            color: #f59e0b;
            background: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="breadcrumbs">
                    <a href="dashboard.php" class="sep">Dashboard</a>
                    <span class="sep">/</span>
                    <span class="current">Loyalty Level</span>
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">⭐</div>
                <div class="context-info">
                    <div class="title">Loyalty Level</div>
                    <div class="subtitle">Kelola tingkat loyalitas pelanggan dan berikan reward bintang.</div>
                </div>
            </div>

            <div style="padding: 24px;">
                <?php if($msg): ?>
                    <div class="item-card" style="border-left: 4px solid #f59e0b; margin-bottom: 24px; padding: 12px 20px;">
                        <span style="color: #f59e0b; font-weight: 600;">Update:</span> <?= $msg ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Loyalitas Pelanggan</h3>
                        <div class="meta"><?= $users->num_rows ?> Customers</div>
                    </div>
                    <div class="table-container" style="overflow-x: auto;">
                        <table>
                        <thead>
                            <tr>
                                <th width="80">No</th>
                                <th>Informasi Pengguna</th>
                                <th>Loyalty Level</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($u = $users->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $no++ ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="item-avatar" style="width: 32px; height: 32px; font-size: 12px; font-weight: 700; background: #fffbeb; color: #f59e0b; border: 1px solid #fef3c7;">
                                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--text-primary);"><?= htmlspecialchars($u['username']) ?></div>
                                            <div style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="update_loyalty">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <select name="loyalty_level" onchange="this.form.submit()" class="star-select">
                                            <option value="1" <?= ($u['loyalty_level'] ?? 1) == 1 ? 'selected' : '' ?>>⭐ 1 (New)</option>
                                            <option value="2" <?= ($u['loyalty_level'] ?? 1) == 2 ? 'selected' : '' ?>>⭐⭐ 2</option>
                                            <option value="3" <?= ($u['loyalty_level'] ?? 1) == 3 ? 'selected' : '' ?>>⭐⭐⭐ 3</option>
                                            <option value="4" <?= ($u['loyalty_level'] ?? 1) == 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ 4</option>
                                            <option value="5" <?= ($u['loyalty_level'] ?? 1) == 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ 5</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="update_loyalty">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" name="reset_loyalty" value="true" class="btn-refresh">
                                            <span>🔄</span> Reset
                                        </button>
                                    </form>
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
