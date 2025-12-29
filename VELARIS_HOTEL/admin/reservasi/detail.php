<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$id = (int)($_GET['id'] ?? 0);

$reservasi = fetch_single("
    SELECT 
        r.*,
        u.nama_lengkap,
        u.email,
        u.no_hp,
        k.nama_kamar,
        k.tipe_kamar,
        k.harga harga_kamar,
        k.foto_kamar
    FROM reservasi r
    JOIN users u ON r.id_user = u.id_user
    JOIN kamar k ON r.id_kamar = k.id_kamar
    WHERE r.id_reservasi = $id
");

if (!$reservasi) {
    redirect('index.php', 'Reservation not found', 'danger');
}

$experiences = fetch_all("
    SELECT e.nama_aktivitas, e.harga, re.jumlah
    FROM reservasi_experience re
    JOIN experiences e ON re.id_experience = e.id_experience
    WHERE re.id_reservasi = $id
");

$checkin  = new DateTime($reservasi['tgl_checkin']);
$checkout = new DateTime($reservasi['tgl_checkout']);
$nights   = $checkin->diff($checkout)->days;

require_once '../includes/header.php';
?>

<style>
.page-wrapper{
    padding:160px 20px 120px;
    background:linear-gradient(180deg,#fff 0%,#f6f6f6 100%);
}
.lux-container{max-width:1200px;margin:auto;}
.lux-card{
    background:#fff;
    border-radius:28px;
    box-shadow:0 30px 70px rgba(0,0,0,.08);
}
.lux-header{
    padding:36px 44px;
    font-family:'Cinzel',serif;
    font-size:1.8rem;
    letter-spacing:2px;
    border-bottom:1px solid #eee;
}
.lux-body{padding:44px;}
.section-title{
    font-size:.85rem;
    letter-spacing:1px;
    text-transform:uppercase;
    color:#888;
    margin-bottom:16px;
}
.room-image{
    border-radius:18px;
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}
.badge{
    padding:8px 16px;
    border-radius:50px;
    font-size:.75rem;
}
</style>

<div class="page-wrapper">
<div class="lux-container">

<div class="row g-4">

<!-- MAIN -->
<div class="col-lg-8">
<div class="lux-card">

<div class="lux-header">
    Reservation #<?= $reservasi['id_reservasi'] ?>
</div>

<div class="lux-body">

<div class="row mb-5">
    <div class="col-md-6">
        <div class="section-title">Guest Information</div>
        <p><strong><?= htmlspecialchars($reservasi['nama_lengkap']) ?></strong></p>
        <p><?= htmlspecialchars($reservasi['email']) ?></p>
        <p><?= htmlspecialchars($reservasi['no_hp']) ?></p>
    </div>
    <div class="col-md-6">
        <div class="section-title">Booking Details</div>
        <p>Check-in: <strong><?= format_tanggal($reservasi['tgl_checkin'],'d F Y') ?></strong></p>
        <p>Check-out: <strong><?= format_tanggal($reservasi['tgl_checkout'],'d F Y') ?></strong></p>
        <p><?= $nights ?> night(s)</p>
    </div>
</div>

<div class="section-title">Room</div>
<div class="row mb-5">
    <div class="col-md-4">
        <?php if ($reservasi['foto_kamar']): ?>
        <img src="../../uploads/kamar/<?= $reservasi['foto_kamar'] ?>" class="img-fluid room-image">
        <?php endif; ?>
    </div>
    <div class="col-md-8">
        <h4><?= htmlspecialchars($reservasi['nama_kamar']) ?></h4>
        <span class="badge bg-info"><?= htmlspecialchars($reservasi['tipe_kamar']) ?></span>
        <p class="mt-3">Qty: <?= $reservasi['jumlah_kamar'] ?> room(s)</p>
        <p>Price/night: <strong><?= format_rupiah($reservasi['harga_kamar']) ?></strong></p>
    </div>
</div>

<?php if ($experiences): ?>
<div class="section-title">Additional Experiences</div>
<table class="table">
<?php
$exp_total = 0;
foreach ($experiences as $e):
$sub = $e['harga'] * $e['jumlah'];
$exp_total += $sub;
?>
<tr>
    <td><?= htmlspecialchars($e['nama_aktivitas']) ?></td>
    <td><?= $e['jumlah'] ?>x</td>
    <td><?= format_rupiah($e['harga']) ?></td>
    <td class="text-end"><?= format_rupiah($sub) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<div class="row mt-5">
<div class="col-md-6 offset-md-6">
<table class="w-100">
<tr>
    <td>Room Subtotal</td>
    <td class="text-end">
        <?= format_rupiah($reservasi['harga_kamar'] * $nights * $reservasi['jumlah_kamar']) ?>
    </td>
</tr>
<?php if (!empty($exp_total)): ?>
<tr>
    <td>Experiences</td>
    <td class="text-end"><?= format_rupiah($exp_total) ?></td>
</tr>
<?php endif; ?>
<tr class="border-top">
    <td><strong>Total</strong></td>
    <td class="text-end"><h4><?= format_rupiah($reservasi['total_harga']) ?></h4></td>
</tr>
</table>
</div>
</div>

<?php if ($reservasi['bukti_bayar']): ?>
<div class="section-title mt-5">Payment Proof</div>
<img src="../../uploads/bukti_bayar/<?= $reservasi['bukti_bayar'] ?>"
     class="img-fluid rounded" style="max-width:420px">
<?php endif; ?>

<a href="index.php" class="btn btn-outline-dark mt-5">← Back</a>

</div>
</div>
</div>

<!-- STATUS -->
<div class="col-lg-4">
<div class="lux-card">
<div class="lux-header text-center">Status</div>
<div class="lux-body text-center">
<?php
$map = [
    'menunggu_bayar'=>'warning',
    'menunggu_verifikasi'=>'info',
    'lunas'=>'success',
    'batal'=>'danger',
    'selesai'=>'secondary'
];
?>
<span class="badge bg-<?= $map[$reservasi['status']] ?>">
    <?= ucfirst(str_replace('_',' ',$reservasi['status'])) ?>
</span>

<button class="btn btn-primary w-100 mt-4" onclick="openStatusModal()">
    Update Status
</button>
</div>
</div>
</div>

</div>
</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="statusModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title">Update Reservation Status</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form id="statusForm">
<input type="hidden" name="id" value="<?= $reservasi['id_reservasi'] ?>">
<select name="status" class="form-select" required>
    <option value="menunggu_bayar" <?= $reservasi['status']=='menunggu_bayar'?'selected':'' ?>>Waiting for Payment</option>
    <option value="menunggu_verifikasi" <?= $reservasi['status']=='menunggu_verifikasi'?'selected':'' ?>>Waiting for Verification</option>
    <option value="lunas" <?= $reservasi['status']=='lunas'?'selected':'' ?>>Paid</option>
    <option value="batal" <?= $reservasi['status']=='batal'?'selected':'' ?>>Cancelled</option>
    <option value="selesai" <?= $reservasi['status']=='selesai'?'selected':'' ?>>Completed</option>
</select>
</form>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary" onclick="saveStatus()">Save</button>
</div>
</div>
</div>
</div>

<script>
function openStatusModal(){
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function saveStatus(){
    $.ajax({
        url:'update_status.php',
        type:'POST',
        data:$('#statusForm').serialize(),
        dataType:'json',
        success:function(res){
            alert(res.message);
            if(res.success) location.reload();
        },
        error:function(){
            alert('Failed to update status');
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
