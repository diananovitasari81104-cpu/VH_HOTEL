<?php 
session_start(); 
require_once '../../config/database.php'; 
require_once '../../config/functions.php'; 

$email = trim($_POST['email'] ?? ''); 
$password = $_POST['password'] ?? ''; 
$user = fetch_single("SELECT * FROM users WHERE email='$email'"); 

if (!$user) { 
    $_SESSION['error'] = "Akun tidak ditemukan"; 
    header("Location: ../../auth/login.php"); 
    exit; 
} 

if (!verify_password($password, $user['password'])) { 
    $_SESSION['error'] = "Password salah"; 
    header("Location: ../../auth/login.php"); 
    exit; 
} 

// Force Set 
$_SESSION['customer_id'] = (int)$user['id_user']; 
$_SESSION['customer_name'] = $user['nama_lengkap'] ?? 'Customer'; 
$_SESSION['customer_email'] = $user['email'] ?? '-'; 

header("Location: ../../booking.php");
exit; 
