-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2026 at 11:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_desa_hilifalago`
--

-- --------------------------------------------------------

--
-- Table structure for table `aparatur_desa`
--

CREATE TABLE `aparatur_desa` (
  `id_aparatur` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aparatur_desa`
--

INSERT INTO `aparatur_desa` (`id_aparatur`, `nama`, `jabatan`, `foto`, `urutan`) VALUES
(1, 'Metawi', 'Sekertaris Desa', 'aparatur_69fb928e6d5d4.jpeg', 1),
(2, 'Jupe', 'Bendahara Desa', 'aparatur_69fb92eb6d373.png', 2),
(3, 'Sanaoha', 'Kaur perencanaan', 'aparatur_69fb934a934fe.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `berita_informasi`
--

CREATE TABLE `berita_informasi` (
  `id_berita` int(11) NOT NULL,
  `judul` varchar(200) DEFAULT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `isi_berita` text DEFAULT NULL,
  `gambar_cover` varchar(255) DEFAULT NULL,
  `tanggal_publikasi` datetime DEFAULT NULL,
  `status` enum('Draft','Published') DEFAULT 'Published'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_penduduk`
--

CREATE TABLE `dokumen_penduduk` (
  `id_dokumen` int(11) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `nama_pemilik` varchar(150) DEFAULT NULL,
  `jenis_dokumen` varchar(100) DEFAULT NULL,
  `file_dokumen` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id_galeri` int(11) NOT NULL,
  `file_foto` varchar(255) DEFAULT NULL,
  `judul_kegiatan` varchar(150) DEFAULT NULL,
  `tanggal_kegiatan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id_galeri`, `file_foto`, `judul_kegiatan`, `tanggal_kegiatan`) VALUES
(8, 'omohada.jpg', 'Profil Desa', '2026-07-22'),
(9, 'hhhh.jpg', 'Profil Desa', '2026-07-22'),
(10, 'hilifalago.jpg', 'Profil Desa', '2026-07-22'),
(11, 'hhh.jpg', 'Profil Desa', '2026-07-22');

-- --------------------------------------------------------

--
-- Table structure for table `kartu_keluarga`
--

CREATE TABLE `kartu_keluarga` (
  `no_kk` varchar(16) NOT NULL,
  `nik_kepala_keluarga` varchar(16) NOT NULL,
  `alamat` text NOT NULL,
  `dusun` varchar(50) NOT NULL,
  `rt` varchar(3) NOT NULL,
  `rw` varchar(3) NOT NULL,
  `tanggal_dikeluarkan` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_surat`
--

CREATE TABLE `master_surat` (
  `id` int(11) NOT NULL,
  `kode_surat` varchar(20) NOT NULL,
  `nama_surat` varchar(100) NOT NULL,
  `format_nomor_surat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mutasi_penduduk`
--

CREATE TABLE `mutasi_penduduk` (
  `id` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `jenis_mutasi` enum('Lahir','Mati','Pindah Keluar','Datang') NOT NULL,
  `tanggal_peristiwa` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penduduk`
--

CREATE TABLE `penduduk` (
  `nik` varchar(16) NOT NULL,
  `no_kk` varchar(16) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `agama` varchar(20) NOT NULL,
  `pendidikan` varchar(50) NOT NULL,
  `pekerjaan` varchar(50) DEFAULT NULL,
  `status_perkawinan` enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') NOT NULL,
  `alamat` text NOT NULL,
  `rt` varchar(3) DEFAULT NULL,
  `rw` varchar(3) DEFAULT NULL,
  `status_penduduk` enum('Tetap','Pendatang','Pindah','Meninggal') DEFAULT 'Tetap',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penerima_bansos`
--

CREATE TABLE `penerima_bansos` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `penerima_id` varchar(16) NOT NULL,
  `tanggal_menerima` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permohonan_surat`
--

CREATE TABLE `permohonan_surat` (
  `id` int(11) NOT NULL,
  `nik_pemohon` varchar(16) NOT NULL,
  `jenis_surat` varchar(100) NOT NULL,
  `keperluan` text NOT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Ditolak') DEFAULT 'Menunggu',
  `tanggal_permohonan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_bansos`
--

CREATE TABLE `program_bansos` (
  `id` int(11) NOT NULL,
  `nama_program` varchar(100) NOT NULL,
  `sasaran` enum('Penduduk','Keluarga') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Aktif','Selesai') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jabatan` enum('Kepala Desa','Sekretaris Desa','Kaur','Admin') DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `jabatan`, `created_at`) VALUES
(1, 'Bago', '2004', 'Admin Hilifalago', 'Admin', '2026-04-16 05:37:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aparatur_desa`
--
ALTER TABLE `aparatur_desa`
  ADD PRIMARY KEY (`id_aparatur`);

--
-- Indexes for table `berita_informasi`
--
ALTER TABLE `berita_informasi`
  ADD PRIMARY KEY (`id_berita`);

--
-- Indexes for table `dokumen_penduduk`
--
ALTER TABLE `dokumen_penduduk`
  ADD PRIMARY KEY (`id_dokumen`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id_galeri`);

--
-- Indexes for table `kartu_keluarga`
--
ALTER TABLE `kartu_keluarga`
  ADD PRIMARY KEY (`no_kk`);

--
-- Indexes for table `master_surat`
--
ALTER TABLE `master_surat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_surat` (`kode_surat`);

--
-- Indexes for table `mutasi_penduduk`
--
ALTER TABLE `mutasi_penduduk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `penduduk`
--
ALTER TABLE `penduduk`
  ADD PRIMARY KEY (`nik`);

--
-- Indexes for table `penerima_bansos`
--
ALTER TABLE `penerima_bansos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `permohonan_surat`
--
ALTER TABLE `permohonan_surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nik_pemohon` (`nik_pemohon`);

--
-- Indexes for table `program_bansos`
--
ALTER TABLE `program_bansos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aparatur_desa`
--
ALTER TABLE `aparatur_desa`
  MODIFY `id_aparatur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `berita_informasi`
--
ALTER TABLE `berita_informasi`
  MODIFY `id_berita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dokumen_penduduk`
--
ALTER TABLE `dokumen_penduduk`
  MODIFY `id_dokumen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id_galeri` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `master_surat`
--
ALTER TABLE `master_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mutasi_penduduk`
--
ALTER TABLE `mutasi_penduduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penerima_bansos`
--
ALTER TABLE `penerima_bansos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permohonan_surat`
--
ALTER TABLE `permohonan_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_bansos`
--
ALTER TABLE `program_bansos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mutasi_penduduk`
--
ALTER TABLE `mutasi_penduduk`
  ADD CONSTRAINT `mutasi_penduduk_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `penduduk` (`nik`) ON DELETE CASCADE;

--
-- Constraints for table `penerima_bansos`
--
ALTER TABLE `penerima_bansos`
  ADD CONSTRAINT `penerima_bansos_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program_bansos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permohonan_surat`
--
ALTER TABLE `permohonan_surat`
  ADD CONSTRAINT `permohonan_surat_ibfk_1` FOREIGN KEY (`nik_pemohon`) REFERENCES `penduduk` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
