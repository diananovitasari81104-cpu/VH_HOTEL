<?php
// Load config dulu
require_once '../../config/database.php';
require_once '../../config/functions.php';

$page_title = 'Edit User';
require_once '../includes/header.php';

require_admin();

$error = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get user data
$user = fetch_single("SELECT * FROM users WHERE id_user = $id");
if (!$user) {
    redirect('index.php', 'User not found', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $no_hp = sanitize($_POST['no_hp']);
    $role = sanitize($_POST['role']);
    $password = $_POST['password'];
    
    // Validasi
    if (empty($nama_lengkap) || empty($email) || empty($no_hp) || empty($role)) {
        $error = 'All fields are required';
    } else {
        // Cek email sudah ada atau belum (kecuali email sendiri)
        $check = fetch_single("SELECT id_user FROM users WHERE email = '" . escape($email) . "' AND id_user != $id");
        if ($check) {
            $error = 'Email already exists';
        } else {
            // Update user
            $sql = "UPDATE users SET 
                    nama_lengkap = '" . escape($nama_lengkap) . "',
                    email = '" . escape($email) . "',
                    no_hp = '" . escape($no_hp) . "',
                    role = '" . escape($role) . "'";
            
            // Update password jika diisi
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters';
                } else {
                    $hashed_password = hash_password($password);
                    $sql .= ", password = '" . $hashed_password . "'";
                }
            }
            
            $sql .= " WHERE id_user = $id";
            
            if (!$error && execute($sql)) {
                log_activity("Updated user: $nama_lengkap (ID: $id)");
                redirect('index.php', 'User updated successfully', 'success');
            } else {
                if (!$error) $error = 'Failed to update user';
            }
        }
    }
}
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit me-2"></i>Edit User
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['no_hp']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="staff" <?php echo $user['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                        </select>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>