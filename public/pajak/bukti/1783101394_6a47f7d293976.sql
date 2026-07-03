-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jul 2026 pada 16.33
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
-- Database: `apyerpsystem2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `klik` int(11) NOT NULL,
  `konversi` int(11) NOT NULL,
  `biaya_total` decimal(15,2) NOT NULL,
  `penjualan` decimal(15,2) NOT NULL,
  `roi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ads_integrations`
--

INSERT INTO `ads_integrations` (`id`, `id_iklan`, `nama_iklan`, `platform`, `tanggal_aktif`, `budget_harian`, `klik`, `konversi`, `biaya_total`, `penjualan`, `roi`, `created_at`, `updated_at`) VALUES
(1, 'ADS001', 'Ads Celana Pria', 'Google', '2025-06-01', 100000.00, 250, 35, 2000000.00, 6000000.00, '200%', '2026-07-01 21:18:36', '2026-07-01 21:18:36');

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
  `status` enum('Aktif','Nonaktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `afiliasis`
--

INSERT INTO `afiliasis` (`id`, `id_program`, `nama_program`, `kode_referral`, `diskon_referral`, `bonus_pengajak`, `batas_waktu`, `status`, `created_at`, `updated_at`) VALUES
(1, 'REF001', 'Ajak Teman', 'AJAKTEMAN50', 50000.00, 'Rp20.000 saldo belanja', '2025-12-31', 'Aktif', '2026-07-01 21:18:36', '2026-07-01 21:18:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `aging_aps`
--

CREATE TABLE `aging_aps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `no_tagihan` varchar(255) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `umur` int(11) NOT NULL,
  `jumlah` bigint(20) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `aging_aps`
--

INSERT INTO `aging_aps` (`id`, `vendor`, `no_tagihan`, `jatuh_tempo`, `umur`, `jumlah`, `kategori`, `created_at`, `updated_at`) VALUES
(1, 'CV Maju', 'TAG-001', '2025-05-01', 54, 6000000, 'Overdue 1', '2026-07-01 21:18:29', '2026-07-01 21:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `aging_ars`
--

CREATE TABLE `aging_ars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer` varchar(255) NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `umur` int(11) NOT NULL,
  `total` bigint(20) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `aging_ars`
--

INSERT INTO `aging_ars` (`id`, `customer`, `invoice`, `jatuh_tempo`, `umur`, `total`, `kategori`, `created_at`, `updated_at`) VALUES
(1, 'PT ABC', 'INV-001', '2025-06-10', 14, 5000000, 'Current', '2026-07-01 21:18:29', '2026-07-01 21:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `api_integrations`
--

CREATE TABLE `api_integrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `tanggal` date NOT NULL,
  `status_approval` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `approval_workflows`
--

INSERT INTO `approval_workflows` (`id`, `id_po`, `urutan_approval`, `jabatan`, `nama_approver`, `tanggal`, `status_approval`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PO-001', 1, 'Purchasing Officer', 'Rina Sari', '2025-06-03', 'Disetujui', 'Sesuai budget', NULL, NULL),
(2, 'PO-001', 2, 'Manager Keuangan', 'Budi Setiawan', '2025-06-03', 'Disetujui', 'Approved', NULL, NULL),
(3, 'PO-002', 1, 'Purchasing Officer', 'Rina Sari', '2025-06-04', 'Menunggu', '-', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_dihapuskans`
--

CREATE TABLE `asset_dihapuskans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `tanggal_hapus` date NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `nilai_buku` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_akhir` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `asset_dihapuskans`
--

INSERT INTO `asset_dihapuskans` (`id`, `kode_aset`, `nama_aset`, `tanggal_hapus`, `alasan`, `nilai_buku`, `status_akhir`, `created_at`, `updated_at`) VALUES
(1, 'AST-005', 'Printer Canon MP287', '2024-01-01', 'Rusak total', 0.00, 'Dihapuskan', NULL, NULL),
(2, 'AST-006', 'Kursi Kantor 5 buah', '2024-03-15', 'Tidak layak pakai', 200000.00, 'Dijual scrap', NULL, NULL);

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

--
-- Dumping data untuk tabel `audit_assets`
--

INSERT INTO `audit_assets` (`id`, `kode_aset`, `tanggal_audit`, `diperiksa_oleh`, `status_fisik`, `temuan`, `tindakan`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', '2025-06-01', 'Supervisor IT', 'Baik', 'Tidak Ada', '-', 'Unit bersih & normal', NULL, NULL),
(2, 'AST-002', '2025-06-01', 'Kepala Produksi', 'Baik', 'Kabel longgar', 'Diperbaiki', 'Perlu dicek ulang 3 bulan lagi', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `auto_reconciles`
--

CREATE TABLE `auto_reconciles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'IDR',
  `status_rekonsiliasi` enum('matched','unmatched','pending') DEFAULT 'pending',
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `va_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `auto_reconciles`
--

INSERT INTO `auto_reconciles` (`id`, `tanggal`, `deskripsi`, `reference_no`, `amount`, `currency`, `status_rekonsiliasi`, `invoice_id`, `va_id`, `created_at`, `updated_at`) VALUES
(1, '2026-07-01', 'Pembayaran VA sesuai invoice', 'TRX001', 1500000.00, 'IDR', 'matched', 12345, 1, '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(2, '2026-07-02', 'Pembayaran tidak sesuai nominal', 'TRX002', 1200000.00, 'IDR', 'pending', 2345, 1, '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(3, '2026-06-30', 'Pembayaran tanpa invoice', 'TRX003', 500000.00, 'IDR', 'unmatched', NULL, NULL, '2026-07-01 21:18:29', '2026-07-01 21:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `billof_materials`
--

CREATE TABLE `billof_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_bom` varchar(255) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `tipe_produk` varchar(255) NOT NULL,
  `versi_bom` varchar(255) NOT NULL,
  `komponen` varchar(255) NOT NULL,
  `qty` double NOT NULL,
  `unit` varchar(255) NOT NULL,
  `sumber` varchar(255) DEFAULT NULL,
  `waktu_proses` varchar(255) DEFAULT NULL,
  `persen_scrap` varchar(255) DEFAULT NULL,
  `substitusi` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `billof_materials`
--

INSERT INTO `billof_materials` (`id`, `kode_bom`, `nama_produk`, `tipe_produk`, `versi_bom`, `komponen`, `qty`, `unit`, `sumber`, `waktu_proses`, `persen_scrap`, `substitusi`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'BOM-001', 'Kemeja Formal Pria', 'Finished Product', 'V1.0', 'Kain Katun 40s', 1.6, 'meter', 'Pembelian', '2 hari', '2%', 'Kain Oxford', 'Jahit manual', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, 'BOM-001', 'Kemeja Formal Pria', 'Finished Product', 'V1.0', 'Kancing 4 lubang', 8, 'pcs', 'Gudang', NULL, '1%', NULL, 'Putih, plastik', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(3, 'BOM-001', 'Kemeja Formal Pria', 'Finished Product', 'V1.0', 'Benang Jahit', 1, 'roll', 'Pembelian', NULL, NULL, NULL, 'Warna senada', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(4, 'BOM-002', 'Celana Panjang Slimfit', 'Finished Product', 'V2.1', 'Kain Drill Jepang', 2, 'meter', 'Pembelian', '3 hari', '3%', 'Drill Lokal', 'Produksi Massal', '2026-07-01 21:18:35', '2026-07-01 21:18:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `budgeting_proyeks`
--

CREATE TABLE `budgeting_proyeks` (
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
-- Dumping data untuk tabel `budgeting_proyeks`
--

INSERT INTO `budgeting_proyeks` (`id`, `proyek`, `kategori`, `budget`, `realisasi`, `sisa`, `persen_terpakai`, `created_at`, `updated_at`) VALUES
(1, 'Gedung A', 'Material', 20000000.00, 15000000.00, 5000000.00, 75.00, '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(2, 'Gedung A', 'Tenaga Kerja', 25000000.00, 18000000.00, 7000000.00, 72.00, '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(3, 'Gedung A', 'Peralatan', 10000000.00, 12000000.00, -2000000.00, 120.00, '2026-07-01 21:18:29', '2026-07-01 21:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku_besars`
--

CREATE TABLE `buku_besars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_jurnal` varchar(255) DEFAULT NULL,
  `transaksi` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `debit` bigint(20) DEFAULT 0,
  `kredit` bigint(20) DEFAULT 0,
  `saldo` bigint(20) DEFAULT 0,
  `aktivitas` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `buku_besars`
--

INSERT INTO `buku_besars` (`id`, `kode_jurnal`, `transaksi`, `kategori`, `tanggal`, `debit`, `kredit`, `saldo`, `aktivitas`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'JR001', 'Pendapatan Sewa Harian', 'Pendapatan', '2026-07-02', 0, 2500000, 2500000, 'operasi', 'Penyewaan unit mobil selama 3 hari', '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(2, 'JR002', 'Beban Gaji Karyawan', 'Beban', '2026-07-02', 1200000, 0, -1200000, 'operasi', 'Pembayaran gaji staff administrasi', '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(3, 'JR003', 'Pembelian Kendaraan Operasional', 'Aktiva', '2026-07-02', 500000000, 0, 500000000, 'investasi', 'Pembelian mobil baru untuk disewakan', '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(4, 'JR004', 'Setoran Modal Awal', 'Modal', '2026-07-02', 0, 1000000000, 1000000000, 'pendanaan', 'Investasi pemilik sebagai modal awal', '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(5, 'JR005', 'Pembayaran Utang Bank', 'Kewajiban', '2026-07-02', 150000000, 0, -150000000, 'pendanaan', 'Pelunasan sebagian utang bank kendaraan', '2026-07-01 21:18:28', '2026-07-01 21:18:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('toty@gmail|127.0.0.1', 'i:2;', 1782966621),
('toty@gmail|127.0.0.1:timer', 'i:1782966621;', 1782966621);

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
-- Struktur dari tabel `cash_flow_forecasts`
--

CREATE TABLE `cash_flow_forecasts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `masuk` bigint(20) NOT NULL DEFAULT 0,
  `keluar` bigint(20) NOT NULL DEFAULT 0,
  `saldo_akhir` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cash_flow_forecasts`
--

INSERT INTO `cash_flow_forecasts` (`id`, `tanggal`, `masuk`, `keluar`, `saldo_akhir`, `created_at`, `updated_at`) VALUES
(1, '2025-06-01', 80000000, 60000000, 20000000, '2026-07-01 21:18:29', '2026-07-01 21:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `contact_management`
--

CREATE TABLE `contact_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `contract_handlings`
--

CREATE TABLE `contract_handlings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cost_estimations`
--

CREATE TABLE `cost_estimations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produk` varchar(255) NOT NULL,
  `biaya_material` bigint(20) NOT NULL,
  `biaya_tenaga_kerja` bigint(20) NOT NULL,
  `overhead` bigint(20) NOT NULL,
  `total_biaya` bigint(20) NOT NULL,
  `margin_target` int(11) NOT NULL,
  `harga_jual` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cost_estimations`
--

INSERT INTO `cost_estimations` (`id`, `produk`, `biaya_material`, `biaya_tenaga_kerja`, `overhead`, `total_biaya`, `margin_target`, `harga_jual`, `created_at`, `updated_at`) VALUES
(1, 'Kemeja Pria Formal', 30000, 20000, 10000, 60000, 40, 84000, '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, 'Celana Slimfit', 40000, 25000, 12000, 77000, 35, 104000, '2026-07-01 21:18:35', '2026-07-01 21:18:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_dashboards`
--

CREATE TABLE `crm_dashboards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_notifications`
--

CREATE TABLE `crm_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_permissions`
--

CREATE TABLE `crm_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
  `tahapan` varchar(255) NOT NULL,
  `estimasi_deal` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `sales` varchar(255) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_prospeks`
--

INSERT INTO `crm_prospeks` (`id`, `kode_prospek`, `nama_kontak`, `perusahaan`, `email`, `tahapan`, `estimasi_deal`, `status`, `sales`, `tanggal_masuk`, `created_at`, `updated_at`) VALUES
(1, 'CRM001', 'Budi Santoso', 'PT Sukses Jaya', 'budi@sukses.com', 'Meeting', 5000000.00, 'Aktif', 'Rina', '2025-06-20', '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer_ratings`
--

CREATE TABLE `customer_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Ahmad Hidayat', 'Cuti Tahunan', '2025-07-01', '2025-07-05', 5, 'Liburan keluarga', 'Disetujui', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'Siti Rahma', 'Izin Sakit', '2025-07-15', '2025-07-17', 3, 'Demam tinggi', 'Pending', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(3, 'Budi Santoso', 'Cuti Melahirkan', '2025-07-01', '2025-09-28', 90, 'Istri melahirkan', 'Disetujui', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

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
(1, '2025-06-20', 'Server Lokal', 'Backup tidak terenkripsi', 'Tinggi', 'Implementasi AES Encryption', 'Selesai', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, '2025-05-15', 'Email Karyawan', 'Password lemah', 'Sedang', 'Wajib 2FA & policy 90 hari', 'Dalam Proses', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

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

--
-- Dumping data untuk tabel `daftar_notaris`
--

INSERT INTO `daftar_notaris` (`id`, `nama_kantor`, `layanan`, `kontak`, `email`, `terakhir_dipakai`, `created_at`, `updated_at`) VALUES
(1, 'Notaris Elvira S.H', 'Akta & Perizinan', '0812-1111-2222', 'elviranotaris@firm.co.id', '2025-06-21', NULL, NULL),
(2, 'Kantor Hukum Maju', 'Litigasi & Draft', '0822-3333-4444', 'legalmaju@firm.co.id', '2025-06-29', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_kirs`
--

CREATE TABLE `data_kirs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_uji_berkala` varchar(255) NOT NULL,
  `no_polisi` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) NOT NULL,
  `merk_type` varchar(255) NOT NULL,
  `jenis_model` varchar(255) NOT NULL,
  `masa_berlaku` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `data_kirs`
--

INSERT INTO `data_kirs` (`id`, `no_uji_berkala`, `no_polisi`, `nama_pemilik`, `merk_type`, `jenis_model`, `masa_berlaku`, `created_at`, `updated_at`) VALUES
(1, 'JKT1512550', 'B 9395 SRU', 'PT ANUGERAH PANCA YOGA', 'ISUZU', 'LIGHT TRUCK', '2023-07-30', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(2, 'CP19700', 'B 9231 TRO', 'NUR FAUZIAH S', 'HINO / 110 LD', 'MOBIL BARANG', '2022-02-24', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(3, 'JKT2007815', 'B 9674 TCL', 'PT PANGAN SARI UTAMA', 'HINO DUTRO', 'MOBIL BARANG', '2022-08-11', '2026-07-01 21:18:42', '2026-07-01 21:18:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_members`
--

CREATE TABLE `data_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_polisi` varchar(255) NOT NULL,
  `nama_member` varchar(255) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `masa_berlaku` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `data_members`
--

INSERT INTO `data_members` (`id`, `no_polisi`, `nama_member`, `no_telp`, `alamat`, `masa_berlaku`, `created_at`, `updated_at`) VALUES
(1, 'B 2890 SIK', 'PT. ANUGERAH PANCA YOGA', '021 83792927', 'JL DR SAHARJO NO 131', '2019-10-02', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(2, 'B 9538 TF', 'PT. ANUGERAH PANCA YOGA', '021 83792927', 'JL PROF SOEPOMO SH NO 6A TEBET JAKSEL', '2018-12-16', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(3, 'B 2890 TF', 'PT. ANUGERAH PANCA YOGA', '021 83792927', 'JL PROF SOEPOMO SH NO.6A TEBET JAKSEL', '2018-12-16', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(4, 'B 9538 TZ', 'SUGIYANTI IMAM. S', '0816 813 711', 'JL PROF SOEPOMO SH NO 6A TEBET JAKSEL', '2018-08-01', '2026-07-01 21:18:42', '2026-07-01 21:18:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_services`
--

CREATE TABLE `data_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_polisi` varchar(20) NOT NULL,
  `keluhan` text NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tanggal` date NOT NULL,
  `kilometer` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','proses','selesai') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `data_services`
--

INSERT INTO `data_services` (`id`, `no_polisi`, `keluhan`, `biaya`, `tanggal`, `kilometer`, `status`, `created_at`, `updated_at`) VALUES
(1, 'B 9883 PXR', 'Pergantian seal roda belakang kiri, storing mekanik luar batu ceper', 0.00, '2025-08-10', 0, 'pending', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(2, 'B 9674 TCL', 'Pergantian vanbelt, seal oring, mekanik luar storing batu ceper', 0.00, '2025-08-10', 0, 'pending', '2026-07-01 21:18:42', '2026-07-01 21:18:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_stnks`
--

CREATE TABLE `data_stnks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_polisi` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `merk_type` varchar(255) DEFAULT NULL,
  `jenis_model` varchar(255) DEFAULT NULL,
  `thn_pembuatan` int(11) DEFAULT NULL,
  `thn_perakitan` int(11) DEFAULT NULL,
  `isi_silinder` varchar(255) DEFAULT NULL,
  `warna` varchar(255) DEFAULT NULL,
  `no_rangka_nik` varchar(255) DEFAULT NULL,
  `no_mesin` varchar(255) DEFAULT NULL,
  `no_bpkb` varchar(255) DEFAULT NULL,
  `warna_tnkb` varchar(255) DEFAULT NULL,
  `bahan_bakar` varchar(255) DEFAULT NULL,
  `kode_lokasi` varchar(255) DEFAULT NULL,
  `no_urut_pendaftaran` varchar(255) DEFAULT NULL,
  `masa_berlaku` date DEFAULT NULL,
  `batasan_biaya` decimal(15,2) DEFAULT NULL,
  `dokumen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `data_stnks`
--

INSERT INTO `data_stnks` (`id`, `no_polisi`, `nama_pemilik`, `alamat`, `merk_type`, `jenis_model`, `thn_pembuatan`, `thn_perakitan`, `isi_silinder`, `warna`, `no_rangka_nik`, `no_mesin`, `no_bpkb`, `warna_tnkb`, `bahan_bakar`, `kode_lokasi`, `no_urut_pendaftaran`, `masa_berlaku`, `batasan_biaya`, `dokumen`, `created_at`, `updated_at`) VALUES
(1, 'B 1234 XYZ', 'PT APY Renta Car', 'Jl. Raya Pondok Gede No. 123, Jakarta', 'Toyota Avanza', 'MPV', 2021, 2021, '1500 cc', 'Hitam', 'MHKA1234567890123', '1NZ1234567', 'BPKB123456', 'Hitam', 'Bensin', 'JKT-01', '0001', '2026-08-01', 500000.00, NULL, '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(2, 'B 5678 ABC', 'PT APY Renta Car', 'Jl. Raya Bekasi No. 88, Bekasi', 'Honda Brio', 'Hatchback', 2020, 2020, '1200 cc', 'Putih', 'MHKA7654321098765', 'L12B345678', 'BPKB765432', 'Hitam', 'Bensin', 'BKS-01', '0002', '2025-10-15', 400000.00, NULL, '2026-07-01 21:18:42', '2026-07-01 21:18:42');

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
(1, 'HRD', 'Siti Nurhaliza', '2020-01-01', 8, 'Divisi SDM utama', 'Aktif', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(2, 'Produksi', 'Budi Santoso', '2018-02-01', 25, 'Operasional Pabrik', 'Aktif', '2026-07-01 21:18:33', '2026-07-01 21:18:33');

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
(1, 'Website Internal', 'GitHub Actions', 'Ya', 'Setiap commit', 'Aktif', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, 'App Mobile HRD', 'GitLab CI', 'Ya', 'Mingguan', 'Aktif', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

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

--
-- Dumping data untuk tabel `dokumentasi_assets`
--

INSERT INTO `dokumentasi_assets` (`id`, `kode_aset`, `nama_aset`, `foto_tersimpan`, `lokasi_file`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', 'Laptop Dell XPS 13', 1, '/asset/foto/AST-001.jpg', 'Diambil 2023-06-15', NULL, NULL),
(2, 'AST-002', 'Mesin Jahit Juki', 1, '/asset/foto/AST-002.jpg', 'Sudah diservis 2025', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_proyeks`
--

CREATE TABLE `dokumen_proyeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `tipe` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `tanggal_upload` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dokumen_proyeks`
--

INSERT INTO `dokumen_proyeks` (`id`, `proyek`, `nama_dokumen`, `tipe`, `file`, `status`, `tanggal_upload`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Gambar Rencana Struktur', 'PDF', 'struktur.pdf', 'Valid', '2025-06-28', '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'PRJ002', 'Surat Pesanan Vendor', 'DOCX', 'pesanan_vendor.docx', 'Draft', '2025-06-29', '2026-07-01 21:18:40', '2026-07-01 21:18:40');

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
  `customer_akhir` varchar(255) DEFAULT NULL,
  `tanggal_kirim` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dropshippings`
--

INSERT INTO `dropshippings` (`id`, `kode_transaksi`, `tipe`, `vendor`, `barang`, `jumlah`, `satuan`, `customer_akhir`, `tanggal_kirim`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DS-001', 'Dropshipping', 'PT. Garment Asia', 'Baju Koko', 100, 'pcs', 'CV. Fashion Retail', '2025-06-07', 'Dalam Pengiriman', '2026-07-01 21:18:36', '2026-07-01 21:18:36'),
(2, 'SC-001', 'Subcontracting', 'CV. Konveksi Mitra', 'Celana Jeans (Setengah Jadi)', 200, 'pcs', NULL, '2025-06-10', 'Proses Produksi', '2026-07-01 21:18:36', '2026-07-01 21:18:36');

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
  `email_aktif` int(11) NOT NULL,
  `dns_terkelola` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `email_domains`
--

INSERT INTO `email_domains` (`id`, `nama_domain`, `provider`, `status`, `expired_date`, `email_aktif`, `dns_terkelola`, `created_at`, `updated_at`) VALUES
(1, 'perusahaan.co.id', 'Niagahoster', 'Aktif', '2025-12-01', 105, 1, '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'internal.local', 'Internal', 'Internal', NULL, 45, 1, '2026-07-01 21:18:40', '2026-07-01 21:18:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `e_bupots`
--

CREATE TABLE `e_bupots` (
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
  `status` varchar(255) DEFAULT 'Draft',
  `file_csv` varchar(255) DEFAULT NULL,
  `file_bupot` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `e_bupots`
--

INSERT INTO `e_bupots` (`id`, `nomor_bukti`, `tanggal_bukti`, `tipe`, `npwp_pemotong`, `nama_pemotong`, `npwp_dipotong`, `nama_dipotong`, `jumlah_bruto`, `tarif_pajak`, `jumlah_potong`, `status`, `file_csv`, `file_bupot`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'BUPOT-2025-001', '2025-08-03', 'PPh23', '03.456.789.0-222.000', 'PT Cahaya Abadi', '01.234.567.8-999.000', 'PT Sinar Jaya', 2000000.00, 2.00, 40000.00, 'Approve', 'bupot1.csv', 'bupot1.pdf', 1, '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(2, 'BUPOT-2025-002', '2025-08-07', 'PPh21', '02.345.678.9-111.000', 'CV Mitra Abadi', '04.567.890.1-333.000', 'Budi Santoso', 15000000.00, 5.00, 750000.00, 'Draft', NULL, NULL, 1, '2026-07-01 21:18:29', '2026-07-01 21:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `e_fakturs`
--

CREATE TABLE `e_fakturs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_faktur` varchar(255) DEFAULT NULL,
  `tanggal_faktur` date DEFAULT NULL,
  `tipe` enum('Keluaran','Masukan') DEFAULT NULL,
  `npwp_lawan` varchar(255) DEFAULT NULL,
  `nama_lawan` varchar(255) DEFAULT NULL,
  `dpp` decimal(20,2) DEFAULT NULL,
  `ppn` decimal(20,2) DEFAULT NULL,
  `ppnbm` decimal(20,2) DEFAULT 0.00,
  `status` varchar(255) DEFAULT 'Draft',
  `file_xml` varchar(255) DEFAULT NULL,
  `file_faktur` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `e_fakturs`
--

INSERT INTO `e_fakturs` (`id`, `nomor_faktur`, `tanggal_faktur`, `tipe`, `npwp_lawan`, `nama_lawan`, `dpp`, `ppn`, `ppnbm`, `status`, `file_xml`, `file_faktur`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '010.001-22.12345678', '2025-08-01', 'Keluaran', '01.234.567.8-999.000', 'PT Sinar Jaya', 10000000.00, 1100000.00, 0.00, 'Approve', 'faktur1.xml', 'faktur1.pdf', 1, '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(2, '010.001-22.87654321', '2025-08-05', 'Masukan', '02.345.678.9-111.000', 'CV Mitra Abadi', 5000000.00, 550000.00, 0.00, 'Draft', NULL, NULL, 1, '2026-07-01 21:18:29', '2026-07-01 21:18:29');

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
-- Struktur dari tabel `files`
--

CREATE TABLE `files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `quotation` text DEFAULT NULL,
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

--
-- Dumping data untuk tabel `hak_hukums`
--

INSERT INTO `hak_hukums` (`id`, `jenis_akses`, `kategori_dokumen`, `penerima_akses`, `level_hak`, `tanggal_akses`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Legal Docs', 'Sertifikat Tanah', 'Direktur Legal', 'Full Read', '2025-07-01', 'Aktif', '2026-07-01 21:18:41', '2026-07-01 21:18:41'),
(2, 'Legal Docs', 'Kontrak Vendor', 'Admin HR', 'View Only', '2025-07-01', 'Aktif', '2026-07-01 21:18:41', '2026-07-01 21:18:41');

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
(1, 'TKT-2025-01', '2025-07-01', 'Akuntansi', 'Tidak bisa akses email', 'Tinggi', 'Selesai', 'Ahmad', '15 Menit', NULL, NULL),
(2, 'TKT-2025-02', '2025-06-30', 'Produksi', 'Printer error', 'Sedang', 'Dalam Proses', 'Dian', '30 Menit', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `helpdesk_ticketings`
--

CREATE TABLE `helpdesk_ticketings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `h_s_eos`
--

CREATE TABLE `h_s_eos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_po` varchar(255) NOT NULL,
  `tanggal_order` date NOT NULL,
  `divisi_pemohon` varchar(255) NOT NULL,
  `jenis_pengadaan` varchar(255) NOT NULL,
  `barang_jasa` varchar(255) NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  `satuan` varchar(255) NOT NULL,
  `harga_satuan` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `estimasi_tiba` date NOT NULL,
  `tipe_pembayaran` varchar(255) NOT NULL,
  `pic_procurement` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `h_s_eos`
--

INSERT INTO `h_s_eos` (`id`, `kode_po`, `tanggal_order`, `divisi_pemohon`, `jenis_pengadaan`, `barang_jasa`, `kode_barang`, `vendor`, `qty`, `satuan`, `harga_satuan`, `total_harga`, `status`, `estimasi_tiba`, `tipe_pembayaran`, `pic_procurement`, `created_at`, `updated_at`) VALUES
(1, 'PO-2025-001', '2025-07-01', 'Produksi', 'Barang', 'Kain Katun 40s', 'BRG-001', 'PT Tekstil Nusantara', 1000, 'meter', 15000, 15000000, 'Dipesan', '2025-07-05', '30% DP', 'Taufik', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, 'PO-2025-002', '2025-06-28', 'IT', 'Jasa', 'Servis Printer Epson', 'SRV-001', 'CV Teknoprint', NULL, 'paket', 1200000, 1200000, 'Selesai', '2025-06-29', 'Lunas', 'Yulia', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

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

--
-- Dumping data untuk tabel `induk_assets`
--

INSERT INTO `induk_assets` (`id`, `kode_aset`, `nama_aset`, `kategori`, `lokasi`, `tanggal_perolehan`, `harga_perolehan`, `status`, `pic`, `umur_ekonomis`, `metode_penyusutan`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', 'Laptop Dell XPS 13', 'Elektronik IT', 'Kantor Pusat', '2023-06-15', 18500000, 'Aktif', 'Dimas', 4, 'Garis Lurus', NULL, NULL),
(2, 'AST-002', 'Mesin Jahit Juki HZL-F600', 'Mesin Produksi', 'Workshop Bekasi', '2022-07-20', 12000000, 'Aktif', 'Wawan', 6, 'Garis Lurus', NULL, NULL);

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
  `progres` varchar(255) NOT NULL,
  `nilai_proyek` bigint(20) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `induk_proyeks`
--

INSERT INTO `induk_proyeks` (`id`, `kode`, `nama_proyek`, `jenis`, `klien_vendor`, `pic`, `status`, `mulai`, `target_selesai`, `progres`, `nilai_proyek`, `lokasi`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Renovasi Pabrik Bekasi', 'Internal', '-', 'Rudi', 'Berjalan', '2025-07-01', '2025-09-30', '35%', 450000000, 'Bekasi', NULL, NULL),
(2, 'PRJ002', 'Produksi Massal Baju Sekolah', 'Eksternal', 'PT TokoSeragam', 'Rina', 'Approved', '2025-07-05', '2025-10-15', '0%', 980000000, 'Tangerang', NULL, NULL),
(3, 'PRJ003', 'Upgrade Mesin Sablon Otomatis', 'Internal', '-', 'Ivan', 'Plan', '2025-07-15', '2025-08-15', '-', 210000000, 'Bandung', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kontrak_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('perorangan','perusahaan') NOT NULL DEFAULT 'perorangan',
  `invoice_no` text DEFAULT NULL,
  `order_no` text DEFAULT NULL,
  `customer_name` text DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `contact_person` text DEFAULT NULL,
  `telephone` text DEFAULT NULL,
  `vehicle` text DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `bank_account_name` text DEFAULT NULL,
  `bank_name` text DEFAULT NULL,
  `bank_branch` text DEFAULT NULL,
  `bank_account_no` text DEFAULT NULL,
  `pengirim` text DEFAULT NULL,
  `staff` text DEFAULT NULL,
  `name_staff` text DEFAULT NULL,
  `direktur` text DEFAULT NULL,
  `name_direktur` text DEFAULT NULL,
  `status` enum('draft','partial','overdue','lunas') DEFAULT NULL,
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `total` text DEFAULT NULL,
  `ppn` decimal(15,2) DEFAULT 0.00,
  `pph` decimal(15,2) DEFAULT 0.00,
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
  `file_pembayaran` text DEFAULT NULL,
  `transaction_id` text DEFAULT NULL,
  `status` text DEFAULT NULL,
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_remaks`
--

CREATE TABLE `invoice_remaks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `periode_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remaks` text DEFAULT NULL,
  `qty` text DEFAULT NULL,
  `price` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `periode` text DEFAULT NULL,
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `inv_penawaran_items`
--

CREATE TABLE `inv_penawaran_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penawaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `car_model` text DEFAULT NULL,
  `qty` text DEFAULT NULL,
  `tahun_unit` text DEFAULT NULL,
  `price` text DEFAULT NULL,
  `durasi` text DEFAULT NULL,
  `satuan_durasi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'IT-001', 'Laptop Finance', 'Laptop', 'Kantor Pusat', 'Rina', 'Lenovo Thinkpad', '2023', 'Aktif', 'Upgrade RAM 2024', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, 'IT-002', 'Printer Epson', 'Printer', 'Gudang A', 'Umum', 'Epson L3150', '2022', 'Rusak', 'Akan diganti', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

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
  `status` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kampanyes`
--

INSERT INTO `kampanyes` (`id`, `id_kampanye`, `nama_kampanye`, `tipe_kampanye`, `channel`, `target_segment`, `tanggal_mulai`, `tanggal_akhir`, `subjek_pesan`, `isi_pesan_ringkas`, `status`, `pic`, `created_at`, `updated_at`) VALUES
(1, 'MKT001', 'Promo Tahun Baru', 'Promosi', 'Email', 'Pelanggan Aktif', '2025-12-25', '2025-12-31', 'Diskon Akhir Tahun!', 'Dapatkan diskon 20%', 'Dijadwalkan', 'Rina', '2026-07-01 21:18:37', '2026-07-01 21:18:37'),
(2, 'MKT002', 'Re-engagement Lama', 'Retensi', 'SMS', 'Inaktif 6 Bulan', '2025-06-20', '2025-06-30', 'Kami Merindukan Anda', 'Ayo beli lagi & dapat hadiah', 'Selesai', 'Ardi', '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `komisi_sales`
--

CREATE TABLE `komisi_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_sales` varchar(255) NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `total_penjualan` decimal(15,2) NOT NULL,
  `persen_komisi` decimal(5,2) NOT NULL,
  `total_komisi` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `komisi_sales`
--

INSERT INTO `komisi_sales` (`id`, `nama_sales`, `bulan`, `total_penjualan`, `persen_komisi`, `total_komisi`, `created_at`, `updated_at`) VALUES
(1, 'Rina', 'Juni 2025', 95000000.00, 5.00, 4750000.00, '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konsolidasi_multi_perusahaans`
--

CREATE TABLE `konsolidasi_multi_perusahaans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perusahaan` varchar(255) NOT NULL,
  `pendapatan` decimal(15,2) NOT NULL,
  `beban` decimal(15,2) NOT NULL,
  `laba` decimal(15,2) NOT NULL,
  `periode` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `konsolidasi_multi_perusahaans`
--

INSERT INTO `konsolidasi_multi_perusahaans` (`id`, `perusahaan`, `pendapatan`, `beban`, `laba`, `periode`, `created_at`, `updated_at`) VALUES
(1, 'PT Alpha', 50000000.00, 20000000.00, 30000000.00, '2025-01-31', '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(2, 'PT Beta', 75000000.00, 25000000.00, 50000000.00, '2025-02-28', '2026-07-01 21:18:28', '2026-07-01 21:18:28'),
(3, 'PT Gamma', 90000000.00, 60000000.00, 30000000.00, '2025-03-31', '2026-07-01 21:18:28', '2026-07-01 21:18:28');

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
  `perpanjangan` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kontrak_aktifs`
--

INSERT INTO `kontrak_aktifs` (`id`, `kode_kontrak`, `mitra`, `nilai`, `tgl_mulai`, `tgl_selesai`, `pic`, `status`, `perpanjangan`, `created_at`, `updated_at`) VALUES
(1, 'KTR-001', 'PT Gedung Indah', 75000000, '2024-01-01', '2026-01-01', 'Silvia', 'Aktif', 1, NULL, NULL),
(2, 'KTR-002', 'PT TextileCo', 180000000, '2025-07-01', '2025-10-01', 'Dimas', 'Draft', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `k_p_i_appraisals`
--

CREATE TABLE `k_p_i_appraisals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL,
  `periode_evaluasi` varchar(255) NOT NULL,
  `disiplin` int(11) NOT NULL,
  `kolaborasi` int(11) NOT NULL,
  `produktivitas` int(11) NOT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL,
  `evaluator` varchar(255) NOT NULL,
  `catatan` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `k_p_i_appraisals`
--

INSERT INTO `k_p_i_appraisals` (`id`, `nama_pegawai`, `periode_evaluasi`, `disiplin`, `kolaborasi`, `produktivitas`, `nilai_akhir`, `evaluator`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Dina Kartika', 'Q1 2024', 90, 85, 88, 87.60, 'Siti Nurhaliza', 'Perlu peningkatan softskill', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'Andi Wijaya', 'Q1 2024', 92, 87, 90, 89.60, 'Siti Nurhaliza', 'Sangat baik dan disiplin', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_agings`
--

CREATE TABLE `laporan_agings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `umur_hari` int(11) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `laporan_agings`
--

INSERT INTO `laporan_agings` (`id`, `sku`, `nama_barang`, `qty`, `tanggal_masuk`, `umur_hari`, `lokasi`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'SKU-A', 'Kemeja Formal', 50, '2024-01-01', 175, 'GUD-01', 'Lama disimpan', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'SKU-D', 'Rompi Outdoor', 30, '2025-06-01', 20, 'GUD-01', 'Stok baru', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_cogs`
--

CREATE TABLE `laporan_cogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `qty_terjual` int(11) NOT NULL,
  `harga_pokok` decimal(15,2) NOT NULL,
  `total_cogs` decimal(15,2) NOT NULL,
  `periode` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `laporan_cogs`
--

INSERT INTO `laporan_cogs` (`id`, `sku`, `item`, `qty_terjual`, `harga_pokok`, `total_cogs`, `periode`, `created_at`, `updated_at`) VALUES
(1, 'SKU-A', 'Kemeja Formal', 100, 50000.00, 5000000.00, 'Juni 2025', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'SKU-B', 'Celana Jeans', 200, 32000.00, 6400000.00, 'Juni 2025', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_deads`
--

CREATE TABLE `laporan_deads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `tgl_terakhir_bergerak` date NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `laporan_deads`
--

INSERT INTO `laporan_deads` (`id`, `sku`, `nama_barang`, `qty`, `tgl_terakhir_bergerak`, `lokasi`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'SKU-ZZ', 'Rompi Hijau', 25, '2023-12-01', 'SCR-01', 'Tidak bergerak 6 bulan', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, 'SKU-YY', 'Sepatu Karet', 12, '2024-01-01', 'GUD-01', 'Stok lama', '2026-07-01 21:18:35', '2026-07-01 21:18:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_movements`
--

CREATE TABLE `laporan_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `sku` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `dari` varchar(255) NOT NULL,
  `ke` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `tipe` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `laporan_movements`
--

INSERT INTO `laporan_movements` (`id`, `tanggal`, `sku`, `item`, `dari`, `ke`, `qty`, `tipe`, `pic`, `created_at`, `updated_at`) VALUES
(1, '2025-06-01', 'SKU-A', 'Kemeja Formal', 'GUD-01', 'TR-BKS', 100, 'Transfer', 'Andi HRD', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, '2025-06-02', 'SKU-B', 'Celana Jeans', 'TR-BKS', 'VIRT-01', 50, 'Transfer', 'Yulianto', '2026-07-01 21:18:35', '2026-07-01 21:18:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `lead_management`
--

CREATE TABLE `lead_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lead_sources`
--

CREATE TABLE `lead_sources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

--
-- Dumping data untuk tabel `legal_documents`
--

INSERT INTO `legal_documents` (`id`, `kode`, `nama_dokumen`, `jenis`, `pihak_terkait`, `tgl_terbit`, `berlaku_hingga`, `status`, `format`, `created_at`, `updated_at`) VALUES
(1, 'LEG001', 'Perjanjian Produksi TokoSeragam', 'Kontrak', 'TokoSeragam', '2025-07-01', '2025-09-30', 'Draft', 'PDF', NULL, NULL),
(2, 'LEG002', 'Sertifikat Hak Guna Gedung', 'Sertifikat', 'BPN', '2020-05-10', NULL, 'Valid', 'Fisik', NULL, NULL);

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

--
-- Dumping data untuk tabel `litigasis`
--

INSERT INTO `litigasis` (`id`, `kasus`, `lawan`, `jenis_kasus`, `status`, `pengacara`, `tanggal_sidang`, `created_at`, `updated_at`) VALUES
(1, 'Tanah Gudang', 'PT Properti Jaya', 'Perdata', 'Sidang ke-2', 'Kantor Hukum Maju', '2025-07-08', '2026-07-01 21:18:41', '2026-07-01 21:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `live_chats`
--

CREATE TABLE `live_chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `logisticsos`
--

CREATE TABLE `logisticsos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_do` varchar(255) NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `tujuan_pengiriman` varchar(255) NOT NULL,
  `driver` varchar(255) NOT NULL,
  `no_polisi` varchar(255) NOT NULL,
  `kendaraan` varchar(255) NOT NULL,
  `jumlah_paket` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `estimasi_tiba` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `logisticsos`
--

INSERT INTO `logisticsos` (`id`, `no_do`, `tanggal_kirim`, `tujuan_pengiriman`, `driver`, `no_polisi`, `kendaraan`, `jumlah_paket`, `status`, `estimasi_tiba`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'DO-0001', '2025-07-01', 'Toko ABC - Bandung', 'Danu', 'B 1234 ABC', 'Pick-up', 12, 'Dalam Perjalanan', '2025-07-02', 'Cek suhu kabin', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

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
  `status` enum('Aktif','Nonaktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `loyalties`
--

INSERT INTO `loyalties` (`id`, `id_program`, `nama_program`, `jenis_reward`, `akumulasi_poin`, `konversi_poin`, `periode_mulai`, `periode_akhir`, `status`, `created_at`, `updated_at`) VALUES
(1, 'LP001', 'SEKA Rewards', 'Voucher Belanja', '1 poin / Rp10.000', '100 poin = Rp10.000', '2025-01-01', '2025-12-31', 'Aktif', '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `maintenanceos`
--

CREATE TABLE `maintenanceos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_aset` varchar(255) NOT NULL,
  `nama_mesin` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `jenis_service` varchar(255) NOT NULL,
  `jadwal` date NOT NULL,
  `teknisi` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `downtime` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `maintenanceos`
--

INSERT INTO `maintenanceos` (`id`, `kode_aset`, `nama_mesin`, `lokasi`, `jenis_service`, `jadwal`, `teknisi`, `status`, `downtime`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', 'Mesin Obras #1', 'Produksi A', 'Preventive', '2025-07-10', 'Jono', 'Terjadwal', '-', 'Ganti belt utama', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `maintenances`
--

CREATE TABLE `maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `marketing_automations`
--

CREATE TABLE `marketing_automations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `material_requests`
--

CREATE TABLE `material_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mr_no` varchar(255) NOT NULL,
  `wo_no` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `bahan` varchar(255) NOT NULL,
  `qty_diminta` int(11) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Waiting',
  `disetujui_oleh` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `material_requests`
--

INSERT INTO `material_requests` (`id`, `mr_no`, `wo_no`, `tanggal`, `bahan`, `qty_diminta`, `satuan`, `status`, `disetujui_oleh`, `created_at`, `updated_at`) VALUES
(1, 'MR-001', 'WO-001', '2025-06-02', 'Kain Katun', 160, 'meter', 'Approved', 'Supervisor Produksi', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, 'MR-002', 'WO-002', '2025-06-11', 'Kain Drill', 300, 'meter', 'Waiting', '-', '2026-07-01 21:18:35', '2026-07-01 21:18:35');

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
(4, '2025_06_24_041751_create_buku_besars_table', 1),
(5, '2025_06_24_044733_create_konsolidasi_multi_perusahaans_table', 1),
(6, '2025_06_24_044843_create_budgeting_proyeks_table', 1),
(7, '2025_06_24_045606_create_e_fakturs_table', 1),
(8, '2025_06_24_045854_create_e_bupots_table', 1),
(9, '2025_06_24_050002_create_auto_reconciles_table', 1),
(10, '2025_06_24_050057_create_virtual_accounts_table', 1),
(11, '2025_06_24_050224_create_cash_flow_forecasts_table', 1),
(12, '2025_06_24_050317_create_aging_aps_table', 1),
(13, '2025_06_24_050412_create_aging_ars_table', 1),
(14, '2025_06_24_050510_create_reminder_tagihans_table', 1),
(15, '2025_06_25_032311_create_struktur_organisasis_table', 1),
(16, '2025_06_25_033452_create_departemens_table', 1),
(17, '2025_06_25_033535_create_skill_matrices_table', 1),
(18, '2025_06_25_033614_create_presensis_table', 1),
(19, '2025_06_25_033656_create_shift_lemburs_table', 1),
(20, '2025_06_25_033737_create_payrolls_table', 1),
(21, '2025_06_25_033820_create_cuti_izins_table', 1),
(22, '2025_06_25_033902_create_k_p_i_appraisals_table', 1),
(23, '2025_06_25_035916_create_resign_offboardings_table', 1),
(24, '2025_06_25_050350_create_multi_gudangs_table', 1),
(25, '2025_06_25_051405_create_pickings_table', 1),
(26, '2025_06_25_051449_create_trackings_table', 1),
(27, '2025_06_25_051537_create_stok_minimums_table', 1),
(28, '2025_06_25_051614_create_valuasi_fifos_table', 1),
(29, '2025_06_25_051651_create_laporan_agings_table', 1),
(30, '2025_06_25_051734_create_laporan_deads_table', 1),
(31, '2025_06_25_051821_create_laporan_movements_table', 1),
(32, '2025_06_25_051933_create_laporan_cogs_table', 1),
(33, '2025_06_25_083211_create_billof_materials_table', 1),
(34, '2025_06_25_083354_create_work_orders_table', 1),
(35, '2025_06_25_083427_create_work_centers_table', 1),
(36, '2025_06_25_083502_create_pelacakans_table', 1),
(37, '2025_06_25_083540_create_maintenances_table', 1),
(38, '2025_06_25_083634_create_material_requests_table', 1),
(39, '2025_06_25_084800_create_cost_estimations_table', 1),
(40, '2025_06_25_093529_create_requestfor_quotations_table', 1),
(41, '2025_06_25_093621_create_purchase_orders_table', 1),
(42, '2025_06_25_093710_create_vendor_pricelists_table', 1),
(43, '2025_06_25_093805_create_approval_workflows_table', 1),
(44, '2025_06_25_093844_create_dropshippings_table', 1),
(45, '2025_06_25_094149_create_vendor_performances_table', 1),
(46, '2025_06_26_035817_create_kampanyes_table', 1),
(47, '2025_06_26_041339_create_otomatisasis_table', 1),
(48, '2025_06_26_041634_create_segmentasis_table', 1),
(49, '2025_06_26_041737_create_loyalties_table', 1),
(50, '2025_06_26_041827_create_afiliasis_table', 1),
(51, '2025_06_26_041915_create_sosmedps_table', 1),
(52, '2025_06_26_042002_create_trackingutms_table', 1),
(53, '2025_06_26_061036_create_ads_integrations_table', 1),
(54, '2025_06_26_065543_create_crm_prospeks_table', 1),
(55, '2025_06_26_065711_create_penawarans_table', 1),
(56, '2025_06_26_065819_create_sales_orders_table', 1),
(57, '2025_06_26_065931_create_pricelist_diskons_table', 1),
(58, '2025_06_26_070032_create_target_penjualans_table', 1),
(59, '2025_06_26_070143_create_komisi_sales_table', 1),
(60, '2025_06_26_070248_create_retur_penjualans_table', 1),
(61, '2025_06_26_070404_create_signature_dokumens_table', 1),
(62, '2025_07_01_051753_create_stockos_table', 1),
(63, '2025_07_01_051840_create_work_orderos_table', 1),
(64, '2025_07_01_051933_create_supplieros_table', 1),
(65, '2025_07_01_052004_create_procurementos_table', 1),
(66, '2025_07_01_052033_create_purchaseros_table', 1),
(67, '2025_07_01_052101_create_vendoreos_table', 1),
(68, '2025_07_01_052130_create_stock_movementos_table', 1),
(69, '2025_07_01_052158_create_logisticsos_table', 1),
(70, '2025_07_01_052226_create_maintenanceos_table', 1),
(71, '2025_07_01_052324_create_s_o_p_managementos_table', 1),
(72, '2025_07_01_052352_create_h_s_eos_table', 1),
(73, '2025_07_01_052424_create_waste_managementos_table', 1),
(74, '2025_07_01_084335_create_operations_dashboards_table', 1),
(75, '2025_07_01_091356_create_itasset_management_table', 1),
(76, '2025_07_01_091438_create_software_licenses_table', 1),
(77, '2025_07_01_091527_create_helpdesk_supports_table', 1),
(78, '2025_07_01_091609_create_user_accesses_table', 1),
(79, '2025_07_01_091650_create_network_monitorings_table', 1),
(80, '2025_07_01_091727_create_cybersecurities_table', 1),
(81, '2025_07_01_091808_create_email_domains_table', 1),
(82, '2025_07_01_091849_create_server_clouds_table', 1),
(83, '2025_07_01_091928_create_system_backups_table', 1),
(84, '2025_07_01_092015_create_project_management_table', 1),
(85, '2025_07_01_092100_create_devops_table', 1),
(86, '2025_07_01_092621_create_policy_compliances_table', 1),
(87, '2025_07_02_041228_create_induk_proyeks_table', 1),
(88, '2025_07_02_042411_create_project_plannings_table', 1),
(89, '2025_07_02_042451_create_project_costs_table', 1),
(90, '2025_07_02_042531_create_pembelian_proyeks_table', 1),
(91, '2025_07_02_042613_create_project_timelines_table', 1),
(92, '2025_07_02_042654_create_project_risks_table', 1),
(93, '2025_07_02_042736_create_dokumen_proyeks_table', 1),
(94, '2025_07_02_043926_create_legal_documents_table', 1),
(95, '2025_07_02_044003_create_review_legals_table', 1),
(96, '2025_07_02_044044_create_kontrak_aktifs_table', 1),
(97, '2025_07_02_044119_create_litigasis_table', 1),
(98, '2025_07_02_044156_create_sertifikasi_perizinans_table', 1),
(99, '2025_07_02_044234_create_hak_hukums_table', 1),
(100, '2025_07_02_044423_create_daftar_notaris_table', 1),
(101, '2025_07_02_053400_create_induk_assets_table', 1),
(102, '2025_07_02_053432_create_pergerakan_assets_table', 1),
(103, '2025_07_02_053500_create_pemeliharaan_assets_table', 1),
(104, '2025_07_02_053528_create_penyusutan_assets_table', 1),
(105, '2025_07_02_053557_create_perolehan_assets_table', 1),
(106, '2025_07_02_053625_create_asset_dihapuskans_table', 1),
(107, '2025_07_02_053654_create_dokumentasi_assets_table', 1),
(108, '2025_07_02_053722_create_penanggung_jawabs_table', 1),
(109, '2025_07_02_054051_create_audit_assets_table', 1),
(110, '2025_07_03_041127_create_lead_management_table', 1),
(111, '2025_07_03_042234_create_pipelines_table', 1),
(112, '2025_07_03_042309_create_activities_table', 1),
(113, '2025_07_03_042344_create_customers_table', 1),
(114, '2025_07_03_042419_create_quotations_table', 1),
(115, '2025_07_03_042446_create_marketing_automations_table', 1),
(116, '2025_07_03_042514_create_helpdesk_ticketings_table', 1),
(117, '2025_07_03_042543_create_live_chats_table', 1),
(118, '2025_07_03_042611_create_contact_management_table', 1),
(119, '2025_07_03_042638_create_route_plans_table', 1),
(120, '2025_07_03_042706_create_crm_dashboards_table', 1),
(121, '2025_07_03_043912_create_partner_programs_table', 1),
(122, '2025_07_03_043942_create_appointments_table', 1),
(123, '2025_07_03_044012_create_team_management_table', 1),
(124, '2025_07_03_044039_create_customer_ratings_table', 1),
(125, '2025_07_03_044107_create_contract_handlings_table', 1),
(126, '2025_07_03_044134_create_crm_notifications_table', 1),
(127, '2025_07_03_044202_create_api_integrations_table', 1),
(128, '2025_07_03_044229_create_crm_permissions_table', 1),
(129, '2025_07_03_044256_create_lead_sources_table', 1),
(130, '2025_08_06_100414_create_data_stnks_table', 1),
(131, '2025_08_06_101407_create_data_kirs_table', 1),
(132, '2025_08_06_101447_create_data_members_table', 1),
(133, '2025_08_06_101525_create_penggunaan_asuransis_table', 1),
(134, '2025_08_06_101603_create_penggunaan_g_p_s_table', 1),
(135, '2025_08_06_101636_create_data_services_table', 1),
(136, '2025_08_23_035410_create_reservasis_table', 1),
(137, '2025_08_23_055843_create_files_table', 1),
(138, '2025_09_06_040038_create_admins_table', 1),
(139, '2025_09_15_045751_create_invoices_table', 1),
(140, '2025_09_16_082145_create_invoice_periodes_table', 1),
(141, '2025_09_16_092647_create_invoice_remaks_table', 1),
(142, '2025_09_19_054128_create_invoice_payments_table', 1),
(143, '2025_09_20_035538_create_inv_penawarans_table', 1),
(144, '2025_09_20_035540_create_inv_kontraks_table', 1),
(145, '2025_09_20_040729_create_inv_penawaran_items_table', 1),
(146, '2025_09_20_065811_create_inv_summaries_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `multi_gudangs`
--

CREATE TABLE `multi_gudangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_gudang` varchar(255) NOT NULL,
  `tipe_lokasi` varchar(255) NOT NULL,
  `kode_lokasi` varchar(255) NOT NULL,
  `kapasitas_maks` varchar(255) NOT NULL,
  `alamat_gudang` varchar(255) NOT NULL,
  `status` enum('Aktif','Non-Aktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `multi_gudangs`
--

INSERT INTO `multi_gudangs` (`id`, `nama_gudang`, `tipe_lokasi`, `kode_lokasi`, `kapasitas_maks`, `alamat_gudang`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Gudang Utama', 'Stok Real', 'GUD-01', '10.000 unit', 'Jl. Industri No. 12', 'Aktif', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'Transit Bekasi', 'Transit', 'TR-BKS', '2.000 unit', 'Jl. Raya Bekasi KM 15', 'Aktif', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

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
(1, 'Kantor Pusat', '103.100.x', 'Aktif', '100 Mbps', '0 jam', 'Stabil', '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'Gudang A', '103.110.x', 'Tidak Stabil', '50 Mbps', '3 jam', 'Cek kabel UTP', '2026-07-01 21:18:40', '2026-07-01 21:18:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `operations_dashboards`
--

CREATE TABLE `operations_dashboards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `indikator` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `realisasi` varchar(255) NOT NULL,
  `pencapaian` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `periode` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `operations_dashboards`
--

INSERT INTO `operations_dashboards` (`id`, `indikator`, `target`, `realisasi`, `pencapaian`, `status`, `periode`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Output Produksi (pcs)', '5.000', '4.500', '90%', 'Kurang', '01/07/25', 'Ada mesin downtime', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, 'Waktu Pengiriman Rata-Rata', '≤24 jam', '27 jam', '-', 'Evaluasi', 'Juni 2025', 'Rute logistik padat', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

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
  `status` varchar(255) NOT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `otomatisasis`
--

INSERT INTO `otomatisasis` (`id`, `workflow_id`, `nama_workflow`, `trigger_event`, `syarat_tambahan`, `aksi`, `delay_aksi`, `status`, `pic`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AUTO001', 'Follow-Up Keranjang Kosong', 'Keranjang ditinggal > 1 hari', 'Total nilai > 100.000', 'Kirim Email Reminder', '24 jam', 'Aktif', 'Rina', '', '2026-07-01 21:18:37', '2026-07-01 21:18:37'),
(2, 'AUTO002', 'Ulang Tahun Pelanggan', 'Hari ulang tahun pelanggan', 'Pelanggan aktif > 3 bulan', 'Kirim SMS & Kupon Diskon', 'Langsung', 'Aktif', 'Andi', '', '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `partner_programs`
--

CREATE TABLE `partner_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `slip_gaji` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `payrolls`
--

INSERT INTO `payrolls` (`id`, `nama_pegawai`, `gaji_pokok`, `tunjangan`, `thr`, `bpjs`, `pph21`, `total_gaji`, `slip_gaji`, `created_at`, `updated_at`) VALUES
(1, 'Rudi Hartono', 5000000.00, 750000.00, 400000.00, 200000.00, 150000.00, 5800000.00, 'Sudah PDF', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelacakans`
--

CREATE TABLE `pelacakans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produk` varchar(255) NOT NULL,
  `tipe_tracking` varchar(255) NOT NULL,
  `nomor_lot_sn` varchar(255) NOT NULL,
  `tgl_produksi` date NOT NULL,
  `work_order` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `lokasi_saat_ini` varchar(255) NOT NULL,
  `batch` varchar(255) NOT NULL,
  `tgl_expired` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pelacakans`
--

INSERT INTO `pelacakans` (`id`, `produk`, `tipe_tracking`, `nomor_lot_sn`, `tgl_produksi`, `work_order`, `status`, `lokasi_saat_ini`, `batch`, `tgl_expired`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Kemeja Pria Formal', 'Lot', 'LOT-0001-KEM', '2025-06-04', 'WO-001', 'Selesai', 'Gudang Jadi', 'Batch-1', NULL, 'Siap kirim toko A', NULL, NULL),
(2, 'Celana Slimfit', 'SN', 'SN-CEL1254', '2025-06-15', 'WO-002', 'Dalam QC', 'Area QC', 'Batch-2', NULL, 'Akan rework', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembelian_proyeks`
--

CREATE TABLE `pembelian_proyeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pr_no` varchar(255) NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `item_diminta` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `estimasi_harga` bigint(20) NOT NULL,
  `status` varchar(255) NOT NULL,
  `tgl_permintaan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembelian_proyeks`
--

INSERT INTO `pembelian_proyeks` (`id`, `pr_no`, `proyek`, `item_diminta`, `qty`, `vendor`, `estimasi_harga`, `status`, `tgl_permintaan`, `created_at`, `updated_at`) VALUES
(1, 'PR-001', 'PRJ001', 'Semen 40kg', 200, 'PT Semen Maju', 8000000, 'Disetujui', '2025-06-28', '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'PR-002', 'PRJ001', 'Bata Merah', 3000, 'CV Batu Hebat', 6000000, 'Pending', '2025-06-30', '2026-07-01 21:18:40', '2026-07-01 21:18:40');

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

--
-- Dumping data untuk tabel `pemeliharaan_assets`
--

INSERT INTO `pemeliharaan_assets` (`id`, `kode_aset`, `tanggal_service`, `jenis_service`, `vendor_pic`, `biaya`, `status`, `jadwal_selanjutnya`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AST-002', '2025-03-20', 'Servis Ringan', 'Teknisi Internal', 250000.00, 'Selesai', '2025-09-20', 'Ganti jarum & motor', NULL, NULL),
(2, 'AST-001', '2025-07-01', 'Cek hardware', 'CV TechSolution', 0.00, 'Selesai', '2026-01-01', 'Free support garansi', NULL, NULL);

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

--
-- Dumping data untuk tabel `penanggung_jawabs`
--

INSERT INTO `penanggung_jawabs` (`id`, `kode_aset`, `nama_aset`, `pic`, `tanggal_penempatan`, `divisi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', 'Laptop Dell XPS 13', 'Dimas Saputra', '2024-01-10', 'IT', 'Aktif', NULL, NULL),
(2, 'AST-002', 'Mesin Jahit Juki', 'Wawan Kurnia', '2023-02-01', 'Produksi', 'Aktif', NULL, NULL);

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
(1, 'QT20250601-01', '2025-06-01', 'PT ABC Jaya', 'Jaket Hoodie', 100, 125000.00, 12500000.00, 'Dikirim', '2025-06-10', '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penggunaan_asuransis`
--

CREATE TABLE `penggunaan_asuransis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_polisi` varchar(255) NOT NULL,
  `merk_type` varchar(255) NOT NULL,
  `nama_asuransi` varchar(255) NOT NULL,
  `jenis_asuransi` varchar(255) NOT NULL,
  `status_kendaraan` varchar(255) NOT NULL,
  `tgl_berlaku` date NOT NULL,
  `tgl_berakhir` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penggunaan_asuransis`
--

INSERT INTO `penggunaan_asuransis` (`id`, `no_polisi`, `merk_type`, `nama_asuransi`, `jenis_asuransi`, `status_kendaraan`, `tgl_berlaku`, `tgl_berakhir`, `created_at`, `updated_at`) VALUES
(1, 'B 1227 UJT', 'Toyota Fortuner 2.4 VRZ A/T', 'Takaful Ulum', 'Comprehensive', 'Commercial', '2022-09-06', '2023-09-06', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(2, 'B 9354 SRU', 'HINO DUTRO', 'Zurich Insurance', 'Comprehensive', 'Commercial', '2022-08-09', '2023-08-09', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(3, 'B 9883 PXR', 'MITSUBISHI/COLT DIESEL', 'ETIQA Insurance', 'Comprehensive', 'Commercial', '2022-12-01', '2023-12-01', '2026-07-01 21:18:42', '2026-07-01 21:18:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penggunaan_g_p_s`
--

CREATE TABLE `penggunaan_g_p_s` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_polisi` varchar(255) NOT NULL,
  `id_gps` varchar(255) NOT NULL,
  `type_gps` varchar(255) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `status_gps` varchar(255) NOT NULL,
  `masa_berlaku` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penggunaan_g_p_s`
--

INSERT INTO `penggunaan_g_p_s` (`id`, `no_polisi`, `id_gps`, `type_gps`, `nama_perusahaan`, `status_gps`, `masa_berlaku`, `created_at`, `updated_at`) VALUES
(1, 'B 2629 SOL', '08113168269', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2018-02-21', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(2, 'B 9353 SCB', '00000', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2018-01-17', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(3, 'B 9354 SCB', '000000', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2018-01-17', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(4, 'B 9349 SCB', '00000', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2018-02-17', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(5, 'B 1186 SYD', '08118595378', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2017-11-22', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(6, 'B 2875 SKR', '08119399284', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2017-06-17', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(7, 'B 9518 TF', '08111568580', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2017-08-09', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(8, 'B 9538 TF', '08111568759', 'E02', 'ARMY TRACKING INDONESIA', 'Aktif', '2017-09-09', '2026-07-01 21:18:42', '2026-07-01 21:18:42');

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

--
-- Dumping data untuk tabel `penyusutan_assets`
--

INSERT INTO `penyusutan_assets` (`id`, `kode_aset`, `tahun`, `nilai_awal`, `akumulasi_penyusutan`, `nilai_buku`, `metode`, `status`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', '2025', 18500000.00, 9250000.00, 9250000.00, 'Garis Lurus', 'Aktif', NULL, NULL),
(2, 'AST-002', '2025', 12000000.00, 4000000.00, 8000000.00, 'Garis Lurus', 'Aktif', NULL, NULL);

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

--
-- Dumping data untuk tabel `pergerakan_assets`
--

INSERT INTO `pergerakan_assets` (`id`, `kode_aset`, `tanggal`, `jenis_pergerakan`, `dari_lokasi`, `ke_lokasi`, `dilakukan_oleh`, `disetujui_oleh`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', '2024-01-05', 'Mutasi Lokasi', 'Gudang IT', 'Kantor Pusat', 'Admin IT', 'Kepala GA', 'Untuk user baru', NULL, NULL),
(2, 'AST-002', '2024-06-10', 'Pemindahan Produksi', 'Cabang A', 'Workshop Bekasi', 'Teknisi A', 'Kepala Produksi', 'Pemusatan mesin utama', NULL, NULL);

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

--
-- Dumping data untuk tabel `perolehan_assets`
--

INSERT INTO `perolehan_assets` (`id`, `tanggal_perolehan`, `kode_aset`, `nama_aset`, `vendor`, `metode_pembelian`, `harga`, `status`, `pembayaran`, `created_at`, `updated_at`) VALUES
(1, '2023-06-15', 'AST-001', 'Laptop Dell XPS 13', 'PT Computindo', 'Pembelian Tunai', 18500000.00, 'Lunas', 'Transfer', NULL, NULL),
(2, '2022-07-20', 'AST-002', 'Mesin Jahit Juki', 'Toko Mesin Cipta', 'Kredit 3x', 12000000.00, 'Cicil', '2x tersisa', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pickings`
--

CREATE TABLE `pickings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_picking` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `gudang_asal` varchar(255) NOT NULL,
  `lokasi_tujuan` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `status` enum('Selesai','Dalam Proses') NOT NULL,
  `rute_otomatis` enum('Ya','Tidak') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pickings`
--

INSERT INTO `pickings` (`id`, `id_picking`, `tanggal`, `gudang_asal`, `lokasi_tujuan`, `item`, `jumlah`, `status`, `rute_otomatis`, `created_at`, `updated_at`) VALUES
(1, 'PKG-0001', '2025-06-20', 'GUD-01', 'TR-BKS', 'SKU-A', 100, 'Selesai', 'Ya', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, 'PKG-0002', '2025-06-21', 'GUD-01', 'VIRT-01', 'SKU-B', 200, 'Dalam Proses', 'Tidak', '2026-07-01 21:18:35', '2026-07-01 21:18:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pipelines`
--

CREATE TABLE `pipelines` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
(1, 'Kebijakan Penggunaan IT', '1.3', '2025-01-01', 'Tim IT', 'Aktif', 'ISO 27001', NULL, NULL),
(2, 'SOP Akses Data Sensitif', '2.0', '2025-06-01', 'IT Security', 'Aktif', 'GDPR Compliance Checklist', NULL, NULL);

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
(1, 'Siti Aminah', '2026-06-27', '08:00:00', '17:00:00', 'Manual', 'Medan', 'Hadir', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'Dewi Lestari', '2026-06-30', '08:00:00', '17:00:00', 'Fingerprint', 'Bandung', 'Hadir', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(3, 'Andi Wijaya', '2026-07-02', '08:00:00', '17:00:00', 'Fingerprint', 'Bandung', 'Alpa', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(4, 'Dewi Lestari', '2026-06-27', '08:00:00', '17:00:00', 'GPS', 'Jakarta', 'Alpa', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(5, 'Andi Wijaya', '2026-06-24', '08:00:00', '17:00:00', 'Manual', 'Medan', 'Izin', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(6, 'Siti Aminah', '2026-06-27', '08:00:00', '17:00:00', 'GPS', 'Surabaya', 'Izin', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(7, 'Siti Aminah', '2026-06-27', '08:00:00', '17:00:00', 'Manual', 'Surabaya', 'Alpa', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(8, 'Budi Santoso', '2026-07-01', '08:00:00', '17:00:00', 'Manual', 'Medan', 'Terlambat', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(9, 'Budi Santoso', '2026-06-30', '08:00:00', '17:00:00', 'GPS', 'Jakarta', 'Terlambat', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(10, 'Budi Santoso', '2026-06-22', '08:00:00', '17:00:00', 'GPS', 'Medan', 'Izin', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pricelist_diskons`
--

INSERT INTO `pricelist_diskons` (`id`, `id_harga`, `nama_produk`, `level_pelanggan`, `harga_normal`, `diskon`, `harga_diskon`, `periode_mulai`, `periode_selesai`, `created_at`, `updated_at`) VALUES
(1, 'PL001', 'Kaos Polos', 'Reseller', 50000.00, 10.00, 45000.00, '2025-06-01', '2025-12-31', '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `procurementos`
--

CREATE TABLE `procurementos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_id` varchar(255) NOT NULL,
  `nama_workflow` varchar(255) NOT NULL,
  `trigger_event` varchar(255) NOT NULL,
  `syarat_tambahan` varchar(255) DEFAULT NULL,
  `aksi_dilakukan` varchar(255) NOT NULL,
  `delay_aksi` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `procurementos`
--

INSERT INTO `procurementos` (`id`, `workflow_id`, `nama_workflow`, `trigger_event`, `syarat_tambahan`, `aksi_dilakukan`, `delay_aksi`, `status`, `pic`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'AUTO001', 'Follow-Up Keranjang Kosong', 'Keranjang ditinggal > 1 hari', 'Total nilai > 100.000', 'Kirim Email Reminder', '24 jam', 'Aktif', 'Rina', '', NULL, NULL),
(2, 'AUTO002', 'Ulang Tahun Pelanggan', 'Hari ulang tahun pelanggan', 'Pelanggan aktif > 3 bulan', 'Kirim SMS & Kupon Diskon', 'Langsung', 'Aktif', 'Andi', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_costs`
--

CREATE TABLE `project_costs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `kategori_biaya` varchar(255) NOT NULL,
  `estimasi` decimal(15,2) NOT NULL,
  `realisasi` decimal(15,2) NOT NULL,
  `selisih` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_costs`
--

INSERT INTO `project_costs` (`id`, `proyek`, `kategori_biaya`, `estimasi`, `realisasi`, `selisih`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Material Semen', 20000000.00, 18500000.00, -1500000.00, 'Efisien', '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'PRJ001', 'Upah Harian', 80000000.00, 92000000.00, 12000000.00, 'Over Budget', '2026-07-01 21:18:40', '2026-07-01 21:18:40');

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
  `progres` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_management`
--

INSERT INTO `project_management` (`id`, `nama_proyek`, `pic_proyek`, `tujuan`, `estimasi_waktu`, `status`, `progres`, `created_at`, `updated_at`) VALUES
(1, 'Implementasi ERP', 'Pak Tono', 'Integrasi semua modul ERP', '6 bulan', 'Dalam Proses', 65, NULL, NULL),
(2, 'Migrasi Email ke Cloud', 'Dian', 'Pindah dari cPanel ke GWS', '1 bulan', 'Selesai', 100, NULL, NULL);

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
  `durasi` int(11) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_plannings`
--

INSERT INTO `project_plannings` (`id`, `kode_proyek`, `tahapan`, `tgl_mulai`, `tgl_selesai`, `durasi`, `pic`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Survey & Desain', '2025-07-01', '2025-07-05', 5, 'Tim GA', 'Selesai', '2026-07-01 21:18:40', '2026-07-01 21:18:40');

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
  `mitigasi` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_risks`
--

INSERT INTO `project_risks` (`id`, `proyek`, `risiko`, `dampak`, `kemungkinan`, `mitigasi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Cuaca ekstrim hujan', 'Sedang', 'Tinggi', 'Tambah terpal & pompa', 'Terkendali', NULL, NULL),
(2, 'PRJ002', 'Vendor terlambat kirim', 'Tinggi', 'Menengah', 'SPK denda keterlambatan', 'Diajukan', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_timelines`
--

CREATE TABLE `project_timelines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proyek` varchar(255) NOT NULL,
  `kegiatan` varchar(255) NOT NULL,
  `deadline` date NOT NULL,
  `reminder` tinyint(1) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `project_timelines`
--

INSERT INTO `project_timelines` (`id`, `proyek`, `kegiatan`, `deadline`, `reminder`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRJ001', 'Pasang Rangka Baja', '2025-07-18', 1, 'Scheduled', '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'PRJ002', 'Produksi Batch 1', '2025-07-20', 1, 'Menunggu', '2026-07-01 21:18:40', '2026-07-01 21:18:40');

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
(1, 'PR-001', '2025-06-25', 'Produksi', 'Rina', 'Label Baju', 'BRG-003', 500, 'pcs', 'Stok Habis', 'Disetujui', 'Manajer Produksi', '2025-06-26', '-', '2026-07-01 21:18:38', '2026-07-01 21:18:38'),
(2, 'PR-002', '2025-06-26', 'Gudang', 'Dodi', 'Barcode Scanner', 'BRG-007', 1, 'unit', 'Scanner rusak total', 'Pending', NULL, NULL, 'Urgent', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

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
  `total_barang` varchar(255) NOT NULL,
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
(1, 'PO-001', '2025-06-03', 'PT. Sumber Jaya', 'RFQ-001', '100 roll', 15000000, 'Dalam Pengiriman', '2025-06-05', NULL, 'Pembayaran tempo 30 hari', '2026-07-01 21:18:36', '2026-07-01 21:18:36'),
(2, 'PO-002', '2025-06-04', 'CV. Textile Makmur', 'RFQ-002', '50 meter', 4000000, 'Dikirim', '2025-06-06', NULL, NULL, '2026-07-01 21:18:36', '2026-07-01 21:18:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reminder_tagihans`
--

CREATE TABLE `reminder_tagihans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer` varchar(255) NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `reminder_via` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `reminder_tagihans`
--

INSERT INTO `reminder_tagihans` (`id`, `customer`, `invoice`, `jatuh_tempo`, `reminder_via`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PT XYZ', 'INV-002', '2025-04-10', 'Email + WA', 'Terkirim', '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(2, 'PT Sukses Selalu', 'INV-001', '2026-07-09', 'Email', 'Belum Bayar', '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(3, 'CV Maju Mundur', 'INV-002', '2026-07-12', 'WhatsApp', 'Sudah Bayar', '2026-07-01 21:18:29', '2026-07-01 21:18:29');

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
(1, 'RFQ-001', '2025-06-01', 'PT. Sumber Jaya', 'BRG-001', 'Bahan Katun 20s', 100, 'roll', 150000, '2025-06-05', 'Dikirim ke Vendor', 'Butuh cepat', '2026-07-01 21:18:36', '2026-07-01 21:18:36'),
(2, 'RFQ-002', '2025-06-02', 'CV. Textile Makmur', 'BRG-002', 'Furing D300', 50, 'meter', 80000, '2025-06-06', 'Dalam Proses', '-', '2026-07-01 21:18:36', '2026-07-01 21:18:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservasis`
--

CREATE TABLE `reservasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merk_mobil` varchar(255) DEFAULT NULL,
  `tipe` varchar(255) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `no_pol` varchar(255) DEFAULT NULL,
  `nama_customer` varchar(255) DEFAULT NULL,
  `checkin` date DEFAULT NULL,
  `checkout` date DEFAULT NULL,
  `tipe_sewa` enum('harian','bulanan','tahunan') DEFAULT 'harian',
  `quotation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `reservasis`
--

INSERT INTO `reservasis` (`id`, `merk_mobil`, `tipe`, `tahun`, `no_pol`, `nama_customer`, `checkin`, `checkout`, `tipe_sewa`, `quotation`, `created_at`, `updated_at`) VALUES
(1, 'Avanza', '1.5 E M/T', '2021', 'B 1080 DFH', 'John Doe', '2025-08-01', '2025-08-05', 'harian', 'harian', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(2, 'Avanza', '1.5 E M/T', '2022', 'B 1226 DFK', 'Jane Smith', '2025-08-02', '2025-08-06', 'bulanan', 'bulanan', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(3, 'Avanza', '1.5 G A/T', '2022', 'B 1231 DKC', 'Michael Lee', '2025-08-03', '2025-08-07', 'harian', 'harian', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(4, 'Avanza', '1.5 G A/T', '2023', 'B 1203 DKV', 'Alice Wong', '2025-08-04', '2025-08-08', 'tahunan', 'tahunan', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(5, 'Avanza', '1.5 G A/T', '2023', 'B 1208 DKV', 'David Tan', '2025-08-05', '2025-08-09', 'bulanan', 'bulanan', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(6, 'Avanza', '1.5 G A/T', '2023', 'B 1741 DOG', 'Siti Nur', '2025-08-06', '2025-08-10', 'harian', 'harian', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(7, 'Avanza', '1.5 G A/T', '2023', 'B 1614 DOD', 'Budi Santoso', '2025-08-07', '2025-08-11', 'bulanan', 'bulanan', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(8, 'Avanza', '1.5 G A/T', '2022', 'B 1066 RKW', 'Lina Marlina', '2025-08-08', '2025-08-12', 'harian', 'harian', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(9, 'Avanza', '1.5 G A/T', '2022', 'B 1837 HKD', 'Rizky Adam', '2025-08-09', '2025-08-13', 'bulanan', 'bulanan', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(10, 'Calya', '1.2 G A/T', '2021', 'B 2925 SRR', 'Tina April', '2025-08-10', '2025-08-14', 'harian', 'harian', '2026-07-01 21:18:42', '2026-07-01 21:18:42'),
(11, 'Innova Zenix', '2.0 HV A/T', '2024', 'B 1855 DOX', 'Ahmad Fauzi', '2025-08-11', '2025-08-15', 'tahunan', 'tahunan', '2026-07-01 21:18:42', '2026-07-01 21:18:42');

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
  `keterangan` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `resign_offboardings`
--

INSERT INTO `resign_offboardings` (`id`, `nama_pegawai`, `jabatan_terakhir`, `tanggal_resign`, `alasan`, `status_offboarding`, `serah_terima`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Ahmad Fikri', 'Staff Gudang', '2024-06-30', 'Pindah kerja', 'Selesai', 'Sudah', 'Disetujui HRD', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'Rina Octavia', 'Admin Produksi', '2024-07-15', 'Mengurus keluarga', 'Dalam Proses', 'Belum', 'Akan dijadwalkan', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

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
(1, 'RT20250603-01', '2025-06-03', 'SO20250602-02', 'CV Makmur Abadi', 'Kaos Polos', 10, 'Cacat Jahitan', 437500.00, 'Disetujui', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

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

--
-- Dumping data untuk tabel `review_legals`
--

INSERT INTO `review_legals` (`id`, `tanggal`, `pemohon`, `dokumen`, `status_review`, `pic_legal`, `catatan`, `created_at`, `updated_at`) VALUES
(1, '2025-06-30', 'Rina', 'Draft Kontrak Vendor', 'Revisi minor', 'Silvia', 'Cek pasal penalti', NULL, NULL),
(2, '2025-07-01', 'Ivan', 'MoU Vendor Mesin', 'Approved', 'Dimas', 'Sudah dikirim revisi', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `route_plans`
--

CREATE TABLE `route_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
(1, 'SO20250602-02', '2025-06-02', 'CV Makmur Abadi', 'Kaos Polos', 200, 8750000.00, 'Proses', 'Transfer Bank', 'Ardi', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

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
(1, 'SEG001', 'Milenial Jakarta', 'Umur 20–35, domisili Jakarta, transaksi >3x', 520, 'Promosi bundle fashion', 'Aktif', NULL, NULL),
(2, 'SEG002', 'Pelanggan Inaktif', 'Tidak transaksi > 6 bulan', 800, 'Re-aktivasi', 'Aktif', NULL, NULL);

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

--
-- Dumping data untuk tabel `sertifikasi_perizinans`
--

INSERT INTO `sertifikasi_perizinans` (`id`, `jenis`, `nomor`, `instansi`, `berlaku_hingga`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ISO 9001:2015', 'ISO9001-2401', 'Sucofindo', '2027-02-20', 'Aktif', '2026-07-01 21:18:41', '2026-07-01 21:18:41'),
(2, 'NIB', '09876543211234', 'OSS Indonesia', 'Tidak Kadaluarsa', 'Aktif', '2026-07-01 21:18:41', '2026-07-01 21:18:41');

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
  `backup_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `server_clouds`
--

INSERT INTO `server_clouds` (`id`, `nama_server`, `jenis_server`, `lokasi`, `os`, `provider_cloud`, `status`, `backup_aktif`, `created_at`, `updated_at`) VALUES
(1, 'ERP-PROD-SRV01', 'Virtual', 'AWS', 'Ubuntu 22.04', 'AWS EC2', 'Aktif', 1, '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'DB-LOCAL-GUDANG', 'Fisik', 'Kantor Gudang', 'Windows Server', '-', 'Aktif', 0, '2026-07-01 21:18:40', '2026-07-01 21:18:40');

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
('A8BMkFgWAl2VLnVXXnrVAPus0Zp46qcpPoftTydH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSGRnTDZ3VDdKekRGbllGSGo4WWhYaXltTzJqcXhoV1phTG9IaWoxOSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvdXNlci9idWt1YmVzYXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1783002598),
('W8d19b5UPncAVSzrxoMOTfohQ5XE0Rf9HvSpCa3U', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib3BkbERnWXpvT2J6UFZhUGUxNjM3azFaSTVYeXcwenE3MzdqeTd2QiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91c2VyL3N0cnVrdHVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1782967911);

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
(1, 'Budi Santoso', 'Shift Pagi', '08:00:00', '17:00:00', '2 jam', '11 jam', 'Lembur Proyek A', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'Andi Wijaya', 'Shift Malam', '22:00:00', '06:00:00', '0 jam', '8 jam', 'Shift rutin', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `signature_dokumens`
--

INSERT INTO `signature_dokumens` (`id`, `document_id`, `jenis_dokumen`, `tanggal`, `pihak_terlibat`, `status_ttd`, `platform_digisign`, `created_at`, `updated_at`) VALUES
(1, 'DOC20250604-01', 'Penawaran QT20250601-01', '2025-06-01', 'PT ABC Jaya', 'Sudah Ditandatangani', 'PrivyID', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

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
(1, 'Andi Wijaya', 'Interview Kandidat', 5, 'Y', 'Siti Nurhaliza', '2024-01-01', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(2, 'Dina Kartika', 'Public Speaking', 4, 'T', 'Siti Nurhaliza', '2024-02-01', '2026-07-01 21:18:33', '2026-07-01 21:18:33');

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
(1, 'Microsoft Office 365', 'Per Seat', 30, 'Microsoft', '2025-08-01', 'Aktif', '2025-07-15', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, 'Adobe Creative Cloud', 'Subscription', 5, 'Adobe', '2024-11-10', 'Akan Habis', NULL, '2026-07-01 21:18:39', '2026-07-01 21:18:39');

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
  `klik` int(11) NOT NULL,
  `konversi` int(11) NOT NULL,
  `total_biaya` decimal(15,2) NOT NULL,
  `total_penjualan` decimal(15,2) NOT NULL,
  `roi` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sosmedps`
--

INSERT INTO `sosmedps` (`id`, `id_kampanye`, `channel`, `utm_source`, `utm_campaign`, `klik`, `konversi`, `total_biaya`, `total_penjualan`, `roi`, `created_at`, `updated_at`) VALUES
(1, 'MKT001', 'Email', 'newsletter', 'newyear2025promo', 1200, 150, 500000.00, 15000000.00, 2900.00, '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stockos`
--

CREATE TABLE `stockos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `gudang_lokasi` varchar(255) NOT NULL,
  `rak` varchar(255) NOT NULL,
  `stok` int(11) NOT NULL,
  `minimum` int(11) NOT NULL,
  `maksimum` int(11) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `terakhir_update` date NOT NULL,
  `safety_stock` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stockos`
--

INSERT INTO `stockos` (`id`, `kode_barang`, `nama_barang`, `kategori`, `gudang_lokasi`, `rak`, `stok`, `minimum`, `maksimum`, `satuan`, `status`, `terakhir_update`, `safety_stock`, `created_at`, `updated_at`) VALUES
(1, 'BRG-001', 'Kain Katun 40s', 'Bahan', 'Gudang A', 'A1', 1200, 500, 5000, 'meter', 'Aman', '2025-07-01', 1, NULL, NULL),
(2, 'BRG-002', 'Kancing Metal', 'Aksesori', 'Gudang B', 'B3', 200, 1000, 2000, 'pcs', 'Kritis', '2025-06-30', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_movementos`
--

CREATE TABLE `stock_movementos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_transaksi` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jenis_pergerakan` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `dari` varchar(255) NOT NULL,
  `ke` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `barcode_qr` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock_movementos`
--

INSERT INTO `stock_movementos` (`id`, `no_transaksi`, `tanggal`, `kode_barang`, `nama_barang`, `jenis_pergerakan`, `qty`, `dari`, `ke`, `pic`, `barcode_qr`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'SM-2025-001', '2025-07-01', 'BRG-003', 'Resleting 25cm', 'Masuk', 500, 'Supplier', 'Gudang A', 'Nani', 'RSZ2501', 'Cek kondisi sebelum simpan', '2026-07-01 21:18:38', '2026-07-01 21:18:38'),
(2, 'SM-2025-002', '2025-07-01', 'BRG-003', 'Resleting 25cm', 'Keluar ke Produksi', 200, 'Gudang A', 'Produksi', 'Roni', 'RSZ2501', 'Untuk pesanan PO-0003', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_minimums`
--

CREATE TABLE `stok_minimums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `stok_saat_ini` int(11) NOT NULL,
  `min_stok` int(11) NOT NULL,
  `rop_otomatis` varchar(255) NOT NULL,
  `status_pemesanan` enum('Aman','Perlu Restock') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stok_minimums`
--

INSERT INTO `stok_minimums` (`id`, `sku`, `nama_barang`, `stok_saat_ini`, `min_stok`, `rop_otomatis`, `status_pemesanan`, `created_at`, `updated_at`) VALUES
(1, 'SKU-A', 'Kemeja Formal', 40, 50, 'Ya (50)', 'Perlu Restock', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, 'SKU-C', 'Jaket Hoodie', 300, 100, 'Tidak', 'Aman', '2026-07-01 21:18:35', '2026-07-01 21:18:35');

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
(1, 'Cartoonist', 'Zachery Nader', '577001', 'HRD', NULL, 'Kantor Cabang 2', 'Kontrak', '1990-01-17', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(2, 'Residential Advisor', 'Zackery Beatty', '710102', 'IT', 'Vivienne Kunde Jr.', 'Kantor Pusat', 'Tetap', '1995-10-15', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(3, 'Social Service Specialists', 'Dexter Reinger', '112301', 'Keuangan', 'Aubree Satterfield Sr.', 'Kantor Pusat', 'Kontrak', '1985-12-01', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(4, 'Heat Treating Equipment Operator', 'Fabiola Pouros', '525068', 'Manajemen', 'Adrienne Berge', 'Kantor Pusat', 'Kontrak', '2022-01-14', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(5, 'RN', 'Alfonzo Auer I', '100974', 'Pemasaran', 'Maryse Rolfson', 'Kantor Cabang 1', 'Tetap', '1975-05-15', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(6, 'Answering Service', 'Prof. Aniya Doyle MD', '957199', 'Keuangan', 'Dr. Lloyd Kessler III', 'Kantor Cabang 2', 'Tetap', '1977-07-08', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(7, 'Procurement Clerk', 'Gretchen Ankunding', '543212', 'IT', 'Dr. Desiree Turcotte', 'Kantor Cabang 2', 'Kontrak', '2007-01-01', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(8, 'Social Science Research Assistant', 'Prof. Amina Harvey II', '795947', 'Pemasaran', 'Domingo Little I', 'Kantor Pusat', 'Kontrak', '1972-05-11', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(9, 'Plant Scientist', 'Janet Kutch', '466039', 'HRD', 'Luther Beatty', 'Kantor Cabang 2', 'Kontrak', '1982-06-01', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(10, 'Crane and Tower Operator', 'Mr. Jessy Bayer PhD', '444525', 'Pemasaran', 'Dr. Gene Mills', 'Kantor Cabang 2', 'Tetap', '2010-12-16', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(11, 'Nuclear Monitoring Technician', 'Fiona Murphy', '484261', 'Manajemen', 'Halle Carter', 'Kantor Pusat', 'Kontrak', '1980-06-20', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(12, 'Plant Scientist', 'Hobart Schumm', '328316', 'Keuangan', 'Jarrod Lynch', 'Kantor Pusat', 'Tetap', '2026-03-21', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(13, 'Medical Scientists', 'Jordyn Leuschke', '151220', 'HRD', 'Ariel Lesch', 'Kantor Pusat', 'Kontrak', '1984-01-29', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(14, 'Nuclear Engineer', 'Lysanne Koch', '998897', 'Pemasaran', 'Patricia Hahn', 'Kantor Pusat', 'Kontrak', '1992-05-15', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(15, 'Woodworking Machine Operator', 'Haven Beatty', '586412', 'Pemasaran', 'Estevan Koelpin', 'Kantor Cabang 1', 'Kontrak', '1984-06-06', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(16, 'Food Tobacco Roasting', 'Prof. Ben Hand', '955771', 'Keuangan', 'Mozelle Olson', 'Kantor Cabang 2', 'Tetap', '2012-12-29', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(17, 'Engineering Manager', 'Dandre Marvin', '104627', 'HRD', 'Dr. Ryleigh Schulist', 'Kantor Cabang 2', 'Tetap', '1994-03-27', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(18, 'Police Identification OR Records Officer', 'Miss Brenda Kessler Sr.', '535997', 'Manajemen', 'Geovany Schamberger III', 'Kantor Pusat', 'Tetap', '2002-04-21', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(19, 'Rail Transportation Worker', 'Lulu Shanahan', '818098', 'Pemasaran', 'Andy Goodwin', 'Kantor Cabang 2', 'Tetap', '2005-04-16', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(20, 'Hand Sewer', 'Kasandra Waters', '818748', 'Manajemen', 'Prof. Trace Schultz I', 'Kantor Cabang 2', 'Kontrak', '2006-06-19', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(21, 'Pediatricians', 'Prof. Gonzalo Dickens', '222306', 'Keuangan', 'Mr. Jaiden Torp', 'Kantor Cabang 2', 'Tetap', '2011-03-26', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(22, 'Installation and Repair Technician', 'Myles Ratke DVM', '116045', 'Pemasaran', 'Prof. Tyra Spinka', 'Kantor Pusat', 'Tetap', '1989-08-19', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(23, 'Library Worker', 'Miss Millie Kuhic DDS', '210155', 'Pemasaran', 'Ethelyn Shields', 'Kantor Cabang 1', 'Tetap', '2020-10-26', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(24, 'Actuary', 'Kathleen Mitchell', '517474', 'IT', 'Laurine Green', 'Kantor Pusat', 'Kontrak', '1982-02-13', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(25, 'Rolling Machine Setter', 'Augustine Marvin', '602990', 'IT', 'Viviane Gutmann', 'Kantor Pusat', 'Kontrak', '2017-12-22', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(26, 'Stock Clerk', 'Madonna Howell', '886703', 'IT', 'Prof. Geraldine Walsh', 'Kantor Cabang 2', 'Tetap', '2007-03-28', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(27, 'Transportation and Material-Moving', 'Mr. Arely Jerde PhD', '383682', 'Pemasaran', 'Gerda Stroman', 'Kantor Pusat', 'Kontrak', '2013-08-24', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(28, 'Motor Vehicle Operator', 'Dr. Noelia Buckridge Jr.', '391251', 'Manajemen', 'Gisselle Schultz', 'Kantor Cabang 2', 'Kontrak', '2018-11-02', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(29, 'Clinical Laboratory Technician', 'Erica Kuphal', '828443', 'Pemasaran', 'Gregoria Bogan', 'Kantor Pusat', 'Kontrak', '2011-04-25', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(30, 'Business Teacher', 'Jorge Wiza Sr.', '176346', 'IT', 'Chyna Reichert', 'Kantor Cabang 2', 'Tetap', '1996-02-12', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(31, 'Interpreter OR Translator', 'Moses Sporer I', '192021', 'Manajemen', 'Prof. Kieran Kunde DVM', 'Kantor Cabang 1', 'Kontrak', '2022-03-17', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(32, 'Continuous Mining Machine Operator', 'Carol Marks', '629496', 'IT', 'Myrl Labadie', 'Kantor Cabang 2', 'Tetap', '1984-12-14', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(33, 'Warehouse', 'Claudine Mann V', '649137', 'Pemasaran', 'Gardner Doyle', 'Kantor Cabang 1', 'Tetap', '2013-10-10', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(34, 'Electronics Engineer', 'Maribel O\'Kon', '564003', 'HRD', 'Lawson Effertz', 'Kantor Cabang 2', 'Kontrak', '2016-08-28', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(35, 'Automotive Technician', 'Prof. Lindsey Schowalter', '482042', 'Keuangan', 'Sophia Auer', 'Kantor Pusat', 'Tetap', '1981-08-19', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(36, 'Electrical Power-Line Installer', 'Jacklyn Bins DDS', '422977', 'HRD', 'Jaqueline Skiles', 'Kantor Cabang 2', 'Kontrak', '2011-03-29', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(37, 'Dot Etcher', 'Winifred Corkery', '263719', 'Keuangan', 'Westley Hammes', 'Kantor Pusat', 'Tetap', '1976-12-09', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(38, 'Tractor Operator', 'Unique Huels', '753376', 'Manajemen', 'Dr. Abigayle Kassulke DDS', 'Kantor Pusat', 'Tetap', '2007-06-02', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(39, 'Directory Assistance Operator', 'Alayna Fahey', '904794', 'Manajemen', 'Dr. Annabell Dach IV', 'Kantor Cabang 2', 'Kontrak', '2000-01-28', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(40, 'Immigration Inspector OR Customs Inspector', 'Alvah Rohan', '220003', 'IT', 'Enoch Kshlerin', 'Kantor Cabang 1', 'Kontrak', '2010-05-23', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(41, 'Welfare Eligibility Clerk', 'Kristian Ondricka', '203687', 'Pemasaran', 'Giuseppe Wisoky', 'Kantor Cabang 1', 'Kontrak', '2022-10-20', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(42, 'Education Administrator', 'Prof. Amya Carter Jr.', '890528', 'Keuangan', 'Dr. Maia Macejkovic DDS', 'Kantor Cabang 2', 'Tetap', '1993-02-07', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(43, 'Bridge Tender OR Lock Tender', 'Fredy Parker', '466699', 'Keuangan', 'Vesta Berge', 'Kantor Cabang 2', 'Tetap', '1992-12-29', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(44, 'Transportation Equipment Maintenance', 'Ismael Batz I', '955743', 'HRD', 'Guillermo Lueilwitz', 'Kantor Pusat', 'Tetap', '2010-09-05', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(45, 'Glazier', 'Pearlie Barton', '606007', 'IT', 'Dr. Graciela Cummerata Jr.', 'Kantor Cabang 2', 'Kontrak', '2006-04-16', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(46, 'Extruding Machine Operator', 'Domenica Ondricka Sr.', '385799', 'IT', 'Dr. Heloise Kuhn Sr.', 'Kantor Pusat', 'Tetap', '2025-12-07', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(47, 'Sheet Metal Worker', 'Sheldon Jenkins V', '322105', 'Pemasaran', 'Mr. D\'angelo Erdman', 'Kantor Pusat', 'Kontrak', '2023-05-16', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(48, 'Business Manager', 'Wade Quigley', '808144', 'Pemasaran', 'Mr. Darrick Carter DVM', 'Kantor Pusat', 'Tetap', '1991-06-19', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(49, 'Brattice Builder', 'Ethel Balistreri DDS', '691486', 'IT', 'Lilyan Padberg', 'Kantor Pusat', 'Kontrak', '1990-12-22', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(50, 'Telephone Operator', 'Mack Schneider', '643759', 'HRD', 'Prof. Frederik Thompson II', 'Kantor Cabang 1', 'Tetap', '1980-07-22', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(51, 'Cashier', 'Shayne Ruecker V', '452461', 'Keuangan', 'Mr. Nigel Berge Jr.', 'Kantor Cabang 2', 'Kontrak', '2024-05-25', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(52, 'Vice President Of Human Resources', 'Isaac Gerlach', '714615', 'IT', 'Anderson Boyer', 'Kantor Cabang 1', 'Tetap', '2014-03-26', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(53, 'Civil Drafter', 'Prof. Terry Upton', '854745', 'Manajemen', 'Mrs. Alexanne Champlin III', 'Kantor Pusat', 'Tetap', '2007-01-03', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(54, 'Makeup Artists', 'Emanuel Schoen', '799101', 'HRD', 'Melisa Tillman', 'Kantor Cabang 2', 'Tetap', '2025-02-10', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(55, 'Hunter and Trapper', 'Hope O\'Conner DDS', '500148', 'IT', 'Hans Considine DDS', 'Kantor Cabang 2', 'Tetap', '2015-08-27', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(56, 'Nursing Aide', 'Morgan Dare III', '386638', 'Keuangan', 'Yasmine Kemmer', 'Kantor Cabang 2', 'Tetap', '2025-09-04', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(57, 'Postal Service Mail Carrier', 'Rupert Mueller', '349833', 'IT', 'Prof. Vallie Schumm I', 'Kantor Cabang 2', 'Tetap', '2009-10-08', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(58, 'Airfield Operations Specialist', 'Makayla Fisher', '733235', 'Manajemen', 'Adela Ullrich', 'Kantor Pusat', 'Tetap', '2005-10-14', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(59, 'Spotters', 'Omer Mohr', '308113', 'Manajemen', 'Mrs. Alverta O\'Keefe', 'Kantor Pusat', 'Kontrak', '1974-02-13', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(60, 'Production Control Manager', 'Cristina Wiza', '386919', 'Manajemen', 'Dr. Devante Wilderman', 'Kantor Cabang 1', 'Tetap', '2001-09-09', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(61, 'Meter Mechanic', 'Annabelle Cronin II', '393405', 'Manajemen', 'Jamel Rempel', 'Kantor Pusat', 'Kontrak', '1970-01-17', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(62, 'Animal Control Worker', 'Talia Jerde', '314022', 'IT', 'Garrett Braun', 'Kantor Cabang 2', 'Tetap', '2015-02-28', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(63, 'Offset Lithographic Press Operator', 'Payton Mosciski', '945644', 'Pemasaran', 'Cristopher Reichert', 'Kantor Cabang 1', 'Tetap', '1993-11-27', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(64, 'Microbiologist', 'Erika Hermiston', '679315', 'Keuangan', 'Ramiro Bosco', 'Kantor Cabang 2', 'Kontrak', '2006-09-10', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(65, 'Agricultural Sales Representative', 'Dante Kassulke', '989096', 'Manajemen', 'Dr. Eladio Ferry', 'Kantor Pusat', 'Tetap', '1974-03-02', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(66, 'Ship Captain', 'Amari Johnston', '995944', 'HRD', 'Dr. Marilou Langosh', 'Kantor Cabang 1', 'Kontrak', '1972-09-11', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(67, 'Gas Plant Operator', 'Diana Kunde', '822057', 'Pemasaran', 'Prof. Nakia Gusikowski Jr.', 'Kantor Pusat', 'Tetap', '1987-06-27', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(68, 'Sociology Teacher', 'Ashlee Goldner', '383199', 'HRD', 'Eden Volkman', 'Kantor Pusat', 'Tetap', '1978-10-11', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(69, 'Shear Machine Set-Up Operator', 'Miss Katelin Konopelski PhD', '188235', 'Keuangan', 'Miss Kimberly Heaney', 'Kantor Cabang 1', 'Kontrak', '2003-02-10', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(70, 'New Accounts Clerk', 'Prof. Colton Zboncak', '530101', 'IT', 'Kristy Purdy', 'Kantor Cabang 2', 'Kontrak', '1999-10-15', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(71, 'Product Safety Engineer', 'Eleanore Kovacek', '123361', 'Keuangan', 'Jessika Lang', 'Kantor Cabang 1', 'Tetap', '1995-01-20', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(72, 'Secondary School Teacher', 'Elva Bashirian', '136501', 'IT', 'Tania McGlynn', 'Kantor Pusat', 'Kontrak', '2021-03-28', '2026-07-01 21:18:32', '2026-07-01 21:18:32'),
(73, 'Textile Dyeing Machine Operator', 'Prof. Lee Wyman', '766309', 'HRD', 'Cydney Hoeger', 'Kantor Cabang 1', 'Kontrak', '2001-04-16', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(74, 'ccc', 'Ora Welch', '548157', 'IT', 'Eliseo Kassulke I', 'Kantor Cabang 2', 'Tetap', '1972-01-25', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(75, 'Maintenance Supervisor', 'Skyla Kirlin', '973652', 'Keuangan', 'Cora Larson', 'Kantor Cabang 1', 'Tetap', '2016-04-06', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(76, 'Architectural Drafter OR Civil Drafter', 'Dr. Trycia Hand I', '985947', 'Pemasaran', 'Thelma Beatty', 'Kantor Cabang 1', 'Tetap', '1979-12-19', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(77, 'Textile Machine Operator', 'Marion Heller IV', '956495', 'HRD', 'Miss Mossie Johns DVM', 'Kantor Cabang 2', 'Kontrak', '2003-11-25', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(78, 'Security Guard', 'Kadin Kub', '882156', 'IT', 'Miss Chaya Berge', 'Kantor Pusat', 'Tetap', '1987-06-08', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(79, 'Stone Sawyer', 'Ali Nikolaus', '106249', 'Keuangan', 'Alva Bauch', 'Kantor Pusat', 'Kontrak', '1992-09-20', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(80, 'Geological Sample Test Technician', 'Ms. Constance Zieme', '284028', 'Keuangan', 'Roderick Daniel', 'Kantor Pusat', 'Tetap', '2007-09-10', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(81, 'Fish Game Warden', 'Jennyfer Smitham', '970515', 'Manajemen', 'Prof. Jaiden Leffler', 'Kantor Pusat', 'Kontrak', '1992-11-14', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(82, 'Gaming Supervisor', 'Linda Bednar', '473082', 'IT', 'Kaleb Fisher', 'Kantor Cabang 1', 'Kontrak', '1971-08-28', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(83, 'Storage Manager OR Distribution Manager', 'Ettie Jerde', '416018', 'Pemasaran', 'Abigail Waelchi', 'Kantor Pusat', 'Tetap', '1974-12-02', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(84, 'Talent Director', 'Robbie Crooks Sr.', '599276', 'Pemasaran', 'Onie Blick', 'Kantor Cabang 1', 'Kontrak', '2010-05-28', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(85, 'Gas Appliance Repairer', 'Devon Watsica Jr.', '135818', 'IT', 'Emile Jacobson', 'Kantor Cabang 2', 'Kontrak', '1992-10-12', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(86, 'Motor Vehicle Operator', 'Isabelle Casper', '159997', 'IT', 'Jedediah Legros', 'Kantor Cabang 1', 'Kontrak', '2014-12-13', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(87, 'Recreation and Fitness Studies Teacher', 'Prudence Baumbach DDS', '770241', 'Keuangan', 'Marilie Koepp', 'Kantor Cabang 2', 'Kontrak', '1996-07-21', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(88, 'Directory Assistance Operator', 'Abby Kozey', '335718', 'Keuangan', 'Monserrat Hodkiewicz', 'Kantor Pusat', 'Tetap', '1970-05-29', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(89, 'Insurance Appraiser', 'Lacey Hessel', '530857', 'HRD', 'Dennis Parker', 'Kantor Cabang 1', 'Kontrak', '1998-01-14', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(90, 'Diamond Worker', 'Sunny Botsford', '781223', 'Keuangan', 'Miss Cali Rutherford', 'Kantor Pusat', 'Tetap', '1973-10-23', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(91, 'Medical Records Technician', 'Abbigail Rowe', '946835', 'Manajemen', 'Charley Grimes', 'Kantor Pusat', 'Tetap', '2015-01-08', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(92, 'Electrical Parts Reconditioner', 'Sylvan Schuster', '459913', 'Manajemen', 'Mr. Cody Conroy', 'Kantor Cabang 2', 'Tetap', '1972-11-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(93, 'Nuclear Technician', 'Jamel Reichert', '772988', 'Keuangan', 'Lillian Emmerich', 'Kantor Cabang 2', 'Kontrak', '1997-04-08', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(94, 'School Social Worker', 'Mrs. Alice Reinger I', '808529', 'IT', 'Dexter Hansen', 'Kantor Cabang 1', 'Kontrak', '2007-07-12', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(95, 'Paste-Up Worker', 'Rashawn Labadie', '633835', 'HRD', 'Aracely Ward', 'Kantor Cabang 1', 'Tetap', '2017-04-18', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(96, 'Mechanical Inspector', 'Zander Schaefer Sr.', '835270', 'Manajemen', 'Rebekah Osinski IV', 'Kantor Pusat', 'Tetap', '2001-09-30', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(97, 'Steel Worker', 'Izaiah Greenfelder', '683668', 'IT', 'Opal Wintheiser DDS', 'Kantor Cabang 2', 'Kontrak', '1976-11-28', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(98, 'Shoe Machine Operators', 'Dr. Cleora Mohr', '665776', 'Keuangan', 'Prof. Timmothy Connelly', 'Kantor Cabang 1', 'Kontrak', '2022-03-24', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(99, 'Information Systems Manager', 'Chance Kerluke', '204690', 'Pemasaran', 'Alexandria Kreiger', 'Kantor Cabang 1', 'Tetap', '2015-03-01', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(100, 'Pewter Caster', 'Erich Rodriguez', '652608', 'Keuangan', 'Ramona Walter', 'Kantor Pusat', 'Kontrak', '1975-02-07', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(101, 'Hydrologist', 'Clay Pouros', '747402', 'Pemasaran', 'Kimberly Braun', 'Kantor Pusat', 'Tetap', '2005-05-10', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(102, 'Parking Enforcement Worker', 'Lavinia Lockman', '734882', 'Pemasaran', 'Mr. Hans Oberbrunner V', 'Kantor Cabang 2', 'Kontrak', '1981-08-31', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(103, 'Stone Cutter', 'Berniece Pfeffer', '160326', 'HRD', 'Grayson Doyle', 'Kantor Cabang 2', 'Tetap', '2011-10-31', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(104, 'Refractory Materials Repairer', 'Saul Greenholt', '894066', 'IT', 'Miss Bethany Moen', 'Kantor Cabang 2', 'Tetap', '2000-10-06', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(105, 'Photographic Restorer', 'Mr. Hayden Nicolas', '364424', 'IT', 'Ramona Olson', 'Kantor Cabang 2', 'Tetap', '1980-05-07', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(106, 'Fashion Model', 'Gerard Wiegand', '539631', 'Pemasaran', 'Toni Ward', 'Kantor Cabang 1', 'Tetap', '1995-06-06', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(107, 'Biological Technician', 'Garland Gulgowski', '948900', 'Pemasaran', 'Vincenzo Paucek PhD', 'Kantor Cabang 2', 'Tetap', '2003-08-08', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(108, 'Veterinarian', 'Dayton Keebler Jr.', '351957', 'Keuangan', 'Dr. Louisa Wilkinson', 'Kantor Cabang 2', 'Tetap', '2023-09-05', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(109, 'Medical Transcriptionist', 'Ms. Gail Hackett', '229349', 'Pemasaran', 'Kenya Deckow', 'Kantor Cabang 2', 'Kontrak', '2006-03-23', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(110, 'Foundry Mold and Coremaker', 'Zita Ferry II', '507894', 'HRD', 'Felicia Dooley', 'Kantor Pusat', 'Tetap', '1978-06-29', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(111, 'Public Relations Specialist', 'Mr. Jairo Fahey Jr.', '924164', 'HRD', 'Lucie Graham', 'Kantor Cabang 1', 'Kontrak', '1994-02-18', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(112, 'Tractor Operator', 'Prof. Nadia Kris', '683218', 'IT', 'Adolphus Considine', 'Kantor Cabang 1', 'Tetap', '1987-09-25', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(113, 'Textile Dyeing Machine Operator', 'Jensen Bergstrom', '176802', 'Keuangan', 'Mr. Terrill Luettgen', 'Kantor Cabang 1', 'Kontrak', '2002-04-26', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(114, 'Boilermaker', 'Prof. Tad Larkin', '406353', 'HRD', 'Fidel Lesch', 'Kantor Cabang 1', 'Tetap', '1996-06-28', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(115, 'Command Control Center Officer', 'Kacey Larkin', '997783', 'Keuangan', 'Miss Yadira Kling', 'Kantor Cabang 2', 'Tetap', '1983-11-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(116, 'Plastic Molding Machine Operator', 'Margarita Yost', '405316', 'HRD', 'Bryana Raynor', 'Kantor Pusat', 'Kontrak', '2001-01-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(117, 'CEO', 'Dr. Cindy Parker DVM', '814232', 'IT', 'Annabelle Johnston', 'Kantor Cabang 2', 'Kontrak', '2024-08-06', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(118, 'Automotive Mechanic', 'Nicholaus Nienow', '218058', 'IT', 'Dr. Cleo Barton', 'Kantor Pusat', 'Tetap', '2017-01-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(119, 'Audio and Video Equipment Technician', 'Annamae Stamm', '942497', 'IT', 'Mrs. Maxie Rippin', 'Kantor Pusat', 'Kontrak', '2001-03-15', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(120, 'Distribution Manager', 'Mrs. Antoinette Hessel PhD', '721663', 'IT', 'Miss Beulah Daugherty V', 'Kantor Cabang 1', 'Kontrak', '1999-06-13', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(121, 'Radiologic Technologist and Technician', 'Miss Arlene Runte', '406658', 'Manajemen', 'Helga Roob', 'Kantor Cabang 2', 'Kontrak', '1985-04-23', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(122, 'Furniture Finisher', 'Arjun Corwin', '370133', 'Manajemen', 'Shanny Grimes III', 'Kantor Cabang 1', 'Kontrak', '2005-07-26', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(123, 'Aviation Inspector', 'Ozella Luettgen', '629753', 'IT', 'Kiarra Buckridge', 'Kantor Pusat', 'Kontrak', '2024-10-26', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(124, 'Chemistry Teacher', 'Minnie Barrows', '117562', 'HRD', 'Lee Skiles', 'Kantor Pusat', 'Tetap', '1984-10-26', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(125, 'Radio Mechanic', 'Dashawn Schimmel MD', '546836', 'Keuangan', 'Colleen Wolff', 'Kantor Cabang 1', 'Kontrak', '1983-02-15', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(126, 'Psychiatric Technician', 'Lavada Nicolas', '514159', 'IT', 'Mr. Raymundo Padberg', 'Kantor Pusat', 'Tetap', '1976-04-17', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(127, 'Communication Equipment Worker', 'Ladarius Cormier', '398215', 'IT', 'Miss Rosina Buckridge', 'Kantor Cabang 2', 'Tetap', '2025-01-17', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(128, 'Chemical Equipment Operator', 'Phyllis Collier', '611125', 'Manajemen', 'Greyson Nitzsche', 'Kantor Cabang 2', 'Tetap', '1974-06-17', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(129, 'Video Editor', 'Einar Moen', '123657', 'Pemasaran', 'Leda Zulauf', 'Kantor Pusat', 'Tetap', '1997-11-28', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(130, 'Precision Mold and Pattern Caster', 'Lillian Erdman PhD', '531669', 'Manajemen', 'Ivah Mitchell', 'Kantor Cabang 1', 'Tetap', '1981-11-24', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(131, 'Office Machine Operator', 'Marlen Ratke', '580472', 'HRD', 'Triston Bradtke', 'Kantor Cabang 1', 'Kontrak', '1997-12-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(132, 'Medical Secretary', 'Caitlyn Kihn', '292882', 'HRD', 'Levi Miller', 'Kantor Cabang 2', 'Kontrak', '1996-03-26', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(133, 'Director Of Business Development', 'Daija Collins', '799069', 'Keuangan', 'Eve Maggio', 'Kantor Pusat', 'Kontrak', '2019-09-08', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(134, 'Welder-Fitter', 'Mariah Dietrich', '658638', 'IT', 'Cordia Hettinger', 'Kantor Cabang 1', 'Tetap', '2017-08-05', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(135, 'Event Planner', 'Naomi Macejkovic', '619762', 'Pemasaran', 'Mr. Randy Hayes', 'Kantor Cabang 1', 'Tetap', '1981-11-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(136, 'Reservation Agent OR Transportation Ticket Agent', 'Ms. Corene Rau MD', '508785', 'Pemasaran', 'Prof. Abner O\'Keefe II', 'Kantor Pusat', 'Kontrak', '1984-07-05', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(137, 'Automotive Body Repairer', 'Prof. Rubye Mohr II', '807308', 'Keuangan', 'Elvie Bahringer', 'Kantor Pusat', 'Tetap', '2021-11-21', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(138, 'Parts Salesperson', 'Natalia Blanda', '308684', 'HRD', 'Miss Lempi Emmerich DVM', 'Kantor Pusat', 'Kontrak', '1979-09-17', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(139, 'Septic Tank Servicer', 'Rosanna Price', '255113', 'IT', 'Reid Fritsch PhD', 'Kantor Cabang 2', 'Kontrak', '1974-09-01', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(140, 'Food Cooking Machine Operators', 'Steve Watsica', '650326', 'Pemasaran', 'Major Lockman', 'Kantor Cabang 2', 'Tetap', '2025-12-11', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(141, 'Maintenance Supervisor', 'Deron Labadie', '756807', 'Keuangan', 'Rubie McDermott', 'Kantor Cabang 1', 'Tetap', '1970-02-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(142, 'Special Force', 'Johnny Luettgen', '158438', 'Keuangan', 'Dr. Earlene Streich Sr.', 'Kantor Pusat', 'Tetap', '2005-06-30', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(143, 'Packer and Packager', 'Miss Eleonore Hoeger', '541028', 'Keuangan', 'Filomena Koepp', 'Kantor Cabang 2', 'Kontrak', '2018-05-13', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(144, 'Writer OR Author', 'Mr. Neal Champlin', '867623', 'Keuangan', 'Anissa Lemke', 'Kantor Pusat', 'Kontrak', '1972-08-11', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(145, 'Foreign Language Teacher', 'Mrs. Ida Stamm PhD', '552242', 'Manajemen', 'Danyka Dooley', 'Kantor Cabang 2', 'Kontrak', '2004-05-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(146, 'Clergy', 'Prof. Enos Padberg III', '590154', 'Pemasaran', 'Ewald Becker', 'Kantor Cabang 1', 'Tetap', '1975-09-24', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(147, 'Photoengraving Machine Operator', 'Freeman Mitchell', '364104', 'HRD', 'Mr. Clifford Pfeffer', 'Kantor Pusat', 'Kontrak', '1997-01-10', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(148, 'Lawn Service Manager', 'Wanda Schinner Jr.', '713494', 'Manajemen', 'Landen Robel', 'Kantor Cabang 1', 'Kontrak', '2005-02-12', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(149, 'Brickmason', 'Miss Libby Schinner', '958489', 'Keuangan', 'Joelle Zulauf', 'Kantor Cabang 1', 'Kontrak', '1995-11-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(150, 'Bookbinder', 'Garrick Lowe', '736232', 'Manajemen', 'Adelle Bergstrom', 'Kantor Cabang 2', 'Tetap', '1977-03-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(151, 'Funeral Attendant', 'Ms. Alivia Schowalter', '751140', 'Manajemen', 'Savannah Gibson', 'Kantor Cabang 1', 'Tetap', '2015-11-24', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(152, 'Mechanical Door Repairer', 'Mrs. Ellen Swaniawski', '878030', 'Manajemen', 'Benedict Lockman', 'Kantor Cabang 2', 'Kontrak', '1994-05-21', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(153, 'House Cleaner', 'Henri Murray III', '600221', 'Keuangan', 'Mozelle Daugherty', 'Kantor Cabang 1', 'Kontrak', '2015-07-28', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(154, 'Transportation Equipment Painters', 'Yolanda Upton', '223360', 'HRD', 'Harvey Keeling', 'Kantor Cabang 1', 'Tetap', '2007-06-24', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(155, 'Molder', 'Miss Christiana Jakubowski', '561614', 'Keuangan', 'Ariane Gerhold', 'Kantor Pusat', 'Tetap', '1991-12-23', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(156, 'Director Of Talent Acquisition', 'Royce Wilkinson', '444257', 'IT', 'Myrtice Kohler', 'Kantor Cabang 2', 'Tetap', '2001-05-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(157, 'Plumber OR Pipefitter OR Steamfitter', 'Verna Reichert II', '736395', 'Pemasaran', 'Prof. Keshaun Bechtelar II', 'Kantor Cabang 2', 'Kontrak', '2003-11-01', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(158, 'Mine Cutting Machine Operator', 'Rowena Abernathy III', '789455', 'IT', 'Zetta Auer', 'Kantor Pusat', 'Tetap', '2000-03-25', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(159, 'Postsecondary Teacher', 'Flo Lebsack', '634178', 'HRD', 'Ima Altenwerth', 'Kantor Pusat', 'Kontrak', '1992-03-19', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(160, 'Government Service Executive', 'Dr. Jena Crona', '505848', 'IT', 'Dexter Casper', 'Kantor Cabang 2', 'Kontrak', '1989-05-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(161, 'Police Identification OR Records Officer', 'Jamie Kuhn', '497879', 'Pemasaran', 'Prof. Lula Bogan', 'Kantor Cabang 2', 'Kontrak', '2006-11-30', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(162, 'Medical Laboratory Technologist', 'Lolita Thiel', '869066', 'HRD', 'Prof. Keon Bednar', 'Kantor Cabang 2', 'Kontrak', '2023-11-30', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(163, 'Cartographer', 'Kirsten Morar', '340135', 'Pemasaran', 'Lucas Kessler Sr.', 'Kantor Pusat', 'Kontrak', '2023-04-08', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(164, 'Short Order Cook', 'Kale Schinner', '359508', 'Manajemen', 'Mr. Brock Gerhold MD', 'Kantor Pusat', 'Kontrak', '1971-07-26', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(165, 'Creative Writer', 'Corrine Borer II', '448713', 'Pemasaran', 'Cody Murazik', 'Kantor Cabang 1', 'Kontrak', '1994-12-08', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(166, 'Pipelayer', 'Tremaine D\'Amore DDS', '509602', 'IT', 'Will Bergstrom I', 'Kantor Cabang 2', 'Kontrak', '1990-08-18', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(167, 'Drilling and Boring Machine Tool Setter', 'Gregorio Wolf', '816328', 'Keuangan', 'Prof. Palma Hudson Sr.', 'Kantor Cabang 1', 'Tetap', '2023-03-21', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(168, 'Telephone Operator', 'Prof. Ava Ruecker', '906241', 'Pemasaran', 'Prof. Grayce Shanahan PhD', 'Kantor Cabang 2', 'Tetap', '2003-09-01', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(169, 'Transportation Worker', 'Mr. Joany Fritsch', '721130', 'Manajemen', 'Myles Cassin', 'Kantor Cabang 1', 'Kontrak', '1975-04-29', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(170, 'Structural Metal Fabricator', 'Loraine Bode MD', '115569', 'IT', 'Emie Senger', 'Kantor Cabang 2', 'Tetap', '1972-11-21', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(171, 'Self-Enrichment Education Teacher', 'Mr. Montana Dach PhD', '681582', 'Manajemen', 'Matteo Walsh MD', 'Kantor Pusat', 'Tetap', '1996-08-18', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(172, 'Gauger', 'Cali Pagac I', '923140', 'IT', 'Prof. Carmel Williamson', 'Kantor Pusat', 'Tetap', '1971-02-25', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(173, 'Professional Photographer', 'Gus Leannon', '271267', 'HRD', 'Russell Bosco', 'Kantor Cabang 2', 'Tetap', '2002-06-30', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(174, 'Electronics Engineering Technician', 'Dr. Athena Harris PhD', '329377', 'Manajemen', 'Mr. Johann Gulgowski DVM', 'Kantor Cabang 1', 'Kontrak', '2025-01-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(175, 'Claims Examiner', 'Guiseppe Grant PhD', '126761', 'Pemasaran', 'Elouise Windler IV', 'Kantor Pusat', 'Kontrak', '1977-08-14', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(176, 'Eligibility Interviewer', 'Miss Edwina Heller II', '536082', 'Manajemen', 'Leda Kautzer IV', 'Kantor Cabang 1', 'Kontrak', '2000-04-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(177, 'Microbiologist', 'Mrs. Hattie Schroeder III', '589690', 'Manajemen', 'Keagan Buckridge V', 'Kantor Pusat', 'Tetap', '1971-08-10', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(178, 'Welding Machine Tender', 'Esperanza Lebsack', '532347', 'HRD', 'Dr. Bonita Leannon', 'Kantor Cabang 2', 'Tetap', '1977-05-21', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(179, 'Equal Opportunity Representative', 'Michele Witting', '334000', 'Manajemen', 'Mrs. Serenity Robel', 'Kantor Cabang 1', 'Tetap', '2021-12-09', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(180, 'Diesel Engine Specialist', 'Bridget Conn Sr.', '758515', 'Manajemen', 'Mr. Louisa Padberg', 'Kantor Pusat', 'Kontrak', '2015-09-16', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(181, 'Copy Writer', 'Lavon Pfeffer', '179085', 'Pemasaran', 'Joanne Wolf', 'Kantor Pusat', 'Tetap', '1986-12-06', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(182, 'Drafter', 'Mrs. Lenore Quitzon', '156658', 'Manajemen', 'Prof. Nestor Towne II', 'Kantor Cabang 1', 'Tetap', '2015-07-27', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(183, 'Refractory Materials Repairer', 'Terry Marvin', '117235', 'Pemasaran', 'Pinkie Gibson Jr.', 'Kantor Cabang 2', 'Tetap', '1995-09-12', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(184, 'Audio-Visual Collections Specialist', 'Madison Marvin', '751829', 'Manajemen', 'Beau Williamson DDS', 'Kantor Pusat', 'Kontrak', '1970-06-16', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(185, 'Bookkeeper', 'Kristy Ondricka', '123259', 'Keuangan', 'Vincenzo Mraz', 'Kantor Pusat', 'Tetap', '1980-01-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(186, 'Buyer', 'Prof. Reuben Hegmann PhD', '181366', 'IT', 'Jorge Hand', 'Kantor Cabang 1', 'Kontrak', '1976-07-03', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(187, 'Compliance Officers', 'Mr. Jesus Hoeger MD', '853845', 'IT', 'Prof. Tomasa Halvorson DDS', 'Kantor Cabang 2', 'Tetap', '1984-11-15', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(188, 'Technical Director', 'Lorenzo Beahan', '283125', 'Keuangan', 'Jazmin Douglas', 'Kantor Cabang 2', 'Tetap', '1975-11-22', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(189, 'Construction Equipment Operator', 'August O\'Hara', '801378', 'Pemasaran', 'Scotty Krajcik', 'Kantor Pusat', 'Kontrak', '2009-02-23', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(190, 'Secondary School Teacher', 'Ms. Guadalupe Mertz DVM', '201568', 'Pemasaran', 'Prof. Jerod Cronin', 'Kantor Pusat', 'Kontrak', '1979-09-29', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(191, 'Instrument Sales Representative', 'Aryanna Witting', '944473', 'HRD', 'Prof. Tavares Kreiger II', 'Kantor Pusat', 'Kontrak', '1992-10-07', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(192, 'Pantograph Engraver', 'Erica Funk', '942517', 'Keuangan', 'Esmeralda Auer Jr.', 'Kantor Cabang 1', 'Tetap', '2005-09-17', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(193, 'Pest Control Worker', 'Ms. Modesta Rodriguez Jr.', '510091', 'Pemasaran', 'Lilliana Jaskolski', 'Kantor Pusat', 'Kontrak', '2006-07-02', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(194, 'Homeland Security', 'Esperanza Padberg', '554978', 'Manajemen', 'Dr. Jeramy Kshlerin', 'Kantor Cabang 2', 'Kontrak', '2024-11-05', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(195, 'Cleaners of Vehicles', 'Mrs. Raquel Glover Jr.', '479807', 'Keuangan', 'Ms. Jammie Reilly', 'Kantor Cabang 1', 'Kontrak', '2011-10-21', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(196, 'Religious Worker', 'Avery Ortiz', '239732', 'HRD', 'Samson Carroll', 'Kantor Cabang 2', 'Kontrak', '1976-10-26', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(197, 'Claims Examiner', 'Euna Schneider PhD', '894023', 'Manajemen', 'Dr. Jamaal Leffler', 'Kantor Cabang 1', 'Tetap', '1987-05-16', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(198, 'Zoologists OR Wildlife Biologist', 'Marcelle Wolff', '948999', 'HRD', 'Antonette Heller', 'Kantor Pusat', 'Tetap', '2001-03-20', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(199, 'Claims Taker', 'Blanca Connelly', '410288', 'IT', 'Rachelle Huels IV', 'Kantor Pusat', 'Tetap', '2021-11-28', '2026-07-01 21:18:33', '2026-07-01 21:18:33'),
(200, 'Forensic Science Technician', 'Ms. Elise Wiza', '502395', 'Pemasaran', 'Manuela Daugherty', 'Kantor Pusat', 'Kontrak', '1975-11-17', '2026-07-01 21:18:33', '2026-07-01 21:18:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplieros`
--

CREATE TABLE `supplieros` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produk` varchar(255) NOT NULL,
  `forecast_bulanan` int(11) NOT NULL,
  `realisasi_sebelumnya` int(11) NOT NULL,
  `target_produksi` int(11) NOT NULL,
  `lokasi_distribusi` varchar(255) NOT NULL,
  `lead_time` int(11) NOT NULL,
  `status_permintaan` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `supplieros`
--

INSERT INTO `supplieros` (`id`, `produk`, `forecast_bulanan`, `realisasi_sebelumnya`, `target_produksi`, `lokasi_distribusi`, `lead_time`, `status_permintaan`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Kaos Polos M', 5000, 4200, 5500, 'Toko Nasional (10)', 5, 'Meningkat', 'Stok buffer dibutuhkan', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

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
(1, 'ERP Production', 'Otomatis Cloud', 'Harian', 'AWS S3 Glacier', 'Aktif', '2025-06-15', '2026-07-01 21:18:40', '2026-07-01 21:18:40'),
(2, 'File Server', 'Manual HDD', 'Mingguan', 'Gudang A', 'Rentan', '2025-04-01', '2026-07-01 21:18:40', '2026-07-01 21:18:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `s_o_p_managementos`
--

CREATE TABLE `s_o_p_managementos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_sop` varchar(255) NOT NULL,
  `nama_sop` varchar(255) NOT NULL,
  `divisi` varchar(255) NOT NULL,
  `versi` varchar(255) NOT NULL,
  `disusun_oleh` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `revisi_terakhir` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `s_o_p_managementos`
--

INSERT INTO `s_o_p_managementos` (`id`, `kode_sop`, `nama_sop`, `divisi`, `versi`, `disusun_oleh`, `status`, `revisi_terakhir`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'SOP-001', 'SOP Penerimaan Barang', 'Gudang', '1.2', 'Dodi', 'Aktif', '2025-06-20', 'Akan migrasi ke digital', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, 'SOP-002', 'SOP Pengiriman & DO', 'Logistik', '1.0', 'Dani', 'Draft', NULL, 'Revisi sedang dibuat', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `target_penjualans`
--

CREATE TABLE `target_penjualans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_sales` varchar(255) NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `target_penjualan` decimal(15,2) NOT NULL,
  `pencapaian` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `target_penjualans`
--

INSERT INTO `target_penjualans` (`id`, `nama_sales`, `bulan`, `target_penjualan`, `pencapaian`, `created_at`, `updated_at`) VALUES
(1, 'Rina', 'Juni 2025', 100000000.00, 95000000.00, '2026-07-01 21:18:38', '2026-07-01 21:18:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `team_management`
--

CREATE TABLE `team_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `trackings`
--

CREATE TABLE `trackings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_sku` varchar(255) NOT NULL,
  `nama_item` varchar(255) NOT NULL,
  `barcode_qr` varchar(255) NOT NULL,
  `rfid_code` varchar(255) NOT NULL,
  `lot_serial_number` varchar(255) NOT NULL,
  `tgl_masuk` date NOT NULL,
  `tgl_expired` date DEFAULT NULL,
  `lokasi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `trackings`
--

INSERT INTO `trackings` (`id`, `item_sku`, `nama_item`, `barcode_qr`, `rfid_code`, `lot_serial_number`, `tgl_masuk`, `tgl_expired`, `lokasi`, `created_at`, `updated_at`) VALUES
(1, 'SKU-A', 'Kemeja Formal', '9876543210', 'RFID1001', 'LOT-23-BATCH7', '2025-06-15', '2026-06-15', 'GUD-01', '2026-07-01 21:18:35', '2026-07-01 21:18:35'),
(2, 'SKU-B', 'Celana Jeans', '1234567890', 'RFID1002', 'SN-8891', '2025-06-16', NULL, 'VIRT-01', '2026-07-01 21:18:35', '2026-07-01 21:18:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `trackingutms`
--

CREATE TABLE `trackingutms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `stok_saat_ini` int(11) NOT NULL,
  `min_stok` int(11) NOT NULL,
  `rop_otomatis` tinyint(1) NOT NULL DEFAULT 0,
  `status_pemesanan` varchar(255) NOT NULL DEFAULT 'Aman',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `trackingutms`
--

INSERT INTO `trackingutms` (`id`, `sku`, `nama_barang`, `stok_saat_ini`, `min_stok`, `rop_otomatis`, `status_pemesanan`, `created_at`, `updated_at`) VALUES
(1, 'SKU-A', 'Kemeja Formal', 40, 50, 1, 'Perlu Restock', '2026-07-01 21:18:37', '2026-07-01 21:18:37'),
(2, 'SKU-C', 'Jaket Hoodie', 300, 100, 0, 'Aman', '2026-07-01 21:18:37', '2026-07-01 21:18:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `jabatan`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'toty', 'toty@gmail.com', NULL, 'Admin Dashboard', '$2y$12$/qAjV9qAB292gdS3rg5lIuaQFr2kjvOc3IAs5es1nC/rA.T.8xnMm', NULL, NULL, NULL);

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
(1, 'rina.f', 'Finance', 'Admin Modul Akuntansi', 'ERP', 'Aktif', '-', '2026-07-01 21:18:39', '2026-07-01 21:18:39'),
(2, 'yusuf.p', 'Produksi', 'Viewer', 'Dashboard ERP', 'Aktif', 'Request upgrade ke Editor', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `valuasi_fifos`
--

CREATE TABLE `valuasi_fifos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `valuasi` varchar(255) NOT NULL,
  `harga_unit` decimal(15,2) NOT NULL,
  `total_valuasi` decimal(15,2) NOT NULL,
  `metode` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `valuasi_fifos`
--

INSERT INTO `valuasi_fifos` (`id`, `sku`, `nama_barang`, `qty`, `valuasi`, `harga_unit`, `total_valuasi`, `metode`, `created_at`, `updated_at`) VALUES
(1, 'SKU-A', 'Kemeja Formal', 100, 'Rp 500.000', 50000.00, 5000000.00, 'FIFO', '2026-07-01 21:18:34', '2026-07-01 21:18:34'),
(2, 'SKU-B', 'Celana Jeans', 200, 'Rp 32.000', 32000.00, 6400000.00, 'Average', '2026-07-01 21:18:34', '2026-07-01 21:18:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendoreos`
--

CREATE TABLE `vendoreos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_vendor` varchar(255) NOT NULL,
  `nama_vendor` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `pic_vendor` varchar(255) NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `produk_jasa` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `tanggal_terakhir_order` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendoreos`
--

INSERT INTO `vendoreos` (`id`, `kode_vendor`, `nama_vendor`, `kategori`, `alamat`, `pic_vendor`, `no_telp`, `produk_jasa`, `rating`, `status`, `tanggal_terakhir_order`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'VND-001', 'PT Tekstil Nusantara', 'Bahan', 'Solo, Jawa Tengah', 'Sari', '0812-3333-8888', 'Kain, Furing', 4, 'Aktif', '2025-07-01', 'Tepat waktu', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendor_performances`
--

CREATE TABLE `vendor_performances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `total_order` int(11) NOT NULL,
  `ketepatan_waktu` double NOT NULL,
  `kualitas_barang` double NOT NULL,
  `komplain` int(11) NOT NULL,
  `penilaian_akhir` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendor_performances`
--

INSERT INTO `vendor_performances` (`id`, `vendor`, `total_order`, `ketepatan_waktu`, `kualitas_barang`, `komplain`, `penilaian_akhir`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'PT. Sumber Jaya', 10, 90, 95, 1, 'A', 'Vendor andalan', NULL, NULL),
(2, 'CV. Textile Makmur', 8, 80, 88, 2, 'B', 'Perlu follow-up rutin', NULL, NULL),
(3, 'CV. Konveksi Mitra', 5, 100, 92, 0, 'A', 'Sangat direkomendasikan', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendor_pricelists`
--

CREATE TABLE `vendor_pricelists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `kode_barang` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `harga_per_unit` int(11) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `diskon` int(11) NOT NULL DEFAULT 0,
  `minimal_order` int(11) NOT NULL,
  `lead_time` int(11) NOT NULL,
  `tanggal_berlaku` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vendor_pricelists`
--

INSERT INTO `vendor_pricelists` (`id`, `vendor`, `kode_barang`, `nama_barang`, `harga_per_unit`, `satuan`, `diskon`, `minimal_order`, `lead_time`, `tanggal_berlaku`, `created_at`, `updated_at`) VALUES
(1, 'PT. Sumber Jaya', 'BRG-001', 'Bahan Katun 20s', 150000, 'roll', 5, 50, 3, '2025-06-01', NULL, NULL),
(2, 'CV. Textile Makmur', 'BRG-002', 'Furing D300', 80000, 'meter', 0, 10, 2, '2025-06-01', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `virtual_accounts`
--

CREATE TABLE `virtual_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `va_number` varchar(255) DEFAULT NULL,
  `bank_code` varchar(10) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `expected_amount` decimal(15,2) DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `expired_at` datetime DEFAULT NULL,
  `status` enum('active','paid','partial','expired','failed') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `virtual_accounts`
--

INSERT INTO `virtual_accounts` (`id`, `customer_id`, `invoice_id`, `va_number`, `bank_code`, `bank_name`, `expected_amount`, `paid_amount`, `expired_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 12345, '700123456789', 'BCA', 'Bank Central Asia', 1500000.00, 0.00, '2026-07-09 04:18:29', 'active', '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(2, 2, 2345, '800987654321', 'BNI', 'Bank Negara Indonesia', 2000000.00, 2000000.00, '2026-07-05 04:18:29', 'paid', '2026-07-01 21:18:29', '2026-07-01 21:18:29'),
(3, 3, 3456, '900123987654', 'MANDIRI', 'Bank Mandiri', 1000000.00, 500000.00, '2026-07-03 04:18:29', 'partial', '2026-07-01 21:18:29', '2026-07-01 21:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `waste_managementos`
--

CREATE TABLE `waste_managementos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_limbah` varchar(255) NOT NULL,
  `volume` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `cara_penanganan` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `waste_managementos`
--

INSERT INTO `waste_managementos` (`id`, `tanggal`, `jenis_limbah`, `volume`, `lokasi`, `cara_penanganan`, `vendor`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, '2025-06-30', 'Potongan Kain', '150 kg', 'Line Jahit A', 'Didaur ulang', 'CV Hijau Lestari', 'Terangkut', 'Dipilah per warna', '2026-07-01 21:18:39', '2026-07-01 21:18:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `work_centers`
--

CREATE TABLE `work_centers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `routing_id` varchar(255) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `step` int(11) NOT NULL,
  `deskripsi_proses` varchar(255) NOT NULL,
  `work_center` varchar(255) NOT NULL,
  `durasi_setup` int(11) NOT NULL,
  `durasi_proses_per_unit` varchar(255) NOT NULL,
  `tools` varchar(255) DEFAULT NULL,
  `inspektor` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `work_centers`
--

INSERT INTO `work_centers` (`id`, `routing_id`, `nama_produk`, `step`, `deskripsi_proses`, `work_center`, `durasi_setup`, `durasi_proses_per_unit`, `tools`, `inspektor`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'RTG-KEM001', 'Kemeja Pria Formal', 1, 'Potong Kain', 'Cutting', 10, '2 mnt', 'Mesin Potong', '-', 'Cutting meja panjang', '2026-07-01 21:18:36', '2026-07-01 21:18:36'),
(2, 'RTG-KEM001', 'Kemeja Pria Formal', 2, 'Jahit Lengan', 'Sewing', 5, '3 mnt', 'Mesin Jahit', 'QC A', 'Cek benang kuat', '2026-07-01 21:18:36', '2026-07-01 21:18:36'),
(3, 'RTG-KEM001', 'Kemeja Pria Formal', 3, 'Pasang Kancing', 'Assembly', 3, '1 mnt', 'Tang Kancing', 'QC B', 'Posisi kancing rapi', '2026-07-01 21:18:36', '2026-07-01 21:18:36'),
(4, 'RTG-KEM001', 'Kemeja Pria Formal', 4, 'QC Akhir', 'QC', 2, '1 mnt', 'Visual', 'Supervisor QC', 'Diuji tarik', '2026-07-01 21:18:36', '2026-07-01 21:18:36'),
(5, 'RTG-KEM001', 'Kemeja Pria Formal', 5, 'Packing', 'Packing', 1, '1 mnt', 'Manual', '-', 'Plastik + label', '2026-07-01 21:18:36', '2026-07-01 21:18:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `work_orderos`
--

CREATE TABLE `work_orderos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_wo` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_wo` varchar(255) NOT NULL,
  `divisi` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `estimasi_selesai` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `work_orderos`
--

INSERT INTO `work_orderos` (`id`, `no_wo`, `tanggal`, `jenis_wo`, `divisi`, `pic`, `status`, `estimasi_selesai`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'WO-001', '2025-06-30', 'Perbaikan Mesin', 'Produksi', 'Teknisi Jono', 'Dalam Pengerjaan', '2025-07-02', 'Ganti komponen sensor', '2026-07-01 21:18:38', '2026-07-01 21:18:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `work_orders`
--

CREATE TABLE `work_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wo_no` varchar(255) NOT NULL,
  `produk` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `tgl_pembuatan` date NOT NULL,
  `jadwal_mulai` date NOT NULL,
  `jadwal_selesai` date NOT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `operator` varchar(255) NOT NULL,
  `shift` varchar(255) NOT NULL,
  `lokasi_produksi` varchar(255) NOT NULL,
  `prioritas` varchar(255) NOT NULL,
  `referensi_so` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `work_orders`
--

INSERT INTO `work_orders` (`id`, `wo_no`, `produk`, `jumlah`, `status`, `tgl_pembuatan`, `jadwal_mulai`, `jadwal_selesai`, `tgl_selesai`, `operator`, `shift`, `lokasi_produksi`, `prioritas`, `referensi_so`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'WO-001', 'Kemeja Pria Formal', 100, 'In Progress', '2025-06-01', '2025-06-03', '2025-06-07', NULL, 'Andi', 'Pagi', 'Pabrik A', 'Tinggi', 'SO-1201', 'Bahan lengkap', '2026-07-01 21:18:36', '2026-07-01 21:18:36');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `api_integrations`
--
ALTER TABLE `api_integrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `appointments`
--
ALTER TABLE `appointments`
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
-- Indeks untuk tabel `audit_assets`
--
ALTER TABLE `audit_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `auto_reconciles`
--
ALTER TABLE `auto_reconciles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `billof_materials`
--
ALTER TABLE `billof_materials`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `budgeting_proyeks`
--
ALTER TABLE `budgeting_proyeks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `buku_besars`
--
ALTER TABLE `buku_besars`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `cash_flow_forecasts`
--
ALTER TABLE `cash_flow_forecasts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `contact_management`
--
ALTER TABLE `contact_management`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `contract_handlings`
--
ALTER TABLE `contract_handlings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cost_estimations`
--
ALTER TABLE `cost_estimations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `crm_dashboards`
--
ALTER TABLE `crm_dashboards`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `crm_notifications`
--
ALTER TABLE `crm_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `crm_permissions`
--
ALTER TABLE `crm_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `crm_prospeks`
--
ALTER TABLE `crm_prospeks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_prospeks_kode_prospek_unique` (`kode_prospek`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customer_ratings`
--
ALTER TABLE `customer_ratings`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `data_kirs`
--
ALTER TABLE `data_kirs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_kirs_no_uji_berkala_unique` (`no_uji_berkala`);

--
-- Indeks untuk tabel `data_members`
--
ALTER TABLE `data_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_members_no_polisi_unique` (`no_polisi`);

--
-- Indeks untuk tabel `data_services`
--
ALTER TABLE `data_services`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `data_stnks`
--
ALTER TABLE `data_stnks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_stnks_no_polisi_unique` (`no_polisi`);

--
-- Indeks untuk tabel `departemens`
--
ALTER TABLE `departemens`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `email_domains`
--
ALTER TABLE `email_domains`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `e_bupots`
--
ALTER TABLE `e_bupots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `e_bupots_nomor_bukti_unique` (`nomor_bukti`);

--
-- Indeks untuk tabel `e_fakturs`
--
ALTER TABLE `e_fakturs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `e_fakturs_nomor_faktur_unique` (`nomor_faktur`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `helpdesk_ticketings`
--
ALTER TABLE `helpdesk_ticketings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `h_s_eos`
--
ALTER TABLE `h_s_eos`
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
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `invoice_periodes`
--
ALTER TABLE `invoice_periodes`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `invoice_remaks`
--
ALTER TABLE `invoice_remaks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `inv_kontraks`
--
ALTER TABLE `inv_kontraks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `inv_penawarans`
--
ALTER TABLE `inv_penawarans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `inv_penawaran_items`
--
ALTER TABLE `inv_penawaran_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `inv_summaries`
--
ALTER TABLE `inv_summaries`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `itasset_management`
--
ALTER TABLE `itasset_management`
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
-- Indeks untuk tabel `komisi_sales`
--
ALTER TABLE `komisi_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `konsolidasi_multi_perusahaans`
--
ALTER TABLE `konsolidasi_multi_perusahaans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kontrak_aktifs`
--
ALTER TABLE `kontrak_aktifs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `k_p_i_appraisals`
--
ALTER TABLE `k_p_i_appraisals`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_agings`
--
ALTER TABLE `laporan_agings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_cogs`
--
ALTER TABLE `laporan_cogs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_deads`
--
ALTER TABLE `laporan_deads`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_movements`
--
ALTER TABLE `laporan_movements`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lead_management`
--
ALTER TABLE `lead_management`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lead_sources`
--
ALTER TABLE `lead_sources`
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
-- Indeks untuk tabel `live_chats`
--
ALTER TABLE `live_chats`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `logisticsos`
--
ALTER TABLE `logisticsos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `loyalties`
--
ALTER TABLE `loyalties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loyalties_id_program_unique` (`id_program`);

--
-- Indeks untuk tabel `maintenanceos`
--
ALTER TABLE `maintenanceos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `marketing_automations`
--
ALTER TABLE `marketing_automations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `material_requests`
--
ALTER TABLE `material_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `multi_gudangs`
--
ALTER TABLE `multi_gudangs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `network_monitorings`
--
ALTER TABLE `network_monitorings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `operations_dashboards`
--
ALTER TABLE `operations_dashboards`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `otomatisasis`
--
ALTER TABLE `otomatisasis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `otomatisasis_workflow_id_unique` (`workflow_id`);

--
-- Indeks untuk tabel `partner_programs`
--
ALTER TABLE `partner_programs`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `pelacakans`
--
ALTER TABLE `pelacakans`
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
-- Indeks untuk tabel `penggunaan_asuransis`
--
ALTER TABLE `penggunaan_asuransis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penggunaan_asuransis_no_polisi_unique` (`no_polisi`);

--
-- Indeks untuk tabel `penggunaan_g_p_s`
--
ALTER TABLE `penggunaan_g_p_s`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `pickings`
--
ALTER TABLE `pickings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pipelines`
--
ALTER TABLE `pipelines`
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
-- Indeks untuk tabel `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `reminder_tagihans`
--
ALTER TABLE `reminder_tagihans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `requestfor_quotations`
--
ALTER TABLE `requestfor_quotations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `reservasis`
--
ALTER TABLE `reservasis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservasis_no_pol_unique` (`no_pol`);

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
-- Indeks untuk tabel `route_plans`
--
ALTER TABLE `route_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

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
-- Indeks untuk tabel `stockos`
--
ALTER TABLE `stockos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `stock_movementos`
--
ALTER TABLE `stock_movementos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_movementos_no_transaksi_unique` (`no_transaksi`);

--
-- Indeks untuk tabel `stok_minimums`
--
ALTER TABLE `stok_minimums`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `struktur_organisasis`
--
ALTER TABLE `struktur_organisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `supplieros`
--
ALTER TABLE `supplieros`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `s_o_p_managementos`
--
ALTER TABLE `s_o_p_managementos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `target_penjualans`
--
ALTER TABLE `target_penjualans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `team_management`
--
ALTER TABLE `team_management`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `trackings`
--
ALTER TABLE `trackings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `trackingutms`
--
ALTER TABLE `trackingutms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trackingutms_sku_unique` (`sku`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `user_accesses`
--
ALTER TABLE `user_accesses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `valuasi_fifos`
--
ALTER TABLE `valuasi_fifos`
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
  ADD UNIQUE KEY `virtual_accounts_va_number_unique` (`va_number`);

--
-- Indeks untuk tabel `waste_managementos`
--
ALTER TABLE `waste_managementos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `work_centers`
--
ALTER TABLE `work_centers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `work_orderos`
--
ALTER TABLE `work_orderos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `work_orders`
--
ALTER TABLE `work_orders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `ads_integrations`
--
ALTER TABLE `ads_integrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `afiliasis`
--
ALTER TABLE `afiliasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `aging_aps`
--
ALTER TABLE `aging_aps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `aging_ars`
--
ALTER TABLE `aging_ars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `api_integrations`
--
ALTER TABLE `api_integrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `approval_workflows`
--
ALTER TABLE `approval_workflows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `asset_dihapuskans`
--
ALTER TABLE `asset_dihapuskans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `audit_assets`
--
ALTER TABLE `audit_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `auto_reconciles`
--
ALTER TABLE `auto_reconciles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `billof_materials`
--
ALTER TABLE `billof_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `budgeting_proyeks`
--
ALTER TABLE `budgeting_proyeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `buku_besars`
--
ALTER TABLE `buku_besars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `cash_flow_forecasts`
--
ALTER TABLE `cash_flow_forecasts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `contact_management`
--
ALTER TABLE `contact_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contract_handlings`
--
ALTER TABLE `contract_handlings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `cost_estimations`
--
ALTER TABLE `cost_estimations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `crm_dashboards`
--
ALTER TABLE `crm_dashboards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `crm_notifications`
--
ALTER TABLE `crm_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `crm_permissions`
--
ALTER TABLE `crm_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `crm_prospeks`
--
ALTER TABLE `crm_prospeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `customer_ratings`
--
ALTER TABLE `customer_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `cuti_izins`
--
ALTER TABLE `cuti_izins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `cybersecurities`
--
ALTER TABLE `cybersecurities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `daftar_notaris`
--
ALTER TABLE `daftar_notaris`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `data_kirs`
--
ALTER TABLE `data_kirs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `data_members`
--
ALTER TABLE `data_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `data_services`
--
ALTER TABLE `data_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `data_stnks`
--
ALTER TABLE `data_stnks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `departemens`
--
ALTER TABLE `departemens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `devops`
--
ALTER TABLE `devops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `dokumentasi_assets`
--
ALTER TABLE `dokumentasi_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `dokumen_proyeks`
--
ALTER TABLE `dokumen_proyeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `dropshippings`
--
ALTER TABLE `dropshippings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `email_domains`
--
ALTER TABLE `email_domains`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `e_bupots`
--
ALTER TABLE `e_bupots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `e_fakturs`
--
ALTER TABLE `e_fakturs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hak_hukums`
--
ALTER TABLE `hak_hukums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `helpdesk_supports`
--
ALTER TABLE `helpdesk_supports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `helpdesk_ticketings`
--
ALTER TABLE `helpdesk_ticketings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `h_s_eos`
--
ALTER TABLE `h_s_eos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `induk_assets`
--
ALTER TABLE `induk_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `induk_proyeks`
--
ALTER TABLE `induk_proyeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invoice_periodes`
--
ALTER TABLE `invoice_periodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invoice_remaks`
--
ALTER TABLE `invoice_remaks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `inv_kontraks`
--
ALTER TABLE `inv_kontraks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `inv_penawarans`
--
ALTER TABLE `inv_penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `inv_penawaran_items`
--
ALTER TABLE `inv_penawaran_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `inv_summaries`
--
ALTER TABLE `inv_summaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `itasset_management`
--
ALTER TABLE `itasset_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT untuk tabel `komisi_sales`
--
ALTER TABLE `komisi_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `konsolidasi_multi_perusahaans`
--
ALTER TABLE `konsolidasi_multi_perusahaans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kontrak_aktifs`
--
ALTER TABLE `kontrak_aktifs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `k_p_i_appraisals`
--
ALTER TABLE `k_p_i_appraisals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `laporan_agings`
--
ALTER TABLE `laporan_agings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `laporan_cogs`
--
ALTER TABLE `laporan_cogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `laporan_deads`
--
ALTER TABLE `laporan_deads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `laporan_movements`
--
ALTER TABLE `laporan_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `lead_management`
--
ALTER TABLE `lead_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `litigasis`
--
ALTER TABLE `litigasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `live_chats`
--
ALTER TABLE `live_chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `logisticsos`
--
ALTER TABLE `logisticsos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `loyalties`
--
ALTER TABLE `loyalties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `maintenanceos`
--
ALTER TABLE `maintenanceos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `marketing_automations`
--
ALTER TABLE `marketing_automations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `material_requests`
--
ALTER TABLE `material_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT untuk tabel `multi_gudangs`
--
ALTER TABLE `multi_gudangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `network_monitorings`
--
ALTER TABLE `network_monitorings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `operations_dashboards`
--
ALTER TABLE `operations_dashboards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `otomatisasis`
--
ALTER TABLE `otomatisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `partner_programs`
--
ALTER TABLE `partner_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pelacakans`
--
ALTER TABLE `pelacakans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pembelian_proyeks`
--
ALTER TABLE `pembelian_proyeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pemeliharaan_assets`
--
ALTER TABLE `pemeliharaan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `penanggung_jawabs`
--
ALTER TABLE `penanggung_jawabs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `penawarans`
--
ALTER TABLE `penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `penggunaan_asuransis`
--
ALTER TABLE `penggunaan_asuransis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `penggunaan_g_p_s`
--
ALTER TABLE `penggunaan_g_p_s`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `penyusutan_assets`
--
ALTER TABLE `penyusutan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pergerakan_assets`
--
ALTER TABLE `pergerakan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `perolehan_assets`
--
ALTER TABLE `perolehan_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pickings`
--
ALTER TABLE `pickings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pipelines`
--
ALTER TABLE `pipelines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `policy_compliances`
--
ALTER TABLE `policy_compliances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `presensis`
--
ALTER TABLE `presensis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `pricelist_diskons`
--
ALTER TABLE `pricelist_diskons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `procurementos`
--
ALTER TABLE `procurementos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `project_costs`
--
ALTER TABLE `project_costs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `project_management`
--
ALTER TABLE `project_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `project_plannings`
--
ALTER TABLE `project_plannings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `project_risks`
--
ALTER TABLE `project_risks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `project_timelines`
--
ALTER TABLE `project_timelines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `purchaseros`
--
ALTER TABLE `purchaseros`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reminder_tagihans`
--
ALTER TABLE `reminder_tagihans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `requestfor_quotations`
--
ALTER TABLE `requestfor_quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `reservasis`
--
ALTER TABLE `reservasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `resign_offboardings`
--
ALTER TABLE `resign_offboardings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `retur_penjualans`
--
ALTER TABLE `retur_penjualans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `review_legals`
--
ALTER TABLE `review_legals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `route_plans`
--
ALTER TABLE `route_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `segmentasis`
--
ALTER TABLE `segmentasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sertifikasi_perizinans`
--
ALTER TABLE `sertifikasi_perizinans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `server_clouds`
--
ALTER TABLE `server_clouds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `shift_lemburs`
--
ALTER TABLE `shift_lemburs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `signature_dokumens`
--
ALTER TABLE `signature_dokumens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `skill_matrices`
--
ALTER TABLE `skill_matrices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `software_licenses`
--
ALTER TABLE `software_licenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sosmedps`
--
ALTER TABLE `sosmedps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `stockos`
--
ALTER TABLE `stockos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `stock_movementos`
--
ALTER TABLE `stock_movementos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `stok_minimums`
--
ALTER TABLE `stok_minimums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `struktur_organisasis`
--
ALTER TABLE `struktur_organisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT untuk tabel `supplieros`
--
ALTER TABLE `supplieros`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `s_o_p_managementos`
--
ALTER TABLE `s_o_p_managementos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `target_penjualans`
--
ALTER TABLE `target_penjualans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `team_management`
--
ALTER TABLE `team_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `trackings`
--
ALTER TABLE `trackings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `trackingutms`
--
ALTER TABLE `trackingutms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user_accesses`
--
ALTER TABLE `user_accesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `valuasi_fifos`
--
ALTER TABLE `valuasi_fifos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `vendoreos`
--
ALTER TABLE `vendoreos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `vendor_performances`
--
ALTER TABLE `vendor_performances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `vendor_pricelists`
--
ALTER TABLE `vendor_pricelists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `virtual_accounts`
--
ALTER TABLE `virtual_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `waste_managementos`
--
ALTER TABLE `waste_managementos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `work_centers`
--
ALTER TABLE `work_centers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `work_orderos`
--
ALTER TABLE `work_orderos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `work_orders`
--
ALTER TABLE `work_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
