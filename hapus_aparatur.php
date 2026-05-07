<?php
session_start();
require_once 'auth_check.php';
require_once 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT foto FROM aparatur_desa WHERE id_aparatur = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        
        if ($data) {
            $stmt = $pdo->prepare("DELETE FROM aparatur_desa WHERE id_aparatur = :id");
            if ($stmt->execute([':id' => $id])) {
                if (!empty($data['foto'])) {
                    $filepath = 'aparatur_img/' . $data['foto'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                $_SESSION['alert'] = ['status' => 'success', 'message' => 'Data aparatur berhasil dihapus.'];
            }
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = ['status' => 'error', 'message' => 'Gagal menghapus data aparatur.'];
    }
}
header("Location: aparatur.php");
exit;