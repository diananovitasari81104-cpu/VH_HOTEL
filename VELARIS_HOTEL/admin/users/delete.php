<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

require_admin();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Tidak bisa hapus diri sendiri
    if ($id == $_SESSION['user_id']) {
        $response['message'] = 'Cannot delete your own account';
        echo json_encode($response);
        exit;
    }
    
    // Get user info untuk log
    $user = fetch_single("SELECT nama_lengkap FROM users WHERE id_user = $id");
    
    if ($user) {
        // Delete user
        if (execute("DELETE FROM users WHERE id_user = $id")) {
            log_activity("Deleted user: {$user['nama_lengkap']} (ID: $id)");
            $response['success'] = true;
            $response['message'] = 'User deleted successfully';
        } else {
            $response['message'] = 'Failed to delete user';
        }
    } else {
        $response['message'] = 'User not found';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>