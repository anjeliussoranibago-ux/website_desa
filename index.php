<?php
session_start();
require_once 'koneksi.php';

// Mengambil data statistik (Hanya angka, privasi terjaga)
$total_penduduk = $pdo->query("SELECT COUNT(*) FROM penduduk WHERE status_penduduk != 'Meninggal' AND status_penduduk != 'Pindah'")->fetchColumn();
$total_l = $pdo->query("SELECT COUNT(*) FROM penduduk WHERE jenis_kelamin = 'Laki-laki' AND status_penduduk != 'Meninggal' AND status_penduduk != 'Pindah'")->fetchColumn();
$total_p = $pdo->query("SELECT COUNT(*) FROM penduduk WHERE jenis_kelamin = 'Perempuan' AND status_penduduk != 'Meninggal' AND status_penduduk != 'Pindah'")->fetchColumn();

$profil_file = 'profil.json';
$profil_data = [
    'nama_kades' => 'ANJELIUS SORANI BAGO S.KOM',
    'visi' => 'Mewujudkan Desa Hilifalago yang mandiri, sejahtera, dan berbudaya.',
    'misi' => "Meningkatkan kualitas pelayanan administrasi kepada masyarakat."
];
if (file_exists($profil_file)) {
    $profil_data = json_decode(file_get_contents($profil_file), true);
}

// Mengambil data Aparatur Desa
$aparatur_data = $pdo->query("SELECT * FROM aparatur_desa ORDER BY urutan ASC")->fetchAll();

$alert = null;
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#ffffff">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Portal Resmi - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        .hero-section {
            background: linear-gradient(rgba(11, 33, 74, 0.88), rgba(11, 33, 74, 0.95)), url('omohada.jpg') center/cover no-repeat fixed;
            color: white;
            padding: 4rem 1rem 5.5rem;
            text-align: center;
            width: 100%;
        }
        .section-title {
            position: relative;
            display: inline-block;
            padding-bottom: 12px;
            margin-bottom: 30px;
            font-weight: 800;
            color: #0b214a;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #4e73df;
            border-radius: 2px;
        }
        .visi-card, .misi-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
            background: #ffffff;
            transition: transform 0.3s ease;
        }
        .visi-card:hover, .misi-card:hover { transform: translateY(-5px); }
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .navbar-public { background: #ffffff; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .footer-link a { transition: color 0.2s, transform 0.2s; display: inline-block; }
        .footer-link a:hover { color: #ffffff !important; transform: translateX(5px); }
        
        @media (max-width: 767.98px) {
            .hero-section { 
                padding: 3.5rem 1rem 5.5rem; 
                background-attachment: scroll !important; 
                background-position: center top !important;
            }
            .hero-section h1 { font-size: 1.1rem !important; letter-spacing: 2px !important; margin-bottom: 0.5rem !important; }
            .hero-section h2 { font-size: 2.75rem !important; }
            .hero-section p { font-size: 1rem !important; line-height: 1.5; }
            .stat-card { padding: 1.25rem 0.5rem; }
            .stat-card .fs-1 { font-size: 1.75rem !important; margin-bottom: 0.25rem !important; }
            .stat-card h5 { font-size: 0.7rem !important; letter-spacing: 0.3px; }
            .stat-card h2 { font-size: 1.1rem !important; }
            .section-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #f4f7f9; padding-top: 76px;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-public fixed-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php" style="color: #0b214a; font-size: 1.3rem;">
                <img src="logo.png?t=<?= time() ?>" alt="Logo" width="55" height="55" onerror="this.style.display='none'">
                Desa Hilifalago
            </a>
            <!-- 6. Navigasi Hamburger (Tombol Garis Tiga) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link active fw-bold" href="index.php"><i class="fa-solid fa-house me-1"></i> Beranda</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-dark" href="galeri_publik.php"><i class="fa-solid fa-images me-1"></i> Galeri Desa</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-dark" href="upload_dokumen.php"><i class="fa-solid fa-file-arrow-up me-1"></i> Layanan Dokumen</a></li>
                    <li class="nav-item"><a class="btn btn-outline-secondary px-4 fw-bold" href="login.php"><i class="fa-solid fa-lock me-1"></i> Login Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

<!-- Wrapper Utama untuk mengunci Footer selalu di bawah -->
<main class="flex-grow-1">

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container animate-fade-up">
            <h1 class="h4 fw-bold mb-2 text-light opacity-75 text-uppercase" style="letter-spacing: 3px;">Portal Resmi Pemerintahan</h1>
            <h2 class="display-4 fw-bold mb-3 text-warning" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4);">Desa Hilifalago</h2>
            <p class="mb-0 text-light opacity-75 mx-auto hero-address" style="font-size: 1.1rem; max-width: 700px;">Kecamatan Onolalu, Kabupaten Nias Selatan &bull; Provinsi Sumatera Utara</p>
        </div>
    </div>

    <!-- Profil Kades & Visi Misi Section -->
    <div class="container position-relative" style="margin-top: -35px; z-index: 10; margin-bottom: 1.5rem;">
        <div class="bg-white p-4 p-md-5 rounded-4 shadow-lg animate-fade-up delay-1 border-top border-primary border-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-4 text-center">
                    <div class="position-relative d-inline-block">
                        <img src="foto_kades.jpg?t=<?= time() ?>" alt="Kepala Desa" class="img-fluid rounded-circle" style="width: 140px; height: 140px; object-fit: cover; box-shadow: 0 8px 25px rgba(0,0,0,0.06); border: 4px solid #ffffff; outline: 2px solid #e2e8f0; outline-offset: 3px;">
                    </div>
                </div>
                <div class="col-lg-8">
                    <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-landmark me-2"></i> Profil Pimpinan</h6>
                    <h3 class="fw-bold text-dark mb-3"><?= htmlspecialchars($profil_data['nama_kades'] ?? 'ANJELIUS SORANI BAGO S.KOM') ?></h3>
                    <p class="text-muted" style="line-height: 1.7; font-size: 1rem; text-align: justify;">
                        "<b>Ya'ahowu!</b> Selamat datang di Portal Resmi Pemerintah Desa Hilifalago. Website ini dibangun sebagai wujud komitmen kami dalam memberikan pelayanan publik yang transparan, cepat, dan mudah diakses oleh seluruh lapisan masyarakat. Melalui portal ini, warga dapat melihat informasi, kegiatan, serta memanfaatkan layanan administrasi secara mandiri."
                    </p>
                    <a href="galeri_publik.php" class="btn btn-outline-primary px-4 rounded-pill fw-bold mt-2"><i class="fa-solid fa-images me-2"></i> Lihat Kegiatan Desa</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Visi Misi Section -->
    <div class="py-4" style="background-color: #f8fafc;">
        <div class="container animate-fade-up delay-2">
            <div class="text-center">
                <h3 class="section-title">Visi & Misi Desa</h3>
            </div>
            <div class="row g-4 mt-2">
                <div class="col-md-5">
                    <div class="card visi-card border-top border-primary border-5 p-4 p-md-4 text-center">
                        <div class="fs-2 text-primary mb-3"><i class="fa-solid fa-eye"></i></div>
                        <h4 class="fw-bold text-dark mb-3">Visi</h4>
                        <p class="text-muted mb-0" style="line-height: 1.6; font-size: 1rem;">"<?= nl2br(htmlspecialchars($profil_data['visi'])) ?>"</p>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card misi-card border-top border-success border-5 p-4 p-md-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="fs-3 text-success me-3"><i class="fa-solid fa-bullseye"></i></div>
                            <h4 class="fw-bold text-dark mb-0">Misi</h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php foreach (explode("\n", trim($profil_data['misi'])) as $misi): ?>
                                <?php if (trim($misi) !== ''): ?>
                                    <li class="list-group-item px-0 py-3 border-bottom border-light text-muted d-flex align-items-start" style="background: transparent;">
                                        <i class="fa-solid fa-circle-check text-success mt-1 me-2 fs-6"></i> 
                                        <span style="line-height: 1.5; font-size: 0.95rem;"><?= htmlspecialchars(trim($misi)) ?></span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aparatur Desa Section -->
    <div class="container py-4">
        <div class="text-center animate-fade-up delay-2">
            <h3 class="section-title">Aparatur Desa</h3>
            <p class="text-muted">Struktur Organisasi Pemerintahan Desa Hilifalago</p>
        </div>
        <div class="row justify-content-center g-4 mt-2 animate-fade-up delay-3">
            <?php if (count($aparatur_data) > 0): ?>
                <?php foreach ($aparatur_data as $person): ?>
                    <div class="col-6 col-md-4 col-lg-3 text-center">
                        <div class="card border-0 shadow-sm h-100 py-3 px-2 hover-scale" style="border-radius: 12px;">
                            <div class="mx-auto mb-2" style="width: 90px; height: 90px; border-radius: 50%; padding: 3px; border: 2px solid #4e73df; background: #f8fafc;">
                                <?php if (!empty($person['foto']) && file_exists('aparatur_img/' . $person['foto'])): ?>
                                    <img src="aparatur_img/<?= htmlspecialchars($person['foto']) ?>?t=<?= time() ?>" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/110?text=Foto" class="rounded-circle w-100 h-100" style="object-fit: cover; opacity: 0.5;">
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($person['nama']) ?></h6>
                            <p class="text-muted mb-0 fw-bold" style="font-size: 0.75rem; color: #4e73df !important;"><?= htmlspecialchars($person['jabatan']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted"><p>Struktur aparatur desa sedang dalam pembaruan data.</p></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistik Penduduk -->
    <div class="container py-4 mb-3">
        <div class="text-center animate-fade-up delay-3">
            <h3 class="section-title">Data Demografi Warga</h3>
            <p class="text-muted">Data demografi warga Desa Hilifalago</p>
        </div>
        <div class="row justify-content-center g-3 g-md-4 animate-fade-up delay-3 mt-1">
            <div class="col-6 col-md-4">
                <div class="stat-card border-bottom border-primary border-4">
                    <div class="fs-2 mb-2 text-primary"><i class="fa-solid fa-users"></i></div>
                    <h5 class="text-muted fw-bold text-uppercase mb-1" style="font-size: 0.85rem;">Total Warga</h5>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($total_penduduk) ?> Jiwa</h3>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="stat-card border-bottom border-success border-4">
                    <div class="fs-2 mb-2 text-success"><i class="fa-solid fa-person"></i></div>
                    <h5 class="text-muted fw-bold text-uppercase mb-1" style="font-size: 0.85rem;">Laki-Laki</h5>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($total_l) ?> Jiwa</h3>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="stat-card border-bottom border-danger border-4">
                    <div class="fs-2 mb-2 text-danger"><i class="fa-solid fa-person-dress"></i></div>
                    <h5 class="text-muted fw-bold text-uppercase mb-1" style="font-size: 0.85rem;">Perempuan</h5>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($total_p) ?> Jiwa</h3>
                </div>
            </div>
        </div>
    </div>

</main>

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
                        <li><a href="index.php" class="text-white-50 text-decoration-none">Beranda</a></li>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($alert): ?>
            Swal.fire({
                icon: '<?= $alert['status'] ?>',
                title: '<?= ($alert['status'] == "success") ? "Berhasil!" : "Gagal!" ?>',
                text: '<?= addslashes($alert['message']) ?>',
                confirmButtonColor: '#4e73df'
            });
            <?php endif; ?>

            // Menampilkan Pop-up Selamat Datang untuk pengunjung baru
            if (!localStorage.getItem('welcome_popup_shown')) {
                setTimeout(() => {
                    Swal.fire({
                        title: 'Selamat Datang!',
                        html: '<h4 class="fw-bold mb-3 text-primary" style="letter-spacing: 2px;">YA\'AHOWU!</h4>Di Portal Resmi <b>Pemerintah Desa Hilifalago</b>.<br><span style="font-size: 0.9rem;" class="text-muted mt-2 d-block">Jelajahi informasi pelayanan publik dan kegiatan desa kami.</span>',
                        imageUrl: 'logo.png?t=<?= time() ?>',
                        imageWidth: 110,
                        imageAlt: 'Logo Desa',
                        confirmButtonText: 'Mulai Menjelajah <i class="fa-solid fa-arrow-right ms-1"></i>',
                        confirmButtonColor: '#4e73df',
                        backdrop: `rgba(11, 33, 74, 0.85)`
                    }).then(() => {
                        localStorage.setItem('welcome_popup_shown', 'true');
                    });
                }, 800); // Muncul setelah 0.8 detik agar animasi halaman selesai dimuat
            }
        });
    </script>
</body>
</html>

