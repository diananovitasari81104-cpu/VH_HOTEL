<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

$page_title = 'Velaris Blog';
require_once 'components/header.php';

// Ambil blog
$blogs = fetch_all("SELECT * FROM blog ORDER BY tgl_posting DESC");
?>

<!-- HERO BLOG -->
<section style="height:100vh; position:relative;">
    <img src="uploads/blog/blog3.jpg"
         style="width:100%; height:100%; object-fit:cover;">
</section>

<!-- BLOG LIST -->
<style>
.blog-wrapper{
    max-width:1200px;
    margin:80px auto;
    padding:0 20px;
}

.blog-title-page{
    font-family:'Cinzel',serif;
    text-align:center;
    font-size:2.4rem;
    margin-bottom:50px;
    letter-spacing:2px;
}

.blog-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:26px;
}

.blog-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 35px rgba(0,0,0,.08);
    transition:.35s ease;
}

.blog-card:hover{
    transform:translateY(-6px);
    box-shadow:0 22px 55px rgba(0,0,0,.15);
}

.blog-thumb{
    height:200px;
}

.blog-thumb img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.blog-body{
    padding:18px 20px 22px;
}

.blog-title{
    font-weight:600;
    margin-bottom:6px;
}

.blog-meta{
    font-size:.75rem;
    color:#888;
    margin-bottom:12px;
}

.blog-excerpt{
    font-size:.85rem;
    line-height:1.6;
    color:#555;
    margin-bottom:18px;
}

.blog-read{
    display:inline-block;
    padding:8px 22px;
    border-radius:30px;
    background:linear-gradient(135deg,#d4af37,#c9a633);
    color:#000;
    text-decoration:none;
    font-size:.75rem;
    letter-spacing:1px;
}
</style>

<section class="blog-wrapper">

    <h2 class="blog-title-page">Velaris Journal</h2>

    <div class="blog-grid">
        <?php foreach ($blogs as $b): ?>
        <div class="blog-card">

            <div class="blog-thumb">
                <?php if ($b['gambar']): ?>
                    <img src="uploads/blog/<?= htmlspecialchars($b['gambar']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x400?text=No+Image">
                <?php endif; ?>
            </div>

            <div class="blog-body">
                <div class="blog-title">
                    <?= htmlspecialchars($b['judul']); ?>
                </div>

                <div class="blog-meta">
                    <?= htmlspecialchars($b['penulis']); ?> •
                    <?= format_tanggal($b['tgl_posting'], 'd M Y'); ?>
                </div>

                <div class="blog-excerpt">
                    <?= substr(strip_tags($b['isi_konten']), 0, 120); ?>...
                </div>

                <a href="blog_detail.php?id=<?= $b['id_blog']; ?>" class="blog-read">
                    READ MORE
                </a>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

</section>

<?php require_once 'components/footer.php'; ?>
