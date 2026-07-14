@extends('admin.layouts.app')

@section('title', 'Hutang Vendor')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Hutang Vendor</h1>
                <p class="text-sm text-gray-500 mt-0.5">Data hutang supplier & vendor</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150 mt-2 sm:mt-0">
                <i class="fa fa-plus text-sm"></i>
                Tambah Data
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-money-bill-wave text-red-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Hutang</p>
                    <p class="text-sm font-bold text-red-600 leading-tight">Rp
                        {{ number_format($data->sum('nominal'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Hutang</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-hand-holding-usd text-green-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Dibayar</p>
                    <p class="text-sm font-bold text-green-600 leading-tight">Rp
                        {{ number_format($data->sum('dibayar'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Dibayar</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-exclamation-circle text-orange-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Sisa</p>
                    <p class="text-sm font-bold text-orange-500 leading-tight">Rp
                        {{ number_format($data->sum('sisa'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Sisa Hutang</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-times-circle text-red-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Belum</p>
                    <p class="text-2xl font-bold text-red-600 leading-tight">
                        {{ $data->where('status', 'belum_lunas')->count() }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Belum Lunas</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-check-circle text-green-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Lunas</p>
                    <p class="text-2xl font-bold text-green-600 leading-tight">
                        {{ $data->where('status', 'lunas')->count() }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Sudah Lunas</p>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TOOLBAR --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Hutang Vendor</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $data->count() }} total data</p>
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i
                            class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" id="searchInput" placeholder="Cari vendor, kategori..."
                            oninput="applyFilters()"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-48">
                    </div>
                    {{-- Filter Status --}}
                    <div class="relative">
                        <i
                            class="fa fa-filter absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filterStatus" onchange="applyFilters()"
                            class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 appearance-none bg-white cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="belum_lunas">Belum Lunas</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                    <a id="pdfBtn" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors"
                        href="#">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>

                    {{-- TAMBAHKAN INI --}}
                    <a id="excelBtn"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors"
                        href="#">
                        <i class="fa fa-file-excel"></i> Excel
                    </a>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Vendor</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kategori</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Nominal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Dibayar</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jatuh Tempo</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="hutangTableBody">
                        @forelse($data as $i => $item)
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100"
                                data-search="{{ strtolower($item->nama_vendor . ' ' . $item->kategori . ' ' . ($item->keterangan ?? '')) }}"
                                data-status="{{ $item->status }}">

                                {{-- NO --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium row-number">{{ $i + 1 }}
                                </td>

                                {{-- VENDOR --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($item->nama_vendor, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $item->nama_vendor }}</span>
                                    </div>
                                </td>

                                {{-- KATEGORI --}}
                                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $item->kategori }}</td>

                                {{-- NOMINAL --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-semibold text-red-500">Rp
                                        {{ number_format($item->nominal) }}</span>
                                </td>

                                {{-- DIBAYAR --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-semibold text-green-600">Rp
                                        {{ number_format($item->dibayar) }}</span>
                                </td>

                                {{-- SISA --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-semibold text-orange-500">Rp
                                        {{ number_format($item->sisa) }}</span>
                                </td>

                                {{-- JATUH TEMPO --}}
                                <td class="px-4 py-3.5">

                                    @php
                                        $jatuhTempo = \Carbon\Carbon::parse($item->jatuh_tempo)->startOfDay();
                                        $hariIni = now()->startOfDay();
                                        $selisih = (int) $hariIni->diffInDays($jatuhTempo, false);

                                        $showReminder = $item->status != 'lunas';
                                    @endphp

                                    <div class="flex flex-col gap-1">

                                        <span
                                            class="
            @if ($item->status != 'Lunas' && $selisih < 0) font-medium text-gray-600
            @elseif ($item->status != 'Lunas' && $selisih <= $reminder)
                font-medium text-gray-600
            @else
                text-gray-600 @endif
        ">
                                            {{ $jatuhTempo->format('d M Y') }}
                                        </span>

                                        @if ($item->status != 'lunas')
                                            @if ($selisih < 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">

                                                    Terlambat {{ abs($selisih) }} hari
                                                </span>
                                            @elseif ($selisih <= $reminder)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold w-fit animate-pulse">


                                                    @if ($selisih == 0)
                                                        Jatuh Tempo Hari Ini
                                                    @elseif ($selisih == 1)
                                                        Jatuh Tempo Besok
                                                    @else
                                                        Jatuh Tempo {{ $selisih }} hari lagi
                                                    @endif

                                                </span>
                                            @endif
                                        @endif

                                    </div>

                                </td>

                                {{-- STATUS --}}
                                <td class="px-4 py-3.5">
                                    <form action="{{ route('hutang-vendor.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                            class="px-3 py-1 rounded-full text-xs font-semibold cursor-pointer border-0 focus:outline-none
                                        @if ($item->status == 'lunas') bg-green-100 text-green-700
                                        @elseif($item->status == 'cicilan') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                            <option value="belum_lunas"
                                                {{ $item->status == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                            <option value="lunas" {{ $item->status == 'lunas' ? 'selected' : '' }}>Lunas
                                            </option>
                                        </select>
                                    </form>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center">
                                        <form action="{{ route('hutang-vendor.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
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
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa fa-file-invoice-dollar text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data hutang vendor</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan hutang baru
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>

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
    MODAL TAMBAH DATA
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Hutang Vendor</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data hutang supplier & vendor</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('hutang-vendor.store') }}" method="POST" class="px-6 py-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Vendor <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_vendor" required placeholder="Nama vendor / supplier"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="kategori" required placeholder="Bengkel / Sparepart / Leasing"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="nominal" required placeholder="Nominal hutang"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dibayar <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="dibayar" value="0" required placeholder="Jumlah sudah dibayar"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white">
                            <option value="belum_lunas">Belum Lunas</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span
                                class="text-red-500">*</span></label>
                        <textarea name="keterangan" rows="3" required placeholder="Keterangan hutang..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-3 pt-1">
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2 rounded-xl text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors flex items-center gap-2">
                            <i class="fa fa-save text-sm"></i> Simpan
                        </button>
                    </div>

                </div>
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
        }
    </style>

    <script>
        // -- MODAL ------------------------------------------
        const modalTambah = document.getElementById('modalTambah');

        function openModal() {
            modalTambah.classList.remove('hidden');
            modalTambah.classList.add('flex');
        }

        function closeModal() {
            modalTambah.classList.add('hidden');
            modalTambah.classList.remove('flex');
        }

        modalTambah.addEventListener('click', function(e) {
            if (e.target === modalTambah) closeModal();
        });

        // -- FILTER & SEARCH --------------------------------
        function applyFilters() {
            const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
            const status = document.getElementById('filterStatus').value;

            const rows = document.querySelectorAll('#hutangTableBody tr[data-search]');
            let visible = 0;

            rows.forEach(row => {
                const matchSearch = !keyword || row.dataset.search.includes(keyword);
                const matchStatus = !status || row.dataset.status === status;
                const show = matchSearch && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('totalCount').textContent = visible + ' total data';

            const noResult = document.getElementById('noResultRow');
            if (noResult) noResult.classList.toggle('hidden', visible > 0 || rows.length === 0);

            // Renumber
            let num = 1;
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cell = row.querySelector('.row-number');
                    if (cell) cell.textContent = num++;
                }
            });
        }

        // -- POPUP ALERT ------------------------------------
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

        function updateExportLinks() {
            const keyword = document.getElementById('searchInput').value;
            const status = document.getElementById('filterStatus').value;

            const params = new URLSearchParams();
            if (keyword) params.set('search', keyword);
            if (status) params.set('status', status);
            const query = params.toString() ? '?' + params.toString() : '';

            document.getElementById('pdfBtn').href = `/admin/hutang-vendor/pdf${query}`;
            document.getElementById('excelBtn').href = `/admin/hutang-vendor/excel${query}`;
        }

        // Ganti semua pemanggilan updatePdfLink() ? updateExportLinks()
        document.getElementById('searchInput').addEventListener('input', updateExportLinks);
        document.getElementById('filterStatus').addEventListener('change', updateExportLinks);
        updateExportLinks(); // initial

        // panggil setiap filter berubah
        document.getElementById('searchInput').addEventListener('input', updatePdfLink);
        document.getElementById('filterStatus').addEventListener('change', updatePdfLink);

        // initial
        updatePdfLink();
    </script>

@endsection
