/**
 * ULTRA SMART AI ASSISTANT v20.0 (UNIVERSAL ATOMIC BRAIN)
 * The Ultimate Merger of reservasi.js + smart_chatbot.js + modern logic.
 * Developed by Antigravity for Neydream Studio.
 */

// ================================================
// 1. DATA: THE MASTER BRAIN (MAPPED FROM ALL FILES)
// ================================================

// This mapping is derived from the switch-like match conditions in your original files.
// It ensures that not a single piece of specialized knowledge or humor is lost.
const MASTER_BRAIN = [
    // --- CORE BUSINESS (PRIORITY HIGH) ---
    { k: /(harga|biaya|tarif|budget|pl|pricelist|berapa|duit|ongkos|nominal|bayar|mahal|murah|hrga)/, r: "Harga kami sangat jujur & bersahabat mulai dari <b>Rp50.000</b> (Gel Polish) sampai <b>Rp200.000</b> (Custom 3D Art). Kualitas premium Korea & alat steril medis (UV Sterilizer). Investasi terbaik buat kuku cantik Kakak! 💰✨", b: ["Lihat Layanan", "Cek Lokasi", "Cara Booking"] },
    { k: /(layanan|menu|service|treatment|nail art|kuku|pedicure|manicure|extension|apa saja|pilihan)/, r: "Neydream Studio menghadirkan layanan kalsium-fortified nail art! 💅\n- <b>Gel Polish:</b> Rp50.000\n- <b>French Manicure:</b> Rp75.000\n- <b>Acrylic Extension:</b> Rp150.000\n- <b>Custom 3D Art:</b> Rp200.000\nSudah termasuk konsultasi desain gratis lho!", b: ["Daftar Harga", "Cara Booking"] },
    { k: /(booking|pesan|reservasi|daftar|slot|jadwal|gimana|cara|langkah|order|mesen)/, r: "📝 <b>Cara Booking Fast Res:</b>\n1. Isi <b>Form Reservasi</b> di atas.\n2. Pilih Terapis & Jam yang masih hijau (tersedia).\n3. Bayar DP 20rb ke BCA/QRIS.\n4. Upload bukti transfer.\n5. Sistem akan mengunci slot Kakak secara otomatis! ✨", b: ["WhatsApp Admin", "Cek Jadwal"] },
    { k: /(lokasi|alamat|dimana|posisi|tempat|maps|gmaps|rute|patokan|daerah|studio|rute|arah)/, r: "📍 <b>Neydream Studio</b> berada di pusat kota dengan akses mudah dan parkir luas. Klik tombol di bawah buat dapet rute tercepat via Google Maps ya! 🚗", b: ["Buka Google Maps", "Jam Buka"] },
    { k: /(jam buka|tutup|operasional|libur|kapan|hari apa|sabtu|minggu|malam)/, r: "🕐 Kami siap melayani Kakak <b>Setiap Hari</b> (Senin-Minggu) pukul 09:00 - 20:00 WIB. Slot terakhir jam 18:30 supaya pengerjaan maksimal ya Kak! 😊", b: ["Booking Sekarang"] },

    // --- POLICY & TECHNICAL (THE BRAIN) ---
    { k: /(awet|tahan lama|kuat|ngelopek|kualitas|tahan berapa|lama)/, r: "Nail art di Neydream dijamin awet dan kuat! Biasanya bertahan <b>3 hingga 4 minggu</b> tergantung aktivitas Kakak. Tips: Jangan pake kuku buat buka kaleng atau disikat kasar ya Kak! 🌟" },
    { k: /(garansi|perbaikan|service gratis|copot|patah|klaim|benerin|rusak)/, r: "Ada <b>Garansi Perbaikan GRATIS selama 2 hari</b> jika ada kuku yang lepas atau rusak bukan karena benturan/kesengajaan ya Kak! Kepuasan Kakak jaminan kami. 🛡️✨" },
    { k: /(bumil|hamil|menyusui|busui|ibu hamil|aman)/, r: "Sangat aman! Produk gel kami <b>Non-Toxic (10-Free)</b>, tidak mengandung bahan bahaya. Tidak berbau tajam juga, jadi sangat nyaman buat Bumil & Busui. 🤰👶" },
    { k: /(wudhu|sholat|halal|breathable|inglot|sah|tembus air|muslim)/, r: "Tersedia pilihan kutek <b>Inglot Breathable</b> (tembus air), namun untuk keabsahan wudhu/ibadah kami serahkan kembali ke pilihan keyakinan Kakak masing-masing. 🕋" },
    { k: /(pria|cowok|laki|grooming|laki-laki|man manicure)/, r: "Pria juga butuh kuku bersih! Kami melayani <b>Basic Manicure/Pedicure</b> biar kuku terlihat rapi, sehat, dan maskulin tanpa warna kutek. 🤵" },
    { k: /(wedding|nikah|kawin|pengantin|manten|akad|lamaran|engagement|tunangan)/, r: "Selamat untuk hari bahagianya! 💍 Paket <b>Wedding Nail Art</b> kami lebih glamor dan sudah include hiasan kristal mewah. Saranku booking 1 minggu sebelumnya ya! ❤️" },
    { k: /(hapus|bersihin|removal|lepas kutek|copot gel|bongkar|peel off)/, r: "Biaya removal gel cuma <b>25rb</b> saja Kak. Hemat kuku asli Kakak dari kerusakan kalau dikopek paksa! Kami pake teknik soak-off yang lembut. 💅" },
    { k: /(cat eye|mata kucing|magnet|kilau)/, r: "Tersedia layanan <b>Cat Eye</b> mewah yang menggunakan magnet untuk memberikan efek kilau cahaya yang cantik dan bergerak! ✨👁️" },
    { k: /(marmer|marble|batu|motif unik)/, r: "Desain Marble dibuat manual oleh terapis kami, jadi setiap kuku akan punya motif yang unik dan estetik layaknya batu marmer asli. 🎨" },
    { k: /(french|v shape|minimalis|rapi|bersih)/, r: "Bosan French biasa? Coba <b>V-Shape French</b> atau <b>Double French</b> yang lebih modern. Tangan auto terlihat 'clean' dan mewah! 💅" },
    { k: /(pendek|bantet|kecil|pendek banget)/, r: "Kuku pendek tetap bisa tampil cantik dengan desain minimalis atau bisa kami bantu pasang <b>Acrylic Extension</b> agar kuku panjang instan dan natural! ✨" },
    { k: /(jamur|hijau|penyakit|infeksi|luka|cantengan|bengkak)/, r: "Maaf Kak, jika kuku sedang berjamur (kehijauan) atau luka infeksi parah, kami sarankan sembuhin dulu demi keamanan medis Kakak & kesterilan alat kami. 🙏" },
    { k: /(steril|bersih|uv|higienis|kesehatan|masker)/, r: "Keamanan nomor 1! Semua alat kami disterilisasi menggunakan <b>UV Sterilizer</b> suhu tinggi. Terapis kami juga selalu menjaga kebersihan tangan & pakai masker. 🛡️" },
    { k: /(warna|stok|pilihan|katalog|banyak warna|merah|pink|nude|gel)/, r: "Koleksi warna kami sangat lengkap! Ada lebih dari <b>200 pilihan warna</b> gel premium (Korea/Jepang) dan berbagai jenis hiasan/glitter yang lucu. ✨🌈" },

    // --- RECOMMENDATIONS (THE SMART SEARCH) ---
    { k: /(wisuda|kelulusan|ijazah|toga)/, r: "Untuk wisuda, kami sarankan warna <b>Soft Nude</b> atau <b>Soft Pink</b> dengan sedikit aksen glitter agar elegan saat sesi foto pegang ijazah! 🎓" },
    { k: /(lebaran|idul fitri|fitri|fitrah)/, r: "Rekomendasi Lebaran: Warna <b>Off-White</b> atau earth tone dengan hiasan emas simpel agar terlihat bersih, rapi, dan fitri. 🕋✨" },
    { k: /(natal|christmas|salju|snowflake)/, r: "Spesial Natal: Perpaduan warna <b>Merah Deep</b> dan <b>Hijau Emerald</b> dengan hiasan kepingan salju sangat favorit di sini! ❄️🎅" },
    { k: /(imlek|chinese new year|naga|merah cabe)/, r: "Untuk Imlek, warna <b>Merah Terang</b> dengan aksen Gold Foil atau hiasan Bunga Mei Hwa sangat cocok buat nyambut keberuntungan! 🧧" },
    { k: /(pantai|liburan|holiday|vacation|renang)/, r: "Mau ke pantai? Pilih warna-warna cerah seperti <b>Biru Turquoise</b> atau <b>Kuning Lemon</b> agar kuku Kakak menonjol di pasir putih! 🏖️" },
    { k: /(kantor|kerja|professional|formal|quiet luxury)/, r: "Gaya <b>'Quiet Luxury'</b> dengan warna kuku transparan (nude beige) sangat aman dan profesional untuk lingkungan kantor. Tetap cantik tapi kalem! 💼" },
    { k: /(kencan|date|date night|romantis|pacar)/, r: "Mau nge-date? Warna <b>Rosewood</b> atau <b>Mauve</b> memberikan kesan feminin, romantis, dan sangat manis di mata si doi. ❤️" },

    // --- CONVERSATIONAL & HUMOR (THE SOUL) ---
    { k: /(halo|hi|hey|p|hallo|permisi|assalamualaikum|hai)/, r: "Halo Kakak cantik! ✨ Selamat datang di Neydream Studio. Ada yang bisa Asisten AI bantu seputar kuku impian hari ini? 💅🤖", b: ["Tanya Harga", "Cara Booking", "WhatsApp Admin"] },
    { k: /(apa kabar|gimana kabar|how are you|sehat min)/, r: "Kabar aku sebagai AI selalu semangat melayani Kakak! 🤖 Semoga Kakak juga sehat, bahagia, dan makin cetar membahana hari ini! ✨" },
    { k: /(siapa kamu|namamu|chatbot|robot|ai apa)/, r: "Aku adalah <b>Asisten AI Neydream Studio</b>! 🤖 Tugasku melayani tanya-jawab 24 jam supaya Kakak nggak perlu nunggu admin manual buat info darurat!" },
    { k: /(makasih|terima kasih|thanks|tq|thank you|nuhun|suwun|oke|siapp|baik|ok)/, r: "Sama-sama Kak! 🥰 Senang bisa membantu. AI tunggu kedatangannya di studio ya! Ada lagi yang mau ditanyakan? ✨" },
    { k: /(bye|dadah|sampai jumpa|pamit|keluar|udahan|sudah)/, r: "Sampai jumpa lagi Kak! Have a beautiful day! 💖 Jangan lupa dandanin kuku ya supaya tetep on point! ✨" },
    { k: /(bodoh|bego|tolol|goblok|stupid|jelek|marah|nyesel|payah|apaan sih|ngaco)/, r: "Aduh, mohon maaf lahir batin Kak kalau jawaban AI masih sering kurang pas. 🙏 Saya sedang renovasi otak biar makin pinter. Boleh coba ketik kata kunci singkat saja? 😊" },
    { k: /(lucu|kocak|ngakak| joke)/, r: "Terima kasih Kak! Kata orang-orang aku memang menghibur, tapi tetep kalah lucu dibanding harga promo kami di hari Selasa. Hehe. 😂" },
    { k: /(bantuan|help|caranya|manual|bingung)/, r: "AI Neydream mengerti banyak hal! Kakak bisa tanya tentang: 💰 <b>Harga</b>, 💅 <b>Layanan</b>, 📍 <b>Lokasi</b>, atau 📝 <b>Cara Booking</b>. Ketik saja salah satunya! ✨" }
];

// ================================================
// 2. ENGINE: ATOMIC SCORER (v20.0)
// ================================================

function getAIResponse(userInput) {
    const raw = userInput.toLowerCase().trim();
    if (!raw) return { r: "Halo Kak! Tulis pesan yuk, AI siap bantu jawab soal kuku. 😊", b: ["Harga", "Layanan"] };

    let topMatch = null;
    let maxWeight = 0;

    // Linear Scanning (No data lost)
    MASTER_BRAIN.forEach(item => {
        const match = raw.match(item.k);
        if (match) {
            // Priority Rule:
            // 1. Literal Equality (Perfect match)
            let weight = 0;
            if (raw === match[0]) weight += 100;

            // 2. Inclusion weight based on pattern length
            weight += (item.k.source.length * 2);

            if (weight > maxWeight) {
                maxWeight = weight;
                topMatch = item;
            }
        }
    });

    if (topMatch && maxWeight >= 2) {
        return { r: topMatch.r, b: topMatch.b || [] };
    }

    // FALLBACK: INTELLIGENT NAVIGATION MENU
    if (raw.includes('?')) return { r: "Wah, pertanyaannya detail banget! Tapi AI belum tau jawabannya. 😅 Boleh tanya Admin WA atau pilih menu di bawah?", b: ["Layanan Utama", "Pricelist", "WhatsApp Admin"] };

    return {
        r: "Hmm, AI belum paham maksud Kakak... 🤔\nBoleh pilih salah satu menu di bawah agar lebih gampang?",
        b: ["Menu Layanan", "Harga Update", "Cara Booking", "Lokasi Studio", "Bantuan Admin"]
    };
}

// ================================================
// 3. UI HANDLERS: MODERN INTERACTION
// ================================================

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
        // Clear old typing indicators on open
        document.querySelectorAll('.message.admin.typing').forEach(el => el.remove());
    }
}

function sendMessage(override = null) {
    const input = document.getElementById('userInput');
    const chatBox = document.getElementById('chatBox');
    const text = override || input.value.trim();

    if (!text || !chatBox) return;

    // 1. Show User Text
    const userDiv = document.createElement('div');
    userDiv.className = 'message user';
    userDiv.textContent = text;
    chatBox.appendChild(userDiv);

    if (!override) input.value = "";
    chatBox.scrollTop = chatBox.scrollHeight;

    // 2. Show AI Typing
    const typingId = "ai-typing-" + Date.now();
    const typeDiv = document.createElement('div');
    typeDiv.className = 'message admin typing';
    typeDiv.id = typingId;
    typeDiv.innerHTML = '<span></span><span></span><span></span>';
    chatBox.appendChild(typeDiv);
    chatBox.scrollTop = chatBox.scrollHeight;

    // 3. Process Logic
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

    // Text part
    const content = document.createElement('div');
    content.innerHTML = html.replace(/\n/g, '<br>');
    botDiv.appendChild(content);

    // Buttons part
    if (buttons && buttons.length > 0) {
        const row = document.createElement('div');
        row.style.cssText = "display:flex; flex-wrap:wrap; gap:5px; margin-top:10px;";

        buttons.forEach(label => {
            const b = document.createElement('button');
            b.innerText = label;
            b.style.cssText = "padding:6px 14px; border:1px solid #ea3671; border-radius:18px; background:#fff; color:#ea3671; font-size:0.8rem; cursor:pointer; font-weight:bold; transition:all 0.2s;";

            b.onclick = () => {
                if (label.includes('Admin')) window.open('https://wa.me/628123456789', '_blank');
                else if (label.toLowerCase().includes('maps')) window.open('https://maps.google.com', '_blank');
                else sendMessage(label);
            };

            b.onmouseover = () => { b.style.background = "#ea3671"; b.style.color = "#fff"; };
            b.onmouseout = () => { b.style.background = "#fff"; b.style.color = "#ea3671"; };
            row.appendChild(b);
        });
        botDiv.appendChild(row);
    }

    chatBox.appendChild(botDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function handleKeyPress(e) { if (e.key === 'Enter') sendMessage(); }

// ================================================
// 4. SHARED FORMS LOGIC (PORTED FROM RESERVASI.JS)
// ================================================

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

// Initial Log
console.log('� ATOMIC AI v20.0 - THE ULTIMATE BRAIN DEPLOYED!');
