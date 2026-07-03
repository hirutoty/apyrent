<?php

namespace App\Exports;

use App\Models\LaporanKeuangan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KonsolidasiExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return LaporanKeuangan::query()
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('nama_perusahaan', 'like', '%' . $this->search . '%')
                   ->orWhere('periode',        'like', '%' . $this->search . '%');
            }))
            ->orderBy('periode');
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Perusahaan',
            'Pendapatan (Rp)', 'Beban (Rp)', 'Laba Bersih (Rp)',
            'Periode',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nama_perusahaan,
            $row->pendapatan,
            $row->beban,
            $row->laba,
            $row->periode,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EDE9FE'], // ungu muda sesuai tema laba
                ],
            ],
        ];
    }
}