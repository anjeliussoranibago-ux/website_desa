<?php
session_start();
require_once 'auth_check.php';
require_once 'koneksi.php';

if (isset($_GET['id']) && isset($_GET['file'])) {
    $id = $_GET['id'];
    $filename = basename($_GET['file']);
    $dokumen_dir = 'dokumen_img/';
    $filepath = $dokumen_dir . $filename;

    try {
        $stmt = $pdo->prepare("DELETE FROM dokumen_penduduk WHERE id_dokumen = :id");
        if ($stmt->execute([':id' => $id])) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            $_SESSION['alert'] = ['status' => 'success', 'message' => 'Dokumen berhasil dihapus.'];
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = ['status' => 'error', 'message' => 'Gagal menghapus dokumen dari database.'];
    }
}

header("Location: dokumen_penduduk.php");
exit;