<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_admin();

$page_title = 'Add New User';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap     = sanitize($_POST['nama_lengkap']);
    $email            = sanitize($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $no_hp            = sanitize($_POST['no_hp']);
    $role             = sanitize($_POST['role']);

    if (!$nama_lengkap || !$email || !$password || !$confirm_password || !$no_hp || !$role) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $check = fetch_single("SELECT id_user FROM users WHERE email='".escape($email)."'");
        if ($check) {
            $error = 'Email already exists';
        } else {
            $sql = "INSERT INTO users (nama_lengkap,email,password,no_hp,role)
                    VALUES (
                        '".escape($nama_lengkap)."',
                        '".escape($email)."',
                        '".hash_password($password)."',
                        '".escape($no_hp)."',
                        '".escape($role)."'
                    )";

            if (insert($sql)) {
                log_activity("Added new user: $nama_lengkap ($role)");
                redirect('index.php','User added successfully','success');
            } else {
                $error = 'Failed to add user';
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
.create-wrap{
    max-width:900px;
    margin:90px auto;
    padding:0 24px;
}

/* CARD */
.create-card{
    border:none;
    border-radius:28px;
    box-shadow:0 14px 40px rgba(0,0,0,.08);
    overflow:hidden;
}

/* HEADER */
.create-header{
    background:#fff;
    padding:28px 32px;
    border-bottom:1px solid #eee;
    font-family:'Cinzel',serif;
    letter-spacing:2px;
    font-size:1.1rem;
}

/* BODY */
.create-body{
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

<section class="create-wrap">

    <div class="card create-card">

        <div class="create-header">
            <i class="fas fa-user-plus me-2"></i>ADD NEW USER
        </div>

        <div class="create-body">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-4">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="nama_lengkap" class="form-control" required
                           value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <div class="helper-text mt-1">Minimum 6 characters</div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="no_hp" class="form-control" required
                           value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                </div>

                <div class="mb-5">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="admin" <?= (($_POST['role'] ?? '')==='admin')?'selected':'' ?>>Admin</option>
                        <option value="staff" <?= (($_POST['role'] ?? '')==='staff')?'selected':'' ?>>Staff</option>
                        <option value="user"  <?= (($_POST['role'] ?? '')==='user')?'selected':'' ?>>User</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-1"></i>Save User
                    </button>
                </div>

            </form>

        </div>
    </div>

</section>

<?php require_once '../includes/footer.php'; ?>
