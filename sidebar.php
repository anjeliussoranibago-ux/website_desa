<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div id="sidebarOverlay" class="sidebar-overlay d-md-none"></div>
<div class="col-md-3 col-lg-2 sidebar p-3 text-white">
    <div class="sidebar-brand">
        <img src="logo.png?t=<?= time() ?>" alt="Logo Desa" class="logo-img mb-2" onerror="this.src='https://via.placeholder.com/90?text=Logo'">
        <h6 class="fw-bold mt-2 mb-0 small text-uppercase opacity-75" style="letter-spacing: 2px;">Desa Hilifalago</h6>
    </div>
    <div class="sidebar-scroll-content flex-grow-1">
        <ul class="nav flex-column mt-2">
            <li class="nav-item"><a href="dashboard.php" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li class="nav-item"><a href="data_penduduk.php" class="nav-link <?= in_array($current_page, ['data_penduduk.php', 'edit_penduduk.php', 'lihat_penduduk.php']) ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Data Penduduk</a></li>
            <li class="nav-item"><a href="dokumen_penduduk.php" class="nav-link <?= in_array($current_page, ['dokumen_penduduk.php']) ? 'active' : '' ?>"><i class="fa-solid fa-folder-open"></i> Dokumen Penduduk</a></li>
            <li class="nav-item"><a href="tambah_penduduk.php" class="nav-link <?= $current_page == 'tambah_penduduk.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-plus"></i> Tambah Penduduk</a></li>
            <li class="nav-item"><a href="berita.php" class="nav-link <?= in_array($current_page, ['berita.php', 'tambah_berita.php', 'edit_berita.php']) ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn"></i> Info & Pengumuman</a></li>
            <li class="nav-item"><a href="aparatur.php" class="nav-link <?= $current_page == 'aparatur.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-tie"></i> Aparatur Desa</a></li>
            <li class="nav-item"><a href="galeri.php" class="nav-link <?= $current_page == 'galeri.php' ? 'active' : '' ?>"><i class="fa-solid fa-images"></i> Galeri Desa</a></li>
        </ul>
    </div>
</div>
