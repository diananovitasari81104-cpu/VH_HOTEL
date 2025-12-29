<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

require_staff();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = sanitize($_POST['status']);
    
    // Validasi status
    $valid_statuses = ['menunggu_bayar', 'menunggu_verifikasi', 'lunas', 'batal', 'selesai'];
    if (!in_array($status, $valid_statuses)) {
        $response['message'] = 'Invalid status';
        echo json_encode($response);
        exit;
    }
    
    // Get reservation info untuk log
    $reservasi = fetch_single("SELECT r.*, u.nama_lengkap FROM reservasi r JOIN users u ON r.id_user = u.id_user WHERE r.id_reservasi = $id");
    
    if ($reservasi) {
        // Update status
        $sql = "UPDATE reservasi SET status = '" . escape($status) . "' WHERE id_reservasi = $id";
        
        if (execute($sql)) {
            $status_text = ucfirst(str_replace('_', ' ', $status));
            log_activity("Updated reservation #$id status to: $status_text (Guest: {$reservasi['nama_lengkap']})");
            $response['success'] = true;
            $response['message'] = 'Status updated successfully';
        } else {
            $response['message'] = 'Failed to update status';
        }
    } else {
        $response['message'] = 'Reservation not found';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>