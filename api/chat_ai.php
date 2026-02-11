<?php
// 1. Paksa session menyala di paling atas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Matikan error display agar tidak merusak JSON, tapi kita simpan ke log jika perlu
require '../../core/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// 3. API Key Gemini dari Google AI Studio (Bisa banyak untuk rotasi)
$API_KEYS = [
    "AIzaSyAt7tLr6mD89qTeINcOgySAlnd64-0all8",
    "AIzaSyD2KpEf1t9u61xq_LftAJZuyCOT5Fk8hTo",
    "AIzaSyDgxDltZiwtPaKk0DqmU1bUkYGsheQyN1M",
    "AIzaSyAYx8QAnLrjgNHFWM6YmuZPrCaiNvN4TZQ",
    "AIzaSyCXPJtGwt9BfYioGLTIyh62VPtW3gDSgQ4",
    "AIzaSyAXfh64bzvu9rMdK6JWilSDcDMB4OgoxvM",
    "AIzaSyCMM_sapJhpNIk9-xSdNEO8ofq8G9p7_KE",
    "AIzaSyDMWnKrZx1Geau8nILdX0au_mYpTJnV8iw",
    "AIzaSyDcIgSBAMB0ohFL2dBgnuUXz4OmryR0OPQ",
    "AIzaSyBDtUWvBTrBLjfQ-BqLHCyx93wHnF416XI"
];
$API_KEY = $API_KEYS[array_rand($API_KEYS)]; 

// 4. Cek apakah user benar-benar login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Sesi login hilang, silakan refresh halaman.']);
    exit;
}

// 5. Tangkap input
$inputJson = file_get_contents('php://input');
$input = json_decode($inputJson, true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['success' => false, 'error' => 'Pesan masih kosong nih Kak.']);
    exit;
}

// 6. Siapkan Payload untuk Gemini (Ambil data lokasi dari DB)
$settingsRes = $conn->query("SELECT * FROM studio_settings WHERE id = 1");
$st = $settingsRes->fetch_assoc();
$realAddress = $st ? ($st['full_address'] . ", " . $st['city'] . ", " . $st['province']) : "Alamat belum diatur.";

$systemPrompt = "Anda adalah Asisten Virtual Neydream Studio. Ramah, santai, gunakan bahasa Indonesia gaul. Jawab singkat dan padat seputar Nail Art. Alamat asli kami adalah: $realAddress. Jika ditanya lokasi, berikan alamat ini.";
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $API_KEY;

$payload = [
    "contents" => [
        ["parts" => [["text" => $systemPrompt . "\n\nUser: " . $userMessage]]]
    ]
];

// 7. Proses CURL (Komunikasi ke Google)
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Wajib untuk InfinityFree
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Wajib untuk InfinityFree
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// 8. Output Hasil
if ($httpCode === 200) {
    $result = json_decode($response, true);
    $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Duh, aku lagi blank. Tanya lagi dong?";
    echo json_encode(['success' => true, 'reply' => $aiResponse]);
} else {
    // Berikan info detail kalau gagal biar kita bisa benerin
    echo json_encode([
        'success' => false, 
        'error' => 'Koneksi AI Gagal (Code: '.$httpCode.')',
        'curl_error' => $curlError
    ]);
}
?>
