<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_admin();

$page_title = 'Activity Log';

/* DATA */
$logs = fetch_all("
    SELECT 
        l.*,
        u.nama_lengkap,
        u.email,
        u.role
    FROM log_aktivitas l
    JOIN users u ON l.id_user = u.id_user
    ORDER BY l.waktu DESC
    LIMIT 500
");

$total_logs = fetch_single("SELECT COUNT(*) total FROM log_aktivitas")['total'];
$today_logs = fetch_single("SELECT COUNT(*) total FROM log_aktivitas WHERE DATE(waktu)=CURDATE()")['total'];

$logs_by_user = fetch_all("
    SELECT u.nama_lengkap, u.role, COUNT(*) total
    FROM log_aktivitas l
    JOIN users u ON l.id_user = u.id_user
    GROUP BY l.id_user
    ORDER BY total DESC
    LIMIT 5
");

require_once '../includes/header.php';
?>

<style>
/*LUXURY CONTENT – MATCH ADD ROOM */

.page-wrapper{
    padding:160px 20px 120px;
    background:linear-gradient(180deg,#ffffff 0%,#f6f6f6 100%);
}

.lux-container{
    max-width:1200px;
    margin:auto;
}

.stat-box{
    background:#fff;
    border-radius:22px;
    padding:32px;
    text-align:center;
    box-shadow:0 20px 45px rgba(0,0,0,.08);
}

.stat-box i{
    font-size:34px;
    color:#d4af37;
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

    <!-- STATS -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="stat-box">
                <i class="fas fa-history mb-2"></i>
                <h2><?= $total_logs ?></h2>
                <p class="text-muted mb-0">Total Activities</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box">
                <i class="fas fa-calendar-day mb-2"></i>
                <h2><?= $today_logs ?></h2>
                <p class="text-muted mb-0">Today's Activities</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box">
                <i class="fas fa-users mb-2"></i>
                <h2><?= count($logs_by_user) ?></h2>
                <p class="text-muted mb-0">Active Users</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- LOG TABLE -->
        <div class="col-lg-8">
            <div class="lux-card">
                <div class="lux-header">
                    Recent Activity Log
                </div>
                <div class="lux-body">
                    <div class="table-responsive">
                        <table id="logTable" class="table lux-table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Activity</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $log['id_log'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($log['nama_lengkap']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($log['email']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?=
                                            $log['role']=='admin'?'danger':
                                            ($log['role']=='staff'?'warning':'info')
                                        ?>">
                                            <?= ucfirst($log['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($log['aksi']) ?></td>
                                    <td>
                                        <?= format_tanggal($log['waktu'],'d M Y') ?><br>
                                        <small class="text-muted"><?= date('H:i:s',strtotime($log['waktu'])) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            <div class="lux-card mb-4">
                <div class="lux-header">Top Active Users</div>
                <div class="lux-body">
                    <?php foreach ($logs_by_user as $u): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <strong><?= htmlspecialchars($u['nama_lengkap']) ?></strong><br>
                                <span class="badge bg-<?=
                                    $u['role']=='admin'?'danger':
                                    ($u['role']=='staff'?'warning':'info')
                                ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </div>
                            <span class="badge bg-primary rounded-pill"><?= $u['total'] ?></span>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="lux-card">
                <div class="lux-header">About Activity Log</div>
                <div class="lux-body">
                    <p class="small text-muted">✔ Tracks all admin & staff actions</p>
                    <p class="small text-muted">✔ Stores last 500 activities</p>
                    <p class="small text-muted">✔ Admin only access</p>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<script>
$(function(){
    $('#logTable').DataTable({
        order:[[0,'desc']],
        pageLength:25
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
