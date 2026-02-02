# Quick Fix - Access Chat System dengan XAMPP

## Masalah
`localhost:8000` tidak bisa diakses karena XAMPP jalan di port berbeda.

## Solusi

### Opsi 1: Gunakan Port XAMPP (Recommended)
Ganti URL menjadi salah satu dari:
- http://localhost/admin/conversations.php
- http://localhost:80/admin/conversations.php
- http://localhost:8080/admin/conversations.php (jika port 80 bentrok)

### Opsi 2: Jalankan PHP Built-in Server di Port 8000
Buka terminal/command prompt di folder project:
```bash
cd "d:\web reservasi"
php -S localhost:8000
```

Kemudian akses: http://localhost:8000/admin/conversations.php

## Cek Port XAMPP
1. Buka XAMPP Control Panel
2. Lihat di sebelah Apache, ada angka port (biasanya 80 atau 8080)
3. Gunakan port tersebut

## Testing URLs

### Untuk Admin:
- http://localhost/admin/conversations.php (XAMPP)
- http://localhost:8000/admin/conversations.php (PHP Server)

### Untuk Customer:
- http://localhost/user/reservasi.php (XAMPP)
- http://localhost:8000/user/reservasi.php (PHP Server)

### Untuk Dashboard:
- http://localhost/admin/dashboard.php (XAMPP)

## Tips
✅ Pastikan Apache di XAMPP Control Panel sudah hijau (running)
✅ Jika pakai PHP server, jangan tutup terminal/command prompt
✅ Gunakan salah satu metode saja (XAMPP atau PHP server), jangan campur
