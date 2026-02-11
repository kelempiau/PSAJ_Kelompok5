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

    if ($action === 'save_faq') {
        $id = intval($_POST['faq_id'] ?? 0);
        $question = $_POST['question'];
        $answer = $_POST['answer'];

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE faq SET question=?, answer=? WHERE id=?");
            $stmt->bind_param("ssi", $question, $answer, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO faq (question, answer) VALUES (?, ?)");
            $stmt->bind_param("ss", $question, $answer);
        }

        if ($stmt->execute()) $msg = "FAQ berhasil disimpan.";
        else $error = "Gagal menyimpan FAQ.";
    }

    if ($action === 'delete_faq') {
        $id = intval($_POST['faq_id']);
        if ($conn->query("DELETE FROM faq WHERE id = $id")) $msg = "FAQ berhasil dihapus.";
        else $error = "Gagal menghapus FAQ.";
    }
}

$faqs = null;
try {
    $faqs = $conn->query("SELECT * FROM faq ORDER BY id ASC");
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        $error = "Tabel 'faq' belum ada. Silakan klik tombol perbaiki di bawah.";
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
    <title>Kelola FAQ - Admin Panel</title>
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
                    <span class="current">FAQ</span>
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">❓</div>
                <div class="context-info">
                    <div class="title">Kelola FAQ</div>
                    <div class="subtitle">Atur pertanyaan yang sering diajukan oleh customer.</div>
                </div>
                <button onclick="openModal()" class="btn btn-primary" style="margin-left: auto;">+ Tambah FAQ</button>
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

                <div class="card" style="padding: 0;">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Pertanyaan</th>
                                    <th>Jawaban</th>
                                    <th style="width: 150px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($faqs && $faqs->num_rows > 0): ?>
                                    <?php while($f = $faqs->fetch_assoc()): ?>
                                    <tr>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($f['question']) ?></td>
                                        <td style="color: var(--text-secondary);"><?= htmlspecialchars($f['answer']) ?></td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; gap: 8px; justify-content: center;">
                                                <button onclick='editFaq(<?= json_encode($f) ?>)' class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">Edit</button>
                                                <button onclick="confirmDeleteFaq(<?= $f['id'] ?>)" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; color: #ef4444;">Hapus</button>
                                                
                                                <!-- Hidden form for deletion -->
                                                <form id="delete-form-<?= $f['id'] ?>" method="POST" style="display:none;">
                                                    <input type="hidden" name="action" value="delete_faq">
                                                    <input type="hidden" name="faq_id" value="<?= $f['id'] ?>">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada FAQ.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FAQ -->
    <div id="faqModal" class="modal-overlay" style="display: none;">
        <div class="modal-card" style="max-width: 600px;">
            <h3 id="modalTitle" style="text-align: center; margin-bottom: 24px;">Tambah FAQ</h3>
            <form method="POST">
                <input type="hidden" name="action" value="save_faq">
                <input type="hidden" name="faq_id" id="edit_id" value="">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Pertanyaan</label>
                    <input type="text" name="question" id="edit_question" style="width: 100%;" required>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label>Jawaban</label>
                    <textarea name="answer" id="edit_answer" rows="6" style="width: 100%;" required></textarea>
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
            document.getElementById('modalTitle').innerText = "Tambah FAQ";
            document.getElementById('edit_id').value = "";
            document.getElementById('edit_question').value = "";
            document.getElementById('edit_answer').value = "";
            document.getElementById('faqModal').style.display = 'flex';
        }

        function closeModal() { document.getElementById('faqModal').style.display = 'none'; }

        function confirmDeleteFaq(id) {
            showGlobalConfirm(
                "Hapus FAQ", 
                "Apakah Kakak yakin ingin menghapus pertanyaan ini?", 
                "delete", 
                () => { document.getElementById('delete-form-' + id).submit(); }
            );
        }

        function editFaq(data) {
            document.getElementById('modalTitle').innerText = "Edit FAQ";
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_question').value = data.question;
            document.getElementById('edit_answer').value = data.answer;
            document.getElementById('faqModal').style.display = 'flex';
        }
    </script>
</body>
</html>
