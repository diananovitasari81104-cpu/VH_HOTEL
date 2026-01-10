<?php
session_start();
require_once "config/database.php";

/**
 * HARUS LOGIN
 */
if (!isset($_SESSION['customer_id'])) {
    header("Location: auth/login.php");
    exit;
}

/**
 * AMBIL DATA DARI FORM
 */
$id_kamar       = $_POST['id_kamar']       ?? '';
$tgl_checkin    = $_POST['checkin']        ?? '';
$tgl_checkout   = $_POST['checkout']       ?? '';
$total_harga    = $_POST['total_harga']    ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$card_number    = $_POST['card_number']    ?? '';
$bukti_bayar    = $_FILES['payment_proof']['name'] ?? '';
$id_user        = $_SESSION['customer_id'];

/**
 * AMBIL KODE BOOKING DARI SESSION
 */
$kode_booking = $_SESSION['kode_booking'] ?? null;

/**
 * VALIDASI DATA UMUM
 */
if (!$id_kamar || !$tgl_checkin || !$tgl_checkout || !$total_harga || !$payment_method || !$kode_booking) {
    header("Location: pembayaran_gagal.php?error=data_tidak_lengkap");
    exit;
}

/**
 * VALIDASI BERDASARKAN METODE PEMBAYARAN
 */
if ($payment_method === 'credit_card') {
    if (empty($card_number)) {
        header("Location: pembayaran_gagal.php?error=card_number_required");
        exit;
    }
    $bukti_bayar = null;
}

if ($payment_method === 'bank_transfer') {
    if (empty($bukti_bayar)) {
        header("Location: pembayaran_gagal.php?error=bukti_bayar_required");
        exit;
    }

    // SIMPAN FILE BUKTI TRANSFER
    $targetDir = "uploads/bukti_pembayaran/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['payment_proof']['name']);
    $targetFile = $targetDir . $fileName;

    if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetFile)) {
        header("Location: pembayaran_gagal.php?error=upload_failed");
        exit;
    }

    $bukti_bayar = $fileName;
}
// TENTUKAN STATUS BERDASARKAN METODE PEMBAYARAN
if ($payment_method === 'bank_transfer') {
    $status = 'menunggu_verifikasi';
} elseif ($payment_method === 'credit_card') {
    $status = 'lunas';
} else {
    $status = 'menunggu_bayar';
}

/**
 * SIMPAN RESERVASI KE DATABASE (DENGAN KODE BOOKING)
 */
$stmt = $conn->prepare("
    INSERT INTO reservasi 
    (id_user, id_kamar, tgl_checkin, tgl_checkout, total_harga, bukti_bayar, kode_booking, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissdsss",
    $id_user,
    $id_kamar,
    $tgl_checkin,
    $tgl_checkout,
    $total_harga,
    $bukti_bayar,
    $kode_booking,
    $status
);

if (!$stmt->execute()) {
    header("Location: pembayaran_gagal.php?error=database_error&detail=" . urlencode($stmt->error));
    exit;
}

/**
 * AMBIL ID RESERVASI YANG BARU DIBUAT
 */
$id_reservasi = $conn->insert_id;

/**
 * JIKA GAGAL INSERT
 */
if (!$id_reservasi) {
    header("Location: pembayaran_gagal.php?error=insert_failed");
    exit;
}

/**
 * HAPUS KODE BOOKING DARI SESSION (SUDAH TERSIMPAN DI DATABASE)
 */
unset($_SESSION['kode_booking']);

/**
 * BERHASIL → KE HALAMAN SUKSES
 */
header("Location: pembayaran_berhasil.php?id_reservasi=$id_reservasi");
exit;
?>