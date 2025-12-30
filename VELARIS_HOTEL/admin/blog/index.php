<!-- hero kek di figma-->
<section style="
    height:100vh;
    position:relative;
">
    <img src="/uaspemweb/VELARIS_HOTEL/uploads/blog/blog3.jpg"
         style="
            width:100%;
            height:100%;
            object-fit:cover;
         ">
</section>

<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$page_title = 'Blog Management';
require_once '../includes/header.php';

$blogs = fetch_all("SELECT * FROM blog ORDER BY tgl_posting DESC");
?>

<style>
/*  BLOG GRID STYLE  */
.blog-wrapper {
    max-width: 1200px;
    margin: 40px auto;
}

.blog-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.blog-header h3 {
    font-weight: 600;
}

.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
}

.blog-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    transition: transform .3s ease, box-shadow .3s ease;
}

.blog-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 45px rgba(0,0,0,0.12);
}

.blog-thumb {
    height: 200px;
    overflow: hidden;
}

.blog-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blog-body {
    padding: 18px;
}

.blog-title {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 6px;
}

.blog-meta {
    font-size: 0.75rem;
    color: #888;
    margin-bottom: 12px;
}

.blog-excerpt {
    font-size: 0.85rem;
    line-height: 1.6;
    color: #555;
}

.blog-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
}

.blog-actions a,
.blog-actions button {
    border: none;
    font-size: 0.75rem;
    padding: 6px 14px;
    border-radius: 20px;
    cursor: pointer;
}

.btn-edit {
    background: #ffc107;
    color: #000;
}

.btn-delete {
    background: #dc3545;
    color: #fff;
}
</style>

<div class="blog-wrapper">

    <div class="blog-header">
        <h3>Blog Articles</h3>
        <a href="create.php" class="btn btn-dark btn-sm">
            + Add New Article
        </a>
    </div>

    <?php if (count($blogs) === 0): ?>
        <p class="text-muted">No blog articles found.</p>
    <?php endif; ?>

    <div class="blog-grid">
        <?php foreach ($blogs as $b): ?>
        <div class="blog-card">

            <div class="blog-thumb">
                <?php if ($b['gambar']): ?>
                    <img src="../../uploads/blog/<?= htmlspecialchars($b['gambar']); ?>">
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

                

                <div class="blog-actions">
                    <a href="edit.php?id=<?= $b['id_blog']; ?>" class="btn-edit">Edit</a>
                    <button class="btn-delete"
                        onclick="deleteBlog(<?= $b['id_blog']; ?>)">
                        Delete
                    </button>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

</div>

<script>
function deleteBlog(id) {
    if (!confirm('Delete this article?')) return;

    fetch('delete.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.success) location.reload();
    })
    .catch(() => alert('Error deleting article'));
}
</script>


<?php require_once '../includes/footer.php'; ?>