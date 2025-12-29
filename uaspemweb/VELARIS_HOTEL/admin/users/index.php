<?php
// Load config dulu sebelum header
require_once '../../config/database.php';
require_once '../../config/functions.php';

$page_title = 'Manage Users';
require_once '../includes/header.php';

// Hanya admin yang bisa akses
require_admin();

// Get all users
$users = fetch_all("SELECT * FROM users ORDER BY created_at DESC");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users me-2"></i>User Management</span>
                <a href="create.php" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i>Add New User
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id_user']; ?></td>
                                <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['no_hp']); ?></td>
                                <td>
                                    <?php
                                    $badge = [
                                        'admin' => 'danger',
                                        'staff' => 'warning',
                                        'user' => 'info'
                                    ];
                                    $class = $badge[$user['role']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $class; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo format_tanggal($user['created_at'], 'd M Y'); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id_user'] != $_SESSION['user_id']): ?>
                                    <button onclick="deleteUser(<?php echo $user['id_user']; ?>)" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10
    });
});

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: 'delete.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error deleting user');
            }
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>