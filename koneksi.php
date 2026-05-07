<?php
$host = '127.0.0.1';
$dbname = 'db_desa_hilifalago';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Otomatis membuat tabel jika belum ada di database untuk mencegah error
    $pdo->exec("CREATE TABLE IF NOT EXISTS galeri (
        id_galeri INT AUTO_INCREMENT PRIMARY KEY,
        file_foto VARCHAR(255),
        judul_kegiatan VARCHAR(150),
        tanggal_kegiatan DATE
    )");
    
    // Auto-populate galeri jika masih kosong dengan foto profil/background desa sebelumnya
    $cek_galeri = $pdo->query("SELECT COUNT(*) FROM galeri")->fetchColumn();
    if ($cek_galeri == 0) {
        $default_photos = ['omohada.jpg', 'hhhh.jpg', 'hilifalago.jpg', 'hhh.jpg'];
        $galeri_dir = 'galeri_img/';
        if (!is_dir($galeri_dir)) {
            @mkdir($galeri_dir, 0777, true);
        }
        foreach ($default_photos as $foto) {
            if (file_exists($foto)) {
                if (!file_exists($galeri_dir . $foto)) {
                    @copy($foto, $galeri_dir . $foto);
                }
                $stmt = $pdo->prepare("INSERT INTO galeri (file_foto, judul_kegiatan, tanggal_kegiatan) VALUES (?, 'Profil Desa', CURDATE())");
                $stmt->execute([$foto]);
            }
        }
    }
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS aparatur_desa (
        id_aparatur INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100),
        jabatan VARCHAR(100),
        foto VARCHAR(255),
        urutan INT
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS berita_informasi (
        id_berita INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(200),
        slug VARCHAR(200),
        isi_berita TEXT,
        gambar_cover VARCHAR(255),
        tanggal_publikasi DATETIME,
        status ENUM('Draft', 'Published') DEFAULT 'Published'
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS dokumen_penduduk (
        id_dokumen INT AUTO_INCREMENT PRIMARY KEY,
        nik VARCHAR(16),
        nama_pemilik VARCHAR(150),
        jenis_dokumen VARCHAR(100),
        file_dokumen VARCHAR(255),
        keterangan TEXT,
        tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>