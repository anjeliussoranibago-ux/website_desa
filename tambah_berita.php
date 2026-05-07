<?php
require_once 'auth_check.php';
require_once 'koneksi.php';
$pesan = ''; $status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $isi_berita = trim($_POST['isi_berita']);
    $status_berita = $_POST['status'];
    $tanggal_publikasi = date('Y-m-d H:i:s');
    
    // Sederhana slug generator untuk URL yang SEO friendly
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    
    try {
        $gambar_cover = '';
        if (isset($_FILES['gambar_cover']) && $_FILES['gambar_cover']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['gambar_cover']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed_ext)) {
                $cover_dir = 'berita_img/';
                if (!is_dir($cover_dir)) mkdir($cover_dir, 0777, true);
                $gambar_cover = uniqid('cover_') . '.' . $ext;
                move_uploaded_file($_FILES['gambar_cover']['tmp_name'], $cover_dir . $gambar_cover);
            }
        }

        $sql = "INSERT INTO berita_informasi (judul, slug, isi_berita, gambar_cover, tanggal_publikasi, status) 
                VALUES (:judul, :slug, :isi_berita, :gambar_cover, :tanggal_publikasi, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':judul' => $judul,
            ':slug' => $slug,
            ':isi_berita' => $isi_berita,
            ':gambar_cover' => $gambar_cover,
            ':tanggal_publikasi' => $tanggal_publikasi,
            ':status' => $status_berita
        ]);

        $_SESSION['alert'] = [
            'status' => 'success',
            'message' => 'Berita berhasil dipublikasikan!'
        ];
        header("Location: berita.php");
        exit;
    } catch (PDOException $e) {
        $status = 'error';
        $pesan = "Terjadi kesalahan sistem saat menyimpan berita. Silakan coba lagi nanti.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tulis Berita Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        body { background-color: #f8f9fc !important; color: #5a5c69; font-family: 'Nunito', sans-serif; }
        .sidebar { background: #4e73df !important; background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%) !important; background-size: cover; z-index: 100; min-height: 100vh; }
        .sidebar .nav-item { margin-bottom: 0.3rem; }
        .sidebar .nav-link { color: rgba(255,255,255,.8) !important; padding: 0.85rem 1.15rem; margin: 0 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; letter-spacing: 0.3px; transition: all 0.3s ease; display: flex; align-items: center; }
        .sidebar .nav-link:hover { color: #ffffff !important; background: rgba(255,255,255,0.15); transform: translateX(5px); }
        .sidebar .nav-link.active { color: #4e73df !important; background: #ffffff !important; font-weight: 800; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .sidebar .nav-link i.me-2 { font-size: 1.2rem; width: 30px; text-align: center; transition: transform 0.3s ease; }
        .sidebar .nav-link:hover i.me-2 { transform: scale(1.2) rotate(5deg); }
        .topbar { height: 4.375rem; background-color: #fff; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); margin-bottom: 0.5rem; }
        .glass-card { background: #ffffff !important; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important; border: 1px solid #e3e6f0; border-radius: 0.35rem !important; }
        .text-secondary { color: #4e73df !important; font-weight: bold; border-bottom: 2px solid #4e73df; padding-bottom: 5px; display: inline-block; margin-bottom: 15px; }
        .setting-block { background: #f8fafc; border: 1px solid #e2e8f0; padding: 1.25rem; border-radius: 6px; margin-bottom: 1.5rem; }
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 0; left: -250px; width: 250px; height: 100vh; transition: all 0.3s ease-in-out; z-index: 1040 !important; }
            .sidebar.show { left: 0; box-shadow: 0.15rem 0 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
            .sidebar-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1030; opacity: 0; visibility: hidden; transition: all 0.3s ease-in-out; }
            .sidebar-overlay.show { opacity: 1; visibility: visible; }
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
                                <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-pen-nib me-2"></i>Tulis Berita Baru</h5>
                            </div>
                            <div class="card-body p-4" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                                <?php if ($pesan && $status == 'error'): ?>
                                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                        <strong><i class="fa-solid fa-triangle-exclamation me-2"></i>Peringatan:</strong> <?= htmlspecialchars($pesan) ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="setting-block">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-muted mb-1">Judul Berita / Pengumuman *</label>
                                                <input type="text" name="judul" class="form-control form-control-lg fs-6" placeholder="Masukkan judul pengumuman..." required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-muted mb-1">Status *</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="Published">Publikasikan (Langsung Tampil)</option>
                                                    <option value="Draft">Simpan Sebagai Draft</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label small fw-bold text-muted mb-1">Foto Sampul (Cover) Berita (Opsional)</label>
                                                <input type="file" name="gambar_cover" class="form-control" accept="image/jpeg, image/png, image/webp" onchange="previewImage(this, 'previewCover')">
                                            </div>
                                            <div class="col-12 mt-2 d-flex justify-content-center">
                                                <img id="previewCover" src="" class="rounded shadow-sm" style="max-height: 250px; display: none; object-fit: cover; width: 100%;">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-muted mb-1">Isi Berita *</label>
                                                <textarea name="isi_berita" class="form-control" rows="10" placeholder="Tulis rincian lengkap dari berita atau pengumuman di sini..." required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="berita.php" class="btn btn-light border px-4">Batal</a>
                                        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-paper-plane me-1"></i> Simpan Berita</button>
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
    <script>
        function previewImage(input, imgId) { const preview = document.getElementById(imgId); if (input.files && input.files[0]) { const reader = new FileReader(); reader.onload = function(e) { preview.src = e.target.result; preview.style.display = 'block'; }; reader.readAsDataURL(input.files[0]); } else { preview.src = ''; preview.style.display = 'none'; } }
        function updateClock() { const now = new Date(); document.getElementById('clock-container').innerHTML = now.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':'); } setInterval(updateClock, 1000); updateClock();
        document.addEventListener('DOMContentLoaded', function() { const sidebar = document.querySelector('.sidebar'), sidebarToggle = document.getElementById('sidebarToggleTop'), sidebarOverlay = document.getElementById('sidebarOverlay'); if (sidebarToggle && sidebar && sidebarOverlay) { sidebarToggle.addEventListener('click', e => { e.preventDefault(); sidebar.classList.toggle('show'); sidebarOverlay.classList.toggle('show'); }); sidebarOverlay.addEventListener('click', () => { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); }); } });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>