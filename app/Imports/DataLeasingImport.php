<?php

namespace App\Imports;

use App\Models\DataLeasing;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class DataLeasingImport implements
    ToModel,
    WithHeadingRow,
    SkipsOnError,
    SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    /** Baris yang berhasil diimport / diupdate */
    public int $importedCount = 0;

    /** Baris yang di-skip beserta alasannya */
    public array $skippedRows = [];

    public function model(array $row): ?DataLeasing
    {
        $rowNum = ($this->importedCount + count($this->skippedRows) + 2);

        // Skip baris kosong
        if (empty(array_filter($row))) {
            return null;
        }

        // ── Resolve field dari dua versi template ─────────────────────
        //
        // Template v2 (baru) — header human-readable, Maatwebsite slug-kan:
        //   "No Kontrak"            → no_kontrak         ✅ sama
        //   "Mobil / Merk"          → mobil_merk         ← baru
        //   "Angsuran / Bulan (Rp)" → angsuran_bulan_rp  ← baru
        //   "User Leasing"          → user_leasing       ✅ sama
        //   "Asuransi Leasing"      → asuransi_leasing   ✅ sama
        //   kolom auto (No, Cicilan, Total, Sisa, Status,
        //   Serial Kontrak, Dibuat Pada) → diabaikan
        //
        // Template v1 (lama) — header snake_case langsung:
        //   "mobil" → mobil, "angsuran_per_bulan" → angsuran_per_bulan
        //
        // Fallback: v2 key → v1 key → kosong

        $noKontrakInput = trim($row['no_kontrak'] ?? '');
        $mobil          = trim($row['mobil_merk']       ?? $row['mobil']              ?? '');
        $angsuranRaw    = $row['angsuran_bulan_rp']     ?? $row['angsuran_per_bulan'] ?? null;
        $userLeasing    = trim($row['user_leasing']     ?? '');
        $asuransiLeas   = trim($row['asuransi_leasing'] ?? '');

        // ── Validasi: no_kontrak wajib ────────────────────────────────
        if ($noKontrakInput === '') {
            $this->skippedRows[] = [
                'row'    => $rowNum,
                'data'   => $mobil ?: '(kosong)',
                'errors' => ['no_kontrak wajib diisi — isi nomor kontrak dari dokumen fisik'],
            ];
            return null;
        }

        // ── Validasi per baris ────────────────────────────────────────
        $errors = [];

        // angsuran: harus numerik — strip separator ribuan jika ada (misal "5,000,000")
        if ($angsuranRaw !== null && $angsuranRaw !== '') {
            $angsuranClean = str_replace([',', '.', ' '], '', (string) $angsuranRaw);
            if (!is_numeric($angsuranClean)) {
                $errors[] = 'Angsuran / Bulan harus berupa angka';
            }
            $angsuranInt = (int) $angsuranClean;
        } else {
            $angsuranInt = 0;
        }

        // jatuh_tempo: 1-31
        // Export data menulis "Tgl 15" — strip prefix "Tgl " jika ada
        $jatuhTempoRaw = $row['jatuh_tempo'] ?? null;
        $jatuhTempo    = null;
        if ($jatuhTempoRaw !== null && $jatuhTempoRaw !== '') {
            // Hilangkan prefix "Tgl " (case-insensitive) jika ada
            $jatuhTempoClean = trim(preg_replace('/^tgl\s*/i', '', (string) $jatuhTempoRaw));
            if (!is_numeric($jatuhTempoClean) || (int) $jatuhTempoClean < 1 || (int) $jatuhTempoClean > 31) {
                $errors[] = 'Jatuh Tempo harus angka 1-31 (atau format "Tgl 15")';
            } else {
                $jatuhTempo = $jatuhTempoClean;
            }
        }

        // periode_mulai
        $periodeMulai = null;
        if (!empty($row['periode_mulai'])) {
            $periodeMulai = $this->parseExcelDate($row['periode_mulai'], $rowNum, 'periode_mulai', $errors);
        }

        // periode_selesai
        $periodeSelesai = null;
        if (!empty($row['periode_selesai'])) {
            $periodeSelesai = $this->parseExcelDate($row['periode_selesai'], $rowNum, 'periode_selesai', $errors);
        }

        // validasi urutan periode
        if ($periodeMulai && $periodeSelesai && $periodeSelesai < $periodeMulai) {
            $errors[] = 'periode_selesai harus lebih besar atau sama dengan periode_mulai';
        }

        // Ada error? Skip baris
        if (!empty($errors)) {
            $this->skippedRows[] = [
                'row'    => $rowNum,
                'data'   => $noKontrakInput,
                'errors' => $errors,
            ];
            return null;
        }

        // ── Susun data ─────────────────────────────────────────────────
        // Kolom "Cicilan" di Excel berisi "36x sisa" (dari export data) atau angka murni (dari template)
        // Strip suffix "x sisa" / "x" lalu ambil angkanya
        $cicilanRaw    = $row['cicilan'] ?? null;
        $jumlahCicilan = null;
        if ($cicilanRaw !== null && (string) $cicilanRaw !== '') {
            // Hilangkan semua karakter non-digit
            $cicilanClean = preg_replace('/[^0-9]/', '', (string) $cicilanRaw);
            if ($cicilanClean !== '' && (int) $cicilanClean > 0) {
                $jumlahCicilan = (int) $cicilanClean;
            }
        }

        $data = [
            'no_kontrak'         => $noKontrakInput,
            'mobil'              => $mobil,
            'tahun'              => trim($row['tahun']            ?? ''),
            'nopol'              => trim($row['nopol']            ?? ''),
            'user_leasing'       => $userLeasing,
            'angsuran_per_bulan' => $angsuranInt,
            'jatuh_tempo'        => $jatuhTempo ? (int) $jatuhTempo : null,
            'periode_mulai'      => $periodeMulai,
            'periode_selesai'    => $periodeSelesai,
            'jumlah_cicilan'     => $jumlahCicilan,  // null = hitung otomatis dari periode
            'personal_account'   => trim($row['personal_account']  ?? ''),
            'sumber_dana_debit'  => trim($row['sumber_dana_debit'] ?? ''),
            'cara_bayar'         => trim($row['cara_bayar']        ?? ''),
            'asuransi_leasing'   => $asuransiLeas,
            'data_kontrak_id'    => null,
        ];

        // ── Upsert: no_kontrak sudah ada → UPDATE, belum ada → INSERT ──
        $existing = DataLeasing::where('no_kontrak', $noKontrakInput)->first();

        if ($existing) {
            // Pertahankan data_kontrak_id yang sudah ada, jangan ditimpa null
            $data['data_kontrak_id'] = $existing->data_kontrak_id;
            $existing->update($data);

            $this->skippedRows[] = [
                'row'    => $rowNum,
                'data'   => $noKontrakInput,
                'errors' => ['Diperbarui (update existing)'],
                'warn'   => true,
            ];
            $this->importedCount++;
            return null; // sudah di-update manual, jangan return model baru
        }

        // INSERT baru
        $this->importedCount++;
        return new DataLeasing($data);
    }

    /**
     * Parse tanggal dari Excel — handle numeric serial atau string DD/MM/YYYY.
     */
    private function parseExcelDate(mixed $value, int $rowNum, string $field, array &$errors): ?string
    {
        $strValue = trim((string) $value);

        if ($strValue === '') return null;

        // Kasus 1: numeric serial Excel (misal: 46023.0)
        if (is_numeric($strValue)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $strValue);
                return Carbon::instance($date)->format('Y-m-d');
            } catch (\Exception $e) {
                $errors[] = "{$field} tidak bisa dibaca sebagai tanggal serial Excel";
                return null;
            }
        }

        // Kasus 2: string DD/MM/YYYY
        try {
            return Carbon::createFromFormat('d/m/Y', $strValue)->format('Y-m-d');
        } catch (\Exception $e) {
            // Fallback: parse bebas
            try {
                return Carbon::parse($strValue)->format('Y-m-d');
            } catch (\Exception $e2) {
                $errors[] = "{$field} format tidak valid (gunakan DD/MM/YYYY, contoh: 01/01/2026)";
                return null;
            }
        }
    }
}
