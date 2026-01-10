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
        SELECT r.*, u.email AS customer_email, u.nama_lengkap AS customer_name, k.nama_kamar, k.tipe_kamar, k.foto_kamar
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
        $message = 'not_found';
    } else {
        // Cek status
        if($reservation['status'] == 'batal' || $reservation['status'] == 'pengajuan_batal'){
            $message = 'cancelled';
        } elseif($reservation['status'] != 'lunas' && $reservation['status'] != 'checkin'){
            $message = 'not_paid';
        } elseif($reservation['tgl_checkin'] != $today){
            $message = 'wrong_date';
        } elseif($reservation['status'] == 'checkin'){
            $message = 'already_checkin';
        } else {
            // Jika ada aksi checkin
            if(isset($_POST['confirm_checkin'])){
                // Update status menjadi checked_in
                $update = $conn->prepare("UPDATE reservasi SET status = 'checkin' WHERE id_reservasi = ?");
                $update->bind_param("i", $reservation['id_reservasi']);
                if($update->execute()){
                    $message = 'success';
                    $reservation['status'] = 'checkin';
                } else {
                    $message = 'failed';
                }
            }
        }
    }
}

require_once "components/header.php";
?>

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* BACKGROUND */
.page-after-header{
    margin-top:90px;
    background:
        linear-gradient(135deg, rgba(212,175,55,.95) 0%, rgba(184,134,11,.95) 100%),
        url('uploads/experiences/pool.jpg') center/cover no-repeat;
    background-attachment:fixed;
    min-height:100vh;
    padding:80px 20px;
    position:relative;
}

.page-after-header::before{
    content:'';
    position:absolute;
    inset:0;
    background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,165.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
    pointer-events:none;
}

.checkin-container{
    max-width:900px;
    margin:auto;
}

/* HEADER */
.checkin-header{
    text-align:center;
    margin-bottom:50px;
}

.checkin-title{
    font-family:'Cinzel',serif;
    font-size:3rem;
    font-weight:700;
    color:#fff;
    margin:0 0 10px;
    letter-spacing:4px;
    text-shadow:0 4px 12px rgba(0,0,0,.2);
}

.checkin-subtitle{
    color:rgba(255,255,255,.9);
    font-size:1.1rem;
    letter-spacing:2px;
}

/* FORM CARD */
.checkin-form-card{
    background:rgba(255,255,255,.95);
    backdrop-filter:blur(10px);
    border-radius:24px;
    padding:45px;
    box-shadow:0 25px 60px rgba(0,0,0,.2);
    margin-bottom:40px;
}

.form-title{
    font-family:'Cinzel',serif;
    font-size:1.5rem;
    text-align:center;
    margin:0 0 30px;
    color:#1a1a1a;
}

.checkin-form{
    display:flex;
    flex-direction:column;
    gap:20px;
}

.input-group{
    position:relative;
}

.input-group label{
    display:block;
    font-size:.85rem;
    font-weight:600;
    color:#555;
    margin-bottom:8px;
    letter-spacing:.5px;
}

.input-group input{
    width:100%;
    padding:16px 20px;
    border-radius:14px;
    border:2px solid #e0e0e0;
    font-size:.95rem;
    transition:.3s;
    background:#fafafa;
}

.input-group input:focus{
    outline:none;
    border-color:#d4af37;
    background:#fff;
    box-shadow:0 0 0 4px rgba(212,175,55,.1);
}

.input-hint{
    font-size:.75rem;
    color:#888;
    margin-top:6px;
    font-style:italic;
}

.btn-submit{
    padding:18px;
    border-radius:32px;
    border:none;
    background:linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
    color:#000;
    font-size:1rem;
    font-weight:600;
    letter-spacing:1px;
    cursor:pointer;
    transition:.3s;
    box-shadow:0 8px 20px rgba(212,175,55,.3);
}

.btn-submit:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 28px rgba(212,175,55,.4);
    background:linear-gradient(135deg, #e5c158 0%, #d4af37 100%);
}

/* RESERVATION CARD */
.reservation-card{
    background:rgba(255,255,255,.95);
    backdrop-filter:blur(10px);
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 25px 60px rgba(0,0,0,.2);
}

.reservation-header{
    background:linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
    padding:30px;
    text-align:center;
    color:#000;
}

.reservation-header h3{
    font-family:'Cinzel',serif;
    margin:0 0 10px;
    font-size:1.8rem;
    letter-spacing:2px;
    font-weight:700;
}

.booking-code-display{
    display:inline-block;
    padding:12px 24px;
    background:rgba(0,0,0,.15);
    border-radius:12px;
    font-family:'Courier New',monospace;
    font-size:1.2rem;
    font-weight:700;
    letter-spacing:3px;
    margin-top:10px;
    color:#000;
}

.reservation-body{
    display:grid;
    grid-template-columns:300px 1fr;
    gap:0;
}

.room-image{
    width:300px;
    height:100%;
    object-fit:cover;
}

.reservation-details{
    padding:35px;
}

.detail-row{
    display:flex;
    justify-content:space-between;
    padding:14px 0;
    border-bottom:1px solid #f0f0f0;
}

.detail-row:last-child{
    border-bottom:none;
}

.detail-label{
    font-weight:600;
    color:#666;
    font-size:.9rem;
}

.detail-value{
    font-weight:600;
    color:#1a1a1a;
    font-size:.95rem;
}

.status-badge{
    display:inline-block;
    padding:8px 18px;
    border-radius:20px;
    font-size:.8rem;
    font-weight:600;
    letter-spacing:1px;
    text-transform:uppercase;
}

.status-lunas{
    background:#d4edda;
    color:#155724;
}

.status-checkin{
    background:#000;
    color:#fff;
}

.status-batal{
    background:#f8d7da;
    color:#721c24;
}

/* ACTION BUTTONS */
.action-section{
    padding:30px;
    background:#fafafa;
    display:flex;
    gap:15px;
    justify-content:center;
}

.btn-action{
    padding:14px 40px;
    border-radius:32px;
    font-size:.9rem;
    font-weight:600;
    letter-spacing:1px;
    cursor:pointer;
    transition:.3s;
    border:none;
}

.btn-checkin{
    background:linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
    color:#000;
    box-shadow:0 8px 20px rgba(212,175,55,.3);
}

.btn-checkin:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 28px rgba(212,175,55,.4);
    background:linear-gradient(135deg, #e5c158 0%, #d4af37 100%);
}

.btn-profile{
    background:#fff;
    color:#333;
    border:2px solid #e0e0e0;
}

.btn-profile:hover{
    background:#f5f5f5;
}

.btn-disabled{
    opacity:.5;
    cursor:not-allowed;
    pointer-events:none;
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

.swal2-popup.luxury-alert .booking-code-swal{
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
    background:linear-gradient(135deg, #d4af37 0%, #b8860b 100%) !important;
    border:none !important;
    border-radius:30px !important;
    padding:14px 40px !important;
    font-size:1rem !important;
    font-weight:600 !important;
    letter-spacing:1px !important;
    box-shadow:0 8px 20px rgba(212,175,55,.3) !important;
    transition:.3s !important;
    color:#000 !important;
}

.swal2-popup.luxury-alert .swal2-confirm:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 28px rgba(212,175,55,.4) !important;
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

/* RESPONSIVE */
@media (max-width: 768px) {
    .checkin-title{
        font-size:2rem;
    }
    .reservation-body{
        grid-template-columns:1fr;
    }
    .room-image{
        width:100%;
        height:200px;
    }
    .action-section{
        flex-direction:column;
    }
}
</style>

<div class="page-after-header">
<div class="checkin-container">

    <!-- HEADER -->
    <div class="checkin-header">
        <h1 class="checkin-title">ONLINE CHECK-IN</h1>
        <p class="checkin-subtitle">VELARIS HOTEL</p>
    </div>

    <!-- FORM CARD -->
    <div class="checkin-form-card">
        <h3 class="form-title">Find Your Reservation</h3>
        
        <form method="post" class="checkin-form" id="searchForm">
            <div class="input-group">
                <label>Booking Code</label>
                <input type="text" name="kode_booking" placeholder="e.g., VLR-20260108-8FBF" required value="<?= isset($_POST['kode_booking']) ? htmlspecialchars($_POST['kode_booking']) : '' ?>">
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="your@email.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                <p class="input-hint">Enter the email address used when making the reservation</p>
            </div>

            <button type="submit" name="checkin_submit" class="btn-submit">
                🔍 FIND RESERVATION
            </button>
        </form>
    </div>

    <!-- RESERVATION CARD -->
    <?php if($reservation): ?>
        <div class="reservation-card">
            <div class="reservation-header">
                <h3>Reservation Found</h3>
                <div class="booking-code-display">
                    <?= htmlspecialchars($reservation['kode_booking']) ?>
                </div>
            </div>

            <div class="reservation-body">
                <img src="uploads/kamar/<?= htmlspecialchars($reservation['foto_kamar']) ?>" 
                     alt="Room" 
                     class="room-image">

                <div class="reservation-details">
                    <div class="detail-row">
                        <span class="detail-label">Guest Name</span>
                        <span class="detail-value"><?= htmlspecialchars($reservation['customer_name']) ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?= htmlspecialchars($reservation['customer_email']) ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Room</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($reservation['nama_kamar']) ?>
                            <small style="color:#888;">(<?= htmlspecialchars($reservation['tipe_kamar']) ?>)</small>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Check-in Date</span>
                        <span class="detail-value"><?= date('d M Y', strtotime($reservation['tgl_checkin'])) ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Check-out Date</span>
                        <span class="detail-value"><?= date('d M Y', strtotime($reservation['tgl_checkout'])) ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            <?php if($reservation['status'] == 'checkin'): ?>
                                <span class="status-badge status-checkin">✓ CHECKED IN</span>
                            <?php elseif($reservation['status'] == 'lunas'): ?>
                                <span class="status-badge status-lunas">✓ CONFIRMED</span>
                            <?php else: ?>
                                <span class="status-badge status-batal">✗ CANCELLED</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="action-section">
                <?php if($reservation['status'] == 'lunas' && $reservation['tgl_checkin'] == $today): ?>
                    <form method="POST" id="checkinForm">
                        <input type="hidden" name="kode_booking" value="<?= htmlspecialchars($reservation['kode_booking']) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($reservation['customer_email']) ?>">
                        <input type="hidden" name="checkin_submit" value="1">
                        <input type="hidden" name="confirm_checkin" value="1">
                        <button type="button" class="btn-action btn-checkin" onclick="confirmCheckin()">
                            ✓ CHECK-IN NOW
                        </button>
                    </form>
                <?php elseif($reservation['status'] == 'checkin'): ?>
                    <a href="guest_profile.php?tab=reservations" class="btn-action btn-profile">
                        📋 View All Reservations
                    </a>
                <?php else: ?>
                    <button class="btn-action btn-checkin btn-disabled">
                        ✗ CHECK-IN NOT AVAILABLE
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Error Messages
<?php if($message == 'not_found'): ?>
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        icon: 'error',
        title: 'Reservation Not Found',
        html: 'No reservation found with the provided booking code and email.<br><br>Please check your details and try again.',
        confirmButtonText: 'Try Again'
    });
<?php elseif($message == 'cancelled'): ?>
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        icon: 'error',
        title: 'Reservation Cancelled',
        html: 'This reservation has been cancelled.<br>Check-in is not available.',
        confirmButtonText: 'OK'
    });
<?php elseif($message == 'not_paid'): ?>
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        icon: 'warning',
        title: 'Payment Required',
        html: 'This reservation has not been paid yet.<br>Please complete payment before check-in.',
        confirmButtonText: 'OK'
    });
<?php elseif($message == 'wrong_date'): ?>
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        icon: 'info',
        title: 'Wrong Check-in Date',
        html: 'Check-in is only available on your scheduled check-in date:<br><strong><?= date("d M Y", strtotime($reservation['tgl_checkin'])) ?></strong><br><br>Today is: <strong><?= date("d M Y") ?></strong>',
        confirmButtonText: 'OK'
    });
<?php elseif($message == 'already_checkin'): ?>
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        icon: 'info',
        title: 'Already Checked In',
        html: 'You have already completed check-in for this reservation.',
        confirmButtonText: 'OK'
    });
<?php elseif($message == 'success'): ?>
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        icon: 'success',
        title: 'Check-in Successful!',
        html: `
            <p>Welcome to Velaris Hotel!</p>
            <div class="booking-code-swal"><?= htmlspecialchars($reservation['kode_booking']) ?></div>
            <p style="font-size:.9rem;color:#888;margin-top:15px;">
                Please proceed to the reception desk with your booking code.
            </p>
        `,
        confirmButtonText: 'View My Reservations',
        timer: 5000,
        timerProgressBar: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'guest_profile.php?tab=reservations';
        }
    });
<?php elseif($message == 'failed'): ?>
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        icon: 'error',
        title: 'Check-in Failed',
        html: 'An error occurred during check-in. Please try again or contact reception.',
        confirmButtonText: 'Try Again'
    });
<?php endif; ?>

// Confirm Check-in
function confirmCheckin(){
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        title: 'Confirm Check-in',
        html: `
            <p>Are you ready to check-in for your reservation?</p>
            <div class="booking-code-swal"><?= htmlspecialchars($reservation['kode_booking'] ?? '') ?></div>
            <p style="font-size:.9rem;color:#888;margin-top:15px;">
                Room: <strong><?= htmlspecialchars($reservation['nama_kamar'] ?? '') ?></strong><br>
                Check-in: <strong><?= date('d M Y', strtotime($reservation['tgl_checkin'] ?? '')) ?></strong>
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Check-in',
        cancelButtonText: 'Not Yet',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if(result.isConfirmed){
            document.getElementById('checkinForm').submit();
        }
    });
}
</script>

<?php require_once "components/footer.php"; ?>