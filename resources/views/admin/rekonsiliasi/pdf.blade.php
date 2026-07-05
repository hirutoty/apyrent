<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekonsiliasi Bank</title>
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
                        <h1>Rekonsiliasi</h1>
                        <p>Laporan Rekonsiliasi Bank Perusahaan</p>
                    </div>
                </td>

            </tr>
        </table>
    </div>

    {{-- INFO BOX --}}
    <div class="info-box">

        <p>
            <strong>Tanggal Cetak :</strong>
            {{ now()->format('d M Y H:i') }}
        </p>

        <p>
            <strong>Total Data :</strong>
            {{ $data->count() }}
        </p>

        <p>
            <strong>Total Nominal :</strong>
            Rp {{ number_format($data->sum('amount'), 0, ',', '.') }}
        </p>

    </div>

    {{-- TABEL --}}
    <div class="table-wrap">

        <table class="main-table">

            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="10%">Tanggal</th>
                    <th width="20%">Deskripsi</th>
                    <th width="13%">Reference</th>
                    <th width="13%">Nominal</th>
                    <th width="8%">Currency</th>
                    <th width="12%">Invoice No</th>
                    <th width="10%">VA</th>
                    <th width="10%">Status</th>
                    <th width="8%">Bukti</th>
                </tr>
            </thead>

            <tbody>

                @forelse($data as $i => $item)
                    <tr>

                        <td class="text-center">
                            {{ $i + 1 }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                        </td>

                        <td>
                            {{ $item->deskripsi }}
                        </td>

                        <td>
                            {{ $item->reference_no }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            {{ $item->currency }}
                        </td>

                        <td class="text-center">
                            {{ $item->invoice_id ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ $item->va ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ ucfirst($item->status_rekonsiliasi) }}
                        </td>

                        <td class="text-center">
                            {{ $item->bukti_pembayaran ? 'Ada' : '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="10" class="text-center">
                            Tidak ada data rekonsiliasi bank.
                        </td>
                    </tr>
                @endforelse

                <tr class="row-subtotal">

                    <td colspan="4" class="text-right">
                        TOTAL REKONSILIASI
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($data->sum('amount'), 0, ',', '.') }}
                    </td>

                    <td colspan="5"></td>

                </tr>

            </tbody>

        </table>

    </div>

    {{-- KETERANGAN
<div class="note-box">

    <div class="note-title">
        KETERANGAN DOKUMEN
    </div>

    <div class="note-content">
        Laporan ini merupakan data rekonsiliasi bank yang tercatat pada
        sistem {{ $setting?->nama_perusahaan }}. Data meliputi tanggal
        transaksi, deskripsi transaksi, nomor referensi, nominal transaksi,
        mata uang yang digunakan, serta status rekonsiliasi untuk keperluan
        pencocokan dan pengawasan transaksi keuangan perusahaan.
    </div>

</div> --}}


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
