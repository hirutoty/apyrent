<!DOCTYPE html>
<html>

<head>
    <title>Hutang Vendor</title>
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
            font-size: 28px;
            color: #1e56b0;
            letter-spacing: 3px;
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

    {{-- HEADER --}}

    <div class="header">

        
        <table class="header-table">
            <tr>

                <td width="15%">
                    @if($logoSrc)
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
                        <h1>HUTANG VENDOR</h1>
                        <p>Laporan Data Hutang Vendor</p>
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

        @if (!empty($search))
            <p>
                <strong>Filter :</strong>
                {{ $search }}
            </p>
        @endif

        <p>
            <strong>Total Data :</strong>
            {{ $data->count() }}
        </p>
        

    </div>

    {{-- TABEL --}}

    <div class="table-wrap">

        
        <table class="main-table">

            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="20%">Vendor</th>
                    <th width="14%">Kategori</th>
                    <th width="15%">Nominal</th>
                    <th width="15%">Dibayar</th>
                    <th width="15%">Sisa</th>
                    <th width="10%">Jatuh Tempo</th>
                    <th width="7%">Status</th>
                </tr>
            </thead>

            <tbody>

                @php
                    $totalNominal = $data->sum('nominal');
                    $totalDibayar = $data->sum('dibayar');
                    $totalSisa = $data->sum('sisa');
                @endphp

                @foreach ($data as $i => $item)
                    <tr>

                        <td class="text-center">
                            {{ $i + 1 }}
                        </td>

                        <td>
                            {{ $item->nama_vendor }}
                        </td>

                        <td>
                            {{ $item->kategori }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($item->dibayar, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($item->sisa, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->jatuh_tempo)->format('d-m-Y') }}
                        </td>

                        <td class="text-center">
                            {{ strtoupper($item->status) }}
                        </td>

                    </tr>
                @endforeach

                <tr class="row-subtotal">
                    <td colspan="3" class="text-right">
                        SUB TOTAL
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalNominal, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalSisa, 0, ',', '.') }}
                    </td>

                    <td colspan="2"></td>
                </tr>

                <tr class="row-total">
                    <td colspan="3" class="text-right">
                        TOTAL HUTANG VENDOR
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalNominal, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalSisa, 0, ',', '.') }}
                    </td>

                    <td colspan="2"></td>
                </tr>

            </tbody>

        </table>
        

    </div>

    

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
