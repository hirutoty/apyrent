@extends('admin.layouts.app')

@section('title', 'Data Reminders')

@section('content')

<div class="space-y-6 p-5">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Reminders</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $today->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reminders.pdf', request()->query()) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-xs"></i> Export PDF
            </a>
            <a href="{{ route('reminders.export.excel') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
                <i class="fa fa-file-excel text-xs"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Overdue</p>
            <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $overdue->count() }}</h2>
            <p class="text-xs text-gray-400 mt-1">invoice terlambat</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Jatuh Tempo Hari Ini</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $dueToday->count() }}</h2>
            <p class="text-xs text-gray-400 mt-1">invoice</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Mendekati JT (7 hari)</p>
            <h2 class="text-3xl font-bold text-blue-500 mt-2">{{ $upcoming->count() }}</h2>
            <p class="text-xs text-gray-400 mt-1">invoice</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Lainnya</p>
            <h2 class="text-3xl font-bold text-gray-400 mt-2">{{ $others->count() }}</h2>
        </div>
    </div>

    {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @php
                    $navItems = [
                        ['label' => 'Summary', 'url' => '/admin/summary', 'icon' => 'bi bi-bar-chart-line'],
                        ['label' => 'Penawaran', 'url' => '/admin/penawaran', 'icon' => 'bi bi-file-earmark-richtext'],
                        ['label' => 'Kontrak', 'url' => '/admin/kontrak', 'icon' => 'bi bi-file-earmark-lock'],
                        ['label' => 'Invoice', 'url' => '/admin/invoices', 'icon' => 'bi bi-receipt-cutoff'],
                        ['label' => 'Payments', 'url' => '/admin/payments', 'icon' => 'bi bi-credit-card-2-front'],
                        ['label' => 'Reminders', 'url' => '/admin/reminders', 'icon' => 'bi bi-bell'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php
                        $isActive =
                            request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*');
                    @endphp
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                            {{ $isActive
                                ? 'border-blue-600 text-blue-600 bg-blue-50/50'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <i class="{{ $item['icon'] }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

    {{-- ============================================================
         OVERDUE
    ============================================================ --}}
    @if($overdue->count())
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 bg-red-50">
            <i class="fa fa-exclamation-circle text-red-500"></i>
            <span class="text-sm font-semibold text-red-600">Overdue</span>
            <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                {{ $overdue->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Invoice</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Customer</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Dibayar</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jatuh Tempo</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Terlambat</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overdue as $row)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-semibold text-red-700 bg-red-50 px-2 py-0.5 rounded">
                                {{ $row->invoice->invoice_no ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $row->invoice->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-gray-700">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-green-600">Rp {{ number_format($row->paid_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right font-bold text-red-600">Rp {{ number_format($row->remaining_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-600">
                            {{ $row->due_date ? $row->due_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                {{ $row->overdue_days }} hari
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                Overdue
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
         JATUH TEMPO HARI INI
    ============================================================ --}}
    @if($dueToday->count())
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 bg-yellow-50">
            <i class="fa fa-clock text-yellow-500"></i>
            <span class="text-sm font-semibold text-yellow-600">Jatuh Tempo Hari Ini</span>
            <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                {{ $dueToday->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Invoice</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Customer</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Dibayar</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jatuh Tempo</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa Hari</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dueToday as $row)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-semibold text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded">
                                {{ $row->invoice->invoice_no ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $row->invoice->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-gray-700">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-green-600">Rp {{ number_format($row->paid_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right font-bold text-yellow-600">Rp {{ number_format($row->remaining_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-600">
                            {{ $row->due_date ? $row->due_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                Hari ini
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                {{ ucfirst($row->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
         MENDEKATI JATUH TEMPO (7 hari)
    ============================================================ --}}
    @if($upcoming->count())
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 bg-blue-50">
            <i class="fa fa-bell text-blue-500"></i>
            <span class="text-sm font-semibold text-blue-600">Mendekati Jatuh Tempo <span class="font-normal text-blue-400">(dalam 7 hari)</span></span>
            <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-600">
                {{ $upcoming->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Invoice</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Customer</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Dibayar</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jatuh Tempo</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa Hari</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcoming as $row)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                {{ $row->invoice->invoice_no ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $row->invoice->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-gray-700">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-green-600">Rp {{ number_format($row->paid_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right font-bold text-blue-600">Rp {{ number_format($row->remaining_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-600">
                            {{ $row->due_date ? $row->due_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $row->days_left }} hari lagi
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                {{ ucfirst($row->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
         LAINNYA (belum lunas, due date masih jauh / tidak ada)
    ============================================================ --}}
    @if($others->count())
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 bg-gray-50">
            <i class="fa fa-list text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-500">Lainnya <span class="font-normal text-gray-400"></span></span>
            <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                {{ $others->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Invoice</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Customer</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Dibayar</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jatuh Tempo</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa Hari</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($others as $row)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                                {{ $row->invoice->invoice_no ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $row->invoice->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-gray-700">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right text-green-600">Rp {{ number_format($row->paid_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-right font-bold text-gray-700">Rp {{ number_format($row->remaining_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-center text-sm text-gray-600">
                            {{ $row->due_date ? $row->due_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($row->days_left ?? false)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    {{ $row->days_left }} hari lagi
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                {{ ucfirst($row->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ============================================================
         SEMUA LUNAS
    ============================================================ --}}
    @if($overdue->isEmpty() && $dueToday->isEmpty() && $upcoming->isEmpty() && $others->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100">
        <div class="text-center py-16">
            <i class="fa fa-check-circle text-5xl text-green-400 mb-4 block"></i>
            <h3 class="text-base font-semibold text-gray-700">Semua invoice sudah lunas!</h3>
            <p class="text-sm text-gray-400 mt-1">Tidak ada reminder yang perlu ditindaklanjuti.</p>
        </div>
    </div>
    @endif

</div>

@endsection