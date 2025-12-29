<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

/* LOAD CONFIG */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

/* AUTH CHECK — WAJIB PALING ATAS */
require_staff();

$page_title = 'Add New Article';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul      = sanitize($_POST['judul'] ?? '');
    $isi_konten = $_POST['isi_konten'] ?? ''; // HTML allowed
    $penulis    = sanitize($_POST['penulis'] ?? '');

    if ($judul === '' || $isi_konten === '' || $penulis === '') {
        $error = 'All fields are required';
    } else {
        $gambar = '';

        if (!empty($_FILES['gambar']['name'])) {
            $upload = upload_file(
                $_FILES['gambar'],
                __DIR__ . '/../../uploads/blog/',
                ['jpg','jpeg','png','gif']
            );

            if (!$upload['success']) {
                $error = $upload['message'];
            } else {
                $gambar = $upload['filename'];
            }
        }

        if ($error === '') {
            $sql = "
                INSERT INTO blog (judul, isi_konten, gambar, penulis)
                VALUES (
                    '".escape($judul)."',
                    '".escape($isi_konten)."',
                    '".escape($gambar)."',
                    '".escape($penulis)."'
                )
            ";

            if (insert($sql)) {
                log_activity("Added blog article: {$judul}");
                redirect('index.php', 'Article added successfully', 'success');
            }

            $error = 'Failed to save article';
        }
    }
}

/* BARU LOAD HEADER SETELAH AUTH & LOGIC */
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5" style="margin-top:120px">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width:900px">
        <div class="card-header bg-white fw-bold text-uppercase">
            Add New Article
        </div>

        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Article Title *</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Author *</label>
                    <input type="text"
                           name="penulis"
                           class="form-control"
                           value="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Thumbnail</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>

                <div class="mb-4">
                    <label class="form-label">Content *</label>
                    <textarea name="isi_konten"
                              class="form-control"
                              rows="12"
                              required></textarea>
                    <small class="text-muted">HTML allowed</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">← Back</a>
                    <button class="btn btn-warning fw-bold">
                        Publish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
