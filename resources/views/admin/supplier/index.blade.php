@extends('admin.layouts.app')

@section('title', 'Supplier')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Supplier</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data supplier barang</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="bi bi-plus-lg text-sm"></i>
                Tambah Supplier
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-1 gap-5">

            {{-- Total Supplier --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Supplier</p>
                        <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalSupplier }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-people-fill text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- CHART FILTER --}}
        <x-chart-filter id="supplierChartFilter" defaultFilter="year" :showCustomRange="true" />

        {{-- CHART CONTAINER --}}
        <x-chart-container
            id="supplierChartContainer"
            layout="stacked"
            pieTitle="Distribusi Supplier per Nama" pieId="supplierPieChart"
            barTitle="Supplier per Periode (Jumlah Barang / Supplier / Nominal)" barId="supplierBarChart"
            lineTitle="Trend Total Nominal" lineId="supplierLineChart"
            :showStats="true" :statsData="[]"
        />

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Supplier</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        @if($search)
                            Menampilkan {{ $data->total() }} dari {{ $totalSupplier }} supplier 
                            <span class="text-blue-600 font-medium">(pencarian: "{{ $search }}")</span>
                        @else
                            {{ $totalSupplier }} total supplier
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a id="pdfBtn" target="_blank" href="{{ route('supplier.export.pdf') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-file-pdf text-xs"></i> Export PDF
                    </a>
                    <form method="GET" action="{{ route('supplier.index') }}" class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari supplier atau no telp..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                    </form>
                    @if($search)
                        <a href="{{ route('supplier.index') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors"
                            title="Clear search">
                            <i class="fa fa-times text-xs"></i> Clear
                        </a>
                    @endif
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
                                Supplier</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                                Telp</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                    Nama Marketing</th>
                                    <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                        Kontak Marketing</th>
                                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                            Alamat</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Barang</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jumlah</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Harga</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Total</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="supplierTableBody">
                        @forelse ($data as $d)
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100"
                                data-search="{{ strtolower($d->nama_supplier . ' ' . $d->no_telp . ' ' . $d->alamat . ' ' . $d->nama_marketing . ' ' . $d->kontak_marketing . ' ' . ($d->user->name ?? '')) }}">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $data->firstItem() + $loop->index }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="bi bi-person-lines-fill text-blue-400 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $d->nama_supplier }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_telp }}</span>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->nama_marketing ?? '-' }}</td>

                                <td class="px-4 py-3.5">
                                    @if($d->kontak_marketing)
                                        <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->kontak_marketing }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->alamat ?? '-' }}</td>

                                @php
                                    $totalPr      = $d->purchaseros->count();
                                    $totalNominal = $d->purchaseros->sum(function($pr) {
                                        if ($pr->items && $pr->items->isNotEmpty()) {
                                            return $pr->items->sum('subtotal');
                                        }
                                        return $pr->nominal ?? 0;
                                    });
                                    // Ambil ringkasan barang dari purchaseros (gabung nama barang unik)
                                    $namaBarang = $d->purchaseros->flatMap(function($pr) {
                                        if ($pr->items && $pr->items->isNotEmpty()) {
                                            return $pr->items->pluck('nama_barang');
                                        }
                                        return collect([$pr->barang_jasa]);
                                    })->filter()->unique()->take(2)->implode(', ');
                                    $jumlahTotal = $d->purchaseros->flatMap(function($pr) {
                                        if ($pr->items && $pr->items->isNotEmpty()) {
                                            return $pr->items->pluck('qty');
                                        }
                                        return collect([$pr->qty ?? 0]);
                                    })->sum();
                                    // Harga rata-rata satuan dari semua item
                                    $allItems = $d->purchaseros->flatMap(function($pr) {
                                        if ($pr->items && $pr->items->isNotEmpty()) {
                                            return $pr->items;
                                        }
                                        return collect([]);
                                    });
                                    $hargaRata = $allItems->isNotEmpty() ? $allItems->avg('harga_satuan') : 0;
                                @endphp

                                <td class="px-4 py-3.5 text-sm text-gray-700">
                                    {{ $namaBarang ?: '-' }}
                                    @if($totalPr > 1)
                                        <span class="text-[10px] text-gray-400 ml-1">(+{{ $totalPr - 1 }} PR)</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">
                                    {{ $jumlahTotal > 0 ? $jumlahTotal : '-' }}
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">
                                    {{ $hargaRata > 0 ? 'Rp ' . number_format($hargaRata, 0, ',', '.') : '-' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    @if($totalNominal > 0)
                                        <span class="text-sm font-bold text-green-600">Rp {{ number_format($totalNominal, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                            data-id="{{ $d->id }}" data-user_id="{{ $d->user_id }}"
                                            data-nama_supplier="{{ $d->nama_supplier }}"
                                            data-no_telp="{{ $d->no_telp }}"
                                            data-alamat="{{ $d->alamat }}"
                                            data-nama_marketing="{{ $d->nama_marketing }}"
                                            data-kontak_marketing="{{ $d->kontak_marketing }}"
                                            onclick="triggerEdit(this)">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="/admin/supplier/{{ $d->id }}" method="POST"
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
                                <td colspan="11" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-people-fill text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data supplier</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Supplier" untuk menambahkan data baru
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100"><x-pagination :paginator="$data" /></div>
            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH / EDIT SUPPLIER
====================================== --}}
    <div id="supplierModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Supplier</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data supplier dengan lengkap</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="supplierForm" action="/admin/supplier" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div id="methodContainer"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Supplier <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_supplier" id="f_nama_supplier" required
                            placeholder="Contoh: CV Maju Jaya"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nama_supplier') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Telp <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="no_telp" id="f_no_telp" required placeholder="08xx-xxxx-xxxx"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('no_telp') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Marketing</label>
                        <input type="text" name="nama_marketing" id="f_nama_marketing"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nama_marketing') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Marketing</label>
                        <input type="number" name="kontak_marketing" id="f_kontak_marketing"
                            placeholder="08xx-xxxx-xxxx"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('kontak_marketing') }}">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                    <textarea name="alamat" id="f_alamat" rows="3"
                        placeholder="Contoh: Jl. Merdeka No. 10, Jakarta"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('alamat') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan Data
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
                        @if (session('error'))
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                        @else
                            <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
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
    </style>

    <script>
        // -- SUPPLIER MODAL --------------------------------------
        const supplierModal = document.getElementById('supplierModal');
        const supplierForm = document.getElementById('supplierForm');
        const methodContainer = document.getElementById('methodContainer');

        function openModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Supplier';
            supplierForm.action = '/admin/supplier';
            methodContainer.innerHTML = '';
            supplierForm.reset();
            supplierModal.classList.remove('hidden');
            supplierModal.classList.add('flex');
        }

        function closeModal() {
            supplierModal.classList.add('hidden');
            supplierModal.classList.remove('flex');
        }

        supplierModal.addEventListener('click', function(e) {
            if (e.target === supplierModal) closeModal();
        });

        function triggerEdit(btn) {
            document.getElementById('modalTitle').innerText = 'Edit Supplier';
            supplierForm.action = '/admin/supplier/' + btn.dataset.id;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('f_nama_supplier').value = btn.dataset.nama_supplier;
            document.getElementById('f_no_telp').value = btn.dataset.no_telp;
            document.getElementById('f_alamat').value = btn.dataset.alamat ?? '';
            document.getElementById('f_nama_marketing').value = btn.dataset.nama_marketing ?? '';
            document.getElementById('f_kontak_marketing').value = btn.dataset.kontak_marketing ?? '';
            supplierModal.classList.remove('hidden');
            supplierModal.classList.add('flex');
        }

        // -- POPUP ALERT (fixed overlay) --------------------
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
    
        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') openModalTambah();
            else if (typeof openModal === 'function') openModal();
        });
        @endif
</script>

<script>
// ========================================
// CHART INITIALIZATION — SUPPLIER
// ========================================
const supplierChartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    initSupplierCharts({ filter_type: 'year' });

    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'supplierChartFilter') {
            updateSupplierCharts({
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            });
        }
    });
});

async function initSupplierCharts(filters) {
    try {
        const data = await fetch(`/admin/chart-data/supplier?filter_type=${filters.filter_type || 'year'}${filters.start_date ? '&start_date='+filters.start_date : ''}${filters.end_date ? '&end_date='+filters.end_date : ''}`).then(r => r.json());

        if (!data.success) throw new Error(data.message || 'Failed');

        const chartData = data.data;

        // Pie chart — standar
        supplierChartManager.initPieChart('supplierPieChart', chartData.pie);

        // Bar chart — dual axis: kiri untuk count/jumlah, kanan untuk nominal (Rp)
        if (chartData.bar && chartData.bar.datasets) {
            const barDatasets = chartData.bar.datasets.map((ds, i) => {
                const colors = ['#4f6ef7', '#10b981', '#f97316'];
                const bg     = colors[i] || colors[0];
                // Dataset index 1 = Total Nominal → pakai axis kanan (y1)
                return {
                    ...ds,
                    backgroundColor : bg + 'cc',
                    borderColor     : bg,
                    borderWidth     : 1,
                    borderRadius    : 4,
                    yAxisID         : i === 1 ? 'y1' : 'y',
                };
            });

            // Overlay trend line: duplikasi dataset Jumlah Barang (index 2) sebagai line merah
            const jumlahBarangDs = chartData.bar.datasets[2];
            if (jumlahBarangDs) {
                barDatasets.push({
                    type                 : 'line',
                    label                : 'Jumlah Barang (trend)',
                    data                 : [...jumlahBarangDs.data],
                    borderColor          : '#ef4444',
                    backgroundColor      : 'transparent',
                    borderWidth          : 2.5,
                    pointRadius          : 3,
                    pointBackgroundColor : '#ef4444',
                    pointBorderColor     : '#fff',
                    pointBorderWidth     : 1.5,
                    tension              : 0.4,
                    fill                 : false,
                    yAxisID              : 'y',
                    order                : 0,
                });
            }

            supplierChartManager.destroyChart('supplierBarChart');
            const ctx = document.getElementById('supplierBarChart');
            if (ctx) {
                supplierChartManager.charts['supplierBarChart'] = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: chartData.bar.labels, datasets: barDatasets },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                title: { display: true, text: 'Jumlah', font: { size: 11 } },
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: { font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" } }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                title: { display: true, text: 'Nominal (Rp)', font: { size: 11 } },
                                grid: { drawOnChartArea: false },
                                ticks: {
                                    font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
                                    callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+' JT' : v >= 1000 ? (v/1000).toFixed(0)+' RB' : v
                                }
                            },
                            x: { grid: { display: false }, ticks: { font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" } } }
                        },
                        plugins: {
                            legend: { display: true, position: 'top', labels: { usePointStyle: true, font: { size: 12 } } },
                            tooltip: {
                                backgroundColor: 'rgba(15,17,23,0.95)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: ctx => {
                                        const v = ctx.parsed.y;
                                        const label = ctx.dataset.label || '';
                                        if (ctx.datasetIndex === 1) return ` ${label}: Rp ${new Intl.NumberFormat('id-ID').format(v)}`;
                                        return ` ${label}: ${new Intl.NumberFormat('id-ID').format(v)}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Line chart — trend nominal
        supplierChartManager.initLineChart('supplierLineChart', chartData.line);

        // Stats
        if (chartData.stats) {
            const container = document.querySelector('#supplierChartContainer [class*="chart-stat"]')?.closest('.grid');
            // stats dihandle oleh ChartManager update
        }

    } catch (e) { console.error('Error loading supplier charts:', e); }
}

async function updateSupplierCharts(filters) {
    await initSupplierCharts(filters);
}

// Auto-submit search form with debounce
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.form.submit();
            }, 500); // Submit after 500ms of no typing
        });
    }
});
</script>

@endsection
