<?php
require 'core/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ney Dream Nail Art Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Satisfy&display=swap" rel="stylesheet">
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
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: 
            color: 
            padding-top: 90px; 
        }


        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 2000; 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 10px 5%;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 90px;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
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
            flex: 1;
        }

        .logo {
            height: 70px;
            width: auto;
            transition: transform 0.3s;
        }
        
        .logo:hover { transform: scale(1.05); }

        .nav-center a {
            text-decoration: none;
            color: 
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
            background: 
            transition: width 0.3s;
        }

        .nav-center a:hover::after, .nav-center a.active::after {
            width: 100%;
        }

        .nav-center a:hover, .nav-center a.active {
            color: 
            font-weight: 700;
        }


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
            background: 
            color: white;
            box-shadow: 0 5px 15px rgba(255, 133, 161, 0.3);
        }

        .btn-primary:hover {
            background: 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 133, 161, 0.4);
        }

        .btn-outline {
            border: 2px solid 
            color: 
            background: transparent;
        }

        .btn-outline:hover {
            background: 
            color: white;
        }

        .btn-logout {
            background: 
            color: 
            border: 1px solid 
        }

        .btn-logout:hover {
            background: 
        }

        
            display: none;
        }

        .btn-icon {
            padding: 10px;
            font-size: 1.2rem;
            line-height: 1;
            background: 
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
            background: 
        }


        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            z-index: 2000;
            padding: 10px;
        }

        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background: 
            border-radius: 5px;
            transition: 0.3s;
        }


        .hamburger.active span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

        
        @media (max-width: 992px) {
            .hamburger { display: flex; }
            
            
                position: fixed;
                top: 0;
                right: 0;
                width: 300px;
                height: 100vh;
                background: white;
                flex-direction: column;
                padding: 100px 30px;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: -10px 0 30px rgba(0,0,0,0.1);
                z-index: 1700;
                display: flex;
                gap: 25px;
                transform: translateX(100%);
                visibility: hidden;
            }

            
                transform: translateX(0);
                visibility: visible;
            }

            .nav-center, .nav-right { display: none; } 

            .mobile-link {
                text-decoration: none;
                color: 
                font-size: 1.2rem;
                font-weight: 500;
                padding-bottom: 5px;
                border-bottom: 1px solid 
            }

            .mobile-link.active {
                color: 
                font-weight: 700;
                border-bottom: 2px solid 
            }

            .mobile-auth {
                margin-top: 20px;
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
        }

        
        .hero {
            min-height: 90vh;
            background: linear-gradient(135deg, 
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 50px 5%;
            gap: 50px;
            flex-wrap: wrap;
            overflow: hidden;
            image-rendering: -webkit-optimize-contrast; 
        }

        .hero-content {
            flex: 1;
            min-width: 300px;
        }

        .hero-content h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            color: 
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: 
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

        
        section {
            padding: 80px 5%;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            color: 
            margin-bottom: 10px;
        }

        .section-header .underline {
            width: 100px;
            height: 4px;
            background: 
            margin: 0 auto;
            border-radius: 2px;
        }

        
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
        }

        .service-card h3 {
            color: 
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .service-card p {
            color: 
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .service-card .price {
            font-weight: 700;
            color: 
            font-size: 1.1rem;
        }

        
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
            image-rendering: auto; 
        }

        .gallery-grid img:hover {
            transform: scale(1.05);
        }

        
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
            color: 
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: 
            line-height: 1.6;
            font-size: 0.95rem;
        }

        
        .bg-pink {
            background: linear-gradient(135deg, 
        }

        
        .hero {
            position: relative;
            overflow: hidden;
            height: 100vh;
            display: flex;
            align-items: center;
            padding: 0;
            background: none !important; 
        }

        .hero::before { 
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, 
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
            .hero > div[style*="background: 
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
                display: none !important; 
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
            background: 
            border-radius: 50%;
        }

        
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
            color: 
            font-size: 1.5rem;
            z-index: 0;
            opacity: 0.6;
            animation: float 5s ease-in-out infinite;
        }

        
        .hero {
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>
    
    <nav>
    
        <div class="nav-left">
            <img src="assets/img/588237789-17951033973048360-6209016104075046821-n-removebg-preview-1.png" alt="Logo" class="logo" onerror="this.style.display='none'">
            <?php if ($isLoggedIn): ?>
                <span class="user-greeting">Hi, <?= htmlspecialchars($username) ?>!</span>
            <?php endif; ?>
        </div>
        

        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div id="mobileMenu">
            <a href="
            <a href="
            <a href="
            <a href="
            
            <div class="mobile-auth">
                <?php if ($isLoggedIn): ?>
                    <?php if ($isAdmin): ?>
                        <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        <a href="logout.php" class="btn btn-logout">Logout</a>
                    <?php else: ?>
                        <a href="user/history.php" class="btn btn-outline" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <span style="font-size: 1.3rem;">📋</span> Riwayat Transaksi
                        </a>
                        <a href="user/reservasi.php" class="btn btn-primary">Booking Sekarang</a>
                        <a href="auth/logout.php" class="btn btn-logout">Logout</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-outline">Log In</a>
                    <a href="auth/register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav-center">
            <a href="
            <a href="
            <a href="
            <a href="
        </div>
        

        <div class="nav-right">
            <?php if ($isLoggedIn): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="auth/logout.php" class="btn btn-logout">Logout</a>
                <?php else: ?>
                    <a href="user/history.php" class="btn btn-icon" title="Riwayat Transaksi">📋</a>
                    <a href="<?= 'user/reservasi.php' ?>" class="btn btn-primary">Reservasi</a>
                    <a href="auth/logout.php" class="btn btn-logout">Logout</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-outline">Log In</a>
                <a href="auth/register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>


    <section class="hero" id="home" style="position: relative; overflow: hidden; height: 100vh; display: flex; align-items: center; padding: 0;">
        
        
        <div style="position: absolute; top: 0; left: 0; width: 50%; height: 100%; background: 
        <div style="position: absolute; top: 0; right: 0; width: 50%; height: 100%; background: linear-gradient(135deg, 
        
        
        <div style="max-width: 1300px; margin: 0 auto; width: 100%; height: 100%; display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1;">
            
            
            <div class="hero-content" style="flex: 1; padding: 0 5%; z-index: 2;">
                <h2 style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 4rem; line-height: 1.1; color: 
                    <span style="color: 
                    <span style="color: 
                </h2>
                <p style="font-family: 'Satisfy', cursive; font-size: 1.5rem; color: 
                    Leave the shine of your hands to us...
                </p>
                <a href="<?php 
                    if (!$isLoggedIn) echo 'auth/login.php';
                    elseif ($isAdmin) echo 'admin/dashboard.php';
                    else echo 'user/reservasi.php';
                ?>" class="btn btn-primary" style="padding: 15px 50px; font-size: 1.2rem; border-radius: 50px; box-shadow: 0 10px 25px rgba(234, 54, 113, 0.4);">
                    Book Now
                </a>
            </div>

            
            <div class="hero-images" style="flex: 1; height: 100%; position: relative; display: flex; align-items: flex-end; justify-content: center;">
                
                <div style="position: relative; width: 80%; height: 85%; background: url('assets/img/img1.jpg') no-repeat center center/cover; border-radius: 200px 200px 0 0; box-shadow: -20px 20px 50px rgba(0,0,0,0.1);">
                    
                    <div style="position: absolute; bottom: 50px; left: -80px; width: 220px; height: 280px; background: url('assets/img/img3.jpg') no-repeat center center/cover; border: 10px solid white; border-radius: 20px; transform: rotate(-10deg); box-shadow: 0 15px 40px rgba(0,0,0,0.15);"></div>
                    
                    
                    <div style="position: absolute; top: 50px; right: -30px; width: 100px; height: 100px; background: 
                </div>
            </div>

        </div>

        
        <div class="sparkle" style="top: 15%; left: 45%; font-size: 2rem;">✨</div>
        <div class="sparkle" style="bottom: 10%; right: 5%; font-size: 2.5rem;">✨</div>
        <div class="deco-shape spin-slow" style="bottom: -100px; left: -50px; width: 300px; height: 300px; border: 40px solid 
    </section>

    
    <section id="layanan" style="position: relative; overflow: hidden; background: linear-gradient(180deg, 
         
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
            <a href="services/service_detail.php?type=nailart" class="service-card">
                <img src="assets/img/img4.png" alt="Nail Art" onerror="this.src='https://via.placeholder.com/250x200/ea3671/ffffff?text=Nail+Art'">
                <div class="service-card-content">
                    <h3>Nail Art</h3>
                    <p>Kreasi seni pada kuku dengan berbagai desain yang dapat disesuaikan dengan keinginan anda.</p>
                    <div class="price">Mulai Rp 30.000</div>
                </div>
            </a>

            <a href="services/service_detail.php?type=extension" class="service-card">
                <img src="assets/img/img5.png" alt="Extension" onerror="this.src='https://via.placeholder.com/250x200/ffd9e2/333333?text=Extension'">
                <div class="service-card-content">
                    <h3>Extension</h3>
                    <p>Memberikan tambahan detail dan desain pada NailArt anda agar terlihat lebih menarik lagi.</p>
                    <div class="price">Mulai Rp 60.000</div>
                </div>
            </a>

            <a href="services/service_detail.php?type=nailart_kaki" class="service-card">
                <img src="assets/img/img6.png" alt="Nail Art Kaki" onerror="this.src='https://via.placeholder.com/250x200/ea3671/ffffff?text=Pedicure'">
                <div class="service-card-content">
                    <h3>Nail Art Kaki</h3>
                    <p>Kreasi seni pada kuku kaki dengan berbagai desain yang dapat disesuaikan dengan keinginan anda.</p>
                    <div class="price">Mulai Rp 35.000</div>
                </div>
            </a>

            <a href="services/service_detail.php?type=addons" class="service-card">
                <img src="assets/img/img7.png" alt="Add Ons" onerror="this.src='https://via.placeholder.com/250x200/ffd9e2/333333?text=Add+Ons'">
                <div class="service-card-content">
                    <h3>Add Ons</h3>
                    <p>Memberi tambahan pada NailArt sesuai keinginan anda dengan tambahan biaya yang tersedia.</p>
                    <div class="price">Mulai Rp 2.000</div>
                </div>
            </a>
        </div>
    </section>

    
    <section id="katalog" style="position: relative; overflow: hidden;">
        
        <div class="dots-grid" style="bottom: 40px; left: 20px;">
            <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
        </div>
        <div class="dots-grid" style="top: 40px; right: 5%; grid-template-columns: repeat(2, 1fr);">
            <div></div><div></div><div></div><div></div><div></div><div></div>
        </div>
        
        <div class="deco-shape float-slow" style="top: 50%; right: -20px; width: 80px; height: 80px; background: 
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
            <div style="width: 8px; height: 8px; background: 
            <div style="width: 8px; height: 8px; background: 
            <div style="width: 8px; height: 8px; background: 
            <div style="width: 8px; height: 8px; background: 
            <div style="width: 8px; height: 8px; background: 
            <div style="width: 8px; height: 8px; background: 
        </div>

        <div class="section-header">
            <h2>Kenapa kamu harus memilih Neydream? 🤔</h2>
            <div class="underline"></div>
        </div>

        <div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 40px;">
            
            
            <div class="feature-card-wide" style="position: relative;">
                <div style="position: absolute; top: -30px; left: -30px; font-size: 4rem; z-index: 1;">✨</div>
                <h3>Premium Quality</h3>
                <p>" Kami hanya menggunakan gel polish pilihan dengan kualitas terbaik yang telah teruji aman untuk kuku asli. Formulanya dirancang agar warna tahan lama, berkilau sempurna, dan tetap menjaga kekuatan serta kesehatan kuku Anda tanpa membuat kuku rapuh atau rusak. "</p>
                
                <div style="position: absolute; bottom: -10px; left: 20px; display: flex; gap: 5px;">
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                </div>
            </div>

            
            <div class="feature-card-wide" style="position: relative;">
                <div style="position: absolute; top: -20px; right: -30px; font-size: 4rem; z-index: 1; transform: rotate(15deg);">🎨</div>
                <h3>Custom Art</h3>
                <p>" Setiap kuku adalah kanvas seni. Anda bebas membawa referensi, ide, atau desain impian apa pun, dan nail artist profesional kami akan menerjemahkannya dengan presisi dan detail tinggi. Dari gaya minimalis hingga nail art kompleks, setiap sentuhan dibuat eksklusif sesuai karakter dan keinginan Anda. "</p>
                
                <div style="position: absolute; top: -15px; right: 80px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px;">
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                </div>
            </div>

            
            <div class="feature-card-wide" style="position: relative;">
                <div style="position: absolute; top: 50%; left: -40px; transform: translateY(-50%); font-size: 4rem; z-index: 1;">🛡️</div>
                <h3>Hygienic Tools</h3>
                <p>" Kebersihan dan keamanan adalah prioritas utama kami. Seluruh alat yang digunakan melalui proses sterilisasi menyeluruh sebelum dan sesudah pemakaian. Kami memastikan setiap perawatan dilakukan dengan standar kebersihan tinggi agar Anda merasa nyaman, aman, dan bebas khawatir selama treatment. "</p>
                
                <div style="position: absolute; top: 20px; right: -20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px;">
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                    <div style="width: 6px; height: 6px; background: 
                </div>
            </div>

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
                color: 
                font-size: 1.8rem;
                margin-bottom: 15px;
                font-weight: 700;
            }
            .feature-card-wide p {
                color: 
                font-size: 1rem;
                line-height: 1.8;
                max-width: 90%;
                margin: 0 auto;
            }
        </style>
    </section>

    
    
    <section id="faq" style="padding: 80px 5%; position: relative; overflow: hidden; background: linear-gradient(180deg, 
        <div class="section-header">
            <h2>Frequency Asked Questions (FAQ)</h2>
            <p style="color: 
            <div class="underline"></div>
        </div>

        <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px; position: relative; z-index: 1;">
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Apakah harus bayar DP untuk booking?</span>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Ya, Kak. Kami memerlukan DP sebesar <strong>Rp20.000</strong> untuk mengunci slot Kakak agar tidak diambil orang lain. Sisa pembayaran dilakukan di studio setelah pengerjaan selesai.</p>
                </div>
            </div>

            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Di mana lokasi tepatnya Neydream Studio?</span>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Neydream Studio berada di pusat kota, Kak! Aksesnya sangat mudah. Untuk Maps detail silakan hubungi Admin via WhatsApp atau cek link di bio Instagram kami.</p>
                </div>
            </div>

            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Berapa lama daya tahan Nail Art di Neydream?</span>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Kuku dari Neydream dijamin awet! Biasanya bertahan <strong>3 hingga 4 minggu</strong> tergantung pada aktivitas dan perawatan Kakak di rumah.</p>
                </div>
            </div>

            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Apakah bisa membawa referensi desain sendiri?</span>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Bisa banget! Kakak boleh bawa foto referensi dari Pinterest atau Instagram, nanti terapis profesional kami akan membuatkan semirip mungkin sesuai request Kakak.</p>
                </div>
            </div>

            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Kenapa namanya ganti menjadi Neydream?</span>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>Neydream Studio sebelumnya dikenal sebagai <strong>Glamour Nails</strong>. Kami melakukan rebranding agar tampil lebih fresh dan modern, namun kualitas dan layanan kami tetap yang nomor satu!</p>
                </div>
            </div>
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
                color: 
                transition: background 0.3s;
            }
            .faq-question:hover {
                background: 
            }
            .faq-answer {
                padding: 0 25px;
                max-height: 0;
                overflow: hidden;
                transition: all 0.3s ease-out;
                color: 
                line-height: 1.6;
            }
            .faq-item.active .faq-answer {
                padding: 0 25px 20px 25px;
                max-height: 200px;
            }
            .faq-item.active .faq-icon {
                transform: rotate(45deg);
                color: 
            }
            .faq-icon {
                font-size: 1.5rem;
                transition: transform 0.3s;
                color: 
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
        
        <div class="deco-shape float-fast" style="top: -50px; left: 10%; width: 150px; height: 150px; border: 20px solid 
        <div class="deco-shape spin-slow" style="bottom: -50px; right: 10%; width: 200px; height: 200px; border: 2px dashed 
        
        <div style="max-width: 800px; margin: 0 auto; position: relative; z-index: 1;">
            <h2 style="font-size: 2.8rem; color: 
            <p style="font-size: 1.2rem; color: 
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

    
    <section id="feedback" style="padding: 80px 5%; background: 
        <div class="section-header" style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2.5rem; color: 
            <p style="color: 
            <div style="width: 80px; height: 4px; background: 
        </div>

        <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <?php if ($isLoggedIn): ?>
                <form action="actions/submit_feedback.php" method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: 
                        <input type="text" name="name" value="<?= htmlspecialchars($username) ?>" readonly placeholder="Masukkan nama Anda" required style="width: 100%; padding: 12px 15px; border: 2px solid 
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: 
                        <input type="text" name="whatsapp_number" placeholder="Contoh: 08123456789" required style="width: 100%; padding: 12px 15px; border: 2px solid 
                    </div>
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: 
                        <textarea name="message" rows="5" placeholder="Tuliskan masukan Anda di sini..." required style="width: 100%; padding: 12px 15px; border: 2px solid 
                    </div>
                    <button type="submit" style="width: 100%; background: 
                        Kirim Masukan ✨
                    </button>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 20px 0;">
                    <p style="margin-bottom: 20px; color: 
                    <a href="auth/login.php" class="btn btn-primary" style="display: inline-block; padding: 12px 35px; border-radius: 30px;">Login Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    
    <div class="chat-container" id="chatContainer" style="display: none;">
        <div class="chat-header">
            <div class="header-info">
                <div class="admin-avatar">
                    <img src="https://cdn-icons-png.flaticon.com/512/1144/1144760.png" alt="Admin">
                    <span class="online-status"></span>
                </div>
                <div>
                    <h4>Asisten Neydream</h4>
                    <p>Online</p>
                </div>
            </div>
            <button class="close-chat" onclick="toggleChat()">×</button>
        </div>

        <div class="chat-box" id="chatBox">
            <div class="message admin">
                Halo! ✨ Selamat datang di Neydream Studio. Ada yang bisa saya bantu hari ini?
            </div>
        </div>

        <div class="chat-input-area">
            <input type="text" id="userInput" placeholder="Tulis pesan..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">➤</button>
        </div>
    </div>

    <div class="chat-icon-bubble" id="chatIcon" onclick="toggleChat()">
        <img src="https://cdn-icons-png.flaticon.com/512/5968/5968841.png" alt="Chat">
    </div>

    <script src="assets/js/ai_assistant.js"></script>
    
    <script>
        
        const overlay = document.createElement('div');
        overlay.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.15); z-index:1650; display:none; transition: opacity 0.3s;";
        document.body.appendChild(overlay);

        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        function toggleMenu() {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            
            if (mobileMenu.classList.contains('active')) {
                overlay.style.display = 'block';
                setTimeout(() => overlay.style.opacity = '1', 10);
            } else {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 300);
            }
        }

        hamburger.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        
        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', () => {
                if(mobileMenu.classList.contains('active')) toggleMenu();
            });
        });

        
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
                        if (link.getAttribute('href') === '
                            link.classList.add('active');
                        }
                    });
                    found = true;
                    break;
                }
            }
            
            
            if (!found || window.scrollY < 100) {
                links.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '
                        link.classList.add('active');
                    }
                });
            }
        }

        window.addEventListener('scroll', updateActiveLink);
        window.addEventListener('load', updateActiveLink);

        
        document.querySelectorAll('a[href^="
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 90,
                        behavior: 'smooth'
                    });
                    
                    
                    setTimeout(updateActiveLink, 800);
                }
            });
        });
    </script>
    
    <style>
        
        .chat-container {
            position: fixed;
            bottom: 90px;
            right: 25px;
            width: 320px;
            height: 450px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            z-index: 9999;
            overflow: hidden;
        }

        .chat-header {
            background: 
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            position: relative;
            width: 45px;
            height: 45px;
        }

        .admin-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid white;
        }

        .online-status {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background: 
            border: 2px solid white;
            border-radius: 50%;
        }

        .header-info h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .header-info p {
            margin: 0;
            font-size: 12px;
            opacity: 0.9;
        }

        .close-chat {
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            width: 30px;
            height: 30px;
        }

        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: 
        }

        .message {
            margin-bottom: 15px;
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 12px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .message.admin {
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-bottom-left-radius: 4px;
        }

        .message.user {
            background: 
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .chat-input-area {
            display: flex;
            gap: 10px;
            padding: 15px 20px;
            background: white;
            border-top: 1px solid 
        }

        .chat-input-area input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid 
            border-radius: 25px;
            font-size: 14px;
            outline: none;
        }

        .chat-input-area input:focus {
            border-color: 
        }

        .chat-input-area button {
            width: 45px;
            height: 45px;
            background: 
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-input-area button:hover {
            transform: scale(1.05);
        }

        .chat-icon-bubble {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 55px;
            height: 55px;
            background: 
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 133, 161, 0.4);
            z-index: 9998;
            transition: all 0.3s;
        }

        .chat-icon-bubble:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(234, 54, 113, 0.5);
        }

        .chat-icon-bubble img {
            width: 28px;
            height: 28px;
            filter: brightness(0) invert(1);
        }

        @media (max-width: 480px) {
            .chat-container {
                width: calc(100% - 20px);
                right: 10px;
                height: 500px;
            }

            .chat-icon-bubble {
                width: 55px;
                height: 55px;
                right: 20px;
                bottom: 20px;
            }

            .chat-icon-bubble img {
                width: 28px;
                height: 28px;
            }
        }
    </style>
</body>
</html>

