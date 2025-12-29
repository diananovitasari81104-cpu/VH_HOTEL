<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_admin();

$page_title = 'Manage Users';
require_once '../includes/header.php';

$users = fetch_all("SELECT * FROM users ORDER BY created_at DESC");
?>

<style>
body{
    background:linear-gradient(180deg,#f6f6f6 0%, #ffffff 65%);
}

/* WRAPPER */
.admin-wrap{
    max-width:1200px;
    margin:80px auto;
    padding:0 24px;
}

/* TITLE */
.page-title{
    font-family:'Cinzel',serif;
    letter-spacing:3px;
    font-size:1.8rem;
    margin-bottom:40px;
    position:relative;
}
.page-title::after{
    content:'';
    position:absolute;
    left:0;
    bottom:-14px;
    width:70px;
    height:3px;
    background:#d4af37;
    border-radius:4px;
}

/* CARD */
.card{
    border:none;
    border-radius:28px;
    box-shadow:0 14px 40px rgba(0,0,0,.08);
    overflow:hidden;
}
.card-header{
    background:#fff;
    padding:26px 32px;
    border-bottom:1px solid #eee;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.card-header .btn{
    background:#d4af37;
    border:none;
    color:#000;
    font-weight:600;
}
.card-header .btn:hover{
    background:#c5a030;
}

/* TABLE */
.table{
    margin:0;
}
.table thead th{
    font-size:.7rem;
    letter-spacing:1.5px;
    text-transform:uppercase;
    background:#fafafa;
    border-bottom:1px solid #eee;
}
.table td{
    vertical-align:middle;
    padding:16px 18px;
    font-size:.9rem;
}
.table tbody tr:hover{
    background:#faf7ef;
}

/* BADGE */
.badge{
    font-size:.65rem;
    padding:6px 14px;
    border-radius:20px;
}
.bg-danger{background:#dc3545!important;}
.bg-warning{background:#ffc107!important;color:#000;}
.bg-info{background:#0dcaf0!important;color:#000;}

/* ACTION BUTTON */
.action-btn{
    width:36px;
    height:36px;
    border-radius:10px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border:none;
}
.btn-edit{
    background:#ffc107;
    color:#000;
}
.btn-delete{
    background:#dc3545;
    color:#fff;
}

/* DATATABLE */
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select{
    border-radius:20px;
    border:1px solid #ddd;
    padding:6px 12px;
}
.dataTables_paginate .paginate_button{
    border-radius:50%!important;
}
</style>

<section class="admin-wrap">

    <h1 class="page-title">USER MANAGEMENT</h1>

    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-users me-2"></i>Manage Users</span>
            <a href="create.php" class="btn btn-sm">
                <i class="fas fa-plus me-1"></i>Add New User
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-center">#<?= $user['id_user'] ?></td>
                            <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['no_hp']) ?></td>
                            <td>
                                <?php
                                $badge = [
                                    'admin' => 'danger',
                                    'staff' => 'warning',
                                    'user'  => 'info'
                                ];
                                $class = $badge[$user['role']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $class ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td><?= format_tanggal($user['created_at'],'d M Y') ?></td>
                            <td class="text-center">
                                <a href="edit.php?id=<?= $user['id_user'] ?>"
                                   class="action-btn btn-edit me-1"
                                   title="Edit User">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <?php if ($user['id_user'] != $_SESSION['user_id']): ?>
                                <button type="button"
                                        class="action-btn btn-delete"
                                        title="Delete User"
                                        onclick="deleteUser(<?= (int)$user['id_user'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        order:[[0,'desc']],
        pageLength:10
    });
});

function deleteUser(id){
    if(confirm('Are you sure you want to delete this user?')){
        $.ajax({
            url:'delete.php',
            type:'POST',
            data:{id:id},
            dataType:'json',
            success:function(res){
                alert(res.message);
                if(res.success) location.reload();
            },
            error:function(){
                alert('Error deleting user');
            }
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
