<?php
session_start();
require_once 'koneksi.php';

$gallery_dir = 'galeri_img/';

if (!is_dir($gallery_dir)) {
    mkdir($gallery_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Deteksi jika server menolak file secara diam-diam karena melebihi batas post_max_size XAMPP
    if (empty($_FILES) && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $_SESSION['alert'] = ['status' => 'error', 'message' => 'Upload Ditolak: Ukuran foto terlampau besar sehingga diblokir otomatis oleh sistem XAMPP.'];
        header("Location: galeri.php");
        exit;
    }

    if (isset($_FILES['foto_galeri'])) {
        $file = $_FILES['foto_galeri'];
        $judul = $_POST['judul_kegiatan'] ?? '';
        $tanggal = $_POST['tanggal_kegiatan'] ?? date('Y-m-d');

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed_ext)) {
            $new_filename = uniqid('galeri_') . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $gallery_dir . $new_filename)) {
                $stmt = $pdo->prepare("INSERT INTO galeri (file_foto, judul_kegiatan, tanggal_kegiatan) VALUES (:file, :judul, :tanggal)");
                $stmt->execute([':file' => $new_filename, ':judul' => $judul, ':tanggal' => $tanggal]);
                $_SESSION['alert'] = ['status' => 'success', 'message' => 'Foto berhasil ditambahkan ke galeri!'];
            } else {
                $_SESSION['alert'] = ['status' => 'error', 'message' => 'Gagal menyimpan foto ke direktori.'];
            }
        } else {
            $_SESSION['alert'] = ['status' => 'error', 'message' => 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WEBP.'];
        }
    } else {
        $err_msg = 'Terjadi kesalahan saat mengunggah gambar.';
        if ($file['error'] == UPLOAD_ERR_INI_SIZE || $file['error'] == UPLOAD_ERR_FORM_SIZE) {
            $err_msg = 'Gagal: Ukuran foto terlalu besar! Maksimal batas server (biasanya 2MB).';
        }
        $_SESSION['alert'] = ['status' => 'error', 'message' => $err_msg];
    }
    header("Location: galeri.php");
    exit;
    }
}

// Gabungkan semua foto yang di luar (tidak memiliki judul folder/kosong) ke dalam folder 'Profil Desa'
$pdo->exec("UPDATE galeri SET judul_kegiatan = 'Profil Desa' WHERE TRIM(judul_kegiatan) = '' OR judul_kegiatan IS NULL");

$filter_judul = $_GET['filter_judul'] ?? '';
$query_galeri = "SELECT * FROM galeri";
$params_galeri = [];
if (!empty($filter_judul)) {
    $query_galeri .= " WHERE judul_kegiatan = :judul";
    $params_galeri[':judul'] = $filter_judul;
}
$query_galeri .= " ORDER BY tanggal_kegiatan DESC, id_galeri DESC";

$stmt = $pdo->prepare($query_galeri);
$stmt->execute($params_galeri);
$images = $stmt->fetchAll();

$stmt_titles = $pdo->query("SELECT DISTINCT judul_kegiatan FROM galeri ORDER BY judul_kegiatan ASC");
$unique_titles = $stmt_titles->fetchAll(PDO::FETCH_COLUMN);

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
    <meta name="theme-color" content="#4e73df">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Galeri Desa - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
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
        .form-control {
            background: #ffffff !important;
            border: 1px solid #d1d3e2 !important;
            color: #6e707e !important;
            font-weight: 500;
            text-shadow: none;
            border-radius: 0.35rem;
            padding: 0.45rem 0.85rem !important;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: #bac8f3 !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
        }

        .gallery-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            border-radius: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .gallery-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .gallery-card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .gallery-card .card-footer {
            background-color: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
            padding: 0.75rem;
        }
        .gallery-card a {
            display: block;
            position: relative;
        }
        .gallery-card a::after {
            content: '🔍';
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
        
        .btn-primary { background-color: #4e73df !important; border-color: #4e73df !important; box-shadow: none !important; border-radius: 4px; font-weight: 600; transition: all 0.2s; }
        .btn-primary:hover { background-color: #2e59d9 !important; border-color: #2653d4 !important; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important; }
        .btn-outline-danger { color: #dc2626 !important; border-color: #dc2626 !important; text-shadow: none; font-weight: 600;}
        .btn-outline-danger:hover { background-color: #dc2626 !important; color: #ffffff !important; }
        
        .topbar { height: 4.375rem; background-color: #fff; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); margin-bottom: 0.5rem; }
        
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 0; left: -250px; width: 250px; height: 100vh; transition: all 0.3s ease-in-out; z-index: 1040 !important; }
            .sidebar.show { left: 0; box-shadow: 0.15rem 0 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
            .sidebar-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1030; opacity: 0; visibility: hidden; transition: all 0.3s ease-in-out; }
            .sidebar-overlay.show { opacity: 1; visibility: visible; }

            /* Menyesuaikan ukuran kotak galeri di layar HP agar lebih ringkas */
            .gallery-card img { 
                height: 180px !important; 
                width: 100% !important; 
                display: block !important; 
                object-fit: cover !important;
            }
            .gallery-card .card-footer { padding: 0.5rem !important; }
            .empty-state-container { padding: 2rem 1rem !important; }
            .empty-state-anim { width: 80px !important; height: 80px !important; margin-bottom: 0.5rem !important; }
            .empty-state-container h4 { font-size: 1.2rem; }
        }

        /* Animasi Collapse Folder Galeri */
        .folder-toggle[aria-expanded="true"] .fa-folder { display: none !important; }
        .folder-toggle[aria-expanded="false"] .fa-folder-open { display: none !important; }
        .folder-toggle[aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }
        .folder-toggle { transition: background-color 0.2s ease, padding-left 0.2s ease; padding: 0.5rem 0.5rem 0.5rem 0; border-radius: 0.5rem; }
        .folder-toggle:hover { background-color: #f1f5f9; padding-left: 0.5rem; }

        .footer-marquee-official {
            background: #fff;
            border-top: 1px solid #e3e6f0;
            color: #858796;
            font-size: 0.8rem;
            padding: 1rem 0;
        }
        
        /* Empty State Styles */
        .empty-state-container {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 4rem 2rem;
        }
        .empty-state-anim {
            width: 120px;
            height: 120px;
            stroke: #94a3b8;
            animation: floatAnim 3s ease-in-out infinite;
            margin-bottom: 1.5rem;
        }
        @keyframes floatAnim { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
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
                                <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-images me-2"></i>Kelola Galeri Desa</h5>
                            </div>
                            
                            <!-- Kotak Form dipisah dari scroll area agar foto bisa lewat membelakangi -->
                            <div class="bg-white px-3 px-md-4 py-3 border-bottom shadow-sm" style="z-index: 1020; position: relative;">
                                <h6 class="fw-bold text-secondary mb-2" style="font-size: 0.95rem; border: none; padding: 0;">
                                    <i class="fa-solid fa-camera me-1"></i> Tambah Foto Baru 
                                    <small class="text-muted fw-normal ms-1" style="font-size: 0.75rem;">(Otomatis dikompres)</small>
                                </h6>
                                <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <input type="file" id="inputFotoGaleri" name="foto_galeri" class="d-none" accept="image/jpeg, image/png, image/gif, image/webp" required>
                                        <label for="inputFotoGaleri" class="form-control form-control-sm d-flex align-items-center mb-0 shadow-sm" style="cursor: pointer; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; background: #f8fafc !important;">
                                            <i class="fa-solid fa-image text-secondary me-2"></i> <span id="namaFileFoto" class="text-muted">Pilih foto...</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="judul_kegiatan" list="folderKegiatan" class="form-control form-control-sm shadow-sm" placeholder="Pilih/Buat Folder Kegiatan..." required autocomplete="off" title="Pilih folder yang sudah ada atau ketik nama folder baru">
                                        <datalist id="folderKegiatan">
                                            <?php foreach ($unique_titles as $title): ?>
                                                <option value="<?= htmlspecialchars($title) ?>"></option>
                                            <?php endforeach; ?>
                                        </datalist>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" name="tanggal_kegiatan" class="form-control form-control-sm shadow-sm" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm">
                                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Unggah
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Filter Pencarian Galeri -->
                            <div class="px-3 px-md-4 py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                                <span class="small fw-bold text-muted"><i class="fa-solid fa-filter me-1"></i> Filter Folder Kegiatan</span>
                                <form method="GET" class="d-flex gap-2 m-0">
                                    <select name="filter_judul" class="form-select form-select-sm shadow-sm border-0" onchange="this.form.submit()" style="min-width: 200px;">
                                        <option value="">Semua Folder Kegiatan</option>
                                        <?php foreach ($unique_titles as $title): ?>
                                            <option value="<?= htmlspecialchars($title) ?>" <?= $filter_judul === $title ? 'selected' : '' ?>><?= htmlspecialchars($title) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>

                            <div class="card-body p-3 p-md-4" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                                <div class="row g-2 g-md-4">
                                    <?php if (empty($images)): ?>
                                        <div class="col-12">
                                            <div class="text-center empty-state-container text-muted mx-auto" style="max-width: 600px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="empty-state-anim">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                    <polyline points="21 15 16 10 5 21"></polyline>
                                                </svg>
                                                <h4 class="fw-bold" style="color: #0b214a;">Galeri Masih Kosong</h4>
                                                <p>Belum ada foto kegiatan atau infrastruktur yang ditambahkan.<br>Silakan unggah foto baru menggunakan form di atas.</p>
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
                                            <div class="col-12 mt-4 mb-2">
                                                <div class="d-flex align-items-center border-bottom pb-2">
                                                    <i class="fa-solid fa-folder-open text-warning fs-4 me-2"></i>
                                                    <h6 class="fw-bold text-dark mb-0 fs-5"><?= htmlspecialchars($judul) ?></h6>
                                                    <span class="badge bg-secondary ms-3"><?= count($album_images) ?> Foto</span>
                                                </div>
                                            </div>
                                            <?php foreach ($album_images as $row): ?>
                                                <div class="col-6 col-md-4 col-lg-3">
                                                    <div class="card gallery-card shadow-sm position-relative h-100 d-flex flex-column border">
                                                        <a href="<?= $gallery_dir . rawurlencode($row['file_foto']) ?>" data-fancybox="gallery-<?= md5($judul) ?>" data-caption="<?= htmlspecialchars($row['judul_kegiatan']) ?> (<?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?>)">
                                                            <img src="<?= $gallery_dir . rawurlencode($row['file_foto']) ?>?t=<?= file_exists($gallery_dir . $row['file_foto']) ? filemtime($gallery_dir . $row['file_foto']) : time() ?>" class="card-img-top" style="height: 140px; width: 100%; object-fit: cover; display: block;" alt="Foto Kegiatan" onerror="this.src='https://via.placeholder.com/300?text=Gambar+Rusak'">
                                                    </a>
                                                        <div class="card-body p-2 text-center flex-grow-1 d-flex flex-column justify-content-center bg-light">
                                                        <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> <?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></small>
                                                    </div>
                                                        <div class="card-footer text-center mt-auto p-2 bg-white border-top-0">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus w-100 fw-bold" data-id="<?= $row['id_galeri'] ?>" data-file="<?= htmlspecialchars($row['file_foto']) ?>"><i class="fa-solid fa-trash-can me-1"></i> Hapus</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
    
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
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

        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($alert): ?> Swal.fire({ icon: '<?= $alert['status'] ?>', title: '<?= ($alert['status'] == "success") ? "Berhasil!" : "Gagal!" ?>', text: '<?= addslashes($alert['message']) ?>', showConfirmButton: false, timer: 2500 }); <?php endif; ?>
            document.querySelectorAll('.btn-hapus').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const file = this.dataset.file;
                    Swal.fire({ title: 'Anda Yakin?', text: `Foto ini akan dihapus permanen dari galeri.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' }).then(result => { if (result.isConfirmed) { window.location.href = `hapus_foto_galeri.php?id=${id}&file=${file}`; } });
                });
            });

            Fancybox.bind("[data-fancybox]", {});

            // Toggle Sidebar on Mobile
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
                
                // Mengubah teks saat foto dipilih dari galeri
                const inputFoto = document.getElementById('inputFotoGaleri');
                const namaFileFoto = document.getElementById('namaFileFoto');
                if (inputFoto && namaFileFoto) {
                    inputFoto.addEventListener('change', function() {
                        namaFileFoto.textContent = this.files.length > 0 ? this.files[0].name : 'Pilih foto dari galeri...';
                        namaFileFoto.className = this.files.length > 0 ? 'text-dark fw-bold' : 'text-muted';
                    });
                }

                // Fitur Auto-Kompresi Foto di sisi Klien (Browser)
                document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (!file) return;
                        
                        // Kompres otomatis jika file di atas 1MB
                        if (file.size > 1024 * 1024) {
                            Swal.fire({title: 'Mengompresi Foto...', text: 'Sedang mengecilkan ukuran gambar...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                            
                            const reader = new FileReader();
                            reader.readAsDataURL(file);
                            reader.onload = function(event) {
                                const img = new Image();
                                img.src = event.target.result;
                                img.onload = function() {
                                    const canvas = document.createElement('canvas');
                                    const MAX_WIDTH = 1200; const MAX_HEIGHT = 1200;
                                    let width = img.width; let height = img.height;

                                    if (width > height) { if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; } } 
                                    else { if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; } }
                                    
                                    canvas.width = width; canvas.height = height;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(img, 0, 0, width, height);
                                    
                                    let mimeType = 'image/jpeg'; let ext = '.jpg';
                                    if(file.type === 'image/png') { mimeType = 'image/png'; ext = '.png'; }
                                    
                                    canvas.toBlob(function(blob) {
                                        const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ext, { type: mimeType, lastModified: Date.now() });
                                        const dataTransfer = new DataTransfer(); dataTransfer.items.add(newFile);
                                        input.files = dataTransfer.files;
                                        Swal.close();
                                        const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                                        Toast.fire({icon: 'success', title: 'Ukuran foto berhasil dikecilkan otomatis!'});
                                    }, mimeType, 0.8);
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