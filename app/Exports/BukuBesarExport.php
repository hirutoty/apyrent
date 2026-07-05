<?php

namespace App\Exports;

use App\Models\Bukubesar;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BukuBesarExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return Bukubesar::query()
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('kode_jurnal', 'like', '%' . $this->search . '%')
                   ->orWhere('transaksi',  'like', '%' . $this->search . '%')
                   ->orWhere('kategori',   'like', '%' . $this->search . '%')
                   ->orWhere('aktivitas',  'like', '%' . $this->search . '%');
            }))
            ->orderBy('tanggal');
    }

    public function headings(): array
    {
        return [
            'No', 'Kode Jurnal', 'Transaksi', 'Kategori',
            'Tanggal', 'Debit (Rp)', 'Kredit (Rp)', 'Saldo (Rp)',
            'Aktivitas', 'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->kode_jurnal,
            $row->transaksi,
            $row->kategori,
            $row->tanggal,
            $row->debit,
            $row->kredit,
            $row->saldo,
            $row->aktivitas,
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
                    'startColor' => ['rgb' => 'FEF9C3'], // kuning muda sesuai tema saldo
                ],
            ],
        ];
    }
}