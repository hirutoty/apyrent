@extends('admin.layouts.app')

@section('title', 'History Rental - ' . $kendaraan->merk)

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">History Rental</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $kendaraan->merk }} &mdash;
                <span class="font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs">{{ $kendaraan->nopol }}</span>
            </p>
        </div>
        <a href="{{ route('history.index') }}"
            class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors duration-150 mt-2 sm:mt-0">
            <i class="fa fa-arrow-left text-sm"></i>
            Kembali
        </a>
    </div>

    {{-- SUMMARY CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4 w-fit">
        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <i class="fa fa-history text-blue-500 text-lg"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total Rental</p>
            <p class="text-2xl font-bold text-blue-600">{{ $rentals->count() }}</p>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- TOOLBAR --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Transaksi Rental</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $rentals->count() }} total transaksi</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Search --}}
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <input type="text" id="searchInput" placeholder="Cari member, status..."
                        oninput="applyFilters()"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                {{-- Filter Status --}}
                <div class="relative">
                    <i class="fa fa-filter absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <select id="filterStatus" onchange="applyFilters()"
                        class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 appearance-none bg-white cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="booking">Booking</option>
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">ID</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Member</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bukti</th>
                    </tr>
                </thead>
                <tbody id="rentalTableBody">
                    @forelse($rentals as $i => $r)
                        <tr class="border-t border-gray-50 transition-colors duration-100 {{ $r->status == 'aktif' ? 'bg-blue-200/50 hover:bg-blue-50' : 'hover:bg-gray-50' }}"
                            data-search="{{ strtolower(($r->member->nama_member ?? '') . ' ' . $r->status) }}"
                            data-status="{{ $r->status }}">

                            {{-- NO --}}
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-medium row-number">{{ $i + 1 }}</td>

                            {{-- ID --}}
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">#{{ $r->id }}</span>
                            </td>

                            {{-- MEMBER --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($r->member->nama_member ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $r->member->nama_member ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- TANGGAL + DURASI --}}
                            <td class="px-4 py-3.5">
                                <div class="text-sm text-gray-700">{{ $r->tanggal_mulai }} &ndash; {{ $r->tanggal_selesai }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    @if($r->durasi_hari)
                                        {{ $r->durasi_hari }} Hari
                                    @else
                                        {{ $r->durasi_jam }} Jam
                                    @endif
                                </div>
                            </td>

                            {{-- BIAYA --}}
                            <td class="px-4 py-3.5">
                                <div class="text-sm font-bold text-blue-600">Rp {{ number_format($r->total_biaya) }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    Dasar: Rp {{ number_format($r->biaya_dasar) }}<br>
                                    Tambahan: Rp {{ number_format($r->biaya_tambahan_total) }}
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($r->status == 'pending') bg-gray-100 text-gray-600
                                    @elseif($r->status == 'booking') bg-blue-100 text-blue-600
                                    @elseif($r->status == 'aktif') bg-green-100 text-green-600
                                    @elseif($r->status == 'selesai') bg-gray-800 text-white
                                    @else bg-red-100 text-red-600 @endif">
                                    {{ strtoupper($r->status) }}
                                </span>
                            </td>

                            {{-- BUKTI TF --}}
                            <td class="px-4 py-3.5">
                                @if($r->bukti_tf)
                                    <a href="{{ asset('storage/bukti_tf/' . $r->bukti_tf) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                        <i class="fa fa-image text-xs"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-history text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data rental</p>
                                </div>
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
                    <p class="text-xs text-gray-400">Coba ubah filter atau kata kunci pencarian</p>
                </div>
            </div>

        </div>

    </div>

</div>


<style>
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239ca3af'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    padding-right: 24px !important;
}
</style>

<script>
function applyFilters() {
    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    const status  = document.getElementById('filterStatus').value;
    const rows    = document.querySelectorAll('#rentalTableBody tr[data-search]');
    let visible   = 0;

    rows.forEach(row => {
        const matchSearch = !keyword || row.dataset.search.includes(keyword);
        const matchStatus = !status  || row.dataset.status === status;
        const show = matchSearch && matchStatus;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('totalCount').textContent = visible + ' total transaksi';

    const noResult = document.getElementById('noResultRow');
    if (noResult) noResult.classList.toggle('hidden', visible > 0 || rows.length === 0);

    let num = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cell = row.querySelector('.row-number');
            if (cell) cell.textContent = num++;
        }
    });
}
</script>

@endsection