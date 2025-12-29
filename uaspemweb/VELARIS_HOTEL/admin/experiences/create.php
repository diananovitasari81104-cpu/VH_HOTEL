<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

/* AUTH CHECK — HARUS PALING ATAS */
require_staff();

$page_title = 'Add New Experience';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_aktivitas = sanitize($_POST['nama_aktivitas'] ?? '');
    $deskripsi      = sanitize($_POST['deskripsi'] ?? '');
    $harga          = sanitize($_POST['harga'] ?? '');

    if ($nama_aktivitas === '' || $harga === '') {
        $error = 'Activity name and price are required';
    } elseif (!is_numeric($harga) || (float)$harga < 0) {
        $error = 'Price must be a valid number';
    } else {
        $foto = '';

        if (!empty($_FILES['foto']['name'])) {
            $upload = upload_file(
                $_FILES['foto'],
                __DIR__ . '/../../uploads/experiences/',
                ['jpg','jpeg','png','gif']
            );

            if (!$upload['success']) {
                $error = $upload['message'];
            } else {
                $foto = $upload['filename'];
            }
        }

        if ($error === '') {
            $sql = "
                INSERT INTO experiences (nama_aktivitas, deskripsi, harga, foto)
                VALUES (
                    '".escape($nama_aktivitas)."',
                    '".escape($deskripsi)."',
                    '".escape($harga)."',
                    '".escape($foto)."'
                )
            ";

            if (insert($sql)) {
                log_activity("Added experience: {$nama_aktivitas}");
                redirect('index.php', 'Experience added successfully', 'success');
            }

            $error = 'Failed to save data';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5" style="margin-top:120px">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width:800px">
        <div class="card-header bg-white fw-bold text-uppercase">
            Add New Experience
        </div>

        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Activity Name *</label>
                    <input type="text" name="nama_aktivitas" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (Rp) *</label>
                    <input type="number" name="harga" min="0" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="deskripsi" class="form-control" rows="4"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Photo</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">← Back</a>
                    <button class="btn btn-warning fw-bold">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
