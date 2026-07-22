<?php
require_once 'auth_check.php';
require_once 'koneksi.php';
$pesan = ''; $status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = trim($_POST['nik']);
    $no_kk = trim($_POST['no_kk']);

    if (strlen($nik) !== 16 || !ctype_digit($nik)) {
        $status = 'error';
        $pesan = 'Format NIK tidak valid. NIK harus 16 digit angka.';
    } elseif (strlen($no_kk) !== 16 || !ctype_digit($no_kk)) {
        $status = 'error';
        $pesan = 'Format No. KK tidak valid. No. KK harus 16 digit angka.';
    } else {
        try {
            $sql = "INSERT INTO penduduk (nik, no_kk, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, pendidikan, pekerjaan, status_perkawinan, alamat, rt, rw, status_penduduk) 
                    VALUES (:nik, :no_kk, :nama_lengkap, :tempat_lahir, :tanggal_lahir, :jenis_kelamin, :agama, :pendidikan, :pekerjaan, :status_perkawinan, :alamat, :rt, :rw, :status_penduduk)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nik' => $nik, ':no_kk' => $no_kk, ':nama_lengkap' => $_POST['nama_lengkap'],
                ':tempat_lahir' => $_POST['tempat_lahir'], ':tanggal_lahir' => $_POST['tanggal_lahir'],
                ':jenis_kelamin' => $_POST['jenis_kelamin'], ':agama' => $_POST['agama'],
                ':pendidikan' => $_POST['pendidikan'], ':pekerjaan' => $_POST['pekerjaan'],
                ':status_perkawinan' => $_POST['status_perkawinan'], ':alamat' => $_POST['alamat'],
                ':rt' => $_POST['rt'], ':rw' => $_POST['rw'], ':status_penduduk' => $_POST['status_penduduk']
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

            $_SESSION['alert'] = ['status' => 'success', 'message' => 'Data Penduduk Baru Berhasil Ditambahkan!'];
            header("Location: data_penduduk.php");
            exit;
        } catch (PDOException $e) {
            $status = 'error';
            $pesan = ($e->getCode() == 23000) ? "Data NIK sudah terdaftar dalam sistem kami." : "Terjadi kesalahan sistem. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tambah Data Penduduk - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-9 col-lg-10 d-flex flex-column" style="height: 100vh; overflow-x: hidden;">
                <?php include 'topbar.php'; ?>
                <div class="flex-grow-1 d-flex flex-column" style="overflow: hidden;">
                    <div class="pt-0 pb-0 px-3 d-flex flex-column flex-grow-1" style="overflow: hidden;">
                        <div class="card shadow-sm glass-card border-0 mb-0 mt-0 flex-grow-1 d-flex flex-column" style="max-height: 100%; overflow: hidden;">
                            <div class="card-header bg-transparent border-bottom pt-3 px-4 pb-2">
                                <h5 class="fw-bold mb-0"><i class="fa-solid fa-user-plus me-2 text-gradient"></i>Tambah Data Penduduk</h5>
                            </div>
                            <div class="card-body p-4" style="overflow-y: auto;">
                                <?php if ($pesan && $status == 'error'): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fa-solid fa-triangle-exclamation me-2"></i>Peringatan:</strong> <?= htmlspecialchars($pesan) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <?php endif; ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="setting-block">
                                        <h6 class="fw-bold"><i class="fa-solid fa-id-card me-2"></i>Identitas Utama</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">NIK *</label>
                                                <input type="text" name="nik" class="form-control" minlength="16" maxlength="16" pattern="\d{16}" placeholder="16 digit NIK" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">No. KK *</label>
                                                <input type="text" name="no_kk" class="form-control" minlength="16" maxlength="16" pattern="\d{16}" placeholder="16 digit No KK" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Nama Lengkap *</label>
                                                <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama sesuai KTP" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Pas Foto (Opsional)</label>
                                                <input type="file" name="foto_warga" class="form-control" accept="image/jpeg,image/png,image/jpg">
                                                <div class="form-text">Format JPG/PNG. Maks 2MB.</div>
                                        </div>

                                    <div class="setting-block">
                                        <h6 class="fw-bold"><i class="fa-solid fa-calendar-days me-2"></i>Data Personal</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Tempat Lahir *</label>
                                                <input type="text" name="tempat_lahir" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Tanggal Lahir *</label>
                                                <input type="date" name="tanggal_lahir" class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Jenis Kelamin *</label>
                                                <select name="jenis_kelamin" class="form-select" required>
                                                    <option value="" hidden>Pilih...</option>
                                                    <option value="Laki-laki">Laki-laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Agama *</label>
                                                <select name="agama" class="form-select" required>
                                                    <option value="" hidden>Pilih...</option>
                                                    <option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Status Perkawinan *</label>
                                                <select name="status_perkawinan" class="form-select" required>
                                                    <option value="" hidden>Pilih...</option>
                                                    <option>Belum Kawin</option><option>Kawin</option><option>Cerai Hidup</option><option>Cerai Mati</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Pendidikan *</label>
                                                <select name="pendidikan" class="form-select" required>
                                                    <option value="" hidden>Pilih...</option>
                                                    <option>Tidak/Belum Sekolah</option><option>SD/Sederajat</option><option>SMP/Sederajat</option>
                                                    <option>SMA/Sederajat</option><option>Diploma (D1-D3)</option><option>Sarjana (S1/D4)</option><option>Pascasarjana (S2/S3)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Pekerjaan</label>
                                                <input type="text" name="pekerjaan" class="form-control" placeholder="Contoh: Petani">
                                            </div>
                                    </div>

                                    <div class="setting-block">
                                        <h6 class="fw-bold"><i class="fa-solid fa-location-dot me-2"></i>Alamat</h6>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Alamat Lengkap *</label>
                                                <textarea name="alamat" class="form-control" rows="2" required></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Dusun *</label>
                                                <input type="text" name="rt" class="form-control" required>
                                                <input type="hidden" name="rw" value="-">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Status *</label>
                                                <select name="status_penduduk" class="form-select" required>
                                                    <option value="Tetap">Tetap</option><option value="Pendatang">Pendatang</option><option value="Pindah">Pindah</option><option value="Meninggal">Meninggal</option>
                                                </select>
                                            </div>
                                    </div>

                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="data_penduduk.php" class="btn btn-outline-secondary px-4">Batal</a>
                                        <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                                    </div>
                                </form>
                            </div>
                    </div>
                <div class="mt-auto text-center small footer-marquee-official">
                    <span>Copyright &copy; Desa Hilifalago <?= date('Y') ?></span>
                </div>
        </div>
    <?php if ($pesan): ?>
    <script>Swal.fire({icon:'<?= $status ?>',title:'<?= ($status=="success")?"Berhasil":"Terjadi Kesalahan" ?>',text:'<?= addslashes($pesan) ?>',showConfirmButton:true,timer:4000});</script>
    <?php endif; ?>
    <script>
    function updateClock() {
        const now = new Date();
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('clock-container').innerHTML = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} | ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}`;
    }
    setInterval(updateClock, 1000); updateClock();
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar'), sidebarToggle = document.getElementById('sidebarToggleTop'), sidebarOverlay = document.getElementById('sidebarOverlay');
        if (sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', e => { e.preventDefault(); sidebar.classList.toggle('show'); sidebarOverlay.classList.toggle('show'); });
            sidebarOverlay.addEventListener('click', () => { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); });
            let touchstartX = 0, touchendX = 0;
            document.addEventListener('touchstart', e => touchstartX = e.changedTouches[0].screenX, {passive: true});
            document.addEventListener('touchend', e => {
                touchendX = e.changedTouches[0].screenX;
                if (touchendX > touchstartX + 50 && touchstartX <= 50) { sidebar.classList.add('show'); sidebarOverlay.classList.add('show'); }
                else if (touchstartX > touchendX + 50 && sidebar.classList.contains('show')) { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); }
            }, {passive: true});
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
