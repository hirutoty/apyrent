# BUKU PANDUAN SISTEM ERP APYRENT
## PT Rental Kendaraan Indonesia

**Versi Dokumen:** 1.0  
**Tanggal:** Oktober 2026  
**Status:** Draft  

---

## DAFTAR ISI

- [BAB 1: PENGENALAN SISTEM](#bab-1-pengenalan-sistem)
  - [1.1 Apa itu APYRent](#11-apa-itu-apyrent)
  - [1.2 Tujuan Sistem](#12-tujuan-sistem)
  - [1.3 Role dan Hak Akses](#13-role-dan-hak-akses)
  - [1.4 Cara Mendapat Akses](#14-cara-mendapat-akses)
- [BAB 2: AKSES DAN NAVIGASI](#bab-2-akses-dan-navigasi)
  - [2.1 Login ke Sistem](#21-login-ke-sistem)
  - [2.2 Dashboard Utama](#22-dashboard-utama)
  - [2.3 Navigasi Menu](#23-navigasi-menu)
  - [2.4 Logout](#24-logout)

---

# BAB 1: PENGENALAN SISTEM

## 1.1 Apa itu APYRent

**APYRent** adalah sistem ERP (Enterprise Resource Planning) terintegrasi untuk manajemen armada kendaraan rental. Sistem ini dirancang khusus untuk PT Rental Kendaraan Indonesia guna mengelola seluruh aspek bisnis rental kendaraan, mulai dari manajemen armada, pemeliharaan, compliance dokumen, hingga keuangan dan invoicing.

APYRent adalah **sistem internal** — artinya tidak ada portal publik untuk pelanggan. Semua operasi dilakukan oleh staf internal perusahaan.

### Fitur Utama APYRent

1. **Manajemen Kendaraan**
   - Data master kendaraan (merk, jenis, nomor polisi, status)
   - Tracking kondisi kendaraan real-time
   - Foto dan dokumentasi kendaraan

2. **Rental & Kontrak**
   - Pengelolaan rental aktif dan historis
   - Kontrak dengan pelanggan (leasing)
   - Member dan pelanggan rental

3. **Service & Pemeliharaan**
   - Service rutin dan insidental
   - Service asuransi klaim
   - Tracking part dan spare parts
   - Reminder service otomatis

4. **Compliance Kendaraan**
   - GPS Tracking
   - Asuransi Kendaraan
   - Pajak Kendaraan
   - KIR (Kendaraan Inspeksi Ringan)
   - STNK

5. **Procurement & Approval**
   - Purchase Order (PO)
   - Approval workflow multi-level
   - Manajemen vendor dan supplier
   - Pembayaran dan pengeluaran

6. **Keuangan & Invoicing**
   - Invoice otomatis dari kontrak
   - Pembayaran dan cicilan
   - Aging AR/AP
   - Hutang vendor
   - Rekonsiliasi bank
   - Buku besar

7. **Laporan**
   - Export PDF/Excel untuk semua modul
   - Dashboard analytics dengan chart

---

## 1.2 Tujuan Sistem

Sistem APYRent dirancang untuk:

1. **Sentralisasi Data**  
   Semua informasi kendaraan, rental, service, dan keuangan tersimpan dalam satu platform.

2. **Otomasi Proses Bisnis**  
   - Reminder otomatis untuk perpanjangan dokumen (pajak, asuransi, KIR, STNK)
   - Generate invoice otomatis dari kontrak
   - Approval workflow untuk Purchase Order dan pembayaran

3. **Traceability dan Audit**  
   Setiap transaksi tercatat dengan timestamp dan user pelaku, memudahkan audit.

4. **Compliance Management**  
   Memastikan semua dokumen kendaraan (GPS, Asuransi, Pajak, KIR, STNK) tidak expired.

5. **Efisiensi Operasional**  
   Mengurangi manual work dan risiko human error.

---

## 1.3 Role dan Hak Akses

APYRent memiliki 3 role utama:

### 1. **Superadmin**
**Hak Akses:** Full access ke semua modul
- Approval Purchase Order (PO)
- Approval Pembayaran
- Approval per-item untuk service part
- Manajemen user dan setting sistem
- Akses ke semua laporan
- Edit/delete data master (kategori service, limit rules, dll)

**Tanggung Jawab:**
- Final approval untuk pengeluaran besar
- Monitoring kesehatan sistem
- User management

---

### 2. **Admin**
**Hak Akses:** Operasional penuh, kecuali approval final
- Input data rental, service, dokumen compliance
- Buat Purchase Order (PO) dan Pembayaran
- **Tidak bisa** approve PO dan Pembayaran sendiri (harus menunggu Superadmin)
- Lihat laporan sesuai scope kerjanya
- Edit data kendaraan, pelanggan, supplier

**Tanggong Jawab:**
- Input transaksi harian
- Follow-up reminder compliance
- Koordinasi dengan vendor/supplier

---

### 3. **Blokir**
**Status:** User yang diblokir  
Akun dengan status "blokir" tidak dapat login ke sistem. Middleware `CheckUserStatus` akan memblokirnya dengan error 403.

**Penggunaan:**
- Untuk suspend akun sementara (misal: karyawan cuti panjang)
- Untuk revoke akses permanen (misal: resign)

---

## 1.4 Cara Mendapat Akses

APYRent **tidak memiliki fitur register publik**. User baru dibuat oleh **Superadmin** atau **Admin** yang memiliki akses ke menu User Management.

### Proses Onboarding User Baru

1. **HR/Manager** mengirim permintaan pembuatan akun ke IT/Admin dengan data:
   - Nama lengkap
   - Email (akan jadi username login)
   - Role yang dibutuhkan (Admin atau Superadmin)

2. **Admin/Superadmin** masuk ke menu **User Management** di APYRent:
   - Klik tombol **"Tambah User"**
   - Isi form:
     - Nama
     - Email
     - Password (default atau custom)
     - Role
   - Simpan

3. **IT/Admin** mengirimkan kredensial ke user baru via email/chat:
   ```
   URL Login: http://[alamat-server]/login
   Email: user@example.com
   Password: [password-default]
   
   Harap ganti password setelah login pertama kali.
   ```

4. **User baru** login pertama kali dan **wajib ganti password** di menu **Profil**.

---

# BAB 2: AKSES DAN NAVIGASI

## 2.1 Login ke Sistem

### Langkah-Langkah Login

1. **Buka browser** (Chrome, Firefox, Edge, atau Safari)

2. **Akses URL APYRent**  
   Ketik alamat berikut di address bar:
   ```
   http://[alamat-server]/login
   ```
   atau
   ```
   http://127.0.0.1:8000/login  (untuk development lokal)
   ```

3. **Tampil Halaman Login**

   ![Halaman Login APYRent](../../../Users/bima1/.kiro/crew/playwright-snapshots/page-2026-10-26T17-56-39-900Z.png)

   Halaman login APYRent menampilkan:
   - **Logo dan nama perusahaan:** "PT Rental Kendaraan Indonesia"
   - **Tagline:** "Solusi Rental Kendaraan Modern - Kelola armada, pelanggan & transaksi dalam satu platform terintegrasi"
   - **Form login** dengan 2 field:
     - **EMAIL:** Masukkan email yang terdaftar
     - **PASSWORD:** Masukkan password Anda
   - **Checkbox "Ingat saya":** Centang jika ingin sistem mengingat login Anda (tidak perlu login ulang dalam periode tertentu)
   - **Tombol "Masuk":** Klik untuk submit kredensial
   - **Link "Buat Akun Baru":** Link ini **tidak berfungsi** untuk user biasa, karena APYRent tidak mendukung registrasi publik

4. **Isi Kredensial Login**

   **EMAIL:**
   ```
   test@example.com
   ```

   **PASSWORD:**
   ```
   ••••••• (masukkan password Anda)
   ```

   > **Catatan Keamanan:**
   > - Jangan share password ke siapapun
   > - Ganti password default segera setelah login pertama
   > - Gunakan password yang kuat (minimal 8 karakter, kombinasi huruf besar/kecil/angka/simbol)

5. **Klik Tombol "Masuk"**

   Sistem akan memvalidasi kredensial Anda:
   - ✅ **Jika valid:** Anda akan diarahkan ke Dashboard
   - ❌ **Jika salah:** Muncul pesan error:
     - "Email atau password salah"
     - "Akun Anda diblokir, hubungi administrator"

---

### Troubleshooting Login

| **Masalah** | **Penyebab** | **Solusi** |
|------------|-------------|-----------|
| "Email atau password salah" | Kredensial tidak cocok dengan database | 1. Pastikan email ditulis dengan benar (case-sensitive untuk password)<br>2. Coba reset password via admin<br>3. Hubungi admin jika lupa kredensial |
| "Akun Anda diblokir" | User memiliki status "blokir" | Hubungi Superadmin untuk aktivasi ulang akun |
| Halaman login tidak muncul | Server tidak berjalan atau URL salah | 1. Cek koneksi internet<br>2. Pastikan URL benar<br>3. Hubungi IT support |
| Redirect ke login terus menerus | Session/cookie issue | 1. Clear browser cache dan cookies<br>2. Coba browser lain<br>3. Restart browser |

---

## 2.2 Dashboard Utama

Setelah login berhasil, Anda akan masuk ke **Dashboard** — halaman utama yang menampilkan ringkasan operasional perusahaan.

### Komponen Dashboard

![Dashboard APYRent](../../../Users/bima1/.kiro/crew/playwright-snapshots/page-2026-10-26T17-57-09-685Z.png)

Dashboard APYRent terdiri dari:

#### 1. **Header (Top Bar)**
   - **Logo APYRent** (kiri atas)
   - **Nama user yang sedang login** (kanan atas)
   - **Tombol Logout** (kanan atas)
   - **Notifikasi** (icon bell, jika ada)

#### 2. **Sidebar Menu (Kiri)**
   Navigasi utama ke semua modul sistem. Menu diorganisir dalam kategori:
   - 🏠 **Dashboard** (halaman ini)
   - 🚗 **Kendaraan**
   - 📝 **Rental**
   - 🔧 **Service**
   - 📋 **Compliance** (GPS, Asuransi, Pajak, KIR, STNK)
   - 💰 **Keuangan**
   - 📊 **Laporan**
   - 👥 **Master Data** (Pelanggan, Supplier, User)
   - ⚙️ **Setting**

   > **Catatan:** Menu yang tampil tergantung role Anda. Admin tidak akan melihat menu yang hanya bisa diakses Superadmin.

#### 3. **Widget Statistik (Card View)**
   Dashboard menampilkan card-card statistik real-time:
   
   - **Total Kendaraan**  
     Jumlah seluruh kendaraan dalam armada
   
   - **Kendaraan Aktif**  
     Kendaraan yang sedang dirental atau siap rental
   
   - **Kendaraan Service**  
     Kendaraan yang sedang dalam proses service/maintenance
   
   - **Rental Aktif**  
     Jumlah kontrak rental yang sedang berjalan
   
   - **Invoice Pending**  
     Invoice yang belum dibayar
   
   - **Total Pendapatan Bulan Ini**  
     Akumulasi pembayaran rental bulan berjalan
   
   - **Pajak Jatuh Tempo (30 hari)**  
     Kendaraan yang pajaknya akan expired dalam 30 hari ke depan
   
   - **Asuransi Jatuh Tempo (30 hari)**  
     Kendaraan yang asuransinya akan expired dalam 30 hari ke depan

#### 4. **Chart & Grafik**
   
   Dashboard menampilkan visualisasi data dalam bentuk chart:
   
   - **Grafik Pendapatan Bulanan** (Line/Bar Chart)  
     Trend pendapatan 12 bulan terakhir
   
   - **Status Kendaraan** (Pie Chart)  
     Distribusi kendaraan berdasarkan status (Aktif, Service, Idle, dll)
   
   - **Top Performing Kendaraan** (Bar Chart)  
     Kendaraan dengan frekuensi rental tertinggi
   
   - **Aging AR** (Pie/Donut Chart)  
     Piutang berdasarkan umur (0-30 hari, 31-60 hari, >60 hari)

#### 5. **Quick Actions (Action Buttons)**
   
   Tombol shortcut untuk aksi yang sering dilakukan:
   - **+ Rental Baru**
   - **+ Service Baru**
   - **+ Kendaraan Baru**
   - **Lihat Semua Invoice**

#### 6. **Reminder & Alert**
   
   Notifikasi penting yang memerlukan tindakan segera:
   - 🔴 **Pajak akan expired dalam 7 hari** → [Lihat Detail]
   - 🟠 **KIR expired bulan ini** → [Lihat Detail]
   - 🟡 **Invoice overdue 60 hari** → [Lihat Detail]

---

### Interpretasi Dashboard

Dashboard adalah **command center** APYRent. Berikut cara membaca informasi:

#### **Indikator Kesehatan Armada:**
- **Hijau:** Semua berjalan lancar, tidak ada alert urgent
- **Kuning:** Ada compliance yang akan expired dalam 30 hari
- **Merah:** Ada dokumen yang sudah expired atau overdue pembayaran

#### **Prioritas Harian:**
1. Cek **Reminder & Alert** terlebih dahulu setiap pagi
2. Tindaklanjuti compliance yang akan expired (perpanjang dokumen)
3. Follow-up **Invoice Pending** yang mendekati due date
4. Monitor **Kendaraan Service** untuk memastikan tidak ada bottleneck

---

## 2.3 Navigasi Menu

Menu sidebar adalah jalur utama untuk mengakses semua fitur APYRent.

### Struktur Menu Lengkap

```
🏠 Dashboard
│
├── 🚗 KENDARAAN
│   ├── Data Kendaraan (list semua kendaraan)
│   ├── Jenis Kendaraan (master data: sedan, minibus, truck, dll)
│   └── Laporan Kehilangan
│
├── 📝 RENTAL
│   ├── Rental Aktif
│   ├── History Rental
│   └── Member / Pelanggan
│
├── 🔧 SERVICE
│   ├── Service History (semua service yang pernah dilakukan)
│   ├── Service Rutin
│   ├── Service Asuransi (klaim asuransi)
│   ├── Service Incident (kecelakaan/insiden)
│   ├── Kategori Service (master data)
│   └── Reminder Service
│
├── 📋 COMPLIANCE
│   ├── GPS Kendaraan
│   ├── Asuransi Kendaraan
│   ├── Pajak Kendaraan
│   ├── KIR (Kendaraan Inspeksi Ringan)
│   └── STNK
│
├── 🛒 PROCUREMENT
│   ├── Purchase Order (PO)
│   ├── Approval Workflow
│   ├── Pembayaran
│   └── Vendor Management
│
├── 💰 KEUANGAN
│   ├── Invoice
│   ├── Payments (pembayaran invoice)
│   ├── Aging AR (piutang)
│   ├── Aging AP (hutang)
│   ├── Hutang Vendor
│   ├── Rekonsiliasi Bank
│   └── Buku Besar
│
├── 📊 LAPORAN
│   ├── Laporan Kendaraan
│   ├── Laporan Service
│   ├── Laporan Keuangan
│   ├── Laporan Compliance
│   └── Export (PDF/Excel)
│
├── 👥 MASTER DATA
│   ├── Pelanggan
│   ├── Supplier
│   ├── User Management (Superadmin only)
│   └── Setting
│
└── ⚙️ SETTING (Superadmin only)
    ├── Kategori Service & Limit Rules
    ├── Company Profile
    └── System Configuration
```

---

### Cara Menggunakan Menu

1. **Klik kategori menu** (misal: KENDARAAN)
   - Menu akan expand menampilkan sub-menu

2. **Klik sub-menu** (misal: Data Kendaraan)
   - Halaman akan berganti ke modul yang dipilih

3. **Breadcrumb** (navigasi di atas konten)
   - Menampilkan lokasi Anda saat ini
   - Contoh: `Dashboard > Kendaraan > Data Kendaraan`
   - Klik breadcrumb untuk kembali ke halaman sebelumnya

---

## 2.4 Logout

### Cara Logout dari Sistem

1. **Klik nama user** di kanan atas header

2. **Klik tombol "Logout"** atau icon logout

3. **Konfirmasi logout**
   - Sistem akan menghapus session Anda
   - Anda akan diarahkan kembali ke halaman login

### Kapan Harus Logout?

✅ **WAJIB logout jika:**
- Selesai bekerja dan meninggalkan komputer
- Menggunakan komputer bersama/publik
- Akan berpindah user/akun

⚠️ **Auto-logout otomatis terjadi jika:**
- Session expired (default: 2 jam tidak ada aktivitas)
- Clear browser cookies/cache
- Browser ditutup (jika tidak centang "Ingat saya")

---

## Navigasi Lanjutan

Pada bab-bab selanjutnya, kita akan membahas setiap modul secara detail:

- **BAB 3: Manajemen Kendaraan**
- **BAB 4: Rental & Kontrak**
- **BAB 5: Service & Pemeliharaan**
- **BAB 6: Compliance Dokumen**
- **BAB 7: Procurement & Approval**
- **BAB 8: Keuangan & Invoicing**
- **BAB 9: Laporan & Export**
- **BAB 10: Troubleshooting & FAQ**

---

## Kontak Support

Jika mengalami kendala teknis atau memerlukan bantuan:

- **Email:** support@apyrent.com
- **Phone:** +62-xxx-xxxx-xxxx
- **WhatsApp:** +62-xxx-xxxx-xxxx
- **Jam Operasional:** Senin - Jumat, 08:00 - 17:00 WIB

---

**© 2026 PT Rental Kendaraan Indonesia. All rights reserved.**
