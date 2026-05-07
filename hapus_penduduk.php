<?php
session_start();
require_once 'auth_check.php';
require_once 'koneksi.php';

if (isset($_GET['nik'])) {
    $nik = trim($_GET['nik']);
    
    if (strlen($nik) === 16 && ctype_digit($nik)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM penduduk WHERE nik = :nik");
            if ($stmt->execute([':nik' => $nik])) {
                // Hapus juga foto warga jika ada
                $foto_path = 'foto_warga/' . $nik . '.jpg';
                if (file_exists($foto_path)) {
                    unlink($foto_path);
                }
                $_SESSION['alert'] = [
                    'status' => 'success',
                    'message' => 'Data penduduk berhasil dihapus.'
                ];
            }
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'status' => 'error',
                'message' => 'Gagal menghapus data.'
            ];
        }
    } else {
        $_SESSION['alert'] = [
            'status' => 'error',
            'message' => 'Format NIK tidak valid.'
        ];
    }
}

header("Location: data_penduduk.php");
exit;