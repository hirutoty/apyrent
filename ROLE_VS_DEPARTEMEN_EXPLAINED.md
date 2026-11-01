# 📚 Role vs Departemen: Penjelasan Lengkap

## 🎯 **Kesimpulan: KEDUANYA DIPAKAI!**

Sistem ApyRent menggunakan **ROLE** dan **DEPARTEMEN** untuk 2 keperluan berbeda:

| Field | Fungsi | Digunakan Untuk |
|-------|--------|-----------------|
| **ROLE** | Permission/Hak Akses | Menentukan **menu/fitur apa yang boleh diakses** |
| **DEPARTEMEN** | Filter Data/Organisasi | Menentukan **data departemen mana yang ditampilkan** |

---

## 🔐 **1. ROLE → Permission System**

### **Kode yang Menggunakan ROLE:**

```php
// routes/web.php
Route::middleware(['role:superadmin'])->group(function () {
    // Hanya superadmin yang bisa akses
    Route::resource('service-categories', ServiceCategoryController::class);
});

// app/Http/Middleware/CheckRole.php
if (!in_array($user->role, $roles)) {
    abort(403, 'Akses tidak diizinkan.');
}
```

### **Fungsi ROLE:**
- ✅ Cek apakah user boleh akses halaman tertentu
- ✅ Superadmin → akses **SEMUA** menu
- ✅ Keuangan → akses menu **Keuangan** saja
- ✅ IT → akses menu **IT Support** saja
- ✅ Sales → akses menu **Sales & Marketing**
- ✅ Dll.

### **Role yang Tersedia:**
```
superadmin → Full access ke semua menu
keuangan   → Menu Keuangan, Pembayaran, Budgeting
produksi   → Menu Produksi, Service, Kendaraan
hrd        → Menu HRD, Cuti, Karyawan
purchase   → Menu Purchase, Supplier, PO
sales      → Menu Sales, Penawaran, Kontrak
marketing  → Menu Marketing, CRM
it         → Menu IT Support, Asset, Network
```

---

## 🏢 **2. DEPARTEMEN → Data Filter**

### **Kode yang Menggunakan DEPARTEMEN:**

```php
// app/Http/Controllers/Admin/PembayaranController.php (SEBELUM FIX)
❌ SALAH - Pakai mapping hardcode:
$deptMap = ['keuangan' => 'Keuangan', 'sales' => 'Sales', ...];
$query->where('departemen', $deptMap[$role]);

✅ BENAR - Pakai departemen dari database:
$userDepartemen = auth()->user()->departemen;
if ($userDepartemen) {
    $query->where('departemen', $userDepartemen);
}
```

### **Fungsi DEPARTEMEN:**
- ✅ Filter data **Pembayaran** by departemen
- ✅ Filter data **Pengeluaran** by departemen
- ✅ Filter data **Budget** by departemen
- ✅ Track "siapa dari divisi mana"
- ✅ Laporan per departemen

### **Contoh Data Pembayaran:**

| ID | Tanggal | Pemohon | Departemen | Nominal |
|----|---------|---------|-----------|---------|
| 1 | 2026-11-01 | John | **Sales** | 5.000.000 |
| 2 | 2026-11-02 | Jane | **Keuangan** | 2.000.000 |
| 3 | 2026-11-03 | Bob | **IT** | 1.500.000 |

**User dengan `departemen: Sales` hanya lihat row #1**  
**Superadmin lihat semua!**

---

## 💡 **Contoh Real Use Case**

### **Scenario 1: User Normal (Role = Departemen)**
```
Nama: John Doe
Role: sales
Departemen: Sales

Akses Menu:
✅ Menu Sales, Penawaran, Kontrak (karena role=sales)

Data yang Tampil:
✅ Pembayaran dari departemen Sales saja (filter by departemen)
✅ Pengeluaran dari departemen Sales saja
```

### **Scenario 2: IT Support Cross-Departemen**
```
Nama: Jane IT
Role: it
Departemen: Keuangan

Akses Menu:
✅ Menu IT, Asset, Network (karena role=it)

Data yang Tampil:
✅ Pembayaran dari departemen Keuangan (filter by departemen)
✅ IT support khusus untuk divisi Keuangan
```

Ini berguna kalau setiap departemen punya **IT support sendiri**!

### **Scenario 3: Manager Cross-Functional**
```
Nama: Bob Manager
Role: sales
Departemen: Marketing

Akses Menu:
✅ Menu Sales (karena role=sales)

Data yang Tampil:
✅ Pembayaran dari departemen Marketing (filter by departemen)
✅ Manager Sales yang handle divisi Marketing
```

---

## 🐛 **Bug yang Sudah Diperbaiki**

### **Masalah SEBELUM Fix:**

```php
❌ HARDCODE MAPPING (SALAH!)
$deptMap = [
    'keuangan' => 'Keuangan',
    'sales'    => 'Sales',
    ...
];
$query->where('departemen', $deptMap[$role]); // ❌ Asumsi role = departemen
```

**Dampak Bug:**
- User dengan `role: it` dan `departemen: Sales`
- Akan lihat data departemen **IT** (salah!)
- Padahal seharusnya lihat data departemen **Sales**

---

### **Solusi SETELAH Fix:**

```php
✅ PAKAI DEPARTEMEN DARI DATABASE (BENAR!)
$userDepartemen = auth()->user()->departemen;
if ($userDepartemen) {
    $query->where('departemen', $userDepartemen);
}
```

**Hasil Fix:**
- User dengan `role: it` dan `departemen: Sales`
- Akan lihat data departemen **Sales** ✅ (benar!)
- Sesuai dengan divisi tempat user bekerja

---

## 📝 **File yang Sudah Diperbaiki**

### **PembayaranController.php**
- ✅ `index()` → Pakai `auth()->user()->departemen` untuk filter
- ✅ Stats calculation → Pakai departemen user
- ✅ `store()` → Ambil departemen dari user database

### **Lokasi Fix:**
1. Line ~60: Filter data pembayaran
2. Line ~175: Stats calculation
3. Line ~842: Store pembayaran baru

---

## 🎯 **Best Practice**

### **✅ DO (Yang Benar):**

```php
// 1. Pakai ROLE untuk permission
Route::middleware(['role:superadmin,keuangan'])->group(function () {
    // ...
});

// 2. Pakai DEPARTEMEN untuk filter data
$userDept = auth()->user()->departemen;
$query->where('departemen', $userDept);

// 3. Isi KEDUANYA saat register
User::create([
    'role'       => 'it',         // Akses menu
    'departemen' => 'Sales',      // Filter data
]);
```

### **❌ DON'T (Yang Salah):**

```php
// ❌ Jangan hardcode mapping role → departemen
$deptMap = ['keuangan' => 'Keuangan'];
$query->where('departemen', $deptMap[$role]); // SALAH!

// ❌ Jangan pakai role untuk filter data
$query->where('role', $userRole); // SALAH!

// ❌ Jangan pakai departemen untuk permission
if ($user->departemen === 'Keuangan') { ... } // SALAH!
```

---

## 🚀 **Testing**

### **Test Case 1: User IT untuk Sales**
```
1. Register user: role=it, departemen=Sales
2. Login → harus bisa akses menu IT ✅
3. Buka /admin/pembayaran → harus lihat data Sales saja ✅
4. Buat pembayaran baru → otomatis departemen=Sales ✅
```

### **Test Case 2: Superadmin**
```
1. Login sebagai superadmin
2. Buka /admin/pembayaran → lihat semua departemen ✅
3. Ada dropdown filter departemen ✅
4. Pilih "Sales" → hanya data Sales yang tampil ✅
```

### **Test Case 3: User Normal**
```
1. Register user: role=keuangan, departemen=Keuangan
2. Login → akses menu Keuangan ✅
3. Buka /admin/pembayaran → hanya data Keuangan ✅
4. Tidak ada dropdown filter (otomatis by departemen) ✅
```

---

## 📊 **Summary**

| Aspek | ROLE | DEPARTEMEN |
|-------|------|-----------|
| **Fungsi** | Permission/Hak Akses | Filter Data/Organisasi |
| **Tipe Data** | Enum (fixed list) | String (bisa custom) |
| **Digunakan di** | Middleware, Route Guard | Query Filter, Laporan |
| **Bisa Null?** | Ya (tapi harus di-set admin) | Ya (opsional) |
| **Wajib Sama?** | **TIDAK!** IT bisa support Sales | |

---

## 🎉 **Kesimpulan**

**ROLE dan DEPARTEMEN KEDUANYA PENTING!**

- **ROLE** = Apa yang boleh user **AKSES** (menu/fitur)
- **DEPARTEMEN** = Data departemen mana yang **DITAMPILKAN**

Dengan fix ini, sistem sekarang **100% fleksibel**:
- ✅ IT Support bisa assigned ke departemen mana saja
- ✅ Manager bisa handle cross-departemen
- ✅ Data filter sesuai departemen user, bukan asumsi role
- ✅ Permission tetap berdasarkan role (aman!)

**Bug mapping role → departemen sudah FIXED!** 🐛 → ✅
