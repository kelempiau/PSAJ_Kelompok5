# 🌟 Neydream Reservation System - Documentation

Selamat datang di dokumentasi resmi **Neydream Reservation System**. Dokumen ini dirancang untuk memberikan panduan lengkap mengenai fitur, struktur, dan cara penggunaan aplikasi bagi pengguna maupun administrator.

---

## 📋 Daftar Isi
1. [Ringkasan Proyek](#-ringkasan-proyek)
2. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
3. [Fitur Utama - Sisi Pelanggan (User)](#-fitur-utama---sisi-pelanggan-user)
4. [Fitur Utama - Sisi Pengelola (Admin)](#-fitur-utama---sisi-pengelola-admin)
5. [Panduan Penggunaan (User)](#-panduan-penggunaan-user)
6. [Panduan Penggunaan (Admin)](#-panduan-penggunaan-admin)
7. [Struktur Folder](#-struktur-folder)

---

## 🚀 Ringkasan Proyek
Neydream adalah platform reservasi modern yang dirancang untuk memudahkan proses pemesanan tempat atau layanan secara real-time. Fokus utama web ini adalah memberikan pengalaman visual yang memukau (Premium UI) dan kemudahan manajemen data bagi sisi administrator.

---

## 🛠 Teknologi yang Digunakan
*   **Backend:** PHP Native (Versi 7.4/8.x compatible)
*   **Database:** MySQL
*   **Frontend:** HTML5, Vanilla CSS3 (Modern UI/UX), JavaScript ES6+
*   **Library Eksternal:**
    *   [Chart.js](https://www.chartjs.org/) - Untuk visualisasi data grafik.
    *   [Google Fonts & Inter] - Untuk tipografi modern.
    *   [FontAwesome] - Untuk ikonografi.

---

## 👤 Fitur Utama - Sisi Pelanggan (User)

### 1. Landing Page Modern
*   **Hero Section:** Tampilan pembuka yang interaktif.
*   **Paket Layanan:** List paket reservasi dengan animasi hover.
*   **Galeri:** Menampilkan hasil karya atau tempat melalui foto.
*   **Google Maps Integration:** Lokasi fisik yang interaktif.
*   **FAQ (Frequently Asked Questions):** Tanya jawab umum dengan sistem akordion.

### 2. Sistem Reservasi (Step-by-Step)
*   **Pilih Paket:** Pengguna memilih jenis layanan.
*   **Pilih Jadwal:** Pemilihan tanggal dan jam (Slot otomatis terkunci jika sudah penuh).
*   **Data Diri:** Pengisian informasi kontak.
*   **Pembayaran DP:** Pengunggahan bukti pembayaran uang muka (Down Payment).

### 3. Dashboard User
*   **History Reservasi:** Melacak status pemesanan (Pending/Approved/Paid/Finished/Canceled).
*   **Receipt Digital:** Mencetak atau melihat nota pembayaran.
*   **Pelunasan Online:** Mengunggah bukti pelunasan setelah reservasi disetujui.
*   **Request Refund:** Mengajukan pengembalian dana jika terjadi pembatalan (sesuai ketentuan).

### 4. Bantuan (Live Chat)
*   **Real-time Chat:** Komunikasi dua arah dengan admin.
*   **Smart Bot:** Jawaban otomatis untuk pertanyaan umum.
*   **Media Upload:** Mengirim gambar atau video melalui chat.

### 5. Pengaturan Profil
*   Update informasi akun (Nama, Email, WhatsApp).
*   Ganti password secara aman.
*   **Dark Mode Toggle:** Antarmuka gelap yang nyaman di mata.

---

## 🛡 Fitur Utama - Sisi Pengelola (Admin)

### 1. Dashboard Statistik
*   **Revenue & Booking Chart:** Grafik pendapatan dan jumlah pelanggan bulanan.
*   **Recap Monthly:** Laporan detail bulanan yang bisa dibuka secara popup.
*   **Live Clock:** Jam digital real-time di header.

### 2. Manajemen Data Reservasi
*   Konfirmasi pembayaran DP dan pelunasan.
*   Pencarian data pelanggan secara cepat.
*   Update status reservasi secara massal.

### 3. Kendali Komunikasi (Chat Center)
*   **Antrean Pesan:** Melihat pesan masuk berdasarkan pelanggan.
*   **Panel Chat Sliding:** Tampilan responsif untuk membalas pesan di HP/Desktop.
*   **Media Center:** Menerima bukti bayar atau foto lewat chat.

### 4. Manajemen Slot & Sistem
*   **Lock Slots:** Mengunci tanggal atau jam tertentu agar tidak bisa dipilih pelanggan (Contoh: Libur nasional atau Renovasi).
*   **User Management:** Daftar seluruh pengguna terdaftar.
*   **Feedback & Reviews:** Memantau penilaian yang diberikan oleh pelanggan.

---

## 📖 Panduan Penggunaan (User)

1.  **Pemesanan:** 
    *   Buka menu `Reservasi`.
    *   Ikuti 4 langkah hingga mengunggah bukti DP.
    *   Tunggu admin memverifikasi pesanan Anda di menu `Riwayat`.
2.  **Pelunasan:**
    *   Setelah reservasi disetujui, buka menu `Riwayat`.
    *   Klik tombol `Lunasi` untuk mengunggah bukti sisa pembayaran.
3.  **Chat:**
    *   Klik ikon chat di pojok bawah untuk bantuan cepat.

---

## 📖 Panduan Penggunaan (Admin)

1.  **Verifikasi Bayar:**
    *   Buka menu `Reservations` atau `Payments Registry`.
    *   Cek gambar bukti transfer yang dikirim pelanggan.
    *   Klik `Approve` untuk mengubah status menjadi 'Confirmed'.
2.  **Menutup Jadwal:**
    *   Buka menu `Manage Locked Slots`.
    *   Pilih tanggal dan jam, lalu klik `Kunci Slot`. Pelanggan tidak akan bisa memesan di waktu tersebut.
3.  **Analisis Data:**
    *   Buka `Dashboard`, klik tombol `Recap` pada kartu pendapatan atau reservasi untuk melihat rincian bulan tertentu.

---

## 📁 Struktur Folder
*   `/admin`: Berisi semua halaman dan logika sisi administrator.
*   `/user`: Berisi semua halaman dan logika sisi pelanggan.
*   `/api`: Endpoint untuk fungsionalitas chat dan data grafik.
*   `/assets`: File CSS, JS, dan Gambar (Logo/UI).
*   `/core`: Pengaturan database (`config.php`).
*   `/uploads`: Tempat penyimpanan bukti pembayaran (DP/Lunas) dan media chat.
*   `/database`: Berisi file `.sql` untuk skema database.

---
*Dokumentasi ini dibuat untuk mempermudah serah terima dan pengembangan web kedepannya.*
