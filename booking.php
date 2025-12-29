<?php
session_start();
require_once "config/database.php";
require_once "config/functions.php";

$page_title = 'Booking';

$db = new Koneksi();
$conn = $db->getKoneksi();

/**
 * GET PARAMETER
 */
$checkin  = $_GET['checkin']  ?? '';
$checkout = $_GET['checkout'] ?? '';

/**
 * AMBIL DATA KAMAR
 */
$rooms = $conn->query("SELECT * FROM kamar");

/* HEADER GLOBAL */
require_once "components/header.php";
?>

<style>
/* OFFSET KARENA HEADER GLOBAL FIXED */
.page-after-header{
    margin-top:90px;
    background:#f6f6f6;
}

/* BOOKING HEADER */
.booking-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:22px 40px;
    background:#fff;
    border-bottom:1px solid #eee;
}
.booking-header h3{
    font-family:'Cinzel',serif;
    margin:0;
}
.booking-header p{
    margin:2px 0 0;
    font-size:.8rem;
    color:#666;
}
.login-btn{
    padding:6px 18px;
    border:1px solid #ccc;
    border-radius:20px;
    text-decoration:none;
    font-size:.75rem;
    color:#333;
}

/* HERO */
.booking-hero img{
    width:100%;
    height:420px;
    object-fit:cover;
    display:block;
}

/* SEARCH BAR */
.search-bar{
    background:#fff;
    padding:26px 34px;
    max-width:1100px;
    margin:40px auto 60px;
    border-radius:18px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
}
.search-bar form{
    display:flex;
    gap:24px;
    align-items:flex-end;
    flex-wrap:wrap;
}
.search-bar .field{
    flex:1;
    min-width:220px;
}
.search-bar label{
    display:block;
    font-size:.75rem;
    letter-spacing:1px;
    margin-bottom:6px;
}
.search-bar input{
    width:100%;
    padding:12px 14px;
    border:1px solid #ccc;
    border-radius:10px;
}
.search-btn{
    height:46px;
    padding:0 36px;
    border:none;
    background:#000;
    color:#fff;
    border-radius:30px;
    font-size:.85rem;
    cursor:pointer;
}

/* CONTENT */
.booking-content{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:40px;
    padding:0 20px 100px;
}

/* ROOM CARD */
.room-card{
    background:#fff;
    border-radius:16px;
    overflow:hidden;
    margin-bottom:30px;
    display:flex;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}
.room-card img{
    width:240px;
    object-fit:cover;
}
.room-info{
    padding:22px;
    flex:1;
}
.room-info h3{
    font-family:'Cinzel',serif;
    margin-bottom:6px;
}
.room-info small{
    color:#999;
    font-size:.75rem;
}
.room-info p{
    font-size:.85rem;
    line-height:1.6;
}
.availability{
    font-size:.8rem;
    color:green;
}
.unavailable{
    font-size:.8rem;
    color:#a00;
}

/* ACTION */
.room-action{
    padding:22px;
    border-left:1px solid #eee;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    gap:14px;
}
.price{
    font-weight:600;
}
.btn{
    padding:8px 22px;
    border-radius:20px;
    border:1px solid #000;
    text-decoration:none;
    font-size:.75rem;
    color:#000;
}
.btn.disabled{
    opacity:.5;
    pointer-events:none;
}
</style>

<div class="page-after-header">

    <!-- BOOKING HEADER -->
    <div class="booking-header">
        <div>
            <h3>Hotel ★★★★★</h3>
            <p>Jl. Slamet Riyadi No.233, Purwosari, Kec. Laweyan, Surakarta, Jawa Tengah 57141</p>
        </div>

        <?php if (isset($_SESSION['customer_id'])): ?>
            👤 <?= htmlspecialchars($_SESSION['customer_name']); ?>
        <?php else: ?>
            <a href="auth/login.php" class="login-btn">Login</a>
        <?php endif; ?>
    </div>

    <!-- HERO -->
    <section class="booking-hero">
        <img src="uploads/experiences/pool.jpg" alt="Velaris Hotel">
    </section>

    <!-- SEARCH -->
    <section class="search-bar">
        <form method="GET">
            <div class="field">
                <label>Check in</label>
                <input type="date" name="checkin" value="<?= htmlspecialchars($checkin); ?>" required>
            </div>
            <div class="field">
                <label>Check out</label>
                <input type="date" name="checkout" value="<?= htmlspecialchars($checkout); ?>" required>
            </div>
            <button type="submit" class="search-btn">Search</button>
        </form>
    </section>

    <!-- CONTENT -->
    <section class="booking-content">

        <!-- LEFT -->
        <div>
        <?php while($r = $rooms->fetch_assoc()): ?>
            <div class="room-card">
                <img src="uploads/kamar/<?= htmlspecialchars($r['foto_kamar']) ?>">

                <div class="room-info">
                    <h3><?= htmlspecialchars($r['nama_kamar']) ?></h3>
                    <small><?= htmlspecialchars($r['tipe_kamar']) ?></small>
                    <p><?= substr(strip_tags($r['deskripsi']),0,140) ?>...</p>

                    <?php if ($r['stok'] > 0): ?>
                        <p class="availability">✔ Free cancellation <br> ✔ Book now, pay later</p>
                    <?php else: ?>
                        <p class="unavailable">Selected dates are unavailable</p>
                    <?php endif; ?>
                </div>

                <div class="room-action">
                    <p class="price">IDR <?= number_format($r['harga'],0,',','.') ?></p>

                    <?php if ($checkin && $checkout && $r['stok'] > 0): ?>
                        <?php if (!isset($_SESSION['customer_id'])): ?>
                            <a href="auth/login.php" class="btn">Select</a>
                        <?php else: ?>
                            <a href="reservasi.php?id_kamar=<?= $r['id_kamar'] ?>&checkin=<?= $checkin ?>&checkout=<?= $checkout ?>" class="btn">Select</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="btn disabled">
                            <?= $r['stok'] > 0 ? 'Select' : 'Find available dates' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        </div>

        <!-- RIGHT SUMMARY -->
        <div>
            <div class="room-card">
                <div class="room-info">
                    <strong><?= $checkin ?: 'Check-in date' ?> – <?= $checkout ?: 'Check-out date' ?></strong>
                    <p>1 room, 2 guests</p>
                </div>
                <div class="room-action">
                    <span class="btn disabled">Book</span>
                </div>
            </div>
        </div>

    </section>

</div>

<?php require_once "components/footer.php"; ?>
