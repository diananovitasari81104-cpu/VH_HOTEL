<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

require_staff();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['catatan_admin'])) {
    $id = (int)$_POST['id'];
    $catatan_admin = sanitize($_POST['catatan_admin']);
    
    if (empty($catatan_admin)) {
        $response['message'] = 'Admin notes are required';
        echo json_encode($response);
        exit;
    }
    
    // Get cancellation info
    $cancel = fetch_single("
        SELECT p.*, u.nama_lengkap 
        FROM pembatalan p 
        JOIN reservasi r ON p.id_reservasi = r.id_reservasi 
        JOIN users u ON r.id_user = u.id_user 
        WHERE p.id_batal = $id AND p.status_pengajuan = 'pending'
    ");
    
    if ($cancel) {
        $now = date('Y-m-d H:i:s');
        
        // Update pembatalan status
        $sql = "UPDATE pembatalan SET 
                status_pengajuan = 'ditolak',
                tgl_diproses = '$now',
                catatan_admin = '" . escape($catatan_admin) . "'
                WHERE id_batal = $id";
        
        if (execute($sql)) {
            log_activity("Rejected cancellation request #$id (Guest: {$cancel['nama_lengkap']})");
            $response['success'] = true;
            $response['message'] = 'Cancellation rejected successfully';
        } else {
            $response['message'] = 'Failed to reject cancellation';
        }
    } else {
        $response['message'] = 'Cancellation not found or already processed';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>