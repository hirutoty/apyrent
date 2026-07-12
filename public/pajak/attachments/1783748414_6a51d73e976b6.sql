-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Jul 2026 pada 05.41
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
(1, 'ADS001', 'Google Ads - Rental Mobil Jakarta', 'Google Ads', '2026-07-01', 500000.00, 350, 28, 15000000.00, 70000000.00, '367%', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'ADS002', 'Facebook Ads - Awareness Campaign', 'Meta Ads', '2026-07-05', 300000.00, 520, 35, 9000000.00, 52500000.00, '483%', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'AFI001', 'Referral Teman', 'REF-APY001', 50000.00, 'Rp 75.000 kredit', '2026-12-31', 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'AFI002', 'Corporate Partner', 'REF-CORP001', 100000.00, 'Komisi 5%', '2026-12-31', 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'Pembangunan Sistem Rental', 'Development', 15000000.00, 6000000.00, 9000000.00, 40.00, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(2, 'Server & Hosting', 'Infrastructure', 5000000.00, 2500000.00, 2500000.00, 50.00, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(3, 'Pembelian GPS', 'Operasional', 10000000.00, 7500000.00, 2500000.00, 75.00, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(4, 'Promosi Rental', 'Marketing', 7000000.00, 3000000.00, 4000000.00, 42.86, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(5, 'Service Kendaraan', 'Maintenance', 12000000.00, 4500000.00, 7500000.00, 37.50, '2026-07-09 03:36:17', '2026-07-09 03:36:17');

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
(1, 'PO-001', 1, 'Supervisor Pembelian', 'Budi Santoso', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'PO-001', 2, 'Manager Operasional', 'Rina Wulandari', '2026-06-09', 'Approved', 'Review urutan 2 untuk PO-001', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'PO-002', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-06-26', 'Rejected', 'Review urutan 1 untuk PO-002', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'PO-002', 2, 'Manager Operasional', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'PO-003', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-05-24', 'Approved', 'Review urutan 1 untuk PO-003', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(6, 'PO-003', 2, 'Manager Operasional', 'Budi Santoso', '2026-07-07', 'Rejected', 'Review urutan 2 untuk PO-003', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(7, 'PO-004', 1, 'Supervisor Pembelian', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(8, 'PO-004', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-06-16', 'Approved', 'Review urutan 2 untuk PO-004', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(9, 'PO-005', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-05-29', 'Rejected', 'Review urutan 1 untuk PO-005', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(10, 'PO-005', 2, 'Manager Operasional', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(11, 'PO-006', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-05-21', 'Approved', 'Review urutan 1 untuk PO-006', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(12, 'PO-006', 2, 'Manager Operasional', 'Rina Wulandari', '2026-05-18', 'Rejected', 'Review urutan 2 untuk PO-006', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(13, 'PO-007', 1, 'Supervisor Pembelian', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(14, 'PO-007', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-06-18', 'Approved', 'Review urutan 2 untuk PO-007', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(15, 'PO-008', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-05-12', 'Rejected', 'Review urutan 1 untuk PO-008', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(16, 'PO-008', 2, 'Manager Operasional', 'Budi Santoso', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(17, 'PO-009', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-06-24', 'Approved', 'Review urutan 1 untuk PO-009', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(18, 'PO-009', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-06-01', 'Rejected', 'Review urutan 2 untuk PO-009', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(19, 'PO-010', 1, 'Supervisor Pembelian', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(20, 'PO-010', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-06-05', 'Approved', 'Review urutan 2 untuk PO-010', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(21, 'PO-011', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-05-27', 'Rejected', 'Review urutan 1 untuk PO-011', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(22, 'PO-011', 2, 'Manager Operasional', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(23, 'PO-012', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-05-29', 'Approved', 'Review urutan 1 untuk PO-012', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(24, 'PO-012', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-07-08', 'Rejected', 'Review urutan 2 untuk PO-012', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(25, 'PO-013', 1, 'Supervisor Pembelian', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(26, 'PO-013', 2, 'Manager Operasional', 'Budi Santoso', '2026-05-24', 'Approved', 'Review urutan 2 untuk PO-013', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(27, 'PO-014', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-06-10', 'Rejected', 'Review urutan 1 untuk PO-014', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(28, 'PO-014', 2, 'Manager Operasional', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(29, 'PO-015', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-05-15', 'Approved', 'Review urutan 1 untuk PO-015', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(30, 'PO-015', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-06-16', 'Rejected', 'Review urutan 2 untuk PO-015', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 1, 'BCA Insurance', 'Jl. Sudirman No. 10 Jakarta', 'Andi Saputra', '081234567890', 'Bengkel Maju Motor', '082233445566', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(2, 1, 'Adira Insurance', 'Jl. Malioboro No. 20 Yogyakarta', 'Budi Hartono', '081298765432', 'Bengkel Jaya Abadi', '085566778899', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(3, 1, 'ACA Insurance', 'Jl. Pemuda No. 12 Semarang', 'Siti Rahma', '087712345678', 'Bengkel Berkah Mobil', '081122334455', '2026-07-09 03:36:17', '2026-07-09 03:36:17');

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
(1, 1, 1, 1, 'expired', '2024-11-09', '2025-02-09', 3, 3500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(2, 2, 2, 2, 'expired', '2025-11-09', '2026-05-09', 6, 23500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(3, 3, 3, 3, 'aktif', '2025-09-09', '2026-09-09', 12, 15500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(4, 4, 1, 1, 'aktif', '2026-03-09', '2028-03-09', 24, 17000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(5, 5, 2, 2, 'expired', '2025-02-09', '2025-05-09', 3, 18000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(6, 6, 3, 3, 'expired', '2025-07-09', '2026-01-09', 6, 17000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(7, 7, 1, 1, 'aktif', '2025-10-09', '2026-10-09', 12, 8000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(8, 8, 2, 2, 'aktif', '2025-02-09', '2027-02-09', 24, 5500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(9, 9, 3, 3, 'expired', '2026-04-09', '2026-07-09', 3, 19500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(10, 10, 1, 1, 'expired', '2025-12-09', '2026-06-09', 6, 10500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(11, 11, 2, 2, 'expired', '2025-07-09', '2026-07-09', 12, 7500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(12, 12, 3, 3, 'aktif', '2025-07-09', '2027-07-09', 24, 14000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(13, 13, 1, 1, 'expired', '2026-02-09', '2026-05-09', 3, 5500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(14, 14, 2, 2, 'expired', '2025-11-09', '2026-05-09', 6, 9500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(15, 15, 3, 3, 'expired', '2025-03-09', '2026-03-09', 12, 6500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(16, 16, 1, 1, 'aktif', '2024-11-09', '2026-11-09', 24, 21500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(17, 17, 2, 2, 'expired', '2025-06-09', '2025-09-09', 3, 22000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(18, 18, 3, 3, 'expired', '2025-11-09', '2026-05-09', 6, 24500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(19, 19, 1, 1, 'expired', '2025-05-09', '2026-05-09', 12, 21000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(20, 20, 2, 2, 'aktif', '2026-03-09', '2028-03-09', 24, 16000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(21, 21, 3, 3, 'aktif', '2026-06-09', '2026-09-09', 3, 5000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(22, 22, 1, 1, 'aktif', '2026-04-09', '2026-10-09', 6, 23500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(23, 23, 2, 2, 'aktif', '2025-10-09', '2026-10-09', 12, 14000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(24, 24, 3, 3, 'aktif', '2025-06-09', '2027-06-09', 24, 16000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(25, 25, 1, 1, 'expired', '2025-06-09', '2025-09-09', 3, 21500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(26, 26, 2, 2, 'expired', '2025-06-09', '2025-12-09', 6, 14000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(27, 27, 3, 3, 'aktif', '2026-02-09', '2027-02-09', 12, 13500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(28, 28, 1, 1, 'aktif', '2025-03-09', '2027-03-09', 24, 20000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(29, 29, 2, 2, 'expired', '2025-08-09', '2025-11-09', 3, 6000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(30, 30, 3, 3, 'expired', '2025-03-09', '2025-09-09', 6, 21000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(31, 31, 1, 1, 'expired', '2025-07-09', '2026-07-09', 12, 2500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(32, 32, 2, 2, 'aktif', '2026-04-09', '2028-04-09', 24, 21000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(33, 33, 3, 3, 'expired', '2024-11-09', '2025-02-09', 3, 3500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(34, 34, 1, 1, 'expired', '2025-06-09', '2025-12-09', 6, 11000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(35, 35, 2, 2, 'aktif', '2025-10-09', '2026-10-09', 12, 16000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(36, 36, 3, 3, 'aktif', '2026-07-09', '2028-07-09', 24, 5000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(37, 37, 1, 1, 'expired', '2026-03-09', '2026-06-09', 3, 2500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(38, 38, 2, 2, 'expired', '2025-11-09', '2026-05-09', 6, 3500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(39, 39, 3, 3, 'aktif', '2026-07-09', '2027-07-09', 12, 16000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(40, 40, 1, 1, 'aktif', '2025-12-09', '2027-12-09', 24, 3500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(41, 41, 2, 2, 'expired', '2025-04-09', '2025-07-09', 3, 24000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(42, 42, 3, 3, 'aktif', '2026-03-09', '2026-09-09', 6, 10000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(43, 43, 1, 1, 'aktif', '2026-03-09', '2027-03-09', 12, 7500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(44, 44, 2, 2, 'aktif', '2025-05-09', '2027-05-09', 24, 17000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(45, 45, 3, 3, 'aktif', '2026-05-09', '2026-08-09', 3, 10500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(46, 46, 1, 1, 'expired', '2025-09-09', '2026-03-09', 6, 18000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(47, 47, 2, 2, 'aktif', '2026-07-09', '2027-07-09', 12, 10000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(48, 48, 3, 3, 'aktif', '2025-06-09', '2027-06-09', 24, 11500000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(49, 49, 1, 1, 'expired', '2025-07-09', '2025-10-09', 3, 11000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(50, 50, 2, 2, 'aktif', '2026-05-09', '2026-11-09', 6, 18000000.00, NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bukubesars`
--

INSERT INTO `bukubesars` (`id`, `kode_jurnal`, `transaksi`, `kategori`, `tanggal`, `debit`, `kredit`, `saldo`, `aktivitas`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'JRNL-001', 'Pemasukan Rental Harian', 'Pendapatan', '2026-01-28', 1500000, 0, 1500000, 'rental', 'Pembayaran rental harian dari customer', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, 'JRNL-002', 'Pemasukan Rental Mingguan', 'Pendapatan', '2026-03-27', 3500000, 0, 5000000, 'rental', 'Pembayaran rental mingguan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(3, 'JRNL-003', 'Penerimaan DP Rental', 'Pendapatan', '2026-02-04', 1000000, 0, 6000000, 'rental', 'DP rental kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(4, 'JRNL-004', 'Pelunasan Rental', 'Pendapatan', '2026-04-26', 2000000, 0, 8000000, 'rental', 'Pelunasan biaya rental', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(5, 'JRNL-005', 'Penerimaan Denda Keterlambatan', 'Pendapatan', '2026-07-05', 250000, 0, 8250000, 'denda', 'Denda pengembalian terlambat', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(6, 'JRNL-006', 'Penerimaan Deposit Customer', 'Pendapatan', '2026-01-16', 500000, 0, 8750000, 'deposit', 'Deposit jaminan kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(7, 'JRNL-007', 'Pendapatan Biaya Tambahan', 'Pendapatan', '2026-05-26', 200000, 0, 8950000, 'rental', 'Biaya supir tambahan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(8, 'JRNL-008', 'Penerimaan Sewa Jangka Panjang', 'Pendapatan', '2026-05-10', 15000000, 0, 23950000, 'rental', 'Kontrak sewa bulanan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(9, 'JRNL-009', 'Pendapatan Lain-lain', 'Pendapatan', '2026-03-28', 350000, 0, 24300000, 'lain', 'Pendapatan di luar operasional utama', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(10, 'JRNL-010', 'Penerimaan Invoice Kontrak', 'Pendapatan', '2026-05-27', 8000000, 0, 32300000, 'invoice', 'Pembayaran invoice kontrak korporat', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(11, 'JRNL-011', 'Biaya Servis Berkala', 'Beban', '2026-02-09', 0, 500000, 31800000, 'service', 'Servis rutin kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(12, 'JRNL-012', 'Biaya Ganti Oli', 'Beban', '2026-05-01', 0, 150000, 31650000, 'service', 'Penggantian oli mesin', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(13, 'JRNL-013', 'Pembayaran Pajak Kendaraan', 'Beban', '2026-01-21', 0, 3500000, 28150000, 'pajak', 'Pajak tahunan kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(14, 'JRNL-014', 'Premi Asuransi Kendaraan', 'Beban', '2026-07-02', 0, 5000000, 23150000, 'asuransi', 'Pembayaran premi asuransi', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(15, 'JRNL-015', 'Biaya Sewa GPS', 'Beban', '2026-04-28', 0, 300000, 22850000, 'gps', 'Biaya langganan GPS tracker', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(16, 'JRNL-016', 'Biaya Bahan Bakar', 'Beban', '2026-07-06', 0, 800000, 22050000, 'operasional', 'Pembelian bahan bakar kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(17, 'JRNL-017', 'Biaya KIR Kendaraan', 'Beban', '2026-02-16', 0, 200000, 21850000, 'kir', 'Biaya uji KIR kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(18, 'JRNL-018', 'Biaya Gaji Karyawan', 'Beban', '2026-04-07', 0, 5000000, 16850000, 'gaji', 'Gaji karyawan bulan ini', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(19, 'JRNL-019', 'Biaya Pembelian Spare Part', 'Beban', '2026-06-13', 0, 1200000, 15650000, 'service', 'Pembelian ban dan kampas rem', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(20, 'JRNL-020', 'Biaya Listrik dan Air', 'Beban', '2026-01-26', 0, 450000, 15200000, 'operasional', 'Tagihan utilitas kantor', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(21, 'JRNL-021', 'Pembelian Kendaraan Baru', 'Aktiva', '2026-05-15', 250000000, 0, 265200000, 'pembelian', 'Penambahan aset kendaraan baru', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(22, 'JRNL-022', 'Kas di Tangan', 'Aktiva', '2026-05-09', 10000000, 0, 275200000, 'kas', 'Saldo kas operasional', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(23, 'JRNL-023', 'Kas di Bank', 'Aktiva', '2026-04-15', 50000000, 0, 325200000, 'kas', 'Saldo rekening bank perusahaan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(24, 'JRNL-024', 'Piutang Rental', 'Aktiva', '2026-04-08', 7500000, 0, 332700000, 'rental', 'Tagihan belum dibayar customer', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(25, 'JRNL-025', 'Perlengkapan Kantor', 'Aktiva', '2026-04-15', 2500000, 0, 335200000, 'operasional', 'Inventaris perlengkapan kantor', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(26, 'JRNL-026', 'Peralatan Workshop', 'Aktiva', '2026-05-03', 15000000, 0, 350200000, 'service', 'Alat bengkel dan servis kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(27, 'JRNL-027', 'Deposit GPS Provider', 'Aktiva', '2026-01-24', 1000000, 0, 351200000, 'gps', 'Deposit ke penyedia GPS', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(28, 'JRNL-028', 'Persediaan Sparepart', 'Aktiva', '2026-03-10', 3000000, 0, 354200000, 'service', 'Stok sparepart di gudang', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(29, 'JRNL-029', 'Gedung Kantor', 'Aktiva', '2026-05-23', 500000000, 0, 854200000, 'aset', 'Nilai gedung kantor operasional', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(30, 'JRNL-030', 'Kendaraan Operasional', 'Aktiva', '2026-03-20', 180000000, 0, 1034200000, 'aset', 'Nilai armada kendaraan sewa', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(31, 'JRNL-031', 'Modal Awal Pemilik', 'Modal', '2026-06-29', 0, 500000000, 534200000, 'modal', 'Setoran modal awal perusahaan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(32, 'JRNL-032', 'Tambahan Modal Investasi', 'Modal', '2026-06-15', 0, 100000000, 434200000, 'modal', 'Investasi tambahan dari pemilik', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(33, 'JRNL-033', 'Laba Ditahan Tahun Lalu', 'Modal', '2026-07-03', 0, 75000000, 359200000, 'modal', 'Akumulasi laba yang tidak dibagikan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(34, 'JRNL-034', 'Dividen Dibayarkan', 'Modal', '2026-04-21', 25000000, 0, 384200000, 'modal', 'Pembagian dividen kepada pemilik', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(35, 'JRNL-035', 'Laba Bersih Periode Berjalan', 'Modal', '2026-06-08', 0, 45000000, 339200000, 'modal', 'Laba bersih periode ini', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(36, 'JRNL-036', 'Cadangan Umum', 'Modal', '2026-03-04', 0, 10000000, 329200000, 'modal', 'Cadangan dana untuk ekspansi', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(37, 'JRNL-037', 'Prive Pemilik', 'Modal', '2026-06-04', 5000000, 0, 334200000, 'modal', 'Pengambilan pribadi pemilik', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(38, 'JRNL-038', 'Revaluasi Aset Kendaraan', 'Modal', '2026-03-16', 0, 20000000, 314200000, 'aset', 'Kenaikan nilai aset kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(39, 'JRNL-039', 'Modal Kerja Tambahan', 'Modal', '2026-06-24', 0, 30000000, 284200000, 'modal', 'Penambahan modal kerja operasional', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(40, 'JRNL-040', 'Saldo Modal Berjalan', 'Modal', '2026-02-14', 0, 15000000, 269200000, 'modal', 'Saldo modal per periode ini', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(41, 'JRNL-041', 'Hutang Bank Jangka Panjang', 'Kewajiban', '2026-02-11', 0, 200000000, 69200000, 'hutang', 'Pinjaman bank untuk pembelian kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(42, 'JRNL-042', 'Hutang Leasing Kendaraan', 'Kewajiban', '2026-03-16', 0, 120000000, -50800000, 'hutang', 'Cicilan leasing kendaraan baru', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(43, 'JRNL-043', 'Hutang Vendor Sparepart', 'Kewajiban', '2026-04-09', 0, 8000000, -58800000, 'hutang', 'Tagihan belum dibayar ke vendor', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(44, 'JRNL-044', 'Hutang Pajak', 'Kewajiban', '2026-06-12', 0, 5000000, -63800000, 'pajak', 'Kewajiban pajak yang belum dibayar', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(45, 'JRNL-045', 'Hutang Gaji Karyawan', 'Kewajiban', '2026-03-22', 0, 15000000, -78800000, 'gaji', 'Gaji bulan lalu yang belum dibayar', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(46, 'JRNL-046', 'Hutang GPS Provider', 'Kewajiban', '2026-04-11', 0, 900000, -79700000, 'gps', 'Tagihan langganan GPS yang tertunda', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(47, 'JRNL-047', 'Hutang Asuransi', 'Kewajiban', '2026-04-29', 0, 3000000, -82700000, 'asuransi', 'Premi asuransi yang belum dibayar', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(48, 'JRNL-048', 'Deposit Customer Diterima', 'Kewajiban', '2026-07-06', 0, 4500000, -87200000, 'deposit', 'Deposit yang harus dikembalikan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(49, 'JRNL-049', 'Hutang Listrik dan Utilitas', 'Kewajiban', '2026-03-15', 0, 750000, -87950000, 'operasional', 'Tagihan utilitas yang belum dibayar', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(50, 'JRNL-050', 'Hutang Jangka Pendek Lainnya', 'Kewajiban', '2026-03-27', 0, 2000000, -89950000, 'hutang', 'Kewajiban jangka pendek lain-lain', '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
(1, 'BUPOT-001', '2026-06-29', 'PPh21', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '09.876.543.2-109.000', 'Budi Santoso', 5000000.00, 0.05, 250000.00, 'Approve', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, 'BUPOT-002', '2026-07-01', 'PPh23', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '08.765.432.1-000.000', 'CV Sinar Abadi', 3000000.00, 0.02, 60000.00, 'Approve', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(3, 'BUPOT-003', '2026-07-04', 'PPh26', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '07.654.321.0-999.000', 'UD Jaya Motor', 10000000.00, 0.10, 1000000.00, 'Draft', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
(1, 'PRO-001', 'Budi Santoso', 'PT Maju Bersama', NULL, '0812-1111-1111', 'Prospek', NULL, 'Aktif', 'Andi', '2026-01-10', 'Butuh armada 5 unit', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'PRO-002', 'Siti Rahayu', 'CV Karya Indah', NULL, '0813-2222-2222', 'Negosiasi', NULL, 'Aktif', 'Budi', '2026-02-05', 'Diskusi harga sudah selesai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'PRO-003', 'Ahmad Fauzi', 'PT Sejahtera Abadi', NULL, '0814-3333-3333', 'Closing', NULL, 'Aktif', 'Cici', '2026-02-20', 'Kontrak siap ditandatangani', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'PRO-004', 'Dewi Lestari', 'PT Global Trans', NULL, '0815-4444-4444', 'Prospek', NULL, 'Aktif', 'Andi', '2026-03-01', 'Masih dalam penjajakan', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'PRO-005', 'Rudi Hartono', 'CV Jaya Mandiri', NULL, '0816-5555-5555', 'Negosiasi', NULL, 'Aktif', 'Dani', '2026-03-15', 'Negosiasi tenor kontrak', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'PRO-006', 'Lia Permata', 'PT Nusantara Raya', NULL, '0817-6666-6666', 'Closing', NULL, 'Aktif', 'Budi', '2026-04-02', 'Deal 3 unit minibus', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'PRO-007', 'Hendra Wijaya', 'PT Sinar Harapan', NULL, '0818-7777-7777', 'Prospek', NULL, 'Tidak Aktif', 'Cici', '2026-04-10', 'Tidak merespon lagi', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'PRO-008', 'Maya Anggraini', 'CV Mitra Logistik', NULL, '0819-8888-8888', 'Negosiasi', NULL, 'Aktif', 'Andi', '2026-05-01', 'Menunggu approval direksi', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(9, 'PRO-009', 'Fajar Nugroho', 'PT Berlian Trans', NULL, '0821-9999-9999', 'Closing', NULL, 'Aktif', 'Dani', '2026-05-20', 'Siap kontrak', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(10, 'PRO-010', 'Indah Kusuma', 'PT Prima Raya', NULL, '0822-1010-1010', 'Prospek', NULL, 'Aktif', 'Budi', '2026-06-05', 'Prospek baru dari referral', '2026-07-09 03:36:20', '2026-07-09 03:36:20');

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
(1, 'Rini Apriani', 'Cuti Tahunan', '2026-03-29', '2026-04-10', 13, 'Keperluan keluarga', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(2, 'Eko Prasetyo', 'Cuti Sakit', '2026-06-23', '2026-06-29', 7, 'Pemulihan kesehatan', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(3, 'Rizky Fadillah', 'Cuti Melahirkan', '2026-06-03', '2026-06-12', 10, 'Acara pernikahan', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(4, 'Yusuf Hidayat', 'Izin Pribadi', '2026-03-14', '2026-03-23', 10, 'Mengurus administrasi', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(5, 'Wahyu Nugroho', 'Cuti Bersama', '2026-02-17', '2026-03-02', 14, 'Liburan keluarga', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(6, 'Fitri Handayani', 'Cuti Tahunan', '2026-03-10', '2026-03-11', 2, 'Cuti bersama hari raya', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(7, 'Teguh Santosa', 'Cuti Sakit', '2026-02-10', '2026-02-11', 2, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(8, 'Arif Budiman', 'Cuti Melahirkan', '2026-06-30', '2026-07-10', 11, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(9, 'Dewi Kusuma', 'Izin Pribadi', '2026-06-05', '2026-06-07', 3, 'Keperluan keluarga', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(10, 'Linda Permata', 'Cuti Bersama', '2026-03-26', '2026-03-29', 4, 'Pemulihan kesehatan', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(11, 'Hendra Gunawan', 'Cuti Tahunan', '2026-06-23', '2026-07-01', 9, 'Acara pernikahan', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(12, 'Dody Kurniawan', 'Cuti Sakit', '2026-01-15', '2026-01-27', 13, 'Mengurus administrasi', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(13, 'Rini Apriani', 'Cuti Melahirkan', '2026-02-14', '2026-02-24', 11, 'Liburan keluarga', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(14, 'Eko Prasetyo', 'Izin Pribadi', '2026-06-06', '2026-06-07', 2, 'Cuti bersama hari raya', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(15, 'Rizky Fadillah', 'Cuti Bersama', '2026-03-15', '2026-03-24', 10, 'Rawat inap di rumah sakit', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(16, 'Yusuf Hidayat', 'Cuti Tahunan', '2026-04-12', '2026-04-13', 2, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(17, 'Wahyu Nugroho', 'Cuti Sakit', '2026-02-14', '2026-02-23', 10, 'Keperluan keluarga', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(18, 'Fitri Handayani', 'Cuti Melahirkan', '2026-04-19', '2026-04-23', 5, 'Pemulihan kesehatan', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(19, 'Teguh Santosa', 'Izin Pribadi', '2026-05-25', '2026-06-01', 8, 'Acara pernikahan', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(20, 'Arif Budiman', 'Cuti Bersama', '2026-01-28', '2026-02-04', 8, 'Mengurus administrasi', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(21, 'Dewi Kusuma', 'Cuti Tahunan', '2026-05-22', '2026-05-22', 1, 'Liburan keluarga', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(22, 'Linda Permata', 'Cuti Sakit', '2026-05-11', '2026-05-22', 12, 'Cuti bersama hari raya', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(23, 'Hendra Gunawan', 'Cuti Melahirkan', '2026-05-17', '2026-05-20', 4, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(24, 'Dody Kurniawan', 'Izin Pribadi', '2026-01-13', '2026-01-26', 14, 'Keperluan mendesak pribadi', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(25, 'Rini Apriani', 'Cuti Bersama', '2026-06-15', '2026-06-16', 2, 'Keperluan keluarga', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(26, 'Eko Prasetyo', 'Cuti Tahunan', '2026-05-04', '2026-05-11', 8, 'Pemulihan kesehatan', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(27, 'Rizky Fadillah', 'Cuti Sakit', '2026-04-11', '2026-04-17', 7, 'Acara pernikahan', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(28, 'Yusuf Hidayat', 'Cuti Melahirkan', '2026-04-09', '2026-04-13', 5, 'Mengurus administrasi', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(29, 'Wahyu Nugroho', 'Izin Pribadi', '2026-05-06', '2026-05-18', 13, 'Liburan keluarga', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(30, 'Fitri Handayani', 'Cuti Bersama', '2026-03-14', '2026-03-19', 6, 'Cuti bersama hari raya', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(31, 'Teguh Santosa', 'Cuti Tahunan', '2026-06-26', '2026-07-04', 9, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(32, 'Arif Budiman', 'Cuti Sakit', '2026-05-26', '2026-06-05', 11, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(33, 'Dewi Kusuma', 'Cuti Melahirkan', '2026-05-11', '2026-05-19', 9, 'Keperluan keluarga', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(34, 'Linda Permata', 'Izin Pribadi', '2026-02-23', '2026-02-27', 5, 'Pemulihan kesehatan', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(35, 'Hendra Gunawan', 'Cuti Bersama', '2026-07-01', '2026-07-12', 12, 'Acara pernikahan', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(36, 'Dody Kurniawan', 'Cuti Tahunan', '2026-04-02', '2026-04-15', 14, 'Mengurus administrasi', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(37, 'Rini Apriani', 'Cuti Sakit', '2026-03-27', '2026-03-28', 2, 'Liburan keluarga', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(38, 'Eko Prasetyo', 'Cuti Melahirkan', '2026-02-08', '2026-02-20', 13, 'Cuti bersama hari raya', 'Disetujui', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(39, 'Rizky Fadillah', 'Izin Pribadi', '2026-02-05', '2026-02-07', 3, 'Rawat inap di rumah sakit', 'Pending', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(40, 'Yusuf Hidayat', 'Cuti Bersama', '2026-02-20', '2026-03-05', 14, 'Keperluan mendesak pribadi', 'Ditolak', '2026-07-09 03:36:25', '2026-07-09 03:36:25');

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
(1, '2025-01-05', 'Web Application', 'SQL Injection pada form login admin panel', 'Critical', 'Input sanitasi dan prepared statement diterapkan', 'Resolved', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, '2025-01-20', 'Jaringan Internal', 'Port 23 (Telnet) masih terbuka di beberapa switch', 'High', 'Disable Telnet dan aktifkan SSH pada semua perangkat jaringan', 'In Progress', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, '2025-02-10', 'Email Server', 'Tidak ada SPF dan DMARC record, rentan email spoofing', 'Medium', 'Konfigurasi SPF, DKIM, dan DMARC pada DNS', 'Open', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, '2025-02-25', 'Endpoint Security', '12 komputer belum update antivirus selama 3 bulan', 'Low', 'Update antivirus terpusat via console management', 'Resolved', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, '2025-03-01', 'Database Server', 'Akun root database dapat diakses dari remote tanpa restriksi IP', 'Critical', 'Batasi akses root hanya dari localhost, buat user terbatas', 'Open', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'Direksi', 'Budi Santoso', '2018-01-02', 3, 'Pimpinan tertinggi perusahaan', 'Aktif', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(2, 'HRD', 'Dewi Kusuma', '2018-06-01', 8, 'Mengelola sumber daya manusia', 'Aktif', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(3, 'IT', 'Hendra Gunawan', '2019-01-15', 6, 'Pengembangan dan pemeliharaan sistem teknologi', 'Aktif', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(4, 'Finance', 'Linda Permata', '2018-06-01', 10, 'Pengelolaan keuangan dan akuntansi', 'Aktif', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(5, 'Operasional', 'Dody Kurniawan', '2019-03-01', 15, 'Pengelolaan operasional lapangan', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(6, 'Marketing', 'Sari Dewanti', '2020-02-01', 7, 'Pemasaran dan promosi produk', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(7, 'Sales', 'Benny Kusuma', '2020-04-01', 12, 'Penjualan dan hubungan pelanggan', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(8, 'Legal', 'Putri Wulandari', '2021-01-01', 4, 'Urusan hukum dan kontrak perusahaan', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(9, 'Procurement', 'Bambang Irawan', '2021-06-01', 5, 'Pengadaan barang dan jasa', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(10, 'Maintenance', 'Suryono Hadi', '2019-07-01', 8, 'Pemeliharaan aset dan kendaraan', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(11, 'R&D', 'Indra Lesmana', '2022-01-01', 4, 'Riset dan pengembangan produk', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(12, 'Customer Service', 'Maya Anggraini', '2020-09-01', 6, 'Layanan pelanggan', 'Aktif', '2026-07-09 03:36:24', '2026-07-09 03:36:24');

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
(1, 'API Backend ERP', 'GitHub Actions', 'Ya', 'Setiap push ke branch main', 'Aktif', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'Frontend Dashboard', 'GitLab CI', 'Ya', 'Setiap merge request approved', 'Aktif', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'Mobile App Driver', 'Bitrise', 'Tidak', 'Manual oleh tim mobile', 'Aktif', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'Laporan Keuangan', 'Jenkins', 'Ya', 'Setiap hari pukul 02.00 WIB', 'Nonaktif', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'Website Company Profile', 'GitHub Actions', 'Ya', 'Setiap push ke branch production', 'Aktif', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'PRJ001', 'RAB Renovasi Pool', 'XLSX', '-', 'Valid', '2025-12-28', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'PRJ001', 'Gambar Desain Konstruksi', 'PDF', '-', 'Valid', '2025-12-30', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(3, 'PRJ001', 'Kontrak Kontraktor', 'PDF', '-', 'Valid', '2026-01-02', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(4, 'PRJ002', 'Spesifikasi Teknis Bus', 'PDF', '-', 'Valid', '2026-01-20', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(5, 'PRJ002', 'Purchase Order Bus', 'PDF', '-', 'Draft', '2026-02-05', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'PRJ003', 'Proposal GPS Monitoring', 'PDF', '-', 'Valid', '2026-01-10', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'PRJ003', 'Kontrak Vendor GPS', 'PDF', '-', 'Valid', '2026-01-14', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'PRJ005', 'PKS Layanan Antar Jemput', 'PDF', '-', 'Valid', '2026-02-12', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'DS-001', 'Regular', 'PT Maju Jaya', 'Spare Part Mesin', 80, 'pcs', 'PT Angin Ribut', '2026-05-06', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'DS-002', 'Express', 'CV Berkah Abadi', 'Oli Mesin', 48, 'liter', 'CV Cahaya Terang', '2026-04-14', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'DS-003', 'Same Day', 'PT Sumber Makmur', 'Ban Kendaraan', 78, 'unit', 'Toko Maju', '2026-06-17', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'DS-004', 'Ekonomi', 'UD Sejahtera', 'Filter Udara', 63, 'set', 'UD Bahagia', '2026-03-12', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'DS-005', 'Regular', 'PT Indo Supplier', 'Aki Kendaraan', 4, 'buah', 'PT Kilat Jaya', '2026-05-04', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(6, 'DS-006', 'Express', 'PT Maju Jaya', 'Kampas Rem', 90, 'pcs', 'CV Sentosa', '2026-05-08', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(7, 'DS-007', 'Same Day', 'CV Berkah Abadi', 'Spare Part Mesin', 25, 'liter', 'PT Angin Ribut', '2026-05-20', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(8, 'DS-008', 'Ekonomi', 'PT Sumber Makmur', 'Oli Mesin', 77, 'unit', 'CV Cahaya Terang', '2026-03-16', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(9, 'DS-009', 'Regular', 'UD Sejahtera', 'Ban Kendaraan', 15, 'set', 'Toko Maju', '2026-05-31', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(10, 'DS-010', 'Express', 'PT Indo Supplier', 'Filter Udara', 4, 'buah', 'UD Bahagia', '2026-03-23', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(11, 'DS-011', 'Same Day', 'PT Maju Jaya', 'Aki Kendaraan', 47, 'pcs', 'PT Kilat Jaya', '2026-03-17', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(12, 'DS-012', 'Ekonomi', 'CV Berkah Abadi', 'Kampas Rem', 13, 'liter', 'CV Sentosa', '2026-03-11', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(13, 'DS-013', 'Regular', 'PT Sumber Makmur', 'Spare Part Mesin', 22, 'unit', 'PT Angin Ribut', '2026-04-08', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(14, 'DS-014', 'Express', 'UD Sejahtera', 'Oli Mesin', 92, 'set', 'CV Cahaya Terang', '2026-06-13', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(15, 'DS-015', 'Same Day', 'PT Indo Supplier', 'Ban Kendaraan', 91, 'buah', 'Toko Maju', '2026-05-01', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(16, 'DS-016', 'Ekonomi', 'PT Maju Jaya', 'Filter Udara', 37, 'pcs', 'UD Bahagia', '2026-04-09', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(17, 'DS-017', 'Regular', 'CV Berkah Abadi', 'Aki Kendaraan', 76, 'liter', 'PT Kilat Jaya', '2026-06-04', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(18, 'DS-018', 'Express', 'PT Sumber Makmur', 'Kampas Rem', 47, 'unit', 'CV Sentosa', '2026-03-31', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(19, 'DS-019', 'Same Day', 'UD Sejahtera', 'Spare Part Mesin', 98, 'set', 'PT Angin Ribut', '2026-05-05', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(20, 'DS-020', 'Ekonomi', 'PT Indo Supplier', 'Oli Mesin', 27, 'buah', 'CV Cahaya Terang', '2026-03-29', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(21, 'DS-021', 'Regular', 'PT Maju Jaya', 'Ban Kendaraan', 87, 'pcs', 'Toko Maju', '2026-04-16', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(22, 'DS-022', 'Express', 'CV Berkah Abadi', 'Filter Udara', 83, 'liter', 'UD Bahagia', '2026-05-13', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(23, 'DS-023', 'Same Day', 'PT Sumber Makmur', 'Aki Kendaraan', 78, 'unit', 'PT Kilat Jaya', '2026-05-02', 'Dikirim', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(24, 'DS-024', 'Ekonomi', 'UD Sejahtera', 'Kampas Rem', 5, 'set', 'CV Sentosa', '2026-04-26', 'Selesai', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(25, 'DS-025', 'Regular', 'PT Indo Supplier', 'Spare Part Mesin', 2, 'buah', 'PT Angin Ribut', '2026-06-09', 'Proses', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, '010.000-26.000001', '2026-06-29', 'Keluaran', '01.234.567.8-901.000', 'PT Rental Maju Jaya', 5000000.00, 550000.00, 0.00, 'terbit', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, '010.000-26.000002', '2026-07-04', 'Masukan', '09.876.543.2-109.000', 'PT Supplier Sparepart', 3000000.00, 330000.00, 0.00, 'draft', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(3, '010.000-26.000003', '2026-07-07', 'Keluaran', '07.111.222.3-444.000', 'CV Transport Jaya', 7500000.00, 825000.00, 0.00, 'terbit', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
(1, 'perusahaan.com', 'GoDaddy', 'aktif', '2026-08-15', 120, 1, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'perusahaan.co.id', 'Rumahweb', 'aktif', '2025-11-30', 45, 1, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'old-brand.com', 'Namecheap', 'nonaktif', '2024-03-10', 0, 0, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'app.perusahaan.com', 'Cloudflare', 'aktif', '2027-01-01', 0, 1, '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 1, 'GPS Tracker Pro', 'Jl. Teknologi No. 1, Jakarta', 'Marketing 1', '08874334131', 'Bengkel GPS 1', '08163120816', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(2, 1, 'Teltonika', 'Jl. Teknologi No. 2, Jakarta', 'Marketing 2', '08648537223', 'Bengkel GPS 2', '08250130019', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(3, 1, 'Queclink', 'Jl. Teknologi No. 3, Jakarta', 'Marketing 3', '08963436116', 'Bengkel GPS 3', '08797629056', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(4, 1, 'Concox', 'Jl. Teknologi No. 4, Jakarta', 'Marketing 4', '08166202502', 'Bengkel GPS 4', '08451452905', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(5, 1, 'Ruptela', 'Jl. Teknologi No. 5, Jakarta', 'Marketing 5', '08117270438', 'Bengkel GPS 5', '08478067412', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(6, 1, 'Coban', 'Jl. Teknologi No. 6, Jakarta', 'Marketing 6', '08484609683', 'Bengkel GPS 6', '08700158962', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(7, 1, 'Gosafe', 'Jl. Teknologi No. 7, Jakarta', 'Marketing 7', '08920304998', 'Bengkel GPS 7', '08660064611', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(8, 1, 'Jointech', 'Jl. Teknologi No. 8, Jakarta', 'Marketing 8', '08715297018', 'Bengkel GPS 8', '08729677838', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(9, 1, 'Meitrack', 'Jl. Teknologi No. 9, Jakarta', 'Marketing 9', '08633302686', 'Bengkel GPS 9', '08241073247', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(10, 1, 'Sinotrack', 'Jl. Teknologi No. 10, Jakarta', 'Marketing 10', '08103489708', 'Bengkel GPS 10', '08980842367', '2026-07-09 03:36:17', '2026-07-09 03:36:17');

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
(1, 1, 1, 'OBD', 'aktif', '2025-05-09', '2026-08-09', 500000, 15, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(2, 2, 2, 'Hardwire', 'nonaktif', '2025-01-09', '2026-02-09', 300000, 13, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(3, 3, 3, 'Magnetic', 'aktif', '2026-06-09', '2027-09-09', 300000, 15, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(4, 4, 4, '4G LTE', 'nonaktif', '2025-05-09', '2026-01-09', 400000, 8, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(5, 5, 5, 'Solar', 'aktif', '2026-02-09', '2027-02-09', 500000, 12, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(6, 6, 6, 'OBD', 'aktif', '2026-02-09', '2027-10-09', 400000, 20, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(7, 7, 7, 'Hardwire', 'aktif', '2025-02-09', '2026-10-09', 100000, 20, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(8, 8, 8, 'Magnetic', 'aktif', '2026-03-09', '2027-05-09', 300000, 14, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(9, 9, 9, '4G LTE', 'nonaktif', '2025-06-09', '2026-06-09', 400000, 12, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(10, 10, 10, 'Solar', 'nonaktif', '2025-06-09', '2026-04-09', 200000, 10, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(11, 11, 1, 'OBD', 'aktif', '2026-04-09', '2027-01-09', 300000, 9, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(12, 12, 2, 'Hardwire', 'nonaktif', '2025-05-09', '2026-02-09', 300000, 9, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(13, 13, 3, 'Magnetic', 'nonaktif', '2025-05-09', '2025-11-09', 300000, 6, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(14, 14, 4, '4G LTE', 'aktif', '2026-01-09', '2027-06-09', 500000, 17, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(15, 15, 5, 'Solar', 'aktif', '2025-07-09', '2027-05-09', 500000, 22, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(16, 16, 6, 'OBD', 'nonaktif', '2025-02-09', '2025-10-09', 200000, 8, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(17, 17, 7, 'Hardwire', 'aktif', '2025-09-09', '2027-08-09', 100000, 23, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(18, 18, 8, 'Magnetic', 'aktif', '2026-01-09', '2027-11-09', 400000, 22, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(19, 19, 9, '4G LTE', 'aktif', '2026-05-09', '2028-05-09', 400000, 24, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(20, 20, 10, 'Solar', 'aktif', '2026-02-09', '2027-11-09', 300000, 21, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(21, 21, 1, 'OBD', 'nonaktif', '2025-01-09', '2026-01-09', 400000, 12, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(22, 22, 2, 'Hardwire', 'nonaktif', '2025-05-09', '2026-04-09', 300000, 11, 'habis', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(23, 23, 3, 'Magnetic', 'aktif', '2025-12-09', '2027-08-09', 300000, 20, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(24, 24, 4, '4G LTE', 'aktif', '2026-05-09', '2027-04-09', 200000, 11, 'aktif', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(25, 25, 5, 'Solar', 'aktif', '2025-06-09', '2027-04-09', 500000, 22, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(26, 26, 6, 'OBD', 'aktif', '2026-04-09', '2028-01-09', 400000, 21, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(27, 27, 7, 'Hardwire', 'aktif', '2026-01-09', '2028-01-09', 200000, 24, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(28, 28, 8, 'Magnetic', 'aktif', '2025-11-09', '2026-09-09', 500000, 10, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(29, 29, 9, '4G LTE', 'aktif', '2025-10-09', '2027-02-09', 100000, 16, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(30, 30, 10, 'Solar', 'aktif', '2025-08-09', '2026-10-09', 300000, 14, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(31, 31, 1, 'OBD', 'aktif', '2025-12-09', '2027-02-09', 100000, 14, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(32, 32, 2, 'Hardwire', 'aktif', '2025-12-09', '2026-10-09', 500000, 10, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(33, 33, 3, 'Magnetic', 'aktif', '2026-03-09', '2028-03-09', 100000, 24, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(34, 34, 4, '4G LTE', 'aktif', '2025-03-09', '2027-02-09', 300000, 23, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(35, 35, 5, 'Solar', 'aktif', '2025-07-09', '2026-09-09', 200000, 14, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(36, 36, 6, 'OBD', 'aktif', '2026-01-09', '2027-12-09', 200000, 23, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(37, 37, 7, 'Hardwire', 'nonaktif', '2025-04-09', '2025-10-09', 300000, 6, 'habis', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(38, 38, 8, 'Magnetic', 'aktif', '2025-12-09', '2026-12-09', 100000, 12, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(39, 39, 9, '4G LTE', 'aktif', '2025-05-09', '2026-10-09', 300000, 17, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(40, 40, 10, 'Solar', 'aktif', '2026-01-09', '2026-12-09', 100000, 11, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(41, 41, 1, 'OBD', 'aktif', '2025-08-09', '2027-05-09', 500000, 21, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(42, 42, 2, 'Hardwire', 'nonaktif', '2025-10-09', '2026-06-09', 200000, 8, 'habis', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(43, 43, 3, 'Magnetic', 'nonaktif', '2025-05-09', '2026-01-09', 200000, 8, 'habis', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(44, 44, 4, '4G LTE', 'nonaktif', '2025-03-09', '2026-06-09', 100000, 15, 'habis', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(45, 45, 5, 'Solar', 'nonaktif', '2025-03-09', '2025-10-09', 200000, 7, 'habis', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(46, 46, 6, 'OBD', 'aktif', '2025-08-09', '2027-06-09', 100000, 22, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(47, 47, 7, 'Hardwire', 'aktif', '2026-06-09', '2027-03-09', 300000, 9, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(48, 48, 8, 'Magnetic', 'aktif', '2025-01-09', '2026-12-09', 300000, 23, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(49, 49, 9, '4G LTE', 'nonaktif', '2025-09-09', '2026-05-09', 500000, 8, 'habis', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(50, 50, 10, 'Solar', 'aktif', '2026-06-09', '2027-09-09', 500000, 15, 'aktif', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18');

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
(1, 'TKT-001', '2025-01-10', 'Finance', 'Laptop tidak bisa menyala setelah update Windows', 'High', 'Resolved', 'Doni Prasetyo', '2 jam', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'TKT-002', '2025-01-15', 'HR', 'Email tidak bisa terkirim ke luar domain', 'Medium', 'Open', 'Siti Rahayu', '4 jam', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'TKT-003', '2025-01-20', 'Sales', 'Koneksi VPN terputus saat WFH', 'Critical', 'In Progress', 'Doni Prasetyo', '30 menit', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'TKT-004', '2025-02-01', 'IT', 'Printer di lantai 3 tidak terdeteksi oleh komputer', 'Low', 'Closed', 'Siti Rahayu', '1 hari', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'TKT-005', '2025-02-10', 'Operasional', 'Sistem ERP lambat saat jam kerja puncak', 'High', 'In Progress', 'Doni Prasetyo', '1 jam', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'Budi Santoso', 'KTP - Budi Santoso', 'KTP', 'hrd_files/budi_santoso/ktp_budi_santoso.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(2, 'Budi Santoso', 'Ijazah - Budi Santoso', 'Ijazah', 'hrd_files/budi_santoso/ijazah_budi_santoso.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(3, 'Budi Santoso', 'SK Pengangkatan - Budi Santoso', 'SK Pengangkatan', 'hrd_files/budi_santoso/sk_budi_santoso.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(4, 'Budi Santoso', 'Kontrak Kerja - Budi Santoso', 'Kontrak Kerja', 'hrd_files/budi_santoso/kontrak_budi_santoso.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(5, 'Budi Santoso', 'BPJS Kesehatan - Budi Santoso', 'BPJS Kesehatan', 'hrd_files/budi_santoso/bpjs_kes_budi_santoso.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(6, 'Budi Santoso', 'BPJS TK - Budi Santoso', 'BPJS TK', 'hrd_files/budi_santoso/bpjs_tk_budi_santoso.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(7, 'Siti Rahayu', 'KTP - Siti Rahayu', 'KTP', 'hrd_files/siti_rahayu/ktp_siti_rahayu.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(8, 'Siti Rahayu', 'Ijazah - Siti Rahayu', 'Ijazah', 'hrd_files/siti_rahayu/ijazah_siti_rahayu.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(9, 'Siti Rahayu', 'SK Pengangkatan - Siti Rahayu', 'SK Pengangkatan', 'hrd_files/siti_rahayu/sk_siti_rahayu.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(10, 'Siti Rahayu', 'Kontrak Kerja - Siti Rahayu', 'Kontrak Kerja', 'hrd_files/siti_rahayu/kontrak_siti_rahayu.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(11, 'Agus Wibowo', 'NPWP - Agus Wibowo', 'NPWP', 'hrd_files/agus_wibowo/npwp_agus_wibowo.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(12, 'Agus Wibowo', 'SK Pengangkatan - Agus Wibowo', 'SK Pengangkatan', 'hrd_files/agus_wibowo/sk_agus_wibowo.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(13, 'Agus Wibowo', 'Kontrak Kerja - Agus Wibowo', 'Kontrak Kerja', 'hrd_files/agus_wibowo/kontrak_agus_wibowo.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(14, 'Agus Wibowo', 'BPJS Kesehatan - Agus Wibowo', 'BPJS Kesehatan', 'hrd_files/agus_wibowo/bpjs_kes_agus_wibowo.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(15, 'Agus Wibowo', 'BPJS TK - Agus Wibowo', 'BPJS TK', 'hrd_files/agus_wibowo/bpjs_tk_agus_wibowo.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(16, 'Dewi Kusuma', 'KTP - Dewi Kusuma', 'KTP', 'hrd_files/dewi_kusuma/ktp_dewi_kusuma.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(17, 'Dewi Kusuma', 'NPWP - Dewi Kusuma', 'NPWP', 'hrd_files/dewi_kusuma/npwp_dewi_kusuma.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(18, 'Dewi Kusuma', 'Ijazah - Dewi Kusuma', 'Ijazah', 'hrd_files/dewi_kusuma/ijazah_dewi_kusuma.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(19, 'Dewi Kusuma', 'SK Pengangkatan - Dewi Kusuma', 'SK Pengangkatan', 'hrd_files/dewi_kusuma/sk_dewi_kusuma.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(20, 'Rini Apriani', 'KTP - Rini Apriani', 'KTP', 'hrd_files/rini_apriani/ktp_rini_apriani.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(21, 'Rini Apriani', 'Ijazah - Rini Apriani', 'Ijazah', 'hrd_files/rini_apriani/ijazah_rini_apriani.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(22, 'Rini Apriani', 'SK Pengangkatan - Rini Apriani', 'SK Pengangkatan', 'hrd_files/rini_apriani/sk_rini_apriani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(23, 'Rini Apriani', 'Kontrak Kerja - Rini Apriani', 'Kontrak Kerja', 'hrd_files/rini_apriani/kontrak_rini_apriani.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(24, 'Rini Apriani', 'BPJS Kesehatan - Rini Apriani', 'BPJS Kesehatan', 'hrd_files/rini_apriani/bpjs_kes_rini_apriani.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(25, 'Eko Prasetyo', 'NPWP - Eko Prasetyo', 'NPWP', 'hrd_files/eko_prasetyo/npwp_eko_prasetyo.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(26, 'Eko Prasetyo', 'Ijazah - Eko Prasetyo', 'Ijazah', 'hrd_files/eko_prasetyo/ijazah_eko_prasetyo.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(27, 'Eko Prasetyo', 'SK Pengangkatan - Eko Prasetyo', 'SK Pengangkatan', 'hrd_files/eko_prasetyo/sk_eko_prasetyo.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(28, 'Eko Prasetyo', 'Kontrak Kerja - Eko Prasetyo', 'Kontrak Kerja', 'hrd_files/eko_prasetyo/kontrak_eko_prasetyo.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(29, 'Eko Prasetyo', 'BPJS Kesehatan - Eko Prasetyo', 'BPJS Kesehatan', 'hrd_files/eko_prasetyo/bpjs_kes_eko_prasetyo.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(30, 'Eko Prasetyo', 'BPJS TK - Eko Prasetyo', 'BPJS TK', 'hrd_files/eko_prasetyo/bpjs_tk_eko_prasetyo.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(31, 'Hendra Gunawan', 'Ijazah - Hendra Gunawan', 'Ijazah', 'hrd_files/hendra_gunawan/ijazah_hendra_gunawan.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(32, 'Hendra Gunawan', 'Kontrak Kerja - Hendra Gunawan', 'Kontrak Kerja', 'hrd_files/hendra_gunawan/kontrak_hendra_gunawan.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(33, 'Hendra Gunawan', 'BPJS Kesehatan - Hendra Gunawan', 'BPJS Kesehatan', 'hrd_files/hendra_gunawan/bpjs_kes_hendra_gunawan.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(34, 'Hendra Gunawan', 'BPJS TK - Hendra Gunawan', 'BPJS TK', 'hrd_files/hendra_gunawan/bpjs_tk_hendra_gunawan.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(35, 'Rizky Fadillah', 'Ijazah - Rizky Fadillah', 'Ijazah', 'hrd_files/rizky_fadillah/ijazah_rizky_fadillah.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(36, 'Rizky Fadillah', 'SK Pengangkatan - Rizky Fadillah', 'SK Pengangkatan', 'hrd_files/rizky_fadillah/sk_rizky_fadillah.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(37, 'Rizky Fadillah', 'BPJS Kesehatan - Rizky Fadillah', 'BPJS Kesehatan', 'hrd_files/rizky_fadillah/bpjs_kes_rizky_fadillah.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(38, 'Rizky Fadillah', 'BPJS TK - Rizky Fadillah', 'BPJS TK', 'hrd_files/rizky_fadillah/bpjs_tk_rizky_fadillah.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(39, 'Yusuf Hidayat', 'KTP - Yusuf Hidayat', 'KTP', 'hrd_files/yusuf_hidayat/ktp_yusuf_hidayat.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(40, 'Yusuf Hidayat', 'NPWP - Yusuf Hidayat', 'NPWP', 'hrd_files/yusuf_hidayat/npwp_yusuf_hidayat.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(41, 'Yusuf Hidayat', 'Ijazah - Yusuf Hidayat', 'Ijazah', 'hrd_files/yusuf_hidayat/ijazah_yusuf_hidayat.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(42, 'Yusuf Hidayat', 'Kontrak Kerja - Yusuf Hidayat', 'Kontrak Kerja', 'hrd_files/yusuf_hidayat/kontrak_yusuf_hidayat.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(43, 'Yusuf Hidayat', 'BPJS Kesehatan - Yusuf Hidayat', 'BPJS Kesehatan', 'hrd_files/yusuf_hidayat/bpjs_kes_yusuf_hidayat.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(44, 'Yusuf Hidayat', 'BPJS TK - Yusuf Hidayat', 'BPJS TK', 'hrd_files/yusuf_hidayat/bpjs_tk_yusuf_hidayat.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(45, 'Linda Permata', 'KTP - Linda Permata', 'KTP', 'hrd_files/linda_permata/ktp_linda_permata.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(46, 'Linda Permata', 'NPWP - Linda Permata', 'NPWP', 'hrd_files/linda_permata/npwp_linda_permata.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(47, 'Linda Permata', 'Kontrak Kerja - Linda Permata', 'Kontrak Kerja', 'hrd_files/linda_permata/kontrak_linda_permata.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(48, 'Linda Permata', 'BPJS Kesehatan - Linda Permata', 'BPJS Kesehatan', 'hrd_files/linda_permata/bpjs_kes_linda_permata.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(49, 'Wahyu Nugroho', 'SK Pengangkatan - Wahyu Nugroho', 'SK Pengangkatan', 'hrd_files/wahyu_nugroho/sk_wahyu_nugroho.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(50, 'Wahyu Nugroho', 'BPJS Kesehatan - Wahyu Nugroho', 'BPJS Kesehatan', 'hrd_files/wahyu_nugroho/bpjs_kes_wahyu_nugroho.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(51, 'Wahyu Nugroho', 'BPJS TK - Wahyu Nugroho', 'BPJS TK', 'hrd_files/wahyu_nugroho/bpjs_tk_wahyu_nugroho.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(52, 'Fitri Handayani', 'NPWP - Fitri Handayani', 'NPWP', 'hrd_files/fitri_handayani/npwp_fitri_handayani.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(53, 'Fitri Handayani', 'SK Pengangkatan - Fitri Handayani', 'SK Pengangkatan', 'hrd_files/fitri_handayani/sk_fitri_handayani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(54, 'Fitri Handayani', 'Kontrak Kerja - Fitri Handayani', 'Kontrak Kerja', 'hrd_files/fitri_handayani/kontrak_fitri_handayani.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(55, 'Fitri Handayani', 'BPJS Kesehatan - Fitri Handayani', 'BPJS Kesehatan', 'hrd_files/fitri_handayani/bpjs_kes_fitri_handayani.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(56, 'Fitri Handayani', 'BPJS TK - Fitri Handayani', 'BPJS TK', 'hrd_files/fitri_handayani/bpjs_tk_fitri_handayani.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(57, 'Dody Kurniawan', 'KTP - Dody Kurniawan', 'KTP', 'hrd_files/dody_kurniawan/ktp_dody_kurniawan.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(58, 'Dody Kurniawan', 'Ijazah - Dody Kurniawan', 'Ijazah', 'hrd_files/dody_kurniawan/ijazah_dody_kurniawan.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(59, 'Dody Kurniawan', 'SK Pengangkatan - Dody Kurniawan', 'SK Pengangkatan', 'hrd_files/dody_kurniawan/sk_dody_kurniawan.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(60, 'Dody Kurniawan', 'BPJS Kesehatan - Dody Kurniawan', 'BPJS Kesehatan', 'hrd_files/dody_kurniawan/bpjs_kes_dody_kurniawan.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(61, 'Teguh Santosa', 'KTP - Teguh Santosa', 'KTP', 'hrd_files/teguh_santosa/ktp_teguh_santosa.pdf', 'Kartu Tanda Penduduk', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(62, 'Teguh Santosa', 'NPWP - Teguh Santosa', 'NPWP', 'hrd_files/teguh_santosa/npwp_teguh_santosa.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(63, 'Teguh Santosa', 'Ijazah - Teguh Santosa', 'Ijazah', 'hrd_files/teguh_santosa/ijazah_teguh_santosa.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(64, 'Teguh Santosa', 'SK Pengangkatan - Teguh Santosa', 'SK Pengangkatan', 'hrd_files/teguh_santosa/sk_teguh_santosa.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(65, 'Teguh Santosa', 'Kontrak Kerja - Teguh Santosa', 'Kontrak Kerja', 'hrd_files/teguh_santosa/kontrak_teguh_santosa.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(66, 'Teguh Santosa', 'BPJS Kesehatan - Teguh Santosa', 'BPJS Kesehatan', 'hrd_files/teguh_santosa/bpjs_kes_teguh_santosa.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(67, 'Teguh Santosa', 'BPJS TK - Teguh Santosa', 'BPJS TK', 'hrd_files/teguh_santosa/bpjs_tk_teguh_santosa.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(68, 'Arif Budiman', 'NPWP - Arif Budiman', 'NPWP', 'hrd_files/arif_budiman/npwp_arif_budiman.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(69, 'Arif Budiman', 'Ijazah - Arif Budiman', 'Ijazah', 'hrd_files/arif_budiman/ijazah_arif_budiman.pdf', 'Ijazah pendidikan terakhir', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(70, 'Arif Budiman', 'SK Pengangkatan - Arif Budiman', 'SK Pengangkatan', 'hrd_files/arif_budiman/sk_arif_budiman.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(71, 'Arif Budiman', 'Kontrak Kerja - Arif Budiman', 'Kontrak Kerja', 'hrd_files/arif_budiman/kontrak_arif_budiman.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(72, 'Arif Budiman', 'BPJS Kesehatan - Arif Budiman', 'BPJS Kesehatan', 'hrd_files/arif_budiman/bpjs_kes_arif_budiman.pdf', 'Kartu BPJS Kesehatan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(73, 'Arif Budiman', 'BPJS TK - Arif Budiman', 'BPJS TK', 'hrd_files/arif_budiman/bpjs_tk_arif_budiman.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-09 03:36:26', '2026-07-09 03:36:26');

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
(1, 'PT Sinar Abadi', 'Sparepart', 5000000.00, 2000000.00, 3000000.00, '2026-07-19', 'belum_lunas', 'Pembelian sparepart mesin', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, 'CV Mitra Jaya', 'Service', 2500000.00, 2500000.00, 0.00, '2026-07-04', 'lunas', 'Service kendaraan fleet', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(3, 'PT Otomotif Nusantara', 'Aksesoris', 1200000.00, 500000.00, 700000.00, '2026-07-12', 'belum_lunas', 'Pembelian aksesoris mobil', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(4, 'UD Jaya Mandiri', 'Ban', 3000000.00, 1000000.00, 2000000.00, '2026-07-24', 'belum_lunas', 'Pembelian ban kendaraan', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(5, 'PT Diesel Prima', 'Mesin', 8000000.00, 8000000.00, 0.00, '2026-07-07', 'lunas', 'Perbaikan mesin besar', '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
(1, 3, 1, NULL, 'perorangan', 'INV-2026-0001', 'ORD-0001', 'PT Teknologi Nusantara', 'Jl. Contoh No.1, Jakarta', 'Hendra Gunawan', '081269922257', 'pt.teknologi.nusantara@email.com', 'Bulan', '2026-02-06', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 71500.00, 13000.00, 708500.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(2, 4, 2, NULL, 'perusahaan', 'INV-2026-0002', 'ORD-0002', 'UD Sumber Rejeki', 'Jl. Contoh No.2, Jakarta', 'Dewi Lestari', '081245879677', 'ud.sumber.rejeki@email.com', 'Hari', '2025-12-17', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 264000.00, 48000.00, 2616000.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(3, 5, 3, NULL, 'perusahaan', 'INV-2026-0003', 'ORD-0003', 'PT Logistik Andalan', 'Jl. Contoh No.3, Jakarta', 'Rizal Fahmi', '081273875592', 'pt.logistik.andalan@email.com', 'Tahun', '2026-07-07', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 242000.00, 44000.00, 2398000.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(4, 9, 4, NULL, 'perorangan', 'INV-2026-0004', 'ORD-0004', 'CV Perdana Sejahtera', 'Jl. Contoh No.4, Jakarta', 'Wahyu Nugroho', '081288970465', 'cv.perdana.sejahtera@email.com', 'Bulan', '2025-12-16', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 187000.00, 34000.00, 1853000.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(5, 10, 5, NULL, 'perusahaan', 'INV-2026-0005', 'ORD-0005', 'PT Aneka Niaga Indonesia', 'Jl. Contoh No.5, Jakarta', 'Fitri Handayani', '081210260808', 'pt.aneka.niaga.indonesia@email.com', 'Hari', '2026-03-31', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 418000.00, 76000.00, 4142000.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(6, 11, 6, NULL, 'perusahaan', 'INV-2026-0006', 'ORD-0006', 'PT Maju Jaya Abadi', 'Jl. Contoh No.6, Jakarta', 'Budi Hartono', '081271509062', 'pt.maju.jaya.abadi@email.com', 'Tahun', '2026-05-27', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 71500.00, 13000.00, 708500.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(7, 15, 7, NULL, 'perorangan', 'INV-2026-0007', 'ORD-0007', 'PT Logistik Andalan', 'Jl. Contoh No.7, Jakarta', 'Rizal Fahmi', '081212340392', 'pt.logistik.andalan@email.com', 'Bulan', '2026-07-06', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 247500.00, 45000.00, 2452500.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(8, 16, 8, NULL, 'perusahaan', 'INV-2026-0008', 'ORD-0008', 'CV Karya Utama', 'Jl. Contoh No.8, Jakarta', 'Nur Hidayah', '081244543595', 'cv.karya.utama@email.com', 'Hari', '2026-07-06', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 220000.00, 40000.00, 2180000.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(9, 17, 9, NULL, 'perusahaan', 'INV-2026-0009', 'ORD-0009', 'PT Solusi Transportasi', 'Jl. Contoh No.9, Jakarta', 'Agus Setiawan', '081216796450', 'pt.solusi.transportasi@email.com', 'Tahun', '2026-05-18', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 280500.00, 51000.00, 2779500.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 2, 1308000.00, '2026-01-01', 'Tunai', 'TXN-E36314E624', NULL, 'verified', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(2, 2, 784800.00, '2026-01-31', 'Virtual Account', 'TXN-F8BC2FBE2C', NULL, 'pending', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(3, 4, 1853000.00, '2026-01-10', 'Cek/Giro', 'TXN-B9C133D14E', NULL, 'verified', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(4, 6, 354250.00, '2026-06-06', 'QRIS', 'TXN-710FB21AC4', NULL, 'verified', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(5, 6, 212550.00, '2026-06-16', 'Virtual Account', 'TXN-D105AE60B3', NULL, 'pending', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(6, 8, 2180000.00, '2026-07-24', 'Virtual Account', 'TXN-318A900A94', NULL, 'verified', '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 1, '2026-02-06', '2026-03-06', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(2, 2, '2025-12-17', '2026-06-17', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(3, 3, '2026-07-07', '2026-11-07', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(4, 4, '2025-12-16', '2026-05-16', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(5, 5, '2026-03-31', '2026-07-01', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(6, 6, '2026-05-27', '2026-10-27', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(7, 7, '2026-07-06', '2026-10-06', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(8, 8, '2026-07-06', '2026-09-06', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(9, 9, '2026-05-18', '2026-10-18', '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 1, 1, 'Sewa Kendaraan Operasional', 4, 4995826.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(2, 2, 2, 'Biaya Driver', 3, 4288443.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(3, 2, 2, 'Bahan Bakar', 1, 3254201.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(4, 3, 3, 'Bahan Bakar', 1, 1056357.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(5, 3, 3, 'Biaya Perawatan', 1, 1212099.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(6, 3, 3, 'Asuransi Kendaraan', 2, 3398502.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(7, 4, 4, 'Biaya Perawatan', 4, 568772.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(8, 4, 4, 'Asuransi Kendaraan', 3, 917565.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(9, 4, 4, 'Biaya Administrasi', 1, 680108.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(10, 5, 5, 'Asuransi Kendaraan', 3, 1252251.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(11, 6, 6, 'Biaya Administrasi', 2, 2175652.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(12, 7, 7, 'Sewa Kendaraan Operasional', 2, 4574410.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(13, 7, 7, 'Biaya Driver', 1, 965781.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(14, 8, 8, 'Biaya Driver', 4, 640032.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(15, 8, 8, 'Bahan Bakar', 4, 3370118.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(16, 8, 8, 'Biaya Perawatan', 4, 3838623.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(17, 9, 9, 'Bahan Bakar', 1, 2533360.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(18, 9, 9, 'Biaya Perawatan', 1, 1702815.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(19, 9, 9, 'Asuransi Kendaraan', 1, 879426.00, '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 3, 'KTR-0003', '2026-02-03', '2027-02-03', 'PT Apyrent Indonesia', '021-12345678', 'PT Teknologi Nusantara', 'Hendra Gunawan', NULL, NULL, 'pending', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(2, 4, 'KTR-0004', '2025-12-14', '2026-05-14', 'PT Apyrent Indonesia', '021-12345678', 'UD Sumber Rejeki', 'Dewi Lestari', NULL, NULL, 'approved', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(3, 5, 'KTR-0005', '2026-07-04', '2027-06-04', 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, 'active', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(4, 9, 'KTR-0009', '2025-12-14', '2026-12-14', 'PT Apyrent Indonesia', '021-12345678', 'CV Perdana Sejahtera', 'Wahyu Nugroho', NULL, NULL, 'completed', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(5, 10, 'KTR-0010', '2026-03-26', '2026-10-26', 'PT Apyrent Indonesia', '021-12345678', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', NULL, NULL, 'terminated', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(6, 11, 'KTR-0011', '2026-05-23', '2027-05-23', 'PT Apyrent Indonesia', '021-12345678', 'PT Maju Jaya Abadi', 'Budi Hartono', NULL, NULL, 'pending', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(7, 15, 'KTR-0015', '2026-07-04', '2027-07-04', 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, 'approved', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(8, 16, 'KTR-0016', '2026-06-30', '2027-06-30', 'PT Apyrent Indonesia', '021-12345678', 'CV Karya Utama', 'Nur Hidayah', NULL, NULL, 'active', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(9, 17, 'KTR-0017', '2026-05-14', '2027-02-14', 'PT Apyrent Indonesia', '021-12345678', 'PT Solusi Transportasi', 'Agus Setiawan', NULL, NULL, 'completed', '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 'PNW-0001', '2025-11-24', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '2550000', NULL, NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'PNW-0002', '2025-12-18', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '950000', NULL, NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'PNW-0003', '2026-01-23', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '650000', NULL, NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'PNW-0004', '2025-12-05', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '2400000', NULL, NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'PNW-0005', '2026-06-27', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'completed', '2200000', NULL, NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(6, 'PNW-0006', '2026-01-17', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '3300000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(7, 'PNW-0007', '2026-02-01', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '750000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(8, 'PNW-0008', '2025-12-06', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '2000000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(9, 'PNW-0009', '2025-12-02', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '1700000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(10, 'PNW-0010', '2026-03-13', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '3800000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(11, 'PNW-0011', '2026-05-11', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'completed', '650000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(12, 'PNW-0012', '2025-12-31', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '4800000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(13, 'PNW-0013', '2026-03-31', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '1100000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(14, 'PNW-0014', '2026-05-11', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '3300000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(15, 'PNW-0015', '2026-06-27', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '2250000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(16, 'PNW-0016', '2026-06-24', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '2000000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(17, 'PNW-0017', '2026-05-09', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'completed', '2550000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(18, 'PNW-0018', '2025-09-23', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '3800000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(19, 'PNW-0019', '2025-12-29', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'dibuat', '1950000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(20, 'PNW-0020', '2026-01-12', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '1200000', NULL, NULL, '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 1, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 1, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 2, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 3, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 4, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(6, 4, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(7, 5, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(8, 5, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(9, 6, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(10, 6, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(11, 7, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(12, 8, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(13, 8, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(14, 9, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(15, 9, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(16, 10, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(17, 10, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(18, 11, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(19, 12, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(20, 12, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(21, 13, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(22, 13, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(23, 14, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(24, 14, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(25, 15, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(26, 15, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(27, 16, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(28, 16, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(29, 17, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(30, 17, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(31, 18, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(32, 18, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(33, 19, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(34, 19, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(35, 20, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 3, 1, 1, 'perorangan', 708500.00, 0.00, 708500.00, 'unpaid', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(2, 4, 2, 2, 'perusahaan', 2616000.00, 1308000.00, 1308000.00, 'partial', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(3, 5, 3, 3, 'perusahaan', 2398000.00, 0.00, 2398000.00, 'unpaid', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(4, 9, 4, 4, 'perorangan', 1853000.00, 1853000.00, 0.00, 'lunas', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(5, 10, 5, 5, 'perusahaan', 4142000.00, 0.00, 4142000.00, 'unpaid', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(6, 11, 6, 6, 'perusahaan', 708500.00, 354250.00, 354250.00, 'partial', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(7, 15, 7, 7, 'perorangan', 2452500.00, 0.00, 2452500.00, 'unpaid', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(8, 16, 8, 8, 'perusahaan', 2180000.00, 2180000.00, 0.00, 'lunas', '2026-07-09 03:36:23', '2026-07-09 03:36:23'),
(9, 17, 9, 9, 'perusahaan', 2779500.00, 0.00, 2779500.00, 'unpaid', '2026-07-09 03:36:23', '2026-07-09 03:36:23');

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
(1, 'AST-001', 'Laptop Dell XPS 15', 'Laptop', 'Ruang IT Lt.2', 'Budi Santoso', 'Dell', '2022', 'Aktif', 'Unit utama developer', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'AST-002', 'HP LaserJet Pro M404', 'Printer', 'Ruang Admin', 'Sari Dewi', 'HP', '2021', 'Aktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'AST-003', 'MacBook Pro M2', 'Laptop', 'Ruang Desain', 'Andi Wijaya', 'Apple', '2023', 'Aktif', 'Untuk tim desain grafis', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'AST-004', 'Monitor LG 27 Inch 4K', 'Monitor', 'Ruang IT Lt.2', 'Rudi Hermawan', 'LG', '2022', 'Aktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'AST-005', 'Cisco Switch 48 Port', 'Network', 'Ruang Server', 'Tim IT', 'Cisco', '2020', 'Rusak', 'Port 12-15 tidak berfungsi', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 1, 'Mobil SUV', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(2, 1, 'Mobil MPV', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(3, 1, 'Mobil Sedan', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(4, 1, 'Pickup', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(5, 1, 'Truck', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(6, 1, 'Bus Pariwisata', '2026-07-09 03:36:16', '2026-07-09 03:36:16');

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
(1, 'All Risk', 'Menanggung kerusakan ringan dan berat', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(2, 'TLO', 'Total Loss Only', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(3, 'Comprehensive', 'Perlindungan menyeluruh kendaraan', '2026-07-09 03:36:17', '2026-07-09 03:36:17');

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
(1, 'MKT001', 'Promo Rental Akhir Tahun', 'Promosi', 'Email', 'Pelanggan Aktif', '2026-12-25', '2026-12-31', 'Diskon Spesial Akhir Tahun!', 'Dapatkan diskon 20% untuk rental mobil', 'Dijadwalkan', 'Rina Marketing', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'MKT002', 'Re-engagement Campaign', 'Retensi', 'WhatsApp', 'Inaktif 6 Bulan', '2026-08-01', '2026-08-15', 'Kami Merindukan Anda', 'Rental lagi dan dapat voucher', 'Aktif', 'Ahmad Marketing', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 1, 1, 'AA 1011 BE', NULL, 'Pemilik Kendaraan 1', 'Wonosobo', 'Toyota Avanza', '2023', '2023', '1500 CC', 'Hitam', 'NRFC5E3E437C60E', 'NMEAA566D8', 'BPKB000001', 'Hitam', 'Pertalite', 'AA', '001234', 455000, 59000, 1183000, NULL, '2027-12-09', 66032, 5000, 6, 63152, '2026-05-09', 'aman', 'tersedia', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(2, 1, 2, 'AB 1022 CF', NULL, 'Pemilik Kendaraan 2', 'Magelang', 'Toyota Innova', '2023', '2023', '1500 CC', 'Putih', 'NR347D8BAA75F0E', 'NMA267766B', 'BPKB000002', 'Hitam', 'Pertamax', 'AB', '002468', 369000, 70000, 1101000, NULL, '2026-05-09', 12786, 5000, 6, 8289, '2025-12-09', 'service', 'disewa', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(3, 1, 3, 'AD 1033 DG', NULL, 'Pemilik Kendaraan 3', 'Purworejo', 'Toyota Rush', '2020', '2020', '1000 CC', 'Silver', 'NR05BCAB37A5B5E', 'NM7DA12124', 'BPKB000003', 'Hitam', 'Solar', 'AD', '003702', 464000, 64000, 1898000, NULL, '2026-04-09', 58205, 5000, 6, 54340, '2025-09-09', 'aman', 'service', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(4, 1, 1, 'AE 1044 EH', NULL, 'Pemilik Kendaraan 4', 'Kebumen', 'Toyota Fortuner', '2015', '2015', '1000 CC', 'Merah', 'NRD9F1155CE77F7', 'NM96142E2A', 'BPKB000004', 'Hitam', 'Pertamax Turbo', 'AE', '004936', 524000, 77000, 999000, NULL, '2027-04-09', 115054, 5000, 6, 113590, '2025-11-09', 'service', 'bermasalah', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(5, 1, 2, 'AG 1055 FI', NULL, 'Pemilik Kendaraan 5', 'Purwokerto', 'Toyota Calya', '2023', '2023', '500 CC', 'Biru', 'NRC2DBC1270E648', 'NMCABF6A7D', 'BPKB000005', 'Hitam', 'Pertalite', 'AG', '006170', 203000, 78000, 691000, NULL, '2027-01-09', 12489, 5000, 6, 8324, '2026-06-09', 'aman', 'tersedia', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(6, 1, 3, 'AA 1066 GJ', NULL, 'Pemilik Kendaraan 6', 'Temanggung', 'Honda Brio', '2017', '2017', '1000 CC', 'Abu-abu', 'NRD5527EB4A669D', 'NM787F80E0', 'BPKB000006', 'Hitam', 'Pertamax', 'AA', '007404', 431000, 76000, 635000, NULL, '2028-04-09', 47433, 5000, 6, 40856, '2026-04-09', 'service', 'disewa', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(7, 1, 1, 'AB 1077 HK', NULL, 'Pemilik Kendaraan 7', 'Kendal', 'Honda Mobilio', '2019', '2019', '500 CC', 'Coklat', 'NRF6B081BFC804D', 'NM238F09D0', 'BPKB000007', 'Hitam', 'Solar', 'AB', '008638', 573000, 74000, 1445000, NULL, '2027-01-09', 81918, 5000, 6, 80170, '2025-10-09', 'aman', 'service', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(8, 1, 2, 'AD 1088 IL', NULL, 'Pemilik Kendaraan 8', 'Batang', 'Honda HR-V', '2023', '2023', '1000 CC', 'Kuning', 'NRC71D2E264F12E', 'NMB3E50BB9', 'BPKB000008', 'Hitam', 'Pertamax Turbo', 'AD', '009872', 222000, 62000, 548000, NULL, '2026-08-09', 38683, 5000, 6, 35704, '2026-02-09', 'service', 'bermasalah', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(9, 1, 3, 'AE 1099 JM', NULL, 'Pemilik Kendaraan 9', 'Wonosobo', 'Honda CR-V', '2021', '2021', '500 CC', 'Hitam', 'NR28CB98ADDC49A', 'NM95D7A4AD', 'BPKB000009', 'Hitam', 'Pertalite', 'AE', '011106', 370000, 68000, 1210000, NULL, '2026-07-09', 37120, 5000, 6, 34283, '2026-01-09', 'aman', 'tersedia', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(10, 1, 1, 'AG 1110 KN', NULL, 'Pemilik Kendaraan 10', 'Magelang', 'Honda Jazz', '2020', '2020', '500 CC', 'Putih', 'NRC7018438D4E08', 'NMA21DC861', 'BPKB000010', 'Hitam', 'Pertamax', 'AG', '012340', 490000, 64000, 614000, NULL, '2026-09-09', 49491, 5000, 6, 47516, '2026-02-09', 'service', 'disewa', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(11, 1, 2, 'AA 1121 LO', NULL, 'Pemilik Kendaraan 11', 'Purworejo', 'Mitsubishi Xpander', '2021', '2021', '1500 CC', 'Silver', 'NR101ED65635098', 'NMB836A727', 'BPKB000011', 'Hitam', 'Solar', 'AA', '013574', 344000, 63000, 1131000, NULL, '2027-11-09', 65353, 5000, 6, 60714, '2026-04-09', 'aman', 'service', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(12, 1, 3, 'AB 1132 MP', NULL, 'Pemilik Kendaraan 12', 'Kebumen', 'Mitsubishi Pajero', '2023', '2023', '500 CC', 'Merah', 'NRBA899300E453F', 'NMFCA534D3', 'BPKB000012', 'Hitam', 'Pertamax Turbo', 'AB', '014808', 301000, 41000, 1361000, NULL, '2028-04-09', 28241, 5000, 6, 23559, '2026-02-09', 'service', 'bermasalah', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(13, 1, 1, 'AD 1143 NQ', NULL, 'Pemilik Kendaraan 13', 'Purwokerto', 'Mitsubishi L300', '2022', '2022', '1000 CC', 'Biru', 'NR28C8D02298AFE', 'NM18E9181C', 'BPKB000013', 'Hitam', 'Pertalite', 'AD', '016042', 218000, 36000, 1387000, NULL, '2028-01-09', 34923, 5000, 6, 31097, '2025-10-09', 'aman', 'tersedia', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(14, 1, 2, 'AE 1154 OR', NULL, 'Pemilik Kendaraan 14', 'Temanggung', 'Mitsubishi Outlander', '2023', '2023', '500 CC', 'Abu-abu', 'NR71E1B33FE3385', 'NM65CE77D8', 'BPKB000014', 'Hitam', 'Pertamax', 'AE', '017276', 436000, 52000, 859000, NULL, '2026-05-09', 10836, 5000, 6, 4714, '2026-02-09', 'service', 'disewa', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(15, 1, 3, 'AG 1165 PS', NULL, 'Pemilik Kendaraan 15', 'Kendal', 'Daihatsu Xenia', '2018', '2018', '1500 CC', 'Coklat', 'NR0188959EBD4CF', 'NMD99046EE', 'BPKB000015', 'Hitam', 'Solar', 'AG', '018510', 310000, 30000, 1638000, NULL, '2027-02-09', 75347, 5000, 6, 72674, '2026-04-09', 'aman', 'service', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(16, 1, 1, 'AA 1176 QT', NULL, 'Pemilik Kendaraan 16', 'Batang', 'Daihatsu Terios', '2023', '2023', '1000 CC', 'Kuning', 'NR4CFC5AB2A1B24', 'NM66FA2F56', 'BPKB000016', 'Hitam', 'Pertamax Turbo', 'AA', '019744', 321000, 46000, 913000, NULL, '2027-06-09', 98544, 5000, 6, 93914, '2026-05-09', 'service', 'bermasalah', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(17, 1, 2, 'AB 1187 RU', NULL, 'Pemilik Kendaraan 17', 'Wonosobo', 'Daihatsu Sigra', '2018', '2018', '500 CC', 'Hitam', 'NRB690B147D29AD', 'NMED999685', 'BPKB000017', 'Hitam', 'Pertalite', 'AB', '020978', 365000, 67000, 1510000, NULL, '2028-06-09', 57798, 5000, 6, 53839, '2026-02-09', 'aman', 'tersedia', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(18, 1, 3, 'AD 1198 SV', NULL, 'Pemilik Kendaraan 18', 'Magelang', 'Daihatsu Gran Max', '2019', '2019', '1500 CC', 'Putih', 'NR89F8444CB9A7D', 'NM8CD1481B', 'BPKB000018', 'Hitam', 'Pertamax', 'AD', '022212', 495000, 45000, 983000, NULL, '2026-05-09', 22163, 5000, 6, 20274, '2025-09-09', 'service', 'disewa', '2026-07-09 03:36:16', '2026-07-09 03:36:16'),
(19, 1, 1, 'AE 1209 TW', NULL, 'Pemilik Kendaraan 19', 'Purworejo', 'Suzuki Ertiga', '2021', '2021', '500 CC', 'Silver', 'NR67D11ED0217CF', 'NM0AAE1062', 'BPKB000019', 'Hitam', 'Solar', 'AE', '023446', 283000, 71000, 1162000, NULL, '2026-08-09', 36607, 5000, 6, 31583, '2025-07-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(20, 1, 2, 'AG 1220 UX', NULL, 'Pemilik Kendaraan 20', 'Kebumen', 'Suzuki APV', '2018', '2018', '500 CC', 'Merah', 'NR4736A85D494F9', 'NM8C8EA669', 'BPKB000020', 'Hitam', 'Pertamax Turbo', 'AG', '024680', 416000, 53000, 781000, NULL, '2027-04-09', 74063, 5000, 6, 66073, '2026-05-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(21, 1, 3, 'AA 1231 VY', NULL, 'Pemilik Kendaraan 21', 'Purwokerto', 'Suzuki Jimny', '2020', '2020', '1500 CC', 'Biru', 'NR6EBC3372CE7C9', 'NMB0B07747', 'BPKB000021', 'Hitam', 'Pertalite', 'AA', '025914', 380000, 36000, 1587000, NULL, '2027-04-09', 84995, 5000, 6, 82575, '2026-05-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(22, 1, 1, 'AB 1242 WZ', NULL, 'Pemilik Kendaraan 22', 'Temanggung', 'Suzuki Carry', '2022', '2022', '1500 CC', 'Abu-abu', 'NR62F120141685C', 'NM6AA28811', 'BPKB000022', 'Hitam', 'Pertamax', 'AB', '027148', 410000, 44000, 1817000, NULL, '2026-11-09', 65360, 5000, 6, 59969, '2025-12-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(23, 1, 2, 'AD 1253 XA', NULL, 'Pemilik Kendaraan 23', 'Kendal', 'Nissan X-Trail', '2021', '2021', '500 CC', 'Coklat', 'NRC5153735B748E', 'NMAB3A9544', 'BPKB000023', 'Hitam', 'Solar', 'AD', '028382', 395000, 71000, 1668000, NULL, '2027-11-09', 73461, 5000, 6, 67808, '2025-10-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(24, 1, 3, 'AE 1264 YB', NULL, 'Pemilik Kendaraan 24', 'Batang', 'Nissan Livina', '2022', '2022', '1000 CC', 'Kuning', 'NR249973A0D8662', 'NMADFDF0BD', 'BPKB000024', 'Hitam', 'Pertamax Turbo', 'AE', '029616', 293000, 50000, 599000, NULL, '2026-10-09', 58900, 5000, 6, 52229, '2025-09-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(25, 1, 1, 'AG 1275 ZC', NULL, 'Pemilik Kendaraan 25', 'Wonosobo', 'Nissan Terra', '2022', '2022', '500 CC', 'Hitam', 'NR1233A73D59FC1', 'NM3B083C41', 'BPKB000025', 'Hitam', 'Pertalite', 'AG', '030850', 475000, 64000, 1673000, NULL, '2027-12-09', 92833, 5000, 6, 91472, '2026-01-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(26, 1, 2, 'AA 1286 AD', NULL, 'Pemilik Kendaraan 26', 'Magelang', 'Isuzu Panther', '2017', '2017', '1000 CC', 'Putih', 'NR8B7FE74809E8F', 'NM11A757F7', 'BPKB000026', 'Hitam', 'Pertamax', 'AA', '032084', 587000, 33000, 587000, NULL, '2026-04-09', 97313, 5000, 6, 90278, '2026-06-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(27, 1, 3, 'AB 1297 BE', NULL, 'Pemilik Kendaraan 27', 'Purworejo', 'Isuzu D-Max', '2019', '2019', '1500 CC', 'Silver', 'NRB559A1B082827', 'NM2BA7C96A', 'BPKB000027', 'Hitam', 'Solar', 'AB', '033318', 573000, 45000, 714000, NULL, '2026-09-09', 25915, 5000, 6, 23610, '2026-05-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(28, 1, 1, 'AD 1308 CF', NULL, 'Pemilik Kendaraan 28', 'Kebumen', 'Isuzu Elf', '2022', '2022', '1000 CC', 'Merah', 'NR8868E52D15081', 'NMF004F89A', 'BPKB000028', 'Hitam', 'Pertamax Turbo', 'AD', '034552', 221000, 71000, 1700000, NULL, '2027-07-09', 59341, 5000, 6, 57231, '2025-11-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(29, 1, 2, 'AE 1319 DG', NULL, 'Pemilik Kendaraan 29', 'Purwokerto', 'Wuling Almaz', '2018', '2018', '1000 CC', 'Biru', 'NR9B965F12D9041', 'NM09AD4CEA', 'BPKB000029', 'Hitam', 'Pertalite', 'AE', '035786', 210000, 78000, 582000, NULL, '2027-03-09', 42822, 5000, 6, 34933, '2026-03-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(30, 1, 3, 'AG 1330 EH', NULL, 'Pemilik Kendaraan 30', 'Temanggung', 'Wuling Air ev', '2024', '2024', '1500 CC', 'Abu-abu', 'NR5238145D8FA5E', 'NMB7E2C1AB', 'BPKB000030', 'Hitam', 'Pertamax', 'AG', '037020', 403000, 56000, 1985000, NULL, '2027-08-09', 55336, 5000, 6, 51561, '2025-08-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(31, 1, 1, 'AA 1341 FI', NULL, 'Pemilik Kendaraan 31', 'Kendal', 'Toyota Avanza', '2023', '2023', '500 CC', 'Coklat', 'NR8551649018B27', 'NMC0EF36C6', 'BPKB000031', 'Hitam', 'Solar', 'AA', '038254', 292000, 71000, 1711000, NULL, '2027-01-09', 119212, 5000, 6, 113877, '2026-04-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(32, 1, 2, 'AB 1352 GJ', NULL, 'Pemilik Kendaraan 32', 'Batang', 'Toyota Innova', '2016', '2016', '1000 CC', 'Kuning', 'NR83DC5F76D3D1F', 'NM1A73548C', 'BPKB000032', 'Hitam', 'Pertamax Turbo', 'AB', '039488', 236000, 70000, 1305000, NULL, '2028-04-09', 69457, 5000, 6, 66896, '2026-04-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(33, 1, 3, 'AD 1363 HK', NULL, 'Pemilik Kendaraan 33', 'Wonosobo', 'Toyota Rush', '2017', '2017', '1000 CC', 'Hitam', 'NR7D8A942BB2B06', 'NMD7DE1C30', 'BPKB000033', 'Hitam', 'Pertalite', 'AD', '040722', 219000, 66000, 788000, NULL, '2027-11-09', 85887, 5000, 6, 78211, '2026-02-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(34, 1, 1, 'AE 1374 IL', NULL, 'Pemilik Kendaraan 34', 'Magelang', 'Toyota Fortuner', '2021', '2021', '500 CC', 'Putih', 'NR6BB7CA4622D0E', 'NMBCAF1E4D', 'BPKB000034', 'Hitam', 'Pertamax', 'AE', '041956', 280000, 72000, 1829000, NULL, '2028-05-09', 76862, 5000, 6, 70067, '2026-05-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(35, 1, 2, 'AG 1385 JM', NULL, 'Pemilik Kendaraan 35', 'Purworejo', 'Toyota Calya', '2017', '2017', '1000 CC', 'Silver', 'NR2B41A619C9BF1', 'NM796B7335', 'BPKB000035', 'Hitam', 'Solar', 'AG', '043190', 239000, 34000, 1410000, NULL, '2027-04-09', 7365, 5000, 6, 4112, '2026-05-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(36, 1, 3, 'AA 1396 KN', NULL, 'Pemilik Kendaraan 36', 'Kebumen', 'Honda Brio', '2022', '2022', '1500 CC', 'Merah', 'NREF70459230C5C', 'NM5B1C5010', 'BPKB000036', 'Hitam', 'Pertamax Turbo', 'AA', '044424', 286000, 71000, 1491000, NULL, '2028-07-09', 65831, 5000, 6, 60996, '2025-11-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(37, 1, 1, 'AB 1407 LO', NULL, 'Pemilik Kendaraan 37', 'Purwokerto', 'Honda Mobilio', '2024', '2024', '1000 CC', 'Biru', 'NRB4F6268FE8E3C', 'NM66823A28', 'BPKB000037', 'Hitam', 'Pertalite', 'AB', '045658', 468000, 60000, 1032000, NULL, '2027-10-09', 46962, 5000, 6, 45671, '2026-02-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(38, 1, 2, 'AD 1418 MP', NULL, 'Pemilik Kendaraan 38', 'Temanggung', 'Honda HR-V', '2023', '2023', '1500 CC', 'Abu-abu', 'NR09F7566B4E018', 'NM6D3C7CF5', 'BPKB000038', 'Hitam', 'Pertamax', 'AD', '046892', 549000, 69000, 912000, NULL, '2027-10-09', 105806, 5000, 6, 104259, '2026-01-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(39, 1, 3, 'AE 1429 NQ', NULL, 'Pemilik Kendaraan 39', 'Kendal', 'Honda CR-V', '2023', '2023', '1500 CC', 'Coklat', 'NR2DFC28E9E22E4', 'NMBE1C5CD3', 'BPKB000039', 'Hitam', 'Solar', 'AE', '048126', 472000, 44000, 1196000, NULL, '2028-05-09', 26351, 5000, 6, 23045, '2025-10-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(40, 1, 1, 'AG 1440 OR', NULL, 'Pemilik Kendaraan 40', 'Batang', 'Honda Jazz', '2021', '2021', '1500 CC', 'Kuning', 'NRD21A1A31A7898', 'NM21C25BDA', 'BPKB000040', 'Hitam', 'Pertamax Turbo', 'AG', '049360', 228000, 74000, 1706000, NULL, '2027-02-09', 111782, 5000, 6, 104093, '2025-08-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(41, 1, 2, 'AA 1451 PS', NULL, 'Pemilik Kendaraan 41', 'Wonosobo', 'Mitsubishi Xpander', '2024', '2024', '1500 CC', 'Hitam', 'NR5BB2C9B97AB51', 'NM20FFAE4E', 'BPKB000041', 'Hitam', 'Pertalite', 'AA', '050594', 217000, 55000, 803000, NULL, '2028-01-09', 89576, 5000, 6, 81643, '2025-10-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(42, 1, 3, 'AB 1462 QT', NULL, 'Pemilik Kendaraan 42', 'Magelang', 'Mitsubishi Pajero', '2019', '2019', '1000 CC', 'Putih', 'NR4DB76BF66AFB5', 'NM68D57762', 'BPKB000042', 'Hitam', 'Pertamax', 'AB', '051828', 577000, 47000, 1411000, NULL, '2028-06-09', 87410, 5000, 6, 85817, '2025-11-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(43, 1, 1, 'AD 1473 RU', NULL, 'Pemilik Kendaraan 43', 'Purworejo', 'Mitsubishi L300', '2015', '2015', '1500 CC', 'Silver', 'NR0DC971245341E', 'NMBA4C4814', 'BPKB000043', 'Hitam', 'Solar', 'AD', '053062', 315000, 40000, 1682000, NULL, '2028-05-09', 26050, 5000, 6, 18999, '2026-02-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(44, 1, 2, 'AE 1484 SV', NULL, 'Pemilik Kendaraan 44', 'Kebumen', 'Mitsubishi Outlander', '2021', '2021', '1500 CC', 'Merah', 'NR476664C2FBE1E', 'NMB8004991', 'BPKB000044', 'Hitam', 'Pertamax Turbo', 'AE', '054296', 376000, 52000, 595000, NULL, '2026-11-09', 99827, 5000, 6, 94721, '2025-11-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(45, 1, 3, 'AG 1495 TW', NULL, 'Pemilik Kendaraan 45', 'Purwokerto', 'Daihatsu Xenia', '2024', '2024', '1500 CC', 'Biru', 'NR7627556EB210F', 'NMF58C9AE1', 'BPKB000045', 'Hitam', 'Pertalite', 'AG', '055530', 221000, 40000, 1059000, NULL, '2026-11-09', 38299, 5000, 6, 36646, '2025-07-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(46, 1, 1, 'AA 1506 UX', NULL, 'Pemilik Kendaraan 46', 'Temanggung', 'Daihatsu Terios', '2023', '2023', '1000 CC', 'Abu-abu', 'NR10E297C43FFA9', 'NM06A94CA6', 'BPKB000046', 'Hitam', 'Pertamax', 'AA', '056764', 528000, 72000, 625000, NULL, '2026-06-09', 37171, 5000, 6, 30109, '2025-10-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(47, 1, 2, 'AB 1517 VY', NULL, 'Pemilik Kendaraan 47', 'Kendal', 'Daihatsu Sigra', '2018', '2018', '1500 CC', 'Coklat', 'NRF3E8AC22132E1', 'NM16A92731', 'BPKB000047', 'Hitam', 'Solar', 'AB', '057998', 457000, 56000, 1939000, NULL, '2027-07-09', 70623, 5000, 6, 68788, '2026-03-09', 'aman', 'service', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(48, 1, 3, 'AD 1528 WZ', NULL, 'Pemilik Kendaraan 48', 'Batang', 'Daihatsu Gran Max', '2023', '2023', '1000 CC', 'Kuning', 'NR8F3EB52F6AA35', 'NM161F258D', 'BPKB000048', 'Hitam', 'Pertamax Turbo', 'AD', '059232', 344000, 48000, 705000, NULL, '2027-11-09', 116456, 5000, 6, 114481, '2026-01-09', 'service', 'bermasalah', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(49, 1, 1, 'AE 1539 XA', NULL, 'Pemilik Kendaraan 49', 'Wonosobo', 'Suzuki Ertiga', '2022', '2022', '1500 CC', 'Hitam', 'NREDD43B2F51720', 'NMDE6CB379', 'BPKB000049', 'Hitam', 'Pertalite', 'AE', '060466', 431000, 55000, 1725000, NULL, '2026-04-09', 86120, 5000, 6, 82500, '2026-04-09', 'aman', 'tersedia', '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(50, 1, 2, 'AG 1550 YB', NULL, 'Pemilik Kendaraan 50', 'Magelang', 'Suzuki APV', '2020', '2020', '1000 CC', 'Putih', 'NREBB917F2E3BB4', 'NM0093B6FC', 'BPKB000050', 'Hitam', 'Pertamax', 'AG', '061700', 264000, 72000, 502000, NULL, '2027-01-09', 97956, 5000, 6, 93374, '2026-03-09', 'service', 'disewa', '2026-07-09 03:36:17', '2026-07-09 03:36:17');

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
(1, '2026-03-03', 'INV-001', 1, 'Rental', 'cash', 'Penerimaan Rental ke-1', 3208000.00, 0.00, 3208000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, '2026-04-24', 'INV-002', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-2', 1375000.00, 0.00, 4583000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(3, '2026-02-18', 'EXP-003', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-3', 0.00, 1865000.00, 2718000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(4, '2026-02-03', 'INV-004', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-4', 2982000.00, 0.00, 5700000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(5, '2026-03-28', 'INV-005', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-5', 1115000.00, 0.00, 6815000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(6, '2026-03-09', 'EXP-006', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-6', 0.00, 2528000.00, 4287000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(7, '2026-05-09', 'INV-007', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-7', 956000.00, 0.00, 5243000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(8, '2026-03-21', 'INV-008', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-8', 2077000.00, 0.00, 7320000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(9, '2026-05-03', 'EXP-009', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-9', 0.00, 892000.00, 6428000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(10, '2026-03-22', 'INV-010', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-10', 3119000.00, 0.00, 9547000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(11, '2026-04-21', 'INV-011', 1, 'Rental', 'cash', 'Penerimaan Rental ke-11', 2236000.00, 0.00, 11783000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(12, '2026-05-23', 'EXP-012', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-12', 0.00, 1377000.00, 10406000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(13, '2026-03-29', 'INV-013', 1, 'Denda', 'cash', 'Penerimaan Denda ke-13', 575000.00, 0.00, 10981000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(14, '2026-03-02', 'INV-014', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-14', 4520000.00, 0.00, 15501000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(15, '2026-06-27', 'EXP-015', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-15', 0.00, 828000.00, 14673000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(16, '2026-04-29', 'INV-016', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-16', 4748000.00, 0.00, 19421000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(17, '2026-06-19', 'INV-017', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-17', 407000.00, 0.00, 19828000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(18, '2026-04-11', 'EXP-018', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-18', 0.00, 2201000.00, 17627000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(19, '2026-03-01', 'INV-019', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-19', 4513000.00, 0.00, 22140000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(20, '2026-05-06', 'INV-020', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-20', 2865000.00, 0.00, 25005000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(21, '2026-07-08', 'EXP-021', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-21', 0.00, 3934000.00, 21071000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(22, '2026-05-28', 'INV-022', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-22', 4505000.00, 0.00, 25576000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(23, '2026-02-12', 'INV-023', 1, 'Denda', 'cash', 'Penerimaan Denda ke-23', 1443000.00, 0.00, 27019000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(24, '2026-07-03', 'EXP-024', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-24', 0.00, 611000.00, 26408000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(25, '2026-06-07', 'INV-025', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-25', 1307000.00, 0.00, 27715000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(26, '2026-03-02', 'INV-026', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-26', 4826000.00, 0.00, 32541000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(27, '2026-04-20', 'EXP-027', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-27', 0.00, 2846000.00, 29695000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(28, '2026-04-02', 'INV-028', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-28', 568000.00, 0.00, 30263000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(29, '2026-04-07', 'INV-029', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-29', 3908000.00, 0.00, 34171000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(30, '2026-01-21', 'EXP-030', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-30', 0.00, 216000.00, 33955000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(31, '2026-03-26', 'INV-031', 1, 'Rental', 'cash', 'Penerimaan Rental ke-31', 2530000.00, 0.00, 36485000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(32, '2026-01-21', 'INV-032', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-32', 826000.00, 0.00, 37311000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(33, '2026-05-10', 'EXP-033', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-33', 0.00, 3770000.00, 33541000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(34, '2026-03-27', 'INV-034', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-34', 1245000.00, 0.00, 34786000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(35, '2026-04-26', 'INV-035', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-35', 2845000.00, 0.00, 37631000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(36, '2026-01-29', 'EXP-036', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-36', 0.00, 1561000.00, 36070000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(37, '2026-02-25', 'INV-037', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-37', 979000.00, 0.00, 37049000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(38, '2026-06-22', 'INV-038', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-38', 3722000.00, 0.00, 40771000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(39, '2026-04-08', 'EXP-039', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-39', 0.00, 1906000.00, 38865000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(40, '2026-03-12', 'INV-040', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-40', 4251000.00, 0.00, 43116000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(41, '2026-01-16', 'INV-041', 1, 'Rental', 'cash', 'Penerimaan Rental ke-41', 1463000.00, 0.00, 44579000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(42, '2026-05-29', 'EXP-042', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-42', 0.00, 1775000.00, 42804000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(43, '2026-03-02', 'INV-043', 1, 'Denda', 'cash', 'Penerimaan Denda ke-43', 911000.00, 0.00, 43715000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(44, '2026-04-16', 'INV-044', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-44', 730000.00, 0.00, 44445000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(45, '2026-06-16', 'EXP-045', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-45', 0.00, 4747000.00, 39698000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(46, '2026-06-22', 'INV-046', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-46', 3270000.00, 0.00, 42968000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(47, '2026-02-08', 'INV-047', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-47', 3634000.00, 0.00, 46602000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(48, '2026-06-08', 'EXP-048', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-48', 0.00, 630000.00, 45972000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(49, '2026-06-15', 'INV-049', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-49', 3824000.00, 0.00, 49796000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(50, '2026-03-03', 'INV-050', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-50', 245000.00, 0.00, 50041000.00, '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
(1, 1, 'KIR-2026-001', '2027-03-08', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(2, 2, 'KIR-2026-002', '2027-07-06', 250000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(3, 3, 'KIR-2026-003', '2027-12-24', 450000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(4, 4, 'KIR-2026-004', '2027-11-05', 100000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(5, 5, 'KIR-2026-005', '2027-12-07', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(6, 6, 'KIR-2026-006', '2026-10-26', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(7, 7, 'KIR-2026-007', '2027-07-20', 450000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(8, 8, 'KIR-2026-008', '2026-11-13', 250000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(9, 9, 'KIR-2026-009', '2027-09-04', 500000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(10, 10, 'KIR-2026-010', '2027-01-20', 350000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(11, 11, 'KIR-2026-011', '2027-03-04', 400000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(12, 12, 'KIR-2026-012', '2026-11-25', 450000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(13, 13, 'KIR-2026-013', '2026-11-21', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(14, 14, 'KIR-2026-014', '2026-09-01', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(15, 15, 'KIR-2026-015', '2027-12-16', 200000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(16, 16, 'KIR-2026-016', '2027-10-02', 500000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(17, 17, 'KIR-2026-017', '2027-02-02', 400000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(18, 18, 'KIR-2026-018', '2026-05-19', 200000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(19, 19, 'KIR-2026-019', '2028-01-22', 450000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(20, 20, 'KIR-2026-020', '2027-09-27', 50000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(21, 21, 'KIR-2026-021', '2027-03-01', 400000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(22, 22, 'KIR-2026-022', '2026-07-06', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(23, 23, 'KIR-2026-023', '2028-02-19', 350000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(24, 24, 'KIR-2026-024', '2027-09-23', 250000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(25, 25, 'KIR-2026-025', '2026-11-11', 50000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(26, 26, 'KIR-2026-026', '2027-12-04', 100000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(27, 27, 'KIR-2026-027', '2027-07-04', 500000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(28, 28, 'KIR-2026-028', '2026-08-19', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(29, 29, 'KIR-2026-029', '2027-06-26', 500000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(30, 30, 'KIR-2026-030', '2027-08-03', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(31, 31, 'KIR-2026-031', '2027-05-29', 50000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(32, 32, 'KIR-2026-032', '2026-05-13', 350000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(33, 33, 'KIR-2026-033', '2027-04-26', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(34, 34, 'KIR-2026-034', '2026-09-07', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(35, 35, 'KIR-2026-035', '2027-03-05', 50000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(36, 36, 'KIR-2026-036', '2026-12-04', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(37, 37, 'KIR-2026-037', '2026-09-14', 250000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(38, 38, 'KIR-2026-038', '2028-06-18', 450000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(39, 39, 'KIR-2026-039', '2026-12-31', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(40, 40, 'KIR-2026-040', '2027-03-13', 350000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(41, 41, 'KIR-2026-041', '2027-03-12', 200000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(42, 42, 'KIR-2026-042', '2027-01-24', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(43, 43, 'KIR-2026-043', '2027-06-21', 200000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(44, 44, 'KIR-2026-044', '2027-06-09', 150000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(45, 45, 'KIR-2026-045', '2027-11-12', 400000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(46, 46, 'KIR-2026-046', '2026-11-28', 50000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(47, 47, 'KIR-2026-047', '2027-07-19', 400000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(48, 48, 'KIR-2026-048', '2028-04-06', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(49, 49, 'KIR-2026-049', '2028-06-13', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(50, 50, 'KIR-2026-050', '2027-04-20', 300000.00, NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18');

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
(1, 'Andi', '2026-01', 45000000.00, 3.00, 1350000.00, 'Sudah Dibayar', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'Budi', '2026-01', 38000000.00, 3.00, 1140000.00, 'Sudah Dibayar', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'Cici', '2026-01', 52000000.00, 3.50, 1820000.00, 'Sudah Dibayar', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'Dani', '2026-01', 29000000.00, 3.00, 870000.00, 'Sudah Dibayar', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'Andi', '2026-02', 55000000.00, 3.50, 1925000.00, 'Sudah Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'Budi', '2026-02', 42000000.00, 3.00, 1260000.00, 'Sudah Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'Cici', '2026-02', 60000000.00, 4.00, 2400000.00, 'Sudah Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'Dani', '2026-02', 35000000.00, 3.00, 1050000.00, 'Sudah Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(9, 'Andi', '2026-03', 48000000.00, 3.00, 1440000.00, 'Sudah Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(10, 'Budi', '2026-03', 51000000.00, 3.50, 1785000.00, 'Sudah Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(11, 'Cici', '2026-03', 44000000.00, 3.00, 1320000.00, 'Belum Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(12, 'Dani', '2026-03', 39000000.00, 3.00, 1170000.00, 'Belum Dibayar', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'Rini Apriani', 'Q1 2025', 91, 80, 96, 89.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(2, 'Rini Apriani', 'Q2 2025', 98, 87, 70, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(3, 'Rini Apriani', 'Q3 2025', 96, 74, 85, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(4, 'Rini Apriani', 'Q4 2025', 72, 78, 80, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(5, 'Rini Apriani', 'Q1 2026', 68, 75, 94, 79.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(6, 'Rini Apriani', 'Q2 2026', 71, 68, 86, 75.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(7, 'Eko Prasetyo', 'Q1 2025', 89, 85, 80, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(8, 'Eko Prasetyo', 'Q2 2025', 75, 69, 98, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(9, 'Eko Prasetyo', 'Q3 2025', 69, 84, 74, 75.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(10, 'Eko Prasetyo', 'Q4 2025', 86, 84, 95, 88.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(11, 'Eko Prasetyo', 'Q1 2026', 81, 88, 93, 87.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(12, 'Eko Prasetyo', 'Q2 2026', 95, 95, 93, 94.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(13, 'Rizky Fadillah', 'Q1 2025', 65, 95, 86, 82.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(14, 'Rizky Fadillah', 'Q2 2025', 88, 67, 76, 77.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(15, 'Rizky Fadillah', 'Q3 2025', 68, 67, 92, 75.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(16, 'Rizky Fadillah', 'Q4 2025', 67, 77, 94, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(17, 'Rizky Fadillah', 'Q1 2026', 71, 74, 80, 75.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(18, 'Rizky Fadillah', 'Q2 2026', 77, 76, 95, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(19, 'Yusuf Hidayat', 'Q1 2025', 97, 82, 77, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(20, 'Yusuf Hidayat', 'Q2 2025', 74, 93, 78, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(21, 'Yusuf Hidayat', 'Q3 2025', 84, 78, 86, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(22, 'Yusuf Hidayat', 'Q4 2025', 93, 97, 97, 95.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(23, 'Yusuf Hidayat', 'Q1 2026', 66, 72, 77, 71.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(24, 'Yusuf Hidayat', 'Q2 2026', 79, 92, 88, 86.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(25, 'Wahyu Nugroho', 'Q1 2025', 82, 81, 71, 78.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(26, 'Wahyu Nugroho', 'Q2 2025', 84, 88, 93, 88.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(27, 'Wahyu Nugroho', 'Q3 2025', 96, 68, 87, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(28, 'Wahyu Nugroho', 'Q4 2025', 80, 99, 66, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(29, 'Wahyu Nugroho', 'Q1 2026', 66, 76, 87, 76.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(30, 'Wahyu Nugroho', 'Q2 2026', 91, 91, 87, 89.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(31, 'Fitri Handayani', 'Q1 2025', 87, 86, 74, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(32, 'Fitri Handayani', 'Q2 2025', 78, 76, 89, 81.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(33, 'Fitri Handayani', 'Q3 2025', 65, 77, 93, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(34, 'Fitri Handayani', 'Q4 2025', 99, 66, 93, 86.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(35, 'Fitri Handayani', 'Q1 2026', 87, 77, 80, 81.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(36, 'Fitri Handayani', 'Q2 2026', 84, 85, 100, 89.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(37, 'Teguh Santosa', 'Q1 2025', 65, 83, 88, 78.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(38, 'Teguh Santosa', 'Q2 2025', 71, 76, 71, 72.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(39, 'Teguh Santosa', 'Q3 2025', 91, 96, 84, 90.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(40, 'Teguh Santosa', 'Q4 2025', 75, 95, 97, 89.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(41, 'Teguh Santosa', 'Q1 2026', 73, 84, 99, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(42, 'Teguh Santosa', 'Q2 2026', 81, 67, 90, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(43, 'Arif Budiman', 'Q1 2025', 81, 93, 79, 84.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(44, 'Arif Budiman', 'Q2 2025', 79, 83, 68, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(45, 'Arif Budiman', 'Q3 2025', 90, 82, 66, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(46, 'Arif Budiman', 'Q4 2025', 79, 72, 94, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(47, 'Arif Budiman', 'Q1 2026', 87, 92, 85, 88.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(48, 'Arif Budiman', 'Q2 2026', 66, 99, 80, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(49, 'Dewi Kusuma', 'Q1 2025', 82, 75, 99, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(50, 'Dewi Kusuma', 'Q2 2025', 66, 69, 80, 71.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(51, 'Dewi Kusuma', 'Q3 2025', 96, 100, 77, 91.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(52, 'Dewi Kusuma', 'Q4 2025', 96, 70, 90, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(53, 'Dewi Kusuma', 'Q1 2026', 69, 91, 91, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(54, 'Dewi Kusuma', 'Q2 2026', 70, 98, 89, 85.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(55, 'Linda Permata', 'Q1 2025', 67, 69, 75, 70.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(56, 'Linda Permata', 'Q2 2025', 67, 83, 80, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(57, 'Linda Permata', 'Q3 2025', 95, 73, 100, 89.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(58, 'Linda Permata', 'Q4 2025', 77, 99, 66, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(59, 'Linda Permata', 'Q1 2026', 88, 94, 88, 90.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(60, 'Linda Permata', 'Q2 2026', 92, 68, 87, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(61, 'Hendra Gunawan', 'Q1 2025', 79, 89, 91, 86.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(62, 'Hendra Gunawan', 'Q2 2025', 82, 80, 89, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(63, 'Hendra Gunawan', 'Q3 2025', 71, 77, 99, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(64, 'Hendra Gunawan', 'Q4 2025', 84, 74, 94, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(65, 'Hendra Gunawan', 'Q1 2026', 78, 93, 84, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(66, 'Hendra Gunawan', 'Q2 2026', 95, 76, 84, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(67, 'Dody Kurniawan', 'Q1 2025', 99, 78, 92, 89.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(68, 'Dody Kurniawan', 'Q2 2025', 95, 69, 72, 78.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(69, 'Dody Kurniawan', 'Q3 2025', 94, 88, 71, 84.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(70, 'Dody Kurniawan', 'Q4 2025', 85, 85, 84, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(71, 'Dody Kurniawan', 'Q1 2026', 93, 67, 86, 82.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(72, 'Dody Kurniawan', 'Q2 2026', 73, 99, 91, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(73, 'Siti Rahayu', 'Q1 2025', 86, 86, 91, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(74, 'Siti Rahayu', 'Q2 2025', 74, 87, 76, 79.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(75, 'Siti Rahayu', 'Q3 2025', 100, 80, 93, 91.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(76, 'Siti Rahayu', 'Q4 2025', 95, 84, 84, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(77, 'Siti Rahayu', 'Q1 2026', 81, 97, 90, 89.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(78, 'Siti Rahayu', 'Q2 2026', 84, 85, 79, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(79, 'Agus Wibowo', 'Q1 2025', 93, 85, 78, 85.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(80, 'Agus Wibowo', 'Q2 2025', 76, 86, 98, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(81, 'Agus Wibowo', 'Q3 2025', 85, 73, 92, 83.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(82, 'Agus Wibowo', 'Q4 2025', 100, 87, 65, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(83, 'Agus Wibowo', 'Q1 2026', 94, 88, 78, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(84, 'Agus Wibowo', 'Q2 2026', 90, 69, 69, 76.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(85, 'Budi Santoso', 'Q1 2025', 75, 88, 95, 86.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(86, 'Budi Santoso', 'Q2 2025', 89, 96, 69, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(87, 'Budi Santoso', 'Q3 2025', 98, 99, 65, 87.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(88, 'Budi Santoso', 'Q4 2025', 75, 65, 92, 77.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(89, 'Budi Santoso', 'Q1 2026', 90, 97, 83, 90.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(90, 'Budi Santoso', 'Q2 2026', 80, 66, 92, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-09 03:36:26', '2026-07-09 03:36:26');

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
(1, 'APY Rental', 25000000.00, 12000000.00, 13000000.00, '2026-07', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, 'APY Rental', 30000000.00, 15000000.00, 15000000.00, '2026-06', '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(3, 'APY Rental', 18000000.00, 9000000.00, 9000000.00, '2026-05', '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
(1, 'LYL001', 'APY Points', 'Poin', '1 poin per 10.000', '100 poin = Rp 50.000', '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'LYL002', 'Free Day Program', 'Hari Gratis', '10 hari rental = 1 hari gratis', '1 hari gratis per periode', '2026-07-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `member`
--

CREATE TABLE `member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_member` varchar(255) DEFAULT NULL,
  `kontak_member` varchar(255) DEFAULT NULL,
  `email_member` varchar(255) DEFAULT NULL,
  `jenis_member` enum('perorangan','perusahaan') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `member`
--

INSERT INTO `member` (`id`, `nama_member`, `kontak_member`, `email_member`, `jenis_member`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '08943930565', 'budi.santoso@gmail.com', 'perorangan', 'Jl. Wonosobo No. 5', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(2, 'Joko Widodo', '08678407372', 'joko.widodo@gmail.com', 'perorangan', 'Jl. Magelang No. 86', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(3, 'Andi Saputra', '08163698960', 'andi.saputra@gmail.com', 'perorangan', 'Jl. Purworejo No. 25', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(4, 'Rizky Pratama', '08343912348', 'rizky.pratama@gmail.com', 'perorangan', 'Jl. Kebumen No. 33', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(5, 'Dian Permata', '08981256974', 'dian.permata@gmail.com', 'perorangan', 'Jl. Purwokerto No. 61', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(6, 'Siti Rahayu', '08712356421', 'siti.rahayu@gmail.com', 'perorangan', 'Jl. Temanggung No. 8', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(7, 'Ahmad Fauzi', '08273805412', 'ahmad.fauzi@gmail.com', 'perorangan', 'Jl. Kendal No. 91', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(8, 'Dewi Lestari', '08300832745', 'dewi.lestari@gmail.com', 'perorangan', 'Jl. Semarang No. 45', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(9, 'Hendra Gunawan', '08919431271', 'hendra.gunawan@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 63', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(10, 'Rina Wati', '08356685330', 'rina.wati@gmail.com', 'perorangan', 'Jl. Solo No. 14', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(11, 'Bambang Sutrisno', '08839792985', 'bambang.sutrisno@gmail.com', 'perorangan', 'Jl. Wonosobo No. 27', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(12, 'Nia Ramadhani', '08423297352', 'nia.ramadhani@gmail.com', 'perorangan', 'Jl. Magelang No. 76', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(13, 'Ferdy Sambo', '08609592905', 'ferdy.sambo@gmail.com', 'perorangan', 'Jl. Purworejo No. 20', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(14, 'Lina Marlina', '08455898289', 'lina.marlina@gmail.com', 'perorangan', 'Jl. Kebumen No. 43', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(15, 'Tono Suprapto', '08744140403', 'tono.suprapto@gmail.com', 'perorangan', 'Jl. Purwokerto No. 82', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(16, 'Yuli Astuti', '08974106547', 'yuli.astuti@gmail.com', 'perorangan', 'Jl. Temanggung No. 78', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(17, 'Fajar Nugroho', '08488780699', 'fajar.nugroho@gmail.com', 'perorangan', 'Jl. Kendal No. 55', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(18, 'Sri Wahyuni', '08410719872', 'sri.wahyuni@gmail.com', 'perorangan', 'Jl. Semarang No. 36', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(19, 'Rudi Hartono', '08144452560', 'rudi.hartono@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 62', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(20, 'Mega Putri', '08272991361', 'mega.putri@gmail.com', 'perorangan', 'Jl. Solo No. 68', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(21, 'Wahyu Setiawan', '08517853688', 'wahyu.setiawan@gmail.com', 'perorangan', 'Jl. Wonosobo No. 26', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(22, 'Indah Kurniasih', '08834448271', 'indah.kurniasih@gmail.com', 'perorangan', 'Jl. Magelang No. 76', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(23, 'Eko Prasetyo', '08142076197', 'eko.prasetyo@gmail.com', 'perorangan', 'Jl. Purworejo No. 57', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(24, 'Fitri Handayani', '08778213995', 'fitri.handayani@gmail.com', 'perorangan', 'Jl. Kebumen No. 17', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(25, 'Galih Wicaksono', '08741195479', 'galih.wicaksono@gmail.com', 'perorangan', 'Jl. Purwokerto No. 77', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(26, 'PT Maju Bersama', '0240872440', 'ptmajubersama@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 150', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(27, 'CV Sumber Rezeki', '0237341889', 'cvsumberrezeki@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 123', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(28, 'PT Cahaya Abadi', '0276080524', 'ptcahayaabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 90', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(29, 'CV Jaya Mandiri', '0279092606', 'cvjayamandiri@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 195', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(30, 'PT Sukses Selalu', '0237840626', 'ptsuksesselalu@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 189', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(31, 'PT Karya Utama', '0275660287', 'ptkaryautama@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 21', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(32, 'CV Harapan Baru', '0292102473', 'cvharapanbaru@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 32', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(33, 'PT Gemilang Jaya', '0248834878', 'ptgemilangjaya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 178', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(34, 'CV Delta Nusantara', '0271631985', 'cvdeltanusantara@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 192', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(35, 'PT Bintang Timur', '0280660397', 'ptbintangtimur@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 200', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(36, 'PT Nusantara Trans', '0220158369', 'ptnusantaratrans@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 135', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(37, 'CV Permata Hijau', '0285752518', 'cvpermatahijau@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 147', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(38, 'PT Sinar Mas Logistik', '0298312201', 'ptsinarmaslogistik@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 105', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(39, 'CV Berkah Sejati', '0272135473', 'cvberkahsejati@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 91', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(40, 'PT Indo Mitra', '0259970145', 'ptindomitra@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 191', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(41, 'PT Wahana Ekspres', '0249063914', 'ptwahanaekspres@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 34', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(42, 'CV Tirta Agung', '0226780229', 'cvtirtaagung@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 49', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(43, 'PT Mandiri Karya', '0227413136', 'ptmandirikarya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 27', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(44, 'CV Perkasa Utama', '0298309819', 'cvperkasautama@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 75', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(45, 'PT Cipta Rasa', '0272473966', 'ptciptarasa@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 197', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(46, 'PT Lancar Jaya', '0219158186', 'ptlancarjaya@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 126', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(47, 'CV Mitra Usaha', '0212603512', 'cvmitrausaha@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 49', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(48, 'PT Sejahtera Abadi', '0228131366', 'ptsejahteraabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 102', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(49, 'CV Putra Bangsa', '0268868768', 'cvputrabangsa@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 135', '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(50, 'PT Global Trans', '0266121438', 'ptglobaltrans@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 145', '2026-07-09 03:36:18', '2026-07-09 03:36:18');

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
(120, '2026_07_09_000003_create_pergerakan_assets_table', 1),
(121, '2026_07_09_000004_create_pemeliharaan_assets_table', 1),
(122, '2026_07_09_000005_create_penyusutan_assets_table', 1),
(123, '2026_07_09_000006_create_perolehan_assets_table', 1),
(124, '2026_07_09_000007_create_asset_dihapuskans_table', 1),
(125, '2026_07_09_000008_create_dokumentasi_assets_table', 1),
(126, '2026_07_09_000009_create_penanggung_jawabs_table', 1),
(127, '2026_07_09_000010_create_audit_assets_table', 1);

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
(1, 'Kantor Pusat Jakarta', '103.12.45.67', 'Online', '500 Mbps', '0 jam/bulan', 'Koneksi utama Indihome Business', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'Cabang Surabaya', '202.67.88.12', 'Online', '100 Mbps', '2 jam/bulan', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'Gudang Bekasi', '180.244.33.91', 'Warning', '50 Mbps', '5 jam/bulan', 'Sering gangguan sore hari', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'Data Center Cibitung', '103.88.12.200', 'Online', '1 Gbps', '0 jam/bulan', 'Tier 3 data center', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'Cabang Bandung', '36.91.44.111', 'Offline', '100 Mbps', '8 jam/bulan', 'Sedang dalam perbaikan jalur fiber', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'WF001', 'Welcome Email', 'Registrasi Baru', 'Member baru', 'Kirim Email Selamat Datang', '10 menit', 'Aktif', 'System', 'Auto-email untuk member baru', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'WF002', 'Reminder Pembayaran', 'H-2 Jatuh Tempo', 'Belum bayar', 'Kirim Notifikasi WA', 'Langsung', 'Aktif', 'Finance', 'Pengingat otomatis pembayaran', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 1, 'Pajak Tahunan', 4700000.00, '2026-09-05', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(2, 2, 'Pajak 5 Tahunan', 6000000.00, '2027-04-11', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(3, 3, 'STNK', 1800000.00, '2027-06-09', '2026-06-23', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(4, 4, 'BPKB', 3000000.00, '2026-08-12', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(5, 5, 'BBN-KB', 2400000.00, '2026-07-18', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(6, 6, 'Pajak Tahunan', 1700000.00, '2026-08-04', '2026-06-20', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(7, 7, 'Pajak 5 Tahunan', 4700000.00, '2027-03-27', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(8, 8, 'STNK', 3400000.00, '2026-10-14', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(9, 9, 'BPKB', 1100000.00, '2026-08-16', '2026-06-23', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(10, 10, 'BBN-KB', 4200000.00, '2026-08-03', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(11, 11, 'Pajak Tahunan', 3800000.00, '2027-01-15', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(12, 12, 'Pajak 5 Tahunan', 4300000.00, '2027-02-17', '2026-07-04', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(13, 13, 'STNK', 3000000.00, '2026-06-09', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(14, 14, 'BPKB', 3500000.00, '2026-10-03', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(15, 15, 'BBN-KB', 3300000.00, '2027-04-20', '2026-07-04', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(16, 16, 'Pajak Tahunan', 3700000.00, '2026-11-18', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(17, 17, 'Pajak 5 Tahunan', 700000.00, '2026-11-07', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(18, 18, 'STNK', 2100000.00, '2026-07-16', '2026-07-05', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(19, 19, 'BPKB', 1300000.00, '2026-06-10', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(20, 20, 'BBN-KB', 4900000.00, '2026-11-08', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(21, 21, 'Pajak Tahunan', 2700000.00, '2027-02-18', '2026-06-21', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(22, 22, 'Pajak 5 Tahunan', 2100000.00, '2027-03-18', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(23, 23, 'STNK', 5000000.00, '2026-08-26', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(24, 24, 'BPKB', 2600000.00, '2027-05-30', '2026-07-05', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(25, 25, 'BBN-KB', 3600000.00, '2027-05-15', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(26, 26, 'Pajak Tahunan', 4200000.00, '2027-01-13', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(27, 27, 'Pajak 5 Tahunan', 3600000.00, '2027-05-17', '2026-06-11', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(28, 28, 'STNK', 5700000.00, '2026-06-13', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(29, 29, 'BPKB', 3700000.00, '2027-03-12', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(30, 30, 'BBN-KB', 900000.00, '2026-08-16', '2026-06-10', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(31, 31, 'Pajak Tahunan', 2100000.00, '2026-09-27', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(32, 32, 'Pajak 5 Tahunan', 3500000.00, '2026-09-17', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(33, 33, 'STNK', 2700000.00, '2027-07-05', '2026-07-02', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(34, 34, 'BPKB', 4400000.00, '2026-10-14', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(35, 35, 'BBN-KB', 4300000.00, '2026-11-30', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(36, 36, 'Pajak Tahunan', 800000.00, '2026-12-12', '2026-07-02', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(37, 37, 'Pajak 5 Tahunan', 5400000.00, '2026-12-15', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(38, 38, 'STNK', 1200000.00, '2027-07-02', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(39, 39, 'BPKB', 2600000.00, '2026-10-02', '2026-06-09', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(40, 40, 'BBN-KB', 800000.00, '2027-05-18', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(41, 41, 'Pajak Tahunan', 6000000.00, '2027-03-27', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(42, 42, 'Pajak 5 Tahunan', 900000.00, '2026-06-12', '2026-06-19', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(43, 43, 'STNK', 3300000.00, '2027-02-20', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(44, 44, 'BPKB', 600000.00, '2027-02-23', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(45, 45, 'BBN-KB', 700000.00, '2026-11-17', '2026-06-24', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(46, 46, 'Pajak Tahunan', 2400000.00, '2026-10-23', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(47, 47, 'Pajak 5 Tahunan', 4100000.00, '2026-06-15', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(48, 48, 'STNK', 1200000.00, '2026-09-24', '2026-06-28', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(49, 49, 'BPKB', 3900000.00, '2026-08-19', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17'),
(50, 50, 'BBN-KB', 2200000.00, '2026-08-31', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-09 03:36:17', '2026-07-09 03:36:17');

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
(1, 'Budi Santoso', 25000000.00, 5000000.00, 25000000.00, 500000.00, 2500000.00, 27000000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(2, 'Siti Rahayu', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(3, 'Agus Wibowo', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(4, 'Dewi Kusuma', 12000000.00, 2000000.00, 12000000.00, 240000.00, 600000.00, 13160000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(5, 'Rini Apriani', 6000000.00, 1000000.00, 6000000.00, 120000.00, 150000.00, 6730000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(6, 'Eko Prasetyo', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(7, 'Hendra Gunawan', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(8, 'Rizky Fadillah', 7000000.00, 1200000.00, 7000000.00, 140000.00, 200000.00, 7860000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(9, 'Yusuf Hidayat', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(10, 'Linda Permata', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(11, 'Wahyu Nugroho', 7500000.00, 1200000.00, 7500000.00, 150000.00, 220000.00, 8330000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(12, 'Fitri Handayani', 6500000.00, 1000000.00, 6500000.00, 130000.00, 170000.00, 7200000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(13, 'Dody Kurniawan', 11000000.00, 2000000.00, 11000000.00, 220000.00, 550000.00, 12230000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(14, 'Teguh Santosa', 8000000.00, 1500000.00, 8000000.00, 160000.00, 280000.00, 9060000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(15, 'Arif Budiman', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-09 03:36:25', '2026-07-09 03:36:25');

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
(1, 'PR-PRJ001-001', 'PRJ001', 'Semen Portland 40kg', 500, 'PT Semen Indonesia', 20000000, 'Disetujui', '2026-01-08', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'PR-PRJ001-002', 'PRJ001', 'Besi Beton 10mm', 200, 'PT Krakatau Steel', 35000000, 'Disetujui', '2026-01-10', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(3, 'PR-PRJ001-003', 'PRJ001', 'Bata Merah 20x10x5', 5000, 'CV Bata Kuat', 10000000, 'Disetujui', '2026-01-12', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(4, 'PR-PRJ001-004', 'PRJ001', 'Cat Tembok & Finishing', 50, 'PT Nippon Paint', 15000000, 'Pending', '2026-02-20', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(5, 'PR-PRJ002-001', 'PRJ002', 'Bus Pariwisata 32 Seat', 3, 'PT Hino Motors', 1200000000, 'Disetujui', '2026-02-05', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'PR-PRJ002-002', 'PRJ002', 'Wrapping & Branding Bus', 3, 'CV Kreatif Visual', 15000000, 'Pending', '2026-04-01', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'PR-PRJ003-001', 'PRJ003', 'Unit GPS Tracker', 50, 'PT TechMaps', 100000000, 'Disetujui', '2026-01-14', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'PR-PRJ003-002', 'PRJ003', 'Server Dashboard Cloud', 1, 'PT AWS Indonesia', 24000000, 'Disetujui', '2026-01-15', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'QUO-2026-001', '2026-01-15', 'PT Maju Bersama', 'Sewa Minibus', 2, 5000000.00, 10000000.00, 'Disetujui', '2026-02-15', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'QUO-2026-002', '2026-01-20', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 8000000.00, 'Terkirim', '2026-02-20', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'QUO-2026-003', '2026-02-01', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 3500000.00, 10500000.00, 'Draft', '2026-03-01', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'QUO-2026-004', '2026-02-10', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 15000000.00, 'Disetujui', '2026-03-10', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'QUO-2026-005', '2026-02-25', 'CV Jaya Mandiri', 'Sewa MPV', 4, 4000000.00, 16000000.00, 'Terkirim', '2026-03-25', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'QUO-2026-006', '2026-03-05', 'PT Nusantara Raya', 'Sewa Minibus', 2, 5500000.00, 11000000.00, 'Disetujui', '2026-04-05', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'QUO-2026-007', '2026-03-15', 'PT Sinar Harapan', 'Sewa SUV', 2, 6000000.00, 12000000.00, 'Ditolak', '2026-04-15', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'QUO-2026-008', '2026-04-01', 'CV Mitra Logistik', 'Sewa Truk', 3, 7500000.00, 22500000.00, 'Terkirim', '2026-05-01', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(9, 'QUO-2026-009', '2026-04-20', 'PT Berlian Trans', 'Sewa Bus Medium', 2, 10000000.00, 20000000.00, 'Disetujui', '2026-05-20', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(10, 'QUO-2026-010', '2026-05-10', 'PT Prima Raya', 'Sewa Sedan', 5, 3000000.00, 15000000.00, 'Draft', '2026-06-10', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(11, 'QUO-001', '2026-05-10', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 4, 2697848.00, 10791392.00, 'Draft', '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(12, 'QUO-002', '2026-02-25', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 7, 3451418.00, 24159926.00, 'Terkirim', '2026-03-17', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(13, 'QUO-003', '2026-04-24', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 6, 3890384.00, 23342304.00, 'Disetujui', '2026-06-07', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(14, 'QUO-004', '2026-05-21', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 5, 2176449.00, 10882245.00, 'Ditolak', '2026-07-06', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(15, 'QUO-005', '2026-03-04', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 2, 4948694.00, 9897388.00, 'Draft', '2026-03-23', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(16, 'QUO-006', '2026-02-01', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 3, 3371118.00, 10113354.00, 'Terkirim', '2026-03-15', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(17, 'QUO-007', '2026-04-06', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 5, 2973421.00, 14867105.00, 'Disetujui', '2026-05-09', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(18, 'QUO-008', '2026-02-26', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 4, 1604327.00, 6417308.00, 'Ditolak', '2026-04-05', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(19, 'QUO-009', '2026-04-11', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 3, 1831559.00, 5494677.00, 'Draft', '2026-06-04', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(20, 'QUO-010', '2026-04-03', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 9, 1671452.00, 15043068.00, 'Terkirim', '2026-05-23', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(21, 'QUO-011', '2026-02-09', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 4, 3514499.00, 14057996.00, 'Disetujui', '2026-04-02', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(22, 'QUO-012', '2026-01-14', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 8, 2154346.00, 17234768.00, 'Ditolak', '2026-02-11', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(23, 'QUO-013', '2026-02-08', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 1, 1466155.00, 1466155.00, 'Draft', '2026-03-07', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(24, 'QUO-014', '2026-01-26', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 3, 949010.00, 2847030.00, 'Terkirim', '2026-02-15', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(25, 'QUO-015', '2026-06-30', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 4, 1701393.00, 6805572.00, 'Disetujui', '2026-08-12', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(26, 'QUO-016', '2026-03-15', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 4, 4933581.00, 19734324.00, 'Ditolak', '2026-03-29', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(27, 'QUO-017', '2026-01-13', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 9, 1272189.00, 11449701.00, 'Draft', '2026-02-22', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(28, 'QUO-018', '2026-01-10', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 6, 598842.00, 3593052.00, 'Terkirim', '2026-03-05', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(29, 'QUO-019', '2026-04-05', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 8, 997342.00, 7978736.00, 'Disetujui', '2026-05-20', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(30, 'QUO-020', '2026-03-26', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 7, 2628279.00, 18397953.00, 'Ditolak', '2026-05-20', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(31, 'QUO-021', '2026-04-03', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 6, 3572260.00, 21433560.00, 'Draft', '2026-05-27', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(32, 'QUO-022', '2026-07-07', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 4, 4410055.00, 17640220.00, 'Terkirim', '2026-07-29', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(33, 'QUO-023', '2026-02-23', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 8, 2317804.00, 18542432.00, 'Disetujui', '2026-04-03', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(34, 'QUO-024', '2026-01-27', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 5, 2736967.00, 13684835.00, 'Ditolak', '2026-02-24', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(35, 'QUO-025', '2026-02-18', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 9, 4105263.00, 36947367.00, 'Draft', '2026-03-19', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(36, 'QUO-026', '2026-02-17', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 7, 2630600.00, 18414200.00, 'Terkirim', '2026-03-13', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(37, 'QUO-027', '2026-01-26', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 10, 1339332.00, 13393320.00, 'Disetujui', '2026-03-09', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(38, 'QUO-028', '2026-04-28', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 2, 3966945.00, 7933890.00, 'Ditolak', '2026-06-23', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(39, 'QUO-029', '2026-01-31', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 7, 548293.00, 3838051.00, 'Draft', '2026-02-22', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(40, 'QUO-030', '2026-05-04', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 8, 4206618.00, 33652944.00, 'Terkirim', '2026-06-20', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'Kebijakan Keamanan Informasi', 'v2.1', '2024-01-01', 'IT Manager', 'Aktif', 'ISO 27001', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'Prosedur Backup & Recovery', 'v1.3', '2024-03-01', 'System Administrator', 'Aktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'Kebijakan Penggunaan Aset IT', 'v1.0', '2024-06-01', 'HR & IT Manager', 'Draft', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'Disaster Recovery Plan', 'v3.0', '2023-07-01', 'CTO', 'Review', 'ISO 22301', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'Kebijakan Password & Akses', 'v2.0', '2024-01-01', 'IT Security Officer', 'Aktif', 'ISO 27001', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'Budi Santoso', '2026-06-10', '07:48:00', '18:38:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(2, 'Dewi Kusuma', '2026-06-10', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(3, 'Rini Apriani', '2026-06-10', '08:52:00', '17:51:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(4, 'Eko Prasetyo', '2026-06-10', '08:14:00', '17:46:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(5, 'Hendra Gunawan', '2026-06-10', '07:03:00', '18:32:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(6, 'Rizky Fadillah', '2026-06-10', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(7, 'Yusuf Hidayat', '2026-06-10', '08:09:00', '18:50:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(8, 'Linda Permata', '2026-06-10', '07:38:00', '17:58:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(9, 'Wahyu Nugroho', '2026-06-10', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(10, 'Fitri Handayani', '2026-06-10', '07:26:00', '18:01:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(11, 'Dody Kurniawan', '2026-06-10', '08:00:00', '18:31:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(12, 'Teguh Santosa', '2026-06-10', '08:57:00', '18:13:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(13, 'Budi Santoso', '2026-06-11', '08:47:00', '17:59:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(14, 'Dewi Kusuma', '2026-06-11', '07:17:00', '17:05:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(15, 'Rini Apriani', '2026-06-11', '08:40:00', '17:15:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(16, 'Eko Prasetyo', '2026-06-11', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(17, 'Hendra Gunawan', '2026-06-11', '08:23:00', '17:51:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(18, 'Rizky Fadillah', '2026-06-11', '08:07:00', '18:09:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(19, 'Yusuf Hidayat', '2026-06-11', '07:20:00', '17:28:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(20, 'Linda Permata', '2026-06-11', '07:44:00', '17:56:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(21, 'Wahyu Nugroho', '2026-06-11', '07:45:00', '17:17:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(22, 'Fitri Handayani', '2026-06-11', '09:39:00', '18:12:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(23, 'Dody Kurniawan', '2026-06-11', '07:38:00', '18:42:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(24, 'Teguh Santosa', '2026-06-11', '08:44:00', '18:50:00', 'Face ID', 'WFH', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(25, 'Budi Santoso', '2026-06-12', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(26, 'Dewi Kusuma', '2026-06-12', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(27, 'Rini Apriani', '2026-06-12', '08:57:00', '17:08:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(28, 'Eko Prasetyo', '2026-06-12', '08:25:00', '17:31:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(29, 'Hendra Gunawan', '2026-06-12', '08:26:00', '18:34:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(30, 'Rizky Fadillah', '2026-06-12', '07:00:00', '18:29:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(31, 'Yusuf Hidayat', '2026-06-12', '07:00:00', '18:35:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(32, 'Linda Permata', '2026-06-12', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(33, 'Wahyu Nugroho', '2026-06-12', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(34, 'Fitri Handayani', '2026-06-12', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(35, 'Dody Kurniawan', '2026-06-12', '09:24:00', '18:43:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(36, 'Teguh Santosa', '2026-06-12', '08:48:00', '17:30:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(37, 'Budi Santoso', '2026-06-15', '07:26:00', '17:00:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(38, 'Dewi Kusuma', '2026-06-15', '07:08:00', '17:50:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(39, 'Rini Apriani', '2026-06-15', '08:23:00', '18:27:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(40, 'Eko Prasetyo', '2026-06-15', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(41, 'Hendra Gunawan', '2026-06-15', '09:36:00', '17:28:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(42, 'Rizky Fadillah', '2026-06-15', '08:59:00', '18:30:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(43, 'Yusuf Hidayat', '2026-06-15', '07:46:00', '18:26:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(44, 'Linda Permata', '2026-06-15', '09:57:00', '17:51:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(45, 'Wahyu Nugroho', '2026-06-15', '08:26:00', '17:35:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(46, 'Fitri Handayani', '2026-06-15', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(47, 'Dody Kurniawan', '2026-06-15', '07:33:00', '18:02:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(48, 'Teguh Santosa', '2026-06-15', '07:06:00', '18:24:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(49, 'Budi Santoso', '2026-06-16', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(50, 'Dewi Kusuma', '2026-06-16', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(51, 'Rini Apriani', '2026-06-16', '08:27:00', '18:02:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(52, 'Eko Prasetyo', '2026-06-16', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(53, 'Hendra Gunawan', '2026-06-16', '09:40:00', '18:19:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(54, 'Rizky Fadillah', '2026-06-16', '07:49:00', '17:05:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(55, 'Yusuf Hidayat', '2026-06-16', '08:00:00', '17:25:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(56, 'Linda Permata', '2026-06-16', '08:50:00', '17:21:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(57, 'Wahyu Nugroho', '2026-06-16', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(58, 'Fitri Handayani', '2026-06-16', '08:49:00', '17:23:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(59, 'Dody Kurniawan', '2026-06-16', '09:12:00', '17:52:00', 'Manual', 'WFH', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(60, 'Teguh Santosa', '2026-06-16', '07:53:00', '17:11:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(61, 'Budi Santoso', '2026-06-17', '08:01:00', '18:18:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(62, 'Dewi Kusuma', '2026-06-17', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(63, 'Rini Apriani', '2026-06-17', '08:47:00', '17:32:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(64, 'Eko Prasetyo', '2026-06-17', '08:47:00', '18:58:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(65, 'Hendra Gunawan', '2026-06-17', '07:24:00', '18:35:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(66, 'Rizky Fadillah', '2026-06-17', '08:52:00', '18:34:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(67, 'Yusuf Hidayat', '2026-06-17', '07:07:00', '17:08:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(68, 'Linda Permata', '2026-06-17', '08:25:00', '18:54:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(69, 'Wahyu Nugroho', '2026-06-17', '07:50:00', '17:26:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(70, 'Fitri Handayani', '2026-06-17', '09:14:00', '18:26:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(71, 'Dody Kurniawan', '2026-06-17', '08:00:00', '18:31:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(72, 'Teguh Santosa', '2026-06-17', '09:45:00', '17:06:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(73, 'Budi Santoso', '2026-06-18', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(74, 'Dewi Kusuma', '2026-06-18', '07:17:00', '17:34:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(75, 'Rini Apriani', '2026-06-18', '08:57:00', '18:50:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(76, 'Eko Prasetyo', '2026-06-18', '09:59:00', '17:13:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(77, 'Hendra Gunawan', '2026-06-18', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(78, 'Rizky Fadillah', '2026-06-18', '08:47:00', '17:32:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(79, 'Yusuf Hidayat', '2026-06-18', '08:58:00', '17:38:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(80, 'Linda Permata', '2026-06-18', '09:54:00', '18:46:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(81, 'Wahyu Nugroho', '2026-06-18', '07:51:00', '18:40:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(82, 'Fitri Handayani', '2026-06-18', '09:21:00', '17:25:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(83, 'Dody Kurniawan', '2026-06-18', '08:23:00', '17:12:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(84, 'Teguh Santosa', '2026-06-18', '08:31:00', '18:02:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(85, 'Budi Santoso', '2026-06-19', '08:10:00', '17:42:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(86, 'Dewi Kusuma', '2026-06-19', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(87, 'Rini Apriani', '2026-06-19', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(88, 'Eko Prasetyo', '2026-06-19', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(89, 'Hendra Gunawan', '2026-06-19', '07:26:00', '18:01:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(90, 'Rizky Fadillah', '2026-06-19', '08:35:00', '18:41:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(91, 'Yusuf Hidayat', '2026-06-19', '08:46:00', '17:02:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(92, 'Linda Permata', '2026-06-19', '07:09:00', '18:56:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(93, 'Wahyu Nugroho', '2026-06-19', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(94, 'Fitri Handayani', '2026-06-19', '07:38:00', '17:43:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(95, 'Dody Kurniawan', '2026-06-19', '08:10:00', '18:23:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(96, 'Teguh Santosa', '2026-06-19', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(97, 'Budi Santoso', '2026-06-22', '09:16:00', '18:49:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(98, 'Dewi Kusuma', '2026-06-22', '07:41:00', '18:03:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(99, 'Rini Apriani', '2026-06-22', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(100, 'Eko Prasetyo', '2026-06-22', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(101, 'Hendra Gunawan', '2026-06-22', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(102, 'Rizky Fadillah', '2026-06-22', '08:23:00', '17:45:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(103, 'Yusuf Hidayat', '2026-06-22', '07:29:00', '18:02:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(104, 'Linda Permata', '2026-06-22', '07:15:00', '18:03:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(105, 'Wahyu Nugroho', '2026-06-22', '07:28:00', '17:42:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(106, 'Fitri Handayani', '2026-06-22', '07:26:00', '17:07:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(107, 'Dody Kurniawan', '2026-06-22', '07:43:00', '17:10:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(108, 'Teguh Santosa', '2026-06-22', '08:03:00', '17:09:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(109, 'Budi Santoso', '2026-06-23', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(110, 'Dewi Kusuma', '2026-06-23', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(111, 'Rini Apriani', '2026-06-23', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(112, 'Eko Prasetyo', '2026-06-23', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(113, 'Hendra Gunawan', '2026-06-23', '09:15:00', '18:02:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(114, 'Rizky Fadillah', '2026-06-23', '09:23:00', '18:43:00', 'Manual', 'WFH', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(115, 'Yusuf Hidayat', '2026-06-23', '08:55:00', '18:20:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(116, 'Linda Permata', '2026-06-23', '08:50:00', '17:42:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(117, 'Wahyu Nugroho', '2026-06-23', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(118, 'Fitri Handayani', '2026-06-23', '08:18:00', '17:18:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(119, 'Dody Kurniawan', '2026-06-23', '08:46:00', '17:59:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(120, 'Teguh Santosa', '2026-06-23', '07:04:00', '17:20:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(121, 'Budi Santoso', '2026-06-24', '08:44:00', '17:23:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(122, 'Dewi Kusuma', '2026-06-24', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(123, 'Rini Apriani', '2026-06-24', '08:18:00', '18:31:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(124, 'Eko Prasetyo', '2026-06-24', '08:29:00', '17:48:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(125, 'Hendra Gunawan', '2026-06-24', '07:58:00', '17:31:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(126, 'Rizky Fadillah', '2026-06-24', '07:41:00', '18:32:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(127, 'Yusuf Hidayat', '2026-06-24', '09:16:00', '18:17:00', 'Face ID', 'WFH', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(128, 'Linda Permata', '2026-06-24', '08:17:00', '18:03:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(129, 'Wahyu Nugroho', '2026-06-24', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(130, 'Fitri Handayani', '2026-06-24', '08:59:00', '18:03:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(131, 'Dody Kurniawan', '2026-06-24', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(132, 'Teguh Santosa', '2026-06-24', '08:57:00', '17:47:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(133, 'Budi Santoso', '2026-06-25', '07:08:00', '18:42:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(134, 'Dewi Kusuma', '2026-06-25', '07:43:00', '18:17:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(135, 'Rini Apriani', '2026-06-25', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(136, 'Eko Prasetyo', '2026-06-25', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(137, 'Hendra Gunawan', '2026-06-25', '09:32:00', '17:23:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(138, 'Rizky Fadillah', '2026-06-25', '08:03:00', '17:14:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(139, 'Yusuf Hidayat', '2026-06-25', '07:45:00', '17:17:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(140, 'Linda Permata', '2026-06-25', '08:10:00', '18:01:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(141, 'Wahyu Nugroho', '2026-06-25', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(142, 'Fitri Handayani', '2026-06-25', '08:19:00', '17:52:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(143, 'Dody Kurniawan', '2026-06-25', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(144, 'Teguh Santosa', '2026-06-25', '08:11:00', '18:08:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(145, 'Budi Santoso', '2026-06-26', '08:16:00', '18:35:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(146, 'Dewi Kusuma', '2026-06-26', '07:29:00', '17:49:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(147, 'Rini Apriani', '2026-06-26', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(148, 'Eko Prasetyo', '2026-06-26', '07:08:00', '17:46:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(149, 'Hendra Gunawan', '2026-06-26', '08:53:00', '18:01:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(150, 'Rizky Fadillah', '2026-06-26', '08:09:00', '18:45:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(151, 'Yusuf Hidayat', '2026-06-26', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(152, 'Linda Permata', '2026-06-26', '07:56:00', '18:57:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(153, 'Wahyu Nugroho', '2026-06-26', '07:04:00', '18:34:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(154, 'Fitri Handayani', '2026-06-26', '09:27:00', '18:39:00', 'Manual', 'WFH', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(155, 'Dody Kurniawan', '2026-06-26', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(156, 'Teguh Santosa', '2026-06-26', '07:29:00', '18:08:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(157, 'Budi Santoso', '2026-06-29', '07:38:00', '18:11:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(158, 'Dewi Kusuma', '2026-06-29', '08:47:00', '18:27:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(159, 'Rini Apriani', '2026-06-29', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(160, 'Eko Prasetyo', '2026-06-29', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(161, 'Hendra Gunawan', '2026-06-29', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(162, 'Rizky Fadillah', '2026-06-29', '07:16:00', '18:43:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(163, 'Yusuf Hidayat', '2026-06-29', '07:35:00', '17:18:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(164, 'Linda Permata', '2026-06-29', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(165, 'Wahyu Nugroho', '2026-06-29', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(166, 'Fitri Handayani', '2026-06-29', '08:27:00', '17:27:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(167, 'Dody Kurniawan', '2026-06-29', '09:53:00', '18:16:00', 'Fingerprint', 'Lapangan', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(168, 'Teguh Santosa', '2026-06-29', '07:24:00', '18:43:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(169, 'Budi Santoso', '2026-06-30', '08:46:00', '18:01:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(170, 'Dewi Kusuma', '2026-06-30', '07:56:00', '18:54:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(171, 'Rini Apriani', '2026-06-30', '07:42:00', '17:43:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(172, 'Eko Prasetyo', '2026-06-30', '08:31:00', '17:39:00', 'Manual', 'WFH', 'Terlambat', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(173, 'Hendra Gunawan', '2026-06-30', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(174, 'Rizky Fadillah', '2026-06-30', '08:58:00', '18:48:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(175, 'Yusuf Hidayat', '2026-06-30', '07:27:00', '18:19:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(176, 'Linda Permata', '2026-06-30', '09:27:00', '17:08:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(177, 'Wahyu Nugroho', '2026-06-30', '08:38:00', '17:01:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(178, 'Fitri Handayani', '2026-06-30', '07:27:00', '18:01:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(179, 'Dody Kurniawan', '2026-06-30', '08:30:00', '17:42:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(180, 'Teguh Santosa', '2026-06-30', '07:11:00', '17:09:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(181, 'Budi Santoso', '2026-07-01', '07:19:00', '18:14:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(182, 'Dewi Kusuma', '2026-07-01', '09:47:00', '18:02:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(183, 'Rini Apriani', '2026-07-01', '09:39:00', '17:12:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(184, 'Eko Prasetyo', '2026-07-01', '08:48:00', '18:53:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(185, 'Hendra Gunawan', '2026-07-01', '07:57:00', '17:04:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(186, 'Rizky Fadillah', '2026-07-01', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(187, 'Yusuf Hidayat', '2026-07-01', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(188, 'Linda Permata', '2026-07-01', '09:21:00', '17:23:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(189, 'Wahyu Nugroho', '2026-07-01', '07:14:00', '18:41:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(190, 'Fitri Handayani', '2026-07-01', '09:14:00', '17:17:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(191, 'Dody Kurniawan', '2026-07-01', '08:12:00', '17:33:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(192, 'Teguh Santosa', '2026-07-01', '08:48:00', '17:28:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(193, 'Budi Santoso', '2026-07-02', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(194, 'Dewi Kusuma', '2026-07-02', '08:19:00', '17:47:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(195, 'Rini Apriani', '2026-07-02', '09:13:00', '17:09:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(196, 'Eko Prasetyo', '2026-07-02', '07:06:00', '17:40:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(197, 'Hendra Gunawan', '2026-07-02', '07:46:00', '18:39:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(198, 'Rizky Fadillah', '2026-07-02', '07:33:00', '18:20:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(199, 'Yusuf Hidayat', '2026-07-02', '07:01:00', '17:49:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(200, 'Linda Permata', '2026-07-02', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(201, 'Wahyu Nugroho', '2026-07-02', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(202, 'Fitri Handayani', '2026-07-02', '08:02:00', '17:44:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(203, 'Dody Kurniawan', '2026-07-02', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(204, 'Teguh Santosa', '2026-07-02', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(205, 'Budi Santoso', '2026-07-03', '08:40:00', '18:06:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(206, 'Dewi Kusuma', '2026-07-03', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(207, 'Rini Apriani', '2026-07-03', '08:41:00', '17:17:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(208, 'Eko Prasetyo', '2026-07-03', '08:11:00', '18:09:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(209, 'Hendra Gunawan', '2026-07-03', '08:05:00', '17:21:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(210, 'Rizky Fadillah', '2026-07-03', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(211, 'Yusuf Hidayat', '2026-07-03', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(212, 'Linda Permata', '2026-07-03', '08:46:00', '18:03:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(213, 'Wahyu Nugroho', '2026-07-03', '08:39:00', '18:18:00', 'GPS', 'WFH', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(214, 'Fitri Handayani', '2026-07-03', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(215, 'Dody Kurniawan', '2026-07-03', '08:16:00', '18:44:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(216, 'Teguh Santosa', '2026-07-03', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(217, 'Budi Santoso', '2026-07-06', '08:08:00', '17:02:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(218, 'Dewi Kusuma', '2026-07-06', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(219, 'Rini Apriani', '2026-07-06', '08:09:00', '17:11:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(220, 'Eko Prasetyo', '2026-07-06', '09:12:00', '17:02:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(221, 'Hendra Gunawan', '2026-07-06', '08:42:00', '17:42:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(222, 'Rizky Fadillah', '2026-07-06', '08:33:00', '17:34:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(223, 'Yusuf Hidayat', '2026-07-06', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(224, 'Linda Permata', '2026-07-06', '07:50:00', '18:37:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(225, 'Wahyu Nugroho', '2026-07-06', '09:20:00', '17:54:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(226, 'Fitri Handayani', '2026-07-06', '08:43:00', '17:20:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(227, 'Dody Kurniawan', '2026-07-06', '08:18:00', '17:39:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(228, 'Teguh Santosa', '2026-07-06', '09:34:00', '17:58:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(229, 'Budi Santoso', '2026-07-07', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(230, 'Dewi Kusuma', '2026-07-07', '08:30:00', '18:22:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(231, 'Rini Apriani', '2026-07-07', '08:16:00', '18:28:00', 'Face ID', 'WFH', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(232, 'Eko Prasetyo', '2026-07-07', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(233, 'Hendra Gunawan', '2026-07-07', '08:52:00', '17:10:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(234, 'Rizky Fadillah', '2026-07-07', '08:32:00', '17:01:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(235, 'Yusuf Hidayat', '2026-07-07', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(236, 'Linda Permata', '2026-07-07', '07:05:00', '17:08:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(237, 'Wahyu Nugroho', '2026-07-07', '07:46:00', '18:55:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(238, 'Fitri Handayani', '2026-07-07', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(239, 'Dody Kurniawan', '2026-07-07', '08:28:00', '18:00:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(240, 'Teguh Santosa', '2026-07-07', '08:22:00', '18:47:00', 'Manual', 'WFH', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(241, 'Budi Santoso', '2026-07-08', '08:41:00', '17:50:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(242, 'Dewi Kusuma', '2026-07-08', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(243, 'Rini Apriani', '2026-07-08', '09:54:00', '18:43:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(244, 'Eko Prasetyo', '2026-07-08', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(245, 'Hendra Gunawan', '2026-07-08', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(246, 'Rizky Fadillah', '2026-07-08', '07:04:00', '18:24:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(247, 'Yusuf Hidayat', '2026-07-08', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(248, 'Linda Permata', '2026-07-08', '08:06:00', '18:30:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(249, 'Wahyu Nugroho', '2026-07-08', '08:02:00', '17:06:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(250, 'Fitri Handayani', '2026-07-08', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(251, 'Dody Kurniawan', '2026-07-08', '07:12:00', '17:10:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(252, 'Teguh Santosa', '2026-07-08', '07:36:00', '17:49:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(253, 'Budi Santoso', '2026-07-09', '07:34:00', '18:46:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(254, 'Dewi Kusuma', '2026-07-09', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(255, 'Rini Apriani', '2026-07-09', '07:01:00', '17:02:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(256, 'Eko Prasetyo', '2026-07-09', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(257, 'Hendra Gunawan', '2026-07-09', '09:19:00', '18:36:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(258, 'Rizky Fadillah', '2026-07-09', '08:26:00', '17:43:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(259, 'Yusuf Hidayat', '2026-07-09', '07:07:00', '17:29:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(260, 'Linda Permata', '2026-07-09', '07:03:00', '17:30:00', 'Face ID', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(261, 'Wahyu Nugroho', '2026-07-09', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(262, 'Fitri Handayani', '2026-07-09', '07:55:00', '17:18:00', 'Manual', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(263, 'Dody Kurniawan', '2026-07-09', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(264, 'Teguh Santosa', '2026-07-09', '07:21:00', '18:34:00', 'GPS', 'WFH', 'Hadir', '2026-07-09 03:36:25', '2026-07-09 03:36:25');

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
(1, 'PRC-001', 'Sewa Sedan', 'Regular', 3500000.00, 0.00, 3500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'PRC-002', 'Sewa Sedan', 'Silver', 3500000.00, 5.00, 3325000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'PRC-003', 'Sewa Sedan', 'Gold', 3500000.00, 10.00, 3150000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'PRC-004', 'Sewa Minibus', 'Regular', 5000000.00, 0.00, 5000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'PRC-005', 'Sewa Minibus', 'Gold', 5000000.00, 10.00, 4500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'PRC-006', 'Sewa Minibus', 'Platinum', 5000000.00, 15.00, 4250000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'PRC-007', 'Sewa Truk', 'Regular', 8000000.00, 0.00, 8000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'PRC-008', 'Sewa Truk', 'Platinum', 8000000.00, 12.00, 7040000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(9, 'PRC-009', 'Sewa Bus Besar', 'Regular', 15000000.00, 0.00, 15000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(10, 'PRC-010', 'Sewa Bus Besar', 'Gold', 15000000.00, 8.00, 13800000.00, '2026-01-01', '2026-12-31', 'Tidak Aktif', '2026-07-09 03:36:20', '2026-07-09 03:36:20');

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
(1, 'WF001', 'Persetujuan Pengadaan Barang', 'Pengajuan Barang', 'Nominal > 5.000.000', 'Kirim Email ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Workflow approval pengadaan barang.', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'WF002', 'Approval Vendor Baru', 'Penambahan Vendor Baru', NULL, 'Kirim Notifikasi ke Admin', '30 Menit', 'Aktif', 'Admin Procurement', 'Workflow untuk approval vendor.', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'WF003', 'Review Purchase Request', 'PR Diajukan', 'Qty > 100 pcs', 'Kirim ke Manajer Gudang', '2 Jam', 'Aktif', 'Manajer Gudang', 'Workflow review permintaan barang dari gudang.', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'WF004', 'Approval Kontrak Vendor', 'Kontrak Baru Dibuat', 'Nilai Kontrak > 50.000.000', 'Kirim Email ke Direktur', '1 Hari', 'Aktif', 'Legal & Finance', 'Persetujuan kontrak vendor bernilai besar.', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'WF005', 'Notifikasi Stok Menipis', 'Stok < Minimum', NULL, 'Kirim Alert ke Procurement', 'Langsung', 'Aktif', 'Procurement', 'Otomatis kirim notifikasi saat stok mendekati batas minimum.', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'WF006', 'Evaluasi Vendor Periodik', 'Akhir Bulan', 'Rating < 3', 'Kirim Laporan ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Evaluasi performa vendor setiap bulan.', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'WF007', 'Approval Pembelian Aset', 'Pengajuan Pembelian Aset', 'Nilai > 100.000.000', 'Kirim ke Komite Anggaran', '3 Hari', 'Nonaktif', 'Finance & Direktur', 'Pembelian aset besar perlu persetujuan komite.', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'WF008', 'Reminder Jatuh Tempo Kontrak', 'H-30 Kontrak Berakhir', NULL, 'Kirim Email Reminder', 'Langsung', 'Aktif', 'Procurement', 'Pengingat otomatis sebelum kontrak vendor habis.', '2026-07-09 03:36:20', '2026-07-09 03:36:20');

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
(1, 'Migrasi ERP ke Cloud', 'Budi Santoso', 'Memindahkan seluruh infrastruktur ERP dari on-premise ke cloud AWS untuk meningkatkan skalabilitas', '6 bulan', 'In Progress', 45, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'Implementasi SSO Perusahaan', 'Doni Prasetyo', 'Implementasi Single Sign-On untuk semua sistem internal menggunakan Keycloak', '3 bulan', 'Selesai', 100, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'Pengembangan Mobile App Driver', 'Andi Wijaya', 'Membuat aplikasi mobile untuk monitoring dan tracking kendaraan operasional', '4 bulan', 'In Progress', 30, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'Upgrade Infrastruktur Jaringan', 'Rudi Hermawan', 'Upgrade seluruh perangkat jaringan kantor pusat ke standar 10 Gbps', '2 bulan', 'Pending', 0, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'Implementasi WAF', 'Budi Santoso', 'Pemasangan Web Application Firewall untuk semua endpoint API publik', '1 bulan', 'Selesai', 100, '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'PRJ001', 'Pengecoran Lantai Garasi', '2026-02-10', 1, 'Selesai', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'PRJ001', 'Pemasangan Atap Baja Ringan', '2026-02-28', 1, 'Berjalan', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(3, 'PRJ001', 'Pemasangan Listrik & CCTV', '2026-03-10', 1, 'Scheduled', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(4, 'PRJ002', 'Pembayaran DP Pembelian Bus', '2026-02-20', 1, 'Selesai', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(5, 'PRJ002', 'Pengiriman Unit Bus ke Pool', '2026-04-15', 1, 'Scheduled', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'PRJ003', 'Pemasangan GPS 20 Unit Sedan', '2026-02-15', 0, 'Berjalan', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'PRJ003', 'Aktivasi Dashboard Monitoring', '2026-03-15', 1, 'Scheduled', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'PRJ005', 'Mulai Operasional Rute I', '2026-03-01', 0, 'Selesai', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
  `status` varchar(255) DEFAULT NULL,
  `disetujui_oleh` varchar(255) DEFAULT NULL,
  `tanggal_persetujuan` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `purchaseros`
--

INSERT INTO `purchaseros` (`id`, `no_pr`, `tanggal`, `departemen`, `pemohon`, `barang_jasa`, `kode_barang`, `qty`, `satuan`, `alasan_permintaan`, `status`, `disetujui_oleh`, `tanggal_persetujuan`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PR-001', '2026-04-15', 'Produksi', 'Pemohon 1', 'Spare Part', 'BRG-007', 336, 'pcs', 'Stok Habis', 'Pending', NULL, NULL, 'Catatan PR ke-1', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'PR-002', '2026-06-03', 'Gudang', 'Pemohon 2', 'ATK', 'BRG-014', 477, 'unit', 'Persediaan Menipis', 'Disetujui', 'Manajer Gudang', '2026-06-06', 'Catatan PR ke-2', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'PR-003', '2026-06-24', 'IT', 'Pemohon 3', 'Komputer', 'BRG-021', 289, 'liter', 'Permintaan Proyek', 'Ditolak', NULL, NULL, 'Catatan PR ke-3', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'PR-004', '2026-01-26', 'Finance', 'Pemohon 4', 'Bahan Bakar', 'BRG-028', 13, 'kg', 'Penggantian Rutin', 'Selesai', 'Manajer Finance', '2026-01-28', 'Catatan PR ke-4', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'PR-005', '2026-05-14', 'HR', 'Pemohon 5', 'Oli Mesin', 'BRG-035', 79, 'set', 'Kebutuhan Mendadak', 'Pending', NULL, NULL, 'Catatan PR ke-5', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'PR-006', '2026-05-28', 'Marketing', 'Pemohon 6', 'Ban Kendaraan', 'BRG-042', 264, 'dus', 'Stok Habis', 'Disetujui', 'Manajer Marketing', '2026-05-31', 'Catatan PR ke-6', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'PR-007', '2026-01-14', 'Operasional', 'Pemohon 7', 'Seragam', 'BRG-049', 397, 'rim', 'Persediaan Menipis', 'Ditolak', NULL, NULL, 'Catatan PR ke-7', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'PR-008', '2026-03-09', 'Maintenance', 'Pemohon 8', 'Alat Kebersihan', 'BRG-056', 225, 'buah', 'Permintaan Proyek', 'Selesai', 'Manajer Maintenance', '2026-03-10', 'Catatan PR ke-8', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(9, 'PR-009', '2026-03-01', 'Produksi', 'Pemohon 9', 'Mebel', 'BRG-063', 474, 'pcs', 'Penggantian Rutin', 'Pending', NULL, NULL, 'Catatan PR ke-9', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(10, 'PR-010', '2026-01-28', 'Gudang', 'Pemohon 10', 'Printer', 'BRG-070', 8, 'unit', 'Kebutuhan Mendadak', 'Disetujui', 'Manajer Gudang', '2026-01-30', 'Catatan PR ke-10', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(11, 'PR-011', '2026-02-21', 'IT', 'Pemohon 11', 'Spare Part', 'BRG-077', 155, 'liter', 'Stok Habis', 'Ditolak', NULL, NULL, 'Catatan PR ke-11', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(12, 'PR-012', '2026-07-03', 'Finance', 'Pemohon 12', 'ATK', 'BRG-084', 252, 'kg', 'Persediaan Menipis', 'Selesai', 'Manajer Finance', '2026-07-05', 'Catatan PR ke-12', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(13, 'PR-013', '2026-02-04', 'HR', 'Pemohon 13', 'Komputer', 'BRG-091', 449, 'set', 'Permintaan Proyek', 'Pending', NULL, NULL, 'Catatan PR ke-13', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(14, 'PR-014', '2026-02-15', 'Marketing', 'Pemohon 14', 'Bahan Bakar', 'BRG-098', 327, 'dus', 'Penggantian Rutin', 'Disetujui', 'Manajer Marketing', '2026-02-16', 'Catatan PR ke-14', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(15, 'PR-015', '2026-06-30', 'Operasional', 'Pemohon 15', 'Oli Mesin', 'BRG-105', 146, 'rim', 'Kebutuhan Mendadak', 'Ditolak', NULL, NULL, 'Catatan PR ke-15', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(16, 'PR-016', '2026-04-28', 'Maintenance', 'Pemohon 16', 'Ban Kendaraan', 'BRG-112', 160, 'buah', 'Stok Habis', 'Selesai', 'Manajer Maintenance', '2026-04-29', 'Catatan PR ke-16', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(17, 'PR-017', '2026-04-20', 'Produksi', 'Pemohon 17', 'Seragam', 'BRG-119', 283, 'pcs', 'Persediaan Menipis', 'Pending', NULL, NULL, 'Catatan PR ke-17', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(18, 'PR-018', '2026-07-04', 'Gudang', 'Pemohon 18', 'Alat Kebersihan', 'BRG-126', 319, 'unit', 'Permintaan Proyek', 'Disetujui', 'Manajer Gudang', '2026-07-07', 'Catatan PR ke-18', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(19, 'PR-019', '2026-04-27', 'IT', 'Pemohon 19', 'Mebel', 'BRG-133', 148, 'liter', 'Penggantian Rutin', 'Ditolak', NULL, NULL, 'Catatan PR ke-19', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(20, 'PR-020', '2026-03-20', 'Finance', 'Pemohon 20', 'Printer', 'BRG-140', 411, 'kg', 'Kebutuhan Mendadak', 'Selesai', 'Manajer Finance', '2026-03-22', 'Catatan PR ke-20', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(21, 'PR-021', '2026-03-20', 'HR', 'Pemohon 21', 'Spare Part', 'BRG-147', 232, 'set', 'Stok Habis', 'Pending', NULL, NULL, 'Catatan PR ke-21', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(22, 'PR-022', '2026-07-05', 'Marketing', 'Pemohon 22', 'ATK', 'BRG-154', 56, 'dus', 'Persediaan Menipis', 'Disetujui', 'Manajer Marketing', '2026-07-06', 'Catatan PR ke-22', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(23, 'PR-023', '2026-05-18', 'Operasional', 'Pemohon 23', 'Komputer', 'BRG-161', 263, 'rim', 'Permintaan Proyek', 'Ditolak', NULL, NULL, 'Catatan PR ke-23', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(24, 'PR-024', '2026-04-25', 'Maintenance', 'Pemohon 24', 'Bahan Bakar', 'BRG-168', 371, 'buah', 'Penggantian Rutin', 'Selesai', 'Manajer Maintenance', '2026-04-28', 'Catatan PR ke-24', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(25, 'PR-025', '2026-05-16', 'Produksi', 'Pemohon 25', 'Oli Mesin', 'BRG-175', 438, 'pcs', 'Kebutuhan Mendadak', 'Pending', NULL, NULL, 'Catatan PR ke-25', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(26, 'PR-026', '2026-04-04', 'Gudang', 'Pemohon 26', 'Ban Kendaraan', 'BRG-182', 194, 'unit', 'Stok Habis', 'Disetujui', 'Manajer Gudang', '2026-04-05', 'Catatan PR ke-26', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(27, 'PR-027', '2026-05-26', 'IT', 'Pemohon 27', 'Seragam', 'BRG-189', 483, 'liter', 'Persediaan Menipis', 'Ditolak', NULL, NULL, 'Catatan PR ke-27', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(28, 'PR-028', '2026-01-14', 'Finance', 'Pemohon 28', 'Alat Kebersihan', 'BRG-196', 88, 'kg', 'Permintaan Proyek', 'Selesai', 'Manajer Finance', '2026-01-17', 'Catatan PR ke-28', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(29, 'PR-029', '2026-05-20', 'HR', 'Pemohon 29', 'Mebel', 'BRG-203', 353, 'set', 'Penggantian Rutin', 'Pending', NULL, NULL, 'Catatan PR ke-29', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(30, 'PR-030', '2026-02-28', 'Marketing', 'Pemohon 30', 'Printer', 'BRG-210', 49, 'dus', 'Kebutuhan Mendadak', 'Disetujui', 'Manajer Marketing', '2026-03-03', 'Catatan PR ke-30', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(31, 'PR-031', '2026-02-28', 'Operasional', 'Pemohon 31', 'Spare Part', 'BRG-217', 455, 'rim', 'Stok Habis', 'Ditolak', NULL, NULL, 'Catatan PR ke-31', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(32, 'PR-032', '2026-03-11', 'Maintenance', 'Pemohon 32', 'ATK', 'BRG-224', 184, 'buah', 'Persediaan Menipis', 'Selesai', 'Manajer Maintenance', '2026-03-13', 'Catatan PR ke-32', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(33, 'PR-033', '2026-06-25', 'Produksi', 'Pemohon 33', 'Komputer', 'BRG-231', 402, 'pcs', 'Permintaan Proyek', 'Pending', NULL, NULL, 'Catatan PR ke-33', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(34, 'PR-034', '2026-01-28', 'Gudang', 'Pemohon 34', 'Bahan Bakar', 'BRG-238', 492, 'unit', 'Penggantian Rutin', 'Disetujui', 'Manajer Gudang', '2026-01-30', 'Catatan PR ke-34', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(35, 'PR-035', '2026-05-14', 'IT', 'Pemohon 35', 'Oli Mesin', 'BRG-245', 370, 'liter', 'Kebutuhan Mendadak', 'Ditolak', NULL, NULL, 'Catatan PR ke-35', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(36, 'PR-036', '2026-02-27', 'Finance', 'Pemohon 36', 'Ban Kendaraan', 'BRG-252', 126, 'kg', 'Stok Habis', 'Selesai', 'Manajer Finance', '2026-03-02', 'Catatan PR ke-36', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(37, 'PR-037', '2026-02-19', 'HR', 'Pemohon 37', 'Seragam', 'BRG-259', 137, 'set', 'Persediaan Menipis', 'Pending', NULL, NULL, 'Catatan PR ke-37', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(38, 'PR-038', '2026-03-05', 'Marketing', 'Pemohon 38', 'Alat Kebersihan', 'BRG-266', 339, 'dus', 'Permintaan Proyek', 'Disetujui', 'Manajer Marketing', '2026-03-06', 'Catatan PR ke-38', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(39, 'PR-039', '2026-04-15', 'Operasional', 'Pemohon 39', 'Mebel', 'BRG-273', 463, 'rim', 'Penggantian Rutin', 'Ditolak', NULL, NULL, 'Catatan PR ke-39', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(40, 'PR-040', '2026-01-12', 'Maintenance', 'Pemohon 40', 'Printer', 'BRG-280', 185, 'buah', 'Kebutuhan Mendadak', 'Selesai', 'Manajer Maintenance', '2026-01-13', 'Catatan PR ke-40', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(41, 'PR-041', '2026-03-06', 'Produksi', 'Pemohon 41', 'Spare Part', 'BRG-287', 84, 'pcs', 'Stok Habis', 'Pending', NULL, NULL, 'Catatan PR ke-41', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(42, 'PR-042', '2026-04-29', 'Gudang', 'Pemohon 42', 'ATK', 'BRG-294', 300, 'unit', 'Persediaan Menipis', 'Disetujui', 'Manajer Gudang', '2026-04-30', 'Catatan PR ke-42', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(43, 'PR-043', '2026-03-03', 'IT', 'Pemohon 43', 'Komputer', 'BRG-301', 228, 'liter', 'Permintaan Proyek', 'Ditolak', NULL, NULL, 'Catatan PR ke-43', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(44, 'PR-044', '2026-02-12', 'Finance', 'Pemohon 44', 'Bahan Bakar', 'BRG-308', 80, 'kg', 'Penggantian Rutin', 'Selesai', 'Manajer Finance', '2026-02-15', 'Catatan PR ke-44', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(45, 'PR-045', '2026-06-04', 'HR', 'Pemohon 45', 'Oli Mesin', 'BRG-315', 405, 'set', 'Kebutuhan Mendadak', 'Pending', NULL, NULL, 'Catatan PR ke-45', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(46, 'PR-046', '2026-05-14', 'Marketing', 'Pemohon 46', 'Ban Kendaraan', 'BRG-322', 193, 'dus', 'Stok Habis', 'Disetujui', 'Manajer Marketing', '2026-05-16', 'Catatan PR ke-46', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(47, 'PR-047', '2026-05-17', 'Operasional', 'Pemohon 47', 'Seragam', 'BRG-329', 155, 'rim', 'Persediaan Menipis', 'Ditolak', NULL, NULL, 'Catatan PR ke-47', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(48, 'PR-048', '2026-07-03', 'Maintenance', 'Pemohon 48', 'Alat Kebersihan', 'BRG-336', 235, 'buah', 'Permintaan Proyek', 'Selesai', 'Manajer Maintenance', '2026-07-06', 'Catatan PR ke-48', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(49, 'PR-049', '2026-03-18', 'Produksi', 'Pemohon 49', 'Mebel', 'BRG-343', 101, 'pcs', 'Penggantian Rutin', 'Pending', NULL, NULL, 'Catatan PR ke-49', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(50, 'PR-050', '2026-01-29', 'Gudang', 'Pemohon 50', 'Printer', 'BRG-350', 480, 'unit', 'Kebutuhan Mendadak', 'Disetujui', 'Manajer Gudang', '2026-01-31', 'Catatan PR ke-50', '2026-07-09 03:36:20', '2026-07-09 03:36:20');

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
(1, 'PO-001', '2026-04-29', 'PT Maju Jaya', 'RFQ-001', 23, 38666422, 'Pending', '2026-05-08', NULL, 'Catatan PO ke-1', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'PO-002', '2026-07-05', 'CV Berkah Abadi', 'RFQ-002', 43, 15531914, 'Approved', '2026-07-16', NULL, 'Catatan PO ke-2', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(3, 'PO-003', '2026-06-20', 'PT Sumber Makmur', 'RFQ-003', 18, 35964851, 'Closed', '2026-07-05', '2026-07-12', 'Catatan PO ke-3', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(4, 'PO-004', '2026-02-19', 'UD Sejahtera', 'RFQ-004', 12, 34889677, 'Pending', '2026-03-03', NULL, 'Catatan PO ke-4', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(5, 'PO-005', '2026-03-04', 'PT Indo Supplier', 'RFQ-005', 8, 18326876, 'Approved', '2026-03-11', NULL, 'Catatan PO ke-5', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'PO-006', '2026-04-17', 'PT Maju Jaya', 'RFQ-006', 18, 42617086, 'Closed', '2026-05-01', '2026-05-06', 'Catatan PO ke-6', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'PO-007', '2026-04-30', 'CV Berkah Abadi', 'RFQ-007', 8, 4955237, 'Pending', '2026-05-21', NULL, 'Catatan PO ke-7', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'PO-008', '2026-06-23', 'PT Sumber Makmur', 'RFQ-008', 17, 7964326, 'Approved', '2026-07-12', NULL, 'Catatan PO ke-8', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(9, 'PO-009', '2026-02-14', 'UD Sejahtera', 'RFQ-009', 7, 4846415, 'Closed', '2026-03-07', '2026-03-13', 'Catatan PO ke-9', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(10, 'PO-010', '2026-04-25', 'PT Indo Supplier', 'RFQ-010', 11, 4962750, 'Pending', '2026-05-04', NULL, 'Catatan PO ke-10', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(11, 'PO-011', '2026-06-05', 'PT Maju Jaya', 'RFQ-011', 11, 37506755, 'Approved', '2026-06-21', NULL, 'Catatan PO ke-11', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(12, 'PO-012', '2026-03-02', 'CV Berkah Abadi', 'RFQ-012', 29, 46740007, 'Closed', '2026-03-19', '2026-03-23', 'Catatan PO ke-12', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(13, 'PO-013', '2026-06-30', 'PT Sumber Makmur', 'RFQ-013', 14, 9891016, 'Pending', '2026-07-09', NULL, 'Catatan PO ke-13', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(14, 'PO-014', '2026-03-12', 'UD Sejahtera', 'RFQ-014', 24, 9971703, 'Approved', '2026-03-23', NULL, 'Catatan PO ke-14', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(15, 'PO-015', '2026-05-05', 'PT Indo Supplier', 'RFQ-015', 31, 2867832, 'Closed', '2026-05-25', '2026-05-27', 'Catatan PO ke-15', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(16, 'PO-016', '2026-04-29', 'PT Maju Jaya', 'RFQ-016', 31, 43073778, 'Pending', '2026-05-07', NULL, 'Catatan PO ke-16', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(17, 'PO-017', '2026-06-15', 'CV Berkah Abadi', 'RFQ-017', 49, 14369434, 'Approved', '2026-07-03', NULL, 'Catatan PO ke-17', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(18, 'PO-018', '2026-06-12', 'PT Sumber Makmur', 'RFQ-018', 5, 26436072, 'Closed', '2026-06-29', '2026-07-06', 'Catatan PO ke-18', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(19, 'PO-019', '2026-06-16', 'UD Sejahtera', 'RFQ-019', 7, 1784955, 'Pending', '2026-07-05', NULL, 'Catatan PO ke-19', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(20, 'PO-020', '2026-05-19', 'PT Indo Supplier', 'RFQ-020', 33, 18749993, 'Approved', '2026-06-07', NULL, 'Catatan PO ke-20', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(21, 'PO-021', '2026-06-24', 'PT Maju Jaya', 'RFQ-021', 34, 33155001, 'Closed', '2026-07-12', '2026-07-16', 'Catatan PO ke-21', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(22, 'PO-022', '2026-06-23', 'CV Berkah Abadi', 'RFQ-022', 48, 3567479, 'Pending', '2026-07-02', NULL, 'Catatan PO ke-22', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(23, 'PO-023', '2026-03-10', 'PT Sumber Makmur', 'RFQ-023', 39, 24421440, 'Approved', '2026-03-22', NULL, 'Catatan PO ke-23', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(24, 'PO-024', '2026-04-22', 'UD Sejahtera', 'RFQ-024', 8, 33448232, 'Closed', '2026-05-02', '2026-05-09', 'Catatan PO ke-24', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(25, 'PO-025', '2026-02-25', 'PT Indo Supplier', 'RFQ-025', 42, 25549955, 'Pending', '2026-03-13', NULL, 'Catatan PO ke-25', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(26, 'PO-026', '2026-04-24', 'PT Maju Jaya', 'RFQ-026', 9, 45523371, 'Approved', '2026-05-09', NULL, 'Catatan PO ke-26', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(27, 'PO-027', '2026-05-07', 'CV Berkah Abadi', 'RFQ-027', 5, 27023790, 'Closed', '2026-05-22', '2026-05-28', 'Catatan PO ke-27', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(28, 'PO-028', '2026-06-06', 'PT Sumber Makmur', 'RFQ-028', 28, 39652651, 'Pending', '2026-06-19', NULL, 'Catatan PO ke-28', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(29, 'PO-029', '2026-02-19', 'UD Sejahtera', 'RFQ-029', 1, 42831128, 'Approved', '2026-03-06', NULL, 'Catatan PO ke-29', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(30, 'PO-030', '2026-03-15', 'PT Indo Supplier', 'RFQ-030', 37, 4143762, 'Closed', '2026-03-26', '2026-04-01', 'Catatan PO ke-30', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, '2026-06-27', 'Pembayaran rental masuk', 'BANK-INV-001', 1500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, '2026-06-30', 'Transfer service kendaraan', 'BANK-INV-002', 500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(3, '2026-07-03', 'Pembayaran deposit rental', 'BANK-INV-003', 2000000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(4, '2026-07-06', 'Pembayaran sparepart', 'BANK-INV-004', 750000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(5, '2026-07-09', 'Pemasukan rental harian', 'BANK-INV-005', 1200000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
(1, 1, 1, 1, '2026-05-16 10:36:19', '2026-05-27 10:36:19', 'Perjalanan dinas ke kota 1', 2, 11, 0, 6171000, 38000, 6209000, 'tunai', 'lunas', NULL, 'Driver 1', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(2, 1, 2, 2, '2026-04-02 10:36:19', '2026-04-10 10:36:19', 'Perjalanan dinas ke kota 2', 7, 8, 0, 2136000, 478000, 2614000, 'transfer', 'dp', 1307000, NULL, '08317841160', 185000, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(3, 1, 3, 3, '2026-04-02 10:36:19', '2026-04-04 10:36:19', 'Perjalanan dinas ke kota 3', 2, 2, 0, 504000, 473000, 977000, 'tunai', 'lunas', NULL, NULL, '08588224412', NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(4, 1, 4, 4, '2026-01-30 10:36:19', '2026-02-13 10:36:19', 'Perjalanan dinas ke kota 4', 1, 14, 0, 3458000, 62000, 3520000, 'transfer', 'dp', 1760000, 'Driver 4', '08412194046', NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(5, 1, 5, 5, '2026-05-09 10:36:19', '2026-05-12 10:36:19', 'Perjalanan dinas ke kota 5', 7, 3, 0, 1113000, 303000, 1416000, 'tunai', 'lunas', NULL, 'Driver 5', NULL, 51000, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(6, 1, 6, 6, '2026-03-28 10:36:19', '2026-03-29 10:36:19', 'Perjalanan dinas ke kota 6', 1, 1, 0, 547000, 62000, 609000, 'transfer', 'dp', 304500, 'Driver 6', NULL, 159000, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(7, 1, 7, 7, '2026-01-15 10:36:19', '2026-01-23 10:36:19', 'Perjalanan dinas ke kota 7', 6, 8, 0, 2320000, 390000, 2710000, 'tunai', 'lunas', NULL, NULL, NULL, 57000, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(8, 1, 8, 8, '2026-02-14 10:36:19', '2026-02-22 10:36:19', 'Perjalanan dinas ke kota 8', 4, 8, 0, 3328000, 120000, 3448000, 'transfer', 'dp', 1724000, NULL, '08355988207', 147000, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(9, 1, 9, 9, '2026-06-10 10:36:19', '2026-06-22 10:36:19', 'Perjalanan dinas ke kota 9', 2, 12, 0, 4956000, 65000, 5021000, 'tunai', 'lunas', NULL, 'Driver 9', NULL, 79000, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(10, 1, 10, 10, '2026-04-18 10:36:19', '2026-04-23 10:36:19', 'Perjalanan dinas ke kota 10', 3, 5, 0, 1170000, 312000, 1482000, 'transfer', 'dp', 741000, NULL, '08878731034', NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(11, 1, 11, 11, '2026-02-13 10:36:19', '2026-02-23 10:36:19', 'Perjalanan dinas ke kota 11', 4, 10, 0, 3670000, 300000, 3970000, 'tunai', 'lunas', NULL, NULL, NULL, 104000, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(12, 1, 12, 12, '2026-02-07 10:36:19', '2026-02-14 10:36:19', 'Perjalanan dinas ke kota 12', 1, 7, 0, 4200000, 131000, 4331000, 'transfer', 'dp', 2165500, NULL, '08108101166', NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(13, 1, 13, 13, '2026-01-22 10:36:19', '2026-01-31 10:36:19', 'Perjalanan dinas ke kota 13', 4, 9, 0, 2412000, 498000, 2910000, 'tunai', 'lunas', NULL, 'Driver 13', '08365513330', NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(14, 1, 14, 14, '2026-01-18 10:36:19', '2026-01-22 10:36:19', 'Perjalanan dinas ke kota 14', 0, 4, 0, 1056000, 132000, 1188000, 'transfer', 'dp', 594000, 'Driver 14', NULL, 163000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(15, 1, 15, 15, '2026-01-26 10:36:19', '2026-02-09 10:36:19', 'Perjalanan dinas ke kota 15', 7, 14, 0, 3150000, 344000, 3494000, 'tunai', 'lunas', NULL, 'Driver 15', NULL, 87000, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(16, 1, 16, 16, '2026-01-23 10:36:19', '2026-02-04 10:36:19', 'Perjalanan dinas ke kota 16', 4, 12, 0, 4392000, 477000, 4869000, 'transfer', 'dp', 2434500, 'Driver 16', '08150604531', 134000, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(17, 1, 17, 17, '2026-06-04 10:36:19', '2026-06-15 10:36:19', 'Perjalanan dinas ke kota 17', 6, 11, 0, 2860000, 11000, 2871000, 'tunai', 'lunas', NULL, NULL, '08987823116', NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(18, 1, 18, 18, '2026-02-12 10:36:19', '2026-02-17 10:36:19', 'Perjalanan dinas ke kota 18', 1, 5, 0, 2255000, 195000, 2450000, 'transfer', 'dp', 1225000, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(19, 1, 19, 19, '2026-06-22 10:36:19', '2026-06-26 10:36:19', 'Perjalanan dinas ke kota 19', 0, 4, 0, 1888000, 331000, 2219000, 'tunai', 'lunas', NULL, 'Driver 19', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(20, 1, 20, 20, '2026-04-09 10:36:19', '2026-04-16 10:36:19', 'Perjalanan dinas ke kota 20', 2, 7, 0, 1435000, 372000, 1807000, 'transfer', 'dp', 903500, 'Driver 20', '08157066325', NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(21, 1, 21, 21, '2026-03-22 10:36:19', '2026-04-03 10:36:19', 'Perjalanan dinas ke kota 21', 8, 12, 0, 6960000, 159000, 7119000, 'tunai', 'lunas', NULL, 'Driver 21', NULL, 180000, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(22, 1, 22, 22, '2026-04-08 10:36:19', '2026-04-15 10:36:19', 'Perjalanan dinas ke kota 22', 8, 7, 0, 1575000, 71000, 1646000, 'transfer', 'dp', 823000, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(23, 1, 23, 23, '2026-06-24 10:36:19', '2026-06-27 10:36:19', 'Perjalanan dinas ke kota 23', 1, 3, 0, 1143000, 320000, 1463000, 'tunai', 'lunas', NULL, NULL, '08651782302', NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(24, 1, 24, 24, '2026-05-19 10:36:19', '2026-05-25 10:36:19', 'Perjalanan dinas ke kota 24', 7, 6, 0, 3180000, 98000, 3278000, 'transfer', 'dp', 1639000, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(25, 1, 25, 25, '2026-05-28 10:36:19', '2026-06-06 10:36:19', 'Perjalanan dinas ke kota 25', 8, 9, 0, 4815000, 490000, 5305000, 'tunai', 'lunas', NULL, NULL, NULL, 161000, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(26, 1, 26, 26, '2026-03-23 10:36:19', '2026-04-01 10:36:19', 'Perjalanan dinas ke kota 26', 1, 9, 0, 2664000, 459000, 3123000, 'transfer', 'dp', 1561500, NULL, NULL, 178000, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(27, 1, 27, 27, '2026-05-06 10:36:19', '2026-05-12 10:36:19', 'Perjalanan dinas ke kota 27', 8, 6, 0, 2646000, 91000, 2737000, 'tunai', 'lunas', NULL, NULL, '08640634124', 84000, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(28, 1, 28, 28, '2026-04-28 10:36:19', '2026-04-29 10:36:19', 'Perjalanan dinas ke kota 28', 7, 1, 0, 316000, 134000, 450000, 'transfer', 'dp', 225000, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(29, 1, 29, 29, '2026-06-23 10:36:19', '2026-07-05 10:36:19', 'Perjalanan dinas ke kota 29', 3, 12, 0, 5724000, 391000, 6115000, 'tunai', 'lunas', NULL, NULL, '08426274958', 129000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(30, 1, 30, 30, '2026-02-09 10:36:19', '2026-02-13 10:36:19', 'Perjalanan dinas ke kota 30', 2, 4, 0, 2164000, 321000, 2485000, 'transfer', 'dp', 1242500, 'Driver 30', '08822963858', 50000, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(31, 1, 31, 31, '2026-06-17 10:36:19', '2026-06-29 10:36:19', 'Perjalanan dinas ke kota 31', 4, 12, 0, 6516000, 288000, 6804000, 'tunai', 'lunas', NULL, NULL, '08145596886', NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(32, 1, 32, 32, '2026-06-27 10:36:19', '2026-07-05 10:36:19', 'Perjalanan dinas ke kota 32', 4, 8, 0, 2344000, 407000, 2751000, 'transfer', 'dp', 1375500, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(33, 1, 33, 33, '2026-06-09 10:36:19', '2026-06-19 10:36:19', 'Perjalanan dinas ke kota 33', 3, 10, 0, 3270000, 85000, 3355000, 'tunai', 'lunas', NULL, 'Driver 33', '08181742183', NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(34, 1, 34, 34, '2026-01-23 10:36:19', '2026-01-31 10:36:19', 'Perjalanan dinas ke kota 34', 3, 8, 0, 1872000, 133000, 2005000, 'transfer', 'dp', 1002500, 'Driver 34', '08785121876', 163000, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(35, 1, 35, 35, '2026-06-04 10:36:19', '2026-06-16 10:36:19', 'Perjalanan dinas ke kota 35', 3, 12, 0, 3480000, 274000, 3754000, 'tunai', 'lunas', NULL, NULL, '08693017076', NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(36, 1, 36, 36, '2026-06-14 10:36:19', '2026-06-17 10:36:19', 'Perjalanan dinas ke kota 36', 6, 3, 0, 792000, 350000, 1142000, 'transfer', 'dp', 571000, 'Driver 36', '08856930973', 99000, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(37, 1, 37, 37, '2026-05-04 10:36:19', '2026-05-06 10:36:19', 'Perjalanan dinas ke kota 37', 2, 2, 0, 686000, 429000, 1115000, 'tunai', 'lunas', NULL, NULL, NULL, 184000, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(38, 1, 38, 38, '2026-06-21 10:36:19', '2026-07-01 10:36:19', 'Perjalanan dinas ke kota 38', 0, 10, 0, 4430000, 136000, 4566000, 'transfer', 'dp', 2283000, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(39, 1, 39, 39, '2026-06-21 10:36:19', '2026-07-04 10:36:19', 'Perjalanan dinas ke kota 39', 1, 13, 0, 3874000, 85000, 3959000, 'tunai', 'lunas', NULL, 'Driver 39', NULL, NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(40, 1, 40, 40, '2026-03-27 10:36:19', '2026-03-31 10:36:19', 'Perjalanan dinas ke kota 40', 4, 4, 0, 976000, 282000, 1258000, 'transfer', 'dp', 629000, NULL, NULL, 146000, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(41, 1, 41, 41, '2026-02-28 10:36:19', '2026-03-08 10:36:19', 'Perjalanan dinas ke kota 41', 0, 8, 0, 2536000, 209000, 2745000, 'tunai', 'lunas', NULL, NULL, '08162745952', NULL, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(42, 1, 42, 42, '2026-01-11 10:36:19', '2026-01-14 10:36:19', 'Perjalanan dinas ke kota 42', 8, 3, 0, 801000, 216000, 1017000, 'transfer', 'dp', 508500, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(43, 1, 43, 43, '2026-03-23 10:36:19', '2026-03-25 10:36:19', 'Perjalanan dinas ke kota 43', 3, 2, 0, 996000, 7000, 1003000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(44, 1, 44, 44, '2026-03-15 10:36:19', '2026-03-27 10:36:19', 'Perjalanan dinas ke kota 44', 7, 12, 0, 3672000, 41000, 3713000, 'transfer', 'dp', 1856500, 'Driver 44', '08195341751', 68000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(45, 1, 45, 45, '2026-03-02 10:36:19', '2026-03-04 10:36:19', 'Perjalanan dinas ke kota 45', 7, 2, 0, 498000, 30000, 528000, 'tunai', 'lunas', NULL, NULL, '08780576988', NULL, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(46, 1, 46, 46, '2026-03-01 10:36:19', '2026-03-08 10:36:19', 'Perjalanan dinas ke kota 46', 4, 7, 0, 1400000, 365000, 1765000, 'transfer', 'dp', 882500, NULL, NULL, 97000, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(47, 1, 47, 47, '2026-04-29 10:36:19', '2026-05-02 10:36:19', 'Perjalanan dinas ke kota 47', 6, 3, 0, 966000, 365000, 1331000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(48, 1, 48, 48, '2026-07-02 10:36:19', '2026-07-13 10:36:19', 'Perjalanan dinas ke kota 48', 1, 11, 0, 5093000, 438000, 5531000, 'transfer', 'dp', 2765500, NULL, NULL, 171000, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(49, 1, 49, 49, '2026-04-12 10:36:19', '2026-04-24 10:36:19', 'Perjalanan dinas ke kota 49', 1, 12, 0, 4008000, 37000, 4045000, 'tunai', 'lunas', NULL, NULL, '08446888668', 195000, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL),
(50, 1, 50, 50, '2026-01-19 10:36:19', '2026-01-23 10:36:19', 'Perjalanan dinas ke kota 50', 2, 4, 0, 1428000, 7000, 1435000, 'transfer', 'dp', 717500, 'Driver 50', NULL, 169000, NULL, NULL, NULL, 'dp', 'batal', '2026-07-09 03:36:19', '2026-07-09 03:36:19', 0, NULL, NULL, NULL, NULL);

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
(1, 'RFQ-001', '2026-05-08', 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 145, 'pcs', 1348944, '2026-05-28', 'Open', 'Catatan RFQ ke-1', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'RFQ-002', '2026-02-27', 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 481, 'liter', 1590717, '2026-03-16', 'Sent', 'Catatan RFQ ke-2', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(3, 'RFQ-003', '2026-01-15', 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 24, 'unit', 1547258, '2026-02-12', 'Closed', 'Catatan RFQ ke-3', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(4, 'RFQ-004', '2026-03-17', 'UD Sejahtera', 'BRG-004', 'Filter Udara', 58, 'set', 1676514, '2026-03-26', 'Open', 'Catatan RFQ ke-4', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(5, 'RFQ-005', '2026-01-22', 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 198, 'buah', 423514, '2026-02-18', 'Sent', 'Catatan RFQ ke-5', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'RFQ-006', '2026-05-31', 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 39, 'dus', 830456, '2026-06-24', 'Closed', 'Catatan RFQ ke-6', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'RFQ-007', '2026-03-04', 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 30, 'kg', 1319645, '2026-03-16', 'Open', 'Catatan RFQ ke-7', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'RFQ-008', '2026-06-23', 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 239, 'pcs', 1549192, '2026-07-21', 'Sent', 'Catatan RFQ ke-8', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(9, 'RFQ-009', '2026-02-08', 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 131, 'liter', 1277858, '2026-03-06', 'Closed', 'Catatan RFQ ke-9', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(10, 'RFQ-010', '2026-05-11', 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 378, 'unit', 1655999, '2026-06-05', 'Open', 'Catatan RFQ ke-10', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(11, 'RFQ-011', '2026-05-17', 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 425, 'set', 1369689, '2026-06-01', 'Sent', 'Catatan RFQ ke-11', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(12, 'RFQ-012', '2026-04-18', 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 177, 'buah', 1668507, '2026-05-03', 'Closed', 'Catatan RFQ ke-12', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(13, 'RFQ-013', '2026-01-27', 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 466, 'dus', 179713, '2026-02-24', 'Open', 'Catatan RFQ ke-13', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(14, 'RFQ-014', '2026-06-04', 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 421, 'kg', 145202, '2026-06-27', 'Sent', 'Catatan RFQ ke-14', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(15, 'RFQ-015', '2026-05-23', 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 371, 'pcs', 1216876, '2026-06-09', 'Closed', 'Catatan RFQ ke-15', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(16, 'RFQ-016', '2026-06-08', 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 416, 'liter', 614972, '2026-06-29', 'Open', 'Catatan RFQ ke-16', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(17, 'RFQ-017', '2026-03-20', 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 239, 'unit', 1147982, '2026-04-19', 'Sent', 'Catatan RFQ ke-17', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(18, 'RFQ-018', '2026-04-22', 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 135, 'set', 1650393, '2026-05-18', 'Closed', 'Catatan RFQ ke-18', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(19, 'RFQ-019', '2026-03-30', 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 388, 'buah', 1775887, '2026-04-21', 'Open', 'Catatan RFQ ke-19', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(20, 'RFQ-020', '2026-05-26', 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 66, 'dus', 1322607, '2026-06-23', 'Sent', 'Catatan RFQ ke-20', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(21, 'RFQ-021', '2026-06-29', 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 287, 'kg', 320790, '2026-07-22', 'Closed', 'Catatan RFQ ke-21', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(22, 'RFQ-022', '2026-01-24', 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 484, 'pcs', 1530921, '2026-02-09', 'Open', 'Catatan RFQ ke-22', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(23, 'RFQ-023', '2026-05-13', 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 241, 'liter', 117840, '2026-05-26', 'Sent', 'Catatan RFQ ke-23', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(24, 'RFQ-024', '2026-02-10', 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 240, 'unit', 720854, '2026-02-28', 'Closed', 'Catatan RFQ ke-24', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(25, 'RFQ-025', '2026-06-26', 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 306, 'set', 207074, '2026-07-23', 'Open', 'Catatan RFQ ke-25', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(26, 'RFQ-026', '2026-06-07', 'PT Maju Jaya', 'BRG-002', 'Oli Mesin 10W-40', 112, 'buah', 1795297, '2026-07-02', 'Sent', 'Catatan RFQ ke-26', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(27, 'RFQ-027', '2026-04-14', 'CV Berkah Abadi', 'BRG-003', 'Ban Kendaraan', 435, 'dus', 278685, '2026-05-02', 'Closed', 'Catatan RFQ ke-27', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(28, 'RFQ-028', '2026-04-19', 'PT Sumber Makmur', 'BRG-004', 'Filter Udara', 90, 'kg', 1118313, '2026-04-29', 'Open', 'Catatan RFQ ke-28', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(29, 'RFQ-029', '2026-04-18', 'UD Sejahtera', 'BRG-005', 'Aki Kendaraan', 240, 'pcs', 1731465, '2026-05-17', 'Sent', 'Catatan RFQ ke-29', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(30, 'RFQ-030', '2026-03-22', 'PT Indo Supplier', 'BRG-006', 'Kampas Rem', 228, 'liter', 1488599, '2026-04-15', 'Closed', 'Catatan RFQ ke-30', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'Ahmad Rifai', 'Staff Gudang', '2024-02-28', 'Mengundurkan diri', 'Selesai', 'Sudah', 'Sudah menyelesaikan serah terima aset dan dokumen', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(2, 'Maya Sari', 'Staff Marketing', '2024-04-15', 'Pindah domisili', 'Selesai', 'Sudah', 'Proses offboarding berjalan lancar', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(3, 'Dika Pratama', 'Developer Junior', '2024-06-01', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Akses sistem telah dicabut', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(4, 'Sari Utami', 'Staff Finance', '2024-07-31', 'Melanjutkan studi', 'Selesai', 'Sudah', 'Dokumen exit interview selesai', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(5, 'Bowo Setiawan', 'Teknisi Lapangan', '2024-09-30', 'Kesehatan', 'Selesai', 'Sudah', 'Serah terima peralatan sudah dilakukan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(6, 'Nita Lestari', 'Admin HR', '2024-11-15', 'Menikah dan pindah kota', 'Selesai', 'Sudah', 'Semua kewajiban telah diselesaikan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(7, 'Reza Aditya', 'IT Support', '2025-01-31', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Credential akun sudah dinonaktifkan', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(8, 'Putri Anggraini', 'Staff Operasional', '2025-03-15', 'Alasan keluarga', 'Proses', 'Belum', 'Sedang dalam proses serah terima', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(9, 'Galih Santoso', 'Supervisor Produksi', '2025-05-30', 'Pensiun dini', 'Proses', 'Belum', 'Menunggu pengganti untuk serah terima', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(10, 'Lina Permatasari', 'Staff Akuntansi', '2025-06-30', 'Wirausaha', 'Proses', 'Belum', 'Dalam proses dokumentasi offboarding', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(11, 'Bagas Wicaksono', 'Driver', '2025-07-01', 'Kontrak tidak diperpanjang', 'Proses', 'Belum', 'Mengembalikan kendaraan dinas', '2026-07-09 03:36:26', '2026-07-09 03:36:26'),
(12, 'Rina Kurniawati', 'Customer Service', '2026-01-31', 'Mengurus anak', 'Proses', 'Belum', 'Exit interview sudah dilakukan', '2026-07-09 03:36:26', '2026-07-09 03:36:26');

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
(1, 'RTR-001', '2026-02-10', 'SO-2026-001', 'PT Maju Bersama', 'Sewa Minibus', 1, 'Unit mengalami kerusakan', 5000000.00, 'Selesai', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'RTR-002', '2026-02-20', 'SO-2026-002', 'PT Global Trans', 'Sewa Bus', 1, 'Spesifikasi tidak sesuai', 15000000.00, 'Diproses', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(3, 'RTR-003', '2026-03-05', 'SO-2026-003', 'CV Karya Indah', 'Sewa Truk', 1, 'Truk bermasalah di tengah jalan', 8000000.00, 'Selesai', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(4, 'RTR-004', '2026-03-18', 'SO-2026-004', 'PT Nusantara Raya', 'Sewa Minibus', 1, 'AC tidak berfungsi', 5500000.00, 'Menunggu', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(5, 'RTR-005', '2026-04-02', 'SO-2026-006', 'PT Berlian Trans', 'Sewa Bus', 1, 'Pembatalan sebagian order', 10000000.00, 'Selesai', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'RTR-006', '2026-04-15', 'SO-2026-005', 'CV Jaya Mandiri', 'Sewa MPV', 2, 'Unit terlambat pengiriman', 8000000.00, 'Diproses', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'RTR-007', '2026-05-01', 'SO-2026-008', 'PT Sejahtera Abadi', 'Sewa Sedan', 1, 'Kendaraan tidak sesuai pesanan', 3500000.00, 'Menunggu', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'RTR-008', '2026-05-15', 'SO-2026-009', 'PT Prima Raya', 'Sewa SUV', 1, 'Kondisi kendaraan buruk', 6000000.00, 'Selesai', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'SO-2026-001', '2026-01-20', 'PT Maju Bersama', 'Sewa Minibus 2 Unit', 2, 10000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'SO-2026-002', '2026-02-05', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'SO-2026-003', '2026-02-18', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 'Diproses', 'Tempo', 'Cici', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'SO-2026-004', '2026-03-01', 'PT Nusantara Raya', 'Sewa Minibus', 2, 11000000.00, 'Selesai', 'Kredit', 'Dani', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'SO-2026-005', '2026-03-10', 'CV Jaya Mandiri', 'Sewa MPV 4 Unit', 4, 16000000.00, 'Diproses', 'Transfer Bank', 'Andi', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'SO-2026-006', '2026-03-25', 'PT Berlian Trans', 'Sewa Bus Medium 2 Unit', 2, 20000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'SO-2026-007', '2026-04-05', 'CV Mitra Logistik', 'Sewa Truk 3 Unit', 3, 22500000.00, 'Dibatalkan', 'Transfer Bank', 'Cici', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'SO-2026-008', '2026-04-20', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 10500000.00, 'Diproses', 'Cash', 'Dani', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(9, 'SO-2026-009', '2026-05-08', 'PT Prima Raya', 'Sewa SUV', 2, 12000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(10, 'SO-2026-010', '2026-05-20', 'PT Sinar Harapan', 'Sewa Minibus', 1, 5500000.00, 'Diproses', 'Tempo', 'Budi', '2026-07-09 03:36:20', '2026-07-09 03:36:20');

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
(1, 'SEG001', 'Corporate Client', 'Perusahaan dengan kontrak bulanan', 15, 'Retain & Upsell', 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'SEG002', 'Individual Frequent', 'Individu rental >3x dalam 6 bulan', 48, 'Loyalty Program', 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'web-server-01', 'Cloud', 'AWS ap-southeast-1', 'Ubuntu 22.04 LTS', 'AWS', 'Aktif', 1, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'db-server-01', 'Cloud', 'AWS ap-southeast-1', 'Amazon Linux 2', 'AWS', 'Aktif', 1, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'file-server-local', 'Physical', 'Data Center Cibitung', 'Windows Server 2022', NULL, 'Aktif', 1, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'dev-server-01', 'VPS', 'Niagahoster VPS', 'Ubuntu 20.04 LTS', 'Niagahoster', 'Aktif', 0, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'backup-server-01', 'Physical', 'Ruang Server Jakarta', 'CentOS 7', NULL, 'Maintenance', 0, '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 1, 'Ganti Oli Mesin', 350000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(2, 1, 'Tune Up', 500000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(3, 1, 'Spooring Balancing', 250000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(4, 1, 'Ganti Kampas Rem', 450000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(5, 1, 'Service AC', 600000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(6, 1, 'Ganti Ban', 900000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(7, 1, 'Overhaul Mesin', 4500000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(8, 1, 'Cuci Mobil Premium', 75000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(9, 1, 'Ganti Aki', 1200000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(10, 1, 'Perbaikan Suspensi', 1800000, '2026-07-09 03:36:18', '2026-07-09 03:36:18');

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
(1, 1, '2025-09-26', 43157, 'Layak', 300000, 'Ganti oli mesin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(2, 2, '2025-08-29', 6944, 'Layak', 500000, 'Tune up', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(3, 3, '2025-09-19', 94624, 'Layak', 100000, 'Ganti kampas rem', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(4, 4, '2026-02-10', 8568, 'Tidak Layak', 900000, 'Servis AC', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(5, 5, '2025-09-13', 72574, 'Layak', 400000, 'Ganti ban', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(6, 6, '2025-12-14', 40257, 'Layak', 1350000, 'Overhaul mesin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(7, 7, '2026-03-25', 115454, 'Layak', 850000, 'Ganti aki', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(8, 8, '2025-08-20', 59482, 'Tidak Layak', 100000, 'Servis transmisi', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(9, 9, '2026-01-20', 48778, 'Layak', 1250000, 'Ganti filter udara', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(10, 10, '2026-05-18', 14907, 'Layak', 1350000, 'Perbaikan body', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(11, 11, '2026-04-06', 8431, 'Layak', 350000, 'Ganti busi', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(12, 12, '2026-02-07', 5718, 'Tidak Layak', 950000, 'Servis suspensi', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(13, 13, '2026-01-23', 31455, 'Layak', 200000, 'Ganti timing belt', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(14, 14, '2026-06-13', 110186, 'Layak', 200000, 'Kalibrasi lampu', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(15, 15, '2025-09-17', 56061, 'Layak', 800000, 'Servis power steering', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(16, 16, '2026-01-17', 94259, 'Tidak Layak', 950000, 'Ganti knalpot', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(17, 17, '2026-01-06', 116121, 'Layak', 100000, 'Perbaikan sistem pendingin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(18, 18, '2026-02-17', 7061, 'Layak', 150000, 'Ganti kopling', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(19, 19, '2025-08-21', 110516, 'Layak', 1350000, 'Servis rem tangan', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(20, 20, '2026-03-15', 39299, 'Tidak Layak', 100000, 'Ganti wiper', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(21, 21, '2026-04-16', 64038, 'Layak', 800000, 'Ganti oli mesin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(22, 22, '2025-12-05', 54733, 'Layak', 500000, 'Tune up', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(23, 23, '2025-07-24', 99800, 'Layak', 850000, 'Ganti kampas rem', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(24, 24, '2026-06-11', 61915, 'Tidak Layak', 200000, 'Servis AC', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(25, 25, '2026-04-18', 109530, 'Layak', 500000, 'Ganti ban', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(26, 26, '2025-08-26', 26540, 'Layak', 950000, 'Overhaul mesin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(27, 27, '2026-01-01', 43325, 'Layak', 300000, 'Ganti aki', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(28, 28, '2026-04-12', 68880, 'Tidak Layak', 700000, 'Servis transmisi', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(29, 29, '2026-01-01', 88165, 'Layak', 1000000, 'Ganti filter udara', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(30, 30, '2025-11-04', 22532, 'Layak', 450000, 'Perbaikan body', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(31, 31, '2025-10-09', 85429, 'Layak', 800000, 'Ganti busi', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(32, 32, '2026-05-20', 112433, 'Tidak Layak', 400000, 'Servis suspensi', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(33, 33, '2026-02-06', 60096, 'Layak', 550000, 'Ganti timing belt', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(34, 34, '2025-09-08', 83480, 'Layak', 1450000, 'Kalibrasi lampu', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(35, 35, '2026-01-28', 19241, 'Layak', 500000, 'Servis power steering', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(36, 36, '2025-10-07', 115824, 'Tidak Layak', 350000, 'Ganti knalpot', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(37, 37, '2025-11-19', 102098, 'Layak', 1500000, 'Perbaikan sistem pendingin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(38, 38, '2025-08-28', 105077, 'Layak', 1100000, 'Ganti kopling', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(39, 39, '2025-12-08', 95364, 'Layak', 550000, 'Servis rem tangan', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(40, 40, '2025-08-18', 28914, 'Tidak Layak', 150000, 'Ganti wiper', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(41, 41, '2026-04-08', 34232, 'Layak', 1350000, 'Ganti oli mesin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(42, 42, '2025-07-28', 63223, 'Layak', 1300000, 'Tune up', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(43, 43, '2026-04-18', 62425, 'Layak', 350000, 'Ganti kampas rem', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(44, 44, '2026-07-02', 65079, 'Tidak Layak', 500000, 'Servis AC', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(45, 45, '2026-03-15', 119981, 'Layak', 1400000, 'Ganti ban', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(46, 46, '2026-03-09', 11770, 'Layak', 400000, 'Overhaul mesin', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(47, 47, '2026-03-06', 36904, 'Layak', 850000, 'Ganti aki', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(48, 48, '2026-02-25', 22423, 'Tidak Layak', 150000, 'Servis transmisi', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(49, 49, '2025-09-08', 91101, 'Layak', 150000, 'Ganti filter udara', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(50, 50, '2025-08-18', 9491, 'Layak', 1150000, 'Perbaikan body', NULL, '2026-07-09 03:36:18', '2026-07-09 03:36:18');

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
(1, 1, 'Oli mesin sudah hitam dan rem berbunyi', 45000, 850000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-19', '2026-07-09 03:36:18', '2026-07-09 03:36:18', NULL),
(2, 2, 'AC tidak dingin', 52000, 600000, 'proses', NULL, 0, 0, 'stabil', '2026-06-29', '2026-07-09 03:36:18', '2026-07-09 03:36:18', NULL),
(3, 3, 'Ban depan aus', 70000, 1800000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-09', '2026-07-09 03:36:18', '2026-07-09 03:36:18', NULL),
(4, 1, 'Mesin getar saat idle', 47000, 500000, 'proses', NULL, 0, 0, 'stabil', '2026-07-04', '2026-07-09 03:36:18', '2026-07-09 03:36:18', NULL),
(5, 2, 'Ganti aki', 55000, 1200000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-24', '2026-07-09 03:36:18', '2026-07-09 03:36:18', NULL);

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
(1, 'PT Rental Kendaraan Indonesia', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-12345678', 'info@rentalkendaraan.co.id', 'https://rentalkendaraan.co.id', 'uploads/setting/1783568277_logo_icon.png', 'Bank BCA', '1234567890', 'PT Rental Kendaraan Indonesia', '10110', 1, 'bulan', '2026-07-09 03:36:20', '2026-07-09 03:37:57');

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
(1, 'Teguh Santosa', 'Pagi', '07:00:00', '15:00:00', '2', '10', 'Lembur pengiriman barang', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(2, 'Arif Budiman', 'Pagi', '07:00:00', '15:00:00', NULL, '8', 'Shift reguler', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(3, 'Dody Kurniawan', 'Siang', '15:00:00', '23:00:00', '1', '9', 'Lembur rapat koordinasi', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(4, 'Rizky Fadillah', 'Pagi', '08:00:00', '17:00:00', '3', '12', 'Lembur deploy sistem', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(5, 'Yusuf Hidayat', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler IT', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(6, 'Hendra Gunawan', 'Pagi', '08:00:00', '17:00:00', '2', '11', 'Lembur maintenance server', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(7, 'Wahyu Nugroho', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(8, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '1.5', '9.5', 'Lembur laporan pajak', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(9, 'Linda Permata', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur audit internal', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(10, 'Rini Apriani', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler HRD', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(11, 'Eko Prasetyo', 'Malam', '23:00:00', '07:00:00', '1', '9', 'Shift malam + lembur', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(12, 'Dewi Kusuma', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(13, 'Teguh Santosa', 'Malam', '23:00:00', '07:00:00', '2', '10', 'Lembur pengawasan malam', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(14, 'Arif Budiman', 'Siang', '15:00:00', '23:00:00', NULL, '8', 'Rotasi shift siang', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(15, 'Rizky Fadillah', 'Siang', '12:00:00', '21:00:00', '2', '11', 'Lembur perbaikan bug produksi', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(16, 'Yusuf Hidayat', 'Malam', '23:00:00', '07:00:00', NULL, '8', 'Shift malam on-call', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(17, 'Wahyu Nugroho', 'Pagi', '07:30:00', '16:30:00', '1', '9', 'Lembur tutup buku', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(18, 'Dody Kurniawan', 'Pagi', '07:00:00', '16:00:00', NULL, '8', 'Shift reguler operasional', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(19, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur SPT tahunan', '2026-07-09 03:36:25', '2026-07-09 03:36:25'),
(20, 'Hendra Gunawan', 'Siang', '12:00:00', '21:00:00', '1', '9', 'Lembur migrasi data', '2026-07-09 03:36:25', '2026-07-09 03:36:25');

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
(1, 'DOC-2026-001', 'Kontrak', '2026-01-20', 'PT Maju Bersama & PT APY Rent', 'Ditandatangani', 'PrivyID', 'Kontrak sewa 3 bulan', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'DOC-2026-002', 'Perjanjian', '2026-02-01', 'CV Karya Indah & PT APY Rent', 'Ditandatangani', 'DocuSign', 'PKS layanan transportasi', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(3, 'DOC-2026-003', 'MOU', '2026-02-15', 'PT Global Trans & PT APY Rent', 'Menunggu', 'PrivyID', 'Menunggu tanda tangan direktur', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(4, 'DOC-2026-004', 'Penawaran', '2026-03-01', 'PT Nusantara Raya & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Penawaran disetujui', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(5, 'DOC-2026-005', 'Kontrak', '2026-03-15', 'CV Jaya Mandiri & PT APY Rent', 'Ditolak', 'PrivyID', 'Ditolak karena klausul tidak sesuai', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(6, 'DOC-2026-006', 'Perjanjian', '2026-04-01', 'PT Berlian Trans & PT APY Rent', 'Ditandatangani', 'Peruri', 'PKS jangka panjang', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(7, 'DOC-2026-007', 'Kontrak', '2026-04-20', 'PT Prima Raya & PT APY Rent', 'Menunggu', 'DocuSign', 'Dalam proses review', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(8, 'DOC-2026-008', 'MOU', '2026-05-05', 'PT Sejahtera Abadi & PT APY Rent', 'Ditandatangani', 'Manual', 'Ditandatangani secara fisik', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(9, 'DOC-2026-009', 'Lainnya', '2026-05-20', 'CV Mitra Logistik & PT APY Rent', 'Menunggu', 'PrivyID', 'Surat kuasa armada', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(10, 'DOC-2026-010', 'Kontrak', '2026-06-01', 'PT Sinar Harapan & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Kontrak perpanjangan', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'Rizky Fadillah', 'Laravel', 3, 'Y', 'Hendra Gunawan', '2026-05-14', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(2, 'Yusuf Hidayat', 'Vue.js', 1, 'T', 'Hendra Gunawan', '2025-10-05', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(3, 'Hendra Gunawan', 'MySQL', 2, 'T', 'Hendra Gunawan', '2025-12-02', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(4, 'Wahyu Nugroho', 'PHP', 3, 'Y', 'Hendra Gunawan', '2026-01-17', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(5, 'Fitri Handayani', 'Microsoft Excel', 2, 'T', 'Linda Permata', '2026-03-24', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(6, 'Linda Permata', 'Akuntansi', 4, 'T', 'Linda Permata', '2025-09-25', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(7, 'Rini Apriani', 'Perpajakan', 1, 'Y', 'Linda Permata', '2025-08-08', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(8, 'Eko Prasetyo', 'SAP', 4, 'T', 'Linda Permata', '2026-05-20', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(9, 'Dewi Kusuma', 'Rekrutmen', 1, 'T', 'Dewi Kusuma', '2025-10-09', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(10, 'Teguh Santosa', 'Payroll', 3, 'Y', 'Dewi Kusuma', '2026-04-03', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(11, 'Arif Budiman', 'K3', 2, 'T', 'Dewi Kusuma', '2026-03-15', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(12, 'Dody Kurniawan', 'Negosiasi', 3, 'T', 'Dody Kurniawan', '2025-10-16', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(13, 'Rizky Fadillah', 'AutoCAD', 3, 'Y', 'Teguh Santosa', '2026-03-17', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(14, 'Yusuf Hidayat', 'Troubleshooting', 2, 'T', 'Yusuf Hidayat', '2026-02-25', '2026-07-09 03:36:24', '2026-07-09 03:36:24');

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
(1, 'Microsoft Office 365', 'Subscription', 25, 'Microsoft', '2025-12-31', 'Aktif', '2024-12-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'Adobe Creative Cloud', 'Subscription', 5, 'Adobe', '2025-06-30', 'Aktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'Kaspersky Endpoint Security', 'Perpetual', 50, 'Kaspersky', '2024-03-31', 'Expired', '2024-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'Zoom Pro', 'Subscription', 10, 'Zoom', '2025-09-30', 'Aktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'AutoCAD 2024', 'Perpetual', 3, 'Autodesk', '2026-01-01', 'Aktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'SMP001', 'Instagram', 'instagram', 'promo_rental_july', 320, 18, 1500000.00, 9000000.00, 500.00, '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'SMP002', 'Facebook', 'facebook', 'awareness_apyrent', 580, 25, 2000000.00, 12500000.00, 525.00, '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'Direktur Utama', 'Budi Santoso', 'NIP-001', 'Direksi', NULL, 'Jakarta', 'Tetap', '2018-01-02', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(2, 'Direktur Operasional', 'Siti Rahayu', 'NIP-002', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(3, 'Direktur Keuangan', 'Agus Wibowo', 'NIP-003', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(4, 'Manager HRD', 'Dewi Kusuma', 'NIP-010', 'HRD', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-01-15', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(5, 'Staff HRD', 'Rini Apriani', 'NIP-011', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Tetap', '2021-04-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(6, 'Staff HRD', 'Eko Prasetyo', 'NIP-012', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Kontrak', '2023-07-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(7, 'Manager IT', 'Hendra Gunawan', 'NIP-020', 'IT', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-02-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(8, 'Developer', 'Rizky Fadillah', 'NIP-021', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2022-05-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(9, 'IT Support', 'Yusuf Hidayat', 'NIP-022', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2023-01-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(10, 'Manager Finance', 'Linda Permata', 'NIP-030', 'Finance', 'Agus Wibowo', 'Jakarta', 'Tetap', '2020-06-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(11, 'Staff Accounting', 'Wahyu Nugroho', 'NIP-031', 'Finance', 'Linda Permata', 'Jakarta', 'Tetap', '2021-08-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(12, 'Staff Pajak', 'Fitri Handayani', 'NIP-032', 'Finance', 'Linda Permata', 'Jakarta', 'Kontrak', '2022-09-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(13, 'Manager Operasional', 'Dody Kurniawan', 'NIP-040', 'Operasional', 'Siti Rahayu', 'Surabaya', 'Tetap', '2019-11-01', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(14, 'Supervisor Lapangan', 'Teguh Santosa', 'NIP-041', 'Operasional', 'Dody Kurniawan', 'Surabaya', 'Tetap', '2021-01-10', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(15, 'Teknisi', 'Arif Budiman', 'NIP-042', 'Operasional', 'Teguh Santosa', 'Surabaya', 'Kontrak', '2023-03-15', '2026-07-09 03:36:24', '2026-07-09 03:36:24');

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
(1, 1, 'CV Suku Cadang Motor', '081234567890', 'Oli Mesin', 50, 75000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(2, 1, 'PT Ban Indonesia', '082233445566', 'Ban Mobil', 20, 850000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(3, 1, 'Toko Sparepart Jaya', '081377788899', 'Aki Mobil', 15, 1200000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(4, 1, 'CV Audio Mobil', '081299988877', 'GPS Tracker', 10, 450000, '2026-07-09 03:36:18', '2026-07-09 03:36:18'),
(5, 1, 'PT Diesel Utama', '082122334455', 'Filter Solar', 40, 95000, '2026-07-09 03:36:18', '2026-07-09 03:36:18');

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
(1, 'Database ERP Production', 'Full', 'Harian', 'AWS S3 Bucket', 'Aktif', '2025-01-15', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'File Server Dokumen', 'Incremental', 'Mingguan', 'NAS Lokal + Cloud', 'Aktif', '2024-12-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'Email Server', 'Full', 'Mingguan', 'Tape Drive', 'Aktif', '2025-02-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'Aplikasi HRIS', 'Differential', 'Harian', 'GCP Storage', 'Gagal', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'Website Company Profile', 'Full', 'Bulanan', 'Hosting cPanel', 'Nonaktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'Andi', '2026-01', 53000000.00, 28000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'Budi', '2026-01', 26000000.00, 65000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'Cici', '2026-01', 53000000.00, 42000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'Dani', '2026-01', 35000000.00, 18000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'Andi', '2026-02', 29000000.00, 46000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'Budi', '2026-02', 33000000.00, 48000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'Cici', '2026-02', 55000000.00, 41000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'Dani', '2026-02', 50000000.00, 37000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(9, 'Andi', '2026-03', 54000000.00, 46000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(10, 'Budi', '2026-03', 33000000.00, 20000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(11, 'Cici', '2026-03', 41000000.00, 64000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(12, 'Dani', '2026-03', 50000000.00, 51000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(13, 'Andi', '2026-04', 49000000.00, 32000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(14, 'Budi', '2026-04', 55000000.00, 24000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(15, 'Cici', '2026-04', 26000000.00, 54000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(16, 'Dani', '2026-04', 25000000.00, 40000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(17, 'Andi', '2026-05', 35000000.00, 21000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(18, 'Budi', '2026-05', 60000000.00, 24000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(19, 'Cici', '2026-05', 49000000.00, 52000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(20, 'Dani', '2026-05', 44000000.00, 28000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(21, 'Andi', '2026-06', 25000000.00, 34000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(22, 'Budi', '2026-06', 38000000.00, 56000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(23, 'Cici', '2026-06', 36000000.00, 37000000.00, 'Target tercapai', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(24, 'Dani', '2026-06', 35000000.00, 17000000.00, 'Belum mencapai target', '2026-07-09 03:36:20', '2026-07-09 03:36:20');

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
(1, 'UTM001', 'https://apyrent.com/promo', 'google', 'cpc', 'rental_promo_q3', 'sewa mobil jakarta', 'text_ad_1', 450, 32, 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21'),
(2, 'UTM002', 'https://apyrent.com/fleet', 'email', 'newsletter', 'new_cars_2026', NULL, 'banner_top', 280, 19, 'Aktif', '2026-07-09 03:36:21', '2026-07-09 03:36:21');

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
(1, 'Test User', 'testuser', 'test@example.com', '$2y$12$GipHkBWzdfcIxsPJXy4J8uncVhY/M7wSv4H55ssuW/Ycegtlh0OBK', '08123456789', NULL, 'superadmin', 'aktif', 'atZQhq6YduVBWhnb4kqdl4yHc3JgHbgQPop6dVrntsC1f56SuuJNT43utHF4', '2026-07-09 03:36:16', '2026-07-09 03:36:16');

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
(1, 'Budi Santoso', 'IT', 'Administrator', 'ERP Sistem', 'Aktif', 'Admin utama sistem', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'Sari Dewi', 'Finance', 'Read-Write', 'Accounting Software', 'Aktif', NULL, '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'Rudi Hermawan', 'HR', 'Read Only', 'HRIS', 'Aktif', 'Akses terbatas laporan', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'Dewi Cahyani', 'Sales', 'User', 'CRM', 'Nonaktif', 'Karyawan resign Desember 2024', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'Anto Nugroho', 'Operasional', 'Read-Write', 'ERP Sistem', 'Suspended', 'Akses ditangguhkan sementara', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'VDR-001', 'CV Vendor Nusantara 1', 'Bahan Baku', 'Jl. Jakarta No. 3', 'PIC Vendor 1', '08889012037', 'Kain Katun', 4, 'Aktif', '2026-02-01', 'Catatan vendor ke-1', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(2, 'VDR-002', 'PT Vendor Nusantara 2', 'Packaging', 'Jl. Bandung No. 6', 'PIC Vendor 2', '08722902231', 'Kardus dan Label', 5, 'Aktif', '2025-09-14', 'Catatan vendor ke-2', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(3, 'VDR-003', 'CV Vendor Nusantara 3', 'Jasa IT', 'Jl. Semarang No. 9', 'PIC Vendor 3', '08603816036', 'Maintenance Sistem', 3, 'Aktif', '2025-10-08', 'Catatan vendor ke-3', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(4, 'VDR-004', 'PT Vendor Nusantara 4', 'Spare Part', 'Jl. Yogyakarta No. 12', 'PIC Vendor 4', '08973852176', 'Spare Part Kendaraan', 4, 'Aktif', '2025-09-20', 'Catatan vendor ke-4', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(5, 'VDR-005', 'CV Vendor Nusantara 5', 'Logistik', 'Jl. Surabaya No. 15', 'PIC Vendor 5', '08347595396', 'Pengiriman Barang', 4, 'Tidak Aktif', '2025-12-31', 'Catatan vendor ke-5', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(6, 'VDR-006', 'PT Vendor Nusantara 6', 'Maintenance', 'Jl. Medan No. 18', 'PIC Vendor 6', '08820980698', 'Servis Mesin', 2, 'Aktif', '2025-07-26', 'Catatan vendor ke-6', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(7, 'VDR-007', 'CV Vendor Nusantara 7', 'Cleaning', 'Jl. Makassar No. 21', 'PIC Vendor 7', '08814806485', 'Jasa Kebersihan', 3, 'Aktif', '2026-01-20', 'Catatan vendor ke-7', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(8, 'VDR-008', 'PT Vendor Nusantara 8', 'Security', 'Jl. Palembang No. 24', 'PIC Vendor 8', '08552560826', 'Jasa Keamanan', 3, 'Aktif', '2025-09-08', 'Catatan vendor ke-8', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(9, 'VDR-009', 'CV Vendor Nusantara 9', 'Percetakan', 'Jl. Malang No. 27', 'PIC Vendor 9', '08398404102', 'Cetak Dokumen', 4, 'Aktif', '2025-12-01', 'Catatan vendor ke-9', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(10, 'VDR-010', 'PT Vendor Nusantara 10', 'ATK', 'Jl. Solo No. 30', 'PIC Vendor 10', '08414418437', 'Alat Tulis Kantor', 3, 'Tidak Aktif', '2025-12-23', 'Catatan vendor ke-10', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(11, 'VDR-011', 'CV Vendor Nusantara 11', 'Bahan Baku', 'Jl. Jakarta No. 33', 'PIC Vendor 11', '08318929573', 'Cat dan Kimia', 3, 'Aktif', '2025-07-28', 'Catatan vendor ke-11', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(12, 'VDR-012', 'PT Vendor Nusantara 12', 'Packaging', 'Jl. Bandung No. 36', 'PIC Vendor 12', '08317466935', 'Komputer dan Aksesoris', 3, 'Aktif', '2026-02-26', 'Catatan vendor ke-12', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(13, 'VDR-013', 'CV Vendor Nusantara 13', 'Jasa IT', 'Jl. Semarang No. 39', 'PIC Vendor 13', '08955254755', 'Mebel Kantor', 2, 'Aktif', '2026-03-06', 'Catatan vendor ke-13', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(14, 'VDR-014', 'PT Vendor Nusantara 14', 'Spare Part', 'Jl. Yogyakarta No. 42', 'PIC Vendor 14', '08859677123', 'Genset dan Panel', 3, 'Aktif', '2025-08-08', 'Catatan vendor ke-14', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(15, 'VDR-015', 'CV Vendor Nusantara 15', 'Logistik', 'Jl. Surabaya No. 45', 'PIC Vendor 15', '08916652259', 'Seragam Karyawan', 4, 'Tidak Aktif', '2026-06-01', 'Catatan vendor ke-15', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(16, 'VDR-016', 'PT Vendor Nusantara 16', 'Maintenance', 'Jl. Medan No. 48', 'PIC Vendor 16', '08307658564', 'Kain Katun', 3, 'Aktif', '2026-05-05', 'Catatan vendor ke-16', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(17, 'VDR-017', 'CV Vendor Nusantara 17', 'Cleaning', 'Jl. Makassar No. 51', 'PIC Vendor 17', '08186860426', 'Kardus dan Label', 3, 'Aktif', '2026-05-31', 'Catatan vendor ke-17', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(18, 'VDR-018', 'PT Vendor Nusantara 18', 'Security', 'Jl. Palembang No. 54', 'PIC Vendor 18', '08803478452', 'Maintenance Sistem', 5, 'Aktif', '2025-10-10', 'Catatan vendor ke-18', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(19, 'VDR-019', 'CV Vendor Nusantara 19', 'Percetakan', 'Jl. Malang No. 57', 'PIC Vendor 19', '08825948795', 'Spare Part Kendaraan', 4, 'Aktif', '2025-08-07', 'Catatan vendor ke-19', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(20, 'VDR-020', 'PT Vendor Nusantara 20', 'ATK', 'Jl. Solo No. 60', 'PIC Vendor 20', '08856846684', 'Pengiriman Barang', 5, 'Tidak Aktif', '2025-08-13', 'Catatan vendor ke-20', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(21, 'VDR-021', 'CV Vendor Nusantara 21', 'Bahan Baku', 'Jl. Jakarta No. 63', 'PIC Vendor 21', '08177096330', 'Servis Mesin', 4, 'Aktif', '2026-04-10', 'Catatan vendor ke-21', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(22, 'VDR-022', 'PT Vendor Nusantara 22', 'Packaging', 'Jl. Bandung No. 66', 'PIC Vendor 22', '08122429816', 'Jasa Kebersihan', 5, 'Aktif', '2026-03-16', 'Catatan vendor ke-22', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(23, 'VDR-023', 'CV Vendor Nusantara 23', 'Jasa IT', 'Jl. Semarang No. 69', 'PIC Vendor 23', '08757328237', 'Jasa Keamanan', 3, 'Aktif', '2026-02-28', 'Catatan vendor ke-23', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(24, 'VDR-024', 'PT Vendor Nusantara 24', 'Spare Part', 'Jl. Yogyakarta No. 72', 'PIC Vendor 24', '08940388406', 'Cetak Dokumen', 5, 'Aktif', '2025-08-09', 'Catatan vendor ke-24', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(25, 'VDR-025', 'CV Vendor Nusantara 25', 'Logistik', 'Jl. Surabaya No. 75', 'PIC Vendor 25', '08197702105', 'Alat Tulis Kantor', 4, 'Tidak Aktif', '2025-08-16', 'Catatan vendor ke-25', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(26, 'VDR-026', 'PT Vendor Nusantara 26', 'Maintenance', 'Jl. Medan No. 78', 'PIC Vendor 26', '08603879097', 'Cat dan Kimia', 3, 'Aktif', '2025-12-19', 'Catatan vendor ke-26', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(27, 'VDR-027', 'CV Vendor Nusantara 27', 'Cleaning', 'Jl. Makassar No. 81', 'PIC Vendor 27', '08665779481', 'Komputer dan Aksesoris', 2, 'Aktif', '2025-11-15', 'Catatan vendor ke-27', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(28, 'VDR-028', 'PT Vendor Nusantara 28', 'Security', 'Jl. Palembang No. 84', 'PIC Vendor 28', '08940907429', 'Mebel Kantor', 2, 'Aktif', '2025-11-29', 'Catatan vendor ke-28', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(29, 'VDR-029', 'CV Vendor Nusantara 29', 'Percetakan', 'Jl. Malang No. 87', 'PIC Vendor 29', '08424921510', 'Genset dan Panel', 4, 'Aktif', '2025-07-23', 'Catatan vendor ke-29', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(30, 'VDR-030', 'PT Vendor Nusantara 30', 'ATK', 'Jl. Solo No. 90', 'PIC Vendor 30', '08638797053', 'Seragam Karyawan', 4, 'Tidak Aktif', '2025-11-17', 'Catatan vendor ke-30', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(31, 'VDR-031', 'CV Vendor Nusantara 31', 'Bahan Baku', 'Jl. Jakarta No. 93', 'PIC Vendor 31', '08931546961', 'Kain Katun', 2, 'Aktif', '2025-10-11', 'Catatan vendor ke-31', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(32, 'VDR-032', 'PT Vendor Nusantara 32', 'Packaging', 'Jl. Bandung No. 96', 'PIC Vendor 32', '08259863063', 'Kardus dan Label', 4, 'Aktif', '2025-12-22', 'Catatan vendor ke-32', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(33, 'VDR-033', 'CV Vendor Nusantara 33', 'Jasa IT', 'Jl. Semarang No. 99', 'PIC Vendor 33', '08196417055', 'Maintenance Sistem', 3, 'Aktif', '2025-11-19', 'Catatan vendor ke-33', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(34, 'VDR-034', 'PT Vendor Nusantara 34', 'Spare Part', 'Jl. Yogyakarta No. 102', 'PIC Vendor 34', '08671718497', 'Spare Part Kendaraan', 3, 'Aktif', '2026-05-03', 'Catatan vendor ke-34', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(35, 'VDR-035', 'CV Vendor Nusantara 35', 'Logistik', 'Jl. Surabaya No. 105', 'PIC Vendor 35', '08130992695', 'Pengiriman Barang', 3, 'Tidak Aktif', '2025-12-21', 'Catatan vendor ke-35', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(36, 'VDR-036', 'PT Vendor Nusantara 36', 'Maintenance', 'Jl. Medan No. 108', 'PIC Vendor 36', '08814238788', 'Servis Mesin', 2, 'Aktif', '2025-11-25', 'Catatan vendor ke-36', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(37, 'VDR-037', 'CV Vendor Nusantara 37', 'Cleaning', 'Jl. Makassar No. 111', 'PIC Vendor 37', '08247102370', 'Jasa Kebersihan', 5, 'Aktif', '2026-01-08', 'Catatan vendor ke-37', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(38, 'VDR-038', 'PT Vendor Nusantara 38', 'Security', 'Jl. Palembang No. 114', 'PIC Vendor 38', '08160977691', 'Jasa Keamanan', 2, 'Aktif', '2025-08-23', 'Catatan vendor ke-38', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(39, 'VDR-039', 'CV Vendor Nusantara 39', 'Percetakan', 'Jl. Malang No. 117', 'PIC Vendor 39', '08523271904', 'Cetak Dokumen', 2, 'Aktif', '2025-08-02', 'Catatan vendor ke-39', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(40, 'VDR-040', 'PT Vendor Nusantara 40', 'ATK', 'Jl. Solo No. 120', 'PIC Vendor 40', '08817579642', 'Alat Tulis Kantor', 5, 'Tidak Aktif', '2026-04-25', 'Catatan vendor ke-40', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(41, 'VDR-041', 'CV Vendor Nusantara 41', 'Bahan Baku', 'Jl. Jakarta No. 123', 'PIC Vendor 41', '08503740045', 'Cat dan Kimia', 4, 'Aktif', '2026-04-13', 'Catatan vendor ke-41', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(42, 'VDR-042', 'PT Vendor Nusantara 42', 'Packaging', 'Jl. Bandung No. 126', 'PIC Vendor 42', '08723652605', 'Komputer dan Aksesoris', 5, 'Aktif', '2025-07-17', 'Catatan vendor ke-42', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(43, 'VDR-043', 'CV Vendor Nusantara 43', 'Jasa IT', 'Jl. Semarang No. 129', 'PIC Vendor 43', '08180216726', 'Mebel Kantor', 2, 'Aktif', '2025-10-03', 'Catatan vendor ke-43', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(44, 'VDR-044', 'PT Vendor Nusantara 44', 'Spare Part', 'Jl. Yogyakarta No. 132', 'PIC Vendor 44', '08932985256', 'Genset dan Panel', 4, 'Aktif', '2026-03-03', 'Catatan vendor ke-44', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(45, 'VDR-045', 'CV Vendor Nusantara 45', 'Logistik', 'Jl. Surabaya No. 135', 'PIC Vendor 45', '08993633465', 'Seragam Karyawan', 4, 'Tidak Aktif', '2025-09-30', 'Catatan vendor ke-45', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(46, 'VDR-046', 'PT Vendor Nusantara 46', 'Maintenance', 'Jl. Medan No. 138', 'PIC Vendor 46', '08694853717', 'Kain Katun', 5, 'Aktif', '2025-09-28', 'Catatan vendor ke-46', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(47, 'VDR-047', 'CV Vendor Nusantara 47', 'Cleaning', 'Jl. Makassar No. 141', 'PIC Vendor 47', '08745793935', 'Kardus dan Label', 4, 'Aktif', '2025-10-11', 'Catatan vendor ke-47', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(48, 'VDR-048', 'PT Vendor Nusantara 48', 'Security', 'Jl. Palembang No. 144', 'PIC Vendor 48', '08970297182', 'Maintenance Sistem', 2, 'Aktif', '2026-04-19', 'Catatan vendor ke-48', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(49, 'VDR-049', 'CV Vendor Nusantara 49', 'Percetakan', 'Jl. Malang No. 147', 'PIC Vendor 49', '08145949093', 'Spare Part Kendaraan', 2, 'Aktif', '2025-12-02', 'Catatan vendor ke-49', '2026-07-09 03:36:20', '2026-07-09 03:36:20'),
(50, 'VDR-050', 'PT Vendor Nusantara 50', 'ATK', 'Jl. Solo No. 150', 'PIC Vendor 50', '08486683699', 'Pengiriman Barang', 4, 'Tidak Aktif', '2026-03-13', 'Catatan vendor ke-50', '2026-07-09 03:36:20', '2026-07-09 03:36:20');

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
(1, 'PT Maju Jaya', 48, 91.67, 88.50, 3, 89.20, 'Vendor terpercaya, pengiriman konsisten', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'CV Berkah Abadi', 35, 74.29, 80.00, 7, 76.50, 'Perlu peningkatan ketepatan waktu pengiriman', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'PT Sumber Makmur', 60, 95.00, 92.30, 2, 93.50, 'Performa terbaik, kualitas produk sangat baik', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'UD Sejahtera', 22, 63.64, 70.00, 9, 65.80, 'Banyak komplain, perlu evaluasi ulang kontrak', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'PT Indo Supplier', 41, 82.93, 85.00, 5, 83.20, 'Performa stabil, harga kompetitif', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 469046, 'pcs', 5.20, 15, 16, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(2, 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 153334, 'liter', 6.02, 46, 8, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(3, 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 1410850, 'unit', 7.53, 5, 28, '2026-07-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(4, 'UD Sejahtera', 'BRG-004', 'Filter Udara', 1151423, 'set', 16.09, 1, 22, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(5, 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 121020, 'buah', 20.04, 1, 30, '2026-05-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(6, 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 883422, 'pcs', 11.31, 15, 17, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(7, 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 28870, 'liter', 17.53, 24, 22, '2026-05-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(8, 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 324409, 'unit', 18.99, 2, 7, '2026-05-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(9, 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 1167999, 'set', 18.64, 33, 16, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(10, 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 1133326, 'buah', 17.34, 11, 10, '2026-05-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(11, 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 987295, 'pcs', 12.79, 4, 6, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(12, 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 1115367, 'liter', 3.52, 10, 25, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(13, 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 327737, 'unit', 10.16, 33, 16, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(14, 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 605849, 'set', 14.97, 46, 18, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(15, 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 547473, 'buah', 10.55, 20, 22, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(16, 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 173934, 'pcs', 2.77, 15, 22, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(17, 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 426825, 'liter', 6.77, 40, 27, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(18, 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 1443959, 'unit', 6.12, 4, 25, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(19, 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 1165516, 'set', 1.99, 15, 26, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(20, 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 1065316, 'buah', 1.82, 45, 10, '2026-06-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(21, 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 1057214, 'pcs', 11.54, 16, 19, '2026-05-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(22, 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 1085984, 'liter', 8.84, 2, 15, '2026-05-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(23, 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 1369046, 'unit', 12.56, 5, 13, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(24, 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 995099, 'set', 8.05, 25, 26, '2026-04-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22'),
(25, 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 65656, 'buah', 12.85, 29, 3, '2026-05-01', '2026-07-09 03:36:22', '2026-07-09 03:36:22');

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
(1, 'VA-75692738', 1, NULL, NULL, 'bca', 1500000.00, 0.00, 'Pending', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19'),
(2, 'VA-36690953', 1, NULL, NULL, 'bni', 2500000.00, 2500000.00, 'paid', NULL, '2026-07-09 03:36:19', '2026-07-09 03:36:19');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `member_kendaraan`
--
ALTER TABLE `member_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

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
