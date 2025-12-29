<?php
/**
 * VELARIS HOTEL - Database Configuration
 * File koneksi ke database MySQL
 */

class Koneksi {
    private $host = "127.0.0.1";
    private $user = "root";
    private $pass = "";
    private $db   = "db_velarishotel";
    private $port = 3306;
    private $conn;
    
    /**
     * Get koneksi database
     */
    function getKoneksi() {
        $this->conn = mysqli_connect(
            $this->host, 
            $this->user, 
            $this->pass, 
            $this->db, 
            $this->port
        );

        if (!$this->conn) {
            die("Koneksi gagal: " . mysqli_connect_error());
        }

        // Set charset UTF-8
        mysqli_set_charset($this->conn, "utf8mb4");

        return $this->conn;
    }
}

// Inisialisasi koneksi global
$database = new Koneksi();
$conn = $database->getKoneksi();

// ============================================
// FUNGSI HELPER UNTUK QUERY DATABASE
// ============================================

/**
 * Escape string (mencegah SQL Injection)
 */
function escape($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

/**
 * Query dengan error handling
 */
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        // Log error untuk debugging
        error_log("SQL Error: " . mysqli_error($conn) . " | Query: " . $sql);
        return false;
    }
    
    return $result;
}

/**
 * Mendapatkan satu baris data
 */
function fetch_single($sql) {
    $result = query($sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Mendapatkan banyak baris data
 */
function fetch_all($sql) {
    $result = query($sql);
    $data = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    return $data;
}

/**
 * Insert data dan return last ID
 */
function insert($sql) {
    global $conn;
    if (query($sql)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

/**
 * Update/Delete dan return affected rows
 */
function execute($sql) {
    global $conn;
    if (query($sql)) {
        return mysqli_affected_rows($conn);
    }
    return false;
}
?>