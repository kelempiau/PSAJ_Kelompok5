
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-content">
        <div class="loading-star">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>
        <div class="loading-text">
            Loading<span class="loading-dots">...</span>
        </div>
    </div>
</div>

<?php 
if(isset($_SESSION['user_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    $cp_sql = isset($conn) ? $conn : (isset($GLOBALS['conn']) ? $GLOBALS['conn'] : null);
    if($cp_sql) {
        $check_ph = $cp_sql->query("SELECT phone FROM users WHERE id = ".$_SESSION['user_id']);
        if($check_ph && $check_ph->num_rows > 0) {
            $cp_data = $check_ph->fetch_assoc();
            if(empty($cp_data['phone'])) {
                $needs_phone = true;
            }
        }
    }
}
?>

<?php if(isset($needs_phone) && $needs_phone): ?>
<!-- Premium Phone Requirement Modal -->
<div id="googlePhoneModal" style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(12px); z-index: 6000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; padding: 50px 40px; border-radius: 40px; text-align: center; max-width: 480px; width: 100%; box-shadow: 0 40px 100px rgba(234, 54, 113, 0.25); animation: premiumPopupIn 0.6s cubic-bezier(0.19, 1, 0.22, 1); position: relative; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.5);">
        
        <!-- Decorative Glows -->
        <div style="position: absolute; top: -100px; left: -100px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(234, 54, 113, 0.15) 0%, transparent 70%); z-index: 0;"></div>
        <div style="position: absolute; bottom: -100px; right: -100px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(234, 54, 113, 0.1) 0%, transparent 70%); z-index: 0;"></div>
        
        <div style="position: relative; z-index: 1;">
            <!-- Icon with Float Animation -->
            <div class="premium-icon-float" style="width: 100px; height: 100px; background: linear-gradient(135deg, #ea3671, #be123c); border-radius: 30px; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; box-shadow: 0 20px 40px rgba(234, 54, 113, 0.4); transform: rotate(-5deg);">
                <span style="font-size: 3rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));">📱</span>
            </div>
            
            <h2 style="color: #1e293b; margin-bottom: 15px; font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 2rem; letter-spacing: -0.5px; line-height: 1.2;">Halo Kak Cantik! ✨</h2>
            <p style="color: #64748b; line-height: 1.7; margin-bottom: 35px; font-family: 'Poppins', sans-serif; font-size: 1rem; font-weight: 500;">
                Terima kasih sudah bergabung! Agar Ney bisa kirim info booking lewat <b>WhatsApp</b>, yuk lengkapi nomor handphone Kakak sekarang.
            </p>
            
            <div style="margin-bottom: 30px; text-align: left;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #ea3671; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Nomor WhatsApp</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #94a3b8; font-size: 1.1rem;">+62</span>
                    <input type="tel" id="googlePhoneInput" placeholder="812xxxxx" style="width: 100%; padding: 18px 20px 18px 60px; border: 2.5px solid #f1f5f9; border-radius: 20px; font-family: 'Poppins', sans-serif; font-size: 1.1rem; outline: none; transition: 0.4s; background: #f8fafc; color: #1e293b; font-weight: 600;" required>
                </div>
                <p id="googlePhoneError" style="color: #ef4444; font-size: 0.85rem; margin-top: 10px; display: none; font-weight: 600; padding: 0 5px;">⚠️ Masukkan nomor WhatsApp yang aktif ya Kak.</p>
            </div>
            
            <button onclick="submitGooglePhone()" id="btnSubmitPhone" style="width: 100%; padding: 20px; background: linear-gradient(135deg, #ea3671, #be123c); color: white; border: none; border-radius: 20px; font-size: 1.1rem; font-weight: 800; font-family: 'Poppins', sans-serif; cursor: pointer; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 15px 35px rgba(234, 54, 113, 0.35); position: relative; overflow: hidden; letter-spacing: 1px;">
                SIMPAN & MULAI CANTIK ✨
            </button>
            
            <p style="margin-top: 20px; font-size: 0.8rem; color: #94a3b8; font-weight: 500;">Privasi Kakak aman bersama Neydream Studio 💖</p>
        </div>
    </div>
</div>

<style>
    @keyframes premiumPopupIn {
        0% { opacity: 0; transform: scale(0.8) translateY(40px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .premium-icon-float {
        animation: premiumFloat 3s ease-in-out infinite;
    }
    @keyframes premiumFloat {
        0%, 100% { transform: translateY(0) rotate(-5deg); }
        50% { transform: translateY(-15px) rotate(5deg); }
    }
    #googlePhoneInput:focus {
        border-color: #ea3671;
        background: white;
        box-shadow: 0 10px 30px rgba(234, 54, 113, 0.1);
    }
    #btnSubmitPhone:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 45px rgba(234, 54, 113, 0.45);
    }
    #btnSubmitPhone:active {
        transform: translateY(0) scale(0.98);
    }
</style>

<script>
    function submitGooglePhone() {
        let phone = document.getElementById('googlePhoneInput').value.trim();
        const err = document.getElementById('googlePhoneError');
        const btn = document.getElementById('btnSubmitPhone');
        
        // Sanitize phone number (remove leading 0 if starting with 62 or 0)
        if(phone.startsWith('0')) phone = phone.substring(1);
        if(phone.startsWith('62')) phone = phone.substring(2);
        
        if(phone.length < 8) {
            err.style.display = 'block';
            err.classList.add('animate-shake');
            return;
        }
        err.style.display = 'none';
        
        btn.disabled = true;
        btn.innerHTML = '<span class="loading-spinner"></span> Sedang Menyimpan...';
        btn.style.opacity = '0.8';
        
        let path = window.location.pathname.includes('/user/') ? '../' : './';
        if(window.location.pathname.includes('/admin/')) path = '../';
        
        const fullPhone = '62' + phone;
        const fd = new FormData();
        fd.append('phone', fullPhone);
        
        fetch(path + 'api/user/update_phone.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                btn.innerHTML = 'BERHASIL! ✨';
                btn.style.background = '#10b981';
                setTimeout(() => {
                    document.getElementById('googlePhoneModal').style.opacity = '0';
                    setTimeout(() => window.location.reload(), 300);
                }, 1000);
            } else {
                alert("Waduh, gagal simpan nih Kak. Coba cek koneksi ya!");
                btn.disabled = false;
                btn.innerHTML = 'COBA LAGI';
            }
        }).catch(e => {
            console.error(e);
            btn.disabled = false;
            btn.innerHTML = 'COBA LAGI';
        });
    }
</script>
<?php endif; ?>
