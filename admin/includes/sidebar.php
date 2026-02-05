<div class="sidebar">
    <!-- Logo & Search Removed per User Request -->

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

<!-- Settings Modal (Legacy Logic Kept, UI Updated) -->
<div id="settingsModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999; align-items:center; justify-content:center;">
    <div class="card active" style="width:90%; max-width:400px; padding: 32px;">
        <h3 style="margin-bottom: 24px;">⚙️ Admin Settings</h3>
        
        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:20px; border-bottom:1px solid var(--border-color);">
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:20px;">🌙</span>
                <span style="font-weight:600;">Dark Mode Preview</span>
            </div>
            <label class="theme-switch">
                <input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode()">
                <span class="slider"></span>
            </label>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 24px;">
            <button onclick="closeSettingsModal()" class="btn btn-outline" style="justify-content: center;">Tutup</button>
            <a href="javascript:void(0)" onclick="confirmLogout('../auth/logout.php')" class="btn btn-danger" style="justify-content: center; text-decoration: none;">
                Logout
            </a>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000000; align-items: center; justify-content: center; color: #333;">
    <div style="background: white; padding: 30px; border-radius: 20px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <div style="font-size: 3rem; margin-bottom: 20px;">🚪</div>
        <h3 style="margin-bottom: 15px; font-weight: 700;">Konfirmasi Logout</h3>
        <p style="color: #666; margin-bottom: 30px; font-size: 14px;">Apakah Kakak yakin ingin keluar dari panel admin?</p>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <button onclick="handleLogoutConfirm(false)" class="btn btn-outline" style="flex: 1; justify-content: center;">Tidak</button>
            <button onclick="handleLogoutConfirm(true)" class="btn btn-primary" style="flex: 1; justify-content: center; background: #ff5c8a; border: none; color: white;">Logout</button>
        </div>
    </div>
</div>

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
// UI HANDLING
function openSettingsModal() { document.getElementById('settingsModal').style.display = 'flex'; }
function closeSettingsModal() { document.getElementById('settingsModal').style.display = 'none'; }

function confirmLogout(url) {
    document.getElementById('settingsModal').style.display = 'none';
    document.getElementById('logoutModal').style.display = 'flex';
}

function handleLogoutConfirm(confirmed) {
    if (confirmed) {
        window.location.href = '../auth/logout.php';
    } else {
        document.getElementById('logoutModal').style.display = 'none';
    }
}

// DARK MODE LOGIC
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

// INITIALIZE
window.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        const toggle = document.getElementById('darkModeToggle');
        if(toggle) toggle.checked = true;
    }
    updateChatBadge();
    setInterval(updateChatBadge, 5000); // Check every 5 seconds
});

// CHAT NOTIFICATION LOGIC
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
        // Silent error
    }
}

// HEARTBEAT
async function adminHeartbeat() { try { await fetch('../api/user/heartbeat.php'); } catch (e) {} }
setInterval(adminHeartbeat, 30000);
adminHeartbeat();

// MENU SEARCH FILTER
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

    // Hide labels if no links under them are visible
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

// SHORTCUT ⌘ K
window.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('menuSearch').focus();
    }
});
</script>
