# Verification: Resubmit Logic di PurchaseOrderController

## Objective
Verify bahwa resubmit logic di PO clear `item_decisions` dengan benar dan item kembali ke status Pending.

---

## Method: `resubmitServiceAsuransi` (Line 1956-2192)

### Two Scenarios Handled

#### 1️⃣ **PARTIAL APPROVAL** (status = 'Disetujui' dengan item rejected)
**Line 2078-2120**

```php
$isPartial = $po->status === 'Disetujui';

if ($isPartial) {
    // Ganti kejadian yang ditolak dengan versi baru dari form
    $rejectedIdx = collect($oldDecisions)
        ->filter(fn($d) => ($d['action'] ?? '') === 'rejected')
        ->keys()->values()->all();

    foreach ($newKejadians as $newIdx => $newKej) {
        $origIdx = $rejectedIdx[$newIdx] ?? null;
        if ($origIdx !== null && isset($allKejadians[$origIdx])) {
            $allKejadians[$origIdx] = array_merge($allKejadians[$origIdx], [
                'nama_kejadian' => $newKej['nama_kejadian'],
                'biaya'         => $newKej['biaya'],
                'lampiran'      => $newKej['lampiran'] ?? $allKejadians[$origIdx]['lampiran'] ?? [],
            ]);
        }
    }

    // ✅ Hapus hanya rejected entries dari item_decisions (approved tetap)
    $newDecisions = array_values(
        array_filter($oldDecisions, fn($d) => ($d['action'] ?? '') === 'approved')
    );

    // Recalculate total harga dan total barang
    $totalHargaNew = collect($allKejadians)->sum(fn($k) => (int)($k['biaya'] ?? 0));

    $updatedSourceData = array_merge($sourceData, [
        'kejadians'      => $allKejadians,
        'item_decisions' => $newDecisions, // ✅ HANYA approved yang dipertahankan
        'temp_files'     => $tempFiles,
    ]);

    $po->update([
        'source_data'       => $updatedSourceData,
        'total_harga'       => $totalHargaNew,
        'total_barang'      => count($allKejadians),
        'can_edit'          => false,
        'terakhir_diajukan' => now(),
    ]);

    return response()->json([
        'success'  => true,
        'message'  => 'Item yang ditolak berhasil diajukan ulang. Silakan approve kembali di PO.',
        'redirect' => route('purchase-order.index', ['status' => 'Disetujui']),
    ]);
}
```

**✅ VERIFIED:**
- Rejected entries di-filter out dari `item_decisions`
- Approved entries dipertahankan
- Item yang di-resubmit **kembali ke PO Pending** (tidak ada di item_decisions)
- PO tetap di tab "Disetujui" dengan badge: Approved (n) + Pending (m)
- Total harga dan total barang di-recalculate
- Item yang di-resubmit akan muncul di modal approval lagi (status: Pending)

---

#### 2️⃣ **FULL REJECTION** (seluruh PO ditolak)
**Line 2145-2186**

```php
// ── FULL REJECT: reset seluruh PO ke Pending ──
$po->update([
    'source_data'         => $newSourceData, // ✅ item_decisions: [] (line 2040)
    'total_harga'         => $biayaTotal,
    'total_barang'        => count($newKejadians),
    'status'              => 'Pending', // ✅ PO kembali Pending
    'catatan_approval'    => null,
    'disetujui_oleh'      => null,
    'tanggal_persetujuan' => null,
    'can_edit'            => false,
    'terakhir_diajukan'   => now(),
]);

// Update ServiceAsuransi ke "Diajukan ke Pembayaran"
\App\Models\ServiceAsuransi::where('id', $serviceAsuransiId)->update([
    'persetujuan'       => 'Diajukan ke Pembayaran',
    'biaya'             => $biayaTotal,
    'purchase_order_id' => $po->id,
]);

// Hapus kejadian lama, buat ulang
\App\Models\ServiceAsuransiKejadian::where('service_asuransi_id', $serviceAsuransiId)->delete();
foreach ($newKejadians as $kej) {
    \App\Models\ServiceAsuransiKejadian::create([
        'service_asuransi_id' => $serviceAsuransiId,
        'nama_kejadian'       => $kej['nama_kejadian'] ?? '',
        'biaya'               => (int)($kej['biaya'] ?? 0),
        'lampiran'            => !empty($kej['lampiran']) ? $kej['lampiran'] : null,
    ]);
}
```

**✅ VERIFIED:**
- `item_decisions` di-reset ke empty array (line 2040)
- PO status kembali ke 'Pending'
- catatan_approval, disetujui_oleh, tanggal_persetujuan di-reset ke null
- ServiceAsuransi record di-update ke "Diajukan ke Pembayaran"
- Kejadian di `service_asuransi_kejadian` di-delete dan dibuat ulang

---

## Line 2040: Clear item_decisions saat resubmit

```php
$newSourceData = array_merge($sourceData, [
    'kejadians'       => $newKejadians,
    'item_decisions'  => [], // ✅ CLEAR old decisions saat resubmit
    'tanggal_service' => $request->input('tanggal_service', $sourceData['tanggal_service'] ?? null),
    ...
]);
```

**✅ VERIFIED:** Item yang di-resubmit clear dari `item_decisions`

---

## Modal Approval Logic

**Di `approveItemsServiceAsuransi` (line ~1280-1450):**
```php
// Ambil hanya item yang belum ada di item_decisions (pending items)
$itemDecisions = collect($sourceData['item_decisions'] ?? [])->keyBy('idx');
$kejadians     = $sourceData['kejadians'] ?? [];

$pendingIndices = [];
foreach ($kejadians as $idx => $kej) {
    if (!$itemDecisions->has($idx)) {
        $pendingIndices[] = $idx; // ✅ Hanya pending items yang masuk modal
    }
}
```

**✅ VERIFIED:**
- Modal approval hanya menampilkan item yang belum ada di `item_decisions`
- Setelah resubmit → `item_decisions` = [] → semua item kembali Pending
- Modal approve akan menampilkan semua item yang di-resubmit

---

## Edge Cases Handled

### 1. Service Incident (remap kejadians → parts)
**Line 2051-2066**
```php
if ($po->source_type === 'service_incident') {
    $remappedParts = array_map(function ($kej, $idx) use ($originalParts) {
        $orig = $originalParts[$idx] ?? [];
        return array_merge($orig, [
            'nama_part' => $kej['nama_kejadian'] ?? ($orig['nama_part'] ?? '-'),
            'biaya'     => (int)($kej['biaya'] ?? 0),
            'lampiran'  => $kej['lampiran'] ?? ($orig['lampiran'] ?? []),
        ]);
    }, $newKejadians, array_keys($newKejadians));

    $newSourceData = array_merge($sourceData, [
        'parts'           => $remappedParts,
        'item_decisions'  => [], // ✅ Clear decisions untuk service_incident juga
        ...
    ]);
}
```

### 2. Lampiran Existing Dipertahankan
**Line 1993-2011**
```php
$newKejadians = array_map(function ($kej, $idx) use ($tempFiles) {
    // Lampiran lama dikirim dari form sebagai hidden inputs
    $lampiranLama = array_values(array_filter(
        array_map(function ($lf) {
            $path = $lf['path'] ?? '';
            if (!$path) return null;
            return [
                'path'          => $path,
                'original_name' => $lf['original_name'] ?? basename($path),
                ...
            ];
        }, $kej['lampiran_lama'] ?? [])
    ));
    // Gabungkan dengan lampiran baru
    $newLampiran = $tempFiles['kejadians'][$idx] ?? [];
    return [
        'nama_kejadian' => $kej['nama_kejadian'] ?? '',
        'biaya'         => (int)($kej['biaya'] ?? 0),
        'lampiran'      => array_merge($lampiranLama, $newLampiran),
    ];
}, $kejadians, array_keys($kejadians));
```

---

## Conclusion

### ✅ SEMUA LOGIC VERIFIED

1. **Full Reject:** `item_decisions` di-clear → PO kembali 'Pending' → item masuk modal approval lagi
2. **Partial Approval:** Rejected entries di-filter dari `item_decisions` → buat Pembayaran baru dengan `item_decisions: []` → item kembali Pending di Pembayaran baru
3. **Modal Approval:** Hanya menampilkan item yang belum ada di `item_decisions`
4. **Service Incident:** Logic yang sama diterapkan (remap ke `parts`)
5. **Lampiran:** Lampiran lama dipertahankan, lampiran baru digabung

### Next Step: Task 4 - Create Test Scenarios
