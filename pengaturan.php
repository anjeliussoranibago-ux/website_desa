<?php
session_start();
require_once 'koneksi.php';
$pesan = ''; 
$status = '';

$profil_file = 'profil.json';
$profil_data = [
    'nama_kades' => 'ANJELIUS SORANI BAGO S.KOM',
    'visi' => 'Mewujudkan Desa Hilifalago yang mandiri, sejahtera, dan berbudaya melalui tata kelola pemerintahan yang baik.',
    'misi' => "Meningkatkan kualitas pelayanan administrasi kepada masyarakat.\nMendorong partisipasi warga dalam pembangunan desa.\nMengembangkan potensi ekonomi lokal secara maksimal."
];

if (file_exists($profil_file)) {
    $profil_data = json_decode(file_get_contents($profil_file), true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pesan_list = [];
    $final_status = 'info';
    $action_taken = false;

    if (isset($_POST['visi']) && isset($_POST['misi']) && isset($_POST['nama_kades'])) {
        if ($profil_data['visi'] != trim($_POST['visi']) || $profil_data['misi'] != trim($_POST['misi']) || ($profil_data['nama_kades'] ?? '') != trim($_POST['nama_kades'])) {
            $profil_data['nama_kades'] = trim($_POST['nama_kades']);
            $profil_data['visi'] = trim($_POST['visi']);
            $profil_data['misi'] = trim($_POST['misi']);
            if (file_put_contents($profil_file, json_encode($profil_data, JSON_PRETTY_PRINT))) {
                $pesan_list[] = 'Profil Desa diperbarui.';
                $final_status = 'success';
            } else {
                $pesan_list[] = 'Gagal menyimpan Profil Desa.';
                $final_status = 'error';
            }
            $action_taken = true;
        }
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($_FILES['logo']['tmp_name'], 'logo.png')) $pesan_list[] = 'Logo diperbarui.';
        $action_taken = true;
        $final_status = 'success';
    }

    if (isset($_FILES['foto_kades']) && $_FILES['foto_kades']['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($_FILES['foto_kades']['tmp_name'], 'foto_kades.jpg')) $pesan_list[] = 'Foto Kades diperbarui.';
        $action_taken = true;
        $final_status = 'success';
    }

    if (isset($_FILES['ttd_kades']) && $_FILES['ttd_kades']['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($_FILES['ttd_kades']['tmp_name'], 'ttd_kades.png')) $pesan_list[] = 'Tanda Tangan Kades diperbarui.';
        $action_taken = true;
        $final_status = 'success';
    }

    if ($action_taken) {
        $pesan = implode(' ', $pesan_list);
        $status = $final_status;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Web - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            
            <?php include 'sidebar.php'; ?>

            <!-- Main Content Area -->
            <div class="col-md-9 col-lg-10 d-flex flex-column" style="height: 100vh; overflow-x: hidden;">
                
                <?php include 'topbar.php'; ?>

                <div class="flex-grow-1 d-flex flex-column" style="overflow: hidden;">
                    <div class="pt-0 pb-0 px-3 d-flex flex-column flex-grow-1" style="overflow: hidden;">

                <!-- Settings Form Card -->
                <div class="card shadow-sm glass-card border-0 mb-0 mt-0 flex-grow-1 d-flex flex-column" style="max-height: 100%; overflow: hidden; border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important;">
                    <div class="card-header bg-transparent border-bottom pt-3 px-4 pb-2">
                        <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-gear me-2"></i>Pengaturan Web Desa</h5>
                    </div>
                    <div class="card-body p-4" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <!-- Visi & Misi Block -->
                            <div class="setting-block">
                                <h6 class="fw-bold text-secondary"><i class="fa-solid fa-bullseye me-2"></i>Profil Identitas Desa</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted mb-1">Nama Kepala Desa *</label>
                                        <input type="text" name="nama_kades" class="form-control" value="<?= htmlspecialchars($profil_data['nama_kades'] ?? 'ANJELIUS SORANI BAGO S.KOM') ?>" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted mb-1">Visi Desa *</label>
                                        <textarea name="visi" class="form-control" rows="3" required><?= htmlspecialchars($profil_data['visi']) ?></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted mb-1">Misi Desa (Pisahkan setiap poin dengan baris baru / Enter) *</label>
                                        <textarea name="misi" class="form-control" rows="5" required><?= htmlspecialchars($profil_data['misi']) ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Logo Upload Block -->
                            <div class="setting-block">
                                <h6 class="fw-bold text-secondary"><i class="fa-regular fa-image me-2"></i>Logo Desa</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted mb-1">Unggah Logo Baru (Format .png)</label>
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <div class="rounded border shadow-sm d-flex justify-content-center align-items-center bg-light overflow-hidden p-2" style="width: 80px; height: 80px;">
                                                <img id="previewLogo" src="logo.png?t=<?= time() ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" name="logo" class="form-control" accept="image/png" onchange="previewImage(this, 'previewLogo')">
                                                <div class="form-text mt-1">Disarankan gambar memiliki latar belakang transparan. Kosongkan jika tidak ingin diubah.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto & TTD Kades Upload Block -->
                            <div class="setting-block">
                                <h6 class="fw-bold text-secondary"><i class="fa-solid fa-camera-retro me-2"></i>Foto & Tanda Tangan Kepala Desa</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Unggah Foto Kades Baru (.jpg)</label>
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <img id="previewKades" src="foto_kades.jpg?t=<?= time() ?>" alt="Foto Kades" class="rounded border shadow-sm" style="width: 70px; height: 90px; object-fit: cover;">
                                            <div class="flex-grow-1">
                                                <input type="file" name="foto_kades" class="form-control" accept="image/jpeg, image/jpg" onchange="previewImage(this, 'previewKades')">
                                                <div class="form-text mt-1">Kosongkan jika tidak ingin diubah.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Unggah Tanda Tangan (.png Transparan)</label>
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <img id="previewTtd" src="ttd_kades.png?t=<?= time() ?>" alt="TTD Kades" class="rounded border shadow-sm bg-light p-1" style="width: 120px; height: 60px; object-fit: contain;">
                                            <div class="flex-grow-1">
                                                <input type="file" name="ttd_kades" class="form-control" accept="image/png" onchange="previewImage(this, 'previewTtd')">
                                                <div class="form-text mt-1">Kosongkan jika tidak ingin diubah.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="index.php" class="btn btn-light border px-4">Batal</a>
                                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                            </div>
                            <!-- End Form Actions -->
                            
                        </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-auto text-center small footer-marquee-official">
                    <span>Copyright &copy; Desa Hilifalago <?= date('Y') ?></span>
                </div>
            </div>
            <!-- End Main Content Area -->
            
        </div>
    </div>
    <?php if ($pesan): ?>
    <script>
        Swal.fire({
            icon: '<?= $status ?>',
            title: '<?= ($status == "success") ? "Berhasil" : "Info" ?>',
            text: '<?= addslashes($pesan) ?>',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
    <?php endif; ?>
    <script>
        function previewImage(input, imgId) {
            const preview = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
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
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>