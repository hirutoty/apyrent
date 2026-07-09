<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Data Rental</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: #ffffff;
            color: #222222;
        }

        /* ===== HEADER ===== */
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

        /* ===== TABEL UTAMA ===== */
        .main-table-wrap {
            padding: 16px 28px 0;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #b0b8c8;
        }

        .main-table thead tr {
            background: #1e56b0;
        }

        .main-table th {
            padding: 9px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            text-align: center;
            border: 1px solid #1a4a99;
        }

        .main-table tbody tr {
            background: #ffffff;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f7f9fc;
        }

        .main-table td {
            padding: 8px 10px;
            font-size: 11px;
            color: #222;
            text-align: center;
            border: 1px solid #d0d7e3;
            vertical-align: middle;
            line-height: 1.5;
        }

        .row-subtotal td {
            background: #f5f5f5 !important;
            font-weight: 700;
            color: #222;
            border-top: 2px solid #d0d7e3;
        }

        .row-total td {
            background: #1e56b0 !important;
            font-weight: 700;
            color: #ffffff !important;
            font-size: 11.5px;
        }

        .text-right {
            text-align: right;
            padding-right: 14px !important;
        }

        .total-cell {
            font-weight: 700;
            color: #222;
        }

        /* ===== KOTAK INFO ===== */
        .info-boxes {
            padding: 16px 28px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .box-note {
            border: 1px solid #d0d7e3;
            border-radius: 4px;
            padding: 12px 14px;
            background: #fefce8;
        }

        .box-note-title {
            font-weight: 700;
            font-size: 11px;
            color: #222;
            margin-bottom: 6px;
        }

        .box-note p {
            font-size: 10.5px;
            color: #333;
            line-height: 1.7;
        }

        .box-note strong {
            font-weight: 700;
            color: #1e56b0;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 24px;
            padding: 14px 28px 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .footer p {
            font-size: 10.5px;
            color: #1e56b0;
            line-height: 1.7;
        }

        /* ===== BADGE ===== */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-aktif    { background: #d1fae5; color: #065f46; }
        .badge-selesai  { background: #dbeafe; color: #1e40af; }
        .badge-pending  { background: #fef9c3; color: #92400e; }
        .badge-batal    { background: #fee2e2; color: #991b1b; }
        .badge-default  { background: #f3f4f6; color: #374151; }

        .badge-lunas    { background: #d1fae5; color: #065f46; }
        .badge-belum    { background: #fee2e2; color: #991b1b; }
        .badge-dp       { background: #fef9c3; color: #9c8609; }

        .badge-bulanan  { background: #e0e7ff; color: #3730a3; }
        .badge-harian   { background: #cffafe; color: #155e75; }
        .badge-tahunan  { background: #ffedd5; color: #9a3412; }
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
                    <div class="company-name">{{ $setting?->nama_perusahaan }}</div>
                    <div class="company-info">
                        {{ $setting?->alamat }}<br>
                        Telp : {{ $setting?->telepon }}<br>
                        Email : {{ $setting?->email }}
                        @if ($setting?->website)
                            <br>Website : {{ $setting->website }}
                        @endif
                    </div>
                </td>

                <td width="30%">
                    <div class="report-title">
                        <h1>Rental</h1>
                        <p>Laporan Rental Kendaraan Perusahaan</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- TABEL --}}
    <div class="main-table-wrap">
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pelanggan</th>
                    <th>Kendaraan</th>
                    <th>Jenis Rental</th>
                    <th>Durasi</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status Rental</th>
                    <th>Status Bayar</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rentals as $i => $r)
                    @php
                        // Tentukan jenis & durasi rental
                        if ($r->durasi_bulan) {
                            $jenisRental = 'Bulanan';
                            $durasi      = $r->durasi_bulan . ' Bulan';
                        } elseif ($r->durasi_hari) {
                            $jenisRental = 'Harian';
                            $durasi      = $r->durasi_hari . ' Hari';
                        } elseif ($r->durasi_tahun) {
                            $jenisRental = 'Tahunan';
                            $durasi      = $r->durasi_tahun . ' Tahun';
                        } else {
                            $jenisRental = '-';
                            $durasi      = '-';
                        }

                        // Class badge status rental (perbandingan harus lowercase vs lowercase)
                        $statusLower = strtolower($r->status ?? '');
                        $statusClass = match ($statusLower) {
                            'aktif'   => 'badge-aktif',
                            'selesai' => 'badge-selesai',
                            'pending' => 'badge-pending',
                            'batal'   => 'badge-batal',
                            default   => 'badge-default',
                        };

                        // Class badge jenis rental
                        $jenisClass = match ($jenisRental) {
                            'Bulanan' => 'badge-bulanan',
                            'Harian'  => 'badge-harian',
                            'Tahunan' => 'badge-tahunan',
                            default   => 'badge-default',
                        };

                        // Status pembayaran
                        if ($r->bukti_pelunasan || $r->bukti_lunas) {
                            $bayarClass = 'badge-lunas';
                            $bayarLabel = 'Lunas';
                        } elseif ($r->bukti_dp) {
                            $bayarClass = 'badge-dp';
                            $bayarLabel = 'DP';
                        } else {
                            $bayarClass = 'badge-belum';
                            $bayarLabel = 'Belum Lunas';
                        }
                    @endphp

                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $r->member->nama_pelanggan ?? '-' }}</td>
                        <td>
                            <strong>{{ $r->kendaraan->merk ?? '-' }}</strong><br>
                            {{ $r->kendaraan->nopol ?? '-' }}
                        </td>
                        <td><span class="badge {{ $jenisClass }}">{{ $jenisRental }}</span></td>
                        <td>{{ $durasi }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal_mulai)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal_selesai)->format('d-m-Y') }}</td>
                        <td><span class="badge {{ $statusClass }}">{{ ucfirst($r->status) }}</span></td>
                        <td><span class="badge {{ $bayarClass }}">{{ $bayarLabel }}</span></td>
                        <td class="total-cell">Rp {{ number_format($r->total_biaya, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <tr class="row-subtotal">
                    <td colspan="9" class="text-right">Sub Total</td>
                    <td>Rp {{ number_format($rentals->sum('total_biaya'), 0, ',', '.') }}</td>
                </tr>
                <tr class="row-total">
                    <td colspan="9" class="text-right" style="letter-spacing:0.5px;">TOTAL</td>
                    <td>Rp {{ number_format($rentals->sum('total_biaya'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- INFORMASI / KETENTUAN --}}
    <div class="info-boxes">
        <div class="box-note">
            <div class="box-note-title">KETENTUAN DOKUMEN</div>
            <p>
                Dokumen ini merupakan dokumen resmi <strong>{{ $setting->nama_perusahaan ?? '-' }}</strong>
                dan diterbitkan berdasarkan transaksi yang sah.
                <br>
                Pembayaran dinyatakan lunas setelah dana diterima dan terverifikasi pada rekening perusahaan.
            </p>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            <strong>{{ $setting->nama_perusahaan ?? '-' }}</strong><br>
            Telp: {{ $setting->telepon ?? '-' }}
            @if (!empty($setting?->email))
                <br>Email: {{ $setting->email }}
            @endif
            <br>{{ $setting->alamat ?? '-' }}
        </p>
    </div>

</body>

</html>
