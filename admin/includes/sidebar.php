<div class="sidebar">

    <div class="sidebar-menu">
        <span class="menu-label">Main Menu</span>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <span>📊</span> Dashboard
        </a>
        <a href="../index.php" target="_blank">
            <span>🏠</span> Beranda / Website
        </a>
        <a href="conversations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'conversations.php' ? 'active' : '' ?>" id="navChatLink">
            <span>💬</span> Chat
            <div id="navChatBadge" class="badge">0</div>
        </a>

        <span class="menu-label">Pengelolaan</span>
        <a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
            <span>👥</span> Basis Pengguna
        </a>
        <a href="loyalty.php" class="<?= basename($_SERVER['PHP_SELF']) == 'loyalty.php' ? 'active' : '' ?>">
            <span>⭐</span> Loyalty Level
        </a>
        <a href="reservations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : '' ?>">
            <span>📅</span> Data Reservasi
        </a>
        <a href="payments_registry.php" class="<?= basename($_SERVER['PHP_SELF']) == 'payments_registry.php' ? 'active' : '' ?>">
            <span>📋</span> Daftar Pelunasan
        </a>
        <a href="refunds.php" class="<?= basename($_SERVER['PHP_SELF']) == 'refunds.php' ? 'active' : '' ?>">
            <span>💰</span> Refund Board
        </a>
        <a href="slots.php" class="<?= basename($_SERVER['PHP_SELF']) == 'slots.php' ? 'active' : '' ?>">
            <span>🔒</span> Jadwal Terkunci
        </a>
        <a href="feedback.php" class="<?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : '' ?>">
            <span>📣</span> Feedback Board
        </a>
        <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
            <span>⚙️</span> Pengaturan Studio
        </a>
        <a href="services.php" class="<?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : '' ?>">
            <span>💅</span> Layanan
        </a>
        <a href="why_choose_us.php" class="<?= basename($_SERVER['PHP_SELF']) == 'why_choose_us.php' ? 'active' : '' ?>">
            <span>💡</span> Penjelasan
        </a>
        <a href="faq.php" class="<?= basename($_SERVER['PHP_SELF']) == 'faq.php' ? 'active' : '' ?>">
            <span>❓</span> FAQ
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-profile-mini" onclick="openSettingsModal()">
            <div class="mini-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
            <div class="mini-info">
                <div class="name"><?= htmlspecialchars($_SESSION['username']) ?></div>
                <div class="role">Senior Admin</div>
            </div>
            <span style="margin-left:auto; font-size:12px; color:var(--text-muted);">⚙️</span>
        </div>
    </div>
</div>

<?php

if(!isset($settings['admin_profile_pic'])) {
    $adminPicRes = $conn->query("SELECT admin_profile_pic FROM studio_settings WHERE id = 1");
    $adminPicData = $adminPicRes ? $adminPicRes->fetch_assoc() : null;
    $adminProfilePic = $adminPicData['admin_profile_pic'] ?? '';
} else {
    $adminProfilePic = $settings['admin_profile_pic'];
}
?>

<div id="settingsModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999; align-items:center; justify-content:center; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="card active" style="width:90%; max-width:400px; padding: 32px; border-radius: 20px;">
        <h3 style="margin-bottom: 24px; text-align: center;">⚙️ Admin Settings</h3>
        
        
        <div style="padding-bottom: 24px; border-bottom: 1px solid var(--border-color); margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; border-radius: 24px; margin: 0 auto 16px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.1); background: var(--bg-main); border: 3px solid white;">
                <?php if(!empty($adminProfilePic)): ?>
                    <img src="../<?= $adminProfilePic ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #eff6ff; color: #2563eb; font-size: 2rem;">👩‍💼</div>
                <?php endif; ?>
            </div>
            
            <form action="settings.php" method="POST" enctype="multipart/form-data" id="sidebarAdminPicForm">
                <input type="hidden" name="action" value="update_admin_pic">
                <div onclick="document.getElementById('sidebar_admin_pic_input').click()" style="text-align: center; cursor: pointer; padding: 8px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; transition: 0.2s;" onmouseover="this.style.borderColor='#2563eb'; this.style.background='#eff6ff';">
                    <span style="font-size: 13px; font-weight: 700; color: #475569;">Ganti Foto Profil</span>
                    <input type="file" name="admin_profile_pic" id="sidebar_admin_pic_input" style="display: none;" accept="image/*" onchange="this.form.submit()">
                </div>
            </form>
        </div>
        


        <div style="padding: 20px 0; border-bottom: 1px solid var(--border-color);">
            <h4 style="font-size: 14px; color: var(--text-muted); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <span>🔑</span> Password Keamanan
            </h4>
            <div style="background: #fffafa; padding: 12px; border: 1px solid #fff0f3; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                <span style="font-size: 13px; font-weight: 600; color: #555; letter-spacing: 2px;" id="adminCurrentPassDisplay">••••••••</span>
                <span style="cursor: pointer; font-size: 16px; opacity: 0.6;" onclick="toggleAdminPassReveal()">👁️</span>
            </div>
            <input type="hidden" id="adminRawPass" value="<?= htmlspecialchars($_SESSION['admin_temp_pass'] ?? '•••••••••••••') ?>">
            
            <button onclick="openAdminPassModal()" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 13px; padding: 10px; background: #2563eb;">Ubah Password</button>
        </div>

        <div style="padding: 20px 0; border-bottom: 1px solid var(--border-color);">
            <button onclick="openResetModal()" class="btn btn-outline" style="width: 100%; justify-content: center; color: #ef4444; border-color: #fee2e2; gap: 10px; font-size: 13px;">
                <span>🗑️</span> Reset Revenue & Booking
            </button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 24px;">
            <button onclick="closeSettingsModal()" class="btn btn-outline" style="justify-content: center;">Tutup</button>
            <a href="javascript:void(0)" onclick="confirmLogout('../auth/logout.php')" class="btn btn-danger" style="justify-content: center; text-decoration: none;">
                Logout
            </a>
        </div>
    </div>
</div>

<div id="adminPassModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:10000; align-items:center; justify-content:center; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
    <div class="card active" style="width:95%; max-width:380px; padding: 32px; border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="background: #eff6ff; padding: 8px; border-radius: 12px;">🔑</span> Ganti Password
        </h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;">Silakan masukkan password lama dan password baru Anda.</p>
        
        <form id="adminPassForm">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Password Saat Ini</label>
                <input type="password" name="current_password" placeholder="••••••••" required 
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; outline: none; background: #f8fafc; transition: 0.2s;" 
                       onfocus="this.style.borderColor='#2563eb'; this.style.background='#fff';">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Password Baru</label>
                <input type="password" name="new_password" placeholder="Min. 6 Karakter" required 
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; outline: none; background: #f8fafc;"
                       onfocus="this.style.borderColor='#2563eb'; this.style.background='#fff';">
            </div>

            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" placeholder="Ulangi Password Baru" required 
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; outline: none; background: #f8fafc;"
                       onfocus="this.style.borderColor='#2563eb'; this.style.background='#fff';">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button type="button" onclick="closeAdminPassModal()" class="btn btn-outline" style="justify-content: center; border-radius: 12px; padding: 12px;">Batal</button>
                <button type="button" onclick="handleAdminPassChange()" class="btn btn-primary" style="justify-content: center; background: #2563eb; border-radius: 12px; color: white; border:none; padding: 12px;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="resetConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 10000020; align-items: center; justify-content: center; color: #333;">
    <div style="background: white; padding: 40px; border-radius: 28px; text-align: center; max-width: 420px; width: 90%; box-shadow: 0 25px 70px rgba(0,0,0,0.4); border: 1px solid #e2e8f0;">
        <div style="font-size: 4rem; margin-bottom: 24px;">🔥</div>
        <h3 style="margin-bottom: 15px; font-weight: 800; color: #1e293b; font-size: 22px;">Hapus Seluruh Data?</h3>
        <p style="color: #64748b; margin-bottom: 32px; font-size: 15px; line-height: 1.6;">Apakah Kakak serius ingin menghapus <b>Data Keuntungan</b> dan <b>Data Booking</b>? Tindakan ini juga akan menghapus data reservasi, pelunasan, dan refund.</p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <button onclick="closeResetModal()" class="btn btn-outline" style="justify-content: center; padding: 14px; border-radius: 14px; font-weight: 700;">Tidak, Batal</button>
            <button id="btnConfirmReset" onclick="executeResetData()" disabled class="btn btn-primary" style="justify-content: center; padding: 14px; border-radius: 14px; font-weight: 700; background: #94a3b8; border: none; cursor: not-allowed; transition: all 0.3s;">
                Iya, Hapus (10s)
            </button>
        </div>
    </div>
</div>

<div id="statusModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 10000030; align-items: center; justify-content: center; color: #333;">
    <div style="background: white; padding: 40px; border-radius: 28px; text-align: center; max-width: 380px; width: 90%; box-shadow: 0 25px 70px rgba(0,0,0,0.3); border: 1px solid #e2e8f0;">
        <div id="statusIcon" style="font-size: 4rem; margin-bottom: 24px;">✅</div>
        <h3 id="statusTitle" style="margin-bottom: 15px; font-weight: 800; color: #1e293b; font-size: 20px;">Berhasil!</h3>
        <p id="statusText" style="color: #64748b; margin-bottom: 32px; font-size: 15px; line-height: 1.6;">Tindakan berhasil dilakukan.</p>
        <button id="statusCloseBtn" onclick="closeStatusModal()" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px; border-radius: 14px; font-weight: 700;">Oke, Mengerti</button>
    </div>
</div>

<div id="confirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000010; align-items: center; justify-content: center; color: #333;">
    <div style="background: white; padding: 30px; border-radius: 24px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: 1px solid #e2e8f0;">
        <div id="confirmIcon" style="font-size: 3rem; margin-bottom: 20px;">⚠️</div>
        <h3 id="confirmTitle" style="margin-bottom: 10px; font-weight: 700; color: #1e293b;">Konfirmasi</h3>
        <p id="confirmText" style="color: #64748b; margin-bottom: 30px; font-size: 14px; line-height: 1.6;">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeGlobalConfirm(false)" class="btn btn-outline" style="flex: 1; justify-content: center;">Batal</button>
            <button id="confirmExecuteBtn" onclick="closeGlobalConfirm(true)" class="btn btn-primary" style="flex: 1; justify-content: center; background: #f43f5e; border: none; color: white;">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
let confirmCallback = null;
function showGlobalConfirm(title, text, type, callback) {
    document.getElementById('confirmTitle').innerText = title;
    document.getElementById('confirmText').innerText = text;
    document.getElementById('confirmIcon').innerText = type === 'delete' ? '🗑️' : '⚠️';
    document.getElementById('confirmExecuteBtn').innerText = type === 'delete' ? 'Ya, Hapus' : 'Lanjutkan';
    document.getElementById('confirmExecuteBtn').style.background = type === 'delete' ? '#f43f5e' : '#2563eb';
    
    confirmCallback = callback;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeGlobalConfirm(confirmed) {
    document.getElementById('confirmModal').style.display = 'none';
    if (confirmed && confirmCallback) confirmCallback();
}
</script>

<style>
/* Slider Toggle Style */
.theme-switch { position: relative; display: inline-block; width: 52px; height: 28px; }
.theme-switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #E5E7EB; transition: .4s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
.sidebar-search input {
    width: 100%;
    padding: 10px 12px 10px 42px; /* Increased left padding for icon */
    background: #fff0f5;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}
input:checked + .slider { background-color: var(--primary); }
input:checked + .slider:before { transform: translateX(24px); }
</style>


<script>
function openSettingsModal() { document.getElementById('settingsModal').style.display = 'flex'; }
function closeSettingsModal() { document.getElementById('settingsModal').style.display = 'none'; }

function openAdminPassModal() {
    document.getElementById('adminPassModal').style.display = 'flex';
}
function closeAdminPassModal() {
    document.getElementById('adminPassModal').style.display = 'none';
}

function confirmLogout(url) {
    document.getElementById('settingsModal').style.display = 'none';
    showGlobalConfirm("Konfirmasi Logout", "Huhu, Kakak yakin ingin keluar dari panel admin?", "logout", () => {
        window.location.href = url;
    });
}

function toggleDarkMode() {
    const isDark = document.getElementById('darkModeToggle').checked;
    if (isDark) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
    } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        const toggle = document.getElementById('darkModeToggle');
        if(toggle) toggle.checked = true;
    }
    updateChatBadge();
    setInterval(updateChatBadge, 5000);
});

async function updateChatBadge() {
    try {
        const response = await fetch('../api/chat/conversations.php');
        const data = await response.json();
        
        if (data.success && data.conversations) {
            let totalUnread = 0;
            data.conversations.forEach(c => {
                totalUnread += parseInt(c.unread_count || 0);
            });

            const badge = document.getElementById('navChatBadge');
            if (badge) {
                if (totalUnread > 0) {
                    badge.innerText = totalUnread > 99 ? '99+' : totalUnread;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    } catch (e) {
    }
}

async function adminHeartbeat() { try { await fetch('../api/user/heartbeat.php'); } catch (e) {} }
setInterval(adminHeartbeat, 30000);
adminHeartbeat();

function filterMenu() {
    const input = document.getElementById('menuSearch');
    const filter = input.value.toLowerCase();
    const menuLinks = document.querySelectorAll('.sidebar-menu a');
    const labels = document.querySelectorAll('.sidebar-menu .menu-label');

    menuLinks.forEach(link => {
        const text = link.innerText.toLowerCase();
        if (text.includes(filter)) {
            link.style.display = "";
        } else {
            link.style.display = "none";
        }
    });

    labels.forEach(label => {
        let next = label.nextElementSibling;
        let hasVisible = false;
        while (next && !next.classList.contains('menu-label')) {
            if (next.tagName === 'A' && next.style.display !== 'none') {
                hasVisible = true;
                break;
            }
            next = next.nextElementSibling;
        }
        label.style.display = hasVisible ? "" : "none";
    });
}

let resetTimer = null;
let resetCountdown = 10;

function openResetModal() {
    closeSettingsModal();
    document.getElementById('resetConfirmModal').style.display = 'flex';
    resetCountdown = 10;
    const btn = document.getElementById('btnConfirmReset');
    btn.disabled = true;
    btn.style.background = '#94a3b8';
    btn.style.cursor = 'not-allowed';
    btn.innerText = `Iya, Hapus (${resetCountdown}s)`;

    if(resetTimer) clearInterval(resetTimer);
    resetTimer = setInterval(() => {
        resetCountdown--;
        btn.innerText = `Iya, Hapus (${resetCountdown}s)`;
        if(resetCountdown <= 0) {
            clearInterval(resetTimer);
            btn.disabled = false;
            btn.style.background = '#ef4444'; 
            btn.style.cursor = 'pointer';
            btn.innerText = "Iya, Hapus SEKARANG";
        }
    }, 1000);
}

function closeResetModal() {
    document.getElementById('resetConfirmModal').style.display = 'none';
    if(resetTimer) clearInterval(resetTimer);
}

async function executeResetData() {
    const btn = document.getElementById('btnConfirmReset');
    btn.disabled = true;
    btn.innerText = "Mereset...";
    
    try {
        const formData = new FormData();
        formData.append('action', 'reset_all_transactions');
        
        const response = await fetch('../api/admin/reset_data.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        if(data.success) {
            closeResetModal();
            showStatusModal("✨ Berhasil!", data.message, "✅", () => {
                window.location.reload();
            });
        } else {
            showStatusModal("❌ Gagal", data.message, "⚠️");
            btn.disabled = false;
            btn.innerText = "Iya, Hapus SEKARANG";
        }
    } catch(e) {
        showStatusModal("❌ Gagal", "Gagal menghubungi server.", "⚠️");
        btn.disabled = false;
        btn.innerText = "Iya, Hapus SEKARANG";
    }
}

let statusModalCallback = null;
function showStatusModal(title, text, icon, callback = null) {
    document.getElementById('statusTitle').innerText = title;
    document.getElementById('statusText').innerText = text;
    document.getElementById('statusIcon').innerText = icon || '✅';
    statusModalCallback = callback;
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    if(statusModalCallback) statusModalCallback();
}

window.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('menuSearch');
        if (searchInput) searchInput.focus();
    }
});

function toggleAdminPassReveal() {
    const display = document.getElementById('adminCurrentPassDisplay');
    const raw = document.getElementById('adminRawPass');
    if (!display || !raw) return;

    if (display.innerText.includes('••••')) {
        if (raw.value === '•••••••••••••') {
            showStatusModal("🔒 Keamanan", "Password lama sudah terenkripsi. Silakan ganti baru jika ingin bisa dilihat.", "⚠️");
        } else {
            display.innerText = raw.value;
            display.style.letterSpacing = 'normal';
        }
    } else {
        display.innerText = "••••••••";
        display.style.letterSpacing = '2px';
    }
}

async function handleAdminPassChange() {
    const form = document.getElementById('adminPassForm');
    const formData = new FormData(form);
    const submitBtn = event.target;
    
    submitBtn.disabled = true;
    submitBtn.innerText = "Memproses...";

    try {
        const response = await fetch('../api/admin/change_password.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            closeAdminPassModal();
            showStatusModal("✨ Berhasil!", data.message, "✅", () => {
                const rawInput = document.getElementById('adminRawPass');
                if (rawInput) rawInput.value = data.new_pass;
                form.reset();
            });
        } else {
            showStatusModal("❌ Gagal", data.message, "⚠️");
        }
    } catch (e) {
        showStatusModal("❌ Gagal", "Terjadi kesalahan koneksi.", "⚠️");
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = "Simpan";
    }
}
</script>
