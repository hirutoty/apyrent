-- ================================================
-- FIX: User Foto Path
-- ================================================
-- Masalah: Path foto masih menggunakan 'uploads/user/' 
-- Solusi: Ubah menjadi 'user/' untuk kompatibilitas Storage
-- 
-- Jalankan di phpMyAdmin atau MySQL client

-- 1. Cek dulu data yang akan diubah
SELECT id, name, foto 
FROM users 
WHERE foto LIKE 'uploads/user/%';

-- 2. Update path foto
UPDATE users 
SET foto = REPLACE(foto, 'uploads/user/', 'user/') 
WHERE foto LIKE 'uploads/user/%';

-- 3. Verifikasi hasil update
SELECT id, name, foto 
FROM users 
WHERE foto LIKE 'user/%';

-- ================================================
-- SELESAI!
-- ================================================
-- Setelah menjalankan script ini:
-- 1. Refresh halaman /admin/user
-- 2. Foto user harus muncul dengan benar
-- 3. Error 403 tidak akan muncul lagi
