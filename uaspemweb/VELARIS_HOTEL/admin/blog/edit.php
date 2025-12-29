<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Ambil data blog
$blog = fetch_single("SELECT * FROM blog WHERE id_blog = $id");
if (!$blog) {
    redirect('index.php', 'Article not found', 'danger');
}

// PROSES UPDATE (SEBELUM HTML) //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul       = sanitize($_POST['judul']);
    $isi_konten  = $_POST['isi_konten'];
    $penulis     = sanitize($_POST['penulis']);

    if (empty($judul) || empty($isi_konten) || empty($penulis)) {
        $error = 'All fields are required';
    } else {

        $gambar = $blog['gambar'];

        if (!empty($_FILES['gambar']['name'])) {
            $upload = upload_file(
                $_FILES['gambar'],
                '../../uploads/blog/',
                ['jpg','jpeg','png','gif']
            );

            if ($upload['success']) {
                if ($gambar && file_exists('../../uploads/blog/'.$gambar)) {
                    unlink('../../uploads/blog/'.$gambar);
                }
                $gambar = $upload['filename'];
            } else {
                $error = $upload['message'];
            }
        }

        if (!$error) {
            $sql = "UPDATE blog SET
                        judul = '".escape($judul)."',
                        isi_konten = '".escape($isi_konten)."',
                        gambar = '".escape($gambar)."',
                        penulis = '".escape($penulis)."'
                    WHERE id_blog = $id";

            if (execute($sql)) {
                redirect('index.php', 'Article updated successfully', 'success');
            } else {
                $error = 'Failed to update article';
            }
        }
    }
}

$page_title = 'Edit Article';
require_once '../includes/header.php';
?>

<style>
.btn-gold{
    background:#d4af37;
    border:none;
    color:#000;
    font-weight:600;
}
.btn-gold:hover{ background:#c9a52f }
</style>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white fw-semibold">
                ✏️ Edit Article
            </div>

            <div class="card-body">

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Article Title *</label>
                        <input type="text" name="judul" class="form-control"
                               value="<?= htmlspecialchars($blog['judul']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Author *</label>
                        <input type="text" name="penulis" class="form-control"
                               value="<?= htmlspecialchars($blog['penulis']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Thumbnail</label><br>
                        <?php if ($blog['gambar']): ?>
                            <img src="../../uploads/blog/<?= $blog['gambar'] ?>"
                                 class="img-thumbnail mb-2" style="max-width:200px">
                        <?php else: ?>
                            <p class="text-muted">No image</p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Change Thumbnail</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Content *</label>
                        <textarea name="isi_konten" rows="10"
                                  class="form-control" required><?= htmlspecialchars($blog['isi_konten']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary px-4">← Back</a>
                        <button class="btn btn-gold px-4">
                            💾 Update Article
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
