<?php

namespace App\Exports;

use App\Models\Bupot;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BupotExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return Bupot::query()
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('nomor_bukti',    'like', '%' . $this->search . '%')
                   ->orWhere('nama_pemotong', 'like', '%' . $this->search . '%')
                   ->orWhere('nama_dipotong', 'like', '%' . $this->search . '%')
                   ->orWhere('tipe',          'like', '%' . $this->search . '%')
                   ->orWhere('status',        'like', '%' . $this->search . '%');
            }))
            ->orderBy('tanggal_bukti', 'desc');
    }

    public function headings(): array
    {
        return [
            'No', 'Nomor Bukti', 'Tanggal', 'Tipe',
            'NPWP Pemotong', 'Nama Pemotong',
            'NPWP Dipotong', 'Nama Dipotong',
            'Jumlah Bruto (Rp)', 'Tarif Pajak (%)', 'Jumlah Potong (Rp)',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nomor_bukti,
            $row->tanggal_bukti,
            $row->tipe,
            $row->npwp_pemotong,
            $row->nama_pemotong,
            $row->npwp_dipotong,
            $row->nama_dipotong,
            $row->jumlah_bruto,
            $row->tarif_pajak . '%',
            $row->jumlah_potong,
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
                    'startColor' => ['rgb' => 'D1FAE5'], // hijau muda sesuai tema bruto
                ],
            ],
        ];
    }
}