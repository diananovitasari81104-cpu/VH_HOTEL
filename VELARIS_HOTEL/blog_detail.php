<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

$page_title = 'Blog Detail';
require_once 'components/header.php';

/* AMBIL ID */
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='container py-5 text-center'>Artikel tidak ditemukan.</div>";
    require_once 'components/footer.php';
    exit;
}

$blog = fetch_single("SELECT * FROM blog WHERE id_blog = '$id'");
if (!$blog) {
    echo "<div class='container py-5 text-center'>Artikel tidak ditemukan.</div>";
    require_once 'components/footer.php';
    exit;
}
?>

<!-- HERO -->
<section class="blog-hero">
    <?php if ($blog['gambar']): ?>
        <img src="uploads/blog/<?= htmlspecialchars($blog['gambar']) ?>">
    <?php else: ?>
        <img src="https://via.placeholder.com/1600x700?text=Velaris+Hotel+Blog">
    <?php endif; ?>
    <div class="hero-overlay"></div>
</section>

<style>
.blog-hero{
    height:60vh;
    position:relative;
    overflow:hidden;
}
.blog-hero img{
    width:100%;
    height:100%;
    object-fit:cover;
}
.hero-overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(to bottom, rgba(0,0,0,.35), rgba(0,0,0,.65));
}

/* CONTENT */
.blog-wrapper{
    max-width:900px;
    margin:-120px auto 80px;
    background:#fff;
    border-radius:28px;
    padding:50px 40px;
    box-shadow:0 30px 80px rgba(0,0,0,.18);
    position:relative;
    z-index:2;
}

.blog-title{
    font-family:'Cinzel',serif;
    font-size:2.2rem;
    margin-bottom:10px;
}

.blog-meta{
    font-size:.85rem;
    color:#888;
    margin-bottom:28px;
}

.blog-content{
    font-size:1rem;
    line-height:1.9;
    color:#333;
}

/* BUTTON */
.btn-back{
    margin-top:40px;
    padding:10px 28px;
    border-radius:30px;
    border:1px solid #000;
    background:#fff;
    color:#000;
    font-weight:500;
    text-decoration:none;
    transition:.3s;
}
.btn-back:hover{
    background:#000;
    color:#fff;
}

/* RESPONSIVE */
@media(max-width:768px){
    .blog-wrapper{
        margin:-80px 16px 60px;
        padding:36px 24px;
    }
    .blog-title{
        font-size:1.7rem;
    }
}
</style>

<!-- CONTENT -->
<section class="container">

    <div class="blog-wrapper">

        <h1 class="blog-title">
            <?= htmlspecialchars($blog['judul']) ?>
        </h1>

        <div class="blog-meta">
            <?= htmlspecialchars($blog['penulis']) ?> •
            <?= format_tanggal($blog['tgl_posting'], 'd M Y') ?>
        </div>

        <div class="blog-content">
            <?= nl2br($blog['isi_konten']) ?>
        </div>
<br>
        <!-- BUTTON KEMBALI -->
        <div class="text-center">
            <a href="blog_guest.php" class="btn-back">
                ← Kembali ke Blog
            </a>
        </div>

    </div>

</section>

<?php require_once 'components/footer.php'; ?>
