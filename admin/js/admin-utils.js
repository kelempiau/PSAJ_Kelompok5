function exportToCSV(filename = 'Export') {
    let table = document.querySelector(".table-container table") || document.querySelector("table");
    if (!table) {
        console.error("No table found to export");
        return;
    }

    let csv = [];
    const rows = table.querySelectorAll("tr");

    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length - 1; j++) {
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            row.push('"' + text + '"');
        }
        if (row.length > 0) csv.push(row.join(","));
    }

    if (csv.length === 0) return;

    const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.download = filename + "_" + new Date().toISOString().slice(0, 10) + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
document.addEventListener('DOMContentLoaded', function () {
    initLiveClock();
    initMobileNav();
});

function initLiveClock() {
    const headerActions = document.querySelector('.header-actions');
    if (!headerActions) return;
    let clockEl = document.getElementById('global-clock');
    if (!clockEl) {
        clockEl = document.createElement('div');
        clockEl.id = 'global-clock';
        clockEl.className = 'header-clock';
        headerActions.insertBefore(clockEl, headerActions.firstChild);
    }

    function updateClock() {
        const now = new Date();
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const dateStr = now.toLocaleDateString('en-GB', options);
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeStr = `${hours}:${minutes}:${seconds}`;

        clockEl.innerText = `${dateStr}   ${timeStr}`;
    }

    updateClock();
    setInterval(updateClock, 1000);
}

function initMobileNav() {
    const topHeader = document.querySelector('.top-header');
    const sidebar = document.querySelector('.sidebar');
    if (!topHeader || !sidebar) return;

    let toggleBtn = document.querySelector('.mobile-nav-toggle');
    if (!toggleBtn) {
        toggleBtn = document.createElement('button');
        toggleBtn.className = 'mobile-nav-toggle';
        toggleBtn.innerHTML = '<span style="font-size: 20px;">☰</span>';
        topHeader.insertBefore(toggleBtn, topHeader.firstChild);
    }

    toggleBtn.onclick = function (e) {
        e.stopPropagation();
        sidebar.classList.toggle('active');
    };

    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });

    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('active');
            }
        });
    });
}

