<?php
session_start();

// Simpan nama customer untuk log (opsional)
$customer_name = $_SESSION['customer_name'] ?? 'Guest';

// Hapus semua session variables
$_SESSION = array();

// Hapus cookie session jika ada
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy session
session_destroy();

// Redirect ke booking.php dengan parameter success untuk menampilkan SweetAlert
header("Location: ../../booking.php?logout=success");
exit();
?>