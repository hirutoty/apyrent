<?php

namespace App\Exports;

use App\Models\DataLeasing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataLeasingDataExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    public function title(): string
    {
        return 'Data Leasing';
    }

    public function collection()
    {
        return DataLeasing::orderBy('no_kontrak')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'no_kontrak',
            'mobil',
            'tahun',
            'nopol',
            'angsuran_per_bulan',
            'jatuh_tempo',
            'periode_mulai',
            'periode_selesai',
            'Cicilan',
            'cicilan_tersisa',
            'status_cicilan',
            'personal_account',
            'sumber_dana_debit',
            'cara_bayar',
            'asuransi_leasing',
            'user_leasing',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->no_kontrak,
            $row->mobil,
            $row->tahun,
            $row->nopol,
            $row->angsuran_per_bulan,
            $row->jatuh_tempo ? 'Tgl ' . $row->jatuh_tempo : '-',
            $row->periode_mulai  ? $row->periode_mulai->format('d/m/Y')  : '-',
            $row->periode_selesai ? $row->periode_selesai->format('d/m/Y') : '-',
            $row->jumlah_cicilan . 'x',
            $row->cicilan_tersisa,
            $row->status_cicilan,
            $row->personal_account,
            $row->sumber_dana_debit,
            $row->cara_bayar,
            $row->asuransi_leasing,
            $row->user_leasing,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // no_kontrak
            'C' => 20,  // mobil
            'D' => 8,   // tahun
            'E' => 14,  // nopol
            'F' => 22,  // angsuran_per_bulan
            'G' => 12,  // jatuh_tempo
            'H' => 14,  // periode_mulai
            'I' => 14,  // periode_selesai
            'J' => 14,  // total_cicilan
            'K' => 14,  // cicilan_tersisa
            'L' => 14,  // status_cicilan
            'M' => 22,  // personal_account
            'N' => 22,  // sumber_dana_debit
            'O' => 16,  // cara_bayar
            'P' => 20,  // asuransi_leasing
            'Q' => 25,  // user_leasing
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->freezePane('A2');

        return [];
    }
}
