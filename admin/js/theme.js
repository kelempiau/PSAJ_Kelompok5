function openSettings() {
    document.getElementById('settingsModal').classList.add('active');
    loadCurrentTheme();
}

function closeSettings() {
    document.getElementById('settingsModal').classList.remove('active');
}

async function loadCurrentTheme() {
    const response = await fetch('../api/settings/theme.php');
    const data = await response.json();

    if (data.success) {
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.theme === data.theme) {
                btn.classList.add('active');
            }
        });
    }
}

async function setTheme(theme) {
    const formData = new FormData();
    formData.append('theme', theme);

    const response = await fetch('../api/settings/theme.php', {
        method: 'POST',
        body: formData
    });

    const data = await response.json();
    if (data.success) {
        document.documentElement.setAttribute('data-theme', theme);
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.theme === theme) {
                btn.classList.add('active');
            }
        });
    }
}

window.onclick = function (event) {
    const modal = document.getElementById('settingsModal');
    if (event.target === modal) {
        closeSettings();
    }
}
