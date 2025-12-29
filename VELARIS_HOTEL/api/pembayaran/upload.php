<?php
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/function.php';

if (!is_logged_in() || $_SESSION['role'] !== 'customer') {
    echo json_encode(['status' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request']);
    exit;
}

$id_user      = $_SESSION['user_id'];
$id_reservasi = (int) ($_POST['id_reservasi'] ?? 0);

if (!$id_reservasi || !isset($_FILES['bukti'])) {
    echo json_encode(['status' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// ===============================
// CEK KEPEMILIKAN RESERVASI
// ===============================
$reservasi = fetch_single("
    SELECT id_reservasi 
    FROM reservasi 
    WHERE id_reservasi = $id_reservasi AND id_user = $id_user
");

if (!$reservasi) {
    echo json_encode(['status' => false, 'message' => 'Reservasi tidak valid']);
    exit;
}

// ===============================
// VALIDASI FILE
// ===============================
$allowed = ['jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode(['status' => false, 'message' => 'Format file tidak valid']);
    exit;
}

// ===============================
// UPLOAD FILE
// ===============================
$folder = '../../uploads/bukti_pembayaran/';
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$filename = 'bukti_' . time() . '_' . $id_reservasi . '.' . $ext;
$path = $folder . $filename;

if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $path)) {
    echo json_encode(['status' => false, 'message' => 'Upload gagal']);
    exit;
}

// ===============================
// SIMPAN KE DATABASE
// ===============================
insert("
    INSERT INTO pembayaran 
    (id_reservasi, bukti_pembayaran, status_verifikasi, tgl_upload)
    VALUES ($id_reservasi, '$filename', 'pending', NOW())
");

update("
    UPDATE reservasi 
    SET status = 'menunggu_verifikasi'
    WHERE id_reservasi = $id_reservasi
");

echo json_encode([
    'status' => true,
    'message' => 'Bukti pembayaran berhasil diupload'
]);
exit;
