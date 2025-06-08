-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jun 2025 pada 13.31
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
(5, 'Matematika'),
(6, 'Matematika'),
(7, 'Matematika'),
(8, 'Matematika'),
(9, 'Pemrograman');

-- --------------------------------------------------------

--
-- Struktur dari tabel `materi`
--

CREATE TABLE `materi` (
  `id` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `quiz_history`
--

CREATE TABLE `quiz_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_grup` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `max_score` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `quiz_history`
--

INSERT INTO `quiz_history` (`id`, `user_id`, `id_grup`, `score`, `max_score`, `percentage`, `completed_at`) VALUES
(2, 3, 9, 30, 30, 100.00, '2025-06-02 03:49:08'),
(3, 4, 9, 30, 30, 100.00, '2025-06-08 11:14:16');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id_soal`, `id_grup`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `jawaban_benar`, `kategori`, `tingkat_kesulitan`, `created_at`, `image`) VALUES
(10, 8, '1 + 1', '2', '3', '4', '5', 'A', NULL, 'Mudah', '2025-06-02 03:44:03', NULL),
(11, 9, 'Dibawah ini mana yang termasuk bahasa pemrograman?', 'inggris', 'indonesia', 'java', 'melayu', 'C', NULL, 'Sulit', '2025-06-02 03:46:53', NULL),
(12, 9, 'Serlok?', 'tak pakani', 'tak parani', 'tak ombeni', 'tak jajali', 'B', NULL, 'Mudah', '2025-06-02 03:46:53', NULL);

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
(2, 'admin', 'admin@gmail.com', '$2y$10$ANMyMAYXSg4MgConoRLZk.pjy8vDA36M8XJaWTWz1r./3iXfS3/Qe', 'admin', '2025-05-15', '0987654321', 'admin', '2025-05-15 12:16:17', 'admin'),
(3, 'khanan', 'khanan6@gmail.com', '$2y$10$PmxRiJGxoB1K4q.sNnL46OvmjBfMkFd4i0QhULFo843ui7qYMLF0C', 'blora', '2025-06-02', '08123373878', 'SMA Wewokdetok', '2025-06-02 03:48:43', 'user'),
(4, 'Azzam', 'azzam12@gmail.com', '$2y$10$GZZ7SbW1AeRaQx5DEg6O9OuFDQ6UHu4U96s05/bDsiRR.kpSu2Nqe', 'Karanganyar', '2025-06-08', '09876543211', 'SMK Presiden', '2025-06-08 11:13:35', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `grup_soal`
--
ALTER TABLE `grup_soal`
  ADD PRIMARY KEY (`id_grup`);

--
-- Indeks untuk tabel `materi`
--
ALTER TABLE `materi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `quiz_history`
--
ALTER TABLE `quiz_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id_grup` (`id_grup`);

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
  MODIFY `id_grup` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `materi`
--
ALTER TABLE `materi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `quiz_history`
--
ALTER TABLE `quiz_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
