@extends('admin.layouts.app')

@section('title', 'Kategori Service')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kategori Service</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola kategori part & aturan limit per kendaraan</p>
        </div>
        <button onclick="openModalKategori()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i> Tambah Kategori
        </button>
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

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl">
            <i class="fa fa-check-circle text-emerald-500 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <i class="fa fa-exclamation-circle text-red-500 flex-shrink-0"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-tags-fill text-blue-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500">Total Kategori</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $categories->count() }}</h3>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-sliders text-amber-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500">Aturan Limit Terdaftar</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $limits->count() }}</h3>
            </div>
        </div>
    </div>

    {{-- ========================= TABEL LIMIT RULES ========================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('service-categories.index') }}"
            class="flex flex-wrap items-end gap-3 px-5 py-4 border-b border-gray-100">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Filter Kategori</label>
                <select name="category_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $filterCategory == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Filter Kendaraan</label>
                <select name="kendaraan_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    <option value="">Semua Kendaraan</option>
                    @foreach($kendaraans as $k)
                        <option value="{{ $k->id }}" {{ $filterKendaraan == $k->id ? 'selected' : '' }}>
                            {{ $k->merk }} — {{ $k->nopol }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fa fa-filter text-xs mr-1"></i> Filter
                </button>
                <a href="{{ route('service-categories.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Reset
                </a>
            </div>
        </form>

        <div class="px-5 py-3 border-b border-gray-50">
            <h2 class="font-semibold text-gray-800 text-sm">Limit Rules per Kendaraan</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $limits->count() }} rule terdaftar</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">Kategori</th>
                        <th class="px-5 py-3 text-left">Kendaraan</th>
                        <th class="px-4 py-3 text-center">Interval</th>
                        <th class="px-4 py-3 text-right">Limit Harga</th>
                        <th class="px-4 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($limits as $rule)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-gray-800">
                                    {{ optional($rule->category)->nama ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-700">{{ optional($rule->kendaraan)->merk ?? '—' }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ optional($rule->kendaraan)->nopol ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-lg">
                                    <i class="bi bi-clock text-[10px]"></i>
                                    {{ $rule->limit_nilai }} {{ $rule->limit_satuan }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                @if($rule->limit_price)
                                    <span class="font-semibold text-emerald-700 tabular-nums">
                                        Rp {{ number_format($rule->limit_price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Tidak dibatasi</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        onclick="openModalEditLimit({{ $rule->id }}, {{ $rule->limit_nilai }}, '{{ $rule->limit_satuan }}', {{ $rule->limit_price ?? 'null' }})"
                                        class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <i class="fa fa-pen text-xs"></i>
                                    </button>
                                    <form method="POST"
                                        action="{{ route('service-categories.limits.destroy', $rule->id) }}"
                                        onsubmit="return confirm('Hapus limit rule ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">
                                <i class="bi bi-inbox text-3xl block mb-2 text-gray-300"></i>
                                Belum ada limit rule.
                                @if(!$filterCategory && !$filterKendaraan)
                                    Pilih kategori di tabel bawah lalu klik <span class="font-semibold text-amber-600">+ Limit</span>.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================= TABEL SEMUA KATEGORI ========================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-sm">Semua Kategori</h2>
            <p class="text-xs text-gray-400 mt-0.5">Kelola nama kategori & tambahkan limit rule per kendaraan</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">Nama Kategori</th>
                        <th class="px-4 py-3 text-center">Limit Rules</th>
                        <th class="px-4 py-3 text-center">Parts Aktif</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-gray-800">{{ $cat->nama }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                    {{ $cat->limits->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $cat->limits->count() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                    {{ $cat->parts_count > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $cat->parts_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        onclick="openModalTambahLimit({{ $cat->id }}, '{{ addslashes($cat->nama) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                        <i class="bi bi-plus-circle text-[10px]"></i> Limit
                                    </button>
                                    <button
                                        onclick="openModalEditKategori({{ $cat->id }}, '{{ addslashes($cat->nama) }}')"
                                        class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Edit nama">
                                        <i class="fa fa-pen text-xs"></i>
                                    </button>
                                    <form method="POST"
                                        action="{{ route('service-categories.destroy', $cat->id) }}"
                                        onsubmit="return confirm('Hapus kategori {{ addslashes($cat->nama) }}? Tindakan ini tidak bisa dibatalkan.')"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors"
                                            title="Hapus kategori">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">
                                <i class="bi bi-inbox text-3xl block mb-2 text-gray-300"></i>
                                Belum ada kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end .space-y-6 --}}

{{-- ===================== MODAL: TAMBAH KATEGORI ===================== --}}
<div id="modal-tambah-kategori"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Tambah Kategori Baru</h3>
            <button onclick="closeModal('modal-tambah-kategori')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('service-categories.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" required maxlength="100"
                    placeholder="contoh: Ban, Mesin, AC ..."
                    class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-tambah-kategori')"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors">
                    <i class="fa fa-plus text-xs mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL: EDIT KATEGORI ===================== --}}
<div id="modal-edit-kategori"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Edit Nama Kategori</h3>
            <button onclick="closeModal('modal-edit-kategori')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="form-edit-kategori" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" id="input-edit-nama" name="nama" required maxlength="100"
                    class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-kategori')"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL: TAMBAH LIMIT RULE ===================== --}}
<div id="modal-tambah-limit"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-800">Tambah Limit Rule</h3>
                <p id="tambah-limit-subtitle" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="closeModal('modal-tambah-limit')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="form-tambah-limit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Kendaraan <span class="text-red-500">*</span>
                </label>
                <select name="kendaraan_id" required
                    class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach($kendaraans as $k)
                        <option value="{{ $k->id }}">{{ $k->merk }} — {{ $k->nopol }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Interval Penggantian <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="number" name="limit_nilai" value="1" min="1" required
                        class="w-24 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-center">
                    <select name="limit_satuan" required
                        class="flex-1 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="hari">Hari</option>
                        <option value="minggu">Minggu</option>
                        <option value="bulan">Bulan</option>
                        <option value="tahun" selected>Tahun</option>
                    </select>
                </div>
                <p class="text-xs text-gray-400 mt-1">Part dalam kategori ini direkomendasikan diganti tiap interval ini.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Harga (Limit Price)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                    <input type="number" name="limit_price" min="0" step="1000"
                        placeholder="Kosongkan jika tidak dibatasi"
                        class="w-full border border-gray-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
                <p class="text-xs text-gray-400 mt-1">Input service akan diblokir jika biaya part melebihi nilai ini.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-tambah-limit')"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-xl hover:bg-amber-600 transition-colors">
                    <i class="fa fa-plus text-xs mr-1"></i> Simpan Limit
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL: EDIT LIMIT RULE ===================== --}}
<div id="modal-edit-limit"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Edit Limit Rule</h3>
            <button onclick="closeModal('modal-edit-limit')"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="form-edit-limit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Interval Penggantian <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="number" id="edit-limit-nilai" name="limit_nilai" min="1" required
                        class="w-24 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-center">
                    <select id="edit-limit-satuan" name="limit_satuan" required
                        class="flex-1 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="hari">Hari</option>
                        <option value="minggu">Minggu</option>
                        <option value="bulan">Bulan</option>
                        <option value="tahun">Tahun</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Harga (Limit Price)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                    <input type="number" id="edit-limit-price" name="limit_price" min="0" step="1000"
                        placeholder="Kosongkan jika tidak dibatasi"
                        class="w-full border border-gray-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-limit')"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    var el = document.getElementById(id);
    if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
}
function closeModal(id) {
    var el = document.getElementById(id);
    if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
}
document.addEventListener('DOMContentLoaded', function () {
    ['modal-tambah-kategori','modal-edit-kategori','modal-tambah-limit','modal-edit-limit']
        .forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('click', function (e) {
                if (e.target === el) closeModal(id);
            });
        });
});

function openModalKategori() { openModal('modal-tambah-kategori'); }

function openModalEditKategori(id, nama) {
    document.getElementById('form-edit-kategori').action = '/admin/service-categories/' + id;
    document.getElementById('input-edit-nama').value = nama;
    openModal('modal-edit-kategori');
}

function openModalTambahLimit(categoryId, categoryNama) {
    document.getElementById('form-tambah-limit').action = '/admin/service-categories/' + categoryId + '/limits';
    document.getElementById('tambah-limit-subtitle').textContent = 'Kategori: ' + categoryNama;
    var form = document.getElementById('form-tambah-limit');
    form.querySelector('[name="kendaraan_id"]').value  = '';
    form.querySelector('[name="limit_nilai"]').value   = '1';
    form.querySelector('[name="limit_satuan"]').value  = 'tahun';
    form.querySelector('[name="limit_price"]').value   = '';
    openModal('modal-tambah-limit');
}

function openModalEditLimit(limitId, limitNilai, limitSatuan, limitPrice) {
    document.getElementById('form-edit-limit').action  = '/admin/service-categories/limits/' + limitId;
    document.getElementById('edit-limit-nilai').value  = limitNilai;
    document.getElementById('edit-limit-satuan').value = limitSatuan;
    document.getElementById('edit-limit-price').value  = limitPrice !== null ? limitPrice : '';
    openModal('modal-edit-limit');
}
</script>

@endsection
