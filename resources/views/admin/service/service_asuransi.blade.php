@extends('admin.layouts.app')

@section('title', 'Service Asuransi')

@section('content')

    @php
        $jumlahService = $data->count();
        $totalBiaya = $data->sum('biaya');
        $jumlahBermasalah = $data->where('status', 'bermasalah')->count();
        $jumlahSelesai    = $data->where('status', 'selesai')->count();
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Service Asuransi</h1>
                <p class="text-sm text-gray-500 mt-0.5">Data Service & Asuransi Kendaraan</p>
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
                        ['label' => 'Service Asuransi', 'url' => '/admin/service-asuransi','icon' => 'bi bi-shield-fill-check'],
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
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-shield-halved text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Service Asuransi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $jumlahService }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Bermasalah</p>
                    <p class="text-2xl font-bold text-red-600">{{ $jumlahBermasalah }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $jumlahSelesai }}</p>
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
                    <h2 class="font-semibold text-gray-800">Service Asuransi</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $data->count() }} data</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="month" id="filterBulan" onchange="setActiveBulan(this.value)"
                        class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" id="searchInput" placeholder="Cari kendaraan..."
                            oninput="applyFilters()"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Kendaraan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Keluhan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">KM</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Periode</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Attachment</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody">
                        @forelse($data as $d)
                            @php
                                    $rawBukti = is_array($d->bukti) ? $d->bukti : (json_decode($d->bukti, true) ?? []);
                                    // Normalise: pastikan setiap item punya key path & name
                                    $buktiArr = array_values(array_filter(array_map(function($f) {
                                        if (is_array($f)) {
                                            return ['path' => $f['path'] ?? '', 'name' => $f['name'] ?? basename($f['path'] ?? '')];
                                        }
                                        // format lama: plain string path
                                        return ['path' => $f, 'name' => basename($f)];
                                    }, $rawBukti), fn($f) => !empty($f['path'])));
                                    $buktiCount = count($buktiArr);
                                    $buktiJson = json_encode($buktiArr);

                                    $rawAttachment = is_array($d->attachment) ? $d->attachment : (json_decode($d->attachment, true) ?? []);
                                    $attachmentArr = array_values(array_filter(array_map(function($f) {
                                        if (is_array($f)) {
                                            return ['path' => $f['path'] ?? '', 'name' => $f['name'] ?? basename($f['path'] ?? '')];
                                        }
                                        return ['path' => $f, 'name' => basename($f)];
                                    }, $rawAttachment), fn($f) => !empty($f['path'])));
                                    $attachmentCount = count($attachmentArr);
                                    $attachmentJson = json_encode($attachmentArr);
                                @endphp
                            <tr class="border-t border-gray-100 odd:bg-white even:bg-gray-50 hover:bg-blue-50/40 transition-colors duration-100"
                                data-search="{{ strtolower(($d->kendaraan->nopol ?? '') . ' ' . ($d->kendaraan->merk ?? '') . ' ' . $d->keterangan) }}"
                                data-bulan="{{ $d->tanggal_service ? \Carbon\Carbon::parse($d->tanggal_service)->format('Y-m-d') : '' }}">

                                {{-- KENDARAAN --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fa fa-car text-blue-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $d->kendaraan->merk ?? '-' }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $d->kendaraan->nopol ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

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

                                    {{-- PERIODE --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        @if($d->periode_mulai && $d->periode_selesai)
                                            <div class="flex flex-col gap-0.5">
                                                <span class="text-xs text-gray-400">Mulai:</span>
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($d->periode_mulai)->format('d M Y') }}</span>
                                                <span class="text-xs text-gray-400 mt-1">Selesai:</span>
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($d->periode_selesai)->format('d M Y') }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>

                                    {{-- BIAYA --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-800">
                                            Rp {{ number_format($d->biaya ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php $st = $d->status ?? 'bermasalah'; @endphp
                                        <button type="button"
                                            onclick="openStatusModal({{ $d->id }}, '{{ $st }}', '{{ addslashes(($d->kendaraan->merk ?? '') . ' - ' . ($d->kendaraan->nopol ?? '')) }}')"
                                            title="Klik untuk ubah status"
                                            class="status-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border cursor-pointer transition-all hover:shadow-sm
                                                {{ $st === 'selesai'
                                                    ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'
                                                    : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $st === 'selesai' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            {{ $st === 'selesai' ? 'Selesai' : 'Bermasalah' }}
                                        </button>
                                    </td>

                                    {{-- BUKTI --}}
                                    <td class="px-5 py-4">
                                        @if ($buktiCount > 0)
                                            <div class="space-y-1">
                                                @foreach ($buktiArr as $i => $buktiItem)
                                                    @php
                                                        $bPath = $buktiItem['path'] ?? '';
                                                        $bName = $buktiItem['name'] ?? basename($bPath);
                                                        $bExt  = strtolower(pathinfo($bPath, PATHINFO_EXTENSION));
                                                        $bIsImg = in_array($bExt, ['jpg','jpeg','png','webp']);
                                                    @endphp
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-gray-200 text-gray-500 text-[9px] font-bold flex-shrink-0">
                                                            {{ $i + 1 }}
                                                        </span>
                                                        <a href="{{ asset($bPath) }}" target="_blank"
                                                            class="text-xs text-blue-600 hover:underline truncate max-w-[140px]"
                                                            title="{{ $bName }}">
                                                            <i class="fa {{ $bIsImg ? 'fa-image' : ($bExt === 'pdf' ? 'fa-file-pdf' : 'fa-file-word') }} text-[10px] mr-0.5"></i>{{ $bName }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button"
                                                onclick="openDetailBukti({{ $d->id }}, {{ $buktiJson }}, '{{ addslashes($d->kendaraan->merk ?? '') }} - {{ addslashes($d->kendaraan->nopol ?? '') }}')"
                                                class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-500 hover:bg-blue-100 border border-blue-200 transition-colors">
                                                <i class="fa fa-expand-alt text-[9px]"></i> Lihat Semua
                                            </button>
                                        @else
                                            <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>

                                    {{-- ATTACHMENT --}}
                                    <td class="px-5 py-4">
                                        @if ($attachmentCount > 0)
                                            <div class="space-y-1">
                                                @foreach ($attachmentArr as $i => $attItem)
                                                    @php
                                                        $aPath = $attItem['path'] ?? '';
                                                        $aName = $attItem['name'] ?? basename($aPath);
                                                        $aExt  = strtolower(pathinfo($aPath, PATHINFO_EXTENSION));
                                                        $aIsImg = in_array($aExt, ['jpg','jpeg','png','webp']);
                                                    @endphp
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-purple-100 text-purple-500 text-[9px] font-bold flex-shrink-0">
                                                            {{ $i + 1 }}
                                                        </span>
                                                        <a href="{{ asset($aPath) }}" target="_blank"
                                                            class="text-xs text-purple-600 hover:underline truncate max-w-[140px]"
                                                            title="{{ $aName }}">
                                                            <i class="fa {{ $aIsImg ? 'fa-image' : ($aExt === 'pdf' ? 'fa-file-pdf' : 'fa-file-word') }} text-[10px] mr-0.5"></i>{{ $aName }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button"
                                                onclick="openDetailAttachment({{ $d->id }}, {{ $attachmentJson }}, '{{ addslashes($d->kendaraan->merk ?? '') }} - {{ addslashes($d->kendaraan->nopol ?? '') }}')"
                                                class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-500 hover:bg-purple-100 border border-purple-200 transition-colors">
                                                <i class="fa fa-expand-alt text-[9px]"></i> Lihat Semua
                                            </button>
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
                                                data-periode_mulai="{{ $d->periode_mulai }}"
                                                data-periode_selesai="{{ $d->periode_selesai }}"
                                                data-kilometer="{{ $d->kilometer }}"
                                                data-biaya="{{ $d->biaya }}"
                                                data-keterangan="{{ $d->keterangan }}"
                                                data-bukti="{{ $buktiJson }}"
                                                data-attachment="{{ $attachmentJson }}">
                                                <i class="fa fa-edit text-xs"></i> Edit
                                            </button>

                                            <form action="{{ route('service-asuransi.destroy', $d->id) }}" method="POST"
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
                                <td colspan="10" class="px-5 py-12 text-center">
                                    <p class="text-sm text-gray-500">Belum ada data service asuransi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-3 border-t border-gray-100"><x-pagination :paginator="$data" /></div>
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

            <div id="tableFooter" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                <p id="showingInfo" class="text-xs text-gray-500"></p>
                <div id="paginationControls" class="flex items-center gap-1"></div>
            </div>

        </div>
    </div>


    {{-- ============================================================
    MODAL TAMBAH / EDIT
    ============================================================ --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 overflow-y-auto py-6"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 flex flex-col">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 shrink-0">
                <div>
                    <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Data Service Asuransi</h2>
                    <p id="modalDesc" class="text-xs text-gray-500 mt-0.5">Isi data service & asuransi kendaraan</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="form" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 space-y-4 overflow-y-auto max-h-[80vh]">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                    <select name="kendaraan_id" id="kendaraan_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}">{{ $k->merk }} - {{ $k->nopol }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Service</label>
                    <input type="date" name="tanggal_service" id="tanggal_service"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Periode Mulai</label>
                        <input type="date" name="periode_mulai" id="periode_mulai"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Periode Selesai</label>
                        <input type="date" name="periode_selesai" id="periode_selesai"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kilometer</label>
                    <input type="number" name="kilometer" id="kilometer" placeholder="Contoh: 45000"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keluhan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" placeholder="Keluhan mobil"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya</label>
                    <input type="number" name="biaya" id="biaya" max="9999999999" placeholder="Nominal biaya service"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                {{-- UPLOAD BUKTI MULTIPLE --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Bukti Upload
                        <span class="text-gray-400 font-normal">(bisa pilih banyak file)</span>
                    </label>

                    {{-- Daftar file existing saat edit --}}
                    <div id="existingBuktiWrap" class="hidden mb-3">
                        <p class="text-xs text-gray-500 mb-2 font-medium">File tersimpan:</p>
                        <div id="existingBuktiList" class="space-y-1.5"></div>
                    </div>

                    {{-- Preview file baru yang dipilih --}}
                    <div id="newBuktiPreview" class="hidden mb-3 space-y-1.5"></div>

                    {{-- Upload area --}}
                    <label for="bukti"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-1"></i>
                        <span class="text-xs text-slate-500">Klik untuk upload / tambah file</span>
                        <span class="text-[11px] text-slate-400">JPG, PNG, PDF, DOC, DOCX (maks 5MB/file)</span>
                    </label>
                    <input type="file" name="bukti[]" id="bukti" class="hidden" multiple
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" onchange="previewNewBukti(this)">
                </div>

                {{-- UPLOAD ATTACHMENT MULTIPLE --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Attachment
                        <span class="text-gray-400 font-normal">(dokumen administratif: surat bengkel, invoice, dll)</span>
                    </label>

                    {{-- Daftar file existing saat edit --}}
                    <div id="existingAttachmentWrap" class="hidden mb-3">
                        <p class="text-xs text-gray-500 mb-2 font-medium">File tersimpan:</p>
                        <div id="existingAttachmentList" class="space-y-1.5"></div>
                    </div>

                    {{-- Preview file baru yang dipilih --}}
                    <div id="newAttachmentPreview" class="hidden mb-3 space-y-1.5"></div>

                    {{-- Upload area --}}
                    <label for="attachment"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-purple-300 rounded-xl cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition">
                        <i class="fa-solid fa-paperclip text-2xl text-purple-400 mb-1"></i>
                        <span class="text-xs text-slate-500">Klik untuk upload / tambah attachment</span>
                        <span class="text-[11px] text-slate-400">JPG, PNG, PDF, DOC, DOCX (maks 5MB/file)</span>
                    </label>
                    <input type="file" name="attachment[]" id="attachment" class="hidden" multiple
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" onchange="previewNewAttachment(this)">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl">
                    <i class="fa fa-save text-sm"></i> Simpan
                </button>
            </form>
        </div>
    </div>


    {{-- ============================================================
    MODAL DETAIL BUKTI
    ============================================================ --}}
    <div id="modalDetailBukti" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Detail Bukti</h2>
                    <p id="detailBuktiSubtitle" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="closeDetailBukti()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-xl leading-none">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div id="detailBuktiContent" class="overflow-y-auto p-6 flex-1">
                {{-- Diisi via JS --}}
            </div>

        </div>
    </div>

    {{-- ============================================================
    MODAL DETAIL ATTACHMENT
    ============================================================ --}}
    <div id="modalDetailAttachment" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Detail Attachment</h2>
                    <p id="detailAttachmentSubtitle" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="closeDetailAttachment()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-xl leading-none">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div id="detailAttachmentContent" class="overflow-y-auto p-6 flex-1">
                {{-- Diisi via JS --}}
            </div>

        </div>
    </div>

    {{-- Lightbox overlay untuk zoom gambar --}}
    <div id="lightbox" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/80"
        onclick="closeLightbox()">
        <img id="lightboxImg" src="" alt="" class="max-w-[90vw] max-h-[90vh] rounded-xl shadow-2xl object-contain">
        <button onclick="closeLightbox()"
            class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center text-lg transition-colors">
            <i class="fa fa-times"></i>
        </button>
    </div>


    {{-- ============================================================
    POPUP ALERT
    ============================================================ --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">
                @if (session('success'))
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
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
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239ca3af'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 24px !important;
        }
    </style>

    <script>
    // =================================================================
    // MODAL TAMBAH / EDIT
    // =================================================================
    const modal = document.getElementById('modal');

    function setModalMode(mode) {
        document.getElementById('modalTitle').textContent = mode === 'edit' ? 'Edit Data Service Asuransi' : 'Tambah Data Service Asuransi';
        document.getElementById('modalDesc').textContent  = mode === 'edit' ? 'Ubah data service & asuransi kendaraan' : 'Isi data service & asuransi kendaraan';
    }

    function openModal() {
        setModalMode('add');
        document.getElementById('form').reset();
        document.getElementById('form').action = '/admin/service-asuransi';
        const mp = document.getElementById('method-put');
        if (mp) mp.remove();
        clearBuktiPreview();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    // -- EDIT BUTTON --------------------------------------------------
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit');
        if (!btn) return;

        setModalMode('edit');

        const form = document.getElementById('form');
        form.action = '/admin/service-asuransi/' + btn.dataset.id;

        const existingMp = document.getElementById('method-put');
        if (existingMp) existingMp.remove();

        const input = document.createElement('input');
        input.type = 'hidden'; input.name = '_method'; input.value = 'PUT'; input.id = 'method-put';
        form.appendChild(input);

        document.getElementById('biaya').value          = btn.dataset.biaya;
        document.getElementById('keterangan').value     = btn.dataset.keterangan;
        document.getElementById('kendaraan_id').value   = btn.dataset.kendaraan_id;
        document.getElementById('tanggal_service').value = btn.dataset.tanggal_service;
        document.getElementById('periode_mulai').value  = btn.dataset.periode_mulai || '';
        document.getElementById('periode_selesai').value = btn.dataset.periode_selesai || '';
        document.getElementById('kilometer').value      = btn.dataset.kilometer;

        // Tampilkan file yang sudah tersimpan
        clearBuktiPreview();
        clearAttachmentPreview();
        try {
            const buktiArr = JSON.parse(btn.dataset.bukti || '[]');
            if (Array.isArray(buktiArr) && buktiArr.length > 0) {
                renderExistingBukti(buktiArr, btn.dataset.id);
            }
        } catch(err) {}

        try {
            const attachmentArr = JSON.parse(btn.dataset.attachment || '[]');
            if (Array.isArray(attachmentArr) && attachmentArr.length > 0) {
                renderExistingAttachment(attachmentArr, btn.dataset.id);
            }
        } catch(err) {}

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });


    // =================================================================
    // BUKTI FILE HELPERS
    // =================================================================
    function clearBuktiPreview() {
        const wrap = document.getElementById('existingBuktiWrap');
        const list = document.getElementById('existingBuktiList');
        const prev = document.getElementById('newBuktiPreview');
        if (wrap) { wrap.classList.add('hidden'); }
        if (list) { list.innerHTML = ''; }
        if (prev) { prev.innerHTML = ''; prev.classList.add('hidden'); }
        const inp = document.getElementById('bukti');
        if (inp) inp.value = '';
    }

    function renderExistingBukti(arr, recordId) {
        const wrap = document.getElementById('existingBuktiWrap');
        const list = document.getElementById('existingBuktiList');
        list.innerHTML = '';

        arr.forEach(function(item) {
            // support both {path,name} and plain string (old format)
            const path     = (typeof item === 'object') ? item.path : item;
            const origName = (typeof item === 'object' && item.name) ? item.name : path.split('/').pop();
            if (!path) return;

            const ext      = path.split('.').pop().toLowerCase();
            const isImage  = ['jpg','jpeg','png','webp'].includes(ext);
            const isPdf    = ext === 'pdf';
            const isDoc    = ['doc','docx'].includes(ext);

            let iconHtml = '';
            let colorClass = 'bg-gray-50 border-gray-200 text-gray-600';
            if (isImage)    { iconHtml = `<img src="/${path}" class="w-8 h-8 object-cover rounded flex-shrink-0">`; colorClass = 'bg-blue-50 border-blue-200 text-blue-700'; }
            else if (isPdf) { iconHtml = `<i class="fa fa-file-pdf text-red-500 text-xl flex-shrink-0"></i>`;  colorClass = 'bg-red-50 border-red-200 text-red-600'; }
            else if (isDoc) { iconHtml = `<i class="fa fa-file-word text-blue-500 text-xl flex-shrink-0"></i>`; colorClass = 'bg-blue-50 border-blue-200 text-blue-600'; }
            else            { iconHtml = `<i class="fa fa-file text-gray-400 text-xl flex-shrink-0"></i>`; }

            const item2 = document.createElement('div');
            item2.className = `flex items-center justify-between gap-3 px-3 py-2 border rounded-xl ${colorClass}`;
            item2.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0">
                    ${iconHtml}
                    <a href="/${path}" target="_blank" class="text-xs font-medium truncate hover:underline" title="${origName}">${origName}</a>
                </div>
                <button type="button" title="Hapus file ini"
                    onclick="deleteSingleBukti('${path}', ${recordId}, this.closest('div[class*=flex]'))"
                    class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 hover:bg-red-200 text-red-500 flex items-center justify-center transition-colors">
                    <i class="fa fa-times text-[10px]"></i>
                </button>`;
            list.appendChild(item2);
        });

        wrap.classList.remove('hidden');
    }

    function deleteSingleBukti(filePath, recordId, rowEl) {
        if (!confirm('Hapus file ini?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/service-asuransi/' + recordId + '/bukti';
        form.innerHTML =
            '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
            '<input type="hidden" name="_method" value="DELETE">' +
            '<input type="hidden" name="file_path" value="' + filePath + '">';
        document.body.appendChild(form);
        form.submit();
    }

    function previewNewBukti(input) {
        const prev = document.getElementById('newBuktiPreview');
        prev.innerHTML = '';

        const files = Array.from(input.files);
        if (!files.length) { prev.classList.add('hidden'); return; }

        files.forEach(function(file) {
            const ext     = file.name.split('.').pop().toLowerCase();
            const isImage = ['jpg','jpeg','png','webp'].includes(ext);
            const url     = URL.createObjectURL(file);

            let iconHtml   = '';
            let colorClass = 'bg-gray-50 border-gray-200 text-gray-600';

            if (isImage) {
                iconHtml   = `<img src="${url}" class="w-8 h-8 object-cover rounded">`;
                colorClass = 'bg-blue-50 border-blue-200 text-blue-700';
            } else if (ext === 'pdf') {
                iconHtml   = `<i class="fa fa-file-pdf text-red-500 text-xl"></i>`;
                colorClass = 'bg-red-50 border-red-200 text-red-600';
            } else {
                iconHtml   = `<i class="fa fa-file-word text-blue-500 text-xl"></i>`;
                colorClass = 'bg-blue-50 border-blue-200 text-blue-600';
            }

            const item = document.createElement('div');
            item.className = `flex items-center gap-2.5 px-3 py-2 border rounded-xl ${colorClass}`;
            item.innerHTML = `${iconHtml}<span class="text-xs font-medium truncate">${file.name}</span>`;
            prev.appendChild(item);
        });

        prev.classList.remove('hidden');
    }


    // =================================================================
    // ATTACHMENT FILE HELPERS
    // =================================================================
    function clearAttachmentPreview() {
        const wrap = document.getElementById('existingAttachmentWrap');
        const list = document.getElementById('existingAttachmentList');
        const prev = document.getElementById('newAttachmentPreview');
        if (wrap) { wrap.classList.add('hidden'); }
        if (list) { list.innerHTML = ''; }
        if (prev) { prev.innerHTML = ''; prev.classList.add('hidden'); }
        const inp = document.getElementById('attachment');
        if (inp) inp.value = '';
    }

    function renderExistingAttachment(arr, recordId) {
        const wrap = document.getElementById('existingAttachmentWrap');
        const list = document.getElementById('existingAttachmentList');
        list.innerHTML = '';

        arr.forEach(function(item) {
            const path     = (typeof item === 'object') ? item.path : item;
            const origName = (typeof item === 'object' && item.name) ? item.name : path.split('/').pop();
            if (!path) return;

            const ext      = path.split('.').pop().toLowerCase();
            const isImage  = ['jpg','jpeg','png','webp'].includes(ext);
            const isPdf    = ext === 'pdf';
            const isDoc    = ['doc','docx'].includes(ext);

            let iconHtml = '';
            let colorClass = 'bg-purple-50 border-purple-200 text-purple-700';
            if (isImage)    { iconHtml = `<img src="/${path}" class="w-8 h-8 object-cover rounded flex-shrink-0">`; }
            else if (isPdf) { iconHtml = `<i class="fa fa-file-pdf text-red-500 text-xl flex-shrink-0"></i>`;  colorClass = 'bg-red-50 border-red-200 text-red-600'; }
            else if (isDoc) { iconHtml = `<i class="fa fa-file-word text-blue-500 text-xl flex-shrink-0"></i>`; colorClass = 'bg-blue-50 border-blue-200 text-blue-600'; }
            else            { iconHtml = `<i class="fa fa-file text-gray-400 text-xl flex-shrink-0"></i>`; colorClass = 'bg-gray-50 border-gray-200 text-gray-600'; }

            const item2 = document.createElement('div');
            item2.className = `flex items-center justify-between gap-3 px-3 py-2 border rounded-xl ${colorClass}`;
            item2.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0">
                    ${iconHtml}
                    <a href="/${path}" target="_blank" class="text-xs font-medium truncate hover:underline" title="${origName}">${origName}</a>
                </div>
                <button type="button" title="Hapus file ini"
                    onclick="deleteSingleAttachment('${path}', ${recordId}, this.closest('div[class*=flex]'))"
                    class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 hover:bg-red-200 text-red-500 flex items-center justify-center transition-colors">
                    <i class="fa fa-times text-[10px]"></i>
                </button>`;
            list.appendChild(item2);
        });

        wrap.classList.remove('hidden');
    }

    function deleteSingleAttachment(filePath, recordId, rowEl) {
        if (!confirm('Hapus file ini?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/service-asuransi/' + recordId + '/attachment';
        form.innerHTML =
            '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
            '<input type="hidden" name="_method" value="DELETE">' +
            '<input type="hidden" name="file_path" value="' + filePath + '">';
        document.body.appendChild(form);
        form.submit();
    }

    function previewNewAttachment(input) {
        const prev = document.getElementById('newAttachmentPreview');
        prev.innerHTML = '';

        const files = Array.from(input.files);
        if (!files.length) { prev.classList.add('hidden'); return; }

        files.forEach(function(file) {
            const ext     = file.name.split('.').pop().toLowerCase();
            const isImage = ['jpg','jpeg','png','webp'].includes(ext);
            const url     = URL.createObjectURL(file);

            let iconHtml   = '';
            let colorClass = 'bg-purple-50 border-purple-200 text-purple-700';

            if (isImage) {
                iconHtml   = `<img src="${url}" class="w-8 h-8 object-cover rounded">`;
            } else if (ext === 'pdf') {
                iconHtml   = `<i class="fa fa-file-pdf text-red-500 text-xl"></i>`;
                colorClass = 'bg-red-50 border-red-200 text-red-600';
            } else {
                iconHtml   = `<i class="fa fa-file-word text-blue-500 text-xl"></i>`;
                colorClass = 'bg-blue-50 border-blue-200 text-blue-600';
            }

            const item = document.createElement('div');
            item.className = `flex items-center gap-2.5 px-3 py-2 border rounded-xl ${colorClass}`;
            item.innerHTML = `${iconHtml}<span class="text-xs font-medium truncate">${file.name}</span>`;
            prev.appendChild(item);
        });

        prev.classList.remove('hidden');
    }


    // =================================================================
    // MODAL DETAIL ATTACHMENT
    // =================================================================
    function openDetailAttachment(recordId, attachmentArr, label) {
        document.getElementById('detailAttachmentSubtitle').textContent = label || '';

        const content = document.getElementById('detailAttachmentContent');
        content.innerHTML = '';

        if (!Array.isArray(attachmentArr) || attachmentArr.length === 0) {
            content.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Tidak ada file attachment</p>';
        } else {
            const files = attachmentArr.map(function(f) {
                const path     = (typeof f === 'object') ? (f.path || '') : f;
                const origName = (typeof f === 'object' && f.name) ? f.name : path.split('/').pop();
                return { path, name: origName };
            }).filter(f => f.path);

            const images = files.filter(f => /\.(jpg|jpeg|png|webp)$/i.test(f.path));
            const docs   = files.filter(f => !/\.(jpg|jpeg|png|webp)$/i.test(f.path));

            if (images.length > 0) {
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4';

                images.forEach(function(f) {
                    const wrap = document.createElement('div');
                    wrap.className = 'group relative rounded-xl overflow-hidden border border-purple-100 bg-purple-50 cursor-pointer';
                    wrap.onclick   = function() { openLightbox('/' + f.path); };
                    wrap.innerHTML = `
                        <img src="/${f.path}" alt="${f.name}"
                            class="w-full h-36 object-cover transition-transform duration-200 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <i class="fa fa-expand text-white opacity-0 group-hover:opacity-100 text-xl transition-opacity drop-shadow"></i>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 px-2 py-1.5 bg-gradient-to-t from-black/60 to-transparent">
                            <p class="text-[10px] text-white/90 truncate font-medium" title="${f.name}">${f.name}</p>
                            <a href="/${f.path}" download="${f.name}" onclick="event.stopPropagation()"
                                class="text-[10px] text-white/60 hover:text-white flex items-center gap-1 mt-0.5">
                                <i class="fa fa-download text-[9px]"></i> Unduh
                            </a>
                        </div>`;
                    grid.appendChild(wrap);
                });

                content.appendChild(grid);
            }

            if (docs.length > 0) {
                if (images.length > 0) {
                    const hr = document.createElement('hr');
                    hr.className = 'border-gray-100 mb-4';
                    content.appendChild(hr);
                }

                const docList = document.createElement('div');
                docList.className = 'space-y-2';

                docs.forEach(function(f) {
                    const ext   = f.path.split('.').pop().toLowerCase();
                    const isPdf = ext === 'pdf';

                    const item = document.createElement('a');
                    item.href   = '/' + f.path;
                    item.target = '_blank';
                    item.className = `flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors hover:bg-gray-50 ${isPdf ? 'border-red-100 bg-red-50/40 text-red-600' : 'border-purple-100 bg-purple-50/40 text-purple-600'}`;
                    item.innerHTML = `
                        <i class="fa ${isPdf ? 'fa-file-pdf' : 'fa-file-word'} text-xl flex-shrink-0"></i>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold truncate" title="${f.name}">${f.name}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Klik untuk buka</p>
                        </div>
                        <i class="fa fa-external-link-alt text-xs text-gray-400 flex-shrink-0"></i>`;
                    docList.appendChild(item);
                });

                content.appendChild(docList);
            }
        }

        const detailModal = document.getElementById('modalDetailAttachment');
        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');
    }

    function closeDetailAttachment() {
        const m = document.getElementById('modalDetailAttachment');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('modalDetailAttachment').addEventListener('click', function(e) {
        if (e.target === this) closeDetailAttachment();
    });


    // =================================================================
    // MODAL DETAIL BUKTI
    // =================================================================
    function openDetailBukti(recordId, buktiArr, label) {
        document.getElementById('detailBuktiSubtitle').textContent = label || '';

        const content = document.getElementById('detailBuktiContent');
        content.innerHTML = '';

        if (!Array.isArray(buktiArr) || buktiArr.length === 0) {
            content.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Tidak ada file bukti</p>';
        } else {
            // Normalise ke {path, name}
            const files = buktiArr.map(function(f) {
                const path     = (typeof f === 'object') ? (f.path || '') : f;
                const origName = (typeof f === 'object' && f.name) ? f.name : path.split('/').pop();
                return { path, name: origName };
            }).filter(f => f.path);

            const images = files.filter(f => /\.(jpg|jpeg|png|webp)$/i.test(f.path));
            const docs   = files.filter(f => !/\.(jpg|jpeg|png|webp)$/i.test(f.path));

            // --- GRID GAMBAR ---
            if (images.length > 0) {
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4';

                images.forEach(function(f) {
                    const wrap = document.createElement('div');
                    wrap.className = 'group relative rounded-xl overflow-hidden border border-gray-100 bg-gray-50 cursor-pointer';
                    wrap.onclick   = function() { openLightbox('/' + f.path); };
                    wrap.innerHTML = `
                        <img src="/${f.path}" alt="${f.name}"
                            class="w-full h-36 object-cover transition-transform duration-200 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <i class="fa fa-expand text-white opacity-0 group-hover:opacity-100 text-xl transition-opacity drop-shadow"></i>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 px-2 py-1.5 bg-gradient-to-t from-black/60 to-transparent">
                            <p class="text-[10px] text-white/90 truncate font-medium" title="${f.name}">${f.name}</p>
                            <a href="/${f.path}" download="${f.name}" onclick="event.stopPropagation()"
                                class="text-[10px] text-white/60 hover:text-white flex items-center gap-1 mt-0.5">
                                <i class="fa fa-download text-[9px]"></i> Unduh
                            </a>
                        </div>`;
                    grid.appendChild(wrap);
                });

                content.appendChild(grid);
            }

            // --- LIST DOKUMEN ---
            if (docs.length > 0) {
                if (images.length > 0) {
                    const hr = document.createElement('hr');
                    hr.className = 'border-gray-100 mb-4';
                    content.appendChild(hr);
                }

                const docList = document.createElement('div');
                docList.className = 'space-y-2';

                docs.forEach(function(f) {
                    const ext   = f.path.split('.').pop().toLowerCase();
                    const isPdf = ext === 'pdf';

                    const item = document.createElement('a');
                    item.href   = '/' + f.path;
                    item.target = '_blank';
                    item.className = `flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors hover:bg-gray-50 ${isPdf ? 'border-red-100 bg-red-50/40 text-red-600' : 'border-blue-100 bg-blue-50/40 text-blue-600'}`;
                    item.innerHTML = `
                        <i class="fa ${isPdf ? 'fa-file-pdf' : 'fa-file-word'} text-xl flex-shrink-0"></i>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold truncate" title="${f.name}">${f.name}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Klik untuk buka</p>
                        </div>
                        <i class="fa fa-external-link-alt text-xs text-gray-400 flex-shrink-0"></i>`;
                    docList.appendChild(item);
                });

                content.appendChild(docList);
            }
        }

        const detailModal = document.getElementById('modalDetailBukti');
        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');
    }

    function closeDetailBukti() {
        const m = document.getElementById('modalDetailBukti');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('modalDetailBukti').addEventListener('click', function(e) {
        if (e.target === this) closeDetailBukti();
    });

    // =================================================================
    // LIGHTBOX
    // =================================================================
    function openLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        const lb = document.getElementById('lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    }

    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        document.getElementById('lightboxImg').src = '';
    }


    // =================================================================
    // FILTER + SEARCH (SIMPLIFIED - NO ACCORDION, NO STATUS FILTER)
    // =================================================================
    let activeBulan = 'semua';
    let currentPage = 1;

    function setActiveBulan(bulan) {
        activeBulan = (bulan && bulan.trim() !== '') ? bulan : 'semua';
        currentPage = 1;
        applyFilters();
    }

    function applyFilters() {
        const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
        const perPage = 15;

        const allRows = document.querySelectorAll('#serviceTableBody tr[data-search]');

        const matched = [];
        allRows.forEach(row => {
            const matchSearch = !keyword || row.dataset.search.includes(keyword);
            const tgl = row.dataset.bulan || '';
            const [rY, rM] = tgl.split('-');
            const rowYM = rY && rM ? `${rY}-${rM}` : '';
            const matchBulan = activeBulan === 'semua' || rowYM === activeBulan;
            if (matchSearch && matchBulan) matched.push(row);
        });

        const total = matched.length;
        const totalPages = Math.ceil(total / perPage) || 1;
        if (currentPage > totalPages) currentPage = 1;

        const start = (currentPage - 1) * perPage;
        const end = Math.min(start + perPage, total);
        const pageSet = new Set(matched.slice(start, end));

        allRows.forEach(row => {
            row.style.display = pageSet.has(row) ? '' : 'none';
        });

        const showingInfo = document.getElementById('showingInfo');
        if (showingInfo) {
            showingInfo.textContent = total === 0
                ? 'Tidak ada data yang ditampilkan'
                : `Menampilkan ${start + 1} sampai ${end} dari ${total} data`;
        }

        renderPagination(totalPages);
        document.getElementById('totalCount').textContent = total + ' total data';

        const noResult = document.getElementById('noResultRow');
        if (noResult) noResult.classList.toggle('hidden', total > 0 || allRows.length === 0);
    }

    function renderPagination(totalPages) {
        const container = document.getElementById('paginationControls');
        if (!container) return;
        container.innerHTML = '';
        if (totalPages <= 1) return;

        const btnClass    = 'px-2.5 py-1 text-xs rounded-lg border transition-colors';
        const activeClass = 'bg-blue-600 text-white border-blue-600';
        const normalClass = 'border-gray-200 text-gray-600 hover:bg-gray-50';

        const prev      = document.createElement('button');
        prev.innerHTML  = '<i class="fa fa-chevron-left text-[10px]"></i>';
        prev.className  = `${btnClass} ${currentPage === 1 ? 'opacity-40 cursor-not-allowed border-gray-200 text-gray-400' : normalClass}`;
        prev.disabled   = currentPage === 1;
        prev.onclick    = () => { currentPage--; applyFilters(); };
        container.appendChild(prev);

        const range = 2;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - range && i <= currentPage + range)) {
                const btn      = document.createElement('button');
                btn.textContent = i;
                btn.className  = `${btnClass} ${i === currentPage ? activeClass : normalClass}`;
                btn.onclick    = (function(page) { return function() { currentPage = page; applyFilters(); }; })(i);
                container.appendChild(btn);
            } else if (i === currentPage - range - 1 || i === currentPage + range + 1) {
                const dots       = document.createElement('span');
                dots.textContent = '...';
                dots.className   = 'px-1 text-xs text-gray-400';
                container.appendChild(dots);
            }
        }

        const next     = document.createElement('button');
        next.innerHTML = '<i class="fa fa-chevron-right text-[10px]"></i>';
        next.className = `${btnClass} ${currentPage === totalPages ? 'opacity-40 cursor-not-allowed border-gray-200 text-gray-400' : normalClass}`;
        next.disabled  = currentPage === totalPages;
        next.onclick   = () => { currentPage++; applyFilters(); };
        container.appendChild(next);
    }

    /* TODO: implement PDF export
    function updatePdfLink() {
        const keyword = document.getElementById('searchInput').value;
        const pdfBtn  = document.getElementById('pdfBtn');
        const params  = new URLSearchParams();
        if (keyword)               params.set('search', keyword);
        if (activeStatus !== 'semua') params.set('status', activeStatus);
        if (activeBulan  !== 'semua') params.set('bulan',  activeBulan);
        const qs = params.toString();
        pdfBtn.href = '/admin/service-asuransi/pdf' + (qs ? '?' + qs : '');
    }
    */


    // =================================================================
    // POPUP ALERT
    // =================================================================
    (function() {
        const overlay = document.getElementById('alertOverlay');
        const box     = document.getElementById('alertBox');
        if (!overlay) return;
        setTimeout(function() {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
            box.style.transform = 'translateY(0)';
        }, 80);
        const timer = setTimeout(closeAlert, 4500);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeAlert(); });
        function closeAlert() {
            clearTimeout(timer);
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            box.style.transform = 'translateY(-16px)';
        }
        window.closeAlert = closeAlert;
    })();

    // =================================================================
    // STATUS MODAL
    // =================================================================
    // Init
    applyFilters();
    </script>

    {{-- ============================================================
    MODAL UBAH STATUS
    ============================================================ --}}
    <div id="modalStatus" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-auto" style="animation:slideUp .18s ease">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Ubah Status</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="statusModalDesc"></p>
                </div>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formStatus" method="POST" action="" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Penanganan</label>
                    <select name="status" id="statusSelect"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="bermasalah">⚠️ Bermasalah — mobil masih bermasalah</option>
                        <option value="selesai">✅ Selesai — masalah sudah ditangani</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="closeStatusModal()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
                        <i class="fa fa-save text-xs mr-1"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
    // =================================================================
    // STATUS MODAL HANDLER
    // =================================================================
    function openStatusModal(id, currentStatus, label) {
        document.getElementById('statusModalDesc').textContent = label || '';
        document.getElementById('statusSelect').value = currentStatus || 'bermasalah';
        document.getElementById('formStatus').action = '/admin/service-asuransi/' + id + '/status';

        const m = document.getElementById('modalStatus');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function closeStatusModal() {
        const m = document.getElementById('modalStatus');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('modalStatus').addEventListener('click', function(e) {
        if (e.target === this) closeStatusModal();
    });
    </script>

@endsection
