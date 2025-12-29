<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';


$id_reservasi = $_GET['id_reservasi'] ?? null;
if (!$id_reservasi) {
    die("ID reservasi tidak valid");
}

// Pastikan reservasi milik user
$reservasi = fetch_single("
    SELECT r.*, k.nama_kamar
    FROM reservasi r
    JOIN kamar k ON r.id_kamar = k.id_kamar
    WHERE r.id_reservasi = $id_reservasi
      AND r.id_user = {$_SESSION['customer_id']}
      AND r.status != 'cancelled'
");

if (!$reservasi) {
    die("Reservasi tidak ditemukan atau sudah dibatalkan");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembatalan Reservasi | Velaris Hotel</title>
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
.card{
    background:#fff;
    border-radius:18px;
    padding:36px;
    width:100%;
    max-width:520px;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}
h1{
    margin-top:0;
}
label{
    font-weight:600;
    display:block;
    margin-top:16px;
}
input, textarea, select{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:10px;
    border:1px solid #ccc;
}
button{
    margin-top:24px;
    width:100%;
    padding:14px;
    background:#d9534f;
    color:#fff;
    border:none;
    border-radius:30px;
    font-weight:600;
    cursor:pointer;
}
button:hover{
    background:#c9302c;
}
.info{
    font-size:.9rem;
    color:#555;
}
</style>
</head>

<body>

<div class="card">
    <h1>Pembatalan Reservasi</h1>

    <p class="info">
        Reservasi <strong>#<?= $reservasi['id_reservasi'] ?></strong><br>
        Kamar: <?= $reservasi['nama_kamar'] ?><br>
        Check-in: <?= $reservasi['tgl_checkin'] ?>
    </p>

    <form action="pembatalan_process.php" method="post">
        <input type="hidden" name="id_reservasi" value="<?= $id_reservasi ?>">

        <label>Alasan Pembatalan</label>
        <textarea name="alasan" required></textarea>

        <label>Nama Bank</label>
        <input type="text" name="nama_bank" required>

        <label>Nomor Rekening</label>
        <input type="text" name="no_rekening" required>

        <label>Nama Pemilik Rekening</label>
        <input type="text" name="nama_pemilik" required>

        <button type="submit">
            Ajukan Pembatalan
        </button>
    </form>
</div>

</body>
</html>
