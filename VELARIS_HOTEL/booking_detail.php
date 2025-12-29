<?php
session_start();
require_once "config/database.php";

/* LOGIN */
if (!isset($_SESSION['customer_id'])) {
    header("Location: auth/login.php");
    exit;
}

/* DB */
$db   = new Koneksi();
$conn = $db->getKoneksi();

/* ID RESERVASI */
$id_reservasi = $_GET['id_reservasi'] ?? null;
if (!$id_reservasi) die("ID reservasi tidak ditemukan");

/* QUERY */
$stmt = $conn->prepare("
    SELECT 
        r.id_reservasi,
        r.tgl_checkin,
        r.tgl_checkout,
        r.total_harga,
        r.status,
        k.nama_kamar,
        k.tipe_kamar,
        k.foto_kamar
    FROM reservasi r
    JOIN kamar k ON r.id_kamar = k.id_kamar
    WHERE r.id_reservasi = ?
      AND r.id_user = ?
");
$stmt->bind_param("ii", $id_reservasi, $_SESSION['customer_id']);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("Data booking tidak ditemukan");

/* HITUNG MALAM */
$checkin  = new DateTime($data['tgl_checkin']);
$checkout = new DateTime($data['tgl_checkout']);
$jumlah_malam = $checkout->diff($checkin)->days;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Booking Detail | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Inter',sans-serif;
    background:url('uploads/experiences/pool.jpg') center/cover no-repeat fixed;
}

/* OVERLAY */
.overlay{
    min-height:100vh;
    backdrop-filter:blur(8px);
    background:rgba(0,0,0,.45);
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 20px;
}

/* CARD */
.card{
    background:#fff;
    border-radius:22px;
    max-width:1100px;
    width:100%;
    display:grid;
    grid-template-columns:1.1fr .9fr;
    box-shadow:0 25px 60px rgba(0,0,0,.3);
    overflow:hidden;
}

/* LEFT */
.left{
    padding:50px;
}
.check{
    width:60px;
    height:60px;
    border-radius:50%;
    background:#2ecc71;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    margin-bottom:20px;
}
.left h1{
    margin:0 0 10px;
}
.left p{
    color:#666;
    line-height:1.6;
}

/* RIGHT */
.right{
    background:#fafafa;
    padding:40px;
}
.right h3{
    font-family:'Cinzel',serif;
    margin-top:0;
}

/* ROOM */
.room{
    display:flex;
    gap:16px;
    margin-bottom:20px;
}
.room img{
    width:120px;
    height:90px;
    border-radius:12px;
    object-fit:cover;
}

/* SUMMARY */
.summary div{
    display:flex;
    justify-content:space-between;
    margin:8px 0;
}
.total{
    border-top:1px solid #ddd;
    margin-top:16px;
    padding-top:16px;
    font-weight:600;
    font-size:1.2rem;
}

/* ACTIONS */
.actions{
    grid-column:1 / -1;
    display:flex;
    gap:20px;
    padding:30px 40px 40px;
    background:#fff;
}

/* BUTTON */
.btn{
    flex:1;
    padding:16px;
    border-radius:40px;
    font-weight:600;
    text-align:center;
    text-decoration:none;
    transition:.3s;
}

/* CANCEL */
.btn.cancel{
    background:#e74c3c;
    color:#fff;
}
.btn.cancel:hover{
    background:#c0392b;
}

/* HOME */
.btn.home{
    background:#d4af37;
    color:#000;
}
.btn.home:hover{
    background:#c9a633;
}
</style>
</head>

<body>

<div class="overlay">

    <div class="card">

        <!-- LEFT -->
        <div class="left">
            <div class="check">✓</div>
            <h1>Booking Confirmed</h1>
            <p>
                Terima kasih <strong><?= htmlspecialchars($_SESSION['customer_name']) ?></strong><br>
                Reservasi kamar Anda telah berhasil.
            </p>

            <p>Status:
                <strong style="color:green;">
                    Sudah Dibayar
                </strong>
            </p>
        </div>

        <!-- RIGHT -->
        <div class="right">
            <h3>Booking Summary</h3>

            <div class="room">
                <img src="uploads/kamar/<?= htmlspecialchars($data['foto_kamar']) ?>">
                <div>
                    <strong><?= htmlspecialchars($data['nama_kamar']) ?></strong><br>
                    <small><?= htmlspecialchars($data['tipe_kamar']) ?></small>
                </div>
            </div>

            <div class="summary">
                <div>
                    <span>Check-in</span>
                    <span><?= date('d M Y', strtotime($data['tgl_checkin'])) ?></span>
                </div>
                <div>
                    <span>Check-out</span>
                    <span><?= date('d M Y', strtotime($data['tgl_checkout'])) ?></span>
                </div>
                <div>
                    <span>Nights</span>
                    <span><?= $jumlah_malam ?></span>
                </div>

                <div class="total">
                    <span>Total</span>
                    <span>IDR <?= number_format($data['total_harga'],0,',','.') ?></span>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="actions">
            <?php if ($data['status'] !== 'cancelled'): ?>
                <a href="pembatalan.php?id_reservasi=<?= $data['id_reservasi'] ?>"
                   class="btn cancel"
                   onclick="return confirm('Yakin ingin membatalkan reservasi ini?')">
                   Batalkan Reservasi
                </a>
            <?php endif; ?>

            <a href="index.php" class="btn home">
                Back to Home
            </a>
        </div>

    </div>

</div>

</body>
</html>
