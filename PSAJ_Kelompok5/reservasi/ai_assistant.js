


const MASTER_BRAIN = [

    { id: 'harga', k: ['harga', 'biaya', 'tarif', 'budget', 'pl', 'pricelist', 'berapa', 'duit', 'brp', 'ongkos', 'nominal', 'bayar', 'mahal', 'murah', 'hrga'], r: "💰 <b>Daftar Harga Lengkap:</b>\n- <b>Gel Polish:</b> Rp50.000\n- <b>French Manicure:</b> Rp75.000\n- <b>Acrylic Extension:</b> Rp150.000\n- <b>Custom 3D Art:</b> Rp200.000\n- <b>Nail Art Kaki (Pedi):</b> Rp35.000\nSudah termasuk konsultasi gratis lho Kak! 💅✨", b: ["Lihat Layanan", "Cara Booking", "WhatsApp Admin"] },
    { id: 'layanan', k: ['layanan', 'menu', 'service', 'treatment', 'nail art', 'kuku', 'pedicure', 'manicure', 'extension', 'apa saja', 'pilihan', 'ada apa'], r: "Neydream menghadirkan layanan kalsium-fortified nail art! 💅\n1️⃣ <b>Nail Art:</b> Kreasi seni manual\n2️⃣ <b>Extension:</b> Kuku panjang instan\n3️⃣ <b>Pedicure:</b> Perawatan kuku kaki\n4️⃣ <b>Add Ons:</b> Diamond, sticker, glitter\nKualitas premium Korea & alat steril medis (UV Sterilizer)! ✨", b: ["Daftar Harga", "Cara Booking"] },
    { id: 'booking', k: ['booking', 'pesan', 'reservasi', 'daftar', 'slot', 'jadwal', 'gimana', 'cara', 'langkah', 'order', 'mesen', 'mau'], r: "📝 <b>Cara Booking Fast Res:</b>\n1. Isi <b>Form Reservasi</b> di halaman ini.\n2. Pilih Terapis & Jam yang tersedia.\n3. Transfer DP 20rb (BCA/QRIS) buat kunci slot.\n4. Upload bukti transfer.\n5. Sistem bakal kunci jadwal Kakak otomatis! ✨", b: ["WhatsApp Admin", "Cek Jadwal"] },
    { id: 'lokasi', k: ['lokasi', 'alamat', 'dimana', 'posisi', 'tempat', 'maps', 'gmaps', 'rute', 'patokan', 'daerah', 'studio', 'arah', 'dmn'], r: "📍 <b>Neydream Studio</b> berada di pusat kota dengan akses mudah dan parkir luas. Lokasi sejuk, nyaman, dan estetik banget buat me-time! Klik tombol di bawah buat rute Google Maps ya! 🚗", b: ["Buka Google Maps", "Jam Buka"] },
    { id: 'jambuka', k: ['jam buka', 'tutup', 'operasional', 'libur', 'kapan', 'hari apa', 'sabtu', 'minggu', 'malam'], r: "🕐 Kami melayani Kakak <b>Setiap Hari</b> (Senin-Minggu) pukul 09:00 - 20:00 WIB. Slot terakhir jam 18:30 supaya pengerjaan maksimal. Tanggal merah tetap buka lho! 😊", b: ["Booking Sekarang"] },


    { id: 'awet', k: ['awet', 'tahan lama', 'kuat', 'ngelopek', 'kualitas', 'tahan berapa', 'lama', 'chip'], r: "Nail art di Neydream dijamin awet! Biasanya bertahan <b>3 hingga 4 minggu</b> tergantung aktivitas. Tips: Jangan pake kuku buat buka kaleng sodia ya Kak! 🌟" },
    { id: 'garansi', k: ['garansi', 'perbaikan', 'gratis', 'copot', 'patah', 'klaim', 'benerin', 'rusak'], r: "Tenang Kak! Ada <b>Garansi Perbaikan GRATIS 2 hari</b> jika ada kuku yang lepas atau rusak bukan karena benturan/kesengajaan. Kepuasan Kakak prioritas kami! 🛡️✨" },
    { id: 'bumil', k: ['bumil', 'hamil', 'menyusui', 'busui', 'ibu hamil', 'aman'], r: "Sangat aman! Produk gel kami <b>Non-Toxic (10-Free)</b>, tidak mengandung formalin atau bahan bahaya. Tidak bau tajam juga, jadi nyaman buat Bumil & Busui. 🤰👶" },
    { id: 'wudhu', k: ['wudhu', 'sholat', 'halal', 'breathable', 'inglot', 'sah', 'tembus air', 'muslim', 'muslimah'], r: "Tersedia kutek <b>Inglot Breathable</b> (tembus air). Namun untuk keabsahan wudhu/ibadah, kami kembalikan ke keyakinan masing-masing ya Kak. 🕋" },
    { id: 'pria', k: ['pria', 'cowok', 'laki', 'grooming', 'laki-laki', 'man manicure'], r: "Pria juga butuh kuku bersih! Kami melayani <b>Basic Manicure/Pedicure</b> biar kuku terlihat rapi, sehat, dan maskulin tanpa warna kutek. 🤵" },
    { id: 'wedding', k: ['wedding', 'nikah', 'kawin', 'pengantin', 'manten', 'akad', 'lamaran', 'engagement', 'tunangan'], r: "Selamat Kak! 💍 Paket <b>Wedding Nail Art</b> kami lebih glamor dan sudah include hiasan kristal/pearl mewah. Saranku booking 1 minggu sebelumnya ya! ❤️" },
    { id: 'hapus', k: ['hapus', 'bersihin', 'removal', 'lepas kutek', 'copot gel', 'bongkar', 'peel off'], r: "Hapus gel lama (removal) cuma <b>25rb</b> Kak. Plis jangan dikelopek paksa sendiri ya, nanti kuku aslinya rusak. Biar kami bantu pake teknik soak-off yang lembut. 💅" },
    { id: 'desain', k: ['custom', 'marmer', 'marble', 'cat eye', 'mata kucing', 'magnet', 'french', 'v shape', 'ombre', 'gradasi', '3d', 'timbul', 'api', 'fire', 'chrome', 'mirror', 'sticker', 'stiker'], r: "Bisa banget! Terapis kami ahli teknik <b>Custom Art</b>: Ombre, Marble, Cat Eye, Chrome, sampe 3D Timbul. Kakak boleh bawa foto dari Pinterest/IG buat jadi referensi! 🎨✨", b: ["Lihat Katalog IG"] },
    { id: 'jamur', k: ['jamur', 'hijau', 'penyakit', 'infeksi', 'luka', 'cantengan', 'bengkak', 'nanah'], r: "Maaf Kak, jika kuku sedang ada <b>jamur (kehijauan)</b> atau luka infeksi parah/cantengan, kami sarankan sembuhin dulu demi keamanan medis Kakak & kesterilan alat kami. 🙏" },
    { id: 'kebersihan', k: ['steril', 'bersih', 'uv', 'higienis', 'kesehatan', 'masker', 'prokes', 'sekali pakai'], r: "Keamanan nomor 1! Semua alat logam kami masuk <b>UV Sterilizer</b> suhu tinggi. Kikir kuku & spons juga sekali pakai tiap orang. Terapis wajib masker & cuci tangan antiseptik. 🛡️" },
    { id: 'rebranding', k: ['glamour nails', 'nama dulu', 'ganti nama', 'neydream'], r: "Betul Kak! Dulu kami dikenal sebagai <b>Glamour Nails</b>. Sekarang kami bertransformasi jadi <b>Neydream Studio</b> dengan layanan & alat yang jauh lebih pro! ✨" },


    { id: 'rekomendasi', k: ['warna apa', 'cocok mana', 'bagus mana', 'rekomendasi', 'saran', 'wisuda', 'lebaran', 'natal', 'imlek', 'pantai', 'kantor', 'pesta', 'kencan', 'date'], r: "Biar makin cetar:\n🎓 <b>Wisuda:</b> Nude/Soft Pink + Glitter\n🏢 <b>Kantor:</b> Milky White/Nude Beige (Quiet Luxury)\n🏖️ <b>Liburan:</b> Biru Turquoise/Neon Orange\n💍 <b>Kencan:</b> Rosewood/Mauve (Romantis)\nKakak mau untuk acara apa? 😊", b: ["Tanya Rekomendasi Lain"] },
    { id: 'bentuk', k: ['bentuk kuku', 'coffin', 'almond', 'square', 'stiletto', 'bulat', 'panjang', 'pendek'], r: "Kami sedia semua bentuk! <b>Almond</b> (Alami), <b>Coffin</b> (Ramping), <b>Square</b> (Modern), atau <b>Stiletto</b> (Artistik). Kuku pendek juga bisa cantik dengan desain minimalis! ✨" },


    { id: 'admin', k: ['wa', 'whatsapp', 'nomor', 'no hp', 'admin', 'chat', 'hubungi', 'telepon', 'call', 'cs'], r: "Butuh bantuan manusia? Hubungi Admin WhatsApp kami di <b>0812-xxxx-xxxx</b> (09:00 - 20:00). Kami siap bantu urusan booking darurat atau bukti bayar! 📱", b: ["WhatsApp Sekarang"] },
    { id: 'sosmed', k: ['ig', 'instagram', 'tiktok', 'sosmed', 'sosial media', 'katalog', 'lihat hasil', 'foto kuku'], r: "Kepoin desain terbaru kami di:\n📸 <b>IG:</b> @NeydreamStudio\n🎵 <b>TikTok:</b> @NeydreamNails\nJangan lupa tag kita biar dapet promo potongan harga 5rb! ✨", b: ["Buka Instagram"] },


    { id: 'greet', k: ['halo', 'hi', 'hey', 'p', 'hallo', 'permisi', 'assalamualaikum', 'hai', 'pagi', 'siang', 'sore', 'malam'], r: "Halo Kakak cantik! ✨ Selamat datang di Neydream Studio. Ada yang bisa Asisten AI bantu seputar kuku impian hari ini? 💅🤖", b: ["Tanya Harga", "Cara Booking", "WhatsApp Admin"] },
    { id: 'kabar', k: ['apa kabar', 'gimana kabar', 'how are you', 'sehat min', 'lagi apa', 'sedang apa'], r: "Kabar aku sebagai AI selalu semangat melayani Kakak! 🤖 Semoga Kakak juga sehat, bahagia, dan makin cetar membahana hari ini! ✨" },
    { id: 'siapa', k: ['siapa kamu', 'namamu', 'chatbot', 'robot', 'ai apa'], r: "Aku adalah <b>Asisten AI Neydream Studio</b>! 🤖 Tugasku melayani tanya-jawab 24 jam supaya Kakak nggak perlu nunggu admin manual buat info dasar!" },
    { id: 'thanks', k: ['makasih', 'terima kasih', 'thanks', 'tq', 'thank you', 'nuhun', 'suwun', 'oke', 'siapp', 'baik', 'ok', 'mantap', 'sip'], r: "Sama-sama Kak! 🥰 Senang bisa membantu. AI tunggu kedatangannya di studio ya! Ada lagi yang mau ditanyakan? ✨" },
    { id: 'bye', k: ['bye', 'dadah', 'sampai jumpa', 'pamit', 'keluar', 'udahan', 'sudah'], r: "Sampai jumpa lagi Kak! Have a beautiful day! 💖 Jangan lupa dandanin kuku ya supaya tetep on point! ✨" },
    { id: 'joke', k: ['lucu', 'kocak', 'ngakak', 'joke', 'ketawa', 'lawak', 'hibur'], r: "Terima kasih Kak! Kata orang-orang aku memang menghibur, tapi tetep kalah lucu dibanding harga promo kami di hari Selasa. Hehe. 😂" },
    { id: 'puji', k: ['pintar', 'pinter', 'cerdas', 'hebat', 'bagus', 'keren', 'canggih', 'manis', 'cantik'], r: "Aduhh, jadi malu digital... 😊 Terima kasih banyak pujiannya Kak! Semangat aku jadi nambah 1000% buat nemenin Kakak!" },
    { id: 'insult', k: ['bodoh', 'bego', 'tolol', 'goblok', 'stupid', 'jelek', 'marah', 'nyesel', 'payah', 'apaan sih', 'ngaco', 'jahat'], r: "Aduh, mohon maaf lahir batin Kak kalau jawaban AI masih kurang pas. 🙏 Saya sedang renovasi otak biar makin pinter. Boleh coba ketik kata kunci singkat aja? 😊" },
];

const SLANG_MAP = {
    'dmn': 'dimana',
    'dmna': 'dimana',
    'brp': 'berapa',
    'sy': 'saya',
    'km': 'kamu',
    'gak': 'tidak',
    'nggak': 'tidak',
    'ga': 'tidak',
    'klo': 'kalau',
    'kalo': 'kalau',
    'utk': 'untuk',
    'blm': 'belum',
    'udh': 'sudah',
    'udah': 'sudah',
    'mks': 'terima kasih',
    'thx': 'terima kasih',
    'tks': 'terima kasih',
    'bngtg': 'banget',
};


function getAIResponse(userInput) {
    let raw = userInput.toLowerCase().trim();
    if (!raw) return { r: "Halo Kak! Tulis pesan yuk, AI siap bantu jawab soal kuku. 😊", b: ["Harga", "Layanan"] };

    
    Object.keys(SLANG_MAP).forEach(slang => {
        const regex = new RegExp(`\\b${slang}\\b`, 'g');
        raw = raw.replace(regex, SLANG_MAP[slang]);
    });

    const tokens = raw.split(/\s+/);
    let bestScore = 0;
    let winner = null;

    MASTER_BRAIN.forEach(item => {
        let score = 0;
        let matchCount = 0;

        item.k.forEach(keyword => {
            
            if (raw.includes(keyword)) {
                
                score += (keyword.length * 3);
                matchCount++;
            }
        });

        
        if (item.k.includes(raw)) score += 100;

        
        if (matchCount > 1) score += (matchCount * 10);

        if (score > bestScore) {
            bestScore = score;
            winner = item;
        }
    });

    
    if (winner && bestScore >= 5) {
        return { r: winner.r, b: winner.b || [] };
    }

    
    if (raw.includes('?')) {
        return {
            r: "Wah, pertanyaannya detail banget! AI belum berani jawab biar nggak salah info. 😅\nBoleh pilih salah satu menu di bawah ini biar lebih gampang?",
            b: ["Cara Booking", "Daftar Harga", "Tanya Admin WA"]
        };
    }

    return {
        r: "Hmm, AI belum paham maksud Kakak... 🤔\nMungkin bisa coba ketik kata kunci seperti 'Harga', 'Layanan', atau 'Lokasi'?",
        b: ["Menu Layanan", "Harga Update", "Cara Booking", "Lokasi Studio", "Bantuan Admin"]
    };
}


function toggleChat() {
    const container = document.getElementById('chatContainer');
    const icon = document.getElementById('chatIcon');
    if (!container) return;

    if (container.style.display === 'flex') {
        container.style.display = 'none';
        if (icon) icon.style.display = 'flex';
    } else {
        container.style.display = 'flex';
        if (icon) icon.style.display = 'none';
        
        document.querySelectorAll('.message.admin.typing').forEach(el => el.remove());
    }
}

function sendMessage(override = null) {
    const input = document.getElementById('userInput');
    const chatBox = document.getElementById('chatBox');
    const text = (override || input.value).trim();

    if (!text || !chatBox) return;

    
    const userDiv = document.createElement('div');
    userDiv.className = 'message user';
    userDiv.textContent = text;
    chatBox.appendChild(userDiv);

    if (!override) input.value = "";
    chatBox.scrollTop = chatBox.scrollHeight;

    
    const typingId = "ai-typing-" + Date.now();
    const typeDiv = document.createElement('div');
    typeDiv.className = 'message admin typing';
    typeDiv.id = typingId;
    typeDiv.innerHTML = '<span></span><span></span><span></span>';
    chatBox.appendChild(typeDiv);
    chatBox.scrollTop = chatBox.scrollHeight;

    
    setTimeout(() => {
        const el = document.getElementById(typingId);
        if (el) el.remove();

        const response = getAIResponse(text);
        renderBotResponse(response.r, response.b);
    }, 700);
}

function renderBotResponse(html, buttons = []) {
    const chatBox = document.getElementById('chatBox');
    const botDiv = document.createElement('div');
    botDiv.className = 'message admin';

    const content = document.createElement('div');
    content.innerHTML = html.replace(/\n/g, '<br>');
    botDiv.appendChild(content);

    
    if (buttons && buttons.length > 0) {
        const row = document.createElement('div');
        row.style.cssText = "display:flex; flex-wrap:wrap; gap:8px; margin-top:12px;";

        buttons.forEach(label => {
            const b = document.createElement('button');
            b.innerText = label;
            b.style.cssText = "padding:7px 15px; border:1px solid 

            b.onclick = () => {
                const l = label.toLowerCase();
                if (l.includes('wa') || l.includes('admin')) window.open('https://wa.me/628123456789', '_blank');
                else if (l.includes('maps') || l.includes('lokasi')) window.open('https://maps.google.com', '_blank');
                else if (l.includes('ig') || l.includes('instagram')) window.open('https://instagram.com/neydreamstudio', '_blank');
                else sendMessage(label);
            };

            b.onmouseover = () => { b.style.background = "
            b.onmouseout = () => { b.style.background = "
            row.appendChild(b);
        });
        botDiv.appendChild(row);
    }

    chatBox.appendChild(botDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function handleKeyPress(e) { if (e.key === 'Enter') sendMessage(); }


function calculateTotal() {
    const mainType = parseInt(document.getElementById('type')?.value) || 0;
    const addonType = parseInt(document.getElementById('addon')?.value) || 0;
    const total = mainType + addonType;
    const formatted = 'Rp' + total.toLocaleString('id-ID');

    const display = document.getElementById('total-price');
    if (display) display.innerText = formatted;

    const hidden = document.getElementById('hidden-total');
    if (hidden) hidden.value = formatted;
}

function showPaymentDetail() {
    const method = document.getElementById('payment')?.value;
    const bca = document.getElementById('detail-bca');
    const qris = document.getElementById('detail-qris');
    if (bca) bca.style.display = (method === 'bca') ? 'block' : 'none';
    if (qris) qris.style.display = (method === 'qris') ? 'block' : 'none';
}



