<?php
session_start();
require_once "middleware/customer_auth.php";
require_once "config/database.php";

/*AMBIL PARAMETER*/
$id_kamar = $_GET['id_kamar'] ?? null;
$checkin = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;

if (!$id_kamar || !$checkin || !$checkout) {
    die("Data reservasi tidak lengkap.");
}

/*KONEKSI DB*/
$db = new Koneksi();
$conn = $db->getKoneksi();

/*DATA KAMAR*/
$stmt = $conn->prepare("SELECT * FROM kamar WHERE id_kamar = ?");
$stmt->bind_param("i", $id_kamar);
$stmt->execute();
$kamar = $stmt->get_result()->fetch_assoc();

if (!$kamar) {
    die("Kamar tidak ditemukan.");
}

/*HITUNG MALAM*/
$checkin_date = new DateTime($checkin);
$checkout_date = new DateTime($checkout);
$jumlah_malam = $checkout_date->diff($checkin_date)->days;

if ($jumlah_malam <= 0) {
    die("Tanggal tidak valid.");
}

$total_harga = $jumlah_malam * $kamar['harga'];

function generateBookingCode()
{
    $prefix = 'VLR';
    $date = date('Ymd');
    $rand = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    return "$prefix-$date-$rand";
}

if (!isset($_SESSION['kode_booking'])) {
    $_SESSION['kode_booking'] = generateBookingCode();
}

$kode_booking = $_SESSION['kode_booking'];

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reservation Summary | Velaris Hotel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter&display=swap" rel="stylesheet">

    <style>
        /*  BASE */
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: #111;
        }

        /* BLUR BACKGROUND */
        .page-bg {
            position: fixed;
            inset: 0;
            background:
                linear-gradient(rgba(0, 0, 0, .45), rgba(0, 0, 0, .45)),
                url('uploads/experiences/pool.jpg') center/cover no-repeat;
            filter: blur(14px);
            z-index: -1;
        }

        .page-content {
            position: relative;
            z-index: 1;
        }

        /* HEADER  */
        .header {
            background: #fff;
            padding: 20px 40px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            font-family: 'Cinzel', serif;
            margin: 0;
        }

        /* LAYOUT */
        .container {
            max-width: 1200px;
            margin: 50px auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            padding: 0 20px;
        }

        /* ROOM CARD */
        .room-box {
            background: #fff;
            border-radius: 18px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .2);
        }

        .room-box img {
            width: 340px;
            object-fit: cover;
        }

        .room-info {
            padding: 30px;
        }

        .room-info h3 {
            font-family: 'Cinzel', serif;
            margin: 0 0 6px;
        }

        .room-info small {
            color: #777;
        }

        .room-info ul {
            padding-left: 18px;
            font-size: .9rem;
            line-height: 1.7;
        }

        .badge {
            margin-top: 10px;
            color: green;
            font-size: .85rem;
        }

        /* SUMMARY */
        .summary {
            background: #fff;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .2);
        }

        .summary h4 {
            margin-top: 0;
        }

        .summary hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 18px 0;
        }

        .total {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .btn {
            width: 100%;
            margin-top: 24px;
            padding: 16px;
            background: #d4af37;
            border: none;
            border-radius: 32px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            opacity: .9;
        }

        .btn-back {
            width: 100%;
            margin-top: 12px;
            padding: 14px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 32px;
            font-weight: 500;
            cursor: pointer;
            font-size: .9rem;
            transition: .3s;
        }

        .btn-back:hover {
            background: #000;
            color: #fff;
            border-color: #000;
        }
    </style>
</head>

<body>

    <div class="page-bg"></div>

    <div class="page-content">

        <!-- HEADER -->
        <div class="header">
            <h2>VELARIS HOTEL ★★★★</h2>
            👤 <?= htmlspecialchars($_SESSION['customer_name']); ?>
        </div>

        <div class="container">

            <!-- LEFT : ROOM DETAIL -->
            <div class="room-box">
                <img src="uploads/kamar/<?= htmlspecialchars($kamar['foto_kamar']); ?>">

                <div class="room-info">
                    <h3><?= htmlspecialchars($kamar['nama_kamar']); ?></h3>
                    <small><?= htmlspecialchars($kamar['tipe_kamar']); ?></small>

                    <ul>
                        <li>AC</li>
                        <li>LED TV</li>
                        <li>Free Wi-Fi</li>
                        <li>Work Desk</li>
                        <li>Bathroom with amenities</li>
                    </ul>

                    <div class="badge">
                        ✔ Free cancellation<br>
                        ✔ Book now, pay later
                    </div>
                </div>
            </div>

            <!-- RIGHT : SUMMARY -->
            <div class="summary">
                <h4>Reservation Summary</h4>
                <p>
                    <strong>Booking Code</strong><br>
                    <span style="letter-spacing:2px;font-weight:600">
                        <?= $kode_booking ?>
                    </span>
                </p>

                <p><strong>Check-in</strong><br><?= date('d M Y', strtotime($checkin)); ?></p>
                <p><strong>Check-out</strong><br><?= date('d M Y', strtotime($checkout)); ?></p>
                <p><strong>Nights</strong>: <?= $jumlah_malam; ?></p>

                <hr>

                <p>Total</p>
                <div class="total">
                    IDR <?= number_format($total_harga, 0, ',', '.'); ?>
                </div>

                <form action="payment.php" method="POST">
                    <input type="hidden" name="id_kamar" value="<?= $id_kamar; ?>">
                    <input type="hidden" name="checkin" value="<?= $checkin; ?>">
                    <input type="hidden" name="checkout" value="<?= $checkout; ?>">
                    <input type="hidden" name="total_harga" value="<?= $total_harga; ?>">
                    <input type="hidden" name="kode_booking" value="<?= $kode_booking; ?>">
                    <button class="btn">Proceed to Payment</button>
                </form>
                <!-- BUTTON BACK -->
                <button class="btn-back" onclick="history.back()">
                    ← Back to Booking
                </button>
            </div>

        </div>

    </div>

</body>

</html>