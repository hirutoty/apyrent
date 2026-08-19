-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Agu 2026 pada 21.35
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
-- Struktur dari tabel `ads_integrations`
--

CREATE TABLE `ads_integrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_iklan` varchar(255) NOT NULL,
  `nama_iklan` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `tanggal_aktif` date NOT NULL,
  `budget_harian` decimal(15,2) NOT NULL,
  `klik` bigint(20) NOT NULL DEFAULT 0,
  `konversi` bigint(20) NOT NULL DEFAULT 0,
  `biaya_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `penjualan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `roi` varchar(255) NOT NULL DEFAULT '0%',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ads_integrations`
--

INSERT INTO `ads_integrations` (`id`, `id_iklan`, `nama_iklan`, `platform`, `tanggal_aktif`, `budget_harian`, `klik`, `konversi`, `biaya_total`, `penjualan`, `roi`, `created_at`, `updated_at`) VALUES
(1, 'ADS001', 'Google Ads - Rental Mobil Jakarta', 'Google Ads', '2026-07-01', 500000.00, 350, 28, 15000000.00, 70000000.00, '367%', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'ADS002', 'Facebook Ads - Awareness Campaign', 'Meta Ads', '2026-07-05', 300000.00, 520, 35, 9000000.00, 52500000.00, '483%', '2026-08-10 23:55:33', '2026-08-10 23:55:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `afiliasis`
--

CREATE TABLE `afiliasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_program` varchar(255) NOT NULL,
  `nama_program` varchar(255) NOT NULL,
  `kode_referral` varchar(255) NOT NULL,
  `diskon_referral` decimal(15,2) NOT NULL,
  `bonus_pengajak` varchar(255) NOT NULL,
  `batas_waktu` date NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `afiliasis`
--

INSERT INTO `afiliasis` (`id`, `id_program`, `nama_program`, `kode_referral`, `diskon_referral`, `bonus_pengajak`, `batas_waktu`, `status`, `created_at`, `updated_at`) VALUES
(1, 'AFI001', 'Referral Teman', 'REF-APY001', 50000.00, 'Rp 75.000 kredit', '2026-12-31', 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'AFI002', 'Corporate Partner', 'REF-CORP001', 100000.00, 'Komisi 5%', '2026-12-31', 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `aging_aps`
--

CREATE TABLE `aging_aps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `no_tagihan` varchar(255) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `jumlah` bigint(20) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `hutang_vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_lunas` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `aging_ars`
--

CREATE TABLE `aging_ars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `total` decimal(15,2) DEFAULT 0.00,
  `kategori` varchar(255) DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `status` enum('Belum Bayar','Bayar') NOT NULL DEFAULT 'Belum Bayar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Pembangunan Sistem Rental', 'Development', 15000000.00, 6000000.00, 9000000.00, 40.00, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(2, 'Server & Hosting', 'Infrastructure', 5000000.00, 2500000.00, 2500000.00, 50.00, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(3, 'Pembelian GPS', 'Operasional', 10000000.00, 7500000.00, 2500000.00, 75.00, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(4, 'Promosi Rental', 'Marketing', 7000000.00, 3000000.00, 4000000.00, 42.86, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(5, 'Service Kendaraan', 'Maintenance', 12000000.00, 4500000.00, 7500000.00, 37.50, '2026-08-10 23:55:26', '2026-08-10 23:55:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `approval_workflows`
--

CREATE TABLE `approval_workflows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_po` varchar(255) NOT NULL,
  `urutan_approval` bigint(20) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `nama_approver` varchar(255) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `status_approval` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `approval_workflows`
--

INSERT INTO `approval_workflows` (`id`, `id_po`, `urutan_approval`, `jabatan`, `nama_approver`, `tanggal`, `status_approval`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PO-001', 1, 'Supervisor Pembelian', 'Budi Santoso', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(2, 'PO-001', 2, 'Manager Operasional', 'Rina Wulandari', '2026-06-28', 'Approved', 'Review urutan 2 untuk PO-001', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(3, 'PO-002', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-07-17', 'Rejected', 'Review urutan 1 untuk PO-002', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(4, 'PO-002', 2, 'Manager Operasional', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(5, 'PO-003', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-07-10', 'Approved', 'Review urutan 1 untuk PO-003', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(6, 'PO-003', 2, 'Manager Operasional', 'Budi Santoso', '2026-06-24', 'Rejected', 'Review urutan 2 untuk PO-003', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(7, 'PO-004', 1, 'Supervisor Pembelian', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(8, 'PO-004', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-07-08', 'Approved', 'Review urutan 2 untuk PO-004', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(9, 'PO-005', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-07-20', 'Rejected', 'Review urutan 1 untuk PO-005', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(10, 'PO-005', 2, 'Manager Operasional', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(11, 'PO-006', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-06-21', 'Approved', 'Review urutan 1 untuk PO-006', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(12, 'PO-006', 2, 'Manager Operasional', 'Rina Wulandari', '2026-07-02', 'Rejected', 'Review urutan 2 untuk PO-006', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(13, 'PO-007', 1, 'Supervisor Pembelian', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(14, 'PO-007', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-07-09', 'Approved', 'Review urutan 2 untuk PO-007', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(15, 'PO-008', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-07-27', 'Rejected', 'Review urutan 1 untuk PO-008', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(16, 'PO-008', 2, 'Manager Operasional', 'Budi Santoso', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(17, 'PO-009', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-07-18', 'Approved', 'Review urutan 1 untuk PO-009', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(18, 'PO-009', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-06-21', 'Rejected', 'Review urutan 2 untuk PO-009', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(19, 'PO-010', 1, 'Supervisor Pembelian', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(20, 'PO-010', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-07-02', 'Approved', 'Review urutan 2 untuk PO-010', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(21, 'PO-011', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-08-05', 'Rejected', 'Review urutan 1 untuk PO-011', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(22, 'PO-011', 2, 'Manager Operasional', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(23, 'PO-012', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-07-29', 'Approved', 'Review urutan 1 untuk PO-012', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(24, 'PO-012', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-07-24', 'Rejected', 'Review urutan 2 untuk PO-012', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(25, 'PO-013', 1, 'Supervisor Pembelian', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(26, 'PO-013', 2, 'Manager Operasional', 'Budi Santoso', '2026-06-30', 'Approved', 'Review urutan 2 untuk PO-013', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(27, 'PO-014', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-07-02', 'Rejected', 'Review urutan 1 untuk PO-014', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(28, 'PO-014', 2, 'Manager Operasional', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(29, 'PO-015', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-06-27', 'Approved', 'Review urutan 1 untuk PO-015', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(30, 'PO-015', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-07-11', 'Rejected', 'Review urutan 2 untuk PO-015', '2026-08-10 23:55:34', '2026-08-10 23:55:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_dihapuskans`
--

CREATE TABLE `asset_dihapuskans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `tanggal_hapus` date NOT NULL,
  `alasan` text NOT NULL,
  `nilai_buku` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_akhir` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 'BCA Insurance', 'Jl. Sudirman No. 10 Jakarta', 'Andi Saputra', '081234567890', 'Bengkel Maju Motor', '082233445566', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(2, 1, 'Adira Insurance', 'Jl. Malioboro No. 20 Yogyakarta', 'Budi Hartono', '081298765432', 'Bengkel Jaya Abadi', '085566778899', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(3, 1, 'ACA Insurance', 'Jl. Pemuda No. 12 Semarang', 'Siti Rahma', '087712345678', 'Bengkel Berkah Mobil', '081122334455', '2026-08-10 23:55:26', '2026-08-10 23:55:26');

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
  `durasi_bulan` bigint(20) NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status_kendaraan` varchar(255) NOT NULL DEFAULT 'aktif',
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `durasi_bulan` bigint(20) NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `asuransi_kendaraan`
--

INSERT INTO `asuransi_kendaraan` (`id`, `kendaraan_id`, `asuransi_id`, `jenis_asuransi_id`, `status_kendaraan`, `tgl_mulai`, `tgl_berakhir`, `durasi_bulan`, `biaya`, `tanggal_bayar`, `bukti_bayar`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'aktif', '2026-08-11', '2026-11-11', 3, 11000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(2, 2, 2, 2, 'expired', '2025-10-11', '2026-04-11', 6, 20500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(3, 3, 3, 3, 'aktif', '2026-02-11', '2027-02-11', 12, 9000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(4, 4, 1, 1, 'aktif', '2025-02-11', '2027-02-11', 24, 9500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(5, 5, 2, 2, 'expired', '2025-09-11', '2025-12-11', 3, 8000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(6, 6, 3, 3, 'aktif', '2026-04-11', '2026-10-11', 6, 9000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(7, 7, 1, 1, 'expired', '2025-04-11', '2026-04-11', 12, 3000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(8, 8, 2, 2, 'aktif', '2026-02-11', '2028-02-11', 24, 2500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(9, 9, 3, 3, 'expired', '2026-05-11', '2026-08-11', 3, 22500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(10, 10, 1, 1, 'expired', '2025-05-11', '2025-11-11', 6, 20000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(11, 11, 2, 2, 'aktif', '2026-03-11', '2027-03-11', 12, 12500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(12, 12, 3, 3, 'aktif', '2026-05-11', '2028-05-11', 24, 9000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(13, 13, 1, 1, 'expired', '2025-06-11', '2025-09-11', 3, 5500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(14, 14, 2, 2, 'expired', '2025-05-11', '2025-11-11', 6, 21000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(15, 15, 3, 3, 'aktif', '2025-11-11', '2026-11-11', 12, 4500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(16, 16, 1, 1, 'aktif', '2026-07-11', '2028-07-11', 24, 24000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(17, 17, 2, 2, 'expired', '2026-01-11', '2026-04-11', 3, 15500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(18, 18, 3, 3, 'expired', '2025-09-11', '2026-03-11', 6, 20000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(19, 19, 1, 1, 'aktif', '2026-08-11', '2027-08-11', 12, 21000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(20, 20, 2, 2, 'aktif', '2025-03-11', '2027-03-11', 24, 16500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(21, 21, 3, 3, 'expired', '2026-01-11', '2026-04-11', 3, 7500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(22, 22, 1, 1, 'aktif', '2026-04-11', '2026-10-11', 6, 19500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(23, 23, 2, 2, 'aktif', '2025-10-11', '2026-10-11', 12, 12500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(24, 24, 3, 3, 'aktif', '2026-02-11', '2028-02-11', 24, 23000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(25, 25, 1, 1, 'aktif', '2026-06-11', '2026-09-11', 3, 15500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(26, 26, 2, 2, 'expired', '2025-01-11', '2025-07-11', 6, 3000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(27, 27, 3, 3, 'expired', '2025-02-11', '2026-02-11', 12, 4000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(28, 28, 1, 1, 'aktif', '2026-04-11', '2028-04-11', 24, 11500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(29, 29, 2, 2, 'expired', '2025-02-11', '2025-05-11', 3, 8500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(30, 30, 3, 3, 'aktif', '2026-08-11', '2027-02-11', 6, 17000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(31, 31, 1, 1, 'expired', '2025-02-11', '2026-02-11', 12, 12000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(32, 32, 2, 2, 'aktif', '2025-08-11', '2027-08-11', 24, 20000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(33, 33, 3, 3, 'expired', '2025-01-11', '2025-04-11', 3, 11000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(34, 34, 1, 1, 'expired', '2025-11-11', '2026-05-11', 6, 11500000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(35, 35, 2, 2, 'aktif', '2025-09-11', '2026-09-11', 12, 13000000.00, NULL, NULL, '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(36, 36, 3, 3, 'aktif', '2025-09-11', '2027-09-11', 24, 12000000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(37, 37, 1, 1, 'expired', '2026-03-11', '2026-06-11', 3, 24500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(38, 38, 2, 2, 'aktif', '2026-07-11', '2027-01-11', 6, 24500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(39, 39, 3, 3, 'aktif', '2026-07-11', '2027-07-11', 12, 11000000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(40, 40, 1, 1, 'aktif', '2025-02-11', '2027-02-11', 24, 15000000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(41, 41, 2, 2, 'expired', '2026-04-11', '2026-07-11', 3, 23000000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(42, 42, 3, 3, 'expired', '2025-06-11', '2025-12-11', 6, 17500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(43, 43, 1, 1, 'aktif', '2026-08-11', '2027-08-11', 12, 24500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(44, 44, 2, 2, 'aktif', '2026-04-11', '2028-04-11', 24, 13500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(45, 45, 3, 3, 'expired', '2025-08-11', '2025-11-11', 3, 10000000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(46, 46, 1, 1, 'expired', '2025-06-11', '2025-12-11', 6, 4500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(47, 47, 2, 2, 'aktif', '2026-02-11', '2027-02-11', 12, 7500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(48, 48, 3, 3, 'aktif', '2026-03-11', '2028-03-11', 24, 22500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(49, 49, 1, 1, 'expired', '2026-01-11', '2026-04-11', 3, 13500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(50, 50, 2, 2, 'expired', '2025-09-11', '2026-03-11', 6, 9500000.00, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `relation_type` varchar(255) NOT NULL,
  `relation_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `attachments`
--

INSERT INTO `attachments` (`id`, `relation_type`, `relation_id`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 'data_kontrak', 5, 'draft_KTR-202608-0006 (1).pdf', 'uploads/data-kontrak/1786551254_draft_KTR-202608-0006 (1).pdf', 'application/pdf', 16644, '2026-08-12 16:14:14', '2026-08-12 16:14:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_assets`
--

CREATE TABLE `audit_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `tanggal_audit` date NOT NULL,
  `diperiksa_oleh` varchar(255) NOT NULL,
  `status_fisik` varchar(255) NOT NULL,
  `temuan` varchar(255) NOT NULL,
  `tindakan` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `kategori` enum('Pendapatan','Beban','Aktiva','Modal','Kewajiban') DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `kredit` decimal(15,2) DEFAULT 0.00,
  `saldo` decimal(15,2) DEFAULT 0.00,
  `aktivitas` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `referensi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bukubesars`
--

INSERT INTO `bukubesars` (`id`, `kode_jurnal`, `transaksi`, `kategori`, `tanggal`, `debit`, `kredit`, `saldo`, `aktivitas`, `keterangan`, `referensi`, `created_at`, `updated_at`) VALUES
(1, 'JRNL-001', 'Pemasukan Rental Harian', 'Pendapatan', '2026-03-27', 1500000.00, 0.00, 1500000.00, 'rental', 'Pembayaran rental harian dari customer', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(2, 'JRNL-002', 'Pemasukan Rental Mingguan', 'Pendapatan', '2026-03-25', 3500000.00, 0.00, 5000000.00, 'rental', 'Pembayaran rental mingguan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(3, 'JRNL-003', 'Penerimaan DP Rental', 'Pendapatan', '2026-02-26', 1000000.00, 0.00, 6000000.00, 'rental', 'DP rental kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(4, 'JRNL-004', 'Pelunasan Rental', 'Pendapatan', '2026-03-11', 2000000.00, 0.00, 8000000.00, 'rental', 'Pelunasan biaya rental', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(5, 'JRNL-005', 'Penerimaan Denda Keterlambatan', 'Pendapatan', '2026-08-09', 250000.00, 0.00, 8250000.00, 'denda', 'Denda pengembalian terlambat', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(6, 'JRNL-006', 'Penerimaan Deposit Customer', 'Pendapatan', '2026-06-12', 500000.00, 0.00, 8750000.00, 'deposit', 'Deposit jaminan kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(7, 'JRNL-007', 'Pendapatan Biaya Tambahan', 'Pendapatan', '2026-06-17', 200000.00, 0.00, 8950000.00, 'rental', 'Biaya supir tambahan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(8, 'JRNL-008', 'Penerimaan Sewa Jangka Panjang', 'Pendapatan', '2026-06-07', 15000000.00, 0.00, 23950000.00, 'rental', 'Kontrak sewa bulanan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(9, 'JRNL-009', 'Pendapatan Lain-lain', 'Pendapatan', '2026-07-31', 350000.00, 0.00, 24300000.00, 'lain', 'Pendapatan di luar operasional utama', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(10, 'JRNL-010', 'Penerimaan Invoice Kontrak', 'Pendapatan', '2026-03-04', 8000000.00, 0.00, 32300000.00, 'invoice', 'Pembayaran invoice kontrak korporat', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(11, 'JRNL-011', 'Biaya Servis Berkala', 'Beban', '2026-04-21', 0.00, 500000.00, 31800000.00, 'service', 'Servis rutin kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(12, 'JRNL-012', 'Biaya Ganti Oli', 'Beban', '2026-06-15', 0.00, 150000.00, 31650000.00, 'service', 'Penggantian oli mesin', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(13, 'JRNL-013', 'Pembayaran Pajak Kendaraan', 'Beban', '2026-06-04', 0.00, 3500000.00, 28150000.00, 'pajak', 'Pajak tahunan kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(14, 'JRNL-014', 'Premi Asuransi Kendaraan', 'Beban', '2026-05-28', 0.00, 5000000.00, 23150000.00, 'asuransi', 'Pembayaran premi asuransi', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(15, 'JRNL-015', 'Biaya Sewa GPS', 'Beban', '2026-03-28', 0.00, 300000.00, 22850000.00, 'gps', 'Biaya langganan GPS tracker', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(16, 'JRNL-016', 'Biaya Bahan Bakar', 'Beban', '2026-03-10', 0.00, 800000.00, 22050000.00, 'operasional', 'Pembelian bahan bakar kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(17, 'JRNL-017', 'Biaya KIR Kendaraan', 'Beban', '2026-06-21', 0.00, 200000.00, 21850000.00, 'kir', 'Biaya uji KIR kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(18, 'JRNL-018', 'Biaya Gaji Karyawan', 'Beban', '2026-08-09', 0.00, 5000000.00, 16850000.00, 'gaji', 'Gaji karyawan bulan ini', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(19, 'JRNL-019', 'Biaya Pembelian Spare Part', 'Beban', '2026-04-19', 0.00, 1200000.00, 15650000.00, 'service', 'Pembelian ban dan kampas rem', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(20, 'JRNL-020', 'Biaya Listrik dan Air', 'Beban', '2026-03-05', 0.00, 450000.00, 15200000.00, 'operasional', 'Tagihan utilitas kantor', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(21, 'JRNL-021', 'Pembelian Kendaraan Baru', 'Aktiva', '2026-06-15', 250000000.00, 0.00, 265200000.00, 'pembelian', 'Penambahan aset kendaraan baru', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(22, 'JRNL-022', 'Kas di Tangan', 'Aktiva', '2026-02-23', 10000000.00, 0.00, 275200000.00, 'kas', 'Saldo kas operasional', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(23, 'JRNL-023', 'Kas di Bank', 'Aktiva', '2026-05-16', 50000000.00, 0.00, 325200000.00, 'kas', 'Saldo rekening bank perusahaan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(24, 'JRNL-024', 'Piutang Rental', 'Aktiva', '2026-03-19', 7500000.00, 0.00, 332700000.00, 'rental', 'Tagihan belum dibayar customer', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(25, 'JRNL-025', 'Perlengkapan Kantor', 'Aktiva', '2026-04-12', 2500000.00, 0.00, 335200000.00, 'operasional', 'Inventaris perlengkapan kantor', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(26, 'JRNL-026', 'Peralatan Workshop', 'Aktiva', '2026-03-04', 15000000.00, 0.00, 350200000.00, 'service', 'Alat bengkel dan servis kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(27, 'JRNL-027', 'Deposit GPS Provider', 'Aktiva', '2026-07-04', 1000000.00, 0.00, 351200000.00, 'gps', 'Deposit ke penyedia GPS', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(28, 'JRNL-028', 'Persediaan Sparepart', 'Aktiva', '2026-05-17', 3000000.00, 0.00, 354200000.00, 'service', 'Stok sparepart di gudang', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(29, 'JRNL-029', 'Gedung Kantor', 'Aktiva', '2026-05-31', 500000000.00, 0.00, 854200000.00, 'aset', 'Nilai gedung kantor operasional', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(30, 'JRNL-030', 'Kendaraan Operasional', 'Aktiva', '2026-03-21', 180000000.00, 0.00, 1034200000.00, 'aset', 'Nilai armada kendaraan sewa', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(31, 'JRNL-031', 'Modal Awal Pemilik', 'Modal', '2026-04-10', 0.00, 500000000.00, 534200000.00, 'modal', 'Setoran modal awal perusahaan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(32, 'JRNL-032', 'Tambahan Modal Investasi', 'Modal', '2026-04-06', 0.00, 100000000.00, 434200000.00, 'modal', 'Investasi tambahan dari pemilik', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(33, 'JRNL-033', 'Laba Ditahan Tahun Lalu', 'Modal', '2026-07-02', 0.00, 75000000.00, 359200000.00, 'modal', 'Akumulasi laba yang tidak dibagikan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(34, 'JRNL-034', 'Dividen Dibayarkan', 'Modal', '2026-07-09', 25000000.00, 0.00, 384200000.00, 'modal', 'Pembagian dividen kepada pemilik', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(35, 'JRNL-035', 'Laba Bersih Periode Berjalan', 'Modal', '2026-07-10', 0.00, 45000000.00, 339200000.00, 'modal', 'Laba bersih periode ini', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(36, 'JRNL-036', 'Cadangan Umum', 'Modal', '2026-04-14', 0.00, 10000000.00, 329200000.00, 'modal', 'Cadangan dana untuk ekspansi', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(37, 'JRNL-037', 'Prive Pemilik', 'Modal', '2026-07-15', 5000000.00, 0.00, 334200000.00, 'modal', 'Pengambilan pribadi pemilik', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(38, 'JRNL-038', 'Revaluasi Aset Kendaraan', 'Modal', '2026-03-05', 0.00, 20000000.00, 314200000.00, 'aset', 'Kenaikan nilai aset kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(39, 'JRNL-039', 'Modal Kerja Tambahan', 'Modal', '2026-05-15', 0.00, 30000000.00, 284200000.00, 'modal', 'Penambahan modal kerja operasional', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(40, 'JRNL-040', 'Saldo Modal Berjalan', 'Modal', '2026-05-16', 0.00, 15000000.00, 269200000.00, 'modal', 'Saldo modal per periode ini', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(41, 'JRNL-041', 'Hutang Bank Jangka Panjang', 'Kewajiban', '2026-02-28', 0.00, 200000000.00, 69200000.00, 'hutang', 'Pinjaman bank untuk pembelian kendaraan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(42, 'JRNL-042', 'Hutang Leasing Kendaraan', 'Kewajiban', '2026-06-05', 0.00, 120000000.00, -50800000.00, 'hutang', 'Cicilan leasing kendaraan baru', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(43, 'JRNL-043', 'Hutang Vendor Sparepart', 'Kewajiban', '2026-06-23', 0.00, 8000000.00, -58800000.00, 'hutang', 'Tagihan belum dibayar ke vendor', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(44, 'JRNL-044', 'Hutang Pajak', 'Kewajiban', '2026-04-23', 0.00, 5000000.00, -63800000.00, 'pajak', 'Kewajiban pajak yang belum dibayar', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(45, 'JRNL-045', 'Hutang Gaji Karyawan', 'Kewajiban', '2026-02-26', 0.00, 15000000.00, -78800000.00, 'gaji', 'Gaji bulan lalu yang belum dibayar', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(46, 'JRNL-046', 'Hutang GPS Provider', 'Kewajiban', '2026-07-01', 0.00, 900000.00, -79700000.00, 'gps', 'Tagihan langganan GPS yang tertunda', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(47, 'JRNL-047', 'Hutang Asuransi', 'Kewajiban', '2026-08-07', 0.00, 3000000.00, -82700000.00, 'asuransi', 'Premi asuransi yang belum dibayar', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(48, 'JRNL-048', 'Deposit Customer Diterima', 'Kewajiban', '2026-06-01', 0.00, 4500000.00, -87200000.00, 'deposit', 'Deposit yang harus dikembalikan', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(49, 'JRNL-049', 'Hutang Listrik dan Utilitas', 'Kewajiban', '2026-02-13', 0.00, 750000.00, -87950000.00, 'operasional', 'Tagihan utilitas yang belum dibayar', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(50, 'JRNL-050', 'Hutang Jangka Pendek Lainnya', 'Kewajiban', '2026-03-10', 0.00, 2000000.00, -89950000.00, 'hutang', 'Kewajiban jangka pendek lain-lain', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31');

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
  `status` enum('Draft','Approve','Submit DJP') NOT NULL DEFAULT 'Draft',
  `file_bupot` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bupot`
--

INSERT INTO `bupot` (`id`, `nomor_bukti`, `tanggal_bukti`, `tipe`, `npwp_pemotong`, `nama_pemotong`, `npwp_dipotong`, `nama_dipotong`, `jumlah_bruto`, `tarif_pajak`, `jumlah_potong`, `status`, `file_bupot`, `created_at`, `updated_at`) VALUES
(1, 'BUPOT-001', '2026-08-01', 'PPh21', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '09.876.543.2-109.000', 'Budi Santoso', 5000000.00, 0.05, 250000.00, 'Approve', NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(2, 'BUPOT-002', '2026-08-03', 'PPh23', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '08.765.432.1-000.000', 'CV Sinar Abadi', 3000000.00, 0.02, 60000.00, 'Approve', NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(3, 'BUPOT-003', '2026-08-06', 'PPh26', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '07.654.321.0-999.000', 'UD Jaya Motor', 10000000.00, 0.10, 1000000.00, 'Draft', NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_prospeks`
--

CREATE TABLE `crm_prospeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_prospek` varchar(255) NOT NULL,
  `nama_kontak` varchar(255) NOT NULL,
  `perusahaan` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `tahapan` varchar(255) NOT NULL,
  `estimasi_deal` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `sales` varchar(255) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_prospeks`
--

INSERT INTO `crm_prospeks` (`id`, `kode_prospek`, `nama_kontak`, `perusahaan`, `email`, `telepon`, `tahapan`, `estimasi_deal`, `status`, `sales`, `tanggal_masuk`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PRO-001', 'Budi Santoso', 'PT Maju Bersama', NULL, '0812-1111-1111', 'Prospek', NULL, 'Aktif', 'Andi', '2026-01-10', 'Butuh armada 5 unit', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'PRO-002', 'Siti Rahayu', 'CV Karya Indah', NULL, '0813-2222-2222', 'Negosiasi', NULL, 'Aktif', 'Budi', '2026-02-05', 'Diskusi harga sudah selesai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'PRO-003', 'Ahmad Fauzi', 'PT Sejahtera Abadi', NULL, '0814-3333-3333', 'Closing', NULL, 'Aktif', 'Cici', '2026-02-20', 'Kontrak siap ditandatangani', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'PRO-004', 'Dewi Lestari', 'PT Global Trans', NULL, '0815-4444-4444', 'Prospek', NULL, 'Aktif', 'Andi', '2026-03-01', 'Masih dalam penjajakan', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'PRO-005', 'Rudi Hartono', 'CV Jaya Mandiri', NULL, '0816-5555-5555', 'Negosiasi', NULL, 'Aktif', 'Dani', '2026-03-15', 'Negosiasi tenor kontrak', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'PRO-006', 'Lia Permata', 'PT Nusantara Raya', NULL, '0817-6666-6666', 'Closing', NULL, 'Aktif', 'Budi', '2026-04-02', 'Deal 3 unit minibus', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'PRO-007', 'Hendra Wijaya', 'PT Sinar Harapan', NULL, '0818-7777-7777', 'Prospek', NULL, 'Tidak Aktif', 'Cici', '2026-04-10', 'Tidak merespon lagi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'PRO-008', 'Maya Anggraini', 'CV Mitra Logistik', NULL, '0819-8888-8888', 'Negosiasi', NULL, 'Aktif', 'Andi', '2026-05-01', 'Menunggu approval direksi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'PRO-009', 'Fajar Nugroho', 'PT Berlian Trans', NULL, '0821-9999-9999', 'Closing', NULL, 'Aktif', 'Dani', '2026-05-20', 'Siap kontrak', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'PRO-010', 'Indah Kusuma', 'PT Prima Raya', NULL, '0822-1010-1010', 'Prospek', NULL, 'Aktif', 'Budi', '2026-06-05', 'Prospek baru dari referral', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cuti_izins`
--

CREATE TABLE `cuti_izins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `jenis_cuti_izin` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `lama_hari` bigint(20) NOT NULL,
  `alasan` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cuti_izins`
--

INSERT INTO `cuti_izins` (`id`, `nama_pegawai`, `jenis_cuti_izin`, `tanggal_mulai`, `tanggal_selesai`, `lama_hari`, `alasan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Rini Apriani', 'Cuti Tahunan', '2026-03-21', '2026-03-26', 6, 'Keperluan keluarga', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(2, 'Eko Prasetyo', 'Cuti Sakit', '2026-07-18', '2026-07-30', 13, 'Pemulihan kesehatan', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(3, 'Rizky Fadillah', 'Cuti Melahirkan', '2026-08-10', '2026-08-13', 4, 'Acara pernikahan', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(4, 'Yusuf Hidayat', 'Izin Pribadi', '2026-05-30', '2026-05-31', 2, 'Mengurus administrasi', 'Pending', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(5, 'Wahyu Nugroho', 'Cuti Bersama', '2026-07-14', '2026-07-25', 12, 'Liburan keluarga', 'Ditolak', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(6, 'Fitri Handayani', 'Cuti Tahunan', '2026-02-19', '2026-02-21', 3, 'Cuti bersama hari raya', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(7, 'Teguh Santosa', 'Cuti Sakit', '2026-06-02', '2026-06-09', 8, 'Rawat inap di rumah sakit', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(8, 'Arif Budiman', 'Cuti Melahirkan', '2026-08-06', '2026-08-06', 1, 'Keperluan mendesak pribadi', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(9, 'Dewi Kusuma', 'Izin Pribadi', '2026-08-09', '2026-08-21', 13, 'Keperluan keluarga', 'Pending', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(10, 'Linda Permata', 'Cuti Bersama', '2026-03-03', '2026-03-10', 8, 'Pemulihan kesehatan', 'Ditolak', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(11, 'Hendra Gunawan', 'Cuti Tahunan', '2026-04-20', '2026-04-24', 5, 'Acara pernikahan', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(12, 'Dody Kurniawan', 'Cuti Sakit', '2026-08-01', '2026-08-03', 3, 'Mengurus administrasi', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(13, 'Rini Apriani', 'Cuti Melahirkan', '2026-06-14', '2026-06-20', 7, 'Liburan keluarga', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(14, 'Eko Prasetyo', 'Izin Pribadi', '2026-06-15', '2026-06-18', 4, 'Cuti bersama hari raya', 'Pending', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(15, 'Rizky Fadillah', 'Cuti Bersama', '2026-04-13', '2026-04-18', 6, 'Rawat inap di rumah sakit', 'Ditolak', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(16, 'Yusuf Hidayat', 'Cuti Tahunan', '2026-06-07', '2026-06-12', 6, 'Keperluan mendesak pribadi', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(17, 'Wahyu Nugroho', 'Cuti Sakit', '2026-05-01', '2026-05-13', 13, 'Keperluan keluarga', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(18, 'Fitri Handayani', 'Cuti Melahirkan', '2026-03-17', '2026-03-26', 10, 'Pemulihan kesehatan', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(19, 'Teguh Santosa', 'Izin Pribadi', '2026-05-16', '2026-05-26', 11, 'Acara pernikahan', 'Pending', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(20, 'Arif Budiman', 'Cuti Bersama', '2026-05-28', '2026-06-03', 7, 'Mengurus administrasi', 'Ditolak', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(21, 'Dewi Kusuma', 'Cuti Tahunan', '2026-06-09', '2026-06-14', 6, 'Liburan keluarga', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(22, 'Linda Permata', 'Cuti Sakit', '2026-03-13', '2026-03-25', 13, 'Cuti bersama hari raya', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(23, 'Hendra Gunawan', 'Cuti Melahirkan', '2026-06-10', '2026-06-11', 2, 'Rawat inap di rumah sakit', 'Disetujui', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(24, 'Dody Kurniawan', 'Izin Pribadi', '2026-03-04', '2026-03-11', 8, 'Keperluan mendesak pribadi', 'Pending', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(25, 'Rini Apriani', 'Cuti Bersama', '2026-08-07', '2026-08-07', 1, 'Keperluan keluarga', 'Ditolak', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(26, 'Eko Prasetyo', 'Cuti Tahunan', '2026-03-12', '2026-03-15', 4, 'Pemulihan kesehatan', 'Disetujui', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(27, 'Rizky Fadillah', 'Cuti Sakit', '2026-06-30', '2026-07-08', 9, 'Acara pernikahan', 'Disetujui', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(28, 'Yusuf Hidayat', 'Cuti Melahirkan', '2026-06-25', '2026-07-07', 13, 'Mengurus administrasi', 'Disetujui', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(29, 'Wahyu Nugroho', 'Izin Pribadi', '2026-06-09', '2026-06-20', 12, 'Liburan keluarga', 'Pending', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(30, 'Fitri Handayani', 'Cuti Bersama', '2026-04-06', '2026-04-09', 4, 'Cuti bersama hari raya', 'Ditolak', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(31, 'Teguh Santosa', 'Cuti Tahunan', '2026-07-01', '2026-07-03', 3, 'Rawat inap di rumah sakit', 'Disetujui', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(32, 'Arif Budiman', 'Cuti Sakit', '2026-07-22', '2026-08-01', 11, 'Keperluan mendesak pribadi', 'Disetujui', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(33, 'Dewi Kusuma', 'Cuti Melahirkan', '2026-07-28', '2026-08-08', 12, 'Keperluan keluarga', 'Disetujui', '2026-08-10 23:55:40', '2026-08-10 23:55:40'),
(34, 'Linda Permata', 'Izin Pribadi', '2026-04-21', '2026-05-04', 14, 'Pemulihan kesehatan', 'Pending', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(35, 'Hendra Gunawan', 'Cuti Bersama', '2026-02-17', '2026-02-25', 9, 'Acara pernikahan', 'Ditolak', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(36, 'Dody Kurniawan', 'Cuti Tahunan', '2026-05-13', '2026-05-15', 3, 'Mengurus administrasi', 'Disetujui', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(37, 'Rini Apriani', 'Cuti Sakit', '2026-05-11', '2026-05-21', 11, 'Liburan keluarga', 'Disetujui', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(38, 'Eko Prasetyo', 'Cuti Melahirkan', '2026-04-03', '2026-04-09', 7, 'Cuti bersama hari raya', 'Disetujui', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(39, 'Rizky Fadillah', 'Izin Pribadi', '2026-05-23', '2026-05-23', 1, 'Rawat inap di rumah sakit', 'Pending', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(40, 'Yusuf Hidayat', 'Cuti Bersama', '2026-05-02', '2026-05-08', 7, 'Keperluan mendesak pribadi', 'Ditolak', '2026-08-10 23:55:41', '2026-08-10 23:55:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cybersecurities`
--

CREATE TABLE `cybersecurities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_audit` date NOT NULL,
  `area_diaudit` varchar(255) NOT NULL,
  `temuan_risiko` text NOT NULL,
  `level_risiko` varchar(255) NOT NULL,
  `tindakan_perbaikan` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cybersecurities`
--

INSERT INTO `cybersecurities` (`id`, `tanggal_audit`, `area_diaudit`, `temuan_risiko`, `level_risiko`, `tindakan_perbaikan`, `status`, `created_at`, `updated_at`) VALUES
(1, '2025-01-05', 'Web Application', 'SQL Injection pada form login admin panel', 'Critical', 'Input sanitasi dan prepared statement diterapkan', 'Resolved', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, '2025-01-20', 'Jaringan Internal', 'Port 23 (Telnet) masih terbuka di beberapa switch', 'High', 'Disable Telnet dan aktifkan SSH pada semua perangkat jaringan', 'In Progress', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, '2025-02-10', 'Email Server', 'Tidak ada SPF dan DMARC record, rentan email spoofing', 'Medium', 'Konfigurasi SPF, DKIM, dan DMARC pada DNS', 'Open', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, '2025-02-25', 'Endpoint Security', '12 komputer belum update antivirus selama 3 bulan', 'Low', 'Update antivirus terpusat via console management', 'Resolved', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, '2025-03-01', 'Database Server', 'Akun root database dapat diakses dari remote tanpa restriksi IP', 'Critical', 'Batasi akses root hanya dari localhost, buat user terbatas', 'Open', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_notaris`
--

CREATE TABLE `daftar_notaris` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kantor` varchar(255) NOT NULL,
  `layanan` varchar(255) NOT NULL,
  `kontak` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `terakhir_dipakai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_kontraks`
--

CREATE TABLE `data_kontraks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `no_kontrak` varchar(255) DEFAULT NULL,
  `kendaraan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mobil` varchar(255) DEFAULT NULL,
  `nopol` varchar(255) DEFAULT NULL,
  `tahun` varchar(255) DEFAULT NULL,
  `user_kontrak` varchar(255) DEFAULT NULL,
  `angsuran_per_bulan` bigint(20) NOT NULL DEFAULT 0,
  `jatuh_tempo` tinyint(3) UNSIGNED DEFAULT NULL,
  `periode_mulai` date DEFAULT NULL,
  `periode_selesai` date DEFAULT NULL,
  `personal_account` varchar(255) DEFAULT NULL,
  `sumber_dana_debit` varchar(255) DEFAULT NULL,
  `cara_bayar` varchar(255) DEFAULT NULL,
  `nama_asuransi` varchar(255) DEFAULT NULL,
  `alamat_asuransi` text DEFAULT NULL,
  `nama_marketing` varchar(255) DEFAULT NULL,
  `kontak_marketing` varchar(255) DEFAULT NULL,
  `nama_bengkel` varchar(255) DEFAULT NULL,
  `kontak_bengkel` varchar(255) DEFAULT NULL,
  `bukti` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `data_kontraks`
--

INSERT INTO `data_kontraks` (`id`, `serial_number`, `no_kontrak`, `kendaraan_id`, `mobil`, `nopol`, `tahun`, `user_kontrak`, `angsuran_per_bulan`, `jatuh_tempo`, `periode_mulai`, `periode_selesai`, `personal_account`, `sumber_dana_debit`, `cara_bayar`, `nama_asuransi`, `alamat_asuransi`, `nama_marketing`, `kontak_marketing`, `nama_bengkel`, `kontak_bengkel`, `bukti`, `created_at`, `updated_at`) VALUES
(1, NULL, 'KTR-202608-0001', 18, 'Daihatsu Gran Max', 'AD 1198 SV', '2021', 'Abidai', 1000000, 10, '2026-08-11', '2027-08-11', 'Ipsum tenetur volup', 'Bca PT.APY', 'Debit', 'BCA Insurance', 'dffdfdfdf', 'Rizky Pratama', '081234567890', 'Aplikasi Penjualan', '0303030303', 'uploads/data-kontrak/1786406628_draft_KTR-202608-0006.pdf', '2026-08-11 00:03:48', '2026-08-11 00:44:34'),
(2, NULL, 'KTR-202608-0002', 12, 'Mitsubishi Pajero', 'AB 1132 MP', '2023', 'Minus alias accusamu', 95, 10, '2026-08-11', '2026-08-11', 'Consequat Fugiat la', 'Neque voluptatem aut', 'Iure voluptatem har', 'Excepteur magnam sit', 'Ex modi enim at quia', 'Consequatur eius pla', 'Nisi iusto labore si', 'Nostrum id ut repell', 'Ut sit est volupta', 'uploads/data-kontrak/1786458104_1.png', '2026-08-11 14:21:44', '2026-08-11 14:21:44'),
(3, NULL, 'KTR-202608-0003', 18, 'Daihatsu Gran Max', 'AD 1198 SV', '2021', 'Abidai', 100000, 25, '2026-06-11', '2029-06-11', 'Consequat Fugiat la', 'Bca PT.APY', 'Debit', 'BCA Insurance', 'gyggh', 'hghjlkjh', '9878987', 'ghjkjhghjk', '98768987678', 'uploads/data-kontrak/1786458789_1.png', '2026-08-11 14:33:09', '2026-08-11 14:35:17'),
(5, NULL, 'KTR-202608-0005', 10, 'Honda Jazz', 'AG 1110 KN', '2016', 'Omnis assumenda enim', 23, 5, '2001-05-08', '2019-12-17', 'Qui animi voluptate', 'In qui et assumenda', 'Rerum nobis delectus', 'Quidem rerum explica', 'Eiusmod nulla ea nem', 'Beatae non quo non d', 'Illo lorem adipisci', 'Voluptatem exercitat', 'Elit illo quidem qu', 'uploads/data-kontrak/1786551254_draft_KTR-202608-0006.pdf', '2026-08-12 16:14:14', '2026-08-12 16:14:14'),
(7, 'KTR-202608-0001', 'KTR-202608-0006', 12, 'Mitsubishi Pajero', 'AB 1132 MP', '2023', 'Ipsum sit reprehende', 8, 30, '1994-01-18', '1999-08-21', 'Amet et illo provid', 'Sint quos laboris la', 'Doloribus vitae quae', 'Molestiae error veri', 'Dignissimos autem vo', 'Quo amet ea dolore', 'Omnis Nam proident', 'Culpa aute ullam ea', 'Voluptas sint ut pro', 'uploads/data-kontrak/1786553625_6a7ca519cd4ed.pdf', '2026-08-12 16:53:45', '2026-08-12 16:53:45'),
(8, 'KTR-202608-0002', 'Quisquam unde ea ex -001', 20, 'Suzuki APV', 'AG 1220 UX', '2020', 'Et fugiat laborum re', 800, 7, '2026-08-13', '2029-08-13', 'Nulla quae autem rat', 'Assumenda anim conse', 'Laboriosam ut magni', 'Cupiditate ullamco i', 'Irure sed blanditiis', 'Unde pariatur Iusto', 'Autem incididunt mol', 'Assumenda id maxime', 'Non fugiat ullam qui', 'uploads/data-kontrak/1786557683_6a7cb4f34a378.png', '2026-08-12 18:01:23', '2026-08-12 18:01:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_leasings`
--

CREATE TABLE `data_leasings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data_kontrak_id` bigint(20) UNSIGNED DEFAULT NULL,
  `no_kontrak` varchar(255) DEFAULT NULL,
  `mobil` varchar(255) DEFAULT NULL,
  `tahun` varchar(255) DEFAULT NULL,
  `nopol` varchar(255) DEFAULT NULL,
  `user_leasing` varchar(255) DEFAULT NULL,
  `angsuran_per_bulan` bigint(20) NOT NULL DEFAULT 0,
  `jatuh_tempo` tinyint(3) UNSIGNED DEFAULT NULL,
  `periode_mulai` date DEFAULT NULL,
  `periode_selesai` date DEFAULT NULL,
  `jumlah_cicilan` smallint(5) UNSIGNED DEFAULT NULL,
  `personal_account` varchar(255) DEFAULT NULL,
  `sumber_dana_debit` varchar(255) DEFAULT NULL,
  `cara_bayar` varchar(255) DEFAULT NULL,
  `asuransi_leasing` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `data_leasings`
--

INSERT INTO `data_leasings` (`id`, `data_kontrak_id`, `no_kontrak`, `mobil`, `tahun`, `nopol`, `user_leasing`, `angsuran_per_bulan`, `jatuh_tempo`, `periode_mulai`, `periode_selesai`, `jumlah_cicilan`, `personal_account`, `sumber_dana_debit`, `cara_bayar`, `asuransi_leasing`, `created_at`, `updated_at`) VALUES
(2, 1, 'KTR-202608-0001', 'Daihatsu Gran Max', '2021', 'AD 1198 SV', 'Abidai', 1000000, 10, '2026-08-11', '2027-08-11', NULL, 'Ipsum tenetur volup', 'Bca PT.APY', 'Debit', 'BCA Insurance', '2026-08-11 00:57:26', '2026-08-11 00:57:26'),
(4, 3, 'KTR-202608-0003', 'Daihatsu Gran Max', '2021', 'AD 1198 SV', 'Abidai', 100000, 25, '2026-08-11', '2029-08-11', NULL, 'Consequat Fugiat la', 'Bca PT.APY', 'Debit', 'BCA Insurance', '2026-08-11 14:34:20', '2026-08-11 14:34:20'),
(5, 3, 'KTR-202608-0003', 'Daihatsu Gran Max', '2021', 'AD 1198 SV', 'Abidai', 100000, 25, '2026-06-11', '2029-06-11', NULL, 'Consequat Fugiat la', 'Bca PT.APY', 'Debit', 'BCA Insurance', '2026-08-12 15:54:29', '2026-08-12 15:54:29'),
(7, 5, 'KTR-202608-0005', 'Honda Jazz', '2016', 'AG 1110 KN', 'Omnis assumenda enim', 23, 5, '2001-05-08', '2019-12-17', NULL, 'Qui animi voluptate', 'In qui et assumenda', 'Rerum nobis delectus', 'Quidem rerum explica', '2026-08-12 16:16:09', '2026-08-12 16:16:09'),
(8, 5, 'KTR-202608-0005', 'Honda Jazz', '2016', 'AG 1110 KN', 'Omnis assumenda enim', 23, 5, '2001-05-08', '2019-12-17', NULL, 'Qui animi voluptate', 'In qui et assumenda', 'Rerum nobis delectus', 'Quidem rerum explica', '2026-08-12 16:16:21', '2026-08-12 16:16:21'),
(13, 8, 'Quisquam unde ea ex -001', 'Suzuki APV', '2020', 'AG 1220 UX', 'Et fugiat laborum re', 800, 7, '2026-07-13', '2026-09-13', NULL, 'Nulla quae autem rat', 'Assumenda anim conse', 'Laboriosam ut magni', 'Cupiditate ullamco i', '2026-08-12 19:30:55', '2026-08-12 19:30:55');

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
-- Struktur dari tabel `departemens`
--

CREATE TABLE `departemens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_departemen` varchar(255) NOT NULL,
  `kepala_departemen` varchar(255) NOT NULL,
  `tanggal_dibentuk` date NOT NULL,
  `jumlah_posisi` bigint(20) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status_aktif` enum('Aktif','Non-Aktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `departemens`
--

INSERT INTO `departemens` (`id`, `nama_departemen`, `kepala_departemen`, `tanggal_dibentuk`, `jumlah_posisi`, `keterangan`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Direksi', 'Budi Santoso', '2018-01-02', 3, 'Pimpinan tertinggi perusahaan', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(2, 'HRD', 'Dewi Kusuma', '2018-06-01', 8, 'Mengelola sumber daya manusia', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(3, 'IT', 'Hendra Gunawan', '2019-01-15', 6, 'Pengembangan dan pemeliharaan sistem teknologi', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(4, 'Finance', 'Linda Permata', '2018-06-01', 10, 'Pengelolaan keuangan dan akuntansi', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(5, 'Operasional', 'Dody Kurniawan', '2019-03-01', 15, 'Pengelolaan operasional lapangan', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(6, 'Marketing', 'Sari Dewanti', '2020-02-01', 7, 'Pemasaran dan promosi produk', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(7, 'Sales', 'Benny Kusuma', '2020-04-01', 12, 'Penjualan dan hubungan pelanggan', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(8, 'Legal', 'Putri Wulandari', '2021-01-01', 4, 'Urusan hukum dan kontrak perusahaan', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(9, 'Procurement', 'Bambang Irawan', '2021-06-01', 5, 'Pengadaan barang dan jasa', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(10, 'Maintenance', 'Suryono Hadi', '2019-07-01', 8, 'Pemeliharaan aset dan kendaraan', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(11, 'R&D', 'Indra Lesmana', '2022-01-01', 4, 'Riset dan pengembangan produk', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(12, 'Customer Service', 'Maya Anggraini', '2020-09-01', 6, 'Layanan pelanggan', 'Aktif', '2026-08-10 23:55:36', '2026-08-10 23:55:36');

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
-- Struktur dari tabel `devops`
--

CREATE TABLE `devops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `aplikasi` varchar(255) NOT NULL,
  `tools` varchar(255) NOT NULL,
  `deployment_otomatis` varchar(255) NOT NULL,
  `jadwal_build` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `devops`
--

INSERT INTO `devops` (`id`, `aplikasi`, `tools`, `deployment_otomatis`, `jadwal_build`, `status`, `created_at`, `updated_at`) VALUES
(1, 'API Backend ERP', 'GitHub Actions', 'Ya', 'Setiap push ke branch main', 'Aktif', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'Frontend Dashboard', 'GitLab CI', 'Ya', 'Setiap merge request approved', 'Aktif', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'Mobile App Driver', 'Bitrise', 'Tidak', 'Manual oleh tim mobile', 'Aktif', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'Laporan Keuangan', 'Jenkins', 'Ya', 'Setiap hari pukul 02.00 WIB', 'Nonaktif', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'Website Company Profile', 'GitHub Actions', 'Ya', 'Setiap push ke branch production', 'Aktif', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumentasi_assets`
--

CREATE TABLE `dokumentasi_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `foto_tersimpan` tinyint(1) NOT NULL DEFAULT 0,
  `lokasi_file` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_proyeks`
--

CREATE TABLE `dokumen_proyeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `tipe` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `tanggal_upload` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dokumen_proyeks`
--

INSERT INTO `dokumen_proyeks` (`id`, `proyek`, `nama_dokumen`, `tipe`, `file`, `status`, `tanggal_upload`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'RAB Renovasi Pool', 'XLSX', '-', 'Valid', '2025-12-28', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(2, 'PRJ001', 'Gambar Desain Konstruksi', 'PDF', '-', 'Valid', '2025-12-30', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(3, 'PRJ001', 'Kontrak Kontraktor', 'PDF', '-', 'Valid', '2026-01-02', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(4, 'PRJ002', 'Spesifikasi Teknis Bus', 'PDF', '-', 'Valid', '2026-01-20', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(5, 'PRJ002', 'Purchase Order Bus', 'PDF', '-', 'Draft', '2026-02-05', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(6, 'PRJ003', 'Proposal GPS Monitoring', 'PDF', '-', 'Valid', '2026-01-10', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(7, 'PRJ003', 'Kontrak Vendor GPS', 'PDF', '-', 'Valid', '2026-01-14', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(8, 'PRJ005', 'PKS Layanan Antar Jemput', 'PDF', '-', 'Valid', '2026-02-12', '2026-08-10 23:55:34', '2026-08-10 23:55:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dropshippings`
--

CREATE TABLE `dropshippings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_transaksi` varchar(255) NOT NULL,
  `tipe` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `barang` varchar(255) NOT NULL,
  `jumlah` bigint(20) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `customer_akhir` varchar(255) NOT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dropshippings`
--

INSERT INTO `dropshippings` (`id`, `kode_transaksi`, `tipe`, `vendor`, `barang`, `jumlah`, `satuan`, `customer_akhir`, `tanggal_kirim`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DS-001', 'Regular', 'PT Maju Jaya', 'Spare Part Mesin', 1, 'pcs', 'PT Angin Ribut', '2026-08-03', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'DS-002', 'Express', 'CV Berkah Abadi', 'Oli Mesin', 69, 'liter', 'CV Cahaya Terang', '2026-06-24', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'DS-003', 'Same Day', 'PT Sumber Makmur', 'Ban Kendaraan', 80, 'unit', 'Toko Maju', '2026-07-20', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'DS-004', 'Ekonomi', 'UD Sejahtera', 'Filter Udara', 74, 'set', 'UD Bahagia', '2026-06-13', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'DS-005', 'Regular', 'PT Indo Supplier', 'Aki Kendaraan', 38, 'buah', 'PT Kilat Jaya', '2026-04-13', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(6, 'DS-006', 'Express', 'PT Maju Jaya', 'Kampas Rem', 61, 'pcs', 'CV Sentosa', '2026-05-25', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(7, 'DS-007', 'Same Day', 'CV Berkah Abadi', 'Spare Part Mesin', 93, 'liter', 'PT Angin Ribut', '2026-04-30', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(8, 'DS-008', 'Ekonomi', 'PT Sumber Makmur', 'Oli Mesin', 66, 'unit', 'CV Cahaya Terang', '2026-08-05', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(9, 'DS-009', 'Regular', 'UD Sejahtera', 'Ban Kendaraan', 12, 'set', 'Toko Maju', '2026-05-01', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(10, 'DS-010', 'Express', 'PT Indo Supplier', 'Filter Udara', 38, 'buah', 'UD Bahagia', '2026-06-17', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(11, 'DS-011', 'Same Day', 'PT Maju Jaya', 'Aki Kendaraan', 5, 'pcs', 'PT Kilat Jaya', '2026-06-04', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(12, 'DS-012', 'Ekonomi', 'CV Berkah Abadi', 'Kampas Rem', 64, 'liter', 'CV Sentosa', '2026-08-05', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(13, 'DS-013', 'Regular', 'PT Sumber Makmur', 'Spare Part Mesin', 79, 'unit', 'PT Angin Ribut', '2026-07-01', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(14, 'DS-014', 'Express', 'UD Sejahtera', 'Oli Mesin', 97, 'set', 'CV Cahaya Terang', '2026-08-06', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(15, 'DS-015', 'Same Day', 'PT Indo Supplier', 'Ban Kendaraan', 63, 'buah', 'Toko Maju', '2026-05-08', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(16, 'DS-016', 'Ekonomi', 'PT Maju Jaya', 'Filter Udara', 53, 'pcs', 'UD Bahagia', '2026-04-18', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(17, 'DS-017', 'Regular', 'CV Berkah Abadi', 'Aki Kendaraan', 65, 'liter', 'PT Kilat Jaya', '2026-06-08', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(18, 'DS-018', 'Express', 'PT Sumber Makmur', 'Kampas Rem', 30, 'unit', 'CV Sentosa', '2026-06-25', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(19, 'DS-019', 'Same Day', 'UD Sejahtera', 'Spare Part Mesin', 73, 'set', 'PT Angin Ribut', '2026-08-01', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(20, 'DS-020', 'Ekonomi', 'PT Indo Supplier', 'Oli Mesin', 1, 'buah', 'CV Cahaya Terang', '2026-06-05', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(21, 'DS-021', 'Regular', 'PT Maju Jaya', 'Ban Kendaraan', 30, 'pcs', 'Toko Maju', '2026-08-07', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(22, 'DS-022', 'Express', 'CV Berkah Abadi', 'Filter Udara', 8, 'liter', 'UD Bahagia', '2026-05-13', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(23, 'DS-023', 'Same Day', 'PT Sumber Makmur', 'Aki Kendaraan', 41, 'unit', 'PT Kilat Jaya', '2026-07-22', 'Dikirim', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(24, 'DS-024', 'Ekonomi', 'UD Sejahtera', 'Kampas Rem', 13, 'set', 'CV Sentosa', '2026-07-30', 'Selesai', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(25, 'DS-025', 'Regular', 'PT Indo Supplier', 'Spare Part Mesin', 46, 'buah', 'PT Angin Ribut', '2026-05-17', 'Proses', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

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
(1, '010.000-26.000001', '2026-08-01', 'Keluaran', '01.234.567.8-901.000', 'PT Rental Maju Jaya', 5000000.00, 550000.00, 0.00, 'terbit', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(2, '010.000-26.000002', '2026-08-06', 'Masukan', '09.876.543.2-109.000', 'PT Supplier Sparepart', 3000000.00, 330000.00, 0.00, 'draft', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(3, '010.000-26.000003', '2026-08-09', 'Keluaran', '07.111.222.3-444.000', 'CV Transport Jaya', 7500000.00, 825000.00, 0.00, 'terbit', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_domains`
--

CREATE TABLE `email_domains` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_domain` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `expired_date` date DEFAULT NULL,
  `email_aktif` bigint(20) NOT NULL DEFAULT 0,
  `dns_terkelola` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `email_domains`
--

INSERT INTO `email_domains` (`id`, `nama_domain`, `provider`, `status`, `expired_date`, `email_aktif`, `dns_terkelola`, `created_at`, `updated_at`) VALUES
(1, 'perusahaan.com', 'GoDaddy', 'aktif', '2026-08-15', 120, 1, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'perusahaan.co.id', 'Rumahweb', 'aktif', '2025-11-30', 45, 1, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'old-brand.com', 'Namecheap', 'nonaktif', '2024-03-10', 0, 0, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'app.perusahaan.com', 'Cloudflare', 'aktif', '2027-01-01', 0, 1, '2026-08-10 23:55:35', '2026-08-10 23:55:35');

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
(1, 1, 'GPS Tracker Pro', 'Jl. Teknologi No. 1, Jakarta', 'Marketing 1', '08634441300', 'Bengkel GPS 1', '08138481377', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(2, 1, 'Teltonika', 'Jl. Teknologi No. 2, Jakarta', 'Marketing 2', '08543895841', 'Bengkel GPS 2', '08375664557', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(3, 1, 'Queclink', 'Jl. Teknologi No. 3, Jakarta', 'Marketing 3', '08694675779', 'Bengkel GPS 3', '08535650688', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(4, 1, 'Concox', 'Jl. Teknologi No. 4, Jakarta', 'Marketing 4', '08242198287', 'Bengkel GPS 4', '08411927411', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(5, 1, 'Ruptela', 'Jl. Teknologi No. 5, Jakarta', 'Marketing 5', '08796285284', 'Bengkel GPS 5', '08672928098', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(6, 1, 'Coban', 'Jl. Teknologi No. 6, Jakarta', 'Marketing 6', '08163677214', 'Bengkel GPS 6', '08574782344', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(7, 1, 'Gosafe', 'Jl. Teknologi No. 7, Jakarta', 'Marketing 7', '08251992539', 'Bengkel GPS 7', '08401662526', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(8, 1, 'Jointech', 'Jl. Teknologi No. 8, Jakarta', 'Marketing 8', '08647543511', 'Bengkel GPS 8', '08128199182', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(9, 1, 'Meitrack', 'Jl. Teknologi No. 9, Jakarta', 'Marketing 9', '08848861207', 'Bengkel GPS 9', '08838739660', '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(10, 1, 'Sinotrack', 'Jl. Teknologi No. 10, Jakarta', 'Marketing 10', '08562997065', 'Bengkel GPS 10', '08483884820', '2026-08-10 23:55:27', '2026-08-10 23:55:27');

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
  `durasi_bulan` bigint(20) NOT NULL DEFAULT 0,
  `status_sewa` enum('aktif','habis') NOT NULL DEFAULT 'aktif',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gps_kendaraan`
--

INSERT INTO `gps_kendaraan` (`id`, `kendaraan_id`, `gps_id`, `type`, `status_gps`, `tanggal_pasang`, `tanggal_habis`, `biaya_sewa`, `durasi_bulan`, `status_sewa`, `bukti_bayar`, `keterangan`, `tanggal_bayar`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'OBD', 'nonaktif', '2025-03-11', '2026-02-11', 400000, 11, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(2, 2, 2, 'Hardwire', 'aktif', '2025-11-11', '2027-11-11', 400000, 24, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(3, 3, 3, 'Magnetic', 'aktif', '2025-07-11', '2027-01-11', 500000, 18, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(4, 4, 4, '4G LTE', 'aktif', '2025-04-11', '2027-04-11', 300000, 24, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(5, 5, 5, 'Solar', 'aktif', '2025-12-11', '2026-09-11', 200000, 9, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(6, 6, 6, 'OBD', 'nonaktif', '2025-02-11', '2026-02-11', 300000, 12, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(7, 7, 7, 'Hardwire', 'nonaktif', '2025-05-11', '2026-07-11', 100000, 14, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(8, 8, 8, 'Magnetic', 'nonaktif', '2025-02-11', '2026-02-11', 200000, 12, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(9, 9, 9, '4G LTE', 'nonaktif', '2025-03-11', '2026-02-11', 400000, 11, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(10, 10, 10, 'Solar', 'nonaktif', '2025-03-11', '2026-03-11', 100000, 12, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(11, 11, 1, 'OBD', 'aktif', '2025-04-11', '2026-10-11', 300000, 18, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(12, 12, 2, 'Hardwire', 'aktif', '2026-03-11', '2028-03-11', 500000, 24, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(13, 13, 3, 'Magnetic', 'aktif', '2025-06-11', '2027-03-11', 100000, 21, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(14, 14, 4, '4G LTE', 'aktif', '2026-05-11', '2028-05-11', 400000, 24, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(15, 15, 5, 'Solar', 'nonaktif', '2025-03-11', '2026-02-11', 100000, 11, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(16, 16, 6, 'OBD', 'aktif', '2025-06-11', '2026-09-11', 200000, 15, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(17, 17, 7, 'Hardwire', 'aktif', '2026-04-11', '2027-10-11', 300000, 18, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(18, 18, 8, 'Magnetic', 'nonaktif', '2025-05-11', '2026-02-11', 500000, 9, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(19, 19, 9, '4G LTE', 'aktif', '2025-05-11', '2026-09-11', 100000, 16, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(20, 20, 10, 'Solar', 'aktif', '2026-03-11', '2027-07-11', 400000, 16, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(21, 21, 1, 'OBD', 'aktif', '2025-06-11', '2026-10-11', 400000, 16, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(22, 22, 2, 'Hardwire', 'aktif', '2026-02-11', '2026-09-11', 300000, 7, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(23, 23, 3, 'Magnetic', 'aktif', '2026-04-11', '2027-01-11', 200000, 9, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(24, 24, 4, '4G LTE', 'aktif', '2025-06-11', '2027-03-11', 100000, 21, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(25, 25, 5, 'Solar', 'aktif', '2026-03-11', '2026-09-11', 100000, 6, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(26, 26, 6, 'OBD', 'aktif', '2025-02-11', '2026-09-11', 500000, 19, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(27, 27, 7, 'Hardwire', 'aktif', '2025-06-11', '2026-11-11', 400000, 17, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(28, 28, 8, 'Magnetic', 'aktif', '2025-05-11', '2027-04-11', 300000, 23, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(29, 29, 9, '4G LTE', 'nonaktif', '2025-07-11', '2026-02-11', 500000, 7, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(30, 30, 10, 'Solar', 'aktif', '2026-06-11', '2027-12-11', 400000, 18, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(31, 31, 1, 'OBD', 'nonaktif', '2025-05-11', '2026-08-11', 200000, 15, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(32, 32, 2, 'Hardwire', 'nonaktif', '2025-11-11', '2026-05-11', 200000, 6, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(33, 33, 3, 'Magnetic', 'aktif', '2026-05-11', '2027-06-11', 200000, 13, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(34, 34, 4, '4G LTE', 'aktif', '2025-08-11', '2026-11-11', 300000, 15, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(35, 35, 5, 'Solar', 'nonaktif', '2025-07-11', '2026-03-11', 100000, 8, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(36, 36, 6, 'OBD', 'aktif', '2025-08-11', '2027-01-11', 400000, 17, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(37, 37, 7, 'Hardwire', 'nonaktif', '2025-08-11', '2026-02-11', 500000, 6, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(38, 38, 8, 'Magnetic', 'nonaktif', '2025-02-11', '2026-03-11', 100000, 13, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(39, 39, 9, '4G LTE', 'aktif', '2026-04-11', '2027-07-11', 100000, 15, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(40, 40, 10, 'Solar', 'aktif', '2026-04-11', '2028-02-11', 300000, 22, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(41, 41, 1, 'OBD', 'aktif', '2025-02-11', '2027-01-11', 300000, 23, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(42, 42, 2, 'Hardwire', 'aktif', '2025-09-11', '2026-12-11', 400000, 15, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(43, 43, 3, 'Magnetic', 'aktif', '2026-02-11', '2028-02-11', 400000, 24, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(44, 44, 4, '4G LTE', 'nonaktif', '2025-05-11', '2026-02-11', 500000, 9, 'habis', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(45, 45, 5, 'Solar', 'aktif', '2026-04-11', '2027-10-11', 300000, 18, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(46, 46, 6, 'OBD', 'aktif', '2026-04-11', '2028-03-11', 100000, 23, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(47, 47, 7, 'Hardwire', 'aktif', '2025-05-11', '2026-10-11', 500000, 17, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(48, 48, 8, 'Magnetic', 'aktif', '2025-06-11', '2027-05-11', 100000, 23, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(49, 49, 9, '4G LTE', 'aktif', '2025-12-11', '2027-08-11', 300000, 20, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(50, 50, 10, 'Solar', 'aktif', '2026-03-11', '2027-11-11', 500000, 20, 'aktif', NULL, NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28');

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
  `biaya_sewa` bigint(20) NOT NULL,
  `durasi_bulan` bigint(20) NOT NULL,
  `status_sewa` varchar(255) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hak_hukums`
--

CREATE TABLE `hak_hukums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jenis_akses` varchar(255) NOT NULL,
  `kategori_dokumen` varchar(255) NOT NULL,
  `penerima_akses` varchar(255) NOT NULL,
  `level_hak` varchar(255) NOT NULL,
  `tanggal_akses` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `helpdesk_supports`
--

CREATE TABLE `helpdesk_supports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_tiket` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `departemen` varchar(255) NOT NULL,
  `masalah` text NOT NULL,
  `prioritas` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `teknisi` varchar(255) NOT NULL,
  `waktu_respon` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `helpdesk_supports`
--

INSERT INTO `helpdesk_supports` (`id`, `no_tiket`, `tanggal`, `departemen`, `masalah`, `prioritas`, `status`, `teknisi`, `waktu_respon`, `created_at`, `updated_at`) VALUES
(1, 'TKT-001', '2025-01-10', 'Finance', 'Laptop tidak bisa menyala setelah update Windows', 'High', 'Resolved', 'Doni Prasetyo', '2 jam', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'TKT-002', '2025-01-15', 'HR', 'Email tidak bisa terkirim ke luar domain', 'Medium', 'Open', 'Siti Rahayu', '4 jam', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'TKT-003', '2025-01-20', 'Sales', 'Koneksi VPN terputus saat WFH', 'Critical', 'In Progress', 'Doni Prasetyo', '30 menit', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'TKT-004', '2025-02-01', 'IT', 'Printer di lantai 3 tidak terdeteksi oleh komputer', 'Low', 'Closed', 'Siti Rahayu', '1 hari', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'TKT-005', '2025-02-10', 'Operasional', 'Sistem ERP lambat saat jam kerja puncak', 'High', 'In Progress', 'Doni Prasetyo', '1 jam', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hrd_files`
--

CREATE TABLE `hrd_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `jenis_dokumen` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `hrd_files`
--

INSERT INTO `hrd_files` (`id`, `nama_pegawai`, `nama_file`, `jenis_dokumen`, `file_path`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', 'KTP - Budi Santoso', 'KTP', 'hrd_files/budi_santoso/ktp_budi_santoso.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(2, 'Budi Santoso', 'NPWP - Budi Santoso', 'NPWP', 'hrd_files/budi_santoso/npwp_budi_santoso.pdf', 'Nomor Pokok Wajib Pajak', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(3, 'Budi Santoso', 'Ijazah - Budi Santoso', 'Ijazah', 'hrd_files/budi_santoso/ijazah_budi_santoso.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(4, 'Budi Santoso', 'SK Pengangkatan - Budi Santoso', 'SK Pengangkatan', 'hrd_files/budi_santoso/sk_budi_santoso.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(5, 'Budi Santoso', 'Kontrak Kerja - Budi Santoso', 'Kontrak Kerja', 'hrd_files/budi_santoso/kontrak_budi_santoso.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(6, 'Budi Santoso', 'BPJS Kesehatan - Budi Santoso', 'BPJS Kesehatan', 'hrd_files/budi_santoso/bpjs_kes_budi_santoso.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(7, 'Budi Santoso', 'BPJS TK - Budi Santoso', 'BPJS TK', 'hrd_files/budi_santoso/bpjs_tk_budi_santoso.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(8, 'Siti Rahayu', 'KTP - Siti Rahayu', 'KTP', 'hrd_files/siti_rahayu/ktp_siti_rahayu.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(9, 'Siti Rahayu', 'NPWP - Siti Rahayu', 'NPWP', 'hrd_files/siti_rahayu/npwp_siti_rahayu.pdf', 'Nomor Pokok Wajib Pajak', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(10, 'Siti Rahayu', 'Ijazah - Siti Rahayu', 'Ijazah', 'hrd_files/siti_rahayu/ijazah_siti_rahayu.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(11, 'Agus Wibowo', 'KTP - Agus Wibowo', 'KTP', 'hrd_files/agus_wibowo/ktp_agus_wibowo.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(12, 'Agus Wibowo', 'Ijazah - Agus Wibowo', 'Ijazah', 'hrd_files/agus_wibowo/ijazah_agus_wibowo.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(13, 'Agus Wibowo', 'Kontrak Kerja - Agus Wibowo', 'Kontrak Kerja', 'hrd_files/agus_wibowo/kontrak_agus_wibowo.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(14, 'Agus Wibowo', 'BPJS Kesehatan - Agus Wibowo', 'BPJS Kesehatan', 'hrd_files/agus_wibowo/bpjs_kes_agus_wibowo.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(15, 'Agus Wibowo', 'BPJS TK - Agus Wibowo', 'BPJS TK', 'hrd_files/agus_wibowo/bpjs_tk_agus_wibowo.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(16, 'Dewi Kusuma', 'Ijazah - Dewi Kusuma', 'Ijazah', 'hrd_files/dewi_kusuma/ijazah_dewi_kusuma.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(17, 'Dewi Kusuma', 'Kontrak Kerja - Dewi Kusuma', 'Kontrak Kerja', 'hrd_files/dewi_kusuma/kontrak_dewi_kusuma.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(18, 'Dewi Kusuma', 'BPJS TK - Dewi Kusuma', 'BPJS TK', 'hrd_files/dewi_kusuma/bpjs_tk_dewi_kusuma.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(19, 'Rini Apriani', 'KTP - Rini Apriani', 'KTP', 'hrd_files/rini_apriani/ktp_rini_apriani.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(20, 'Rini Apriani', 'Ijazah - Rini Apriani', 'Ijazah', 'hrd_files/rini_apriani/ijazah_rini_apriani.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(21, 'Rini Apriani', 'SK Pengangkatan - Rini Apriani', 'SK Pengangkatan', 'hrd_files/rini_apriani/sk_rini_apriani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(22, 'Rini Apriani', 'Kontrak Kerja - Rini Apriani', 'Kontrak Kerja', 'hrd_files/rini_apriani/kontrak_rini_apriani.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(23, 'Rini Apriani', 'BPJS Kesehatan - Rini Apriani', 'BPJS Kesehatan', 'hrd_files/rini_apriani/bpjs_kes_rini_apriani.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(24, 'Rini Apriani', 'BPJS TK - Rini Apriani', 'BPJS TK', 'hrd_files/rini_apriani/bpjs_tk_rini_apriani.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(25, 'Eko Prasetyo', 'KTP - Eko Prasetyo', 'KTP', 'hrd_files/eko_prasetyo/ktp_eko_prasetyo.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(26, 'Eko Prasetyo', 'Ijazah - Eko Prasetyo', 'Ijazah', 'hrd_files/eko_prasetyo/ijazah_eko_prasetyo.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(27, 'Eko Prasetyo', 'SK Pengangkatan - Eko Prasetyo', 'SK Pengangkatan', 'hrd_files/eko_prasetyo/sk_eko_prasetyo.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(28, 'Eko Prasetyo', 'BPJS Kesehatan - Eko Prasetyo', 'BPJS Kesehatan', 'hrd_files/eko_prasetyo/bpjs_kes_eko_prasetyo.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(29, 'Hendra Gunawan', 'Ijazah - Hendra Gunawan', 'Ijazah', 'hrd_files/hendra_gunawan/ijazah_hendra_gunawan.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(30, 'Hendra Gunawan', 'SK Pengangkatan - Hendra Gunawan', 'SK Pengangkatan', 'hrd_files/hendra_gunawan/sk_hendra_gunawan.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(31, 'Hendra Gunawan', 'BPJS TK - Hendra Gunawan', 'BPJS TK', 'hrd_files/hendra_gunawan/bpjs_tk_hendra_gunawan.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(32, 'Rizky Fadillah', 'Ijazah - Rizky Fadillah', 'Ijazah', 'hrd_files/rizky_fadillah/ijazah_rizky_fadillah.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(33, 'Rizky Fadillah', 'SK Pengangkatan - Rizky Fadillah', 'SK Pengangkatan', 'hrd_files/rizky_fadillah/sk_rizky_fadillah.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(34, 'Rizky Fadillah', 'Kontrak Kerja - Rizky Fadillah', 'Kontrak Kerja', 'hrd_files/rizky_fadillah/kontrak_rizky_fadillah.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(35, 'Rizky Fadillah', 'BPJS Kesehatan - Rizky Fadillah', 'BPJS Kesehatan', 'hrd_files/rizky_fadillah/bpjs_kes_rizky_fadillah.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(36, 'Yusuf Hidayat', 'Ijazah - Yusuf Hidayat', 'Ijazah', 'hrd_files/yusuf_hidayat/ijazah_yusuf_hidayat.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(37, 'Yusuf Hidayat', 'SK Pengangkatan - Yusuf Hidayat', 'SK Pengangkatan', 'hrd_files/yusuf_hidayat/sk_yusuf_hidayat.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(38, 'Yusuf Hidayat', 'BPJS Kesehatan - Yusuf Hidayat', 'BPJS Kesehatan', 'hrd_files/yusuf_hidayat/bpjs_kes_yusuf_hidayat.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(39, 'Yusuf Hidayat', 'BPJS TK - Yusuf Hidayat', 'BPJS TK', 'hrd_files/yusuf_hidayat/bpjs_tk_yusuf_hidayat.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(40, 'Linda Permata', 'Ijazah - Linda Permata', 'Ijazah', 'hrd_files/linda_permata/ijazah_linda_permata.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(41, 'Linda Permata', 'Kontrak Kerja - Linda Permata', 'Kontrak Kerja', 'hrd_files/linda_permata/kontrak_linda_permata.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(42, 'Linda Permata', 'BPJS Kesehatan - Linda Permata', 'BPJS Kesehatan', 'hrd_files/linda_permata/bpjs_kes_linda_permata.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(43, 'Wahyu Nugroho', 'KTP - Wahyu Nugroho', 'KTP', 'hrd_files/wahyu_nugroho/ktp_wahyu_nugroho.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(44, 'Wahyu Nugroho', 'NPWP - Wahyu Nugroho', 'NPWP', 'hrd_files/wahyu_nugroho/npwp_wahyu_nugroho.pdf', 'Nomor Pokok Wajib Pajak', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(45, 'Wahyu Nugroho', 'Ijazah - Wahyu Nugroho', 'Ijazah', 'hrd_files/wahyu_nugroho/ijazah_wahyu_nugroho.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(46, 'Wahyu Nugroho', 'SK Pengangkatan - Wahyu Nugroho', 'SK Pengangkatan', 'hrd_files/wahyu_nugroho/sk_wahyu_nugroho.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(47, 'Wahyu Nugroho', 'BPJS TK - Wahyu Nugroho', 'BPJS TK', 'hrd_files/wahyu_nugroho/bpjs_tk_wahyu_nugroho.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(48, 'Fitri Handayani', 'NPWP - Fitri Handayani', 'NPWP', 'hrd_files/fitri_handayani/npwp_fitri_handayani.pdf', 'Nomor Pokok Wajib Pajak', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(49, 'Fitri Handayani', 'Ijazah - Fitri Handayani', 'Ijazah', 'hrd_files/fitri_handayani/ijazah_fitri_handayani.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(50, 'Fitri Handayani', 'SK Pengangkatan - Fitri Handayani', 'SK Pengangkatan', 'hrd_files/fitri_handayani/sk_fitri_handayani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(51, 'Fitri Handayani', 'Kontrak Kerja - Fitri Handayani', 'Kontrak Kerja', 'hrd_files/fitri_handayani/kontrak_fitri_handayani.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(52, 'Fitri Handayani', 'BPJS Kesehatan - Fitri Handayani', 'BPJS Kesehatan', 'hrd_files/fitri_handayani/bpjs_kes_fitri_handayani.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(53, 'Fitri Handayani', 'BPJS TK - Fitri Handayani', 'BPJS TK', 'hrd_files/fitri_handayani/bpjs_tk_fitri_handayani.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(54, 'Dody Kurniawan', 'KTP - Dody Kurniawan', 'KTP', 'hrd_files/dody_kurniawan/ktp_dody_kurniawan.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(55, 'Dody Kurniawan', 'NPWP - Dody Kurniawan', 'NPWP', 'hrd_files/dody_kurniawan/npwp_dody_kurniawan.pdf', 'Nomor Pokok Wajib Pajak', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(56, 'Dody Kurniawan', 'Ijazah - Dody Kurniawan', 'Ijazah', 'hrd_files/dody_kurniawan/ijazah_dody_kurniawan.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(57, 'Dody Kurniawan', 'SK Pengangkatan - Dody Kurniawan', 'SK Pengangkatan', 'hrd_files/dody_kurniawan/sk_dody_kurniawan.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(58, 'Dody Kurniawan', 'Kontrak Kerja - Dody Kurniawan', 'Kontrak Kerja', 'hrd_files/dody_kurniawan/kontrak_dody_kurniawan.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(59, 'Dody Kurniawan', 'BPJS Kesehatan - Dody Kurniawan', 'BPJS Kesehatan', 'hrd_files/dody_kurniawan/bpjs_kes_dody_kurniawan.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(60, 'Dody Kurniawan', 'BPJS TK - Dody Kurniawan', 'BPJS TK', 'hrd_files/dody_kurniawan/bpjs_tk_dody_kurniawan.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(61, 'Teguh Santosa', 'KTP - Teguh Santosa', 'KTP', 'hrd_files/teguh_santosa/ktp_teguh_santosa.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(62, 'Teguh Santosa', 'NPWP - Teguh Santosa', 'NPWP', 'hrd_files/teguh_santosa/npwp_teguh_santosa.pdf', 'Nomor Pokok Wajib Pajak', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(63, 'Teguh Santosa', 'Ijazah - Teguh Santosa', 'Ijazah', 'hrd_files/teguh_santosa/ijazah_teguh_santosa.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(64, 'Teguh Santosa', 'SK Pengangkatan - Teguh Santosa', 'SK Pengangkatan', 'hrd_files/teguh_santosa/sk_teguh_santosa.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(65, 'Teguh Santosa', 'Kontrak Kerja - Teguh Santosa', 'Kontrak Kerja', 'hrd_files/teguh_santosa/kontrak_teguh_santosa.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(66, 'Teguh Santosa', 'BPJS Kesehatan - Teguh Santosa', 'BPJS Kesehatan', 'hrd_files/teguh_santosa/bpjs_kes_teguh_santosa.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(67, 'Teguh Santosa', 'BPJS TK - Teguh Santosa', 'BPJS TK', 'hrd_files/teguh_santosa/bpjs_tk_teguh_santosa.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(68, 'Arif Budiman', 'KTP - Arif Budiman', 'KTP', 'hrd_files/arif_budiman/ktp_arif_budiman.pdf', 'Kartu Tanda Penduduk', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(69, 'Arif Budiman', 'NPWP - Arif Budiman', 'NPWP', 'hrd_files/arif_budiman/npwp_arif_budiman.pdf', 'Nomor Pokok Wajib Pajak', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(70, 'Arif Budiman', 'Ijazah - Arif Budiman', 'Ijazah', 'hrd_files/arif_budiman/ijazah_arif_budiman.pdf', 'Ijazah pendidikan terakhir', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(71, 'Arif Budiman', 'Kontrak Kerja - Arif Budiman', 'Kontrak Kerja', 'hrd_files/arif_budiman/kontrak_arif_budiman.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(72, 'Arif Budiman', 'BPJS Kesehatan - Arif Budiman', 'BPJS Kesehatan', 'hrd_files/arif_budiman/bpjs_kes_arif_budiman.pdf', 'Kartu BPJS Kesehatan', '2026-08-10 23:55:42', '2026-08-10 23:55:42'),
(73, 'Arif Budiman', 'BPJS TK - Arif Budiman', 'BPJS TK', 'hrd_files/arif_budiman/bpjs_tk_arif_budiman.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-08-10 23:55:42', '2026-08-10 23:55:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hutang_vendors`
--

CREATE TABLE `hutang_vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_vendor` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `nominal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dibayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sisa` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jatuh_tempo` date NOT NULL,
  `status` enum('lunas','belum_lunas') NOT NULL DEFAULT 'belum_lunas',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `hutang_vendors`
--

INSERT INTO `hutang_vendors` (`id`, `nama_vendor`, `kategori`, `nominal`, `dibayar`, `sisa`, `jatuh_tempo`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'PT Sinar Abadi', 'Sparepart', 5000000.00, 2000000.00, 3000000.00, '2026-08-21', 'belum_lunas', 'Pembelian sparepart mesin', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(2, 'CV Mitra Jaya', 'Service', 2500000.00, 2500000.00, 0.00, '2026-08-06', 'lunas', 'Service kendaraan fleet', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(3, 'PT Otomotif Nusantara', 'Aksesoris', 1200000.00, 500000.00, 700000.00, '2026-08-14', 'belum_lunas', 'Pembelian aksesoris mobil', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(4, 'UD Jaya Mandiri', 'Ban', 3000000.00, 1000000.00, 2000000.00, '2026-08-26', 'belum_lunas', 'Pembelian ban kendaraan', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(5, 'PT Diesel Prima', 'Mesin', 8000000.00, 8000000.00, 0.00, '2026-08-09', 'lunas', 'Perbaikan mesin besar', '2026-08-10 23:55:30', '2026-08-10 23:55:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `induk_assets`
--

CREATE TABLE `induk_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `tanggal_perolehan` date NOT NULL,
  `harga_perolehan` bigint(20) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `pic` varchar(255) NOT NULL,
  `umur_ekonomis` bigint(20) NOT NULL,
  `metode_penyusutan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `induk_proyeks`
--

CREATE TABLE `induk_proyeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `nama_proyek` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `klien_vendor` varchar(255) DEFAULT NULL,
  `pic` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `mulai` date NOT NULL,
  `target_selesai` date NOT NULL,
  `progres` varchar(255) NOT NULL DEFAULT '0%',
  `nilai_proyek` bigint(20) NOT NULL DEFAULT 0,
  `lokasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `induk_proyeks`
--

INSERT INTO `induk_proyeks` (`id`, `kode`, `nama_proyek`, `jenis`, `klien_vendor`, `pic`, `status`, `mulai`, `target_selesai`, `progres`, `nilai_proyek`, `lokasi`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Renovasi Pool Kendaraan Bekasi', 'Internal', '-', 'Rudi', 'Berjalan', '2026-01-01', '2026-03-31', '65%', 450000000, 'Bekasi', NULL, NULL),
(2, 'PRJ002', 'Pengadaan Armada Bus Pariwisata', 'Internal', '-', 'Rina', 'Approved', '2026-02-01', '2026-04-30', '20%', 1500000000, 'Jakarta', NULL, NULL),
(3, 'PRJ003', 'Sistem GPS & Monitoring Armada', 'Internal', 'PT TechMaps', 'Ivan', 'Berjalan', '2026-01-15', '2026-05-15', '45%', 210000000, 'Bandung', NULL, NULL),
(4, 'PRJ004', 'Renovasi Kantor Pusat Tangerang', 'Internal', '-', 'Sari', 'Plan', '2026-04-01', '2026-06-30', '0%', 320000000, 'Tangerang', NULL, NULL),
(5, 'PRJ005', 'Layanan Antar Jemput PT Sinar Abadi', 'Eksternal', 'PT Sinar Abadi', 'Andi', 'Berjalan', '2026-02-15', '2026-12-31', '30%', 850000000, 'Surabaya', NULL, NULL),
(6, 'PRJ006', 'Workshop Pelatihan Driver Safety', 'Internal', '-', 'Budi', 'Selesai', '2025-12-01', '2025-12-31', '100%', 75000000, 'Jakarta', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kontrak_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kendaraan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('perorangan','perusahaan') NOT NULL DEFAULT 'perorangan',
  `invoice_no` text DEFAULT NULL,
  `order_no` text DEFAULT NULL,
  `customer_name` text DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `contact_person` text DEFAULT NULL,
  `telephone` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `pengirim` text DEFAULT NULL,
  `staff` text DEFAULT NULL,
  `name_staff` text DEFAULT NULL,
  `ttd_staff` varchar(255) DEFAULT NULL,
  `direktur` text DEFAULT NULL,
  `name_direktur` text DEFAULT NULL,
  `ttd_direktur` varchar(255) DEFAULT NULL,
  `status` enum('draft','partial','overdue','lunas') DEFAULT NULL,
  `last_email_sent_at` timestamp NULL DEFAULT NULL,
  `payment_status` enum('unpaid','paid','partial') DEFAULT 'unpaid',
  `ppn` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pph` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invoices`
--

INSERT INTO `invoices` (`id`, `penawaran_id`, `kontrak_id`, `kendaraan_id`, `type`, `invoice_no`, `order_no`, `customer_name`, `customer_address`, `contact_person`, `telephone`, `email`, `satuan`, `invoice_date`, `pengirim`, `staff`, `name_staff`, `ttd_staff`, `direktur`, `name_direktur`, `ttd_direktur`, `status`, `last_email_sent_at`, `payment_status`, `ppn`, `pph`, `total`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, 'perorangan', 'INV-2026-0001', 'ORD-0001', 'PT Teknologi Nusantara', 'Jl. Contoh No.1, Jakarta', 'Hendra Gunawan', '081252372684', 'pt.teknologi.nusantara@email.com', 'Bulan', '2026-06-21', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 71500.00, 13000.00, 708500.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(2, 4, 2, NULL, 'perusahaan', 'INV-2026-0002', 'ORD-0002', 'UD Sumber Rejeki', 'Jl. Contoh No.2, Jakarta', 'Dewi Lestari', '081292638510', 'ud.sumber.rejeki@email.com', 'Hari', '2026-08-04', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 396000.00, 72000.00, 3924000.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(3, 5, 3, NULL, 'perusahaan', 'INV-2026-0003', 'ORD-0003', 'PT Logistik Andalan', 'Jl. Contoh No.3, Jakarta', 'Rizal Fahmi', '081264622364', 'pt.logistik.andalan@email.com', 'Tahun', '2026-05-14', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 121000.00, 22000.00, 1199000.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(4, 9, 4, NULL, 'perorangan', 'INV-2026-0004', 'ORD-0004', 'CV Perdana Sejahtera', 'Jl. Contoh No.4, Jakarta', 'Wahyu Nugroho', '081287906229', 'cv.perdana.sejahtera@email.com', 'Bulan', '2026-06-28', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 187000.00, 34000.00, 1853000.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(5, 10, 5, NULL, 'perusahaan', 'INV-2026-0005', 'ORD-0005', 'PT Aneka Niaga Indonesia', 'Jl. Contoh No.5, Jakarta', 'Fitri Handayani', '081280867375', 'pt.aneka.niaga.indonesia@email.com', 'Hari', '2025-11-16', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 313500.00, 57000.00, 3106500.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(6, 11, 6, NULL, 'perusahaan', 'INV-2026-0006', 'ORD-0006', 'PT Maju Jaya Abadi', 'Jl. Contoh No.6, Jakarta', 'Budi Hartono', '081256852352', 'pt.maju.jaya.abadi@email.com', 'Tahun', '2026-05-15', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 71500.00, 13000.00, 708500.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(7, 15, 7, NULL, 'perorangan', 'INV-2026-0007', 'ORD-0007', 'PT Logistik Andalan', 'Jl. Contoh No.7, Jakarta', 'Rizal Fahmi', '081227126303', 'pt.logistik.andalan@email.com', 'Bulan', '2026-06-17', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 247500.00, 45000.00, 2452500.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(8, 16, 8, NULL, 'perusahaan', 'INV-2026-0008', 'ORD-0008', 'CV Karya Utama', 'Jl. Contoh No.8, Jakarta', 'Nur Hidayah', '081268331836', 'cv.karya.utama@email.com', 'Hari', '2025-12-15', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 165000.00, 30000.00, 1635000.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(9, 17, 9, NULL, 'perusahaan', 'INV-2026-0009', 'ORD-0009', 'PT Solusi Transportasi', 'Jl. Contoh No.9, Jakarta', 'Agus Setiawan', '081234099725', 'pt.solusi.transportasi@email.com', 'Tahun', '2026-06-27', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 374000.00, 68000.00, 3706000.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_kendaraans`
--

CREATE TABLE `invoice_kendaraans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_kontraks`
--

CREATE TABLE `invoice_kontraks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `kontrak_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_payments`
--

CREATE TABLE `invoice_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `method` text DEFAULT NULL,
  `transaction_id` text DEFAULT NULL,
  `file_pembayaran` text DEFAULT NULL,
  `file_pembayaran_name` varchar(255) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invoice_payments`
--

INSERT INTO `invoice_payments` (`id`, `invoice_id`, `amount`, `payment_date`, `method`, `transaction_id`, `file_pembayaran`, `file_pembayaran_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1962000.00, '2026-08-11', 'Tunai', 'TXN-E36314E624', NULL, NULL, 'verified', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(2, 2, 1177200.00, '2026-09-18', 'QRIS', 'TXN-F8BC2FBE2C', NULL, NULL, 'pending', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(3, 4, 1853000.00, '2026-07-21', 'QRIS', 'TXN-6E4161B760', NULL, NULL, 'verified', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(4, 6, 354250.00, '2026-05-17', 'Cek/Giro', 'TXN-710FB21AC4', NULL, NULL, 'verified', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(5, 6, 212550.00, '2026-06-11', 'Transfer Bank', 'TXN-D105AE60B3', NULL, NULL, 'pending', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(6, 8, 1635000.00, '2026-01-13', 'Transfer Bank', 'TXN-9536E44A1B', NULL, NULL, 'verified', '2026-08-10 23:55:36', '2026-08-10 23:55:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_penawarans`
--

CREATE TABLE `invoice_penawarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_periodes`
--

CREATE TABLE `invoice_periodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `periode_awal` date DEFAULT NULL,
  `periode_akhir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invoice_periodes`
--

INSERT INTO `invoice_periodes` (`id`, `invoice_id`, `periode_awal`, `periode_akhir`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-06-21', '2026-11-21', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(2, 2, '2026-08-04', '2026-11-04', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(3, 3, '2026-05-14', '2026-07-14', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(4, 4, '2026-06-28', '2026-10-28', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(5, 5, '2025-11-16', '2026-05-16', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(6, 6, '2026-05-15', '2026-06-15', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(7, 7, '2026-06-17', '2026-08-17', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(8, 8, '2025-12-15', '2026-01-15', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(9, 9, '2026-06-27', '2026-11-27', '2026-08-10 23:55:36', '2026-08-10 23:55:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_remaks`
--

CREATE TABLE `invoice_remaks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `periode_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remaks` text DEFAULT NULL,
  `qty` int(10) UNSIGNED DEFAULT 1,
  `price` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invoice_remaks`
--

INSERT INTO `invoice_remaks` (`id`, `invoice_id`, `periode_id`, `remaks`, `qty`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Sewa Kendaraan Operasional', 3, 3496419.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(2, 1, 1, 'Biaya Driver', 1, 3600174.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(3, 2, 2, 'Biaya Driver', 2, 754397.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(4, 3, 3, 'Bahan Bakar', 1, 3196733.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(5, 3, 3, 'Biaya Perawatan', 1, 4226058.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(6, 3, 3, 'Asuransi Kendaraan', 1, 4950719.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(7, 4, 4, 'Biaya Perawatan', 4, 4983658.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(8, 4, 4, 'Asuransi Kendaraan', 4, 3355968.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(9, 4, 4, 'Biaya Administrasi', 2, 2484082.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(10, 5, 5, 'Asuransi Kendaraan', 2, 2929326.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(11, 5, 5, 'Biaya Administrasi', 4, 4684827.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(12, 5, 5, 'Sewa Kendaraan Operasional', 2, 4136315.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(13, 6, 6, 'Biaya Administrasi', 4, 1773708.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(14, 6, 6, 'Sewa Kendaraan Operasional', 4, 756258.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(15, 6, 6, 'Biaya Driver', 1, 1178116.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(16, 7, 7, 'Sewa Kendaraan Operasional', 4, 3688659.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(17, 7, 7, 'Biaya Driver', 2, 2095707.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(18, 8, 8, 'Biaya Driver', 1, 4323072.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(19, 8, 8, 'Bahan Bakar', 2, 1169199.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(20, 9, 9, 'Bahan Bakar', 4, 719389.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(21, 9, 9, 'Biaya Perawatan', 3, 4825374.00, '2026-08-10 23:55:36', '2026-08-10 23:55:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `inv_kontraks`
--

CREATE TABLE `inv_kontraks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `no_kontrak` text DEFAULT NULL,
  `tanggal_kontrak` date DEFAULT NULL,
  `perjanjian_pembayaran` date DEFAULT NULL,
  `durasi_value` int(10) UNSIGNED DEFAULT NULL,
  `durasi_satuan` varchar(10) DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `pihak_pertama` text DEFAULT NULL,
  `contact_pertama` text DEFAULT NULL,
  `pihak_kedua` text DEFAULT NULL,
  `contact_kedua` text DEFAULT NULL,
  `no_ktp_kedua` varchar(16) DEFAULT NULL,
  `email_kedua` varchar(255) DEFAULT NULL,
  `jenis_pelanggan` varchar(255) DEFAULT NULL,
  `alamat_kedua` text DEFAULT NULL,
  `file_kontrak` text DEFAULT NULL,
  `file_persyaratan` text DEFAULT NULL,
  `file_draft` text DEFAULT NULL,
  `ketentuan_asuransi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ketentuan_asuransi`)),
  `pasal_ketentuan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pasal_ketentuan`)),
  `status` enum('dibuat','pending','approved','active','rejected','expired','completed','terminated','selesai-belum lunas') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inv_kontraks`
--

INSERT INTO `inv_kontraks` (`id`, `penawaran_id`, `no_kontrak`, `tanggal_kontrak`, `perjanjian_pembayaran`, `durasi_value`, `durasi_satuan`, `tanggal_selesai`, `pihak_pertama`, `contact_pertama`, `pihak_kedua`, `contact_kedua`, `no_ktp_kedua`, `email_kedua`, `jenis_pelanggan`, `alamat_kedua`, `file_kontrak`, `file_persyaratan`, `file_draft`, `ketentuan_asuransi`, `pasal_ketentuan`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'KTR-0003', '2026-06-16', '2027-05-16', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'PT Teknologi Nusantara', 'Hendra Gunawan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(2, 4, 'KTR-0004', '2026-07-29', '2026-08-29', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'UD Sumber Rejeki', 'Dewi Lestari', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(3, 5, 'KTR-0005', '2026-05-12', '2027-03-12', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(4, 9, 'KTR-0009', '2026-06-22', '2027-02-22', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'CV Perdana Sejahtera', 'Wahyu Nugroho', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(5, 10, 'KTR-0010', '2025-11-14', '2026-08-14', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'terminated', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(6, 11, 'KTR-0011', '2026-05-14', '2027-04-14', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'PT Maju Jaya Abadi', 'Budi Hartono', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(7, 15, 'KTR-0015', '2026-06-12', '2027-06-12', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(8, 16, 'KTR-0016', '2025-12-11', '2026-10-11', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'CV Karya Utama', 'Nur Hidayah', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(9, 17, 'KTR-0017', '2026-06-24', '2026-07-24', NULL, NULL, NULL, 'PT Apyrent Indonesia', '021-12345678', 'PT Solusi Transportasi', 'Agus Setiawan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(10, 22, 'KTR-202608-0001', '2026-08-11', '2026-08-11', 70, 'bulan', '2032-06-11', 'Magni illum deserun', '988', 'Ad fugiat cum et ver', '098768', '7986987890', 'vycexu@mailinator.com', 'perusahaan', 'Eveniet ut nihil qu', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0001.pdf', NULL, '[{\"judul_id\":\"PASAL 2\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"b\",\"en\":\"b\"},{\"id\":\"c\",\"en\":\"c\"}]},{\"judul_id\":\"PASAL 3\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The car rental fee is written on the attachment 1 (one) of the Agreement.\"},{\"id\":\"Sewa mobil yang dibayarkan sudah termasuk :\\r\\n  - Pemeliharaan dan reparasi Kendaraan\\r\\n  - Biaya STNK\\/KIR\\r\\n  - Asuransi All Risk\\r\\n  - Kendaraan Pengganti\\r\\nDan tidak termasuk\\r\\n  - PPN {PPN}%\\r\\n  - PPH 23 {PPH}%\\r\\n  - Bensin, parkir dan tol.\",\"en\":\"The car rental fee paid include:\\r\\n  - Maintenance And Repair\\r\\n  - Motor Vehicle Document (STNK\\/KIR)\\r\\n  - All Risk Insurance\\r\\n  - Replacement car\\r\\nAnd exclude\\r\\n  - Value Added Tax {PPN}%\\r\\n  - Income tax {PPH}%\\r\\n  - Gasoline, parking and toll fee\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\nSTNK\",\"judul_en\":\"ARTICLE 6\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'expired', '2026-08-11 00:20:53', '2026-08-12 10:34:35'),
(11, 15, 'KTR-202608-0002', '2026-08-11', '2026-08-11', 1, 'bulan', '2026-09-11', 'Itaque consequatur', NULL, 'Quod totam enim vero', NULL, NULL, 'finu@mailinator.com', 'perorangan', 'Accusamus fugiat eum', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0002.pdf', NULL, '[{\"judul_id\":\"PASAL 2\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"}]},{\"judul_id\":\"PASAL 3\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\nSTNK\",\"judul_en\":\"ARTICLE 6\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.\",\"en\":\"If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'expired', '2026-08-11 00:27:07', '2026-08-12 10:34:35'),
(12, 22, 'KTR-202608-0003', '2026-08-11', '2026-08-12', 70, 'bulan', '2032-06-11', 'Incididunt quam vel', '0898999999', 'Sapiente irure corpo', '00998888888', '987679098779', 'sesecyhycu@mailinator.com', 'perorangan', 'Officia enim sunt do', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0003.pdf', NULL, '[{\"judul_id\":\"PASAL 2\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"}]},{\"judul_id\":\"PASAL 3\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The car rental fee is written on the attachment 1 (one) of the Agreement.\"},{\"id\":\"Sewa mobil yang dibayarkan sudah termasuk :\\r\\n  - Pemeliharaan dan reparasi Kendaraan\\r\\n  - Biaya STNK\\/KIR\\r\\n  - Asuransi All Risk\\r\\n  - Kendaraan Pengganti\\r\\nDan tidak termasuk\\r\\n  - PPN {PPN}%\\r\\n  - PPH 23 {PPH}%\\r\\n  - Bensin, parkir dan tol.\",\"en\":\"The car rental fee paid include:\\r\\n  - Maintenance And Repair\\r\\n  - Motor Vehicle Document (STNK\\/KIR)\\r\\n  - All Risk Insurance\\r\\n  - Replacement car\\r\\nAnd exclude\\r\\n  - Value Added Tax {PPN}%\\r\\n  - Income tax {PPH}%\\r\\n  - Gasoline, parking and toll fee\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\nSTNK\",\"judul_en\":\"ARTICLE 6\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.\",\"en\":\"If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'pending', '2026-08-11 00:33:14', '2026-08-12 10:34:35');
INSERT INTO `inv_kontraks` (`id`, `penawaran_id`, `no_kontrak`, `tanggal_kontrak`, `perjanjian_pembayaran`, `durasi_value`, `durasi_satuan`, `tanggal_selesai`, `pihak_pertama`, `contact_pertama`, `pihak_kedua`, `contact_kedua`, `no_ktp_kedua`, `email_kedua`, `jenis_pelanggan`, `alamat_kedua`, `file_kontrak`, `file_persyaratan`, `file_draft`, `ketentuan_asuransi`, `pasal_ketentuan`, `status`, `created_at`, `updated_at`) VALUES
(13, 22, 'KTR-202608-0004', '2026-08-11', '2026-08-11', 70, 'bulan', '2032-06-11', 'Et et voluptas labor', '99876790987', 'Vel laboris beatae v', '0098767890', '9876098', 'daziqybute@mailinator.com', 'perusahaan', 'Qui totam officiis s', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0004.pdf', NULL, '[{\"judul_id\":\"PASAL 2\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"}]},{\"judul_id\":\"PASAL 3\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The car rental fee is written on the attachment 1 (one) of the Agreement.\"},{\"id\":\"Sewa mobil yang dibayarkan sudah termasuk :\\r\\n  - Pemeliharaan dan reparasi Kendaraan\\r\\n  - Biaya STNK\\/KIR\\r\\n  - Asuransi All Risk\\r\\n  - Kendaraan Pengganti\\r\\nDan tidak termasuk\\r\\n  - PPN {PPN}%\\r\\n  - PPH 23 {PPH}%\\r\\n  - Bensin, parkir dan tol.\",\"en\":\"The car rental fee paid include:\\r\\n  - Maintenance And Repair\\r\\n  - Motor Vehicle Document (STNK\\/KIR)\\r\\n  - All Risk Insurance\\r\\n  - Replacement car\\r\\nAnd exclude\\r\\n  - Value Added Tax {PPN}%\\r\\n  - Income tax {PPH}%\\r\\n  - Gasoline, parking and toll fee\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\nSTNK\",\"judul_en\":\"ARTICLE 6\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.\",\"en\":\"If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'expired', '2026-08-11 00:36:33', '2026-08-12 10:34:36'),
(14, 15, 'KTR-202608-0005', '2026-08-11', '2026-08-11', 1, 'bulan', '2026-09-11', 'Ducimus qui dolorem', '4123312342', 'Consequatur in sunt', '23452345643', '2345234565432', 'mabab@mailinator.com', 'perusahaan', 'Voluptates est eu i', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0005.pdf', NULL, '[{\"judul_id\":\"PASAL 2\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"},{\"id\":\"a\",\"en\":\"a\"}]},{\"judul_id\":\"PASAL 3\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The car rental fee is written on the attachment 1 (one) of the Agreement.\"},{\"id\":\"Sewa mobil yang dibayarkan sudah termasuk :\\r\\n  - Pemeliharaan dan reparasi Kendaraan\\r\\n  - Biaya STNK\\/KIR\\r\\n  - Asuransi All Risk\\r\\n  - Kendaraan Pengganti\\r\\nDan tidak termasuk\\r\\n  - PPN {PPN}%\\r\\n  - PPH 23 {PPH}%\\r\\n  - Bensin, parkir dan tol.\",\"en\":\"The car rental fee paid include:\\r\\n  - Maintenance And Repair\\r\\n  - Motor Vehicle Document (STNK\\/KIR)\\r\\n  - All Risk Insurance\\r\\n  - Replacement car\\r\\nAnd exclude\\r\\n  - Value Added Tax {PPN}%\\r\\n  - Income tax {PPH}%\\r\\n  - Gasoline, parking and toll fee\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\nSTNK\",\"judul_en\":\"ARTICLE 6\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.\",\"en\":\"If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'expired', '2026-08-11 00:42:49', '2026-08-12 10:34:36'),
(21, 9, 'KTR-202608-0006', '2026-08-09', '2026-08-09', 1, 'bulan', '2026-09-12', 'Ullam nostrum proide', NULL, 'Error expedita facil', NULL, NULL, 'qaqaqala@mailinator.com', 'perorangan', 'Quasi consequatur U', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0006.pdf', NULL, '[{\"judul_id\":\"PASAL 1\\r\\nDATA-DATA KENDARAAN\",\"judul_en\":\"ARTICLE 1\\r\\nVEHICLE DATA\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"PIHAK PERTAMA telah menyerahkan kendaraan untuk disewa oleh PIHAK KEDUA dan PIHAK KEDUA telah menerima kendaraan tersebut yang tertuang dalam berita acara serah terima kendaraan dan check list yang ditandatangani oleh Para Pihak dan merupakan bagian yang tak terpisahkan dari Perjanjian ini. Adapun spesifikasi dan jumlah kendaraan tertuang dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The FIRST PARTY shall provide a car to be rented by the SECOND PARTY and the SECOND PARTY shall receive the said car that is specifically stated in the vehicle handover form and check list form signed by The Parties that constitute and inseparable part of this Agreement. The car specification and quantity are described in detail in attachment 1 (one) of the Agreement.\"}]},{\"judul_id\":\"PASAL 2\\r\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\r\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Mobil tersebut diatas disewa oleh PIHAK KEDUA untuk jangka waktu {DURASI}, mulai {TANGGAL_MULAI} s\\/d {TANGGAL_SELESAI}, terhitung sejak tanggal serah terima kendaraan.\",\"en\":\"The Car mentioned above shall be rented by the Second Party for a period of {DURASI}, commencing {TANGGAL_MULAI_EN} until {TANGGAL_SELESAI_EN}, after the delivery of cars.\"},{\"id\":\"Apabila kendaraan tidak dikembalikan tepat waktu, maka akan dikenakan biaya sewa harian sebesar Rp. 400.000,- \\/ hari.\",\"en\":\"If the vehicle is not returned on time, it will be charged a daily rental of Rp.400.000,- \\/ day\"},{\"id\":\"Pengiriman kendaraan paling lambat 3 minggu setelah diterimanya SPK (Surat Perintah Kerja) atau PO (Purchase Order).\",\"en\":\"Delivery of cars at least 3 (three) weeks after the receipt of Work Authorization or PO (Purchase Order)\"}]},{\"judul_id\":\"PASAL 3\\r\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\r\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The car rental fee is written on the attachment 1 (one) of the Agreement.\"},{\"id\":\"Sewa mobil yang dibayarkan sudah termasuk :\\r\\n  - Pemeliharaan dan reparasi Kendaraan\\r\\n  - Biaya STNK\\/KIR\\r\\n  - Asuransi All Risk\\r\\n  - Kendaraan Pengganti\\r\\nDan tidak termasuk\\r\\n  - PPN {PPN}%\\r\\n  - PPH 23 {PPH}%\\r\\n  - Bensin, parkir dan tol.\",\"en\":\"The car rental fee paid include:\\r\\n  - Maintenance And Repair\\r\\n  - Motor Vehicle Document (STNK\\/KIR)\\r\\n  - All Risk Insurance\\r\\n  - Replacement car\\r\\nAnd exclude\\r\\n  - Value Added Tax {PPN}%\\r\\n  - Income tax {PPH}%\\r\\n  - Gasoline, parking and toll fee\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\r\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\r\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\r\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\r\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\r\\nSTNK\",\"judul_en\":\"ARTICLE 6\\r\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\r\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\r\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\r\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\r\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.\",\"en\":\"If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\r\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\r\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\r\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\r\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'expired', '2026-08-12 10:39:12', '2026-08-12 11:05:48');
INSERT INTO `inv_kontraks` (`id`, `penawaran_id`, `no_kontrak`, `tanggal_kontrak`, `perjanjian_pembayaran`, `durasi_value`, `durasi_satuan`, `tanggal_selesai`, `pihak_pertama`, `contact_pertama`, `pihak_kedua`, `contact_kedua`, `no_ktp_kedua`, `email_kedua`, `jenis_pelanggan`, `alamat_kedua`, `file_kontrak`, `file_persyaratan`, `file_draft`, `ketentuan_asuransi`, `pasal_ketentuan`, `status`, `created_at`, `updated_at`) VALUES
(24, 23, 'KTR-202608-0007', '2026-08-12', '2026-08-12', 42, 'bulan', '2030-02-12', 'Ex commodo asperiore', NULL, 'Aspernatur ut ex ut', NULL, NULL, 'pygevi@mailinator.com', 'perusahaan', 'Non soluta est itaqu', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0007.pdf', NULL, '[{\"judul_id\":\"PASAL 1\\r\\nDATA-DATA KENDARAAN\",\"judul_en\":\"ARTICLE 1\\r\\nVEHICLE DATA\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"PIHAK PERTAMA telah menyerahkan kendaraan untuk disewa oleh PIHAK KEDUA dan PIHAK KEDUA telah menerima kendaraan tersebut yang tertuang dalam berita acara serah terima kendaraan dan check list yang ditandatangani oleh Para Pihak dan merupakan bagian yang tak terpisahkan dari Perjanjian ini. Adapun spesifikasi dan jumlah kendaraan tertuang dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The FIRST PARTY shall provide a car to be rented by the SECOND PARTY and the SECOND PARTY shall receive the said car that is specifically stated in the vehicle handover form and check list form signed by The Parties that constitute and inseparable part of this Agreement. The car specification and quantity are described in detail in attachment 1 (one) of the Agreement.\"}]},{\"judul_id\":\"PASAL 2\\r\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\r\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Mobil tersebut diatas disewa oleh PIHAK KEDUA untuk jangka waktu {DURASI}, mulai {TANGGAL_MULAI} s\\/d {TANGGAL_SELESAI}, terhitung sejak tanggal serah terima kendaraan.\",\"en\":\"The Car mentioned above shall be rented by the Second Party for a period of {DURASI}, commencing {TANGGAL_MULAI_EN} until {TANGGAL_SELESAI_EN}, after the delivery of cars.\"},{\"id\":\"Apabila kendaraan tidak dikembalikan tepat waktu, maka akan dikenakan biaya sewa harian sebesar Rp. 400.000,- \\/ hari.\",\"en\":\"If the vehicle is not returned on time, it will be charged a daily rental of Rp.400.000,- \\/ day\"},{\"id\":\"Pengiriman kendaraan paling lambat 3 minggu setelah diterimanya SPK (Surat Perintah Kerja) atau PO (Purchase Order).\",\"en\":\"Delivery of cars at least 3 (three) weeks after the receipt of Work Authorization or PO (Purchase Order)\"}]},{\"judul_id\":\"PASAL 3\\r\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\r\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The car rental fee is written on the attachment 1 (one) of the Agreement.\"},{\"id\":\"Sewa mobil yang dibayarkan sudah termasuk :\\r\\n  - Pemeliharaan dan reparasi Kendaraan\\r\\n  - Biaya STNK\\/KIR\\r\\n  - Asuransi All Risk\\r\\n  - Kendaraan Pengganti\\r\\nDan tidak termasuk\\r\\n  - PPN {PPN}%\\r\\n  - PPH 23 {PPH}%\\r\\n  - Bensin, parkir dan tol.\",\"en\":\"The car rental fee paid include:\\r\\n  - Maintenance And Repair\\r\\n  - Motor Vehicle Document (STNK\\/KIR)\\r\\n  - All Risk Insurance\\r\\n  - Replacement car\\r\\nAnd exclude\\r\\n  - Value Added Tax {PPN}%\\r\\n  - Income tax {PPH}%\\r\\n  - Gasoline, parking and toll fee\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\r\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\r\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\r\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\r\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\r\\nSTNK\",\"judul_en\":\"ARTICLE 6\\r\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\r\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\r\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\r\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\r\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.\",\"en\":\"If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\r\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\r\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\r\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\r\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'pending', '2026-08-12 14:16:15', '2026-08-12 14:16:16'),
(25, 24, 'KTR-202608-0008', '2026-08-03', '2026-08-03', 1, 'hari', '2026-08-13', 'Similique quae assum', NULL, 'Exercitation sunt mi', NULL, NULL, 'hurub@mailinator.com', 'perusahaan', 'Aliquip explicabo I', NULL, NULL, 'uploads/kontrak/draft_KTR-202608-0008.pdf', NULL, '[{\"judul_id\":\"PASAL 1\\r\\nDATA-DATA KENDARAAN\",\"judul_en\":\"ARTICLE 1\\r\\nVEHICLE DATA\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"PIHAK PERTAMA telah menyerahkan kendaraan untuk disewa oleh PIHAK KEDUA dan PIHAK KEDUA telah menerima kendaraan tersebut yang tertuang dalam berita acara serah terima kendaraan dan check list yang ditandatangani oleh Para Pihak dan merupakan bagian yang tak terpisahkan dari Perjanjian ini. Adapun spesifikasi dan jumlah kendaraan tertuang dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The FIRST PARTY shall provide a car to be rented by the SECOND PARTY and the SECOND PARTY shall receive the said car that is specifically stated in the vehicle handover form and check list form signed by The Parties that constitute and inseparable part of this Agreement. The car specification and quantity are described in detail in attachment 1 (one) of the Agreement.\"}]},{\"judul_id\":\"PASAL 2\\r\\nMASA SEWA\",\"judul_en\":\"ARTICLE 2\\r\\nRENTAL PERIOD\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Mobil tersebut diatas disewa oleh PIHAK KEDUA untuk jangka waktu {DURASI}, mulai {TANGGAL_MULAI} s\\/d {TANGGAL_SELESAI}, terhitung sejak tanggal serah terima kendaraan.\",\"en\":\"The Car mentioned above shall be rented by the Second Party for a period of {DURASI}, commencing {TANGGAL_MULAI_EN} until {TANGGAL_SELESAI_EN}, after the delivery of cars.\"},{\"id\":\"Apabila kendaraan tidak dikembalikan tepat waktu, maka akan dikenakan biaya sewa harian sebesar Rp. 400.000,- \\/ hari.\",\"en\":\"If the vehicle is not returned on time, it will be charged a daily rental of Rp.400.000,- \\/ day\"},{\"id\":\"Pengiriman kendaraan paling lambat 3 minggu setelah diterimanya SPK (Surat Perintah Kerja) atau PO (Purchase Order).\",\"en\":\"Delivery of cars at least 3 (three) weeks after the receipt of Work Authorization or PO (Purchase Order)\"}]},{\"judul_id\":\"PASAL 3\\r\\nHARGA SEWA DAN PEMBAYARAN\",\"judul_en\":\"ARTICLE 3\\r\\nRENTAL PRICE AND PAYMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.\",\"en\":\"The car rental fee is written on the attachment 1 (one) of the Agreement.\"},{\"id\":\"Sewa mobil yang dibayarkan sudah termasuk :\\r\\n  - Pemeliharaan dan reparasi Kendaraan\\r\\n  - Biaya STNK\\/KIR\\r\\n  - Asuransi All Risk\\r\\n  - Kendaraan Pengganti\\r\\nDan tidak termasuk\\r\\n  - PPN {PPN}%\\r\\n  - PPH 23 {PPH}%\\r\\n  - Bensin, parkir dan tol.\",\"en\":\"The car rental fee paid include:\\r\\n  - Maintenance And Repair\\r\\n  - Motor Vehicle Document (STNK\\/KIR)\\r\\n  - All Risk Insurance\\r\\n  - Replacement car\\r\\nAnd exclude\\r\\n  - Value Added Tax {PPN}%\\r\\n  - Income tax {PPH}%\\r\\n  - Gasoline, parking and toll fee\"},{\"id\":\"PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.\",\"en\":\"The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.\"},{\"id\":\"Pembayaran dilakukan melalui:\\r\\n  Nama Bank    : {NAMA_BANK}\\r\\n  No. Rekening : {NO_REKENING}\\r\\n  Atas nama    : {ATAS_NAMA}\",\"en\":\"Payment is done through:\\r\\n  Bank Name    : {NAMA_BANK}\\r\\n  Account No.  : {NO_REKENING}\\r\\n  Account Name : {ATAS_NAMA}\"},{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.\",\"en\":\"If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.\"}]},{\"judul_id\":\"PASAL 4\\r\\nKEWAJIBAN PIHAK PERTAMA\",\"judul_en\":\"ARTICLE 4\\r\\nOBLIGATION OF THE FIRST PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool\\/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi\\/baik selama masa sewa.\",\"en\":\"The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.\"},{\"id\":\"Batas jarak tempuh kendaraan adalah sebesar 2500 km\\/bulan\",\"en\":\"The maximum distance travel in a month is 2500 km\"},{\"id\":\"Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.\",\"en\":\"The mileage can be accumulated and the surcharge will be billed in the end of the rent period.\"},{\"id\":\"Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.\",\"en\":\"If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.\"},{\"id\":\"PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.\",\"en\":\"FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.\"},{\"id\":\"PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.\",\"en\":\"THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.\"},{\"id\":\"PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\\r\\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\\r\\n  b. Dalam hal kecelakaan\\/kehilangan\\/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\\r\\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\\r\\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.\",\"en\":\"The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\\r\\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\\r\\n  b. In the event of damage\\/loss\\/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\\r\\n  c. While undergoing the process of insurance claim for the loss\\/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\\r\\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.\"}]},{\"judul_id\":\"PASAL 5\\r\\nKEWAJIBAN PIHAK KEDUA\",\"judul_en\":\"ARTICLE 5\\r\\nOBLIGATION OF THE SECOND PARTY\",\"tipe\":\"list\",\"poin\":[{\"id\":\"PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.\",\"en\":\"SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.\"},{\"id\":\"Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.\",\"en\":\"During rental period, the car will be parked in SECOND PARTY\'s parking lot.\"},{\"id\":\"Bila terjadi kehilangan\\/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.\",\"en\":\"In the event of loss\\/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.\"},{\"id\":\"PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan\",\"en\":\"The SECOND PARTY is not allowed to re-let and\\/or transfer its right in any nature to any other party including making the car as a guarantee.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.\",\"en\":\"The SECOND PARTY is not allowed to change and\\/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.\"},{\"id\":\"PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\\r\\n  a. Perubahan nama\\/alamat PIHAK KEDUA\\r\\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.\",\"en\":\"The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\\r\\n  a. If the SECOND PARTY changes their name\\/address.\\r\\n  b. In case of any change of car utilization purpose.\"},{\"id\":\"PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap\\/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.\",\"en\":\"The SECOND PARTY is not allowed to use the car in\\/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and\\/or any other purpose besides the domestic and social purposes.\"},{\"id\":\"Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.\",\"en\":\"To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.\"}]},{\"judul_id\":\"PASAL 6\\r\\nSTNK\",\"judul_en\":\"ARTICLE 6\\r\\nMOTOR VEHICLE DOCUMENT (STNK)\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.\",\"en\":\"The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.\"},{\"id\":\"PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.\",\"en\":\"The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.\"}]},{\"judul_id\":\"PASAL 7\\r\\nPENGEMUDI\",\"judul_en\":\"ARTICLE 7\\r\\nDRIVER\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.\",\"en\":\"The Car lease shall be driven by SECOND PARTY\'s driver\"},{\"id\":\"PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.\",\"en\":\"SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.\"}]},{\"judul_id\":\"PASAL 8\\r\\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN\",\"judul_en\":\"ARTICLE 8\\r\\nTERMINATION AND EXTENSION OF AGREEMENT\",\"tipe\":\"list\",\"poin\":[{\"id\":\"Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.\",\"en\":\"If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.\"},{\"id\":\"Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.\",\"en\":\"Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.\"},{\"id\":\"Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan\",\"en\":\"Both PARTIES can terminate\\/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use\"},{\"id\":\"Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.\",\"en\":\"All other matter which is not covered in the articles will be covered in the attachment.\"}]},{\"judul_id\":\"PASAL 9\\r\\nPEMBERITAHUAN\",\"judul_en\":\"ARTICLE 9\\r\\nNOTICE\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\\r\\n\\r\\nPIHAK PERTAMA\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nPIHAK KEDUA\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\",\"en\":\"Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\\r\\n\\r\\nTHE FIRST PARTY\\r\\n{NAMA_PERUSAHAAN}\\r\\n{ALAMAT_PERUSAHAAN}\\r\\nTelp. {TELEPON_PERUSAHAAN}\\r\\nFax.  {FAX_PERUSAHAAN}\\r\\n\\r\\nTHE SECOND PARTY\\r\\n{NAMA_PIHAK_KEDUA}\\r\\n{ALAMAT_PIHAK_KEDUA}\\r\\nHp. {KONTAK_PIHAK_KEDUA}\"}]},{\"judul_id\":\"PASAL 10\\r\\nPENUTUP\",\"judul_en\":\"ARTICLE 10\\r\\nCLOSING PROVISION\",\"tipe\":\"paragraf\",\"poin\":[{\"id\":\"Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.\",\"en\":\"Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.\"},{\"id\":\"Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.\",\"en\":\"In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.\"}]}]', 'expired', '2026-08-12 14:16:52', '2026-08-12 15:06:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `inv_penawarans`
--

CREATE TABLE `inv_penawarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_penawaran` text DEFAULT NULL,
  `tanggal_penawaran` date DEFAULT NULL,
  `kepada` text DEFAULT NULL,
  `up` text DEFAULT NULL,
  `perihal` text DEFAULT NULL,
  `customer_name` text DEFAULT NULL,
  `contact_person` text DEFAULT NULL,
  `email_person` text DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_ktp` varchar(20) DEFAULT NULL,
  `jenis_pelanggan` text DEFAULT NULL,
  `pengirim` text DEFAULT NULL,
  `periode` int(10) UNSIGNED DEFAULT NULL,
  `periode_satuan` varchar(10) NOT NULL DEFAULT 'bulan',
  `staff` text DEFAULT NULL,
  `name_staff` text DEFAULT NULL,
  `direktur` text DEFAULT NULL,
  `name_direktur` text DEFAULT NULL,
  `status` enum('dibuat','pending','approved','active','rejected','expired','completed','terminated') DEFAULT 'pending',
  `total` text DEFAULT NULL,
  `file_penawaran` text DEFAULT NULL,
  `ketentuan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ketentuan`)),
  `file_persyaratan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inv_penawarans`
--

INSERT INTO `inv_penawarans` (`id`, `no_penawaran`, `tanggal_penawaran`, `kepada`, `up`, `perihal`, `customer_name`, `contact_person`, `email_person`, `alamat`, `no_ktp`, `jenis_pelanggan`, `pengirim`, `periode`, `periode_satuan`, `staff`, `name_staff`, `direktur`, `name_direktur`, `status`, `total`, `file_penawaran`, `ketentuan`, `file_persyaratan`, `created_at`, `updated_at`) VALUES
(1, 'PNW-0001', '2026-01-17', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '850000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(2, 'PNW-0002', '2026-04-03', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2850000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(3, 'PNW-0003', '2026-06-05', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '650000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'PNW-0004', '2026-07-15', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '3600000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'PNW-0005', '2026-05-06', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1100000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(6, 'PNW-0006', '2026-06-23', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1100000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(7, 'PNW-0007', '2026-02-23', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '3000000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(8, 'PNW-0008', '2026-05-25', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '500000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(9, 'PNW-0009', '2026-06-19', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '1700000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(10, 'PNW-0010', '2025-11-05', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2850000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(11, 'PNW-0011', '2026-05-07', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '650000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(12, 'PNW-0012', '2026-06-14', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '3600000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(13, 'PNW-0013', '2025-10-21', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1650000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(14, 'PNW-0014', '2026-05-10', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2200000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(15, 'PNW-0015', '2026-05-29', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '2250000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(16, 'PNW-0016', '2025-12-03', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1500000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(17, 'PNW-0017', '2026-06-19', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '3400000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(18, 'PNW-0018', '2026-03-22', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2850000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(19, 'PNW-0019', '2026-05-25', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2600000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(20, 'PNW-0020', '2025-11-17', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', NULL, NULL, NULL, NULL, 'Divisi Sales', 1, 'bulan', 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1200000', NULL, NULL, NULL, '2026-08-10 23:55:35', '2026-08-11 00:09:11'),
(21, 'PNW-202608-0001', '2026-09-05', 'Obcaecati reprehende', 'Odio autem asperiore', 'Tempore ipsum sunt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'hari', 'Direktur Utama (CEO)', 'Danielle King', NULL, NULL, 'pending', '8280000', 'uploads/penawaran/draft_PNW-202608-0001.pdf', NULL, NULL, '2026-08-11 00:11:00', '2026-08-11 00:11:06'),
(22, 'PNW-202608-0002', '2026-08-11', 'Magna cupidatat sint', 'Amet aliqua Non en', 'Molestiae quaerat no', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'hari', 'Wakil Direktur (Vice President)', 'Chaim Pugh', NULL, NULL, 'approved', '7200000', 'uploads/penawaran/1786407484_signed_PNW-202608-0002.pdf', NULL, NULL, '2026-08-11 00:15:03', '2026-08-11 00:18:04'),
(23, 'PNW-202608-0003', '2026-08-11', 'Architecto sit qui', 'Sed ea blanditiis vo', 'Adipisci sed molesti', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'hari', 'Manajer Operasional', 'Illana Colon', NULL, NULL, 'approved', '8160000', 'uploads/penawaran/1786455929_signed_PNW-202608-0003.pdf', NULL, NULL, '2026-08-11 13:41:15', '2026-08-11 13:45:29'),
(24, 'PNW-202608-0004', '2026-08-12', 'PT Maju Jaya', 'Budi Santoso', 'Sewa Kendaraan Operasional 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 'hari', 'Direktur Utama (CEO)', 'Hiru Toty Riawan', NULL, NULL, 'approved', '8160000', 'uploads/penawaran/1786527342_signed_PNW-202608-0004.pdf', '[{\"teks\":\"Harga sewa termasuk PPN 11%, diluar BBM, Tol dan Parkir\",\"sub\":[]},{\"teks\":\"TOP (Term of payment) min. 2 minggu setelah pengiriman kendaraan dan invoice diterima\",\"sub\":[]},{\"teks\":\"Pembatalan kontrak di kenakan penalty sebesar 25% dari sisa nilai kontrak sewa kendaraan\",\"sub\":[]},{\"teks\":\"Klaim own risk untuk kerusakan kendaraan sebesar Rp. 350.000,- \\/ kejadian\",\"sub\":[]},{\"teks\":\"Klaim own risk untuk kehilangan kendaraan sebesar 10% dari nilai pertanggungan\",\"sub\":[]},{\"teks\":\"Harga penawaran ini berlaku selama 2 (dua) minggu sejak tanggal penawaran\",\"sub\":[]},{\"teks\":\"Pengiriman Kendaraan 4 (Empat) minggu setelah PO \\/ SPK diterima\",\"sub\":[]},{\"teks\":\"Harga sudah termasuk :\",\"sub\":[\"Perawatan kendaraan (Maintenance, Sparepart, & Penggantian ban bisa dilakukan di tahun ke-3)\",\"Asuransi All Risk (TJH max. 10 jt)\",\"Kendaraan pengganti sementara\",\"Perpanjangan STNK dan KIR\",\"Perpanjang Garansi Mobildfdfdfdfdf\"]}]', NULL, '2026-08-12 09:32:30', '2026-08-12 09:35:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `inv_penawaran_items`
--

CREATE TABLE `inv_penawaran_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kendaraan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty` int(10) UNSIGNED DEFAULT 1,
  `tahun_unit` text DEFAULT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `durasi` int(10) UNSIGNED DEFAULT 1,
  `satuan_durasi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inv_penawaran_items`
--

INSERT INTO `inv_penawaran_items` (`id`, `penawaran_id`, `kendaraan_id`, `qty`, `tahun_unit`, `price`, `durasi`, `satuan_durasi`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, '2022', 850000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 2, NULL, 1, '2021', 950000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 2, NULL, 1, '2020', 650000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 3, NULL, 1, '2020', 650000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 4, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(6, 4, NULL, 1, '2021', 550000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(7, 5, NULL, 1, '2021', 550000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(8, 5, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(9, 6, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(10, 7, NULL, 1, '2020', 750000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(11, 7, NULL, 1, '2021', 500000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(12, 8, NULL, 1, '2021', 500000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(13, 9, NULL, 1, '2022', 850000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(14, 9, NULL, 1, '2021', 950000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(15, 10, NULL, 1, '2021', 950000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(16, 10, NULL, 1, '2020', 650000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(17, 11, NULL, 1, '2020', 650000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(18, 12, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(19, 12, NULL, 1, '2021', 550000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(20, 13, NULL, 1, '2021', 550000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(21, 13, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(22, 14, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(23, 14, NULL, 1, '2020', 750000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(24, 15, NULL, 1, '2020', 750000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(25, 15, NULL, 1, '2021', 500000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(26, 16, NULL, 1, '2021', 500000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(27, 16, NULL, 1, '2022', 850000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(28, 17, NULL, 1, '2022', 850000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(29, 17, NULL, 1, '2021', 950000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(30, 18, NULL, 1, '2021', 950000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(31, 18, NULL, 1, '2020', 650000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(32, 19, NULL, 1, '2020', 650000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(33, 19, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(34, 20, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(35, 21, 22, 1, '2020', 8280000.00, 1, 'Tahun', '2026-08-11 00:11:00', '2026-08-11 00:11:00'),
(36, 22, 34, 1, '2024', 7200000.00, 70, 'Bulan', '2026-08-11 00:15:03', '2026-08-11 00:15:03'),
(37, 23, 1, 1, '2021', 8160000.00, 42, 'Bulan', '2026-08-11 13:41:15', '2026-08-11 13:41:15'),
(40, 24, 1, 1, '2021', 8160000.00, 1, 'Hari', '2026-08-12 09:34:21', '2026-08-12 09:34:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `inv_summaries`
--

CREATE TABLE `inv_summaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kontrak_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `periode_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `type` text DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `remaining_amount` decimal(15,2) DEFAULT 0.00,
  `payment_status` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inv_summaries`
--

INSERT INTO `inv_summaries` (`id`, `penawaran_id`, `kontrak_id`, `invoice_id`, `periode_count`, `type`, `total_amount`, `paid_amount`, `remaining_amount`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 1, 1, 'perorangan', 708500.00, 0.00, 708500.00, 'unpaid', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(2, 4, 2, 2, 1, 'perusahaan', 3924000.00, 1962000.00, 1962000.00, 'partial', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(3, 5, 3, 3, 1, 'perusahaan', 1199000.00, 0.00, 1199000.00, 'unpaid', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(4, 9, 4, 4, 1, 'perorangan', 1853000.00, 1853000.00, 0.00, 'lunas', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(5, 10, 5, 5, 1, 'perusahaan', 3106500.00, 0.00, 3106500.00, 'unpaid', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(6, 11, 6, 6, 1, 'perusahaan', 708500.00, 354250.00, 354250.00, 'partial', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(7, 15, 7, 7, 1, 'perorangan', 2452500.00, 0.00, 2452500.00, 'unpaid', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(8, 16, 8, 8, 1, 'perusahaan', 1635000.00, 1635000.00, 0.00, 'lunas', '2026-08-10 23:55:36', '2026-08-10 23:55:36'),
(9, 17, 9, 9, 1, 'perusahaan', 3706000.00, 0.00, 3706000.00, 'unpaid', '2026-08-10 23:55:36', '2026-08-10 23:55:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `itasset_management`
--

CREATE TABLE `itasset_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `pengguna` varchar(255) NOT NULL,
  `merek` varchar(255) NOT NULL,
  `tahun_beli` year(4) NOT NULL,
  `status` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `itasset_management`
--

INSERT INTO `itasset_management` (`id`, `kode_aset`, `nama_aset`, `jenis`, `lokasi`, `pengguna`, `merek`, `tahun_beli`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', 'Laptop Dell XPS 15', 'Laptop', 'Ruang IT Lt.2', 'Budi Santoso', 'Dell', '2022', 'Aktif', 'Unit utama developer', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'AST-002', 'HP LaserJet Pro M404', 'Printer', 'Ruang Admin', 'Sari Dewi', 'HP', '2021', 'Aktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'AST-003', 'MacBook Pro M2', 'Laptop', 'Ruang Desain', 'Andi Wijaya', 'Apple', '2023', 'Aktif', 'Untuk tim desain grafis', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'AST-004', 'Monitor LG 27 Inch 4K', 'Monitor', 'Ruang IT Lt.2', 'Rudi Hermawan', 'LG', '2022', 'Aktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'AST-005', 'Cisco Switch 48 Port', 'Network', 'Ruang Server', 'Tim IT', 'Cisco', '2020', 'Rusak', 'Port 12-15 tidak berfungsi', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

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
(1, 1, 'Mobil SUV', '2026-08-10 23:55:25', '2026-08-10 23:55:25'),
(2, 1, 'Mobil MPV', '2026-08-10 23:55:25', '2026-08-10 23:55:25'),
(3, 1, 'Mobil Sedan', '2026-08-10 23:55:25', '2026-08-10 23:55:25'),
(4, 1, 'Pickup', '2026-08-10 23:55:25', '2026-08-10 23:55:25'),
(5, 1, 'Truck', '2026-08-10 23:55:25', '2026-08-10 23:55:25'),
(6, 1, 'Bus Pariwisata', '2026-08-10 23:55:25', '2026-08-10 23:55:25');

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
(1, 'All Risk', 'Menanggung kerusakan ringan dan berat', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(2, 'TLO', 'Total Loss Only', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(3, 'Comprehensive', 'Perlindungan menyeluruh kendaraan', '2026-08-10 23:55:26', '2026-08-10 23:55:26');

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
  `total_jobs` bigint(20) NOT NULL,
  `Pending_jobs` bigint(20) NOT NULL,
  `failed_jobs` bigint(20) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` bigint(20) DEFAULT NULL,
  `created_at` bigint(20) NOT NULL,
  `finished_at` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kampanyes`
--

CREATE TABLE `kampanyes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_kampanye` varchar(255) NOT NULL,
  `nama_kampanye` varchar(255) NOT NULL,
  `tipe_kampanye` varchar(255) NOT NULL,
  `channel` varchar(255) NOT NULL,
  `target_segment` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `subjek_pesan` varchar(255) NOT NULL,
  `isi_pesan_ringkas` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Dijadwalkan',
  `pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kampanyes`
--

INSERT INTO `kampanyes` (`id`, `id_kampanye`, `nama_kampanye`, `tipe_kampanye`, `channel`, `target_segment`, `tanggal_mulai`, `tanggal_akhir`, `subjek_pesan`, `isi_pesan_ringkas`, `status`, `pic`, `created_at`, `updated_at`) VALUES
(1, 'MKT001', 'Promo Rental Akhir Tahun', 'Promosi', 'Email', 'Pelanggan Aktif', '2026-12-25', '2026-12-31', 'Diskon Spesial Akhir Tahun!', 'Dapatkan diskon 20% untuk rental mobil', 'Dijadwalkan', 'Rina Marketing', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'MKT002', 'Re-engagement Campaign', 'Retensi', 'WhatsApp', 'Inaktif 6 Bulan', '2026-08-01', '2026-08-15', 'Kami Merindukan Anda', 'Rental lagi dan dapat voucher', 'Aktif', 'Ahmad Marketing', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `jenis_id` bigint(20) UNSIGNED NOT NULL,
  `nopol` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `foto_masalah` text DEFAULT NULL,
  `catatan_masalah` text DEFAULT NULL,
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
  `kilometer_sekarang` bigint(20) NOT NULL DEFAULT 0,
  `limit_km_service` bigint(20) NOT NULL DEFAULT 0,
  `limit_biaya_bulanan_service` bigint(20) NOT NULL DEFAULT 0,
  `limit_biaya_tahunan_service` bigint(20) NOT NULL DEFAULT 0,
  `km_terakhir_service` bigint(20) NOT NULL DEFAULT 0,
  `tanggal_terakhir_service` date DEFAULT NULL,
  `status_service` enum('aman','service') NOT NULL DEFAULT 'aman',
  `status_kendaraan` enum('tersedia','disewa','service','bermasalah') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `user_id`, `member_id`, `jenis_id`, `nopol`, `foto`, `foto_masalah`, `catatan_masalah`, `nama_pemilik`, `alamat`, `merk`, `tahun_pembuatan`, `tahun_perakitan`, `isi_silinder`, `warna`, `no_rangka`, `no_mesin`, `no_bpkb`, `warna_tnkb`, `bahan_bakar`, `kode_lokasi`, `no_urut_pendaftaran`, `harga_sewa_per_hari`, `harga_sewa_per_jam`, `batas_biaya`, `dokumen`, `masa_berlaku`, `kilometer_sekarang`, `limit_km_service`, `limit_biaya_bulanan_service`, `limit_biaya_tahunan_service`, `km_terakhir_service`, `tanggal_terakhir_service`, `status_service`, `status_kendaraan`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, 'AA 1011 BE', NULL, NULL, NULL, 'Pemilik Kendaraan 1', 'Wonosobo', 'Toyota Avanza', '2021', '2021', '500 CC', 'Hitam', 'NRFC5E3E437C60E', 'NMEAA566D8', 'BPKB000001', 'Hitam', 'Pertalite', 'AA', '001234', 272000, 53000, 972000, NULL, '2028-05-11', 23356, 5000, 0, 0, 19936, '2026-07-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(2, 1, NULL, 2, 'AB 1022 CF', NULL, NULL, NULL, 'Pemilik Kendaraan 2', 'Magelang', 'Toyota Innova', '2023', '2023', '1000 CC', 'Putih', 'NR347D8BAA75F0E', 'NMA267766B', 'BPKB000002', 'Hitam', 'Pertamax', 'AB', '002468', 534000, 32000, 800000, NULL, '2026-11-11', 63063, 5000, 0, 0, 59208, '2026-02-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(3, 1, NULL, 3, 'AD 1033 DG', NULL, NULL, NULL, 'Pemilik Kendaraan 3', 'Purworejo', 'Toyota Rush', '2021', '2021', '500 CC', 'Silver', 'NR05BCAB37A5B5E', 'NM7DA12124', 'BPKB000003', 'Hitam', 'Solar', 'AD', '003702', 585000, 67000, 1696000, NULL, '2027-04-11', 96216, 5000, 0, 0, 93630, '2025-08-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(4, 1, NULL, 1, 'AE 1044 EH', NULL, NULL, NULL, 'Pemilik Kendaraan 4', 'Kebumen', 'Toyota Fortuner', '2022', '2022', '1000 CC', 'Merah', 'NRD9F1155CE77F7', 'NM96142E2A', 'BPKB000004', 'Hitam', 'Pertamax Turbo', 'AE', '004936', 253000, 58000, 1409000, NULL, '2027-09-11', 113717, 5000, 0, 0, 112155, '2026-02-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(5, 1, NULL, 2, 'AG 1055 FI', NULL, NULL, NULL, 'Pemilik Kendaraan 5', 'Purwokerto', 'Toyota Calya', '2019', '2019', '500 CC', 'Biru', 'NRC2DBC1270E648', 'NMCABF6A7D', 'BPKB000005', 'Hitam', 'Pertalite', 'AG', '006170', 600000, 31000, 1805000, NULL, '2026-06-11', 25990, 5000, 0, 0, 23204, '2025-08-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(6, 1, NULL, 3, 'AA 1066 GJ', NULL, NULL, NULL, 'Pemilik Kendaraan 6', 'Temanggung', 'Honda Brio', '2024', '2024', '1000 CC', 'Abu-abu', 'NRD5527EB4A669D', 'NM787F80E0', 'BPKB000006', 'Hitam', 'Pertamax', 'AA', '007404', 345000, 47000, 1014000, NULL, '2027-02-11', 59865, 5000, 0, 0, 54185, '2026-04-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(7, 1, NULL, 1, 'AB 1077 HK', NULL, NULL, NULL, 'Pemilik Kendaraan 7', 'Kendal', 'Honda Mobilio', '2023', '2023', '1500 CC', 'Coklat', 'NRF6B081BFC804D', 'NM238F09D0', 'BPKB000007', 'Hitam', 'Solar', 'AB', '008638', 302000, 32000, 1490000, NULL, '2026-06-11', 58517, 5000, 0, 0, 53956, '2025-08-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(8, 1, NULL, 2, 'AD 1088 IL', NULL, NULL, NULL, 'Pemilik Kendaraan 8', 'Batang', 'Honda HR-V', '2022', '2022', '1500 CC', 'Kuning', 'NRC71D2E264F12E', 'NMB3E50BB9', 'BPKB000008', 'Hitam', 'Pertamax Turbo', 'AD', '009872', 387000, 77000, 1077000, NULL, '2027-06-11', 103184, 5000, 0, 0, 99178, '2026-01-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(9, 1, NULL, 3, 'AE 1099 JM', NULL, NULL, NULL, 'Pemilik Kendaraan 9', 'Wonosobo', 'Honda CR-V', '2017', '2017', '1000 CC', 'Hitam', 'NR28CB98ADDC49A', 'NM95D7A4AD', 'BPKB000009', 'Hitam', 'Pertalite', 'AE', '011106', 516000, 79000, 1471000, NULL, '2027-09-11', 5653, 5000, 0, 0, 2240, '2026-02-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(10, 1, NULL, 1, 'AG 1110 KN', NULL, NULL, NULL, 'Pemilik Kendaraan 10', 'Magelang', 'Honda Jazz', '2016', '2016', '1000 CC', 'Putih', 'NRC7018438D4E08', 'NMA21DC861', 'BPKB000010', 'Hitam', 'Pertamax', 'AG', '012340', 201000, 33000, 844000, NULL, '2027-07-11', 67486, 5000, 0, 0, 60067, '2025-08-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(11, 1, NULL, 2, 'AA 1121 LO', NULL, NULL, NULL, 'Pemilik Kendaraan 11', 'Purworejo', 'Mitsubishi Xpander', '2015', '2015', '1500 CC', 'Silver', 'NR101ED65635098', 'NMB836A727', 'BPKB000011', 'Hitam', 'Solar', 'AA', '013574', 426000, 36000, 1163000, NULL, '2027-04-11', 57816, 5000, 0, 0, 55588, '2026-02-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-11 00:08:52'),
(12, 1, NULL, 3, 'AB 1132 MP', NULL, NULL, NULL, 'Pemilik Kendaraan 12', 'Kebumen', 'Mitsubishi Pajero', '2023', '2023', '500 CC', 'Merah', 'NRBA899300E453F', 'NMFCA534D3', 'BPKB000012', 'Hitam', 'Pertamax Turbo', 'AB', '014808', 337000, 45000, 1970000, NULL, '2027-11-11', 25465, 5000, 0, 0, 18944, '2026-02-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(13, 1, NULL, 1, 'AD 1143 NQ', NULL, NULL, NULL, 'Pemilik Kendaraan 13', 'Purwokerto', 'Mitsubishi L300', '2023', '2023', '1000 CC', 'Biru', 'NR28C8D02298AFE', 'NM18E9181C', 'BPKB000013', 'Hitam', 'Pertalite', 'AD', '016042', 533000, 57000, 928000, NULL, '2026-08-11', 79883, 5000, 0, 0, 72346, '2025-11-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(14, 1, NULL, 2, 'AE 1154 OR', NULL, NULL, NULL, 'Pemilik Kendaraan 14', 'Temanggung', 'Mitsubishi Outlander', '2018', '2018', '1500 CC', 'Abu-abu', 'NR71E1B33FE3385', 'NM65CE77D8', 'BPKB000014', 'Hitam', 'Pertamax', 'AE', '017276', 594000, 64000, 654000, NULL, '2026-08-11', 52853, 5000, 0, 0, 47525, '2026-02-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(15, 1, NULL, 3, 'AG 1165 PS', NULL, NULL, NULL, 'Pemilik Kendaraan 15', 'Kendal', 'Daihatsu Xenia', '2020', '2020', '1000 CC', 'Coklat', 'NR0188959EBD4CF', 'NMD99046EE', 'BPKB000015', 'Hitam', 'Solar', 'AG', '018510', 547000, 30000, 1711000, NULL, '2027-08-11', 60499, 5000, 0, 0, 54858, '2026-03-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-11 14:18:15'),
(16, 1, NULL, 1, 'AA 1176 QT', NULL, NULL, NULL, 'Pemilik Kendaraan 16', 'Batang', 'Daihatsu Terios', '2017', '2017', '500 CC', 'Kuning', 'NR4CFC5AB2A1B24', 'NM66FA2F56', 'BPKB000016', 'Hitam', 'Pertamax Turbo', 'AA', '019744', 588000, 47000, 596000, NULL, '2028-03-11', 114606, 5000, 0, 0, 110993, '2026-05-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(17, 1, NULL, 2, 'AB 1187 RU', NULL, NULL, NULL, 'Pemilik Kendaraan 17', 'Wonosobo', 'Daihatsu Sigra', '2021', '2021', '1500 CC', 'Hitam', 'NRB690B147D29AD', 'NMED999685', 'BPKB000017', 'Hitam', 'Pertalite', 'AB', '020978', 256000, 54000, 1479000, NULL, '2028-08-11', 38467, 5000, 0, 0, 33993, '2026-04-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(18, 1, NULL, 3, 'AD 1198 SV', NULL, NULL, NULL, 'Pemilik Kendaraan 18', 'Magelang', 'Daihatsu Gran Max', '2021', '2021', '1500 CC', 'Putih', 'NR89F8444CB9A7D', 'NM8CD1481B', 'BPKB000018', 'Hitam', 'Pertamax', 'AD', '022212', 410000, 73000, 766000, NULL, '2027-07-11', 17212, 5000, 0, 0, 12708, '2025-10-11', 'service', 'tersedia', '2026-08-10 23:55:26', '2026-08-12 15:41:27'),
(19, 1, NULL, 1, 'AE 1209 TW', NULL, NULL, NULL, 'Pemilik Kendaraan 19', 'Purworejo', 'Suzuki Ertiga', '2016', '2016', '500 CC', 'Silver', 'NR67D11ED0217CF', 'NM0AAE1062', 'BPKB000019', 'Hitam', 'Solar', 'AE', '023446', 238000, 37000, 830000, NULL, '2027-08-11', 91781, 5000, 0, 0, 86464, '2025-11-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(20, 1, NULL, 2, 'AG 1220 UX', NULL, NULL, NULL, 'Pemilik Kendaraan 20', 'Kebumen', 'Suzuki APV', '2020', '2020', '1000 CC', 'Merah', 'NR4736A85D494F9', 'NM8C8EA669', 'BPKB000020', 'Hitam', 'Pertamax Turbo', 'AG', '024680', 319000, 59000, 698000, NULL, '2027-04-11', 21858, 5000, 0, 0, 15201, '2026-05-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(21, 1, NULL, 3, 'AA 1231 VY', NULL, NULL, NULL, 'Pemilik Kendaraan 21', 'Purwokerto', 'Suzuki Jimny', '2018', '2018', '500 CC', 'Biru', 'NR6EBC3372CE7C9', 'NMB0B07747', 'BPKB000021', 'Hitam', 'Pertalite', 'AA', '025914', 511000, 48000, 1396000, NULL, '2028-06-11', 61342, 5000, 0, 0, 55109, '2026-04-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(22, 1, NULL, 1, 'AB 1242 WZ', NULL, NULL, NULL, 'Pemilik Kendaraan 22', 'Temanggung', 'Suzuki Carry', '2020', '2020', '1500 CC', 'Abu-abu', 'NR62F120141685C', 'NM6AA28811', 'BPKB000022', 'Hitam', 'Pertamax', 'AB', '027148', 276000, 52000, 1729000, NULL, '2027-11-11', 38737, 5000, 0, 0, 36160, '2025-12-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(23, 1, NULL, 2, 'AD 1253 XA', NULL, NULL, NULL, 'Pemilik Kendaraan 23', 'Kendal', 'Nissan X-Trail', '2023', '2023', '500 CC', 'Coklat', 'NRC5153735B748E', 'NMAB3A9544', 'BPKB000023', 'Hitam', 'Solar', 'AD', '028382', 592000, 53000, 1328000, NULL, '2028-08-11', 71970, 5000, 0, 0, 65374, '2026-05-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(24, 1, NULL, 3, 'AE 1264 YB', NULL, NULL, NULL, 'Pemilik Kendaraan 24', 'Batang', 'Nissan Livina', '2019', '2019', '500 CC', 'Kuning', 'NR249973A0D8662', 'NMADFDF0BD', 'BPKB000024', 'Hitam', 'Pertamax Turbo', 'AE', '029616', 355000, 74000, 1637000, NULL, '2026-09-11', 67279, 5000, 0, 0, 63009, '2026-05-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(25, 1, NULL, 1, 'AG 1275 ZC', NULL, NULL, NULL, 'Pemilik Kendaraan 25', 'Wonosobo', 'Nissan Terra', '2015', '2015', '1000 CC', 'Hitam', 'NR1233A73D59FC1', 'NM3B083C41', 'BPKB000025', 'Hitam', 'Pertalite', 'AG', '030850', 221000, 63000, 1555000, NULL, '2026-10-11', 5181, 5000, 0, 0, 2965, '2025-12-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(26, 1, NULL, 2, 'AA 1286 AD', NULL, NULL, NULL, 'Pemilik Kendaraan 26', 'Magelang', 'Isuzu Panther', '2024', '2024', '1500 CC', 'Putih', 'NR8B7FE74809E8F', 'NM11A757F7', 'BPKB000026', 'Hitam', 'Pertamax', 'AA', '032084', 251000, 47000, 1637000, NULL, '2027-01-11', 44111, 5000, 0, 0, 40111, '2026-04-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(27, 1, NULL, 3, 'AB 1297 BE', NULL, NULL, NULL, 'Pemilik Kendaraan 27', 'Purworejo', 'Isuzu D-Max', '2020', '2020', '500 CC', 'Silver', 'NRB559A1B082827', 'NM2BA7C96A', 'BPKB000027', 'Hitam', 'Solar', 'AB', '033318', 561000, 63000, 1577000, NULL, '2027-12-11', 40475, 5000, 0, 0, 36579, '2025-08-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(28, 1, NULL, 1, 'AD 1308 CF', NULL, NULL, NULL, 'Pemilik Kendaraan 28', 'Kebumen', 'Isuzu Elf', '2018', '2018', '500 CC', 'Merah', 'NR8868E52D15081', 'NMF004F89A', 'BPKB000028', 'Hitam', 'Pertamax Turbo', 'AD', '034552', 286000, 45000, 508000, NULL, '2028-07-11', 56293, 5000, 0, 0, 48993, '2026-04-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(29, 1, NULL, 2, 'AE 1319 DG', NULL, NULL, NULL, 'Pemilik Kendaraan 29', 'Purwokerto', 'Wuling Almaz', '2020', '2020', '1500 CC', 'Biru', 'NR9B965F12D9041', 'NM09AD4CEA', 'BPKB000029', 'Hitam', 'Pertalite', 'AE', '035786', 512000, 77000, 1685000, NULL, '2027-04-11', 19675, 5000, 0, 0, 17258, '2026-01-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(30, 1, NULL, 3, 'AG 1330 EH', NULL, NULL, NULL, 'Pemilik Kendaraan 30', 'Temanggung', 'Wuling Air ev', '2015', '2015', '500 CC', 'Abu-abu', 'NR5238145D8FA5E', 'NMB7E2C1AB', 'BPKB000030', 'Hitam', 'Pertamax', 'AG', '037020', 458000, 57000, 2000000, NULL, '2028-03-11', 60578, 5000, 0, 0, 53222, '2026-01-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(31, 1, NULL, 1, 'AA 1341 FI', NULL, NULL, NULL, 'Pemilik Kendaraan 31', 'Kendal', 'Toyota Avanza', '2016', '2016', '1500 CC', 'Coklat', 'NR8551649018B27', 'NMC0EF36C6', 'BPKB000031', 'Hitam', 'Solar', 'AA', '038254', 342000, 50000, 1968000, NULL, '2026-08-11', 43866, 5000, 0, 0, 39888, '2026-05-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(32, 1, NULL, 2, 'AB 1352 GJ', NULL, NULL, NULL, 'Pemilik Kendaraan 32', 'Batang', 'Toyota Innova', '2015', '2015', '1000 CC', 'Kuning', 'NR83DC5F76D3D1F', 'NM1A73548C', 'BPKB000032', 'Hitam', 'Pertamax Turbo', 'AB', '039488', 507000, 70000, 1887000, NULL, '2028-05-11', 24593, 5000, 0, 0, 22896, '2026-03-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(33, 1, NULL, 3, 'AD 1363 HK', NULL, NULL, NULL, 'Pemilik Kendaraan 33', 'Wonosobo', 'Toyota Rush', '2022', '2022', '1000 CC', 'Hitam', 'NR7D8A942BB2B06', 'NMD7DE1C30', 'BPKB000033', 'Hitam', 'Pertalite', 'AD', '040722', 369000, 46000, 884000, NULL, '2027-08-11', 91780, 5000, 0, 0, 85968, '2026-01-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(34, 1, NULL, 1, 'AE 1374 IL', NULL, NULL, NULL, 'Pemilik Kendaraan 34', 'Magelang', 'Toyota Fortuner', '2024', '2024', '500 CC', 'Putih', 'NR6BB7CA4622D0E', 'NMBCAF1E4D', 'BPKB000034', 'Hitam', 'Pertamax', 'AE', '041956', 240000, 77000, 873000, NULL, '2027-08-11', 93215, 5000, 0, 0, 87881, '2025-11-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(35, 1, NULL, 2, 'AG 1385 JM', NULL, NULL, NULL, 'Pemilik Kendaraan 35', 'Purworejo', 'Toyota Calya', '2023', '2023', '500 CC', 'Silver', 'NR2B41A619C9BF1', 'NM796B7335', 'BPKB000035', 'Hitam', 'Solar', 'AG', '043190', 418000, 50000, 575000, NULL, '2027-08-11', 22150, 5000, 0, 0, 15582, '2026-06-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(36, 1, NULL, 3, 'AA 1396 KN', NULL, NULL, NULL, 'Pemilik Kendaraan 36', 'Kebumen', 'Honda Brio', '2017', '2017', '500 CC', 'Merah', 'NREF70459230C5C', 'NM5B1C5010', 'BPKB000036', 'Hitam', 'Pertamax Turbo', 'AA', '044424', 439000, 68000, 869000, NULL, '2026-05-11', 42587, 5000, 0, 0, 41423, '2025-11-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(37, 1, NULL, 1, 'AB 1407 LO', NULL, NULL, NULL, 'Pemilik Kendaraan 37', 'Purwokerto', 'Honda Mobilio', '2019', '2019', '500 CC', 'Biru', 'NRB4F6268FE8E3C', 'NM66823A28', 'BPKB000037', 'Hitam', 'Pertalite', 'AB', '045658', 256000, 32000, 1546000, NULL, '2026-12-11', 69841, 5000, 0, 0, 63925, '2026-03-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(38, 1, NULL, 2, 'AD 1418 MP', NULL, NULL, NULL, 'Pemilik Kendaraan 38', 'Temanggung', 'Honda HR-V', '2015', '2015', '1500 CC', 'Abu-abu', 'NR09F7566B4E018', 'NM6D3C7CF5', 'BPKB000038', 'Hitam', 'Pertamax', 'AD', '046892', 315000, 57000, 1282000, NULL, '2026-08-11', 51279, 5000, 0, 0, 43453, '2025-08-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(39, 1, NULL, 3, 'AE 1429 NQ', NULL, NULL, NULL, 'Pemilik Kendaraan 39', 'Kendal', 'Honda CR-V', '2021', '2021', '500 CC', 'Coklat', 'NR2DFC28E9E22E4', 'NMBE1C5CD3', 'BPKB000039', 'Hitam', 'Solar', 'AE', '048126', 216000, 71000, 657000, NULL, '2027-04-11', 93207, 5000, 0, 0, 89799, '2026-01-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(40, 1, NULL, 1, 'AG 1440 OR', NULL, NULL, NULL, 'Pemilik Kendaraan 40', 'Batang', 'Honda Jazz', '2015', '2015', '500 CC', 'Kuning', 'NRD21A1A31A7898', 'NM21C25BDA', 'BPKB000040', 'Hitam', 'Pertamax Turbo', 'AG', '049360', 589000, 51000, 1150000, NULL, '2028-03-11', 11134, 5000, 0, 0, 9731, '2026-06-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(41, 1, NULL, 2, 'AA 1451 PS', NULL, NULL, NULL, 'Pemilik Kendaraan 41', 'Wonosobo', 'Mitsubishi Xpander', '2018', '2018', '500 CC', 'Hitam', 'NR5BB2C9B97AB51', 'NM20FFAE4E', 'BPKB000041', 'Hitam', 'Pertalite', 'AA', '050594', 321000, 71000, 1476000, NULL, '2027-05-11', 90699, 5000, 0, 0, 88925, '2025-09-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(42, 1, NULL, 3, 'AB 1462 QT', NULL, NULL, NULL, 'Pemilik Kendaraan 42', 'Magelang', 'Mitsubishi Pajero', '2019', '2019', '500 CC', 'Putih', 'NR4DB76BF66AFB5', 'NM68D57762', 'BPKB000042', 'Hitam', 'Pertamax', 'AB', '051828', 213000, 75000, 1085000, NULL, '2028-07-11', 5816, 5000, 0, 0, 0, '2026-01-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(43, 1, NULL, 1, 'AD 1473 RU', NULL, NULL, NULL, 'Pemilik Kendaraan 43', 'Purworejo', 'Mitsubishi L300', '2016', '2016', '500 CC', 'Silver', 'NR0DC971245341E', 'NMBA4C4814', 'BPKB000043', 'Hitam', 'Solar', 'AD', '053062', 203000, 47000, 608000, NULL, '2028-03-11', 42686, 5000, 0, 0, 39376, '2026-01-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(44, 1, NULL, 2, 'AE 1484 SV', NULL, NULL, NULL, 'Pemilik Kendaraan 44', 'Kebumen', 'Mitsubishi Outlander', '2023', '2023', '500 CC', 'Merah', 'NR476664C2FBE1E', 'NMB8004991', 'BPKB000044', 'Hitam', 'Pertamax Turbo', 'AE', '054296', 406000, 45000, 1648000, NULL, '2027-05-11', 35287, 5000, 0, 0, 30335, '2026-04-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(45, 1, NULL, 3, 'AG 1495 TW', NULL, NULL, NULL, 'Pemilik Kendaraan 45', 'Purwokerto', 'Daihatsu Xenia', '2017', '2017', '1500 CC', 'Biru', 'NR7627556EB210F', 'NMF58C9AE1', 'BPKB000045', 'Hitam', 'Pertalite', 'AG', '055530', 473000, 38000, 705000, NULL, '2027-02-11', 16105, 5000, 0, 0, 11178, '2026-05-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(46, 1, NULL, 1, 'AA 1506 UX', NULL, NULL, NULL, 'Pemilik Kendaraan 46', 'Temanggung', 'Daihatsu Terios', '2024', '2024', '500 CC', 'Abu-abu', 'NR10E297C43FFA9', 'NM06A94CA6', 'BPKB000046', 'Hitam', 'Pertamax', 'AA', '056764', 429000, 32000, 610000, NULL, '2027-09-11', 97682, 5000, 0, 0, 90800, '2026-01-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(47, 1, NULL, 2, 'AB 1517 VY', NULL, NULL, NULL, 'Pemilik Kendaraan 47', 'Kendal', 'Daihatsu Sigra', '2019', '2019', '1000 CC', 'Coklat', 'NRF3E8AC22132E1', 'NM16A92731', 'BPKB000047', 'Hitam', 'Solar', 'AB', '057998', 510000, 45000, 1775000, NULL, '2027-12-11', 107262, 5000, 0, 0, 103700, '2025-12-11', 'aman', 'service', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(48, 1, NULL, 3, 'AD 1528 WZ', NULL, NULL, NULL, 'Pemilik Kendaraan 48', 'Batang', 'Daihatsu Gran Max', '2017', '2017', '500 CC', 'Kuning', 'NR8F3EB52F6AA35', 'NM161F258D', 'BPKB000048', 'Hitam', 'Pertamax Turbo', 'AD', '059232', 453000, 46000, 1535000, NULL, '2027-11-11', 77536, 5000, 0, 0, 75406, '2025-11-11', 'service', 'bermasalah', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(49, 1, NULL, 1, 'AE 1539 XA', NULL, NULL, NULL, 'Pemilik Kendaraan 49', 'Wonosobo', 'Suzuki Ertiga', '2017', '2017', '1500 CC', 'Hitam', 'NREDD43B2F51720', 'NMDE6CB379', 'BPKB000049', 'Hitam', 'Pertalite', 'AE', '060466', 513000, 63000, 755000, NULL, '2027-11-11', 43667, 5000, 0, 0, 42496, '2025-08-11', 'aman', 'tersedia', '2026-08-10 23:55:26', '2026-08-10 23:55:26'),
(50, 1, NULL, 2, 'AG 1550 YB', NULL, NULL, NULL, 'Pemilik Kendaraan 50', 'Magelang', 'Suzuki APV', '2015', '2015', '1500 CC', 'Putih', 'NREBB917F2E3BB4', 'NM0093B6FC', 'BPKB000050', 'Hitam', 'Pertamax', 'AG', '061700', 563000, 67000, 1611000, NULL, '2026-06-11', 98516, 5000, 0, 0, 90998, '2026-01-11', 'service', 'disewa', '2026-08-10 23:55:26', '2026-08-10 23:55:26');

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
  `sumber` enum('manual','auto') NOT NULL DEFAULT 'manual',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `keuangans`
--

INSERT INTO `keuangans` (`id`, `tanggal`, `reference`, `user_id`, `kategori`, `metode`, `keterangan`, `pemasukan`, `pengeluaran`, `saldo`, `sumber`, `created_at`, `updated_at`) VALUES
(1, '2026-03-31', 'INV-001', 1, 'Rental', 'cash', 'Penerimaan Rental ke-1', 3896000.00, 0.00, 3896000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(2, '2026-07-18', 'INV-002', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-2', 2521000.00, 0.00, 6417000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(3, '2026-07-13', 'EXP-003', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-3', 0.00, 4958000.00, 1459000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(4, '2026-06-13', 'INV-004', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-4', 4172000.00, 0.00, 5631000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(5, '2026-03-07', 'INV-005', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-5', 1089000.00, 0.00, 6720000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(6, '2026-02-17', 'EXP-006', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-6', 0.00, 3605000.00, 3115000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(7, '2026-07-11', 'INV-007', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-7', 3233000.00, 0.00, 6348000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(8, '2026-06-01', 'INV-008', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-8', 2704000.00, 0.00, 9052000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(9, '2026-02-13', 'EXP-009', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-9', 0.00, 2295000.00, 6757000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(10, '2026-06-13', 'INV-010', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-10', 388000.00, 0.00, 7145000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(11, '2026-07-14', 'INV-011', 1, 'Rental', 'cash', 'Penerimaan Rental ke-11', 1299000.00, 0.00, 8444000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(12, '2026-03-05', 'EXP-012', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-12', 0.00, 4068000.00, 4376000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(13, '2026-06-13', 'INV-013', 1, 'Denda', 'cash', 'Penerimaan Denda ke-13', 4916000.00, 0.00, 9292000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(14, '2026-07-04', 'INV-014', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-14', 751000.00, 0.00, 10043000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(15, '2026-07-21', 'EXP-015', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-15', 0.00, 3771000.00, 6272000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(16, '2026-06-14', 'INV-016', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-16', 1485000.00, 0.00, 7757000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(17, '2026-08-09', 'INV-017', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-17', 4307000.00, 0.00, 12064000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(18, '2026-03-29', 'EXP-018', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-18', 0.00, 1187000.00, 10877000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(19, '2026-07-29', 'INV-019', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-19', 4020000.00, 0.00, 14897000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(20, '2026-03-05', 'INV-020', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-20', 1770000.00, 0.00, 16667000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(21, '2026-03-08', 'EXP-021', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-21', 0.00, 2687000.00, 13980000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(22, '2026-05-07', 'INV-022', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-22', 3345000.00, 0.00, 17325000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(23, '2026-06-11', 'INV-023', 1, 'Denda', 'cash', 'Penerimaan Denda ke-23', 2171000.00, 0.00, 19496000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(24, '2026-07-27', 'EXP-024', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-24', 0.00, 2964000.00, 16532000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(25, '2026-07-05', 'INV-025', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-25', 4611000.00, 0.00, 21143000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(26, '2026-06-17', 'INV-026', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-26', 4620000.00, 0.00, 25763000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(27, '2026-05-30', 'EXP-027', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-27', 0.00, 815000.00, 24948000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(28, '2026-05-26', 'INV-028', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-28', 4805000.00, 0.00, 29753000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(29, '2026-02-18', 'INV-029', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-29', 3539000.00, 0.00, 33292000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(30, '2026-06-30', 'EXP-030', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-30', 0.00, 576000.00, 32716000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(31, '2026-04-11', 'INV-031', 1, 'Rental', 'cash', 'Penerimaan Rental ke-31', 2516000.00, 0.00, 35232000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(32, '2026-03-24', 'INV-032', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-32', 2561000.00, 0.00, 37793000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(33, '2026-07-19', 'EXP-033', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-33', 0.00, 1597000.00, 36196000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(34, '2026-04-25', 'INV-034', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-34', 4795000.00, 0.00, 40991000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(35, '2026-04-17', 'INV-035', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-35', 4328000.00, 0.00, 45319000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(36, '2026-05-13', 'EXP-036', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-36', 0.00, 3150000.00, 42169000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(37, '2026-07-17', 'INV-037', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-37', 2520000.00, 0.00, 44689000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(38, '2026-03-24', 'INV-038', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-38', 2586000.00, 0.00, 47275000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(39, '2026-05-09', 'EXP-039', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-39', 0.00, 4162000.00, 43113000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(40, '2026-05-11', 'INV-040', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-40', 2551000.00, 0.00, 45664000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(41, '2026-05-25', 'INV-041', 1, 'Rental', 'cash', 'Penerimaan Rental ke-41', 3603000.00, 0.00, 49267000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(42, '2026-03-08', 'EXP-042', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-42', 0.00, 1652000.00, 47615000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(43, '2026-04-14', 'INV-043', 1, 'Denda', 'cash', 'Penerimaan Denda ke-43', 2182000.00, 0.00, 49797000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(44, '2026-05-10', 'INV-044', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-44', 4287000.00, 0.00, 54084000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(45, '2026-05-06', 'EXP-045', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-45', 0.00, 3779000.00, 50305000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(46, '2026-06-06', 'INV-046', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-46', 640000.00, 0.00, 50945000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(47, '2026-07-04', 'INV-047', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-47', 439000.00, 0.00, 51384000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(48, '2026-02-28', 'EXP-048', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-48', 0.00, 1433000.00, 49951000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(49, '2026-05-19', 'INV-049', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-49', 4333000.00, 0.00, 54284000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(50, '2026-02-24', 'INV-050', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-50', 3818000.00, 0.00, 58102000.00, 'manual', '2026-08-10 23:55:30', '2026-08-10 23:55:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kir`
--

CREATE TABLE `kir` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `no_ktp` varchar(255) NOT NULL,
  `nama_ktp` varchar(255) NOT NULL,
  `lokasi_uji` varchar(255) NOT NULL,
  `penguji` varchar(255) DEFAULT NULL,
  `status_uji` enum('uji berkala','uji pertama') NOT NULL,
  `no_uji` varchar(255) NOT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tanggal_bayar` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kir`
--

INSERT INTO `kir` (`id`, `kendaraan_id`, `no_ktp`, `nama_ktp`, `lokasi_uji`, `penguji`, `status_uji`, `no_uji`, `masa_berlaku`, `biaya`, `tanggal_bayar`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, '3170000000000001', 'Pemilik Kendaraan 1', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-001', '2028-08-10', 100000.00, '2026-07-29', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(2, 2, '3170000000000002', 'Pemilik Kendaraan 2', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-002', '2027-06-30', 450000.00, '2026-08-04', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(3, 3, '3170000000000003', 'Pemilik Kendaraan 3', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-003', '2027-07-09', 450000.00, '2026-07-14', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(4, 4, '3170000000000004', 'Pemilik Kendaraan 4', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-004', '2026-09-16', 350000.00, '2026-07-30', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(5, 5, '3170000000000005', 'Pemilik Kendaraan 5', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-005', '2028-02-19', 100000.00, '2026-08-01', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(6, 6, '3170000000000006', 'Pemilik Kendaraan 6', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-006', '2027-12-01', 400000.00, '2026-08-09', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(7, 7, '3170000000000007', 'Pemilik Kendaraan 7', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-007', '2028-06-27', 350000.00, '2026-07-18', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(8, 8, '3170000000000008', 'Pemilik Kendaraan 8', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-008', '2028-03-17', 450000.00, '2026-07-25', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(9, 9, '3170000000000009', 'Pemilik Kendaraan 9', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-009', '2027-01-30', 450000.00, '2026-07-18', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(10, 10, '3170000000000010', 'Pemilik Kendaraan 10', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-010', '2028-01-16', 100000.00, '2026-08-07', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(11, 11, '3170000000000011', 'Pemilik Kendaraan 11', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-011', '2027-09-16', 500000.00, '2026-07-14', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(12, 12, '3170000000000012', 'Pemilik Kendaraan 12', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-012', '2026-09-22', 350000.00, '2026-07-15', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(13, 13, '3170000000000013', 'Pemilik Kendaraan 13', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-013', '2026-09-24', 300000.00, '2026-07-26', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(14, 14, '3170000000000014', 'Pemilik Kendaraan 14', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-014', '2027-08-21', 500000.00, '2026-07-24', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(15, 15, '3170000000000015', 'Pemilik Kendaraan 15', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-015', '2028-03-07', 350000.00, '2026-07-15', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(16, 16, '3170000000000016', 'Pemilik Kendaraan 16', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-016', '2026-11-02', 200000.00, '2026-08-10', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(17, 17, '3170000000000017', 'Pemilik Kendaraan 17', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-017', '2026-07-14', 150000.00, '2026-08-02', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(18, 18, '3170000000000018', 'Pemilik Kendaraan 18', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-018', '2028-02-09', 200000.00, '2026-07-23', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(19, 19, '3170000000000019', 'Pemilik Kendaraan 19', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-019', '2027-07-10', 500000.00, '2026-07-26', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(20, 20, '3170000000000020', 'Pemilik Kendaraan 20', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-020', '2027-11-16', 350000.00, '2026-07-22', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(21, 21, '3170000000000021', 'Pemilik Kendaraan 21', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-021', '2027-04-02', 500000.00, '2026-08-06', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(22, 22, '3170000000000022', 'Pemilik Kendaraan 22', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-022', '2026-11-13', 150000.00, '2026-07-23', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(23, 23, '3170000000000023', 'Pemilik Kendaraan 23', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-023', '2027-06-29', 50000.00, '2026-08-02', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(24, 24, '3170000000000024', 'Pemilik Kendaraan 24', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-024', '2027-10-24', 50000.00, '2026-08-07', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(25, 25, '3170000000000025', 'Pemilik Kendaraan 25', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-025', '2028-06-09', 200000.00, '2026-07-13', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(26, 26, '3170000000000026', 'Pemilik Kendaraan 26', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-026', '2027-02-06', 300000.00, '2026-08-03', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(27, 27, '3170000000000027', 'Pemilik Kendaraan 27', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-027', '2026-12-10', 250000.00, '2026-08-03', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(28, 28, '3170000000000028', 'Pemilik Kendaraan 28', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-028', '2028-06-22', 50000.00, '2026-08-09', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(29, 29, '3170000000000029', 'Pemilik Kendaraan 29', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-029', '2028-04-22', 150000.00, '2026-07-14', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(30, 30, '3170000000000030', 'Pemilik Kendaraan 30', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-030', '2028-01-29', 50000.00, '2026-07-24', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(31, 31, '3170000000000031', 'Pemilik Kendaraan 31', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-031', '2028-04-21', 300000.00, '2026-07-23', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(32, 32, '3170000000000032', 'Pemilik Kendaraan 32', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-032', '2026-08-18', 250000.00, '2026-07-21', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(33, 33, '3170000000000033', 'Pemilik Kendaraan 33', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-033', '2028-05-17', 50000.00, '2026-07-31', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(34, 34, '3170000000000034', 'Pemilik Kendaraan 34', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-034', '2027-12-04', 500000.00, '2026-07-27', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(35, 35, '3170000000000035', 'Pemilik Kendaraan 35', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-035', '2027-04-11', 500000.00, '2026-07-30', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(36, 36, '3170000000000036', 'Pemilik Kendaraan 36', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-036', '2027-05-26', 350000.00, '2026-07-24', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(37, 37, '3170000000000037', 'Pemilik Kendaraan 37', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-037', '2027-01-27', 50000.00, '2026-07-15', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(38, 38, '3170000000000038', 'Pemilik Kendaraan 38', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-038', '2028-06-22', 200000.00, '2026-07-12', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(39, 39, '3170000000000039', 'Pemilik Kendaraan 39', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-039', '2028-07-30', 500000.00, '2026-08-01', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(40, 40, '3170000000000040', 'Pemilik Kendaraan 40', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-040', '2028-02-17', 250000.00, '2026-07-22', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(41, 41, '3170000000000041', 'Pemilik Kendaraan 41', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-041', '2028-01-02', 450000.00, '2026-08-07', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(42, 42, '3170000000000042', 'Pemilik Kendaraan 42', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-042', '2028-08-02', 500000.00, '2026-07-26', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(43, 43, '3170000000000043', 'Pemilik Kendaraan 43', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-043', '2026-12-12', 400000.00, '2026-07-18', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(44, 44, '3170000000000044', 'Pemilik Kendaraan 44', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-044', '2026-08-31', 200000.00, '2026-07-19', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(45, 45, '3170000000000045', 'Pemilik Kendaraan 45', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-045', '2028-02-27', 300000.00, '2026-08-08', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(46, 46, '3170000000000046', 'Pemilik Kendaraan 46', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-046', '2027-05-21', 250000.00, '2026-07-20', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(47, 47, '3170000000000047', 'Pemilik Kendaraan 47', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-047', '2027-09-22', 350000.00, '2026-08-03', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(48, 48, '3170000000000048', 'Pemilik Kendaraan 48', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-048', '2028-03-06', 100000.00, '2026-08-06', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(49, 49, '3170000000000049', 'Pemilik Kendaraan 49', 'UPTD PKB Dinas Perhubungan', NULL, 'uji pertama', 'KIR-2026-049', '2027-04-26', 200000.00, '2026-07-29', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(50, 50, '3170000000000050', 'Pemilik Kendaraan 50', 'UPTD PKB Dinas Perhubungan', NULL, 'uji berkala', 'KIR-2026-050', '2028-02-15', 350000.00, '2026-07-14', NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29');

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

-- --------------------------------------------------------

--
-- Struktur dari tabel `komisi_sales`
--

CREATE TABLE `komisi_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_sales` varchar(255) NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `total_penjualan` decimal(15,2) NOT NULL,
  `persen_komisi` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_komisi` decimal(15,2) NOT NULL,
  `status_bayar` varchar(255) NOT NULL DEFAULT 'Belum Dibayar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `komisi_sales`
--

INSERT INTO `komisi_sales` (`id`, `nama_sales`, `bulan`, `total_penjualan`, `persen_komisi`, `total_komisi`, `status_bayar`, `created_at`, `updated_at`) VALUES
(1, 'Andi', '2026-01', 45000000.00, 3.00, 1350000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'Budi', '2026-01', 38000000.00, 3.00, 1140000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'Cici', '2026-01', 52000000.00, 3.50, 1820000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'Dani', '2026-01', 29000000.00, 3.00, 870000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'Andi', '2026-02', 55000000.00, 3.50, 1925000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'Budi', '2026-02', 42000000.00, 3.00, 1260000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'Cici', '2026-02', 60000000.00, 4.00, 2400000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'Dani', '2026-02', 35000000.00, 3.00, 1050000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'Andi', '2026-03', 48000000.00, 3.00, 1440000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'Budi', '2026-03', 51000000.00, 3.50, 1785000.00, 'Sudah Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(11, 'Cici', '2026-03', 44000000.00, 3.00, 1320000.00, 'Belum Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(12, 'Dani', '2026-03', 39000000.00, 3.00, 1170000.00, 'Belum Dibayar', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontrak_aktifs`
--

CREATE TABLE `kontrak_aktifs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_kontrak` varchar(255) NOT NULL,
  `mitra` varchar(255) NOT NULL,
  `nilai` bigint(20) UNSIGNED NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `pic` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `perpanjangan` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_appraisals`
--

CREATE TABLE `kpi_appraisals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `periode_evaluasi` varchar(255) NOT NULL,
  `disiplin` bigint(20) NOT NULL,
  `kolaborasi` bigint(20) NOT NULL,
  `produktivitas` bigint(20) NOT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL,
  `evaluator` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_appraisals`
--

INSERT INTO `kpi_appraisals` (`id`, `nama_pegawai`, `periode_evaluasi`, `disiplin`, `kolaborasi`, `produktivitas`, `nilai_akhir`, `evaluator`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Rini Apriani', 'Q1 2025', 89, 90, 100, 93.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(2, 'Rini Apriani', 'Q2 2025', 95, 84, 82, 87.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(3, 'Rini Apriani', 'Q3 2025', 80, 89, 68, 79.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(4, 'Rini Apriani', 'Q4 2025', 86, 66, 96, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(5, 'Rini Apriani', 'Q1 2026', 83, 77, 97, 85.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(6, 'Rini Apriani', 'Q2 2026', 88, 91, 92, 90.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(7, 'Eko Prasetyo', 'Q1 2025', 83, 100, 83, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(8, 'Eko Prasetyo', 'Q2 2025', 84, 93, 79, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(9, 'Eko Prasetyo', 'Q3 2025', 65, 99, 77, 80.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(10, 'Eko Prasetyo', 'Q4 2025', 88, 92, 84, 88.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(11, 'Eko Prasetyo', 'Q1 2026', 96, 80, 90, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(12, 'Eko Prasetyo', 'Q2 2026', 92, 68, 97, 85.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(13, 'Rizky Fadillah', 'Q1 2025', 79, 94, 79, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(14, 'Rizky Fadillah', 'Q2 2025', 86, 87, 86, 86.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(15, 'Rizky Fadillah', 'Q3 2025', 91, 65, 73, 76.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(16, 'Rizky Fadillah', 'Q4 2025', 71, 91, 95, 85.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(17, 'Rizky Fadillah', 'Q1 2026', 94, 95, 77, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(18, 'Rizky Fadillah', 'Q2 2026', 70, 76, 98, 81.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(19, 'Yusuf Hidayat', 'Q1 2025', 73, 77, 86, 78.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(20, 'Yusuf Hidayat', 'Q2 2025', 79, 77, 66, 74.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(21, 'Yusuf Hidayat', 'Q3 2025', 81, 81, 82, 81.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(22, 'Yusuf Hidayat', 'Q4 2025', 71, 79, 71, 73.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(23, 'Yusuf Hidayat', 'Q1 2026', 78, 87, 83, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(24, 'Yusuf Hidayat', 'Q2 2026', 66, 73, 92, 77.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(25, 'Wahyu Nugroho', 'Q1 2025', 97, 99, 94, 96.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(26, 'Wahyu Nugroho', 'Q2 2025', 99, 76, 80, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(27, 'Wahyu Nugroho', 'Q3 2025', 77, 86, 91, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(28, 'Wahyu Nugroho', 'Q4 2025', 97, 85, 90, 90.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(29, 'Wahyu Nugroho', 'Q1 2026', 74, 71, 99, 81.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(30, 'Wahyu Nugroho', 'Q2 2026', 94, 91, 90, 91.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(31, 'Fitri Handayani', 'Q1 2025', 77, 97, 90, 88.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(32, 'Fitri Handayani', 'Q2 2025', 79, 98, 83, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(33, 'Fitri Handayani', 'Q3 2025', 80, 75, 80, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(34, 'Fitri Handayani', 'Q4 2025', 96, 73, 70, 79.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(35, 'Fitri Handayani', 'Q1 2026', 89, 82, 73, 81.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(36, 'Fitri Handayani', 'Q2 2026', 100, 82, 77, 86.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(37, 'Teguh Santosa', 'Q1 2025', 69, 79, 86, 78.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(38, 'Teguh Santosa', 'Q2 2025', 93, 76, 95, 88.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(39, 'Teguh Santosa', 'Q3 2025', 82, 81, 91, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(40, 'Teguh Santosa', 'Q4 2025', 65, 94, 78, 79.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(41, 'Teguh Santosa', 'Q1 2026', 96, 76, 66, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(42, 'Teguh Santosa', 'Q2 2026', 68, 70, 87, 75.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(43, 'Arif Budiman', 'Q1 2025', 71, 79, 91, 80.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(44, 'Arif Budiman', 'Q2 2025', 71, 85, 84, 80.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(45, 'Arif Budiman', 'Q3 2025', 67, 70, 66, 67.67, 'Dewi Kusuma', 'Perlu pembinaan dan evaluasi lanjutan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(46, 'Arif Budiman', 'Q4 2025', 70, 98, 80, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(47, 'Arif Budiman', 'Q1 2026', 99, 75, 85, 86.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(48, 'Arif Budiman', 'Q2 2026', 84, 68, 82, 78.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(49, 'Dewi Kusuma', 'Q1 2025', 97, 83, 78, 86.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(50, 'Dewi Kusuma', 'Q2 2025', 81, 81, 65, 75.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(51, 'Dewi Kusuma', 'Q3 2025', 96, 87, 72, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(52, 'Dewi Kusuma', 'Q4 2025', 94, 84, 69, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(53, 'Dewi Kusuma', 'Q1 2026', 81, 66, 95, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(54, 'Dewi Kusuma', 'Q2 2026', 96, 97, 92, 95.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(55, 'Linda Permata', 'Q1 2025', 94, 67, 67, 76.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(56, 'Linda Permata', 'Q2 2025', 90, 88, 85, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(57, 'Linda Permata', 'Q3 2025', 67, 83, 81, 77.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(58, 'Linda Permata', 'Q4 2025', 92, 88, 97, 92.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(59, 'Linda Permata', 'Q1 2026', 76, 75, 84, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(60, 'Linda Permata', 'Q2 2026', 82, 76, 67, 75.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(61, 'Hendra Gunawan', 'Q1 2025', 91, 66, 76, 77.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(62, 'Hendra Gunawan', 'Q2 2025', 68, 88, 96, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(63, 'Hendra Gunawan', 'Q3 2025', 80, 94, 93, 89.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(64, 'Hendra Gunawan', 'Q4 2025', 67, 69, 91, 75.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(65, 'Hendra Gunawan', 'Q1 2026', 92, 100, 65, 85.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(66, 'Hendra Gunawan', 'Q2 2026', 83, 83, 96, 87.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(67, 'Dody Kurniawan', 'Q1 2025', 82, 81, 91, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(68, 'Dody Kurniawan', 'Q2 2025', 85, 84, 77, 82.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(69, 'Dody Kurniawan', 'Q3 2025', 72, 98, 72, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(70, 'Dody Kurniawan', 'Q4 2025', 77, 70, 71, 72.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(71, 'Dody Kurniawan', 'Q1 2026', 75, 72, 78, 75.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(72, 'Dody Kurniawan', 'Q2 2026', 72, 74, 66, 70.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(73, 'Siti Rahayu', 'Q1 2025', 97, 98, 71, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(74, 'Siti Rahayu', 'Q2 2025', 66, 96, 83, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(75, 'Siti Rahayu', 'Q3 2025', 69, 69, 90, 76.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(76, 'Siti Rahayu', 'Q4 2025', 92, 72, 83, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(77, 'Siti Rahayu', 'Q1 2026', 68, 94, 79, 80.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(78, 'Siti Rahayu', 'Q2 2026', 70, 86, 77, 77.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(79, 'Agus Wibowo', 'Q1 2025', 75, 72, 66, 71.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(80, 'Agus Wibowo', 'Q2 2025', 93, 71, 81, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(81, 'Agus Wibowo', 'Q3 2025', 99, 83, 99, 93.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(82, 'Agus Wibowo', 'Q4 2025', 72, 78, 80, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(83, 'Agus Wibowo', 'Q1 2026', 94, 76, 70, 80.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(84, 'Agus Wibowo', 'Q2 2026', 83, 65, 93, 80.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(85, 'Budi Santoso', 'Q1 2025', 100, 85, 94, 93.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(86, 'Budi Santoso', 'Q2 2025', 74, 93, 84, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(87, 'Budi Santoso', 'Q3 2025', 88, 74, 84, 82.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(88, 'Budi Santoso', 'Q4 2025', 99, 86, 100, 95.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(89, 'Budi Santoso', 'Q1 2026', 88, 85, 86, 86.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(90, 'Budi Santoso', 'Q2 2026', 78, 99, 66, 81.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-08-10 23:55:41', '2026-08-10 23:55:41');

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
(1, 'APY Rental', 25000000.00, 12000000.00, 13000000.00, '2026-08', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(2, 'APY Rental', 30000000.00, 15000000.00, 15000000.00, '2026-07', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(3, 'APY Rental', 18000000.00, 9000000.00, 9000000.00, '2026-06', '2026-08-10 23:55:31', '2026-08-10 23:55:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `legal_documents`
--

CREATE TABLE `legal_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `pihak_terkait` varchar(255) NOT NULL,
  `tgl_terbit` date NOT NULL,
  `berlaku_hingga` date DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `format` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `litigasis`
--

CREATE TABLE `litigasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kasus` varchar(255) NOT NULL,
  `lawan` varchar(255) NOT NULL,
  `jenis_kasus` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `pengacara` varchar(255) NOT NULL,
  `tanggal_sidang` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `loyalties`
--

CREATE TABLE `loyalties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_program` varchar(255) NOT NULL,
  `nama_program` varchar(255) NOT NULL,
  `jenis_reward` varchar(255) NOT NULL,
  `akumulasi_poin` varchar(255) NOT NULL,
  `konversi_poin` varchar(255) NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `loyalties`
--

INSERT INTO `loyalties` (`id`, `id_program`, `nama_program`, `jenis_reward`, `akumulasi_poin`, `konversi_poin`, `periode_mulai`, `periode_akhir`, `status`, `created_at`, `updated_at`) VALUES
(1, 'LYL001', 'APY Points', 'Poin', '1 poin per 10.000', '100 poin = Rp 50.000', '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'LYL002', 'Free Day Program', 'Hari Gratis', '10 hari rental = 1 hari gratis', '1 hari gratis per periode', '2026-07-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `member`
--

CREATE TABLE `member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pelanggan` varchar(255) DEFAULT NULL,
  `kontak_pelanggan` varchar(255) DEFAULT NULL,
  `no_ktp` varchar(20) DEFAULT NULL,
  `email_pelanggan` varchar(255) DEFAULT NULL,
  `jenis_pelanggan` enum('perorangan','perusahaan') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `member`
--

INSERT INTO `member` (`id`, `nama_pelanggan`, `kontak_pelanggan`, `no_ktp`, `email_pelanggan`, `jenis_pelanggan`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '08267279938', NULL, 'budi.santoso@gmail.com', 'perorangan', 'Jl. Wonosobo No. 60', '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(2, 'Joko Widodo', '08349788195', NULL, 'joko.widodo@gmail.com', 'perorangan', 'Jl. Magelang No. 2', '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(3, 'Andi Saputra', '08600818979', NULL, 'andi.saputra@gmail.com', 'perorangan', 'Jl. Purworejo No. 86', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(4, 'Rizky Pratama', '08289152334', NULL, 'rizky.pratama@gmail.com', 'perorangan', 'Jl. Kebumen No. 37', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(5, 'Dian Permata', '08712151414', NULL, 'dian.permata@gmail.com', 'perorangan', 'Jl. Purwokerto No. 78', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(6, 'Siti Rahayu', '08437473069', NULL, 'siti.rahayu@gmail.com', 'perorangan', 'Jl. Temanggung No. 3', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(7, 'Ahmad Fauzi', '08895485726', NULL, 'ahmad.fauzi@gmail.com', 'perorangan', 'Jl. Kendal No. 16', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(8, 'Dewi Lestari', '08723222341', NULL, 'dewi.lestari@gmail.com', 'perorangan', 'Jl. Semarang No. 43', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(9, 'Hendra Gunawan', '08772882201', NULL, 'hendra.gunawan@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 62', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(10, 'Rina Wati', '08726525954', NULL, 'rina.wati@gmail.com', 'perorangan', 'Jl. Solo No. 13', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(11, 'Bambang Sutrisno', '08467154250', NULL, 'bambang.sutrisno@gmail.com', 'perorangan', 'Jl. Wonosobo No. 76', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(12, 'Nia Ramadhani', '08890829861', NULL, 'nia.ramadhani@gmail.com', 'perorangan', 'Jl. Magelang No. 39', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(13, 'Ferdy Sambo', '08457277129', NULL, 'ferdy.sambo@gmail.com', 'perorangan', 'Jl. Purworejo No. 50', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(14, 'Lina Marlina', '08761358996', NULL, 'lina.marlina@gmail.com', 'perorangan', 'Jl. Kebumen No. 6', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(15, 'Tono Suprapto', '08960112941', NULL, 'tono.suprapto@gmail.com', 'perorangan', 'Jl. Purwokerto No. 36', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(16, 'Yuli Astuti', '08456661657', NULL, 'yuli.astuti@gmail.com', 'perorangan', 'Jl. Temanggung No. 100', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(17, 'Fajar Nugroho', '08961383670', NULL, 'fajar.nugroho@gmail.com', 'perorangan', 'Jl. Kendal No. 79', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(18, 'Sri Wahyuni', '08334731298', NULL, 'sri.wahyuni@gmail.com', 'perorangan', 'Jl. Semarang No. 39', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(19, 'Rudi Hartono', '08598171084', NULL, 'rudi.hartono@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 33', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(20, 'Mega Putri', '08916047823', NULL, 'mega.putri@gmail.com', 'perorangan', 'Jl. Solo No. 16', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(21, 'Wahyu Setiawan', '08710534204', NULL, 'wahyu.setiawan@gmail.com', 'perorangan', 'Jl. Wonosobo No. 1', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(22, 'Indah Kurniasih', '08207979511', NULL, 'indah.kurniasih@gmail.com', 'perorangan', 'Jl. Magelang No. 14', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(23, 'Eko Prasetyo', '08785112157', NULL, 'eko.prasetyo@gmail.com', 'perorangan', 'Jl. Purworejo No. 38', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(24, 'Fitri Handayani', '08570201669', NULL, 'fitri.handayani@gmail.com', 'perorangan', 'Jl. Kebumen No. 12', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(25, 'Galih Wicaksono', '08486794778', NULL, 'galih.wicaksono@gmail.com', 'perorangan', 'Jl. Purwokerto No. 22', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(26, 'PT Maju Bersama', '0289668594', NULL, 'ptmajubersama@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 26', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(27, 'CV Sumber Rezeki', '0299214152', NULL, 'cvsumberrezeki@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 15', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(28, 'PT Cahaya Abadi', '0253650763', NULL, 'ptcahayaabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 35', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(29, 'CV Jaya Mandiri', '0266406648', NULL, 'cvjayamandiri@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 36', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(30, 'PT Sukses Selalu', '0275414885', NULL, 'ptsuksesselalu@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 57', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(31, 'PT Karya Utama', '0242128455', NULL, 'ptkaryautama@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 140', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(32, 'CV Harapan Baru', '0294940681', NULL, 'cvharapanbaru@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 112', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(33, 'PT Gemilang Jaya', '0298975009', NULL, 'ptgemilangjaya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 171', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(34, 'CV Delta Nusantara', '0244004036', NULL, 'cvdeltanusantara@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 126', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(35, 'PT Bintang Timur', '0239296702', NULL, 'ptbintangtimur@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 163', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(36, 'PT Nusantara Trans', '0268150082', NULL, 'ptnusantaratrans@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 90', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(37, 'CV Permata Hijau', '0231938993', NULL, 'cvpermatahijau@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 46', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(38, 'PT Sinar Mas Logistik', '0232607685', NULL, 'ptsinarmaslogistik@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 91', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(39, 'CV Berkah Sejati', '0211224277', NULL, 'cvberkahsejati@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 137', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(40, 'PT Indo Mitra', '0217682968', NULL, 'ptindomitra@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 71', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(41, 'PT Wahana Ekspres', '0262446624', NULL, 'ptwahanaekspres@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 123', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(42, 'CV Tirta Agung', '0259131694', NULL, 'cvtirtaagung@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 57', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(43, 'PT Mandiri Karya', '0235064779', NULL, 'ptmandirikarya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 24', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(44, 'CV Perkasa Utama', '0275479061', NULL, 'cvperkasautama@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 170', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(45, 'PT Cipta Rasa', '0288863803', NULL, 'ptciptarasa@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 110', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(46, 'PT Lancar Jaya', '0291208997', NULL, 'ptlancarjaya@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 60', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(47, 'CV Mitra Usaha', '0285317515', NULL, 'cvmitrausaha@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 138', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(48, 'PT Sejahtera Abadi', '0241742915', NULL, 'ptsejahteraabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 183', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(49, 'CV Putra Bangsa', '0264442240', NULL, 'cvputrabangsa@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 157', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(50, 'PT Global Trans', '0288022079', NULL, 'ptglobaltrans@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 191', '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(51, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-12 09:33:58', '2026-08-12 09:33:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kontak` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `jenis_member` enum('perorangan','perusahaan') NOT NULL DEFAULT 'perorangan',
  `alamat` text DEFAULT NULL,
  `file_stnk` text DEFAULT NULL,
  `file_attachment` text DEFAULT NULL,
  `file_kontrak` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `members`
--

INSERT INTO `members` (`id`, `nama`, `kontak`, `email`, `jenis_member`, `alamat`, `file_stnk`, `file_attachment`, `file_kontrak`, `created_at`, `updated_at`) VALUES
(1, 'moneyhost', '98768909876', 'toty@gmail', 'perorangan', 'tyuioiuyuiopoiu', NULL, NULL, NULL, '2026-08-11 14:46:21', '2026-08-11 14:46:21'),
(2, 'Milik Sendiri', '0', 'toty@gmail', 'perusahaan', 'oikhgkjhghj', NULL, NULL, NULL, '2026-08-11 14:48:46', '2026-08-11 14:48:46');

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
(20, '2026_05_11_094830_create_aging_aps_table', 1),
(21, '2026_05_11_134521_create_rental_biaya_tambahan_table', 1),
(22, '2026_05_11_151258_add_batas_biaya_to_rentals_table', 1),
(23, '2026_05_11_160910_create_keuangans_table', 1),
(24, '2026_05_11_161000_create_hutang_vendors_table', 1),
(25, '2026_05_16_094933_create_deposit_customers_table', 1),
(26, '2026_05_16_095006_create_denda_rentals_table', 1),
(27, '2026_05_16_095050_create_biaya_operasional_kendaraans_table', 1),
(28, '2026_05_16_095111_create_pajak_kendaraans_table', 1),
(29, '2026_05_20_162153_create_anggaran_proyek_table', 1),
(30, '2026_05_20_163514_create_laporan_keuangan_table', 1),
(31, '2026_05_20_170654_create_efakturs_table', 1),
(32, '2026_05_20_174514_create_rekonsiliasi_bank_table', 1),
(33, '2026_05_20_182735_create_bukubesars_table', 1),
(34, '2026_05_20_980538_create_virtual_accounts_table', 1),
(35, '2026_05_21_172335_create_ebukots_table', 1),
(36, '2026_05_25_144239_add_bukti_to_pajak_kendaraans_table', 1),
(37, '2026_05_29_095213_add_sisa_limit_to_service_history_table', 1),
(38, '2026_06_06_152857_create_settings_table', 1),
(39, '2026_06_19_125311_create_pajak_histories_table', 1),
(40, '2026_06_20_033524_create_gps_histories_table', 1),
(41, '2026_06_20_063123_create_asuransi_history_table', 1),
(42, '2026_06_20_082104_create_kir_history_table', 1),
(43, '2026_06_20_171514_create_stnk_table', 1),
(44, '2026_06_20_185146_create_stnk_history_table', 1),
(45, '2026_06_21_194202_add_durasi_tahun_kelayakan_invoice_to_rentals_table', 1),
(46, '2026_06_25_074036_create_inv_penawarans_table', 1),
(47, '2026_06_25_074129_create_inv_penawaran_items_table', 1),
(48, '2026_06_25_074143_create_inv_kontraks_table', 1),
(49, '2026_06_25_074153_create_invoices_table', 1),
(50, '2026_06_25_074200_create_aging_ars_table', 1),
(51, '2026_06_25_074317_create_invoice_periodes_and_remaks_table', 1),
(52, '2026_06_25_074339_create_invoice_payments_and_summaries_table', 1),
(53, '2026_07_01_130223_add_last_email_sent_at_to_invoice_table', 1),
(54, '2026_07_03_095431_create_procurementos_table', 1),
(55, '2026_07_03_112242_create_purchaseros_table', 1),
(56, '2026_07_03_121211_create_vendoreos_table', 1),
(57, '2026_07_04_003726_create_attachments_table', 1),
(58, '2026_07_05_143751_add_ttd_to_invoices_table', 1),
(59, '2026_07_06_220001_create_struktur_organisasis_table', 1),
(60, '2026_07_06_220002_create_departemens_table', 1),
(61, '2026_07_06_220003_create_skill_matrices_table', 1),
(62, '2026_07_06_220004_create_presensis_table', 1),
(63, '2026_07_06_220005_create_shift_lemburs_table', 1),
(64, '2026_07_06_220006_create_payrolls_table', 1),
(65, '2026_07_06_220007_create_cuti_izins_table', 1),
(66, '2026_07_06_220008_create_kpi_appraisals_table', 1),
(67, '2026_07_06_220009_create_resign_offboardings_table', 1),
(68, '2026_07_06_220010_create_hrd_files_table', 1),
(69, '2026_07_08_000001_create_kampanyes_table', 1),
(70, '2026_07_08_000002_create_otomatisasis_table', 1),
(71, '2026_07_08_000003_create_segmentasis_table', 1),
(72, '2026_07_08_000004_create_loyalties_table', 1),
(73, '2026_07_08_000005_create_afiliasis_table', 1),
(74, '2026_07_08_000006_create_sosmedps_table', 1),
(75, '2026_07_08_000007_create_trackingutms_table', 1),
(76, '2026_07_08_000008_create_ads_integrations_table', 1),
(77, '2026_07_08_000009_create_crm_prospeks_table', 1),
(78, '2026_07_08_000010_create_penawarans_table', 1),
(79, '2026_07_08_000011_create_sales_orders_table', 1),
(80, '2026_07_08_000012_create_pricelist_diskons_table', 1),
(81, '2026_07_08_000013_create_target_penjualans_table', 1),
(82, '2026_07_08_000014_create_komisi_sales_table', 1),
(83, '2026_07_08_000015_create_retur_penjualans_table', 1),
(84, '2026_07_08_000016_create_signature_dokumens_table', 1),
(85, '2026_07_08_000020_create_induk_proyeks_table', 1),
(86, '2026_07_08_000021_create_project_plannings_table', 1),
(87, '2026_07_08_000022_create_project_timelines_table', 1),
(88, '2026_07_08_000023_create_project_costs_table', 1),
(89, '2026_07_08_000024_create_project_risks_table', 1),
(90, '2026_07_08_000025_create_dokumen_proyeks_table', 1),
(91, '2026_07_08_000026_create_pembelian_proyeks_table', 1),
(92, '2026_07_08_000030_create_legal_documents_table', 1),
(93, '2026_07_08_000031_create_kontrak_aktifs_table', 1),
(94, '2026_07_08_000032_create_review_legals_table', 1),
(95, '2026_07_08_000033_create_hak_hukums_table', 1),
(96, '2026_07_08_000034_create_litigasis_table', 1),
(97, '2026_07_08_000035_create_sertifikasi_perizinans_table', 1),
(98, '2026_07_08_000036_create_daftar_notaris_table', 1),
(99, '2026_07_08_130642_create_requestfor_quotations_table', 1),
(100, '2026_07_08_200001_create_itasset_management_table', 1),
(101, '2026_07_08_200001_create_purchase_orders_table', 1),
(102, '2026_07_08_200002_create_software_licenses_table', 1),
(103, '2026_07_08_200002_create_vendor_pricelists_table', 1),
(104, '2026_07_08_200003_create_approval_workflows_table', 1),
(105, '2026_07_08_200003_create_helpdesk_supports_table', 1),
(106, '2026_07_08_200004_create_dropshippings_table', 1),
(107, '2026_07_08_200004_create_user_accesses_table', 1),
(108, '2026_07_08_200005_create_network_monitorings_table', 1),
(109, '2026_07_08_200005_create_vendor_performances_table', 1),
(110, '2026_07_08_200006_create_cybersecurities_table', 1),
(111, '2026_07_08_200007_create_email_domains_table', 1),
(112, '2026_07_08_200008_create_server_clouds_table', 1),
(113, '2026_07_08_200009_create_system_backups_table', 1),
(114, '2026_07_08_200010_create_project_management_table', 1),
(115, '2026_07_08_200011_create_devops_table', 1),
(116, '2026_07_08_200012_create_policy_compliances_table', 1),
(117, '2026_07_09_000001_create_invoice_pivot_tables', 1),
(118, '2026_07_09_000001_recreate_it_technology_tables', 1),
(119, '2026_07_09_000002_create_induk_assets_table', 1),
(120, '2026_07_09_000002_create_members_table', 1),
(121, '2026_07_09_000003_add_member_id_to_kendaraan_table', 1),
(122, '2026_07_09_000003_create_pergerakan_assets_table', 1),
(123, '2026_07_09_000004_change_file_attachment_to_text_in_members', 1),
(124, '2026_07_09_000004_create_pemeliharaan_assets_table', 1),
(125, '2026_07_09_000005_change_file_stnk_to_text_in_members', 1),
(126, '2026_07_09_000005_create_penyusutan_assets_table', 1),
(127, '2026_07_09_000006_create_perolehan_assets_table', 1),
(128, '2026_07_09_000007_create_asset_dihapuskans_table', 1),
(129, '2026_07_09_000008_create_dokumentasi_assets_table', 1),
(130, '2026_07_09_000009_create_penanggung_jawabs_table', 1),
(131, '2026_07_09_000010_create_audit_assets_table', 1),
(132, '2026_07_09_210636_add_tanggal_bayar_to_kir_table', 1),
(133, '2026_07_09_220001_add_terakhir_diajukan_to_purchaseros_table', 1),
(134, '2026_07_09_220002_add_nominal_to_purchaseros_table', 1),
(135, '2026_07_10_000001_add_tanggal_bayar_to_asuransi_tables', 1),
(136, '2026_07_10_001052_add_referensi_to_bukubesars_table', 1),
(137, '2026_07_11_124914_add_tanggal_bayar_to_gps_kendaraan_table', 1),
(138, '2026_07_12_000001_create_reminder_service_table', 1),
(139, '2026_07_13_145757_add_biaya_to_reminder_service_table', 1),
(140, '2026_07_14_000001_add_sumber_to_keuangans_table', 1),
(141, '2026_07_14_000002_change_bukubesars_to_decimal', 1),
(142, '2026_07_14_000003_change_aging_ars_total_to_decimal', 1),
(143, '2026_07_14_000004_add_hutang_vendor_id_to_aging_aps_table', 1),
(144, '2026_07_22_000001_rename_limit_service_columns_in_kendaraan_table', 1),
(145, '2026_07_22_000002_add_service_history_id_to_service_detail_table', 1),
(146, '2026_07_25_000001_add_nominal_pelunasan_to_rentals_table', 1),
(147, '2026_07_28_001913_add_keterangan_to_gps_kendaraan_table', 1),
(148, '2026_07_28_004513_add_pengantaran_penjemputan_to_rentals_table', 1),
(149, '2026_07_28_135000_drop_unique_no_pr_purchaseros', 1),
(150, '2026_07_28_220233_add_customer_fields_to_inv_penawarans_table', 1),
(151, '2026_07_30_073000_add_durasi_and_status_to_inv_kontraks_table', 1),
(152, '2026_07_30_100113_add_ppn_pph_to_settings_table', 1),
(153, '2026_07_30_120000_add_noktp_fax_to_tables', 1),
(154, '2026_08_04_000001_add_partial_to_invoices_payment_status_enum', 1),
(155, '2026_08_05_090000_add_foto_masalah_to_kendaraan_table', 1),
(156, '2026_08_06_163530_add_partial_to_rentals_status_pembayaran', 1),
(157, '2026_08_07_122443_change_bukti_to_text_in_service_detail_table', 1),
(158, '2026_08_07_205430_add_attachment_to_service_detail_table', 1),
(159, '2026_08_07_212700_create_service_asuransi_table', 1),
(160, '2026_08_07_221940_add_periode_count_to_inv_summaries_table', 1),
(161, '2026_08_07_224421_create_data_leasings_table', 1),
(162, '2026_08_07_233110_alter_data_leasings_periode_jatuh_tempo', 1),
(163, '2026_08_08_000519_add_customer_fields_to_inv_kontraks_table', 1),
(164, '2026_08_08_001710_add_ketentuan_asuransi_to_inv_kontraks_table', 1),
(165, '2026_08_08_002548_add_alamat_kedua_to_inv_kontraks_table', 1),
(166, '2026_08_08_142516_make_biaya_nullable_in_service_asuransi_table', 1),
(167, '2026_08_08_143244_add_file_pembayaran_name_to_invoice_payments_table', 1),
(168, '2026_08_10_174719_add_periode_satuan_to_inv_penawarans_table', 1),
(169, '2026_08_10_181517_add_pasal_ketentuan_to_inv_kontraks_table', 1),
(170, '2026_08_10_203500_add_status_to_service_asuransi_table', 1),
(171, '2026_08_10_210000_create_data_kontraks_table', 1),
(172, '2026_08_10_211000_update_data_leasings_fk_to_data_kontraks', 1),
(173, '2026_08_10_212000_replace_asuransi_leasing_in_data_kontraks', 1),
(174, '2026_08_12_000001_add_ketentuan_to_inv_penawarans_table', 2),
(175, '2026_08_13_000001_add_asuransi_fields_to_service_asuransi_table', 3),
(176, '2026_08_13_000002_add_serial_number_to_data_kontraks_table', 4),
(177, '2026_08_13_022717_add_jumlah_cicilan_to_data_leasings_table', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `network_monitorings`
--

CREATE TABLE `network_monitorings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `ip_public` varchar(255) NOT NULL,
  `status_koneksi` varchar(255) NOT NULL,
  `bandwidth` varchar(255) NOT NULL,
  `downtime` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `network_monitorings`
--

INSERT INTO `network_monitorings` (`id`, `lokasi`, `ip_public`, `status_koneksi`, `bandwidth`, `downtime`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Kantor Pusat Jakarta', '103.12.45.67', 'Online', '500 Mbps', '0 jam/bulan', 'Koneksi utama Indihome Business', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'Cabang Surabaya', '202.67.88.12', 'Online', '100 Mbps', '2 jam/bulan', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'Gudang Bekasi', '180.244.33.91', 'Warning', '50 Mbps', '5 jam/bulan', 'Sering gangguan sore hari', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'Data Center Cibitung', '103.88.12.200', 'Online', '1 Gbps', '0 jam/bulan', 'Tier 3 data center', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'Cabang Bandung', '36.91.44.111', 'Offline', '100 Mbps', '8 jam/bulan', 'Sedang dalam perbaikan jalur fiber', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `otomatisasis`
--

CREATE TABLE `otomatisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_id` varchar(255) NOT NULL,
  `nama_workflow` varchar(255) NOT NULL,
  `trigger_event` varchar(255) NOT NULL,
  `syarat_tambahan` varchar(255) DEFAULT NULL,
  `aksi` varchar(255) NOT NULL,
  `delay_aksi` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `pic` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `otomatisasis`
--

INSERT INTO `otomatisasis` (`id`, `workflow_id`, `nama_workflow`, `trigger_event`, `syarat_tambahan`, `aksi`, `delay_aksi`, `status`, `pic`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'WF001', 'Welcome Email', 'Registrasi Baru', 'Member baru', 'Kirim Email Selamat Datang', '10 menit', 'Aktif', 'System', 'Auto-email untuk member baru', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'WF002', 'Reminder Pembayaran', 'H-2 Jatuh Tempo', 'Belum bayar', 'Kirim Notifikasi WA', 'Langsung', 'Aktif', 'Finance', 'Pengingat otomatis pembayaran', '2026-08-10 23:55:33', '2026-08-10 23:55:33');

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
(1, 1, 'Pajak Tahunan', 2600000.00, '2026-08-30', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(2, 2, 'Pajak 5 Tahunan', 1100000.00, '2026-08-15', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(3, 3, 'STNK', 1100000.00, '2026-09-09', '2026-07-20', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(4, 4, 'BPKB', 1400000.00, '2027-01-01', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(5, 5, 'BBN-KB', 3000000.00, '2027-03-16', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(6, 6, 'Pajak Tahunan', 900000.00, '2026-08-08', '2026-08-03', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(7, 7, 'Pajak 5 Tahunan', 2800000.00, '2026-12-23', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(8, 8, 'STNK', 1500000.00, '2026-09-16', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(9, 9, 'BPKB', 5500000.00, '2027-04-27', '2026-08-04', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(10, 10, 'BBN-KB', 1600000.00, '2026-12-02', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(11, 11, 'Pajak Tahunan', 2700000.00, '2027-05-19', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(12, 12, 'Pajak 5 Tahunan', 1100000.00, '2026-08-12', '2026-07-22', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(13, 13, 'STNK', 4500000.00, '2026-07-12', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(14, 14, 'BPKB', 4500000.00, '2027-04-17', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(15, 15, 'BBN-KB', 4500000.00, '2026-08-11', '2026-08-07', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(16, 16, 'Pajak Tahunan', 4400000.00, '2027-04-28', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(17, 17, 'Pajak 5 Tahunan', 800000.00, '2026-10-19', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(18, 18, 'STNK', 1400000.00, '2026-11-14', '2026-07-19', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(19, 19, 'BPKB', 4500000.00, '2026-12-09', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(20, 20, 'BBN-KB', 2500000.00, '2026-11-14', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(21, 21, 'Pajak Tahunan', 2600000.00, '2027-02-10', '2026-07-26', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(22, 22, 'Pajak 5 Tahunan', 1200000.00, '2027-05-21', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(23, 23, 'STNK', 2800000.00, '2027-03-31', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(24, 24, 'BPKB', 500000.00, '2027-01-11', '2026-07-19', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(25, 25, 'BBN-KB', 1700000.00, '2026-09-17', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(26, 26, 'Pajak Tahunan', 1500000.00, '2027-06-12', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(27, 27, 'Pajak 5 Tahunan', 900000.00, '2026-12-31', '2026-08-09', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(28, 28, 'STNK', 2100000.00, '2027-06-25', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(29, 29, 'BPKB', 1200000.00, '2026-11-14', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(30, 30, 'BBN-KB', 2500000.00, '2027-08-07', '2026-07-30', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(31, 31, 'Pajak Tahunan', 4500000.00, '2026-07-25', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(32, 32, 'Pajak 5 Tahunan', 2100000.00, '2027-08-04', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(33, 33, 'STNK', 5800000.00, '2027-02-03', '2026-07-26', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(34, 34, 'BPKB', 2600000.00, '2027-04-16', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(35, 35, 'BBN-KB', 2700000.00, '2027-04-11', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(36, 36, 'Pajak Tahunan', 4600000.00, '2026-10-23', '2026-07-25', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(37, 37, 'Pajak 5 Tahunan', 1400000.00, '2026-12-07', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(38, 38, 'STNK', 1600000.00, '2027-04-12', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(39, 39, 'BPKB', 700000.00, '2027-03-02', '2026-08-06', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(40, 40, 'BBN-KB', 1500000.00, '2026-11-23', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(41, 41, 'Pajak Tahunan', 2600000.00, '2026-09-20', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(42, 42, 'Pajak 5 Tahunan', 4900000.00, '2026-11-18', '2026-07-24', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(43, 43, 'STNK', 3800000.00, '2026-10-04', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(44, 44, 'BPKB', 900000.00, '2026-08-05', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(45, 45, 'BBN-KB', 5200000.00, '2027-07-05', '2026-07-12', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(46, 46, 'Pajak Tahunan', 5600000.00, '2026-12-20', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(47, 47, 'Pajak 5 Tahunan', 5800000.00, '2026-12-31', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(48, 48, 'STNK', 2500000.00, '2027-03-12', '2026-07-31', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(49, 49, 'BPKB', 3600000.00, '2027-06-25', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27'),
(50, 50, 'BBN-KB', 1600000.00, '2027-01-09', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-08-10 23:55:27', '2026-08-10 23:55:27');

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
-- Struktur dari tabel `payrolls`
--

CREATE TABLE `payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL,
  `tunjangan` decimal(15,2) NOT NULL,
  `thr` decimal(15,2) NOT NULL,
  `bpjs` decimal(15,2) NOT NULL,
  `pph21` decimal(15,2) NOT NULL,
  `total_gaji` decimal(15,2) NOT NULL,
  `slip_gaji` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `payrolls`
--

INSERT INTO `payrolls` (`id`, `nama_pegawai`, `gaji_pokok`, `tunjangan`, `thr`, `bpjs`, `pph21`, `total_gaji`, `slip_gaji`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', 25000000.00, 5000000.00, 25000000.00, 500000.00, 2500000.00, 27000000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(2, 'Siti Rahayu', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(3, 'Agus Wibowo', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(4, 'Dewi Kusuma', 12000000.00, 2000000.00, 12000000.00, 240000.00, 600000.00, 13160000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(5, 'Rini Apriani', 6000000.00, 1000000.00, 6000000.00, 120000.00, 150000.00, 6730000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(6, 'Eko Prasetyo', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(7, 'Hendra Gunawan', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(8, 'Rizky Fadillah', 7000000.00, 1200000.00, 7000000.00, 140000.00, 200000.00, 7860000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(9, 'Yusuf Hidayat', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(10, 'Linda Permata', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(11, 'Wahyu Nugroho', 7500000.00, 1200000.00, 7500000.00, 150000.00, 220000.00, 8330000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(12, 'Fitri Handayani', 6500000.00, 1000000.00, 6500000.00, 130000.00, 170000.00, 7200000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(13, 'Dody Kurniawan', 11000000.00, 2000000.00, 11000000.00, 220000.00, 550000.00, 12230000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(14, 'Teguh Santosa', 8000000.00, 1500000.00, 8000000.00, 160000.00, 280000.00, 9060000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(15, 'Arif Budiman', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-08-10 23:55:39', '2026-08-10 23:55:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembelian_proyeks`
--

CREATE TABLE `pembelian_proyeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pr_no` varchar(255) NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `item_diminta` varchar(255) NOT NULL,
  `qty` bigint(20) NOT NULL DEFAULT 0,
  `vendor` varchar(255) DEFAULT NULL,
  `estimasi_harga` bigint(20) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL,
  `tgl_permintaan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembelian_proyeks`
--

INSERT INTO `pembelian_proyeks` (`id`, `pr_no`, `proyek`, `item_diminta`, `qty`, `vendor`, `estimasi_harga`, `status`, `tgl_permintaan`, `created_at`, `updated_at`) VALUES
(1, 'PR-PRJ001-001', 'PRJ001', 'Semen Portland 40kg', 500, 'PT Semen Indonesia', 20000000, 'Disetujui', '2026-01-08', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(2, 'PR-PRJ001-002', 'PRJ001', 'Besi Beton 10mm', 200, 'PT Krakatau Steel', 35000000, 'Disetujui', '2026-01-10', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(3, 'PR-PRJ001-003', 'PRJ001', 'Bata Merah 20x10x5', 5000, 'CV Bata Kuat', 10000000, 'Disetujui', '2026-01-12', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(4, 'PR-PRJ001-004', 'PRJ001', 'Cat Tembok & Finishing', 50, 'PT Nippon Paint', 15000000, 'Pending', '2026-02-20', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(5, 'PR-PRJ002-001', 'PRJ002', 'Bus Pariwisata 32 Seat', 3, 'PT Hino Motors', 1200000000, 'Disetujui', '2026-02-05', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(6, 'PR-PRJ002-002', 'PRJ002', 'Wrapping & Branding Bus', 3, 'CV Kreatif Visual', 15000000, 'Pending', '2026-04-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(7, 'PR-PRJ003-001', 'PRJ003', 'Unit GPS Tracker', 50, 'PT TechMaps', 100000000, 'Disetujui', '2026-01-14', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(8, 'PR-PRJ003-002', 'PRJ003', 'Server Dashboard Cloud', 1, 'PT AWS Indonesia', 24000000, 'Disetujui', '2026-01-15', '2026-08-10 23:55:34', '2026-08-10 23:55:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeliharaan_assets`
--

CREATE TABLE `pemeliharaan_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `tanggal_service` date NOT NULL,
  `jenis_service` varchar(255) NOT NULL,
  `vendor_pic` varchar(255) NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `jadwal_selanjutnya` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penanggung_jawabs`
--

CREATE TABLE `penanggung_jawabs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `tanggal_penempatan` date NOT NULL,
  `divisi` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penawarans`
--

CREATE TABLE `penawarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_quotation` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `produk_jasa` varchar(255) NOT NULL,
  `jumlah` bigint(20) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `valid_sampai` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penawarans`
--

INSERT INTO `penawarans` (`id`, `no_quotation`, `tanggal`, `pelanggan`, `produk_jasa`, `jumlah`, `harga_satuan`, `total_harga`, `status`, `valid_sampai`, `created_at`, `updated_at`) VALUES
(1, 'QUO-2026-001', '2026-01-15', 'PT Maju Bersama', 'Sewa Minibus', 2, 5000000.00, 10000000.00, 'Disetujui', '2026-02-15', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'QUO-2026-002', '2026-01-20', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 8000000.00, 'Terkirim', '2026-02-20', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'QUO-2026-003', '2026-02-01', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 3500000.00, 10500000.00, 'Draft', '2026-03-01', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'QUO-2026-004', '2026-02-10', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 15000000.00, 'Disetujui', '2026-03-10', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'QUO-2026-005', '2026-02-25', 'CV Jaya Mandiri', 'Sewa MPV', 4, 4000000.00, 16000000.00, 'Terkirim', '2026-03-25', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'QUO-2026-006', '2026-03-05', 'PT Nusantara Raya', 'Sewa Minibus', 2, 5500000.00, 11000000.00, 'Disetujui', '2026-04-05', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'QUO-2026-007', '2026-03-15', 'PT Sinar Harapan', 'Sewa SUV', 2, 6000000.00, 12000000.00, 'Ditolak', '2026-04-15', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'QUO-2026-008', '2026-04-01', 'CV Mitra Logistik', 'Sewa Truk', 3, 7500000.00, 22500000.00, 'Terkirim', '2026-05-01', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'QUO-2026-009', '2026-04-20', 'PT Berlian Trans', 'Sewa Bus Medium', 2, 10000000.00, 20000000.00, 'Disetujui', '2026-05-20', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'QUO-2026-010', '2026-05-10', 'PT Prima Raya', 'Sewa Sedan', 5, 3000000.00, 15000000.00, 'Draft', '2026-06-10', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(11, 'QUO-001', '2026-02-17', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 8, 2958949.00, 23671592.00, 'Draft', '2026-03-20', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(12, 'QUO-002', '2026-03-29', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 4, 1341559.00, 5366236.00, 'Terkirim', '2026-05-27', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(13, 'QUO-003', '2026-03-06', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 6, 3840769.00, 23044614.00, 'Disetujui', '2026-04-06', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(14, 'QUO-004', '2026-07-23', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 10, 3491769.00, 34917690.00, 'Ditolak', '2026-09-17', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(15, 'QUO-005', '2026-03-04', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 4, 1482635.00, 5930540.00, 'Draft', '2026-03-31', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(16, 'QUO-006', '2026-03-19', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 5, 3379617.00, 16898085.00, 'Terkirim', '2026-04-25', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(17, 'QUO-007', '2026-06-30', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 7, 3288933.00, 23022531.00, 'Disetujui', '2026-08-08', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(18, 'QUO-008', '2026-08-04', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 3, 4824281.00, 14472843.00, 'Ditolak', '2026-09-28', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(19, 'QUO-009', '2026-04-07', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 7, 3324073.00, 23268511.00, 'Draft', '2026-05-14', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(20, 'QUO-010', '2026-03-17', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 1, 4562011.00, 4562011.00, 'Terkirim', '2026-04-15', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(21, 'QUO-011', '2026-07-13', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 1, 1818288.00, 1818288.00, 'Disetujui', '2026-09-06', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(22, 'QUO-012', '2026-06-28', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 2, 4096912.00, 8193824.00, 'Ditolak', '2026-07-14', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(23, 'QUO-013', '2026-02-19', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 5, 4221736.00, 21108680.00, 'Draft', '2026-04-05', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(24, 'QUO-014', '2026-04-19', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 3, 2545657.00, 7636971.00, 'Terkirim', '2026-06-05', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(25, 'QUO-015', '2026-07-21', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 8, 2524421.00, 20195368.00, 'Disetujui', '2026-08-09', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(26, 'QUO-016', '2026-03-01', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 9, 2300824.00, 20707416.00, 'Ditolak', '2026-04-09', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(27, 'QUO-017', '2026-05-25', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 3, 1316161.00, 3948483.00, 'Draft', '2026-06-14', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(28, 'QUO-018', '2026-05-22', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 8, 1930834.00, 15446672.00, 'Terkirim', '2026-07-17', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(29, 'QUO-019', '2026-04-09', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 6, 1490209.00, 8941254.00, 'Disetujui', '2026-05-04', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(30, 'QUO-020', '2026-06-18', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 7, 3574997.00, 25024979.00, 'Ditolak', '2026-08-08', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(31, 'QUO-021', '2026-06-17', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 4, 1399748.00, 5598992.00, 'Draft', '2026-08-15', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(32, 'QUO-022', '2026-06-06', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 10, 2343394.00, 23433940.00, 'Terkirim', '2026-06-21', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(33, 'QUO-023', '2026-06-17', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 9, 3253630.00, 29282670.00, 'Disetujui', '2026-08-05', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(34, 'QUO-024', '2026-06-15', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 10, 2484447.00, 24844470.00, 'Ditolak', '2026-07-04', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(35, 'QUO-025', '2026-07-12', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 10, 717005.00, 7170050.00, 'Draft', '2026-08-11', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(36, 'QUO-026', '2026-05-12', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 10, 2600209.00, 26002090.00, 'Terkirim', '2026-06-16', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(37, 'QUO-027', '2026-02-19', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 2, 3302934.00, 6605868.00, 'Disetujui', '2026-04-02', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(38, 'QUO-028', '2026-02-28', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 4, 4978410.00, 19913640.00, 'Ditolak', '2026-04-25', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(39, 'QUO-029', '2026-07-05', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 2, 4663897.00, 9327794.00, 'Draft', '2026-08-24', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(40, 'QUO-030', '2026-02-26', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 8, 890479.00, 7123832.00, 'Terkirim', '2026-04-15', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyusutan_assets`
--

CREATE TABLE `penyusutan_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `tahun` year(4) NOT NULL,
  `nilai_awal` decimal(15,2) NOT NULL,
  `akumulasi_penyusutan` decimal(15,2) NOT NULL,
  `nilai_buku` decimal(15,2) NOT NULL,
  `metode` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pergerakan_assets`
--

CREATE TABLE `pergerakan_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_pergerakan` varchar(255) NOT NULL,
  `dari_lokasi` varchar(255) NOT NULL,
  `ke_lokasi` varchar(255) NOT NULL,
  `dilakukan_oleh` varchar(255) NOT NULL,
  `disetujui_oleh` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `perolehan_assets`
--

CREATE TABLE `perolehan_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_perolehan` date NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `metode_pembelian` varchar(255) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `pembayaran` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `policy_compliances`
--

CREATE TABLE `policy_compliances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `versi` varchar(255) NOT NULL,
  `tanggal_berlaku` date NOT NULL,
  `tanggung_jawab` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `sertifikasi_terkait` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `policy_compliances`
--

INSERT INTO `policy_compliances` (`id`, `nama_dokumen`, `versi`, `tanggal_berlaku`, `tanggung_jawab`, `status`, `sertifikasi_terkait`, `created_at`, `updated_at`) VALUES
(1, 'Kebijakan Keamanan Informasi', 'v2.1', '2024-01-01', 'IT Manager', 'Aktif', 'ISO 27001', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'Prosedur Backup & Recovery', 'v1.3', '2024-03-01', 'System Administrator', 'Aktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'Kebijakan Penggunaan Aset IT', 'v1.0', '2024-06-01', 'HR & IT Manager', 'Draft', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'Disaster Recovery Plan', 'v3.0', '2023-07-01', 'CTO', 'Review', 'ISO 22301', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'Kebijakan Password & Akses', 'v2.0', '2024-01-01', 'IT Security Officer', 'Aktif', 'ISO 27001', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensis`
--

CREATE TABLE `presensis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `metode_presensi` varchar(255) NOT NULL,
  `lokasi_presensi` varchar(255) NOT NULL,
  `status` enum('Hadir','Alpa','Izin','Terlambat') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `presensis`
--

INSERT INTO `presensis` (`id`, `nama_pegawai`, `tanggal`, `jam_masuk`, `jam_pulang`, `metode_presensi`, `lokasi_presensi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '2026-07-13', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(2, 'Dewi Kusuma', '2026-07-13', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(3, 'Rini Apriani', '2026-07-13', '08:08:00', '18:52:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(4, 'Eko Prasetyo', '2026-07-13', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(5, 'Hendra Gunawan', '2026-07-13', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(6, 'Rizky Fadillah', '2026-07-13', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(7, 'Yusuf Hidayat', '2026-07-13', '07:41:00', '17:10:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(8, 'Linda Permata', '2026-07-13', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(9, 'Wahyu Nugroho', '2026-07-13', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(10, 'Fitri Handayani', '2026-07-13', '08:57:00', '18:47:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(11, 'Dody Kurniawan', '2026-07-13', '07:10:00', '17:02:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(12, 'Teguh Santosa', '2026-07-13', '08:09:00', '18:11:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(13, 'Budi Santoso', '2026-07-14', '07:39:00', '17:22:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(14, 'Dewi Kusuma', '2026-07-14', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(15, 'Rini Apriani', '2026-07-14', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(16, 'Eko Prasetyo', '2026-07-14', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(17, 'Hendra Gunawan', '2026-07-14', '07:43:00', '18:22:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(18, 'Rizky Fadillah', '2026-07-14', '08:37:00', '17:45:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(19, 'Yusuf Hidayat', '2026-07-14', '08:36:00', '17:54:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(20, 'Linda Permata', '2026-07-14', '07:22:00', '18:00:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(21, 'Wahyu Nugroho', '2026-07-14', '07:17:00', '18:25:00', 'Face ID', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(22, 'Fitri Handayani', '2026-07-14', '08:37:00', '17:14:00', 'Manual', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(23, 'Dody Kurniawan', '2026-07-14', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(24, 'Teguh Santosa', '2026-07-14', '07:31:00', '18:16:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(25, 'Budi Santoso', '2026-07-15', '07:07:00', '18:10:00', 'GPS', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(26, 'Dewi Kusuma', '2026-07-15', '08:02:00', '18:45:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(27, 'Rini Apriani', '2026-07-15', '08:30:00', '17:24:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(28, 'Eko Prasetyo', '2026-07-15', '08:46:00', '17:30:00', 'GPS', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(29, 'Hendra Gunawan', '2026-07-15', '09:48:00', '18:53:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(30, 'Rizky Fadillah', '2026-07-15', '08:53:00', '18:02:00', 'GPS', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(31, 'Yusuf Hidayat', '2026-07-15', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(32, 'Linda Permata', '2026-07-15', '07:34:00', '18:10:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(33, 'Wahyu Nugroho', '2026-07-15', '08:28:00', '18:45:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(34, 'Fitri Handayani', '2026-07-15', '08:42:00', '17:45:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(35, 'Dody Kurniawan', '2026-07-15', '07:27:00', '18:33:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(36, 'Teguh Santosa', '2026-07-15', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(37, 'Budi Santoso', '2026-07-16', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(38, 'Dewi Kusuma', '2026-07-16', '08:42:00', '18:31:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(39, 'Rini Apriani', '2026-07-16', '07:45:00', '18:31:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(40, 'Eko Prasetyo', '2026-07-16', '07:33:00', '18:38:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(41, 'Hendra Gunawan', '2026-07-16', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(42, 'Rizky Fadillah', '2026-07-16', '07:45:00', '17:55:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(43, 'Yusuf Hidayat', '2026-07-16', '08:28:00', '17:20:00', 'GPS', 'Lapangan', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(44, 'Linda Permata', '2026-07-16', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(45, 'Wahyu Nugroho', '2026-07-16', '08:03:00', '17:49:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(46, 'Fitri Handayani', '2026-07-16', '08:57:00', '17:05:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(47, 'Dody Kurniawan', '2026-07-16', '08:08:00', '18:05:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(48, 'Teguh Santosa', '2026-07-16', '08:29:00', '17:50:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(49, 'Budi Santoso', '2026-07-17', '07:37:00', '18:51:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(50, 'Dewi Kusuma', '2026-07-17', '08:55:00', '17:12:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(51, 'Rini Apriani', '2026-07-17', '08:42:00', '18:29:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(52, 'Eko Prasetyo', '2026-07-17', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(53, 'Hendra Gunawan', '2026-07-17', '07:23:00', '18:42:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(54, 'Rizky Fadillah', '2026-07-17', '07:40:00', '17:23:00', 'GPS', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(55, 'Yusuf Hidayat', '2026-07-17', '07:00:00', '17:13:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(56, 'Linda Permata', '2026-07-17', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(57, 'Wahyu Nugroho', '2026-07-17', '08:49:00', '18:36:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(58, 'Fitri Handayani', '2026-07-17', '08:35:00', '18:25:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(59, 'Dody Kurniawan', '2026-07-17', '09:42:00', '17:33:00', 'GPS', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(60, 'Teguh Santosa', '2026-07-17', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(61, 'Budi Santoso', '2026-07-20', '08:10:00', '18:22:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(62, 'Dewi Kusuma', '2026-07-20', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(63, 'Rini Apriani', '2026-07-20', '09:24:00', '17:45:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(64, 'Eko Prasetyo', '2026-07-20', '08:42:00', '17:07:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(65, 'Hendra Gunawan', '2026-07-20', '07:46:00', '17:17:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(66, 'Rizky Fadillah', '2026-07-20', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(67, 'Yusuf Hidayat', '2026-07-20', '07:33:00', '17:00:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(68, 'Linda Permata', '2026-07-20', '08:29:00', '17:17:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(69, 'Wahyu Nugroho', '2026-07-20', '07:33:00', '18:09:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(70, 'Fitri Handayani', '2026-07-20', '07:42:00', '17:25:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(71, 'Dody Kurniawan', '2026-07-20', '07:41:00', '17:32:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(72, 'Teguh Santosa', '2026-07-20', '07:13:00', '17:43:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(73, 'Budi Santoso', '2026-07-21', '07:56:00', '18:37:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(74, 'Dewi Kusuma', '2026-07-21', '09:57:00', '18:17:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(75, 'Rini Apriani', '2026-07-21', '08:26:00', '17:57:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(76, 'Eko Prasetyo', '2026-07-21', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(77, 'Hendra Gunawan', '2026-07-21', '07:12:00', '18:18:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(78, 'Rizky Fadillah', '2026-07-21', '07:14:00', '17:21:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(79, 'Yusuf Hidayat', '2026-07-21', '07:31:00', '17:07:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(80, 'Linda Permata', '2026-07-21', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(81, 'Wahyu Nugroho', '2026-07-21', '08:19:00', '18:39:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(82, 'Fitri Handayani', '2026-07-21', '08:13:00', '17:12:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(83, 'Dody Kurniawan', '2026-07-21', '09:41:00', '18:21:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(84, 'Teguh Santosa', '2026-07-21', '07:12:00', '18:39:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(85, 'Budi Santoso', '2026-07-22', '07:55:00', '18:13:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(86, 'Dewi Kusuma', '2026-07-22', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(87, 'Rini Apriani', '2026-07-22', '07:59:00', '17:34:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(88, 'Eko Prasetyo', '2026-07-22', '09:38:00', '18:06:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(89, 'Hendra Gunawan', '2026-07-22', '07:00:00', '17:55:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(90, 'Rizky Fadillah', '2026-07-22', '08:07:00', '17:52:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(91, 'Yusuf Hidayat', '2026-07-22', '08:53:00', '18:58:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(92, 'Linda Permata', '2026-07-22', '07:36:00', '18:51:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(93, 'Wahyu Nugroho', '2026-07-22', '07:21:00', '17:22:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(94, 'Fitri Handayani', '2026-07-22', '08:13:00', '17:08:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(95, 'Dody Kurniawan', '2026-07-22', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(96, 'Teguh Santosa', '2026-07-22', '07:24:00', '17:24:00', 'Face ID', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(97, 'Budi Santoso', '2026-07-23', '09:40:00', '18:22:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(98, 'Dewi Kusuma', '2026-07-23', '07:13:00', '18:58:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(99, 'Rini Apriani', '2026-07-23', '07:21:00', '17:33:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(100, 'Eko Prasetyo', '2026-07-23', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(101, 'Hendra Gunawan', '2026-07-23', '07:59:00', '17:39:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(102, 'Rizky Fadillah', '2026-07-23', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(103, 'Yusuf Hidayat', '2026-07-23', '07:43:00', '17:18:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(104, 'Linda Permata', '2026-07-23', '07:26:00', '17:16:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(105, 'Wahyu Nugroho', '2026-07-23', '07:49:00', '18:32:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(106, 'Fitri Handayani', '2026-07-23', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(107, 'Dody Kurniawan', '2026-07-23', '07:46:00', '18:28:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(108, 'Teguh Santosa', '2026-07-23', '08:13:00', '18:12:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(109, 'Budi Santoso', '2026-07-24', '07:40:00', '17:53:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(110, 'Dewi Kusuma', '2026-07-24', '07:54:00', '18:19:00', 'GPS', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(111, 'Rini Apriani', '2026-07-24', '09:22:00', '17:05:00', 'Face ID', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(112, 'Eko Prasetyo', '2026-07-24', '08:49:00', '18:51:00', 'GPS', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(113, 'Hendra Gunawan', '2026-07-24', '07:30:00', '17:45:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(114, 'Rizky Fadillah', '2026-07-24', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(115, 'Yusuf Hidayat', '2026-07-24', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(116, 'Linda Permata', '2026-07-24', '08:11:00', '18:35:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(117, 'Wahyu Nugroho', '2026-07-24', '08:12:00', '17:43:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(118, 'Fitri Handayani', '2026-07-24', '07:49:00', '17:43:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(119, 'Dody Kurniawan', '2026-07-24', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(120, 'Teguh Santosa', '2026-07-24', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(121, 'Budi Santoso', '2026-07-27', '08:20:00', '18:29:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(122, 'Dewi Kusuma', '2026-07-27', '07:30:00', '17:28:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(123, 'Rini Apriani', '2026-07-27', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(124, 'Eko Prasetyo', '2026-07-27', '08:45:00', '18:55:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(125, 'Hendra Gunawan', '2026-07-27', '08:50:00', '18:28:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(126, 'Rizky Fadillah', '2026-07-27', '07:05:00', '18:38:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(127, 'Yusuf Hidayat', '2026-07-27', '08:38:00', '17:34:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(128, 'Linda Permata', '2026-07-27', '08:48:00', '18:53:00', 'Face ID', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(129, 'Wahyu Nugroho', '2026-07-27', '07:39:00', '17:54:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(130, 'Fitri Handayani', '2026-07-27', '08:16:00', '17:17:00', 'Manual', 'Lapangan', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(131, 'Dody Kurniawan', '2026-07-27', '09:48:00', '17:27:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(132, 'Teguh Santosa', '2026-07-27', '08:37:00', '18:42:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(133, 'Budi Santoso', '2026-07-28', '08:34:00', '18:58:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(134, 'Dewi Kusuma', '2026-07-28', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(135, 'Rini Apriani', '2026-07-28', '08:41:00', '18:23:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(136, 'Eko Prasetyo', '2026-07-28', '08:32:00', '17:46:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(137, 'Hendra Gunawan', '2026-07-28', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(138, 'Rizky Fadillah', '2026-07-28', '08:50:00', '17:32:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(139, 'Yusuf Hidayat', '2026-07-28', '07:06:00', '17:28:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(140, 'Linda Permata', '2026-07-28', '07:12:00', '17:48:00', 'GPS', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(141, 'Wahyu Nugroho', '2026-07-28', '08:48:00', '18:34:00', 'Face ID', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(142, 'Fitri Handayani', '2026-07-28', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(143, 'Dody Kurniawan', '2026-07-28', '08:17:00', '18:15:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(144, 'Teguh Santosa', '2026-07-28', '07:03:00', '18:07:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(145, 'Budi Santoso', '2026-07-29', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(146, 'Dewi Kusuma', '2026-07-29', '08:53:00', '17:06:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(147, 'Rini Apriani', '2026-07-29', '09:22:00', '17:37:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(148, 'Eko Prasetyo', '2026-07-29', '07:46:00', '17:30:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(149, 'Hendra Gunawan', '2026-07-29', '07:24:00', '18:49:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(150, 'Rizky Fadillah', '2026-07-29', '08:46:00', '18:59:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(151, 'Yusuf Hidayat', '2026-07-29', '07:05:00', '17:59:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(152, 'Linda Permata', '2026-07-29', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(153, 'Wahyu Nugroho', '2026-07-29', '09:12:00', '18:03:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(154, 'Fitri Handayani', '2026-07-29', '07:58:00', '17:33:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(155, 'Dody Kurniawan', '2026-07-29', '09:25:00', '17:57:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(156, 'Teguh Santosa', '2026-07-29', '08:23:00', '18:46:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(157, 'Budi Santoso', '2026-07-30', '07:37:00', '17:33:00', 'Face ID', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(158, 'Dewi Kusuma', '2026-07-30', '07:48:00', '17:49:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(159, 'Rini Apriani', '2026-07-30', '07:28:00', '17:50:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(160, 'Eko Prasetyo', '2026-07-30', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(161, 'Hendra Gunawan', '2026-07-30', '07:27:00', '18:22:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(162, 'Rizky Fadillah', '2026-07-30', '07:25:00', '17:52:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(163, 'Yusuf Hidayat', '2026-07-30', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(164, 'Linda Permata', '2026-07-30', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(165, 'Wahyu Nugroho', '2026-07-30', '08:58:00', '18:31:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(166, 'Fitri Handayani', '2026-07-30', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(167, 'Dody Kurniawan', '2026-07-30', '07:53:00', '18:00:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(168, 'Teguh Santosa', '2026-07-30', '07:09:00', '18:49:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(169, 'Budi Santoso', '2026-07-31', '09:12:00', '18:52:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(170, 'Dewi Kusuma', '2026-07-31', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(171, 'Rini Apriani', '2026-07-31', '07:18:00', '17:29:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(172, 'Eko Prasetyo', '2026-07-31', '07:04:00', '17:29:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(173, 'Hendra Gunawan', '2026-07-31', '07:58:00', '18:03:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(174, 'Rizky Fadillah', '2026-07-31', '08:35:00', '18:26:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(175, 'Yusuf Hidayat', '2026-07-31', '08:53:00', '17:08:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(176, 'Linda Permata', '2026-07-31', '07:27:00', '18:52:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(177, 'Wahyu Nugroho', '2026-07-31', '08:24:00', '18:41:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(178, 'Fitri Handayani', '2026-07-31', '07:29:00', '17:04:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(179, 'Dody Kurniawan', '2026-07-31', '08:57:00', '18:09:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(180, 'Teguh Santosa', '2026-07-31', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(181, 'Budi Santoso', '2026-08-03', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(182, 'Dewi Kusuma', '2026-08-03', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(183, 'Rini Apriani', '2026-08-03', '08:54:00', '17:38:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(184, 'Eko Prasetyo', '2026-08-03', '07:04:00', '17:01:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(185, 'Hendra Gunawan', '2026-08-03', '08:04:00', '17:29:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(186, 'Rizky Fadillah', '2026-08-03', '07:24:00', '17:26:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(187, 'Yusuf Hidayat', '2026-08-03', '07:41:00', '18:55:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(188, 'Linda Permata', '2026-08-03', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(189, 'Wahyu Nugroho', '2026-08-03', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(190, 'Fitri Handayani', '2026-08-03', '08:11:00', '17:47:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(191, 'Dody Kurniawan', '2026-08-03', '07:26:00', '18:44:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(192, 'Teguh Santosa', '2026-08-03', '08:21:00', '17:52:00', 'Manual', 'Lapangan', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(193, 'Budi Santoso', '2026-08-04', '08:02:00', '17:51:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(194, 'Dewi Kusuma', '2026-08-04', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(195, 'Rini Apriani', '2026-08-04', '07:58:00', '18:25:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(196, 'Eko Prasetyo', '2026-08-04', '08:26:00', '18:24:00', 'Manual', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(197, 'Hendra Gunawan', '2026-08-04', '09:46:00', '17:59:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(198, 'Rizky Fadillah', '2026-08-04', '07:55:00', '18:46:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(199, 'Yusuf Hidayat', '2026-08-04', '07:08:00', '18:12:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(200, 'Linda Permata', '2026-08-04', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(201, 'Wahyu Nugroho', '2026-08-04', '07:51:00', '18:34:00', 'Face ID', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(202, 'Fitri Handayani', '2026-08-04', '07:05:00', '17:00:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(203, 'Dody Kurniawan', '2026-08-04', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(204, 'Teguh Santosa', '2026-08-04', '07:40:00', '17:25:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(205, 'Budi Santoso', '2026-08-05', '08:49:00', '17:50:00', 'Manual', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(206, 'Dewi Kusuma', '2026-08-05', '08:37:00', '17:41:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(207, 'Rini Apriani', '2026-08-05', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(208, 'Eko Prasetyo', '2026-08-05', '08:03:00', '17:21:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(209, 'Hendra Gunawan', '2026-08-05', '07:27:00', '18:58:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(210, 'Rizky Fadillah', '2026-08-05', '08:25:00', '17:59:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(211, 'Yusuf Hidayat', '2026-08-05', '07:04:00', '17:15:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(212, 'Linda Permata', '2026-08-05', '08:53:00', '18:20:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(213, 'Wahyu Nugroho', '2026-08-05', '09:54:00', '17:23:00', 'Manual', 'Lapangan', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(214, 'Fitri Handayani', '2026-08-05', '07:56:00', '18:19:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(215, 'Dody Kurniawan', '2026-08-05', '07:57:00', '17:20:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(216, 'Teguh Santosa', '2026-08-05', '07:19:00', '17:39:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(217, 'Budi Santoso', '2026-08-06', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(218, 'Dewi Kusuma', '2026-08-06', '08:34:00', '17:44:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(219, 'Rini Apriani', '2026-08-06', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(220, 'Eko Prasetyo', '2026-08-06', '08:10:00', '17:09:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(221, 'Hendra Gunawan', '2026-08-06', '08:04:00', '17:20:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(222, 'Rizky Fadillah', '2026-08-06', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(223, 'Yusuf Hidayat', '2026-08-06', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(224, 'Linda Permata', '2026-08-06', '08:30:00', '18:16:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(225, 'Wahyu Nugroho', '2026-08-06', '09:54:00', '18:54:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(226, 'Fitri Handayani', '2026-08-06', '09:22:00', '17:08:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(227, 'Dody Kurniawan', '2026-08-06', '08:50:00', '18:51:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(228, 'Teguh Santosa', '2026-08-06', '07:31:00', '18:12:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(229, 'Budi Santoso', '2026-08-07', '08:27:00', '17:07:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(230, 'Dewi Kusuma', '2026-08-07', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(231, 'Rini Apriani', '2026-08-07', '08:17:00', '17:50:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(232, 'Eko Prasetyo', '2026-08-07', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(233, 'Hendra Gunawan', '2026-08-07', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(234, 'Rizky Fadillah', '2026-08-07', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(235, 'Yusuf Hidayat', '2026-08-07', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(236, 'Linda Permata', '2026-08-07', '09:45:00', '18:06:00', 'Manual', 'Lapangan', 'Terlambat', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(237, 'Wahyu Nugroho', '2026-08-07', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(238, 'Fitri Handayani', '2026-08-07', '07:33:00', '18:08:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(239, 'Dody Kurniawan', '2026-08-07', '08:39:00', '17:09:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(240, 'Teguh Santosa', '2026-08-07', '08:12:00', '17:12:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(241, 'Budi Santoso', '2026-08-10', '08:21:00', '17:41:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(242, 'Dewi Kusuma', '2026-08-10', '08:44:00', '18:30:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(243, 'Rini Apriani', '2026-08-10', '07:45:00', '17:18:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(244, 'Eko Prasetyo', '2026-08-10', '08:04:00', '18:16:00', 'Face ID', 'Lapangan', 'Hadir', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(245, 'Hendra Gunawan', '2026-08-10', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-08-10 23:55:38', '2026-08-10 23:55:38'),
(246, 'Rizky Fadillah', '2026-08-10', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(247, 'Yusuf Hidayat', '2026-08-10', '07:42:00', '18:52:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(248, 'Linda Permata', '2026-08-10', '08:09:00', '18:19:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(249, 'Wahyu Nugroho', '2026-08-10', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(250, 'Fitri Handayani', '2026-08-10', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(251, 'Dody Kurniawan', '2026-08-10', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(252, 'Teguh Santosa', '2026-08-10', '07:51:00', '18:33:00', 'Fingerprint', 'WFH', 'Hadir', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(253, 'Budi Santoso', '2026-08-11', '08:43:00', '17:52:00', 'GPS', 'WFH', 'Hadir', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(254, 'Dewi Kusuma', '2026-08-11', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(255, 'Rini Apriani', '2026-08-11', '08:27:00', '17:17:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(256, 'Eko Prasetyo', '2026-08-11', '08:58:00', '17:30:00', 'Manual', 'Lapangan', 'Terlambat', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(257, 'Hendra Gunawan', '2026-08-11', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(258, 'Rizky Fadillah', '2026-08-11', '07:05:00', '17:00:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(259, 'Yusuf Hidayat', '2026-08-11', '09:28:00', '17:16:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(260, 'Linda Permata', '2026-08-11', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(261, 'Wahyu Nugroho', '2026-08-11', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Izin', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(262, 'Fitri Handayani', '2026-08-11', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(263, 'Dody Kurniawan', '2026-08-11', '08:41:00', '18:06:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(264, 'Teguh Santosa', '2026-08-11', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-08-10 23:55:39', '2026-08-10 23:55:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pricelist_diskons`
--

CREATE TABLE `pricelist_diskons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_harga` varchar(255) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `level_pelanggan` varchar(255) NOT NULL,
  `harga_normal` decimal(15,2) NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT 0.00,
  `harga_diskon` decimal(15,2) NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_selesai` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pricelist_diskons`
--

INSERT INTO `pricelist_diskons` (`id`, `id_harga`, `nama_produk`, `level_pelanggan`, `harga_normal`, `diskon`, `harga_diskon`, `periode_mulai`, `periode_selesai`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRC-001', 'Sewa Sedan', 'Regular', 3500000.00, 0.00, 3500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'PRC-002', 'Sewa Sedan', 'Silver', 3500000.00, 5.00, 3325000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'PRC-003', 'Sewa Sedan', 'Gold', 3500000.00, 10.00, 3150000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'PRC-004', 'Sewa Minibus', 'Regular', 5000000.00, 0.00, 5000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'PRC-005', 'Sewa Minibus', 'Gold', 5000000.00, 10.00, 4500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'PRC-006', 'Sewa Minibus', 'Platinum', 5000000.00, 15.00, 4250000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'PRC-007', 'Sewa Truk', 'Regular', 8000000.00, 0.00, 8000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'PRC-008', 'Sewa Truk', 'Platinum', 8000000.00, 12.00, 7040000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'PRC-009', 'Sewa Bus Besar', 'Regular', 15000000.00, 0.00, 15000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'PRC-010', 'Sewa Bus Besar', 'Gold', 15000000.00, 8.00, 13800000.00, '2026-01-01', '2026-12-31', 'Tidak Aktif', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `procurementos`
--

CREATE TABLE `procurementos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_id` varchar(255) DEFAULT NULL,
  `nama_workflow` varchar(255) DEFAULT NULL,
  `trigger_event` varchar(255) DEFAULT NULL,
  `syarat_tambahan` varchar(255) DEFAULT NULL,
  `aksi_dilakukan` varchar(255) DEFAULT NULL,
  `delay_aksi` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `procurementos`
--

INSERT INTO `procurementos` (`id`, `workflow_id`, `nama_workflow`, `trigger_event`, `syarat_tambahan`, `aksi_dilakukan`, `delay_aksi`, `status`, `pic`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'WF001', 'Persetujuan Pengadaan Barang', 'Pengajuan Barang', 'Nominal > 5.000.000', 'Kirim Email ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Workflow approval pengadaan barang.', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(2, 'WF002', 'Approval Vendor Baru', 'Penambahan Vendor Baru', NULL, 'Kirim Notifikasi ke Admin', '30 Menit', 'Aktif', 'Admin Procurement', 'Workflow untuk approval vendor.', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(3, 'WF003', 'Review Purchase Request', 'PR Diajukan', 'Qty > 100 pcs', 'Kirim ke Manajer Gudang', '2 Jam', 'Aktif', 'Manajer Gudang', 'Workflow review permintaan barang dari gudang.', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(4, 'WF004', 'Approval Kontrak Vendor', 'Kontrak Baru Dibuat', 'Nilai Kontrak > 50.000.000', 'Kirim Email ke Direktur', '1 Hari', 'Aktif', 'Legal & Finance', 'Persetujuan kontrak vendor bernilai besar.', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(5, 'WF005', 'Notifikasi Stok Menipis', 'Stok < Minimum', NULL, 'Kirim Alert ke Procurement', 'Langsung', 'Aktif', 'Procurement', 'Otomatis kirim notifikasi saat stok mendekati batas minimum.', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(6, 'WF006', 'Evaluasi Vendor Periodik', 'Akhir Bulan', 'Rating < 3', 'Kirim Laporan ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Evaluasi performa vendor setiap bulan.', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(7, 'WF007', 'Approval Pembelian Aset', 'Pengajuan Pembelian Aset', 'Nilai > 100.000.000', 'Kirim ke Komite Anggaran', '3 Hari', 'Nonaktif', 'Finance & Direktur', 'Pembelian aset besar perlu persetujuan komite.', '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(8, 'WF008', 'Reminder Jatuh Tempo Kontrak', 'H-30 Kontrak Berakhir', NULL, 'Kirim Email Reminder', 'Langsung', 'Aktif', 'Procurement', 'Pengingat otomatis sebelum kontrak vendor habis.', '2026-08-10 23:55:31', '2026-08-10 23:55:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_costs`
--

CREATE TABLE `project_costs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `kategori_biaya` varchar(255) NOT NULL,
  `estimasi` decimal(15,2) NOT NULL DEFAULT 0.00,
  `realisasi` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selisih` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_costs`
--

INSERT INTO `project_costs` (`id`, `proyek`, `kategori_biaya`, `estimasi`, `realisasi`, `selisih`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Material Bangunan', 150000000.00, 142000000.00, -8000000.00, 'Efisien', NULL, NULL),
(2, 'PRJ001', 'Upah Tenaga Kerja', 100000000.00, 115000000.00, 15000000.00, 'Over Budget', NULL, NULL),
(3, 'PRJ001', 'Sewa Alat Berat', 50000000.00, 48000000.00, -2000000.00, 'Efisien', NULL, NULL),
(4, 'PRJ002', 'Pembelian Unit Bus', 1200000000.00, 1200000000.00, 0.00, 'Normal', NULL, NULL),
(5, 'PRJ002', 'Aksesoris & Modifikasi', 80000000.00, 92000000.00, 12000000.00, 'Over Budget', NULL, NULL),
(6, 'PRJ003', 'Perangkat GPS', 120000000.00, 118500000.00, -1500000.00, 'Efisien', NULL, NULL),
(7, 'PRJ003', 'Biaya Instalasi', 30000000.00, 30000000.00, 0.00, 'Normal', NULL, NULL),
(8, 'PRJ005', 'Biaya Operasional', 200000000.00, 185000000.00, -15000000.00, 'Efisien', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_management`
--

CREATE TABLE `project_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_proyek` varchar(255) NOT NULL,
  `pic_proyek` varchar(255) NOT NULL,
  `tujuan` text NOT NULL,
  `estimasi_waktu` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `progres` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_management`
--

INSERT INTO `project_management` (`id`, `nama_proyek`, `pic_proyek`, `tujuan`, `estimasi_waktu`, `status`, `progres`, `created_at`, `updated_at`) VALUES
(1, 'Migrasi ERP ke Cloud', 'Budi Santoso', 'Memindahkan seluruh infrastruktur ERP dari on-premise ke cloud AWS untuk meningkatkan skalabilitas', '6 bulan', 'In Progress', 45, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'Implementasi SSO Perusahaan', 'Doni Prasetyo', 'Implementasi Single Sign-On untuk semua sistem internal menggunakan Keycloak', '3 bulan', 'Selesai', 100, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'Pengembangan Mobile App Driver', 'Andi Wijaya', 'Membuat aplikasi mobile untuk monitoring dan tracking kendaraan operasional', '4 bulan', 'In Progress', 30, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'Upgrade Infrastruktur Jaringan', 'Rudi Hermawan', 'Upgrade seluruh perangkat jaringan kantor pusat ke standar 10 Gbps', '2 bulan', 'Pending', 0, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'Implementasi WAF', 'Budi Santoso', 'Pemasangan Web Application Firewall untuk semua endpoint API publik', '1 bulan', 'Selesai', 100, '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_plannings`
--

CREATE TABLE `project_plannings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_proyek` varchar(255) NOT NULL,
  `tahapan` varchar(255) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `durasi` bigint(20) NOT NULL DEFAULT 0,
  `pic` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_plannings`
--

INSERT INTO `project_plannings` (`id`, `kode_proyek`, `tahapan`, `tgl_mulai`, `tgl_selesai`, `durasi`, `pic`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Survey & Perencanaan', '2026-01-01', '2026-01-07', 7, 'Tim GA', 'Selesai', NULL, NULL),
(2, 'PRJ001', 'Pengadaan Material', '2026-01-08', '2026-01-20', 12, 'Rudi', 'Selesai', NULL, NULL),
(3, 'PRJ001', 'Konstruksi', '2026-01-21', '2026-03-15', 53, 'Kontraktor', 'Berjalan', NULL, NULL),
(4, 'PRJ001', 'Finishing & Serahterima', '2026-03-16', '2026-03-31', 15, 'Rudi', 'Plan', NULL, NULL),
(5, 'PRJ002', 'Seleksi Vendor Bus', '2026-02-01', '2026-02-15', 14, 'Rina', 'Selesai', NULL, NULL),
(6, 'PRJ002', 'Negosiasi & Kontrak', '2026-02-16', '2026-02-28', 12, 'Rina', 'Berjalan', NULL, NULL),
(7, 'PRJ003', 'Instalasi Perangkat GPS', '2026-01-15', '2026-02-28', 44, 'Ivan', 'Berjalan', NULL, NULL),
(8, 'PRJ003', 'Uji Coba & Training', '2026-03-01', '2026-03-31', 30, 'Ivan', 'Plan', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_risks`
--

CREATE TABLE `project_risks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `risiko` varchar(255) NOT NULL,
  `dampak` varchar(255) NOT NULL,
  `kemungkinan` varchar(255) NOT NULL,
  `mitigasi` text DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_risks`
--

INSERT INTO `project_risks` (`id`, `proyek`, `risiko`, `dampak`, `kemungkinan`, `mitigasi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Cuaca ekstrim hujan deras', 'Sedang', 'Tinggi', 'Sediakan terpal & pompa air', 'Terkendali', NULL, NULL),
(2, 'PRJ001', 'Kenaikan harga material', 'Tinggi', 'Menengah', 'Kontrak harga tetap dengan supplier', 'Terkendali', NULL, NULL),
(3, 'PRJ002', 'Keterlambatan pengiriman unit bus', 'Tinggi', 'Rendah', 'Klausul denda dalam perjanjian', 'Diajukan', NULL, NULL),
(4, 'PRJ002', 'Fluktuasi kurs impor', 'Tinggi', 'Menengah', 'Hedging mata uang', 'Diajukan', NULL, NULL),
(5, 'PRJ003', 'Perangkat GPS tidak kompatibel', 'Tinggi', 'Rendah', 'Uji coba sebelum instalasi massal', 'Terkendali', NULL, NULL),
(6, 'PRJ003', 'Gangguan sinyal di area tertentu', 'Sedang', 'Menengah', 'Pasang booster sinyal di pool', 'Diajukan', NULL, NULL),
(7, 'PRJ005', 'Driver tidak hadir mendadak', 'Tinggi', 'Menengah', 'Siapkan driver cadangan on-call', 'Terkendali', NULL, NULL),
(8, 'PRJ005', 'Kemacetan rute utama', 'Sedang', 'Tinggi', 'Siapkan rute alternatif', 'Terkendali', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_timelines`
--

CREATE TABLE `project_timelines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `kegiatan` varchar(255) NOT NULL,
  `deadline` date NOT NULL,
  `reminder` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_timelines`
--

INSERT INTO `project_timelines` (`id`, `proyek`, `kegiatan`, `deadline`, `reminder`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Pengecoran Lantai Garasi', '2026-02-10', 1, 'Selesai', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'PRJ001', 'Pemasangan Atap Baja Ringan', '2026-02-28', 1, 'Berjalan', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(3, 'PRJ001', 'Pemasangan Listrik & CCTV', '2026-03-10', 1, 'Scheduled', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(4, 'PRJ002', 'Pembayaran DP Pembelian Bus', '2026-02-20', 1, 'Selesai', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(5, 'PRJ002', 'Pengiriman Unit Bus ke Pool', '2026-04-15', 1, 'Scheduled', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(6, 'PRJ003', 'Pemasangan GPS 20 Unit Sedan', '2026-02-15', 0, 'Berjalan', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(7, 'PRJ003', 'Aktivasi Dashboard Monitoring', '2026-03-15', 1, 'Scheduled', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(8, 'PRJ005', 'Mulai Operasional Rute I', '2026-03-01', 0, 'Selesai', '2026-08-10 23:55:33', '2026-08-10 23:55:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchaseros`
--

CREATE TABLE `purchaseros` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_pr` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `departemen` varchar(255) DEFAULT NULL,
  `pemohon` varchar(255) DEFAULT NULL,
  `barang_jasa` varchar(255) DEFAULT NULL,
  `kode_barang` varchar(255) DEFAULT NULL,
  `qty` bigint(20) DEFAULT NULL,
  `satuan` varchar(255) DEFAULT NULL,
  `alasan_permintaan` varchar(255) DEFAULT NULL,
  `nominal` bigint(20) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `disetujui_oleh` varchar(255) DEFAULT NULL,
  `tanggal_persetujuan` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `terakhir_diajukan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `purchaseros`
--

INSERT INTO `purchaseros` (`id`, `no_pr`, `tanggal`, `departemen`, `pemohon`, `barang_jasa`, `kode_barang`, `qty`, `satuan`, `alasan_permintaan`, `nominal`, `status`, `disetujui_oleh`, `tanggal_persetujuan`, `catatan`, `terakhir_diajukan`, `created_at`, `updated_at`) VALUES
(1, 'PR-001', '2026-03-22', 'Produksi', 'Pemohon 1', 'Spare Part', 'BRG-007', 428, 'pcs', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-1', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(2, 'PR-002', '2026-03-09', 'Gudang', 'Pemohon 2', 'ATK', 'BRG-014', 124, 'unit', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Gudang', '2026-03-11', 'Catatan PR ke-2', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(3, 'PR-003', '2026-06-28', 'IT', 'Pemohon 3', 'Komputer', 'BRG-021', 406, 'liter', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-3', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(4, 'PR-004', '2026-07-06', 'Finance', 'Pemohon 4', 'Bahan Bakar', 'BRG-028', 500, 'kg', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Finance', '2026-07-09', 'Catatan PR ke-4', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(5, 'PR-005', '2026-05-06', 'HR', 'Pemohon 5', 'Oli Mesin', 'BRG-035', 392, 'set', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-5', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(6, 'PR-006', '2026-08-01', 'Marketing', 'Pemohon 6', 'Ban Kendaraan', 'BRG-042', 28, 'dus', 'Stok Habis', NULL, 'Disetujui', 'Manajer Marketing', '2026-08-03', 'Catatan PR ke-6', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(7, 'PR-007', '2026-07-10', 'Operasional', 'Pemohon 7', 'Seragam', 'BRG-049', 495, 'rim', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-7', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(8, 'PR-008', '2026-07-10', 'Maintenance', 'Pemohon 8', 'Alat Kebersihan', 'BRG-056', 98, 'buah', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Maintenance', '2026-07-11', 'Catatan PR ke-8', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(9, 'PR-009', '2026-08-03', 'Produksi', 'Pemohon 9', 'Mebel', 'BRG-063', 342, 'pcs', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-9', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(10, 'PR-010', '2026-04-30', 'Gudang', 'Pemohon 10', 'Printer', 'BRG-070', 488, 'unit', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Gudang', '2026-05-01', 'Catatan PR ke-10', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(11, 'PR-011', '2026-07-22', 'IT', 'Pemohon 11', 'Spare Part', 'BRG-077', 45, 'liter', 'Stok Habis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-11', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(12, 'PR-012', '2026-07-11', 'Finance', 'Pemohon 12', 'ATK', 'BRG-084', 173, 'kg', 'Persediaan Menipis', NULL, 'Selesai', 'Manajer Finance', '2026-07-12', 'Catatan PR ke-12', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(13, 'PR-013', '2026-03-23', 'HR', 'Pemohon 13', 'Komputer', 'BRG-091', 289, 'set', 'Permintaan Proyek', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-13', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(14, 'PR-014', '2026-08-09', 'Marketing', 'Pemohon 14', 'Bahan Bakar', 'BRG-098', 10, 'dus', 'Penggantian Rutin', NULL, 'Disetujui', 'Manajer Marketing', '2026-08-11', 'Catatan PR ke-14', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(15, 'PR-015', '2026-03-14', 'Operasional', 'Pemohon 15', 'Oli Mesin', 'BRG-105', 447, 'rim', 'Kebutuhan Mendadak', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-15', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(16, 'PR-016', '2026-04-10', 'Maintenance', 'Pemohon 16', 'Ban Kendaraan', 'BRG-112', 3, 'buah', 'Stok Habis', NULL, 'Selesai', 'Manajer Maintenance', '2026-04-12', 'Catatan PR ke-16', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(17, 'PR-017', '2026-05-15', 'Produksi', 'Pemohon 17', 'Seragam', 'BRG-119', 352, 'pcs', 'Persediaan Menipis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-17', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(18, 'PR-018', '2026-07-29', 'Gudang', 'Pemohon 18', 'Alat Kebersihan', 'BRG-126', 491, 'unit', 'Permintaan Proyek', NULL, 'Disetujui', 'Manajer Gudang', '2026-08-01', 'Catatan PR ke-18', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(19, 'PR-019', '2026-05-21', 'IT', 'Pemohon 19', 'Mebel', 'BRG-133', 458, 'liter', 'Penggantian Rutin', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-19', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(20, 'PR-020', '2026-04-06', 'Finance', 'Pemohon 20', 'Printer', 'BRG-140', 238, 'kg', 'Kebutuhan Mendadak', NULL, 'Selesai', 'Manajer Finance', '2026-04-07', 'Catatan PR ke-20', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(21, 'PR-021', '2026-03-12', 'HR', 'Pemohon 21', 'Spare Part', 'BRG-147', 192, 'set', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-21', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(22, 'PR-022', '2026-05-20', 'Marketing', 'Pemohon 22', 'ATK', 'BRG-154', 164, 'dus', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Marketing', '2026-05-23', 'Catatan PR ke-22', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(23, 'PR-023', '2026-06-17', 'Operasional', 'Pemohon 23', 'Komputer', 'BRG-161', 113, 'rim', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-23', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(24, 'PR-024', '2026-02-12', 'Maintenance', 'Pemohon 24', 'Bahan Bakar', 'BRG-168', 106, 'buah', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Maintenance', '2026-02-15', 'Catatan PR ke-24', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(25, 'PR-025', '2026-06-10', 'Produksi', 'Pemohon 25', 'Oli Mesin', 'BRG-175', 231, 'pcs', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-25', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(26, 'PR-026', '2026-06-29', 'Gudang', 'Pemohon 26', 'Ban Kendaraan', 'BRG-182', 22, 'unit', 'Stok Habis', NULL, 'Disetujui', 'Manajer Gudang', '2026-07-01', 'Catatan PR ke-26', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(27, 'PR-027', '2026-08-01', 'IT', 'Pemohon 27', 'Seragam', 'BRG-189', 155, 'liter', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-27', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(28, 'PR-028', '2026-04-27', 'Finance', 'Pemohon 28', 'Alat Kebersihan', 'BRG-196', 331, 'kg', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Finance', '2026-04-29', 'Catatan PR ke-28', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(29, 'PR-029', '2026-07-07', 'HR', 'Pemohon 29', 'Mebel', 'BRG-203', 223, 'set', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-29', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(30, 'PR-030', '2026-05-31', 'Marketing', 'Pemohon 30', 'Printer', 'BRG-210', 6, 'dus', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Marketing', '2026-06-01', 'Catatan PR ke-30', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(31, 'PR-031', '2026-05-27', 'Operasional', 'Pemohon 31', 'Spare Part', 'BRG-217', 81, 'rim', 'Stok Habis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-31', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(32, 'PR-032', '2026-04-19', 'Maintenance', 'Pemohon 32', 'ATK', 'BRG-224', 88, 'buah', 'Persediaan Menipis', NULL, 'Selesai', 'Manajer Maintenance', '2026-04-20', 'Catatan PR ke-32', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(33, 'PR-033', '2026-04-10', 'Produksi', 'Pemohon 33', 'Komputer', 'BRG-231', 21, 'pcs', 'Permintaan Proyek', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-33', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(34, 'PR-034', '2026-03-09', 'Gudang', 'Pemohon 34', 'Bahan Bakar', 'BRG-238', 185, 'unit', 'Penggantian Rutin', NULL, 'Disetujui', 'Manajer Gudang', '2026-03-12', 'Catatan PR ke-34', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(35, 'PR-035', '2026-05-29', 'IT', 'Pemohon 35', 'Oli Mesin', 'BRG-245', 166, 'liter', 'Kebutuhan Mendadak', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-35', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(36, 'PR-036', '2026-02-18', 'Finance', 'Pemohon 36', 'Ban Kendaraan', 'BRG-252', 89, 'kg', 'Stok Habis', NULL, 'Selesai', 'Manajer Finance', '2026-02-20', 'Catatan PR ke-36', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(37, 'PR-037', '2026-05-19', 'HR', 'Pemohon 37', 'Seragam', 'BRG-259', 390, 'set', 'Persediaan Menipis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-37', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(38, 'PR-038', '2026-02-12', 'Marketing', 'Pemohon 38', 'Alat Kebersihan', 'BRG-266', 59, 'dus', 'Permintaan Proyek', NULL, 'Disetujui', 'Manajer Marketing', '2026-02-13', 'Catatan PR ke-38', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(39, 'PR-039', '2026-05-08', 'Operasional', 'Pemohon 39', 'Mebel', 'BRG-273', 476, 'rim', 'Penggantian Rutin', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-39', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(40, 'PR-040', '2026-05-15', 'Maintenance', 'Pemohon 40', 'Printer', 'BRG-280', 382, 'buah', 'Kebutuhan Mendadak', NULL, 'Selesai', 'Manajer Maintenance', '2026-05-16', 'Catatan PR ke-40', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(41, 'PR-041', '2026-04-11', 'Produksi', 'Pemohon 41', 'Spare Part', 'BRG-287', 18, 'pcs', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-41', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(42, 'PR-042', '2026-06-20', 'Gudang', 'Pemohon 42', 'ATK', 'BRG-294', 313, 'unit', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Gudang', '2026-06-21', 'Catatan PR ke-42', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(43, 'PR-043', '2026-04-24', 'IT', 'Pemohon 43', 'Komputer', 'BRG-301', 421, 'liter', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-43', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(44, 'PR-044', '2026-02-25', 'Finance', 'Pemohon 44', 'Bahan Bakar', 'BRG-308', 256, 'kg', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Finance', '2026-02-28', 'Catatan PR ke-44', NULL, '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(45, 'PR-045', '2026-07-13', 'HR', 'Pemohon 45', 'Oli Mesin', 'BRG-315', 404, 'set', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-45', NULL, '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(46, 'PR-046', '2026-07-28', 'Marketing', 'Pemohon 46', 'Ban Kendaraan', 'BRG-322', 201, 'dus', 'Stok Habis', NULL, 'Disetujui', 'Manajer Marketing', '2026-07-29', 'Catatan PR ke-46', NULL, '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(47, 'PR-047', '2026-03-02', 'Operasional', 'Pemohon 47', 'Seragam', 'BRG-329', 219, 'rim', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-47', NULL, '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(48, 'PR-048', '2026-04-19', 'Maintenance', 'Pemohon 48', 'Alat Kebersihan', 'BRG-336', 373, 'buah', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Maintenance', '2026-04-22', 'Catatan PR ke-48', NULL, '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(49, 'PR-049', '2026-03-30', 'Produksi', 'Pemohon 49', 'Mebel', 'BRG-343', 147, 'pcs', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-49', NULL, '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(50, 'PR-050', '2026-03-14', 'Gudang', 'Pemohon 50', 'Printer', 'BRG-350', 31, 'unit', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Gudang', '2026-03-16', 'Catatan PR ke-50', NULL, '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_id` varchar(255) NOT NULL,
  `tanggal_po` date NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `terkait_rfq` varchar(255) DEFAULT NULL,
  `total_barang` bigint(20) NOT NULL,
  `total_harga` bigint(20) NOT NULL,
  `status_po` varchar(255) NOT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_terima` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_id`, `tanggal_po`, `vendor`, `terkait_rfq`, `total_barang`, `total_harga`, `status_po`, `tanggal_kirim`, `tanggal_terima`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PO-001', '2026-06-07', 'PT Maju Jaya', 'RFQ-001', 17, 15267768, 'Pending', '2026-06-14', NULL, 'Catatan PO ke-1', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(2, 'PO-002', '2026-07-03', 'CV Berkah Abadi', 'RFQ-002', 13, 26502384, 'Approved', '2026-07-12', NULL, 'Catatan PO ke-2', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(3, 'PO-003', '2026-03-31', 'PT Sumber Makmur', 'RFQ-003', 21, 3522800, 'Closed', '2026-04-21', '2026-04-23', 'Catatan PO ke-3', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(4, 'PO-004', '2026-06-29', 'UD Sejahtera', 'RFQ-004', 14, 32241935, 'Pending', '2026-07-13', NULL, 'Catatan PO ke-4', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(5, 'PO-005', '2026-08-07', 'PT Indo Supplier', 'RFQ-005', 43, 39310252, 'Approved', '2026-08-20', NULL, 'Catatan PO ke-5', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(6, 'PO-006', '2026-05-08', 'PT Maju Jaya', 'RFQ-006', 32, 47926857, 'Closed', '2026-05-15', '2026-05-18', 'Catatan PO ke-6', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(7, 'PO-007', '2026-07-19', 'CV Berkah Abadi', 'RFQ-007', 24, 29082876, 'Pending', '2026-07-27', NULL, 'Catatan PO ke-7', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(8, 'PO-008', '2026-07-01', 'PT Sumber Makmur', 'RFQ-008', 10, 47884071, 'Approved', '2026-07-14', NULL, 'Catatan PO ke-8', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(9, 'PO-009', '2026-06-06', 'UD Sejahtera', 'RFQ-009', 42, 3292603, 'Closed', '2026-06-18', '2026-06-19', 'Catatan PO ke-9', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(10, 'PO-010', '2026-03-20', 'PT Indo Supplier', 'RFQ-010', 38, 20542312, 'Pending', '2026-03-28', NULL, 'Catatan PO ke-10', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(11, 'PO-011', '2026-04-11', 'PT Maju Jaya', 'RFQ-011', 34, 42544048, 'Approved', '2026-05-01', NULL, 'Catatan PO ke-11', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(12, 'PO-012', '2026-07-27', 'CV Berkah Abadi', 'RFQ-012', 29, 7945535, 'Closed', '2026-08-06', '2026-08-13', 'Catatan PO ke-12', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(13, 'PO-013', '2026-05-07', 'PT Sumber Makmur', 'RFQ-013', 33, 25648872, 'Pending', '2026-05-15', NULL, 'Catatan PO ke-13', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(14, 'PO-014', '2026-04-20', 'UD Sejahtera', 'RFQ-014', 32, 48050847, 'Approved', '2026-04-30', NULL, 'Catatan PO ke-14', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(15, 'PO-015', '2026-07-13', 'PT Indo Supplier', 'RFQ-015', 2, 25513017, 'Closed', '2026-08-02', '2026-08-04', 'Catatan PO ke-15', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(16, 'PO-016', '2026-05-10', 'PT Maju Jaya', 'RFQ-016', 22, 30296337, 'Pending', '2026-05-23', NULL, 'Catatan PO ke-16', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(17, 'PO-017', '2026-06-13', 'CV Berkah Abadi', 'RFQ-017', 4, 887248, 'Approved', '2026-07-03', NULL, 'Catatan PO ke-17', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(18, 'PO-018', '2026-04-09', 'PT Sumber Makmur', 'RFQ-018', 9, 37457212, 'Closed', '2026-04-26', '2026-04-27', 'Catatan PO ke-18', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(19, 'PO-019', '2026-03-27', 'UD Sejahtera', 'RFQ-019', 25, 46559985, 'Pending', '2026-04-08', NULL, 'Catatan PO ke-19', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(20, 'PO-020', '2026-03-15', 'PT Indo Supplier', 'RFQ-020', 28, 46101482, 'Approved', '2026-03-29', NULL, 'Catatan PO ke-20', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(21, 'PO-021', '2026-05-16', 'PT Maju Jaya', 'RFQ-021', 30, 26207149, 'Closed', '2026-06-06', '2026-06-13', 'Catatan PO ke-21', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(22, 'PO-022', '2026-04-12', 'CV Berkah Abadi', 'RFQ-022', 50, 43710204, 'Pending', '2026-04-29', NULL, 'Catatan PO ke-22', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(23, 'PO-023', '2026-06-21', 'PT Sumber Makmur', 'RFQ-023', 3, 5726748, 'Approved', '2026-06-29', NULL, 'Catatan PO ke-23', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(24, 'PO-024', '2026-04-20', 'UD Sejahtera', 'RFQ-024', 21, 4667426, 'Closed', '2026-05-09', '2026-05-15', 'Catatan PO ke-24', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(25, 'PO-025', '2026-06-03', 'PT Indo Supplier', 'RFQ-025', 29, 38214781, 'Pending', '2026-06-11', NULL, 'Catatan PO ke-25', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(26, 'PO-026', '2026-04-01', 'PT Maju Jaya', 'RFQ-026', 29, 24867805, 'Approved', '2026-04-19', NULL, 'Catatan PO ke-26', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(27, 'PO-027', '2026-08-02', 'CV Berkah Abadi', 'RFQ-027', 2, 37911587, 'Closed', '2026-08-12', '2026-08-15', 'Catatan PO ke-27', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(28, 'PO-028', '2026-04-13', 'PT Sumber Makmur', 'RFQ-028', 8, 24973290, 'Pending', '2026-04-20', NULL, 'Catatan PO ke-28', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(29, 'PO-029', '2026-05-10', 'UD Sejahtera', 'RFQ-029', 31, 27270496, 'Approved', '2026-05-19', NULL, 'Catatan PO ke-29', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(30, 'PO-030', '2026-04-17', 'PT Indo Supplier', 'RFQ-030', 17, 49878631, 'Closed', '2026-05-02', '2026-05-09', 'Catatan PO ke-30', '2026-08-10 23:55:34', '2026-08-10 23:55:34');

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
  `status_rekonsiliasi` enum('matched','unmatched','Pending') NOT NULL DEFAULT 'Pending',
  `invoice_id` varchar(255) DEFAULT NULL,
  `va` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rekonsiliasi_bank`
--

INSERT INTO `rekonsiliasi_bank` (`id`, `tanggal`, `deskripsi`, `reference_no`, `amount`, `currency`, `status_rekonsiliasi`, `invoice_id`, `va`, `bukti_pembayaran`, `created_at`, `updated_at`) VALUES
(1, '2026-07-30', 'Pembayaran rental masuk', 'BANK-INV-001', 1500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(2, '2026-08-02', 'Transfer service kendaraan', 'BANK-INV-002', 500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(3, '2026-08-05', 'Pembayaran deposit rental', 'BANK-INV-003', 2000000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(4, '2026-08-08', 'Pembayaran sparepart', 'BANK-INV-004', 750000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30'),
(5, '2026-08-11', 'Pemasukan rental harian', 'BANK-INV-005', 1200000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-08-10 23:55:30', '2026-08-10 23:55:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reminder_service`
--

CREATE TABLE `reminder_service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `nama_reminder` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `interval_nilai` bigint(20) NOT NULL DEFAULT 1,
  `interval_satuan` enum('hari','minggu','bulan','tahun') NOT NULL DEFAULT 'bulan',
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `biaya` decimal(15,2) DEFAULT NULL,
  `status` enum('aktif','jatuh_tempo','selesai') NOT NULL DEFAULT 'aktif',
  `sudah_dibuat_masalah` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `tujuan` varchar(255) DEFAULT NULL,
  `tujuan_perjalanan` enum('dalam_kota','luar_kota') DEFAULT NULL,
  `alamat_pengantaran` varchar(255) DEFAULT NULL,
  `alamat_penjemputan` varchar(255) DEFAULT NULL,
  `durasi_jam` bigint(20) DEFAULT NULL,
  `durasi_hari` bigint(20) DEFAULT NULL,
  `durasi_bulan` bigint(20) DEFAULT NULL,
  `biaya_dasar` bigint(20) NOT NULL DEFAULT 0,
  `biaya_tambahan_total` bigint(20) NOT NULL DEFAULT 0,
  `total_biaya` bigint(20) NOT NULL DEFAULT 0,
  `metode_pembayaran` enum('tunai','transfer') NOT NULL DEFAULT 'transfer',
  `jenis_pembayaran` enum('lunas','dp') NOT NULL DEFAULT 'lunas',
  `nominal_dp` bigint(20) DEFAULT NULL,
  `nama_driver` varchar(255) DEFAULT NULL,
  `kontak_driver` varchar(255) DEFAULT NULL,
  `biaya_driver` bigint(20) DEFAULT NULL,
  `bukti_lunas` varchar(255) DEFAULT NULL,
  `bukti_dp` varchar(255) DEFAULT NULL,
  `bukti_pelunasan` varchar(255) DEFAULT NULL,
  `nominal_pelunasan` bigint(20) DEFAULT NULL,
  `status_pembayaran` enum('belum_bayar','dp','partial','lunas') NOT NULL DEFAULT 'belum_bayar',
  `status` enum('Pending','booking','aktif','selesai','batal') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pakai_batas_biaya` tinyint(1) NOT NULL DEFAULT 0,
  `batas_biaya` decimal(15,2) DEFAULT NULL,
  `durasi_tahun` bigint(20) DEFAULT NULL,
  `invoice` varchar(255) DEFAULT NULL,
  `kelayakan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rentals`
--

INSERT INTO `rentals` (`id`, `user_id`, `kendaraan_id`, `member_id`, `tanggal_mulai`, `tanggal_selesai`, `tujuan`, `tujuan_perjalanan`, `alamat_pengantaran`, `alamat_penjemputan`, `durasi_jam`, `durasi_hari`, `durasi_bulan`, `biaya_dasar`, `biaya_tambahan_total`, `total_biaya`, `metode_pembayaran`, `jenis_pembayaran`, `nominal_dp`, `nama_driver`, `kontak_driver`, `biaya_driver`, `bukti_lunas`, `bukti_dp`, `bukti_pelunasan`, `nominal_pelunasan`, `status_pembayaran`, `status`, `created_at`, `updated_at`, `pakai_batas_biaya`, `batas_biaya`, `durasi_tahun`, `invoice`, `kelayakan`) VALUES
(1, 1, 1, 1, '2026-04-05 06:55:30', '2026-04-10 06:55:30', 'Perjalanan dinas ke kota 1', NULL, NULL, NULL, 3, 5, 0, 2225000, 414000, 2639000, 'tunai', 'lunas', NULL, NULL, NULL, 87000, NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(2, 1, 2, 2, '2026-07-21 06:55:30', '2026-07-27 06:55:30', 'Perjalanan dinas ke kota 2', NULL, NULL, NULL, 3, 6, 0, 2478000, 404000, 2882000, 'transfer', 'dp', 1441000, NULL, '08794890809', NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(3, 1, 3, 3, '2026-05-07 06:55:30', '2026-05-21 06:55:30', 'Perjalanan dinas ke kota 3', NULL, NULL, NULL, 0, 14, 0, 5992000, 105000, 6097000, 'tunai', 'lunas', NULL, 'Driver 3', '08295794210', 139000, NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(4, 1, 4, 4, '2026-03-29 06:55:30', '2026-03-31 06:55:30', 'Perjalanan dinas ke kota 4', NULL, NULL, NULL, 1, 2, 0, 420000, 473000, 893000, 'transfer', 'dp', 446500, NULL, '08681730052', 164000, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(5, 1, 5, 5, '2026-07-11 06:55:30', '2026-07-19 06:55:30', 'Perjalanan dinas ke kota 5', NULL, NULL, NULL, 7, 8, 0, 3352000, 261000, 3613000, 'tunai', 'lunas', NULL, 'Driver 5', '08726398042', NULL, NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(6, 1, 6, 6, '2026-06-09 06:55:30', '2026-06-10 06:55:30', 'Perjalanan dinas ke kota 6', NULL, NULL, NULL, 3, 1, 0, 203000, 45000, 248000, 'transfer', 'dp', 124000, NULL, NULL, 111000, NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(7, 1, 7, 7, '2026-05-10 06:55:30', '2026-05-12 06:55:30', 'Perjalanan dinas ke kota 7', NULL, NULL, NULL, 8, 2, 0, 588000, 479000, 1067000, 'tunai', 'lunas', NULL, NULL, NULL, 58000, NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(8, 1, 8, 8, '2026-08-04 06:55:30', '2026-08-07 06:55:30', 'Perjalanan dinas ke kota 8', NULL, NULL, NULL, 7, 3, 0, 1320000, 386000, 1706000, 'transfer', 'dp', 853000, 'Driver 8', NULL, 104000, NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(9, 1, 9, 9, '2026-08-07 06:55:30', '2026-08-21 06:55:30', 'Perjalanan dinas ke kota 9', NULL, NULL, NULL, 3, 14, 0, 5208000, 365000, 5573000, 'tunai', 'lunas', NULL, NULL, '08929923978', 72000, NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(10, 1, 10, 10, '2026-05-04 06:55:30', '2026-05-05 06:55:30', 'Perjalanan dinas ke kota 10', NULL, NULL, NULL, 4, 1, 0, 418000, 135000, 553000, 'transfer', 'dp', 276500, 'Driver 10', '08449651585', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(11, 1, 11, 11, '2026-05-25 06:55:30', '2026-06-03 06:55:30', 'Perjalanan dinas ke kota 11', NULL, NULL, NULL, 0, 9, 0, 1881000, 178000, 2059000, 'tunai', 'lunas', NULL, NULL, NULL, 84000, NULL, NULL, NULL, NULL, 'dp', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(12, 1, 12, 12, '2026-03-14 06:55:30', '2026-03-16 06:55:30', 'Perjalanan dinas ke kota 12', NULL, NULL, NULL, 5, 2, 0, 1150000, 208000, 1358000, 'transfer', 'dp', 679000, 'Driver 12', NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(13, 1, 13, 13, '2026-06-29 06:55:30', '2026-07-02 06:55:30', 'Perjalanan dinas ke kota 13', NULL, NULL, NULL, 1, 3, 0, 1260000, 63000, 1323000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(14, 1, 14, 14, '2026-03-24 06:55:30', '2026-03-31 06:55:30', 'Perjalanan dinas ke kota 14', NULL, NULL, NULL, 5, 7, 0, 4046000, 409000, 4455000, 'transfer', 'dp', 2227500, 'Driver 14', '08191633691', 62000, NULL, NULL, NULL, NULL, 'dp', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(15, 1, 15, 15, '2026-05-28 06:55:30', '2026-06-07 06:55:30', 'Perjalanan dinas ke kota 15', NULL, NULL, NULL, 4, 10, 0, 5780000, 259000, 6039000, 'tunai', 'lunas', NULL, 'Driver 15', NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(16, 1, 16, 16, '2026-08-05 06:55:30', '2026-08-17 06:55:30', 'Perjalanan dinas ke kota 16', NULL, NULL, NULL, 4, 12, 0, 2460000, 185000, 2645000, 'transfer', 'dp', 1322500, NULL, NULL, 91000, NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(17, 1, 17, 17, '2026-07-09 06:55:30', '2026-07-13 06:55:30', 'Perjalanan dinas ke kota 17', NULL, NULL, NULL, 0, 4, 0, 1096000, 227000, 1323000, 'tunai', 'lunas', NULL, 'Driver 17', '08627700373', NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(18, 1, 18, 18, '2026-07-16 06:55:30', '2026-07-29 06:55:30', 'Perjalanan dinas ke kota 18', NULL, NULL, NULL, 0, 13, 0, 6604000, 497000, 7101000, 'transfer', 'dp', 3550500, NULL, NULL, 123000, NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(19, 1, 19, 19, '2026-08-08 06:55:30', '2026-08-21 06:55:30', 'Perjalanan dinas ke kota 19', NULL, NULL, NULL, 1, 13, 0, 5902000, 417000, 6319000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(20, 1, 20, 20, '2026-05-28 06:55:30', '2026-06-09 06:55:30', 'Perjalanan dinas ke kota 20', NULL, NULL, NULL, 7, 12, 0, 3792000, 444000, 4236000, 'transfer', 'dp', 2118000, NULL, '08999107098', NULL, NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(21, 1, 21, 21, '2026-06-16 06:55:30', '2026-06-22 06:55:30', 'Perjalanan dinas ke kota 21', NULL, NULL, NULL, 2, 6, 0, 3240000, 324000, 3564000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(22, 1, 22, 22, '2026-03-26 06:55:30', '2026-04-02 06:55:30', 'Perjalanan dinas ke kota 22', NULL, NULL, NULL, 8, 7, 0, 2485000, 134000, 2619000, 'transfer', 'dp', 1309500, NULL, '08285830180', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(23, 1, 23, 23, '2026-02-19 06:55:30', '2026-02-24 06:55:30', 'Perjalanan dinas ke kota 23', NULL, NULL, NULL, 7, 5, 0, 1765000, 272000, 2037000, 'tunai', 'lunas', NULL, NULL, '08817080613', 52000, NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(24, 1, 24, 24, '2026-05-03 06:55:30', '2026-05-16 06:55:30', 'Perjalanan dinas ke kota 24', NULL, NULL, NULL, 8, 13, 0, 7228000, 46000, 7274000, 'transfer', 'dp', 3637000, NULL, NULL, 183000, NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(25, 1, 25, 25, '2026-04-24 06:55:30', '2026-04-29 06:55:30', 'Perjalanan dinas ke kota 25', NULL, NULL, NULL, 1, 5, 0, 1270000, 317000, 1587000, 'tunai', 'lunas', NULL, 'Driver 25', NULL, 169000, NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(26, 1, 26, 26, '2026-02-23 06:55:30', '2026-02-25 06:55:30', 'Perjalanan dinas ke kota 26', NULL, NULL, NULL, 7, 2, 0, 1078000, 488000, 1566000, 'transfer', 'dp', 783000, 'Driver 26', NULL, 100000, NULL, NULL, NULL, NULL, 'dp', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(27, 1, 27, 27, '2026-03-07 06:55:30', '2026-03-16 06:55:30', 'Perjalanan dinas ke kota 27', NULL, NULL, NULL, 3, 9, 0, 2286000, 432000, 2718000, 'tunai', 'lunas', NULL, NULL, '08127957404', NULL, NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(28, 1, 28, 28, '2026-05-15 06:55:30', '2026-05-29 06:55:30', 'Perjalanan dinas ke kota 28', NULL, NULL, NULL, 2, 14, 0, 7924000, 301000, 8225000, 'transfer', 'dp', 4112500, 'Driver 28', '08186590381', 75000, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(29, 1, 29, 29, '2026-05-10 06:55:30', '2026-05-16 06:55:30', 'Perjalanan dinas ke kota 29', NULL, NULL, NULL, 8, 6, 0, 3558000, 497000, 4055000, 'tunai', 'lunas', NULL, 'Driver 29', NULL, 122000, NULL, NULL, NULL, NULL, 'dp', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(30, 1, 30, 30, '2026-03-21 06:55:30', '2026-03-23 06:55:30', 'Perjalanan dinas ke kota 30', NULL, NULL, NULL, 4, 2, 0, 718000, 370000, 1088000, 'transfer', 'dp', 544000, NULL, '08990472663', NULL, NULL, NULL, NULL, NULL, 'lunas', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(31, 1, 31, 31, '2026-05-31 06:55:30', '2026-06-10 06:55:30', 'Perjalanan dinas ke kota 31', NULL, NULL, NULL, 1, 10, 0, 2870000, 137000, 3007000, 'tunai', 'lunas', NULL, NULL, NULL, 95000, NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(32, 1, 32, 32, '2026-07-20 06:55:30', '2026-07-26 06:55:30', 'Perjalanan dinas ke kota 32', NULL, NULL, NULL, 2, 6, 0, 1530000, 191000, 1721000, 'transfer', 'dp', 860500, NULL, NULL, 153000, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(33, 1, 33, 33, '2026-05-02 06:55:30', '2026-05-12 06:55:30', 'Perjalanan dinas ke kota 33', NULL, NULL, NULL, 5, 10, 0, 2330000, 336000, 2666000, 'tunai', 'lunas', NULL, NULL, '08403332219', 56000, NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(34, 1, 34, 34, '2026-06-12 06:55:30', '2026-06-19 06:55:30', 'Perjalanan dinas ke kota 34', NULL, NULL, NULL, 7, 7, 0, 2905000, 131000, 3036000, 'transfer', 'dp', 1518000, NULL, NULL, 198000, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(35, 1, 35, 35, '2026-06-25 06:55:30', '2026-07-05 06:55:30', 'Perjalanan dinas ke kota 35', NULL, NULL, NULL, 1, 10, 0, 3140000, 257000, 3397000, 'tunai', 'lunas', NULL, 'Driver 35', NULL, 148000, NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(36, 1, 36, 36, '2026-08-09 06:55:30', '2026-08-23 06:55:30', 'Perjalanan dinas ke kota 36', NULL, NULL, NULL, 3, 14, 0, 3024000, 11000, 3035000, 'transfer', 'dp', 1517500, 'Driver 36', NULL, 137000, NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(37, 1, 37, 37, '2026-07-11 06:55:30', '2026-07-24 06:55:30', 'Perjalanan dinas ke kota 37', NULL, NULL, NULL, 6, 13, 0, 6006000, 16000, 6022000, 'tunai', 'lunas', NULL, NULL, '08741561960', 104000, NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(38, 1, 38, 38, '2026-04-17 06:55:30', '2026-04-22 06:55:30', 'Perjalanan dinas ke kota 38', NULL, NULL, NULL, 7, 5, 0, 2365000, 184000, 2549000, 'transfer', 'dp', 1274500, 'Driver 38', '08516094805', NULL, NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(39, 1, 39, 39, '2026-04-04 06:55:30', '2026-04-07 06:55:30', 'Perjalanan dinas ke kota 39', NULL, NULL, NULL, 4, 3, 0, 1458000, 59000, 1517000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(40, 1, 40, 40, '2026-02-15 06:55:30', '2026-02-23 06:55:30', 'Perjalanan dinas ke kota 40', NULL, NULL, NULL, 4, 8, 0, 2896000, 253000, 3149000, 'transfer', 'dp', 1574500, NULL, '08433745312', 167000, NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(41, 1, 41, 41, '2026-03-29 06:55:30', '2026-04-08 06:55:30', 'Perjalanan dinas ke kota 41', NULL, NULL, NULL, 7, 10, 0, 5320000, 73000, 5393000, 'tunai', 'lunas', NULL, NULL, '08121380196', 133000, NULL, NULL, NULL, NULL, 'dp', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(42, 1, 42, 42, '2026-02-16 06:55:30', '2026-02-23 06:55:30', 'Perjalanan dinas ke kota 42', NULL, NULL, NULL, 0, 7, 0, 2667000, 352000, 3019000, 'transfer', 'dp', 1509500, 'Driver 42', NULL, 52000, NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(43, 1, 43, 43, '2026-05-26 06:55:30', '2026-06-02 06:55:30', 'Perjalanan dinas ke kota 43', NULL, NULL, NULL, 8, 7, 0, 2422000, 358000, 2780000, 'tunai', 'lunas', NULL, 'Driver 43', NULL, 188000, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(44, 1, 44, 44, '2026-04-21 06:55:30', '2026-04-26 06:55:30', 'Perjalanan dinas ke kota 44', NULL, NULL, NULL, 0, 5, 0, 1035000, 209000, 1244000, 'transfer', 'dp', 622000, 'Driver 44', '08319759418', 57000, NULL, NULL, NULL, NULL, 'dp', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(45, 1, 45, 45, '2026-04-26 06:55:30', '2026-05-04 06:55:30', 'Perjalanan dinas ke kota 45', NULL, NULL, NULL, 5, 8, 0, 2584000, 111000, 2695000, 'tunai', 'lunas', NULL, 'Driver 45', NULL, 117000, NULL, NULL, NULL, NULL, 'lunas', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(46, 1, 46, 46, '2026-02-19 06:55:30', '2026-02-23 06:55:30', 'Perjalanan dinas ke kota 46', NULL, NULL, NULL, 2, 4, 0, 1640000, 333000, 1973000, 'transfer', 'dp', 986500, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(47, 1, 47, 47, '2026-03-01 06:55:30', '2026-03-05 06:55:30', 'Perjalanan dinas ke kota 47', NULL, NULL, NULL, 0, 4, 0, 2132000, 339000, 2471000, 'tunai', 'lunas', NULL, 'Driver 47', NULL, 191000, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(48, 1, 48, 48, '2026-04-21 06:55:30', '2026-05-04 06:55:30', 'Perjalanan dinas ke kota 48', NULL, NULL, NULL, 8, 13, 0, 4498000, 342000, 4840000, 'transfer', 'dp', 2420000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(49, 1, 49, 49, '2026-05-29 06:55:30', '2026-06-05 06:55:30', 'Perjalanan dinas ke kota 49', NULL, NULL, NULL, 5, 7, 0, 2443000, 239000, 2682000, 'tunai', 'lunas', NULL, NULL, '08515849237', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL),
(50, 1, 50, 50, '2026-05-05 06:55:30', '2026-05-12 06:55:30', 'Perjalanan dinas ke kota 50', NULL, NULL, NULL, 2, 7, 0, 4039000, 156000, 4195000, 'transfer', 'dp', 2097500, 'Driver 50', '08606212424', NULL, NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-08-10 23:55:30', '2026-08-10 23:55:30', 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rental_biaya_tambahan`
--

CREATE TABLE `rental_biaya_tambahan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rental_id` bigint(20) UNSIGNED NOT NULL,
  `biaya_tambahan_id` bigint(20) UNSIGNED NOT NULL,
  `jumlah` bigint(20) NOT NULL DEFAULT 1,
  `subtotal` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `requestfor_quotations`
--

CREATE TABLE `requestfor_quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_rfq` varchar(255) NOT NULL,
  `tanggal_rfq` date NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `kuantitas` bigint(20) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `harga_estimasi` bigint(20) NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `status_rfq` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `requestfor_quotations`
--

INSERT INTO `requestfor_quotations` (`id`, `id_rfq`, `tanggal_rfq`, `vendor`, `kode_barang`, `nama_barang`, `kuantitas`, `satuan`, `harga_estimasi`, `tanggal_kirim`, `status_rfq`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'RFQ-001', '2026-05-05', 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 114, 'pcs', 1632170, '2026-06-03', 'Open', 'Catatan RFQ ke-1', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(2, 'RFQ-002', '2026-06-05', 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 381, 'liter', 1161055, '2026-07-01', 'Sent', 'Catatan RFQ ke-2', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(3, 'RFQ-003', '2026-02-22', 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 18, 'unit', 1097120, '2026-03-02', 'Closed', 'Catatan RFQ ke-3', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(4, 'RFQ-004', '2026-02-27', 'UD Sejahtera', 'BRG-004', 'Filter Udara', 443, 'set', 130420, '2026-03-15', 'Open', 'Catatan RFQ ke-4', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(5, 'RFQ-005', '2026-04-05', 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 424, 'buah', 532658, '2026-04-24', 'Sent', 'Catatan RFQ ke-5', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(6, 'RFQ-006', '2026-04-05', 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 273, 'dus', 1079737, '2026-04-16', 'Closed', 'Catatan RFQ ke-6', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(7, 'RFQ-007', '2026-08-06', 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 194, 'kg', 170128, '2026-08-17', 'Open', 'Catatan RFQ ke-7', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(8, 'RFQ-008', '2026-06-11', 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 150, 'pcs', 1907933, '2026-07-11', 'Sent', 'Catatan RFQ ke-8', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(9, 'RFQ-009', '2026-04-01', 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 165, 'liter', 706380, '2026-04-27', 'Closed', 'Catatan RFQ ke-9', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(10, 'RFQ-010', '2026-06-22', 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 57, 'unit', 1213205, '2026-07-10', 'Open', 'Catatan RFQ ke-10', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(11, 'RFQ-011', '2026-04-22', 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 27, 'set', 187793, '2026-05-12', 'Sent', 'Catatan RFQ ke-11', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(12, 'RFQ-012', '2026-07-06', 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 84, 'buah', 329791, '2026-07-24', 'Closed', 'Catatan RFQ ke-12', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(13, 'RFQ-013', '2026-05-05', 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 416, 'dus', 1821933, '2026-05-20', 'Open', 'Catatan RFQ ke-13', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(14, 'RFQ-014', '2026-05-27', 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 293, 'kg', 169042, '2026-06-04', 'Sent', 'Catatan RFQ ke-14', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(15, 'RFQ-015', '2026-05-31', 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 450, 'pcs', 1025956, '2026-06-07', 'Closed', 'Catatan RFQ ke-15', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(16, 'RFQ-016', '2026-07-15', 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 128, 'liter', 81678, '2026-07-27', 'Open', 'Catatan RFQ ke-16', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(17, 'RFQ-017', '2026-05-21', 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 114, 'unit', 1075746, '2026-06-11', 'Sent', 'Catatan RFQ ke-17', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(18, 'RFQ-018', '2026-04-12', 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 85, 'set', 289731, '2026-05-03', 'Closed', 'Catatan RFQ ke-18', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(19, 'RFQ-019', '2026-06-16', 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 96, 'buah', 1526147, '2026-06-29', 'Open', 'Catatan RFQ ke-19', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(20, 'RFQ-020', '2026-05-11', 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 425, 'dus', 767946, '2026-05-18', 'Sent', 'Catatan RFQ ke-20', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(21, 'RFQ-021', '2026-05-26', 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 293, 'kg', 357879, '2026-06-20', 'Closed', 'Catatan RFQ ke-21', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(22, 'RFQ-022', '2026-03-02', 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 333, 'pcs', 539014, '2026-03-09', 'Open', 'Catatan RFQ ke-22', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(23, 'RFQ-023', '2026-03-09', 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 258, 'liter', 1933301, '2026-03-31', 'Sent', 'Catatan RFQ ke-23', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(24, 'RFQ-024', '2026-03-05', 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 133, 'unit', 1670373, '2026-04-03', 'Closed', 'Catatan RFQ ke-24', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(25, 'RFQ-025', '2026-05-29', 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 394, 'set', 957040, '2026-06-05', 'Open', 'Catatan RFQ ke-25', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(26, 'RFQ-026', '2026-08-07', 'PT Maju Jaya', 'BRG-002', 'Oli Mesin 10W-40', 110, 'buah', 1683026, '2026-08-26', 'Sent', 'Catatan RFQ ke-26', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(27, 'RFQ-027', '2026-07-31', 'CV Berkah Abadi', 'BRG-003', 'Ban Kendaraan', 322, 'dus', 810951, '2026-08-15', 'Closed', 'Catatan RFQ ke-27', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(28, 'RFQ-028', '2026-08-05', 'PT Sumber Makmur', 'BRG-004', 'Filter Udara', 305, 'kg', 496778, '2026-08-21', 'Open', 'Catatan RFQ ke-28', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(29, 'RFQ-029', '2026-04-20', 'UD Sejahtera', 'BRG-005', 'Aki Kendaraan', 160, 'pcs', 1956385, '2026-05-14', 'Sent', 'Catatan RFQ ke-29', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(30, 'RFQ-030', '2026-04-14', 'PT Indo Supplier', 'BRG-006', 'Kampas Rem', 250, 'liter', 688364, '2026-05-02', 'Closed', 'Catatan RFQ ke-30', '2026-08-10 23:55:34', '2026-08-10 23:55:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `resign_offboardings`
--

CREATE TABLE `resign_offboardings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `jabatan_terakhir` varchar(255) NOT NULL,
  `tanggal_resign` date NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `status_offboarding` varchar(255) NOT NULL,
  `serah_terima` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `resign_offboardings`
--

INSERT INTO `resign_offboardings` (`id`, `nama_pegawai`, `jabatan_terakhir`, `tanggal_resign`, `alasan`, `status_offboarding`, `serah_terima`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Ahmad Rifai', 'Staff Gudang', '2024-02-28', 'Mengundurkan diri', 'Selesai', 'Sudah', 'Sudah menyelesaikan serah terima aset dan dokumen', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(2, 'Maya Sari', 'Staff Marketing', '2024-04-15', 'Pindah domisili', 'Selesai', 'Sudah', 'Proses offboarding berjalan lancar', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(3, 'Dika Pratama', 'Developer Junior', '2024-06-01', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Akses sistem telah dicabut', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(4, 'Sari Utami', 'Staff Finance', '2024-07-31', 'Melanjutkan studi', 'Selesai', 'Sudah', 'Dokumen exit interview selesai', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(5, 'Bowo Setiawan', 'Teknisi Lapangan', '2024-09-30', 'Kesehatan', 'Selesai', 'Sudah', 'Serah terima peralatan sudah dilakukan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(6, 'Nita Lestari', 'Admin HR', '2024-11-15', 'Menikah dan pindah kota', 'Selesai', 'Sudah', 'Semua kewajiban telah diselesaikan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(7, 'Reza Aditya', 'IT Support', '2025-01-31', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Credential akun sudah dinonaktifkan', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(8, 'Putri Anggraini', 'Staff Operasional', '2025-03-15', 'Alasan keluarga', 'Proses', 'Belum', 'Sedang dalam proses serah terima', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(9, 'Galih Santoso', 'Supervisor Produksi', '2025-05-30', 'Pensiun dini', 'Proses', 'Belum', 'Menunggu pengganti untuk serah terima', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(10, 'Lina Permatasari', 'Staff Akuntansi', '2025-06-30', 'Wirausaha', 'Proses', 'Belum', 'Dalam proses dokumentasi offboarding', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(11, 'Bagas Wicaksono', 'Driver', '2025-07-01', 'Kontrak tidak diperpanjang', 'Proses', 'Belum', 'Mengembalikan kendaraan dinas', '2026-08-10 23:55:41', '2026-08-10 23:55:41'),
(12, 'Rina Kurniawati', 'Customer Service', '2026-01-31', 'Mengurus anak', 'Proses', 'Belum', 'Exit interview sudah dilakukan', '2026-08-10 23:55:41', '2026-08-10 23:55:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `retur_penjualans`
--

CREATE TABLE `retur_penjualans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_retur` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `no_order` varchar(255) NOT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `produk` varchar(255) NOT NULL,
  `qty` bigint(20) NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `nilai_retur` decimal(15,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `retur_penjualans`
--

INSERT INTO `retur_penjualans` (`id`, `no_retur`, `tanggal`, `no_order`, `pelanggan`, `produk`, `qty`, `alasan`, `nilai_retur`, `status`, `created_at`, `updated_at`) VALUES
(1, 'RTR-001', '2026-02-10', 'SO-2026-001', 'PT Maju Bersama', 'Sewa Minibus', 1, 'Unit mengalami kerusakan', 5000000.00, 'Selesai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'RTR-002', '2026-02-20', 'SO-2026-002', 'PT Global Trans', 'Sewa Bus', 1, 'Spesifikasi tidak sesuai', 15000000.00, 'Diproses', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'RTR-003', '2026-03-05', 'SO-2026-003', 'CV Karya Indah', 'Sewa Truk', 1, 'Truk bermasalah di tengah jalan', 8000000.00, 'Selesai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'RTR-004', '2026-03-18', 'SO-2026-004', 'PT Nusantara Raya', 'Sewa Minibus', 1, 'AC tidak berfungsi', 5500000.00, 'Menunggu', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'RTR-005', '2026-04-02', 'SO-2026-006', 'PT Berlian Trans', 'Sewa Bus', 1, 'Pembatalan sebagian order', 10000000.00, 'Selesai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'RTR-006', '2026-04-15', 'SO-2026-005', 'CV Jaya Mandiri', 'Sewa MPV', 2, 'Unit terlambat pengiriman', 8000000.00, 'Diproses', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'RTR-007', '2026-05-01', 'SO-2026-008', 'PT Sejahtera Abadi', 'Sewa Sedan', 1, 'Kendaraan tidak sesuai pesanan', 3500000.00, 'Menunggu', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'RTR-008', '2026-05-15', 'SO-2026-009', 'PT Prima Raya', 'Sewa SUV', 1, 'Kondisi kendaraan buruk', 6000000.00, 'Selesai', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `review_legals`
--

CREATE TABLE `review_legals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `pemohon` varchar(255) NOT NULL,
  `dokumen` varchar(255) NOT NULL,
  `status_review` varchar(255) NOT NULL,
  `pic_legal` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_no` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `produk_jasa` varchar(255) NOT NULL,
  `qty` bigint(20) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `status_order` varchar(255) NOT NULL,
  `metode_pembayaran` varchar(255) NOT NULL,
  `sales` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `order_no`, `tanggal`, `pelanggan`, `produk_jasa`, `qty`, `total_harga`, `status_order`, `metode_pembayaran`, `sales`, `created_at`, `updated_at`) VALUES
(1, 'SO-2026-001', '2026-01-20', 'PT Maju Bersama', 'Sewa Minibus 2 Unit', 2, 10000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'SO-2026-002', '2026-02-05', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'SO-2026-003', '2026-02-18', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 'Diproses', 'Tempo', 'Cici', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'SO-2026-004', '2026-03-01', 'PT Nusantara Raya', 'Sewa Minibus', 2, 11000000.00, 'Selesai', 'Kredit', 'Dani', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'SO-2026-005', '2026-03-10', 'CV Jaya Mandiri', 'Sewa MPV 4 Unit', 4, 16000000.00, 'Diproses', 'Transfer Bank', 'Andi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'SO-2026-006', '2026-03-25', 'PT Berlian Trans', 'Sewa Bus Medium 2 Unit', 2, 20000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'SO-2026-007', '2026-04-05', 'CV Mitra Logistik', 'Sewa Truk 3 Unit', 3, 22500000.00, 'Dibatalkan', 'Transfer Bank', 'Cici', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'SO-2026-008', '2026-04-20', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 10500000.00, 'Diproses', 'Cash', 'Dani', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'SO-2026-009', '2026-05-08', 'PT Prima Raya', 'Sewa SUV', 2, 12000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'SO-2026-010', '2026-05-20', 'PT Sinar Harapan', 'Sewa Minibus', 1, 5500000.00, 'Diproses', 'Tempo', 'Budi', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `segmentasis`
--

CREATE TABLE `segmentasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `segment_code` varchar(255) NOT NULL,
  `segment_name` varchar(255) NOT NULL,
  `segmentation_criteria` text NOT NULL,
  `customer_count` bigint(20) NOT NULL DEFAULT 0,
  `campaign_goal` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `segmentasis`
--

INSERT INTO `segmentasis` (`id`, `segment_code`, `segment_name`, `segmentation_criteria`, `customer_count`, `campaign_goal`, `status`, `created_at`, `updated_at`) VALUES
(1, 'SEG001', 'Corporate Client', 'Perusahaan dengan kontrak bulanan', 15, 'Retain & Upsell', 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'SEG002', 'Individual Frequent', 'Individu rental >3x dalam 6 bulan', 48, 'Loyalty Program', 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sertifikasi_perizinans`
--

CREATE TABLE `sertifikasi_perizinans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `nomor` varchar(255) NOT NULL,
  `instansi` varchar(255) NOT NULL,
  `berlaku_hingga` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `server_clouds`
--

CREATE TABLE `server_clouds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_server` varchar(255) NOT NULL,
  `jenis_server` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `provider_cloud` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `backup_aktif` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `server_clouds`
--

INSERT INTO `server_clouds` (`id`, `nama_server`, `jenis_server`, `lokasi`, `os`, `provider_cloud`, `status`, `backup_aktif`, `created_at`, `updated_at`) VALUES
(1, 'web-server-01', 'Cloud', 'AWS ap-southeast-1', 'Ubuntu 22.04 LTS', 'AWS', 'Aktif', 1, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'db-server-01', 'Cloud', 'AWS ap-southeast-1', 'Amazon Linux 2', 'AWS', 'Aktif', 1, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'file-server-local', 'Physical', 'Data Center Cibitung', 'Windows Server 2022', NULL, 'Aktif', 1, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'dev-server-01', 'VPS', 'Niagahoster VPS', 'Ubuntu 20.04 LTS', 'Niagahoster', 'Aktif', 0, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'backup-server-01', 'Physical', 'Ruang Server Jakarta', 'CentOS 7', NULL, 'Maintenance', 0, '2026-08-10 23:55:35', '2026-08-10 23:55:35');

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
(1, 1, 'Ganti Oli Mesin', 350000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(2, 1, 'Tune Up', 500000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(3, 1, 'Spooring Balancing', 250000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(4, 1, 'Ganti Kampas Rem', 450000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(5, 1, 'Service AC', 600000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(6, 1, 'Ganti Ban', 900000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(7, 1, 'Overhaul Mesin', 4500000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(8, 1, 'Cuci Mobil Premium', 75000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(9, 1, 'Ganti Aki', 1200000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(10, 1, 'Perbaikan Suspensi', 1800000, '2026-08-10 23:55:28', '2026-08-10 23:55:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_asuransi`
--

CREATE TABLE `service_asuransi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `nama_asuransi` varchar(255) DEFAULT NULL,
  `jenis_asuransi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_service` date NOT NULL,
  `periode_mulai` date DEFAULT NULL,
  `periode_selesai` date DEFAULT NULL,
  `kilometer` decimal(12,2) NOT NULL DEFAULT 0.00,
  `biaya` decimal(15,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti` text DEFAULT NULL,
  `attachment` text DEFAULT NULL,
  `status` enum('bermasalah','selesai') NOT NULL DEFAULT 'bermasalah',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service_asuransi`
--

INSERT INTO `service_asuransi` (`id`, `kendaraan_id`, `nama_asuransi`, `jenis_asuransi_id`, `tanggal_service`, `periode_mulai`, `periode_selesai`, `kilometer`, `biaya`, `keterangan`, `bukti`, `attachment`, `status`, `created_at`, `updated_at`) VALUES
(2, 15, NULL, NULL, '2026-08-11', '2026-08-11', '2026-08-11', 32.00, 3466980483.00, 'Eligendi veritatis e', '\"[{\\\"path\\\":\\\"service-asuransi\\\\\\/1786457336_6a7b2cf83bf1f.png\\\",\\\"name\\\":\\\"2.png\\\"}]\"', NULL, 'selesai', '2026-08-11 14:08:56', '2026-08-11 14:18:15'),
(3, 18, 'ACA Insurance', 1, '2026-08-12', '2026-08-12', '2026-08-12', 3432343.00, NULL, 'sdfgfdsdfgfd', '[{\"path\":\"service-asuransi\\/1786548852_6a7c9274a0bf1.pdf\",\"name\":\"draft_KTR-202608-0006 (1).pdf\"},{\"path\":\"service-asuransi\\/1786548852_6a7c9274a1524.pdf\",\"name\":\"draft_KTR-202608-0006.pdf\"},{\"path\":\"service-asuransi\\/1786548852_6a7c9274a2042.png\",\"name\":\"1.png\"}]', '[{\"path\":\"service-asuransi-attachment\\/1786548852_6a7c9274a2ff5.pdf\",\"name\":\"draft_KTR-202608-0006.pdf\"}]', 'selesai', '2026-08-12 15:34:12', '2026-08-12 15:41:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_detail`
--

CREATE TABLE `service_detail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `service_history_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_service` date NOT NULL,
  `kilometer` bigint(20) NOT NULL DEFAULT 0,
  `status` enum('Layak','Tidak Layak') NOT NULL DEFAULT 'Layak',
  `biaya` bigint(20) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `bukti` text DEFAULT NULL,
  `attachment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service_detail`
--

INSERT INTO `service_detail` (`id`, `kendaraan_id`, `service_history_id`, `tanggal_service`, `kilometer`, `status`, `biaya`, `keterangan`, `bukti`, `attachment`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2026-03-28', 61859, 'Layak', 850000, 'Ganti oli mesin', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(2, 2, NULL, '2025-09-07', 106736, 'Layak', 200000, 'Tune up', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(3, 3, NULL, '2025-09-16', 83989, 'Layak', 1150000, 'Ganti kampas rem', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(4, 4, NULL, '2026-06-17', 92159, 'Tidak Layak', 100000, 'Servis AC', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(5, 5, NULL, '2025-10-24', 56026, 'Layak', 750000, 'Ganti ban', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(6, 6, NULL, '2025-09-22', 79718, 'Layak', 1300000, 'Overhaul mesin', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(7, 7, NULL, '2026-04-21', 102636, 'Layak', 1400000, 'Ganti aki', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(8, 8, NULL, '2026-02-15', 67960, 'Tidak Layak', 600000, 'Servis transmisi', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(9, 9, NULL, '2025-12-23', 84443, 'Layak', 700000, 'Ganti filter udara', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(10, 10, NULL, '2026-03-30', 53855, 'Layak', 400000, 'Perbaikan body', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(11, 11, NULL, '2026-08-07', 115961, 'Layak', 150000, 'Ganti busi', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(12, 12, NULL, '2026-03-25', 90815, 'Tidak Layak', 1000000, 'Servis suspensi', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(13, 13, NULL, '2026-07-10', 27206, 'Layak', 600000, 'Ganti timing belt', NULL, NULL, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(14, 14, NULL, '2026-05-31', 65083, 'Layak', 500000, 'Kalibrasi lampu', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(15, 15, NULL, '2025-11-30', 35494, 'Layak', 450000, 'Servis power steering', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(16, 16, NULL, '2025-09-10', 100115, 'Tidak Layak', 750000, 'Ganti knalpot', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(17, 17, NULL, '2026-02-01', 30341, 'Layak', 1150000, 'Perbaikan sistem pendingin', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(18, 18, NULL, '2026-02-10', 118609, 'Layak', 1300000, 'Ganti kopling', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(19, 19, NULL, '2025-09-30', 17485, 'Layak', 1350000, 'Servis rem tangan', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(20, 20, NULL, '2025-09-12', 57403, 'Tidak Layak', 1350000, 'Ganti wiper', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(21, 21, NULL, '2026-03-08', 84239, 'Layak', 1250000, 'Ganti oli mesin', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(22, 22, NULL, '2026-07-01', 16194, 'Layak', 350000, 'Tune up', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(23, 23, NULL, '2026-04-04', 28039, 'Layak', 1000000, 'Ganti kampas rem', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(24, 24, NULL, '2025-08-27', 93403, 'Tidak Layak', 350000, 'Servis AC', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(25, 25, NULL, '2025-09-13', 62050, 'Layak', 1450000, 'Ganti ban', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(26, 26, NULL, '2025-11-27', 75223, 'Layak', 1350000, 'Overhaul mesin', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(27, 27, NULL, '2026-04-19', 102164, 'Layak', 1500000, 'Ganti aki', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(28, 28, NULL, '2025-08-24', 10961, 'Tidak Layak', 800000, 'Servis transmisi', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(29, 29, NULL, '2026-04-07', 5499, 'Layak', 450000, 'Ganti filter udara', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(30, 30, NULL, '2026-06-28', 53639, 'Layak', 400000, 'Perbaikan body', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(31, 31, NULL, '2025-10-07', 58249, 'Layak', 1050000, 'Ganti busi', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(32, 32, NULL, '2026-03-09', 67312, 'Tidak Layak', 1100000, 'Servis suspensi', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(33, 33, NULL, '2026-03-16', 32886, 'Layak', 1300000, 'Ganti timing belt', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(34, 34, NULL, '2025-09-17', 77810, 'Layak', 1000000, 'Kalibrasi lampu', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(35, 35, NULL, '2025-11-16', 26980, 'Layak', 250000, 'Servis power steering', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(36, 36, NULL, '2025-11-12', 89471, 'Tidak Layak', 150000, 'Ganti knalpot', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(37, 37, NULL, '2026-03-19', 90524, 'Layak', 1200000, 'Perbaikan sistem pendingin', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(38, 38, NULL, '2026-02-15', 17618, 'Layak', 300000, 'Ganti kopling', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(39, 39, NULL, '2026-07-10', 96061, 'Layak', 1100000, 'Servis rem tangan', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(40, 40, NULL, '2026-01-09', 99420, 'Tidak Layak', 100000, 'Ganti wiper', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(41, 41, NULL, '2026-01-01', 69969, 'Layak', 450000, 'Ganti oli mesin', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(42, 42, NULL, '2026-07-05', 61374, 'Layak', 400000, 'Tune up', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(43, 43, NULL, '2025-09-19', 84302, 'Layak', 950000, 'Ganti kampas rem', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(44, 44, NULL, '2026-07-23', 104261, 'Tidak Layak', 500000, 'Servis AC', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(45, 45, NULL, '2025-09-10', 79443, 'Layak', 1000000, 'Ganti ban', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(46, 46, NULL, '2026-03-23', 112028, 'Layak', 750000, 'Overhaul mesin', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(47, 47, NULL, '2026-04-03', 48252, 'Layak', 1250000, 'Ganti aki', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(48, 48, NULL, '2025-10-07', 50505, 'Tidak Layak', 1300000, 'Servis transmisi', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(49, 49, NULL, '2026-01-02', 45829, 'Layak', 1200000, 'Ganti filter udara', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29'),
(50, 50, NULL, '2026-05-19', 84178, 'Layak', 200000, 'Perbaikan body', NULL, NULL, '2026-08-10 23:55:29', '2026-08-10 23:55:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_history`
--

CREATE TABLE `service_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `keluhan` text DEFAULT NULL,
  `kilometer` bigint(20) NOT NULL DEFAULT 0,
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
(1, 1, 'Oli mesin sudah hitam dan rem berbunyi', 45000, 850000, 'selesai', NULL, 0, 0, 'stabil', '2026-07-22', '2026-08-10 23:55:28', '2026-08-10 23:55:28', NULL),
(2, 2, 'AC tidak dingin', 52000, 600000, 'proses', NULL, 0, 0, 'stabil', '2026-08-01', '2026-08-10 23:55:28', '2026-08-10 23:55:28', NULL),
(3, 3, 'Ban depan aus', 70000, 1800000, 'selesai', NULL, 0, 0, 'stabil', '2026-07-12', '2026-08-10 23:55:28', '2026-08-10 23:55:28', NULL),
(4, 1, 'Mesin getar saat idle', 47000, 500000, 'proses', NULL, 0, 0, 'stabil', '2026-08-06', '2026-08-10 23:55:28', '2026-08-10 23:55:28', NULL),
(5, 2, 'Ganti aki', 55000, 1200000, 'selesai', NULL, 0, 0, 'stabil', '2026-07-27', '2026-08-10 23:55:28', '2026-08-10 23:55:28', NULL);

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
  `last_activity` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `nama_bank` varchar(255) DEFAULT NULL,
  `nomor_rekening` varchar(255) DEFAULT NULL,
  `atas_nama_rekening` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(255) DEFAULT NULL,
  `batas_reminder` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `satuan_reminder` enum('hari','minggu','bulan','tahun') NOT NULL DEFAULT 'hari',
  `ppn_default` decimal(5,2) DEFAULT 0.00,
  `pph_default` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `nama_perusahaan`, `alamat`, `telepon`, `fax`, `email`, `website`, `logo`, `nama_bank`, `nomor_rekening`, `atas_nama_rekening`, `kode_pos`, `batas_reminder`, `satuan_reminder`, `ppn_default`, `pph_default`, `created_at`, `updated_at`) VALUES
(1, 'PT Rental Kendaraan Indonesia', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-12345678', NULL, 'info@rentalkendaraan.co.id', 'https://rentalkendaraan.co.id', NULL, 'Bank BCA', '1234567890', 'PT Rental Kendaraan Indonesia', '10110', 1, 'bulan', 0.00, 0.00, '2026-08-10 23:55:31', '2026-08-10 23:55:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shift_lemburs`
--

CREATE TABLE `shift_lemburs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `shift` varchar(255) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `jam_lembur` varchar(255) DEFAULT NULL,
  `total_jam` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shift_lemburs`
--

INSERT INTO `shift_lemburs` (`id`, `nama_pegawai`, `shift`, `jam_masuk`, `jam_pulang`, `jam_lembur`, `total_jam`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Teguh Santosa', 'Pagi', '07:00:00', '15:00:00', '2', '10', 'Lembur pengiriman barang', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(2, 'Arif Budiman', 'Pagi', '07:00:00', '15:00:00', NULL, '8', 'Shift reguler', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(3, 'Dody Kurniawan', 'Siang', '15:00:00', '23:00:00', '1', '9', 'Lembur rapat koordinasi', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(4, 'Rizky Fadillah', 'Pagi', '08:00:00', '17:00:00', '3', '12', 'Lembur deploy sistem', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(5, 'Yusuf Hidayat', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler IT', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(6, 'Hendra Gunawan', 'Pagi', '08:00:00', '17:00:00', '2', '11', 'Lembur maintenance server', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(7, 'Wahyu Nugroho', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(8, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '1.5', '9.5', 'Lembur laporan pajak', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(9, 'Linda Permata', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur audit internal', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(10, 'Rini Apriani', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler HRD', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(11, 'Eko Prasetyo', 'Malam', '23:00:00', '07:00:00', '1', '9', 'Shift malam + lembur', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(12, 'Dewi Kusuma', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(13, 'Teguh Santosa', 'Malam', '23:00:00', '07:00:00', '2', '10', 'Lembur pengawasan malam', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(14, 'Arif Budiman', 'Siang', '15:00:00', '23:00:00', NULL, '8', 'Rotasi shift siang', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(15, 'Rizky Fadillah', 'Siang', '12:00:00', '21:00:00', '2', '11', 'Lembur perbaikan bug produksi', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(16, 'Yusuf Hidayat', 'Malam', '23:00:00', '07:00:00', NULL, '8', 'Shift malam on-call', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(17, 'Wahyu Nugroho', 'Pagi', '07:30:00', '16:30:00', '1', '9', 'Lembur tutup buku', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(18, 'Dody Kurniawan', 'Pagi', '07:00:00', '16:00:00', NULL, '8', 'Shift reguler operasional', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(19, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur SPT tahunan', '2026-08-10 23:55:39', '2026-08-10 23:55:39'),
(20, 'Hendra Gunawan', 'Siang', '12:00:00', '21:00:00', '1', '9', 'Lembur migrasi data', '2026-08-10 23:55:39', '2026-08-10 23:55:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `signature_dokumens`
--

CREATE TABLE `signature_dokumens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_id` varchar(255) NOT NULL,
  `jenis_dokumen` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `pihak_terlibat` varchar(255) NOT NULL,
  `status_ttd` varchar(255) NOT NULL,
  `platform_digisign` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `signature_dokumens`
--

INSERT INTO `signature_dokumens` (`id`, `document_id`, `jenis_dokumen`, `tanggal`, `pihak_terlibat`, `status_ttd`, `platform_digisign`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'DOC-2026-001', 'Kontrak', '2026-01-20', 'PT Maju Bersama & PT APY Rent', 'Ditandatangani', 'PrivyID', 'Kontrak sewa 3 bulan', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'DOC-2026-002', 'Perjanjian', '2026-02-01', 'CV Karya Indah & PT APY Rent', 'Ditandatangani', 'DocuSign', 'PKS layanan transportasi', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'DOC-2026-003', 'MOU', '2026-02-15', 'PT Global Trans & PT APY Rent', 'Menunggu', 'PrivyID', 'Menunggu tanda tangan direktur', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'DOC-2026-004', 'Penawaran', '2026-03-01', 'PT Nusantara Raya & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Penawaran disetujui', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'DOC-2026-005', 'Kontrak', '2026-03-15', 'CV Jaya Mandiri & PT APY Rent', 'Ditolak', 'PrivyID', 'Ditolak karena klausul tidak sesuai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'DOC-2026-006', 'Perjanjian', '2026-04-01', 'PT Berlian Trans & PT APY Rent', 'Ditandatangani', 'Peruri', 'PKS jangka panjang', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'DOC-2026-007', 'Kontrak', '2026-04-20', 'PT Prima Raya & PT APY Rent', 'Menunggu', 'DocuSign', 'Dalam proses review', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'DOC-2026-008', 'MOU', '2026-05-05', 'PT Sejahtera Abadi & PT APY Rent', 'Ditandatangani', 'Manual', 'Ditandatangani secara fisik', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'DOC-2026-009', 'Lainnya', '2026-05-20', 'CV Mitra Logistik & PT APY Rent', 'Menunggu', 'PrivyID', 'Surat kuasa armada', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'DOC-2026-010', 'Kontrak', '2026-06-01', 'PT Sinar Harapan & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Kontrak perpanjangan', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `skill_matrices`
--

CREATE TABLE `skill_matrices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `skill` varchar(255) NOT NULL,
  `level` bigint(20) UNSIGNED NOT NULL,
  `sertifikasi` enum('Y','T') NOT NULL,
  `evaluator` varchar(255) NOT NULL,
  `tanggal_evaluasi` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `skill_matrices`
--

INSERT INTO `skill_matrices` (`id`, `nama_pegawai`, `skill`, `level`, `sertifikasi`, `evaluator`, `tanggal_evaluasi`, `created_at`, `updated_at`) VALUES
(1, 'Rizky Fadillah', 'Laravel', 4, 'Y', 'Hendra Gunawan', '2026-05-23', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(2, 'Yusuf Hidayat', 'Vue.js', 3, 'T', 'Hendra Gunawan', '2026-01-17', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(3, 'Hendra Gunawan', 'MySQL', 1, 'T', 'Hendra Gunawan', '2025-12-10', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(4, 'Wahyu Nugroho', 'PHP', 4, 'Y', 'Hendra Gunawan', '2025-11-10', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(5, 'Fitri Handayani', 'Microsoft Excel', 5, 'T', 'Linda Permata', '2026-04-28', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(6, 'Linda Permata', 'Akuntansi', 4, 'T', 'Linda Permata', '2026-03-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(7, 'Rini Apriani', 'Perpajakan', 5, 'Y', 'Linda Permata', '2025-12-27', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(8, 'Eko Prasetyo', 'SAP', 2, 'T', 'Linda Permata', '2025-10-08', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(9, 'Dewi Kusuma', 'Rekrutmen', 1, 'T', 'Dewi Kusuma', '2025-09-27', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(10, 'Teguh Santosa', 'Payroll', 5, 'Y', 'Dewi Kusuma', '2026-04-19', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(11, 'Arif Budiman', 'K3', 3, 'T', 'Dewi Kusuma', '2025-08-20', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(12, 'Dody Kurniawan', 'Negosiasi', 2, 'T', 'Dody Kurniawan', '2025-11-03', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(13, 'Rizky Fadillah', 'AutoCAD', 4, 'Y', 'Teguh Santosa', '2026-07-05', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(14, 'Yusuf Hidayat', 'Troubleshooting', 2, 'T', 'Yusuf Hidayat', '2026-04-13', '2026-08-10 23:55:37', '2026-08-10 23:55:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `software_licenses`
--

CREATE TABLE `software_licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_software` varchar(255) NOT NULL,
  `jenis_lisensi` varchar(255) NOT NULL,
  `jumlah_lisensi` bigint(20) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `masa_berlaku` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `tanggal_perpanjangan` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `software_licenses`
--

INSERT INTO `software_licenses` (`id`, `nama_software`, `jenis_lisensi`, `jumlah_lisensi`, `provider`, `masa_berlaku`, `status`, `tanggal_perpanjangan`, `created_at`, `updated_at`) VALUES
(1, 'Microsoft Office 365', 'Subscription', 25, 'Microsoft', '2025-12-31', 'Aktif', '2024-12-01', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'Adobe Creative Cloud', 'Subscription', 5, 'Adobe', '2025-06-30', 'Aktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'Kaspersky Endpoint Security', 'Perpetual', 50, 'Kaspersky', '2024-03-31', 'Expired', '2024-04-01', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'Zoom Pro', 'Subscription', 10, 'Zoom', '2025-09-30', 'Aktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'AutoCAD 2024', 'Perpetual', 3, 'Autodesk', '2026-01-01', 'Aktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sosmedps`
--

CREATE TABLE `sosmedps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_kampanye` varchar(255) NOT NULL,
  `channel` varchar(255) NOT NULL,
  `utm_source` varchar(255) NOT NULL,
  `utm_campaign` varchar(255) NOT NULL,
  `klik` bigint(20) NOT NULL DEFAULT 0,
  `konversi` bigint(20) NOT NULL DEFAULT 0,
  `total_biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_penjualan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `roi` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sosmedps`
--

INSERT INTO `sosmedps` (`id`, `id_kampanye`, `channel`, `utm_source`, `utm_campaign`, `klik`, `konversi`, `total_biaya`, `total_penjualan`, `roi`, `created_at`, `updated_at`) VALUES
(1, 'SMP001', 'Instagram', 'instagram', 'promo_rental_july', 320, 18, 1500000.00, 9000000.00, 500.00, '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'SMP002', 'Facebook', 'facebook', 'awareness_apyrent', 580, 25, 2000000.00, 12500000.00, 525.00, '2026-08-10 23:55:33', '2026-08-10 23:55:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stnk`
--

CREATE TABLE `stnk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `nopol` varchar(255) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) NOT NULL,
  `jenis_model` varchar(255) NOT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bukti` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stnk_histories`
--

CREATE TABLE `stnk_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stnk_id` bigint(20) UNSIGNED NOT NULL,
  `kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `nopol` varchar(255) NOT NULL,
  `merk` varchar(255) DEFAULT NULL,
  `nama_pemilik` varchar(255) DEFAULT NULL,
  `jenis_model` varchar(255) DEFAULT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bukti` varchar(255) DEFAULT NULL,
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `struktur_organisasis`
--

CREATE TABLE `struktur_organisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_jabatan` varchar(255) NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `nip_id` varchar(255) NOT NULL,
  `departemen` varchar(255) NOT NULL,
  `atasan_langsung` varchar(255) DEFAULT NULL,
  `lokasi` varchar(255) NOT NULL,
  `status_jabatan` enum('Tetap','Kontrak') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `struktur_organisasis`
--

INSERT INTO `struktur_organisasis` (`id`, `nama_jabatan`, `nama_pegawai`, `nip_id`, `departemen`, `atasan_langsung`, `lokasi`, `status_jabatan`, `tanggal_mulai`, `created_at`, `updated_at`) VALUES
(1, 'Direktur Utama', 'Budi Santoso', 'NIP-001', 'Direksi', NULL, 'Jakarta', 'Tetap', '2018-01-02', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(2, 'Direktur Operasional', 'Siti Rahayu', 'NIP-002', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(3, 'Direktur Keuangan', 'Agus Wibowo', 'NIP-003', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(4, 'Manager HRD', 'Dewi Kusuma', 'NIP-010', 'HRD', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-01-15', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(5, 'Staff HRD', 'Rini Apriani', 'NIP-011', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Tetap', '2021-04-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(6, 'Staff HRD', 'Eko Prasetyo', 'NIP-012', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Kontrak', '2023-07-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(7, 'Manager IT', 'Hendra Gunawan', 'NIP-020', 'IT', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-02-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(8, 'Developer', 'Rizky Fadillah', 'NIP-021', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2022-05-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(9, 'IT Support', 'Yusuf Hidayat', 'NIP-022', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2023-01-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(10, 'Manager Finance', 'Linda Permata', 'NIP-030', 'Finance', 'Agus Wibowo', 'Jakarta', 'Tetap', '2020-06-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(11, 'Staff Accounting', 'Wahyu Nugroho', 'NIP-031', 'Finance', 'Linda Permata', 'Jakarta', 'Tetap', '2021-08-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(12, 'Staff Pajak', 'Fitri Handayani', 'NIP-032', 'Finance', 'Linda Permata', 'Jakarta', 'Kontrak', '2022-09-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(13, 'Manager Operasional', 'Dody Kurniawan', 'NIP-040', 'Operasional', 'Siti Rahayu', 'Surabaya', 'Tetap', '2019-11-01', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(14, 'Supervisor Lapangan', 'Teguh Santosa', 'NIP-041', 'Operasional', 'Dody Kurniawan', 'Surabaya', 'Tetap', '2021-01-10', '2026-08-10 23:55:37', '2026-08-10 23:55:37'),
(15, 'Teknisi', 'Arif Budiman', 'NIP-042', 'Operasional', 'Teguh Santosa', 'Surabaya', 'Kontrak', '2023-03-15', '2026-08-10 23:55:37', '2026-08-10 23:55:37');

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
(1, 1, 'CV Suku Cadang Motor', '081234567890', 'Oli Mesin', 50, 75000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(2, 1, 'PT Ban Indonesia', '082233445566', 'Ban Mobil', 20, 850000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(3, 1, 'Toko Sparepart Jaya', '081377788899', 'Aki Mobil', 15, 1200000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(4, 1, 'CV Audio Mobil', '081299988877', 'GPS Tracker', 10, 450000, '2026-08-10 23:55:28', '2026-08-10 23:55:28'),
(5, 1, 'PT Diesel Utama', '082122334455', 'Filter Solar', 40, 95000, '2026-08-10 23:55:28', '2026-08-10 23:55:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_backups`
--

CREATE TABLE `system_backups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sistem` varchar(255) NOT NULL,
  `metode_backup` varchar(255) NOT NULL,
  `frekuensi` varchar(255) NOT NULL,
  `lokasi_backup` varchar(255) NOT NULL,
  `status_backup` varchar(255) NOT NULL,
  `uji_restore_terakhir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `system_backups`
--

INSERT INTO `system_backups` (`id`, `sistem`, `metode_backup`, `frekuensi`, `lokasi_backup`, `status_backup`, `uji_restore_terakhir`, `created_at`, `updated_at`) VALUES
(1, 'Database ERP Production', 'Full', 'Harian', 'AWS S3 Bucket', 'Aktif', '2025-01-15', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'File Server Dokumen', 'Incremental', 'Mingguan', 'NAS Lokal + Cloud', 'Aktif', '2024-12-01', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'Email Server', 'Full', 'Mingguan', 'Tape Drive', 'Aktif', '2025-02-01', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'Aplikasi HRIS', 'Differential', 'Harian', 'GCP Storage', 'Gagal', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'Website Company Profile', 'Full', 'Bulanan', 'Hosting cPanel', 'Nonaktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `target_penjualans`
--

CREATE TABLE `target_penjualans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_sales` varchar(255) NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `target_penjualan` decimal(15,2) NOT NULL,
  `pencapaian` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `target_penjualans`
--

INSERT INTO `target_penjualans` (`id`, `nama_sales`, `bulan`, `target_penjualan`, `pencapaian`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Andi', '2026-01', 47000000.00, 45000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'Budi', '2026-01', 27000000.00, 44000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'Cici', '2026-01', 21000000.00, 27000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'Dani', '2026-01', 45000000.00, 31000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'Andi', '2026-02', 47000000.00, 32000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'Budi', '2026-02', 29000000.00, 64000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'Cici', '2026-02', 21000000.00, 26000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'Dani', '2026-02', 48000000.00, 49000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'Andi', '2026-03', 48000000.00, 52000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'Budi', '2026-03', 60000000.00, 30000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(11, 'Cici', '2026-03', 46000000.00, 31000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(12, 'Dani', '2026-03', 35000000.00, 46000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(13, 'Andi', '2026-04', 33000000.00, 39000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(14, 'Budi', '2026-04', 46000000.00, 46000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(15, 'Cici', '2026-04', 43000000.00, 19000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(16, 'Dani', '2026-04', 21000000.00, 25000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(17, 'Andi', '2026-05', 56000000.00, 64000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(18, 'Budi', '2026-05', 30000000.00, 49000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(19, 'Cici', '2026-05', 59000000.00, 35000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(20, 'Dani', '2026-05', 41000000.00, 34000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(21, 'Andi', '2026-06', 37000000.00, 25000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(22, 'Budi', '2026-06', 46000000.00, 19000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(23, 'Cici', '2026-06', 47000000.00, 20000000.00, 'Belum mencapai target', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(24, 'Dani', '2026-06', 27000000.00, 40000000.00, 'Target tercapai', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trackingutms`
--

CREATE TABLE `trackingutms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_tracking` varchar(255) NOT NULL,
  `url_tujuan` varchar(255) NOT NULL,
  `utm_source` varchar(255) NOT NULL,
  `utm_medium` varchar(255) NOT NULL,
  `utm_campaign` varchar(255) NOT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `utm_content` varchar(255) DEFAULT NULL,
  `total_klik` bigint(20) NOT NULL DEFAULT 0,
  `total_konversi` bigint(20) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `trackingutms`
--

INSERT INTO `trackingutms` (`id`, `kode_tracking`, `url_tujuan`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `total_klik`, `total_konversi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'UTM001', 'https://apyrent.com/promo', 'google', 'cpc', 'rental_promo_q3', 'sewa mobil jakarta', 'text_ad_1', 450, 32, 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33'),
(2, 'UTM002', 'https://apyrent.com/fleet', 'email', 'newsletter', 'new_cars_2026', NULL, 'banner_top', 280, 19, 'Aktif', '2026-08-10 23:55:33', '2026-08-10 23:55:33');

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
(1, 'Test User', 'testuser', 'test@example.com', '$2y$12$DS.9NWIxcTROul28vyHkO.xg0Gu.fh8A.LQv4O5nL2Off3c.Ae5Tm', '08123456789', NULL, 'superadmin', 'aktif', NULL, '2026-08-10 23:55:25', '2026-08-10 23:55:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_accesses`
--

CREATE TABLE `user_accesses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pengguna` varchar(255) NOT NULL,
  `divisi` varchar(255) NOT NULL,
  `role_akses` varchar(255) NOT NULL,
  `sistem` varchar(255) NOT NULL,
  `status_akses` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user_accesses`
--

INSERT INTO `user_accesses` (`id`, `nama_pengguna`, `divisi`, `role_akses`, `sistem`, `status_akses`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', 'IT', 'Administrator', 'ERP Sistem', 'Aktif', 'Admin utama sistem', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'Sari Dewi', 'Finance', 'Read-Write', 'Accounting Software', 'Aktif', NULL, '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'Rudi Hermawan', 'HR', 'Read Only', 'HRIS', 'Aktif', 'Akses terbatas laporan', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'Dewi Cahyani', 'Sales', 'User', 'CRM', 'Nonaktif', 'Karyawan resign Desember 2024', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'Anto Nugroho', 'Operasional', 'Read-Write', 'ERP Sistem', 'Suspended', 'Akses ditangguhkan sementara', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendoreos`
--

CREATE TABLE `vendoreos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_vendor` varchar(255) DEFAULT NULL,
  `nama_vendor` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `pic_vendor` varchar(255) DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `produk_jasa` varchar(255) DEFAULT NULL,
  `rating` bigint(20) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `tanggal_terakhir_order` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendoreos`
--

INSERT INTO `vendoreos` (`id`, `kode_vendor`, `nama_vendor`, `kategori`, `alamat`, `pic_vendor`, `no_telp`, `produk_jasa`, `rating`, `status`, `tanggal_terakhir_order`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'VDR-001', 'CV Vendor Nusantara 1', 'Bahan Baku', 'Jl. Jakarta No. 3', 'PIC Vendor 1', '08920431736', 'Kain Katun', 4, 'Aktif', '2025-08-27', 'Catatan vendor ke-1', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(2, 'VDR-002', 'PT Vendor Nusantara 2', 'Packaging', 'Jl. Bandung No. 6', 'PIC Vendor 2', '08585172209', 'Kardus dan Label', 2, 'Aktif', '2025-12-14', 'Catatan vendor ke-2', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(3, 'VDR-003', 'CV Vendor Nusantara 3', 'Jasa IT', 'Jl. Semarang No. 9', 'PIC Vendor 3', '08778053069', 'Maintenance Sistem', 5, 'Aktif', '2026-05-03', 'Catatan vendor ke-3', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(4, 'VDR-004', 'PT Vendor Nusantara 4', 'Spare Part', 'Jl. Yogyakarta No. 12', 'PIC Vendor 4', '08572639309', 'Spare Part Kendaraan', 5, 'Aktif', '2025-11-11', 'Catatan vendor ke-4', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(5, 'VDR-005', 'CV Vendor Nusantara 5', 'Logistik', 'Jl. Surabaya No. 15', 'PIC Vendor 5', '08594660518', 'Pengiriman Barang', 4, 'Tidak Aktif', '2026-02-19', 'Catatan vendor ke-5', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(6, 'VDR-006', 'PT Vendor Nusantara 6', 'Maintenance', 'Jl. Medan No. 18', 'PIC Vendor 6', '08904863914', 'Servis Mesin', 3, 'Aktif', '2026-05-30', 'Catatan vendor ke-6', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(7, 'VDR-007', 'CV Vendor Nusantara 7', 'Cleaning', 'Jl. Makassar No. 21', 'PIC Vendor 7', '08501718510', 'Jasa Kebersihan', 3, 'Aktif', '2025-12-17', 'Catatan vendor ke-7', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(8, 'VDR-008', 'PT Vendor Nusantara 8', 'Security', 'Jl. Palembang No. 24', 'PIC Vendor 8', '08545622196', 'Jasa Keamanan', 3, 'Aktif', '2025-08-30', 'Catatan vendor ke-8', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(9, 'VDR-009', 'CV Vendor Nusantara 9', 'Percetakan', 'Jl. Malang No. 27', 'PIC Vendor 9', '08737020145', 'Cetak Dokumen', 5, 'Aktif', '2026-07-31', 'Catatan vendor ke-9', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(10, 'VDR-010', 'PT Vendor Nusantara 10', 'ATK', 'Jl. Solo No. 30', 'PIC Vendor 10', '08226624752', 'Alat Tulis Kantor', 3, 'Tidak Aktif', '2026-04-01', 'Catatan vendor ke-10', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(11, 'VDR-011', 'CV Vendor Nusantara 11', 'Bahan Baku', 'Jl. Jakarta No. 33', 'PIC Vendor 11', '08705679527', 'Cat dan Kimia', 4, 'Aktif', '2026-05-12', 'Catatan vendor ke-11', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(12, 'VDR-012', 'PT Vendor Nusantara 12', 'Packaging', 'Jl. Bandung No. 36', 'PIC Vendor 12', '08716412928', 'Komputer dan Aksesoris', 2, 'Aktif', '2026-05-05', 'Catatan vendor ke-12', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(13, 'VDR-013', 'CV Vendor Nusantara 13', 'Jasa IT', 'Jl. Semarang No. 39', 'PIC Vendor 13', '08565387208', 'Mebel Kantor', 2, 'Aktif', '2026-02-23', 'Catatan vendor ke-13', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(14, 'VDR-014', 'PT Vendor Nusantara 14', 'Spare Part', 'Jl. Yogyakarta No. 42', 'PIC Vendor 14', '08744732900', 'Genset dan Panel', 5, 'Aktif', '2025-09-18', 'Catatan vendor ke-14', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(15, 'VDR-015', 'CV Vendor Nusantara 15', 'Logistik', 'Jl. Surabaya No. 45', 'PIC Vendor 15', '08583099626', 'Seragam Karyawan', 5, 'Tidak Aktif', '2026-07-17', 'Catatan vendor ke-15', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(16, 'VDR-016', 'PT Vendor Nusantara 16', 'Maintenance', 'Jl. Medan No. 48', 'PIC Vendor 16', '08874360830', 'Kain Katun', 2, 'Aktif', '2025-10-21', 'Catatan vendor ke-16', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(17, 'VDR-017', 'CV Vendor Nusantara 17', 'Cleaning', 'Jl. Makassar No. 51', 'PIC Vendor 17', '08764783955', 'Kardus dan Label', 3, 'Aktif', '2025-12-05', 'Catatan vendor ke-17', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(18, 'VDR-018', 'PT Vendor Nusantara 18', 'Security', 'Jl. Palembang No. 54', 'PIC Vendor 18', '08965612773', 'Maintenance Sistem', 5, 'Aktif', '2026-07-16', 'Catatan vendor ke-18', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(19, 'VDR-019', 'CV Vendor Nusantara 19', 'Percetakan', 'Jl. Malang No. 57', 'PIC Vendor 19', '08449528633', 'Spare Part Kendaraan', 2, 'Aktif', '2026-01-05', 'Catatan vendor ke-19', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(20, 'VDR-020', 'PT Vendor Nusantara 20', 'ATK', 'Jl. Solo No. 60', 'PIC Vendor 20', '08954205756', 'Pengiriman Barang', 3, 'Tidak Aktif', '2025-11-21', 'Catatan vendor ke-20', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(21, 'VDR-021', 'CV Vendor Nusantara 21', 'Bahan Baku', 'Jl. Jakarta No. 63', 'PIC Vendor 21', '08648477253', 'Servis Mesin', 5, 'Aktif', '2026-05-11', 'Catatan vendor ke-21', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(22, 'VDR-022', 'PT Vendor Nusantara 22', 'Packaging', 'Jl. Bandung No. 66', 'PIC Vendor 22', '08345846912', 'Jasa Kebersihan', 2, 'Aktif', '2026-04-26', 'Catatan vendor ke-22', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(23, 'VDR-023', 'CV Vendor Nusantara 23', 'Jasa IT', 'Jl. Semarang No. 69', 'PIC Vendor 23', '08873566526', 'Jasa Keamanan', 5, 'Aktif', '2026-06-01', 'Catatan vendor ke-23', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(24, 'VDR-024', 'PT Vendor Nusantara 24', 'Spare Part', 'Jl. Yogyakarta No. 72', 'PIC Vendor 24', '08402333155', 'Cetak Dokumen', 2, 'Aktif', '2025-09-03', 'Catatan vendor ke-24', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(25, 'VDR-025', 'CV Vendor Nusantara 25', 'Logistik', 'Jl. Surabaya No. 75', 'PIC Vendor 25', '08149697903', 'Alat Tulis Kantor', 5, 'Tidak Aktif', '2026-04-25', 'Catatan vendor ke-25', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(26, 'VDR-026', 'PT Vendor Nusantara 26', 'Maintenance', 'Jl. Medan No. 78', 'PIC Vendor 26', '08563946362', 'Cat dan Kimia', 4, 'Aktif', '2026-04-22', 'Catatan vendor ke-26', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(27, 'VDR-027', 'CV Vendor Nusantara 27', 'Cleaning', 'Jl. Makassar No. 81', 'PIC Vendor 27', '08182963027', 'Komputer dan Aksesoris', 5, 'Aktif', '2025-09-29', 'Catatan vendor ke-27', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(28, 'VDR-028', 'PT Vendor Nusantara 28', 'Security', 'Jl. Palembang No. 84', 'PIC Vendor 28', '08146998683', 'Mebel Kantor', 4, 'Aktif', '2025-09-29', 'Catatan vendor ke-28', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(29, 'VDR-029', 'CV Vendor Nusantara 29', 'Percetakan', 'Jl. Malang No. 87', 'PIC Vendor 29', '08895243481', 'Genset dan Panel', 5, 'Aktif', '2025-11-07', 'Catatan vendor ke-29', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(30, 'VDR-030', 'PT Vendor Nusantara 30', 'ATK', 'Jl. Solo No. 90', 'PIC Vendor 30', '08586123339', 'Seragam Karyawan', 4, 'Tidak Aktif', '2025-10-12', 'Catatan vendor ke-30', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(31, 'VDR-031', 'CV Vendor Nusantara 31', 'Bahan Baku', 'Jl. Jakarta No. 93', 'PIC Vendor 31', '08807052627', 'Kain Katun', 2, 'Aktif', '2026-04-25', 'Catatan vendor ke-31', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(32, 'VDR-032', 'PT Vendor Nusantara 32', 'Packaging', 'Jl. Bandung No. 96', 'PIC Vendor 32', '08851128399', 'Kardus dan Label', 4, 'Aktif', '2026-05-08', 'Catatan vendor ke-32', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(33, 'VDR-033', 'CV Vendor Nusantara 33', 'Jasa IT', 'Jl. Semarang No. 99', 'PIC Vendor 33', '08868516515', 'Maintenance Sistem', 2, 'Aktif', '2026-05-07', 'Catatan vendor ke-33', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(34, 'VDR-034', 'PT Vendor Nusantara 34', 'Spare Part', 'Jl. Yogyakarta No. 102', 'PIC Vendor 34', '08154618494', 'Spare Part Kendaraan', 3, 'Aktif', '2026-05-27', 'Catatan vendor ke-34', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(35, 'VDR-035', 'CV Vendor Nusantara 35', 'Logistik', 'Jl. Surabaya No. 105', 'PIC Vendor 35', '08164300274', 'Pengiriman Barang', 4, 'Tidak Aktif', '2026-02-10', 'Catatan vendor ke-35', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(36, 'VDR-036', 'PT Vendor Nusantara 36', 'Maintenance', 'Jl. Medan No. 108', 'PIC Vendor 36', '08712735802', 'Servis Mesin', 4, 'Aktif', '2025-12-16', 'Catatan vendor ke-36', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(37, 'VDR-037', 'CV Vendor Nusantara 37', 'Cleaning', 'Jl. Makassar No. 111', 'PIC Vendor 37', '08362637132', 'Jasa Kebersihan', 4, 'Aktif', '2026-04-25', 'Catatan vendor ke-37', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(38, 'VDR-038', 'PT Vendor Nusantara 38', 'Security', 'Jl. Palembang No. 114', 'PIC Vendor 38', '08916682200', 'Jasa Keamanan', 5, 'Aktif', '2025-10-06', 'Catatan vendor ke-38', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(39, 'VDR-039', 'CV Vendor Nusantara 39', 'Percetakan', 'Jl. Malang No. 117', 'PIC Vendor 39', '08659689512', 'Cetak Dokumen', 3, 'Aktif', '2025-09-28', 'Catatan vendor ke-39', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(40, 'VDR-040', 'PT Vendor Nusantara 40', 'ATK', 'Jl. Solo No. 120', 'PIC Vendor 40', '08588685300', 'Alat Tulis Kantor', 3, 'Tidak Aktif', '2026-02-08', 'Catatan vendor ke-40', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(41, 'VDR-041', 'CV Vendor Nusantara 41', 'Bahan Baku', 'Jl. Jakarta No. 123', 'PIC Vendor 41', '08400134005', 'Cat dan Kimia', 4, 'Aktif', '2026-06-04', 'Catatan vendor ke-41', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(42, 'VDR-042', 'PT Vendor Nusantara 42', 'Packaging', 'Jl. Bandung No. 126', 'PIC Vendor 42', '08758661104', 'Komputer dan Aksesoris', 4, 'Aktif', '2026-05-01', 'Catatan vendor ke-42', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(43, 'VDR-043', 'CV Vendor Nusantara 43', 'Jasa IT', 'Jl. Semarang No. 129', 'PIC Vendor 43', '08281671393', 'Mebel Kantor', 3, 'Aktif', '2025-12-15', 'Catatan vendor ke-43', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(44, 'VDR-044', 'PT Vendor Nusantara 44', 'Spare Part', 'Jl. Yogyakarta No. 132', 'PIC Vendor 44', '08319940722', 'Genset dan Panel', 5, 'Aktif', '2025-11-20', 'Catatan vendor ke-44', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(45, 'VDR-045', 'CV Vendor Nusantara 45', 'Logistik', 'Jl. Surabaya No. 135', 'PIC Vendor 45', '08391480694', 'Seragam Karyawan', 4, 'Tidak Aktif', '2026-07-14', 'Catatan vendor ke-45', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(46, 'VDR-046', 'PT Vendor Nusantara 46', 'Maintenance', 'Jl. Medan No. 138', 'PIC Vendor 46', '08764968566', 'Kain Katun', 5, 'Aktif', '2026-07-13', 'Catatan vendor ke-46', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(47, 'VDR-047', 'CV Vendor Nusantara 47', 'Cleaning', 'Jl. Makassar No. 141', 'PIC Vendor 47', '08958509310', 'Kardus dan Label', 3, 'Aktif', '2025-12-26', 'Catatan vendor ke-47', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(48, 'VDR-048', 'PT Vendor Nusantara 48', 'Security', 'Jl. Palembang No. 144', 'PIC Vendor 48', '08848688081', 'Maintenance Sistem', 5, 'Aktif', '2025-09-12', 'Catatan vendor ke-48', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(49, 'VDR-049', 'CV Vendor Nusantara 49', 'Percetakan', 'Jl. Malang No. 147', 'PIC Vendor 49', '08539382730', 'Spare Part Kendaraan', 4, 'Aktif', '2026-01-12', 'Catatan vendor ke-49', '2026-08-10 23:55:32', '2026-08-10 23:55:32'),
(50, 'VDR-050', 'PT Vendor Nusantara 50', 'ATK', 'Jl. Solo No. 150', 'PIC Vendor 50', '08851995340', 'Pengiriman Barang', 4, 'Tidak Aktif', '2026-01-28', 'Catatan vendor ke-50', '2026-08-10 23:55:32', '2026-08-10 23:55:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendor_performances`
--

CREATE TABLE `vendor_performances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `total_order` bigint(20) NOT NULL DEFAULT 0,
  `ketepatan_waktu` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'persen',
  `kualitas_barang` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'skala 1-100',
  `komplain` bigint(20) NOT NULL DEFAULT 0,
  `penilaian_akhir` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'skala 1-100',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendor_performances`
--

INSERT INTO `vendor_performances` (`id`, `vendor`, `total_order`, `ketepatan_waktu`, `kualitas_barang`, `komplain`, `penilaian_akhir`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PT Maju Jaya', 48, 91.67, 88.50, 3, 89.20, 'Vendor terpercaya, pengiriman konsisten', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(2, 'CV Berkah Abadi', 35, 74.29, 80.00, 7, 76.50, 'Perlu peningkatan ketepatan waktu pengiriman', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(3, 'PT Sumber Makmur', 60, 95.00, 92.30, 2, 93.50, 'Performa terbaik, kualitas produk sangat baik', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(4, 'UD Sejahtera', 22, 63.64, 70.00, 9, 65.80, 'Banyak komplain, perlu evaluasi ulang kontrak', '2026-08-10 23:55:35', '2026-08-10 23:55:35'),
(5, 'PT Indo Supplier', 41, 82.93, 85.00, 5, 83.20, 'Performa stabil, harga kompetitif', '2026-08-10 23:55:35', '2026-08-10 23:55:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendor_pricelists`
--

CREATE TABLE `vendor_pricelists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `harga_per_unit` bigint(20) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT 0.00,
  `minimal_order` bigint(20) NOT NULL DEFAULT 1,
  `lead_time` bigint(20) NOT NULL DEFAULT 0 COMMENT 'dalam hari',
  `tanggal_berlaku` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendor_pricelists`
--

INSERT INTO `vendor_pricelists` (`id`, `vendor`, `kode_barang`, `nama_barang`, `harga_per_unit`, `satuan`, `diskon`, `minimal_order`, `lead_time`, `tanggal_berlaku`, `created_at`, `updated_at`) VALUES
(1, 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 827250, 'pcs', 5.49, 39, 9, '2026-08-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(2, 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 506947, 'liter', 16.35, 47, 30, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(3, 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 106930, 'unit', 18.15, 25, 29, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(4, 'UD Sejahtera', 'BRG-004', 'Filter Udara', 198084, 'set', 3.30, 34, 28, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(5, 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 419695, 'buah', 1.94, 11, 16, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(6, 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 962451, 'pcs', 3.93, 34, 28, '2026-08-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(7, 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 780889, 'liter', 15.60, 33, 11, '2026-08-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(8, 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 74473, 'unit', 12.83, 22, 29, '2026-05-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(9, 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 237489, 'set', 15.46, 2, 14, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(10, 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 1077184, 'buah', 7.61, 35, 24, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(11, 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 692115, 'pcs', 2.81, 39, 18, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(12, 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 75962, 'liter', 5.57, 7, 22, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(13, 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 684443, 'unit', 13.35, 41, 28, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(14, 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 578949, 'set', 0.59, 4, 22, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(15, 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 51764, 'buah', 1.28, 20, 28, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(16, 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 1099749, 'pcs', 12.03, 1, 27, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(17, 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 497040, 'liter', 9.86, 38, 19, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(18, 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 207391, 'unit', 3.84, 16, 14, '2026-08-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(19, 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 230825, 'set', 18.44, 26, 30, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(20, 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 1329791, 'buah', 17.49, 34, 26, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(21, 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 1446298, 'pcs', 19.70, 5, 24, '2026-08-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(22, 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 604836, 'liter', 9.54, 19, 22, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(23, 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 1271512, 'unit', 1.61, 31, 20, '2026-07-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(24, 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 577712, 'set', 15.33, 39, 17, '2026-06-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34'),
(25, 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 1234299, 'buah', 0.45, 3, 25, '2026-08-01', '2026-08-10 23:55:34', '2026-08-10 23:55:34');

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
  `status` enum('Pending','paid') NOT NULL DEFAULT 'Pending',
  `expired_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `virtual_accounts`
--

INSERT INTO `virtual_accounts` (`id`, `va_number`, `member_id`, `invoice_id`, `bukti_pembayaran`, `bank`, `expected_amount`, `paid_amount`, `status`, `expired_at`, `created_at`, `updated_at`) VALUES
(1, 'VA-71905759', 1, NULL, NULL, 'bca', 1500000.00, 0.00, 'Pending', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31'),
(2, 'VA-58401353', 1, NULL, NULL, 'bni', 2500000.00, 2500000.00, 'paid', NULL, '2026-08-10 23:55:31', '2026-08-10 23:55:31');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ads_integrations`
--
ALTER TABLE `ads_integrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ads_integrations_id_iklan_unique` (`id_iklan`);

--
-- Indeks untuk tabel `afiliasis`
--
ALTER TABLE `afiliasis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `afiliasis_id_program_unique` (`id_program`),
  ADD UNIQUE KEY `afiliasis_kode_referral_unique` (`kode_referral`);

--
-- Indeks untuk tabel `aging_aps`
--
ALTER TABLE `aging_aps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aging_aps_hutang_vendor_id_foreign` (`hutang_vendor_id`);

--
-- Indeks untuk tabel `aging_ars`
--
ALTER TABLE `aging_ars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aging_ars_member_id_foreign` (`member_id`),
  ADD KEY `aging_ars_invoice_id_foreign` (`invoice_id`);

--
-- Indeks untuk tabel `anggaran_proyek`
--
ALTER TABLE `anggaran_proyek`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `approval_workflows`
--
ALTER TABLE `approval_workflows`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `asset_dihapuskans`
--
ALTER TABLE `asset_dihapuskans`
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
-- Indeks untuk tabel `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_relation_type_relation_id_index` (`relation_type`,`relation_id`);

--
-- Indeks untuk tabel `audit_assets`
--
ALTER TABLE `audit_assets`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `crm_prospeks`
--
ALTER TABLE `crm_prospeks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_prospeks_kode_prospek_unique` (`kode_prospek`);

--
-- Indeks untuk tabel `cuti_izins`
--
ALTER TABLE `cuti_izins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cybersecurities`
--
ALTER TABLE `cybersecurities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `daftar_notaris`
--
ALTER TABLE `daftar_notaris`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `data_kontraks`
--
ALTER TABLE `data_kontraks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_kontraks_no_kontrak_unique` (`no_kontrak`),
  ADD UNIQUE KEY `data_kontraks_serial_number_unique` (`serial_number`),
  ADD KEY `data_kontraks_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `data_leasings`
--
ALTER TABLE `data_leasings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_leasings_data_kontrak_id_foreign` (`data_kontrak_id`);

--
-- Indeks untuk tabel `denda_rentals`
--
ALTER TABLE `denda_rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `denda_rentals_rental_id_foreign` (`rental_id`);

--
-- Indeks untuk tabel `departemens`
--
ALTER TABLE `departemens`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `deposit_customers`
--
ALTER TABLE `deposit_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposit_customers_rental_id_foreign` (`rental_id`);

--
-- Indeks untuk tabel `devops`
--
ALTER TABLE `devops`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dokumentasi_assets`
--
ALTER TABLE `dokumentasi_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dokumen_proyeks`
--
ALTER TABLE `dokumen_proyeks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dropshippings`
--
ALTER TABLE `dropshippings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `efakturs`
--
ALTER TABLE `efakturs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `efakturs_nomor_faktur_index` (`nomor_faktur`);

--
-- Indeks untuk tabel `email_domains`
--
ALTER TABLE `email_domains`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `hak_hukums`
--
ALTER TABLE `hak_hukums`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `helpdesk_supports`
--
ALTER TABLE `helpdesk_supports`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hrd_files`
--
ALTER TABLE `hrd_files`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hutang_vendors`
--
ALTER TABLE `hutang_vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `induk_assets`
--
ALTER TABLE `induk_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `induk_assets_kode_aset_unique` (`kode_aset`);

--
-- Indeks untuk tabel `induk_proyeks`
--
ALTER TABLE `induk_proyeks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `induk_proyeks_kode_unique` (`kode`);

--
-- Indeks untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_penawaran_id_foreign` (`penawaran_id`),
  ADD KEY `invoices_kontrak_id_foreign` (`kontrak_id`),
  ADD KEY `invoices_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `invoice_kendaraans`
--
ALTER TABLE `invoice_kendaraans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_kendaraans_invoice_id_kendaraan_id_unique` (`invoice_id`,`kendaraan_id`),
  ADD KEY `invoice_kendaraans_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `invoice_kontraks`
--
ALTER TABLE `invoice_kontraks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_kontraks_invoice_id_kontrak_id_unique` (`invoice_id`,`kontrak_id`),
  ADD KEY `invoice_kontraks_kontrak_id_foreign` (`kontrak_id`);

--
-- Indeks untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_payments_invoice_id_foreign` (`invoice_id`);

--
-- Indeks untuk tabel `invoice_penawarans`
--
ALTER TABLE `invoice_penawarans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_penawarans_invoice_id_penawaran_id_unique` (`invoice_id`,`penawaran_id`),
  ADD KEY `invoice_penawarans_penawaran_id_foreign` (`penawaran_id`);

--
-- Indeks untuk tabel `invoice_periodes`
--
ALTER TABLE `invoice_periodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_periodes_invoice_id_foreign` (`invoice_id`);

--
-- Indeks untuk tabel `invoice_remaks`
--
ALTER TABLE `invoice_remaks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_remaks_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_remaks_periode_id_foreign` (`periode_id`);

--
-- Indeks untuk tabel `inv_kontraks`
--
ALTER TABLE `inv_kontraks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inv_kontraks_penawaran_id_foreign` (`penawaran_id`);

--
-- Indeks untuk tabel `inv_penawarans`
--
ALTER TABLE `inv_penawarans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `inv_penawaran_items`
--
ALTER TABLE `inv_penawaran_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inv_penawaran_items_penawaran_id_foreign` (`penawaran_id`),
  ADD KEY `inv_penawaran_items_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `inv_summaries`
--
ALTER TABLE `inv_summaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inv_summaries_penawaran_id_foreign` (`penawaran_id`),
  ADD KEY `inv_summaries_kontrak_id_foreign` (`kontrak_id`),
  ADD KEY `inv_summaries_invoice_id_foreign` (`invoice_id`);

--
-- Indeks untuk tabel `itasset_management`
--
ALTER TABLE `itasset_management`
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
-- Indeks untuk tabel `kampanyes`
--
ALTER TABLE `kampanyes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kampanyes_id_kampanye_unique` (`id_kampanye`);

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kendaraan_user_id_foreign` (`user_id`),
  ADD KEY `kendaraan_jenis_id_foreign` (`jenis_id`),
  ADD KEY `kendaraan_member_id_foreign` (`member_id`);

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
-- Indeks untuk tabel `komisi_sales`
--
ALTER TABLE `komisi_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kontrak_aktifs`
--
ALTER TABLE `kontrak_aktifs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kpi_appraisals`
--
ALTER TABLE `kpi_appraisals`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `legal_documents_kode_unique` (`kode`);

--
-- Indeks untuk tabel `litigasis`
--
ALTER TABLE `litigasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `loyalties`
--
ALTER TABLE `loyalties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loyalties_id_program_unique` (`id_program`);

--
-- Indeks untuk tabel `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `members`
--
ALTER TABLE `members`
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
-- Indeks untuk tabel `network_monitorings`
--
ALTER TABLE `network_monitorings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `otomatisasis`
--
ALTER TABLE `otomatisasis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `otomatisasis_workflow_id_unique` (`workflow_id`);

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
-- Indeks untuk tabel `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pembelian_proyeks`
--
ALTER TABLE `pembelian_proyeks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pembelian_proyeks_pr_no_unique` (`pr_no`);

--
-- Indeks untuk tabel `pemeliharaan_assets`
--
ALTER TABLE `pemeliharaan_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `penanggung_jawabs`
--
ALTER TABLE `penanggung_jawabs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `penawarans`
--
ALTER TABLE `penawarans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penawarans_no_quotation_unique` (`no_quotation`);

--
-- Indeks untuk tabel `penyusutan_assets`
--
ALTER TABLE `penyusutan_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pergerakan_assets`
--
ALTER TABLE `pergerakan_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `perolehan_assets`
--
ALTER TABLE `perolehan_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `policy_compliances`
--
ALTER TABLE `policy_compliances`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `presensis`
--
ALTER TABLE `presensis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pricelist_diskons`
--
ALTER TABLE `pricelist_diskons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pricelist_diskons_id_harga_unique` (`id_harga`);

--
-- Indeks untuk tabel `procurementos`
--
ALTER TABLE `procurementos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `procurementos_workflow_id_unique` (`workflow_id`);

--
-- Indeks untuk tabel `project_costs`
--
ALTER TABLE `project_costs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `project_management`
--
ALTER TABLE `project_management`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `project_plannings`
--
ALTER TABLE `project_plannings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `project_risks`
--
ALTER TABLE `project_risks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `project_timelines`
--
ALTER TABLE `project_timelines`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `purchaseros`
--
ALTER TABLE `purchaseros`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rekonsiliasi_bank`
--
ALTER TABLE `rekonsiliasi_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `reminder_service`
--
ALTER TABLE `reminder_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reminder_service_kendaraan_id_foreign` (`kendaraan_id`);

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
-- Indeks untuk tabel `requestfor_quotations`
--
ALTER TABLE `requestfor_quotations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `resign_offboardings`
--
ALTER TABLE `resign_offboardings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `retur_penjualans`
--
ALTER TABLE `retur_penjualans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `retur_penjualans_no_retur_unique` (`no_retur`);

--
-- Indeks untuk tabel `review_legals`
--
ALTER TABLE `review_legals`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_orders_order_no_unique` (`order_no`);

--
-- Indeks untuk tabel `segmentasis`
--
ALTER TABLE `segmentasis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `segmentasis_segment_code_unique` (`segment_code`);

--
-- Indeks untuk tabel `sertifikasi_perizinans`
--
ALTER TABLE `sertifikasi_perizinans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `server_clouds`
--
ALTER TABLE `server_clouds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `service_asuransi`
--
ALTER TABLE `service_asuransi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_asuransi_kendaraan_id_foreign` (`kendaraan_id`),
  ADD KEY `service_asuransi_jenis_asuransi_id_foreign` (`jenis_asuransi_id`);

--
-- Indeks untuk tabel `service_detail`
--
ALTER TABLE `service_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_detail_kendaraan_id_foreign` (`kendaraan_id`),
  ADD KEY `service_detail_service_history_id_foreign` (`service_history_id`);

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
-- Indeks untuk tabel `shift_lemburs`
--
ALTER TABLE `shift_lemburs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `signature_dokumens`
--
ALTER TABLE `signature_dokumens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `signature_dokumens_document_id_unique` (`document_id`);

--
-- Indeks untuk tabel `skill_matrices`
--
ALTER TABLE `skill_matrices`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `software_licenses`
--
ALTER TABLE `software_licenses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sosmedps`
--
ALTER TABLE `sosmedps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sosmedps_id_kampanye_unique` (`id_kampanye`);

--
-- Indeks untuk tabel `stnk`
--
ALTER TABLE `stnk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stnk_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `stnk_histories`
--
ALTER TABLE `stnk_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stnk_histories_stnk_id_foreign` (`stnk_id`),
  ADD KEY `stnk_histories_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `struktur_organisasis`
--
ALTER TABLE `struktur_organisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `target_penjualans`
--
ALTER TABLE `target_penjualans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `trackingutms`
--
ALTER TABLE `trackingutms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trackingutms_kode_tracking_unique` (`kode_tracking`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `user_accesses`
--
ALTER TABLE `user_accesses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `vendoreos`
--
ALTER TABLE `vendoreos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `vendor_performances`
--
ALTER TABLE `vendor_performances`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `vendor_pricelists`
--
ALTER TABLE `vendor_pricelists`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT untuk tabel `ads_integrations`
--
ALTER TABLE `ads_integrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `afiliasis`
--
ALTER TABLE `afiliasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `aging_aps`
--
ALTER TABLE `aging_aps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `aging_ars`
--
ALTER TABLE `aging_ars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `anggaran_proyek`
--
ALTER TABLE `anggaran_proyek`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `approval_workflows`
--
ALTER TABLE `approval_workflows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `asset_dihapuskans`
--
ALTER TABLE `asset_dihapuskans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asuransi`
--
ALTER TABLE `asuransi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `asuransi_history`
--
ALTER TABLE `asuransi_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asuransi_kendaraan`
--
ALTER TABLE `asuransi_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `audit_assets`
--
ALTER TABLE `audit_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `bupot`
--
ALTER TABLE `bupot`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `crm_prospeks`
--
ALTER TABLE `crm_prospeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `cuti_izins`
--
ALTER TABLE `cuti_izins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `cybersecurities`
--
ALTER TABLE `cybersecurities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `daftar_notaris`
--
ALTER TABLE `daftar_notaris`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `data_kontraks`
--
ALTER TABLE `data_kontraks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `data_leasings`
--
ALTER TABLE `data_leasings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `denda_rentals`
--
ALTER TABLE `denda_rentals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `departemens`
--
ALTER TABLE `departemens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `deposit_customers`
--
ALTER TABLE `deposit_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `devops`
--
ALTER TABLE `devops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `dokumentasi_assets`
--
ALTER TABLE `dokumentasi_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumen_proyeks`
--
ALTER TABLE `dokumen_proyeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `dropshippings`
--
ALTER TABLE `dropshippings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `efakturs`
--
ALTER TABLE `efakturs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `email_domains`
--
ALTER TABLE `email_domains`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `gps_kendaraan`
--
ALTER TABLE `gps_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `gps_kendaraan_histories`
--
ALTER TABLE `gps_kendaraan_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hak_hukums`
--
ALTER TABLE `hak_hukums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `helpdesk_supports`
--
ALTER TABLE `helpdesk_supports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `hrd_files`
--
ALTER TABLE `hrd_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT untuk tabel `hutang_vendors`
--
ALTER TABLE `hutang_vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `induk_assets`
--
ALTER TABLE `induk_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `induk_proyeks`
--
ALTER TABLE `induk_proyeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `invoice_kendaraans`
--
ALTER TABLE `invoice_kendaraans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invoice_kontraks`
--
ALTER TABLE `invoice_kontraks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `invoice_penawarans`
--
ALTER TABLE `invoice_penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invoice_periodes`
--
ALTER TABLE `invoice_periodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `invoice_remaks`
--
ALTER TABLE `invoice_remaks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `inv_kontraks`
--
ALTER TABLE `inv_kontraks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `inv_penawarans`
--
ALTER TABLE `inv_penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `inv_penawaran_items`
--
ALTER TABLE `inv_penawaran_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `inv_summaries`
--
ALTER TABLE `inv_summaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `itasset_management`
--
ALTER TABLE `itasset_management`
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
-- AUTO_INCREMENT untuk tabel `kampanyes`
--
ALTER TABLE `kampanyes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `keuangans`
--
ALTER TABLE `keuangans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `kir`
--
ALTER TABLE `kir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `kir_history`
--
ALTER TABLE `kir_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `komisi_sales`
--
ALTER TABLE `komisi_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `kontrak_aktifs`
--
ALTER TABLE `kontrak_aktifs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kpi_appraisals`
--
ALTER TABLE `kpi_appraisals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT untuk tabel `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `litigasis`
--
ALTER TABLE `litigasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `loyalties`
--
ALTER TABLE `loyalties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `member`
--
ALTER TABLE `member`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `member_kendaraan`
--
ALTER TABLE `member_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT untuk tabel `network_monitorings`
--
ALTER TABLE `network_monitorings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `otomatisasis`
--
ALTER TABLE `otomatisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pajak_histories`
--
ALTER TABLE `pajak_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pajak_kendaraans`
--
ALTER TABLE `pajak_kendaraans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `pembelian_proyeks`
--
ALTER TABLE `pembelian_proyeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pemeliharaan_assets`
--
ALTER TABLE `pemeliharaan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penanggung_jawabs`
--
ALTER TABLE `penanggung_jawabs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penawarans`
--
ALTER TABLE `penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `penyusutan_assets`
--
ALTER TABLE `penyusutan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pergerakan_assets`
--
ALTER TABLE `pergerakan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `perolehan_assets`
--
ALTER TABLE `perolehan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `policy_compliances`
--
ALTER TABLE `policy_compliances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `presensis`
--
ALTER TABLE `presensis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT untuk tabel `pricelist_diskons`
--
ALTER TABLE `pricelist_diskons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `procurementos`
--
ALTER TABLE `procurementos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `project_costs`
--
ALTER TABLE `project_costs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `project_management`
--
ALTER TABLE `project_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `project_plannings`
--
ALTER TABLE `project_plannings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `project_risks`
--
ALTER TABLE `project_risks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `project_timelines`
--
ALTER TABLE `project_timelines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `purchaseros`
--
ALTER TABLE `purchaseros`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `rekonsiliasi_bank`
--
ALTER TABLE `rekonsiliasi_bank`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `reminder_service`
--
ALTER TABLE `reminder_service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `rental_biaya_tambahan`
--
ALTER TABLE `rental_biaya_tambahan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `requestfor_quotations`
--
ALTER TABLE `requestfor_quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `resign_offboardings`
--
ALTER TABLE `resign_offboardings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `retur_penjualans`
--
ALTER TABLE `retur_penjualans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `review_legals`
--
ALTER TABLE `review_legals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `segmentasis`
--
ALTER TABLE `segmentasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sertifikasi_perizinans`
--
ALTER TABLE `sertifikasi_perizinans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `server_clouds`
--
ALTER TABLE `server_clouds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `service`
--
ALTER TABLE `service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `service_asuransi`
--
ALTER TABLE `service_asuransi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `service_detail`
--
ALTER TABLE `service_detail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `service_history`
--
ALTER TABLE `service_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `shift_lemburs`
--
ALTER TABLE `shift_lemburs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `signature_dokumens`
--
ALTER TABLE `signature_dokumens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `skill_matrices`
--
ALTER TABLE `skill_matrices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `software_licenses`
--
ALTER TABLE `software_licenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `sosmedps`
--
ALTER TABLE `sosmedps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `stnk`
--
ALTER TABLE `stnk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `stnk_histories`
--
ALTER TABLE `stnk_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `struktur_organisasis`
--
ALTER TABLE `struktur_organisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `target_penjualans`
--
ALTER TABLE `target_penjualans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `trackingutms`
--
ALTER TABLE `trackingutms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user_accesses`
--
ALTER TABLE `user_accesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `vendoreos`
--
ALTER TABLE `vendoreos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `vendor_performances`
--
ALTER TABLE `vendor_performances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `vendor_pricelists`
--
ALTER TABLE `vendor_pricelists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `virtual_accounts`
--
ALTER TABLE `virtual_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `aging_aps`
--
ALTER TABLE `aging_aps`
  ADD CONSTRAINT `aging_aps_hutang_vendor_id_foreign` FOREIGN KEY (`hutang_vendor_id`) REFERENCES `hutang_vendors` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `aging_ars`
--
ALTER TABLE `aging_ars`
  ADD CONSTRAINT `aging_ars_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `aging_ars_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE;

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
-- Ketidakleluasaan untuk tabel `data_kontraks`
--
ALTER TABLE `data_kontraks`
  ADD CONSTRAINT `data_kontraks_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `data_leasings`
--
ALTER TABLE `data_leasings`
  ADD CONSTRAINT `data_leasings_data_kontrak_id_foreign` FOREIGN KEY (`data_kontrak_id`) REFERENCES `data_kontraks` (`id`) ON DELETE SET NULL;

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
-- Ketidakleluasaan untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_kontrak_id_foreign` FOREIGN KEY (`kontrak_id`) REFERENCES `inv_kontraks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `invoice_kendaraans`
--
ALTER TABLE `invoice_kendaraans`
  ADD CONSTRAINT `invoice_kendaraans_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_kendaraans_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `invoice_kontraks`
--
ALTER TABLE `invoice_kontraks`
  ADD CONSTRAINT `invoice_kontraks_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_kontraks_kontrak_id_foreign` FOREIGN KEY (`kontrak_id`) REFERENCES `inv_kontraks` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD CONSTRAINT `invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `invoice_penawarans`
--
ALTER TABLE `invoice_penawarans`
  ADD CONSTRAINT `invoice_penawarans_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_penawarans_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `invoice_periodes`
--
ALTER TABLE `invoice_periodes`
  ADD CONSTRAINT `invoice_periodes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `invoice_remaks`
--
ALTER TABLE `invoice_remaks`
  ADD CONSTRAINT `invoice_remaks_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_remaks_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `invoice_periodes` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `inv_kontraks`
--
ALTER TABLE `inv_kontraks`
  ADD CONSTRAINT `inv_kontraks_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `inv_penawaran_items`
--
ALTER TABLE `inv_penawaran_items`
  ADD CONSTRAINT `inv_penawaran_items_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inv_penawaran_items_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `inv_summaries`
--
ALTER TABLE `inv_summaries`
  ADD CONSTRAINT `inv_summaries_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inv_summaries_kontrak_id_foreign` FOREIGN KEY (`kontrak_id`) REFERENCES `inv_kontraks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inv_summaries_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `kendaraan_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
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
-- Ketidakleluasaan untuk tabel `reminder_service`
--
ALTER TABLE `reminder_service`
  ADD CONSTRAINT `reminder_service_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

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
-- Ketidakleluasaan untuk tabel `service_asuransi`
--
ALTER TABLE `service_asuransi`
  ADD CONSTRAINT `service_asuransi_jenis_asuransi_id_foreign` FOREIGN KEY (`jenis_asuransi_id`) REFERENCES `jenis_asuransi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_asuransi_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `service_detail`
--
ALTER TABLE `service_detail`
  ADD CONSTRAINT `service_detail_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_detail_service_history_id_foreign` FOREIGN KEY (`service_history_id`) REFERENCES `service_history` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `service_history`
--
ALTER TABLE `service_history`
  ADD CONSTRAINT `service_history_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stnk`
--
ALTER TABLE `stnk`
  ADD CONSTRAINT `stnk_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stnk_histories`
--
ALTER TABLE `stnk_histories`
  ADD CONSTRAINT `stnk_histories_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stnk_histories_stnk_id_foreign` FOREIGN KEY (`stnk_id`) REFERENCES `stnk` (`id`) ON DELETE CASCADE;

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
