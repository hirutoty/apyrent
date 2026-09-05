@extends('admin.layouts.app')

@section('title', 'Tambah Pembayaran')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6 max-w-5xl mx-auto">

    {{-- PAGE HEADER --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('pembayaran.index') }}" 
           class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">
            <i class="fa fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Tambah Pembayaran</h1>
            <p class="text-xs text-gray-500 mt-0.5">Buat permintaan pembayaran barang & jasa dengan multiple items</p>
        </div>
    </div>

    <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SECTION 1: HEADER PEMBAYARAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">1</span>
                Informasi Pembayaran
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- No PR --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        No PR <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_pr" readonly
                        value="Auto Generate"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-600 cursor-not-allowed focus:outline-none">
                </div>

                {{-- Tanggal Pengajuan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Pengajuan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal" required
                        value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('tanggal')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Departemen --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Departemen <span class="text-red-500">*</span>
                    </label>
                    @if($role === 'superadmin')
                        <select name="departemen" required id="select_departemen"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            onchange="onDepartemenChange(this.value)">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemenOptions as $dept)
                                <option value="{{ $dept }}" {{ old('departemen') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="departemen" required readonly
                            value="{{ old('departemen', $deptLabel) }}"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-600 cursor-not-allowed focus:outline-none">
                    @endif
                    @error('departemen')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Pemohon --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Pemohon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pemohon" required readonly
                        value="{{ old('pemohon', auth()->user()->name) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-600 cursor-not-allowed focus:outline-none">
                    @error('pemohon')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Tipe Pembayaran --}}
                @php
                    $isProduksi = ($role === 'produksi');
                    $defaultTipe = old('tipe_pembayaran', 'belanja');
                @endphp

                {{-- Non-produksi & non-superadmin: hidden, selalu belanja --}}
                @if(!$isProduksi && $role !== 'superadmin')
                    <input type="hidden" name="tipe_pembayaran" value="belanja">
                @elseif($isProduksi)
                {{-- Produksi: 2 tombol toggle --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pembayaran</label>
                    <div class="flex gap-2">
                        <label id="btn_belanja"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl border cursor-pointer transition-colors {{ $defaultTipe === 'belanja' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                            onclick="switchTipe('belanja')">
                            <input type="radio" name="tipe_pembayaran" id="radio_belanja" value="belanja"
                                {{ $defaultTipe === 'belanja' ? 'checked' : '' }} class="hidden">
                            <i class="fa fa-shopping-bag text-xs"></i>
                            <span class="text-sm font-medium">Belanja</span>
                        </label>
                        <label id="btn_service"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl border cursor-pointer transition-colors {{ $defaultTipe === 'service' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                            onclick="switchTipe('service')">
                            <input type="radio" name="tipe_pembayaran" id="radio_service" value="service"
                                {{ $defaultTipe === 'service' ? 'checked' : '' }} class="hidden">
                            <i class="fa fa-wrench text-xs"></i>
                            <span class="text-sm font-medium">Service Kendaraan</span>
                        </label>
                    </div>
                </div>
                @else
                {{-- Superadmin: dropdown (bisa muncul toggle dinamis saat pilih Produksi) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pembayaran</label>
                    <div id="tipe_toggle_wrapper" class="hidden">
                        <div class="flex gap-2">
                            <label id="btn_belanja"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl border cursor-pointer transition-colors {{ $defaultTipe === 'belanja' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                                onclick="switchTipe('belanja')">
                                <input type="radio" name="tipe_pembayaran" id="radio_belanja" value="belanja"
                                    {{ $defaultTipe === 'belanja' ? 'checked' : '' }} class="hidden">
                                <i class="fa fa-shopping-bag text-xs"></i>
                                <span class="text-sm font-medium">Belanja</span>
                            </label>
                            <label id="btn_service"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl border cursor-pointer transition-colors {{ $defaultTipe === 'service' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
                                onclick="switchTipe('service')">
                                <input type="radio" name="tipe_pembayaran" id="radio_service" value="service"
                                    {{ $defaultTipe === 'service' ? 'checked' : '' }} class="hidden">
                                <i class="fa fa-wrench text-xs"></i>
                                <span class="text-sm font-medium">Service Kendaraan</span>
                            </label>
                        </div>
                    </div>
                    <div id="tipe_belanja_hidden" class="{{ $defaultTipe === 'service' ? 'hidden' : '' }}">
                        <input type="hidden" id="hidden_tipe_belanja" name="tipe_pembayaran" value="belanja">
                        <p class="text-xs text-gray-400 italic">Pilih departemen Produksi untuk mengaktifkan mode Service</p>
                    </div>
                </div>
                @endif

                {{-- Kendaraan (muncul saat service) — hanya relevan untuk produksi/superadmin --}}
                @if($isProduksi || $role === 'superadmin')
                <div id="field_kendaraan" class="{{ old('tipe_pembayaran')==='service' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select id="kendaraan_id" name="kendaraan_id"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        onchange="onKendaraanChange(this.value)">
                        <option value="">-- Pilih Kendaraan --</option>
                    </select>
                    @error('kendaraan_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                @endif

                {{-- Supplier (hanya untuk belanja) --}}
                <div id="field_supplier" class="{{ old('tipe_pembayaran')==='service' ? 'hidden' : '' }}">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Supplier <span class="text-gray-400 text-[10px]">(jika tidak ada, klik +)</span>
                    </label>
                    <div class="flex gap-2">
                        <select name="supplier_id" id="supplier_id"
                            class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach(\App\Models\Supplier::orderBy('nama_supplier')->get() as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="openSupplierModal()"
                            class="w-10 h-10 rounded-xl bg-green-600 hover:bg-green-700 text-white flex items-center justify-center transition-colors"
                            title="Tambah Supplier Baru">
                            <i class="fa fa-plus text-sm"></i>
                        </button>
                    </div>
                    @error('supplier_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Alasan Permintaan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Alasan Permintaan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan_permintaan" rows="3" required placeholder="Jelaskan alasan dan kebutuhan pembayaran..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('alasan_permintaan') }}</textarea>
                    @error('alasan_permintaan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- SECTION 2: ITEMS (Belanja) --}}
        <div id="section_belanja" class="{{ old('tipe_pembayaran')==='service' ? 'hidden' : '' }} bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">2</span>
                    Barang / Jasa yang Dibutuhkan
                </h2>
                <button type="button" onclick="addItemRow()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fa fa-plus text-xs"></i> Tambah Barang
                </button>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="text-xs text-gray-400">Total Nominal:</div>
                <div class="font-bold text-lg text-gray-800" id="total_display">Rp 0</div>
                <input type="hidden" name="total_nominal" id="total_nominal_input" value="0">
            </div>
            @error('items')<div class="text-xs text-red-500">{{ $message }}</div>@enderror
            <div id="items-container" class="space-y-4"></div>
            <div id="empty-items-hint" class="text-center py-6 text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                <i class="fa fa-shopping-cart text-gray-300 text-2xl mb-2 block"></i>
                Klik "+ Tambah Barang" untuk menambahkan item pembayaran
            </div>
        </div>

        {{-- SECTION 2: INFORMASI SERVICE --}}
        <div id="section_service" class="{{ old('tipe_pembayaran')==='service' ? '' : 'hidden' }} bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xs">2</span>
                Informasi Service
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Service <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_service" value="{{ old('tanggal_service', date('Y-m-d')) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('tanggal_service')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kilometer Saat Ini <span class="text-red-500">*</span></label>
                    <input type="number" name="kilometer" id="input_kilometer" value="{{ old('kilometer', 0) }}" min="0"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('kilometer')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keluhan</label>
                    <textarea name="keluhan" rows="3" placeholder="Deskripsikan keluhan kendaraan..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('keluhan') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lampiran Tambahan</label>
                    <input type="file" name="lampiran_tambahan" multiple accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                </div>
            </div>
        </div>

        {{-- SECTION 3: PART / KOMPONEN (Service) --}}
        <div id="section_parts" class="{{ old('tipe_pembayaran')==='service' ? '' : 'hidden' }} bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xs">3</span>
                    Part / Komponen yang Dipasang
                </h2>
                <button type="button" onclick="addPartRow()"
                    class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fa fa-plus text-xs"></i> Tambah Part
                </button>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="text-xs text-gray-400">Total Biaya Part:</div>
                <div class="font-bold text-lg text-gray-800" id="total_parts_display">Rp 0</div>
            </div>
            {{-- Warning limit: ditampilkan oleh JS saat ada part melebihi limit --}}
            <div id="parts_over_limit_summary" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <p class="text-xs font-semibold text-red-700 mb-1.5">
                    <i class="fa fa-triangle-exclamation mr-1"></i> Peringatan: Beberapa part melebihi Service Limit
                </p>
                <ul id="parts_over_limit_list" class="text-xs text-red-600 space-y-0.5 list-disc ml-4"></ul>
                <p class="text-xs text-red-500 mt-1.5 italic">Pembayaran tetap bisa disubmit, namun akan tercatat sebagai overservice.</p>
            </div>
            @error('parts')<div class="text-xs text-red-500">{{ $message }}</div>@enderror
            <div id="parts-container" class="space-y-4"></div>
            <div id="empty-parts-hint" class="text-center py-6 text-xs text-gray-400 border-2 border-dashed border-orange-200 rounded-xl">
                <i class="fa fa-wrench text-orange-200 text-2xl mb-2 block"></i>
                Klik "+ Tambah Part" untuk menambahkan part yang akan dipasang
            </div>
        </div>

        {{-- SECTION PEMBAYARAN: nama, bank, rekening --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-xs">
                    <i class="fa fa-university text-xs"></i>
                </span>
                Data Pembayaran
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Penerima <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_penerima" required value="{{ old('nama_penerima') }}"
                        placeholder="Nama penerima pembayaran"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('nama_penerima')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_bank" required value="{{ old('nama_bank') }}"
                        placeholder="Contoh: BCA, Mandiri, BNI"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('nama_bank')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="no_rekening" required value="{{ old('no_rekening') }}"
                        placeholder="Nomor rekening"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('no_rekening')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('pembayaran.index') }}"
                class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors">
                <i class="fa fa-save text-sm"></i>
                @if(auth()->user()->role === 'superadmin')
                    Simpan & Ajukan
                @else
                    Simpan Pembayaran
                @endif
            </button>
        </div>

    </form>
</div>

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.item-row { animation: slideUp 0.15s ease; }
</style>

<script>
// ── Globals ───────────────────────────────────────────────────
let itemIndex = 0;
let partIndex = 0;
let categoryData = []; // cache dari AJAX

// ── Toggle Belanja / Service ──────────────────────────────────
function switchTipe(val) {
    const btnBelanja = document.getElementById('btn_belanja');
    const btnService = document.getElementById('btn_service');
    const radioBelanja = document.getElementById('radio_belanja');
    const radioService = document.getElementById('radio_service');

    if (btnBelanja && btnService) {
        if (val === 'belanja') {
            btnBelanja.classList.remove('bg-white', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-50');
            btnBelanja.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
            btnService.classList.remove('bg-orange-500', 'text-white', 'border-orange-500');
            btnService.classList.add('bg-white', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-50');
            if (radioBelanja) radioBelanja.checked = true;
        } else {
            btnService.classList.remove('bg-white', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-50');
            btnService.classList.add('bg-orange-500', 'text-white', 'border-orange-500');
            btnBelanja.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
            btnBelanja.classList.add('bg-white', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-50');
            if (radioService) radioService.checked = true;
        }
    }
    onTipePembayaranChange(val);
}

// ── Superadmin: saat pilih departemen → tampil/sembunyikan toggle service ──
function onDepartemenChange(deptVal) {
    const isProduksi = (deptVal === 'Produksi');
    const toggleWrapper  = document.getElementById('tipe_toggle_wrapper');
    const belanjaHidden  = document.getElementById('tipe_belanja_hidden');
    const hiddenBelanja  = document.getElementById('hidden_tipe_belanja');
    const fieldKendaraan = document.getElementById('field_kendaraan');

    if (!toggleWrapper) return;

    if (isProduksi) {
        // Tampilkan toggle, sembunyikan hidden belanja
        toggleWrapper.classList.remove('hidden');
        if (belanjaHidden) belanjaHidden.classList.add('hidden');
        if (hiddenBelanja) hiddenBelanja.disabled = true;
    } else {
        // Sembunyikan toggle, kembalikan ke belanja
        toggleWrapper.classList.add('hidden');
        if (belanjaHidden) belanjaHidden.classList.remove('hidden');
        if (hiddenBelanja) hiddenBelanja.disabled = false;
        // Reset ke belanja
        switchTipe('belanja');
        onTipePembayaranChange('belanja');
        if (fieldKendaraan) fieldKendaraan.classList.add('hidden');
    }
}

function onTipePembayaranChange(val) {
    const isService = val === 'service';
    document.getElementById('section_belanja').classList.toggle('hidden', isService);
    document.getElementById('section_service').classList.toggle('hidden', !isService);
    document.getElementById('section_parts').classList.toggle('hidden', !isService);
    const fieldKendaraan = document.getElementById('field_kendaraan');
    const fieldSupplier  = document.getElementById('field_supplier');
    if (fieldKendaraan) fieldKendaraan.classList.toggle('hidden', !isService);
    if (fieldSupplier)  fieldSupplier.classList.toggle('hidden', isService);

    // Disable required fields di section tersembunyi agar browser tidak memblokir submit
    // Section belanja
    document.querySelectorAll('#section_belanja input[required], #section_belanja textarea[required]').forEach(el => {
        el.disabled = isService;
    });
    // Section service + parts
    document.querySelectorAll('#section_service input[required], #section_service textarea[required], #section_parts input[required], #section_parts textarea[required]').forEach(el => {
        el.disabled = !isService;
    });
    // Kendaraan field
    const kendaraanSel = document.getElementById('kendaraan_id');
    if (kendaraanSel) kendaraanSel.disabled = !isService;

    if (isService && document.getElementById('kendaraan_id') && document.getElementById('kendaraan_id').options.length <= 1) {
        loadKendaraan();
    }
}

// ── Load daftar kendaraan via AJAX ────────────────────────────
function loadKendaraan() {
    fetch('{{ route("pembayaran.api.kendaraan") }}')
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const sel = document.getElementById('kendaraan_id');
            sel.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
            res.data.forEach(k => {
                const opt = new Option(k.label, k.id);
                opt.dataset.km = k.kilometer_sekarang;
                sel.appendChild(opt);
            });
        });
}

// ── Saat kendaraan dipilih ─────────────────────────────────────
function onKendaraanChange(kendaraanId) {
    if (!kendaraanId) return;

    // Auto-fill kilometer
    const sel = document.getElementById('kendaraan_id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.km) {
        document.getElementById('input_kilometer').value = opt.dataset.km;
    }

    // Load kategori + limit untuk kendaraan ini
    fetch(`{{ route("pembayaran.api.category-limit") }}?kendaraan_id=${kendaraanId}`)
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                categoryData = res.data;
                // Re-render dropdown kategori yang sudah ada
                document.querySelectorAll('.part-category-select').forEach(sel => {
                    updateCategoryOptions(sel);
                });
            }
        });
}

function updateCategoryOptions(sel) {
    const current = sel.value;
    sel.innerHTML = '<option value="">-- Pilih Kategori --</option>';
    categoryData.forEach(c => {
        const opt = new Option(c.nama, c.id);
        opt.dataset.limitNilai  = c.limit_nilai;
        opt.dataset.limitSatuan = c.limit_satuan;
        opt.dataset.limitPrice  = c.limit_price;
        sel.appendChild(opt);
    });
    if (current) sel.value = current;
}

// ── Saat kategori part dipilih → autofill interval ────────────
function onCategoryChange(sel, idx) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    const nilai  = opt.dataset.limitNilai  || 1;
    const satuan = opt.dataset.limitSatuan || 'bulan';
    const price  = parseInt(opt.dataset.limitPrice || 0);

    const intvNilai  = document.getElementById(`part_interval_nilai_${idx}`);
    const intvSatuan = document.getElementById(`part_interval_satuan_${idx}`);
    const biayaInput = document.getElementById(`part_biaya_${idx}`);

    if (intvNilai)  intvNilai.value  = nilai;
    if (intvSatuan) intvSatuan.value = satuan;

    // Cek limit harga — tampilkan warning jika biaya melebihi limit
    if (biayaInput) checkPartLimit(biayaInput, idx, price);
}

function checkPartLimit(input, idx, limitPrice) {
    const warn = document.getElementById(`part_limit_warn_${idx}`);
    if (!warn) return;
    const biaya = parseInt(input.value) || 0;
    const limitEl = document.getElementById(`part_category_${idx}`);
    const lp = limitPrice ?? parseInt(limitEl?.options[limitEl.selectedIndex]?.dataset?.limitPrice || 0);
    if (lp > 0 && biaya > lp) {
        warn.textContent = `⚠ Biaya melebihi service limit (Rp ${lp.toLocaleString('id-ID')}). Kendaraan sudah limit service.`;
        warn.classList.remove('hidden');
    } else {
        warn.classList.add('hidden');
    }
    recalcPartsTotal();
}

// ── Tambah row ITEM (belanja) ─────────────────────────────────
function addItemRow(data = null) {
    const container = document.getElementById('items-container');
    const hint      = document.getElementById('empty-items-hint');
    const idx       = itemIndex++;
    if (hint) hint.style.display = 'none';

    const row = document.createElement('div');
    row.className = 'item-row bg-gray-50 border border-gray-200 rounded-xl p-4';
    row.id = `item-row-${idx}`;
    row.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-600">Item #${idx + 1}</span>
            <button type="button" onclick="removeItemRow(${idx})"
                class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Barang/Jasa <span class="text-red-400">*</span></label>
                <input type="text" name="items[${idx}][nama_barang]" required value="${data?.nama_barang || ''}"
                    placeholder="Nama barang atau jasa"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">QTY <span class="text-red-400">*</span></label>
                <input type="number" name="items[${idx}][qty]" id="qty-${idx}" required min="1" value="${data?.qty || 1}"
                    onchange="calculateSubtotal(${idx})" oninput="calculateSubtotal(${idx})"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Satuan</label>
                <input type="text" name="items[${idx}][satuan]" value="${data?.satuan || ''}" placeholder="pcs, unit, set"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Harga Satuan</label>
                <input type="number" name="items[${idx}][harga_satuan]" id="harga-${idx}" min="0" step="0.01" value="${data?.harga_satuan || 0}"
                    onchange="calculateSubtotal(${idx})" oninput="calculateSubtotal(${idx})"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="hidden">
                <input type="number" name="items[${idx}][subtotal]" id="subtotal-${idx}" readonly value="${data?.subtotal || 0}">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Merk/Brand</label>
                <input type="text" name="items[${idx}][merk]" value="${data?.merk || ''}" placeholder="Dell, HP, Canon"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Keterangan</label>
                <input type="text" name="items[${idx}][keterangan]" value="${data?.keterangan || ''}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Bukti/Lampiran</label>
                <input type="file" name="items[${idx}][bukti]" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>`;
    container.appendChild(row);
    if (data?.qty && data?.harga_satuan) calculateSubtotal(idx);

    // Disable jika section sedang tersembunyi
    if (document.getElementById('section_belanja').classList.contains('hidden')) {
        row.querySelectorAll('input[required], textarea[required]').forEach(el => el.disabled = true);
    }
}

function removeItemRow(idx) {
    const row = document.getElementById(`item-row-${idx}`);
    if (row) { row.remove(); recalcTotal(); }
    const hint = document.getElementById('empty-items-hint');
    if (document.getElementById('items-container').children.length === 0 && hint) hint.style.display = 'block';
}

function calculateSubtotal(idx) {
    const qty      = parseFloat(document.getElementById(`qty-${idx}`)?.value) || 0;
    const harga    = parseFloat(document.getElementById(`harga-${idx}`)?.value) || 0;
    const subtotal = qty * harga;
    const el = document.getElementById(`subtotal-${idx}`);
    if (el) el.value = subtotal;
    recalcTotal();
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('[name$="[subtotal]"]').forEach(i => { total += parseFloat(i.value) || 0; });
    document.getElementById('total_display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_nominal_input').value = total;
}

// ── Tambah row PART (service) ─────────────────────────────────
function addPartRow(data = null) {
    const container = document.getElementById('parts-container');
    const hint      = document.getElementById('empty-parts-hint');
    const idx       = partIndex++;
    if (hint) hint.style.display = 'none';

    const today = new Date().toISOString().split('T')[0];
    const kmNow = document.getElementById('input_kilometer')?.value || 0;

    const row = document.createElement('div');
    row.className = 'item-row bg-orange-50 border border-orange-200 rounded-xl p-4';
    row.id = `part-row-${idx}`;
    row.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-orange-700">Part #${idx + 1}</span>
            <button type="button" onclick="removePartRow(${idx})"
                class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Nama Part <span class="text-red-400">*</span></label>
                <input type="text" name="parts[${idx}][nama_part]" required value="${data?.nama_part || ''}"
                    placeholder="cth: Ban Depan, Oli Mesin..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Service Limit (Kategori)
                    <span class="text-blue-500 text-[10px] ml-1"><i class="fa fa-info-circle"></i> Auto dari kategori</span>
                </label>
                <select id="part_category_${idx}" name="parts[${idx}][category_id]"
                    class="part-category-select w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100"
                    onchange="onCategoryChange(this, ${idx})">
                    <option value="">-- Pilih Kategori --</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Posisi</label>
                <select name="parts[${idx}][posisi]"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <option value="">-- Pilih Posisi --</option>
                    ${['Depan','Belakang','Kiri','Kanan','Depan Kiri','Depan Kanan','Belakang Kiri','Belakang Kanan','Semua'].map(p => `<option value="${p}">${p}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Part Number</label>
                <input type="text" name="parts[${idx}][part_number]" value="${data?.part_number || ''}" placeholder="No. part (opsional)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Serial Number</label>
                <input type="text" name="parts[${idx}][serial_number]" value="${data?.serial_number || ''}" placeholder="SN part baru"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Tgl Pasang <span class="text-red-400">*</span></label>
                <input type="date" name="parts[${idx}][tgl_pasang]" required value="${data?.tgl_pasang || today}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">KM Pasang <span class="text-red-400">*</span></label>
                <input type="number" name="parts[${idx}][kilometer_pasang]" required value="${data?.kilometer_pasang || kmNow}" min="0"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Interval <span class="text-red-400">*</span></label>
                <div class="flex gap-2">
                    <input type="number" name="parts[${idx}][interval_nilai]" id="part_interval_nilai_${idx}" required min="1" value="${data?.interval_nilai || 12}"
                        class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <select name="parts[${idx}][interval_satuan]" id="part_interval_satuan_${idx}"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                        ${['hari','minggu','bulan','tahun'].map(s => `<option value="${s}" ${(data?.interval_satuan||'bulan')===s?'selected':''}>${s.charAt(0).toUpperCase()+s.slice(1)}</option>`).join('')}
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Kondisi</label>
                <select name="parts[${idx}][kondisi]"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <option value="Baik">Baik</option>
                    <option value="Rusak">Rusak</option>
                    <option value="Perlu Ganti">Perlu Ganti</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Merk/Brand</label>
                <input type="text" name="parts[${idx}][merk]" value="${data?.merk || ''}" placeholder="Merk part"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Biaya (Rp) <span class="text-red-400">*</span></label>
                <input type="number" name="parts[${idx}][biaya]" id="part_biaya_${idx}" required min="0" value="${data?.biaya || 0}"
                    oninput="checkPartLimit(this, ${idx})"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                <p id="part_limit_warn_${idx}" class="hidden text-xs text-red-500 mt-1 font-medium"></p>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Keterangan</label>
                <textarea name="parts[${idx}][keterangan]" rows="2" placeholder="Catatan kondisi, alasan ganti, dll..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 resize-none">${data?.keterangan || ''}</textarea>
            </div>
        </div>`;

    container.appendChild(row);

    // Init kategori dropdown
    const catSel = row.querySelector('.part-category-select');
    if (catSel) {
        updateCategoryOptions(catSel);
        if (data?.category_id) catSel.value = data.category_id;
    }

    // Disable jika section sedang tersembunyi
    if (document.getElementById('section_parts').classList.contains('hidden')) {
        row.querySelectorAll('input[required], textarea[required]').forEach(el => el.disabled = true);
    }
}

function removePartRow(idx) {
    const row = document.getElementById(`part-row-${idx}`);
    if (row) { row.remove(); recalcPartsTotal(); }
    const hint = document.getElementById('empty-parts-hint');
    if (document.getElementById('parts-container').children.length === 0 && hint) hint.style.display = 'block';
}

function recalcPartsTotal() {
    let total = 0;
    document.querySelectorAll('[name$="[biaya]"]').forEach(i => { total += parseInt(i.value) || 0; });
    const el = document.getElementById('total_parts_display');
    if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    let tipeAktif = 'belanja';
    const radioChecked = document.querySelector('input[name="tipe_pembayaran"]:checked');
    const selectTipe   = document.getElementById('tipe_pembayaran');
    const hiddenTipe   = document.querySelector('input[type="hidden"][name="tipe_pembayaran"]');

    if (radioChecked)      tipeAktif = radioChecked.value;
    else if (selectTipe)   tipeAktif = selectTipe.value;
    else if (hiddenTipe)   tipeAktif = hiddenTipe.value;

    if (tipeAktif === 'service') {
        onTipePembayaranChange('service');
        loadKendaraan();
        addPartRow();
    } else {
        addItemRow();
        // Pastikan section service ter-disable dari awal
        onTipePembayaranChange('belanja');
    }
});

// ── Modal Supplier Functions ──────────────────────────────────
function openSupplierModal() {
    document.getElementById('supplierModal').classList.remove('hidden');
    document.getElementById('supplierForm').reset();
}

function closeSupplierModal() {
    document.getElementById('supplierModal').classList.add('hidden');
    document.getElementById('supplierForm').reset();
    document.getElementById('supplierError').classList.add('hidden');
}

function submitSupplier() {
    const form = document.getElementById('supplierForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('supplierSubmitBtn');
    const errorDiv = document.getElementById('supplierError');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Menyimpan...';
    errorDiv.classList.add('hidden');

    fetch('{{ route("supplier.api.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('supplier_id');
            const option = new Option(data.data.nama_supplier, data.data.id, true, true);
            select.add(option);
            closeSupplierModal();
            alert('Supplier berhasil ditambahkan!');
        } else {
            throw new Error(data.message || 'Gagal menambahkan supplier');
        }
    })
    .catch(error => {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('hidden');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa fa-save mr-2"></i>Simpan';
    });
}
</script>


@endsection

<script>
// ── Override recalcPartsTotal dengan versi yang update summary warning ──
(function() {
    const _orig = window.recalcPartsTotal;
    window.recalcPartsTotal = function() {
        let total = 0;
        document.querySelectorAll('[name$="[biaya]"]').forEach(function(i) { total += parseInt(i.value) || 0; });
        const el = document.getElementById('total_parts_display');
        if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');

        // Update summary warning di bawah total
        const summaryDiv  = document.getElementById('parts_over_limit_summary');
        const summaryList = document.getElementById('parts_over_limit_list');
        if (!summaryDiv || !summaryList) return;

        const overItems = [];
        document.querySelectorAll('[id^="part_limit_warn_"]').forEach(function(warnEl) {
            if (!warnEl.classList.contains('hidden') && warnEl.textContent.trim()) {
                const idx2 = warnEl.id.replace('part_limit_warn_', '');
                const nameEl = document.querySelector('[name="parts[' + idx2 + '][nama_part]"]');
                const namaPart = nameEl ? nameEl.value : 'Part #' + (parseInt(idx2) + 1);
                overItems.push(namaPart + ' — ' + warnEl.textContent.trim());
            }
        });

        if (overItems.length > 0) {
            summaryList.innerHTML = overItems.map(function(t) { return '<li>' + t + '</li>'; }).join('');
            summaryDiv.classList.remove('hidden');
        } else {
            summaryDiv.classList.add('hidden');
            summaryList.innerHTML = '';
        }
    };
})();
</script>
