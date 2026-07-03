<?php

namespace App\Exports;

use App\Models\RekonsiliasiBank;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekonsiliasiExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return RekonsiliasiBank::query()
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('deskripsi',          'like', '%' . $this->search . '%')
                   ->orWhere('reference_no',      'like', '%' . $this->search . '%')
                   ->orWhere('currency',          'like', '%' . $this->search . '%')
                   ->orWhere('status_rekonsiliasi','like', '%' . $this->search . '%')
                   ->orWhere('tanggal',           'like', '%' . $this->search . '%');
            }))
            ->orderBy('tanggal', 'desc');
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal', 'Deskripsi', 'Reference No',
            'Amount (Rp)', 'Currency', 'Invoice ID', 'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->tanggal,
            $row->deskripsi,
            $row->reference_no,
            $row->amount,
            $row->currency,
            $row->invoice_id,
            ucfirst($row->status_rekonsiliasi),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCFCE7'], // hijau muda sesuai tema amount
                ],
            ],
        ];
    }
}