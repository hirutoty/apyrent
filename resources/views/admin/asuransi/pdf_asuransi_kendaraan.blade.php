<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Asuransi Kendaraan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header-box {
            border-bottom: 3px solid #1e56b0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .logo {
            height: 65px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e56b0;
        }

        .company-info {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
        }

        .report-title {
            font-size: 26px;
            font-weight: bold;
            color: #1e56b0;
            letter-spacing: 3px;
        }

        .report-subtitle {
            font-size: 11px;
            color: #666;
        }

        .info-box {
            border: 1px solid #dbeafe;
            background: #f8fafc;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-box p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead tr {
            background: #1e56b0;
        }

        table th {
            color: white;
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        table td {
            border: 1px solid #dbeafe;
            padding: 7px;
        }

        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .aktif {
            color: #15803d;
            font-weight: bold;
        }

        .expired {
            color: #dc2626;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #dbeafe;
            padding-top: 10px;
            color: #1e56b0;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <div class="header-box">

        <table width="100%">
            <tr>

                <td width="15%">
                    @if($logoSrc)
                       <img src="{{ $logoSrc }}" class="logo">
                    @endif
                </td>

                <td width="55%">
                    <div class="company-name">
                        {{ $setting->nama_perusahaan }}
                    </div>

                    <div class="company-info">
                        {{ $setting->alamat }}
                        <br>
                        Telp : {{ $setting->telepon }}
                        <br>
                        Email : {{ $setting->email }}

                        @if ($setting?->website)
                            <br>
                            Website : {{ $setting->website }}
                        @endif
                    </div>
                </td>

                <td width="30%" align="right">
                    <div class="report-title">
                        LAPORAN
                    </div>

                    <div class="report-subtitle">
                        ASURANSI KENDARAAN
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
            <strong>Total Data :</strong>
            {{ $data->count() }}
        </p>

    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="13%">Nopol</th>
                <th width="13%">Merk</th>
                <th width="15%">Asuransi</th>
                <th width="12%">Jenis</th>
                <th width="8%">Status</th>
                <th width="9%">Tgl Mulai</th>
                <th width="6%">Durasi</th>
                <th width="9%">Tgl Berakhir</th>
                <th width="10%">Biaya</th>
                <th width="10%">Bukti Bayar</th>
                <th width="12%">Lampiran</th>
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
                        {{ $item->asuransi->nama_asuransi ?? '-' }}
                    </td>

                    <td>
                        {{ $item->jenisAsuransi->nama_jenis ?? '-' }}
                    </td>

                    <td class="text-center">
                        @if ($item->status_kendaraan == 'aktif')
                            <span class="aktif">AKTIF</span>
                        @else
                            <span class="expired">EXPIRED</span>
                        @endif
                    </td>

                    <td>
                        {{ $item->tgl_mulai ? \Carbon\Carbon::parse($item->tgl_mulai)->format('d-m-Y') : '-' }}
                    </td>

                    <td>
                        {{ $item->durasi_bulan ?? '-' }}
                    </td>

                    <td>
                        {{ $item->tgl_berakhir ? \Carbon\Carbon::parse($item->tgl_berakhir)->format('d-m-Y') : '-' }}
                    </td>
                    <td>
                        {{ $item->biaya ? 'Rp ' . number_format($item->biaya, 0, ',', '.') : '-' }}
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
                        @if ($item->attachments && $item->attachments->isNotEmpty())
                            {{ $item->attachments->pluck('file_name')->join(', ') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">
                        Tidak ada data ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">

        <strong>{{ $setting->nama_perusahaan }}</strong>
        <br>

        {{ $setting->alamat }}
        <br>

        Telp : {{ $setting->telepon }}
        |
        Email : {{ $setting->email }}

    </div>

</body>

</html>
