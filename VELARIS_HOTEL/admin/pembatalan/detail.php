<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
    redirect('index.php','Cancellation not found','danger');
}

$page_title = 'Cancellation Detail';
require_once '../includes/header.php';
?>

<style>
body{
    background:linear-gradient(180deg,#f6f6f6 0%,#ffffff 65%);
}

.detail-wrap{
    max-width:1100px;
    margin:140px auto 120px;
    padding:0 20px;
}

.detail-card{
    background:#fff;
    border-radius:30px;
    box-shadow:0 25px 60px rgba(0,0,0,.08);
    overflow:hidden;
}

.detail-header{
    padding:36px 40px;
    font-family:'Cinzel',serif;
    letter-spacing:2px;
    font-size:1.6rem;
    border-bottom:1px solid #eee;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.status-badge{
    font-size:.75rem;
    padding:8px 18px;
    border-radius:50px;
}

.detail-body{
    padding:46px 42px;
}

.section-title{
    font-size:.7rem;
    letter-spacing:2px;
    font-weight:600;
    color:#999;
    margin-bottom:14px;
}

.info-box{
    background:#fafafa;
    border-radius:20px;
    padding:20px 24px;
    margin-bottom:28px;
}

.info-box p{
    margin:0 0 8px;
    font-size:.9rem;
}

.info-box p:last-child{margin-bottom:0}

.reason-box,
.note-box{
    background:#fafafa;
    border-radius:20px;
    padding:22px 24px;
    font-size:.9rem;
    line-height:1.6;
}

.refund-amount{
    font-size:2.1rem;
    font-weight:600;
    color:#d4af37;
}

.divider{
    height:1px;
    background:#eee;
    margin:48px 0;
}

.btn-back{
    border-radius:20px;
    padding:10px 26px;
    font-size:.8rem;
    letter-spacing:1px;
}
</style>

<section class="detail-wrap">

<div class="detail-card">

    <!-- HEADER -->
    <div class="detail-header">
        <div>
            <i class="fas fa-ban me-2"></i>
            CANCELLATION DETAIL
        </div>

        <?php
        $badge = [
            'pending'=>'warning',
            'disetujui'=>'success',
            'ditolak'=>'danger'
        ];
        $cls = $badge[$cancel['status_pengajuan']] ?? 'secondary';
        ?>
        <span class="badge bg-<?= $cls ?> status-badge">
            <?= ucfirst($cancel['status_pengajuan']) ?>
        </span>
    </div>

    <div class="detail-body">

        <!-- GUEST & RESERVATION -->
        <div class="row">
            <div class="col-md-6">
                <div class="section-title">GUEST INFORMATION</div>
                <div class="info-box">
                    <p><strong>Name</strong><br><?= htmlspecialchars($cancel['nama_lengkap']) ?></p>
                    <p><strong>Email</strong><br><?= htmlspecialchars($cancel['email']) ?></p>
                    <p><strong>Phone</strong><br><?= htmlspecialchars($cancel['no_hp']) ?></p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="section-title">RESERVATION INFORMATION</div>
                <div class="info-box">
                    <p><strong>Reservation ID</strong><br>#<?= $cancel['id_reservasi'] ?></p>
                    <p><strong>Room</strong><br>
                        <?= htmlspecialchars($cancel['nama_kamar']) ?> (<?= htmlspecialchars($cancel['tipe_kamar']) ?>)
                    </p>
                    <p><strong>Check-in</strong><br><?= format_tanggal($cancel['tgl_checkin'],'d M Y') ?></p>
                    <p><strong>Check-out</strong><br><?= format_tanggal($cancel['tgl_checkout'],'d M Y') ?></p>
                    <p><strong>Quantity</strong><br><?= $cancel['jumlah_kamar'] ?> room(s)</p>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- CANCELLATION -->
        <div class="section-title">CANCELLATION DETAIL</div>
        <div class="info-box">
            <p><strong>Request Date</strong><br>
                <?= format_tanggal($cancel['tgl_pengajuan'],'d F Y, H:i') ?>
            </p>
        </div>

        <div class="section-title">REASON</div>
        <div class="reason-box mb-4">
            <?= nl2br(htmlspecialchars($cancel['alasan'])) ?>
        </div>

        <div class="divider"></div>

        <!-- REFUND -->
        <div class="section-title">REFUND INFORMATION</div>
        <div class="row">
            <div class="col-md-6">
                <div class="info-box">
                    <p><strong>Bank Name</strong><br><?= htmlspecialchars($cancel['nama_bank']) ?></p>
                    <p><strong>Account Number</strong><br><?= htmlspecialchars($cancel['no_rekening']) ?></p>
                    <p><strong>Account Holder</strong><br><?= htmlspecialchars($cancel['nama_pemilik']) ?></p>
                </div>
            </div>

            <div class="col-md-6 d-flex align-items-center">
                <div>
                    <div class="section-title">REFUND AMOUNT</div>
                    <div class="refund-amount">
                        <?= format_rupiah($cancel['total_harga']) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($cancel['status_pengajuan'] != 'pending'): ?>
        <div class="divider"></div>
        <div class="section-title">PROCESSED INFORMATION</div>
        <div class="info-box">
            <p><strong>Processed Date</strong><br>
                <?= $cancel['tgl_diproses']
                    ? format_tanggal($cancel['tgl_diproses'],'d F Y, H:i')
                    : '-' ?>
            </p>
        </div>
        <?php endif; ?>

        <?php if ($cancel['catatan_admin']): ?>
        <div class="section-title">ADMIN NOTES</div>
        <div class="note-box">
            <?= nl2br(htmlspecialchars($cancel['catatan_admin'])) ?>
        </div>
        <?php endif; ?>

        <!-- ACTION -->
        <div class="mt-5 text-end">
            <a href="index.php" class="btn btn-secondary btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

    </div>
</div>

</section>

<?php require_once '../includes/footer.php'; ?>
