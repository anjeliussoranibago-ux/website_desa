<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div id="sidebarOverlay" class="sidebar-overlay d-md-none"></div>
<div class="col-md-3 col-lg-2 sidebar p-3 text-white">
    <div class="text-center mb-4 mt-3">
        <img src="logo.png?t=<?= time() ?>" alt="Logo Desa" class="logo-img mb-2" onerror="this.src='https://via.placeholder.com/90?text=Logo'">
        <h6 class="fw-bold mt-2 mb-0 text-uppercase" style="letter-spacing: 1px;">Desa Hilifalago</h6>
    </div>
    <div class="sidebar-scroll-content flex-grow-1">
        <hr class="sidebar-divider mb-3 mt-0" style="border-top: 1px solid rgba(255,255,255,0.15);">
        <ul class="nav flex-column">
            <li class="nav-item"><a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a href="data_penduduk.php" class="nav-link <?= in_array($current_page, ['data_penduduk.php', 'edit_penduduk.php', 'lihat_penduduk.php']) ? 'active' : '' ?>"><i class="fa-solid fa-users me-2"></i> Data Penduduk</a></li>
            <li class="nav-item"><a href="dokumen_penduduk.php" class="nav-link <?= in_array($current_page, ['dokumen_penduduk.php']) ? 'active' : '' ?>"><i class="fa-solid fa-folder-open me-2"></i> Dokumen Penduduk</a></li>
            <li class="nav-item"><a href="tambah_penduduk.php" class="nav-link <?= $current_page == 'tambah_penduduk.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-plus me-2"></i> Tambah Penduduk</a></li>
            <li class="nav-item"><a href="berita.php" class="nav-link <?= in_array($current_page, ['berita.php', 'tambah_berita.php', 'edit_berita.php']) ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn me-2"></i> Info & Pengumuman</a></li>
            <li class="nav-item"><a href="aparatur.php" class="nav-link <?= $current_page == 'aparatur.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-tie me-2"></i> Aparatur Desa</a></li>
            <li class="nav-item"><a href="galeri.php" class="nav-link <?= $current_page == 'galeri.php' ? 'active' : '' ?>"><i class="fa-solid fa-images me-2"></i> Galeri Desa</a></li>
        </ul>
    </div>
</div>