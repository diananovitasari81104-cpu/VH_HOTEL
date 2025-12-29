<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Redirect jika sudah login
if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $sql = "SELECT * FROM users WHERE email = '" . escape($email) . "' AND role IN ('admin', 'staff')";
        $user = fetch_single($sql);
        
        if ($user && verify_password($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            log_activity('Login to admin panel');
            redirect('index.php', 'Welcome back, ' . $user['nama_lengkap'] . '!', 'success');
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Velaris Hotel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* BACKGROUND FOTO  */
body{
    min-height:100vh;
    margin:0;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Inter',sans-serif;
     background:url('/phpdiana/VELARIS_HOTEL/uploads/experiences/pool.jpg') center/cover no-repeat;
    position:relative;
}

body::before{
    content:'';
    position:absolute;
    inset:0;
    backdrop-filter:blur(6px);
    background:rgba(0,0,0,.35);
}

/*  CARD LOGIN  */
.login-container{
    position:relative;
    z-index:1;
    max-width:460px;
    width:100%;
    padding:20px;
}

.login-card{
    background:#fff;
    border-radius:22px;
    box-shadow:0 25px 70px rgba(0,0,0,.35);
    padding:40px;
}

/* HEADER */
.login-header h2{
    font-size:26px;
    font-weight:600;
    margin-bottom:6px;
}

.login-header p{
    color:#666;
    font-size:14px;
    margin-bottom:28px;
}

/* INPUT */
.form-control{
    border-radius:14px;
    padding:12px 14px;
}

.form-control:focus{
    border-color:#f2c94c;
    box-shadow:0 0 0 .2rem rgba(242,201,76,.25);
}

/* BUTTON */
.btn-login{
    background:#f2c94c;
    border:none;
    padding:12px;
    font-weight:600;
    border-radius:14px;
    color:#000;
}

.btn-login:hover{
    background:#e0b83f;
}

/* DEFAULT LOGIN TEXT */
.default-login{
    margin-top:18px;
    font-size:13px;
    color:#666;
}
</style>
</head>

<body>

<div class="login-container">
    <div class="login-card">
        <div class="login-header text-center">
            <h2>Velaris Hotel</h2>
            <p>Admin / Staff Panel Login</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email"
                       class="form-control"
                       name="email"
                       placeholder="admin@velaris.com"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password"
                       class="form-control"
                       name="password"
                       placeholder="Enter your password"
                       required>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Login
            </button>
        </form>

        <div class="text-center default-login">
            Default Login: admin@velaris.com / vel4ris4dmin
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
