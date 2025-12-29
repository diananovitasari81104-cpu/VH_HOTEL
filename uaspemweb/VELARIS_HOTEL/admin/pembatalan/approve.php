<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

require_staff();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Get cancellation info
    $cancel = fetch_single("
        SELECT p.*, r.id_reservasi, u.nama_lengkap 
        FROM pembatalan p 
        JOIN reservasi r ON p.id_reservasi = r.id_reservasi 
        JOIN users u ON r.id_user = u.id_user 
        WHERE p.id_batal = $id AND p.status_pengajuan = 'pending'
    ");
    
    if ($cancel) {
        // Start transaction-like updates
        $now = date('Y-m-d H:i:s');
        
        // Update pembatalan status
        $sql1 = "UPDATE pembatalan SET 
                status_pengajuan = 'disetujui',
                tgl_diproses = '$now',
                catatan_admin = 'Cancellation approved. Refund will be processed.'
                WHERE id_batal = $id";
        
        // Update reservasi status to 'batal'
        $sql2 = "UPDATE reservasi SET status = 'batal' WHERE id_reservasi = {$cancel['id_reservasi']}";
        
        if (execute($sql1) && execute($sql2)) {
            log_activity("Approved cancellation request #$id (Reservation #{$cancel['id_reservasi']}, Guest: {$cancel['nama_lengkap']})");
            $response['success'] = true;
            $response['message'] = 'Cancellation approved successfully';
        } else {
            $response['message'] = 'Failed to approve cancellation';
        }
    } else {
        $response['message'] = 'Cancellation not found or already processed';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>