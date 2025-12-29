<?php
session_start();

if (isset($_SESSION['customer_id'])) {
    header("Location: ../booking.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet">

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
</style>
</head>

<body>

<div class="bg"></div>

<div class="login-wrapper">
    <div class="login-box">

        <!-- CLOSE → BALIK KE BOOKING -->
        <a href="../booking.php" class="close">×</a>

        <h3>Login Member</h3>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color:red;font-size:.8rem;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </p>
        <?php endif; ?>

        <form action="../api/auth/login.php" method="POST">
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

</body>
</html>
