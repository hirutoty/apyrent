@extends('admin.layouts.app')

@section('title', 'Reminder Service')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Reminder Service</h1>
            <p class="text-sm text-gray-500 mt-0.5">Reminder otomatis dari part kendaraan yang melewati interval</p>
        </div>
        {{-- Info: tidak ada tombol tambah manual --}}
        <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-medium px-4 py-2 rounded-xl">
            <i class="fa fa-info-circle"></i>
            Reminder dibuat otomatis dari sistem
        </div>
    </div>

    {{-- NAV TABS --}}
    <div>
        <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
            @php
                $navItems = [
                    ['label' => 'Service History',  'url' => '/admin/service-history',    'icon' => 'bi bi-clock-history'],
                    ['label' => 'Service Asuransi', 'url' => '/admin/service-asuransi',   'icon' => 'bi bi-shield-fill-check'],
                    ['label' => 'Reminder Service', 'url' => '/admin/reminder-service',   'icon' => 'bi bi-bell-fill'],
                    ['label' => 'Kategori Service', 'url' => '/admin/service-categories', 'icon' => 'bi bi-tags-fill'],
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
                    <p class="text-sm text-slate-500">Perawatan Kembali</p>
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
                <h2 class="font-semibold text-gray-800">Daftar Reminder</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} data</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Filter Status --}}
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                    @foreach (['' => 'Semua', 'aktif' => 'Aktif', 'jatuh_tempo' => 'Perawatan Kembali', 'selesai' => 'Selesai'] as $val => $label)
                        <a href="{{ url('/admin/reminder-service' . ($val ? '?status=' . $val : '')) }}"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors
                                {{ request('status') == $val ? 'bg-white text-' . ($val === 'aktif' ? 'blue' : ($val === 'jatuh_tempo' ? 'red' : ($val === 'selesai' ? 'emerald' : 'gray'))) . '-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                {{-- Search --}}
                <form method="GET" class="flex items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari kendaraan / part..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 w-48">
                    </div>
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Cari</button>
                    @if(request('search'))
                        <a href="{{ url('/admin/reminder-service' . (request('status') ? '?status=' . request('status') : '')) }}"
                            class="px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-100 rounded-lg">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Kendaraan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Part</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Kategori</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Interval</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Tgl Limit</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Sisa Hari</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $d)
                        @php
                            $sisa     = $d->sisaHari();
                            $part     = $d->servicePart;
                            $rowBg    = $d->status === 'jatuh_tempo' ? 'bg-red-50' : ($sisa <= 7 && $d->status === 'aktif' ? 'bg-amber-50' : '');
                        @endphp
                        <tr class="border-t border-gray-100 {{ $rowBg ?: 'hover:bg-blue-50/30' }} transition-colors">

                            {{-- Kendaraan --}}
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-800 text-sm">{{ $d->kendaraan?->merk ?? '-' }}</div>
                                <div class="text-xs font-mono text-gray-500">{{ $d->kendaraan?->nopol ?? '-' }}</div>
                                @if ($d->kendaraan?->jenis)
                                    <div class="text-xs text-gray-400">{{ $d->kendaraan->jenis->nama }}</div>
                                @endif
                            </td>

                            {{-- Part --}}
                            <td class="px-5 py-4">
                                @if ($part)
                                    <div class="font-medium text-gray-800 text-sm">{{ $part->nama_part }}</div>
                                    @if ($part->posisi)
                                        <div class="text-xs text-gray-500">Posisi: {{ $part->posisi }}</div>
                                    @endif
                                    @if ($part->serial_number)
                                        <div class="text-xs text-gray-400 font-mono">SN: {{ $part->serial_number }}</div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">{{ $d->nama_reminder }}</span>
                                @endif
                            </td>

                            {{-- Kategori --}}
                            <td class="px-5 py-4 text-sm text-gray-500">
                                {{ $part?->category?->nama ?? '—' }}
                            </td>

                            {{-- Interval --}}
                            <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ $d->interval_nilai }} {{ $d->interval_satuan }}
                            </td>

                            {{-- Tgl Limit --}}
                            <td class="px-5 py-4 text-sm font-semibold whitespace-nowrap
                                {{ $d->status === 'jatuh_tempo' ? 'text-red-600' : 'text-gray-700' }}">
                                {{ $d->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($d->tanggal_jatuh_tempo)->format('d M Y') : '-' }}
                            </td>

                            {{-- Sisa Hari --}}
                            <td class="px-5 py-4">
                                @if ($d->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Selesai
                                    </span>
                                @elseif ($sisa < 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Lewat {{ abs($sisa) }} hari
                                    </span>
                                @elseif ($sisa === 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Hari ini!
                                    </span>
                                @elseif ($sisa <= 7)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> {{ $sisa }} hari lagi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $sisa }} hari lagi
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $d->status === 'aktif' ? 'bg-blue-50 text-blue-700 border border-blue-200'
                                      : ($d->status === 'jatuh_tempo' ? 'bg-red-50 text-red-700 border border-red-200'
                                      : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $d->status === 'aktif' ? 'bg-blue-500' : ($d->status === 'jatuh_tempo' ? 'bg-red-500' : 'bg-emerald-500') }}">
                                    </span>
                                    {{ $d->label_status }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                    {{-- Tombol Selesaikan — hanya jika status aktif/jatuh_tempo dan punya service_part --}}
                                    @if ($d->status !== 'selesai')
                                        <a href="{{ route('service-history.create', ['from_reminder' => $d->id]) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors whitespace-nowrap">
                                            <i class="fa fa-check-circle text-xs"></i> Selesaikan
                                        </a>
                                    @endif

                                    {{-- Hapus --}}
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
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-bell text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada reminder service</p>
                                    <p class="text-xs text-gray-400">Reminder akan muncul otomatis saat part kendaraan melewati batas interval</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="py-3 border-t border-gray-100">
            <x-pagination :paginator="$data" />
        </div>
    </div>

</div>

{{-- ALERT --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if (session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold">Berhasil!</p><p class="text-xs text-gray-500">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold">Error!</p><ul class="text-xs text-gray-500 list-disc ml-4">@foreach($errors->any() ? $errors->all() : [session('error')] as $e)<li>{{$e}}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>

<script>
(function() {
    const overlay = document.getElementById('alertOverlay');
    const box = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(() => { overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 80);
    const timer = setTimeout(closeAlert, 5000);
    overlay.addEventListener('click', e => { if (e.target===overlay) closeAlert(); });
    function closeAlert() { clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>

@endsection
