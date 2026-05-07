<?php
require_once 'auth_check.php';
require_once 'koneksi.php';

// Cek alert dari session (notifikasi)
$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);

// Proses Pencarian dan Ambil Data
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM penduduk";
$params = [];

if (!empty($search)) {
    $query .= " WHERE nama_lengkap LIKE :search OR nik LIKE :search";
    $params[':search'] = "%$search%";
}
$query .= " ORDER BY nama_lengkap ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data_penduduk = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Data Penduduk - Desa Hilifalago</title>
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
        .text-primary { color: #4e73df !important; }
        .text-secondary { color: #4e73df !important; font-weight: bold; }
        
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

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 d-flex flex-column" style="height: 100vh; overflow-x: hidden;">
                
                <?php include 'topbar.php'; ?>

                <!-- Konten Tabel -->
                <div class="flex-grow-1 d-flex flex-column" style="overflow: hidden;">
                    <div class="pt-0 pb-0 px-3 d-flex flex-column flex-grow-1" style="overflow: hidden;">
                        <div class="card shadow-sm glass-card border-0 mb-0 mt-0 flex-grow-1 d-flex flex-column" style="max-height: 100%; overflow: hidden; border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important;">
                            <div class="card-header bg-transparent border-bottom pt-3 px-4 pb-3 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-users me-2"></i>Daftar Penduduk</h5>
                                <a href="tambah_penduduk.php" class="btn btn-primary btn-sm shadow-sm"><i class="fa-solid fa-user-plus me-1"></i> Tambah Data</a>
                            </div>
                            <div class="card-body p-3" style="overflow-y: auto; -webkit-overflow-scrolling: touch;">
                                
                                <div class="table-responsive m-0">
                                    <table class="table table-hover table-bordered align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>Foto</th>
                                                <th>NIK</th>
                                                <th>Nama Lengkap</th>
                                                <th>L/P</th>
                                                <th>Dusun</th>
                                                <th>Status</th>
                                                <th style="min-width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($data_penduduk) > 0): ?>
                                                <?php $no = 1; foreach ($data_penduduk as $row): ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td class="text-center">
                                                        <img src="foto_warga/<?= htmlspecialchars($row['nik']) ?>.jpg?t=<?= time() ?>" onerror="this.src='https://via.placeholder.com/40'" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                                    </td>
                                                    <td class="text-center"><?= htmlspecialchars($row['nik']) ?></td>
                                                    <td class="fw-bold text-primary"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row['jenis_kelamin'] == 'Laki-laki' ? 'L' : 'P') ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row['rt']) ?></td>
                                                    <td class="text-center">
                                                        <?php 
                                                            $badge = 'bg-success';
                                                            if ($row['status_penduduk'] == 'Menunggu Verifikasi') $badge = 'bg-warning text-dark';
                                                            elseif ($row['status_penduduk'] == 'Pindah' || $row['status_penduduk'] == 'Meninggal') $badge = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?= $badge ?>"><?= htmlspecialchars($row['status_penduduk']) ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="lihat_penduduk.php?nik=<?= $row['nik'] ?>" class="btn btn-sm btn-info border-0 text-white" title="Lihat Detail"><i class="fa-solid fa-eye"></i></a>
                                                        <a href="edit_penduduk.php?nik=<?= $row['nik'] ?>" class="btn btn-sm btn-light border" title="Edit Data"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <button onclick="konfirmasiHapus('<?= $row['nik'] ?>')" class="btn btn-sm btn-danger border-0" title="Hapus Data"><i class="fa-solid fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada data penduduk yang ditemukan.</td>
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

    <!-- SweetAlert Notifikasi -->
    <?php if ($alert): ?>
    <script>
        Swal.fire({
            icon: '<?= $alert['status'] ?>',
            title: '<?= ($alert['status'] == "success") ? "Berhasil" : "Terjadi Kesalahan" ?>',
            text: '<?= addslashes($alert['message']) ?>',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
    <?php endif; ?>

    <script>
        function konfirmasiHapus(nik) {
            Swal.fire({ title: 'Hapus Data?', text: "Data yang dihapus tidak dapat dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' }).then((result) => {
                if (result.isConfirmed) { window.location.href = 'hapus_penduduk.php?nik=' + nik; }
            })
        }
        // Fungsi Sidebar & Jam...
        function updateClock() { const now = new Date(); document.getElementById('clock-container').innerHTML = now.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':'); } setInterval(updateClock, 1000); updateClock();
        document.addEventListener('DOMContentLoaded', function() { 
            const sidebar = document.querySelector('.sidebar'), sidebarToggle = document.getElementById('sidebarToggleTop'), sidebarOverlay = document.getElementById('sidebarOverlay'); 
            if (sidebarToggle && sidebar && sidebarOverlay) { 
                sidebarToggle.addEventListener('click', e => { e.preventDefault(); sidebar.classList.toggle('show'); sidebarOverlay.classList.toggle('show'); }); 
                sidebarOverlay.addEventListener('click', () => { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); }); 
                
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