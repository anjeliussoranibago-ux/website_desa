<?php
session_start();
require_once 'auth_check.php';
require_once 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT gambar_cover FROM berita_informasi WHERE id_berita = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        
        if ($data) {
            $stmt = $pdo->prepare("DELETE FROM berita_informasi WHERE id_berita = :id");
            if ($stmt->execute([':id' => $id])) {
                if (!empty($data['gambar_cover'])) {
                    $filepath = 'berita_img/' . $data['gambar_cover'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                $_SESSION['alert'] = ['status' => 'success', 'message' => 'Berita berhasil dihapus.'];
            }
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = ['status' => 'error', 'message' => 'Gagal menghapus berita.'];
    }
}

header("Location: berita.php");
exit;