<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_feature') {
        $id = intval($_POST['feature_id'] ?? 0);
        $title = $_POST['title'];
        $description = $_POST['description'];
        $icon = $_POST['icon'];

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE why_choose_us SET title=?, description=?, icon=? WHERE id=?");
            $stmt->bind_param("sssi", $title, $description, $icon, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO why_choose_us (title, description, icon) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $description, $icon);
        }

        if ($stmt->execute()) $msg = "Penjelasan berhasil disimpan.";
        else $error = "Gagal menyimpan penjelasan.";
    }

    if ($action === 'delete_feature') {
        $id = intval($_POST['feature_id']);
        if ($conn->query("DELETE FROM why_choose_us WHERE id = $id")) $msg = "Penjelasan berhasil dihapus.";
        else $error = "Gagal menghapus penjelasan.";
    }
}

$features = null;
try {
    $features = $conn->query("SELECT * FROM why_choose_us ORDER BY id ASC");
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        $error = "Tabel 'why_choose_us' belum ada. Silakan klik tombol perbaiki di bawah.";
    } else {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Penjelasan - Admin Panel</title>
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
                    <span class="current">Penjelasan</span>
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">💡</div>
                <div class="context-info">
                    <div class="title">Kenapa Memilih Neydream?</div>
                    <div class="subtitle">Kelola poin-poin keunggulan studio yang muncul di Beranda.</div>
                </div>
                <button onclick="openModal()" class="btn btn-primary" style="margin-left: auto;">+ Tambah Poin</button>
            </div>

            <div style="padding: 24px;">
                <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999;"></div>
                <?php if($msg): ?> <script>window.onload = () => showToast("<?= $msg ?>", "success");</script> <?php endif; ?>
                <?php if($error): ?> <div class="card" style="background: #fff1f2; border: 1px solid #fda4af; padding: 20px; color: #9f1239; margin-bottom: 24px; display: flex; align-items: center; gap: 15px; justify-content: space-between;">
                    <div>⚠️ <?= $error ?></div>
                    <?php if(strpos($error, 'belum ada') !== false): ?>
                        <a href="fix_database.php" class="btn btn-primary" style="background: #e11d48; border: none; white-space: nowrap;">Perbaiki Sekarang</a>
                    <?php endif; ?>
                </div> <?php endif; ?>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                    <?php if($features): while($f = $features->fetch_assoc()): ?>
                    <div class="card" style="margin-bottom: 0;">
                        <div style="padding: 24px;">
                            <div style="font-size: 40px; margin-bottom: 16px;"><?= $f['icon'] ?></div>
                            <h3 style="margin-bottom: 12px;"><?= htmlspecialchars($f['title']) ?></h3>
                            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6; margin-bottom: 24px;">
                                <?= htmlspecialchars($f['description']) ?>
                            </p>
                            <div style="display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid var(--border-color);">
                                <button onclick='editFeature(<?= json_encode($f) ?>)' class="btn btn-outline" style="flex:1; justify-content: center;">Edit</button>
                                <button onclick="confirmDeleteFeature(<?= $f['id'] ?>, '<?= addslashes($f['title']) ?>')" class="btn btn-outline" style="flex:1; justify-content: center; color: #ef4444;">Hapus</button>
                            </div>

                            <!-- Hidden form for deletion -->
                            <form id="delete-form-<?= $f['id'] ?>" method="POST" style="display:none;">
                                <input type="hidden" name="action" value="delete_feature">
                                <input type="hidden" name="feature_id" value="<?= $f['id'] ?>">
                            </form>
                        </div>
                    </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POIN -->
    <div id="featureModal" class="modal-overlay" style="display: none;">
        <div class="modal-card" style="max-width: 500px;">
            <h3 id="modalTitle" style="text-align: center; margin-bottom: 24px;">Tambah Keunggulan</h3>
            <form method="POST">
                <input type="hidden" name="action" value="save_feature">
                <input type="hidden" name="feature_id" id="edit_id" value="">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Icon / Emoji (Contoh: ✨, 🎨, 🛡️)</label>
                    <input type="text" name="icon" id="edit_icon" style="width: 100%;" placeholder="Pilih 1 emoji" required>
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Judul Keunggulan</label>
                    <input type="text" name="title" id="edit_title" style="width: 100%;" required>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label>Deskripsi Penjelasan</label>
                    <textarea name="description" id="edit_description" rows="5" style="width: 100%;" required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button type="button" onclick="closeModal()" class="btn btn-outline" style="justify-content: center;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="justify-content: center;">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.style.cssText = `padding: 12px 24px; background: ${type === 'success' ? '#10b981' : '#ef4444'}; color: white; border-radius: 12px; margin-bottom: 10px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); animation: slideIn 0.3s ease-out forwards; font-weight: 600; display: flex; align-items: center; gap: 10px;`;
            toast.innerHTML = `<span>${type === 'success' ? '✅' : '❌'}</span> ${message}`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        const toastStyle = document.createElement('style');
        toastStyle.innerHTML = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }`;
        document.head.appendChild(toastStyle);

        function openModal() {
            document.getElementById('modalTitle').innerText = "Tambah Keunggulan";
            document.getElementById('edit_id').value = "";
            document.getElementById('edit_icon').value = "";
            document.getElementById('edit_title').value = "";
            document.getElementById('edit_description').value = "";
            document.getElementById('featureModal').style.display = 'flex';
        }

        function closeModal() { document.getElementById('featureModal').style.display = 'none'; }

        function confirmDeleteFeature(id, title) {
            showGlobalConfirm(
                "Hapus Poin", 
                `Apakah Kakak yakin ingin menghapus poin "${title}"?`, 
                "delete", 
                () => { document.getElementById('delete-form-' + id).submit(); }
            );
        }

        function editFeature(data) {
            document.getElementById('modalTitle').innerText = "Edit Keunggulan";
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_icon').value = data.icon;
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('featureModal').style.display = 'flex';
        }
    </script>
</body>
</html>
