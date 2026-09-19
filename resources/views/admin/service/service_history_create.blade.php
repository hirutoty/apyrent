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
                @if($prefill && ($prefill['source'] ?? '') === 'part')
                    Ganti Part Baru
                @elseif($prefill && ($prefill['source'] ?? '') === 'reminder')
                    Selesaikan Reminder Service
                @elseif($prefill && ($prefill['source'] ?? '') === 'edit_po')
                    Ajukan Ulang Service Part
                @elseif($prefill && ($prefill['source'] ?? '') === 'edit_pembayaran')
                    Ajukan Ulang Service Part
                @else
                    Tambah Service Kendaraan
                @endif
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                @if($prefill && ($prefill['source'] ?? '') === 'part')
                    Form pre-filled dari part limit — lengkapi data part pengganti dan ajukan
                @elseif($prefill && ($prefill['source'] ?? '') === 'reminder')
                    Form pre-filled dari reminder — perbarui data part yang diganti
                @else
                    Isi header service lalu tambahkan part yang dipasang
                @endif
            </p>
        </div>
    </div>

    {{-- PREFILL NOTICE --}}
    @if ($prefill && ($prefill['source'] ?? '') === 'reminder')
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
        <input type="hidden" id="prefill_reminder_id" value="{{ $prefill['reminder_id'] ?? '' }}">
        <input type="hidden" id="prefill_kendaraan_id" value="{{ $prefill['kendaraan_id'] }}">
    @elseif ($prefill && ($prefill['source'] ?? '') === 'part')
        <div class="bg-orange-50 border border-orange-200 rounded-xl px-5 py-4 flex items-start gap-3">
            <i class="fa fa-rotate-right text-orange-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-semibold text-orange-800">Ganti Part Baru — Part Sudah Limit</p>
                <p class="text-xs text-orange-700 mt-0.5">
                    Part <strong>{{ $prefill['part']['nama_part'] }}</strong> pada kendaraan
                    <strong>{{ $prefill['kendaraan']->merk }} — {{ $prefill['kendaraan']->nopol }}</strong>
                    sudah melewati limit interval. Lengkapi data part pengganti dan ajukan.
                </p>
            </div>
        </div>
        <input type="hidden" id="prefill_kendaraan_id" value="{{ $prefill['kendaraan_id'] }}">
    @elseif ($prefill && ($prefill['source'] ?? '') === 'edit_po')
        <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 flex items-start gap-3">
            <i class="fa fa-times-circle text-red-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-semibold text-red-800">Ajukan Ulang — Pengajuan Sebelumnya Ditolak</p>
                @if(!empty($prefill['catatan_tolak']))
                    <p class="text-xs text-red-700 mt-0.5">Catatan penolakan: <strong>{{ $prefill['catatan_tolak'] }}</strong></p>
                @endif
                <p class="text-xs text-red-600 mt-1">Perbarui data yang diperlukan lalu ajukan kembali.</p>
            </div>
        </div>
        <input type="hidden" id="prefill_kendaraan_id" value="{{ $prefill['kendaraan_id'] }}">
    @elseif ($prefill && ($prefill['source'] ?? '') === 'edit_pembayaran')
        <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 flex items-start gap-3">
            <i class="fa fa-times-circle text-red-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-semibold text-red-800">Ajukan Ulang — Pembayaran Sebelumnya Ditolak</p>
                @if(!empty($prefill['catatan_tolak']))
                    <p class="text-xs text-red-700 mt-0.5">Alasan penolakan: <strong>{{ $prefill['catatan_tolak'] }}</strong></p>
                @endif
                <p class="text-xs text-red-600 mt-1">Perbarui data yang diperlukan lalu ajukan kembali.</p>
            </div>
        </div>
        <input type="hidden" id="prefill_kendaraan_id" value="{{ $prefill['kendaraan_id'] }}">
    @endif

    <form action="{{ route('service-history.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($prefill)
            @if(!empty($prefill['reminder_id']))
            <input type="hidden" name="from_reminder" value="{{ $prefill['reminder_id'] }}">
            @endif
            @if(($prefill['source'] ?? '') === 'part')
            <input type="hidden" name="from_part" value="{{ $prefill['replace_part_id'] }}">
            @endif
            @if(($prefill['source'] ?? '') === 'edit_po')
            <input type="hidden" name="edit_po" value="{{ $prefill['edit_po_id'] }}">
            @endif
            @if(($prefill['source'] ?? '') === 'edit_pembayaran')
            <input type="hidden" name="edit_pembayaran" value="{{ $prefill['edit_pembayaran_id'] }}">
            @endif
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

                {{-- Alasan Permintaan & Keterangan Pengadaan — hidden, auto-generate --}}
                <input type="hidden" name="alasan_permintaan" id="alasan_permintaan" value="{{ old('alasan_permintaan', '') }}">
                <input type="hidden" name="keterangan_pengadaan" id="keterangan_pengadaan" value="{{ old('keterangan_pengadaan', '') }}">

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
let allSuppliers = @json($suppliers->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama_supplier]));
let partIndex = 0;

// Flag: true saat ajukan ulang dari PO/Pembayaran yang ditolak — lampiran tidak wajib
const IS_RESUBMIT = {{ (($prefill['source'] ?? '') === 'edit_po' || ($prefill['source'] ?? '') === 'edit_pembayaran') ? 'true' : 'false' }};

// ── Limit rules: di-load saat kendaraan dipilih ───────────────
// Format: { [category_id]: { limit_price, limit_km, limit_nilai, limit_satuan } }
let limitRulesMap = {};

// ── Cache km_pasang part lama dari DB ─────────────────────────
// Key: "categoryId_posisi" → integer km_pasang (atau null)
let partLamaKmCache = {};

/**
 * Fetch km_pasang part lama (Terpasang/aktif) dari server
 * berdasarkan kendaraan + kategori + posisi.
 * Hasil di-cache di partLamaKmCache.
 */
async function fetchPartLamaKm(kendaraanId, categoryId, posisi, idx) {
    if (!kendaraanId) return;
    const cacheKey = (categoryId || '0') + '_' + (posisi || '');
    // Jangan fetch ulang jika sudah ada di cache
    if (partLamaKmCache.hasOwnProperty(cacheKey)) {
        calcKeteranganLimit(idx);
        return;
    }
    try {
        const params = new URLSearchParams({ kendaraan_id: kendaraanId });
        if (categoryId)  params.set('category_id', categoryId);
        if (posisi)       params.set('posisi', posisi);
        const res  = await fetch('/admin/service-history/part-lama-km?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        partLamaKmCache[cacheKey] = data.kilometer_pasang ?? null;
    } catch (e) {
        partLamaKmCache[cacheKey] = null;
    }
    calcKeteranganLimit(idx);
}

async function loadLimitRules(kendaraanId) {
    limitRulesMap = {};
    if (!kendaraanId) return;
    try {
        const res  = await fetch('/admin/service-history/limit-rules/' + kendaraanId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        data.forEach(function(r) {
            limitRulesMap[r.category_id] = r;
        });
    } catch (e) { /* silent */ }
    recalcAllKeterangan();
}

/**
 * Hitung keterangan limit untuk satu baris part secara client-side.
 * Mirror logika generateKeteranganLimit() PHP + KM kumulatif per tahun.
 */
function calcKeteranganLimit(idx) {
    const catSelect    = document.getElementById('cat-select-' + idx);
    const categoryId   = catSelect ? parseInt(catSelect.value) : null;
    const limitRule    = (categoryId && limitRulesMap[categoryId]) ? limitRulesMap[categoryId] : null;

    const resultEl     = document.getElementById('ket-limit-text-' + idx);
    const badgeEl      = document.getElementById('ket-limit-badge-' + idx);
    const wrapEl       = document.getElementById('ket-limit-wrap-' + idx);
    const hiddenEl     = document.getElementById('ket-limit-hidden-' + idx);

    if (!resultEl) return;

    // Tidak ada rule → "-"
    if (!limitRule) {
        resultEl.textContent = '-';
        if (badgeEl) { badgeEl.className = 'hidden'; }
        if (hiddenEl) hiddenEl.value = '-';
        return;
    }

    // Ambil nilai dari form
    const biaya         = parseInt(document.querySelector('[name="parts[' + idx + '][biaya]"]')?.value || 0);
    const tglPasangStr  = document.querySelector('[name="parts[' + idx + '][tgl_pasang]"]')?.value || '';
    const intervalNilai = parseInt(document.getElementById('interval-nilai-' + idx)?.value || 0);
    const intervalSat   = document.getElementById('interval-satuan-' + idx)?.value || 'bulan';
    const kmPasang      = parseInt(document.getElementById('km-pasang-' + idx)?.value || 0);
    const kmInput       = parseInt(document.getElementById('kilometer')?.value || 0);
    const tanggalServis = document.querySelector('[name="tanggal_service"]')?.value || '';

    const tglPasang     = tglPasangStr  ? new Date(tglPasangStr)  : new Date();
    const refTanggal    = tanggalServis ? new Date(tanggalServis) : new Date();

    // Hitung tanggal limit (tgl_pasang + interval)
    const tglLimit = calcTglLimit(tglPasang, intervalNilai, intervalSat);

    const limitPrice    = limitRule.limit_price  ? parseInt(limitRule.limit_price)  : null;
    const limitKm       = limitRule.limit_km     ? parseInt(limitRule.limit_km)     : null;
    const intervalAda   = intervalNilai > 0;

    // ── Dimensi biaya ─────────────────────────────────────────
    const biayaSama  = limitPrice !== null && biaya === limitPrice;
    const biayaLewat = limitPrice !== null && biaya  >  limitPrice;
    const biayaAman  = limitPrice === null || biaya  <  limitPrice;

    // ── Dimensi waktu ─────────────────────────────────────────
    // Normalisasi ke startOfDay
    const refMs   = new Date(refTanggal.getFullYear(), refTanggal.getMonth(), refTanggal.getDate()).getTime();
    const limMs   = new Date(tglLimit.getFullYear(),   tglLimit.getMonth(),   tglLimit.getDate()).getTime();
    const waktuSama  = intervalAda && limMs === refMs;
    const waktuLewat = intervalAda && limMs  <  refMs;
    const waktuAman  = !intervalAda || limMs  >  refMs;

    // ── Dimensi KM (km_pasang part LAMA + limit_km = target) ────────────
    // km_pasang diambil dari part lama (Terpasang/aktif) di DB,
    // bukan dari km_pasang form baru. Ini agar limit terdeteksi
    // meski part baru baru saja dipasang di KM yang lebih tinggi.
    let kmSama = false, kmLewat = false, kmAman = true;
    const kmAda = limitKm !== null && limitKm > 0;
    if (kmAda) {
        // Coba ambil km_pasang part lama dari cache
        const posisiVal = document.querySelector('[name="parts[' + idx + '][posisi]"]')?.value || '';
        const cacheKey  = (categoryId || '0') + '_' + posisiVal;
        const kmPasangLama = partLamaKmCache.hasOwnProperty(cacheKey)
            ? partLamaKmCache[cacheKey]
            : null;
        // Gunakan km_pasang lama jika ada, fallback ke km_pasang form baru
        const baseKm   = (kmPasangLama !== null && kmPasangLama !== undefined) ? kmPasangLama : kmPasang;
        const targetKm = baseKm + limitKm;
        kmSama  = kmInput === targetKm;
        kmLewat = kmInput  >  targetKm;
        kmAman  = kmInput  <  targetKm;
    }

    // Tidak ada limit dikonfigurasi → "-"
    const adaLimit = limitPrice || intervalAda || kmAda;
    if (!adaLimit) {
        resultEl.textContent = '-';
        if (badgeEl) badgeEl.className = 'hidden';
        if (hiddenEl) hiddenEl.value = '-';
        return;
    }

    // Semua aman → "-"
    if (biayaAman && waktuAman && kmAman) {
        resultEl.textContent = '-';
        if (badgeEl) badgeEl.className = 'hidden';
        if (hiddenEl) hiddenEl.value = '-';
        return;
    }

    // ── Bangun kalimat ────────────────────────────────────────
    const parts_ket = [];
    const belum     = [];

    if (biayaSama)       parts_ket.push('mencapai batas limit biaya');
    else if (biayaLewat) parts_ket.push('sudah melebihi limit biaya');
    else if (limitPrice && biayaAman) belum.push('belum mencapai limit biaya');

    if (waktuSama)       parts_ket.push('mencapai batas limit jangka waktu');
    else if (waktuLewat) parts_ket.push('sudah melebihi batas waktu');
    else if (intervalAda && waktuAman) belum.push('belum mencapai limit jangka waktu');

    if (kmSama)          parts_ket.push('mencapai batas limit KM');
    else if (kmLewat)    parts_ket.push('sudah melebihi batas limit KM');
    else if (kmAda && kmAman) belum.push('belum mencapai limit KM');

    const kalimat = parts_ket.concat(belum);
    if (kalimat.length === 0) {
        resultEl.textContent = '-';
        if (badgeEl) badgeEl.className = 'hidden';
        if (hiddenEl) hiddenEl.value = '-';
        return;
    }

    const hasil = kalimat[0].charAt(0).toUpperCase() + kalimat[0].slice(1) + (kalimat.length > 1 ? ', ' + kalimat.slice(1).join(', ') : '');
    resultEl.textContent = hasil;
    if (hiddenEl) hiddenEl.value = hasil;

    // ── Badge warna ───────────────────────────────────────────
    if (badgeEl) {
        const adaLewat = biayaLewat || waktuLewat || kmLewat;
        const adaSama  = !adaLewat && (biayaSama || waktuSama || kmSama);
        if (adaLewat) {
            badgeEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700';
            badgeEl.innerHTML = '<i class="fa fa-circle-exclamation text-[9px]"></i> Melebihi Limit';
        } else if (adaSama) {
            badgeEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-700';
            badgeEl.innerHTML = '<i class="fa fa-triangle-exclamation text-[9px]"></i> Mencapai Limit';
        } else {
            badgeEl.className = 'hidden';
        }
    }
}

/** Hitung tanggal limit dari tgl_pasang + interval */
function calcTglLimit(tglPasang, nilai, satuan) {
    const d = new Date(tglPasang);
    if (!nilai || nilai <= 0) return d;
    switch (satuan) {
        case 'hari':   d.setDate(d.getDate() + nilai); break;
        case 'minggu': d.setDate(d.getDate() + nilai * 7); break;
        case 'tahun':  d.setFullYear(d.getFullYear() + nilai); break;
        default:       d.setMonth(d.getMonth() + nilai); break; // bulan
    }
    return d;
}

/** Recalc semua baris part yang sudah ada */
function recalcAllKeterangan() {
    document.querySelectorAll('[id^="ket-limit-text-"]').forEach(function(el) {
        const m = el.id.match(/ket-limit-text-(\d+)/);
        if (m) calcKeteranganLimit(parseInt(m[1]));
    });
}

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

    // Build options supplier
    let supplierOptions = '<option value="">-- Pilih Supplier/Bengkel --</option>';
    allSuppliers.forEach(s => {
        const sel = data?.supplier_id == s.id ? 'selected' : '';
        supplierOptions += `<option value="${s.id}" ${sel}>${s.nama}</option>`;
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

            <!-- KM Pasang — hidden, sync otomatis dari header KM -->
            <input type="hidden" name="parts[${idx}][kilometer_pasang]" id="km-pasang-${idx}"
                value="${kmPasang}">

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

            <!-- Biaya -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Biaya (Rp)</label>
                <input type="number" name="parts[${idx}][biaya]" id="biaya-${idx}" min="0"
                    value="${data?.biaya || 0}"
                    onchange="recalcTotal(); calcKeteranganLimit(${idx});"
                    oninput="recalcTotal(); calcKeteranganLimit(${idx});"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                <p id="biaya-hint-${idx}" class="text-[10px] text-gray-400 mt-1 hidden"></p>
            </div>

            <!-- Keterangan Limit Otomatis -->
            <div class="md:col-span-3">
                <div id="ket-limit-wrap-${idx}" class="rounded-xl border border-gray-200 bg-gray-50/60 px-4 py-3 flex items-start gap-3">
                    <i class="fa fa-circle-info text-blue-400 text-sm mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Keterangan Limit</span>
                            <span id="ket-limit-badge-${idx}" class="hidden"></span>
                        </div>
                        <p id="ket-limit-text-${idx}" class="text-xs text-gray-600 break-words">
                            ${data?.keterangan_limit || '-'}
                        </p>
                    </div>
                    <input type="hidden" name="parts[${idx}][keterangan_limit]" id="ket-limit-hidden-${idx}"
                        value="${data?.keterangan_limit || '-'}">
                </div>
            </div>

            <!-- Info Pembayaran (nama rekening, bank, no rekening) -->
            <div class="md:col-span-3">
                <div class="border border-dashed border-blue-200 rounded-xl p-3 bg-blue-50/40 space-y-3">
                    <p class="text-[10px] font-semibold text-blue-600 uppercase tracking-wide flex items-center gap-1.5">
                        <i class="fa fa-university text-[10px]"></i> Info Pembayaran
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Rekening</label>
                            <input type="text" name="parts[${idx}][nama_rekening]"
                                value="${data?.nama_rekening || ''}"
                                placeholder="cth: Budi Santoso"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Bank</label>
                            <input type="text" name="parts[${idx}][nama_bank]"
                                value="${data?.nama_bank || ''}"
                                placeholder="cth: BCA, Mandiri, BRI..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 mb-1 block">No. Rekening</label>
                            <input type="text" name="parts[${idx}][no_rekening]"
                                value="${data?.no_rekening || ''}"
                                placeholder="cth: 1234567890"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier per Part -->
            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Supplier / Bengkel
                    <span class="text-gray-400 text-[10px] font-normal ml-1">
                        (opsional —
                        <button type="button" onclick="openSupplierModal(${idx})"
                            class="text-blue-500 hover:underline text-[10px] font-medium">+ tambah baru</button>
                        jika belum ada)
                    </span>
                </label>
                <select name="parts[${idx}][supplier_id]" id="supplier-select-${idx}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    ${supplierOptions}
                </select>
            </div>

            <!-- Lampiran (full width) -->
            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Lampiran ${IS_RESUBMIT ? '' : '<span class="text-red-400">*</span>'}
                    <span class="text-[10px] font-normal text-gray-400 ml-1">${IS_RESUBMIT ? '(opsional — lampiran lama dipertahankan)' : '(wajib — bisa lebih dari 1 file)'}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer border border-dashed border-blue-300 hover:border-blue-400 bg-gray-50 hover:bg-blue-50/40 rounded-lg px-3 py-2.5 transition-colors group">
                    <i class="fa fa-paperclip text-blue-400 group-hover:text-blue-500 text-sm transition-colors"></i>
                    <span class="text-xs text-gray-500 group-hover:text-blue-600 transition-colors">Klik untuk pilih file...</span>
                    <input type="file" id="bukti-input-${idx}" name="parts[${idx}][bukti][]"
                        multiple ${IS_RESUBMIT ? '' : 'required'} accept="image/*,video/mp4,video/mov"
                        onchange="updateFileList(${idx})"
                        class="hidden">
                </label>
                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, MP4, MOV</p>
                <!-- Daftar nama file yang dipilih (baru) -->
                <div id="bukti-list-${idx}" class="mt-2 space-y-1"></div>
                <!-- Daftar file lama (dari PO/Pembayaran ditolak) -->
                ${(data?.old_files && data.old_files.length > 0) ? `
                <div class="mt-2 space-y-1" id="old-bukti-list-${idx}">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1"><i class="fa fa-paperclip mr-1"></i>Lampiran lama (dipertahankan):</p>
                    ${data.old_files.map(f => `
                        <div class="flex items-center gap-2 text-[11px] text-gray-500 bg-gray-50 border border-gray-200 rounded px-2 py-1">
                            <i class="fa fa-file text-gray-400 text-[10px]"></i>
                            <span class="truncate">${f.original_name || f.stored_name || 'file'}</span>
                        </div>
                    `).join('')}
                </div>
                ` : ''}
            </div>

        </div>
    `;

    container.appendChild(row);

    // Auto-fill km pasang dari header KM jika belum ada
    syncKmPasang(idx);

    recalcTotal();
    generateKeterangan();

    // Kalkulasi keterangan limit untuk baris baru
    calcKeteranganLimit(idx);

    // Pasang listener pada field tgl pasang baris ini
    const tglPasangInput = document.querySelector('[name="parts[' + idx + '][tgl_pasang]"]');
    if (tglPasangInput) {
        tglPasangInput.addEventListener('change', function() { calcKeteranganLimit(idx); });
    }

    // Pasang listener pada field posisi — saat posisi berubah, fetch part lama baru
    const posisiSelect = document.querySelector('[name="parts[' + idx + '][posisi]"]');
    if (posisiSelect) {
        posisiSelect.addEventListener('change', function() {
            const kendaraanId = document.getElementById('kendaraan_id').value;
            const catSelect   = document.getElementById('cat-select-' + idx);
            const categoryId  = catSelect ? catSelect.value : null;
            if (kendaraanId && categoryId) {
                fetchPartLamaKm(kendaraanId, categoryId, this.value, idx);
            } else {
                calcKeteranganLimit(idx);
            }
        });
    }

    // Fetch part lama saat row baru ditambah (jika kategori sudah terisi dari prefill)
    const catSelectNew = document.getElementById('cat-select-' + idx);
    if (catSelectNew && catSelectNew.value) {
        const kendaraanId = document.getElementById('kendaraan_id').value;
        const posisiVal   = posisiSelect ? posisiSelect.value : '';
        if (kendaraanId) {
            fetchPartLamaKm(kendaraanId, catSelectNew.value, posisiVal, idx);
        }
    }
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

// Auto-fill km pasang dari header KM — selalu sync karena field hidden
function syncKmPasang(idx) {
    const headerKm = document.getElementById('kilometer').value;
    const kmInput  = document.getElementById('km-pasang-' + idx);
    if (kmInput && headerKm) kmInput.value = headerKm;
}

// ── Kategori inline ───────────────────────────────────────────
function onCategoryChange(select, idx) {
    // Auto-fill limit rule
    const kendaraanId = document.getElementById('kendaraan_id').value;
    if (kendaraanId && select.value) {
        fetchLimitRule(kendaraanId, select.value, idx);
    }
    // Fetch km_pasang part lama (pakai kategori baru + posisi saat ini)
    const posisiVal = document.querySelector('[name="parts[' + idx + '][posisi]"]')?.value || '';
    if (kendaraanId && select.value) {
        partLamaKmCache = {}; // reset cache saat kategori berubah
        fetchPartLamaKm(kendaraanId, select.value, posisiVal, idx);
    }
    calcKeteranganLimit(idx);
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

        // Simpan ke limitRulesMap agar calcKeteranganLimit bisa pakai
        if (data) {
            limitRulesMap[categoryId] = {
                category_id : parseInt(categoryId),
                limit_price : data.limit_price  ? parseInt(data.limit_price)  : null,
                limit_km    : data.limit_km      ? parseInt(data.limit_km)     : null,
                limit_nilai : parseInt(data.limit_nilai  || 12),
                limit_satuan: data.limit_satuan || 'bulan',
            };
        } else {
            delete limitRulesMap[categoryId];
        }

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
            calcKeteranganLimit(idx);
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

        // Recalc keterangan setelah interval dan limit rules ter-update
        calcKeteranganLimit(idx);
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
    // Load limit rules baru untuk kendaraan ini (juga re-fetch per-baris)
    if (val) {
        partLamaKmCache = {}; // reset cache part lama saat kendaraan berubah
        loadLimitRules(val).then(function() {
            // Setelah map ter-update, re-fetch per baris agar interval ikut update
            document.querySelectorAll('[id^="cat-select-"]').forEach(function(catSel) {
                var idxMatch = catSel.id.match(/cat-select-(\d+)/);
                if (!idxMatch) return;
                var rowIdx = idxMatch[1];
                if (catSel.value && catSel.value !== '__new__') {
                    fetchLimitRule(val, catSel.value, rowIdx);
                }
            });
        });
    } else {
        limitRulesMap = {};
        recalcAllKeterangan();
    }
    generateKeterangan();
}

document.getElementById('kilometer').addEventListener('input', function() {
    document.getElementById('km-badge').classList.add('hidden');
    // Sync semua km-pasang (hidden) ke nilai header KM
    document.querySelectorAll('[id^="km-pasang-"]').forEach(input => {
        input.value = this.value;
    });
    // Recalc keterangan semua baris (KM berubah)
    recalcAllKeterangan();
});

// Recalc keterangan saat tanggal service berubah
document.querySelector('[name="tanggal_service"]')?.addEventListener('change', function() {
    recalcAllKeterangan();
});

// ── Auto-generate keterangan slug ──────────────────────────────
function generateKeterangan() {
    const kendaraanSel = document.getElementById('kendaraan_id');
    const nopolOpt = kendaraanSel?.options[kendaraanSel.selectedIndex];
    const nopol = (nopolOpt?.dataset?.nopol || '').toLowerCase().replace(/[^a-z0-9]/g, '');
    const merk  = (nopolOpt?.dataset?.merk  || '').toLowerCase().replace(/[^a-z0-9]/g, '');

    const partNames = [];
    document.querySelectorAll('[name$="[nama_part]"]').forEach(inp => {
        const val = inp.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        if (val) partNames.push(val);
    });

    const kendaraanSlug = merk + nopol;
    const partSlug = partNames.slice(0, 4).join('-');
    const parts = [kendaraanSlug, partSlug].filter(Boolean).join('-');
    const slug = nopol ? ('service-' + parts).substring(0, 500) : '';

    const el = document.getElementById('keterangan_pengadaan');
    if (el) el.value = slug;
}

// ── Init prefill (dari reminder) ─────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    @if ($prefill)
        // Set kendaraan dan trigger auto-fill KM + load limit rules
        const kendaraanSelect = document.getElementById('kendaraan_id');
        kendaraanSelect.value = '{{ $prefill["kendaraan_id"] }}';

        // Load limit rules dulu, baru render baris part agar keterangan langsung akurat
        loadLimitRules('{{ $prefill["kendaraan_id"] }}').then(function() {
            onKendaraanChange('{{ $prefill["kendaraan_id"] }}');

        @if(($prefill['source'] ?? '') === 'edit_po' && !empty($prefill['all_parts']))
            {{-- Ajukan ulang dari PO ditolak: render semua parts --}}
            const allParts  = @json($prefill['all_parts']);
            const tempFiles = @json($prefill['temp_files'] ?? []);
            allParts.forEach(function(part, idx) {
                const oldBukti = (tempFiles.parts && tempFiles.parts[idx] && tempFiles.parts[idx].bukti)
                    ? tempFiles.parts[idx].bukti
                    : [];
                addPartRow({
                    nama_part:        part.nama_part       || '',
                    category_id:      part.category_id     || '',
                    supplier_id:      part.supplier_id     || '',
                    posisi:           part.posisi           || '',
                    part_number:      part.part_number      || '',
                    interval_nilai:   part.interval_nilai   || 12,
                    interval_satuan:  part.interval_satuan  || 'bulan',
                    biaya:            part.biaya            || 0,
                    kondisi:          part.kondisi          || '',
                    keterangan_limit: part.keterangan_limit || part.keterangan || '',
                    nama_bank:        part.nama_bank        || '',
                    no_rekening:      part.no_rekening      || '',
                    nama_rekening:    part.nama_rekening     || '',
                    old_files:        oldBukti,
                });
            });
        @elseif(($prefill['source'] ?? '') === 'edit_pembayaran' && !empty($prefill['all_parts']))
            {{-- Ajukan ulang dari Pembayaran ditolak: render semua parts --}}
            const allParts  = @json($prefill['all_parts']);
            const tempFiles = @json($prefill['temp_files'] ?? []);
            allParts.forEach(function(part, idx) {
                const oldBukti = (tempFiles.parts && tempFiles.parts[idx] && tempFiles.parts[idx].bukti)
                    ? tempFiles.parts[idx].bukti
                    : [];
                addPartRow({
                    nama_part:        part.nama_part       || '',
                    category_id:      part.category_id     || '',
                    supplier_id:      part.supplier_id     || '',
                    posisi:           part.posisi           || '',
                    part_number:      part.part_number      || '',
                    interval_nilai:   part.interval_nilai   || 12,
                    interval_satuan:  part.interval_satuan  || 'bulan',
                    biaya:            part.biaya            || 0,
                    kondisi:          part.kondisi          || '',
                    keterangan_limit: part.keterangan_limit || part.keterangan || '',
                    nama_bank:        part.nama_bank        || '',
                    no_rekening:      part.no_rekening      || '',
                    nama_rekening:    part.nama_rekening     || '',
                    old_files:        oldBukti,
                });
            });
        @else
            {{-- Tambah row pre-filled tunggal (reminder / part) --}}
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
        }); // end loadLimitRules().then
    @endif
    generateKeterangan();
});
</script>

{{-- Modal Create Supplier --}}
<div id="supplierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-800">Tambah Supplier / Bengkel Baru</h3>
                <p class="text-xs text-gray-500 mt-0.5">Isi data supplier/bengkel dengan lengkap</p>
            </div>
            <button type="button" onclick="closeSupplierModal()"
                class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="supplierForm" onsubmit="event.preventDefault(); submitSupplier();" class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_supplier" required placeholder="CV/PT / Nama Bengkel"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="number" name="no_telp" required placeholder="08xxxxxxxxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Marketing</label>
                    <input type="text" name="nama_marketing" placeholder="Contoh: Budi Santoso"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Marketing</label>
                    <input type="number" name="kontak_marketing" placeholder="08xxxxxxxxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                <textarea name="alamat" rows="2" placeholder="Contoh: Jl. Merdeka No. 10, Jakarta"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <div id="supplierError" class="hidden text-xs text-red-500 bg-red-50 border border-red-200 rounded-lg p-2"></div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeSupplierModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" id="supplierSubmitBtn"
                    class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <i class="fa fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let _supplierTargetIdx = null;

function openSupplierModal(idx) {
    _supplierTargetIdx = (idx !== undefined) ? idx : null;
    document.getElementById('supplierModal').classList.remove('hidden');
    document.getElementById('supplierForm').reset();
    document.getElementById('supplierError').classList.add('hidden');
}
function closeSupplierModal() {
    document.getElementById('supplierModal').classList.add('hidden');
    document.getElementById('supplierForm').reset();
    document.getElementById('supplierError').classList.add('hidden');
    _supplierTargetIdx = null;
}
function submitSupplier() {
    const form      = document.getElementById('supplierForm');
    const formData  = new FormData(form);
    const submitBtn = document.getElementById('supplierSubmitBtn');
    const errorDiv  = document.getElementById('supplierError');
    const namaInput = form.querySelector('[name="nama_supplier"]');
    const namaBaru  = namaInput ? namaInput.value.trim().toLowerCase() : '';

    // Cek duplikat client-side — cek dari allSuppliers
    const existingNames = allSuppliers.map(s => s.nama.toLowerCase());
    if (namaBaru && existingNames.includes(namaBaru)) {
        errorDiv.textContent = 'Supplier "' + namaInput.value.trim() + '" sudah ada dalam daftar.';
        errorDiv.classList.remove('hidden');
        return;
    }

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
            // Push ke array global agar row baru ikut ter-populate
            allSuppliers.push({ id: data.data.id, nama: data.data.nama_supplier });

            // Tambah option ke semua select supplier yang ada di halaman
            document.querySelectorAll('[id^="supplier-select-"]').forEach(function(select) {
                const option = new Option(data.data.nama_supplier, data.data.id, false, false);
                select.add(option);
            });

            // Jika ada target spesifik (dari baris part tertentu), pilih di sana
            if (_supplierTargetIdx !== null) {
                const targetSelect = document.getElementById('supplier-select-' + _supplierTargetIdx);
                if (targetSelect) targetSelect.value = data.data.id;
            }

            closeSupplierModal();
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
