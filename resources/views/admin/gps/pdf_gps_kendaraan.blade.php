<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan GPS Kendaraan</title>

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
                        <h1>GPS KENDARAAN</h1>
                        <p>Laporan Data GPS Kendaraan</p>
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
                    <th width="12%">Kendaraan</th>
                    <th width="10%">No Polisi</th>
                    <th width="12%">GPS</th>
                    <th width="8%">Type</th>
                    <th width="10%">Status GPS</th>
                    <th width="10%">Tgl Pasang</th>
                    <th width="10%">Tgl Habis</th>
                    <th width="10%">Biaya</th>
                    <th width="7%">Durasi</th>
                    <th width="7%">Status</th>
                    <th width="7%">Bukti</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($data as $i => $d)
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
                            {{ $d->gps->nama_gps ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ $d->type }}
                        </td>

                        <td class="text-center">
                            {{ ucfirst($d->status_gps) }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($d->tanggal_pasang)->format('d-m-Y') }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($d->tanggal_habis)->format('d-m-Y') }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($d->biaya_sewa, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            {{ $d->durasi_bulan }} Bulan
                        </td>

                        <td class="text-center">
                            {{ ucfirst($d->status_sewa) }}
                        </td>

                        <td>
                                    @if ($d->bukti_bayar)
                                        @php
                                            $filename = basename($d->bukti_bayar);
                                        @endphp

                                        <a href="{{ asset($d->bukti_bayar) }}" target="_blank"
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
                        <td colspan="8" class="text-center">
                            Tidak ada data ditemukan.
                        </td>
                    </tr>
                @endforelse


                <tr class="row-subtotal">
                    <td colspan="11" class="text-right">
                        Total GPS
                    </td>
                    <td class="text-center">
                        {{ $data->count() }}
                    </td>
                </tr>

                <tr class="row-subtotal">
                    <td colspan="11" class="text-right">
                        GPS Aktif
                    </td>
                    <td class="text-center">
                        {{ $data->where('status_gps', 'aktif')->count() }}
                    </td>
                </tr>

                <tr class="row-subtotal">
                    <td colspan="11" class="text-right">
                        GPS Nonaktif
                    </td>
                    <td class="text-center">
                        {{ $data->where('status_gps', 'nonaktif')->count() }}
                    </td>
                </tr>

                <tr class="row-total">
                    <td colspan="11" class="text-right">
                        TOTAL BIAYA GPS
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($data->sum('biaya_sewa'), 0, ',', '.') }}
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
            Laporan ini merupakan data GPS kendaraan yang terdaftar pada sistem
            {{ $setting?->nama_perusahaan }}.
            Data mencakup status GPS, masa aktif perangkat, biaya sewa GPS,
            serta status penggunaan GPS pada kendaraan.
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
