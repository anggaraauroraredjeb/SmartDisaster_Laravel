-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2026 at 08:17 AM
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
-- Database: `smartdisaster`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `aksi` varchar(200) NOT NULL,
  `detail` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sensor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tingkat_bahaya` enum('waspada','siaga','awas') NOT NULL,
  `pesan_peringatan` text NOT NULL,
  `waktu_kejadian` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`id`, `sensor_id`, `tingkat_bahaya`, `pesan_peringatan`, `waktu_kejadian`) VALUES
(1, 1, 'waspada', 'Terdeteksi asap tipis di Gedung TULT Lantai 5. Mohon periksa lokasi.', '2026-10-09 10:13:36'),
(2, 2, 'siaga', 'Getaran ringan terdeteksi di Gedung FIT. Sistem bersiap siaga.', '2026-10-10 10:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `assembly_points`
--

CREATE TABLE `assembly_points` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_titik` varchar(150) NOT NULL,
  `lokasi` varchar(200) NOT NULL,
  `kapasitas` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assembly_points`
--

INSERT INTO `assembly_points` (`id`, `nama_titik`, `lokasi`, `kapasitas`, `deskripsi`, `created_at`) VALUES
(1, 'Titik Kumpul A', 'Lapangan Basket Gedung TULT', 500, 'Area lapangan terbuka utama dekat gedung TULT', '2026-10-11 10:13:36'),
(2, 'Titik Kumpul B', 'Parkir Utama Gedung Rektorat', 800, 'Area parkir luas di depan rektorat', '2026-10-11 10:13:36'),
(3, 'Titik Kumpul C', 'Taman Kampus Barat', 300, 'Taman hijau terbuka di zona kampus barat', '2026-10-11 10:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `education_modules`
--

CREATE TABLE `education_modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(200) NOT NULL,
  `kategori` enum('gempa','kebakaran','banjir','umum') NOT NULL DEFAULT 'umum',
  `konten` longtext NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_modules`
--

INSERT INTO `education_modules` (`id`, `judul`, `kategori`, `konten`, `created_by`, `created_at`) VALUES
(1, 'Prosedur Evakuasi Kebakaran', 'kebakaran', 'Saat alarm kebakaran berbunyi, segera hentikan aktivitas. Jangan gunakan lift. Gunakan tangga darurat yang telah ditandai. Berkumpul di titik kumpul yang sudah ditentukan. Jangan kembali ke gedung sebelum ada izin dari petugas.', 1, '2026-10-11 10:13:36'),
(2, 'Tindakan Saat Gempa Bumi', 'gempa', 'Jika berada di dalam gedung: berlindung di bawah meja yang kuat, lindungi kepala. Jauhi jendela dan benda yang bisa jatuh. Setelah getaran berhenti, segera evakuasi melalui jalur aman. Berkumpul di titik kumpul terdekat.', 1, '2026-10-11 10:13:36'),
(3, 'Penanganan Banjir Kampus', 'banjir', 'Pantau level air secara berkala melalui aplikasi. Jika air mulai naik, segera pindahkan barang-barang berharga ke tempat yang lebih tinggi. Evakuasi ke lantai atas gedung atau titik kumpul yang aman dari banjir.', 1, '2026-10-11 10:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `evacuation_routes`
--

CREATE TABLE `evacuation_routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_rute` varchar(150) NOT NULL,
  `lokasi_asal` varchar(150) NOT NULL,
  `titik_kumpul` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jarak_meter` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evacuation_routes`
--

INSERT INTO `evacuation_routes` (`id`, `nama_rute`, `lokasi_asal`, `titik_kumpul`, `deskripsi`, `jarak_meter`, `created_at`) VALUES
(1, 'Rute TULT ke Lapangan', 'Gedung TULT', 'Titik Kumpul A', 'Keluar dari pintu utama, belok kiri, menuju lapangan basket.', 150, '2026-10-11 10:13:36'),
(2, 'Rute FIT ke Parkir', 'Gedung FIT', 'Titik Kumpul B', 'Turun tangga darurat, menuju pintu barat, parkir rektorat.', 280, '2026-10-11 10:13:36'),
(3, 'Rute FEB ke Taman', 'Gedung FEB', 'Titik Kumpul C', 'Keluar pintu timur, ikuti jalur hijau menuju taman kampus barat.', 200, '2026-10-11 10:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_06_23_000001_create_users_table', 1),
(2, '2026_06_23_000002_create_sensors_table', 1),
(3, '2026_06_23_000003_create_alerts_table', 1),
(4, '2026_06_23_000004_create_assembly_points_table', 1),
(5, '2026_06_23_000005_create_evacuation_routes_table', 1),
(6, '2026_06_23_000006_create_education_modules_table', 1),
(7, '2026_06_23_000007_create_reports_table', 1),
(8, '2026_06_23_000008_create_activity_logs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi_kejadian` text NOT NULL,
  `lokasi_kejadian` varchar(150) NOT NULL,
  `status_laporan` enum('menunggu','diproses','selesai','palsu') NOT NULL DEFAULT 'menunggu',
  `foto_bukti` varchar(255) DEFAULT NULL,
  `waktu_lapor` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `deskripsi_kejadian`, `lokasi_kejadian`, `status_laporan`, `foto_bukti`, `waktu_lapor`) VALUES
(1, 2, 'Ada percikan api dari panel listrik dekat kantin.', 'Kantin Gedung TULT', 'diproses', NULL, '2026-10-08 10:13:36'),
(2, 3, 'Banjir selutut yang membuat akses jalan tertutupi', 'depan Gedung FIT', 'selesai', NULL, '2026-10-09 10:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE `sensors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_sensor` varchar(50) NOT NULL,
  `jenis_sensor` enum('asap','api','gempa','banjir') NOT NULL,
  `lokasi_gedung` varchar(100) NOT NULL,
  `status` enum('aktif','rusak','maintenance') NOT NULL DEFAULT 'aktif',
  `last_checked` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`id`, `kode_sensor`, `jenis_sensor`, `lokasi_gedung`, `status`, `last_checked`) VALUES
(1, 'S-ASAP-TULT-01', 'asap', 'Gedung TULT Lantai 5', 'aktif', '2026-10-11 10:13:36'),
(2, 'S-GEMPA-FIT-01', 'gempa', 'Gedung FIT Lantai Dasar', 'aktif', '2026-10-11 10:13:36'),
(3, 'S-AIR-KU-01', 'banjir', 'Area Parkir Kolam', 'aktif', '2026-10-11 10:13:36'),
(4, 'S-API-FEB-01', 'api', 'Gedung FEB Lantai 2', 'maintenance', '2026-10-11 10:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT '',
  `role` enum('admin','mahasiswa','dosen','staf') NOT NULL DEFAULT 'mahasiswa',
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `no_telepon`, `role`, `password_hash`, `created_at`) VALUES
(1, 'Admin Pusat', 'admin@telkomuniversity.ac.id', '081234567890', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-10-11 10:13:36'),
(2, 'Budi Santoso', 'budi@student.telkomuniversity.ac.id', '089876543210', 'mahasiswa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-10-11 10:13:36'),
(3, 'aisy', 'aisy02@gamil.com', '0828378179', 'mahasiswa', '$2y$10$SEHX.Ar4d3woPZY7k166leMNtvpke..OokMcJJec/AMInmuIjPuFO', '2026-10-11 10:13:36'),
(4, 'Amanaisriz', 'admin2@telkomuniversity.ac.id', '0876237278317', 'admin', '$2y$10$R29Bnu5xiDar0vN6jQBuuO/Vx93fv4LpTWLtfKXZZ0i3ngG1BUF46', '2026-10-11 10:13:36'),
(5, 'acop', 'acop@gmail.com', '', 'mahasiswa', '$2y$12$b0.eiNzviZ30GvLnHtxEiee2wJQzyFUEJ/558sbG1DNFLrW1V43e2', '2026-10-11 10:43:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alerts_sensor_id_foreign` (`sensor_id`);

--
-- Indexes for table `assembly_points`
--
ALTER TABLE `assembly_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `education_modules`
--
ALTER TABLE `education_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `education_modules_created_by_foreign` (`created_by`);

--
-- Indexes for table `evacuation_routes`
--
ALTER TABLE `evacuation_routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_user_id_foreign` (`user_id`);

--
-- Indexes for table `sensors`
--
ALTER TABLE `sensors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sensors_kode_sensor_unique` (`kode_sensor`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assembly_points`
--
ALTER TABLE `assembly_points`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `education_modules`
--
ALTER TABLE `education_modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `evacuation_routes`
--
ALTER TABLE `evacuation_routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sensors`
--
ALTER TABLE `sensors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `alerts_sensor_id_foreign` FOREIGN KEY (`sensor_id`) REFERENCES `sensors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `education_modules`
--
ALTER TABLE `education_modules`
  ADD CONSTRAINT `education_modules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
