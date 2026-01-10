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
    padding:120px 20px 80px;
    background:#f5f6f8;
}

/* CARD */
.simple-card{
    background:#fff;
    border-radius:18px;
    box-shadow:0 10px 35px rgba(0,0,0,.08);
}

/* HEADER */
.simple-header{
    padding:24px 30px;
    border-bottom:1px solid #eee;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.simple-header h5{
    margin:0;
    font-weight:600;
}

/* BODY */
.simple-body{
    padding:24px;
}

/* TABLE */
.simple-table th{
    font-size:.75rem;
    text-transform:uppercase;
    letter-spacing:1px;
    color:#888;
    border-bottom:1px solid #eee;
}

.simple-table td{
    vertical-align:middle;
    font-size:.85rem;
}

/* ID */
.reservation-id {
    font-weight: 700;
    color: #d4af37 !important;
}

/* STATUS */
.badge-status{
    padding:6px 14px;
    border-radius:30px;
    font-size:.7rem;
    font-weight:600;
}

/* ACTION */
.btn-icon{
    padding:6px 10px;
    border-radius:10px;
}
</style>

<div class="page-wrapper">
<div class="container-fluid">

    <div class="simple-card">
        <div class="simple-header">
            <h5>Reservation Management</h5>
        </div>

        <div class="simple-body">
            <div class="table-responsive">
                <table id="reservationsTable"
                       class="table table-hover align-middle simple-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Booking Code</th>
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
                            <td class="reservation-id">
                                #<?= str_pad($res['id_reservasi'], 4, '0', STR_PAD_LEFT) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($res['nama_lengkap']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($res['email']) ?></small>
                            </td>

                            <td>
                                <?= htmlspecialchars($res['nama_kamar']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($res['tipe_kamar']) ?></small>
                            </td>

                            <td>
                                <strong style="letter-spacing:1px;">
                                    <?= htmlspecialchars($res['kode_booking']) ?>
                                </strong>
                            </td>

                            <td><?= format_tanggal($res['tgl_checkin'], 'd M Y') ?></td>
                            <td><?= format_tanggal($res['tgl_checkout'], 'd M Y') ?></td>

                            <td>
                                <span class="badge bg-light text-dark">
                                    <?= $res['jumlah_kamar'] ?> room
                                </span>
                            </td>

                            <td>
                                <strong><?= format_rupiah($res['total_harga']) ?></strong>
                            </td>

                            <td>
                                <?php
                                // Mapping status sesuai database
                                $status_map = [
                                    'menunggu_bayar'       => 'warning',
                                    'menunggu_verifikasi'  => 'info',
                                    'lunas'                => 'success',
                                    'pembatalan_diajukan'  => 'primary',
                                    'batal'                => 'danger',
                                    'selesai'              => 'secondary',
                                    'checkin'              => 'info'
                                ];
                                $badge = $status_map[$res['status']] ?? 'secondary';
                                ?>
                                <span class="badge badge-status bg-<?= $badge ?>">
                                    <?= ucfirst(str_replace('_',' ',$res['status'])) ?>
                                </span>
                            </td>

                            <td class="text-end">
                                <a href="detail.php?id=<?= $res['id_reservasi'] ?>"
                                class="btn btn-outline-dark btn-sm btn-icon">
                                    <i class="fas fa-eye"></i>
                                </a>
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
        pageLength: 10,
        lengthChange: false
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>