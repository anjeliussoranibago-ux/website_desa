<?php
require_once 'auth_check.php';
require_once 'koneksi.php';
$pesan = ''; $status = '';

if (!isset($_GET['nik'])) { header("Location: data_penduduk.php"); exit; }
$nik = $_GET['nik'];
$stmt = $pdo->prepare("SELECT * FROM penduduk WHERE nik = :nik");
$stmt->execute([':nik' => $nik]);
$data = $stmt->fetch();
if (!$data) { header("Location: data_penduduk.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik_baru = trim($_POST['nik_baru']);
    $no_kk = trim($_POST['no_kk']);

    if (strlen($nik_baru) !== 16 || !ctype_digit($nik_baru)) {
        $status = 'error';
        $pesan = 'Format NIK tidak valid. NIK harus 16 digit angka.';
    } elseif (strlen($no_kk) !== 16 || !ctype_digit($no_kk)) {
        $status = 'error';
        $pesan = 'Format No. KK tidak valid. No. KK harus 16 digit angka.';
    } else {
        try {
            $sql = "UPDATE penduduk SET nik=:nik_baru, no_kk=:no_kk, nama_lengkap=:nama_lengkap, tempat_lahir=:tempat_lahir, tanggal_lahir=:tanggal_lahir, jenis_kelamin=:jenis_kelamin, agama=:agama, pendidikan=:pendidikan, pekerjaan=:pekerjaan, status_perkawinan=:status_perkawinan, alamat=:alamat, rt=:rt, rw=:rw, status_penduduk=:status_penduduk WHERE nik=:nik_lama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nik_lama' => $nik,
                ':nik_baru' => $nik_baru,
                ':no_kk' => $no_kk, ':nama_lengkap' => $_POST['nama_lengkap'],
                ':tempat_lahir' => $_POST['tempat_lahir'], ':tanggal_lahir' => $_POST['tanggal_lahir'],
                ':jenis_kelamin' => $_POST['jenis_kelamin'], ':agama' => $_POST['agama'],
                ':pendidikan' => $_POST['pendidikan'], ':pekerjaan' => $_POST['pekerjaan'],
                ':status_perkawinan' => $_POST['status_perkawinan'], ':alamat' => $_POST['alamat'],
                ':rt' => $_POST['rt'], ':rw' => $_POST['rw'], ':status_penduduk' => $_POST['status_penduduk']
            ]);
            $status = 'success'; $pesan = 'Data penduduk berhasil diperbarui!';
            
            // Proses upload & pembaruan foto warga
            $foto_dir = 'foto_warga/';
            if (!is_dir($foto_dir)) mkdir($foto_dir, 0777, true);
            
            if ($nik !== $nik_baru && file_exists($foto_dir . $nik . '.jpg')) {
                rename($foto_dir . $nik . '.jpg', $foto_dir . $nik_baru . '.jpg');
            }
            if (isset($_FILES['foto_warga']) && $_FILES['foto_warga']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                $file_info = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($file_info, $_FILES['foto_warga']['tmp_name']);
                finfo_close($file_info);
                
                if (in_array($mime_type, $allowed_types) && $_FILES['foto_warga']['size'] <= 2097152) {
                    move_uploaded_file($_FILES['foto_warga']['tmp_name'], $foto_dir . $nik_baru . '.jpg');
                }
            }

            $nik = $nik_baru; // Perbarui NIK di memori ke NIK baru agar query di bawah tetap berjalan
            $stmt = $pdo->prepare("SELECT * FROM penduduk WHERE nik = :nik");
            $stmt->execute([':nik' => $nik]);
            $data = $stmt->fetch();
        } catch (PDOException $e) {
            $status = 'error'; $pesan = ($e->getCode() == 23000) ? "Data NIK sudah terdaftar dalam sistem kami, silakan periksa kembali." : "Terjadi kesalahan sistem saat memperbarui data. Silakan coba lagi nanti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Data Penduduk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        body {
            background-color: #f8f9fc !important;
            color: #5a5c69;
            font-family: 'Nunito', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: #4e73df !important;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%) !important;
            background-size: cover;
            z-index: 100;
            min-height: 100vh;
        }
        .sidebar .nav-item { margin-bottom: 0.3rem; }
        .sidebar .nav-link { color: rgba(255,255,255,.8) !important; padding: 0.85rem 1.15rem; margin: 0 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; letter-spacing: 0.3px; transition: all 0.3s ease; display: flex; align-items: center; }
        .sidebar .nav-link:hover { color: #ffffff !important; background: rgba(255,255,255,0.15); transform: translateX(5px); }
        .sidebar .nav-link.active { color: #4e73df !important; background: #ffffff !important; font-weight: 800; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .sidebar .nav-link i.me-2 { font-size: 1.2rem; width: 30px; text-align: center; transition: transform 0.3s ease; }
        .sidebar .nav-link:hover i.me-2 { transform: scale(1.2) rotate(5deg); }

        .glass-card {
            background: #ffffff !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem !important;
            color: #5a5c69 !important;
        }
        .glass-card.border-primary { border-left: 0.25rem solid #4e73df !important; }
        .glass-card h3, .glass-card h4, .glass-card h5, .glass-card h6 { font-weight: 700; text-shadow: none !important; }
        .glass-card .text-muted { color: #858796 !important; font-weight: 400; }
        .text-primary { color: #4e73df !important; }
        .glass-card .text-secondary { color: #4e73df !important; border-bottom: 2px solid #4e73df; padding-bottom: 5px; display: inline-block; margin-bottom: 15px; font-weight: bold; text-shadow: none !important; letter-spacing: normal; }
        
        .glass-card .form-label { color: #334155 !important; font-weight: 600; text-shadow: none; letter-spacing: normal; }
        .glass-card .form-control, .glass-card .form-select {
            background: #ffffff !important;
            border: 1px solid #d1d3e2 !important;
            color: #6e707e !important;
            font-weight: 500;
            text-shadow: none;
            border-radius: 0.35rem;
            padding: 0.45rem 0.85rem !important;
            box-shadow: none !important;
        }
        .glass-card .form-control::placeholder { color: #94a3b8 !important; }
        .glass-card .form-control:focus, .glass-card .form-select:focus {
            border-color: #bac8f3 !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }
        .glass-card select option { background: #ffffff; color: #1e293b; }
        .glass-card .form-control:disabled, .glass-card .form-control[readonly] { background: #f1f5f9 !important; border-color: #e2e8f0 !important; color: #64748b !important; }

        .setting-block {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            box-shadow: none;
            transition: border-color 0.2s ease;
        }
        .setting-block:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        /* Buttons */
        .btn-primary { background-color: #4e73df !important; border-color: #4e73df !important; box-shadow: none !important; border-radius: 4px; font-weight: 600; transition: all 0.2s; }
        .btn-primary:hover { background-color: #2e59d9 !important; border-color: #2653d4 !important; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important; }
        .btn-light { background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1 !important; box-shadow: none !important; border-radius: 4px; font-weight: 600; text-shadow: none; backdrop-filter: none; transition: all 0.2s; }
        .btn-light:hover { background: #e2e8f0 !important; color: #1e293b !important; transform: translateY(-1px); }
        
        .topbar { height: 4.375rem; background-color: #fff; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); margin-bottom: 0.5rem; }
        
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 0; left: -250px; width: 250px; height: 100vh; transition: all 0.3s ease-in-out; z-index: 1040 !important; }
            .sidebar.show { left: 0; box-shadow: 0.15rem 0 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
            .sidebar-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1030; opacity: 0; visibility: hidden; transition: all 0.3s ease-in-out; }
            .sidebar-overlay.show { opacity: 1; visibility: visible; }
        }

        .footer-marquee-official {
            background: #fff;
            border-top: 1px solid #e3e6f0;
            color: #858796;
            font-size: 0.8rem;
            padding: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php include 'sidebar.php'; ?>

            <div class="col-md-9 col-lg-10 d-flex flex-column" style="height: 100vh; overflow-x: hidden;">
                
                <?php include 'topbar.php'; ?>

                <div class="flex-grow-1 d-flex flex-column" style="overflow: hidden;">
                    <div class="pt-0 pb-0 px-3 d-flex flex-column flex-grow-1" style="overflow: hidden;">

                <div class="card shadow-sm glass-card border-0 mb-0 mt-0 flex-grow-1 d-flex flex-column" style="max-height: 100%; overflow: hidden; border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important;">
                    <div class="card-header bg-transparent border-bottom pt-3 px-4 pb-2">
                        <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Data Warga</h5>
                    </div>
                    <div class="card-body p-4" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                        <?php if ($pesan && $status == 'error'): ?>
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 5px solid #dc2626; background-color: #fef2f2; color: #dc2626;">
                                <strong><i class="fa-solid fa-triangle-exclamation me-2"></i>Peringatan:</strong> <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="setting-block">
                                <h6 class="fw-bold text-secondary"><i class="fa-solid fa-id-card me-2"></i>Identitas Utama</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Nomor Induk Kependudukan (NIK) *</label>
                                        <input type="text" name="nik_baru" class="form-control" minlength="16" maxlength="16" pattern="\d{16}" title="Pastikan NIK sesuai dengan KTP (16 digit)" value="<?= htmlspecialchars($data['nik']) ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Nomor Kartu Keluarga (KK) *</label>
                                        <input type="text" name="no_kk" class="form-control" minlength="16" maxlength="16" pattern="\d{16}" title="Pastikan No. KK sesuai (16 digit)" value="<?= htmlspecialchars($data['no_kk']) ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted mb-1">Nama Lengkap Sesuai KTP *</label>
                                        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" required>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Pas Foto Warga (Opsional, 3x4)</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded border shadow-sm d-flex justify-content-center align-items-center bg-light text-muted overflow-hidden" style="width: 60px; height: 80px; font-size: 0.8rem; font-weight: bold;">
                                                <?php if (file_exists("foto_warga/" . $data['nik'] . ".jpg")): ?>
                                                    <img id="previewWarga" src="foto_warga/<?= $data['nik'] ?>.jpg?t=<?= time() ?>" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                                    <span id="previewText" style="display: none;">3x4</span>
                                                <?php else: ?>
                                                    <img id="previewWarga" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                    <span id="previewText">3x4</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" name="foto_warga" class="form-control" accept="image/jpeg, image/png, image/jpg" onchange="previewImage(this, 'previewWarga', 'previewText')">
                                                <div class="form-text">Format: JPG/PNG. Otomatis dikompres jika kebesaran. Biarkan kosong jika tidak diubah.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="setting-block">
                                <h6 class="fw-bold text-secondary"><i class="fa-solid fa-calendar-days me-2"></i>Data Kelahiran & Personal</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Tempat Lahir *</label>
                                        <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($data['tempat_lahir']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Tanggal Lahir *</label>
                                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($data['tanggal_lahir']) ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted mb-1">Jenis Kelamin *</label>
                                        <select name="jenis_kelamin" class="form-select" required>
                                            <option value="Laki-laki" <?= $data['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="Perempuan" <?= $data['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted mb-1">Agama *</label>
                                        <select name="agama" class="form-select" required>
                                            <option value="Islam" <?= $data['agama'] == 'Islam' ? 'selected' : '' ?>>Islam</option><option value="Kristen" <?= $data['agama'] == 'Kristen' ? 'selected' : '' ?>>Kristen</option><option value="Katolik" <?= $data['agama'] == 'Katolik' ? 'selected' : '' ?>>Katolik</option><option value="Hindu" <?= $data['agama'] == 'Hindu' ? 'selected' : '' ?>>Hindu</option><option value="Buddha" <?= $data['agama'] == 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted mb-1">Status Perkawinan *</label>
                                        <select name="status_perkawinan" class="form-select" required>
                                            <option value="Belum Kawin" <?= $data['status_perkawinan'] == 'Belum Kawin' ? 'selected' : '' ?>>Belum Kawin</option><option value="Kawin" <?= $data['status_perkawinan'] == 'Kawin' ? 'selected' : '' ?>>Kawin</option><option value="Cerai Hidup" <?= $data['status_perkawinan'] == 'Cerai Hidup' ? 'selected' : '' ?>>Cerai Hidup</option><option value="Cerai Mati" <?= $data['status_perkawinan'] == 'Cerai Mati' ? 'selected' : '' ?>>Cerai Mati</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Pendidikan Terakhir *</label>
                                        <select name="pendidikan" class="form-select" required>
                                            <?php 
                                            $edu_opts = ['Tidak/Belum Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'Diploma (D1-D3)', 'Sarjana (S1/D4)', 'Pascasarjana (S2/S3)'];
                                            $cur_edu = $data['pendidikan'];
                                            if (!in_array($cur_edu, $edu_opts) && !empty($cur_edu)) {
                                                echo "<option value=\"".htmlspecialchars($cur_edu)."\" selected>".htmlspecialchars($cur_edu)."</option>";
                                            } else {
                                                echo "<option value=\"\" hidden>Pilih...</option>";
                                            }
                                            foreach($edu_opts as $opt) {
                                                $sel = ($cur_edu == $opt) ? 'selected' : '';
                                                echo "<option value=\"$opt\" $sel>$opt</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Pekerjaan</label>
                                        <input type="text" name="pekerjaan" class="form-control" value="<?= htmlspecialchars($data['pekerjaan']) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="setting-block">
                                <h6 class="fw-bold text-secondary"><i class="fa-solid fa-location-dot me-2"></i>Alamat & Domisili</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted mb-1">Alamat Lengkap *</label>
                                        <textarea name="alamat" class="form-control" rows="2" required><?= htmlspecialchars($data['alamat']) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Dusun</label>
                                        <input type="text" name="rt" class="form-control" value="<?= htmlspecialchars($data['rt']) ?>">
                                        <input type="hidden" name="rw" value="<?= htmlspecialchars($data['rw'] ?? '-') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Status Penduduk *</label>
                                        <select name="status_penduduk" class="form-select" required>
                                            <option value="Tetap" <?= $data['status_penduduk'] == 'Tetap' ? 'selected' : '' ?>>Tetap</option><option value="Pendatang" <?= $data['status_penduduk'] == 'Pendatang' ? 'selected' : '' ?>>Pendatang</option><option value="Pindah" <?= $data['status_penduduk'] == 'Pindah' ? 'selected' : '' ?>>Pindah</option><option value="Meninggal" <?= $data['status_penduduk'] == 'Meninggal' ? 'selected' : '' ?>>Meninggal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="data_penduduk.php" class="btn btn-light border px-4">Batal</a>
                                <button type="submit" class="btn btn-primary px-4">Update Data</button>
                            </div>
                        </form>
                    </div>
                </div>
                    </div>
                </div>
                <div class="mt-auto text-center small footer-marquee-official">
                    <span>Copyright &copy; Desa Hilifalago <?= date('Y') ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php if ($pesan): ?>
    <script>
        <?php if ($status == 'success'): ?>
        window.history.replaceState(null, null, "?nik=<?= $nik ?>");
        <?php endif; ?>

        Swal.fire({
            icon: '<?= $status ?>',
            title: '<?= ($status == "success") ? "Berhasil" : "Terjadi Kesalahan" ?>',
            text: '<?= addslashes($pesan) ?>',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
    <?php endif; ?>
    <script>
        function previewImage(input, imgId, textId) {
            const preview = document.getElementById(imgId);
            const text = document.getElementById(textId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (text) text.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = ''; preview.style.display = 'none';
                if (text) text.style.display = 'block';
            }
        }

        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const day = days[now.getDay()];
            const date = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            document.getElementById('clock-container').innerHTML = `${day}, ${date} ${month} ${year} | ${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggleTop');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });

                // Fitur Swipe untuk Mobile
                let touchstartX = 0;
                let touchendX = 0;
                document.addEventListener('touchstart', e => touchstartX = e.changedTouches[0].screenX, {passive: true});
                document.addEventListener('touchend', e => {
                    touchendX = e.changedTouches[0].screenX;
                    if (touchendX > touchstartX + 50 && touchstartX <= 50) {
                        sidebar.classList.add('show');
                        sidebarOverlay.classList.add('show');
                    } else if (touchstartX > touchendX + 50 && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                }, {passive: true});

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
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>