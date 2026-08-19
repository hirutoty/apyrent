@extends('admin.layouts.app')

@section('title', 'Request Part')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6 max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('service-history.index') }}"
            class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">
            <i class="fa fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Request Part</h1>
            <p class="text-xs text-gray-500 mt-0.5">Request akan menunggu approval sebelum diproses</p>
        </div>
    </div>

    {{-- INFO NOTICE --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <i class="fa fa-info-circle text-amber-500 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-800">Request Part — Menunggu Approval</p>
            <p class="text-xs text-amber-700 mt-0.5">Data akan masuk ke service history dengan status <strong>Pending</strong> hingga disetujui.</p>
        </div>
    </div>

    <form action="{{ route('service-history.request.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SECTION 1: HEADER SERVICE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">1</span>
                Informasi Service
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Kendaraan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="kendaraan_id" id="kendaraan_id" required
                        onchange="onKendaraanChange(this.value)"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 {{ $errors->has('kendaraan_id') ? 'border-red-400' : '' }}">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}"
                                data-km="{{ $k->kilometer_sekarang ?? 0 }}"
                                data-merk="{{ $k->merk }}"
                                data-nopol="{{ $k->nopol }}"
                                {{ (old('kendaraan_id', $prefill['kendaraan_id'] ?? '') == $k->id) ? 'selected' : '' }}>
                                {{ $k->merk }} — {{ $k->nopol }}
                            </option>
                        @endforeach
                    </select>
                    @error('kendaraan_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal Service --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Service <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_service" required
                        value="{{ old('tanggal_service', $prefill['tanggal_service'] ?? now()->format('Y-m-d')) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('tanggal_service')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Kilometer --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kilometer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="kilometer" id="kilometer" required
                            value="{{ old('kilometer', $prefill['kilometer'] ?? '') }}" placeholder="Auto dari kendaraan"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 pr-24">
                        <span id="km-badge" class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded px-1.5 py-0.5">
                            <i class="fa fa-database text-[9px]"></i> Auto
                        </span>
                    </div>
                    @error('kilometer')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Keluhan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keluhan</label>
                    <textarea name="keluhan" rows="2" placeholder="Deskripsikan keluhan kendaraan..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('keluhan', $prefill['keluhan'] ?? '') }}</textarea>
                </div>

                {{-- Bukti Pembayaran --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                {{-- Lampiran --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lampiran Tambahan</label>
                    <input type="file" name="bukti_attachment[]" multiple
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                </div>

            </div>
        </div>

        {{-- SECTION 2: PARTS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">2</span>
                    Part / Komponen yang Dipasang
                </h2>
                <button type="button" onclick="addPartRow()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fa fa-plus text-xs"></i> Tambah Part
                </button>
            </div>

            {{-- Total biaya override --}}
            <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="override_biaya" onchange="toggleOverrideBiaya(this)"
                        class="rounded text-blue-600">
                    <label for="override_biaya" class="text-xs font-semibold text-gray-600">Override Total Biaya</label>
                </div>
                <div class="text-xs text-gray-400">Auto-sum dari biaya per part:</div>
                <div class="font-bold text-sm text-gray-800" id="sum_biaya_display">Rp 0</div>
                <div id="override_biaya_wrap" class="hidden ml-auto">
                    <input type="number" name="total_biaya_override" id="total_biaya_override"
                        placeholder="Masukkan total manual"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            @error('parts')<div class="text-xs text-red-500">{{ $message }}</div>@enderror

            {{-- Parts container --}}
            <div id="parts-container" class="space-y-4">
                {{-- Rows will be added via JS --}}
            </div>

            <div id="empty-parts-hint" class="text-center py-6 text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                <i class="fa fa-cogs text-gray-300 text-2xl mb-2 block"></i>
                Klik "+ Tambah Part" untuk menambahkan part/komponen yang dipasang
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('service-history.index') }}"
                class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors">
                <i class="fa fa-paper-plane text-sm"></i>
                Kirim Request
            </button>
        </div>

    </form>
</div>

{{-- MODAL TAMBAH KATEGORI INLINE --}}
<div id="modalKategori" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4">
        <h2 class="text-base font-bold text-gray-800">Tambah Kategori Baru</h2>
        <input type="text" id="input_nama_kategori" placeholder="Nama kategori (misal: Kaki-kaki)"
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
        <div id="kategori_error" class="hidden text-xs text-red-500"></div>
        <div class="flex gap-3">
            <button onclick="closeModalKategori()" class="flex-1 border border-gray-200 text-gray-600 text-sm py-2.5 rounded-xl hover:bg-gray-50">Batal</button>
            <button onclick="submitKategoriBaru()" class="flex-1 bg-blue-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-blue-700">Simpan</button>
        </div>
    </div>
</div>

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.part-row { animation: slideUp 0.15s ease; }
</style>

<script>
// ── Data dari blade ──────────────────────────────────────────
const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'nama' => $c->nama]));
const prefillData = null;
let partIndex = 0;
let _currentCategoryTarget = null; // select yang trigger modal kategori

// ── Tambah row part ──────────────────────────────────────────
function addPartRow(data = null) {
    const container = document.getElementById('parts-container');
    const hint      = document.getElementById('empty-parts-hint');
    hint.style.display = 'none';

    const idx  = partIndex++;
    const row  = document.createElement('div');
    row.className   = 'part-row bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3 relative';
    row.id          = 'part-row-' + idx;

    // Build options kategori
    let catOptions = '<option value="">— Pilih Kategori —</option>';
    categories.forEach(c => {
        catOptions += `<option value="${c.id}" ${data?.category_id == c.id ? 'selected' : ''}>${c.nama}</option>`;
    });

    const tglPasang = data?.tgl_pasang || '{{ now()->format("Y-m-d") }}';
    const kmPasang  = data?.kilometer_pasang || '';

    row.innerHTML = `
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-bold text-gray-600">Part #${idx + 1}</span>
            <button type="button" onclick="removePartRow(${idx})"
                class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">

            <!-- Nama Part -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Part <span class="text-red-400">*</span></label>
                <input type="text" name="parts[${idx}][nama_part]" required
                    value="${data?.nama_part || ''}"
                    placeholder="cth: Ban Depan, Oli Mesin..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Kategori -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Kategori</label>
                <select name="parts[${idx}][category_id]" id="cat-select-${idx}"
                    onchange="onCategoryChange(this, ${idx})"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    ${catOptions}
                </select>
                <input type="hidden" name="parts[${idx}][nama_category_baru]" id="cat-new-${idx}" value="">
            </div>

            <!-- Posisi -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Posisi</label>
                <select name="parts[${idx}][posisi]"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Pilih Posisi --</option>
                    <optgroup label="🚗 Eksterior Depan">
                        <option value="Bumper Depan">Bumper Depan</option>
                        <option value="Grill Depan">Grill Depan</option>
                        <option value="Kap Mesin">Kap Mesin</option>
                        <option value="Lampu Depan Kiri">Lampu Depan Kiri</option>
                        <option value="Lampu Depan Kanan">Lampu Depan Kanan</option>
                        <option value="Fog Lamp Kiri">Fog Lamp Kiri</option>
                        <option value="Fog Lamp Kanan">Fog Lamp Kanan</option>
                        <option value="Spion Kiri">Spion Kiri</option>
                        <option value="Spion Kanan">Spion Kanan</option>
                    </optgroup>
                    <optgroup label="🚗 Eksterior Samping">
                        <option value="Pintu Depan Kiri">Pintu Depan Kiri</option>
                        <option value="Pintu Depan Kanan">Pintu Depan Kanan</option>
                        <option value="Pintu Belakang Kiri">Pintu Belakang Kiri</option>
                        <option value="Pintu Belakang Kanan">Pintu Belakang Kanan</option>
                        <option value="Fender Depan Kiri">Fender Depan Kiri</option>
                        <option value="Fender Depan Kanan">Fender Depan Kanan</option>
                        <option value="Running Board Kiri">Running Board Kiri</option>
                        <option value="Running Board Kanan">Running Board Kanan</option>
                    </optgroup>
                    <optgroup label="🚗 Eksterior Belakang">
                        <option value="Bumper Belakang">Bumper Belakang</option>
                        <option value="Bagasi">Bagasi</option>
                        <option value="Lampu Belakang Kiri">Lampu Belakang Kiri</option>
                        <option value="Lampu Belakang Kanan">Lampu Belakang Kanan</option>
                        <option value="Wiper Belakang">Wiper Belakang</option>
                    </optgroup>
                    <optgroup label="🚗 Atap & Kaca">
                        <option value="Atap">Atap</option>
                        <option value="Sunroof">Sunroof</option>
                        <option value="Kaca Depan">Kaca Depan</option>
                        <option value="Kaca Belakang">Kaca Belakang</option>
                        <option value="Wiper Depan">Wiper Depan</option>
                    </optgroup>
                    <optgroup label="⚙️ Mesin">
                        <option value="Mesin">Mesin</option>
                        <option value="Radiator">Radiator</option>
                        <option value="Aki/Battery">Aki/Battery</option>
                        <option value="Busi">Busi</option>
                        <option value="Filter Udara">Filter Udara</option>
                        <option value="Filter Oli">Filter Oli</option>
                        <option value="Timing Belt">Timing Belt</option>
                        <option value="Fan Belt">Fan Belt</option>
                    </optgroup>
                    <optgroup label="🔧 Transmisi">
                        <option value="Transmisi">Transmisi</option>
                        <option value="Kopling">Kopling</option>
                        <option value="Gardan">Gardan</option>
                    </optgroup>
                    <optgroup label="🛞 Ban & Velg">
                        <option value="Ban Depan Kiri">Ban Depan Kiri</option>
                        <option value="Ban Depan Kanan">Ban Depan Kanan</option>
                        <option value="Ban Belakang Kiri">Ban Belakang Kiri</option>
                        <option value="Ban Belakang Kanan">Ban Belakang Kanan</option>
                        <option value="Ban Serep">Ban Serep</option>
                    </optgroup>
                    <optgroup label="🔩 Kaki-kaki">
                        <option value="Shock Absorber Depan Kiri">Shock Depan Kiri</option>
                        <option value="Shock Absorber Depan Kanan">Shock Depan Kanan</option>
                        <option value="Shock Absorber Belakang Kiri">Shock Belakang Kiri</option>
                        <option value="Shock Absorber Belakang Kanan">Shock Belakang Kanan</option>
                        <option value="Ball Joint Depan Kiri">Ball Joint Depan Kiri</option>
                        <option value="Ball Joint Depan Kanan">Ball Joint Depan Kanan</option>
                        <option value="Tie Rod Kiri">Tie Rod Kiri</option>
                        <option value="Tie Rod Kanan">Tie Rod Kanan</option>
                    </optgroup>
                    <optgroup label="🛑 Rem">
                        <option value="Brake Pad Depan">Brake Pad Depan</option>
                        <option value="Brake Pad Belakang">Brake Pad Belakang</option>
                        <option value="Disc Brake Depan">Disc Brake Depan</option>
                        <option value="Master Rem">Master Rem</option>
                        <option value="Minyak Rem">Minyak Rem</option>
                    </optgroup>
                    <optgroup label="❄️ AC & Interior">
                        <option value="Kompresor AC">Kompresor AC</option>
                        <option value="Blower AC">Blower AC</option>
                        <option value="Filter Cabin">Filter Cabin</option>
                        <option value="Freon AC">Freon AC</option>
                        <option value="Jok Depan">Jok Depan</option>
                        <option value="Jok Belakang">Jok Belakang</option>
                    </optgroup>
                    <optgroup label="🧴 Cairan">
                        <option value="Oli Mesin">Oli Mesin</option>
                        <option value="Oli Transmisi">Oli Transmisi</option>
                        <option value="Oli Gardan">Oli Gardan</option>
                        <option value="Coolant">Coolant/Air Radiator</option>
                    </optgroup>
                    <optgroup label="🔧 Lain-lain">
                        <option value="Umum">Umum</option>
                        <option value="Keseluruhan">Keseluruhan</option>
                    </optgroup>
                </select>
            </div>

            <!-- Part Number -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Part Number</label>
                <input type="text" name="parts[${idx}][part_number]"
                    value="${data?.part_number || ''}"
                    placeholder="No. part (opsional)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Serial Number -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Serial Number ${data ? '<span class="text-amber-500 text-[10px] font-normal">(perbarui!)</span>' : ''}
                </label>
                <input type="text" name="parts[${idx}][serial_number]"
                    value=""
                    placeholder="SN part baru"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 ${data ? 'border-amber-300 bg-amber-50' : ''}">
            </div>

            <!-- Tgl Pasang -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Tgl Pasang <span class="text-red-400">*</span>
                    ${data ? '<span class="text-amber-500 text-[10px] font-normal">(perbarui!)</span>' : ''}
                </label>
                <input type="date" name="parts[${idx}][tgl_pasang]" required
                    value="{{ now()->format('Y-m-d') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 ${data ? 'border-amber-300 bg-amber-50' : ''}">
            </div>

            <!-- KM Pasang -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">KM Pasang</label>
                <input type="number" name="parts[${idx}][kilometer_pasang]" id="km-pasang-${idx}"
                    value="${kmPasang}"
                    placeholder="Auto dari header KM"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Interval Nilai -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Interval <span class="text-red-400">*</span>
                    <span class="text-blue-500 text-[10px] font-normal ml-1">
                        <i class="fa fa-lock text-[9px]"></i> Auto dari kategori
                    </span>
                </label>
                <div class="flex gap-1 items-center">
                    <div id="interval-nilai-display-${idx}"
                        class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600 text-center">
                        ${data?.interval_nilai || 12}
                    </div>
                    <div id="interval-satuan-display-${idx}"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600">
                        ${ ({'hari':'Hari','minggu':'Minggu','bulan':'Bulan','tahun':'Tahun'})[data?.interval_satuan || 'bulan'] || 'Bulan' }
                    </div>
                    <input type="hidden" name="parts[${idx}][interval_nilai]" id="interval-nilai-${idx}"
                        value="${data?.interval_nilai || 12}">
                    <input type="hidden" name="parts[${idx}][interval_satuan]" id="interval-satuan-${idx}"
                        value="${(data?.interval_satuan && ['hari','minggu','bulan','tahun'].includes(data.interval_satuan)) ? data.interval_satuan : 'bulan'}">
                    <select id="interval-satuan-select-${idx}" class="hidden" disabled>
                        <option value="hari"   ${(data?.interval_satuan||'') === 'hari'   ? 'selected':''}>Hari</option>
                        <option value="minggu" ${(data?.interval_satuan||'') === 'minggu' ? 'selected':''}>Minggu</option>
                        <option value="bulan"  ${(!data?.interval_satuan||data?.interval_satuan==='bulan') ? 'selected':''}>Bulan</option>
                        <option value="tahun"  ${(data?.interval_satuan||'') === 'tahun'  ? 'selected':''}>Tahun</option>
                    </select>
                </div>
                <p id="interval-hint-${idx}" class="text-[10px] text-blue-500 mt-1 hidden">
                    <i class="fa fa-circle-info text-[9px]"></i> Auto-fill dari limit rule kategori
                </p>
            </div>

            <!-- Kondisi -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Kondisi</label>
                <select name="parts[${idx}][kondisi]"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="Baik">Baik</option>
                    <option value="Rusak">Rusak</option>
                    <option value="Perlu Ganti">Perlu Ganti</option>
                </select>
            </div>

            <!-- Status Part -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Status Part</label>
                <select name="parts[${idx}][status]"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="Proses" ${data?.status === 'Proses' ? 'selected' : 'selected'}>Proses</option>
                    <option value="Terpasang" ${data?.status === 'Terpasang' ? 'selected' : ''}>Terpasang</option>
                </select>
            </div>

            <!-- Biaya -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Biaya (Rp)</label>
                <input type="number" name="parts[${idx}][biaya]" id="biaya-${idx}" min="0"
                    value="${data?.biaya || 0}"
                    onchange="recalcTotal()" oninput="recalcTotal()"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                <p id="biaya-hint-${idx}" class="text-[10px] text-gray-400 mt-1 hidden"></p>
            </div>

            <!-- Keterangan (full width) -->
            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Keterangan</label>
                <textarea name="parts[${idx}][keterangan]" rows="2"
                    placeholder="Catatan kondisi, alasan ganti, dll..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none">${data?.keterangan || ''}</textarea>
            </div>

            <!-- Bukti (full width) -->
            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Bukti (Foto/Video)</label>
                <input type="file" name="parts[${idx}][bukti][]" multiple accept="image/*,video/mp4,video/mov"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, MP4, MOV</p>
            </div>

        </div>
    `;

    container.appendChild(row);

    // Auto-fill km pasang dari header KM jika belum ada
    syncKmPasang(idx);

    recalcTotal();
}

function removePartRow(idx) {
    const row = document.getElementById('part-row-' + idx);
    if (row) row.remove();
    recalcTotal();

    // Tampilkan hint jika tidak ada part
    const container = document.getElementById('parts-container');
    if (container.children.length === 0) {
        document.getElementById('empty-parts-hint').style.display = '';
    }
}

// Auto-fill KM pasang dari field kilometer header
function syncKmPasang(idx) {
    const headerKm = document.getElementById('kilometer').value;
    const kmInput  = document.getElementById('km-pasang-' + idx);
    if (kmInput && !kmInput.value && headerKm) {
        kmInput.value = headerKm;
    }
}

// ── Kategori inline ───────────────────────────────────────────
function onCategoryChange(select, idx) {
    if (select.value === '__new__') {
        _currentCategoryTarget = { select, idx };
        document.getElementById('input_nama_kategori').value = '';
        document.getElementById('kategori_error').classList.add('hidden');
        const m = document.getElementById('modalKategori');
        m.classList.remove('hidden'); m.classList.add('flex');
        return;
    }
    // Auto-fill limit rule
    const kendaraanId = document.getElementById('kendaraan_id').value;
    if (kendaraanId && select.value) {
        fetchLimitRule(kendaraanId, select.value, idx);
    }
}

// ── Fetch limit rule dari server lalu auto-fill interval & hint harga ─────
function fetchLimitRule(kendaraanId, categoryId, idx) {
    if (!kendaraanId || !categoryId || categoryId === '__new__') return;
    fetch('{{ route("service-categories.limit-for") }}?kendaraan_id=' + kendaraanId + '&category_id=' + categoryId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var labelMap = {hari:'Hari', minggu:'Minggu', bulan:'Bulan', tahun:'Tahun'};
        var h  = document.getElementById('interval-hint-' + idx);
        var bh = document.getElementById('biaya-hint-' + idx);
        var nilaiHidden   = document.getElementById('interval-nilai-' + idx);
        var satuanHidden  = document.getElementById('interval-satuan-' + idx);
        var nilaiDisplay  = document.getElementById('interval-nilai-display-' + idx);
        var satuanDisplay = document.getElementById('interval-satuan-display-' + idx);

        if (!data) {
            if (h)  { h.classList.add('hidden'); }
            if (bh) { bh.classList.add('hidden'); }
            if (nilaiHidden)  { nilaiHidden.value  = 12; }
            if (satuanHidden) { satuanHidden.value = 'bulan'; }
            if (nilaiDisplay)  { nilaiDisplay.textContent  = '12'; }
            if (satuanDisplay) { satuanDisplay.textContent = 'Bulan'; }
            return;
        }
        var validSatuan = ['hari','minggu','bulan','tahun'].includes(data.limit_satuan)
            ? data.limit_satuan : 'bulan';
        if (nilaiHidden)  { nilaiHidden.value  = data.limit_nilai || 12; }
        if (satuanHidden) { satuanHidden.value = validSatuan; }
        if (nilaiDisplay)  { nilaiDisplay.textContent  = data.limit_nilai || 12; }
        if (satuanDisplay) { satuanDisplay.textContent = labelMap[validSatuan] || 'Bulan'; }
        if (h)        { h.classList.remove('hidden'); }
        if (bh) {
            if (data.limit_price) {
                bh.innerHTML = '<i class="fa fa-triangle-exclamation text-[9px] text-amber-500"></i>'
                    + ' Batas harga kategori ini: <strong class="text-amber-600">'
                    + data.limit_price_formatted + '</strong>';
                bh.classList.remove('hidden');
            } else {
                bh.classList.add('hidden');
            }
        }
    })
    .catch(function() {});
}

function closeModalKategori() {
    const m = document.getElementById('modalKategori');
    m.classList.add('hidden'); m.classList.remove('flex');
    if (_currentCategoryTarget) {
        _currentCategoryTarget.select.value = '';
    }
}

function submitKategoriBaru() {
    const nama = document.getElementById('input_nama_kategori').value.trim();
    if (!nama) {
        document.getElementById('kategori_error').textContent = 'Nama kategori wajib diisi.';
        document.getElementById('kategori_error').classList.remove('hidden');
        return;
    }

    fetch('{{ route("service-history.storeCategory") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ nama })
    })
    .then(r => r.json())
    .then(data => {
        if (data.id) {
            // Tambah ke semua dropdown kategori yang ada
            const option = new Option(data.nama, data.id, true, true);
            categories.push({ id: data.id, nama: data.nama });

            document.querySelectorAll('[id^="cat-select-"]').forEach(sel => {
                // Tambah sebelum option "__new__"
                const newOpt = new Option(data.nama, data.id);
                const lastOpt = Array.from(sel.options).find(o => o.value === '__new__');
                if (lastOpt) sel.insertBefore(newOpt, lastOpt);
            });

            // Set nilai ke select yang trigger
            if (_currentCategoryTarget) {
                _currentCategoryTarget.select.value = data.id;
            }

            closeModalKategori();
        } else {
            document.getElementById('kategori_error').textContent = data.message || 'Gagal menyimpan kategori.';
            document.getElementById('kategori_error').classList.remove('hidden');
        }
    })
    .catch(() => {
        document.getElementById('kategori_error').textContent = 'Terjadi kesalahan, coba lagi.';
        document.getElementById('kategori_error').classList.remove('hidden');
    });
}

// ── Total biaya auto-sum ──────────────────────────────────────
function recalcTotal() {
    let sum = 0;
    document.querySelectorAll('[name$="[biaya]"]').forEach(input => {
        sum += parseInt(input.value || 0);
    });
    document.getElementById('sum_biaya_display').textContent = 'Rp ' + sum.toLocaleString('id-ID');
}

function toggleOverrideBiaya(cb) {
    const wrap = document.getElementById('override_biaya_wrap');
    wrap.classList.toggle('hidden', !cb.checked);
    if (!cb.checked) document.getElementById('total_biaya_override').value = '';
}

// ── KM auto-fill dari kendaraan ───────────────────────────────
function onKendaraanChange(val) {
    const sel = document.getElementById('kendaraan_id');
    const opt = Array.from(sel.options).find(o => o.value == val);
    if (opt) {
        const km = opt.dataset.km || 0;
        document.getElementById('kilometer').value = km;
        document.getElementById('km-badge').classList.remove('hidden');
    } else {
        document.getElementById('km-badge').classList.add('hidden');
    }
    // Re-fetch limit rules untuk semua part row yang sudah ada
    if (val) {
        document.querySelectorAll('[id^="cat-select-"]').forEach(function(catSel) {
            var idxMatch = catSel.id.match(/cat-select-(\d+)/);
            if (!idxMatch) return;
            var rowIdx = idxMatch[1];
            if (catSel.value && catSel.value !== '__new__') {
                fetchLimitRule(val, catSel.value, rowIdx);
            }
        });
    }
}

document.getElementById('kilometer').addEventListener('input', function() {
    document.getElementById('km-badge').classList.add('hidden');
    // Sync semua km-pasang yang kosong
    document.querySelectorAll('[id^="km-pasang-"]').forEach(input => {
        if (!input.dataset.userEdited) input.value = this.value;
    });
});

// ── Init prefill (dari reminder) ─────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Request form — no prefill needed
});
</script>

@endsection
