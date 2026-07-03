<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Reminder Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        h2 {
            margin: 0;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 18px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #2563eb;
            color: #fff;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .overdue {
            color: #dc2626;
            font-weight: bold;
        }

        .today {
            color: #ca8a04;
            font-weight: bold;
        }

        .upcoming {
            color: #2563eb;
            font-weight: bold;
        }

        .other {
            color: #6b7280;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>

    <h2>LAPORAN REMINDER INVOICE</h2>

    <div class="subtitle">
        Data Reminder Pembayaran Invoice
    </div>

    <table style="margin-bottom:15px;">
        <tr>
            <td width="20%"><strong>Tanggal Cetak</strong></td>
            <td>{{ now()->format('d-m-Y H:i') }}</td>

            <td class="right">
                <strong>Total Reminder :</strong>
                {{ $overdue->count() + $dueToday->count() + $upcoming->count() + $others->count() }}
            </td>
        </tr>
    </table>

    <table>

        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kategori</th>
                <th width="12%">Invoice</th>
                <th width="18%">Customer</th>
                <th width="12%">Jatuh Tempo</th>
                <th width="14%">Total</th>
                <th width="14%">Dibayar</th>
                <th width="14%">Sisa</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @php
                $no = 1;
            @endphp

            {{-- OVERDUE --}}
            @foreach($overdue as $row)
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td class="center overdue">Overdue</td>
                <td>{{ $row->invoice->invoice_no ?? '-' }}</td>
                <td>{{ $row->invoice->customer_name ?? '-' }}</td>
                <td class="center">{{ optional($row->due_date)->format('d-m-Y') }}</td>
                <td class="right">Rp {{ number_format($row->total_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->paid_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->remaining_amount,0,',','.') }}</td>
                <td class="center">{{ $row->overdue_days }} Hari</td>
            </tr>
            @endforeach

            {{-- DUE TODAY --}}
            @foreach($dueToday as $row)
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td class="center today">Hari Ini</td>
                <td>{{ $row->invoice->invoice_no ?? '-' }}</td>
                <td>{{ $row->invoice->customer_name ?? '-' }}</td>
                <td class="center">{{ optional($row->due_date)->format('d-m-Y') }}</td>
                <td class="right">Rp {{ number_format($row->total_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->paid_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->remaining_amount,0,',','.') }}</td>
                <td class="center">Hari Ini</td>
            </tr>
            @endforeach

            {{-- UPCOMING --}}
            @foreach($upcoming as $row)
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td class="center upcoming">7 Hari</td>
                <td>{{ $row->invoice->invoice_no ?? '-' }}</td>
                <td>{{ $row->invoice->customer_name ?? '-' }}</td>
                <td class="center">{{ optional($row->due_date)->format('d-m-Y') }}</td>
                <td class="right">Rp {{ number_format($row->total_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->paid_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->remaining_amount,0,',','.') }}</td>
                <td class="center">{{ $row->days_left }} Hari Lagi</td>
            </tr>
            @endforeach

            {{-- OTHERS --}}
            @foreach($others as $row)
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td class="center other">Lainnya</td>
                <td>{{ $row->invoice->invoice_no ?? '-' }}</td>
                <td>{{ $row->invoice->customer_name ?? '-' }}</td>
                <td class="center">
                    {{ $row->due_date ? $row->due_date->format('d-m-Y') : '-' }}
                </td>
                <td class="right">Rp {{ number_format($row->total_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->paid_amount,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->remaining_amount,0,',','.') }}</td>
                <td class="center">
                    {{ $row->days_left ? $row->days_left.' Hari Lagi' : '-' }}
                </td>
            </tr>
            @endforeach

            @if($no==1)
            <tr>
                <td colspan="9" class="center">
                    Tidak ada reminder.
                </td>
            </tr>
            @endif

        </tbody>

    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>

</html>