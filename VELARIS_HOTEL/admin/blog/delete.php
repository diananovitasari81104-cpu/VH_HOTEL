<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

require_staff();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Get blog info untuk log dan gambar
    $blog = fetch_single("SELECT * FROM blog WHERE id_blog = $id");
    
    if ($blog) {
        // Delete blog
        if (execute("DELETE FROM blog WHERE id_blog = $id")) {
            // Hapus gambar jika ada
            if ($blog['gambar'] && file_exists('../../uploads/blog/' . $blog['gambar'])) {
                delete_file('../../uploads/blog/' . $blog['gambar']);
            }
            
            log_activity("Deleted blog article: {$blog['judul']} (ID: $id)");
            $response['success'] = true;
            $response['message'] = 'Article deleted successfully';
        } else {
            $response['message'] = 'Failed to delete article';
        }
    } else {
        $response['message'] = 'Article not found';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>