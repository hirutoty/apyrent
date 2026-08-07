@extends('admin.layouts.app')
@section('title', 'Data Leasing')
@section('content')
<div class="space-y-6 p-5">

    {{-- ALERTS --}}
    @if (session('success'))
    <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <i class="fa fa-exclamation-circle text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Leasing</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data leasing kendaraan</p>
        </div>
        <button onclick="openModal('modalCreate')"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <i class="fa fa-plus text-sm"></i> Tambah Data Leasing
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Data</p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $leasings->total() }}</h2>
        </div>
  
        <div class="bg-white rounded-2xl border border-gray-100 p-5 col-span-2 md:col-span-1">
            <p class="text-sm text-gray-500">Total Angsuran/Bln</p>
            <h2 class="text-2xl font-bold text-green-600 mt-2">
                Rp {{ number_format($leasings->getCollection()->sum('angsuran_per_bulan'), 0, ',', '.') }}
            </h2>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">No Kontrak</th>
                        <th class="px-4 py-3 text-left">Mobil</th>
                        <th class="px-4 py-3 text-left">Tahun</th>
                        <th class="px-4 py-3 text-left">Nopol</th>
                        <th class="px-4 py-3 text-right">Angsuran/Bln</th>
                        <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-left">Periode Mulai</th>
                        <th class="px-4 py-3 text-left">Periode Selesai</th>
                        <th class="px-4 py-3 text-left">Personal Account</th>
                        <th class="px-4 py-3 text-left">Sumber Dana Debit</th>
                        <th class="px-4 py-3 text-left">Cara Bayar</th>
                        <th class="px-4 py-3 text-left">Asuransi Leasing</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($leasings as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500">{{ $leasings->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium text-blue-700">{{ $item->no_kontrak ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->mobil ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->tahun ?? '-' }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $item->nopol ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium text-green-700">
                            Rp {{ number_format($item->angsuran_per_bulan, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $item->jatuh_tempo ? 'Tgl '.$item->jatuh_tempo : '-' }}
                        </td>
                        <td class="px-4 py-3">{{ $item->periode_mulai ? $item->periode_mulai->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3">{{ $item->periode_selesai ? $item->periode_selesai->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3">{{ $item->personal_account ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->sumber_dana_debit ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->cara_bayar ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->asuransi_leasing ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->user_leasing ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    onclick="openEditModal({{ json_encode($item) }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <i class="fa fa-pencil"></i> Edit
                                </button>
                                <form action="{{ route('data-leasing.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus data leasing ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="px-4 py-12 text-center text-gray-400">
                            <i class="bi bi-bank text-4xl block mb-3"></i>
                            Belum ada data leasing
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($leasings->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $leasings->links() }}
        </div>
        @endif
    </div>

</div>


{{-- ══════════════════════════════════════════
     MODAL CREATE
══════════════════════════════════════════ --}}
<div id="modalCreate" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Tambah Data Leasing</h3>
            <button onclick="closeModal('modalCreate')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form action="{{ route('data-leasing.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf

            {{-- Pilih Kontrak --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kontrak <span class="text-gray-400 text-xs">(auto-fill: No Kontrak, User, Mobil, Tahun, Nopol)</span></label>
                <select name="kontrak_id" id="create_kontrak_id" onchange="fetchKontrakDetail(this.value, 'create')"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">-- Pilih Kontrak --</option>
                    @foreach ($kontraks as $k)
                    <option value="{{ $k->id }}">{{ $k->no_kontrak }} – {{ $k->penawaran?->kepada ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- No Kontrak --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontrak</label>
                    <input type="text" name="no_kontrak" id="create_no_kontrak"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kontrak">
                </div>
                {{-- User --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <input type="text" name="user_leasing" id="create_user_leasing"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kontrak">
                </div>
            </div>

            {{-- Kendaraan (dinamis, auto-fill dari kontrak) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kendaraan <span class="text-gray-400 text-xs">(auto-fill dari kontrak)</span>
                </label>
                <div id="create_kendaraan_list" class="space-y-2">
                    <p class="text-sm text-gray-400 italic">Pilih kontrak terlebih dahulu</p>
                </div>
                {{-- Hidden inputs kendaraan di-generate JS --}}
                <div id="create_kendaraan_hidden"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Angsuran/Bln --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Angsuran/Bulan (Rp)</label>
                    <input type="number" name="angsuran_per_bulan" min="0"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="0">
                </div>
                {{-- Jatuh Tempo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span class="text-gray-400 text-xs">(tanggal tiap bulan)</span></label>
                    <div class="relative">
                        <input type="number" name="jatuh_tempo" id="create_jatuh_tempo"
                            min="1" max="31" disabled
                            class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                            placeholder="Auto-fill dari kontrak">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">setiap bulan</span>
                    </div>
                    <input type="hidden" name="jatuh_tempo" id="create_jatuh_tempo_hidden">
                </div>
                {{-- Periode Mulai --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="periode_mulai" id="create_periode_mulai"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                    <input type="hidden" name="periode_mulai" id="create_periode_mulai_hidden">
                </div>
                {{-- Periode Selesai --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="periode_selesai" id="create_periode_selesai"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                    <input type="hidden" name="periode_selesai" id="create_periode_selesai_hidden">
                </div>
                {{-- Personal Account --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personal Account</label>
                    <input type="text" name="personal_account"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Nama / No. Rekening">
                </div>
                {{-- Sumber Dana Debit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana Debit</label>
                    <input type="text" name="sumber_dana_debit"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Nama bank / sumber dana">
                </div>
                {{-- Cara Bayar --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cara Bayar</label>
                    <input type="text" name="cara_bayar"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Transfer / Auto Debit / dll">
                </div>
                {{-- Asuransi Leasing --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asuransi Leasing</label>
                    <select name="asuransi_leasing"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Pilih Asuransi --</option>
                        @foreach ($asuransis as $a)
                        <option value="{{ $a->nama_asuransi }}">{{ $a->nama_asuransi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hidden inputs untuk disabled fields --}}
            <input type="hidden" name="no_kontrak" id="create_no_kontrak_hidden">
            <input type="hidden" name="user_leasing" id="create_user_leasing_hidden">

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('modalCreate')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                    <i class="fa fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════
     MODAL EDIT
══════════════════════════════════════════ --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Edit Data Leasing</h3>
            <button onclick="closeModal('modalEdit')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="formEdit" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')

            {{-- Pilih Kontrak --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kontrak <span class="text-gray-400 text-xs">(auto-fill: No Kontrak, User, Mobil, Tahun, Nopol)</span></label>
                <select name="kontrak_id" id="edit_kontrak_id" onchange="fetchKontrakDetail(this.value, 'edit')"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Pilih Kontrak --</option>
                    @foreach ($kontraks as $k)
                    <option value="{{ $k->id }}">{{ $k->no_kontrak }} – {{ $k->penawaran?->kepada ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontrak</label>
                    <input type="text" name="no_kontrak" id="edit_no_kontrak"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <input type="text" name="user_leasing" id="edit_user_leasing"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>
            </div>

            {{-- Kendaraan dinamis --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kendaraan <span class="text-gray-400 text-xs">(auto-fill dari kontrak)</span>
                </label>
                <div id="edit_kendaraan_list" class="space-y-2">
                    <p class="text-sm text-gray-400 italic">Pilih kontrak untuk refresh, atau data tersimpan ditampilkan di bawah</p>
                </div>
                <div id="edit_kendaraan_hidden"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Angsuran/Bulan (Rp)</label>
                    <input type="number" name="angsuran_per_bulan" id="edit_angsuran" min="0"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span class="text-gray-400 text-xs">(tanggal tiap bulan)</span></label>
                    <div class="relative">
                        <input type="number" name="jatuh_tempo" id="edit_jatuh_tempo"
                            min="1" max="31" disabled
                            class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">setiap bulan</span>
                    </div>
                    <input type="hidden" name="jatuh_tempo" id="edit_jatuh_tempo_hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="periode_mulai" id="edit_periode_mulai"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                    <input type="hidden" name="periode_mulai" id="edit_periode_mulai_hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="periode_selesai" id="edit_periode_selesai"
                        disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                    <input type="hidden" name="periode_selesai" id="edit_periode_selesai_hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personal Account</label>
                    <input type="text" name="personal_account" id="edit_personal_account"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana Debit</label>
                    <input type="text" name="sumber_dana_debit" id="edit_sumber_dana"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cara Bayar</label>
                    <input type="text" name="cara_bayar" id="edit_cara_bayar"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asuransi Leasing</label>
                    <select name="asuransi_leasing" id="edit_asuransi"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Pilih Asuransi --</option>
                        @foreach ($asuransis as $a)
                        <option value="{{ $a->nama_asuransi }}">{{ $a->nama_asuransi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hidden inputs untuk disabled fields --}}
            <input type="hidden" name="no_kontrak" id="edit_no_kontrak_hidden">
            <input type="hidden" name="user_leasing" id="edit_user_leasing_hidden">

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('modalEdit')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-yellow-500 hover:bg-yellow-600 rounded-xl transition-colors">
                    <i class="fa fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════ --}}
<script>
// ── Modal helpers ──────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    el.classList.add('flex');
}
function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    el.classList.remove('flex');
}

// Tutup modal klik backdrop
document.querySelectorAll('#modalCreate, #modalEdit').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});

// ── Render kendaraan rows (shared by create & edit) ───
function renderKendaraans(kendaraans, prefix) {
    const list   = document.getElementById(prefix + '_kendaraan_list');
    const hidden = document.getElementById(prefix + '_kendaraan_hidden');

    if (!kendaraans || kendaraans.length === 0) {
        list.innerHTML = '<p class="text-sm text-gray-400 italic">Tidak ada data kendaraan pada kontrak ini</p>';
        hidden.innerHTML = '';
        return;
    }

    // Tampilan rows (disabled / read-only)
    list.innerHTML = kendaraans.map((k, i) => `
        <div class="grid grid-cols-3 gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100">
            <div>
                <p class="text-[10px] text-gray-400 mb-0.5">Mobil</p>
                <p class="text-sm font-medium text-gray-700">${k.mobil || '-'}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 mb-0.5">Tahun</p>
                <p class="text-sm font-medium text-gray-700">${k.tahun || '-'}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 mb-0.5">Nopol</p>
                <p class="text-sm font-mono text-xs font-medium text-gray-700">${k.nopol || '-'}</p>
            </div>
        </div>
    `).join('');

    // Hidden inputs — gabung jadi string koma untuk disimpan ke DB
    const mobilVal = kendaraans.map(k => k.mobil).filter(Boolean).join(', ');
    const tahunVal = kendaraans.map(k => k.tahun).filter(Boolean).join(', ');
    const nopolVal = kendaraans.map(k => k.nopol).filter(Boolean).join(', ');

    hidden.innerHTML = `
        <input type="hidden" name="mobil" value="${mobilVal}">
        <input type="hidden" name="tahun" value="${tahunVal}">
        <input type="hidden" name="nopol" value="${nopolVal}">
    `;
}

// ── Fetch kontrak detail untuk auto-fill ──────────────
function fetchKontrakDetail(kontrakId, prefix) {
    if (!kontrakId) return;

    fetch(`/admin/data-leasing/kontrak/${kontrakId}/detail`)
        .then(r => r.json())
        .then(data => {
            const noKontrak      = data.no_kontrak      ?? '';
            const userLeasing    = data.user_leasing    ?? '';
            const jatuhTempo     = data.jatuh_tempo     ?? '';
            const periodeMulai   = data.periode_mulai   ?? '';
            const periodeSelesai = data.periode_selesai ?? '';

            // Update displayed (disabled) fields
            document.getElementById(prefix + '_no_kontrak').value    = noKontrak;
            document.getElementById(prefix + '_user_leasing').value  = userLeasing;
            document.getElementById(prefix + '_jatuh_tempo').value   = jatuhTempo;
            document.getElementById(prefix + '_periode_mulai').value   = periodeMulai;
            document.getElementById(prefix + '_periode_selesai').value = periodeSelesai;

            // Sync ke hidden inputs
            document.getElementById(prefix + '_no_kontrak_hidden').value    = noKontrak;
            document.getElementById(prefix + '_user_leasing_hidden').value  = userLeasing;
            document.getElementById(prefix + '_jatuh_tempo_hidden').value   = jatuhTempo;
            document.getElementById(prefix + '_periode_mulai_hidden').value   = periodeMulai;
            document.getElementById(prefix + '_periode_selesai_hidden').value = periodeSelesai;

            renderKendaraans(data.kendaraans ?? [], prefix);
        })
        .catch(() => console.warn('Gagal mengambil detail kontrak.'));
}

// ── Open Edit Modal ────────────────────────────────────
function openEditModal(item) {
    const form = document.getElementById('formEdit');
    form.action = `/admin/data-leasing/${item.id}`;

    // Set dropdown kontrak
    document.getElementById('edit_kontrak_id').value    = item.kontrak_id   ?? '';
    document.getElementById('edit_no_kontrak').value    = item.no_kontrak   ?? '';
    document.getElementById('edit_user_leasing').value  = item.user_leasing ?? '';

    // Sync ke hidden inputs
    document.getElementById('edit_no_kontrak_hidden').value   = item.no_kontrak   ?? '';
    document.getElementById('edit_user_leasing_hidden').value = item.user_leasing ?? '';

    // Render kendaraan dari data tersimpan (mobil/tahun/nopol bisa berisi koma-separated)
    const mobils = (item.mobil ?? '').split(',').map(s => s.trim());
    const tahuns = (item.tahun ?? '').split(',').map(s => s.trim());
    const nopols = (item.nopol ?? '').split(',').map(s => s.trim());
    const maxLen = Math.max(mobils.length, tahuns.length, nopols.length);
    const kendaraans = maxLen > 0 && (mobils[0] || tahuns[0] || nopols[0])
        ? Array.from({ length: maxLen }, (_, i) => ({
            mobil: mobils[i] ?? '',
            tahun: tahuns[i] ?? '',
            nopol: nopols[i] ?? '',
        }))
        : [];
    renderKendaraans(kendaraans, 'edit');

    document.getElementById('edit_angsuran').value               = item.angsuran_per_bulan  ?? 0;
    document.getElementById('edit_jatuh_tempo').value            = item.jatuh_tempo          ?? '';
    document.getElementById('edit_jatuh_tempo_hidden').value     = item.jatuh_tempo          ?? '';
    document.getElementById('edit_periode_mulai').value          = item.periode_mulai        ?? '';
    document.getElementById('edit_periode_mulai_hidden').value   = item.periode_mulai        ?? '';
    document.getElementById('edit_periode_selesai').value        = item.periode_selesai      ?? '';
    document.getElementById('edit_periode_selesai_hidden').value = item.periode_selesai      ?? '';
    document.getElementById('edit_personal_account').value       = item.personal_account     ?? '';
    document.getElementById('edit_sumber_dana').value            = item.sumber_dana_debit    ?? '';
    document.getElementById('edit_cara_bayar').value             = item.cara_bayar           ?? '';
    document.getElementById('edit_asuransi').value               = item.asuransi_leasing     ?? '';

    openModal('modalEdit');
}
</script>

@endsection
