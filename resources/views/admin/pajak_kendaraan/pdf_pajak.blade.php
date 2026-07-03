<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pajak Kendaraan</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
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

        .main-table th {
            color: #fff;
            padding: 9px;
            border: 1px solid #d0d7e3;
            text-align: center;
            font-size: 11px;
        }

        .main-table td {
            border: 1px solid #d0d7e3;
            padding: 7px;
            font-size: 10px;
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
            color: #fff;
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

    <div class="header">
        <table class="header-table">
            <tr>

                <td width="15%">
                    @if ($setting?->logo)
                       <img src="{{ public_path($setting->logo) }}" class="logo">
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
                        <h1>Pajak</h1>
                        <p>Laporan Pajak Kendaraan Perusahaan</p>
                    </div>
                </td>

            </tr>
        </table>
    </div>
    
<div class="info-box">

    <p>
        <strong>Tanggal Cetak :</strong>
        {{ now()->format('d M Y H:i') }}
    </p>

    @if ($search)
        <p>
            <strong>Filter :</strong>
            {{ $search }}
        </p>
    @endif

    <p>
        <strong>Total Data Pajak :</strong>
        {{ $data->count() }}
    </p>

    <p>
        <strong>Total Nominal Pajak :</strong>
        Rp {{ number_format($data->sum('nominal'), 0, ',', '.') }}
    </p>

</div>

<div class="table-wrap">

    <table class="main-table">

        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">Nopol</th>
                <th width="12%">Merk</th>
                <th width="12%">Jenis Pajak</th>
                <th width="12%">Nominal</th>
                <th width="12%">Jatuh Tempo</th>
                <th width="12%">Tgl Bayar</th>
                <th width="10%">Status</th>
                <th width="16%">Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @forelse($data as $item)
                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->kendaraan->nopol ?? '-' }}
                    </td>

                    <td>
                        {{ $item->kendaraan->merk ?? '-' }}
                    </td>

                    <td>
                        {{ $item->jenis_pajak }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->jatuh_tempo)->format('d-m-Y') }}
                    </td>

                    <td class="text-center">
                        {{ $item->tanggal_bayar
                            ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d-m-Y')
                            : '-' }}
                    </td>

                    <td>
                                    @if ($item->bukti)
                                        @php
                                            $filename = basename($item->bukti);
                                        @endphp

                                        <a href="{{ asset($item->bukti) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                    <td>
                        {{ $item->keterangan ?? '-' }}
                    </td>

                </tr>
            @empty

                <tr>
                    <td colspan="9" class="text-center">
                        Tidak ada data pajak kendaraan.
                    </td>
                </tr>

            @endforelse

            <tr class="row-subtotal">

                <td colspan="4" class="text-right">
                    TOTAL PAJAK
                </td>

                <td class="text-right">
                    Rp {{ number_format($data->sum('nominal'), 0, ',', '.') }}
                </td>

                <td colspan="4"></td>

            </tr>

        </tbody>

    </table>

</div>

<div class="note-box">

    {{-- <div class="note-title">
        KETERANGAN DOKUMEN
    </div>

    <div class="note-content">
        Laporan ini berisi data pajak kendaraan yang tercatat pada sistem
        {{ $setting?->nama_perusahaan }}. Informasi meliputi kendaraan,
        jenis pajak, nominal kewajiban, jatuh tempo pembayaran,
        tanggal pembayaran, status pajak, serta keterangan pendukung
        untuk keperluan monitoring administrasi kendaraan.
    </div> --}}

</div>

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
