<?php
$search_val = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>
<nav class="navbar navbar-expand navbar-light topbar static-top px-3 px-md-4 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle text-dark p-1 fs-5" style="text-decoration: none;">
            <i class="fa-solid fa-bars"></i>
        </button>
        <form action="data_penduduk.php" method="GET" class="d-none d-sm-inline-block mw-100 search-form" style="min-width: 260px;">
            <div class="input-group">
                <input type="text" name="search" class="form-control border-0 small" placeholder="Cari NIK / Nama..." aria-label="Search" value="<?= $search_val ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>
    <ul class="navbar-nav align-items-center gap-1 gap-md-3">
        <li class="nav-item d-none d-md-block">
            <div id="clock-container" class="clock-widget"></div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center gap-2 px-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-none d-lg-inline fw-bold text-dark small">Admin</span>
                <img class="img-profile rounded-circle border border-2 border-primary" src="foto_kades.jpg?t=<?= time() ?>" onerror="this.src='https://via.placeholder.com/60'" style="width: 36px; height: 36px; object-fit: cover;">
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown" style="min-width: 13rem;">
                <li><h6 class="dropdown-header fw-bold text-dark">Pengaturan</h6></li>
                <li><a class="dropdown-item d-flex align-items-center py-2" href="pengaturan.php"><i class="fa-solid fa-gear me-2 text-primary"></i> Pengaturan Web</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item d-flex align-items-center text-danger fw-bold py-2" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
