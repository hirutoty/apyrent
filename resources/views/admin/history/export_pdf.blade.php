<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan History Rental</title>

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

        {{-- SUMMARY CARDS --}}
        .summary-wrap {
            margin: 0 28px 15px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            width: 50%;
            padding: 12px 16px;
            border: 1px solid #d0d7e3;
            border-radius: 4px;
        }

        .summary-label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: bold;
            margin-top: 4px;
        }

        .summary-value.blue { color: #1e56b0; }
        .summary-value.green { color: #059669; }

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
            font-size: 10px;
        }

        .main-table td {
            border: 1px solid #d0d7e3;
            padding: 7px;
            font-size: 9.5px;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .status {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-pending { background:#f3f4f6; color:#4b5563; }
        .status-booking { background:#dbeafe; color:#2563eb; }
        .status-aktif   { background:#d1fae5; color:#059669; }
        .status-selesai { background:#111827; color:#fff; }
        .status-batal   { background:#fee2e2; color:#dc2626; }

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
                        <h1>HISTORY RENTAL</h1>
                        <p>{{ $kendaraan->merk }} - {{ $kendaraan->nopol }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFO --}}
    <div class="info-box">
        <p><strong>Tanggal Cetak :</strong> {{ now()->format('d M Y H:i') }}</p>
        <p><strong>Kendaraan :</strong> {{ $kendaraan->merk }} ({{ $kendaraan->nopol }})</p>
        <p><strong>Total Transaksi :</strong> {{ $rentals->count() }}</p>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="summary-wrap">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Total Rental</div>
                    <div class="summary-value blue">{{ $totalRental }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Pendapatan</div>
                    <div class="summary-value green">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- TABEL --}}
    <div class="table-wrap">
        <table class="main-table">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="5%">ID</th>
                    <th width="11%">Pelanggan</th>
                    <th width="10%">Kontak</th>
                    <th width="9%">Driver</th>
                    <th width="10%">Tgl Mulai</th>
                    <th width="10%">Tgl Selesai</th>
                    <th width="7%">Durasi</th>
                    <th width="10%">Tujuan</th>
                    <th width="10%">Total Biaya</th>
                    <th width="8%">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rentals as $i => $r)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">#{{ $r->id }}</td>
                        <td>{{ $r->Pelanggan->nama_Pelanggan ?? '-' }}</td>
                        <td>{{ $r->Pelanggan->kontak_Pelanggan ?? '-' }}</td>
                        <td>{{ $r->nama_driver ?? '-' }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($r->tanggal_mulai)->format('d-m-Y H:i') }}
                        </td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($r->tanggal_selesai)->format('d-m-Y H:i') }}
                        </td>
                        <td class="text-center">
                            @if ($r->durasi_jam) {{ $r->durasi_jam }} Jam
                            @elseif($r->durasi_hari) {{ $r->durasi_hari }} Hari
                            @elseif($r->durasi_bulan) {{ $r->durasi_bulan }} Bulan
                            @elseif($r->durasi_tahun) {{ $r->durasi_tahun }} Tahun
                            @else - @endif
                        </td>
                        <td>{{ $r->tujuan ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($r->total_biaya, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="status status-{{ $r->status }}">
                                {{ strtoupper($r->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">
                            Tidak ada data rental ditemukan.
                        </td>
                    </tr>
                @endforelse

                <tr class="row-subtotal">
                    <td colspan="10" class="text-right">Total Rental</td>
                    <td class="text-center">{{ $totalRental }}</td>
                </tr>

                <tr class="row-total">
                    <td colspan="10" class="text-right">TOTAL PENDAPATAN</td>
                    <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- KETERANGAN --}}
    <div class="note-box">
        <div class="note-title">KETERANGAN DOKUMEN</div>
        <p>
            Laporan ini merupakan riwayat transaksi rental untuk kendaraan
            {{ $kendaraan->merk }} ({{ $kendaraan->nopol }}) yang terdaftar pada sistem
            {{ $setting?->nama_perusahaan }}. Data mencakup informasi Pelanggan, driver,
            periode rental, biaya, serta status transaksi.
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
