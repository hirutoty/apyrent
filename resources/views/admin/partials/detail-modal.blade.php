{{-- ════════════════════════════════════════════════════════════════
     SHARED DETAIL MODAL — dipakai di Pajak, Asuransi, GPS, KIR
     Include: @include('admin.partials.detail-modal')
═══════════════════════════════════════════════════════════════════ --}}
<div id="modalDetailPajak"
     class="fixed inset-0 z-[999] flex items-center justify-center hidden"
     onclick="if(event.target===this) closeDetailModal()">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl mx-4 max-h-[92vh] flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-lg font-bold text-gray-800" id="modalDetailPajakTitle">Detail</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="modalDetailPajakSubtitle">–</p>
            </div>
            <button onclick="closeDetailModal()"
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <i class="fa fa-times text-gray-500 text-sm"></i>
            </button>
        </div>

        {{-- Body (scrollable) --}}
        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-6">

            {{-- ── Info Cards (diisi dinamis oleh JS sesuai type) ── --}}
            <div id="dpInfoCards" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                {{-- Baris 1: Kendaraan + field utama --}}
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Kendaraan</p>
                    <p class="font-bold text-gray-800 text-sm" id="dpNopol">–</p>
                    <p class="text-xs text-gray-500" id="dpMerk">–</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100" id="dpCard2">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1" id="dpCard2Label">–</p>
                    <p class="font-semibold text-gray-800 text-sm" id="dpCard2Val">–</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                    <p class="text-[10px] text-blue-400 uppercase tracking-wider mb-1" id="dpBiayaLabel">Biaya</p>
                    <p class="font-bold text-blue-700 text-sm" id="dpNominal">–</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100" id="dpCard4">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1" id="dpCard4Label">–</p>
                    <p class="font-semibold text-sm" id="dpCard4Val">–</p>
                </div>

                {{-- Baris 2: tanggal + extra --}}
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100" id="dpCard5">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1" id="dpCard5Label">–</p>
                    <p class="font-semibold text-gray-700 text-sm" id="dpCard5Val">–</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100" id="dpCard6">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1" id="dpCard6Label">–</p>
                    <p class="font-semibold text-gray-700 text-sm" id="dpCard6Val">–</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 col-span-2" id="dpCard7">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1" id="dpCard7Label">–</p>
                    <p class="text-gray-600 text-sm" id="dpCard7Val">–</p>
                </div>
            </div>

            {{-- ── Chart perpanjangan Jan-Des ── --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">
                        <i class="fa fa-chart-bar text-blue-500 mr-1.5"></i>
                        <span id="dpChartTitle">Biaya Perpanjangan per Bulan</span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-500">Tahun:</label>
                        <select id="dpTahunFilter"
                                onchange="fetchDetailModal(window._currentDetailType, window._currentDetailId, this.value)"
                                class="text-xs border border-gray-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-300">
                        </select>
                    </div>
                </div>
                <div style="height: 240px; position: relative;">
                    <canvas id="dpBarChart"></canvas>
                </div>
            </div>

            {{-- ── Tabel riwayat perpanjangan ── --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fa fa-history text-purple-500 mr-1.5"></i>Riwayat Perpanjangan
                </h3>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50">
                            <tr id="dpHistoryHead"></tr>
                        </thead>
                        <tbody id="dpHistoryBody" class="divide-y divide-gray-50"></tbody>
                    </table>
                </div>
                <p id="dpHistoryEmpty" class="hidden text-center text-gray-400 py-8 text-sm">
                    <i class="fa fa-inbox text-2xl text-gray-300 block mb-2"></i>
                    Belum ada riwayat perpanjangan
                </p>
            </div>

        </div>
    </div>
</div>

<script>
// ─── DETAIL MODAL ENGINE (shared: pajak / asuransi / gps / kir) ─────────────
window._currentDetailType = null;
window._currentDetailId   = null;
window._dpChart           = null;

const DETAIL_ENDPOINTS = {
    pajak:    id => `/admin/pajak/${id}/detail`,
    asuransi: id => `/admin/asuransi-kendaraan/${id}/detail`,
    gps:      id => `/admin/gps-kendaraan/${id}/detail`,
    kir:      id => `/admin/kir/${id}/detail`,
};

const DETAIL_COLUMNS = {
    pajak:    ['No','Jenis Pajak','Nominal','Tgl Bayar','Jatuh Tempo','Status','Keterangan','Diperpanjang','Bukti'],
    asuransi: ['No','Perusahaan','Jenis','Biaya','Tgl Mulai','Tgl Berakhir','Durasi','Tgl Bayar','Diperpanjang','Bukti'],
    gps:      ['No','Nama GPS','Type','Biaya Sewa','Durasi','Tgl Pasang','Tgl Habis','Status Sewa','Diperpanjang','Bukti'],
    kir:      ['No','No Uji','Biaya','Masa Berlaku','Tgl Bayar','Diperpanjang','Bukti'],
};

const DETAIL_CHART_LABELS = {
    pajak:    'Nominal Perpanjangan per Bulan',
    asuransi: 'Biaya Asuransi per Bulan',
    gps:      'Biaya Sewa GPS per Bulan',
    kir:      'Biaya KIR per Bulan',
};

const DETAIL_CHART_COLORS = {
    pajak:    { bg: 'rgba(59,130,246,0.7)',  border: 'rgba(59,130,246,1)' },
    asuransi: { bg: 'rgba(16,185,129,0.7)',  border: 'rgba(16,185,129,1)' },
    gps:      { bg: 'rgba(99,102,241,0.7)',  border: 'rgba(99,102,241,1)' },
    kir:      { bg: 'rgba(245,158,11,0.7)',  border: 'rgba(245,158,11,1)' },
};

function fmtRp(v) {
    if (v == null || v === '') return '–';
    return 'Rp ' + Number(v).toLocaleString('id-ID');
}

function _setEl(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = (val !== null && val !== undefined && val !== '') ? val : '–';
}

function openDetailModal(type, id) {
    window._currentDetailType = type;
    window._currentDetailId   = id;
    document.getElementById('modalDetailPajak').classList.remove('hidden');
    document.getElementById('dpHistoryBody').innerHTML =
        '<tr><td colspan="20" class="text-center py-8 text-gray-400 text-xs">' +
        '<i class="fa fa-spinner fa-spin mr-1.5"></i>Memuat data…</td></tr>';
    document.getElementById('dpHistoryEmpty').classList.add('hidden');
    fetchDetailModal(type, id, null);
}

function closeDetailModal() {
    document.getElementById('modalDetailPajak').classList.add('hidden');
    if (window._dpChart) { window._dpChart.destroy(); window._dpChart = null; }
}

async function fetchDetailModal(type, id, tahun) {
    try {
        const url  = DETAIL_ENDPOINTS[type](id) + (tahun ? `?tahun=${tahun}` : '');
        const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();
        if (!json.success) return;
        renderDetailModal(type, json);
    } catch (e) {
        console.error('Detail modal fetch error:', e);
    }
}

function renderDetailModal(type, json) {
    const { record, histories, chart, available_years } = json;

    // ── Header
    const titles = { pajak:'Pajak Kendaraan', asuransi:'Asuransi Kendaraan', gps:'GPS Kendaraan', kir:'KIR Kendaraan' };
    document.getElementById('modalDetailPajakTitle').textContent   = 'Detail ' + (titles[type] || '');
    document.getElementById('modalDetailPajakSubtitle').textContent =
        (record.nopol || '') + (record.merk ? ' — ' + record.merk : '');

    // ── Info Cards (universal slots: card2-7)
    _setEl('dpNopol', record.nopol);
    _setEl('dpMerk',  record.merk);

    if (type === 'pajak') {
        document.getElementById('dpBiayaLabel').textContent  = 'Nominal';
        _setEl('dpNominal', fmtRp(record.nominal));
        document.getElementById('dpCard2Label').textContent  = 'Jenis Pajak';
        _setEl('dpCard2Val', record.jenis_pajak);
        document.getElementById('dpCard4Label').textContent  = 'Status';
        const stEl = document.getElementById('dpCard4Val');
        stEl.textContent  = record.status === 'sudah_bayar' ? 'Lunas' : 'Belum';
        stEl.className    = 'font-semibold text-sm ' + (record.status === 'sudah_bayar' ? 'text-emerald-600' : 'text-red-500');
        document.getElementById('dpCard5Label').textContent  = 'Tgl Bayar';
        _setEl('dpCard5Val', record.tanggal_bayar);
        document.getElementById('dpCard6Label').textContent  = 'Jatuh Tempo';
        _setEl('dpCard6Val', record.jatuh_tempo);
        document.getElementById('dpCard7Label').textContent  = 'Keterangan';
        _setEl('dpCard7Val', record.keterangan);
        document.getElementById('dpCard7').classList.remove('hidden');

    } else if (type === 'asuransi') {
        document.getElementById('dpBiayaLabel').textContent  = 'Biaya Premi';
        _setEl('dpNominal', fmtRp(record.biaya));
        document.getElementById('dpCard2Label').textContent  = 'Perusahaan';
        _setEl('dpCard2Val', record.perusahaan);
        document.getElementById('dpCard4Label').textContent  = 'Jenis Asuransi';
        document.getElementById('dpCard4Val').className      = 'font-semibold text-gray-700 text-sm';
        _setEl('dpCard4Val', record.jenis);
        document.getElementById('dpCard5Label').textContent  = 'Tgl Mulai';
        _setEl('dpCard5Val', record.tgl_mulai);
        document.getElementById('dpCard6Label').textContent  = 'Tgl Berakhir';
        _setEl('dpCard6Val', record.tgl_berakhir);
        document.getElementById('dpCard7Label').textContent  = 'Durasi & Status';
        _setEl('dpCard7Val', (record.durasi_bulan ? record.durasi_bulan + ' bulan' : '–') + ' | ' + (record.status || '–'));
        document.getElementById('dpCard7').classList.remove('hidden');

    } else if (type === 'gps') {
        document.getElementById('dpBiayaLabel').textContent  = 'Biaya Sewa';
        _setEl('dpNominal', fmtRp(record.biaya_sewa));
        document.getElementById('dpCard2Label').textContent  = 'Nama GPS';
        _setEl('dpCard2Val', record.nama_gps);
        document.getElementById('dpCard4Label').textContent  = 'Type';
        document.getElementById('dpCard4Val').className      = 'font-semibold text-gray-700 text-sm';
        _setEl('dpCard4Val', record.type);
        document.getElementById('dpCard5Label').textContent  = 'Tgl Pasang';
        _setEl('dpCard5Val', record.tanggal_pasang);
        document.getElementById('dpCard6Label').textContent  = 'Tgl Habis';
        _setEl('dpCard6Val', record.tanggal_habis);
        document.getElementById('dpCard7Label').textContent  = 'Durasi & Status';
        _setEl('dpCard7Val',
            (record.durasi_bulan ? record.durasi_bulan + ' bulan' : '–') +
            ' | GPS: ' + (record.status_gps || '–') +
            ' | Sewa: ' + (record.status_sewa || '–'));
        document.getElementById('dpCard7').classList.remove('hidden');

    } else if (type === 'kir') {
        document.getElementById('dpBiayaLabel').textContent  = 'Biaya KIR';
        _setEl('dpNominal', fmtRp(record.biaya));
        document.getElementById('dpCard2Label').textContent  = 'No Uji';
        _setEl('dpCard2Val', record.no_uji);
        document.getElementById('dpCard4Label').textContent  = 'Status Uji';
        document.getElementById('dpCard4Val').className      = 'font-semibold text-gray-700 text-sm';
        _setEl('dpCard4Val', record.status_uji);
        document.getElementById('dpCard5Label').textContent  = 'Tgl Bayar';
        _setEl('dpCard5Val', record.tanggal_bayar);
        document.getElementById('dpCard6Label').textContent  = 'Masa Berlaku';
        _setEl('dpCard6Val', record.masa_berlaku);
        document.getElementById('dpCard7Label').textContent  = 'Lokasi & Penguji';
        _setEl('dpCard7Val', (record.lokasi_uji || '–') + (record.penguji ? ' · ' + record.penguji : ''));
        document.getElementById('dpCard7').classList.remove('hidden');
    }

    // ── Chart title + color
    const color = DETAIL_CHART_COLORS[type] || DETAIL_CHART_COLORS.pajak;
    document.getElementById('dpChartTitle').textContent = DETAIL_CHART_LABELS[type] || 'Biaya per Bulan';

    // ── Tahun filter
    const sel          = document.getElementById('dpTahunFilter');
    const currentTahun = chart.tahun;
    sel.innerHTML      = '';
    const years        = (available_years && available_years.length) ? [...available_years] : [currentTahun];
    if (!years.map(Number).includes(currentTahun)) years.unshift(currentTahun);
    years.forEach(y => {
        const opt      = document.createElement('option');
        opt.value      = y;
        opt.textContent = y;
        if (parseInt(y) === currentTahun) opt.selected = true;
        sel.appendChild(opt);
    });

    // ── Bar chart
    if (window._dpChart) { window._dpChart.destroy(); window._dpChart = null; }
    const ctx = document.getElementById('dpBarChart').getContext('2d');
    window._dpChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chart.labels,
            datasets: [{
                label: DETAIL_CHART_LABELS[type] || 'Biaya',
                data:  chart.data,
                backgroundColor: color.bg,
                borderColor:     color.border,
                borderWidth: 1.5,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + fmtRp(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID'), font: { size: 10 } },
                    grid: { color: '#f1f5f9' }
                },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // ── History table header
    const cols = DETAIL_COLUMNS[type] || [];
    document.getElementById('dpHistoryHead').innerHTML = cols.map(c =>
        `<th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">${c}</th>`
    ).join('');

    // ── History rows
    const tbody = document.getElementById('dpHistoryBody');
    const empty = document.getElementById('dpHistoryEmpty');

    if (!histories || !histories.length) {
        tbody.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }
    empty.classList.add('hidden');

    const buktiBtn = url => url
        ? `<a href="${url}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 text-[10px] whitespace-nowrap"><i class="fa fa-paperclip text-[9px]"></i> Lihat</a>`
        : '<span class="text-gray-300">–</span>';

    tbody.innerHTML = histories.map((h, i) => {
        const no = `<td class="px-3 py-2.5 text-gray-400 text-center w-8">${i + 1}</td>`;
        let cells = '';

        if (type === 'pajak') {
            cells = `${no}
                <td class="px-3 py-2.5 text-gray-700">${h.jenis_pajak || '–'}</td>
                <td class="px-3 py-2.5 text-emerald-700 font-semibold whitespace-nowrap">${fmtRp(h.nominal)}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.tanggal_bayar || '–'}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.jatuh_tempo || '–'}</td>
                <td class="px-3 py-2.5">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold ${h.status === 'sudah_bayar' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}">
                        ${h.status === 'sudah_bayar' ? 'Lunas' : 'Belum'}
                    </span>
                </td>
                <td class="px-3 py-2.5 max-w-[120px] truncate text-gray-500">${h.keterangan || '–'}</td>
                <td class="px-3 py-2.5 text-gray-400 whitespace-nowrap">${h.diperpanjang_pada || '–'}</td>
                <td class="px-3 py-2.5">${buktiBtn(h.bukti)}</td>`;
        } else if (type === 'asuransi') {
            cells = `${no}
                <td class="px-3 py-2.5 text-gray-700">${h.perusahaan || '–'}</td>
                <td class="px-3 py-2.5 text-gray-600">${h.jenis || '–'}</td>
                <td class="px-3 py-2.5 text-emerald-700 font-semibold whitespace-nowrap">${fmtRp(h.biaya)}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.tgl_mulai || '–'}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.tgl_berakhir || '–'}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.durasi_bulan ? h.durasi_bulan + ' bln' : '–'}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.tanggal_bayar || '–'}</td>
                <td class="px-3 py-2.5 text-gray-400 whitespace-nowrap">${h.diperpanjang_pada || '–'}</td>
                <td class="px-3 py-2.5">${buktiBtn(h.bukti_bayar)}</td>`;
        } else if (type === 'gps') {
            cells = `${no}
                <td class="px-3 py-2.5 text-gray-700">${h.nama_gps || '–'}</td>
                <td class="px-3 py-2.5 text-gray-600">${h.type || '–'}</td>
                <td class="px-3 py-2.5 text-emerald-700 font-semibold whitespace-nowrap">${fmtRp(h.biaya_sewa)}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.durasi_bulan ? h.durasi_bulan + ' bln' : '–'}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.tanggal_pasang || '–'}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.tanggal_habis || '–'}</td>
                <td class="px-3 py-2.5">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold ${h.status_sewa === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}">
                        ${h.status_sewa || '–'}
                    </span>
                </td>
                <td class="px-3 py-2.5 text-gray-400 whitespace-nowrap">${h.diperpanjang_pada || '–'}</td>
                <td class="px-3 py-2.5">${buktiBtn(h.bukti_bayar)}</td>`;
        } else if (type === 'kir') {
            cells = `${no}
                <td class="px-3 py-2.5 font-mono text-gray-700">${h.no_uji || '–'}</td>
                <td class="px-3 py-2.5 text-emerald-700 font-semibold whitespace-nowrap">${fmtRp(h.biaya)}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.masa_berlaku || '–'}</td>
                <td class="px-3 py-2.5 whitespace-nowrap">${h.tanggal_bayar || '–'}</td>
                <td class="px-3 py-2.5 text-gray-400 whitespace-nowrap">${h.diperpanjang_pada || '–'}</td>
                <td class="px-3 py-2.5">${buktiBtn(h.image)}</td>`;
        }
        return `<tr class="hover:bg-gray-50 transition-colors text-xs">${cells}</tr>`;
    }).join('');
}
// ────────────────────────────────────────────────────────────────────────────
</script>
