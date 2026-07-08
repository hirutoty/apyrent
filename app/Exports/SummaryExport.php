<?php

namespace App\Exports;

use App\Models\InvSummary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SummaryExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected $status;

    public function __construct(?string $status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        return InvSummary::with(['penawaran', 'kontrak', 'invoice'])
            ->when($this->status, fn($q) => $q->where('payment_status', $this->status))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Invoice',
            'Customer',
            'No Penawaran',
            'No Kontrak',
            'Tipe',
            'Total Amount',
            'Paid Amount',
            'Remaining Amount',
            'Status Pembayaran',
            'Tanggal Dibuat',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            optional($row->invoice)->invoice_no ?? '-',
            optional($row->invoice)->customer_name ?? '-',
            optional($row->penawaran)->no_penawaran ?? '-',
            optional($row->kontrak)->no_kontrak ?? '-',
            $row->type ?? '-',
            'Rp ' . number_format($row->total_amount, 0, ',', '.'),
            'Rp ' . number_format($row->paid_amount, 0, ',', '.'),
            'Rp ' . number_format($row->remaining_amount, 0, ',', '.'),
            $row->payment_status ?? '-',
            optional($row->created_at)->format('d-m-Y'),
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
            'B' => 20,
            'C' => 28,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 15,
            'K' => 15,
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}
