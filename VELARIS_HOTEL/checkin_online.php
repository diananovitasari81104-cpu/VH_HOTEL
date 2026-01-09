<?php
session_start();
require_once "config/database.php";

$page_title = 'Online Check-in';

$db = new Koneksi();
$conn = $db->getKoneksi();

$today = date('Y-m-d');
$message = '';
$reservation = null;

// Jika form submit
if(isset($_POST['checkin_submit'])){
    $kode_booking = $conn->real_escape_string($_POST['kode_booking']);
    $email        = $conn->real_escape_string($_POST['email']);

    // Cari reservasi
    $stmt = $conn->prepare("
        SELECT r.*, u.email AS customer_email, k.nama_kamar, k.tipe_kamar
        FROM reservasi r
        JOIN users u ON r.id_user = u.id_user
        JOIN kamar k ON r.id_kamar = k.id_kamar
        WHERE r.kode_booking = ? AND u.email = ? 
        LIMIT 1
    ");
    $stmt->bind_param("ss", $kode_booking, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();

    if(!$reservation){
        $message = 'Reservation not found.';
    } else {
        // Cek status
        if($reservation['status'] != 'lunas'){
            $message = 'Reservation cannot be checked-in (status not lunas).';
        } elseif($reservation['tgl_checkin'] != $today){
            $message = 'Check-in is only allowed on the check-in date.';
        } else {
            // Update status menjadi checked_in
            $update = $conn->prepare("UPDATE reservasi SET status = 'checkin' WHERE id_reservasi = ?");
            $update->bind_param("i", $reservation['id_reservasi']);
            if($update->execute()){
                $message = 'success';
                // refresh data reservasi
                $reservation['status'] = 'checkin';
            } else {
                $message = 'Failed to update check-in status.';
            }
        }
    }
}

require_once "components/header.php";
?>

<style>
.page-after-header{
    margin-top:90px;
    background:#f6f6f6;
    min-height:100vh;
    padding:60px 20px;
}
.checkin-container{
    max-width:600px;
    margin:auto;
}
.checkin-title{
    font-family:'Cinzel',serif;
    text-align:center;
    margin-bottom:40px;
    letter-spacing:2px;
}
.checkin-form{
    background:#fff;
    border-radius:18px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    display:flex;
    flex-direction:column;
    gap:15px;
}
.checkin-form input{
    padding:12px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:.9rem;
}
.checkin-form button{
    padding:12px;
    border-radius:25px;
    border:none;
    background:#000;
    color:#fff;
    font-size:.9rem;
    cursor:pointer;
    transition:.3s;
}
.checkin-form button:hover{ background:#333; }
.checkin-card{
    background:#fff;
    border-radius:18px;
    padding:26px;
    margin-top:30px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.checkin-info p{ margin:4px 0; font-size:.85rem; }
.badge{ display:inline-block; margin-top:10px; padding:6px 14px; border-radius:20px; font-size:.7rem; letter-spacing:1px; }
.badge.confirmed{ background:#f5c842;color:#000; }
.badge.checked_in{ background:#000;color:#fff; }
.btn-checkin{ padding:10px 26px; border-radius:25px; border:1px solid #000; background:#fff; font-size:.75rem; cursor:pointer; transition:.3s; }
.btn-checkin:hover{ background:#000;color:#fff; }
.btn-disabled{ opacity:.4; pointer-events:none; }
.message{ color:red; font-size:.85rem; text-align:center; margin-bottom:15px; }
.empty{ background:#fff; padding:60px; border-radius:20px; text-align:center; color:#777; }
</style>

<div class="page-after-header">
<div class="checkin-container">

    <h2 class="checkin-title">ONLINE CHECK-IN</h2>

    <form method="post" class="checkin-form">
        <?php if($message && $message != 'success'): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <input type="text" name="kode_booking" placeholder="Booking Code" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit" name="checkin_submit">Find Reservation</button>
    </form>

    <?php if($reservation): ?>
        <div class="checkin-card">
            <div class="checkin-info">
                <p><strong>Booking Code:</strong> <?= htmlspecialchars($reservation['kode_booking']) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($reservation['customer_email']) ?></p>
                <p><strong>Room:</strong> <?= htmlspecialchars($reservation['nama_kamar'].' ('.$reservation['tipe_kamar'].')') ?></p>
                <p><strong>Check-in:</strong> <?= $reservation['tgl_checkin'] ?></p>
                <p><strong>Check-out:</strong> <?= $reservation['tgl_checkout'] ?></p>
                <?php if($reservation['status']=='checkin'): ?>
                    <span class="badge checked_in">CHECKED IN</span>
                <?php else: ?>
                    <span class="badge confirmed">CONFIRMED</span>
                <?php endif; ?>
            </div>

            <div>
                <?php
                    $canCheckin = ($reservation['status']=='confirmed');
                ?>
                <?php if($canCheckin): ?>
                    <button class="btn-checkin" onclick="confirmCheckin()">
                        CHECK-IN
                    </button>
                <?php elseif($reservation['status']=='checkin'): ?>
                    <a href="guest_profile.php?tab=reservations" class="btn-checkin">
                        View Reservations
                    </a>
                <?php else: ?>
                    <button class="btn-checkin btn-disabled">NOT AVAILABLE</button>
                <?php endif; ?>
            </div>

        </div>
    <?php endif; ?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if($message=='success'): ?>
Swal.fire({
    icon: 'success',
    title: 'Check-in Successful!',
    text: 'Your reservation has been checked in.',
    confirmButtonColor: '#000'
});
<?php endif; ?>

function confirmCheckin(){
    Swal.fire({
        title:'Confirm Online Check-in',
        text:'Proceed with online check-in for this reservation?',
        icon:'question',
        showCancelButton:true,
        confirmButtonText:'Yes, Check-in',
        cancelButtonText:'Cancel',
        confirmButtonColor:'#000'
    }).then((result)=>{
        if(result.isConfirmed){
            // submit ulang form untuk update status
            document.querySelector('form.checkin-form').submit();
        }
    });
}
</script>

<?php require_once "components/footer.php"; ?>
