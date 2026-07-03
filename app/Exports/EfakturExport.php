<?php

namespace App\Exports;

use App\Models\Efaktur;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EfakturExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return Efaktur::query()
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('nomor_faktur', 'like', '%' . $this->search . '%')
                   ->orWhere('nama_lawan',  'like', '%' . $this->search . '%')
                   ->orWhere('npwp_lawan',  'like', '%' . $this->search . '%')
                   ->orWhere('tipe',        'like', '%' . $this->search . '%')
                   ->orWhere('status',      'like', '%' . $this->search . '%');
            }))
            ->orderBy('tanggal_faktur', 'desc');
    }

    public function headings(): array
    {
        return [
            'No', 'Nomor Faktur', 'Tanggal Faktur', 'Tipe',
            'NPWP Lawan', 'Nama Lawan',
            'DPP (Rp)', 'PPN (Rp)', 'PPNBM (Rp)',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nomor_faktur,
            $row->tanggal_faktur,
            $row->tipe,
            $row->npwp_lawan,
            $row->nama_lawan,
            $row->dpp,
            $row->ppn,
            $row->ppnbm,
            $row->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E7FF'], // indigo muda sesuai tema DPP
                ],
            ],
        ];
    }
}