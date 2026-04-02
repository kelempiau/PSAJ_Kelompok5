<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🛠️ Laporan Diagnostik Website (Ney Dream Studio)</h1>";
echo "<hr>";


echo "<h3>1. Informasi Server:</h3>";
echo "<li>📍 Versi PHP: " . phpversion() . "</li>";
echo "<li>📍 Direktori Website: " . __DIR__ . "</li>";
echo "<li>📍 Header Host: " . $_SERVER['HTTP_HOST'] . "</li>";


echo "<h3>2. Testing Koneksi Database (Mandiri):</h3>";
$host = 'sql106.infinityfree.com'; 
$user = 'if0_40996393'; 
$pass = 'iqRNXszcPY'; 
$db   = 'if0_40996393_db'; 

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    echo "<span style='color:red'>❌ KONEKSI GAGAL: " . $conn->connect_error . "</span><br>";
    echo "<small>Tip: Periksa lagi Host/User/Pass di CPanel Hosting Kakak.</small>";
} else {
    echo "<span style='color:green'>✅ KONEKSI KE SERVER SQL BERHASIL!</span><br>";
    
    
    if ($conn->select_db($db)) {
        echo "<span style='color:green'>✅ DATABASE '$db' DITEMUKAN!</span><br>";
        
        
        $tables = ['users', 'services', 'studio_settings', 'reservations'];
        echo "<h4>Status Tabel Database:</h4>";
        foreach ($tables as $t) {
            $check = $conn->query("SHOW TABLES LIKE '$t'");
            if ($check->num_rows > 0) {
                echo "<li>Tabel '$t': <span style='color:green'>Ditemukan (OK)</span></li>";
            } else {
                echo "<li>Tabel '$t': <span style='color:red'>HILANG / TIDAK ADA!</span></li>";
            }
        }
    } else {
        echo "<span style='color:red'>❌ DATABASE '$db' TIDAK DITEMUKAN!</span><br>";
    }
}


echo "<h3>3. Cek File Utama:</h3>";
$files_to_check = ['core/config.php', 'index.php', 'includes/loading.php'];
foreach ($files_to_check as $f) {
    if (file_exists($f)) {
        echo "<li>File '$f': <span style='color:green'>Ada (OK)</span></li>";
    } else {
        echo "<li>File '$f': <span style='color:red'>TIDAK ADA! (Mungkin salah folder?)</span></li>";
    }
}

echo "<br><hr>";
echo "<p>Kirimkan screenshot atau isi laporan ini ke saya ya Kak!</p>";
?>
