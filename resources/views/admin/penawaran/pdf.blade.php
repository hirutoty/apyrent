<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Penawaran Kendaraan</title>
    <style>
    @page {
        margin: 20px 25px;
        size: A4 landscape;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Times New Roman", serif;
        margin: 0;
        padding: 0;
        color: #000;
        background: #fff;
        font-size: 15px;
        line-height: 1.5;
    }

    /* ===========================
        JUDUL
    ============================ */

    .title {
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    /* ===========================
        TABEL
    ============================ */

    .main-table {
        width: 100%;
        border-collapse: collapse;
    }

    .main-table th,
    .main-table td {
        border: 1px solid #000;
    }

    /* ===========================
        HEADER
    ============================ */

    .main-table thead tr.row-head-1 th {
        background: #AFC4DF;
        color: #000;
        padding: 5px 4px;
        text-align: center;
        font-size: 9px;
        font-weight: bold;
        vertical-align: middle;
    }

    .main-table thead tr.row-head-2 th {
        background: #AFC4DF;
        color: #000;
        padding: 4px;
        text-align: center;
        font-size: 9px;
        font-weight: bold;
    }

    /* ===========================
        ISI TABEL
    ============================ */

    .main-table td {
        padding: 4px 5px;
        vertical-align: middle;
        font-size: 9px;
    }

    /* No */
    .main-table tbody td:nth-child(1) {
        background: #FFFFFF;
    }

    /* Periode */
    .main-table tbody td:nth-child(2) {
        background: #F8E39A;
    }

    /* Tujuan Penawaran */
    .main-table tbody td:nth-child(3) {
        background: #EFE2D2;
    }

    /* Rincian Unit */
    .main-table tbody td:nth-child(4),
    .main-table tbody td:nth-child(5),
    .main-table tbody td:nth-child(6),
    .main-table tbody td:nth-child(7),
    .main-table tbody td:nth-child(8),
    .main-table tbody td:nth-child(9) {
        background: #D6DCEC;
    }

    /* Harga */
    .main-table tbody td:nth-child(10) {
        background: #F6E2D6;
    }

    /* PIC */
    .main-table tbody td:nth-child(11) {
        background: #D6DCEC;
    }

    /* Status */
    .main-table tbody td:nth-child(12) {
        background: #D6DCEC;
    }

    /* ===========================
        STATUS
    ============================ */

    .status-cell {
        text-align: center;
        font-style: italic;
        font-weight: bold;
        font-size: 8.5px;
        color: #000;
    }

    .status-delivered,
    .status-progress,
    .status-pending,
    .status-rejected,
    .status-expired {
        color: #000;
    }

    /* ===========================
        UTILITAS
    ============================ */

    .row-data td {
        background: transparent;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .font-bold {
        font-weight: bold;
    }
</style>
</head>
<body>

@php
    use Carbon\Carbon;

    // Kelompokkan per bulan dari tanggal_penawaran
    $grouped = $penawarans->groupBy(function ($p) {
        return Carbon::parse($p->tanggal_penawaran)->format('Y-m');
    })->sortKeys();

    $no = 1;
@endphp

{{-- JUDUL --}}
<div class="title">
    DAFTAR KENDARAAN BARU {{ $setting?->nama_perusahaan ? strtoupper($setting->nama_perusahaan) : 'APY' }}
    {{ now()->format('Y') }}
</div>

<table class="main-table">
    <thead>
        {{-- Baris header 1 --}}
        <tr class="row-head-1">
            <th rowspan="2" width="3%">No.</th>
            <th rowspan="2" width="7%">PERIODE</th>
            <th rowspan="2" width="16%">TUJUAN PENAWARAN</th>
            {{-- Colspan untuk rincian unit --}}
            <th colspan="6" width="38%">RINCIAN UNIT</th>
            <th rowspan="2" width="12%">HARGA SEWA/<br>BULAN/UNIT</th>
            <th rowspan="2" width="11%">PIC</th>
            <th rowspan="2" width="13%">STATUS</th>
        </tr>
        {{-- Baris header 2: sub-header rincian unit --}}
        <tr class="row-head-2">
            <th width="16%">Tipe</th>
            <th width="5%">Qty</th>
            <th width="7%">Warna</th>
            <th width="7%">Nopol</th>
            <th width="6%">Periode</th>
            <th width="6%">Tahun</th>
        </tr>
    </thead>
    <tbody>

        @forelse($grouped as $yearMonth => $items)

            @foreach($items as $penawaran)
                @php
                    $unitItems = $penawaran->items ?? collect();
                    $totalItems = $unitItems->count();
                    $labelBulan = Carbon::parse($penawaran->tanggal_penawaran)->locale('id')->isoFormat('MMMM');
                    $statusClass = match($penawaran->status) {
                        'approved'  => 'status-delivered',
                        'active'    => 'status-progress',
                        'completed' => 'status-delivered',
                        'rejected'  => 'status-rejected',
                        'expired'   => 'status-expired',
                        default     => 'status-pending',
                    };
                    $statusLabel = match($penawaran->status) {
                        'approved'  => 'Approved',
                        'active'    => 'On Progress',
                        'completed' => 'Completed',
                        'rejected'  => 'Rejected',
                        'expired'   => 'Expired',
                        'pending'   => 'On Progress',
                        default     => ucfirst($penawaran->status ?? '-'),
                    };
                @endphp

                @if($totalItems === 0)
                    <tr class="row-data">
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center font-bold">{{ strtoupper($labelBulan) }}</td>
                        <td>
                            <span class="font-bold">{{ strtoupper($penawaran->kepada ?? '') }}</span>
                            @if($penawaran->up)
                                <br><span style="font-size:8px;">{{ $penawaran->up }}</span>
                            @endif
                        </td>
                        <td colspan="6"></td>
                        <td class="text-right"></td>
                        <td style="font-size:8px; text-align:center;">{{ $penawaran->contact_person ?? '' }}</td>
                        <td class="status-cell {{ $statusClass }}">{{ $statusLabel }}</td>
                    </tr>
                @else
                    @foreach($unitItems as $idx => $item)
                        <tr class="row-data">
                            @if($idx === 0)
                                <td class="text-center" rowspan="{{ $totalItems }}">{{ $no++ }}</td>
                                <td class="text-center font-bold" rowspan="{{ $totalItems }}">{{ strtoupper($labelBulan) }}</td>
                                <td rowspan="{{ $totalItems }}">
                                    <span class="font-bold">{{ strtoupper($penawaran->kepada ?? '') }}</span>
                                    @if($penawaran->up)
                                        <br><span style="font-size:8px;">{{ $penawaran->up }}</span>
                                    @endif
                                </td>
                            @endif

                            <td>{{ strtoupper(optional($item->kendaraan)->merk ?? '-') }}</td>
                            <td class="text-center">{{ $item->qty ?? 1 }} Unit</td>
                            <td class="text-center">{{ optional($item->kendaraan)->warna ?? '-' }}</td>
                            <td class="text-center">{{ optional($item->kendaraan)->nopol ?? '-' }}</td>
                            <td class="text-center">
                                {{ $item->durasi ?? $penawaran->periode ?? '-' }}
                                {{ ucfirst($item->satuan_durasi ?? 'Bulan') }}
                            </td>
                            <td class="text-center">
                                {{ $item->tahun_unit ?? optional($item->kendaraan)->tahun_pembuatan ?? '-' }}
                            </td>

                            @if($idx === 0)
                                <td class="text-right" rowspan="{{ $totalItems }}">
                                    Rp{{ number_format($item->price ?? 0, 0, ',', '.') }}
                                </td>
                                <td rowspan="{{ $totalItems }}" style="font-size:8px; text-align:center;">
                                    {{ $penawaran->contact_person ?? '' }}
                                </td>
                                <td class="status-cell {{ $statusClass }}" rowspan="{{ $totalItems }}">
                                    {{ $statusLabel }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            @endforeach

        @empty
            <tr>
                <td colspan="12" class="text-center" style="padding:20px;">
                    Tidak ada data penawaran.
                </td>
            </tr>
        @endforelse

    </tbody>
</table>

</body>
</html>
