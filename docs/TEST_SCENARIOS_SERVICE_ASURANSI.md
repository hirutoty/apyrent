# Test Scenarios: Flow Service Asuransi (Input → PO → Pembayaran → Transfer)

## Objective
Memverify bahwa flow Service Asuransi aman dari input → PO (approve/reject per-kejadian) → Pembayaran (approve/reject per-kejadian) → transfer ke tabel `service_asuransi`.

---

## Test Scenario 1: Happy Path — Full Approval di PO dan Pembayaran

### Steps
1. **Input Service Asuransi**
   - Masuk ke halaman Service Asuransi → Create
   - Pilih kendaraan
   - Input 2 kejadian:
     - Kejadian 1: "Ganti Oli" - Rp 500.000
     - Kejadian 2: "Ganti Ban" - Rp 1.500.000
   - Upload lampiran untuk kedua kejadian
   - Submit

2. **Verifikasi di PO (Tab Pending)**
   - Buka halaman PO → Tab Pending
   - Cari PO dengan source_type = `service_asuransi`
   - **Expected:** Badge menampilkan "Pending: 2"
   - Klik tombol "Approve" → modal muncul
   - **Expected:** Modal menampilkan 2 kejadian (Ganti Oli dan Ganti Ban)
   - Centang kedua kejadian → Approve
   - **Expected:** PO masuk ke tab "Disetujui" dengan badge "Approved: 2"

3. **Verifikasi di Pembayaran (Tab Diajukan)**
   - Buka halaman Pembayaran → Tab Diajukan
   - Cari PR dengan source_type = `service_asuransi`
   - **Expected:** Badge menampilkan "Pending: 2" (item belum diapprove di Pembayaran)
   - Klik tombol "Approve" → modal muncul
   - **Expected:** Modal menampilkan 2 kejadian (Ganti Oli dan Ganti Ban)
   - Centang kedua kejadian → Approve
   - **Expected:** PR masuk ke tab "Disetujui" dengan badge "Approved: 2"

4. **Verifikasi Transfer ke tabel `service_asuransi`**
   - Query database:
     ```sql
     SELECT * FROM service_asuransi WHERE purchase_order_id = [PO_ID];
     SELECT * FROM service_asuransi_kejadian WHERE service_asuransi_id = [SERVICE_ASURANSI_ID];
     ```
   - **Expected:**
     - 1 record di `service_asuransi` dengan `persetujuan` = 'Selesai'
     - 2 records di `service_asuransi_kejadian` (Ganti Oli dan Ganti Ban)
     - Total biaya = Rp 2.000.000

### Evidence Checklist
- [ ] Screenshot modal approval di PO (2 kejadian visible)
- [ ] Screenshot badge di PO tab Disetujui (Approved: 2)
- [ ] Screenshot modal approval di Pembayaran (2 kejadian visible)
- [ ] Screenshot badge di Pembayaran tab Disetujui (Approved: 2)
- [ ] Screenshot query result: `service_asuransi` table
- [ ] Screenshot query result: `service_asuransi_kejadian` table

---

## Test Scenario 2: Partial Approval di PO → Resubmit → Full Approval

### Steps
1. **Input Service Asuransi**
   - Input 2 kejadian:
     - Kejadian 1: "Ganti Oli" - Rp 500.000
     - Kejadian 2: "Ganti Ban" - Rp 1.500.000
   - Submit

2. **PO: Approve 1, Tolak 1**
   - Buka PO → Tab Pending
   - **Expected:** Badge "Pending: 2"
   - Klik Approve → modal muncul dengan 2 kejadian
   - Centang hanya "Ganti Oli" → Reject "Ganti Ban" dengan catatan "Biaya terlalu tinggi"
   - Submit
   - **Expected:**
     - Tab "Disetujui" menampilkan PO dengan badge:
       - ✅ Approved: 1 (Ganti Oli)
       - ⏳ Pending: 0
       - ❌ Rejected: 0 (rejected item tidak termasuk di PO ini)
     - Tab "Ditolak" menampilkan PO **dengan nomor yang sama** dengan badge:
       - ✅ Approved: 0
       - ⏳ Pending: 0
       - ❌ Rejected: 1 (Ganti Ban)

3. **PO: Ajukan Ulang Kejadian yang Ditolak**
   - Di tab "Disetujui" → klik tombol "Ajukan Ulang" (untuk item rejected)
   - Modal muncul → edit biaya "Ganti Ban" menjadi Rp 1.200.000
   - Submit
   - **Expected:**
     - PO **pindah ke tab Pending** (status reset ke 'Pending')
     - `item_decisions` di-clear SEMUA (approved entries juga dihapus)
     - Badge di tab Pending: ⏳ Pending: 2
     - Tidak ada badge Approved (semua item kembali pending)

4. **PO: Approve Semua Kejadian (Ulang)**
   - Di tab Pending → klik Approve
   - Modal muncul → **menampilkan KEDUA kejadian** (Ganti Oli + Ganti Ban)
   - Centang kedua kejadian → Approve
   - **Expected:**
     - PO masuk tab "Disetujui" dengan badge "Approved: 2"

5. **Pembayaran: Approve Kedua Kejadian**
   - Buka Pembayaran → Tab Diajukan
   - **Expected:** Badge "Pending: 2"
   - Klik Approve → centang kedua kejadian → Submit
   - **Expected:** PR masuk tab "Disetujui" dengan badge "Approved: 2"

6. **Verifikasi Transfer**
   - Query database: `service_asuransi` dan `service_asuransi_kejadian`
   - **Expected:**
     - 2 records di `service_asuransi_kejadian` (Ganti Oli + Ganti Ban dengan biaya Rp 1.200.000)
     - Total biaya = Rp 1.700.000

### Evidence Checklist
- [ ] Screenshot PO tab Disetujui dengan badge "Approved: 1, Rejected: 1" (sebelum resubmit)
- [ ] Screenshot modal "Ajukan Ulang" dengan edit biaya
- [ ] Screenshot PO pindah ke tab Pending setelah resubmit
- [ ] Screenshot badge "Pending: 2" di tab Pending (semua item kembali pending)
- [ ] Screenshot modal approval menampilkan KEDUA kejadian (Ganti Oli + Ganti Ban)
- [ ] Screenshot badge "Approved: 2" setelah approve ulang
- [ ] Screenshot Pembayaran dengan badge "Pending: 2"
- [ ] Screenshot query result final: 2 kejadian di `service_asuransi_kejadian`

---

## Test Scenario 3: Full Rejection di PO → Resubmit → Approval

### Steps
1. **Input Service Asuransi**
   - Input 2 kejadian:
     - Kejadian 1: "Ganti Oli" - Rp 500.000
     - Kejadian 2: "Ganti Ban" - Rp 1.500.000
   - Submit

2. **PO: Tolak Semua**
   - Buka PO → Tab Pending
   - Klik Approve → modal muncul dengan 2 kejadian
   - **Tolak kedua kejadian** dengan catatan "Budget tidak cukup"
   - Submit
   - **Expected:**
     - PO masuk tab "Ditolak" dengan badge "Rejected: 2"
     - PO **tidak** masuk tab "Disetujui"

3. **PO: Ajukan Ulang**
   - Di tab "Ditolak" → klik "Ajukan Ulang"
   - Modal muncul → edit kedua biaya:
     - Ganti Oli: Rp 400.000
     - Ganti Ban: Rp 1.200.000
   - Submit
   - **Expected:**
     - `item_decisions` di-clear
     - PO kembali ke tab "Pending" dengan nomor PO **yang sama**
     - Badge "Pending: 2"

4. **PO: Approve Semua**
   - Klik Approve → modal muncul dengan 2 kejadian
   - Centang kedua kejadian → Approve
   - **Expected:** PO masuk tab "Disetujui" dengan badge "Approved: 2"

5. **Pembayaran: Approve Semua**
   - Buka Pembayaran → Tab Diajukan
   - Badge "Pending: 2"
   - Approve kedua kejadian
   - **Expected:** PR masuk tab "Disetujui" dengan badge "Approved: 2"

6. **Verifikasi Transfer**
   - Query database
   - **Expected:**
     - 2 records di `service_asuransi_kejadian` dengan biaya baru (Rp 400.000 + Rp 1.200.000)
     - Total biaya = Rp 1.600.000

### Evidence Checklist
- [ ] Screenshot PO tab Ditolak dengan badge "Rejected: 2"
- [ ] Screenshot modal "Ajukan Ulang" dengan edit biaya
- [ ] Screenshot PO kembali ke tab Pending dengan nomor PO yang sama
- [ ] Screenshot badge "Pending: 2" setelah resubmit
- [ ] Screenshot badge "Approved: 2" setelah approve
- [ ] Screenshot query result: biaya baru di `service_asuransi_kejadian`

---

## Test Scenario 4: Partial Approval di Pembayaran → Resubmit

### Steps
1. **Setup: PO Full Approved**
   - Input 2 kejadian → Approve semua di PO
   - **Expected:** PR masuk Pembayaran dengan badge "Pending: 2"

2. **Pembayaran: Approve 1, Tolak 1**
   - Buka Pembayaran → Tab Diajukan
   - Klik Approve → modal muncul dengan 2 kejadian
   - Centang "Ganti Oli" → Reject "Ganti Ban" dengan catatan "Perlu konfirmasi ulang"
   - Submit
   - **Expected:**
     - Tab "Disetujui" menampilkan PR dengan badge:
       - ✅ Approved: 1 (Ganti Oli)
       - ❌ Rejected: 0 (rejected item filtered di tab ini)
     - Tab "Ditolak" menampilkan PR **dengan nomor yang sama** dengan badge:
       - ✅ Approved: 0
       - ❌ Rejected: 1 (Ganti Ban)

3. **Pembayaran: Ajukan Ulang Item Rejected**
   - Di tab "Ditolak" → klik "Ajukan Ulang"
   - Modal muncul → edit biaya "Ganti Ban"
   - Submit
   - **Expected:**
     - **Pembayaran baru dibuat** dengan status "Diajukan" (TIDAK kembali ke PO)
     - Badge di Pembayaran baru: "Pending: 1" (hanya Ganti Ban)
     - Pembayaran lama tetap di tab "Disetujui" dengan badge "Approved: 1"

4. **Pembayaran Baru: Approve**
   - Approve item "Ganti Ban" di Pembayaran baru
   - **Expected:** Pembayaran baru masuk tab "Disetujui" dengan badge "Approved: 1"

5. **Verifikasi Transfer**
   - Query database
   - **Expected:**
     - 2 records di `service_asuransi_kejadian` (Ganti Oli dari Pembayaran lama + Ganti Ban dari Pembayaran baru)
     - Total biaya = sum kedua biaya

### Evidence Checklist
- [ ] Screenshot Pembayaran tab Disetujui dengan badge "Approved: 1" (partial approval)
- [ ] Screenshot Pembayaran tab Ditolak dengan badge "Rejected: 1"
- [ ] Screenshot modal "Ajukan Ulang" di Pembayaran
- [ ] Screenshot Pembayaran baru dibuat (no PR berbeda) dengan badge "Pending: 1"
- [ ] Screenshot Pembayaran baru di tab Disetujui setelah approve
- [ ] Screenshot query result: 2 kejadian dari 2 Pembayaran berbeda

---

## Additional Verification Points

### Badge Rendering
- [ ] Badge warna hijau (approved) muncul dengan icon ✓
- [ ] Badge warna kuning (pending) muncul dengan icon ⏱
- [ ] Badge warna merah (rejected) muncul dengan icon ✗
- [ ] Badge hanya muncul untuk source_type yang support per-item: `service_asuransi`, `service_part`, `service_incident`, `gps`, `gps_perpanjang`
- [ ] Fallback ke item count untuk source_type lain (e.g., non-per-item types)

### Modal Approval
- [ ] Modal hanya menampilkan item yang pending (belum ada di `item_decisions`)
- [ ] Setelah approve/reject → modal tidak menampilkan item yang sudah diproses
- [ ] Resubmit → item muncul kembali di modal

### Database Integrity
- [ ] Hanya kejadian yang approved masuk ke `service_asuransi_kejadian`
- [ ] Kejadian yang rejected tidak masuk database sampai di-resubmit dan diapprove
- [ ] Lampiran (bukti) tersimpan dengan benar di setiap kejadian
- [ ] `purchase_order_id` dan `pembayaran_id` valid di `service_asuransi`

---

## Test Execution Log

| Scenario | Executed By | Date | Status | Notes |
|----------|-------------|------|--------|-------|
| 1. Happy Path | | | ⏳ Pending | |
| 2. Partial Approval PO | | | ⏳ Pending | |
| 3. Full Rejection PO | | | ⏳ Pending | |
| 4. Partial Approval Pembayaran | | | ⏳ Pending | |

---

## Bug Report Template

**Bug ID:** [AUTO-INCREMENT]  
**Scenario:** [Scenario Number & Name]  
**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Result:**  
[Describe expected behavior]

**Actual Result:**  
[Describe actual behavior]

**Screenshot:**  
[Attach screenshot]

**Severity:** [Critical / High / Medium / Low]  
**Status:** [Open / In Progress / Fixed / Closed]

---

## Success Criteria

✅ **All scenarios pass without errors**  
✅ **Badge per-item rendered correctly in Pembayaran index**  
✅ **Resubmit logic clear item_decisions with no side effects**  
✅ **Only approved items transferred to `service_asuransi` table**  
✅ **No data loss during resubmit flow**  
✅ **Lampiran/bukti preserved across resubmit**
