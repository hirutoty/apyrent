<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Merk Kendaraan</title>

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

        .note-box {
            margin-top: 15px;
            padding: 12px 14px;
            border: 1px solid #d0d7e3;
            border-radius: 4px;
            background: #fefce8;
        }

        .note-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 6px;
            color: #222;
        }

        .note-box p {
            font-size: 10.5px;
            line-height: 1.7;
            color: #333;
            margin: 0;
        }

        .note-box strong {
            color: #1e56b0;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}

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
                        <h1>MERK KENDARAAN</h1>
                        <p>Laporan Data Merk Kendaraan</p>
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
                    <th width="5%">No</th>
                    <th width="30%">Nama Merk</th>
                    <th width="25%">Jenis Kendaraan</th>
                    <th width="13%">Total Stok</th>
                    <th width="13%">Tersedia</th>
                    <th width="14%">Status</th>
                </tr>
            </thead>

            <tbody>

                @php
                    $totalStok = $data->sum('total_unit');
                    $totalTersedia = $data->sum('tersedia_unit');
                @endphp

                @foreach ($data as $i => $d)
                    <tr>

                        <td class="text-center">
                            {{ $i + 1 }}
                        </td>

                        <td>
                            {{ $d->merk }}
                        </td>

                        <td>
                            {{ $d->jenis->nama_jenis ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ $d->total_unit }}
                        </td>

                        <td class="text-center">
                            {{ $d->tersedia_unit }}
                        </td>

                        <td class="text-center">
                            @if ($d->tersedia_unit == 0)
                                HABIS
                            @elseif($d->tersedia_unit < $d->total_unit)
                                SEBAGIAN TERSEDIA
                            @else
                                TERSEDIA
                            @endif
                        </td>

                    </tr>
                @endforeach

                <tr class="row-subtotal">

                    <td colspan="3" class="text-right">
                        SUB TOTAL
                    </td>

                    <td class="text-center">
                        {{ $totalStok }}
                    </td>

                    <td class="text-center">
                        {{ $totalTersedia }}
                    </td>

                    <td></td>

                </tr>

                <tr class="row-total">

                    <td colspan="3" class="text-right">
                        TOTAL DATA MERK
                    </td>

                    <td class="text-center">
                        {{ $totalStok }}
                    </td>

                    <td class="text-center">
                        {{ $totalTersedia }}
                    </td>

                    <td>
                        {{ $data->count() }} Merk
                    </td>

                </tr>

            </tbody>

        </table>
        

    </div>

    {{-- KETERANGAN --}}

    <div class="note-box">

        
        <div class="note-title">
            KETERANGAN DOKUMEN
        </div>

        <p>
            Laporan ini merupakan data merk kendaraan yang terdaftar pada sistem
            {{ $setting?->nama_perusahaan }}.
            Data mencakup jumlah unit kendaraan, stok yang tersedia,
            jenis kendaraan, serta status ketersediaan setiap merk kendaraan.
        </p>
        

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
