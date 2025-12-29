<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Ambil data experience
$experience = fetch_single("SELECT * FROM experiences WHERE id_experience = $id");
if (!$experience) {
    redirect('index.php', 'Experience not found', 'danger');
}

/* ========= PROSES UPDATE (SEBELUM HTML) ========= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_aktivitas = sanitize($_POST['nama_aktivitas']);
    $deskripsi      = sanitize($_POST['deskripsi']);
    $harga          = sanitize($_POST['harga']);

    if (empty($nama_aktivitas) || $harga === '') {
        $error = 'Activity name and price are required';
    } elseif (!is_numeric($harga) || $harga < 0) {
        $error = 'Price must be a valid number';
    } else {

        $foto = $experience['foto'];

        if (!empty($_FILES['foto']['name'])) {
            $upload = upload_file(
                $_FILES['foto'],
                '../../uploads/experiences/',
                ['jpg','jpeg','png','gif']
            );

            if ($upload['success']) {
                if ($foto && file_exists('../../uploads/experiences/'.$foto)) {
                    unlink('../../uploads/experiences/'.$foto);
                }
                $foto = $upload['filename'];
            } else {
                $error = $upload['message'];
            }
        }

        if (!$error) {
            $sql = "UPDATE experiences SET
                        nama_aktivitas = '".escape($nama_aktivitas)."',
                        deskripsi = '".escape($deskripsi)."',
                        harga = '".escape($harga)."',
                        foto = '".escape($foto)."'
                    WHERE id_experience = $id";

            if (execute($sql)) {
                redirect('index.php', 'Experience updated successfully', 'success');
            } else {
                $error = 'Failed to update experience';
            }
        }
    }
}

/*  HTML BARU  */
$page_title = 'Edit Experience';
require_once '../includes/header.php';
?>

<style>
.btn-gold{
    background:#d4af37;
    border:none;
    color:#000;
    font-weight:600;
}
.btn-gold:hover{
    background:#c9a52f;
    color:#000;
}
</style>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-header bg-white fw-semibold">
                ✨ Edit Experience
            </div>

            <div class="card-body">

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Activity Name *</label>
                        <input type="text" name="nama_aktivitas"
                               class="form-control" required
                               value="<?= htmlspecialchars($experience['nama_aktivitas']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (Rp) *</label>
                        <input type="number" name="harga"
                               class="form-control" min="0"
                               value="<?= htmlspecialchars($experience['harga']) ?>">
                        <small class="text-muted">Set to 0 for free activities</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="deskripsi" rows="4"
                                  class="form-control"><?= htmlspecialchars($experience['deskripsi']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Photo</label><br>
                        <?php if ($experience['foto']): ?>
                            <img src="../../uploads/experiences/<?= $experience['foto'] ?>"
                                 class="img-thumbnail mb-2"
                                 style="max-width:200px">
                        <?php else: ?>
                            <p class="text-muted">No photo uploaded</p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Change Photo</label>
                        <input type="file" name="foto" class="form-control">
                        <small class="text-muted">Leave blank to keep current photo</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary px-4">
                            ← Back
                        </a>
                        <button class="btn btn-gold px-4">
                            💾 Update Experience
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
