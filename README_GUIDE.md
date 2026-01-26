# Panduan Lengkap Sistem Neydream Nail Art Studio 💅✨

Selamat datang di panduan penggunaan website Neydream Studio! Website ini telah dioptimalkan dengan struktur profesional, sistem AI pintar, dan alur kerja yang sangat rapi.

---

## 🏗️ 1. Struktur Folder (Arsitektur V2)
Sistem sekarang sudah menggunakan struktur "Enterprise Level" agar mudah dikelola dan aman:

-   📁 `core/`: Jantung aplikasi. Tempat setting database (`config.php`).
-   📁 `assets/`: Museum visual. Berisi semua Gambar, CSS (desain), dan JS (logika interaktif).
-   📁 `auth/`: Gerbang masuk. Login, Register, dan Logout pelanggan.
-   📁 `user/`: Area khusus pelanggan. Tempat reservasi, lihat riwayat, dan mintak refund.
-   📁 `admin/`: Markas pusat. Dashboard untuk kelola pesanan, user, dan feedback.
-   📁 `services/`: Detail keindahan tiap kategori layanan kuku.
-   📁 `actions/`: Logika di balik layar (proses kirim data).

---

## 🤖 2. Modul AI Assistant (Supreme v21.0)
Asisten digital ini siap membantu 24 jam dengan kemampuan "Multi-Intent Recognition".

### **Fitur Utama:**
-   **Kamus Bahasa Gaul**: Paham kata singkatan (dmn, brp, nggak, udh).
-   **Multi-Intent**: Bisa menjawab kalimat panjang dengan dua pertanyaan sekaligus.
-   **Tombol Pintar**: Memberikan link langsung ke WA, Maps, atau Instagram.
-   **600+ Knowledge Base**: Tahu segalanya soal teknik kuku, garansi, hingga tips perawatan kuku.

### **Cara Pakai:**
1. Klik gelembung Chat di kanan bawah.
2. Ketik apa saja: *"brp harga nailart dan lokasinya dmn?"*
3. AI akan menjawab dan memberikan tombol navigasi cepat.

---

## 🗓️ 3. Modul Reservasi & Pembayaran
Alur booking otomatis yang memastikan tidak ada jadwal yang bentrok.

### **Tutorial Reservasi:**
1.  **Login**: Pelanggan harus masuk ke akunnya dulu.
2.  **Isi Form**: Masuk ke menu `Booking Sekarang`.
3.  **Pilih Slot**: Pilih Terapis, Tanggal, dan Jam yang masih tersedia (warna hijau).
4.  **Kalkulasi Otomatis**: Pilih layanan utama + Add-ons, harga total akan muncul otomatis.
5.  **Pembayaran DP**: Transfer Rp20.000 ke rekening BCA/QRIS yang tertera.
6.  **Upload Bukti**: Masukkan screenshot bukti transfer.
7.  **Finalisasi**: Admin akan memverifikasi di Dashboard.

---

## 👤 4. Modul User Area
Tempat pelanggan memantau "perjalanan cantik" mereka.

-   **Riwayat Transaksi**: Lihat status booking (Pending, Confirmed, Completed).
-   **E-Receipt**: Klik tombol untuk melihat nota digital resmi yang cantik.
-   **Sistem Refund**: Jika ingin batal, pelanggan bisa ajukan refund (status bisa dipantau).

---

## 🛠️ 5. Dashboard Admin (Pusat Kendali)
Hanya bisa diakses oleh akun dengan role `admin`.

### **Fitur Admin:**
1.  **Kelola Booking**: Mengubah status pesanan (Konfirmasi bayar atau Selesai).
2.  **Kelola Jadwal (Slots)**: Membuka/menutup jam kerja terapis tertentu.
3.  **Manajemen User**: Mengubah role user (Pelanggan biasa menjadi Admin).
4.  **Monitor Feedback**: Membaca kritik dan saran yang masuk dari pelanggan.
5.  **Refund Manager**: Menyetujui atau menolak pengembalian dana.

---

## 🚀 6. Cara Update ke GitHub (Kolaborasi)
Tutorial untuk Kakak dan teman kelompok agar kode selalu Sinkron!

### **A. Kakak (Update Kode Baru):**
```bash
git add .
git commit -m "Catatan perubahan Anda"
git push origin v2-modernized-structure
```

### **B. Teman (Tarik Kode Terbaru):**
```bash
git pull origin v2-modernized-structure
```

---

## 💡 Tips & Trik:
- **Responsive**: Web ini sudah 100% aman dibuka di HP ukuran apa saja tanpa "geser-geser" ke samping.
- **Warna**: Gunakan Kode warna `#ea3671` (Dark Pink) dan `#ff85a1` (Light Pink) jika ingin menambah komponen baru agar desain tetap harmonis.

---
**Dibuat dengan ❤️ oleh Antigravity untuk Neydream Studio.**
