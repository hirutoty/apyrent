@extends('admin.layouts.app')

@section('title', 'Mobil Bermasalah')

@section('content')

    @php
        $jumlahService = $data->count();
        $totalBiaya = $data->sum('biaya');
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Service Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Riwayat Service & Data Mobil Bermasalah</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150 mt-2 sm:mt-0">
                <i class="fa fa-plus text-sm"></i>
                Tambah
            </button>
        </div>

        {{-- NAV TABS --}}
        <div>
            <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
                @php
                    $navItems = [
                        ['label' => 'Service History',  'url' => '/admin/service-history', 'icon' => 'bi bi-clock-history'],
                        ['label' => 'Mobil Bermasalah', 'url' => '/admin/service-detail',  'icon' => 'bi bi-exclamation-triangle-fill'],
                        ['label' => 'Reminder Service', 'url' => '/admin/reminder-service','icon' => 'bi bi-bell-fill'],
                    ];
                @endphp
                @foreach ($navItems as $item)
                    @php $isActiveTab = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*'); @endphp
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-semibold whitespace-nowrap rounded-lg transition-all duration-150
                            {{ $isActiveTab ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-white/60' }}">
                        <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-screwdriver-wrench text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Jumlah Mobil Bermasalah</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $jumlahService }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-wallet text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Biaya</p>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TOOLBAR --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800">Mobil Bermasalah</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $data->count() }} data</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Filter Status --}}
                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                        <button type="button" onclick="setActiveStatus('semua')" id="btnSemua"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors bg-white text-gray-700 shadow-sm">
                            Semua
                        </button>
                        <button type="button" onclick="setActiveStatus('Layak')" id="btnLayak"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-green-600">
                            Layak
                        </button>
                        <button type="button" onclick="setActiveStatus('Tidak Layak')" id="btnTidakLayak"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-red-500">
                            Tidak Layak
                        </button>
                    </div>
                    {{-- Filter Bulan --}}
                    <input type="month" id="filterBulan" onchange="setActiveBulan(this.value)"
                        class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    {{-- Search --}}
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" id="searchInput" placeholder="Cari kendaraan..."
                            oninput="applyFilters()"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>
                    <a id="pdfBtn" href="{{ route('service-detail.pdf') }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <button type="button" id="btnExpandAll" onclick="expandAllAccordion()"
                        class="px-3 py-1.5 text-xs font-medium text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                        <i class="fa fa-chevron-down text-xs"></i> Buka Semua
                    </button>
                    <button type="button" id="btnCollapseAll" onclick="collapseAllAccordion()"
                        class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-chevron-right text-xs"></i> Tutup Semua
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Keluhan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">KM</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Bukti</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody">
                        @php
                            $grouped = $data->groupBy('kendaraan_id');
                            $rowCounter = 0;
                        @endphp
                        @forelse($grouped as $kendaraanId => $rows)
                            @php
                                $firstRow   = $rows->first();
                                $merk       = $firstRow->kendaraan->merk  ?? '-';
                                $nopol      = $firstRow->kendaraan->nopol ?? '-';
                                $groupCount = $rows->count();
                                $groupBiaya = $rows->sum('biaya');
                                $groupKey   = 'group-' . $kendaraanId;
                            @endphp

                            {{-- -- GROUP HEADER ROW -- --}}
                            <tr class="group-header bg-blue-50 border-t border-blue-100 border-l-4 border-l-blue-500 cursor-pointer select-none hover:bg-blue-100 transition-colors duration-150 shadow-sm"
                                data-group="{{ $groupKey }}"
                                onclick="toggleAccordion('{{ $groupKey }}', this)">
                                <td class="px-4 py-3" colspan="2" class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa fa-car text-blue-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $merk }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $nopol }}</p>
                                        </div>
                                        <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-200 text-blue-700 text-[10px] font-bold">
                                            {{ $groupCount }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400 italic" colspan="3">
                                    {{ $groupCount }} catatan
                                </td>
                                <td class="px-4 py-3" colspan="2" class="px-5 py-3.5">
                                    <span class="text-sm font-bold text-emerald-600">
                                        Rp {{ number_format($groupBiaya, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3" colspan="2" class="px-5 py-3.5">
                                    <div class="flex justify-end pr-2">
                                        <i class="fa fa-chevron-down text-blue-400 text-xs group-chevron transition-transform duration-200"
                                           data-group="{{ $groupKey }}"></i>
                                    </div>
                                </td>
                            </tr>

                            {{-- -- CHILD / DETAIL ROWS -- --}}
                            @foreach($rows as $d)
                                @php $rowCounter++ @endphp
                                <tr class="group-child border-t border-gray-100 odd:bg-white even:bg-gray-100 hover:bg-blue-50/40 transition-colors duration-100"
                                    data-group-child="{{ $groupKey }}"
                                    data-search="{{ strtolower(($d->kendaraan->nopol ?? '') . ' ' . ($d->kendaraan->merk ?? '') . ' ' . $d->keterangan) }}"
                                    data-status="{{ $d->status }}"
                                    data-bulan="{{ $d->tanggal_service ? \Carbon\Carbon::parse($d->tanggal_service)->format('Y-m-d') : '' }}"
                                    style="display:none;">

                                    {{-- KELUHAN --}}
                                    <td class="px-5 py-4 text-sm text-gray-700 max-w-xs">
                                        <span class="line-clamp-2">{{ $d->keterangan ?? '-' }}</span>
                                    </td>

                                    {{-- KM --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ number_format($d->kilometer ?? 0, 0, ',', '.') }} km
                                    </td>

                                    {{-- TANGGAL --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $d->tanggal_service ? \Carbon\Carbon::parse($d->tanggal_service)->format('d M Y') : '-' }}
                                    </td>

                                    {{-- BIAYA --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-800">
                                            Rp {{ number_format($d->biaya ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-5 py-4">
                                        <button type="button" class="btn-status" data-id="{{ $d->id }}"
                                            data-status="{{ $d->status }}" onclick="openStatusModal(this)">
                                            @if ($d->status == 'Layak')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Layak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tidak Layak
                                                </span>
                                            @endif
                                        </button>
                                    </td>

                                    {{-- BUKTI --}}
                                    <td class="px-5 py-4">
                                        @if ($d->bukti)
                                            @php $filename = basename($d->bukti); @endphp
                                            <a href="{{ asset($d->bukti) }}" target="_blank"
                                                class="text-blue-500 text-xs hover:underline">
                                                <i class="fa fa-paperclip text-[10px]"></i> {{ $filename }}
                                            </a>
                                        @else
                                            <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button
                                                class="btn-edit inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                                data-id="{{ $d->id }}"
                                                data-kendaraan_id="{{ $d->kendaraan_id }}"
                                                data-tanggal_service="{{ $d->tanggal_service }}"
                                                data-kilometer="{{ $d->kilometer }}"
                                                data-status="{{ $d->status }}"
                                                data-biaya="{{ $d->biaya }}"
                                                data-keterangan="{{ $d->keterangan }}"
                                                data-bukti="{{ $d->bukti }}">
                                                <i class="fa fa-edit text-xs"></i> Edit
                                            </button>

                                            <form action="{{ route('service-detail.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
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
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <p class="text-sm text-gray-500">Belum ada data service</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
                <div id="noResultRow" class="hidden px-5 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fa fa-search text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Tidak ada hasil yang cocok</p>
                        <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau filter</p>
                    </div>
                </div>

            </div>

            {{-- FOOTER: SHOWING INFO + PAGINATION --}}
            <div id="tableFooter" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                <p id="showingInfo" class="text-xs text-gray-500"></p>
                <div id="paginationControls" class="flex items-center gap-1"></div>
            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH / EDIT
====================================== --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 overflow-y-auto py-6"
        style="backdrop-filter:blur(2px)">

        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 max-h-[130vh] flex flex-col">

            <!-- HEADER (FIX) -->
            <div class="px-6 py-5 border-b border-gray-100 shrink-0">




                <div>
                    <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Data Service</h2>
                    <p id="modalDesc" class="text-xs text-gray-500 mt-0.5">Isi data detail service kendaraan</p>
                </div>

                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="form" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 space-y-4 overflow-y-auto">
                @csrf

                {{-- KENDARAAN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                    <select name="kendaraan_id" id="kendaraan_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}">
                                {{ $k->merk }} - {{ $k->nopol }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TANGGAL SERVICE --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bermasalah</label>
                    <input type="date" name="tanggal_service" id="tanggal_service"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                {{-- KILOMETER --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kilometer</label>
                    <input type="number" name="kilometer" id="kilometer" placeholder="Contoh: 45000"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                {{-- STATUS --}}
                <div>
                    {{-- <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Kendaraan</label> --}}
                    <select name="status" id="status" hidden
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="Tidak Layak">Tidak Layak</option>
                    </select>
                </div>

                {{-- KELUHAN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keluhan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" placeholder="Keluhan mobil"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
                </div>

                {{-- BIAYA --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya</label>
                    <input type="number" name="biaya" id="biaya" placeholder="Nominal biaya service"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Bukti Upload
                    </label>

                    {{-- PREVIEW AREA --}}
                    <div id="previewWrapper" class="hidden mb-3"></div>

                    {{-- UPLOAD AREA --}}
                    <label for="bukti"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-1"></i>

                        <span class="text-xs text-slate-500">
                            Klik untuk upload / ganti file
                        </span>

                        <span class="text-[11px] text-slate-400">
                            JPG, PNG, PDF, DOC, DOCX (maks 2MB)
                        </span>
                    </label>

                    <input type="file" name="bukti" id="bukti" class="hidden"
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" onchange="previewBukti(this)">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl">
                    <i class="fa fa-save text-sm"></i> Simpan
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

    <div id="statusModal" class="fixed inset-0 hidden items-center justify-center bg-black/30 z-50">
        <div class="bg-white p-5 rounded-xl w-80">

            <h2 class="font-bold text-sm mb-3">Ubah Status</h2>

            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')

                <select name="status" id="statusSelect" class="w-full border rounded-lg px-3 py-2 text-sm mb-4">
                    <option value="Layak">Layak</option>
                    <option value="Tidak Layak">Tidak Layak</option>
                </select>

                <button class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm">
                    Simpan
                </button>
            </form>

            <button onclick="closeStatusModal()" class="text-xs text-gray-500 mt-2 w-full">
                Batal
            </button>
        </div>
    </div>


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
        function setModalMode(mode) {
            const title = document.querySelector('#modalTitle');
            const desc = document.querySelector('#modalDesc');

            if (mode === 'edit') {
                if (title) title.textContent = 'Edit Data Service';
                if (desc) desc.textContent = 'Ubah data detail service kendaraan';
            } else {
                if (title) title.textContent = 'Tambah Data Service';
                if (desc) desc.textContent = 'Isi data detail service kendaraan';
            }
        }
        // -- MODAL ------------------------------------------
        const modal = document.getElementById('modal');

        function openModal() {
            setModalMode('add');

            document.getElementById('form').reset();
            document.getElementById('form').action = '/admin/service-detail';

            const mp = document.getElementById('method-put');
            if (mp) mp.remove();

            // Reset preview bukti
            const previewWrapper = document.getElementById('previewWrapper');
            if (previewWrapper) {
                previewWrapper.innerHTML = '';
                previewWrapper.classList.add('hidden');
            }

            // Reset input file
            const buktiInput = document.getElementById('bukti');
            if (buktiInput) buktiInput.value = '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });


        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-edit');
            if (!btn) return;

            setModalMode('edit');

            const form = document.getElementById('form');
            form.action = '/admin/service-detail/' + btn.dataset.id;

            // Hapus method PUT lama jika ada, lalu tambah baru
            const existingMp = document.getElementById('method-put');
            if (existingMp) existingMp.remove();

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_method';
            input.value = 'PUT';
            input.id = 'method-put';
            form.appendChild(input);

            // Isi field form
            document.getElementById('biaya').value = btn.dataset.biaya;
            document.getElementById('keterangan').value = btn.dataset.keterangan;
            document.getElementById('kendaraan_id').value = btn.dataset.kendaraan_id;
            document.getElementById('tanggal_service').value = btn.dataset.tanggal_service;
            document.getElementById('kilometer').value = btn.dataset.kilometer;
            document.getElementById('status').value = btn.dataset.status;

            // Preview bukti � pakai previewWrapper + previewBukti logic yang sudah ada
            const previewWrapper = document.getElementById('previewWrapper');
            previewWrapper.innerHTML = '';

            if (btn.dataset.bukti && btn.dataset.bukti !== 'null' && btn.dataset.bukti !== '') {
                const buktiUrl = '/' + btn.dataset.bukti;
                const ext = btn.dataset.bukti.split('.').pop().toLowerCase();

                let html = '';

                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    html = `
                <div class="relative inline-block">
                    <img src="${buktiUrl}" class="w-28 h-28 object-cover rounded-lg border shadow">
                    <button type="button" onclick="removeBukti()"
                        class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs">?</button>
                </div>`;
                } else if (ext === 'pdf') {
                    html = `
                <div class="flex items-center justify-between p-3 border rounded-xl bg-red-50 text-red-600">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <i class="fa-solid fa-file-pdf"></i> ${btn.dataset.bukti.split('/').pop()}
                    </div>
                    <button type="button" onclick="removeBukti()"
                        class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">?</button>
                </div>`;
                } else if (['doc', 'docx'].includes(ext)) {
                    html = `
                <div class="flex items-center justify-between p-3 border rounded-xl bg-blue-50 text-blue-600">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <i class="fa-solid fa-file-word"></i> ${btn.dataset.bukti.split('/').pop()}
                    </div>
                    <button type="button" onclick="removeBukti()"
                        class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">?</button>
                </div>`;
                }

                previewWrapper.innerHTML = html;
                previewWrapper.classList.remove('hidden');
            } else {
                previewWrapper.classList.add('hidden');
            }

            // Buka modal � gunakan variable global, bukan deklarasi ulang
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        // -- FILTER STATUS + BULAN + SEARCH ------------------
        let activeStatus = 'semua';
        let activeBulan = 'semua';
        let activeTahun = 'semua';
        let currentPage = 1;

        function setActiveStatus(status) {
            activeStatus = status;
            currentPage = 1;

            const buttons = {
                semua: document.getElementById('btnSemua'),
                'Layak': document.getElementById('btnLayak'),
                'Tidak Layak': document.getElementById('btnTidakLayak'),
            };

            Object.entries(buttons).forEach(([key, btn]) => {
                if (!btn) return;

                if (key === status) {
                    btn.classList.add('bg-white', 'shadow-sm', 'border', 'border-gray-200');
                    if (key === 'Layak') btn.classList.add('text-green-700');
                    else if (key === 'Tidak Layak') btn.classList.add('text-red-600');
                    else btn.classList.add('text-gray-700');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('bg-white', 'shadow-sm', 'border', 'border-gray-200', 'text-green-700',
                        'text-red-600', 'text-gray-700');
                    btn.classList.add('text-gray-500');
                }
            });

            applyFilters();
        }

        function setActiveBulan(bulan) {
            activeBulan = (bulan && bulan.trim() !== '') ? bulan : 'semua';
            currentPage = 1;
            applyFilters();
        }

        function resetBulan() {
            document.getElementById('filterBulan').value = '';
            activeBulan = 'semua';
            currentPage = 1;
            applyFilters();
        }

        function setActiveTahun(tahun) {
            activeTahun = tahun;
            // Reset filter bulan jika ganti tahun
            document.getElementById('filterBulan').value = '';
            activeBulan = 'semua';
            currentPage = 1;
            applyFilters();
        }

        function applyFilters() {
            const keyword    = document.getElementById('searchInput').value.toLowerCase().trim();
            const filterHari = document.getElementById('filterHari').value;
            const showVal    = document.getElementById('showEntries').value;
            const perPage    = showVal === 'all' ? Infinity : parseInt(showVal);

            // All child rows (have data-search attribute)
            const childRows = document.querySelectorAll('#serviceTableBody tr[data-search]');

            // Determine which child rows pass the filters
            const matched = [];
            childRows.forEach(row => {
                const matchSearch = !keyword || row.dataset.search.includes(keyword);
                const matchStatus = activeStatus === 'semua' || row.dataset.status === activeStatus;

                const tgl               = row.dataset.bulan || '';
                const [rowYear, rowMonth, rowDay] = tgl.split('-');
                const rowYM             = rowYear && rowMonth ? `${rowYear}-${rowMonth}` : '';

                const matchHari  = !filterHari    || rowDay   === filterHari;
                const matchBulan = activeBulan === 'semua' || rowYM    === activeBulan;
                const matchTahun = activeTahun === 'semua' || rowYear  === activeTahun;

                if (matchSearch && matchStatus && matchHari && matchBulan && matchTahun) {
                    matched.push(row);
                }
            });

            const total      = matched.length;
            const totalPages = perPage === Infinity ? 1 : Math.ceil(total / perPage);
            if (currentPage > totalPages) currentPage = 1;

            const start = perPage === Infinity ? 0 : (currentPage - 1) * perPage;
            const end   = perPage === Infinity ? total : Math.min(start + perPage, total);

            // Build a Set of rows visible on this page
            const pageSet = new Set(matched.slice(start, end));

            // Build a Set of groups that have at least one matching row overall
            const visibleGroups = new Set(matched.map(r => r.dataset.groupChild));

            // Show/hide each child row
            childRows.forEach(row => {
                if (pageSet.has(row)) {
                    // Only show if its group is expanded
                    const gKey = row.dataset.groupChild;
                    row.style.display = groupState[gKey] ? '' : 'none';
                    row.style.opacity = '1';
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide group header rows
            document.querySelectorAll('#serviceTableBody tr.group-header').forEach(headerRow => {
                const gKey = headerRow.dataset.group;
                headerRow.style.display = visibleGroups.has(gKey) ? '' : 'none';
            });

            // Renumber visible child rows
            let num = start + 1;
            matched.slice(start, end).forEach(row => {
                const cell = row.querySelector('.row-number');
                if (cell) cell.textContent = num++;
            });

            // Info text
            const showingInfo = document.getElementById('showingInfo');
            if (showingInfo) {
                if (total === 0) {
                    showingInfo.textContent = 'Tidak ada data yang ditampilkan';
                } else {
                    showingInfo.textContent = `Menampilkan ${start + 1} sampai ${end} dari ${total} data`;
                }
            }

            // Pagination
            renderPagination(totalPages);

            // Header count
            document.getElementById('totalCount').textContent = total + ' total data';

            // No result row
            const noResult = document.getElementById('noResultRow');
            if (noResult) noResult.classList.toggle('hidden', total > 0 || childRows.length === 0);

            updatePdfLink();
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('paginationControls');
            if (!container) return;
            container.innerHTML = '';

            if (totalPages <= 1) return;

            const btnClass = 'px-2.5 py-1 text-xs rounded-lg border transition-colors';
            const activeClass = 'bg-blue-600 text-white border-blue-600';
            const normalClass = 'border-gray-200 text-gray-600 hover:bg-gray-50';

            // Prev
            const prev = document.createElement('button');
            prev.innerHTML = '<i class="fa fa-chevron-left text-[10px]"></i>';
            prev.className = `${btnClass} ${currentPage === 1 ? 'opacity-40 cursor-not-allowed border-gray-200 text-gray-400' : normalClass}`;
            prev.disabled = currentPage === 1;
            prev.onclick = () => { currentPage--; applyFilters(); };
            container.appendChild(prev);

            // Page numbers (max 5 pages shown)
            const range = 2;
            for (let i = 1; i <= totalPages; i++) {
                if (
                    i === 1 || i === totalPages ||
                    (i >= currentPage - range && i <= currentPage + range)
                ) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    btn.className = `${btnClass} ${i === currentPage ? activeClass : normalClass}`;
                    btn.onclick = (function(page) {
                        return function() { currentPage = page; applyFilters(); };
                    })(i);
                    container.appendChild(btn);
                } else if (
                    i === currentPage - range - 1 ||
                    i === currentPage + range + 1
                ) {
                    const dots = document.createElement('span');
                    dots.textContent = '...';
                    dots.className = 'px-1 text-xs text-gray-400';
                    container.appendChild(dots);
                }
            }

            // Next
            const next = document.createElement('button');
            next.innerHTML = '<i class="fa fa-chevron-right text-[10px]"></i>';
            next.className = `${btnClass} ${currentPage === totalPages ? 'opacity-40 cursor-not-allowed border-gray-200 text-gray-400' : normalClass}`;
            next.disabled = currentPage === totalPages;
            next.onclick = () => { currentPage++; applyFilters(); };
            container.appendChild(next);
        }

        // -- PDF LINK IKUT BAWA FILTER AKTIF -----------------
        function updatePdfLink() {
            const keyword  = document.getElementById('searchInput').value;
            const filterHari = document.getElementById('filterHari').value;
            const pdfBtn   = document.getElementById('pdfBtn');

            const params = new URLSearchParams();
            if (keyword)               params.set('search', keyword);
            if (filterHari)            params.set('hari', filterHari);
            if (activeStatus !== 'semua') params.set('status', activeStatus);
            if (activeBulan  !== 'semua') params.set('bulan', activeBulan);
            if (activeTahun  !== 'semua') params.set('tahun', activeTahun);

            const qs = params.toString();
            pdfBtn.href = "{{ route('service-detail.pdf') }}" + (qs ? '?' + qs : '');
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

        function openStatusModal(el) {
            const id = el.dataset.id;
            const status = el.dataset.status;

            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            const select = document.getElementById('statusSelect');

            form.action = 'service/service-detail/' + id + '/status';
            select.value = status;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeStatusModal() {
            const modal = document.getElementById('statusModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.getElementById('statusForm').reset();
        }

        function previewBukti(input) {
            const wrapper = document.getElementById('previewWrapper');
            wrapper.innerHTML = '';
            wrapper.classList.remove('hidden');

            const file = input.files[0];
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);

            let html = '';

            // IMAGE
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                html = `
            <div class="relative inline-block">
                <img src="${url}"
                    class="w-28 h-28 object-cover rounded-lg border shadow">

                <button type="button"
                    onclick="removeBukti()"
                    class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            // PDF / DOC
            else {
                let icon = 'fa-file';
                let color = 'bg-gray-50 text-gray-600';

                if (ext === 'pdf') {
                    icon = 'fa-file-pdf';
                    color = 'bg-red-50 text-red-600';
                }

                if (ext === 'doc' || ext === 'docx') {
                    icon = 'fa-file-word';
                    color = 'bg-blue-50 text-blue-600';
                }

                html = `
            <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <i class="fa-solid ${icon}"></i>
                    ${file.name}
                </div>

                <button type="button"
                    onclick="removeBukti()"
                    class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            wrapper.innerHTML = html;
        }

        function removeBukti() {
            const input = document.getElementById('bukti');
            const wrapper = document.getElementById('previewWrapper');

            input.value = '';
            wrapper.innerHTML = '';
            wrapper.classList.add('hidden');
        }

        // -- ACCORDION GROUP TOGGLE ----------------------------
        // Track which groups are expanded (key = groupKey, value = bool)
        const groupState = {};

        function toggleAccordion(groupKey, headerRow) {
            const isExpanded = groupState[groupKey] || false;
            const newState   = !isExpanded;
            groupState[groupKey] = newState;

            // Show/hide child rows
            const childRows = document.querySelectorAll(`tr[data-group-child="${groupKey}"]`);
            childRows.forEach(row => {
                if (newState) {
                    row.style.display = '';
                    row.style.opacity = '0';
                    // Smooth fade-in
                    requestAnimationFrame(() => {
                        row.style.transition = 'opacity 0.18s ease';
                        row.style.opacity = '1';
                    });
                } else {
                    row.style.transition = 'opacity 0.15s ease';
                    row.style.opacity = '0';
                    setTimeout(() => { row.style.display = 'none'; }, 150);
                }
            });

            // Rotate chevron
            const chevron = document.querySelector(`.group-chevron[data-group="${groupKey}"]`);
            if (chevron) {
                chevron.style.transform = newState ? 'rotate(180deg)' : 'rotate(0deg)';
            }

            // Update header row appearance
            if (headerRow) {
                if (newState) {
                    headerRow.classList.add('bg-blue-100');
                    headerRow.classList.remove('bg-blue-50');
                } else {
                    headerRow.classList.add('bg-blue-50');
                    headerRow.classList.remove('bg-blue-100');
                }
            }
        }

        function expandAllAccordion() {
            document.querySelectorAll('tr.group-header').forEach(headerRow => {
                const key = headerRow.dataset.group;
                if (key && !groupState[key]) {
                    toggleAccordion(key, headerRow);
                }
            });
        }

        function collapseAllAccordion() {
            document.querySelectorAll('tr.group-header').forEach(headerRow => {
                const key = headerRow.dataset.group;
                if (key && groupState[key]) {
                    toggleAccordion(key, headerRow);
                }
            });
        }

        // -- END ACCORDION -------------------------------------

        // Inisialisasi awal (set link PDF & totalCount sesuai state default)
        applyFilters();
    </script>

@endsection
