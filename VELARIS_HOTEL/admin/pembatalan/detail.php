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
    background:linear-gradient(180deg,#f6f6f6 0%, #ffffff 65%);
}

.detail-wrap{
    max-width:1000px;
    margin:80px auto;
    padding:0 20px;
}

.detail-card{
    border:none;
    border-radius:28px;
    box-shadow:0 14px 40px rgba(0,0,0,.08);
}

.detail-header{
    padding:28px 32px;
    border-bottom:1px solid #eee;
    font-family:'Cinzel',serif;
    letter-spacing:2px;
    font-size:1.1rem;
}

.detail-body{
    padding:36px 32px 40px;
}

.section-title{
    font-size:.7rem;
    letter-spacing:2px;
    font-weight:600;
    color:#999;
    margin-bottom:16px;
}

.info-box{
    background:#fafafa;
    border-radius:18px;
    padding:18px 20px;
    margin-bottom:24px;
}

.info-box p{
    margin-bottom:6px;
    font-size:.9rem;
}

.reason-box,
.note-box{
    background:#fafafa;
    border-radius:18px;
    padding:20px;
    font-size:.9rem;
}

.refund-amount{
    font-size:1.8rem;
    font-weight:600;
}

.status-badge{
    font-size:.75rem;
    padding:8px 18px;
    border-radius:20px;
}

.btn-back{
    background:#6c757d;
    border:none;
    border-radius:14px;
    padding:10px 18px;
}
</style>

<section class="detail-wrap">

    <div class="card detail-card">

        <div class="detail-header">
            <i class="fas fa-ban me-2"></i>CANCELLATION DETAIL
        </div>

        <div class="detail-body">

            <!-- GUEST & RESERVATION -->
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title">GUEST INFORMATION</div>
                    <div class="info-box">
                        <p><strong>Name:</strong> <?= htmlspecialchars($cancel['nama_lengkap']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($cancel['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($cancel['no_hp']) ?></p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="section-title">RESERVATION INFORMATION</div>
                    <div class="info-box">
                        <p><strong>Reservation ID:</strong> #<?= $cancel['id_reservasi'] ?></p>
                        <p><strong>Room:</strong> <?= htmlspecialchars($cancel['nama_kamar']) ?> (<?= htmlspecialchars($cancel['tipe_kamar']) ?>)</p>
                        <p><strong>Check-in:</strong> <?= format_tanggal($cancel['tgl_checkin'],'d M Y') ?></p>
                        <p><strong>Check-out:</strong> <?= format_tanggal($cancel['tgl_checkout'],'d M Y') ?></p>
                        <p><strong>Quantity:</strong> <?= $cancel['jumlah_kamar'] ?> room(s)</p>
                    </div>
                </div>
            </div>

            <!-- CANCELLATION -->
            <div class="section-title mt-4">CANCELLATION DETAIL</div>
            <div class="info-box">
                <p><strong>Request Date:</strong>
                    <?= format_tanggal($cancel['tgl_pengajuan'],'d F Y, H:i') ?>
                </p>
            </div>

            <div class="section-title">REASON</div>
            <div class="reason-box mb-4">
                <?= nl2br(htmlspecialchars($cancel['alasan'])) ?>
            </div>

            <!-- REFUND -->
            <div class="section-title">REFUND INFORMATION</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-box">
                        <p><strong>Bank Name:</strong> <?= htmlspecialchars($cancel['nama_bank']) ?></p>
                        <p><strong>Account Number:</strong> <?= htmlspecialchars($cancel['no_rekening']) ?></p>
                        <p><strong>Account Holder:</strong> <?= htmlspecialchars($cancel['nama_pemilik']) ?></p>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div>
                        <div class="section-title mb-2">REFUND AMOUNT</div>
                        <div class="refund-amount text-success">
                            <?= format_rupiah($cancel['total_harga']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATUS -->
            <div class="section-title mt-4">STATUS</div>
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

            <?php if ($cancel['status_pengajuan'] != 'pending'): ?>
            <div class="info-box mt-3">
                <p><strong>Processed Date:</strong>
                    <?= $cancel['tgl_diproses']
                        ? format_tanggal($cancel['tgl_diproses'],'d F Y, H:i')
                        : '-' ?>
                </p>
            </div>
            <?php endif; ?>

            <?php if ($cancel['catatan_admin']): ?>
            <div class="section-title mt-3">ADMIN NOTES</div>
            <div class="note-box">
                <?= nl2br(htmlspecialchars($cancel['catatan_admin'])) ?>
            </div>
            <?php endif; ?>

            <!-- ACTION -->
            <div class="mt-5 text-end">
                <a href="index.php" class="btn btn-back">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>

        </div>
    </div>

</section>

<?php require_once '../includes/footer.php'; ?>
