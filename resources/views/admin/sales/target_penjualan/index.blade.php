@extends('admin.layouts.app')
@section('title', 'Target Penjualan')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Target Penjualan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pantau dan kelola target penjualan tim sales</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('target-penjualan.pdf') }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Target
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-bullseye"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Data</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0">
                <i class="fa fa-flag"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">Rp {{ number_format($totalTarget/1000000, 1) }}jt</p>
                <p class="text-xs text-gray-500 mt-1">Total Target</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-chart-line"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">Rp {{ number_format($totalPencapaian/1000000, 1) }}jt</p>
                <p class="text-xs text-gray-500 mt-1">Pencapaian</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 text-lg flex-shrink-0">
                <i class="fa fa-percent"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $persentase }}%</p>
                <p class="text-xs text-gray-500 mt-1">Rata-rata Capaian</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Target Penjualan</h2>
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
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Target</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Pencapaian</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">% Capaian</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Keterangan</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    @php
                        $pct = $d->target_penjualan > 0 ? round(($d->pencapaian / $d->target_penjualan) * 100, 1) : 0;
                        $barColor = $pct >= 100 ? 'bg-green-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-red-400');
                    @endphp
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400">{{ $1->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->nama_sales }}</td>
                        <td class="px-4 py-3.5 text-gray-600 text-xs">{{ $d->bulan }}</td>
                        <td class="px-4 py-3.5 text-right text-gray-700 text-xs">Rp {{ number_format($d->target_penjualan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right text-gray-800 text-xs font-semibold">Rp {{ number_format($d->pencapaian, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-xs font-bold {{ $pct >= 100 ? 'text-green-600' : ($pct >= 70 ? 'text-yellow-600' : 'text-red-500') }}">{{ $pct }}%</span>
                                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="{{ $barColor }} h-full rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 text-xs">{{ $d->keterangan ?? '-' }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $d->id }})"
                                    class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors">
                                    <i class="fa fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('target-penjualan.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
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
                            <i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data target penjualan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tambah Target Penjualan</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('target-penjualan.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Sales <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sales" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('nama_sales') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
                    <input type="month" name="bulan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('bulan') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Target Penjualan <span class="text-red-500">*</span></label>
                    <input type="number" name="target_penjualan" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('target_penjualan') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pencapaian <span class="text-red-500">*</span></label>
                    <input type="number" name="pencapaian" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('pencapaian') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('keterangan') }}"
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
            <h3 class="font-semibold text-gray-800">Edit Target Penjualan</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Sales <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sales" id="edit_nama_sales" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('nama_sales') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
                    <input type="month" name="bulan" id="edit_bulan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('bulan') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Target Penjualan <span class="text-red-500">*</span></label>
                    <input type="number" name="target_penjualan" id="edit_target_penjualan" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('target_penjualan') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pencapaian <span class="text-red-500">*</span></label>
                    <input type="number" name="pencapaian" id="edit_pencapaian" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('pencapaian') }}"
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" id="edit_keterangan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('keterangan') }}"
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
function openModal() { document.getElementById('modalAdd').classList.remove('hidden'); document.getElementById('modalAdd').classList.add('flex'); }
function closeModal() { document.getElementById('modalAdd').classList.add('hidden'); document.getElementById('modalAdd').classList.remove('flex'); }
function openEditModal(id) {
    fetch(`/admin/target-penjualan/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/target-penjualan/${id}`;
            document.getElementById('edit_nama_sales').value = d.nama_sales;
            document.getElementById('edit_bulan').value = d.bulan;
            document.getElementById('edit_target_penjualan').value = d.target_penjualan;
            document.getElementById('edit_pencapaian').value = d.pencapaian;
            document.getElementById('edit_keterangan').value = d.keterangan ?? '';
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

        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') {
                openModalTambah();
            } else if (typeof openModal === 'function') {
                openModal();
            }
        });
        @endif
</script>
@endif
@endsection
