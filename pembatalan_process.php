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

if (!$id_reservasi || !$alasan) {
    die("Data pembatalan tidak lengkap");
}

/**
 * SIMPAN KE DATABASE
 */
$tgl_pengajuan = date('Y-m-d H:i:s');

$query = "
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
";

execute($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembatalan Diproses | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

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
}

.success-card{
    background:rgba(255,255,255,.95);
    backdrop-filter: blur(6px);
    border-radius:18px;
    padding:40px 36px;
    width:100%;
    max-width:520px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.success-icon{
    width:64px;
    height:64px;
    background:#f39c12;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:34px;
    margin:0 auto 20px;
}

.success-card h1{
    margin:10px 0;
    font-size:26px;
}

.success-card p{
    color:#444;
    line-height:1.6;
    font-size:.95rem;
}

.success-card a{
    display:inline-block;
    margin-top:26px;
    padding:14px 34px;
    background:#d4af37;
    color:#000;
    text-decoration:none;
    border-radius:30px;
    font-weight:600;
    transition:.2s;
}

.success-card a:hover{
    background:#c9a52f;
}
</style>
</head>

<body>

<div class="success-card">
    <div class="success-icon">⏳</div>

    <h1>Pembatalan Diproses</h1>

    <p>
        Pengajuan pembatalan reservasi Anda<br>
        telah berhasil dikirim.
    </p>

    <p style="font-size:.85rem; color:#666;">
        Permintaan akan ditinjau oleh tim kami.<br>
        Anda akan mendapatkan notifikasi setelah disetujui.
    </p>

    <a href="index.php">
        Back to Home
    </a>
</div>

</body>
</html>
