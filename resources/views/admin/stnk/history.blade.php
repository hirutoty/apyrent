@extends('admin.layouts.app')

@section('title', 'History STNK Kendaraan')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @php
        $totalHistory = $data->count();
        $totalBiaya   = $data->sum('biaya');
        $tanpaBukti   = $data->filter(fn($item) => empty($item->bukti))->count();
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">History STNK Kendaraan</h1>
                <p class="text-sm text-slate-500 mt-1">Riwayat seluruh perpanjangan STNK kendaraan armada</p>
            </div>
            <a href="{{ route('stnk.index') }}"
                class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke STNK
            </a>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

            {{-- Total Perpanjangan --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Perpanjangan</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $totalHistory }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Biaya --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Biaya Perpanjangan</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-2">Rp
                            {{ number_format($totalBiaya, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Tanpa Bukti --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Tanpa Bukti Bayar</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $tanpaBukti }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-exclamation text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- TOOLBAR --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800 text-base">Riwayat Perpanjangan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $totalHistory }} total data</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">

                    {{-- FILTER BULAN --}}
                    <form method="GET" class="flex items-center gap-2">
                        <input type="month" name="bulan" value="{{ request('bulan') }}"
                            class="text-xs border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">

                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                            <i class="fa fa-filter text-xs"></i> Filter
                        </button>

                        @if (request('bulan'))
                            <a href="{{ route('stnk-history.index') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-slate-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fa fa-rotate-left text-xs"></i> Reset
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('stnk-history.export.pdf', ['bulan' => request('bulan')]) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        <i class="fa fa-file-pdf"></i>
                        Export PDF
                    </a>

                    {{-- SEARCH --}}
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="search" placeholder="Cari kendaraan, nopol, pemilik..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 w-56">
                    </div>

                </div>
            </div>

            {{-- Info filter aktif --}}
            @if (request('bulan'))
                <div class="px-5 pt-4">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-full">
                        <i class="fa fa-calendar text-[10px]"></i>
                        Menampilkan bulan:
                        {{ \Carbon\Carbon::createFromFormat('Y-m', request('bulan'))->translatedFormat('F Y') }}
                    </span>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Kendaraan</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Pemilik</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Model</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Biaya</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Masa Berlaku Lama</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Diperpanjang Pada</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Bukti</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100">

                        @forelse ($data as $item)
                            <tr class="hover:bg-slate-50 transition">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-800">
                                        {{ $item->nopol ?? ($item->kendaraan->nopol ?? '-') }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $item->merk ?? ($item->kendaraan->merk ?? '-') }}
                                    </div>
                                </td>

                                {{-- Pemilik --}}
                                <td class="px-5 py-4 text-slate-700">{{ $item->nama_pemilik ?? '-' }}</td>

                                {{-- Model --}}
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $item->jenis_model ?? '-' }}
                                    </span>
                                </td>

                                {{-- Biaya --}}
                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                </td>

                                {{-- Masa Berlaku Lama --}}
                                <td class="px-5 py-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->masa_berlaku)->format('d M Y') }}
                                </td>

                                {{-- Diperpanjang Pada --}}
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-rotate-right text-[10px]"></i>
                                        {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                </td>

                                {{-- Bukti --}}
                                <td>
                                    @if ($item->bukti)
                                        @php
                                            $filename = basename($item->bukti);
                                        @endphp

                                        <a href="{{ asset($item->bukti) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center">
                                        <form action="{{ route('stnk-history.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data history ini? Tindakan ini tidak bisa dibatalkan.')">
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
                                <td colspan="9" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-clock-rotate-left text-4xl mb-3 block"></i>
                                    @if (request('bulan'))
                                        Tidak ada history perpanjangan STNK pada bulan ini.
                                    @else
                                        Belum ada history perpanjangan STNK.
                                    @endif
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

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
                        <p class="text-sm font-bold text-gray-800">Gagal!</p>
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
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>

            </div>
        </div>
    @endif

    <script>
        // ── SEARCH (client-side) ────────────────────────────
        const search = document.getElementById('search');

        search.addEventListener('keyup', function() {
            let value = this.value.toLowerCase();

            document.querySelectorAll('#tableBody tr').forEach(function(row) {
                row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        });

        // ── POPUP ALERT ──────────────────────────────────────
        (function() {
            var overlay = document.getElementById('alertOverlay');
            var box     = document.getElementById('alertBox');
            if (!overlay) return;

            setTimeout(function() {
                overlay.style.opacity      = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform        = 'translateY(0)';
            }, 80);

            var timer = setTimeout(closeAlert, 4500);

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeAlert();
            });

            function closeAlert() {
                clearTimeout(timer);
                overlay.style.opacity      = '0';
                overlay.style.pointerEvents = 'none';
                box.style.transform        = 'translateY(-16px)';
            }
            window.closeAlert = closeAlert;
        })();
    </script>

@endsection