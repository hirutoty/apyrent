-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Jun 2026 pada 06.03
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
(1, 'Pembangunan Sistem Rental', 'Development', 15000000.00, 6000000.00, 9000000.00, 40.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 'Server & Hosting', 'Infrastructure', 5000000.00, 2500000.00, 2500000.00, 50.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 'Pembelian GPS', 'Operasional', 10000000.00, 7500000.00, 2500000.00, 75.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 'Promosi Rental', 'Marketing', 7000000.00, 3000000.00, 4000000.00, 42.86, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, 'Service Kendaraan', 'Maintenance', 12000000.00, 4500000.00, 7500000.00, 37.50, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 1, 'BCA Insurance', 'Jl. Sudirman No. 10 Jakarta', 'Andi Saputra', '081234567890', 'Bengkel Maju Motor', '082233445566', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 1, 'Adira Insurance', 'Jl. Malioboro No. 20 Yogyakarta', 'Budi Hartono', '081298765432', 'Bengkel Jaya Abadi', '085566778899', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 1, 'ACA Insurance', 'Jl. Pemuda No. 12 Semarang', 'Siti Rahma', '087712345678', 'Bengkel Berkah Mobil', '081122334455', '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 1, 1, 1, 'aktif', '2026-06-25', '2027-06-25', 12, 5000000.00, NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 2, 2, 2, 'aktif', '2026-06-25', '2026-12-25', 6, 2500000.00, NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 1, 3, 3, 'expired', '2025-10-25', '2026-06-20', 8, 3500000.00, NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 'JRNL-001', 'Pemasukan Rental Mobil', 'pemasukan', '2026-06-20', 1500000, 0, 1500000, 'rental', 'Pembayaran rental mobil dari customer A', '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(2, 'JRNL-002', 'Pembelian Service Kendaraan', 'pengeluaran', '2026-06-22', 0, 500000, 1000000, 'service', 'Biaya servis berkala kendaraan', '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(3, 'JRNL-003', 'Pajak Kendaraan', 'pengeluaran', '2026-06-24', 0, 200000, 800000, 'pajak', 'Pembayaran pajak kendaraan tahunan', '2026-06-25 11:53:21', '2026-06-25 11:53:21');

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
(1, 'BUPOT-001', '2026-06-15', 'PPh21', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '09.876.543.2-109.000', 'Budi Santoso', 5000000.00, 0.05, 250000.00, 'Approve', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 'BUPOT-002', '2026-06-17', 'PPh23', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '08.765.432.1-000.000', 'CV Sinar Abadi', 3000000.00, 0.02, 60000.00, 'Approve', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 'BUPOT-003', '2026-06-20', 'PPh26', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '07.654.321.0-999.000', 'UD Jaya Motor', 10000000.00, 0.10, 1000000.00, 'Draft', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21');

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
(1, '010.000-26.000001', '2026-06-15', 'Keluaran', '01.234.567.8-901.000', 'PT Rental Maju Jaya', 5000000.00, 550000.00, 0.00, 'terbit', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(2, '010.000-26.000002', '2026-06-20', 'Masukan', '09.876.543.2-109.000', 'PT Supplier Sparepart', 3000000.00, 330000.00, 0.00, 'draft', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(3, '010.000-26.000003', '2026-06-23', 'Keluaran', '07.111.222.3-444.000', 'CV Transport Jaya', 7500000.00, 825000.00, 0.00, 'terbit', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21');

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
(1, 1, 'GPS Tracker Nusantara', 'Jl. Ahmad Yani No. 12 Wonosobo', 'Rizky Pratama', '081234567890', 'Bengkel GPS Wonosobo', '082233445566', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 1, 'GPS Satelit Indonesia', 'Jl. Dieng No. 45 Banjarnegara', 'Andi Saputra', '081298765432', 'Bengkel Satelit Banjarnegara', '082112223333', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 1, 'Global Tracker GPS', 'Jl. Soekarno Hatta No. 8 Magelang', 'Fajar Nugroho', '081377788899', 'Tracker Service Magelang', '083344556677', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 1, 'Smart GPS Solution', 'Jl. Veteran No. 22 Temanggung', 'Dimas Setiawan', '081355566677', 'Smart GPS Garage', '082266778899', '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 1, 1, 'GT06N', 'aktif', '2026-01-25', '2027-01-25', 150000, 12, 'aktif', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 2, 2, 'TK103', 'aktif', '2025-08-25', '2026-06-30', 120000, 12, 'habis', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 3, 3, 'Concox JM01', 'nonaktif', '2025-06-25', '2026-06-23', 100000, 12, 'habis', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 1, 4, 'GT02A', 'nonaktif', '2026-04-25', '2027-04-25', 175000, 12, 'aktif', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 'PT Sinar Abadi', 'Sparepart', 5000000.00, 2000000.00, 3000000.00, '2026-07-05', 'belum_lunas', 'Pembelian sparepart mesin', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 'CV Mitra Jaya', 'Service', 2500000.00, 2500000.00, 0.00, '2026-06-20', 'lunas', 'Service kendaraan fleet', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 'PT Otomotif Nusantara', 'Aksesoris', 1200000.00, 500000.00, 700000.00, '2026-06-28', 'belum_lunas', 'Pembelian aksesoris mobil', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 'UD Jaya Mandiri', 'Ban', 3000000.00, 1000000.00, 2000000.00, '2026-07-10', 'belum_lunas', 'Pembelian ban kendaraan', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, 'PT Diesel Prima', 'Mesin', 8000000.00, 8000000.00, 0.00, '2026-06-23', 'lunas', 'Perbaikan mesin besar', '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
  `satuan` text DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `pengirim` text DEFAULT NULL,
  `staff` text DEFAULT NULL,
  `name_staff` text DEFAULT NULL,
  `direktur` text DEFAULT NULL,
  `name_direktur` text DEFAULT NULL,
  `status` enum('draft','partial','overdue','lunas') DEFAULT NULL,
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

INSERT INTO `invoices` (`id`, `penawaran_id`, `kontrak_id`, `kendaraan_id`, `type`, `invoice_no`, `order_no`, `customer_name`, `customer_address`, `contact_person`, `telephone`, `satuan`, `invoice_date`, `pengirim`, `staff`, `name_staff`, `direktur`, `name_direktur`, `status`, `payment_status`, `ppn`, `pph`, `total`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 2, 'perusahaan', 'INV-202606-0001', NULL, 'Budi Santoso', NULL, NULL, NULL, 'PCS', '2026-06-26', 'Aditia', 'Karyawan', 'Hirutoty', 'Agung', 'Hirutotyriawan D', 'draft', 'unpaid', 120000.00, 11.00, 150000.00, '2026-06-25 19:38:16', '2026-06-25 19:38:16'),
(2, 1, 1, 3, 'perusahaan', 'INV-202606-0002', NULL, 'PT. JayaAA', 'Ds. Lumiring, Rt03 Rw02, Kecamatan Sukoharjo, Kab Wonosobo', 'hiru', '085876047371', 'Hari', '2026-06-26', 'Aditia', 'Manajer', 'Hirutoti Riawan', 'Direktur Utama', 'Hiru Riawan', 'draft', 'unpaid', 1.00, 12000.00, 150000.00, '2026-06-25 19:54:15', '2026-06-25 20:36:03');

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
(1, 2, 10000.00, '2026-06-26', 'Transfer', 'TRX-20260626-00001', 'uploads/payment/1782444256_6a3df0e03f15b.pdf', 'Verified', '2026-06-25 20:24:16', '2026-06-25 20:26:06');

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
  `qty` int(10) UNSIGNED DEFAULT 1,
  `price` decimal(15,2) DEFAULT 0.00,
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

--
-- Dumping data untuk tabel `inv_kontraks`
--

INSERT INTO `inv_kontraks` (`id`, `penawaran_id`, `no_kontrak`, `tanggal_kontrak`, `perjanjian_pembayaran`, `pihak_pertama`, `contact_pertama`, `pihak_kedua`, `contact_kedua`, `file_kontrak`, `file_persyaratan`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'KTR-202606-0001', '2026-06-26', '2026-07-10', 'Agung', '0898999999', 'Riawan', '00998888888', 'uploads/kontrak/1782444936_apyrentnew (10).sql', 'uploads/kontrak/1782444936_apyrentnew (9).sql', 'pending', '2026-06-25 20:35:36', '2026-06-25 20:35:36');

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
(1, 'PNW-202606-0001', '2026-06-26', 'PT Maju Jaya', 'dfdf', 'Sewa Kendaraan Operasional 1', 'Budi Santoso', '086785746352', 'Aditia', 2, 'Karyawan', 'Hirutoty', 'Agung', 'Hirutotyriawan D', 'pending', '200000', NULL, NULL, '2026-06-25 20:34:37', '2026-06-25 20:34:37');

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
(1, 1, 1, 1, '2026', 200000.00, 1, 'Hari', '2026-06-25 20:34:37', '2026-06-25 20:34:37');

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
(1, 1, 1, 2, 'GT06N2222', 1000000.00, 1000000.00, 0.00, 'Paid', '2026-06-25 20:39:52', '2026-06-25 20:40:06'),
(2, NULL, NULL, 2, 'GT06N2222', 20000.00, 0.00, 20000.00, 'Unpaid', '2026-06-25 20:41:10', '2026-06-25 20:46:54');

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
(1, 1, 'Mobil SUV', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 1, 'Mobil MPV', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 1, 'Mobil Sedan', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 1, 'Pickup', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, 1, 'Truck', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(6, 1, 'Bus Pariwisata', '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 'All Risk', 'Menanggung kerusakan ringan dan berat', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 'TLO', 'Total Loss Only', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 'Comprehensive', 'Perlindungan menyeluruh kendaraan', '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 1, 1, 'AA 1234 ZX', NULL, 'Budi Santoso', 'Wonosobo', 'Toyota Avanza', '2021', '2021', '1500 CC', 'Hitam', 'MHKS12345678901', 'ENG123456', 'BPKB001122', 'Hitam', 'Pertalite', 'AA', '123456', 350000, 50000, 1000000, NULL, '2027-06-25', 45000, 5000, 6, 40000, '2026-04-25', 'aman', 'tersedia', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 1, 2, 'AA 5678 YY', NULL, 'Joko Widodo', 'Magelang', 'Honda Brio', '2020', '2020', '1200 CC', 'Merah', 'MHKS987654321', 'ENG998877', 'BPKB9988', 'Hitam', 'Pertamax', 'AA', '654321', 300000, 45000, 900000, NULL, '2027-02-25', 60000, 5000, 6, 55000, '2026-01-25', 'service', 'disewa', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 1, 3, 'AA 9090 KK', NULL, 'Andi Saputra', 'Temanggung', 'Mitsubishi Xpander', '2022', '2022', '1500 CC', 'Putih', 'MTRX00112233', 'MESIN778899', 'BPKB778899', 'Hitam', 'Solar', 'AA', '998877', 450000, 70000, 1200000, NULL, '2027-04-25', 22000, 5000, 6, 18000, '2026-05-25', 'aman', 'tersedia', '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, '2026-06-15', 'INV-001', 1, 'Pemasukan', 'Cash', 'Pembayaran rental mobil', 1500000.00, 0.00, 1500000.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, '2026-06-17', 'EXP-001', 1, 'Pengeluaran', 'Transfer', 'Service kendaraan', 0.00, 500000.00, 1000000.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, '2026-06-20', 'INV-002', 1, 'Pemasukan', 'Transfer', 'Rental harian', 2000000.00, 0.00, 3000000.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, '2026-06-22', 'EXP-002', 1, 'Pengeluaran', 'Cash', 'Beli sparepart', 0.00, 750000.00, 2250000.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, '2026-06-25', 'INV-003', 1, 'Pemasukan', 'Cash', 'DP rental kendaraan', 1000000.00, 0.00, 3250000.00, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(6, '2026-06-26', 'TRX-20260626-00001', 1, 'Payment Invoice', 'Transfer', 'Pembayaran Invoice INV-202606-0002', 10000.00, 0.00, 3260000.00, '2026-06-25 20:26:06', '2026-06-25 20:26:06');

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
(1, 1, 'KIR-2026-001', '2026-12-25', 200000.00, NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 2, 'KIR-2026-002', '2026-10-25', 500000.00, NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 3, 'KIR-2026-003', '2026-06-15', 130000.00, NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 1, 'KIR-2026-004', '2027-06-25', 4500000.00, NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 'APY Rental', 25000000.00, 12000000.00, 13000000.00, '2026-06', '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(2, 'APY Rental', 30000000.00, 15000000.00, 15000000.00, '2026-05', '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(3, 'APY Rental', 18000000.00, 9000000.00, 9000000.00, '2026-04', '2026-06-25 11:53:21', '2026-06-25 11:53:21');

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
(1, 'Budi Santoso', '081234567890', 'Wonosobo, Jawa Tengah', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 'Rina Permata', '082145678901', 'Magelang, Jawa Tengah', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 'Agus Setiawan', '083156789012', 'Temanggung, Jawa Tengah', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 'Dewi Lestari', '085267890123', 'Banjarnegara, Jawa Tengah', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, 'Fajar Hidayat', '087878901234', 'Purwokerto, Jawa Tengah', '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(38, '2026_06_19_125311_create_pajak_histories_table', 1),
(39, '2026_06_20_033524_create_gps_histories_table', 1),
(40, '2026_06_20_063123_create_asuransi_history_table', 1),
(41, '2026_06_20_082104_create_kir_history_table', 1),
(42, '2026_06_20_171514_create_stnk_table', 1),
(43, '2026_06_20_185146_create_stnk_history_table', 1),
(44, '2026_06_21_194202_add_durasi_tahun_kelayakan_invoice_to_rentals_table', 1),
(45, '2026_06_25_074036_create_inv_penawarans_table', 1),
(46, '2026_06_25_074129_create_inv_penawaran_items_table', 1),
(47, '2026_06_25_074143_create_inv_kontraks_table', 1),
(48, '2026_06_25_074153_create_invoices_table', 1),
(49, '2026_06_25_074317_create_invoice_periodes_and_remaks_table', 1),
(50, '2026_06_25_074339_create_invoice_payments_and_summaries_table', 1);

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
(1, 1, 'Pajak Tahunan', 3500000.00, '2026-06-30', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 2, 'STNK', 2200000.00, '2026-07-07', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 3, 'Pajak 5 Tahunan', 5400000.00, '2026-06-23', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 2, 'Pajak Tahunan', 3100000.00, '2026-08-25', '2026-06-25', 'sudah_bayar', 'Pembayaran berhasil', 'bukti-pajak/bukti1.jpg', '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, 1, 'STNK', 1800000.00, '2026-06-28', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
  `status_rekonsiliasi` enum('matched','unmatched','Pending') NOT NULL DEFAULT 'Pending',
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rekonsiliasi_bank`
--

INSERT INTO `rekonsiliasi_bank` (`id`, `tanggal`, `deskripsi`, `reference_no`, `amount`, `currency`, `status_rekonsiliasi`, `invoice_id`, `created_at`, `updated_at`) VALUES
(1, '2026-06-13', 'Pembayaran rental masuk', 'BANK-INV-001', 1500000.00, 'IDR', 'matched', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(2, '2026-06-16', 'Transfer service kendaraan', 'BANK-INV-002', 500000.00, 'IDR', 'matched', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(3, '2026-06-19', 'Pembayaran deposit rental', 'BANK-INV-003', 2000000.00, 'IDR', 'Pending', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(4, '2026-06-22', 'Pembayaran sparepart', 'BANK-INV-004', 750000.00, 'IDR', 'matched', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(5, '2026-06-25', 'Pemasukan rental harian', 'BANK-INV-005', 1200000.00, 'IDR', 'Pending', NULL, '2026-06-25 11:53:21', '2026-06-25 11:53:21');

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
(1, 1, 1, 1, '2026-06-22 18:53:20', '2026-06-27 18:53:20', NULL, 0, 5, NULL, 1750000, 200000, 1950000, 'transfer', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-06-25 11:53:20', '2026-06-25 11:53:20', 0, NULL, NULL, NULL, NULL),
(2, 1, 2, 2, '2026-06-15 18:53:20', '2026-06-20 18:53:20', NULL, 6, 2, NULL, 1000000, 150000, 1150000, 'transfer', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-06-25 11:53:20', '2026-06-25 11:53:20', 0, NULL, NULL, NULL, NULL),
(3, 1, 3, 3, '2026-06-24 18:53:20', '2026-06-26 18:53:20', NULL, 4, 1, NULL, 550000, 50000, 600000, 'transfer', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-06-25 11:53:20', '2026-06-25 11:53:20', 0, NULL, NULL, NULL, NULL),
(4, 1, 2, 5, '2026-06-25 18:53:20', '2026-07-02 18:53:20', NULL, 0, 7, NULL, 4200000, 500000, 4700000, 'transfer', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-06-25 11:53:20', '2026-06-25 11:53:20', 0, NULL, NULL, NULL, NULL);

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
(1, 1, 'Ganti Oli Mesin', 350000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 1, 'Tune Up', 500000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 1, 'Spooring Balancing', 250000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 1, 'Ganti Kampas Rem', 450000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, 1, 'Service AC', 600000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(6, 1, 'Ganti Ban', 900000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(7, 1, 'Overhaul Mesin', 4500000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(8, 1, 'Cuci Mobil Premium', 75000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(9, 1, 'Ganti Aki', 1200000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(10, 1, 'Perbaikan Suspensi', 1800000, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 1, '2026-01-10', 25000, 'Layak', 350000, 'Ganti oli dan filter oli', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 1, '2026-02-15', 28000, 'Layak', 500000, 'Ganti kampas rem depan', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 2, '2026-03-05', 42000, 'Layak', 600000, 'Isi freon dan service AC', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 3, '2026-03-20', 55000, 'Tidak Layak', 1800000, 'Ganti 2 ban depan', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 1, 'Oli mesin sudah hitam dan rem berbunyi', 45000, 850000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-05', '2026-06-25 11:53:20', '2026-06-25 11:53:20', NULL),
(2, 2, 'AC tidak dingin', 52000, 600000, 'proses', NULL, 0, 0, 'stabil', '2026-06-15', '2026-06-25 11:53:20', '2026-06-25 11:53:20', NULL),
(3, 3, 'Ban depan aus', 70000, 1800000, 'selesai', NULL, 0, 0, 'stabil', '2026-05-26', '2026-06-25 11:53:20', '2026-06-25 11:53:20', NULL),
(4, 1, 'Mesin getar saat idle', 47000, 500000, 'proses', NULL, 0, 0, 'stabil', '2026-06-20', '2026-06-25 11:53:20', '2026-06-25 11:53:20', NULL),
(5, 2, 'Ganti aki', 55000, 1200000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-10', '2026-06-25 11:53:20', '2026-06-25 11:53:20', NULL);

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
('VM78KZvWKIEIgdVak4xYdh5VflYp9SA6l0TTSSUS', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicUZwUnNCaWlyT0JqZ0NJdjdMVHhwb001N1ZnVjdNZFFPY1BIS3VRZSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2ludm9pY2VzIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9idWt1YmVzYXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1782446603);

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
(1, 'PT Rental Kendaraan Indonesia', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-12345678', 'info@rentalkendaraan.co.id', 'https://rentalkendaraan.co.id', 'uploads/setting/1782446577_logo_icon.png', 'Bank BCA', '1234567890', 'PT Rental Kendaraan Indonesia', '10110', '2026-06-25 11:53:21', '2026-06-25 21:02:57');

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
(1, 1, 'CV Suku Cadang Motor', '081234567890', 'Oli Mesin', 50, 75000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(2, 1, 'PT Ban Indonesia', '082233445566', 'Ban Mobil', 20, 850000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(3, 1, 'Toko Sparepart Jaya', '081377788899', 'Aki Mobil', 15, 1200000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(4, 1, 'CV Audio Mobil', '081299988877', 'GPS Tracker', 10, 450000, '2026-06-25 11:53:20', '2026-06-25 11:53:20'),
(5, 1, 'PT Diesel Utama', '082122334455', 'Filter Solar', 40, 95000, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
(1, 'Test User', 'testuser', 'test@example.com', '$2y$12$uebjNuzRLKPFhfhSVM4LgePvWmgb9Ss8SQoJqtNVY4Yds5/kh1.a.', '08123456789', NULL, 'superadmin', 'aktif', NULL, '2026-06-25 11:53:20', '2026-06-25 11:53:20');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `virtual_accounts`
--

INSERT INTO `virtual_accounts` (`id`, `va_number`, `member_id`, `invoice_id`, `bukti_pembayaran`, `bank`, `expected_amount`, `paid_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'VA-22973795', 1, NULL, NULL, 'bca', 1500000.00, 0.00, 'Pending', '2026-06-25 11:53:21', '2026-06-25 11:53:21'),
(2, 'VA-93508357', 1, NULL, NULL, 'bni', 2500000.00, 2500000.00, 'paid', '2026-06-25 11:53:21', '2026-06-25 11:53:21');

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
-- Indeks untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_penawaran_id_foreign` (`penawaran_id`),
  ADD KEY `invoices_kontrak_id_foreign` (`kontrak_id`),
  ADD KEY `invoices_kendaraan_id_foreign` (`kendaraan_id`);

--
-- Indeks untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_payments_invoice_id_foreign` (`invoice_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `asuransi_kendaraan`
--
ALTER TABLE `asuransi_kendaraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hutang_vendors`
--
ALTER TABLE `hutang_vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `inv_penawarans`
--
ALTER TABLE `inv_penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `inv_penawaran_items`
--
ALTER TABLE `inv_penawaran_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `inv_summaries`
--
ALTER TABLE `inv_summaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kir`
--
ALTER TABLE `kir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kir_history`
--
ALTER TABLE `kir_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `pajak_histories`
--
ALTER TABLE `pajak_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pajak_kendaraans`
--
ALTER TABLE `pajak_kendaraans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `rekonsiliasi_bank`
--
ALTER TABLE `rekonsiliasi_bank`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `virtual_accounts`
--
ALTER TABLE `virtual_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Ketidakleluasaan untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_kontrak_id_foreign` FOREIGN KEY (`kontrak_id`) REFERENCES `inv_kontraks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD CONSTRAINT `invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

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
