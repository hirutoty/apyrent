@extends('admin.layouts.app')

@section('title', 'Ceklist Kondisi Kendaraan')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ceklist Kondisi Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar ceklist kondisi kendaraan yang telah dibuat</p>
        </div>
    </div>

    {{-- NAV TABS --}}
    @include('admin.service._nav_tabs', ['activeTab' => 'ceklist'])

    {{-- SUMMARY CARDS --}}
    @php
        $totalCeklist = $kendaraans->sum(fn($k) => $k->ceklistKondisi->count());
        $totalKendaraan = $kendaraans->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Ceklist</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $totalCeklist }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="bi bi-clipboard-check-fill text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Kendaraan</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $totalKendaraan }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="bi bi-car-front-fill text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Rata-rata per Kendaraan</p>
                    <h3 class="text-3xl font-bold text-purple-600 mt-2">{{ $totalKendaraan > 0 ? number_format($totalCeklist / $totalKendaraan, 1) : 0 }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center">
                    <i class="bi bi-graph-up text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- KENDARAAN LIST --}}
    @forelse($kendaraans as $kendaraan)
        @php $ceklists = $kendaraan->ceklistKondisi; @endphp
        @if($ceklists->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            
            {{-- KENDARAAN HEADER --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-5 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center text-lg font-bold shadow-md">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $kendaraan->nopol }}</h3>
                            <p class="text-sm text-gray-500">{{ $kendaraan->merk }} • {{ $ceklists->count() }} ceklist</p>
                        </div>
                    </div>
                    <button onclick="toggleKendaraan('{{ $kendaraan->id }}')" 
                        class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                        <i id="chevron-{{ $kendaraan->id }}" class="bi bi-chevron-down transition-transform"></i>
                        <span id="label-{{ $kendaraan->id }}">Lihat Ceklist</span>
                    </button>
                </div>
            </div>

            {{-- CEKLIST TABLE (collapsed by default) --}}
            <div id="ceklist-{{ $kendaraan->id }}" class="hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama / Perusahaan</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pemeriksa</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kilometer</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Fuel</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                                <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ceklists as $ceklist)
                            <tr class="border-t border-gray-50 hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-700">{{ $ceklist->tanggal->format('d M Y') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-800">{{ $ceklist->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $ceklist->perusahaan }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $ceklist->pemeriksa }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-blue-600">{{ number_format($ceklist->kilometer) }} km</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold 
                                        {{ $ceklist->fuel === 'F' ? 'bg-green-100 text-green-700' : ($ceklist->fuel === 'E' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ $ceklist->fuel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($ceklist->status)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                            {{ ucfirst($ceklist->status) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('ceklist-kondisi.show', $ceklist->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="bi bi-eye text-xs"></i> Detail
                                        </a>
                                        <form action="{{ route('ceklist-kondisi.destroy', $ceklist->id) }}" method="POST" 
                                            onsubmit="return confirm('Yakin ingin menghapus ceklist ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                <i class="bi bi-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        @endif
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="text-center text-gray-400">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-clipboard-x text-3xl text-gray-300"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Belum ada ceklist kondisi</p>
                <p class="text-xs text-gray-400 mt-1">Ceklist kondisi akan muncul setelah dibuat dari halaman service</p>
            </div>
        </div>
    @endforelse

</div>

{{-- POPUP ALERT --}}
@if(session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                    @if(session('error'))<li>{{ session('error') }}</li>@endif
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>
@endif

<script>
// Toggle kendaraan ceklist visibility
function toggleKendaraan(id) {
    const ceklist = document.getElementById('ceklist-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const label = document.getElementById('label-' + id);
    
    if (ceklist.classList.contains('hidden')) {
        ceklist.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
        label.textContent = 'Sembunyikan';
    } else {
        ceklist.classList.add('hidden');
        chevron.style.transform = '';
        label.textContent = 'Lihat Ceklist';
    }
}

// Alert auto-show
(function() {
    const overlay = document.getElementById('alertOverlay');
    const box = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(() => { 
        overlay.style.opacity = '1'; 
        overlay.style.pointerEvents = 'auto'; 
        box.style.transform = 'translateY(0)'; 
    }, 80);
    const timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeAlert(); });
})();

function closeAlert() {
    const overlay = document.getElementById('alertOverlay');
    if (overlay) {
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        const box = document.getElementById('alertBox');
        if (box) box.style.transform = 'translateY(-16px)';
    }
}
</script>

@endsection
