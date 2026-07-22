<?php
require_once 'auth_check.php';
require_once 'koneksi.php';

// Mengambil data statistik penduduk
try {
    $sql = "SELECT 
                COUNT(*) as total_penduduk,
                SUM(CASE WHEN jenis_kelamin = 'Laki-laki' THEN 1 ELSE 0 END) as total_l,
                SUM(CASE WHEN jenis_kelamin = 'Perempuan' THEN 1 ELSE 0 END) as total_p,
                SUM(CASE WHEN status_penduduk = 'Menunggu Verifikasi' THEN 1 ELSE 0 END) as total_v
            FROM penduduk";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $total_penduduk = $result['total_penduduk'] ?? 0;
    $total_l = $result['total_l'] ?? 0;
    $total_p = $result['total_p'] ?? 0;
    $total_verifikasi = $result['total_v'] ?? 0;
} catch (PDOException $e) {
    $total_penduduk = $total_l = $total_p = $total_verifikasi = 0;
}

// Mengambil data status perkawinan untuk chart (Pengganti Pendidikan)
$stmt_kawin = $pdo->query("SELECT status_perkawinan, COUNT(*) as count FROM penduduk WHERE status_penduduk NOT IN ('Meninggal', 'Pindah') AND status_perkawinan != '' GROUP BY status_perkawinan");
$kawin_data = $stmt_kawin->fetchAll();
$kawin_labels = []; $kawin_counts = [];
foreach ($kawin_data as $row) {
    $kawin_labels[] = $row['status_perkawinan'];
    $kawin_counts[] = $row['count'];
}

// Mengambil data kelompok umur
$stmt_umur = $pdo->query("SELECT 
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= 5 THEN 1 ELSE 0 END) as balita,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 17 THEN 1 ELSE 0 END) as remaja,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 59 THEN 1 ELSE 0 END) as produktif,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60 THEN 1 ELSE 0 END) as lansia
    FROM penduduk WHERE tanggal_lahir IS NOT NULL AND status_penduduk NOT IN ('Meninggal', 'Pindah')");
$umur_data = $stmt_umur->fetch(PDO::FETCH_ASSOC);
$umur_counts = [
    $umur_data['balita'] ?? 0, 
    $umur_data['remaja'] ?? 0, 
    $umur_data['produktif'] ?? 0, 
    $umur_data['lansia'] ?? 0
];

// Mengambil data pekerjaan (Top 6)
$stmt_job = $pdo->query("SELECT pekerjaan, COUNT(*) as count FROM penduduk WHERE pekerjaan != '' AND status_penduduk NOT IN ('Meninggal', 'Pindah') GROUP BY pekerjaan ORDER BY count DESC LIMIT 6");
$job_data = $stmt_job->fetchAll();
$job_labels = []; $job_counts = [];
foreach ($job_data as $row) {
    $job_labels[] = $row['pekerjaan'];
    $job_counts[] = $row['count'];
}

// Mengambil data pendidikan
$stmt_edu = $pdo->query("SELECT 
    CASE 
        WHEN pendidikan LIKE '%S2%' OR pendidikan LIKE '%S3%' OR pendidikan LIKE '%Magister%' OR pendidikan LIKE '%Doktor%' OR pendidikan LIKE '%Pascasarjana%' THEN 'Pascasarjana (S2/S3)'
        WHEN pendidikan LIKE '%S1%' OR pendidikan LIKE '%D4%' OR pendidikan LIKE '%Sarjana%' THEN 'Sarjana (S1/D4)'
        WHEN pendidikan LIKE '%D1%' OR pendidikan LIKE '%D2%' OR pendidikan LIKE '%D3%' OR pendidikan LIKE '%Diploma%' THEN 'Diploma (D1-D3)'
        WHEN pendidikan LIKE '%SMA%' OR pendidikan LIKE '%SMK%' OR pendidikan LIKE '%SLTA%' OR pendidikan LIKE '%MA%' THEN 'SMA/Sederajat'
        WHEN pendidikan LIKE '%SMP%' OR pendidikan LIKE '%SLTP%' OR pendidikan LIKE '%MTs%' THEN 'SMP/Sederajat'
        WHEN pendidikan LIKE '%SD%' OR pendidikan LIKE '%Sekolah Dasar%' OR pendidikan LIKE '%MI%' THEN 'SD/Sederajat'
        WHEN pendidikan IS NULL OR pendidikan = '' OR pendidikan LIKE '%Tidak%' OR pendidikan LIKE '%Belum%' THEN 'Tidak/Belum Sekolah'
        ELSE 'Lainnya'
    END as tingkat, 
    COUNT(*) as count 
    FROM penduduk WHERE status_penduduk NOT IN ('Meninggal', 'Pindah') 
    GROUP BY tingkat 
    ORDER BY CASE tingkat WHEN 'Tidak/Belum Sekolah' THEN 1 WHEN 'SD/Sederajat' THEN 2 WHEN 'SMP/Sederajat' THEN 3 WHEN 'SMA/Sederajat' THEN 4 WHEN 'Diploma (D1-D3)' THEN 5 WHEN 'Sarjana (S1/D4)' THEN 6 WHEN 'Pascasarjana (S2/S3)' THEN 7 ELSE 8 END ASC");
$edu_data = $stmt_edu->fetchAll();
$edu_labels = []; $edu_counts = [];
foreach ($edu_data as $row) {
    $edu_labels[] = $row['tingkat'];
    $edu_counts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#4e73df">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Dashboard - Desa Hilifalago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php include 'sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 d-flex flex-column" style="height: 100vh; overflow-x: hidden;">
                
                <?php include 'topbar.php'; ?>

                <!-- Konten Dashboard -->
                <div class="flex-grow-1 d-flex flex-column" style="overflow-y: auto;">
                    <div class="pt-0 pb-2 px-4 flex-grow-1">
                        <!-- Header Dashboard -->
                        <div class="d-sm-flex align-items-center justify-content-between mb-3 mt-2">
                            <h1 class="h4 mb-0 text-gray-800 fw-bold" style="color: #4e73df;"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard Admin</h1>
                            <div class="mt-3 mt-sm-0">
                                <a href="tambah_penduduk.php" class="btn btn-sm btn-primary shadow-sm me-2"><i class="fa-solid fa-user-plus text-white-50 me-1"></i> Warga Baru</a>
                                <a href="tambah_berita.php" class="btn btn-sm btn-success shadow-sm"><i class="fa-solid fa-pen text-white-50 me-1"></i> Tulis Info/Berita</a>
                            </div>
                        </div>

                        <!-- Row Statistik (Ringkas) -->
                        <div class="row g-3 mb-3">
                            <!-- Total Penduduk Card -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-stats border-left-primary h-100 py-1">
                                    <div class="card-body p-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Warga</div>
                                                <div class="h5 mb-0 fw-bold text-gray-800"><?= $total_penduduk ?> Jiwa</div>
                                            </div>
                                            <div class="col-auto"><i class="fa-solid fa-users fa-2x text-gray-300" style="color: #dddfeb;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Laki-laki Card -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-stats border-left-info h-100 py-1">
                                    <div class="card-body p-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Laki-laki</div>
                                                <div class="h5 mb-0 fw-bold text-gray-800"><?= $total_l ?> Jiwa</div>
                                            </div>
                                            <div class="col-auto"><i class="fa-solid fa-person fa-2x text-gray-300" style="color: #dddfeb;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Perempuan Card -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-stats border-left-success h-100 py-1">
                                    <div class="card-body p-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs fw-bold text-success text-uppercase mb-1">Perempuan</div>
                                                <div class="h5 mb-0 fw-bold text-gray-800"><?= $total_p ?> Jiwa</div>
                                            </div>
                                            <div class="col-auto"><i class="fa-solid fa-person-dress fa-2x text-gray-300" style="color: #dddfeb;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Menunggu Verifikasi Card -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-stats border-left-warning h-100 py-1">
                                    <div class="card-body p-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Verifikasi</div>
                                                <div class="h5 mb-0 fw-bold text-gray-800"><?= $total_verifikasi ?> Data</div>
                                            </div>
                                            <div class="col-auto"><i class="fa-solid fa-clock-rotate-left fa-2x text-gray-300" style="color: #dddfeb;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row Charts (Grid Rapi) -->
                        <div class="row g-3 mb-4">
                            <!-- Baris 1 -->
                            <div class="col-lg-4 col-md-5">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header bg-transparent border-bottom pt-3 px-3 pb-2"><h6 class="m-0 fw-bold text-primary" style="font-size: 0.95rem;"><i class="fa-solid fa-venus-mars me-2"></i>Komposisi L/P</h6></div>
                                    <div class="card-body d-flex justify-content-center align-items-center p-3">
                                        <div style="width: 100%; max-width: 200px; height: 200px;"><canvas id="genderPieChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header bg-transparent border-bottom pt-3 px-3 pb-2"><h6 class="m-0 fw-bold text-success" style="font-size: 0.95rem;"><i class="fa-solid fa-users-viewfinder me-2"></i>Kelompok Umur</h6></div>
                                    <div class="card-body p-3">
                                        <div style="width: 100%; height: 200px;"><canvas id="ageBarChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Baris 2 -->
                            <div class="col-lg-4 col-md-5">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header bg-transparent border-bottom pt-3 px-3 pb-2"><h6 class="m-0 fw-bold text-warning" style="font-size: 0.95rem;"><i class="fa-solid fa-briefcase me-2"></i>Pekerjaan (Top 6)</h6></div>
                                    <div class="card-body d-flex justify-content-center align-items-center p-3">
                                        <div style="width: 100%; max-width: 200px; height: 200px;"><canvas id="jobBarChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header bg-transparent border-bottom pt-3 px-3 pb-2"><h6 class="m-0 fw-bold text-info" style="font-size: 0.95rem;"><i class="fa-solid fa-ring me-2"></i>Status Perkawinan</h6></div>
                                    <div class="card-body p-3">
                                        <div style="width: 100%; height: 200px;"><canvas id="kawinBarChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Baris 3 -->
                            <div class="col-lg-12">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header bg-transparent border-bottom pt-3 px-3 pb-2"><h6 class="m-0 fw-bold text-secondary" style="font-size: 0.95rem;"><i class="fa-solid fa-graduation-cap me-2"></i>Statistik Pendidikan Warga</h6></div>
                                    <div class="card-body p-3">
                                        <div style="width: 100%; height: 220px;"><canvas id="educationBarChart"></canvas></div>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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

        // Inisialisasi Chart.js
        const ctxPie = document.getElementById('genderPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{ data: [<?= $total_l ?>, <?= $total_p ?>], backgroundColor: ['#36b9cc', '#1cc88a'], hoverBackgroundColor: ['#2c9faf', '#17a673'], hoverBorderColor: 'rgba(234, 236, 244, 1)' }]
            },
            options: { maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });

        const ctxKawin = document.getElementById('kawinBarChart').getContext('2d');
        new Chart(ctxKawin, {
            type: 'bar',
            data: {
                labels: <?= json_encode($kawin_labels) ?>,
                datasets: [{ label: 'Jumlah Warga', data: <?= json_encode($kawin_counts) ?>, backgroundColor: '#36b9cc', hoverBackgroundColor: '#2a96a5', maxBarThickness: 50 }]
            },
            options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } } }
        });

        const ctxAge = document.getElementById('ageBarChart').getContext('2d');
        new Chart(ctxAge, {
            type: 'bar',
            data: {
                labels: ['Balita (0-5 th)', 'Remaja (6-17 th)', 'Produktif (18-59 th)', 'Lansia (60+ th)'],
                datasets: [{ label: 'Jumlah Jiwa', data: <?= json_encode($umur_counts) ?>, backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a', '#e74a3b'], maxBarThickness: 50 }]
            },
            options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } } }
        });

        const ctxJob = document.getElementById('jobBarChart').getContext('2d');
        new Chart(ctxJob, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($job_labels) ?>,
                datasets: [{ data: <?= json_encode($job_counts) ?>, backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'], hoverOffset: 4 }]
            },
            options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
        });

        const ctxEdu = document.getElementById('educationBarChart').getContext('2d');
        new Chart(ctxEdu, {
            type: 'bar',
            data: {
                labels: <?= json_encode($edu_labels) ?>,
                datasets: [{ label: 'Jumlah Warga', data: <?= json_encode($edu_counts) ?>, backgroundColor: '#4e73df', hoverBackgroundColor: '#2e59d9', maxBarThickness: 40 }]
            },
            options: { maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } } }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

