<?php
session_start();
require_once 'auth_check.php'; // Tambahan keamanan
require_once 'koneksi.php';

if (isset($_GET['id']) && isset($_GET['file'])) {
    $id = $_GET['id'];
    $filename = basename($_GET['file']);
    $gallery_dir = 'galeri_img/';
    $filepath = $gallery_dir . $filename;

    try {
        $stmt = $pdo->prepare("DELETE FROM galeri WHERE id_galeri = :id");
        if ($stmt->execute([':id' => $id])) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            $_SESSION['alert'] = [
                'status' => 'success',
                'message' => 'Foto berhasil dihapus dari galeri.'
            ];
        }
    } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'status' => 'error',
                'message' => 'Gagal menghapus data dari database.'
            ];
    }
}

header("Location: galeri.php");
exit;