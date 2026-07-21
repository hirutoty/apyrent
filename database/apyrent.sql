-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table apyrent.ads_integrations
CREATE TABLE IF NOT EXISTS `ads_integrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_iklan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_iklan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_aktif` date NOT NULL,
  `budget_harian` decimal(15,2) NOT NULL,
  `klik` int NOT NULL DEFAULT '0',
  `konversi` int NOT NULL DEFAULT '0',
  `biaya_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `penjualan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `roi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0%',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ads_integrations_id_iklan_unique` (`id_iklan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.ads_integrations: ~2 rows (approximately)
INSERT INTO `ads_integrations` (`id`, `id_iklan`, `nama_iklan`, `platform`, `tanggal_aktif`, `budget_harian`, `klik`, `konversi`, `biaya_total`, `penjualan`, `roi`, `created_at`, `updated_at`) VALUES
	(1, 'ADS001', 'Google Ads - Rental Mobil Jakarta', 'Google Ads', '2026-07-01', 500000.00, 350, 28, 15000000.00, 70000000.00, '367%', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'ADS002', 'Facebook Ads - Awareness Campaign', 'Meta Ads', '2026-07-05', 300000.00, 520, 35, 9000000.00, 52500000.00, '483%', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.afiliasis
CREATE TABLE IF NOT EXISTS `afiliasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_referral` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diskon_referral` decimal(15,2) NOT NULL,
  `bonus_pengajak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batas_waktu` date NOT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `afiliasis_id_program_unique` (`id_program`),
  UNIQUE KEY `afiliasis_kode_referral_unique` (`kode_referral`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.afiliasis: ~2 rows (approximately)
INSERT INTO `afiliasis` (`id`, `id_program`, `nama_program`, `kode_referral`, `diskon_referral`, `bonus_pengajak`, `batas_waktu`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'AFI001', 'Referral Teman', 'REF-APY001', 50000.00, 'Rp 75.000 kredit', '2026-12-31', 'Aktif', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'AFI002', 'Corporate Partner', 'REF-CORP001', 100000.00, 'Komisi 5%', '2026-12-31', 'Aktif', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.aging_aps
CREATE TABLE IF NOT EXISTS `aging_aps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_tagihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `jumlah` bigint NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hutang_vendor_id` bigint unsigned DEFAULT NULL,
  `status_lunas` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aging_aps_hutang_vendor_id_foreign` (`hutang_vendor_id`),
  CONSTRAINT `aging_aps_hutang_vendor_id_foreign` FOREIGN KEY (`hutang_vendor_id`) REFERENCES `hutang_vendors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.aging_aps: ~0 rows (approximately)

-- Dumping structure for table apyrent.aging_ars
CREATE TABLE IF NOT EXISTS `aging_ars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `total` decimal(15,2) DEFAULT '0.00',
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Belum Bayar','Bayar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Bayar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aging_ars_member_id_foreign` (`member_id`),
  KEY `aging_ars_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `aging_ars_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `aging_ars_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.aging_ars: ~0 rows (approximately)

-- Dumping structure for table apyrent.anggaran_proyek
CREATE TABLE IF NOT EXISTS `anggaran_proyek` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget` decimal(15,2) NOT NULL,
  `realisasi` decimal(15,2) NOT NULL,
  `sisa` decimal(15,2) NOT NULL DEFAULT '0.00',
  `persen_terpakai` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.anggaran_proyek: ~5 rows (approximately)
INSERT INTO `anggaran_proyek` (`id`, `proyek`, `kategori`, `budget`, `realisasi`, `sisa`, `persen_terpakai`, `created_at`, `updated_at`) VALUES
	(1, 'Pembangunan Sistem Rental', 'Development', 15000000.00, 6000000.00, 9000000.00, 40.00, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(2, 'Server & Hosting', 'Infrastructure', 5000000.00, 2500000.00, 2500000.00, 50.00, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(3, 'Pembelian GPS', 'Operasional', 10000000.00, 7500000.00, 2500000.00, 75.00, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(4, 'Promosi Rental', 'Marketing', 7000000.00, 3000000.00, 4000000.00, 42.86, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(5, 'Service Kendaraan', 'Maintenance', 12000000.00, 4500000.00, 7500000.00, 37.50, '2026-07-11 21:07:01', '2026-07-11 21:07:01');

-- Dumping structure for table apyrent.approval_workflows
CREATE TABLE IF NOT EXISTS `approval_workflows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan_approval` int NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_approver` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date DEFAULT NULL,
  `status_approval` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.approval_workflows: ~30 rows (approximately)
INSERT INTO `approval_workflows` (`id`, `id_po`, `urutan_approval`, `jabatan`, `nama_approver`, `tanggal`, `status_approval`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'PO-001', 1, 'Supervisor Pembelian', 'Budi Santoso', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'PO-001', 2, 'Manager Operasional', 'Rina Wulandari', '2026-06-19', 'Approved', 'Review urutan 2 untuk PO-001', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'PO-002', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-06-28', 'Rejected', 'Review urutan 1 untuk PO-002', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'PO-002', 2, 'Manager Operasional', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'PO-003', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-05-22', 'Approved', 'Review urutan 1 untuk PO-003', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(6, 'PO-003', 2, 'Manager Operasional', 'Budi Santoso', '2026-06-27', 'Rejected', 'Review urutan 2 untuk PO-003', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 'PO-004', 1, 'Supervisor Pembelian', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(8, 'PO-004', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-05-13', 'Approved', 'Review urutan 2 untuk PO-004', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(9, 'PO-005', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-05-31', 'Rejected', 'Review urutan 1 untuk PO-005', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(10, 'PO-005', 2, 'Manager Operasional', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(11, 'PO-006', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-05-19', 'Approved', 'Review urutan 1 untuk PO-006', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(12, 'PO-006', 2, 'Manager Operasional', 'Rina Wulandari', '2026-06-05', 'Rejected', 'Review urutan 2 untuk PO-006', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(13, 'PO-007', 1, 'Supervisor Pembelian', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(14, 'PO-007', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-07-08', 'Approved', 'Review urutan 2 untuk PO-007', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(15, 'PO-008', 1, 'Supervisor Pembelian', 'Hendra Wijaya', '2026-06-12', 'Rejected', 'Review urutan 1 untuk PO-008', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(16, 'PO-008', 2, 'Manager Operasional', 'Budi Santoso', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(17, 'PO-009', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-05-13', 'Approved', 'Review urutan 1 untuk PO-009', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(18, 'PO-009', 2, 'Manager Operasional', 'Agus Prasetyo', '2026-06-12', 'Rejected', 'Review urutan 2 untuk PO-009', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(19, 'PO-010', 1, 'Supervisor Pembelian', 'Dewi Kusuma', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(20, 'PO-010', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-05-20', 'Approved', 'Review urutan 2 untuk PO-010', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(21, 'PO-011', 1, 'Supervisor Pembelian', 'Budi Santoso', '2026-07-06', 'Rejected', 'Review urutan 1 untuk PO-011', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(22, 'PO-011', 2, 'Manager Operasional', 'Rina Wulandari', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(23, 'PO-012', 1, 'Supervisor Pembelian', 'Agus Prasetyo', '2026-06-21', 'Approved', 'Review urutan 1 untuk PO-012', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(24, 'PO-012', 2, 'Manager Operasional', 'Dewi Kusuma', '2026-05-26', 'Rejected', 'Review urutan 2 untuk PO-012', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(25, 'PO-013', 1, 'Supervisor Pembelian', 'Hendra Wijaya', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(26, 'PO-013', 2, 'Manager Operasional', 'Budi Santoso', '2026-06-18', 'Approved', 'Review urutan 2 untuk PO-013', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(27, 'PO-014', 1, 'Supervisor Pembelian', 'Rina Wulandari', '2026-06-28', 'Rejected', 'Review urutan 1 untuk PO-014', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(28, 'PO-014', 2, 'Manager Operasional', 'Agus Prasetyo', NULL, 'Pending', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(29, 'PO-015', 1, 'Supervisor Pembelian', 'Dewi Kusuma', '2026-07-04', 'Approved', 'Review urutan 1 untuk PO-015', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(30, 'PO-015', 2, 'Manager Operasional', 'Hendra Wijaya', '2026-06-27', 'Rejected', 'Review urutan 2 untuk PO-015', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.asset_dihapuskans
CREATE TABLE IF NOT EXISTS `asset_dihapuskans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_hapus` date NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_buku` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_akhir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.asset_dihapuskans: ~0 rows (approximately)

-- Dumping structure for table apyrent.asuransi
CREATE TABLE IF NOT EXISTS `asuransi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nama_asuransi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `nama_marketing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_marketing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bengkel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_bengkel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asuransi_user_id_foreign` (`user_id`),
  CONSTRAINT `asuransi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.asuransi: ~3 rows (approximately)
INSERT INTO `asuransi` (`id`, `user_id`, `nama_asuransi`, `alamat`, `nama_marketing`, `kontak_marketing`, `nama_bengkel`, `kontak_bengkel`, `created_at`, `updated_at`) VALUES
	(1, 1, 'BCA Insurance', 'Jl. Sudirman No. 10 Jakarta', 'Andi Saputra', '081234567890', 'Bengkel Maju Motor', '082233445566', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(2, 1, 'Adira Insurance', 'Jl. Malioboro No. 20 Yogyakarta', 'Budi Hartono', '081298765432', 'Bengkel Jaya Abadi', '085566778899', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(3, 1, 'ACA Insurance', 'Jl. Pemuda No. 12 Semarang', 'Siti Rahma', '087712345678', 'Bengkel Berkah Mobil', '081122334455', '2026-07-11 21:07:01', '2026-07-11 21:07:01');

-- Dumping structure for table apyrent.asuransi_history
CREATE TABLE IF NOT EXISTS `asuransi_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asuransi_kendaraan_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `asuransi_id` bigint unsigned NOT NULL,
  `jenis_asuransi_id` bigint unsigned NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_berakhir` date NOT NULL,
  `durasi_bulan` int NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_kendaraan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asuransi_history_asuransi_kendaraan_id_foreign` (`asuransi_kendaraan_id`),
  KEY `asuransi_history_kendaraan_id_foreign` (`kendaraan_id`),
  KEY `asuransi_history_asuransi_id_foreign` (`asuransi_id`),
  KEY `asuransi_history_jenis_asuransi_id_foreign` (`jenis_asuransi_id`),
  CONSTRAINT `asuransi_history_asuransi_id_foreign` FOREIGN KEY (`asuransi_id`) REFERENCES `asuransi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asuransi_history_asuransi_kendaraan_id_foreign` FOREIGN KEY (`asuransi_kendaraan_id`) REFERENCES `asuransi_kendaraan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asuransi_history_jenis_asuransi_id_foreign` FOREIGN KEY (`jenis_asuransi_id`) REFERENCES `jenis_asuransi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asuransi_history_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.asuransi_history: ~6 rows (approximately)
INSERT INTO `asuransi_history` (`id`, `asuransi_kendaraan_id`, `kendaraan_id`, `asuransi_id`, `jenis_asuransi_id`, `tgl_mulai`, `tgl_berakhir`, `durasi_bulan`, `biaya`, `tanggal_bayar`, `bukti_bayar`, `status_kendaraan`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, '2025-03-12', '2026-03-12', 12, 8000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784016798_Invoice-INV-202607-0002 (1).pdf', 'aktif', '2026-07-14 01:13:18', '2026-07-14 01:13:18', '2026-07-14 01:13:18'),
	(2, 1, 1, 1, 1, '2026-03-12', '2027-03-12', 12, 8000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784016825_Invoice-INV-202607-0002 (2).pdf', 'aktif', '2026-07-14 01:13:45', '2026-07-14 01:13:45', '2026-07-14 01:13:45'),
	(3, 1, 1, 1, 1, '2027-03-12', '2028-03-12', 12, 800000000.00, '2026-12-14', 'asuransi/bukti_bayar/1784025163_contoh invoice.jpeg', 'aktif', '2026-07-14 10:32:43', '2026-07-14 10:32:43', '2026-07-14 10:32:43'),
	(4, 1, 1, 1, 1, '2028-03-12', '2029-03-12', 12, 80000000000.00, '2026-12-14', 'asuransi/bukti_bayar/1784025257_contoh invoice.jpeg', 'aktif', '2026-12-13 17:00:00', '2026-07-14 10:34:17', '2026-07-14 10:34:17'),
	(5, 1, 1, 1, 1, '2029-03-12', '2030-03-12', 12, 8000000000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784026462_Invoice-INV-202607-0002 (1).pdf', 'aktif', '2026-07-13 17:00:00', '2026-07-14 10:54:22', '2026-07-14 10:54:22'),
	(6, 2, 2, 2, 2, '2025-12-12', '2026-12-12', 12, 1250000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784026552_Invoice-INV-202607-0002 (1).pdf', 'aktif', '2026-07-13 17:00:00', '2026-07-14 10:55:52', '2026-07-14 10:55:52'),
	(7, 3, 3, 3, 3, '2026-05-12', '2027-05-12', 12, 5000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784028566_Invoice-INV-202607-0002.pdf', 'aktif', '2026-07-13 17:00:00', '2026-07-14 11:29:26', '2026-07-14 11:29:26');

-- Dumping structure for table apyrent.asuransi_kendaraan
CREATE TABLE IF NOT EXISTS `asuransi_kendaraan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `asuransi_id` bigint unsigned NOT NULL,
  `jenis_asuransi_id` bigint unsigned NOT NULL,
  `status_kendaraan` enum('aktif','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `tgl_mulai` date NOT NULL,
  `tgl_berakhir` date NOT NULL,
  `durasi_bulan` int NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asuransi_kendaraan_kendaraan_id_foreign` (`kendaraan_id`),
  KEY `asuransi_kendaraan_asuransi_id_foreign` (`asuransi_id`),
  KEY `asuransi_kendaraan_jenis_asuransi_id_foreign` (`jenis_asuransi_id`),
  CONSTRAINT `asuransi_kendaraan_asuransi_id_foreign` FOREIGN KEY (`asuransi_id`) REFERENCES `asuransi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asuransi_kendaraan_jenis_asuransi_id_foreign` FOREIGN KEY (`jenis_asuransi_id`) REFERENCES `jenis_asuransi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asuransi_kendaraan_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.asuransi_kendaraan: ~50 rows (approximately)
INSERT INTO `asuransi_kendaraan` (`id`, `kendaraan_id`, `asuransi_id`, `jenis_asuransi_id`, `status_kendaraan`, `tgl_mulai`, `tgl_berakhir`, `durasi_bulan`, `biaya`, `tanggal_bayar`, `bukti_bayar`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 'aktif', '2029-03-12', '2030-03-12', 12, 8000000000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784026462_Invoice-INV-202607-0002 (1).pdf', '2026-07-11 21:07:01', '2026-07-14 10:54:22'),
	(2, 2, 2, 2, 'aktif', '2025-12-12', '2026-12-12', 12, 1250000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784026552_Invoice-INV-202607-0002 (1).pdf', '2026-07-11 21:07:01', '2026-07-14 10:55:52'),
	(3, 3, 3, 3, 'aktif', '2026-05-12', '2027-05-12', 12, 5000000.00, '2026-07-14', 'asuransi/bukti_bayar/1784028566_Invoice-INV-202607-0002.pdf', '2026-07-11 21:07:01', '2026-07-14 11:29:26'),
	(4, 4, 1, 1, 'aktif', '2025-10-12', '2027-10-12', 24, 15500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(5, 5, 2, 2, 'aktif', '2026-06-12', '2026-09-12', 3, 22500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(6, 6, 3, 3, 'aktif', '2026-02-12', '2026-08-12', 6, 12000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(7, 7, 1, 1, 'expired', '2024-11-12', '2025-11-12', 12, 12500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(8, 8, 2, 2, 'aktif', '2026-04-12', '2028-04-12', 24, 25000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(9, 9, 3, 3, 'expired', '2025-05-12', '2025-08-12', 3, 17500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(10, 10, 1, 1, 'expired', '2025-11-12', '2026-05-12', 6, 16000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(11, 11, 2, 2, 'aktif', '2026-04-12', '2027-04-12', 12, 8500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(12, 12, 3, 3, 'aktif', '2026-06-12', '2028-06-12', 24, 8000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(13, 13, 1, 1, 'expired', '2025-01-12', '2025-04-12', 3, 9500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(14, 14, 2, 2, 'expired', '2025-02-12', '2025-08-12', 6, 10500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(15, 15, 3, 3, 'aktif', '2026-03-12', '2027-03-12', 12, 6000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(16, 16, 1, 1, 'aktif', '2025-01-12', '2027-01-12', 24, 15000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(17, 17, 2, 2, 'expired', '2026-04-12', '2026-07-12', 3, 4000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(18, 18, 3, 3, 'aktif', '2026-04-12', '2026-10-12', 6, 2500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(19, 19, 1, 1, 'expired', '2025-05-12', '2026-05-12', 12, 12000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(20, 20, 2, 2, 'aktif', '2025-01-12', '2027-01-12', 24, 8500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(21, 21, 3, 3, 'aktif', '2026-06-12', '2026-09-12', 3, 2500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(22, 22, 1, 1, 'expired', '2025-12-12', '2026-06-12', 6, 4000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(23, 23, 2, 2, 'aktif', '2025-12-12', '2026-12-12', 12, 21500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(24, 24, 3, 3, 'aktif', '2025-04-12', '2027-04-12', 24, 12500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(25, 25, 1, 1, 'aktif', '2026-05-12', '2026-08-12', 3, 6500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(26, 26, 2, 2, 'expired', '2025-02-12', '2025-08-12', 6, 23000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(27, 27, 3, 3, 'aktif', '2026-01-12', '2027-01-12', 12, 11000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(28, 28, 1, 1, 'aktif', '2025-10-12', '2027-10-12', 24, 19500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(29, 29, 2, 2, 'expired', '2025-09-12', '2025-12-12', 3, 5000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(30, 30, 3, 3, 'expired', '2024-12-12', '2025-06-12', 6, 24500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(31, 31, 1, 1, 'aktif', '2026-05-12', '2027-05-12', 12, 8000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(32, 32, 2, 2, 'aktif', '2025-03-12', '2027-03-12', 24, 17000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(33, 33, 3, 3, 'expired', '2025-01-12', '2025-04-12', 3, 12000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(34, 34, 1, 1, 'expired', '2025-06-12', '2025-12-12', 6, 21000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(35, 35, 2, 2, 'aktif', '2026-01-12', '2027-01-12', 12, 24000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(36, 36, 3, 3, 'aktif', '2024-11-12', '2026-11-12', 24, 13500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(37, 37, 1, 1, 'expired', '2025-08-12', '2025-11-12', 3, 17500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(38, 38, 2, 2, 'expired', '2025-10-12', '2026-04-12', 6, 7500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(39, 39, 3, 3, 'expired', '2025-07-12', '2026-07-12', 12, 13500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(40, 40, 1, 1, 'aktif', '2025-05-12', '2027-05-12', 24, 24500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(41, 41, 2, 2, 'expired', '2024-11-12', '2025-02-12', 3, 14000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(42, 42, 3, 3, 'expired', '2025-09-12', '2026-03-12', 6, 8500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(43, 43, 1, 1, 'aktif', '2025-09-12', '2026-09-12', 12, 20500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(44, 44, 2, 2, 'aktif', '2025-03-12', '2027-03-12', 24, 16500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(45, 45, 3, 3, 'expired', '2025-12-12', '2026-03-12', 3, 4000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(46, 46, 1, 1, 'expired', '2025-10-12', '2026-04-12', 6, 16500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(47, 47, 2, 2, 'aktif', '2026-02-12', '2027-02-12', 12, 21000000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(48, 48, 3, 3, 'aktif', '2025-05-12', '2027-05-12', 24, 8500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(49, 49, 1, 1, 'expired', '2025-02-12', '2025-05-12', 3, 22500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(50, 50, 2, 2, 'aktif', '2026-04-12', '2026-10-12', 6, 9500000.00, NULL, NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01');

-- Dumping structure for table apyrent.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `relation_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relation_id` bigint unsigned NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachments_relation_type_relation_id_index` (`relation_type`,`relation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.attachments: ~29 rows (approximately)
INSERT INTO `attachments` (`id`, `relation_type`, `relation_id`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
	(1, 'pajak', 7, 'Invoice-INV-202607-0002 (3).pdf', 'pajak/attachments/1784016355_6a55ede33e0e3.pdf', 'pdf', 97294, '2026-07-14 01:05:55', '2026-07-14 01:05:55'),
	(2, 'pajak', 7, 'invoice_1.pdf', 'pajak/attachments/1784016355_6a55ede33efd5.pdf', 'pdf', 21526, '2026-07-14 01:05:55', '2026-07-14 01:05:55'),
	(3, 'pajak', 45, 'Invoice-INV-202607-0002 (2).pdf', 'pajak/attachments/1784016631_6a55eef77b6d4.pdf', 'pdf', 44819, '2026-07-14 01:10:31', '2026-07-14 01:10:31'),
	(4, 'pajak_history', 2, 'Invoice-INV-202607-0002 (2).pdf', 'pajak/attachments/1784016631_6a55eef77b6d4.pdf', 'pdf', 44819, '2026-07-14 01:10:31', '2026-07-14 01:10:31'),
	(5, 'pajak', 45, 'Invoice-INV-202607-0002 (1).pdf', 'pajak/attachments/1784016631_6a55eef77c9cb.pdf', 'pdf', 1366548, '2026-07-14 01:10:31', '2026-07-14 01:10:31'),
	(6, 'pajak_history', 2, 'Invoice-INV-202607-0002 (1).pdf', 'pajak/attachments/1784016631_6a55eef77c9cb.pdf', 'pdf', 1366548, '2026-07-14 01:10:31', '2026-07-14 01:10:31'),
	(7, 'asuransi', 1, 'Invoice-INV-202607-0002 (2).pdf', 'asuransi/attachments/1784016798_6a55ef9ec384a.pdf', 'pdf', 44819, '2026-07-14 01:13:18', '2026-07-14 01:13:18'),
	(8, 'asuransi_history', 1, 'Invoice-INV-202607-0002 (2).pdf', 'asuransi/attachments/1784016798_6a55ef9ec384a.pdf', 'pdf', 44819, '2026-07-14 01:13:18', '2026-07-14 01:13:18'),
	(9, 'asuransi', 1, 'Invoice-INV-202607-0002 (1).pdf', 'asuransi/attachments/1784016798_6a55ef9ec44cd.pdf', 'pdf', 1366548, '2026-07-14 01:13:18', '2026-07-14 01:13:18'),
	(10, 'asuransi_history', 1, 'Invoice-INV-202607-0002 (1).pdf', 'asuransi/attachments/1784016798_6a55ef9ec44cd.pdf', 'pdf', 1366548, '2026-07-14 01:13:18', '2026-07-14 01:13:18'),
	(11, 'gps', 1, 'Invoice-INV-202607-0002 (1).pdf', 'gps/attachments/1784017126_6a55f0e6709dc.pdf', 'pdf', 1366548, '2026-07-14 01:18:46', '2026-07-14 01:18:46'),
	(12, 'gps_history', 1, 'Invoice-INV-202607-0002 (1).pdf', 'gps/attachments/1784017126_6a55f0e6709dc.pdf', 'pdf', 1366548, '2026-07-14 01:18:46', '2026-07-14 01:18:46'),
	(13, 'gps', 1, 'Invoice-INV-202607-0002.pdf', 'gps/attachments/1784017126_6a55f0e671874.pdf', 'pdf', 1366256, '2026-07-14 01:18:46', '2026-07-14 01:18:46'),
	(14, 'gps_history', 1, 'Invoice-INV-202607-0002.pdf', 'gps/attachments/1784017126_6a55f0e671874.pdf', 'pdf', 1366256, '2026-07-14 01:18:46', '2026-07-14 01:18:46'),
	(15, 'kir', 1, 'Invoice-INV-202607-0002 (2).pdf', 'kir/attachments/1784017214_6a55f13ee9f59.pdf', 'pdf', 44819, '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(16, 'kir_history', 1, 'Invoice-INV-202607-0002 (2).pdf', 'kir/attachments/1784017214_6a55f13ee9f59.pdf', 'pdf', 44819, '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(17, 'kir', 1, 'Invoice-INV-202607-0002 (1).pdf', 'kir/attachments/1784017214_6a55f13eeb06e.pdf', 'pdf', 1366548, '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(18, 'kir_history', 1, 'Invoice-INV-202607-0002 (1).pdf', 'kir/attachments/1784017214_6a55f13eeb06e.pdf', 'pdf', 1366548, '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(19, 'kir', 1, 'Invoice-INV-202607-0002.pdf', 'kir/attachments/1784017214_6a55f13eebceb.pdf', 'pdf', 1366256, '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(20, 'kir_history', 1, 'Invoice-INV-202607-0002.pdf', 'kir/attachments/1784017214_6a55f13eebceb.pdf', 'pdf', 1366256, '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(21, 'pajak_history', 4, 'Invoice-INV-202607-0002 (3).pdf', 'pajak/attachments/1784016355_6a55ede33e0e3.pdf', 'pdf', 97294, '2026-07-14 10:30:08', '2026-07-14 10:30:08'),
	(22, 'pajak_history', 4, 'invoice_1.pdf', 'pajak/attachments/1784016355_6a55ede33efd5.pdf', 'pdf', 21526, '2026-07-14 10:30:08', '2026-07-14 10:30:08'),
	(23, 'asuransi_history', 3, 'Invoice-INV-202607-0002 (2).pdf', 'asuransi/attachments/1784016798_6a55ef9ec384a.pdf', 'pdf', 44819, '2026-07-14 10:32:43', '2026-07-14 10:32:43'),
	(24, 'asuransi_history', 3, 'Invoice-INV-202607-0002 (1).pdf', 'asuransi/attachments/1784016798_6a55ef9ec44cd.pdf', 'pdf', 1366548, '2026-07-14 10:32:43', '2026-07-14 10:32:43'),
	(25, 'asuransi_history', 4, 'Invoice-INV-202607-0002 (2).pdf', 'asuransi/attachments/1784016798_6a55ef9ec384a.pdf', 'pdf', 44819, '2026-07-14 10:34:17', '2026-07-14 10:34:17'),
	(26, 'asuransi_history', 4, 'Invoice-INV-202607-0002 (1).pdf', 'asuransi/attachments/1784016798_6a55ef9ec44cd.pdf', 'pdf', 1366548, '2026-07-14 10:34:17', '2026-07-14 10:34:17'),
	(27, 'gps_history', 3, 'Invoice-INV-202607-0002 (1).pdf', 'gps/attachments/1784017126_6a55f0e6709dc.pdf', 'pdf', 1366548, '2026-07-14 10:40:04', '2026-07-14 10:40:04'),
	(28, 'gps_history', 3, 'Invoice-INV-202607-0002.pdf', 'gps/attachments/1784017126_6a55f0e671874.pdf', 'pdf', 1366256, '2026-07-14 10:40:04', '2026-07-14 10:40:04'),
	(29, 'kir_history', 3, 'Invoice-INV-202607-0002 (2).pdf', 'kir/attachments/1784017214_6a55f13ee9f59.pdf', 'pdf', 44819, '2026-07-14 10:41:57', '2026-07-14 10:41:57'),
	(30, 'kir_history', 3, 'Invoice-INV-202607-0002 (1).pdf', 'kir/attachments/1784017214_6a55f13eeb06e.pdf', 'pdf', 1366548, '2026-07-14 10:41:57', '2026-07-14 10:41:57'),
	(31, 'kir_history', 3, 'Invoice-INV-202607-0002.pdf', 'kir/attachments/1784017214_6a55f13eebceb.pdf', 'pdf', 1366256, '2026-07-14 10:41:57', '2026-07-14 10:41:57'),
	(32, 'asuransi_history', 5, 'Invoice-INV-202607-0002 (2).pdf', 'asuransi/attachments/1784016798_6a55ef9ec384a.pdf', 'pdf', 44819, '2026-07-14 10:54:22', '2026-07-14 10:54:22'),
	(33, 'asuransi_history', 5, 'Invoice-INV-202607-0002 (1).pdf', 'asuransi/attachments/1784016798_6a55ef9ec44cd.pdf', 'pdf', 1366548, '2026-07-14 10:54:22', '2026-07-14 10:54:22');

-- Dumping structure for table apyrent.audit_assets
CREATE TABLE IF NOT EXISTS `audit_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_audit` date NOT NULL,
  `diperiksa_oleh` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_fisik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `temuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tindakan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.audit_assets: ~0 rows (approximately)

-- Dumping structure for table apyrent.biaya_operasional_kendaraans
CREATE TABLE IF NOT EXISTS `biaya_operasional_kendaraans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.biaya_operasional_kendaraans: ~0 rows (approximately)

-- Dumping structure for table apyrent.biaya_tambahans
CREATE TABLE IF NOT EXISTS `biaya_tambahans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `nama_tambahan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biaya` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `biaya_tambahans_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `biaya_tambahans_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.biaya_tambahans: ~0 rows (approximately)

-- Dumping structure for table apyrent.bukubesars
CREATE TABLE IF NOT EXISTS `bukubesars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_jurnal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaksi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` enum('Pendapatan','Beban','Aktiva','Modal','Kewajiban') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT '0.00',
  `kredit` decimal(15,2) DEFAULT '0.00',
  `saldo` decimal(15,2) DEFAULT '0.00',
  `aktivitas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `referensi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.bukubesars: ~73 rows (approximately)
INSERT INTO `bukubesars` (`id`, `kode_jurnal`, `transaksi`, `kategori`, `tanggal`, `debit`, `kredit`, `saldo`, `aktivitas`, `keterangan`, `referensi`, `created_at`, `updated_at`) VALUES
	(1, 'JRNL-001', 'Pemasukan Rental Harian', 'Pendapatan', '2026-05-10', 1500000.00, 0.00, 1500000.00, 'rental', 'Pembayaran rental harian dari customer', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'JRNL-002', 'Pemasukan Rental Mingguan', 'Pendapatan', '2026-06-19', 3500000.00, 0.00, 5000000.00, 'rental', 'Pembayaran rental mingguan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'JRNL-003', 'Penerimaan DP Rental', 'Pendapatan', '2026-05-06', 1000000.00, 0.00, 6000000.00, 'rental', 'DP rental kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'JRNL-004', 'Pelunasan Rental', 'Pendapatan', '2026-03-14', 2000000.00, 0.00, 8000000.00, 'rental', 'Pelunasan biaya rental', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'JRNL-005', 'Penerimaan Denda Keterlambatan', 'Pendapatan', '2026-05-13', 250000.00, 0.00, 8250000.00, 'denda', 'Denda pengembalian terlambat', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'JRNL-006', 'Penerimaan Deposit Customer', 'Pendapatan', '2026-07-07', 500000.00, 0.00, 8750000.00, 'deposit', 'Deposit jaminan kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'JRNL-007', 'Pendapatan Biaya Tambahan', 'Pendapatan', '2026-05-15', 200000.00, 0.00, 8950000.00, 'rental', 'Biaya supir tambahan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'JRNL-008', 'Penerimaan Sewa Jangka Panjang', 'Pendapatan', '2026-05-03', 15000000.00, 0.00, 23950000.00, 'rental', 'Kontrak sewa bulanan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'JRNL-009', 'Pendapatan Lain-lain', 'Pendapatan', '2026-01-18', 350000.00, 0.00, 24300000.00, 'lain', 'Pendapatan di luar operasional utama', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'JRNL-010', 'Penerimaan Invoice Kontrak', 'Pendapatan', '2026-01-15', 8000000.00, 0.00, 32300000.00, 'invoice', 'Pembayaran invoice kontrak korporat', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(11, 'JRNL-011', 'Biaya Servis Berkala', 'Beban', '2026-07-07', 0.00, 500000.00, 31800000.00, 'service', 'Servis rutin kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(12, 'JRNL-012', 'Biaya Ganti Oli', 'Beban', '2026-05-31', 0.00, 150000.00, 31650000.00, 'service', 'Penggantian oli mesin', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(13, 'JRNL-013', 'Pembayaran Pajak Kendaraan', 'Beban', '2026-03-09', 0.00, 3500000.00, 28150000.00, 'pajak', 'Pajak tahunan kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(14, 'JRNL-014', 'Premi Asuransi Kendaraan', 'Beban', '2026-03-10', 0.00, 5000000.00, 23150000.00, 'asuransi', 'Pembayaran premi asuransi', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(15, 'JRNL-015', 'Biaya Sewa GPS', 'Beban', '2026-01-26', 0.00, 300000.00, 22850000.00, 'gps', 'Biaya langganan GPS tracker', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(16, 'JRNL-016', 'Biaya Bahan Bakar', 'Beban', '2026-06-10', 0.00, 800000.00, 22050000.00, 'operasional', 'Pembelian bahan bakar kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(17, 'JRNL-017', 'Biaya KIR Kendaraan', 'Beban', '2026-05-30', 0.00, 200000.00, 21850000.00, 'kir', 'Biaya uji KIR kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(18, 'JRNL-018', 'Biaya Gaji Karyawan', 'Beban', '2026-01-31', 0.00, 5000000.00, 16850000.00, 'gaji', 'Gaji karyawan bulan ini', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(19, 'JRNL-019', 'Biaya Pembelian Spare Part', 'Beban', '2026-07-07', 0.00, 1200000.00, 15650000.00, 'service', 'Pembelian ban dan kampas rem', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(20, 'JRNL-020', 'Biaya Listrik dan Air', 'Beban', '2026-03-31', 0.00, 450000.00, 15200000.00, 'operasional', 'Tagihan utilitas kantor', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(21, 'JRNL-021', 'Pembelian Kendaraan Baru', 'Aktiva', '2026-04-18', 250000000.00, 0.00, 265200000.00, 'pembelian', 'Penambahan aset kendaraan baru', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(22, 'JRNL-022', 'Kas di Tangan', 'Aktiva', '2026-03-01', 10000000.00, 0.00, 275200000.00, 'kas', 'Saldo kas operasional', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(23, 'JRNL-023', 'Kas di Bank', 'Aktiva', '2026-04-15', 50000000.00, 0.00, 325200000.00, 'kas', 'Saldo rekening bank perusahaan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(24, 'JRNL-024', 'Piutang Rental', 'Aktiva', '2026-03-06', 7500000.00, 0.00, 332700000.00, 'rental', 'Tagihan belum dibayar customer', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(25, 'JRNL-025', 'Perlengkapan Kantor', 'Aktiva', '2026-05-09', 2500000.00, 0.00, 335200000.00, 'operasional', 'Inventaris perlengkapan kantor', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(26, 'JRNL-026', 'Peralatan Workshop', 'Aktiva', '2026-03-01', 15000000.00, 0.00, 350200000.00, 'service', 'Alat bengkel dan servis kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(27, 'JRNL-027', 'Deposit GPS Provider', 'Aktiva', '2026-06-06', 1000000.00, 0.00, 351200000.00, 'gps', 'Deposit ke penyedia GPS', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(28, 'JRNL-028', 'Persediaan Sparepart', 'Aktiva', '2026-03-14', 3000000.00, 0.00, 354200000.00, 'service', 'Stok sparepart di gudang', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(29, 'JRNL-029', 'Gedung Kantor', 'Aktiva', '2026-04-24', 500000000.00, 0.00, 854200000.00, 'aset', 'Nilai gedung kantor operasional', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(30, 'JRNL-030', 'Kendaraan Operasional', 'Aktiva', '2026-03-28', 180000000.00, 0.00, 1034200000.00, 'aset', 'Nilai armada kendaraan sewa', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(31, 'JRNL-031', 'Modal Awal Pemilik', 'Modal', '2026-04-12', 0.00, 500000000.00, 534200000.00, 'modal', 'Setoran modal awal perusahaan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(32, 'JRNL-032', 'Tambahan Modal Investasi', 'Modal', '2026-06-17', 0.00, 100000000.00, 434200000.00, 'modal', 'Investasi tambahan dari pemilik', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(33, 'JRNL-033', 'Laba Ditahan Tahun Lalu', 'Modal', '2026-01-24', 0.00, 75000000.00, 359200000.00, 'modal', 'Akumulasi laba yang tidak dibagikan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(34, 'JRNL-034', 'Dividen Dibayarkan', 'Modal', '2026-06-28', 25000000.00, 0.00, 384200000.00, 'modal', 'Pembagian dividen kepada pemilik', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(35, 'JRNL-035', 'Laba Bersih Periode Berjalan', 'Modal', '2026-03-03', 0.00, 45000000.00, 339200000.00, 'modal', 'Laba bersih periode ini', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(36, 'JRNL-036', 'Cadangan Umum', 'Modal', '2026-06-06', 0.00, 10000000.00, 329200000.00, 'modal', 'Cadangan dana untuk ekspansi', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(37, 'JRNL-037', 'Prive Pemilik', 'Modal', '2026-01-28', 5000000.00, 0.00, 334200000.00, 'modal', 'Pengambilan pribadi pemilik', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(38, 'JRNL-038', 'Revaluasi Aset Kendaraan', 'Modal', '2026-06-29', 0.00, 20000000.00, 314200000.00, 'aset', 'Kenaikan nilai aset kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(39, 'JRNL-039', 'Modal Kerja Tambahan', 'Modal', '2026-02-02', 0.00, 30000000.00, 284200000.00, 'modal', 'Penambahan modal kerja operasional', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(40, 'JRNL-040', 'Saldo Modal Berjalan', 'Modal', '2026-01-23', 0.00, 15000000.00, 269200000.00, 'modal', 'Saldo modal per periode ini', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(41, 'JRNL-041', 'Hutang Bank Jangka Panjang', 'Kewajiban', '2026-03-29', 0.00, 200000000.00, 69200000.00, 'hutang', 'Pinjaman bank untuk pembelian kendaraan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(42, 'JRNL-042', 'Hutang Leasing Kendaraan', 'Kewajiban', '2026-07-02', 0.00, 120000000.00, -50800000.00, 'hutang', 'Cicilan leasing kendaraan baru', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(43, 'JRNL-043', 'Hutang Vendor Sparepart', 'Kewajiban', '2026-03-06', 0.00, 8000000.00, -58800000.00, 'hutang', 'Tagihan belum dibayar ke vendor', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(44, 'JRNL-044', 'Hutang Pajak', 'Kewajiban', '2026-03-29', 0.00, 5000000.00, -63800000.00, 'pajak', 'Kewajiban pajak yang belum dibayar', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(45, 'JRNL-045', 'Hutang Gaji Karyawan', 'Kewajiban', '2026-03-10', 0.00, 15000000.00, -78800000.00, 'gaji', 'Gaji bulan lalu yang belum dibayar', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(46, 'JRNL-046', 'Hutang GPS Provider', 'Kewajiban', '2026-05-25', 0.00, 900000.00, -79700000.00, 'gps', 'Tagihan langganan GPS yang tertunda', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(47, 'JRNL-047', 'Hutang Asuransi', 'Kewajiban', '2026-02-14', 0.00, 3000000.00, -82700000.00, 'asuransi', 'Premi asuransi yang belum dibayar', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(48, 'JRNL-048', 'Deposit Customer Diterima', 'Kewajiban', '2026-02-13', 0.00, 4500000.00, -87200000.00, 'deposit', 'Deposit yang harus dikembalikan', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(49, 'JRNL-049', 'Hutang Listrik dan Utilitas', 'Kewajiban', '2026-06-05', 0.00, 750000.00, -87950000.00, 'operasional', 'Tagihan utilitas yang belum dibayar', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(50, 'JRNL-050', 'Hutang Jangka Pendek Lainnya', 'Kewajiban', '2026-04-08', 0.00, 2000000.00, -89950000.00, 'hutang', 'Kewajiban jangka pendek lain-lain', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(51, 'SRV-6', 'Beban Service - Daihatsu Gran Max AD 1528 WZ', 'Beban', '2026-07-12', 700999.00, 0.00, 700999.00, 'Operasi', 'Auto-posting: Service kendaraan AD 1528 WZ', NULL, '2026-07-11 22:17:45', '2026-07-11 22:17:45'),
	(52, 'SRV-7', 'Beban Service - Daihatsu Gran Max AD 1528 WZ', 'Beban', '2026-07-12', 701999.00, 0.00, 701999.00, 'Operasi', 'Auto-posting: Service kendaraan AD 1528 WZ', NULL, '2026-07-11 22:30:57', '2026-07-11 22:30:57'),
	(53, 'SRV-8', 'Beban Service - Daihatsu Gran Max AD 1198 SV', 'Beban', '2026-07-12', 90000.00, 0.00, 90000.00, 'Operasi', 'Auto-posting: Service kendaraan AD 1198 SV', NULL, '2026-07-12 00:22:33', '2026-07-12 00:22:33'),
	(54, 'SRV-9', 'Beban Service - Isuzu Elf AD 1308 CF', 'Beban', '2026-07-12', 2889999.00, 0.00, 2889999.00, 'Operasi', 'Auto-posting: Service kendaraan AD 1308 CF', NULL, '2026-07-13 08:13:03', '2026-07-13 08:13:03'),
	(55, 'SRV-10', 'Beban Service - Daihatsu Terios AA 1176 QT', 'Beban', '2026-07-23', 400000.00, 0.00, 400000.00, 'Operasi', 'Auto-posting: Service kendaraan AA 1176 QT', NULL, '2026-07-13 08:14:15', '2026-07-13 08:14:15'),
	(56, 'SRV-11', 'Beban Service - Daihatsu Gran Max AD 1528 WZ', 'Beban', '2026-07-13', 90000.00, 0.00, 90000.00, 'Operasi', 'Auto-posting: Service kendaraan AD 1528 WZ', NULL, '2026-07-13 09:06:24', '2026-07-13 09:06:24'),
	(57, 'PAJAK-7-1784016355', 'Beban Pajak - Pajak 5 Tahunan', 'Beban', '2026-07-14', 700000.00, 0.00, -610000.00, 'Operasi', 'Auto-posting: Pembayaran pajak kendaraan AB 1077 HK', NULL, '2026-07-14 01:05:55', '2026-07-14 01:05:55'),
	(58, 'PAJAK-45-1784016631', 'Beban Pajak - BBN-KB', 'Beban', '2026-07-14', 3300000.00, 0.00, -3910000.00, 'Operasi', 'Auto-posting: Pembayaran pajak kendaraan AG 1495 TW', NULL, '2026-07-14 01:10:31', '2026-07-14 01:10:31'),
	(59, 'PAJAK-45-1784016670', 'Beban Pajak - BBN-KB', 'Beban', '2026-07-14', 3300000.00, 0.00, -7210000.00, 'Operasi', 'Auto-posting: Pembayaran pajak kendaraan AG 1495 TW', NULL, '2026-07-14 01:11:10', '2026-07-14 01:11:10'),
	(60, 'Asuransi-1-1784016798', 'Beban Asuransi - All Risk', 'Beban', '2026-07-14', 8000000.00, 0.00, -15210000.00, 'Operasi', 'Auto-posting: Perpanjangan asuransi kendaraan AA 1011 BE', NULL, '2026-07-14 01:13:18', '2026-07-14 01:13:18'),
	(61, 'Asuransi-1-1784016825', 'Beban Asuransi - All Risk', 'Beban', '2026-07-14', 8000000.00, 0.00, -23210000.00, 'Operasi', 'Auto-posting: Perpanjangan asuransi kendaraan AA 1011 BE', NULL, '2026-07-14 01:13:45', '2026-07-14 01:13:45'),
	(62, 'GPS-1-1784017126', 'Beban GPS - OBD', 'Beban', '2026-07-14', 400000.00, 0.00, -23610000.00, 'Operasi', 'Auto-posting: Perpanjangan GPS kendaraan AA 1011 BE', NULL, '2026-07-14 01:18:46', '2026-07-14 01:18:46'),
	(63, 'GPS-1-1784017171', 'Beban GPS - OBD', 'Beban', '2026-07-14', 400000.00, 0.00, -24010000.00, 'Operasi', 'Auto-posting: Perpanjangan GPS kendaraan AA 1011 BE', NULL, '2026-07-14 08:19:31', '2026-07-14 08:19:31'),
	(64, 'KIR-1-1784017214', 'Beban KIR - AA 1011 BE', 'Beban', '2026-07-14', 500000.00, 0.00, -24510000.00, 'Operasi', 'Auto-posting: Pembayaran KIR kendaraan AA 1011 BE', NULL, '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(65, 'KIR-2-1784024188', 'Beban KIR - AB 1022 CF', 'Beban', '2026-07-14', 50000000.00, 0.00, -74510000.00, 'Operasi', 'Auto-posting: Pembayaran KIR kendaraan AB 1022 CF', NULL, '2026-07-14 10:16:28', '2026-07-14 10:16:28'),
	(66, 'PAJAK-7-1784025008', 'Beban Pajak - Pajak 5 Tahunan', 'Beban', '2026-07-14', 70000000.00, 0.00, -144510000.00, 'Operasi', 'Auto-posting: Pembayaran pajak kendaraan AB 1077 HK', NULL, '2026-07-14 10:30:08', '2026-07-14 10:30:08'),
	(67, 'PAJAK-47-1784025117', 'Beban Pajak - Pajak 5 Tahunan', 'Beban', '2026-07-14', 450000000.00, 0.00, -594510000.00, 'Operasi', 'Auto-posting: Pembayaran pajak kendaraan AB 1517 VY', NULL, '2026-07-14 10:31:57', '2026-07-14 10:31:57'),
	(68, 'Asuransi-1-1784025163', 'Beban Asuransi - All Risk', 'Beban', '2026-07-14', 800000000.00, 0.00, -1394510000.00, 'Operasi', 'Auto-posting: Perpanjangan asuransi kendaraan AA 1011 BE', NULL, '2026-07-14 10:32:43', '2026-07-14 10:32:43'),
	(69, 'Asuransi-1-1784025257', 'Beban Asuransi - All Risk', 'Beban', '2026-07-14', 80000000000.00, 0.00, -81394510000.00, 'Operasi', 'Auto-posting: Perpanjangan asuransi kendaraan AA 1011 BE', NULL, '2026-07-14 10:34:17', '2026-07-14 10:34:17'),
	(70, 'GPS-1-1784025604', 'Beban GPS - OBD', 'Beban', '2026-07-14', 400000.00, 0.00, -81394910000.00, 'Operasi', 'Auto-posting: Perpanjangan GPS kendaraan AA 1011 BE', NULL, '2026-07-14 10:40:04', '2026-07-14 10:40:04'),
	(71, 'KIR-1-1784025717', 'Beban KIR - AA 1011 BE', 'Beban', '2026-07-14', 50000000.00, 0.00, -81444910000.00, 'Operasi', 'Auto-posting: Pembayaran KIR kendaraan AA 1011 BE', NULL, '2026-07-14 10:41:57', '2026-07-14 10:41:57'),
	(72, 'Asuransi-1-1784026462', 'Beban Asuransi - All Risk', 'Beban', '2026-07-14', 8000000000000.00, 0.00, -8081444910000.00, 'Operasi', 'Auto-posting: Perpanjangan asuransi kendaraan AA 1011 BE', NULL, '2026-07-14 10:54:22', '2026-07-14 10:54:22'),
	(73, 'Asuransi-2-1784026552', 'Beban Asuransi - TLO', 'Beban', '2026-07-14', 1250000000.00, 0.00, -8082694910000.00, 'Operasi', 'Auto-posting: Perpanjangan asuransi kendaraan AB 1022 CF', NULL, '2026-07-14 10:55:52', '2026-07-14 10:55:52'),
	(74, 'Asuransi-3-1784028566', 'Beban Asuransi - Comprehensive', 'Beban', '2026-07-14', 5000000.00, 0.00, -8082699910000.00, 'Operasi', 'Auto-posting: Perpanjangan asuransi kendaraan AD 1033 DG', NULL, '2026-07-14 11:29:26', '2026-07-14 11:29:26');

-- Dumping structure for table apyrent.bupot
CREATE TABLE IF NOT EXISTS `bupot` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_bukti` date DEFAULT NULL,
  `tipe` enum('PPh21','PPh22','PPh23','PPh26') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp_pemotong` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pemotong` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp_dipotong` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_dipotong` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_bruto` decimal(20,2) DEFAULT NULL,
  `tarif_pajak` decimal(5,2) DEFAULT NULL,
  `jumlah_potong` decimal(20,2) DEFAULT NULL,
  `status` enum('Draft','Approve','Submit DJP') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `file_bupot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bupot_nomor_bukti_index` (`nomor_bukti`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.bupot: ~3 rows (approximately)
INSERT INTO `bupot` (`id`, `nomor_bukti`, `tanggal_bukti`, `tipe`, `npwp_pemotong`, `nama_pemotong`, `npwp_dipotong`, `nama_dipotong`, `jumlah_bruto`, `tarif_pajak`, `jumlah_potong`, `status`, `file_bupot`, `created_at`, `updated_at`) VALUES
	(1, 'BUPOT-001', '2026-07-02', 'PPh21', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '09.876.543.2-109.000', 'Budi Santoso', 5000000.00, 0.05, 250000.00, 'Approve', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 'BUPOT-002', '2026-07-04', 'PPh23', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '08.765.432.1-000.000', 'CV Sinar Abadi', 3000000.00, 0.02, 60000.00, 'Approve', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 'BUPOT-003', '2026-07-07', 'PPh26', '01.234.567.8-901.000', 'PT Rental Maju Jaya', '07.654.321.0-999.000', 'UD Jaya Motor', 10000000.00, 0.10, 1000000.00, 'Draft', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.cache: ~0 rows (approximately)

-- Dumping structure for table apyrent.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.cache_locks: ~0 rows (approximately)

-- Dumping structure for table apyrent.crm_prospeks
CREATE TABLE IF NOT EXISTS `crm_prospeks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_prospek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kontak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahapan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimasi_deal` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_prospeks_kode_prospek_unique` (`kode_prospek`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.crm_prospeks: ~10 rows (approximately)
INSERT INTO `crm_prospeks` (`id`, `kode_prospek`, `nama_kontak`, `perusahaan`, `email`, `telepon`, `tahapan`, `estimasi_deal`, `status`, `sales`, `tanggal_masuk`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'PRO-001', 'Budi Santoso', 'PT Maju Bersama', NULL, '0812-1111-1111', 'Prospek', NULL, 'Aktif', 'Andi', '2026-01-10', 'Butuh armada 5 unit', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'PRO-002', 'Siti Rahayu', 'CV Karya Indah', NULL, '0813-2222-2222', 'Negosiasi', NULL, 'Aktif', 'Budi', '2026-02-05', 'Diskusi harga sudah selesai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'PRO-003', 'Ahmad Fauzi', 'PT Sejahtera Abadi', NULL, '0814-3333-3333', 'Closing', NULL, 'Aktif', 'Cici', '2026-02-20', 'Kontrak siap ditandatangani', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'PRO-004', 'Dewi Lestari', 'PT Global Trans', NULL, '0815-4444-4444', 'Prospek', NULL, 'Aktif', 'Andi', '2026-03-01', 'Masih dalam penjajakan', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'PRO-005', 'Rudi Hartono', 'CV Jaya Mandiri', NULL, '0816-5555-5555', 'Negosiasi', NULL, 'Aktif', 'Dani', '2026-03-15', 'Negosiasi tenor kontrak', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'PRO-006', 'Lia Permata', 'PT Nusantara Raya', NULL, '0817-6666-6666', 'Closing', NULL, 'Aktif', 'Budi', '2026-04-02', 'Deal 3 unit minibus', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'PRO-007', 'Hendra Wijaya', 'PT Sinar Harapan', NULL, '0818-7777-7777', 'Prospek', NULL, 'Tidak Aktif', 'Cici', '2026-04-10', 'Tidak merespon lagi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'PRO-008', 'Maya Anggraini', 'CV Mitra Logistik', NULL, '0819-8888-8888', 'Negosiasi', NULL, 'Aktif', 'Andi', '2026-05-01', 'Menunggu approval direksi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'PRO-009', 'Fajar Nugroho', 'PT Berlian Trans', NULL, '0821-9999-9999', 'Closing', NULL, 'Aktif', 'Dani', '2026-05-20', 'Siap kontrak', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'PRO-010', 'Indah Kusuma', 'PT Prima Raya', NULL, '0822-1010-1010', 'Prospek', NULL, 'Aktif', 'Budi', '2026-06-05', 'Prospek baru dari referral', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.cuti_izins
CREATE TABLE IF NOT EXISTS `cuti_izins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_cuti_izin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `lama_hari` int NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.cuti_izins: ~40 rows (approximately)
INSERT INTO `cuti_izins` (`id`, `nama_pegawai`, `jenis_cuti_izin`, `tanggal_mulai`, `tanggal_selesai`, `lama_hari`, `alasan`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Rini Apriani', 'Cuti Tahunan', '2026-05-27', '2026-05-31', 5, 'Keperluan keluarga', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 'Eko Prasetyo', 'Cuti Sakit', '2026-04-30', '2026-05-04', 5, 'Pemulihan kesehatan', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 'Rizky Fadillah', 'Cuti Melahirkan', '2026-04-20', '2026-04-24', 5, 'Acara pernikahan', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 'Yusuf Hidayat', 'Izin Pribadi', '2026-03-14', '2026-03-24', 11, 'Mengurus administrasi', 'Pending', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 'Wahyu Nugroho', 'Cuti Bersama', '2026-03-12', '2026-03-21', 10, 'Liburan keluarga', 'Ditolak', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 'Fitri Handayani', 'Cuti Tahunan', '2026-03-22', '2026-04-03', 13, 'Cuti bersama hari raya', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 'Teguh Santosa', 'Cuti Sakit', '2026-03-21', '2026-03-28', 8, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 'Arif Budiman', 'Cuti Melahirkan', '2026-02-15', '2026-02-15', 1, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 'Dewi Kusuma', 'Izin Pribadi', '2026-03-08', '2026-03-20', 13, 'Keperluan keluarga', 'Pending', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 'Linda Permata', 'Cuti Bersama', '2026-03-23', '2026-03-28', 6, 'Pemulihan kesehatan', 'Ditolak', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(11, 'Hendra Gunawan', 'Cuti Tahunan', '2026-02-12', '2026-02-25', 14, 'Acara pernikahan', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(12, 'Dody Kurniawan', 'Cuti Sakit', '2026-04-25', '2026-05-05', 11, 'Mengurus administrasi', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(13, 'Rini Apriani', 'Cuti Melahirkan', '2026-07-06', '2026-07-13', 8, 'Liburan keluarga', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(14, 'Eko Prasetyo', 'Izin Pribadi', '2026-04-30', '2026-05-09', 10, 'Cuti bersama hari raya', 'Pending', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(15, 'Rizky Fadillah', 'Cuti Bersama', '2026-04-12', '2026-04-24', 13, 'Rawat inap di rumah sakit', 'Ditolak', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(16, 'Yusuf Hidayat', 'Cuti Tahunan', '2026-01-28', '2026-02-10', 14, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(17, 'Wahyu Nugroho', 'Cuti Sakit', '2026-06-01', '2026-06-12', 12, 'Keperluan keluarga', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(18, 'Fitri Handayani', 'Cuti Melahirkan', '2026-02-05', '2026-02-14', 10, 'Pemulihan kesehatan', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(19, 'Teguh Santosa', 'Izin Pribadi', '2026-04-30', '2026-05-09', 10, 'Acara pernikahan', 'Pending', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(20, 'Arif Budiman', 'Cuti Bersama', '2026-05-06', '2026-05-15', 10, 'Mengurus administrasi', 'Ditolak', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(21, 'Dewi Kusuma', 'Cuti Tahunan', '2026-07-10', '2026-07-15', 6, 'Liburan keluarga', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(22, 'Linda Permata', 'Cuti Sakit', '2026-03-25', '2026-03-29', 5, 'Cuti bersama hari raya', 'Disetujui', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(23, 'Hendra Gunawan', 'Cuti Melahirkan', '2026-04-30', '2026-05-13', 14, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(24, 'Dody Kurniawan', 'Izin Pribadi', '2026-06-29', '2026-07-01', 3, 'Keperluan mendesak pribadi', 'Pending', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(25, 'Rini Apriani', 'Cuti Bersama', '2026-05-11', '2026-05-12', 2, 'Keperluan keluarga', 'Ditolak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(26, 'Eko Prasetyo', 'Cuti Tahunan', '2026-07-03', '2026-07-09', 7, 'Pemulihan kesehatan', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(27, 'Rizky Fadillah', 'Cuti Sakit', '2026-05-13', '2026-05-21', 9, 'Acara pernikahan', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(28, 'Yusuf Hidayat', 'Cuti Melahirkan', '2026-02-23', '2026-02-24', 2, 'Mengurus administrasi', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(29, 'Wahyu Nugroho', 'Izin Pribadi', '2026-03-27', '2026-04-05', 10, 'Liburan keluarga', 'Pending', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(30, 'Fitri Handayani', 'Cuti Bersama', '2026-05-25', '2026-06-03', 10, 'Cuti bersama hari raya', 'Ditolak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(31, 'Teguh Santosa', 'Cuti Tahunan', '2026-05-31', '2026-06-05', 6, 'Rawat inap di rumah sakit', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(32, 'Arif Budiman', 'Cuti Sakit', '2026-04-24', '2026-04-29', 6, 'Keperluan mendesak pribadi', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(33, 'Dewi Kusuma', 'Cuti Melahirkan', '2026-04-30', '2026-05-12', 13, 'Keperluan keluarga', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(34, 'Linda Permata', 'Izin Pribadi', '2026-07-05', '2026-07-11', 7, 'Pemulihan kesehatan', 'Pending', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(35, 'Hendra Gunawan', 'Cuti Bersama', '2026-04-16', '2026-04-16', 1, 'Acara pernikahan', 'Ditolak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(36, 'Dody Kurniawan', 'Cuti Tahunan', '2026-04-28', '2026-05-06', 9, 'Mengurus administrasi', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(37, 'Rini Apriani', 'Cuti Sakit', '2026-03-15', '2026-03-15', 1, 'Liburan keluarga', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(38, 'Eko Prasetyo', 'Cuti Melahirkan', '2026-02-14', '2026-02-25', 12, 'Cuti bersama hari raya', 'Disetujui', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(39, 'Rizky Fadillah', 'Izin Pribadi', '2026-02-10', '2026-02-12', 3, 'Rawat inap di rumah sakit', 'Pending', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(40, 'Yusuf Hidayat', 'Cuti Bersama', '2026-01-23', '2026-02-05', 14, 'Keperluan mendesak pribadi', 'Ditolak', '2026-07-11 21:07:07', '2026-07-11 21:07:07');

-- Dumping structure for table apyrent.cybersecurities
CREATE TABLE IF NOT EXISTS `cybersecurities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal_audit` date NOT NULL,
  `area_diaudit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `temuan_risiko` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_risiko` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tindakan_perbaikan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.cybersecurities: ~5 rows (approximately)
INSERT INTO `cybersecurities` (`id`, `tanggal_audit`, `area_diaudit`, `temuan_risiko`, `level_risiko`, `tindakan_perbaikan`, `status`, `created_at`, `updated_at`) VALUES
	(1, '2025-01-05', 'Web Application', 'SQL Injection pada form login admin panel', 'Critical', 'Input sanitasi dan prepared statement diterapkan', 'Resolved', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, '2025-01-20', 'Jaringan Internal', 'Port 23 (Telnet) masih terbuka di beberapa switch', 'High', 'Disable Telnet dan aktifkan SSH pada semua perangkat jaringan', 'In Progress', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, '2025-02-10', 'Email Server', 'Tidak ada SPF dan DMARC record, rentan email spoofing', 'Medium', 'Konfigurasi SPF, DKIM, dan DMARC pada DNS', 'Open', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, '2025-02-25', 'Endpoint Security', '12 komputer belum update antivirus selama 3 bulan', 'Low', 'Update antivirus terpusat via console management', 'Resolved', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, '2025-03-01', 'Database Server', 'Akun root database dapat diakses dari remote tanpa restriksi IP', 'Critical', 'Batasi akses root hanya dari localhost, buat user terbatas', 'Open', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.daftar_notaris
CREATE TABLE IF NOT EXISTS `daftar_notaris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kantor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layanan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terakhir_dipakai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.daftar_notaris: ~0 rows (approximately)

-- Dumping structure for table apyrent.denda_rentals
CREATE TABLE IF NOT EXISTS `denda_rentals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_id` bigint unsigned NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `tanggal_denda` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `denda_rentals_rental_id_foreign` (`rental_id`),
  CONSTRAINT `denda_rentals_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.denda_rentals: ~0 rows (approximately)

-- Dumping structure for table apyrent.departemens
CREATE TABLE IF NOT EXISTS `departemens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_departemen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kepala_departemen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_dibentuk` date NOT NULL,
  `jumlah_posisi` int NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status_aktif` enum('Aktif','Non-Aktif') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.departemens: ~12 rows (approximately)
INSERT INTO `departemens` (`id`, `nama_departemen`, `kepala_departemen`, `tanggal_dibentuk`, `jumlah_posisi`, `keterangan`, `status_aktif`, `created_at`, `updated_at`) VALUES
	(1, 'Direksi', 'Budi Santoso', '2018-01-02', 3, 'Pimpinan tertinggi perusahaan', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 'HRD', 'Dewi Kusuma', '2018-06-01', 8, 'Mengelola sumber daya manusia', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 'IT', 'Hendra Gunawan', '2019-01-15', 6, 'Pengembangan dan pemeliharaan sistem teknologi', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 'Finance', 'Linda Permata', '2018-06-01', 10, 'Pengelolaan keuangan dan akuntansi', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 'Operasional', 'Dody Kurniawan', '2019-03-01', 15, 'Pengelolaan operasional lapangan', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 'Marketing', 'Sari Dewanti', '2020-02-01', 7, 'Pemasaran dan promosi produk', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 'Sales', 'Benny Kusuma', '2020-04-01', 12, 'Penjualan dan hubungan pelanggan', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 'Legal', 'Putri Wulandari', '2021-01-01', 4, 'Urusan hukum dan kontrak perusahaan', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 'Procurement', 'Bambang Irawan', '2021-06-01', 5, 'Pengadaan barang dan jasa', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 'Maintenance', 'Suryono Hadi', '2019-07-01', 8, 'Pemeliharaan aset dan kendaraan', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(11, 'R&D', 'Indra Lesmana', '2022-01-01', 4, 'Riset dan pengembangan produk', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(12, 'Customer Service', 'Maya Anggraini', '2020-09-01', 6, 'Layanan pelanggan', 'Aktif', '2026-07-11 21:07:06', '2026-07-11 21:07:06');

-- Dumping structure for table apyrent.deposit_customers
CREATE TABLE IF NOT EXISTS `deposit_customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_id` bigint unsigned NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `potongan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('ditahan','dikembalikan','dipotong') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ditahan',
  `tanggal_deposit` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deposit_customers_rental_id_foreign` (`rental_id`),
  CONSTRAINT `deposit_customers_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.deposit_customers: ~0 rows (approximately)

-- Dumping structure for table apyrent.devops
CREATE TABLE IF NOT EXISTS `devops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `aplikasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tools` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deployment_otomatis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jadwal_build` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.devops: ~5 rows (approximately)
INSERT INTO `devops` (`id`, `aplikasi`, `tools`, `deployment_otomatis`, `jadwal_build`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'API Backend ERP', 'GitHub Actions', 'Ya', 'Setiap push ke branch main', 'Aktif', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'Frontend Dashboard', 'GitLab CI', 'Ya', 'Setiap merge request approved', 'Aktif', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'Mobile App Driver', 'Bitrise', 'Tidak', 'Manual oleh tim mobile', 'Aktif', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'Laporan Keuangan', 'Jenkins', 'Ya', 'Setiap hari pukul 02.00 WIB', 'Nonaktif', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'Website Company Profile', 'GitHub Actions', 'Ya', 'Setiap push ke branch production', 'Aktif', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.dokumentasi_assets
CREATE TABLE IF NOT EXISTS `dokumentasi_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_tersimpan` tinyint(1) NOT NULL DEFAULT '0',
  `lokasi_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.dokumentasi_assets: ~0 rows (approximately)

-- Dumping structure for table apyrent.dokumen_proyeks
CREATE TABLE IF NOT EXISTS `dokumen_proyeks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_upload` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.dokumen_proyeks: ~8 rows (approximately)
INSERT INTO `dokumen_proyeks` (`id`, `proyek`, `nama_dokumen`, `tipe`, `file`, `status`, `tanggal_upload`, `created_at`, `updated_at`) VALUES
	(1, 'PRJ001', 'RAB Renovasi Pool', 'XLSX', '-', 'Valid', '2025-12-28', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'PRJ001', 'Gambar Desain Konstruksi', 'PDF', '-', 'Valid', '2025-12-30', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(3, 'PRJ001', 'Kontrak Kontraktor', 'PDF', '-', 'Valid', '2026-01-02', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(4, 'PRJ002', 'Spesifikasi Teknis Bus', 'PDF', '-', 'Valid', '2026-01-20', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(5, 'PRJ002', 'Purchase Order Bus', 'PDF', '-', 'Draft', '2026-02-05', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(6, 'PRJ003', 'Proposal GPS Monitoring', 'PDF', '-', 'Valid', '2026-01-10', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(7, 'PRJ003', 'Kontrak Vendor GPS', 'PDF', '-', 'Valid', '2026-01-14', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(8, 'PRJ005', 'PKS Layanan Antar Jemput', 'PDF', '-', 'Valid', '2026-02-12', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.dropshippings
CREATE TABLE IF NOT EXISTS `dropshippings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_akhir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.dropshippings: ~25 rows (approximately)
INSERT INTO `dropshippings` (`id`, `kode_transaksi`, `tipe`, `vendor`, `barang`, `jumlah`, `satuan`, `customer_akhir`, `tanggal_kirim`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'DS-001', 'Regular', 'PT Maju Jaya', 'Spare Part Mesin', 37, 'pcs', 'PT Angin Ribut', '2026-05-12', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'DS-002', 'Express', 'CV Berkah Abadi', 'Oli Mesin', 33, 'liter', 'CV Cahaya Terang', '2026-05-09', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'DS-003', 'Same Day', 'PT Sumber Makmur', 'Ban Kendaraan', 24, 'unit', 'Toko Maju', '2026-05-09', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'DS-004', 'Ekonomi', 'UD Sejahtera', 'Filter Udara', 57, 'set', 'UD Bahagia', '2026-03-15', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'DS-005', 'Regular', 'PT Indo Supplier', 'Aki Kendaraan', 66, 'buah', 'PT Kilat Jaya', '2026-03-28', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(6, 'DS-006', 'Express', 'PT Maju Jaya', 'Kampas Rem', 64, 'pcs', 'CV Sentosa', '2026-06-06', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 'DS-007', 'Same Day', 'CV Berkah Abadi', 'Spare Part Mesin', 30, 'liter', 'PT Angin Ribut', '2026-05-17', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(8, 'DS-008', 'Ekonomi', 'PT Sumber Makmur', 'Oli Mesin', 60, 'unit', 'CV Cahaya Terang', '2026-06-13', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(9, 'DS-009', 'Regular', 'UD Sejahtera', 'Ban Kendaraan', 34, 'set', 'Toko Maju', '2026-05-22', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(10, 'DS-010', 'Express', 'PT Indo Supplier', 'Filter Udara', 66, 'buah', 'UD Bahagia', '2026-05-31', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(11, 'DS-011', 'Same Day', 'PT Maju Jaya', 'Aki Kendaraan', 14, 'pcs', 'PT Kilat Jaya', '2026-07-01', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(12, 'DS-012', 'Ekonomi', 'CV Berkah Abadi', 'Kampas Rem', 82, 'liter', 'CV Sentosa', '2026-06-08', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(13, 'DS-013', 'Regular', 'PT Sumber Makmur', 'Spare Part Mesin', 100, 'unit', 'PT Angin Ribut', '2026-05-08', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(14, 'DS-014', 'Express', 'UD Sejahtera', 'Oli Mesin', 53, 'set', 'CV Cahaya Terang', '2026-04-16', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(15, 'DS-015', 'Same Day', 'PT Indo Supplier', 'Ban Kendaraan', 38, 'buah', 'Toko Maju', '2026-04-12', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(16, 'DS-016', 'Ekonomi', 'PT Maju Jaya', 'Filter Udara', 93, 'pcs', 'UD Bahagia', '2026-03-25', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(17, 'DS-017', 'Regular', 'CV Berkah Abadi', 'Aki Kendaraan', 14, 'liter', 'PT Kilat Jaya', '2026-06-20', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(18, 'DS-018', 'Express', 'PT Sumber Makmur', 'Kampas Rem', 34, 'unit', 'CV Sentosa', '2026-07-10', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(19, 'DS-019', 'Same Day', 'UD Sejahtera', 'Spare Part Mesin', 59, 'set', 'PT Angin Ribut', '2026-05-22', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(20, 'DS-020', 'Ekonomi', 'PT Indo Supplier', 'Oli Mesin', 37, 'buah', 'CV Cahaya Terang', '2026-07-04', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(21, 'DS-021', 'Regular', 'PT Maju Jaya', 'Ban Kendaraan', 81, 'pcs', 'Toko Maju', '2026-07-01', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(22, 'DS-022', 'Express', 'CV Berkah Abadi', 'Filter Udara', 50, 'liter', 'UD Bahagia', '2026-04-03', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(23, 'DS-023', 'Same Day', 'PT Sumber Makmur', 'Aki Kendaraan', 28, 'unit', 'PT Kilat Jaya', '2026-07-09', 'Dikirim', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(24, 'DS-024', 'Ekonomi', 'UD Sejahtera', 'Kampas Rem', 7, 'set', 'CV Sentosa', '2026-05-15', 'Selesai', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(25, 'DS-025', 'Regular', 'PT Indo Supplier', 'Spare Part Mesin', 86, 'buah', 'PT Angin Ribut', '2026-05-08', 'Proses', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.efakturs
CREATE TABLE IF NOT EXISTS `efakturs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_faktur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_faktur` date DEFAULT NULL,
  `tipe` enum('Keluaran','Masukan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp_lawan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_lawan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dpp` decimal(20,2) DEFAULT NULL,
  `ppn` decimal(20,2) DEFAULT NULL,
  `ppnbm` decimal(20,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Draft',
  `file_faktur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `efakturs_nomor_faktur_index` (`nomor_faktur`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.efakturs: ~3 rows (approximately)
INSERT INTO `efakturs` (`id`, `nomor_faktur`, `tanggal_faktur`, `tipe`, `npwp_lawan`, `nama_lawan`, `dpp`, `ppn`, `ppnbm`, `status`, `file_faktur`, `created_at`, `updated_at`) VALUES
	(1, '010.000-26.000001', '2026-07-02', 'Keluaran', '01.234.567.8-901.000', 'PT Rental Maju Jaya', 5000000.00, 550000.00, 0.00, 'terbit', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, '010.000-26.000002', '2026-07-07', 'Masukan', '09.876.543.2-109.000', 'PT Supplier Sparepart', 3000000.00, 330000.00, 0.00, 'draft', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, '010.000-26.000003', '2026-07-10', 'Keluaran', '07.111.222.3-444.000', 'CV Transport Jaya', 7500000.00, 825000.00, 0.00, 'terbit', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.email_domains
CREATE TABLE IF NOT EXISTS `email_domains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_date` date DEFAULT NULL,
  `email_aktif` int NOT NULL DEFAULT '0',
  `dns_terkelola` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.email_domains: ~4 rows (approximately)
INSERT INTO `email_domains` (`id`, `nama_domain`, `provider`, `status`, `expired_date`, `email_aktif`, `dns_terkelola`, `created_at`, `updated_at`) VALUES
	(1, 'perusahaan.com', 'GoDaddy', 'aktif', '2026-08-15', 120, 1, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'perusahaan.co.id', 'Rumahweb', 'aktif', '2025-11-30', 45, 1, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'old-brand.com', 'Namecheap', 'nonaktif', '2024-03-10', 0, 0, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'app.perusahaan.com', 'Cloudflare', 'aktif', '2027-01-01', 0, 1, '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table apyrent.gps
CREATE TABLE IF NOT EXISTS `gps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nama_gps` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `nama_marketing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_marketing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bengkel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_bengkel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gps_user_id_foreign` (`user_id`),
  CONSTRAINT `gps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.gps: ~10 rows (approximately)
INSERT INTO `gps` (`id`, `user_id`, `nama_gps`, `alamat`, `nama_marketing`, `kontak_marketing`, `nama_bengkel`, `kontak_bengkel`, `created_at`, `updated_at`) VALUES
	(1, 1, 'GPS Tracker Pro', 'Jl. Teknologi No. 1, Jakarta', 'Marketing 1', '08413681759', 'Bengkel GPS 1', '08244642910', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 1, 'Teltonika', 'Jl. Teknologi No. 2, Jakarta', 'Marketing 2', '08829752821', 'Bengkel GPS 2', '08285312273', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 1, 'Queclink', 'Jl. Teknologi No. 3, Jakarta', 'Marketing 3', '08480104322', 'Bengkel GPS 3', '08373950546', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 1, 'Concox', 'Jl. Teknologi No. 4, Jakarta', 'Marketing 4', '08585760293', 'Bengkel GPS 4', '08819517030', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 1, 'Ruptela', 'Jl. Teknologi No. 5, Jakarta', 'Marketing 5', '08674633833', 'Bengkel GPS 5', '08489689139', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(6, 1, 'Coban', 'Jl. Teknologi No. 6, Jakarta', 'Marketing 6', '08289741345', 'Bengkel GPS 6', '08214668932', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(7, 1, 'Gosafe', 'Jl. Teknologi No. 7, Jakarta', 'Marketing 7', '08272519391', 'Bengkel GPS 7', '08773854494', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(8, 1, 'Jointech', 'Jl. Teknologi No. 8, Jakarta', 'Marketing 8', '08159672027', 'Bengkel GPS 8', '08805809283', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, 1, 'Meitrack', 'Jl. Teknologi No. 9, Jakarta', 'Marketing 9', '08209237498', 'Bengkel GPS 9', '08968348078', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, 1, 'Sinotrack', 'Jl. Teknologi No. 10, Jakarta', 'Marketing 10', '08927861418', 'Bengkel GPS 10', '08501389086', '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.gps_kendaraan
CREATE TABLE IF NOT EXISTS `gps_kendaraan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `gps_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_gps` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `tanggal_pasang` date NOT NULL,
  `tanggal_habis` date NOT NULL,
  `biaya_sewa` bigint NOT NULL DEFAULT '0',
  `durasi_bulan` int NOT NULL DEFAULT '0',
  `status_sewa` enum('aktif','habis') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gps_kendaraan_kendaraan_id_foreign` (`kendaraan_id`),
  KEY `gps_kendaraan_gps_id_foreign` (`gps_id`),
  CONSTRAINT `gps_kendaraan_gps_id_foreign` FOREIGN KEY (`gps_id`) REFERENCES `gps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gps_kendaraan_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.gps_kendaraan: ~50 rows (approximately)
INSERT INTO `gps_kendaraan` (`id`, `kendaraan_id`, `gps_id`, `type`, `status_gps`, `tanggal_pasang`, `tanggal_habis`, `biaya_sewa`, `durasi_bulan`, `status_sewa`, `bukti_bayar`, `tanggal_bayar`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'OBD', 'aktif', '2026-12-14', '2030-07-12', 400000, 12, 'aktif', 'gps/bukti_bayar/1784025604_6a5612041ac1d.pdf', '2026-12-14', '2026-07-11 21:07:02', '2026-07-14 10:40:04'),
	(2, 2, 2, 'Hardwire', 'aktif', '2025-07-12', '2027-04-12', 400000, 21, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 3, 3, 'Magnetic', 'aktif', '2025-05-12', '2026-10-12', 100000, 17, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 4, 4, '4G LTE', 'aktif', '2026-02-12', '2027-10-12', 500000, 20, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 5, 5, 'Solar', 'aktif', '2025-06-12', '2027-02-12', 200000, 20, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(6, 6, 6, 'OBD', 'nonaktif', '2025-06-12', '2026-07-12', 100000, 13, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(7, 7, 7, 'Hardwire', 'aktif', '2025-08-12', '2027-08-12', 200000, 24, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(8, 8, 8, 'Magnetic', 'nonaktif', '2025-01-12', '2026-06-12', 100000, 17, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, 9, 9, '4G LTE', 'nonaktif', '2025-01-12', '2025-09-12', 100000, 8, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, 10, 10, 'Solar', 'aktif', '2026-02-12', '2027-03-12', 300000, 13, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(11, 11, 1, 'OBD', 'aktif', '2025-10-12', '2027-05-12', 100000, 19, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(12, 12, 2, 'Hardwire', 'aktif', '2026-05-12', '2027-08-12', 200000, 15, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(13, 13, 3, 'Magnetic', 'aktif', '2025-11-12', '2027-07-12', 500000, 20, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(14, 14, 4, '4G LTE', 'nonaktif', '2025-03-12', '2025-12-12', 200000, 9, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(15, 15, 5, 'Solar', 'aktif', '2025-09-12', '2026-11-12', 100000, 14, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(16, 16, 6, 'OBD', 'nonaktif', '2025-05-12', '2025-12-12', 400000, 7, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(17, 17, 7, 'Hardwire', 'nonaktif', '2025-04-12', '2025-12-12', 200000, 8, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(18, 18, 8, 'Magnetic', 'aktif', '2025-12-12', '2026-10-12', 200000, 10, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(19, 19, 9, '4G LTE', 'aktif', '2026-01-12', '2027-01-12', 200000, 12, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(20, 20, 10, 'Solar', 'nonaktif', '2025-01-12', '2026-03-12', 500000, 14, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(21, 21, 1, 'OBD', 'aktif', '2025-06-12', '2026-12-12', 500000, 18, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(22, 22, 2, 'Hardwire', 'aktif', '2025-10-12', '2026-08-12', 500000, 10, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(23, 23, 3, 'Magnetic', 'nonaktif', '2025-04-12', '2025-11-12', 100000, 7, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(24, 24, 4, '4G LTE', 'aktif', '2026-02-12', '2026-12-12', 400000, 10, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(25, 25, 5, 'Solar', 'aktif', '2025-12-12', '2027-04-12', 500000, 16, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(26, 26, 6, 'OBD', 'aktif', '2026-06-12', '2027-09-12', 200000, 15, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(27, 27, 7, 'Hardwire', 'nonaktif', '2025-01-12', '2026-06-12', 500000, 17, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(28, 28, 8, 'Magnetic', 'nonaktif', '2025-03-12', '2026-04-12', 200000, 13, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(29, 29, 9, '4G LTE', 'aktif', '2026-03-12', '2027-08-12', 500000, 17, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(30, 30, 10, 'Solar', 'aktif', '2026-05-12', '2028-05-12', 100000, 24, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(31, 31, 1, 'OBD', 'nonaktif', '2025-03-12', '2026-03-12', 400000, 12, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(32, 32, 2, 'Hardwire', 'aktif', '2025-05-12', '2026-11-12', 500000, 18, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(33, 33, 3, 'Magnetic', 'nonaktif', '2025-07-12', '2026-07-12', 100000, 12, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(34, 34, 4, '4G LTE', 'nonaktif', '2025-04-12', '2026-06-12', 200000, 14, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(35, 35, 5, 'Solar', 'nonaktif', '2025-01-12', '2025-10-12', 500000, 9, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(36, 36, 6, 'OBD', 'nonaktif', '2025-07-12', '2026-01-12', 200000, 6, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(37, 37, 7, 'Hardwire', 'aktif', '2026-05-12', '2027-02-12', 200000, 9, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(38, 38, 8, 'Magnetic', 'aktif', '2025-12-12', '2027-01-12', 200000, 13, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(39, 39, 9, '4G LTE', 'aktif', '2026-04-12', '2028-03-12', 300000, 23, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(40, 40, 10, 'Solar', 'aktif', '2025-10-12', '2026-10-12', 200000, 12, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(41, 41, 1, 'OBD', 'nonaktif', '2025-07-12', '2026-04-12', 100000, 9, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(42, 42, 2, 'Hardwire', 'aktif', '2025-12-12', '2026-09-12', 100000, 9, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(43, 43, 3, 'Magnetic', 'aktif', '2025-07-12', '2026-08-12', 100000, 13, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(44, 44, 4, '4G LTE', 'aktif', '2025-07-12', '2027-03-12', 200000, 20, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(45, 45, 5, 'Solar', 'aktif', '2026-06-12', '2027-06-12', 300000, 12, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(46, 46, 6, 'OBD', 'aktif', '2025-08-12', '2026-12-12', 500000, 16, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(47, 47, 7, 'Hardwire', 'aktif', '2025-12-12', '2026-10-12', 200000, 10, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(48, 48, 8, 'Magnetic', 'aktif', '2025-09-12', '2027-04-12', 200000, 19, 'aktif', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(49, 49, 9, '4G LTE', 'nonaktif', '2025-01-12', '2026-01-12', 400000, 12, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(50, 50, 10, 'Solar', 'nonaktif', '2025-05-12', '2026-07-12', 100000, 14, 'habis', NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.gps_kendaraan_histories
CREATE TABLE IF NOT EXISTS `gps_kendaraan_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gps_kendaraan_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `gps_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_gps` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pasang` date NOT NULL,
  `tanggal_habis` date NOT NULL,
  `biaya_sewa` int NOT NULL,
  `durasi_bulan` int NOT NULL,
  `status_sewa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gps_kendaraan_histories_gps_kendaraan_id_foreign` (`gps_kendaraan_id`),
  KEY `gps_kendaraan_histories_kendaraan_id_foreign` (`kendaraan_id`),
  KEY `gps_kendaraan_histories_gps_id_foreign` (`gps_id`),
  CONSTRAINT `gps_kendaraan_histories_gps_id_foreign` FOREIGN KEY (`gps_id`) REFERENCES `gps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gps_kendaraan_histories_gps_kendaraan_id_foreign` FOREIGN KEY (`gps_kendaraan_id`) REFERENCES `gps_kendaraan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gps_kendaraan_histories_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.gps_kendaraan_histories: ~2 rows (approximately)
INSERT INTO `gps_kendaraan_histories` (`id`, `gps_kendaraan_id`, `kendaraan_id`, `gps_id`, `type`, `status_gps`, `tanggal_pasang`, `tanggal_habis`, `biaya_sewa`, `durasi_bulan`, `status_sewa`, `bukti_bayar`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 'OBD', 'aktif', '2026-07-14', '2028-07-12', 400000, 12, 'aktif', 'gps/bukti_bayar/1784017126_6a55f0e668a89.jpeg', '2026-07-14 01:18:46', '2026-07-14 01:18:46', '2026-07-14 01:18:46'),
	(2, 1, 1, 1, 'OBD', 'aktif', '2026-07-14', '2029-07-12', 400000, 12, 'aktif', 'gps/bukti_bayar/1784017171_6a55f1138eb35.pdf', '2026-07-14 08:19:31', '2026-07-14 08:19:31', '2026-07-14 08:19:31'),
	(3, 1, 1, 1, 'OBD', 'aktif', '2026-12-14', '2030-07-12', 400000, 12, 'aktif', 'gps/bukti_bayar/1784025604_6a5612041ac1d.pdf', '2026-07-14 10:40:04', '2026-07-14 10:40:04', '2026-07-14 10:40:04');

-- Dumping structure for table apyrent.hak_hukums
CREATE TABLE IF NOT EXISTS `hak_hukums` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_akses` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penerima_akses` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_hak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_akses` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.hak_hukums: ~0 rows (approximately)

-- Dumping structure for table apyrent.helpdesk_supports
CREATE TABLE IF NOT EXISTS `helpdesk_supports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_tiket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `masalah` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `prioritas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teknisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_respon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.helpdesk_supports: ~5 rows (approximately)
INSERT INTO `helpdesk_supports` (`id`, `no_tiket`, `tanggal`, `departemen`, `masalah`, `prioritas`, `status`, `teknisi`, `waktu_respon`, `created_at`, `updated_at`) VALUES
	(1, 'TKT-001', '2025-01-10', 'Finance', 'Laptop tidak bisa menyala setelah update Windows', 'High', 'Resolved', 'Doni Prasetyo', '2 jam', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'TKT-002', '2025-01-15', 'HR', 'Email tidak bisa terkirim ke luar domain', 'Medium', 'Open', 'Siti Rahayu', '4 jam', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'TKT-003', '2025-01-20', 'Sales', 'Koneksi VPN terputus saat WFH', 'Critical', 'In Progress', 'Doni Prasetyo', '30 menit', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'TKT-004', '2025-02-01', 'IT', 'Printer di lantai 3 tidak terdeteksi oleh komputer', 'Low', 'Closed', 'Siti Rahayu', '1 hari', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'TKT-005', '2025-02-10', 'Operasional', 'Sistem ERP lambat saat jam kerja puncak', 'High', 'In Progress', 'Doni Prasetyo', '1 jam', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.hrd_files
CREATE TABLE IF NOT EXISTS `hrd_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.hrd_files: ~76 rows (approximately)
INSERT INTO `hrd_files` (`id`, `nama_pegawai`, `nama_file`, `jenis_dokumen`, `file_path`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'Budi Santoso', 'NPWP - Budi Santoso', 'NPWP', 'hrd_files/budi_santoso/npwp_budi_santoso.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(2, 'Budi Santoso', 'Ijazah - Budi Santoso', 'Ijazah', 'hrd_files/budi_santoso/ijazah_budi_santoso.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(3, 'Budi Santoso', 'SK Pengangkatan - Budi Santoso', 'SK Pengangkatan', 'hrd_files/budi_santoso/sk_budi_santoso.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(4, 'Budi Santoso', 'Kontrak Kerja - Budi Santoso', 'Kontrak Kerja', 'hrd_files/budi_santoso/kontrak_budi_santoso.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(5, 'Budi Santoso', 'BPJS Kesehatan - Budi Santoso', 'BPJS Kesehatan', 'hrd_files/budi_santoso/bpjs_kes_budi_santoso.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(6, 'Siti Rahayu', 'NPWP - Siti Rahayu', 'NPWP', 'hrd_files/siti_rahayu/npwp_siti_rahayu.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(7, 'Siti Rahayu', 'Ijazah - Siti Rahayu', 'Ijazah', 'hrd_files/siti_rahayu/ijazah_siti_rahayu.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(8, 'Siti Rahayu', 'SK Pengangkatan - Siti Rahayu', 'SK Pengangkatan', 'hrd_files/siti_rahayu/sk_siti_rahayu.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(9, 'Siti Rahayu', 'BPJS Kesehatan - Siti Rahayu', 'BPJS Kesehatan', 'hrd_files/siti_rahayu/bpjs_kes_siti_rahayu.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(10, 'Siti Rahayu', 'BPJS TK - Siti Rahayu', 'BPJS TK', 'hrd_files/siti_rahayu/bpjs_tk_siti_rahayu.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(11, 'Agus Wibowo', 'NPWP - Agus Wibowo', 'NPWP', 'hrd_files/agus_wibowo/npwp_agus_wibowo.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(12, 'Agus Wibowo', 'Ijazah - Agus Wibowo', 'Ijazah', 'hrd_files/agus_wibowo/ijazah_agus_wibowo.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(13, 'Agus Wibowo', 'Kontrak Kerja - Agus Wibowo', 'Kontrak Kerja', 'hrd_files/agus_wibowo/kontrak_agus_wibowo.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(14, 'Dewi Kusuma', 'NPWP - Dewi Kusuma', 'NPWP', 'hrd_files/dewi_kusuma/npwp_dewi_kusuma.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(15, 'Dewi Kusuma', 'Ijazah - Dewi Kusuma', 'Ijazah', 'hrd_files/dewi_kusuma/ijazah_dewi_kusuma.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(16, 'Dewi Kusuma', 'SK Pengangkatan - Dewi Kusuma', 'SK Pengangkatan', 'hrd_files/dewi_kusuma/sk_dewi_kusuma.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(17, 'Dewi Kusuma', 'Kontrak Kerja - Dewi Kusuma', 'Kontrak Kerja', 'hrd_files/dewi_kusuma/kontrak_dewi_kusuma.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(18, 'Dewi Kusuma', 'BPJS Kesehatan - Dewi Kusuma', 'BPJS Kesehatan', 'hrd_files/dewi_kusuma/bpjs_kes_dewi_kusuma.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(19, 'Dewi Kusuma', 'BPJS TK - Dewi Kusuma', 'BPJS TK', 'hrd_files/dewi_kusuma/bpjs_tk_dewi_kusuma.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(20, 'Rini Apriani', 'NPWP - Rini Apriani', 'NPWP', 'hrd_files/rini_apriani/npwp_rini_apriani.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(21, 'Rini Apriani', 'SK Pengangkatan - Rini Apriani', 'SK Pengangkatan', 'hrd_files/rini_apriani/sk_rini_apriani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(22, 'Rini Apriani', 'Kontrak Kerja - Rini Apriani', 'Kontrak Kerja', 'hrd_files/rini_apriani/kontrak_rini_apriani.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(23, 'Rini Apriani', 'BPJS Kesehatan - Rini Apriani', 'BPJS Kesehatan', 'hrd_files/rini_apriani/bpjs_kes_rini_apriani.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(24, 'Rini Apriani', 'BPJS TK - Rini Apriani', 'BPJS TK', 'hrd_files/rini_apriani/bpjs_tk_rini_apriani.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(25, 'Eko Prasetyo', 'KTP - Eko Prasetyo', 'KTP', 'hrd_files/eko_prasetyo/ktp_eko_prasetyo.pdf', 'Kartu Tanda Penduduk', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(26, 'Eko Prasetyo', 'NPWP - Eko Prasetyo', 'NPWP', 'hrd_files/eko_prasetyo/npwp_eko_prasetyo.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(27, 'Eko Prasetyo', 'Ijazah - Eko Prasetyo', 'Ijazah', 'hrd_files/eko_prasetyo/ijazah_eko_prasetyo.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(28, 'Eko Prasetyo', 'SK Pengangkatan - Eko Prasetyo', 'SK Pengangkatan', 'hrd_files/eko_prasetyo/sk_eko_prasetyo.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(29, 'Eko Prasetyo', 'Kontrak Kerja - Eko Prasetyo', 'Kontrak Kerja', 'hrd_files/eko_prasetyo/kontrak_eko_prasetyo.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(30, 'Eko Prasetyo', 'BPJS Kesehatan - Eko Prasetyo', 'BPJS Kesehatan', 'hrd_files/eko_prasetyo/bpjs_kes_eko_prasetyo.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(31, 'Eko Prasetyo', 'BPJS TK - Eko Prasetyo', 'BPJS TK', 'hrd_files/eko_prasetyo/bpjs_tk_eko_prasetyo.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(32, 'Hendra Gunawan', 'SK Pengangkatan - Hendra Gunawan', 'SK Pengangkatan', 'hrd_files/hendra_gunawan/sk_hendra_gunawan.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(33, 'Hendra Gunawan', 'Kontrak Kerja - Hendra Gunawan', 'Kontrak Kerja', 'hrd_files/hendra_gunawan/kontrak_hendra_gunawan.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(34, 'Hendra Gunawan', 'BPJS Kesehatan - Hendra Gunawan', 'BPJS Kesehatan', 'hrd_files/hendra_gunawan/bpjs_kes_hendra_gunawan.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(35, 'Rizky Fadillah', 'KTP - Rizky Fadillah', 'KTP', 'hrd_files/rizky_fadillah/ktp_rizky_fadillah.pdf', 'Kartu Tanda Penduduk', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(36, 'Rizky Fadillah', 'NPWP - Rizky Fadillah', 'NPWP', 'hrd_files/rizky_fadillah/npwp_rizky_fadillah.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(37, 'Rizky Fadillah', 'Ijazah - Rizky Fadillah', 'Ijazah', 'hrd_files/rizky_fadillah/ijazah_rizky_fadillah.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(38, 'Rizky Fadillah', 'SK Pengangkatan - Rizky Fadillah', 'SK Pengangkatan', 'hrd_files/rizky_fadillah/sk_rizky_fadillah.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(39, 'Rizky Fadillah', 'BPJS Kesehatan - Rizky Fadillah', 'BPJS Kesehatan', 'hrd_files/rizky_fadillah/bpjs_kes_rizky_fadillah.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(40, 'Rizky Fadillah', 'BPJS TK - Rizky Fadillah', 'BPJS TK', 'hrd_files/rizky_fadillah/bpjs_tk_rizky_fadillah.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(41, 'Yusuf Hidayat', 'SK Pengangkatan - Yusuf Hidayat', 'SK Pengangkatan', 'hrd_files/yusuf_hidayat/sk_yusuf_hidayat.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(42, 'Yusuf Hidayat', 'Kontrak Kerja - Yusuf Hidayat', 'Kontrak Kerja', 'hrd_files/yusuf_hidayat/kontrak_yusuf_hidayat.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(43, 'Yusuf Hidayat', 'BPJS Kesehatan - Yusuf Hidayat', 'BPJS Kesehatan', 'hrd_files/yusuf_hidayat/bpjs_kes_yusuf_hidayat.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(44, 'Yusuf Hidayat', 'BPJS TK - Yusuf Hidayat', 'BPJS TK', 'hrd_files/yusuf_hidayat/bpjs_tk_yusuf_hidayat.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(45, 'Linda Permata', 'KTP - Linda Permata', 'KTP', 'hrd_files/linda_permata/ktp_linda_permata.pdf', 'Kartu Tanda Penduduk', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(46, 'Linda Permata', 'NPWP - Linda Permata', 'NPWP', 'hrd_files/linda_permata/npwp_linda_permata.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(47, 'Linda Permata', 'Ijazah - Linda Permata', 'Ijazah', 'hrd_files/linda_permata/ijazah_linda_permata.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(48, 'Linda Permata', 'SK Pengangkatan - Linda Permata', 'SK Pengangkatan', 'hrd_files/linda_permata/sk_linda_permata.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(49, 'Wahyu Nugroho', 'KTP - Wahyu Nugroho', 'KTP', 'hrd_files/wahyu_nugroho/ktp_wahyu_nugroho.pdf', 'Kartu Tanda Penduduk', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(50, 'Wahyu Nugroho', 'BPJS Kesehatan - Wahyu Nugroho', 'BPJS Kesehatan', 'hrd_files/wahyu_nugroho/bpjs_kes_wahyu_nugroho.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(51, 'Wahyu Nugroho', 'BPJS TK - Wahyu Nugroho', 'BPJS TK', 'hrd_files/wahyu_nugroho/bpjs_tk_wahyu_nugroho.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(52, 'Fitri Handayani', 'NPWP - Fitri Handayani', 'NPWP', 'hrd_files/fitri_handayani/npwp_fitri_handayani.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(53, 'Fitri Handayani', 'SK Pengangkatan - Fitri Handayani', 'SK Pengangkatan', 'hrd_files/fitri_handayani/sk_fitri_handayani.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(54, 'Fitri Handayani', 'Kontrak Kerja - Fitri Handayani', 'Kontrak Kerja', 'hrd_files/fitri_handayani/kontrak_fitri_handayani.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(55, 'Fitri Handayani', 'BPJS Kesehatan - Fitri Handayani', 'BPJS Kesehatan', 'hrd_files/fitri_handayani/bpjs_kes_fitri_handayani.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(56, 'Fitri Handayani', 'BPJS TK - Fitri Handayani', 'BPJS TK', 'hrd_files/fitri_handayani/bpjs_tk_fitri_handayani.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(57, 'Dody Kurniawan', 'KTP - Dody Kurniawan', 'KTP', 'hrd_files/dody_kurniawan/ktp_dody_kurniawan.pdf', 'Kartu Tanda Penduduk', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(58, 'Dody Kurniawan', 'Ijazah - Dody Kurniawan', 'Ijazah', 'hrd_files/dody_kurniawan/ijazah_dody_kurniawan.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(59, 'Dody Kurniawan', 'SK Pengangkatan - Dody Kurniawan', 'SK Pengangkatan', 'hrd_files/dody_kurniawan/sk_dody_kurniawan.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(60, 'Dody Kurniawan', 'Kontrak Kerja - Dody Kurniawan', 'Kontrak Kerja', 'hrd_files/dody_kurniawan/kontrak_dody_kurniawan.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(61, 'Dody Kurniawan', 'BPJS Kesehatan - Dody Kurniawan', 'BPJS Kesehatan', 'hrd_files/dody_kurniawan/bpjs_kes_dody_kurniawan.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(62, 'Dody Kurniawan', 'BPJS TK - Dody Kurniawan', 'BPJS TK', 'hrd_files/dody_kurniawan/bpjs_tk_dody_kurniawan.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(63, 'Teguh Santosa', 'KTP - Teguh Santosa', 'KTP', 'hrd_files/teguh_santosa/ktp_teguh_santosa.pdf', 'Kartu Tanda Penduduk', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(64, 'Teguh Santosa', 'NPWP - Teguh Santosa', 'NPWP', 'hrd_files/teguh_santosa/npwp_teguh_santosa.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(65, 'Teguh Santosa', 'Ijazah - Teguh Santosa', 'Ijazah', 'hrd_files/teguh_santosa/ijazah_teguh_santosa.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(66, 'Teguh Santosa', 'SK Pengangkatan - Teguh Santosa', 'SK Pengangkatan', 'hrd_files/teguh_santosa/sk_teguh_santosa.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(67, 'Teguh Santosa', 'Kontrak Kerja - Teguh Santosa', 'Kontrak Kerja', 'hrd_files/teguh_santosa/kontrak_teguh_santosa.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(68, 'Teguh Santosa', 'BPJS Kesehatan - Teguh Santosa', 'BPJS Kesehatan', 'hrd_files/teguh_santosa/bpjs_kes_teguh_santosa.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(69, 'Teguh Santosa', 'BPJS TK - Teguh Santosa', 'BPJS TK', 'hrd_files/teguh_santosa/bpjs_tk_teguh_santosa.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(70, 'Arif Budiman', 'KTP - Arif Budiman', 'KTP', 'hrd_files/arif_budiman/ktp_arif_budiman.pdf', 'Kartu Tanda Penduduk', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(71, 'Arif Budiman', 'NPWP - Arif Budiman', 'NPWP', 'hrd_files/arif_budiman/npwp_arif_budiman.pdf', 'Nomor Pokok Wajib Pajak', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(72, 'Arif Budiman', 'Ijazah - Arif Budiman', 'Ijazah', 'hrd_files/arif_budiman/ijazah_arif_budiman.pdf', 'Ijazah pendidikan terakhir', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(73, 'Arif Budiman', 'SK Pengangkatan - Arif Budiman', 'SK Pengangkatan', 'hrd_files/arif_budiman/sk_arif_budiman.pdf', 'Surat Keputusan Pengangkatan Pegawai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(74, 'Arif Budiman', 'Kontrak Kerja - Arif Budiman', 'Kontrak Kerja', 'hrd_files/arif_budiman/kontrak_arif_budiman.pdf', 'Perjanjian kerja yang telah ditandatangani', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(75, 'Arif Budiman', 'BPJS Kesehatan - Arif Budiman', 'BPJS Kesehatan', 'hrd_files/arif_budiman/bpjs_kes_arif_budiman.pdf', 'Kartu BPJS Kesehatan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(76, 'Arif Budiman', 'BPJS TK - Arif Budiman', 'BPJS TK', 'hrd_files/arif_budiman/bpjs_tk_arif_budiman.pdf', 'Kartu BPJS Ketenagakerjaan', '2026-07-11 21:07:07', '2026-07-11 21:07:07');

-- Dumping structure for table apyrent.hutang_vendors
CREATE TABLE IF NOT EXISTS `hutang_vendors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `dibayar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sisa` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jatuh_tempo` date NOT NULL,
  `status` enum('lunas','belum_lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_lunas',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.hutang_vendors: ~5 rows (approximately)
INSERT INTO `hutang_vendors` (`id`, `nama_vendor`, `kategori`, `nominal`, `dibayar`, `sisa`, `jatuh_tempo`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'PT Sinar Abadi', 'Sparepart', 5000000.00, 2000000.00, 3000000.00, '2026-07-22', 'belum_lunas', 'Pembelian sparepart mesin', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 'CV Mitra Jaya', 'Service', 2500000.00, 2500000.00, 0.00, '2026-07-07', 'lunas', 'Service kendaraan fleet', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 'PT Otomotif Nusantara', 'Aksesoris', 1200000.00, 500000.00, 700000.00, '2026-07-15', 'belum_lunas', 'Pembelian aksesoris mobil', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 'UD Jaya Mandiri', 'Ban', 3000000.00, 1000000.00, 2000000.00, '2026-07-27', 'belum_lunas', 'Pembelian ban kendaraan', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 'PT Diesel Prima', 'Mesin', 8000000.00, 8000000.00, 0.00, '2026-07-10', 'lunas', 'Perbaikan mesin besar', '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.induk_assets
CREATE TABLE IF NOT EXISTS `induk_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_perolehan` date NOT NULL,
  `harga_perolehan` bigint NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `umur_ekonomis` int NOT NULL,
  `metode_penyusutan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `induk_assets_kode_aset_unique` (`kode_aset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.induk_assets: ~0 rows (approximately)

-- Dumping structure for table apyrent.induk_proyeks
CREATE TABLE IF NOT EXISTS `induk_proyeks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `klien_vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mulai` date NOT NULL,
  `target_selesai` date NOT NULL,
  `progres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0%',
  `nilai_proyek` bigint NOT NULL DEFAULT '0',
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `induk_proyeks_kode_unique` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.induk_proyeks: ~6 rows (approximately)
INSERT INTO `induk_proyeks` (`id`, `kode`, `nama_proyek`, `jenis`, `klien_vendor`, `pic`, `status`, `mulai`, `target_selesai`, `progres`, `nilai_proyek`, `lokasi`, `created_at`, `updated_at`) VALUES
	(1, 'PRJ001', 'Renovasi Pool Kendaraan Bekasi', 'Internal', '-', 'Rudi', 'Berjalan', '2026-01-01', '2026-03-31', '65%', 450000000, 'Bekasi', NULL, NULL),
	(2, 'PRJ002', 'Pengadaan Armada Bus Pariwisata', 'Internal', '-', 'Rina', 'Approved', '2026-02-01', '2026-04-30', '20%', 1500000000, 'Jakarta', NULL, NULL),
	(3, 'PRJ003', 'Sistem GPS & Monitoring Armada', 'Internal', 'PT TechMaps', 'Ivan', 'Berjalan', '2026-01-15', '2026-05-15', '45%', 210000000, 'Bandung', NULL, NULL),
	(4, 'PRJ004', 'Renovasi Kantor Pusat Tangerang', 'Internal', '-', 'Sari', 'Plan', '2026-04-01', '2026-06-30', '0%', 320000000, 'Tangerang', NULL, NULL),
	(5, 'PRJ005', 'Layanan Antar Jemput PT Sinar Abadi', 'Eksternal', 'PT Sinar Abadi', 'Andi', 'Berjalan', '2026-02-15', '2026-12-31', '30%', 850000000, 'Surabaya', NULL, NULL),
	(6, 'PRJ006', 'Workshop Pelatihan Driver Safety', 'Internal', '-', 'Budi', 'Selesai', '2025-12-01', '2025-12-31', '100%', 75000000, 'Jakarta', NULL, NULL);

-- Dumping structure for table apyrent.invoices
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penawaran_id` bigint unsigned DEFAULT NULL,
  `kontrak_id` bigint unsigned DEFAULT NULL,
  `kendaraan_id` bigint unsigned DEFAULT NULL,
  `type` enum('perorangan','perusahaan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'perorangan',
  `invoice_no` text COLLATE utf8mb4_unicode_ci,
  `order_no` text COLLATE utf8mb4_unicode_ci,
  `customer_name` text COLLATE utf8mb4_unicode_ci,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `contact_person` text COLLATE utf8mb4_unicode_ci,
  `telephone` text COLLATE utf8mb4_unicode_ci,
  `email` text COLLATE utf8mb4_unicode_ci,
  `satuan` text COLLATE utf8mb4_unicode_ci,
  `invoice_date` date DEFAULT NULL,
  `pengirim` text COLLATE utf8mb4_unicode_ci,
  `staff` text COLLATE utf8mb4_unicode_ci,
  `name_staff` text COLLATE utf8mb4_unicode_ci,
  `ttd_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direktur` text COLLATE utf8mb4_unicode_ci,
  `name_direktur` text COLLATE utf8mb4_unicode_ci,
  `ttd_direktur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','partial','overdue','lunas') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_email_sent_at` timestamp NULL DEFAULT NULL,
  `payment_status` enum('unpaid','paid') COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `ppn` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pph` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_penawaran_id_foreign` (`penawaran_id`),
  KEY `invoices_kontrak_id_foreign` (`kontrak_id`),
  KEY `invoices_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `invoices_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_kontrak_id_foreign` FOREIGN KEY (`kontrak_id`) REFERENCES `inv_kontraks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.invoices: ~9 rows (approximately)
INSERT INTO `invoices` (`id`, `penawaran_id`, `kontrak_id`, `kendaraan_id`, `type`, `invoice_no`, `order_no`, `customer_name`, `customer_address`, `contact_person`, `telephone`, `email`, `satuan`, `invoice_date`, `pengirim`, `staff`, `name_staff`, `ttd_staff`, `direktur`, `name_direktur`, `ttd_direktur`, `status`, `last_email_sent_at`, `payment_status`, `ppn`, `pph`, `total`, `created_at`, `updated_at`) VALUES
	(1, 3, 1, NULL, 'perorangan', 'INV-2026-0001', 'ORD-0001', 'PT Teknologi Nusantara', 'Jl. Contoh No.1, Jakarta', 'Hendra Gunawan', '081273741895', 'pt.teknologi.nusantara@email.com', 'Bulan', '2025-12-20', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 286000.00, 52000.00, 2834000.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 4, 2, NULL, 'perusahaan', 'INV-2026-0002', 'ORD-0002', 'UD Sumber Rejeki', 'Jl. Contoh No.2, Jakarta', 'Dewi Lestari', '081289588628', 'ud.sumber.rejeki@email.com', 'Hari', '2026-04-21', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 396000.00, 72000.00, 3924000.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 5, 3, NULL, 'perusahaan', 'INV-2026-0003', 'ORD-0003', 'PT Logistik Andalan', 'Jl. Contoh No.3, Jakarta', 'Rizal Fahmi', '081261960928', 'pt.logistik.andalan@email.com', 'Tahun', '2025-09-28', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 60500.00, 11000.00, 599500.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 9, 4, NULL, 'perorangan', 'INV-2026-0004', 'ORD-0004', 'CV Perdana Sejahtera', 'Jl. Contoh No.4, Jakarta', 'Wahyu Nugroho', '081294464263', 'cv.perdana.sejahtera@email.com', 'Bulan', '2026-01-27', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 93500.00, 17000.00, 926500.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 10, 5, NULL, 'perusahaan', 'INV-2026-0005', 'ORD-0005', 'PT Aneka Niaga Indonesia', 'Jl. Contoh No.5, Jakarta', 'Fitri Handayani', '081219551044', 'pt.aneka.niaga.indonesia@email.com', 'Hari', '2026-06-29', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 313500.00, 57000.00, 3106500.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(6, 11, 6, NULL, 'perusahaan', 'INV-2026-0006', 'ORD-0006', 'PT Maju Jaya Abadi', 'Jl. Contoh No.6, Jakarta', 'Budi Hartono', '081225648038', 'pt.maju.jaya.abadi@email.com', 'Tahun', '2025-10-07', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'partial', NULL, 'unpaid', 71500.00, 13000.00, 708500.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 15, 7, NULL, 'perorangan', 'INV-2026-0007', 'ORD-0007', 'PT Logistik Andalan', 'Jl. Contoh No.7, Jakarta', 'Rizal Fahmi', '081216093000', 'pt.logistik.andalan@email.com', 'Bulan', '2025-11-27', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'overdue', NULL, 'unpaid', 165000.00, 30000.00, 1635000.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(8, 16, 8, NULL, 'perusahaan', 'INV-2026-0008', 'ORD-0008', 'CV Karya Utama', 'Jl. Contoh No.8, Jakarta', 'Nur Hidayah', '081280657097', 'cv.karya.utama@email.com', 'Hari', '2026-04-02', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'lunas', NULL, 'paid', 55000.00, 10000.00, 545000.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(9, 17, 9, NULL, 'perusahaan', 'INV-2026-0009', 'ORD-0009', 'PT Solusi Transportasi', 'Jl. Contoh No.9, Jakarta', 'Agus Setiawan', '081220026091', 'pt.solusi.transportasi@email.com', 'Tahun', '2026-06-04', 'Divisi Finance', 'Staff Finance', 'Wahyu Nugroho', NULL, 'Direktur', 'Budi Santoso', NULL, 'draft', NULL, 'unpaid', 93500.00, 17000.00, 926500.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.invoice_kendaraans
CREATE TABLE IF NOT EXISTS `invoice_kendaraans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_kendaraans_invoice_id_kendaraan_id_unique` (`invoice_id`,`kendaraan_id`),
  KEY `invoice_kendaraans_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `invoice_kendaraans_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_kendaraans_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.invoice_kendaraans: ~0 rows (approximately)

-- Dumping structure for table apyrent.invoice_kontraks
CREATE TABLE IF NOT EXISTS `invoice_kontraks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `kontrak_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_kontraks_invoice_id_kontrak_id_unique` (`invoice_id`,`kontrak_id`),
  KEY `invoice_kontraks_kontrak_id_foreign` (`kontrak_id`),
  CONSTRAINT `invoice_kontraks_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_kontraks_kontrak_id_foreign` FOREIGN KEY (`kontrak_id`) REFERENCES `inv_kontraks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.invoice_kontraks: ~0 rows (approximately)

-- Dumping structure for table apyrent.invoice_payments
CREATE TABLE IF NOT EXISTS `invoice_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `method` text COLLATE utf8mb4_unicode_ci,
  `transaction_id` text COLLATE utf8mb4_unicode_ci,
  `file_pembayaran` text COLLATE utf8mb4_unicode_ci,
  `status` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_payments_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.invoice_payments: ~6 rows (approximately)
INSERT INTO `invoice_payments` (`id`, `invoice_id`, `amount`, `payment_date`, `method`, `transaction_id`, `file_pembayaran`, `status`, `created_at`, `updated_at`) VALUES
	(1, 2, 1962000.00, '2026-04-26', 'Cek/Giro', 'TXN-E36314E624', NULL, 'verified', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 2, 1177200.00, '2026-06-03', 'Tunai', 'TXN-F8BC2FBE2C', NULL, 'pending', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 4, 926500.00, '2026-02-24', 'Virtual Account', 'TXN-FECEFFD2BA', NULL, 'verified', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 6, 354250.00, '2025-10-15', 'Tunai', 'TXN-710FB21AC4', NULL, 'verified', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 6, 212550.00, '2025-11-08', 'Tunai', 'TXN-D105AE60B3', NULL, 'pending', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 8, 545000.00, '2026-04-13', 'Transfer Bank', 'TXN-95454947CD', NULL, 'verified', '2026-07-11 21:07:06', '2026-07-11 21:07:06');

-- Dumping structure for table apyrent.invoice_penawarans
CREATE TABLE IF NOT EXISTS `invoice_penawarans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `penawaran_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_penawarans_invoice_id_penawaran_id_unique` (`invoice_id`,`penawaran_id`),
  KEY `invoice_penawarans_penawaran_id_foreign` (`penawaran_id`),
  CONSTRAINT `invoice_penawarans_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_penawarans_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.invoice_penawarans: ~0 rows (approximately)

-- Dumping structure for table apyrent.invoice_periodes
CREATE TABLE IF NOT EXISTS `invoice_periodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `periode_awal` date DEFAULT NULL,
  `periode_akhir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_periodes_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_periodes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.invoice_periodes: ~9 rows (approximately)
INSERT INTO `invoice_periodes` (`id`, `invoice_id`, `periode_awal`, `periode_akhir`, `created_at`, `updated_at`) VALUES
	(1, 1, '2025-12-20', '2026-05-20', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 2, '2026-04-21', '2026-07-21', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 3, '2025-09-28', '2025-11-28', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 4, '2026-01-27', '2026-07-27', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 5, '2026-06-29', '2026-09-29', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(6, 6, '2025-10-07', '2026-02-07', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 7, '2025-11-27', '2025-12-27', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(8, 8, '2026-04-02', '2026-07-02', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(9, 9, '2026-06-04', '2026-08-04', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.invoice_remaks
CREATE TABLE IF NOT EXISTS `invoice_remaks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `periode_id` bigint unsigned DEFAULT NULL,
  `remaks` text COLLATE utf8mb4_unicode_ci,
  `qty` int unsigned DEFAULT '1',
  `price` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_remaks_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_remaks_periode_id_foreign` (`periode_id`),
  CONSTRAINT `invoice_remaks_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_remaks_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `invoice_periodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.invoice_remaks: ~22 rows (approximately)
INSERT INTO `invoice_remaks` (`id`, `invoice_id`, `periode_id`, `remaks`, `qty`, `price`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Sewa Kendaraan Operasional', 2, 1472902.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 1, 1, 'Biaya Driver', 2, 4197467.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 1, 1, 'Bahan Bakar', 3, 4877085.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 2, 2, 'Biaya Driver', 3, 3630633.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 3, 3, 'Bahan Bakar', 3, 4355269.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(6, 3, 3, 'Biaya Perawatan', 4, 3553483.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 3, 3, 'Asuransi Kendaraan', 3, 4183956.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(8, 4, 4, 'Biaya Perawatan', 3, 3943352.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(9, 4, 4, 'Asuransi Kendaraan', 3, 4397961.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(10, 4, 4, 'Biaya Administrasi', 2, 3240143.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(11, 5, 5, 'Asuransi Kendaraan', 3, 2603613.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(12, 5, 5, 'Biaya Administrasi', 2, 3697742.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(13, 6, 6, 'Biaya Administrasi', 2, 4869876.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(14, 6, 6, 'Sewa Kendaraan Operasional', 3, 4140995.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(15, 6, 6, 'Biaya Driver', 4, 3530883.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(16, 7, 7, 'Sewa Kendaraan Operasional', 2, 3895726.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(17, 7, 7, 'Biaya Driver', 3, 1619795.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(18, 7, 7, 'Bahan Bakar', 4, 1610317.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(19, 8, 8, 'Biaya Driver', 1, 612247.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(20, 8, 8, 'Bahan Bakar', 1, 2676706.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(21, 8, 8, 'Biaya Perawatan', 2, 1342994.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(22, 9, 9, 'Bahan Bakar', 4, 1953444.00, '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.inv_kontraks
CREATE TABLE IF NOT EXISTS `inv_kontraks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penawaran_id` bigint unsigned DEFAULT NULL,
  `no_kontrak` text COLLATE utf8mb4_unicode_ci,
  `tanggal_kontrak` date DEFAULT NULL,
  `perjanjian_pembayaran` date DEFAULT NULL,
  `pihak_pertama` text COLLATE utf8mb4_unicode_ci,
  `contact_pertama` text COLLATE utf8mb4_unicode_ci,
  `pihak_kedua` text COLLATE utf8mb4_unicode_ci,
  `contact_kedua` text COLLATE utf8mb4_unicode_ci,
  `file_kontrak` text COLLATE utf8mb4_unicode_ci,
  `file_persyaratan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('dibuat','pending','approved','active','rejected','expired','completed','terminated') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inv_kontraks_penawaran_id_foreign` (`penawaran_id`),
  CONSTRAINT `inv_kontraks_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.inv_kontraks: ~9 rows (approximately)
INSERT INTO `inv_kontraks` (`id`, `penawaran_id`, `no_kontrak`, `tanggal_kontrak`, `perjanjian_pembayaran`, `pihak_pertama`, `contact_pertama`, `pihak_kedua`, `contact_kedua`, `file_kontrak`, `file_persyaratan`, `status`, `created_at`, `updated_at`) VALUES
	(1, 3, 'KTR-0003', '2025-12-19', '2026-10-19', 'PT Apyrent Indonesia', '021-12345678', 'PT Teknologi Nusantara', 'Hendra Gunawan', NULL, NULL, 'pending', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 4, 'KTR-0004', '2026-04-15', '2026-12-15', 'PT Apyrent Indonesia', '021-12345678', 'UD Sumber Rejeki', 'Dewi Lestari', NULL, NULL, 'approved', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 5, 'KTR-0005', '2025-09-24', '2025-12-24', 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, 'active', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 9, 'KTR-0009', '2026-01-26', '2026-07-26', 'PT Apyrent Indonesia', '021-12345678', 'CV Perdana Sejahtera', 'Wahyu Nugroho', NULL, NULL, 'completed', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 10, 'KTR-0010', '2026-06-28', '2027-02-28', 'PT Apyrent Indonesia', '021-12345678', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', NULL, NULL, 'terminated', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(6, 11, 'KTR-0011', '2025-09-30', '2026-08-30', 'PT Apyrent Indonesia', '021-12345678', 'PT Maju Jaya Abadi', 'Budi Hartono', NULL, NULL, 'pending', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 15, 'KTR-0015', '2025-11-21', '2026-10-21', 'PT Apyrent Indonesia', '021-12345678', 'PT Logistik Andalan', 'Rizal Fahmi', NULL, NULL, 'approved', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(8, 16, 'KTR-0016', '2026-03-31', '2026-12-01', 'PT Apyrent Indonesia', '021-12345678', 'CV Karya Utama', 'Nur Hidayah', NULL, NULL, 'active', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(9, 17, 'KTR-0017', '2026-06-01', '2026-08-01', 'PT Apyrent Indonesia', '021-12345678', 'PT Solusi Transportasi', 'Agus Setiawan', NULL, NULL, 'completed', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.inv_penawarans
CREATE TABLE IF NOT EXISTS `inv_penawarans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_penawaran` text COLLATE utf8mb4_unicode_ci,
  `tanggal_penawaran` date DEFAULT NULL,
  `kepada` text COLLATE utf8mb4_unicode_ci,
  `up` text COLLATE utf8mb4_unicode_ci,
  `perihal` text COLLATE utf8mb4_unicode_ci,
  `customer_name` text COLLATE utf8mb4_unicode_ci,
  `contact_person` text COLLATE utf8mb4_unicode_ci,
  `pengirim` text COLLATE utf8mb4_unicode_ci,
  `periode` int unsigned DEFAULT NULL,
  `staff` text COLLATE utf8mb4_unicode_ci,
  `name_staff` text COLLATE utf8mb4_unicode_ci,
  `direktur` text COLLATE utf8mb4_unicode_ci,
  `name_direktur` text COLLATE utf8mb4_unicode_ci,
  `status` enum('dibuat','pending','approved','active','rejected','expired','completed','terminated') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `total` text COLLATE utf8mb4_unicode_ci,
  `file_penawaran` text COLLATE utf8mb4_unicode_ci,
  `file_persyaratan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.inv_penawarans: ~20 rows (approximately)
INSERT INTO `inv_penawarans` (`id`, `no_penawaran`, `tanggal_penawaran`, `kepada`, `up`, `perihal`, `customer_name`, `contact_person`, `pengirim`, `periode`, `staff`, `name_staff`, `direktur`, `name_direktur`, `status`, `total`, `file_penawaran`, `file_persyaratan`, `created_at`, `updated_at`) VALUES
	(1, 'PNW-0001', '2026-02-09', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '850000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(2, 'PNW-0002', '2025-11-18', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '950000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(3, 'PNW-0003', '2025-12-10', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '2600000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'PNW-0004', '2026-04-04', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '3600000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(5, 'PNW-0005', '2025-09-21', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '550000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(6, 'PNW-0006', '2026-01-30', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1100000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 'PNW-0007', '2025-10-16', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1500000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(8, 'PNW-0008', '2025-12-24', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '500000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(9, 'PNW-0009', '2026-01-14', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '850000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(10, 'PNW-0010', '2026-06-14', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'active', '2850000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(11, 'PNW-0011', '2025-09-25', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Penawaran Sewa Kendaraan Operasional', 'PT Maju Jaya Abadi', 'Budi Hartono', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '650000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(12, 'PNW-0012', '2026-01-28', 'CV Berkah Mandiri', 'Siti Rahayu', 'Penawaran Sewa Armada Angkutan', 'CV Berkah Mandiri', 'Siti Rahayu', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '4800000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(13, 'PNW-0013', '2025-12-12', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Penawaran Layanan Transportasi', 'PT Teknologi Nusantara', 'Hendra Gunawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '550000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(14, 'PNW-0014', '2026-06-26', 'UD Sumber Rejeki', 'Dewi Lestari', 'Penawaran Sewa Kendaraan Proyek', 'UD Sumber Rejeki', 'Dewi Lestari', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'pending', '3300000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(15, 'PNW-0015', '2025-11-16', 'PT Logistik Andalan', 'Rizal Fahmi', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Logistik Andalan', 'Rizal Fahmi', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'approved', '1500000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(16, 'PNW-0016', '2026-03-20', 'CV Karya Utama', 'Nur Hidayah', 'Penawaran Sewa Kendaraan Operasional', 'CV Karya Utama', 'Nur Hidayah', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '500000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(17, 'PNW-0017', '2026-05-25', 'PT Solusi Transportasi', 'Agus Setiawan', 'Penawaran Sewa Armada Angkutan', 'PT Solusi Transportasi', 'Agus Setiawan', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '850000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(18, 'PNW-0018', '2026-01-07', 'PT Global Rentcar', 'Maya Anggraini', 'Penawaran Layanan Transportasi', 'PT Global Rentcar', 'Maya Anggraini', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '1900000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(19, 'PNW-0019', '2026-01-15', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Penawaran Sewa Kendaraan Proyek', 'CV Perdana Sejahtera', 'Wahyu Nugroho', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '2600000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08'),
	(20, 'PNW-0020', '2026-06-01', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Penawaran Rental Kendaraan Jangka Panjang', 'PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Divisi Sales', 1, 'Staff Sales', 'Eko Prasetyo', 'Direktur', 'Budi Santoso', 'expired', '4800000', NULL, NULL, '2026-07-11 21:07:05', '2026-07-11 21:28:08');

-- Dumping structure for table apyrent.inv_penawaran_items
CREATE TABLE IF NOT EXISTS `inv_penawaran_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penawaran_id` bigint unsigned DEFAULT NULL,
  `kendaraan_id` bigint unsigned DEFAULT NULL,
  `qty` int unsigned DEFAULT '1',
  `tahun_unit` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(15,2) DEFAULT '0.00',
  `durasi` int unsigned DEFAULT '1',
  `satuan_durasi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inv_penawaran_items_penawaran_id_foreign` (`penawaran_id`),
  KEY `inv_penawaran_items_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `inv_penawaran_items_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inv_penawaran_items_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.inv_penawaran_items: ~30 rows (approximately)
INSERT INTO `inv_penawaran_items` (`id`, `penawaran_id`, `kendaraan_id`, `qty`, `tahun_unit`, `price`, `durasi`, `satuan_durasi`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 2, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 3, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 3, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 4, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(6, 4, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(7, 5, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(8, 6, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(9, 7, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(10, 7, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(11, 8, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(12, 9, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(13, 10, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(14, 10, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(15, 11, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(16, 12, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(17, 12, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(18, 13, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(19, 14, NULL, 1, '2022', 1100000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(20, 14, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(21, 15, NULL, 1, '2020', 750000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(22, 15, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(23, 16, NULL, 1, '2021', 500000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(24, 17, NULL, 1, '2022', 850000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(25, 18, NULL, 1, '2021', 950000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(26, 18, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(27, 19, NULL, 1, '2020', 650000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(28, 19, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(29, 20, NULL, 1, '2023', 1200000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(30, 20, NULL, 1, '2021', 550000.00, 1, 'month', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.inv_summaries
CREATE TABLE IF NOT EXISTS `inv_summaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penawaran_id` bigint unsigned DEFAULT NULL,
  `kontrak_id` bigint unsigned DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `type` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_amount` decimal(15,2) DEFAULT '0.00',
  `payment_status` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inv_summaries_penawaran_id_foreign` (`penawaran_id`),
  KEY `inv_summaries_kontrak_id_foreign` (`kontrak_id`),
  KEY `inv_summaries_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `inv_summaries_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inv_summaries_kontrak_id_foreign` FOREIGN KEY (`kontrak_id`) REFERENCES `inv_kontraks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inv_summaries_penawaran_id_foreign` FOREIGN KEY (`penawaran_id`) REFERENCES `inv_penawarans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.inv_summaries: ~9 rows (approximately)
INSERT INTO `inv_summaries` (`id`, `penawaran_id`, `kontrak_id`, `invoice_id`, `type`, `total_amount`, `paid_amount`, `remaining_amount`, `payment_status`, `created_at`, `updated_at`) VALUES
	(1, 3, 1, 1, 'perorangan', 2834000.00, 0.00, 2834000.00, 'unpaid', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 4, 2, 2, 'perusahaan', 3924000.00, 1962000.00, 1962000.00, 'partial', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 5, 3, 3, 'perusahaan', 599500.00, 0.00, 599500.00, 'unpaid', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 9, 4, 4, 'perorangan', 926500.00, 926500.00, 0.00, 'lunas', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 10, 5, 5, 'perusahaan', 3106500.00, 0.00, 3106500.00, 'unpaid', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 11, 6, 6, 'perusahaan', 708500.00, 354250.00, 354250.00, 'partial', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 15, 7, 7, 'perorangan', 1635000.00, 0.00, 1635000.00, 'unpaid', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 16, 8, 8, 'perusahaan', 545000.00, 545000.00, 0.00, 'lunas', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 17, 9, 9, 'perusahaan', 926500.00, 0.00, 926500.00, 'unpaid', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 1, 2, 1, 'Rental', 1900.00, 19000.00, 0.00, 'Paid', '2026-07-13 06:48:17', '2026-07-13 06:48:17'),
	(11, 10, 9, 1, 'Rental', 67.00, 0.00, 67.00, 'Unpaid', '2026-07-13 06:48:29', '2026-07-13 06:48:29');

-- Dumping structure for table apyrent.itasset_management
CREATE TABLE IF NOT EXISTS `itasset_management` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengguna` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_beli` year NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.itasset_management: ~5 rows (approximately)
INSERT INTO `itasset_management` (`id`, `kode_aset`, `nama_aset`, `jenis`, `lokasi`, `pengguna`, `merek`, `tahun_beli`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'AST-001', 'Laptop Dell XPS 15', 'Laptop', 'Ruang IT Lt.2', 'Budi Santoso', 'Dell', '2022', 'Aktif', 'Unit utama developer', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'AST-002', 'HP LaserJet Pro M404', 'Printer', 'Ruang Admin', 'Sari Dewi', 'HP', '2021', 'Aktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'AST-003', 'MacBook Pro M2', 'Laptop', 'Ruang Desain', 'Andi Wijaya', 'Apple', '2023', 'Aktif', 'Untuk tim desain grafis', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'AST-004', 'Monitor LG 27 Inch 4K', 'Monitor', 'Ruang IT Lt.2', 'Rudi Hermawan', 'LG', '2022', 'Aktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'AST-005', 'Cisco Switch 48 Port', 'Network', 'Ruang Server', 'Tim IT', 'Cisco', '2020', 'Rusak', 'Port 12-15 tidak berfungsi', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.jenis
CREATE TABLE IF NOT EXISTS `jenis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nama_jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jenis_user_id_foreign` (`user_id`),
  CONSTRAINT `jenis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.jenis: ~6 rows (approximately)
INSERT INTO `jenis` (`id`, `user_id`, `nama_jenis`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Mobil SUV', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(2, 1, 'Mobil MPV', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(3, 1, 'Mobil Sedan', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(4, 1, 'Pickup', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(5, 1, 'Truck', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(6, 1, 'Bus Pariwisata', '2026-07-11 21:07:01', '2026-07-11 21:07:01');

-- Dumping structure for table apyrent.jenis_asuransi
CREATE TABLE IF NOT EXISTS `jenis_asuransi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.jenis_asuransi: ~3 rows (approximately)
INSERT INTO `jenis_asuransi` (`id`, `nama_jenis`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'All Risk', 'Menanggung kerusakan ringan dan berat', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(2, 'TLO', 'Total Loss Only', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(3, 'Comprehensive', 'Perlindungan menyeluruh kendaraan', '2026-07-11 21:07:01', '2026-07-11 21:07:01');

-- Dumping structure for table apyrent.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.jobs: ~0 rows (approximately)

-- Dumping structure for table apyrent.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `Pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.job_batches: ~0 rows (approximately)

-- Dumping structure for table apyrent.kampanyes
CREATE TABLE IF NOT EXISTS `kampanyes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_kampanye` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kampanye` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_kampanye` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_segment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `subjek_pesan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi_pesan_ringkas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dijadwalkan',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kampanyes_id_kampanye_unique` (`id_kampanye`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.kampanyes: ~2 rows (approximately)
INSERT INTO `kampanyes` (`id`, `id_kampanye`, `nama_kampanye`, `tipe_kampanye`, `channel`, `target_segment`, `tanggal_mulai`, `tanggal_akhir`, `subjek_pesan`, `isi_pesan_ringkas`, `status`, `pic`, `created_at`, `updated_at`) VALUES
	(1, 'MKT001', 'Promo Rental Akhir Tahun', 'Promosi', 'Email', 'Pelanggan Aktif', '2026-12-25', '2026-12-31', 'Diskon Spesial Akhir Tahun!', 'Dapatkan diskon 20% untuk rental mobil', 'Dijadwalkan', 'Rina Marketing', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'MKT002', 'Re-engagement Campaign', 'Retensi', 'WhatsApp', 'Inaktif 6 Bulan', '2026-08-01', '2026-08-15', 'Kami Merindukan Anda', 'Rental lagi dan dapat voucher', 'Aktif', 'Ahmad Marketing', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.kendaraan
CREATE TABLE IF NOT EXISTS `kendaraan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `jenis_id` bigint unsigned NOT NULL,
  `nopol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pemilik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `merk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_pembuatan` year DEFAULT NULL,
  `tahun_perakitan` year DEFAULT NULL,
  `isi_silinder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warna` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rangka` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_mesin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_bpkb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warna_tnkb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bahan_bakar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_urut_pendaftaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_sewa_per_hari` bigint NOT NULL DEFAULT '0',
  `harga_sewa_per_jam` bigint NOT NULL DEFAULT '0',
  `batas_biaya` bigint NOT NULL DEFAULT '0',
  `dokumen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `masa_berlaku` date DEFAULT NULL,
  `kilometer_sekarang` int NOT NULL DEFAULT '0',
  `limit_km_service` int NOT NULL DEFAULT '0',
  `limit_bulan_service` int NOT NULL DEFAULT '0',
  `km_terakhir_service` int NOT NULL DEFAULT '0',
  `tanggal_terakhir_service` date DEFAULT NULL,
  `status_service` enum('aman','service') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aman',
  `status_kendaraan` enum('tersedia','disewa','service','bermasalah') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kendaraan_user_id_foreign` (`user_id`),
  KEY `kendaraan_jenis_id_foreign` (`jenis_id`),
  KEY `kendaraan_member_id_foreign` (`member_id`),
  CONSTRAINT `kendaraan_jenis_id_foreign` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kendaraan_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kendaraan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.kendaraan: ~50 rows (approximately)
INSERT INTO `kendaraan` (`id`, `user_id`, `member_id`, `jenis_id`, `nopol`, `foto`, `nama_pemilik`, `alamat`, `merk`, `tahun_pembuatan`, `tahun_perakitan`, `isi_silinder`, `warna`, `no_rangka`, `no_mesin`, `no_bpkb`, `warna_tnkb`, `bahan_bakar`, `kode_lokasi`, `no_urut_pendaftaran`, `harga_sewa_per_hari`, `harga_sewa_per_jam`, `batas_biaya`, `dokumen`, `masa_berlaku`, `kilometer_sekarang`, `limit_km_service`, `limit_bulan_service`, `km_terakhir_service`, `tanggal_terakhir_service`, `status_service`, `status_kendaraan`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 1, 'AA 1011 BE', 'kendaraan/foto/1783958587_Screenshot_7-7-2026_142125_www.instagram.com.jpeg', 'Pemilik Kendaraan 1', 'Wonosobo', 'Toyota Avanza', '2023', '2023', '1000 CC', 'Hitam', 'NRFC5E3E437C60E', 'NMEAA566D8', 'BPKB000001', 'Hitam', 'Pertalite', 'AA', '001234', 510000, 53000, 966000, NULL, NULL, 7513, 5000, 100000000, 0, NULL, 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-13 09:03:07'),
	(2, 1, NULL, 2, 'AB 1022 CF', NULL, 'Pemilik Kendaraan 2', 'Magelang', 'Toyota Innova', '2019', '2019', '1000 CC', 'Putih', 'NR347D8BAA75F0E', 'NMA267766B', 'BPKB000002', 'Hitam', 'Pertamax', 'AB', '002468', 313000, 48000, 849000, NULL, '2026-04-12', 99437, 5000, 6, 97465, '2025-08-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(3, 1, NULL, 3, 'AD 1033 DG', NULL, 'Pemilik Kendaraan 3', 'Purworejo', 'Toyota Rush', '2017', '2017', '1000 CC', 'Silver', 'NR05BCAB37A5B5E', 'NM7DA12124', 'BPKB000003', 'Hitam', 'Solar', 'AD', '003702', 299000, 47000, 1231000, NULL, '2028-01-12', 21519, 5000, 6, 17751, '2026-02-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(4, 1, NULL, 1, 'AE 1044 EH', NULL, 'Pemilik Kendaraan 4', 'Kebumen', 'Toyota Fortuner', '2016', '2016', '500 CC', 'Merah', 'NRD9F1155CE77F7', 'NM96142E2A', 'BPKB000004', 'Hitam', 'Pertamax Turbo', 'AE', '004936', 437000, 43000, 1578000, NULL, '2028-07-12', 56523, 5000, 6, 53073, '2025-09-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(5, 1, NULL, 2, 'AG 1055 FI', NULL, 'Pemilik Kendaraan 5', 'Purwokerto', 'Toyota Calya', '2019', '2019', '1000 CC', 'Biru', 'NRC2DBC1270E648', 'NMCABF6A7D', 'BPKB000005', 'Hitam', 'Pertalite', 'AG', '006170', 295000, 67000, 592000, NULL, '2027-02-12', 31411, 5000, 6, 27367, '2026-02-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(6, 1, NULL, 3, 'AA 1066 GJ', NULL, 'Pemilik Kendaraan 6', 'Temanggung', 'Honda Brio', '2016', '2016', '1500 CC', 'Abu-abu', 'NRD5527EB4A669D', 'NM787F80E0', 'BPKB000006', 'Hitam', 'Pertamax', 'AA', '007404', 241000, 40000, 703000, NULL, '2027-11-12', 30041, 5000, 6, 27143, '2025-07-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(7, 1, NULL, 1, 'AB 1077 HK', NULL, 'Pemilik Kendaraan 7', 'Kendal', 'Honda Mobilio', '2015', '2015', '1000 CC', 'Coklat', 'NRF6B081BFC804D', 'NM238F09D0', 'BPKB000007', 'Hitam', 'Solar', 'AB', '008638', 420000, 70000, 964000, NULL, '2027-05-12', 53703, 5000, 6, 50319, '2025-12-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(8, 1, NULL, 2, 'AD 1088 IL', NULL, 'Pemilik Kendaraan 8', 'Batang', 'Honda HR-V', '2020', '2020', '1500 CC', 'Kuning', 'NRC71D2E264F12E', 'NMB3E50BB9', 'BPKB000008', 'Hitam', 'Pertamax Turbo', 'AD', '009872', 207000, 55000, 1313000, NULL, '2026-05-12', 92176, 5000, 6, 87908, '2025-09-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(9, 1, NULL, 3, 'AE 1099 JM', NULL, 'Pemilik Kendaraan 9', 'Wonosobo', 'Honda CR-V', '2015', '2015', '500 CC', 'Hitam', 'NR28CB98ADDC49A', 'NM95D7A4AD', 'BPKB000009', 'Hitam', 'Pertalite', 'AE', '011106', 352000, 37000, 1803000, NULL, '2026-11-12', 77156, 5000, 6, 76098, '2026-01-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(10, 1, NULL, 1, 'AG 1110 KN', NULL, 'Pemilik Kendaraan 10', 'Magelang', 'Honda Jazz', '2015', '2015', '1500 CC', 'Putih', 'NRC7018438D4E08', 'NMA21DC861', 'BPKB000010', 'Hitam', 'Pertamax', 'AG', '012340', 500000, 76000, 1958000, NULL, '2027-08-12', 24168, 5000, 6, 19618, '2026-02-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(11, 1, NULL, 2, 'AA 1121 LO', NULL, 'Pemilik Kendaraan 11', 'Purworejo', 'Mitsubishi Xpander', '2016', '2016', '1000 CC', 'Silver', 'NR101ED65635098', 'NMB836A727', 'BPKB000011', 'Hitam', 'Solar', 'AA', '013574', 349000, 32000, 715000, NULL, '2027-06-12', 98262, 5000, 6, 92088, '2026-01-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(12, 1, NULL, 3, 'AB 1132 MP', NULL, 'Pemilik Kendaraan 12', 'Kebumen', 'Mitsubishi Pajero', '2016', '2016', '500 CC', 'Merah', 'NRBA899300E453F', 'NMFCA534D3', 'BPKB000012', 'Hitam', 'Pertamax Turbo', 'AB', '014808', 275000, 43000, 1109000, NULL, '2028-04-12', 49983, 5000, 6, 46807, '2025-12-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(13, 1, NULL, 1, 'AD 1143 NQ', NULL, 'Pemilik Kendaraan 13', 'Purwokerto', 'Mitsubishi L300', '2022', '2022', '1500 CC', 'Biru', 'NR28C8D02298AFE', 'NM18E9181C', 'BPKB000013', 'Hitam', 'Pertalite', 'AD', '016042', 523000, 66000, 1347000, NULL, '2028-03-12', 46311, 5000, 6, 41762, '2026-06-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(14, 1, NULL, 2, 'AE 1154 OR', NULL, 'Pemilik Kendaraan 14', 'Temanggung', 'Mitsubishi Outlander', '2016', '2016', '500 CC', 'Abu-abu', 'NR71E1B33FE3385', 'NM65CE77D8', 'BPKB000014', 'Hitam', 'Pertamax', 'AE', '017276', 586000, 36000, 1289000, NULL, '2027-01-12', 18115, 5000, 6, 12122, '2026-01-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(15, 1, NULL, 3, 'AG 1165 PS', NULL, 'Pemilik Kendaraan 15', 'Kendal', 'Daihatsu Xenia', '2016', '2016', '1000 CC', 'Coklat', 'NR0188959EBD4CF', 'NMD99046EE', 'BPKB000015', 'Hitam', 'Solar', 'AG', '018510', 527000, 35000, 1474000, NULL, '2027-11-12', 84625, 5000, 6, 79002, '2025-08-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(16, 1, NULL, 1, 'AA 1176 QT', NULL, 'Pemilik Kendaraan 16', 'Batang', 'Daihatsu Terios', '2022', '2022', '1000 CC', 'Kuning', 'NR4CFC5AB2A1B24', 'NM66FA2F56', 'BPKB000016', 'Hitam', 'Pertamax Turbo', 'AA', '019744', 590000, 78000, 801000, NULL, '2028-03-12', 17050, 5000, 6, 10197, '2026-07-23', 'service', 'tersedia', '2026-07-11 21:07:01', '2026-07-13 08:15:59'),
	(17, 1, NULL, 2, 'AB 1187 RU', NULL, 'Pemilik Kendaraan 17', 'Wonosobo', 'Daihatsu Sigra', '2022', '2022', '1000 CC', 'Hitam', 'NRB690B147D29AD', 'NMED999685', 'BPKB000017', 'Hitam', 'Pertalite', 'AB', '020978', 381000, 42000, 1807000, NULL, '2027-04-12', 12425, 5000, 6, 6187, '2026-06-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(18, 1, NULL, 3, 'AD 1198 SV', NULL, 'Pemilik Kendaraan 18', 'Magelang', 'Daihatsu Gran Max', '2019', '2019', '1500 CC', 'Putih', 'NR89F8444CB9A7D', 'NM8CD1481B', 'BPKB000018', 'Hitam', 'Pertamax', 'AD', '022212', 269000, 60000, 689000, NULL, '2027-11-12', 21856, 5000, 6, 16848, '2026-07-12', 'service', 'tersedia', '2026-07-11 21:07:01', '2026-07-12 00:22:51'),
	(19, 1, NULL, 1, 'AE 1209 TW', NULL, 'Pemilik Kendaraan 19', 'Purworejo', 'Suzuki Ertiga', '2020', '2020', '1500 CC', 'Silver', 'NR67D11ED0217CF', 'NM0AAE1062', 'BPKB000019', 'Hitam', 'Solar', 'AE', '023446', 302000, 41000, 1135000, NULL, '2028-07-12', 112478, 5000, 6, 110079, '2026-05-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(20, 1, NULL, 2, 'AG 1220 UX', NULL, 'Pemilik Kendaraan 20', 'Kebumen', 'Suzuki APV', '2021', '2021', '500 CC', 'Merah', 'NR4736A85D494F9', 'NM8C8EA669', 'BPKB000020', 'Hitam', 'Pertamax Turbo', 'AG', '024680', 373000, 33000, 859000, NULL, '2027-03-12', 10414, 5000, 6, 6934, '2026-04-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(21, 1, NULL, 3, 'AA 1231 VY', NULL, 'Pemilik Kendaraan 21', 'Purwokerto', 'Suzuki Jimny', '2024', '2024', '500 CC', 'Biru', 'NR6EBC3372CE7C9', 'NMB0B07747', 'BPKB000021', 'Hitam', 'Pertalite', 'AA', '025914', 248000, 73000, 1188000, NULL, '2027-03-12', 60247, 5000, 6, 55715, '2025-10-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(22, 1, NULL, 1, 'AB 1242 WZ', NULL, 'Pemilik Kendaraan 22', 'Temanggung', 'Suzuki Carry', '2017', '2017', '500 CC', 'Abu-abu', 'NR62F120141685C', 'NM6AA28811', 'BPKB000022', 'Hitam', 'Pertamax', 'AB', '027148', 578000, 68000, 522000, NULL, '2026-09-12', 42118, 5000, 6, 38682, '2025-12-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(23, 1, NULL, 2, 'AD 1253 XA', NULL, 'Pemilik Kendaraan 23', 'Kendal', 'Nissan X-Trail', '2016', '2016', '1000 CC', 'Coklat', 'NRC5153735B748E', 'NMAB3A9544', 'BPKB000023', 'Hitam', 'Solar', 'AD', '028382', 499000, 70000, 1373000, NULL, '2026-10-12', 101840, 5000, 6, 97979, '2026-03-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(24, 1, NULL, 3, 'AE 1264 YB', NULL, 'Pemilik Kendaraan 24', 'Batang', 'Nissan Livina', '2019', '2019', '500 CC', 'Kuning', 'NR249973A0D8662', 'NMADFDF0BD', 'BPKB000024', 'Hitam', 'Pertamax Turbo', 'AE', '029616', 482000, 71000, 1432000, NULL, '2027-07-12', 80398, 5000, 6, 72982, '2025-08-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(25, 1, NULL, 1, 'AG 1275 ZC', NULL, 'Pemilik Kendaraan 25', 'Wonosobo', 'Nissan Terra', '2019', '2019', '1500 CC', 'Hitam', 'NR1233A73D59FC1', 'NM3B083C41', 'BPKB000025', 'Hitam', 'Pertalite', 'AG', '030850', 288000, 43000, 825000, NULL, '2026-08-12', 106952, 5000, 6, 104820, '2026-01-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(26, 1, NULL, 2, 'AA 1286 AD', NULL, 'Pemilik Kendaraan 26', 'Magelang', 'Isuzu Panther', '2015', '2015', '1500 CC', 'Putih', 'NR8B7FE74809E8F', 'NM11A757F7', 'BPKB000026', 'Hitam', 'Pertamax', 'AA', '032084', 334000, 49000, 1006000, NULL, '2026-08-12', 37128, 5000, 6, 30187, '2026-04-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(27, 1, NULL, 3, 'AB 1297 BE', NULL, 'Pemilik Kendaraan 27', 'Purworejo', 'Isuzu D-Max', '2020', '2020', '1500 CC', 'Silver', 'NRB559A1B082827', 'NM2BA7C96A', 'BPKB000027', 'Hitam', 'Solar', 'AB', '033318', 584000, 33000, 518000, NULL, '2026-08-12', 57982, 5000, 6, 54949, '2026-06-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(28, 1, NULL, 1, 'AD 1308 CF', NULL, 'Pemilik Kendaraan 28', 'Kebumen', 'Isuzu Elf', '2020', '2020', '1500 CC', 'Merah', 'NR8868E52D15081', 'NMF004F89A', 'BPKB000028', 'Hitam', 'Pertamax Turbo', 'AD', '034552', 277000, 57000, 749000, NULL, '2028-04-12', 108242, 5000, 6, 105861, '2026-07-12', 'service', 'tersedia', '2026-07-11 21:07:01', '2026-07-13 08:23:57'),
	(29, 1, NULL, 2, 'AE 1319 DG', NULL, 'Pemilik Kendaraan 29', 'Purwokerto', 'Wuling Almaz', '2019', '2019', '500 CC', 'Biru', 'NR9B965F12D9041', 'NM09AD4CEA', 'BPKB000029', 'Hitam', 'Pertalite', 'AE', '035786', 234000, 50000, 793000, NULL, '2026-09-12', 17416, 5000, 6, 13027, '2025-12-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(30, 1, NULL, 3, 'AG 1330 EH', NULL, 'Pemilik Kendaraan 30', 'Temanggung', 'Wuling Air ev', '2020', '2020', '1500 CC', 'Abu-abu', 'NR5238145D8FA5E', 'NMB7E2C1AB', 'BPKB000030', 'Hitam', 'Pertamax', 'AG', '037020', 278000, 62000, 941000, NULL, '2028-06-12', 54078, 5000, 6, 46173, '2025-12-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(31, 1, NULL, 1, 'AA 1341 FI', NULL, 'Pemilik Kendaraan 31', 'Kendal', 'Toyota Avanza', '2020', '2020', '1000 CC', 'Coklat', 'NR8551649018B27', 'NMC0EF36C6', 'BPKB000031', 'Hitam', 'Solar', 'AA', '038254', 373000, 31000, 573000, NULL, '2026-09-12', 109692, 5000, 6, 108186, '2025-08-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(32, 1, NULL, 2, 'AB 1352 GJ', NULL, 'Pemilik Kendaraan 32', 'Batang', 'Toyota Innova', '2015', '2015', '500 CC', 'Kuning', 'NR83DC5F76D3D1F', 'NM1A73548C', 'BPKB000032', 'Hitam', 'Pertamax Turbo', 'AB', '039488', 557000, 44000, 1025000, NULL, '2027-11-12', 62302, 5000, 6, 55613, '2025-07-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(33, 1, NULL, 3, 'AD 1363 HK', NULL, 'Pemilik Kendaraan 33', 'Wonosobo', 'Toyota Rush', '2016', '2016', '500 CC', 'Hitam', 'NR7D8A942BB2B06', 'NMD7DE1C30', 'BPKB000033', 'Hitam', 'Pertalite', 'AD', '040722', 587000, 54000, 1423000, NULL, '2026-06-12', 30430, 5000, 6, 25509, '2025-07-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(34, 1, NULL, 1, 'AE 1374 IL', NULL, 'Pemilik Kendaraan 34', 'Magelang', 'Toyota Fortuner', '2019', '2019', '1500 CC', 'Putih', 'NR6BB7CA4622D0E', 'NMBCAF1E4D', 'BPKB000034', 'Hitam', 'Pertamax', 'AE', '041956', 360000, 36000, 1467000, NULL, '2028-01-12', 28532, 5000, 6, 21956, '2026-03-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(35, 1, NULL, 2, 'AG 1385 JM', NULL, 'Pemilik Kendaraan 35', 'Purworejo', 'Toyota Calya', '2015', '2015', '500 CC', 'Silver', 'NR2B41A619C9BF1', 'NM796B7335', 'BPKB000035', 'Hitam', 'Solar', 'AG', '043190', 472000, 51000, 1140000, NULL, '2026-07-12', 21642, 5000, 6, 14559, '2025-12-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(36, 1, NULL, 3, 'AA 1396 KN', NULL, 'Pemilik Kendaraan 36', 'Kebumen', 'Honda Brio', '2024', '2024', '1000 CC', 'Merah', 'NREF70459230C5C', 'NM5B1C5010', 'BPKB000036', 'Hitam', 'Pertamax Turbo', 'AA', '044424', 537000, 35000, 1532000, NULL, '2027-07-12', 7518, 5000, 6, 5102, '2025-08-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(37, 1, NULL, 1, 'AB 1407 LO', NULL, 'Pemilik Kendaraan 37', 'Purwokerto', 'Honda Mobilio', '2020', '2020', '1500 CC', 'Biru', 'NRB4F6268FE8E3C', 'NM66823A28', 'BPKB000037', 'Hitam', 'Pertalite', 'AB', '045658', 292000, 43000, 1526000, NULL, '2028-01-12', 78621, 5000, 6, 76316, '2025-10-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(38, 1, NULL, 2, 'AD 1418 MP', NULL, 'Pemilik Kendaraan 38', 'Temanggung', 'Honda HR-V', '2016', '2016', '500 CC', 'Abu-abu', 'NR09F7566B4E018', 'NM6D3C7CF5', 'BPKB000038', 'Hitam', 'Pertamax', 'AD', '046892', 360000, 47000, 912000, NULL, '2027-07-12', 29252, 5000, 6, 24344, '2025-10-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(39, 1, NULL, 3, 'AE 1429 NQ', NULL, 'Pemilik Kendaraan 39', 'Kendal', 'Honda CR-V', '2019', '2019', '1500 CC', 'Coklat', 'NR2DFC28E9E22E4', 'NMBE1C5CD3', 'BPKB000039', 'Hitam', 'Solar', 'AE', '048126', 574000, 79000, 765000, NULL, '2027-10-12', 68682, 5000, 6, 62641, '2026-03-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(40, 1, NULL, 1, 'AG 1440 OR', NULL, 'Pemilik Kendaraan 40', 'Batang', 'Honda Jazz', '2024', '2024', '500 CC', 'Kuning', 'NRD21A1A31A7898', 'NM21C25BDA', 'BPKB000040', 'Hitam', 'Pertamax Turbo', 'AG', '049360', 295000, 69000, 1169000, NULL, '2027-03-12', 98935, 5000, 6, 94265, '2026-01-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(41, 1, NULL, 2, 'AA 1451 PS', NULL, 'Pemilik Kendaraan 41', 'Wonosobo', 'Mitsubishi Xpander', '2016', '2016', '500 CC', 'Hitam', 'NR5BB2C9B97AB51', 'NM20FFAE4E', 'BPKB000041', 'Hitam', 'Pertalite', 'AA', '050594', 295000, 58000, 723000, NULL, '2027-02-12', 18471, 5000, 6, 10963, '2026-01-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(42, 1, NULL, 3, 'AB 1462 QT', NULL, 'Pemilik Kendaraan 42', 'Magelang', 'Mitsubishi Pajero', '2016', '2016', '1000 CC', 'Putih', 'NR4DB76BF66AFB5', 'NM68D57762', 'BPKB000042', 'Hitam', 'Pertamax', 'AB', '051828', 263000, 72000, 562000, NULL, '2028-07-12', 12439, 5000, 6, 8288, '2026-06-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(43, 1, NULL, 1, 'AD 1473 RU', NULL, 'Pemilik Kendaraan 43', 'Purworejo', 'Mitsubishi L300', '2017', '2017', '500 CC', 'Silver', 'NR0DC971245341E', 'NMBA4C4814', 'BPKB000043', 'Hitam', 'Solar', 'AD', '053062', 479000, 62000, 563000, NULL, '2028-01-12', 85062, 5000, 6, 78740, '2025-07-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(44, 1, NULL, 2, 'AE 1484 SV', NULL, 'Pemilik Kendaraan 44', 'Kebumen', 'Mitsubishi Outlander', '2024', '2024', '1500 CC', 'Merah', 'NR476664C2FBE1E', 'NMB8004991', 'BPKB000044', 'Hitam', 'Pertamax Turbo', 'AE', '054296', 266000, 66000, 834000, NULL, '2028-07-12', 24678, 5000, 6, 17074, '2026-05-12', 'service', 'bermasalah', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(45, 1, NULL, 3, 'AG 1495 TW', NULL, 'Pemilik Kendaraan 45', 'Purwokerto', 'Daihatsu Xenia', '2023', '2023', '1000 CC', 'Biru', 'NR7627556EB210F', 'NMF58C9AE1', 'BPKB000045', 'Hitam', 'Pertalite', 'AG', '055530', 245000, 49000, 1149000, NULL, '2026-04-12', 45508, 5000, 6, 38745, '2026-04-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(46, 1, NULL, 1, 'AA 1506 UX', NULL, 'Pemilik Kendaraan 46', 'Temanggung', 'Daihatsu Terios', '2015', '2015', '1000 CC', 'Abu-abu', 'NR10E297C43FFA9', 'NM06A94CA6', 'BPKB000046', 'Hitam', 'Pertamax', 'AA', '056764', 463000, 77000, 1467000, NULL, '2027-03-12', 27784, 5000, 6, 21113, '2025-11-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(47, 1, NULL, 2, 'AB 1517 VY', NULL, 'Pemilik Kendaraan 47', 'Kendal', 'Daihatsu Sigra', '2016', '2016', '1000 CC', 'Coklat', 'NRF3E8AC22132E1', 'NM16A92731', 'BPKB000047', 'Hitam', 'Solar', 'AB', '057998', 594000, 32000, 1415000, NULL, '2028-03-12', 72111, 5000, 6, 68738, '2026-02-12', 'aman', 'service', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(48, 1, NULL, 3, 'AD 1528 WZ', NULL, 'Pemilik Kendaraan 48', 'Batang', 'Daihatsu Gran Max', '2022', '2022', '1500 CC', 'Kuning', 'NR8F3EB52F6AA35', 'NM161F258D', 'BPKB000048', 'Hitam', 'Pertamax Turbo', 'AD', '059232', 224000, 43000, 1512000, NULL, NULL, 16491, 5000, 90000000, 11713, '2026-07-13', 'service', 'tersedia', '2026-07-11 21:07:01', '2026-07-13 09:08:33'),
	(49, 1, NULL, 1, 'AE 1539 XA', NULL, 'Pemilik Kendaraan 49', 'Wonosobo', 'Suzuki Ertiga', '2017', '2017', '1000 CC', 'Hitam', 'NREDD43B2F51720', 'NMDE6CB379', 'BPKB000049', 'Hitam', 'Pertalite', 'AE', '060466', 359000, 53000, 1779000, NULL, '2027-10-12', 80863, 5000, 6, 73101, '2025-09-12', 'aman', 'tersedia', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(50, 1, NULL, 2, 'AG 1550 YB', NULL, 'Pemilik Kendaraan 50', 'Magelang', 'Suzuki APV', '2017', '2017', '1500 CC', 'Putih', 'NREBB917F2E3BB4', 'NM0093B6FC', 'BPKB000050', 'Hitam', 'Pertamax', 'AG', '061700', 202000, 46000, 1256000, NULL, '2026-09-12', 33010, 5000, 6, 25865, '2026-06-12', 'service', 'disewa', '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(51, 1, NULL, 1, 'AA 2859 VF', 'kendaraan/foto/1783839922_cars-banner.png', 'Bima', 'nonono', 'Lambo', '2025', '2021', '2', 'Putih', 'niad', 'ni', 'dsom', NULL, 'dssk', NULL, NULL, 90000, 9000000, 222, 'kendaraan/dokumen/1783839922_icon.png', '2026-07-09', 2, 2, 2, 2, '2026-07-12', 'aman', 'tersedia', '2026-07-12 00:05:22', '2026-07-12 00:05:22');

-- Dumping structure for table apyrent.keuangans
CREATE TABLE IF NOT EXISTS `keuangans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `pemasukan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pengeluaran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sumber` enum('manual','auto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `keuangans_user_id_foreign` (`user_id`),
  CONSTRAINT `keuangans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.keuangans: ~73 rows (approximately)
INSERT INTO `keuangans` (`id`, `tanggal`, `reference`, `user_id`, `kategori`, `metode`, `keterangan`, `pemasukan`, `pengeluaran`, `saldo`, `sumber`, `created_at`, `updated_at`) VALUES
	(1, '2026-03-20', 'INV-001', 1, 'Rental', 'cash', 'Penerimaan Rental ke-1', 417000.00, 0.00, 417000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, '2026-03-03', 'INV-002', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-2', 415000.00, 0.00, 832000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, '2026-02-13', 'EXP-003', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-3', 0.00, 913000.00, 0.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, '2026-05-16', 'INV-004', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-4', 4781000.00, 0.00, 4700000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, '2026-01-21', 'INV-005', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-5', 4133000.00, 0.00, 8833000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(6, '2026-06-10', 'EXP-006', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-6', 0.00, 1412000.00, 7421000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(7, '2026-05-29', 'INV-007', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-7', 714000.00, 0.00, 8135000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(8, '2026-05-24', 'INV-008', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-8', 305000.00, 0.00, 8440000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, '2026-02-07', 'EXP-009', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-9', 0.00, 4174000.00, 4266000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, '2026-05-22', 'INV-010', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-10', 1015000.00, 0.00, 5281000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(11, '2026-02-14', 'INV-011', 1, 'Rental', 'cash', 'Penerimaan Rental ke-11', 4292000.00, 0.00, 9573000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(12, '2026-07-04', 'EXP-012', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-12', 0.00, 273000.00, 9300000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(13, '2026-07-06', 'INV-013', 1, 'Denda', 'cash', 'Penerimaan Denda ke-13', 1360000.00, 0.00, 10660000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(14, '2026-04-24', 'INV-014', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-14', 3943000.00, 0.00, 14603000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(15, '2026-03-07', 'EXP-015', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-15', 0.00, 1444000.00, 13159000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(16, '2026-06-23', 'INV-016', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-16', 1750000.00, 0.00, 14909000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(17, '2026-07-06', 'INV-017', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-17', 1792000.00, 0.00, 16701000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(18, '2026-04-11', 'EXP-018', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-18', 0.00, 3874000.00, 12827000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(19, '2026-06-15', 'INV-019', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-19', 1139000.00, 0.00, 13966000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(20, '2026-03-18', 'INV-020', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-20', 4395000.00, 0.00, 18361000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(21, '2026-04-11', 'EXP-021', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-21', 0.00, 3850000.00, 14511000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(22, '2026-04-30', 'INV-022', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-22', 3089000.00, 0.00, 17600000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(23, '2026-07-02', 'INV-023', 1, 'Denda', 'cash', 'Penerimaan Denda ke-23', 4306000.00, 0.00, 21906000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(24, '2026-05-24', 'EXP-024', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-24', 0.00, 1582000.00, 20324000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(25, '2026-01-22', 'INV-025', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-25', 4175000.00, 0.00, 24499000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(26, '2026-05-11', 'INV-026', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-26', 2690000.00, 0.00, 27189000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(27, '2026-04-25', 'EXP-027', 1, 'Pajak', 'cash', 'Pengeluaran Pajak ke-27', 0.00, 962000.00, 26227000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(28, '2026-06-07', 'INV-028', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-28', 3557000.00, 0.00, 29784000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(29, '2026-02-19', 'INV-029', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-29', 3482000.00, 0.00, 33266000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(30, '2026-02-09', 'EXP-030', 1, 'Gaji', 'transfer', 'Pengeluaran Gaji ke-30', 0.00, 1951000.00, 31315000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(31, '2026-07-10', 'INV-031', 1, 'Rental', 'cash', 'Penerimaan Rental ke-31', 1600000.00, 0.00, 32915000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(32, '2026-05-09', 'INV-032', 1, 'Deposit', 'transfer', 'Penerimaan Deposit ke-32', 2822000.00, 0.00, 35737000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(33, '2026-05-23', 'EXP-033', 1, 'Servis', 'cash', 'Pengeluaran Servis ke-33', 0.00, 1008000.00, 34729000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(34, '2026-04-27', 'INV-034', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-34', 1918000.00, 0.00, 36647000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(35, '2026-02-24', 'INV-035', 1, 'Pelunasan', 'cash', 'Penerimaan Pelunasan ke-35', 735000.00, 0.00, 37382000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(36, '2026-06-21', 'EXP-036', 1, 'Asuransi', 'transfer', 'Pengeluaran Asuransi ke-36', 0.00, 2539000.00, 34843000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(37, '2026-06-09', 'INV-037', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-37', 2800000.00, 0.00, 37643000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(38, '2026-03-04', 'INV-038', 1, 'Denda', 'transfer', 'Penerimaan Denda ke-38', 4154000.00, 0.00, 41797000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(39, '2026-03-05', 'EXP-039', 1, 'Operasional', 'cash', 'Pengeluaran Operasional ke-39', 0.00, 3132000.00, 38665000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(40, '2026-05-13', 'INV-040', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-40', 3424000.00, 0.00, 42089000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(41, '2026-06-23', 'INV-041', 1, 'Rental', 'cash', 'Penerimaan Rental ke-41', 4511000.00, 0.00, 46600000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(42, '2026-06-26', 'EXP-042', 1, 'Bahan Bakar', 'transfer', 'Pengeluaran Bahan Bakar ke-42', 0.00, 2617000.00, 43983000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(43, '2026-05-12', 'INV-043', 1, 'Denda', 'cash', 'Penerimaan Denda ke-43', 2744000.00, 0.00, 46727000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(44, '2026-02-09', 'INV-044', 1, 'Lain-lain', 'transfer', 'Penerimaan Lain-lain ke-44', 1953000.00, 0.00, 48680000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(45, '2026-02-10', 'EXP-045', 1, 'GPS', 'cash', 'Pengeluaran GPS ke-45', 0.00, 1082000.00, 47598000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(46, '2026-06-23', 'INV-046', 1, 'Rental', 'transfer', 'Penerimaan Rental ke-46', 598000.00, 0.00, 48196000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(47, '2026-04-06', 'INV-047', 1, 'Deposit', 'cash', 'Penerimaan Deposit ke-47', 3114000.00, 0.00, 51310000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(48, '2026-06-04', 'EXP-048', 1, 'Spare Part', 'transfer', 'Pengeluaran Spare Part ke-48', 0.00, 3953000.00, 47357000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(49, '2026-02-27', 'INV-049', 1, 'Lain-lain', 'cash', 'Penerimaan Lain-lain ke-49', 471000.00, 0.00, 47828000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(50, '2026-04-24', 'INV-050', 1, 'Pelunasan', 'transfer', 'Penerimaan Pelunasan ke-50', 2391000.00, 0.00, 50219000.00, 'manual', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(51, '2026-07-12', 'SRV-6', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 700999.00, -283999.00, 'manual', '2026-07-11 22:17:45', '2026-07-11 22:17:45'),
	(52, '2026-07-12', 'SRV-7', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 701999.00, -985998.00, 'manual', '2026-07-11 22:30:57', '2026-07-11 22:30:57'),
	(53, '2026-07-12', 'SRV-8', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 90000.00, -1075998.00, 'manual', '2026-07-12 00:22:33', '2026-07-12 00:22:33'),
	(54, '2026-07-12', 'SRV-9', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 2889999.00, -3965997.00, 'manual', '2026-07-13 08:13:03', '2026-07-13 08:13:03'),
	(55, '2026-07-23', 'SRV-10', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 400000.00, -4365997.00, 'manual', '2026-07-13 08:14:15', '2026-07-13 08:14:15'),
	(56, '2026-07-13', 'SRV-11', 1, 'Pengeluaran', 'Cash', 'Service Kendaraan', 0.00, 90000.00, -4455997.00, 'manual', '2026-07-13 09:06:24', '2026-07-13 09:06:24'),
	(57, '2026-07-14', 'PAJAK-7-1784016355', 1, 'Pengeluaran', 'cash', 'Pembayaran pajak kendaraan: Pajak 5 Tahunan - Dalam proses pembayaran', 0.00, 700000.00, -5155997.00, 'manual', '2026-07-14 01:05:55', '2026-07-14 01:05:55'),
	(58, '2026-07-14', 'PAJAK-45-1784016631', 1, 'Pengeluaran', 'cash', 'Pembayaran pajak kendaraan: BBN-KB - Sudah melewati jatuh tempo', 0.00, 3300000.00, -8455997.00, 'manual', '2026-07-14 01:10:31', '2026-07-14 01:10:31'),
	(59, '2026-07-14', 'PAJAK-45-1784016670', 1, 'Pengeluaran', 'cash', 'Pembayaran pajak kendaraan: BBN-KB - Sudah melewati jatuh tempo', 0.00, 3300000.00, -11755997.00, 'manual', '2026-07-14 01:11:10', '2026-07-14 01:11:10'),
	(60, '2026-07-14', 'Asuransi-1-1784016798', 1, 'Pengeluaran', '-', 'Pembayaran asuransi kendaraan: All Risk - ', 0.00, 8000000.00, -19755997.00, 'manual', '2026-07-14 01:13:18', '2026-07-14 01:13:18'),
	(61, '2026-07-14', 'Asuransi-1-1784016825', 1, 'Pengeluaran', '-', 'Pembayaran asuransi kendaraan: All Risk - ', 0.00, 8000000.00, -27755997.00, 'manual', '2026-07-14 01:13:45', '2026-07-14 01:13:45'),
	(62, '2026-07-14', 'GPS-1-1784017126', 1, 'Pengeluaran', '-', 'Perpanjangan GPS kendaraan: OBD - AA 1011 BE', 0.00, 400000.00, -28155997.00, 'manual', '2026-07-14 01:18:46', '2026-07-14 01:18:46'),
	(63, '2026-07-14', 'GPS-1-1784017171', 1, 'Pengeluaran', '-', 'Perpanjangan GPS kendaraan: OBD - AA 1011 BE', 0.00, 400000.00, -28555997.00, 'manual', '2026-07-14 08:19:31', '2026-07-14 08:19:31'),
	(64, '2026-07-14', 'KIR-1-1784017214', 1, 'Pengeluaran', '-', 'Pembayaran KIR kendaraan: AA 1011 BE', 0.00, 500000.00, -29055997.00, 'manual', '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(65, '2026-07-14', 'KIR-2-1784024188', 1, 'Pengeluaran', '-', 'Pembayaran KIR kendaraan: AB 1022 CF', 0.00, 50000000.00, -79055997.00, 'manual', '2026-07-14 10:16:28', '2026-07-14 10:16:28'),
	(66, '2026-07-14', 'PAJAK-7-1784025008', 1, 'Pengeluaran', 'cash', 'Pembayaran pajak kendaraan: Pajak 5 Tahunan - Dalam proses pembayaran', 0.00, 70000000.00, -149055997.00, 'manual', '2026-07-14 10:30:08', '2026-07-14 10:30:08'),
	(67, '2026-07-14', 'PAJAK-47-1784025117', 1, 'Pengeluaran', 'cash', 'Pembayaran pajak kendaraan: Pajak 5 Tahunan - Perlu segera diperpanjang', 0.00, 450000000.00, -599055997.00, 'manual', '2026-07-14 10:31:57', '2026-07-14 10:31:57'),
	(68, '2026-07-14', 'Asuransi-1-1784025163', 1, 'Pengeluaran', '-', 'Pembayaran asuransi kendaraan: All Risk - ', 0.00, 800000000.00, -1399055997.00, 'manual', '2026-07-14 10:32:43', '2026-07-14 10:32:43'),
	(69, '2026-07-14', 'Asuransi-1-1784025257', 1, 'Pengeluaran', '-', 'Pembayaran asuransi kendaraan: All Risk - ', 0.00, 80000000000.00, -81399055997.00, 'manual', '2026-07-14 10:34:17', '2026-07-14 10:34:17'),
	(70, '2026-07-14', 'GPS-1-1784025604', 1, 'Pengeluaran', '-', 'Perpanjangan GPS kendaraan: OBD - AA 1011 BE', 0.00, 400000.00, -81399455997.00, 'manual', '2026-07-14 10:40:04', '2026-07-14 10:40:04'),
	(71, '2026-07-14', 'KIR-1-1784025717', 1, 'Pengeluaran', '-', 'Pembayaran KIR kendaraan: AA 1011 BE', 0.00, 50000000.00, -81449455997.00, 'manual', '2026-07-14 10:41:57', '2026-07-14 10:41:57'),
	(72, '2026-07-14', 'Asuransi-1-1784026462', 1, 'Pengeluaran', '-', 'Pembayaran asuransi kendaraan: All Risk - ', 0.00, 8000000000000.00, -8081449455997.00, 'manual', '2026-07-14 10:54:22', '2026-07-14 10:54:22'),
	(73, '2026-07-14', 'Asuransi-2-1784026552', 1, 'Pengeluaran', '-', 'Pembayaran asuransi kendaraan: TLO - ', 0.00, 1250000000.00, -8082699455997.00, 'manual', '2026-07-14 10:55:52', '2026-07-14 10:55:52'),
	(74, '2026-07-14', 'Asuransi-3-1784028566', 1, 'Pengeluaran', '-', 'Pembayaran asuransi kendaraan: Comprehensive - ', 0.00, 5000000.00, -8082704455997.00, 'manual', '2026-07-14 11:29:26', '2026-07-14 11:29:26');

-- Dumping structure for table apyrent.kir
CREATE TABLE IF NOT EXISTS `kir` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `no_uji` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tanggal_bayar` date DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kir_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `kir_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.kir: ~50 rows (approximately)
INSERT INTO `kir` (`id`, `kendaraan_id`, `no_uji`, `masa_berlaku`, `biaya`, `tanggal_bayar`, `image`, `created_at`, `updated_at`) VALUES
	(1, 1, 'KIR-2026-001', '2030-01-12', 50000000.00, '2026-12-14', 'kir/dokumen/1784025717_fbs.pdf', '2026-07-11 21:07:02', '2026-07-14 10:41:57'),
	(2, 2, 'KIR-2026-002', '2028-03-26', 50000000.00, '2026-12-14', 'kir/dokumen/1784024188_Invoice-INV-202607-0002 (1).pdf', '2026-07-11 21:07:02', '2026-07-14 10:16:28'),
	(3, 3, 'KIR-2026-003', '2026-12-20', 500000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 4, 'KIR-2026-004', '2026-08-24', 450000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 5, 'KIR-2026-005', '2026-08-21', 350000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(6, 6, 'KIR-2026-006', '2027-09-19', 200000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(7, 7, 'KIR-2026-007', '2027-09-04', 350000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(8, 8, 'KIR-2026-008', '2026-05-22', 300000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, 9, 'KIR-2026-009', '2028-05-12', 100000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, 10, 'KIR-2026-010', '2027-05-03', 300000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(11, 11, 'KIR-2026-011', '2027-02-15', 100000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(12, 12, 'KIR-2026-012', '2028-04-04', 250000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(13, 13, 'KIR-2026-013', '2027-07-18', 100000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(14, 14, 'KIR-2026-014', '2027-12-16', 350000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(15, 15, 'KIR-2026-015', '2027-07-05', 150000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(16, 16, 'KIR-2026-016', '2028-02-18', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(17, 17, 'KIR-2026-017', '2027-06-29', 200000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(18, 18, 'KIR-2026-018', '2026-05-15', 100000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(19, 19, 'KIR-2026-019', '2026-07-07', 350000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(20, 20, 'KIR-2026-020', '2026-07-03', 350000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(21, 21, 'KIR-2026-021', '2027-07-21', 500000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(22, 22, 'KIR-2026-022', '2027-06-12', 150000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(23, 23, 'KIR-2026-023', '2028-02-24', 500000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(24, 24, 'KIR-2026-024', '2027-07-18', 150000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(25, 25, 'KIR-2026-025', '2027-05-20', 400000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(26, 26, 'KIR-2026-026', '2027-06-15', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(27, 27, 'KIR-2026-027', '2026-08-26', 100000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(28, 28, 'KIR-2026-028', '2027-01-17', 500000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(29, 29, 'KIR-2026-029', '2026-08-29', 100000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(30, 30, 'KIR-2026-030', '2027-01-25', 250000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(31, 31, 'KIR-2026-031', '2028-01-09', 300000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(32, 32, 'KIR-2026-032', '2027-07-02', 300000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(33, 33, 'KIR-2026-033', '2026-06-25', 200000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(34, 34, 'KIR-2026-034', '2027-02-18', 200000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(35, 35, 'KIR-2026-035', '2026-05-29', 450000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(36, 36, 'KIR-2026-036', '2027-01-26', 250000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(37, 37, 'KIR-2026-037', '2026-05-31', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(38, 38, 'KIR-2026-038', '2027-08-23', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(39, 39, 'KIR-2026-039', '2027-03-10', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(40, 40, 'KIR-2026-040', '2026-12-28', 500000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(41, 41, 'KIR-2026-041', '2028-01-14', 300000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(42, 42, 'KIR-2026-042', '2026-09-09', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(43, 43, 'KIR-2026-043', '2027-10-15', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(44, 44, 'KIR-2026-044', '2028-02-25', 100000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(45, 45, 'KIR-2026-045', '2027-04-13', 300000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(46, 46, 'KIR-2026-046', '2028-03-27', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(47, 47, 'KIR-2026-047', '2028-05-28', 400000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(48, 48, 'KIR-2026-048', '2027-12-15', 450000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(49, 49, 'KIR-2026-049', '2026-10-26', 150000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(50, 50, 'KIR-2026-050', '2027-01-03', 50000.00, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.kir_history
CREATE TABLE IF NOT EXISTS `kir_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kir_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `no_uji` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kir_history_kir_id_foreign` (`kir_id`),
  KEY `kir_history_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `kir_history_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kir_history_kir_id_foreign` FOREIGN KEY (`kir_id`) REFERENCES `kir` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.kir_history: ~2 rows (approximately)
INSERT INTO `kir_history` (`id`, `kir_id`, `kendaraan_id`, `no_uji`, `masa_berlaku`, `biaya`, `image`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'KIR-2026-001', '2029-01-12', 500000.00, 'kir/dokumen/1784017214_Invoice-INV-202607-0002 (2).pdf', '2026-07-14 08:20:14', '2026-07-14 08:20:14', '2026-07-14 08:20:14'),
	(2, 2, 2, 'KIR-2026-002', '2028-03-26', 50000000.00, 'kir/dokumen/1784024188_Invoice-INV-202607-0002 (1).pdf', '2026-07-14 10:16:28', '2026-07-14 10:16:28', '2026-07-14 10:16:28'),
	(3, 1, 1, 'KIR-2026-001', '2030-01-12', 50000000.00, 'kir/dokumen/1784025717_fbs.pdf', '2026-07-14 10:41:57', '2026-07-14 10:41:57', '2026-07-14 10:41:57');

-- Dumping structure for table apyrent.komisi_sales
CREATE TABLE IF NOT EXISTS `komisi_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_sales` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_penjualan` decimal(15,2) NOT NULL,
  `persen_komisi` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_komisi` decimal(15,2) NOT NULL,
  `status_bayar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Dibayar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.komisi_sales: ~12 rows (approximately)
INSERT INTO `komisi_sales` (`id`, `nama_sales`, `bulan`, `total_penjualan`, `persen_komisi`, `total_komisi`, `status_bayar`, `created_at`, `updated_at`) VALUES
	(1, 'Andi', '2026-01', 45000000.00, 3.00, 1350000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'Budi', '2026-01', 38000000.00, 3.00, 1140000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'Cici', '2026-01', 52000000.00, 3.50, 1820000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'Dani', '2026-01', 29000000.00, 3.00, 870000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'Andi', '2026-02', 55000000.00, 3.50, 1925000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'Budi', '2026-02', 42000000.00, 3.00, 1260000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'Cici', '2026-02', 60000000.00, 4.00, 2400000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'Dani', '2026-02', 35000000.00, 3.00, 1050000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'Andi', '2026-03', 48000000.00, 3.00, 1440000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'Budi', '2026-03', 51000000.00, 3.50, 1785000.00, 'Sudah Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(11, 'Cici', '2026-03', 44000000.00, 3.00, 1320000.00, 'Belum Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(12, 'Dani', '2026-03', 39000000.00, 3.00, 1170000.00, 'Belum Dibayar', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.kontrak_aktifs
CREATE TABLE IF NOT EXISTS `kontrak_aktifs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_kontrak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mitra` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` bigint unsigned NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perpanjangan` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.kontrak_aktifs: ~0 rows (approximately)

-- Dumping structure for table apyrent.kpi_appraisals
CREATE TABLE IF NOT EXISTS `kpi_appraisals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode_evaluasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disiplin` int NOT NULL,
  `kolaborasi` int NOT NULL,
  `produktivitas` int NOT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL,
  `evaluator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.kpi_appraisals: ~90 rows (approximately)
INSERT INTO `kpi_appraisals` (`id`, `nama_pegawai`, `periode_evaluasi`, `disiplin`, `kolaborasi`, `produktivitas`, `nilai_akhir`, `evaluator`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'Rini Apriani', 'Q1 2025', 72, 98, 73, 81.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(2, 'Rini Apriani', 'Q2 2025', 73, 86, 99, 86.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(3, 'Rini Apriani', 'Q3 2025', 100, 81, 90, 90.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(4, 'Rini Apriani', 'Q4 2025', 70, 78, 84, 77.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(5, 'Rini Apriani', 'Q1 2026', 71, 91, 79, 80.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(6, 'Rini Apriani', 'Q2 2026', 91, 99, 71, 87.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(7, 'Eko Prasetyo', 'Q1 2025', 84, 97, 92, 91.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(8, 'Eko Prasetyo', 'Q2 2025', 77, 99, 72, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(9, 'Eko Prasetyo', 'Q3 2025', 94, 77, 100, 90.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(10, 'Eko Prasetyo', 'Q4 2025', 89, 86, 94, 89.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(11, 'Eko Prasetyo', 'Q1 2026', 82, 73, 99, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(12, 'Eko Prasetyo', 'Q2 2026', 77, 91, 73, 80.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(13, 'Rizky Fadillah', 'Q1 2025', 72, 74, 84, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(14, 'Rizky Fadillah', 'Q2 2025', 76, 67, 75, 72.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(15, 'Rizky Fadillah', 'Q3 2025', 82, 66, 71, 73.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(16, 'Rizky Fadillah', 'Q4 2025', 65, 91, 86, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(17, 'Rizky Fadillah', 'Q1 2026', 84, 72, 91, 82.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(18, 'Rizky Fadillah', 'Q2 2026', 92, 94, 74, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(19, 'Yusuf Hidayat', 'Q1 2025', 90, 68, 69, 75.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(20, 'Yusuf Hidayat', 'Q2 2025', 66, 94, 86, 82.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(21, 'Yusuf Hidayat', 'Q3 2025', 77, 75, 78, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(22, 'Yusuf Hidayat', 'Q4 2025', 97, 100, 84, 93.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(23, 'Yusuf Hidayat', 'Q1 2026', 70, 87, 96, 84.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(24, 'Yusuf Hidayat', 'Q2 2026', 67, 80, 97, 81.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(25, 'Wahyu Nugroho', 'Q1 2025', 94, 81, 85, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(26, 'Wahyu Nugroho', 'Q2 2025', 99, 89, 99, 95.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(27, 'Wahyu Nugroho', 'Q3 2025', 93, 100, 75, 89.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(28, 'Wahyu Nugroho', 'Q4 2025', 90, 75, 87, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(29, 'Wahyu Nugroho', 'Q1 2026', 95, 95, 86, 92.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(30, 'Wahyu Nugroho', 'Q2 2026', 99, 68, 99, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(31, 'Fitri Handayani', 'Q1 2025', 81, 82, 100, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(32, 'Fitri Handayani', 'Q2 2025', 72, 91, 86, 83.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(33, 'Fitri Handayani', 'Q3 2025', 89, 82, 97, 89.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(34, 'Fitri Handayani', 'Q4 2025', 93, 82, 74, 83.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(35, 'Fitri Handayani', 'Q1 2026', 89, 85, 98, 90.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(36, 'Fitri Handayani', 'Q2 2026', 88, 94, 88, 90.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(37, 'Teguh Santosa', 'Q1 2025', 78, 90, 68, 78.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(38, 'Teguh Santosa', 'Q2 2025', 96, 89, 68, 84.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(39, 'Teguh Santosa', 'Q3 2025', 65, 75, 65, 68.33, 'Dewi Kusuma', 'Perlu pembinaan dan evaluasi lanjutan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(40, 'Teguh Santosa', 'Q4 2025', 85, 79, 76, 80.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(41, 'Teguh Santosa', 'Q1 2026', 76, 79, 82, 79.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(42, 'Teguh Santosa', 'Q2 2026', 82, 96, 90, 89.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(43, 'Arif Budiman', 'Q1 2025', 67, 68, 87, 74.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(44, 'Arif Budiman', 'Q2 2025', 74, 65, 70, 69.67, 'Dewi Kusuma', 'Perlu pembinaan dan evaluasi lanjutan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(45, 'Arif Budiman', 'Q3 2025', 66, 89, 76, 77.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(46, 'Arif Budiman', 'Q4 2025', 79, 73, 84, 78.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(47, 'Arif Budiman', 'Q1 2026', 96, 91, 78, 88.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(48, 'Arif Budiman', 'Q2 2026', 91, 96, 65, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(49, 'Dewi Kusuma', 'Q1 2025', 99, 67, 89, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(50, 'Dewi Kusuma', 'Q2 2025', 65, 66, 95, 75.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(51, 'Dewi Kusuma', 'Q3 2025', 99, 81, 71, 83.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(52, 'Dewi Kusuma', 'Q4 2025', 75, 80, 83, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(53, 'Dewi Kusuma', 'Q1 2026', 94, 87, 71, 84.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(54, 'Dewi Kusuma', 'Q2 2026', 100, 74, 86, 86.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(55, 'Linda Permata', 'Q1 2025', 80, 82, 66, 76.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(56, 'Linda Permata', 'Q2 2025', 84, 84, 86, 84.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(57, 'Linda Permata', 'Q3 2025', 83, 67, 79, 76.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(58, 'Linda Permata', 'Q4 2025', 98, 85, 80, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(59, 'Linda Permata', 'Q1 2026', 71, 100, 70, 80.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(60, 'Linda Permata', 'Q2 2026', 70, 86, 83, 79.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(61, 'Hendra Gunawan', 'Q1 2025', 91, 84, 70, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(62, 'Hendra Gunawan', 'Q2 2025', 79, 77, 86, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(63, 'Hendra Gunawan', 'Q3 2025', 82, 88, 66, 78.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(64, 'Hendra Gunawan', 'Q4 2025', 85, 92, 86, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(65, 'Hendra Gunawan', 'Q1 2026', 84, 85, 73, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(66, 'Hendra Gunawan', 'Q2 2026', 85, 82, 95, 87.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(67, 'Dody Kurniawan', 'Q1 2025', 86, 86, 86, 86.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(68, 'Dody Kurniawan', 'Q2 2025', 74, 81, 69, 74.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(69, 'Dody Kurniawan', 'Q3 2025', 87, 67, 88, 80.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(70, 'Dody Kurniawan', 'Q4 2025', 97, 89, 69, 85.00, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(71, 'Dody Kurniawan', 'Q1 2026', 76, 69, 73, 72.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(72, 'Dody Kurniawan', 'Q2 2026', 74, 95, 76, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(73, 'Siti Rahayu', 'Q1 2025', 92, 80, 97, 89.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(74, 'Siti Rahayu', 'Q2 2025', 66, 86, 85, 79.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(75, 'Siti Rahayu', 'Q3 2025', 75, 76, 92, 81.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(76, 'Siti Rahayu', 'Q4 2025', 99, 74, 99, 90.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(77, 'Siti Rahayu', 'Q1 2026', 91, 71, 86, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(78, 'Siti Rahayu', 'Q2 2026', 98, 69, 96, 87.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(79, 'Agus Wibowo', 'Q1 2025', 69, 88, 88, 81.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(80, 'Agus Wibowo', 'Q2 2025', 70, 94, 71, 78.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(81, 'Agus Wibowo', 'Q3 2025', 83, 75, 75, 77.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(82, 'Agus Wibowo', 'Q4 2025', 92, 77, 97, 88.67, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(83, 'Agus Wibowo', 'Q1 2026', 88, 78, 99, 88.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(84, 'Agus Wibowo', 'Q2 2026', 73, 71, 86, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(85, 'Budi Santoso', 'Q1 2025', 68, 82, 80, 76.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(86, 'Budi Santoso', 'Q2 2025', 75, 78, 85, 79.33, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(87, 'Budi Santoso', 'Q3 2025', 85, 76, 70, 77.00, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(88, 'Budi Santoso', 'Q4 2025', 96, 74, 78, 82.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(89, 'Budi Santoso', 'Q1 2026', 91, 79, 92, 87.33, 'Dewi Kusuma', 'Performa sangat baik, pertahankan.', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(90, 'Budi Santoso', 'Q2 2026', 69, 68, 96, 77.67, 'Dewi Kusuma', 'Performa cukup, perlu peningkatan di beberapa aspek.', '2026-07-11 21:07:07', '2026-07-11 21:07:07');

-- Dumping structure for table apyrent.laporan_keuangan
CREATE TABLE IF NOT EXISTS `laporan_keuangan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pendapatan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `beban` decimal(15,2) NOT NULL DEFAULT '0.00',
  `laba` decimal(15,2) NOT NULL DEFAULT '0.00',
  `periode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.laporan_keuangan: ~3 rows (approximately)
INSERT INTO `laporan_keuangan` (`id`, `nama_perusahaan`, `pendapatan`, `beban`, `laba`, `periode`, `created_at`, `updated_at`) VALUES
	(1, 'APY Rental', 25000000.00, 12000000.00, 13000000.00, '2026-07', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'APY Rental', 30000000.00, 15000000.00, 15000000.00, '2026-06', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'APY Rental', 18000000.00, 9000000.00, 9000000.00, '2026-05', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.legal_documents
CREATE TABLE IF NOT EXISTS `legal_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pihak_terkait` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_terbit` date NOT NULL,
  `berlaku_hingga` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `legal_documents_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.legal_documents: ~0 rows (approximately)

-- Dumping structure for table apyrent.litigasis
CREATE TABLE IF NOT EXISTS `litigasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kasus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lawan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kasus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengacara` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_sidang` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.litigasis: ~0 rows (approximately)

-- Dumping structure for table apyrent.loyalties
CREATE TABLE IF NOT EXISTS `loyalties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_reward` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `akumulasi_poin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `konversi_poin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loyalties_id_program_unique` (`id_program`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.loyalties: ~2 rows (approximately)
INSERT INTO `loyalties` (`id`, `id_program`, `nama_program`, `jenis_reward`, `akumulasi_poin`, `konversi_poin`, `periode_mulai`, `periode_akhir`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'LYL001', 'APY Points', 'Poin', '1 poin per 10.000', '100 poin = Rp 50.000', '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'LYL002', 'Free Day Program', 'Hari Gratis', '10 hari rental = 1 hari gratis', '1 hari gratis per periode', '2026-07-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.member
CREATE TABLE IF NOT EXISTS `member` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_pelanggan` enum('perorangan','perusahaan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.member: ~50 rows (approximately)
INSERT INTO `member` (`id`, `nama_pelanggan`, `kontak_pelanggan`, `email_pelanggan`, `jenis_pelanggan`, `alamat`, `created_at`, `updated_at`) VALUES
	(1, 'Budi Santoso', '08983483649', 'budi.santoso@gmail.com', 'perorangan', 'Jl. Wonosobo No. 96', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 'Joko Widodo', '08173820691', 'joko.widodo@gmail.com', 'perorangan', 'Jl. Magelang No. 47', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 'Andi Saputra', '08133868829', 'andi.saputra@gmail.com', 'perorangan', 'Jl. Purworejo No. 50', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 'Rizky Pratama', '08289745115', 'rizky.pratama@gmail.com', 'perorangan', 'Jl. Kebumen No. 99', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 'Dian Permata', '08748244530', 'dian.permata@gmail.com', 'perorangan', 'Jl. Purwokerto No. 51', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(6, 'Siti Rahayu', '08123995298', 'siti.rahayu@gmail.com', 'perorangan', 'Jl. Temanggung No. 16', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(7, 'Ahmad Fauzi', '08471175341', 'ahmad.fauzi@gmail.com', 'perorangan', 'Jl. Kendal No. 94', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(8, 'Dewi Lestari', '08448333234', 'dewi.lestari@gmail.com', 'perorangan', 'Jl. Semarang No. 51', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, 'Hendra Gunawan', '08349323238', 'hendra.gunawan@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 62', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, 'Rina Wati', '08229635503', 'rina.wati@gmail.com', 'perorangan', 'Jl. Solo No. 93', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(11, 'Bambang Sutrisno', '08492418678', 'bambang.sutrisno@gmail.com', 'perorangan', 'Jl. Wonosobo No. 91', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(12, 'Nia Ramadhani', '08467283211', 'nia.ramadhani@gmail.com', 'perorangan', 'Jl. Magelang No. 48', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(13, 'Ferdy Sambo', '08660103553', 'ferdy.sambo@gmail.com', 'perorangan', 'Jl. Purworejo No. 13', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(14, 'Lina Marlina', '08495888163', 'lina.marlina@gmail.com', 'perorangan', 'Jl. Kebumen No. 86', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(15, 'Tono Suprapto', '08268526839', 'tono.suprapto@gmail.com', 'perorangan', 'Jl. Purwokerto No. 51', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(16, 'Yuli Astuti', '08843287866', 'yuli.astuti@gmail.com', 'perorangan', 'Jl. Temanggung No. 24', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(17, 'Fajar Nugroho', '08230633700', 'fajar.nugroho@gmail.com', 'perorangan', 'Jl. Kendal No. 36', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(18, 'Sri Wahyuni', '08269921042', 'sri.wahyuni@gmail.com', 'perorangan', 'Jl. Semarang No. 50', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(19, 'Rudi Hartono', '08391018449', 'rudi.hartono@gmail.com', 'perorangan', 'Jl. Yogyakarta No. 39', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(20, 'Mega Putri', '08134655456', 'mega.putri@gmail.com', 'perorangan', 'Jl. Solo No. 98', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(21, 'Wahyu Setiawan', '08128248646', 'wahyu.setiawan@gmail.com', 'perorangan', 'Jl. Wonosobo No. 84', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(22, 'Indah Kurniasih', '08276727187', 'indah.kurniasih@gmail.com', 'perorangan', 'Jl. Magelang No. 89', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(23, 'Eko Prasetyo', '08859325136', 'eko.prasetyo@gmail.com', 'perorangan', 'Jl. Purworejo No. 38', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(24, 'Fitri Handayani', '08901078520', 'fitri.handayani@gmail.com', 'perorangan', 'Jl. Kebumen No. 45', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(25, 'Galih Wicaksono', '08775561456', 'galih.wicaksono@gmail.com', 'perorangan', 'Jl. Purwokerto No. 15', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(26, 'PT Maju Bersama', '0249419889', 'ptmajubersama@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 137', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(27, 'CV Sumber Rezeki', '0263660806', 'cvsumberrezeki@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 32', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(28, 'PT Cahaya Abadi', '0292537682', 'ptcahayaabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 150', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(29, 'CV Jaya Mandiri', '0268911940', 'cvjayamandiri@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 14', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(30, 'PT Sukses Selalu', '0226577217', 'ptsuksesselalu@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 23', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(31, 'PT Karya Utama', '0245335473', 'ptkaryautama@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 106', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(32, 'CV Harapan Baru', '0220706241', 'cvharapanbaru@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 153', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(33, 'PT Gemilang Jaya', '0262578631', 'ptgemilangjaya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 36', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(34, 'CV Delta Nusantara', '0264745843', 'cvdeltanusantara@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 98', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(35, 'PT Bintang Timur', '0278820591', 'ptbintangtimur@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 185', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(36, 'PT Nusantara Trans', '0262716286', 'ptnusantaratrans@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 88', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(37, 'CV Permata Hijau', '0252214656', 'cvpermatahijau@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 136', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(38, 'PT Sinar Mas Logistik', '0258732825', 'ptsinarmaslogistik@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 152', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(39, 'CV Berkah Sejati', '0293080271', 'cvberkahsejati@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 61', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(40, 'PT Indo Mitra', '0295157481', 'ptindomitra@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 126', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(41, 'PT Wahana Ekspres', '0261970309', 'ptwahanaekspres@mail.co.id', 'perusahaan', 'Jl. Raya Temanggung No. 132', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(42, 'CV Tirta Agung', '0219097545', 'cvtirtaagung@mail.co.id', 'perusahaan', 'Jl. Raya Kendal No. 123', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(43, 'PT Mandiri Karya', '0262409728', 'ptmandirikarya@mail.co.id', 'perusahaan', 'Jl. Raya Semarang No. 87', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(44, 'CV Perkasa Utama', '0261465738', 'cvperkasautama@mail.co.id', 'perusahaan', 'Jl. Raya Yogyakarta No. 79', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(45, 'PT Cipta Rasa', '0210551494', 'ptciptarasa@mail.co.id', 'perusahaan', 'Jl. Raya Solo No. 134', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(46, 'PT Lancar Jaya', '0256720974', 'ptlancarjaya@mail.co.id', 'perusahaan', 'Jl. Raya Wonosobo No. 71', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(47, 'CV Mitra Usaha', '0256129671', 'cvmitrausaha@mail.co.id', 'perusahaan', 'Jl. Raya Magelang No. 93', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(48, 'PT Sejahtera Abadi', '0262719457', 'ptsejahteraabadi@mail.co.id', 'perusahaan', 'Jl. Raya Purworejo No. 109', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(49, 'CV Putra Bangsa', '0286450626', 'cvputrabangsa@mail.co.id', 'perusahaan', 'Jl. Raya Kebumen No. 155', '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(50, 'PT Global Trans', '0217311181', 'ptglobaltrans@mail.co.id', 'perusahaan', 'Jl. Raya Purwokerto No. 116', '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.members
CREATE TABLE IF NOT EXISTS `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_member` enum('perorangan','perusahaan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'perorangan',
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `file_stnk` text COLLATE utf8mb4_unicode_ci,
  `file_attachment` text COLLATE utf8mb4_unicode_ci,
  `file_kontrak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.members: ~0 rows (approximately)
INSERT INTO `members` (`id`, `nama`, `kontak`, `email`, `jenis_member`, `alamat`, `file_stnk`, `file_attachment`, `file_kontrak`, `created_at`, `updated_at`) VALUES
	(1, 'Ari Bima Prasetya', '085727152082', 'bimasaxti7@gmail.com', 'perorangan', 'Nang kanadddddddddddddddddddddddddddddddddddddddddddddddddddd', '["uploads\\/member\\/1783960523_stnk_6a5513cb7c3bc_Invoice-INV-202607-0002 (1).pdf"]', '["uploads\\/member\\/1783960523_att_6a5513cb7ca0c_Daftar-Penawaran-2026-07-05 (2).pdf"]', 'uploads/member/1783960523_file_kontrak_Summary-2026-07-05 (1).pdf', '2026-07-13 09:35:23', '2026-07-13 10:52:48');

-- Dumping structure for table apyrent.member_kendaraan
CREATE TABLE IF NOT EXISTS `member_kendaraan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `tanggal_sewa` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `biaya_sewa` bigint NOT NULL DEFAULT '0',
  `status_sewa` enum('aktif','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_kendaraan_member_id_foreign` (`member_id`),
  KEY `member_kendaraan_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `member_kendaraan_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_kendaraan_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.member_kendaraan: ~0 rows (approximately)

-- Dumping structure for table apyrent.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.migrations: ~140 rows (approximately)
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
	(138, '2026_07_12_000001_create_reminder_service_table', 2),
	(139, '2026_07_13_145757_add_biaya_to_reminder_service_table', 3),
	(140, '2026_07_14_000001_add_sumber_to_keuangans_table', 4),
	(141, '2026_07_14_000003_change_aging_ars_total_to_decimal', 5),
	(142, '2026_07_14_000002_change_bukubesars_to_decimal', 5),
	(143, '2026_07_14_000004_add_hutang_vendor_id_to_aging_aps_table', 6);

-- Dumping structure for table apyrent.network_monitorings
CREATE TABLE IF NOT EXISTS `network_monitorings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_public` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_koneksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bandwidth` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `downtime` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.network_monitorings: ~5 rows (approximately)
INSERT INTO `network_monitorings` (`id`, `lokasi`, `ip_public`, `status_koneksi`, `bandwidth`, `downtime`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'Kantor Pusat Jakarta', '103.12.45.67', 'Online', '500 Mbps', '0 jam/bulan', 'Koneksi utama Indihome Business', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'Cabang Surabaya', '202.67.88.12', 'Online', '100 Mbps', '2 jam/bulan', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'Gudang Bekasi', '180.244.33.91', 'Warning', '50 Mbps', '5 jam/bulan', 'Sering gangguan sore hari', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'Data Center Cibitung', '103.88.12.200', 'Online', '1 Gbps', '0 jam/bulan', 'Tier 3 data center', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'Cabang Bandung', '36.91.44.111', 'Offline', '100 Mbps', '8 jam/bulan', 'Sedang dalam perbaikan jalur fiber', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.otomatisasis
CREATE TABLE IF NOT EXISTS `otomatisasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_workflow` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `syarat_tambahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delay_aksi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `otomatisasis_workflow_id_unique` (`workflow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.otomatisasis: ~2 rows (approximately)
INSERT INTO `otomatisasis` (`id`, `workflow_id`, `nama_workflow`, `trigger_event`, `syarat_tambahan`, `aksi`, `delay_aksi`, `status`, `pic`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'WF001', 'Welcome Email', 'Registrasi Baru', 'Member baru', 'Kirim Email Selamat Datang', '10 menit', 'Aktif', 'System', 'Auto-email untuk member baru', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'WF002', 'Reminder Pembayaran', 'H-2 Jatuh Tempo', 'Belum bayar', 'Kirim Notifikasi WA', 'Langsung', 'Aktif', 'Finance', 'Pengingat otomatis pembayaran', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.pajak_histories
CREATE TABLE IF NOT EXISTS `pajak_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pajak_kendaraan_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `jenis_pajak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diperpanjang_pada` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pajak_histories_pajak_kendaraan_id_foreign` (`pajak_kendaraan_id`),
  KEY `pajak_histories_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `pajak_histories_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pajak_histories_pajak_kendaraan_id_foreign` FOREIGN KEY (`pajak_kendaraan_id`) REFERENCES `pajak_kendaraans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.pajak_histories: ~4 rows (approximately)
INSERT INTO `pajak_histories` (`id`, `pajak_kendaraan_id`, `kendaraan_id`, `jenis_pajak`, `nominal`, `jatuh_tempo`, `tanggal_bayar`, `status`, `keterangan`, `bukti`, `diperpanjang_pada`, `created_at`, `updated_at`) VALUES
	(1, 7, 7, 'Pajak 5 Tahunan', 700000.00, '2026-11-09', '2026-07-14', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-07-13 17:00:00', '2026-07-14 01:05:55', '2026-07-14 01:05:55'),
	(2, 45, 45, 'BBN-KB', 3300000.00, '2027-07-31', '2026-07-14', 'sudah_bayar', 'Sudah melewati jatuh tempo', 'pajak/bukti/1784016631_6a55eef775dcc.pdf', '2026-07-14 01:10:31', '2026-07-14 01:10:31', '2026-07-14 01:10:31'),
	(3, 45, 45, 'BBN-KB', 3300000.00, '2028-07-31', '2026-07-14', 'sudah_bayar', 'Sudah melewati jatuh tempo', 'pajak/bukti/1784016670_6a55ef1e2b3b1.pdf', '2026-07-14 01:11:10', '2026-07-14 01:11:10', '2026-07-14 01:11:10'),
	(4, 7, 7, 'Pajak 5 Tahunan', 70000000.00, '2028-11-09', '2026-12-14', 'sudah_bayar', 'Dalam proses pembayaran', 'pajak/bukti/1784025008_6a560fb015500.pdf', '2026-07-14 10:30:08', '2026-07-14 10:30:08', '2026-07-14 10:30:08'),
	(5, 47, 47, 'Pajak 5 Tahunan', 450000000.00, '2028-01-26', '2026-12-14', 'sudah_bayar', 'Perlu segera diperpanjang', 'pajak/bukti/1784025117_6a56101d513fb.pdf', '2026-12-13 17:00:00', '2026-07-14 10:31:57', '2026-07-14 10:31:57');

-- Dumping structure for table apyrent.pajak_kendaraans
CREATE TABLE IF NOT EXISTS `pajak_kendaraans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `jenis_pajak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `status` enum('belum_bayar','sudah_bayar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_bayar',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pajak_kendaraans_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `pajak_kendaraans_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.pajak_kendaraans: ~50 rows (approximately)
INSERT INTO `pajak_kendaraans` (`id`, `kendaraan_id`, `jenis_pajak`, `nominal`, `jatuh_tempo`, `tanggal_bayar`, `status`, `keterangan`, `bukti`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Pajak Tahunan', 1200000.00, '2027-03-23', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(2, 2, 'Pajak 5 Tahunan', 3800000.00, '2026-09-23', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(3, 3, 'STNK', 3900000.00, '2027-06-10', '2026-07-07', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(4, 4, 'BPKB', 2500000.00, '2026-12-09', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(5, 5, 'BBN-KB', 3400000.00, '2027-06-01', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(6, 6, 'Pajak Tahunan', 4100000.00, '2027-06-18', '2026-07-10', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-11 21:07:01', '2026-07-11 21:07:01'),
	(7, 7, 'Pajak 5 Tahunan', 70000000.00, '2028-11-09', '2026-12-14', 'sudah_bayar', 'Dalam proses pembayaran', 'pajak/bukti/1784025008_6a560fb015500.pdf', '2026-07-11 21:07:02', '2026-07-14 10:30:08'),
	(8, 8, 'STNK', 2000000.00, '2027-03-29', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, 9, 'BPKB', 2500000.00, '2026-12-08', '2026-07-10', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, 10, 'BBN-KB', 1500000.00, '2027-04-29', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(11, 11, 'Pajak Tahunan', 2700000.00, '2027-06-28', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(12, 12, 'Pajak 5 Tahunan', 1800000.00, '2026-07-10', '2026-06-30', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(13, 13, 'STNK', 5800000.00, '2027-07-08', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(14, 14, 'BPKB', 2500000.00, '2027-04-07', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(15, 15, 'BBN-KB', 1900000.00, '2026-11-18', '2026-06-14', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(16, 16, 'Pajak Tahunan', 4800000.00, '2026-09-07', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(17, 17, 'Pajak 5 Tahunan', 1700000.00, '2026-09-10', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(18, 18, 'STNK', 3800000.00, '2027-05-29', '2026-07-01', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(19, 19, 'BPKB', 3700000.00, '2027-06-24', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(20, 20, 'BBN-KB', 5700000.00, '2026-08-29', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(21, 21, 'Pajak Tahunan', 4100000.00, '2027-03-06', '2026-06-15', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(22, 22, 'Pajak 5 Tahunan', 3300000.00, '2027-01-30', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(23, 23, 'STNK', 2600000.00, '2027-04-25', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(24, 24, 'BPKB', 3200000.00, '2027-01-29', '2026-06-14', 'sudah_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(25, 25, 'BBN-KB', 4400000.00, '2027-05-23', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(26, 26, 'Pajak Tahunan', 4600000.00, '2027-03-21', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(27, 27, 'Pajak 5 Tahunan', 2600000.00, '2026-09-05', '2026-07-05', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(28, 28, 'STNK', 5500000.00, '2026-07-14', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(29, 29, 'BPKB', 4900000.00, '2026-12-26', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(30, 30, 'BBN-KB', 3200000.00, '2027-05-19', '2026-06-13', 'sudah_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(31, 31, 'Pajak Tahunan', 3500000.00, '2026-09-12', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(32, 32, 'Pajak 5 Tahunan', 3600000.00, '2027-06-29', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(33, 33, 'STNK', 700000.00, '2026-12-26', '2026-06-28', 'sudah_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(34, 34, 'BPKB', 1000000.00, '2027-06-24', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(35, 35, 'BBN-KB', 700000.00, '2027-06-09', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(36, 36, 'Pajak Tahunan', 1600000.00, '2026-07-09', '2026-07-11', 'sudah_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(37, 37, 'Pajak 5 Tahunan', 3500000.00, '2027-02-09', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(38, 38, 'STNK', 2500000.00, '2026-10-18', NULL, 'belum_bayar', 'Sudah melewati jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(39, 39, 'BPKB', 5000000.00, '2027-01-01', '2026-06-30', 'sudah_bayar', 'Pembayaran berhasil', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(40, 40, 'BBN-KB', 1600000.00, '2027-04-25', NULL, 'belum_bayar', 'Perlu segera diperpanjang', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(41, 41, 'Pajak Tahunan', 1600000.00, '2026-07-18', NULL, 'belum_bayar', 'Menunggu verifikasi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(42, 42, 'Pajak 5 Tahunan', 1200000.00, '2026-12-03', '2026-07-05', 'sudah_bayar', 'Dalam proses pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(43, 43, 'STNK', 1100000.00, '2026-08-15', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(44, 44, 'BPKB', 1500000.00, '2027-02-23', NULL, 'belum_bayar', 'Segera lakukan pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(45, 45, 'BBN-KB', 3300000.00, '2028-07-31', '2026-07-14', 'sudah_bayar', 'Sudah melewati jatuh tempo', 'pajak/bukti/1784016670_6a55ef1e2b3b1.pdf', '2026-07-11 21:07:02', '2026-07-14 01:11:10'),
	(46, 46, 'Pajak Tahunan', 5000000.00, '2026-10-25', NULL, 'belum_bayar', 'Pembayaran berhasil', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(47, 47, 'Pajak 5 Tahunan', 450000000.00, '2028-01-26', '2026-12-14', 'sudah_bayar', 'Perlu segera diperpanjang', 'pajak/bukti/1784025117_6a56101d513fb.pdf', '2026-07-11 21:07:02', '2026-07-14 10:31:57'),
	(48, 48, 'STNK', 800000.00, '2026-08-18', '2026-06-25', 'sudah_bayar', 'Menunggu verifikasi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(49, 49, 'BPKB', 4300000.00, '2026-10-04', NULL, 'belum_bayar', 'Dalam proses pembayaran', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(50, 50, 'BBN-KB', 4900000.00, '2027-02-13', NULL, 'belum_bayar', 'Pajak hampir jatuh tempo', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table apyrent.payrolls
CREATE TABLE IF NOT EXISTS `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL,
  `tunjangan` decimal(15,2) NOT NULL,
  `thr` decimal(15,2) NOT NULL,
  `bpjs` decimal(15,2) NOT NULL,
  `pph21` decimal(15,2) NOT NULL,
  `total_gaji` decimal(15,2) NOT NULL,
  `slip_gaji` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.payrolls: ~15 rows (approximately)
INSERT INTO `payrolls` (`id`, `nama_pegawai`, `gaji_pokok`, `tunjangan`, `thr`, `bpjs`, `pph21`, `total_gaji`, `slip_gaji`, `created_at`, `updated_at`) VALUES
	(1, 'Budi Santoso', 25000000.00, 5000000.00, 25000000.00, 500000.00, 2500000.00, 27000000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 'Siti Rahayu', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 'Agus Wibowo', 20000000.00, 4000000.00, 20000000.00, 400000.00, 2000000.00, 21600000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 'Dewi Kusuma', 12000000.00, 2000000.00, 12000000.00, 240000.00, 600000.00, 13160000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 'Rini Apriani', 6000000.00, 1000000.00, 6000000.00, 120000.00, 150000.00, 6730000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 'Eko Prasetyo', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 'Hendra Gunawan', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 'Rizky Fadillah', 7000000.00, 1200000.00, 7000000.00, 140000.00, 200000.00, 7860000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 'Yusuf Hidayat', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 'Linda Permata', 13000000.00, 2500000.00, 13000000.00, 260000.00, 750000.00, 14490000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(11, 'Wahyu Nugroho', 7500000.00, 1200000.00, 7500000.00, 150000.00, 220000.00, 8330000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(12, 'Fitri Handayani', 6500000.00, 1000000.00, 6500000.00, 130000.00, 170000.00, 7200000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(13, 'Dody Kurniawan', 11000000.00, 2000000.00, 11000000.00, 220000.00, 550000.00, 12230000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(14, 'Teguh Santosa', 8000000.00, 1500000.00, 8000000.00, 160000.00, 280000.00, 9060000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(15, 'Arif Budiman', 5500000.00, 800000.00, 5500000.00, 110000.00, 120000.00, 6070000.00, NULL, '2026-07-11 21:07:06', '2026-07-11 21:07:06');

-- Dumping structure for table apyrent.pembelian_proyeks
CREATE TABLE IF NOT EXISTS `pembelian_proyeks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pr_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_diminta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimasi_harga` bigint NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_permintaan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembelian_proyeks_pr_no_unique` (`pr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.pembelian_proyeks: ~8 rows (approximately)
INSERT INTO `pembelian_proyeks` (`id`, `pr_no`, `proyek`, `item_diminta`, `qty`, `vendor`, `estimasi_harga`, `status`, `tgl_permintaan`, `created_at`, `updated_at`) VALUES
	(1, 'PR-PRJ001-001', 'PRJ001', 'Semen Portland 40kg', 500, 'PT Semen Indonesia', 20000000, 'Disetujui', '2026-01-08', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'PR-PRJ001-002', 'PRJ001', 'Besi Beton 10mm', 200, 'PT Krakatau Steel', 35000000, 'Disetujui', '2026-01-10', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(3, 'PR-PRJ001-003', 'PRJ001', 'Bata Merah 20x10x5', 5000, 'CV Bata Kuat', 10000000, 'Disetujui', '2026-01-12', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(4, 'PR-PRJ001-004', 'PRJ001', 'Cat Tembok & Finishing', 50, 'PT Nippon Paint', 15000000, 'Pending', '2026-02-20', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(5, 'PR-PRJ002-001', 'PRJ002', 'Bus Pariwisata 32 Seat', 3, 'PT Hino Motors', 1200000000, 'Disetujui', '2026-02-05', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(6, 'PR-PRJ002-002', 'PRJ002', 'Wrapping & Branding Bus', 3, 'CV Kreatif Visual', 15000000, 'Pending', '2026-04-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(7, 'PR-PRJ003-001', 'PRJ003', 'Unit GPS Tracker', 50, 'PT TechMaps', 100000000, 'Disetujui', '2026-01-14', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(8, 'PR-PRJ003-002', 'PRJ003', 'Server Dashboard Cloud', 1, 'PT AWS Indonesia', 24000000, 'Disetujui', '2026-01-15', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.pemeliharaan_assets
CREATE TABLE IF NOT EXISTS `pemeliharaan_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_service` date NOT NULL,
  `jenis_service` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biaya` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jadwal_selanjutnya` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.pemeliharaan_assets: ~0 rows (approximately)

-- Dumping structure for table apyrent.penanggung_jawabs
CREATE TABLE IF NOT EXISTS `penanggung_jawabs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_penempatan` date NOT NULL,
  `divisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.penanggung_jawabs: ~0 rows (approximately)

-- Dumping structure for table apyrent.penawarans
CREATE TABLE IF NOT EXISTS `penawarans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_quotation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_jasa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_sampai` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penawarans_no_quotation_unique` (`no_quotation`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.penawarans: ~40 rows (approximately)
INSERT INTO `penawarans` (`id`, `no_quotation`, `tanggal`, `pelanggan`, `produk_jasa`, `jumlah`, `harga_satuan`, `total_harga`, `status`, `valid_sampai`, `created_at`, `updated_at`) VALUES
	(1, 'QUO-2026-001', '2026-01-15', 'PT Maju Bersama', 'Sewa Minibus', 2, 5000000.00, 10000000.00, 'Disetujui', '2026-02-15', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'QUO-2026-002', '2026-01-20', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 8000000.00, 'Terkirim', '2026-02-20', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'QUO-2026-003', '2026-02-01', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 3500000.00, 10500000.00, 'Draft', '2026-03-01', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'QUO-2026-004', '2026-02-10', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 15000000.00, 'Disetujui', '2026-03-10', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'QUO-2026-005', '2026-02-25', 'CV Jaya Mandiri', 'Sewa MPV', 4, 4000000.00, 16000000.00, 'Terkirim', '2026-03-25', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'QUO-2026-006', '2026-03-05', 'PT Nusantara Raya', 'Sewa Minibus', 2, 5500000.00, 11000000.00, 'Disetujui', '2026-04-05', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'QUO-2026-007', '2026-03-15', 'PT Sinar Harapan', 'Sewa SUV', 2, 6000000.00, 12000000.00, 'Ditolak', '2026-04-15', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'QUO-2026-008', '2026-04-01', 'CV Mitra Logistik', 'Sewa Truk', 3, 7500000.00, 22500000.00, 'Terkirim', '2026-05-01', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'QUO-2026-009', '2026-04-20', 'PT Berlian Trans', 'Sewa Bus Medium', 2, 10000000.00, 20000000.00, 'Disetujui', '2026-05-20', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'QUO-2026-010', '2026-05-10', 'PT Prima Raya', 'Sewa Sedan', 5, 3000000.00, 15000000.00, 'Draft', '2026-06-10', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(11, 'QUO-001', '2026-03-23', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 10, 3696565.00, 36965650.00, 'Draft', '2026-05-08', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(12, 'QUO-002', '2026-05-10', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 9, 3437592.00, 30938328.00, 'Terkirim', '2026-07-09', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(13, 'QUO-003', '2026-04-08', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 3, 4327020.00, 12981060.00, 'Disetujui', '2026-04-28', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(14, 'QUO-004', '2026-03-29', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 10, 1510932.00, 15109320.00, 'Ditolak', '2026-04-16', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(15, 'QUO-005', '2026-01-19', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 8, 3071658.00, 24573264.00, 'Draft', '2026-02-07', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(16, 'QUO-006', '2026-04-28', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 8, 2089807.00, 16718456.00, 'Terkirim', '2026-06-25', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(17, 'QUO-007', '2026-05-20', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 7, 4424149.00, 30969043.00, 'Disetujui', '2026-06-20', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(18, 'QUO-008', '2026-07-06', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 3, 2593366.00, 7780098.00, 'Ditolak', '2026-08-29', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(19, 'QUO-009', '2026-03-16', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 3, 3348547.00, 10045641.00, 'Draft', '2026-04-12', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(20, 'QUO-010', '2026-06-02', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 5, 1143485.00, 5717425.00, 'Terkirim', '2026-07-29', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(21, 'QUO-011', '2026-02-22', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 2, 4246286.00, 8492572.00, 'Disetujui', '2026-03-23', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(22, 'QUO-012', '2026-02-08', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 7, 4256610.00, 29796270.00, 'Ditolak', '2026-02-26', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(23, 'QUO-013', '2026-05-06', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 10, 4832384.00, 48323840.00, 'Draft', '2026-05-28', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(24, 'QUO-014', '2026-05-15', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 6, 1169743.00, 7018458.00, 'Terkirim', '2026-07-05', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(25, 'QUO-015', '2026-06-02', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 10, 4098314.00, 40983140.00, 'Disetujui', '2026-07-08', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(26, 'QUO-016', '2026-06-09', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 1, 4614653.00, 4614653.00, 'Ditolak', '2026-07-22', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(27, 'QUO-017', '2026-06-06', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 9, 3167208.00, 28504872.00, 'Draft', '2026-07-03', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(28, 'QUO-018', '2026-06-28', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 8, 4560465.00, 36483720.00, 'Terkirim', '2026-08-20', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(29, 'QUO-019', '2026-06-20', 'PT Solusi Transportasi', 'Sewa Kendaraan Operasional', 5, 2882008.00, 14410040.00, 'Disetujui', '2026-07-24', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(30, 'QUO-020', '2026-05-16', 'PT Global Rentcar', 'Layanan Transportasi Proyek', 8, 2577364.00, 20618912.00, 'Ditolak', '2026-06-12', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(31, 'QUO-021', '2026-06-10', 'CV Perdana Sejahtera', 'Sewa Armada Angkutan Barang', 2, 3591266.00, 7182532.00, 'Draft', '2026-08-04', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(32, 'QUO-022', '2026-01-27', 'PT Aneka Niaga', 'Sewa Kendaraan Jangka Panjang', 9, 3794528.00, 34150752.00, 'Terkirim', '2026-03-06', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(33, 'QUO-023', '2026-06-01', 'PT Bintang Timur', 'Layanan Shuttle Karyawan', 5, 4111088.00, 20555440.00, 'Disetujui', '2026-07-31', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(34, 'QUO-024', '2026-04-06', 'CV Mitra Sejati', 'Sewa Minibus Pariwisata', 4, 4038505.00, 16154020.00, 'Ditolak', '2026-05-12', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(35, 'QUO-025', '2026-06-08', 'PT Maju Jaya Abadi', 'Sewa Kendaraan Operasional', 4, 1586547.00, 6346188.00, 'Draft', '2026-07-28', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(36, 'QUO-026', '2026-06-06', 'CV Berkah Mandiri', 'Layanan Transportasi Proyek', 10, 3502789.00, 35027890.00, 'Terkirim', '2026-07-08', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(37, 'QUO-027', '2026-06-04', 'PT Teknologi Nusantara', 'Sewa Armada Angkutan Barang', 5, 3183914.00, 15919570.00, 'Disetujui', '2026-06-20', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(38, 'QUO-028', '2026-04-17', 'UD Sumber Rejeki', 'Sewa Kendaraan Jangka Panjang', 5, 1332260.00, 6661300.00, 'Ditolak', '2026-05-27', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(39, 'QUO-029', '2026-06-18', 'PT Logistik Andalan', 'Layanan Shuttle Karyawan', 5, 3223484.00, 16117420.00, 'Draft', '2026-08-07', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(40, 'QUO-030', '2026-01-23', 'CV Karya Utama', 'Sewa Minibus Pariwisata', 7, 1555153.00, 10886071.00, 'Terkirim', '2026-02-08', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.penyusutan_assets
CREATE TABLE IF NOT EXISTS `penyusutan_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` year NOT NULL,
  `nilai_awal` decimal(15,2) NOT NULL,
  `akumulasi_penyusutan` decimal(15,2) NOT NULL,
  `nilai_buku` decimal(15,2) NOT NULL,
  `metode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.penyusutan_assets: ~0 rows (approximately)

-- Dumping structure for table apyrent.pergerakan_assets
CREATE TABLE IF NOT EXISTS `pergerakan_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_pergerakan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dari_lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ke_lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dilakukan_oleh` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disetujui_oleh` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.pergerakan_assets: ~0 rows (approximately)

-- Dumping structure for table apyrent.perolehan_assets
CREATE TABLE IF NOT EXISTS `perolehan_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal_perolehan` date NOT NULL,
  `kode_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metode_pembelian` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.perolehan_assets: ~0 rows (approximately)

-- Dumping structure for table apyrent.policy_compliances
CREATE TABLE IF NOT EXISTS `policy_compliances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `versi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_berlaku` date NOT NULL,
  `tanggung_jawab` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sertifikasi_terkait` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.policy_compliances: ~5 rows (approximately)
INSERT INTO `policy_compliances` (`id`, `nama_dokumen`, `versi`, `tanggal_berlaku`, `tanggung_jawab`, `status`, `sertifikasi_terkait`, `created_at`, `updated_at`) VALUES
	(1, 'Kebijakan Keamanan Informasi', 'v2.1', '2024-01-01', 'IT Manager', 'Aktif', 'ISO 27001', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'Prosedur Backup & Recovery', 'v1.3', '2024-03-01', 'System Administrator', 'Aktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'Kebijakan Penggunaan Aset IT', 'v1.0', '2024-06-01', 'HR & IT Manager', 'Draft', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'Disaster Recovery Plan', 'v3.0', '2023-07-01', 'CTO', 'Review', 'ISO 22301', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'Kebijakan Password & Akses', 'v2.0', '2024-01-01', 'IT Security Officer', 'Aktif', 'ISO 27001', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.presensis
CREATE TABLE IF NOT EXISTS `presensis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `metode_presensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi_presensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Hadir','Alpa','Izin','Terlambat') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.presensis: ~240 rows (approximately)
INSERT INTO `presensis` (`id`, `nama_pegawai`, `tanggal`, `jam_masuk`, `jam_pulang`, `metode_presensi`, `lokasi_presensi`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Budi Santoso', '2026-06-15', '00:00:00', '00:00:00', 'Manual', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 'Dewi Kusuma', '2026-06-15', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 'Rini Apriani', '2026-06-15', '07:32:00', '18:48:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 'Eko Prasetyo', '2026-06-15', '08:13:00', '18:21:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 'Hendra Gunawan', '2026-06-15', '09:13:00', '17:30:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 'Rizky Fadillah', '2026-06-15', '08:41:00', '17:57:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 'Yusuf Hidayat', '2026-06-15', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 'Linda Permata', '2026-06-15', '08:39:00', '18:28:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 'Wahyu Nugroho', '2026-06-15', '08:07:00', '18:48:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 'Fitri Handayani', '2026-06-15', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(11, 'Dody Kurniawan', '2026-06-15', '09:47:00', '18:46:00', 'GPS', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(12, 'Teguh Santosa', '2026-06-15', '07:01:00', '18:22:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(13, 'Budi Santoso', '2026-06-16', '09:10:00', '18:01:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(14, 'Dewi Kusuma', '2026-06-16', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(15, 'Rini Apriani', '2026-06-16', '08:33:00', '18:44:00', 'Manual', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(16, 'Eko Prasetyo', '2026-06-16', '08:59:00', '18:38:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(17, 'Hendra Gunawan', '2026-06-16', '08:46:00', '17:40:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(18, 'Rizky Fadillah', '2026-06-16', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(19, 'Yusuf Hidayat', '2026-06-16', '07:18:00', '17:24:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(20, 'Linda Permata', '2026-06-16', '08:24:00', '18:55:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(21, 'Wahyu Nugroho', '2026-06-16', '08:16:00', '18:58:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(22, 'Fitri Handayani', '2026-06-16', '07:08:00', '17:02:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(23, 'Dody Kurniawan', '2026-06-16', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(24, 'Teguh Santosa', '2026-06-16', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(25, 'Budi Santoso', '2026-06-17', '08:03:00', '18:20:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(26, 'Dewi Kusuma', '2026-06-17', '07:40:00', '18:10:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(27, 'Rini Apriani', '2026-06-17', '08:17:00', '17:35:00', 'Manual', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(28, 'Eko Prasetyo', '2026-06-17', '08:27:00', '17:16:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(29, 'Hendra Gunawan', '2026-06-17', '08:51:00', '18:09:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(30, 'Rizky Fadillah', '2026-06-17', '07:35:00', '17:09:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(31, 'Yusuf Hidayat', '2026-06-17', '08:39:00', '18:48:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(32, 'Linda Permata', '2026-06-17', '08:16:00', '17:55:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(33, 'Wahyu Nugroho', '2026-06-17', '07:04:00', '18:39:00', 'Face ID', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(34, 'Fitri Handayani', '2026-06-17', '08:13:00', '17:46:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(35, 'Dody Kurniawan', '2026-06-17', '07:53:00', '18:53:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(36, 'Teguh Santosa', '2026-06-17', '07:18:00', '18:58:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(37, 'Budi Santoso', '2026-06-18', '08:29:00', '17:52:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(38, 'Dewi Kusuma', '2026-06-18', '08:50:00', '18:56:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(39, 'Rini Apriani', '2026-06-18', '07:03:00', '18:40:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(40, 'Eko Prasetyo', '2026-06-18', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(41, 'Hendra Gunawan', '2026-06-18', '09:26:00', '17:03:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(42, 'Rizky Fadillah', '2026-06-18', '08:54:00', '17:29:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(43, 'Yusuf Hidayat', '2026-06-18', '07:58:00', '18:34:00', 'GPS', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(44, 'Linda Permata', '2026-06-18', '07:34:00', '18:52:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(45, 'Wahyu Nugroho', '2026-06-18', '08:04:00', '17:53:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(46, 'Fitri Handayani', '2026-06-18', '08:43:00', '17:53:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(47, 'Dody Kurniawan', '2026-06-18', '07:34:00', '17:44:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(48, 'Teguh Santosa', '2026-06-18', '07:04:00', '18:10:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(49, 'Budi Santoso', '2026-06-19', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(50, 'Dewi Kusuma', '2026-06-19', '08:02:00', '18:36:00', 'GPS', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(51, 'Rini Apriani', '2026-06-19', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(52, 'Eko Prasetyo', '2026-06-19', '08:53:00', '18:55:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(53, 'Hendra Gunawan', '2026-06-19', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(54, 'Rizky Fadillah', '2026-06-19', '07:37:00', '17:11:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(55, 'Yusuf Hidayat', '2026-06-19', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(56, 'Linda Permata', '2026-06-19', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(57, 'Wahyu Nugroho', '2026-06-19', '07:40:00', '17:15:00', 'GPS', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(58, 'Fitri Handayani', '2026-06-19', '07:03:00', '17:15:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(59, 'Dody Kurniawan', '2026-06-19', '08:46:00', '17:25:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(60, 'Teguh Santosa', '2026-06-19', '08:08:00', '17:38:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(61, 'Budi Santoso', '2026-06-22', '07:47:00', '18:37:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(62, 'Dewi Kusuma', '2026-06-22', '08:12:00', '17:11:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(63, 'Rini Apriani', '2026-06-22', '07:45:00', '18:05:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(64, 'Eko Prasetyo', '2026-06-22', '09:20:00', '18:48:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(65, 'Hendra Gunawan', '2026-06-22', '08:15:00', '18:50:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(66, 'Rizky Fadillah', '2026-06-22', '08:24:00', '17:37:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(67, 'Yusuf Hidayat', '2026-06-22', '07:25:00', '17:30:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(68, 'Linda Permata', '2026-06-22', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(69, 'Wahyu Nugroho', '2026-06-22', '08:39:00', '17:48:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(70, 'Fitri Handayani', '2026-06-22', '07:32:00', '18:03:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(71, 'Dody Kurniawan', '2026-06-22', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(72, 'Teguh Santosa', '2026-06-22', '08:42:00', '17:57:00', 'GPS', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(73, 'Budi Santoso', '2026-06-23', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(74, 'Dewi Kusuma', '2026-06-23', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(75, 'Rini Apriani', '2026-06-23', '07:33:00', '18:10:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(76, 'Eko Prasetyo', '2026-06-23', '08:24:00', '17:13:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(77, 'Hendra Gunawan', '2026-06-23', '08:58:00', '18:53:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(78, 'Rizky Fadillah', '2026-06-23', '07:43:00', '18:59:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(79, 'Yusuf Hidayat', '2026-06-23', '07:39:00', '17:45:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(80, 'Linda Permata', '2026-06-23', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(81, 'Wahyu Nugroho', '2026-06-23', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(82, 'Fitri Handayani', '2026-06-23', '08:37:00', '18:01:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(83, 'Dody Kurniawan', '2026-06-23', '08:21:00', '17:59:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(84, 'Teguh Santosa', '2026-06-23', '08:15:00', '17:26:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(85, 'Budi Santoso', '2026-06-24', '08:04:00', '17:41:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(86, 'Dewi Kusuma', '2026-06-24', '08:38:00', '18:33:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(87, 'Rini Apriani', '2026-06-24', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(88, 'Eko Prasetyo', '2026-06-24', '07:48:00', '18:22:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(89, 'Hendra Gunawan', '2026-06-24', '07:11:00', '17:15:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(90, 'Rizky Fadillah', '2026-06-24', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(91, 'Yusuf Hidayat', '2026-06-24', '08:20:00', '17:38:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(92, 'Linda Permata', '2026-06-24', '07:44:00', '18:38:00', 'GPS', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(93, 'Wahyu Nugroho', '2026-06-24', '07:57:00', '18:26:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(94, 'Fitri Handayani', '2026-06-24', '09:30:00', '18:47:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(95, 'Dody Kurniawan', '2026-06-24', '08:22:00', '18:51:00', 'Fingerprint', 'Kantor Surabaya', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(96, 'Teguh Santosa', '2026-06-24', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(97, 'Budi Santoso', '2026-06-25', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(98, 'Dewi Kusuma', '2026-06-25', '08:18:00', '17:34:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(99, 'Rini Apriani', '2026-06-25', '07:03:00', '18:31:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(100, 'Eko Prasetyo', '2026-06-25', '08:41:00', '18:56:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(101, 'Hendra Gunawan', '2026-06-25', '09:59:00', '17:06:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(102, 'Rizky Fadillah', '2026-06-25', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(103, 'Yusuf Hidayat', '2026-06-25', '08:54:00', '17:54:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(104, 'Linda Permata', '2026-06-25', '07:30:00', '18:51:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(105, 'Wahyu Nugroho', '2026-06-25', '08:27:00', '18:10:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(106, 'Fitri Handayani', '2026-06-25', '08:26:00', '17:45:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(107, 'Dody Kurniawan', '2026-06-25', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(108, 'Teguh Santosa', '2026-06-25', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(109, 'Budi Santoso', '2026-06-26', '08:32:00', '18:12:00', 'Manual', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(110, 'Dewi Kusuma', '2026-06-26', '07:03:00', '17:12:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(111, 'Rini Apriani', '2026-06-26', '07:07:00', '17:11:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(112, 'Eko Prasetyo', '2026-06-26', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(113, 'Hendra Gunawan', '2026-06-26', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(114, 'Rizky Fadillah', '2026-06-26', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(115, 'Yusuf Hidayat', '2026-06-26', '07:17:00', '17:34:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(116, 'Linda Permata', '2026-06-26', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(117, 'Wahyu Nugroho', '2026-06-26', '08:51:00', '17:36:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(118, 'Fitri Handayani', '2026-06-26', '07:56:00', '17:16:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(119, 'Dody Kurniawan', '2026-06-26', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(120, 'Teguh Santosa', '2026-06-26', '08:20:00', '17:15:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(121, 'Budi Santoso', '2026-06-29', '08:05:00', '17:03:00', 'Face ID', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(122, 'Dewi Kusuma', '2026-06-29', '07:56:00', '18:58:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(123, 'Rini Apriani', '2026-06-29', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(124, 'Eko Prasetyo', '2026-06-29', '08:25:00', '18:05:00', 'GPS', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(125, 'Hendra Gunawan', '2026-06-29', '08:13:00', '18:19:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(126, 'Rizky Fadillah', '2026-06-29', '07:09:00', '18:25:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(127, 'Yusuf Hidayat', '2026-06-29', '08:10:00', '17:13:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(128, 'Linda Permata', '2026-06-29', '08:27:00', '18:57:00', 'Manual', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(129, 'Wahyu Nugroho', '2026-06-29', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(130, 'Fitri Handayani', '2026-06-29', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(131, 'Dody Kurniawan', '2026-06-29', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(132, 'Teguh Santosa', '2026-06-29', '07:09:00', '18:45:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(133, 'Budi Santoso', '2026-06-30', '07:22:00', '18:58:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(134, 'Dewi Kusuma', '2026-06-30', '08:44:00', '17:28:00', 'GPS', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(135, 'Rini Apriani', '2026-06-30', '09:15:00', '18:10:00', 'Fingerprint', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(136, 'Eko Prasetyo', '2026-06-30', '08:11:00', '17:53:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(137, 'Hendra Gunawan', '2026-06-30', '07:58:00', '18:41:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(138, 'Rizky Fadillah', '2026-06-30', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(139, 'Yusuf Hidayat', '2026-06-30', '08:19:00', '17:27:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(140, 'Linda Permata', '2026-06-30', '08:55:00', '18:41:00', 'Face ID', 'Kantor Surabaya', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(141, 'Wahyu Nugroho', '2026-06-30', '08:12:00', '17:26:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(142, 'Fitri Handayani', '2026-06-30', '08:57:00', '18:13:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(143, 'Dody Kurniawan', '2026-06-30', '07:39:00', '17:34:00', 'Face ID', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(144, 'Teguh Santosa', '2026-06-30', '07:30:00', '17:13:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(145, 'Budi Santoso', '2026-07-01', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(146, 'Dewi Kusuma', '2026-07-01', '00:00:00', '00:00:00', 'Fingerprint', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(147, 'Rini Apriani', '2026-07-01', '07:48:00', '17:40:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(148, 'Eko Prasetyo', '2026-07-01', '08:51:00', '17:45:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(149, 'Hendra Gunawan', '2026-07-01', '09:56:00', '17:31:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(150, 'Rizky Fadillah', '2026-07-01', '08:11:00', '18:54:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(151, 'Yusuf Hidayat', '2026-07-01', '08:31:00', '18:42:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(152, 'Linda Permata', '2026-07-01', '08:55:00', '18:19:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(153, 'Wahyu Nugroho', '2026-07-01', '08:04:00', '17:59:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(154, 'Fitri Handayani', '2026-07-01', '07:04:00', '18:53:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(155, 'Dody Kurniawan', '2026-07-01', '08:29:00', '17:07:00', 'Manual', 'Kantor Surabaya', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(156, 'Teguh Santosa', '2026-07-01', '07:17:00', '18:37:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(157, 'Budi Santoso', '2026-07-02', '09:46:00', '17:58:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(158, 'Dewi Kusuma', '2026-07-02', '08:55:00', '18:57:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(159, 'Rini Apriani', '2026-07-02', '08:36:00', '18:30:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(160, 'Eko Prasetyo', '2026-07-02', '08:28:00', '17:46:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(161, 'Hendra Gunawan', '2026-07-02', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(162, 'Rizky Fadillah', '2026-07-02', '09:11:00', '18:12:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(163, 'Yusuf Hidayat', '2026-07-02', '09:52:00', '18:37:00', 'Face ID', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(164, 'Linda Permata', '2026-07-02', '07:04:00', '18:16:00', 'Manual', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(165, 'Wahyu Nugroho', '2026-07-02', '08:45:00', '17:49:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(166, 'Fitri Handayani', '2026-07-02', '07:16:00', '18:28:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(167, 'Dody Kurniawan', '2026-07-02', '07:05:00', '18:20:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(168, 'Teguh Santosa', '2026-07-02', '00:00:00', '00:00:00', 'Face ID', 'Lapangan', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(169, 'Budi Santoso', '2026-07-03', '08:32:00', '18:29:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(170, 'Dewi Kusuma', '2026-07-03', '08:21:00', '17:10:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(171, 'Rini Apriani', '2026-07-03', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(172, 'Eko Prasetyo', '2026-07-03', '07:10:00', '18:37:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(173, 'Hendra Gunawan', '2026-07-03', '08:34:00', '17:44:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(174, 'Rizky Fadillah', '2026-07-03', '08:43:00', '18:54:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(175, 'Yusuf Hidayat', '2026-07-03', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(176, 'Linda Permata', '2026-07-03', '09:27:00', '17:44:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(177, 'Wahyu Nugroho', '2026-07-03', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(178, 'Fitri Handayani', '2026-07-03', '00:00:00', '00:00:00', 'Manual', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(179, 'Dody Kurniawan', '2026-07-03', '07:33:00', '17:17:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(180, 'Teguh Santosa', '2026-07-03', '08:24:00', '17:32:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(181, 'Budi Santoso', '2026-07-06', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(182, 'Dewi Kusuma', '2026-07-06', '08:39:00', '18:57:00', 'Fingerprint', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(183, 'Rini Apriani', '2026-07-06', '07:56:00', '18:26:00', 'Face ID', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(184, 'Eko Prasetyo', '2026-07-06', '08:55:00', '17:48:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(185, 'Hendra Gunawan', '2026-07-06', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(186, 'Rizky Fadillah', '2026-07-06', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(187, 'Yusuf Hidayat', '2026-07-06', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(188, 'Linda Permata', '2026-07-06', '08:45:00', '17:01:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(189, 'Wahyu Nugroho', '2026-07-06', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(190, 'Fitri Handayani', '2026-07-06', '09:47:00', '17:07:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(191, 'Dody Kurniawan', '2026-07-06', '09:42:00', '17:36:00', 'Manual', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(192, 'Teguh Santosa', '2026-07-06', '00:00:00', '00:00:00', 'GPS', 'Kantor Surabaya', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(193, 'Budi Santoso', '2026-07-07', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(194, 'Dewi Kusuma', '2026-07-07', '07:35:00', '17:55:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(195, 'Rini Apriani', '2026-07-07', '08:03:00', '17:46:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(196, 'Eko Prasetyo', '2026-07-07', '07:56:00', '18:49:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(197, 'Hendra Gunawan', '2026-07-07', '08:28:00', '17:54:00', 'Face ID', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(198, 'Rizky Fadillah', '2026-07-07', '08:50:00', '17:30:00', 'Face ID', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(199, 'Yusuf Hidayat', '2026-07-07', '07:56:00', '17:54:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(200, 'Linda Permata', '2026-07-07', '00:00:00', '00:00:00', 'Face ID', 'WFH', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(201, 'Wahyu Nugroho', '2026-07-07', '09:34:00', '18:50:00', 'Manual', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(202, 'Fitri Handayani', '2026-07-07', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(203, 'Dody Kurniawan', '2026-07-07', '08:17:00', '18:19:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(204, 'Teguh Santosa', '2026-07-07', '00:00:00', '00:00:00', 'Manual', 'Lapangan', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(205, 'Budi Santoso', '2026-07-08', '08:40:00', '17:29:00', 'GPS', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(206, 'Dewi Kusuma', '2026-07-08', '08:59:00', '18:17:00', 'Manual', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(207, 'Rini Apriani', '2026-07-08', '07:31:00', '17:00:00', 'Fingerprint', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(208, 'Eko Prasetyo', '2026-07-08', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(209, 'Hendra Gunawan', '2026-07-08', '08:26:00', '17:30:00', 'Face ID', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(210, 'Rizky Fadillah', '2026-07-08', '08:43:00', '18:22:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(211, 'Yusuf Hidayat', '2026-07-08', '07:17:00', '17:14:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(212, 'Linda Permata', '2026-07-08', '07:15:00', '17:10:00', 'Manual', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(213, 'Wahyu Nugroho', '2026-07-08', '08:13:00', '17:34:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(214, 'Fitri Handayani', '2026-07-08', '07:33:00', '18:47:00', 'GPS', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(215, 'Dody Kurniawan', '2026-07-08', '08:54:00', '17:33:00', 'Face ID', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(216, 'Teguh Santosa', '2026-07-08', '00:00:00', '00:00:00', 'GPS', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(217, 'Budi Santoso', '2026-07-09', '00:00:00', '00:00:00', 'GPS', 'Lapangan', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(218, 'Dewi Kusuma', '2026-07-09', '00:00:00', '00:00:00', 'GPS', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(219, 'Rini Apriani', '2026-07-09', '08:30:00', '18:37:00', 'Manual', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(220, 'Eko Prasetyo', '2026-07-09', '07:15:00', '18:06:00', 'Fingerprint', 'Lapangan', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(221, 'Hendra Gunawan', '2026-07-09', '07:36:00', '18:54:00', 'Manual', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(222, 'Rizky Fadillah', '2026-07-09', '09:59:00', '18:17:00', 'GPS', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(223, 'Yusuf Hidayat', '2026-07-09', '09:43:00', '17:22:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(224, 'Linda Permata', '2026-07-09', '08:50:00', '17:07:00', 'Manual', 'WFH', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(225, 'Wahyu Nugroho', '2026-07-09', '09:48:00', '18:55:00', 'Fingerprint', 'Kantor Jakarta', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(226, 'Fitri Handayani', '2026-07-09', '07:22:00', '17:18:00', 'Face ID', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(227, 'Dody Kurniawan', '2026-07-09', '00:00:00', '00:00:00', 'Face ID', 'Kantor Surabaya', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(228, 'Teguh Santosa', '2026-07-09', '08:27:00', '18:32:00', 'Manual', 'Kantor Jakarta', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(229, 'Budi Santoso', '2026-07-10', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(230, 'Dewi Kusuma', '2026-07-10', '07:06:00', '17:29:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(231, 'Rini Apriani', '2026-07-10', '08:19:00', '18:02:00', 'GPS', 'WFH', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(232, 'Eko Prasetyo', '2026-07-10', '08:19:00', '18:36:00', 'GPS', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(233, 'Hendra Gunawan', '2026-07-10', '08:08:00', '18:20:00', 'Fingerprint', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(234, 'Rizky Fadillah', '2026-07-10', '08:04:00', '17:22:00', 'Manual', 'Kantor Surabaya', 'Hadir', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(235, 'Yusuf Hidayat', '2026-07-10', '08:21:00', '17:09:00', 'GPS', 'Lapangan', 'Terlambat', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(236, 'Linda Permata', '2026-07-10', '00:00:00', '00:00:00', 'Fingerprint', 'Kantor Surabaya', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(237, 'Wahyu Nugroho', '2026-07-10', '00:00:00', '00:00:00', 'Manual', 'WFH', 'Alpa', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(238, 'Fitri Handayani', '2026-07-10', '00:00:00', '00:00:00', 'Fingerprint', 'WFH', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(239, 'Dody Kurniawan', '2026-07-10', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(240, 'Teguh Santosa', '2026-07-10', '00:00:00', '00:00:00', 'Face ID', 'Kantor Jakarta', 'Izin', '2026-07-11 21:07:06', '2026-07-11 21:07:06');

-- Dumping structure for table apyrent.pricelist_diskons
CREATE TABLE IF NOT EXISTS `pricelist_diskons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_harga` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_normal` decimal(15,2) NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT '0.00',
  `harga_diskon` decimal(15,2) NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_selesai` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pricelist_diskons_id_harga_unique` (`id_harga`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.pricelist_diskons: ~10 rows (approximately)
INSERT INTO `pricelist_diskons` (`id`, `id_harga`, `nama_produk`, `level_pelanggan`, `harga_normal`, `diskon`, `harga_diskon`, `periode_mulai`, `periode_selesai`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'PRC-001', 'Sewa Sedan', 'Regular', 3500000.00, 0.00, 3500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'PRC-002', 'Sewa Sedan', 'Silver', 3500000.00, 5.00, 3325000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'PRC-003', 'Sewa Sedan', 'Gold', 3500000.00, 10.00, 3150000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'PRC-004', 'Sewa Minibus', 'Regular', 5000000.00, 0.00, 5000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'PRC-005', 'Sewa Minibus', 'Gold', 5000000.00, 10.00, 4500000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'PRC-006', 'Sewa Minibus', 'Platinum', 5000000.00, 15.00, 4250000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'PRC-007', 'Sewa Truk', 'Regular', 8000000.00, 0.00, 8000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'PRC-008', 'Sewa Truk', 'Platinum', 8000000.00, 12.00, 7040000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'PRC-009', 'Sewa Bus Besar', 'Regular', 15000000.00, 0.00, 15000000.00, '2026-01-01', '2026-12-31', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'PRC-010', 'Sewa Bus Besar', 'Gold', 15000000.00, 8.00, 13800000.00, '2026-01-01', '2026-12-31', 'Tidak Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.procurementos
CREATE TABLE IF NOT EXISTS `procurementos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workflow_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_workflow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trigger_event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `syarat_tambahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi_dilakukan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delay_aksi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `procurementos_workflow_id_unique` (`workflow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.procurementos: ~8 rows (approximately)
INSERT INTO `procurementos` (`id`, `workflow_id`, `nama_workflow`, `trigger_event`, `syarat_tambahan`, `aksi_dilakukan`, `delay_aksi`, `status`, `pic`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'WF001', 'Persetujuan Pengadaan Barang', 'Pengajuan Barang', 'Nominal > 5.000.000', 'Kirim Email ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Workflow approval pengadaan barang.', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'WF002', 'Approval Vendor Baru', 'Penambahan Vendor Baru', NULL, 'Kirim Notifikasi ke Admin', '30 Menit', 'Aktif', 'Admin Procurement', 'Workflow untuk approval vendor.', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'WF003', 'Review Purchase Request', 'PR Diajukan', 'Qty > 100 pcs', 'Kirim ke Manajer Gudang', '2 Jam', 'Aktif', 'Manajer Gudang', 'Workflow review permintaan barang dari gudang.', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'WF004', 'Approval Kontrak Vendor', 'Kontrak Baru Dibuat', 'Nilai Kontrak > 50.000.000', 'Kirim Email ke Direktur', '1 Hari', 'Aktif', 'Legal & Finance', 'Persetujuan kontrak vendor bernilai besar.', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'WF005', 'Notifikasi Stok Menipis', 'Stok < Minimum', NULL, 'Kirim Alert ke Procurement', 'Langsung', 'Aktif', 'Procurement', 'Otomatis kirim notifikasi saat stok mendekati batas minimum.', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'WF006', 'Evaluasi Vendor Periodik', 'Akhir Bulan', 'Rating < 3', 'Kirim Laporan ke Manager', '1 Hari', 'Aktif', 'Procurement', 'Evaluasi performa vendor setiap bulan.', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'WF007', 'Approval Pembelian Aset', 'Pengajuan Pembelian Aset', 'Nilai > 100.000.000', 'Kirim ke Komite Anggaran', '3 Hari', 'Nonaktif', 'Finance & Direktur', 'Pembelian aset besar perlu persetujuan komite.', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'WF008', 'Reminder Jatuh Tempo Kontrak', 'H-30 Kontrak Berakhir', NULL, 'Kirim Email Reminder', 'Langsung', 'Aktif', 'Procurement', 'Pengingat otomatis sebelum kontrak vendor habis.', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.project_costs
CREATE TABLE IF NOT EXISTS `project_costs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_biaya` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimasi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `realisasi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `selisih` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.project_costs: ~8 rows (approximately)
INSERT INTO `project_costs` (`id`, `proyek`, `kategori_biaya`, `estimasi`, `realisasi`, `selisih`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'PRJ001', 'Material Bangunan', 150000000.00, 142000000.00, -8000000.00, 'Efisien', NULL, NULL),
	(2, 'PRJ001', 'Upah Tenaga Kerja', 100000000.00, 115000000.00, 15000000.00, 'Over Budget', NULL, NULL),
	(3, 'PRJ001', 'Sewa Alat Berat', 50000000.00, 48000000.00, -2000000.00, 'Efisien', NULL, NULL),
	(4, 'PRJ002', 'Pembelian Unit Bus', 1200000000.00, 1200000000.00, 0.00, 'Normal', NULL, NULL),
	(5, 'PRJ002', 'Aksesoris & Modifikasi', 80000000.00, 92000000.00, 12000000.00, 'Over Budget', NULL, NULL),
	(6, 'PRJ003', 'Perangkat GPS', 120000000.00, 118500000.00, -1500000.00, 'Efisien', NULL, NULL),
	(7, 'PRJ003', 'Biaya Instalasi', 30000000.00, 30000000.00, 0.00, 'Normal', NULL, NULL),
	(8, 'PRJ005', 'Biaya Operasional', 200000000.00, 185000000.00, -15000000.00, 'Efisien', NULL, NULL);

-- Dumping structure for table apyrent.project_management
CREATE TABLE IF NOT EXISTS `project_management` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic_proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tujuan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimasi_waktu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `progres` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.project_management: ~5 rows (approximately)
INSERT INTO `project_management` (`id`, `nama_proyek`, `pic_proyek`, `tujuan`, `estimasi_waktu`, `status`, `progres`, `created_at`, `updated_at`) VALUES
	(1, 'Migrasi ERP ke Cloud', 'Budi Santoso', 'Memindahkan seluruh infrastruktur ERP dari on-premise ke cloud AWS untuk meningkatkan skalabilitas', '6 bulan', 'In Progress', 45, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'Implementasi SSO Perusahaan', 'Doni Prasetyo', 'Implementasi Single Sign-On untuk semua sistem internal menggunakan Keycloak', '3 bulan', 'Selesai', 100, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'Pengembangan Mobile App Driver', 'Andi Wijaya', 'Membuat aplikasi mobile untuk monitoring dan tracking kendaraan operasional', '4 bulan', 'In Progress', 30, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'Upgrade Infrastruktur Jaringan', 'Rudi Hermawan', 'Upgrade seluruh perangkat jaringan kantor pusat ke standar 10 Gbps', '2 bulan', 'Pending', 0, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'Implementasi WAF', 'Budi Santoso', 'Pemasangan Web Application Firewall untuk semua endpoint API publik', '1 bulan', 'Selesai', 100, '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.project_plannings
CREATE TABLE IF NOT EXISTS `project_plannings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahapan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `durasi` int NOT NULL DEFAULT '0',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.project_plannings: ~8 rows (approximately)
INSERT INTO `project_plannings` (`id`, `kode_proyek`, `tahapan`, `tgl_mulai`, `tgl_selesai`, `durasi`, `pic`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'PRJ001', 'Survey & Perencanaan', '2026-01-01', '2026-01-07', 7, 'Tim GA', 'Selesai', NULL, NULL),
	(2, 'PRJ001', 'Pengadaan Material', '2026-01-08', '2026-01-20', 12, 'Rudi', 'Selesai', NULL, NULL),
	(3, 'PRJ001', 'Konstruksi', '2026-01-21', '2026-03-15', 53, 'Kontraktor', 'Berjalan', NULL, NULL),
	(4, 'PRJ001', 'Finishing & Serahterima', '2026-03-16', '2026-03-31', 15, 'Rudi', 'Plan', NULL, NULL),
	(5, 'PRJ002', 'Seleksi Vendor Bus', '2026-02-01', '2026-02-15', 14, 'Rina', 'Selesai', NULL, NULL),
	(6, 'PRJ002', 'Negosiasi & Kontrak', '2026-02-16', '2026-02-28', 12, 'Rina', 'Berjalan', NULL, NULL),
	(7, 'PRJ003', 'Instalasi Perangkat GPS', '2026-01-15', '2026-02-28', 44, 'Ivan', 'Berjalan', NULL, NULL),
	(8, 'PRJ003', 'Uji Coba & Training', '2026-03-01', '2026-03-31', 30, 'Ivan', 'Plan', NULL, NULL);

-- Dumping structure for table apyrent.project_risks
CREATE TABLE IF NOT EXISTS `project_risks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `risiko` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dampak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kemungkinan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mitigasi` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.project_risks: ~8 rows (approximately)
INSERT INTO `project_risks` (`id`, `proyek`, `risiko`, `dampak`, `kemungkinan`, `mitigasi`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'PRJ001', 'Cuaca ekstrim hujan deras', 'Sedang', 'Tinggi', 'Sediakan terpal & pompa air', 'Terkendali', NULL, NULL),
	(2, 'PRJ001', 'Kenaikan harga material', 'Tinggi', 'Menengah', 'Kontrak harga tetap dengan supplier', 'Terkendali', NULL, NULL),
	(3, 'PRJ002', 'Keterlambatan pengiriman unit bus', 'Tinggi', 'Rendah', 'Klausul denda dalam perjanjian', 'Diajukan', NULL, NULL),
	(4, 'PRJ002', 'Fluktuasi kurs impor', 'Tinggi', 'Menengah', 'Hedging mata uang', 'Diajukan', NULL, NULL),
	(5, 'PRJ003', 'Perangkat GPS tidak kompatibel', 'Tinggi', 'Rendah', 'Uji coba sebelum instalasi massal', 'Terkendali', NULL, NULL),
	(6, 'PRJ003', 'Gangguan sinyal di area tertentu', 'Sedang', 'Menengah', 'Pasang booster sinyal di pool', 'Diajukan', NULL, NULL),
	(7, 'PRJ005', 'Driver tidak hadir mendadak', 'Tinggi', 'Menengah', 'Siapkan driver cadangan on-call', 'Terkendali', NULL, NULL),
	(8, 'PRJ005', 'Kemacetan rute utama', 'Sedang', 'Tinggi', 'Siapkan rute alternatif', 'Terkendali', NULL, NULL);

-- Dumping structure for table apyrent.project_timelines
CREATE TABLE IF NOT EXISTS `project_timelines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kegiatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deadline` date NOT NULL,
  `reminder` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.project_timelines: ~8 rows (approximately)
INSERT INTO `project_timelines` (`id`, `proyek`, `kegiatan`, `deadline`, `reminder`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'PRJ001', 'Pengecoran Lantai Garasi', '2026-02-10', 1, 'Selesai', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'PRJ001', 'Pemasangan Atap Baja Ringan', '2026-02-28', 1, 'Berjalan', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(3, 'PRJ001', 'Pemasangan Listrik & CCTV', '2026-03-10', 1, 'Scheduled', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(4, 'PRJ002', 'Pembayaran DP Pembelian Bus', '2026-02-20', 1, 'Selesai', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(5, 'PRJ002', 'Pengiriman Unit Bus ke Pool', '2026-04-15', 1, 'Scheduled', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(6, 'PRJ003', 'Pemasangan GPS 20 Unit Sedan', '2026-02-15', 0, 'Berjalan', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(7, 'PRJ003', 'Aktivasi Dashboard Monitoring', '2026-03-15', 1, 'Scheduled', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(8, 'PRJ005', 'Mulai Operasional Rute I', '2026-03-01', 0, 'Selesai', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.purchaseros
CREATE TABLE IF NOT EXISTS `purchaseros` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_pr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemohon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_jasa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_permintaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominal` bigint DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disetujui_oleh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_persetujuan` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `terakhir_diajukan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchaseros_no_pr_unique` (`no_pr`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.purchaseros: ~50 rows (approximately)
INSERT INTO `purchaseros` (`id`, `no_pr`, `tanggal`, `departemen`, `pemohon`, `barang_jasa`, `kode_barang`, `qty`, `satuan`, `alasan_permintaan`, `nominal`, `status`, `disetujui_oleh`, `tanggal_persetujuan`, `catatan`, `terakhir_diajukan`, `created_at`, `updated_at`) VALUES
	(1, 'PR-001', '2026-03-03', 'Produksi', 'Pemohon 1', 'Spare Part', 'BRG-007', 234, 'pcs', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-1', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'PR-002', '2026-07-10', 'Gudang', 'Pemohon 2', 'ATK', 'BRG-014', 334, 'unit', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Gudang', '2026-07-12', 'Catatan PR ke-2', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'PR-003', '2026-03-25', 'IT', 'Pemohon 3', 'Komputer', 'BRG-021', 483, 'liter', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-3', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'PR-004', '2026-03-14', 'Finance', 'Pemohon 4', 'Bahan Bakar', 'BRG-028', 300, 'kg', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Finance', '2026-03-17', 'Catatan PR ke-4', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'PR-005', '2026-05-18', 'HR', 'Pemohon 5', 'Oli Mesin', 'BRG-035', 80, 'set', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-5', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'PR-006', '2026-05-21', 'Marketing', 'Pemohon 6', 'Ban Kendaraan', 'BRG-042', 285, 'dus', 'Stok Habis', NULL, 'Disetujui', 'Manajer Marketing', '2026-05-23', 'Catatan PR ke-6', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'PR-007', '2026-04-10', 'Operasional', 'Pemohon 7', 'Seragam', 'BRG-049', 459, 'rim', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-7', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'PR-008', '2026-03-12', 'Maintenance', 'Pemohon 8', 'Alat Kebersihan', 'BRG-056', 418, 'buah', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Maintenance', '2026-03-13', 'Catatan PR ke-8', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'PR-009', '2026-03-06', 'Produksi', 'Pemohon 9', 'Mebel', 'BRG-063', 85, 'pcs', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-9', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'PR-010', '2026-02-11', 'Gudang', 'Pemohon 10', 'Printer', 'BRG-070', 391, 'unit', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Gudang', '2026-02-14', 'Catatan PR ke-10', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(11, 'PR-011', '2026-04-30', 'IT', 'Pemohon 11', 'Spare Part', 'BRG-077', 271, 'liter', 'Stok Habis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-11', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(12, 'PR-012', '2026-01-22', 'Finance', 'Pemohon 12', 'ATK', 'BRG-084', 499, 'kg', 'Persediaan Menipis', NULL, 'Selesai', 'Manajer Finance', '2026-01-24', 'Catatan PR ke-12', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(13, 'PR-013', '2026-03-09', 'HR', 'Pemohon 13', 'Komputer', 'BRG-091', 111, 'set', 'Permintaan Proyek', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-13', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(14, 'PR-014', '2026-04-28', 'Marketing', 'Pemohon 14', 'Bahan Bakar', 'BRG-098', 229, 'dus', 'Penggantian Rutin', NULL, 'Disetujui', 'Manajer Marketing', '2026-04-30', 'Catatan PR ke-14', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(15, 'PR-015', '2026-03-31', 'Operasional', 'Pemohon 15', 'Oli Mesin', 'BRG-105', 195, 'rim', 'Kebutuhan Mendadak', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-15', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(16, 'PR-016', '2026-02-24', 'Maintenance', 'Pemohon 16', 'Ban Kendaraan', 'BRG-112', 408, 'buah', 'Stok Habis', NULL, 'Selesai', 'Manajer Maintenance', '2026-02-26', 'Catatan PR ke-16', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(17, 'PR-017', '2026-03-18', 'Produksi', 'Pemohon 17', 'Seragam', 'BRG-119', 384, 'pcs', 'Persediaan Menipis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-17', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(18, 'PR-018', '2026-05-11', 'Gudang', 'Pemohon 18', 'Alat Kebersihan', 'BRG-126', 347, 'unit', 'Permintaan Proyek', NULL, 'Disetujui', 'Manajer Gudang', '2026-05-14', 'Catatan PR ke-18', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(19, 'PR-019', '2026-05-12', 'IT', 'Pemohon 19', 'Mebel', 'BRG-133', 188, 'liter', 'Penggantian Rutin', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-19', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(20, 'PR-020', '2026-02-18', 'Finance', 'Pemohon 20', 'Printer', 'BRG-140', 117, 'kg', 'Kebutuhan Mendadak', NULL, 'Selesai', 'Manajer Finance', '2026-02-19', 'Catatan PR ke-20', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(21, 'PR-021', '2026-03-24', 'HR', 'Pemohon 21', 'Spare Part', 'BRG-147', 320, 'set', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-21', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(22, 'PR-022', '2026-02-09', 'Marketing', 'Pemohon 22', 'ATK', 'BRG-154', 54, 'dus', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Marketing', '2026-02-11', 'Catatan PR ke-22', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(23, 'PR-023', '2026-02-10', 'Operasional', 'Pemohon 23', 'Komputer', 'BRG-161', 410, 'rim', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-23', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(24, 'PR-024', '2026-01-18', 'Maintenance', 'Pemohon 24', 'Bahan Bakar', 'BRG-168', 265, 'buah', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Maintenance', '2026-01-20', 'Catatan PR ke-24', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(25, 'PR-025', '2026-02-18', 'Produksi', 'Pemohon 25', 'Oli Mesin', 'BRG-175', 79, 'pcs', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-25', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(26, 'PR-026', '2026-03-19', 'Gudang', 'Pemohon 26', 'Ban Kendaraan', 'BRG-182', 213, 'unit', 'Stok Habis', NULL, 'Disetujui', 'Manajer Gudang', '2026-03-22', 'Catatan PR ke-26', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(27, 'PR-027', '2026-04-17', 'IT', 'Pemohon 27', 'Seragam', 'BRG-189', 93, 'liter', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-27', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(28, 'PR-028', '2026-01-27', 'Finance', 'Pemohon 28', 'Alat Kebersihan', 'BRG-196', 18, 'kg', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Finance', '2026-01-28', 'Catatan PR ke-28', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(29, 'PR-029', '2026-05-05', 'HR', 'Pemohon 29', 'Mebel', 'BRG-203', 248, 'set', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-29', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(30, 'PR-030', '2026-06-22', 'Marketing', 'Pemohon 30', 'Printer', 'BRG-210', 5, 'dus', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Marketing', '2026-06-25', 'Catatan PR ke-30', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(31, 'PR-031', '2026-01-17', 'Operasional', 'Pemohon 31', 'Spare Part', 'BRG-217', 354, 'rim', 'Stok Habis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-31', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(32, 'PR-032', '2026-04-11', 'Maintenance', 'Pemohon 32', 'ATK', 'BRG-224', 149, 'buah', 'Persediaan Menipis', NULL, 'Selesai', 'Manajer Maintenance', '2026-04-14', 'Catatan PR ke-32', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(33, 'PR-033', '2026-05-26', 'Produksi', 'Pemohon 33', 'Komputer', 'BRG-231', 21, 'pcs', 'Permintaan Proyek', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-33', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(34, 'PR-034', '2026-02-05', 'Gudang', 'Pemohon 34', 'Bahan Bakar', 'BRG-238', 289, 'unit', 'Penggantian Rutin', NULL, 'Disetujui', 'Manajer Gudang', '2026-02-06', 'Catatan PR ke-34', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(35, 'PR-035', '2026-05-04', 'IT', 'Pemohon 35', 'Oli Mesin', 'BRG-245', 18, 'liter', 'Kebutuhan Mendadak', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-35', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(36, 'PR-036', '2026-05-10', 'Finance', 'Pemohon 36', 'Ban Kendaraan', 'BRG-252', 408, 'kg', 'Stok Habis', NULL, 'Selesai', 'Manajer Finance', '2026-05-12', 'Catatan PR ke-36', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(37, 'PR-037', '2026-05-31', 'HR', 'Pemohon 37', 'Seragam', 'BRG-259', 227, 'set', 'Persediaan Menipis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-37', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(38, 'PR-038', '2026-02-03', 'Marketing', 'Pemohon 38', 'Alat Kebersihan', 'BRG-266', 119, 'dus', 'Permintaan Proyek', NULL, 'Disetujui', 'Manajer Marketing', '2026-02-06', 'Catatan PR ke-38', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(39, 'PR-039', '2026-02-12', 'Operasional', 'Pemohon 39', 'Mebel', 'BRG-273', 490, 'rim', 'Penggantian Rutin', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-39', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(40, 'PR-040', '2026-04-10', 'Maintenance', 'Pemohon 40', 'Printer', 'BRG-280', 387, 'buah', 'Kebutuhan Mendadak', NULL, 'Selesai', 'Manajer Maintenance', '2026-04-11', 'Catatan PR ke-40', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(41, 'PR-041', '2026-01-15', 'Produksi', 'Pemohon 41', 'Spare Part', 'BRG-287', 252, 'pcs', 'Stok Habis', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-41', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(42, 'PR-042', '2026-03-28', 'Gudang', 'Pemohon 42', 'ATK', 'BRG-294', 148, 'unit', 'Persediaan Menipis', NULL, 'Disetujui', 'Manajer Gudang', '2026-03-30', 'Catatan PR ke-42', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(43, 'PR-043', '2026-06-11', 'IT', 'Pemohon 43', 'Komputer', 'BRG-301', 36, 'liter', 'Permintaan Proyek', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-43', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(44, 'PR-044', '2026-05-26', 'Finance', 'Pemohon 44', 'Bahan Bakar', 'BRG-308', 331, 'kg', 'Penggantian Rutin', NULL, 'Selesai', 'Manajer Finance', '2026-05-29', 'Catatan PR ke-44', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(45, 'PR-045', '2026-04-02', 'HR', 'Pemohon 45', 'Oli Mesin', 'BRG-315', 438, 'set', 'Kebutuhan Mendadak', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-45', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(46, 'PR-046', '2026-04-28', 'Marketing', 'Pemohon 46', 'Ban Kendaraan', 'BRG-322', 147, 'dus', 'Stok Habis', NULL, 'Disetujui', 'Manajer Marketing', '2026-04-30', 'Catatan PR ke-46', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(47, 'PR-047', '2026-02-19', 'Operasional', 'Pemohon 47', 'Seragam', 'BRG-329', 383, 'rim', 'Persediaan Menipis', NULL, 'Ditolak', NULL, NULL, 'Catatan PR ke-47', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(48, 'PR-048', '2026-02-27', 'Maintenance', 'Pemohon 48', 'Alat Kebersihan', 'BRG-336', 370, 'buah', 'Permintaan Proyek', NULL, 'Selesai', 'Manajer Maintenance', '2026-03-01', 'Catatan PR ke-48', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(49, 'PR-049', '2026-06-24', 'Produksi', 'Pemohon 49', 'Mebel', 'BRG-343', 202, 'pcs', 'Penggantian Rutin', NULL, 'Pending', NULL, NULL, 'Catatan PR ke-49', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(50, 'PR-050', '2026-04-30', 'Gudang', 'Pemohon 50', 'Printer', 'BRG-350', 69, 'unit', 'Kebutuhan Mendadak', NULL, 'Disetujui', 'Manajer Gudang', '2026-05-01', 'Catatan PR ke-50', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.purchase_orders
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_po` date NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terkait_rfq` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_barang` int NOT NULL,
  `total_harga` bigint NOT NULL,
  `status_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_terima` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.purchase_orders: ~30 rows (approximately)
INSERT INTO `purchase_orders` (`id`, `po_id`, `tanggal_po`, `vendor`, `terkait_rfq`, `total_barang`, `total_harga`, `status_po`, `tanggal_kirim`, `tanggal_terima`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'PO-001', '2026-06-24', 'PT Maju Jaya', 'RFQ-001', 37, 14773740, 'Pending', '2026-07-09', NULL, 'Catatan PO ke-1', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'PO-002', '2026-06-17', 'CV Berkah Abadi', 'RFQ-002', 47, 25961482, 'Approved', '2026-06-27', NULL, 'Catatan PO ke-2', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(3, 'PO-003', '2026-04-10', 'PT Sumber Makmur', 'RFQ-003', 16, 17137920, 'Closed', '2026-04-23', '2026-04-27', 'Catatan PO ke-3', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(4, 'PO-004', '2026-03-23', 'UD Sejahtera', 'RFQ-004', 28, 24692230, 'Pending', '2026-04-09', NULL, 'Catatan PO ke-4', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(5, 'PO-005', '2026-05-08', 'PT Indo Supplier', 'RFQ-005', 25, 15868416, 'Approved', '2026-05-16', NULL, 'Catatan PO ke-5', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(6, 'PO-006', '2026-03-20', 'PT Maju Jaya', 'RFQ-006', 26, 4518178, 'Closed', '2026-04-02', '2026-04-04', 'Catatan PO ke-6', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(7, 'PO-007', '2026-04-28', 'CV Berkah Abadi', 'RFQ-007', 21, 34198600, 'Pending', '2026-05-18', NULL, 'Catatan PO ke-7', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(8, 'PO-008', '2026-05-06', 'PT Sumber Makmur', 'RFQ-008', 12, 38275256, 'Approved', '2026-05-20', NULL, 'Catatan PO ke-8', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(9, 'PO-009', '2026-03-13', 'UD Sejahtera', 'RFQ-009', 8, 33295804, 'Closed', '2026-03-28', '2026-03-31', 'Catatan PO ke-9', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(10, 'PO-010', '2026-07-04', 'PT Indo Supplier', 'RFQ-010', 8, 45713110, 'Pending', '2026-07-18', NULL, 'Catatan PO ke-10', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(11, 'PO-011', '2026-04-03', 'PT Maju Jaya', 'RFQ-011', 46, 5633233, 'Approved', '2026-04-17', NULL, 'Catatan PO ke-11', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(12, 'PO-012', '2026-05-13', 'CV Berkah Abadi', 'RFQ-012', 30, 44222199, 'Closed', '2026-05-29', '2026-06-03', 'Catatan PO ke-12', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(13, 'PO-013', '2026-04-09', 'PT Sumber Makmur', 'RFQ-013', 1, 1220464, 'Pending', '2026-04-28', NULL, 'Catatan PO ke-13', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(14, 'PO-014', '2026-05-16', 'UD Sejahtera', 'RFQ-014', 38, 20627833, 'Approved', '2026-06-06', NULL, 'Catatan PO ke-14', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(15, 'PO-015', '2026-02-19', 'PT Indo Supplier', 'RFQ-015', 45, 8262387, 'Closed', '2026-03-09', '2026-03-15', 'Catatan PO ke-15', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(16, 'PO-016', '2026-03-27', 'PT Maju Jaya', 'RFQ-016', 7, 33534396, 'Pending', '2026-04-06', NULL, 'Catatan PO ke-16', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(17, 'PO-017', '2026-05-16', 'CV Berkah Abadi', 'RFQ-017', 42, 885300, 'Approved', '2026-05-30', NULL, 'Catatan PO ke-17', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(18, 'PO-018', '2026-02-17', 'PT Sumber Makmur', 'RFQ-018', 19, 31594682, 'Closed', '2026-02-27', '2026-02-28', 'Catatan PO ke-18', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(19, 'PO-019', '2026-05-15', 'UD Sejahtera', 'RFQ-019', 20, 19289318, 'Pending', '2026-05-31', NULL, 'Catatan PO ke-19', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(20, 'PO-020', '2026-05-21', 'PT Indo Supplier', 'RFQ-020', 40, 48323233, 'Approved', '2026-06-09', NULL, 'Catatan PO ke-20', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(21, 'PO-021', '2026-03-24', 'PT Maju Jaya', 'RFQ-021', 16, 32724268, 'Closed', '2026-04-10', '2026-04-11', 'Catatan PO ke-21', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(22, 'PO-022', '2026-07-09', 'CV Berkah Abadi', 'RFQ-022', 13, 19648577, 'Pending', '2026-07-23', NULL, 'Catatan PO ke-22', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(23, 'PO-023', '2026-04-13', 'PT Sumber Makmur', 'RFQ-023', 42, 1276828, 'Approved', '2026-04-22', NULL, 'Catatan PO ke-23', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(24, 'PO-024', '2026-06-26', 'UD Sejahtera', 'RFQ-024', 2, 49118107, 'Closed', '2026-07-11', '2026-07-16', 'Catatan PO ke-24', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(25, 'PO-025', '2026-04-28', 'PT Indo Supplier', 'RFQ-025', 23, 35516311, 'Pending', '2026-05-09', NULL, 'Catatan PO ke-25', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(26, 'PO-026', '2026-06-07', 'PT Maju Jaya', 'RFQ-026', 3, 10588163, 'Approved', '2026-06-17', NULL, 'Catatan PO ke-26', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(27, 'PO-027', '2026-03-12', 'CV Berkah Abadi', 'RFQ-027', 8, 33852586, 'Closed', '2026-03-23', '2026-03-26', 'Catatan PO ke-27', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(28, 'PO-028', '2026-04-04', 'PT Sumber Makmur', 'RFQ-028', 50, 7287858, 'Pending', '2026-04-25', NULL, 'Catatan PO ke-28', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(29, 'PO-029', '2026-05-18', 'UD Sejahtera', 'RFQ-029', 30, 45764135, 'Approved', '2026-05-28', NULL, 'Catatan PO ke-29', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(30, 'PO-030', '2026-04-01', 'PT Indo Supplier', 'RFQ-030', 8, 1224482, 'Closed', '2026-04-16', '2026-04-18', 'Catatan PO ke-30', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.rekonsiliasi_bank
CREATE TABLE IF NOT EXISTS `rekonsiliasi_bank` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `status_rekonsiliasi` enum('matched','unmatched','Pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `invoice_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `va` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.rekonsiliasi_bank: ~5 rows (approximately)
INSERT INTO `rekonsiliasi_bank` (`id`, `tanggal`, `deskripsi`, `reference_no`, `amount`, `currency`, `status_rekonsiliasi`, `invoice_id`, `va`, `bukti_pembayaran`, `created_at`, `updated_at`) VALUES
	(1, '2026-06-30', 'Pembayaran rental masuk', 'BANK-INV-001', 1500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, '2026-07-03', 'Transfer service kendaraan', 'BANK-INV-002', 500000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, '2026-07-06', 'Pembayaran deposit rental', 'BANK-INV-003', 2000000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, '2026-07-09', 'Pembayaran sparepart', 'BANK-INV-004', 750000.00, 'IDR', 'matched', NULL, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, '2026-07-12', 'Pemasukan rental harian', 'BANK-INV-005', 1200000.00, 'IDR', 'Pending', NULL, NULL, NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.reminder_service
CREATE TABLE IF NOT EXISTS `reminder_service` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `nama_reminder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `interval_nilai` int NOT NULL DEFAULT '1',
  `interval_satuan` enum('hari','minggu','bulan','tahun') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bulan',
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `biaya` decimal(15,2) DEFAULT NULL,
  `status` enum('aktif','jatuh_tempo','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `sudah_dibuat_masalah` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reminder_service_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `reminder_service_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.reminder_service: ~15 rows (approximately)
INSERT INTO `reminder_service` (`id`, `kendaraan_id`, `nama_reminder`, `tanggal_mulai`, `interval_nilai`, `interval_satuan`, `tanggal_jatuh_tempo`, `keterangan`, `biaya`, `status`, `sudah_dibuat_masalah`, `created_at`, `updated_at`) VALUES
	(1, 26, 'Ganti Ban', '2026-06-12', 1, 'bulan', '2026-07-12', 'Kae', NULL, 'jatuh_tempo', 1, '2026-07-11 23:23:41', '2026-07-11 23:28:10'),
	(2, 51, 'Service Rutin', '2026-07-12', 3, 'bulan', '2026-10-12', 'Auto-generated dari tanggal terakhir service kendaraan', NULL, 'aktif', 0, '2026-07-12 00:05:22', '2026-07-12 00:05:22'),
	(3, 18, 'Ban', '2026-07-13', 1, 'bulan', '2026-08-13', 'Ban jsaiijsa', NULL, 'aktif', 0, '2026-07-12 00:21:02', '2026-07-12 00:21:02'),
	(4, 18, 'Kaca', '2026-07-11', 1, 'hari', '2026-07-12', 'Kacang', NULL, 'jatuh_tempo', 1, '2026-07-12 00:21:02', '2026-07-12 00:21:02'),
	(5, 18, 'Service Rutin', '2026-07-12', 3, 'bulan', '2026-10-12', 'Auto-reset setelah service selesai pada 12/07/2026', NULL, 'aktif', 0, '2026-07-12 00:22:51', '2026-07-12 00:22:51'),
	(6, 17, 'Ban', '2026-07-13', 1, 'bulan', '2026-08-13', 'Ban jsaiijsa', NULL, 'aktif', 0, '2026-07-12 00:50:08', '2026-07-12 00:50:08'),
	(7, 17, 'Kaca', '2026-07-28', 1, 'bulan', '2026-08-28', 'Kacang', NULL, 'aktif', 0, '2026-07-12 00:50:08', '2026-07-12 00:50:08'),
	(8, 1, 'Ganti Velg', '2026-07-12', 1, 'hari', '2026-07-13', 'Kae', NULL, 'jatuh_tempo', 1, '2026-07-13 07:51:56', '2026-07-13 07:51:56'),
	(9, 1, 'Ganti Ban', '2026-07-12', 1, 'hari', '2026-07-13', 'Kae', NULL, 'jatuh_tempo', 1, '2026-07-13 07:52:29', '2026-07-13 07:52:29'),
	(10, 46, 'Ganti Velg', '2026-07-13', 1, 'bulan', '2026-08-13', 'BUUBDFGHJKSAK', 900000.00, 'aktif', 0, '2026-07-13 08:05:34', '2026-07-13 08:05:34'),
	(11, 28, 'Service Rutin', '2026-07-12', 3, 'bulan', '2026-10-12', 'Auto-reset setelah service selesai pada 12/07/2026', NULL, 'selesai', 0, '2026-07-13 08:13:02', '2026-07-13 08:23:57'),
	(12, 16, 'Service Rutin', '2026-07-23', 3, 'bulan', '2026-10-23', 'Auto-reset setelah service selesai pada 23/07/2026', NULL, 'aktif', 0, '2026-07-13 08:15:59', '2026-07-13 08:15:59'),
	(13, 28, 'Service Rutin', '2026-07-12', 3, 'bulan', '2026-10-12', 'Auto-reset setelah service selesai pada 12/07/2026', NULL, 'aktif', 0, '2026-07-13 08:23:57', '2026-07-13 08:23:57'),
	(14, 1, 'Service Rutin', '2026-07-07', 3, 'bulan', '2026-10-07', 'Auto-reset setelah service selesai pada 07/07/2026', NULL, 'aktif', 0, '2026-07-13 08:31:17', '2026-07-13 08:31:17'),
	(15, 48, 'Service Rutin', '2026-07-13', 3, 'bulan', '2026-10-13', 'Auto-reset setelah service selesai pada 13/07/2026', NULL, 'selesai', 0, '2026-07-13 09:08:03', '2026-07-13 09:08:33'),
	(16, 48, 'Service Rutin', '2026-07-13', 3, 'bulan', '2026-10-13', 'Auto-reset setelah service selesai pada 13/07/2026', NULL, 'aktif', 0, '2026-07-13 09:08:33', '2026-07-13 09:08:33'),
	(17, 18, 'Ganti Ban', '2026-06-13', 1, 'bulan', '2026-07-13', 'Kae', 100000.00, 'jatuh_tempo', 1, '2026-07-14 11:37:45', '2026-07-14 11:37:45'),
	(18, 39, 'Ganti Velg', '2026-07-15', 1, 'hari', '2026-07-16', 'Kae', 900000.00, 'aktif', 0, '2026-07-14 11:41:33', '2026-07-14 11:42:50');

-- Dumping structure for table apyrent.rentals
CREATE TABLE IF NOT EXISTS `rentals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `member_id` bigint unsigned NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `tujuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `durasi_jam` int DEFAULT NULL,
  `durasi_hari` int DEFAULT NULL,
  `durasi_bulan` int DEFAULT NULL,
  `biaya_dasar` bigint NOT NULL DEFAULT '0',
  `biaya_tambahan_total` bigint NOT NULL DEFAULT '0',
  `total_biaya` bigint NOT NULL DEFAULT '0',
  `metode_pembayaran` enum('tunai','transfer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transfer',
  `jenis_pembayaran` enum('lunas','dp') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lunas',
  `nominal_dp` bigint DEFAULT NULL,
  `nama_driver` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_driver` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biaya_driver` bigint DEFAULT NULL,
  `bukti_lunas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_dp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_pelunasan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pembayaran` enum('belum_bayar','dp','lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_bayar',
  `status` enum('Pending','booking','aktif','selesai','batal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pakai_batas_biaya` tinyint(1) NOT NULL DEFAULT '0',
  `batas_biaya` decimal(15,2) DEFAULT NULL,
  `durasi_tahun` bigint DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelayakan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rentals_user_id_foreign` (`user_id`),
  KEY `rentals_kendaraan_id_foreign` (`kendaraan_id`),
  KEY `rentals_member_id_foreign` (`member_id`),
  CONSTRAINT `rentals_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rentals_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rentals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.rentals: ~50 rows (approximately)
INSERT INTO `rentals` (`id`, `user_id`, `kendaraan_id`, `member_id`, `tanggal_mulai`, `tanggal_selesai`, `tujuan`, `durasi_jam`, `durasi_hari`, `durasi_bulan`, `biaya_dasar`, `biaya_tambahan_total`, `total_biaya`, `metode_pembayaran`, `jenis_pembayaran`, `nominal_dp`, `nama_driver`, `kontak_driver`, `biaya_driver`, `bukti_lunas`, `bukti_dp`, `bukti_pelunasan`, `status_pembayaran`, `status`, `created_at`, `updated_at`, `pakai_batas_biaya`, `batas_biaya`, `durasi_tahun`, `invoice`, `kelayakan`) VALUES
	(1, 1, 1, 1, '2026-04-07 04:07:02', '2026-04-14 04:07:02', 'Perjalanan dinas ke kota 1', 8, 7, 0, 1428000, 477000, 1905000, 'tunai', 'lunas', NULL, NULL, NULL, 72000, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(2, 1, 2, 2, '2026-07-03 04:07:02', '2026-07-17 04:07:02', 'Perjalanan dinas ke kota 2', 1, 14, 0, 3934000, 272000, 4206000, 'transfer', 'dp', 2103000, NULL, '08356795000', 186000, NULL, NULL, NULL, 'dp', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(3, 1, 3, 3, '2026-05-26 04:07:02', '2026-05-28 04:07:02', 'Perjalanan dinas ke kota 3', 2, 2, 0, 1026000, 443000, 1469000, 'tunai', 'lunas', NULL, 'Driver 3', '08547443346', NULL, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(4, 1, 4, 4, '2026-03-15 04:07:02', '2026-03-23 04:07:02', 'Perjalanan dinas ke kota 4', 2, 8, 0, 3680000, 193000, 3873000, 'transfer', 'dp', 1936500, 'Driver 4', '08467702393', NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(5, 1, 5, 5, '2026-02-02 04:07:02', '2026-02-10 04:07:02', 'Perjalanan dinas ke kota 5', 3, 8, 0, 3160000, 221000, 3381000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(6, 1, 6, 6, '2026-06-15 04:07:02', '2026-06-18 04:07:02', 'Perjalanan dinas ke kota 6', 2, 3, 0, 1263000, 322000, 1585000, 'transfer', 'dp', 792500, 'Driver 6', '08393730438', NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(7, 1, 7, 7, '2026-01-22 04:07:02', '2026-02-03 04:07:02', 'Perjalanan dinas ke kota 7', 7, 12, 0, 5700000, 55000, 5755000, 'tunai', 'lunas', NULL, 'Driver 7', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(8, 1, 8, 8, '2026-02-12 04:07:02', '2026-02-25 04:07:02', 'Perjalanan dinas ke kota 8', 8, 13, 0, 4056000, 10000, 4066000, 'transfer', 'dp', 2033000, NULL, '08943025870', NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(9, 1, 9, 9, '2026-05-04 04:07:02', '2026-05-09 04:07:02', 'Perjalanan dinas ke kota 9', 8, 5, 0, 1625000, 59000, 1684000, 'tunai', 'lunas', NULL, NULL, NULL, 124000, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(10, 1, 10, 10, '2026-03-27 04:07:02', '2026-04-02 04:07:02', 'Perjalanan dinas ke kota 10', 5, 6, 0, 3474000, 83000, 3557000, 'transfer', 'dp', 1778500, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(11, 1, 11, 11, '2026-04-15 04:07:02', '2026-04-28 04:07:02', 'Perjalanan dinas ke kota 11', 4, 13, 0, 4615000, 13000, 4628000, 'tunai', 'lunas', NULL, 'Driver 11', NULL, 59000, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(12, 1, 12, 12, '2026-03-26 04:07:02', '2026-04-09 04:07:02', 'Perjalanan dinas ke kota 12', 0, 14, 0, 7770000, 124000, 7894000, 'transfer', 'dp', 3947000, NULL, '08175813004', NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(13, 1, 13, 13, '2026-03-13 04:07:02', '2026-03-24 04:07:02', 'Perjalanan dinas ke kota 13', 4, 11, 0, 6248000, 13000, 6261000, 'tunai', 'lunas', NULL, NULL, '08497640101', 180000, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(14, 1, 14, 14, '2026-06-09 04:07:02', '2026-06-20 04:07:02', 'Perjalanan dinas ke kota 14', 7, 11, 0, 4180000, 114000, 4294000, 'transfer', 'dp', 2147000, 'Driver 14', '08248061831', 155000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(15, 1, 15, 15, '2026-04-28 04:07:02', '2026-05-04 04:07:02', 'Perjalanan dinas ke kota 15', 7, 6, 0, 2892000, 8000, 2900000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(16, 1, 16, 16, '2026-03-06 04:07:02', '2026-03-11 04:07:02', 'Perjalanan dinas ke kota 16', 6, 5, 0, 1075000, 499000, 1574000, 'transfer', 'dp', 787000, 'Driver 16', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(17, 1, 17, 17, '2026-06-03 04:07:02', '2026-06-16 04:07:02', 'Perjalanan dinas ke kota 17', 3, 13, 0, 6526000, 251000, 6777000, 'tunai', 'lunas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dp', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(18, 1, 18, 18, '2026-02-15 04:07:02', '2026-02-28 04:07:02', 'Perjalanan dinas ke kota 18', 1, 13, 0, 4134000, 292000, 4426000, 'transfer', 'dp', 2213000, NULL, NULL, 158000, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(19, 1, 19, 19, '2026-04-07 04:07:02', '2026-04-15 04:07:02', 'Perjalanan dinas ke kota 19', 1, 8, 0, 3432000, 26000, 3458000, 'tunai', 'lunas', NULL, 'Driver 19', '08826136031', 143000, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(20, 1, 20, 20, '2026-06-22 04:07:02', '2026-07-04 04:07:02', 'Perjalanan dinas ke kota 20', 0, 12, 0, 3840000, 321000, 4161000, 'transfer', 'dp', 2080500, NULL, '08281391766', 133000, NULL, NULL, NULL, 'dp', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(21, 1, 21, 21, '2026-02-18 04:07:02', '2026-02-24 04:07:02', 'Perjalanan dinas ke kota 21', 1, 6, 0, 1860000, 146000, 2006000, 'tunai', 'lunas', NULL, 'Driver 21', '08919686501', NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(22, 1, 22, 22, '2026-04-19 04:07:02', '2026-04-30 04:07:02', 'Perjalanan dinas ke kota 22', 2, 11, 0, 3784000, 95000, 3879000, 'transfer', 'dp', 1939500, 'Driver 22', '08563288901', NULL, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(23, 1, 23, 23, '2026-06-29 04:07:02', '2026-07-09 04:07:02', 'Perjalanan dinas ke kota 23', 3, 10, 0, 4590000, 434000, 5024000, 'tunai', 'lunas', NULL, 'Driver 23', '08208721110', 59000, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(24, 1, 24, 24, '2026-04-20 04:07:02', '2026-04-29 04:07:02', 'Perjalanan dinas ke kota 24', 2, 9, 0, 2007000, 424000, 2431000, 'transfer', 'dp', 1215500, 'Driver 24', '08595348726', 96000, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(25, 1, 25, 25, '2026-02-20 04:07:02', '2026-02-22 04:07:02', 'Perjalanan dinas ke kota 25', 1, 2, 0, 900000, 369000, 1269000, 'tunai', 'lunas', NULL, 'Driver 25', '08385068633', NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(26, 1, 26, 26, '2026-05-02 04:07:02', '2026-05-15 04:07:02', 'Perjalanan dinas ke kota 26', 0, 13, 0, 4472000, 141000, 4613000, 'transfer', 'dp', 2306500, NULL, NULL, 63000, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(27, 1, 27, 27, '2026-06-20 04:07:02', '2026-07-01 04:07:02', 'Perjalanan dinas ke kota 27', 8, 11, 0, 5236000, 445000, 5681000, 'tunai', 'lunas', NULL, NULL, '08524160587', NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(28, 1, 28, 28, '2026-07-09 04:07:02', '2026-07-16 04:07:02', 'Perjalanan dinas ke kota 28', 0, 7, 0, 3248000, 72000, 3320000, 'transfer', 'dp', 1660000, 'Driver 28', '08944409431', 80000, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(29, 1, 29, 29, '2026-07-11 04:07:02', '2026-07-13 04:07:02', 'Perjalanan dinas ke kota 29', 4, 2, 0, 444000, 480000, 924000, 'tunai', 'lunas', NULL, 'Driver 29', '08430141118', 138000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(30, 1, 30, 30, '2026-02-28 04:07:02', '2026-03-13 04:07:02', 'Perjalanan dinas ke kota 30', 7, 13, 0, 5252000, 466000, 5718000, 'transfer', 'dp', 2859000, 'Driver 30', '08254135259', 104000, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(31, 1, 31, 31, '2026-03-13 04:07:02', '2026-03-14 04:07:02', 'Perjalanan dinas ke kota 31', 2, 1, 0, 453000, 77000, 530000, 'tunai', 'lunas', NULL, NULL, '08258400252', NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(32, 1, 32, 32, '2026-04-30 04:07:02', '2026-05-12 04:07:02', 'Perjalanan dinas ke kota 32', 1, 12, 0, 2628000, 96000, 2724000, 'transfer', 'dp', 1362000, NULL, '08684400123', 51000, NULL, NULL, NULL, 'dp', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(33, 1, 33, 33, '2026-07-04 04:07:02', '2026-07-16 04:07:02', 'Perjalanan dinas ke kota 33', 4, 12, 0, 3360000, 262000, 3622000, 'tunai', 'lunas', NULL, NULL, NULL, 141000, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(34, 1, 34, 34, '2026-05-06 04:07:02', '2026-05-17 04:07:02', 'Perjalanan dinas ke kota 34', 0, 11, 0, 4477000, 177000, 4654000, 'transfer', 'dp', 2327000, NULL, NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(35, 1, 35, 35, '2026-06-01 04:07:02', '2026-06-05 04:07:02', 'Perjalanan dinas ke kota 35', 2, 4, 0, 1296000, 80000, 1376000, 'tunai', 'lunas', NULL, 'Driver 35', '08619662140', 189000, NULL, NULL, NULL, 'dp', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(36, 1, 36, 36, '2026-05-23 04:07:02', '2026-06-01 04:07:02', 'Perjalanan dinas ke kota 36', 5, 9, 0, 1854000, 261000, 2115000, 'transfer', 'dp', 1057500, 'Driver 36', '08260869233', NULL, NULL, NULL, NULL, 'lunas', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(37, 1, 37, 37, '2026-04-14 04:07:02', '2026-04-15 04:07:02', 'Perjalanan dinas ke kota 37', 2, 1, 0, 282000, 102000, 384000, 'tunai', 'lunas', NULL, 'Driver 37', NULL, 89000, NULL, NULL, NULL, 'belum_bayar', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(38, 1, 38, 38, '2026-04-29 04:07:02', '2026-05-04 04:07:02', 'Perjalanan dinas ke kota 38', 4, 5, 0, 1085000, 164000, 1249000, 'transfer', 'dp', 624500, 'Driver 38', NULL, NULL, NULL, NULL, NULL, 'dp', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(39, 1, 39, 39, '2026-04-05 04:07:02', '2026-04-15 04:07:02', 'Perjalanan dinas ke kota 39', 5, 10, 0, 3480000, 419000, 3899000, 'tunai', 'lunas', NULL, NULL, '08632311287', NULL, NULL, NULL, NULL, 'lunas', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(40, 1, 40, 40, '2026-04-05 04:07:02', '2026-04-18 04:07:02', 'Perjalanan dinas ke kota 40', 7, 13, 0, 2743000, 152000, 2895000, 'transfer', 'dp', 1447500, 'Driver 40', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(41, 1, 41, 41, '2026-04-09 04:07:02', '2026-04-12 04:07:02', 'Perjalanan dinas ke kota 41', 2, 3, 0, 786000, 473000, 1259000, 'tunai', 'lunas', NULL, 'Driver 41', NULL, NULL, NULL, NULL, NULL, 'dp', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(42, 1, 42, 42, '2026-04-10 04:07:02', '2026-04-11 04:07:02', 'Perjalanan dinas ke kota 42', 1, 1, 0, 325000, 163000, 488000, 'transfer', 'dp', 244000, 'Driver 42', '08241389657', NULL, NULL, NULL, NULL, 'lunas', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(43, 1, 43, 43, '2026-01-18 04:07:02', '2026-01-23 04:07:02', 'Perjalanan dinas ke kota 43', 7, 5, 0, 2450000, 454000, 2904000, 'tunai', 'lunas', NULL, 'Driver 43', NULL, NULL, NULL, NULL, NULL, 'belum_bayar', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(44, 1, 44, 44, '2026-01-19 04:07:02', '2026-01-26 04:07:02', 'Perjalanan dinas ke kota 44', 5, 7, 0, 4179000, 239000, 4418000, 'transfer', 'dp', 2209000, NULL, '08115699052', 132000, NULL, NULL, NULL, 'dp', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(45, 1, 45, 45, '2026-03-26 04:07:02', '2026-04-05 04:07:02', 'Perjalanan dinas ke kota 45', 0, 10, 0, 2400000, 391000, 2791000, 'tunai', 'lunas', NULL, NULL, NULL, 61000, NULL, NULL, NULL, 'lunas', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(46, 1, 46, 46, '2026-06-26 04:07:02', '2026-07-08 04:07:02', 'Perjalanan dinas ke kota 46', 2, 12, 0, 5208000, 253000, 5461000, 'transfer', 'dp', 2730500, NULL, '08677471894', NULL, NULL, NULL, NULL, 'belum_bayar', 'Pending', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(47, 1, 47, 47, '2026-02-16 04:07:02', '2026-03-01 04:07:02', 'Perjalanan dinas ke kota 47', 2, 13, 0, 3900000, 321000, 4221000, 'tunai', 'lunas', NULL, 'Driver 47', '08515505044', 70000, NULL, NULL, NULL, 'dp', 'booking', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(48, 1, 48, 48, '2026-01-25 04:07:02', '2026-01-27 04:07:02', 'Perjalanan dinas ke kota 48', 6, 2, 0, 760000, 92000, 852000, 'transfer', 'dp', 426000, NULL, '08711016657', 63000, NULL, NULL, NULL, 'lunas', 'aktif', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(49, 1, 49, 49, '2026-06-12 04:07:02', '2026-06-25 04:07:02', 'Perjalanan dinas ke kota 49', 2, 13, 0, 5369000, 470000, 5839000, 'tunai', 'lunas', NULL, NULL, '08579486384', NULL, NULL, NULL, NULL, 'belum_bayar', 'selesai', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL),
	(50, 1, 50, 50, '2026-05-15 04:07:02', '2026-05-28 04:07:02', 'Perjalanan dinas ke kota 50', 1, 13, 0, 6747000, 438000, 7185000, 'transfer', 'dp', 3592500, NULL, NULL, 69000, NULL, NULL, NULL, 'dp', 'batal', '2026-07-11 21:07:02', '2026-07-11 21:07:02', 0, NULL, NULL, NULL, NULL);

-- Dumping structure for table apyrent.rental_biaya_tambahan
CREATE TABLE IF NOT EXISTS `rental_biaya_tambahan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rental_id` bigint unsigned NOT NULL,
  `biaya_tambahan_id` bigint unsigned NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `subtotal` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rental_biaya_tambahan_rental_id_foreign` (`rental_id`),
  KEY `rental_biaya_tambahan_biaya_tambahan_id_foreign` (`biaya_tambahan_id`),
  CONSTRAINT `rental_biaya_tambahan_biaya_tambahan_id_foreign` FOREIGN KEY (`biaya_tambahan_id`) REFERENCES `biaya_tambahans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rental_biaya_tambahan_rental_id_foreign` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.rental_biaya_tambahan: ~0 rows (approximately)

-- Dumping structure for table apyrent.requestfor_quotations
CREATE TABLE IF NOT EXISTS `requestfor_quotations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rfq` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_rfq` date NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kuantitas` int NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_estimasi` bigint NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `status_rfq` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.requestfor_quotations: ~30 rows (approximately)
INSERT INTO `requestfor_quotations` (`id`, `id_rfq`, `tanggal_rfq`, `vendor`, `kode_barang`, `nama_barang`, `kuantitas`, `satuan`, `harga_estimasi`, `tanggal_kirim`, `status_rfq`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'RFQ-001', '2026-05-27', 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 244, 'pcs', 189065, '2026-06-08', 'Open', 'Catatan RFQ ke-1', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'RFQ-002', '2026-01-18', 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 408, 'liter', 615968, '2026-02-05', 'Sent', 'Catatan RFQ ke-2', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(3, 'RFQ-003', '2026-01-27', 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 125, 'unit', 958181, '2026-02-23', 'Closed', 'Catatan RFQ ke-3', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(4, 'RFQ-004', '2026-02-25', 'UD Sejahtera', 'BRG-004', 'Filter Udara', 184, 'set', 435702, '2026-03-19', 'Open', 'Catatan RFQ ke-4', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(5, 'RFQ-005', '2026-03-14', 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 346, 'buah', 1942885, '2026-03-22', 'Sent', 'Catatan RFQ ke-5', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(6, 'RFQ-006', '2026-07-09', 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 161, 'dus', 1359557, '2026-07-31', 'Closed', 'Catatan RFQ ke-6', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(7, 'RFQ-007', '2026-06-02', 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 394, 'kg', 1630540, '2026-06-21', 'Open', 'Catatan RFQ ke-7', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(8, 'RFQ-008', '2026-04-12', 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 454, 'pcs', 914704, '2026-05-06', 'Sent', 'Catatan RFQ ke-8', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(9, 'RFQ-009', '2026-02-09', 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 372, 'liter', 1796255, '2026-03-01', 'Closed', 'Catatan RFQ ke-9', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(10, 'RFQ-010', '2026-03-15', 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 401, 'unit', 771738, '2026-04-03', 'Open', 'Catatan RFQ ke-10', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(11, 'RFQ-011', '2026-04-17', 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 44, 'set', 155067, '2026-05-07', 'Sent', 'Catatan RFQ ke-11', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(12, 'RFQ-012', '2026-06-20', 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 61, 'buah', 393545, '2026-07-20', 'Closed', 'Catatan RFQ ke-12', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(13, 'RFQ-013', '2026-02-12', 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 31, 'dus', 1521278, '2026-03-13', 'Open', 'Catatan RFQ ke-13', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(14, 'RFQ-014', '2026-06-02', 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 223, 'kg', 245435, '2026-06-09', 'Sent', 'Catatan RFQ ke-14', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(15, 'RFQ-015', '2026-02-01', 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 311, 'pcs', 271246, '2026-02-26', 'Closed', 'Catatan RFQ ke-15', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(16, 'RFQ-016', '2026-07-11', 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 281, 'liter', 1313988, '2026-08-01', 'Open', 'Catatan RFQ ke-16', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(17, 'RFQ-017', '2026-02-18', 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 286, 'unit', 83187, '2026-03-05', 'Sent', 'Catatan RFQ ke-17', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(18, 'RFQ-018', '2026-03-24', 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 436, 'set', 1668793, '2026-04-23', 'Closed', 'Catatan RFQ ke-18', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(19, 'RFQ-019', '2026-02-04', 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 410, 'buah', 1052872, '2026-03-06', 'Open', 'Catatan RFQ ke-19', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(20, 'RFQ-020', '2026-01-19', 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 206, 'dus', 1231796, '2026-02-05', 'Sent', 'Catatan RFQ ke-20', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(21, 'RFQ-021', '2026-05-14', 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 420, 'kg', 317388, '2026-06-08', 'Closed', 'Catatan RFQ ke-21', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(22, 'RFQ-022', '2026-05-29', 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 365, 'pcs', 1412375, '2026-06-21', 'Open', 'Catatan RFQ ke-22', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(23, 'RFQ-023', '2026-02-02', 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 432, 'liter', 1557682, '2026-02-22', 'Sent', 'Catatan RFQ ke-23', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(24, 'RFQ-024', '2026-05-02', 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 351, 'unit', 175999, '2026-05-22', 'Closed', 'Catatan RFQ ke-24', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(25, 'RFQ-025', '2026-02-26', 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 360, 'set', 153192, '2026-03-09', 'Open', 'Catatan RFQ ke-25', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(26, 'RFQ-026', '2026-05-08', 'PT Maju Jaya', 'BRG-002', 'Oli Mesin 10W-40', 61, 'buah', 282848, '2026-05-19', 'Sent', 'Catatan RFQ ke-26', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(27, 'RFQ-027', '2026-05-16', 'CV Berkah Abadi', 'BRG-003', 'Ban Kendaraan', 56, 'dus', 1029366, '2026-06-03', 'Closed', 'Catatan RFQ ke-27', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(28, 'RFQ-028', '2026-06-10', 'PT Sumber Makmur', 'BRG-004', 'Filter Udara', 231, 'kg', 1338782, '2026-06-26', 'Open', 'Catatan RFQ ke-28', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(29, 'RFQ-029', '2026-01-26', 'UD Sejahtera', 'BRG-005', 'Aki Kendaraan', 181, 'pcs', 1599738, '2026-02-19', 'Sent', 'Catatan RFQ ke-29', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(30, 'RFQ-030', '2026-02-19', 'PT Indo Supplier', 'BRG-006', 'Kampas Rem', 432, 'liter', 1194627, '2026-03-21', 'Closed', 'Catatan RFQ ke-30', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.resign_offboardings
CREATE TABLE IF NOT EXISTS `resign_offboardings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_resign` date NOT NULL,
  `alasan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_offboarding` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serah_terima` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.resign_offboardings: ~12 rows (approximately)
INSERT INTO `resign_offboardings` (`id`, `nama_pegawai`, `jabatan_terakhir`, `tanggal_resign`, `alasan`, `status_offboarding`, `serah_terima`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'Ahmad Rifai', 'Staff Gudang', '2024-02-28', 'Mengundurkan diri', 'Selesai', 'Sudah', 'Sudah menyelesaikan serah terima aset dan dokumen', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(2, 'Maya Sari', 'Staff Marketing', '2024-04-15', 'Pindah domisili', 'Selesai', 'Sudah', 'Proses offboarding berjalan lancar', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(3, 'Dika Pratama', 'Developer Junior', '2024-06-01', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Akses sistem telah dicabut', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(4, 'Sari Utami', 'Staff Finance', '2024-07-31', 'Melanjutkan studi', 'Selesai', 'Sudah', 'Dokumen exit interview selesai', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(5, 'Bowo Setiawan', 'Teknisi Lapangan', '2024-09-30', 'Kesehatan', 'Selesai', 'Sudah', 'Serah terima peralatan sudah dilakukan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(6, 'Nita Lestari', 'Admin HR', '2024-11-15', 'Menikah dan pindah kota', 'Selesai', 'Sudah', 'Semua kewajiban telah diselesaikan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(7, 'Reza Aditya', 'IT Support', '2025-01-31', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah', 'Credential akun sudah dinonaktifkan', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(8, 'Putri Anggraini', 'Staff Operasional', '2025-03-15', 'Alasan keluarga', 'Proses', 'Belum', 'Sedang dalam proses serah terima', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(9, 'Galih Santoso', 'Supervisor Produksi', '2025-05-30', 'Pensiun dini', 'Proses', 'Belum', 'Menunggu pengganti untuk serah terima', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(10, 'Lina Permatasari', 'Staff Akuntansi', '2025-06-30', 'Wirausaha', 'Proses', 'Belum', 'Dalam proses dokumentasi offboarding', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(11, 'Bagas Wicaksono', 'Driver', '2025-07-01', 'Kontrak tidak diperpanjang', 'Proses', 'Belum', 'Mengembalikan kendaraan dinas', '2026-07-11 21:07:07', '2026-07-11 21:07:07'),
	(12, 'Rina Kurniawati', 'Customer Service', '2026-01-31', 'Mengurus anak', 'Proses', 'Belum', 'Exit interview sudah dilakukan', '2026-07-11 21:07:07', '2026-07-11 21:07:07');

-- Dumping structure for table apyrent.retur_penjualans
CREATE TABLE IF NOT EXISTS `retur_penjualans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_retur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `no_order` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `alasan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_retur` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `retur_penjualans_no_retur_unique` (`no_retur`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.retur_penjualans: ~8 rows (approximately)
INSERT INTO `retur_penjualans` (`id`, `no_retur`, `tanggal`, `no_order`, `pelanggan`, `produk`, `qty`, `alasan`, `nilai_retur`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'RTR-001', '2026-02-10', 'SO-2026-001', 'PT Maju Bersama', 'Sewa Minibus', 1, 'Unit mengalami kerusakan', 5000000.00, 'Selesai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'RTR-002', '2026-02-20', 'SO-2026-002', 'PT Global Trans', 'Sewa Bus', 1, 'Spesifikasi tidak sesuai', 15000000.00, 'Diproses', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'RTR-003', '2026-03-05', 'SO-2026-003', 'CV Karya Indah', 'Sewa Truk', 1, 'Truk bermasalah di tengah jalan', 8000000.00, 'Selesai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'RTR-004', '2026-03-18', 'SO-2026-004', 'PT Nusantara Raya', 'Sewa Minibus', 1, 'AC tidak berfungsi', 5500000.00, 'Menunggu', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'RTR-005', '2026-04-02', 'SO-2026-006', 'PT Berlian Trans', 'Sewa Bus', 1, 'Pembatalan sebagian order', 10000000.00, 'Selesai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'RTR-006', '2026-04-15', 'SO-2026-005', 'CV Jaya Mandiri', 'Sewa MPV', 2, 'Unit terlambat pengiriman', 8000000.00, 'Diproses', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'RTR-007', '2026-05-01', 'SO-2026-008', 'PT Sejahtera Abadi', 'Sewa Sedan', 1, 'Kendaraan tidak sesuai pesanan', 3500000.00, 'Menunggu', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'RTR-008', '2026-05-15', 'SO-2026-009', 'PT Prima Raya', 'Sewa SUV', 1, 'Kondisi kendaraan buruk', 6000000.00, 'Selesai', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.review_legals
CREATE TABLE IF NOT EXISTS `review_legals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `pemohon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_review` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic_legal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.review_legals: ~0 rows (approximately)

-- Dumping structure for table apyrent.sales_orders
CREATE TABLE IF NOT EXISTS `sales_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_jasa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `status_order` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_orders_order_no_unique` (`order_no`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.sales_orders: ~10 rows (approximately)
INSERT INTO `sales_orders` (`id`, `order_no`, `tanggal`, `pelanggan`, `produk_jasa`, `qty`, `total_harga`, `status_order`, `metode_pembayaran`, `sales`, `created_at`, `updated_at`) VALUES
	(1, 'SO-2026-001', '2026-01-20', 'PT Maju Bersama', 'Sewa Minibus 2 Unit', 2, 10000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'SO-2026-002', '2026-02-05', 'PT Global Trans', 'Sewa Bus Besar', 1, 15000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'SO-2026-003', '2026-02-18', 'CV Karya Indah', 'Sewa Truk', 1, 8000000.00, 'Diproses', 'Tempo', 'Cici', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'SO-2026-004', '2026-03-01', 'PT Nusantara Raya', 'Sewa Minibus', 2, 11000000.00, 'Selesai', 'Kredit', 'Dani', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'SO-2026-005', '2026-03-10', 'CV Jaya Mandiri', 'Sewa MPV 4 Unit', 4, 16000000.00, 'Diproses', 'Transfer Bank', 'Andi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'SO-2026-006', '2026-03-25', 'PT Berlian Trans', 'Sewa Bus Medium 2 Unit', 2, 20000000.00, 'Selesai', 'Transfer Bank', 'Budi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'SO-2026-007', '2026-04-05', 'CV Mitra Logistik', 'Sewa Truk 3 Unit', 3, 22500000.00, 'Dibatalkan', 'Transfer Bank', 'Cici', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'SO-2026-008', '2026-04-20', 'PT Sejahtera Abadi', 'Sewa Sedan', 3, 10500000.00, 'Diproses', 'Cash', 'Dani', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'SO-2026-009', '2026-05-08', 'PT Prima Raya', 'Sewa SUV', 2, 12000000.00, 'Selesai', 'Transfer Bank', 'Andi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'SO-2026-010', '2026-05-20', 'PT Sinar Harapan', 'Sewa Minibus', 1, 5500000.00, 'Diproses', 'Tempo', 'Budi', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.segmentasis
CREATE TABLE IF NOT EXISTS `segmentasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `segment_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `segment_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `segmentation_criteria` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_count` int NOT NULL DEFAULT '0',
  `campaign_goal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `segmentasis_segment_code_unique` (`segment_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.segmentasis: ~2 rows (approximately)
INSERT INTO `segmentasis` (`id`, `segment_code`, `segment_name`, `segmentation_criteria`, `customer_count`, `campaign_goal`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'SEG001', 'Corporate Client', 'Perusahaan dengan kontrak bulanan', 15, 'Retain & Upsell', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'SEG002', 'Individual Frequent', 'Individu rental >3x dalam 6 bulan', 48, 'Loyalty Program', 'Aktif', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.sertifikasi_perizinans
CREATE TABLE IF NOT EXISTS `sertifikasi_perizinans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instansi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `berlaku_hingga` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.sertifikasi_perizinans: ~0 rows (approximately)

-- Dumping structure for table apyrent.server_clouds
CREATE TABLE IF NOT EXISTS `server_clouds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_server` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_server` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `os` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_cloud` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `backup_aktif` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.server_clouds: ~5 rows (approximately)
INSERT INTO `server_clouds` (`id`, `nama_server`, `jenis_server`, `lokasi`, `os`, `provider_cloud`, `status`, `backup_aktif`, `created_at`, `updated_at`) VALUES
	(1, 'web-server-01', 'Cloud', 'AWS ap-southeast-1', 'Ubuntu 22.04 LTS', 'AWS', 'Aktif', 1, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'db-server-01', 'Cloud', 'AWS ap-southeast-1', 'Amazon Linux 2', 'AWS', 'Aktif', 1, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'file-server-local', 'Physical', 'Data Center Cibitung', 'Windows Server 2022', NULL, 'Aktif', 1, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'dev-server-01', 'VPS', 'Niagahoster VPS', 'Ubuntu 20.04 LTS', 'Niagahoster', 'Aktif', 0, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'backup-server-01', 'Physical', 'Ruang Server Jakarta', 'CentOS 7', NULL, 'Maintenance', 0, '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.service
CREATE TABLE IF NOT EXISTS `service` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nama_service` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biaya_default` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_user_id_foreign` (`user_id`),
  CONSTRAINT `service_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.service: ~10 rows (approximately)
INSERT INTO `service` (`id`, `user_id`, `nama_service`, `biaya_default`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Ganti Oli Mesin', 350000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 1, 'Tune Up', 500000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 1, 'Spooring Balancing', 250000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 1, 'Ganti Kampas Rem', 450000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 1, 'Service AC', 600000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(6, 1, 'Ganti Ban', 900000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(7, 1, 'Overhaul Mesin', 4500000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(8, 1, 'Cuci Mobil Premium', 75000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, 1, 'Ganti Aki', 1200000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, 1, 'Perbaikan Suspensi', 1800000, '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.service_detail
CREATE TABLE IF NOT EXISTS `service_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `tanggal_service` date NOT NULL,
  `kilometer` int NOT NULL DEFAULT '0',
  `status` enum('Layak','Tidak Layak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Layak',
  `biaya` bigint NOT NULL DEFAULT '0',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_detail_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `service_detail_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.service_detail: ~50 rows (approximately)
INSERT INTO `service_detail` (`id`, `kendaraan_id`, `tanggal_service`, `kilometer`, `status`, `biaya`, `keterangan`, `bukti`, `created_at`, `updated_at`) VALUES
	(1, 1, '2026-06-03', 79948, 'Layak', 700000, 'Ganti oli mesin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 2, '2026-06-17', 47150, 'Layak', 600000, 'Tune up', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 3, '2025-09-18', 38277, 'Layak', 1250000, 'Ganti kampas rem', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 4, '2026-04-24', 49404, 'Tidak Layak', 1200000, 'Servis AC', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 5, '2026-01-03', 116407, 'Layak', 300000, 'Ganti ban', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(6, 6, '2026-06-14', 101045, 'Layak', 150000, 'Overhaul mesin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(7, 7, '2026-03-25', 46781, 'Layak', 350000, 'Ganti aki', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(8, 8, '2025-10-10', 54389, 'Tidak Layak', 650000, 'Servis transmisi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(9, 9, '2025-10-29', 27628, 'Layak', 650000, 'Ganti filter udara', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(10, 10, '2026-01-29', 118707, 'Layak', 1200000, 'Perbaikan body', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(11, 11, '2026-03-29', 111051, 'Layak', 1050000, 'Ganti busi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(12, 12, '2025-12-23', 88969, 'Tidak Layak', 900000, 'Servis suspensi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(13, 13, '2026-06-22', 114225, 'Layak', 1200000, 'Ganti timing belt', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(14, 14, '2025-09-26', 88775, 'Layak', 600000, 'Kalibrasi lampu', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(15, 15, '2026-01-17', 39749, 'Layak', 400000, 'Servis power steering', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(16, 16, '2025-08-11', 115157, 'Layak', 400000, 'Ganti knalpot', NULL, '2026-07-11 21:07:02', '2026-07-13 08:14:15'),
	(17, 17, '2025-10-20', 32084, 'Layak', 1450000, 'Perbaikan sistem pendingin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(18, 18, '2025-12-08', 80233, 'Layak', 350000, 'Ganti kopling', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(19, 19, '2026-03-25', 19056, 'Layak', 400000, 'Servis rem tangan', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(20, 20, '2025-08-25', 104711, 'Tidak Layak', 400000, 'Ganti wiper', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(21, 21, '2025-11-19', 93440, 'Layak', 550000, 'Ganti oli mesin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(22, 22, '2025-08-23', 20645, 'Layak', 1500000, 'Tune up', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(23, 23, '2026-06-07', 103456, 'Layak', 1050000, 'Ganti kampas rem', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(24, 24, '2025-11-21', 97103, 'Tidak Layak', 300000, 'Servis AC', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(25, 25, '2026-01-08', 118425, 'Layak', 650000, 'Ganti ban', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(26, 26, '2026-01-07', 94471, 'Layak', 600000, 'Overhaul mesin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(27, 27, '2026-01-15', 51763, 'Layak', 550000, 'Ganti aki', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(28, 28, '2025-11-24', 101642, 'Layak', 200000, 'Servis transmisi', NULL, '2026-07-11 21:07:02', '2026-07-13 08:13:02'),
	(29, 29, '2025-12-02', 118653, 'Layak', 1250000, 'Ganti filter udara', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(30, 30, '2025-08-02', 118245, 'Layak', 350000, 'Perbaikan body', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(31, 31, '2025-10-21', 113036, 'Layak', 200000, 'Ganti busi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(32, 32, '2025-07-20', 16462, 'Tidak Layak', 1500000, 'Servis suspensi', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(33, 33, '2026-01-29', 65647, 'Layak', 550000, 'Ganti timing belt', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(34, 34, '2025-11-21', 87999, 'Layak', 900000, 'Kalibrasi lampu', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(35, 35, '2026-04-09', 110687, 'Layak', 750000, 'Servis power steering', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(36, 36, '2025-10-25', 17317, 'Tidak Layak', 100000, 'Ganti knalpot', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(37, 37, '2025-12-16', 78497, 'Layak', 1450000, 'Perbaikan sistem pendingin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(38, 38, '2025-11-26', 45462, 'Layak', 1200000, 'Ganti kopling', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(39, 39, '2025-10-22', 22084, 'Layak', 300000, 'Servis rem tangan', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(40, 40, '2025-10-26', 81679, 'Tidak Layak', 500000, 'Ganti wiper', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(41, 41, '2025-08-02', 93087, 'Layak', 400000, 'Ganti oli mesin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(42, 42, '2025-12-21', 11756, 'Layak', 900000, 'Tune up', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(43, 43, '2026-06-03', 43047, 'Layak', 200000, 'Ganti kampas rem', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(44, 44, '2026-03-19', 11803, 'Tidak Layak', 1100000, 'Servis AC', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(45, 45, '2025-12-23', 37914, 'Layak', 950000, 'Ganti ban', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(46, 46, '2025-10-28', 94145, 'Layak', 700000, 'Overhaul mesin', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(47, 47, '2025-10-12', 27629, 'Layak', 150000, 'Ganti aki', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(48, 48, '2025-08-29', 19605, 'Layak', 700000, 'Servis transmisi', NULL, '2026-07-11 21:07:02', '2026-07-11 22:30:57'),
	(49, 49, '2026-04-15', 58298, 'Layak', 1500000, 'Ganti filter udara', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(50, 50, '2026-01-24', 33283, 'Layak', 350000, 'Perbaikan body', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(51, 48, '2026-07-12', 788786, 'Layak', 999, 'KOKO', NULL, '2026-07-11 22:16:38', '2026-07-11 22:30:57'),
	(52, 48, '2026-07-12', 99889, 'Layak', 1000, 'sok', NULL, '2026-07-11 22:29:33', '2026-07-11 22:30:57'),
	(53, 48, '2026-07-06', 899999, 'Layak', 8999, 'iinni', NULL, '2026-07-11 22:37:30', '2026-07-13 09:06:24'),
	(54, 26, '2026-07-12', 0, 'Tidak Layak', 0, '[Reminder: Ganti Ban] Kae', NULL, '2026-07-11 23:28:10', '2026-07-11 23:28:10'),
	(55, 18, '2026-07-12', 0, 'Layak', 0, '[Reminder: Kaca] Kacang', NULL, '2026-07-12 00:21:02', '2026-07-12 00:22:33'),
	(56, 1, '2026-07-13', 0, 'Tidak Layak', 0, '[Reminder: Ganti Velg] Kae', NULL, '2026-07-13 07:51:56', '2026-07-13 07:51:56'),
	(57, 1, '2026-07-13', 0, 'Tidak Layak', 0, '[Reminder: Ganti Ban] Kae', NULL, '2026-07-13 07:52:29', '2026-07-13 07:52:29'),
	(58, 18, '2026-07-14', 0, 'Tidak Layak', 0, '[Reminder: Ganti Ban] Kae', NULL, '2026-07-14 11:37:45', '2026-07-14 11:37:45'),
	(59, 39, '2026-07-14', 0, 'Tidak Layak', 0, '[Reminder: Ganti Velg] Kae', NULL, '2026-07-14 11:41:33', '2026-07-14 11:41:33');

-- Dumping structure for table apyrent.service_history
CREATE TABLE IF NOT EXISTS `service_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `keluhan` text COLLATE utf8mb4_unicode_ci,
  `kilometer` int NOT NULL DEFAULT '0',
  `total_biaya` bigint NOT NULL DEFAULT '0',
  `status` enum('proses','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'proses',
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maks_bulanan` bigint NOT NULL DEFAULT '0',
  `biaya_tahunan` bigint NOT NULL DEFAULT '0',
  `status_pengeluaran` enum('stabil','overservice') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stabil',
  `tanggal_service` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sisa_limit` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_history_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `service_history_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.service_history: ~10 rows (approximately)
INSERT INTO `service_history` (`id`, `kendaraan_id`, `keluhan`, `kilometer`, `total_biaya`, `status`, `bukti_pembayaran`, `maks_bulanan`, `biaya_tahunan`, `status_pengeluaran`, `tanggal_service`, `created_at`, `updated_at`, `sisa_limit`) VALUES
	(1, 1, 'Oli mesin sudah hitam dan rem berbunyi', 45000, 850000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-22', '2026-07-11 21:07:02', '2026-07-11 21:07:02', NULL),
	(2, 2, 'AC tidak dingin', 52000, 600000, 'proses', NULL, 0, 0, 'stabil', '2026-07-02', '2026-07-11 21:07:02', '2026-07-11 21:07:02', NULL),
	(3, 3, 'Ban depan aus', 70000, 1800000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-12', '2026-07-11 21:07:02', '2026-07-11 21:07:02', NULL),
	(4, 1, 'Mesin getar saat idle', 47000, 500000, 'selesai', NULL, 0, 0, 'stabil', '2026-07-07', '2026-07-11 21:07:02', '2026-07-13 08:31:17', NULL),
	(5, 2, 'Ganti aki', 55000, 1200000, 'selesai', NULL, 0, 0, 'stabil', '2026-06-27', '2026-07-11 21:07:02', '2026-07-11 21:07:02', NULL),
	(6, 48, 'KOKO, Servis transmisi', 788786, 700999, 'selesai', 'bukti_pembayaran/1783833465_Daftar-Penawaran-2026-07-05 (2).pdf', 6, 700999, 'overservice', '2026-07-12', '2026-07-11 22:17:45', '2026-07-11 22:17:56', -700993),
	(7, 48, 'KOKO, sok, Servis transmisi', 788786, 701999, 'selesai', 'bukti_pembayaran/1783834257_Summary-2026-07-05 (1).pdf', 6, 1402998, 'overservice', '2026-07-12', '2026-07-11 22:30:57', '2026-07-11 22:31:15', -1402992),
	(8, 18, '[Reminder: Kaca] Kacang', 19000, 90000, 'selesai', 'bukti_pembayaran/1783840953_icon.png', 6, 90000, 'overservice', '2026-07-12', '2026-07-12 00:22:33', '2026-07-12 00:22:51', -89994),
	(9, 28, 'Servis transmisi, karo servis nganu', 101642, 2889999, 'selesai', 'bukti_pembayaran/1783955582_Invoice-INV-202607-0002 (3).pdf', 6, 2889999, 'overservice', '2026-07-12', '2026-07-13 08:13:02', '2026-07-13 08:23:57', -2889993),
	(10, 16, 'Ganti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpotGanti knalpot', 115157, 400000, 'selesai', 'bukti_pembayaran/1783955655_Invoice-INV-202607-0002 (1).pdf', 6, 400000, 'overservice', '2026-07-23', '2026-07-13 08:14:15', '2026-07-13 08:15:59', -399994),
	(11, 48, 'nkkkk,,,m', 16491, 90000, 'selesai', 'bukti_pembayaran/1783958784_Screenshot_7-7-2026_142125_www.instagram.com.jpeg', 90000000, 1492998, 'stabil', '2026-07-13', '2026-07-13 09:06:24', '2026-07-13 09:08:33', 88507002);

-- Dumping structure for table apyrent.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.sessions: ~1 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('BJbIIn6iNZBWQBCeVcqCI6SUGhaTMu4O2SmYLgpe', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYzFrdWJRSnc1Rk1GRklMMnVTYTdhZ3FQN243MFk0NzJ0TFJZY29iYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZW1pbmRlci1zZXJ2aWNlIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1784029371);

-- Dumping structure for table apyrent.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_rekening` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atas_nama_rekening` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batas_reminder` int unsigned NOT NULL DEFAULT '1',
  `satuan_reminder` enum('hari','minggu','bulan','tahun') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hari',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.settings: ~0 rows (approximately)
INSERT INTO `settings` (`id`, `nama_perusahaan`, `alamat`, `telepon`, `email`, `website`, `logo`, `nama_bank`, `nomor_rekening`, `atas_nama_rekening`, `kode_pos`, `batas_reminder`, `satuan_reminder`, `created_at`, `updated_at`) VALUES
	(1, 'PT Rental Kendaraan Indonesia', 'Jl. Sudirman No. 123, Jakarta Pusat', '09876543', 'hirutotygithub@gmail.com', 'https://rentalkendaraan.co.id', 'uploads/setting/1783837585_logo_icon.png', 'Bank BCA', '1234567890', 'PT Rental Kendaraan Indonesia', '10110', 1, 'bulan', '2026-07-11 21:07:03', '2026-07-11 23:26:25');

-- Dumping structure for table apyrent.shift_lemburs
CREATE TABLE IF NOT EXISTS `shift_lemburs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `jam_lembur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_jam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.shift_lemburs: ~20 rows (approximately)
INSERT INTO `shift_lemburs` (`id`, `nama_pegawai`, `shift`, `jam_masuk`, `jam_pulang`, `jam_lembur`, `total_jam`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'Teguh Santosa', 'Pagi', '07:00:00', '15:00:00', '2', '10', 'Lembur pengiriman barang', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 'Arif Budiman', 'Pagi', '07:00:00', '15:00:00', NULL, '8', 'Shift reguler', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 'Dody Kurniawan', 'Siang', '15:00:00', '23:00:00', '1', '9', 'Lembur rapat koordinasi', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 'Rizky Fadillah', 'Pagi', '08:00:00', '17:00:00', '3', '12', 'Lembur deploy sistem', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 'Yusuf Hidayat', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler IT', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 'Hendra Gunawan', 'Pagi', '08:00:00', '17:00:00', '2', '11', 'Lembur maintenance server', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 'Wahyu Nugroho', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '1.5', '9.5', 'Lembur laporan pajak', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 'Linda Permata', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur audit internal', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 'Rini Apriani', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler HRD', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(11, 'Eko Prasetyo', 'Malam', '23:00:00', '07:00:00', '1', '9', 'Shift malam + lembur', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(12, 'Dewi Kusuma', 'Pagi', '08:00:00', '17:00:00', NULL, '8', 'Shift reguler', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(13, 'Teguh Santosa', 'Malam', '23:00:00', '07:00:00', '2', '10', 'Lembur pengawasan malam', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(14, 'Arif Budiman', 'Siang', '15:00:00', '23:00:00', NULL, '8', 'Rotasi shift siang', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(15, 'Rizky Fadillah', 'Siang', '12:00:00', '21:00:00', '2', '11', 'Lembur perbaikan bug produksi', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(16, 'Yusuf Hidayat', 'Malam', '23:00:00', '07:00:00', NULL, '8', 'Shift malam on-call', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(17, 'Wahyu Nugroho', 'Pagi', '07:30:00', '16:30:00', '1', '9', 'Lembur tutup buku', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(18, 'Dody Kurniawan', 'Pagi', '07:00:00', '16:00:00', NULL, '8', 'Shift reguler operasional', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(19, 'Fitri Handayani', 'Pagi', '08:00:00', '17:00:00', '2', '10', 'Lembur SPT tahunan', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(20, 'Hendra Gunawan', 'Siang', '12:00:00', '21:00:00', '1', '9', 'Lembur migrasi data', '2026-07-11 21:07:06', '2026-07-11 21:07:06');

-- Dumping structure for table apyrent.signature_dokumens
CREATE TABLE IF NOT EXISTS `signature_dokumens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `pihak_terlibat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_ttd` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform_digisign` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `signature_dokumens_document_id_unique` (`document_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.signature_dokumens: ~10 rows (approximately)
INSERT INTO `signature_dokumens` (`id`, `document_id`, `jenis_dokumen`, `tanggal`, `pihak_terlibat`, `status_ttd`, `platform_digisign`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'DOC-2026-001', 'Kontrak', '2026-01-20', 'PT Maju Bersama & PT APY Rent', 'Ditandatangani', 'PrivyID', 'Kontrak sewa 3 bulan', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'DOC-2026-002', 'Perjanjian', '2026-02-01', 'CV Karya Indah & PT APY Rent', 'Ditandatangani', 'DocuSign', 'PKS layanan transportasi', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'DOC-2026-003', 'MOU', '2026-02-15', 'PT Global Trans & PT APY Rent', 'Menunggu', 'PrivyID', 'Menunggu tanda tangan direktur', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'DOC-2026-004', 'Penawaran', '2026-03-01', 'PT Nusantara Raya & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Penawaran disetujui', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'DOC-2026-005', 'Kontrak', '2026-03-15', 'CV Jaya Mandiri & PT APY Rent', 'Ditolak', 'PrivyID', 'Ditolak karena klausul tidak sesuai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'DOC-2026-006', 'Perjanjian', '2026-04-01', 'PT Berlian Trans & PT APY Rent', 'Ditandatangani', 'Peruri', 'PKS jangka panjang', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'DOC-2026-007', 'Kontrak', '2026-04-20', 'PT Prima Raya & PT APY Rent', 'Menunggu', 'DocuSign', 'Dalam proses review', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'DOC-2026-008', 'MOU', '2026-05-05', 'PT Sejahtera Abadi & PT APY Rent', 'Ditandatangani', 'Manual', 'Ditandatangani secara fisik', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'DOC-2026-009', 'Lainnya', '2026-05-20', 'CV Mitra Logistik & PT APY Rent', 'Menunggu', 'PrivyID', 'Surat kuasa armada', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'DOC-2026-010', 'Kontrak', '2026-06-01', 'PT Sinar Harapan & PT APY Rent', 'Ditandatangani', 'Adobe Sign', 'Kontrak perpanjangan', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.skill_matrices
CREATE TABLE IF NOT EXISTS `skill_matrices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skill` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int unsigned NOT NULL,
  `sertifikasi` enum('Y','T') COLLATE utf8mb4_unicode_ci NOT NULL,
  `evaluator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_evaluasi` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.skill_matrices: ~14 rows (approximately)
INSERT INTO `skill_matrices` (`id`, `nama_pegawai`, `skill`, `level`, `sertifikasi`, `evaluator`, `tanggal_evaluasi`, `created_at`, `updated_at`) VALUES
	(1, 'Rizky Fadillah', 'Laravel', 5, 'Y', 'Hendra Gunawan', '2026-01-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 'Yusuf Hidayat', 'Vue.js', 3, 'T', 'Hendra Gunawan', '2025-10-14', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 'Hendra Gunawan', 'MySQL', 5, 'T', 'Hendra Gunawan', '2025-08-21', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 'Wahyu Nugroho', 'PHP', 5, 'Y', 'Hendra Gunawan', '2026-02-09', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 'Fitri Handayani', 'Microsoft Excel', 3, 'T', 'Linda Permata', '2025-12-28', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 'Linda Permata', 'Akuntansi', 3, 'T', 'Linda Permata', '2026-03-20', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 'Rini Apriani', 'Perpajakan', 5, 'Y', 'Linda Permata', '2026-03-16', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 'Eko Prasetyo', 'SAP', 4, 'T', 'Linda Permata', '2026-04-19', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 'Dewi Kusuma', 'Rekrutmen', 2, 'T', 'Dewi Kusuma', '2025-09-09', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 'Teguh Santosa', 'Payroll', 1, 'Y', 'Dewi Kusuma', '2025-12-26', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(11, 'Arif Budiman', 'K3', 2, 'T', 'Dewi Kusuma', '2026-03-23', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(12, 'Dody Kurniawan', 'Negosiasi', 5, 'T', 'Dody Kurniawan', '2026-06-12', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(13, 'Rizky Fadillah', 'AutoCAD', 5, 'Y', 'Teguh Santosa', '2025-10-04', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(14, 'Yusuf Hidayat', 'Troubleshooting', 2, 'T', 'Yusuf Hidayat', '2026-03-27', '2026-07-11 21:07:06', '2026-07-11 21:07:06');

-- Dumping structure for table apyrent.software_licenses
CREATE TABLE IF NOT EXISTS `software_licenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_software` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_lisensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_lisensi` int NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `masa_berlaku` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_perpanjangan` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.software_licenses: ~5 rows (approximately)
INSERT INTO `software_licenses` (`id`, `nama_software`, `jenis_lisensi`, `jumlah_lisensi`, `provider`, `masa_berlaku`, `status`, `tanggal_perpanjangan`, `created_at`, `updated_at`) VALUES
	(1, 'Microsoft Office 365', 'Subscription', 25, 'Microsoft', '2025-12-31', 'Aktif', '2024-12-01', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'Adobe Creative Cloud', 'Subscription', 5, 'Adobe', '2025-06-30', 'Aktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'Kaspersky Endpoint Security', 'Perpetual', 50, 'Kaspersky', '2024-03-31', 'Expired', '2024-04-01', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'Zoom Pro', 'Subscription', 10, 'Zoom', '2025-09-30', 'Aktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'AutoCAD 2024', 'Perpetual', 3, 'Autodesk', '2026-01-01', 'Aktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.sosmedps
CREATE TABLE IF NOT EXISTS `sosmedps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_kampanye` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utm_source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utm_campaign` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `klik` int NOT NULL DEFAULT '0',
  `konversi` int NOT NULL DEFAULT '0',
  `total_biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_penjualan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `roi` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sosmedps_id_kampanye_unique` (`id_kampanye`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.sosmedps: ~2 rows (approximately)
INSERT INTO `sosmedps` (`id`, `id_kampanye`, `channel`, `utm_source`, `utm_campaign`, `klik`, `konversi`, `total_biaya`, `total_penjualan`, `roi`, `created_at`, `updated_at`) VALUES
	(1, 'SMP001', 'Instagram', 'instagram', 'promo_rental_july', 320, 18, 1500000.00, 9000000.00, 500.00, '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'SMP002', 'Facebook', 'facebook', 'awareness_apyrent', 580, 25, 2000000.00, 12500000.00, 525.00, '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.stnk
CREATE TABLE IF NOT EXISTS `stnk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kendaraan_id` bigint unsigned NOT NULL,
  `nopol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pemilik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stnk_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `stnk_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.stnk: ~0 rows (approximately)

-- Dumping structure for table apyrent.stnk_histories
CREATE TABLE IF NOT EXISTS `stnk_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stnk_id` bigint unsigned NOT NULL,
  `kendaraan_id` bigint unsigned NOT NULL,
  `nopol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pemilik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `masa_berlaku` date NOT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diperpanjang_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stnk_histories_stnk_id_foreign` (`stnk_id`),
  KEY `stnk_histories_kendaraan_id_foreign` (`kendaraan_id`),
  CONSTRAINT `stnk_histories_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stnk_histories_stnk_id_foreign` FOREIGN KEY (`stnk_id`) REFERENCES `stnk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.stnk_histories: ~0 rows (approximately)

-- Dumping structure for table apyrent.struktur_organisasis
CREATE TABLE IF NOT EXISTS `struktur_organisasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `atasan_langsung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_jabatan` enum('Tetap','Kontrak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.struktur_organisasis: ~15 rows (approximately)
INSERT INTO `struktur_organisasis` (`id`, `nama_jabatan`, `nama_pegawai`, `nip_id`, `departemen`, `atasan_langsung`, `lokasi`, `status_jabatan`, `tanggal_mulai`, `created_at`, `updated_at`) VALUES
	(1, 'Direktur Utama', 'Budi Santoso', 'NIP-001', 'Direksi', NULL, 'Jakarta', 'Tetap', '2018-01-02', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(2, 'Direktur Operasional', 'Siti Rahayu', 'NIP-002', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(3, 'Direktur Keuangan', 'Agus Wibowo', 'NIP-003', 'Direksi', 'Budi Santoso', 'Jakarta', 'Tetap', '2019-03-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(4, 'Manager HRD', 'Dewi Kusuma', 'NIP-010', 'HRD', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-01-15', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(5, 'Staff HRD', 'Rini Apriani', 'NIP-011', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Tetap', '2021-04-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(6, 'Staff HRD', 'Eko Prasetyo', 'NIP-012', 'HRD', 'Dewi Kusuma', 'Jakarta', 'Kontrak', '2023-07-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(7, 'Manager IT', 'Hendra Gunawan', 'NIP-020', 'IT', 'Budi Santoso', 'Jakarta', 'Tetap', '2020-02-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(8, 'Developer', 'Rizky Fadillah', 'NIP-021', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2022-05-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(9, 'IT Support', 'Yusuf Hidayat', 'NIP-022', 'IT', 'Hendra Gunawan', 'Jakarta', 'Kontrak', '2023-01-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(10, 'Manager Finance', 'Linda Permata', 'NIP-030', 'Finance', 'Agus Wibowo', 'Jakarta', 'Tetap', '2020-06-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(11, 'Staff Accounting', 'Wahyu Nugroho', 'NIP-031', 'Finance', 'Linda Permata', 'Jakarta', 'Tetap', '2021-08-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(12, 'Staff Pajak', 'Fitri Handayani', 'NIP-032', 'Finance', 'Linda Permata', 'Jakarta', 'Kontrak', '2022-09-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(13, 'Manager Operasional', 'Dody Kurniawan', 'NIP-040', 'Operasional', 'Siti Rahayu', 'Surabaya', 'Tetap', '2019-11-01', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(14, 'Supervisor Lapangan', 'Teguh Santosa', 'NIP-041', 'Operasional', 'Dody Kurniawan', 'Surabaya', 'Tetap', '2021-01-10', '2026-07-11 21:07:06', '2026-07-11 21:07:06'),
	(15, 'Teknisi', 'Arif Budiman', 'NIP-042', 'Operasional', 'Teguh Santosa', 'Surabaya', 'Kontrak', '2023-03-15', '2026-07-11 21:07:06', '2026-07-11 21:07:06');

-- Dumping structure for table apyrent.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nama_supplier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_barang` bigint NOT NULL DEFAULT '0',
  `harga_barang` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_user_id_foreign` (`user_id`),
  CONSTRAINT `supplier_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.supplier: ~5 rows (approximately)
INSERT INTO `supplier` (`id`, `user_id`, `nama_supplier`, `no_telp`, `nama_barang`, `jumlah_barang`, `harga_barang`, `created_at`, `updated_at`) VALUES
	(1, 1, 'CV Suku Cadang Motor', '081234567890', 'Oli Mesin', 50, 75000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 1, 'PT Ban Indonesia', '082233445566', 'Ban Mobil', 20, 850000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(3, 1, 'Toko Sparepart Jaya', '081377788899', 'Aki Mobil', 15, 1200000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(4, 1, 'CV Audio Mobil', '081299988877', 'GPS Tracker', 10, 450000, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(5, 1, 'PT Diesel Utama', '082122334455', 'Filter Solar', 40, 95000, '2026-07-11 21:07:02', '2026-07-11 21:07:02');

-- Dumping structure for table apyrent.system_backups
CREATE TABLE IF NOT EXISTS `system_backups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sistem` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metode_backup` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frekuensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi_backup` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_backup` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uji_restore_terakhir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.system_backups: ~5 rows (approximately)
INSERT INTO `system_backups` (`id`, `sistem`, `metode_backup`, `frekuensi`, `lokasi_backup`, `status_backup`, `uji_restore_terakhir`, `created_at`, `updated_at`) VALUES
	(1, 'Database ERP Production', 'Full', 'Harian', 'AWS S3 Bucket', 'Aktif', '2025-01-15', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'File Server Dokumen', 'Incremental', 'Mingguan', 'NAS Lokal + Cloud', 'Aktif', '2024-12-01', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'Email Server', 'Full', 'Mingguan', 'Tape Drive', 'Aktif', '2025-02-01', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'Aplikasi HRIS', 'Differential', 'Harian', 'GCP Storage', 'Gagal', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'Website Company Profile', 'Full', 'Bulanan', 'Hosting cPanel', 'Nonaktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.target_penjualans
CREATE TABLE IF NOT EXISTS `target_penjualans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_sales` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_penjualan` decimal(15,2) NOT NULL,
  `pencapaian` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.target_penjualans: ~24 rows (approximately)
INSERT INTO `target_penjualans` (`id`, `nama_sales`, `bulan`, `target_penjualan`, `pencapaian`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'Andi', '2026-01', 48000000.00, 22000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'Budi', '2026-01', 51000000.00, 17000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'Cici', '2026-01', 40000000.00, 49000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'Dani', '2026-01', 45000000.00, 29000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'Andi', '2026-02', 25000000.00, 25000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'Budi', '2026-02', 42000000.00, 40000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'Cici', '2026-02', 59000000.00, 58000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'Dani', '2026-02', 33000000.00, 15000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'Andi', '2026-03', 35000000.00, 19000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'Budi', '2026-03', 38000000.00, 16000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(11, 'Cici', '2026-03', 24000000.00, 46000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(12, 'Dani', '2026-03', 30000000.00, 62000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(13, 'Andi', '2026-04', 25000000.00, 25000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(14, 'Budi', '2026-04', 53000000.00, 45000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(15, 'Cici', '2026-04', 40000000.00, 48000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(16, 'Dani', '2026-04', 35000000.00, 57000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(17, 'Andi', '2026-05', 54000000.00, 51000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(18, 'Budi', '2026-05', 31000000.00, 25000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(19, 'Cici', '2026-05', 27000000.00, 65000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(20, 'Dani', '2026-05', 27000000.00, 64000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(21, 'Andi', '2026-06', 34000000.00, 59000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(22, 'Budi', '2026-06', 35000000.00, 34000000.00, 'Belum mencapai target', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(23, 'Cici', '2026-06', 22000000.00, 36000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(24, 'Dani', '2026-06', 38000000.00, 47000000.00, 'Target tercapai', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.trackingutms
CREATE TABLE IF NOT EXISTS `trackingutms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_tracking` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_tujuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utm_source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utm_medium` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utm_campaign` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utm_term` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `utm_content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_klik` int NOT NULL DEFAULT '0',
  `total_konversi` int NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trackingutms_kode_tracking_unique` (`kode_tracking`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.trackingutms: ~2 rows (approximately)
INSERT INTO `trackingutms` (`id`, `kode_tracking`, `url_tujuan`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `total_klik`, `total_konversi`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'UTM001', 'https://apyrent.com/promo', 'google', 'cpc', 'rental_promo_q3', 'sewa mobil jakarta', 'text_ad_1', 450, 32, 'Aktif', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'UTM002', 'https://apyrent.com/fleet', 'email', 'newsletter', 'new_cars_2026', NULL, 'banner_top', 280, 19, 'Aktif', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('superadmin','keuangan','produksi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'produksi',
  `status` enum('aktif','blokir') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.users: ~0 rows (approximately)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `no_telp`, `foto`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Test User', 'testuser', 'test@example.com', '$2y$12$s6EDEqr9GZzJpeCN7/6WjecUUOIAZXbA7ZRkwpeGnorLqysEflEkG', '08123456789', NULL, 'superadmin', 'aktif', 'MpCp7OdBbiAzbspnwumAGX9Q09lLZR8gh3SyeE4ynCbpP6qA9Gn6ySo51bHQ', '2026-07-11 21:07:01', '2026-07-11 21:07:01');

-- Dumping structure for table apyrent.user_accesses
CREATE TABLE IF NOT EXISTS `user_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pengguna` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `divisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_akses` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sistem` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_akses` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.user_accesses: ~5 rows (approximately)
INSERT INTO `user_accesses` (`id`, `nama_pengguna`, `divisi`, `role_akses`, `sistem`, `status_akses`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'Budi Santoso', 'IT', 'Administrator', 'ERP Sistem', 'Aktif', 'Admin utama sistem', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'Sari Dewi', 'Finance', 'Read-Write', 'Accounting Software', 'Aktif', NULL, '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'Rudi Hermawan', 'HR', 'Read Only', 'HRIS', 'Aktif', 'Akses terbatas laporan', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'Dewi Cahyani', 'Sales', 'User', 'CRM', 'Nonaktif', 'Karyawan resign Desember 2024', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'Anto Nugroho', 'Operasional', 'Read-Write', 'ERP Sistem', 'Suspended', 'Akses ditangguhkan sementara', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.vendoreos
CREATE TABLE IF NOT EXISTS `vendoreos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `produk_jasa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_terakhir_order` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.vendoreos: ~50 rows (approximately)
INSERT INTO `vendoreos` (`id`, `kode_vendor`, `nama_vendor`, `kategori`, `alamat`, `pic_vendor`, `no_telp`, `produk_jasa`, `rating`, `status`, `tanggal_terakhir_order`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'VDR-001', 'CV Vendor Nusantara 1', 'Bahan Baku', 'Jl. Jakarta No. 3', 'PIC Vendor 1', '08837657500', 'Kain Katun', 5, 'Aktif', '2026-07-10', 'Catatan vendor ke-1', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(2, 'VDR-002', 'PT Vendor Nusantara 2', 'Packaging', 'Jl. Bandung No. 6', 'PIC Vendor 2', '08983046282', 'Kardus dan Label', 2, 'Aktif', '2025-11-07', 'Catatan vendor ke-2', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(3, 'VDR-003', 'CV Vendor Nusantara 3', 'Jasa IT', 'Jl. Semarang No. 9', 'PIC Vendor 3', '08562384044', 'Maintenance Sistem', 4, 'Aktif', '2026-03-17', 'Catatan vendor ke-3', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(4, 'VDR-004', 'PT Vendor Nusantara 4', 'Spare Part', 'Jl. Yogyakarta No. 12', 'PIC Vendor 4', '08745193228', 'Spare Part Kendaraan', 3, 'Aktif', '2025-09-21', 'Catatan vendor ke-4', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(5, 'VDR-005', 'CV Vendor Nusantara 5', 'Logistik', 'Jl. Surabaya No. 15', 'PIC Vendor 5', '08366272181', 'Pengiriman Barang', 4, 'Tidak Aktif', '2025-07-26', 'Catatan vendor ke-5', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(6, 'VDR-006', 'PT Vendor Nusantara 6', 'Maintenance', 'Jl. Medan No. 18', 'PIC Vendor 6', '08557182863', 'Servis Mesin', 3, 'Aktif', '2025-08-12', 'Catatan vendor ke-6', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(7, 'VDR-007', 'CV Vendor Nusantara 7', 'Cleaning', 'Jl. Makassar No. 21', 'PIC Vendor 7', '08736202241', 'Jasa Kebersihan', 5, 'Aktif', '2025-07-14', 'Catatan vendor ke-7', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(8, 'VDR-008', 'PT Vendor Nusantara 8', 'Security', 'Jl. Palembang No. 24', 'PIC Vendor 8', '08109080620', 'Jasa Keamanan', 4, 'Aktif', '2025-12-02', 'Catatan vendor ke-8', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(9, 'VDR-009', 'CV Vendor Nusantara 9', 'Percetakan', 'Jl. Malang No. 27', 'PIC Vendor 9', '08425995665', 'Cetak Dokumen', 4, 'Aktif', '2025-11-10', 'Catatan vendor ke-9', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(10, 'VDR-010', 'PT Vendor Nusantara 10', 'ATK', 'Jl. Solo No. 30', 'PIC Vendor 10', '08299733911', 'Alat Tulis Kantor', 5, 'Tidak Aktif', '2025-11-04', 'Catatan vendor ke-10', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(11, 'VDR-011', 'CV Vendor Nusantara 11', 'Bahan Baku', 'Jl. Jakarta No. 33', 'PIC Vendor 11', '08570495827', 'Cat dan Kimia', 5, 'Aktif', '2026-03-12', 'Catatan vendor ke-11', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(12, 'VDR-012', 'PT Vendor Nusantara 12', 'Packaging', 'Jl. Bandung No. 36', 'PIC Vendor 12', '08259217920', 'Komputer dan Aksesoris', 2, 'Aktif', '2026-01-05', 'Catatan vendor ke-12', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(13, 'VDR-013', 'CV Vendor Nusantara 13', 'Jasa IT', 'Jl. Semarang No. 39', 'PIC Vendor 13', '08314088539', 'Mebel Kantor', 5, 'Aktif', '2025-09-05', 'Catatan vendor ke-13', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(14, 'VDR-014', 'PT Vendor Nusantara 14', 'Spare Part', 'Jl. Yogyakarta No. 42', 'PIC Vendor 14', '08580653541', 'Genset dan Panel', 2, 'Aktif', '2026-02-09', 'Catatan vendor ke-14', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(15, 'VDR-015', 'CV Vendor Nusantara 15', 'Logistik', 'Jl. Surabaya No. 45', 'PIC Vendor 15', '08147474060', 'Seragam Karyawan', 3, 'Tidak Aktif', '2026-02-28', 'Catatan vendor ke-15', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(16, 'VDR-016', 'PT Vendor Nusantara 16', 'Maintenance', 'Jl. Medan No. 48', 'PIC Vendor 16', '08806010920', 'Kain Katun', 3, 'Aktif', '2025-12-08', 'Catatan vendor ke-16', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(17, 'VDR-017', 'CV Vendor Nusantara 17', 'Cleaning', 'Jl. Makassar No. 51', 'PIC Vendor 17', '08876744977', 'Kardus dan Label', 5, 'Aktif', '2025-08-09', 'Catatan vendor ke-17', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(18, 'VDR-018', 'PT Vendor Nusantara 18', 'Security', 'Jl. Palembang No. 54', 'PIC Vendor 18', '08915435363', 'Maintenance Sistem', 4, 'Aktif', '2026-03-17', 'Catatan vendor ke-18', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(19, 'VDR-019', 'CV Vendor Nusantara 19', 'Percetakan', 'Jl. Malang No. 57', 'PIC Vendor 19', '08809968039', 'Spare Part Kendaraan', 4, 'Aktif', '2026-03-01', 'Catatan vendor ke-19', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(20, 'VDR-020', 'PT Vendor Nusantara 20', 'ATK', 'Jl. Solo No. 60', 'PIC Vendor 20', '08540861704', 'Pengiriman Barang', 4, 'Tidak Aktif', '2026-07-09', 'Catatan vendor ke-20', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(21, 'VDR-021', 'CV Vendor Nusantara 21', 'Bahan Baku', 'Jl. Jakarta No. 63', 'PIC Vendor 21', '08450785704', 'Servis Mesin', 2, 'Aktif', '2025-08-08', 'Catatan vendor ke-21', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(22, 'VDR-022', 'PT Vendor Nusantara 22', 'Packaging', 'Jl. Bandung No. 66', 'PIC Vendor 22', '08381036446', 'Jasa Kebersihan', 3, 'Aktif', '2025-12-08', 'Catatan vendor ke-22', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(23, 'VDR-023', 'CV Vendor Nusantara 23', 'Jasa IT', 'Jl. Semarang No. 69', 'PIC Vendor 23', '08308976770', 'Jasa Keamanan', 2, 'Aktif', '2026-05-26', 'Catatan vendor ke-23', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(24, 'VDR-024', 'PT Vendor Nusantara 24', 'Spare Part', 'Jl. Yogyakarta No. 72', 'PIC Vendor 24', '08456007308', 'Cetak Dokumen', 2, 'Aktif', '2026-01-31', 'Catatan vendor ke-24', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(25, 'VDR-025', 'CV Vendor Nusantara 25', 'Logistik', 'Jl. Surabaya No. 75', 'PIC Vendor 25', '08120039398', 'Alat Tulis Kantor', 2, 'Tidak Aktif', '2026-02-01', 'Catatan vendor ke-25', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(26, 'VDR-026', 'PT Vendor Nusantara 26', 'Maintenance', 'Jl. Medan No. 78', 'PIC Vendor 26', '08190270793', 'Cat dan Kimia', 5, 'Aktif', '2026-05-02', 'Catatan vendor ke-26', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(27, 'VDR-027', 'CV Vendor Nusantara 27', 'Cleaning', 'Jl. Makassar No. 81', 'PIC Vendor 27', '08581494395', 'Komputer dan Aksesoris', 4, 'Aktif', '2025-09-10', 'Catatan vendor ke-27', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(28, 'VDR-028', 'PT Vendor Nusantara 28', 'Security', 'Jl. Palembang No. 84', 'PIC Vendor 28', '08642376420', 'Mebel Kantor', 2, 'Aktif', '2025-08-09', 'Catatan vendor ke-28', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(29, 'VDR-029', 'CV Vendor Nusantara 29', 'Percetakan', 'Jl. Malang No. 87', 'PIC Vendor 29', '08699990320', 'Genset dan Panel', 2, 'Aktif', '2026-05-28', 'Catatan vendor ke-29', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(30, 'VDR-030', 'PT Vendor Nusantara 30', 'ATK', 'Jl. Solo No. 90', 'PIC Vendor 30', '08225809193', 'Seragam Karyawan', 3, 'Tidak Aktif', '2026-06-09', 'Catatan vendor ke-30', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(31, 'VDR-031', 'CV Vendor Nusantara 31', 'Bahan Baku', 'Jl. Jakarta No. 93', 'PIC Vendor 31', '08336323531', 'Kain Katun', 4, 'Aktif', '2025-12-11', 'Catatan vendor ke-31', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(32, 'VDR-032', 'PT Vendor Nusantara 32', 'Packaging', 'Jl. Bandung No. 96', 'PIC Vendor 32', '08315454027', 'Kardus dan Label', 2, 'Aktif', '2025-09-01', 'Catatan vendor ke-32', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(33, 'VDR-033', 'CV Vendor Nusantara 33', 'Jasa IT', 'Jl. Semarang No. 99', 'PIC Vendor 33', '08882562458', 'Maintenance Sistem', 5, 'Aktif', '2025-09-05', 'Catatan vendor ke-33', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(34, 'VDR-034', 'PT Vendor Nusantara 34', 'Spare Part', 'Jl. Yogyakarta No. 102', 'PIC Vendor 34', '08368159734', 'Spare Part Kendaraan', 3, 'Aktif', '2026-04-12', 'Catatan vendor ke-34', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(35, 'VDR-035', 'CV Vendor Nusantara 35', 'Logistik', 'Jl. Surabaya No. 105', 'PIC Vendor 35', '08110091594', 'Pengiriman Barang', 4, 'Tidak Aktif', '2026-01-03', 'Catatan vendor ke-35', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(36, 'VDR-036', 'PT Vendor Nusantara 36', 'Maintenance', 'Jl. Medan No. 108', 'PIC Vendor 36', '08331955278', 'Servis Mesin', 2, 'Aktif', '2026-04-27', 'Catatan vendor ke-36', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(37, 'VDR-037', 'CV Vendor Nusantara 37', 'Cleaning', 'Jl. Makassar No. 111', 'PIC Vendor 37', '08641337022', 'Jasa Kebersihan', 2, 'Aktif', '2025-08-10', 'Catatan vendor ke-37', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(38, 'VDR-038', 'PT Vendor Nusantara 38', 'Security', 'Jl. Palembang No. 114', 'PIC Vendor 38', '08114130334', 'Jasa Keamanan', 5, 'Aktif', '2025-08-08', 'Catatan vendor ke-38', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(39, 'VDR-039', 'CV Vendor Nusantara 39', 'Percetakan', 'Jl. Malang No. 117', 'PIC Vendor 39', '08586136723', 'Cetak Dokumen', 3, 'Aktif', '2026-04-20', 'Catatan vendor ke-39', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(40, 'VDR-040', 'PT Vendor Nusantara 40', 'ATK', 'Jl. Solo No. 120', 'PIC Vendor 40', '08783342143', 'Alat Tulis Kantor', 3, 'Tidak Aktif', '2026-06-24', 'Catatan vendor ke-40', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(41, 'VDR-041', 'CV Vendor Nusantara 41', 'Bahan Baku', 'Jl. Jakarta No. 123', 'PIC Vendor 41', '08236043156', 'Cat dan Kimia', 4, 'Aktif', '2026-04-14', 'Catatan vendor ke-41', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(42, 'VDR-042', 'PT Vendor Nusantara 42', 'Packaging', 'Jl. Bandung No. 126', 'PIC Vendor 42', '08219960759', 'Komputer dan Aksesoris', 3, 'Aktif', '2025-11-27', 'Catatan vendor ke-42', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(43, 'VDR-043', 'CV Vendor Nusantara 43', 'Jasa IT', 'Jl. Semarang No. 129', 'PIC Vendor 43', '08901553061', 'Mebel Kantor', 2, 'Aktif', '2026-04-27', 'Catatan vendor ke-43', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(44, 'VDR-044', 'PT Vendor Nusantara 44', 'Spare Part', 'Jl. Yogyakarta No. 132', 'PIC Vendor 44', '08135146067', 'Genset dan Panel', 5, 'Aktif', '2026-02-19', 'Catatan vendor ke-44', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(45, 'VDR-045', 'CV Vendor Nusantara 45', 'Logistik', 'Jl. Surabaya No. 135', 'PIC Vendor 45', '08784533374', 'Seragam Karyawan', 2, 'Tidak Aktif', '2025-09-05', 'Catatan vendor ke-45', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(46, 'VDR-046', 'PT Vendor Nusantara 46', 'Maintenance', 'Jl. Medan No. 138', 'PIC Vendor 46', '08905922075', 'Kain Katun', 5, 'Aktif', '2025-08-11', 'Catatan vendor ke-46', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(47, 'VDR-047', 'CV Vendor Nusantara 47', 'Cleaning', 'Jl. Makassar No. 141', 'PIC Vendor 47', '08394848722', 'Kardus dan Label', 3, 'Aktif', '2025-11-23', 'Catatan vendor ke-47', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(48, 'VDR-048', 'PT Vendor Nusantara 48', 'Security', 'Jl. Palembang No. 144', 'PIC Vendor 48', '08694100639', 'Maintenance Sistem', 2, 'Aktif', '2025-07-29', 'Catatan vendor ke-48', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(49, 'VDR-049', 'CV Vendor Nusantara 49', 'Percetakan', 'Jl. Malang No. 147', 'PIC Vendor 49', '08223838028', 'Spare Part Kendaraan', 4, 'Aktif', '2025-11-26', 'Catatan vendor ke-49', '2026-07-11 21:07:03', '2026-07-11 21:07:03'),
	(50, 'VDR-050', 'PT Vendor Nusantara 50', 'ATK', 'Jl. Solo No. 150', 'PIC Vendor 50', '08902890879', 'Pengiriman Barang', 5, 'Tidak Aktif', '2026-01-07', 'Catatan vendor ke-50', '2026-07-11 21:07:03', '2026-07-11 21:07:03');

-- Dumping structure for table apyrent.vendor_performances
CREATE TABLE IF NOT EXISTS `vendor_performances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_order` int NOT NULL DEFAULT '0',
  `ketepatan_waktu` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'persen',
  `kualitas_barang` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'skala 1-100',
  `komplain` int NOT NULL DEFAULT '0',
  `penilaian_akhir` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'skala 1-100',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.vendor_performances: ~5 rows (approximately)
INSERT INTO `vendor_performances` (`id`, `vendor`, `total_order`, `ketepatan_waktu`, `kualitas_barang`, `komplain`, `penilaian_akhir`, `catatan`, `created_at`, `updated_at`) VALUES
	(1, 'PT Maju Jaya', 48, 91.67, 88.50, 3, 89.20, 'Vendor terpercaya, pengiriman konsisten', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(2, 'CV Berkah Abadi', 35, 74.29, 80.00, 7, 76.50, 'Perlu peningkatan ketepatan waktu pengiriman', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(3, 'PT Sumber Makmur', 60, 95.00, 92.30, 2, 93.50, 'Performa terbaik, kualitas produk sangat baik', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(4, 'UD Sejahtera', 22, 63.64, 70.00, 9, 65.80, 'Banyak komplain, perlu evaluasi ulang kontrak', '2026-07-11 21:07:05', '2026-07-11 21:07:05'),
	(5, 'PT Indo Supplier', 41, 82.93, 85.00, 5, 83.20, 'Performa stabil, harga kompetitif', '2026-07-11 21:07:05', '2026-07-11 21:07:05');

-- Dumping structure for table apyrent.vendor_pricelists
CREATE TABLE IF NOT EXISTS `vendor_pricelists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_per_unit` bigint NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT '0.00',
  `minimal_order` int NOT NULL DEFAULT '1',
  `lead_time` int NOT NULL DEFAULT '0' COMMENT 'dalam hari',
  `tanggal_berlaku` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.vendor_pricelists: ~25 rows (approximately)
INSERT INTO `vendor_pricelists` (`id`, `vendor`, `kode_barang`, `nama_barang`, `harga_per_unit`, `satuan`, `diskon`, `minimal_order`, `lead_time`, `tanggal_berlaku`, `created_at`, `updated_at`) VALUES
	(1, 'PT Maju Jaya', 'BRG-001', 'Spare Part Mesin', 1356498, 'pcs', 2.16, 11, 25, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(2, 'CV Berkah Abadi', 'BRG-002', 'Oli Mesin 10W-40', 445805, 'liter', 13.51, 50, 7, '2026-07-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(3, 'PT Sumber Makmur', 'BRG-003', 'Ban Kendaraan', 161402, 'unit', 10.96, 24, 10, '2026-05-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(4, 'UD Sejahtera', 'BRG-004', 'Filter Udara', 1127059, 'set', 17.54, 37, 10, '2026-04-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(5, 'PT Indo Supplier', 'BRG-005', 'Aki Kendaraan', 484439, 'buah', 16.99, 19, 23, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(6, 'PT Maju Jaya', 'BRG-006', 'Kampas Rem', 673112, 'pcs', 18.12, 1, 19, '2026-05-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(7, 'CV Berkah Abadi', 'BRG-007', 'Radiator Coolant', 852116, 'liter', 9.29, 22, 24, '2026-07-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(8, 'PT Sumber Makmur', 'BRG-008', 'Busi Platinum', 466098, 'unit', 4.88, 41, 14, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(9, 'UD Sejahtera', 'BRG-001', 'Spare Part Mesin', 583724, 'set', 3.23, 30, 15, '2026-07-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(10, 'PT Indo Supplier', 'BRG-002', 'Oli Mesin 10W-40', 256288, 'buah', 9.11, 31, 14, '2026-04-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(11, 'PT Maju Jaya', 'BRG-003', 'Ban Kendaraan', 971451, 'pcs', 4.12, 50, 24, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(12, 'CV Berkah Abadi', 'BRG-004', 'Filter Udara', 106185, 'liter', 3.55, 44, 20, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(13, 'PT Sumber Makmur', 'BRG-005', 'Aki Kendaraan', 653391, 'unit', 15.93, 7, 28, '2026-05-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(14, 'UD Sejahtera', 'BRG-006', 'Kampas Rem', 226266, 'set', 13.71, 31, 25, '2026-05-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(15, 'PT Indo Supplier', 'BRG-007', 'Radiator Coolant', 1423503, 'buah', 17.86, 38, 28, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(16, 'PT Maju Jaya', 'BRG-008', 'Busi Platinum', 1342138, 'pcs', 16.41, 30, 25, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(17, 'CV Berkah Abadi', 'BRG-001', 'Spare Part Mesin', 1303327, 'liter', 2.37, 48, 9, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(18, 'PT Sumber Makmur', 'BRG-002', 'Oli Mesin 10W-40', 1209252, 'unit', 12.74, 33, 22, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(19, 'UD Sejahtera', 'BRG-003', 'Ban Kendaraan', 79076, 'set', 1.62, 9, 21, '2026-04-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(20, 'PT Indo Supplier', 'BRG-004', 'Filter Udara', 1324986, 'buah', 1.51, 27, 19, '2026-05-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(21, 'PT Maju Jaya', 'BRG-005', 'Aki Kendaraan', 318227, 'pcs', 2.24, 45, 9, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(22, 'CV Berkah Abadi', 'BRG-006', 'Kampas Rem', 1382163, 'liter', 19.58, 33, 7, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(23, 'PT Sumber Makmur', 'BRG-007', 'Radiator Coolant', 410725, 'unit', 0.29, 24, 23, '2026-06-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(24, 'UD Sejahtera', 'BRG-008', 'Busi Platinum', 566147, 'set', 4.91, 28, 5, '2026-07-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04'),
	(25, 'PT Indo Supplier', 'BRG-001', 'Spare Part Mesin', 1213503, 'buah', 10.41, 44, 13, '2026-05-01', '2026-07-11 21:07:04', '2026-07-11 21:07:04');

-- Dumping structure for table apyrent.virtual_accounts
CREATE TABLE IF NOT EXISTS `virtual_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `va_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `invoice_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('Pending','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `expired_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `virtual_accounts_va_number_unique` (`va_number`),
  KEY `virtual_accounts_member_id_foreign` (`member_id`),
  CONSTRAINT `virtual_accounts_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table apyrent.virtual_accounts: ~2 rows (approximately)
INSERT INTO `virtual_accounts` (`id`, `va_number`, `member_id`, `invoice_id`, `bukti_pembayaran`, `bank`, `expected_amount`, `paid_amount`, `status`, `expired_at`, `created_at`, `updated_at`) VALUES
	(1, 'VA-82854519', 1, NULL, NULL, 'bca', 1500000.00, 0.00, 'Pending', NULL, '2026-07-11 21:07:02', '2026-07-11 21:07:02'),
	(2, 'VA-38277053', 1, NULL, NULL, 'bni', 2500000.00, 2500000.00, 'paid', NULL, '2026-07-11 21:07:03', '2026-07-11 21:07:03');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
