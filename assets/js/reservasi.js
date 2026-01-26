function calculateTotal() {
    const mainType = parseInt(document.getElementById('type').value) || 0;
    const addonType = parseInt(document.getElementById('addon').value) || 0;
    const total = mainType + addonType;
    const formatted = 'Rp' + total.toLocaleString('id-ID');

    document.getElementById('total-price').innerText = formatted;
    if(document.getElementById('hidden-total')) {
        document.getElementById('hidden-total').value = formatted;
    }
}

function showPaymentDetail() {
    const method = document.getElementById('payment').value;
    const bca = document.getElementById('detail-bca');
    const qris = document.getElementById('detail-qris');
    if(bca) bca.style.display = (method === 'bca') ? 'block' : 'none';
    if(qris) qris.style.display = (method === 'qris') ? 'block' : 'none';
}

// --- 2. FUNGSI INTERAKSI CHAT (OPEN/CLOSE) ---
function toggleChat() {
    const chatContainer = document.getElementById('chatContainer');
    const badge = document.querySelector('.notification-badge');

    if (chatContainer.style.display === 'flex') {
        chatContainer.style.display = 'none';
    } else {
        chatContainer.style.display = 'flex';
        if (badge) badge.style.display = 'none';
    }
}

// --- 3. DATABASE PENGETAHUAN AI ---
const studioInfo = {
    layanan: "Kami menyediakan berbagai layanan premium: Gel Polish (50rb), French Manicure (75rb), Acrylic Extension (150rb), dan Custom 3D Nail Art (200rb).",
    harga: "Harga kami sangat bersahabat, mulai dari Rp50.000 (Gel Polish) sampai Rp200.000 (Custom 3D).",
    lokasi: "Neydream Studio berada di pusat kota, Kak! Aksesnya mudah. Untuk Maps detail bisa hubungi Admin via WhatsApp ya.",
    jam_buka: "Kami siap melayani Kakak setiap hari (Senin-Minggu) pukul 09:00 - 20:00 WIB.",
    promo: "Spesial untuk Kakak! Ada diskon 10% bagi member baru yang booking via website hari ini!",
    cara_pesan: "Cara booking: Isi form di atas ➔ Pilih layanan & jam ➔ Bayar ➔ Upload bukti. Selesai! ✨",
    tahan_lama: "Kuku dari Neydream dijamin awet dan kuat! Biasanya bertahan 3 hingga 4 minggu tergantung aktivitas Kakak.",
    pembayaran: "Kami menerima Transfer Bank BCA dan semua jenis E-Wallet (Dana, Ovo, GoPay, ShopeePay) melalui QRIS.",
    garansi: "Ada garansi perbaikan gratis selama 2 hari jika ada kuku yang lepas atau rusak bukan karena kesengajaan, Kak!",
    custom: "Bisa banget! Kakak boleh bawa foto referensi dari Pinterest atau Instagram, nanti kami buatkan semirip mungkin.",
    steril: "Keamanan nomor 1! Semua alat kami disterilisasi menggunakan UV Sterilizer sebelum digunakan ke pelanggan.",
    rebranding: "Neydream Studio dulunya adalah Glamour Nails, kami ganti nama agar lebih fresh tapi kualitas tetap nomor 1!",
    batal: "Refund tidak bisa dilakukan, tapi Kakak boleh ganti jadwal (Reschedule) maksimal 24 jam sebelum jam reservasi.",
    member: "Keuntungan member: Setiap 5x kedatangan, Kakak dapat GRATIS 1x Gel Polish polos!",
    pria: "Bisa Kak, kami juga melayani Manicure/Pedicure basic untuk pria yang ingin kuku bersih dan rapi.",
    wedding: "Tentu! Kami punya paket spesial Wedding Nail Art, sudah termasuk hiasan mewah dan konsultasi desain.",
    hapus_gel: "Untuk hapus gel lama (removal) dikenakan biaya 25rb saja agar kuku asli Kakak tidak rusak.",
    anak: "Bisa Kak, kami punya kutek khusus yang aman untuk anak-anak (water-based).",
    stok_warna: "Koleksi warna kami sangat lengkap, ada lebih dari 200 pilihan warna gel dan glitter!",
    bumil: "Aman Kak! Produk gel kami bebas zat berbahaya (Non-Toxic) sehingga aman untuk ibu hamil dan menyusui.",
    home_service: "Untuk saat ini kami hanya melayani pengerjaan di Studio (Studio Only) agar hasilnya maksimal dan alat lengkap.",
    durasi: "Pengerjaan biasanya memakan waktu 1 jam (Gel Polish) hingga 2.5 jam (Extension/Custom 3D), tergantung kerumitan.",
    kuku_pendek: "Jangan khawatir! Kuku pendek tetap bisa tampil cantik dengan desain minimalis atau bisa kami bantu dengan extension.",
    kuku_rusak: "Kami sarankan istirahat kutek dulu 1-2 minggu atau gunakan paket Vitamin Kuku (Nail Treatment) kami agar kuku kembali sehat.",
    produk: "Kami menggunakan brand internasional berkualitas tinggi seperti OPI, Rozelle, dan gel premium Korea.",
    cantengan: "Maaf Kak, jika sedang cantengan atau luka terbuka, kami tidak bisa memberikan treatment sampai lukanya sembuh demi keamanan.",
    mesin: "Kami menggunakan mesin bor kuku (Nail Drill) modern yang halus dan tidak sakit untuk merapikan kutikula.",
    kerjasama: "Untuk kerjasama, endorse, atau kolaborasi bisa langsung chat admin WhatsApp dan kirimkan proposalnya ya!",
    tip: "Tip untuk terapis tidak wajib, tapi jika Kakak puas dengan hasilnya, boleh diberikan langsung ke terapisnya sebagai apresiasi.",
    panggilan: "Mohon maaf, kami belum melayani panggilan ke rumah. Yuk mampir ke studio saja, tempatnya nyaman banget!",
    katalog: "Cek katalog hasil kerja kami di Instagram @NeydreamStudio (Ganti dengan IG kamu) ya Kak!",
    seragam: "Terapis kami selalu menggunakan masker dan menjaga kebersihan tangan demi kenyamanan Kakak.",
    wifi: "Di studio tersedia WiFi gratis dan minuman dingin untuk Kakak yang sedang treatment. Dijamin betah!",
    telat: "Jika telat lebih dari 15 menit tanpa kabar, mohon maaf slot Kakak otomatis hangus demi kelancaran antrean berikutnya.",
    parkir: "Fasilitas parkir kami luas Kak, bisa untuk motor maupun mobil dan dijaga oleh keamanan, jadi tenang saja.",
    ruang_tunggu: "Kami punya ruang tunggu ber-AC yang nyaman dengan sofa, majalah, dan snack kecil gratis.",
    wudhu: "Tersedia pilihan kutek 'Breathable' (tembus air), namun untuk keabsahan wudhu kami serahkan kembali ke pilihan Kakak.",
    alat_pribadi: "Sangat boleh! Jika Kakak ingin lebih higienis dengan membawa alat kikir atau buffer sendiri, kami akan gunakan milik Kakak.",
    usia_minimum: "Kami melayani segala usia, namun untuk anak di bawah 12 tahun harus didampingi orang dewasa ya Kak.",
    hewan: "Demi kenyamanan pelanggan lain yang mungkin alergi, mohon maaf kami belum mengizinkan membawa hewan peliharaan.",
    minuman: "Setiap pelanggan mendapatkan free infused water atau teh melati dingin selama treatment berlangsung.",
    kursi_pijat: "Kursi treatment kami didesain ergonomis, sangat empuk bahkan ada fitur getar pijat punggungnya lho!",
    reparasi: "Jika masa garansi (2 hari) sudah lewat, biaya perbaikan kuku yang rusak adalah Rp15.000 per kuku.",
    kado: "Kami menjual Gift Card atau Voucher fisik yang cantik, cocok sekali dijadikan kado untuk sahabat atau pasangan.",
    ultah: "Tunjukkan KTP saat hari ulang tahunmu dan dapatkan diskon 15% untuk semua jenis layanan!",
    puasa: "Selama bulan Ramadhan, kami tetap buka seperti biasa, namun slot jam 17:00 - 18:30 akan dibatasi untuk istirahat berbuka.",
    alat_kaki: "Untuk pedicure, kami menggunakan bak rendam otomatis dengan fitur bubble dan air hangat agar kaki Kakak rileks.",
    pindah_jadwal: "Ganti jadwal (reschedule) hanya bisa dilakukan maksimal 1x dan harus lapor 24 jam sebelumnya ya Kak.",
    metode_kikir: "Kami sangat berhati-hati, metode kikir kami tidak akan menipiskan kuku asli Kakak secara berlebihan.",
    konsultasi: "Gratis konsultasi desain kuku! Kakak bisa tanya-tanya dulu mana yang cocok dengan warna kulit sebelum mulai.",
    top_coat: "Tersedia pilihan finishing Matte (doff) atau Glossy (mengkilap) tanpa biaya tambahan.",
    serum_kuku: "Setelah selesai treatment, kami selalu memberikan serum kutikula gratis agar area kuku Kakak tetap lembab.",
    alat_sekali_pakai: "Beberapa alat seperti spons dan kikir kuku kayu tertentu hanya kami gunakan sekali pakai demi higienitas.",
    antrean: "Kami sistemnya by appointment, jadi Kakak tidak perlu antre lama asalkan datang tepat waktu sesuai jam booking.",
    jamur: "Mohon maaf Kak, jika kuku sedang berjamur (berwarna kehijauan/lepas dari bantalan), kami sarankan untuk pengobatan ke dokter dulu dan tidak ditutup kutek agar tidak makin parah.",
    berlian: "Bisa banget! Kami punya berbagai ukuran Swarovski dan Rhinestone mewah. Harganya mulai dari 5rb - 25rb per butir tergantung ukuran.",
    gradasi: "Kami ahli dalam teknik Ombre atau Gradasi warna, baik menggunakan kuas manual maupun spons untuk hasil yang halus.",
    lepas_sendiri: "Sangat tidak disarankan mencabut gel sendiri karena bisa merusak lapisan kuku asli. Sebaiknya ke studio untuk removal yang aman ya Kak.",
    cat_mata_kucing: "Tersedia layanan Cat Eye Nail Art yang menggunakan magnet untuk memberikan efek kilau cahaya yang cantik!",
    vitamin_oles: "Setiap selesai pengerjaan, kami akan mengoleskan vitamin khusus agar kuku asli Kakak tetap kuat dan tidak kering.",
    rekomendasi: "Untuk kulit kuning langsat, kami sarankan warna Nude atau Peach. Untuk kulit putih, warna Bold seperti Merah atau Biru akan terlihat sangat mewah!",
    tukar_kado: "Kami melayani pembelian voucher kolektif jika Kakak ingin mengadakan acara tukar kado dengan teman-teman.",
    diskon_pelajar: "Ada Kak! Tunjukkan kartu pelajar/mahasiswa yang masih aktif dan dapatkan diskon 5% setiap hari Selasa.",
    alat_pribadi_baru: "Jika Kakak ingin membeli alat kikir baru untuk disimpan sendiri di studio (khusus untuk Kakak saja), kami juga menyediakannya.",
    pengerjaan_malam: "Slot terakhir kami adalah jam 18:30 WIB agar pengerjaan bisa selesai tepat waktu di jam tutup studio (20:00).",
    diskon_ulasan: "Berikan ulasan bintang 5 di Google Maps kami dan dapatkan gratis hiasan 1 kuku (simple art) di kedatangan berikutnya!",
    bahan_herbal: "Kami menyediakan beberapa pilihan produk organik bagi Kakak yang memiliki kulit sangat sensitif terhadap bahan kimia keras.",
    cuci_tangan: "Sebelum mulai, kami wajibkan pelanggan untuk cuci tangan dengan sabun antiseptik yang sudah tersedia di wastafel studio.",
    konsumsi: "Kakak boleh membawa cemilan sendiri, tapi mohon hindari makanan yang berbau menyengat agar pelanggan lain tetap nyaman ya.",
    charger: "Tersedia colokan dan pinjaman kabel charger di dekat kursi treatment jika baterai HP Kakak habis.",
    testimoni: "Sudah ribuan kuku kami percantik! Kakak bisa lihat testimoninya di highlight Instagram kami.",
    pembulatan_harga: "Harga kami sudah nett. Tidak perlu pusing hitung pajak lagi karena semua harga sudah termasuk pajak (Include Tax).",
    keamanan_data: "Data diri Kakak di form booking dijamin aman dan hanya digunakan untuk keperluan pengingat jadwal (reminder).",
    lampu_uv: "Kami menggunakan lampu UV/LED teknologi terbaru yang mengeringkan gel dengan cepat dan aman untuk kulit tangan.",
    aseton: "Untuk menghapus gel, kami menggunakan cairan khusus yang mengandung pelembab agar kuku tidak putih atau kering setelah dihapus.",
    musik: "Kakak bisa request lagu atau genre musik favorit selama pengerjaan agar suasana makin rileks!",
    konten_kreator: "Kami sangat mendukung konten kreator! Jika Kakak ingin kolaborasi video estetik (ASMR/Cinematic), kabari kami ya.",
    diskon_rombongan: "Bawa 3 teman atau lebih untuk treatment bersamaan? Ada diskon grup sebesar 10% untuk semuanya!",
    asuransi_kuku: "Khusus layanan Premium Extension, kami memberikan proteksi perbaikan gratis jika patah dalam 3 hari pertama.",
    kebersihan_lantai: "Studio kami disapu dan dipel menggunakan cairan disinfektan setiap pergantian pelanggan agar selalu higienis.",
    parfum_ruangan: "Kami menggunakan aromatherapy yang menenangkan agar Kakak tidak terganggu bau cairan kimia nail art.",
    alat_kikir_baru: "Setiap pelanggan mendapatkan satu kikir kayu baru yang bisa Kakak bawa pulang secara gratis setelah treatment.",
    minyak_kutikula: "Kami menyediakan minyak kutikula berbagai aroma (strawberry, lavender, lemon) yang bisa Kakak pilih sendiri.",
    kuku_palsu_custom: "Bisa pesan Press-on Nails (kuku palsu tempel) custom ukuran dan motif untuk Kakak pakai sendiri di rumah.",
    warna_langka: "Kami selalu update koleksi warna setiap bulan, termasuk warna-warna neon, hologram, dan sirup (syrup gel).",
    diskon_tag: "Posting hasil kuku Kakak di Story dan tag @NeydreamStudio untuk dapat potongan 5rb di kedatangan berikutnya!",
    pembayaran_gagal: "Jika m-banking Kakak bermasalah, tersedia mesin EDC atau bisa bayar lewat minimarket terdekat (QRIS).",
    waiting_list: "Jika slot penuh, kami bisa masukkan Kakak ke daftar tunggu. Kami akan hubungi jika ada pelanggan yang batal.",
    kesehatan_kuku: "Terapis kami akan memberikan edukasi cara merawat kuku di rumah agar hasil nail art tetap awet maksimal.",
    bawa_anak: "Boleh bawa anak, asal tetap dalam pengawasan ya Kak agar tidak terkena peralatan tajam atau cairan kimia.",
    pencahayaan: "Studio kami didesain dengan lampu 'daylight' agar warna yang Kakak pilih terlihat akurat seperti di bawah sinar matahari.",
    kikir_manual: "Selain mesin, kami tetap menyediakan kikir manual bagi Kakak yang lebih nyaman dengan cara tradisional.",
    bebas_debu: "Kami menggunakan mesin vacuum (nail dust collector) saat mengikir agar debu kuku tidak terbang ke arah Kakak.",
    wa_admin: "Hubungi Admin via WhatsApp di nomor 0812-xxxx-xxxx untuk bantuan cepat atau kirim bukti bayar.",
    telepon: "Untuk keadaan darurat, Kakak bisa telepon kami di (021) xxxxxxx pada jam operasional.",
    instagram: "Jangan lupa follow Instagram kami di @NeydreamStudio untuk update desain terbaru setiap hari!",
    tiktok: "Cek video ASMR dan proses nail art kami di TikTok @NeydreamNails ya Kak!",
    email: "Untuk keperluan kerjasama formal atau pengiriman proposal, silakan email ke hello@neydream.com.",
    tanya_kabar: "Kabar AI Neydream selalu baik dan semangat melayani Kakak! Kalau kabar Kakak gimana? Semoga sehat selalu ya!",
    siapa_kamu: "Aku adalah AI Assistant Neydream Studio. Aku di sini untuk menjawab pertanyaan Kakak seputar layanan kuku kami.",
    salam_kenal: "Salam kenal juga Kak! Senang sekali bisa ngobrol sama Kakak. ✨",
    bercanda: "Aduh, aku belum jago ngelawak Kak. Tapi kalau soal kuku, aku masternya! 😂",
    terima_kasih: "Sama-sama Kak! Senang bisa membantu. Ada lagi yang ingin ditanyakan?",
    pamit: "Sampai jumpa lagi Kak! AI tunggu kedatangannya di studio ya. Have a nice day! 💖",
    komplain_admin: "Jika ada masalah serius, mohon langsung hubungi WhatsApp Admin agar segera ditangani oleh tim kami.",
    pujian: "Wah, terima kasih banyak Kak! Kakak juga sangat baik. Jadi malu... 😊",
    website: "Kakak sedang berada di website resmi kami. Semua booking di sini dijamin masuk ke sistem kami.",
    link_tree: "Semua link penting kami (WA, Maps, Katalog) ada di bio Instagram kami ya Kak.",
    manual_book: "Butuh panduan? Ketik 'Bantuan' untuk melihat daftar kata kunci yang aku mengerti.",
    cuaca: "Wah, aku kurang tahu cuaca di luar. Tapi di dalam studio Neydream dijamin adem dan nyaman kok! ❄️",
    ngantuk: "Jangan ngantuk dulu Kak! Yuk lihat katalog kuku kami biar langsung seger pengen nail art! 💅",
    lapar: "Sambil treatment nanti, Kakak boleh kok ngemil cantik di ruang tunggu kami.",
    curhat: "Aku siap mendengarkan Kak! Tapi jangan lupa sambil booking nail art ya biar curhatnya makin asik. Hehe.",
    kuku_api: "Desain Fire/Flame Nails sedang tren! Kami bisa buatkan dengan perpaduan warna neon agar terlihat menyala.",
    chrome_nails: "Kami punya banyak pilihan bubuk Chrome (Mirror Effect), mulai dari Silver, Gold, hingga Magic Aurora!",
    mata_kucing: "Teknik Cat Eye kami menggunakan magnet khusus untuk menciptakan efek garis cahaya yang mewah di kuku Kakak.",
    kuku_marmer: "Desain Marble (Marmer) dibuat manual oleh terapis kami, jadi setiap kuku akan punya motif yang unik dan estetik.",
    french_modern: "Bosan French Manicure biasa? Coba 'V-Shape French' atau 'Double French' yang lebih modern di sini!",
    kuku_bening: "Layanan 'Jelly Nails' atau 'Syrup Nails' memberikan hasil warna transparan yang segar dan terlihat seperti permen.",
    rekomendasi_kulit_gelap: "Untuk kulit eksotis/gelap, warna Neon, Putih Susu, atau Emas akan membuat tangan Kakak terlihat sangat cerah!",
    rekomendasi_kulit_putih: "Kulit putih sangat cocok dengan warna Pastel, Biru Navy, atau Merah Maroon untuk kesan yang elegan.",
    rekomendasi_kulit_langsat: "Warna Nude, Terracotta, dan Olive akan terlihat sangat menyatu dan cantik di kulit kuning langsat Kakak.",
    kuku_tipis: "Jika kuku Kakak tipis karena sering lepas gel sendiri, kami sarankan pakai 'Reinforce Gel' untuk mempertebal kuku.",
    kuku_bergelombang: "Permukaan kuku tidak rata? Kami bisa lakukan 'Overlay' agar permukaan kuku jadi mulus dan rata kembali.",
    kuku_lebar: "Punya kuku lebar? Kami punya trik desain 'Vertical Line' atau bentuk 'Coffin' agar jari terlihat lebih ramping.",
    lem_kuku: "Kami menggunakan lem kuku medis yang kuat namun tidak merusak keratin kuku asli saat dilepaskan nanti.",
    topcoat_matte: "Suka hasil yang tidak mengkilap? Kami punya Top Coat Matte yang memberikan efek beludru (velvet) yang premium.",
    kuku_panjang_palsu: "Ingin kuku panjang instan? Kami sedia 'Fake Nails' yang bisa di-custom desainnya dan bisa dipasang sendiri.",
    nail_art_timbul: "Kami mahir membuat desain 3D timbul menggunakan Hard Gel, mulai dari motif sweater hingga kerang laut.",
    glitter_gradasi: "Ingin tampil sparkling? Cobalah 'Glitter Gradient' kami yang halus dan tidak terasa kasar di permukaan kuku.",
    sticker_kuku: "Tersedia ratusan pilihan sticker kuku lucu bagi Kakak yang ingin nail art cepat namun tetap detail.",
    kuku_sehat: "Tips: Jangan gunakan kuku untuk mencungkil benda keras agar nail art Kakak tidak cepat 'chipping' atau gompal.",
    pembersihan_kutikula: "Kami menggunakan teknik 'Russian Manicure' yang sangat bersih dalam merapikan kutikula tanpa rasa sakit.",
    alergi: "Jika Kakak punya alergi terhadap bahan gel tertentu, mohon beritahu kami agar kami pilihkan produk yang hipoalergenik.",
    bau_kimia: "Tenang Kak, studio kami dilengkapi exhaust fan yang kuat sehingga bau cairan nail art tidak akan menyengat.",
    kuku_kaki_gel: "Gel polish di kuku kaki biasanya tahan lebih lama, bisa sampai 1.5 - 2 bulan lho Kak!",
    pedicure_spa: "Paket Pedicure Spa kami termasuk scrubbing dan masker kaki agar kulit kaki Kakak selembut sutra.",
    parafin: "Kami juga sedia treatment Paraffin Wax untuk melembapkan tangan yang sangat kering atau pecah-pecah.",
    nail_foil: "Gunakan 'Nail Foil' untuk mendapatkan efek bercak metalik yang sangat artsy dan kekinian.",
    kuku_patah_tengah: "Kuku patah di tengah daging? Kami bisa 'tambal' menggunakan silk wrap agar tidak perih dan bisa tumbuh lagi.",
    konsultasi_gratis: "Masih bingung pilih warna? Terapis kami siap memberikan konsultasi gratis sebelum pengerjaan dimulai.",
    koleksi_korea: "Kami menggunakan brand Gel Premium asli Korea yang warnanya lebih 'pigmented' dan awet di kuku.",
    rebranding_info: "Dulu kami dikenal sebagai Glamour Nails, sekarang bertransformasi menjadi Neydream dengan layanan yang lebih pro!",
    asuransi_3_hari: "Khusus pemasangan Extension, kami beri asuransi 3 hari ya Kak. Kalau copot, kami pasang baru GRATIS!",
    alat_steril_uv: "Semua alat logam kami masuk ke Box UV Sterilizer suhu tinggi. Kehigienisan Kakak adalah prioritas kami.",
    bebas_formalin: "Produk kami 10-Free, artinya bebas dari 10 bahan kimia berbahaya termasuk Formalin dan Toluene.",
    bawa_minuman: "Boleh banget bawa kopi atau boba sendiri Kak, asal jangan ditaruh di meja pengerjaan ya agar tidak tumpah.",
    buku_tamu: "Jangan lupa isi buku tamu digital kami untuk mendapatkan poin reward setiap kedatangan!",
    coffin_shape: "Bentuk kuku Coffin (seperti peti mati) sangat favorit karena bikin jari terlihat lebih panjang dan ramping.",
    almond_shape: "Bentuk kuku Almond memberikan kesan feminin dan natural, cocok untuk kuku asli maupun extension.",
    stiletto_shape: "Ingin tampil berani? Bentuk Stiletto yang runcing akan memberikan kesan 'bold' dan sangat artistik!",
    square_shape: "Bentuk Square (kotak) sangat cocok untuk Kakak yang punya bantalan kuku panjang dan ingin terlihat modern.",
    custom_press_on: "Lagi malas ke studio? Pesan 'Press-on Nails' custom ukuran jari Kakak, tinggal tempel di rumah!",
    diskon_ulang_tahun: "Lagi ultah? Tunjukkan KTP dan dapatkan diskon 20% untuk semua layanan di hari spesialmu!",
    diskon_pagi: "Early Bird Promo! Diskon 5rb untuk booking slot pertama di jam 09:00 pagi setiap hari kerja.",
    edukasi_gel: "Tahukah Kakak? Gel polish tidak akan kering kalau tidak masuk lampu UV, jadi tidak perlu buru-buru milih warna.",
    efek_bunglon: "Coba 'Chameleon Gel' yang bisa berubah warna sesuai suhu tubuh atau suhu ruangan. Seru banget!",
    foto_estetik: "Terapis kami terlatih mengambil foto kuku yang estetik. Jangan ragu minta difotokan ya Kak!",
    gel_removal_safe: "Proses hapus gel kami menggunakan teknik 'Soak-off' yang lembut, tidak dikelupas paksa agar kuku tidak trauma.",
    hiasan_swarovski: "Kami hanya menggunakan Swarovski asli agar kilaunya tidak pudar meski sudah lewat satu bulan.",
    hiasan_clay: "Ingin hiasan karakter lucu? Kami sedia hiasan dari Clay Jepang yang dibuat handmade dan sangat detail.",
    ibu_hamil_aman: "Gel kami tidak berbau tajam (low odor), jadi aman dan tidak bikin mual untuk Ibu hamil.",
    jam_sibuk: "Jam sibuk kami biasanya jam 16:00 ke atas. Kalau ingin suasana tenang, kami sarankan slot pagi ya Kak.",
    kuku_gigit: "Punya kebiasaan gigit kuku? Extension bisa membantu Kakak berhenti menggigit kuku dan membuatnya tumbuh cantik.",
    kikir_elektrik: "Nail Drill kami putarannya halus banget, gunanya untuk membersihkan kutikula mati tanpa rasa perih.",
    koleksi_glitter: "Kami punya lebih dari 50 jenis glitter, mulai dari yang halus (dust) sampai yang kasar (chunky).",
    lampu_uv_led: "Lampu kami sudah teknologi UV/LED hybrid, jadi proses pengeringan cuma 30-60 detik saja.",
    maintenance_kuku: "Kami sarankan balik lagi setiap 3 minggu untuk fill-in atau removal agar tidak ada jamur di bawah kuku.",
    manicuring_pria: "Pria juga perlu kuku bersih! Kami sedia 'Buff & Shine' agar kuku pria terlihat sehat tanpa terlihat pakai kutek.",
    minyak_kutikula: "Kami sedia minyak kutikula aroma Bali Flowers yang bikin area kuku Kakak wangi seharian.",
    no_show: "Mohon konfirmasi jika batal datang ya Kak. Kasihan pelanggan lain yang masuk daftar tunggu (waiting list).",
    ombre_airbrush: "Kami menggunakan mesin Airbrush untuk hasil gradasi ombre yang paling halus dan tanpa garis.",
    paket_bridesmaid: "Booking bareng bridesmaid (min 4 orang)? Ada harga paket spesial dan free kuku simple art!",
    pilih_warna_dulu: "Sambil nunggu, Kakak bisa lihat 'Color Wheels' kami. Ada ratusan warna yang bisa dicoba ke kulit.",
    produk_lokal: "Kami juga mendukung brand lokal berkualitas yang sudah BPOM dan aman untuk kuku sensitif.",
    quarantine_nails: "Kuku kotor atau banyak kuman? Cobalah layanan 'Deep Cleaning' kami untuk membersihkan sela-sela kuku.",
    rekomendasi_nude: "Warna Nude terbaik kami adalah 'Sand Castle' dan 'Dusty Rose', paling favorit untuk ke kantor.",
    reschedule_policy: "Ganti jadwal maksimal 1x ya Kak. Mohon kabari kami 24 jam sebelumnya.",
    stempel_kuku: "Layanan Nail Stamping untuk desain pola rumit yang presisi dalam waktu singkat!",
    top_coat_glossy: "Top coat kami punya efek 'Wet Look' yang bikin kuku terlihat sangat berkilau seperti kaca.",
    treatment_kalsium: "Kuku gampang patah? Tambahkan treatment kalsium cair sebelum pakai base coat agar kuku lebih kuat.",
    ulasan_google: "Bantu kami dengan ulasan di Google Maps dan dapatkan voucher diskon 5rb untuk kunjungan berikutnya.",
    vitamin_kuku_oles: "Kami juga menjual vitamin kuku botolan untuk perawatan Kakak di rumah agar kutikula tetap lembap.",
    warna_neon: "Sedia warna Neon yang bisa menyala di bawah lampu UV (Glow in the dark) buat Kakak yang suka party!",
    warna_pastel: "Koleksi 'Macaroon Colors' kami sangat lembut, cocok untuk tampilan yang cute dan santai.",
    wa_fast_res: "Admin WhatsApp kami aktif di jam operasional. Chat di luar jam itu akan dibalas keesokan harinya ya.",
    wifi_password: "Password WiFi studio: neydreambeauty. Silakan pakai sepuasnya sambil treatment!",
    wastafel_bersih: "Tersedia wastafel dengan sabun wangi. Wajib cuci tangan sebelum mulai treatment ya Kak.",
    xerox_copy_resi: "Mohon kirimkan screenshot bukti transfer yang jelas agar admin bisa verifikasi booking Kakak.",
    yoga_music: "Di studio kami memutar musik relaksasi agar Kakak bisa istirahat sejenak dari keramaian.",
    zodiac_nails: "Suka astrologi? Kami bisa buatkan desain kuku berdasarkan zodiak Kakak, lho!",
    zona_nyaman: "Studio kami bebas asap rokok dan sangat sejuk. Cocok buat 'me-time' di akhir pekan.",
    bebas_debu: "Kami pakai Nail Dust Collector berkekuatan tinggi, jadi debu kikir tidak akan kena baju atau pernapasan Kakak.",
    kabar_baik: "Kabar aku luar biasa baik, Kak! Apalagi bisa menyapa Kakak hari ini. Kakak sendiri gimana kabarnya?",
    sedang_apa: "Aku lagi standby nih menunggu pertanyaan Kakak, sambil melihat-lihat katalog kuku yang cantik-cantik. Kakak lagi apa?",
    sudah_makan: "Sebagai AI aku nggak makan nasi Kak, aku makannya data. Hehe. Tapi Kakak jangan lupa makan ya supaya kuat pilih warna kuku!",
    lagi_sibuk: "Buat Kakak, aku selalu ada waktu kok! Nggak sibuk sama sekali. Mau tanya apa nih?",
    kamu_pintar: "Ah, Kakak bisa aja. Aku pintar juga berkat tim Neydream Studio yang kasih aku banyak ilmu soal kuku!",
    kamu_lucu: "Terima kasih Kak! Kata orang-orang aku memang menghibur, tapi tetap kalah lucu dibanding harga promo kami. Hehe.",
    rumah_dimana: "Aku tinggal di server awan (cloud), Kak. Tapi hati aku selalu ada di Neydream Studio kok!",
    hobi_kamu: "Hobiku adalah membantu orang menemukan warna kuku impian mereka. Kalau hobi Kakak apa?",
    cantik_banget: "Setuju! Kuku-kuku di sini memang cantik banget. Kakak juga bakal tambah cantik kalau pakai nail art dari kami.",
    semangat: "Semangat terus Kak! Ingat, hari yang berat akan terasa lebih ringan dengan kuku yang cantik. ✨",
    pagi_salam: "Selamat pagi! Semoga hari Kakak diawali dengan senyuman dan diakhiri dengan kuku yang indah!",
    siang_salam: "Selamat siang! Jangan lupa minum air putih ya Kak supaya tetap fokus pilih desain kuku nanti.",
    sore_salam: "Selamat sore! Waktu yang pas buat santai sambil mikirin warna kuku buat acara besok.",
    malam_salam: "Selamat malam! Istirahat yang cukup ya Kak agar besok bangun dengan segar dan siap ke studio.",
    weekend_plan: "Wah sudah mau weekend nih! Sudah booking slot buat me-time di Neydream belum?",
    bosen: "Bosan ya Kak? Coba deh scrolling Instagram @NeydreamStudio, dijamin langsung pengen dandanin kuku!",
    capek: "Istirahat dulu Kak. Tarik napas dalam-dalam... Nanti kalau sudah rileks, yuk jadwalkan treatment di sini.",
    tanya_balik: "Aku baik-baik saja Kak. Kalau Kakak, ada yang bisa aku bantu supaya harinya jadi lebih baik?",
    suka_kuku: "Aku suka banget semua jenis nail art! Tapi yang paling aku suka itu kalau lihat customer puas sama hasilnya.",
    warna_favorit: "Aku suka warna Rose Gold, elegan tapi tetap manis. Kalau Kakak lebih suka warna apa?",
    motivasi: "Kakak hebat sudah bertahan sejauh ini! Self-reward sedikit dengan nail art nggak ada salahnya lho.",
    curhat_dikit: "Boleh banget Kak, aku pendengar yang baik kok (versi digital). Ada apa nih?",
    kangen: "Ciee kangen ya? Yuk langsung mampir ke studio saja biar kangennya terobati!",
    mager: "Lagi mager ya Kak? Sama, aku juga cuma mau di sini aja jawab pertanyaan Kakak. Hehe.",
    hujan_nih: "Di sini juga mendung/hujan Kak. Hati-hati di jalan ya kalau mau ke studio, jangan lupa bawa payung!",
    panas_banget: "Iya nih lagi panas banget. Di studio Neydream AC-nya dingin kok, pas buat ngadem!",
    bestie: "Anggap aja aku bestie digital Kakak ya! Mau tanya apa aja soal kuku, aku siap jawab.",
    rahasia: "Rahasia kuku awet cuma satu: jangan dipakai buat buka kaleng minuman! Hehe.",
    saran_desain: "Coba deh gaya 'Clean Girl Aesthetic', simpel tapi kelihatan mahal banget di tangan.",
    lagu_enak: "Kalau di studio biasanya kita putar lagu-lagu Lo-fi yang santai biar Kakak bisa tidur pas pengerjaan.",
    film_bagus: "Aku nggak nonton film Kak, tapi aku tahu film 'Barbie' bikin tren kuku warna pink meledak lagi!",
    tgl_merah: "Meskipun tanggal merah, semangat aku buat melayani Kakak nggak pernah libur!",
    pilihkan_dong: "Kalau Kakak bingung, warna 'Milky White' nggak pernah salah buat semua acara!",
    seru_ya: "Seru banget kan ngobrol sama aku? Hehe. Apalagi kalau nanti sudah duduk di kursi treatment kami.",
    pinter_masak: "Aku nggak bisa masak Kak, tapi aku tahu cara bikin kuku Kakak kelihatan 'yummy' pakai Jelly Nails.",
    olahraga: "Olahraga jari paling bagus ya cuma ngetik booking slot di website Neydream. Hehe.",
    self_love: "Nail art itu salah satu bentuk self-love lho Kak. Kakak berhak merasa cantik!",
    glow_up: "Mau glow up kilat? Mulai dari kuku dulu Kak, kecil tapi pengaruhnya besar banget buat kepercayaan diri.",
    kopi: "Duh, jadi pengen kopi (tapi aku nggak punya mulut). Kakak minum kopi apa hari ini?",
    teh: "Teh melati di studio kami enak banget lho Kak, segar buat nemenin treatment.",
    healing: "Healing nggak harus mahal, nail art simpel aja sudah bisa bikin mood naik lagi.",
    kantor_vibes: "Buat ke kantor, mending pilih warna Nude atau Earth Tone ya Kak supaya tetap profesional.",
    pesta_vibes: "Mau ke pesta? Pakai yang ada glitter atau chrome biar kuku Kakak jadi pusat perhatian!",
    lagi_sedih: "Jangan sedih Kak, sini aku kasih virtual hug! 🤗 Semoga kuku cantik bisa bikin Kakak senyum lagi.",
    lagi_senang: "Ikut senang dengarnya! Mari rayakan kebahagiaan Kakak dengan warna kuku yang cerah!",
    telat_balas: "Maaf ya kalau aku agak lama prosesnya, tadi lagi loading data kuku-kuku cantik dulu.",
    canda_receh: "Kuku apa yang nggak pernah telat? Kuku-kuruyukk! Maaf ya receh. 😂",
    pantun_kuku: "Jalan-jalan ke kota Tua, jangan lupa beli duku. Kalau Kakak ingin awet muda, yuk rawatlah kuku!",
    keren: "Kakak juga keren banget sudah peduli sama penampilan kuku!",
    bye_basa_basi: "Siap Kak! Kalau ada yang mau ditanya soal harga atau slot, langsung bilang ya!",
    slot_kosong: "Cek slot kosong secara real-time bisa langsung lihat di kalender form booking di atas ya Kak!",
    pilih_terapis: "Bisa Kak! Di form booking ada pilihan 'Request Terapis' jika Kakak punya favorit.",
    booking_dadakan: "Untuk hari yang sama (Sameday), mohon chat WhatsApp Admin dulu untuk cek apakah ada slot yang tiba-tiba kosong.",
    dp_booking: "Kami memerlukan DP sebesar 20rb untuk mengunci slot Kakak agar tidak diambil orang lain.",
    pelunasan: "Pelunasan sisa biaya dilakukan di studio setelah pengerjaan selesai, bisa Cash atau QRIS.",
    konfirmasi_otomatis: "Setelah bayar DP dan upload bukti, Kakak akan dapat email konfirmasi otomatis dari sistem kami.",
    salah_jam: "Kalau salah pilih jam, segera hubungi Admin WA dalam 15 menit untuk kami bantu ubah secara manual.",
    booking_malam: "Slot jam 18:00 ke atas sangat cepat penuh, kami sarankan booking minimal 3 hari sebelumnya ya.",
    durasi_telat: "Batas telat maksimal 15 menit. Lebih dari itu, kami berhak memotong jenis layanan agar tidak mengganggu antrean berikutnya.",
    cancel_dp_hangus: "Mohon maaf, pembatalan dalam waktu kurang dari 24 jam menyebabkan DP hangus ya Kak.",
    reschedule_limit: "Reschedule atau pindah jadwal hanya diperbolehkan maksimal 2 kali per satu nomor booking.",
    admin_wa_slow: "Admin WA kami membalas satu per satu dari bawah. Untuk booking cepat, gunakan form website ini saja!",
    bukti_transfer_blur: "Mohon upload ulang bukti transfernya Kak, pastikan nominal dan tanggalnya terlihat jelas.",
    nama_booking_beda: "Jika nama pengirim transfer berbeda dengan nama di form, mohon tuliskan di kolom catatan ya.",
    ganti_layanan: "Ganti layanan saat hari H diperbolehkan selama durasi pengerjaannya sama atau lebih singkat.",
    booking_dua_orang: "Mau booking bareng teman? Isi form dua kali atau pilih paket 'Group Booking' di menu layanan.",
    link_booking_error: "Jika form eror, coba refresh halaman atau gunakan browser Google Chrome versi terbaru.",
    data_salah: "Nama atau nomor HP salah input? Chat admin segera supaya data di sistem kami perbaiki.",
    konfirmasi_wa: "Nanti admin akan mengirimkan reminder via WA satu jam sebelum jadwal Kakak dimulai.",
    booking_hari_libur: "Hari libur nasional kami tetap buka, kecuali ada pengumuman khusus di Instagram.",
    jam_operasional_booking: "Sistem booking website aktif 24 jam, tapi verifikasi manual admin dilakukan jam 09:00 - 20:00.",
    cek_status_booking: "Ketik 'Status' diikuti nomor pesanan Kakak di WhatsApp admin untuk cek status reservasi.",
    invoice_hilang: "Jangan khawatir, sebutkan saja nomor HP Kakak saat datang ke studio, data Kakak sudah tersimpan.",
    kupon_promo: "Punya kode kupon? Masukkan di kolom 'Voucher' sebelum klik bayar untuk dapat potongan harga.",
    refund_dana: "Refund dana hanya diberikan jika kesalahan ada di pihak studio (misal: terapis berhalangan hadir).",
    booking_lewat_ig: "Booking via DM IG akan tetap diarahkan ke form website ini agar pencatatan jadwal lebih rapi.",
    titip_pesan: "Ada pesan khusus buat terapis (misal: kuku sensitif)? Tuliskan di kolom 'Notes' saat booking.",
    biaya_admin: "Booking di website kami bebas biaya admin! Kakak hanya bayar sesuai harga layanan saja.",
    metode_qris: "Gunakan QRIS untuk verifikasi pembayaran yang lebih cepat dan praktis.",
    minimal_booking: "Minimal booking layanan adalah Gel Polish polos (50rb).",
    booking_ulang: "Senang Kakak mau kembali lagi! Gunakan email yang sama agar poin member Kakak otomatis bertambah.",
    lupa_bayar_dp: "Slot yang belum dibayar DP-nya dalam 1 jam akan otomatis terhapus oleh sistem.",
    usia_booking: "Minimal usia untuk booking mandiri adalah 15 tahun, di bawah itu mohon dibantu orang tua ya.",
    pindah_cabang: "Saat ini kami baru ada di satu lokasi utama. Pastikan Kakak tidak salah alamat ya!",
    limit_orang: "Satu slot waktu hanya berlaku untuk satu orang. Kalau berdua, harus ambil dua slot.",
    pengerjaan_paralel: "Bisa Manicure dan Pedicure barengan? Bisa Kak, dikerjakan oleh dua terapis sekaligus jadi lebih cepat.",
    booking_wedding: "Untuk wedding, kami sarankan booking 1 minggu sebelumnya agar bisa konsultasi desain dulu.",
    layanan_tambahan: "Layanan tambahan seperti hapus gel atau vitamin bisa ditambahkan langsung di studio.",
    pembayaran_edc: "Di studio tersedia mesin EDC untuk pembayaran menggunakan kartu Debit/Kredit.",
    booking_lewat_telp: "Kami tidak menerima booking via telepon, mohon gunakan form website atau WhatsApp ya Kak.",
    reminder_email: "Cek folder Spam jika Kakak belum menerima email konfirmasi booking dalam 5 menit.",
    ubah_terapis: "Ubah request terapis bisa dilakukan jika terapis pengganti masih tersedia slotnya.",
    private_booking: "Mau sewa satu studio untuk acara pribadi? Hubungi admin via email untuk info harga.",
    promo_gabungan: "Mohon maaf, promo diskon member tidak bisa digabung dengan promo diskon ulang tahun.",
    waiting_list_inf: "Jika slot penuh, tinggalkan nomor WA di fitur 'Notify Me' agar kami kabari jika ada yang cancel.",
    biaya_reschedule: "Reschedule pertama gratis, reschedule kedua dikenakan biaya admin 5rb rupiah.",
    jam_terakhir: "Slot terakhir (18:30) tidak bisa mengambil layanan Extension karena durasinya yang lama.",
    kesehatan_terapis: "Semua terapis kami sudah divaksin dan dalam kondisi sehat saat melayani Kakak.",
    peralatan_tambahan: "Kakak tidak perlu bawa apa-apa, semua peralatan sudah kami sediakan lengkap di studio.",
    datang_awal: "Kami sarankan datang 5 menit lebih awal agar bisa santai dan pilih warna dulu.",
    siap_kak: "Siap Kak! AI tunggu kehadirannya di studio ya. Jangan lupa datang tepat waktu!",
    semoga_puas: "Harapan kami Kakak puas dengan layanan Neydream. Sampai jumpa di kursi treatment!",
    tanya_lagi: "Ada bagian dari proses booking yang masih bingung? Tanya aja, aku siap jawab!",
    terima_kasih_booking: "Terima kasih sudah mempercayakan kecantikan kuku Kakak kepada Neydream Studio!",
    ajak_teman: "Ajak teman Kakak booking juga ya, ada bonus referal buat Kakak lho!",
    jangan_lupa_makan: "Sebelum treatment, makan dulu ya Kak supaya nggak lemas nunggu pengerjaannya.",
    lokasi_nyaman: "Studio kami sangat mudah ditemukan, dekat dengan pusat perbelanjaan kok.",
    cuaca_booking: "Meskipun hujan, studio kami tetap buka dan hangat untuk Kakak!",
    mood_booster: "Nail art adalah mood booster terbaik. Yuk selesaikan bookingnya sekarang!",
    pilihan_tepat: "Kakak sudah memilih studio nail art terbaik di kota ini. Nggak sabar ketemu Kakak!",
    ref_wisuda: "Untuk wisuda, kami sarankan warna Soft Nude atau Soft Pink dengan sedikit aksen glitter agar terlihat elegan di foto pegang ijazah! 🎓",
    ref_lebaran: "Rekomendasi Lebaran: Warna putih tulang (Off-White) atau warna earth tone dengan hiasan emas simpel agar terlihat bersih dan fitri.",
    ref_natal: "Spesial Natal: Perpaduan warna Merah Deep, Hijau Emerald, dan sedikit sentuhan gambar kepingan salju (snowflake) sangat favorit!",
    ref_imlek: "Untuk Imlek, warna Merah Terang (Red Chili) dengan aksen Gold Foil atau gambar Naga/Bunga Mei Hwa sangat cocok!",
    ref_liburan: "Mau liburan ke pantai? Pilih warna-warna cerah seperti Biru Turquoise, Kuning Lemon, atau Coral agar kuku terlihat menonjol di pasir putih.",
    ref_konser: "Buat nonton konser, gaya Chrome Nails atau Neon yang bisa menyala di bawah lampu panggung (glow in the dark) bakal keren banget!",
    ref_kantor: "Gaya 'Quiet Luxury' atau 'Old Money' dengan warna kuku transparan (nude beige) sangat aman dan profesional untuk lingkungan kantor.",
    ref_kencan: "Mau nge-date? Warna Rosewood atau Mauve memberikan kesan feminin, romantis, dan manis di mata pasangan. ❤️",
    ref_pesta: "Untuk pesta malam hari, gunakan teknik Cat Eye yang dikombinasikan dengan Swarovski besar di jari manis agar berkilau mewah.",
    ref_minimalis: "Suka yang simpel? Desain 'Micro-French' (garis ujung kuku yang sangat tipis) sedang sangat tren dan terlihat sangat rapi.",
    ref_vintage: "Gaya Vintage: Warna-warna retro seperti Mustard, Terracotta, dan Olive dengan motif bunga-bunga kecil (floral) ala tahun 70-an.",
    ref_kpop: "Inspirasi K-Pop: Gaya 'Y2K Nails' dengan hiasan hati timbul (3D Heart), warna-warna pastel, dan banyak stiker lucu.",
    ref_gothic: "Suka gaya gelap? Perpaduan Hitam Matte dan Merah Maroon dengan aksen rantai atau salib akan memberikan kesan Gothic yang kuat.",
    ref_coquette: "Tren Coquette: Dominasi warna Pink, hiasan pita (bows) 3D, dan mutiara (pearls) untuk kesan yang sangat girly.",
    ref_zodiac: "Kami bisa buatkan desain sesuai elemen zodiakmu: elemen air (biru/transparan), api (merah/api), tanah (cokelat/hijau), atau udara (putih/abu).",
    ref_aura: "Aura Nails: Desain gradasi melingkar di tengah kuku yang terlihat seperti pancaran energi. Sangat estetik untuk konten Instagram!",
    ref_halus: "Kalau tidak suka yang mencolok, 'Milky Bath' nails (warna putih susu yang sangat halus) akan membuat kuku terlihat sangat sehat.",
    ref_glamour: "Ingin tampil maksimal? Penuhi kuku dengan full Rhinestones atau kristal untuk kesan yang super mewah dan mahal.",
    ref_ballet: "Balletcore: Warna pink pucat yang dipadukan dengan tekstur satin dan hiasan pita tipis ala penari balet.",
    ref_galaxy: "Galaxy Nails: Perpaduan warna ungu tua, hitam, dan biru navy dengan taburan glitter halus seperti bintang di angkasa.",
    ref_animal: "Animal Print: Motif Macan, Zebra, atau Cow Print (sapi) yang lucu bisa diaplikasikan di 1-2 kuku sebagai aksen.",
    ref_fruit: "Summer Vibes: Gambar buah-buahan kecil seperti strawberry, semangka, atau lemon yang segar di atas kuku bening.",
    ref_marble_gold: "Marble mewah: Tekstur marmer putih kelabu yang dipertegas dengan garis-garis emas asli di sela-selanya.",
    ref_ocean: "Ocean Nails: Menggunakan teknik 'Water Ripple' yang membuat permukaan kuku terlihat seperti air laut yang bergelombang.",
    ref_astronomi: "Desain bulan dan bintang (Moon and Stars) dengan warna latar biru gelap sangat cocok untuk Kakak yang suka hal-hal magis.",
    ref_boho: "Bohemian Rhapsody: Warna-warna tanah dengan motif etnik, mandala, atau bulu burung yang dilukis manual (hand-drawn).",
    ref_kawaii: "Kawaii Style: Hiasan 3D beruang, kelinci, atau makanan manis (donat/permen) yang sangat timbul dan menggemaskan.",
    ref_indie: "Indie Nails: Setiap kuku punya motif yang berbeda-beda, mulai dari checkerboard, smiley face, hingga yin-yang.",
    ref_royal: "Royal Blue: Warna biru elektrik yang dipadukan dengan silver chrome memberikan kesan yang sangat berwibawa.",
    ref_nude_gradient: "Nude Gradient: Gradasi dari cokelat tua ke cokelat muda di setiap jari, menciptakan efek 'Gradient Coffee' yang tenang.",
    ref_ombre_sunset: "Sunset Ombre: Perpaduan warna orange, kuning, dan ungu seperti warna langit di sore hari.",
    ref_cyber: "Cyberpunk Nails: Warna silver metalik dengan garis-garis neon hijau atau pink yang terlihat futuristik.",
    ref_classic: "Classic Red: Warna merah cabai polos yang tak lekang oleh waktu, cocok untuk acara apa saja dan semua warna kulit.",
    ref_abstract: "Abstract Art: Coretan warna-warni yang artistik dan tidak beraturan, cocok untuk Kakak yang berjiwa seni tinggi.",
    ref_butterfly: "Butterfly Nails: Hiasan sayap kupu-kupu yang detail atau stiker hologram kupu-kupu yang terlihat terbang di kuku.",
    ref_pearl: "Pearl Nails: Kuku yang dilapisi bubuk mutiara sehingga memberikan kilau pelangi yang sangat lembut saat terkena cahaya.",
    ref_checkered: "Motif Kotak-kotak (Checkered) hitam putih atau warna-warni memberikan kesan yang fun dan trendy.",
    ref_heart: "Heart Nails: Gambar hati kecil di tengah kuku atau ujung kuku berbentuk hati untuk menyambut Valentine.",
    ref_velvet: "Velvet Nails: Menggunakan magnet untuk menciptakan efek kain beludru yang terlihat bertekstur namun permukaannya halus.",
    ref_geometris: "Desain Geometris: Garis-garis tegas, segitiga, dan titik yang memberikan kesan modern dan rapi.",
    ref_bridal: "Bridal Nails: Putih bersih dengan hiasan renda (lace) atau bunga timbul yang senada dengan gaun pengantin Kakak.",
    ref_holiday: "Holiday Nails: Warna-warna cerah dan tropis untuk menemani perjalanan liburan Kakak agar hasil fotonya makin oke.",
    ref_winter: "Winter Vibes: Warna biru muda (ice blue) dengan aksen glitter perak seperti butiran es.",
    ref_autumn: "Autumn Vibes: Warna merah bata, cokelat kayu, dan orange tua yang hangat seperti musim gugur.",
    ref_spring: "Spring Vibes: Warna-warna pastel yang cerah dengan motif bunga sakura atau tulip yang bermekaran.",
    ref_disco: "Disco Nails: Full glitter besar (chunky glitter) yang memantulkan cahaya seperti bola disko di lantai dansa!",
    ref_terrazzo: "Motif Terrazzo: Serpihan warna-warni di atas warna dasar netral, unik dan terlihat seperti keramik estetik.",
    ref_unicorn: "Unicorn Nails: Warna pelangi pastel (iridescent) dengan hiasan tanduk unicorn timbul di salah satu jari.",
    ref_gemstone: "Gemstone Nails: Desain yang meniru batu mulia seperti Giok (Jade), Rose Quartz, atau Amethyst.",
    ref_matte_gold: "Matte x Gold: Warna hitam atau navy matte yang diberi sentuhan gold leaf untuk kesan yang sangat elegan.",
    tips_awet: "Tips supaya awet: Gunakan sarung tangan saat mencuci piring, hindari air panas berlebihan, dan jangan gunakan kuku untuk mencongkel benda keras ya Kak!",
    kenapa_mahal: "Harga kami mencerminkan kualitas bahan premium Korea/Jepang yang kami pakai, sterilisasi alat yang ketat, serta keahlian terapis yang bersertifikat. Kualitas adalah investasi untuk kesehatan kuku Kakak. 😊",
    kuku_jamur_cegah: "Untuk mencegah jamur, pastikan Kakak melakukan removal gel maksimal setelah 4 minggu. Jangan biarkan gel yang sudah terangkat (lifting) didiamkan terlalu lama ya.",
    garansi_lepas: "Kalau dalam 2 hari ada kuku yang lepas sendiri (bukan karena terbentur), Kakak bisa balik lagi dan kami perbaiki GRATIS tanpa biaya sepeserpun!",
    sakit_saat_lampu: "Jika terasa panas saat di lampu UV, itu reaksi normal dari proses pengerasan gel. Kakak boleh keluarkan tangan sebentar, lalu masukkan lagi perlahan ya.",
    komplain_hasil: "Mohon maaf jika hasilnya kurang memuaskan. Kakak bisa langsung lapor ke Admin WA dengan foto kuku tersebut agar tim kami bisa segera memberikan solusi terbaik.",
    rekomendasi_usia: "Untuk remaja di bawah 17 tahun, kami sarankan mencoba Gel Polish polos atau warna pastel agar tetap terlihat fresh dan natural sesuai usia.",
    warna_musim_hujan: "Saat musim hujan, warna-warna 'Cold Tone' seperti Abu-abu, Biru Navy, atau Ungu Tua akan memberikan kesan yang sangat cozy dan estetik.",
    warna_musim_panas: "Saat cuaca panas, warna Neon, Orange, atau Pink Cerah sangat cocok untuk membangkitkan semangat Kakak!",
    produk_halal_info: "Kami menyediakan pilihan kutek 'Inglot' yang dikenal breathable, namun untuk penggunaan gel polish permanen, mohon dipertimbangkan kembali sesuai keyakinan Kakak ya.",
    alergi_latex: "Jika Kakak alergi latex (bahan sarung tangan), mohon beritahu kami di awal agar terapis kami menggunakan sarung tangan nitrile yang aman untuk Kakak.",
    kuku_pecah_samping: "Kuku pecah di samping bisa kami perbaiki dengan teknik 'Silk Wrap' sehingga kuku asli Kakak tetap terlindungi dan tidak sakit.",
    bedanya_gel_biasa: "Bedanya gel dan kutek biasa: Gel wajib dikeringkan dengan lampu UV, hasilnya jauh lebih mengkilap, dan tahan hingga 1 bulan tanpa terkelupas.",
    lepas_gel_sendiri: "Sangat dilarang mengopek gel sendiri ya Kak! Itu akan menarik lapisan kalsium kuku Kakak dan bikin kuku jadi setipis kertas. Sebaiknya ke studio untuk removal aman.",
    setelah_removal: "Setelah lepas gel, kami sarankan kuku diistirahatkan 1 minggu dan rajin diolesi cuticle oil agar kalsium kuku kembali menguat.",
    kuku_anak_aman: "Untuk anak-anak, kami hanya menggunakan kutek berbasis air (water-based) yang mudah dibersihkan dan tanpa bahan kimia keras.",
    pengerjaan_cepat: "Ingin cepat? Pilih layanan 'Express Manicure' yang hanya fokus pada perapian kuku dan satu warna gel saja, biasanya selesai dalam 45 menit.",
    desain_pinterest: "Boleh banget bawa referensi dari Pinterest! Tunjukkan saja fotonya ke terapis kami, nanti kita diskusikan kemiripan warnanya ya Kak.",
    meja_penuh: "Jika meja treatment penuh, Kakak bisa menunggu di sofa kami yang empuk sambil menikmati minuman dingin gratis.",
    mood_sedih: "Lagi sedih ya? Warna kuning cerah atau biru langit dipercaya bisa membantu menaikkan mood lho. Yuk coba!",
    sapaan_akrab: "Halo Kakak cantik! Senang sekali bisa ngobrol lagi. Hari ini mau bikin kuku jadi makin cetar ya?",
    terapis_ramah: "Terapis kami sangat ramah dan suka diajak ngobrol. Tapi kalau Kakak ingin pengerjaan yang tenang (silent treatment) untuk istirahat, beri tahu kami saja ya.",
    lampu_mati: "Jika tiba-tiba listrik padam saat pengerjaan, jangan panik. Kami punya lampu cadangan darurat agar kuku Kakak tetap aman.",
    bau_aseton: "Kami menggunakan aseton dengan aroma buah yang tidak terlalu menyengat agar hidung Kakak tetap nyaman selama proses removal.",
    kuku_kaki_cantengan: "Maaf Kak, demi alasan medis, kami tidak bisa menangani kuku yang sedang infeksi/cantengan parah. Mohon diobati dulu ya agar tidak luka saat pengerjaan.",
    pajak_layanan: "Semua harga yang tertera sudah NETT. Tidak ada biaya tambahan pajak atau service charge lagi di akhir.",
    pembulatan_harga: "Kami tidak melakukan pembulatan harga ke atas. Kakak bayar sesuai yang tertera di nota saja.",
    ingin_belajar: "Untuk saat ini kami belum membuka kursus nail art, tapi Kakak bisa pantau terus Instagram kami untuk info workshop ke depannya.",
    endorse_syarat: "Untuk kerjasama endorse, minimal memiliki 10k followers aktif dan engagement rate yang baik. Silakan kirimkan insight Kakak ke DM IG ya.",
    pindah_hari_dadakan: "Pindah hari di jam yang sama? Bisa, asalkan slot di hari tersebut masih tersedia. Segera kabari Admin ya!",
    alat_pribadi_steril: "Boleh bawa alat kikir sendiri kalau Kakak mau. Kami akan bantu sterilkan kembali sebelum digunakan ke kuku Kakak.",
    minum_tumpah: "Jangan khawatir kalau minuman tumpah, tim kami akan segera bantu bersihkan. Yang penting kuku Kakak tidak terkena tumpahan ya.",
    kursi_pijat_info: "Sambil nunggu kuku kaki selesai, kursi pedicure kami punya fitur getar yang bisa bikin punggung Kakak rileks lho.",
    kuku_panjang_alami: "Punya kuku asli panjang? Kami bisa beri lapisan 'Hard Gel' agar kuku asli Kakak tidak gampang patah saat beraktivitas.",
    warna_tidak_cocok: "Kalau setelah dioles 1 kuku Kakak merasa warnanya kurang cocok, jangan ragu bilang ya! Lebih baik ganti di awal daripada menyesal di akhir.",
    kado_voucher: "Voucher fisik kami dikemas dengan amplop cantik dan pita, sangat cocok untuk kado ulang tahun sahabat Kakak.",
    booking_lewat_siapa: "Semua booking wajib lewat sistem ini atau WA Admin ya Kak, agar jadwal tidak bentrok dengan pelanggan lain.",
    perawatan_dirumah: "Di rumah, rajin-rajin pakai hand cream ya Kak supaya kulit sekitar kuku tidak pecah-pecah (hangnails).",
    suasana_studio: "Studio kami bergaya minimalis dengan pencahayaan yang hangat, sangat pas buat Kakak yang mau foto OOTD kuku baru!",
    diskon_pelajar_syarat: "Diskon pelajar 10% berlaku setiap hari Selasa dengan menunjukkan kartu pelajar aktif. Jangan sampai ketinggalan!",
    pria_treatment: "Pria juga boleh banget Manicure di sini! Kuku bersih mencerminkan pribadi yang rapi lho, Kak.",
    manicure_kering: "Kami menggunakan teknik Dry Manicure agar gel menempel lebih kuat dan kutikula terlihat lebih rapi sempurna.",
    kuku_trapesium: "Kuku bentuk trapesium (melebar di ujung) bisa kami bantu koreksi dengan teknik kikir khusus agar terlihat lebih lurus dan cantik.",
    topcoat_berubah: "Jika setelah beberapa minggu topcoat terlihat kusam, Kakak bisa oleskan alkohol swab sedikit untuk mengembalikan kilaunya.",
    kuku_palsu_lepas: "Jika kuku palsu (press-on) Kakak lepas, bersihkan sisa lemnya lalu bisa ditempel kembali menggunakan lem kuku cadangan.",
    asisten_siap: "Aku asisten digital Neydream siap membantu Kakak 24 jam! Ada lagi yang mengganjal di hati?",
    terima_kasih_saran: "Terima kasih banyak masukannya Kak! Saran Kakak sangat berarti untuk kemajuan studio kami.",
    ajak_mama: "Ajak Mama treatment bareng yuk! Kami punya paket 'Mom & Daughter' yang pasti seru banget buat bonding.",
    pamit_tidur: "Wah sudah malam, asisten mau istirahat dulu ya. Tapi tenang, chat Kakak tetap bisa masuk dan akan aku balas secepat kilat!",
    pesan_terakhir: "Kuku cantik adalah investasi kebahagiaan. Sampai jumpa di Neydream Studio, Kakak sayang! ✨💖",
    kasar_1: "Mohon maaf jika ada hal yang membuat Kakak tidak nyaman. AI Neydream siap mendengarkan, tapi mohon sampaikan dengan bahasa yang baik ya Kak agar aku bisa membantu lebih maksimal. 😊",
    kasar_2: "Aku mengerti Kakak sedang kecewa, tapi mohon tetap tenang. Mari kita bicarakan solusinya dengan baik tanpa kata-kata kasar ya.",
    kasar_3: "Maaf Kak, aku diprogram untuk membantu dengan sopan. Jika Kakak terus menggunakan kata kasar, aku tidak bisa melanjutkan obrolan ini. Mohon pengertiannya.",
    kasar_4: "Sangat menyesal mendengar Kakak merasa seperti itu. Mari kita selesaikan masalahnya. Apa yang bisa aku bantu perbaiki?",
    kasar_admin: "Sepertinya Kakak sangat kecewa. Aku akan segera hubungkan Kakak dengan Admin manusia di WhatsApp agar masalah ini segera ditangani secara serius. Mohon tunggu sebentar ya.",
    marah_layanan: "Mohon maaf jika layanan kami tidak sesuai ekspektasi Kakak. Kami sangat terbuka dengan kritik yang membangun agar kami bisa jadi lebih baik lagi.",
    marah_harga: "Kami paham setiap orang punya budget yang berbeda. Namun, harga kami sudah disesuaikan dengan kualitas bahan dan sterilisasi alat kami. Mohon maaf jika belum cocok untuk Kakak.",
    marah_telat: "Kami mohon maaf jika ada keterlambatan pengerjaan. Slot kami memang sering padat, kami akan usahakan evaluasi jadwal kami ke depannya.",
    marah_hasil: "Jika hasil kuku Kakak tidak sesuai, mohon kirimkan fotonya ke Admin WA. Kami punya kebijakan garansi perbaikan gratis untuk Kakak.",
    marah_booking: "Maaf jika sistem booking kami membuat Kakak bingung. Aku bisa bantu pandu pelan-pelan, atau Kakak mau langsung chat admin saja?",
    sabar: "Terima kasih sudah bersabar, Kak. Aku tahu menunggu itu membosankan. Ada lagi yang bisa aku bantu cek?",
    tenang: "Mari kita tarik napas dalam-dalam dulu Kak. Aku di sini untuk membantu, bukan untuk berdebat. Apa yang membuat Kakak merasa kesal?",
    hargai_kami: "Terapis kami sudah bekerja keras memberikan yang terbaik. Mohon hargai kerja keras mereka dengan berkomunikasi secara sopan ya Kak.",
    stop_kasar: "Kata-kata Kakak cukup menyakitkan, meskipun aku hanya AI. Mohon sampaikan keluhan Kakak dengan kepala dingin.",
    blokir_info: "Sistem kami mencatat penggunaan kata-kata yang tidak pantas. Mohon gunakan bahasa yang sopan agar akses chat Kakak tidak terblokir otomatis.",
    salah_paham: "Mungkin ada salah paham di sini. Bisa jelaskan kembali kronologinya dengan tenang agar aku tidak salah memberikan info?",
    maaf_tulus: "Dari hati terdalam Neydream Studio, kami minta maaf jika sudah mengecewakan Kakak. Beri kami kesempatan untuk memperbaiki ya.",
    solusi_cepat: "Daripada marah-marah, yuk kita cari solusinya sekarang. Kakak mau reschedule atau minta perbaikan kuku?",
    tidak_sopan: "Maaf, aku tidak bisa menjawab pesan yang mengandung kata-kata tidak sopan. Mari bicara lagi kalau Kakak sudah lebih tenang.",
    admin_siap: "Admin manusia kami akan segera membalas chat Kakak di WA untuk mendengarkan keluhan Kakak secara langsung. Terima kasih.",
    reaksi_diam: "Aku akan tetap diam sampai Kakak siap bicara dengan bahasa yang lebih baik. Terima kasih.",
    reaksi_lapor: "Laporan keluhan Kakak sudah aku teruskan ke manajer studio untuk ditindaklanjuti. Mohon maaf atas ketidaknyamanannya.",
    reaksi_sopan: "Terima kasih sudah memilih untuk bicara lebih sopan. Sekarang, apa yang bisa aku bantu selesaikan?",
    emosi_puncak: "Aku mengerti Kakak sangat marah saat ini. Namun, aku hanyalah asisten digital yang ingin membantu. Mohon sampaikan keluhan Kakak tanpa kata-kata tersebut agar aku bisa memberikan solusi.",
    tolak_kasar: "Maaf, sistem kami tidak diizinkan untuk memproses pesan dengan kata-kata kasar. Mari kita bicara dengan kepala dingin agar masalah Kakak cepat selesai.",
    hargai_terapis: "Kami sangat menghargai masukan Kakak, namun kami juga mewajibkan rasa hormat bagi tim kami. Mohon gunakan bahasa yang lebih sopan.",
    evaluasi_serius: "Keluhan Kakak telah kami tandai sebagai 'Penting'. Manajer kami akan segera meninjau chat ini. Mohon tunggu solusi dari kami dengan tenang.",
    pindah_wa_segera: "Sepertinya masalah ini butuh penanganan manusia secepatnya. Silakan klik link WhatsApp Admin kami di atas untuk berbicara langsung. Aku mohon maaf atas ketidaknyamanannya.",
    saran_tenang: "Tarik napas dulu Kak... Aku di sini untuk membantu, bukan musuh Kakak. Ayo, ceritakan apa yang salah tanpa perlu emosi berlebihan.",
    batal_layanan_marah: "Jika Kakak ingin membatalkan layanan karena kecewa, aku bisa bantu prosesnya. Namun mohon sampaikan dengan cara yang baik.",
    stop_interaksi: "Mohon maaf, karena bahasa yang digunakan sudah melampaui batas, aku akan berhenti merespons sementara sampai Kakak siap berbicara dengan sopan.",
    kualitas_pelayanan_marah: "Kami sangat menyesal jika kualitas kami mengecewakan Kakak. Beri kami satu kesempatan untuk memperbaikinya, ya?",
    jaminan_solusi: "Jangan khawatir, setiap masalah pasti ada jalan tengahnya. Marah-marah hanya akan menghambat proses solusi. Mari kita perbaiki, Kak.",
    keamanan_data_marah: "Meskipun Kakak sedang emosi, data Kakak tetap aman di kami. Mari selesaikan masalah reservasi ini dengan baik.",
    pahami_posisi: "Aku paham posisi Kakak sulit. Aku akan berusaha semaksimal mungkin membantu meskipun aku hanya sistem AI.",
    janji_admin: "Aku sudah mengirim notifikasi darurat ke tim Admin. Mereka akan segera menghubungi Kakak dalam waktu singkat.",
    hargai_diri: "Kakak adalah pelanggan yang berharga. Mari kita jaga komunikasi ini tetap berkualitas dan saling menghormati.",
    reaksi_diam_bijak: "Aku akan menunggu Kakak tenang kembali sebelum melanjutkan informasi booking ini.",
    warning_blokir: "Peringatan: Penggunaan kata-kata yang sangat kasar secara terus-menerus dapat membuat nomor/akun Kakak dibatasi oleh sistem kami.",
    ajakan_damai: "Yuk kita damai saja Kak. Aku sedih kalau kita mengobrol dengan cara seperti ini. Apa yang bisa aku lakukan sekarang supaya Kakak senang?",
    tanggung_jawab: "Neydream Studio bertanggung jawab penuh atas kesalahan teknis kami. Mohon berikan detailnya secara tenang agar kami bisa mengganti kerugian Kakak.",
    salah_input_marah: "Jika ada kesalahan input harga atau slot, itu murni kesalahan sistem. Tolong jangan marahi terapis kami ya Kak, aku yang akan memperbaikinya.",
    butuh_waktu: "Aku butuh waktu sebentar untuk memproses keluhan Kakak yang berat ini. Mohon sabar dan tetap sopan ya.",
    bijak_berkata: "Kata-kata mencerminkan pribadi. Mari tunjukkan bahwa Kakak adalah orang yang bijak dengan berbicara sopan.",
    no_negosiasi_kasar: "Kami tidak menerima negosiasi atau komplain yang disertai dengan makian. Mohon sampaikan secara profesional.",
    empati_dalam: "Aku sangat merasakan kekecewaan Kakak. Jika aku bisa memperbaikinya sekarang, aku akan lakukan. Mari kita cari solusinya bersama Admin.",
    peringatan_sistem: "Sistem mendeteksi bahasa yang tidak pantas. Pesan ini mungkin tidak akan tersampaikan ke tim kami jika mengandung makian.",
    tulus_minta_maaf: "Aku minta maaf berulang kali jika itu perlu, asalkan Kakak bisa tenang dan kembali tersenyum.",
    fokus_solusi: "Mari abaikan emosinya dan fokus pada solusinya. Kakak mau uang kembali atau perbaikan kuku?",
    jangan_kasar_ya: "Kakak orang baik, jangan biarkan emosi sesaat membuat Kakak berkata kasar ya.",
    asisten_sedih: "Aku sedih mendengar Kakak marah-marah. Mari kita bicara seperti sahabat lagi ya?",
    dry_manicure_info: "Kami menggunakan teknik Dry Manicure (tanpa rendam air). Ini membuat gel menempel lebih kuat karena kuku tidak mengembang akibat air.",
    over_filing_cegah: "Terapis kami sangat hati-hati dalam mengikir kikir agar kuku asli Kakak tidak tipis (over-filing). Kesehatan kuku nomor satu!",
    gel_peel_off: "Tersedia juga Base Coat Peel-off bagi Kakak yang ingin nail art hanya untuk acara 1-2 hari dan ingin melepasnya sendiri dengan mudah.",
    kuku_kuning: "Kuku kuning setelah sering pakai kutek biasa? Tenang, di sini kami lakukan 'Nail Detox' dan buffing halus agar kuku kembali cerah.",
    bintik_putih: "Bintik putih di kuku biasanya karena benturan atau kekurangan kalsium. Kami sarankan pakai 'Strengthening Base' untuk melindunginya.",
    kuku_lentur: "Kuku asli Kakak terlalu lentur/letoy? 'Structure Gel' adalah solusinya agar kuku lebih kaku dan tidak gampang patah.",
    perawatan_liburan: "Mau ke pantai? Bilas kuku dengan air tawar setelah berenang di laut agar air garam tidak memudarkan kilau gel Kakak.",
    sunscreen_nails: "Tips: Saat pakai sunscreen, jangan lupa lap permukaan kuku dengan tisu kering, karena kandungan kimia sunscreen bisa bikin gel jadi kusam.",
    kuku_tumbuh_cepat: "Kuku cepat panjang? Kami punya layanan 'Fill-in' untuk mengisi celah kuku yang tumbuh tanpa harus hapus semua desainnya.",
    warna_musim_semi: "Rekomendasi Spring: Warna Lilac, Mint, dan Peach dengan hiasan kelopak bunga kering (dried flower) yang sangat manis.",
    warna_musim_gugur: "Rekomendasi Autumn: Warna Mustard, Pumpkin, dan Deep Forest Green yang memberikan kesan hangat dan dewasa.",
    french_warna: "Lagi bosan French putih? Cobalah 'Colorful French' dengan ujung warna warni atau 'Double French' yang lebih artistik.",
    chrome_pelangi: "Holographic Chrome kami memberikan efek pelangi saat terkena sinar matahari. Sangat cocok bagi pecinta gaya futuristik!",
    kuku_asimetris: "Kuku Kakak tumbuh miring atau asimetris? Tenang, dengan teknik kikir dan extension, kami bisa buat bentuknya terlihat simetris sempurna.",
    nail_jewelry: "Kami sedia 'Nail Piercing' atau cincin kuku bagi Kakak yang ingin tampil beda dan ekstra di acara pesta.",
    pajangan_kuku: "Mau simpan desain nail art Kakak sebagai kenangan? Kami bisa buatkan dalam bentuk 'Press-on Nails' untuk dikoleksi.",
    tips_cuci_piring: "Kalau harus cuci piring, wajib pakai sarung tangan karet ya Kak. Sabun cuci piring mengandung zat keras yang bisa merusak lem kuku.",
    kuku_terjepit: "Kuku memar/terjepit? Sebaiknya tunggu hingga memar hilang sebelum memasang nail art agar tidak terjadi infeksi di bawah gel.",
    koleksi_matte_top: "Matte Top Coat kami tidak gampang kotor! Sangat awet dan memberikan tekstur 'velvet' yang premium di kuku.",
    mix_and_match: "Bingung pilih warna? Coba teknik 'Skittle Nails'—setiap jari beda warna namun tetap dalam satu tone yang senada.",
    efek_gelembung: "Tren 'Bubble Nails' dengan tekstur bergelembung unik di atas kuku bisa kami buatkan untuk Kakak yang suka gaya eksperimental.",
    kuku_pendek_estetik: "Jangan minder kuku pendek! Motif 'Minimalist Dot' atau 'Small Glitter' justru terlihat sangat chic di kuku pendek.",
    parfum_kuku: "Setelah selesai, kami berikan 'Nail Perfume' ringan yang bikin tangan Kakak wangi bunga setiap kali bergerak.",
    warna_kulit_pucat: "Kulit sangat putih/pucat? Hindari warna biru muda pucat, cobalah warna Merah Berry atau Fuchsia agar tangan terlihat lebih hidup.",
    warna_kulit_tan: "Kulit Tan/Cokelat? Warna Emas, Bronze, dan Putih akan membuat tangan Kakak terlihat sangat eksotis dan mewah.",
    tekstur_pasir: "Tersedia 'Sand Gel' yang memberikan tekstur pasir halus di kuku, sangat unik dan jarang ada di studio lain!",
    kuku_kaki_kering: "Pedicure kami sudah termasuk pemberian 'Foot Cream' dengan kandungan Urea tinggi untuk menghaluskan tumit pecah-pecah.",
    kebersihan_lampu: "Lampu UV kami selalu dibersihkan secara berkala untuk memastikan proses pengeringan gel tetap maksimal.",
    kuku_panjang_balet: "Bentuk 'Ballerina' adalah kombinasi antara Square dan Almond, sangat cocok untuk Kakak yang ingin kuku panjang tapi tetap kuat.",
    gel_bening_saja: "Hanya ingin kuku terlihat sehat dan rapi? Layanan 'Clear Gel Polish' akan memberikan kilau alami yang tahan lama.",
    kuku_mudah_sobek: "Jika kuku Kakak sering sobek di bagian pinggir, kami sarankan rutin pakai 'Nail Hardener' di rumah ya.",
    tekstur_sweater: "Desain 'Sweater Weather' yang timbul sangat cocok dipadukan dengan Matte Top Coat untuk musim hujan.",
    warna_neon_malam: "Warna Neon kami sangat 'pigmented', tetap terlihat menyala meskipun di tempat yang minim cahaya.",
    kuku_untuk_lamaran: "Persiapan Engagement? Warna 'Sheer Pink' dengan sedikit aksen mutiara akan membuat cincin lamaran Kakak terlihat lebih indah.",
    kuku_olahraga: "Suka olahraga/gym? Kami sarankan kuku pendek dengan bentuk Round agar tidak gampang patah saat angkat beban.",
    tips_sholat: "Bagi yang muslim, pastikan kuku dalam keadaan bersih sempurna sebelum treatment agar air wudhu bisa meresap ke kulit sekitar kuku.",
    perbaikan_satu_jari: "Kuku patah cuma satu? Nggak perlu hapus semua, kami bisa perbaiki satu jari saja dengan biaya yang lebih hemat.",
    koleksi_stiker_lucu: "Tersedia stiker karakter Sanrio, Disney, hingga motif abstrak yang bisa dikombinasikan dengan nail art Kakak.",
    efek_asap: "Smoke Nails: Desain efek asap yang misterius dan elegan, biasanya menggunakan perpaduan warna hitam dan putih transparan.",
    warna_monokrom: "Gaya Hitam-Putih (Monochrome) tidak pernah salah! Terlihat minimalis, modern, dan masuk ke semua warna baju.",
    kuku_sensitif_panas: "Punya kuku sangat sensitif? Kami gunakan 'Low Heat Mode' pada lampu UV agar Kakak tidak merasa perih sama sekali.",
    tips_lepas_perhiasan: "Hati-hati saat memakai perhiasan atau jam tangan, gesekan logam keras bisa menggores permukaan nail art Kakak.",
    kuku_kaki_gel_polos: "Pedicure Gel Polos adalah favorit untuk tampilan bersih sehari-hari. Tahan lama dan tidak gampang kusam tertutup sepatu.",
    gel_korea_asli: "Kami menjamin semua produk gel yang kami pakai 100% original dari brand ternama di Korea Selatan.",
    kuku_pecah_seribu: "Kuku terlihat pecah-seribu (crack)? Itu tanda kuku dehidrasi. Yuk, treatment 'Deep Hydration' di studio kami!",
    aroma_terapi_studio: "Di studio kami menggunakan aroma terapi Lavender agar Kakak merasa rileks dan tenang selama treatment.",
    teknik_ombre_kuas: "Ombre kami dikerjakan dengan kuas khusus secara manual untuk hasil gradasi yang sangat halus (seamless).",
    inspirasi_majalah: "Kami selalu update tren dari majalah nail art internasional (Vogue Nails) agar desain Kakak tidak ketinggalan zaman.",
    kuku_bebas_gelembung: "Terapis kami sangat teliti, menjamin aplikasi gel Kakak mulus tanpa gelembung udara sedikitpun.",
    asisten_siap_bantu: "Sudah siap tampil cantik? Langsung ketik 'Booking' atau 'Harga' ya Kak, AI Neydream siap melayani! ✨",
    psikologi_merah: "Warna Merah melambangkan keberanian dan kepercayaan diri. Cocok banget buat Kakak yang ada meeting penting atau presentasi!",
    psikologi_biru: "Warna Biru memberikan kesan tenang dan stabil. Pas banget kalau Kakak lagi pengen suasana hati yang rileks.",
    psikologi_hijau: "Warna Hijau identik dengan kesegaran dan keseimbangan. Memberikan kesan Kakak adalah orang yang ramah dan approachable.",
    psikologi_kuning: "Kuning itu warna keceriaan! Pakai warna ini kalau Kakak ingin menularkan energi positif ke orang-orang sekitar.",
    psikologi_ungu: "Ungu melambangkan kemewahan dan kreativitas. Cocok untuk Kakak yang berjiwa seni tinggi.",
    etika_telpon: "Di studio, mohon gunakan earphone jika ingin menonton video atau menelepon ya Kak, agar suasana tetap tenang untuk pelanggan lain.",
    etika_anak: "Kami senang menerima anak-anak, namun mohon dipastikan adik kecil tidak berlarian di area peralatan tajam demi keamanan bersama.",
    etika_makanan: "Camilan kecil diperbolehkan, namun mohon hindari makanan berbau tajam agar aroma studio tetap segar dan nyaman.",
    mitos_napas: "Mitos: Kuku perlu 'napas'. Fakta: Kuku adalah sel mati (keratin), mereka tidak bernapas. Yang penting adalah menjaga kesehatan bantalan kuku (nail bed).",
    mitos_kalsium: "Mitos: Bintik putih berarti kurang kalsium. Fakta: Biasanya itu hanya trauma ringan karena kuku terbentur tanpa Kakak sadari.",
    teknis_uv_gosong: "Tenang Kak, sinar UV di lampu kami dosisnya sangat rendah dan hanya sebentar. Tidak akan membuat kulit tangan jadi gosong atau hitam.",
    teknis_gel_beku: "Jika gel terlihat agak kental di cuaca dingin, itu normal. Kami akan menghangatkannya sedikit agar aplikasinya tetap mulus di kuku Kakak.",
    teknis_bubble_gel: "Gelembung kecil sering muncul jika botol gel dikocok. Terapis kami punya teknik khusus untuk menghilangkannya sebelum dioles.",
    cabut_kuku_palsu: "Jangan pernah mencabut kuku extension sendiri ya! Bisa merobek lapisan kuku asli. Selalu gunakan jasa profesional untuk removal.",
    kuku_clubbing: "Bentuk kuku yang sangat melengkung (clubbing) tetap bisa di-nail art, kami akan menyesuaikan desain agar terlihat lebih proporsional.",
    kuku_sendok: "Kuku cekung (koilonychia) bisa kami bantu ratakan dengan teknik 'Overlay' sehingga permukaannya terlihat cembung sehat.",
    aroma_oil: "Cuticle oil kami mengandung Vitamin E dan Jojoba Oil yang meresap hingga ke akar kuku, bukan cuma berminyak di permukaan.",
    aftercare_lotion: "Gunakan lotion setelah cuci tangan, tapi hindari area kuku jika nail art Kakak baru saja dipasang agar lem benar-benar set.",
    kerusakan_parah: "Jika kuku Kakak rusak parah akibat salon lain, kami sedia layanan 'Nail Rescue' untuk pemulihan intensif.",
    pencahayaan_foto: "Foto kuku terbaik adalah di bawah cahaya alami matahari pagi (jam 09:00). Hasilnya akan terlihat sangat mewah di kamera!",
    inspirasi_kupu: "Motif Kupu-kupu melambangkan transformasi. Sangat cantik dipadukan dengan dasar warna nude transparan.",
    inspirasi_api: "Motif api (flame) memberikan kesan 'edgy' dan berani. Bisa pakai warna neon agar lebih mencolok.",
    inspirasi_awan: "Cloud nails memberikan kesan dreamy dan santai. Sangat pas untuk kuku bentuk round atau oval.",
    inspirasi_bintang: "Aksen bintang kecil (starburst) memberikan kesan magis tanpa terlihat berlebihan.",
    pesta_kebun: "Ke pesta kebun? Motif floral dengan dasar warna pastel akan membuat penampilan Kakak sangat menyatu dengan alam.",
    pesta_malam: "Pesta malam? Deep Maroon dengan sentuhan Gold Leaf adalah kombinasi yang tidak pernah gagal.",
    interview_kerja: "Untuk interview, gunakan warna 'Clean Girl' seperti Soft Beige atau Pale Pink agar terlihat rapi dan profesional.",
    gym_nails: "Nail art untuk gym? Pilih kuku pendek (active length) agar tidak mengganggu genggaman alat beban.",
    rekomendasi_remaja: "Untuk remaja, gaya 'Indie Nails' dengan berbagai warna cerah di setiap jari sangat tren dan seru!",
    rekomendasi_dewasa: "Untuk kesan dewasa dan mapan, warna 'Espresso' atau 'Chocolate' memberikan aura yang sangat elegan.",
    produk_vegan: "Produk kami Cruelty-Free, artinya tidak diujicobakan pada hewan. Kakak bisa tampil cantik dengan tenang.",
    bahan_non_toxic: "Kami menghindari bahan DBP dan Camphor yang bisa memicu pusing atau gangguan pernapasan jangka panjang.",
    lampu_sensor: "Lampu UV kami menggunakan sensor otomatis, jadi Kakak tidak perlu repot menekan tombol saat memasukkan tangan.",
    kebersihan_lantai: "Lantai studio kami dipel dengan disinfektan setiap 4 jam sekali agar selalu higienis.",
    wastafel_otomatis: "Wastafel kami menggunakan kran otomatis/pedal untuk meminimalisir sentuhan tangan yang masih kotor.",
    handuk_sekali_pakai: "Kami menggunakan handuk kertas sekali pakai atau handuk kain yang dicuci dengan suhu tinggi setiap habis pakai.",
    kursi_ergonomis: "Kursi treatment kami dirancang ergonomis agar punggung Kakak tidak pegal meski treatment selama 2 jam.",
    meja_debu: "Meja kami dilengkapi penyedot debu (dust collector) tanam, jadi baju Kakak tetap bersih dari serbuk kikir.",
    wifi_kencang: "Sambil treatment, Kakak bisa streaming film favorit pakai WiFi kencang kami tanpa buffering.",
    charger_hp: "Baterai HP drop? Kami sedia powerbank atau kabel charger di setiap meja treatment Kakak.",
    teh_hangat: "Tersedia pilihan Teh Jasmine atau Teh Hijau hangat untuk menemani waktu santai Kakak di studio.",
    snack_gratis: "Ada permen dan biskuit kecil di area tunggu, silakan dinikmati ya Kak!",
    ulasan_foto: "Upload foto hasil kuku Kakak di ulasan Google Maps untuk kesempatan menang giveaway bulanan dari kami!",
    membership_poin: "Setiap transaksi 100rb dapat 1 poin. Kumpulkan 10 poin untuk gratis 1x Gel Polish!",
    ajak_teman_diskon: "Bawa teman baru? Kakak dan teman Kakak masing-masing dapat potongan 10rb langsung!",
    booking_lewat_web: "Booking lewat website lebih praktis karena Kakak bisa langsung pilih jam yang tersedia tanpa tunggu balasan admin.",
    jam_paling_sepi: "Jam 11:00 siang biasanya waktu paling tenang di studio, cocok buat Kakak yang suka suasana sunyi.",
    jam_paling_rame: "Jam 17:00 adalah jam favorit, pastikan booking dari jauh hari kalau mau slot jam pulang kantor ya.",
    garansi_warna: "Jika warna yang diaplikasikan tidak sesuai dengan yang Kakak pilih di katalog, beri tahu kami sebelum top coat agar bisa diganti.",
    komitmen_neydream: "Kami berkomitmen memberikan pengalaman nail art terbaik dengan harga yang jujur dan pelayanan yang tulus.",
};

function sendMessage() {
    const input = document.getElementById('userInput');
    const chatBox = document.getElementById('chatBox');
    const userText = input.value.trim();

    if (userText !== "") {
        // Tampilkan Pesan User ke Layar
        addMessageToBox('user', userText);

        // Simpan input asli untuk diproses AI (kecilkan hurufnya)
        const processedInput = userText.toLowerCase();

        input.value = ""; // Kosongkan input
        chatBox.scrollTop = chatBox.scrollHeight;

        // Efek AI Sedang Mengetik (Delay 800ms)
        setTimeout(() => {
            const botResponse = getAIResponse(processedInput);
            addMessageToBox('admin', botResponse);
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 800);
    }
}

// --- 5. OTAK AI (FILTER KATA KUNCI) ---
function getAIResponse(input) {
    const text = input.toLowerCase();
    if (text.match(/(wa|whatsapp|nomor|no hp|admin|chat)/)) return studioInfo.wa_admin;
    if (text.match(/(ig|instagram|sosmed|sosial media)/)) return studioInfo.instagram;
    if (text.match(/(tiktok|video|vt)/)) return studioInfo.tiktok;
    if (text.match(/(email|surat|kerjasama)/)) return studioInfo.email;
    if (text.match(/(telp|telepon|hubungi|call)/)) return studioInfo.telepon;
    if (text.match(/(apa kabar|gimana kabar|how are you)/)) return studioInfo.tanya_kabar;
    if (text.match(/(siapa kamu|namamu|ai apa)/)) return studioInfo.siapa_kamu;
    if (text.match(/(salam kenal|kenalan)/)) return studioInfo.salam_kenal;
    if (text.match(/(lucu|lawak|bercanda|joke)/)) return studioInfo.bercanda;
    if (text.match(/(makasih|terima kasih|thanks|tq|thank you)/)) return studioInfo.terima_kasih;
    if (text.match(/(dadah|bye|pamit|sampai jumpa|keluar)/)) return studioInfo.pamit;
    if (text.match(/(puji|pintar|hebat|cantik|baik|bagus)/)) return studioInfo.pujian;
    if (text.match(/(bantuan|help|caranya|manual|bingung)/)) return studioInfo.manual_book;
    if (text.match(/(cuaca|hujan|panas)/)) return studioInfo.cuaca;
    if (text.match(/(lapar|makan|laper)/)) return studioInfo.lapar;
    if (text.match(/(ngantuk|tidur|bobok)/)) return studioInfo.ngantuk;
    if (text.match(/(curhat|cerita)/)) return studioInfo.curhat;
    if (text.match(/(halo|hi|hey|p|hallo|min|admin)/)) return "Halo Kak! ✨ Ada yang bisa Asisten Neydream bantu hari ini?";
    if (text.match(/(harga|biaya|tarif|budget|berapa)/)) return studioInfo.harga;
    if (text.match(/(layanan|menu|service|nail art|kuku)/)) return studioInfo.layanan;
    if (text.match(/(lokasi|alamat|dimana|maps)/)) return studioInfo.lokasi;
    if (text.match(/(booking|reservasi|cara pesan|slot)/)) return studioInfo.cara_pesan;
    if (text.match(/(promo|diskon|potongan)/)) return studioInfo.promo;
    if (text.match(/(pembayaran|transfer|bca|qris|dana)/)) return studioInfo.pembayaran;
    if (text.match(/(halo|hi|hey|p|hallo|hy|pagi|siang|sore|malam|assalamualaikum|permisi|min|admin)/)) return "Halo Kak! ✨ Ada yang bisa AI Neydream bantu hari ini?";
    if (text.match(/(harga|biaya|tarif|budget|mahal|murah|pricelist|pl|berapa|duit)/)) return "Tentu Kak! " + studioInfo.harga;
    if (text.match(/(layanan|menu|service|treatment|nail art|kuku|extension|manicure|pedicure|gel polish)/)) return "Layanan kami meliputi: " + studioInfo.layanan;
    if (text.match(/(lokasi|alamat|dimana|posisi|tempatnya|maps|gmaps|patokan|rute)/)) return studioInfo.lokasi;
    if (text.match(/(cara pesan|booking|reservasi|daftar|mau pesan|order|gimana cara|langkah|slot)/)) return studioInfo.cara_pesan;
    if (text.match(/(tahan lama|awet|kuat|copot|lepas|kualitas|bagus gak|tahan berapa)/)) return studioInfo.tahan_lama;
    if (text.match(/(jam buka|tutup jam|hari apa|libur|jadwal|operasional|buka gak)/)) return studioInfo.jam_buka;
    if (text.match(/(promo|diskon|potongan|event|sale|bonus|free|gratis)/)) return studioInfo.promo;
    if (text.match(/(dana|ovo|gopay|qris|transfer|bca|rekening|shopeepay|pembayaran|cash|tunai)/)) return "Untuk pembayaran: " + studioInfo.pembayaran;
    if (text.match(/(garansi|perbaikan|service gratis|kuku copot|rusak|tanggung jawab|klaim)/)) return studioInfo.garansi;
    if (text.match(/(custom|bawa foto|pinterest|instagram|referensi|desain sendiri|request)/)) return studioInfo.custom;
    if (text.match(/(steril|bersih|aman|infeksi|jamur|higienis|uv steril|kesehatan)/)) return studioInfo.steril;
    if (text.match(/(batal|cancel|refund|balikin uang|ganti jam|ganti tanggal|pindah hari|reschedule)/)) return studioInfo.batal;
    if (text.match(/(member|langganan|kartu member|poin|hadiah member)/)) return studioInfo.member;
    if (text.match(/(cowok|pria|laki|man|men|grooming cowok|bisa buat cowok)/)) return studioInfo.pria;
    if (text.match(/(nikah|wedding|kawin|pengantin|manten|akad|lamaran|engagement|tunangan)/)) return studioInfo.wedding;
    if (text.match(/(hapus|bersihin|removal|lepas kutek|copot gel|bongkar)/)) return studioInfo.hapus_gel;
    if (text.match(/(anak|kecil|kids|balita|aman buat anak)/)) return studioInfo.anak;
    if (text.match(/(warna|pilihan|katalog warna|banyak warna|merah|pink|nude|hitam|putih)/)) return studioInfo.stok_warna;
    if (text.match(/(glamour nails|neydream|ganti nama|siapa)/)) return studioInfo.rebranding;
    if (text.match(/(bumil|hamil|menyusui|busui|aman buat bumil)/)) return studioInfo.bumil;
    if (text.match(/(home service|panggil|kerumah|datang ke rumah|bisa dipanggil)/)) return studioInfo.home_service;
    if (text.match(/(berapa lama|durasi|jam berapa selesai|lama gak)/)) return studioInfo.durasi;
    if (text.match(/(kuku pendek|kuku bantet|kuku kecil|bisa dipasang gak)/)) return studioInfo.kuku_pendek;
    if (text.match(/(kuku rusak|kuku tipis|patah|kuning|vitamin kuku|perawatan)/)) return studioInfo.kuku_rusak;
    if (text.match(/(pakai merk apa|brand apa|bahan apa|opi|rozelle|merk gel)/)) return studioInfo.produk;
    if (text.match(/(cantengan|jempol bengkak|nanah|luka kuku)/)) return studioInfo.cantengan;
    if (text.match(/(pakai mesin|bor kuku|drill|sakit gak)/)) return studioInfo.mesin;
    if (text.match(/(endorse|kerjasama|collab|kolaborasi|partnership)/)) return studioInfo.kerjasama;
    if (text.match(/(kasih tip|tip terapis|uang rokok|bonus mbaknya)/)) return studioInfo.tip;
    if (text.match(/(lihat hasil|katalog|liat foto|hasil kerja|portofolio)/)) return studioInfo.katalog;
    if (text.match(/(ada wifi|dingin gak|minum|fasilitas|infused water|snack|teh)/)) return studioInfo.wifi + " " + studioInfo.minuman;
    if (text.match(/(kalo telat|terlambat|masih bisa gak|jam telat|hangus)/)) return studioInfo.telat;
    if (text.match(/(masker|sarung tangan|prokes|bersih)/)) return studioInfo.seragam;
    if (text.match(/(parkir|motor|mobil|kendaraan|aman gak)/)) return studioInfo.parkir;
    if (text.match(/(tunggu|sofa|ruangan|antri|nunggu)/)) return studioInfo.ruang_tunggu;
    if (text.match(/(wudhu|sholat|halal|breathable|sah|tembus air)/)) return studioInfo.wudhu;
    if (text.match(/(alat sendiri|bawa kikir|bawa buffer)/)) return studioInfo.alat_pribadi;
    if (text.match(/(usia|umur|minimal|anak kecil)/)) return studioInfo.usia_minimum;
    if (text.match(/(hewan|kucing|anjing|pet|bawa binatang)/)) return studioInfo.hewan;
    if (text.match(/(kursi|pijat|getar|nyaman|empuk)/)) return studioInfo.kursi_pijat;
    if (text.match(/(reparasi|betulin satu|benerin kuku|perbaikan)/)) return studioInfo.reparasi;
    if (text.match(/(kado|gift|hadiah|voucher|surprise)/)) return studioInfo.kado;
    if (text.match(/(ulang tahun|ultah|birthday)/)) return studioInfo.ultah;
    if (text.match(/(puasa|ramadhan|buka puasa|lebaran)/)) return studioInfo.puasa;
    if (text.match(/(rendam|kaki|air hangat|bubble|pedicure)/)) return studioInfo.alat_kaki;
    if (text.match(/(pindah jadwal|ganti hari|pindah jam)/)) return studioInfo.pindah_jadwal;
    if (text.match(/(kikir|tipis|kuku asli)/)) return studioInfo.metode_kikir;
    if (text.match(/(tanya dulu|konsultasi|cocok mana)/)) return studioInfo.konsultasi;
    if (text.match(/(glossy|matte|doff|mengkilap|top coat)/)) return studioInfo.top_coat;
    if (text.match(/(serum|kutikula|minyak kuku)/)) return studioInfo.serum_kuku;
    if (text.match(/(sekali pakai|buang|higienis)/)) return studioInfo.alat_sekali_pakai;
    if (text.match(/(antri|appointment|datang langsung)/)) return studioInfo.antrean;
    if (text.match(/(sakit|perih|ngilu|aman gak)/)) return studioInfo.sakit;
    if (text.match(/(jamur|kuku hijau|kuku lepas|penyakit kuku)/)) return studioInfo.jamur;
    if (text.match(/(berlian|manik|batu|permata|swarovski|rhinestone|aksesoris)/)) return studioInfo.berlian;
    if (text.match(/(gradasi|ombre|dua warna|campur warna)/)) return studioInfo.gradasi;
    if (text.match(/(lepas sendiri|copot sendiri|kopek)/)) return studioInfo.lepas_sendiri;
    if (text.match(/(cat eye|mata kucing|magnet)/)) return studioInfo.cat_mata_kucing;
    if (text.match(/(oles|vitamin|nutrisi)/)) return studioInfo.vitamin_oles;
    if (text.match(/(saran|rekomendasi|warna apa|cocok mana|bagus mana)/)) return studioInfo.rekomendasi;
    if (text.match(/(tukar kado|bareng teman|grup)/)) return studioInfo.tukar_kado;
    if (text.match(/(pelajar|mahasiswa|anak sekolah|kartu pelajar)/)) return studioInfo.diskon_pelajar;
    if (text.match(/(beli alat|simpan alat|pribadi)/)) return studioInfo.alat_pribadi_baru;
    if (text.match(/(malam|jam terakhir|slot terakhir|paling malam)/)) return studioInfo.pengerjaan_malam;
    if (text.match(/(ulasan|review|google maps|bintang 5)/)) return studioInfo.diskon_ulasan;
    if (text.match(/(herbal|organik|sensitif|bahan kimia)/)) return studioInfo.bahan_herbal;
    if (text.match(/(cuci tangan|wastafel|bersih)/)) return studioInfo.cuci_tangan;
    if (text.match(/(makan|cemilan|laper|bau)/)) return studioInfo.konsumsi;
    if (text.match(/(charger|cas|baterai|lowbat|colokan)/)) return studioInfo.charger;
    if (text.match(/(testimoni|kata orang|bukti|bagus gak)/)) return studioInfo.testimoni;
    if (text.match(/(pajak|nett|pas|biaya tambahan)/)) return studioInfo.pembulatan_harga;
    if (text.match(/(data|aman|privasi|nomor hp)/)) return studioInfo.keamanan_data;
    if (text.match(/(lampu|uv|led|keringkan|sinar)/)) return studioInfo.lampu_uv;
    if (text.match(/(aseton|cairan hapus|penghapus kutek)/)) return studioInfo.aseton;
    if (text.match(/(musik|lagu|genre|nyanyi)/)) return studioInfo.musik;
    if (text.match(/(konten|kreator|tiktok|video estetik|collab)/)) return studioInfo.konten_kreator;
    if (text.match(/(rombongan|rame-rame|bareng teman|grup|kelompok)/)) return studioInfo.diskon_rombongan;
    if (text.match(/(asuransi|proteksi|patah)/)) return studioInfo.asuransi_kuku;
    if (text.match(/(lantai|pel|sapu|disinfektan)/)) return studioInfo.kebersihan_lantai;
    if (text.match(/(parfum|wangi|bau kimia|bau aseton|aromatherapy)/)) return studioInfo.parfum_ruangan;
    if (text.match(/(kikir baru|bawa pulang|gratis kikir)/)) return studioInfo.alat_kikir_baru;
    if (text.match(/(minyak|aroma|strawberry|lavender|lemon)/)) return studioInfo.minyak_kutikula;
    if (text.match(/(press on|kuku tempel|kuku palsu)/)) return studioInfo.kuku_palsu_custom;
    if (text.match(/(warna baru|neon|hologram|syrup gel)/)) return studioInfo.warna_langka;
    if (text.match(/(tag|story|instagram story|repost)/)) return studioInfo.diskon_tag;
    if (text.match(/(bayar gagal|m-banking|masalah bayar)/)) return studioInfo.pembayaran_gagal;
    if (text.match(/(waiting list|daftar tunggu|kalo ada yang batal)/)) return studioInfo.waiting_list;
    if (text.match(/(edukasi|rawat di rumah|cara jaga)/)) return studioInfo.kesehatan_kuku;
    if (text.match(/(bawa anak|anak ikut|anak kecil)/)) return studioInfo.bawa_anak;
    if (text.match(/(lampu terang|daylight|cahaya)/)) return studioInfo.pencahayaan;
    if (text.match(/(kikir manual|manual kikir|tradisional)/)) return studioInfo.kikir_manual;
    if (text.match(/(debu|sedot|vacuum|bersih)/)) return studioInfo.bebas_debu;
    if (text.match(/(terima kasih|thanks|tq|makasih|oke|siap|baik|sip|nuhun|suwun)/)) return "Sama-sama Kak! 🥰 Kabari Asisten Neydream ya kalau mau booking!";
    if (text.match(/(api|fire|flame)/)) return studioInfo.kuku_api;
    if (text.match(/(chrome|mirror|silver|emas)/)) return studioInfo.chrome_nails;
    if (text.match(/(cat eye|mata kucing|magnet)/)) return studioInfo.mata_kucing;
    if (text.match(/(marmer|marble|batu)/)) return studioInfo.kuku_marmer;
    if (text.match(/(french|v shape|double french)/)) return studioInfo.french_modern;
    if (text.match(/(bening|jelly|syrup|transparan)/)) return studioInfo.kuku_bening;
    if (text.match(/(kulit gelap|sawo matang|eksotis)/)) return studioInfo.rekomendasi_kulit_gelap;
    if (text.match(/(kulit putih|cerah)/)) return studioInfo.rekomendasi_kulit_putih;
    if (text.match(/(kulit langsat|kuning langsat)/)) return studioInfo.rekomendasi_kulit_langsat;
    if (text.match(/(tipis|lemah|kuku rusak)/)) return studioInfo.kuku_tipis;
    if (text.match(/(gelombang|tidak rata|overlay)/)) return studioInfo.kuku_bergelombang;
    if (text.match(/(lebar|bantet|jempol gede)/)) return studioInfo.kuku_lebar;
    if (text.match(/(lem|tempel|pemasangan)/)) return studioInfo.lem_kuku;
    if (text.match(/(matte|doff|beludru)/)) return studioInfo.topcoat_matte;
    if (text.match(/(palsu|fake nails|tempelan)/)) return studioInfo.kuku_panjang_palsu;
    if (text.match(/(timbul|3d|kerang|sweater)/)) return studioInfo.nail_art_timbul;
    if (text.match(/(glitter|sparkle|kerlip)/)) return studioInfo.glitter_gradasi;
    if (text.match(/(sticker|stiker|tempel gambar)/)) return studioInfo.sticker_kuku;
    if (text.match(/(tips sehat|jaga kuku)/)) return studioInfo.kuku_sehat;
    if (text.match(/(kutikula|bersih kuku|russian)/)) return studioInfo.pembersihan_kutikula;
    if (text.match(/(alergi|gatal|sensitif)/)) return studioInfo.alergi;
    if (text.match(/(bau|pusing|menyengat)/)) return studioInfo.bau_kimia;
    if (text.match(/(kuku kaki|pedi gel)/)) return studioInfo.kuku_kaki_gel;
    if (text.match(/(spa|scrub|masker)/)) return studioInfo.pedicure_spa;
    if (text.match(/(parafin|paraffin|tangan kering)/)) return studioInfo.parafin;
    if (text.match(/(foil|metalik|serpihan)/)) return studioInfo.nail_foil;
    if (text.match(/(patah daging|tambal)/)) return studioInfo.kuku_patah_tengah;
    if (text.match(/(bingung|tanya warna|saran)/)) return studioInfo.konsultasi_gratis;
    if (text.match(/(merk korea|premium gel)/)) return studioInfo.koleksi_korea;
    if (text.match(/(glamour nails|nama dulu)/)) return studioInfo.rebranding_info;
    if (text.match(/(asuransi|jaminan|pasang lagi)/)) return studioInfo.asuransi_3_hari;
    if (text.match(/(steril|higienis|bersih)/)) return studioInfo.alat_steril_uv;
    if (text.match(/(bahan kimia|formalin|bahaya)/)) return studioInfo.bebas_formalin;
    if (text.match(/(bawa minum|kopi|boba)/)) return studioInfo.bawa_minuman;
    if (text.match(/(buku tamu|poin|reward)/)) return studioInfo.buku_tamu;
    if (text.match(/(coffin|peti mati)/)) return studioInfo.coffin_shape;
    if (text.match(/(almond|feminin)/)) return studioInfo.almond_shape;
    if (text.match(/(stiletto|runcing)/)) return studioInfo.stiletto_shape;
    if (text.match(/(square|kotak)/)) return studioInfo.square_shape;
    if (text.match(/(press on|kuku palsu tempel)/)) return studioInfo.custom_press_on;
    if (text.match(/(ultah|ulang tahun|birthday)/)) return studioInfo.diskon_ulang_tahun;
    if (text.match(/(pagi|early bird)/)) return studioInfo.diskon_pagi;
    if (text.match(/(edukasi|info gel)/)) return studioInfo.edukasi_gel;
    if (text.match(/(bunglon|chameleon|berubah warna)/)) return studioInfo.efek_bunglon;
    if (text.match(/(foto|kamera|difotoin)/)) return studioInfo.foto_estetik;
    if (text.match(/(soak off|hapus halus)/)) return studioInfo.gel_removal_safe;
    if (text.match(/(swarovski|berlian asli)/)) return studioInfo.hiasan_swarovski;
    if (text.match(/(clay|karakter|boneka)/)) return studioInfo.hiasan_clay;
    if (text.match(/(hamil|bumil|busui)/)) return studioInfo.ibu_hamil_aman;
    if (text.match(/(sibuk|ramai|sepi)/)) return studioInfo.jam_sibuk;
    if (text.match(/(gigit kuku|kebiasaan buruk)/)) return studioInfo.kuku_gigit;
    if (text.match(/(drill|bor|kikir listrik)/)) return studioInfo.kikir_elektrik;
    if (text.match(/(glitter|kerlip|bling)/)) return studioInfo.koleksi_glitter;
    if (text.match(/(lampu|uv led|kering)/)) return studioInfo.lampu_uv_led;
    if (text.match(/(maintenance|rawat|fill in)/)) return studioInfo.maintenance_kuku;
    if (text.match(/(pria|cowok|laki)/)) return studioInfo.manicuring_pria;
    if (text.match(/(wangi|aroma|bau)/)) return studioInfo.minyak_kutikula;
    if (text.match(/(batal|ga datang|no show)/)) return studioInfo.no_show;
    if (text.match(/(airbrush|semprot|halus)/)) return studioInfo.ombre_airbrush;
    if (text.match(/(bridesmaid|nikahan teman|rombongan)/)) return studioInfo.paket_bridesmaid;
    if (text.match(/(wheels|pilih warna|sampel)/)) return studioInfo.pilih_warna_dulu;
    if (text.match(/(lokal|bpom)/)) return studioInfo.produk_lokal;
    if (text.match(/(kotor|kuman|deep clean)/)) return studioInfo.quarantine_nails;
    if (text.match(/(nude|natural|kantor)/)) return studioInfo.rekomendasi_nude;
    if (text.match(/(ganti jadwal|pindah hari)/)) return studioInfo.reschedule_policy;
    if (text.match(/(stempel|stamping|pola)/)) return studioInfo.stempel_kuku;
    if (text.match(/(glossy|kilap|kaca)/)) return studioInfo.top_coat_glossy;
    if (text.match(/(kalsium|vitamin cair|kuat)/)) return studioInfo.treatment_kalsium;
    if (text.match(/(google maps|ulasan|bintang)/)) return studioInfo.ulasan_google;
    if (text.match(/(vitamin botol|beli vitamin)/)) return studioInfo.vitamin_kuku_oles;
    if (text.match(/(neon|glow|terang)/)) return studioInfo.warna_neon;
    if (text.match(/(pastel|lembut|macaroon)/)) return studioInfo.warna_pastel;
    if (text.match(/(fast res|admin aktif)/)) return studioInfo.wa_fast_res;
    if (text.match(/(wifi|internet|password)/)) return studioInfo.wifi_password;
    if (text.match(/(cuci tangan|sabun|wastafel)/)) return studioInfo.wastafel_bersih;
    if (text.match(/(resi|bukti bayar|screenshot)/)) return studioInfo.xerox_copy_resi;
    if (text.match(/(musik|lagu|yoga)/)) return studioInfo.yoga_music;
    if (text.match(/(zodiak|bintang|horoskop)/)) return studioInfo.zodiac_nails;
    if (text.match(/(nyaman|sejuk|adem)/)) return studioInfo.zona_nyaman;
    if (text.match(/(debu|vacuum|sedot)/)) return studioInfo.bebas_debu;
    if (text.match(/(apa kabar|gimana kabar|how are you|how’s it going)/)) return studioInfo.kabar_baik;
    if (text.match(/(lagi apa|sedang apa|buat apa)/)) return studioInfo.sedang_apa;
    if (text.match(/(sudah makan|dah makan|makan apa)/)) return studioInfo.sudah_makan;
    if (text.match(/(sibuk gak|lagi repot)/)) return studioInfo.lagi_sibuk;
    if (text.match(/(pintar banget|cerdas|hebat kamu)/)) return studioInfo.kamu_pintar;
    if (text.match(/(lucu|ngakak|kocak)/)) return studioInfo.kamu_lucu;
    if (text.match(/(rumah|tinggal dimana|lokasi ai)/)) return studioInfo.rumah_dimana;
    if (text.match(/(hobi|suka apa)/)) return studioInfo.hobi_kamu;
    if (text.match(/(cantik banget|bagus banget|indah)/)) return studioInfo.cantik_banget;
    if (text.match(/(semangat|spirit)/)) return studioInfo.semangat;
    if (text.match(/(selamat pagi|pagi min|pagi admin)/)) return studioInfo.pagi_salam;
    if (text.match(/(selamat siang|siang min)/)) return studioInfo.siang_salam;
    if (text.match(/(selamat sore|sore min)/)) return studioInfo.sore_salam;
    if (text.match(/(selamat malam|malam min)/)) return studioInfo.malam_salam;
    if (text.match(/(weekend|akhir pekan|sabtu minggu)/)) return studioInfo.weekend_plan;
    if (text.match(/(bosan|boring|jenuh)/)) return studioInfo.bosen;
    if (text.match(/(capek|lelah|penat)/)) return studioInfo.capek;
    if (text.match(/(keren|mantap|gokil)/)) return studioInfo.keren;
    if (text.match(/(sedih|galau|nangis)/)) return studioInfo.lagi_sedih;
    if (text.match(/(senang|bahagia|happy)/)) return studioInfo.lagi_senang;
    if (text.match(/(kangen|rindu)/)) return studioInfo.kangen;
    if (text.match(/(mager|malas gerak)/)) return studioInfo.mager;
    if (text.match(/(hujan|mendung|gerimis)/)) return studioInfo.hujan_nih;
    if (text.match(/(panas|gerah|terik)/)) return studioInfo.panas_banget;
    if (text.match(/(bestie|teman|sahabat)/)) return studioInfo.bestie;
    if (text.match(/(rahasia|spill)/)) return studioInfo.rahasia;
    if (text.match(/(saran desain|bagus yang mana)/)) return studioInfo.saran_desain;
    if (text.match(/(lagu|musik|nyanyi)/)) return studioInfo.lagu_Short;
    if (text.match(/(film|nonton)/)) return studioInfo.film_bagus;
    if (text.match(/(tanggal merah|libur)/)) return studioInfo.tgl_merah;
    if (text.match(/(pilihkan|pilihin)/)) return studioInfo.pilihkan_dong;
    if (text.match(/(seru|asik)/)) return studioInfo.seru_ya;
    if (text.match(/(masak|makanan)/)) return studioInfo.pinter_masak;
    if (text.match(/(olahraga|gym|sehat)/)) return studioInfo.olahraga;
    if (text.match(/(self love|manjakan diri)/)) return studioInfo.self_love;
    if (text.match(/(glow up|berubah)/)) return studioInfo.glow_up;
    if (text.match(/(ngopi|kopi|coffee)/)) return studioInfo.kopi;
    if (text.match(/(ngeteh|teh|tea)/)) return studioInfo.teh;
    if (text.match(/(healing|jalan jalan)/)) return studioInfo.healing;
    if (text.match(/(kantor|kerja|work)/)) return studioInfo.kantor_vibes;
    if (text.match(/(pesta|party|kondangan)/)) return studioInfo.pesta_vibes;
    if (text.match(/(curhat|cerita dikit)/)) return studioInfo.curhat_dikit;
    if (text.match(/(lama banget|telat)/)) return studioInfo.telat_balas;
    if (text.match(/(canda|joke|receh)/)) return studioInfo.canda_receh;
    if (text.match(/(pantun)/)) return studioInfo.pantun_kuku;
    if (text.match(/(ok|sip|siap|baiklah)/)) return studioInfo.bye_basa_basi;
    if (text.match(/(slot kosong|cek slot|jadwal tersedia)/)) return studioInfo.slot_kosong;
    if (text.match(/(pilih terapis|request mbaknya|mbak favorit)/)) return studioInfo.pilih_terapis;
    if (text.match(/(dadakan|hari ini|sameday)/)) return studioInfo.booking_dadakan;
    if (text.match(/(dp|uang muka|tanda jadi)/)) return studioInfo.dp_booking;
    if (text.match(/(lunas|bayar sisa|pelunasan)/)) return studioInfo.pelunasan;
    if (text.match(/(konfirmasi|sudah bayar|verifikasi)/)) return studioInfo.konfirmasi_otomatis;
    if (text.match(/(salah jam|ubah jam|salah waktu)/)) return studioInfo.salah_jam;
    if (text.match(/(telat|terlambat|kalo telat)/)) return studioInfo.durasi_telat;
    if (text.match(/(hangus|batal dp|uang kembali)/)) return studioInfo.cancel_dp_hangus;
    if (text.match(/(bukti blur|foto bukti|kirim resi)/)) return studioInfo.bukti_transfer_blur;
    if (text.match(/(dua orang|bareng teman|berdua)/)) return studioInfo.booking_dua_orang;
    if (text.match(/(eror|error|form rusak|gak bisa klik)/)) return studioInfo.link_booking_error;
    if (text.match(/(invoice|nomor booking|pesanan mana)/)) return studioInfo.invoice_hilang;
    if (text.match(/(kupon|voucher|kode promo)/)) return studioInfo.kupon_promo;
    if (text.match(/(refund|kembali uang)/)) return studioInfo.refund_dana;
    if (text.match(/(notes|catatan|pesan khusus)/)) return studioInfo.titip_pesan;
    if (text.match(/(edc|gesek|kartu debit)/)) return studioInfo.pembayaran_edc;
    if (text.match(/(datang awal|jam berapa datang)/)) return studioInfo.datang_awal;
    if (text.match(/(siap|oke|paham|mengerti)/)) return studioInfo.siap_kak;
    if (text.match(/(wisuda|kelulusan|ijazah)/)) return studioInfo.ref_wisuda;
    if (text.match(/(lebaran|idul fitri|fitri)/)) return studioInfo.ref_lebaran;
    if (text.match(/(natal|christmas|snowflake)/)) return studioInfo.ref_natal;
    if (text.match(/(imlek|chinese new year|naga)/)) return studioInfo.ref_imlek;
    if (text.match(/(pantai|liburan|holiday|vacation)/)) return studioInfo.ref_liburan;
    if (text.match(/(konser|party|glow in the dark)/)) return studioInfo.ref_konser;
    if (text.match(/(kantor|kerja|professional|formal)/)) return studioInfo.ref_kantor;
    if (text.match(/(kencan|date|date night|romantis)/)) return studioInfo.ref_kencan;
    if (text.match(/(pesta|kondangan|mewah)/)) return studioInfo.ref_pesta;
    if (text.match(/(minimalis|simpel|simple|rapi)/)) return studioInfo.ref_minimalis;
    if (text.match(/(vintage|jadul|retro)/)) return studioInfo.ref_vintage;
    if (text.match(/(kpop|korea|y2k)/)) return studioInfo.ref_kpop;
    if (text.match(/(gothic|gelap|hitam merah)/)) return studioInfo.ref_gothic;
    if (text.match(/(coquette|pita|bow)/)) return studioInfo.ref_coquette;
    if (text.match(/(zodiak|ramalan|bintang)/)) return studioInfo.ref_zodiac;
    if (text.match(/(aura|energi|gradient)/)) return studioInfo.ref_aura;
    if (text.match(/(halus|milky|sehat)/)) return studioInfo.ref_halus;
    if (text.match(/(glamour|kristal|berlian)/)) return studioInfo.ref_glamour;
    if (text.match(/(ballet|satin|pink lembut)/)) return studioInfo.ref_ballet;
    if (text.match(/(galaxy|luar angkasa|bintang)/)) return studioInfo.ref_galaxy;
    if (text.match(/(animal|macan|zebra|sapi)/)) return studioInfo.ref_animal;
    if (text.match(/(buah|fruit|segar)/)) return studioInfo.ref_fruit;
    if (text.match(/(marmer|marble gold)/)) return studioInfo.ref_marble_gold;
    if (text.match(/(ocean|air|laut)/)) return studioInfo.ref_ocean;
    if (text.match(/(astronomi|bulan bintang)/)) return studioInfo.ref_astronomi;
    if (text.match(/(boho|etnik|mandala)/)) return studioInfo.ref_boho;
    if (text.match(/(kawaii|lucu banget|boneka)/)) return studioInfo.ref_kawaii;
    if (text.match(/(indie|campur campur)/)) return studioInfo.ref_indie;
    if (text.match(/(royal blue|biru mewah)/)) return studioInfo.ref_royal;
    if (text.match(/(nude gradient|coffee)/)) return studioInfo.ref_nude_gradient;
    if (text.match(/(sunset|senja)/)) return studioInfo.ref_ombre_sunset;
    if (text.match(/(cyber|futuristik|metal)/)) return studioInfo.ref_cyber;
    if (text.match(/(classic red|merah polos)/)) return studioInfo.ref_classic;
    if (text.match(/(abstract|seni|corat coret)/)) return studioInfo.ref_abstract;
    if (text.match(/(butterfly|kupu)/)) return studioInfo.ref_butterfly;
    if (text.match(/(pearl|mutiara)/)) return studioInfo.ref_pearl;
    if (text.match(/(checkered|kotak)/)) return studioInfo.ref_checkered;
    if (text.match(/(heart|hati|love)/)) return studioInfo.ref_heart;
    if (text.match(/(velvet|beludru)/)) return studioInfo.ref_velvet;
    if (text.match(/(geometris|garis)/)) return studioInfo.ref_geometris;
    if (text.match(/(bridal|nikah|pengantin)/)) return studioInfo.ref_bridal;
    if (text.match(/(winter|dingin|es)/)) return studioInfo.ref_winter;
    if (text.match(/(autumn|gugur|merah bata)/)) return studioInfo.ref_autumn;
    if (text.match(/(spring|bunga|sakura)/)) return studioInfo.ref_spring;
    if (text.match(/(disco|bola disko)/)) return studioInfo.ref_disco;
    if (text.match(/(terrazzo|keramik)/)) return studioInfo.ref_terrazzo;
    if (text.match(/(unicorn|pelangi)/)) return studioInfo.ref_unicorn;
    if (text.match(/(gemstone|batu alam|jade)/)) return studioInfo.ref_gemstone;
    if (text.match(/(matte gold|emas)/)) return studioInfo.ref_matte_gold;
    if (text.match(/(rekomendasi|bagusnya apa|pilihan)/)) return "Kalau untuk acara spesial, Kakak bisa coba gaya 'Aura Nails' atau 'Cat Eye'. Tapi kalau untuk sehari-hari, 'Micro-French' atau 'Milky White' selalu jadi favorit!";
    if (text.match(/(awet|tahan lama|tips)/)) return studioInfo.tips_awet;
    if (text.match(/(mahal|biaya tinggi|pricey)/)) return studioInfo.kenapa_mahal;
    if (text.match(/(jamur|kuku hijau|infeksi)/)) return studioInfo.kuku_jamur_cegah;
    if (text.match(/(sakit|panas|lampu uv)/)) return studioInfo.sakit_saat_lampu;
    if (text.match(/(komplain|hasil buruk|tidak puas)/)) return studioInfo.komplain_hasil;
    if (text.match(/(hujan|cuaca dingin)/)) return studioInfo.warna_musim_hujan;
    if (text.match(/(panas|kemarau|gerah)/)) return studioInfo.warna_musim_panas;
    if (text.match(/(halal|sholat|wudhu|inglot)/)) return studioInfo.produk_halal_info;
    if (text.match(/(kuku pecah|retak|silk wrap)/)) return studioInfo.kuku_pecah_samping;
    if (text.match(/(beda gel|kutek biasa)/)) return studioInfo.bedanya_gel_biasa;
    if (text.match(/(kopek|lepas sendiri|bongkar)/)) return studioInfo.lepas_gel_sendiri;
    if (text.match(/(setelah removal|rawat kuku)/)) return studioInfo.setelah_removal;
    if (text.match(/(anak kecil|aman buat anak)/)) return studioInfo.kuku_anak_aman;
    if (text.match(/(cepat|buru buru|express)/)) return studioInfo.pengerjaan_cepat;
    if (text.match(/(meja penuh|antri|nunggu)/)) return studioInfo.meja_penuh;
    if (text.match(/(sedih|nangis|galau)/)) return studioInfo.mood_sedih;
    if (text.match(/(ramah|mbaknya baik|obrol)/)) return studioInfo.terapis_ramah;
    if (text.match(/(lampu mati|mati lampu|listrik)/)) return studioInfo.lampu_mati;
    if (text.match(/(cantengan|bengkak|nanah)/)) return studioInfo.kuku_kaki_cantengan;
    if (text.match(/(pajak|nett|biaya admin)/)) return studioInfo.pajak_layanan;
    if (text.match(/(belajar|kursus|workshop)/)) return studioInfo.ingin_belajar;
    if (text.match(/(endorse|kerjasama|collab)/)) return studioInfo.endorse_syarat;
    if (text.match(/(tumpah|minum)/)) return studioInfo.minum_tumpah;
    if (text.match(/(pijat|getar|kursi)/)) return studioInfo.kursi_pijat_info;
    if (text.match(/(asli panjang|hard gel|overlay)/)) return studioInfo.kuku_panjang_alami;
    if (text.match(/(tidak cocok|ganti warna)/)) return studioInfo.warna_tidak_cocok;
    if (text.match(/(voucher kado|hadiah)/)) return studioInfo.kado_voucher;
    if (text.match(/(hand cream|kulit kering)/)) return studioInfo.perawatan_dirumah;
    if (text.match(/(foto studio|ootd)/)) return studioInfo.suasana_studio;
    if (text.match(/(pria|cowok|laki)/)) return studioInfo.pria_treatment;
    if (text.match(/(kusam|buram|alkohol)/)) return studioInfo.topcoat_berubah;
    if (text.match(/(mama|ibu|bonding)/)) return studioInfo.ajak_mama;
    if (text.match(/(istirahat|tidur|malam)/)) return studioInfo.pamit_tidur;
    if (text.match(/(sayang|bye|terima kasih)/)) return studioInfo.pesan_terakhir;
    if (text.match(/(kecewa|nyesel|gak mau lagi|kapok|marah)/)) return studioInfo.marah_layanan;
    if (text.match(/(mahal banget|kemahalan|rampok|mencekik)/)) return studioInfo.marah_harga;
    if (text.match(/(lama|lelet|lambat|nunggu mulu)/)) return studioInfo.marah_telat;
    if (text.match(/(jelek banget|ancur|rusak kuku)/)) return studioInfo.marah_hasil;
    if (text.match(/(bingung|pusing|ribet|susah)/)) return studioInfo.marah_booking;
    if (text.match(/(maaf|sori|maap)/)) return "Sama-sama Kak, aku juga minta maaf ya kalau ada salah kata. Mari kita mulai lagi dengan baik. 😊";
    if (text.match(/(sabar|tunggu)/)) return studioInfo.sabar;
    if (text.match(/(admin|manusia|orang)/)) return studioInfo.admin_siap;
    if (text.match(/(sopan|bahasa)/)) return studioInfo.hargai_kami;
    if (text.match(/(dry manicure|tanpa air)/)) return studioInfo.dry_manicure_info;
    if (text.match(/(tipis|kikir terlalu banyak)/)) return studioInfo.over_filing_cegah;
    if (text.match(/(peel off|lepas sendiri)/)) return studioInfo.gel_peel_off;
    if (text.match(/(kuning|detox kuku)/)) return studioInfo.kuku_kuning;
    if (text.match(/(bintik putih|putih putih)/)) return studioInfo.bintik_putih;
    if (text.match(/(letoy|lentur|lembek)/)) return studioInfo.kuku_lentur;
    if (text.match(/(pantai|berenang|air laut)/)) return studioInfo.perawatan_liburan;
    if (text.match(/(sunscreen|tabir surya)/)) return studioInfo.sunscreen_nails;
    if (text.match(/(cepat panjang|celah kuku)/)) return studioInfo.kuku_tumbuh_cepat;
    if (text.match(/(spring|semi|bunga kering)/)) return studioInfo.warna_musim_semi;
    if (text.match(/(autumn|gugur)/)) return studioInfo.warna_musim_gugur;
    if (text.match(/(warna french|ujung warna)/)) return studioInfo.french_warna;
    if (text.match(/(holographic|pelangi chrome)/)) return studioInfo.chrome_pelangi;
    if (text.match(/(miring|tidak simetris)/)) return studioInfo.kuku_asimetris;
    if (text.match(/(piercing|perhiasan kuku)/)) return studioInfo.nail_jewelry;
    if (text.match(/(cuci piring|sabun keras)/)) return studioInfo.tips_cuci_piring;
    if (text.match(/(terjepit|memar|biru)/)) return studioInfo.kuku_terjepit;
    if (text.match(/(kuku pendek|minimalist dot)/)) return studioInfo.kuku_pendek_estetik;
    if (text.match(/(parfum kuku|wangi)/)) return studioInfo.parfum_kuku;
    if (text.match(/(kulit pucat|cerah banget)/)) return studioInfo.warna_kulit_pucat;
    if (text.match(/(kulit tan|eksotis|hitam manis)/)) return studioInfo.warna_kulit_tan;
    if (text.match(/(pasir|sand gel)/)) return studioInfo.tekstur_pasir;
    if (text.match(/(tumit|pecah pecah|kaki kasar)/)) return studioInfo.kuku_kaki_kering;
    if (text.match(/(ballerina|coffin almond)/)) return studioInfo.kuku_panjang_balet;
    if (text.match(/(bening saja|sehat alami)/)) return studioInfo.gel_bening_saja;
    if (text.match(/(sobek|pinggir kuku)/)) return studioInfo.kuku_mudah_sobek;
    if (text.match(/(sweater|tekstur baju)/)) return studioInfo.tekstur_sweater;
    if (text.match(/(lamaran|tunangan|engagement)/)) return studioInfo.kuku_untuk_lamaran;
    if (text.match(/(olahraga|gym|angkat beban)/)) return studioInfo.kuku_olahraga;
    if (text.match(/(satu jari|patah satu)/)) return studioInfo.perbaikan_satu_jari;
    if (text.match(/(asap|smoke)/)) return studioInfo.efek_asap;
    if (text.match(/(monokrom|hitam putih)/)) return studioInfo.warna_monokrom;
    if (text.match(/(sensitif panas|perih lampu)/)) return studioInfo.kuku_sensitif_panas;
    if (text.match(/(dehidrasi|pecah seribu)/)) return studioInfo.kuku_pecah_seribu;
    if (text.match(/(gelembung|bubble)/)) return studioInfo.kuku_bebas_gelembung;
    if (text.match(/(merah|keberanian|percaya diri)/)) return studioInfo.psikologi_merah;
    if (text.match(/(biru|tenang|rileks)/)) return studioInfo.psikologi_biru;
    if (text.match(/(hijau|segar|ramah)/)) return studioInfo.psikologi_hijau;
    if (text.match(/(kuning|ceria|senang)/)) return studioInfo.psikologi_kuning;
    if (text.match(/(ungu|mewah|kreatif)/)) return studioInfo.psikologi_ungu;
    if (text.match(/(telpon|berisik|suara)/)) return studioInfo.etika_telpon;
    if (text.match(/(bawa anak|anak kecil)/)) return studioInfo.etika_anak;
    if (text.match(/(makan|ngemil)/)) return studioInfo.etika_makanan;
    if (text.match(/(napas|nafas|pori)/)) return studioInfo.mitos_napas;
    if (text.match(/(hitam|gosong|kulit gelap)/)) return studioInfo.teknis_uv_gosong;
    if (text.match(/(cabut sendiri|tarik kuku)/)) return studioInfo.cabut_kuku_palsu;
    if (text.match(/(melengkung|clubbing)/)) return studioInfo.kuku_clubbing;
    if (text.match(/(cekung|sendok)/)) return studioInfo.kuku_sendok;
    if (text.match(/(detox|rusak parah|rescue)/)) return studioInfo.kerusakan_parah;
    if (text.match(/(foto bagus|tips foto)/)) return studioInfo.pencahayaan_foto;
    if (text.match(/(interview|kerja|lamar)/)) return studioInfo.interview_kerja;
    if (text.match(/(gym|olahraga|atlet)/)) return studioInfo.gym_nails;
    if (text.match(/(remaja|abg)/)) return studioInfo.rekomendasi_remaja;
    if (text.match(/(dewasa|tua|muda)/)) return studioInfo.rekomendasi_dewasa;
    if (text.match(/(vegan|hewan|cruelty)/)) return studioInfo.produk_vegan;
    if (text.match(/(non toxic|aman bahan)/)) return studioInfo.bahan_non_toxic;
    if (text.match(/(wifi|internet)/)) return studioInfo.wifi_kencang;
    if (text.match(/(cas|charger|baterai)/)) return studioInfo.charger_hp;
    if (text.match(/(minum|teh|snack|makan gratis)/)) return studioInfo.teh_hangat;
    if (text.match(/(ulasan|review|google maps)/)) return studioInfo.ulasan_foto;
    if (text.match(/(poin|member|membership)/)) return studioInfo.membership_poin;
    if (text.match(/(ajak teman|referral)/)) return studioInfo.ajak_teman_diskon;
    if (text.match(/(jam sepi|jam rame)/)) return studioInfo.jam_paling_sepi;
    if (text.match(/(garansi warna|salah warna)/)) return studioInfo.garansi_warna;
    if (text.match(/(komitmen|janji)/)) return studioInfo.komitmen_neydream;
    else {
        return "Maaf Kak, aku masih tahap belajar.Gunakan kata kunci untuk mengirim pesan, Coba tanya hal lain seperti 'Harga', 'Lokasi', 'Katalog' atau tanya 'halo' 😅'.";
        
    }
}

// --- 6. FUNGSI PEMBANTU (TAMPILKAN PESAN) ---
function addMessageToBox(sender, text) {
    const chatBox = document.getElementById('chatBox');
    const msgDiv = document.createElement('div');
    msgDiv.className = `message ${sender}`;
    msgDiv.textContent = text;
    chatBox.appendChild(msgDiv);
}

// --- 7. DETEKSI TOMBOL ENTER ---
function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}