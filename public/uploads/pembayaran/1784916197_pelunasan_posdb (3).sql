-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jul 2026 pada 10.46
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
-- Database: `posdb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bengkel_settings`
--

CREATE TABLE `bengkel_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_bengkel` varchar(255) NOT NULL DEFAULT 'BengkelKu',
  `alamat_bengkel` text DEFAULT NULL,
  `telepon_bengkel` varchar(255) DEFAULT NULL,
  `email_bengkel` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bengkel_settings`
--

INSERT INTO `bengkel_settings` (`id`, `nama_bengkel`, `alamat_bengkel`, `telepon_bengkel`, `email_bengkel`, `logo_path`, `created_at`, `updated_at`) VALUES
(1, 'Aplikasi Penjualan', 'Ds. Lumiring, Rt03 Rw02, Kecamatan Sukoharjo, Kab Wonosobo', NULL, 'test@bengkel.com', 'logos/logo_1784194123.jpg', NULL, NULL);

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
('laravel_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:79:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:13:\"service.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"service.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"service.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"service.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"service.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"service.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:9:\"role.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:11:\"role.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:13:\"category.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:15:\"category.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:14:\"category.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:13:\"category.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"category.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:15:\"category.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:13:\"supplier.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:15:\"supplier.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:14:\"supplier.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:13:\"supplier.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:15:\"supplier.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"supplier.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:13:\"customer.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:15:\"customer.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:14:\"customer.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:13:\"customer.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:15:\"customer.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:15:\"customer.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:9:\"user.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:11:\"user.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:10:\"user.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:9:\"user.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:11:\"user.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:11:\"user.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:14:\"sparepart.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:16:\"sparepart.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:15:\"sparepart.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:14:\"sparepart.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:16:\"sparepart.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:16:\"sparepart.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:16:\"transaction.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:18:\"transaction.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:17:\"transaction.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:16:\"transaction.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:18:\"transaction.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:18:\"transaction.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:20:\"jenis-kendaraan.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:22:\"jenis-kendaraan.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:21:\"jenis-kendaraan.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:22:\"jenis-kendaraan.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:22:\"jenis-kendaraan.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:17:\"stock-handle.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:19:\"stock-handle.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:18:\"stock-handle.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:17:\"stock-handle.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:19:\"stock-handle.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:19:\"stock-handle.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:35:\"stock-handle.quick-create-sparepart\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:18:\"report.transaction\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:23:\"report.sparepart-report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:24:\"report.inventory-summary\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:15:\"report.purchase\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:19:\"purchase_order.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:21:\"purchase_order.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:20:\"purchase_order.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:19:\"purchase_order.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:21:\"purchase_order.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:21:\"purchase_order.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:24:\"purchase_order_item.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:26:\"purchase_order_item.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:25:\"purchase_order_item.store\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:24:\"purchase_order_item.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:26:\"purchase_order_item.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:26:\"purchase_order_item.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:12:\"report.stock\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:22:\"manual-book-kasir.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:22:\"manual-book-admin.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:13:\"disposal.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:15:\"disposal.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:15:\"disposal.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:15:\"disposal.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"kasir\";s:1:\"c\";s:3:\"web\";}}}', 1784879803);

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
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Mesin', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(2, 'Sistem Transmisi', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(3, 'Body dan Aksesoris', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(4, 'Oli dan Cairan', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(5, 'Ban dan Velg', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(6, 'Sistem Pendingin', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(7, 'Sistem Bahan Bakar ', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(8, 'Sistem Kelistrikan', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(9, 'Sistem Rem', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(10, 'Suspensi dan Kaki Kaki', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(11, 'Buku', '2026-07-11 09:27:59', '2026-07-11 09:27:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'hiru', '081231231231', 'toty@gmail.com', 'fdfdfdfd', '2026-07-11 09:37:39', '2026-07-11 09:37:46'),
(2, 'Hiru Toty', '000303', 'toty@gmail', 'Ds. Lumiring, Rt03 Rw02, Kecamatan Sukoharjo, Kab Wonosobo', '2026-07-11 09:51:40', '2026-07-11 09:51:40'),
(3, 'ilham', '0484848484', 'ilham@gmail', 'Ds. Lumiring, Rt03 Rw02, Kecamatan Sukoharjo, Kab Wonosobo', '2026-07-15 10:30:29', '2026-07-15 10:30:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `disposals`
--

CREATE TABLE `disposals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sparepart_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` enum('rusak','hilang','kadaluarsa') NOT NULL,
  `notes` text DEFAULT NULL,
  `disposal_date` date NOT NULL,
  `status` enum('pending','disetujui') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `disposals`
--

INSERT INTO `disposals` (`id`, `sparepart_id`, `purchase_order_item_id`, `user_id`, `quantity`, `reason`, `notes`, `disposal_date`, `status`, `created_at`, `updated_at`) VALUES
(3, 1, NULL, 1, 1, 'rusak', NULL, '2026-07-23', 'pending', '2026-07-23 01:09:23', '2026-07-23 01:09:23'),
(4, 7, NULL, 1, 2, 'rusak', NULL, '2026-07-23', 'pending', '2026-07-23 01:09:23', '2026-07-23 01:09:23');

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
-- Struktur dari tabel `jenis_kendaraans`
--

CREATE TABLE `jenis_kendaraans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jenis_kendaraans`
--

INSERT INTO `jenis_kendaraans` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'motor matic', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(2, 'motor manual', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(3, 'mobil matic', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(4, 'mobil manual', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(5, 'Buku Gambar A3+', '2026-07-11 09:35:15', '2026-07-11 09:35:26');

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
(4, '2025_05_22_042100_create_permission_tables', 1),
(5, '2025_05_22_170351_create_categories_table', 1),
(6, '2025_05_23_130042_create_suppliers_table', 1),
(7, '2025_05_25_054558_add_some_column_to_users_table', 1),
(8, '2025_06_14_034450_create_spareparts_table', 1),
(9, '2025_06_21_081805_create_customers_table', 1),
(10, '2025_07_14_030516_create_purchase_orders_table', 1),
(11, '2025_07_14_030541_create_purchase_orders_items_table', 1),
(12, '2025_07_15_063708_create_transactions', 1),
(13, '2025_07_15_063728_create_transaction_items', 1),
(14, '2025_07_16_035609_create_supplier_sparepart_stocks_table', 1),
(15, '2025_07_16_040226_create_jenis_kendaraans_table', 1),
(16, '2025_07_16_042609_create_services_table', 1),
(17, '2025_07_17_141718_add_discount_fields_to_spareparts_table', 1),
(18, '2025_07_22_063009_add_discount_to_transactions_table', 1),
(19, '2025_07_25_040934_make_selling_price_nullable_in_spareparts_table', 1),
(20, '2025_08_01_062719_add_stock_to_spareparts_table', 1),
(21, '2025_08_04_021050_make_code_part_nullable_in_spareparts_table', 1),
(22, '2025_08_04_032211_add_purchase_price_to_spareparts_table', 1),
(23, '2025_08_05_032435_add_sparepart_id_to_transaction_items_table', 1),
(24, '2025_08_05_034106_add_sold_quantity_to_purchase_orders_items_table', 1),
(25, '2025_08_05_072957_add_available_stock_to_spareparts_table', 1),
(26, '2025_08_13_021720_create_notifications_table', 1),
(27, '2025_08_15_000000_create_bengkel_settings_table', 1),
(28, '2025_08_20_000000_fix_available_stock_default', 1),
(29, '2025_08_21_000000_add_size_to_spareparts_table', 2),
(38, '2025_08_22_000000_create_stock_movements_table', 3),
(39, '2025_08_22_000001_create_disposals_table', 3),
(40, '2025_08_22_000002_add_disposal_id_to_stock_movements_table', 3),
(41, '2025_08_22_000003_add_purchase_order_item_id_to_disposals_table', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `message`, `read_at`, `data`, `created_at`, `updated_at`) VALUES
(1, 'purchase', 'App\\Models\\User', 1, 'PO #INV-001 dari Nafarudin Pamungkas (status: received) dibuat.', '2026-07-11 10:39:41', '{\"invoice_number\":\"INV-001\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-11 09:46:28', '2026-07-11 10:39:41'),
(2, 'purchase', 'App\\Models\\User', 2, 'PO #INV-001 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-001\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-11 09:46:28', '2026-07-11 09:46:28'),
(3, 'purchase', 'App\\Models\\User', 1, 'PO #559 dari Bondan Gunawan Prakosa (status: received) dibuat.', '2026-07-11 10:39:41', '{\"invoice_number\":\"559\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-11 09:47:31', '2026-07-11 10:39:41'),
(4, 'purchase', 'App\\Models\\User', 2, 'PO #559 dari Bondan Gunawan Prakosa (status: received) dibuat.', NULL, '{\"invoice_number\":\"559\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-11 09:47:31', '2026-07-11 09:47:31'),
(5, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260711-2127 untuk Hiru Toty dibuat.', '2026-07-11 10:39:41', '{\"invoice_number\":\"INV-20260711-2127\",\"customer\":\"Hiru Toty\"}', '2026-07-11 09:51:40', '2026-07-11 10:39:41'),
(6, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260711-2127 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260711-2127\",\"customer\":\"Hiru Toty\"}', '2026-07-11 09:51:40', '2026-07-11 09:51:40'),
(7, 'purchase', 'App\\Models\\User', 1, 'PO #INV-001 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-001\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-15 10:17:17', '2026-07-15 10:17:17'),
(8, 'purchase', 'App\\Models\\User', 2, 'PO #INV-001 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-001\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-15 10:17:17', '2026-07-15 10:17:17'),
(9, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260715-2327 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260715-2327\",\"customer\":\"Hiru Toty\"}', '2026-07-15 10:20:42', '2026-07-15 10:20:42'),
(10, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260715-2327 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260715-2327\",\"customer\":\"Hiru Toty\"}', '2026-07-15 10:20:42', '2026-07-15 10:20:42'),
(11, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260715-5592 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260715-5592\",\"customer\":\"Hiru Toty\"}', '2026-07-15 10:29:45', '2026-07-15 10:29:45'),
(12, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260715-5592 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260715-5592\",\"customer\":\"Hiru Toty\"}', '2026-07-15 10:29:45', '2026-07-15 10:29:45'),
(13, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260715-1903 untuk ilham dibuat.', NULL, '{\"invoice_number\":\"INV-20260715-1903\",\"customer\":\"ilham\"}', '2026-07-15 10:30:29', '2026-07-15 10:30:29'),
(14, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260715-1903 untuk ilham dibuat.', NULL, '{\"invoice_number\":\"INV-20260715-1903\",\"customer\":\"ilham\"}', '2026-07-15 10:30:29', '2026-07-15 10:30:29'),
(15, 'purchase', 'App\\Models\\User', 1, 'PO #INV-006 dari Bondan Gunawan Prakosa (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-006\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-16 00:22:20', '2026-07-16 00:22:20'),
(16, 'purchase', 'App\\Models\\User', 2, 'PO #INV-006 dari Bondan Gunawan Prakosa (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-006\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-16 00:22:20', '2026-07-16 00:22:20'),
(17, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260716-2867 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2867\",\"customer\":\"Hiru Toty\"}', '2026-07-16 00:40:32', '2026-07-16 00:40:32'),
(18, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260716-2867 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2867\",\"customer\":\"Hiru Toty\"}', '2026-07-16 00:40:32', '2026-07-16 00:40:32'),
(19, 'stock_alert', 'App\\Models\\User', 1, 'Stok menipis/habis: Buku Gambar2 tersedia 0.', NULL, NULL, '2026-07-16 00:40:32', '2026-07-16 00:40:32'),
(20, 'stock_alert', 'App\\Models\\User', 2, 'Stok menipis/habis: Buku Gambar2 tersedia 0.', NULL, NULL, '2026-07-16 00:40:32', '2026-07-16 00:40:32'),
(21, 'purchase', 'App\\Models\\User', 1, 'PO #INV-0010 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-0010\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 00:42:59', '2026-07-16 00:42:59'),
(22, 'purchase', 'App\\Models\\User', 2, 'PO #INV-0010 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-0010\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 00:42:59', '2026-07-16 00:42:59'),
(23, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260716-7849 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-7849\",\"customer\":\"Hiru Toty\"}', '2026-07-16 00:46:10', '2026-07-16 00:46:10'),
(24, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260716-7849 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-7849\",\"customer\":\"Hiru Toty\"}', '2026-07-16 00:46:10', '2026-07-16 00:46:10'),
(25, 'stock_alert', 'App\\Models\\User', 1, 'Stok menipis/habis: Buku Gambar tersedia 0.', NULL, NULL, '2026-07-16 00:46:10', '2026-07-16 00:46:10'),
(26, 'stock_alert', 'App\\Models\\User', 2, 'Stok menipis/habis: Buku Gambar tersedia 0.', NULL, NULL, '2026-07-16 00:46:10', '2026-07-16 00:46:10'),
(27, 'purchase', 'App\\Models\\User', 1, 'PO #INV-011 dari Bondan Gunawan Prakosa (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-011\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-16 00:47:18', '2026-07-16 00:47:18'),
(28, 'purchase', 'App\\Models\\User', 2, 'PO #INV-011 dari Bondan Gunawan Prakosa (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-011\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-16 00:47:18', '2026-07-16 00:47:18'),
(29, 'purchase', 'App\\Models\\User', 1, 'PO #INV-012 dari Bondan Gunawan Prakosa (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-012\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-16 00:48:22', '2026-07-16 00:48:22'),
(30, 'purchase', 'App\\Models\\User', 2, 'PO #INV-012 dari Bondan Gunawan Prakosa (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-012\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"received\"}', '2026-07-16 00:48:22', '2026-07-16 00:48:22'),
(31, 'purchase', 'App\\Models\\User', 1, 'PO #INV-014 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-014\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 00:49:49', '2026-07-16 00:49:49'),
(32, 'purchase', 'App\\Models\\User', 2, 'PO #INV-014 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-014\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 00:49:49', '2026-07-16 00:49:49'),
(33, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260716-2345 untuk hiru dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2345\",\"customer\":\"hiru\"}', '2026-07-16 01:15:48', '2026-07-16 01:15:48'),
(34, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260716-2345 untuk hiru dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2345\",\"customer\":\"hiru\"}', '2026-07-16 01:15:48', '2026-07-16 01:15:48'),
(35, 'stock_alert', 'App\\Models\\User', 1, 'Stok menipis/habis: Buku Gambar tersedia 0.', NULL, NULL, '2026-07-16 01:15:48', '2026-07-16 01:15:48'),
(36, 'stock_alert', 'App\\Models\\User', 2, 'Stok menipis/habis: Buku Gambar tersedia 0.', NULL, NULL, '2026-07-16 01:15:48', '2026-07-16 01:15:48'),
(37, 'purchase', 'App\\Models\\User', 1, 'PO #INV-0020 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-0020\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 01:19:50', '2026-07-16 01:19:50'),
(38, 'purchase', 'App\\Models\\User', 2, 'PO #INV-0020 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-0020\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 01:19:50', '2026-07-16 01:19:50'),
(39, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260716-1075 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-1075\",\"customer\":\"Hiru Toty\"}', '2026-07-16 01:20:37', '2026-07-16 01:20:37'),
(40, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260716-1075 untuk Hiru Toty dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-1075\",\"customer\":\"Hiru Toty\"}', '2026-07-16 01:20:37', '2026-07-16 01:20:37'),
(41, 'stock_alert', 'App\\Models\\User', 1, 'Stok menipis/habis: Buku Gambar tersedia 0.', NULL, NULL, '2026-07-16 01:20:37', '2026-07-16 01:20:37'),
(42, 'stock_alert', 'App\\Models\\User', 2, 'Stok menipis/habis: Buku Gambar tersedia 0.', NULL, NULL, '2026-07-16 01:20:37', '2026-07-16 01:20:37'),
(43, 'purchase', 'App\\Models\\User', 1, 'PO #277 dari Bondan Gunawan Prakosa (status: canceled) dibuat.', NULL, '{\"invoice_number\":\"277\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"canceled\"}', '2026-07-16 01:21:14', '2026-07-16 01:21:14'),
(44, 'purchase', 'App\\Models\\User', 2, 'PO #277 dari Bondan Gunawan Prakosa (status: canceled) dibuat.', NULL, '{\"invoice_number\":\"277\",\"supplier\":\"Bondan Gunawan Prakosa\",\"status\":\"canceled\"}', '2026-07-16 01:21:14', '2026-07-16 01:21:14'),
(45, 'purchase', 'App\\Models\\User', 1, 'PO #INV-0018 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-0018\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 01:22:03', '2026-07-16 01:22:03'),
(46, 'purchase', 'App\\Models\\User', 2, 'PO #INV-0018 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-0018\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-16 01:22:03', '2026-07-16 01:22:03'),
(47, 'purchase', 'App\\Models\\User', 1, 'PO #INV-20260716-2629 dari Nafarudin Pamungkas (status: pending) dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2629\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"pending\"}', '2026-07-22 21:26:59', '2026-07-22 21:26:59'),
(48, 'purchase', 'App\\Models\\User', 2, 'PO #INV-20260716-2629 dari Nafarudin Pamungkas (status: pending) dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2629\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"pending\"}', '2026-07-22 21:26:59', '2026-07-22 21:26:59'),
(49, 'purchase', 'App\\Models\\User', 1, 'PO #INV-20260716-2628 dari Aurora Burgess (status: pending) dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2628\",\"supplier\":\"Aurora Burgess\",\"status\":\"pending\"}', '2026-07-22 22:11:35', '2026-07-22 22:11:35'),
(50, 'purchase', 'App\\Models\\User', 2, 'PO #INV-20260716-2628 dari Aurora Burgess (status: pending) dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2628\",\"supplier\":\"Aurora Burgess\",\"status\":\"pending\"}', '2026-07-22 22:11:35', '2026-07-22 22:11:35'),
(51, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260723-4958 untuk ilham dibuat.', NULL, '{\"invoice_number\":\"INV-20260723-4958\",\"customer\":\"ilham\"}', '2026-07-22 22:15:03', '2026-07-22 22:15:03'),
(52, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260723-4958 untuk ilham dibuat.', NULL, '{\"invoice_number\":\"INV-20260723-4958\",\"customer\":\"ilham\"}', '2026-07-22 22:15:03', '2026-07-22 22:15:03'),
(53, 'sale', 'App\\Models\\User', 1, 'Penjualan #INV-20260723-6624 untuk ilham dibuat.', NULL, '{\"invoice_number\":\"INV-20260723-6624\",\"customer\":\"ilham\"}', '2026-07-22 22:35:06', '2026-07-22 22:35:06'),
(54, 'sale', 'App\\Models\\User', 2, 'Penjualan #INV-20260723-6624 untuk ilham dibuat.', NULL, '{\"invoice_number\":\"INV-20260723-6624\",\"customer\":\"ilham\"}', '2026-07-22 22:35:06', '2026-07-22 22:35:06'),
(55, 'stock_alert', 'App\\Models\\User', 1, 'Stok menipis/habis: Baju Pramuka tersedia 0.', NULL, NULL, '2026-07-22 22:35:06', '2026-07-22 22:35:06'),
(56, 'stock_alert', 'App\\Models\\User', 2, 'Stok menipis/habis: Baju Pramuka tersedia 0.', NULL, NULL, '2026-07-22 22:35:06', '2026-07-22 22:35:06'),
(57, 'stock_alert', 'App\\Models\\User', 1, 'Stok menipis/habis: Buku Gambar2 tersedia 0.', NULL, NULL, '2026-07-22 22:35:06', '2026-07-22 22:35:06'),
(58, 'stock_alert', 'App\\Models\\User', 2, 'Stok menipis/habis: Buku Gambar2 tersedia 0.', NULL, NULL, '2026-07-22 22:35:06', '2026-07-22 22:35:06'),
(59, 'purchase', 'App\\Models\\User', 1, 'PO #INV-20260716-2623 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2623\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-23 01:08:46', '2026-07-23 01:08:46'),
(60, 'purchase', 'App\\Models\\User', 2, 'PO #INV-20260716-2623 dari Nafarudin Pamungkas (status: received) dibuat.', NULL, '{\"invoice_number\":\"INV-20260716-2623\",\"supplier\":\"Nafarudin Pamungkas\",\"status\":\"received\"}', '2026-07-23 01:08:46', '2026-07-23 01:08:46');

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
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'service.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(2, 'service.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(3, 'service.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(4, 'service.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(5, 'service.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(6, 'service.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(7, 'role.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(8, 'role.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(9, 'category.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(10, 'category.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(11, 'category.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(12, 'category.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(13, 'category.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(14, 'category.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(15, 'supplier.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(16, 'supplier.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(17, 'supplier.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(18, 'supplier.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(19, 'supplier.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(20, 'supplier.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(21, 'customer.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(22, 'customer.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(23, 'customer.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(24, 'customer.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(25, 'customer.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(26, 'customer.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(27, 'user.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(28, 'user.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(29, 'user.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(30, 'user.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(31, 'user.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(32, 'user.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(33, 'sparepart.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(34, 'sparepart.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(35, 'sparepart.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(36, 'sparepart.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(37, 'sparepart.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(38, 'sparepart.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(39, 'transaction.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(40, 'transaction.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(41, 'transaction.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(42, 'transaction.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(43, 'transaction.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(44, 'transaction.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(45, 'jenis-kendaraan.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(46, 'jenis-kendaraan.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(47, 'jenis-kendaraan.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(48, 'jenis-kendaraan.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(49, 'jenis-kendaraan.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(50, 'stock-handle.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(51, 'stock-handle.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(52, 'stock-handle.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(53, 'stock-handle.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(54, 'stock-handle.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(55, 'stock-handle.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(56, 'stock-handle.quick-create-sparepart', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(57, 'report.transaction', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(58, 'report.sparepart-report', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(59, 'report.inventory-summary', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(60, 'report.purchase', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(61, 'purchase_order.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(62, 'purchase_order.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(63, 'purchase_order.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(64, 'purchase_order.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(65, 'purchase_order.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(66, 'purchase_order.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(67, 'purchase_order_item.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(68, 'purchase_order_item.create', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(69, 'purchase_order_item.store', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(70, 'purchase_order_item.edit', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(71, 'purchase_order_item.update', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(72, 'purchase_order_item.delete', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(73, 'report.stock', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(74, 'manual-book-kasir.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(75, 'manual-book-admin.view', 'web', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(76, 'disposal.view', 'web', '2026-07-23 00:07:30', '2026-07-23 00:07:30'),
(77, 'disposal.create', 'web', '2026-07-23 00:07:30', '2026-07-23 00:07:30'),
(78, 'disposal.delete', 'web', '2026-07-23 00:07:30', '2026-07-23 00:07:30'),
(79, 'disposal.update', 'web', '2026-07-23 00:55:39', '2026-07-23 00:55:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `invoice_number`, `supplier_id`, `order_date`, `total_price`, `payment_method`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(2, '559', 3, '1974-01-26 17:00:00', 89960.00, 'E-Wallet', NULL, 'received', '2026-07-11 09:47:31', '2026-07-11 09:47:31'),
(3, 'INV-001', 2, '2026-07-14 17:00:00', 10000.00, 'Tunai', NULL, 'received', '2026-07-15 10:17:17', '2026-07-15 10:17:17'),
(4, 'INV-006', 3, '2026-07-15 17:00:00', 2000.00, 'Transfer Bank', NULL, 'received', '2026-07-16 00:22:20', '2026-07-16 00:47:42'),
(5, 'INV-0010', 2, '2026-07-15 17:00:00', 30000.00, 'Transfer Bank', NULL, 'received', '2026-07-16 00:42:59', '2026-07-16 00:42:59'),
(6, 'INV-011', 3, '2026-07-15 17:00:00', 1000.00, 'Transfer Bank', NULL, 'received', '2026-07-16 00:47:18', '2026-07-16 00:47:18'),
(7, 'INV-012', 3, '2026-07-15 17:00:00', 2000.00, 'Transfer Bank', NULL, 'received', '2026-07-16 00:48:22', '2026-07-16 00:48:22'),
(8, 'INV-014', 2, '2026-07-15 17:00:00', 3000.00, 'Tunai', NULL, 'received', '2026-07-16 00:49:49', '2026-07-16 00:49:49'),
(9, 'INV-0020', 2, '2026-07-15 17:00:00', 2000.00, 'Tunai', NULL, 'received', '2026-07-16 01:19:50', '2026-07-16 01:19:50'),
(10, '277', 3, '2001-08-18 17:00:00', 53104984.00, 'Kartu Debit', NULL, 'canceled', '2026-07-16 01:21:14', '2026-07-16 01:21:14'),
(11, 'INV-0018', 2, '2026-07-15 17:00:00', 10000.00, 'Tunai', NULL, 'received', '2026-07-16 01:22:03', '2026-07-16 01:22:03'),
(12, 'INV-20260716-2629', 2, '2026-07-22 17:00:00', 60000.00, 'Tunai', NULL, 'received', '2026-07-22 21:26:59', '2026-07-22 21:27:25'),
(13, 'INV-20260716-2628', 5, '2026-07-22 17:00:00', 10000.00, 'Tunai', NULL, 'received', '2026-07-22 22:11:35', '2026-07-22 22:12:22'),
(14, 'INV-20260716-2623', 2, '2026-07-22 17:00:00', 30000.00, 'Tunai', NULL, 'received', '2026-07-23 01:08:46', '2026-07-23 01:08:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `sparepart_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `sold_quantity` int(11) NOT NULL DEFAULT 0,
  `purchase_price` decimal(10,2) NOT NULL,
  `expired_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `sparepart_id`, `quantity`, `sold_quantity`, `purchase_price`, `expired_date`, `notes`, `created_at`, `updated_at`) VALUES
(2, 2, 1, 90, 0, 1000.00, '1989-07-27', NULL, '2026-07-11 09:47:31', '2026-07-11 09:47:31'),
(3, 3, 1, 10, 10, 1000.00, NULL, NULL, '2026-07-15 10:17:17', '2026-07-16 00:46:10'),
(4, 4, 1, 2, 2, 1000.00, NULL, NULL, '2026-07-16 00:22:20', '2026-07-16 00:47:42'),
(5, 5, 3, 3, 3, 10000.00, NULL, NULL, '2026-07-16 00:42:59', '2026-07-22 22:35:06'),
(6, 6, 1, 1, 1, 1000.00, NULL, NULL, '2026-07-16 00:47:18', '2026-07-16 01:15:48'),
(7, 7, 1, 2, 2, 1000.00, NULL, NULL, '2026-07-16 00:48:22', '2026-07-16 01:15:48'),
(8, 8, 1, 3, 3, 1000.00, NULL, NULL, '2026-07-16 00:49:49', '2026-07-16 01:15:48'),
(9, 9, 1, 2, 2, 1000.00, NULL, NULL, '2026-07-16 01:19:50', '2026-07-16 01:20:37'),
(10, 10, 1, 53105, 0, 1000.00, '2018-08-22', NULL, '2026-07-16 01:21:14', '2026-07-16 01:21:14'),
(11, 11, 1, 10, 8, 1000.00, NULL, NULL, '2026-07-16 01:22:03', '2026-07-23 01:06:09'),
(12, 12, 7, 6, 6, 10000.00, NULL, NULL, '2026-07-22 21:26:59', '2026-07-22 22:35:06'),
(13, 13, 7, 1, 1, 10000.00, NULL, NULL, '2026-07-22 22:11:35', '2026-07-22 22:35:06'),
(14, 14, 7, 3, 0, 10000.00, NULL, NULL, '2026-07-23 01:08:46', '2026-07-23 01:08:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-07-10 07:07:13', '2026-07-10 07:07:13'),
(2, 'kasir', 'web', '2026-07-10 07:07:13', '2026-07-10 07:07:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(2, 2),
(9, 2),
(15, 2),
(21, 2),
(27, 2),
(33, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(45, 2),
(50, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(67, 2),
(74, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_kendaraan_id` bigint(20) UNSIGNED NOT NULL,
  `durasi_estimasi` varchar(255) NOT NULL,
  `harga_standar` decimal(10,2) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `services`
--

INSERT INTO `services` (`id`, `nama`, `jenis_kendaraan_id`, `durasi_estimasi`, `harga_standar`, `status`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Buku', 5, '30 menit', 25000.00, 'aktif', 'Penggantian oli mesin untuk motor matic', '2026-07-10 07:07:14', '2026-07-11 09:51:08'),
(2, 'Service Rem Mobil Manual', 4, '1 jam', 120000.00, 'aktif', 'Pengecekan dan penggantian kampas rem', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(3, 'Tune Up Motor Manual', 2, '45 menit', 50000.00, 'aktif', 'Perawatan mesin motor manual', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(4, 'Ganti Oli Mobil Matic', 3, '1 jam', 100000.00, 'aktif', 'Penggantian oli transmisi mobil matic', '2026-07-10 07:07:14', '2026-07-10 07:07:14');

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
('ifx8Cp4f5i8PmlIyd5vT2ZSC2tJrTvUylGEA6rx3', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicUN6YjFGV24xeTkwdVpNWGxhd3FSMllWb1lPOE9FT2owREF1SVdYOCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbGFwb3Jhbi9wZW11c25haGFuIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1784796055);

-- --------------------------------------------------------

--
-- Struktur dari tabel `spareparts`
--

CREATE TABLE `spareparts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(100) DEFAULT NULL,
  `code_part` varchar(255) DEFAULT NULL,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_start_date` date DEFAULT NULL,
  `discount_end_date` date DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `available_stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `spareparts`
--

INSERT INTO `spareparts` (`id`, `name`, `size`, `code_part`, `purchase_price`, `selling_price`, `stock`, `discount_percentage`, `discount_start_date`, `discount_end_date`, `category_id`, `created_at`, `updated_at`, `available_stock`) VALUES
(1, 'Buku Gambar', 'L', 'BUK-BUK-9879', 0.00, 100000.00, 0, 0.00, NULL, NULL, 11, '2026-07-11 09:29:17', '2026-07-23 00:21:38', 1),
(3, 'Buku Gambar2', NULL, 'BUK-BUK-1999', 0.00, 100000.00, 0, 0.00, NULL, NULL, 11, '2026-07-11 10:46:12', '2026-07-16 00:27:13', 0),
(5, 'Petos Tempe', NULL, 'BOD-PET-5242', 0.00, 20000.00, 0, 0.00, NULL, NULL, 3, '2026-07-20 09:37:00', '2026-07-20 09:37:00', 0),
(6, 'Buku Gambar234322', NULL, 'SIS-BUK-5675', 0.00, 100000.00, 0, 0.00, NULL, NULL, 2, '2026-07-20 09:37:37', '2026-07-20 09:37:37', 0),
(7, 'Baju Pramuka', 'XL', 'SIS-BAJ-8968', 0.00, 120000.00, 0, 0.00, NULL, NULL, 2, '2026-07-22 21:24:47', '2026-07-22 21:24:47', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sparepart_id` bigint(20) UNSIGNED NOT NULL,
  `disposal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `type` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `remaining_stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`, `note`, `created_at`, `updated_at`) VALUES
(2, 'Nafarudin Pamungkas', '082112345678', 'sparepartjaya@example.com', 'Jl. Gatot Subroto No. 45, Jakarta', 'PT Sparepart Jaya', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(3, 'Bondan Gunawan Prakosa', '085612345678', NULL, 'Jl. Imam Bonjol No. 7, Surabaya', 'UD Sumber Sukses', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(4, 'Rizky Dwi Riswanto', '081298765432', 'mitra@example.com', 'Jl. Diponegoro No. 9, Yogyakarta', 'CV Mitra Bengkel', '2026-07-10 07:07:14', '2026-07-10 07:07:14'),
(5, 'Aurora Burgess', '+1 (385) 681-3867', 'zyrofiha@mailinator.com', 'Est aliquid aut sit', 'Nisi quisquam harum', '2026-07-11 09:39:57', '2026-07-11 09:39:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier_sparepart_stocks`
--

CREATE TABLE `supplier_sparepart_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `sparepart_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(8,2) NOT NULL DEFAULT 0.00,
  `purchase_price` decimal(10,2) NOT NULL,
  `received_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_number` varchar(255) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `invoice_number` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `proof_of_transfer_url` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `customer_id`, `vehicle_number`, `vehicle_model`, `transaction_date`, `total_price`, `discount_amount`, `invoice_number`, `payment_method`, `proof_of_transfer_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL, '2026-07-10 17:00:00', 25000.00, 0.00, 'INV-20260711-2127', 'transfer bank', NULL, 'completed', '2026-07-11 09:51:40', '2026-07-11 09:51:40'),
(2, 2, NULL, NULL, '2026-07-14 17:00:00', 400000.00, 0.00, 'INV-20260715-2327', 'transfer bank', NULL, 'completed', '2026-07-15 10:20:42', '2026-07-15 10:20:42'),
(3, 2, NULL, NULL, '2026-07-14 17:00:00', 100000.00, 0.00, 'INV-20260715-5592', 'transfer bank', NULL, 'completed', '2026-07-15 10:29:45', '2026-07-15 10:29:45'),
(4, 3, NULL, NULL, '2026-07-14 17:00:00', 100000.00, 0.00, 'INV-20260715-1903', 'transfer bank', NULL, 'completed', '2026-07-15 10:30:29', '2026-07-15 10:30:29'),
(5, 2, NULL, NULL, '2026-07-15 17:00:00', 200000.00, 0.00, 'INV-20260716-2867', 'transfer bank', NULL, 'completed', '2026-07-16 00:40:32', '2026-07-16 00:40:32'),
(6, 2, NULL, NULL, '2026-07-15 17:00:00', 400000.00, 0.00, 'INV-20260716-7849', 'tunai', NULL, 'completed', '2026-07-16 00:46:10', '2026-07-16 00:46:10'),
(7, 1, NULL, NULL, '2026-07-15 17:00:00', 600000.00, 0.00, 'INV-20260716-2345', 'transfer bank', NULL, 'completed', '2026-07-16 01:15:48', '2026-07-16 01:15:48'),
(8, 2, NULL, NULL, '2019-07-08 17:00:00', 200000.00, 0.00, 'INV-20260716-1075', 'kartu debit', NULL, 'completed', '2026-07-16 01:20:37', '2026-07-16 01:20:37'),
(9, 3, NULL, NULL, '2026-07-22 17:00:00', 120000.00, 0.00, 'INV-20260723-4958', 'Tunai', NULL, 'completed', '2026-07-22 22:15:03', '2026-07-22 22:26:47'),
(10, 3, NULL, NULL, '2026-07-22 17:00:00', 1520000.00, 0.00, 'INV-20260723-6624', 'tunai', NULL, 'completed', '2026-07-22 22:35:06', '2026-07-22 22:35:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` enum('service','sparepart') NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sparepart_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `item_type`, `item_id`, `purchase_order_item_id`, `price`, `quantity`, `created_at`, `updated_at`, `sparepart_id`) VALUES
(1, 1, 'service', 1, NULL, 25000.00, 1, '2026-07-11 09:51:40', '2026-07-11 09:51:40', NULL),
(2, 2, 'sparepart', 1, 3, 100000.00, 4, '2026-07-15 10:20:42', '2026-07-15 10:20:42', NULL),
(3, 3, 'sparepart', 1, 3, 100000.00, 1, '2026-07-15 10:29:45', '2026-07-15 10:29:45', NULL),
(4, 4, 'sparepart', 1, 3, 100000.00, 1, '2026-07-15 10:30:29', '2026-07-15 10:30:29', NULL),
(5, 5, 'sparepart', 3, 4, 100000.00, 2, '2026-07-16 00:40:32', '2026-07-16 00:40:32', NULL),
(6, 6, 'sparepart', 1, 3, 100000.00, 4, '2026-07-16 00:46:10', '2026-07-16 00:46:10', NULL),
(7, 7, 'sparepart', 1, 6, 100000.00, 1, '2026-07-16 01:15:48', '2026-07-16 01:15:48', NULL),
(8, 7, 'sparepart', 1, 7, 100000.00, 2, '2026-07-16 01:15:48', '2026-07-16 01:15:48', NULL),
(9, 7, 'sparepart', 1, 8, 100000.00, 3, '2026-07-16 01:15:48', '2026-07-16 01:15:48', NULL),
(10, 8, 'sparepart', 1, 9, 100000.00, 2, '2026-07-16 01:20:37', '2026-07-16 01:20:37', NULL),
(13, 9, 'sparepart', 7, 12, 120000.00, 1, '2026-07-22 22:26:47', '2026-07-22 22:26:47', NULL),
(14, 10, 'sparepart', 7, 12, 120000.00, 5, '2026-07-22 22:35:06', '2026-07-22 22:35:06', NULL),
(15, 10, 'sparepart', 7, 13, 120000.00, 1, '2026-07-22 22:35:06', '2026-07-22 22:35:06', NULL),
(16, 10, 'sparepart', 1, 11, 100000.00, 5, '2026-07-22 22:35:06', '2026-07-22 22:35:06', NULL),
(17, 10, 'sparepart', 3, 5, 100000.00, 3, '2026-07-22 22:35:06', '2026-07-22 22:35:06', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `phone`, `address`) VALUES
(1, 'Admin', 'admin@mail.com', NULL, '$2y$12$FOQCVtWOehb6tvro0VL3c.Rozu8ZO1h.kHaLrQZduwdNSuA4KIHsO', NULL, '2026-07-10 07:07:13', '2026-07-10 07:07:13', NULL, NULL),
(2, 'Kasir', 'kasir@mail.com', NULL, '$2y$12$UZ.Az5ZUn5I7fpj.TkALVuyxZdNIwTvbJeEaOz5lFA8a4RMIkFtfi', NULL, '2026-07-10 07:07:14', '2026-07-10 07:07:14', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bengkel_settings`
--
ALTER TABLE `bengkel_settings`
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
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`);

--
-- Indeks untuk tabel `disposals`
--
ALTER TABLE `disposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disposals_sparepart_id_foreign` (`sparepart_id`),
  ADD KEY `disposals_user_id_foreign` (`user_id`),
  ADD KEY `disposals_purchase_order_item_id_foreign` (`purchase_order_item_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jenis_kendaraans`
--
ALTER TABLE `jenis_kendaraans`
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
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_invoice_number_unique` (`invoice_number`),
  ADD KEY `purchase_orders_supplier_id_foreign` (`supplier_id`);

--
-- Indeks untuk tabel `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `purchase_order_items_sparepart_id_foreign` (`sparepart_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_jenis_kendaraan_id_foreign` (`jenis_kendaraan_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `spareparts`
--
ALTER TABLE `spareparts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `spareparts_code_part_unique` (`code_part`),
  ADD KEY `spareparts_category_id_foreign` (`category_id`);

--
-- Indeks untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_sparepart_id_foreign` (`sparepart_id`),
  ADD KEY `stock_movements_disposal_id_foreign` (`disposal_id`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `supplier_sparepart_stocks`
--
ALTER TABLE `supplier_sparepart_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_sparepart_stocks_supplier_id_foreign` (`supplier_id`),
  ADD KEY `supplier_sparepart_stocks_sparepart_id_foreign` (`sparepart_id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_invoice_number_unique` (`invoice_number`),
  ADD KEY `transactions_customer_id_foreign` (`customer_id`);

--
-- Indeks untuk tabel `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_items_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  ADD KEY `transaction_items_sparepart_id_foreign` (`sparepart_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bengkel_settings`
--
ALTER TABLE `bengkel_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `disposals`
--
ALTER TABLE `disposals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jenis_kendaraans`
--
ALTER TABLE `jenis_kendaraans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `spareparts`
--
ALTER TABLE `spareparts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `supplier_sparepart_stocks`
--
ALTER TABLE `supplier_sparepart_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `disposals`
--
ALTER TABLE `disposals`
  ADD CONSTRAINT `disposals_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `disposals_sparepart_id_foreign` FOREIGN KEY (`sparepart_id`) REFERENCES `spareparts` (`id`),
  ADD CONSTRAINT `disposals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_sparepart_id_foreign` FOREIGN KEY (`sparepart_id`) REFERENCES `spareparts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_jenis_kendaraan_id_foreign` FOREIGN KEY (`jenis_kendaraan_id`) REFERENCES `jenis_kendaraans` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `spareparts`
--
ALTER TABLE `spareparts`
  ADD CONSTRAINT `spareparts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_disposal_id_foreign` FOREIGN KEY (`disposal_id`) REFERENCES `disposals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_sparepart_id_foreign` FOREIGN KEY (`sparepart_id`) REFERENCES `spareparts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `supplier_sparepart_stocks`
--
ALTER TABLE `supplier_sparepart_stocks`
  ADD CONSTRAINT `supplier_sparepart_stocks_sparepart_id_foreign` FOREIGN KEY (`sparepart_id`) REFERENCES `spareparts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_sparepart_stocks_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`),
  ADD CONSTRAINT `transaction_items_sparepart_id_foreign` FOREIGN KEY (`sparepart_id`) REFERENCES `spareparts` (`id`),
  ADD CONSTRAINT `transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
