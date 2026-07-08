<?php

namespace App\Exports;

use App\Models\InvoicePayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PaymentsExport implements
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
        $query = InvoicePayment::with('invoice')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('transaction_id', 'like', '%' . $this->search . '%')
                  ->orWhere('method', 'like', '%' . $this->search . '%')
                  ->orWhere('status', 'like', '%' . $this->search . '%')
                  ->orWhereHas('invoice', function ($i) {
                      $i->where('invoice_no', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Transaction ID',
            'No Invoice',
            'Customer',
            'Tanggal Bayar',
            'Jumlah',
            'Metode',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->transaction_id,
            optional($row->invoice)->invoice_no ?? '-',
            optional($row->invoice)->customer_name ?? '-',
            $row->payment_date ? \Carbon\Carbon::parse($row->payment_date)->format('d-m-Y') : '-',
            'Rp ' . number_format($row->amount, 0, ',', '.'),
            $row->method ?? '-',
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
            'B' => 28,
            'C' => 22,
            'D' => 28,
            'E' => 16,
            'F' => 20,
            'G' => 15,
            'H' => 12,
        ];
    }

    public function title(): string
    {
        return 'Payments';
    }
}
