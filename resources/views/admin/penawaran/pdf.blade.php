<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penawaran</title>

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

        /* Soft alternating month colors */
        .month-0 td { background: #eef4ff; color: #1e3a6e; }  /* biru muda */
        .month-1 td { background: #f0fdf4; color: #14532d; }  /* hijau muda */
        .month-2 td { background: #fef9ec; color: #78350f; }  /* kuning muda */
        .month-3 td { background: #fdf2f8; color: #701a75; }  /* ungu muda  */

        /* Data rows inherit a lighter tint of the month color */
        .bg-month-0 { background: #f7faff; }
        .bg-month-1 { background: #f7fdf9; }
        .bg-month-2 { background: #fffef5; }
        .bg-month-3 { background: #fef9fc; }

        /* ── SUBTOTAL per bulan ── */
        .row-subtotal td {
            background: #eff6ff;
            font-weight: bold;
        }

        /* ── GRAND TOTAL ── */
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
                        <h1>Penawaran</h1>
                        <p>Laporan Penawaran Kendaraan</p>
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
            <strong>Total Data Penawaran :</strong>
            {{ $penawarans->count() }}
        </p>
        <p>
            <strong>Grand Total Nominal :</strong>
            Rp {{ number_format($penawarans->sum('total'), 0, ',', '.') }}
        </p>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-wrap">

        @php
            // Kelompokkan berdasarkan bulan (format: "Y-m" untuk sorting, "F Y" untuk label)
            $grouped = $penawarans->groupBy(fn($p) => \Carbon\Carbon::parse($p->tanggal_penawaran)->format('Y-m'));
            $grandTotal = 0;
            $grandCount = 0;
            $monthIndex = 0;
        @endphp

        <table class="main-table">

            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">No Penawaran</th>
                    <th width="9%">Tanggal</th>
                    <th width="15%">Customer</th>
                    <th width="15%">Jenis Customer</th>
                    <th width="28%">Kendaraan</th>
                    <th width="7%">Periode</th>
                    <th width="10%">Total</th>
                    <th width="18%">Status</th>
                    
                </tr>
            </thead>

            <tbody>

                @forelse ($grouped as $yearMonth => $items)

                    @php
                        $label      = \Carbon\Carbon::parse($yearMonth . '-01')->translatedFormat('F Y');
                        $subTotal   = $items->sum('total');
                        $colorClass = 'month-' . ($monthIndex % 4);
                        $bgClass    = 'bg-month-' . ($monthIndex % 4);
                        $rowNum     = 0;
                        $grandTotal += $subTotal;
                        $grandCount += $items->count();
                    @endphp

                    {{-- Month group header --}}
                    <tr class="row-month-header {{ $colorClass }}">
                        <td colspan="8">
                            &#128197; {{ strtoupper($label) }}
                        </td>
                    </tr>

                    @foreach ($items as $p)
                        @php $rowNum++; @endphp
                        <tr class="{{ $bgClass }}">

                            <td class="text-center">
                                {{ $rowNum }}
                            </td>

                            <td>
                                {{ $p->no_penawaran ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ optional($p->tanggal_penawaran)->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $p->customer_name ?? '-' }}
                            </td>
                            <td>
                                {{ $p->jenis_member ?? '-' }}
                            </td>

                            <td>
                                @foreach ($p->items as $item)
                                    &bull; {{ optional($item->kendaraan)->merk ?? '-' }}
                                    &ndash;
                                    {{ optional($item->kendaraan)->nopol ?? '-' }}<br>
                                @endforeach
                            </td>

                            <td class="text-center">
                                {{ $p->periode }} Bulan
                            </td>

                            <td class="text-right">
                                Rp {{ number_format($p->total, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                {{ strtoupper($p->status ?? '-') }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Subtotal per bulan --}}
                    <tr class="row-subtotal">
                        <td colspan="5" class="text-right">
                            Subtotal {{ $label }} &mdash; {{ $items->count() }} penawaran
                        </td>
                        <td></td>
                        <td class="text-right">
                            Rp {{ number_format($subTotal, 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>

                    @php $monthIndex++; @endphp

                @empty

                    <tr>
                        <td colspan="9" class="text-center">
                            Tidak ada data penawaran.
                        </td>
                    </tr>

                @endforelse

                {{-- Grand total --}}
                <tr class="row-total">
                    <td colspan="5" class="text-right">
                        GRAND TOTAL &mdash; {{ $grandCount }} penawaran
                    </td>
                    <td></td>
                    <td class="text-right">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                    <td colspan="2"></td>
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