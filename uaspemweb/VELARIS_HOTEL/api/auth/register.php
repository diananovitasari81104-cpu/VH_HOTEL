<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../auth/register.php");
    exit;
}

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

$sql = "INSERT INTO users (email, password, no_hp, role)
        VALUES ('$email', '$hash', '$no_hp', 'customer')";

insert($sql);

$_SESSION['success'] = "Registrasi berhasil, silakan login";
header("Location: ../../auth/login.php");
exit;
