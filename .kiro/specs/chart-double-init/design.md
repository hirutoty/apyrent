# Chart Double-Init Bugfix Design

## Overview

Bug ini terjadi karena dua jalur inisialisasi chart berjalan bersamaan pada halaman yang menggunakan komponen `x-chart-filter`. Komponen `chart-filter.blade.php` mendispatch event `chartFilterChange` via `window.addEventListener('load', ...)`, sementara sebagian besar halaman juga memanggil `initXxxCharts()` secara langsung di `DOMContentLoaded`. Akibatnya, setiap chart di-fetch dua kali — pertama dari `DOMContentLoaded`, lalu sekali lagi saat `window.load` menyulut event filter ulang.

Pola yang benar sudah ada di `members/index.blade.php`: tidak memanggil init chart di `DOMContentLoaded`, melainkan hanya memasang listener `chartFilterChange` dan menggunakan `chartManager.hasChart()` untuk membedakan init pertama vs update berikutnya. Strategi fix adalah: (1) hapus `window.load` auto-dispatch dari `chart-filter.blade.php`, dan (2) migrasikan semua halaman lain ke pola members.

---

## Glossary

- **Bug_Condition (C)**: Kondisi yang memicu bug — halaman menggunakan `x-chart-filter` DAN memanggil `initXxxCharts()` langsung di `DOMContentLoaded` DAN `chart-filter.blade.php` mendispatch ulang `chartFilterChange` via `window.load`
- **Property (P)**: Perilaku yang diharapkan — chart diinisialisasi tepat satu kali, langsung saat `DOMContentLoaded`, menggunakan satu jalur inisialisasi melalui listener `chartFilterChange`
- **Preservation**: Fungsionalitas filter (klik tombol periode, custom date range, category filter) harus tetap bekerja persis sama setelah fix
- **`hasChart(canvasId)`**: Method di `ChartManager` yang mengembalikan `true` jika chart untuk canvas tertentu sudah diinisialisasi — digunakan sebagai gate untuk membedakan init vs update
- **`window.load` auto-dispatch**: Baris kode di `chart-filter.blade.php` yang mendispatch `chartFilterChange` saat `window.addEventListener('load', ...)` — ini adalah sumber race condition
- **Pola Members**: Pola implementasi yang benar di `members/index.blade.php` — tidak memanggil init langsung, hanya pasang listener, gunakan `hasChart()` sebagai gate

---

## Bug Details

### Bug Condition

Bug terjadi ketika halaman memenuhi tiga kondisi sekaligus:
1. Menggunakan komponen `x-chart-filter`
2. Memanggil `initXxxCharts()` langsung di `DOMContentLoaded`
3. `chart-filter.blade.php` mendispatch ulang `chartFilterChange` via `window.load`

Akibatnya terjadi dua fetch berurutan: satu dari `DOMContentLoaded` (call `initXxxCharts()` langsung), satu lagi saat `window.load` selesai dan filter mendispatch event. Chart tampak "berkedip" atau loading ganda — terutama terlihat setelah operasi CRUD yang memicu page reload.

**Formal Specification:**
```
FUNCTION isBugCondition(X)
  INPUT: X of type PageLoad
  OUTPUT: boolean

  RETURN X.hasChartFilter
     AND X.callsInitDirectlyOnDOMContentLoaded
     AND X.chartFilterDispatchesOnWindowLoad
END FUNCTION
```

### Examples

- **Halaman Kontrak** — `initKontrakCharts({ filter_type: 'month' })` dipanggil di `DOMContentLoaded`, lalu `window.load` memicu `chartFilterChange` → chart di-fetch 2x
- **Halaman History** — sama, `initHistoryCharts({ filter_type: 'month' })` langsung di `DOMContentLoaded`
- **Halaman GPS** — `initGpsCharts({ filter_type: 'year' })` langsung di `DOMContentLoaded`
- **Halaman Members (BENAR)** — tidak memanggil init langsung; hanya pasang listener, gunakan `hasChart()` → chart di-fetch 1x

---

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Klik tombol filter (Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini) harus tetap mendispatch `chartFilterChange` dan memperbarui chart
- Custom date range (pilih tanggal + klik Terapkan) harus tetap bekerja dengan `filterType: 'custom'`
- Chart harus tetap tampil dengan filter default sesuai atribut `defaultFilter` yang dikonfigurasi per halaman
- Category filter (`showCategoryFilter="true"`) harus tetap mendispatch `chartFilterChange` dengan `categoryId`
- Lazy-init chart pada halaman keuangan (AP/AR hanya init saat tab dibuka pertama kali) harus tetap bekerja
- Loading overlay dan error overlay di atas canvas harus tetap muncul saat fetch
- `hasChart()` method harus tetap berfungsi sebagai gate kondisi

**Scope:**
Semua halaman yang TIDAK menggunakan `x-chart-filter` tidak terpengaruh. Halaman `members/index.blade.php` sudah menggunakan pola yang benar — tidak berubah, hanya dijadikan referensi. Perubahan hanya pada `chart-filter.blade.php` dan 14 halaman lain yang menggunakan pola lama.

---

## Hypothesized Root Cause

Berdasarkan analisis kode, penyebab utama dan rinciannya:

1. **`window.load` auto-dispatch di `chart-filter.blade.php`**: Baris `window.addEventListener('load', function () { document.dispatchEvent(new CustomEvent('chartFilterChange', { detail: {...} })) })` menyebabkan setiap instance filter mendispatch event setelah semua resource halaman selesai dimuat. Karena halaman sudah melakukan init di `DOMContentLoaded` (yang terjadi sebelum `window.load`), terjadi double-fetch.

2. **`initXxxCharts()` langsung di `DOMContentLoaded`**: Pola umum di semua halaman (kecuali members) adalah memanggil `initXxxCharts(filters)` secara langsung di `DOMContentLoaded`, tanpa mengecek apakah chart sudah ada. Ini membuat halaman tidak "event-driven" — chart diinisialisasi dari dua jalur secara berurutan.

3. **Tidak ada gate `hasChart()`**: Listener `chartFilterChange` pada halaman-halaman bermasalah langsung memanggil `updateXxxCharts()` tanpa mengecek apakah chart sudah pernah diinisialisasi. Jika event pertama adalah init, dan event kedua (dari `window.load`) langsung `update`, urutan fetch tidak terdefinisi dengan baik.

4. **Duplikasi `x-chart-filter` di `data_leasing/index.blade.php`**: File ini bahkan mendefinisikan `x-chart-filter` dan `x-chart-container` dua kali untuk chart yang sama — menyebabkan event duplikat dan filter yang muncul dua kali di halaman.

---

## Correctness Properties

Property 1: Bug Condition — Single Initialization Per Chart

_For any_ halaman yang menggunakan `x-chart-filter` (isBugCondition returns true pada pola lama), setelah fix diterapkan, setiap chart SHALL hanya melakukan fetch data tepat satu kali saat halaman dimuat, menggunakan jalur tunggal melalui listener `chartFilterChange` yang menggunakan `hasChart()` sebagai gate untuk membedakan init vs update.

**Validates: Requirements 2.1, 2.2, 2.3, 2.4**

Property 2: Preservation — Filter Interactivity Unchanged

_For any_ interaksi pengguna dengan filter (klik tombol periode, custom date range, category filter) yang tidak terkait dengan inisialisasi pertama (isBugCondition returns false untuk interaksi ini), chart SHALL merespons dengan `updateChartsFromAPI()` persis seperti sebelum fix, menghasilkan output grafik yang identik.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

---

## Fix Implementation

### Changes Required

Perbaikan terdiri dari dua bagian utama:

**File 1**: `resources/views/components/chart-filter.blade.php`

**Perubahan**: Hapus blok `window.addEventListener('load', ...)` yang mendispatch `chartFilterChange` secara otomatis. Inisialisasi chart kini sepenuhnya menjadi tanggung jawab halaman via listener `chartFilterChange`.

**Sebelum (baris yang dihapus):**
```javascript
// Auto-dispatch default filter saat halaman load agar chart langsung terinisialisasi
const defaultFilterType = '{{ $defaultFilter }}';
if (defaultFilterType && defaultFilterType !== 'custom') {
    // Tunggu DOM + script lain selesai, lalu dispatch
    window.addEventListener('load', function () {
        document.dispatchEvent(new CustomEvent('chartFilterChange', {
            detail: {
                filterId: filterId,
                filterType: defaultFilterType,
                startDate: null,
                endDate: null,
                categoryId: ''
            }
        }));
    });
}
```

**Pengganti (dispatch di DOMContentLoaded, bukan window.load):**
```javascript
// Dispatch default filter saat DOMContentLoaded agar chart terinisialisasi segera
const defaultFilterType = '{{ $defaultFilter }}';
if (defaultFilterType && defaultFilterType !== 'custom') {
    document.addEventListener('DOMContentLoaded', function () {
        document.dispatchEvent(new CustomEvent('chartFilterChange', {
            detail: {
                filterId: filterId,
                filterType: defaultFilterType,
                startDate: null,
                endDate: null,
                categoryId: ''
            }
        }));
    });
}
```

---

**File 2–15**: Semua halaman yang menggunakan pola lama (memanggil `initXxxCharts()` langsung di `DOMContentLoaded`)

**Pola LAMA (dihapus):**
```javascript
document.addEventListener('DOMContentLoaded', function () {
    initXxxCharts({ filter_type: 'month' }); // ← dihapus

    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'xxxChartFilter') {
            updateXxxCharts({ ... });           // ← hanya update, tidak pernah init
        }
    });
});
```

**Pola BARU (mengikuti pola members):**
```javascript
document.addEventListener('DOMContentLoaded', function () {
    // Tidak ada init langsung — chart diinisialisasi via listener chartFilterChange
    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'xxxChartFilter') {
            const filters = { filter_type: e.detail.filterType };
            if (e.detail.filterType === 'custom') {
                filters.start_date = e.detail.startDate;
                filters.end_date   = e.detail.endDate;
            }
            if (!xxxChartManager.hasChart('xxxBarChart')) {
                initXxxCharts(filters);    // init pertama kali
            } else {
                updateXxxCharts(filters);  // update berikutnya
            }
        }
    });
});
```

**Daftar file yang perlu diubah:**
1. `resources/views/components/chart-filter.blade.php` — ganti `window.load` → `DOMContentLoaded`
2. `resources/views/admin/kontrak/index.blade.php` — hapus direct init, tambah `hasChart()` gate
3. `resources/views/admin/kendaraan/index.blade.php` — hapus direct init, tambah `hasChart()` gate
4. `resources/views/admin/invoice/index.blade.php` — hapus direct init, tambah `hasChart()` gate (sudah ada `invoiceChartsInitialized` flag, distandarisasi ke `hasChart()`)
5. `resources/views/admin/history/index.blade.php` — hapus direct init, tambah `hasChart()` gate
6. `resources/views/admin/rental/index.blade.php` — hapus direct init, tambah `hasChart()` gate
7. `resources/views/admin/penawaran/index.blade.php` — hapus direct init, tambah `hasChart()` gate
8. `resources/views/admin/asuransi/index.blade.php` — hapus direct init, tambah `hasChart()` gate
9. `resources/views/admin/asuransi/asuransi_kendaraan.blade.php` — hapus direct init, tambah `hasChart()` gate
10. `resources/views/admin/asuransi/history.blade.php` — hapus direct init, tambah `hasChart()` gate
11. `resources/views/admin/asuransi/jenis_asuransi.blade.php` — hapus direct init, tambah `hasChart()` gate
12. `resources/views/admin/gps/gps_kendaraan.blade.php` — hapus direct init, tambah `hasChart()` gate
13. `resources/views/admin/data_leasing/index.blade.php` — hapus direct init, tambah `hasChart()` gate + hapus duplikasi komponen
14. `resources/views/admin/keuangan/index.blade.php` — refaktor 3 chart manager (keuangan, aging-ap, aging-ar) menggunakan `hasChart()` gate
15. `resources/views/admin/summary/index.blade.php` — hapus direct init, tambah `hasChart()` gate

**Catatan khusus `keuangan/index.blade.php`:**
Halaman ini punya lazy-init (AP/AR hanya init saat tab dibuka pertama kali). Pola ini dipertahankan dengan tetap menggunakan flag `agingApChartInited` / `agingArChartInited` untuk chart AP/AR, dan mengganti init cashflow ke `hasChart()` gate.

---

## Testing Strategy

### Validation Approach

Strategi pengujian dua fase: pertama verifikasi bug ada di kode unfixed (exploratory), lalu verifikasi fix bekerja dan tidak merusak fungsionalitas yang ada (fix checking + preservation checking).

### Exploratory Bug Condition Checking

**Goal**: Konfirmasi bahwa double-init memang terjadi sebelum fix diterapkan.

**Test Plan**: Tambahkan `console.count('chart-fetch')` di `fetchChartData()` pada `chart-manager.js`, muat halaman kontrak/history/rental, dan verifikasi bahwa counter mencapai angka > jumlah chart di halaman (berarti double-fetch).

**Test Cases**:
1. **Kontrak halaman** — muat halaman, periksa network tab di browser DevTools → harus muncul 2 request ke `/admin/chart-data/kontrak` (akan gagal pada unfixed code, hanya 1 yang diharapkan setelah fix)
2. **History halaman** — sama seperti di atas dengan endpoint `/admin/chart-data/history`
3. **GPS halaman** — sama dengan endpoint `/admin/chart-data/gps-kendaraan`
4. **Members halaman (kontrol)** — harus hanya muncul 1 request, sudah benar sebelum fix

**Expected Counterexamples (pada unfixed code)**:
- Network tab menunjukkan 2 request identik ke endpoint chart dalam interval < 500ms
- Chart tampak "berkedip" atau menampilkan loading overlay dua kali berturut-turut

### Fix Checking

**Goal**: Setelah fix diterapkan, pastikan setiap chart hanya di-fetch satu kali per load halaman.

**Pseudocode:**
```
FOR ALL halaman WHERE isBugCondition(halaman) DO
  result := loadPage_fixed(halaman)
  ASSERT result.chartFetchCount = jumlahChartDiHalaman  // 1x per chart, bukan 2x
  AND result.chartMunculSetelahDOMContentLoaded = true
  AND result.tidakAdaDoubleLoading = true
END FOR
```

### Preservation Checking

**Goal**: Pastikan interaksi filter pengguna (klik tombol, custom range) tetap bekerja persis sama.

**Pseudocode:**
```
FOR ALL interaksi WHERE NOT isBugCondition(interaksi) DO
  ASSERT filterKlik_original(interaksi) = filterKlik_fixed(interaksi)
  // Data chart yang ditampilkan identik untuk filter yang sama
END FOR
```

**Testing Approach**: Property-based testing disarankan untuk preservation checking karena menghasilkan kombinasi filter (filterType × filterId × range) secara otomatis dan memastikan setiap kombinasi menghasilkan request yang benar.

**Test Cases**:
1. **Filter Button Preservation** — klik "Bulan Ini" pada halaman yang sudah difix → harus memicu `updateChartsFromAPI` (bukan `initChartsFromAPI` lagi) dan memperbarui chart dengan data bulan ini
2. **Custom Range Preservation** — pilih custom date range dan klik Terapkan → harus mendispatch `chartFilterChange` dengan `filterType: 'custom'` dan tanggal yang benar
3. **Default Filter Preservation** — muat halaman kontrak (defaultFilter='month') → chart harus tampil dengan data bulan ini tanpa interaksi pengguna
4. **Lazy-Init Preservation (keuangan)** — buka tab Aging AP/AR untuk pertama kalinya → chart harus tetap baru diinisialisasi saat tab dibuka

### Unit Tests

- Verifikasi `hasChart('canvasId')` mengembalikan `false` sebelum init dan `true` setelah init
- Verifikasi listener `chartFilterChange` memanggil `initChartsFromAPI` saat `hasChart()` = false
- Verifikasi listener `chartFilterChange` memanggil `updateChartsFromAPI` saat `hasChart()` = true
- Verifikasi `chart-filter.blade.php` tidak mendispatch `chartFilterChange` via `window.load` setelah fix

### Property-Based Tests

- Generate semua kombinasi (filterId, filterType) untuk setiap halaman dan verifikasi tepat satu fetch per load
- Generate random urutan interaksi filter dan verifikasi chart selalu menampilkan data sesuai filter terakhir
- Verifikasi `hasChart()` gate selalu konsisten — tidak pernah memanggil `init` dua kali untuk canvas yang sama

### Integration Tests

- Muat halaman, verifikasi chart tampil tanpa double-loading, klik filter, verifikasi chart update
- Tambah data baru (misal tambah kontrak) → reload halaman → verifikasi chart langsung tampil tanpa delay `window.load`
- Verifikasi halaman `keuangan` dengan switching tab AP/AR tetap lazy-init dengan benar
- Verifikasi halaman `members` (pola referensi) tidak berubah perilakunya setelah fix
