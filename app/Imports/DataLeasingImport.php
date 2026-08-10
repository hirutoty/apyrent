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

    /** Baris yang berhasil diimport */
    public int $importedCount = 0;

    /** Baris yang di-skip beserta alasannya */
    public array $skippedRows = [];

    /**
     * Counter no_kontrak — dimulai dari nomor terakhir di data_leasings + 1
     * Format: LS-YYYYMM-XXXX (khusus leasing, beda prefix dari KTR)
     */
    private ?int $nextNumber = null;
    private string $prefix;

    public function __construct()
    {
        $this->prefix = 'LS-' . now()->format('Ym');

        // Ambil nomor terakhir dari data_leasings untuk prefix bulan ini
        $last = DataLeasing::where('no_kontrak', 'like', $this->prefix . '-%')
            ->orderByRaw('CAST(RIGHT(no_kontrak, 4) AS UNSIGNED) DESC')
            ->first();

        $this->nextNumber = $last
            ? (int) substr($last->no_kontrak, -4) + 1
            : 1;
    }

    public function model(array $row): ?DataLeasing
    {
        $rowNum = ($this->importedCount + count($this->skippedRows) + 2);

        // Skip baris kosong
        if (empty(array_filter($row))) {
            return null;
        }

        // ── Validasi per baris ─────────────────────────────────────────

        $errors = [];

        // angsuran_per_bulan: harus numerik
        $angsuran = $row['angsuran_per_bulan'] ?? null;
        if ($angsuran !== null && $angsuran !== '' && !is_numeric($angsuran)) {
            $errors[] = 'angsuran_per_bulan harus berupa angka';
        }

        // jatuh_tempo: 1-31
        $jatuhTempo = $row['jatuh_tempo'] ?? null;
        if ($jatuhTempo !== null && $jatuhTempo !== '') {
            if (!is_numeric($jatuhTempo) || (int) $jatuhTempo < 1 || (int) $jatuhTempo > 31) {
                $errors[] = 'jatuh_tempo harus angka 1-31';
            }
        }

        // periode_mulai: handle Excel date serial atau string DD/MM/YYYY
        $periodeMulai = null;
        if (!empty($row['periode_mulai'])) {
            $periodeMulai = $this->parseExcelDate($row['periode_mulai'], $rowNum, 'periode_mulai', $errors);
        }

        // periode_selesai: handle Excel date serial atau string DD/MM/YYYY
        $periodeSelesai = null;
        if (!empty($row['periode_selesai'])) {
            $periodeSelesai = $this->parseExcelDate($row['periode_selesai'], $rowNum, 'periode_selesai', $errors);
            if ($periodeMulai && $periodeSelesai && $periodeSelesai < $periodeMulai) {
                $errors[] = 'periode_selesai harus lebih besar atau sama dengan periode_mulai';
            }
        }

        // Ada error? Skip baris
        if (!empty($errors)) {
            $this->skippedRows[] = [
                'row'    => $rowNum,
                'data'   => trim($row['mobil'] ?? '(kosong)'),
                'errors' => $errors,
            ];
            return null;
        }

        // ── Generate no_kontrak otomatis ───────────────────────────────
        $noKontrak = $this->prefix . '-' . str_pad($this->nextNumber, 4, '0', STR_PAD_LEFT);
        $this->nextNumber++;
        $this->importedCount++;

        return new DataLeasing([
            'data_kontrak_id'    => null,
            'no_kontrak'         => $noKontrak,
            'mobil'              => trim($row['mobil'] ?? ''),
            'tahun'              => trim($row['tahun'] ?? ''),
            'nopol'              => trim($row['nopol'] ?? ''),
            'user_leasing'       => trim($row['user_leasing'] ?? ''),
            'angsuran_per_bulan' => (int) ($row['angsuran_per_bulan'] ?? 0),
            'jatuh_tempo'        => $jatuhTempo ? (int) $jatuhTempo : null,
            'periode_mulai'      => $periodeMulai,
            'periode_selesai'    => $periodeSelesai,
            'personal_account'   => trim($row['personal_account'] ?? ''),
            'sumber_dana_debit'  => trim($row['sumber_dana_debit'] ?? ''),
            'cara_bayar'         => trim($row['cara_bayar'] ?? ''),
            'asuransi_leasing'   => trim($row['asuransi_leasing'] ?? ''),
        ]);
    }

    /**
     * Parse tanggal dari Excel — handle dua kasus:
     * 1. Numeric serial (kolom diformat Date di Excel) → pakai PhpSpreadsheet converter
     * 2. String DD/MM/YYYY (kolom General) → createFromFormat
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
            // Fallback: coba parse bebas (Y-m-d, d-m-Y, dll)
            try {
                return Carbon::parse($strValue)->format('Y-m-d');
            } catch (\Exception $e2) {
                $errors[] = "{$field} format tidak valid (gunakan DD/MM/YYYY, contoh: 01/01/2026)";
                return null;
            }
        }
    }
}
