# Testing Checklist - Dynamic Pihak Kedua Feature

## ✅ Pre-requisites
- [ ] Migration sudah run: `php artisan migrate`
- [ ] Check kolom ada: `php debug_kontrak.php`

## 🧪 Test Scenario 1: Buat Kontrak Baru (Perusahaan)

### Steps:
1. [ ] Buka `/admin/kontrak` → Klik "Buat Kontrak Baru"
2. [ ] Pilih **Jenis Pelanggan = "Perusahaan"** PERTAMA KALI
3. [ ] Verify: Field "Diwakili Oleh" dan "Jabatan" **muncul**
4. [ ] Isi data:
   - [ ] Pihak Kedua: `PT. Putra Tunggal Trans`
   - [ ] Alamat: `Jl. Kemang Sari Raya No. 18A RT. 002 RW. 011 Jatibening Baru, Pondok Gede, Kota Bekasi, Jawa Barat`
   - [ ] No. KTP: `31.7105.680790.0004`
   - [ ] Diwakili Oleh: `Yuningsih`
   - [ ] Jabatan: `Direktur Utama`
   - [ ] Tanggal Kontrak: (pilih tanggal)
   - [ ] Durasi: `12` bulan
5. [ ] Tab 2: Pilih ketentuan default
6. [ ] Submit → Kontrak tersimpan
7. [ ] Download PDF → Buka file
8. [ ] **VERIFY PDF menampilkan:**
   ```
   2. PT. Putra Tunggal Trans, suatu perseroan terbatas yang memiliki kantor 
   terdaftar di Jl. Kemang Sari Raya No. 18A RT. 002 RW. 011 Jatibening Baru, 
   Pondok Gede, Kota Bekasi, Jawa Barat, dalam hal ini diwakili oleh Yuningsih 
   dalam jabatannya selaku Direktur Utama, pemegang KTP No. 31.7105.680790.0004, 
   selanjutnya disebut sebagai "Pihak Kedua".
   ```

### Expected Result:
✅ PDF menampilkan format lengkap perusahaan dengan perwakilan  
✅ Nama perusahaan dan perwakilan dalam **bold**  
✅ Format bahasa Inggris juga benar

---

## 🧪 Test Scenario 2: Edit Kontrak Existing

### Steps:
1. [ ] Cari kontrak `KTR-202608-0001` (PT Teknologi Nusantara)
2. [ ] Klik tombol **Edit** (icon pensil)
3. [ ] Modal terbuka → Verify:
   - [ ] Jenis Pelanggan = "Perusahaan"
   - [ ] Field "Diwakili Oleh" dan "Jabatan" **sudah muncul**
   - [ ] Field kosong (karena data lama NULL)
4. [ ] Isi field perwakilan:
   - [ ] Diwakili Oleh: `Yuningsih`
   - [ ] Jabatan: `Direktur Utama`
5. [ ] Submit/Update
6. [ ] Klik tombol **"Regenerate Draft"** (icon refresh)
7. [ ] Download PDF lagi
8. [ ] **VERIFY:** Format Pihak Kedua berubah (sekarang ada "diwakili oleh Yuningsih...")

### Expected Result:
✅ Data perwakilan tersimpan  
✅ PDF ter-regenerate dengan format baru  
✅ Run `php debug_kontrak.php` → kontrak ID 10 sekarang punya `perwakilan_pihak_kedua = 'Yuningsih'`

---

## 🧪 Test Scenario 3: Switch Jenis Pelanggan (Create)

### Steps:
1. [ ] Buat kontrak baru → Default Jenis = "Perorangan"
2. [ ] Verify: Field perwakilan **tidak muncul** (hidden)
3. [ ] Switch ke **"Perusahaan"**
4. [ ] Verify: Field perwakilan **langsung muncul** dengan tanda * merah (required)
5. [ ] Switch kembali ke **"Perorangan"**
6. [ ] Verify: Field perwakilan **langsung hidden**

### Expected Result:
✅ Toggle show/hide real-time works  
✅ Required validation active saat jenis = Perusahaan

---

## 🧪 Test Scenario 4: Backward Compatibility

### Steps:
1. [ ] Run `php debug_kontrak.php` → Lihat kontrak ID 6-9 (punya `jenis_pelanggan = NULL`)
2. [ ] Download PDF kontrak lama tersebut
3. [ ] **VERIFY:** PDF tetap tampil normal (tidak error)
4. [ ] Format Pihak Kedua menggunakan **fallback perorangan** (sederhana)

### Expected Result:
✅ Kontrak lama tidak error  
✅ Format default ke perorangan jika jenis_pelanggan NULL

---

## 🐛 Troubleshooting

### Jika field perwakilan tidak muncul saat edit:
1. Buka browser console (F12)
2. Check error JavaScript
3. Verify `togglePerwakilanFields` function exists
4. Manual test: `togglePerwakilanFields('edit')` di console

### Jika data tidak tersimpan:
1. Buka Network tab (F12) saat submit form
2. Check payload request → ada field `perwakilan_pihak_kedua` dan `jabatan_pihak_kedua`?
3. Check response → ada error validasi?
4. Run `php debug_kontrak.php` → verify data masuk database

### Jika PDF masih format lama:
1. Pastikan klik tombol **"Regenerate Draft"** setelah update data
2. Hard refresh browser (Ctrl+Shift+R)
3. Check timestamp file PDF (pastikan file baru)

---

## 📊 Final Verification

Setelah semua test:
```bash
php debug_kontrak.php
```

Expected output:
```
ID    No. Kontrak          Pihak Kedua                    Jenis           Perwakilan           Jabatan              Status    
────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
11    KTR-202608-0002      PT. Putra Tunggal Trans        perusahaan      Yuningsih            Direktur Utama       pending   
10    KTR-202608-0001      PT Teknologi Nusantara         perusahaan      Yuningsih            Direktur Utama       pending   
...

✅ Kontrak with perwakilan data: 2
```

---

**Status:** Ready for Manual Testing  
**Next Step:** Follow Test Scenario 1 or 2 above
