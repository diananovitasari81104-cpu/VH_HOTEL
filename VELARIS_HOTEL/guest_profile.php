<?php
session_start();
require_once "config/database.php";
require_once "config/functions.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: auth/login.php");
    exit;
}

$page_title = 'Guest Profile';

$db = new Koneksi();
$conn = $db->getKoneksi();

$customer_id = $_SESSION['customer_id'];
$tab = $_GET['tab'] ?? 'profile';

/* AMBIL DATA USER JIKA TAB PROFILE */
$user = null;
if($tab=='profile'){
    $stmt = $conn->prepare("SELECT nama_lengkap, email, no_hp, password FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

/* AMBIL DATA RESERVASI JIKA TAB RESERVATIONS */
$reservations = [];
if ($tab == 'reservations') {
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
        WHERE r.id_user = ?
        ORDER BY r.id_reservasi DESC
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}

/* AMBIL DATA CANCELLED RESERVASI DENGAN PENGAJUAN */
$cancellations = [];
if ($tab == 'cancellation') {
    $stmt = $conn->prepare("
        SELECT 
            r.id_reservasi,
            r.tgl_checkin,
            r.tgl_checkout,
            r.total_harga,
            r.status AS status_reservasi,
            r.kode_booking,
            k.nama_kamar,
            k.tipe_kamar,
            k.foto_kamar,
            b.status_pengajuan,
            b.tgl_pengajuan
        FROM reservasi r
        JOIN kamar k ON r.id_kamar = k.id_kamar
        JOIN pembatalan b ON r.id_reservasi = b.id_reservasi
        WHERE r.id_user = ? AND r.status = 'batal'
        ORDER BY b.tgl_pengajuan DESC
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cancellations[] = $row;
    }
}

require_once "components/header.php";
?>

<style>
.page-after-header {
    margin-top: 90px;
    background: #f6f6f6;
    min-height: 100vh;
    padding: 60px 20px;
}

.profile-container {
    max-width: 1100px;
    margin: auto;
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 40px;
}

/* SIDEBAR */
.profile-sidebar {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    height: fit-content;
}

.profile-user {
    text-align: center;
    margin-bottom: 30px;
}

.avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    font-size: 1.6rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.profile-user strong { display: block; font-size: .9rem; }
.profile-user small { color: #888; font-size: .75rem; }

.profile-menu { display: flex; flex-direction: column; gap: 10px; }
.profile-menu a {
    padding: 12px 18px;
    border-radius: 14px;
    text-decoration: none;
    font-size: .78rem;
    letter-spacing: 1px;
    color: #333;
    transition: .3s;
}
.profile-menu a:hover { background: #f1f1f1; }
.profile-menu a.active { background: #000; color: #fff; }

/* CONTENT */
.profile-content {
    background: #fff;
    border-radius: 22px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
}

.section-title { font-family: 'Cinzel', serif; margin-bottom: 20px; }
.empty { padding: 60px; text-align: center; color: #777; font-size: .85rem; }

.logout-link {
    margin-top: 10px;
    padding: 12px 18px;
    border-radius: 14px;
    font-size: .78rem;
    letter-spacing: 1px;
    color: #b00020;
    text-decoration: none;
    border: 1px solid #f1c4cc;
    background: #fff5f7;
    transition: .3s;
    display: block;
    text-align: center;
}
.logout-link:hover { background: #b00020; color: #fff; border-color: #b00020; }

/* RESERVATION CARD */
.reservation-list { display: grid; gap: 20px; }
.reservation-card {
    border: 1px solid #e5e5e5;
    border-radius: 16px;
    padding: 20px;
    display: grid;
    grid-template-columns: 140px 1fr auto;
    gap: 20px;
    transition: .3s;
}
.reservation-card:hover { box-shadow: 0 8px 20px rgba(0,0,0,.1); }
.reservation-card img { width:140px;height:100px;object-fit:cover;border-radius:12px; }
.reservation-info h4 { margin:0 0 8px; font-size:1rem; }
.reservation-info p { margin:4px 0; font-size:.85rem; color:#666; }
.booking-code {
    display:inline-block;
    margin-top:8px;
    padding:6px 12px;
    background:#f8f9fa;
    border:1px dashed #d4af37;
    border-radius:8px;
    font-size:.75rem;
    font-weight:600;
    letter-spacing:2px;
}
.reservation-actions {
    display:flex;
    flex-direction:column;
    gap:10px;
    align-items:flex-end;
    justify-content:center;
}
.status-badge {
    padding:6px 14px;
    border-radius:20px;
    font-size:.75rem;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:1px;
}
.status-badge.paid { background:#d4edda;color:#155724; }
.status-badge.pending { background:#fff3cd;color:#856404; }
.status-badge.cancelled { background:#f8d7da;color:#721c24; }
.btn-detail {
    padding:10px 24px;
    background:#000;
    color:#fff;
    text-decoration:none;
    border-radius:25px;
    font-size:.8rem;
    transition:.3s;
}
.btn-detail:hover { background:#333; }
.swal2-popup { font-family:'Poppins',sans-serif; border-radius:20px; }
.swal2-title { font-family:'Cinzel',serif; color:#333; }
.swal2-confirm { background:#000 !important; border-radius:25px; padding:10px 30px !important; }
.swal2-cancel { border-radius:25px; padding:10px 30px !important; }
</style>

<div class="page-after-header">
    <div class="profile-container">

        <!-- SIDEBAR -->
        <aside class="profile-sidebar">
            <div class="profile-user">
                <div class="avatar"><?= strtoupper(substr($_SESSION['customer_name'],0,1)) ?></div>
                <strong><?= htmlspecialchars($_SESSION['customer_name']) ?></strong>
                <small>Velaris Guest</small>
            </div>

            <nav class="profile-menu">
                <a href="guest_profile.php?tab=profile" class="<?= $tab=='profile'?'active':'' ?>">Profile Information</a>
                <a href="guest_profile.php?tab=reservations" class="<?= $tab=='reservations'?'active':'' ?>">My Reservations</a>
                <a href="guest_profile.php?tab=cancellation" class="<?= $tab=='cancellation'?'active':'' ?>">Cancellation Requests</a>
                <a href="javascript:void(0)" class="logout-link" onclick="confirmLogout()">Logout</a>
            </nav>
        </aside>

        <!-- CONTENT -->
        <main class="profile-content">

        <?php if($tab=='profile'): ?>
            <h3 class="section-title">Profile Information</h3>
            <p style="font-size:.85rem;color:#666">Manage your personal information used for reservations.</p>

            <?php if($user): ?>
                <form class="checkin-form" style="max-width:500px; margin:auto; gap:15px;" method="post">
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" placeholder="Full Name" readonly>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" readonly>
                    <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>" placeholder="Phone Number" readonly>
                    <input type="password" name="password" value="<?= htmlspecialchars($user['password']) ?>" placeholder="Password" readonly>
                    <div class="empty" style="padding:10px; text-align:center; font-size:.75rem; color:#777;">Edit profile feature coming soon.</div>
                </form>
            <?php else: ?>
                <div class="empty">User data not found.</div>
            <?php endif; ?>

        <?php elseif($tab=='reservations'): ?>
            <!-- Reservations Tab Content (sama seperti sebelumnya) -->
            <h3 class="section-title">My Reservations</h3>
            <p style="font-size:.85rem;color:#666;margin-bottom:30px">View and manage all your hotel reservations.</p>

            <?php if(empty($reservations)): ?>
                <div class="empty">
                    <p>Anda belum memiliki reservasi.</p>
                    <a href="booking.php" style="color:#d4af37;text-decoration:none;font-weight:600">Buat Reservasi Sekarang →</a>
                </div>
            <?php else: ?>
                <div class="reservation-list">
                    <?php foreach($reservations as $res):
                        $checkin = new DateTime($res['tgl_checkin']);
                        $checkout = new DateTime($res['tgl_checkout']);
                        $nights = $checkout->diff($checkin)->days;

                        $status_class = '';
                        $status_label = '';

                        switch($res['status']){
                            case 'paid': $status_class='paid'; $status_label='Active'; break;
                            case 'cancelled_request': $status_class='pending'; $status_label='Cancellation Requested'; break;
                            case 'cancelled': $status_class='cancelled'; $status_label='Cancelled'; break;
                            default: $status_class='pending'; $status_label=ucfirst($res['status']);
                        }
                    ?>
                        <div class="reservation-card">
                            <img src="uploads/kamar/<?= htmlspecialchars($res['foto_kamar']) ?>" alt="<?= htmlspecialchars($res['nama_kamar']) ?>">
                            <div class="reservation-info">
                                <h4><?= htmlspecialchars($res['nama_kamar']) ?></h4>
                                <p><?= htmlspecialchars($res['tipe_kamar']) ?></p>
                                <div class="booking-code"><?= htmlspecialchars($res['kode_booking']) ?></div>
                                <p style="margin-top:12px">
                                    <strong>Check-in:</strong> <?= date('d M Y',strtotime($res['tgl_checkin'])) ?><br>
                                    <strong>Check-out:</strong> <?= date('d M Y',strtotime($res['tgl_checkout'])) ?><br>
                                    <strong>Nights:</strong> <?= $nights ?> malam
                                </p>
                                <p style="font-weight:600;color:#000;font-size:.95rem;margin-top:8px">
                                    IDR <?= number_format($res['total_harga'],0,',','.') ?>
                                </p>
                            </div>
                            <div class="reservation-actions">
                                <span class="status-badge <?= $status_class ?>"><?= $status_label ?></span>
                                <?php if($res['status']=='cancelled_request'): ?>
                                    <small style="color:#856404;font-size:.7rem;margin-top:4px;">Menunggu persetujuan admin</small>
                                <?php endif; ?>
                                <a href="booking_detail.php?id_reservasi=<?= $res['id_reservasi'] ?>" class="btn-detail">View Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php elseif($tab=='cancellation'): ?>
            <!-- Cancellation Tab Content (sama seperti sebelumnya) -->
            <h3 class="section-title">Cancellation Requests</h3>
            <?php if(empty($cancellations)): ?>
                <div class="empty">(Belum ada pengajuan pembatalan)</div>
            <?php else: ?>
                <div class="reservation-list">
                    <?php foreach($cancellations as $res):
                        $checkin = new DateTime($res['tgl_checkin']);
                        $checkout = new DateTime($res['tgl_checkout']);
                        $nights = $checkout->diff($checkin)->days;

                        $status_class = '';
                        $status_label = '';
                        switch($res['status_pengajuan']){
                            case 'pending': $status_class='pending'; $status_label='Pending'; break;
                            case 'disetujui': $status_class='paid'; $status_label='Approved'; break;
                            case 'ditolak': $status_class='cancelled'; $status_label='Rejected'; break;
                        }
                    ?>
                        <div class="reservation-card">
                            <img src="uploads/kamar/<?= htmlspecialchars($res['foto_kamar']) ?>" alt="<?= htmlspecialchars($res['nama_kamar']) ?>">
                            <div class="reservation-info">
                                <h4><?= htmlspecialchars($res['nama_kamar']) ?></h4>
                                <p><?= htmlspecialchars($res['tipe_kamar']) ?></p>
                                <div class="booking-code"><?= htmlspecialchars($res['kode_booking']) ?></div>
                                <p style="margin-top:12px">
                                    <strong>Check-in:</strong> <?= date('d M Y',strtotime($res['tgl_checkin'])) ?><br>
                                    <strong>Check-out:</strong> <?= date('d M Y',strtotime($res['tgl_checkout'])) ?><br>
                                    <strong>Nights:</strong> <?= $nights ?> malam
                                </p>
                                <p style="font-weight:600;color:#000;font-size:.95rem;margin-top:8px">
                                    IDR <?= number_format($res['total_harga'],0,',','.') ?>
                                </p>
                            </div>
                            <div class="reservation-actions">
                                <span class="status-badge <?= $status_class ?>"><?= $status_label ?></span>
                                <?php if($res['status_pengajuan']=='pending'): ?>
                                    <small style="color:#856404;font-size:.7rem;margin-top:4px;">Menunggu persetujuan admin</small>
                                <?php elseif($res['status_pengajuan']=='ditolak'): ?>
                                    <small style="color:#721c24;font-size:.7rem;margin-top:4px;">Pengajuan ditolak</small>
                                <?php endif; ?>
                                <a href="booking_detail.php?id_reservasi=<?= $res['id_reservasi'] ?>" class="btn-detail">View Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout() {
    Swal.fire({
        title: 'Confirm Logout',
        text: 'Are you sure you want to logout from your account?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#000',
        cancelButtonColor: '#999',
        reverseButtons: true
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = 'auth/logout.php';
        }
    });
}
</script>

<?php require_once "components/footer.php"; ?>
