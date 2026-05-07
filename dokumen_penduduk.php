<?php
session_start();
require_once 'auth_check.php';
require_once 'koneksi.php';

$dokumen_dir = 'dokumen_img/';
if (!is_dir($dokumen_dir)) {
    mkdir($dokumen_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file_dokumen'])) {
        $file = $_FILES['file_dokumen'];
        $nik = trim($_POST['nik']);
        $nama = trim($_POST['nama_pemilik']);
        $jenis = $_POST['jenis_dokumen'];
        $keterangan = $_POST['keterangan'] ?? '';

        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
            
            if (in_array($ext, $allowed_ext)) {
                $new_filename = uniqid('dok_') . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $dokumen_dir . $new_filename)) {
                    $stmt = $pdo->prepare("INSERT INTO dokumen_penduduk (nik, nama_pemilik, jenis_dokumen, file_dokumen, keterangan) VALUES (:nik, :nama, :jenis, :file, :keterangan)");
                    $stmt->execute([':nik' => $nik, ':nama' => $nama, ':jenis' => $jenis, ':file' => $new_filename, ':keterangan' => $keterangan]);
                    $_SESSION['alert'] = ['status' => 'success', 'message' => 'Dokumen berhasil diunggah!'];
                } else {
                    $_SESSION['alert'] = ['status' => 'error', 'message' => 'Gagal menyimpan dokumen ke server.'];
                }
            } else {
                $_SESSION['alert'] = ['status' => 'error', 'message' => 'Format file tidak valid. Gunakan PDF, JPG, atau PNG.'];
            }
        } else {
            $_SESSION['alert'] = ['status' => 'error', 'message' => 'Terjadi kesalahan saat mengunggah gambar. Pastikan ukuran di bawah 2MB.'];
        }
        header("Location: dokumen_penduduk.php");
        exit;
    }
}

$filter_jenis = $_GET['filter_jenis'] ?? '';
$query_dokumen = "SELECT * FROM dokumen_penduduk";
$params_dokumen = [];
if (!empty($filter_jenis)) {
    $query_dokumen .= " WHERE jenis_dokumen = :jenis";
    $params_dokumen[':jenis'] = $filter_jenis;
}
$query_dokumen .= " ORDER BY tanggal_upload DESC";
$stmt = $pdo->prepare($query_dokumen);
$stmt->execute($params_dokumen);
$dokumen = $stmt->fetchAll();

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dokumen Penduduk - Desa Hilifalago</title>
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
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 0; left: -250px; width: 250px; height: 100vh; transition: all 0.3s ease-in-out; z-index: 1040 !important; }
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
                            <div class="card-header bg-transparent border-bottom pt-3 px-4 pb-2">
                                <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-folder-open me-2"></i>Kelola Dokumen Penduduk</h5>
                            </div>
                            
                            <div class="bg-white px-3 px-md-4 py-3 border-bottom shadow-sm" style="z-index: 1020; position: relative;">
                                <h6 class="fw-bold text-secondary mb-3" style="font-size: 0.95rem;"><i class="fa-solid fa-file-arrow-up me-1"></i> Tambah Dokumen Baru</h6>
                                <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted mb-1">NIK Warga *</label>
                                        <input type="text" name="nik" class="form-control form-control-sm shadow-sm" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Nama Pemilik *</label>
                                        <input type="text" name="nama_pemilik" class="form-control form-control-sm shadow-sm" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted mb-1">Jenis Dokumen *</label>
                                        <select name="jenis_dokumen" class="form-select form-select-sm shadow-sm" required>
                                            <option value="" hidden>Pilih...</option>
                                            <option value="Kartu Keluarga">Kartu Keluarga (KK)</option>
                                            <option value="KTP">KTP</option>
                                            <option value="Akta Kelahiran">Akta Kelahiran</option>
                                            <option value="Akta Nikah">Akta Nikah</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">File Dokumen (PDF/JPG/PNG) *</label>
                                        <input type="file" name="file_dokumen" class="form-control form-control-sm shadow-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-10 mt-2">
                                        <input type="text" name="keterangan" class="form-control form-control-sm shadow-sm" placeholder="Keterangan tambahan (Opsional)">
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm"><i class="fa-solid fa-upload me-1"></i> Simpan</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Filter Pencarian Dokumen -->
                            <div class="px-3 px-md-4 py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                                <span class="small fw-bold text-muted"><i class="fa-solid fa-list me-1"></i> Daftar Dokumen Terupload</span>
                                <form method="GET" class="d-flex gap-2 m-0">
                                    <select name="filter_jenis" class="form-select form-select-sm shadow-sm border-0" onchange="this.form.submit()" style="min-width: 160px;">
                                        <option value="">Semua Jenis Dokumen</option>
                                        <option value="Kartu Keluarga" <?= $filter_jenis == 'Kartu Keluarga' ? 'selected' : '' ?>>Kartu Keluarga (KK)</option>
                                        <option value="KTP" <?= $filter_jenis == 'KTP' ? 'selected' : '' ?>>KTP</option>
                                        <option value="Akta Kelahiran" <?= $filter_jenis == 'Akta Kelahiran' ? 'selected' : '' ?>>Akta Kelahiran</option>
                                        <option value="Akta Nikah" <?= $filter_jenis == 'Akta Nikah' ? 'selected' : '' ?>>Akta Nikah</option>
                                        <option value="Lainnya" <?= $filter_jenis == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </form>
                            </div>

                            <div class="card-body p-3" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                                <div class="table-responsive m-0">
                                    <table class="table table-hover table-bordered align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>NIK</th>
                                                <th>Nama Pemilik</th>
                                                <th>Jenis Dokumen</th>
                                                <th>Keterangan</th>
                                                <th>Tanggal Upload</th>
                                                <th style="min-width: 90px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($dokumen) > 0): ?>
                                                <?php $no = 1; foreach ($dokumen as $row): ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td class="text-center fw-bold"><?= htmlspecialchars($row['nik']) ?></td>
                                                    <td class="text-primary fw-bold"><?= htmlspecialchars($row['nama_pemilik']) ?></td>
                                                    <td class="text-center"><span class="badge bg-info text-dark shadow-sm"><?= htmlspecialchars($row['jenis_dokumen']) ?></span></td>
                                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                                    <td class="text-center text-muted small"><?= date('d M Y, H:i', strtotime($row['tanggal_upload'])) ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= $dokumen_dir . rawurlencode($row['file_dokumen']) ?>" target="_blank" class="btn btn-sm btn-success border-0" title="Lihat Dokumen"><i class="fa-solid fa-eye"></i></a>
                                                        <button onclick="konfirmasiHapus(<?= $row['id_dokumen'] ?>, '<?= htmlspecialchars($row['file_dokumen']) ?>')" class="btn btn-sm btn-danger border-0" title="Hapus Dokumen"><i class="fa-solid fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada dokumen yang diunggah.</td></tr>
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
    <script>Swal.fire({icon: '<?= $alert['status'] ?>', title: '<?= ($alert['status'] == "success") ? "Berhasil" : "Gagal" ?>', text: '<?= addslashes($alert['message']) ?>', showConfirmButton: false, timer: 3000});</script>
    <?php endif; ?>

    <script>
        function konfirmasiHapus(id, file) { Swal.fire({ title: 'Hapus Dokumen?', text: "File dokumen ini akan dihapus permanen!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' }).then((result) => { if (result.isConfirmed) { window.location.href = `hapus_dokumen.php?id=${id}&file=${file}`; } }) }
        function updateClock() { const now = new Date(); document.getElementById('clock-container').innerHTML = now.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':'); } setInterval(updateClock, 1000); updateClock();
        document.addEventListener('DOMContentLoaded', function() { const sidebar = document.querySelector('.sidebar'), sidebarToggle = document.getElementById('sidebarToggleTop'), sidebarOverlay = document.getElementById('sidebarOverlay'); if (sidebarToggle && sidebar && sidebarOverlay) { sidebarToggle.addEventListener('click', e => { e.preventDefault(); sidebar.classList.toggle('show'); sidebarOverlay.classList.toggle('show'); }); sidebarOverlay.addEventListener('click', () => { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); }); } });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>