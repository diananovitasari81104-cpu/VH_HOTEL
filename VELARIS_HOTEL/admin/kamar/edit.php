<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Ambil data kamar
$room = fetch_single("SELECT * FROM kamar WHERE id_kamar = $id");
if (!$room) {
    redirect('index.php', 'Room not found', 'danger');
}

/*  PROSES UPDATE (SEBELUM HTML)  */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_kamar = sanitize($_POST['nama_kamar']);
    $tipe_kamar = sanitize($_POST['tipe_kamar']);
    $harga      = sanitize($_POST['harga']);
    $deskripsi  = sanitize($_POST['deskripsi']);
    $stok       = (int) $_POST['stok'];

    if (empty($nama_kamar) || empty($tipe_kamar) || $harga === '' || $stok === '') {
        $error = 'All required fields must be filled';
    } elseif (!is_numeric($harga) || $harga < 0) {
        $error = 'Price must be a valid number';
    } elseif ($stok < 0) {
        $error = 'Stock must be a valid number';
    } else {

        $foto_kamar = $room['foto_kamar'];

        if (!empty($_FILES['foto_kamar']['name'])) {
            $upload = upload_file(
                $_FILES['foto_kamar'],
                '../../uploads/kamar/',
                ['jpg','jpeg','png','gif']
            );

            if ($upload['success']) {
                if ($foto_kamar && file_exists('../../uploads/kamar/'.$foto_kamar)) {
                    unlink('../../uploads/kamar/'.$foto_kamar);
                }
                $foto_kamar = $upload['filename'];
            } else {
                $error = $upload['message'];
            }
        }

        if (!$error) {
            $sql = "UPDATE kamar SET
                        nama_kamar = '".escape($nama_kamar)."',
                        tipe_kamar = '".escape($tipe_kamar)."',
                        harga = '".escape($harga)."',
                        deskripsi = '".escape($deskripsi)."',
                        foto_kamar = '".escape($foto_kamar)."',
                        stok = $stok
                    WHERE id_kamar = $id";

            if (execute($sql)) {
                redirect('index.php', 'Room updated successfully', 'success');
            } else {
                $error = 'Failed to update room';
            }
        }
    }
}

/* HTML  */
$page_title = 'Edit Room';
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
                🛏️ Edit Room
            </div>

            <div class="card-body">

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Room Name *</label>
                        <input type="text" name="nama_kamar" class="form-control" required
                               value="<?= htmlspecialchars($room['nama_kamar']) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Room Type *</label>
                            <select name="tipe_kamar" class="form-select" required>
                                <?php
                                $types = ['Standard','Deluxe','Suite','VIP'];
                                foreach ($types as $t):
                                ?>
                                    <option value="<?= $t ?>" <?= $room['tipe_kamar']===$t?'selected':'' ?>>
                                        <?= $t ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price per Night (Rp) *</label>
                            <input type="number" name="harga" class="form-control" min="0"
                                   value="<?= htmlspecialchars($room['harga']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock *</label>
                        <input type="number" name="stok" class="form-control" min="0"
                               value="<?= htmlspecialchars($room['stok']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="deskripsi" rows="4"
                                  class="form-control"><?= htmlspecialchars($room['deskripsi']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Photo</label><br>
                        <?php if ($room['foto_kamar']): ?>
                            <img src="../../uploads/kamar/<?= $room['foto_kamar'] ?>"
                                 class="img-thumbnail mb-2"
                                 style="max-width:200px">
                        <?php else: ?>
                            <p class="text-muted">No photo uploaded</p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Change Photo</label>
                        <input type="file" name="foto_kamar" class="form-control">
                        <small class="text-muted">Leave blank to keep current photo</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary px-4">
                            ← Back
                        </a>
                        <button class="btn btn-gold px-4">
                            💾 Update Room
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
