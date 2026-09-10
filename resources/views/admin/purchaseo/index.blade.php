@extends('admin.layouts.app')
@section('title', 'Purchase Order Approval')
@section('content')
<div class="space-y-6 p-5">
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

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Order Approval</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola approval purchase order dari pengeluaran kendaraan</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @php
                    $tabs = [
                        ['label' => 'Pending',   'count' => $totalPending,  'color' => 'yellow'],
                        ['label' => 'Disetujui', 'count' => $totalApproved, 'color' => 'green'],
                        ['label' => 'Ditolak',   'count' => $totalRejected, 'color' => 'red'],
                    ];
                @endphp
                @foreach ($tabs as $tab)
                    @php $isActive = $statusFilter === $tab['label']; @endphp
                    <a href="{{ route('purchase-order.index', ['status' => $tab['label']]) }}"
                        class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                            {{ $isActive
                                ? 'border-'.$tab['color'].'-600 text-'.$tab['color'].'-600 bg-'.$tab['color'].'-50/50'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <span>{{ $tab['label'] }}</span>
                        <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full
                            {{ $isActive ? 'bg-'.$tab['color'].'-100 text-'.$tab['color'].'-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">PO Number</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Source Type</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Vendor</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Items</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Harga</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $po)
                    <tr class="border-t border-gray-50 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $po->po_id }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $po->source_type ?? '-')) }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $po->vendor ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ number_format($po->total_barang ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $po->tanggal_po ? $po->tanggal_po->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3.5">
                            @if($po->status === 'Pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">
                                    <i class="fa fa-clock text-[8px]"></i> Pending
                                </span>
                            @elseif($po->status === 'Disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                                    <i class="fa fa-check text-[8px]"></i> Disetujui
                                </span>
                            @elseif($po->status === 'Ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                    <i class="fa fa-times text-[8px]"></i> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="viewDetail({{ $po->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <i class="fa fa-eye text-xs"></i> Detail
                                </button>

                                @if($po->status === 'Pending' && auth()->user()->role === 'superadmin')
                                    <button onclick="openApproveModal({{ $po->id }}, '{{ $po->po_id }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                        <i class="fa fa-check text-xs"></i> Approve
                                    </button>
                                    <button onclick="openRejectModal({{ $po->id }}, '{{ $po->po_id }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                        <i class="fa fa-times text-xs"></i> Reject
                                    </button>
                                @endif

                                @if(in_array($po->status, ['Pending', 'Ditolak']))
                                    <form action="{{ route('purchase-order.destroy', $po->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus Purchase Order ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                            <i class="fa fa-inbox text-3xl mb-2"></i>
                            <p>Tidak ada Purchase Order dengan status {{ $statusFilter }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($data->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Purchase Order Detail</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6" id="detailContent">
            <div class="flex items-center justify-center py-12">
                <i class="fa fa-spinner fa-spin text-2xl text-gray-400"></i>
            </div>
        </div>
    </div>
</div>

{{-- MODAL APPROVE (per-item GPS) --}}
<div id="approveModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Approve Purchase Order</h3>
                <p class="text-sm text-gray-500 mt-0.5">No PO: <span id="approvePoId" class="font-mono font-semibold text-blue-600"></span></p>
            </div>
            <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="approveModalLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data item...</p>
            </div>
        </div>
        <div id="approveModalContent" class="hidden flex-1 overflow-y-auto">
            <div id="approveKendaraanInfo" class="px-6 pt-4 pb-2"></div>
            <div class="px-6 pb-2">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <i class="fa fa-list-ul mr-1 text-green-500"></i> Item GPS — Centang yang ingin disetujui
                    </p>
                    <div class="flex gap-2">
                        <button type="button" onclick="approveSelectAll(true)"
                            class="text-[11px] text-green-600 font-medium px-2 py-0.5 bg-green-50 rounded-md border border-green-200 hover:bg-green-100">
                            Pilih Semua
                        </button>
                        <button type="button" onclick="approveSelectAll(false)"
                            class="text-[11px] text-gray-500 font-medium px-2 py-0.5 bg-gray-50 rounded-md border border-gray-200 hover:bg-gray-100">
                            Hapus Pilihan
                        </button>
                    </div>
                </div>
                <div id="approveItemList" class="space-y-2"></div>
            </div>
            <div class="px-6 pb-4 pt-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Catatan Approval <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="approveCatatan" rows="2" placeholder="Catatan untuk approval ini..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-green-400"></textarea>
            </div>
            <div id="approveSummary" class="mx-6 mb-4 px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-gray-600 hidden">
                <span id="approveSummaryText"></span>
            </div>
        </div>
        <div id="approveModalFooter" class="hidden border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
            <button type="button" onclick="closeApproveModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" id="approveSubmitBtn" onclick="submitApproveItems()"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-check"></i> Konfirmasi Approval
            </button>
        </div>
    </div>
</div>

{{-- MODAL REJECT --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Reject Purchase Order</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Anda akan menolak Purchase Order <span id="rejectPoId" class="font-mono font-bold"></span>.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    <i class="fa fa-times mr-1"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── DETAIL MODAL ──────────────────────────────────────────────
function viewDetail(poId) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<div class="flex items-center justify-center py-12"><i class="fa fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
    fetch('/admin/purchase-order/' + poId + '/detail')
        .then(r => r.json())
        .then(data => {
            content.innerHTML = data.success ? buildDetailContent(data) : '<div class="text-center text-red-600 py-8"><p>' + (data.message || 'Error') + '</p></div>';
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-red-600 py-8"><p>' + err.message + '</p></div>';
        });
}

function buildDetailContent(data) {
    const po = data.po;
    const details = data.details;
    let html = '<div class="space-y-6">'
        + '<div class="bg-gray-50 rounded-lg p-4"><div class="grid grid-cols-2 gap-4">'
        + '<div><p class="text-xs text-gray-500">PO Number</p><p class="font-mono font-bold text-blue-700">' + po.po_id + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Source Type</p><p class="font-semibold capitalize">' + (po.source_type || '').replace(/_/g, ' ') + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Vendor</p><p class="font-medium">' + (po.vendor || '-') + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Tanggal PO</p><p class="font-medium">' + (po.tanggal_po || '-') + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Total Items</p><p class="font-medium">' + po.total_barang + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Total Harga</p><p class="font-bold text-lg">Rp ' + Number(po.total_harga).toLocaleString('id-ID') + '</p></div>'
        + '</div></div>'
        + '<div class="flex items-center gap-2"><span class="text-sm text-gray-600">Status:</span>' + getStatusBadge(po.status) + '</div>';

    if (details.type === 'gps') {
        const k = details.kendaraan || {};
        html += '<div><h4 class="font-semibold text-gray-800 mb-2">Kendaraan</h4>'
            + '<div class="bg-blue-50 rounded-lg p-3 text-sm space-y-1">'
            + '<p><span class="text-gray-600">Nopol:</span> <b>' + (k.nopol || '-') + '</b></p>'
            + '<p><span class="text-gray-600">Merk:</span> ' + (k.merk || '-') + '</p>'
            + '<p><span class="text-gray-600">Tgl Bayar:</span> ' + (details.tanggal_bayar || '-') + '</p>'
            + '<p><span class="text-gray-600">Berlaku s/d:</span> ' + (details.tanggal_habis || '-') + '</p>'
            + '</div></div>';

        html += '<div><h4 class="font-semibold text-gray-800 mb-2">GPS Items (' + details.items.length + ')</h4><div class="space-y-2">';
        details.items.forEach(function(item) {
            html += '<div class="border border-gray-200 rounded-lg p-3">'
                + '<div class="flex items-start justify-between mb-1">'
                + '<div><p class="font-semibold text-gray-800">' + (item.gps_name || '-') + '</p>'
                + '<p class="text-xs text-gray-500">Type: ' + (item.type || '-') + '</p></div>'
                + '<p class="font-bold text-blue-600">Rp ' + Number(item.biaya_sewa || 0).toLocaleString('id-ID') + '</p></div>'
                + '<div class="grid grid-cols-3 gap-2 text-xs text-gray-600">'
                + '<div><span class="text-gray-400">Bank:</span> ' + (item.nama_bank || '-') + '</div>'
                + '<div><span class="text-gray-400">Rek:</span> ' + (item.no_rekening || '-') + '</div>'
                + '<div><span class="text-gray-400">A/n:</span> ' + (item.nama_pemilik || '-') + '</div>'
                + '</div></div>';
        });
        html += '</div></div>';
    }

    if (po.status !== 'Pending') {
        html += '<div class="border-t pt-4"><h4 class="font-semibold text-gray-800 mb-2">Approval Info</h4>'
            + '<div class="bg-gray-50 rounded-lg p-3 space-y-1 text-sm">'
            + '<p><span class="text-gray-600">Oleh:</span> ' + (po.disetujui_oleh || '-') + '</p>'
            + '<p><span class="text-gray-600">Tanggal:</span> ' + (po.tanggal_persetujuan || '-') + '</p>'
            + (po.catatan_approval ? '<p><span class="text-gray-600">Catatan:</span> ' + po.catatan_approval + '</p>' : '')
            + (po.pembayaran_no_pr ? '<p><span class="text-gray-600">Pembayaran:</span> <b class="font-mono">' + po.pembayaran_no_pr + '</b></p>' : '')
            + '</div></div>';
    }

    html += '</div>';
    return html;
}

function getStatusBadge(status) {
    const map = {
        'Pending':   '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600"><i class="fa fa-clock text-[8px]"></i> Pending</span>',
        'Disetujui': '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600"><i class="fa fa-check text-[8px]"></i> Disetujui</span>',
        'Ditolak':   '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600"><i class="fa fa-times text-[8px]"></i> Ditolak</span>',
    };
    return map[status] || status;
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.replace('flex', 'hidden');
    document.getElementById('detailModal').classList.add('hidden');
}

// ── APPROVE MODAL (per-item GPS) ──────────────────────────────
let currentApprovePoId = null;
let approveItemDecisions = [];

function openApproveModal(poId, poNumber) {
    currentApprovePoId = poId;
    document.getElementById('approvePoId').textContent = poNumber;
    document.getElementById('approveModalLoading').classList.remove('hidden');
    document.getElementById('approveModalContent').classList.add('hidden');
    document.getElementById('approveModalFooter').classList.add('hidden');
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal').classList.add('flex');

    fetch('/admin/purchase-order/' + poId + '/detail')
        .then(r => r.json())
        .then(function(data) {
            if (!data.success) throw new Error(data.message || 'Gagal memuat data');
            renderApproveItems(data);
            document.getElementById('approveModalLoading').classList.add('hidden');
            document.getElementById('approveModalContent').classList.remove('hidden');
            document.getElementById('approveModalFooter').classList.remove('hidden');
        })
        .catch(function(err) {
            document.getElementById('approveModalLoading').innerHTML =
                '<div class="text-center text-red-500 py-8"><i class="fa fa-exclamation-circle text-2xl mb-2"></i><p class="text-sm">' + err.message + '</p></div>';
        });
}

function renderApproveItems(data) {
    const details = data.details;
    const items = details.items || [];
    approveItemDecisions = items.map(function() { return { action: null, buktiFile: null }; });

    // Info kendaraan
    const k = details.kendaraan || {};
    document.getElementById('approveKendaraanInfo').innerHTML =
        '<div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-3 flex items-center gap-3">'
        + '<i class="fa fa-car text-green-600"></i>'
        + '<div class="text-sm">'
        + '<span class="font-bold text-gray-800">' + (k.nopol || '-') + '</span>'
        + '<span class="text-gray-500 ml-2">' + (k.merk || '') + '</span>'
        + '<span class="ml-3 text-gray-400 text-xs">Tgl Bayar: <b>' + (details.tanggal_bayar || '-') + '</b></span>'
        + '<span class="ml-3 text-gray-400 text-xs">Berlaku s/d: <b>' + (details.tanggal_habis || '-') + '</b></span>'
        + '</div></div>';

    const list = document.getElementById('approveItemList');
    list.innerHTML = '';

    items.forEach(function(item, idx) {
        const bankInfo = [
            item.nama_bank    ? '<span><i class="fa fa-building text-[9px]"></i> ' + item.nama_bank + '</span>' : '',
            item.no_rekening  ? '<span class="font-mono">' + item.no_rekening + '</span>' : '',
            item.nama_pemilik ? '<span>a/n ' + item.nama_pemilik + '</span>' : '',
        ].filter(Boolean).join(' ');

        const card = document.createElement('div');
        card.id = 'approve-item-card-' + idx;
        card.className = 'border border-red-200 rounded-xl overflow-hidden transition-all bg-red-50/10';

        // Row utama
        const row = document.createElement('div');
        row.className = 'flex items-start gap-3 px-4 py-3';
        row.innerHTML = '<div class="flex-shrink-0 pt-0.5">'
            + '<input type="checkbox" id="item-chk-' + idx + '"'
            + ' class="w-4 h-4 rounded text-green-600 cursor-pointer border-gray-300 focus:ring-green-400">'
            + '</div>'
            + '<div class="flex-1 min-w-0">'
            + '<label for="item-chk-' + idx + '" class="cursor-pointer">'
            + '<div class="flex items-center gap-2 flex-wrap">'
            + '<span class="text-xs text-gray-400">#' + (idx + 1) + '</span>'
            + '<span class="font-semibold text-gray-800 text-sm">' + (item.gps_name || '-') + '</span>'
            + '<span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-mono">' + (item.type || '-') + '</span>'
            + '<span class="ml-auto text-xs font-bold text-emerald-600">Rp ' + formatNumber(item.biaya_sewa || 0) + '</span>'
            + '</div>'
            + (bankInfo ? '<div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-gray-400">' + bankInfo + '</div>' : '')
            + '</label>'
            + '</div>'
            + '<div id="approve-item-badge-' + idx + '" class="flex-shrink-0 self-center">'
            + '<span class="text-[10px] font-semibold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-times text-[8px]"></i> Ditolak</span>'
            + '</div>';
        card.appendChild(row);

        // Panel bukti (tersembunyi, muncul saat checked) + panel alasan (default tampil karena default = ditolak)
        const panel = document.createElement('div');
        panel.id = 'approve-item-panel-' + idx;
        panel.className = 'hidden px-4 pb-3 pt-1 border-t border-green-100 bg-green-50/30';
        panel.innerHTML = '<p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">'
            + '<i class="fa fa-paperclip mr-1 text-green-500"></i> Upload Bukti Bayar '
            + '<span class="font-normal text-gray-400">(opsional)</span></p>'
            + '<label for="approve-bukti-input-' + idx + '"'
            + ' class="flex items-center gap-2 px-3 py-2 border border-dashed border-green-300 rounded-lg cursor-pointer hover:bg-green-50 bg-white">'
            + '<i class="fa fa-paperclip text-green-400 text-xs"></i>'
            + '<span class="text-xs text-gray-500" id="approve-bukti-label-' + idx + '">Klik untuk pilih file (JPG, PNG, PDF, max 5MB)</span>'
            + '</label>'
            + '<input id="approve-bukti-input-' + idx + '" type="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="hidden">';
        card.appendChild(panel);

        // Panel alasan penolakan (default tampil karena default = ditolak)
        const rejectPanel = document.createElement('div');
        rejectPanel.id = 'reject-item-panel-' + idx;
        rejectPanel.className = 'px-4 pb-3 pt-2 border-t border-red-100 bg-red-50/20';
        rejectPanel.innerHTML = '<label class="text-[11px] font-semibold text-red-500 uppercase tracking-wide mb-1.5 block">'
            + '<i class="fa fa-comment-dots mr-1"></i> Alasan Penolakan '
            + '<span class="font-normal text-red-400">(opsional)</span></label>'
            + '<textarea id="reject-catatan-' + idx + '" rows="2"'
            + ' placeholder="Tulis alasan penolakan item ini..."'
            + ' class="w-full text-xs px-3 py-2 border border-red-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400 bg-white"></textarea>';
        card.appendChild(rejectPanel);

        // Event listeners
        const chk = row.querySelector('input[type=checkbox]');
        chk.addEventListener('change', function() { toggleApproveItem(idx, this.checked); });
        panel.querySelector('input[type=file]').addEventListener('change', function(e) { handleApproveBuktiFile(e, idx); });

        list.appendChild(card);
    });

    updateApproveSummary();
}

function toggleApproveItem(idx, checked) {
    approveItemDecisions[idx].action = checked ? 'approved' : null;
    approveItemDecisions[idx].buktiFile = null;
    const card        = document.getElementById('approve-item-card-' + idx);
    const buktiPanel  = document.getElementById('approve-item-panel-' + idx);
    const rejectPanel = document.getElementById('reject-item-panel-' + idx);
    const badge       = document.getElementById('approve-item-badge-' + idx);
    if (checked) {
        card.className = 'border border-green-300 rounded-xl overflow-hidden transition-all bg-green-50/20';
        buktiPanel.classList.remove('hidden');
        rejectPanel.classList.add('hidden');
        if (badge) badge.innerHTML = '<span class="text-[10px] font-semibold text-green-700 bg-green-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-check text-[8px]"></i> Disetujui</span>';
    } else {
        card.className = 'border border-red-200 rounded-xl overflow-hidden transition-all bg-red-50/10';
        buktiPanel.classList.add('hidden');
        rejectPanel.classList.remove('hidden');
        document.getElementById('approve-bukti-label-' + idx).textContent = 'Klik untuk pilih file (JPG, PNG, PDF, max 5MB)';
        if (badge) badge.innerHTML = '<span class="text-[10px] font-semibold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-times text-[8px]"></i> Ditolak</span>';
    }
    updateApproveSummary();
}

function approveSelectAll(select) {
    approveItemDecisions.forEach(function(d, idx) {
        const chk = document.getElementById('item-chk-' + idx);
        if (chk) { chk.checked = select; toggleApproveItem(idx, select); }
    });
}

function handleApproveBuktiFile(event, idx) {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) { alert('File terlalu besar. Max 5MB.'); event.target.value = ''; return; }
    approveItemDecisions[idx].buktiFile = file;
    document.getElementById('approve-bukti-label-' + idx).textContent = '\u2713 ' + file.name;
}

function updateApproveSummary() {
    const approved = approveItemDecisions.filter(function(d) { return d.action === 'approved'; }).length;
    const total    = approveItemDecisions.length;
    const summary  = document.getElementById('approveSummary');
    const text     = document.getElementById('approveSummaryText');
    if (approved > 0 || total > 0) {
        summary.classList.remove('hidden');
        const rejected = total - approved;
        text.innerHTML = '<i class="fa fa-check-circle text-green-500 mr-1"></i>'
            + '<b>' + approved + '</b> item disetujui'
            + (rejected > 0 ? ', <i class="fa fa-times-circle text-red-400 ml-2 mr-1"></i><b>' + rejected + '</b> item akan ditolak' : '');
    } else {
        summary.classList.add('hidden');
    }
}

async function submitApproveItems() {
    const approved = approveItemDecisions.filter(function(d) { return d.action === 'approved'; });
    if (approved.length === 0) { alert('Pilih minimal 1 item yang ingin disetujui.'); return; }

    const total    = approveItemDecisions.length;
    const rejected = total - approved.length;
    const msg = 'Approve ' + approved.length + ' item'
        + (rejected > 0 ? ', tolak ' + rejected + ' item yang tidak dipilih?' : '?');
    if (!confirm(msg)) return;

    const btn = document.getElementById('approveSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';

    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]');
    formData.append('_token', token ? token.content : '');
    formData.append('catatan', document.getElementById('approveCatatan').value);

    // Kirim semua item — yang tidak dicentang otomatis jadi rejected
    approveItemDecisions.forEach(function(d, idx) {
        const action  = d.action === 'approved' ? 'approved' : 'rejected';
        const catatan = action === 'rejected'
            ? (document.getElementById('reject-catatan-' + idx) || {}).value || ''
            : '';
        formData.append('items[' + idx + '][action]',  action);
        formData.append('items[' + idx + '][catatan]', catatan);
        if (action === 'approved' && d.buktiFile) {
            formData.append('items[' + idx + '][bukti]', d.buktiFile);
        }
    });

    try {
        const res    = await fetch('/admin/purchase-order/' + currentApprovePoId + '/approve-items', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) {
            window.location.href = result.redirect || window.location.href;
        } else {
            alert(result.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Approval';
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Approval';
    }
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveModal').classList.remove('flex');
    document.getElementById('approveCatatan').value = '';
    currentApprovePoId = null;
    approveItemDecisions = [];
}

function formatNumber(n) {
    return Number(n).toLocaleString('id-ID');
}

// ── REJECT MODAL ──────────────────────────────────────────────
function openRejectModal(poId, poNumber) {
    document.getElementById('rejectPoId').textContent = poNumber;
    document.getElementById('rejectForm').action = '/admin/purchase-order/' + poId + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

// Close on backdrop click
document.getElementById('detailModal').addEventListener('click',  function(e) { if (e.target === this) closeDetailModal();  });
document.getElementById('approveModal').addEventListener('click', function(e) { if (e.target === this) closeApproveModal(); });
document.getElementById('rejectModal').addEventListener('click',  function(e) { if (e.target === this) closeRejectModal();  });
</script>
@endpush
