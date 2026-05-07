<?php
session_start();
require_once 'koneksi.php';

$dokumen_dir = 'dokumen_img/';
if (!is_dir($dokumen_dir)) {
    mkdir($dokumen_dir, 0777, true);
}

$pesan = ''; $status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file_dokumen'])) {
        $file = $_FILES['file_dokumen'];
        $nik = trim($_POST['nik']);
        $nama = trim($_POST['nama_pemilik']);
        $jenis = $_POST['jenis_dokumen'];
        $keterangan = $_POST['keterangan'] ?? '';

        if (strlen($nik) !== 16 || !ctype_digit($nik)) {
            $status = 'error'; $pesan = 'Format NIK tidak valid. NIK harus 16 digit angka.';
        } elseif ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
            
            if (in_array($ext, $allowed_ext)) {
                if ($file['size'] <= 2097152) { // Batas Max 2MB
                    $new_filename = uniqid('dok_') . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $dokumen_dir . $new_filename)) {
                        $stmt = $pdo->prepare("INSERT INTO dokumen_penduduk (nik, nama_pemilik, jenis_dokumen, file_dokumen, keterangan) VALUES (:nik, :nama, :jenis, :file, :keterangan)");
                        $stmt->execute([':nik' => $nik, ':nama' => $nama, ':jenis' => $jenis, ':file' => $new_filename, ':keterangan' => $keterangan]);
                        $_SESSION['alert'] = ['status' => 'success', 'message' => 'Dokumen Anda berhasil dikirim! Admin/Operator akan segera meninjaunya.'];
                        header("Location: portal.php");
                        exit;
                    } else {
                        $status = 'error'; $pesan = 'Gagal menyimpan dokumen ke server.';
                    }
                } else {
                    $status = 'error'; $pesan = 'Ukuran file melebihi batas 2MB.';
                }
            } else {
                $status = 'error'; $pesan = 'Format file tidak valid. Gunakan format PDF, JPG, atau PNG.';
            }
        } else {
            $status = 'error'; $pesan = 'Terjadi kesalahan saat mengunggah dokumen. Pastikan ukuran file memenuhi standar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Upload Dokumen - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        body { background-color: #f4f7f9; }
        .form-container { width: 95%; max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        @media (max-width: 767.98px) {
            .form-container { width: 100%; margin: 10px auto; padding: 20px 15px; border-radius: 8px; }
            h3 { font-size: 1.3rem !important; }
            .form-action-buttons { flex-direction: column-reverse; width: 100%; }
            .form-action-buttons a, .form-action-buttons button { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container animate-fade-up">
            <div class="text-center border-bottom pb-3 mb-4">
                <img src="logo.png?t=<?= time() ?>" alt="Logo" width="60" class="mb-2" onerror="this.style.display='none'">
                <h3 class="fw-bold" style="color: #0b214a;">Layanan Upload Dokumen Warga</h3>
                <p class="text-muted mb-0">Unggah berkas kependudukan yang diperlukan ke pemerintah desa.</p>
            </div>

            <?php if ($pesan && $status == 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal:</strong> <?= htmlspecialchars($pesan) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <h5 class="fw-bold text-primary mb-3 mt-4 border-start border-4 border-primary ps-2">Informasi File Dokumen</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-bold small">Nomor Induk Kependudukan (NIK) *</label><input type="text" name="nik" class="form-control bg-light" minlength="16" maxlength="16" pattern="\d{16}" title="Harus 16 digit angka" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold small">Nama Pemilik Dokumen *</label><input type="text" name="nama_pemilik" class="form-control bg-light" required></div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Jenis Dokumen *</label>
                        <select name="jenis_dokumen" class="form-select bg-light" required>
                            <option value="" hidden>Pilih...</option><option value="Kartu Keluarga">Kartu Keluarga (KK)</option><option value="KTP">KTP</option><option value="Akta Kelahiran">Akta Kelahiran</option><option value="Akta Nikah">Akta Nikah</option><option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6"><label class="form-label fw-bold small">File Dokumen (PDF, JPG, PNG) *</label><input type="file" name="file_dokumen" class="form-control bg-light" accept=".pdf,.jpg,.jpeg,.png" required><div class="form-text">Maksimal ukuran file: 2MB</div></div>
                    <div class="col-md-12"><label class="form-label fw-bold small">Keterangan Tambahan (Opsional)</label><textarea name="keterangan" class="form-control bg-light" rows="3" placeholder="Keterangan singkat, misalnya untuk keperluan pengajuan permohonan..."></textarea></div>
                </div>

                <hr class="my-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 form-action-buttons">
                    <a href="portal.php" class="btn btn-outline-secondary px-4 fw-bold"><i class="fa-solid fa-arrow-left me-2"></i> Batal / Kembali</a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow">Kirim Dokumen <i class="fa-solid fa-cloud-arrow-up ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3 mt-auto w-100">
        <div class="container">
            <div class="row g-4 mb-4 text-center text-lg-start">
                <div class="col-lg-5 text-center text-lg-start">
                    <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-center justify-content-lg-start gap-2">
                        <img src="logo.png?t=<?= time() ?>" width="45" alt="Logo" onerror="this.style.display='none'"> Pemerintah Desa Hilifalago
                    </h5>
                    <p class="text-white-50 pe-lg-4" style="line-height: 1.6;">Website resmi Pemerintah Desa Hilifalago, Kecamatan Onolalu, Kabupaten Nias Selatan, sebagai pusat informasi dan pelayanan publik digital.</p>
                </div>
                <div class="col-lg-3 col-md-6 footer-link text-center text-lg-start">
                    <h5 class="fw-bold mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled text-white-50" style="line-height: 2;">
                        <li><a href="portal.php" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li><a href="galeri_publik.php" class="text-white-50 text-decoration-none">Galeri Kegiatan</a></li>
                        <li><a href="upload_dokumen.php" class="text-white-50 text-decoration-none">Layanan Dokumen</a></li>
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Login Admin</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 text-center text-lg-start">
                    <h5 class="fw-bold mb-3">Kontak & Alamat</h5>
                    <ul class="list-unstyled text-white-50" style="line-height: 1.8;">
                        <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i> Kantor Kepala Desa Hilifalago, 22865</li>
                        <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i> pemdes@hilifalago.desa.id</li>
                        <li class="mb-2"><i class="fa-brands fa-whatsapp me-2 fs-5 align-middle"></i> +62 812-3456-7890</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center text-white-50 pt-2 small">
                <p class="mb-0">Copyright &copy; Pemerintah Desa Hilifalago <?= date('Y') ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>