<?php

namespace App\Exports;

use App\Models\Keuangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class KeuanganExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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

    public function collection()
    {
        $data = Keuangan::with('user')
            ->when($this->tahun, fn($q) => $q->whereYear('tanggal', $this->tahun))
            ->when($this->bulan, fn($q) => $q->whereMonth('tanggal', $this->bulan))
            ->when($this->hari,  fn($q) => $q->whereDay('tanggal', $this->hari))
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('kategori', 'like', '%' . $this->search . '%')
                   ->orWhere('keterangan', 'like', '%' . $this->search . '%')
                   ->orWhere('reference', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('tanggal')
            ->get();

        // Map data dengan nomor urut
        $mapped = $data->map(function ($row, $index) {
            return [
                'no' => $index + 1,
                'tanggal' => Carbon::parse($row->tanggal)->format('d-m-Y'),
                'reference' => $row->reference ?? '-',
                'divisi' => $row->divisi ?? '-',
                'user' => $row->user->name ?? '-',
                'kategori' => $row->kategori,
                'keterangan' => $row->keterangan,
                'pemasukan' => $row->pemasukan > 0 ? $row->pemasukan : '',
                'pengeluaran' => $row->pengeluaran > 0 ? $row->pengeluaran : '',
                'saldo' => $row->saldo,
            ];
        });

        // Tambahkan baris total di akhir
        $totalSaldo = $data->last()->saldo ?? 0;
        $mapped->push([
            'no' => '',
            'tanggal' => '',
            'reference' => '',
            'divisi' => '',
            'user' => '',
            'kategori' => '',
            'keterangan' => 'TOTAL SALDO AKHIR',
            'pemasukan' => '',
            'pengeluaran' => '',
            'saldo' => $totalSaldo,
        ]);

        return $mapped;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Reference', 'Divisi', 'User', 'Kategori', 'Keterangan', 'Pemasukan', 'Pengeluaran', 'Saldo'];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        return [
            // Header row - gray background
            1 => [
                'font' => ['bold' => true], 
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ]
            ],
            // Total row - yellow background
            $lastRow => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7'],
                ]
            ],
        ];
    }
}