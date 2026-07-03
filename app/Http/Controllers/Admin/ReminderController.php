<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvSummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReminderController extends Controller
{
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