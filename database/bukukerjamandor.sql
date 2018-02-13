-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2018 at 04:19 AM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bukukerjamandor`
--

-- --------------------------------------------------------

--
-- Table structure for table `aktivitas`
--

CREATE TABLE `aktivitas` (
  `kode_aktivitas` char(10) NOT NULL,
  `nama_aktivitas` varchar(50) NOT NULL,
  `kode_alat` char(10) NOT NULL,
  `kode_material` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `kode_alat` char(10) NOT NULL,
  `nama_material` varchar(50) NOT NULL,
  `unit` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_aktivitas`
--

CREATE TABLE `bkm_aktivitas` (
  `no_bkm` char(10) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `kode_aktivitas` char(10) NOT NULL,
  `foto_aktivitas` varbinary(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_alat`
--

CREATE TABLE `bkm_alat` (
  `no_bkm` char(10) NOT NULL,
  `no_alat` int(2) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `kode_alat` char(10) NOT NULL,
  `kuantitas` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_harian`
--

CREATE TABLE `bkm_harian` (
  `no_bkm` char(10) NOT NULL,
  `tgl_bkm` date NOT NULL,
  `no_rkh` char(10) NOT NULL,
  `kode_mandoran` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_material`
--

CREATE TABLE `bkm_material` (
  `no_bkm` char(10) NOT NULL,
  `no_material` int(2) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `kode_material` char(10) NOT NULL,
  `kuantitas` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_pegawai`
--

CREATE TABLE `bkm_pegawai` (
  `no_bkm` char(10) NOT NULL,
  `no_pegawai` int(2) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `id_pegawai` char(10) NOT NULL,
  `hasil_kerja_standar` int(5) NOT NULL,
  `hasil_kerja_riil` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mandoran`
--

CREATE TABLE `mandoran` (
  `kode_mandoran` char(10) NOT NULL,
  `id_mandor` char(10) NOT NULL,
  `id_pegawai` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE `material` (
  `kode_material` char(10) NOT NULL,
  `nama_material` varchar(50) NOT NULL,
  `unit` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`kode_material`, `nama_material`, `unit`) VALUES
('1', 'Semen', 'kg'),
('2', 'Kayu', 'm'),
('3', 'Sepeda', 'cm');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` char(10) NOT NULL,
  `nama_pegawai` varchar(25) NOT NULL,
  `panggilan_pegawai` varchar(10) NOT NULL,
  `jabatan` enum('mandor','pekerja') NOT NULL,
  `status` enum('tetap','bebas') NOT NULL,
  `username` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nama_pegawai`, `panggilan_pegawai`, `jabatan`, `status`, `username`) VALUES
('1', 'haha', 'haha', 'mandor', 'tetap', 'akhiyar');

-- --------------------------------------------------------

--
-- Table structure for table `rkh`
--

CREATE TABLE `rkh` (
  `no_rkh` char(10) NOT NULL,
  `tgl_kegiatan` date NOT NULL,
  `sektor_tanam` varchar(3) NOT NULL,
  `blok_tanam` varchar(3) NOT NULL,
  `kode_mandoran` char(10) NOT NULL,
  `kode_aktivitas` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(10) NOT NULL,
  `email` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `api_key`, `created_at`) VALUES
(1, 'akhiyar', 'akiyar18@gmail.com', '$2a$10$2c7181d77100c2f2825f6uAA1QcYBmU/ZIT2VglFxbAnQJxdugNvC', '5d55ed73dda2730ec3e01a5f8c631966', '2018-02-11 08:42:44'),
(0, 'dedra', 'dedra@gmail.com', '$2a$10$43654877ef9d4c8c60a30uR35vF4ssRjvAzT/ZMVbAn1nfi5OgxIa', '0a47b5f292c2401f066883a5f8debbb8', '2018-02-13 03:07:11'),
(0, 'waladi', 'akhiyar.waladi@ui.ac.id', '$2a$10$fba9e426fc93709ed0804u73JUga9xMNURxBuv/.iswW/ou.49D3q', '9368215ff2fe5d197d261583606f5b50', '2018-02-11 08:52:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`kode_aktivitas`),
  ADD UNIQUE KEY `kode_aktivitas` (`kode_aktivitas`),
  ADD KEY `kode_material` (`kode_material`),
  ADD KEY `kode_alat` (`kode_alat`) USING BTREE;

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`kode_alat`),
  ADD UNIQUE KEY `kode_alat` (`kode_alat`);

--
-- Indexes for table `bkm_aktivitas`
--
ALTER TABLE `bkm_aktivitas`
  ADD PRIMARY KEY (`no_aktivitas`),
  ADD KEY `kode_aktivitas` (`kode_aktivitas`),
  ADD KEY `no_bkm` (`no_bkm`);

--
-- Indexes for table `bkm_alat`
--
ALTER TABLE `bkm_alat`
  ADD PRIMARY KEY (`no_alat`),
  ADD KEY `no_aktivitas` (`no_aktivitas`),
  ADD KEY `kode_alat` (`kode_alat`),
  ADD KEY `no_bkm` (`no_bkm`);

--
-- Indexes for table `bkm_harian`
--
ALTER TABLE `bkm_harian`
  ADD PRIMARY KEY (`no_bkm`),
  ADD UNIQUE KEY `no_bkm` (`no_bkm`),
  ADD KEY `no_rkh` (`no_rkh`),
  ADD KEY `kode_mandoran` (`kode_mandoran`);

--
-- Indexes for table `bkm_material`
--
ALTER TABLE `bkm_material`
  ADD PRIMARY KEY (`no_material`),
  ADD KEY `no_aktivitas` (`no_aktivitas`),
  ADD KEY `kode_material` (`kode_material`),
  ADD KEY `no_bkm` (`no_bkm`);

--
-- Indexes for table `bkm_pegawai`
--
ALTER TABLE `bkm_pegawai`
  ADD PRIMARY KEY (`no_pegawai`),
  ADD KEY `no_aktivitas` (`no_aktivitas`),
  ADD KEY `id_pegawai` (`id_pegawai`),
  ADD KEY `no_bkm` (`no_bkm`);

--
-- Indexes for table `mandoran`
--
ALTER TABLE `mandoran`
  ADD PRIMARY KEY (`kode_mandoran`),
  ADD UNIQUE KEY `kode_mandoran` (`kode_mandoran`),
  ADD KEY `id_pegawai` (`id_pegawai`),
  ADD KEY `id_mandor` (`id_mandor`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`kode_material`),
  ADD UNIQUE KEY `kode_material` (`kode_material`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `rkh`
--
ALTER TABLE `rkh`
  ADD PRIMARY KEY (`no_rkh`),
  ADD UNIQUE KEY `no_rkh` (`no_rkh`),
  ADD KEY `kode_mandoran` (`kode_mandoran`),
  ADD KEY `kode_aktivitas` (`kode_aktivitas`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD CONSTRAINT `aktivitas_ibfk_1` FOREIGN KEY (`kode_alat`) REFERENCES `alat` (`kode_alat`) ON UPDATE CASCADE,
  ADD CONSTRAINT `aktivitas_ibfk_2` FOREIGN KEY (`kode_material`) REFERENCES `material` (`kode_material`) ON UPDATE CASCADE;

--
-- Constraints for table `bkm_aktivitas`
--
ALTER TABLE `bkm_aktivitas`
  ADD CONSTRAINT `bkm_aktivitas_ibfk_1` FOREIGN KEY (`kode_aktivitas`) REFERENCES `aktivitas` (`kode_aktivitas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_aktivitas_ibfk_2` FOREIGN KEY (`no_bkm`) REFERENCES `bkm_harian` (`no_bkm`) ON UPDATE CASCADE;

--
-- Constraints for table `bkm_alat`
--
ALTER TABLE `bkm_alat`
  ADD CONSTRAINT `bkm_alat_ibfk_1` FOREIGN KEY (`no_aktivitas`) REFERENCES `bkm_aktivitas` (`no_aktivitas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_alat_ibfk_2` FOREIGN KEY (`kode_alat`) REFERENCES `alat` (`kode_alat`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_alat_ibfk_3` FOREIGN KEY (`no_bkm`) REFERENCES `bkm_harian` (`no_bkm`) ON UPDATE CASCADE;

--
-- Constraints for table `bkm_harian`
--
ALTER TABLE `bkm_harian`
  ADD CONSTRAINT `bkm_harian_ibfk_1` FOREIGN KEY (`kode_mandoran`) REFERENCES `mandoran` (`kode_mandoran`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_harian_ibfk_2` FOREIGN KEY (`no_rkh`) REFERENCES `rkh` (`no_rkh`) ON UPDATE CASCADE;

--
-- Constraints for table `bkm_material`
--
ALTER TABLE `bkm_material`
  ADD CONSTRAINT `bkm_material_ibfk_1` FOREIGN KEY (`no_aktivitas`) REFERENCES `bkm_aktivitas` (`no_aktivitas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_material_ibfk_2` FOREIGN KEY (`kode_material`) REFERENCES `material` (`kode_material`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_material_ibfk_3` FOREIGN KEY (`no_bkm`) REFERENCES `bkm_harian` (`no_bkm`) ON UPDATE CASCADE;

--
-- Constraints for table `bkm_pegawai`
--
ALTER TABLE `bkm_pegawai`
  ADD CONSTRAINT `bkm_pegawai_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_pegawai_ibfk_2` FOREIGN KEY (`no_aktivitas`) REFERENCES `bkm_aktivitas` (`no_aktivitas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_pegawai_ibfk_3` FOREIGN KEY (`no_bkm`) REFERENCES `bkm_harian` (`no_bkm`) ON UPDATE CASCADE;

--
-- Constraints for table `mandoran`
--
ALTER TABLE `mandoran`
  ADD CONSTRAINT `mandoran_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mandoran_ibfk_2` FOREIGN KEY (`id_mandor`) REFERENCES `pegawai` (`id_pegawai`) ON UPDATE CASCADE;

--
-- Constraints for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD CONSTRAINT `pegawai_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON UPDATE CASCADE;

--
-- Constraints for table `rkh`
--
ALTER TABLE `rkh`
  ADD CONSTRAINT `rkh_ibfk_1` FOREIGN KEY (`kode_mandoran`) REFERENCES `mandoran` (`kode_mandoran`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rkh_ibfk_2` FOREIGN KEY (`kode_aktivitas`) REFERENCES `aktivitas` (`kode_aktivitas`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
