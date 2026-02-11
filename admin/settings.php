<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$msg = "";
$error = "";

// --- HANDLE POST ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Update Location
    if ($action === 'update_location') {
        $province = $_POST['province'];
        $city = $_POST['city'];
        $address = $_POST['full_address'];
        
        $stmt = $conn->prepare("UPDATE studio_settings SET province = ?, city = ?, full_address = ? WHERE id = 1");
        $stmt->bind_param("sss", $province, $city, $address);
        if ($stmt->execute()) $msg = "Lokasi studio berhasil diperbarui.";
        else $error = "Gagal memperbarui lokasi.";
    }

    // 1.5 Update Admin Profile Pic
    if ($action === 'update_admin_pic') {
        if (isset($_FILES['admin_profile_pic']) && $_FILES['admin_profile_pic']['error'] === 0) {
            $targetDir = "../assets/img/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = "admin_profile_" . time() . "_" . $_FILES['admin_profile_pic']['name'];
            if (move_uploaded_file($_FILES['admin_profile_pic']['tmp_name'], $targetDir . $fileName)) {
                $picPath = "assets/img/" . $fileName;
                
                // Self-healing: Check if column exists, if not, add it
                $colCheck = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'admin_profile_pic'");
                if ($colCheck && $colCheck->num_rows == 0) {
                    $conn->query("ALTER TABLE studio_settings ADD COLUMN admin_profile_pic VARCHAR(255) DEFAULT NULL");
                }

                $stmt = $conn->prepare("UPDATE studio_settings SET admin_profile_pic = ? WHERE id = 1");
                $stmt->bind_param("s", $picPath);
                if ($stmt->execute()) $msg = "Foto profil admin berhasil diperbarui.";
                else $error = "Gagal menyimpan foto profil.";
            }
        }
    }

    // 2. Add/Update Payment Method
    if ($action === 'save_payment') {
        $id = intval($_POST['method_id'] ?? 0);
        $name = $_POST['method_name'];
        $acc_name = $_POST['account_name'] ?? '';
        $acc_num = $_POST['account_number'] ?? '';
        $type = $_POST['type'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $qr_image = $_POST['existing_qr'] ?? '';
        
        // Handle QR Image Upload
        if ($type === 'qris' && isset($_FILES['qr_image']) && $_FILES['qr_image']['error'] === 0) {
            $targetDir = "../assets/img/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = time() . "_" . $_FILES['qr_image']['name'];
            if (move_uploaded_file($_FILES['qr_image']['tmp_name'], $targetDir . $fileName)) {
                $qr_image = "assets/img/" . $fileName;
            }
        }

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE payment_methods SET method_name=?, account_name=?, account_number=?, qr_image=?, type=?, is_active=? WHERE id=?");
            $stmt->bind_param("sssssii", $name, $acc_name, $acc_num, $qr_image, $type, $is_active, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO payment_methods (method_name, account_name, account_number, qr_image, type, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $name, $acc_name, $acc_num, $qr_image, $type, $is_active);
        }

        if ($stmt->execute()) $msg = "Metode pembayaran berhasil disimpan.";
        else $error = "Gagal menyimpan metode pembayaran.";
    }

    // 3. Delete Payment Method
    if ($action === 'delete_payment') {
        $id = intval($_POST['method_id']);
        if ($conn->query("DELETE FROM payment_methods WHERE id = $id")) $msg = "Metode pembayaran dihapus.";
    }
}

// --- FETCH DATA ---
$settings = ['province'=>'', 'city'=>'', 'full_address'=>'', 'admin_profile_pic'=>''];
$colCheck = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'admin_profile_pic'");
if ($colCheck && $colCheck->num_rows > 0) {
    $settingsRes = $conn->query("SELECT * FROM studio_settings WHERE id = 1");
    if ($settingsRes) $settings = $settingsRes->fetch_assoc() ?: $settings;
} else {
    $settingsRes = $conn->query("SELECT province, city, full_address FROM studio_settings WHERE id = 1");
    if ($settingsRes) $settings = array_merge($settings, $settingsRes->fetch_assoc() ?: []);
}

$payments = $conn->query("SELECT * FROM payment_methods ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Studio - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        /* Grid & Tab Style */
        .settings-grid { 
            display: grid; 
            grid-template-columns: 1fr 1.5fr; 
            gap: 24px; 
        }

        @media (max-width: 992px) {
            .settings-grid { grid-template-columns: 1fr; }
        }

        .tab-btn { padding: 12px 20px; border: none; background: none; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 2px solid transparent; transition: 0.3s; }
        .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
        .payment-card { border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); transition: 0.3s; }
        .payment-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .qr-preview { width: 50px; height: 50px; border-radius: 4px; object-fit: cover; }
        
        /* New Admin Profile Pic Styles */
        .upload-area {
            border: 2px dashed #e2e8f0; border-radius: 16px; padding: 20px;
            transition: 0.3s; cursor: pointer; position: relative; background: #f8fafc;
            text-align: center;
        }
        .upload-area:hover { border-color: var(--primary); background: #f1f5f9; }
        .upload-icon { font-size: 24px; color: var(--primary); margin-bottom: 8px; display: block; }
        .upload-text { font-size: 13px; font-weight: 700; color: #475569; display: block; }
        .upload-subtext { font-size: 11px; color: #94a3b8; display: block; margin-top: 4px; }
        
        .admin-avatar-preview {
            width: 70px; height: 70px; border-radius: 18px; margin: 0 auto 18px;
            overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            background: white; border: 3px solid white;
        }
        .admin-avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
        
        input[type="file"].hidden-input { 
            position: absolute; width: 100%; height: 100%; top: 0; left: 0; 
            opacity: 0; cursor: pointer; 
        }

        /* Responsive Mobile Tweak */
        @media (max-width: 576px) {
            .upload-area { padding: 15px; }
            .upload-text { font-size: 12px; }
            .admin-avatar-preview { width: 60px; height: 60px; }
        }
    </style>
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
                    <span class="current">Pengaturan Studio</span>
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">⚙️</div>
                <div class="context-info">
                    <div class="title">Pengaturan Studio</div>
                    <div class="subtitle">Kelola informasi lokasi dan metode pembayaran website.</div>
                </div>
            </div>

            <div style="padding: 24px;">
                <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999;"></div>

                <?php if($msg): ?>
                    <script>window.onload = () => showToast("<?= $msg ?>", "success");</script>
                <?php endif; ?>
                <?php if($error): ?>
                    <script>window.onload = () => showToast("<?= $error ?>", "error");</script>
                <?php endif; ?>

                <div class="settings-grid">
                    <!-- LEFT COLUMN: STUDIO PROFILE & LOCATION -->
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <!-- ADMIN PROFILE PIC -->
                        <div class="card">
                            <div class="card-header"><h3>👤 Profil Admin Website</h3></div>
                            <div style="padding: 24px;">
                                <div class="admin-avatar-preview">
                                    <?php if(!empty($settings['admin_profile_pic'])): ?>
                                        <img src="../<?= $settings['admin_profile_pic'] ?>" id="avatarPreview">
                                    <?php else: ?>
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--primary-light); color: var(--primary); font-size: 2rem;">👩‍💼</div>
                                    <?php endif; ?>
                                </div>
                                <form method="POST" enctype="multipart/form-data" id="adminPicForm">
                                    <input type="hidden" name="action" value="update_admin_pic">
                                    <div class="upload-area" onclick="document.getElementById('admin_pic_input').click()">
                                        <span class="upload-icon">☁️</span>
                                        <span class="upload-text">Klik untuk ganti foto</span>
                                        <span class="upload-subtext">PNG, JPG up to 2MB</span>
                                        <input type="file" name="admin_profile_pic" id="admin_pic_input" class="hidden-input" accept="image/*" onchange="this.form.submit()">
                                    </div>
                                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 12px; text-align: center;">
                                        *Foto ini akan muncul di profil chat pelanggan.
                                    </p>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h3>📍 Lokasi Studio</h3></div>
                            <div style="padding: 24px;">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_location">
                                    <div class="form-group" style="margin-bottom: 16px;">
                                        <label>Provinsi</label>
                                        <input type="text" name="province" value="<?= htmlspecialchars($settings['province']) ?>" style="width: 100%;" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 16px;">
                                        <label>Kota</label>
                                        <input type="text" name="city" value="<?= htmlspecialchars($settings['city']) ?>" style="width: 100%;" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 20px;">
                                        <label>Alamat Lengkap</label>
                                        <textarea name="full_address" rows="4" style="width: 100%;" required><?= htmlspecialchars($settings['full_address']) ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Simpan Lokasi</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: PAYMENTS -->
                    <div class="card">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h3>💳 Metode Pembayaran</h3>
                            <button onclick="openPaymentModal()" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">+ Tambah</button>
                        </div>
                        <div style="padding: 24px;">
                            <?php while($p = $payments->fetch_assoc()): ?>
                            <div class="payment-card">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <?php if($p['type'] === 'qris'): ?>
                                        <img src="../<?= $p['qr_image'] ?>" class="qr-preview" alt="QR">
                                    <?php else: ?>
                                        <div style="font-size: 24px;">🏦</div>
                                    <?php endif; ?>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; text-align: left;"><?= htmlspecialchars($p['method_name']) ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted); text-align: left;">
                                            <?= $p['type'] === 'qris' ? 'Scan QRIS' : $p['account_number'] . " (a/n " . $p['account_name'] . ")" ?>
                                        </div>
                                        <?php if(!$p['is_active']): ?>
                                            <span style="font-size: 10px; color: #ef4444; font-weight: 700;">[ NON-AKTIF ]</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button onclick='editPayment(<?= json_encode($p) ?>)' class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">Edit</button>
                                    <button onclick="confirmDeletePayment(<?= $p['id'] ?>, '<?= addslashes($p['method_name']) ?>')" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; color: #ef4444;">Hapus</button>
                                    
                                    <!-- Hidden form for deletion -->
                                    <form id="delete-payment-<?= $p['id'] ?>" method="POST" style="display:none;">
                                        <input type="hidden" name="action" value="delete_payment">
                                        <input type="hidden" name="method_id" value="<?= $p['id'] ?>">
                                    </form>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="card" style="width: 450px; padding: 32px; max-height: 90vh; overflow-y: auto;">
            <h3 id="modalTitle" style="margin-bottom: 24px; text-align: center;">Tambah Pembayaran</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_payment">
                <input type="hidden" name="method_id" id="edit_id" value="">
                <input type="hidden" name="existing_qr" id="edit_existing_qr" value="">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Nama Metode (Contoh: BCA / Dana)</label>
                    <input type="text" name="method_name" id="edit_name" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Jenis</label>
                    <select name="type" id="edit_type" onchange="toggleFieldDisplay()" style="width: 100%;" required>
                        <option value="transfer">Bank / Wallet Transfer</option>
                        <option value="qris">QRIS (Upload Gambar)</option>
                    </select>
                </div>

                <div id="transfer_fields">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label>Atas Nama (a/n)</label>
                        <input type="text" name="account_name" id="edit_acc_name" style="width: 100%;">
                    </div>
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label>Nomor Rekening / HP</label>
                        <input type="text" name="account_number" id="edit_acc_num" style="width: 100%;">
                    </div>
                </div>

                <div id="qris_fields" style="display: none;">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Upload Gambar QRIS</label>
                        <div class="upload-area" onclick="document.getElementById('qr_pic_input').click()" style="padding: 15px;">
                            <span class="upload-icon" style="font-size: 20px;">🖼️</span>
                            <span class="upload-text" style="font-size: 12px;">Pilih QR Code</span>
                            <input type="file" name="qr_image" id="qr_pic_input" class="hidden-input" accept="image/*">
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                    <input type="checkbox" name="is_active" id="edit_active" checked>
                    <label for="edit_active" style="display: inline; cursor: pointer;">Metode Aktif</label>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button type="button" onclick="closePaymentModal()" class="btn btn-outline" style="justify-content: center;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="justify-content: center;">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.style.cssText = `
                padding: 12px 24px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                border-radius: 12px;
                margin-bottom: 10px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                animation: slideIn 0.3s ease-out forwards;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
            `;
            toast.innerHTML = `<span>${type === 'success' ? '✅' : '❌'}</span> ${message}`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        const toastStyle = document.createElement('style');
        toastStyle.innerHTML = `
            @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
            @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
        `;
        document.head.appendChild(toastStyle);
        function openPaymentModal() {
            document.getElementById('modalTitle').innerText = "Tambah Pembayaran";
            document.getElementById('edit_id').value = "";
            document.getElementById('edit_name').value = "";
            document.getElementById('edit_acc_name').value = "";
            document.getElementById('edit_acc_num').value = "";
            document.getElementById('edit_existing_qr').value = "";
            document.getElementById('edit_type').value = "transfer";
            document.getElementById('edit_active').checked = true;
            toggleFieldDisplay();
            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closePaymentModal() { document.getElementById('paymentModal').style.display = 'none'; }

        function confirmDeletePayment(id, name) {
            showGlobalConfirm(
                "Hapus Pembayaran", 
                `Apakah Kakak yakin ingin menghapus metode "${name}"?`, 
                "delete", 
                () => { document.getElementById('delete-payment-' + id).submit(); }
            );
        }

        function toggleFieldDisplay() {
            const type = document.getElementById('edit_type').value;
            document.getElementById('transfer_fields').style.display = type === 'transfer' ? 'block' : 'none';
            document.getElementById('qris_fields').style.display = type === 'qris' ? 'block' : 'none';
        }

        function editPayment(data) {
            document.getElementById('modalTitle').innerText = "Edit Pembayaran";
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.method_name;
            document.getElementById('edit_acc_name').value = data.account_name;
            document.getElementById('edit_acc_num').value = data.account_number;
            document.getElementById('edit_existing_qr').value = data.qr_image;
            document.getElementById('edit_type').value = data.type;
            document.getElementById('edit_active').checked = parseInt(data.is_active) === 1;
            toggleFieldDisplay();
            document.getElementById('paymentModal').style.display = 'flex';
        }
    </script>
</body>
</html>
