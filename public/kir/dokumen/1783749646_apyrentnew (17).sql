-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Jul 2026 pada 20.07
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
  `klik` int(11) NOT NULL DEFAULT 0,
  `konversi` int(11) NOT NULL DEFAULT 0,
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
(1, 'ADS001', 'Google Ads - Rental Mobil Jakarta', 'Google Ads', '2026-07-01', 500000.00, 350, 28, 15000000.00, 70000000.00, '367%', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'ADS002', 'Facebook Ads - Awareness Campaign', 'Meta Ads', '2026-07-05', 300000.00, 520, 35, 9000000.00, 52500000.00, '483%', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
(1, 'AFI001', 'Referral Teman', 'REF-APY001', 50000.00, 'Rp 75.000 kredit', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'AFI002', 'Corporate Partner', 'REF-CORP001', 100000.00, 'Komisi 5%', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
  `total` bigint(20) NOT NULL,
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
(1, 'Pembangunan Sistem Rental', 'Development', 15000000.00, 6000000.00, 9000000.00, 40.00, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(2, 'Server & Hosting', 'Infrastructure', 5000000.00, 2500000.00, 2500000.00, 50.00, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(3, 'Pembelian GPS', 'Operasional', 10000000.00, 7500000.00, 2500000.00, 75.00, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(4, 'Promosi Rental', 'Marketing', 7000000.00, 3000000.00, 4000000.00, 42.86, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(5, 'Service Kendaraan', 'Maintenance', 12000000.00, 4500000.00, 7500000.00, 37.50, '2026-07-09 17:56:54', '2026-07-09 17:56:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `approval_workflows`
--

CREATE TABLE `approval_workflows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_po` varchar(255) NOT NULL,
  `urutan_approval` int(11) NOT NULL,
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
(1, 'PO-001', 1, 'Supervisor Pembelian', 'Budi Santoso', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'PO-001', 2, 'Manager Operasional', 'Rina Wulandari', '2026-05-17', 'Approved', 'Review urutan 2 untuk PO-001', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'PO-002', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-05-31', 'Rejected', 'Review urutan 1 untuk PO-002', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'PO-002', 2, 'Manager Operasional', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'PO-003', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-06-05', 'Approved', 'Review urutan 1 untuk PO-003', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(6, 'PO-003', 2, 'Manager Operasional', 'Budi Santoso', '2026-07-05', 'Rejected', 'Review urutan 2 untuk PO-003', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(7, 'PO-004', 1, 'Supervisor Pembelian', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(8, 'PO-004', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-05-12', 'Approved', 'Review urutan 2 untuk PO-004', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(9, 'PO-005', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-06-27', 'Rejected', 'Review urutan 1 untuk PO-005', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(10, 'PO-005', 2, 'Manager Operasional', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(11, 'PO-006', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-05-22', 'Approved', 'Review urutan 1 untuk PO-006', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(12, 'PO-006', 2, 'Manager Operasional', 'Rina Wulandari', '2026-05-26', 'Rejected', 'Review urutan 2 untuk PO-006', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(13, 'PO-007', 1, 'Supervisor Pembelian', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(14, 'PO-007', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-06-21', 'Approved', 'Review urutan 2 untuk PO-007', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(15, 'PO-008', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-05-20', 'Rejected', 'Review urutan 1 untuk PO-008', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(16, 'PO-008', 2, 'Manager Operasional', 'Budi Santoso', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(17, 'PO-009', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-05-14', 'Approved', 'Review urutan 1 untuk PO-009', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(18, 'PO-009', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-06-19', 'Rejected', 'Review urutan 2 untuk PO-009', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(19, 'PO-010', 1, 'Supervisor Pembelian', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(20, 'PO-010', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-05-23', 'Approved', 'Review urutan 2 untuk PO-010', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(21, 'PO-011', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-05-15', 'Rejected', 'Review urutan 1 untuk PO-011', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(22, 'PO-011', 2, 'Manager Operasional', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(23, 'PO-012', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-06-17', 'Approved', 'Review urutan 1 untuk PO-012', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(24, 'PO-012', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-05-14', 'Rejected', 'Review urutan 2 untuk PO-012', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(25, 'PO-013', 1, 'Supervisor Pembelian', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(26, 'PO-013', 2, 'Manager Operasional', 'Budi Santoso', '2026-05-27', 'Approved', 'Review urutan 2 untuk PO-013', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(27, 'PO-014', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-06-28', 'Rejected', 'Review urutan 1 untuk PO-014', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(28, 'PO-014', 2, 'Manager Operasional', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(29, 'PO-015', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-06-08', 'Approved', 'Review urutan 1 untuk PO-015', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(30, 'PO-015', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-06-26', 'Rejected', 'Review urutan 2 untuk PO-015', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
(1, 1, 'BCA Insurance', 'Jl. Sudirman No. 10 Jakarta', 'Andi Saputra', '081234567890', 'Bengkel Maju Motor', '082233445566', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(2, 1, 'Adira Insurance', 'Jl. Malioboro No. 20 Yogyakarta', 'Budi Hartono', '081298765432', 'Bengkel Jaya Abadi', '085566778899', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(3, 1, 'ACA Insurance', 'Jl. Pemuda No. 12 Semarang', 'Siti Rahma', '087712345678', 'Bengkel Berkah Mobil', '081122334455', '2026-07-09 17:56:54', '2026-07-09 17:56:54');

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
(1, 1, 1, 1, 'expired', '2026-01-10', '2026-04-10', 3, 23500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(2, 2, 2, 2, 'aktif', '2026-03-10', '2026-09-10', 6, 10000000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(3, 3, 3, 3, 'aktif', '2025-08-10', '2026-08-10', 12, 21500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(4, 4, 1, 1, 'aktif', '2025-12-10', '2027-12-10', 24, 10000000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(5, 5, 2, 2, 'expired', '2025-04-10', '2025-07-10', 3, 3500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(6, 6, 3, 3, 'expired', '2025-04-10', '2025-10-10', 6, 17500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(7, 7, 1, 1, 'expired', '2024-11-10', '2025-11-10', 12, 8000000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(8, 8, 2, 2, 'aktif', '2025-09-10', '2027-09-10', 24, 18500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(9, 9, 3, 3, 'expired', '2025-10-10', '2026-01-10', 3, 5500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(10, 10, 1, 1, 'expired', '2026-01-10', '2026-07-10', 6, 23500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(11, 11, 2, 2, 'expired', '2025-06-10', '2026-06-10', 12, 22500000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(12, 12, 3, 3, 'aktif', '2025-10-10', '2027-10-10', 24, 25000000.00, NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(13, 13, 1, 1, 'expired', '2025-08-10', '2025-11-10', 3, 11500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(14, 14, 2, 2, 'expired', '2025-12-10', '2026-06-10', 6, 14500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(15, 15, 3, 3, 'aktif', '2025-09-10', '2026-09-10', 12, 14000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(16, 16, 1, 1, 'aktif', '2024-12-10', '2026-12-10', 24, 8500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(17, 17, 2, 2, 'expired', '2025-05-10', '2025-08-10', 3, 13500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(18, 18, 3, 3, 'aktif', '2026-02-10', '2026-08-10', 6, 13500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(19, 19, 1, 1, 'aktif', '2025-10-10', '2026-10-10', 12, 14500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(20, 20, 2, 2, 'aktif', '2026-06-10', '2028-06-10', 24, 22000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(21, 21, 3, 3, 'expired', '2025-01-10', '2025-04-10', 3, 2500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(22, 22, 1, 1, 'expired', '2025-01-10', '2025-07-10', 6, 11500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(23, 23, 2, 2, 'expired', '2025-06-10', '2026-06-10', 12, 8500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(24, 24, 3, 3, 'aktif', '2026-07-10', '2028-07-10', 24, 12000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(25, 25, 1, 1, 'expired', '2025-02-10', '2025-05-10', 3, 9500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(26, 26, 2, 2, 'expired', '2025-01-10', '2025-07-10', 6, 3500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(27, 27, 3, 3, 'expired', '2024-11-10', '2025-11-10', 12, 6500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(28, 28, 1, 1, 'aktif', '2026-06-10', '2028-06-10', 24, 13500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(29, 29, 2, 2, 'aktif', '2026-05-10', '2026-08-10', 3, 20000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(30, 30, 3, 3, 'expired', '2025-06-10', '2025-12-10', 6, 19500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(31, 31, 1, 1, 'aktif', '2026-04-10', '2027-04-10', 12, 11500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(32, 32, 2, 2, 'aktif', '2025-09-10', '2027-09-10', 24, 12500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(33, 33, 3, 3, 'expired', '2025-07-10', '2025-10-10', 3, 18000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(34, 34, 1, 1, 'expired', '2025-10-10', '2026-04-10', 6, 3000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(35, 35, 2, 2, 'aktif', '2025-10-10', '2026-10-10', 12, 16500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(36, 36, 3, 3, 'aktif', '2025-08-10', '2027-08-10', 24, 7000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(37, 37, 1, 1, 'expired', '2025-06-10', '2025-09-10', 3, 21000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(38, 38, 2, 2, 'expired', '2025-02-10', '2025-08-10', 6, 17000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(39, 39, 3, 3, 'aktif', '2026-01-10', '2027-01-10', 12, 10000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(40, 40, 1, 1, 'aktif', '2025-06-10', '2027-06-10', 24, 19500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(41, 41, 2, 2, 'expired', '2025-09-10', '2025-12-10', 3, 18000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(42, 42, 3, 3, 'aktif', '2026-05-10', '2026-11-10', 6, 8500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(43, 43, 1, 1, 'aktif', '2026-03-10', '2027-03-10', 12, 4000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(44, 44, 2, 2, 'aktif', '2026-02-10', '2028-02-10', 24, 17500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(45, 45, 3, 3, 'expired', '2025-11-10', '2026-02-10', 3, 13000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(46, 46, 1, 1, 'expired', '2025-05-10', '2025-11-10', 6, 24500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(47, 47, 2, 2, 'aktif', '2026-03-10', '2027-03-10', 12, 14500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(48, 48, 3, 3, 'aktif', '2025-04-10', '2027-04-10', 24, 17000000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(49, 49, 1, 1, 'expired', '2024-12-10', '2025-03-10', 3, 21500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(50, 50, 2, 2, 'expired', '2025-06-10', '2025-12-10', 6, 15500000.00, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55');

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
  `debit` bigint(20) NOT NULL DEFAULT 0,
  `kredit` bigint(20) NOT NULL DEFAULT 0,
  `saldo` bigint(20) NOT NULL DEFAULT 0,
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
(1, 'JRNL-001', 'Pemasukan Rental Harian', 'Pendapatan', '2026-05-15', 1500000, 0, 1500000, 'rental', 'Pembayaran rental harian dari customer', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(2, 'JRNL-002', 'Pemasukan Rental Mingguan', 'Pendapatan', '2026-04-26', 3500000, 0, 5000000, 'rental', 'Pembayaran rental mingguan', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(3, 'JRNL-003', 'Penerimaan DP Rental', 'Pendapatan', '2026-06-30', 1000000, 0, 6000000, 'rental', 'DP rental kendaraan', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(4, 'JRNL-004', 'Pelunasan Rental', 'Pendapatan', '2026-01-17', 2000000, 0, 8000000, 'rental', 'Pelunasan biaya rental', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(5, 'JRNL-005', 'Penerimaan Denda Keterlambatan', 'Pendapatan', '2026-03-03', 250000, 0, 8250000, 'denda', 'Denda pengembalian terlambat', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(6, 'JRNL-006', 'Penerimaan Deposit Customer', 'Pendapatan', '2026-04-15', 500000, 0, 8750000, 'deposit', 'Deposit jaminan kendaraan', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(7, 'JRNL-007', 'Pendapatan Biaya Tambahan', 'Pendapatan', '2026-05-23', 200000, 0, 8950000, 'rental', 'Biaya supir tambahan', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(8, 'JRNL-008', 'Penerimaan Sewa Jangka Panjang', 'Pendapatan', '2026-03-20', 15000000, 0, 23950000, 'rental', 'Kontrak sewa bulanan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(9, 'JRNL-009', 'Pendapatan Lain-lain', 'Pendapatan', '2026-04-22', 350000, 0, 24300000, 'lain', 'Pendapatan di luar operasional utama', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(10, 'JRNL-010', 'Penerimaan Invoice Kontrak', 'Pendapatan', '2026-06-29', 8000000, 0, 32300000, 'invoice', 'Pembayaran invoice kontrak korporat', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(11, 'JRNL-011', 'Biaya Servis Berkala', 'Beban', '2026-03-22', 0, 500000, 31800000, 'service', 'Servis rutin kendaraan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(12, 'JRNL-012', 'Biaya Ganti Oli', 'Beban', '2026-03-22', 0, 150000, 31650000, 'service', 'Penggantian oli mesin', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(13, 'JRNL-013', 'Pembayaran Pajak Kendaraan', 'Beban', '2026-01-29', 0, 3500000, 28150000, 'pajak', 'Pajak tahunan kendaraan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(14, 'JRNL-014', 'Premi Asuransi Kendaraan', 'Beban', '2026-04-03', 0, 5000000, 23150000, 'asuransi', 'Pembayaran premi asuransi', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(15, 'JRNL-015', 'Biaya Sewa GPS', 'Beban', '2026-05-11', 0, 300000, 22850000, 'gps', 'Biaya langganan GPS tracker', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(16, 'JRNL-016', 'Biaya Bahan Bakar', 'Beban', '2026-03-08', 0, 800000, 22050000, 'operasional', 'Pembelian bahan bakar kendaraan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(17, 'JRNL-017', 'Biaya KIR Kendaraan', 'Beban', '2026-03-12', 0, 200000, 21850000, 'kir', 'Biaya uji KIR kendaraan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(18, 'JRNL-018', 'Biaya Gaji Karyawan', 'Beban', '2026-05-12', 0, 5000000, 16850000, 'gaji', 'Gaji karyawan bulan ini', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(19, 'JRNL-019', 'Biaya Pembelian Spare Part', 'Beban', '2026-01-18', 0, 1200000, 15650000, 'service', 'Pembelian ban dan kampas rem', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(20, 'JRNL-020', 'Biaya Listrik dan Air', 'Beban', '2026-06-22', 0, 450000, 15200000, 'operasional', 'Tagihan utilitas kantor', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(21, 'JRNL-021', 'Pembelian Kendaraan Baru', 'Aktiva', '2026-01-18', 250000000, 0, 265200000, 'pembelian', 'Penambahan aset kendaraan baru', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(22, 'JRNL-022', 'Kas di Tangan', 'Aktiva', '2026-03-18', 10000000, 0, 275200000, 'kas', 'Saldo kas operasional', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(23, 'JRNL-023', 'Kas di Bank', 'Aktiva', '2026-02-19', 50000000, 0, 325200000, 'kas', 'Saldo rekening bank perusahaan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(24, 'JRNL-024', 'Piutang Rental', 'Aktiva', '2026-05-29', 7500000, 0, 332700000, 'rental', 'Tagihan belum dibayar customer', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(25, 'JRNL-025', 'Perlengkapan Kantor', 'Aktiva', '2026-04-10', 2500000, 0, 335200000, 'operasional', 'Inventaris perlengkapan kantor', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(26, 'JRNL-026', 'Peralatan Workshop', 'Aktiva', '2026-03-17', 15000000, 0, 350200000, 'service', 'Alat bengkel dan servis kendaraan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(27, 'JRNL-027', 'Deposit GPS Provider', 'Aktiva', '2026-04-02', 1000000, 0, 351200000, 'gps', 'Deposit ke penyedia GPS', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(28, 'JRNL-028', 'Persediaan Sparepart', 'Aktiva', '2026-06-07', 3000000, 0, 354200000, 'service', 'Stok sparepart di gudang', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(29, 'JRNL-029', 'Gedung Kantor', 'Aktiva', '2026-03-09', 500000000, 0, 854200000, 'aset', 'Nilai gedung kantor operasional', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(30, 'JRNL-030', 'Kendaraan Operasional', 'Aktiva', '2026-03-11', 180000000, 0, 1034200000, 'aset', 'Nilai armada kendaraan sewa', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(31, 'JRNL-031', 'Modal Awal Pemilik', 'Modal', '2026-03-13', 0, 500000000, 534200000, 'modal', 'Setoran modal awal perusahaan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(32, 'JRNL-032', 'Tambahan Modal Investasi', 'Modal', '2026-02-15', 0, 100000000, 434200000, 'modal', 'Investasi tambahan dari pemilik', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(33, 'JRNL-033', 'Laba Ditahan Tahun Lalu', 'Modal', '2026-06-09', 0, 75000000, 359200000, 'modal', 'Akumulasi laba yang tidak dibagikan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(34, 'JRNL-034', 'Dividen Dibayarkan', 'Modal', '2026-03-04', 25000000, 0, 384200000, 'modal', 'Pembagian dividen kepada pemilik', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(35, 'JRNL-035', 'Laba Bersih Periode Berjalan', 'Modal', '2026-07-06', 0, 45000000, 339200000, 'modal', 'Laba bersih periode ini', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(36, 'JRNL-036', 'Cadangan Umum', 'Modal', '2026-05-12', 0, 10000000, 329200000, 'modal', 'Cadangan dana untuk ekspansi', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(37, 'JRNL-037', 'Prive Pemilik', 'Modal', '2026-03-19', 5000000, 0, 334200000, 'modal', 'Pengambilan pribadi pemilik', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(38, 'JRNL-038', 'Revaluasi Aset Kendaraan', 'Modal', '2026-06-15', 0, 20000000, 314200000, 'aset', 'Kenaikan nilai aset kendaraan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(39, 'JRNL-039', 'Modal Kerja Tambahan', 'Modal', '2026-02-25', 0, 30000000, 284200000, 'modal', 'Penambahan modal kerja operasional', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(40, 'JRNL-040', 'Saldo Modal Berjalan', 'Modal', '2026-03-29', 0, 15000000, 269200000, 'modal', 'Saldo modal per periode ini', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(41, 'JRNL-041', 'Hutang Bank Jangka Panjang', 'Kewajiban', '2026-04-24', 0, 200000000, 69200000, 'hutang', 'Pinjaman bank untuk pembelian kendaraan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(42, 'JRNL-042', 'Hutang Leasing Kendaraan', 'Kewajiban', '2026-04-07', 0, 120000000, -50800000, 'hutang', 'Cicilan leasing kendaraan baru', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(43, 'JRNL-043', 'Hutang Vendor Sparepart', 'Kewajiban', '2026-02-13', 0, 8000000, -58800000, 'hutang', 'Tagihan belum dibayar ke vendor', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(44, 'JRNL-044', 'Hutang Pajak', 'Kewajiban', '2026-02-22', 0, 5000000, -63800000, 'pajak', 'Kewajiban pajak yang belum dibayar', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(45, 'JRNL-045', 'Hutang Gaji Karyawan', 'Kewajiban', '2026-06-24', 0, 15000000, -78800000, 'gaji', 'Gaji bulan lalu yang belum dibayar', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(46, 'JRNL-046', 'Hutang GPS Provider', 'Kewajiban', '2026-02-03', 0, 900000, -79700000, 'gps', 'Tagihan langganan GPS yang tertunda', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(47, 'JRNL-047', 'Hutang Asuransi', 'Kewajiban', '2026-01-11', 0, 3000000, -82700000, 'asuransi', 'Premi asuransi yang belum dibayar', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(48, 'JRNL-048', 'Deposit Customer Diterima', 'Kewajiban', '2026-07-07', 0, 4500000, -87200000, 'deposit', 'Deposit yang harus dikembalikan', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(49, 'JRNL-049', 'Hutang Listrik dan Utilitas', 'Kewajiban', '2026-05-02', 0, 750000, -87950000, 'operasional', 'Tagihan utilitas yang belum dibayar', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(50, 'JRNL-050', 'Hutang Jangka Pendek Lainnya', 'Kewajiban', '2026-07-01', 0, 2000000, -89950000, 'hutang', 'Kewajiban jangka pendek lain-lain', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57');

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
(1, 'BUPOT-001', '2026-06-30', 'PPh21', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '09.876.543.2-109.000', 'Budi Santoso', 5000000.00, 0.05, 250000.00, 'Approve', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(2, 'BUPOT-002', '2026-07-02', 'PPh23', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '08.765.432.1-000.000', 'CV Sinar Abadi', 3000000.00, 0.02, 60000.00, 'Approve', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(3, 'BUPOT-003', '2026-07-05', 'PPh26', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '07.654.321.0-999.000', 'UD Jaya Motor', 10000000.00, 0.10, 1000000.00, 'Draft', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56');

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
(1, 'PRO-001', 'Budi Santoso', 'PT Maju Bersama', NULL, '0812-1111-1111', 'Prospek', NULL, 'Aktif', 'Andi', '2026-01-10', 'Butuh armada 5 unit', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(2, 'PRO-002', 'Siti Rahayu', 'CV Karya Indah', NULL, '0813-2222-2222', 'Negosiasi', NULL, 'Aktif', 'Budi', '2026-02-05', 'Diskusi harga sudah selesai', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(3, 'PRO-003', 'Ahmad Fauzi', 'PT Sejahtera Abadi', NULL, '0814-3333-3333', 'Closing', NULL, 'Aktif', 'Cici', '2026-02-20', 'Kontrak siap ditandatangani', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(4, 'PRO-004', 'Dewi Lestari', 'PT Global Trans', NULL, '0815-4444-4444', 'Prospek', NULL, 'Aktif', 'Andi', '2026-03-01', 'Masih dalam penjajakan', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(5, 'PRO-005', 'Rudi Hartono', 'CV Jaya Mandiri', NULL, '0816-5555-5555', 'Negosiasi', NULL, 'Aktif', 'Dani', '2026-03-15', 'Negosiasi tenor kontrak', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(6, 'PRO-006', 'Lia Permata', 'PT Nusantara Raya', NULL, '0817-6666-6666', 'Closing', NULL, 'Aktif', 'Budi', '2026-04-02', 'Deal 3 unit minibus', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(7, 'PRO-007', 'Hendra Wijaya', 'PT Sinar Harapan', NULL, '0818-7777-7777', 'Prospek', NULL, 'Tidak Aktif', 'Cici', '2026-04-10', 'Tidak merespon lagi', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(8, 'PRO-008', 'Maya Anggraini', 'CV Mitra Logistik', NULL, '0819-8888-8888', 'Negosiasi', NULL, 'Aktif', 'Andi', '2026-05-01', 'Menunggu approval direksi', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(9, 'PRO-009', 'Fajar Nugroho', 'PT Berlian Trans', NULL, '0821-9999-9999', 'Closing', NULL, 'Aktif', 'Dani', '2026-05-20', 'Siap kontrak', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(10, 'PRO-010', 'Indah Kusuma', 'PT Prima Raya', NULL, '0822-1010-1010', 'Prospek', NULL, 'Aktif', 'Budi', '2026-06-05', 'Prospek baru dari referral', '2026-07-09 17:56:57', '2026-07-09 17:56:57');

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
  `lama_hari` int(11) NOT NULL,
  `alasan` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cuti_izins`
--

INSERT INTO `cuti_izins` (`id`, `nama_pegawai`, `jenis_cuti_izin`, `tanggal_mulai`, `tanggal_selesai`, `lama_hari`, `alasan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Rini Apriani', 'Cuti Tahunan', '2026-06-07', '2026-06-15', 9, 'Keperluan keluarga', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(2, 'Eko Prasetyo', 'Cuti Sakit', '2026-03-22', '2026-04-01', 11, 'Pemulihan kesehatan', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(3, 'Rizky Fadillah', 'Cuti Melahirkan', '2026-07-01', '2026-07-09', 9, 'Acara pernikahan', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(4, 'Yusuf Hidayat', 'Izin Pribadi', '2026-05-14', '2026-05-24', 11, 'Mengurus administrasi', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(5, 'Wahyu Nugroho', 'Cuti Bersama', '2026-04-25', '2026-05-08', 14, 'Liburan keluarga', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(6, 'Fitri Handayani', 'Cuti Tahunan', '2026-04-14', '2026-04-19', 6, 'Cuti bersama hari raya', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(7, 'Teguh Santosa', 'Cuti Sakit', '2026-04-06', '2026-04-07', 2, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(8, 'Arif Budiman', 'Cuti Melahirkan', '2026-07-03', '2026-07-11', 9, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(9, 'Dewi Kusuma', 'Izin Pribadi', '2026-05-21', '2026-06-03', 14, 'Keperluan keluarga', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(10, 'Linda Permata', 'Cuti Bersama', '2026-01-24', '2026-01-28', 5, 'Pemulihan kesehatan', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(11, 'Hendra Gunawan', 'Cuti Tahunan', '2026-06-14', '2026-06-17', 4, 'Acara pernikahan', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(12, 'Dody Kurniawan', 'Cuti Sakit', '2026-05-31', '2026-06-02', 3, 'Mengurus administrasi', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(13, 'Rini Apriani', 'Cuti Melahirkan', '2026-07-05', '2026-07-05', 1, 'Liburan keluarga', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(14, 'Eko Prasetyo', 'Izin Pribadi', '2026-03-27', '2026-03-30', 4, 'Cuti bersama hari raya', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(15, 'Rizky Fadillah', 'Cuti Bersama', '2026-04-29', '2026-05-04', 6, 'Rawat inap di rumah sakit', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(16, 'Yusuf Hidayat', 'Cuti Tahunan', '2026-04-25', '2026-04-27', 3, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(17, 'Wahyu Nugroho', 'Cuti Sakit', '2026-04-29', '2026-05-08', 10, 'Keperluan keluarga', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(18, 'Fitri Handayani', 'Cuti Melahirkan', '2026-04-26', '2026-05-05', 10, 'Pemulihan kesehatan', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(19, 'Teguh Santosa', 'Izin Pribadi', '2026-04-14', '2026-04-21', 8, 'Acara pernikahan', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(20, 'Arif Budiman', 'Cuti Bersama', '2026-05-09', '2026-05-13', 5, 'Mengurus administrasi', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(21, 'Dewi Kusuma', 'Cuti Tahunan', '2026-02-07', '2026-02-15', 9, 'Liburan keluarga', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(22, 'Linda Permata', 'Cuti Sakit', '2026-03-16', '2026-03-29', 14, 'Cuti bersama hari raya', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(23, 'Hendra Gunawan', 'Cuti Melahirkan', '2026-02-19', '2026-03-03', 13, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(24, 'Dody Kurniawan', 'Izin Pribadi', '2026-05-17', '2026-05-18', 2, 'Keperluan mendesak pribadi', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(25, 'Rini Apriani', 'Cuti Bersama', '2026-03-13', '2026-03-17', 5, 'Keperluan keluarga', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(26, 'Eko Prasetyo', 'Cuti Tahunan', '2026-02-13', '2026-02-25', 13, 'Pemulihan kesehatan', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(27, 'Rizky Fadillah', 'Cuti Sakit', '2026-05-27', '2026-06-09', 14, 'Acara pernikahan', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(28, 'Yusuf Hidayat', 'Cuti Melahirkan', '2026-03-14', '2026-03-17', 4, 'Mengurus administrasi', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(29, 'Wahyu Nugroho', 'Izin Pribadi', '2026-02-11', '2026-02-17', 7, 'Liburan keluarga', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(30, 'Fitri Handayani', 'Cuti Bersama', '2026-06-21', '2026-06-29', 9, 'Cuti bersama hari raya', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(31, 'Teguh Santosa', 'Cuti Tahunan', '2026-05-25', '2026-05-31', 7, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(32, 'Arif Budiman', 'Cuti Sakit', '2026-03-17', '2026-03-17', 1, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(33, 'Dewi Kusuma', 'Cuti Melahirkan', '2026-06-10', '2026-06-18', 9, 'Keperluan keluarga', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(34, 'Linda Permata', 'Izin Pribadi', '2026-06-07', '2026-06-07', 1, 'Pemulihan kesehatan', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(35, 'Hendra Gunawan', 'Cuti Bersama', '2026-04-09', '2026-04-18', 10, 'Acara pernikahan', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(36, 'Dody Kurniawan', 'Cuti Tahunan', '2026-04-19', '2026-04-19', 1, 'Mengurus administrasi', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(37, 'Rini Apriani', 'Cuti Sakit', '2026-04-18', '2026-04-26', 9, 'Liburan keluarga', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(38, 'Eko Prasetyo', 'Cuti Melahirkan', '2026-05-08', '2026-05-13', 6, 'Cuti bersama hari raya', 'Disetujui', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(39, 'Rizky Fadillah', 'Izin Pribadi', '2026-03-13', '2026-03-14', 2, 'Rawat inap di rumah sakit', 'Pending', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(40, 'Yusuf Hidayat', 'Cuti Bersama', '2026-03-20', '2026-03-26', 7, 'Keperluan mendesak pribadi', 'Ditolak', '2026-07-09 17:57:06', '2026-07-09 17:57:06');

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
(1, '2025-01-05', 'Web Application', 'SQL Injection pada form login admin panel', 'Critical', 'Input sanitasi dan prepared statement diterapkan', 'Resolved', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, '2025-01-20', 'Jaringan Internal', 'Port 23 (Telnet) masih terbuka di beberapa switch', 'High', 'Disable Telnet dan aktifkan SSH pada semua perangkat jaringan', 'In Progress', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, '2025-02-10', 'Email Server', 'Tidak ada SPF dan DMARC record, rentan email spoofing', 'Medium', 'Konfigurasi SPF, DKIM, dan DMARC pada DNS', 'Open', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, '2025-02-25', 'Endpoint Security', '12 komputer belum update antivirus selama 3 bulan', 'Low', 'Update antivirus terpusat via console management', 'Resolved', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, '2025-03-01', 'Database Server', 'Akun root database dapat diakses dari remote tanpa restriksi IP', 'Critical', 'Batasi akses root hanya dari localhost, buat user terbatas', 'Open', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
  `jumlah_posisi` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status_aktif` enum('Aktif','Non-Aktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `departemens`
--

INSERT INTO `departemens` (`id`, `nama_departemen`, `kepala_departemen`, `tanggal_dibentuk`, `jumlah_posisi`, `keterangan`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Direksi', 'Budi Santoso', '2018-01-02', 3, 'Pimpinan tertinggi perusahaan', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'HRD', 'Dewi Kusuma', '2018-06-01', 8, 'Mengelola sumber daya manusia', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'IT', 'Hendra Gunawan', '2019-01-15', 6, 'Pengembangan dan pemeliharaan sistem teknologi', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Finance', 'Linda Permata', '2018-06-01', 10, 'Pengelolaan keuangan dan akuntansi', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Operasional', 'Dody Kurniawan', '2019-03-01', 15, 'Pengelolaan operasional lapangan', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 'Marketing', 'Sari Dewanti', '2020-02-01', 7, 'Pemasaran dan promosi produk', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 'Sales', 'Benny Kusuma', '2020-04-01', 12, 'Penjualan dan hubungan pelanggan', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 'Legal', 'Putri Wulandari', '2021-01-01', 4, 'Urusan hukum dan kontrak perusahaan', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 'Procurement', 'Bambang Irawan', '2021-06-01', 5, 'Pengadaan barang dan jasa', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(10, 'Maintenance', 'Suryono Hadi', '2019-07-01', 8, 'Pemeliharaan aset dan kendaraan', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(11, 'R&D', 'Indra Lesmana', '2022-01-01', 4, 'Riset dan pengembangan produk', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(12, 'Customer Service', 'Maya Anggraini', '2020-09-01', 6, 'Layanan pelanggan', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'API Backend ERP', 'GitHub Actions', 'Ya', 'Setiap push ke branch main', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Frontend Dashboard', 'GitLab CI', 'Ya', 'Setiap merge request approved', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Mobile App Driver', 'Bitrise', 'Tidak', 'Manual oleh tim mobile', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Laporan Keuangan', 'Jenkins', 'Ya', 'Setiap hari pukul 02.00 WIB', 'Nonaktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Website Company Profile', 'GitHub Actions', 'Ya', 'Setiap push ke branch production', 'Aktif', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'PRJ001', 'RAB Renovasi Pool', 'XLSX', '-', 'Valid', '2025-12-28', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'PRJ001', 'Gambar Desain Konstruksi', 'PDF', '-', 'Valid', '2025-12-30', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'PRJ001', 'Kontrak Kontraktor', 'PDF', '-', 'Valid', '2026-01-02', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'PRJ002', 'Spesifikasi Teknis Bus', 'PDF', '-', 'Valid', '2026-01-20', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'PRJ002', 'Purchase Order Bus', 'PDF', '-', 'Draft', '2026-02-05', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(6, 'PRJ003', 'Proposal GPS Monitoring', 'PDF', '-', 'Valid', '2026-01-10', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(7, 'PRJ003', 'Kontrak Vendor GPS', 'PDF', '-', 'Valid', '2026-01-14', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(8, 'PRJ005', 'PKS Layanan Antar Jemput', 'PDF', '-', 'Valid', '2026-02-12', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
  `jumlah` int(11) NOT NULL,
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
(1, 'DS-001', 'Regular', 'PT Maju Jaya', 'Spare Part Mesin', 32, 'pcs', 'PT Angin Ribut', '2026-04-12', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'DS-002', 'Express', 'CV Berkah Abadi', 'Oli Mesin', 54, 'liter', 'CV Cahaya Terang', '2026-03-23', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'DS-003', 'Same Day', 'PT Sumber Makmur', 'Ban Kendaraan', 64, 'unit', 'Toko Maju', '2026-06-30', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'DS-004', 'Ekonomi', 'UD Sejahtera', 'Filter Udara', 52, 'set', 'UD Bahagia', '2026-06-19', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'DS-005', 'Regular', 'PT Indo Supplier', 'Aki Kendaraan', 12, 'buah', 'PT Kilat Jaya', '2026-04-17', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(6, 'DS-006', 'Express', 'PT Maju Jaya', 'Kampas Rem', 95, 'pcs', 'CV Sentosa', '2026-04-09', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(7, 'DS-007', 'Same Day', 'CV Berkah Abadi', 'Spare Part Mesin', 43, 'liter', 'PT Angin Ribut', '2026-04-28', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(8, 'DS-008', 'Ekonomi', 'PT Sumber Makmur', 'Oli Mesin', 95, 'unit', 'CV Cahaya Terang', '2026-04-24', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(9, 'DS-009', 'Regular', 'UD Sejahtera', 'Ban Kendaraan', 49, 'set', 'Toko Maju', '2026-03-18', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(10, 'DS-010', 'Express', 'PT Indo Supplier', 'Filter Udara', 55, 'buah', 'UD Bahagia', '2026-07-08', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(11, 'DS-011', 'Same Day', 'PT Maju Jaya', 'Aki Kendaraan', 66, 'pcs', 'PT Kilat Jaya', '2026-04-02', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(12, 'DS-012', 'Ekonomi', 'CV Berkah Abadi', 'Kampas Rem', 35, 'liter', 'CV Sentosa', '2026-05-27', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(13, 'DS-013', 'Regular', 'PT Sumber Makmur', 'Spare Part Mesin', 54, 'unit', 'PT Angin Ribut', '2026-03-30', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(14, 'DS-014', 'Express', 'UD Sejahtera', 'Oli Mesin', 54, 'set', 'CV Cahaya Terang', '2026-05-15', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(15, 'DS-015', 'Same Day', 'PT Indo Supplier', 'Ban Kendaraan', 54, 'buah', 'Toko Maju', '2026-06-19', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(16, 'DS-016', 'Ekonomi', 'PT Maju Jaya', 'Filter Udara', 34, 'pcs', 'UD Bahagia', '2026-06-08', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(17, 'DS-017', 'Regular', 'CV Berkah Abadi', 'Aki Kendaraan', 29, 'liter', 'PT Kilat Jaya', '2026-04-28', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(18, 'DS-018', 'Express', 'PT Sumber Makmur', 'Kampas Rem', 47, 'unit', 'CV Sentosa', '2026-06-09', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(19, 'DS-019', 'Same Day', 'UD Sejahtera', 'Spare Part Mesin', 39, 'set', 'PT Angin Ribut', '2026-06-10', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(20, 'DS-020', 'Ekonomi', 'PT Indo Supplier', 'Oli Mesin', 85, 'buah', 'CV Cahaya Terang', '2026-06-20', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(21, 'DS-021', 'Regular', 'PT Maju Jaya', 'Ban Kendaraan', 13, 'pcs', 'Toko Maju', '2026-05-11', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(22, 'DS-022', 'Express', 'CV Berkah Abadi', 'Filter Udara', 69, 'liter', 'UD Bahagia', '2026-03-16', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(23, 'DS-023', 'Same Day', 'PT Sumber Makmur', 'Aki Kendaraan', 49, 'unit', 'PT Kilat Jaya', '2026-05-20', 'Dikirim', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(24, 'DS-024', 'Ekonomi', 'UD Sejahtera', 'Kampas Rem', 71, 'set', 'CV Sentosa', '2026-05-30', 'Selesai', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(25, 'DS-025', 'Regular', 'PT Indo Supplier', 'Spare Part Mesin', 10, 'buah', 'PT Angin Ribut', '2026-05-17', 'Proses', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
(1, '010.000-26.000001', '2026-06-30', 'Keluaran', '01.234.567.8-901.000', 'PT Rental Maju Jaya', 5000000.00, 550000.00, 0.00, 'terbit', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(2, '010.000-26.000002', '2026-07-05', 'Masukan', '09.876.543.2-109.000', 'PT Supplier Sparepart', 3000000.00, 330000.00, 0.00, 'draft', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(3, '010.000-26.000003', '2026-07-08', 'Keluaran', '07.111.222.3-444.000', 'CV Transport Jaya', 7500000.00, 825000.00, 0.00, 'terbit', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57');

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
  `email_aktif` int(11) NOT NULL DEFAULT 0,
  `dns_terkelola` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `email_domains`
--

INSERT INTO `email_domains` (`id`, `nama_domain`, `provider`, `status`, `expired_date`, `email_aktif`, `dns_terkelola`, `created_at`, `updated_at`) VALUES
(1, 'perusahaan.com', 'GoDaddy', 'aktif', '2026-08-15', 120, 1, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'perusahaan.co.id', 'Rumahweb', 'aktif', '2025-11-30', 45, 1, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'old-brand.com', 'Namecheap', 'nonaktif', '2024-03-10', 0, 0, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'app.perusahaan.com', 'Cloudflare', 'aktif', '2027-01-01', 0, 1, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 1, 'GPS Tracker Pro', 'Jl. Teknologi No. 1, Jakarta', 'Marketing 1', '08550495278', 'Bengkel GPS 1', '08835224489', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(2, 1, 'Teltonika', 'Jl. Teknologi No. 2, Jakarta', 'Marketing 2', '08418797260', 'Bengkel GPS 2', '08355326141', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(3, 1, 'Queclink', 'Jl. Teknologi No. 3, Jakarta', 'Marketing 3', '08529640250', 'Bengkel GPS 3', '08221657942', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(4, 1, 'Concox', 'Jl. Teknologi No. 4, Jakarta', 'Marketing 4', '08785993532', 'Bengkel GPS 4', '08184012664', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(5, 1, 'Ruptela', 'Jl. Teknologi No. 5, Jakarta', 'Marketing 5', '08652491942', 'Bengkel GPS 5', '08615512909', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(6, 1, 'Coban', 'Jl. Teknologi No. 6, Jakarta', 'Marketing 6', '08236471352', 'Bengkel GPS 6', '08722495474', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(7, 1, 'Gosafe', 'Jl. Teknologi No. 7, Jakarta', 'Marketing 7', '08532742561', 'Bengkel GPS 7', '08396601369', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(8, 1, 'Jointech', 'Jl. Teknologi No. 8, Jakarta', 'Marketing 8', '08157679395', 'Bengkel GPS 8', '08207250977', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(9, 1, 'Meitrack', 'Jl. Teknologi No. 9, Jakarta', 'Marketing 9', '08967203768', 'Bengkel GPS 9', '08411908946', '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(10, 1, 'Sinotrack', 'Jl. Teknologi No. 10, Jakarta', 'Marketing 10', '08702591093', 'Bengkel GPS 10', '08123115821', '2026-07-09 17:56:55', '2026-07-09 17:56:55');

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
(1, 1, 1, 'OBD', 'aktif', '2025-12-10', '2026-10-10', 100000, 10, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(2, 2, 2, 'Hardwire', 'aktif', '2026-03-10', '2026-12-10', 200000, 9, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(3, 3, 3, 'Magnetic', 'aktif', '2025-08-10', '2026-09-10', 400000, 13, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(4, 4, 4, '4G LTE', 'nonaktif', '2025-02-10', '2025-11-10', 300000, 9, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(5, 5, 5, 'Solar', 'aktif', '2025-05-10', '2027-01-10', 200000, 20, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(6, 6, 6, 'OBD', 'aktif', '2026-02-10', '2027-06-10', 100000, 16, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(7, 7, 7, 'Hardwire', 'aktif', '2025-09-10', '2027-01-10', 100000, 16, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(8, 8, 8, 'Magnetic', 'aktif', '2026-03-10', '2028-02-10', 400000, 23, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(9, 9, 9, '4G LTE', 'aktif', '2026-04-10', '2028-01-10', 400000, 21, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(10, 10, 10, 'Solar', 'nonaktif', '2025-09-10', '2026-03-10', 500000, 6, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(11, 11, 1, 'OBD', 'aktif', '2026-06-10', '2027-11-10', 200000, 17, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(12, 12, 2, 'Hardwire', 'aktif', '2026-03-10', '2027-09-10', 200000, 18, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(13, 13, 3, 'Magnetic', 'nonaktif', '2025-04-10', '2026-04-10', 100000, 12, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(14, 14, 4, '4G LTE', 'aktif', '2025-12-10', '2027-01-10', 500000, 13, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(15, 15, 5, 'Solar', 'aktif', '2025-09-10', '2027-06-10', 500000, 21, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(16, 16, 6, 'OBD', 'aktif', '2025-06-10', '2027-03-10', 200000, 21, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(17, 17, 7, 'Hardwire', 'aktif', '2026-02-10', '2026-12-10', 400000, 10, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(18, 18, 8, 'Magnetic', 'aktif', '2025-08-10', '2026-09-10', 500000, 13, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(19, 19, 9, '4G LTE', 'nonaktif', '2025-01-10', '2026-05-10', 500000, 16, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(20, 20, 10, 'Solar', 'nonaktif', '2025-08-10', '2026-03-10', 300000, 7, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(21, 21, 1, 'OBD', 'nonaktif', '2025-06-10', '2026-07-10', 500000, 13, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(22, 22, 2, 'Hardwire', 'aktif', '2025-01-10', '2026-11-10', 200000, 22, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(23, 23, 3, 'Magnetic', 'aktif', '2026-04-10', '2027-02-10', 100000, 10, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(24, 24, 4, '4G LTE', 'aktif', '2026-05-10', '2027-07-10', 200000, 14, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(25, 25, 5, 'Solar', 'nonaktif', '2025-03-10', '2025-09-10', 100000, 6, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(26, 26, 6, 'OBD', 'aktif', '2025-11-10', '2027-01-10', 500000, 14, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(27, 27, 7, 'Hardwire', 'aktif', '2025-12-10', '2027-03-10', 400000, 15, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(28, 28, 8, 'Magnetic', 'nonaktif', '2025-05-10', '2026-04-10', 400000, 11, 'habis', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(29, 29, 9, '4G LTE', 'aktif', '2026-05-10', '2027-07-10', 100000, 14, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(30, 30, 10, 'Solar', 'aktif', '2025-02-10', '2026-11-10', 200000, 21, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(31, 31, 1, 'OBD', 'aktif', '2026-06-10', '2027-10-10', 200000, 16, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(32, 32, 2, 'Hardwire', 'aktif', '2025-02-10', '2027-01-10', 100000, 23, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(33, 33, 3, 'Magnetic', 'aktif', '2026-06-10', '2028-03-10', 400000, 21, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(34, 34, 4, '4G LTE', 'aktif', '2025-10-10', '2027-03-10', 200000, 17, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(35, 35, 5, 'Solar', 'aktif', '2026-01-10', '2027-11-10', 400000, 22, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(36, 36, 6, 'OBD', 'aktif', '2025-02-10', '2026-08-10', 500000, 18, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(37, 37, 7, 'Hardwire', 'aktif', '2026-02-10', '2026-08-10', 400000, 6, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(38, 38, 8, 'Magnetic', 'aktif', '2025-05-10', '2026-11-10', 100000, 18, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(39, 39, 9, '4G LTE', 'aktif', '2026-05-10', '2027-02-10', 300000, 9, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(40, 40, 10, 'Solar', 'aktif', '2026-04-10', '2027-05-10', 400000, 13, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(41, 41, 1, 'OBD', 'aktif', '2026-06-10', '2027-03-10', 500000, 9, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(42, 42, 2, 'Hardwire', 'aktif', '2025-10-10', '2027-05-10', 200000, 19, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(43, 43, 3, 'Magnetic', 'aktif', '2025-06-10', '2027-06-10', 500000, 24, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(44, 44, 4, '4G LTE', 'aktif', '2026-02-10', '2026-08-10', 400000, 6, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(45, 45, 5, 'Solar', 'aktif', '2025-11-10', '2026-09-10', 100000, 10, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(46, 46, 6, 'OBD', 'aktif', '2025-04-10', '2027-04-10', 200000, 24, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(47, 47, 7, 'Hardwire', 'aktif', '2026-03-10', '2027-04-10', 500000, 13, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(48, 48, 8, 'Magnetic', 'aktif', '2025-08-10', '2026-09-10', 200000, 13, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(49, 49, 9, '4G LTE', 'aktif', '2026-01-10', '2027-08-10', 100000, 19, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(50, 50, 10, 'Solar', 'aktif', '2025-06-10', '2026-11-10', 500000, 17, 'aktif', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55');

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
(1, 'TKT-001', '2025-01-10', 'Finance', 'Laptop tidak bisa menyala setelah update Windows', 'High', 'Resolved', 'Doni Prasetyo', '2 jam', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'TKT-002', '2025-01-15', 'HR', 'Email tidak bisa terkirim ke luar domain', 'Medium', 'Open', 'Siti Rahayu', '4 jam', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'TKT-003', '2025-01-20', 'Sales', 'Koneksi VPN terputus saat WFH', 'Critical', 'In Progress', 'Doni Prasetyo', '30 menit', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'TKT-004', '2025-02-01', 'IT', 'Printer di lantai 3 tidak terdeteksi oleh komputer', 'Low', 'Closed', 'Siti Rahayu', '1 hari', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'TKT-005', '2025-02-10', 'Operasional', 'Sistem ERP lambat saat jam kerja puncak', 'High', 'In Progress', 'Doni Prasetyo', '1 jam', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'Budi Santoso', 'Ijazah - Budi Santoso', 'Ijazah', 'hrd_files/budi_santoso/ijazah_budi_santoso.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(2, 'Budi Santoso', 'BPJS Kesehatan - Budi Santoso', 'BPJS Kesehatan', 'hrd_files/budi_santoso/bpjs_kes_budi_santoso.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(3, 'Budi Santoso', 'BPJS TK - Budi Santoso', 'BPJS TK', 'hrd_files/budi_santoso/bpjs_tk_budi_santoso.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(4, 'Siti Rahayu', 'KTP - Siti Rahayu', 'KTP', 'hrd_files/siti_rahayu/ktp_siti_rahayu.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(5, 'Siti Rahayu', 'NPWP - Siti Rahayu', 'NPWP', 'hrd_files/siti_rahayu/npwp_siti_rahayu.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(6, 'Siti Rahayu', 'SK Pengangkatan - Siti Rahayu', 'SK Pengangkatan', 'hrd_files/siti_rahayu/sk_siti_rahayu.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(7, 'Siti Rahayu', 'Kontrak Kerja - Siti Rahayu', 'Kontrak Kerja', 'hrd_files/siti_rahayu/kontrak_siti_rahayu.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(8, 'Agus Wibowo', 'KTP - Agus Wibowo', 'KTP', 'hrd_files/agus_wibowo/ktp_agus_wibowo.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(9, 'Agus Wibowo', 'NPWP - Agus Wibowo', 'NPWP', 'hrd_files/agus_wibowo/npwp_agus_wibowo.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(10, 'Agus Wibowo', 'BPJS Kesehatan - Agus Wibowo', 'BPJS Kesehatan', 'hrd_files/agus_wibowo/bpjs_kes_agus_wibowo.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(11, 'Dewi Kusuma', 'KTP - Dewi Kusuma', 'KTP', 'hrd_files/dewi_kusuma/ktp_dewi_kusuma.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(12, 'Dewi Kusuma', 'NPWP - Dewi Kusuma', 'NPWP', 'hrd_files/dewi_kusuma/npwp_dewi_kusuma.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(13, 'Dewi Kusuma', 'Ijazah - Dewi Kusuma', 'Ijazah', 'hrd_files/dewi_kusuma/ijazah_dewi_kusuma.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(14, 'Dewi Kusuma', 'SK Pengangkatan - Dewi Kusuma', 'SK Pengangkatan', 'hrd_files/dewi_kusuma/sk_dewi_kusuma.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(15, 'Dewi Kusuma', 'Kontrak Kerja - Dewi Kusuma', 'Kontrak Kerja', 'hrd_files/dewi_kusuma/kontrak_dewi_kusuma.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(16, 'Rini Apriani', 'KTP - Rini Apriani', 'KTP', 'hrd_files/rini_apriani/ktp_rini_apriani.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(17, 'Rini Apriani', 'NPWP - Rini Apriani', 'NPWP', 'hrd_files/rini_apriani/npwp_rini_apriani.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(18, 'Rini Apriani', 'SK Pengangkatan - Rini Apriani', 'SK Pengangkatan', 'hrd_files/rini_apriani/sk_rini_apriani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(19, 'Rini Apriani', 'BPJS Kesehatan - Rini Apriani', 'BPJS Kesehatan', 'hrd_files/rini_apriani/bpjs_kes_rini_apriani.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(20, 'Rini Apriani', 'BPJS TK - Rini Apriani', 'BPJS TK', 'hrd_files/rini_apriani/bpjs_tk_rini_apriani.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(21, 'Eko Prasetyo', 'NPWP - Eko Prasetyo', 'NPWP', 'hrd_files/eko_prasetyo/npwp_eko_prasetyo.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(22, 'Eko Prasetyo', 'Ijazah - Eko Prasetyo', 'Ijazah', 'hrd_files/eko_prasetyo/ijazah_eko_prasetyo.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(23, 'Eko Prasetyo', 'SK Pengangkatan - Eko Prasetyo', 'SK Pengangkatan', 'hrd_files/eko_prasetyo/sk_eko_prasetyo.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(24, 'Eko Prasetyo', 'BPJS TK - Eko Prasetyo', 'BPJS TK', 'hrd_files/eko_prasetyo/bpjs_tk_eko_prasetyo.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(25, 'Hendra Gunawan', 'KTP - Hendra Gunawan', 'KTP', 'hrd_files/hendra_gunawan/ktp_hendra_gunawan.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(26, 'Hendra Gunawan', 'NPWP - Hendra Gunawan', 'NPWP', 'hrd_files/hendra_gunawan/npwp_hendra_gunawan.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(27, 'Hendra Gunawan', 'Ijazah - Hendra Gunawan', 'Ijazah', 'hrd_files/hendra_gunawan/ijazah_hendra_gunawan.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(28, 'Hendra Gunawan', 'SK Pengangkatan - Hendra Gunawan', 'SK Pengangkatan', 'hrd_files/hendra_gunawan/sk_hendra_gunawan.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(29, 'Hendra Gunawan', 'Kontrak Kerja - Hendra Gunawan', 'Kontrak Kerja', 'hrd_files/hendra_gunawan/kontrak_hendra_gunawan.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(30, 'Rizky Fadillah', 'KTP - Rizky Fadillah', 'KTP', 'hrd_files/rizky_fadillah/ktp_rizky_fadillah.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(31, 'Rizky Fadillah', 'NPWP - Rizky Fadillah', 'NPWP', 'hrd_files/rizky_fadillah/npwp_rizky_fadillah.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(32, 'Rizky Fadillah', 'Ijazah - Rizky Fadillah', 'Ijazah', 'hrd_files/rizky_fadillah/ijazah_rizky_fadillah.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(33, 'Rizky Fadillah', 'SK Pengangkatan - Rizky Fadillah', 'SK Pengangkatan', 'hrd_files/rizky_fadillah/sk_rizky_fadillah.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(34, 'Rizky Fadillah', 'Kontrak Kerja - Rizky Fadillah', 'Kontrak Kerja', 'hrd_files/rizky_fadillah/kontrak_rizky_fadillah.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(35, 'Rizky Fadillah', 'BPJS Kesehatan - Rizky Fadillah', 'BPJS Kesehatan', 'hrd_files/rizky_fadillah/bpjs_kes_rizky_fadillah.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(36, 'Rizky Fadillah', 'BPJS TK - Rizky Fadillah', 'BPJS TK', 'hrd_files/rizky_fadillah/bpjs_tk_rizky_fadillah.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(37, 'Yusuf Hidayat', 'KTP - Yusuf Hidayat', 'KTP', 'hrd_files/yusuf_hidayat/ktp_yusuf_hidayat.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(38, 'Yusuf Hidayat', 'NPWP - Yusuf Hidayat', 'NPWP', 'hrd_files/yusuf_hidayat/npwp_yusuf_hidayat.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(39, 'Yusuf Hidayat', 'Ijazah - Yusuf Hidayat', 'Ijazah', 'hrd_files/yusuf_hidayat/ijazah_yusuf_hidayat.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(40, 'Yusuf Hidayat', 'SK Pengangkatan - Yusuf Hidayat', 'SK Pengangkatan', 'hrd_files/yusuf_hidayat/sk_yusuf_hidayat.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(41, 'Yusuf Hidayat', 'Kontrak Kerja - Yusuf Hidayat', 'Kontrak Kerja', 'hrd_files/yusuf_hidayat/kontrak_yusuf_hidayat.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(42, 'Yusuf Hidayat', 'BPJS TK - Yusuf Hidayat', 'BPJS TK', 'hrd_files/yusuf_hidayat/bpjs_tk_yusuf_hidayat.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(43, 'Linda Permata', 'KTP - Linda Permata', 'KTP', 'hrd_files/linda_permata/ktp_linda_permata.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(44, 'Linda Permata', 'NPWP - Linda Permata', 'NPWP', 'hrd_files/linda_permata/npwp_linda_permata.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(45, 'Linda Permata', 'Ijazah - Linda Permata', 'Ijazah', 'hrd_files/linda_permata/ijazah_linda_permata.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(46, 'Linda Permata', 'SK Pengangkatan - Linda Permata', 'SK Pengangkatan', 'hrd_files/linda_permata/sk_linda_permata.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(47, 'Linda Permata', 'Kontrak Kerja - Linda Permata', 'Kontrak Kerja', 'hrd_files/linda_permata/kontrak_linda_permata.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(48, 'Linda Permata', 'BPJS Kesehatan - Linda Permata', 'BPJS Kesehatan', 'hrd_files/linda_permata/bpjs_kes_linda_permata.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(49, 'Linda Permata', 'BPJS TK - Linda Permata', 'BPJS TK', 'hrd_files/linda_permata/bpjs_tk_linda_permata.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(50, 'Wahyu Nugroho', 'KTP - Wahyu Nugroho', 'KTP', 'hrd_files/wahyu_nugroho/ktp_wahyu_nugroho.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(51, 'Wahyu Nugroho', 'NPWP - Wahyu Nugroho', 'NPWP', 'hrd_files/wahyu_nugroho/npwp_wahyu_nugroho.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(52, 'Wahyu Nugroho', 'SK Pengangkatan - Wahyu Nugroho', 'SK Pengangkatan', 'hrd_files/wahyu_nugroho/sk_wahyu_nugroho.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(53, 'Wahyu Nugroho', 'BPJS TK - Wahyu Nugroho', 'BPJS TK', 'hrd_files/wahyu_nugroho/bpjs_tk_wahyu_nugroho.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(54, 'Fitri Handayani', 'NPWP - Fitri Handayani', 'NPWP', 'hrd_files/fitri_handayani/npwp_fitri_handayani.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(55, 'Fitri Handayani', 'SK Pengangkatan - Fitri Handayani', 'SK Pengangkatan', 'hrd_files/fitri_handayani/sk_fitri_handayani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(56, 'Fitri Handayani', 'BPJS Kesehatan - Fitri Handayani', 'BPJS Kesehatan', 'hrd_files/fitri_handayani/bpjs_kes_fitri_handayani.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(57, 'Fitri Handayani', 'BPJS TK - Fitri Handayani', 'BPJS TK', 'hrd_files/fitri_handayani/bpjs_tk_fitri_handayani.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(58, 'Dody Kurniawan', 'NPWP - Dody Kurniawan', 'NPWP', 'hrd_files/dody_kurniawan/npwp_dody_kurniawan.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(59, 'Dody Kurniawan', 'Ijazah - Dody Kurniawan', 'Ijazah', 'hrd_files/dody_kurniawan/ijazah_dody_kurniawan.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(60, 'Dody Kurniawan', 'SK Pengangkatan - Dody Kurniawan', 'SK Pengangkatan', 'hrd_files/dody_kurniawan/sk_dody_kurniawan.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(61, 'Dody Kurniawan', 'BPJS TK - Dody Kurniawan', 'BPJS TK', 'hrd_files/dody_kurniawan/bpjs_tk_dody_kurniawan.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(62, 'Teguh Santosa', 'KTP - Teguh Santosa', 'KTP', 'hrd_files/teguh_santosa/ktp_teguh_santosa.pdf', 'Kartu Tanda Penduduk', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(63, 'Teguh Santosa', 'BPJS Kesehatan - Teguh Santosa', 'BPJS Kesehatan', 'hrd_files/teguh_santosa/bpjs_kes_teguh_santosa.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(64, 'Teguh Santosa', 'BPJS TK - Teguh Santosa', 'BPJS TK', 'hrd_files/teguh_santosa/bpjs_tk_teguh_santosa.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(65, 'Arif Budiman', 'NPWP - Arif Budiman', 'NPWP', 'hrd_files/arif_budiman/npwp_arif_budiman.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(66, 'Arif Budiman', 'Ijazah - Arif Budiman', 'Ijazah', 'hrd_files/arif_budiman/ijazah_arif_budiman.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(67, 'Arif Budiman', 'SK Pengangkatan - Arif Budiman', 'SK Pengangkatan', 'hrd_files/arif_budiman/sk_arif_budiman.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 17:57:07', '2026-07-09 17:57:07'),
(68, 'Arif Budiman', 'BPJS Kesehatan - Arif Budiman', 'BPJS Kesehatan', 'hrd_files/arif_budiman/bpjs_kes_arif_budiman.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 17:57:07', '2026-07-09 17:57:07');

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
(1, 'PT Sinar Abadi', 'Sparepart', 5000000.00, 2000000.00, 3000000.00, '2026-07-20', 'belum_lunas', 'Pembelian sparepart mesin', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(2, 'CV Mitra Jaya', 'Service', 2500000.00, 2500000.00, 0.00, '2026-07-05', 'lunas', 'Service kendaraan fleet', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(3, 'PT Otomotif Nusantara', 'Aksesoris', 1200000.00, 500000.00, 700000.00, '2026-07-13', 'belum_lunas', 'Pembelian aksesoris mobil', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(4, 'UD Jaya Mandiri', 'Ban', 3000000.00, 1000000.00, 2000000.00, '2026-07-25', 'belum_lunas', 'Pembelian ban kendaraan', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(5, 'PT Diesel Prima', 'Mesin', 8000000.00, 8000000.00, 0.00, '2026-07-08', 'lunas', 'Perbaikan mesin besar', '2026-07-09 17:56:56', '2026-07-09 17:56:56');

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
  `umur_ekonomis` int(11) NOT NULL,
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
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
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
(1, 3, 1, NULL, 'perorangan', 'INV-2026-0001', 'ORD-0001', 'PT Teknologi Nusantara', 'Jl. Contoh No.1, Jakarta', 'Hendra Gunawan', '081294299828', 'pt.teknologi.nusantara@email.com', 'Bulan', '2025-12-09', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 286000.00, 52000.00, 2834000.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 4, 2, NULL, 'perusahaan', 'INV-2026-0002', 'ORD-0002', 'UD Sumber Rejeki', 'Jl. Contoh No.2, Jakarta', 'Dewi Lestari', '081291459866', 'ud.sumber.rejeki@email.com', 'Hari', '2026-03-01', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 132000.00, 24000.00, 1308000.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 5, 3, NULL, 'perusahaan', 'INV-2026-0003', 'ORD-0003', 'PT Logistik Andalan', 'Jl. Contoh No.3, Jakarta', 'Rizal Fahmi', '081242070361', 'pt.logistik.andalan@email.com', 'Tahun', '2025-10-12', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 242000.00, 44000.00, 2398000.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 9, 4, NULL, 'perorangan', 'INV-2026-0004', 'ORD-0004', 'CV Perdana Sejahtera', 'Jl. Contoh No.4, Jakarta', 'Wahyu Nugroho', '081239313516', 'cv.perdana.sejahtera@email.com', 'Bulan', '2026-04-22', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 374000.00, 68000.00, 3706000.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 10, 5, NULL, 'perusahaan', 'INV-2026-0005', 'ORD-0005', 'PT Aneka Niaga Indonesia', 'Jl. Contoh No.5, Jakarta', 'Fitri Handayani', '081225325991', 'pt.aneka.niaga.indonesia@email.com', 'Hari', '2025-12-27', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 104500.00, 19000.00, 1035500.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 11, 6, NULL, 'perusahaan', 'INV-2026-0006', 'ORD-0006', 'PT Maju Jaya Abadi', 'Jl. Contoh No.6, Jakarta', 'Budi Hartono', '081248285819', 'pt.maju.jaya.abadi@email.com', 'Tahun', '2025-12-20', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 71500.00, 13000.00, 708500.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 15, 7, NULL, 'perorangan', 'INV-2026-0007', 'ORD-0007', 'PT Logistik Andalan', 'Jl. Contoh No.7, Jakarta', 'Rizal Fahmi', '081247254358', 'pt.logistik.andalan@email.com', 'Bulan', '2026-05-11', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 165000.00, 30000.00, 1635000.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 16, 8, NULL, 'perusahaan', 'INV-2026-0008', 'ORD-0008', 'CV Karya Utama', 'Jl. Contoh No.8, Jakarta', 'Nur Hidayah', '081246589493', 'cv.karya.utama@email.com', 'Hari', '2026-01-07', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 55000.00, 10000.00, 545000.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 17, 9, NULL, 'perusahaan', 'INV-2026-0009', 'ORD-0009', 'PT Solusi Transportasi', 'Jl. Contoh No.9, Jakarta', 'Agus Setiawan', '081293874499', 'pt.solusi.transportasi@email.com', 'Tahun', '2026-06-23', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 280500.00, 51000.00, 2779500.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
  `status` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invoice_payments`
--

INSERT INTO `invoice_payments` (`id`, `invoice_id`, `amount`, `payment_date`, `method`, `transaction_id`, `file_pembayaran`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 654000.00, '2026-03-07', 'Cek/Giro', 'TXN-E36314E624', NULL, 'verified', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 2, 392400.00, '2026-03-25', 'Virtual Account', 'TXN-F8BC2FBE2C', NULL, 'pending', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 4, 3706000.00, '2026-05-02', 'Tunai', 'TXN-9929512DBE', NULL, 'verified', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 6, 354250.00, '2026-01-04', 'Cek/Giro', 'TXN-710FB21AC4', NULL, 'verified', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 6, 212550.00, '2026-01-18', 'Transfer Bank', 'TXN-D105AE60B3', NULL, 'pending', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 8, 545000.00, '2026-01-24', 'Virtual Account', 'TXN-E2B1D6A425', NULL, 'verified', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 1, '2025-12-09', '2026-05-09', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 2, '2026-03-01', '2026-06-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 3, '2025-10-12', '2026-02-12', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 4, '2026-04-22', '2026-10-22', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 5, '2025-12-27', '2026-06-27', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 6, '2025-12-20', '2026-05-20', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 7, '2026-05-11', '2026-06-11', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 8, '2026-01-07', '2026-05-07', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 9, '2026-06-23', '2026-08-23', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 1, 1, 'Sewa Kendaraan Operasional', 1, 4567418.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 2, 2, 'Biaya Driver', 4, 1485209.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 2, 2, 'Bahan Bakar', 3, 2569508.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 2, 2, 'Biaya Perawatan', 3, 4015086.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 3, 3, 'Bahan Bakar', 2, 1251381.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 3, 3, 'Biaya Perawatan', 2, 2366105.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 4, 4, 'Biaya Perawatan', 3, 678395.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 5, 5, 'Asuransi Kendaraan', 1, 3932320.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 5, 5, 'Biaya Administrasi', 1, 1257113.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(10, 5, 5, 'Sewa Kendaraan Operasional', 4, 4658529.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(11, 6, 6, 'Biaya Administrasi', 2, 4254346.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(12, 6, 6, 'Sewa Kendaraan Operasional', 4, 4387962.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(13, 7, 7, 'Sewa Kendaraan Operasional', 3, 1628073.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(14, 7, 7, 'Biaya Driver', 3, 1060201.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(15, 7, 7, 'Bahan Bakar', 2, 4856373.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(16, 8, 8, 'Biaya Driver', 3, 3930247.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(17, 8, 8, 'Bahan Bakar', 3, 3702159.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(18, 9, 9, 'Bahan Bakar', 4, 4568815.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(19, 9, 9, 'Biaya Perawatan', 4, 808642.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(20, 9, 9, 'Asuransi Kendaraan', 1, 3446073.00, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
  `pihak_pertama` text DEFAULT NULL,
  `contact_pertama` text DEFAULT NULL,
  `pihak_kedua` text DEFAULT NULL,
  `contact_kedua` text DEFAULT NULL,
  `file_kontrak` text DEFAULT NULL,
  `file_persyaratan` text DEFAULT NULL,
  `status` enum('dibuat','pending','approved','active','rejected','expired','completed','terminated') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inv_kontraks`
--

INSERT INTO `inv_kontraks` (`id`, `penawaran_id`, `no_kontrak`, `tanggal_kontrak`, `perjanjian_pembayaran`, `pihak_pertama`, `contact_pertama`, `pihak_kedua`, `contact_kedua`, `file_kontrak`, `file_persyaratan`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'KTR-0003', '2025-12-05', '2026-03-05', 'PT Apyrent Indonesia', '021-12345678', 'PT Teknologi Nusantara', 'Hendra Gunawan', NULL, NULL, 'pending', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 4, 'KTR-0004', '2026-02-24', '2026-05-24', 'PT Apyrent Indonesia', '021-12345678', 'UD Sumber Rejeki', 'Dewi Lestari', NULL, NULL, 'approved', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 5, 'KTR-0005', '2025-10-07', '2026-06-07', 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, 'active', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 9, 'KTR-0009', '2026-04-21', '2026-10-21', 'PT Apyrent Indonesia', '021-12345678', 'CV Perdana Sejahtera', 'Wahyu Nugroho', NULL, NULL, 'completed', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 10, 'KTR-0010', '2025-12-25', '2026-09-25', 'PT Apyrent Indonesia', '021-12345678', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', NULL, NULL, 'terminated', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 11, 'KTR-0011', '2025-12-17', '2026-10-17', 'PT Apyrent Indonesia', '021-12345678', 'PT Maju Jaya Abadi', 'Budi Hartono', NULL, NULL, 'pending', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 15, 'KTR-0015', '2026-05-06', '2026-10-06', 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, 'approved', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 16, 'KTR-0016', '2026-01-03', '2027-01-03', 'PT Apyrent Indonesia', '021-12345678', 'CV Karya Utama', 'Nur Hidayah', NULL, NULL, 'active', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 17, 'KTR-0017', '2026-06-21', '2026-10-21', 'PT Apyrent Indonesia', '021-12345678', 'PT Solusi Transportasi', 'Agus Setiawan', NULL, NULL, 'completed', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
  `pengirim` text DEFAULT NULL,
  `periode` int(10) UNSIGNED DEFAULT NULL,
  `staff` text DEFAULT NULL,
  `name_staff` text DEFAULT NULL,
  `direktur` text DEFAULT NULL,
  `name_direktur` text DEFAULT NULL,
  `status` enum('dibuat','pending','approved','active','rejected','expired','completed','terminated') DEFAULT 'pending',
  `total` text DEFAULT NULL,
  `file_penawaran` text DEFAULT NULL,
  `file_persyaratan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inv_penawarans`
--

INSERT INTO `inv_penawarans` (`id`, `no_penawaran`, `tanggal_penawaran`, `kepada`, `up`, `perihal`, `customer_name`, `contact_person`, `pengirim`, `periode`, `staff`, `name_staff`, `direktur`, `name_direktur`, `status`, `total`, `file_penawaran`, `file_persyaratan`, `created_at`, `updated_at`) VALUES
(1, 'PNW-0001', '2026-01-26', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '3400000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'PNW-0002', '2025-10-17', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '2850000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'PNW-0003', '2025-11-27', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '2600000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'PNW-0004', '2026-02-16', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '1200000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'PNW-0005', '2025-09-30', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'completed', '2200000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 'PNW-0006', '2025-10-25', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2200000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 'PNW-0007', '2025-12-05', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '1500000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 'PNW-0008', '2025-09-16', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '1500000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 'PNW-0009', '2026-04-12', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '3400000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(10, 'PNW-0010', '2025-12-12', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '950000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(11, 'PNW-0011', '2025-12-09', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'completed', '650000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(12, 'PNW-0012', '2026-03-15', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1200000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(13, 'PNW-0013', '2025-12-25', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '2200000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(14, 'PNW-0014', '2026-06-15', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '4400000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(15, 'PNW-0015', '2026-04-24', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '1500000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(16, 'PNW-0016', '2025-12-24', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '500000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(17, 'PNW-0017', '2026-06-12', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'completed', '2550000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(18, 'PNW-0018', '2026-04-24', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2850000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(19, 'PNW-0019', '2026-02-08', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '1300000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(20, 'PNW-0020', '2025-09-29', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '2400000', NULL, NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 1, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 1, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 2, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 2, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 3, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 3, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 4, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 5, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 5, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(10, 6, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(11, 6, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(12, 7, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(13, 7, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(14, 8, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(15, 8, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(16, 9, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(17, 9, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(18, 10, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(19, 11, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(20, 12, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(21, 13, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(22, 13, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(23, 14, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(24, 14, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(25, 15, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(26, 15, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(27, 16, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(28, 17, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(29, 17, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(30, 18, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(31, 18, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(32, 19, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(33, 19, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(34, 20, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(35, 20, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `inv_summaries`
--

CREATE TABLE `inv_summaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kontrak_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `inv_summaries` (`id`, `penawaran_id`, `kontrak_id`, `invoice_id`, `type`, `total_amount`, `paid_amount`, `remaining_amount`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 1, 'perorangan', 2834000.00, 0.00, 2834000.00, 'unpaid', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 4, 2, 2, 'perusahaan', 1308000.00, 654000.00, 654000.00, 'partial', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 5, 3, 3, 'perusahaan', 2398000.00, 0.00, 2398000.00, 'unpaid', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 9, 4, 4, 'perorangan', 3706000.00, 3706000.00, 0.00, 'lunas', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 10, 5, 5, 'perusahaan', 1035500.00, 0.00, 1035500.00, 'unpaid', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 11, 6, 6, 'perusahaan', 708500.00, 354250.00, 354250.00, 'partial', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 15, 7, 7, 'perorangan', 1635000.00, 0.00, 1635000.00, 'unpaid', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 16, 8, 8, 'perusahaan', 545000.00, 545000.00, 0.00, 'lunas', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 17, 9, 9, 'perusahaan', 2779500.00, 0.00, 2779500.00, 'unpaid', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'AST-001', 'Laptop Dell XPS 15', 'Laptop', 'Ruang IT Lt.2', 'Budi Santoso', 'Dell', '2022', 'Aktif', 'Unit utama developer', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'AST-002', 'HP LaserJet Pro M404', 'Printer', 'Ruang Admin', 'Sari Dewi', 'HP', '2021', 'Aktif', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'AST-003', 'MacBook Pro M2', 'Laptop', 'Ruang Desain', 'Andi Wijaya', 'Apple', '2023', 'Aktif', 'Untuk tim desain grafis', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'AST-004', 'Monitor LG 27 Inch 4K', 'Monitor', 'Ruang IT Lt.2', 'Rudi Hermawan', 'LG', '2022', 'Aktif', NULL, '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'AST-005', 'Cisco Switch 48 Port', 'Network', 'Ruang Server', 'Tim IT', 'Cisco', '2020', 'Rusak', 'Port 12-15 tidak berfungsi', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
(1, 1, 'Mobil SUV', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(2, 1, 'Mobil MPV', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(3, 1, 'Mobil Sedan', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(4, 1, 'Pickup', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(5, 1, 'Truck', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(6, 1, 'Bus Pariwisata', '2026-07-09 17:56:54', '2026-07-09 17:56:54');

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
(1, 'All Risk', 'Menanggung kerusakan ringan dan berat', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(2, 'TLO', 'Total Loss Only', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(3, 'Comprehensive', 'Perlindungan menyeluruh kendaraan', '2026-07-09 17:56:54', '2026-07-09 17:56:54');

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
  `Pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
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
(1, 'MKT001', 'Promo Rental Akhir Tahun', 'Promosi', 'Email', 'Pelanggan Aktif', '2026-12-25', '2026-12-31', 'Diskon Spesial Akhir Tahun!', 'Dapatkan diskon 20% untuk rental mobil', 'Dijadwalkan', 'Rina Marketing', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'MKT002', 'Re-engagement Campaign', 'Retensi', 'WhatsApp', 'Inaktif 6 Bulan', '2026-08-01', '2026-08-15', 'Kami Merindukan Anda', 'Rental lagi dan dapat voucher', 'Aktif', 'Ahmad Marketing', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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

INSERT INTO `kendaraan` (`id`, `user_id`, `member_id`, `jenis_id`, `nopol`, `foto`, `nama_pemilik`, `alamat`, `merk`, `tahun_pembuatan`, `tahun_perakitan`, `isi_silinder`, `warna`, `no_rangka`, `no_mesin`, `no_bpkb`, `warna_tnkb`, `bahan_bakar`, `kode_lokasi`, `no_urut_pendaftaran`, `harga_sewa_per_hari`, `harga_sewa_per_jam`, `batas_biaya`, `dokumen`, `masa_berlaku`, `kilometer_sekarang`, `limit_km_service`, `limit_bulan_service`, `km_terakhir_service`, `tanggal_terakhir_service`, `status_service`, `status_kendaraan`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, 'AA 1011 BE', NULL, 'Pemilik Kendaraan 1', 'Wonosobo', 'Toyota Avanza', '2021', '2021', '1500 CC', 'Hitam', 'NRFC5E3E437C60E', 'NMEAA566D8', 'BPKB000001', 'Hitam', 'Pertalite', 'AA', '001234', 409000, 72000, 1330000, NULL, '2026-04-10', 50111, 5000, 6, 45154, '2025-12-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(2, 1, NULL, 2, 'AB 1022 CF', NULL, 'Pemilik Kendaraan 2', 'Magelang', 'Toyota Innova', '2020', '2020', '1500 CC', 'Putih', 'NR347D8BAA75F0E', 'NMA267766B', 'BPKB000002', 'Hitam', 'Pertamax', 'AB', '002468', 518000, 50000, 1974000, NULL, '2028-02-10', 69166, 5000, 6, 62737, '2025-11-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(3, 1, NULL, 3, 'AD 1033 DG', NULL, 'Pemilik Kendaraan 3', 'Purworejo', 'Toyota Rush', '2024', '2024', '1500 CC', 'Silver', 'NR05BCAB37A5B5E', 'NM7DA12124', 'BPKB000003', 'Hitam', 'Solar', 'AD', '003702', 335000, 72000, 1306000, NULL, '2027-12-10', 72887, 5000, 6, 66219, '2025-09-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(4, 1, NULL, 1, 'AE 1044 EH', NULL, 'Pemilik Kendaraan 4', 'Kebumen', 'Toyota Fortuner', '2016', '2016', '1500 CC', 'Merah', 'NRD9F1155CE77F7', 'NM96142E2A', 'BPKB000004', 'Hitam', 'Pertamax Turbo', 'AE', '004936', 442000, 59000, 1324000, NULL, '2028-02-10', 42397, 5000, 6, 38085, '2025-10-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(5, 1, NULL, 2, 'AG 1055 FI', NULL, 'Pemilik Kendaraan 5', 'Purwokerto', 'Toyota Calya', '2018', '2018', '1500 CC', 'Biru', 'NRC2DBC1270E648', 'NMCABF6A7D', 'BPKB000005', 'Hitam', 'Pertalite', 'AG', '006170', 543000, 30000, 1115000, NULL, '2028-07-10', 54572, 5000, 6, 53404, '2025-10-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(6, 1, NULL, 3, 'AA 1066 GJ', NULL, 'Pemilik Kendaraan 6', 'Temanggung', 'Honda Brio', '2022', '2022', '1500 CC', 'Abu-abu', 'NRD5527EB4A669D', 'NM787F80E0', 'BPKB000006', 'Hitam', 'Pertamax', 'AA', '007404', 315000, 40000, 667000, NULL, '2026-06-10', 17504, 5000, 6, 10684, '2025-11-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(7, 1, NULL, 1, 'AB 1077 HK', NULL, 'Pemilik Kendaraan 7', 'Kendal', 'Honda Mobilio', '2019', '2019', '1000 CC', 'Coklat', 'NRF6B081BFC804D', 'NM238F09D0', 'BPKB000007', 'Hitam', 'Solar', 'AB', '008638', 206000, 59000, 1742000, NULL, '2027-09-10', 95994, 5000, 6, 93308, '2025-08-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(8, 1, NULL, 2, 'AD 1088 IL', NULL, 'Pemilik Kendaraan 8', 'Batang', 'Honda HR-V', '2020', '2020', '1000 CC', 'Kuning', 'NRC71D2E264F12E', 'NMB3E50BB9', 'BPKB000008', 'Hitam', 'Pertamax Turbo', 'AD', '009872', 392000, 57000, 797000, NULL, '2027-01-10', 87756, 5000, 6, 85871, '2026-04-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(9, 1, NULL, 3, 'AE 1099 JM', NULL, 'Pemilik Kendaraan 9', 'Wonosobo', 'Honda CR-V', '2017', '2017', '1500 CC', 'Hitam', 'NR28CB98ADDC49A', 'NM95D7A4AD', 'BPKB000009', 'Hitam', 'Pertalite', 'AE', '011106', 222000, 53000, 1866000, NULL, '2027-08-10', 40447, 5000, 6, 33005, '2026-03-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(10, 1, NULL, 1, 'AG 1110 KN', NULL, 'Pemilik Kendaraan 10', 'Magelang', 'Honda Jazz', '2015', '2015', '500 CC', 'Putih', 'NRC7018438D4E08', 'NMA21DC861', 'BPKB000010', 'Hitam', 'Pertamax', 'AG', '012340', 434000, 58000, 639000, NULL, '2028-02-10', 64211, 5000, 6, 59516, '2025-07-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(11, 1, NULL, 2, 'AA 1121 LO', NULL, 'Pemilik Kendaraan 11', 'Purworejo', 'Mitsubishi Xpander', '2016', '2016', '500 CC', 'Silver', 'NR101ED65635098', 'NMB836A727', 'BPKB000011', 'Hitam', 'Solar', 'AA', '013574', 463000, 65000, 1260000, NULL, '2028-05-10', 49364, 5000, 6, 47387, '2026-03-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(12, 1, NULL, 3, 'AB 1132 MP', NULL, 'Pemilik Kendaraan 12', 'Kebumen', 'Mitsubishi Pajero', '2022', '2022', '1500 CC', 'Merah', 'NRBA899300E453F', 'NMFCA534D3', 'BPKB000012', 'Hitam', 'Pertamax Turbo', 'AB', '014808', 551000, 67000, 1466000, NULL, '2028-02-10', 119029, 5000, 6, 115952, '2025-12-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(13, 1, NULL, 1, 'AD 1143 NQ', NULL, 'Pemilik Kendaraan 13', 'Purwokerto', 'Mitsubishi L300', '2016', '2016', '1000 CC', 'Biru', 'NR28C8D02298AFE', 'NM18E9181C', 'BPKB000013', 'Hitam', 'Pertalite', 'AD', '016042', 352000, 53000, 1088000, NULL, '2027-10-10', 82993, 5000, 6, 77413, '2026-03-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(14, 1, NULL, 2, 'AE 1154 OR', NULL, 'Pemilik Kendaraan 14', 'Temanggung', 'Mitsubishi Outlander', '2021', '2021', '1500 CC', 'Abu-abu', 'NR71E1B33FE3385', 'NM65CE77D8', 'BPKB000014', 'Hitam', 'Pertamax', 'AE', '017276', 365000, 63000, 1582000, NULL, '2028-02-10', 110849, 5000, 6, 108012, '2026-05-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(15, 1, NULL, 3, 'AG 1165 PS', NULL, 'Pemilik Kendaraan 15', 'Kendal', 'Daihatsu Xenia', '2022', '2022', '500 CC', 'Coklat', 'NR0188959EBD4CF', 'NMD99046EE', 'BPKB000015', 'Hitam', 'Solar', 'AG', '018510', 495000, 30000, 1646000, NULL, '2027-07-10', 54630, 5000, 6, 49407, '2026-04-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(16, 1, NULL, 1, 'AA 1176 QT', NULL, 'Pemilik Kendaraan 16', 'Batang', 'Daihatsu Terios', '2017', '2017', '1500 CC', 'Kuning', 'NR4CFC5AB2A1B24', 'NM66FA2F56', 'BPKB000016', 'Hitam', 'Pertamax Turbo', 'AA', '019744', 259000, 69000, 957000, NULL, '2026-09-10', 98205, 5000, 6, 91603, '2025-10-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(17, 1, NULL, 2, 'AB 1187 RU', NULL, 'Pemilik Kendaraan 17', 'Wonosobo', 'Daihatsu Sigra', '2015', '2015', '1000 CC', 'Hitam', 'NRB690B147D29AD', 'NMED999685', 'BPKB000017', 'Hitam', 'Pertalite', 'AB', '020978', 269000, 30000, 1706000, NULL, '2026-07-10', 55760, 5000, 6, 52469, '2026-01-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(18, 1, NULL, 3, 'AD 1198 SV', NULL, 'Pemilik Kendaraan 18', 'Magelang', 'Daihatsu Gran Max', '2024', '2024', '500 CC', 'Putih', 'NR89F8444CB9A7D', 'NM8CD1481B', 'BPKB000018', 'Hitam', 'Pertamax', 'AD', '022212', 544000, 50000, 1580000, NULL, '2027-05-10', 25584, 5000, 6, 20060, '2026-01-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(19, 1, NULL, 1, 'AE 1209 TW', NULL, 'Pemilik Kendaraan 19', 'Purworejo', 'Suzuki Ertiga', '2023', '2023', '1000 CC', 'Silver', 'NR67D11ED0217CF', 'NM0AAE1062', 'BPKB000019', 'Hitam', 'Solar', 'AE', '023446', 306000, 77000, 1885000, NULL, '2026-04-10', 114227, 5000, 6, 110974, '2026-02-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(20, 1, NULL, 2, 'AG 1220 UX', NULL, 'Pemilik Kendaraan 20', 'Kebumen', 'Suzuki APV', '2019', '2019', '1500 CC', 'Merah', 'NR4736A85D494F9', 'NM8C8EA669', 'BPKB000020', 'Hitam', 'Pertamax Turbo', 'AG', '024680', 295000, 61000, 698000, NULL, '2026-11-10', 48127, 5000, 6, 46958, '2025-07-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(21, 1, NULL, 3, 'AA 1231 VY', NULL, 'Pemilik Kendaraan 21', 'Purwokerto', 'Suzuki Jimny', '2016', '2016', '1500 CC', 'Biru', 'NR6EBC3372CE7C9', 'NMB0B07747', 'BPKB000021', 'Hitam', 'Pertalite', 'AA', '025914', 370000, 30000, 610000, NULL, '2026-04-10', 57324, 5000, 6, 51763, '2026-06-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(22, 1, NULL, 1, 'AB 1242 WZ', NULL, 'Pemilik Kendaraan 22', 'Temanggung', 'Suzuki Carry', '2015', '2015', '500 CC', 'Abu-abu', 'NR62F120141685C', 'NM6AA28811', 'BPKB000022', 'Hitam', 'Pertamax', 'AB', '027148', 511000, 33000, 1064000, NULL, '2027-12-10', 109166, 5000, 6, 107513, '2026-05-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(23, 1, NULL, 2, 'AD 1253 XA', NULL, 'Pemilik Kendaraan 23', 'Kendal', 'Nissan X-Trail', '2021', '2021', '1500 CC', 'Coklat', 'NRC5153735B748E', 'NMAB3A9544', 'BPKB000023', 'Hitam', 'Solar', 'AD', '028382', 494000, 56000, 813000, NULL, '2027-10-10', 91205, 5000, 6, 83899, '2025-11-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(24, 1, NULL, 3, 'AE 1264 YB', NULL, 'Pemilik Kendaraan 24', 'Batang', 'Nissan Livina', '2021', '2021', '1000 CC', 'Kuning', 'NR249973A0D8662', 'NMADFDF0BD', 'BPKB000024', 'Hitam', 'Pertamax Turbo', 'AE', '029616', 599000, 35000, 1433000, NULL, '2027-11-10', 46988, 5000, 6, 45025, '2026-02-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(25, 1, NULL, 1, 'AG 1275 ZC', NULL, 'Pemilik Kendaraan 25', 'Wonosobo', 'Nissan Terra', '2018', '2018', '500 CC', 'Hitam', 'NR1233A73D59FC1', 'NM3B083C41', 'BPKB000025', 'Hitam', 'Pertalite', 'AG', '030850', 460000, 67000, 872000, NULL, '2027-08-10', 78117, 5000, 6, 71526, '2025-07-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(26, 1, NULL, 2, 'AA 1286 AD', NULL, 'Pemilik Kendaraan 26', 'Magelang', 'Isuzu Panther', '2021', '2021', '1500 CC', 'Putih', 'NR8B7FE74809E8F', 'NM11A757F7', 'BPKB000026', 'Hitam', 'Pertamax', 'AA', '032084', 443000, 37000, 1443000, NULL, '2028-01-10', 89869, 5000, 6, 86357, '2026-04-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(27, 1, NULL, 3, 'AB 1297 BE', NULL, 'Pemilik Kendaraan 27', 'Purworejo', 'Isuzu D-Max', '2016', '2016', '1000 CC', 'Silver', 'NRB559A1B082827', 'NM2BA7C96A', 'BPKB000027', 'Hitam', 'Solar', 'AB', '033318', 458000, 75000, 1617000, NULL, '2027-10-10', 61563, 5000, 6, 55436, '2025-12-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(28, 1, NULL, 1, 'AD 1308 CF', NULL, 'Pemilik Kendaraan 28', 'Kebumen', 'Isuzu Elf', '2024', '2024', '500 CC', 'Merah', 'NR8868E52D15081', 'NMF004F89A', 'BPKB000028', 'Hitam', 'Pertamax Turbo', 'AD', '034552', 368000, 32000, 1399000, NULL, '2027-02-10', 87666, 5000, 6, 84992, '2026-06-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(29, 1, NULL, 2, 'AE 1319 DG', NULL, 'Pemilik Kendaraan 29', 'Purwokerto', 'Wuling Almaz', '2024', '2024', '1500 CC', 'Biru', 'NR9B965F12D9041', 'NM09AD4CEA', 'BPKB000029', 'Hitam', 'Pertalite', 'AE', '035786', 268000, 80000, 757000, NULL, '2028-04-10', 12511, 5000, 6, 8958, '2025-08-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(30, 1, NULL, 3, 'AG 1330 EH', NULL, 'Pemilik Kendaraan 30', 'Temanggung', 'Wuling Air ev', '2018', '2018', '1500 CC', 'Abu-abu', 'NR5238145D8FA5E', 'NMB7E2C1AB', 'BPKB000030', 'Hitam', 'Pertamax', 'AG', '037020', 444000, 57000, 1758000, NULL, '2026-07-10', 7570, 5000, 6, 901, '2026-01-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(31, 1, NULL, 1, 'AA 1341 FI', NULL, 'Pemilik Kendaraan 31', 'Kendal', 'Toyota Avanza', '2024', '2024', '1500 CC', 'Coklat', 'NR8551649018B27', 'NMC0EF36C6', 'BPKB000031', 'Hitam', 'Solar', 'AA', '038254', 476000, 79000, 1438000, NULL, '2028-02-10', 27708, 5000, 6, 23677, '2026-04-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(32, 1, NULL, 2, 'AB 1352 GJ', NULL, 'Pemilik Kendaraan 32', 'Batang', 'Toyota Innova', '2016', '2016', '500 CC', 'Kuning', 'NR83DC5F76D3D1F', 'NM1A73548C', 'BPKB000032', 'Hitam', 'Pertamax Turbo', 'AB', '039488', 289000, 36000, 530000, NULL, '2027-03-10', 94304, 5000, 6, 87807, '2025-12-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(33, 1, NULL, 3, 'AD 1363 HK', NULL, 'Pemilik Kendaraan 33', 'Wonosobo', 'Toyota Rush', '2024', '2024', '1500 CC', 'Hitam', 'NR7D8A942BB2B06', 'NMD7DE1C30', 'BPKB000033', 'Hitam', 'Pertalite', 'AD', '040722', 563000, 56000, 737000, NULL, '2027-09-10', 14068, 5000, 6, 10429, '2026-03-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(34, 1, NULL, 1, 'AE 1374 IL', NULL, 'Pemilik Kendaraan 34', 'Magelang', 'Toyota Fortuner', '2016', '2016', '1500 CC', 'Putih', 'NR6BB7CA4622D0E', 'NMBCAF1E4D', 'BPKB000034', 'Hitam', 'Pertamax', 'AE', '041956', 353000, 64000, 1296000, NULL, '2028-01-10', 85416, 5000, 6, 80453, '2026-03-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(35, 1, NULL, 2, 'AG 1385 JM', NULL, 'Pemilik Kendaraan 35', 'Purworejo', 'Toyota Calya', '2016', '2016', '1000 CC', 'Silver', 'NR2B41A619C9BF1', 'NM796B7335', 'BPKB000035', 'Hitam', 'Solar', 'AG', '043190', 497000, 35000, 537000, NULL, '2027-08-10', 23111, 5000, 6, 21194, '2025-07-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(36, 1, NULL, 3, 'AA 1396 KN', NULL, 'Pemilik Kendaraan 36', 'Kebumen', 'Honda Brio', '2018', '2018', '500 CC', 'Merah', 'NREF70459230C5C', 'NM5B1C5010', 'BPKB000036', 'Hitam', 'Pertamax Turbo', 'AA', '044424', 331000, 39000, 1060000, NULL, '2026-05-10', 61964, 5000, 6, 57163, '2026-02-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(37, 1, NULL, 1, 'AB 1407 LO', NULL, 'Pemilik Kendaraan 37', 'Purwokerto', 'Honda Mobilio', '2018', '2018', '500 CC', 'Biru', 'NRB4F6268FE8E3C', 'NM66823A28', 'BPKB000037', 'Hitam', 'Pertalite', 'AB', '045658', 394000, 36000, 1162000, NULL, '2027-01-10', 22967, 5000, 6, 17103, '2026-06-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(38, 1, NULL, 2, 'AD 1418 MP', NULL, 'Pemilik Kendaraan 38', 'Temanggung', 'Honda HR-V', '2018', '2018', '500 CC', 'Abu-abu', 'NR09F7566B4E018', 'NM6D3C7CF5', 'BPKB000038', 'Hitam', 'Pertamax', 'AD', '046892', 344000, 65000, 1888000, NULL, '2027-12-10', 67120, 5000, 6, 59619, '2026-04-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(39, 1, NULL, 3, 'AE 1429 NQ', NULL, 'Pemilik Kendaraan 39', 'Kendal', 'Honda CR-V', '2015', '2015', '1500 CC', 'Coklat', 'NR2DFC28E9E22E4', 'NMBE1C5CD3', 'BPKB000039', 'Hitam', 'Solar', 'AE', '048126', 238000, 57000, 1464000, NULL, '2028-04-10', 60442, 5000, 6, 53985, '2026-02-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(40, 1, NULL, 1, 'AG 1440 OR', NULL, 'Pemilik Kendaraan 40', 'Batang', 'Honda Jazz', '2022', '2022', '500 CC', 'Kuning', 'NRD21A1A31A7898', 'NM21C25BDA', 'BPKB000040', 'Hitam', 'Pertamax Turbo', 'AG', '049360', 352000, 38000, 1345000, NULL, '2026-09-10', 26724, 5000, 6, 19973, '2026-05-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(41, 1, NULL, 2, 'AA 1451 PS', NULL, 'Pemilik Kendaraan 41', 'Wonosobo', 'Mitsubishi Xpander', '2018', '2018', '1500 CC', 'Hitam', 'NR5BB2C9B97AB51', 'NM20FFAE4E', 'BPKB000041', 'Hitam', 'Pertalite', 'AA', '050594', 575000, 74000, 500000, NULL, '2027-05-10', 92746, 5000, 6, 89917, '2026-06-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(42, 1, NULL, 3, 'AB 1462 QT', NULL, 'Pemilik Kendaraan 42', 'Magelang', 'Mitsubishi Pajero', '2020', '2020', '500 CC', 'Putih', 'NR4DB76BF66AFB5', 'NM68D57762', 'BPKB000042', 'Hitam', 'Pertamax', 'AB', '051828', 229000, 38000, 717000, NULL, '2028-01-10', 42431, 5000, 6, 36568, '2026-02-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(43, 1, NULL, 1, 'AD 1473 RU', NULL, 'Pemilik Kendaraan 43', 'Purworejo', 'Mitsubishi L300', '2015', '2015', '1500 CC', 'Silver', 'NR0DC971245341E', 'NMBA4C4814', 'BPKB000043', 'Hitam', 'Solar', 'AD', '053062', 425000, 75000, 1945000, NULL, '2026-12-10', 116018, 5000, 6, 113210, '2026-02-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(44, 1, NULL, 2, 'AE 1484 SV', NULL, 'Pemilik Kendaraan 44', 'Kebumen', 'Mitsubishi Outlander', '2019', '2019', '500 CC', 'Merah', 'NR476664C2FBE1E', 'NMB8004991', 'BPKB000044', 'Hitam', 'Pertamax Turbo', 'AE', '054296', 370000, 62000, 1062000, NULL, '2026-11-10', 6654, 5000, 6, 3329, '2026-03-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(45, 1, NULL, 3, 'AG 1495 TW', NULL, 'Pemilik Kendaraan 45', 'Purwokerto', 'Daihatsu Xenia', '2015', '2015', '1500 CC', 'Biru', 'NR7627556EB210F', 'NMF58C9AE1', 'BPKB000045', 'Hitam', 'Pertalite', 'AG', '055530', 523000, 80000, 1646000, NULL, '2028-05-10', 103269, 5000, 6, 101542, '2025-12-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(46, 1, NULL, 1, 'AA 1506 UX', NULL, 'Pemilik Kendaraan 46', 'Temanggung', 'Daihatsu Terios', '2020', '2020', '500 CC', 'Abu-abu', 'NR10E297C43FFA9', 'NM06A94CA6', 'BPKB000046', 'Hitam', 'Pertamax', 'AA', '056764', 205000, 40000, 1048000, NULL, '2027-08-10', 27932, 5000, 6, 23304, '2025-10-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(47, 1, NULL, 2, 'AB 1517 VY', NULL, 'Pemilik Kendaraan 47', 'Kendal', 'Daihatsu Sigra', '2017', '2017', '1500 CC', 'Coklat', 'NRF3E8AC22132E1', 'NM16A92731', 'BPKB000047', 'Hitam', 'Solar', 'AB', '057998', 248000, 65000, 1050000, NULL, '2026-08-10', 82547, 5000, 6, 76789, '2025-11-10', 'aman', 'service', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(48, 1, NULL, 3, 'AD 1528 WZ', NULL, 'Pemilik Kendaraan 48', 'Batang', 'Daihatsu Gran Max', '2015', '2015', '1000 CC', 'Kuning', 'NR8F3EB52F6AA35', 'NM161F258D', 'BPKB000048', 'Hitam', 'Pertamax Turbo', 'AD', '059232', 321000, 62000, 1871000, NULL, '2027-06-10', 48859, 5000, 6, 45224, '2026-05-10', 'service', 'bermasalah', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(49, 1, NULL, 1, 'AE 1539 XA', NULL, 'Pemilik Kendaraan 49', 'Wonosobo', 'Suzuki Ertiga', '2018', '2018', '1000 CC', 'Hitam', 'NREDD43B2F51720', 'NMDE6CB379', 'BPKB000049', 'Hitam', 'Pertalite', 'AE', '060466', 249000, 50000, 552000, NULL, '2026-07-10', 12482, 5000, 6, 10562, '2026-05-10', 'aman', 'tersedia', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(50, 1, NULL, 2, 'AG 1550 YB', NULL, 'Pemilik Kendaraan 50', 'Magelang', 'Suzuki APV', '2017', '2017', '500 CC', 'Putih', 'NREBB917F2E3BB4', 'NM0093B6FC', 'BPKB000050', 'Hitam', 'Pertamax', 'AG', '061700', 426000, 71000, 1596000, NULL, '2028-03-10', 110670, 5000, 6, 104460, '2025-11-10', 'service', 'disewa', '2026-07-09 17:56:54', '2026-07-09 17:56:54'),
(51, 1, 1, 2, 'AA2859VF', 'kendaraan/foto/1783620202_images (3).jpg', 'budi', 'Wonosobo, Jawa Tengah', 'Toyota', '2021', '1989', '4', 'Putih', 'Sit sed laudantium', 'Adipisicing saepe co', 'Nihil enim voluptate', NULL, 'Pertamax', NULL, NULL, 90, 56, 44, 'kendaraan/dokumen/1783620202_Daftar-Penawaran-2026-07-09.pdf', '1996-06-18', 2, 58, 100, 97, '1972-07-03', 'aman', 'tersedia', '2026-07-09 18:03:22', '2026-07-09 18:03:22');

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
(1, '2026-04-07', 'INV-001', 1, 'Rental', 'cash', 'Penerimaan Rental ke-1', 4069000.00, 0.00, 4069000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(2, '2026-07-07', 'INV-002', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-2', 1374000.00, 0.00, 5443000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(3, '2026-07-05', 'EXP-003', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-3', 0.00, 3134000.00, 2309000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(4, '2026-02-04', 'INV-004', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-4', 2796000.00, 0.00, 5105000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(5, '2026-02-10', 'INV-005', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-5', 993000.00, 0.00, 6098000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(6, '2026-03-26', 'EXP-006', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-6', 0.00, 2533000.00, 3565000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(7, '2026-06-06', 'INV-007', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-7', 706000.00, 0.00, 4271000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(8, '2026-03-09', 'INV-008', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-8', 3205000.00, 0.00, 7476000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(9, '2026-02-15', 'EXP-009', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-9', 0.00, 1473000.00, 6003000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(10, '2026-04-16', 'INV-010', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-10', 3570000.00, 0.00, 9573000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(11, '2026-05-15', 'INV-011', 1, 'Rental', 'cash', 'Penerimaan Rental ke-11', 3881000.00, 0.00, 13454000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(12, '2026-03-28', 'EXP-012', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-12', 0.00, 4908000.00, 8546000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(13, '2026-01-13', 'INV-013', 1, 'Denda', 'cash', 'Penerimaan Denda ke-13', 4626000.00, 0.00, 13172000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(14, '2026-04-15', 'INV-014', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-14', 2649000.00, 0.00, 15821000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(15, '2026-06-14', 'EXP-015', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-15', 0.00, 4023000.00, 11798000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(16, '2026-06-14', 'INV-016', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-16', 1616000.00, 0.00, 13414000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(17, '2026-07-09', 'INV-017', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-17', 768000.00, 0.00, 14182000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(18, '2026-05-06', 'EXP-018', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-18', 0.00, 2556000.00, 11626000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(19, '2026-02-27', 'INV-019', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-19', 4347000.00, 0.00, 15973000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(20, '2026-06-14', 'INV-020', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-20', 4259000.00, 0.00, 20232000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(21, '2026-02-13', 'EXP-021', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-21', 0.00, 2923000.00, 17309000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(22, '2026-04-30', 'INV-022', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-22', 495000.00, 0.00, 17804000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(23, '2026-05-10', 'INV-023', 1, 'Denda', 'cash', 'Penerimaan Denda ke-23', 2963000.00, 0.00, 20767000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(24, '2026-04-11', 'EXP-024', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-24', 0.00, 1125000.00, 19642000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(25, '2026-02-21', 'INV-025', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-25', 393000.00, 0.00, 20035000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(26, '2026-02-11', 'INV-026', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-26', 4781000.00, 0.00, 24816000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(27, '2026-02-19', 'EXP-027', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-27', 0.00, 305000.00, 24511000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(28, '2026-06-22', 'INV-028', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-28', 2573000.00, 0.00, 27084000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(29, '2026-06-18', 'INV-029', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-29', 3833000.00, 0.00, 30917000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(30, '2026-04-30', 'EXP-030', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-30', 0.00, 1447000.00, 29470000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(31, '2026-04-08', 'INV-031', 1, 'Rental', 'cash', 'Penerimaan Rental ke-31', 2863000.00, 0.00, 32333000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(32, '2026-06-12', 'INV-032', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-32', 263000.00, 0.00, 32596000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(33, '2026-05-16', 'EXP-033', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-33', 0.00, 4824000.00, 27772000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(34, '2026-02-27', 'INV-034', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-34', 1928000.00, 0.00, 29700000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(35, '2026-05-24', 'INV-035', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-35', 1658000.00, 0.00, 31358000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(36, '2026-05-03', 'EXP-036', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-36', 0.00, 4585000.00, 26773000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(37, '2026-07-03', 'INV-037', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-37', 1113000.00, 0.00, 27886000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(38, '2026-04-13', 'INV-038', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-38', 584000.00, 0.00, 28470000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(39, '2026-05-01', 'EXP-039', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-39', 0.00, 2576000.00, 25894000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(40, '2026-04-27', 'INV-040', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-40', 397000.00, 0.00, 26291000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(41, '2026-03-29', 'INV-041', 1, 'Rental', 'cash', 'Penerimaan Rental ke-41', 2533000.00, 0.00, 28824000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(42, '2026-05-03', 'EXP-042', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-42', 0.00, 2534000.00, 26290000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(43, '2026-02-12', 'INV-043', 1, 'Denda', 'cash', 'Penerimaan Denda ke-43', 888000.00, 0.00, 27178000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(44, '2026-05-15', 'INV-044', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-44', 1643000.00, 0.00, 28821000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(45, '2026-05-27', 'EXP-045', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-45', 0.00, 454000.00, 28367000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(46, '2026-03-02', 'INV-046', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-46', 2894000.00, 0.00, 31261000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(47, '2026-04-22', 'INV-047', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-47', 1669000.00, 0.00, 32930000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(48, '2026-05-28', 'EXP-048', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-48', 0.00, 3137000.00, 29793000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(49, '2026-06-11', 'INV-049', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-49', 4914000.00, 0.00, 34707000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(50, '2026-06-05', 'INV-050', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-50', 2410000.00, 0.00, 37117000.00, '2026-07-09 17:56:56', '2026-07-09 17:56:56');

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
  `tanggal_bayar` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kir`
--

INSERT INTO `kir` (`id`, `kendaraan_id`, `no_uji`, `masa_berlaku`, `biaya`, `tanggal_bayar`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'KIR-2026-001', '2026-06-21', 350000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(2, 2, 'KIR-2026-002', '2027-10-10', 500000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(3, 3, 'KIR-2026-003', '2026-10-28', 50000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(4, 4, 'KIR-2026-004', '2027-07-11', 500000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(5, 5, 'KIR-2026-005', '2028-03-11', 100000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(6, 6, 'KIR-2026-006', '2027-09-18', 400000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(7, 7, 'KIR-2026-007', '2027-08-31', 50000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(8, 8, 'KIR-2026-008', '2026-11-26', 450000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(9, 9, 'KIR-2026-009', '2026-12-14', 100000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(10, 10, 'KIR-2026-010', '2027-10-26', 150000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(11, 11, 'KIR-2026-011', '2028-05-09', 50000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(12, 12, 'KIR-2026-012', '2028-05-13', 500000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(13, 13, 'KIR-2026-013', '2026-10-09', 200000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(14, 14, 'KIR-2026-014', '2028-02-26', 500000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(15, 15, 'KIR-2026-015', '2026-06-06', 100000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(16, 16, 'KIR-2026-016', '2027-07-06', 100000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(17, 17, 'KIR-2026-017', '2026-08-24', 150000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(18, 18, 'KIR-2026-018', '2027-02-14', 50000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(19, 19, 'KIR-2026-019', '2027-09-13', 100000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(20, 20, 'KIR-2026-020', '2027-06-01', 250000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(21, 21, 'KIR-2026-021', '2028-04-02', 100000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(22, 22, 'KIR-2026-022', '2028-03-27', 400000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(23, 23, 'KIR-2026-023', '2026-12-19', 300000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(24, 24, 'KIR-2026-024', '2028-05-08', 450000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(25, 25, 'KIR-2026-025', '2028-05-06', 400000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(26, 26, 'KIR-2026-026', '2027-01-12', 150000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(27, 27, 'KIR-2026-027', '2027-03-12', 450000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(28, 28, 'KIR-2026-028', '2026-11-17', 200000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(29, 29, 'KIR-2026-029', '2027-03-02', 150000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(30, 30, 'KIR-2026-030', '2026-07-30', 400000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(31, 31, 'KIR-2026-031', '2027-11-18', 150000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(32, 32, 'KIR-2026-032', '2027-12-24', 450000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(33, 33, 'KIR-2026-033', '2028-01-21', 300000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(34, 34, 'KIR-2026-034', '2026-07-15', 450000.00, NULL, NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(35, 35, 'KIR-2026-035', '2027-11-18', 350000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(36, 36, 'KIR-2026-036', '2026-06-02', 500000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(37, 37, 'KIR-2026-037', '2027-09-27', 350000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(38, 38, 'KIR-2026-038', '2028-04-13', 50000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(39, 39, 'KIR-2026-039', '2027-02-06', 50000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(40, 40, 'KIR-2026-040', '2027-03-01', 400000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(41, 41, 'KIR-2026-041', '2026-06-10', 150000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(42, 42, 'KIR-2026-042', '2028-05-22', 150000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(43, 43, 'KIR-2026-043', '2026-12-15', 250000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(44, 44, 'KIR-2026-044', '2027-08-11', 350000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(45, 45, 'KIR-2026-045', '2027-12-03', 300000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(46, 46, 'KIR-2026-046', '2027-11-29', 250000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(47, 47, 'KIR-2026-047', '2027-09-15', 450000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(48, 48, 'KIR-2026-048', '2027-10-29', 100000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(49, 49, 'KIR-2026-049', '2026-05-18', 250000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(50, 50, 'KIR-2026-050', '2027-04-20', 350000.00, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56');

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
(1, 'Andi', '2026-01', 45000000.00, 3.00, 1350000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'Budi', '2026-01', 38000000.00, 3.00, 1140000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(3, 'Cici', '2026-01', 52000000.00, 3.50, 1820000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(4, 'Dani', '2026-01', 29000000.00, 3.00, 870000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(5, 'Andi', '2026-02', 55000000.00, 3.50, 1925000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(6, 'Budi', '2026-02', 42000000.00, 3.00, 1260000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(7, 'Cici', '2026-02', 60000000.00, 4.00, 2400000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(8, 'Dani', '2026-02', 35000000.00, 3.00, 1050000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(9, 'Andi', '2026-03', 48000000.00, 3.00, 1440000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(10, 'Budi', '2026-03', 51000000.00, 3.50, 1785000.00, 'Sudah Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(11, 'Cici', '2026-03', 44000000.00, 3.00, 1320000.00, 'Belum Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(12, 'Dani', '2026-03', 39000000.00, 3.00, 1170000.00, 'Belum Dibayar', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
  `disiplin` int(11) NOT NULL,
  `kolaborasi` int(11) NOT NULL,
  `produktivitas` int(11) NOT NULL,
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
(1, 'Rini Apriani', 'Q1 2025', 73, 91, 87, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(2, 'Rini Apriani', 'Q2 2025', 93, 87, 95, 91.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(3, 'Rini Apriani', 'Q3 2025', 75, 95, 70, 80.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(4, 'Rini Apriani', 'Q4 2025', 80, 77, 72, 76.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(5, 'Rini Apriani', 'Q1 2026', 81, 100, 71, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(6, 'Rini Apriani', 'Q2 2026', 95, 84, 91, 90.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(7, 'Eko Prasetyo', 'Q1 2025', 97, 92, 73, 87.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(8, 'Eko Prasetyo', 'Q2 2025', 79, 68, 71, 72.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(9, 'Eko Prasetyo', 'Q3 2025', 91, 98, 95, 94.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(10, 'Eko Prasetyo', 'Q4 2025', 74, 74, 72, 73.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(11, 'Eko Prasetyo', 'Q1 2026', 84, 94, 96, 91.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(12, 'Eko Prasetyo', 'Q2 2026', 78, 73, 67, 72.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(13, 'Rizky Fadillah', 'Q1 2025', 93, 94, 89, 92.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(14, 'Rizky Fadillah', 'Q2 2025', 74, 95, 92, 87.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(15, 'Rizky Fadillah', 'Q3 2025', 81, 66, 99, 82.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(16, 'Rizky Fadillah', 'Q4 2025', 88, 93, 96, 92.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(17, 'Rizky Fadillah', 'Q1 2026', 65, 68, 78, 70.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(18, 'Rizky Fadillah', 'Q2 2026', 75, 88, 67, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(19, 'Yusuf Hidayat', 'Q1 2025', 90, 84, 90, 88.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(20, 'Yusuf Hidayat', 'Q2 2025', 92, 97, 87, 92.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(21, 'Yusuf Hidayat', 'Q3 2025', 96, 82, 71, 83.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(22, 'Yusuf Hidayat', 'Q4 2025', 71, 95, 90, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(23, 'Yusuf Hidayat', 'Q1 2026', 68, 95, 85, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(24, 'Yusuf Hidayat', 'Q2 2026', 85, 72, 93, 83.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(25, 'Wahyu Nugroho', 'Q1 2025', 91, 89, 68, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(26, 'Wahyu Nugroho', 'Q2 2025', 84, 82, 69, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(27, 'Wahyu Nugroho', 'Q3 2025', 100, 96, 98, 98.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(28, 'Wahyu Nugroho', 'Q4 2025', 99, 77, 95, 90.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(29, 'Wahyu Nugroho', 'Q1 2026', 65, 98, 98, 87.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(30, 'Wahyu Nugroho', 'Q2 2026', 65, 74, 68, 69.00, 'Dewi Kusuma', 'Perlu pembinaan dan evaluasi lanjutan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(31, 'Fitri Handayani', 'Q1 2025', 75, 86, 85, 82.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(32, 'Fitri Handayani', 'Q2 2025', 87, 78, 99, 88.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(33, 'Fitri Handayani', 'Q3 2025', 100, 66, 84, 83.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(34, 'Fitri Handayani', 'Q4 2025', 65, 85, 72, 74.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(35, 'Fitri Handayani', 'Q1 2026', 75, 88, 98, 87.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(36, 'Fitri Handayani', 'Q2 2026', 86, 69, 83, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(37, 'Teguh Santosa', 'Q1 2025', 66, 91, 78, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(38, 'Teguh Santosa', 'Q2 2025', 95, 84, 65, 81.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(39, 'Teguh Santosa', 'Q3 2025', 93, 79, 70, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(40, 'Teguh Santosa', 'Q4 2025', 70, 93, 97, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(41, 'Teguh Santosa', 'Q1 2026', 94, 79, 74, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(42, 'Teguh Santosa', 'Q2 2026', 94, 75, 70, 79.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(43, 'Arif Budiman', 'Q1 2025', 88, 82, 81, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(44, 'Arif Budiman', 'Q2 2025', 70, 84, 93, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(45, 'Arif Budiman', 'Q3 2025', 92, 69, 94, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(46, 'Arif Budiman', 'Q4 2025', 87, 66, 79, 77.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(47, 'Arif Budiman', 'Q1 2026', 66, 91, 76, 77.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(48, 'Arif Budiman', 'Q2 2026', 76, 66, 78, 73.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(49, 'Dewi Kusuma', 'Q1 2025', 82, 67, 66, 71.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(50, 'Dewi Kusuma', 'Q2 2025', 78, 91, 65, 78.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(51, 'Dewi Kusuma', 'Q3 2025', 96, 66, 75, 79.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(52, 'Dewi Kusuma', 'Q4 2025', 69, 74, 82, 75.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(53, 'Dewi Kusuma', 'Q1 2026', 72, 71, 87, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(54, 'Dewi Kusuma', 'Q2 2026', 89, 77, 91, 85.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(55, 'Linda Permata', 'Q1 2025', 93, 86, 76, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(56, 'Linda Permata', 'Q2 2025', 89, 74, 91, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(57, 'Linda Permata', 'Q3 2025', 67, 73, 89, 76.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(58, 'Linda Permata', 'Q4 2025', 85, 73, 74, 77.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(59, 'Linda Permata', 'Q1 2026', 76, 91, 99, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(60, 'Linda Permata', 'Q2 2026', 89, 98, 77, 88.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(61, 'Hendra Gunawan', 'Q1 2025', 77, 66, 78, 73.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(62, 'Hendra Gunawan', 'Q2 2025', 70, 84, 69, 74.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(63, 'Hendra Gunawan', 'Q3 2025', 92, 86, 73, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(64, 'Hendra Gunawan', 'Q4 2025', 93, 70, 67, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(65, 'Hendra Gunawan', 'Q1 2026', 98, 70, 92, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(66, 'Hendra Gunawan', 'Q2 2026', 80, 71, 68, 73.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(67, 'Dody Kurniawan', 'Q1 2025', 67, 96, 66, 76.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(68, 'Dody Kurniawan', 'Q2 2025', 70, 65, 100, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(69, 'Dody Kurniawan', 'Q3 2025', 78, 92, 99, 89.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(70, 'Dody Kurniawan', 'Q4 2025', 90, 90, 76, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(71, 'Dody Kurniawan', 'Q1 2026', 83, 94, 83, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(72, 'Dody Kurniawan', 'Q2 2026', 83, 87, 81, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(73, 'Siti Rahayu', 'Q1 2025', 71, 81, 87, 79.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(74, 'Siti Rahayu', 'Q2 2025', 66, 69, 84, 73.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(75, 'Siti Rahayu', 'Q3 2025', 75, 73, 75, 74.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(76, 'Siti Rahayu', 'Q4 2025', 94, 69, 73, 78.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(77, 'Siti Rahayu', 'Q1 2026', 100, 99, 89, 96.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(78, 'Siti Rahayu', 'Q2 2026', 86, 83, 94, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(79, 'Agus Wibowo', 'Q1 2025', 67, 94, 74, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(80, 'Agus Wibowo', 'Q2 2025', 97, 82, 87, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(81, 'Agus Wibowo', 'Q3 2025', 73, 76, 85, 78.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(82, 'Agus Wibowo', 'Q4 2025', 87, 74, 97, 86.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(83, 'Agus Wibowo', 'Q1 2026', 71, 90, 96, 85.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(84, 'Agus Wibowo', 'Q2 2026', 68, 95, 65, 76.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(85, 'Budi Santoso', 'Q1 2025', 66, 94, 96, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(86, 'Budi Santoso', 'Q2 2025', 73, 75, 77, 75.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(87, 'Budi Santoso', 'Q3 2025', 93, 97, 70, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(88, 'Budi Santoso', 'Q4 2025', 66, 92, 69, 75.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(89, 'Budi Santoso', 'Q1 2026', 78, 88, 97, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(90, 'Budi Santoso', 'Q2 2026', 75, 95, 97, 89.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 17:57:06', '2026-07-09 17:57:06');

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
(1, 'APY Rental', 25000000.00, 12000000.00, 13000000.00, '2026-07', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(2, 'APY Rental', 30000000.00, 15000000.00, 15000000.00, '2026-06', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(3, 'APY Rental', 18000000.00, 9000000.00, 9000000.00, '2026-05', '2026-07-09 17:56:57', '2026-07-09 17:56:57');

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
(1, 'LYL001', 'APY Points', 'Poin', '1 poin per 10.000', '100 poin = Rp 50.000', '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'LYL002', 'Free Day Program', 'Hari Gratis', '10 hari rental = 1 hari gratis', '1 hari gratis per periode', '2026-07-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `member`
--

CREATE TABLE `member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pelanggan` varchar(255) DEFAULT NULL,
  `kontak_pelanggan` varchar(255) DEFAULT NULL,
  `email_pelanggan` varchar(255) DEFAULT NULL,
  `jenis_pelanggan` enum('perorangan','perusahaan') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `member`
--

INSERT INTO `member` (`id`, `nama_pelanggan`, `kontak_pelanggan`, `email_pelanggan`, `jenis_pelanggan`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '08879126221', 'budi.santoso@gmail.com', 'perorangan', 'Jl. Wonosobo No. 5', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(2, 'Joko Widodo', '08876588242', 'joko.widodo@gmail.com', 'perorangan', 'Jl. Magelang No. 79', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(3, 'Andi Saputra', '08957804742', 'andi.saputra@gmail.com', 'perorangan', 'Jl. Purworejo No. 51', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(4, 'Rizky Pratama', '08224699187', 'rizky.pratama@gmail.com', 'perorangan', 'Jl. Kebumen No. 41', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(5, 'Dian Permata', '08742216275', 'dian.permata@gmail.com', 'perorangan', 'Jl. Purwokerto No. 17', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(6, 'Siti Rahayu', '08420295923', 'siti.rahayu@gmail.com', 'perorangan', 'Jl. Temanggung No. 46', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(7, 'Ahmad Fauzi', '08554917505', 'ahmad.fauzi@gmail.com', 'perorangan', 'Jl. Kendal No. 19', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(8, 'Dewi Lestari', '08945879536', 'dewi.lestari@gmail.com', 'perorangan', 'Jl. Semarang No. 53', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(9, 'Hendra Gunawan', '08800528372', 'hendra.gunawan@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 67', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(10, 'Rina Wati', '08323314982', 'rina.wati@gmail.com', 'perorangan', 'Jl. Solo No. 93', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(11, 'Bambang Sutrisno', '08894089103', 'bambang.sutrisno@gmail.com', 'perorangan', 'Jl. Wonosobo No. 12', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(12, 'Nia Ramadhani', '08905177440', 'nia.ramadhani@gmail.com', 'perorangan', 'Jl. Magelang No. 40', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(13, 'Ferdy Sambo', '08861610866', 'ferdy.sambo@gmail.com', 'perorangan', 'Jl. Purworejo No. 99', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(14, 'Lina Marlina', '08318147171', 'lina.marlina@gmail.com', 'perorangan', 'Jl. Kebumen No. 78', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(15, 'Tono Suprapto', '08178399437', 'tono.suprapto@gmail.com', 'perorangan', 'Jl. Purwokerto No. 96', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(16, 'Yuli Astuti', '08424377566', 'yuli.astuti@gmail.com', 'perorangan', 'Jl. Temanggung No. 88', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(17, 'Fajar Nugroho', '08525681049', 'fajar.nugroho@gmail.com', 'perorangan', 'Jl. Kendal No. 17', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(18, 'Sri Wahyuni', '08960223959', 'sri.wahyuni@gmail.com', 'perorangan', 'Jl. Semarang No. 43', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(19, 'Rudi Hartono', '08619097452', 'rudi.hartono@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 5', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(20, 'Mega Putri', '08221281210', 'mega.putri@gmail.com', 'perorangan', 'Jl. Solo No. 49', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(21, 'Wahyu Setiawan', '08720170682', 'wahyu.setiawan@gmail.com', 'perorangan', 'Jl. Wonosobo No. 81', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(22, 'Indah Kurniasih', '08709645323', 'indah.kurniasih@gmail.com', 'perorangan', 'Jl. Magelang No. 63', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(23, 'Eko Prasetyo', '08632707804', 'eko.prasetyo@gmail.com', 'perorangan', 'Jl. Purworejo No. 37', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(24, 'Fitri Handayani', '08691342485', 'fitri.handayani@gmail.com', 'perorangan', 'Jl. Kebumen No. 91', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(25, 'Galih Wicaksono', '08924860906', 'galih.wicaksono@gmail.com', 'perorangan', 'Jl. Purwokerto No. 53', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(26, 'PT Maju Bersama', '0265467166', 'ptmajubersama@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 64', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(27, 'CV Sumber Rezeki', '0224352270', 'cvsumberrezeki@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 109', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(28, 'PT Cahaya Abadi', '0291292804', 'ptcahayaabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 49', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(29, 'CV Jaya Mandiri', '0285926986', 'cvjayamandiri@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 10', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(30, 'PT Sukses Selalu', '0258729257', 'ptsuksesselalu@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 147', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(31, 'PT Karya Utama', '0240714091', 'ptkaryautama@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 192', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(32, 'CV Harapan Baru', '0284555271', 'cvharapanbaru@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 163', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(33, 'PT Gemilang Jaya', '0272395016', 'ptgemilangjaya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 94', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(34, 'CV Delta Nusantara', '0256946143', 'cvdeltanusantara@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 108', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(35, 'PT Bintang Timur', '0227468266', 'ptbintangtimur@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 55', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(36, 'PT Nusantara Trans', '0214782110', 'ptnusantaratrans@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 168', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(37, 'CV Permata Hijau', '0288503251', 'cvpermatahijau@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 187', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(38, 'PT Sinar Mas Logistik', '0212146324', 'ptsinarmaslogistik@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 152', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(39, 'CV Berkah Sejati', '0214006258', 'cvberkahsejati@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 146', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(40, 'PT Indo Mitra', '0263699469', 'ptindomitra@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 156', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(41, 'PT Wahana Ekspres', '0236986825', 'ptwahanaekspres@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 152', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(42, 'CV Tirta Agung', '0261451514', 'cvtirtaagung@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 185', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(43, 'PT Mandiri Karya', '0281807175', 'ptmandirikarya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 16', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(44, 'CV Perkasa Utama', '0223490131', 'cvperkasautama@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 1', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(45, 'PT Cipta Rasa', '0246337269', 'ptciptarasa@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 54', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(46, 'PT Lancar Jaya', '0236856117', 'ptlancarjaya@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 132', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(47, 'CV Mitra Usaha', '0255417535', 'cvmitrausaha@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 167', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(48, 'PT Sejahtera Abadi', '0261600592', 'ptsejahteraabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 177', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(49, 'CV Putra Bangsa', '0290241852', 'cvputrabangsa@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 192', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(50, 'PT Global Trans', '0236230666', 'ptglobaltrans@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 74', '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(51, 'Lorem reprehenderit', 'Recusandae Sed repe', 'kyjesile@mailinator.com', 'perorangan', 'Sunt aut tenetur pro', '2026-07-09 18:03:59', '2026-07-09 18:03:59');

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
(1, 'Id impedit sunt co', 'Cum sed dicta maxime', 'kabafud@mailinator.com', 'perorangan', 'Facere veniam facer', '[\"uploads\\/member\\/1783620008_stnk_6a4fe1a811b5a_Daftar-Penawaran-2026-07-09.pdf\"]', '[\"uploads\\/member\\/1783620008_att_6a4fe1a8120f4_Invoice-INV-2026-0001.pdf\",\"uploads\\/member\\/1783620008_att_6a4fe1a812702_Daftar-Penawaran-2026-07-09.pdf\"]', 'uploads/member/1783620008_file_kontrak_Invoice-INV-2026-0001.pdf', '2026-07-09 18:00:08', '2026-07-09 18:00:08');

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
(119, '2026_07_09_000001_rename_member_columns_to_pelanggan', 1),
(120, '2026_07_09_000002_create_induk_assets_table', 1),
(121, '2026_07_09_000002_create_members_table', 1),
(122, '2026_07_09_000003_add_member_id_to_kendaraan_table', 1),
(123, '2026_07_09_000003_create_pergerakan_assets_table', 1),
(124, '2026_07_09_000004_change_file_attachment_to_text_in_members', 1),
(125, '2026_07_09_000004_create_pemeliharaan_assets_table', 1),
(126, '2026_07_09_000005_change_file_stnk_to_text_in_members', 1),
(127, '2026_07_09_000005_create_penyusutan_assets_table', 1),
(128, '2026_07_09_000006_create_perolehan_assets_table', 1),
(129, '2026_07_09_000007_create_asset_dihapuskans_table', 1),
(130, '2026_07_09_000008_create_dokumentasi_assets_table', 1),
(131, '2026_07_09_000009_create_penanggung_jawabs_table', 1),
(132, '2026_07_09_000010_create_audit_assets_table', 1),
(133, '2026_07_09_210636_add_tanggal_bayar_to_kir_table', 1),
(134, '2026_07_09_220001_add_terakhir_diajukan_to_purchaseros_table', 1),
(135, '2026_07_09_220002_add_nominal_to_purchaseros_table', 1),
(136, '2026_07_10_001052_add_referensi_to_bukubesars_table', 1);

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
(1, 'Kantor Pusat Jakarta', '103.12.45.67', 'Online', '500 Mbps', '0 jam/bulan', 'Koneksi utama Indihome Business', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Cabang Surabaya', '202.67.88.12', 'Online', '100 Mbps', '2 jam/bulan', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Gudang Bekasi', '180.244.33.91', 'Warning', '50 Mbps', '5 jam/bulan', 'Sering gangguan sore hari', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Data Center Cibitung', '103.88.12.200', 'Online', '1 Gbps', '0 jam/bulan', 'Tier 3 data center', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Cabang Bandung', '36.91.44.111', 'Offline', '100 Mbps', '8 jam/bulan', 'Sedang dalam perbaikan jalur fiber', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'WF001', 'Welcome Email', 'Registrasi Baru', 'Member baru', 'Kirim Email Selamat Datang', '10 menit', 'Aktif', 'System', 'Auto-email untuk member baru', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'WF002', 'Reminder Pembayaran', 'H-2 Jatuh Tempo', 'Belum bayar', 'Kirim Notifikasi WA', 'Langsung', 'Aktif', 'Finance', 'Pengingat otomatis pembayaran', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
(1, 1, 'Pajak Tahunan', 3600000.00, '2026-06-30', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(2, 2, 'Pajak 5 Tahunan', 800000.00, '2026-12-24', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(3, 3, 'STNK', 4600000.00, '2027-03-12', '2026-06-19', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(4, 4, 'BPKB', 2100000.00, '2026-09-19', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(5, 5, 'BBN-KB', 900000.00, '2027-05-04', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(6, 6, 'Pajak Tahunan', 2200000.00, '2026-08-17', '2026-06-23', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(7, 7, 'Pajak 5 Tahunan', 1700000.00, '2026-12-06', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(8, 8, 'STNK', 1800000.00, '2026-09-11', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(9, 9, 'BPKB', 1200000.00, '2027-03-06', '2026-07-06', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(10, 10, 'BBN-KB', 2300000.00, '2027-04-25', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(11, 11, 'Pajak Tahunan', 2300000.00, '2027-05-25', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(12, 12, 'Pajak 5 Tahunan', 3500000.00, '2027-01-04', '2026-06-10', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(13, 13, 'STNK', 1300000.00, '2027-05-04', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(14, 14, 'BPKB', 2700000.00, '2026-06-21', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(15, 15, 'BBN-KB', 800000.00, '2027-02-23', '2026-06-10', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(16, 16, 'Pajak Tahunan', 4200000.00, '2026-07-03', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(17, 17, 'Pajak 5 Tahunan', 4700000.00, '2026-09-17', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(18, 18, 'STNK', 4900000.00, '2026-10-03', '2026-06-18', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(19, 19, 'BPKB', 3300000.00, '2027-03-08', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(20, 20, 'BBN-KB', 3800000.00, '2027-03-31', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(21, 21, 'Pajak Tahunan', 2000000.00, '2027-02-27', '2026-06-15', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(22, 22, 'Pajak 5 Tahunan', 5300000.00, '2026-12-30', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(23, 23, 'STNK', 5200000.00, '2027-04-01', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(24, 24, 'BPKB', 5200000.00, '2026-09-28', '2026-06-13', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(25, 25, 'BBN-KB', 2400000.00, '2027-03-14', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(26, 26, 'Pajak Tahunan', 4500000.00, '2026-09-10', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(27, 27, 'Pajak 5 Tahunan', 2900000.00, '2027-03-21', '2026-06-15', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(28, 28, 'STNK', 4500000.00, '2027-04-25', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(29, 29, 'BPKB', 5100000.00, '2026-08-24', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(30, 30, 'BBN-KB', 2100000.00, '2027-06-08', '2026-06-24', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(31, 31, 'Pajak Tahunan', 2800000.00, '2026-11-04', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(32, 32, 'Pajak 5 Tahunan', 900000.00, '2027-02-25', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(33, 33, 'STNK', 5600000.00, '2027-02-13', '2026-06-27', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(34, 34, 'BPKB', 3000000.00, '2026-07-26', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(35, 35, 'BBN-KB', 1500000.00, '2026-11-10', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(36, 36, 'Pajak Tahunan', 5000000.00, '2026-07-01', '2026-07-09', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(37, 37, 'Pajak 5 Tahunan', 2600000.00, '2027-06-25', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(38, 38, 'STNK', 3000000.00, '2026-07-21', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(39, 39, 'BPKB', 3200000.00, '2027-05-23', '2026-07-07', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(40, 40, 'BBN-KB', 3800000.00, '2026-08-03', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(41, 41, 'Pajak Tahunan', 1800000.00, '2026-12-20', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(42, 42, 'Pajak 5 Tahunan', 5000000.00, '2026-11-16', '2026-06-28', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(43, 43, 'STNK', 2900000.00, '2027-04-21', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(44, 44, 'BPKB', 2300000.00, '2026-10-21', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(45, 45, 'BBN-KB', 5600000.00, '2026-10-02', '2026-06-24', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(46, 46, 'Pajak Tahunan', 900000.00, '2027-02-12', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(47, 47, 'Pajak 5 Tahunan', 2000000.00, '2026-07-13', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(48, 48, 'STNK', 600000.00, '2026-10-07', '2026-07-06', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(49, 49, 'BPKB', 3500000.00, '2027-03-13', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(50, 50, 'BBN-KB', 5500000.00, '2026-06-24', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55');

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
(1, 'Budi Santoso', 25000000.00, 5000000.00, 25000000.00, 500000.00, 2500000.00, 27000000.00, NULL, '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(2, 'Siti Rahayu', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(3, 'Agus Wibowo', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(4, 'Dewi Kusuma', 12000000.00, 2000000.00, 12000000.00, 240000.00, 600000.00, 13160000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(5, 'Rini Apriani', 6000000.00, 1000000.00, 6000000.00, 120000.00, 150000.00, 6730000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(6, 'Eko Prasetyo', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(7, 'Hendra Gunawan', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(8, 'Rizky Fadillah', 7000000.00, 1200000.00, 7000000.00, 140000.00, 200000.00, 7860000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(9, 'Yusuf Hidayat', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(10, 'Linda Permata', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(11, 'Wahyu Nugroho', 7500000.00, 1200000.00, 7500000.00, 150000.00, 220000.00, 8330000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(12, 'Fitri Handayani', 6500000.00, 1000000.00, 6500000.00, 130000.00, 170000.00, 7200000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(13, 'Dody Kurniawan', 11000000.00, 2000000.00, 11000000.00, 220000.00, 550000.00, 12230000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(14, 'Teguh Santosa', 8000000.00, 1500000.00, 8000000.00, 160000.00, 280000.00, 9060000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(15, 'Arif Budiman', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-09 17:57:06', '2026-07-09 17:57:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembelian_proyeks`
--

CREATE TABLE `pembelian_proyeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pr_no` varchar(255) NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `item_diminta` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 0,
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
(1, 'PR-PRJ001-001', 'PRJ001', 'Semen Portland 40kg', 500, 'PT Semen Indonesia', 20000000, 'Disetujui', '2026-01-08', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'PR-PRJ001-002', 'PRJ001', 'Besi Beton 10mm', 200, 'PT Krakatau Steel', 35000000, 'Disetujui', '2026-01-10', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'PR-PRJ001-003', 'PRJ001', 'Bata Merah 20x10x5', 5000, 'CV Bata Kuat', 10000000, 'Disetujui', '2026-01-12', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'PR-PRJ001-004', 'PRJ001', 'Cat Tembok & Finishing', 50, 'PT Nippon Paint', 15000000, 'Pending', '2026-02-20', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'PR-PRJ002-001', 'PRJ002', 'Bus Pariwisata 32 Seat', 3, 'PT Hino Motors', 1200000000, 'Disetujui', '2026-02-05', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(6, 'PR-PRJ002-002', 'PRJ002', 'Wrapping & Branding Bus', 3, 'CV Kreatif Visual', 15000000, 'Pending', '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(7, 'PR-PRJ003-001', 'PRJ003', 'Unit GPS Tracker', 50, 'PT TechMaps', 100000000, 'Disetujui', '2026-01-14', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(8, 'PR-PRJ003-002', 'PRJ003', 'Server Dashboard Cloud', 1, 'PT AWS Indonesia', 24000000, 'Disetujui', '2026-01-15', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
  `jumlah` int(11) NOT NULL,
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
(1, 'QUO-2026-001', '2026-01-15', 'PT Maju Bersama', 'Sewa Minibus', 2, 5000000.00, 10000000.00, 'Disetujui', '2026-02-15', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(2, 'QUO-2026-002', '2026-01-20', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 8000000.00, 'Terkirim', '2026-02-20', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(3, 'QUO-2026-003', '2026-02-01', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 3500000.00, 10500000.00, 'Draft', '2026-03-01', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(4, 'QUO-2026-004', '2026-02-10', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 15000000.00, 'Disetujui', '2026-03-10', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(5, 'QUO-2026-005', '2026-02-25', 'CV Jaya Mandiri', 'Sewa MPV', 4, 4000000.00, 16000000.00, 'Terkirim', '2026-03-25', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(6, 'QUO-2026-006', '2026-03-05', 'PT Nusantara Raya', 'Sewa Minibus', 2, 5500000.00, 11000000.00, 'Disetujui', '2026-04-05', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(7, 'QUO-2026-007', '2026-03-15', 'PT Sinar Harapan', 'Sewa SUV', 2, 6000000.00, 12000000.00, 'Ditolak', '2026-04-15', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(8, 'QUO-2026-008', '2026-04-01', 'CV Mitra Logistik', 'Sewa Truk', 3, 7500000.00, 22500000.00, 'Terkirim', '2026-05-01', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(9, 'QUO-2026-009', '2026-04-20', 'PT Berlian Trans', 'Sewa Bus Medium', 2, 10000000.00, 20000000.00, 'Disetujui', '2026-05-20', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(10, 'QUO-2026-010', '2026-05-10', 'PT Prima Raya', 'Sewa Sedan', 5, 3000000.00, 15000000.00, 'Draft', '2026-06-10', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(11, 'QUO-001', '2026-05-08', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 9, 2790044.00, 25110396.00, 'Draft', '2026-06-02', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(12, 'QUO-002', '2026-04-07', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 4, 3086953.00, 12347812.00, 'Terkirim', '2026-05-26', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(13, 'QUO-003', '2026-02-08', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 10, 4611174.00, 46111740.00, 'Disetujui', '2026-04-03', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(14, 'QUO-004', '2026-03-08', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 6, 560464.00, 3362784.00, 'Ditolak', '2026-04-12', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(15, 'QUO-005', '2026-04-22', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 6, 3830282.00, 22981692.00, 'Draft', '2026-06-21', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(16, 'QUO-006', '2026-04-13', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 3, 1940092.00, 5820276.00, 'Terkirim', '2026-05-02', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(17, 'QUO-007', '2026-02-20', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 2, 1236214.00, 2472428.00, 'Disetujui', '2026-03-24', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(18, 'QUO-008', '2026-02-23', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 7, 2788044.00, 19516308.00, 'Ditolak', '2026-04-14', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(19, 'QUO-009', '2026-05-09', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 6, 4818386.00, 28910316.00, 'Draft', '2026-06-21', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(20, 'QUO-010', '2026-05-10', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 8, 2974944.00, 23799552.00, 'Terkirim', '2026-06-18', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(21, 'QUO-011', '2026-05-07', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 3, 4354641.00, 13063923.00, 'Disetujui', '2026-06-25', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(22, 'QUO-012', '2026-03-19', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 1, 4385031.00, 4385031.00, 'Ditolak', '2026-04-20', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(23, 'QUO-013', '2026-06-22', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 1, 2551635.00, 2551635.00, 'Draft', '2026-07-30', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(24, 'QUO-014', '2026-05-24', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 6, 2134361.00, 12806166.00, 'Terkirim', '2026-07-16', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(25, 'QUO-015', '2026-03-19', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 4, 4554564.00, 18218256.00, 'Disetujui', '2026-04-21', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(26, 'QUO-016', '2026-04-18', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 6, 1022634.00, 6135804.00, 'Ditolak', '2026-05-03', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(27, 'QUO-017', '2026-03-14', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 6, 1554852.00, 9329112.00, 'Draft', '2026-03-30', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(28, 'QUO-018', '2026-02-13', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 3, 4294673.00, 12884019.00, 'Terkirim', '2026-03-04', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(29, 'QUO-019', '2026-05-20', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 4, 3633368.00, 14533472.00, 'Disetujui', '2026-07-16', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(30, 'QUO-020', '2026-04-04', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 7, 1667236.00, 11670652.00, 'Ditolak', '2026-04-26', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(31, 'QUO-021', '2026-03-24', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 10, 4529408.00, 45294080.00, 'Draft', '2026-04-21', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(32, 'QUO-022', '2026-03-13', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 4, 3788156.00, 15152624.00, 'Terkirim', '2026-03-29', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(33, 'QUO-023', '2026-01-23', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 8, 2891005.00, 23128040.00, 'Disetujui', '2026-03-11', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(34, 'QUO-024', '2026-02-05', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 3, 2016425.00, 6049275.00, 'Ditolak', '2026-03-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(35, 'QUO-025', '2026-02-05', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 7, 4342073.00, 30394511.00, 'Draft', '2026-03-03', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(36, 'QUO-026', '2026-04-09', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 9, 4044031.00, 36396279.00, 'Terkirim', '2026-06-05', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(37, 'QUO-027', '2026-05-08', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 1, 905680.00, 905680.00, 'Disetujui', '2026-06-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(38, 'QUO-028', '2026-04-25', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 2, 2388707.00, 4777414.00, 'Ditolak', '2026-06-08', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(39, 'QUO-029', '2026-06-10', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 5, 4435987.00, 22179935.00, 'Draft', '2026-06-24', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(40, 'QUO-030', '2026-01-18', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 8, 1363676.00, 10909408.00, 'Terkirim', '2026-02-06', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'Kebijakan Keamanan Informasi', 'v2.1', '2024-01-01', 'IT Manager', 'Aktif', 'ISO 27001', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Prosedur Backup & Recovery', 'v1.3', '2024-03-01', 'System Administrator', 'Aktif', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Kebijakan Penggunaan Aset IT', 'v1.0', '2024-06-01', 'HR & IT Manager', 'Draft', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Disaster Recovery Plan', 'v3.0', '2023-07-01', 'CTO', 'Review', 'ISO 22301', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Kebijakan Password & Akses', 'v2.0', '2024-01-01', 'IT Security Officer', 'Aktif', 'ISO 27001', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'Budi Santoso', '2026-06-11', '09:27:00', '17:44:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Dewi Kusuma', '2026-06-11', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Rini Apriani', '2026-06-11', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Eko Prasetyo', '2026-06-11', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Hendra Gunawan', '2026-06-11', '07:46:00', '17:01:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 'Rizky Fadillah', '2026-06-11', '08:25:00', '18:57:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 'Yusuf Hidayat', '2026-06-11', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(8, 'Linda Permata', '2026-06-11', '09:18:00', '18:00:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(9, 'Wahyu Nugroho', '2026-06-11', '08:58:00', '18:47:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(10, 'Fitri Handayani', '2026-06-11', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(11, 'Dody Kurniawan', '2026-06-11', '08:51:00', '18:10:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(12, 'Teguh Santosa', '2026-06-11', '08:35:00', '17:19:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(13, 'Budi Santoso', '2026-06-12', '09:55:00', '18:00:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(14, 'Dewi Kusuma', '2026-06-12', '07:07:00', '18:52:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(15, 'Rini Apriani', '2026-06-12', '08:14:00', '18:34:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(16, 'Eko Prasetyo', '2026-06-12', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(17, 'Hendra Gunawan', '2026-06-12', '08:40:00', '18:06:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(18, 'Rizky Fadillah', '2026-06-12', '09:21:00', '18:09:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(19, 'Yusuf Hidayat', '2026-06-12', '07:57:00', '17:20:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(20, 'Linda Permata', '2026-06-12', '08:12:00', '18:18:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(21, 'Wahyu Nugroho', '2026-06-12', '08:16:00', '17:19:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(22, 'Fitri Handayani', '2026-06-12', '08:20:00', '17:54:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(23, 'Dody Kurniawan', '2026-06-12', '07:39:00', '17:56:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(24, 'Teguh Santosa', '2026-06-12', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(25, 'Budi Santoso', '2026-06-15', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(26, 'Dewi Kusuma', '2026-06-15', '07:26:00', '17:22:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(27, 'Rini Apriani', '2026-06-15', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(28, 'Eko Prasetyo', '2026-06-15', '08:48:00', '17:35:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(29, 'Hendra Gunawan', '2026-06-15', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(30, 'Rizky Fadillah', '2026-06-15', '08:54:00', '17:59:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(31, 'Yusuf Hidayat', '2026-06-15', '08:49:00', '17:47:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(32, 'Linda Permata', '2026-06-15', '07:08:00', '17:25:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(33, 'Wahyu Nugroho', '2026-06-15', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(34, 'Fitri Handayani', '2026-06-15', '08:10:00', '17:01:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(35, 'Dody Kurniawan', '2026-06-15', '08:22:00', '17:43:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(36, 'Teguh Santosa', '2026-06-15', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(37, 'Budi Santoso', '2026-06-16', '07:04:00', '17:52:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(38, 'Dewi Kusuma', '2026-06-16', '07:07:00', '18:29:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(39, 'Rini Apriani', '2026-06-16', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(40, 'Eko Prasetyo', '2026-06-16', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(41, 'Hendra Gunawan', '2026-06-16', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(42, 'Rizky Fadillah', '2026-06-16', '08:20:00', '18:53:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(43, 'Yusuf Hidayat', '2026-06-16', '07:52:00', '18:28:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(44, 'Linda Permata', '2026-06-16', '07:01:00', '18:24:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(45, 'Wahyu Nugroho', '2026-06-16', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(46, 'Fitri Handayani', '2026-06-16', '07:37:00', '17:01:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(47, 'Dody Kurniawan', '2026-06-16', '08:24:00', '18:08:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(48, 'Teguh Santosa', '2026-06-16', '08:39:00', '18:48:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(49, 'Budi Santoso', '2026-06-17', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(50, 'Dewi Kusuma', '2026-06-17', '07:30:00', '17:30:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(51, 'Rini Apriani', '2026-06-17', '08:45:00', '18:17:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(52, 'Eko Prasetyo', '2026-06-17', '08:56:00', '18:11:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(53, 'Hendra Gunawan', '2026-06-17', '08:28:00', '17:45:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(54, 'Rizky Fadillah', '2026-06-17', '08:27:00', '18:30:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(55, 'Yusuf Hidayat', '2026-06-17', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(56, 'Linda Permata', '2026-06-17', '07:41:00', '18:28:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(57, 'Wahyu Nugroho', '2026-06-17', '07:09:00', '17:14:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(58, 'Fitri Handayani', '2026-06-17', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(59, 'Dody Kurniawan', '2026-06-17', '08:11:00', '18:23:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(60, 'Teguh Santosa', '2026-06-17', '09:31:00', '17:18:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(61, 'Budi Santoso', '2026-06-18', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(62, 'Dewi Kusuma', '2026-06-18', '08:27:00', '17:01:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(63, 'Rini Apriani', '2026-06-18', '07:53:00', '18:13:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(64, 'Eko Prasetyo', '2026-06-18', '07:09:00', '17:12:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(65, 'Hendra Gunawan', '2026-06-18', '08:19:00', '17:48:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(66, 'Rizky Fadillah', '2026-06-18', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(67, 'Yusuf Hidayat', '2026-06-18', '08:48:00', '18:29:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(68, 'Linda Permata', '2026-06-18', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(69, 'Wahyu Nugroho', '2026-06-18', '07:49:00', '18:21:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(70, 'Fitri Handayani', '2026-06-18', '08:54:00', '18:28:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(71, 'Dody Kurniawan', '2026-06-18', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(72, 'Teguh Santosa', '2026-06-18', '08:34:00', '17:21:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(73, 'Budi Santoso', '2026-06-19', '07:08:00', '18:33:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(74, 'Dewi Kusuma', '2026-06-19', '07:28:00', '17:00:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(75, 'Rini Apriani', '2026-06-19', '08:06:00', '17:38:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(76, 'Eko Prasetyo', '2026-06-19', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(77, 'Hendra Gunawan', '2026-06-19', '08:56:00', '17:19:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(78, 'Rizky Fadillah', '2026-06-19', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(79, 'Yusuf Hidayat', '2026-06-19', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(80, 'Linda Permata', '2026-06-19', '08:17:00', '17:19:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(81, 'Wahyu Nugroho', '2026-06-19', '08:35:00', '17:20:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(82, 'Fitri Handayani', '2026-06-19', '09:15:00', '18:41:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(83, 'Dody Kurniawan', '2026-06-19', '09:32:00', '17:45:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(84, 'Teguh Santosa', '2026-06-19', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(85, 'Budi Santoso', '2026-06-22', '09:31:00', '17:41:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(86, 'Dewi Kusuma', '2026-06-22', '09:15:00', '18:46:00', 'Face ID', 'WFH', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(87, 'Rini Apriani', '2026-06-22', '08:55:00', '17:32:00', 'Face ID', 'WFH', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(88, 'Eko Prasetyo', '2026-06-22', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(89, 'Hendra Gunawan', '2026-06-22', '07:13:00', '17:08:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(90, 'Rizky Fadillah', '2026-06-22', '09:30:00', '17:02:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(91, 'Yusuf Hidayat', '2026-06-22', '08:30:00', '17:44:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(92, 'Linda Permata', '2026-06-22', '07:50:00', '18:50:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(93, 'Wahyu Nugroho', '2026-06-22', '08:05:00', '17:53:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(94, 'Fitri Handayani', '2026-06-22', '09:23:00', '18:48:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(95, 'Dody Kurniawan', '2026-06-22', '08:17:00', '18:13:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(96, 'Teguh Santosa', '2026-06-22', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(97, 'Budi Santoso', '2026-06-23', '08:47:00', '18:25:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(98, 'Dewi Kusuma', '2026-06-23', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(99, 'Rini Apriani', '2026-06-23', '08:51:00', '18:19:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(100, 'Eko Prasetyo', '2026-06-23', '07:51:00', '18:12:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(101, 'Hendra Gunawan', '2026-06-23', '07:50:00', '17:39:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(102, 'Rizky Fadillah', '2026-06-23', '07:18:00', '18:48:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(103, 'Yusuf Hidayat', '2026-06-23', '07:16:00', '18:01:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(104, 'Linda Permata', '2026-06-23', '07:32:00', '17:17:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(105, 'Wahyu Nugroho', '2026-06-23', '07:29:00', '18:06:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(106, 'Fitri Handayani', '2026-06-23', '07:51:00', '18:11:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(107, 'Dody Kurniawan', '2026-06-23', '08:13:00', '18:51:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(108, 'Teguh Santosa', '2026-06-23', '07:59:00', '17:26:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(109, 'Budi Santoso', '2026-06-24', '08:35:00', '17:52:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:01', '2026-07-09 17:57:01'),
(110, 'Dewi Kusuma', '2026-06-24', '07:48:00', '17:31:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(111, 'Rini Apriani', '2026-06-24', '07:37:00', '18:14:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(112, 'Eko Prasetyo', '2026-06-24', '07:35:00', '17:47:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(113, 'Hendra Gunawan', '2026-06-24', '09:55:00', '18:39:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(114, 'Rizky Fadillah', '2026-06-24', '08:30:00', '17:43:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(115, 'Yusuf Hidayat', '2026-06-24', '07:01:00', '17:01:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(116, 'Linda Permata', '2026-06-24', '08:52:00', '18:55:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(117, 'Wahyu Nugroho', '2026-06-24', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(118, 'Fitri Handayani', '2026-06-24', '07:36:00', '18:52:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(119, 'Dody Kurniawan', '2026-06-24', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(120, 'Teguh Santosa', '2026-06-24', '08:10:00', '18:52:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(121, 'Budi Santoso', '2026-06-25', '08:47:00', '17:07:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(122, 'Dewi Kusuma', '2026-06-25', '08:15:00', '17:41:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(123, 'Rini Apriani', '2026-06-25', '07:48:00', '17:01:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(124, 'Eko Prasetyo', '2026-06-25', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(125, 'Hendra Gunawan', '2026-06-25', '08:31:00', '18:16:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(126, 'Rizky Fadillah', '2026-06-25', '07:53:00', '18:55:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(127, 'Yusuf Hidayat', '2026-06-25', '07:37:00', '17:27:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(128, 'Linda Permata', '2026-06-25', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(129, 'Wahyu Nugroho', '2026-06-25', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(130, 'Fitri Handayani', '2026-06-25', '08:20:00', '18:56:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(131, 'Dody Kurniawan', '2026-06-25', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(132, 'Teguh Santosa', '2026-06-25', '09:27:00', '17:19:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(133, 'Budi Santoso', '2026-06-26', '08:05:00', '18:38:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(134, 'Dewi Kusuma', '2026-06-26', '07:40:00', '17:02:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(135, 'Rini Apriani', '2026-06-26', '08:34:00', '18:50:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(136, 'Eko Prasetyo', '2026-06-26', '08:54:00', '18:46:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(137, 'Hendra Gunawan', '2026-06-26', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(138, 'Rizky Fadillah', '2026-06-26', '08:04:00', '18:53:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(139, 'Yusuf Hidayat', '2026-06-26', '07:32:00', '18:45:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(140, 'Linda Permata', '2026-06-26', '07:48:00', '18:43:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(141, 'Wahyu Nugroho', '2026-06-26', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:02', '2026-07-09 17:57:02'),
(142, 'Fitri Handayani', '2026-06-26', '07:20:00', '18:27:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(143, 'Dody Kurniawan', '2026-06-26', '09:30:00', '18:54:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(144, 'Teguh Santosa', '2026-06-26', '08:42:00', '18:44:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(145, 'Budi Santoso', '2026-06-29', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(146, 'Dewi Kusuma', '2026-06-29', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(147, 'Rini Apriani', '2026-06-29', '07:55:00', '18:12:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(148, 'Eko Prasetyo', '2026-06-29', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(149, 'Hendra Gunawan', '2026-06-29', '07:48:00', '17:45:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(150, 'Rizky Fadillah', '2026-06-29', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(151, 'Yusuf Hidayat', '2026-06-29', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(152, 'Linda Permata', '2026-06-29', '08:02:00', '18:12:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(153, 'Wahyu Nugroho', '2026-06-29', '08:52:00', '17:57:00', 'Face ID', 'WFH', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(154, 'Fitri Handayani', '2026-06-29', '07:14:00', '18:33:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(155, 'Dody Kurniawan', '2026-06-29', '08:57:00', '18:47:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(156, 'Teguh Santosa', '2026-06-29', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(157, 'Budi Santoso', '2026-06-30', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(158, 'Dewi Kusuma', '2026-06-30', '07:19:00', '17:18:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(159, 'Rini Apriani', '2026-06-30', '08:57:00', '17:30:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(160, 'Eko Prasetyo', '2026-06-30', '08:51:00', '17:59:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(161, 'Hendra Gunawan', '2026-06-30', '07:46:00', '18:19:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(162, 'Rizky Fadillah', '2026-06-30', '08:07:00', '18:51:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(163, 'Yusuf Hidayat', '2026-06-30', '07:24:00', '18:27:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(164, 'Linda Permata', '2026-06-30', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(165, 'Wahyu Nugroho', '2026-06-30', '07:35:00', '18:49:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(166, 'Fitri Handayani', '2026-06-30', '08:44:00', '18:05:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(167, 'Dody Kurniawan', '2026-06-30', '09:10:00', '17:33:00', 'GPS', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(168, 'Teguh Santosa', '2026-06-30', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(169, 'Budi Santoso', '2026-07-01', '07:32:00', '18:03:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(170, 'Dewi Kusuma', '2026-07-01', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(171, 'Rini Apriani', '2026-07-01', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(172, 'Eko Prasetyo', '2026-07-01', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(173, 'Hendra Gunawan', '2026-07-01', '08:36:00', '17:15:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(174, 'Rizky Fadillah', '2026-07-01', '09:45:00', '17:54:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(175, 'Yusuf Hidayat', '2026-07-01', '07:52:00', '17:32:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(176, 'Linda Permata', '2026-07-01', '08:02:00', '17:21:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(177, 'Wahyu Nugroho', '2026-07-01', '08:57:00', '18:50:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(178, 'Fitri Handayani', '2026-07-01', '07:24:00', '18:25:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(179, 'Dody Kurniawan', '2026-07-01', '09:10:00', '17:51:00', 'GPS', 'WFH', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(180, 'Teguh Santosa', '2026-07-01', '08:39:00', '17:35:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(181, 'Budi Santoso', '2026-07-02', '07:51:00', '18:06:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(182, 'Dewi Kusuma', '2026-07-02', '08:26:00', '17:09:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(183, 'Rini Apriani', '2026-07-02', '08:55:00', '18:12:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(184, 'Eko Prasetyo', '2026-07-02', '07:40:00', '18:32:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(185, 'Hendra Gunawan', '2026-07-02', '08:46:00', '18:56:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(186, 'Rizky Fadillah', '2026-07-02', '08:29:00', '18:26:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(187, 'Yusuf Hidayat', '2026-07-02', '08:56:00', '17:02:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(188, 'Linda Permata', '2026-07-02', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(189, 'Wahyu Nugroho', '2026-07-02', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(190, 'Fitri Handayani', '2026-07-02', '07:51:00', '18:43:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(191, 'Dody Kurniawan', '2026-07-02', '07:19:00', '17:10:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(192, 'Teguh Santosa', '2026-07-02', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(193, 'Budi Santoso', '2026-07-03', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(194, 'Dewi Kusuma', '2026-07-03', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(195, 'Rini Apriani', '2026-07-03', '09:29:00', '18:44:00', 'Manual', 'WFH', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(196, 'Eko Prasetyo', '2026-07-03', '08:34:00', '18:12:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(197, 'Hendra Gunawan', '2026-07-03', '08:49:00', '17:13:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(198, 'Rizky Fadillah', '2026-07-03', '09:31:00', '17:33:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(199, 'Yusuf Hidayat', '2026-07-03', '08:00:00', '17:17:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(200, 'Linda Permata', '2026-07-03', '08:13:00', '18:33:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(201, 'Wahyu Nugroho', '2026-07-03', '07:19:00', '17:31:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(202, 'Fitri Handayani', '2026-07-03', '08:35:00', '18:56:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(203, 'Dody Kurniawan', '2026-07-03', '09:19:00', '18:40:00', 'GPS', 'WFH', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(204, 'Teguh Santosa', '2026-07-03', '08:14:00', '17:46:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(205, 'Budi Santoso', '2026-07-06', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(206, 'Dewi Kusuma', '2026-07-06', '08:57:00', '17:26:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(207, 'Rini Apriani', '2026-07-06', '09:15:00', '18:19:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(208, 'Eko Prasetyo', '2026-07-06', '08:30:00', '18:35:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(209, 'Hendra Gunawan', '2026-07-06', '07:05:00', '17:19:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(210, 'Rizky Fadillah', '2026-07-06', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(211, 'Yusuf Hidayat', '2026-07-06', '07:53:00', '18:47:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(212, 'Linda Permata', '2026-07-06', '08:07:00', '18:26:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(213, 'Wahyu Nugroho', '2026-07-06', '07:55:00', '17:43:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(214, 'Fitri Handayani', '2026-07-06', '09:36:00', '18:46:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(215, 'Dody Kurniawan', '2026-07-06', '07:43:00', '17:36:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(216, 'Teguh Santosa', '2026-07-06', '07:35:00', '17:12:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(217, 'Budi Santoso', '2026-07-07', '07:38:00', '17:52:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(218, 'Dewi Kusuma', '2026-07-07', '08:26:00', '18:15:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(219, 'Rini Apriani', '2026-07-07', '08:35:00', '18:36:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(220, 'Eko Prasetyo', '2026-07-07', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(221, 'Hendra Gunawan', '2026-07-07', '07:40:00', '17:33:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:03', '2026-07-09 17:57:03'),
(222, 'Rizky Fadillah', '2026-07-07', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(223, 'Yusuf Hidayat', '2026-07-07', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(224, 'Linda Permata', '2026-07-07', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(225, 'Wahyu Nugroho', '2026-07-07', '08:43:00', '17:21:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(226, 'Fitri Handayani', '2026-07-07', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(227, 'Dody Kurniawan', '2026-07-07', '09:50:00', '18:21:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(228, 'Teguh Santosa', '2026-07-07', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(229, 'Budi Santoso', '2026-07-08', '08:06:00', '18:59:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(230, 'Dewi Kusuma', '2026-07-08', '08:42:00', '17:15:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(231, 'Rini Apriani', '2026-07-08', '07:11:00', '17:22:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(232, 'Eko Prasetyo', '2026-07-08', '07:53:00', '18:14:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(233, 'Hendra Gunawan', '2026-07-08', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(234, 'Rizky Fadillah', '2026-07-08', '08:46:00', '18:09:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(235, 'Yusuf Hidayat', '2026-07-08', '08:27:00', '17:51:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(236, 'Linda Permata', '2026-07-08', '08:27:00', '17:09:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(237, 'Wahyu Nugroho', '2026-07-08', '08:11:00', '18:16:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(238, 'Fitri Handayani', '2026-07-08', '07:26:00', '17:30:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(239, 'Dody Kurniawan', '2026-07-08', '08:12:00', '18:44:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(240, 'Teguh Santosa', '2026-07-08', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(241, 'Budi Santoso', '2026-07-09', '07:36:00', '18:50:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(242, 'Dewi Kusuma', '2026-07-09', '08:31:00', '18:40:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(243, 'Rini Apriani', '2026-07-09', '08:47:00', '18:45:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(244, 'Eko Prasetyo', '2026-07-09', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(245, 'Hendra Gunawan', '2026-07-09', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(246, 'Rizky Fadillah', '2026-07-09', '09:53:00', '17:56:00', 'Manual', 'WFH', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(247, 'Yusuf Hidayat', '2026-07-09', '07:12:00', '17:34:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(248, 'Linda Permata', '2026-07-09', '07:00:00', '17:54:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(249, 'Wahyu Nugroho', '2026-07-09', '08:35:00', '18:57:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(250, 'Fitri Handayani', '2026-07-09', '07:09:00', '17:01:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(251, 'Dody Kurniawan', '2026-07-09', '08:18:00', '18:08:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(252, 'Teguh Santosa', '2026-07-09', '07:46:00', '17:25:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(253, 'Budi Santoso', '2026-07-10', '08:26:00', '18:00:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(254, 'Dewi Kusuma', '2026-07-10', '08:32:00', '17:26:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(255, 'Rini Apriani', '2026-07-10', '07:32:00', '18:43:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(256, 'Eko Prasetyo', '2026-07-10', '07:19:00', '17:31:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(257, 'Hendra Gunawan', '2026-07-10', '08:31:00', '17:51:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(258, 'Rizky Fadillah', '2026-07-10', '08:01:00', '17:15:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(259, 'Yusuf Hidayat', '2026-07-10', '09:29:00', '17:47:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(260, 'Linda Permata', '2026-07-10', '08:05:00', '18:58:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:04', '2026-07-09 17:57:04'),
(261, 'Wahyu Nugroho', '2026-07-10', '08:01:00', '17:55:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(262, 'Fitri Handayani', '2026-07-10', '07:33:00', '18:13:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(263, 'Dody Kurniawan', '2026-07-10', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(264, 'Teguh Santosa', '2026-07-10', '07:57:00', '17:28:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 17:57:05', '2026-07-09 17:57:05');

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
(1, 'PRC-001', 'Sewa Sedan', 'Regular', 3500000.00, 0.00, 3500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'PRC-002', 'Sewa Sedan', 'Silver', 3500000.00, 5.00, 3325000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(3, 'PRC-003', 'Sewa Sedan', 'Gold', 3500000.00, 10.00, 3150000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(4, 'PRC-004', 'Sewa Minibus', 'Regular', 5000000.00, 0.00, 5000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(5, 'PRC-005', 'Sewa Minibus', 'Gold', 5000000.00, 10.00, 4500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(6, 'PRC-006', 'Sewa Minibus', 'Platinum', 5000000.00, 15.00, 4250000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(7, 'PRC-007', 'Sewa Truk', 'Regular', 8000000.00, 0.00, 8000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(8, 'PRC-008', 'Sewa Truk', 'Platinum', 8000000.00, 12.00, 7040000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(9, 'PRC-009', 'Sewa Bus Besar', 'Regular', 15000000.00, 0.00, 15000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(10, 'PRC-010', 'Sewa Bus Besar', 'Gold', 15000000.00, 8.00, 13800000.00, '2026-01-01', '2026-12-31', 'Tidak Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
(1, 'WF001', 'Persetujuan Pengadaan Barang', 'Pengajuan Barang', 'Nominal > 5.000.000', 'Kirim Email ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Workflow approval pengadaan barang.', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(2, 'WF002', 'Approval Vendor Baru', 'Penambahan Vendor Baru', NULL, 'Kirim Notifikasi ke Admin', '30 Menit', 'Aktif', 'Admin Procurement', 'Workflow untuk approval vendor.', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(3, 'WF003', 'Review Purchase Request', 'PR Diajukan', 'Qty > 100 pcs', 'Kirim ke Manajer Gudang', '2 Jam', 'Aktif', 'Manajer Gudang', 'Workflow review permintaan barang dari gudang.', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(4, 'WF004', 'Approval Kontrak Vendor', 'Kontrak Baru Dibuat', 'Nilai Kontrak > 50.000.000', 'Kirim Email ke Direktur', '1 Hari', 'Aktif', 'Legal & Finance', 'Persetujuan kontrak vendor bernilai besar.', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(5, 'WF005', 'Notifikasi Stok Menipis', 'Stok < Minimum', NULL, 'Kirim Alert ke Procurement', 'Langsung', 'Aktif', 'Procurement', 'Otomatis kirim notifikasi saat stok mendekati batas minimum.', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(6, 'WF006', 'Evaluasi Vendor Periodik', 'Akhir Bulan', 'Rating < 3', 'Kirim Laporan ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Evaluasi performa vendor setiap bulan.', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(7, 'WF007', 'Approval Pembelian Aset', 'Pengajuan Pembelian Aset', 'Nilai > 100.000.000', 'Kirim ke Komite Anggaran', '3 Hari', 'Nonaktif', 'Finance & Direktur', 'Pembelian aset besar perlu persetujuan komite.', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(8, 'WF008', 'Reminder Jatuh Tempo Kontrak', 'H-30 Kontrak Berakhir', NULL, 'Kirim Email Reminder', 'Langsung', 'Aktif', 'Procurement', 'Pengingat otomatis sebelum kontrak vendor habis.', '2026-07-09 17:56:57', '2026-07-09 17:56:57');

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
  `progres` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_management`
--

INSERT INTO `project_management` (`id`, `nama_proyek`, `pic_proyek`, `tujuan`, `estimasi_waktu`, `status`, `progres`, `created_at`, `updated_at`) VALUES
(1, 'Migrasi ERP ke Cloud', 'Budi Santoso', 'Memindahkan seluruh infrastruktur ERP dari on-premise ke cloud AWS untuk meningkatkan skalabilitas', '6 bulan', 'In Progress', 45, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Implementasi SSO Perusahaan', 'Doni Prasetyo', 'Implementasi Single Sign-On untuk semua sistem internal menggunakan Keycloak', '3 bulan', 'Selesai', 100, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Pengembangan Mobile App Driver', 'Andi Wijaya', 'Membuat aplikasi mobile untuk monitoring dan tracking kendaraan operasional', '4 bulan', 'In Progress', 30, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Upgrade Infrastruktur Jaringan', 'Rudi Hermawan', 'Upgrade seluruh perangkat jaringan kantor pusat ke standar 10 Gbps', '2 bulan', 'Pending', 0, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Implementasi WAF', 'Budi Santoso', 'Pemasangan Web Application Firewall untuk semua endpoint API publik', '1 bulan', 'Selesai', 100, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
  `durasi` int(11) NOT NULL DEFAULT 0,
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
(1, 'PRJ001', 'Pengecoran Lantai Garasi', '2026-02-10', 1, 'Selesai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'PRJ001', 'Pemasangan Atap Baja Ringan', '2026-02-28', 1, 'Berjalan', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(3, 'PRJ001', 'Pemasangan Listrik & CCTV', '2026-03-10', 1, 'Scheduled', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(4, 'PRJ002', 'Pembayaran DP Pembelian Bus', '2026-02-20', 1, 'Selesai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(5, 'PRJ002', 'Pengiriman Unit Bus ke Pool', '2026-04-15', 1, 'Scheduled', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(6, 'PRJ003', 'Pemasangan GPS 20 Unit Sedan', '2026-02-15', 0, 'Berjalan', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(7, 'PRJ003', 'Aktivasi Dashboard Monitoring', '2026-03-15', 1, 'Scheduled', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(8, 'PRJ005', 'Mulai Operasional Rute I', '2026-03-01', 0, 'Selesai', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
  `qty` int(11) DEFAULT NULL,
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
(1, 'PR-001', '2026-01-20', 'Produksi', 'Pemohon 1', 'Spare Part', 'BRG-007', 227, 'pcs', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-1', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(2, 'PR-002', '2026-03-07', 'Gudang', 'Pemohon 2', 'ATK', 'BRG-014', 262, 'unit', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Gudang', '2026-03-09', 'Catatan PR ke-2', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(3, 'PR-003', '2026-02-14', 'IT', 'Pemohon 3', 'Komputer', 'BRG-021', 340, 'liter', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-3', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(4, 'PR-004', '2026-02-22', 'Finance', 'Pemohon 4', 'Bahan Bakar', 'BRG-028', 426, 'kg', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Finance', '2026-02-23', 'Catatan PR ke-4', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(5, 'PR-005', '2026-06-25', 'HR', 'Pemohon 5', 'Oli Mesin', 'BRG-035', 353, 'set', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-5', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(6, 'PR-006', '2026-05-15', 'Marketing', 'Pemohon 6', 'Ban Kendaraan', 'BRG-042', 431, 'dus', 'Stok Habis', NULL, 'Disetujui', 'Manajer Marketing', '2026-05-18', 'Catatan PR ke-6', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(7, 'PR-007', '2026-01-21', 'Operasional', 'Pemohon 7', 'Seragam', 'BRG-049', 52, 'rim', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-7', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(8, 'PR-008', '2026-05-06', 'Maintenance', 'Pemohon 8', 'Alat Kebersihan', 'BRG-056', 328, 'buah', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Maintenance', '2026-05-07', 'Catatan PR ke-8', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(9, 'PR-009', '2026-04-05', 'Produksi', 'Pemohon 9', 'Mebel', 'BRG-063', 19, 'pcs', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-9', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(10, 'PR-010', '2026-02-20', 'Gudang', 'Pemohon 10', 'Printer', 'BRG-070', 201, 'unit', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Gudang', '2026-02-22', 'Catatan PR ke-10', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(11, 'PR-011', '2026-03-13', 'IT', 'Pemohon 11', 'Spare Part', 'BRG-077', 23, 'liter', 'Stok Habis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-11', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(12, 'PR-012', '2026-05-08', 'Finance', 'Pemohon 12', 'ATK', 'BRG-084', 353, 'kg', 'Persediaan Menipis', NULL, 'Selesai', 'Manajer Finance', '2026-05-11', 'Catatan PR ke-12', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(13, 'PR-013', '2026-04-25', 'HR', 'Pemohon 13', 'Komputer', 'BRG-091', 473, 'set', 'Permintaan Proyek', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-13', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(14, 'PR-014', '2026-04-21', 'Marketing', 'Pemohon 14', 'Bahan Bakar', 'BRG-098', 417, 'dus', 'Penggantian Rutin', NULL, 'Disetujui', 'Manajer Marketing', '2026-04-24', 'Catatan PR ke-14', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(15, 'PR-015', '2026-01-30', 'Operasional', 'Pemohon 15', 'Oli Mesin', 'BRG-105', 99, 'rim', 'Kebutuhan Mendadak', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-15', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(16, 'PR-016', '2026-07-01', 'Maintenance', 'Pemohon 16', 'Ban Kendaraan', 'BRG-112', 359, 'buah', 'Stok Habis', NULL, 'Selesai', 'Manajer Maintenance', '2026-07-03', 'Catatan PR ke-16', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(17, 'PR-017', '2026-04-05', 'Produksi', 'Pemohon 17', 'Seragam', 'BRG-119', 463, 'pcs', 'Persediaan Menipis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-17', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(18, 'PR-018', '2026-05-28', 'Gudang', 'Pemohon 18', 'Alat Kebersihan', 'BRG-126', 313, 'unit', 'Permintaan Proyek', NULL, 'Disetujui', 'Manajer Gudang', '2026-05-31', 'Catatan PR ke-18', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(19, 'PR-019', '2026-06-11', 'IT', 'Pemohon 19', 'Mebel', 'BRG-133', 77, 'liter', 'Penggantian Rutin', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-19', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(20, 'PR-020', '2026-06-03', 'Finance', 'Pemohon 20', 'Printer', 'BRG-140', 249, 'kg', 'Kebutuhan Mendadak', NULL, 'Selesai', 'Manajer Finance', '2026-06-05', 'Catatan PR ke-20', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(21, 'PR-021', '2026-04-04', 'HR', 'Pemohon 21', 'Spare Part', 'BRG-147', 247, 'set', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-21', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(22, 'PR-022', '2026-05-16', 'Marketing', 'Pemohon 22', 'ATK', 'BRG-154', 221, 'dus', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Marketing', '2026-05-18', 'Catatan PR ke-22', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(23, 'PR-023', '2026-07-02', 'Operasional', 'Pemohon 23', 'Komputer', 'BRG-161', 303, 'rim', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-23', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(24, 'PR-024', '2026-04-11', 'Maintenance', 'Pemohon 24', 'Bahan Bakar', 'BRG-168', 135, 'buah', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Maintenance', '2026-04-14', 'Catatan PR ke-24', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(25, 'PR-025', '2026-05-15', 'Produksi', 'Pemohon 25', 'Oli Mesin', 'BRG-175', 104, 'pcs', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-25', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(26, 'PR-026', '2026-06-11', 'Gudang', 'Pemohon 26', 'Ban Kendaraan', 'BRG-182', 56, 'unit', 'Stok Habis', NULL, 'Disetujui', 'Manajer Gudang', '2026-06-12', 'Catatan PR ke-26', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(27, 'PR-027', '2026-06-08', 'IT', 'Pemohon 27', 'Seragam', 'BRG-189', 500, 'liter', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-27', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(28, 'PR-028', '2026-05-08', 'Finance', 'Pemohon 28', 'Alat Kebersihan', 'BRG-196', 359, 'kg', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Finance', '2026-05-10', 'Catatan PR ke-28', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(29, 'PR-029', '2026-03-25', 'HR', 'Pemohon 29', 'Mebel', 'BRG-203', 299, 'set', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-29', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(30, 'PR-030', '2026-07-08', 'Marketing', 'Pemohon 30', 'Printer', 'BRG-210', 405, 'dus', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Marketing', '2026-07-10', 'Catatan PR ke-30', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(31, 'PR-031', '2026-06-07', 'Operasional', 'Pemohon 31', 'Spare Part', 'BRG-217', 449, 'rim', 'Stok Habis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-31', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(32, 'PR-032', '2026-07-06', 'Maintenance', 'Pemohon 32', 'ATK', 'BRG-224', 477, 'buah', 'Persediaan Menipis', NULL, 'Selesai', 'Manajer Maintenance', '2026-07-07', 'Catatan PR ke-32', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(33, 'PR-033', '2026-07-03', 'Produksi', 'Pemohon 33', 'Komputer', 'BRG-231', 360, 'pcs', 'Permintaan Proyek', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-33', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(34, 'PR-034', '2026-03-07', 'Gudang', 'Pemohon 34', 'Bahan Bakar', 'BRG-238', 138, 'unit', 'Penggantian Rutin', NULL, 'Disetujui', 'Manajer Gudang', '2026-03-09', 'Catatan PR ke-34', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(35, 'PR-035', '2026-01-24', 'IT', 'Pemohon 35', 'Oli Mesin', 'BRG-245', 311, 'liter', 'Kebutuhan Mendadak', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-35', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(36, 'PR-036', '2026-05-30', 'Finance', 'Pemohon 36', 'Ban Kendaraan', 'BRG-252', 100, 'kg', 'Stok Habis', NULL, 'Selesai', 'Manajer Finance', '2026-06-02', 'Catatan PR ke-36', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(37, 'PR-037', '2026-06-17', 'HR', 'Pemohon 37', 'Seragam', 'BRG-259', 162, 'set', 'Persediaan Menipis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-37', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(38, 'PR-038', '2026-03-05', 'Marketing', 'Pemohon 38', 'Alat Kebersihan', 'BRG-266', 469, 'dus', 'Permintaan Proyek', NULL, 'Disetujui', 'Manajer Marketing', '2026-03-07', 'Catatan PR ke-38', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(39, 'PR-039', '2026-01-25', 'Operasional', 'Pemohon 39', 'Mebel', 'BRG-273', 25, 'rim', 'Penggantian Rutin', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-39', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(40, 'PR-040', '2026-06-15', 'Maintenance', 'Pemohon 40', 'Printer', 'BRG-280', 341, 'buah', 'Kebutuhan Mendadak', NULL, 'Selesai', 'Manajer Maintenance', '2026-06-17', 'Catatan PR ke-40', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(41, 'PR-041', '2026-06-30', 'Produksi', 'Pemohon 41', 'Spare Part', 'BRG-287', 139, 'pcs', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-41', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(42, 'PR-042', '2026-02-15', 'Gudang', 'Pemohon 42', 'ATK', 'BRG-294', 295, 'unit', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Gudang', '2026-02-18', 'Catatan PR ke-42', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(43, 'PR-043', '2026-05-31', 'IT', 'Pemohon 43', 'Komputer', 'BRG-301', 476, 'liter', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-43', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(44, 'PR-044', '2026-01-29', 'Finance', 'Pemohon 44', 'Bahan Bakar', 'BRG-308', 471, 'kg', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Finance', '2026-02-01', 'Catatan PR ke-44', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(45, 'PR-045', '2026-05-03', 'HR', 'Pemohon 45', 'Oli Mesin', 'BRG-315', 284, 'set', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-45', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(46, 'PR-046', '2026-02-26', 'Marketing', 'Pemohon 46', 'Ban Kendaraan', 'BRG-322', 426, 'dus', 'Stok Habis', NULL, 'Disetujui', 'Manajer Marketing', '2026-03-01', 'Catatan PR ke-46', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(47, 'PR-047', '2026-03-05', 'Operasional', 'Pemohon 47', 'Seragam', 'BRG-329', 84, 'rim', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-47', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(48, 'PR-048', '2026-04-10', 'Maintenance', 'Pemohon 48', 'Alat Kebersihan', 'BRG-336', 226, 'buah', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Maintenance', '2026-04-11', 'Catatan PR ke-48', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(49, 'PR-049', '2026-03-24', 'Produksi', 'Pemohon 49', 'Mebel', 'BRG-343', 326, 'pcs', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-49', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(50, 'PR-050', '2026-02-11', 'Gudang', 'Pemohon 50', 'Printer', 'BRG-350', 200, 'unit', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Gudang', '2026-02-14', 'Catatan PR ke-50', NULL, '2026-07-09 17:56:57', '2026-07-09 17:56:57');

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
  `total_barang` int(11) NOT NULL,
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
(1, 'PO-001', '2026-02-26', 'PT Maju Jaya', 'RFQ-001', 36, 49736631, 'Pending', '2026-03-15', NULL, 'Catatan PO ke-1', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'PO-002', '2026-04-01', 'CV Berkah Abadi', 'RFQ-002', 42, 30953712, 'Approved', '2026-04-16', NULL, 'Catatan PO ke-2', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'PO-003', '2026-05-19', 'PT Sumber Makmur', 'RFQ-003', 2, 46260444, 'Closed', '2026-05-26', '2026-05-28', 'Catatan PO ke-3', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'PO-004', '2026-02-13', 'UD Sejahtera', 'RFQ-004', 39, 30539962, 'Pending', '2026-03-01', NULL, 'Catatan PO ke-4', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'PO-005', '2026-05-13', 'PT Indo Supplier', 'RFQ-005', 10, 21460117, 'Approved', '2026-06-01', NULL, 'Catatan PO ke-5', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(6, 'PO-006', '2026-02-21', 'PT Maju Jaya', 'RFQ-006', 47, 3060577, 'Closed', '2026-03-02', '2026-03-08', 'Catatan PO ke-6', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(7, 'PO-007', '2026-05-24', 'CV Berkah Abadi', 'RFQ-007', 19, 10241572, 'Pending', '2026-06-05', NULL, 'Catatan PO ke-7', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(8, 'PO-008', '2026-06-20', 'PT Sumber Makmur', 'RFQ-008', 20, 47965163, 'Approved', '2026-07-08', NULL, 'Catatan PO ke-8', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(9, 'PO-009', '2026-05-07', 'UD Sejahtera', 'RFQ-009', 25, 33857801, 'Closed', '2026-05-18', '2026-05-23', 'Catatan PO ke-9', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(10, 'PO-010', '2026-05-14', 'PT Indo Supplier', 'RFQ-010', 17, 29553801, 'Pending', '2026-06-04', NULL, 'Catatan PO ke-10', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(11, 'PO-011', '2026-05-21', 'PT Maju Jaya', 'RFQ-011', 22, 24771922, 'Approved', '2026-05-29', NULL, 'Catatan PO ke-11', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(12, 'PO-012', '2026-02-25', 'CV Berkah Abadi', 'RFQ-012', 32, 26961549, 'Closed', '2026-03-09', '2026-03-12', 'Catatan PO ke-12', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(13, 'PO-013', '2026-06-07', 'PT Sumber Makmur', 'RFQ-013', 31, 41649296, 'Pending', '2026-06-24', NULL, 'Catatan PO ke-13', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(14, 'PO-014', '2026-03-04', 'UD Sejahtera', 'RFQ-014', 3, 10496944, 'Approved', '2026-03-25', NULL, 'Catatan PO ke-14', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(15, 'PO-015', '2026-04-29', 'PT Indo Supplier', 'RFQ-015', 37, 18752474, 'Closed', '2026-05-11', '2026-05-13', 'Catatan PO ke-15', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(16, 'PO-016', '2026-04-12', 'PT Maju Jaya', 'RFQ-016', 37, 48172104, 'Pending', '2026-04-23', NULL, 'Catatan PO ke-16', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(17, 'PO-017', '2026-04-08', 'CV Berkah Abadi', 'RFQ-017', 26, 5906203, 'Approved', '2026-04-18', NULL, 'Catatan PO ke-17', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(18, 'PO-018', '2026-07-01', 'PT Sumber Makmur', 'RFQ-018', 2, 3496214, 'Closed', '2026-07-17', '2026-07-21', 'Catatan PO ke-18', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(19, 'PO-019', '2026-06-18', 'UD Sejahtera', 'RFQ-019', 50, 981501, 'Pending', '2026-07-01', NULL, 'Catatan PO ke-19', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(20, 'PO-020', '2026-02-21', 'PT Indo Supplier', 'RFQ-020', 15, 46510241, 'Approved', '2026-03-13', NULL, 'Catatan PO ke-20', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(21, 'PO-021', '2026-03-11', 'PT Maju Jaya', 'RFQ-021', 44, 19760367, 'Closed', '2026-03-24', '2026-03-30', 'Catatan PO ke-21', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(22, 'PO-022', '2026-04-04', 'CV Berkah Abadi', 'RFQ-022', 36, 22866865, 'Pending', '2026-04-12', NULL, 'Catatan PO ke-22', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(23, 'PO-023', '2026-06-27', 'PT Sumber Makmur', 'RFQ-023', 20, 31982646, 'Approved', '2026-07-18', NULL, 'Catatan PO ke-23', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(24, 'PO-024', '2026-04-18', 'UD Sejahtera', 'RFQ-024', 25, 4411208, 'Closed', '2026-04-27', '2026-04-28', 'Catatan PO ke-24', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(25, 'PO-025', '2026-06-10', 'PT Indo Supplier', 'RFQ-025', 17, 38736317, 'Pending', '2026-06-25', NULL, 'Catatan PO ke-25', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(26, 'PO-026', '2026-04-22', 'PT Maju Jaya', 'RFQ-026', 50, 19776552, 'Approved', '2026-05-10', NULL, 'Catatan PO ke-26', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(27, 'PO-027', '2026-05-08', 'CV Berkah Abadi', 'RFQ-027', 5, 32734743, 'Closed', '2026-05-16', '2026-05-21', 'Catatan PO ke-27', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(28, 'PO-028', '2026-02-12', 'PT Sumber Makmur', 'RFQ-028', 18, 38012808, 'Pending', '2026-03-05', NULL, 'Catatan PO ke-28', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(29, 'PO-029', '2026-05-11', 'UD Sejahtera', 'RFQ-029', 6, 16776326, 'Approved', '2026-05-22', NULL, 'Catatan PO ke-29', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(30, 'PO-030', '2026-07-04', 'PT Indo Supplier', 'RFQ-030', 2, 19758903, 'Closed', '2026-07-13', '2026-07-17', 'Catatan PO ke-30', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
(1, '2026-06-28', 'Pembayaran rental masuk', 'BANK-INV-001', 1500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(2, '2026-07-01', 'Transfer service kendaraan', 'BANK-INV-002', 500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(3, '2026-07-04', 'Pembayaran deposit rental', 'BANK-INV-003', 2000000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(4, '2026-07-07', 'Pembayaran sparepart', 'BANK-INV-004', 750000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(5, '2026-07-10', 'Pemasukan rental harian', 'BANK-INV-005', 1200000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56');

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
  `durasi_jam` int(11) DEFAULT NULL,
  `durasi_hari` int(11) DEFAULT NULL,
  `durasi_bulan` int(11) DEFAULT NULL,
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
  `status_pembayaran` enum('belum_bayar','dp','lunas') NOT NULL DEFAULT 'belum_bayar',
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

INSERT INTO `rentals` (`id`, `user_id`, `kendaraan_id`, `member_id`, `tanggal_mulai`, `tanggal_selesai`, `tujuan`, `durasi_jam`, `durasi_hari`, `durasi_bulan`, `biaya_dasar`, `biaya_tambahan_total`, `total_biaya`, `metode_pembayaran`, `jenis_pembayaran`, `nominal_dp`, `nama_driver`, `kontak_driver`, `biaya_driver`, `bukti_lunas`, `bukti_dp`, `bukti_pelunasan`, `status_pembayaran`, `status`, `created_at`, `updated_at`, `pakai_batas_biaya`, `batas_biaya`, `durasi_tahun`, `invoice`, `kelayakan`) VALUES
(1, 1, 1, 1, '2026-06-28 00:56:56', '2026-06-30 00:56:56', 'Perjalanan dinas ke kota 1', 1, 2, 0, 668000, 299000, 967000, 'tunai', 'lunas', NULL, 'Driver 1', '08466132661', NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(2, 1, 2, 2, '2026-06-29 00:56:56', '2026-07-10 00:56:56', 'Perjalanan dinas ke kota 2', 7, 11, 0, 3036000, 474000, 3510000, 'transfer', 'dp', 1755000, 'Driver 2', NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(3, 1, 3, 3, '2026-06-17 00:56:56', '2026-06-30 00:56:56', 'Perjalanan dinas ke kota 3', 6, 13, 0, 3965000, 49000, 4014000, 'tunai', 'lunas', NULL, 'Driver 3', '08993940853', NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(4, 1, 4, 4, '2026-05-30 00:56:56', '2026-06-12 00:56:56', 'Perjalanan dinas ke kota 4', 6, 13, 0, 3744000, 481000, 4225000, 'transfer', 'dp', 2112500, 'Driver 4', '08152345625', 65000, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(5, 1, 5, 5, '2026-06-04 00:56:56', '2026-06-16 00:56:56', 'Perjalanan dinas ke kota 5', 1, 12, 0, 5484000, 13000, 5497000, 'tunai', 'lunas', NULL, NULL, '08940007593', NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(6, 1, 6, 6, '2026-06-08 00:56:56', '2026-06-10 00:56:56', 'Perjalanan dinas ke kota 6', 5, 2, 0, 1140000, 34000, 1174000, 'transfer', 'dp', 587000, 'Driver 6', NULL, NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(7, 1, 7, 7, '2026-05-08 00:56:56', '2026-05-13 00:56:56', 'Perjalanan dinas ke kota 7', 7, 5, 0, 2310000, 160000, 2470000, 'tunai', 'lunas', NULL, 'Driver 7', '08916936208', NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(8, 1, 8, 8, '2026-06-03 00:56:56', '2026-06-10 00:56:56', 'Perjalanan dinas ke kota 8', 2, 7, 0, 3941000, 96000, 4037000, 'transfer', 'dp', 2018500, NULL, NULL, 86000, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(9, 1, 9, 9, '2026-02-12 00:56:56', '2026-02-26 00:56:56', 'Perjalanan dinas ke kota 9', 4, 14, 0, 7588000, 305000, 7893000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(10, 1, 10, 10, '2026-01-31 00:56:56', '2026-02-14 00:56:56', 'Perjalanan dinas ke kota 10', 6, 14, 0, 5936000, 199000, 6135000, 'transfer', 'dp', 3067500, NULL, '08869636627', NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(11, 1, 11, 11, '2026-03-31 00:56:56', '2026-04-05 00:56:56', 'Perjalanan dinas ke kota 11', 6, 5, 0, 1155000, 69000, 1224000, 'tunai', 'lunas', NULL, NULL, NULL, 106000, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(12, 1, 12, 12, '2026-02-03 00:56:56', '2026-02-13 00:56:56', 'Perjalanan dinas ke kota 12', 7, 10, 0, 2320000, 144000, 2464000, 'transfer', 'dp', 1232000, 'Driver 12', NULL, NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(13, 1, 13, 13, '2026-06-29 00:56:56', '2026-07-07 00:56:56', 'Perjalanan dinas ke kota 13', 6, 8, 0, 4320000, 335000, 4655000, 'tunai', 'lunas', NULL, NULL, NULL, 194000, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(14, 1, 14, 14, '2026-03-21 00:56:56', '2026-03-29 00:56:56', 'Perjalanan dinas ke kota 14', 7, 8, 0, 3344000, 213000, 3557000, 'transfer', 'dp', 1778500, NULL, NULL, 97000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(15, 1, 15, 15, '2026-01-18 00:56:56', '2026-01-30 00:56:56', 'Perjalanan dinas ke kota 15', 1, 12, 0, 6852000, 271000, 7123000, 'tunai', 'lunas', NULL, 'Driver 15', NULL, NULL, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(16, 1, 16, 16, '2026-04-18 00:56:56', '2026-04-24 00:56:56', 'Perjalanan dinas ke kota 16', 0, 6, 0, 1470000, 325000, 1795000, 'transfer', 'dp', 897500, NULL, NULL, 120000, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(17, 1, 17, 17, '2026-06-19 00:56:56', '2026-07-02 00:56:56', 'Perjalanan dinas ke kota 17', 4, 13, 0, 4693000, 407000, 5100000, 'tunai', 'lunas', NULL, 'Driver 17', NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(18, 1, 18, 18, '2026-06-18 00:56:56', '2026-06-26 00:56:56', 'Perjalanan dinas ke kota 18', 4, 8, 0, 3040000, 136000, 3176000, 'transfer', 'dp', 1588000, 'Driver 18', '08484151858', 109000, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(19, 1, 19, 19, '2026-03-12 00:56:56', '2026-03-18 00:56:56', 'Perjalanan dinas ke kota 19', 8, 6, 0, 2292000, 340000, 2632000, 'tunai', 'lunas', NULL, 'Driver 19', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(20, 1, 20, 20, '2026-04-26 00:56:56', '2026-04-27 00:56:56', 'Perjalanan dinas ke kota 20', 6, 1, 0, 200000, 65000, 265000, 'transfer', 'dp', 132500, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(21, 1, 21, 21, '2026-06-08 00:56:56', '2026-06-22 00:56:56', 'Perjalanan dinas ke kota 21', 5, 14, 0, 3248000, 330000, 3578000, 'tunai', 'lunas', NULL, 'Driver 21', '08137548745', 161000, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(22, 1, 22, 22, '2026-05-09 00:56:56', '2026-05-13 00:56:56', 'Perjalanan dinas ke kota 22', 8, 4, 0, 1460000, 249000, 1709000, 'transfer', 'dp', 854500, 'Driver 22', '08103596575', 60000, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(23, 1, 23, 23, '2026-05-15 00:56:56', '2026-05-22 00:56:56', 'Perjalanan dinas ke kota 23', 3, 7, 0, 3920000, 355000, 4275000, 'tunai', 'lunas', NULL, 'Driver 23', NULL, 149000, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(24, 1, 24, 24, '2026-03-29 00:56:56', '2026-04-06 00:56:56', 'Perjalanan dinas ke kota 24', 0, 8, 0, 3784000, 50000, 3834000, 'transfer', 'dp', 1917000, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(25, 1, 25, 25, '2026-02-13 00:56:56', '2026-02-14 00:56:56', 'Perjalanan dinas ke kota 25', 4, 1, 0, 333000, 197000, 530000, 'tunai', 'lunas', NULL, NULL, '08119073049', NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(26, 1, 26, 26, '2026-01-16 00:56:56', '2026-01-24 00:56:56', 'Perjalanan dinas ke kota 26', 5, 8, 0, 1808000, 283000, 2091000, 'transfer', 'dp', 1045500, 'Driver 26', '08197746708', NULL, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(27, 1, 27, 27, '2026-05-22 00:56:56', '2026-05-25 00:56:56', 'Perjalanan dinas ke kota 27', 1, 3, 0, 822000, 454000, 1276000, 'tunai', 'lunas', NULL, 'Driver 27', NULL, 91000, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(28, 1, 28, 28, '2026-04-06 00:56:56', '2026-04-19 00:56:56', 'Perjalanan dinas ke kota 28', 2, 13, 0, 6721000, 161000, 6882000, 'transfer', 'dp', 3441000, NULL, '08227478947', 187000, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(29, 1, 29, 29, '2026-06-30 00:56:56', '2026-07-08 00:56:56', 'Perjalanan dinas ke kota 29', 0, 8, 0, 2312000, 82000, 2394000, 'tunai', 'lunas', NULL, 'Driver 29', NULL, 113000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(30, 1, 30, 30, '2026-01-19 00:56:56', '2026-01-25 00:56:56', 'Perjalanan dinas ke kota 30', 1, 6, 0, 2952000, 99000, 3051000, 'transfer', 'dp', 1525500, 'Driver 30', NULL, NULL, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(31, 1, 31, 31, '2026-01-22 00:56:56', '2026-02-03 00:56:56', 'Perjalanan dinas ke kota 31', 8, 12, 0, 5904000, 442000, 6346000, 'tunai', 'lunas', NULL, NULL, '08200774863', 62000, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(32, 1, 32, 32, '2026-04-03 00:56:56', '2026-04-07 00:56:56', 'Perjalanan dinas ke kota 32', 0, 4, 0, 1608000, 172000, 1780000, 'transfer', 'dp', 890000, NULL, '08812861728', NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(33, 1, 33, 33, '2026-02-05 00:56:56', '2026-02-15 00:56:56', 'Perjalanan dinas ke kota 33', 6, 10, 0, 2710000, 252000, 2962000, 'tunai', 'lunas', NULL, 'Driver 33', '08530070234', NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(34, 1, 34, 34, '2026-05-31 00:56:56', '2026-06-02 00:56:56', 'Perjalanan dinas ke kota 34', 3, 2, 0, 500000, 492000, 992000, 'transfer', 'dp', 496000, NULL, '08335587163', NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(35, 1, 35, 35, '2026-01-18 00:56:56', '2026-01-23 00:56:56', 'Perjalanan dinas ke kota 35', 8, 5, 0, 1815000, 169000, 1984000, 'tunai', 'lunas', NULL, 'Driver 35', '08196918268', 107000, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(36, 1, 36, 36, '2026-02-16 00:56:56', '2026-02-17 00:56:56', 'Perjalanan dinas ke kota 36', 8, 1, 0, 535000, 182000, 717000, 'transfer', 'dp', 358500, 'Driver 36', NULL, NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(37, 1, 37, 37, '2026-02-10 00:56:56', '2026-02-12 00:56:56', 'Perjalanan dinas ke kota 37', 6, 2, 0, 698000, 38000, 736000, 'tunai', 'lunas', NULL, 'Driver 37', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(38, 1, 38, 38, '2026-07-05 00:56:56', '2026-07-17 00:56:56', 'Perjalanan dinas ke kota 38', 8, 12, 0, 2736000, 355000, 3091000, 'transfer', 'dp', 1545500, NULL, '08508678417', NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(39, 1, 39, 39, '2026-02-26 00:56:56', '2026-03-10 00:56:56', 'Perjalanan dinas ke kota 39', 3, 12, 0, 6816000, 431000, 7247000, 'tunai', 'lunas', NULL, 'Driver 39', '08526210227', 125000, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(40, 1, 40, 40, '2026-04-17 00:56:56', '2026-04-22 00:56:56', 'Perjalanan dinas ke kota 40', 7, 5, 0, 1300000, 410000, 1710000, 'transfer', 'dp', 855000, 'Driver 40', NULL, 116000, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(41, 1, 41, 41, '2026-05-06 00:56:56', '2026-05-09 00:56:56', 'Perjalanan dinas ke kota 41', 2, 3, 0, 654000, 494000, 1148000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(42, 1, 42, 42, '2026-03-09 00:56:56', '2026-03-19 00:56:56', 'Perjalanan dinas ke kota 42', 6, 10, 0, 3110000, 198000, 3308000, 'transfer', 'dp', 1654000, 'Driver 42', '08407894401', NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(43, 1, 43, 43, '2026-06-11 00:56:56', '2026-06-20 00:56:56', 'Perjalanan dinas ke kota 43', 1, 9, 0, 5247000, 98000, 5345000, 'tunai', 'lunas', NULL, NULL, NULL, 73000, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(44, 1, 44, 44, '2026-04-11 00:56:56', '2026-04-12 00:56:56', 'Perjalanan dinas ke kota 44', 8, 1, 0, 227000, 416000, 643000, 'transfer', 'dp', 321500, 'Driver 44', '08155786851', NULL, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(45, 1, 45, 45, '2026-05-14 00:56:56', '2026-05-17 00:56:56', 'Perjalanan dinas ke kota 45', 0, 3, 0, 1263000, 51000, 1314000, 'tunai', 'lunas', NULL, 'Driver 45', '08515665839', 159000, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(46, 1, 46, 46, '2026-01-19 00:56:56', '2026-02-02 00:56:56', 'Perjalanan dinas ke kota 46', 3, 14, 0, 7616000, 387000, 8003000, 'transfer', 'dp', 4001500, 'Driver 46', NULL, 79000, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(47, 1, 47, 47, '2026-06-13 00:56:56', '2026-06-15 00:56:56', 'Perjalanan dinas ke kota 47', 0, 2, 0, 894000, 196000, 1090000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(48, 1, 48, 48, '2026-02-23 00:56:56', '2026-02-28 00:56:56', 'Perjalanan dinas ke kota 48', 5, 5, 0, 1005000, 474000, 1479000, 'transfer', 'dp', 739500, 'Driver 48', NULL, 187000, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(49, 1, 49, 49, '2026-05-24 00:56:56', '2026-05-31 00:56:56', 'Perjalanan dinas ke kota 49', 8, 7, 0, 2163000, 241000, 2404000, 'tunai', 'lunas', NULL, NULL, '08207518003', 196000, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL),
(50, 1, 50, 50, '2026-01-12 00:56:56', '2026-01-13 00:56:56', 'Perjalanan dinas ke kota 50', 3, 1, 0, 404000, 55000, 459000, 'transfer', 'dp', 229500, 'Driver 50', NULL, NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 17:56:56', '2026-07-09 17:56:56', 0, NULL, NULL, NULL, NULL);

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
-- Struktur dari tabel `requestfor_quotations`
--

CREATE TABLE `requestfor_quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_rfq` varchar(255) NOT NULL,
  `tanggal_rfq` date NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `kuantitas` int(11) NOT NULL,
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
(1, 'RFQ-001', '2026-05-29', 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 180, 'pcs', 514579, '2026-06-06', 'Open', 'Catatan RFQ ke-1', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'RFQ-002', '2026-05-03', 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 359, 'liter', 948134, '2026-06-01', 'Sent', 'Catatan RFQ ke-2', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'RFQ-003', '2026-01-30', 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 482, 'unit', 1913388, '2026-02-18', 'Closed', 'Catatan RFQ ke-3', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'RFQ-004', '2026-02-21', 'UD Sejahtera', 'BRG-004', 'Filter Udara', 250, 'set', 977658, '2026-03-03', 'Open', 'Catatan RFQ ke-4', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'RFQ-005', '2026-03-12', 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 303, 'buah', 1032965, '2026-04-11', 'Sent', 'Catatan RFQ ke-5', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(6, 'RFQ-006', '2026-06-11', 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 391, 'dus', 1044372, '2026-06-24', 'Closed', 'Catatan RFQ ke-6', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(7, 'RFQ-007', '2026-04-20', 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 144, 'kg', 1946785, '2026-05-20', 'Open', 'Catatan RFQ ke-7', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(8, 'RFQ-008', '2026-04-10', 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 55, 'pcs', 273505, '2026-05-04', 'Sent', 'Catatan RFQ ke-8', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(9, 'RFQ-009', '2026-05-27', 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 147, 'liter', 913966, '2026-06-12', 'Closed', 'Catatan RFQ ke-9', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(10, 'RFQ-010', '2026-04-13', 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 96, 'unit', 1341197, '2026-05-01', 'Open', 'Catatan RFQ ke-10', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(11, 'RFQ-011', '2026-03-30', 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 217, 'set', 1806607, '2026-04-20', 'Sent', 'Catatan RFQ ke-11', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(12, 'RFQ-012', '2026-04-13', 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 432, 'buah', 469016, '2026-04-24', 'Closed', 'Catatan RFQ ke-12', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(13, 'RFQ-013', '2026-06-01', 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 392, 'dus', 1838762, '2026-06-19', 'Open', 'Catatan RFQ ke-13', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(14, 'RFQ-014', '2026-02-24', 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 372, 'kg', 1897317, '2026-03-13', 'Sent', 'Catatan RFQ ke-14', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(15, 'RFQ-015', '2026-04-03', 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 372, 'pcs', 1444236, '2026-04-21', 'Closed', 'Catatan RFQ ke-15', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(16, 'RFQ-016', '2026-06-03', 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 192, 'liter', 243838, '2026-06-11', 'Open', 'Catatan RFQ ke-16', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(17, 'RFQ-017', '2026-05-13', 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 214, 'unit', 910169, '2026-06-03', 'Sent', 'Catatan RFQ ke-17', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(18, 'RFQ-018', '2026-02-18', 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 336, 'set', 753875, '2026-03-12', 'Closed', 'Catatan RFQ ke-18', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(19, 'RFQ-019', '2026-05-05', 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 272, 'buah', 293836, '2026-05-29', 'Open', 'Catatan RFQ ke-19', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(20, 'RFQ-020', '2026-04-14', 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 45, 'dus', 692972, '2026-04-21', 'Sent', 'Catatan RFQ ke-20', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(21, 'RFQ-021', '2026-02-18', 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 196, 'kg', 669787, '2026-03-12', 'Closed', 'Catatan RFQ ke-21', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(22, 'RFQ-022', '2026-04-02', 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 107, 'pcs', 470563, '2026-04-12', 'Open', 'Catatan RFQ ke-22', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(23, 'RFQ-023', '2026-01-19', 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 118, 'liter', 1970455, '2026-02-10', 'Sent', 'Catatan RFQ ke-23', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(24, 'RFQ-024', '2026-04-26', 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 378, 'unit', 770066, '2026-05-19', 'Closed', 'Catatan RFQ ke-24', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(25, 'RFQ-025', '2026-05-24', 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 162, 'set', 217303, '2026-06-22', 'Open', 'Catatan RFQ ke-25', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(26, 'RFQ-026', '2026-04-20', 'PT Maju Jaya', 'BRG-002', 'Oli Mesin 10W-40', 301, 'buah', 521446, '2026-05-15', 'Sent', 'Catatan RFQ ke-26', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(27, 'RFQ-027', '2026-01-24', 'CV Berkah Abadi', 'BRG-003', 'Ban Kendaraan', 233, 'dus', 145211, '2026-02-09', 'Closed', 'Catatan RFQ ke-27', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(28, 'RFQ-028', '2026-07-02', 'PT Sumber Makmur', 'BRG-004', 'Filter Udara', 243, 'kg', 697243, '2026-07-11', 'Open', 'Catatan RFQ ke-28', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(29, 'RFQ-029', '2026-02-09', 'UD Sejahtera', 'BRG-005', 'Aki Kendaraan', 117, 'pcs', 1526624, '2026-02-24', 'Sent', 'Catatan RFQ ke-29', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(30, 'RFQ-030', '2026-06-04', 'PT Indo Supplier', 'BRG-006', 'Kampas Rem', 39, 'liter', 344268, '2026-06-12', 'Closed', 'Catatan RFQ ke-30', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
(1, 'Ahmad Rifai', 'Staff Gudang', '2024-02-28', 'Mengundurkan diri', 'Selesai', 'Sudah', 'Sudah menyelesaikan serah terima aset dan dokumen', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(2, 'Maya Sari', 'Staff Marketing', '2024-04-15', 'Pindah domisili', 'Selesai', 'Sudah', 'Proses offboarding berjalan lancar', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(3, 'Dika Pratama', 'Developer Junior', '2024-06-01', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Akses sistem telah dicabut', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(4, 'Sari Utami', 'Staff Finance', '2024-07-31', 'Melanjutkan studi', 'Selesai', 'Sudah', 'Dokumen exit interview selesai', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(5, 'Bowo Setiawan', 'Teknisi Lapangan', '2024-09-30', 'Kesehatan', 'Selesai', 'Sudah', 'Serah terima peralatan sudah dilakukan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(6, 'Nita Lestari', 'Admin HR', '2024-11-15', 'Menikah dan pindah kota', 'Selesai', 'Sudah', 'Semua kewajiban telah diselesaikan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(7, 'Reza Aditya', 'IT Support', '2025-01-31', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Credential akun sudah dinonaktifkan', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(8, 'Putri Anggraini', 'Staff Operasional', '2025-03-15', 'Alasan keluarga', 'Proses', 'Belum', 'Sedang dalam proses serah terima', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(9, 'Galih Santoso', 'Supervisor Produksi', '2025-05-30', 'Pensiun dini', 'Proses', 'Belum', 'Menunggu pengganti untuk serah terima', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(10, 'Lina Permatasari', 'Staff Akuntansi', '2025-06-30', 'Wirausaha', 'Proses', 'Belum', 'Dalam proses dokumentasi offboarding', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(11, 'Bagas Wicaksono', 'Driver', '2025-07-01', 'Kontrak tidak diperpanjang', 'Proses', 'Belum', 'Mengembalikan kendaraan dinas', '2026-07-09 17:57:06', '2026-07-09 17:57:06'),
(12, 'Rina Kurniawati', 'Customer Service', '2026-01-31', 'Mengurus anak', 'Proses', 'Belum', 'Exit interview sudah dilakukan', '2026-07-09 17:57:06', '2026-07-09 17:57:06');

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
  `qty` int(11) NOT NULL,
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
(1, 'RTR-001', '2026-02-10', 'SO-2026-001', 'PT Maju Bersama', 'Sewa Minibus', 1, 'Unit mengalami kerusakan', 5000000.00, 'Selesai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'RTR-002', '2026-02-20', 'SO-2026-002', 'PT Global Trans', 'Sewa Bus', 1, 'Spesifikasi tidak sesuai', 15000000.00, 'Diproses', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(3, 'RTR-003', '2026-03-05', 'SO-2026-003', 'CV Karya Indah', 'Sewa Truk', 1, 'Truk bermasalah di tengah jalan', 8000000.00, 'Selesai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(4, 'RTR-004', '2026-03-18', 'SO-2026-004', 'PT Nusantara Raya', 'Sewa Minibus', 1, 'AC tidak berfungsi', 5500000.00, 'Menunggu', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(5, 'RTR-005', '2026-04-02', 'SO-2026-006', 'PT Berlian Trans', 'Sewa Bus', 1, 'Pembatalan sebagian order', 10000000.00, 'Selesai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(6, 'RTR-006', '2026-04-15', 'SO-2026-005', 'CV Jaya Mandiri', 'Sewa MPV', 2, 'Unit terlambat pengiriman', 8000000.00, 'Diproses', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(7, 'RTR-007', '2026-05-01', 'SO-2026-008', 'PT Sejahtera Abadi', 'Sewa Sedan', 1, 'Kendaraan tidak sesuai pesanan', 3500000.00, 'Menunggu', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(8, 'RTR-008', '2026-05-15', 'SO-2026-009', 'PT Prima Raya', 'Sewa SUV', 1, 'Kondisi kendaraan buruk', 6000000.00, 'Selesai', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
  `qty` int(11) NOT NULL,
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
(1, 'SO-2026-001', '2026-01-20', 'PT Maju Bersama', 'Sewa Minibus 2 Unit', 2, 10000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'SO-2026-002', '2026-02-05', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(3, 'SO-2026-003', '2026-02-18', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 'Diproses', 'Tempo', 'Cici', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(4, 'SO-2026-004', '2026-03-01', 'PT Nusantara Raya', 'Sewa Minibus', 2, 11000000.00, 'Selesai', 'Kredit', 'Dani', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(5, 'SO-2026-005', '2026-03-10', 'CV Jaya Mandiri', 'Sewa MPV 4 Unit', 4, 16000000.00, 'Diproses', 'Transfer Bank', 'Andi', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(6, 'SO-2026-006', '2026-03-25', 'PT Berlian Trans', 'Sewa Bus Medium 2 Unit', 2, 20000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(7, 'SO-2026-007', '2026-04-05', 'CV Mitra Logistik', 'Sewa Truk 3 Unit', 3, 22500000.00, 'Dibatalkan', 'Transfer Bank', 'Cici', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(8, 'SO-2026-008', '2026-04-20', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 10500000.00, 'Diproses', 'Cash', 'Dani', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(9, 'SO-2026-009', '2026-05-08', 'PT Prima Raya', 'Sewa SUV', 2, 12000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(10, 'SO-2026-010', '2026-05-20', 'PT Sinar Harapan', 'Sewa Minibus', 1, 5500000.00, 'Diproses', 'Tempo', 'Budi', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `segmentasis`
--

CREATE TABLE `segmentasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `segment_code` varchar(255) NOT NULL,
  `segment_name` varchar(255) NOT NULL,
  `segmentation_criteria` text NOT NULL,
  `customer_count` int(11) NOT NULL DEFAULT 0,
  `campaign_goal` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `segmentasis`
--

INSERT INTO `segmentasis` (`id`, `segment_code`, `segment_name`, `segmentation_criteria`, `customer_count`, `campaign_goal`, `status`, `created_at`, `updated_at`) VALUES
(1, 'SEG001', 'Corporate Client', 'Perusahaan dengan kontrak bulanan', 15, 'Retain & Upsell', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'SEG002', 'Individual Frequent', 'Individu rental >3x dalam 6 bulan', 48, 'Loyalty Program', 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
(1, 'web-server-01', 'Cloud', 'AWS ap-southeast-1', 'Ubuntu 22.04 LTS', 'AWS', 'Aktif', 1, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'db-server-01', 'Cloud', 'AWS ap-southeast-1', 'Amazon Linux 2', 'AWS', 'Aktif', 1, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'file-server-local', 'Physical', 'Data Center Cibitung', 'Windows Server 2022', NULL, 'Aktif', 1, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'dev-server-01', 'VPS', 'Niagahoster VPS', 'Ubuntu 20.04 LTS', 'Niagahoster', 'Aktif', 0, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'backup-server-01', 'Physical', 'Ruang Server Jakarta', 'CentOS 7', NULL, 'Maintenance', 0, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 1, 'Ganti Oli Mesin', 350000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(2, 1, 'Tune Up', 500000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(3, 1, 'Spooring Balancing', 250000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(4, 1, 'Ganti Kampas Rem', 450000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(5, 1, 'Service AC', 600000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(6, 1, 'Ganti Ban', 900000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(7, 1, 'Overhaul Mesin', 4500000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(8, 1, 'Cuci Mobil Premium', 75000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(9, 1, 'Ganti Aki', 1200000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(10, 1, 'Perbaikan Suspensi', 1800000, '2026-07-09 17:56:55', '2026-07-09 17:56:55');

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
(1, 1, '2026-04-28', 77463, 'Layak', 750000, 'Ganti oli mesin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(2, 2, '2025-09-16', 100458, 'Layak', 600000, 'Tune up', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(3, 3, '2025-07-14', 117658, 'Layak', 1350000, 'Ganti kampas rem', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(4, 4, '2025-07-30', 73099, 'Tidak Layak', 1450000, 'Servis AC', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(5, 5, '2026-07-07', 10131, 'Layak', 1250000, 'Ganti ban', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(6, 6, '2025-07-17', 13230, 'Layak', 800000, 'Overhaul mesin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(7, 7, '2026-03-27', 61947, 'Layak', 1000000, 'Ganti aki', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(8, 8, '2025-10-23', 55207, 'Tidak Layak', 400000, 'Servis transmisi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(9, 9, '2026-02-02', 51048, 'Layak', 1000000, 'Ganti filter udara', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(10, 10, '2026-05-01', 71053, 'Layak', 750000, 'Perbaikan body', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(11, 11, '2026-02-19', 99617, 'Layak', 1500000, 'Ganti busi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(12, 12, '2026-02-01', 73995, 'Tidak Layak', 1200000, 'Servis suspensi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(13, 13, '2026-05-31', 28594, 'Layak', 1150000, 'Ganti timing belt', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(14, 14, '2025-10-03', 67457, 'Layak', 900000, 'Kalibrasi lampu', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(15, 15, '2026-05-02', 5333, 'Layak', 300000, 'Servis power steering', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(16, 16, '2026-05-20', 91828, 'Tidak Layak', 600000, 'Ganti knalpot', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(17, 17, '2026-02-09', 35164, 'Layak', 1400000, 'Perbaikan sistem pendingin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(18, 18, '2026-05-24', 85636, 'Layak', 300000, 'Ganti kopling', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(19, 19, '2025-11-16', 100397, 'Layak', 250000, 'Servis rem tangan', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(20, 20, '2025-08-08', 51183, 'Tidak Layak', 1200000, 'Ganti wiper', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(21, 21, '2026-07-08', 68729, 'Layak', 400000, 'Ganti oli mesin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(22, 22, '2026-02-04', 63969, 'Layak', 350000, 'Tune up', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(23, 23, '2025-11-22', 106563, 'Layak', 1050000, 'Ganti kampas rem', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(24, 24, '2025-07-22', 76961, 'Tidak Layak', 450000, 'Servis AC', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(25, 25, '2025-08-14', 21284, 'Layak', 500000, 'Ganti ban', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(26, 26, '2025-11-15', 60012, 'Layak', 1500000, 'Overhaul mesin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(27, 27, '2026-03-07', 41890, 'Layak', 800000, 'Ganti aki', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(28, 28, '2025-09-25', 23468, 'Tidak Layak', 700000, 'Servis transmisi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(29, 29, '2025-08-03', 64704, 'Layak', 1250000, 'Ganti filter udara', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(30, 30, '2026-05-07', 54289, 'Layak', 1050000, 'Perbaikan body', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(31, 31, '2026-05-29', 117199, 'Layak', 1400000, 'Ganti busi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(32, 32, '2025-09-21', 101563, 'Tidak Layak', 1000000, 'Servis suspensi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(33, 33, '2026-04-09', 21820, 'Layak', 900000, 'Ganti timing belt', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(34, 34, '2026-05-23', 40162, 'Layak', 700000, 'Kalibrasi lampu', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(35, 35, '2025-12-21', 11903, 'Layak', 250000, 'Servis power steering', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(36, 36, '2026-04-16', 71674, 'Tidak Layak', 200000, 'Ganti knalpot', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(37, 37, '2026-03-08', 27260, 'Layak', 1500000, 'Perbaikan sistem pendingin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(38, 38, '2025-10-31', 100014, 'Layak', 700000, 'Ganti kopling', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(39, 39, '2025-09-21', 92169, 'Layak', 800000, 'Servis rem tangan', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(40, 40, '2026-03-08', 75500, 'Tidak Layak', 1150000, 'Ganti wiper', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(41, 41, '2025-07-12', 10630, 'Layak', 150000, 'Ganti oli mesin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(42, 42, '2026-06-11', 69032, 'Layak', 1050000, 'Tune up', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(43, 43, '2026-07-07', 6528, 'Layak', 1050000, 'Ganti kampas rem', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(44, 44, '2026-04-07', 105704, 'Tidak Layak', 650000, 'Servis AC', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(45, 45, '2025-10-12', 90878, 'Layak', 1300000, 'Ganti ban', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(46, 46, '2026-02-22', 117449, 'Layak', 750000, 'Overhaul mesin', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(47, 47, '2026-05-26', 106079, 'Layak', 1150000, 'Ganti aki', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(48, 48, '2025-07-30', 38702, 'Tidak Layak', 150000, 'Servis transmisi', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(49, 49, '2025-11-09', 32152, 'Layak', 1050000, 'Ganti filter udara', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(50, 50, '2026-03-30', 66412, 'Layak', 250000, 'Perbaikan body', NULL, '2026-07-09 17:56:55', '2026-07-09 17:56:55');

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
(1, 1, 'Oli mesin sudah hitam dan rem berbunyi', 45000, 850000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-20', '2026-07-09 17:56:55', '2026-07-09 17:56:55', NULL),
(2, 2, 'AC tidak dingin', 52000, 600000, 'proses', NULL, 0, 0, 'stabil', '2026-06-30', '2026-07-09 17:56:55', '2026-07-09 17:56:55', NULL),
(3, 3, 'Ban depan aus', 70000, 1800000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-10', '2026-07-09 17:56:55', '2026-07-09 17:56:55', NULL),
(4, 1, 'Mesin getar saat idle', 47000, 500000, 'proses', NULL, 0, 0, 'stabil', '2026-07-05', '2026-07-09 17:56:55', '2026-07-09 17:56:55', NULL),
(5, 2, 'Ganti aki', 55000, 1200000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-25', '2026-07-09 17:56:55', '2026-07-09 17:56:55', NULL);

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
  `batas_reminder` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `satuan_reminder` enum('hari','minggu','bulan','tahun') NOT NULL DEFAULT 'hari',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `nama_perusahaan`, `alamat`, `telepon`, `email`, `website`, `logo`, `nama_bank`, `nomor_rekening`, `atas_nama_rekening`, `kode_pos`, `batas_reminder`, `satuan_reminder`, `created_at`, `updated_at`) VALUES
(1, 'PT Rental Kendaraan Indonesia', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-12345678', 'info@rentalkendaraan.co.id', 'https://rentalkendaraan.co.id', NULL, 'Bank BCA', '1234567890', 'PT Rental Kendaraan Indonesia', '10110', 1, 'bulan', '2026-07-09 17:56:57', '2026-07-09 17:56:57');

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
(1, 'Teguh Santosa', 'Pagi', '07:00:00', '15:00:00', '2', '10', 'Lembur pengiriman barang', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(2, 'Arif Budiman', 'Pagi', '07:00:00', '15:00:00', NULL, '8', 'Shift reguler', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(3, 'Dody Kurniawan', 'Siang', '15:00:00', '23:00:00', '1', '9', 'Lembur rapat koordinasi', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(4, 'Rizky Fadillah', 'Pagi', '08:00:00', '17:00:00', '3', '12', 'Lembur deploy sistem', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(5, 'Yusuf Hidayat', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler IT', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(6, 'Hendra Gunawan', 'Pagi', '08:00:00', '17:00:00', '2', '11', 'Lembur maintenance server', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(7, 'Wahyu Nugroho', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(8, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '1.5', '9.5', 'Lembur laporan pajak', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(9, 'Linda Permata', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur audit internal', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(10, 'Rini Apriani', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler HRD', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(11, 'Eko Prasetyo', 'Malam', '23:00:00', '07:00:00', '1', '9', 'Shift malam + lembur', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(12, 'Dewi Kusuma', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(13, 'Teguh Santosa', 'Malam', '23:00:00', '07:00:00', '2', '10', 'Lembur pengawasan malam', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(14, 'Arif Budiman', 'Siang', '15:00:00', '23:00:00', NULL, '8', 'Rotasi shift siang', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(15, 'Rizky Fadillah', 'Siang', '12:00:00', '21:00:00', '2', '11', 'Lembur perbaikan bug produksi', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(16, 'Yusuf Hidayat', 'Malam', '23:00:00', '07:00:00', NULL, '8', 'Shift malam on-call', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(17, 'Wahyu Nugroho', 'Pagi', '07:30:00', '16:30:00', '1', '9', 'Lembur tutup buku', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(18, 'Dody Kurniawan', 'Pagi', '07:00:00', '16:00:00', NULL, '8', 'Shift reguler operasional', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(19, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur SPT tahunan', '2026-07-09 17:57:05', '2026-07-09 17:57:05'),
(20, 'Hendra Gunawan', 'Siang', '12:00:00', '21:00:00', '1', '9', 'Lembur migrasi data', '2026-07-09 17:57:05', '2026-07-09 17:57:05');

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
(1, 'DOC-2026-001', 'Kontrak', '2026-01-20', 'PT Maju Bersama & PT APY Rent', 'Ditandatangani', 'PrivyID', 'Kontrak sewa 3 bulan', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'DOC-2026-002', 'Perjanjian', '2026-02-01', 'CV Karya Indah & PT APY Rent', 'Ditandatangani', 'DocuSign', 'PKS layanan transportasi', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(3, 'DOC-2026-003', 'MOU', '2026-02-15', 'PT Global Trans & PT APY Rent', 'Menunggu', 'PrivyID', 'Menunggu tanda tangan direktur', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(4, 'DOC-2026-004', 'Penawaran', '2026-03-01', 'PT Nusantara Raya & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Penawaran disetujui', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(5, 'DOC-2026-005', 'Kontrak', '2026-03-15', 'CV Jaya Mandiri & PT APY Rent', 'Ditolak', 'PrivyID', 'Ditolak karena klausul tidak sesuai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(6, 'DOC-2026-006', 'Perjanjian', '2026-04-01', 'PT Berlian Trans & PT APY Rent', 'Ditandatangani', 'Peruri', 'PKS jangka panjang', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(7, 'DOC-2026-007', 'Kontrak', '2026-04-20', 'PT Prima Raya & PT APY Rent', 'Menunggu', 'DocuSign', 'Dalam proses review', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(8, 'DOC-2026-008', 'MOU', '2026-05-05', 'PT Sejahtera Abadi & PT APY Rent', 'Ditandatangani', 'Manual', 'Ditandatangani secara fisik', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(9, 'DOC-2026-009', 'Lainnya', '2026-05-20', 'CV Mitra Logistik & PT APY Rent', 'Menunggu', 'PrivyID', 'Surat kuasa armada', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(10, 'DOC-2026-010', 'Kontrak', '2026-06-01', 'PT Sinar Harapan & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Kontrak perpanjangan', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `skill_matrices`
--

CREATE TABLE `skill_matrices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `skill` varchar(255) NOT NULL,
  `level` int(10) UNSIGNED NOT NULL,
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
(1, 'Rizky Fadillah', 'Laravel', 2, 'Y', 'Hendra Gunawan', '2026-01-06', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Yusuf Hidayat', 'Vue.js', 3, 'T', 'Hendra Gunawan', '2025-12-04', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Hendra Gunawan', 'MySQL', 1, 'T', 'Hendra Gunawan', '2025-10-17', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Wahyu Nugroho', 'PHP', 5, 'Y', 'Hendra Gunawan', '2025-08-26', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Fitri Handayani', 'Microsoft Excel', 2, 'T', 'Linda Permata', '2025-07-20', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 'Linda Permata', 'Akuntansi', 1, 'T', 'Linda Permata', '2025-07-10', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 'Rini Apriani', 'Perpajakan', 1, 'Y', 'Linda Permata', '2026-05-19', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 'Eko Prasetyo', 'SAP', 3, 'T', 'Linda Permata', '2025-11-14', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 'Dewi Kusuma', 'Rekrutmen', 4, 'T', 'Dewi Kusuma', '2025-10-09', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(10, 'Teguh Santosa', 'Payroll', 5, 'Y', 'Dewi Kusuma', '2026-02-05', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(11, 'Arif Budiman', 'K3', 5, 'T', 'Dewi Kusuma', '2025-11-25', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(12, 'Dody Kurniawan', 'Negosiasi', 4, 'T', 'Dody Kurniawan', '2026-05-20', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(13, 'Rizky Fadillah', 'AutoCAD', 5, 'Y', 'Teguh Santosa', '2025-09-09', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(14, 'Yusuf Hidayat', 'Troubleshooting', 5, 'T', 'Yusuf Hidayat', '2026-06-05', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `software_licenses`
--

CREATE TABLE `software_licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_software` varchar(255) NOT NULL,
  `jenis_lisensi` varchar(255) NOT NULL,
  `jumlah_lisensi` int(11) NOT NULL,
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
(1, 'Microsoft Office 365', 'Subscription', 25, 'Microsoft', '2025-12-31', 'Aktif', '2024-12-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Adobe Creative Cloud', 'Subscription', 5, 'Adobe', '2025-06-30', 'Aktif', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Kaspersky Endpoint Security', 'Perpetual', 50, 'Kaspersky', '2024-03-31', 'Expired', '2024-04-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Zoom Pro', 'Subscription', 10, 'Zoom', '2025-09-30', 'Aktif', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'AutoCAD 2024', 'Perpetual', 3, 'Autodesk', '2026-01-01', 'Aktif', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
  `klik` int(11) NOT NULL DEFAULT 0,
  `konversi` int(11) NOT NULL DEFAULT 0,
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
(1, 'SMP001', 'Instagram', 'instagram', 'promo_rental_july', 320, 18, 1500000.00, 9000000.00, 500.00, '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'SMP002', 'Facebook', 'facebook', 'awareness_apyrent', 580, 25, 2000000.00, 12500000.00, 525.00, '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
(1, 'Direktur Utama', 'Budi Santoso', 'NIP-001', 'Direksi', NULL, 'Jakarta', 'Tetap', '2018-01-02', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Direktur Operasional', 'Siti Rahayu', 'NIP-002', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Direktur Keuangan', 'Agus Wibowo', 'NIP-003', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Manager HRD', 'Dewi Kusuma', 'NIP-010', 'HRD', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-01-15', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Staff HRD', 'Rini Apriani', 'NIP-011', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Tetap', '2021-04-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(6, 'Staff HRD', 'Eko Prasetyo', 'NIP-012', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Kontrak', '2023-07-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(7, 'Manager IT', 'Hendra Gunawan', 'NIP-020', 'IT', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-02-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(8, 'Developer', 'Rizky Fadillah', 'NIP-021', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2022-05-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(9, 'IT Support', 'Yusuf Hidayat', 'NIP-022', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2023-01-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(10, 'Manager Finance', 'Linda Permata', 'NIP-030', 'Finance', 'Agus Wibowo', 'Jakarta', 'Tetap', '2020-06-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(11, 'Staff Accounting', 'Wahyu Nugroho', 'NIP-031', 'Finance', 'Linda Permata', 'Jakarta', 'Tetap', '2021-08-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(12, 'Staff Pajak', 'Fitri Handayani', 'NIP-032', 'Finance', 'Linda Permata', 'Jakarta', 'Kontrak', '2022-09-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(13, 'Manager Operasional', 'Dody Kurniawan', 'NIP-040', 'Operasional', 'Siti Rahayu', 'Surabaya', 'Tetap', '2019-11-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(14, 'Supervisor Lapangan', 'Teguh Santosa', 'NIP-041', 'Operasional', 'Dody Kurniawan', 'Surabaya', 'Tetap', '2021-01-10', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(15, 'Teknisi', 'Arif Budiman', 'NIP-042', 'Operasional', 'Teguh Santosa', 'Surabaya', 'Kontrak', '2023-03-15', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 1, 'CV Suku Cadang Motor', '081234567890', 'Oli Mesin', 50, 75000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(2, 1, 'PT Ban Indonesia', '082233445566', 'Ban Mobil', 20, 850000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(3, 1, 'Toko Sparepart Jaya', '081377788899', 'Aki Mobil', 15, 1200000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(4, 1, 'CV Audio Mobil', '081299988877', 'GPS Tracker', 10, 450000, '2026-07-09 17:56:55', '2026-07-09 17:56:55'),
(5, 1, 'PT Diesel Utama', '082122334455', 'Filter Solar', 40, 95000, '2026-07-09 17:56:55', '2026-07-09 17:56:55');

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
(1, 'Database ERP Production', 'Full', 'Harian', 'AWS S3 Bucket', 'Aktif', '2025-01-15', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'File Server Dokumen', 'Incremental', 'Mingguan', 'NAS Lokal + Cloud', 'Aktif', '2024-12-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Email Server', 'Full', 'Mingguan', 'Tape Drive', 'Aktif', '2025-02-01', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Aplikasi HRIS', 'Differential', 'Harian', 'GCP Storage', 'Gagal', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Website Company Profile', 'Full', 'Bulanan', 'Hosting cPanel', 'Nonaktif', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
(1, 'Andi', '2026-01', 33000000.00, 45000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'Budi', '2026-01', 29000000.00, 27000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(3, 'Cici', '2026-01', 48000000.00, 65000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(4, 'Dani', '2026-01', 27000000.00, 38000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(5, 'Andi', '2026-02', 20000000.00, 32000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(6, 'Budi', '2026-02', 34000000.00, 26000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(7, 'Cici', '2026-02', 35000000.00, 34000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(8, 'Dani', '2026-02', 56000000.00, 63000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(9, 'Andi', '2026-03', 48000000.00, 41000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(10, 'Budi', '2026-03', 20000000.00, 33000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(11, 'Cici', '2026-03', 40000000.00, 50000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(12, 'Dani', '2026-03', 24000000.00, 44000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(13, 'Andi', '2026-04', 32000000.00, 27000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(14, 'Budi', '2026-04', 51000000.00, 21000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(15, 'Cici', '2026-04', 33000000.00, 64000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(16, 'Dani', '2026-04', 37000000.00, 51000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(17, 'Andi', '2026-05', 28000000.00, 18000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(18, 'Budi', '2026-05', 21000000.00, 59000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(19, 'Cici', '2026-05', 55000000.00, 38000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(20, 'Dani', '2026-05', 37000000.00, 26000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(21, 'Andi', '2026-06', 29000000.00, 51000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(22, 'Budi', '2026-06', 57000000.00, 36000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(23, 'Cici', '2026-06', 34000000.00, 31000000.00, 'Belum mencapai target', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(24, 'Dani', '2026-06', 56000000.00, 63000000.00, 'Target tercapai', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
  `total_klik` int(11) NOT NULL DEFAULT 0,
  `total_konversi` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `trackingutms`
--

INSERT INTO `trackingutms` (`id`, `kode_tracking`, `url_tujuan`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `total_klik`, `total_konversi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'UTM001', 'https://apyrent.com/promo', 'google', 'cpc', 'rental_promo_q3', 'sewa mobil jakarta', 'text_ad_1', 450, 32, 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58'),
(2, 'UTM002', 'https://apyrent.com/fleet', 'email', 'newsletter', 'new_cars_2026', NULL, 'banner_top', 280, 19, 'Aktif', '2026-07-09 17:56:58', '2026-07-09 17:56:58');

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
(1, 'Test User', 'testuser', 'test@example.com', '$2y$12$u3MqXZrnlDeOhuXtSV9yu.tWnNEEZHLSorHlI312sV7q.EfCkVpS2', '08123456789', NULL, 'superadmin', 'aktif', NULL, '2026-07-09 17:56:54', '2026-07-09 17:56:54');

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
(1, 'Budi Santoso', 'IT', 'Administrator', 'ERP Sistem', 'Aktif', 'Admin utama sistem', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(2, 'Sari Dewi', 'Finance', 'Read-Write', 'Accounting Software', 'Aktif', NULL, '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(3, 'Rudi Hermawan', 'HR', 'Read Only', 'HRIS', 'Aktif', 'Akses terbatas laporan', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(4, 'Dewi Cahyani', 'Sales', 'User', 'CRM', 'Nonaktif', 'Karyawan resign Desember 2024', '2026-07-09 17:57:00', '2026-07-09 17:57:00'),
(5, 'Anto Nugroho', 'Operasional', 'Read-Write', 'ERP Sistem', 'Suspended', 'Akses ditangguhkan sementara', '2026-07-09 17:57:00', '2026-07-09 17:57:00');

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
  `rating` int(11) DEFAULT NULL,
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
(1, 'VDR-001', 'CV Vendor Nusantara 1', 'Bahan Baku', 'Jl. Jakarta No. 3', 'PIC Vendor 1', '08446645432', 'Kain Katun', 4, 'Aktif', '2025-12-24', 'Catatan vendor ke-1', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(2, 'VDR-002', 'PT Vendor Nusantara 2', 'Packaging', 'Jl. Bandung No. 6', 'PIC Vendor 2', '08998079831', 'Kardus dan Label', 2, 'Aktif', '2026-04-01', 'Catatan vendor ke-2', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(3, 'VDR-003', 'CV Vendor Nusantara 3', 'Jasa IT', 'Jl. Semarang No. 9', 'PIC Vendor 3', '08560468661', 'Maintenance Sistem', 3, 'Aktif', '2025-09-12', 'Catatan vendor ke-3', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(4, 'VDR-004', 'PT Vendor Nusantara 4', 'Spare Part', 'Jl. Yogyakarta No. 12', 'PIC Vendor 4', '08643741061', 'Spare Part Kendaraan', 4, 'Aktif', '2025-11-18', 'Catatan vendor ke-4', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(5, 'VDR-005', 'CV Vendor Nusantara 5', 'Logistik', 'Jl. Surabaya No. 15', 'PIC Vendor 5', '08603458500', 'Pengiriman Barang', 3, 'Tidak Aktif', '2026-06-22', 'Catatan vendor ke-5', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(6, 'VDR-006', 'PT Vendor Nusantara 6', 'Maintenance', 'Jl. Medan No. 18', 'PIC Vendor 6', '08835914278', 'Servis Mesin', 2, 'Aktif', '2026-04-13', 'Catatan vendor ke-6', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(7, 'VDR-007', 'CV Vendor Nusantara 7', 'Cleaning', 'Jl. Makassar No. 21', 'PIC Vendor 7', '08166769342', 'Jasa Kebersihan', 5, 'Aktif', '2026-04-24', 'Catatan vendor ke-7', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(8, 'VDR-008', 'PT Vendor Nusantara 8', 'Security', 'Jl. Palembang No. 24', 'PIC Vendor 8', '08655177118', 'Jasa Keamanan', 4, 'Aktif', '2026-04-28', 'Catatan vendor ke-8', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(9, 'VDR-009', 'CV Vendor Nusantara 9', 'Percetakan', 'Jl. Malang No. 27', 'PIC Vendor 9', '08824204119', 'Cetak Dokumen', 5, 'Aktif', '2026-04-11', 'Catatan vendor ke-9', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(10, 'VDR-010', 'PT Vendor Nusantara 10', 'ATK', 'Jl. Solo No. 30', 'PIC Vendor 10', '08737522171', 'Alat Tulis Kantor', 3, 'Tidak Aktif', '2026-04-29', 'Catatan vendor ke-10', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(11, 'VDR-011', 'CV Vendor Nusantara 11', 'Bahan Baku', 'Jl. Jakarta No. 33', 'PIC Vendor 11', '08412406518', 'Cat dan Kimia', 3, 'Aktif', '2025-07-26', 'Catatan vendor ke-11', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(12, 'VDR-012', 'PT Vendor Nusantara 12', 'Packaging', 'Jl. Bandung No. 36', 'PIC Vendor 12', '08557488410', 'Komputer dan Aksesoris', 3, 'Aktif', '2025-09-13', 'Catatan vendor ke-12', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(13, 'VDR-013', 'CV Vendor Nusantara 13', 'Jasa IT', 'Jl. Semarang No. 39', 'PIC Vendor 13', '08925148158', 'Mebel Kantor', 3, 'Aktif', '2025-11-28', 'Catatan vendor ke-13', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(14, 'VDR-014', 'PT Vendor Nusantara 14', 'Spare Part', 'Jl. Yogyakarta No. 42', 'PIC Vendor 14', '08937301491', 'Genset dan Panel', 5, 'Aktif', '2025-11-25', 'Catatan vendor ke-14', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(15, 'VDR-015', 'CV Vendor Nusantara 15', 'Logistik', 'Jl. Surabaya No. 45', 'PIC Vendor 15', '08957430766', 'Seragam Karyawan', 4, 'Tidak Aktif', '2025-09-09', 'Catatan vendor ke-15', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(16, 'VDR-016', 'PT Vendor Nusantara 16', 'Maintenance', 'Jl. Medan No. 48', 'PIC Vendor 16', '08225391691', 'Kain Katun', 5, 'Aktif', '2026-03-08', 'Catatan vendor ke-16', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(17, 'VDR-017', 'CV Vendor Nusantara 17', 'Cleaning', 'Jl. Makassar No. 51', 'PIC Vendor 17', '08715317779', 'Kardus dan Label', 5, 'Aktif', '2025-08-15', 'Catatan vendor ke-17', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(18, 'VDR-018', 'PT Vendor Nusantara 18', 'Security', 'Jl. Palembang No. 54', 'PIC Vendor 18', '08750429019', 'Maintenance Sistem', 5, 'Aktif', '2025-08-05', 'Catatan vendor ke-18', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(19, 'VDR-019', 'CV Vendor Nusantara 19', 'Percetakan', 'Jl. Malang No. 57', 'PIC Vendor 19', '08628136399', 'Spare Part Kendaraan', 4, 'Aktif', '2025-08-11', 'Catatan vendor ke-19', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(20, 'VDR-020', 'PT Vendor Nusantara 20', 'ATK', 'Jl. Solo No. 60', 'PIC Vendor 20', '08275688670', 'Pengiriman Barang', 2, 'Tidak Aktif', '2026-05-03', 'Catatan vendor ke-20', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(21, 'VDR-021', 'CV Vendor Nusantara 21', 'Bahan Baku', 'Jl. Jakarta No. 63', 'PIC Vendor 21', '08286141460', 'Servis Mesin', 3, 'Aktif', '2026-07-09', 'Catatan vendor ke-21', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(22, 'VDR-022', 'PT Vendor Nusantara 22', 'Packaging', 'Jl. Bandung No. 66', 'PIC Vendor 22', '08775517365', 'Jasa Kebersihan', 3, 'Aktif', '2025-07-22', 'Catatan vendor ke-22', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(23, 'VDR-023', 'CV Vendor Nusantara 23', 'Jasa IT', 'Jl. Semarang No. 69', 'PIC Vendor 23', '08184079790', 'Jasa Keamanan', 3, 'Aktif', '2026-03-16', 'Catatan vendor ke-23', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(24, 'VDR-024', 'PT Vendor Nusantara 24', 'Spare Part', 'Jl. Yogyakarta No. 72', 'PIC Vendor 24', '08841580666', 'Cetak Dokumen', 5, 'Aktif', '2025-11-23', 'Catatan vendor ke-24', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(25, 'VDR-025', 'CV Vendor Nusantara 25', 'Logistik', 'Jl. Surabaya No. 75', 'PIC Vendor 25', '08333222507', 'Alat Tulis Kantor', 2, 'Tidak Aktif', '2025-10-14', 'Catatan vendor ke-25', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(26, 'VDR-026', 'PT Vendor Nusantara 26', 'Maintenance', 'Jl. Medan No. 78', 'PIC Vendor 26', '08390970716', 'Cat dan Kimia', 2, 'Aktif', '2026-03-10', 'Catatan vendor ke-26', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(27, 'VDR-027', 'CV Vendor Nusantara 27', 'Cleaning', 'Jl. Makassar No. 81', 'PIC Vendor 27', '08534821977', 'Komputer dan Aksesoris', 5, 'Aktif', '2025-08-01', 'Catatan vendor ke-27', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(28, 'VDR-028', 'PT Vendor Nusantara 28', 'Security', 'Jl. Palembang No. 84', 'PIC Vendor 28', '08632887770', 'Mebel Kantor', 3, 'Aktif', '2025-09-08', 'Catatan vendor ke-28', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(29, 'VDR-029', 'CV Vendor Nusantara 29', 'Percetakan', 'Jl. Malang No. 87', 'PIC Vendor 29', '08573217024', 'Genset dan Panel', 4, 'Aktif', '2025-07-10', 'Catatan vendor ke-29', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(30, 'VDR-030', 'PT Vendor Nusantara 30', 'ATK', 'Jl. Solo No. 90', 'PIC Vendor 30', '08977357298', 'Seragam Karyawan', 5, 'Tidak Aktif', '2025-09-19', 'Catatan vendor ke-30', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(31, 'VDR-031', 'CV Vendor Nusantara 31', 'Bahan Baku', 'Jl. Jakarta No. 93', 'PIC Vendor 31', '08414226748', 'Kain Katun', 5, 'Aktif', '2025-09-10', 'Catatan vendor ke-31', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(32, 'VDR-032', 'PT Vendor Nusantara 32', 'Packaging', 'Jl. Bandung No. 96', 'PIC Vendor 32', '08946073627', 'Kardus dan Label', 3, 'Aktif', '2025-12-29', 'Catatan vendor ke-32', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(33, 'VDR-033', 'CV Vendor Nusantara 33', 'Jasa IT', 'Jl. Semarang No. 99', 'PIC Vendor 33', '08522452912', 'Maintenance Sistem', 5, 'Aktif', '2025-12-04', 'Catatan vendor ke-33', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(34, 'VDR-034', 'PT Vendor Nusantara 34', 'Spare Part', 'Jl. Yogyakarta No. 102', 'PIC Vendor 34', '08595996802', 'Spare Part Kendaraan', 4, 'Aktif', '2025-08-29', 'Catatan vendor ke-34', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(35, 'VDR-035', 'CV Vendor Nusantara 35', 'Logistik', 'Jl. Surabaya No. 105', 'PIC Vendor 35', '08749200782', 'Pengiriman Barang', 2, 'Tidak Aktif', '2025-08-21', 'Catatan vendor ke-35', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(36, 'VDR-036', 'PT Vendor Nusantara 36', 'Maintenance', 'Jl. Medan No. 108', 'PIC Vendor 36', '08251751608', 'Servis Mesin', 3, 'Aktif', '2026-02-14', 'Catatan vendor ke-36', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(37, 'VDR-037', 'CV Vendor Nusantara 37', 'Cleaning', 'Jl. Makassar No. 111', 'PIC Vendor 37', '08661746544', 'Jasa Kebersihan', 2, 'Aktif', '2025-12-29', 'Catatan vendor ke-37', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(38, 'VDR-038', 'PT Vendor Nusantara 38', 'Security', 'Jl. Palembang No. 114', 'PIC Vendor 38', '08171919981', 'Jasa Keamanan', 2, 'Aktif', '2025-11-05', 'Catatan vendor ke-38', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(39, 'VDR-039', 'CV Vendor Nusantara 39', 'Percetakan', 'Jl. Malang No. 117', 'PIC Vendor 39', '08854235856', 'Cetak Dokumen', 3, 'Aktif', '2026-04-09', 'Catatan vendor ke-39', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(40, 'VDR-040', 'PT Vendor Nusantara 40', 'ATK', 'Jl. Solo No. 120', 'PIC Vendor 40', '08169498860', 'Alat Tulis Kantor', 5, 'Tidak Aktif', '2026-01-28', 'Catatan vendor ke-40', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(41, 'VDR-041', 'CV Vendor Nusantara 41', 'Bahan Baku', 'Jl. Jakarta No. 123', 'PIC Vendor 41', '08699781931', 'Cat dan Kimia', 3, 'Aktif', '2025-09-23', 'Catatan vendor ke-41', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(42, 'VDR-042', 'PT Vendor Nusantara 42', 'Packaging', 'Jl. Bandung No. 126', 'PIC Vendor 42', '08102585037', 'Komputer dan Aksesoris', 2, 'Aktif', '2026-06-18', 'Catatan vendor ke-42', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(43, 'VDR-043', 'CV Vendor Nusantara 43', 'Jasa IT', 'Jl. Semarang No. 129', 'PIC Vendor 43', '08777355311', 'Mebel Kantor', 4, 'Aktif', '2025-12-18', 'Catatan vendor ke-43', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(44, 'VDR-044', 'PT Vendor Nusantara 44', 'Spare Part', 'Jl. Yogyakarta No. 132', 'PIC Vendor 44', '08629293502', 'Genset dan Panel', 4, 'Aktif', '2026-05-26', 'Catatan vendor ke-44', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(45, 'VDR-045', 'CV Vendor Nusantara 45', 'Logistik', 'Jl. Surabaya No. 135', 'PIC Vendor 45', '08521901633', 'Seragam Karyawan', 5, 'Tidak Aktif', '2026-01-08', 'Catatan vendor ke-45', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(46, 'VDR-046', 'PT Vendor Nusantara 46', 'Maintenance', 'Jl. Medan No. 138', 'PIC Vendor 46', '08584046157', 'Kain Katun', 5, 'Aktif', '2025-07-29', 'Catatan vendor ke-46', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(47, 'VDR-047', 'CV Vendor Nusantara 47', 'Cleaning', 'Jl. Makassar No. 141', 'PIC Vendor 47', '08276914710', 'Kardus dan Label', 4, 'Aktif', '2026-06-21', 'Catatan vendor ke-47', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(48, 'VDR-048', 'PT Vendor Nusantara 48', 'Security', 'Jl. Palembang No. 144', 'PIC Vendor 48', '08860893014', 'Maintenance Sistem', 2, 'Aktif', '2026-04-21', 'Catatan vendor ke-48', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(49, 'VDR-049', 'CV Vendor Nusantara 49', 'Percetakan', 'Jl. Malang No. 147', 'PIC Vendor 49', '08250982501', 'Spare Part Kendaraan', 4, 'Aktif', '2025-10-01', 'Catatan vendor ke-49', '2026-07-09 17:56:57', '2026-07-09 17:56:57'),
(50, 'VDR-050', 'PT Vendor Nusantara 50', 'ATK', 'Jl. Solo No. 150', 'PIC Vendor 50', '08376842924', 'Pengiriman Barang', 3, 'Tidak Aktif', '2026-07-08', 'Catatan vendor ke-50', '2026-07-09 17:56:57', '2026-07-09 17:56:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendor_performances`
--

CREATE TABLE `vendor_performances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `total_order` int(11) NOT NULL DEFAULT 0,
  `ketepatan_waktu` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'persen',
  `kualitas_barang` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'skala 1-100',
  `komplain` int(11) NOT NULL DEFAULT 0,
  `penilaian_akhir` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'skala 1-100',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendor_performances`
--

INSERT INTO `vendor_performances` (`id`, `vendor`, `total_order`, `ketepatan_waktu`, `kualitas_barang`, `komplain`, `penilaian_akhir`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PT Maju Jaya', 48, 91.67, 88.50, 3, 89.20, 'Vendor terpercaya, pengiriman konsisten', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'CV Berkah Abadi', 35, 74.29, 80.00, 7, 76.50, 'Perlu peningkatan ketepatan waktu pengiriman', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'PT Sumber Makmur', 60, 95.00, 92.30, 2, 93.50, 'Performa terbaik, kualitas produk sangat baik', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'UD Sejahtera', 22, 63.64, 70.00, 9, 65.80, 'Banyak komplain, perlu evaluasi ulang kontrak', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'PT Indo Supplier', 41, 82.93, 85.00, 5, 83.20, 'Performa stabil, harga kompetitif', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
  `minimal_order` int(11) NOT NULL DEFAULT 1,
  `lead_time` int(11) NOT NULL DEFAULT 0 COMMENT 'dalam hari',
  `tanggal_berlaku` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendor_pricelists`
--

INSERT INTO `vendor_pricelists` (`id`, `vendor`, `kode_barang`, `nama_barang`, `harga_per_unit`, `satuan`, `diskon`, `minimal_order`, `lead_time`, `tanggal_berlaku`, `created_at`, `updated_at`) VALUES
(1, 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 990455, 'pcs', 1.23, 16, 18, '2026-05-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(2, 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 919847, 'liter', 7.59, 2, 26, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(3, 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 298938, 'unit', 20.29, 16, 22, '2026-05-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(4, 'UD Sejahtera', 'BRG-004', 'Filter Udara', 857759, 'set', 4.67, 29, 5, '2026-06-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(5, 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 1425385, 'buah', 12.64, 23, 21, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(6, 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 246813, 'pcs', 2.52, 3, 10, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(7, 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 1324539, 'liter', 9.03, 11, 7, '2026-07-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(8, 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 909162, 'unit', 9.84, 17, 26, '2026-07-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(9, 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 479787, 'set', 0.49, 20, 13, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(10, 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 1286316, 'buah', 17.22, 24, 26, '2026-05-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(11, 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 193562, 'pcs', 7.79, 32, 9, '2026-05-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(12, 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 646440, 'liter', 17.99, 41, 23, '2026-07-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(13, 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 1219358, 'unit', 17.05, 48, 20, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(14, 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 740241, 'set', 14.57, 43, 3, '2026-06-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(15, 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 1479620, 'buah', 4.88, 49, 20, '2026-06-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(16, 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 999737, 'pcs', 10.00, 14, 5, '2026-07-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(17, 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 1226661, 'liter', 14.78, 32, 30, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(18, 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 263524, 'unit', 15.65, 29, 26, '2026-05-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(19, 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 908831, 'set', 9.60, 23, 10, '2026-07-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(20, 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 194749, 'buah', 13.19, 45, 22, '2026-06-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(21, 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 193703, 'pcs', 12.97, 45, 15, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(22, 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 867474, 'liter', 9.74, 2, 26, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(23, 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 1109544, 'unit', 18.79, 24, 12, '2026-06-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(24, 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 288741, 'set', 17.71, 1, 8, '2026-04-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59'),
(25, 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 685294, 'buah', 4.20, 42, 7, '2026-05-01', '2026-07-09 17:56:59', '2026-07-09 17:56:59');

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
(1, 'VA-73730976', 1, NULL, NULL, 'bca', 1500000.00, 0.00, 'Pending', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56'),
(2, 'VA-25041684', 1, NULL, NULL, 'bni', 2500000.00, 2500000.00, 'paid', NULL, '2026-07-09 17:56:56', '2026-07-09 17:56:56');

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
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchaseros_no_pr_unique` (`no_pr`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `inv_kontraks`
--
ALTER TABLE `inv_kontraks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `inv_penawarans`
--
ALTER TABLE `inv_penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `inv_penawaran_items`
--
ALTER TABLE `inv_penawaran_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `member_kendaraan`
--
ALTER TABLE `member_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

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
