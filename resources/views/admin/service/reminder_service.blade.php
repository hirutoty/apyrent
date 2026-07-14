@extends('admin.layouts.app')

@section('title', 'Reminder Service')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Service Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat Service & Data Mobil Bermasalah</p>
        </div>
        <button onclick="openModalTambah()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i> Tambah Reminder
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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Aktif</p>
                    <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalAktif }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-bell text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Jatuh Tempo</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $totalJatuhTempo }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Selesai</p>
                    <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalSelesai }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- TOOLBAR --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800">Reminder Service</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} data</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Filter Status (pill) --}}
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                    <a href="{{ url('/admin/reminder-service') }}"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors {{ !request('status') ? 'bg-white text-gray-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Semua
                    </a>
                    <a href="{{ url('/admin/reminder-service?status=aktif') }}"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors {{ request('status') == 'aktif' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-blue-600' }}">
                        Aktif
                    </a>
                    <a href="{{ url('/admin/reminder-service?status=jatuh_tempo') }}"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors {{ request('status') == 'jatuh_tempo' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-red-600' }}">
                        Jatuh Tempo
                    </a>
                    <a href="{{ url('/admin/reminder-service?status=selesai') }}"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors {{ request('status') == 'selesai' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-500 hover:text-emerald-600' }}">
                        Selesai
                    </a>
                </div>
                {{-- Search --}}
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari kendaraan..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 w-44">
                    </div>
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Cari</button>
                    @if(request('search'))
                    <a href="{{ url('/admin/reminder-service') }}" class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Reset</a>
                    @endif
                </form>
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

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Reminder</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Interval</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Jatuh Tempo</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Sisa Hari</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Keterangan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Biaya</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Status</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Group items by kendaraan_id while preserving pagination numbering
                        $grouped      = $data->getCollection()->groupBy('kendaraan_id');
                        $globalIndex  = $data->firstItem(); // 1-based starting number for this page
                        $groupCounter = 0;
                    @endphp

                    @forelse ($grouped as $kendaraanId => $reminders)
                        @php
                            $groupCounter++;
                            $groupId        = 'group-' . $kendaraanId . '-' . $groupCounter;
                            $firstItem      = $reminders->first();
                            $merk           = $firstItem->kendaraan->merk  ?? '-';
                            $nopol          = $firstItem->kendaraan->nopol ?? '-';
                            $totalCount     = $reminders->count();
                            $jatuhTempoCount = $reminders->where('status', 'jatuh_tempo')->count();
                            // Auto-expand if any reminder in this group is jatuh_tempo
                            $autoExpand     = $jatuhTempoCount > 0;
                        @endphp

                        {{-- GROUP HEADER ROW --}}
                        <tr class="group-header bg-blue-50 border-t border-blue-100 border-l-4 border-l-blue-500 cursor-pointer select-none hover:bg-blue-100 transition-colors shadow-sm"
                            onclick="toggleAccordion('{{ $groupId }}')"
                            data-group="{{ $groupId }}">
                            <td colspan="8" class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    {{-- Car icon --}}
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-car text-sm"></i>
                                    </div>
                                    {{-- Merk & Nopol --}}
                                    <div class="flex-1 min-w-0">
                                        <span class="font-semibold text-blue-900 text-sm">{{ $merk }}</span>
                                        <span class="ml-2 text-xs font-mono text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded">{{ $nopol }}</span>
                                    </div>
                                    {{-- Total reminders badge --}}
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-white text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-bell text-[10px]"></i>
                                        {{ $totalCount }} reminder
                                    </span>
                                    {{-- Jatuh tempo badge --}}
                                    @if ($jatuhTempoCount > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            {{ $jatuhTempoCount }} jatuh tempo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            Aman
                                        </span>
                                    @endif
                                    {{-- Chevron --}}
                                    <i id="chevron-{{ $groupId }}"
                                       class="fa-solid fa-chevron-down text-blue-500 text-xs transition-transform duration-200 {{ $autoExpand ? 'rotate-180' : '' }}"></i>
                                </div>
                            </td>
                        </tr>

                        {{-- DETAIL ROWS (child rows) --}}
                        @foreach ($reminders as $d)
                            @php
                                $sisa     = $d->sisaHari();
                                $rowClass = '';
                                if ($d->status === 'jatuh_tempo') {
                                    $rowClass = 'bg-red-50';
                                } elseif ($sisa <= 7 && $d->status === 'aktif') {
                                    $rowClass = 'bg-yellow-50';
                                }
                            @endphp
                            <tr class="group-detail border-t border-gray-100 transition-colors {{ $rowClass ?: 'hover:bg-blue-50/30' }}"
                                data-group="{{ $groupId }}"
                                style="{{ $autoExpand ? '' : 'display:none;' }}">
                                {{-- Nama Reminder --}}
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ $d->nama_reminder }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Mulai {{ \Carbon\Carbon::parse($d->tanggal_mulai)->format('d M Y') }}</p>
                                </td>

                                {{-- Interval --}}
                                <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $d->interval_nilai }} {{ $d->interval_satuan }}
                                </td>

                                {{-- Jatuh Tempo --}}
                                <td class="px-5 py-4 text-sm font-semibold
                                    {{ $d->status === 'jatuh_tempo' ? 'text-red-600' : 'text-gray-700' }}">
                                    {{ $d->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($d->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                                </td>

                                {{-- Sisa Hari --}}
                                <td class="px-5 py-4">
                                    @if ($d->status === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Selesai</span>
                                    @elseif ($sisa < 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Lewat {{ abs($sisa) }} hari</span>
                                    @elseif ($sisa === 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Hari ini!</span>
                                    @elseif ($sisa <= 7)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> {{ $sisa }} hari lagi</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $sisa }} hari lagi</span>
                                    @endif
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-5 py-4 text-sm text-gray-500 max-w-xs">
                                    <span class="line-clamp-2">{{ $d->keterangan ?? '-' }}</span>
                                </td>

                                {{-- Biaya --}}
                                <td class="px-5 py-4 text-sm text-gray-700 whitespace-nowrap">
                                    @if ($d->biaya !== null)
                                        Rp {{ number_format($d->biaya, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    <button type="button"
                                        onclick="openStatusModal({{ $d->id }}, '{{ $d->status }}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium transition-all hover:scale-105
                                            {{ $d->status === 'aktif'       ? 'bg-blue-50 text-blue-700 border border-blue-200'
                                              : ($d->status === 'jatuh_tempo' ? 'bg-red-50 text-red-700 border border-red-200'
                                              : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            {{ $d->status === 'aktif' ? 'bg-blue-500' : ($d->status === 'jatuh_tempo' ? 'bg-red-500' : 'bg-emerald-500') }}">
                                        </span>
                                        {{ $d->label_status }}
                                        <i class="fa-solid fa-pen-to-square text-[10px] opacity-60"></i>
                                    </button>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                            onclick="openModalEdit(
                                                {{ $d->id }},
                                                {{ $d->kendaraan_id }},
                                                '{{ addslashes($d->nama_reminder) }}',
                                                '{{ $d->tanggal_mulai }}',
                                                {{ $d->interval_nilai }},
                                                '{{ $d->interval_satuan }}',
                                                '{{ addslashes($d->keterangan ?? '') }}',
                                                '{{ $d->biaya ?? '' }}'
                                            )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="{{ route('reminder-service.destroy', $d->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus reminder ini?')" class="inline">
                                            @csrf @method('DELETE')
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
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-bell text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada reminder service</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Reminder" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>

</div>



{{-- ======================================
    MODAL TAMBAH
====================================== --}}
<div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
    style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] overflow-y-auto"
        style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Tambah Reminder Service</h2>
                <p class="text-xs text-gray-500 mt-0.5">Pilih kendaraan lalu tambah satu atau beberapa reminder sekaligus</p>
            </div>
            <button onclick="closeModalTambah()" class="text-gray-400 hover:text-red-500 text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form action="{{ route('reminder-service.store') }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf

            {{-- Kendaraan (satu, di atas) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Kendaraan <span class="text-red-500">*</span>
                </label>
                <select name="kendaraan_id" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach ($kendaraan as $k)
                        <option value="{{ $k->id }}">{{ $k->merk }} - {{ $k->nopol }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Header kolom --}}
            <div class="hidden md:flex gap-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide px-1">
                <div class="flex-[3_3_0%] min-w-0">Nama Reminder <span class="text-red-400">*</span></div>
                <div class="flex-[2_2_0%] min-w-0">Tanggal Mulai <span class="text-red-400">*</span></div>
                <div class="flex-[3_3_0%] min-w-0">Interval <span class="text-red-400">*</span></div>
                <div class="flex-[2_2_0%] min-w-0">Keterangan</div>
                <div class="flex-[2_2_0%] min-w-0">Biaya</div>
                <div class="w-8 flex-shrink-0"></div>
            </div>

            {{-- Rows Container --}}
            <div id="reminderRows" class="space-y-3">
                {{-- Row pertama (template) --}}
                <div class="reminder-row flex flex-col md:flex-row gap-3 items-start bg-gray-50 border border-gray-100 rounded-xl p-3">
                    {{-- Nama Reminder --}}
                    <div class="w-full md:flex-[3_3_0%] min-w-0">
                        <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Nama Reminder *</label>
                        <input type="text" name="items[0][nama_reminder]" required
                            placeholder="Service Rutin, Ganti Accu..."
                            class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                    </div>
                    {{-- Tanggal Mulai --}}
                    <div class="w-full md:flex-[2_2_0%] min-w-0">
                        <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Tanggal Mulai *</label>
                        <input type="date" name="items[0][tanggal_mulai]" required
                            class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                    </div>
                    {{-- Interval --}}
                    <div class="w-full md:flex-[3_3_0%] min-w-0">
                        <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Interval *</label>
                        <div class="flex gap-1.5">
                            <input type="number" name="items[0][interval_nilai]" required min="1" value="1"
                                class="w-16 flex-shrink-0 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                            <select name="items[0][interval_satuan]" required
                                class="flex-1 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                                <option value="hari">Hari</option>
                                <option value="minggu">Minggu</option>
                                <option value="bulan" selected>Bulan</option>
                                <option value="tahun">Tahun</option>
                            </select>
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="w-full md:flex-[2_2_0%] min-w-0">
                        <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Keterangan</label>
                        <input type="text" name="items[0][keterangan]"
                            placeholder="Opsional..."
                            class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                    </div>
                    {{-- Biaya --}}
                    <div class="w-full md:flex-[2_2_0%] min-w-0">
                        <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Biaya</label>
                        <input type="number" name="items[0][biaya]" min="0" step="1000"
                            placeholder="0"
                            class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                    </div>
                    {{-- Hapus Baris --}}
                    <div class="md:flex-shrink-0 flex md:items-center">
                        <button type="button" onclick="hapusBaris(this)"
                            class="hapus-baris w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 flex items-center justify-center transition-colors"
                            title="Hapus baris" style="display:none">
                            <i class="fa fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tombol Tambah Baris --}}
            <button type="button" onclick="tambahBaris()"
                class="w-full border-2 border-dashed border-blue-200 hover:border-blue-400 text-blue-500 hover:text-blue-700 text-sm font-medium py-2.5 rounded-xl flex items-center justify-center gap-2 transition-colors">
                <i class="fa fa-plus text-xs"></i> Tambah Baris
            </button>

            {{-- Tombol Submit --}}
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModalTambah()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Simpan Semua
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================
    MODAL EDIT
====================================== --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
    style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
        style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Edit Reminder Service</h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui jadwal pengingat service</p>
            </div>
            <button onclick="closeModalEdit()" class="text-gray-400 hover:text-red-500 text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="formEdit" method="POST"
            class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf @method('PUT')

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span class="text-red-500">*</span></label>
                <select name="kendaraan_id" id="edit_kendaraan_id" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @foreach ($kendaraan as $k)
                        <option value="{{ $k->id }}">{{ $k->merk }} - {{ $k->nopol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Reminder <span class="text-red-500">*</span></label>
                <input type="text" name="nama_reminder" id="edit_nama_reminder" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Interval <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input type="number" name="interval_nilai" id="edit_interval_nilai" required min="1"
                        class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <select name="interval_satuan" id="edit_interval_satuan" required
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="hari">Hari</option>
                        <option value="minggu">Minggu</option>
                        <option value="bulan">Bulan</option>
                        <option value="tahun">Tahun</option>
                    </select>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                <textarea name="keterangan" id="edit_keterangan" rows="3"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                <p class="text-[10px] text-gray-400 mt-1">Isi keterangan bebas: merek, SN, jenis ban, tipe kendaraan, dll.</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                    <input type="number" name="biaya" id="edit_biaya" min="0" step="1000"
                        placeholder="0"
                        class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika tidak ada estimasi biaya.</p>
            </div>

            <div class="md:col-span-2 flex gap-3 pt-1">
                <button type="button" onclick="closeModalEdit()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================
    MODAL STATUS
====================================== --}}
<div id="modalStatus" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
    style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-80 p-6" style="animation:slideUp .2s ease">
        <h2 class="font-bold text-sm text-gray-800 mb-4">Ubah Status Reminder</h2>
        <form id="formStatus" method="POST">
            @csrf @method('PUT')
            <select name="status" id="statusSelect"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-4">
                <option value="aktif">Aktif</option>
                <option value="jatuh_tempo">Jatuh Tempo</option>
                <option value="selesai">Selesai</option>
            </select>
            <div class="flex gap-3">
                <button type="button" onclick="closeStatusModal()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm py-2 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white text-sm font-semibold py-2 rounded-xl hover:bg-blue-700">Simpan</button>
            </div>
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
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p>
                </div>
            @else
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                    <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">
                        @foreach ($errors->any() ? $errors->all() : [session('error')] as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none flex-shrink-0">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
@endif

<style>
    @keyframes slideUp {
        from { opacity:0; transform:translateY(16px); }
        to   { opacity:1; transform:translateY(0); }
    }
</style>

<script>
    // ── MODAL TAMBAH ───────────────────────────────────────
    var rowIndex = 1; // baris ke-0 sudah ada, mulai dari 1

    function openModalTambah() {
        var m = document.getElementById('modalTambah');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
    function closeModalTambah() {
        var m = document.getElementById('modalTambah');
        m.classList.add('hidden');
        m.classList.remove('flex');
        // Reset: hapus semua baris kecuali baris pertama
        var container = document.getElementById('reminderRows');
        var rows = container.querySelectorAll('.reminder-row');
        rows.forEach(function(row, i) {
            if (i === 0) {
                // Reset isi baris pertama
                row.querySelectorAll('input').forEach(function(inp) {
                    inp.value = inp.type === 'number' ? '1' : '';
                });
                row.querySelectorAll('select').forEach(function(sel) {
                    // set default ke 'bulan'
                    for (var o = 0; o < sel.options.length; o++) {
                        sel.options[o].selected = sel.options[o].value === 'bulan';
                    }
                });
                row.querySelector('.hapus-baris').style.display = 'none';
            } else {
                row.remove();
            }
        });
        rowIndex = 1;
        syncRowNames();
    }
    document.getElementById('modalTambah').addEventListener('click', function(e) {
        if (e.target === this) closeModalTambah();
    });

    function tambahBaris() {
        var container = document.getElementById('reminderRows');
        var idx = rowIndex++;

        var row = document.createElement('div');
        row.className = 'reminder-row flex flex-col md:flex-row gap-3 items-start bg-gray-50 border border-gray-100 rounded-xl p-3';
        row.innerHTML = `
            <div class="w-full md:flex-[3_3_0%] min-w-0">
                <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Nama Reminder *</label>
                <input type="text" name="items[${idx}][nama_reminder]" required
                    placeholder="Service Rutin, Ganti Accu..."
                    class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
            <div class="w-full md:flex-[2_2_0%] min-w-0">
                <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Tanggal Mulai *</label>
                <input type="date" name="items[${idx}][tanggal_mulai]" required
                    class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
            <div class="w-full md:flex-[3_3_0%] min-w-0">
                <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Interval *</label>
                <div class="flex gap-1.5">
                    <input type="number" name="items[${idx}][interval_nilai]" required min="1" value="1"
                        class="w-16 flex-shrink-0 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                    <select name="items[${idx}][interval_satuan]" required
                        class="flex-1 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                        <option value="hari">Hari</option>
                        <option value="minggu">Minggu</option>
                        <option value="bulan" selected>Bulan</option>
                        <option value="tahun">Tahun</option>
                    </select>
                </div>
            </div>
            <div class="w-full md:flex-[2_2_0%] min-w-0">
                <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Keterangan</label>
                <input type="text" name="items[${idx}][keterangan]"
                    placeholder="Opsional..."
                    class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
            <div class="w-full md:flex-[2_2_0%] min-w-0">
                <label class="md:hidden text-[11px] font-semibold text-gray-500 mb-1 block">Biaya</label>
                <input type="number" name="items[${idx}][biaya]" min="0" step="1000"
                    placeholder="0"
                    class="w-full border border-gray-200 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
            <div class="md:flex-shrink-0 flex md:items-center">
                <button type="button" onclick="hapusBaris(this)"
                    class="hapus-baris w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 flex items-center justify-center transition-colors"
                    title="Hapus baris">
                    <i class="fa fa-trash text-xs"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        updateHapusVisibility();
    }

    function hapusBaris(btn) {
        var row = btn.closest('.reminder-row');
        row.remove();
        syncRowNames();
        updateHapusVisibility();
    }

    // Pastikan tombol hapus baris pertama hanya muncul jika ada > 1 baris
    function updateHapusVisibility() {
        var rows = document.querySelectorAll('#reminderRows .reminder-row');
        rows.forEach(function(row, i) {
            var btn = row.querySelector('.hapus-baris');
            if (btn) btn.style.display = rows.length > 1 ? 'flex' : 'none';
        });
    }

    // Re-index name attributes supaya sequential (opsional, Laravel tetap bisa baca array non-sequential)
    function syncRowNames() {
        var rows = document.querySelectorAll('#reminderRows .reminder-row');
        rows.forEach(function(row, i) {
            row.querySelectorAll('input, select').forEach(function(el) {
                if (el.name) {
                    el.name = el.name.replace(/items\[\d+\]/, 'items[' + i + ']');
                }
            });
        });
        rowIndex = rows.length;
    }

    // ── MODAL EDIT ─────────────────────────────────────────
    function openModalEdit(id, kendaraan_id, nama, tanggal_mulai, interval_nilai, interval_satuan, keterangan, biaya) {
        var m = document.getElementById('modalEdit');
        m.classList.remove('hidden');
        m.classList.add('flex');

        document.getElementById('formEdit').action = '/admin/reminder-service/' + id;
        document.getElementById('edit_kendaraan_id').value    = kendaraan_id;
        document.getElementById('edit_nama_reminder').value   = nama;
        document.getElementById('edit_tanggal_mulai').value   = tanggal_mulai;
        document.getElementById('edit_interval_nilai').value  = interval_nilai;
        document.getElementById('edit_interval_satuan').value = interval_satuan;
        document.getElementById('edit_keterangan').value      = keterangan;
        document.getElementById('edit_biaya').value           = biaya || '';
    }
    function closeModalEdit() {
        var m = document.getElementById('modalEdit');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }
    document.getElementById('modalEdit').addEventListener('click', function(e) {
        if (e.target === this) closeModalEdit();
    });

    // ── MODAL STATUS ───────────────────────────────────────
    function openStatusModal(id, currentStatus) {
        var m = document.getElementById('modalStatus');
        m.classList.remove('hidden');
        m.classList.add('flex');
        document.getElementById('formStatus').action = '/admin/reminder-service/' + id + '/status';
        document.getElementById('statusSelect').value = currentStatus;
    }
    function closeStatusModal() {
        var m = document.getElementById('modalStatus');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }
    document.getElementById('modalStatus').addEventListener('click', function(e) {
        if (e.target === this) closeStatusModal();
    });

    // ── ACCORDION GROUPS ───────────────────────────────────
    // Track open/closed state per groupId
    var groupState = {};

    // Initialize state from DOM on page load (auto-expanded groups already visible)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('tr.group-header').forEach(function (hdr) {
            var gid     = hdr.getAttribute('data-group');
            var chevron = document.getElementById('chevron-' + gid);
            // If chevron has rotate-180, it means auto-expanded
            groupState[gid] = chevron && chevron.classList.contains('rotate-180');
        });
    });

    function toggleAccordion(groupId) {
        var isOpen  = groupState[groupId];
        var details = document.querySelectorAll('tr.group-detail[data-group="' + groupId + '"]');
        var chevron = document.getElementById('chevron-' + groupId);

        if (isOpen) {
            // Collapse
            details.forEach(function (row) { row.style.display = 'none'; });
            if (chevron) { chevron.classList.remove('rotate-180'); }
            groupState[groupId] = false;
        } else {
            // Expand
            details.forEach(function (row) { row.style.display = ''; });
            if (chevron) { chevron.classList.add('rotate-180'); }
            groupState[groupId] = true;
        }
    }

    function expandAllAccordion() {
        document.querySelectorAll('tr.group-header').forEach(function (hdr) {
            var gid     = hdr.getAttribute('data-group');
            var details = document.querySelectorAll('tr.group-detail[data-group="' + gid + '"]');
            var chevron = document.getElementById('chevron-' + gid);
            details.forEach(function (row) { row.style.display = ''; });
            if (chevron) { chevron.classList.add('rotate-180'); }
            groupState[gid] = true;
        });
    }

    function collapseAllAccordion() {
        document.querySelectorAll('tr.group-header').forEach(function (hdr) {
            var gid     = hdr.getAttribute('data-group');
            var details = document.querySelectorAll('tr.group-detail[data-group="' + gid + '"]');
            var chevron = document.getElementById('chevron-' + gid);
            details.forEach(function (row) { row.style.display = 'none'; });
            if (chevron) { chevron.classList.remove('rotate-180'); }
            groupState[gid] = false;
        });
    }

    // ── ESC key ────────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeModalTambah(); closeModalEdit(); closeStatusModal(); }
    });

    // ── POPUP ALERT ────────────────────────────────────────
    (function () {
        var overlay = document.getElementById('alertOverlay');
        var box     = document.getElementById('alertBox');
        if (!overlay) return;
        setTimeout(function () {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
            box.style.transform = 'translateY(0)';
        }, 80);
        var timer = setTimeout(closeAlert, 4500);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeAlert(); });
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
