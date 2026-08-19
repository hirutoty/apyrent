@extends('admin.layouts.app')

@section('title', 'Tambah Service')

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
            <h1 class="text-xl font-bold text-gray-800">
                {{ $prefill ? 'Selesaikan Reminder Service' : 'Tambah Service Kendaraan' }}
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $prefill ? 'Form pre-filled dari reminder — perbarui data part yang diganti' : 'Isi header service lalu tambahkan part yang dipasang' }}
            </p>
        </div>
    </div>

    {{-- PREFILL NOTICE --}}
    @if ($prefill)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 flex items-start gap-3">
            <i class="fa fa-bell text-amber-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-semibold text-amber-800">Dari Reminder: Part Limit</p>
                <p class="text-xs text-amber-700 mt-0.5">
                    Part <strong>{{ $prefill['part']['nama_part'] }}</strong> pada kendaraan
                    <strong>{{ $prefill['kendaraan']->merk }} — {{ $prefill['kendaraan']->nopol }}</strong>
                    telah melewati limit interval. Perbarui Serial Number dan Tanggal Pasang untuk part baru.
                </p>
            </div>
        </div>
        <input type="hidden" id="prefill_reminder_id" value="{{ $prefill['reminder_id'] }}">
        <input type="hidden" id="prefill_kendaraan_id" value="{{ $prefill['kendaraan_id'] }}">
    @endif

    <form action="{{ route('service-history.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($prefill)
            <input type="hidden" name="from_reminder" value="{{ $prefill['reminder_id'] }}">
            @if(isset($prefill['service_history_id']))
                <input type="hidden" name="service_history_id" value="{{ $prefill['service_history_id'] }}">
            @endif
        @endif

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
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors">
                <i class="fa fa-save text-sm"></i>
                {{ $prefill ? 'Simpan & Tutup Reminder' : 'Simpan Service' }}
            </button>
        </div>

    </form>
</div>


<style>
@keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.part-row { animation: slideUp 0.15s ease; }
</style>

<script>
// ── Data dari blade ──────────────────────────────────────────
const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'nama' => $c->nama]));
const prefillData = @json($prefill ? $prefill['part'] : null);
let partIndex = 0;

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
                        <option value="Bumper Depan" ${data?.posisi === 'Bumper Depan' ? 'selected' : ''}>Bumper Depan</option>
                        <option value="Grill Depan" ${data?.posisi === 'Grill Depan' ? 'selected' : ''}>Grill Depan</option>
                        <option value="Kap Mesin" ${data?.posisi === 'Kap Mesin' ? 'selected' : ''}>Kap Mesin</option>
                        <option value="Lampu Depan Kiri" ${data?.posisi === 'Lampu Depan Kiri' ? 'selected' : ''}>Lampu Depan Kiri</option>
                        <option value="Lampu Depan Kanan" ${data?.posisi === 'Lampu Depan Kanan' ? 'selected' : ''}>Lampu Depan Kanan</option>
                        <option value="Fog Lamp Kiri" ${data?.posisi === 'Fog Lamp Kiri' ? 'selected' : ''}>Fog Lamp Kiri</option>
                        <option value="Fog Lamp Kanan" ${data?.posisi === 'Fog Lamp Kanan' ? 'selected' : ''}>Fog Lamp Kanan</option>
                        <option value="Spion Kiri" ${data?.posisi === 'Spion Kiri' ? 'selected' : ''}>Spion Kiri</option>
                        <option value="Spion Kanan" ${data?.posisi === 'Spion Kanan' ? 'selected' : ''}>Spion Kanan</option>
                    </optgroup>
                    <optgroup label="🚗 Eksterior Samping">
                        <option value="Pintu Depan Kiri" ${data?.posisi === 'Pintu Depan Kiri' ? 'selected' : ''}>Pintu Depan Kiri</option>
                        <option value="Pintu Depan Kanan" ${data?.posisi === 'Pintu Depan Kanan' ? 'selected' : ''}>Pintu Depan Kanan</option>
                        <option value="Pintu Belakang Kiri" ${data?.posisi === 'Pintu Belakang Kiri' ? 'selected' : ''}>Pintu Belakang Kiri</option>
                        <option value="Pintu Belakang Kanan" ${data?.posisi === 'Pintu Belakang Kanan' ? 'selected' : ''}>Pintu Belakang Kanan</option>
                        <option value="Fender Depan Kiri" ${data?.posisi === 'Fender Depan Kiri' ? 'selected' : ''}>Fender Depan Kiri</option>
                        <option value="Fender Depan Kanan" ${data?.posisi === 'Fender Depan Kanan' ? 'selected' : ''}>Fender Depan Kanan</option>
                        <option value="Fender Belakang Kiri" ${data?.posisi === 'Fender Belakang Kiri' ? 'selected' : ''}>Fender Belakang Kiri</option>
                        <option value="Fender Belakang Kanan" ${data?.posisi === 'Fender Belakang Kanan' ? 'selected' : ''}>Fender Belakang Kanan</option>
                        <option value="Running Board Kiri" ${data?.posisi === 'Running Board Kiri' ? 'selected' : ''}>Running Board Kiri</option>
                        <option value="Running Board Kanan" ${data?.posisi === 'Running Board Kanan' ? 'selected' : ''}>Running Board Kanan</option>
                    </optgroup>
                    <optgroup label="🚗 Eksterior Belakang">
                        <option value="Bumper Belakang" ${data?.posisi === 'Bumper Belakang' ? 'selected' : ''}>Bumper Belakang</option>
                        <option value="Bagasi" ${data?.posisi === 'Bagasi' ? 'selected' : ''}>Bagasi</option>
                        <option value="Lampu Belakang Kiri" ${data?.posisi === 'Lampu Belakang Kiri' ? 'selected' : ''}>Lampu Belakang Kiri</option>
                        <option value="Lampu Belakang Kanan" ${data?.posisi === 'Lampu Belakang Kanan' ? 'selected' : ''}>Lampu Belakang Kanan</option>
                        <option value="Lampu Rem Atas" ${data?.posisi === 'Lampu Rem Atas' ? 'selected' : ''}>Lampu Rem Atas</option>
                        <option value="Wiper Belakang" ${data?.posisi === 'Wiper Belakang' ? 'selected' : ''}>Wiper Belakang</option>
                    </optgroup>
                    <optgroup label="🚗 Atap & Kaca">
                        <option value="Atap" ${data?.posisi === 'Atap' ? 'selected' : ''}>Atap</option>
                        <option value="Sunroof" ${data?.posisi === 'Sunroof' ? 'selected' : ''}>Sunroof</option>
                        <option value="Roof Rack" ${data?.posisi === 'Roof Rack' ? 'selected' : ''}>Roof Rack</option>
                        <option value="Kaca Depan" ${data?.posisi === 'Kaca Depan' ? 'selected' : ''}>Kaca Depan</option>
                        <option value="Kaca Belakang" ${data?.posisi === 'Kaca Belakang' ? 'selected' : ''}>Kaca Belakang</option>
                        <option value="Kaca Pintu Depan Kiri" ${data?.posisi === 'Kaca Pintu Depan Kiri' ? 'selected' : ''}>Kaca Pintu Depan Kiri</option>
                        <option value="Kaca Pintu Depan Kanan" ${data?.posisi === 'Kaca Pintu Depan Kanan' ? 'selected' : ''}>Kaca Pintu Depan Kanan</option>
                        <option value="Kaca Pintu Belakang Kiri" ${data?.posisi === 'Kaca Pintu Belakang Kiri' ? 'selected' : ''}>Kaca Pintu Belakang Kiri</option>
                        <option value="Kaca Pintu Belakang Kanan" ${data?.posisi === 'Kaca Pintu Belakang Kanan' ? 'selected' : ''}>Kaca Pintu Belakang Kanan</option>
                        <option value="Wiper Depan" ${data?.posisi === 'Wiper Depan' ? 'selected' : ''}>Wiper Depan</option>
                    </optgroup>
                    <optgroup label="⚙️ Mesin">
                        <option value="Mesin" ${data?.posisi === 'Mesin' ? 'selected' : ''}>Mesin</option>
                        <option value="Radiator" ${data?.posisi === 'Radiator' ? 'selected' : ''}>Radiator</option>
                        <option value="Kipas Radiator" ${data?.posisi === 'Kipas Radiator' ? 'selected' : ''}>Kipas Radiator</option>
                        <option value="Alternator" ${data?.posisi === 'Alternator' ? 'selected' : ''}>Alternator</option>
                        <option value="Starter" ${data?.posisi === 'Starter' ? 'selected' : ''}>Starter</option>
                        <option value="Aki/Battery" ${data?.posisi === 'Aki/Battery' ? 'selected' : ''}>Aki/Battery</option>
                        <option value="Busi" ${data?.posisi === 'Busi' ? 'selected' : ''}>Busi</option>
                        <option value="Koil" ${data?.posisi === 'Koil' ? 'selected' : ''}>Koil</option>
                        <option value="Filter Udara" ${data?.posisi === 'Filter Udara' ? 'selected' : ''}>Filter Udara</option>
                        <option value="Filter Oli" ${data?.posisi === 'Filter Oli' ? 'selected' : ''}>Filter Oli</option>
                        <option value="Filter Bensin" ${data?.posisi === 'Filter Bensin' ? 'selected' : ''}>Filter Bensin</option>
                        <option value="Timing Belt" ${data?.posisi === 'Timing Belt' ? 'selected' : ''}>Timing Belt</option>
                        <option value="Fan Belt" ${data?.posisi === 'Fan Belt' ? 'selected' : ''}>Fan Belt</option>
                        <option value="Drive Belt" ${data?.posisi === 'Drive Belt' ? 'selected' : ''}>Drive Belt</option>
                        <option value="Tensioner Belt" ${data?.posisi === 'Tensioner Belt' ? 'selected' : ''}>Tensioner Belt</option>
                        <option value="Water Pump" ${data?.posisi === 'Water Pump' ? 'selected' : ''}>Water Pump</option>
                        <option value="Thermostat" ${data?.posisi === 'Thermostat' ? 'selected' : ''}>Thermostat</option>
                        <option value="Injector" ${data?.posisi === 'Injector' ? 'selected' : ''}>Injector</option>
                        <option value="Throttle Body" ${data?.posisi === 'Throttle Body' ? 'selected' : ''}>Throttle Body</option>
                    </optgroup>
                    <optgroup label="🔧 Transmisi & Drivetrain">
                        <option value="Transmisi" ${data?.posisi === 'Transmisi' ? 'selected' : ''}>Transmisi</option>
                        <option value="Kopling" ${data?.posisi === 'Kopling' ? 'selected' : ''}>Kopling</option>
                        <option value="Gardan" ${data?.posisi === 'Gardan' ? 'selected' : ''}>Gardan</option>
                        <option value="CV Joint Depan Kiri" ${data?.posisi === 'CV Joint Depan Kiri' ? 'selected' : ''}>CV Joint Depan Kiri</option>
                        <option value="CV Joint Depan Kanan" ${data?.posisi === 'CV Joint Depan Kanan' ? 'selected' : ''}>CV Joint Depan Kanan</option>
                        <option value="CV Joint Belakang Kiri" ${data?.posisi === 'CV Joint Belakang Kiri' ? 'selected' : ''}>CV Joint Belakang Kiri</option>
                        <option value="CV Joint Belakang Kanan" ${data?.posisi === 'CV Joint Belakang Kanan' ? 'selected' : ''}>CV Joint Belakang Kanan</option>
                        <option value="Propeller Shaft" ${data?.posisi === 'Propeller Shaft' ? 'selected' : ''}>Propeller Shaft</option>
                        <option value="Transfer Case" ${data?.posisi === 'Transfer Case' ? 'selected' : ''}>Transfer Case (4WD)</option>
                    </optgroup>
                    <optgroup label="🛞 Ban & Velg">
                        <option value="Ban Depan Kiri" ${data?.posisi === 'Ban Depan Kiri' ? 'selected' : ''}>Ban Depan Kiri</option>
                        <option value="Ban Depan Kanan" ${data?.posisi === 'Ban Depan Kanan' ? 'selected' : ''}>Ban Depan Kanan</option>
                        <option value="Ban Belakang Kiri" ${data?.posisi === 'Ban Belakang Kiri' ? 'selected' : ''}>Ban Belakang Belakang Kiri</option>
                        <option value="Ban Belakang Kanan" ${data?.posisi === 'Ban Belakang Kanan' ? 'selected' : ''}>Ban Belakang Kanan</option>
                        <option value="Ban Serep" ${data?.posisi === 'Ban Serep' ? 'selected' : ''}>Ban Serep</option>
                        <option value="Velg Depan Kiri" ${data?.posisi === 'Velg Depan Kiri' ? 'selected' : ''}>Velg Depan Kiri</option>
                        <option value="Velg Depan Kanan" ${data?.posisi === 'Velg Depan Kanan' ? 'selected' : ''}>Velg Depan Kanan</option>
                        <option value="Velg Belakang Kiri" ${data?.posisi === 'Velg Belakang Kiri' ? 'selected' : ''}>Velg Belakang Kiri</option>
                        <option value="Velg Belakang Kanan" ${data?.posisi === 'Velg Belakang Kanan' ? 'selected' : ''}>Velg Belakang Kanan</option>
                    </optgroup>
                    <optgroup label="🔩 Kaki-kaki & Suspensi">
                        <option value="Shock Absorber Depan Kiri" ${data?.posisi === 'Shock Absorber Depan Kiri' ? 'selected' : ''}>Shock Absorber Depan Kiri</option>
                        <option value="Shock Absorber Depan Kanan" ${data?.posisi === 'Shock Absorber Depan Kanan' ? 'selected' : ''}>Shock Absorber Depan Kanan</option>
                        <option value="Shock Absorber Belakang Kiri" ${data?.posisi === 'Shock Absorber Belakang Kiri' ? 'selected' : ''}>Shock Absorber Belakang Kiri</option>
                        <option value="Shock Absorber Belakang Kanan" ${data?.posisi === 'Shock Absorber Belakang Kanan' ? 'selected' : ''}>Shock Absorber Belakang Kanan</option>
                        <option value="Per Depan Kiri" ${data?.posisi === 'Per Depan Kiri' ? 'selected' : ''}>Per Depan Kiri</option>
                        <option value="Per Depan Kanan" ${data?.posisi === 'Per Depan Kanan' ? 'selected' : ''}>Per Depan Kanan</option>
                        <option value="Per Belakang Kiri" ${data?.posisi === 'Per Belakang Kiri' ? 'selected' : ''}>Per Belakang Kiri</option>
                        <option value="Per Belakang Kanan" ${data?.posisi === 'Per Belakang Kanan' ? 'selected' : ''}>Per Belakang Kanan</option>
                        <option value="Lower Arm Depan Kiri" ${data?.posisi === 'Lower Arm Depan Kiri' ? 'selected' : ''}>Lower Arm Depan Kiri</option>
                        <option value="Lower Arm Depan Kanan" ${data?.posisi === 'Lower Arm Depan Kanan' ? 'selected' : ''}>Lower Arm Depan Kanan</option>
                        <option value="Upper Arm Depan Kiri" ${data?.posisi === 'Upper Arm Depan Kiri' ? 'selected' : ''}>Upper Arm Depan Kiri</option>
                        <option value="Upper Arm Depan Kanan" ${data?.posisi === 'Upper Arm Depan Kanan' ? 'selected' : ''}>Upper Arm Depan Kanan</option>
                        <option value="Tie Rod Depan Kiri" ${data?.posisi === 'Tie Rod Depan Kiri' ? 'selected' : ''}>Tie Rod Depan Kiri</option>
                        <option value="Tie Rod Depan Kanan" ${data?.posisi === 'Tie Rod Depan Kanan' ? 'selected' : ''}>Tie Rod Depan Kanan</option>
                        <option value="Long Tie Rod" ${data?.posisi === 'Long Tie Rod' ? 'selected' : ''}>Long Tie Rod</option>
                        <option value="Ball Joint Atas Kiri" ${data?.posisi === 'Ball Joint Atas Kiri' ? 'selected' : ''}>Ball Joint Atas Kiri</option>
                        <option value="Ball Joint Atas Kanan" ${data?.posisi === 'Ball Joint Atas Kanan' ? 'selected' : ''}>Ball Joint Atas Kanan</option>
                        <option value="Ball Joint Bawah Kiri" ${data?.posisi === 'Ball Joint Bawah Kiri' ? 'selected' : ''}>Ball Joint Bawah Kiri</option>
                        <option value="Ball Joint Bawah Kanan" ${data?.posisi === 'Ball Joint Bawah Kanan' ? 'selected' : ''}>Ball Joint Bawah Kanan</option>
                        <option value="Stabilizer Link Depan Kiri" ${data?.posisi === 'Stabilizer Link Depan Kiri' ? 'selected' : ''}>Stabilizer Link Depan Kiri</option>
                        <option value="Stabilizer Link Depan Kanan" ${data?.posisi === 'Stabilizer Link Depan Kanan' ? 'selected' : ''}>Stabilizer Link Depan Kanan</option>
                        <option value="Bushing Stabilizer Depan" ${data?.posisi === 'Bushing Stabilizer Depan' ? 'selected' : ''}>Bushing Stabilizer Depan</option>
                        <option value="Bushing Stabilizer Belakang" ${data?.posisi === 'Bushing Stabilizer Belakang' ? 'selected' : ''}>Bushing Stabilizer Belakang</option>
                    </optgroup>
                    <optgroup label="🛑 Rem">
                        <option value="Brake Pad Depan" ${data?.posisi === 'Brake Pad Depan' ? 'selected' : ''}>Brake Pad Depan</option>
                        <option value="Brake Pad Belakang" ${data?.posisi === 'Brake Pad Belakang' ? 'selected' : ''}>Brake Pad Belakang</option>
                        <option value="Brake Shoe Belakang" ${data?.posisi === 'Brake Shoe Belakang' ? 'selected' : ''}>Brake Shoe Belakang</option>
                        <option value="Disc Brake Depan Kiri" ${data?.posisi === 'Disc Brake Depan Kiri' ? 'selected' : ''}>Disc Brake Depan Kiri</option>
                        <option value="Disc Brake Depan Kanan" ${data?.posisi === 'Disc Brake Depan Kanan' ? 'selected' : ''}>Disc Brake Depan Kanan</option>
                        <option value="Disc Brake Belakang Kiri" ${data?.posisi === 'Disc Brake Belakang Kiri' ? 'selected' : ''}>Disc Brake Belakang Kiri</option>
                        <option value="Disc Brake Belakang Kanan" ${data?.posisi === 'Disc Brake Belakang Kanan' ? 'selected' : ''}>Disc Brake Belakang Kanan</option>
                        <option value="Caliper Depan Kiri" ${data?.posisi === 'Caliper Depan Kiri' ? 'selected' : ''}>Caliper Depan Kiri</option>
                        <option value="Caliper Depan Kanan" ${data?.posisi === 'Caliper Depan Kanan' ? 'selected' : ''}>Caliper Depan Kanan</option>
                        <option value="Caliper Belakang Kiri" ${data?.posisi === 'Caliper Belakang Kiri' ? 'selected' : ''}>Caliper Belakang Kiri</option>
                        <option value="Caliper Belakang Kanan" ${data?.posisi === 'Caliper Belakang Kanan' ? 'selected' : ''}>Caliper Belakang Kanan</option>
                        <option value="Master Rem" ${data?.posisi === 'Master Rem' ? 'selected' : ''}>Master Rem</option>
                        <option value="Booster Rem" ${data?.posisi === 'Booster Rem' ? 'selected' : ''}>Booster Rem</option>
                        <option value="Minyak Rem" ${data?.posisi === 'Minyak Rem' ? 'selected' : ''}>Minyak Rem</option>
                        <option value="Hand Brake" ${data?.posisi === 'Hand Brake' ? 'selected' : ''}>Hand Brake</option>
                    </optgroup>
                    <optgroup label="🎛️ Sistem Kemudi">
                        <option value="Stir/Setir" ${data?.posisi === 'Stir/Setir' ? 'selected' : ''}>Stir/Setir</option>
                        <option value="Power Steering Pump" ${data?.posisi === 'Power Steering Pump' ? 'selected' : ''}>Power Steering Pump</option>
                        <option value="Rack Steer" ${data?.posisi === 'Rack Steer' ? 'selected' : ''}>Rack Steer</option>
                        <option value="Steering Column" ${data?.posisi === 'Steering Column' ? 'selected' : ''}>Steering Column</option>
                        <option value="Universal Joint Steer" ${data?.posisi === 'Universal Joint Steer' ? 'selected' : ''}>Universal Joint Steer</option>
                    </optgroup>
                    <optgroup label="❄️ AC & Interior">
                        <option value="Kompresor AC" ${data?.posisi === 'Kompresor AC' ? 'selected' : ''}>Kompresor AC</option>
                        <option value="Kondensor AC" ${data?.posisi === 'Kondensor AC' ? 'selected' : ''}>Kondensor AC</option>
                        <option value="Evaporator AC" ${data?.posisi === 'Evaporator AC' ? 'selected' : ''}>Evaporator AC</option>
                        <option value="Blower AC" ${data?.posisi === 'Blower AC' ? 'selected' : ''}>Blower AC</option>
                        <option value="Filter Cabin" ${data?.posisi === 'Filter Cabin' ? 'selected' : ''}>Filter Cabin</option>
                        <option value="Refrigerant/Freon AC" ${data?.posisi === 'Refrigerant/Freon AC' ? 'selected' : ''}>Refrigerant/Freon AC</option>
                        <option value="Expansion Valve AC" ${data?.posisi === 'Expansion Valve AC' ? 'selected' : ''}>Expansion Valve AC</option>
                        <option value="Dashboard" ${data?.posisi === 'Dashboard' ? 'selected' : ''}>Dashboard</option>
                        <option value="Speedometer" ${data?.posisi === 'Speedometer' ? 'selected' : ''}>Speedometer</option>
                        <option value="Jok Depan Kiri" ${data?.posisi === 'Jok Depan Kiri' ? 'selected' : ''}>Jok Depan Kiri</option>
                        <option value="Jok Depan Kanan" ${data?.posisi === 'Jok Depan Kanan' ? 'selected' : ''}>Jok Depan Kanan</option>
                        <option value="Jok Belakang" ${data?.posisi === 'Jok Belakang' ? 'selected' : ''}>Jok Belakang</option>
                        <option value="Karpet" ${data?.posisi === 'Karpet' ? 'selected' : ''}>Karpet</option>
                        <option value="Plafon" ${data?.posisi === 'Plafon' ? 'selected' : ''}>Plafon</option>
                        <option value="Door Trim Depan Kiri" ${data?.posisi === 'Door Trim Depan Kiri' ? 'selected' : ''}>Door Trim Depan Kiri</option>
                        <option value="Door Trim Depan Kanan" ${data?.posisi === 'Door Trim Depan Kanan' ? 'selected' : ''}>Door Trim Depan Kanan</option>
                        <option value="Door Trim Belakang Kiri" ${data?.posisi === 'Door Trim Belakang Kiri' ? 'selected' : ''}>Door Trim Belakang Kiri</option>
                        <option value="Door Trim Belakang Kanan" ${data?.posisi === 'Door Trim Belakang Kanan' ? 'selected' : ''}>Door Trim Belakang Kanan</option>
                    </optgroup>
                    <optgroup label="🔊 Audio & Elektronik">
                        <option value="Head Unit" ${data?.posisi === 'Head Unit' ? 'selected' : ''}>Head Unit</option>
                        <option value="Speaker Depan Kiri" ${data?.posisi === 'Speaker Depan Kiri' ? 'selected' : ''}>Speaker Depan Kiri</option>
                        <option value="Speaker Depan Kanan" ${data?.posisi === 'Speaker Depan Kanan' ? 'selected' : ''}>Speaker Depan Kanan</option>
                        <option value="Speaker Belakang Kiri" ${data?.posisi === 'Speaker Belakang Kiri' ? 'selected' : ''}>Speaker Belakang Kiri</option>
                        <option value="Speaker Belakang Kanan" ${data?.posisi === 'Speaker Belakang Kanan' ? 'selected' : ''}>Speaker Belakang Kanan</option>
                        <option value="Subwoofer" ${data?.posisi === 'Subwoofer' ? 'selected' : ''}>Subwoofer</option>
                        <option value="Amplifier" ${data?.posisi === 'Amplifier' ? 'selected' : ''}>Amplifier</option>
                        <option value="Antena" ${data?.posisi === 'Antena' ? 'selected' : ''}>Antena</option>
                        <option value="Klakson" ${data?.posisi === 'Klakson' ? 'selected' : ''}>Klakson</option>
                        <option value="ECU" ${data?.posisi === 'ECU' ? 'selected' : ''}>ECU</option>
                        <option value="TCU" ${data?.posisi === 'TCU' ? 'selected' : ''}>TCU</option>
                        <option value="Sensor Parkir" ${data?.posisi === 'Sensor Parkir' ? 'selected' : ''}>Sensor Parkir</option>
                        <option value="Kamera Mundur" ${data?.posisi === 'Kamera Mundur' ? 'selected' : ''}>Kamera Mundur</option>
                        <option value="Dashcam" ${data?.posisi === 'Dashcam' ? 'selected' : ''}>Dashcam</option>
                        <option value="GPS Tracker" ${data?.posisi === 'GPS Tracker' ? 'selected' : ''}>GPS Tracker</option>
                    </optgroup>
                    <optgroup label="⛽ Sistem Bahan Bakar & Knalpot">
                        <option value="Tangki Bensin" ${data?.posisi === 'Tangki Bensin' ? 'selected' : ''}>Tangki Bensin</option>
                        <option value="Pompa Bensin" ${data?.posisi === 'Pompa Bensin' ? 'selected' : ''}>Pompa Bensin</option>
                        <option value="Knalpot Depan" ${data?.posisi === 'Knalpot Depan' ? 'selected' : ''}>Knalpot Depan</option>
                        <option value="Knalpot Tengah" ${data?.posisi === 'Knalpot Tengah' ? 'selected' : ''}>Knalpot Tengah</option>
                        <option value="Knalpot Belakang" ${data?.posisi === 'Knalpot Belakang' ? 'selected' : ''}>Knalpot Belakang</option>
                        <option value="Catalytic Converter" ${data?.posisi === 'Catalytic Converter' ? 'selected' : ''}>Catalytic Converter</option>
                        <option value="Muffler" ${data?.posisi === 'Muffler' ? 'selected' : ''}>Muffler</option>
                        <option value="Resonator" ${data?.posisi === 'Resonator' ? 'selected' : ''}>Resonator</option>
                    </optgroup>
                    <optgroup label="🔋 Kelistrikan">
                        <option value="Fuse Box" ${data?.posisi === 'Fuse Box' ? 'selected' : ''}>Fuse Box</option>
                        <option value="Relay" ${data?.posisi === 'Relay' ? 'selected' : ''}>Relay</option>
                        <option value="Wiring Harness" ${data?.posisi === 'Wiring Harness' ? 'selected' : ''}>Wiring Harness</option>
                        <option value="Switch Lampu" ${data?.posisi === 'Switch Lampu' ? 'selected' : ''}>Switch Lampu</option>
                        <option value="Switch Wiper" ${data?.posisi === 'Switch Wiper' ? 'selected' : ''}>Switch Wiper</option>
                        <option value="Switch Power Window" ${data?.posisi === 'Switch Power Window' ? 'selected' : ''}>Switch Power Window</option>
                    </optgroup>
                    <optgroup label="🧴 Cairan & Fluida">
                        <option value="Oli Mesin" ${data?.posisi === 'Oli Mesin' ? 'selected' : ''}>Oli Mesin</option>
                        <option value="Oli Transmisi" ${data?.posisi === 'Oli Transmisi' ? 'selected' : ''}>Oli Transmisi</option>
                        <option value="Oli Gardan" ${data?.posisi === 'Oli Gardan' ? 'selected' : ''}>Oli Gardan</option>
                        <option value="Oli Power Steering" ${data?.posisi === 'Oli Power Steering' ? 'selected' : ''}>Oli Power Steering</option>
                        <option value="Coolant/Air Radiator" ${data?.posisi === 'Coolant/Air Radiator' ? 'selected' : ''}>Coolant/Air Radiator</option>
                    </optgroup>
                    <optgroup label="🔧 Lain-lain">
                        <option value="Umum" ${data?.posisi === 'Umum' ? 'selected' : ''}>Umum</option>
                        <option value="Keseluruhan" ${data?.posisi === 'Keseluruhan' ? 'selected' : ''}>Keseluruhan</option>
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
                    {{-- Tampilan visual nilai interval (tidak di-submit) --}}
                    <div id="interval-nilai-display-${idx}"
                        class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600 text-center">
                        ${data?.interval_nilai || 12}
                    </div>
                    <div id="interval-satuan-display-${idx}"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600">
                        ${ ({'hari':'Hari','minggu':'Minggu','bulan':'Bulan','tahun':'Tahun'})[data?.interval_satuan || 'bulan'] || 'Bulan' }
                    </div>
                    {{-- Hidden inputs yang benar-benar di-submit --}}
                    <input type="hidden" name="parts[${idx}][interval_nilai]" id="interval-nilai-${idx}"
                        value="${data?.interval_nilai || 12}">
                    <input type="hidden" name="parts[${idx}][interval_satuan]" id="interval-satuan-${idx}"
                        value="${(data?.interval_satuan && ['hari','minggu','bulan','tahun'].includes(data.interval_satuan)) ? data.interval_satuan : 'bulan'}">
                    {{-- Ini tetap ada untuk fetchLimitRule yang set .value dan .disabled --}}
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
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Bukti / Attachment
                    <span class="text-[10px] font-normal text-gray-400 ml-1">(bisa lebih dari 1 file)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer border border-dashed border-gray-300 hover:border-blue-400 bg-gray-50 hover:bg-blue-50/40 rounded-lg px-3 py-2.5 transition-colors group">
                    <i class="fa fa-paperclip text-gray-400 group-hover:text-blue-500 text-sm transition-colors"></i>
                    <span class="text-xs text-gray-500 group-hover:text-blue-600 transition-colors">Klik untuk pilih file...</span>
                    <input type="file" id="bukti-input-${idx}" name="parts[${idx}][bukti][]"
                        multiple accept="image/*,video/mp4,video/mov"
                        onchange="updateFileList(${idx})"
                        class="hidden">
                </label>
                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, MP4, MOV</p>
                <!-- Daftar nama file yang dipilih -->
                <div id="bukti-list-${idx}" class="mt-2 space-y-1"></div>
            </div>

        </div>
    `;

    container.appendChild(row);

    // Auto-fill km pasang dari header KM jika belum ada
    syncKmPasang(idx);

    recalcTotal();
}

// ── Bukti per-part: render daftar nama file ───────────────────
function updateFileList(idx) {
    const input   = document.getElementById('bukti-input-' + idx);
    const listEl  = document.getElementById('bukti-list-' + idx);
    if (!input || !listEl) return;

    const files = Array.from(input.files);
    listEl.innerHTML = '';

    if (files.length === 0) return;

    files.forEach(function(file, fileIdx) {
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        // Format ukuran file
        const size = file.size < 1024 * 1024
            ? (file.size / 1024).toFixed(1) + ' KB'
            : (file.size / 1024 / 1024).toFixed(1) + ' MB';

        const icon = isImage
            ? '<i class="fa fa-image text-blue-400 text-xs w-4 text-center"></i>'
            : isVideo
                ? '<i class="fa fa-film text-purple-400 text-xs w-4 text-center"></i>'
                : '<i class="fa fa-file text-gray-400 text-xs w-4 text-center"></i>';

        const item = document.createElement('div');
        item.id        = 'bukti-item-' + idx + '-' + fileIdx;
        item.className = 'flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs';
        item.innerHTML =
            icon +
            '<span class="flex-1 truncate text-gray-700 font-medium" title="' + file.name + '">' + file.name + '</span>' +
            '<span class="text-gray-400 text-[10px] flex-shrink-0">' + size + '</span>' +
            '<button type="button" onclick="removePartFile(' + idx + ', ' + fileIdx + ')" ' +
                'class="w-5 h-5 rounded flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors flex-shrink-0">' +
                '<i class="fa fa-times text-[10px]"></i>' +
            '</button>';

        listEl.appendChild(item);
    });
}

// Hapus satu file dari input (per index) — pakai DataTransfer
function removePartFile(idx, fileIdx) {
    const input = document.getElementById('bukti-input-' + idx);
    if (!input) return;

    const dt    = new DataTransfer();
    const files = Array.from(input.files);
    files.forEach(function(file, i) {
        if (i !== fileIdx) dt.items.add(file);
    });

    input.files = dt.files;
    updateFileList(idx); // re-render
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

        // Ambil elemen display dan hidden
        var nilaiHidden       = document.getElementById('interval-nilai-' + idx);
        var satuanHidden      = document.getElementById('interval-satuan-' + idx);
        var nilaiDisplay      = document.getElementById('interval-nilai-display-' + idx);
        var satuanDisplay     = document.getElementById('interval-satuan-display-' + idx);
        var hintEl            = document.getElementById('interval-hint-' + idx);
        var biayaHint         = document.getElementById('biaya-hint-' + idx);

        if (!data) {
            // Tidak ada rule — reset ke default
            var h = document.getElementById('interval-hint-' + idx);
            var bh = document.getElementById('biaya-hint-' + idx);
            if (h)  { h.classList.add('hidden'); }
            if (bh) { bh.classList.add('hidden'); }
            if (nilaiHidden)  { nilaiHidden.value  = 12; }
            if (satuanHidden) { satuanHidden.value = 'bulan'; }
            if (nilaiDisplay)  { nilaiDisplay.textContent  = '12'; }
            if (satuanDisplay) { satuanDisplay.textContent = 'Bulan'; }
            return;
        }

        // Ada rule — set hidden inputs (yang benar-benar di-submit)
        var validSatuan = ['hari','minggu','bulan','tahun'].includes(data.limit_satuan)
            ? data.limit_satuan : 'bulan';

        if (nilaiHidden)  { nilaiHidden.value  = data.limit_nilai || 12; }
        if (satuanHidden) { satuanHidden.value = validSatuan; }

        // Update tampilan visual
        if (nilaiDisplay)  { nilaiDisplay.textContent  = data.limit_nilai || 12; }
        if (satuanDisplay) { satuanDisplay.textContent = labelMap[validSatuan] || 'Bulan'; }

        if (hintEl) { hintEl.classList.remove('hidden'); }

        // Tampilkan batas harga di bawah field biaya
        if (biayaHint) {
            if (data.limit_price) {
                biayaHint.innerHTML = '<i class="fa fa-triangle-exclamation text-[9px] text-amber-500"></i>'
                    + ' Batas harga kategori ini: <strong class="text-amber-600">'
                    + data.limit_price_formatted + '</strong>';
                biayaHint.classList.remove('hidden');
            } else {
                biayaHint.classList.add('hidden');
            }
        }
    })
    .catch(function() { /* silent fail */ });
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
        document.querySelectorAll('[id^="cat-select-"]').forEach(function(sel) {
            var idxMatch = sel.id.match(/cat-select-(\d+)/);
            if (!idxMatch) return;
            var rowIdx = idxMatch[1];
            if (sel.value && sel.value !== '__new__') {
                fetchLimitRule(val, sel.value, rowIdx);
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
    @if ($prefill)
        // Set kendaraan dan trigger auto-fill KM
        const kendaraanSelect = document.getElementById('kendaraan_id');
        kendaraanSelect.value = '{{ $prefill["kendaraan_id"] }}';
        onKendaraanChange('{{ $prefill["kendaraan_id"] }}');

        // Tambah row pre-filled
        addPartRow({
            nama_part:        '{{ addslashes($prefill["part"]["nama_part"]) }}',
            category_id:      '{{ $prefill["part"]["category_id"] }}',
            posisi:           '{{ addslashes($prefill["part"]["posisi"] ?? "") }}',
            part_number:      '{{ addslashes($prefill["part"]["part_number"] ?? "") }}',
            interval_nilai:   '{{ $prefill["part"]["interval_nilai"] }}',
            interval_satuan:  '{{ $prefill["part"]["interval_satuan"] }}',
            biaya:            '{{ $prefill["part"]["biaya"] ?? 0 }}',
        });
    @endif
});
</script>

{{-- ALERT POPUP --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if (session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @elseif (session('error'))
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Error!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('error') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Validasi Error!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa fa-times"></i></button>
    </div>
</div>

<script>
(function() {
    const overlay = document.getElementById('alertOverlay');
    if (!overlay) return;
    const box = document.getElementById('alertBox');
    setTimeout(() => { overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 50);
    const timer = setTimeout(() => closeAlert(), 5000);
    function closeAlert() { clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>
@endif

@endsection
