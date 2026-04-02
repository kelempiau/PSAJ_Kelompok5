<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$msg = "";
$error = "";



try {
    $colCheck = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'theme_config'");
    if ($colCheck && $colCheck->num_rows == 0) {
        $conn->query("ALTER TABLE studio_settings ADD COLUMN theme_config TEXT DEFAULT NULL");
    }
    
    
    $rowCheck = $conn->query("SELECT id FROM studio_settings WHERE id = 1");
    if ($rowCheck && $rowCheck->num_rows == 0) {
        $conn->query("INSERT INTO studio_settings (id, province, city, full_address) VALUES (1, 'Jawa Tengah', 'Purwokerto', 'Dusun Sokawera, Rempoah, Baturraden')");
    }
} catch (Exception $e) {
    
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    
    if ($action === 'update_location') {
        $province = $_POST['province'];
        $city = $_POST['city'];
        $address = $_POST['full_address'];
        
        $stmt = $conn->prepare("UPDATE studio_settings SET province = ?, city = ?, full_address = ? WHERE id = 1");
        $stmt->bind_param("sss", $province, $city, $address);
        if ($stmt->execute()) $msg = "Lokasi studio berhasil diperbarui.";
        else $error = "Gagal memperbarui lokasi.";
    }

    
    if ($action === 'update_general') {
        $whatsapp = $_POST['whatsapp'];
        $instagram = $_POST['instagram'];
        $min_dp_percent = intval($_POST['min_dp_percent']);
        $status = intval($_POST['studio_status']);
        $studio_name = $_POST['studio_name'];

        
        $email = $_POST['email'] ?? '';

        $cols = [
            'whatsapp' => "VARCHAR(20) DEFAULT NULL",
            'instagram' => "VARCHAR(50) DEFAULT NULL",
            'email' => "VARCHAR(100) DEFAULT NULL",
            'min_dp_percent' => "INT DEFAULT 0",
            'studio_status' => "TINYINT(1) DEFAULT 1",
            'studio_name' => "VARCHAR(100) DEFAULT 'Ney Dream'"
        ];
        foreach($cols as $col => $def) {
            $check = $conn->query("SHOW COLUMNS FROM studio_settings LIKE '$col'");
            if($check && $check->num_rows == 0) {
                $conn->query("ALTER TABLE studio_settings ADD COLUMN $col $def");
            }
        }

        $stmt = $conn->prepare("UPDATE studio_settings SET whatsapp = ?, instagram = ?, email = ?, min_dp_percent = ?, studio_status = ?, studio_name = ? WHERE id = 1");
        $stmt->bind_param("sssiis", $whatsapp, $instagram, $email, $min_dp_percent, $status, $studio_name);
        if ($stmt->execute()) $msg = "Pengaturan berhasil diperbarui.";
        else $error = "Gagal memperbarui pengaturan.";
    }

    
    if ($action === 'update_branding') {
        $name = $_POST['studio_name'];
        $logo = $_POST['existing_logo'] ?? '';

        
        if (isset($_FILES['studio_logo']) && $_FILES['studio_logo']['error'] === 0) {
            $targetDir = "../assets/img/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = "logo_" . time() . "_" . $_FILES['studio_logo']['name'];
            if (move_uploaded_file($_FILES['studio_logo']['tmp_name'], $targetDir . $fileName)) {
                $logo = "assets/img/" . $fileName;
            }
        }

        
        $cols = [
            'studio_name' => "VARCHAR(100) DEFAULT 'Ney Dream'",
            'studio_logo' => "VARCHAR(255) DEFAULT NULL"
        ];
        foreach($cols as $col => $def) {
            $check = $conn->query("SHOW COLUMNS FROM studio_settings LIKE '$col'");
            if($check && $check->num_rows == 0) {
                $conn->query("ALTER TABLE studio_settings ADD COLUMN $col $def");
            }
        }

        $stmt = $conn->prepare("UPDATE studio_settings SET studio_name = ?, studio_logo = ? WHERE id = 1");
        $stmt->bind_param("ss", $name, $logo);
        if ($stmt->execute()) $msg = "Branding berhasil diperbarui.";
        else $error = "Gagal memperbarui branding.";
    }

    
    if ($action === 'update_admin_pic') {
        if (isset($_FILES['admin_profile_pic']) && $_FILES['admin_profile_pic']['error'] === 0) {
            $targetDir = "../assets/img/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = "admin_profile_" . time() . "_" . $_FILES['admin_profile_pic']['name'];
            if (move_uploaded_file($_FILES['admin_profile_pic']['tmp_name'], $targetDir . $fileName)) {
                $picPath = "assets/img/" . $fileName;
                
                
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

    
    if ($action === 'save_payment') {
        $id = intval($_POST['method_id'] ?? 0);
        $name = $_POST['method_name'];
        $acc_name = $_POST['account_name'] ?? '';
        $acc_num = $_POST['account_number'] ?? '';
        $type = $_POST['type'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $qr_image = $_POST['existing_qr'] ?? '';
        
        
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

    
    if ($action === 'delete_payment') {
        $id = intval($_POST['method_id']);
        if ($conn->query("DELETE FROM payment_methods WHERE id = $id")) $msg = "Metode pembayaran dihapus.";
    }

    
    if ($action === 'add_slot') {
        $time = $_POST['slot_time']; 
        if (!empty($time)) {
            
            $conn->query("CREATE TABLE IF NOT EXISTS reservation_slots (
                id INT AUTO_INCREMENT PRIMARY KEY,
                slot_time TIME NOT NULL,
                is_active TINYINT(1) DEFAULT 1
            )");
            
            $check = $conn->prepare("SELECT id FROM reservation_slots WHERE slot_time = ?");
            $check->bind_param("s", $time);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = "Jam reservasi $time sudah ada.";
            } else {
                $stmt = $conn->prepare("INSERT INTO reservation_slots (slot_time) VALUES (?)");
                $stmt->bind_param("s", $time);
                if ($stmt->execute()) $msg = "Jam reservasi berhasil ditambahkan.";
                else $error = "Gagal menambahkan jam.";
            }
        }
    }

    if ($action === 'delete_slot') {
        $id = intval($_POST['slot_id']);
        if ($conn->query("DELETE FROM reservation_slots WHERE id = $id")) $msg = "Jam reservasi berhasil dihapus.";
    }

    
    if ($action === 'update_theme') {
        $mode = $_POST['theme_mode'] ?? 'light';
        $preset = $_POST['theme_preset'] ?? 'blue';
        $colors = [
            'primary' => $_POST['color_primary'] ?? '#2563eb',
            'bg_main' => $_POST['color_bg_main'] ?? '#f8fafc',
            'bg_card' => $_POST['color_bg_card'] ?? '#ffffff'
        ];
        
        $themeConfig = json_encode([
            'mode' => $mode,
            'preset' => $preset,
            'colors' => $colors
        ]);
        
        
        $colCheck = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'theme_config'");
        if ($colCheck && $colCheck->num_rows == 0) {
            $conn->query("ALTER TABLE studio_settings ADD COLUMN theme_config TEXT DEFAULT NULL");
        }

        $stmt = $conn->prepare("UPDATE studio_settings SET theme_config = ? WHERE id = 1");
        $stmt->bind_param("s", $themeConfig);
        if ($stmt->execute()) {
            $msg = "Tema aplikasi berhasil diperbarui.";
            
        } else {
            $error = "Gagal memperbarui tema.";
        }
    }
}


$settings = ['province'=>'', 'city'=>'', 'full_address'=>'', 'admin_profile_pic'=>''];
$colCheck = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'admin_profile_pic'");
if ($colCheck && $colCheck->num_rows > 0) {
    $settingsRes = $conn->query("SELECT * FROM studio_settings WHERE id = 1");
    if ($settingsRes) $settings = $settingsRes->fetch_assoc() ?: $settings;
} else {
    $settingsRes = $conn->query("SELECT province, city, full_address FROM studio_settings WHERE id = 1");
    if ($settingsRes) $settings = array_merge($settings, $settingsRes->fetch_assoc() ?: []);
}


$themeObj = json_decode($settings['theme_config'] ?? '{}', true);
$currentMode = $themeObj['mode'] ?? 'light';
$currentPreset = $themeObj['preset'] ?? 'blue';
$currentColors = $themeObj['colors'] ?? [
    'primary' => '#2563eb',
    'bg_main' => '#f8fafc',
    'bg_card' => '#ffffff'
];

$payments = $conn->query("SELECT * FROM payment_methods ORDER BY id ASC");


$slots = [];
$slotCheck = $conn->query("SHOW TABLES LIKE 'reservation_slots'");
if ($slotCheck && $slotCheck->num_rows > 0) {
    $slotRes = $conn->query("SELECT * FROM reservation_slots ORDER BY slot_time ASC");
    while($s = $slotRes->fetch_assoc()) $slots[] = $s;
} else {
    
    $conn->query("CREATE TABLE IF NOT EXISTS reservation_slots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slot_time TIME NOT NULL,
        is_active TINYINT(1) DEFAULT 1
    )");
    
    
    $defaults = ['09:00:00', '11:00:00', '13:00:00', '15:00:00', '17:00:00', '19:00:00'];
    foreach($defaults as $t) {
        $conn->query("INSERT INTO reservation_slots (slot_time) VALUES ('$t')");
    }
    
    
    $slotRes = $conn->query("SELECT * FROM reservation_slots ORDER BY slot_time ASC");
    while($s = $slotRes->fetch_assoc()) $slots[] = $s;
}
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
            grid-template-columns: 1.2fr 1.5fr; 
            gap: 24px; 
        }

        @media (max-width: 1024px) {
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
        
        input[type="text"], input[type="number"], input[type="time"], textarea, select {
            background: var(--bg-main);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            padding: 10px;
            border-radius: 8px;
            width: 100%;
        }
        
        /* Explicit Dark Mode Overrides */
        body.dark-mode input[type="text"], 
        body.dark-mode input[type="number"], 
        body.dark-mode input[type="time"], 
        body.dark-mode textarea, 
        body.dark-mode select {
            background-color: #1e293b; 
            color: #f8fafc;
            border-color: #334155;
        }

        input[type="text"]:focus, input[type="time"]:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        
        .admin-avatar-preview {
            width: 70px; height: 70px; border-radius: 18px; margin: 0 auto 18px;
            overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            background: var(--bg-card); border: 3px solid var(--bg-card);
        }
        .admin-avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
        
        input[type="file"].hidden-input { 
            position: absolute; width: 100%; height: 100%; top: 0; left: 0; 
            opacity: 0; cursor: pointer; 
        }

        /* Responsive Mobile Tweak */
        @media (max-width: 600px) {
            .admin-avatar-preview { width: 60px; height: 60px; }
            .card-header h3 { font-size: 16px; }
            .card { border-radius: 12px; }
            .page-header-context { padding: 16px; }
            .context-info .title { font-size: 1.2rem; }
        }

        /* Theme Picker Styles */
        .theme-option {
            border: 2px solid transparent;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            overflow: hidden;
            position: relative;
        }
        .theme-option:hover { transform: translateY(-3px); }
        .theme-option.active { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }

        .palette-preview {
            height: 60px;
            width: 100%;
            display: flex;
        }
        .palette-left { width: 50%; height: 100%; }
        .palette-right { width: 50%; height: 100%; display: flex; flex-direction: column; }
        .palette-right-top { height: 50%; width: 100%; }
        .palette-right-bottom { height: 50%; width: 100%; }

        .color-picker-group label {
            font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; display: block;
        }
        .color-input-wrapper {
            display: flex; align-items: center; gap: 10px; 
            padding: 8px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-main);
        }
        .color-input-wrapper input[type="color"] {
            border: none; width: 32px; height: 32px; padding: 0; background: none; cursor: pointer;
        }
        .color-input-wrapper input[type="text"] {
            border: none; background: none; padding: 0; font-family: monospace; font-size: 13px;
        }

        /* Mobile Responsive Grids */
        @media (max-width: 992px) {
            .main-wrapper { 
                margin-left: 0 !important; 
                width: 100% !important;
                padding: 0 !important;
                overflow-x: hidden !important;
                display: block !important;
            }
            .admin-container { 
                width: 100% !important; 
                overflow-x: hidden !important;
                display: block !important;
            }
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
                position: relative;
            }
            .page-header-context {
                margin: 15px !important;
                padding: 20px !important;
                width: calc(100% - 30px) !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
            .settings-grid {
                display: flex !important;
                flex-direction: column !important;
                padding: 0 15px !important;
                gap: 20px !important;
                width: 100% !important;
            }
            .settings-grid > div {
                width: 100% !important;
            }
            .card {
                margin-bottom: 20px !important;
                width: 100% !important;
            }
        }

        @media (max-width: 500px) {
            .theme-presets-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .color-custom-grid {
                grid-template-columns: 1fr !important;
            }
            .general-settings-grid {
                grid-template-columns: 1fr !important;
            }
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
                    <script>window.onload = () => showGlobalAlert("Berhasil!", "<?= $msg ?>", "success");</script>
                <?php endif; ?>
                <?php if($error): ?>
                    <script>window.onload = () => showGlobalAlert("Gagal!", "<?= $error ?>", "error");</script>
                <?php endif; ?>

                <div class="settings-grid">
                    
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        
                        
                        <div class="card">
                            <div class="card-header"><h3>🎨 Tampilan Aplikasi</h3></div>
                            <div style="padding: 24px;">
                                <form id="themeForm" method="POST">
                                    <input type="hidden" name="action" value="update_theme">
                                    
                                    
                                    <div style="background: var(--bg-main); padding: 4px; border-radius: 12px; display: flex; gap: 4px; margin-bottom: 24px; border: 1px solid var(--border-color);">
                                        <button type="button" class="tab-btn <?= $currentMode === 'light' ? 'bg-white shadow-sm' : '' ?>" style="flex: 1; border-radius: 8px; font-size: 13px; padding: 10px;" onclick="setThemeMode('light', this)">☀️ Light</button>
                                        <button type="button" class="tab-btn <?= $currentMode === 'dark' ? 'bg-white shadow-sm' : '' ?>" style="flex: 1; border-radius: 8px; font-size: 13px; padding: 10px;" onclick="setThemeMode('dark', this)">🌙 Dark</button>
                                    </div>
                                    <input type="hidden" name="theme_mode" id="inputMode" value="<?= $currentMode ?>">
                                    <input type="hidden" name="theme_preset" id="inputPreset" value="<?= $currentPreset ?>">

                                    
                                    <label style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; display: block;">Tema Warna</label>
                                    <div class="theme-presets-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px;">
                                        
                                        <div class="theme-option <?= $currentPreset === 'blue' ? 'active' : '' ?>" onclick="selectPreset('blue', '#2563eb', '#f8fafc', '#ffffff')">
                                            <div class="palette-preview">
                                                <div class="palette-left" style="background: #2563eb;"></div>
                                                <div class="palette-right">
                                                    <div class="palette-right-top" style="background: #ffffff;"></div>
                                                    <div class="palette-right-bottom" style="background: #f8fafc;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="theme-option <?= $currentPreset === 'emerald' ? 'active' : '' ?>" onclick="selectPreset('emerald', '#10b981', '#f0fdf4', '#ffffff')">
                                            <div class="palette-preview">
                                                <div class="palette-left" style="background: #10b981;"></div>
                                                <div class="palette-right">
                                                    <div class="palette-right-top" style="background: #ffffff;"></div>
                                                    <div class="palette-right-bottom" style="background: #f0fdf4;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="theme-option <?= $currentPreset === 'violet' ? 'active' : '' ?>" onclick="selectPreset('violet', '#8b5cf6', '#f5f3ff', '#ffffff')">
                                            <div class="palette-preview">
                                                <div class="palette-left" style="background: #8b5cf6;"></div>
                                                <div class="palette-right">
                                                    <div class="palette-right-top" style="background: #ffffff;"></div>
                                                    <div class="palette-right-bottom" style="background: #f5f3ff;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="theme-option <?= $currentPreset === 'orange' ? 'active' : '' ?>" onclick="selectPreset('orange', '#f97316', '#fff7ed', '#ffffff')">
                                            <div class="palette-preview">
                                                <div class="palette-left" style="background: #f97316;"></div>
                                                <div class="palette-right">
                                                    <div class="palette-right-top" style="background: #ffffff;"></div>
                                                    <div class="palette-right-bottom" style="background: #fff7ed;"></div>
                                                </div>
                                            </div>
                                        </div>
                                         
                                         <div class="theme-option <?= $currentPreset === 'rose' ? 'active' : '' ?>" onclick="selectPreset('rose', '#e11d48', '#fff1f2', '#ffffff')">
                                            <div class="palette-preview">
                                                <div class="palette-left" style="background: #e11d48;"></div>
                                                <div class="palette-right">
                                                    <div class="palette-right-top" style="background: #ffffff;"></div>
                                                    <div class="palette-right-bottom" style="background: #fff1f2;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="theme-option <?= $currentPreset === 'sweet_pink' ? 'active' : '' ?>" onclick="selectPreset('sweet_pink', '#ec4899', '#fff1f2', '#ffffff')">
                                            <div class="palette-preview">
                                                <div class="palette-left" style="background: #ec4899;"></div>
                                                <div class="palette-right">
                                                    <div class="palette-right-top" style="background: #ffffff;"></div>
                                                    <div class="palette-right-bottom" style="background: #fff1f2;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div style="background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                            <label style="font-weight: 700; font-size: 13px;">Kustomisasi Warna</label>
                                            <span style="font-size: 11px; color: var(--text-muted);">Ubah sesuka hati 👇</span>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                                            <div class="color-picker-group">
                                                <label>Warna Utama (Primary)</label>
                                                <div class="color-input-wrapper">
                                                    <input type="color" name="color_primary" value="<?= $currentColors['primary'] ?>" oninput="updateLivePreview('primary', this.value)">
                                                    <input type="text" value="<?= $currentColors['primary'] ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="color-custom-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                                <div class="color-picker-group">
                                                    <label>Warna Latar (Body)</label>
                                                    <div class="color-input-wrapper">
                                                        <input type="color" name="color_bg_main" value="<?= $currentColors['bg_main'] ?>" oninput="updateLivePreview('bg_main', this.value)">
                                                        <input type="text" value="<?= $currentColors['bg_main'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="color-picker-group">
                                                    <label>Warna Kartu (Card)</label>
                                                    <div class="color-input-wrapper">
                                                        <input type="color" name="color_bg_card" value="<?= $currentColors['bg_card'] ?>" oninput="updateLivePreview('bg_card', this.value)">
                                                        <input type="text" value="<?= $currentColors['bg_card'] ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 24px;">Simpan Tampilan</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header"><h3>👩‍💼 Profil Chat (Assistant)</h3></div>
                            <div style="padding: 24px;">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_admin_pic">
                                    
                                    <div style="text-align: center; margin-bottom: 20px;">
                                        <div class="admin-avatar-preview">
                                            <?php if(!empty($settings['admin_profile_pic'])): ?>
                                                <img src="../<?= $settings['admin_profile_pic'] ?>" alt="Profile">
                                            <?php else: ?>
                                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 30px; background: var(--bg-main);">👩‍💼</div>
                                            <?php endif; ?>
                                        </div>
                                        <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 15px;">Pesan dari Admin/Bot akan menggunakan foto ini.</p>
                                        
                                        <div class="upload-area" onclick="document.getElementById('admin_pic_input').click()" style="width: 100%;">
                                            <span class="upload-icon">📸</span>
                                            <span class="upload-text">Ganti Foto Profil Chat</span>
                                            <input type="file" name="admin_profile_pic" id="admin_pic_input" class="hidden-input" accept="image/*" onchange="this.form.submit()">
                                        </div>
                                    </div>
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

                    
                    <div style="display: flex; flex-direction: column; gap: 24px;">
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
                                    
                                    
                                    <form id="delete-payment-<?= $p['id'] ?>" method="POST" style="display:none;">
                                        <input type="hidden" name="action" value="delete_payment">
                                        <input type="hidden" name="method_id" value="<?= $p['id'] ?>">
                                    </form>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    
                    <div class="card">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h3>⏰ Jam Reservasi</h3>
                            <button onclick="document.getElementById('slotModal').style.display='flex'" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">+ Tambah</button>
                        </div>
                        <div style="padding: 24px;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px;">
                                <?php foreach($slots as $s): 
                                    $timeObj = new DateTime($s['slot_time']);
                                    $timeDisplay = $timeObj->format('H:i') . " WIB";
                                ?>
                                <div class="card-slot" style="border: 1px solid var(--border-color); border-radius: 12px; padding: 15px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <div style="font-weight: 800; color: var(--text-primary); font-size: 1.3rem; margin-bottom: 4px;"><?= $timeDisplay ?></div>
                                    <button onclick="confirmDeleteSlot(<?= $s['id'] ?>, '<?= $timeDisplay ?>')" 
                                            onmouseover="this.style.background='#dc2626'; this.style.transform='scale(1.05)'" 
                                            onmouseout="this.style.background='#ef4444'; this.style.transform='scale(1)'"
                                            style="background: #ef4444; border: none; color: white; font-size: 0.85rem; padding: 8px 20px; border-radius: 20px; cursor: pointer; font-weight: 600; transition: all 0.2s; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);">Hapus</button>
                                    
                                    <form id="delete-slot-<?= $s['id'] ?>" method="POST" style="display:none;">
                                        <input type="hidden" name="action" value="delete_slot">
                                        <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card">
                        <div class="card-header"><h3>🔗 Pengaturan Studio & Kebijakan</h3></div>
                        <div style="padding: 24px;">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_general">
                                
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label>Nama Studio</label>
                                    <input type="text" name="studio_name" value="<?= htmlspecialchars($settings['studio_name'] ?? 'Ney Dream') ?>" required>
                                </div>

                                <div class="general-settings-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 12px;">
                                    <div class="form-group">
                                        <label>WhatsApp (62xxx)</label>
                                        <input type="text" name="whatsapp" value="<?= htmlspecialchars($settings['whatsapp'] ?? '') ?>" placeholder="62812345678" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Instagram User</label>
                                        <input type="text" name="instagram" value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>" placeholder="@username" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Studio</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" placeholder="email@studio.com" required style="width: 100%; border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; background: var(--bg-main); color: var(--text-primary);">
                                    </div>
                                </div>
                                <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 20px; line-height: 1.4;">
                                    * WhatsApp dan Instagram akan muncul sebagai link bantuan/kontak resmi di halaman pelanggan agar mereka mudah bertanya.
                                </p>

                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label>Minimal DP (%) <span style="font-size: 11px; color: var(--text-muted);">Contoh: 50 untuk DP 50%</span></label>
                                    <div style="position: relative;">
                                        <input type="number" name="min_dp_percent" value="<?= intval($settings['min_dp_percent'] ?? 0) ?>" style="width: 100%; padding-right: 40px;" required>
                                        <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--text-muted);">%</span>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 24px;">
                                    <label>Status Studio</label>
                                    <select name="studio_status" style="width: 100%;">
                                        <option value="1" <?= ($settings['studio_status'] ?? 1) == 1 ? 'selected' : '' ?>>🟢 Buka (Menerima Reservasi)</option>
                                        <option value="0" <?= ($settings['studio_status'] ?? 1) == 0 ? 'selected' : '' ?>>🔴 Tutup / Libur (Booking Dimatikan)</option>
                                    </select>
                                    <p style="font-size: 11px; color: var(--text-muted); mt: 8px;">
                                        * Jika tutup, tombol reservasi di website akan otomatis memunculkan pesan pengumuman libur.
                                    </p>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Simpan Pengaturan</button>
                            </form>
                        </div>
                    </div>
                </div> 
            </div> 
        </div> 
    </div>  
</div> 

    
    <div id="paymentModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="card" style="width: 450px; padding: 32px; max-height: 90vh; overflow-y: auto; background: var(--bg-card); color: var(--text-primary);">
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

    
    <div id="slotModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10001; align-items: center; justify-content: center;">
        <div class="card" style="width: 350px; padding: 32px; background: var(--bg-card); border-radius: 16px; color: var(--text-primary);">
            <h3 style="margin-bottom: 24px; text-align: center;">Tambah Jam Reservasi</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_slot">
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Pilih Jam</label>
                    <input type="time" name="slot_time" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button type="button" onclick="document.getElementById('slotModal').style.display='none'" class="btn btn-outline" style="justify-content: center;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="justify-content: center;">Tambah</button>
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

        function confirmDeleteSlot(id, time) {
            showGlobalConfirm(
                "Hapus Jam Reservasi", 
                `Apakah Kakak yakin ingin menghapus jam "${time}"?`, 
                "danger", 
                () => { document.getElementById('delete-slot-' + id).submit(); }
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
        function showGlobalConfirm(title, message, type, onConfirm) {
            if (!document.getElementById('globalConfirmModal')) {
                const modal = document.createElement('div');
                modal.id = 'globalConfirmModal';
                modal.style.cssText = `display:none; position:fixed; top:0; left:0; width:100%; height:100%; bg:rgba(0,0,0,0.5); z-index:100000; align-items:center; justify-content:center; background:rgba(0,0,0,0.5);`;
                modal.innerHTML = `
                    <div style="background:var(--bg-card, white); padding:30px; border-radius:16px; width:400px; text-align:center; animation: popUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); color: var(--text-color, #333);">
                        <div style="font-size:3rem; margin-bottom:15px;">⚠️</div>
                        <h3 id="gcm-title" style="margin-bottom:10px; color:var(--text-color, #333);">Title</h3>
                        <p id="gcm-msg" style="color:var(--text-muted, #666); margin-bottom:25px; line-height:1.5;">Message</p>
                        <div style="display:flex; gap:10px; justify-content:center;">
                            <button id="gcm-cancel" class="btn btn-outline" style="flex:1;">Batal</button>
                            <button id="gcm-confirm" class="btn btn-primary" style="flex:1;">Ya, Lanjutkan</button>
                        </div>
                    </div>
                    <style>@keyframes popUp { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }</style>
                `;
                document.body.appendChild(modal);
            }
            
            
            const modal = document.getElementById('globalConfirmModal');
            document.getElementById('gcm-title').innerText = title;
            document.getElementById('gcm-msg').innerText = message;
            
            const confirmBtn = document.getElementById('gcm-confirm');
            if (type === 'danger') {
                confirmBtn.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                confirmBtn.style.color = 'white';
                confirmBtn.style.boxShadow = '0 4px 15px rgba(239, 68, 68, 0.4)';
                confirmBtn.style.border = 'none';
                confirmBtn.innerText = 'Ya, Hapus';
            } else {
                confirmBtn.style.background = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
                confirmBtn.style.color = 'white';
                confirmBtn.style.boxShadow = '0 4px 15px rgba(59, 130, 246, 0.4)';
                confirmBtn.style.border = 'none';
                confirmBtn.innerText = 'Ya, Lanjutkan';
            }
            
            confirmBtn.style.padding = '12px 24px';
            confirmBtn.style.borderRadius = '12px';
            confirmBtn.style.fontWeight = '600';
            confirmBtn.style.transition = 'transform 0.2s';
            
            confirmBtn.onmouseover = () => confirmBtn.style.transform = 'scale(1.05)';
            confirmBtn.onmouseout = () => confirmBtn.style.transform = 'scale(1)';

            confirmBtn.onclick = () => {
                onConfirm();
                modal.style.display = 'none';
            };
            
            document.getElementById('gcm-cancel').onclick = () => { modal.style.display = 'none'; };
            
            modal.style.display = 'flex';
        }

        function showGlobalAlert(title, message, type) {
            if (!document.getElementById('globalAlertModal')) {
                const modal = document.createElement('div');
                modal.id = 'globalAlertModal';
                modal.style.cssText = `display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:100000; align-items:center; justify-content:center; background:rgba(0,0,0,0.5);`;
                modal.innerHTML = `
                    <div style="background:var(--bg-card, white); padding:30px; border-radius:16px; width:400px; text-align:center; animation: popUp 0.3s ease; box-shadow: 0 20px 60px rgba(0,0,0,0.2); color: var(--text-color, #333);">
                        <div id="gam-icon" style="font-size:3rem; margin-bottom:15px;"></div>
                        <h3 id="gam-title" style="margin-bottom:10px; color:var(--text-color, #333); font-weight:700;"></h3>
                        <p id="gam-msg" style="color:var(--text-muted, #666); margin-bottom:25px; line-height:1.5;"></p>
                        <button id="gam-ok" style="background:#2563eb; color:white; border:none; padding:12px 30px; border-radius:12px; font-weight:600; cursor:pointer; width:100%; transition:0.2s; text-align:center;">OK</button>
                    </div>
                `;
                document.body.appendChild(modal);
                document.getElementById('gam-ok').onclick = () => {
                    modal.style.display = 'none';
                };
            }
            
            const modal = document.getElementById('globalAlertModal');
            const icon = document.getElementById('gam-icon');
            const btn = document.getElementById('gam-ok');
            
            document.getElementById('gam-title').innerText = title;
            document.getElementById('gam-msg').innerText = message;
            
            if (type === 'success') {
                icon.innerText = '✅';
                btn.style.background = '#10b981';
            } else {
                icon.innerText = '❌';
                btn.style.background = '#ef4444';
            }
            
            modal.style.display = 'flex';
        }
        function setThemeMode(mode, btn) {
            document.getElementById('inputMode').value = mode;
            document.querySelectorAll('#themeForm .tab-btn').forEach(b => {
                b.classList.remove('bg-white', 'shadow-sm');
                b.style.background = 'transparent';
                b.style.boxShadow = 'none';
                b.style.color = 'var(--text-secondary)';
            });
            btn.classList.add('bg-white', 'shadow-sm');
            btn.style.background = 'var(--bg-card)';
            btn.style.boxShadow = 'var(--shadow-sm)'; 
            btn.style.color = 'var(--primary)';
            if (mode === 'dark') document.body.classList.add('dark-mode');
            else if (mode === 'light') document.body.classList.remove('dark-mode');
        }

        function selectPreset(name, primary, bgMain, bgCard) {
            document.getElementById('inputPreset').value = name;
            document.querySelectorAll('.theme-option').forEach(el => el.classList.remove('active'));
            event.currentTarget.classList.add('active');
            const options = document.querySelectorAll('.theme-option');
            document.querySelector('input[name="color_primary"]').value = primary;
            document.querySelector('input[name="color_bg_main"]').value = bgMain;
            document.querySelector('input[name="color_bg_card"]').value = bgCard;
            document.documentElement.style.setProperty('--primary', primary);
            document.documentElement.style.setProperty('--bg-main', bgMain);
            document.documentElement.style.setProperty('--bg-card', bgCard);
            document.querySelector('input[name="color_primary"]').nextElementSibling.value = primary;
            document.querySelector('input[name="color_bg_main"]').nextElementSibling.value = bgMain;
            document.querySelector('input[name="color_bg_card"]').nextElementSibling.value = bgCard;
        }

        function updateLivePreview(key, value) {
            if (key === 'primary') document.documentElement.style.setProperty('--primary', value);
            if (key === 'bg_main') document.documentElement.style.setProperty('--bg-main', value);
            if (key === 'bg_card') document.documentElement.style.setProperty('--bg-card', value);
            const picker = document.querySelector(`input[name="color_${key}"]`);
            if (picker && picker.nextElementSibling) {
                picker.nextElementSibling.value = value.toUpperCase();
            }
            document.getElementById('inputPreset').value = 'custom';
            document.querySelectorAll('.theme-option').forEach(el => el.classList.remove('active'));
        }
        document.querySelectorAll('.theme-option').forEach(opt => {
            opt.addEventListener('click', function() {
                document.querySelectorAll('.theme-option').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
