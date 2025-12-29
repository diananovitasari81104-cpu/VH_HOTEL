<?php
session_start();
require_once "config/database.php";
require_once "config/functions.php";

$page_title = 'Experience';

$db = new Koneksi();
$conn = $db->getKoneksi();

$experiences = $conn->query("SELECT * FROM experiences");
require_once "components/header.php";
?>

<!-- HERO -->
<section style="height:100vh; position:relative;">
    <img src="uploads/experiences/pool.jpg"
         alt="Experience Velaris Hotel"
         style="width:100%; height:100%; object-fit:cover;">
</section>

<style>
/*  EXPERIENCE PAGE  */
.exp-section{
    padding:100px 20px 80px;
    background:#fff;
}

.exp-title{
    text-align:center;
    font-family:'Cinzel',serif;
    letter-spacing:2px;
    font-size:2.4rem;
    margin-bottom:80px;
}

/* GRID */
.exp-grid{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:48px;
}

/* CARD */
.exp-card{
    text-align:left;
}

.exp-card img{
    width:100%;
    height:240px;
    object-fit:cover;
    border-radius:4px;
    margin-bottom:18px;
}

.exp-card h3{
    font-family:'Cinzel',serif;
    color:#d4af37;
    font-size:1.15rem;
    margin-bottom:10px;
}

.exp-card p{
    font-size:.9rem;
    line-height:1.7;
    color:#555;
}

/* GRID VARIATION */
.exp-grid.bottom{
    grid-template-columns:repeat(2,1fr);
    margin-top:80px;
}

/* RESPONSIVE */
@media(max-width:992px){
    .exp-grid,
    .exp-grid.bottom{
        grid-template-columns:1fr;
        gap:40px;
    }
}
</style>

<section class="exp-section">

    <h2 class="exp-title">ACTIVITIES</h2>

    <!-- GRID ATAS -->
    <div class="exp-grid">
        <?php
        $count = 0;
        while($exp = $experiences->fetch_assoc()):
            $count++;
            if($count > 6) break;
        ?>
        <div class="exp-card">
            <img src="uploads/experiences/<?= htmlspecialchars($exp['foto']) ?>"
                 alt="<?= htmlspecialchars($exp['nama_aktivitas']) ?>">

            <h3><?= htmlspecialchars($exp['nama_aktivitas']) ?></h3>
            <p><?= substr(strip_tags($exp['deskripsi']),0,140) ?>...</p>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- GRID BAWAH (2 COL) -->
    <?php
    $experiences->data_seek(6);
    if($experiences->num_rows > 6):
    ?>
    <div class="exp-grid bottom">
        <?php while($exp = $experiences->fetch_assoc()): ?>
        <div class="exp-card">
            <img src="uploads/experiences/<?= htmlspecialchars($exp['foto']) ?>"
                 alt="<?= htmlspecialchars($exp['nama_aktivitas']) ?>">

            <h3><?= htmlspecialchars($exp['nama_aktivitas']) ?></h3>
            <p><?= substr(strip_tags($exp['deskripsi']),0,160) ?>...</p>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

</section>

<?php require_once "components/footer.php"; ?>
