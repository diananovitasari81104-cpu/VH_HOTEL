<?php
session_start();

// Jika sudah login, redirect
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
    header("Location: ../booking.php");
    exit;
}

// Ambil error jika ada
$login_error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet">

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
    margin:0;
    font-family:'Inter',sans-serif;
}

/* BACKGROUND BLUR */
.bg{
    position:fixed;
    inset:0;
    background:url("../uploads/experiences/pool.jpg") center/cover no-repeat;
}
.bg::after{
    content:'';
    position:absolute;
    inset:0;
    backdrop-filter:blur(8px);
    background:rgba(0,0,0,.35);
}

/* LOGIN BOX */
.login-wrapper{
    position:relative;
    z-index:10;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-box{
    background:#fff;
    width:420px;
    padding:32px;
    border-radius:20px;
    box-shadow:0 20px 60px rgba(0,0,0,.3);
    position:relative;
}

.close{
    position:absolute;
    right:16px;
    top:12px;
    font-size:22px;
    text-decoration:none;
    color:#333;
}

.login-box h3{
    font-family:'Cinzel',serif;
    margin-bottom:22px;
}

label{
    font-size:.8rem;
    margin-top:12px;
    display:block;
}

input{
    width:100%;
    padding:10px;
    margin-top:4px;
    border:1px solid #ccc;
    border-radius:8px;
}

button{
    width:100%;
    margin-top:20px;
    padding:10px;
    background:#f5c842;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}

.links{
    display:flex;
    justify-content:space-between;
    margin-top:14px;
    font-size:.75rem;
}
.links a{
    text-decoration:none;
    color:#c59d2a;
}

/* ===== LUXURY SWEETALERT ===== */
.swal2-popup.luxury-alert{
    border-radius:22px !important;
    padding:36px !important;
    box-shadow:0 25px 70px rgba(0,0,0,.3) !important;
}

.swal2-popup.luxury-alert .swal2-title{
    font-family:'Cinzel',serif !important;
    font-size:1.6rem !important;
    letter-spacing:1px;
}

.swal2-popup.luxury-alert .swal2-confirm{
    background:linear-gradient(135deg,#c62828,#b71c1c) !important;
    border-radius:30px !important;
    padding:12px 36px !important;
    font-weight:600;
}

.swal2-popup.luxury-alert{
    border-radius:24px !important;
    padding:36px !important;
    box-shadow:0 25px 80px rgba(0,0,0,.35) !important;
}

.swal2-title{
    font-family:'Cinzel',serif !important;
    letter-spacing:1px;
}

.swal2-confirm{
    background:linear-gradient(135deg,#d4af37,#c9a633) !important;
    color:#000 !important;
    border-radius:30px !important;
    padding:12px 40px !important;
    font-weight:600 !important;
}

</style>
</head>

<body>

<div class="bg"></div>

<div class="login-wrapper">
    <div class="login-box">

        <!-- CLOSE -->
        <a href="../booking.php" class="close">×</a>

        <h3>Login Member</h3>

        <form id="loginForm">
    <label>Nama</label>
    <input type="text" name="nama_lengkap" required>

    <label>Email address</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Sign In</button>
</form>


        <div class="links">
            <a href="#">Forgot password?</a>
            <a href="register.php">Create account</a>
        </div>

    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(this);

    fetch('../api/auth/login.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {

            Swal.fire({
                customClass: {
                    popup: 'luxury-alert'
                },
                icon: 'success',
                title: 'Welcome to Velaris Hotel',
                html: `
                    <p style="margin-top:10px;">
                        Selamat datang,<br>
                        <strong>${data.name}</strong>
                    </p>
                `,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });

            setTimeout(() => {
                window.location.href = '../booking.php';
            }, 2500);

        } else {
            Swal.fire({
                customClass: {
                    popup: 'luxury-alert'
                },
                icon: 'error',
                title: 'Login Gagal',
                text: data.message
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan sistem'
        });
    });
});
</script>

</body>
</html>
