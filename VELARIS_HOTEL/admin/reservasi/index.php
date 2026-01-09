<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$page_title = 'Manage Reservations';

$reservations = fetch_all("
    SELECT 
        r.*,
        u.nama_lengkap,
        u.email,
        u.no_hp,
        k.nama_kamar,
        k.tipe_kamar
    FROM reservasi r
    JOIN users u ON r.id_user = u.id_user
    JOIN kamar k ON r.id_kamar = k.id_kamar
    ORDER BY r.created_at DESC
");

require_once '../includes/header.php';
?>

<style>
.page-wrapper{
    padding:160px 20px 120px;
    background:linear-gradient(180deg,#ffffff 0%,#f6f6f6 100%);
}

.lux-container{
    max-width:1200px;
    margin:auto;
}

.lux-card{
    background:#fff;
    border-radius:26px;
    box-shadow:0 25px 60px rgba(0,0,0,.08);
}

.lux-header{
    padding:34px 40px;
    font-family:'Cinzel',serif;
    font-size:1.6rem;
    letter-spacing:2px;
    border-bottom:1px solid #eee;
}

.lux-body{
    padding:36px;
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

    <div class="lux-card">
        <div class="lux-header">
            Reservation Management
        </div>

        <div class="lux-body">
            <div class="table-responsive">
                <table id="reservationsTable" class="table lux-table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><strong>#<?= $res['id_reservasi'] ?></strong></td>

                            <td>
                                <?= htmlspecialchars($res['nama_lengkap']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($res['email']) ?></small>
                            </td>

                            <td>
                                <?= htmlspecialchars($res['nama_kamar']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($res['tipe_kamar']) ?></small>
                            </td>

                            <td><?= format_tanggal($res['tgl_checkin'], 'd M Y') ?></td>
                            <td><?= format_tanggal($res['tgl_checkout'], 'd M Y') ?></td>

                            <td>
                                <span class="badge bg-info">
                                    <?= $res['jumlah_kamar'] ?> room(s)
                                </span>
                            </td>

                            <td>
                                <strong><?= format_rupiah($res['total_harga']) ?></strong>
                            </td>

                            <td>
                                <?php
                                $status_map = [
                                    'menunggu_bayar'      => 'warning',
                                    'menunggu_verifikasi' => 'info',
                                    'lunas'               => 'success',
                                    'cancelled_request'   => 'warning',
                                    'batal'               => 'danger',
                                    'selesai'             => 'secondary'
                                ];
                                $badge = $status_map[$res['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badge ?>">
                                    <?= ucfirst(str_replace('_',' ',$res['status'])) ?>
                                </span>
                            </td>

                            <td class="text-end">
                                <a href="detail.php?id=<?= $res['id_reservasi'] ?>"
                                   class="btn btn-sm btn-outline-dark"
                                   title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <?php if ($res['status'] === 'cancelled_request'): ?>
                                    <span class="badge bg-warning ms-2">
                                        Cancellation Requested
                                    </span>
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
document.addEventListener('DOMContentLoaded', function () {
    $('#reservationsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
