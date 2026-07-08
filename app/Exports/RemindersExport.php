<?php

namespace App\Exports;

use App\Models\InvSummary;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RemindersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    public function collection()
    {
        return InvSummary::with(['invoice', 'kontrak', 'penawaran'])
            ->where('payment_status', '!=', 'lunas')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Invoice',
            'Customer',
            'No Kontrak',
            'Total Amount',
            'Paid Amount',
            'Remaining Amount',
            'Status Pembayaran',
            'Due Date',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $today   = Carbon::today();
        $dueDate = null;

        if ($row->kontrak && $row->kontrak->perjanjian_pembayaran) {
            $dueDate = Carbon::parse($row->kontrak->perjanjian_pembayaran);
        } elseif ($row->invoice && $row->invoice->invoice_date) {
            $dueDate = Carbon::parse($row->invoice->invoice_date);
        }

        $keterangan = '-';
        if ($dueDate) {
            if ($dueDate->lt($today)) {
                $keterangan = 'Overdue ' . $dueDate->diffInDays($today) . ' hari';
            } elseif ($dueDate->isToday()) {
                $keterangan = 'Jatuh tempo hari ini';
            } elseif ($dueDate->lte($today->copy()->addDays(7))) {
                $keterangan = 'Jatuh tempo ' . $today->diffInDays($dueDate) . ' hari lagi';
            } else {
                $keterangan = 'Jatuh tempo ' . $today->diffInDays($dueDate) . ' hari lagi';
            }
        }

        return [
            $no,
            optional($row->invoice)->invoice_no ?? '-',
            optional($row->invoice)->customer_name ?? '-',
            optional($row->kontrak)->no_kontrak ?? '-',
            'Rp ' . number_format($row->total_amount, 0, ',', '.'),
            'Rp ' . number_format($row->paid_amount, 0, ',', '.'),
            'Rp ' . number_format($row->remaining_amount, 0, ',', '.'),
            $row->payment_status,
            $dueDate ? $dueDate->format('d-m-Y') : '-',
            $keterangan,
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
            'C' => 28,
            'D' => 22,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 18,
            'I' => 15,
            'J' => 30,
        ];
    }

    public function title(): string
    {
        return 'Reminders';
    }
}
