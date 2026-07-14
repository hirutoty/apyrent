<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvSummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function exportPdf(Request $request)
    {
        $today = Carbon::today();

        $summaries = InvSummary::with(['invoice', 'kontrak', 'penawaran'])
            ->where('payment_status', '!=', 'lunas')
            ->latest()
            ->get();

        $overdue  = collect();
        $dueToday = collect();
        $upcoming = collect();
        $others   = collect();

        foreach ($summaries as $summary) {
            $dueDate = null;

            if ($summary->kontrak && $summary->kontrak->perjanjian_pembayaran) {
                $dueDate = Carbon::parse($summary->kontrak->perjanjian_pembayaran);
            } elseif ($summary->invoice && $summary->invoice->invoice_date) {
                $dueDate = Carbon::parse($summary->invoice->invoice_date);
            }

            $summary->due_date = $dueDate;

            if (!$dueDate) {
                $others->push($summary);
            } elseif ($dueDate->lt($today)) {
                $summary->overdue_days = $dueDate->diffInDays($today);
                $overdue->push($summary);
            } elseif ($dueDate->isToday()) {
                $dueToday->push($summary);
            } elseif ($dueDate->lte($today->copy()->addDays(7))) {
                $summary->days_left = $today->diffInDays($dueDate);
                $upcoming->push($summary);
            } else {
                $summary->days_left = $today->diffInDays($dueDate);
                $others->push($summary);
            }
        }

        $setting = \App\Models\Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reminders.pdf', compact(
            'overdue', 'dueToday', 'upcoming', 'others', 'setting', 'logoSrc'
        ));

        return $pdf->stream('reminder-invoice.pdf');
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RemindersExport(),
            'Reminders-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function index(Request $request)
    {
        $today = Carbon::today();

        // Ambil semua summary yang belum lunas, dengan relasi invoice & kontrak
        $summaries = InvSummary::with(['invoice', 'kontrak', 'penawaran'])
            ->where('payment_status', '!=', 'lunas')
            ->latest()
            ->get();

        // Kelompokkan berdasarkan status jatuh tempo
        $overdue      = collect();
        $dueToday     = collect();
        $upcoming     = collect();  // due dalam 7 hari ke depan
        $others       = collect();  // sisanya

        foreach ($summaries as $summary) {
            // Ambil due date: prioritaskan dari kontrak, fallback ke invoice_date
            $dueDate = null;

            if ($summary->kontrak && $summary->kontrak->perjanjian_pembayaran) {
                $dueDate = Carbon::parse($summary->kontrak->perjanjian_pembayaran);
            } elseif ($summary->invoice && $summary->invoice->invoice_date) {
                $dueDate = Carbon::parse($summary->invoice->invoice_date);
            }

            $summary->due_date = $dueDate;

            if (!$dueDate) {
                $others->push($summary);
            } elseif ($dueDate->lt($today)) {
                $summary->overdue_days = $dueDate->diffInDays($today);
                $overdue->push($summary);
            } elseif ($dueDate->isToday()) {
                $dueToday->push($summary);
            } elseif ($dueDate->lte($today->copy()->addDays(7))) {
                $summary->days_left = $today->diffInDays($dueDate);
                $upcoming->push($summary);
            } else {
                $summary->days_left = $today->diffInDays($dueDate);
                $others->push($summary);
            }
        }

        return view('admin.reminders.index', compact(
            'overdue',
            'dueToday',
            'upcoming',
            'others',
            'today'
        ));
    }
}