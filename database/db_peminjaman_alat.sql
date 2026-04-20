-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 20, 2026 at 01:54 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_peminjaman_alat`
--

-- --------------------------------------------------------

--
-- Table structure for table `alats`
--

CREATE TABLE `alats` (
  `id` bigint UNSIGNED NOT NULL,
  `kategori_id` bigint UNSIGNED NOT NULL,
  `nama_alat` varchar(255) NOT NULL,
  `kondisi` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `stok_total` int NOT NULL DEFAULT '0',
  `stok_tersedia` int NOT NULL DEFAULT '0',
  `gambar` varchar(255) DEFAULT NULL,
  `harga_beli` decimal(12,2) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alats`
--

INSERT INTO `alats` (`id`, `kategori_id`, `nama_alat`, `kondisi`, `stok_total`, `stok_tersedia`, `gambar`, `harga_beli`, `keterangan`, `created_at`, `updated_at`) VALUES
(3, 1, 'Mouse', 'baik', 9, 5, '1770879268_mouse.jfif', '50000.00', NULL, '2026-02-12 06:54:31', '2026-04-20 12:30:07');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dendas`
--

CREATE TABLE `dendas` (
  `id` bigint UNSIGNED NOT NULL,
  `denda_per_hari` decimal(10,2) NOT NULL DEFAULT '5000.00',
  `denda_rusak_ringan` int NOT NULL DEFAULT '10',
  `denda_rusak_berat` int NOT NULL DEFAULT '50',
  `persentase_penggantian_hilang` int NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dendas`
--

INSERT INTO `dendas` (`id`, `denda_per_hari`, `denda_rusak_ringan`, `denda_rusak_berat`, `persentase_penggantian_hilang`, `created_at`, `updated_at`) VALUES
(1, '5000.00', 10, 50, 100, '2026-02-11 18:42:55', '2026-02-11 18:42:55');

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjamen`
--

CREATE TABLE `detail_peminjamen` (
  `id` bigint UNSIGNED NOT NULL,
  `peminjaman_id` bigint UNSIGNED NOT NULL,
  `alat_id` bigint UNSIGNED NOT NULL,
  `jumlah` int NOT NULL,
  `kondisi_pinjam` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_peminjamen`
--

INSERT INTO `detail_peminjamen` (`id`, `peminjaman_id`, `alat_id`, `jumlah`, `kondisi_pinjam`, `created_at`, `updated_at`) VALUES
(4, 4, 3, 1, 'baik', '2026-04-20 09:42:29', '2026-04-20 09:42:29'),
(5, 5, 3, 1, 'baik', '2026-04-20 11:06:12', '2026-04-20 11:06:12'),
(6, 6, 3, 1, 'baik', '2026-04-20 12:19:22', '2026-04-20 12:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pengembalians`
--

CREATE TABLE `detail_pengembalians` (
  `id` bigint UNSIGNED NOT NULL,
  `pengembalian_id` bigint UNSIGNED NOT NULL,
  `alat_id` bigint UNSIGNED NOT NULL,
  `jumlah_kembali` int NOT NULL,
  `kondisi_kembali` enum('baik','rusak_ringan','rusak_berat','hilang') NOT NULL,
  `keterangan_kondisi` text,
  `biaya_perbaikan` decimal(10,2) DEFAULT NULL,
  `biaya_penggantian` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pengembalians`
--

INSERT INTO `detail_pengembalians` (`id`, `pengembalian_id`, `alat_id`, `jumlah_kembali`, `kondisi_kembali`, `keterangan_kondisi`, `biaya_perbaikan`, `biaya_penggantian`, `created_at`, `updated_at`) VALUES
(4, 5, 3, 1, 'rusak_ringan', 'ngelag', '0.00', '0.00', '2026-04-20 12:03:41', '2026-04-20 12:03:41'),
(5, 6, 3, 1, 'rusak_berat', NULL, '0.00', '0.00', '2026-04-20 12:30:07', '2026-04-20 12:30:07');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategoris`
--

CREATE TABLE `kategoris` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategoris`
--

INSERT INTO `kategoris` (`id`, `nama_kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Elektronik', 'Alat seperti laptop', '2026-02-11 18:49:36', '2026-02-11 18:50:34'),
(2, 'Multimedia', NULL, '2026-02-11 18:52:07', '2026-02-11 18:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `activity` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `keterangan` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `activity`, `model`, `model_id`, `keterangan`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'Edit User', 'User', 3, 'Mengubah user: Angel | Perubahan: Nama: Peminjam → Angel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:47:17', '2026-02-11 18:47:17'),
(2, 1, 'Tambah User', 'User', 4, 'Menambahkan user baru: nabila (nabila@gmail.com) dengan role peminjam', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:48:26', '2026-02-11 18:48:26'),
(3, 1, 'Hapus User', 'User', 4, 'Menghapus user: nabila (nabila@gmail.com) dengan role peminjam', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:48:42', '2026-02-11 18:48:42'),
(4, 1, 'Tambah Kategori', 'Kategori', 1, 'Menambahkan kategori: Elektronik', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:49:36', '2026-02-11 18:49:36'),
(5, 1, 'Edit Kategori', 'Kategori', 1, 'Mengubah kategori: Elektronik | Perubahan: Deskripsi diubah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:50:34', '2026-02-11 18:50:34'),
(6, 1, 'Tambah Kategori', 'Kategori', 2, 'Menambahkan kategori: Multimedia', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:52:07', '2026-02-11 18:52:07'),
(7, 1, 'Tambah Kategori', 'Kategori', 3, 'Menambahkan kategori: Alat Kebersihan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:52:31', '2026-02-11 18:52:31'),
(8, 1, 'Hapus Kategori', 'Kategori', 3, 'Menghapus kategori: Alat Kebersihan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:52:38', '2026-02-11 18:52:38'),
(9, 1, 'Tambah Alat', 'Alat', 1, 'Menambahkan alat baru: Laptop dengan stok 50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:55:56', '2026-02-11 18:55:56'),
(10, 1, 'Edit Alat', 'Alat', 1, 'Mengubah data alat: Laptop → Laptop', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:56:19', '2026-02-11 18:56:19'),
(11, 1, 'Tambah Alat', 'Alat', 2, 'Menambahkan alat baru: Mouse dengan stok 10', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:57:49', '2026-02-11 18:57:49'),
(12, 1, 'Hapus Alat', 'Alat', 2, 'Menghapus alat: Mouse', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:57:58', '2026-02-11 18:57:58'),
(13, 1, 'Tambah Peminjaman', 'Peminjaman', 1, 'Admin menambahkan peminjaman untuk Angel | Alat: Laptop (1 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 19:08:39', '2026-02-11 19:08:39'),
(14, 1, 'Edit Peminjaman', 'Peminjaman', 1, 'Mengubah peminjaman #1 | Perubahan: Tanggal Pinjam: 2026-02-12 00:00:00 → 2026-02-12, Tanggal Kembali: 2026-02-13 00:00:00 → 2026-02-13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 19:12:23', '2026-02-11 19:12:23'),
(15, 2, 'Peminjaman Disetujui', 'Peminjaman', 1, 'Menyetujui peminjaman oleh Angel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 19:14:52', '2026-02-11 19:14:52'),
(16, 1, 'Tambah Pengembalian', 'Pengembalian', 1, 'Admin menambahkan pengembalian untuk Angel | Peminjaman #1 | Alat: Laptop (1 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 19:16:28', '2026-02-11 19:16:28'),
(17, 1, 'Tambah Alat', 'Alat', 3, 'Menambahkan alat baru: Mouse dengan stok 10', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 06:54:31', '2026-02-12 06:54:31'),
(18, 1, 'Tambah Peminjaman', 'Peminjaman', 2, 'Admin menambahkan peminjaman untuk Angel | Alat: Mouse (2 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 06:59:00', '2026-02-12 06:59:00'),
(19, 2, 'Peminjaman Disetujui', 'Peminjaman', 2, 'Menyetujui peminjaman oleh Angel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 07:00:38', '2026-02-12 07:00:38'),
(20, 1, 'Tambah Pengembalian', 'Pengembalian', 2, 'Admin menambahkan pengembalian untuk Angel | Peminjaman #2 | Alat: Mouse (2 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-17 12:58:24', '2026-04-17 12:58:24'),
(21, 3, 'Ajukan Peminjaman', 'Peminjaman', 3, 'Peminjam mengajukan peminjaman | Alat: Mouse (1)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 09:26:24', '2026-04-20 09:26:24'),
(22, 2, 'Peminjaman Disetujui', 'Peminjaman', 3, 'Menyetujui peminjaman oleh Angel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 09:27:39', '2026-04-20 09:27:39'),
(23, 3, 'Ajukan Peminjaman', 'Peminjaman', 4, 'Peminjam mengajukan peminjaman | Alat: Mouse (1)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 09:42:29', '2026-04-20 09:42:29'),
(24, 2, 'Peminjaman Disetujui', 'Peminjaman', 4, 'Menyetujui peminjaman oleh Angel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 09:42:53', '2026-04-20 09:42:53'),
(25, 1, 'Tambah Peminjaman', 'Peminjaman', 5, 'Admin menambahkan peminjaman untuk Angel | Alat: Mouse (1 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 11:06:12', '2026-04-20 11:06:12'),
(26, 2, 'Peminjaman Disetujui', 'Peminjaman', 5, 'Menyetujui peminjaman oleh Angel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 11:06:46', '2026-04-20 11:06:46'),
(27, 1, 'Tambah Peminjaman', 'Peminjaman', 6, 'Admin menambahkan peminjaman untuk Angel | Alat: Mouse (1 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 12:19:22', '2026-04-20 12:19:22'),
(28, 2, 'Peminjaman Disetujui', 'Peminjaman', 6, 'Menyetujui peminjaman oleh Angel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 12:19:49', '2026-04-20 12:19:49'),
(29, 1, 'Tambah Pengembalian', 'Pengembalian', 6, 'Menambahkan pengembalian untuk peminjam: Angel | Keterlambatan: 1 hari', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-04-20 12:30:07', '2026-04-20 12:30:07');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_07_172547_create_kategoris_table', 1),
(5, '2026_02_07_190144_create_alats_table', 1),
(6, '2026_02_08_132907_create_peminjamen_table', 1),
(7, '2026_02_08_141700_create_detail_peminjamen_table', 1),
(8, '2026_02_10_111506_create_pengembalians_table', 1),
(9, '2026_02_10_112228_create_detail_pengembalians_table', 1),
(10, '2026_02_10_113021_create_dendas_table', 1),
(11, '2026_02_11_090906_create_log_aktivitas_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjamen`
--

CREATE TABLE `peminjamen` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `petugas_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `keperluan` text NOT NULL,
  `status` enum('menunggu_persetujuan','disetujui','dipinjam','dikembalikan','ditolak','terlambat') NOT NULL DEFAULT 'menunggu_persetujuan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjamen`
--

INSERT INTO `peminjamen` (`id`, `user_id`, `petugas_id`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `keperluan`, `status`, `created_at`, `updated_at`) VALUES
(4, 3, 2, '2026-04-20', '2026-04-21', 'mudah untuk scrolling', 'dikembalikan', '2026-04-20 09:42:29', '2026-04-20 10:23:30'),
(5, 3, 2, '2026-04-20', '2026-04-21', 'yyyy', 'dikembalikan', '2026-04-20 11:06:12', '2026-04-20 12:16:57'),
(6, 3, 2, '2026-04-20', '2026-04-21', 'hujui', 'dikembalikan', '2026-04-20 12:19:22', '2026-04-20 12:31:23');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalians`
--

CREATE TABLE `pengembalians` (
  `id` bigint UNSIGNED NOT NULL,
  `peminjaman_id` bigint UNSIGNED NOT NULL,
  `tanggal_kembali_aktual` date NOT NULL,
  `petugas_id` bigint UNSIGNED DEFAULT NULL,
  `keterlambatan_hari` int NOT NULL DEFAULT '0',
  `denda_keterlambatan` decimal(10,2) NOT NULL DEFAULT '0.00',
  `denda_kerusakan` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_denda` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` enum('lunas','belum_lunas') NOT NULL DEFAULT 'belum_lunas',
  `status_pengembalian` enum('diajukan','dikonfirmasi') NOT NULL DEFAULT 'diajukan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengembalians`
--

INSERT INTO `pengembalians` (`id`, `peminjaman_id`, `tanggal_kembali_aktual`, `petugas_id`, `keterlambatan_hari`, `denda_keterlambatan`, `denda_kerusakan`, `total_denda`, `status_pembayaran`, `status_pengembalian`, `created_at`, `updated_at`) VALUES
(4, 4, '2026-04-22', 2, 1, '5000.00', '0.00', '5000.00', 'lunas', 'dikonfirmasi', '2026-04-20 09:43:43', '2026-04-20 10:46:21'),
(5, 5, '2026-04-22', 2, 1, '5000.00', '5000.00', '10000.00', 'lunas', 'dikonfirmasi', '2026-04-20 12:03:41', '2026-04-20 12:33:17'),
(6, 6, '2026-04-22', 2, 1, '5000.00', '25000.00', '30000.00', 'lunas', 'dikonfirmasi', '2026-04-20 12:30:07', '2026-04-20 12:32:50');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('WOm6A8QV83chq7NRNVTCgcpZXelDC5VEYo6rqCgp', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSGFXQ0hkSTBrYTM3T0dmaE9CZU5BVlJ5OFBGSE4zWnJCS1VjNVRUQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTY6Imh0dHA6Ly9wZW1pbmphbWFuLWFsYXQudGVzdC9wZXR1Z2FzL3BlbmdlbWJhbGlhbi1yaXdheWF0Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1776692571);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','peminjam') NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_logout` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `last_login`, `last_logout`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$aUqfG5sasfeYDZMo8B.4oeBJKLaCxLhLjGbfqYcU5wHDKKufNi/oS', 'admin', '2026-04-20 13:17:22', '2026-04-20 13:22:37', NULL, '2026-02-11 18:42:54', '2026-04-20 13:22:37'),
(2, 'Petugas', 'petugas@gmail.com', NULL, '$2y$12$gAd2.YGntt5yFPCOEsSeNeVFtd/LsZjqcQHsDLU/4KQ/FSpH4VGmu', 'petugas', '2026-04-20 13:26:09', '2026-04-20 13:12:54', NULL, '2026-02-11 18:42:55', '2026-04-20 13:26:09'),
(3, 'Angel', 'peminjam@gmail.com', NULL, '$2y$12$DPET7rCoQUL.eOLZHdMB4OwLHs0xgD2WwjzsDlBHEMMTnUahn/LvG', 'peminjam', '2026-04-20 13:22:43', '2026-04-20 13:26:01', NULL, '2026-02-11 18:42:55', '2026-04-20 13:26:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alats`
--
ALTER TABLE `alats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alats_kategori_id_foreign` (`kategori_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `dendas`
--
ALTER TABLE `dendas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detail_peminjamen`
--
ALTER TABLE `detail_peminjamen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_peminjamen_peminjaman_id_foreign` (`peminjaman_id`),
  ADD KEY `detail_peminjamen_alat_id_foreign` (`alat_id`);

--
-- Indexes for table `detail_pengembalians`
--
ALTER TABLE `detail_pengembalians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_pengembalians_pengembalian_id_foreign` (`pengembalian_id`),
  ADD KEY `detail_pengembalians_alat_id_foreign` (`alat_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategoris`
--
ALTER TABLE `kategoris`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_aktivitas_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peminjamen`
--
ALTER TABLE `peminjamen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjamen_user_id_foreign` (`user_id`),
  ADD KEY `peminjamen_petugas_id_foreign` (`petugas_id`);

--
-- Indexes for table `pengembalians`
--
ALTER TABLE `pengembalians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengembalians_peminjaman_id_foreign` (`peminjaman_id`),
  ADD KEY `pengembalians_petugas_id_foreign` (`petugas_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

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
-- AUTO_INCREMENT for table `alats`
--
ALTER TABLE `alats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dendas`
--
ALTER TABLE `dendas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_peminjamen`
--
ALTER TABLE `detail_peminjamen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_pengembalians`
--
ALTER TABLE `detail_pengembalians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategoris`
--
ALTER TABLE `kategoris`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `peminjamen`
--
ALTER TABLE `peminjamen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pengembalians`
--
ALTER TABLE `pengembalians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alats`
--
ALTER TABLE `alats`
  ADD CONSTRAINT `alats_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `detail_peminjamen`
--
ALTER TABLE `detail_peminjamen`
  ADD CONSTRAINT `detail_peminjamen_alat_id_foreign` FOREIGN KEY (`alat_id`) REFERENCES `alats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_peminjamen_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjamen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detail_pengembalians`
--
ALTER TABLE `detail_pengembalians`
  ADD CONSTRAINT `detail_pengembalians_alat_id_foreign` FOREIGN KEY (`alat_id`) REFERENCES `alats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pengembalians_pengembalian_id_foreign` FOREIGN KEY (`pengembalian_id`) REFERENCES `pengembalians` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peminjamen`
--
ALTER TABLE `peminjamen`
  ADD CONSTRAINT `peminjamen_petugas_id_foreign` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjamen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengembalians`
--
ALTER TABLE `pengembalians`
  ADD CONSTRAINT `pengembalians_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjamen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengembalians_petugas_id_foreign` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
