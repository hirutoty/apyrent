@extends('admin.layouts.app')

@section('title', 'Keuangan')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Keuangan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola pemasukan dan pengeluaran</p>
        </div>

    </div>

    {{--  NAVIGATION --}}
    <div class="flex gap-1 border-b border-gray-200 mb-0">
        <button onclick="switchTab('cashflow')" id="tab-cashflow"
            class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-blue-600
  text-blue-600 transition-all">
            <i class="fa fa-wallet mr-1"></i> Cash Flow
        </button>
        <button onclick="switchTab('aging-ap')" id="tab-aging-ap"
            class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent
  text-gray-400 hover:text-gray-600 transition-all">
            <i class="fa fa-file-invoice mr-1"></i> Aging AP
        </button>
        <button onclick="switchTab('aging-ar')" id="tab-aging-ar"
            class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent
  text-gray-400 hover:text-gray-600 transition-all">
            <i class="fa fa-file-invoice-dollar mr-1"></i> Aging AR
        </button>
        <button onclick="switchTab('reminder')" id="tab-reminder"
            class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent
  text-gray-400 hover:text-gray-600 transition-all">
            <i class="fa fa-bell mr-1"></i> Reminder AR
        </button>
        <button onclick="switchTab('lunas')" id="tab-lunas"
            class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent
  text-gray-400 hover:text-gray-600 transition-all">
            <i class="fa fa-circle-check mr-1"></i> AR Lunas
        </button>
    </div>


    {{-- ======================================
        CASHFLOW
  ====================================== --}}
    <div id="content-cashflow">
        <div class="space-y-6 pt-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                  
                </div>
                <button onclick="openModalKeuangan()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white
  text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                    <i class="fa fa-plus text-sm"></i> Tambah Transaksi
                </button>
            </div>

            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex
  items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center
  flex-shrink-0">
                        <i class="fa fa-hand-holding-usd text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Total Pemasukan</p>
                        <p class="text-lg font-bold text-green-600">Rp {{ number_format($totalPemasukan) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex
  items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center
  flex-shrink-0">
                        <i class="fa fa-money-bill-wave text-red-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Total Pengeluaran</p>
                        <p class="text-lg font-bold text-red-600">Rp {{ number_format($totalPengeluaran) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex
  items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center
  flex-shrink-0">
                        <i class="fa fa-wallet text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Saldo</p>
                        <p class="text-lg font-bold text-blue-600">Rp {{ number_format($saldo) }}</p>
                    </div>
                </div>
            </div>

            {{-- LE --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 space-y-3">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between
  gap-4">
                        <div>
                            <h2 class="font-semibold text-gray-800 text-base">Daftar
                                Transaksi</h2>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $keuangans->count() }}
                                total transaksi</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('keuangan.export.pdf', request()->query()) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 text-xs
  font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                                <i class="fa fa-file-pdf text-sm"></i> Export PDF
                            </a>
                            <a href="{{ route('keuangan.export.excel', request()->query()) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
                                <i class="fa fa-file-excel text-sm"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="GET" action="{{ route('keuangan.index') }}">
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="relative">
                                    <i
                                        class="fa fa-calendar-day absolute left-2.5 top-1/2
  -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    <select name="hari" onchange="this.form.submit()"
                                        class="pl-7 pr-6 py-1.5 text-xs border border-gray-200
  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400
  appearance-none bg-white cursor-pointer">
                                        <option value="">Semua Hari</option>
                                        @for ($d = 1; $d <= 31; $d++)
                                            <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}"
                                                {{ request('hari') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ str_pad($d, 2, '0', STR_PAD_LEFT) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="relative">
                                    <i
                                        class="fa fa-calendar-alt absolute left-2.5 top-1/2
  -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    <select name="bulan" onchange="this.form.submit()"
                                        class="pl-7 pr-6 py-1.5 text-xs border border-gray-200
  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400
  appearance-none bg-white cursor-pointer">
                                        <option value="">Semua Bulan</option>
                                        <option value="01" {{ request('bulan') == '01' ? 'selected' : '' }}>Januari
                                        </option>
                                        <option value="02" {{ request('bulan') == '02' ? 'selected' : '' }}>Februari
                                        </option>
                                        <option value="03" {{ request('bulan') == '03' ? 'selected' : '' }}>Maret
                                        </option>
                                        <option value="04" {{ request('bulan') == '04' ? 'selected' : '' }}>April
                                        </option>
                                        <option value="05" {{ request('bulan') == '05' ? 'selected' : '' }}>Mei
                                        </option>
                                        <option value="06" {{ request('bulan') == '06' ? 'selected' : '' }}>Juni
                                        </option>
                                        <option value="07" {{ request('bulan') == '07' ? 'selected' : '' }}>Juli
                                        </option>
                                        <option value="08" {{ request('bulan') == '08' ? 'selected' : '' }}>Agustus
                                        </option>
                                        <option value="09" {{ request('bulan') == '09' ? 'selected' : '' }}>September
                                        </option>
                                        <option value="10" {{ request('bulan') == '10' ? 'selected' : '' }}>Oktober
                                        </option>
                                        <option value="11" {{ request('bulan') == '11' ? 'selected' : '' }}>November
                                        </option>
                                        <option value="12" {{ request('bulan') == '12' ? 'selected' : '' }}>Desember
                                        </option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <i
                                        class="fa fa-calendar absolute left-2.5 top-1/2
  -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    <select name="tahun" onchange="this.form.submit()"
                                        class="pl-7 pr-6 py-1.5 text-xs border border-gray-200
  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400
  appearance-none bg-white cursor-pointer">
                                        <option value="">Semua Tahun</option>
                                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                            <option value="{{ $y }}"
                                                {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="relative">
                                    <i
                                        class="fa fa-filter absolute left-2.5 top-1/2
  -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    <select name="jenis" onchange="this.form.submit()"
                                        class="pl-7 pr-6 py-1.5 text-xs border border-gray-200
  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400
  appearance-none bg-white cursor-pointer">
                                        <option value="">Semua Transaksi</option>
                                        <option value="Pemasukan" {{ request('jenis') == 'Pemasukan' ? 'selected' : '' }}>
                                            Pemasukan</option>
                                        <option value="Pengeluaran"
                                            {{ request('jenis') == 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran
                                        </option>
                                    </select>
                                </div>
                                <div class="relative flex-1 min-w-[160px]">
                                    <i
                                        class="fa fa-search absolute left-3 top-1/2
  -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Cari kategori..."
                                        onkeydown="if(event.key=='Enter') this.form.submit();"
                                        class="w-full pl-9 pr-3 py-2 text-sm border
  border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
  focus:border-blue-500 transition">
                                </div>
                                <a href="{{ route('keuangan.index') }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs
  font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <i class="fa fa-times-circle"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    No</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    Tanggal</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    Reference</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    User</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    Kategori</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    Keterangan</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    Pemasukan</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    Pengeluaran</th>
                                <th
                                    class="text-left text-xs font-semibold uppercase tracking-wide
  text-gray-500 px-4 py-3">
                                    Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="keuanganTableBody">
                            @forelse($keuangans as $i => $k)
                                <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100">
                                    <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">
                                        {{ $keuangans->firstItem() + $i }}</td>
                                    <td class="px-4 py-3.5 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($k->tanggal)->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3.5">
                                        <span
                                            class="font-mono text-xs text-gray-600 bg-gray-100
  px-2 py-0.5 rounded">{{ $k->reference ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-7 h-7 rounded-full bg-blue-50
  text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                {{ strtoupper(substr($k->user->name ?? 'U', 0, 2)) }}
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $k->user->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-sm text-gray-700">{{ $k->kategori }}</td>
                                    <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[180px]
  truncate">
                                        {{ $k->keterangan }}</td>
                                    <td class="px-4 py-3.5">
                                        @if ($k->pemasukan > 0)
                                            <span class="text-sm font-semibold text-green-600">Rp
                                                {{ number_format($k->pemasukan) }}</span>
                                        @else
                                            <span class="text-xs text-gray-300">�</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if ($k->pengeluaran > 0)
                                            <span class="text-sm font-semibold text-red-500">Rp
                                                {{ number_format($k->pengeluaran) }}</span>
                                        @else
                                            <span class="text-xs text-gray-300">�</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="text-sm font-bold text-blue-600">Rp
                                            {{ number_format($k->saldo) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="w-14 h-14 rounded-full bg-gray-100 flex
  items-center justify-center">
                                                <i class="fa fa-wallet text-2xl
  text-gray-300"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500">Belum ada
                                                data transaksi</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="py-4 border-t border-gray-100">
                        {{ $keuangans->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{-- / CASHFLOW --}}


    {{-- ======================================
        AGING AP
  ====================================== --}}
    <div id="content-aging-ap" class="hidden">
        <div class="space-y-6 pt-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Aging AP</h1>
                    <p class="text-sm text-slate-500 mt-1">Monitoring umur hutang (accounts
                        payable) vendor</p>
                </div>
                <button onclick="openModalAp()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white
  text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                    <i class="fa fa-plus text-sm"></i> Tambah Data
                </button>
            </div>

            {{-- FILTER AGING AP --}}
            <form method="GET" action="{{ route('keuangan.index') }}">
                <input type="hidden" name="tab" value="aging-ap">
                @foreach(request()->except(['hari_ap','bulan_ap','tahun_ap','tab']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <div class="flex flex-wrap items-center gap-2 mb-2 bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500"><i class="fa fa-filter mr-1"></i>Filter Jatuh Tempo:</span>
                    <div class="relative">
                        <i class="fa fa-calendar-day absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select name="hari_ap" onchange="this.form.submit()" class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 appearance-none bg-white cursor-pointer">
                            <option value="">Semua Hari</option>
                            @for ($d = 1; $d <= 31; $d++)
                                <option value="{{ str_pad($d,2,'0',STR_PAD_LEFT) }}" {{ request('hari_ap')==str_pad($d,2,'0',STR_PAD_LEFT)?'selected':'' }}>{{ str_pad($d,2,'0',STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fa fa-calendar-alt absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select name="bulan_ap" onchange="this.form.submit()" class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 appearance-none bg-white cursor-pointer">
                            <option value="">Semua Bulan</option>
                            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $bv=>$bl)
                                <option value="{{ $bv }}" {{ request('bulan_ap')==$bv?'selected':'' }}>{{ $bl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fa fa-calendar absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select name="tahun_ap" onchange="this.form.submit()" class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 appearance-none bg-white cursor-pointer">
                            <option value="">Semua Tahun</option>
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ request('tahun_ap')==$y?'selected':'' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    @if(request()->hasAny(['hari_ap','bulan_ap','tahun_ap']))
                    <a href="{{ route('keuangan.index') }}?tab=aging-ap" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fa fa-times-circle"></i> Reset
                    </a>
                    @endif
                </div>
            </form>

            {{-- SUMMARY CARDS AP --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Total Tagihan</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $dataAp->count() }}</h3>
                        </div>
                        <div
                            class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex
  items-center justify-center">
                            <i class="fa-solid fa-file-invoice text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Jatuh Tempo Segera</p>
                            <h3 class="text-3xl font-bold text-amber-600 mt-2">
                                {{ $dataAp->filter(function ($d) use ($reminderAp) {
                                        $sisa = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay(), false);
                                        return $sisa >= 0 && $sisa <= $reminderAp;
                                    })->count() }}
                            </h3>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex
  items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Terlambat</p>
                            <h3 class="text-3xl font-bold text-red-600 mt-2">
                                {{ $dataAp->filter(function ($d) {
                                        return \Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay()->lt(now()->startOfDay());
                                    })->count() }}
                            </h3>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex
  items-center justify-center">
                            <i class="fa-solid fa-circle-xmark text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Total Nilai Hutang</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-2">Rp
                                {{ number_format($dataAp->sum('jumlah')) }}</h3>
                        </div>
                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex
  items-center justify-center">
                            <i class="fa-solid fa-wallet text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LE AP --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3
  px-5 py-4 border-b border-slate-100">
                    <div>
                        <h2 class="font-semibold text-slate-800 text-base">Daftar Aging AP</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $dataAp->count() }} total
                            tagihan tercatat</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2
  text-gray-400 text-xs"></i>
                            <input type="text" placeholder="Cari no. tagihan, vendor..."
                                oninput="filterTableAp(this.value)"
                                class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg
  focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 w-56">
                        </div>
                        <button onclick="window.location.reload()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs
  font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50
  transition-colors">
                            <i class="fa fa-sync text-xs"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-4 text-left font-semibold
  text-slate-600">No</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">No.
                                    Tagihan</th>
                                <th class="px-5 py-4 text-left font-semibold
  text-slate-600">Vendor</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Jatuh
                                    Tempo</th>
                                <th class="px-5 py-4 text-left font-semibold
  text-slate-600">Umur</th>
                                <th class="px-5 py-4 text-left font-semibold
  text-slate-600">Jumlah</th>
                                <th class="px-5 py-4 text-left font-semibold
  text-slate-600">Kategori</th>
                                <th class="px-5 py-4 text-center font-semibold
  text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyAp" class="divide-y divide-slate-100">
                            @forelse ($dataAp as $d)
                                <tr class="hover:bg-slate-50 transition"
                                    data-search="{{ strtolower($d->no_tagihan . ' ' . $d->vendor . ' ' . $d->kategori) }}">
                                    <td class="px-4 py-3.5 text-xs text-slate-400 font-medium">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-4 font-medium text-slate-800">{{ $d->no_tagihan }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $d->vendor }}</td>
                                    <td class="px-5 py-4">
                                        @php
                                            $jatuhTempo = \Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay();
                                            $hariIni = now()->startOfDay();
                                            $sisaHari = (int) $hariIni->diffInDays($jatuhTempo, false);
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <span class="text-slate-600 text-sm">{{ $jatuhTempo->format('d M Y') }}</span>
                                            @if ($sisaHari < 0)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-red-600 bg-red-50 border border-red-200 px-2 py-1
  rounded-full w-fit">
                                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                                    Terlambat {{ abs($sisaHari) }} hari
                                                </span>
                                            @elseif ($sisaHari <= $reminderAp)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1
  rounded-full w-fit">
                                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                    @if ($sisaHari == 0)
                                                        Jatuh Tempo Hari Ini
                                                    @elseif ($sisaHari == 1)
                                                        Jatuh Tempo Besok
                                                    @else
                                                        Jatuh Tempo dalam {{ $sisaHari }} hari
                                                    @endif
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2
  py-1 rounded-full w-fit">
                                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                    Belum Jatuh Tempo
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-slate-800">{{ $d->umur_otomatis }} Hari</td>
                                    <td class="px-5 py-4 font-semibold text-slate-800">Rp {{ number_format($d->jumlah) }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $d->kategori == 'Current'
                                                ? 'bg-blue-100 text-blue-700'
                                                : 'bg-red-100
                                                                                                                                                                                                                              text-red-700' }}">
                                            {{ $d->kategori }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="openEditModalAp({{ $d }})"
                                                class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center
  gap-1">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                            </button>
                                            <form action="{{ route('aging_ap.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-trash text-xs"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-slate-400">
                                        <i class="fa-solid fa-file-invoice text-4xl mb-3 block"></i>
                                        Belum ada data aging AP
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    {{-- / AGING AP --}}


    {{-- ======================================
        AGING AR
  ====================================== --}}
    <div id="content-aging-ar" class="hidden">
        <div class="space-y-6 pt-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Aging AR</h1>
                    <p class="text-sm text-slate-500 mt-1">Monitoring umur piutang (accounts receivable) customer</p>
                </div>
                <button type="button" onclick="openModalAr()"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl
  shadow-sm transition-colors duration-150">
                    <i class="fa fa-plus text-sm"></i> Tambah Data
                </button>
            </div>

            {{-- FILTER AGING AR --}}
            <form method="GET" action="{{ route('keuangan.index') }}">
                <input type="hidden" name="tab" value="aging-ar">
                @foreach(request()->except(['hari_ar','bulan_ar','tahun_ar','tab']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <div class="flex flex-wrap items-center gap-2 mb-2 bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500"><i class="fa fa-filter mr-1"></i>Filter Jatuh Tempo:</span>
                    <div class="relative">
                        <i class="fa fa-calendar-day absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select name="hari_ar" onchange="this.form.submit()" class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 appearance-none bg-white cursor-pointer">
                            <option value="">Semua Hari</option>
                            @for ($d = 1; $d <= 31; $d++)
                                <option value="{{ str_pad($d,2,'0',STR_PAD_LEFT) }}" {{ request('hari_ar')==str_pad($d,2,'0',STR_PAD_LEFT)?'selected':'' }}>{{ str_pad($d,2,'0',STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fa fa-calendar-alt absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select name="bulan_ar" onchange="this.form.submit()" class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 appearance-none bg-white cursor-pointer">
                            <option value="">Semua Bulan</option>
                            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $bv=>$bl)
                                <option value="{{ $bv }}" {{ request('bulan_ar')==$bv?'selected':'' }}>{{ $bl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fa fa-calendar absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select name="tahun_ar" onchange="this.form.submit()" class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 appearance-none bg-white cursor-pointer">
                            <option value="">Semua Tahun</option>
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ request('tahun_ar')==$y?'selected':'' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    @if(request()->hasAny(['hari_ar','bulan_ar','tahun_ar']))
                    <a href="{{ route('keuangan.index') }}?tab=aging-ar" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fa fa-times-circle"></i> Reset
                    </a>
                    @endif
                </div>
            </form>

            {{-- SUMMARY CARDS AR --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Total Invoice</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $dataAr->count() }}</h3>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Piutang Lancar</p>
                            <h3 class="text-3xl font-bold text-green-600 mt-2">
                                {{ $dataAr->where('kategori', 'Current')->count() }}</h3>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fa-solid fa-circle-check text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Piutang Overdue</p>
                            <h3 class="text-3xl font-bold text-red-600 mt-2">
                                {{ $dataAr->where('kategori', '!=', 'Current')->count() }}</h3>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                            <i class="fa-solid fa-circle-xmark text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Total Piutang</p>
                            <h3 class="text-2xl font-bold text-amber-600 mt-2">Rp
                                {{ number_format($dataAr->sum('total')) }}</h3>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-wallet text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LE AR --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
                    <div>
                        <h2 class="font-semibold text-slate-800 text-base">Daftar Aging AR</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $dataAr->count() }} total invoice tercatat</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" placeholder="Cari invoice, customer, kategori..."
                                oninput="filterTableAr(this.value)"
                                class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100
  focus:border-indigo-400 w-56">
                        </div>
                        <button onclick="window.location.reload()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg
  odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                            <i class="fa fa-sync text-xs"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Invoice</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Customer</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Email</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Kontak</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Jatuh Tempo</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Umur</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Total</th>
                                <th class="px-5 py-4 text-left font-semibold text-slate-600">Kategori</th>
                                <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyAr" class="divide-y divide-slate-100">
                            @forelse ($dataAr as $d)
                                <tr class="hover:bg-slate-50 transition"
                                    data-search="{{ strtolower(
                                        $d->invoice->invoice_no .
                                            ' ' .
                                            $d->member->nama_pelanggan .
                                            ' ' .
                                            ($d->member->email_pelanggan ?? '') .
                                            ' ' .
                                            $d->kategori,
                                    ) }}">
                                    <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-4 font-medium text-slate-800">{{ $d->invoice->invoice_no }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $d->member->nama_pelanggan }}</td>
                                    <td class="px-5 py-4 text-slate-600 text-xs">{{ $d->member->email_pelanggan ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 text-xs">{{ $d->member->kontak_pelanggan ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @php
                                            $jatuhTempoAr = \Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay();
                                            $sisaHariAr = now()->startOfDay()->diffInDays($jatuhTempoAr, false);
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-slate-600 text-sm">{{ $jatuhTempoAr->format('d M Y') }}</span>
                                            @if ($d->status != 'Bayar')
                                                @if ($sisaHariAr < 0)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-red-600 bg-red-50 border border-red-200 px-2 py-1
  rounded-full w-fit">
                                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                                        Terlambat {{ abs($sisaHariAr) }} hari
                                                    </span>
                                                @elseif ($sisaHariAr <= $reminderAr)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1
  rounded-full w-fit">
                                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                        @if ($sisaHariAr == 0)
                                                            Jatuh Tempo Hari Ini
                                                        @elseif ($sisaHariAr == 1)
                                                            Jatuh Tempo Besok
                                                        @else
                                                            Jatuh Tempo dalam {{ $sisaHariAr }} hari
                                                        @endif
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-slate-800">{{ $d->umur_otomatis ?? $d->umur }}
                                        Hari</td>
                                    <td class="px-5 py-4 font-semibold text-slate-800">Rp {{ number_format($d->total) }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($d->status == 'Bayar')
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Lunas
                                            </span>
                                        @elseif ($d->kategori == 'Current')
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Current</span>
                                        @else
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $d->kategori }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick='openEditModalAr(@json($d))'
                                                class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center
  gap-1">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                            </button>
                                            <form action="{{ route('aging_ar.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-trash text-xs"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-12 text-slate-400">
                                        <i class="fa-solid fa-file-invoice-dollar text-4xl mb-3 block"></i>
                                        Belum ada data aging AR
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    {{-- / AGING AR --}}
    {{-- ======================================
       TAB REMINDER AR
  ====================================== --}}
    <div id="content-reminder" class="hidden">
        <div class="space-y-6 pt-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Reminder Aging AR</h1>
                    <p class="text-sm text-slate-500 mt-1">Daftar piutang yang perlu ditindaklanjuti</p>
                </div>
                <div class="px-4 py-2 rounded-xl bg-amber-100 text-amber-700 font-semibold text-sm">
                    Total Reminder: {{ $dataReminder->count() }}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-700">Daftar Piutang</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-4 text-left">No</th>
                                <th class="px-5 py-4 text-left">Invoice</th>
                                <th class="px-5 py-4 text-left">Customer</th>
                                <th class="px-5 py-4 text-left">Email</th>
                                <th class="px-5 py-4 text-left">Kontak</th>
                                <th class="px-5 py-4 text-left">Total</th>
                                <th class="px-5 py-4 text-center">Status</th>
                                <th class="px-5 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dataReminder as $d)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-4 font-semibold text-slate-800">{{ $d->invoice->invoice_no }}</td>
                                    <td class="px-5 py-4">{{ $d->member->nama_pelanggan }}</td>
                                    <td class="px-5 py-4">{{ $d->member->email_pelanggan ?? '-' }}</td>
                                    <td class="px-5 py-4">{{ $d->member->kontak_pelanggan ?? '-' }}</td>
                                    <td class="px-5 py-4 font-semibold text-red-600">Rp
                                        {{ number_format($d->total, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700
  text-xs font-semibold">
                                            <i class="fa-solid fa-circle-exclamation"></i> Belum Lunas
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($d->status == 'Belum Bayar')
                                            <button type="button"
                                                onclick="document.getElementById('modal-bayar-{{ $d->id }}').classList.remove('hidden')"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium
  transition">
                                                <i class="fa-solid fa-money-bill"></i> Bayar
                                            </button>

                                            {{-- Modal Upload Bukti --}}
                                            <div id="modal-bayar-{{ $d->id }}"
                                                class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                                <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 relative">
                                                    <button type="button"
                                                        onclick="document.getElementById('modal-bayar-{{ $d->id }}').classList.add('hidden')"
                                                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                                                        <i class="fa-solid fa-xmark text-lg"></i>
                                                    </button>
                                                    <h3 class="text-lg font-bold text-slate-800 mb-1">Konfirmasi Pembayaran
                                                    </h3>
                                                    <p class="text-sm text-slate-500 mb-4">
                                                        Invoice: <strong>{{ $d->invoice->invoice_no }}</strong>
                                                    </p>
                                                    <form action="{{ route('aging_ar.bayar', $d->id) }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <label class="block text-sm font-medium text-slate-700 mb-2">Upload
                                                            Bukti Bayar</label>
                                                        <input type="file" name="bukti" required
                                                            class="block w-full text-sm border border-slate-300 rounded-lg cursor-pointer mb-1
  file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                        @error('bukti')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                        <div class="flex justify-end gap-2 mt-5">
                                                            <button type="button"
                                                                onclick="document.getElementById('modal-bayar-{{ $d->id }}').classList.add('hidden')"
                                                                class="px-4 py-2 rounded-lg text-sm font-medium border border-slate-300 text-slate-600
  hover:bg-slate-50">
                                                                Batal
                                                            </button>
                                                            <button type="submit"
                                                                class="px-4 py-2 rounded-lg text-sm font-medium bg-green-600 hover:bg-green-700 text-white">
                                                                <i class="fa-solid fa-upload"></i> Simpan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <form action="{{ route('aging_ar.lunas.index', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin invoice ini sudah lunas?')">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs
  font-medium transition">
                                                    <i class="fa-solid fa-circle-check"></i> Tandai Lunas
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-14 text-slate-400">
                                        <i class="fa-solid fa-circle-check text-5xl mb-3 block text-green-500"></i>
                                        <p class="font-semibold text-lg">Tidak ada reminder.</p>
                                        <p class="text-sm">Semua piutang sudah lunas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    {{-- / TAB REMINDER AR --}}


    {{-- ======================================
       TAB AR LUNAS
  ====================================== --}}
    <div id="content-lunas" class="hidden">
        <div class="space-y-6 pt-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Data Lunas</h1>
                    <p class="text-sm text-slate-500 mt-1">Daftar invoice yang sudah dibayar</p>
                </div>
                <div class="px-4 py-2 rounded-xl bg-green-100 text-green-700 font-semibold text-sm">
                    Total Lunas: {{ $dataLunas->count() }}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-700">Daftar Pembayaran</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-4 text-left">No</th>
                                <th class="px-5 py-4 text-left">Invoice</th>
                                <th class="px-5 py-4 text-left">Customer</th>
                                <th class="px-5 py-4 text-left">Total</th>
                                <th class="px-5 py-4 text-center">Status</th>
                                <th class="px-5 py-4 text-center">Bukti Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dataLunas as $d)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-4 font-semibold text-slate-800">{{ $d->invoice->invoice_no }}</td>
                                    <td class="px-5 py-4">{{ $d->member->nama_pelanggan }}</td>
                                    <td class="px-5 py-4 font-semibold text-green-600">Rp
                                        {{ number_format($d->total, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700
  text-xs font-semibold">
                                            <i class="fa-solid fa-circle-check"></i> Bayar
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($d->bukti)
                                            <a href="{{ asset('bukti/' . $d->bukti) }}" target="_blank"
                                                class="text-blue-600 hover:underline text-xs font-medium">
                                                <i class="fa-solid fa-image"></i> {{ $d->bukti }}
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-xs">Tidak ada bukti</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-14 text-slate-400">
                                        <i class="fa-solid fa-box-open text-5xl mb-3 block text-slate-300"></i>
                                        <p class="font-semibold text-lg">Belum ada data lunas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    {{-- / TAB AR LUNAS --}}

    {{-- ======================================
       MODAL TAMBAH KEUANGAN
  ====================================== --}}
    <div id="modalKeuangan" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4" style="animation:slideUp .2s ease">
            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Data</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data transaksi keuangan</p>
                </div>
                <button onclick="closeModalKeuangan()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form action="{{ route('keuangan.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Transaksi</label>
                    <select name="jenis" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span
                            class="text-blue-500">*</span></label>
                    <input type="text" name="kategori" required placeholder="Contoh: BBM, Servis, dll"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100
  focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Metode <span
                            class="text-blue-500">*</span></label>
                    <input type="text" name="metode" required placeholder="Cash / Transfer"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100
  focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal Transaksi <span
                            class="text-blue-500">*</span></label>
                    <input type="number" name="nominal" required placeholder="Contoh: 150000"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100
  focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span
                            class="text-blue-500">*</span></label>
                    <input type="text" name="keterangan" required placeholder="Keterangan singkat transaksi"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100
  focus:border-blue-400">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex
  items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan Transaksi
                </button>
            </form>
        </div>
    </div>


    {{-- ======================================
       MODAL TAMBAH AGING AP
  ====================================== --}}
    <div id="modalTambahAp"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah</h2>
                    <p class="text-sm text-slate-500">Isi data tagihan vendor baru</p>
                </div>
                <button onclick="closeModalAp()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex
  items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form action="{{ route('aging_ap.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Vendor <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="vendor" required placeholder="Nama vendor"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Jumlah <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" required placeholder="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center
  justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data
                </button>
            </form>
        </div>
    </div>


    {{-- ======================================
       MODAL EDIT AGING AP
  ====================================== --}}
    <div id="modalEditAp"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Edit Aging AP</h2>
                    <p class="text-sm text-slate-500">Perbarui data tagihan vendor</p>
                </div>
                <button onclick="closeEditModalAp()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex
  items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="formEditAp" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Vendor <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="vendor" id="ap_vendor" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="ap_jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Jumlah <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" id="ap_jumlah" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center
  justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Data
                </button>
            </form>
        </div>
    </div>


    {{-- ======================================
       MODAL TAMBAH AGING AR
  ====================================== --}}
    <div id="modalTambahAr"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah Aging AR</h2>
                    <p class="text-sm text-slate-500">Isi data piutang customer baru</p>
                </div>
                <button onclick="closeModalAr()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex
  items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="formTambahAr" action="{{ route('aging_ar.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Customer <span
                                class="text-red-500">*</span></label>
                        <select id="ar_member_id" name="member_id" style="width:100%"></select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Invoice <span
                                class="text-red-500">*</span></label>
                        <select id="ar_invoice_id" name="invoice_id" style="width:100%"></select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Email Member</label>
                        <input type="text" id="ar_add_email" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kontak Member</label>
                        <input type="number" id="ar_add_kontak" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="ar_add_jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Total <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="total" id="ar_add_total" required placeholder="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kategori <span
                                class="text-red-500">*</span></label>
                        <select name="kategori" id="ar_add_kategori" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="Current">Current</option>
                            <option value="1-30 Hari">1-30 Hari</option>
                            <option value="31-60 Hari">31-60 Hari</option>
                            <option value="61-90 Hari">61-90 Hari</option>
                            <option value=">90 Hari">&gt;90 Hari</option>
                        </select>
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center
  justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data
                </button>
            </form>
        </div>
    </div>


    {{-- ======================================
       MODAL EDIT AGING AR
  ====================================== --}}
    <div id="modalEditAr"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Edit Aging AR</h2>
                    <p class="text-sm text-slate-500">Perbarui data piutang customer</p>
                </div>
                <button onclick="closeEditModalAr()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex
  items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="formEditAr" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Customer <span
                                class="text-red-500">*</span></label>
                        <select id="ar_edit_member_id" name="member_id" style="width:100%"></select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Invoice <span
                                class="text-red-500">*</span></label>
                        <select id="ar_edit_invoice_id" name="invoice_id" style="width:100%"></select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Email Member</label>
                        <input type="text" id="ar_edit_email" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kontak Member</label>
                        <input type="number" id="ar_edit_kontak" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="ar_edit_jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Total <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="total" id="ar_edit_total" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center
  justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Data
                </button>
            </form>
        </div>
    </div>


    {{-- ======================================
       POPUP ALERT
  ====================================== --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">
                @if (session('success'))
                    <div
                        class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif (session('error'))
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button onclick="closeAlert()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    @endif


    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z'
     fill='%239ca3af'/%3E%3C/svg%3E");
     background-repeat: no-repeat;
                    background-position: right 8px center;
                    padding-right: 24px !important;
            }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function switchTab(tab) {
            ['cashflow', 'aging-ap', 'aging-ar', 'reminder', 'lunas'].forEach(t => {
                document.getElementById('content-' + t).classList.add('hidden');
                const btn = document.getElementById('tab-' + t);
                btn.classList.remove('border-blue-600', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-400');
            });
            document.getElementById('content-' + tab).classList.remove('hidden');
            const active = document.getElementById('tab-' + tab);
            active.classList.add('border-blue-600', 'text-blue-600');
            active.classList.remove('border-transparent', 'text-gray-400');
        }

        // -- PERTAHANKAN  AKTIF ------------------------------

        (function() {
            let targetTab = null;

            // 1) Cek URL param ?tab= (dari filter Aging AP/AR)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab')) {
                targetTab = urlParams.get('tab');
            }

            @if (session('active_tab'))
                if (!targetTab) targetTab = '{{ session('active_tab') }}';
            @endif

            if (!targetTab && sessionStorage.getItem('justSubmitted')) {
                targetTab = sessionStorage.getItem('activeTab');
            }
            sessionStorage.removeItem('justSubmitted');

            if (targetTab) {
                switchTab(targetTab);
            }

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    sessionStorage.setItem('activeTab', this.id.replace('tab-', ''));
                });
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const tabs = ['cashflow', 'aging-ap', 'aging-ar', 'reminder', 'lunas'];
                    const active = tabs.find(t => !document.getElementById('content-' + t).classList
                        .contains('hidden'));
                    if (active) sessionStorage.setItem('activeTab', active);
                    sessionStorage.setItem('justSubmitted', '1');
                });
            });
        })();
        // -- MODAL KEUANGAN -------------------------------------
        function openModalKeuangan() {
            var m = document.getElementById('modalKeuangan');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalKeuangan() {
            var m = document.getElementById('modalKeuangan');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalKeuangan').addEventListener('click', function(e) {
            if (e.target === this) closeModalKeuangan();
        });

        // -- MODAL AGING AP -------------------------------------
        function openModalAp() {
            document.getElementById('modalTambahAp').classList.remove('hidden');
        }

        function closeModalAp() {
            document.getElementById('modalTambahAp').classList.add('hidden');
        }

        function openEditModalAp(data) {
            document.getElementById('ap_vendor').value = data.vendor;
            document.getElementById('ap_jatuh_tempo').value = data.jatuh_tempo.substring(0, 10);
            document.getElementById('ap_jumlah').value = data.jumlah;
            document.getElementById('formEditAp').action = '/admin/aging_ap/' + data.id;
            document.getElementById('modalEditAp').classList.remove('hidden');
        }

        function closeEditModalAp() {
            document.getElementById('modalEditAp').classList.add('hidden');
        }
        document.getElementById('modalTambahAp').addEventListener('click', function(e) {
            if (e.target === this) closeModalAp();
        });
        document.getElementById('modalEditAp').addEventListener('click', function(e) {
            if (e.target === this) closeEditModalAp();
        });

        // -- MODAL AGING AR -------------------------------------
        function openModalAr() {
            $('#ar_member_id').val(null).trigger('change');
            $('#ar_invoice_id').val(null).trigger('change');
            document.getElementById('ar_add_email').value = '';
            document.getElementById('ar_add_kontak').value = '';
            document.getElementById('formTambahAr').reset();
            document.getElementById('modalTambahAr').classList.remove('hidden');
        }

        function closeModalAr() {
            document.getElementById('modalTambahAr').classList.add('hidden');
        }

        function openEditModalAr(data) {
            $('#ar_edit_invoice_id').empty().append(
                new Option(data.invoice.invoice_no, data.invoice.id, true, true)
            ).trigger('change');
            $('#ar_edit_member_id').empty().append(
                new Option(data.member.nama_pelanggan, data.member.id, true, true)
            ).trigger('change');
            document.getElementById('ar_edit_email').value = data.member.email_pelanggan ?? '';
            document.getElementById('ar_edit_kontak').value = data.member.kontak_pelanggan ?? '';
            document.getElementById('ar_edit_jatuh_tempo').value = data.jatuh_tempo.substring(0, 10);
            document.getElementById('ar_edit_total').value = data.total;
            document.getElementById('formEditAr').action = '/admin/aging_ar/' + data.id;
            document.getElementById('modalEditAr').classList.remove('hidden');
        }

        function closeEditModalAr() {
            document.getElementById('modalEditAr').classList.add('hidden');
        }
        document.getElementById('modalTambahAr').addEventListener('click', function(e) {
            if (e.target === this) closeModalAr();
        });
        document.getElementById('modalEditAr').addEventListener('click', function(e) {
            if (e.target === this) closeEditModalAr();
        });

        // -- ESCAPE KEY -----------------------------------------
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalKeuangan();
                closeModalAp();
                closeEditModalAp();
                closeModalAr();
                closeEditModalAr();
            }
        });

        // -- SEARCH FILTER --------------------------------------
        function filterTableAp(q) {
            document.querySelectorAll('#tableBodyAp tr[data-search]').forEach(row => {
                row.style.display = row.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
            });
        }

        function filterTableAr(q) {
            document.querySelectorAll('#tableBodyAr tr[data-search]').forEach(row => {
                row.style.display = row.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
            });
        }

        // -- POPUP ALERT ----------------------------------------
        (function() {
            var overlay = document.getElementById('alertOverlay');
            var box = document.getElementById('alertBox');
            if (!overlay) return;
            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);
            var timer = setTimeout(closeAlert, 4500);
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeAlert();
            });

            function closeAlert() {
                clearTimeout(timer);
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                box.style.transform = 'translateY(-16px)';
            }
            window.closeAlert = closeAlert;
        })();

        // -- SELECT2 AGING AR -----------------------------------
        $(document).ready(function() {

            // Member - Modal Tambah
            $('#ar_member_id').select2({
                dropdownParent: $('#modalTambahAr'),
                placeholder: 'Cari customer...',
                ajax: {
                    url: '/admin/ajax/members',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name,
                            email: item.email,
                            kontak: item.kontak
                        }))
                    }),
                    cache: true
                }
            });
            $('#ar_member_id').on('select2:select', function(e) {
                document.getElementById('ar_add_email').value = e.params.data.email ?? '';
                document.getElementById('ar_add_kontak').value = e.params.data.kontak ?? '';
            });

            // Invoice - Modal Tambah
            $('#ar_invoice_id').select2({
                dropdownParent: $('#modalTambahAr'),
                placeholder: 'Cari invoice...',
                ajax: {
                    url: '/admin/ajax/invoices',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: item.no_invoice
                        }))
                    }),
                    cache: true
                }
            });

            // Member - Modal Edit
            $('#ar_edit_member_id').select2({
                dropdownParent: $('#modalEditAr'),
                placeholder: 'Cari customer...',
                ajax: {
                    url: '/admin/ajax/members',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name,
                            email: item.email,
                            kontak: item.kontak
                        }))
                    }),
                    cache: true
                }
            });
            $('#ar_edit_member_id').on('select2:select', function(e) {
                document.getElementById('ar_edit_email').value = e.params.data.email ?? '';
                document.getElementById('ar_edit_kontak').value = e.params.data.kontak ?? '';
            });

            // Invoice - Modal Edit
            $('#ar_edit_invoice_id').select2({
                dropdownParent: $('#modalEditAr'),
                placeholder: 'Cari invoice...',
                ajax: {
                    url: '/admin/ajax/invoices',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: item.no_invoice
                        }))
                    }),
                    cache: true
                }
            });
        });
    </script>

@endsection

