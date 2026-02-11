<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'lock_slot') {
        $date = $_POST['lock_date'];
        $time = $_POST['lock_time'];
        $check = $conn->query("SELECT * FROM locked_slots WHERE date='$date' AND time='$time'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO locked_slots (date, time) VALUES ('$date', '$time')");
            $_SESSION['msg'] = "Slot waktu berhasil dikunci.";
        } else {
            $_SESSION['msg'] = "Slot waktu tersebut sudah terkunci sebelumnya.";
        }
    }
    elseif ($_POST['action'] === 'unlock_slot_id') {
        $id = intval($_POST['slot_id']);
        $conn->query("DELETE FROM locked_slots WHERE id = $id");
        $_SESSION['msg'] = "Slot waktu dibuka kembali.";
    }
    header("Location: slots.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$locked_slots = $conn->query("SELECT * FROM locked_slots ORDER BY date, time");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal - Admin Panel</title>
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
                    <span class="current">Jadwal Terkunci</span>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">🔒</div>
                <div class="context-info">
                    <div class="title">Pengaturan Jadwal Operasional</div>
                    <div class="subtitle">Kunci slot waktu tertentu untuk mencegah pelanggan melakukan reservasi pada jam tersebut.</div>
                </div>
            </div>

            <div style="padding: 24px;">
                <?php if($msg): ?>
                    <div class="item-card" style="border-left: 4px solid #6366f1; margin-bottom: 24px; padding: 12px 20px;">
                        <span style="color: var(--primary); font-weight: 600;">System:</span> <?= $msg ?>
                    </div>
                <?php endif; ?>

                <div class="responsive-grid grid-admin-sidebar-layout">
                    <div class="card mobile-order-1" style="padding: 24px;">
                        <h3 style="font-size: 16px; margin-bottom: 20px;">Kunci Slot Baru</h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="lock_slot">
                            <?php $today = date('Y-m-d'); ?>
                            
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Pilih Tanggal</label>
                                <input type="date" name="lock_date" class="btn btn-outline" style="width: 100%; text-align: left;" required min="<?= $today ?>">
                            </div>
                            
                            <div style="margin-bottom: 24px;">
                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Pilih Jam</label>
                                <select name="lock_time" class="btn btn-outline" style="width: 100%; text-align: left;" required>
                                    <option value="09:00 WIB">09:00 WIB</option>
                                    <option value="11:00 WIB">11:00 WIB</option>
                                    <option value="13:00 WIB">13:00 WIB</option>
                                    <option value="15:00 WIB">15:00 WIB</option>
                                    <option value="17:00 WIB">17:00 WIB</option>
                                    <option value="19:00 WIB">19:00 WIB</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Kunci Sekarang 🔒</button>
                        </form>
                    </div>

                    <div class="card mobile-order-2">
                        <div class="card-header">
                            <h3>Daftar Slot Terblokir</h3>
                            <div class="meta"><?= $locked_slots->num_rows ?> Slots Hidden</div>
                        </div>
                        <div class="table-container" style="overflow-x: auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Detail Jadwal</th>
                                        <th>Status Keamanan</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($locked_slots->num_rows == 0): ?>
                                        <tr><td colspan="3" style="text-align: center; padding: 60px; color: var(--text-muted);">Belum ada jadwal yang dikunci</td></tr>
                                    <?php else: ?>
                                        <?php while($s = $locked_slots->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 700; color: var(--text-primary);"><?= date('d M Y', strtotime($s['date'])) ?></div>
                                                <div style="font-size: 12px; color: var(--text-secondary);">Pukul <?= $s['time'] ?></div>
                                            </td>
                                            <td>
                                                <span style="background: var(--primary-light); color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800;">🔒 TERKUNCI</span>
                                            </td>
                                            <td>
                                                <form method="POST" style="margin: 0;" onsubmit="return confirm('Buka kunci slot ini?');">
                                                    <input type="hidden" name="action" value="unlock_slot_id">
                                                    <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
                                                    <button type="submit" class="btn btn-outline" style="padding: 6px 14px; font-size: 11px; color: var(--primary);">Unlock</button>
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
    </div>
</body>
</html>

