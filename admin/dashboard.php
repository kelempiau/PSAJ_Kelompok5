<?php
require '../core/config.php';

// --- AUTHENTICATION CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// --- HANDLE POST ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        // 1. Delete User
        if ($_POST['action'] === 'delete_user') {
            $id = $_POST['user_id'];
            $conn->query("DELETE FROM users WHERE id = $id");
            $_SESSION['msg'] = "User berhasil dihapus.";
        }
        
        // 2. Update Role
        elseif ($_POST['action'] === 'update_role') {
            $id = $_POST['user_id'];
            $role = $_POST['role'];
            $conn->query("UPDATE users SET role = '$role' WHERE id = $id");
            $_SESSION['msg'] = "Role pengguna berhasil diperbarui.";
        }
        
        // 3. Update Reservation Status
        elseif ($_POST['action'] === 'update_status') {
            $id = $_POST['res_id'];
            $status = $_POST['status'];
            $conn->query("UPDATE reservations SET status = '$status' WHERE id = $id");
            $_SESSION['msg'] = "Status reservasi diperbarui.";
        }
        
        // 4. Delete Reservation
        elseif ($_POST['action'] === 'delete_reservation') {
            $id = $_POST['res_id'];
            $conn->query("DELETE FROM reservations WHERE id = $id");
            $_SESSION['msg'] = "Data reservasi berhasil dihapus.";
        }
        
        // 5. Lock Time Slot
        elseif ($_POST['action'] === 'lock_slot') {
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
        
        // 6. Unlock Slot
        elseif ($_POST['action'] === 'unlock_slot_id') {
            $id = $_POST['slot_id'];
            $conn->query("DELETE FROM locked_slots WHERE id = $id");
            $_SESSION['msg'] = "Slot waktu dibuka kembali.";
        }
        
        // 7. Approve Refund
        elseif ($_POST['action'] === 'approve_refund') {
            $id = $_POST['booking_id'];
            // Update reservation to approved and status to cancelled
            $conn->query("UPDATE reservations SET refund_status = 'approved', status = 'cancelled' WHERE id = $id");
            $_SESSION['msg'] = "Refund disetujui.";
        }
        
        // 8. Reject Refund
        elseif ($_POST['action'] === 'reject_refund') {
            $id = $_POST['booking_id'];
            $conn->query("UPDATE reservations SET refund_status = 'rejected' WHERE id = $id");
            $_SESSION['msg'] = "Refund ditolak.";
        }
        
        // 9. Delete Feedback
        elseif ($_POST['action'] === 'delete_feedback') {
            $id = $_POST['feedback_id'];
            $conn->query("DELETE FROM feedback WHERE id = $id");
            $_SESSION['msg'] = "Kritik & Saran berhasil dihapus.";
        }
        
        // Refresh page to avoid re-submission
        header("Location: dashboard.php");
        exit();
    }
}

// --- FETCH DATA FOR DASHBOARD ---
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

// Get all reservations with username
$reservations = $conn->query("SELECT reservations.*, users.username FROM reservations LEFT JOIN users ON reservations.user_id = users.id ORDER BY reservation_date DESC, reservation_time ASC");

// Get locked slots
$locked_slots = $conn->query("SELECT * FROM locked_slots ORDER BY date, time");

// Get refund requests
$refund_requests = $conn->query("SELECT reservations.*, users.username, users.email FROM reservations LEFT JOIN users ON reservations.user_id = users.id WHERE refund_status IS NOT NULL ORDER BY refund_date DESC");

// Get feedback
$feedbacks = $conn->query("SELECT feedback.*, users.username as user_uname FROM feedback LEFT JOIN users ON feedback.user_id = users.id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ney Dream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; margin: 0; display: flex; transition: all 0.3s; min-height: 100vh; color: #1e293b; }
        * { box-sizing: border-box; }

        /* Sidebar Styling */
        .sidebar { 
            width: 260px; 
            background-color: #1e293b; 
            color: white; 
            height: 100vh; 
            padding: 20px; 
            position: sticky;
            top: 0;
            flex-shrink: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 { margin-top: 0; color: #f8fafc; font-size: 1.4rem; margin-bottom: 30px; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; font-size: 0.95rem; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; padding-left: 20px; }
        .sidebar a.active { background: #d63384; color: white; box-shadow: 0 4px 10px rgba(214, 51, 132, 0.3); }

        /* Mobile Header */
        .admin-header-mobile { 
            display: none;
            background: #1e293b; 
            color: white; 
            padding: 15px 20px; 
            justify-content: space-between; 
            align-items: center; 
            position: fixed; 
            top: 0; left: 0; width: 100%; 
            z-index: 2000; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .admin-header-mobile h2 { margin: 0; font-size: 1.2rem; }
        .sidebar-toggle { background: #d63384; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; font-weight: 500; font-family: inherit; }

        /* Main Content */
        .main-content { flex: 1; padding: 30px; width: 100%; overflow-x: hidden; }
        h1 { color: #1e293b; margin-bottom: 30px; font-weight: 600; font-size: 1.8rem; }
        
        .card { 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
            margin-bottom: 30px; 
            border: 1px solid #e2e8f0; 
        }
        h3 { 
            margin-top: 0; 
            color: #1e293b; 
            margin-bottom: 25px; 
            font-size: 1.2rem; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        h3::before {
            content: '';
            width: 4px;
            height: 20px;
            background: #d63384;
            border-radius: 2px;
        }
        
        /* Table Styles */
        .table-container { overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; background: white; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { text-align: left; padding: 16px; border-bottom: 1px solid #f1f5f9; }
        th { background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:hover { background-color: #f8fafc; }
        
        /* Buttons */
        .btn { padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; color: white; font-size: 12px; font-weight: 500; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-del { background-color: #ef4444; }
        .btn-edit { background-color: #f59e0b; }
        .btn-lock { background-color: #1e293b; }
        .btn-view { background-color: #3b82f6; }
        
        /* Form Elements */
        select { padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; transition: 0.2s; font-family: inherit; }
        select:focus { border-color: #d63384; ring: 2px solid rgba(214, 51, 132, 0.1); }
        
        .msg { background: #dcfce7; color: #166534; padding: 12px 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #bbf7d0; font-weight: 500; }

        .time-slot-form { display: flex; gap: 15px; align-items: flex-end; background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #e2e8f0; }
        .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .form-group label { font-size: 0.85rem; color: #64748b; font-weight: 500; }
        .form-input { padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none; transition: 0.2s; }
        .form-input:focus { border-color: #d63384; }

        /* Mobile Responsiveness */
        @media (max-width: 1200px) {
            body { flex-direction: column; }
            .admin-header-mobile { display: flex; }
            .sidebar { 
                position: fixed; 
                left: 0; top: 0; 
                height: 100%; 
                transform: translateX(-100%); 
                box-shadow: 10px 0 30px rgba(0,0,0,0.1);
                z-index: 3000;
            }
            .sidebar.active { transform: translateX(0);  }
            .main-content { padding: 90px 20px 40px; }
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(4px);
            z-index: 2500;
        }
        .overlay.active { display: block; }

        @media (max-width: 600px) {
            .card { padding: 15px; }
            h1 { font-size: 1.5rem; }
            .time-slot-form { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <header class="admin-header-mobile">
        <h2>Admin Panel</h2>
        <button class="sidebar-toggle" onclick="toggleSidebar()">☰ Menu</button>
    </header>

    <div class="sidebar" id="sidebar">
        <h2>Admin Panel</h2>
        <a href="../index.php" onclick="closeSidebarOnMobile()"><span>🏠</span> Halaman Utama</a>
        <a href="dashboard.php" class="active" onclick="closeSidebarOnMobile()"><span>📊</span> Dashboard</a>
        <a href="conversations.php" onclick="closeSidebarOnMobile()"><span>💬</span> Conversations</a>
        <a href="#users" onclick="closeSidebarOnMobile()"><span>👥</span> Pengguna</a>
        <a href="#reservations" onclick="closeSidebarOnMobile()"><span>📅</span> Reservasi</a>
        <a href="#slots" onclick="closeSidebarOnMobile()"><span>🔒</span> Kunci Jadwal</a>
        <a href="#feedback" onclick="closeSidebarOnMobile()"><span>📣</span> Feedback</a>
        <a href="../auth/logout.php" style="color: #fca5a5; margin-top: auto;" onclick="closeSidebarOnMobile()"><span>🚪</span> Logout</a>
    </div>

    <div class="main-content">
        <h1>Dashboard Overview</h1>
        <?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

        <!-- USERS SECTION -->
        <div class="card" id="users">
            <h3>Daftar Pengguna</h3>
            <div class="table-container">
                <table>
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="role" onchange="this.form.submit()" style="background: <?= $u['role'] == 'admin' ? '#f0fdf4' : '#f8fafc' ?>; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; color: <?= $u['role'] == 'admin' ? '#166534' : '#475569' ?>; font-weight: 600;">
                                    <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <?php if($u['role'] !== 'admin'): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus user ini?');">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-del">Hapus</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                </table>
            </div>
        </div>

        <!-- RESERVATIONS SECTION -->
        <div class="card" id="reservations">
            <h3>Data Reservasi Masuk</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl & Jam</th>
                            <th>Nama Client</th>
                            <th>Layanan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reservations->num_rows == 0): ?>
                            <tr><td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">Belum ada data reservasi.</td></tr>
                        <?php else: ?>
                            <?php while($r = $reservations->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($r['reservation_date'])) ?> <br> <small><?= $r['reservation_time'] ?></small></td>
                                <td><?= htmlspecialchars($r['name'] ?? $r['username']) ?> <br> <small><?= $r['phone'] ?? '-' ?></small></td>
                                <td>Price: Rp<?= number_format($r['service_type']) ?> <br> <small>Addon: Rp<?= number_format($r['addons']) ?></small></td>
                                <td>
                                    Rp<?= number_format($r['total_price']) ?>
                                    <?php if($r['refund_status']): ?>
                                        <br><span style="background: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 600;">Refund: <?= htmlspecialchars(strtoupper($r['refund_status'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                        <select name="status" onchange="this.form.submit()" style="width: 120px; background: <?= $r['status']=='pending'?'#fff7ed':($r['status']=='confirmed'?'#eff6ff':($r['status']=='completed'?'#f0fdf4':'#fef2f2')) ?>; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px; color: <?= $r['status']=='pending'?'#9a3412':($r['status']=='confirmed'?'#1e40af':($r['status']=='completed'?'#166534':'#991b1b')) ?>; font-weight: 600;">
                                            <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>Pending</option>
                                            <option value="confirmed" <?= $r['status']=='confirmed'?'selected':'' ?>>Confirmed</option>
                                            <option value="completed" <?= $r['status']=='completed'?'selected':'' ?>>Completed</option>
                                            <option value="cancelled" <?= $r['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <div style="display:flex; gap: 5px;">
                                        <?php if($r['payment_proof']): ?>
                                            <a href="../<?= $r['payment_proof'] ?>" target="_blank" class="btn btn-view">Bukti</a>
                                        <?php endif; ?>
                                        <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus reservasi ini?');">
                                            <input type="hidden" name="action" value="delete_reservation">
                                            <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                            <button type="submit" class="btn btn-del">Hapus</button>
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

        <!-- REFUND REQUESTS SECTION -->
        <div class="card" id="refunds">
            <h3>Permintaan Refund</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Reservasi</th>
                            <th>Alasan Refund</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($refund_requests->num_rows == 0): ?>
                            <tr><td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">Tidak ada permintaan refund.</td></tr>
                        <?php else: ?>
                            <?php while($rf = $refund_requests->fetch_assoc()): ?>
                            <tr style="background: <?= $rf['refund_status'] == 'pending' ? '#fff7ed' : '#ffffff' ?>;">
                                <td><?= $rf['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($rf['username']) ?></strong><br>
                                    <small><?= htmlspecialchars($rf['email']) ?></small>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($rf['reservation_date'])) ?> | <?= $rf['reservation_time'] ?><br>
                                    <strong>Rp<?= number_format($rf['total_price'], 0, ',', '.') ?></strong>
                                </td>
                                <td style="max-width: 250px; font-size: 0.9rem;">
                                    <?= htmlspecialchars($rf['refund_reason']) ?><br>
                                    <small style="color: #64748b;"><?= date('d/m/Y H:i', strtotime($rf['refund_date'])) ?></small>
                                </td>
                                <td>
                                    <span style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; background: <?= $rf['refund_status']=='pending'?'#fed7aa':($rf['refund_status']=='approved'?'#bbf7d0':'#fecaca') ?>; color: <?= $rf['refund_status']=='pending'?'#9a3412':($rf['refund_status']=='approved'?'#166534':'#991b1b') ?>;">
                                        <?= strtoupper($rf['refund_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <?php if ($rf['refund_status'] == 'pending'): ?>
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Setujui refund?');">
                                                <input type="hidden" name="action" value="approve_refund">
                                                <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                <button type="submit" class="btn" style="background: #22c55e;">Approve</button>
                                            </form>
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Tolak refund?');">
                                                <input type="hidden" name="action" value="reject_refund">
                                                <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                <button type="submit" class="btn btn-del">Tolak</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.8rem;">Sudah diproses</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- LOCK SLOTS SECTION -->
        <div class="card" id="slots">
            <h3>Kelola Jam Operasional</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Kunci slot waktu tertentu agar tidak bisa dipilih oleh pelanggan.</p>
            
            <form method="POST" class="time-slot-form">
                <input type="hidden" name="action" value="lock_slot">
                <?php $today = date('Y-m-d'); ?>
                
                <div class="form-group">
                    <label>📅 Tanggal</label>
                    <input type="date" name="lock_date" class="form-input" required min="<?= $today ?>">
                </div>
                
                <div class="form-group">
                    <label>⏰ Jam</label>
                    <select name="lock_time" class="form-input" required>
                        <option value="09:00 WIB">09:00 WIB</option>
                        <option value="11:00 WIB">11:00 WIB</option>
                        <option value="13:00 WIB">13:00 WIB</option>
                        <option value="15:00 WIB">15:00 WIB</option>
                        <option value="17:00 WIB">17:00 WIB</option>
                        <option value="19:00 WIB">19:00 WIB</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-lock" style="height: 42px; padding: 0 25px;">Kunci Slot 🔒</button>
            </form>

            <h4 style="margin-top: 30px; color: #1e293b; font-size: 1rem;">Daftar Slot Terkunci</h4>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if ($locked_slots->num_rows == 0): 
                        ?>
                            <tr><td colspan="5" style="text-align: center; color: #64748b; padding: 20px;">Belum ada slot waktu yang dikunci.</td></tr>
                        <?php else: ?>
                            <?php 
                            mysqli_data_seek($locked_slots, 0);
                            while($s = $locked_slots->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($s['date'])) ?></td>
                                <td><?= $s['time'] ?></td>
                                <td><span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">LOCKED</span></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Buka kunci slot ini?');">
                                        <input type="hidden" name="action" value="unlock_slot_id">
                                        <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-view">Buka Kunci</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FEEDBACK SECTION -->
        <div class="card" id="feedback">
            <h3>Feedback Pelanggan</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengirim</th>
                            <th>Pesan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($feedbacks->num_rows == 0): ?>
                            <tr><td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">Belum ada feedback masuk.</td></tr>
                        <?php else: ?>
                            <?php while($f = $feedbacks->fetch_assoc()): ?>
                            <tr>
                                <td style="white-space: nowrap; font-size: 0.85rem;"><?= date('d M Y', strtotime($f['created_at'])) ?><br><small><?= date('H:i', strtotime($f['created_at'])) ?></small></td>
                                <td>
                                    <strong><?= htmlspecialchars($f['name']) ?></strong>
                                    <?php if($f['whatsapp_number']): ?>
                                        <br><a href="https://wa.me/62<?= ltrim($f['whatsapp_number'], '08') ?>" target="_blank" style="color: #22c55e; font-size: 0.8rem; text-decoration: none;">💬 Hubungi WA</a>
                                    <?php endif; ?>
                                </td>
                                <td style="min-width: 250px; line-height: 1.5; color: #334155; font-size: 0.9rem;"><?= nl2br(htmlspecialchars($f['message'])) ?></td>
                                <td>
                                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus feedback ini?');">
                                        <input type="hidden" name="action" value="delete_feedback">
                                        <input type="hidden" name="feedback_id" value="<?= $f['id'] ?>">
                                        <button type="submit" class="btn btn-del">Hapus</button>
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

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth <= 1200) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        }
    </script>
</body>
</html>
