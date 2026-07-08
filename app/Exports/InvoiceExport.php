<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InvoiceExport implements
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
        $query = Invoice::with(['penawaran', 'kontrak', 'kendaraan'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_no', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('order_no', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Invoice',
            'Tanggal Invoice',
            'Customer',
            'Alamat Customer',
            'No Penawaran',
            'No Kontrak',
            'Kendaraan',
            'Total',
            'PPN',
            'PPH',
            'Status',
            'Status Pembayaran',
            'Terakhir Reminder',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $kendaraan = $row->kendaraan
            ? ($row->kendaraan->merk . ' - ' . $row->kendaraan->nopol)
            : '-';

        return [
            $no,
            $row->invoice_no,
            optional($row->invoice_date)->format('d-m-Y'),
            $row->customer_name,
            $row->customer_address ?? '-',
            optional($row->penawaran)->no_penawaran ?? '-',
            optional($row->kontrak)->no_kontrak ?? '-',
            $kendaraan,
            'Rp ' . number_format($row->total ?? 0, 0, ',', '.'),
            'Rp ' . number_format($row->ppn ?? 0, 0, ',', '.'),
            'Rp ' . number_format($row->pph ?? 0, 0, ',', '.'),
            strtoupper($row->status ?? '-'),
            strtoupper($row->payment_status ?? '-'),
            optional($row->last_email_sent_at)->format('d-m-Y') ?? '-',
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
            'C' => 16,
            'D' => 28,
            'E' => 30,
            'F' => 22,
            'G' => 22,
            'H' => 28,
            'I' => 18,
            'J' => 18,
            'K' => 18,
            'L' => 12,
            'M' => 18,
            'N' => 18,
        ];
    }

    public function title(): string
    {
        return 'Invoice';
    }
}
