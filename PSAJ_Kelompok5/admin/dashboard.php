<?php
require '../core/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        if ($_POST['action'] === 'delete_user') {
            $id = $_POST['user_id'];
            $conn->query("DELETE FROM users WHERE id = $id");
            $_SESSION['msg'] = "User berhasil dihapus.";
        }
        
        elseif ($_POST['action'] === 'update_role') {
            $id = $_POST['user_id'];
            $role = $_POST['role'];
            $conn->query("UPDATE users SET role = '$role' WHERE id = $id");
            $_SESSION['msg'] = "Role pengguna berhasil diperbarui.";
        }
        
        elseif ($_POST['action'] === 'update_status') {
            $id = $_POST['res_id'];
            $status = $_POST['status'];
            $conn->query("UPDATE reservations SET status = '$status' WHERE id = $id");
            $_SESSION['msg'] = "Status reservasi diperbarui.";
        }
        
        elseif ($_POST['action'] === 'delete_reservation') {
            $id = $_POST['res_id'];
            $conn->query("DELETE FROM reservations WHERE id = $id");
            $_SESSION['msg'] = "Data reservasi berhasil dihapus.";
        }
        
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
        
        elseif ($_POST['action'] === 'unlock_slot_id') {
            $id = $_POST['slot_id'];
            $conn->query("DELETE FROM locked_slots WHERE id = $id");
            $_SESSION['msg'] = "Slot waktu dibuka kembali.";
        }
        
        elseif ($_POST['action'] === 'approve_refund') {
            $id = $_POST['booking_id'];
            
            $conn->query("UPDATE reservations SET refund_status = 'approved', status = 'cancelled' WHERE id = $id");
            $_SESSION['msg'] = "Refund disetujui.";
        }
        elseif ($_POST['action'] === 'reject_refund') {
            $id = $_POST['booking_id'];
            $conn->query("UPDATE reservations SET refund_status = 'rejected' WHERE id = $id");
            $_SESSION['msg'] = "Refund ditolak.";
        }
        
        elseif ($_POST['action'] === 'delete_feedback') {
            $id = $_POST['feedback_id'];
            $conn->query("DELETE FROM feedback WHERE id = $id");
            $_SESSION['msg'] = "Kritik & Saran berhasil dihapus.";
        }
        
        
        header("Location: dashboard.php");
        exit();
    }
}


$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}


$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: 
        * { box-sizing: border-box; }

        .sidebar { 
            width: 260px; 
            background-color: 
            color: white; 
            height: 100vh; 
            padding: 20px; 
            position: sticky;
            top: 0;
            flex-shrink: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        .sidebar h2 { margin-top: 0; color: 
        .sidebar a { display: flex; align-items: center; gap: 12px; color: 
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; padding-left: 20px; }
        .sidebar a.active { background: 

        .admin-header-mobile { 
            display: none;
            background: 
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
        .sidebar-toggle { background: 

        .main-content { flex: 1; padding: 30px; width: 100%; overflow-x: hidden; }
        h1 { color: 
        
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-bottom: 30px; border: 1px solid 
        h3 { margin-top: 0; border-bottom: 2px solid 
        
        .table-container { 
            overflow-x: auto;
            margin-top: 15px; 
            border-radius: 12px; 
            border: 1px solid 
            background: white; 
        }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { text-align: left; padding: 16px; border-bottom: 1px solid 
        th { background-color: 
        tr:hover { background-color: 
        
        .btn { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; color: white; font-size: 12px; font-weight: 500; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-del { background-color: 
        .btn-edit { background-color: 
        .btn-lock { background-color: 
        .btn-view { background-color: 
        
        select { padding: 6px 10px; border-radius: 6px; border: 1px solid 
        
        .msg { background: 

        .time-slot-form { display: flex; gap: 15px; align-items: flex-end; background: 
        .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .form-group label { font-size: 0.85rem; color: 
        .form-input { padding: 10px; border: 1px solid 

        @media (max-width: 1200px) {
            body { flex-direction: column; }
            
            .admin-header-mobile { display: flex; }

            .sidebar { 
                position: fixed; 
                left: 0; 
                top: 0; 
                height: 100%; 
                transform: translateX(-100%); 
                box-shadow: 5px 0 15px rgba(0,0,0,0.2);
                z-index: 3000;
            }
            .sidebar.active { transform: translateX(0);  }

            
            .main-content { 
                padding: 90px 20px 40px; 
            }
        }

        
        @media (max-width: 600px) {
            .card { padding: 15px; }
            h1 { font-size: 1.5rem; }
            h3 { font-size: 1.1rem; }
            
            
            .time-slot-form { flex-direction: column; align-items: stretch; }
            .time-slot-form .btn-lock { width: 100%; margin-top: 10px; height: 40px; }
            
            .table-container { border-radius: 8px; }
        }
        
        
        .overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2500;
        }
        .overlay.active { display: block; }
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
        <a href="../index.php"><span>🏠</span> Halaman Utama</a>
        <a href="
        <a href="
        <a href="
        <a href="
        <a href="
        <a href="../auth/logout.php" style="color: 
    </div>

    <div class="main-content">
        <h1>Dashboard Overview</h1>
        <?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

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
                                <select name="role" onchange="this.form.submit()" style="background: <?= $u['role'] == 'admin' ? '
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
                        <?php while($r = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['reservation_date'] ?> <br> <small style="color:
                            <td>
                                <strong><?= htmlspecialchars($r['name']) ?></strong><br> 
                                <small>📞 <?= $r['phone'] ?></small>
                                <?php if($r['username']): ?><br><small>User: <?= $r['username'] ?></small><?php endif; ?>
                            </td>
                            <td>Price: Rp<?= number_format($r['service_type']) ?> <br> <small>Addon: Rp<?= number_format($r['addons']) ?></small></td>
                            <td>
                                Rp<?= number_format($r['total_price']) ?>
                                <?php if($r['refund_status']): ?>
                                    <br><span style="background: 
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                    <select name="status" onchange="this.form.submit()" style="width: 100px; background: <?= $r['status']=='pending'?'
                                        <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>Pending</option>
                                        <option value="confirmed" <?= $r['status']=='confirmed'?'selected':'' ?>>Confirmed</option>
                                        <option value="completed" <?= $r['status']=='completed'?'selected':'' ?>>Completed</option>
                                        <option value="cancelled" <?= $r['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:5px;">
                                <?php if($r['payment_proof']): ?>
                                    <a href="../<?= $r['payment_proof'] ?>" target="_blank" class="btn btn-view">Bukti Bayar</a>
                                <?php else: ?>
                                    <span style="font-size:11px; color:
                                <?php endif; ?>
                                
                                <form method="POST" onsubmit="return confirm('Hapus data reservasi ini?');">
                                    <input type="hidden" name="action" value="delete_reservation">
                                    <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-del" style="width:100%;">Hapus</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" id="refunds">
            <h3>Request Refund</h3>
            <p style="color:
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
                            <tr><td colspan="6" style="text-align: center; color: 
                        <?php else: ?>
                            <?php while($rf = $refund_requests->fetch_assoc()): ?>
                            <tr style="background: <?= $rf['refund_status'] == 'pending' ? '
                                <td>
                                <td>
                                    <?= htmlspecialchars($rf['username']) ?><br>
                                    <small><?= htmlspecialchars($rf['email']) ?></small>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($rf['reservation_date'])) ?> | <?= $rf['reservation_time'] ?><br>
                                    <strong>Rp<?= number_format($rf['total_price'], 0, ',', '.') ?></strong>
                                </td>
                                <td style="max-width: 250px;">
                                    <?= htmlspecialchars($rf['refund_reason']) ?><br>
                                    <small style="color: 
                                </td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: <?= $rf['refund_status']=='pending'?'
                                        <?= strtoupper($rf['refund_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <?php if ($rf['refund_status'] == 'pending'): ?>
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Setujui refund?');">
                                                <input type="hidden" name="action" value="approve_refund">
                                                <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                <button type="submit" class="btn" style="background: 
                                            </form>
                                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Tolak refund?');">
                                                <input type="hidden" name="action" value="reject_refund">
                                                <input type="hidden" name="booking_id" value="<?= $rf['id'] ?>">
                                                <button type="submit" class="btn btn-del">✗</button>
                                            </form>
                                        <?php else: ?>
                                            -
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

        <div class="card" id="slots">
            <h3>Kunci Jadwal (Lock Slots)</h3>
            <p style="color:
            
            <form method="POST" class="time-slot-form">
                <input type="hidden" name="action" value="lock_slot">
                <?php $today = date('Y-m-d'); ?>
                
                <div class="form-group">
                    <label>📅 Pilih Tanggal</label>
                    <input type="date" name="lock_date" class="form-input" required min="<?= $today ?>">
                </div>
                
                <div class="form-group">
                    <label>⏰ Pilih Jam</label>
                    <select name="lock_time" class="form-input" required>
                        <option value="09:00 WIB">09:00 WIB</option>
                        <option value="11:00 WIB">11:00 WIB</option>
                        <option value="13:00 WIB">13:00 WIB</option>
                        <option value="15:00 WIB">15:00 WIB</option>
                        <option value="17:00 WIB">17:00 WIB</option>
                        <option value="19:00 WIB">19:00 WIB</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-lock" style="height: 42px; padding: 0 25px;">Lock 🔒</button>
            </form>

            <h4 style="margin-top: 30px; color: 
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
                            <tr><td colspan="5" style="text-align: center; color: 
                        <?php else: ?>
                            <?php 
                            mysqli_data_seek($locked_slots, 0);
                            while($s = $locked_slots->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($s['date'])) ?></td>
                                <td><?= $s['time'] ?></td>
                                <td><span style="background: 
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

        <div class="card" id="feedback">
            <h3>Kritik & Saran Pelanggan</h3>
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
                            <tr><td colspan="4" style="text-align: center; color: 
                        <?php else: ?>
                            <?php while($f = $feedbacks->fetch_assoc()): ?>
                            <tr>
                                <td style="white-space: nowrap; font-size: 0.85rem;"><?= date('d M Y', strtotime($f['created_at'])) ?><br><small><?= date('H:i', strtotime($f['created_at'])) ?></small></td>
                                <td>
                                    <strong><?= htmlspecialchars($f['name']) ?></strong>
                                    <?php if($f['whatsapp_number']): ?>
                                        <br><a href="https://wa.me/62<?= ltrim($f['whatsapp_number'], '08') ?>" target="_blank" style="color: 
                                    <?php endif; ?>
                                </td>
                                <td style="min-width: 250px; line-height: 1.5; color: 
                                <td>
                                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Hapus pesan ini?');">
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

        
        const menuLinks = document.querySelectorAll('.sidebar a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                
                menuLinks.forEach(a => a.classList.remove('active'));
                
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
