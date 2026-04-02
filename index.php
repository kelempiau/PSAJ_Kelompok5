<?php
require 'core/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$userPhone = '';
if ($isLoggedIn) {
    try {
        $stmt = $conn->prepare("SELECT phone FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $userPhone = $row['phone'];
        }
    } catch (Exception $e) {
        $userPhone = ''; 
    }
}



$servicesRes = $conn->query("SELECT * FROM services ORDER BY id ASC");
$featuresRes = $conn->query("SELECT * FROM why_choose_us ORDER BY id ASC");
$faqsRes = $conn->query("SELECT * FROM faq ORDER BY id ASC");


$settingsRes = $conn->query("SELECT * FROM studio_settings WHERE id = 1");
$settings = ($settingsRes && $settingsRes->num_rows > 0) ? $settingsRes->fetch_assoc() : [];
if (!$settings) $settings = []; 
$studioName = $settings['studio_name'] ?? 'Ney Dream Nail Art Studio';
$studioLogo = !empty($settings['studio_logo']) ? $settings['studio_logo'] : (!empty($settings['admin_profile_pic']) ? $settings['admin_profile_pic'] : 'assets/img/logo_default.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($studioName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Satisfy&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/neydream.png">
    <link rel="stylesheet" href="css/loading.css">
    <script defer src="js/loading.js"></script>
    <style>
        #sb98124, #sb98124_image, #sb98124_close, .tutup2,
        div[style*="position: fixed"][style*="z-index: 999999"],
        div[style*="position: absolute"][style*="z-index: 99999"],
        div[id^="sb"], div[class^="sb"],
        a[href*="infinityfree"], a[href*="epizy"],
        iframe[src*="ad"], iframe[id*="google_ads"],
        .disclaimer, center a[title*="Free Web Hosting"] {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            height: 0 !important;
            width: 0 !important;
            position: absolute !important;
            left: -9999px !important;
        }
    </style>
    <script>
        (function(){
            const cleanup = () => {
                const selectors = [
                    '#sb98124', '.tutup2', 'div[id^="sb"]', 
                    'a[href*="infinityfree"]', 'center a[title*="Hosting"]',
                    'iframe[src*="ad"]'
                ];
                selectors.forEach(s => {
                    document.querySelectorAll(s).forEach(el => {
                        try { el.remove(); } catch(e) {}
                    });
                });
                
                // Specifically target infinityfree fixed overlays that might block clicks
                // but EXCLUDE our own elements (id containing 'chat', 'Modal', or 'hamburger')
                document.querySelectorAll('div[style*="fixed"]').forEach(el => {
                    const style = el.getAttribute('style') || '';
                    if (style.includes('999999')) {
                        const id = el.id || '';
                        const cls = el.className || '';
                        if (!id.toLowerCase().includes('chat') && 
                            !id.toLowerCase().includes('modal') && 
                            !id.toLowerCase().includes('hamburger') &&
                            !cls.toLowerCase().includes('chat')) {
                            try { el.remove(); } catch(e) {}
                        }
                    }
                });
            };
            cleanup();
            setInterval(cleanup, 1500);
            window.addEventListener('load', cleanup);
        })();

        // Define toggle functions immediately in head-area to ensure they exist
        // Initialize chat/menu state early
        var isPopupOpen = false;
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            position: relative;
            scroll-behavior: smooth; /* Enable smooth scrolling */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fff;
            color: #333;
            padding-top: 90px; /* Offset for fixed navbar */
        }

        /* Navbar Reset & Premium Desktop Style */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 5000; /* Higher priority */
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 10px 5%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 90px;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 0 0 auto;
        }

        .nav-center {
            display: flex;
            gap: 30px;
            align-items: center;
            justify-content: center;
            flex: 2;
        }

        .nav-right {
            display: flex;
            gap: 15px;
            align-items: center;
            justify-content: flex-end;
            flex: 0 0 auto;
        }

        .logo {
            height: 40px;
            width: auto;
            object-fit: contain;
            transition: transform 0.3s;
        }
        @media (max-width: 768px) {
            .logo { height: 32px; }
        }
        .logo:hover { transform: scale(1.05); }

        .nav-center a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s;
            position: relative;
        }

        .nav-center a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ea3671;
            transition: width 0.3s;
        }

        .nav-center a:hover::after, .nav-center a.active::after {
            width: 100%;
        }

        .nav-center a:hover, .nav-center a.active {
            color: #ea3671;
            font-weight: 700;
        }

        /* Buttons */
        .btn {
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            cursor: pointer;
            border: none;
            text-align: center;
        }

        .btn-primary {
            background: #ff85a1;
            color: white;
            box-shadow: 0 5px 15px rgba(255, 133, 161, 0.3);
        }

        .btn-primary:hover {
            background: #ff5c8a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 133, 161, 0.4);
        }

        .btn-outline {
            border: 2px solid #ff85a1;
            color: #ff85a1;
            background: transparent;
        }

        .btn-outline:hover {
            background: #ff85a1;
            color: white;
        }

        .btn-logout {
            background: #fff0f3;
            color: #ff85a1;
            border: 1px solid #ffb7c5;
        }

        .btn-logout:hover {
            background: #ffe0e6;
        }

        /* mobileMenu hidden by default on desktop */
        #mobileMenu {
            display: none;
        }

        .btn-icon {
            padding: 10px;
            font-size: 1.2rem;
            line-height: 1;
            background: #ff85a1;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            color: white;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(255, 133, 161, 0.2);
        }

        .btn-icon:hover {
            transform: scale(1.1);
            background: #ff5c8a;
        }

        .btn-settings {
            text-decoration: none;
            font-size: 1.1rem;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 240, 245, 0.8);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 183, 197, 0.3);
            color: #ff85a1;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }

        .btn-settings:hover {
            transform: scale(1.1) rotate(60deg);
            background: rgba(255, 240, 245, 1);
            color: #ea3671;
            box-shadow: 0 6px 12px rgba(234, 54, 113, 0.15);
        }

        .user-profile-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 240, 245, 0.8);
            backdrop-filter: blur(5px);
            padding: 6px 16px 6px 6px;
            border-radius: 40px;
            border: 1px solid rgba(255, 183, 197, 0.3);
            margin-left: 15px;
            transition: all 0.3s;
            max-width: 150px;
            flex-shrink: 1;
            min-width: 0;
        }

        .user-profile-pill:hover {
            background: rgba(255, 240, 245, 1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 133, 161, 0.1);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #ff85a1, #ea3671);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 3px 8px rgba(234, 54, 113, 0.2);
            flex-shrink: 0;
        }

        .user-info-text {
            font-size: 0.95rem;
            color: #555;
            font-weight: 500;
        }

        .user-info-text strong {
            color: #ea3671;
            font-weight: 700;
        }

        /* Hamburger Styles */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            z-index: 10001;
            padding: 12px;
            pointer-events: auto !important;
            flex: 0 0 auto;
            border-radius: 8px;
            background: rgba(234, 54, 113, 0.05);
        }

        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background: #ff85a1;
            border-radius: 5px;
            transition: 0.3s;
        }

        /* Active Hamburger Animation */
        .hamburger.active span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

        /* Mobile Menu Optimization */
        @media (max-width: 992px) {
            .hamburger { display: flex !important; }
            
            #mobileMenu {
                display: flex !important;
                position: fixed !important;
                top: 0 !important;
                right: -320px;
                width: 300px;
                height: 100vh;
                height: 100dvh;
                background: #ffffff !important;
                flex-direction: column !important;
                padding: 100px 30px 40px !important;
                transition: right 0.4s ease-in-out !important;
                box-shadow: -10px 0 30px rgba(0,0,0,0.2) !important;
                z-index: 99999999 !important;
                gap: 25px;
                overflow-y: auto !important;
                visibility: hidden;
            }

            #mobileMenu.active { 
                right: 0 !important;
                visibility: visible !important;
            }

            .nav-center, .nav-right { display: none !important; }

            .mobile-link {
                text-decoration: none;
                color: #333;
                font-size: 1.2rem;
                font-weight: 500;
                padding-bottom: 5px;
                border-bottom: 1px solid #f0f0f0;
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.4s ease, transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .mobile-auth {
                margin-top: 20px;
                display: flex;
                flex-direction: column;
                gap: 15px;
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.4s ease, transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            #mobileMenu.active .mobile-link,
            #mobileMenu.active .mobile-auth {
                opacity: 1;
                transform: translateY(0);
            }

            /* Efek urut turun dari atas ke bawah / stagger */
            #mobileMenu.active .mobile-link:nth-child(1) { transition-delay: 0.1s; }
            #mobileMenu.active .mobile-link:nth-child(2) { transition-delay: 0.15s; }
            #mobileMenu.active .mobile-link:nth-child(3) { transition-delay: 0.2s; }
            #mobileMenu.active .mobile-link:nth-child(4) { transition-delay: 0.25s; }
            #mobileMenu.active .mobile-link:nth-child(5) { transition-delay: 0.3s; }
            #mobileMenu.active .mobile-link:nth-child(6) { transition-delay: 0.35s; }
            #mobileMenu.active .mobile-auth { transition-delay: 0.4s; }

            .mobile-link.active {
                color: #ea3671;
                font-weight: 700;
                border-bottom: 2px solid #ea3671;
            }
        }

        /* Hero Section */
        .hero {
            min-height: 90vh;
            background: linear-gradient(135deg, #ffd9e2 0%, #fff 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 50px 5%;
            gap: 50px;
            flex-wrap: wrap;
            overflow: hidden;
            image-rendering: -webkit-optimize-contrast; /* Fix blurriness */
        }

        .hero-content {
            flex: 1;
            min-width: 300px;
        }

        .hero-content h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            color: #333;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
        }

        .hero-images {
            flex: 1;
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: center;
            min-width: 300px;
        }

        .hero-images img {
            width: 200px;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: transform 0.3s;
        }

        .hero-images img:hover {
            transform: translateY(-10px);
        }

        /* Section Styling */
        section {
            padding: 80px 5%;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            color: #5f162e;
            margin-bottom: 10px;
        }

        .section-header .underline {
            width: 100px;
            height: 4px;
            background: #e91e63;
            margin: 0 auto;
            border-radius: 2px;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            image-rendering: -webkit-optimize-contrast;
        }

        .service-card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .service-card h3 {
            color: #ea3671;
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .service-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.6;
            flex-grow: 1; /* Ensure text takes up available space, but price stays at bottom */
        }

        .service-card .price {
            font-weight: 700;
            color: #333;
            font-size: 1.1rem;
            margin-top: auto;
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gallery-grid img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            image-rendering: auto; /* Ensure high quality */
        }

        .gallery-grid img:hover {
            transform: scale(1.05);
        }

        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card .icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            color: #5f162e;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Background Sections */
        .bg-pink {
            background: linear-gradient(135deg, #ffd9e2 0%, #ffe6f0 100%);
        }

        /* Hero Section Premium Styles */
        .hero {
            position: relative;
            overflow: hidden;
            height: 100vh;
            display: flex;
            align-items: center;
            padding: 0;
            background: none !important; /* Managed by dividers */
        }

        .hero::before { /* Mobile Background */
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, #ffd9e2 0%, #fff 100%);
            display: none;
            z-index: 0;
        }

        @media (max-width: 768px) {
            .hero {
                flex-direction: column; 
                text-align: center;
                height: auto;
                min-height: 80vh;
                padding: 120px 20px 60px;
            }
            .hero::before { display: block; }
            .hero > div[style*="background: #fdfbfd"] { display: none; }
            .hero > div[style*="background: linear-gradient"] { display: none; }
            
            .hero-content {
                padding: 0 !important;
                margin-top: 40px;
                width: 100%;
            }
            .hero-content h2 {
                font-size: clamp(1.8rem, 8vw, 2.5rem) !important;
            }
            .hero-content h2 span:nth-child(2) {
                font-size: clamp(2.2rem, 10vw, 3.2rem) !important;
            }
            .hero-images {
                display: none !important; /* Hide images on mobile as they clutter the space */
            }
        }
        .dots-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            position: absolute;
            z-index: 0;
            opacity: 0.6;
        }

        .dots-grid div {
            width: 6px;
            height: 6px;
            background: #ea3671;
            border-radius: 50%;
        }

        /* Animations & Shapes */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .deco-shape {
            position: absolute;
            z-index: 0;
            opacity: 0.1;
            pointer-events: none;
        }

        .float-fast { animation: float 4s ease-in-out infinite; }
        .float-slow { animation: float 7s ease-in-out infinite; }
        .spin-slow { animation: rotate 25s linear infinite; }

        .sparkle {
            position: absolute;
            color: #ea3671;
            font-size: 1.5rem;
            z-index: 0;
            opacity: 0.6;
            animation: float 5s ease-in-out infinite;
            pointer-events: none; /* DO NOT BLOCK CLICKS */
        }

        /* Section Separator removed shadow as requested */
        .hero {
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>
    <?php include 'includes/loading.php'; ?>
    
    <nav>
        <!-- nav-left: user profile (desktop) -->
        <div class="nav-left" style="display: flex; align-items: center; gap: 15px;">
            <?php if ($isLoggedIn): 
                $uData = ['profile_pic' => null];
                $colCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_pic'");
                if ($colCheck && $colCheck->num_rows > 0) {
                    $q = $conn->query("SELECT profile_pic FROM users WHERE id = ".$_SESSION['user_id']);
                    if ($q && ($row = $q->fetch_assoc())) $uData = $row;
                }
            ?>
                <div class="user-profile-pill" style="padding: 4px 12px; height: 36px; gap: 8px; flex-shrink: 1; min-width: 0;">
                    <div class="user-avatar" style="width: 28px; height: 28px; font-size: 12px; overflow: hidden; flex-shrink: 0;">
                        <?php $initial = strtoupper(substr($username, 0, 1)); ?>
                        <?= $initial ?>
                    </div>
                    <div class="user-info-text" style="font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex-shrink: 1;">
                        Hi, <strong><?= htmlspecialchars($username) ?></strong>!
                    </div>
                </div>
                <a href="user/settings.php" class="btn-settings" title="Pengaturan Akun" style="text-decoration: none; margin-left: 5px; flex-shrink: 0;">⚙️</a>
            <?php endif; ?>
        </div>

        <script>
            function checkStudioStatus(e, targetUrl) {
                const isClosed = <?= ($settings['studio_status'] ?? 1) == 0 ? 'true' : 'false' ?>;
                const isAdmin = <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false' ?>;
                
                if (isClosed && !isAdmin) {
                    e.preventDefault();
                    const modal = document.getElementById('studioClosedModal');
                    if (modal) {
                        modal.style.display = 'flex';
                    } else {
                        alert('📢 Studio Sedang Libur\n\nMaaf Kak, saat ini kami sedang libur. Silakan hubungi kami via Chat atau coba kembali besok ya! ✨');
                    }
                    return false;
                }
                window.location.href = targetUrl;
            }
        </script>

        <div class="nav-center">
            <a href="#home">Beranda</a>
            <a href="#layanan">Layanan</a>
            <a href="#lokasi">Lokasi</a>
            <a href="#katalog">Katalog</a>
            <a href="#faq">FAQ</a>
        </div>
        
        <div class="nav-right">
            <?php if ($isLoggedIn): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="javascript:void(0)" onclick="confirmLogout('auth/logout.php')" class="btn btn-logout">Logout</a>
                <?php else: ?>
                    <a href="user/history.php" class="btn btn-icon" title="Riwayat Transaksi">📋</a>
                    <a href="javascript:void(0)" onclick="checkStudioStatus(event, 'user/reservasi.php')" class="btn btn-primary">Reservasi</a>
                    <a href="javascript:void(0)" onclick="confirmLogout('auth/logout.php')" class="btn btn-logout">Logout</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-outline">Log In</a>
                <a href="auth/register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>

        <!-- Hamburger - hanya tampil di mobile/tablet -->
        <div class="hamburger" id="hamburger" onclick="window.toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- Mobile Menu (di luar nav agar tidak terpengaruh flex nav) -->
    <div id="mobileMenu" style="opacity: 1 !important; pointer-events: auto !important;">
        <a href="#home" class="mobile-link">Beranda</a>
        <a href="#layanan" class="mobile-link">Layanan</a>
        <a href="#lokasi" class="mobile-link">Lokasi</a>
        <a href="#katalog" class="mobile-link">Katalog</a>
        <a href="#faq" class="mobile-link">FAQ</a>
        <?php if ($isLoggedIn): ?>
            <a href="user/settings.php" class="mobile-link" style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 1.1rem;">⚙️</span> Pengaturan Akun
            </a>
        <?php endif; ?>
        <div class="mobile-auth">
            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="javascript:void(0)" onclick="window.confirmLogout('auth/logout.php')" class="btn btn-logout">Logout</a>
                <?php else: ?>
                    <a href="user/history.php" class="btn btn-outline" style="display:flex;align-items:center;justify-content:center;gap:10px;">
                        <span style="font-size:1.3rem;">📋</span> Riwayat Transaksi
                    </a>
                    <a href="javascript:void(0)" onclick="checkStudioStatus(event,'user/reservasi.php')" class="btn btn-primary">Booking Sekarang</a>
                    <a href="javascript:void(0)" onclick="window.confirmLogout('auth/logout.php')" class="btn btn-logout">Logout</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-outline">Log In</a>
                <a href="auth/register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

    
    <section class="hero" id="home" style="min-height: 95vh; background: linear-gradient(to bottom, #ffc0cb 0%, #ffffff 50%, #ffc0cb 100%); position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 0;">
        
        
        <div style="position: absolute; top: 10%; left: 5%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(234, 54, 113, 0.08) 0%, transparent 70%); z-index: 0;"></div>
        <div style="position: absolute; bottom: 5%; right: 5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255, 133, 161, 0.1) 0%, transparent 70%); z-index: 0;"></div>

        <div style="max-width: 1300px; width: 100%; display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 10; padding: 0 5%;">
            
            
            <div class="hero-content" style="flex: 1; padding-right: 50px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                    <div style="width: 40px; height: 1px; background: #ea3671;"></div>
                    <span style="text-transform: uppercase; letter-spacing: 5px; font-size: 0.85rem; color: #ea3671; font-weight: 700;">Exclusive Nail Art Studio</span>
                </div>
                
                <h1 style="font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800; color: #333; line-height: 1.1; margin-bottom: 25px; letter-spacing: -1px;">
                    Kembalikan <span style="color: #ea3671;">Kilau</span> <br>
                    Alami Jari Anda
                </h1>

                <p style="font-size: 1.1rem; line-height: 1.6; color: #666; margin-bottom: 45px; max-width: 500px;">
                    Wujudkan tampilan kuku yang elegan dan profesional bersama kami. <br>
                    <span style="font-family: 'Satisfy', cursive; color: #ea3671; font-size: 1.8rem;">Sentuhan seni untuk setiap momen spesial Anda.</span>
                </p>

                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <a href="javascript:void(0)" 
                       onclick="checkStudioStatus(event, '<?= $isLoggedIn ? ($isAdmin ? 'admin/dashboard.php' : 'user/reservasi.php') : 'auth/login.php' ?>')"
                       class="btn btn-primary" 
                       style="padding: 18px 45px; font-size: 1.05rem; border-radius: 50px; background: #ea3671; color: #fff; box-shadow: 0 10px 30px rgba(234, 54, 113, 0.2); transition: all 0.3s ease; text-decoration: none;">
                        Mulai Reservasi
                    </a>
                </div>

                
                <div style="margin-top: 50px; display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 1.3rem; color: #ea3671; font-weight: 600; font-family: 'Satisfy', cursive;">Kecantikan Sejati Dimulai dari Sini...</span>
                </div>
            </div>

            
            <div class="hero-visual" style="flex: 1; position: relative; height: 600px; display: flex; align-items: center; justify-content: center;">
                <div style="position: relative; width: 100%; height: 100%;">
                    
                    
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-40%, -50%); width: 70%; height: 75%; overflow: hidden; border-radius: 180px 180px 40px 40px; border: 12px solid #fff; box-shadow: 0 30px 60px rgba(0,0,0,0.12); z-index: 2;">
                        <img src="assets/img/img8.jpeg" 
                             style="width: 100%; height: 100%; object-fit: cover;" alt="Luxury Nail Polish">
                    </div>

                    
                    <div style="position: absolute; bottom: 8%; left: -8%; width: 240px; height: 300px; overflow: hidden; border-radius: 30px; border: 10px solid #fff; box-shadow: 0 20px 45px rgba(0,0,0,0.15); z-index: 3; transform: rotate(-12deg);">
                        <img src="assets/img/img10.jpeg" 
                             style="width: 100%; height: 100%; object-fit: cover;" alt="Premium Nail Art">
                    </div>

                    
                    <div class="float-slow" style="position: absolute; top: 10%; right: 5%; width: 120px; height: 120px; background: rgba(234, 54, 113, 0.12); border-radius: 50%; backdrop-filter: blur(8px); z-index: 1;"></div>
                    <div class="float-fast" style="position: absolute; bottom: 35%; right: 15%; color: #ea3671; font-size: 3rem; z-index: 4;">✨</div>
                    
                    
                    <div style="position: absolute; top: 20%; right: -25px; background: #fff; padding: 18px 30px; border-radius: 20px; box-shadow: 0 15px 35px rgba(234,54,113,0.1); z-index: 5; border: 1px solid rgba(255,133,161,0.2); text-align: center;">
                        <div style="font-weight: 800; color: #ea3671; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px;">Dikerjakan oleh</div>
                        <div style="font-size: 1.2rem; color: #333; font-weight: 700;">Ahlinya</div>
                    </div>
                </div>
            </div>

        </div>

        
        <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; gap: 10px; opacity: 0.6;">
            <div style="width: 1px; height: 50px; background: #ea3671;"></div>
            <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 4px; color: #ea3671;">Explore</span>
        </div>
    </section>

    <style>
        @media (max-width: 991px) {
            .hero {
                min-height: auto;
                padding: 130px 24px 100px;
                background: linear-gradient(to bottom, #ffc0cb 0%, #ffffff 50%, #ffc0cb 100%) !important;
            }
            .hero > div:first-of-type {
                flex-direction: column;
                text-align: center;
                justify-content: center;
            }
            .hero-content {
                padding-right: 0 !important;
                margin-bottom: 0;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .hero-content h1 {
                font-size: clamp(2.5rem, 8vw, 3.5rem) !important;
                margin-bottom: 20px !important;
            }
            .hero-content p {
                margin: 0 auto 40px !important;
            }
            .hero-visual {
                display: none !important;
            }
            .hero-content .btn {
                width: 100%;
                max-width: 320px;
            }
            .hero-content div[style*="margin-top: 50px"] {
                margin-top: 45px !important;
                justify-content: center;
            }
        }
    </style>

    
    <section id="layanan" style="position: relative; overflow: hidden; background: linear-gradient(180deg, #ffd9e2 0%, #fff 100%);">
         
         <div class="dots-grid" style="top: 40px; right: 20px; grid-template-columns: repeat(4, 1fr);">
            <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
        </div>
        <div class="dots-grid" style="top: 150px; left: 10%; opacity: 0.4;">
            <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
        </div>
        
        <div class="deco-shape float-fast" style="top: 100px; left: -30px; width: 100px; height: 100px; border: 15px solid rgba(234, 54, 113, 0.1); border-radius: 50%;"></div>
        <div class="sparkle" style="top: 60px; right: 30%;">✨</div>
        <div class="sparkle" style="bottom: 100px; left: 5%;">✨</div>
        <div class="deco-shape spin-slow" style="bottom: -50px; right: 100px; width: 150px; height: 150px; border: 4px dashed rgba(234, 54, 113, 0.2); border-radius: 50%;"></div>

        <div class="section-header" style="position: relative; z-index: 1;">
            <h2>Layanan Kami</h2>
            <div class="underline"></div>
        </div>

        <div class="services-grid" style="position: relative; z-index: 1;">
            <?php if($servicesRes && $servicesRes->num_rows > 0): ?>
                <?php while($s = $servicesRes->fetch_assoc()): ?>
                <a href="services/service_detail.php?id=<?= $s['id'] ?>" class="service-card">
                    <img src="<?= $s['image_path'] ?>" alt="<?= htmlspecialchars($s['name']) ?>" onerror="this.src='https://via.placeholder.com/250x200/ea3671/ffffff?text=Service'">
                    <div class="service-card-content">
                        <h3><?= htmlspecialchars($s['name']) ?></h3>
                        <p><?= htmlspecialchars($s['description']) ?></p>
                        <div class="price">Mulai Rp <?= number_format($s['price_start'], 0, ',', '.') ?></div>
                    </div>
                </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; grid-column: 1/-1; color: #666;">Layanan akan segera hadir!</p>
            <?php endif; ?>
        </div>
    </section>

    
    <section id="lokasi" style="padding: 60px 5%; background: #fff;">
        <div class="section-header" style="margin-bottom: 40px;">
            <h2>Lokasi Neydream</h2>
            <div class="underline"></div>
        </div>
        
        <div style="max-width: 1200px; margin: 0 auto; border-radius: 30px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border: 8px solid white; background: #f0f0f0; position: relative; padding-bottom: 56.25%; height: 0;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1183.1!2d109.2372!3d-7.3716!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e655f75cf4500dd%3A0x11758038b45defc1!2sNeydream%20Studio!5e0!3m2!1sen!2sid!4v1711234567890!5m2!1sen!2sid" 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border:0;" 
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

    </section>

    
    <section id="katalog" style="position: relative; overflow: hidden;">
        
        <div class="dots-grid" style="bottom: 40px; left: 20px;">
            <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
        </div>
        <div class="dots-grid" style="top: 40px; right: 5%; grid-template-columns: repeat(2, 1fr);">
            <div></div><div></div><div></div><div></div><div></div><div></div>
        </div>
        
        <div class="deco-shape float-slow" style="top: 50%; right: -20px; width: 80px; height: 80px; background: #fff0f5; border-radius: 50%;"></div>
        <div class="sparkle" style="top: 80px; left: 20%;">✨</div>

        <div class="section-header" style="position: relative; z-index: 1;">
            <h2>Galeri Hasil Kami</h2>
            <div class="underline"></div>
        </div>

        <div class="gallery-grid" style="position: relative; z-index: 1;">
            <img src="assets/img/img8.jpeg" alt="Gallery" onerror="this.src='https://via.placeholder.com/400x350/ea3671/ffffff?text=Gallery+1'">
            <img src="assets/img/img9.jpeg" alt="Gallery" onerror="this.src='https://via.placeholder.com/400x350/ffd9e2/333333?text=Gallery+2'">
            <img src="assets/img/img10.jpeg" alt="Gallery" onerror="this.src='https://via.placeholder.com/400x350/ea3671/ffffff?text=Gallery+3'">
        </div>
    </section>

    
    <section class="bg-pink" style="position: relative; overflow: hidden;">
        
        <div style="position: absolute; top: 20px; right: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
            <div style="width: 8px; height: 8px; background: #ea3671; border-radius: 50%;"></div>
            <div style="width: 8px; height: 8px; background: #ea3671; border-radius: 50%;"></div>
            <div style="width: 8px; height: 8px; background: #ea3671; border-radius: 50%;"></div>
            <div style="width: 8px; height: 8px; background: #ea3671; border-radius: 50%;"></div>
            <div style="width: 8px; height: 8px; background: #ea3671; border-radius: 50%;"></div>
            <div style="width: 8px; height: 8px; background: #ea3671; border-radius: 50%;"></div>
        </div>

        <div class="section-header">
            <h2>Kenapa kamu harus memilih Neydream? 🤔</h2>
            <div class="underline"></div>
        </div>

        <div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 40px;">
            <?php if($featuresRes && $featuresRes->num_rows > 0): ?>
                <?php $i = 0; while($f = $featuresRes->fetch_assoc()): $i++; ?>
                <div class="feature-card-wide" style="position: relative;">
                    <div style="position: absolute; <?= $i % 2 === 0 ? 'top: -20px; right: -30px;' : 'top: -30px; left: -30px;' ?> font-size: 4rem; z-index: 1; transform: <?= $i % 2 === 0 ? 'rotate(15deg)' : '' ?>;"><?= $f['icon'] ?></div>
                    <h3><?= htmlspecialchars($f['title']) ?></h3>
                    <p>" <?= htmlspecialchars($f['description']) ?> "</p>
                    
                    
                    <div style="position: absolute; <?= $i % 2 === 0 ? 'top: -15px; right: 80px;' : 'bottom: -10px; left: 20px;' ?> display: <?= $i % 2 === 0 ? 'grid' : 'flex' ?>; grid-template-columns: repeat(2, 1fr); gap: 5px;">
                        <div style="width: 6px; height: 6px; background: #ea3671; border-radius: 50%;"></div>
                        <div style="width: 6px; height: 6px; background: #ea3671; border-radius: 50%;"></div>
                        <div style="width: 6px; height: 6px; background: #ea3671; border-radius: 50%;"></div>
                        <div style="width: 6px; height: 6px; background: #ea3671; border-radius: 50%;"></div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        
        <style>
            .feature-card-wide {
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.05);
                text-align: center;
                transition: transform 0.3s;
                position: relative;
                z-index: 1;
            }
            .feature-card-wide:hover {
                transform: translateY(-5px);
            }
            .feature-card-wide h3 {
                color: #5f162e;
                font-size: 1.8rem;
                margin-bottom: 15px;
                font-weight: 700;
            }
            .feature-card-wide p {
                color: #555;
                font-size: 1rem;
                line-height: 1.8;
                max-width: 90%;
                margin: 0 auto;
            }
        </style>
    </section>

    
    
    <section id="faq" style="padding: 80px 5%; position: relative; overflow: hidden; background: linear-gradient(180deg, #ffd9e2 0%, #fff 100%);">
        <div class="section-header">
            <h2>Frequency Asked Questions (FAQ)</h2>
            <p style="color: #666; margin-top: 10px;">Punya pertanyaan? Cari jawabannya di sini!</p>
            <div class="underline"></div>
        </div>

        <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px; position: relative; z-index: 1;">
            <?php if($faqsRes && $faqsRes->num_rows > 0): ?>
                <?php while($f = $faqsRes->fetch_assoc()): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <span><?= htmlspecialchars($f['question']) ?></span>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars($f['answer']) ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; color: #666;">Belum ada pertanyaan yang diajukan.</p>
            <?php endif; ?>
        </div>

        <style>
            .faq-item {
                background: white;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                transition: all 0.3s;
            }
            .faq-question {
                padding: 20px 25px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                cursor: pointer;
                font-weight: 600;
                color: #5f162e;
                transition: background 0.3s;
            }
            .faq-question:hover {
                background: #fffafa;
            }
            .faq-answer {
                padding: 0 25px;
                max-height: 0;
                overflow: hidden;
                transition: all 0.3s ease-out;
                color: #666;
                line-height: 1.6;
            }
            .faq-item.active .faq-answer {
                padding: 0 25px 20px 25px;
                max-height: 200px;
            }
            .faq-item.active .faq-icon {
                transform: rotate(45deg);
                color: #ea3671;
            }
            .faq-icon {
                font-size: 1.5rem;
                transition: transform 0.3s;
                color: #999;
            }
        </style>

        <script>
            document.querySelectorAll('.faq-question').forEach(q => {
                q.addEventListener('click', () => {
                    const item = q.parentElement;
                    item.classList.toggle('active');
                });
            });
        </script>
    </section>

    
    <section class="final-cta" style="padding: 100px 5%; background: white; text-align: center; position: relative; overflow: hidden;">
        
        <div class="deco-shape float-fast" style="top: -50px; left: 10%; width: 150px; height: 150px; border: 20px solid #fff0f5; border-radius: 50%;"></div>
        <div class="deco-shape spin-slow" style="bottom: -50px; right: 10%; width: 200px; height: 200px; border: 2px dashed #ea3671; border-radius: 50%; opacity: 0.1;"></div>
        
        <div style="max-width: 800px; margin: 0 auto; position: relative; z-index: 1;">
            <h2 style="font-size: 2.8rem; color: #5f162e; margin-bottom: 20px; font-weight: 700;">Jadilah Bagian dari Keluarga Neydream! ✨</h2>
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 40px; line-height: 1.8;">
                Dapatkan info promo spesial, kemudahan booking, dan pantau riwayat cantikmu hanya dalam satu akun. Tunggu apa lagi? Daftar sekarang juga!
            </p>
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <?php if (!$isLoggedIn): ?>
                    <a href="auth/register.php" class="btn btn-primary" style="padding: 18px 50px; font-size: 1.2rem; border-radius: 50px; box-shadow: 0 10px 25px rgba(234, 54, 113, 0.4);">
                        Daftar Akun Sekarang
                    </a>
                <?php else: ?>
                    <a href="<?php echo $isAdmin ? 'admin/dashboard.php' : 'user/reservasi.php'; ?>" class="btn btn-primary" style="padding: 18px 50px; font-size: 1.2rem; border-radius: 50px; box-shadow: 0 10px 25px rgba(234, 54, 113, 0.4);">
                        Booking Sekarang
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <section id="feedback" style="padding: 80px 5%; background: #fffafb;">
        <div class="section-header" style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2.5rem; color: #5f162e; font-weight: 700;">Kritik & Saran 📝</h2>
            <p style="color: #666; margin-top: 10px;">Masukan Anda sangat berharga bagi peningkatan layanan kami.</p>
            <div style="width: 80px; height: 4px; background: #ea3671; margin: 20px auto; border-radius: 2px;"></div>
        </div>

        <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <?php if ($isLoggedIn): ?>
                <form action="actions/submit_feedback.php" method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #5f162e;">Nama Lengkap</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($username) ?>" readonly placeholder="Masukkan nama Anda" required style="width: 100%; padding: 12px 15px; border: 2px solid #f9f9f9; border-radius: 10px; font-family: 'Poppins', sans-serif; background: #fdfdfd;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #5f162e;">Nomor WhatsApp</label>
                        <input type="text" name="whatsapp_number" value="<?= htmlspecialchars($userPhone) ?>" readonly placeholder="Contoh: 08123456789" required style="width: 100%; padding: 12px 15px; border: 2px solid #f9f9f9; border-radius: 10px; font-family: 'Poppins', sans-serif; background: #fdfdfd; cursor: not-allowed;">
                    </div>
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #5f162e;">Pesan Kritik / Saran</label>
                        <textarea name="message" rows="5" placeholder="Tuliskan masukan Anda di sini..." required style="width: 100%; padding: 12px 15px; border: 2px solid #f0f0f0; border-radius: 10px; font-family: 'Poppins', sans-serif; resize: none;"></textarea>
                    </div>
                    <button type="submit" style="width: 100%; background: #ea3671; color: white; padding: 15px; border: none; border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.3s; box-shadow: 0 5px 15px rgba(234, 54, 113, 0.2);">
                        Kirim Masukan ✨
                    </button>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 20px 0;">
                    <p style="margin-bottom: 20px; color: #666;">Anda harus login terlebih dahulu untuk mengirim kritik dan saran.</p>
                    <a href="auth/login.php" class="btn btn-primary" style="display: inline-block; padding: 12px 35px; border-radius: 30px;">Login Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
    // --- CORE LOGIC & NAVIGATION ---
    window.isPopupOpen = false;
    window.currentConvId = null;

    // Heartbeat
    async function customerHeartbeat() {
        try { await fetch('api/user/heartbeat.php'); } catch (e) {}
    }
    setInterval(customerHeartbeat, 30000);
    customerHeartbeat();

    // Logout Confirmation
    window.confirmLogout = function(logoutUrl) {
        const modal = document.getElementById('logoutModal');
        if (modal) {
            modal.dataset.logoutUrl = logoutUrl;
            modal.style.display = 'flex';
        } else {
            if (confirm("Apakah Anda yakin ingin logout?")) window.location.href = logoutUrl;
        }
    };

    window.handleLogoutConfirm = function(confirmed) {
        const modal = document.getElementById('logoutModal');
        if (confirmed && modal && modal.dataset.logoutUrl) {
            window.location.href = modal.dataset.logoutUrl;
        } else if (modal) {
            modal.style.display = 'none';
        }
    };

    // Mobile Navigation
    window.initMenuOverlay = function() {
        let ov = document.getElementById('mobileMenuOverlay');
        if (ov) return ov;
        ov = document.createElement('div');
        ov.id = 'mobileMenuOverlay';
        ov.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter:blur(4px); z-index:15000; display:none; opacity:0; transition: opacity 0.3s; cursor:pointer;";
        document.body.appendChild(ov);
        ov.addEventListener('click', window.toggleMobileMenu);
        return ov;
    };

    window.toggleMobileMenu = function() {
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        const ov = window.initMenuOverlay();
        
        if (hamburger && mobileMenu) {
            const isActive = mobileMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
            
            if (isActive) {
                ov.style.display = 'block';
                setTimeout(() => ov.style.opacity = '1', 10);
                document.body.style.overflow = 'hidden';
            } else {
                ov.style.opacity = '0';
                setTimeout(() => {
                    ov.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }
        }
    };

    // Chat Popup toggle - dihandle oleh includes/chat_popup.php
    // Fallback jika chat_popup tidak tersedia (pengguna belum login)
    if (typeof window.toggleChatPopup === 'undefined') {
        window.toggleChatPopup = function() {};
    }


    document.addEventListener('DOMContentLoaded', () => {
        // Handle mobile links
        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', () => {
                const mobileMenu = document.getElementById('mobileMenu');
                if (mobileMenu && mobileMenu.classList.contains('active')) window.toggleMobileMenu();
            });
        });

        // Update active nav links on scroll
        function updateActiveLink() {
            const scrollPos = window.scrollY + 100;
            const sections = document.querySelectorAll('section[id]');
            const links = document.querySelectorAll('.nav-center a, .mobile-link');
            
            let found = false;
            for (let i = sections.length - 1; i >= 0; i--) {
                const section = sections[i];
                if (scrollPos >= section.offsetTop) {
                    const id = section.getAttribute('id');
                    links.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + id) link.classList.add('active');
                    });
                    found = true;
                    break;
                }
            }
            if (!found || window.scrollY < 100) {
                links.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#home') link.classList.add('active');
                });
            }
        }

        window.addEventListener('scroll', updateActiveLink);
        window.addEventListener('load', updateActiveLink);

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href.length > 1) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - 90,
                            behavior: 'smooth'
                        });
                        setTimeout(updateActiveLink, 800);
                    }
                }
            });
        });
    });
    </script>

    <!-- Footer Section -->
    <footer style="background: #fff; padding: 70px 5% 40px; border-top: 1px solid #f9f9f9;">
        <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px;">
            <!-- Column 1: Alamat -->
            <div>
                <h4 style="color: #5f162e; font-weight: 800; margin-bottom: 20px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1.5px; border-bottom: 2px solid #ea3671; display: inline-block; padding-bottom: 5px;">Alamat Studio</h4>
                <?php 
                    $fullAddr = (!empty($settings['full_address'])) ? $settings['full_address'] : "Dusun Sakawera, Rempoah, Baturaden, Banyumas Regency, Central Java 53126";
                ?>
                <p style="color: #4b5563; font-size: 0.95rem; line-height: 1.7;"><?= htmlspecialchars($fullAddr) ?></p>
            </div>

            <!-- Column 2: Layanan -->
            <div>
                <h4 style="color: #5f162e; font-weight: 800; margin-bottom: 25px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1.5px; border-bottom: 2px solid #ea3671; display: inline-block; padding-bottom: 5px;">Layanan Utama</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 12px;"><a href="#layanan" style="text-decoration: none; color: #64748b; font-size: 0.95rem;">Nail Art Design</a></li>
                    <li style="margin-bottom: 12px;"><a href="#layanan" style="text-decoration: none; color: #64748b; font-size: 0.95rem;">Acrylic Extension</a></li>
                </ul>
            </div>

            <!-- Column 3: Sosmed -->
            <div>
                <h4 style="color: #5f162e; font-weight: 800; margin-bottom: 25px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1.5px; border-bottom: 2px solid #ea3671; display: inline-block; padding-bottom: 5px;">Hubungi Kami</h4>
                <div style="display: flex; flex-direction: column; gap: 18px;">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['whatsapp'] ?? '628123456789') ?>" target="_blank" style="display: flex; align-items: center; gap: 12px; text-decoration: none; group">
                        <div style="width: 35px; height: 35px; background: #25d366; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 10px rgba(37, 211, 102, 0.2);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        </div>
                        <span style="color: #25d366; font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($settings['whatsapp'] ?? '08123456789') ?></span>
                    </a>
                    <a href="https://instagram.com/<?= htmlspecialchars($settings['instagram'] ?? 'neydream.studio') ?>" target="_blank" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                        <div style="width: 35px; height: 35px; background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 10px rgba(220, 39, 67, 0.2);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.805.249 2.227.412.558.217.957.477 1.377.896.419.42.679.819.896 1.377.164.422.358 1.057.412 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.249 1.805-.412 2.227-.217.558-.477.957-.896 1.377-.42.419-.819.679-1.377.896-.422.164-1.057.358-2.227.412-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.805-.249-2.227-.412-.558-.217-.957-.477-1.377-.896-.42-.42-.679-.819-.896-1.377-.164-.422-.358-1.057-.412-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.17.249-1.805.412-2.227.217-.558.477-.957.896-1.377.42-.419.819-.679 1.377-.896.422-.164 1.057-.358 2.227-.412 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-1.277.059-2.148.262-2.911.559-.788.306-1.457.715-2.122 1.38-.665.665-1.074 1.334-1.38 2.122-.297.763-.5 1.634-.559 2.911-.058 1.28-.072 1.688-.072 4.947s.014 3.667.072 4.947c.059 1.277.262 2.148.559 2.911.306.788.715 1.457 1.38 2.122.665.665 1.334 1.074 2.122 1.38.763.297 1.634.5 2.911.559 1.28.058 1.688.072 4.947.072s3.667-.014 4.947-.072c1.277-.059 2.148-.262 2.911-.559.788-.306 1.457-.715 2.122-1.38.665-.665 1.074-1.334 1.38-2.122.297-.763.5-1.634.559-2.911.058-1.28.072-1.688.072-4.947s-.014-3.667-.072-4.947c-.059-1.277-.262-2.148-.559-2.911-.306-.788-.715-1.457-1.38-2.122-.665-.665-1.334-1.074-2.122-1.38-.763-.297-1.634-.5-2.911-.559-1.28-.058-1.688-.072-4.947-.072zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.162 6.162 6.162 6.162-2.759 6.162-6.162-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm6.406-11.845c.796 0 1.441.645 1.441 1.44s-.645 1.44-1.441 1.44-1.44-.645-1.44-1.44.645-1.44 1.44-1.44z"/></svg>
                        </div>
                        <?php 
                            $igHandle = $settings['instagram'] ?? 'neydream.studio';
                            $displayIG = (strpos($igHandle, '@') === 0) ? $igHandle : '@' . $igHandle;
                        ?>
                        <span style="color: #e1306c; font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($displayIG) ?></span>
                    </a>
                </div>
            </div>
        </div>
        <div style="text-align: center; margin-top: 50px; padding-top: 25px; border-top: 1px solid #f1f5f9; color: #94a3b8; font-size: 0.85rem;">
            &copy; <?= date('Y') ?> Neydream Studio. All rights reserved.
        </div>
    </footer>
    
    <?php include 'includes/chat_popup.php'; ?>

    <!-- Modals -->
    <div id="logoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 100000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div style="background: white; padding: 40px; border-radius: 30px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
            <div style="font-size: 4rem; margin-bottom: 20px;">👋</div>
            <h3 style="color:#5f162e; margin-bottom: 15px; font-weight:700;">Yakin mau keluar?</h3>
            <p style="color:#666; margin-bottom: 30px;">Sampai jumpa lagi di Neydream Studio ya Kak! ✨</p>
            <div style="display: flex; gap: 15px;">
                <button onclick="window.handleLogoutConfirm(false)" style="flex:1; padding:15px; border-radius:15px; border:1px solid #ddd; background:white; cursor:pointer;">Tidak</button>
                <button onclick="window.handleLogoutConfirm(true)" style="flex:1; padding:15px; border-radius:15px; background:#ea3671; color:white; border:none; font-weight:700; cursor:pointer;">Ya, Logout</button>
            </div>
        </div>
    </div>

    <script src="assets/js/home.js?v=<?= time() ?>"></script>
</body>
</html>
    
    
    <script>
    </script>
</body>
</html>
