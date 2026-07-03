<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kendaraan</title>

    <style>
    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }

    body{
        font-family: DejaVu Sans, sans-serif;
        font-size:8px;
        color:#222;
    }

    /* ======= HEADER (persis laporan merk) ======= */
    .header {
        padding: 18px 28px 14px;
        border-bottom: 2px solid #e0e0e0;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-table td {
        border: none;
        vertical-align: middle;
        padding: 0;
    }

    .logo {
        height: 60px;
        width: auto;
        object-fit: contain;
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
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }

    .report-title p {
        font-size: 11px;
        color: #666;
        margin-top: 4px;
    }

    /* ======= INFO BOX ======= */
    .info-box {
        margin: 15px 28px;
        padding: 12px;
        border: 1px solid #d0d7e3;
        background: #f8fafc;
        border-radius: 4px;
        font-size: 10px;
        line-height: 1.7;
    }

    .info-box p { margin: 3px 0; }

    /* ======= TABEL UTAMA ======= */
    .main-table-wrap {
        padding: 0 15px;
    }

    .main-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1px solid #b0b8c8;
    }

    /* Baris 1 header — grup */
    .main-table thead tr:first-child th {
        background: #1e56b0;
        color: #fff;
        border: 1px solid #1a4a99;
        font-size: 8px;
        font-weight: 700;
        text-align: center;
        padding: 6px;
    }

    /* Baris 2 header — sub kolom */
    .main-table thead tr:nth-child(2) th {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #c7d2fe;
        font-size: 7px;
        font-weight: 700;
        text-align: center;
        padding: 5px;
    }

    /* Data row utama */
    .main-table td {
        border: 1px solid #d0d7e3;
        padding: 4px;
        font-size: 7px;
        vertical-align: middle;
        word-break: break-word;
        white-space: normal;
    }

    /* Zebra hanya pada baris-utama (baris ganjil dari pasangan) */
    .main-table tbody .row-main:nth-child(4n+1),
    .main-table tbody .row-main:nth-child(4n+1) + .row-detail {
        background: #ffffff;
    }
    .main-table tbody .row-main:nth-child(4n+3),
    .main-table tbody .row-main:nth-child(4n+3) + .row-detail {
        background: #f7f9fc;
    }

    /* Baris detail (bawah) */
    .row-detail td {
        background: #eef4ff !important;
        border-top: 1px dashed #c7d2fe !important;
        font-size: 7px;
        vertical-align: middle;
        padding: 3px 4px;
    }

    /* Label grup di row-detail */
    .detail-label {
        background: #1e56b0 !important;
        color: #fff;
        font-weight: 700;
        font-size: 7px;
        text-align: center;
        white-space: nowrap;
    }

    .detail-subhead {
        background: #dbeafe !important;
        color: #1e40af;
        font-weight: 700;
        font-size: 7px;
        text-align: center;
        border-bottom: 1px solid #c7d2fe;
    }

    .text-center { text-align: center; }
    .text-right  { text-align: right; }

    /* ======= NOTE BOX ======= */
    .note-box {
        margin: 15px 28px 0;
        padding: 12px 14px;
        border: 1px solid #d0d7e3;
        border-radius: 4px;
        background: #fefce8;
    }

    .note-title {
        font-weight: 700;
        font-size: 11px;
        margin-bottom: 6px;
    }

    .note-box p {
        margin: 0;
        line-height: 1.7;
        font-size: 10px;
    }

    /* ======= FOOTER ======= */
    .footer {
        margin-top: 20px;
        padding: 15px 28px;
        text-align: center;
        border-top: 1px solid #e0e0e0;
    }

    .footer p {
        color: #1e56b0;
        font-size: 10px;
        line-height: 1.7;
    }
    </style>
</head>

<body>

    {{-- HEADER (persis laporan merk) --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="12%">
                    @if($setting?->logo)
                       <img src="{{ public_path($setting->logo) }}" class="logo">
                    @endif
                </td>
                <td width="58%">
                    <div class="company-name">{{ $setting?->nama_perusahaan }}</div>
                    <div class="company-info">
                        {{ $setting?->alamat }}<br>
                        Telp : {{ $setting?->telepon }}<br>
                        Email : {{ $setting?->email }}
                        @if($setting?->website)
                            <br>Website : {{ $setting->website }}
                        @endif
                    </div>
                </td>
                <td width="30%">
                    <div class="report-title">
                        <h1>DATA KENDARAAN</h1>
                        <p>Laporan Data Kendaraan Rental</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFO BOX --}}
    <div class="info-box">
        <p><strong>Tanggal Cetak :</strong> {{ now()->format('d M Y H:i') }}</p>
        <p><strong>Filter Merk :</strong> {{ $merk ?: 'Semua Merk' }}</p>
        <p><strong>Total Data :</strong> {{ $data->count() }} Kendaraan</p>
    </div>

    {{-- TABEL --}}
    <div class="main-table-wrap">
        <table class="main-table">

            <thead>
                {{-- Baris 1: Grup --}}
                <tr>
                    <th rowspan="2" style="width:3%">No</th>
                    <th colspan="6">Informasi Kendaraan</th>
                    <th colspan="2">Harga Sewa</th>
                    <th colspan="9">Spesifikasi</th>
                </tr>
                {{-- Baris 2: Sub kolom --}}
                <tr>
                    <th>User</th>
                    <th>Jenis</th>
                    <th>Merk</th>
                    <th>Nopol</th>
                    <th>Pemilik</th>
                    <th>Alamat</th>
                    <th>Per Jam</th>
                    <th>Per Hari</th>
                    <th>Tahun</th>
                    <th>Rakit</th>
                    <th>CC</th>
                    <th>Warna</th>
                    <th>No Rangka</th>
                    <th>No Mesin</th>
                    <th>No BPKB</th>
                    <th>BBM</th>
                    <th>TNKB</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($data as $i => $d)

                    {{-- BARIS UTAMA: No + Info + Harga + Spesifikasi --}}
                    <tr class="row-main">
                        <td class="text-center" rowspan="2">{{ $i + 1 }}</td>
                        <td>{{ $d->user->name ?? '-' }}</td>
                        <td>{{ $d->jenis->nama_jenis ?? '-' }}</td>
                        <td>{{ $d->merk ?? '-' }}</td>
                        <td>{{ $d->nopol ?? '-' }}</td>
                        <td>{{ $d->nama_pemilik ?? '-' }}</td>
                        <td>{{ $d->alamat ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($d->harga_sewa_per_jam ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($d->harga_sewa_per_hari ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $d->tahun_pembuatan ?? '-' }}</td>
                        <td class="text-center">{{ $d->tahun_perakitan ?? '-' }}</td>
                        <td class="text-center">{{ $d->isi_silinder ?? '-' }}</td>
                        <td>{{ $d->warna ?? '-' }}</td>
                        <td>{{ $d->no_rangka ?? '-' }}</td>
                        <td>{{ $d->no_mesin ?? '-' }}</td>
                        <td>{{ $d->no_bpkb ?? '-' }}</td>
                        <td class="text-center">{{ $d->bahan_bakar ?? '-' }}</td>
                        <td class="text-center">{{ $d->warna_tnkb ?? '-' }}</td>
                    </tr>

                    {{-- BARIS DETAIL: Administrasi + Monitoring + Status --}}
                    <tr class="row-detail">
                        {{-- Label + data Administrasi --}}
                        <td class="detail-label" colspan="1">Administrasi</td>
                        <td class="text-center"><strong>Kode:</strong> {{ $d->kode_lokasi ?? '-' }}</td>
                        <td class="text-center" colspan="2"><strong>No Urut:</strong> {{ $d->no_urut_pendaftaran ?? '-' }}</td>

                        {{-- Label + data Monitoring Service --}}
                        <td class="detail-label" colspan="1">Monitoring Service</td>
                        <td class="text-right"><strong>Biaya:</strong> Rp {{ number_format($d->batas_biaya ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center"><strong>Berlaku:</strong> {{ $d->masa_berlaku ?? '-' }}</td>
                        <td class="text-right"><strong>KM:</strong> {{ number_format($d->kilometer_sekarang ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Limit KM:</strong> {{ number_format($d->limit_km_service ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center"><strong>Limit Bln:</strong> {{ $d->limit_bulan_service ?? '-' }}</td>
                        <td class="text-right"><strong>KM Srv:</strong> {{ number_format($d->km_terakhir_service ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center"><strong>Tgl Srv:</strong> {{ $d->tanggal_terakhir_service ? \Carbon\Carbon::parse($d->tanggal_terakhir_service)->format('d-m-Y') : '-' }}</td>

                        {{-- Label + data Status --}}
                        <td class="detail-label" colspan="1">Status</td>
                        <td class="text-center"><strong>Service:</strong> {{ ucfirst($d->status_service ?? '-') }}</td>
                        <td class="text-center"><strong>Kendaraan:</strong> {{ ucfirst($d->status_kendaraan ?? '-') }}</td>
                        <td colspan="2"></td>
                    </tr>

                @endforeach
            </tbody>

        </table>
    </div>

    {{-- KETERANGAN --}}
    <div class="note-box">
        <div class="note-title">KETERANGAN DOKUMEN</div>
        <p>
            Laporan ini merupakan data kendaraan yang terdaftar pada sistem
            {{ $setting?->nama_perusahaan }}.
            Setiap kendaraan ditampilkan dalam dua baris: baris atas berisi informasi utama, harga sewa, dan spesifikasi;
            baris bawah berisi data administrasi, monitoring service, dan status kendaraan.
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            <strong>{{ $setting?->nama_perusahaan }}</strong><br>
            {{ $setting?->alamat }}<br>
            Telp : {{ $setting?->telepon }}
            @if($setting?->website)
                | {{ $setting->website }}
            @endif
            <br>Email : {{ $setting?->email }}
        </p>
    </div>

</body>
</html>