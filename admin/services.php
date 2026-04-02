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

    if ($action === 'save_service') {
        $id = intval($_POST['service_id'] ?? 0);
        $name = $_POST['name'];
        $description = $_POST['description'];
        $details = $_POST['details'];
        $price = floatval($_POST['price_start']);
        $image_path = $_POST['existing_image'] ?? '';

        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === 0) {
            $targetDir = "../assets/img/";
            if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = time() . "_" . $_FILES['service_image']['name'];
            if (move_uploaded_file($_FILES['service_image']['tmp_name'], $targetDir . $fileName)) {
                $image_path = "assets/img/" . $fileName;
            }
        }

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE services SET name=?, description=?, details=?, price_start=?, image_path=? WHERE id=?");
            $stmt->bind_param("sssdsi", $name, $description, $details, $price, $image_path, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO services (name, description, details, price_start, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $name, $description, $details, $price, $image_path);
        }

        if ($stmt->execute()) $msg = "Layanan berhasil disimpan.";
        else $error = "Gagal menyimpan layanan.";
    }

    if ($action === 'delete_service') {
        $id = intval($_POST['service_id']);
        if ($conn->query("DELETE FROM services WHERE id = $id")) $msg = "Layanan berhasil dihapus.";
        else $error = "Gagal menghapus layanan.";
    }
}

$services = null;
try {
    $services = $conn->query("SELECT * FROM services ORDER BY id DESC");
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        $error = "Tabel 'services' belum ada. Silakan klik tombol perbaiki di bawah.";
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
    <title>Kelola Layanan - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'includes/admin_styles.php'; ?>
    <style>
        .service-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px; }
        .s-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.3s; }
        .s-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
        .s-img { width: 100%; height: 180px; object-fit: cover; border-bottom: 1px solid var(--border-color); }
        .s-content { padding: 20px; flex-grow: 1; }
        .s-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary); }
        .s-desc { font-size: 14px; color: var(--text-secondary); margin-bottom: 15px; line-height: 1.6; }
        .s-price { font-weight: 700; color: var(--primary); font-size: 16px; margin-bottom: 20px; }
        .s-actions { display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid var(--border-color); }
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
                    <span class="current">Layanan</span>
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">💅</div>
                <div class="context-info">
                    <div class="title">Kelola Layanan</div>
                    <div class="subtitle">Atur daftar layanan nail art yang muncul di website.</div>
                </div>
                <button onclick="openModal()" class="btn btn-primary" style="margin-left: auto;">+ Tambah Layanan</button>
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

                <div class="service-list">
                    <?php if($services): while($s = $services->fetch_assoc()): ?>
                    <div class="s-card">
                        <img src="../<?= $s['image_path'] ?>" class="s-img" alt="Service">
                        <div class="s-content">
                            <div class="s-title"><?= htmlspecialchars($s['name']) ?></div>
                            <div class="s-desc"><?= htmlspecialchars($s['description']) ?></div>
                            <div class="s-price">Mulai Rp <?= number_format($s['price_start'], 0, ',', '.') ?></div>
                            <div class="s-actions">
                                <button onclick='editService(<?= json_encode($s) ?>)' class="btn btn-outline" style="flex:1; justify-content: center;">Edit</button>
                                <button onclick="confirmDeleteService(<?= $s['id'] ?>, '<?= addslashes($s['name']) ?>')" class="btn btn-outline" style="flex:1; justify-content: center; color: #ef4444; border-color: #fee2e2;">Hapus</button>
                            </div>

                            <form id="delete-form-<?= $s['id'] ?>" method="POST" style="display:none;">
                                <input type="hidden" name="action" value="delete_service">
                                <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                            </form>
                        </div>
                    </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="serviceModal" class="modal-overlay" style="display: none;">
        <div class="modal-card" style="max-width: 500px;">
            <h3 id="modalTitle" style="text-align: center; margin-bottom: 24px;">Tambah Layanan</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_service">
                <input type="hidden" name="service_id" id="edit_id" value="">
                <input type="hidden" name="existing_image" id="edit_existing_image" value="">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Nama Layanan</label>
                    <input type="text" name="name" id="edit_name" style="width: 100%;" required>
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Deskripsi Singkat (Muncul di List)</label>
                    <textarea name="description" id="edit_description" rows="2" style="width: 100%;" required></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Detail Layanan (Pisahkan dengan koma)</label>
                    <input type="text" name="details" id="edit_details" style="width: 100%;" placeholder="Contoh: Gel Polish, Ombre, Marble" required>
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Harga Mulai (Angka saja)</label>
                    <input type="number" name="price_start" id="edit_price" style="width: 100%;" required>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label>Gambar Layanan</label>
                    <input type="file" name="service_image" accept="image/*" style="width: 100%;">
                    <small style="color: var(--text-muted);">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button type="button" onclick="closeModal()" class="btn btn-outline" style="justify-content: center;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="justify-content: center;">Simpan Layanan</button>
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
            document.getElementById('modalTitle').innerText = "Tambah Layanan";
            document.getElementById('edit_id').value = "";
            document.getElementById('edit_name').value = "";
            document.getElementById('edit_description').value = "";
            document.getElementById('edit_details').value = "";
            document.getElementById('edit_price').value = "";
            document.getElementById('edit_existing_image').value = "";
            document.getElementById('serviceModal').style.display = 'flex';
        }

        function closeModal() { document.getElementById('serviceModal').style.display = 'none'; }

        function confirmDeleteService(id, name) {
            showGlobalConfirm(
                "Hapus Layanan", 
                `Apakah Kakak yakin ingin menghapus layanan "${name}"? Tindakan ini tidak bisa dibatalkan.`, 
                "delete", 
                () => { document.getElementById('delete-form-' + id).submit(); }
            );
        }

        function editService(data) {
            document.getElementById('modalTitle').innerText = "Edit Layanan";
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_details').value = data.details || "";
            document.getElementById('edit_price').value = data.price_start;
            document.getElementById('edit_existing_image').value = data.image_path;
            document.getElementById('serviceModal').style.display = 'flex';
        }
    </script>
</body>
</html>
