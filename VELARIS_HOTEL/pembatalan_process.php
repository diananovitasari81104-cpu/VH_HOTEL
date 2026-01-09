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
 * VALIDASI INPUT
 */
$id_reservasi   = $_POST['id_reservasi'] ?? null;
$alasan         = $_POST['alasan'] ?? '';
$nama_bank      = $_POST['nama_bank'] ?? '';
$no_rekening    = $_POST['no_rekening'] ?? '';
$nama_pemilik   = $_POST['nama_pemilik'] ?? '';

if (!$id_reservasi || !$alasan || !$nama_bank || !$no_rekening || !$nama_pemilik) {
    die("Data pembatalan tidak lengkap.");
}

$id_reservasi = (int)$id_reservasi;
$id_user      = (int)$_SESSION['customer_id'];

/**
 * AMBIL DATA RESERVASI
 */
$reservasi = fetch_single("
    SELECT id_reservasi, status, kode_booking
    FROM reservasi
    WHERE id_reservasi = '$id_reservasi'
      AND id_user = '$id_user'
");

if (!$reservasi) {
    die("Reservasi tidak ditemukan.");
}

/**
 * CEK STATUS (ANTI DOBEL PEMBATALAN)
 */
if (in_array($reservasi['status'], ['cancelled', 'cancelled_request'])) {
    die("Reservasi ini sudah diajukan pembatalan atau tidak dapat dibatalkan.");
}

/**
 * CEK APAKAH SUDAH ADA DATA PEMBATALAN
 */
$cekBatal = fetch_single("
    SELECT id_batal FROM pembatalan
    WHERE id_reservasi = '$id_reservasi'
");

if ($cekBatal) {
    die("Reservasi ini sudah pernah diajukan pembatalan.");
}

/**
 * SIMPAN PEMBATALAN
 */
$tgl_pengajuan = date('Y-m-d H:i:s');

execute("
    INSERT INTO pembatalan
    (id_reservasi, tgl_pengajuan, alasan, nama_bank, no_rekening, nama_pemilik, status_pengajuan)
    VALUES (
        '$id_reservasi',
        '$tgl_pengajuan',
        '$alasan',
        '$nama_bank',
        '$no_rekening',
        '$nama_pemilik',
        'pending'
    )
");

/**
 * UPDATE STATUS RESERVASI → pembatalan_diajukan
 */
execute("
    UPDATE reservasi
    SET status = 'pembatalan_diajukan'
    WHERE id_reservasi = '$id_reservasi'
      AND id_user = '$id_user'
");

$kode_booking = $reservasi['kode_booking'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembatalan Diproses | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;font-family:'Inter',sans-serif}
body{
    margin:0;
    min-height:100vh;
    background:linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)),
               url('uploads/experiences/pool.jpg') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
}
.success-card{
    background:rgba(255,255,255,.95);
    border-radius:18px;
    padding:40px 36px;
    max-width:520px;
    width:100%;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}
.success-icon{
    width:64px;height:64px;
    background:#f39c12;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:34px;
    margin:0 auto 20px;
}
.booking-code{
    margin:18px 0;
    font-weight:600;
    letter-spacing:2px;
}
.actions{
    margin-top:28px;
    display:flex;
    gap:12px;
    justify-content:center;
}
.actions a{
    padding:13px 30px;
    border-radius:30px;
    text-decoration:none;
    font-weight:600;
}
.btn-home{background:#d4af37;color:#000}
.btn-history{background:#000;color:#fff}

.booking-code-box{
    margin:20px 0;
    padding:18px;
    background:#f8f9fa;
    border:1px dashed #d4af37;
    border-radius:14px;
    text-align:center;
}
.booking-code-box small{
    display:block;
    color:#777;
    font-size:.75rem;
    margin-bottom:6px;
    letter-spacing:1px;
}
.booking-code-box strong{
    font-size:1.3rem;
    letter-spacing:3px;
    color:#000;
    font-weight:600;
}

</style>
</head>

<body>
<div class="success-card">
    <div class="success-icon">⏳</div>
    <h1>Pembatalan Diproses</h1>
    <p>Pengajuan pembatalan reservasi Anda telah berhasil dikirim.</p>

    <div class="booking-code-box">
    <small>BOOKING CODE</small>
    <strong><?= htmlspecialchars($kode_booking) ?></strong>
</div>


    <p style="font-size:.85rem;color:#666">
        Permintaan akan ditinjau oleh tim kami.<br>
        Anda akan mendapatkan notifikasi setelah diproses.
    </p>

    <div class="actions">
        <a href="index.php" class="btn-home">Back to Home</a>
        <a href="guest_profile.php?tab=reservations" class="btn-history">Riwayat Reservasi</a>
    </div>
</div>
</body>
</html>
