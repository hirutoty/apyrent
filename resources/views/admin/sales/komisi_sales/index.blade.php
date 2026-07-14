@extends('admin.layouts.app')
@section('title', 'Komisi Sales')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Komisi Sales</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data komisi dan insentif tim sales</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('komisi-sales.pdf') }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Komisi
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-users"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Data</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-money-bill-wave"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">Rp {{ number_format($totalKomisi/1000000, 1) }}jt</p>
                <p class="text-xs text-gray-500 mt-1">Total Komisi</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0">
                <i class="fa fa-chart-bar"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">Rp {{ number_format($totalPenjualan/1000000, 1) }}jt</p>
                <p class="text-xs text-gray-500 mt-1">Total Penjualan</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Komisi Sales</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
            </div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari nama sales..." oninput="onSearchInput(this.value)"
                    class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nama Sales</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Bulan</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Penjualan</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">% Komisi</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Komisi</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status Bayar</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->nama_sales }}</td>
                        <td class="px-4 py-3.5 text-gray-600 text-xs">{{ $d->bulan }}</td>
                        <td class="px-4 py-3.5 text-right text-gray-700 text-xs">Rp {{ number_format($d->total_penjualan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $d->persen_komisi }}%
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right text-green-700 text-xs font-bold">Rp {{ number_format($d->total_komisi, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $sc = match($d->status_bayar ?? 'Belum Dibayar') {
                                    'Sudah Dibayar' => 'bg-green-100 text-green-700',
                                    'Belum Dibayar' => 'bg-red-100 text-red-600',
                                    default         => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">
                                <i class="fa fa-circle text-[6px]"></i> {{ $d->status_bayar ?? 'Belum Dibayar' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $d->id }})"
                                    class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors">
                                    <i class="fa fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('komisi-sales.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors">
                                        <i class="fa fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400 text-sm">
                            <i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data komisi sales
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tambah Komisi Sales</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('komisi-sales.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Sales <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sales" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
                    <input type="month" name="bulan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Penjualan <span class="text-red-500">*</span></label>
                    <input type="number" name="total_penjualan" id="total_penjualan" required min="0" oninput="calcKomisi()"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">% Komisi <span class="text-red-500">*</span></label>
                    <input type="number" name="persen_komisi" id="persen_komisi" required min="0" max="100" oninput="calcKomisi()"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Komisi <span class="text-red-500">*</span></label>
                    <input type="number" name="total_komisi" id="total_komisi" required min="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status Bayar</label>
                    <select name="status_bayar" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Belum Dibayar">Belum Dibayar</option>
                        <option value="Sudah Dibayar">Sudah Dibayar</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Edit Komisi Sales</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Sales <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sales" id="edit_nama_sales" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
                    <input type="month" name="bulan" id="edit_bulan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Penjualan <span class="text-red-500">*</span></label>
                    <input type="number" name="total_penjualan" id="edit_total_penjualan" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">% Komisi <span class="text-red-500">*</span></label>
                    <input type="number" name="persen_komisi" id="edit_persen_komisi" required min="0" max="100" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Komisi <span class="text-red-500">*</span></label>
                    <input type="number" name="total_komisi" id="edit_total_komisi" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status Bayar</label>
                    <select name="status_bayar" id="edit_status_bayar" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Belum Dibayar">Belum Dibayar</option>
                        <option value="Sudah Dibayar">Sudah Dibayar</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function calcKomisi() {
    const p = parseFloat(document.getElementById('total_penjualan').value) || 0;
    const k = parseFloat(document.getElementById('persen_komisi').value) || 0;
    document.getElementById('total_komisi').value = (p * k / 100).toFixed(0);
}
function openModal() { document.getElementById('modalAdd').classList.remove('hidden'); document.getElementById('modalAdd').classList.add('flex'); }
function closeModal() { document.getElementById('modalAdd').classList.add('hidden'); document.getElementById('modalAdd').classList.remove('flex'); }
function openEditModal(id) {
    fetch(`/admin/komisi-sales/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/komisi-sales/${id}`;
            document.getElementById('edit_nama_sales').value = d.nama_sales;
            document.getElementById('edit_bulan').value = d.bulan;
            document.getElementById('edit_total_penjualan').value = d.total_penjualan;
            document.getElementById('edit_persen_komisi').value = d.persen_komisi;
            document.getElementById('edit_total_komisi').value = d.total_komisi;
            document.getElementById('edit_status_bayar').value = d.status_bayar ?? 'Belum Dibayar';
            document.getElementById('modalEdit').classList.remove('hidden');
            document.getElementById('modalEdit').classList.add('flex');
        });
}
function closeEditModal() { document.getElementById('modalEdit').classList.add('hidden'); document.getElementById('modalEdit').classList.remove('flex'); }

</script>

{{-- POPUP ALERT --}}
@if(session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox"
        class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @elseif(session('error'))
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
            <i class="fa fa-times"></i>
        </button>
    </div>
</div>
<script>
(function(){
    var overlay = document.getElementById('alertOverlay');
    var box = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(function(){ overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 80);
    var timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', function(e){ if(e.target===overlay) closeAlert(); });
    function closeAlert(){ clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>
@endif
@endsection
