<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

require_staff();

$page_title = 'Cancellation Requests';

/* DATA */
$cancellations = fetch_all("
    SELECT 
        p.*,
        r.tgl_checkin,
        r.total_harga,
        u.nama_lengkap,
        u.email,
        k.nama_kamar
    FROM pembatalan p
    JOIN reservasi r ON p.id_reservasi = r.id_reservasi
    JOIN users u ON r.id_user = u.id_user
    JOIN kamar k ON r.id_kamar = k.id_kamar
    ORDER BY p.tgl_pengajuan DESC
");

$pending_count = 0;
foreach ($cancellations as $c) {
    if ($c['status_pengajuan'] === 'pending') $pending_count++;
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
/* LUXURY CONTENT – MATCH ADD ROOM */

.page-wrapper{
    padding:160px 20px 120px;
    background:linear-gradient(180deg,#ffffff 0%,#f6f6f6 100%);
}

.lux-container{
    max-width:1100px;
    margin:auto;
}

.lux-card{
    background:#fff;
    border-radius:26px;
    box-shadow:0 25px 60px rgba(0,0,0,.08);
    border:0;
}

.lux-header{
    padding:34px 40px;
    font-family:'Cinzel',serif;
    font-size:1.8rem;
    letter-spacing:2px;
    border-bottom:1px solid #eee;
}

.lux-body{
    padding:40px;
}

.stat-box{
    background:#fff;
    border-radius:22px;
    padding:32px;
    text-align:center;
    box-shadow:0 20px 45px rgba(0,0,0,.08);
}

.stat-box i{
    font-size:36px;
    color:#d4af37;
}

.lux-table thead th{
    text-transform:uppercase;
    font-size:.75rem;
    letter-spacing:1px;
    border-bottom:1px solid #eee;
}

.lux-table td{
    vertical-align:middle;
}

.badge{
    padding:8px 14px;
    border-radius:50px;
    font-size:.7rem;
}
</style>

<div class="page-wrapper">
    <div class="lux-container">

        <!-- STAT -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="stat-box">
                    <i class="fas fa-clock mb-2"></i>
                    <h2><?= $pending_count ?></h2>
                    <p class="text-muted mb-0">Pending Requests</p>
                </div>
            </div>
        </div>

        <!-- MAIN CARD -->
        <div class="lux-card">
            <div class="lux-header">
                Cancellation Requests
            </div>

            <div class="lux-body">
                <div class="table-responsive">
                    <table id="cancellationsTable" class="table lux-table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reservation</th>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Refund</th>
                                <th>Requested</th>
                                <th>Status</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cancellations as $c): ?>
                            <tr>
                                <td><strong>#<?= $c['id_batal'] ?></strong></td>
                                <td>#<?= $c['id_reservasi'] ?></td>
                                <td>
                                    <?= htmlspecialchars($c['nama_lengkap']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($c['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($c['nama_kamar']) ?></td>
                                <td><?= format_tanggal($c['tgl_checkin'], 'd M Y') ?></td>
                                <td><strong><?= format_rupiah($c['total_harga']) ?></strong></td>
                                <td><?= format_tanggal($c['tgl_pengajuan'], 'd M Y') ?></td>
                                <td>
                                    <span class="badge bg-<?=
                                        $c['status_pengajuan']=='pending'?'warning':
                                        ($c['status_pengajuan']=='disetujui'?'success':'danger')
                                    ?>">
                                        <?= ucfirst($c['status_pengajuan']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-dark">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <?php if ($c['status_pengajuan']=='pending'): ?>
                                        <button class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(function(){
    $('#cancellationsTable').DataTable({
        order:[[0,'desc']],
        pageLength:10
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
