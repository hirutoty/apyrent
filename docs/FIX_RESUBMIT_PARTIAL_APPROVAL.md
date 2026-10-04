# Fix: Resubmit Item Rejected di PO (Partial Approval)

## Issue
User melaporkan bahwa setelah approve 1 item dan tolak 1 item di PO, lalu ajukan ulang item yang ditolak, item tersebut **tidak masuk ke PO Pending** melainkan langsung masuk ke Pembayaran baru.

## Expected Behavior
Item yang di-reject di PO lalu diajukan ulang **harus kembali ke PO Pending** (status: pending, tidak ada di `item_decisions`), sehingga perlu di-approve lagi di PO sebelum masuk ke Pembayaran.

## Root Cause
Logic resubmit untuk partial approval (line 2078-2120) di `PurchaseOrderController@resubmitServiceAsuransi` membuat **Pembayaran baru** untuk item yang di-resubmit, instead of returning item to PO Pending.

```php
// OLD LOGIC (WRONG)
if ($isPartial) {
    // ... update kejadians dan hapus rejected dari item_decisions
    
    // ❌ WRONG: Buat Pembayaran baru
    $pembayaranBaru = \App\Models\Pembayaran::create([
        'no_pr'           => $noPR,
        'status'          => 'Diajukan',
        'source_data'     => $newSourceForPR, // hanya berisi item rejected
        ...
    ]);
    
    return response()->json([
        'message'  => 'Item yang ditolak berhasil diajukan ulang. Pembayaran ' . $noPR . ' otomatis dibuat.',
        'redirect' => route('purchase-order.index', ['status' => 'Disetujui']),
    ]);
}
```

## Solution
Update logic resubmit untuk partial approval:
1. Update kejadian yang ditolak dengan data baru dari form
2. **Clear SEMUA `item_decisions`** (termasuk approved entries)
3. **Reset status PO ke 'Pending'**
4. Reset catatan_approval, disetujui_oleh, tanggal_persetujuan
5. Recalculate `total_harga` dan `total_barang`
6. Redirect ke tab Pending

```php
// NEW LOGIC (FINAL - APPROVED BY USER)
if ($isPartial) {
    // Update kejadian yang ditolak dengan data baru
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

    // Recalculate total harga dan total barang
    $totalHargaNew = collect($allKejadians)->sum(fn($k) => (int)($k['biaya'] ?? 0));

    $updatedSourceData = array_merge($sourceData, [
        'kejadians'      => $allKejadians,
        'item_decisions' => [], // ✅ Clear ALL decisions (termasuk approved)
        'temp_files'     => $tempFiles,
    ]);

    // ✅ Reset PO ke Pending - semua item harus di-approve ulang
    $po->update([
        'source_data'         => $updatedSourceData,
        'total_harga'         => $totalHargaNew,
        'total_barang'        => count($allKejadians),
        'status'              => 'Pending', // ✅ Reset ke Pending
        'catatan_approval'    => null,
        'disetujui_oleh'      => null,
        'tanggal_persetujuan' => null,
        'can_edit'            => false,
        'terakhir_diajukan'   => now(),
    ]);

    return response()->json([
        'success'  => true,
        'message'  => 'PO berhasil diajukan ulang. Silakan approve semua item kembali.',
        'redirect' => route('purchase-order.index', ['status' => 'Pending']),
    ]);
}
```

## Impact

### Before Fix:
```
1. Input 2 kejadian → PO Pending
2. Approve 1, Tolak 1 → PO Disetujui (badge: Approved 1, Rejected 1)
3. Ajukan ulang item rejected → Pembayaran BARU dibuat ❌
4. PO tetap di tab Disetujui (badge: Approved 1)
5. Item rejected masuk Pembayaran baru (status: Diajukan)
```

### After Fix (FINAL):
```
1. Input 2 kejadian → PO Pending
2. Approve 1, Tolak 1 → PO Disetujui (badge: Approved 1, Rejected 1)
3. Ajukan ulang item rejected → PO kembali ke tab Pending ✅
4. Status PO: Pending ✅
5. item_decisions di-clear (semua item kembali pending) ✅
6. Badge: Pending 2 ✅
7. Approve semua item (Anu + Asu) → PO Disetujui
8. PO masuk Pembayaran dengan 2 kejadian
```

### Key Changes:
- ✅ **PO status reset ke 'Pending'** (tidak tetap Disetujui)
- ✅ **Semua item_decisions di-clear** (approved entries juga dihapus)
- ✅ **Superadmin approve ulang semua item** (dari awal)
- ✅ **PO muncul di tab Pending** (bukan tab Disetujui)
- ✅ **Redirect ke tab Pending** setelah resubmit

## Files Modified
- `app/Http/Controllers/Admin/PurchaseOrderController.php` (line 2078-2120)
- `docs/TEST_SCENARIOS_SERVICE_ASURANSI.md` (test scenario 2 updated)
- `docs/VERIFICATION_RESUBMIT_LOGIC.md` (partial approval section updated)

## Testing

### Manual Test:
1. Input Service Asuransi dengan 2 kejadian
2. Di PO: Approve 1, Tolak 1
3. Verify badge di tab Disetujui: `Approved: 1, Rejected: 1`
4. Klik "Ajukan Ulang" → edit item rejected → submit
5. **Expected:**
   - Badge berubah: `Approved: 1, Pending: 1`
   - Tidak ada Pembayaran baru dibuat
   - Modal approval menampilkan item yang di-resubmit
6. Approve item yang di-resubmit
7. **Expected:**
   - Badge berubah: `Approved: 2`
   - PO masuk Pembayaran dengan 2 kejadian

### Verification:
- [ ] Badge per-item di PO menampilkan count correct
- [ ] Badge per-item di Pembayaran menampilkan count correct
- [ ] Item rejected yang di-resubmit muncul di modal approval
- [ ] Tidak ada Pembayaran baru dibuat saat resubmit di PO
- [ ] Total harga di-recalculate dengan benar
- [ ] Lampiran existing dipertahankan setelah resubmit

## Notes

**Consistency Check:**
- ✅ **PO partial approval resubmit:** Item kembali ke PO Pending (fixed)
- ✅ **PO full rejection resubmit:** PO kembali ke Pending dengan nomor sama (already correct)
- ✅ **Pembayaran partial approval resubmit:** Item kembali ke Pembayaran Pending (already correct, verified in PembayaranController line 3360-3380)
- ✅ **Pembayaran full rejection resubmit:** (not implemented yet, out of scope)

**Design Decision:**
Item rejected di PO yang di-resubmit **harus melewati approval PO lagi** sebelum masuk Pembayaran. Ini untuk memastikan:
1. Konsistensi approval workflow (PO → Pembayaran)
2. Superadmin punya kontrol penuh di level PO
3. Item tidak "skip" approval PO setelah resubmit

---

**Date:** 2026-10-04  
**Reporter:** User  
**Fixed By:** AI Assistant (Kiro)  
**Status:** ✅ Fixed & Documented
