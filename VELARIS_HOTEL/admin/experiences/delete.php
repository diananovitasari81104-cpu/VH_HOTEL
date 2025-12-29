<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

require_staff();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Get experience info untuk log dan foto
    $experience = fetch_single("SELECT * FROM experiences WHERE id_experience = $id");
    
    if ($experience) {
        // Delete experience
        if (execute("DELETE FROM experiences WHERE id_experience = $id")) {
            // Hapus foto jika ada
            if ($experience['foto'] && file_exists('../../uploads/experiences/' . $experience['foto'])) {
                delete_file('../../uploads/experiences/' . $experience['foto']);
            }
            
            log_activity("Deleted experience: {$experience['nama_aktivitas']} (ID: $id)");
            $response['success'] = true;
            $response['message'] = 'Experience deleted successfully';
        } else {
            $response['message'] = 'Failed to delete experience';
        }
    } else {
        $response['message'] = 'Experience not found';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>