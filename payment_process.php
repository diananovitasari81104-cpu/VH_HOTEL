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
 * AMBIL DATA
 */
$id_kamar       = $_POST['id_kamar'] ?? '';
$tgl_checkin        = $_POST['checkin'] ?? '';
$tgl_checkout       = $_POST['checkout'] ?? '';
$total_harga    = $_POST['total_harga'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

$card_number    = $_POST['card_number'] ?? '';
$bukti_bayar  = $_FILES['payment_proof']['name'] ?? '';

$id_user    = $_SESSION['customer_id'];

/**
 * VALIDASI DATA UMUM
 */
if (!$id_kamar || !$tgl_checkin || !$tgl_checkout || !$total_harga || !$payment_method) {
    header("Location: pembayaran_gagal.php");
    exit;
}

/**
 * VALIDASI BERDASARKAN METODE PEMBAYARAN
 */
if ($payment_method === 'credit_card') {

    if (empty($card_number)) {
        header("Location: pembayaran_gagal.php");
        exit;
    }

    $bukti_bayar = null;
}

if ($payment_method === 'bank_transfer') {

    if (empty($bukti_bayar)) {
        header("Location: pembayaran_gagal.php");
        exit;
    }

    // SIMPAN FILE BUKTI TRANSFER
    $targetDir = "uploads/bukti_pembayaran/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['payment_proof']['name']);
    $targetFile = $targetDir . $fileName;

    move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetFile);

    $bukti_bayar = $fileName;
}

/**
 * SIMPAN RESERVASI KE DATABASE
 */
$stmt = $conn->prepare("
    INSERT INTO reservasi 
    (id_user, id_kamar, tgl_checkin, tgl_checkout, total_harga, bukti_bayar, status)
    VALUES (?, ?, ?, ?, ?, ?, 'paid')
");

$stmt->bind_param(
    "iissds",
    $id_user,
    $id_kamar,
    $tgl_checkin,
    $tgl_checkout,
    $total_harga,
    $bukti_bayar
);

$stmt->execute();

/**
 * AMBIL ID RESERVASI YANG BARU DIBUAT
 */
$id_reservasi = $conn->insert_id;

/**
 * JIKA GAGAL INSERT
 */
if (!$id_reservasi) {
    header("Location: pembayaran_gagal.php");
    exit;
}

/**
 * BERHASIL → KE HALAMAN SUKSES
 */
header("Location: pembayaran_berhasil.php?id_reservasi=$id_reservasi");
exit;
