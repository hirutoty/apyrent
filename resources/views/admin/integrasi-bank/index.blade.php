@extends('admin.layouts.app')

@section('title', 'Integrasi Bank')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Integrasi Bank</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola Rekonsiliasi Bank & Virtual Account</p>
        </div>
    </div>

    {{-- NAV TABS --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
        <button onclick="switchTab('rekonsiliasi')" id="tab-rekonsiliasi"
            class="tab-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all bg-white text-blue-600 shadow-sm">
            <i class="bi bi-arrow-left-right mr-1.5"></i> Rekonsiliasi Bank
        </button>
        <button onclick="switchTab('virtual')" id="tab-virtual"
            class="tab-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all text-gray-500 hover:text-gray-700">
            <i class="bi bi-credit-card-2-front-fill mr-1.5"></i> Virtual Account
        </button>
    </div>

    {{-- ═══════════════════════════════════════════
         TAB: REKONSILIASI BANK
    ═══════════════════════════════════════════ --}}
    <div id="panel-rekonsiliasi">

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Jumlah Rekonsiliasi</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $rekonsiliasi->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-bank text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <h2 class="text-lg font-bold text-green-600 mt-2 leading-tight">
                            Rp {{ number_format($rekonsiliasi->sum('amount'), 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Pending</p>
                        <h2 class="text-3xl font-bold text-yellow-600 mt-2">{{ $rekonsiliasi->where('status_rekonsiliasi','Pending')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center">
                        <i class="bi bi-clock-history text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Matched</p>
                        <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $rekonsiliasi->where('status_rekonsiliasi','matched')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-check-circle-fill text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Unmatched</p>
                        <h2 class="text-3xl font-bold text-red-600 mt-2">{{ $rekonsiliasi->where('status_rekonsiliasi','unmatched')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">
                        <i class="bi bi-x-circle-fill text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>


        {{-- TABLE REKONSILIASI --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Rekonsiliasi</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $rekonsiliasi->count() }} total data</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('integrasi-bank.rekonsiliasi.pdf') }}" target="_blank"
                        class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg inline-flex items-center gap-1.5 hover:bg-red-700">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('integrasi-bank.rekonsiliasi.excel') }}"
                        class="px-3 py-1.5 text-xs bg-green-600 text-white rounded-lg inline-flex items-center gap-1.5 hover:bg-green-700">
                        <i class="fa fa-file-excel"></i> Excel
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari referensi, deskripsi..." oninput="filterRekonsiliasi(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-52">
                    </div>
                    <button onclick="openModalRekonsiliasi()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        <i class="fa fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Deskripsi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Reference</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Amount</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Currency</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Invoice</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">VA</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableRekonsiliasi">
                        @forelse ($rekonsiliasi as $item)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($item->tanggal.' '.$item->deskripsi.' '.$item->reference_no.' '.$item->currency.' '.$item->status_rekonsiliasi) }}">
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $rekonsiliasi->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->tanggal }}</td>
                                <td class="px-4 py-3.5 text-sm text-gray-700 max-w-[160px] truncate">{{ $item->deskripsi }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->reference_no }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-bold text-green-600">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($item->bukti_pembayaran)
                                        <a href="{{ asset($item->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat</a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->currency }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->invoice_id ?? '-' }}</td>
                                <td class="px-4 py-3.5">
                                    @if ($item->status_rekonsiliasi == 'matched')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Matched</span>
                                    @elseif ($item->status_rekonsiliasi == 'unmatched')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Unmatched</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->va ?? '-' }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick="openEditRekonsiliasi('{{ $item->id }}','{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}','{{ addslashes($item->deskripsi) }}','{{ $item->reference_no }}','{{ $item->amount }}','{{ $item->currency }}','{{ $item->status_rekonsiliasi }}','{{ $item->invoice_id }}','{{ $item->va }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="{{ route('integrasi-bank.rekonsiliasi.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus data ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                <i class="fa fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-bank text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data rekonsiliasi</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $rekonsiliasi->links() }}</div>
            </div>
        </div>
    </div>{{-- end panel-rekonsiliasi --}}


    {{-- ═══════════════════════════════════════════
         TAB: VIRTUAL ACCOUNT
    ═══════════════════════════════════════════ --}}
    <div id="panel-virtual" class="hidden">

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Virtual Account</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $virtualAccounts->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-credit-card-2-front-fill text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Expected</p>
                        <h2 class="text-lg font-bold text-indigo-600 mt-2 leading-tight">
                            Rp {{ number_format($virtualAccounts->sum('expected_amount'), 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">
                        <i class="bi bi-wallet2 text-2xl text-indigo-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Paid</p>
                        <h2 class="text-lg font-bold text-green-600 mt-2 leading-tight">
                            Rp {{ number_format($virtualAccounts->sum('paid_amount'), 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Pending</p>
                        <h2 class="text-3xl font-bold text-yellow-600 mt-2">{{ $virtualAccounts->where('status','Pending')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center">
                        <i class="bi bi-clock-history text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Paid</p>
                        <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $virtualAccounts->where('status','paid')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-check-circle-fill text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE VIRTUAL ACCOUNT --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Virtual Account</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $virtualAccounts->count() }} total data</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('integrasi-bank.virtual.pdf') }}" target="_blank"
                        class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg inline-flex items-center gap-1.5 hover:bg-red-700">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('integrasi-bank.virtual.excel') }}"
                        class="px-3 py-1.5 text-xs bg-green-600 text-white rounded-lg inline-flex items-center gap-1.5 hover:bg-green-700">
                        <i class="fa fa-file-excel"></i> Excel
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari VA, member, bank..." oninput="filterVirtual(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-52">
                    </div>
                    <button onclick="openModalVirtual()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        <i class="fa fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">VA Number</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Customer</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Invoice</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bank</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Expected</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Paid</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Expired</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableVirtual">
                        @forelse ($virtualAccounts as $item)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($item->va_number.' '.($item->member->nama_member ?? '').' '.$item->bank.' '.$item->status) }}">
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $virtualAccounts->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->va_number }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($item->member->nama_member ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $item->member->nama_member ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($item->invoice)
                                        <div class="text-xs">
                                            <span class="font-medium text-gray-700">{{ $item->invoice->invoice_no }}</span>
                                            <span class="text-gray-400 block">{{ $item->invoice->customer_name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($item->bukti_pembayaran)
                                        <a href="{{ asset($item->bukti_pembayaran) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">{{ basename($item->bukti_pembayaran) }}</a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->bank }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-medium text-indigo-600">Rp {{ number_format($item->expected_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-bold text-green-600">Rp {{ number_format($item->paid_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($item->status == 'paid')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Paid</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($item->expired_at)
                                        @php $isExpired = $item->expired_at->isPast(); @endphp
                                        <span class="text-xs font-medium {{ $isExpired ? 'text-red-500' : 'text-gray-600' }}">
                                            {{ $item->expired_at->format('d/m/Y H:i') }}
                                            @if ($isExpired)
                                                <span class="ml-1 px-1.5 py-0.5 rounded bg-red-100 text-red-600 text-[10px]">Expired</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick="openEditVirtual('{{ $item->id }}','{{ $item->va_number }}','{{ $item->member_id }}','{{ $item->invoice_id }}','{{ $item->bank }}','{{ $item->expected_amount }}','{{ $item->paid_amount }}','{{ $item->status }}','{{ $item->expired_at ? $item->expired_at->format('Y-m-d\TH:i') : '' }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="{{ route('integrasi-bank.virtual.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus data ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                <i class="fa fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-credit-card-2-front-fill text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data virtual account</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $virtualAccounts->links() }}</div>
            </div>
        </div>
    </div>{{-- end panel-virtual --}}

</div>{{-- end space-y-6 --}}



{{-- ═══════════════════════════════════════════
     MODAL TAMBAH REKONSILIASI
═══════════════════════════════════════════ --}}
<div id="modalRekonsiliasi" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Tambah Data Rekonsiliasi</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data rekonsiliasi bank dengan lengkap</p>
            </div>
            <button onclick="closeModalRekonsiliasi()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form action="{{ route('integrasi-bank.rekonsiliasi.store') }}" method="POST" enctype="multipart/form-data"
            class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reference No <span class="text-red-500">*</span></label>
                <input type="text" name="reference_no" required placeholder="Nomor referensi" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="deskripsi" rows="3" required placeholder="Deskripsi transaksi..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount <span class="text-red-500">*</span></label>
                <input type="number" name="amount" required placeholder="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Currency</label>
                <input type="text" name="currency" value="IDR" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                <select name="status_rekonsiliasi" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="Pending">Pending</option>
                    <option value="matched">Matched</option>
                    <option value="unmatched">Unmatched</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice ID</label>
                <input type="text" name="invoice_id" placeholder="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">VA</label>
                <input type="text" name="va" placeholder="Virtual Account" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran <span class="text-red-500">*</span></label>
                <input type="file" name="bukti_pembayaran" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="md:col-span-2 flex gap-3 pt-1">
                <button type="button" onclick="closeModalRekonsiliasi()" class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL EDIT REKONSILIASI
═══════════════════════════════════════════ --}}
<div id="modalEditRekonsiliasi" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Edit Data Rekonsiliasi</h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui data rekonsiliasi bank</p>
            </div>
            <button onclick="closeEditRekonsiliasi()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="formEditRekonsiliasi" method="POST" enctype="multipart/form-data"
            class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="er_tanggal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reference No <span class="text-red-500">*</span></label>
                <input type="text" name="reference_no" id="er_reference_no" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="deskripsi" id="er_deskripsi" rows="3" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="er_amount" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Currency</label>
                <input type="text" name="currency" id="er_currency" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                <select name="status_rekonsiliasi" id="er_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="Pending">Pending</option>
                    <option value="matched">Matched</option>
                    <option value="unmatched">Unmatched</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice ID</label>
                <input type="text" name="invoice_id" id="er_invoice_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">VA</label>
                <input type="text" name="va" id="er_va" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran (biarkan kosong jika tidak diubah)</label>
                <input type="file" name="bukti_pembayaran" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="md:col-span-2 flex gap-3 pt-1">
                <button type="button" onclick="closeEditRekonsiliasi()" class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════
     MODAL TAMBAH VIRTUAL ACCOUNT
═══════════════════════════════════════════ --}}
<div id="modalVirtual" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Tambah Virtual Account</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data virtual account dengan lengkap</p>
            </div>
            <button onclick="closeModalVirtual()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form action="{{ route('integrasi-bank.virtual.store') }}" method="POST" enctype="multipart/form-data"
            class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Member <span class="text-red-500">*</span></label>
                <select name="member_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_member }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank <span class="text-red-500">*</span></label>
                <input type="text" name="bank" required placeholder="Nama bank" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expected Amount <span class="text-red-500">*</span></label>
                <input type="number" name="expected_amount" required placeholder="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Paid Amount</label>
                <input type="number" name="paid_amount" value="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="Pending">Pending</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice</label>
                <select name="invoice_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">-- Pilih Invoice --</option>
                    @foreach ($invoices as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->invoice_no }} — {{ $inv->customer_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expired At</label>
                <input type="datetime-local" name="expired_at" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran</label>
                <input type="file" name="bukti_pembayaran" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="md:col-span-2 flex gap-3 pt-1">
                <button type="button" onclick="closeModalVirtual()" class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL EDIT VIRTUAL ACCOUNT
═══════════════════════════════════════════ --}}
<div id="modalEditVirtual" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Edit Virtual Account</h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui data virtual account</p>
            </div>
            <button onclick="closeEditVirtual()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="formEditVirtual" method="POST" enctype="multipart/form-data"
            class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">VA Number</label>
                <input type="text" name="va_number" id="ev_va_number" readonly class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Member <span class="text-red-500">*</span></label>
                <select name="member_id" id="ev_member_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_member }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice</label>
                <select name="invoice_id" id="ev_invoice_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">-- Pilih Invoice --</option>
                    @foreach ($invoices as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->invoice_no }} — {{ $inv->customer_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank <span class="text-red-500">*</span></label>
                <input type="text" name="bank" id="ev_bank" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expected Amount <span class="text-red-500">*</span></label>
                <input type="number" name="expected_amount" id="ev_expected_amount" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Paid Amount</label>
                <input type="number" name="paid_amount" id="ev_paid_amount" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                <select name="status" id="ev_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="Pending">Pending</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expired At</label>
                <input type="datetime-local" name="expired_at" id="ev_expired_at" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran (biarkan kosong jika tidak diubah)</label>
                <input type="file" name="bukti_pembayaran" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="md:col-span-2 flex gap-3 pt-1">
                <button type="button" onclick="closeEditVirtual()" class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════
     POPUP ALERT
═══════════════════════════════════════════ --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if (session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @elseif (session('error'))
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Validasi Gagal!</p>
                <ul class="text-xs text-gray-500 mt-0.5 list-disc list-inside">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endif

@push('scripts')
<script>
// ── Tab switching ──────────────────────────────────────────
function switchTab(tab) {
    const tabs = ['rekonsiliasi', 'virtual'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab-' + t);
        const panel = document.getElementById('panel-' + t);
        if (t === tab) {
            btn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            btn.classList.remove('text-gray-500', 'hover:text-gray-700');
            panel.classList.remove('hidden');
        } else {
            btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
            btn.classList.add('text-gray-500', 'hover:text-gray-700');
            panel.classList.add('hidden');
        }
    });
    localStorage.setItem('integrasiTab', tab);
}

// Restore tab on load
document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem('integrasiTab') || 'rekonsiliasi';
    switchTab(saved);

    // Alert popup
    const overlay = document.getElementById('alertOverlay');
    if (overlay) {
        overlay.style.opacity = '1';
        overlay.style.pointerEvents = 'auto';
        document.getElementById('alertBox').style.transform = 'translateY(0)';
        setTimeout(() => {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
        }, 3500);
    }
});

// ── Filter Rekonsiliasi ────────────────────────────────────
function filterRekonsiliasi(val) {
    const rows = document.querySelectorAll('#tableRekonsiliasi tr[data-search]');
    rows.forEach(r => {
        r.style.display = r.dataset.search.includes(val.toLowerCase()) ? '' : 'none';
    });
}

// ── Filter Virtual ─────────────────────────────────────────
function filterVirtual(val) {
    const rows = document.querySelectorAll('#tableVirtual tr[data-search]');
    rows.forEach(r => {
        r.style.display = r.dataset.search.includes(val.toLowerCase()) ? '' : 'none';
    });
}

// ── Modal helpers ──────────────────────────────────────────
function showModal(id) { document.getElementById(id).classList.replace('hidden', 'flex'); }
function hideModal(id) { document.getElementById(id).classList.replace('flex', 'hidden'); }

// Rekonsiliasi modals
function openModalRekonsiliasi()  { showModal('modalRekonsiliasi'); }
function closeModalRekonsiliasi() { hideModal('modalRekonsiliasi'); }
function closeEditRekonsiliasi()  { hideModal('modalEditRekonsiliasi'); }

function openEditRekonsiliasi(id, tanggal, deskripsi, ref, amount, currency, status, invoice_id, va) {
    document.getElementById('formEditRekonsiliasi').action = '/admin/integrasi-bank/rekonsiliasi/' + id;
    document.getElementById('er_tanggal').value     = tanggal;
    document.getElementById('er_deskripsi').value   = deskripsi;
    document.getElementById('er_reference_no').value = ref;
    document.getElementById('er_amount').value      = amount;
    document.getElementById('er_currency').value    = currency;
    document.getElementById('er_invoice_id').value  = invoice_id;
    document.getElementById('er_va').value          = va;
    const sel = document.getElementById('er_status');
    for (let o of sel.options) o.selected = (o.value === status);
    showModal('modalEditRekonsiliasi');
}

// Virtual Account modals
function openModalVirtual()  { showModal('modalVirtual'); }
function closeModalVirtual() { hideModal('modalVirtual'); }
function closeEditVirtual()  { hideModal('modalEditVirtual'); }

function openEditVirtual(id, va_number, member_id, invoice_id, bank, expected, paid, status, expired) {
    document.getElementById('formEditVirtual').action = '/admin/integrasi-bank/virtual/' + id;
    document.getElementById('ev_va_number').value        = va_number;
    document.getElementById('ev_bank').value             = bank;
    document.getElementById('ev_expected_amount').value  = expected;
    document.getElementById('ev_paid_amount').value      = paid;
    document.getElementById('ev_expired_at').value       = expired;

    const mSel = document.getElementById('ev_member_id');
    for (let o of mSel.options) o.selected = (o.value == member_id);

    const iSel = document.getElementById('ev_invoice_id');
    for (let o of iSel.options) o.selected = (o.value == invoice_id);

    const sSel = document.getElementById('ev_status');
    for (let o of sSel.options) o.selected = (o.value === status);

    showModal('modalEditVirtual');
}

// Close modal on backdrop click
['modalRekonsiliasi','modalEditRekonsiliasi','modalVirtual','modalEditVirtual'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.replace('flex', 'hidden');
    });
});
</script>

<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>
@endpush

@endsection
