-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2023 at 08:25 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scheduling_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tabel_courses`
--

CREATE TABLE `tabel_courses` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `sks` int(11) NOT NULL,
  `meetings_per_week` int(11) NOT NULL,
  `total_students` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_courses`
--

INSERT INTO `tabel_courses` (`id`, `code`, `name`, `teacher_id`, `sks`, `meetings_per_week`, `total_students`) VALUES
(1, 'MK004', 'Fisika', 4, 3, 2, 40),
(2, 'MK005', 'Sejarah', 5, 2, 1, 28),
(3, 'MK006', 'Geografi', 6, 2, 2, 32),
(4, 'MK007', 'Biologi Lanjutan', 7, 3, 2, 29),
(5, 'MK008', 'Kimia Dasar', 8, 2, 1, 34),
(6, 'MK009', 'Fisika Dasar', 9, 2, 2, 33),
(7, 'MK010', 'Matematika Lanjutan', 10, 3, 1, 31),
(8, 'MK011', 'Statistika', 11, 2, 1, 36),
(9, 'MK012', 'Bahasa Indonesia', 12, 2, 2, 37),
(10, 'MK013', 'Bahasa Inggris', 13, 3, 2, 38),
(11, 'MK014', 'Pendidikan Pancasila', 14, 2, 1, 27),
(12, 'MK015', 'Pendidikan Kewarganegaraan', 15, 2, 2, 39),
(13, 'MK016', 'Sejarah Indonesia', 16, 3, 1, 41),
(14, 'MK017', 'Geografi Indonesia', 17, 3, 2, 42),
(15, 'MK018', 'Teknologi Informasi', 18, 2, 2, 26),
(16, 'MK019', 'Dasar Pemrograman', 19, 3, 2, 43),
(17, 'MK020', 'Pemrograman Lanjutan', 20, 2, 1, 44),
(18, 'MK021', 'Pemrograman Web', 1, 2, 2, 30),
(19, 'MK022', 'Basis Data', 2, 3, 2, 25),
(20, 'MK023', 'Jaringan Komputer', 3, 2, 1, 35);

-- --------------------------------------------------------

--
-- Table structure for table `tabel_rooms`
--

CREATE TABLE `tabel_rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_rooms`
--

INSERT INTO `tabel_rooms` (`id`, `name`, `capacity`) VALUES
(1, 'R101', 35),
(2, 'R102', 45),
(3, 'R103', 25);

-- --------------------------------------------------------

--
-- Table structure for table `tabel_teachers`
--

CREATE TABLE `tabel_teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `availability` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_teachers`
--

INSERT INTO `tabel_teachers` (`id`, `name`, `availability`) VALUES
(1, 'Dr. Fajar', '{\"Monday\": [\"09:00-11:00\", \"14:00-16:00\"]}'),
(2, 'Dr. Dian', '{\"Tuesday\": [\"08:00-10:00\"], \"Thursday\": [\"13:00-15:00\"]}'),
(3, 'Dr. Rina', '{\"Wednesday\": [\"10:00-12:00\", \"15:00-17:00\"]}'),
(4, 'Dr. Reza', '{\"Monday\": [\"08:00-10:00\", \"13:00-15:00\"]}'),
(5, 'Dr. Lina', '{\"Friday\": [\"09:00-12:00\"]}'),
(6, 'Dr. Rais', '{\"Tuesday\": [\"10:00-12:00\", \"13:00-15:00\"]}'),
(7, 'Dr. Indra', '{\"Wednesday\": [\"09:00-12:00\"]}'),
(8, 'Dr. Fitri', '{\"Thursday\": [\"14:00-17:00\"]}'),
(9, 'Dr. Rudi', '{\"Monday\": [\"10:00-12:00\"]}'),
(10, 'Dr. Risma', '{\"Friday\": [\"13:00-15:00\", \"15:00-17:00\"]}'),
(11, 'Dr. Iwan', '{\"Wednesday\": [\"08:00-10:00\"]}'),
(12, 'Dr. Asep', '{\"Thursday\": [\"10:00-12:00\", \"13:00-15:00\"]}'),
(13, 'Dr. Tari', '{\"Friday\": [\"09:00-11:00\"]}'),
(14, 'Dr. Rita', '{\"Monday\": [\"13:00-15:00\"]}'),
(15, 'Dr. Erna', '{\"Tuesday\": [\"14:00-17:00\"]}'),
(16, 'Dr. Eka', '{\"Wednesday\": [\"13:00-15:00\"]}'),
(17, 'Dr. Ikhsan', '{\"Thursday\": [\"08:00-10:00\"]}'),
(18, 'Dr. Joko', '{\"Friday\": [\"10:00-12:00\"]}'),
(19, 'Dr. Sari', '{\"Monday\": [\"15:00-17:00\"]}'),
(20, 'Dr. Anto', '{\"Tuesday\": [\"08:00-10:00\", \"10:00-12:00\"]}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tabel_courses`
--
ALTER TABLE `tabel_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `tabel_rooms`
--
ALTER TABLE `tabel_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tabel_teachers`
--
ALTER TABLE `tabel_teachers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tabel_courses`
--
ALTER TABLE `tabel_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tabel_rooms`
--
ALTER TABLE `tabel_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tabel_teachers`
--
ALTER TABLE `tabel_teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tabel_courses`
--
ALTER TABLE `tabel_courses`
  ADD CONSTRAINT `tabel_courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `tabel_teachers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
