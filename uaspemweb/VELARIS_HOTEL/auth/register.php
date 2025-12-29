<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Register | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet">

<style>
body{margin:0;font-family:'Inter',sans-serif}

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

.wrapper{
    position:relative;
    z-index:10;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.box{
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

h3{
    font-family:'Cinzel',serif;
    margin-bottom:18px;
}

label{font-size:.8rem;margin-top:10px;display:block}
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
}

.links{
    text-align:center;
    margin-top:14px;
    font-size:.75rem;
}
.links a{color:#c59d2a;text-decoration:none}
</style>
</head>

<body>

<div class="bg"></div>

<div class="wrapper">
<div class="box">

<a href="../booking.php" class="close">×</a>

<h3>Create Account</h3>

<form action="../api/auth/register.php" method="POST">
    <label>Nama</label>
    <input type="text" name="nama_lengkap" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>No HP</label>
    <input type="text" name="no_hp" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Confirm Password</label>
    <input type="password" name="password_confirm" required>

    <button type="submit">Register</button>
</form>

<div class="links">
    <a href="login.php">Already have account? Login</a>
</div>

</div>
</div>

</body>
</html>
