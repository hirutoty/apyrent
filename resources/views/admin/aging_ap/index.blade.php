@extends('admin.layouts.app')

@section('title', 'Aging AP')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Aging AP</h1>
                <p class="text-sm text-slate-500 mt-1">Monitoring umur hutang (accounts payable) vendor</p>
            </div>
        </div>

        <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            + Tambah Data
        </button>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Total Tagihan --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Tagihan</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $data->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Jatuh Tempo Segera --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Jatuh Tempo Segera</p>
                        <h3 class="text-3xl font-bold text-amber-600 mt-2">
                            {{ $data->filter(function ($d) use ($reminder) {
                                    $sisa = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay(), false);
                                    return $sisa >= 0 && $sisa <= $reminder;
                                })->count() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Terlambat --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Terlambat</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">
                            {{ $data->filter(function ($d) {
                                    return \Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay()->lt(now()->startOfDay());
                                })->count() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Jumlah --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Nilai Hutang</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-2">Rp
                            {{ number_format($data->sum('jumlah')) }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- TABLE HEADER + SEARCH --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800 text-base">Daftar Aging AP</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $data->count() }} total tagihan tercatat</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInput" placeholder="Cari no. tagihan, vendor, kategori..."
                            oninput="filterTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 w-56">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">No. Tagihan</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Vendor</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Jatuh Tempo</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Umur</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Jumlah</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Kategori</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100">

                        @forelse ($data as $d)
                            <tr class="hover:bg-slate-50 transition"
                                data-search="{{ strtolower($d->no_tagihan . ' ' . $d->vendor . ' ' . $d->kategori) }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- No Tagihan --}}
                                <td class="px-5 py-4 font-medium text-slate-800">{{ $d->no_tagihan }}</td>

                                {{-- Vendor --}}
                                <td class="px-5 py-4 text-slate-700">{{ $d->vendor }}</td>

                                {{-- Jatuh Tempo --}}
                                <td class="px-5 py-4">
                                    @php
                                        $jatuhTempo = \Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay();
                                        $hariIni = now()->startOfDay();
                                        $sisaHari = (int) $hariIni->diffInDays($jatuhTempo, false);
                                    @endphp

                                    <div class="flex flex-col gap-1">
                                        <span class="text-slate-600 text-sm">
                                            {{ $jatuhTempo->format('d M Y') }}
                                        </span>

                                        @if ($sisaHari < 0)
                                            <span
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-red-600 bg-red-50 border border-red-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                                Terlambat {{ abs($sisaHari) }} hari
                                            </span>
                                        @elseif ($sisaHari <= $reminder)
                                            <span
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>

                                                @if ($sisaHari == 0)
                                                    Jatuh Tempo Hari Ini
                                                @elseif ($sisaHari == 1)
                                                    Jatuh Tempo Besok
                                                @else
                                                    Jatuh Tempo dalam {{ $sisaHari }} hari
                                                @endif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                Belum Jatuh Tempo
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Umur --}}

                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    {{ $d->umur_otomatis }} Hari
                                </td>


                                {{-- Jumlah --}}
                                <td class="px-5 py-4 font-semibold text-slate-800">Rp
                                    {{ number_format($d->jumlah) }}
                                </td>

                                {{-- Kategori --}}
                                <td class="px-5 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
    {{ $d->kategori == 'Current' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $d->kategori }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Edit --}}
                                        <button onclick="openEditModal({{ $d }})"
                                            class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('aging_ap.destroy', $d->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                                <i class="fa-solid fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-file-invoice text-4xl mb-3 block"></i>
                                    Belum ada data aging AP
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

        </div>

    </div>

    {{-- MODAL TAMBAH  --}}

    <div id="modal" class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah Aging AP</h2>
                    <p class="text-sm text-slate-500">Isi data tagihan vendor baru</p>
                </div>
                <button onclick="closeModal()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="form" action="{{ route('aging_ap.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Vendor <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="vendor" id="add_vendor" required placeholder="Nama vendor"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" value="{{ old('vendor') }}">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="add_jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" value="{{ old('jatuh_tempo') }}">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Jumlah <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" id="add_jumlah" required placeholder="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" value="{{ old('jumlah') }}">
                    </div>

                </div>

                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}

    <div id="editModal" class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Edit Aging AP</h2>
                    <p class="text-sm text-slate-500">Perbarui data tagihan vendor</p>
                </div>
                <button onclick="closeModal()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Vendor --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Vendor <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="vendor" id="vendor" required placeholder="Nama vendor"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" value="{{ old('vendor') }}">
                    </div>

                    {{-- Jatuh Tempo --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" value="{{ old('jatuh_tempo') }}">
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Jumlah <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" required placeholder="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" value="{{ old('jumlah') }}">
                    </div>

                </div>

                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Data
                </button>

            </form>

        </div>
    </div>

    {{-- ======================================
        POPUP ALERT
    ====================================== --}}
    @if(session('success') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">
                @if(session('success'))
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif
                <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const addModal = document.getElementById('modal');
            const addForm = document.getElementById('form');

            // ===== MODAL EDIT =====
            window.openEditModal = function(data) {
                document.getElementById('vendor').value = data.vendor;
                document.getElementById('jatuh_tempo').value = data.jatuh_tempo.substring(0, 10);
                document.getElementById('jumlah').value = data.jumlah;
                editForm.action = '/admin/aging_ap/' + data.id;
                editModal.classList.remove('hidden');
            };

            window.closeEditModal = function() {
                editModal.classList.add('hidden');
            };

            editModal?.addEventListener('click', (e) => {
                if (e.target === editModal) editModal.classList.add('hidden');
            });

            // ===== MODAL TAMBAH =====
            window.openModal = function() {
                addForm.reset();
                addModal.classList.remove('hidden');
            };

            window.closeModal = function() {
                addModal.classList.add('hidden');
            };

            addModal?.addEventListener('click', (e) => {
                if (e.target === addModal) addModal.classList.add('hidden');
            });


            window.filterTable = function(q) {
                const rows = document.querySelectorAll('#tableBody tr[data-search]');
                rows.forEach(row => {
                    row.style.display = row.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
                });
            };

        });

        (function(){
            var overlay = document.getElementById('alertOverlay'),
                box = document.getElementById('alertBox');
            if (!overlay) return;
            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);
            var t = setTimeout(closeAlert, 4500);
            overlay.addEventListener('click', function(e) { if (e.target === overlay) closeAlert(); });
            function closeAlert() {
                clearTimeout(t);
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

    {{-- STYLE --}}
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
@endsection
