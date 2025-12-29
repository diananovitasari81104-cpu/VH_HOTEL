<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

require_staff();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Get room info untuk log dan foto
    $room = fetch_single("SELECT * FROM kamar WHERE id_kamar = $id");
    
    if ($room) {
        // Delete room
        if (execute("DELETE FROM kamar WHERE id_kamar = $id")) {
            // Hapus foto jika ada
            if ($room['foto_kamar'] && file_exists('../../uploads/kamar/' . $room['foto_kamar'])) {
                delete_file('../../uploads/kamar/' . $room['foto_kamar']);
            }
            
            log_activity("Deleted room: {$room['nama_kamar']} (ID: $id)");
            $response['success'] = true;
            $response['message'] = 'Room deleted successfully';
        } else {
            $response['message'] = 'Failed to delete room';
        }
    } else {
        $response['message'] = 'Room not found';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>