# Bugfix Requirements Document

## Introduction

Chart pada halaman-halaman yang menggunakan komponen `x-chart-filter` melakukan dua kali fetch data (double-init) saat halaman dimuat, sehingga chart terlihat loading dua kali dan membutuhkan waktu lebih lama untuk tampil — terutama setelah operasi tambah/edit/hapus data yang memicu page reload. Penyebab utamanya adalah `chart-filter.blade.php` mendispatch ulang event `chartFilterChange` melalui `window.addEventListener('load', ...)` setelah halaman sudah selesai dimuat, padahal sebagian besar halaman sudah memanggil `initXxxCharts()` secara langsung di dalam `DOMContentLoaded`.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN halaman dengan komponen `x-chart-filter` dimuat THEN sistem melakukan dua kali fetch chart data: sekali dari `DOMContentLoaded` yang memanggil `initXxxCharts()` langsung, dan sekali lagi dari `window.load` yang mendispatch event `chartFilterChange`

1.2 WHEN `window.load` selesai dan `chartFilterChange` di-dispatch ulang oleh `chart-filter.blade.php` THEN sistem melakukan fetch kedua dan merender ulang chart dari awal, menyebabkan chart terlihat "berkedip" atau loading dua kali

1.3 WHEN pengguna menambah, mengedit, atau menghapus data yang memicu page reload (misalnya tambah kontrak, tambah invoice) THEN chart tidak langsung tampil setelah `DOMContentLoaded` — pengguna harus menunggu `window.load` selesai sebelum chart muncul

1.4 WHEN terdapat beberapa komponen `x-chart-filter` pada halaman yang sama THEN masing-masing instance mendispatch event `chartFilterChange` via `window.load`, sehingga setiap filter memicu fetch yang tidak diperlukan

### Expected Behavior (Correct)

2.1 WHEN halaman dengan komponen `x-chart-filter` dimuat THEN sistem SHALL melakukan tepat satu kali fetch chart data, menggunakan satu jalur inisialisasi saja (tidak ada double-init)

2.2 WHEN event `chartFilterChange` di-dispatch oleh komponen filter THEN sistem SHALL memutuskan apakah perlu `initChartsFromAPI` atau `updateChartsFromAPI` berdasarkan state chart (sudah diinisialisasi atau belum), bukan dari dua sumber event yang berbeda

2.3 WHEN pengguna menambah, mengedit, atau menghapus data yang memicu page reload THEN chart SHALL langsung tampil segera setelah `DOMContentLoaded` tanpa menunggu `window.load`

2.4 WHEN komponen `x-chart-filter` selesai dirender THEN sistem SHALL TIDAK mendispatch ulang event `chartFilterChange` via `window.load` — inisialisasi chart cukup dipicu satu kali, oleh listener `chartFilterChange` di halaman masing-masing yang menangkap event dari klik tombol filter atau dari dispatch awal yang terjadi di `DOMContentLoaded`

### Unchanged Behavior (Regression Prevention)

3.1 WHEN pengguna mengklik tombol filter periode (Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini) THEN sistem SHALL CONTINUE TO mendispatch event `chartFilterChange` dan memperbarui chart sesuai filter yang dipilih

3.2 WHEN pengguna memilih custom date range dan menekan "Terapkan" THEN sistem SHALL CONTINUE TO mendispatch event `chartFilterChange` dengan `filterType: 'custom'` dan `startDate`/`endDate` yang sesuai

3.3 WHEN halaman pertama kali dimuat tanpa interaksi pengguna THEN sistem SHALL CONTINUE TO menampilkan chart dengan filter default sesuai atribut `defaultFilter` yang dikonfigurasi per halaman (misalnya `year` untuk kendaraan/invoice/members, `month` untuk kontrak/history)

3.4 WHEN komponen `x-chart-filter` memiliki `showCategoryFilter="true"` dan pengguna mengubah dropdown kategori THEN sistem SHALL CONTINUE TO memanggil `applyChartCategoryFilter()` dan mendispatch `chartFilterChange` dengan `categoryId` yang dipilih

3.5 WHEN komponen `x-chart-filter` memiliki `showCustomRange="true"` THEN sistem SHALL CONTINUE TO menampilkan dan menyembunyikan custom date range section saat tombol "Custom" diklik

3.6 WHEN halaman members menggunakan pola `hasChart()` untuk membedakan init vs update THEN sistem SHALL CONTINUE TO menggunakan pola tersebut sebagai referensi untuk halaman-halaman lain yang dimigrasi

3.7 WHEN chart sedang loading data dari API THEN sistem SHALL CONTINUE TO menampilkan loading overlay di atas canvas tanpa menggantikan elemen HTML canvas

3.8 WHEN terjadi error saat fetch data chart THEN sistem SHALL CONTINUE TO menampilkan error overlay pada canvas yang bersangkutan

---

## Bug Condition (Pseudocode)

```pascal
FUNCTION isBugCondition(X)
  INPUT: X of type PageLoad
  OUTPUT: boolean

  // Bug terpicu ketika halaman:
  // (a) menggunakan x-chart-filter, DAN
  // (b) memanggil initXxxCharts() langsung di DOMContentLoaded (bukan hanya via listener chartFilterChange), DAN
  // (c) chart-filter.blade.php mendispatch ulang chartFilterChange via window.load
  RETURN X.hasChartFilter
     AND X.callsInitDirectlyOnDOMContentLoaded
     AND X.chartFilterDispatchesOnWindowLoad
END FUNCTION
```

```pascal
// Property: Fix Checking
FOR ALL X WHERE isBugCondition(X) DO
  result ← loadPage'(X)
  ASSERT result.fetchCount = 1
  AND result.chartAppearsOnDOMContentLoaded = true
  AND result.noDoubleLoading = true
END FOR
```

```pascal
// Property: Preservation Checking
FOR ALL X WHERE NOT isBugCondition(X) DO
  ASSERT loadPage(X) = loadPage'(X)
  // Halaman yang tidak terpengaruh (tidak pakai x-chart-filter) tetap berperilaku sama
END FOR
```
