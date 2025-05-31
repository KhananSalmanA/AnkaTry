-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Bulan Mei 2025 pada 06.14
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_utbk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `grup_soal`
--

CREATE TABLE `grup_soal` (
  `id_grup` int(11) NOT NULL,
  `judul_grup` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `grup_soal`
--

INSERT INTO `grup_soal` (`id_grup`, `judul_grup`) VALUES
(3, 'Soal pertama'),
(4, 'awokwokwok');

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal`
--

CREATE TABLE `soal` (
  `id_soal` int(11) NOT NULL,
  `id_grup` int(11) DEFAULT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` varchar(255) NOT NULL,
  `pilihan_b` varchar(255) NOT NULL,
  `pilihan_c` varchar(255) NOT NULL,
  `pilihan_d` varchar(255) NOT NULL,
  `jawaban_benar` enum('A','B','C','D') NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `tingkat_kesulitan` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id_soal`, `id_grup`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `jawaban_benar`, `kategori`, `tingkat_kesulitan`, `created_at`) VALUES
(5, 3, 'aku adalah nomer 1', 'a', 'b', 'c', 'd', 'A', NULL, 'Mudah', '2025-05-28 00:26:54'),
(6, 3, 'aku adalah nomer 2', 'a', 'b', 'c', 'd', 'B', NULL, 'Sedang', '2025-05-28 00:26:54'),
(7, 4, 'wiwokdwtok', 'not', 'onle', 'tok', 'detok', 'C', NULL, 'Sedang', '2025-05-28 00:33:37'),
(8, 4, 'aduhhh gantengnyaa', 'azril', 'claara', 'asuniii', 'icibooss', 'A', NULL, 'Sedang', '2025-05-28 00:33:37'),
(9, 4, 'hiduppp??', 'jokowi', 'blonde', 'bapak brayan', 'semua benar', 'D', NULL, 'Sulit', '2025-05-28 00:33:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `asal_sekolah` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `alamat`, `tanggal_lahir`, `no_telp`, `asal_sekolah`, `created_at`, `role`) VALUES
(1, 'khanan', 'khnnn@gmail.com', '$2y$10$q9agfRbFxBddM2.hDy5zQeSNcvjw0HwELbUa7SFCjRi8vWeM86TYW', 'ngemplak', '2025-05-15', '1234567890', 'sfaas', '2025-05-15 11:23:50', 'user'),
(2, 'admin', 'admin@gmail.com', '$2y$10$ANMyMAYXSg4MgConoRLZk.pjy8vDA36M8XJaWTWz1r./3iXfS3/Qe', 'admin', '2025-05-15', '0987654321', 'admin', '2025-05-15 12:16:17', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `grup_soal`
--
ALTER TABLE `grup_soal`
  ADD PRIMARY KEY (`id_grup`);

--
-- Indeks untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id_soal`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `grup_soal`
--
ALTER TABLE `grup_soal`
  MODIFY `id_grup` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Tambahan struktur tabel untuk riwayat kuis
CREATE TABLE IF NOT EXISTS `quiz_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `id_grup` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `max_score` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `id_grup` (`id_grup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pembuatan atau update tabel materi untuk mendukung gambar
CREATE TABLE IF NOT EXISTS `materi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deskripsi` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
