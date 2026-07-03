<?php

namespace App\Exports;

use App\Models\HutangVendor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HutangVendorExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search, $status;

    public function __construct($search = null, $status = null)
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function query()
    {
        return HutangVendor::query()
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('nama_vendor', 'like', '%' . $this->search . '%')
                   ->orWhere('kategori',   'like', '%' . $this->search . '%')
                   ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('jatuh_tempo');
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Vendor', 'Kategori',
            'Nominal', 'Dibayar', 'Sisa',
            'Jatuh Tempo', 'Status', 'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nama_vendor,
            $row->kategori,
            $row->nominal,
            $row->dibayar,
            $row->sisa,
            $row->jatuh_tempo,
            $row->status === 'lunas' ? 'Lunas' : 'Belum Lunas',
            $row->keterangan,
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