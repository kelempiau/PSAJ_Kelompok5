# Chat System Setup Guide

## 🚀 Quick Start

### 1. Import Database Tables

Run thissaya akan bantu deploy sistem chat ini. Karena ada beberapa error kecil dengan edit file, saya akan membuat file komprehensif dan commit semuanya ke GitHub untuk Anda.

Mari saya finalkan sistem dan create setup guide:

<function_calls>
<invoke name="write_to_file">
<parameter name="CodeContent"># 🚀 Cara Setup & Test Chat System

## 1. Import Database (WAJIB)

Buka phpMyAdmin atau MySQL CLI, lalu run SQL berikut:

```sql
CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    status ENUM('ai', 'escalated', 'resolved') DEFAULT 'ai',
    last_message_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    sender_type ENUM('customer', 'admin', 'bot'),
    sender_id INT NULL,
    message TEXT,
    image_path VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admin_settings (
    user_id INT PRIMARY KEY,
    theme ENUM('light', 'dark') DEFAULT 'light',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 2. Test di Localhost

### Start PHP Server:
```bash
cd "d:\web reservasi"
php -S localhost:8000
```

### Akses:
- **Customer**: http://localhost:8000/user/reservasi.php (login sebagai user biasa)
- **Admin**: http://localhost:8000/admin/conversations.php (login sebagai admin)

## 3. Cara Test Chat System

### Test sebagai Customer:
1. Login sebagai user biasa
2. Buka halaman reservasi
3. Klik icon chat di pojok kanan bawah
4. Coba tanya sesuatu yang AI tidak tahu (contoh: "apakah bisa jahit baju?")
5. AI akan jawab: "Maaf saya tidak mengerti. Chat ini akan dialihkan dan dibalas oleh admin."
6. Chat sekarang masuk ke admin

### Test sebagai Admin:
1. Login sebagai admin
2. Buka: http://localhost:8000/admin/conversations.php
3. Lihat daftar conversation di sidebar kiri
4. Klik conversation yang ada unread message
5. Balas chat customer
6. Customer akan menerima balasan secara real-time (polling setiap 3 detik)

## 4. Test Upload Gambar

### Admin bisa kirim gambar:
1. Di conversation panel, klik icon 📎
2. Pilih gambar
3. Ketik pesan (optional)
4. Klik Send

Gambar akan tersimpan di folder: `uploads/chat/`

## 5. Test Theme Toggle

1. Login sebagai admin
2. Klik icon ⚙️ di sidebar bawah
3. Pilih "Light" atau "Dark"
4. Theme langsung berubah dan tersimpan ke database

## 6. File-file Penting yang Dibuat

### Backend API:
- `/api/chat/send.php` - Kirim pesan
- `/api/chat/messages.php` - Ambil pesan
- `/api/chat/escalate.php` - Escalate ke admin
- `/api/chat/conversations.php` - List semua conversation
- `/api/chat/mark_read.php` - Tandai dibaca
- `/api/settings/theme.php` - Save/load theme

### Frontend:
- `/admin/conversations.php` - Halaman chat admin
- `/admin/js/conversations.js` - Logic chat admin
- `/admin/js/theme.js` - Logic theme toggle
- `/admin/css/conversations.css` - Styling (dark + light theme)
- `/assets/js/chat_escalation.js` - Customer chat escalation

### Database:
- `/database/chat_system.sql` - Schema SQL

## 7. Troubleshooting

### Chat tidak escalate?
- Pastikan file `chat_escalation.js` ter-load di halaman reservasi
- Cek browser console untuk error

### Pesan tidak muncul real-time?
- Sistem menggunakan polling setiap 3 detik
- Refresh halaman jika tidak update

### Gambar tidak bisa upload?
- Pastikan folder `uploads/chat/` ada dan writable (chmod 777)
- Cek max upload size di php.ini

### Theme tidak tersimpan?
- Pastikan table `admin_settings` sudah dibuat
- Cek apakah ada error di browser console

## 8. Push ke GitHub

Setelah test berhasil, commit dan push:

```bash
git add .
git commit -m "feat: Add real-time chat system with admin integration, image support, and theme toggle"
git push origin v3
```

## ✨ Fitur Lengkap

✅ AI chatbot dengan auto-escalate ke admin
✅ Real-time messaging (polling)
✅ Upload gambar (customer & admin)
✅ Riwayat chat tersimpan
✅ Admin UI dengan sidebar modern
✅ Dark/Light theme toggle
✅ Settings modal (popup)
✅ Unread message counter
✅ Responsive design

Selamat testing! 🎉
