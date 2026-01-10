<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

header('Content-Type: application/json');

try {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $catatan_admin = trim($_POST['catatan_admin'] ?? '');

    if ($id <= 0) {
        throw new Exception('ID reservasi tidak valid');
    }

    // Ambil reservasi
    $reservasi = fetch_single("SELECT * FROM reservasi WHERE id_reservasi = $id");
    if (!$reservasi) throw new Exception('Reservasi tidak ditemukan');

    // Cek apakah status adalah pembatalan_diajukan
    if ($reservasi['status'] !== 'pembatalan_diajukan') {
        throw new Exception('Reservasi ini bukan pengajuan pembatalan. Status saat ini: ' . $reservasi['status']);
    }

    // Ambil pengajuan pembatalan
    $batal = fetch_single("SELECT * FROM pembatalan WHERE id_reservasi = $id");
    if (!$batal) throw new Exception('Pengajuan pembatalan tidak ditemukan');

    if ($action === 'reject') {
        if (empty($catatan_admin)) {
            throw new Exception('Catatan admin wajib diisi untuk menolak pembatalan');
        }

        // Ambil status sebelumnya dari tabel pembatalan
        $status_kembali = $batal['status_sebelumnya'] ?? 'lunas'; // Default ke lunas jika tidak ada
        
        // Escape catatan admin
        $catatan_escaped = mysqli_real_escape_string($conn, $catatan_admin);

        // Update reservasi - kembalikan ke status sebelumnya
        $sql1 = "UPDATE reservasi SET status='$status_kembali' WHERE id_reservasi=$id";
        $res1 = mysqli_query($conn, $sql1);

        // Update pembatalan
        $sql2 = "UPDATE pembatalan SET status_pengajuan='ditolak', catatan_admin='$catatan_escaped', tgl_diproses=NOW() WHERE id_batal=" . $batal['id_batal'];
        $res2 = mysqli_query($conn, $sql2);

        if ($res1 && $res2) {
            echo json_encode([
                'success' => true, 
                'message' => 'Pembatalan berhasil ditolak. Status dikembalikan ke: ' . ucfirst($status_kembali),
                'new_status' => $status_kembali
            ]);
        } else {
            $error = 'Gagal menolak pembatalan';
            if (!$res1) $error .= ' - Update reservasi gagal: ' . mysqli_error($conn);
            if (!$res2) $error .= ' - Update pembatalan gagal: ' . mysqli_error($conn);
            throw new Exception($error);
        }
        exit;
    }

    if ($action === 'approve') {
        // Update reservasi
        $sql1 = "UPDATE reservasi SET status='batal' WHERE id_reservasi=$id";
        $res1 = mysqli_query($conn, $sql1);

        // Update pembatalan
        $sql2 = "UPDATE pembatalan SET status_pengajuan='disetujui', tgl_diproses=NOW() WHERE id_batal=" . $batal['id_batal'];
        $res2 = mysqli_query($conn, $sql2);

        if ($res1 && $res2) {
            echo json_encode(['success' => true, 'message' => 'Pembatalan berhasil disetujui']);
        } else {
            $error = 'Gagal menyetujui pembatalan';
            if (!$res1) $error .= ' - Update reservasi gagal: ' . mysqli_error($conn);
            if (!$res2) $error .= ' - Update pembatalan gagal: ' . mysqli_error($conn);
            throw new Exception($error);
        }
        exit;
    }

    throw new Exception('Action tidak dikenali: ' . $action);

} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}