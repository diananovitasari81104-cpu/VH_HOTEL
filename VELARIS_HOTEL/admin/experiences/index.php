<!-- hero kek di figma-->
<section style="
    height:100vh;
    position:relative;
">
    <img src="/uaspemweb/VELARIS_HOTEL/uploads/experiences/pool.jpg"
         style="
            width:100%;
            height:100%;
            object-fit:cover;
         ">
</section>

<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

$page_title = 'Experiences';
require_once '../includes/header.php';

require_staff();

// ambil data experiences
$experiences = fetch_all("SELECT * FROM experiences ORDER BY id_experience DESC");
?>

<style>
/* ACTIVITIES STYLE */
.activities-wrapper {
    max-width: 1200px;
    margin: 60px auto;
}

.activities-title {
    text-align: center;
    font-family: 'Cinzel', serif;
    font-size: 2.4rem;
    letter-spacing: 4px;
    margin-bottom: 60px;
}

.activity-card {
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    transition: 0.3s;
    height: 100%;
}

.activity-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

.activity-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.activity-body {
    padding: 18px;
}

.activity-name {
    font-family: 'Cinzel', serif;
    font-size: 1.1rem;
    color: #c9a227;
    margin-bottom: 8px;
}

.activity-desc {
    font-size: 0.85rem;
    color: #666;
    line-height: 1.6;
    min-height: 70px;
}

.activity-price {
    margin-top: 12px;
    font-size: 0.9rem;
    font-weight: 600;
}

.activity-price.free {
    color: #198754;
}

.activity-actions {
    margin-top: 14px;
    display: flex;
    gap: 10px;
}

.activity-actions a,
.activity-actions button {
    font-size: 0.75rem;
    padding: 6px 14px;
    border-radius: 20px;
    border: none;
    text-decoration: none;
}

.btn-edit {
    background: #ffc107;
    color: #000;
}

.btn-delete {
    background: #dc3545;
    color: #fff;
}

.btn-add {
    position: absolute;
    right: 0;
    top: -40px;
}
</style>

<div class="activities-wrapper position-relative">

    <h2 class="activities-title">ACTIVITIES</h2>

    <a href="create.php" class="btn btn-dark btn-sm btn-add">
        + Add New Experience
    </a>

    <div class="row g-4">

        <?php if (count($experiences) == 0): ?>
            <p class="text-center text-muted">No activities found.</p>
        <?php endif; ?>

        <?php foreach ($experiences as $exp): ?>
            <div class="col-md-4">
                <div class="activity-card">

                    <!-- IMAGE -->
                    <?php if ($exp['foto']): ?>
                        <img src="../../uploads/experiences/<?= htmlspecialchars($exp['foto']); ?>"
                             class="activity-img">
                    <?php else: ?>
                        <img src="../../assets/img/no-image.jpg" class="activity-img">
                    <?php endif; ?>

                    <!-- BODY -->
                    <div class="activity-body">

                        <div class="activity-name">
                            <?= htmlspecialchars($exp['nama_aktivitas']); ?>
                        </div>

                        <div class="activity-desc">
                            <?= strlen($exp['deskripsi']) > 100
                                ? substr(strip_tags($exp['deskripsi']),0,100).'...'
                                : strip_tags($exp['deskripsi']); ?>
                        </div>

                        <div class="activity-price <?= $exp['harga'] == 0 ? 'free' : '' ?>">
                            <?= $exp['harga'] > 0
                                ? format_rupiah($exp['harga'])
                                : 'Free Activity'; ?>
                        </div>

                        <div class="activity-actions">
                            <a href="edit.php?id=<?= $exp['id_experience']; ?>" class="btn-edit">
                                Edit
                            </a>
                            <button onclick="deleteExperience(<?= $exp['id_experience']; ?>)"
                                    class="btn-delete">
                                Delete
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<script>
function deleteExperience(id) {
    if (confirm('Delete this activity?')) {
        fetch('delete.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'id=' + id
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>