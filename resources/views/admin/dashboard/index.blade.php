@extends('admin.layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* ── Welcome Banner ── */
        .welcome-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 45%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -40px;
            right: 80px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
        }

        .welcome-dots {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* ── KPI Cards ── */
        .kpi-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 1.1rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            position: relative;
            transition: transform 200ms ease, box-shadow 200ms ease, border-color 200ms ease;
            cursor: pointer;
            text-decoration: none;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px -8px rgba(0, 0, 0, 0.12);
        }

        .kpi-card .kpi-arrow {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            opacity: 0;
            transform: translateX(-4px);
            transition: opacity 200ms ease, transform 200ms ease;
        }

        .kpi-card:hover .kpi-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
            transition: transform 200ms ease;
        }

        .kpi-card:hover .kpi-icon {
            transform: scale(1.1) rotate(-3deg);
        }

        /* ── Finance Cards ── */
        .finance-card {
            border-radius: 14px;
            padding: 1.2rem 1.3rem;
            position: relative;
            overflow: hidden;
            transition: transform 200ms ease, box-shadow 200ms ease;
        }

        .finance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -8px rgba(0, 0, 0, 0.15);
        }

        .finance-card::after {
            content: '';
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        /* ── Reminder Cards ── */
        .reminder-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #f1f5f9;
            margin-bottom: 8px;
            transition: all 200ms ease;
            cursor: default;
        }

        .reminder-item:hover {
            background: #f8faff;
            border-color: #dbeafe;
            transform: translateX(3px);
            box-shadow: 0 2px 12px -4px rgba(59, 130, 246, 0.12);
        }

        .reminder-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── Rental rows ── */
        .rental-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            border-bottom: 1px solid #f8fafc;
            transition: all 180ms ease;
        }

        .rental-row:last-child {
            border-bottom: none;
        }

        .rental-row:hover {
            background: linear-gradient(90deg, #eff6ff, #f8faff);
            padding-left: 24px;
        }

        /* ── Section headers ── */
        .section-head {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .section-head-dot {
            width: 4px;
            height: 18px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* ── Stagger animation ── */
        .fade-up {
            opacity: 0;
            transform: translateY(14px);
            animation: fadeUp 400ms ease forwards;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up:nth-child(1) {
            animation-delay: 60ms;
        }

        .fade-up:nth-child(2) {
            animation-delay: 120ms;
        }

        .fade-up:nth-child(3) {
            animation-delay: 180ms;
        }

        .fade-up:nth-child(4) {
            animation-delay: 240ms;
        }

        .fade-up:nth-child(5) {
            animation-delay: 300ms;
        }

        .fade-up:nth-child(6) {
            animation-delay: 360ms;
        }

        .fade-up:nth-child(7) {
            animation-delay: 420ms;
        }

        .fade-up:nth-child(8) {
            animation-delay: 480ms;
        }

        /* ── Chart container ── */
        .chart-wrapper {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 1.4rem 1.5rem;
            transition: box-shadow 200ms ease;
        }

        .chart-wrapper:hover {
            box-shadow: 0 6px 24px -6px rgba(0, 0, 0, 0.08);
        }
    </style>
@endpush

@section('content')

    <div class="space-y-5">

        {{-- ══════════════════════════════════
        WELCOME BANNER
    ══════════════════════════════════ --}}
        <div class="welcome-banner rounded-2xl p-5 sm:p-6 fade-up">
            <div class="welcome-dots"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">

                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">
                        Selamat datang kembali, <span class="text-blue-200">{{ auth()->user()->name ?? 'Admin' }}</span> 👋
                    </h1>
                    <p class="text-blue-200/80 text-[13px] mt-1.5 max-w-md leading-relaxed">
                        Berikut ringkasan terkini platform pengelolaan rental kendaraan Anda. Pantau performa dan kelola
                        operasional APYRENT A CAR dengan mudah.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right hidden sm:block">
                        <p class="text-blue-200/70 text-[11px] uppercase tracking-widest font-medium">Hari ini</p>
                        <p class="text-white text-[15px] font-bold" id="welcomeDate">—</p>
                        <p class="text-blue-200/70 text-[11px]" id="welcomeTime">—</p>
                    </div>

                </div>
            </div>
        </div>



        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <form method="GET" action="{{ route('dashboard') }}">
                <div class="flex flex-wrap gap-2">

                    <select name="hari" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Hari</option>
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ $d }}" {{ request('hari') == $d ? 'selected' : '' }}>
                                {{ $d }}
                            </option>
                        @endfor
                    </select>

                    <select name="bulan" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Bulan</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="tahun" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        Filter
                    </button>

                    <a href="{{ route('dashboard') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">
                        Reset
                    </a>

                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════
        KPI ROW 1
    ══════════════════════════════════ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            @if(auth()->user()->role === 'admin')
            <a href="/admin/kendaraan" class="kpi-card fade-up" style="--hover-color:#3b82f6">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-blue-50">
                    <i class="fa fa-car text-blue-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">Kendaraan</p>
                    <h2 class="text-[22px] font-bold text-gray-800 leading-none">{{ $totalKendaraan ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-blue-50 text-blue-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="/admin/rental" class="kpi-card fade-up">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-violet-50">
                    <i class="fa fa-key text-violet-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">Disewa</p>
                    <h2 class="text-[22px] font-bold text-violet-600 leading-none">{{ $kendaraanDisewa ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-violet-50 text-violet-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="/admin/user" class="kpi-card fade-up">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-emerald-50">
                    <i class="fa fa-users text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">User</p>
                    <h2 class="text-[22px] font-bold text-gray-800 leading-none">{{ $totalUser ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-emerald-50 text-emerald-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="/admin/history" class="kpi-card fade-up">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-amber-50">
                    <i class="fa fa-file-invoice text-amber-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">Total Rental</p>
                    <h2 class="text-[22px] font-bold text-amber-600 leading-none">{{ $totalRental ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-amber-50 text-amber-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

        </div>


        {{-- ══════════════════════════════════
        KPI ROW 2
    ══════════════════════════════════ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            @if(auth()->user()->role === 'admin')
            <a href="/admin/gps-kendaraan" class="kpi-card fade-up">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-sky-50">
                    <i class="fa fa-map-marker-alt text-sky-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">GPS Aktif</p>
                    <h2 class="text-[22px] font-bold text-sky-600 leading-none">{{ $gpsAktif ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-sky-50 text-sky-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="/admin/asuransi" class="kpi-card fade-up">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-teal-50">
                    <i class="fa fa-shield-alt text-teal-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">Asuransi</p>
                    <h2 class="text-[22px] font-bold text-teal-600 leading-none">{{ $totalAsuransi ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-teal-50 text-teal-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="/admin/kir" class="kpi-card fade-up">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-yellow-50">
                    <i class="fa fa-clipboard-check text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">KIR</p>
                    <h2 class="text-[22px] font-bold text-yellow-600 leading-none">{{ $totalKir ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-yellow-50 text-yellow-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="/admin/pajak" class="kpi-card fade-up">
            @else
            <div class="kpi-card fade-up" style="cursor:default">
            @endif
                <div class="kpi-icon bg-rose-50">
                    <i class="fa fa-receipt text-rose-500"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest leading-none mb-1">Pajak</p>
                    <h2 class="text-[22px] font-bold text-rose-600 leading-none">{{ $totalPajak ?? 0 }}</h2>
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="kpi-arrow bg-rose-50 text-rose-500"><i class="bi bi-arrow-up-right"></i></div>
                @endif
            @if(auth()->user()->role === 'admin')
            </a>
            @else
            </div>
            @endif

        </div>


        {{-- ══════════════════════════════════
        KEUANGAN BULAN INI
    ══════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 fade-up">

            <div class="finance-card" style="background: linear-gradient(135deg,#4f46e5,#6366f1)">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                            <i class="bi bi-arrow-down-circle-fill text-white text-sm"></i>
                        </div>
                        <p class="text-[11px] text-indigo-200 font-semibold uppercase tracking-widest">Pemasukan</p>
                    </div>
                    <h2 class="text-xl font-bold text-white">Rp {{ number_format($pemasukanBulanIni ?? 0) }}</h2>
                    <p class="text-indigo-300/70 text-[11px] mt-1">Bulan berjalan</p>
                </div>
            </div>

            <div class="finance-card" style="background: linear-gradient(135deg,#e11d48,#f43f5e)">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                            <i class="bi bi-arrow-up-circle-fill text-white text-sm"></i>
                        </div>
                        <p class="text-[11px] text-rose-200 font-semibold uppercase tracking-widest">Pengeluaran</p>
                    </div>
                    <h2 class="text-xl font-bold text-white">Rp {{ number_format($pengeluaranBulanIni ?? 0) }}</h2>
                    <p class="text-rose-300/70 text-[11px] mt-1">Bulan berjalan</p>
                </div>
            </div>

            <div class="finance-card" style="background: linear-gradient(135deg,#059669,#10b981)">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                            <i class="bi bi-graph-up-arrow text-white text-sm"></i>
                        </div>
                        <p class="text-[11px] text-emerald-200 font-semibold uppercase tracking-widest">Profit Bersih</p>
                    </div>
                    <h2 class="text-xl font-bold text-white">Rp {{ number_format($profitBulanIni ?? 0) }}</h2>
                    <p class="text-emerald-300/70 text-[11px] mt-1">Bulan berjalan</p>
                </div>
            </div>

        </div>


        {{-- ══════════════════════════════════
        CHART
    ══════════════════════════════════ --}}
        <div class="chart-wrapper fade-up">
            <div class="flex items-center justify-between mb-4">
                <div class="section-head mb-0">
                    <div class="section-head-dot bg-blue-500"></div>
                    <div>
                        <h2 class="text-[14px] font-bold text-gray-800 leading-none">Grafik Pemasukan</h2>
                        <p class="text-[11px] text-gray-400 mt-0.5">12 bulan terakhir</p>
                    </div>
                </div>
                <span
                    class="inline-flex items-center gap-1.5 text-[11px] text-blue-600 font-semibold bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100">
                    <i class="bi bi-bar-chart-fill text-[10px]"></i> Revenue
                </span>
            </div>
            <div style="height:280px;">
                <canvas id="chartPemasukan"></canvas>
            </div>
        </div>


        {{-- ══════════════════════════════════
        REMINDER SECTION
    ══════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- PAJAK --}}
            <div
                class="bg-white border border-gray-100 rounded-2xl p-5 fade-up hover:shadow-md transition-shadow duration-200">
                <div class="section-head">
                    <div class="section-head-dot bg-rose-500"></div>
                    <div class="flex-1">
                        <h2 class="text-[13px] font-bold text-gray-800">Pajak Hampir Habis</h2>
                        <p class="text-[11px] text-gray-400">Perlu segera diperbarui</p>
                    </div>
                    <span class="w-7 h-7 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 text-xs">
                        <i class="fa fa-receipt"></i>
                    </span>
                </div>
                @forelse($pajakHampirHabis ?? [] as $p)
                    <div class="reminder-item">
                        <div class="reminder-dot bg-rose-400"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $p->kendaraan->merk ?? '-' }}
                            </p>
                            <p class="text-[11px] text-gray-400">{{ $p->kendaraan->nopol ?? '-' }}</p>
                        </div>
                        <i class="bi bi-chevron-right text-gray-300 text-[11px]"></i>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-5 text-center gap-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-300 text-base">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <p class="text-[12px] text-gray-400">Semua pajak masih berlaku</p>
                    </div>
                @endforelse
                @if(auth()->user()->role === 'admin' && ($pajakHasMore ?? false))
                    <a href="/admin/pajak"
                        class="mt-3 flex items-center justify-center gap-1.5 text-[11px] font-semibold text-rose-500 hover:text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg py-2 transition-colors duration-200">
                        <i class="bi bi-arrow-right-circle text-[12px]"></i>
                        Lihat Lainnya
                    </a>
                @endif
            </div>

            {{-- ASURANSI --}}
            <div
                class="bg-white border border-gray-100 rounded-2xl p-5 fade-up hover:shadow-md transition-shadow duration-200">
                <div class="section-head">
                    <div class="section-head-dot bg-teal-500"></div>
                    <div class="flex-1">
                        <h2 class="text-[13px] font-bold text-gray-800">Asuransi Hampir Habis</h2>
                        <p class="text-[11px] text-gray-400">Perlu segera diperbarui</p>
                    </div>
                    <span class="w-7 h-7 rounded-xl bg-teal-50 flex items-center justify-center text-teal-500 text-xs">
                        <i class="fa fa-shield-alt"></i>
                    </span>
                </div>
                @forelse($asuransiHampirHabis ?? [] as $a)
                    <div class="reminder-item">
                        <div class="reminder-dot bg-teal-400"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $a->kendaraan->merk ?? '-' }}
                            </p>
                            <p class="text-[11px] text-gray-400">{{ $a->kendaraan->nopol ?? '-' }}</p>
                        </div>
                        <i class="bi bi-chevron-right text-gray-300 text-[11px]"></i>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-5 text-center gap-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-300 text-base">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <p class="text-[12px] text-gray-400">Semua asuransi masih berlaku</p>
                    </div>
                @endforelse
                @if(auth()->user()->role === 'admin' && ($asuransiHasMore ?? false))
                    <a href="/admin/asuransi"
                        class="mt-3 flex items-center justify-center gap-1.5 text-[11px] font-semibold text-teal-500 hover:text-teal-600 bg-teal-50 hover:bg-teal-100 rounded-lg py-2 transition-colors duration-200">
                        <i class="bi bi-arrow-right-circle text-[12px]"></i>
                        Lihat Lainnya
                    </a>
                @endif
            </div>

            {{-- KIR --}}
            <div
                class="bg-white border border-gray-100 rounded-2xl p-5 fade-up hover:shadow-md transition-shadow duration-200">
                <div class="section-head">
                    <div class="section-head-dot bg-amber-500"></div>
                    <div class="flex-1">
                        <h2 class="text-[13px] font-bold text-gray-800">KIR Hampir Habis</h2>
                        <p class="text-[11px] text-gray-400">Perlu segera diperbarui</p>
                    </div>
                    <span class="w-7 h-7 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 text-xs">
                        <i class="fa fa-clipboard-check"></i>
                    </span>
                </div>
                @forelse($kirHampirHabis ?? [] as $k)
                    <div class="reminder-item">
                        <div class="reminder-dot bg-amber-400"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $k->kendaraan->merk ?? '-' }}
                            </p>
                            <p class="text-[11px] text-gray-400">{{ $k->kendaraan->nopol ?? '-' }}</p>
                        </div>
                        <i class="bi bi-chevron-right text-gray-300 text-[11px]"></i>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-5 text-center gap-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-300 text-base">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <p class="text-[12px] text-gray-400">Semua KIR masih berlaku</p>
                    </div>
                @endforelse
                @if(auth()->user()->role === 'admin' && ($kirHasMore ?? false))
                    <a href="/admin/kir"
                        class="mt-3 flex items-center justify-center gap-1.5 text-[11px] font-semibold text-amber-500 hover:text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg py-2 transition-colors duration-200">
                        <i class="bi bi-arrow-right-circle text-[12px]"></i>
                        Lihat Lainnya
                    </a>
                @endif
            </div>

           

            {{-- GPS --}}
            <div
                class="bg-white border border-gray-100 rounded-2xl p-5 fade-up hover:shadow-md transition-shadow duration-200">
                <div class="section-head">
                    <div class="section-head-dot bg-sky-500"></div>
                    <div class="flex-1">
                        <h2 class="text-[13px] font-bold text-gray-800">GPS Hampir Habis</h2>
                        <p class="text-[11px] text-gray-400">Masa sewa akan berakhir</p>
                    </div>
                    <span class="w-7 h-7 rounded-xl bg-sky-50 flex items-center justify-center text-sky-500 text-xs">
                        <i class="fa fa-map-marker-alt"></i>
                    </span>
                </div>
                @forelse($gpsHampirHabis ?? [] as $g)
                    <div class="reminder-item">
                        <div class="reminder-dot bg-sky-400"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $g->kendaraan->merk ?? '-' }}
                            </p>
                            <p class="text-[11px] text-gray-400">{{ $g->kendaraan->nopol ?? '-' }}</p>
                        </div>
                        <i class="bi bi-chevron-right text-gray-300 text-[11px]"></i>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-5 text-center gap-2">
                        <div
                            class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-300 text-base">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <p class="text-[12px] text-gray-400">Semua GPS masih aktif</p>
                    </div>
                @endforelse
                @if(auth()->user()->role === 'admin' && ($gpsHasMore ?? false))
                    <a href="/admin/gps-kendaraan"
                        class="mt-3 flex items-center justify-center gap-1.5 text-[11px] font-semibold text-sky-500 hover:text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg py-2 transition-colors duration-200">
                        <i class="bi bi-arrow-right-circle text-[12px]"></i>
                        Lihat Lainnya
                    </a>
                @endif
            </div>

        </div>


        


    {{-- ══ CHART JS ══ --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ── Welcome date/time ──
        (function() {
            var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            var d = new Date();
            var dateEl = document.getElementById('welcomeDate');
            var timeEl = document.getElementById('welcomeTime');
            if (dateEl) dateEl.textContent = days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' +
                d.getFullYear();

            function tick() {
                var n = new Date();
                var h = String(n.getHours()).padStart(2, '0');
                var m = String(n.getMinutes()).padStart(2, '0');
                var s = String(n.getSeconds()).padStart(2, '0');
                if (timeEl) timeEl.textContent = h + ':' + m + ':' + s + ' WIB';
                requestAnimationFrame(function() {
                    setTimeout(tick, 1000);
                });
            }
            tick();
        })();

        // ── Chart ──
        new Chart(document.getElementById('chartPemasukan'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Pemasukan',
                    data: @json($chartPemasukan ?? []),
                    borderColor: '#4f6ef7',
                    backgroundColor: function(ctx) {
                        var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                        g.addColorStop(0, 'rgba(79,110,247,0.18)');
                        g.addColorStop(1, 'rgba(79,110,247,0.01)');
                        return g;
                    },
                    borderWidth: 2.5,
                    tension: 0.42,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f6ef7',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#4f6ef7',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f1117',
                        titleColor: '#9aa0bb',
                        bodyColor: '#fff',
                        borderColor: '#1f2333',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: 'Plus Jakarta Sans'
                            },
                            color: '#9aa0bb'
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: '#f8fafc',
                            lineWidth: 1.5
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: 'Plus Jakarta Sans'
                            },
                            color: '#9aa0bb',
                            callback: function(v) {
                                if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(0) + 'jt';
                                if (v >= 1000) return 'Rp ' + (v / 1000).toFixed(0) + 'rb';
                                return 'Rp ' + v;
                            }
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>


    {{-- ══ POPUP ALERT ══ --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay"
            style="position:fixed;inset:0;z-index:9999;display:flex;align-items:flex-start;justify-content:center;padding-top:1.5rem;background:rgba(0,0,0,0.12);opacity:0;transition:opacity 0.2s;pointer-events:none;">
            <div id="alertBox"
                style="background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:1rem 1.25rem;min-width:320px;max-width:440px;display:flex;align-items:flex-start;gap:12px;box-shadow:0 12px 40px rgba(0,0,0,0.12);transform:translateY(-16px);transition:transform 0.25s;">

                @if (session('success'))
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:#ecfdf5;color:#059669;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div style="flex:1">
                        <p style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:3px;">Berhasil!</p>
                        <p style="font-size:12px;color:#64748b;line-height:1.6;">{{ session('success') }}</p>
                    </div>
                @else
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:#fff1f2;color:#e11d48;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div style="flex:1">
                        <p style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:3px;">Terjadi Kesalahan!</p>
                        <ul style="font-size:12px;color:#64748b;line-height:1.7;padding-left:1rem;list-style:disc;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button onclick="closeAlert()"
                    style="background:none;border:none;cursor:pointer;font-size:16px;color:#cbd5e1;padding:2px;line-height:1;border-radius:6px;transition:color 0.15s;"
                    onmouseenter="this.style.color='#64748b'" onmouseleave="this.style.color='#cbd5e1'"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        <script>
            (function() {
                var overlay = document.getElementById('alertOverlay');
                var box = document.getElementById('alertBox');
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
        </script>
    @endif

@endsection
