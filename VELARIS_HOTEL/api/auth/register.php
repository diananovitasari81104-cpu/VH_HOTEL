<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../auth/register.php");
    exit;
}

$nama     = sanitize($_POST['nama_lengkap']);  
$email    = sanitize($_POST['email']);
$no_hp    = sanitize($_POST['no_hp']);
$password = $_POST['password'];
$confirm  = $_POST['password_confirm'];

if ($password !== $confirm) {
    $_SESSION['error'] = "Password tidak cocok";
    header("Location: ../../auth/register.php");
    exit;
}

$cek = fetch_single("SELECT * FROM users WHERE email='$email'");
if ($cek) {
    $_SESSION['error'] = "Email sudah terdaftar";
    header("Location: ../../auth/register.php");
    exit;
}

$hash = hash_password($password);

$sql = "INSERT INTO users (nama_lengkap, email, password, no_hp, role)
        VALUES ('$nama', '$email', '$hash', '$no_hp', 'user')";

insert($sql);

$_SESSION['success'] = "Registrasi berhasil, silakan login";
header("Location: ../../auth/register.php?success=1");
exit;

