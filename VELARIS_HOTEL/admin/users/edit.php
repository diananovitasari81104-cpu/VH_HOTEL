<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_admin();

$page_title = 'Edit User';

/* ===== GET USER ===== */
$error = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$user = fetch_single("SELECT * FROM users WHERE id_user = $id");
if (!$user) {
    redirect('index.php','User not found','danger');
}

/* ===== UPDATE ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email        = sanitize($_POST['email']);
    $no_hp        = sanitize($_POST['no_hp']);
    $role         = sanitize($_POST['role']);
    $password     = $_POST['password'];

    if (!$nama_lengkap || !$email || !$no_hp || !$role) {
        $error = 'All fields are required';
    } else {
        $check = fetch_single("
            SELECT id_user FROM users 
            WHERE email='".escape($email)."' AND id_user!=$id
        ");
        if ($check) {
            $error = 'Email already exists';
        } else {
            $sql = "UPDATE users SET
                        nama_lengkap='".escape($nama_lengkap)."',
                        email='".escape($email)."',
                        no_hp='".escape($no_hp)."',
                        role='".escape($role)."'";

            if ($password) {
                if (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters';
                } else {
                    $sql .= ", password='".hash_password($password)."'";
                }
            }

            $sql .= " WHERE id_user=$id";

            if (!$error && execute($sql)) {
                log_activity("Updated user: $nama_lengkap (ID:$id)");
                redirect('index.php','User updated successfully','success');
            } elseif (!$error) {
                $error = 'Failed to update user';
            }
        }
    }
}

require_once '../includes/header.php';
?>

<style>
body{
    background:linear-gradient(180deg,#f6f6f6 0%, #ffffff 65%);
}

/* WRAPPER */
.edit-wrap{
    max-width:900px;
    margin:90px auto;
    padding:0 24px;
}

/* CARD */
.edit-card{
    border:none;
    border-radius:28px;
    box-shadow:0 14px 40px rgba(0,0,0,.08);
    overflow:hidden;
}

/* HEADER */
.edit-header{
    background:#fff;
    padding:28px 32px;
    border-bottom:1px solid #eee;
    font-family:'Cinzel',serif;
    letter-spacing:2px;
    font-size:1.1rem;
}

/* BODY */
.edit-body{
    padding:36px 32px 40px;
}

/* FORM */
.form-label{
    font-size:.8rem;
    letter-spacing:1px;
    font-weight:600;
}

.form-control,
.form-select{
    border-radius:14px;
    padding:12px 14px;
    font-size:.9rem;
}

.form-control:focus,
.form-select:focus{
    border-color:#d4af37;
    box-shadow:0 0 0 .15rem rgba(212,175,55,.25);
}

.helper-text{
    font-size:.75rem;
    color:#999;
}

/* BUTTON */
.btn-back{
    background:#6c757d;
    border:none;
    border-radius:14px;
    padding:10px 18px;
}

.btn-save{
    background:#d4af37;
    color:#000;
    border:none;
    border-radius:14px;
    padding:10px 22px;
    font-weight:600;
}
.btn-save:hover{
    background:#c5a030;
}
</style>

<section class="edit-wrap">

    <div class="card edit-card">

        <div class="edit-header">
            <i class="fas fa-user-edit me-2"></i>EDIT USER
        </div>

        <div class="edit-body">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-4">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="nama_lengkap" class="form-control" required
                           value="<?= htmlspecialchars($user['nama_lengkap']) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required
                           value="<?= htmlspecialchars($user['email']) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="no_hp" class="form-control" required
                           value="<?= htmlspecialchars($user['no_hp']) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select" required>
                        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                        <option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>Staff</option>
                        <option value="user"  <?= $user['role']=='user'?'selected':'' ?>>User</option>
                    </select>
                </div>

                <hr class="my-4">

                <div class="mb-5">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" minlength="6">
                    <div class="helper-text mt-1">
                        Leave blank to keep current password
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-1"></i>Update User
                    </button>
                </div>

            </form>

        </div>
    </div>

</section>

<?php require_once '../includes/footer.php'; ?>
