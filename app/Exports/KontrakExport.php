<?php

namespace App\Exports;

use App\Models\InvKontrak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KontrakExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected $search;
    protected $status;

    public function __construct(?string $search = null, ?string $status = null)
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function collection()
    {
        $query = InvKontrak::with('penawaran')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('no_kontrak', 'like', '%' . $this->search . '%')
                  ->orWhere('pihak_pertama', 'like', '%' . $this->search . '%')
                  ->orWhere('pihak_kedua', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Kontrak',
            'No Penawaran',
            'Tanggal Kontrak',
            'Perjanjian Pembayaran',
            'Pihak Pertama',
            'Kontak Pertama',
            'Pihak Kedua',
            'Kontak Kedua',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->no_kontrak,
            optional($row->penawaran)->no_penawaran ?? '-',
            optional($row->tanggal_kontrak)->format('d-m-Y'),
            $row->perjanjian_pembayaran ? \Carbon\Carbon::parse($row->perjanjian_pembayaran)->format('d-m-Y') : '-',
            $row->pihak_pertama,
            $row->contact_pertama ?? '-',
            $row->pihak_kedua,
            $row->contact_kedua ?? '-',
            strtoupper($row->status ?? '-'),
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
            'C' => 22,
            'D' => 18,
            'E' => 22,
            'F' => 28,
            'G' => 20,
            'H' => 28,
            'I' => 20,
            'J' => 15,
        ];
    }

    public function title(): string
    {
        return 'Kontrak';
    }
}
