<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get cancellation detail
$cancel = fetch_single("
    SELECT 
        p.*,
        r.tgl_checkin,
        r.tgl_checkout,
        r.total_harga,
        r.jumlah_kamar,
        u.nama_lengkap,
        u.email,
        u.no_hp,
        k.nama_kamar,
        k.tipe_kamar
    FROM pembatalan p
    JOIN reservasi r ON p.id_reservasi = r.id_reservasi
    JOIN users u ON r.id_user = u.id_user
    JOIN kamar k ON r.id_kamar = k.id_kamar
    WHERE p.id_batal = $id
");

if (!$cancel) {
    echo '<div class="alert alert-danger">Cancellation not found</div>';
    exit;
}
?>

<div class="row">
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Guest Information</h6>
        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($cancel['nama_lengkap']); ?></p>
        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($cancel['email']); ?></p>
        <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($cancel['no_hp']); ?></p>
    </div>
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Reservation Information</h6>
        <p class="mb-1"><strong>Reservation ID:</strong> #<?php echo $cancel['id_reservasi']; ?></p>
        <p class="mb-1"><strong>Room:</strong> <?php echo htmlspecialchars($cancel['nama_kamar']); ?> (<?php echo htmlspecialchars($cancel['tipe_kamar']); ?>)</p>
        <p class="mb-1"><strong>Check-in:</strong> <?php echo format_tanggal($cancel['tgl_checkin'], 'd M Y'); ?></p>
        <p class="mb-1"><strong>Check-out:</strong> <?php echo format_tanggal($cancel['tgl_checkout'], 'd M Y'); ?></p>
        <p class="mb-1"><strong>Quantity:</strong> <?php echo $cancel['jumlah_kamar']; ?> room(s)</p>
    </div>
</div>

<hr>

<h6 class="text-muted mb-3">Cancellation Details</h6>
<div class="mb-3">
    <strong>Request Date:</strong> <?php echo format_tanggal($cancel['tgl_pengajuan'], 'd F Y, H:i'); ?>
</div>

<div class="mb-3">
    <strong>Reason for Cancellation:</strong>
    <div class="alert alert-light mt-2">
        <?php echo nl2br(htmlspecialchars($cancel['alasan'])); ?>
    </div>
</div>

<hr>

<h6 class="text-muted mb-3">Refund Information</h6>
<div class="row">
    <div class="col-md-6">
        <p class="mb-1"><strong>Bank Name:</strong> <?php echo htmlspecialchars($cancel['nama_bank']); ?></p>
        <p class="mb-1"><strong>Account Number:</strong> <?php echo htmlspecialchars($cancel['no_rekening']); ?></p>
        <p class="mb-1"><strong>Account Holder:</strong> <?php echo htmlspecialchars($cancel['nama_pemilik']); ?></p>
    </div>
    <div class="col-md-6">
        <p class="mb-1"><strong>Refund Amount:</strong></p>
        <h4 class="text-success"><?php echo format_rupiah($cancel['total_harga']); ?></h4>
    </div>
</div>

<hr>

<h6 class="text-muted mb-3">Status</h6>
<div class="mb-3">
    <?php
    $badge_class = [
        'pending' => 'warning',
        'disetujui' => 'success',
        'ditolak' => 'danger'
    ];
    $class = $badge_class[$cancel['status_pengajuan']] ?? 'secondary';
    ?>
    <span class="badge bg-<?php echo $class; ?> fs-6"><?php echo ucfirst($cancel['status_pengajuan']); ?></span>
</div>

<?php if ($cancel['status_pengajuan'] != 'pending'): ?>
<div class="mb-3">
    <strong>Processed Date:</strong> 
    <?php echo $cancel['tgl_diproses'] ? format_tanggal($cancel['tgl_diproses'], 'd F Y, H:i') : '-'; ?>
</div>
<?php endif; ?>

<?php if ($cancel['catatan_admin']): ?>
<div class="mb-3">
    <strong>Admin Notes:</strong>
    <div class="alert alert-info mt-2">
        <?php echo nl2br(htmlspecialchars($cancel['catatan_admin'])); ?>
    </div>
</div>
<?php endif; ?>