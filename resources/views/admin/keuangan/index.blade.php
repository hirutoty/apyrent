@extends('admin.layouts.app')

@section('title', 'Data Keuangan')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Keuangan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola pemasukan dan pengeluaran</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150 mt-2 sm:mt-0">
                <i class="fa fa-plus text-sm"></i>
                Tambah Transaksi
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-hand-holding-usd text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Pemasukan</p>
                    <p class="text-lg font-bold text-green-600">
                        Rp {{ number_format($totalPemasukan) }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-money-bill-wave text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Pengeluaran</p>
                    <p class="text-lg font-bold text-red-600">
                        Rp {{ number_format($totalPengeluaran) }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-wallet text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Saldo</p>
                    <p class="text-lg font-bold text-blue-600">
                        Rp {{ number_format($saldo) }}
                    </p>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TOOLBAR --}}
            <div class="px-5 py-4 border-b border-gray-100 space-y-3">

                {{-- Baris 1: Judul + Count --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                    {{-- Judul --}}
                    <div>
                        <h2 class="font-semibold text-gray-800 text-base">
                            Daftar Transaksi
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5" id="totalCount">
                            {{ $keuangans->count() }} total transaksi
                        </p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex flex-wrap items-center gap-2">

                        {{-- Export PDF --}}
                        <a href="{{ route('keuangan.export.pdf', request()->query()) }}" target="_blank"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm">

                            <i class="fa fa-file-pdf text-sm"></i>
                            Export PDF
                        </a>

                        {{-- Export Excel --}}
                        <a href="{{ route('keuangan.export.excel', request()->query()) }}"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm">

                            <i class="fa fa-file-excel text-sm"></i>
                            Export Excel
                        </a>

                    </div>

                </div>

                {{-- Baris 2: Filter Tanggal + Search --}}
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('keuangan.index') }}">
                        <div class="flex flex-wrap items-center gap-2">


                            {{-- Filter Hari --}}
                            <div class="relative">
                                <i
                                    class="fa fa-calendar-day absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <select name="hari" onchange="this.form.submit()"
                                    class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 appearance-none bg-white cursor-pointer">

                                    <option value="">Semua Hari</option>

                                    @for ($d = 1; $d <= 31; $d++)
                                        <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}"
                                            {{ request('hari') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                            {{ str_pad($d, 2, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endfor

                                </select>
                            </div>

                            {{-- Filter Bulan --}}
                            <div class="relative">
                                <i
                                    class="fa fa-calendar-alt absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <select name="bulan" onchange="this.form.submit()"
                                    class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 appearance-none bg-white cursor-pointer">
                                    <option value="">Semua Bulan</option>
                                    <option value="01" {{ request('bulan') == '01' ? 'selected' : '' }}>Januari
                                    </option>
                                    <option value="02" {{ request('bulan') == '02' ? 'selected' : '' }}>Februari
                                    </option>
                                    <option value="03" {{ request('bulan') == '03' ? 'selected' : '' }}>Maret</option>
                                    <option value="04" {{ request('bulan') == '04' ? 'selected' : '' }}>April</option>
                                    <option value="05" {{ request('bulan') == '05' ? 'selected' : '' }}>Mei</option>
                                    <option value="06" {{ request('bulan') == '06' ? 'selected' : '' }}>Juni</option>
                                    <option value="07" {{ request('bulan') == '07' ? 'selected' : '' }}>Juli</option>
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

                            {{-- Filter Tahun --}}
                            <div class="relative">
                                <i
                                    class="fa fa-calendar absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <select name="tahun" onchange="this.form.submit()"
                                    class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 appearance-none bg-white cursor-pointer">

                                    <option value="">Semua Tahun</option>

                                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}"
                                            {{ request('tahun') == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor

                                </select>
                            </div>

                            <div class="relative">
                                <i
                                    class="fa fa-filter absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

                                <select name="jenis" onchange="this.form.submit()"
                                    class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 appearance-none bg-white cursor-pointer">

                                    <option value="">Semua Transaksi</option>

                                    <option value="Pemasukan" {{ request('jenis') == 'Pemasukan' ? 'selected' : '' }}>
                                        Pemasukan
                                    </option>

                                    <option value="Pengeluaran" {{ request('jenis') == 'Pengeluaran' ? 'selected' : '' }}>
                                        Pengeluaran
                                    </option>

                                </select>
                            </div>

                            {{-- Search --}}
                            <div class="relative flex-1 min-w-[160px]">
                                <i
                                    class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari kategori..." onkeydown="if(event.key=='Enter') this.form.submit();"
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md
               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
               transition">
                            </div>

                            {{-- Reset --}}
                            <a href="{{ route('keuangan.index') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <i class="fa fa-times-circle"></i>
                                Reset
                            </a>
                        </div>
                    </form>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Reference</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                User
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kategori</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Keterangan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Pemasukan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Pengeluaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="keuanganTableBody">
                        @forelse($keuangans as $i => $k)
                            {{-- data-tanggal format: YYYY-MM-DD atau sesuaikan dengan format $k->tanggal --}}
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($k->kategori . ' ' . $k->keterangan . ' ' . ($k->reference ?? '') . ' ' . ($k->user->name ?? '')) }}"
                                data-tanggal="{{ $k->tanggal }}"
                                data-jenis="{{ $k->pemasukan > 0 ? 'Pemasukan' : 'Pengeluaran' }}">

                                {{-- NO --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium row-number">
                                    {{ $keuangans->firstItem() + $i }}
                                </td>

                                {{-- TANGGAL --}}
                                <td class="px-4 py-3.5 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($k->tanggal)->format('d-m-Y') }}</td>

                                {{-- REFERENCE --}}
                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                                        {{ $k->reference ?? '-' }}
                                    </span>
                                </td>

                                {{-- USER --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($k->user->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $k->user->name ?? '-' }}</span>
                                    </div>
                                </td>

                                {{-- KATEGORI --}}
                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $k->kategori }}</td>

                                {{-- KETERANGAN --}}
                                <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[180px] truncate">
                                    {{ $k->keterangan }}
                                </td>

                                {{-- PEMASUKAN --}}
                                <td class="px-4 py-3.5">
                                    @if ($k->pemasukan > 0)
                                        <span class="text-sm font-semibold text-green-600">
                                            Rp {{ number_format($k->pemasukan) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- PENGELUARAN --}}
                                <td class="px-4 py-3.5">
                                    @if ($k->pengeluaran > 0)
                                        <span class="text-sm font-semibold text-red-500">
                                            Rp {{ number_format($k->pengeluaran) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- SALDO --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-bold text-blue-600">
                                        Rp {{ number_format($k->saldo) }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa fa-wallet text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data transaksi</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Pengeluaran" untuk menambahkan
                                            transaksi baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $keuangans->links() }}
                </div>

                {{-- Empty state saat filter tidak cocok --}}
                <div id="noResultRow" class="hidden px-5 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fa fa-search text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Tidak ada hasil yang cocok</p>
                        <p class="text-xs text-gray-400">Coba ubah filter atau kata kunci pencarian</p>
                    </div>
                </div>



            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH PENGELUARAN
====================================== --}}
    <div id="modalPengeluaran" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Transaksi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data transaksi keuangan</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('keuangan.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Jenis Transaksi
                    </label>

                    <select name="jenis" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span
                            class="text-blue-500">*</span></label>
                    <input type="text" name="kategori" required placeholder="Contoh: BBM, Servis, dll"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Metode <span
                            class="text-blue-500">*</span></label>
                    <input type="text" name="metode" required placeholder="Cash / Transfer"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal Transaksi <span
                            class="text-blue-500">*</span></label>
                    <input type="number" name="nominal" required placeholder="Contoh: 150000"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span
                            class="text-blue-500">*</span></label>
                    <input type="text" name="keterangan" required placeholder="Keterangan singkat transaksi"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan Tranksaksi
                </button>
            </form>

        </div>
    </div>


    {{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
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
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>

            </div>
        </div>
    @endif


    {{-- STYLE & SCRIPT --}}
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
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239ca3af'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 24px !important;
        }
    </style>

    <script>
        // ── MODAL PENGELUARAN ──────────────────────────────
        const modalPengeluaran = document.getElementById('modalPengeluaran');

        function openModal() {
            modalPengeluaran.classList.remove('hidden');
            modalPengeluaran.classList.add('flex');
        }

        function closeModal() {
            modalPengeluaran.classList.add('hidden');
            modalPengeluaran.classList.remove('flex');
        }

        modalPengeluaran.addEventListener('click', function(e) {
            if (e.target === modalPengeluaran) closeModal();
        });

        // ── FILTER & SEARCH ────────────────────────────────
        // Format data-tanggal diasumsikan: YYYY-MM-DD
        // Sesuaikan jika format berbeda (misal: DD-MM-YYYY, dsb.)



        // ── POPUP ALERT ────────────────────────────────────
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
    </script>

@endsection
