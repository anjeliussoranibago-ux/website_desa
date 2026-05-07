<?php
require_once 'auth_check.php';
require_once 'koneksi.php';

if (!isset($_GET['nik'])) { 
    header("Location: data_penduduk.php"); 
    exit; 
}

$nik = $_GET['nik'];
$stmt = $pdo->prepare("SELECT * FROM penduduk WHERE nik = :nik");
$stmt->execute([':nik' => $nik]);
$data = $stmt->fetch();

if (!$data) { 
    header("Location: data_penduduk.php"); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Penduduk - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .glass-card h3, .glass-card h4, .glass-card h5, .glass-card h6 { font-weight: 700; text-shadow: none !important; }
        .glass-card .text-muted { color: #858796 !important; font-weight: 400; }
        .text-primary { color: #4e73df !important; }
        .glass-card .text-secondary { color: #4e73df !important; border-bottom: 2px solid #4e73df; padding-bottom: 5px; display: inline-block; margin-bottom: 15px; font-weight: bold; text-shadow: none !important; letter-spacing: normal; }
        
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
        
        .detail-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            height: 100%;
            transition: all 0.2s ease;
        }
        .detail-box:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }
        .data-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            font-weight: 700;
        }
        .data-value {
            font-size: 1.05rem;
            color: #1e293b;
            font-weight: 600;
            word-wrap: break-word;
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
                            <div class="card-header bg-transparent border-bottom pt-3 px-4 pb-2 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-user me-2"></i>Detail Profil Warga</h5>
                                <div>
                                    <a href="data_penduduk.php" class="btn btn-sm btn-light border px-3 me-1" title="Kembali ke Data Penduduk"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
                                    <a href="cetak_biodata.php?nik=<?= $data['nik'] ?>" target="_blank" class="btn btn-sm btn-danger px-3 me-1" title="Cetak Biodata (PDF)"><i class="fa-solid fa-file-pdf me-1"></i> Cetak PDF</a>
                                    <a href="edit_penduduk.php?nik=<?= $data['nik'] ?>" class="btn btn-sm btn-primary px-3" title="Edit Data Warga"><i class="fa-solid fa-pen-to-square me-1"></i> Edit</a>
                                </div>
                            </div>
                            <div class="card-body p-4" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                                
                                <div class="setting-block">
                                    <h6 class="fw-bold text-secondary"><i class="fa-solid fa-id-card me-2"></i>Identitas Utama</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3 col-lg-2 text-center text-md-start">
                                            <?php if (file_exists("foto_warga/" . $data['nik'] . ".jpg")): ?>
                                                <img src="foto_warga/<?= $data['nik'] ?>.jpg?t=<?= time() ?>" alt="Foto Warga" class="img-thumbnail shadow-sm rounded" style="width: 130px; height: 173px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="img-thumbnail shadow-sm rounded d-flex justify-content-center align-items-center bg-light text-muted mx-auto mx-md-0" style="width: 130px; height: 173px; font-size: 0.9rem; font-weight: bold; border: 2px dashed #cbd5e1;">Foto 3x4<br>Belum Ada</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-9 col-lg-10">
                                            <div class="row g-3 h-100">
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="detail-box">
                                                        <span class="data-label"><i class="fa-solid fa-address-card me-2 fs-6"></i> NIK</span>
                                                        <div class="data-value text-primary"><?= htmlspecialchars($data['nik']) ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="detail-box">
                                                        <span class="data-label"><i class="fa-solid fa-file-lines me-2 fs-6"></i> No. KK</span>
                                                        <div class="data-value"><?= htmlspecialchars($data['no_kk']) ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-4">
                                                    <div class="detail-box">
                                                        <span class="data-label"><i class="fa-solid fa-user me-2 fs-6"></i> Nama Lengkap</span>
                                                        <div class="data-value"><?= htmlspecialchars($data['nama_lengkap']) ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="setting-block">
                                    <h6 class="fw-bold text-secondary"><i class="fa-solid fa-calendar-days me-2"></i>Data Kelahiran & Personal</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-hospital me-2 fs-6"></i> Tempat Lahir</span>
                                                <div class="data-value"><?= htmlspecialchars($data['tempat_lahir']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-cake-candles me-2 fs-6"></i> Tanggal Lahir</span>
                                                <div class="data-value"><?= date('d F Y', strtotime($data['tanggal_lahir'])) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-venus-mars me-2 fs-6"></i> Jenis Kelamin</span>
                                                <div class="data-value"><?= htmlspecialchars($data['jenis_kelamin']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-star-and-crescent me-2 fs-6"></i> Agama</span>
                                                <div class="data-value"><?= htmlspecialchars($data['agama']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-ring me-2 fs-6"></i> Status Perkawinan</span>
                                                <div class="data-value"><?= htmlspecialchars($data['status_perkawinan']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-graduation-cap me-2 fs-6"></i> Pendidikan Terakhir</span>
                                                <div class="data-value"><?= htmlspecialchars($data['pendidikan']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-briefcase me-2 fs-6"></i> Pekerjaan</span>
                                                <div class="data-value"><?= htmlspecialchars($data['pekerjaan']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="setting-block mb-0">
                                    <h6 class="fw-bold text-secondary"><i class="fa-solid fa-location-dot me-2"></i>Alamat & Domisili</h6>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-house-chimney me-2 fs-6"></i> Alamat Lengkap</span>
                                                <div class="data-value" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($data['alamat'])) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-map-location-dot me-2 fs-6"></i> Dusun</span>
                                                <div class="data-value"><?= htmlspecialchars($data['rt']) ?: '-' ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-box">
                                                <span class="data-label"><i class="fa-solid fa-thumbtack me-2 fs-6"></i> Status Penduduk</span>
                                                <?php 
                                                    $badge_class = 'bg-secondary';
                                                    if($data['status_penduduk'] == 'Tetap') $badge_class = 'bg-success';
                                                    if($data['status_penduduk'] == 'Pendatang') $badge_class = 'bg-warning text-dark';
                                                    if($data['status_penduduk'] == 'Pindah') $badge_class = 'bg-info';
                                                    if($data['status_penduduk'] == 'Meninggal') $badge_class = 'bg-dark';
                                                ?>
                                                <div class="data-value mt-1"><span class="badge <?= $badge_class ?> px-3 py-2" style="font-size: 0.9rem;"><?= htmlspecialchars($data['status_penduduk']) ?></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
    <script>
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
            const sidebarToggle = document.getElementById('sidebarToggleTop');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function(e) { e.preventDefault(); sidebar.classList.toggle('show'); sidebarOverlay.classList.toggle('show'); });
                sidebarOverlay.addEventListener('click', function() { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); });

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