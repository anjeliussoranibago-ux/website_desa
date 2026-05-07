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
        .glass-card .form-text { color: #64748b !important; text-shadow: none; font-weight: 500; }
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