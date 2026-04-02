<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 PHP Diagnostic Test</h1>";
echo "<p>Jika Kakak melihat tulisan ini, berarti PHP di hosting Kakak sudah berjalan!</p>";
echo "<hr>";
echo "<h3>Informasi Server:</h3>";
echo "<li>Versi PHP: " . phpversion() . "</li>";
echo "<li>Direktori Saat Ini: " . __DIR__ . "</li>";
echo "<li>Host: " . $_SERVER['HTTP_HOST'] . "</li>";
echo "<hr>";
echo "<p><b>Langkah selanjutnya:</b> Coba akses <a href='core/config.php'>core/config.php</a>. Jika halamannya BLANK (putih bersih), berarti file config sudah benar. Jika 500, berarti ada kesalahan di dalam file tersebut.</p>";
?>
