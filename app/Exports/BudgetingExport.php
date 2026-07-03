<?php

namespace App\Exports;

use App\Models\AnggaranProyek;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BudgetingExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return AnggaranProyek::query()
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('proyek',   'like', '%' . $this->search . '%')
                   ->orWhere('kategori', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('proyek');
    }

    public function headings(): array
    {
        return [
            'No', 'Proyek', 'Kategori',
            'Budget (Rp)', 'Realisasi (Rp)', 'Sisa (Rp)', 'Terpakai (%)',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->proyek,
            $row->kategori,
            $row->budget,
            $row->realisasi,
            $row->sisa,
            number_format($row->persen_terpakai, 1) . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DBEAFE'], // biru muda sesuai tema
                ],
            ],
        ];
    }
}