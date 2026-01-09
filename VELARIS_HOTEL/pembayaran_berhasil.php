<?php
session_start();
require_once "config/database.php";

$id_reservasi = $_GET['id_reservasi'] ?? null;
if (!$id_reservasi) {
    die("ID reservasi tidak valid");
}

/* KONEKSI DB */
$db   = new Koneksi();
$conn = $db->getKoneksi();

/* AMBIL DATA RESERVASI */
$stmt = $conn->prepare("
    SELECT kode_booking 
    FROM reservasi 
    WHERE id_reservasi = ?
");
$stmt->bind_param("i", $id_reservasi);
$stmt->execute();
$result = $stmt->get_result();
$data   = $result->fetch_assoc();

if (!$data) {
    die("Data reservasi tidak ditemukan");
}

$kode_booking = $data['kode_booking'];

// UPDATE STATUS JADI LUNAS
$update = $conn->prepare("
    UPDATE reservasi 
    SET status = 'lunas'
    WHERE id_reservasi = ?
      AND status != 'lunas'
");
$update->bind_param("i", $id_reservasi);
$update->execute();


?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Berhasil | Velaris Hotel</title>
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

/* CARD */
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

/* ICON */
.success-icon{
    width:64px;
    height:64px;
    background:#27ae60;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:34px;
    margin:0 auto 20px;
}

/* TEXT */
.success-card h1{
    margin:10px 0;
    font-size:26px;
}

.success-card p{
    color:#444;
    line-height:1.6;
    font-size:.95rem;
}

/* BOOKING CODE */
.booking-code{
    margin:22px 0;
    padding:16px;
    border-radius:14px;
    background:#f6f6f6;
    border:1px dashed #d4af37;
}

.booking-code span{
    display:block;
    font-size:.75rem;
    letter-spacing:1px;
    color:#777;
}

.booking-code strong{
    font-size:1.25rem;
    letter-spacing:3px;
    color:#000;
}

/* BUTTON */
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
    <div class="success-icon">✓</div>

    <h1>Pembayaran Berhasil</h1>

    <p>
        Terima kasih.<br>
        Pembayaran Anda telah berhasil diproses.
    </p>

    <!-- KODE BOOKING -->
    <div class="booking-code">
        <span>BOOKING CODE</span>
        <strong><?= htmlspecialchars($kode_booking) ?></strong>
    </div>

    <p style="font-size:.85rem; color:#666;">
        Simpan kode booking ini untuk proses check-in di hotel.
    </p>

    <a href="booking_detail.php?id_reservasi=<?= $id_reservasi ?>">
        Lihat Detail Booking
    </a>
</div>

</body>
</html>
