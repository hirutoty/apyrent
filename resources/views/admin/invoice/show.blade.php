@extends('admin.layouts.app')

@section('title', 'Detail Invoice - ' . $invoice->invoice_no)

@section('content')
<div class="p-5">

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER CARD --}}
    <div class="bg-white rounded-xl shadow mb-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b p-5">
            <div>
                <a href="{{ route('invoices.index') }}" class="text-sm text-gray-400 hover:text-blue-600 mb-1 inline-flex items-center gap-1">
                    <i class="fa fa-arrow-left text-xs"></i> Kembali ke Daftar Invoice
                </a>
                <h2 class="text-xl font-bold text-gray-800 mt-1">{{ $invoice->invoice_no }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $invoice->customer_name }} &mdash; {{ optional($invoice->invoice_date)->format('d M Y') }}</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                    <i class="fa fa-file-pdf text-xs"></i> Preview PDF
                </a>
                <form action="{{ route('invoices.email', $invoice->id) }}" method="POST">
                    @csrf
                    <button class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                        <i class="fa fa-envelope"></i> Kirim Email
                    </button>
                </form>
            </div>
        </div>

        {{-- INFO SINGKAT --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-0 divide-x divide-y">
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 mb-0.5">Kendaraan</p>
                <p class="text-sm font-semibold text-gray-800">
                    {{ $invoice->kendaraan ? $invoice->kendaraan->merk . ' – ' . $invoice->kendaraan->nopol : '-' }}
                </p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 mb-0.5">PPN / PPh</p>
                <p class="text-sm font-semibold text-gray-800">{{ $invoice->ppn ?? 0 }}% / {{ $invoice->pph ?? 0 }}%</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 mb-0.5">Status</p>
                @php
                    $warna = match($invoice->status) {
                        'lunas'   => 'green', 'partial' => 'yellow',
                        'overdue' => 'red',   default   => 'gray',
                    };
                @endphp
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $warna }}-100 text-{{ $warna }}-700">
                    {{ strtoupper($invoice->status) }}
                </span>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 mb-0.5">Pembayaran</p>
                @php $wp = $invoice->payment_status === 'paid' ? 'green' : 'red'; @endphp
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $wp }}-100 text-{{ $wp }}-700">
                    {{ strtoupper($invoice->payment_status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- PERIODE & REMAKS --}}
    <div class="bg-white rounded-xl shadow">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <div>
                <h3 class="text-base font-bold text-gray-800">Periode & Remaks</h3>
                <p class="text-xs text-gray-400 mt-0.5">Kelola rincian periode dan item tagihan invoice</p>
            </div>
            <button id="btnTambahPeriode"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                <i class="fa fa-plus"></i> Tambah Periode
            </button>
        </div>

        {{-- LIST PERIODE --}}
        <div id="periodeList" class="divide-y">
            {{-- Diisi via JS --}}
        </div>

        {{-- SUMMARY TOTAL --}}
        <div class="border-t px-5 py-4">
            <div class="flex justify-end">
                <table class="text-sm">
                    <tr>
                        <td class="text-gray-500 pr-8 py-1">Total</td>
                        <td class="text-right font-semibold text-gray-800 min-w-[130px]" id="summaryTotal">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="text-gray-500 pr-8 py-1">PPN {{ $invoice->ppn ?? 0 }}%</td>
                        <td class="text-right text-gray-800" id="summaryPpn">-</td>
                    </tr>
                    <tr>
                        <td class="text-gray-500 pr-8 py-1">Sub Total</td>
                        <td class="text-right text-gray-800" id="summarySubTotal">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="text-gray-500 pr-8 py-1">PPh {{ $invoice->pph ?? 0 }}%</td>
                        <td class="text-right text-gray-800" id="summaryPph">-</td>
                    </tr>
                    <tr class="border-t">
                        <td class="text-gray-800 font-bold pr-8 py-2">Total Invoice</td>
                        <td class="text-right font-bold text-blue-700 text-base" id="summaryGrand">Rp 0</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ===================== MODAL TAMBAH PERIODE ===================== --}}
<div id="modalPeriode" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 id="modalPeriodeTitle" class="text-base font-bold text-gray-800">Tambah Periode</h3>
            <button type="button" id="closeModalPeriode" class="text-gray-400 hover:text-red-500">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <input type="hidden" id="editPeriodeId" value="">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Awal <span class="text-red-500">*</span></label>
                <input type="date" id="periodeAwal"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Akhir</label>
                <input type="date" id="periodeAkhir"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
        </div>
        <div class="border-t px-6 py-4 flex justify-end gap-2">
            <button type="button" id="closeModalPeriode2"
                class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
            <button type="button" id="savePeriode"
                class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl">Simpan</button>
        </div>
    </div>
</div>

{{-- ===================== MODAL TAMBAH REMAK ===================== --}}
<div id="modalRemak" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 id="modalRemakTitle" class="text-base font-bold text-gray-800">Tambah Remaks</h3>
            <button type="button" id="closeModalRemak" class="text-gray-400 hover:text-red-500">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <input type="hidden" id="editRemakId" value="">
            <input type="hidden" id="currentPeriodeId" value="">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan (Remaks) <span class="text-red-500">*</span></label>
                <textarea id="remakText" rows="3"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                    placeholder="Contoh: Sewa Mobil Innova Reborn Nopol B 2471 SYM"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">QTY <span class="text-red-500">*</span></label>
                    <input type="number" id="remakQty" value="1" min="1"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="remakPrice" value="0" min="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
        </div>
        <div class="border-t px-6 py-4 flex justify-end gap-2">
            <button type="button" id="closeModalRemak2"
                class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
            <button type="button" id="saveRemak"
                class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl">Simpan</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const INVOICE_ID = {{ $invoice->id }};
const PPN_PCT    = {{ floatval($invoice->ppn ?? 0) }};
const PPH_PCT    = {{ floatval($invoice->pph ?? 0) }};
const BASE_URL   = '/admin/invoices/' + INVOICE_ID + '/periodes';
const CSRF       = '{{ csrf_token() }}';

// ============================================================
//  HELPERS
// ============================================================
function rp(n) {
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}

function openModal(el)  { el.classList.remove('hidden'); el.classList.add('flex'); }
function closeModal(el) { el.classList.add('hidden');    el.classList.remove('flex'); }

async function apiFetch(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    const json = await res.json();
    if (!res.ok) throw new Error(json.message ?? 'Terjadi kesalahan.');
    return json;
}

// ============================================================
//  RENDER
// ============================================================
function calcSubtotal(remaks) {
    return remaks.reduce((s, r) => s + (r.qty ?? 1) * (r.price ?? 0), 0);
}

function renderSummary(allPeriodes) {
    const total      = allPeriodes.reduce((s, p) => s + calcSubtotal(p.remaks ?? []), 0);
    const ppnNom     = Math.round(total * PPN_PCT / 100);
    const subAfterPpn = total + ppnNom;
    const pphNom     = Math.round(total * PPH_PCT / 100);
    const grand      = subAfterPpn - pphNom;

    document.getElementById('summaryTotal').textContent    = total.toLocaleString('id-ID');
    document.getElementById('summaryPpn').textContent      = ppnNom > 0 ? ppnNom.toLocaleString('id-ID') : '-';
    document.getElementById('summarySubTotal').textContent = subAfterPpn.toLocaleString('id-ID');
    document.getElementById('summaryPph').textContent      = pphNom > 0 ? pphNom.toLocaleString('id-ID') : '-';
    document.getElementById('summaryGrand').textContent    = rp(grand);
}

function renderRemakRow(r, periodeId) {
    const subtotal = (r.qty ?? 1) * (r.price ?? 0);
    return `
        <tr class="border-b border-gray-100 hover:bg-gray-50" id="remak-row-${r.id}">
            <td class="px-4 py-2 text-sm text-gray-700">${r.remaks ?? ''}</td>
            <td class="px-4 py-2 text-sm text-center text-gray-700">${r.qty ?? 1}</td>
            <td class="px-4 py-2 text-sm text-right text-gray-700">${(r.price ?? 0).toLocaleString('id-ID')}</td>
            <td class="px-4 py-2 text-sm text-right text-gray-700">${subtotal.toLocaleString('id-ID')}</td>
            <td class="px-4 py-2 text-center">
                <div class="flex justify-center gap-1">
                    <button class="editRemakBtn text-yellow-500 hover:text-yellow-600 px-2 py-1 text-xs rounded hover:bg-yellow-50"
                        data-id="${r.id}" data-periode-id="${periodeId}"
                        data-remaks="${(r.remaks ?? '').replace(/"/g, '&quot;')}"
                        data-qty="${r.qty ?? 1}" data-price="${r.price ?? 0}">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="deleteRemakBtn text-red-500 hover:text-red-600 px-2 py-1 text-xs rounded hover:bg-red-50"
                        data-id="${r.id}" data-periode-id="${periodeId}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
}

function renderPeriode(p) {
    const remaks   = p.remaks ?? [];
    const subtotal = calcSubtotal(remaks);
    const awal     = formatDate(p.periode_awal);
    const akhir    = p.periode_akhir && p.periode_akhir !== p.periode_awal ? formatDate(p.periode_akhir) : '';

    const remakRows = remaks.map(r => renderRemakRow(r, p.id)).join('');

    return `
    <div class="px-5 py-4" id="periode-${p.id}">
        <div class="flex items-start justify-between mb-3">
            <div>
                <span class="text-sm font-semibold text-gray-800">${awal}${akhir ? ' <span class="text-gray-400 font-normal">s/d</span> ' + akhir : ''}</span>
                <span class="ml-2 text-xs text-gray-400">Sub Total: <strong>${rp(subtotal)}</strong></span>
            </div>
            <div class="flex gap-1 flex-shrink-0 ml-3">
                <button class="addRemakBtn text-blue-600 hover:bg-blue-50 px-2 py-1 text-xs rounded font-semibold border border-blue-200"
                    data-periode-id="${p.id}">
                    <i class="fa fa-plus mr-1"></i>Tambah Remaks
                </button>
                <button class="editPeriodeBtn text-yellow-500 hover:bg-yellow-50 px-2 py-1 text-xs rounded border border-yellow-200"
                    data-id="${p.id}" data-awal="${p.periode_awal ?? ''}" data-akhir="${p.periode_akhir ?? ''}">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="deletePeriodeBtn text-red-500 hover:bg-red-50 px-2 py-1 text-xs rounded border border-red-200"
                    data-id="${p.id}">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm" id="remak-table-${p.id}">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Remaks</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 w-16">QTY</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 w-32">Harga/Hari</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 w-32">Sub Total</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody id="remak-tbody-${p.id}">
                    ${remakRows || '<tr><td colspan="5" class="text-center py-4 text-gray-400 text-xs">Belum ada remaks. Klik "+ Tambah Remaks"</td></tr>'}
                </tbody>
            </table>
        </div>
    </div>`;
}

// ============================================================
//  LOAD DATA
// ============================================================
let allPeriodes = [];

async function loadPeriodes() {
    try {
        allPeriodes = await apiFetch(BASE_URL);
        const list = document.getElementById('periodeList');

        if (allPeriodes.length === 0) {
            list.innerHTML = `
                <div class="px-5 py-10 text-center text-gray-400">
                    <i class="fa fa-calendar-alt text-3xl mb-2 block"></i>
                    Belum ada periode. Klik "Tambah Periode" untuk memulai.
                </div>`;
        } else {
            list.innerHTML = allPeriodes.map(renderPeriode).join('');
            bindRemakButtons();
        }
        renderSummary(allPeriodes);
    } catch (e) {
        console.error(e);
    }
}

function bindRemakButtons() {
    // Edit remak
    document.querySelectorAll('.editRemakBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('editRemakId').value    = d.id;
            document.getElementById('currentPeriodeId').value = d.periodeId;
            document.getElementById('remakText').value      = d.remaks;
            document.getElementById('remakQty').value       = d.qty;
            document.getElementById('remakPrice').value     = d.price;
            document.getElementById('modalRemakTitle').textContent = 'Edit Remaks';
            openModal(document.getElementById('modalRemak'));
        });
    });

    // Delete remak
    document.querySelectorAll('.deleteRemakBtn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('Hapus remaks ini?')) return;
            const remakId  = this.dataset.id;
            const periodeId = this.dataset.periodeId;
            try {
                await apiFetch(`${BASE_URL}/${periodeId}/remaks/${remakId}`, 'DELETE');
                await loadPeriodes();
            } catch (e) { alert(e.message); }
        });
    });

    // Add remak
    document.querySelectorAll('.addRemakBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('editRemakId').value    = '';
            document.getElementById('currentPeriodeId').value = this.dataset.periodeId;
            document.getElementById('remakText').value      = '';
            document.getElementById('remakQty').value       = 1;
            document.getElementById('remakPrice').value     = 0;
            document.getElementById('modalRemakTitle').textContent = 'Tambah Remaks';
            openModal(document.getElementById('modalRemak'));
        });
    });

    // Edit periode
    document.querySelectorAll('.editPeriodeBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('editPeriodeId').value = d.id;
            document.getElementById('periodeAwal').value   = d.awal;
            document.getElementById('periodeAkhir').value  = d.akhir;
            document.getElementById('modalPeriodeTitle').textContent = 'Edit Periode';
            openModal(document.getElementById('modalPeriode'));
        });
    });

    // Delete periode
    document.querySelectorAll('.deletePeriodeBtn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('Hapus periode beserta semua remaks-nya?')) return;
            try {
                await apiFetch(`${BASE_URL}/${this.dataset.id}`, 'DELETE');
                await loadPeriodes();
            } catch (e) { alert(e.message); }
        });
    });
}

// ============================================================
//  MODAL PERIODE
// ============================================================
const modalPeriode = document.getElementById('modalPeriode');
document.getElementById('btnTambahPeriode').onclick = () => {
    document.getElementById('editPeriodeId').value = '';
    document.getElementById('periodeAwal').value   = '';
    document.getElementById('periodeAkhir').value  = '';
    document.getElementById('modalPeriodeTitle').textContent = 'Tambah Periode';
    openModal(modalPeriode);
};
document.getElementById('closeModalPeriode').onclick  = () => closeModal(modalPeriode);
document.getElementById('closeModalPeriode2').onclick = () => closeModal(modalPeriode);

document.getElementById('savePeriode').addEventListener('click', async () => {
    const id    = document.getElementById('editPeriodeId').value;
    const awal  = document.getElementById('periodeAwal').value;
    const akhir = document.getElementById('periodeAkhir').value;

    if (!awal) { alert('Tanggal awal wajib diisi.'); return; }

    const body = { periode_awal: awal, periode_akhir: akhir || null };

    try {
        if (id) {
            await apiFetch(`${BASE_URL}/${id}`, 'PUT', body);
        } else {
            await apiFetch(BASE_URL, 'POST', body);
        }
        closeModal(modalPeriode);
        await loadPeriodes();
    } catch (e) { alert(e.message); }
});

// ============================================================
//  MODAL REMAK
// ============================================================
const modalRemak = document.getElementById('modalRemak');
document.getElementById('closeModalRemak').onclick  = () => closeModal(modalRemak);
document.getElementById('closeModalRemak2').onclick = () => closeModal(modalRemak);

document.getElementById('saveRemak').addEventListener('click', async () => {
    const remakId   = document.getElementById('editRemakId').value;
    const periodeId = document.getElementById('currentPeriodeId').value;
    const remaks    = document.getElementById('remakText').value.trim();
    const qty       = parseInt(document.getElementById('remakQty').value, 10);
    const price     = parseFloat(document.getElementById('remakPrice').value);

    if (!remaks)       { alert('Keterangan remaks wajib diisi.'); return; }
    if (isNaN(qty) || qty < 1)   { alert('QTY harus minimal 1.'); return; }
    if (isNaN(price) || price < 0) { alert('Harga tidak valid.'); return; }

    const body = { remaks, qty, price };

    try {
        if (remakId) {
            await apiFetch(`${BASE_URL}/${periodeId}/remaks/${remakId}`, 'PUT', body);
        } else {
            await apiFetch(`${BASE_URL}/${periodeId}/remaks`, 'POST', body);
        }
        closeModal(modalRemak);
        await loadPeriodes();
    } catch (e) { alert(e.message); }
});

// ============================================================
//  INIT
// ============================================================
loadPeriodes();
</script>
@endpush

@endsection
