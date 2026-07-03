-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Jun 2026 pada 12.02
-- Versi server: 11.8.2-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apyrentnew`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggaran_proyek`
--

CREATE TABLE `anggaran_proyek` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `budget` decimal(15,2) NOT NULL,
  `realisasi` decimal(15,2) NOT NULL,
  `sisa` decimal(15,2) NOT NULL DEFAULT 0.00,
  `persen_terpakai` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `anggaran_proyek`
--

INSERT INTO `anggaran_proyek` (`id`, `proyek`, `kategori`, `budget`, `realisasi`, `sisa`, `persen_terpakai`, `created_at`, `updated_at`) VALUES
(1, 'Pembangunan Sistem Rental', 'Development', 15000000.00, 6000000.00, 9000000.00, 40.00, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(2, 'Server & Hosting', 'Infrastructure', 5000000.00, 2500000.00, 2500000.00, 50.00, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 'Pembelian GPS', 'Operasional', 10000000.00, 7500000.00, 2500000.00, 75.00, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(4, 'Promosi Rental', 'Marketing', 7000000.00, 3000000.00, 4000000.00, 42.86, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(5, 'Service Kendaraan', 'Maintenance', 12000000.00, 4500000.00, 7500000.00, 37.50, '2026-06-10 20:04:03', '2026-06-10 20:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `asuransi`
--

CREATE TABLE `asuransi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama_asuransi` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `nama_marketing` varchar(255) DEFAULT NULL,
  `kontak_marketing` varchar(255) DEFAULT NULL,
  `nama_bengkel` varchar(255) DEFAULT NULL,
  `kontak_bengkel` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `asuransi`
--

INSERT INTO `asuransi` (`id`, `user_id`, `nama_asuransi`, `alamat`, `nama_marketing`, `kontak_marketing`, `nama_bengkel`, `kontak_bengkel`, `created_at`, `updated_at`) VALUES
(1, 1, 'BCA Insurance', 'Jl. Sudirman No. 10 Jakarta', 'Andi Saputra', '081234567890', 'Bengkel Maju Motor', '082233445566', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(2, 1, 'Adira Insurance', 'Jl. Malioboro No. 20 Yogyakarta', 'Budi Hartono', '081298765432', 'Bengkel Jaya Abadi', '085566778899', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 1, 'ACA Insurance', 'Jl. Pemuda No. 12 Semarang', 'Siti Rahma', '087712345678', 'Bengkel Berkah Mobil', '081122334455', '2026-06-10 20:04:03', '2026-06-10 20:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `asuransi_history`
--

CREATE TABLE `asuransi_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asuransi_kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `asuransi_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_asuransi_id` bigint(20) UNSIGNED NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_berakhir` date NOT NULL,
  `durasi_bulan` int(11) NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status_kendaraan` varchar(255) NOT NULL DEFAULT 'aktif',
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `asuransi_history`
--

INSERT INTO `asuransi_history` (`id`, `asuransi_kendaraan_id`, `kendaraan_id`, `asuransi_id`, `jenis_asuransi_id`, `tgl_mulai`, `tgl_berakhir`, `durasi_bulan`, `biaya`, `bukti_bayar`, `status_kendaraan`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 2, 1, '2026-05-14', '2026-06-14', 1, 20000.00, 'asuransi/bukti-bayar/kr9QF5bfiBJYLMy88ULSJII2b0Vmet0Vl0Lu8tnc.pdf', 'aktif', '2026-06-19 23:44:02', '2026-06-19 23:44:02', '2026-06-19 23:44:02'),
(2, 5, 1, 2, 1, '2026-05-19', '2026-07-19', 2, 20000.00, 'asuransi/bukti_bayar/1781937842_avatar-pria-muda-berkacamata_1308-175763.jpg', 'aktif', '2026-06-19 23:44:27', '2026-06-19 23:44:27', '2026-06-19 23:44:27'),
(3, 4, 3, 1, 1, '2026-06-14', '2026-08-14', 2, 10000.00, 'asuransi/bukti_bayar/1781460399_download (2).jpg', 'aktif', '2026-06-20 00:26:59', '2026-06-20 00:26:59', '2026-06-20 00:26:59'),
(4, 2, 2, 2, 2, '2026-06-11', '2026-12-11', 6, 2500000.00, NULL, 'aktif', '2026-06-20 00:29:17', '2026-06-20 00:29:17', '2026-06-20 00:29:17'),
(5, 5, 1, 2, 1, '2026-05-19', '2026-11-19', 6, 20000.00, 'asuransi/bukti_bayar/1781937842_avatar-pria-muda-berkacamata_1308-175763.jpg', 'aktif', '2026-06-20 00:29:48', '2026-06-20 00:29:48', '2026-06-20 00:29:48'),
(6, 5, 1, 2, 1, '2026-06-20', '2026-12-20', 6, 20000.00, 'asuransi/bukti_bayar/1781940588_pngtree-creative-boys-avatar-png-image_12850267.png', 'aktif', '2026-06-20 00:30:23', '2026-06-20 00:30:23', '2026-06-20 00:30:23'),
(7, 5, 1, 2, 1, '2026-06-20', '2026-12-20', 6, 20000.00, 'asuransi/bukti_bayar/1781940623_download (2).jpg', 'aktif', '2026-06-20 00:32:36', '2026-06-20 00:32:36', '2026-06-20 00:32:36'),
(8, 2, 2, 2, 2, '2026-06-20', '2026-12-20', 6, 2500000.00, 'asuransi/bukti_bayar/1781941021_pngtree-creative-boys-avatar-png-image_12850267.png', 'aktif', '2026-06-20 00:37:01', '2026-06-20 00:37:01', '2026-06-20 00:37:01'),
(9, 4, 3, 1, 1, '2026-06-20', '2026-12-20', 6, 10000.00, 'asuransi/bukti_bayar/1781941149_download (2).jpg', 'aktif', '2026-06-20 00:39:09', '2026-06-20 00:39:09', '2026-06-20 00:39:09'),
(10, 5, 1, 2, 1, '2026-06-20', '2026-12-20', 6, 20000.00, 'asuransi/bukti_bayar/1781941257_download (2).jpg', 'aktif', '2026-06-20 00:40:57', '2026-06-20 00:40:57', '2026-06-20 00:40:57'),
(12, 2, 2, 2, 2, '2026-06-20', '2026-12-20', 6, 2500000.00, 'asuransi/bukti_bayar/1781941494_download (2).jpg', 'aktif', '2026-06-20 00:44:54', '2026-06-20 00:44:54', '2026-06-20 00:44:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `asuransi_kendaraan`
--

CREATE TABLE `asuransi_kendaraan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `asuransi_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_asuransi_id` bigint(20) UNSIGNED NOT NULL,
  `status_kendaraan` enum('aktif','expired') NOT NULL DEFAULT 'aktif',
  `tgl_mulai` date NOT NULL,
  `tgl_berakhir` date NOT NULL,
  `durasi_bulan` int(11) NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `asuransi_kendaraan`
--

INSERT INTO `asuransi_kendaraan` (`id`, `kendaraan_id`, `asuransi_id`, `jenis_asuransi_id`, `status_kendaraan`, `tgl_mulai`, `tgl_berakhir`, `durasi_bulan`, `biaya`, `bukti_bayar`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 2, 'aktif', '2026-06-20', '2026-12-20', 6, 2500000.00, 'asuransi/bukti_bayar/1781941494_download (2).jpg', '2026-06-10 20:04:03', '2026-06-20 00:44:54'),
(4, 3, 1, 1, 'aktif', '2026-06-20', '2026-12-20', 6, 10000.00, 'asuransi/bukti_bayar/1781940419_logo (2).png', '2026-06-13 10:09:54', '2026-06-20 00:26:59'),
(5, 1, 2, 1, 'aktif', '2026-06-20', '2026-12-20', 6, 20000.00, 'asuransi/bukti_bayar/1781940756_avatar-pria-muda-berkacamata_1308-175763.jpg', '2026-06-13 12:28:57', '2026-06-20 00:32:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `biaya_operasional_kendaraans`
--

CREATE TABLE `biaya_operasional_kendaraans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `biaya_tambahans`
--

CREATE TABLE `biaya_tambahans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `nama_tambahan` varchar(255) NOT NULL,
  `biaya` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bukubesars`
--

CREATE TABLE `bukubesars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_jurnal` varchar(255) DEFAULT NULL,
  `transaksi` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `debit` bigint(20) NOT NULL DEFAULT 0,
  `kredit` bigint(20) NOT NULL DEFAULT 0,
  `saldo` bigint(20) NOT NULL DEFAULT 0,
  `aktivitas` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bukubesars`
--

INSERT INTO `bukubesars` (`id`, `kode_jurnal`, `transaksi`, `kategori`, `tanggal`, `debit`, `kredit`, `saldo`, `aktivitas`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'JRNL-001', 'Pemasukan Rental Mobil', 'pemasukan', '2026-06-06', 1500000, 0, 1500000, 'rental', 'Pembayaran rental mobil dari customer A', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(2, 'JRNL-002', 'Pembelian Service Kendaraan', 'pengeluaran', '2026-06-08', 0, 500000, 1000000, 'service', 'Biaya servis berkala kendaraan', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, 'JRNL-003', 'Pajak Kendaraan', 'pengeluaran', '2026-06-10', 0, 200000, 800000, 'pajak', 'Pembayaran pajak kendaraan tahunan', '2026-06-10 20:04:04', '2026-06-10 20:04:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bupot`
--

CREATE TABLE `bupot` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_bukti` varchar(255) DEFAULT NULL,
  `tanggal_bukti` date DEFAULT NULL,
  `tipe` enum('PPh21','PPh22','PPh23','PPh26') DEFAULT NULL,
  `npwp_pemotong` varchar(255) DEFAULT NULL,
  `nama_pemotong` varchar(255) DEFAULT NULL,
  `npwp_dipotong` varchar(255) DEFAULT NULL,
  `nama_dipotong` varchar(255) DEFAULT NULL,
  `jumlah_bruto` decimal(20,2) DEFAULT NULL,
  `tarif_pajak` decimal(5,2) DEFAULT NULL,
  `jumlah_potong` decimal(20,2) DEFAULT NULL,
  `status` enum('Draft','Approve') NOT NULL DEFAULT 'Draft',
  `file_bupot` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bupot`
--

INSERT INTO `bupot` (`id`, `nomor_bukti`, `tanggal_bukti`, `tipe`, `npwp_pemotong`, `nama_pemotong`, `npwp_dipotong`, `nama_dipotong`, `jumlah_bruto`, `tarif_pajak`, `jumlah_potong`, `status`, `file_bupot`, `created_at`, `updated_at`) VALUES
(1, NULL, '2026-06-01', 'PPh21', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '09.876.543.2-109.000', 'Budi Santoso', 5000000.00, 0.05, 2500.00, 'Approve', 'bupot/1781455279_logo (2).png', '2026-06-10 20:04:04', '2026-06-14 09:41:19'),
(2, NULL, '2026-06-03', 'PPh23', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '08.765.432.1-000.000', 'CV Sinar Abadi', 3000000.00, 0.02, 600.00, 'Approve', 'bupot/1781455291_data-rental (8).pdf', '2026-06-10 20:04:04', '2026-06-14 09:41:31'),
(3, NULL, '2026-06-06', 'PPh26', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '07.654.321.0-999.000', 'UD Jaya Motor', 10000000.00, 0.10, 10000.00, 'Draft', 'bupot/1781455392_logo (2).png', '2026-06-10 20:04:04', '2026-06-14 09:43:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `denda_rentals`
--

CREATE TABLE `denda_rentals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rental_id` bigint(20) UNSIGNED NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `tanggal_denda` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `deposit_customers`
--

CREATE TABLE `deposit_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rental_id` bigint(20) UNSIGNED NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `potongan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('ditahan','dikembalikan','dipotong') NOT NULL DEFAULT 'ditahan',
  `tanggal_deposit` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `efakturs`
--

CREATE TABLE `efakturs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_faktur` varchar(255) DEFAULT NULL,
  `tanggal_faktur` date DEFAULT NULL,
  `tipe` enum('Keluaran','Masukan') DEFAULT NULL,
  `npwp_lawan` varchar(255) DEFAULT NULL,
  `nama_lawan` varchar(255) DEFAULT NULL,
  `dpp` decimal(20,2) DEFAULT NULL,
  `ppn` decimal(20,2) DEFAULT NULL,
  `ppnbm` decimal(20,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) DEFAULT 'Draft',
  `file_faktur` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `efakturs`
--

INSERT INTO `efakturs` (`id`, `nomor_faktur`, `tanggal_faktur`, `tipe`, `npwp_lawan`, `nama_lawan`, `dpp`, `ppn`, `ppnbm`, `status`, `file_faktur`, `created_at`, `updated_at`) VALUES
(1, '010.000-26.000001', '2026-06-01', 'Keluaran', '01.234.567.8-901.000', 'PT Rental Maju Jaya', 5000000.00, 550000.00, 0.00, 'Pending', 'efaktur/1781455656_logo (2).png', '2026-06-10 20:04:04', '2026-06-14 09:47:36'),
(2, '010.000-26.000002', '2026-06-06', 'Masukan', '09.876.543.2-109.000', 'PT Supplier Sparepart', 3000000.00, 330000.00, 0.00, 'draft', NULL, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, '010.000-26.000003', '2026-06-09', 'Keluaran', '07.111.222.3-444.000', 'CV Transport Jaya', 7500000.00, 825000.00, 0.00, 'Submit DJP', NULL, '2026-06-10 20:04:04', '2026-06-14 09:48:07'),
(4, '010.001-22.87654321', '2026-06-14', 'Keluaran', '01.234.567.8-901.000', 'PT Rental Maju Jaya', 10.00, 3.00, 10.00, 'Approve', 'efaktur/1781455671_data-rental (7).pdf', '2026-06-13 16:10:03', '2026-06-14 09:47:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gps`
--

CREATE TABLE `gps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama_gps` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `nama_marketing` varchar(255) DEFAULT NULL,
  `kontak_marketing` varchar(255) DEFAULT NULL,
  `nama_bengkel` varchar(255) DEFAULT NULL,
  `kontak_bengkel` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gps`
--

INSERT INTO `gps` (`id`, `user_id`, `nama_gps`, `alamat`, `nama_marketing`, `kontak_marketing`, `nama_bengkel`, `kontak_bengkel`, `created_at`, `updated_at`) VALUES
(1, 1, 'GPS Tracker Nusantara', 'Jl. Ahmad Yani No. 12 Wonosobod', 'Rizky Pratama', '081234567890', 'Bengkel GPS Wonosobo', '082233445566', '2026-06-10 20:04:03', '2026-06-13 07:06:42'),
(2, 1, 'GPS Satelit Indonesia', 'Jl. Dieng No. 45 Banjarnegara', 'Andi Saputra', '081298765432', 'Bengkel Satelit Banjarnegara', '082112223333', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 1, 'Global Tracker GPS', 'Jl. Soekarno Hatta No. 8 Magelang', 'Fajar Nugroho', '081377788899', 'Tracker Service Magelang', '083344556677', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(4, 1, 'Smart GPS Solution', 'Jl. Veteran No. 22 Temanggung', 'Dimas Setiawan', '081355566677', 'Smart GPS Garage', '082266778899', '2026-06-10 20:04:03', '2026-06-10 20:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gps_kendaraan`
--

CREATE TABLE `gps_kendaraan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `gps_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status_gps` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `tanggal_pasang` date NOT NULL,
  `tanggal_habis` date NOT NULL,
  `biaya_sewa` bigint(20) NOT NULL DEFAULT 0,
  `durasi_bulan` int(11) NOT NULL DEFAULT 0,
  `status_sewa` enum('aktif','habis') NOT NULL DEFAULT 'aktif',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gps_kendaraan`
--

INSERT INTO `gps_kendaraan` (`id`, `kendaraan_id`, `gps_id`, `type`, `status_gps`, `tanggal_pasang`, `tanggal_habis`, `biaya_sewa`, `durasi_bulan`, `status_sewa`, `bukti_bayar`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'GT06N2222', 'nonaktif', '2026-06-20', '2027-04-20', 150000, 10, 'aktif', 'gps/bukti_bayar/1781941986_6a3646e29eca7.jpg', '2026-06-10 20:04:03', '2026-06-20 00:53:06'),
(2, 2, 2, 'TK103', 'aktif', '2025-08-11', '2026-08-11', 120000, 12, 'aktif', 'gps/bukti_bayar/M6ZmJ1xAIYxsa8R6UWcgzJyuBJzJrMziD70jpgU2.pdf', '2026-06-10 20:04:03', '2026-06-20 00:52:08'),
(3, 3, 3, 'Concox JM01', 'nonaktif', '2026-06-20', '2027-06-20', 100000, 12, 'aktif', 'gps/bukti_bayar/1781942055_6a364727660e5.jpg', '2026-06-10 20:04:03', '2026-06-20 00:54:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gps_kendaraan_histories`
--

CREATE TABLE `gps_kendaraan_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gps_kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `gps_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `status_gps` varchar(255) NOT NULL,
  `tanggal_pasang` date NOT NULL,
  `tanggal_habis` date NOT NULL,
  `biaya_sewa` int(11) NOT NULL,
  `durasi_bulan` int(11) NOT NULL,
  `status_sewa` varchar(255) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gps_kendaraan_histories`
--

INSERT INTO `gps_kendaraan_histories` (`id`, `gps_kendaraan_id`, `kendaraan_id`, `gps_id`, `type`, `status_gps`, `tanggal_pasang`, `tanggal_habis`, `biaya_sewa`, `durasi_bulan`, `status_sewa`, `bukti_bayar`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 2, 'TK103', 'aktif', '2025-08-11', '2026-08-11', 120000, 12, 'aktif', 'gps/bukti_bayar/1781941942_6a3646b6aedb7.png', '2026-06-20 00:52:22', '2026-06-20 00:52:22', '2026-06-20 00:52:22'),
(3, 1, 1, 2, 'GT06N2222', 'nonaktif', '2026-06-20', '2027-04-20', 150000, 10, 'aktif', 'gps/bukti_bayar/1781941986_6a3646e29eca7.jpg', '2026-06-20 00:53:06', '2026-06-20 00:53:06', '2026-06-20 00:53:06'),
(4, 3, 3, 3, 'Concox JM01', 'nonaktif', '2025-06-11', '2026-06-09', 100000, 12, 'habis', 'gps/bukti_bayar/1781942055_6a364727660e5.jpg', '2026-06-20 00:54:15', '2026-06-20 00:54:15', '2026-06-20 00:54:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hutang_vendors`
--

CREATE TABLE `hutang_vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_vendor` varchar(255) NOT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `dibayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sisa` decimal(15,2) NOT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `status` enum('belum_lunas','cicilan','lunas') NOT NULL DEFAULT 'belum_lunas',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `hutang_vendors`
--

INSERT INTO `hutang_vendors` (`id`, `nama_vendor`, `kategori`, `nominal`, `dibayar`, `sisa`, `jatuh_tempo`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'PT Sinar Abadi', 'Sparepart', 5000000.00, 2000000.00, 3000000.00, '2026-06-21', 'belum_lunas', 'Pembelian sparepart mesin', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(2, 'CV Mitra Jaya', 'Service', 2500000.00, 2500000.00, 0.00, '2026-06-06', 'lunas', 'Service kendaraan fleet', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, 'PT Otomotif Nusantara', 'Aksesoris', 1200000.00, 500000.00, 700000.00, '2026-06-14', 'belum_lunas', 'Pembelian aksesoris mobil', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(4, 'UD Jaya Mandiri', 'Ban', 3000000.00, 1000000.00, 2000000.00, '2026-06-26', 'belum_lunas', 'Pembelian ban kendaraan', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(5, 'PT Diesel Prima', 'Mesin', 8000000.00, 8000000.00, 0.00, '2026-06-09', 'lunas', 'Perbaikan mesin besar', '2026-06-10 20:04:04', '2026-06-10 20:04:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis`
--

CREATE TABLE `jenis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama_jenis` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jenis`
--

INSERT INTO `jenis` (`id`, `user_id`, `nama_jenis`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mobil SUV', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(2, 1, 'Mobil MPV', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 1, 'Mobil Sedan', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(4, 1, 'Pickup', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(5, 1, 'Truck', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(6, 1, 'Bus Pariwisata', '2026-06-10 20:04:03', '2026-06-10 20:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_asuransi`
--

CREATE TABLE `jenis_asuransi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_jenis` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jenis_asuransi`
--

INSERT INTO `jenis_asuransi` (`id`, `nama_jenis`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'All Risk', 'Menanggung kerusakan ringan dan berat', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(2, 'TLO', 'Total Loss Only', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 'Comprehensive', 'Perlindungan menyeluruh kendaraan', '2026-06-10 20:04:03', '2026-06-10 20:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_id` bigint(20) UNSIGNED NOT NULL,
  `nopol` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nama_pemilik` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `merk` varchar(255) NOT NULL,
  `tahun_pembuatan` year(4) DEFAULT NULL,
  `tahun_perakitan` year(4) DEFAULT NULL,
  `isi_silinder` varchar(255) DEFAULT NULL,
  `warna` varchar(255) DEFAULT NULL,
  `no_rangka` varchar(255) DEFAULT NULL,
  `no_mesin` varchar(255) DEFAULT NULL,
  `no_bpkb` varchar(255) DEFAULT NULL,
  `warna_tnkb` varchar(255) DEFAULT NULL,
  `bahan_bakar` varchar(255) DEFAULT NULL,
  `kode_lokasi` varchar(255) DEFAULT NULL,
  `no_urut_pendaftaran` varchar(255) DEFAULT NULL,
  `harga_sewa_per_hari` bigint(20) NOT NULL DEFAULT 0,
  `harga_sewa_per_jam` bigint(20) NOT NULL DEFAULT 0,
  `batas_biaya` bigint(20) NOT NULL DEFAULT 0,
  `dokumen` varchar(255) DEFAULT NULL,
  `masa_berlaku` date DEFAULT NULL,
  `kilometer_sekarang` int(11) NOT NULL DEFAULT 0,
  `limit_km_service` int(11) NOT NULL DEFAULT 0,
  `limit_bulan_service` int(11) NOT NULL DEFAULT 0,
  `km_terakhir_service` int(11) NOT NULL DEFAULT 0,
  `tanggal_terakhir_service` date DEFAULT NULL,
  `status_service` enum('aman','service') NOT NULL DEFAULT 'aman',
  `status_kendaraan` enum('tersedia','disewa','service','bermasalah') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `user_id`, `jenis_id`, `nopol`, `foto`, `nama_pemilik`, `alamat`, `merk`, `tahun_pembuatan`, `tahun_perakitan`, `isi_silinder`, `warna`, `no_rangka`, `no_mesin`, `no_bpkb`, `warna_tnkb`, `bahan_bakar`, `kode_lokasi`, `no_urut_pendaftaran`, `harga_sewa_per_hari`, `harga_sewa_per_jam`, `batas_biaya`, `dokumen`, `masa_berlaku`, `kilometer_sekarang`, `limit_km_service`, `limit_bulan_service`, `km_terakhir_service`, `tanggal_terakhir_service`, `status_service`, `status_kendaraan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'AA 1234 ZX', NULL, 'Budi Santoso', 'Wonosobo', 'Toyota Avanza', '2021', '2021', '1500 CC', 'Hitam', 'MHKS12345678901', 'ENG123456', 'BPKB001122', 'Hitam', 'Pertalite', 'AA', '123456', 350000, 50000, 1000000, NULL, '2027-06-11', 45000, 5000, 6, 40000, '2026-04-11', 'aman', 'tersedia', '2026-06-10 20:04:03', '2026-06-13 12:54:31'),
(2, 1, 2, 'AA 5678 YY', 'kendaraan/foto/1781399355_ChatGPT Image 31 Mei 2026, 21.18.42.png', 'Joko Widodo', 'Magelang', 'Honda Brio', '2020', '2020', '1200 CC', 'Merah', 'MHKS987654321', 'ENG998877', 'BPKB9988', 'Hitam', 'Pertamax', 'AA', '654321', 300000, 45000, 900000, 'kendaraan/dokumen/1781397755_data-rental (9).pdf', NULL, 60000, 5000, 6, 55000, NULL, 'service', 'tersedia', '2026-06-10 20:04:03', '2026-06-14 09:32:11'),
(3, 1, 3, 'AA 9090 KK', NULL, 'Andi Saputra', 'Temanggung', 'Mitsubishi Xpander', '2022', '2022', '1500 CC', 'Putih', 'MTRX00112233', 'MESIN778899', 'BPKB778899', 'Hitam', 'Solar', 'AA', '998877', 450000, 70000, 1200000, NULL, NULL, 22000, 5000, 100000, 18000, NULL, 'aman', 'tersedia', '2026-06-10 20:04:03', '2026-06-10 21:35:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keuangans`
--

CREATE TABLE `keuangans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kategori` varchar(255) NOT NULL,
  `metode` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `pemasukan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pengeluaran` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `keuangans`
--

INSERT INTO `keuangans` (`id`, `tanggal`, `reference`, `user_id`, `kategori`, `metode`, `keterangan`, `pemasukan`, `pengeluaran`, `saldo`, `created_at`, `updated_at`) VALUES
(1, '2026-06-01', 'INV-001', 1, 'Pemasukan', 'Cash', 'Pembayaran rental mobil', 1500000.00, 0.00, 1500000.00, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(2, '2026-06-03', 'EXP-001', 1, 'Pengeluaran', 'Transfer', 'Service kendaraan', 0.00, 500000.00, 1000000.00, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, '2026-06-06', 'INV-002', 1, 'Pemasukan', 'Transfer', 'Rental harian', 2000000.00, 0.00, 3000000.00, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(4, '2026-06-08', 'EXP-002', 1, 'Pengeluaran', 'Cash', 'Beli sparepart', 0.00, 750000.00, 2250000.00, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(5, '2026-06-11', 'INV-003', 1, 'Pemasukan', 'Cash', 'DP rental kendaraan', 1000000.00, 0.00, 3250000.00, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(6, '2026-06-11', 'SRV-1781149447', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 10000.00, 990000.00, '2026-06-10 20:44:07', '2026-06-10 20:44:07'),
(7, '2026-06-11', 'SRV-1781150627', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 70000.00, 920000.00, '2026-06-10 21:03:47', '2026-06-10 21:03:47'),
(8, '2026-06-11', 'SRV-1781152645', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 1.00, 919999.00, '2026-06-10 21:37:25', '2026-06-10 21:37:25'),
(9, '2026-06-13', 'PAY-8-180905677', 1, 'Pemasukan', 'auto', 'Rental Toyota Avanza - AA 1234 ZX', 21000000.00, 0.00, 21919999.00, '2026-06-13 11:09:05', '2026-06-13 11:09:05'),
(10, '2026-06-14', 'SRV-1781381137', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 3000.00, 21916999.00, '2026-06-13 13:05:37', '2026-06-13 13:05:37'),
(11, '2026-06-14', 'SRV-1781381240', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 5000.00, 21911999.00, '2026-06-13 13:07:20', '2026-06-13 13:07:20'),
(12, '2026-06-14', 'SRV-1781454731', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 2.00, 21911997.00, '2026-06-14 09:32:11', '2026-06-14 09:32:11'),
(13, '2026-06-19', 'PJK-FOW5BE', 1, 'Pajak Kendaraan', 'cash', 'Perpanjangan pajak kendaraan - AA 9090 KK', 0.00, 10000.00, 21901997.00, '2026-06-19 08:18:15', '2026-06-19 08:18:15'),
(14, '2026-06-19', 'PJK-4LSGDS', 1, 'Pajak Kendaraan', 'cash', 'Perpanjangan pajak kendaraan - AA 5678 YY', 0.00, 2200000.00, 19701997.00, '2026-06-19 08:20:02', '2026-06-19 08:20:02'),
(15, '2026-06-19', 'PJK-AAO0YV', 1, 'Pajak Kendaraan', 'cash', 'Perpanjangan pajak kendaraan - AA 9090 KK', 0.00, 10000.00, 19691997.00, '2026-06-19 08:20:26', '2026-06-19 08:20:26'),
(20, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19681997.00, '2026-06-19 08:28:08', '2026-06-19 08:28:08'),
(21, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19671997.00, '2026-06-19 08:38:02', '2026-06-19 08:38:02'),
(22, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19661997.00, '2026-06-19 08:40:00', '2026-06-19 08:40:00'),
(23, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19651997.00, '2026-06-19 08:41:02', '2026-06-19 08:41:02'),
(24, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19641997.00, '2026-06-19 08:48:26', '2026-06-19 08:48:26'),
(25, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19631997.00, '2026-06-19 08:48:47', '2026-06-19 08:48:47'),
(26, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19621997.00, '2026-06-19 08:49:43', '2026-06-19 08:49:43'),
(27, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19611997.00, '2026-06-19 08:51:49', '2026-06-19 08:51:49'),
(28, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19601997.00, '2026-06-19 08:54:04', '2026-06-19 08:54:04'),
(29, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19591997.00, '2026-06-19 08:55:47', '2026-06-19 08:55:47'),
(30, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19581997.00, '2026-06-19 08:56:13', '2026-06-19 08:56:13'),
(31, '2026-06-19', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19571997.00, '2026-06-19 08:59:26', '2026-06-19 08:59:26'),
(32, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19561997.00, '2026-06-19 19:27:51', '2026-06-19 19:27:51'),
(33, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 19551997.00, '2026-06-19 19:28:34', '2026-06-19 19:28:34'),
(34, '2026-06-20', 'PAJAK-2', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: STNK - Segera lakukan pembayaran', 0.00, 2200000.00, 17351997.00, '2026-06-19 19:29:08', '2026-06-19 19:29:08'),
(35, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 17341997.00, '2026-06-19 19:38:56', '2026-06-19 19:38:56'),
(36, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 17331997.00, '2026-06-19 19:43:07', '2026-06-19 19:43:07'),
(37, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 17321997.00, '2026-06-19 19:49:52', '2026-06-19 19:49:52'),
(38, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 17311997.00, '2026-06-19 19:58:11', '2026-06-19 19:58:11'),
(39, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 17301997.00, '2026-06-19 20:10:15', '2026-06-19 20:10:15'),
(40, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222', 0.00, 10000.00, 17291997.00, '2026-06-19 20:14:17', '2026-06-19 20:14:17'),
(41, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222223', 0.00, 10000.00, 17281997.00, '2026-06-19 20:14:45', '2026-06-19 20:14:45'),
(42, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii222223', 0.00, 10000.00, 17271997.00, '2026-06-19 20:16:57', '2026-06-19 20:16:57'),
(43, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii2222234', 0.00, 10000.00, 17261997.00, '2026-06-19 20:18:56', '2026-06-19 20:18:56'),
(44, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii2222234', 0.00, 10000.00, 17251997.00, '2026-06-19 20:21:36', '2026-06-19 20:21:36'),
(45, '2026-06-20', 'PAJAK-7', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: Pajak toty22 - haii2222234', 0.00, 10000.00, 17241997.00, '2026-06-19 20:22:52', '2026-06-19 20:22:52'),
(46, '2026-06-20', 'GPS-1', 1, 'gps_kendaraan', 'cash', 'Perpanjangan GPS kendaraan: GT06N2222 - AA 1234 ZX', 0.00, 150000.00, 17091997.00, '2026-06-19 22:37:17', '2026-06-19 22:37:17'),
(47, '2026-06-20', 'Asuransi-2', 1, 'asuransi_kendaraan', '-', 'Pembayaran asuransi kendaraan:  - ', 0.00, 2500000.00, 14591997.00, '2026-06-20 00:44:54', '2026-06-20 00:44:54'),
(48, '2026-06-20', 'GPS-1', 1, 'gps_kendaraan', '-', 'Perpanjangan GPS kendaraan: GT06N2222 - AA 1234 ZX', 0.00, 150000.00, 14441997.00, '2026-06-20 00:53:06', '2026-06-20 00:53:06'),
(49, '2026-06-20', 'GPS-3', 1, 'gps_kendaraan', '-', 'Perpanjangan GPS kendaraan: Concox JM01 - AA 9090 KK', 0.00, 100000.00, 14341997.00, '2026-06-20 00:54:15', '2026-06-20 00:54:15'),
(50, '2026-06-20', 'PAJAK-5', 1, 'pajak_kendaraan', 'cash', 'Pembayaran pajak kendaraan: STNK - Perlu segera diperpanjang', 0.00, 1800000.00, 12541997.00, '2026-06-20 02:42:44', '2026-06-20 02:42:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kir`
--

CREATE TABLE `kir` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `no_uji` varchar(255) NOT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kir`
--

INSERT INTO `kir` (`id`, `kendaraan_id`, `no_uji`, `masa_berlaku`, `biaya`, `image`, `created_at`, `updated_at`) VALUES
(7, 1, '12020200', '2026-08-20', 100000.00, 'kir/dokumen/1781944155_pngtree-creative-boys-avatar-png-image_12850267.png', '2026-06-13 08:15:29', '2026-06-20 01:29:15'),
(9, 2, 'KIR-2026-001', '2026-08-20', 20000.00, 'kir/dokumen/1781944430_download (2).jpg', '2026-06-13 12:44:31', '2026-06-20 01:33:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kir_history`
--

CREATE TABLE `kir_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kir_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `no_uji` varchar(255) NOT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kir_history`
--

INSERT INTO `kir_history` (`id`, `kir_id`, `kendaraan_id`, `no_uji`, `masa_berlaku`, `biaya`, `image`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
(2, 7, 1, '12020200', '2026-06-20', 100000.00, 'kir/dokumen/1781944100_avatar-pria-muda-berkacamata_1308-175763.jpg', '2026-06-20 01:29:15', '2026-06-20 01:29:15', '2026-06-20 01:29:15'),
(3, 9, 2, 'KIR-2026-001', '2026-06-14', 20000.00, 'kir/dokumen/1781453546_logo (2).png', '2026-06-20 01:31:21', '2026-06-20 01:31:21', '2026-06-20 01:31:21'),
(4, 9, 2, 'KIR-2026-001', '2026-10-18', 20000.00, 'kir/dokumen/1781453546_logo (2).png', '2026-06-20 01:31:49', '2026-06-20 01:31:49', '2026-06-20 01:31:49'),
(5, 9, 2, 'KIR-2026-001', '2026-10-20', 20000.00, 'kir/dokumen/1781944430_download (2).jpg', '2026-06-20 01:33:50', '2026-06-20 01:33:50', '2026-06-20 01:33:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_keuangan`
--

CREATE TABLE `laporan_keuangan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `pendapatan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `beban` decimal(15,2) NOT NULL DEFAULT 0.00,
  `laba` decimal(15,2) NOT NULL DEFAULT 0.00,
  `periode` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `laporan_keuangan`
--

INSERT INTO `laporan_keuangan` (`id`, `nama_perusahaan`, `pendapatan`, `beban`, `laba`, `periode`, `created_at`, `updated_at`) VALUES
(1, 'APY Rental', 25000000.00, 12000000.00, 13000000.00, '2026-06', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(2, 'APY Rental', 30000000.00, 15000000.00, 15000000.00, '2026-05', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, 'APY Rental', 18000000.00, 9000000.00, 9000000.00, '2026-04', '2026-06-10 20:04:04', '2026-06-10 20:04:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `member`
--

CREATE TABLE `member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_member` varchar(255) NOT NULL,
  `kontak_member` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `member`
--

INSERT INTO `member` (`id`, `nama_member`, `kontak_member`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '081234567890', 'Wonosobo, Jawa Tengah', '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(2, 'Rina Permata', '082145678901', 'Magelang, Jawa Tengah', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, 'Agus Setiawan', '083156789012', 'Temanggung, Jawa Tengah', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(4, 'Dewi Lestari', '085267890123', 'Banjarnegara, Jawa Tengah', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(5, 'Fajar Hidayat', '087878901234', 'Purwokerto, Jawa Tengah', '2026-06-10 20:04:04', '2026-06-10 20:04:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `member_kendaraan`
--

CREATE TABLE `member_kendaraan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_sewa` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `biaya_sewa` bigint(20) NOT NULL DEFAULT 0,
  `status_sewa` enum('aktif','selesai') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_09_070346_create_jenis_table', 1),
(5, '2026_05_09_070800_create_kendaraans_table', 1),
(6, '2026_05_09_070808_create_members_table', 1),
(7, '2026_05_09_072803_create_member_kendaraans_table', 1),
(8, '2026_05_09_072812_create_services_table', 1),
(9, '2026_05_09_072818_create_service_histories_table', 1),
(10, '2026_05_09_072823_create_service_details_table', 1),
(11, '2026_05_09_072829_create_gps_table', 1),
(12, '2026_05_09_072835_create_gps_kendaraans_table', 1),
(13, '2026_05_09_072840_create_jenis_asuransis_table', 1),
(14, '2026_05_09_072841_create_asuransis_table', 1),
(15, '2026_05_09_072848_create_asuransi_kendaraans_table', 1),
(16, '2026_05_09_072916_create_kirs_table', 1),
(17, '2026_05_09_072921_create_suppliers_table', 1),
(18, '2026_05_10_122153_create_biaya_tambahans_table', 1),
(19, '2026_05_10_170500_create_rentals_table', 1),
(20, '2026_05_11_134521_create_rental_biaya_tambahan_table', 1),
(21, '2026_05_11_151258_add_batas_biaya_to_rentals_table', 1),
(22, '2026_05_11_160910_create_keuangans_table', 1),
(23, '2026_05_16_094830_create_hutang_vendors_table', 1),
(24, '2026_05_16_094933_create_deposit_customers_table', 1),
(25, '2026_05_16_095006_create_denda_rentals_table', 1),
(26, '2026_05_16_095050_create_biaya_operasional_kendaraans_table', 1),
(27, '2026_05_16_095111_create_pajak_kendaraans_table', 1),
(28, '2026_05_20_162153_create_anggaran_proyek_table', 1),
(29, '2026_05_20_163514_create_laporan_keuangan_table', 1),
(30, '2026_05_20_170654_create_efakturs_table', 1),
(31, '2026_05_20_172335_create_ebukots_table', 1),
(32, '2026_05_20_174514_create_rekonsiliasi_bank_table', 1),
(33, '2026_05_20_180538_create_virtual_accounts_table', 1),
(34, '2026_05_20_182735_create_bukubesars_table', 1),
(35, '2026_05_25_144239_add_bukti_to_pajak_kendaraans_table', 1),
(36, '2026_05_29_095213_add_sisa_limit_to_service_history_table', 1),
(37, '2026_06_06_152857_create_settings_table', 1),
(38, '2026_06_19_125311_create_pajak_histories_table', 2),
(39, '2026_06_20_033524_create_gps_histories_table', 3),
(40, '2026_06_20_063123_create_asuransi_history_table', 4),
(41, '2026_06_20_082104_create_kir_history_table', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pajak_histories`
--

CREATE TABLE `pajak_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pajak_kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_pajak` varchar(255) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `diperpanjang_pada` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pajak_histories`
--

INSERT INTO `pajak_histories` (`id`, `pajak_kendaraan_id`, `kendaraan_id`, `jenis_pajak`, `nominal`, `jatuh_tempo`, `tanggal_bayar`, `status`, `keterangan`, `bukti`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
(38, 7, 3, 'Pajak toty22', 10000.00, '2026-10-29', '2026-06-20', 'sudah_bayar', 'haii2222234', 'pajak/bukti/1781925772_6a36078c52b68.png', '2026-06-19 20:22:52', '2026-06-19 20:22:52', '2026-06-19 20:22:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pajak_kendaraans`
--

CREATE TABLE `pajak_kendaraans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_pajak` varchar(255) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `status` enum('belum_bayar','sudah_bayar') NOT NULL DEFAULT 'belum_bayar',
  `keterangan` text DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pajak_kendaraans`
--

INSERT INTO `pajak_kendaraans` (`id`, `kendaraan_id`, `jenis_pajak`, `nominal`, `jatuh_tempo`, `tanggal_bayar`, `status`, `keterangan`, `bukti`, `created_at`, `updated_at`) VALUES
(2, 2, 'STNK', 2200000.00, '2026-07-30', '2026-06-20', 'sudah_bayar', 'Segera lakukan pembayaran', 'pajak/bukti/1781922548_6a35faf48e68f.png', '2026-06-10 20:04:03', '2026-06-19 19:29:08'),
(5, 1, 'STNK', 1800000.00, '2026-07-15', '2026-06-20', 'sudah_bayar', 'Perlu segera diperpanjang', 'pajak/bukti/1781948564_6a366094c240d.jpg', '2026-06-10 20:04:03', '2026-06-20 02:42:44'),
(7, 3, 'Pajak toty22', 10000.00, '2026-10-29', '2026-06-20', 'sudah_bayar', 'haii2222234', 'pajak/bukti/1781925772_6a36078c52b68.png', '2026-06-13 12:20:12', '2026-06-19 20:22:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekonsiliasi_bank`
--

CREATE TABLE `rekonsiliasi_bank` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'IDR',
  `status_rekonsiliasi` enum('matched','unmatched','pending') NOT NULL DEFAULT 'pending',
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rekonsiliasi_bank`
--

INSERT INTO `rekonsiliasi_bank` (`id`, `tanggal`, `deskripsi`, `reference_no`, `amount`, `currency`, `status_rekonsiliasi`, `invoice_id`, `created_at`, `updated_at`) VALUES
(1, '2026-05-30', 'Pembayaran rental masuk', 'BANK-INV-001', 1500000.00, 'IDR', 'matched', NULL, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(2, '2026-06-02', 'Transfer service kendaraan', 'BANK-INV-002', 500000.00, 'IDR', 'matched', NULL, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, '2026-06-05', 'Pembayaran deposit rental', 'BANK-INV-003', 2000000.00, 'IDR', 'pending', NULL, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(4, '2026-06-08', 'Pembayaran sparepart', 'BANK-INV-004', 750000.00, 'IDR', 'matched', NULL, '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(5, '2026-06-11', 'Pemasukan rental harian', 'BANK-INV-005', 1200000.00, 'IDR', 'pending', NULL, '2026-06-10 20:04:04', '2026-06-10 20:04:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rentals`
--

CREATE TABLE `rentals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `durasi_jam` int(11) DEFAULT NULL,
  `durasi_hari` int(11) DEFAULT NULL,
  `durasi_bulan` int(11) DEFAULT NULL,
  `biaya_dasar` bigint(20) NOT NULL DEFAULT 0,
  `biaya_tambahan_total` bigint(20) NOT NULL DEFAULT 0,
  `total_biaya` bigint(20) NOT NULL DEFAULT 0,
  `metode_pembayaran` enum('tunai','transfer') NOT NULL DEFAULT 'transfer',
  `jenis_pembayaran` enum('lunas','dp') NOT NULL DEFAULT 'lunas',
  `nominal_dp` bigint(20) DEFAULT NULL,
  `bukti_lunas` varchar(255) DEFAULT NULL,
  `bukti_dp` varchar(255) DEFAULT NULL,
  `bukti_pelunasan` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('belum_bayar','dp','lunas') NOT NULL DEFAULT 'belum_bayar',
  `status` enum('pending','booking','aktif','selesai','batal') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pakai_batas_biaya` tinyint(1) NOT NULL DEFAULT 0,
  `batas_biaya` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rentals`
--

INSERT INTO `rentals` (`id`, `user_id`, `kendaraan_id`, `member_id`, `tanggal_mulai`, `tanggal_selesai`, `durasi_jam`, `durasi_hari`, `durasi_bulan`, `biaya_dasar`, `biaya_tambahan_total`, `total_biaya`, `metode_pembayaran`, `jenis_pembayaran`, `nominal_dp`, `bukti_lunas`, `bukti_dp`, `bukti_pelunasan`, `status_pembayaran`, `status`, `created_at`, `updated_at`, `pakai_batas_biaya`, `batas_biaya`) VALUES
(8, 1, 1, 5, '2026-07-01 12:00:00', '2026-09-01 12:00:00', NULL, NULL, 2, 21000000, 0, 21000000, 'tunai', 'dp', 10000000, NULL, '1781374106_dp_logo (1).png', NULL, 'lunas', 'selesai', '2026-06-13 11:08:26', '2026-06-13 11:09:05', 0, NULL),
(12, 1, 1, 5, '2026-06-15 12:00:00', '2026-06-25 12:00:00', NULL, 10, NULL, 3500000, 0, 3500000, 'tunai', 'lunas', 0, 'uploads/pembayaran/1781458576_lunas_pngtree-creative-boys-avatar-png-image_12850267.png', NULL, NULL, 'lunas', 'booking', '2026-06-14 10:20:36', '2026-06-14 10:36:16', 0, NULL),
(13, 1, 2, 5, '2026-06-15 12:00:00', '2026-06-27 12:00:00', NULL, 12, NULL, 3600000, 0, 3600000, 'tunai', 'lunas', 0, 'uploads/pembayaran/1781457955_lunas_download (2).jpg', NULL, NULL, 'belum_bayar', 'booking', '2026-06-14 10:25:55', '2026-06-14 10:25:55', 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rental_biaya_tambahan`
--

CREATE TABLE `rental_biaya_tambahan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rental_id` bigint(20) UNSIGNED NOT NULL,
  `biaya_tambahan_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `subtotal` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service`
--

CREATE TABLE `service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama_service` varchar(255) NOT NULL,
  `biaya_default` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service`
--

INSERT INTO `service` (`id`, `user_id`, `nama_service`, `biaya_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ganti Oli Mesin', 350000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(2, 1, 'Tune Up', 500000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 1, 'Spooring Balancing', 250000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(4, 1, 'Ganti Kampas Rem', 450000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(5, 1, 'Service AC', 600000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(6, 1, 'Ganti Ban', 900000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(7, 1, 'Overhaul Mesin', 4500000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(8, 1, 'Cuci Mobil Premium', 75000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(9, 1, 'Ganti Aki', 1200000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(10, 1, 'Perbaikan Suspensi', 1800000, '2026-06-10 20:04:03', '2026-06-10 20:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_detail`
--

CREATE TABLE `service_detail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_service` date NOT NULL,
  `kilometer` int(11) NOT NULL DEFAULT 0,
  `status` enum('Layak','Tidak Layak') NOT NULL DEFAULT 'Layak',
  `biaya` bigint(20) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service_detail`
--

INSERT INTO `service_detail` (`id`, `kendaraan_id`, `tanggal_service`, `kilometer`, `status`, `biaya`, `keterangan`, `bukti`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-01-10', 25000, 'Layak', 350000, 'Ganti oli dan filter oli', 'service-detail/8HqswAa5XSYA83byjBnbOtsrpO9PAGEI7wzvtwGl.pdf', '2026-06-10 20:04:03', '2026-06-13 12:54:31'),
(2, 1, '2026-02-15', 28000, 'Layak', 500000, 'Ganti kampas rem depan', NULL, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 2, '2026-03-05', 42000, 'Layak', 600000, 'Isi freon dan service AC', NULL, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(4, 3, '2026-03-20', 55000, 'Tidak Layak', 1800000, 'Ganti 2 ban depan', NULL, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(5, 2, '2026-06-14', 2000, 'Layak', 100000, 'oli', 'service-detail/1781454400_avatar-pria-muda-berkacamata_1308-175763.jpg', '2026-06-13 12:50:04', '2026-06-14 09:26:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_history`
--

CREATE TABLE `service_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `keluhan` text DEFAULT NULL,
  `kilometer` int(11) NOT NULL DEFAULT 0,
  `total_biaya` bigint(20) NOT NULL DEFAULT 0,
  `status` enum('proses','selesai') NOT NULL DEFAULT 'proses',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `maks_bulanan` bigint(20) NOT NULL DEFAULT 0,
  `biaya_tahunan` bigint(20) NOT NULL DEFAULT 0,
  `status_pengeluaran` enum('stabil','overservice') NOT NULL DEFAULT 'stabil',
  `tanggal_service` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sisa_limit` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service_history`
--

INSERT INTO `service_history` (`id`, `kendaraan_id`, `keluhan`, `kilometer`, `total_biaya`, `status`, `bukti_pembayaran`, `maks_bulanan`, `biaya_tahunan`, `status_pengeluaran`, `tanggal_service`, `created_at`, `updated_at`, `sisa_limit`) VALUES
(1, 1, 'Oli mesin sudah hitam dan rem berbunyi', 45000, 850000, 'selesai', NULL, 0, 0, 'stabil', '2026-05-22', '2026-06-10 20:04:03', '2026-06-10 20:04:03', NULL),
(3, 3, 'Ban depan aus', 70000, 1800000, 'selesai', NULL, 0, 0, 'stabil', '2026-05-12', '2026-06-10 20:04:03', '2026-06-10 20:04:03', NULL),
(5, 2, 'Ganti aki', 55000, 1200000, 'selesai', NULL, 0, 0, 'stabil', '2026-05-27', '2026-06-10 20:04:03', '2026-06-10 20:04:03', NULL),
(7, 3, 'oli', 120, 90000, 'selesai', NULL, 100000, 1890000, 'stabil', '2026-06-11', '2026-06-10 21:03:47', '2026-06-10 21:35:20', 10000),
(8, 2, 'Ban Bocor', 10, 1, 'proses', 'bukti_pembayaran/1781454575_Turnamen Bola Voli (1).png', 6, 1200001, 'stabil', '2026-06-11', '2026-06-10 21:37:25', '2026-06-14 09:29:35', 5),
(9, 3, '2', 10, 3000, 'selesai', 'bukti_pembayaran/YuYFmM4AHxQraBa5DelclGhfcnWN61TR4b1tyuOh.pdf', 100000, 1893000, 'stabil', '2026-06-14', '2026-06-13 13:05:37', '2026-06-13 13:05:37', 7000),
(10, 3, '2000', 5, 5000, 'selesai', 'bukti_pembayaran/9rnEAFRuptWFTs5VoDK200Ur4QGgvRvo0AXMbGw0.pdf', 100000, 1898000, 'stabil', '2026-06-14', '2026-06-13 13:07:20', '2026-06-13 13:07:20', 2000),
(11, 2, '2222', 123, 2, 'selesai', 'bukti_pembayaran/1781454731_data-rental (9).pdf', 6, 1200003, 'stabil', '2026-06-14', '2026-06-14 09:32:11', '2026-06-14 09:32:11', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('xmrZAW9hOgpqNZtf1xzDPRK1qUkfBtbMiSCHcbAJ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNkcyeXlLb2M2MjZET2laN3loYkRUdERjemszWE9RN0FGZHk0VnJrUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2tldWFuZ2FuIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9ncHMta2VuZGFyYWFuLWhpc3RvcnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1781949699);

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `nama_bank` varchar(255) DEFAULT NULL,
  `nomor_rekening` varchar(255) DEFAULT NULL,
  `atas_nama_rekening` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `nama_perusahaan`, `alamat`, `telepon`, `email`, `website`, `logo`, `nama_bank`, `nomor_rekening`, `atas_nama_rekening`, `kode_pos`, `created_at`, `updated_at`) VALUES
(1, 'APY RENT A CAR', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-12345678', 'info@rentalkendaraan.co.id', 'https://rentalkendaraan.co.id', 'uploads/setting/1781459798_logo_icon.png', 'Bank BCA', '1234567890', 'PT Rental Kendaraan Indonesia', '10110', '2026-06-10 20:04:04', '2026-06-14 10:56:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama_supplier` varchar(255) NOT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jumlah_barang` bigint(20) NOT NULL DEFAULT 0,
  `harga_barang` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`id`, `user_id`, `nama_supplier`, `no_telp`, `nama_barang`, `jumlah_barang`, `harga_barang`, `created_at`, `updated_at`) VALUES
(1, 1, 'CV Suku Cadang Motor', '081234567890', 'Oli Mesin', 50, 75000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(2, 1, 'PT Ban Indonesia', '082233445566', 'Ban Mobil', 20, 850000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(3, 1, 'Toko Sparepart Jaya', '081377788899', 'Aki Mobil', 15, 1200000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(4, 1, 'CV Audio Mobil', '081299988877', 'GPS Tracker', 10, 450000, '2026-06-10 20:04:03', '2026-06-10 20:04:03'),
(5, 1, 'PT Diesel Utama', '082122334455', 'Filter Solar', 40, 95000, '2026-06-10 20:04:03', '2026-06-10 20:04:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('superadmin','keuangan','produksi') NOT NULL DEFAULT 'produksi',
  `status` enum('aktif','blokir') NOT NULL DEFAULT 'aktif',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `no_telp`, `foto`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test Use2', 'testuse2', 'test@example.com', '$2y$12$bUj4AGEJJA0WSy0tc20nUOCOsI39nSEeNmVisGVt/b2H1l3PTUlI6', '08123456789', 'uploads/user/1781872598_6a3537d609428.jpg', 'superadmin', 'aktif', NULL, '2026-06-10 20:04:03', '2026-06-19 05:36:38'),
(2, 'Ega Apriliansyah', 'Ega', 'keuangan@gmail.com', '$2y$12$oMPeLLO1mgHJrSocdjyEyuLIco737IZ/RRGiVQYDVGYTw.iYykPPy', '089976541236', 'user/Aq4iSRI8BuNWuuxgHIV7a1JAijFoa34MpWX6fc0B.jpg', 'keuangan', 'aktif', NULL, '2026-06-10 23:32:55', '2026-06-11 00:22:47'),
(3, 'Ilham Ainurofiq', 'Ilham', 'produksi@gmail.com', '$2y$12$I.lVaUcPFWkRioc6KM7dduScWdLqFn5MLQN/OVaVvsL0IXsn7G4Gm', '08972738316', 'user/TvtsvnGQ9shsZ0akdwdR1uOK8XeXXIkmOppfOJwL.jpg', 'produksi', 'aktif', NULL, '2026-06-10 23:33:39', '2026-06-11 00:21:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `virtual_accounts`
--

CREATE TABLE `virtual_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `va_number` varchar(255) NOT NULL,
  `member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` varchar(255) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `expected_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `virtual_accounts`
--

INSERT INTO `virtual_accounts` (`id`, `va_number`, `member_id`, `invoice_id`, `bukti_pembayaran`, `bank`, `expected_amount`, `paid_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'VA-53974527', 1, '10001', 'virtual-account/bukti/1781455011_logo (1).png', 'bca', 1500000.00, 0.00, 'pending', '2026-06-10 20:04:04', '2026-06-14 09:36:51'),
(2, 'VA-94741434', 1, NULL, NULL, 'bni', 2500000.00, 2500000.00, 'paid', '2026-06-10 20:04:04', '2026-06-10 20:04:04'),
(3, 'VA-2026-00003', 2, '10000', 'virtual-account/bukti/Pl3Wilod2OdUIXqsGtULAqkuAMUOWcvA15714wv1.pdf', 'BCA', 10000.00, 10000.00, 'pending', '2026-06-13 16:03:08', '2026-06-13 16:03:08');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggaran_proyek`
--
ALTER TABLE `anggaran_proyek`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `asuransi`
--
ALTER TABLE `asuransi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asuransi_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `asuransi_history`
--
ALTER TABLE `asuransi_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asuransi_history_asuransi_kendaraan_id_foreign` (`asuransi_kendaraan_id`),
  ADD KEY `asuransi_history_kendaraan_id_foreign` (`kendaraan_id`),
  ADD KEY `asuransi_history_asuransi_id_foreign` (`asuransi_id`),
  ADD KEY `asuransi_history_jenis_asuransi_id_foreign` (`jenis_asuransi_id`);

--
-- Indeks untuk tabel `asuransi_kendaraan`
--
ALTER TABLE `asuransi_kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asuransi_kendaraan_kendaraan_id_foreign` (`kendaraan_id`),
  ADD KEY `asuransi_kendaraan_asuransi_id_foreign` (`asuransi_id`),
  ADD KEY `asuransi_kendaraan_jenis_asuransi_id_foreign` (`jenis_asuransi_id`);

--
-- Indeks untuk tabel `biaya_operasional_kendaraans`
--
ALTER TABLE `biaya_operasional_kendaraans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `biaya_tambahans`
--
ALTER TABLE `biaya_tambahans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `biaya_tambahans_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `bukubesars`
--
ALTER TABLE `bukubesars`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `bupot`
--
ALTER TABLE `bupot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bupot_nomor_bukti_index` (`nomor_bukti`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `denda_rentals`
--
ALTER TABLE `denda_rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `denda_rentals_rental_id_foreign` (`rental_id`);

--
-- Indeks untuk tabel `deposit_customers`
--
ALTER TABLE `deposit_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposit_customers_rental_id_foreign` (`rental_id`);

--
-- Indeks untuk tabel `efakturs`
--
ALTER TABLE `efakturs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `efakturs_nomor_faktur_index` (`nomor_faktur`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `gps`
--
ALTER TABLE `gps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gps_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `gps_kendaraan`
--
ALTER TABLE `gps_kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gps_kendaraan_kendaraan_id_foreign` (`kendaraan_id`),
  ADD KEY `gps_kendaraan_gps_id_foreign` (`gps_id`);

--
-- Indeks untuk tabel `gps_kendaraan_histories`
--
ALTER TABLE `gps_kendaraan_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gps_kendaraan_histories_gps_kendaraan_id_foreign` (`gps_kendaraan_id`),
  ADD KEY `gps_kendaraan_histories_kendaraan_id_foreign` (`kendaraan_id`),
  ADD KEY `gps_kendaraan_histories_gps_id_foreign` (`gps_id`);

--
-- Indeks untuk tabel `hutang_vendors`
--
ALTER TABLE `hutang_vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jenis`
--
ALTER TABLE `jenis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `jenis_asuransi`
--
ALTER TABLE `jenis_asuransi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kendaraan_user_id_foreign` (`user_id`),
  ADD KEY `kendaraan_jenis_id_foreign` (`jenis_id`);

--
-- Indeks untuk tabel `keuangans`
--
ALTER TABLE `keuangans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keuangans_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `kir`
--
ALTER TABLE `kir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kir_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `kir_history`
--
ALTER TABLE `kir_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kir_history_kir_id_foreign` (`kir_id`),
  ADD KEY `kir_history_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `member_kendaraan`
--
ALTER TABLE `member_kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_kendaraan_member_id_foreign` (`member_id`),
  ADD KEY `member_kendaraan_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pajak_histories`
--
ALTER TABLE `pajak_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pajak_histories_pajak_kendaraan_id_foreign` (`pajak_kendaraan_id`),
  ADD KEY `pajak_histories_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `pajak_kendaraans`
--
ALTER TABLE `pajak_kendaraans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pajak_kendaraans_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `rekonsiliasi_bank`
--
ALTER TABLE `rekonsiliasi_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rentals_user_id_foreign` (`user_id`),
  ADD KEY `rentals_kendaraan_id_foreign` (`kendaraan_id`),
  ADD KEY `rentals_member_id_foreign` (`member_id`);

--
-- Indeks untuk tabel `rental_biaya_tambahan`
--
ALTER TABLE `rental_biaya_tambahan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rental_biaya_tambahan_rental_id_foreign` (`rental_id`),
  ADD KEY `rental_biaya_tambahan_biaya_tambahan_id_foreign` (`biaya_tambahan_id`);

--
-- Indeks untuk tabel `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `service_detail`
--
ALTER TABLE `service_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_detail_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `service_history`
--
ALTER TABLE `service_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_history_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `virtual_accounts`
--
ALTER TABLE `virtual_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `virtual_accounts_va_number_unique` (`va_number`),
  ADD KEY `virtual_accounts_member_id_foreign` (`member_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggaran_proyek`
--
ALTER TABLE `anggaran_proyek`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `asuransi`
--
ALTER TABLE `asuransi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `asuransi_history`
--
ALTER TABLE `asuransi_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `asuransi_kendaraan`
--
ALTER TABLE `asuransi_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `biaya_operasional_kendaraans`
--
ALTER TABLE `biaya_operasional_kendaraans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `biaya_tambahans`
--
ALTER TABLE `biaya_tambahans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bukubesars`
--
ALTER TABLE `bukubesars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `bupot`
--
ALTER TABLE `bupot`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `denda_rentals`
--
ALTER TABLE `denda_rentals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `deposit_customers`
--
ALTER TABLE `deposit_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `efakturs`
--
ALTER TABLE `efakturs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gps`
--
ALTER TABLE `gps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `gps_kendaraan`
--
ALTER TABLE `gps_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `gps_kendaraan_histories`
--
ALTER TABLE `gps_kendaraan_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `hutang_vendors`
--
ALTER TABLE `hutang_vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jenis`
--
ALTER TABLE `jenis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `jenis_asuransi`
--
ALTER TABLE `jenis_asuransi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `keuangans`
--
ALTER TABLE `keuangans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `kir`
--
ALTER TABLE `kir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `kir_history`
--
ALTER TABLE `kir_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `member`
--
ALTER TABLE `member`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `member_kendaraan`
--
ALTER TABLE `member_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `pajak_histories`
--
ALTER TABLE `pajak_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `pajak_kendaraans`
--
ALTER TABLE `pajak_kendaraans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `rekonsiliasi_bank`
--
ALTER TABLE `rekonsiliasi_bank`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `rental_biaya_tambahan`
--
ALTER TABLE `rental_biaya_tambahan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `service`
--
ALTER TABLE `service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `service_detail`
--
ALTER TABLE `service_detail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `service_history`
--
ALTER TABLE `service_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `virtual_accounts`
--
ALTER TABLE `virtual_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `asuransi`
--
ALTER TABLE `asuransi`
  ADD CONSTRAINT `asuransi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `asuransi_history`
--
ALTER TABLE `asuransi_history`
  ADD CONSTRAINT `asuransi_history_asuransi_id_foreign` FOREIGN KEY (`asuransi_id`) REFERENCES `asuransi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asuransi_history_asuransi_kendaraan_id_foreign` FOREIGN KEY (`asuransi_kendaraan_id`) REFERENCES `asuransi_kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asuransi_history_jenis_asuransi_id_foreign` FOREIGN KEY (`jenis_asuransi_id`) REFERENCES `jenis_asuransi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asuransi_history_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `asuransi_kendaraan`
--
ALTER TABLE `asuransi_kendaraan`
  ADD CONSTRAINT `asuransi_kendaraan_asuransi_id_foreign` FOREIGN KEY (`asuransi_id`) REFERENCES `asuransi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asuransi_kendaraan_jenis_asuransi_id_foreign` FOREIGN KEY (`jenis_asuransi_id`) REFERENCES `jenis_asuransi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asuransi_kendaraan_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `biaya_tambahans`
--
ALTER TABLE `biaya_tambahans`
  ADD CONSTRAINT `biaya_tambahans_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `denda_rentals`
--
ALTER TABLE `denda_rentals`
  ADD CONSTRAINT `denda_rentals_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `deposit_customers`
--
ALTER TABLE `deposit_customers`
  ADD CONSTRAINT `deposit_customers_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `gps`
--
ALTER TABLE `gps`
  ADD CONSTRAINT `gps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `gps_kendaraan`
--
ALTER TABLE `gps_kendaraan`
  ADD CONSTRAINT `gps_kendaraan_gps_id_foreign` FOREIGN KEY (`gps_id`) REFERENCES `gps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gps_kendaraan_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `gps_kendaraan_histories`
--
ALTER TABLE `gps_kendaraan_histories`
  ADD CONSTRAINT `gps_kendaraan_histories_gps_id_foreign` FOREIGN KEY (`gps_id`) REFERENCES `gps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gps_kendaraan_histories_gps_kendaraan_id_foreign` FOREIGN KEY (`gps_kendaraan_id`) REFERENCES `gps_kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gps_kendaraan_histories_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jenis`
--
ALTER TABLE `jenis`
  ADD CONSTRAINT `jenis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `kendaraan_jenis_id_foreign` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kendaraan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keuangans`
--
ALTER TABLE `keuangans`
  ADD CONSTRAINT `keuangans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kir`
--
ALTER TABLE `kir`
  ADD CONSTRAINT `kir_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kir_history`
--
ALTER TABLE `kir_history`
  ADD CONSTRAINT `kir_history_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kir_history_kir_id_foreign` FOREIGN KEY (`kir_id`) REFERENCES `kir` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `member_kendaraan`
--
ALTER TABLE `member_kendaraan`
  ADD CONSTRAINT `member_kendaraan_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_kendaraan_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pajak_histories`
--
ALTER TABLE `pajak_histories`
  ADD CONSTRAINT `pajak_histories_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pajak_histories_pajak_kendaraan_id_foreign` FOREIGN KEY (`pajak_kendaraan_id`) REFERENCES `pajak_kendaraans` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pajak_kendaraans`
--
ALTER TABLE `pajak_kendaraans`
  ADD CONSTRAINT `pajak_kendaraans_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rental_biaya_tambahan`
--
ALTER TABLE `rental_biaya_tambahan`
  ADD CONSTRAINT `rental_biaya_tambahan_biaya_tambahan_id_foreign` FOREIGN KEY (`biaya_tambahan_id`) REFERENCES `biaya_tambahans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rental_biaya_tambahan_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `service_detail`
--
ALTER TABLE `service_detail`
  ADD CONSTRAINT `service_detail_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `service_history`
--
ALTER TABLE `service_history`
  ADD CONSTRAINT `service_history_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `virtual_accounts`
--
ALTER TABLE `virtual_accounts`
  ADD CONSTRAINT `virtual_accounts_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
