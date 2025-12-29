<?php
require_once '../config/database.php';
require_once '../config/functions.php';

$page_title = 'Admin Dashboard';
require_once 'includes/header.php';


/*  DATA  */
$total_users = fetch_single("SELECT COUNT(*) total FROM users WHERE role='user'")['total'];
$total_kamar = fetch_single("SELECT COUNT(*) total FROM kamar")['total'];
$total_reservasi = fetch_single("SELECT COUNT(*) total FROM reservasi")['total'];
$pending_pembatalan = fetch_single("SELECT COUNT(*) total FROM pembatalan WHERE status_pengajuan='pending'")['total'];

$total_revenue = fetch_single("
    SELECT IFNULL(SUM(total_harga),0) total
    FROM reservasi
    WHERE status IN ('lunas','selesai')
")['total'];

$today_reservations = fetch_single("
    SELECT COUNT(*) total FROM reservasi WHERE DATE(created_at)=CURDATE()
")['total'];

$avg = $total_reservasi > 0 ? $total_revenue / $total_reservasi : 0;

$recent = fetch_all("
    SELECT r.*, u.nama_lengkap, k.nama_kamar, k.tipe_kamar
    FROM reservasi r
    JOIN users u ON r.id_user=u.id_user
    JOIN kamar k ON r.id_kamar=k.id_kamar
    ORDER BY r.created_at DESC
    LIMIT 5
");
/* =========================
   DATA CHART RESERVASI BULANAN
========================= */
$chart_data = fetch_all("
    SELECT 
        MONTH(created_at) bulan,
        COUNT(id_reservasi) total
    FROM reservasi
    WHERE status != 'batal'
    GROUP BY MONTH(created_at)
    ORDER BY bulan ASC
");

$bulan = [];
$total = [];

$nama_bulan = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
    5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
    9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
];

foreach ($chart_data as $d) {
    $bulan[] = $nama_bulan[$d['bulan']];
    $total[] = (int)$d['total'];
}

?>

<style>
/*  GLOBAL  */
body{
    background:linear-gradient(180deg,#f6f6f6 0%, #ffffff 60%);
}

/*  WRAPPER  */
.admin-wrap{
    max-width:1200px;
    margin:80px auto;
    padding:0 32px;
}

/*  TITLE  */
.admin-title{
    font-family:'Cinzel',serif;
    letter-spacing:3px;
    font-size:1.9rem;
    margin-bottom:60px;
    color:#111;
    position:relative;
}


/* STAT */
.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:24px;
    margin-bottom:60px;
}
.stat-card{
    background:linear-gradient(135deg,#ffffff,#faf7ef);
    border-radius:22px;
    padding:28px;
    display:flex;
    gap:18px;
    align-items:center;
    box-shadow:0 10px 28px rgba(0,0,0,.06);
    position:relative;
}
.stat-card::before{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:4px;
    background:#d4af37;
}
.stat-icon{
    width:46px;
    height:46px;
    border-radius:50%;
    background:#d4af37;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#000;
    font-size:18px;
}
.stat-card small{
    letter-spacing:1.5px;
    font-size:.65rem;
    color:#888;
}
.stat-card h4{
    margin:0;
    font-size:1.6rem;
    font-weight:500;
}

/* INFO  */
.info-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:24px;
    margin-bottom:80px;
}
.info-card{
    background:#fff;
    border-radius:26px;
    padding:42px 30px;
    text-align:center;
    box-shadow:0 10px 26px rgba(0,0,0,.06);
}
.info-card h6{
    font-size:.65rem;
    letter-spacing:2px;
    color:#999;
    margin-bottom:14px;
}
.info-card h3{
    font-size:1.9rem;
    font-weight:500;
}
.gold{ color:#d4af37; }

/* SECTION */
.section{
    margin-bottom:90px;
}
.section-title{
    font-family:'Cinzel',serif;
    letter-spacing:2px;
    font-size:1.1rem;
    margin-bottom:24px;
}

/* CHART */
.chart-box{
    background:#fff;
    border-radius:28px;
    padding:32px;
    box-shadow:0 10px 26px rgba(0,0,0,.06);
}

/* TABLE */
.table-wrap{
    background:#fff;
    border-radius:26px;
    overflow:hidden;
    box-shadow:0 10px 26px rgba(0,0,0,.06);
}
table{
    width:100%;
    border-collapse:collapse;
    font-size:.85rem;
}
th,td{
    padding:16px 18px;
    border-bottom:1px solid #eee;
}
th{
    font-weight:500;
    color:#555;
    background:#fafafa;
}
.status{
    font-size:.7rem;
    padding:5px 14px;
    border-radius:20px;
    background:#f1f1f1;
    text-transform:capitalize;
}

/* RESPONSIVE  */
@media(max-width:992px){
    .stats-grid,.info-grid{grid-template-columns:1fr 1fr;}
}
@media(max-width:576px){
    .stats-grid,.info-grid{grid-template-columns:1fr;}
}
</style>

<section class="admin-wrap">

    <h1 class="admin-title">ADMIN DASHBOARD</h1>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div>
                <small>TOTAL USERS</small>
                <h4><?= $total_users ?></h4>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-bed"></i></div>
            <div>
                <small>TOTAL ROOMS</small>
                <h4><?= $total_kamar ?></h4>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div>
                <small>RESERVATIONS</small>
                <h4><?= $total_reservasi ?></h4>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-times"></i></div>
            <div>
                <small>PENDING CANCEL</small>
                <h4><?= $pending_pembatalan ?></h4>
            </div>
        </div>
    </div>

    <!-- INFO -->
    <div class="info-grid">
        <div class="info-card">
            <h6>TOTAL REVENUE</h6>
            <h3 class="gold"><?= format_rupiah($total_revenue) ?></h3>
        </div>
        <div class="info-card">
            <h6>TODAY’S RESERVATIONS</h6>
            <h3><?= $today_reservations ?></h3>
        </div>
        <div class="info-card">
            <h6>AVERAGE RATE</h6>
            <h3><?= format_rupiah($avg) ?></h3>
        </div>
    </div>

    <!-- CHART -->
    <div class="section">
        <div class="section-title">RESERVATION TREND</div>
        <div class="chart-box">
            <div id="reservasiChart" style="height:280px"></div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="section">
        <div class="section-title">RECENT RESERVATIONS</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($recent as $r): ?>
                    <tr>
                        <td>#<?= $r['id_reservasi'] ?></td>
                        <td><?= htmlspecialchars($r['nama_lengkap']) ?></td>
                        <td><?= $r['nama_kamar'] ?> (<?= $r['tipe_kamar'] ?>)</td>
                        <td><?= format_tanggal($r['tgl_checkin'],'d M Y') ?></td>
                        <td><?= format_tanggal($r['tgl_checkout'],'d M Y') ?></td>
                        <td><?= format_rupiah($r['total_harga']) ?></td>
                        <td><span class="status"><?= str_replace('_',' ',$r['status']) ?></span></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

</section>

<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
Highcharts.chart('reservasiChart', {
    chart: {
        type: 'column'
    },
    title: {
        text: null
    },
    xAxis: {
        categories: <?= json_encode($bulan) ?>,
        title: {
            text: 'Bulan'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah Reservasi'
        }
    },
    tooltip: {
        valueSuffix: ' reservasi'
    },
    series: [{
        name: 'Reservasi',
        data: <?= json_encode($total) ?>,
        color: '#d4af37'
    }],
    credits: {
        enabled: false
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
