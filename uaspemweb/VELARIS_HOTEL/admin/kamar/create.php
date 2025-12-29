<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

$page_title = 'Add New Room';
require_once '../includes/header.php';

require_staff();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kamar = sanitize($_POST['nama_kamar']);
    $tipe_kamar = sanitize($_POST['tipe_kamar']);
    $harga = sanitize($_POST['harga']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $stok = (int)$_POST['stok'];

    if (empty($nama_kamar) || empty($tipe_kamar) || empty($harga) || empty($stok)) {
        $error = 'All required fields must be filled';
    } else {
        $foto_kamar = '';

        if (!empty($_FILES['foto_kamar']['name'])) {
            $upload = upload_file(
                $_FILES['foto_kamar'],
                '../../uploads/kamar/',
                ['jpg','jpeg','png','gif']
            );

            if ($upload['success']) {
                $foto_kamar = $upload['filename'];
            } else {
                $error = $upload['message'];
            }
        }

        if (!$error) {
            $sql = "INSERT INTO kamar 
                    (nama_kamar, tipe_kamar, harga, deskripsi, foto_kamar, stok)
                    VALUES (
                        '".escape($nama_kamar)."',
                        '".escape($tipe_kamar)."',
                        '".escape($harga)."',
                        '".escape($deskripsi)."',
                        '".escape($foto_kamar)."',
                        $stok
                    )";

            if (insert($sql)) {
                redirect('index.php','Room added successfully','success');
            } else {
                $error = 'Failed to add room';
            }
        }
    }
}
?>

<style>
.room-modal-wrap{
    max-width: 820px;
    margin: 60px auto;
}

.room-modal-card{
    background:#fff;
    border-radius:22px;
    box-shadow:0 25px 70px rgba(0,0,0,.15);
    padding:32px;
}

.room-modal-title{
    font-family:'Cinzel',serif;
    font-size:22px;
    margin-bottom:24px;
}

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

.btn-gold:focus{
    box-shadow:0 0 0 .25rem rgba(212,175,55,.35);
}

</style>

<div class="room-modal-wrap">
    <div class="room-modal-card">

        <div class="room-modal-title">Add New Room</div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Room Name *</label>
                <input type="text" name="nama_kamar" class="form-control"
                       value="<?= htmlspecialchars($_POST['nama_kamar'] ?? '') ?>"
                       placeholder="e.g. Deluxe Ocean View 101">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Room Type *</label>
                    <select name="tipe_kamar" class="form-select">
                        <option value="">Select Type</option>
                        <?php
                        $types = ['Standard','Deluxe','Suite','VIP'];
                        foreach($types as $t):
                        ?>
                        <option value="<?= $t ?>" <?= ($_POST['tipe_kamar'] ?? '') === $t ? 'selected' : '' ?>>
                            <?= $t ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Price per Night (Rp) *</label>
                    <input type="number" name="harga" class="form-control"
                           value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>"
                           placeholder="350000">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Stock *</label>
                <input type="number" name="stok" class="form-control"
                       value="<?= htmlspecialchars($_POST['stok'] ?? '') ?>"
                       placeholder="5">
                <small class="text-muted">Number of available units</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="deskripsi" rows="4" class="form-control"
                          placeholder="Room facilities and description..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Room Photo</label>
                <input type="file" name="foto_kamar" class="form-control">
                <small class="text-muted">JPG, JPEG, PNG, GIF (max 5MB)</small>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">← Back</a>
                <button type="submit" class="btn btn-gold px-4">
                    <i class="fas fa-save me-1"></i>Save Room
                </button>

            </div>

        </form>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
