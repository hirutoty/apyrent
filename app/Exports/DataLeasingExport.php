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
        return [
            'mobil',                // A
            'tahun',                // B
            'nopol',                // C
            'user_leasing',         // D
            'angsuran_per_bulan',   // E — angka, tanpa Rp
            'jatuh_tempo',          // F — angka 1-31
            'periode_mulai',        // G — DD/MM/YYYY
            'periode_selesai',      // H — DD/MM/YYYY
            'personal_account',     // I
            'sumber_dana_debit',    // J
            'cara_bayar',           // K
            'asuransi_leasing',     // L
        ];
    }

    public function array(): array
    {
        // 1 baris contoh
        return [
            [
                'Toyota Avanza',        // mobil
                '2023',                 // tahun
                'B 1234 ABC',           // nopol
                'PT Contoh Perusahaan', // user_leasing
                5000000,                // angsuran_per_bulan
                15,                     // jatuh_tempo
                '01/01/2026',           // periode_mulai
                '31/12/2028',           // periode_selesai
                'Budi Santoso',         // personal_account
                'BCA 1234567890',       // sumber_dana_debit
                'Auto Debit',           // cara_bayar
                'Jasa Raharja',         // asuransi_leasing
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // mobil
            'B' => 8,   // tahun
            'C' => 14,  // nopol
            'D' => 25,  // user_leasing
            'E' => 20,  // angsuran_per_bulan
            'F' => 12,  // jatuh_tempo
            'G' => 14,  // periode_mulai
            'H' => 14,  // periode_selesai
            'I' => 22,  // personal_account
            'J' => 22,  // sumber_dana_debit
            'K' => 16,  // cara_bayar
            'L' => 20,  // asuransi_leasing
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Style header row (row 1)
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // blue-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style baris contoh (row 2) — warna kuning muda
        $sheet->getStyle('A2:L2')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF9C3'], // yellow-100
            ],
        ]);

        // Freeze row header
        $sheet->freezePane('A2');

        // Catatan di cell A1
        $sheet->getComment('A1')->getText()->createTextRun(
            'Nomor kontrak akan di-generate otomatis saat import. ' .
            'Baris berwarna kuning adalah contoh — hapus sebelum import. ' .
            'Format tanggal: DD/MM/YYYY (contoh: 01/01/2026). ' .
            'Angsuran: angka tanpa titik/koma (contoh: 5000000).'
        );

        return [];
    }
}
