<?php
require_once 'auth_check.php';
require_once 'koneksi.php';

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);

// Proses Tambah Aparatur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $jabatan = trim($_POST['jabatan']);
    $urutan = (int) $_POST['urutan'];
    $foto = '';

    if (isset($_FILES['foto_aparatur']) && $_FILES['foto_aparatur']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto_aparatur']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $dir = 'aparatur_img/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $foto = uniqid('aparatur_') . '.' . $ext;
            move_uploaded_file($_FILES['foto_aparatur']['tmp_name'], $dir . $foto);
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO aparatur_desa (nama, jabatan, foto, urutan) VALUES (:nama, :jabatan, :foto, :urutan)");
        $stmt->execute([':nama' => $nama, ':jabatan' => $jabatan, ':foto' => $foto, ':urutan' => $urutan]);
        $_SESSION['alert'] = ['status' => 'success', 'message' => 'Aparatur berhasil ditambahkan!'];
    } catch (PDOException $e) {
        $_SESSION['alert'] = ['status' => 'error', 'message' => 'Gagal menambahkan aparatur.'];
    }
    header("Location: aparatur.php");
    exit;
}

// Ambil data aparatur
$stmt = $pdo->query("SELECT * FROM aparatur_desa ORDER BY urutan ASC, id_aparatur DESC");
$data_aparatur = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kelola Aparatur Desa - Desa Hilifalago</title>
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
        .text-secondary { color: #4e73df !important; font-weight: bold; }
        @media (max-width: 767.98px) {; top: 0; left: -250px; width: 250px; height: 100vh; transition: all 0.3s ease-in-out; z-index: 1040 !important; }
            .sidebar.show { left: 0; box-shadow: 0.15rem 0 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
            .sidebar-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1030; opacity: 0; visibility: hidden; transition: all 0.3s ease-in-out; }
            .sidebar-overlay.show { opacity: 1; visibility: visible; }
            .table-responsive { white-space: nowrap; }
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
                            <div class="card-header bg-transparent border-bottom pt-3 px-4 pb-3 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-user-tie me-2"></i>Aparatur Desa</h5>
                            </div>
                            
                            <!-- Form Tambah Aparatur -->
                            <div class="bg-white px-4 py-3 border-bottom shadow-sm" style="z-index: 1020;">
                                <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Nama Lengkap</label>
                                        <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Budi Santoso" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Jabatan</label>
                                        <input type="text" name="jabatan" class="form-control form-control-sm" placeholder="Contoh: Sekretaris Desa" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted mb-1">Urutan Tampil</label>
                                        <input type="number" name="urutan" class="form-control form-control-sm" placeholder="1, 2, 3..." required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Foto (Opsional, 3x4)</label>
                                        <input type="file" name="foto_aparatur" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tabel Aparatur -->
                            <div class="card-body p-3" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                                <div class="table-responsive m-0">
                                    <table class="table table-hover table-bordered align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th style="width: 60px;">Urutan</th>
                                                <th style="width: 80px;">Foto</th>
                                                <th>Nama Lengkap</th>
                                                <th>Jabatan</th>
                                                <th style="width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($data_aparatur) > 0): ?>
                                                <?php foreach ($data_aparatur as $row): ?>
                                                <tr>
                                                    <td class="text-center fw-bold"><?= $row['urutan'] ?></td>
                                                    <td class="text-center">
                                                        <?php if (!empty($row['foto']) && file_exists('aparatur_img/' . $row['foto'])): ?>
                                                            <img src="aparatur_img/<?= htmlspecialchars($row['foto']) ?>?t=<?= time() ?>" class="rounded shadow-sm" style="width: 40px; height: 50px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="rounded shadow-sm bg-light text-muted d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 50px; font-size: 0.6rem;">No Foto</div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="fw-bold text-primary"><?= htmlspecialchars($row['nama']) ?></td>
                                                    <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                                    <td class="text-center">
                                                        <button onclick="konfirmasiHapus(<?= $row['id_aparatur'] ?>)" class="btn btn-sm btn-danger border-0" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data aparatur desa.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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

    <?php if ($alert): ?>
    <script>Swal.fire({icon: '<?= $alert['status'] ?>', title: '<?= ($alert['status'] == "success") ? "Berhasil" : "Terjadi Kesalahan" ?>', text: '<?= addslashes($alert['message']) ?>', showConfirmButton: false, timer: 3000});</script>
    <?php endif; ?>

    <script>
        function konfirmasiHapus(id) { Swal.fire({ title: 'Hapus Aparatur?', text: "Data aparatur ini akan dihapus secara permanen!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' }).then((result) => { if (result.isConfirmed) { window.location.href = 'hapus_aparatur.php?id=' + id; } }) }
        function updateClock() { const now = new Date(); document.getElementById('clock-container').innerHTML = now.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':'); } setInterval(updateClock, 1000); updateClock();
        document.addEventListener('DOMContentLoaded', function() { const sidebar = document.querySelector('.sidebar'), sidebarToggle = document.getElementById('sidebarToggleTop'), sidebarOverlay = document.getElementById('sidebarOverlay'); if (sidebarToggle && sidebar && sidebarOverlay) { sidebarToggle.addEventListener('click', e => { e.preventDefault(); sidebar.classList.toggle('show'); sidebarOverlay.classList.toggle('show'); }); sidebarOverlay.addEventListener('click', () => { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); }); } });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>