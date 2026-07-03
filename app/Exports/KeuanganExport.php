<?php

namespace App\Exports;

use App\Models\Keuangan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class KeuanganExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $hari, $bulan, $tahun, $jenis, $search;

    public function __construct($hari, $bulan, $tahun, $jenis, $search)
    {
        $this->hari   = $hari;
        $this->bulan  = $bulan;
        $this->tahun  = $tahun;
        $this->jenis = $jenis;
        $this->search = $search;
    }

    public function query()
    {
        return Keuangan::with('user')
            ->when($this->tahun, fn($q) => $q->whereYear('tanggal', $this->tahun))
            ->when($this->bulan, fn($q) => $q->whereMonth('tanggal', $this->bulan))
            ->when($this->hari,  fn($q) => $q->whereDay('tanggal', $this->hari))
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('kategori', 'like', '%' . $this->search . '%')
                   ->orWhere('keterangan', 'like', '%' . $this->search . '%')
                   ->orWhere('reference', 'like', '%' . $this->search . '%');
                   
            }))
            
            ->orderBy('tanggal');

            
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Reference', 'User', 'Kategori', 'Keterangan', 'Pemasukan', 'Pengeluaran', 'Saldo'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            Carbon::parse($row->tanggal)->format('d-m-Y'),
            $row->reference ?? '-',
            $row->user->name ?? '-',
            $row->kategori,
            $row->keterangan,
            $row->pemasukan > 0 ? $row->pemasukan : 0,
            $row->pengeluaran > 0 ? $row->pengeluaran : 0,
            $row->saldo,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB'],
            ]],
        ];
    }
}