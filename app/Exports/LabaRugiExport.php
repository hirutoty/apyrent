<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LabaRugiExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $totalBeban;
    protected $pendapatan;
    protected $labaKotor;
    protected $labaBersih;

    public function __construct($totalBeban, $pendapatan, $labaKotor, $labaBersih)
    {
        $this->totalBeban  = $totalBeban;
        $this->pendapatan  = $pendapatan;
        $this->labaKotor   = $labaKotor;
        $this->labaBersih  = $labaBersih;
    }

    public function array(): array
    {
        return [
            [1, $this->totalBeban, $this->pendapatan, $this->labaKotor, $this->labaBersih],
        ];
    }

    public function headings(): array
    {
        return ['No', 'Total Beban (Rp)', 'Total Pendapatan (Rp)', 'Laba Kotor (Rp)', 'Laba Bersih (Rp)'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCFCE7'],
                ],
            ],
        ];
    }
}
