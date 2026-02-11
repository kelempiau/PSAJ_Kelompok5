<?php
require '../core/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_reservations = $conn->query("SELECT COUNT(*) as count FROM reservations")->fetch_assoc()['count'];
$pending_refunds = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE refund_status = 'pending'")->fetch_assoc()['count'];
$locked_slots_count = $conn->query("SELECT COUNT(*) as count FROM locked_slots")->fetch_assoc()['count'];
$pending_reservations = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'")->fetch_assoc()['count'];
$recent_feedback = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];

// --- GRAPH DATA FETCHING (DAILY FOR CURRENT MONTH) ---
$current_month = date('m');
$current_year = date('Y');
$days_in_month = date('t'); // Number of days in current month

$chart_query = "SELECT 
                    DAY(reservation_date) as day, 
                    SUM(total_price) as revenue, 
                    COUNT(id) as bookings 
                FROM reservations 
                WHERE status != 'cancelled' 
                  AND MONTH(reservation_date) = '$current_month' 
                  AND YEAR(reservation_date) = '$current_year'
                GROUP BY DAY(reservation_date)";
$chart_result = $conn->query($chart_query);

$chart_data = ['revenue' => [], 'bookings' => []];
while($row = $chart_result->fetch_assoc()) {
    $chart_data['revenue'][$row['day']] = $row['revenue'];
    $chart_data['bookings'][$row['day']] = $row['bookings'];
}

// Prepare Labels (1 to 30/31)
$chart_labels = range(1, $days_in_month);

// Calculate Totals for Display
$total_rev_month = array_sum($chart_data['revenue']);
$total_book_month = array_sum($chart_data['bookings']);

// CHECK IF NEW TABLES EXIST (For Migration Hint)
$tables_missing = false;
$check = $conn->query("SHOW TABLES LIKE 'services'");
if ($check->num_rows === 0) $tables_missing = true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview - Admin Panel</title>
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
                    <span class="sep">Dashboard</span>
                    <span class="sep">/</span>
                    <span class="current">Overview</span>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">👋</div>
                <div class="context-info">
                    <div class="title">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</div>
                    <div class="subtitle">Here's what's happening with Neydream today.</div>
                </div>
            </div>

            <?php if($tables_missing): ?>
            <div style="margin: 20px 24px; padding: 20px; background: #fff1f2; border: 2px dashed #fb7185; border-radius: 16px; display: flex; align-items: center; gap: 20px;">
                <div style="font-size: 32px;">⚠️</div>
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 5px 0; color: #9f1239;">Penting: Database Belum Siap</h4>
                    <p style="margin: 0; font-size: 14px; color: #be123c;">Beberapa tabel (Layanan, FAQ, dll) belum ada di database Kakak. Silakan perbaiki sekarang.</p>
                </div>
                <a href="fix_database.php" class="btn btn-primary" style="background: #e11d48; border: none;">Klik Perbaiki Otomatis</a>
            </div>
            <?php endif; ?>

            <!-- SALES & RESERVATION GRAPH SECTION -->
            <div class="charts-row" style="display: flex; gap: 24px; margin: 30px 24px 24px 24px; flex-wrap: wrap;">
                
                <!-- REVENUE CHART -->
                <div class="chart-card" style="flex: 1; min-width: 320px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0; color: var(--text-primary); font-weight: 700; font-size: 1rem;">Daily Revenue</h3>
                            <p style="margin: 3px 0 0; color: var(--text-secondary); font-size: 0.8rem;"><?= date('F Y') ?></p>
                        </div>
                        <div style="text-align: right; display: flex; align-items: center; gap: 8px;">
                             <button onclick="openRecapModal('revenue')" style="background: none; border: 1px solid var(--border-color); color: var(--text-secondary); padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                                📅 Recap
                             </button>
                             <span style="font-weight: 600; color: #1e3a8a; background: #eaefff; padding: 3px 10px; border-radius: 6px; font-size: 0.75rem;">Revenue</span>
                        </div>
                    </div>
                    <div style="height: 220px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.85rem; color: var(--text-secondary);">Total Revenue</span>
                        <span style="font-size: 1rem; font-weight: 700; color: var(--primary);">
                            Rp <?= number_format($total_rev_month, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>

                <!-- RESERVATION CHART -->
                <div class="chart-card" style="flex: 1; min-width: 320px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0; color: var(--text-primary); font-weight: 700; font-size: 1rem;">Daily Bookings</h3>
                            <p style="margin: 3px 0 0; color: var(--text-secondary); font-size: 0.8rem;"><?= date('F Y') ?></p>
                        </div>
                         <div style="text-align: right; display: flex; align-items: center; gap: 8px;">
                             <button onclick="openRecapModal('bookings')" style="background: none; border: 1px solid var(--border-color); color: var(--text-secondary); padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;">
                                📅 Recap
                             </button>
                             <span style="font-weight: 600; color: #0284c7; background: #e0f2fe; padding: 3px 10px; border-radius: 6px; font-size: 0.75rem;">Bookings</span>
                        </div>
                    </div>
                    <div style="height: 220px; width: 100%;">
                        <canvas id="bookingChart"></canvas>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.85rem; color: var(--text-secondary);">Total Bookings</span>
                        <span style="font-size: 1rem; font-weight: 700; color: #0284c7;">
                            <?= $total_book_month ?> Customers
                        </span>
                    </div>
                </div>
            </div>

            <!-- Existing Summary Grid -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="label">Total Users</div>
                    <div class="value"><?= $total_users ?></div>
                    <div class="trend">Active Users</div>
                </div>
                <div class="summary-card">
                    <div class="label">Total Reservations</div>
                    <div class="value"><?= $total_reservations ?></div>
                    <?php if($pending_reservations > 0): ?>
                        <div class="trend" style="color: var(--accent);"><?= $pending_reservations ?> pending</div>
                    <?php endif; ?>
                </div>
                <div class="summary-card">
                    <div class="label">Recent Feedback</div>
                    <div class="value"><?= $recent_feedback ?></div>
                    <div class="trend">Last 7 days</div>
                </div>
            </div>

            <div class="board">
                <div class="board-column">
                    <div class="column-header">
                        <span class="column-title">🔥 Urgent Actions</span>
                        <span class="column-count"><?= $pending_refunds + $pending_reservations ?></span>
                    </div>
                    
                    <?php if($pending_refunds > 0): ?>
                    <a href="refunds.php" class="item-card" style="text-decoration: none; color: inherit;">
                        <div class="item-header">
                            <div class="item-avatar" style="background:#fef3c7;">💰</div>
                            <div class="item-title">Process Refund Requests</div>
                        </div>
                        <div class="item-desc">Ada <?= $pending_refunds ?> permintaan pengembalian dana yang menunggu persetujuan Anda.</div>
                        <div class="item-footer">
                            <div class="meta">⏱️ Priority High</div>
                            <div class="meta">💰 Money</div>
                        </div>
                    </a>
                    <?php endif; ?>

                    <?php if($pending_reservations > 0): ?>
                    <a href="reservations.php" class="item-card" style="text-decoration: none; color: inherit;">
                        <div class="item-header">
                            <div class="item-avatar" style="background:#dcfce7;">📅</div>
                            <div class="item-title">Verify Pending Bookings</div>
                        </div>
                        <div class="item-desc">Terdapat <?= $pending_reservations ?> reservasi baru yang perlu diverifikasi segera.</div>
                        <div class="item-footer">
                            <div class="meta">📅 Date</div>
                            <div class="meta">✅ Verify</div>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="board-column">
                    <div class="column-header">
                        <span class="column-title">💬 Communication</span>
                    </div>
                    <a href="conversations.php" class="item-card" style="text-decoration: none; color: inherit;">
                        <div class="item-header">
                            <div class="item-avatar" style="background:var(--primary-light);">💬</div>
                            <div class="item-title">Customer Support Chat</div>
                        </div>
                        <div class="item-desc">Tanggap pesan dari pelanggan dan selesaikan masalah mereka secara real-time.</div>
                        <div class="item-footer">
                            <div class="meta">💬 Chat</div>
                            <div class="meta">⚡ Live</div>
                        </div>
                    </a>
                    <a href="feedback.php" class="item-card" style="text-decoration: none; color: inherit;">
                        <div class="item-header">
                            <div class="item-avatar" style="background:#f3e8ff;">📣</div>
                            <div class="item-title">Review User Feedback</div>
                        </div>
                        <div class="item-desc">Lihat apa yang pelanggan katakan tentang layanan Neydream.</div>
                        <div class="item-footer">
                            <div class="meta">📣 Feedback</div>
                            <div class="meta">📈 Satisfaction</div>
                        </div>
                    </a>
                </div>

                <div class="board-column">
                    <div class="column-header">
                        <span class="column-title">⚙️ System Mgmt</span>
                    </div>
                    <a href="slots.php" class="item-card" style="text-decoration: none; color: inherit;">
                        <div class="item-header">
                            <div class="item-avatar" style="background:var(--primary-light);">🔒</div>
                            <div class="item-title">Manage Locked Slots</div>
                        </div>
                        <div class="item-desc">Buka atau kunci slot waktu reservasi untuk mengontrol ketersediaan.</div>
                        <div class="item-footer">
                            <div class="meta">🔒 Security</div>
                            <div class="meta">⚙️ Config</div>
                        </div>
                    </a>
                </div>

        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const ctxBooking = document.getElementById('bookingChart').getContext('2d');
        
        // Data from PHP
        const chartData = <?= json_encode($chart_data) ?>;
        const labels = <?= json_encode($chart_labels) ?>;
        
        const revenueData = labels.map(day => chartData.revenue[day] || 0);
        const bookingData = labels.map(day => chartData.bookings[day] || 0);

        Chart.defaults.font.family = "'Inter', 'sans-serif'";
        Chart.defaults.font.size = 11;

        // Function to get current theme colors
        function getThemeColors() {
            const isDark = document.body.classList.contains('dark-mode');
            return {
                text: isDark ? '#94a3b8' : '#64748b',
                grid: isDark ? 'rgba(30, 41, 59, 0.5)' : '#f1f5f9',
                ticks: isDark ? '#64748b' : '#94a3b8'
            };
        }

        let colors = getThemeColors();

        // --- DASHBOARD CHARTS ---
        const revenueChart = new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    backgroundColor: '#1e3a8a',
                    borderRadius: 3,
                    hoverBackgroundColor: '#ea3671'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10, color: colors.ticks } },
                    y: { border: { display: false }, grid: { borderDash: [4, 4], color: colors.grid }, ticks: {
                        color: colors.ticks,
                        callback: function(value) {
                            if(value >= 1000000) return (value/1000000).toFixed(0) + 'jt';
                            if(value >= 1000) return (value/1000).toFixed(0) + 'rb';
                            return value;
                        }
                    }}
                }
            }
        });

        const bookingChart = new Chart(ctxBooking, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Bookings',
                    data: bookingData,
                    backgroundColor: '#3b82f6',
                    borderRadius: 3,
                    hoverBackgroundColor: '#ea3671'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10, color: colors.ticks } },
                    y: { border: { display: false }, grid: { borderDash: [4, 4], color: colors.grid }, ticks: { stepSize: 1, precision: 0, color: colors.ticks } }
                }
            }
        });

        // Watch for dark mode changes to update charts
        const observer = new MutationObserver(() => {
            const newColors = getThemeColors();
            [revenueChart, bookingChart, modalChartInstance].forEach(chart => {
                if (chart) {
                    chart.options.scales.x.ticks.color = newColors.ticks;
                    chart.options.scales.y.ticks.color = newColors.ticks;
                    chart.options.scales.y.grid.color = newColors.grid;
                    chart.update();
                }
            });
            // Update modal background for chart container
            const chartContainer = document.getElementById('recapChartContainer');
            if (chartContainer) {
                chartContainer.style.background = document.body.classList.contains('dark-mode') ? '#1e293b' : '#f8fafc';
            }
            // Update modal text colors
            const modalTitle = document.getElementById('modalTitle');
            const modalTotalDisplay = document.getElementById('modalTotalDisplay');
            if (modalTitle) modalTitle.style.color = document.body.classList.contains('dark-mode') ? '#f8fafc' : '#1e293b';
            if (modalTotalDisplay && !modalTotalDisplay.innerText.includes('Error')) {
                // Keep original logic for color in modalTotalDisplay if needed, but ensure readability
            }
        });
        observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });

        // --- MODAL LOGIC ---
        let modalChartInstance = null;
        let currentRecapMode = 'revenue'; // 'revenue' or 'bookings'

        function openRecapModal(mode) {
            currentRecapMode = mode;
            const titleEl = document.getElementById('modalTitle');
            if(mode === 'revenue') {
                titleEl.textContent = '📅 Revenue Recap';
            } else {
                titleEl.textContent = '📅 Bookings Recap';
            }
            
            document.getElementById('recapModal').style.display = 'flex';
            updateRecapChart();
        }

        async function updateRecapChart() {
            const m = document.getElementById('recapMonth').value;
            const y = document.getElementById('recapYear').value;
            const totalDisplay = document.getElementById('modalTotalDisplay');
            const themeColors = getThemeColors();
            
            console.log(`Fetching recap for ${m}/${y}...`);
            totalDisplay.innerText = "Loading data...";

            try {
                const response = await fetch(`api_chart_data.php?month=${m}&year=${y}`);
                const data = await response.json();
                console.log("Recap Data Received:", data);
                
                if (!data.labels || data.labels.length === 0) {
                     totalDisplay.innerText = "No data available for this month";
                     if (modalChartInstance) modalChartInstance.destroy();
                     return;
                }

                if (modalChartInstance) modalChartInstance.destroy();

                const ctx = document.getElementById('modalChartCanvas').getContext('2d');

                if (currentRecapMode === 'revenue') {
                    // Update Total Text for Revenue
                    const totalRev = data.revenue.reduce((a, b) => a + b, 0);
                    totalDisplay.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(totalRev);
                    totalDisplay.style.color = document.body.classList.contains('dark-mode') ? '#38bdf8' : '#1e3a8a';

                    // Render Revenue Line Chart
                    modalChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Revenue',
                                data: data.revenue,
                                borderColor: '#ea3671',
                                backgroundColor: 'rgba(234, 54, 113, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#ea3671',
                                pointRadius: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                title: { display: true, text: 'Daily Revenue - ' + data.label_title, color: themeColors.text }, 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                        }
                                    }
                                }
                            },
                             scales: {
                                x: { grid: { display: false }, ticks: { color: themeColors.ticks } },
                                y: { border: { display: false }, grid: { borderDash: [4, 4], color: themeColors.grid }, ticks: {
                                    color: themeColors.ticks,
                                    callback: function(value) {
                                        if(value >= 1000000) return (value/1000000).toFixed(0) + 'jt';
                                        if(value >= 1000) return (value/1000).toFixed(0) + 'rb';
                                        return value;
                                    }
                                }}
                            }
                        }
                    });
                } else {
                    // Update Total Text for Bookings
                    const totalBook = data.bookings.reduce((a, b) => a + b, 0);
                    totalDisplay.innerText = totalBook + " Customers";
                    totalDisplay.style.color = document.body.classList.contains('dark-mode') ? '#38bdf8' : '#0284c7';

                    // Render Bookings Bar Chart
                    modalChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Bookings',
                                data: data.bookings,
                                backgroundColor: '#0ea5e9',
                                borderRadius: 4
                            }]
                        },
                        options: {
                             responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                title: { display: true, text: 'Daily Bookings - ' + data.label_title, color: themeColors.text }, 
                                legend: { display: false } 
                            },
                             scales: {
                                x: { grid: { display: false }, ticks: { color: themeColors.ticks } },
                                y: { border: { display: false }, grid: { borderDash: [4, 4], color: themeColors.grid }, ticks: { stepSize: 1, color: themeColors.ticks } }
                            }
                        }
                    });
                }

            } catch (error) {
                console.error('Error fetching chart data:', error);
                totalDisplay.innerText = "Error loading data: " + error.message;
            }
        }
        
        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('recapModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>


    <!-- RECAP MODAL -->
    <div id="recapModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; align-items: center; justify-content: center;">
        <div id="recapModalContent" class="modal-card">
            <div class="recap-header">
                <div>
                    <h2 id="modalTitle">📅 Monthly Recap</h2>
                    <p class="modal-subtitle">Review performance data</p>
                </div>
                <div class="recap-selects">
                    <select id="recapMonth" onchange="updateRecapChart()">
                        <?php 
                        for($m=1; $m<=12; $m++){ 
                            $selected = $m == date('m') ? 'selected' : '';
                            echo "<option value='$m' $selected>".date('F', mktime(0,0,0,$m, 1))."</option>";
                        } 
                        ?>
                    </select>
                    <select id="recapYear" onchange="updateRecapChart()">
                        <?php 
                        $curYear = date('Y');
                        for($y=$curYear; $y>=$curYear-2; $y--){ 
                            echo "<option value='$y'>$y</option>";
                        } 
                        ?>
                    </select>
                    <span class="close-modal" onclick="document.getElementById('recapModal').style.display='none'">&times;</span>
                </div>
            </div>

            <!-- MODAL CHARTS -->
            <div id="recapChartContainer">
                <canvas id="modalChartCanvas"></canvas>
            </div>
            
            <!-- MODAL TOTAL FOOTER -->
            <div class="recap-footer">
                 <span class="footer-label">Total for Selected Month</span>
                 <span id="modalTotalDisplay">-</span>
            </div>
        </div>
    </div>
</body>
</html>

