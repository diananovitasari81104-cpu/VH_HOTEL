<?php
require_once '../config/database.php';
require_once '../config/functions.php';

require_staff();

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
    WHERE status IN ('lunas','selesai','paid')
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
    LIMIT 8
");

/* DATA CHART */
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

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* GLOBAL */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #333;
}

/* WRAPPER */
.admin-wrap {
    max-width: 1400px;
    margin: 0 auto;
    padding: 70px 30px 80px;
}

/* HEADER SECTION */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 50px;
    padding: 30px;
    background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
    border-radius: 24px;
    box-shadow: 0 15px 40px rgba(212, 175, 55, .25);
}

.dashboard-header h1 {
    font-family: 'Cinzel', serif;
    font-size: 2.2rem;
    font-weight: 700;
    color: #000;
    letter-spacing: 2px;
    margin: 0;
}

.dashboard-header p {
    color: rgba(0, 0, 0, .7);
    font-size: .95rem;
    margin-top: 5px;
}

.dashboard-date {
    text-align: right;
}

.dashboard-date .date {
    font-size: 1.1rem;
    font-weight: 600;
    color: #000;
}

.dashboard-date .time {
    font-size: .85rem;
    color: rgba(0, 0, 0, .7);
    margin-top: 3px;
}

/* STATS GRID */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 50px;
}

.stat-card {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    transition: .3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, .12);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, #d4af37 0%, #b8860b 100%);
}

.stat-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.stat-info h6 {
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: 1.5px;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.stat-info h3 {
    font-size: 2rem;
    font-weight: 700;
    color: #000;
    line-height: 1;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(212, 175, 55, .15) 0%, rgba(212, 175, 55, .05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #d4af37;
    font-size: 24px;
}

/* INFO CARDS */
.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 50px;
}

.info-card {
    background: linear-gradient(135deg, #fff 0%, #fafafa 100%);
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    border: 2px solid #f5f5f5;
    position: relative;
    overflow: hidden;
}

.info-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #d4af37 0%, #b8860b 100%);
}

.info-card h6 {
    font-size: .7rem;
    letter-spacing: 2px;
    color: #999;
    margin-bottom: 16px;
    text-transform: uppercase;
    font-weight: 600;
}

.info-card h3 {
    font-size: 2.2rem;
    font-weight: 700;
    color: #d4af37;
}

/* SECTION */
.section {
    margin-bottom: 50px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.section-title {
    font-family: 'Cinzel', serif;
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: 1px;
    color: #333;
}

.section-action {
    padding: 8px 20px;
    background: #fff;
    border: 2px solid #e5e5e5;
    border-radius: 25px;
    font-size: .8rem;
    font-weight: 600;
    color: #666;
    text-decoration: none;
    transition: .3s;
}

.section-action:hover {
    background: #d4af37;
    border-color: #d4af37;
    color: #000;
}

/* CHART BOX */
.chart-box {
    background: #fff;
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
}

/* TABLE */
.table-wrap {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
}

th {
    padding: 18px 20px;
    text-align: left;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #666;
    border-bottom: 2px solid #e5e5e5;
}

td {
    padding: 18px 20px;
    font-size: .85rem;
    color: #555;
    border-bottom: 1px solid #f5f5f5;
}

tbody tr {
    transition: .2s;
}

tbody tr:hover {
    background: #fafafa;
}

.reservation-id {
    font-weight: 700;
    color: #d4af37;
}

.guest-name {
    font-weight: 600;
    color: #333;
}

.room-info {
    font-size: .8rem;
}

.room-name {
    font-weight: 600;
    color: #333;
}

.room-type {
    color: #999;
    font-size: .75rem;
}

.status-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: .7rem;
    font-weight: 600;
    text-transform: capitalize;
    letter-spacing: .5px;
}

.status-paid,
.status-lunas {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-cancelled,
.status-batal {
    background: #f8d7da;
    color: #721c24;
}

.status-checkin {
    background: #d1ecf1;
    color: #0c5460;
}

/* EMPTY STATE */
.empty-state {
    padding: 60px;
    text-align: center;
    color: #999;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 20px;
    opacity: .3;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .admin-wrap {
        padding: 30px 20px;
    }
    
    .dashboard-header {
        flex-direction: column;
        text-align: center;
        padding: 25px;
    }
    
    .dashboard-header h1 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .dashboard-date {
        text-align: center;
        margin-top: 15px;
    }
    
    .stats-grid,
    .info-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .section-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .table-wrap {
        overflow-x: auto;
    }
    
    table {
        min-width: 800px;
    }
    
    th, td {
        padding: 14px 16px;
        font-size: .8rem;
    }
}

@media (max-width: 480px) {
    .dashboard-header h1 {
        font-size: 1.5rem;
    }
    
    .stat-info h3 {
        font-size: 1.6rem;
    }
    
    .info-card h3 {
        font-size: 1.8rem;
    }
}
</style>

<section class="admin-wrap">

    <!-- HEADER -->
    <div class="dashboard-header">
        <div>
            <h1>VELARIS DASHBOARD</h1>
            <p>Welcome back, Admin</p>
        </div>
        <div class="dashboard-date">
            <div class="date" id="currentDate"></div>
            <div class="time" id="currentTime"></div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h6>Total Users</h6>
                    <h3><?= number_format($total_users) ?></h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h6>Total Rooms</h6>
                    <h3><?= number_format($total_kamar) ?></h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-bed"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h6>Reservations</h6>
                    <h3><?= number_format($total_reservasi) ?></h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <h6>Pending Cancel</h6>
                    <h3><?= number_format($pending_pembatalan) ?></h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO CARDS -->
    <div class="info-grid">
        <div class="info-card">
            <h6>Total Revenue</h6>
            <h3><?= format_rupiah($total_revenue) ?></h3>
        </div>

        <div class="info-card">
            <h6>Today's Reservations</h6>
            <h3><?= number_format($today_reservations) ?></h3>
        </div>

        <div class="info-card">
            <h6>Average Rate</h6>
            <h3><?= format_rupiah($avg) ?></h3>
        </div>
    </div>

    <!-- CHART SECTION -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Reservation Trend</h2>
            <a href="reservasi/index.php" class="section-action">View All</a>
        </div>
        <div class="chart-box">
            <div id="reservasiChart" style="height:320px"></div>
        </div>
    </div>

    <!-- TABLE SECTION -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Recent Reservations</h2>
            <a href="reservasi/index.php" class="section-action">View All</a>
        </div>
        <div class="table-wrap">
            <?php if(empty($recent)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No reservations yet</p>
                </div>
            <?php else: ?>
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
                            <td class="reservation-id">#<?= str_pad($r['id_reservasi'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td class="guest-name"><?= htmlspecialchars($r['nama_lengkap']) ?></td>
                            <td class="room-info">
                                <div class="room-name"><?= htmlspecialchars($r['nama_kamar']) ?></div>
                                <div class="room-type"><?= htmlspecialchars($r['tipe_kamar']) ?></div>
                            </td>
                            <td><?= date('d M Y', strtotime($r['tgl_checkin'])) ?></td>
                            <td><?= date('d M Y', strtotime($r['tgl_checkout'])) ?></td>
                            <td><strong><?= format_rupiah($r['total_harga']) ?></strong></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace('_', '-', $r['status'])) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $r['status'])) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</section>

<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
// Real-time Clock
function updateTime() {
    const now = new Date();
    
    // Date
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', options);
    
    // Time
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
}

updateTime();
setInterval(updateTime, 1000);

// Chart
Highcharts.chart('reservasiChart', {
    chart: {
        type: 'areaspline',
        backgroundColor: 'transparent'
    },
    title: {
        text: null
    },
    xAxis: {
        categories: <?= json_encode($bulan) ?>,
        title: {
            text: 'Month'
        },
        gridLineWidth: 0
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Total Reservations'
        },
        gridLineColor: '#f5f5f5'
    },
    tooltip: {
        valueSuffix: ' reservations',
        backgroundColor: '#fff',
        borderColor: '#d4af37',
        borderRadius: 10,
        borderWidth: 2
    },
    plotOptions: {
        areaspline: {
            fillColor: {
                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                stops: [
                    [0, 'rgba(212, 175, 55, 0.3)'],
                    [1, 'rgba(212, 175, 55, 0.05)']
                ]
            },
            marker: {
                radius: 5,
                fillColor: '#d4af37',
                lineWidth: 2,
                lineColor: '#fff'
            },
            lineWidth: 3
        }
    },
    series: [{
        name: 'Reservations',
        data: <?= json_encode($total) ?>,
        color: '#d4af37'
    }],
    credits: {
        enabled: false
    },
    legend: {
        enabled: false
    }
});
</script>
<?php if (isset($_SESSION['login_success'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    title: 'Welcome Back',
    html: `
        <div style="margin-top:10px">
            Welcome to <strong>Velaris Admin Dashboard</strong><br>
            <span style="color:#d4af37;font-weight:600;">
                <?= htmlspecialchars($_SESSION['login_success']) ?>
            </span>
        </div>
    `,
    icon: 'success',
    confirmButtonColor: '#000',
    confirmButtonText: 'Enter Dashboard',
    timer: 3000,
    timerProgressBar: true
});
</script>
<?php unset($_SESSION['login_success']); endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>