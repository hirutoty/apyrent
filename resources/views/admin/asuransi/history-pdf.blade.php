<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan History Asuransi</title>

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
                        <h1>ASURANSI KENDARAAN</h1>
                        <p>Laporan History Perpanjangan Asuransi</p>
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

        @if (!empty($namaBulan) && !empty($namaTahun))
            <p>
                <strong>Periode :</strong>
                {{ $namaBulan }} {{ $namaTahun }}
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
                    <th width="9%">No Polisi</th>
                    <th width="11%">Asuransi</th>
                    <th width="9%">Jenis</th>
                    <th width="9%">Biaya</th>
                    <th width="10%">Tgl Mulai</th>
                    <th width="10%">Tgl Berakhir</th>
                    <th width="13%">Diperpanjang Pada</th>
                    <th width="7%">Bukti</th>
                    <th width="6%">Lampiran</th>
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
                            {{ $item->kendaraan->nopol ?? '-' }}
                        </td>

                        <td>
                            {{ $item->asuransi->nama_asuransi ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ $item->jenisAsuransi->nama_jenis ?? '-' }}
                        </td>

                        <td class="text-right">
                            Rp {{ number_format($item->biaya, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d-m-Y') }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->tgl_berakhir)->format('d-m-Y') }}
                        </td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('d-m-Y H:i') }}
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

                        <td>
                            @if($item->attachments->isNotEmpty())
                                {{ $item->attachments->pluck('file_name')->join(', ') }}
                            @else
                                -
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">
                            Tidak ada data ditemukan.
                        </td>
                    </tr>
                @endforelse

                <tr class="row-subtotal">
                    <td colspan="10" class="text-right">
                        Total Perpanjangan
                    </td>
                    <td class="text-center">
                        {{ $data->count() }}
                    </td>
                </tr>

                <tr class="row-total">
                    <td colspan="10" class="text-right">
                        TOTAL BIAYA PERPANJANGAN ASURANSI
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($data->sum('biaya'), 0, ',', '.') }}
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
            Laporan ini merupakan data riwayat perpanjangan asuransi kendaraan yang terdaftar pada sistem
            {{ $setting?->nama_perusahaan }}.
            Data mencakup informasi kendaraan, nama asuransi, jenis asuransi, biaya perpanjangan,
            periode masa berlaku, serta tanggal perpanjangan dilakukan.
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