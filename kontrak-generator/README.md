# kontrak-generator

PDF overlay generator untuk **Perjanjian Sewa Menyewa Kendaraan** — mereplikasi `075 - Draft Kontrak Waste4Change.pdf` secara visual 1:1.

## Prinsip Utama

> **Kesamaan 100% hanya bisa dicapai dengan menggunakan PDF template asli + overlay, bukan dengan membuat ulang layout menggunakan HTML/CSS atau library PDF dari nol.**

Ketika layout dibuat ulang (HTML → DomPDF / Puppeteer / wkhtmltopdf), hasil akhir **selalu berbeda** dari aslinya karena:
- Font metrics berbeda (Arial ≠ Helvetica, DejaVu ≠ Times New Roman)
- Word-wrap dan line-break dihitung ulang oleh engine yang berbeda
- Spasi antar paragraf, indentasi list, dan posisi teks bergeser
- Nomor halaman dan page break ditentukan oleh engine, bukan dokumen asli

Pendekatan yang benar:
1. Simpan PDF asli sebagai template (`assets/template-kontrak.pdf`)
2. Copy seluruh 7 halaman ke PDF baru tanpa rasterisasi
3. Timpa **hanya** placeholder bertitik dengan data dinamis menggunakan koordinat tetap
4. Gunakan `pdf-lib` untuk overlay (white box → drawText)

## Struktur

```
kontrak-generator/
├── assets/
│   └── template-kontrak.pdf    ← PDF ASLI (copy dari database/)
├── src/
│   ├── generateContract.js     ← main overlay engine
│   ├── schema.js               ← Zod validation schema
│   └── coordinates.js          ← semua koordinat overlay per halaman
├── examples/
│   └── sample-data.json        ← contoh input untuk testing
├── scripts/
│   └── visualDiff.js           ← pixel-diff test (butuh Ghostscript)
├── output/                     ← hasil generate (gitignored)
└── package.json
```

## Setup

```bash
cd kontrak-generator
npm install
```

Pastikan file template ada:
```bash
# Dari root proyek Laravel:
copy "database\075 - Draft Kontrak Waste4Change.pdf" "kontrak-generator\assets\template-kontrak.pdf"
```

## Usage

### CLI

```bash
node src/generateContract.js '<json-data>' output/hasil.pdf
```

Contoh dengan sample data:

```bash
# Windows PowerShell
$data = Get-Content examples/sample-data.json -Raw
node src/generateContract.js $data output/sample-output.pdf

# Linux/macOS
node src/generateContract.js "$(cat examples/sample-data.json)" output/sample-output.pdf
```

### Sebagai Module (dipanggil dari Laravel via Process::run)

Laravel memanggil script ini via:
```php
Process::run("node {$scriptPath} {$jsonData} {$outputPath}")
```

Output stdout: `OK:/path/to/output.pdf`
Output stderr jika error: `ERROR:pesan error`

### Visual Diff Test

Membutuhkan [Ghostscript](https://www.ghostscript.com/) di PATH.

```bash
npm run visual-diff
# atau
node scripts/visualDiff.js assets/template-kontrak.pdf output/sample-output.pdf 200
```

Exit code 0 = pixel diff 0% di luar zona overlay = identik visual.

## Data Dinamis

Hanya field berikut yang boleh berubah (semua teks statis tetap dari template):

| Field | Keterangan | Contoh |
|---|---|---|
| `contractDay` | Nama hari Indonesia | `"Kamis"` |
| `contractDayEn` | Nama hari Inggris | `"Thursday"` |
| `contractDate` | Angka tanggal | `"30"` |
| `contractMonth` | Nama bulan Indonesia | `"Juli"` |
| `contractMonthEn` | Nama bulan Inggris | `"July"` |
| `contractYear` | Tahun | `"2026"` |
| `contractNumber` | Nomor kontrak | `"KTR-202607-0001"` |
| `secondPartyName` | Nama Pihak Kedua | `"PT. Waste4Change..."` |
| `secondPartyKtp` | Alamat/KTP Pihak Kedua | `"Jl. ..."` |
| `secondPartyRepresentative` | Wakil Pihak Kedua | `"Mohamad..."` |
| `rentalPeriod` | Label durasi sewa | `"12 bulan"` |
| `rentalStart` | Tanggal mulai (ID) | `"30 Juli 2026"` |
| `rentalEnd` | Tanggal selesai (ID) | `"30 Juli 2027"` |
| `rentalStartEn` | Tanggal mulai (EN) | `"30 July 2026"` |
| `rentalEndEn` | Tanggal selesai (EN) | `"30 July 2027"` |
| `secondPartyNoticeLines` | Baris alamat pemberitahuan (max 4) | `["PT...", "Jl...", "", ""]` |
| `secondPartyPhone` | Nomor HP Pihak Kedua | `"081234567890"` |
| `signingDateId` | Tanggal TTD (ID) | `"30 Juli 2026"` |
| `signingDateEn` | Tanggal TTD (EN) | `"30 July 2026"` |
| `secondPartySignerName` | Nama penanda tangan Pihak Kedua | `"Mohamad..."` |

## Koordinat Overlay

Semua koordinat ada di `src/coordinates.js`. Sistem koordinat menggunakan **origin kiri-bawah** (PDF standard / pdf-lib).

Konversi dari spesifikasi (origin kiri-atas):
```
pdfY = 841.68 - topY - lineHeight
```

Untuk kalibrasi ulang koordinat, gunakan tools seperti [PDF.js viewer](https://mozilla.github.io/pdf.js/) dengan inspect coordinates, atau Adobe Acrobat → Tools → Measure.

## Integrasi Laravel

Controller memanggil service ini di `InvKontrakController::generateDraftPdf()`:

```php
$result = Process::run("node {$scriptPath} {$jsonData} {$outputPath}");
if ($result->successful() && str_starts_with($result->output(), 'OK:')) {
    // berhasil
}
```

Pastikan `node` tersedia di PATH yang digunakan oleh PHP/web server.
Di Laragon, biasanya tersedia via `C:\laragon\bin\nodejs\node.exe`.
