<?php
/**
 * VELARIS HOTEL - Helper Functions
 * Fungsi-fungsi bantuan untuk sistem
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Cek apakah user adalah admin
 */
function is_admin() {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

/**
 * Cek apakah user adalah staff
 */
function is_staff() {
    return is_logged_in() && ($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'admin');
}

/**
 * Redirect jika belum login
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Redirect jika bukan admin
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        header("Location: index.php");
        exit;
    }
}

/**
 * Redirect jika bukan staff/admin
 */
function require_staff() {
    require_login();
    if (!is_staff()) {
        header("Location: index.php");
        exit;
    }
}

/**
 * Simpan log aktivitas admin
 */
function log_activity($aksi) {
    if (!is_logged_in()) return false;
    
    $id_user = $_SESSION['user_id'];
    $aksi = escape($aksi);
    
    $sql = "INSERT INTO log_aktivitas (id_user, aksi) VALUES ($id_user, '$aksi')";
    return insert($sql);
}

/**
 * Hash password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random string untuk nama file
 */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * Upload file dengan validasi
 */
function upload_file($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    // Cek apakah ada file yang diupload
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'Tidak ada file yang diupload'];
    }
    
    // Cek error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Terjadi error saat upload file'];
    }
    
    // Ambil info file
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Validasi ekstensi file
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Gunakan: ' . implode(', ', $allowed_types)];
    }
    
    // Validasi ukuran file (max 5MB)
    if ($file_size > 5242880) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB'];
    }
    
    // Generate nama file baru
    $new_file_name = generate_random_string(20) . '.' . $file_ext;
    $target_file = $target_dir . $new_file_name;
    
    // Buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file_tmp, $target_file)) {
        return ['success' => true, 'filename' => $new_file_name];
    } else {
        return ['success' => false, 'message' => 'Gagal upload file'];
    }
}

/**
 * Hapus file
 */
function delete_file($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Format rupiah
 */
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Format tanggal (English)
 */
function format_tanggal($tanggal, $format = 'd F Y') {
    $timestamp = strtotime($tanggal);
    return date($format, $timestamp);
}

/**
 * Alert message dengan Bootstrap
 */
function alert($message, $type = 'success') {
    return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Redirect dengan pesan
 */
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit;
}

/**
 * Tampilkan pesan flash message
 */
function show_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'success';
        
        echo alert($message, $type);
        
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

/**
 * Generate token CSRF
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi token CSRF
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>