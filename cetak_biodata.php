<?php
require_once 'auth_check.php';
require_once 'koneksi.php';

if (!isset($_GET['nik'])) {
    die("Data tidak ditemukan.");
}

$nik = $_GET['nik'];
$stmt = $pdo->prepare("SELECT * FROM penduduk WHERE nik = :nik");
$stmt->execute([':nik' => $nik]);
$data = $stmt->fetch();

if (!$data) {
    die("Data penduduk tidak ditemukan.");
}

// Ambil Nama Kepala Desa dari Pengaturan (profil.json)
$profil_file = 'profil.json';
$nama_kades = 'ANJELIUS SORANI BAGO S.KOM'; // Nama Default
if (file_exists($profil_file)) {
    $profil_data = json_decode(file_get_contents($profil_file), true);
    $nama_kades = $profil_data['nama_kades'] ?? $nama_kades;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=900"> <!-- Memaksa HP merender seperti desktop untuk mempertahankan proporsi A4 -->
    <title>Cetak Biodata - <?= htmlspecialchars($data['nama_lengkap']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.5;
            margin: 0;
            padding: 30px 0; /* Jarak luar kertas ke tepi layar di mode preview */
            background-color: #e2e8f0;
        }
        .kertas-dokumen {
            background-color: #fff;
            width: 210mm; /* Lebar standar A4 */
            min-height: 297mm; /* Tinggi standar A4 */
            margin: 0 auto;
            padding: 2cm; /* Ruang putih margin di dalam kertas */
            box-sizing: border-box;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .kop-surat {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
            padding-top: 10px;
        }
        .kop-surat::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 2px;
            border-bottom: 3px solid #000;
        }
        .kop-surat img {
            position: absolute;
            left: 15px;
            top: 5px;
            width: 105px;
            height: auto;
        }
        .kop-surat h2, .kop-surat h3, .kop-surat p { margin: 0; }
        .kop-surat h2 { font-size: 18px; text-transform: uppercase; }
        .kop-surat h3 { font-size: 22px; text-transform: uppercase; font-weight: bold; }
        .kop-surat p { font-size: 14px; margin-top: 5px; }
        
        .judul {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .table-data td {
            padding: 6px 4px;
            vertical-align: top;
            font-size: 16px; /* Standar baku ukuran 12pt */
        }
        .col-label { width: 32%; font-weight: bold; }
        .col-separator { width: 3%; text-align: center; }
        .col-value { width: 45%; }
        .col-foto { width: 20%; text-align: right; vertical-align: top; padding-right: 0; }
        
        .pas-foto {
            width: 3cm;
            height: 4cm;
            border: 2px solid #000;
            text-align: center;
            line-height: 4cm;
            font-size: 16px;
            background-color: #f8f9fa;
            float: right;
            overflow: hidden;
        }
        .pas-foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .ttd-box {
            width: 250px;
            float: right;
            text-align: center;
            margin-top: 30px;
        }
        .ttd-box p { margin: 0; font-size: 16px; }
        .ttd-space { height: 80px; }
        .ttd-img {
            height: 80px;
            width: 100%;
            object-fit: contain;
            transform: scale(4.5) translateY(-15px); /* Digeser lebih jauh ke atas */
            margin: 0 auto; /* Menghilangkan jarak atas yang mendorong gambar ke bawah */
            display: block;
        }
        .ttd-name { font-weight: bold; text-decoration: underline; font-size: 14px; white-space: nowrap; }
        
        .editable-date {
            border-bottom: 1px dashed #94a3b8;
            cursor: text;
            padding: 0 2px;
            outline: none;
        }
        .editable-date:hover, .editable-date:focus { background-color: #f1f5f9; }
        
        @page {
            size: A4 portrait;
            margin: 2cm;
        }
        @media print {
            body { padding: 0; margin: 0; background-color: #fff; }
            .kertas-dokumen { 
                padding: 0; margin: 0; width: 100%; min-height: auto; 
                box-shadow: none; border-radius: 0; overflow-x: visible; 
            }
            .no-print { display: none; }
            .editable-date { border-bottom: none; background-color: transparent !important; }
        }
        
        .no-print { margin-bottom: 30px; text-align: center; }
        .btn-print {
            background: #dc2626; color: #fff; padding: 12px 30px; border: none;
            border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 18px; font-family: sans-serif;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak ke PDF</button>
    </div>
    
<div class="kertas-dokumen">
    <div class="kop-surat">
        <img src="logo.png?t=<?= time() ?>" alt="Logo Desa" onerror="this.style.display='none'">
        <h2>PEMERINTAH PROVINSI SUMATERA UTARA</h2>
        <h2>KABUPATEN NIAS SELATAN</h2>
        <h2>KECAMATAN ONOLALU</h2>
        <h3>DESA HILIFALAGO</h3>
        <p>Alamat: Kantor Kepala Desa Hilifalago, Kode Pos: 22865</p>
    </div>

    <div class="judul">BIODATA PENDUDUK</div>

    <table class="table-data">
        <tr>
            <td class="col-label">Nomor Induk Kependudukan (NIK)</td><td class="col-separator">:</td><td class="col-value"><?= htmlspecialchars($data['nik']) ?></td>
            <td rowspan="6" class="col-foto">
                <div class="pas-foto">
                    <?php 
                    $foto_path = "foto_warga/" . $data['nik'] . ".jpg";
                    if (file_exists($foto_path)) {
                        echo '<img src="' . $foto_path . '?t=' . time() . '" alt="Pas Foto">';
                    } else {
                        echo '3 x 4';
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr><td class="col-label">Nomor Kartu Keluarga (KK)</td><td class="col-separator">:</td><td class="col-value"><?= htmlspecialchars($data['no_kk']) ?></td></tr>
        <tr><td class="col-label">Nama Lengkap</td><td class="col-separator">:</td><td class="col-value"><?= htmlspecialchars($data['nama_lengkap']) ?></td></tr>
        <tr><td class="col-label">Tempat, Tanggal Lahir</td><td class="col-separator">:</td><td class="col-value"><?= htmlspecialchars($data['tempat_lahir']) ?>, <?= date('d F Y', strtotime($data['tanggal_lahir'])) ?></td></tr>
        <tr><td class="col-label">Jenis Kelamin</td><td class="col-separator">:</td><td class="col-value"><?= htmlspecialchars($data['jenis_kelamin']) ?></td></tr>
        <tr><td class="col-label">Agama</td><td class="col-separator">:</td><td class="col-value"><?= htmlspecialchars($data['agama']) ?></td></tr>
        <tr><td class="col-label">Status Perkawinan</td><td class="col-separator">:</td><td colspan="2"><?= htmlspecialchars($data['status_perkawinan']) ?></td></tr>
        <tr><td class="col-label">Pendidikan Terakhir</td><td class="col-separator">:</td><td colspan="2"><?= htmlspecialchars($data['pendidikan']) ?></td></tr>
        <tr><td class="col-label">Pekerjaan</td><td class="col-separator">:</td><td colspan="2"><?= htmlspecialchars($data['pekerjaan']) ?></td></tr>
        <tr><td class="col-label">Alamat Lengkap</td><td class="col-separator">:</td><td colspan="2"><?= htmlspecialchars($data['alamat']) ?></td></tr>
        <tr><td class="col-label">Dusun</td><td class="col-separator">:</td><td colspan="2"><?= htmlspecialchars($data['rt']) ?: '-' ?></td></tr>
        <tr><td class="col-label">Status Penduduk</td><td class="col-separator">:</td><td colspan="2"><?= htmlspecialchars($data['status_penduduk']) ?></td></tr>
    </table>

    <div class="ttd-box">
        <?php
        $bulan = ['January'=>'Januari', 'February'=>'Februari', 'March'=>'Maret', 'April'=>'April', 'May'=>'Mei', 'June'=>'Juni', 'July'=>'Juli', 'August'=>'Agustus', 'September'=>'September', 'October'=>'Oktober', 'November'=>'November', 'December'=>'Desember'];
        $tgl_sekarang = date('d') . ' ' . $bulan[date('F')] . ' ' . date('Y');
        ?>
        <p>Hilifalago, <span contenteditable="true" class="editable-date" title="Klik untuk mengedit tanggal"><?= $tgl_sekarang ?></span></p>
        <p>Kepala Desa Hilifalago</p>
        <?php if (file_exists('ttd_kades.png')): ?>
            <img src="ttd_kades.png?t=<?= time() ?>" alt="Tanda Tangan Kades" class="ttd-img">
        <?php else: ?>
            <div class="ttd-space"></div>
        <?php endif; ?>
        <p class="ttd-name"><?= htmlspecialchars($nama_kades) ?></p>
    </div>
</div>

</body>
</html>