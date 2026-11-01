-- ================================================
-- MANUAL MIGRATION: Update Users Table Enum
-- ================================================
-- Jalankan script ini di phpMyAdmin atau MySQL client
-- Database: (sesuaikan dengan nama database Anda)

-- 1. Update enum STATUS untuk menambahkan 'regis'
ALTER TABLE `users` 
MODIFY COLUMN `status` ENUM('regis', 'aktif', 'blokir') 
DEFAULT 'regis';

-- 2. Update enum ROLE untuk menambahkan role yang kurang
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('superadmin', 'keuangan', 'produksi', 'hrd', 'purchase', 'sales', 'marketing', 'it') 
NULL DEFAULT NULL;

-- 3. (Opsional) Update user yang sudah ada dengan status NULL atau kosong menjadi 'aktif'
-- Uncomment baris berikut jika ada user yang statusnya NULL
-- UPDATE `users` SET `status` = 'aktif' WHERE `status` IS NULL;

-- ================================================
-- VERIFIKASI
-- ================================================
-- Jalankan query ini untuk memastikan perubahan berhasil:
-- SHOW COLUMNS FROM `users` LIKE 'status';
-- SHOW COLUMNS FROM `users` LIKE 'role';
