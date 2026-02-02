

let chatHistory = [];
let userContext = {
    hasAskedPrice: false,
    hasAskedLocation: false,
    wantsToBook: false,
    preferredService: null
};


const knowledgeBase = {
    
    'cara_booking': {
        triggers: ['cara pesan', 'cara booking', 'gimana pesan', 'gimana booking', 'mau pesan', 'mau booking', 'how to book', 'pesan gimana', 'booking gimana', 'mesennya', 'bookingnya'],
        response: `📝 <b>Cara Booking Super Gampang:</b>\n\n1️⃣ <b>Isi form</b> di halaman ini (scroll ke atas)\n2️⃣ <b>Pilih tanggal & jam</b> yang diinginkan\n3️⃣ <b>Pilih layanan</b> (Gel Polish, Extension, dll)\n4️⃣ <b>Transfer DP 20rb</b> ke:\n   • BCA: 123-456-7890\n   • atau QRIS\n5️⃣ <b>Upload bukti transfer</b>\n6️⃣ <b>Tunggu konfirmasi</b> dari admin (max 1 jam)\n\nSelesai! Gampang kan? 😊\n\nLangsung isi form aja ya!`,
        keywords: ['cara', 'pesan', 'booking', 'gimana', 'mau', 'mesennya', 'bookingnya']
    },

    
    'harga': {
        triggers: ['harga', 'biaya', 'tarif', 'berapa', 'price', 'cost', 'mahal', 'murah', 'hrga', 'biayanya'],
        response: `💰 <b>Daftar Harga Lengkap:</b>\n\n💅 <b>Gel Polish:</b> Rp50.000\n✨ <b>French Manicure:</b> Rp75.000\n💎 <b>Acrylic Extension:</b> Rp150.000\n🎨 <b>Custom 3D Nail Art:</b> Rp200.000\n🦶 <b>Nail Art Kaki:</b> Rp35.000\n\n<b>Add Ons:</b>\n• Diamond: +10rb\n• Glitter: +15rb\n• Removal gel lama: +25rb\n\n<b>Semua harga SUDAH INCLUDE:</b>\n✅ Konsultasi desain gratis\n✅ Garansi 2 hari\n✅ Alat steril\n\nMau booking yang mana? 💅`,
        keywords: ['harga', 'biaya', 'tarif', 'berapa', 'price']
    },

    
    'lokasi': {
        triggers: ['lokasi', 'alamat', 'dimana', 'di mana', 'posisi', 'tempat', 'maps', 'gmaps', 'dmana', 'lokasinya', 'tempatnya'],
        response: `📍 <b>Lokasi Neydream Studio:</b>\n\nKami berada di <b>pusat kota</b> dengan akses mudah!\n\n<b>Untuk alamat lengkap & Google Maps:</b>\n📱 Hubungi Admin WA: <b>0812-xxxx-xxxx</b>\n\n<b>Benefit lokasi kami:</b>\n✅ Mudah diakses\n✅ Parkir luas (mobil & motor)\n✅ Dekat transportasi umum\n✅ Tempat nyaman & cozy\n\nDitunggu kedatangannya! 🚗`,
        keywords: ['lokasi', 'alamat', 'dimana', 'posisi', 'tempat']
    },

    
    'jam_buka': {
        triggers: ['jam buka', 'jam tutup', 'buka jam', 'tutup jam', 'jam berapa', 'kapan buka', 'operasional', 'libur'],
        response: `🕐 <b>Jam Operasional:</b>\n\n📅 <b>Setiap hari</b> (Senin-Minggu)\n⏰ <b>09:00 - 20:00 WIB</b>\n\n<b>TETAP BUKA</b> di hari libur nasional! ✅\n\n<b>Catatan:</b>\n• Slot terakhir: 18:30 WIB\n• Untuk Extension: maksimal slot 17:00\n\nMau booking jam berapa? 😊`,
        keywords: ['jam', 'buka', 'tutup', 'operasional', 'libur']
    },

    
    'layanan': {
        triggers: ['layanan', 'service', 'menu', 'paket', 'treatment', 'apa aja', 'ada apa'],
        response: `💅 <b>Layanan Lengkap Kami:</b>\n\n1. <b>Gel Polish</b> - Rp50k\n   Warna solid, tahan 3-4 minggu\n\n2. <b>French Manicure</b> - Rp75k\n   Klasik & elegan\n\n3. <b>Acrylic Extension</b> - Rp150k\n   Kuku panjang instan!\n\n4. <b>Custom 3D Nail Art</b> - Rp200k\n   Desain suka-suka, bawa referensi!\n\n5. <b>Nail Art Kaki</b> - Rp35k\n   Pedicure + nail art\n\n6. <b>Add Ons</b> - mulai Rp2k\n   Diamond, glitter, sticker, dll\n\n<b>Bisa combo layanan!</b> 🎨\n\nMau yang mana?`,
        keywords: ['layanan', 'service', 'menu', 'paket']
    },

    
    'durasi': {
        triggers: ['lama', 'durasi', 'waktu', 'berapa lama', 'selesai', 'butuh waktu'],
        response: `⏱ <b>Durasi Treatment:</b>\n\n• <b>Gel Polish:</b> 45-60 menit\n• <b>French Manicure:</b> 60-75 menit\n• <b>Extension:</b> 1.5-2 jam\n• <b>Custom 3D:</b> 2-2.5 jam\n• <b>Pedicure:</b> 45-60 menit\n\n<b>Note:</b> Waktu bisa bervariasi tergantung detail desain.\n\nSambil treatment:\n📱 WiFi gratis\n☕ Free drink\n📚 Majalah\n\nSantai aja, hasil maksimal! 💯`,
        keywords: ['lama', 'durasi', 'waktu', 'berapa lama']
    },

    
    'kualitas': {
        triggers: ['awet', 'tahan lama', 'kuat', 'kualitas', 'bagus', 'tahan berapa', 'berkualitas'],
        response: `🌟 <b>Kualitas Premium Terjamin!</b>\n\n<b>Gel Polish kami:</b>\n✅ Tahan 3-4 minggu\n✅ Tidak mudah terkelupas\n✅ Warna tetap vibrant\n✅ Brand premium Korea\n✅ Formula non-toxic\n\n<b>GARANSI:</b>\n🛡 Perbaikan GRATIS 2 hari pertama\n(jika copot bukan karena benturan)\n\n<b>Tips supaya awet:</b>\n🧤 Pakai sarung tangan cuci piring\n💧 Hindari rendam air panas lama\n💅 Pakai hand cream rutin\n\nDijamin puas! 💯`,
        keywords: ['awet', 'tahan', 'kuat', 'kualitas', 'bagus']
    },

    
    'promo': {
        triggers: ['promo', 'diskon', 'potongan', 'sale', 'bonus', 'gratis', 'free', 'promosi'],
        response: `🎉 <b>PROMO SPESIAL!</b>\n\n🎁 <b>Member Baru:</b>\n   Diskon 10% booking pertama!\n\n💎 <b>Member Setia:</b>\n   FREE 1x Gel Polish tiap 5x visit\n\n🎂 <b>Birthday Special:</b>\n   Diskon 15% di hari ultah\n   (tunjukkan KTP)\n\n👯 <b>Ajak Teman (3+ orang):</b>\n   Diskon 10% untuk semua!\n\n📱 <b>Tag IG Story:</b>\n   Potongan 5rb next visit\n   (@NeydreamStudio)\n\nManfaatkan promonya! 💖`,
        keywords: ['promo', 'diskon', 'potongan', 'sale']
    },

    
    'pembayaran': {
        triggers: ['bayar', 'payment', 'transfer', 'bca', 'qris', 'dana', 'ovo', 'gopay', 'cash', 'metode bayar'],
        response: `💳 <b>Metode Pembayaran:</b>\n\n🏦 <b>Transfer BCA:</b>\n   Rek: 123-456-7890\n   a/n: Neydream Studio\n\n📱 <b>QRIS (Semua E-Wallet):</b>\n   • GoPay, OVO, DANA, ShopeePay\n   • Scan QR di halaman booking\n\n💵 <b>Cash di Studio:</b>\n   Bayar setelah selesai\n\n<b>⚠️ DP Booking:</b>\nMinimal <b>Rp20.000</b> untuk lock slot\n(Dikurangi dari total)\n\nAman & tercatat! 🔒`,
        keywords: ['bayar', 'payment', 'transfer', 'bca', 'qris']
    },

    
    'kebersihan': {
        triggers: ['bersih', 'steril', 'higienis', 'aman', 'hygiene', 'sehat', 'infeksi'],
        response: `🛡 <b>Kebersihan = Prioritas 
        keywords: ['bersih', 'steril', 'higienis', 'aman']
    },

    
    'custom_design': {
        triggers: ['custom', 'desain', 'design', 'referensi', 'pinterest', 'instagram', 'foto', 'gambar', 'bawa foto'],
        response: `🎨 <b>Custom Design? BISA!</b>\n\n<b>Bawa referensi dari:</b>\n📌 Pinterest\n📸 Instagram\n🎥 TikTok\n💭 Ide sendiri\n\n<b>Terapis kami ahli dalam:</b>\n✅ Replikasi desain rumit\n✅ 3D nail art\n✅ Kombinasi warna\n✅ Konsultasi style\n\n<b>Proses:</b>\n1. Tunjukkan referensi\n2. Diskusi sama terapis\n3. Pilih warna final\n4. Magic time! ✨\n\nHasil mirip 95%+ guaranteed! 💯`,
        keywords: ['custom', 'desain', 'referensi', 'pinterest']
    },

    
    'kontak': {
        triggers: ['kontak', 'hubungi', 'whatsapp', 'wa', 'admin', 'cs', 'customer service', 'telepon', 'telp'],
        response: `📞 <b>Hubungi Kami:</b>\n\n📱 <b>WhatsApp Admin:</b>\n   0812-xxxx-xxxx\n   (Aktif 09:00-20:00)\n   Fast response!\n\n📧 <b>Email:</b>\n   hello@neydream.com\n\n📷 <b>Instagram:</b>\n   @NeydreamStudio\n   DM always open!\n\n🎵 <b>TikTok:</b>\n   @NeydreamNails\n   Video ASMR & tutorial\n\nDitunggu! 💕`,
        keywords: ['kontak', 'hubungi', 'whatsapp', 'wa', 'admin']
    }
};


const greetings = [
    "Halo Kak! ✨ Selamat datang di Neydream Studio. Ada yang bisa saya bantu?",
    "Hi! 💅 Mau tanya-tanya seputar nail art? Langsung aja ya!",
    "Hai Kak! Senang bisa membantu. Mau booking atau tanya-tanya dulu nih?",
    "Hello! Asisten Neydream siap bantu. Ada yang ingin ditanyakan?"
];

const thanks = [
    "Sama-sama Kak! 🥰 Ada lagi yang mau ditanya?",
    "You're welcome! Jangan sungkan tanya-tanya ya~",
    "Siap! Kalau ada yang kurang jelas, langsung tanya! 😊",
    "Dengan senang hati! Semoga membantu! ✨"
];

const farewells = [
    "Sampai jumpa! Ditunggu bookingnya ya 💖",
    "Bye! Jangan lupa follow IG kami @NeydreamStudio! ✨",
    "See you! Semoga jadi customer setia 💅",
    "Dadah! Have a beautiful day! 🌸"
];


function sendMessage() {
    const input = document.getElementById('userInput');
    const chatBox = document.getElementById('chatBox');
    const userText = input.value.trim();

    if (userText === "") return;

    addMessageToBox('user', userText);
    chatHistory.push({ role: 'user', text: userText });

    input.value = "";
    chatBox.scrollTop = chatBox.scrollHeight;

    showTypingIndicator();

    setTimeout(() => {
        hideTypingIndicator();
        const response = getIntelligentResponse(userText);
        addMessageToBox('admin', response);
        chatHistory.push({ role: 'bot', text: response });
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 600);
}

function addMessageToBox(sender, text) {
    const chatBox = document.getElementById('chatBox');
    const msg = document.createElement('div');
    msg.className = `message ${sender}`;
    msg.innerHTML = text.replace(/\n/g, '<br>');
    chatBox.appendChild(msg);
}

function handleKeyPress(e) {
    if (e.key === "Enter") sendMessage();
}

function showTypingIndicator() {
    const chatBox = document.getElementById('chatBox');
    const typing = document.createElement('div');
    typing.id = 'typing-indicator';
    typing.className = 'message admin typing';
    typing.innerHTML = '<span></span><span></span><span></span>';
    chatBox.appendChild(typing);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typing-indicator');
    if (indicator) indicator.remove();
}


function getIntelligentResponse(userInput) {
    const text = userInput.toLowerCase().trim();

    
    if (/^(halo|hai|hi|hey|hallo|pagi|siang|sore|malam|assalamualaikum|salam|permisi)$/i.test(text)) {
        return greetings[Math.floor(Math.random() * greetings.length)];
    }

    
    if (/makasih|terima kasih|thanks|thank you|thx|tq/i.test(text)) {
        return thanks[Math.floor(Math.random() * thanks.length)];
    }

    
    if (/bye|dadah|sampai jumpa|pamit|goodbye/i.test(text)) {
        return farewells[Math.floor(Math.random() * farewells.length)];
    }

    
    let bestMatch = null;
    let highestScore = 0;

    for (let key in knowledgeBase) {
        const kb = knowledgeBase[key];
        let score = 0;

        
        for (let trigger of kb.triggers) {
            if (text.includes(trigger)) {
                score += 15;
            }
        }

        
        for (let keyword of kb.keywords) {
            if (text.includes(keyword)) {
                score += 5;
            }
        }

        if (score > highestScore) {
            highestScore = score;
            bestMatch = kb;
        }
    }

    
    if (bestMatch && highestScore >= 10) {
        return bestMatch.response;
    }

    
    return getFallbackResponse(text);
}

function getFallbackResponse(text) {
    
    if (/apa|gimana|bagaimana|kenapa|mengapa|berapa|dimana|\?/i.test(text)) {
        return `Hmm, saya belum paham pertanyaannya nih 🤔\n\nCoba tanya tentang:\n\n💰 Harga layanan\n📅 Cara booking\n📍 Lokasi studio\n🕐 Jam buka\n💅 Jenis layanan\n🎨 Custom design\n📞 Kontak admin\n\nPilih salah satu ya Kak!`;
    }

    
    const fallbacks = [
        "Oke! Ada yang bisa saya bantu lebih lanjut? 😊",
        "Noted! Mau tanya tentang harga, booking, atau yang lain?",
        "Siap! Kalau mau booking atau tanya-tanya, langsung aja ya! 💅",
        "Okay! Feel free to ask anything tentang Neydream Studio! ✨"
    ];

    return fallbacks[Math.floor(Math.random() * fallbacks.length)];
}


function toggleChat() {
    const container = document.getElementById("chatContainer");
    const icon = document.getElementById("chatIcon");

    if (container.style.display === "flex" || container.style.display === "") {
        container.style.display = "none";
        icon.style.display = "flex";
    } else {
        container.style.display = "flex";
        icon.style.display = "none";
    }
}


const style = document.createElement('style');
style.textContent = `
    .message.typing {
        display: flex;
        gap: 5px;
        padding: 10px 15px;
        background: 
        border-radius: 10px;
        width: fit-content;
    }
    .message.typing span {
        width: 8px;
        height: 8px;
        background: 
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    .message.typing span:nth-child(2) {
        animation-delay: 0.2s;
    }
    .message.typing span:nth-child(3) {
        animation-delay: 0.4s;
    }
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.7; }
        30% { transform: translateY(-10px); opacity: 1; }
    }
`;
document.head.appendChild(style);



