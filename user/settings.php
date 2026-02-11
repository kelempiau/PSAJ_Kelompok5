<?php
require '../core/config.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_popup = false;
$error_message = "";
$visible_password = "•••••••••••••"; // Default censored view

// Logic to show NEW password after change
if (isset($_SESSION['temp_new_password'])) {
    $visible_password = $_SESSION['temp_new_password'];
    // Optional: unset it immediately if you want it shown only ONCE (refresh will hide it)
    // unset($_SESSION['temp_new_password']); 
}

// Fetch User Data
$user = ['username' => '', 'email' => '', 'phone' => '', 'password' => '', 'loyalty_level' => '', 'profile_pic' => null];
$colCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_pic'");
if ($colCheck && $colCheck->num_rows > 0) {
    $stmt = $conn->prepare("SELECT username, email, phone, password, loyalty_level, profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
} else {
    $stmt = $conn->prepare("SELECT username, email, phone, password, loyalty_level FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();
    if ($userData) $user = array_merge($user, $userData);
}

// Statistics: Count Reservations
$res_count_query = $conn->prepare("SELECT COUNT(*) as total FROM reservations WHERE user_id = ?");
$res_count_query->bind_param("i", $user_id);
$res_count_query->execute();
$stats = $res_count_query->get_result()->fetch_assoc();
$total_reservations = $stats['total'] ?? 0;

// Handle POST actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_email') {
        $new_email = $_POST['new_email'];
        $update = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $update->bind_param("si", $new_email, $user_id);
        if ($update->execute()) { $success_popup = true; }
        else { $error_message = "Gagal memperbarui email. Mungkin sudah digunakan."; }
    } 
    elseif ($action === 'update_phone') {
        $new_phone = $_POST['new_phone'];
        $update = $conn->prepare("UPDATE users SET phone = ? WHERE id = ?");
        $update->bind_param("si", $new_phone, $user_id);
        if ($update->execute()) { $success_popup = true; }
        else { $error_message = "Gagal memperbarui nomor HP."; }
    } 
    elseif ($action === 'change_password') {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass !== $confirm_pass) {
            $error_message = "Konfirmasi password baru tidak cocok.";
        } elseif (password_verify($current_pass, $user['password'])) {
            if (strlen($new_pass) < 6) {
                $error_message = "Password baru minimal 6 karakter.";
            } else {
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update->bind_param("si", $new_hash, $user_id);
                if ($update->execute()) { 
                    $success_popup = true; 
                    // Store new password in session temporarily to display it
                    $_SESSION['temp_new_password'] = $new_pass;
                    $visible_password = $new_pass;
                }
            }
        } else { $error_message = "Password saat ini salah."; }
    }
    elseif ($action === 'update_profile_pic') {
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            $targetDir = "../assets/img/profiles/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            
            $fileExtension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $fileName = "user_" . $user_id . "_" . time() . "." . $fileExtension;
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
                $picPath = "assets/img/profiles/" . $fileName;
                
                // Self-healing: Check if column exists, if not, add it
                $colCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_pic'");
                if ($colCheck && $colCheck->num_rows == 0) {
                    $conn->query("ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL");
                }

                $update = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                $update->bind_param("si", $picPath, $user_id);
                if ($update->execute()) { 
                    $success_popup = true; 
                }
            } else {
                $error_message = "Gagal mengunggah gambar.";
            }
        }
    }
    elseif ($action === 'delete_account') {
        $conn->query("DELETE FROM reservations WHERE user_id = $user_id");
        $conn->query("DELETE FROM feedback WHERE user_id = $user_id");
        $delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete->bind_param("i", $user_id);
        if ($delete->execute()) {
            session_destroy();
            header("Location: ../index.php?deleted=true");
            exit();
        }
    }

    if ($success_popup) {
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Profil - Neydream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?php include 'includes/loading_styles.php'; ?>
    <style>
        /* ANTIGRAVITY AD PROTECTION */
        #sb98124, #sb98124_image, #sb98124_close, .tutup2,
        div[id^="sb"][style*="display: block"], 
        div[id^="sb"][style*="position: fixed"],
        a[href*="infinityfree"] {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            z-index: -99999 !important;
        }
    </style>
    <script>
        (function(){
            setInterval(function(){
                var ads = document.querySelectorAll('#sb98124, #sb98124_image, .tutup2, div[id^="sb"][style*="fixed"]');
                ads.forEach(function(el){ el.remove(); });
            }, 500);
        })();
    </script>
    <style>
        :root {
            --primary: #ea3671;
            --primary-light: #ffd9e2;
            --secondary: #5f162e;
            --bg-gradient: linear-gradient(135deg, #ffd9e2 0%, #ffe6f0 100%);
            --white: #ffffff;
            --text-main: #333;
            --text-muted: #888;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center; justify-content: center;
            padding: 20px;
            color: var(--text-main);
        }

        .profile-container {
            background: var(--white);
            width: 100%; max-width: 550px;
            border-radius: 40px; padding: 40px;
            box-shadow: 0 20px 60px rgba(234, 54, 113, 0.15);
            position: relative; overflow: hidden;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .header-section { text-align: center; margin-bottom: 30px; }
        .avatar-large {
            width: 100px; height: 100px; background: var(--primary); color: white;
            border-radius: 35px; display: flex; align-items: center; justify-content: center;
            font-size: 3rem; font-weight: 800; margin: 0 auto 15px;
            box-shadow: 0 10px 25px rgba(234, 54, 113, 0.3); transform: rotate(-5deg);
            position: relative; overflow: hidden;
        }
        .avatar-large img { width: 100%; height: 100%; object-fit: cover; }
        .edit-avatar-overlay {
            position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0,0,0,0.5);
            color: white; font-size: 0.7rem; padding: 4px 0; cursor: pointer; opacity: 0; transition: 0.3s;
        }
        .avatar-large:hover .edit-avatar-overlay { opacity: 1; }
        .header-section h2 { font-size: 1.8rem; font-weight: 800; color: var(--secondary); }
        .header-section p { color: var(--text-muted); font-size: 0.9rem; }

        .stats-row {
            background: #fffafa; border: 2px solid #fff0f3;
            border-radius: 25px; padding: 20px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-item { text-align: center; flex: 1; }
        .stat-item .value { font-size: 1.5rem; font-weight: 800; color: var(--primary); display: block; }
        .stat-item .label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }

        .detail-card {
            background: #fdfdfd; border: 1px solid #f0f0f0;
            border-radius: 20px; padding: 20px; margin-bottom: 25px;
        }
        .detail-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 0; border-bottom: 1px solid #f5f5f5;
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }
        .detail-value { font-size: 0.95rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
        .edit-icon { cursor: pointer; color: var(--primary); font-size: 1.1rem; transition: 0.2s; }
        .edit-icon:hover { transform: scale(1.2); }

        .btn-update {
            width: 100%; padding: 16px; border-radius: 20px; border: none;
            background: var(--primary); color: white;
            font-weight: 700; font-size: 1rem; cursor: pointer;
            box-shadow: 0 10px 20px rgba(234, 54, 113, 0.2);
            transition: 0.3s; margin-top: 10px;
        }
        .btn-update:hover { background: #d4205c; transform: translateY(-2px); }

        .btn-danger-lite {
            display: block; width: fit-content; margin: 30px auto 0;
            color: #ff5c8a; font-size: 0.85rem; font-weight: 600;
            background: none; border: none; cursor: pointer; text-decoration: underline;
        }

        .btn-back { display: block; text-align: center; margin-top: 20px; color: var(--text-muted); text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .btn-back:hover { color: var(--primary); }

        /* Modal Fancy Styling */
        .modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);
            z-index: 9999; align-items: center; justify-content: center;
        }
        .modal-content {
            background: white; padding: 40px; border-radius: 30px; width: 90%; max-width: 400px; text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2); animation: modalIn 0.3s ease;
        }
        @keyframes modalIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .modal h3 { margin-bottom: 15px; color: var(--secondary); font-weight: 800; }
        .modal p { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 25px; line-height: 1.6; }
        
        input.modal-input {
            width: 100%; padding: 14px 20px; border: 2px solid #fff0f3;
            border-radius: 15px; font-family: inherit; font-size: 1rem;
            margin-bottom: 20px; outline: none; transition: 0.3s;
        }
        input.modal-input:focus { border-color: var(--primary); background: #fff9fa; }

        .modal-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .btn-modal { padding: 14px; border-radius: 15px; border: none; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: 0.2s; }
        .btn-cancel { background: #f5f5f5; color: #888; }
        .btn-confirm { background: var(--primary); color: white; }

        /* Password Wrapper Refined */
        .pass-view-box { 
            position: relative;
            display: flex; width: 100%; align-items: center; justify-content: center;
            background: #fffafa; padding: 12px 15px; 
            border-radius: 12px; border: 1px solid #fff0f3; 
            margin-top: 10px; color: #555; font-family: monospace; letter-spacing: 2px;
            text-align: center; font-weight: 600;
        }
        .eye-icon { position: absolute; right: 15px; cursor: pointer; font-size: 1.1rem; opacity: 0.6; }
        .eye-icon:hover { opacity: 1; color: var(--primary); }

        #successModal .modal-content { border-top: 8px solid #10b981; }
        #successModal h3 { color: #065f46; }
        #deleteModal .modal-content { border-top: 8px solid #ef4444; }
        #deleteModal .modal-icon { font-size: 3.5rem; margin-bottom: 15px; display: block; }
        .btn-delete-final { background: #ef4444; color: white; }
        
        .btn-delete-icon {
            position: absolute;
            top: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff0f3;
            border: 1px solid #ffdeeb;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            color: #ef4444; 
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.1);
        }
        .btn-delete-icon:hover { 
            transform: rotate(15deg) scale(1.1); 
            background: #fee2e2;
            box-shadow: 0 6px 15px rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <?php include '../includes/loading.php'; ?>
    <div class="profile-container">
        <button onclick="openModal('deleteModal')" class="btn-delete-icon" title="Hapus Akun">🗑️</button>
        <div class="header-section">
            <div class="avatar-large" onclick="document.getElementById('picInput').click()">
                <?php if(!empty($user['profile_pic'])): ?>
                    <img src="../<?= $user['profile_pic'] ?>" alt="Profile">
                <?php else: ?>
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                <?php endif; ?>
                <div class="edit-avatar-overlay">Ganti</div>
            </div>
            <form id="picForm" method="POST" enctype="multipart/form-data" style="display:none;">
                <input type="hidden" name="action" value="update_profile_pic">
                <input type="file" id="picInput" name="profile_pic" onchange="document.getElementById('picForm').submit()" accept="image/*">
            </form>
            <h2><?= htmlspecialchars($user['username']) ?></h2>
            <p>Customer Premium Neydream</p>
        </div>

        <div class="stats-row">
            <div class="stat-item">
                <span class="value"><?= str_repeat('⭐', $user['loyalty_level'] ?? 1) ?></span>
                <span class="label">Loyalty level</span>
            </div>
            <div style="width: 1px; height: 30px; background: #f0f0f0;"></div>
            <div class="stat-item">
                <span class="value"><?= $total_reservations ?></span>
                <span class="label">Total Reservasi</span>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-item">
                <span class="detail-label">Email</span>
                <div class="detail-value">
                    <span><?= htmlspecialchars($user['email']) ?></span>
                    <span class="edit-icon" onclick="openModal('emailModal')">✏️</span>
                </div>
            </div>
            <div class="detail-item">
                <span class="detail-label">Nomor HP</span>
                <div class="detail-value">
                    <span><?= htmlspecialchars($user['phone']) ?></span>
                    <span class="edit-icon" onclick="openModal('phoneModal')">✏️</span>
                </div>
            </div>
            
            <div class="detail-item" style="flex-direction: column; align-items: flex-start; gap: 8px; border-bottom: none; padding-bottom: 0;">
                <span class="detail-label" style="width: 100%;">Password Keamanan</span>
                <div class="pass-view-box">
                    <span id="passText">•••••••••••••</span>
                    <span class="eye-icon" onclick="togglePass()">👁️</span>
                </div>
                <!-- Logic Note: If user just changed password, we have it in PHP var $visible_password -->
                <input type="hidden" id="realPass" value="<?= htmlspecialchars($visible_password) ?>">
            </div>
        </div>

        <button onclick="openModal('passModal')" class="btn-update">Ganti Password</button>

        <a href="../index.php" class="btn-back">← Kembali ke Beranda</a>
    </div>

    <!-- Modals (Email/Phone same as before) -->
    
    <div id="emailModal" class="modal">
        <div class="modal-content">
            <h3>Update Email</h3>
            <p>Masukkan alamat email baru Kamu.</p>
            <form method="POST">
                <input type="hidden" name="action" value="update_email">
                <input type="email" name="new_email" class="modal-input" required value="<?= $user['email'] ?>">
                <div class="modal-btns">
                    <button type="button" onclick="closeModal('emailModal')" class="btn-modal btn-cancel">Batal</button>
                    <button type="submit" class="btn-modal btn-confirm">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <div id="phoneModal" class="modal">
        <div class="modal-content">
            <h3>Update Nomor HP</h3>
            <p>Masukkan nomor WhatsApp baru Kamu.</p>
            <form method="POST">
                <input type="hidden" name="action" value="update_phone">
                <input type="tel" name="new_phone" class="modal-input" required value="<?= $user['phone'] ?>">
                <div class="modal-btns">
                    <button type="button" onclick="closeModal('phoneModal')" class="btn-modal btn-cancel">Batal</button>
                    <button type="submit" class="btn-modal btn-confirm">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <div id="passModal" class="modal">
        <div class="modal-content">
            <h3>Ganti Password</h3>
            <p>Masukkan password baru dan konfirmasi ulang.</p>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <input type="password" name="current_password" class="modal-input" placeholder="Password Lama" required>
                <input type="password" name="new_password" class="modal-input" placeholder="Password Baru (min 6 char)" required>
                <input type="password" name="confirm_password" class="modal-input" placeholder="Konfirmasi Password Baru" required>
                <div class="modal-btns">
                    <button type="button" onclick="closeModal('passModal')" class="btn-modal btn-cancel">Batal</button>
                    <button type="submit" class="btn-modal btn-confirm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="modal-icon">❗️</span>
            <h3 style="color: #ef4444;">WARNING KERAS!</h3>
            <p>Yakin ingin menghapus akun? <strong>Semua data akan hangus selamanya!</strong></p>
            <form method="POST">
                <input type="hidden" name="action" value="delete_account">
                <div class="modal-btns">
                    <button type="button" onclick="closeModal('deleteModal')" class="btn-modal btn-cancel">Batal</button>
                    <button type="submit" class="btn-modal btn-delete-final">YA, HAPUS</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Warning Modal -->
    <div id="passWarningModal" class="modal">
        <div class="modal-content">
            <span style="font-size: 3rem; margin-bottom: 15px; display: block;">🔒</span>
            <h3>Password Aman</h3>
            <p>Password lama Kakak sudah terenkripsi demi keamanan dan tidak bisa dilihat langsung.<br><br>Mau **GANTI PASSWORD** baru agar bisa dilihat 'Terang-terangan'?</p>
            <div class="modal-btns">
                <button type="button" onclick="closeModal('passWarningModal')" class="btn-modal btn-cancel">Batal</button>
                <button type="button" onclick="closeModal('passWarningModal'); openModal('passModal');" class="btn-modal btn-confirm">Ya, Ganti Baru</button>
            </div>
        </div>
    </div>

    <div id="successModal" class="modal" style="<?= $success_popup ? 'display:flex;' : '' ?>">
        <div class="modal-content">
            <div style="font-size: 3rem; margin-bottom: 20px;">✅</div>
            <h3>Berhasil Diperbarui!</h3>
            <p>Data profil Kakak sudah kami update.</p>
            <button onclick="closeModal('successModal')" class="btn-modal btn-confirm" style="width:100%;">Oke</button>
        </div>
    </div>

    <?php if ($error_message): ?>
        <script>alert("<?= $error_message ?>");</script>
    <?php endif; ?>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        
        function togglePass() {
            let textField = document.getElementById('passText');
            let realPass = document.getElementById('realPass').value;
            
            if (realPass === '•••••••••••••') {
                openModal('passWarningModal');
            } else {
                if (textField.innerText === '•••••••••••••') {
                    textField.innerText = realPass;
                    document.querySelector('.eye-icon').innerText = '🙈';
                } else {
                    textField.innerText = '•••••••••••••';
                    document.querySelector('.eye-icon').innerText = '👁️';
                }
            }
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
