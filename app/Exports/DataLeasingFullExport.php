<?php

namespace App\Exports;

use App\Models\DataLeasing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DataLeasingFullExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithProperties
{
    protected int $rowCount = 0;

    public function title(): string
    {
        return 'Data Leasing';
    }

    public function properties(): array
    {
        return [
            'creator'     => 'Apyrent System',
            'lastModifiedBy' => 'Apyrent System',
            'title'       => 'Export Data Leasing',
            'description' => 'Data lengkap cicilan leasing kendaraan',
            'subject'     => 'Data Leasing',
            'keywords'    => 'leasing,kendaraan,cicilan',
            'category'    => 'Export',
        ];
    }

    public function collection()
    {
        return DataLeasing::with('dataKontrak')
            ->orderBy('no_kontrak')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',                    // A
            'No Kontrak',            // B
            'Mobil / Merk',          // C
            'Tahun',                 // D
            'Nopol',                 // E
            'Angsuran / Bulan (Rp)', // F
            'Jatuh Tempo',           // G
            'Periode Mulai',         // H
            'Periode Selesai',       // I
            'Cicilan',               // J  → "Nx sisa"
            'Total Cicilan (Rp)',    // K
            'Sisa Cicilan (Rp)',     // L
            'Status Cicilan',        // M
            'Asuransi Leasing',      // N
            // Pembayaran
            'Personal Account',      // O
            'Sumber Dana Debit',     // P
            'Cara Bayar',            // Q
            // Referensi & metadata
            'Serial Kontrak',        // R
            'Dibuat Pada',           // S
            // User paling akhir
            'User Leasing',          // T
        ];
    }

    public function map($row): array
    {
        $this->rowCount++;

        $jumlah  = $row->jumlah_cicilan;
        $tersisa = $row->cicilan_tersisa;

        return [
            $this->rowCount,
            $row->no_kontrak         ?? '-',
            $row->mobil              ?? '-',
            $row->tahun              ?? '-',
            $row->nopol              ?? '-',
            $row->angsuran_per_bulan ?? 0,
            $row->jatuh_tempo ? 'Tgl ' . $row->jatuh_tempo : '-',
            $row->periode_mulai   ? $row->periode_mulai->format('d/m/Y')   : '-',
            $row->periode_selesai ? $row->periode_selesai->format('d/m/Y') : '-',
            $jumlah . 'x',                                       // J - ubah dari tersisa ke jumlah
            $jumlah  * ($row->angsuran_per_bulan ?? 0),          // K
            $tersisa * ($row->angsuran_per_bulan ?? 0),          // L
            $row->status_cicilan,
            $row->asuransi_leasing   ?? '-',
            // Pembayaran
            $row->personal_account   ?? '-',
            $row->sumber_dana_debit  ?? '-',
            $row->cara_bayar         ?? '-',
            // Referensi & metadata
            $row->dataKontrak?->serial_number       ?? '-',
            $row->created_at?->format('d/m/Y H:i')  ?? '-',
            // User paling akhir
            $row->user_leasing       ?? '-',
        ];
    }

    public function columnWidths(): array
    {
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
            'J' => 14,  // Cicilan (Nx sisa)
            'K' => 22,  // Total Cicilan (Rp)
            'L' => 22,  // Sisa Cicilan (Rp)
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
        $lastRow = $this->rowCount + 1;
        $lastCol = 'T';

        // ── Header row ──────────────────────────────────────────────────
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
        $sheet->freezePane('A2');

        // ── Data rows ────────────────────────────────────────────────────
        if ($lastRow >= 2) {
            $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                'font'      => ['size' => 9],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => false,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E5E7EB'],
                    ],
                ],
            ]);

            // Zebra striping
            for ($r = 2; $r <= $lastRow; $r++) {
                if ($r % 2 === 0) {
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8FAFC'],
                        ],
                    ]);
                }
            }

            // Right-align kolom angka: F, K, L
            foreach (['F', 'K', 'L'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Center kolom cicilan (J)
            $sheet->getStyle("J2:J{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Rp: F (angsuran/bln), K (total cicilan), L (sisa cicilan)
            foreach (['F', 'K', 'L'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }

            // Warna teks kolom J: hijau untuk total cicilan (informasi positif)
            for ($r = 2; $r <= $lastRow; $r++) {
                $val   = $sheet->getCell("J{$r}")->getValue();
                $total = (int) $val;   // "36x" → intval → 36
                $color = $total > 0 ? '059669' : '6B7280'; // hijau jika ada cicilan, abu jika 0
                $sheet->getStyle("J{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $color], 'size' => 9],
                ]);
            }

            // Status cicilan — warna per nilai (kolom M)
            for ($r = 2; $r <= $lastRow; $r++) {
                $status = $sheet->getCell("M{$r}")->getValue();
                $color  = match ($status) {
                    'Lunas'       => 'D1FAE5',
                    'Partial'     => 'FEF3C7',
                    'Belum Mulai' => 'DBEAFE',
                    default       => 'F3F4F6',
                };
                $sheet->getStyle("M{$r}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color],
                    ],
                    'font'      => ['bold' => true, 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        // ── Header kolom referensi/metadata (R, S) — abu gelap ───────────
        foreach (['R', 'S'] as $col) {
            $sheet->getStyle("{$col}1")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4B5563'],
                ],
            ]);
        }

        // ── Header User Leasing (T) — teal gelap ─────────────────────────
        $sheet->getStyle('T1')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'],
            ],
        ]);

        return [];
    }
}
