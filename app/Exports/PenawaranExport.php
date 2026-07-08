<?php

namespace App\Exports;

use App\Models\InvPenawaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PenawaranExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = InvPenawaran::with('items.kendaraan')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('no_penawaran', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('kepada', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Penawaran',
            'Tanggal Penawaran',
            'Customer',
            'Kepada',
            'Perihal',
            'Periode (Bulan)',
            'Total',
            'Status',
            'Kendaraan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $kendaraan = $row->items->map(fn($item) => $item->kendaraan->merk ?? '-')->join(', ');

        return [
            $no,
            $row->no_penawaran,
            optional($row->tanggal_penawaran)->format('d-m-Y'),
            $row->customer_name,
            $row->kepada ?? '-',
            $row->perihal ?? '-',
            $row->periode,
            'Rp ' . number_format($row->total, 0, ',', '.'),
            strtoupper($row->status ?? '-'),
            $kendaraan ?: '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 22,
            'C' => 18,
            'D' => 28,
            'E' => 25,
            'F' => 30,
            'G' => 16,
            'H' => 20,
            'I' => 15,
            'J' => 35,
        ];
    }

    public function title(): string
    {
        return 'Penawaran';
    }
}
