<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Data Pembayaran</title>

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

        /* ── STATUS BADGE ── */
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }

        .badge-verified { background: #dcfce7; color: #166534; }
        .badge-pending  { background: #fef9c3; color: #854d0e; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

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
                        <h1>Pembayaran</h1>
                        <p>Laporan Data Pembayaran Invoice</p>
                    </div>
                </td>

            </tr>
        </table>
    </div>

    {{-- ── INFO BOX ── --}}
    @php
        $grandTotal = $payments->sum('amount');
    @endphp

    <div class="info-box">
        <p>
            <strong>Tanggal Cetak :</strong>
            {{ now()->format('d M Y H:i') }}
        </p>
        <p>
            <strong>Total Data Pembayaran :</strong>
            {{ $payments->count() }}
        </p>
        <p>
            <strong>Total Nominal Pembayaran :</strong>
            Rp {{ number_format($grandTotal, 0, ',', '.') }}
        </p>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-wrap">

        @php
            $grouped    = $payments->groupBy(fn($p) => \Carbon\Carbon::parse($p->payment_date)->format('Y-m'));
            $grandCount = 0;
            $grandSum   = 0;
            $monthIndex = 0;
        @endphp

        <table class="main-table">

            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="13%">Transaction ID</th>
                    <th width="11%">Invoice</th>
                    <th width="16%">Customer</th>
                    <th width="9%">Tanggal</th>
                    <th width="10%">Metode</th>
                    <th width="12%">Jumlah</th>
                    <th width="9%">Status</th>
                    <th width="16%">Bukti</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($grouped as $yearMonth => $items)

                    @php
                        $label      = \Carbon\Carbon::parse($yearMonth . '-01')->translatedFormat('F Y');
                        $subTotal   = $items->sum('amount');
                        $colorClass = 'month-' . ($monthIndex % 4);
                        $bgClass    = 'bg-month-' . ($monthIndex % 4);
                        $rowNum     = 0;
                        $grandCount += $items->count();
                        $grandSum   += $subTotal;
                    @endphp

                    {{-- Month group header --}}
                    <tr class="row-month-header {{ $colorClass }}">
                        <td colspan="9">
                            &#128197; {{ strtoupper($label) }}
                        </td>
                    </tr>

                    @foreach ($items as $pay)
                        @php $rowNum++; @endphp
                        <tr class="{{ $bgClass }}">

                            <td class="text-center">
                                {{ $rowNum }}
                            </td>

                            <td>
                                {{ $pay->transaction_id ?? '-' }}
                            </td>

                            <td>
                                {{ optional($pay->invoice)->invoice_no ?? '-' }}
                            </td>

                            <td>
                                {{ optional($pay->invoice)->customer_name ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d-m-Y') : '-' }}
                            </td>

                            <td class="text-center">
                                {{ $pay->method ?? '-' }}
                            </td>

                            <td class="text-right">
                                Rp {{ number_format($pay->amount, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                @if ($pay->status == 'Verified')
                                    <span class="badge badge-verified">Verified</span>
                                @elseif ($pay->status == 'Pending')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif ($pay->status == 'Rejected')
                                    <span class="badge badge-rejected">Rejected</span>
                                @else
                                    {{ $pay->status ?? '-' }}
                                @endif
                            </td>

                            <td>
                                {{ $pay->file_pembayaran ? basename($pay->file_pembayaran) : '-' }}
                            </td>

                        </tr>
                    @endforeach

                    {{-- Subtotal per bulan --}}
                    <tr class="row-subtotal">
                        <td colspan="5" class="text-right">
                            Subtotal {{ $label }} &mdash; {{ $items->count() }} transaksi
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
                            Tidak ada data pembayaran.
                        </td>
                    </tr>

                @endforelse

                {{-- Grand total --}}
                <tr class="row-total">
                    <td colspan="5" class="text-right">
                        GRAND TOTAL &mdash; {{ $grandCount }} transaksi
                    </td>
                    <td></td>
                    <td class="text-right">
                        Rp {{ number_format($grandSum, 0, ',', '.') }}
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