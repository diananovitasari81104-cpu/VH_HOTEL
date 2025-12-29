<?php
session_start();
require_once "config/database.php";
require_once "config/functions.php";

$page_title = 'Rooms';

$db = new Koneksi();
$conn = $db->getKoneksi();

$kamar = $conn->query("SELECT * FROM kamar");
require_once "components/header.php";
?>

<!-- HERO -->
<section style="height:100vh; position:relative;">
    <img src="uploads/kamar/executivesuite.jpg"
         alt="Rooms Velaris Hotel"
         style="width:100%; height:100%; object-fit:cover;">
</section>

<style>
/* ROOMS PAGE STYLE  */
.rooms-section{
    padding:120px 0 80px;
    background:#f9f9f9;
}

.rooms-title{
    font-family:'Cinzel',serif;
    text-align:center;
    letter-spacing:2px;
    margin-bottom:60px;
}

.rooms-grid{
    max-width:1200px;
    margin:auto;
    padding:0 20px;
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:48px;
}

/* CARD */
.room-box{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 12px 40px rgba(0,0,0,.08);
    transition:.4s;
}
.room-box:hover{
    transform:translateY(-6px);
}

/* IMAGE */
.room-img{
    position:relative;
    height:300px;
}
.room-img img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:.5s;
}
.room-box:hover img{
    transform:scale(1.05);
}

/* BADGE TIPE */
.room-badge{
    position:absolute;
    top:16px;
    left:16px;
    background:#d4af37;
    color:#000;
    padding:6px 14px;
    font-size:.7rem;
    letter-spacing:1px;
    border-radius:20px;
    font-weight:600;
}

/* STOK */
.room-stock{
    position:absolute;
    top:16px;
    right:16px;
    background:rgba(0,0,0,.65);
    color:#fff;
    padding:6px 14px;
    font-size:.7rem;
    border-radius:20px;
}

/* RESERVE TOP */
.room-reserve-top{
    position:absolute;
    bottom:16px;
    left:50%;
    transform:translateX(-50%);
    border:1px solid #fff;
    padding:7px 22px;
    border-radius:30px;
    font-size:.7rem;
    letter-spacing:1px;
    color:#fff;
    text-decoration:none;
    background:rgba(0,0,0,.45);
}
.room-reserve-top:hover{
    background:#d4af37;
    border-color:#d4af37;
    color:#000;
}
.room-reserve-top.disabled{
    background:rgba(0,0,0,.6);
    pointer-events:none;
}

/* CONTENT */
.room-content{
    padding:28px;
}
.room-content h3{
    font-family:'Cinzel',serif;
    color:#d4af37;
    font-size:1.4rem;
    margin-bottom:12px;
}
.room-content p{
    font-size:.92rem;
    line-height:1.7;
    color:#555;
}
.room-price{
    margin:12px 0;
    font-weight:600;
    color:#333;
}

/* BUTTON BOTTOM */
.room-actions a{
    font-size:.7rem;
    letter-spacing:1px;
    padding:7px 18px;
    border-radius:20px;
    border:1px solid #d4af37;
    color:#d4af37;
    text-decoration:none;
}
.room-actions a:hover{
    background:#d4af37;
    color:#000;
}

/* RESPONSIVE */
@media(max-width:992px){
    .rooms-grid{
        grid-template-columns:1fr;
    }
}
</style>

<section class="rooms-section">

    <h2 class="rooms-title">ROOMS</h2>

    <div class="rooms-grid">

        <?php while($r = $kamar->fetch_assoc()): ?>
        <div class="room-box">

            <!-- IMAGE -->
            <div class="room-img">
                <img src="uploads/kamar/<?= htmlspecialchars($r['foto_kamar']) ?>"
                     alt="<?= htmlspecialchars($r['nama_kamar']) ?>">

                <div class="room-badge">
                    <?= strtoupper($r['tipe_kamar']) ?>
                </div>

                <div class="room-stock">
                    Stok: <?= (int)$r['stok'] ?>
                </div>

                <?php if($r['stok'] > 0): ?>
                <?php else: ?>
                    <div class="room-reserve-top disabled">
                        SOLD OUT
                    </div>
                <?php endif; ?>
            </div>

            <!-- CONTENT -->
            <div class="room-content">
                <h3><?= htmlspecialchars($r['nama_kamar']) ?></h3>

                <p>
                    <?= substr(strip_tags($r['deskripsi']),0,150) ?>...
                </p>

                <div class="room-price">
                    Rp <?= number_format($r['harga'],0,',','.') ?>/night
                </div>

                <div class="room-actions">
                    <?php if($r['stok'] > 0): ?>
                        <a href="booking.php?kamar=<?= $r['id_kamar'] ?>">
                            🔔 RESERVE NOW
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php endwhile; ?>

    </div>

</section>

<?php require_once "components/footer.php"; ?>
