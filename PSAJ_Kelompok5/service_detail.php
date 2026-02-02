<?php
require '../core/config.php';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$type = isset($_GET['type']) ? $_GET['type'] : 'unknown';

$services = [
    'nailart' => [
        'title' => 'Nail Art',
        'price' => 'Mulai Rp 30.000',
        'desc' => 'yang dapat disesuaikan dengan keinginan anda. Kami menawarkan berbagai gaya mulai dari minimalis hingga rumit.',
        'details' => ['Gel Polish', 'French Tips', 'Ombre', 'Marble'],
        'img' => 'home/img/image-5.png'
    ],
    'extension' => [
        'title' => 'Extension',
        'price' => 'Mulai Rp 60.000',
        'desc' => 'Ingin kukuension kami menggunakan bahan berkualitas untuk hasil',
        'details' => ['Acrylic Extension', 'Gel Extension', 'Polygel'],
        'img' => 'home/img/rectangle-46.svg' 
    ],
    'nailart_kaki' => [
        'title' => 'Nail Art Kaki',
        'price' => 'Mulai Rp 35.000',
        'desc' => 'Percantik jari kaki anda dengan perawatan pedicure dan nail art yang memukau.',
        'details' => ['Pedicure', 'Gel Polish Kaki', 'Spa Kaki'],
        'img' => 'home/img/rectangle-48.svg'
    ],
    'addons' => [
        'title' => 'Add Ons',
        'price' => 'Mulai Rp 2.000',
        'desc' => 'Tambahkan ornamen cantik pada kuku anda.',
        'details' => ['Diamond', 'Sticker', '3D Charm', 'Cat Eye Effect'],
        'img' => 'home/img/rectangle-47.svg'
    ]
];

$data = isset($services[$type]) ? $services[$type] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data ? $data['title'] : 'Layanan' ?> - Ney Dream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, 
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .header {
            background: linear-gradient(135deg, 
            padding: 60px 40px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header .price {
            font-size: 1.5rem;
            font-weight: 600;
            opacity: 0.95;
        }

        .content {
            padding: 50px 40px;
        }

        .description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: 
            margin-bottom: 40px;
            text-align: center;
        }

        .features {
            background: 
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 40px;
        }

        .features h3 {
            color: 
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .features ul {
            list-style: none;
        }

        .features li {
            padding: 12px 0;
            border-bottom: 1px solid 
            display: flex;
            align-items: center;
            color: 
            font-size: 1rem;
        }

        .features li:last-child {
            border-bottom: none;
        }

        .features li:before {
            content: "✓";
            color: 
            font-weight: bold;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 35px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-back {
            background: 
            color: 
        }

        .btn-back:hover {
            background: 
        }

        .btn-reserve {
            background: linear-gradient(135deg, 
            color: white;
        }

        .btn-reserve:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(234, 54, 113, 0.3);
        }

        .not-found {
            text-align: center;
            padding: 60px 40px;
        }

        .not-found h1 {
            color: 
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .header {
                padding: 40px 25px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .content {
                padding: 35px 25px;
            }

            .buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($data): ?>
            <div class="header">
                <h1><?= $data['title'] ?></h1>
                <div class="price"><?= $data['price'] ?></div>
            </div>
            
            <div class="content">
                <p class="description"><?= $data['desc'] ?></p>
                
                <div class="features">
                    <h3>Yang Anda Dapatkan:</h3>
                    <ul>
                        <?php foreach($data['details'] as $item): ?>
                            <li><?= $item ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="buttons">
                    <a href="../index.php
                    <a href="<?php echo $isAdmin ? '../admin/dashboard.php' : '../user/reservasi.php'; ?>" class="btn btn-reserve">Booking Sekarang</a>
                </div>
            </div>
        <?php else: ?>
            <div class="not-found">
                <h1>Layanan tidak ditemukan</h1>
                <p style="margin-bottom: 30px; color: 
                <a href="../index.php
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

