<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Service History</title>

    <style>
        @page {
            size: A3 landscape;
            margin: 15px;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        .header {
            padding: 18px 28px 14px;
            border-bottom: 2px solid #e0e0e0;
        }

        .header-table {
            width: 100%;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e56b0;
            margin-bottom: 4px;
        }

        .company-info {
            font-size: 10px;
            line-height: 1.6;
            color: #555;
        }

        .report-title {
            text-align: right;
        }

        .report-title h1 {
            font-size: 26px;
            color: #1e56b0;
            letter-spacing: 2px;
        }

        .report-title p {
            font-size: 11px;
            color: #666;
        }

        .info-box {
            margin: 15px 28px;
            padding: 12px;
            border: 1px solid #d0d7e3;
            background: #f8fafc;
            border-radius: 4px;
        }

        .info-box p {
            margin: 4px 0;
        }

        .table-wrap {
            padding: 0 28px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-table thead tr {
            background: #1e56b0;
        }

        .main-table th,
        .main-table td {
            padding: 4px;
            font-size: 8px;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .row-subtotal td {
            background: #eff6ff;
            font-weight: bold;
        }

        .row-total td {
            background: #1e56b0;
            color: white;
            font-weight: bold;
        }

        .note-box {
            margin: 18px 28px;
            border: 1px solid #d0d7e3;
            border-radius: 5px;
            overflow: hidden;
            background: #fff;
        }

        .note-title {
            background: #1e56b0;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            padding: 8px 12px;
        }

        .note-content {
            padding: 12px;
            font-size: 10px;
            line-height: 1.8;
            color: #444;
            text-align: justify;
        }

        .footer {
            margin-top: 25px;
            padding: 14px 28px;
            border-top: 1px solid #dbeafe;
            text-align: center;
            color: #1e56b0;
            font-size: 10px;
            line-height: 1.7;
        }
    </style>
</head>

<body>

    @php
        $totalService = $data->count();
        $totalBiaya = $data->sum('total_biaya');
    @endphp

    {{-- HEADER --}}
    <div class="header">

        <table class="header-table">
            <tr>

                <td width="15%">
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" class="logo">
                    @endif
                </td>

                <td width="55%">
                    <div class="company-name">
                        {{ $setting?->nama_perusahaan }}
                    </div>

                    <div class="company-info">
                        {{ $setting?->alamat }}
                        <br>
                        Telp : {{ $setting?->telepon }}
                        <br>
                        Email : {{ $setting?->email }}

                        @if ($setting?->website)
                            <br>
                            Website : {{ $setting->website }}
                        @endif
                    </div>
                </td>

                <td width="30%">
                    <div class="report-title">
                        <h1>SERVICE</h1>
                        <p>Laporan Riwayat Service Kendaraan</p>
                    </div>
                </td>

            </tr>
        </table>

    </div>

    {{-- INFO --}}
    <div class="info-box">

        <p>
            <strong>Tanggal Cetak :</strong>
            {{ now()->format('d M Y H:i') }}
        </p>

        @if (!empty($bulan))
            <p>
                <strong>Filter Bulan :</strong>
                {{ $bulan }}
            </p>
        @endif

        @if (!empty($search))
            <p>
                <strong>Pencarian :</strong>
                {{ $search }}
            </p>
        @endif

        <p>
            <strong>Total Service :</strong>
            {{ $totalService }}
        </p>

        <p>
            <strong>Total Biaya Service :</strong>
            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
        </p>

    </div>

    {{-- TABEL --}}
    <div class="table-wrap">

        <table class="main-table">

            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="10%">Kendaraan</th>
                    <th width="8%">Nopol</th>
                    <th width="12%">Keluhan</th>
                    <th width="5%">KM</th>
                    <th width="8%">Max per Tahun</th>
                    <th width="8%">Max per Bulan</th>
                    <th width="8%">Biaya</th>
                    <th width="8%">Sisa</th>
                    <th width="7%">Status</th>
                    <th width="8%">Pengeluaran</th>
                    <th width="8%">Tanggal</th>
                    <th width="8%">Bukti</th>
                    <th width="10%">Lampiran</th>
                </tr>
            </thead>

            <tbody>

                @forelse($data as $i => $d)
                    @php
                        $maksTahunan = $d->kendaraan->limit_biaya_tahunan_service ?? 0;

                        $limitBulan = $d->kendaraan->limit_biaya_bulanan_service ?? 0;

                        $totalBulanIni = \App\Models\ServiceHistory::where('kendaraan_id', $d->kendaraan_id)
                            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [
                                date('Y-m', strtotime($d->tanggal_service)),
                            ])
                            ->sum('total_biaya');

                        $statusPengeluaran = $limitBulan > 0 && $totalBulanIni > $limitBulan ? 'Overservice' : 'Stabil';
                    @endphp

                    <tr>

                        <td class="text-center">
                            {{ $i + 1 }}
                        </td>

                        <td>
                            {{ $d->kendaraan->merk ?? '-' }}
                        </td>

                        <td>
                            {{ $d->kendaraan->nopol ?? '-' }}
                        </td>

                        <td>
                            {{ $d->keluhan }}
                        </td>

                        <td class="text-right">
                            {{ number_format($d->kilometer, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($maksTahunan, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($limitBulan, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($d->total_biaya, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($d->sisa_limit, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            {{ ucfirst($d->status) }}
                        </td>

                        <td class="text-center">
                            {{ $statusPengeluaran }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($d->tanggal_service)->format('d-m-Y') }}
                        </td>

                        <td>
                                    @if ($d->bukti_pembayaran)
                                        @php
                                            $filename = basename($d->bukti_pembayaran);
                                        @endphp

                                        <a href="{{ asset($d->bukti_pembayaran) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                        <td>
                            @if ($d->attachments && $d->attachments->isNotEmpty())
                                {{ $d->attachments->pluck('file_name')->join(', ') }}
                            @else
                                -
                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="14" class="text-center">
                            Tidak ada data service kendaraan.
                        </td>
                    </tr>
                @endforelse

        </table>

    </div>

    {{-- KETERANGAN
    <div class="note-box">

        <div class="note-title">
            KETERANGAN DOKUMEN
        </div>

        <div class="note-content">
            Laporan ini merupakan riwayat perawatan dan perbaikan kendaraan
            yang tercatat pada sistem {{ $setting?->nama_perusahaan }}.
            Data meliputi kendaraan yang diservis, nomor polisi kendaraan,
            keluhan yang dilaporkan, kilometer kendaraan saat servis,
            biaya yang dikeluarkan, status pengerjaan, serta tanggal
            pelaksanaan servis sebagai bahan monitoring kondisi armada.
        </div>

    </div> --}}

    {{-- FOOTER --}}
    <div class="footer">

        <strong>{{ $setting?->nama_perusahaan }}</strong>
        <br>

        {{ $setting?->alamat }}
        <br>

        Telp : {{ $setting?->telepon }}
        |
        Email : {{ $setting?->email }}

        @if ($setting?->website)
            <br>
            {{ $setting->website }}
        @endif

    </div>

</body>

</html>
