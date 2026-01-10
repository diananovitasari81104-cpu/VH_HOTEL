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
$edit_success = false;
$edit_error = '';

/* PROSES UPDATE PROFILE */
if(isset($_POST['update_profile'])){
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $no_hp = trim($_POST['no_hp']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);
    
    // Validasi
    if(empty($nama_lengkap) || empty($no_hp)){
        $edit_error = 'Nama lengkap dan nomor HP wajib diisi';
    } elseif(!empty($password_baru) && $password_baru !== $konfirmasi_password){
        $edit_error = 'Password baru dan konfirmasi password tidak cocok';
    } else {
        // Update data
        if(!empty($password_baru)){
            // Update dengan password baru
            $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, no_hp = ?, password = ? WHERE id_user = ?");
            $stmt->bind_param("sssi", $nama_lengkap, $no_hp, $password_baru, $customer_id);
        } else {
            // Update tanpa password
            $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, no_hp = ? WHERE id_user = ?");
            $stmt->bind_param("ssi", $nama_lengkap, $no_hp, $customer_id);
        }
        
        if($stmt->execute()){
            $edit_success = true;
            $_SESSION['customer_name'] = $nama_lengkap; // Update session
        } else {
            $edit_error = 'Gagal mengupdate profil';
        }
    }
}

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
        WHERE r.id_user = ?
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

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

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
    position: sticky;
    top: 110px; /* 90px header + 20px spacing */
    transition: top 0.3s ease;
}

.profile-user {
    text-align: center;
    margin-bottom: 30px;
}

.avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d4af37, #b8860b);
    color: #fff;
    font-size: 2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    box-shadow: 0 8px 20px rgba(212, 175, 55, .3);
}

.profile-user strong { 
    display: block; 
    font-size: 1rem;
    margin-bottom: 4px;
}
.profile-user small { 
    color: #888; 
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

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
.profile-menu a.active { 
    background: linear-gradient(135deg, #d4af37, #b8860b);
    color: #000;
    font-weight: 600;
}

/* CONTENT */
.profile-content {
    background: #fff;
    border-radius: 22px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
}

.section-title { 
    font-family: 'Cinzel', serif; 
    margin: 0 0 10px;
    font-size: 1.8rem;
}

.section-subtitle {
    font-size: .9rem;
    color: #666;
    margin-bottom: 30px;
}

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

/* PROFILE FORM */
.profile-form-container {
    max-width: 700px;
    margin: auto;
}

.profile-header-card {
    background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
    border-radius: 18px;
    padding: 30px;
    text-align: center;
    margin-bottom: 30px;
    color: #000;
}

.profile-header-card .avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(0, 0, 0, .2);
    color: #fff;
    font-size: 2.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .2);
}

.profile-header-card h3 {
    font-family: 'Cinzel', serif;
    margin: 0 0 5px;
    font-size: 1.5rem;
}

.profile-header-card p {
    margin: 0;
    font-size: .9rem;
    opacity: .9;
}

.form-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 18px;
    padding: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: .85rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    letter-spacing: .5px;
}

.form-group input {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    font-size: .9rem;
    transition: .3s;
    background: #fafafa;
}

.form-group input:focus {
    outline: none;
    border-color: #d4af37;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(212, 175, 55, .1);
}

.form-group input:disabled {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed;
}

.form-group .input-icon {
    position: relative;
}

.form-group .input-icon input {
    padding-right: 45px;
}

.form-group .toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #999;
    font-size: 1.2rem;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-save {
    flex: 1;
    padding: 14px;
    background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
    color: #000;
    border: none;
    border-radius: 30px;
    font-size: .95rem;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
    transition: .3s;
    box-shadow: 0 8px 20px rgba(212, 175, 55, .3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(212, 175, 55, .4);
}

.btn-cancel {
    flex: 1;
    padding: 14px;
    background: #fff;
    color: #666;
    border: 2px solid #e5e5e5;
    border-radius: 30px;
    font-size: .95rem;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
    transition: .3s;
}

.btn-cancel:hover {
    background: #f5f5f5;
    border-color: #ccc;
}

.info-note {
    background: #f8f9fa;
    border-left: 4px solid #d4af37;
    padding: 15px;
    border-radius: 8px;
    font-size: .85rem;
    color: #666;
    margin-bottom: 20px;
}

/* RESERVATION STYLES (keep existing) */
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

/* RESPONSIVE DESIGN */
@media (max-width: 992px) {
    .profile-container {
        grid-template-columns: 220px 1fr;
        gap: 30px;
    }
    
    .profile-sidebar {
        padding: 25px;
    }
    
    .avatar {
        width: 70px;
        height: 70px;
        font-size: 1.8rem;
    }
    
    .profile-content {
        padding: 30px;
    }
    
    .section-title {
        font-size: 1.6rem;
    }
}

@media (max-width: 768px) {
    .page-after-header {
        padding: 40px 15px;
    }
    
    .profile-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    /* SIDEBAR MOBILE */
    .profile-sidebar {
        position: relative;
        top: 0;
        padding: 20px;
    }
    
    .profile-user {
        display: flex;
        align-items: center;
        text-align: left;
        margin-bottom: 20px;
        gap: 15px;
    }
    
    .avatar {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
        margin: 0;
    }
    
    .profile-user strong {
        font-size: .9rem;
    }
    
    .profile-user small {
        font-size: .7rem;
    }
    
    /* MOBILE MENU - HORIZONTAL SCROLL */
    .profile-menu {
        flex-direction: row;
        overflow-x: auto;
        gap: 8px;
        padding-bottom: 5px;
        -webkit-overflow-scrolling: touch;
    }
    
    .profile-menu::-webkit-scrollbar {
        height: 4px;
    }
    
    .profile-menu::-webkit-scrollbar-thumb {
        background: #d4af37;
        border-radius: 4px;
    }
    
    .profile-menu a {
        padding: 10px 16px;
        font-size: .75rem;
        white-space: nowrap;
        border-radius: 12px;
    }
    
    .logout-link {
        margin-top: 0;
        padding: 10px 16px;
        font-size: .75rem;
        white-space: nowrap;
    }
    
    /* CONTENT MOBILE */
    .profile-content {
        padding: 25px 20px;
    }
    
    .section-title {
        font-size: 1.4rem;
    }
    
    .section-subtitle {
        font-size: .85rem;
    }
    
    /* PROFILE FORM MOBILE */
    .profile-header-card {
        padding: 25px 20px;
    }
    
    .profile-header-card .avatar-large {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .profile-header-card h3 {
        font-size: 1.3rem;
    }
    
    .form-card {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-save,
    .btn-cancel {
        width: 100%;
    }
    
    /* RESERVATION CARD MOBILE */
    .reservation-card {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 15px;
    }
    
    .reservation-card img {
        width: 100%;
        height: 180px;
    }
    
    .reservation-actions {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    
    .btn-detail {
        padding: 8px 20px;
        font-size: .75rem;
    }
}

@media (max-width: 480px) {
    .page-after-header {
        padding: 30px 10px;
    }
    
    .profile-sidebar {
        padding: 15px;
    }
    
    .profile-content {
        padding: 20px 15px;
    }
    
    .section-title {
        font-size: 1.2rem;
    }
    
    .profile-header-card {
        padding: 20px 15px;
    }
    
    .profile-header-card .avatar-large {
        width: 70px;
        height: 70px;
        font-size: 1.8rem;
    }
    
    .profile-header-card h3 {
        font-size: 1.1rem;
    }
    
    .form-card {
        padding: 15px;
    }
    
    .form-group input {
        padding: 12px 15px;
        font-size: .85rem;
    }
    
    .btn-save,
    .btn-cancel {
        padding: 12px;
        font-size: .85rem;
    }
    
    .info-note {
        font-size: .8rem;
        padding: 12px;
    }
    
    .reservation-card {
        padding: 12px;
    }
    
    .reservation-card img {
        height: 150px;
    }
    
    .reservation-info h4 {
        font-size: .9rem;
    }
    
    .reservation-info p {
        font-size: .8rem;
    }
    
    .booking-code {
        font-size: .7rem;
        padding: 5px 10px;
    }
    
    .status-badge {
        font-size: .7rem;
        padding: 5px 12px;
    }
}

/* LUXURY SWEETALERT */
.swal2-popup.luxury-alert {
    border-radius: 24px !important;
    padding: 40px !important;
}
.swal2-popup.luxury-alert .swal2-title {
    font-family: 'Cinzel', serif !important;
    font-size: 1.8rem !important;
}
.swal2-popup.luxury-alert .swal2-confirm {
    background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%) !important;
    border: none !important;
    border-radius: 30px !important;
    padding: 14px 40px !important;
    color: #000 !important;
    font-weight: 600 !important;
}
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
            <p class="section-subtitle">Manage your personal information and account settings</p>

            <?php if($user): ?>
                <div class="profile-form-container">
                    
                    <!-- PROFILE HEADER -->
                    <div class="profile-header-card">
                        <div class="avatar-large">
                            <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                        </div>
                        <h3><?= htmlspecialchars($user['nama_lengkap']) ?></h3>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                    </div>

                    <!-- EDIT FORM -->
                    <div class="form-card">
                        <form method="POST" id="profileForm">
                            
                            <div class="info-note">
                                ℹ️ <strong>Note:</strong> Email address cannot be changed. To update your password, fill in the new password fields below.
                            </div>

                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="nama_lengkap" 
                                       value="<?= htmlspecialchars($user['nama_lengkap']) ?>" 
                                       required placeholder="Enter your full name">
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" 
                                       disabled>
                            </div>

                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input type="text" name="no_hp" 
                                       value="<?= htmlspecialchars($user['no_hp']) ?>" 
                                       required placeholder="e.g., 08123456789">
                            </div>

                            <hr style="border:none;border-top:1px solid #e5e5e5;margin:30px 0;">

                            <div class="form-group">
                                <label>New Password (Leave blank if not changing)</label>
                                <div class="input-icon">
                                    <input type="password" id="password_baru" name="password_baru" 
                                           placeholder="Enter new password">
                                    <span class="toggle-password" onclick="togglePassword('password_baru')">
                                        👁️
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <div class="input-icon">
                                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" 
                                           placeholder="Confirm new password">
                                    <span class="toggle-password" onclick="togglePassword('konfirmasi_password')">
                                        👁️
                                    </span>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn-cancel" onclick="window.location.reload()">
                                    Cancel
                                </button>
                                <button type="submit" name="update_profile" class="btn-save">
                                    💾 Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            <?php else: ?>
                <div class="empty">User data not found.</div>
            <?php endif; ?>

        <?php elseif($tab=='reservations'): ?>
            <h3 class="section-title">My Reservations</h3>
            <p class="section-subtitle">View and manage all your hotel reservations</p>

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
            <h3 class="section-title">Cancellation Requests</h3>
            <p class="section-subtitle">Track your cancellation requests and refund status</p>

            <?php if(empty($cancellations)): ?>
                <div class="empty">No cancellation requests yet</div>
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
// Toggle Password Visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
    } else {
        field.type = 'password';
    }
}

// Success Alert
<?php if($edit_success): ?>
Swal.fire({
    customClass: { popup: 'luxury-alert' },
    icon: 'success',
    title: 'Profile Updated!',
    text: 'Your profile has been successfully updated.',
    confirmButtonText: 'OK',
    timer: 3000,
    timerProgressBar: true
});
<?php endif; ?>

// Error Alert
<?php if($edit_error): ?>
Swal.fire({
    customClass: { popup: 'luxury-alert' },
    icon: 'error',
    title: 'Update Failed',
    text: '<?= htmlspecialchars($edit_error) ?>',
    confirmButtonText: 'Try Again'
});
<?php endif; ?>

// Logout Confirmation
function confirmLogout() {
    Swal.fire({
        customClass: { popup: 'luxury-alert' },
        title: 'Confirm Logout',
        text: 'Are you sure you want to logout from your account?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d4af37',
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