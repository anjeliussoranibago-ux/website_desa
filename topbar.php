<?php
$search_val = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>
<nav class="navbar navbar-expand navbar-light topbar static-top px-4 d-flex justify-content-between align-items-center">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <span class="fs-4">☰</span>
    </button>
    <form action="data_penduduk.php" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" style="min-width: 250px;">
        <div class="input-group">
            <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Cari NIK / Nama..." aria-label="Search" value="<?= $search_val ?>" style="background-color: #f3f4f6 !important;">
            <button class="btn btn-primary px-3" type="submit" title="Cari Data">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item d-none d-md-block me-3">
            <div id="clock-container" class="fw-bold px-3 py-2 shadow-sm rounded border-start border-3 border-primary" style="background-color: #f8f9fc; color: #0b214a; font-size: 0.85rem; letter-spacing: 0.5px;"></div>
        </li>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small fw-bold" style="color: #858796;">Admin</span>
                <img class="img-profile rounded-circle" src="foto_kades.jpg?t=<?= time() ?>" onerror="this.src='https://via.placeholder.com/60'" style="width: 2rem; height: 2rem; object-fit: cover; border-radius: 50%;">
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="border: 1px solid #e3e6f0; border-radius: 0.35rem; margin-top: 0.5rem; min-width: 12rem;">
                <li><a class="dropdown-item d-flex align-items-center text-secondary py-2" href="pengaturan.php"><i class="fa-solid fa-gear me-2 fs-6"></i> Pengaturan Web</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item d-flex align-items-center text-danger fw-bold py-2" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2 fs-6"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>