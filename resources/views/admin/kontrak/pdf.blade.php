<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kontrak Kendaraan</title>

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

        /* ── HEADER ── */
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

        /* ── INFO BOX ── */
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

        /* ── TABLE ── */
        .table-wrap {
            padding: 0 28px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
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
            vertical-align: top;
        }

        /* ── MONTH GROUP HEADER ── */
        .row-month-header td {
            font-weight: bold;
            font-size: 10.5px;
            padding: 6px 9px;
            border: 1px solid #d0d7e3;
            letter-spacing: 0.5px;
        }

        .month-0 td { background: #eef4ff; color: #1e3a6e; }
        .month-1 td { background: #f0fdf4; color: #14532d; }
        .month-2 td { background: #fef9ec; color: #78350f; }
        .month-3 td { background: #fdf2f8; color: #701a75; }

        .bg-month-0 { background: #f7faff; }
        .bg-month-1 { background: #f7fdf9; }
        .bg-month-2 { background: #fffef5; }
        .bg-month-3 { background: #fef9fc; }

        /* ── SUBTOTAL & TOTAL ── */
        .row-subtotal td {
            background: #eff6ff;
            font-weight: bold;
        }

        .row-total td {
            background: #1e56b0;
            color: #fff;
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right;  }

        /* ── FOOTER ── */
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

    {{-- ── HEADER ── --}}
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
                        <h1>Kontrak</h1>
                        <p>Laporan Kontrak Kendaraan</p>
                    </div>
                </td>

            </tr>
        </table>
    </div>

    {{-- ── INFO BOX ── --}}
    <div class="info-box">
        <p>
            <strong>Tanggal Cetak :</strong>
            {{ now()->format('d M Y H:i') }}
        </p>
        <p>
            <strong>Total Data Kontrak :</strong>
            {{ $kontraks->count() }}
        </p>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-wrap">

        @php
            $grouped    = $kontraks->groupBy(fn($k) => \Carbon\Carbon::parse($k->tanggal_kontrak)->format('Y-m'));
            $grandCount = 0;
            $monthIndex = 0;
        @endphp

        <table class="main-table">

            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">No Kontrak</th>
                    <th width="11%">No Penawaran</th>
                    <th width="8%">Tgl Kontrak</th>
                    <th width="8%">Perjanjian</th>
                    <th width="14%">Pihak Pertama</th>
                    <th width="14%">Pihak Kedua</th>
                    <th width="9%">Status</th>
                    <th width="21%">File</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($grouped as $yearMonth => $items)

                    @php
                        $label      = \Carbon\Carbon::parse($yearMonth . '-01')->translatedFormat('F Y');
                        $colorClass = 'month-' . ($monthIndex % 4);
                        $bgClass    = 'bg-month-' . ($monthIndex % 4);
                        $rowNum     = 0;
                        $grandCount += $items->count();
                    @endphp

                    {{-- Month group header --}}
                    <tr class="row-month-header {{ $colorClass }}">
                        <td colspan="9">
                            &#128197; {{ strtoupper($label) }}
                        </td>
                    </tr>

                    @foreach ($items as $k)
                        @php $rowNum++; @endphp
                        <tr class="{{ $bgClass }}">

                            <td class="text-center">
                                {{ $rowNum }}
                            </td>

                            <td>
                                {{ $k->no_kontrak ?? '-' }}
                            </td>

                            <td>
                                {{ $k->penawaran->no_penawaran ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ optional($k->tanggal_kontrak)->format('d-m-Y') ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ optional($k->perjanjian_pembayaran)->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $k->pihak_pertama ?? '-' }}
                            </td>

                            <td>
                                {{ $k->pihak_kedua ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ strtoupper($k->status ?? '-') }}
                            </td>

                            <td>
                                <strong>Kontrak:</strong><br>
                                {{ $k->file_kontrak ? basename($k->file_kontrak) : '-' }}
                                <br><br>
                                <strong>Persyaratan:</strong><br>
                                {{ $k->file_persyaratan ? basename($k->file_persyaratan) : '-' }}
                            </td>

                        </tr>
                    @endforeach

                    {{-- Subtotal per bulan --}}
                    <tr class="row-subtotal">
                        <td colspan="8" class="text-right">
                            Subtotal {{ $label }} &mdash; {{ $items->count() }} kontrak
                        </td>
                        <td></td>
                    </tr>

                    @php $monthIndex++; @endphp

                @empty

                    <tr>
                        <td colspan="9" class="text-center">
                            Tidak ada data kontrak.
                        </td>
                    </tr>

                @endforelse

                {{-- Grand total --}}
                <tr class="row-total">
                    <td colspan="8" class="text-right">
                        GRAND TOTAL &mdash; {{ $grandCount }} kontrak
                    </td>
                    <td></td>
                </tr>

            </tbody>

        </table>

    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <strong>{{ $setting?->nama_perusahaan }}</strong><br>
        {{ $setting?->alamat }}<br>
        Telp : {{ $setting?->telepon }}
        |
        Email : {{ $setting?->email }}
        @if ($setting?->website)
            <br>{{ $setting->website }}
        @endif
    </div>

</body>

</html>