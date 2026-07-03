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
                <h1 class="text-2xl font-bold text-gray-800">Data Mobil Bermasalah</h1>
                <p class="text-sm text-gray-500 mt-0.5">Detail mobil bermasalah</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150 mt-2 sm:mt-0">
                <i class="fa fa-plus text-sm"></i>
                Tambah
            </button>
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
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Mobil Bermasalah</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $data->count() }} total data</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">

                    {{-- FILTER STATUS --}}
                    <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-0.5 bg-gray-50">
                        <button type="button" onclick="setActiveStatus('semua')" id="btnSemua"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors bg-white text-gray-700 shadow-sm border border-gray-200">
                            Semua
                        </button>
                        <button type="button" onclick="setActiveStatus('Layak')" id="btnLayak"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-green-600">
                            <i class="fa fa-check text-[10px]"></i> Layak
                        </button>
                        <button type="button" onclick="setActiveStatus('Tidak Layak')" id="btnTidakLayak"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-red-500">
                            <i class="fa fa-times text-[10px]"></i> Tidak Layak
                        </button>
                    </div>

                    {{-- FILTER BULAN (MANUAL DATE) --}}
                    <div class="flex items-center gap-1">
                        <input type="month" id="filterBulan" onchange="setActiveBulan(this.value)"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <button type="button" onclick="resetBulan()" title="Hapus filter bulan"
                            class="px-2 py-1.5 text-xs text-gray-400 hover:text-red-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <a id="pdfBtn" href="{{ route('service-detail.pdf') }}" target="_blank"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        PDF
                    </a>
                    <div class="relative">
                        <i
                            class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" id="searchInput" placeholder="Cari kendaraan, service..."
                            oninput="applyFilters()"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-48">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
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
                                Kendaraan
                            </th>


                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kilometer
                            </th>


                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Keluhan
                            </th>

                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tanggal Service
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Biaya
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status
                            </th>

                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti
                            </th>

                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody">
                        @forelse($data as $i => $d)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower(($d->kendaraan->nopol ?? '') . ' ' . ($d->kendaraan->merk ?? '') . ' ' . $d->keterangan) }}"
                                data-status="{{ $d->status }}"
                                data-bulan="{{ $d->tanggal_service ? \Carbon\Carbon::parse($d->tanggal_service)->format('Y-m') : '' }}">

                                {{-- NO --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium row-number">
                                    {{ $i + 1 }}
                                </td>

                                {{-- KENDARAAN --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fa fa-car text-blue-400 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $d->kendaraan->merk ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-400 font-mono">
                                                {{ $d->kendaraan->nopol ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>


                                {{-- KILOMETER --}}
                                <td class="px-4 py-3.5 text-xs text-gray-600">
                                    {{ number_format($d->kilometer ?? 0, 0, ',', '.') }} km
                                </td>


                                {{-- KELUHAN --}}
                                <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px] truncate">
                                    {{ $d->keterangan ?? '-' }}
                                </td>
                                {{-- TANGGAL SERVICE --}}
                                <td class="px-4 py-3.5 text-xs text-gray-600">
                                    {{ $d->tanggal_service ?? '-' }}
                                </td>

                                {{-- BIAYA --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-semibold text-emerald-600">
                                        Rp {{ number_format($d->biaya ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <button type="button" class="btn-status" data-id="{{ $d->id }}"
                                        data-status="{{ $d->status }}" onclick="openStatusModal(this)">
                                        @if ($d->status == 'Layak')
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-lg bg-green-100 text-green-600">
                                                Layak
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-lg bg-red-100 text-red-600">
                                                Tidak Layak
                                            </span>
                                        @endif
                                    </button>
                                </td>

                                {{-- BUKTI --}}
                                <td>
                                    @if ($d->bukti)
                                        @php
                                            $filename = basename($d->bukti);
                                        @endphp

                                        <a href="{{ asset($d->bukti) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>


                                {{-- AKSI --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <button
                                            class="btn-edit inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                            data-id="{{ $d->id }}" data-kendaraan_id="{{ $d->kendaraan_id }}"
                                            data-tanggal_service="{{ $d->tanggal_service }}"
                                            data-kilometer="{{ $d->kilometer }}" data-status="{{ $d->status }}"
                                            data-biaya="{{ $d->biaya }}" data-keterangan="{{ $d->keterangan }}"
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
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <p class="text-sm text-gray-500">Belum ada data service</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

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
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Service</label>
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
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Kendaraan</label>
                    <select name="status" id="status"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="Layak">Layak</option>
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
        // ── MODAL ──────────────────────────────────────────
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

            // Preview bukti — pakai previewWrapper + previewBukti logic yang sudah ada
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
                        class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs">✕</button>
                </div>`;
                } else if (ext === 'pdf') {
                    html = `
                <div class="flex items-center justify-between p-3 border rounded-xl bg-red-50 text-red-600">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <i class="fa-solid fa-file-pdf"></i> ${btn.dataset.bukti.split('/').pop()}
                    </div>
                    <button type="button" onclick="removeBukti()"
                        class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">✕</button>
                </div>`;
                } else if (['doc', 'docx'].includes(ext)) {
                    html = `
                <div class="flex items-center justify-between p-3 border rounded-xl bg-blue-50 text-blue-600">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <i class="fa-solid fa-file-word"></i> ${btn.dataset.bukti.split('/').pop()}
                    </div>
                    <button type="button" onclick="removeBukti()"
                        class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">✕</button>
                </div>`;
                }

                previewWrapper.innerHTML = html;
                previewWrapper.classList.remove('hidden');
            } else {
                previewWrapper.classList.add('hidden');
            }

            // Buka modal — gunakan variable global, bukan deklarasi ulang
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        // ── FILTER STATUS + BULAN + SEARCH ──────────────────
        let activeStatus = 'semua';
        let activeBulan = 'semua';

        function setActiveStatus(status) {
            activeStatus = status;

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
            applyFilters();
        }

        function resetBulan() {
            document.getElementById('filterBulan').value = '';
            activeBulan = 'semua';
            applyFilters();
        }

        function applyFilters() {
            const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#serviceTableBody tr[data-search]');
            let visible = 0;

            rows.forEach(row => {
                const matchSearch = !keyword || row.dataset.search.includes(keyword);
                const matchStatus = activeStatus === 'semua' || row.dataset.status === activeStatus;
                const matchBulan = activeBulan === 'semua' || row.dataset.bulan === activeBulan;
                const show = matchSearch && matchStatus && matchBulan;

                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('totalCount').textContent = visible + ' total data';

            const noResult = document.getElementById('noResultRow');
            if (noResult) noResult.classList.toggle('hidden', visible > 0 || rows.length === 0);

            let num = 1;
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cell = row.querySelector('.row-number');
                    if (cell) cell.textContent = num++;
                }
            });

            updatePdfLink();
        }

        // ── PDF LINK IKUT BAWA FILTER AKTIF ─────────────────
        function updatePdfLink() {
            const keyword = document.getElementById('searchInput').value;
            const pdfBtn = document.getElementById('pdfBtn');

            const params = new URLSearchParams();
            if (keyword) params.set('search', keyword);
            if (activeStatus !== 'semua') params.set('status', activeStatus);
            if (activeBulan !== 'semua') params.set('bulan', activeBulan);

            const qs = params.toString();
            pdfBtn.href = "{{ route('service-detail.pdf') }}" + (qs ? '?' + qs : '');
        }

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
                    ✕
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
                    ✕
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

        // Inisialisasi awal (set link PDF & totalCount sesuai state default)
        applyFilters();
    </script>

@endsection
