<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Summary Invoice</title>

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
                        <h1>Invoice</h1>
                        <p>Summary Invoice Perusahaan</p>
                    </div>
                </td>

            </tr>
        </table>
    </div>

    @php
        $totalAmount   = $summaries->sum('total_amount');
        $totalPaid     = $summaries->sum('paid_amount');
        $totalRemaining = $summaries->sum('remaining_amount');
    @endphp

    <div class="info-box">
        <p>
            <strong>Tanggal Cetak :</strong>
            {{ now()->format('d M Y H:i') }}
        </p>

        <p>
            <strong>Total Data Invoice :</strong>
            {{ $summaries->count() }}
        </p>

        <p>
            <strong>Total Nominal :</strong>
            Rp {{ number_format($totalAmount, 0, ',', '.') }}
        </p>

        <p>
            <strong>Total Dibayar :</strong>
            Rp {{ number_format($totalPaid, 0, ',', '.') }}
        </p>

        <p>
            <strong>Total Sisa :</strong>
            Rp {{ number_format($totalRemaining, 0, ',', '.') }}
        </p>
    </div>

    <div class="table-wrap">

        <table class="main-table">

            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="12%">Invoice</th>
                    <th width="16%">Customer</th>
                    <th width="12%">Penawaran</th>
                    <th width="12%">Kontrak</th>
                    <th width="8%">Tipe</th>
                    <th width="12%">Total</th>
                    <th width="12%">Dibayar</th>
                    <th width="12%">Sisa</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($summaries as $i => $row)
                    <tr>

                        <td class="text-center">
                            {{ $i + 1 }}
                        </td>

                        <td>
                            {{ optional($row->invoice)->invoice_no ?? '-' }}
                        </td>

                        <td>
                            {{ optional($row->invoice)->customer_name ?? '-' }}
                        </td>

                        <td>
                            {{ optional($row->penawaran)->no_penawaran ?? '-' }}
                        </td>

                        <td>
                            {{ optional($row->kontrak)->no_kontrak ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ $row->type ?? '-' }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($row->paid_amount, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($row->remaining_amount, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            {{ $row->payment_status ?? '-' }}
                        </td>

                    </tr>
                @empty

                    <tr>
                        <td colspan="10" class="text-center">
                            Tidak ada data invoice.
                        </td>
                    </tr>

                @endforelse

                <tr class="row-subtotal">

                    <td colspan="6" class="text-right">
                        TOTAL
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalAmount, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalRemaining, 0, ',', '.') }}
                    </td>

                    <td></td>

                </tr>

            </tbody>

        </table>

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