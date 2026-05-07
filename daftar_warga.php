<?php
session_start();
require_once 'koneksi.php';
$pesan = ''; $status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = trim($_POST['nik']);
    $no_kk = trim($_POST['no_kk']);

    if (strlen($nik) !== 16 || !ctype_digit($nik)) {
        $status = 'error'; $pesan = 'Format NIK tidak valid. NIK harus 16 digit angka.';
    } elseif (strlen($no_kk) !== 16 || !ctype_digit($no_kk)) {
        $status = 'error'; $pesan = 'Format No. KK tidak valid. No. KK harus 16 digit angka.';
    } else {
        try {
            // Status penduduk otomatis diset menjadi "Menunggu Verifikasi"
            $sql = "INSERT INTO penduduk (nik, no_kk, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, pendidikan, pekerjaan, status_perkawinan, alamat, rt, rw, status_penduduk) 
                    VALUES (:nik, :no_kk, :nama_lengkap, :tempat_lahir, :tanggal_lahir, :jenis_kelamin, :agama, :pendidikan, :pekerjaan, :status_perkawinan, :alamat, :rt, '-', 'Menunggu Verifikasi')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nik' => $nik, ':no_kk' => $no_kk, ':nama_lengkap' => $_POST['nama_lengkap'],
                ':tempat_lahir' => $_POST['tempat_lahir'], ':tanggal_lahir' => $_POST['tanggal_lahir'],
                ':jenis_kelamin' => $_POST['jenis_kelamin'], ':agama' => $_POST['agama'],
                ':pendidikan' => $_POST['pendidikan'], ':pekerjaan' => $_POST['pekerjaan'],
                ':status_perkawinan' => $_POST['status_perkawinan'], ':alamat' => $_POST['alamat'],
                ':rt' => $_POST['rt']
            ]);

            if (isset($_FILES['foto_warga']) && $_FILES['foto_warga']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                $file_info = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($file_info, $_FILES['foto_warga']['tmp_name']);
                finfo_close($file_info);
                
                if (in_array($mime_type, $allowed_types) && $_FILES['foto_warga']['size'] <= 2097152) {
                    $foto_dir = 'foto_warga/';
                    if (!is_dir($foto_dir)) mkdir($foto_dir, 0777, true);
                    move_uploaded_file($_FILES['foto_warga']['tmp_name'], $foto_dir . $nik . '.jpg');
                }
            }

            $_SESSION['alert'] = [
                'status' => 'success',
                'message' => 'Pendaftaran berhasil! Data Anda sedang Menunggu Verifikasi dari perangkat desa.'
            ];
            header("Location: portal.php");
            exit;
        } catch (PDOException $e) {
            $status = 'error';
            $pesan = ($e->getCode() == 23000) ? "Data NIK sudah terdaftar dalam sistem kami! Hubungi Kantor Desa jika ini sebuah kesalahan." : "Terjadi kesalahan sistem saat menyimpan pendaftaran. Silakan coba lagi nanti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Daftar Warga - Desa Hilifalago</title>
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
            .btn-lg { padding: 0.75rem 1rem !important; font-size: 1rem !important; }
            .form-action-buttons { flex-direction: column-reverse; width: 100%; }
            .form-action-buttons a, .form-action-buttons button { width: 100%; text-align: center; }
        }
        .footer-link a { transition: color 0.2s, transform 0.2s; display: inline-block; }
        .footer-link a:hover { color: #ffffff !important; transform: translateX(5px); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container flex-grow-1">
        <div class="form-container animate-fade-up">
            <div class="text-center border-bottom pb-3 mb-4">
                <img src="logo.png?t=<?= time() ?>" alt="Logo" width="60" class="mb-2" onerror="this.style.display='none'">
                <h3 class="fw-bold" style="color: #0b214a;">Formulir Pendaftaran Warga Mandiri</h3>
                <p class="text-muted mb-0">Lengkapi data diri Anda di bawah ini dengan benar sesuai KTP dan KK.</p>
            </div>

            <?php if ($pesan && $status == 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Peringatan:</strong> <?= htmlspecialchars($pesan) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <h5 class="fw-bold text-primary mb-3 mt-4 border-start border-4 border-primary ps-2">A. Identitas Utama</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nomor Induk Kependudukan (NIK) *</label>
                        <input type="text" name="nik" class="form-control bg-light" minlength="16" maxlength="16" pattern="\d{16}" title="Harus 16 digit angka" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nomor Kartu Keluarga (KK) *</label>
                        <input type="text" name="no_kk" class="form-control bg-light" minlength="16" maxlength="16" pattern="\d{16}" title="Harus 16 digit angka" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Nama Lengkap Sesuai KTP *</label>
                        <input type="text" name="nama_lengkap" class="form-control bg-light" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Upload Pas Foto Diri (Otomatis dikompres jika ukuran terlalu besar)</label>
                        <input type="file" name="foto_warga" class="form-control bg-light" accept="image/jpeg, image/png, image/jpg">
                    </div>
                </div>

                <h5 class="fw-bold text-primary mb-3 mt-5 border-start border-4 border-primary ps-2">B. Data Personal</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tempat Lahir *</label>
                        <input type="text" name="tempat_lahir" class="form-control bg-light" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" class="form-control bg-light" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" class="form-select bg-light" required>
                            <option value="" hidden>Pilih...</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Agama *</label>
                        <select name="agama" class="form-select bg-light" required>
                            <option value="" hidden>Pilih...</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Status Perkawinan *</label>
                        <select name="status_perkawinan" class="form-select bg-light" required>
                            <option value="" hidden>Pilih...</option><option value="Belum Kawin">Belum Kawin</option><option value="Kawin">Kawin</option><option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Pendidikan Terakhir *</label>
                        <select name="pendidikan" class="form-select bg-light" required>
                            <option value="" hidden>Pilih...</option>
                            <option value="Tidak/Belum Sekolah">Tidak/Belum Sekolah</option>
                            <option value="SD/Sederajat">SD/Sederajat</option>
                            <option value="SMP/Sederajat">SMP/Sederajat</option>
                            <option value="SMA/Sederajat">SMA/Sederajat</option>
                            <option value="Diploma (D1-D3)">Diploma (D1-D3)</option>
                            <option value="Sarjana (S1/D4)">Sarjana (S1/D4)</option>
                            <option value="Pascasarjana (S2/S3)">Pascasarjana (S2/S3)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control bg-light">
                    </div>
                </div>

                <h5 class="fw-bold text-primary mb-3 mt-5 border-start border-4 border-primary ps-2">C. Alamat KTP</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Alamat Lengkap *</label>
                        <textarea name="alamat" class="form-control bg-light" rows="2" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Dusun *</label>
                        <input type="text" name="rt" class="form-control bg-light" placeholder="Contoh: 1, 2, atau Nama Dusun" required>
                    </div>
                </div>

                <hr class="my-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 form-action-buttons">
                    <a href="portal.php" class="btn btn-outline-secondary px-4 fw-bold"><i class="fa-solid fa-arrow-left me-2"></i> Batal / Kembali</a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow">Kirim Data Pendaftaran <i class="fa-solid fa-paper-plane ms-2"></i></button>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fitur Auto-Kompresi Foto di sisi Klien (Browser)
        document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                if (file.size > 1024 * 1024) {
                    Swal.fire({title: 'Mengompresi Foto...', text: 'Sedang mengecilkan ukuran gambar...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                    
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function(event) {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = function() {
                            const canvas = document.createElement('canvas');
                            const MAX_WIDTH = 1000; const MAX_HEIGHT = 1000;
                            let width = img.width; let height = img.height;

                            if (width > height) { if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; } } 
                            else { if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; } }
                            
                            canvas.width = width; canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            
                            canvas.toBlob(function(blob) {
                                const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", { type: 'image/jpeg', lastModified: Date.now() });
                                const dataTransfer = new DataTransfer(); dataTransfer.items.add(newFile);
                                input.files = dataTransfer.files;
                                Swal.close();
                            }, 'image/jpeg', 0.8);
                        }
                    };
                }
            });
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>