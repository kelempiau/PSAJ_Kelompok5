<?php
require '../core/config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit();
}


$sql = "SELECT * FROM reservations 
        WHERE user_id = ? AND refund_status IS NOT NULL 
        ORDER BY refund_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Get Studio WhatsApp for buttons
$settingsRes = $conn->query("SELECT whatsapp FROM studio_settings WHERE id = 1");
$studio_wa = $settingsRes ? $settingsRes->fetch_assoc()['whatsapp'] : '628123456789';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Refund - Neydream</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include 'includes/loading_styles.php'; ?>
    <style>
        /* ANTIGRAVITY AD PROTECTION */
        #sb98124, #sb98124_image, #sb98124_close, .tutup2,
        div[id^="sb"][style*="display: block"], 
        div[id^="sb"][style*="position: fixed"],
        a[href*="infinityfree"] {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            z-index: -99999 !important;
        }
    </style>
    <script>
        (function(){
            setInterval(function(){
                var ads = document.querySelectorAll('#sb98124, #sb98124_image, .tutup2, div[id^="sb"][style*="fixed"]');
                ads.forEach(function(el){ el.remove(); });
            }, 500);
        })();
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffd9e2 0%, #ffe6f0 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #5f162e;
            font-size: 1.8rem;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back {
            background: #f0f0f0;
            color: #333;
        }

        .btn-back:hover {
            background: #e0e0e0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .pending .stat-number { color: #856404; }
        .approved .stat-number { color: #155724; }
        .rejected .stat-number { color: #721c24; }

        .empty-state {
            background: white;
            padding: 60px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .empty-state h2 {
            color: #5f162e;
            margin-bottom: 15px;
        }

        .refund-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .refund-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .refund-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .refund-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detail-item strong {
            color: #5f162e;
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .detail-item span {
            color: #666;
        }

        .reason-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .reason-box strong {
            color: #5f162e;
            display: block;
            margin-bottom: 8px;
        }

        .timeline {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .timeline-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            background: #ea3671;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-date {
            color: #999;
            font-size: 0.85rem;
        }

        .btn-refund-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .btn-form {
            background: #ea3671;
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(234, 54, 113, 0.3);
        }

        .btn-form:disabled {
            background: #ccc;
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-wa-alt {
            background: #25d366;
            color: white;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            padding: 35px;
            border-radius: 25px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            animation: modalScale 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes modalScale {
            from { transform: scale(0.85); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
            color: #5f162e;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            margin-bottom: 20px;
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        .form-input:focus {
            border-color: #ea3671;
            background: #fffafc;
        }

        /* Modern Premium SweetAlert Styling */
        .premium-swal-popup {
            border-radius: 24px !important;
            padding: 2rem !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1) !important;
        }
        .premium-swal-title {
            color: #5f162e !important;
            font-weight: 800 !important;
            font-size: 1.5rem !important;
        }
        .premium-swal-confirm {
            background: linear-gradient(135deg, #ea3671, #be123c) !important;
            color: white !important;
            padding: 12px 35px !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-family: 'Poppins', sans-serif !important;
            box-shadow: 0 4px 15px rgba(234, 54, 113, 0.3) !important;
            transition: 0.3s !important;
            border: none !important;
            outline: none !important;
        }
        .premium-swal-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(234, 54, 113, 0.4) !important;
        }

        /* Fix SweetAlert z-index behind modal */
        .swal2-container {
            z-index: 50000 !important;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 25px;
            cursor: pointer;
            user-select: none;
            position: relative;
            padding-left: 35px;
        }

        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: absolute;
            top: 2px;
            left: 0;
            height: 22px;
            width: 22px;
            background-color: #eee;
            border-radius: 6px;
            transition: 0.3s;
        }

        .checkbox-container:hover input ~ .checkmark {
            background-color: #ccc;
        }

        .checkbox-container input:checked ~ .checkmark {
            background-color: #ea3671;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }

        .checkbox-container .checkmark:after {
            left: 8px;
            top: 4px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2.5px 2.5px 0;
            transform: rotate(45deg);
        }

        .checkbox-container .label-text {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .stats {
                grid-template-columns: 1fr;
            }
            .btn-refund-group {
                flex-direction: column;
            }
            .btn-refund-group .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
    <script>
        function openRefundForm(bookingId) {
            document.getElementById('refund_booking_id').value = bookingId;
            document.getElementById('refundModal').style.display = 'flex';
        }

        function closeRefundModal() {
            document.getElementById('refundModal').style.display = 'none';
        }

        function toggleOtherMethod(val) {
            const otherDiv = document.getElementById('other_method_div');
            if (val === 'Lainnya') {
                otherDiv.style.display = 'block';
                document.getElementById('other_method').required = true;
            } else {
                otherDiv.style.display = 'none';
                document.getElementById('other_method').required = false;
            }
        }

        async function submitRefundForm() {
            const form = document.getElementById('refundDataForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const confirmBox = document.getElementById('confirmData');
            if (!confirmBox.checked) {
                Swal.fire({
                    title: 'Ops! ✨',
                    text: 'Silakan centang konfirmasi data terlebih dahulu ya Kak!',
                    icon: 'warning',
                    iconColor: '#ea3671',
                    showConfirmButton: true,
                    confirmButtonText: 'Oke, Mengerti',
                    customClass: {
                        popup: 'premium-swal-popup',
                        title: 'premium-swal-title',
                        confirmButton: 'premium-swal-confirm'
                    },
                    buttonsStyling: false
                });
                return;
            }

            const btn = document.getElementById('btnSubmitRefundData');
            btn.disabled = true;
            btn.innerHTML = 'Sedang Mengirim...';

            const formData = new FormData(form);

            try {
                const res = await fetch('../api/user/save_refund_data.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    closeRefundModal();
                    document.getElementById('successRefundModal').style.display = 'flex';
                } else {
                    alert("Gagal: " + (data.error || "Terjadi kesalahan"));
                    btn.disabled = false;
                    btn.innerHTML = 'Kirim Pengajuan Refund';
                }
            } catch (e) {
                console.error(e);
                btn.disabled = false;
                btn.innerHTML = 'Kirim Pengajuan Refund';
            }
        }
    </script>
</head>
<body>
    <?php include '../includes/loading.php'; ?>
    <div class="container">
        <div class="header">
            <h1>🔄 Status Refund</h1>
            <a href="history.php" class="btn btn-back">← Kembali</a>
        </div>

        <?php
        $pending = 0;
        $approved = 0;
        $rejected = 0;
        
        
        $temp_result = $result;
        mysqli_data_seek($result, 0); 
        while ($row = $result->fetch_assoc()) {
            if ($row['refund_status'] == 'pending') $pending++;
            elseif ($row['refund_status'] == 'approved') $approved++;
            elseif ($row['refund_status'] == 'rejected') $rejected++;
        }
        mysqli_data_seek($result, 0); 
        ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="stats">
                <div class="stat-card pending">
                    <div class="stat-number"><?= $pending ?></div>
                    <div class="stat-label">⏳ Pending</div>
                </div>
                <div class="stat-card approved">
                    <div class="stat-number"><?= $approved ?></div>
                    <div class="stat-label">✅ Disetujui</div>
                </div>
                <div class="stat-card rejected">
                    <div class="stat-number"><?= $rejected ?></div>
                    <div class="stat-label">❌ Ditolak</div>
                </div>
            </div>

            <?php while ($refund = $result->fetch_assoc()): ?>
                <div class="refund-card">
                    <div class="refund-header">
                        <div>
                            <strong>Booking #<?= $refund['id'] ?></strong>
                            <div style="color: #999; font-size: 0.9rem; margin-top: 5px;">
                                Request: <?= date('d M Y H:i', strtotime($refund['refund_date'])) ?>
                            </div>
                        </div>
                        <span class="status-badge status-<?= $refund['refund_status'] ?>">
                            <?= ucfirst($refund['refund_status']) ?>
                        </span>
                    </div>

                    <div class="refund-details">
                        <div class="detail-item">
                            <strong>📅 Tanggal Reservasi</strong>
                            <span><?= date('d F Y', strtotime($refund['reservation_date'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>🕐 Jam</strong>
                            <span><?= $refund['reservation_time'] ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>💅 Layanan</strong>
                            <span><?= $refund['service_type'] ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>💰 Total</strong>
                            <span>Rp <?= number_format($refund['total_price'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="reason-box">
                        <strong>Alasan Refund:</strong>
                        <p style="color: #666;"><?= htmlspecialchars($refund['refund_reason']) ?></p>
                    </div>

                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon">📝</div>
                            <div class="timeline-content">
                                <strong>Request Dibuat</strong>
                                <div class="timeline-date"><?= date('d M Y H:i', strtotime($refund['refund_date'])) ?></div>
                            </div>
                        </div>

                        <?php if ($refund['refund_status'] == 'pending'): ?>
                            <div class="timeline-item">
                                <div class="timeline-icon" style="background: #ffc107;">⏳</div>
                                <div class="timeline-content">
                                    <strong>Menunggu Review Admin</strong>
                                    <div class="timeline-date">Status akan diupdate dalam 1-2 hari kerja</div>
                                </div>
                            </div>
                        <?php elseif ($refund['refund_status'] == 'approved'): ?>
                            <div class="timeline-item">
                                <div class="timeline-icon" style="background: #28a745;">✓</div>
                                <div class="timeline-content">
                                    <strong>Refund Disetujui</strong>
                                    <div class="timeline-date">Dana akan dikembalikan dalam 3-5 hari kerja</div>
                                </div>
                            </div>

                            <div class="btn-refund-group">
                                <?php if (empty($refund['refund_target'])): ?>
                                    <button onclick="openRefundForm(<?= $refund['id'] ?>)" class="btn btn-form">
                                        📝 Isi Form Refund
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-form" disabled>
                                        ✅ Data Refund Sudah Terisi
                                    </button>
                                <?php endif; ?>
                                
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $studio_wa) ?>" target="_blank" class="btn btn-wa-alt">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    Pengajuan Via WhatsApp
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="timeline-item">
                                <div class="timeline-icon" style="background: #dc3545;">✗</div>
                                <div class="timeline-content">
                                    <strong>Refund Ditolak</strong>
                                    <div class="timeline-date">Hubungi admin untuk informasi lebih lanjut</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="empty-state">
                <h2>Belum Ada Request Refund</h2>
                <p style="color: #666; margin-bottom: 25px;">
                    Anda belum pernah mengajukan refund untuk transaksi apapun.
                </p>
                <a href="history.php" class="btn" style="background: #ea3671; color: white;">
                    Lihat Riwayat Transaksi
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Refund Data Modal -->
    <div id="refundModal" class="modal">
        <div class="modal-content">
            <h2 style="color: #ea3671; margin-bottom: 20px; font-weight: 800;">📝 Form Pengajuan Refund</h2>
            <p style="color: #666; margin-bottom: 25px; font-size: 0.9rem; line-height: 1.6;">
                Silakan lengkapi data rekening atau e-wallet Kakak untuk proses pengembalian dana.
            </p>
            
            <form id="refundDataForm">
                <input type="hidden" name="booking_id" id="refund_booking_id">
                
                <div class="form-group">
                    <label class="form-label">Pilih Bank / E-Wallet</label>
                    <select class="form-input" name="refund_method" onchange="toggleOtherMethod(this.value)" required>
                        <option value="" disabled selected>Pilih salah satu...</option>
                        <optgroup label="E-Wallet">
                            <option value="Dana">Dana</option>
                            <option value="OVO">OVO</option>
                            <option value="GoPay">GoPay</option>
                            <option value="ShopeePay">ShopeePay</option>
                        </optgroup>
                        <optgroup label="Bank">
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                        </optgroup>
                        <option value="Lainnya">Lainnya (Tulis manual)</option>
                    </select>
                </div>

                <div id="other_method_div" class="form-group" style="display: none;">
                    <label class="form-label">Nama Bank / Dompet Lain</label>
                    <input type="text" name="other_method" id="other_method" class="form-input" placeholder="Contoh: Bank Jateng, LinkAja, dll">
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Rekening / HP E-Wallet</label>
                    <input type="text" name="refund_account" class="form-input" placeholder="Masukkan nomor akun..." required>
                </div>

                <label class="checkbox-container">
                    <input type="checkbox" id="confirmData">
                    <span class="checkmark"></span>
                    <span class="label-text">Data yang saya masukan sudah benar dan siap untuk dikirim</span>
                </label>

                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeRefundModal()" class="btn btn-back" style="flex: 1; text-align: center; border: none;">Batal</button>
                    <button type="button" id="btnSubmitRefundData" onclick="submitRefundForm()" class="btn btn-form" style="flex: 2;">Kirim Pengajuan Refund</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Popup Modal -->
    <div id="successRefundModal" class="modal">
        <div class="modal-content" style="text-align: center; border-top: 8px solid #25d366;">
            <div style="font-size: 4rem; margin-bottom: 20px;">📤</div>
            <h2 style="color: #15803d; margin-bottom: 15px; font-weight: 800;">Berhasil Terkirim!</h2>
            <p style="color: #666; line-height: 1.6; margin-bottom: 30px;">
                Pengembalian dana Kakak sedang diproses oleh tim kami. Jika ada pertanyaan, silakan hubungi admin di bawah ini ya! ✨
            </p>
            
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $studio_wa) ?>" class="btn btn-wa-alt" style="justify-content: center; padding: 15px; font-size: 1.1rem; border-radius: 15px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    Hubungi via WhatsApp
                                </a>
                <a href="../index.php" class="btn btn-back" style="text-align: center; padding: 15px; border-radius: 15px;">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
