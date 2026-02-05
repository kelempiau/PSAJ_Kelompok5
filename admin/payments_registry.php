<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'settle_balance') {
        $id = intval($_POST['res_id']);
        $res = $conn->query("SELECT total_price FROM reservations WHERE id = $id");
        if ($row = $res->fetch_assoc()) {
            $total = $row['total_price'];
            $conn->query("UPDATE reservations SET balance_due = 0, amount_paid = $total WHERE id = $id");
            $_SESSION['msg'] = "Pembayaran sisa telah dilunasi secara manual.";
        }
    }
    header("Location: payments_registry.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Only show reservations that are not Full Payment (only DP Transfer/Cash) or all for registry?
// User said: "daftar pelunasan jadi yang sudah atau yang belum bayar sisa dpnya bakal ada datanya"
// Better show all reservations that have balance_due > 0 or have had balance_due in the past (DP orders).
$reservations = $conn->query("SELECT reservations.*, users.username FROM reservations LEFT JOIN users ON reservations.user_id = users.id WHERE payment_type != 'full' ORDER BY reservation_date DESC, reservation_time ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelunasan - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .modal-fancy {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 99999;
            align-items: center; justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .modal-fancy-content {
            background: var(--bg-card);
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            transform: scale(0.9);
            animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        .btn-green { background: #10b981 !important; color: white !important; border:none !important; }
        .btn-green:hover { background: #059669 !important; transform: translateY(-1px); }
        .btn-blue { background: #3b82f6 !important; color: white !important; border:none !important; }
        .btn-blue:hover { background: #2563eb !important; transform: translateY(-1px); }
        .btn-text { text-decoration: none !important; font-weight: 700; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="breadcrumbs">
                    <a href="dashboard.php" class="sep" style="text-decoration:none; color:var(--text-muted);">Dashboard</a>
                    <span class="sep" style="margin:0 8px; color:var(--text-muted);">/</span>
                    <span class="current">Daftar Pelunasan</span>
                </div>
            </header>

            <main class="content-body" style="padding: 24px;">
                <div class="card">
                    <div style="padding: 32px; border-bottom: 1px solid var(--border-color); background: var(--bg-card);">
                        <h2 style="margin:0; font-size: 24px; font-weight: 800; color: var(--text-primary);">📋 Daftar Pelunasan Sisa</h2>
                        <p style="margin: 8px 0 0; color: var(--text-secondary); font-size: 14px;">Pantau dan kelola sisa tagihan reservasi dengan sistem DP secara terpusat.</p>
                    </div>

                    <?php if($msg): ?>
                        <div style="margin: 24px; background: #ecfdf5; color: #065f46; padding: 16px; border-radius: 12px; font-size: 14px; border: 1px solid #a7f3d0; font-weight: 600;">
                            ✨ <?= $msg ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Client & Jadwal</th>
                                    <th>Status Pembayaran</th>
                                    <th>Metode & Tipe DP</th>
                                    <th>Manajemen Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($reservations->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 60px; color: var(--text-muted); font-style: italic;">Tidak ada data pelunasan sisa saat ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php while($r = $reservations->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info-cell">
                                                <div class="user-avatar-small" style="background: var(--primary-light); color: var(--primary); font-size: 16px;">👤</div>
                                                <div>
                                                    <div style="font-weight: 800; color: var(--text-primary); font-size: 15px;"><?= htmlspecialchars($r['name'] ?? $r['username']) ?></div>
                                                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">📅 <?= date('d M Y', strtotime($r['reservation_date'])) ?> • <?= $r['reservation_time'] ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; font-size: 16px;">Sisa: <span style="color: <?= $r['balance_due'] > 0 ? '#ef4444' : '#10b981' ?>;">Rp<?= number_format($r['balance_due']) ?></span></div>
                                            <div style="font-size: 11px; margin-top: 6px; color: var(--text-secondary);">
                                                Total: Rp<?= number_format($r['total_price']) ?> | Terbayar: Rp<?= number_format($r['amount_paid']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;"><?= str_replace('_', ' ', $r['payment_type']) ?></div>
                                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;"><?= strtoupper($r['payment_method']) ?></div>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                <?php if($r['payment_proof']): ?>
                                                    <button onclick="openProofModal('../<?= $r['payment_proof'] ?>', 'Bukti Transaksi')" class="btn btn-blue btn-text" style="padding: 10px; font-size: 11px; border-radius: 8px; justify-content: center; border:none; width:100%; cursor:pointer;">Bukti Transaksi</button>
                                                <?php endif; ?>

                                                <?php if($r['balance_due'] > 0): ?>
                                                    <button onclick="confirmSettle(<?= $r['id'] ?>)" class="btn btn-green btn-text" style="padding: 10px; font-size: 11px; border-radius: 8px; justify-content: center; border:none; width:100%; cursor:pointer;">Lunasi</button>
                                                    
                                                    <?php if(!empty($r['final_payment_proof'])): ?>
                                                        <button onclick="openProofModal('../<?= $r['final_payment_proof'] ?>', 'Bukti Pelunasan')" class="btn btn-blue btn-text" style="padding: 10px; font-size: 11px; border-radius: 8px; justify-content: center; border:none; width:100%; cursor:pointer;">Bukti Pelunasan</button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <div style="color: #10b981; font-weight: 800; font-size: 11px; text-align: center; background: #ecfdf5; padding: 8px; border-radius: 8px;">✨ TERBAYAR LUNAS</div>
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
            </main>
        </div>
    </div>

    <!-- Custom Modal Popup -->
    <div id="settleModal" class="modal-fancy">
        <div class="modal-fancy-content">
            <div style="font-size: 3.5rem; margin-bottom: 20px;">💰</div>
            <h3 style="margin-bottom: 12px; font-weight: 800; font-size: 20px; color: var(--text-primary);">Konfirmasi Pelunasan</h3>
            <p style="color: var(--text-secondary); margin-bottom: 32px; font-size: 14px; line-height: 1.6;">Apakah pesanan ini sudah beneran lunas? Tindakan ini akan mengupdate sisa tagihan menjadi Rp0.</p>
            
            <form id="settleForm" method="POST">
                <input type="hidden" name="action" value="settle_balance">
                <input type="hidden" name="res_id" id="modalResId">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button type="button" onclick="closeSettleModal()" class="btn btn-outline" style="justify-content: center; border-radius: 12px;">Belum</button>
                    <button type="submit" class="btn btn-green" style="justify-content: center; border:none; border-radius: 12px; cursor:pointer;">Lunas</button>
                </div>
            </form>
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

        function confirmSettle(id) {
            document.getElementById('modalResId').value = id;
            document.getElementById('settleModal').style.display = 'flex';
        }
        function closeSettleModal() {
            document.getElementById('settleModal').style.display = 'none';
        }
        window.onclick = function(event) {
            let modal = document.getElementById('settleModal');
            if (event.target == modal) {
                closeSettleModal();
            }
        }
    </script>
</body>
</html>
