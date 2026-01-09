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

/* QUERY - TAMBAHKAN KODE BOOKING */
$stmt = $conn->prepare("
    SELECT 
        r.id_reservasi,
        r.tgl_checkin,
        r.tgl_checkout,
        r.total_harga,
        r.status,
        r.kode_booking,
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
$status = strtolower(trim($data['status']));

// NORMALISASI STATUS DATABASE → STATUS UI
switch ($status) {
    case 'paid':
        $status = 'lunas';
        break;

    case 'cancelled_request':
        $status = 'pembatalan_diajukan';
        break;

    case 'cancelled':
        $status = 'batal';
        break;

    case 'completed':
        $status = 'selesai';
        break;
}

/* HITUNG MALAM */
$checkin  = new DateTime($data['tgl_checkin']);
$checkout = new DateTime($data['tgl_checkout']);
$jumlah_malam = $checkout->diff($checkin)->days;

/* CEK STATUS PEMBATALAN */
// Cek apakah sudah pernah mengajukan pembatalan (ada di tabel pembatalan)
$stmt_check = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM pembatalan 
    WHERE id_reservasi = ?
");
$stmt_check->bind_param("i", $id_reservasi);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$pembatalan_data = $result_check->fetch_assoc();
$sudah_ajukan_pembatalan = ($pembatalan_data['total'] > 0);

/* TENTUKAN STATUS */
$is_cancelled = ($data['status'] === 'batal');
$is_cancelled_request = ($data['status'] === 'pembatalan_diajukan');

/* BUTTON BATAL MUNCUL JIKA: 
   - Belum pernah ajukan pembatalan DAN
   - Status bukan 'cancelled' 
   (pending, paid, apapun selain cancelled bisa dibatalkan)
*/
$show_cancel_button = (
    !$sudah_ajukan_pembatalan &&
    $data['status'] === 'lunas'
);


// DEBUG - Uncomment untuk debugging
// echo "<!-- DEBUG: status=" . $data['status'] . ", sudah_ajukan=" . ($sudah_ajukan_pembatalan?'YES':'NO') . ", is_cancelled=" . ($is_cancelled?'YES':'NO') . ", show_button=" . ($show_cancel_button?'YES':'NO') . " -->";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Booking Detail | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

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

/* BOOKING CODE BOX */
.booking-code-box{
    margin:25px 0;
    padding:18px;
    background:#f8f9fa;
    border:1px dashed #d4af37;
    border-radius:14px;
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
    border:none;
    cursor:pointer;
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

/* HISTORY */
.btn.history{
    background:#34495e;
    color:#fff;
}
.btn.history:hover{
    background:#2c3e50;
}

/* DISABLED BUTTON */
.btn:disabled{
    opacity:.5;
    cursor:not-allowed;
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
    letter-spacing:1px;
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<div class="overlay">

    <div class="card">

        <!-- LEFT -->
        <div class="left">
            <div class="check" style="background:<?= $is_cancelled ? '#e74c3c' : '#2ecc71' ?>">
                <?= $is_cancelled ? '✕' : '✓' ?>
            </div>
            <h1>
<?php
switch ($status) {
    case 'batal':
        echo 'Booking Dibatalkan';
        break;

    case 'pembatalan_diajukan':
        echo 'Pembatalan Diajukan';
        break;

    case 'menunggu_bayar':
        echo 'Menunggu Pembayaran';
        break;

    case 'menunggu_verifikasi':
        echo 'Menunggu Verifikasi';
        break;

    case 'lunas':
        echo 'Booking Confirmed';
        break;

    case 'selesai':
        echo 'Booking Selesai';
        break;

    default:
        echo 'Status Reservasi';
}
?>
</h1>


            <p>
                <?php if ($is_cancelled): ?>
                    Reservasi Anda telah dibatalkan.
                <?php else: ?>
                    Terima kasih <strong><?= htmlspecialchars($_SESSION['customer_name']) ?></strong><br>
                    Reservasi kamar Anda telah berhasil.
                <?php endif; ?>
            </p>

            <!-- BOOKING CODE -->
            <div class="booking-code-box">
                <small>BOOKING CODE</small>
                <strong><?= htmlspecialchars($data['kode_booking']) ?></strong>
            </div>

            <p style="font-size:.85rem; color:#888;">
                Simpan kode booking ini untuk proses check-in di hotel.
            </p>

           <p>Status:
<?php
switch ($status) {
    case 'batal':
        echo '<strong style="color:#e74c3c;">✗ Dibatalkan</strong>';
        break;

    case 'pembatalan_diajukan':
        echo '<strong style="color:#f39c12;">⏳ Pembatalan Diajukan</strong><br>
              <small style="color:#888;font-size:.8rem;">
                  Menunggu persetujuan admin
              </small>';
        break;

    case 'menunggu_bayar':
        echo '<strong style="color:#f39c12;">⏳ Menunggu Pembayaran</strong>';
        break;

    case 'menunggu_verifikasi':
        echo '<strong style="color:#3498db;">⏳ Menunggu Verifikasi</strong>';
        break;

    case 'lunas':
        echo '<strong style="color:#2ecc71;">✓ Lunas</strong>';
        break;

    case 'selesai':
        echo '<strong style="color:#9b59b6;">✓ Selesai</strong>';
        break;

    default:
        echo '<strong style="color:#000;">' . htmlspecialchars($status) . '</strong>';
}
?>
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
            <?php if ($show_cancel_button): ?>
                <a href="pembatalan.php?id_reservasi=<?= $data['id_reservasi'] ?>"
                   class="btn cancel">
                   Batalkan Reservasi
                </a>
            <?php elseif ($is_cancelled_request): ?>
                <div class="btn cancel" style="opacity:.6;cursor:not-allowed;background:#f39c12;">
                    ⏳ Pengajuan Sedang Diproses
                </div>
            <?php elseif ($is_cancelled): ?>
                <div class="btn cancel" style="opacity:.6;cursor:not-allowed;background:#999;">
                    ✗ Reservasi Sudah Dibatalkan
                </div>
            <?php endif; ?>

            <a href="guest_profile.php?tab=reservations" class="btn history">
                Riwayat Reservasi
            </a>

            <a href="index.php" class="btn home">
                Back to Home
            </a>
        </div>

    </div>

</div>

<script>
// Luxury Cancellation Confirmation
function confirmCancel(idReservasi, bookingCode){
    Swal.fire({
        customClass: {
            popup: 'luxury-alert'
        },
        title: 'Cancel Reservation',
        html: `
            <p>Are you sure you want to cancel this reservation?</p>
            <div class="booking-code-display">${bookingCode}</div>
            <p style="font-size:.9rem;color:#888;margin-top:15px;">
                This action cannot be undone. Your reservation will be cancelled immediately.
            </p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel It',
        cancelButtonText: 'Keep Reservation',
        reverseButtons: true,
        focusCancel: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('pembatalan.php?id_reservasi=' + idReservasi, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(
                    `Request failed: ${error}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                customClass: {
                    popup: 'luxury-alert'
                },
                title: 'Cancelled!',
                html: `
                    <p>Your reservation has been successfully cancelled.</p>
                    <div class="booking-code-display">${bookingCode}</div>
                `,
                icon: 'success',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location.reload();
            });
        }
    });
}
</script>

</body>
</html>