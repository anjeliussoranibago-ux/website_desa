<?php
session_start();
require_once 'koneksi.php';

// Direktori tempat foto galeri disimpan
$gallery_dir = 'galeri_img/';

$stmt = $pdo->query("SELECT * FROM galeri ORDER BY tanggal_kegiatan DESC, id_galeri DESC");
$images = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Publik - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        body { background-color: #f4f7f9; }
        .navbar-public { background: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        .page-header {
            background: linear-gradient(rgba(11, 33, 74, 0.85), rgba(11, 33, 74, 0.85)), url('omohada.jpg') center/cover no-repeat;
            color: white;
            padding: 60px 0 40px;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .gallery-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .gallery-card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.5s ease;
        }
        .gallery-card:hover img {
            transform: scale(1.05);
        }
        .gallery-card a {
            display: block;
            position: relative;
            overflow: hidden;
        }
        .gallery-card a::after {
            content: '\f002';
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            color: white;
            background-color: rgba(0,0,0,0.4);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .gallery-card a:hover::after {
            opacity: 1;
        }
        
        .empty-state-container {
            background-color: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 4rem 2rem;
        }

        @media (max-width: 767.98px) {
            .gallery-card img { 
                height: 180px !important; 
                width: 100% !important; 
                display: block !important; 
                object-fit: cover !important;
            }
            .page-header { padding: 40px 0 20px !important; margin-bottom: 20px !important; }
            .empty-state-container { padding: 2.5rem 1rem !important; }
        }
        .footer-link a { transition: color 0.2s, transform 0.2s; display: inline-block; }
        .footer-link a:hover { color: #ffffff !important; transform: translateX(5px); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100" style="padding-top: 76px;">

    <!-- Navbar Publik -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-public fixed-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="portal.php" style="color: #0b214a;">
                <img src="logo.png?t=<?= time() ?>" alt="Logo" width="40" height="40" onerror="this.style.display='none'">
                Desa Hilifalago
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link fw-bold text-dark" href="portal.php"><i class="fa-solid fa-house me-1"></i> Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold" href="galeri_publik.php"><i class="fa-solid fa-images me-1"></i> Galeri Desa</a></li>
                    <li class="nav-item"><a class="btn btn-outline-secondary px-4 fw-bold" href="login.php"><i class="fa-solid fa-lock me-1"></i> Login Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Halaman -->
    <div class="page-header animate-fade-up">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Galeri Kegiatan Desa</h1>
            <p class="lead mb-0 opacity-75">Dokumentasi kegiatan, pembangunan, dan momen penting Desa Hilifalago</p>
        </div>
    </div>

    <!-- Konten Galeri -->
    <div class="container py-4 mb-5">
        <div class="row g-2 g-md-4 animate-fade-up delay-1">
            <?php if (empty($images)): ?>
                <div class="col-12">
                    <div class="text-center empty-state-container text-muted mx-auto" style="max-width: 600px;">
                        <h4 class="fw-bold" style="color: #0b214a;">Galeri Masih Kosong</h4>
                        <p>Belum ada foto kegiatan yang dipublikasikan oleh perangkat desa saat ini.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php 
                $albums = [];
                foreach ($images as $img) {
                    $judul = $img['judul_kegiatan'];
                    if (!isset($albums[$judul])) $albums[$judul] = [];
                    $albums[$judul][] = $img;
                }
                ?>
                <?php foreach ($albums as $judul => $album_images): ?>
                    <?php $cover = $album_images[0]; ?>
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="gallery-card h-100 d-flex flex-column">
                            <a href="<?= $gallery_dir . rawurlencode($cover['file_foto']) ?>" data-fancybox="gallery-<?= md5($judul) ?>" data-caption="<?= htmlspecialchars($cover['judul_kegiatan']) ?> (<?= date('d M Y', strtotime($cover['tanggal_kegiatan'])) ?>)">
                                <img src="<?= $gallery_dir . rawurlencode($cover['file_foto']) ?>?t=<?= file_exists($gallery_dir . $cover['file_foto']) ? filemtime($gallery_dir . $cover['file_foto']) : time() ?>" alt="Foto Kegiatan" onerror="this.src='https://via.placeholder.com/300?text=Gambar+Rusak'">
                            </a>
                            
                            <?php for ($i = 1; $i < count($album_images); $i++): ?>
                                <a href="<?= $gallery_dir . rawurlencode($album_images[$i]['file_foto']) ?>" data-fancybox="gallery-<?= md5($judul) ?>" data-caption="<?= htmlspecialchars($album_images[$i]['judul_kegiatan']) ?> (<?= date('d M Y', strtotime($album_images[$i]['tanggal_kegiatan'])) ?>)" style="display: none;"></a>
                            <?php endfor; ?>

                            <div class="p-3 text-center flex-grow-1 d-flex flex-column justify-content-center">
                                <h6 class="fw-bold mb-1" style="color: #0b214a; font-size: 0.95rem;">
                                    <i class="fa-solid fa-folder-open text-warning me-1"></i> <?= htmlspecialchars($judul) ?>
                                </h6>
                                <small class="text-muted"><i class="fa-regular fa-images me-1"></i> <?= count($album_images) ?> Foto &bull; <i class="fa-regular fa-calendar ms-1 me-1"></i> <?= date('d M Y', strtotime($cover['tanggal_kegiatan'])) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Fancybox.bind("[data-fancybox]", {
                // Konfigurasi efek transisi pop-up gambar
                hideScrollbar: false,
            });
        });
    </script>
</body>
</html>