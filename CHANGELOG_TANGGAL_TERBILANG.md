# Feature: Format Tanggal Terbilang untuk Pembukaan Kontrak

**Tanggal:** 26 Agustus 2026  
**Files Modified:**
- `app/Helpers/KontrakHelper.php`
- `resources/views/admin/kontrak/draft_pdf.blade.php`
- `resources/views/admin/kontrak/draft_print.blade.php`

---

## 🎯 Objective

Mengubah format tanggal pembukaan kontrak dari format standar menjadi **format legal/notaris Indonesia** yang menggunakan **terbilang** (penulisan dalam huruf) untuk tanggal, bulan, dan tahun — sesuai standar dokumen hukum di Indonesia.

---

## 📋 Requirements

### Format Output

**BEFORE (Format Standar):**
```
Bahasa Indonesia:
Pada hari ini, Rabu, tanggal 26 Agustus 2026, kami yang bertanda tangan dibawah ini, masing-masing:

English:
On this day, Wednesday, 26 August 2026, we the undersigned, respectively:
```

**AFTER (Format Terbilang/Legal):**
```
Bahasa Indonesia:
Pada hari ini, Rabu, tanggal Dua puluh enam bulan Agustus tahun Dua ribu dua puluh enam, kami yang bertanda tangan dibawah ini, masing-masing:

English:
On this day, Wednesday, the twenty-sixth of August two thousand twenty-six, we the undersigned, respectively:
```

### Scope
- ✅ **Hanya berlaku untuk** bagian pembukaan kontrak ("Pada hari ini...")
- ✅ **Tidak mengubah** format tanggal di pasal-pasal lain (Pasal 2, dll. tetap menggunakan format normal "26 Agustus 2026")
- ✅ **Location:** Draft PDF dan Draft Print view kontrak

### Rules
- **Nama hari:** Auto-calculate dari tanggal kontrak (Senin, Selasa, dst untuk ID; Monday, Tuesday untuk EN)
- **Tanggal:** Terbilang dengan huruf kapital di awal setiap kata (Bahasa Indonesia) atau lowercase (English)
- **Bulan:** Nama lengkap bulan
- **Tahun:** Terbilang lengkap

---

## 🛠️ Implementation

### 1. **New Helper Function: `KontrakHelper::formatTanggalTerbilang()`**

**Location:** `app/Helpers/KontrakHelper.php`

**Signature:**
```php
public static function formatTanggalTerbilang($date, string $lang = 'id'): string
```

**Parameters:**
- `$date` (Carbon|string): Tanggal yang akan diformat
- `$lang` (string): Bahasa output — `'id'` untuk Indonesia, `'en'` untuk English

**Returns:**
- `string`: Tanggal dalam format terbilang legal

**Features:**
- ✅ Auto-detect nama hari dari tanggal
- ✅ Convert angka tanggal ke terbilang (1-31)
- ✅ Convert tahun ke terbilang (support 1000-9999)
- ✅ Ordinal numbers untuk English (first, second, third, ... twenty-sixth, ... thirty-first)

**Example Usage:**
```php
use Carbon\Carbon;
use App\Helpers\KontrakHelper;

$date = Carbon::parse('2026-08-26'); // Rabu, 26 Agustus 2026

// Bahasa Indonesia
KontrakHelper::formatTanggalTerbilang($date, 'id');
// Output: "Rabu, tanggal Dua puluh enam bulan Agustus tahun Dua ribu dua puluh enam"

// English
KontrakHelper::formatTanggalTerbilang($date, 'en');
// Output: "Wednesday, the twenty-sixth of August two thousand twenty-six"
```

---

### 2. **Supporting Functions (Private)**

#### `numberToWordsIndonesia(int $num): string`

Convert angka (1-9999) ke terbilang Bahasa Indonesia.

**Examples:**
- `1` → "Satu"
- `10` → "Sepuluh"
- `26` → "Dua puluh enam"
- `100` → "Seratus"
- `2026` → "Dua ribu dua puluh enam"

**Logic:**
- 0-9: Basic ones
- 10-19: Teens (Sepuluh, Sebelas, Dua belas, ...)
- 20-99: Tens + ones (Dua puluh enam)
- 100-199: Seratus + remainder
- 200-999: Hundreds (Dua ratus, Tiga ratus, ...)
- 1000-1999: Seribu + remainder
- 2000-9999: Thousands (Dua ribu, Tiga ribu, ...)

---

#### `numberToWordsEnglish(int $num): string`

Convert angka (1-9999) ke terbilang English.

**Examples:**
- `1` → "one"
- `10` → "ten"
- `26` → "twenty-six"
- `100` → "one hundred"
- `2026` → "two thousand twenty-six"

**Logic:**
- 0-9: Basic ones
- 10-19: Teens (ten, eleven, twelve, ...)
- 20-99: Tens + ones (twenty-six)
- 100-999: Hundreds (one hundred, two hundred, ...)
- 1000-9999: Thousands (one thousand, two thousand, ...)

---

### 3. **Ordinal Numbers for English Dates**

Mapping tanggal 1-31 ke ordinal words:

```php
$ordinals = [
    1 => 'first',
    2 => 'second',
    3 => 'third',
    4 => 'fourth',
    ...
    26 => 'twenty-sixth',
    ...
    31 => 'thirty-first',
];
```

---

### 4. **View Updates**

#### `draft_pdf.blade.php`

**BEFORE:**
```php
$tgl    = Carbon::parse($kontrak->tanggal_kontrak);
$hariId = $tgl->isoFormat('dddd');
$tglId  = $tgl->isoFormat('D MMMM YYYY');
$hariEn = $tgl->locale('en')->isoFormat('dddd');
$tglEn  = $tgl->locale('en')->isoFormat('D MMMM YYYY');
```

```html
<table class="tc"><tr>
    <td><p>Pada hari ini, {{ $hariId }}, tanggal {{ $tglId }}, kami yang bertanda tangan dibawah ini, masing-masing:</p></td>
    <td class="r"><p>On this day, {{ $hariEn }}, {{ $tglEn }}, we the undersigned, respectively:</p></td>
</tr></table>
```

**AFTER:**
```php
$tgl = Carbon::parse($kontrak->tanggal_kontrak);
// Format terbilang untuk pembukaan kontrak
$tglTerbilangId = KontrakHelper::formatTanggalTerbilang($tgl, 'id');
$tglTerbilangEn = KontrakHelper::formatTanggalTerbilang($tgl, 'en');
```

```html
<table class="tc"><tr>
    <td><p>Pada hari ini, {{ $tglTerbilangId }}, kami yang bertanda tangan dibawah ini, masing-masing:</p></td>
    <td class="r"><p>On this day, {{ $tglTerbilangEn }}, we the undersigned, respectively:</p></td>
</tr></table>
```

#### `draft_print.blade.php`

Perubahan sama seperti `draft_pdf.blade.php` — hanya berbeda di struktur HTML (menggunakan `<div class="two-col">` alih-alih `<table>`).

---

## 🧪 Testing Scenarios

### Test Case 1: Tanggal Normal (26 Agustus 2026 - Rabu)

**Input:**
```php
$date = Carbon::parse('2026-08-26');
```

**Expected Output:**

**Bahasa Indonesia:**
```
Rabu, tanggal Dua puluh enam bulan Agustus tahun Dua ribu dua puluh enam
```

**English:**
```
Wednesday, the twenty-sixth of August two thousand twenty-six
```

---

### Test Case 2: Tanggal Awal Bulan (1 Maret 2026 - Minggu)

**Input:**
```php
$date = Carbon::parse('2026-03-01');
```

**Expected Output:**

**Bahasa Indonesia:**
```
Minggu, tanggal Satu bulan Maret tahun Dua ribu dua puluh enam
```

**English:**
```
Sunday, the first of March two thousand twenty-six
```

---

### Test Case 3: Tanggal Tengah (15 Mei 2026 - Jumat)

**Input:**
```php
$date = Carbon::parse('2026-05-15');
```

**Expected Output:**

**Bahasa Indonesia:**
```
Jumat, tanggal Lima belas bulan Mei tahun Dua ribu dua puluh enam
```

**English:**
```
Friday, the fifteenth of May two thousand twenty-six
```

---

### Test Case 4: Tanggal Akhir Bulan (31 Desember 2026 - Kamis)

**Input:**
```php
$date = Carbon::parse('2026-12-31');
```

**Expected Output:**

**Bahasa Indonesia:**
```
Kamis, tanggal Tiga puluh satu bulan Desember tahun Dua ribu dua puluh enam
```

**English:**
```
Thursday, the thirty-first of December two thousand twenty-six
```

---

### Test Case 5: Tanggal dengan Teens (12 April 2026 - Minggu)

**Input:**
```php
$date = Carbon::parse('2026-04-12');
```

**Expected Output:**

**Bahasa Indonesia:**
```
Minggu, tanggal Dua belas bulan April tahun Dua ribu dua puluh enam
```

**English:**
```
Sunday, the twelfth of April two thousand twenty-six
```

---

### Test Case 6: Edge Case - Tahun 2000

**Input:**
```php
$date = Carbon::parse('2000-01-01');
```

**Expected Output:**

**Bahasa Indonesia:**
```
Sabtu, tanggal Satu bulan Januari tahun Dua ribu
```

**English:**
```
Saturday, the first of January two thousand
```

---

## 📊 Visual Comparison

### PDF Header - Before vs After

**BEFORE:**
```
═══════════════════════════════════════════════════════════════
           PERJANJIAN SEWA MENYEWA KENDARAAN
                CAR RENTAL AGREEMENT
                    NO : KTR-202608-0001
───────────────────────────────────────────────────────────────

Pada hari ini, Rabu, tanggal 26 Agustus 2026, kami yang bertanda 
tangan dibawah ini, masing-masing:
```

**AFTER:**
```
═══════════════════════════════════════════════════════════════
           PERJANJIAN SEWA MENYEWA KENDARAAN
                CAR RENTAL AGREEMENT
                    NO : KTR-202608-0001
───────────────────────────────────────────────────────────────

Pada hari ini, Rabu, tanggal Dua puluh enam bulan Agustus tahun 
Dua ribu dua puluh enam, kami yang bertanda tangan dibawah ini, 
masing-masing:
```

---

## ✅ Verification Checklist

- [x] Fungsi `formatTanggalTerbilang()` ditambahkan ke `KontrakHelper.php`
- [x] Fungsi helper private `numberToWordsIndonesia()` dan `numberToWordsEnglish()` sudah lengkap
- [x] Mapping ordinal numbers English (1st-31st) sudah lengkap
- [x] `draft_pdf.blade.php` diupdate untuk menggunakan `$tglTerbilangId` dan `$tglTerbilangEn`
- [x] `draft_print.blade.php` diupdate dengan perubahan yang sama
- [x] Format output sesuai contoh legal Indonesia (huruf kapital di awal setiap kata untuk ID)
- [x] Format output English lowercase dengan ordinal words (first, second, twenty-sixth, dst)
- [x] Nama hari auto-calculate dari tanggal (Senin-Minggu / Sunday-Saturday)

---

## 🚀 How to Test

1. **Buka halaman Kontrak** → Pilih kontrak dengan tanggal 26 Agustus 2026
2. **Klik "Download Draft PDF"** atau **"Print Draft"**
3. **Verifikasi bagian pembukaan kontrak:**
   - **Kolom kiri (Bahasa Indonesia):** Harus tampil "Rabu, tanggal Dua puluh enam bulan Agustus tahun Dua ribu dua puluh enam"
   - **Kolom kanan (English):** Harus tampil "Wednesday, the twenty-sixth of August two thousand twenty-six"
4. **Verifikasi Pasal 2 (MASA SEWA):** Format tanggal di pasal-pasal tetap normal "26 Agustus 2026" (tidak berubah)

---

## 🔧 Technical Notes

### Performance
- **No database queries:** Semua konversi dilakukan in-memory
- **Minimal overhead:** Fungsi hanya dipanggil 1x per PDF generation
- **No external dependencies:** Pure PHP, tidak butuh library tambahan

### Maintenance
- **Easy to extend:** Jika ada format khusus untuk notaris tertentu, tinggal tambahkan parameter ke `formatTanggalTerbilang()`
- **Localized:** Mudah untuk menambahkan bahasa lain (misal: Dutch, Arabic) dengan menambahkan case baru di fungsi

### Backward Compatibility
- ✅ **Kontrak lama tidak terpengaruh** — format terbilang hanya berlaku untuk PDF/print yang di-generate ulang
- ✅ **Database tidak berubah** — tanggal tetap tersimpan sebagai `date` type di database
- ✅ **API tetap sama** — tidak ada breaking change

---

## 🎨 Future Enhancements (Optional)

1. **Custom Format per Notaris:** Tambahkan setting untuk memilih format notaris yang berbeda-beda
2. **Abbreviation Support:** Opsi untuk format pendek "Rabu, 26 Agustus 2026" vs panjang "Rabu, tanggal Dua puluh enam..."
3. **Historical Format:** Support format terbilang untuk dokumen era kolonial (tahun 1900-an)
4. **Locale Expansion:** Tambahkan bahasa daerah (Jawa, Sunda, dll) untuk dokumen khusus

---

## 📝 Related Code Locations

**Files Modified:**
- `app/Helpers/KontrakHelper.php` (Lines ~140-300: new functions added)
- `resources/views/admin/kontrak/draft_pdf.blade.php` (Line ~33: date formatting, Line ~260: opening paragraph)
- `resources/views/admin/kontrak/draft_print.blade.php` (Line ~157: date formatting, Line ~441: opening paragraph)

**Related Functions (not modified):**
- `InvKontrakController::store()` — Generate draft PDF (uses the view)
- `InvKontrakController::regenerateDraft()` — Regenerate PDF (uses the view)
- `InvKontrakController::draftPrint()` — Print view (uses the view)

---

**Implemented by:** Kiro AI  
**Status:** ✅ Complete & Ready for Testing  
**Date:** 26 Agustus 2026
