<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

/**
 * HARUS LOGIN
 */
if (!isset($_SESSION['customer_id'])) {
    header("Location: auth/login.php");
    exit;
}

/**
 * JIKA REQUEST METHOD POST - PROSES PEMBATALAN
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reservasi = $_POST['id_reservasi'] ?? null;
    $alasan = $_POST['alasan'] ?? '';
    $nama_bank = $_POST['nama_bank'] ?? '';
    $no_rekening = $_POST['no_rekening'] ?? '';
    $nama_pemilik = $_POST['nama_pemilik'] ?? '';
    
    if (!$id_reservasi || !$alasan || !$nama_bank || !$no_rekening || !$nama_pemilik) {
        die("Data tidak lengkap");
    }
    
    $db = new Koneksi();
    $conn = $db->getKoneksi();
    
    // Cek kepemilikan reservasi
    $stmt = $conn->prepare("
        SELECT id_reservasi, status, kode_booking
        FROM reservasi 
        WHERE id_reservasi = ? 
          AND id_user = ?
    ");
    $stmt->bind_param("ii", $id_reservasi, $_SESSION['customer_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservasi = $result->fetch_assoc();
    
    if (!$reservasi) {
        die("Reservasi tidak ditemukan atau bukan milik Anda");
    }
    
    if ($reservasi['status'] === 'cancelled') {
        die("Reservasi ini sudah dibatalkan sebelumnya");
    }
    
    // Mulai transaksi
    $conn->begin_transaction();
    
    try {
        // 1. Update status reservasi menjadi 'cancelled_request' (menunggu approval admin)
        $stmt = $conn->prepare("
            UPDATE reservasi 
            SET status = 'cancelled_request'
            WHERE id_reservasi = ?
        ");
        $stmt->bind_param("i", $id_reservasi);
        $stmt->execute();
        
        // 2. Insert data pembatalan ke tabel pembatalan
        // Sesuaikan dengan nama kolom: id_batal, tgl_pengajuan, status_pengajuan
        $stmt = $conn->prepare("
            INSERT INTO pembatalan (
                id_reservasi,
                tgl_pengajuan,
                alasan,
                nama_bank,
                no_rekening,
                nama_pemilik,
                status_pengajuan
            ) VALUES (?, NOW(), ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("issss", $id_reservasi, $alasan, $nama_bank, $no_rekening, $nama_pemilik);
        $stmt->execute();
        
        // Commit transaksi
        $conn->commit();
        
        // Redirect ke halaman sukses
        header("Location: pembatalan_process.php?id_reservasi=" . $id_reservasi);
        exit;
        
    } catch (Exception $e) {
        $conn->rollback();
        die("Gagal membatalkan reservasi: " . $e->getMessage());
    }
    
    exit;
}

/**
 * JIKA REQUEST METHOD GET - TAMPILKAN FORM
 */
$id_reservasi = $_GET['id_reservasi'] ?? null;
if (!$id_reservasi) {
    die("ID reservasi tidak valid");
}

$id_reservasi = (int)$id_reservasi;
$id_user = (int)$_SESSION['customer_id'];

// Ambil data reservasi
$reservasi = fetch_single("
    SELECT 
        r.id_reservasi,
        r.kode_booking,
        r.tgl_checkin,
        r.tgl_checkout,
        r.status,
        r.total_harga,
        k.nama_kamar,
        k.tipe_kamar
    FROM reservasi r
    JOIN kamar k ON r.id_kamar = k.id_kamar
    WHERE r.id_reservasi = $id_reservasi
      AND r.id_user = $id_user
");

if (!$reservasi) {
    die("Reservasi tidak ditemukan atau bukan milik Anda");
}

if ($reservasi['status'] === 'cancelled') {
    die("Reservasi ini sudah dibatalkan.");
}

// Tidak perlu cek status = paid, semua status bisa dibatalkan selama belum cancelled
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembatalan Reservasi | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
*{
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}
body{
    margin:0;
    min-height:100vh;
    background:
        linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)),
        url('uploads/experiences/pool.jpg') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 20px;
}
.card{
    background:#fff;
    border-radius:22px;
    padding:40px;
    width:100%;
    max-width:580px;
    box-shadow:0 25px 60px rgba(0,0,0,.3);
}
h1{
    font-family:'Cinzel',serif;
    margin:0 0 10px;
    font-size:1.8rem;
}
.subtitle{
    color:#666;
    font-size:.9rem;
    margin-bottom:30px;
}

/* BOOKING INFO BOX */
.booking-info{
    background:#f8f9fa;
    border:1px dashed #d4af37;
    border-radius:14px;
    padding:18px;
    margin-bottom:30px;
}
.booking-info .code{
    font-family:'Courier New', monospace;
    font-size:1.1rem;
    font-weight:700;
    letter-spacing:3px;
    color:#000;
    margin:8px 0;
}
.booking-info p{
    margin:6px 0;
    font-size:.9rem;
    color:#555;
}

/* FORM */
label{
    font-weight:600;
    display:block;
    margin-top:20px;
    font-size:.9rem;
    color:#333;
}
input, textarea, select{
    width:100%;
    padding:12px 16px;
    margin-top:8px;
    border-radius:12px;
    border:1px solid #ddd;
    font-size:.9rem;
    transition:.3s;
}
input:focus, textarea:focus{
    outline:none;
    border-color:#d4af37;
    box-shadow:0 0 0 3px rgba(212,175,55,.1);
}
textarea{
    resize:vertical;
    min-height:100px;
}

/* BUTTONS */
.btn{
    margin-top:24px;
    width:100%;
    padding:16px;
    border:none;
    border-radius:32px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
    font-size:.95rem;
    letter-spacing:.5px;
}
.btn-submit{
    background:#e74c3c;
    color:#fff;
}
.btn-submit:hover{
    background:#c0392b;
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(231,76,60,.3);
}
.btn-back{
    background:#fff;
    color:#666;
    border:1px solid #ddd;
}
.btn-back:hover{
    background:#000;
    color:#fff;
    border-color:#000;
}

/* LUXURY SWEETALERT */
.swal2-popup.luxury-alert{
    border-radius:24px !important;
    padding:40px !important;
    box-shadow:0 30px 80px rgba(0,0,0,.25) !important;
}
.swal2-popup.luxury-alert .swal2-icon{
    margin:0 auto 20px !important;
    border-width:3px !important;
}
.swal2-popup.luxury-alert .swal2-title{
    font-family:'Cinzel',serif !important;
    font-size:1.8rem !important;
    color:#1a1a1a !important;
    margin-bottom:10px !important;
}
.swal2-popup.luxury-alert .swal2-html-container{
    font-size:1rem !important;
    color:#666 !important;
    line-height:1.6 !important;
    margin:20px 0 !important;
}
.swal2-popup.luxury-alert .booking-code-display{
    display:inline-block;
    margin:15px 0;
    padding:12px 20px;
    background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border:2px dashed #d4af37;
    border-radius:12px;
    font-family:'Courier New', monospace;
    font-size:1.1rem;
    font-weight:700;
    letter-spacing:3px;
    color:#000;
}
.swal2-popup.luxury-alert .swal2-actions{
    margin-top:30px !important;
    gap:15px !important;
}
.swal2-popup.luxury-alert .swal2-confirm{
    background:linear-gradient(135deg, #c62828 0%, #b71c1c 100%) !important;
    border:none !important;
    border-radius:30px !important;
    padding:14px 40px !important;
    font-size:1rem !important;
    font-weight:600 !important;
    letter-spacing:1px !important;
    box-shadow:0 8px 20px rgba(198,40,40,.3) !important;
    transition:.3s !important;
}
.swal2-popup.luxury-alert .swal2-confirm:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 28px rgba(198,40,40,.4) !important;
}
.swal2-popup.luxury-alert .swal2-cancel{
    background:#fff !important;
    border:2px solid #e0e0e0 !important;
    border-radius:30px !important;
    padding:14px 40px !important;
    font-size:1rem !important;
    font-weight:600 !important;
    letter-spacing:1px !important;
    color:#555 !important;
    transition:.3s !important;
}
.swal2-popup.luxury-alert .swal2-cancel:hover{
    background:#f5f5f5 !important;
    border-color:#bdbdbd !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<div class="card">
    <h1>Pembatalan Reservasi</h1>
    <p class="subtitle">Lengkapi formulir di bawah untuk mengajukan pembatalan reservasi Anda.</p>

    <!-- BOOKING INFO -->
    <div class="booking-info">
        <strong style="font-size:.85rem;color:#777;text-transform:uppercase;letter-spacing:1px;">Booking Code</strong>
        <div class="code"><?= htmlspecialchars($reservasi['kode_booking']) ?></div>
        <p><strong>Kamar:</strong> <?= htmlspecialchars($reservasi['nama_kamar']) ?> (<?= htmlspecialchars($reservasi['tipe_kamar']) ?>)</p>
        <p><strong>Check-in:</strong> <?= date('d M Y', strtotime($reservasi['tgl_checkin'])) ?></p>
        <p><strong>Check-out:</strong> <?= date('d M Y', strtotime($reservasi['tgl_checkout'])) ?></p>
        <p><strong>Total:</strong> IDR <?= number_format($reservasi['total_harga'], 0, ',', '.') ?></p>
    </div>

    <form id="cancelForm" method="POST" action="pembatalan_process.php">
        <input type="hidden" name="id_reservasi" value="<?= $id_reservasi ?>">

        <label>Alasan Pembatalan *</label>
        <textarea name="alasan" required placeholder="Jelaskan alasan pembatalan Anda..."></textarea>

        <label>Nama Bank *</label>
        <input type="text" name="nama_bank" required placeholder="contoh: BCA, Mandiri, BNI">

        <label>Nomor Rekening *</label>
        <input type="text" name="no_rekening" required placeholder="Nomor rekening untuk refund">

        <label>Nama Pemilik Rekening *</label>
        <input type="text" name="nama_pemilik" required placeholder="Sesuai dengan nama di rekening bank">

        <button type="button" class="btn btn-submit" onclick="confirmCancel()">
            Ajukan Pembatalan
        </button>

        <button type="button" class="btn btn-back" onclick="window.location.href='guest_profile.php?tab=reservations'">
            ← Kembali ke Reservasi
        </button>
    </form>
</div>

<script>
function confirmCancel() {
    // Validasi form
    const form = document.getElementById('cancelForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Get form data
    const alasan = form.alasan.value;
    const namaBank = form.nama_bank.value;
    const noRek = form.no_rekening.value;
    const namaPemilik = form.nama_pemilik.value;

    Swal.fire({
        customClass: {
            popup: 'luxury-alert'
        },
        title: 'Konfirmasi Pembatalan',
        html: `
            <p>Anda akan membatalkan reservasi dengan booking code:</p>
            <div class="booking-code-display"><?= htmlspecialchars($reservasi['kode_booking']) ?></div>
            <p style="font-size:.9rem;color:#888;margin-top:15px;">
                Refund akan diproses ke rekening:<br>
                <strong>${namaBank} - ${noRek}</strong><br>
                a.n. <strong>${namaPemilik}</strong>
            </p>
            <p style="font-size:.85rem;color:#e74c3c;margin-top:10px;">
                Tindakan ini tidak dapat dibatalkan.
            </p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Batalkan Reservasi',
        cancelButtonText: 'Tidak, Kembali',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form
            form.submit();
        }
    });
}
</script>

</body>
</html>