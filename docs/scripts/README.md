# APYRent Documentation Screenshot Automation

Otomasi pengambilan screenshot dan annotation untuk dokumentasi APYRent.

## Requirements

- Python 3.8+
- APYRent harus sudah running di `http://127.0.0.1:8000`
- Credentials login: `test@example.com` / `admin123`

## Installation

1. Install Python dependencies:
```bash
pip install -r requirements.txt
```

2. Install Playwright browsers:
```bash
playwright install chromium
```

## Usage

### Capture All Screenshots (Auto)

```bash
python capture_screenshots.py
```

Script akan:
- Login otomatis ke APYRent
- Navigate ke setiap halaman sesuai config
- Capture screenshot
- Annotate dengan lingkaran merah dan numbering
- Simpan ke folder `../images/`

### Configuration

Edit `screenshot_config.py` untuk:
- Tambah/kurangi screenshot
- Ubah koordinat annotation
- Sesuaikan URL dan selector

## Output

Semua screenshot disimpan di:
```
C:\laragon\www\apyrent\docs\images\
```

Format nama: `[number]-[descriptive-name].png`

## Screenshot List

Total: 85 screenshots

**BAB 1: Login dan Dashboard** (4 screenshots)
- 01-login-page.png
- 02-dashboard.png
- 03-header-user-menu.png
- 04-logout-button.png

**BAB 2: Data & Stok Kendaraan** (12 screenshots)
- 05-sidebar-kendaraan.png
- 06-tabel-kendaraan.png
- 07-btn-tambah-kendaraan.png
- 08-form-tambah-kendaraan.png
- 09-upload-foto-kendaraan.png
- 10-btn-simpan-kendaraan.png
- 11-notif-berhasil.png
- 12-icon-edit-kendaraan.png
- 13-form-edit-kendaraan.png
- 14-btn-update-kendaraan.png
- 15-icon-hapus-kendaraan.png
- 16-popup-konfirmasi-hapus.png

**BAB 3: Pajak Kendaraan** (8 screenshots)
- 17-sidebar-compliance.png
- 18-tabel-pajak.png
- 19-btn-perpanjang-pajak.png
- 20-form-perpanjang-pajak.png
- 21-upload-bukti-pajak.png
- 22-btn-ajukan-pajak.png
- 23-notif-pengajuan-berhasil.png
- 24-btn-ajukan-ulang-pajak.png

... (dan seterusnya untuk BAB 4-14)

## Annotation Types

Script support 2 jenis annotation:

### 1. Circle (Lingkaran Merah)
```python
{"type": "circle", "x": 400, "y": 300, "number": 1}
```
- Lingkaran merah dengan nomor di tengah
- Untuk highlight button/field/element

### 2. Rectangle (Kotak Merah)
```python
{"type": "rectangle", "x1": 100, "y1": 200, "x2": 500, "y2": 400}
```
- Kotak merah untuk area/section
- Untuk highlight form/table/container

## Customization

### Ubah Warna Annotation

Edit di `capture_screenshots.py`:

```python
# Line ~70
outline="red"  # Ganti dengan "blue", "green", dll
```

### Ubah Ukuran Circle

```python
# Line ~55
radius = 40  # Ganti dengan ukuran lain
```

### Ubah Font Numbering

```python
# Line ~45
font = ImageFont.truetype("arial.ttf", 24)  # Ganti font/size
```

## Troubleshooting

### Error: "APYRent is not accessible"
- Pastikan APYRent sudah running
- Cek URL di browser: `http://127.0.0.1:8000`

### Error: "playwright: command not found"
- Install Playwright: `pip install playwright`
- Install browsers: `playwright install chromium`

### Error: "selector not found"
- Tunggu loading lebih lama: ubah `timeout` di config
- Cek selector di browser DevTools (F12)

### Error: "font not found"
- Font default akan digunakan otomatis
- Atau install Arial font di sistem

### Screenshot kosong/hitam
- Ubah `headless=False` untuk lihat browser
- Tambah `time.sleep()` di script untuk debug

## Manual Annotation

Jika prefer annotate manual:

1. Capture tanpa annotation:
```python
# Comment out baris ini di main():
# annotate_image(output_path, config["annotations"])
```

2. Gunakan tool pilihan:
- Paint (Windows)
- GIMP (Free)
- Photoshop

3. Simpan dengan nama yang sama di `images/`

## Performance

- **Headless mode** (browser tidak tampil): ~2-3 menit untuk 85 screenshot
- **Headed mode** (browser tampil): ~5-8 menit (bisa lihat prosesnya)
- **Network**: Tergantung loading time APYRent

## License

Internal use only - PT Rental Kendaraan Indonesia
