<?php
require '../core/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: text/plain');
header('X-Chat-Engine-Version: 4.5.0');

$API_KEYS = [
    "AIzaSyCvJPGGc0yq6Vl_Z_kMIT59ioU_cXRFHjA", 
    "AIzaSyD2KpEf1t9u61xq_LftAJZuyCOT5Fk8hTo",
    "AIzaSyDgxDltZiwtPaKk0DqmU1bUkYGsheQyN1M",
    "AIzaSyAYx8QAnLrjgNHFWM6YmuZPrCaiNvN4TZQ",
    "AIzaSyCXPJtGwt9BfYioGLTIyh62VPtW3gDSgQ4",
    "AIzaSyAXfh64bzvu9rMdK6JWilSDcDMB4OgoxvM",
    "AIzaSyCMM_sapJhpNIk9-xSdNEO8ofq8G9p7_KE",
    "AIzaSyDMWnKrZx1Geau8nILdX0au_mYpTJnV8iw",
    "AIzaSyDcIgSBAMB0ohFL2dBgnuUXz4OmryR0OPQ",
    "AIzaSyBDtUWvBTrBLjfQ-BqLHCyx93wHnF416XI",
    "AIzaSyDaKqmHjaOR3zRnfL12Bl9icBNtKOg2tV8",
    "AIzaSyDCkpn714vavPgAprU2QKmyKFv_KVnnld8",
    "AIzaSyDAvpqzP4fmiGus8qiSr6RR_W6WOo_D8aw",
];
$API_KEY = $API_KEYS[0]; 

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Sesi login tidak terdeteksi. Silakan Login kembali.']) . "!!!JSON_END!!!";
    exit;
}

$inputJson = file_get_contents('php://input');
$input = json_decode($inputJson, true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    ob_clean();
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Pesan masih kosong nih Kak.']) . "!!!JSON_END!!!";
    exit;
}


$settingsRes = $conn->query("SELECT * FROM studio_settings WHERE id = 1");
$st = $settingsRes->fetch_assoc();
$realAddress = $st ? (($st['full_address'] ?? '') . ", " . ($st['city'] ?? '') . ", " . ($st['province'] ?? '')) : "Alamat belum diatur.";
$wa = $st['whatsapp'] ?? 'Belum diatur';
$ig = $st['instagram'] ?? 'Belum diatur';
$em = $st['email'] ?? 'Belum diatur';
$dp = intval($st['min_dp_percent'] ?? 0) . '%';
$studioName = $st['studio_name'] ?? 'Neydream Studio';


$servicesList = "";
$sRes = $conn->query("SELECT name, price_start FROM services");
if ($sRes) {
    while($s = $sRes->fetch_assoc()) {
        $servicesList .= "- " . $s['name'] . " (Mulai Rp " . number_format($s['price_start'], 0, ',', '.') . ")\n";
    }
}


$systemPrompt = "IDENTITAS: Anda adalah Nova, asisten virtual dari $studioName. KAMI ADALAH STUDIO NAIL ART (KECANTIKAN KUKU), BUKAN PROGRAMMER/AGENSI KREATIF.\n" .
                "GAYA BICARA: Ramah, santai, gunakan bahasa Indonesia gaul (seperti 'Kak', 'nih', 'ya'). Jawab sangat singkat (max 2 kalimat).\n\n" .
                "KONTROL TOPIK (MUTLAK):\n" .
                "1. Fokus utama HANYA pada layanan kuku: Nail Art, Extension, Menicure, Pedicure.\n" .
                "2. Jika user bertanya tentang coding, kode, pemrograman, desain grafis, fotografi, media sosial, atau hal lain di luar kuku, TOLAK dengan sopan: 'Maaf Kak, $studioName itu khusus buat kuku cantik (Nail Art), aku nggak bisa jawab soal coding atau hal lainnya! ✨'.\n" .
                "3. Jangan pernah membicarakan atau memberikan jawaban tentang coding/pemrograman.\n\n" .
                "INFO STUDIO:\n" .
                "- Alamat: $realAddress\n" .
                "- Kontak: WA $wa, IG $ig, Email $em\n" .
                "- DP Booking: $dp\n" .
                "- Daftar Layanan:\n$servicesList\n\n" .
                "Tugas: Jawab pertanyaan user sesuai aturan ketat di atas.";


$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $API_KEY;
$payload = [
    "system_instruction" => ["parts" => [["text" => $systemPrompt]]],
    "contents" => [["parts" => [["text" => $userMessage]]]]
];
$jsonPayload = json_encode($payload);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

ob_clean();
if ($httpCode === 200) {
    $result = json_decode($response, true);
    $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Duh, aku lagi blank. Tanya lagi dong?";
    echo "!!!JSON_START!!!" . json_encode(['success' => true, 'reply' => $aiResponse]) . "!!!JSON_END!!!";
} else {
    echo "!!!JSON_START!!!" . json_encode(['success' => false, 'error' => 'Koneksi AI sibuk.']) . "!!!JSON_END!!!";
}
exit;
