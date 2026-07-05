<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>History Perpanjangan GPS Kendaraan</title>

    <style>
        .note-box {
            margin: 20px 28px;
            padding: 14px 16px;
            background: #a3d6b142;
            border: 1px solid #dbeafe;
            border-radius: 6px;
        }

        .note-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e56b0;
            margin-bottom: 8px;
        }

        .note-box p {
            font-size: 10px;
            line-height: 1.7;
            color: #555;
            text-align: justify;
        }

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
            font-size: 20px;
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

        .info-box-table {
            width: 100%;
        }

        .info-box-table td {
            font-size: 11px;
            padding: 3px 0;
            vertical-align: top;
        }

        .info-box-table td:nth-child(1) {
            width: 18%;
            font-weight: bold;
        }

        .info-box-table td:nth-child(2) {
            width: 32%;
        }

        .info-box-table td:nth-child(3) {
            width: 18%;
            font-weight: bold;
        }

        .info-box-table td:nth-child(4) {
            width: 32%;
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

        .nopol {
            font-family: DejaVu Sans Mono, monospace;
            background-color: #f3f4f6;
            padding: 1px 4px;
            border-radius: 3px;
        }

        .badge-type {
            background-color: #eff6ff;
            color: #1d4ed8;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 9px;
        }

        .bukti-ada {
            color: #15803d;
            font-weight: bold;
        }

        .bukti-tidak {
            color: #9ca3af;
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
                        <h1>HISTORY GPS</h1>
                        <p>Laporan History Perpanjangan GPS Kendaraan</p>
                    </div>
                </td>

            </tr>
        </table>
    </div>

    {{-- INFO --}}

    <div class="info-box">
        <table class="info-box-table">
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->format('d M Y H:i') }}</td>
                <td>Total Data</td>
                <td>: {{ $data->count() }}</td>
            </tr>
            <tr>
                <td>Periode Bulan</td>
                <td>:
                    @if(request('bulan'))
                        {{ [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'][(int) request('bulan')] }}
                    @else
                        Semua Bulan
                    @endif
                </td>
                <td>Total Biaya</td>
                <td>: Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Periode Tahun</td>
                <td>: {{ request('tahun') ?: 'Semua Tahun' }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    {{-- TABEL --}}

    <div class="table-wrap">
        <table class="main-table">

            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="14%">Kendaraan</th>
                    <th width="10%">No Polisi</th>
                    <th width="12%">GPS</th>
                    <th width="7%">Type</th>
                    <th width="11%">Biaya Sewa</th>
                    <th width="11%">Tgl Habis Lama</th>
                    <th width="13%">Diperpanjang</th>
                    <th width="9%">Durasi</th>
                    <th width="9%">Bukti</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($data as $i => $item)
                    <tr>

                        <td class="text-center">
                            {{ $i + 1 }}
                        </td>

                        <td>
                            {{ $item->kendaraan->merk ?? '-' }}
                        </td>

                        <td class="text-center">
                            <span class="nopol">{{ $item->kendaraan->nopol ?? '-' }}</span>
                        </td>

                        <td>
                            {{ $item->gps->nama_gps ?? '-' }}
                        </td>

                        <td class="text-center">
                            <span class="badge-type">{{ $item->type }}</span>
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($item->biaya_sewa, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->tanggal_habis)->format('d-m-Y') }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('d-m-Y H:i') }}
                        </td>

                        <td class="text-center">
                            {{ $item->durasi_bulan }} Bulan
                        </td>

                        <td>
                                    @if ($item->bukti_bayar)
                                        @php
                                            $filename = basename($item->bukti_bayar);
                                        @endphp

                                        <a href="{{ asset($item->bukti_bayar) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            Tidak ada data history perpanjangan ditemukan.
                        </td>
                    </tr>
                @endforelse

                <tr class="row-subtotal">
                    <td colspan="9" class="text-right">Total Perpanjangan</td>
                    <td class="text-center">{{ $data->count() }}</td>
                </tr>

                <tr class="row-total">
                    <td colspan="5" class="text-right">TOTAL BIAYA PERPANJANGAN</td>
                    <td class="text-right">
                        Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                    </td>
                    <td colspan="4"></td>
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
            Laporan ini merupakan data history perpanjangan GPS kendaraan yang terdaftar pada sistem
            {{ $setting?->nama_perusahaan }}.
            Data mencakup informasi kendaraan, perangkat GPS yang digunakan, biaya sewa perpanjangan,
            tanggal habis sebelumnya, serta tanggal dilakukannya perpanjangan.
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