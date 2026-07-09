<?php

namespace App\Exports;

use App\Models\VirtualAccount;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VirtualAccountExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return VirtualAccount::with('pelanggan', 'invoice')
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('va_number', 'like', '%' . $this->search . '%')
                   ->orWhere('bank',    'like', '%' . $this->search . '%')
                   ->orWhere('status',  'like', '%' . $this->search . '%')
                   ->orWhereHas('pelanggan', fn($m) =>
                       $m->where('nama_pelanggan', 'like', '%' . $this->search . '%')
                   );
            }))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No', 'VA Number', 'Pelanggan', 'Invoice No', 'Customer Invoice',
            'Bank', 'Expected Amount (Rp)', 'Paid Amount (Rp)',
            'Status', 'Expired At',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->va_number,
            $row->pelanggan->nama_pelanggan ?? '-',
            $row->invoice->invoice_no ?? '-',
            $row->invoice->customer_name ?? '-',
            $row->bank,
            $row->expected_amount,
            $row->paid_amount,
            ucfirst($row->status),
            $row->expired_at ? $row->expired_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E7FF'], // indigo muda sesuai tema expected
                ],
            ],
        ];
    }
}
