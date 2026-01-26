<?php
require '../core/config.php';

// Check Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Actions (Delete User, Update Reservation, Lock Slot)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Delete User
        if ($_POST['action'] === 'delete_user') {
            $id = $_POST['user_id'];
            $conn->query("DELETE FROM users WHERE id = $id");
            $_SESSION['msg'] = "User berhasil dihapus.";
        }
        // Update User Role
        elseif ($_POST['action'] === 'update_role') {
            $id = $_POST['user_id'];
            $role = $_POST['role'];
            $conn->query("UPDATE users SET role = '$role' WHERE id = $id");
            $_SESSION['msg'] = "Role pengguna berhasil diperbarui.";
        }
        // Update Reservation Status
        elseif ($_POST['action'] === 'update_status') {
            $id = $_POST['res_id'];
            $status = $_POST['status'];
            $conn->query("UPDATE reservations SET status = '$status' WHERE id = $id");
            $_SESSION['msg'] = "Status reservasi diperbarui.";
        }
        // Delete Reservation
        elseif ($_POST['action'] === 'delete_reservation') {
            $id = $_POST['res_id'];
            $conn->query("DELETE FROM reservations WHERE id = $id");
            $_SESSION['msg'] = "Data reservasi berhasil dihapus.";
        }
        // Lock Slot
        elseif ($_POST['action'] === 'lock_slot') {
            $date = $_POST['lock_date'];
            $time = $_POST['lock_time'];
            $check = $conn->query("SELECT * FROM locked_slots WHERE date='$date' AND time='$time'");
            if ($check->num_rows == 0) {
                $conn->query("INSERT INTO locked_slots (date, time) VALUES ('$date', '$time')");
                $_SESSION['msg'] = "Slot waktu berhasil dikunci.";
            } else {
                $conn->query("DELETE FROM locked_slots WHERE date='$date' AND time='$time'");
                $_SESSION['msg'] = "Slot waktu dibuka kembali.";
            }
        }
        // Unlock Slot via ID
        elseif ($_POST['action'] === 'unlock_slot_id') {
            $id = $_POST['slot_id'];
            $conn->query("DELETE FROM locked_slots WHERE id = $id");
            $_SESSION['msg'] = "Slot waktu dibuka kembali.";
        }
        // Approve/Reject Refund
        elseif ($_POST['action'] === 'approve_refund') {
            $id = $_POST['booking_id'];
            // When approved, cancel the reservation
            $conn->query("UPDATE reservations SET refund_status = 'approved', status = 'cancelled' WHERE id = $id");
            $_SESSION['msg'] = "Refund disetujui.";
        }
        elseif ($_POST['action'] === 'reject_refund') {
            $id = $_POST['booking_id'];
            $conn->query("UPDATE reservations SET refund_status = 'rejected' WHERE id = $id");
            $_SESSION['msg'] = "Refund ditolak.";
        }
        // Delete Feedback
        elseif ($_POST['action'] === 'delete_feedback') {
            $id = $_POST['feedback_id'];
            $conn->query("DELETE FROM feedback WHERE id = $id");
            $_SESSION['msg'] = "Kritik & Saran berhasil dihapus.";
        }
        
        // Redirect to prevent form resubmission
        header("Location: dashboard.php");
        exit();
    }
}

// Get message from session
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Fetch Data
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
// Show ALL reservations (including with refund requests) to prevent "disappearing" data
$reservations = $conn->query("SELECT reservations.*, users.username FROM reservations LEFT JOIN users ON reservations.user_id = users.id ORDER BY reservation_date DESC, reservation_time ASC");
$locked_slots = $conn->query("SELECT * FROM locked_slots ORDER BY date, time");
$refund_requests = $conn->query("SELECT reservations.*, users.username, users.email FROM reservations LEFT JOIN users ON reservations.user_id = users.id WHERE refund_status IS NOT NULL ORDER BY refund_date DESC");
$feedbacks = $conn->query("SELECT feedback.*, users.username as user_uname FROM feedback LEFT JOIN users ON feedback.user_id = users.id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ney Dream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 250px; background-color: #2c3e50; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h2 { margin-top: 0; color: #ecf0f1; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 10px 0; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .sidebar a:hover { color: white; padding-left: 10px; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; height: 100vh; }
        
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        h3 { margin-top: 0; border-bottom: 2px solid #d63384; padding-bottom: 10px; display: inline-block; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #333; }
        tr:hover { background-color: #f1f1f1; }
        
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; color: white; font-size: 12px; }
        .btn-del { background-color: #e74c3c; }
        .btn-edit { background-color: #f39c12; }
        .btn-lock { background-color: #3498db; }
        
        select { padding: 5px; border-radius: 4px; border: 1px solid #ddd; }
        
        .msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }

        .time-slot-form { 
            display: flex; 
            gap: 15px; 
            align-items: flex-end; 
            background: #fff; 
            padding: 20px; 
            border: 1px solid #eee;
            border-radius: 12px; 
            max-width: 600px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .form-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #333;
            outline: none;
            transition: all 0.3s;
        }

        .form-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="../index.php" style="background: rgba(255,255,255,0.1); border-radius: 4px; padding-left: 10px;">🏠 Halaman Utama</a>
        <a href="#users">Kelola User</a>
        <a href="#reservations">Data Reservasi</a>
        <a href="#refunds">Refund Requests</a>
        <a href="#slots">Kelola Jadwal</a>
        <a href="#feedback">Kritik & Saran</a>
        <a href="../auth/logout.php" style="color: #e74c3c; margin-top: 50px;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Dashboard Overview</h1>
        <?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

        <!-- USERS SECTION -->
        <div class="card" id="users">
            <h3>Daftar Pengguna</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
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
                                <select name="role" onchange="this.form.submit()" style="padding: 2px 5px; border-radius: 4px; border: 1px solid #ddd; background: <?= $u['role'] == 'admin' ? '#e7f1ff' : '#fff' ?>;">
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

        <!-- RESERVATIONS SECTION -->
        <div class="card" id="reservations">
            <h3>Data Reservasi Masuk</h3>
            <div style="overflow-x:auto;">
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
                        <?php while($r = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['reservation_date'] ?> <br> <small><?= $r['reservation_time'] ?></small></td>
                            <td><?= htmlspecialchars($r['name']) ?> <br> <small><?= $r['phone'] ?></small></td>
                            <td>Price: Rp<?= number_format($r['service_type']) ?> <br> <small>Addon: Rp<?= number_format($r['addons']) ?></small></td>
                            <td>
                                Rp<?= number_format($r['total_price']) ?>
                                <?php if($r['refund_status']): ?>
                                    <br><span style="background: #e7f1ff; color: #004085; padding: 2px 5px; border-radius: 4px; font-size: 10px;">Refund: <?= $r['refund_status'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                    <select name="status" onchange="this.form.submit()" style="background: <?= $r['status']=='pending'?'#f1c40f':($r['status']=='confirmed'?'#2ecc71':'#e74c3c') ?>; color: #333;">
                                        <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>Pending</option>
                                        <option value="confirmed" <?= $r['status']=='confirmed'?'selected':'' ?>>Confirmed</option>
                                        <option value="completed" <?= $r['status']=='completed'?'selected':'' ?>>Completed</option>
                                        <option value="cancelled" <?= $r['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <!-- Link to Proof if exists -->
                                <?php if($r['payment_proof']): ?>
                                    <a href="../<?= $r['payment_proof'] ?>" target="_blank" class="btn btn-lock">Lihat Bukti</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                                
                                <!-- Delete Button -->
                                <form method="POST" style="display:inline; margin-left: 5px;" onsubmit="return confirm('Hapus data reservasi ini?');">
                                    <input type="hidden" name="action" value="delete_reservation">
                                    <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-del">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- REFUND REQUESTS SECTION -->
        <div class="card" id="refunds">
            <h3>Request Refund</h3>
            <p>Kelola permintaan refund dari customer</p>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>User</th>
                            <th>Reservasi</th>
                            <th>Total</th>
                            <th>Alasan Refund</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($refund_requests->num_rows == 0): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #999;">Tidak ada request refund</td>
                            </tr>
                        <?php else: ?>
                            <?php while($rf = $refund_requests->fetch_assoc()): ?>
                            <tr style="background: <?= $rf['refund_status'] == 'pending' ? '#fff3cd' : ($rf['refund_status'] == 'approved' ? '#d4edda' : '#f8d7da') ?>;">
                                <td>#<?= $rf['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($rf['username']) ?><br>
                                    <small><?= htmlspecialchars($rf['email']) ?></small>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($rf['reservation_date'])) ?><br>
                                    <small><?= $rf['reservation_time'] ?></small><br>
                                    <small><?= $rf['service_type'] ?></small>
                                </td>
                                <td>Rp<?= number_format($rf['total_price'], 0, ',', '.') ?></td>
                                <td style="max-width: 200px;">
                                    <?= htmlspecialchars($rf['refund_reason']) ?><br>
                                    <small style="color: #999;">Request: <?= date('d M Y H:i', strtotime($rf['refund_date'])) ?></small>
                                </td>
                                <td>
                                    <span style="padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: bold; background: <?= $rf['refund_status']=='pending'?'#f1c40f':($rf['refund_status']=='approved'?'#2ecc71':'#e74c3c') ?>; color: white;">
                                        <?= strtoupper($rf['refund_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                        <?php if ($rf['refund_status'] == 'pending'): ?>
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Setujui refund ini?');">
                                                <input type="hidden" name="action" value="approve_refund">
                                                <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                <button type="submit" class="btn" style="background: #2ecc71; padding: 5px 10px; font-size: 11px;">✓ Approve</button>
                                            </form>
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Tolak refund ini?');">
                                                <input type="hidden" name="action" value="reject_refund">
                                                <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                <button type="submit" class="btn btn-del" style="padding: 5px 10px; font-size: 11px;">✗ Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 11px; margin-right: 5px;">
                                                <?= $rf['refund_status'] == 'approved' ? 'Disetujui' : 'Ditolak' ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <!-- Delete Button -->
                                        <form method="POST" style="margin: 0;" onsubmit="return confirm('PERINGATAN: Menghapus item ini akan menghapus SELURUH DATA RESERVASI & RIWAYAT customer ini secara permanen. Lanjutkan?');">
                                            <input type="hidden" name="action" value="delete_reservation">
                                            <input type="hidden" name="res_id" value="<?= $rf['id'] ?>">
                                            <button type="submit" class="btn btn-del" style="background: #e74c3c; padding: 5px 10px; font-size: 11px;">Hapus Permanen</button>
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

        <!-- MANAGING SLOTS -->
        <div class="card" id="slots">
            <h3>Kunci Jadwal (Lock Slots)</h3>
            <p>Pilih tanggal dan jam untuk menutup reservasi secara manual.</p>
            
            <form method="POST" class="time-slot-form">
                <input type="hidden" name="action" value="lock_slot">
                <?php $today = date('Y-m-d'); ?>
                
                <div class="form-group">
                    <label>📅 Pilih Tanggal</label>
                    <input type="date" name="lock_date" class="form-input" required min="<?= $today ?>">
                </div>
                
                <div class="form-group">
                    <label>⏰ Pilih Jam</label>
                    <select name="lock_time" class="form-input" required style="min-width: 150px;">
                        <option value="09:00 WIB">09:00 WIB</option>
                        <option value="11:00 WIB">11:00 WIB</option>
                        <option value="13:00 WIB">13:00 WIB</option>
                        <option value="15:00 WIB">15:00 WIB</option>
                        <option value="17:00 WIB">17:00 WIB</option>
                        <option value="19:00 WIB">19:00 WIB</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-lock" style="height: 38px; padding: 0 20px; font-weight: 600;">Lock Slot 🔒</button>
            </form>

            <h4>Daftar Slot Terkunci:</h4>
            <div style="overflow-x:auto; margin-top: 15px;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
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
                            <tr>
                                <td colspan="5" style="text-align: center; color: #999;">Tidak ada slot yang dikunci.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            // Reset pointer just in case
                            mysqli_data_seek($locked_slots, 0);
                            while($s = $locked_slots->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($s['date'])) ?></td>
                                <td><?= $s['time'] ?></td>
                                <td>
                                    <span style="padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: bold; background: #e74c3c; color: white;">
                                        LOCKED
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Buka kunci slot ini?');">
                                        <input type="hidden" name="action" value="unlock_slot_id">
                                        <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-del" style="padding: 5px 10px; font-size: 12px; background: #3498db;">Buka Kunci</button>
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
            <h3>Kritik & Saran Pelanggan</h3>
            <p>Masukan terbaru dari para customer</p>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengirim</th>
                            <th>WhatsApp</th>
                            <th>Pesan</th>
                            <th>Status Akun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($feedbacks->num_rows == 0): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999;">Belum ada kritik atau saran.</td>
                            </tr>
                        <?php else: ?>
                            <?php while($f = $feedbacks->fetch_assoc()): ?>
                            <tr>
                                <td style="white-space: nowrap; font-size: 0.9rem;"><?= date('d M Y H:i', strtotime($f['created_at'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($f['name']) ?></strong>
                                    <?php if($f['user_uname']): ?><br><small>@<?= htmlspecialchars($f['user_uname']) ?></small><?php endif; ?>
                                </td>
                                <td>
                                    <?php if($f['whatsapp_number']): ?>
                                        <a href="https://wa.me/62<?= ltrim($f['whatsapp_number'], '08') ?>" target="_blank" style="color: #25d366; text-decoration: none; font-weight: 600;">
                                            <?= htmlspecialchars($f['whatsapp_number']) ?> 💬
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td style="max-width: 400px; line-height: 1.5; color: #444;"><?= nl2br(htmlspecialchars($f['message'])) ?></td>
                                <td>
                                    <?php if($f['user_id']): ?>
                                        <span style="color: #2ecc71; font-size: 0.8rem; font-weight: 600;">Reguler User</span>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.8rem;">Guest / Anonim</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus kritik/saran ini?');">
                                        <input type="hidden" name="action" value="delete_feedback">
                                        <input type="hidden" name="feedback_id" value="<?= $f['id'] ?>">
                                        <button type="submit" class="btn btn-del" style="padding: 5px 10px; font-size: 11px;">Hapus</button>
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
</body>
</html>
