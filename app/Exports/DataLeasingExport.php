<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Template unduh — kolom 100% sama dengan DataLeasingFullExport (export data).
 * Isi baris 2 adalah contoh dummy berwarna kuning — hapus sebelum import.
 *
 * Kolom-kolom read-only / auto-hitung (No, Cicilan, Total, Sisa, Status,
 * Serial Kontrak, Dibuat Pada) ditampilkan dengan header abu agar user tahu
 * kolom tersebut tidak perlu diisi saat import.
 */
class DataLeasingExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    public function title(): string
    {
        return 'Data Leasing';
    }

    public function headings(): array
    {
        // Harus persis sama urutan & teks dengan DataLeasingFullExport::headings()
        return [
            'No',                    // A  — auto (tidak perlu diisi)
            'No Kontrak',            // B  — wajib, unik
            'Mobil / Merk',          // C
            'Tahun',                 // D
            'Nopol',                 // E
            'Angsuran / Bulan (Rp)', // F  — angka tanpa titik/koma
            'Jatuh Tempo',           // G  — angka 1-31
            'Periode Mulai',         // H  — DD/MM/YYYY
            'Periode Selesai',       // I  — DD/MM/YYYY
            'Cicilan',               // J  — auto-hitung ("Nx sisa")
            'Total Cicilan (Rp)',    // K  — auto-hitung
            'Sisa Cicilan (Rp)',     // L  — auto-hitung
            'Status Cicilan',        // M  — auto-hitung
            'Asuransi Leasing',      // N
            'Personal Account',      // O
            'Sumber Dana Debit',     // P
            'Cara Bayar',            // Q
            'Serial Kontrak',        // R  — auto (referensi)
            'Dibuat Pada',           // S  — auto (referensi)
            'User Leasing',          // T  — paling akhir
        ];
    }

    public function array(): array
    {
        // 1 baris contoh dummy — warna kuning, hapus sebelum import
        return [
            [
                1,                      // No      — abaikan
                'PKS/2026/001',         // No Kontrak
                'Toyota Avanza',        // Mobil
                '2023',                 // Tahun
                'B 1234 ABC',           // Nopol
                5000000,                // Angsuran/Bln
                15,                     // Jatuh Tempo
                '01/01/2026',           // Periode Mulai
                '31/12/2028',           // Periode Selesai
                '36x sisa',             // Cicilan — auto, abaikan
                180000000,              // Total Cicilan — auto, abaikan
                180000000,              // Sisa Cicilan — auto, abaikan
                'Belum Mulai',          // Status — auto, abaikan
                'Jasa Raharja',         // Asuransi
                'Budi Santoso',         // Personal Account
                'BCA 1234567890',       // Sumber Dana
                'Auto Debit',           // Cara Bayar
                'KTR-202601-0001',      // Serial Kontrak — auto, abaikan
                '01/01/2026 08:00',     // Dibuat Pada — auto, abaikan
                'PT Contoh Perusahaan', // User Leasing
            ],
        ];
    }

    public function columnWidths(): array
    {
        // Harus persis sama dengan DataLeasingFullExport::columnWidths()
        return [
            'A' => 5,   // No
            'B' => 22,  // No Kontrak
            'C' => 22,  // Mobil
            'D' => 8,   // Tahun
            'E' => 14,  // Nopol
            'F' => 22,  // Angsuran/Bln
            'G' => 12,  // Jatuh Tempo
            'H' => 14,  // Periode Mulai
            'I' => 14,  // Periode Selesai
            'J' => 14,  // Cicilan
            'K' => 22,  // Total Cicilan
            'L' => 22,  // Sisa Cicilan
            'M' => 16,  // Status
            'N' => 22,  // Asuransi
            'O' => 24,  // Personal Account
            'P' => 24,  // Sumber Dana
            'Q' => 16,  // Cara Bayar
            'R' => 20,  // Serial Kontrak
            'S' => 18,  // Dibuat Pada
            'T' => 28,  // User Leasing
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'T';

        // ── Header row — biru, sama dengan export data ───────────────────
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1D4ED8'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(32);

        // ── Kolom wajib isi (B) — merah ──────────────────────────────────
        $sheet->getStyle('B1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
        ]);

        // ── Kolom auto-hitung / read-only (A, J, K, L, M, R, S) — abu ───
        foreach (['A', 'J', 'K', 'L', 'M', 'R', 'S'] as $col) {
            $sheet->getStyle("{$col}1")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6B7280']],
            ]);
        }

        // ── Header User Leasing (T) — teal, sama dengan export data ──────
        $sheet->getStyle('T1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
        ]);

        // ── Baris contoh (row 2) — kuning muda ───────────────────────────
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF9C3'],
            ],
            'font'      => ['size' => 9],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        // Right-align angka di baris dummy
        foreach (['F', 'K', 'L'] as $col) {
            $sheet->getStyle("{$col}2")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("{$col}2")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        // Center kolom cicilan dummy (J)
        $sheet->getStyle('J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J2')->getFont()->setBold(true)->getColor()->setRGB('EA580C');

        // Status dummy (M) — warna biru (Belum Mulai)
        $sheet->getStyle('M2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Freeze header ─────────────────────────────────────────────────
        $sheet->freezePane('A2');

        // ── Catatan di header kolom B (No Kontrak) ────────────────────────
        $sheet->getComment('B1')->getText()->createTextRun(
            'No Kontrak (MERAH): Wajib diisi, harus unik — nomor kontrak dari dokumen fisik.' . "\n" .
            'Kolom ABU (No, Cicilan, Total, Sisa, Status, Serial, Dibuat Pada): diabaikan saat import, diisi otomatis sistem.' . "\n\n" .
            'Format tanggal: DD/MM/YYYY (contoh: 01/01/2026).' . "\n" .
            'Angsuran/Bln: angka tanpa titik/koma (contoh: 5000000).' . "\n" .
            'Jatuh Tempo: angka 1–31.' . "\n\n" .
            'Baris KUNING adalah contoh — hapus sebelum import!'
        );

        return [];
    }
}
