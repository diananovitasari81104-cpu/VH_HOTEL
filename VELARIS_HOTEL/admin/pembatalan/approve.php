<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

header('Content-Type: application/json');

if (!is_staff()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    echo json_encode(['success'=>false,'message'=>'ID missing']);
    exit;
}

$sql1 = "UPDATE pembatalan 
         SET status_pengajuan='disetujui', tgl_diproses=NOW()
         WHERE id_batal=$id";

if (execute($sql1)) {
    log_activity("Approve cancellation #$id");
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>'DB error']);
}
