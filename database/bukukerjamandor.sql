-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2018 at 09:25 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

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
  `nama_aktivitas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `aktivitas`
--

INSERT INTO `aktivitas` (`kode_aktivitas`, `nama_aktivitas`) VALUES
('BAAA01', 'Semprot Lalang'),
('BAAB01', 'Wiping Lalang'),
('BACA01', 'Semprot Piringan'),
('BACC01', 'Semprot Pasar Rintis'),
('BAEA01', 'Semprot Gawangan'),
('BCAC05', 'Tabur Pupuk NPK 13'),
('BCAG05', 'Tabur Pupuk Borate'),
('BCAL05', 'Tabur Pupuk CuSO4'),
('BCBB01', 'Tabur Humic Acid Ostindo');

-- --------------------------------------------------------

--
-- Table structure for table `bkm`
--

CREATE TABLE `bkm` (
  `no_bkm` char(10) NOT NULL,
  `tgl_bkm` date NOT NULL,
  `no_rkh` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_aktivitas`
--

CREATE TABLE `bkm_aktivitas` (
  `no_bkm` char(10) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `kode_aktivitas` char(10) NOT NULL,
  `sektor_tanam` varchar(3) NOT NULL,
  `blok_tanam` varchar(3) NOT NULL,
  `foto_aktivitas` varbinary(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_material`
--

CREATE TABLE `bkm_material` (
  `no_bkm` char(10) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `no_material` int(2) NOT NULL,
  `kode_material` char(10) NOT NULL,
  `kuantitas` decimal(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bkm_pegawai`
--

CREATE TABLE `bkm_pegawai` (
  `no_bkm` char(10) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `no_pegawai` int(2) NOT NULL,
  `id_pegawai` char(10) NOT NULL,
  `hasil_kerja_riil` decimal(3,2) NOT NULL
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
('1000002', 'Glifosat 480 g/l - Prima Up', 'L'),
('1000021', 'Metil Metsulfuron 20% - Ally', 'kg'),
('22000464', 'Knapsack Sprayer INTER', 'pc'),
('2600002', 'Pupuk NPK Hikay (13/6/27/4 + 0.65 B)', 'kg'),
('2600012', 'Borate ex USA', 'kg'),
('2600017', 'Pupuk Humic Acid OSTINDO kemasan 40 kg', 'kg'),
('2600040', 'Copper Sulfate CuSO4', 'kg');

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
  `kode_mandoran` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nama_pegawai`, `panggilan_pegawai`, `jabatan`, `status`, `kode_mandoran`) VALUES
('10001', 'admin', 'admin', 'mandor', 'tetap', '10021'),
('10011', 'Pegawai 1', 'Pegawai 1', 'pekerja', 'tetap', '10022'),
('10012', 'Pegawai 2', 'Pegawai 2', 'pekerja', 'tetap', '10022'),
('10044', 'Mandor 1', 'Mandor 1', 'mandor', 'tetap', '10022');

-- --------------------------------------------------------

--
-- Table structure for table `rkh`
--

CREATE TABLE `rkh` (
  `no_rkh` char(10) NOT NULL,
  `tgl_kegiatan` date NOT NULL,
  `id_pegawai` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rkh`
--

INSERT INTO `rkh` (`no_rkh`, `tgl_kegiatan`, `id_pegawai`) VALUES
('26022018', '2018-03-04', '10044');

-- --------------------------------------------------------

--
-- Table structure for table `rkh_aktivitas`
--

CREATE TABLE `rkh_aktivitas` (
  `no_rkh` char(10) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `kode_aktivitas` char(10) NOT NULL,
  `sektor_tanam` varchar(3) NOT NULL,
  `blok_tanam` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rkh_aktivitas`
--

INSERT INTO `rkh_aktivitas` (`no_rkh`, `no_aktivitas`, `kode_aktivitas`, `sektor_tanam`, `blok_tanam`) VALUES
('26022018', 1, 'BAAA01', 'A01', '01'),
('26022018', 2, 'BCBB01', 'A02', '01');

-- --------------------------------------------------------

--
-- Table structure for table `rkh_material`
--

CREATE TABLE `rkh_material` (
  `no_rkh` char(10) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `kode_material` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rkh_material`
--

INSERT INTO `rkh_material` (`no_rkh`, `no_aktivitas`, `kode_material`) VALUES
('26022018', 1, '22000464'),
('26022018', 2, '2600017');

-- --------------------------------------------------------

--
-- Table structure for table `rkh_pegawai`
--

CREATE TABLE `rkh_pegawai` (
  `no_rkh` char(10) NOT NULL,
  `no_aktivitas` int(2) NOT NULL,
  `id_pegawai` char(10) NOT NULL,
  `hasil_kerja_standar` decimal(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rkh_pegawai`
--

INSERT INTO `rkh_pegawai` (`no_rkh`, `no_aktivitas`, `id_pegawai`, `hasil_kerja_standar`) VALUES
('26022018', 1, '10011', '0.05'),
('26022018', 1, '10012', '0.05'),
('26022018', 1, '10044', '1.00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_pegawai` char(10) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_pegawai`, `username`, `password`, `api_key`, `created_at`) VALUES
('10001', 'admin', '$2a$10$f33de8f4f0fbe79bfe08euNIIzWRR7bOajTz5ltMznh7ZJZQEnI8O', '6232707875d8368c143ba51caa0de7a7', '2018-02-21 12:32:39'),
('10044', 'mandor1', '$2a$10$2768e494ca252e076547cervAXUbYmVg3U3DJkbKLYIMK8Zxq.Spq', 'd1c3df8030f0b1c68891363868256720', '2018-02-28 16:12:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`kode_aktivitas`),
  ADD UNIQUE KEY `kode_aktivitas` (`kode_aktivitas`);

--
-- Indexes for table `bkm`
--
ALTER TABLE `bkm`
  ADD PRIMARY KEY (`no_bkm`),
  ADD UNIQUE KEY `no_bkm` (`no_bkm`),
  ADD KEY `no_rkh` (`no_rkh`);

--
-- Indexes for table `bkm_aktivitas`
--
ALTER TABLE `bkm_aktivitas`
  ADD PRIMARY KEY (`no_aktivitas`),
  ADD KEY `kode_aktivitas` (`kode_aktivitas`),
  ADD KEY `no_bkm` (`no_bkm`);

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
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`kode_material`),
  ADD KEY `kode_material` (`kode_material`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`);

--
-- Indexes for table `rkh`
--
ALTER TABLE `rkh`
  ADD PRIMARY KEY (`no_rkh`),
  ADD UNIQUE KEY `no_rkh` (`no_rkh`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `rkh_aktivitas`
--
ALTER TABLE `rkh_aktivitas`
  ADD PRIMARY KEY (`no_aktivitas`),
  ADD KEY `no_rkh` (`no_rkh`),
  ADD KEY `kode_aktivitas` (`kode_aktivitas`);

--
-- Indexes for table `rkh_material`
--
ALTER TABLE `rkh_material`
  ADD PRIMARY KEY (`kode_material`),
  ADD KEY `no_rkh` (`no_rkh`),
  ADD KEY `no_aktivitas` (`no_aktivitas`),
  ADD KEY `kode_material` (`kode_material`);

--
-- Indexes for table `rkh_pegawai`
--
ALTER TABLE `rkh_pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD KEY `no_rkh` (`no_rkh`),
  ADD KEY `no_aktivitas` (`no_aktivitas`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bkm`
--
ALTER TABLE `bkm`
  ADD CONSTRAINT `bkm_ibfk_2` FOREIGN KEY (`no_rkh`) REFERENCES `rkh` (`no_rkh`) ON UPDATE CASCADE;

--
-- Constraints for table `bkm_aktivitas`
--
ALTER TABLE `bkm_aktivitas`
  ADD CONSTRAINT `bkm_aktivitas_ibfk_2` FOREIGN KEY (`no_bkm`) REFERENCES `bkm` (`no_bkm`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_aktivitas_ibfk_3` FOREIGN KEY (`kode_aktivitas`) REFERENCES `aktivitas` (`kode_aktivitas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bkm_material`
--
ALTER TABLE `bkm_material`
  ADD CONSTRAINT `bkm_material_ibfk_1` FOREIGN KEY (`no_aktivitas`) REFERENCES `bkm_aktivitas` (`no_aktivitas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_material_ibfk_2` FOREIGN KEY (`kode_material`) REFERENCES `material` (`kode_material`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_material_ibfk_3` FOREIGN KEY (`no_bkm`) REFERENCES `bkm` (`no_bkm`) ON UPDATE CASCADE;

--
-- Constraints for table `bkm_pegawai`
--
ALTER TABLE `bkm_pegawai`
  ADD CONSTRAINT `bkm_pegawai_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_pegawai_ibfk_2` FOREIGN KEY (`no_aktivitas`) REFERENCES `bkm_aktivitas` (`no_aktivitas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bkm_pegawai_ibfk_3` FOREIGN KEY (`no_bkm`) REFERENCES `bkm` (`no_bkm`) ON UPDATE CASCADE;

--
-- Constraints for table `rkh`
--
ALTER TABLE `rkh`
  ADD CONSTRAINT `rkh_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`);

--
-- Constraints for table `rkh_aktivitas`
--
ALTER TABLE `rkh_aktivitas`
  ADD CONSTRAINT `rkh_aktivitas_ibfk_1` FOREIGN KEY (`no_rkh`) REFERENCES `rkh` (`no_rkh`),
  ADD CONSTRAINT `rkh_aktivitas_ibfk_2` FOREIGN KEY (`kode_aktivitas`) REFERENCES `aktivitas` (`kode_aktivitas`);

--
-- Constraints for table `rkh_material`
--
ALTER TABLE `rkh_material`
  ADD CONSTRAINT `rkh_material_ibfk_2` FOREIGN KEY (`kode_material`) REFERENCES `material` (`kode_material`),
  ADD CONSTRAINT `rkh_material_ibfk_3` FOREIGN KEY (`no_rkh`) REFERENCES `rkh` (`no_rkh`),
  ADD CONSTRAINT `rkh_material_ibfk_4` FOREIGN KEY (`no_aktivitas`) REFERENCES `rkh_aktivitas` (`no_aktivitas`);

--
-- Constraints for table `rkh_pegawai`
--
ALTER TABLE `rkh_pegawai`
  ADD CONSTRAINT `rkh_pegawai_ibfk_2` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`),
  ADD CONSTRAINT `rkh_pegawai_ibfk_3` FOREIGN KEY (`no_rkh`) REFERENCES `rkh` (`no_rkh`),
  ADD CONSTRAINT `rkh_pegawai_ibfk_4` FOREIGN KEY (`no_aktivitas`) REFERENCES `rkh_aktivitas` (`no_aktivitas`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
