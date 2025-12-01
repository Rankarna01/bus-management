-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 05:11 AM
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
-- Database: `db_bus_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `laporan_keberangkatan`
--

CREATE TABLE `laporan_keberangkatan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_berangkat` date NOT NULL,
  `waktu_berangkat` time NOT NULL,
  `no_polisi` varchar(20) NOT NULL,
  `nama_driver` varchar(100) NOT NULL,
  `jumlah_penumpang` int(11) NOT NULL,
  `tujuan` varchar(100) NOT NULL,
  `foto_dokumentasi` varchar(255) DEFAULT NULL,
  `status` enum('pending','terverifikasi') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_keberangkatan`
--

INSERT INTO `laporan_keberangkatan` (`id`, `user_id`, `tanggal_berangkat`, `waktu_berangkat`, `no_polisi`, `nama_driver`, `jumlah_penumpang`, `tujuan`, `foto_dokumentasi`, `status`, `created_at`) VALUES
(1, 2, '2025-11-25', '08:00:00', 'BK 1234 AB', 'Supriadi', 30, 'Medan - Aceh', NULL, 'terverifikasi', '2025-11-25 11:24:06'),
(2, 2, '2025-11-25', '10:00:00', 'BK 5678 CD', 'Joko', 25, 'Medan - Padang', NULL, 'terverifikasi', '2025-11-25 11:24:06'),
(3, 2, '2025-11-25', '13:54:00', 'B 9706 INALU', 'Rendy', 10, 'Terminal Amplas', '6925a7304a675_1764075312.png', 'terverifikasi', '2025-11-25 12:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mandor','direksi') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator Utama', 'admin', '$2y$10$AIx1FysQ516X8lZvs/sbjealLtj1ZxWmUWX7SJaLl.EswzB6c1yCS', 'admin', '2025-11-25 11:24:06'),
(2, 'Budi Mandor', 'mandor', '$2y$10$GnTMfJc4D/JyjfBoJMj8YeLG8.TPnYzUFkCMVvTqV31uGCu8TfA.m', 'mandor', '2025-11-25 11:24:06'),
(3, 'Bapak Direktur', 'direksi', '$2y$10$rr98511jPtWM0gDojRCciu6v82/3kfLA5pk3BZYucmHrCMIi0lGu2', 'direksi', '2025-11-25 11:24:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `laporan_keberangkatan`
--
ALTER TABLE `laporan_keberangkatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `laporan_keberangkatan`
--
ALTER TABLE `laporan_keberangkatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laporan_keberangkatan`
--
ALTER TABLE `laporan_keberangkatan`
  ADD CONSTRAINT `laporan_keberangkatan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
